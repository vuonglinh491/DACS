<?php
// ============================================================
//  LKSecure — Xử lý đơn hàng (API JSON)
//  Điều kiện hủy: chỉ khi status = pending | confirmed
//  KHÔNG cho hủy khi: shipping | delivered | cancelled
// ============================================================

// ✅ ob_start() ngăn PHP warning/notice phá vỡ JSON output
ob_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth_check.php';
ob_clean(); // xoá output thừa trước JSON
header('Content-Type: application/json; charset=utf-8');

requireLogin(true); // JSON mode

$action  = $_POST['action'] ?? $_GET['action'] ?? '';
$user_id = (int)$_SESSION['user_id'];

// ── DANH SÁCH ĐƠN HÀNG ────────────────────────────────────
if ($action === 'list') {
    // Dùng đúng tên cột theo schema: ordered_at, ship_address
    $stmt = $conn->prepare(
        "SELECT id, order_code, total, status, ship_address AS address,
                note, ordered_at AS created_at
         FROM orders WHERE user_id = ?
         ORDER BY ordered_at DESC"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $row['created_at'] = date('d/m/Y H:i', strtotime($row['created_at']));
        // Đánh dấu có thể hủy không
        $row['can_cancel'] = in_array($row['status'], ['pending', 'confirmed']);
        $orders[] = $row;
    }
    $stmt->close();

    // Lấy items từ order_items (dùng đúng tên cột: product_name, product_image, unit_price)
    $tableCheck = $conn->query("SHOW TABLES LIKE 'order_items'");
    if ($tableCheck && $tableCheck->num_rows > 0 && !empty($orders)) {
        $ids = implode(',', array_map('intval', array_column($orders, 'id')));
        $itemsResult = $conn->query(
            "SELECT order_id, product_name, product_image AS product_img,
                    unit_price AS price, quantity
             FROM order_items WHERE order_id IN ($ids)"
        );
        $itemsMap = [];
        if ($itemsResult) {
            while ($item = $itemsResult->fetch_assoc()) {
                $itemsMap[$item['order_id']][] = $item;
            }
        }
        foreach ($orders as &$order) {
            $order['items'] = $itemsMap[$order['id']] ?? [];
        }
        unset($order);
    }

    echo json_encode(['success' => true, 'orders' => $orders]);
    exit;
}

// ── HUỶ ĐƠN HÀNG ─────────────────────────────────────────
if ($action === 'cancel' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    if (!$order_id) {
        echo json_encode(['success' => false, 'message' => 'Đơn hàng không hợp lệ.']);
        exit;
    }

    // Lấy trạng thái đơn hàng hiện tại
    $check = $conn->prepare("SELECT status FROM orders WHERE id = ? AND user_id = ?");
    $check->bind_param("ii", $order_id, $user_id);
    $check->execute();
    $row = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng.']);
        exit;
    }

    $cancellable = ['pending', 'confirmed'];
    if (!in_array($row['status'], $cancellable)) {
        $blockMsg = match($row['status']) {
            'shipping'  => 'Đơn hàng đang được giao, không thể hủy.',
            'delivered' => 'Đơn hàng đã hoàn thành, không thể hủy.',
            'cancelled' => 'Đơn hàng đã được hủy trước đó.',
            default     => 'Không thể hủy đơn hàng ở trạng thái này.',
        };
        echo json_encode(['success' => false, 'message' => $blockMsg]);
        exit;
    }

    $reason = trim($_POST['reason'] ?? 'Khách hàng yêu cầu hủy');

    $stmt = $conn->prepare(
        "UPDATE orders SET status = 'cancelled', cancelled_reason = ?
         WHERE id = ? AND user_id = ?"
    );
    $stmt->bind_param("sii", $reason, $order_id, $user_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected > 0) {
        // Hoàn lại stock
        $items = $conn->query("SELECT product_id, quantity FROM order_items WHERE order_id = $order_id");
        if ($items) {
            while ($it = $items->fetch_assoc()) {
                $conn->query("UPDATE products SET stock = stock + {$it['quantity']} WHERE id = {$it['product_id']}");
            }
        }
        echo json_encode(['success' => true, 'message' => 'Đã hủy đơn hàng thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể hủy đơn hàng này.']);
    }
    exit;
}

// ── ĐẶT HÀNG MỚI (checkout) ───────────────────────────────
if ($action === 'place' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ship_name    = trim($_POST['ship_name']    ?? '');
    $ship_phone   = trim($_POST['ship_phone']   ?? '');
    $ship_address = trim($_POST['ship_address'] ?? '');
    $note         = trim($_POST['note']         ?? '');
    $pay_method   = $_POST['payment_method']    ?? 'cod';

    if (!$ship_name || !$ship_phone || !$ship_address) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin giao hàng.']);
        exit;
    }
    if (!preg_match('/^(0[35789])([0-9]{8})$/', $ship_phone)) {
        echo json_encode(['success' => false, 'message' => 'Số điện thoại không hợp lệ.']);
        exit;
    }
    $allowed_pay = ['cod', 'bank_transfer', 'momo', 'vnpay'];
    if (!in_array($pay_method, $allowed_pay)) $pay_method = 'cod';

    // Lấy giỏ hàng
    $cartRes = $conn->prepare(
        "SELECT c.product_id, c.quantity, p.name, p.price, p.image, p.stock, p.status
         FROM cart c
         JOIN products p ON c.product_id = p.id
         WHERE c.user_id = ?"
    );
    $cartRes->bind_param("i", $user_id);
    $cartRes->execute();
    $cartItems = $cartRes->get_result()->fetch_all(MYSQLI_ASSOC);
    $cartRes->close();

    if (empty($cartItems)) {
        echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống.']);
        exit;
    }

    // Kiểm tra tồn kho
    foreach ($cartItems as $item) {
        if ($item['status'] !== 'active' || $item['stock'] < $item['quantity']) {
            echo json_encode(['success' => false, 'message' => "Sản phẩm \"{$item['name']}\" không đủ hàng."]);
            exit;
        }
    }

    $subtotal = 0;
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $shipping_fee = $subtotal >= 5000000 ? 0 : 30000;
    $total = $subtotal + $shipping_fee;

    // Tạo mã đơn hàng
    $order_code = 'LKS-' . date('Y') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

    // Bắt đầu transaction
    $conn->begin_transaction();
    try {
        // ✅ Tạo đơn hàng — bind_param đúng kiểu: i=int, s=string, d=decimal
        $orderStmt = $conn->prepare(
            "INSERT INTO orders
             (user_id, order_code, ship_name, ship_phone, ship_address, note,
              subtotal, discount, shipping_fee, total, payment_method, payment_status, status, ordered_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, 'unpaid', 'pending', NOW())"
        );
        $orderStmt->bind_param("isssssddds",
            $user_id,      // i
            $order_code,   // s
            $ship_name,    // s
            $ship_phone,   // s
            $ship_address, // s
            $note,         // s
            $subtotal,     // d
            $shipping_fee, // d
            $total,        // d
            $pay_method    // s
        );
        if (!$orderStmt->execute()) {
            throw new Exception('Lỗi INSERT orders: ' . $orderStmt->error);
        }
        $order_id = $conn->insert_id;
        $orderStmt->close();

        // ✅ Lưu order_items — bind_param: i,i,i,s,s,d,i,d
        $itemStmt = $conn->prepare(
            "INSERT INTO order_items
             (order_id, user_id, product_id, product_name, product_image, unit_price, quantity, line_total)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stockStmt = $conn->prepare(
            "UPDATE products SET stock = GREATEST(0, stock - ?), sold_count = sold_count + ? WHERE id = ?"
        );

        foreach ($cartItems as $item) {
            $pid   = (int)$item['product_id'];
            $qty   = (int)$item['quantity'];
            $price = (float)$item['price'];
            $img   = $item['image'] ?? '';
            $name  = $item['name'];
            $line  = $price * $qty;

            $itemStmt->bind_param("iiissdid",
                $order_id, $user_id, $pid, $name, $img, $price, $qty, $line
            );
            if (!$itemStmt->execute()) {
                throw new Exception('Lỗi INSERT order_items: ' . $itemStmt->error);
            }

            $stockStmt->bind_param("iii", $qty, $qty, $pid);
            $stockStmt->execute();
        }
        $itemStmt->close();
        $stockStmt->close();

        // Xóa giỏ hàng
        $clearStmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clearStmt->bind_param("i", $user_id);
        $clearStmt->execute();
        $clearStmt->close();

        $conn->commit();
        echo json_encode(['success' => true, 'order_code' => $order_code, 'order_id' => $order_id, 'message' => 'Đặt hàng thành công!']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action không hợp lệ.']);