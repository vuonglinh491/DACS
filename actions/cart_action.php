<?php
// ============================================================
//  LKSecure — Xử lý giỏ hàng (yêu cầu đăng nhập)
// ============================================================
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth_check.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'require_login' => true, 'message' => 'Vui lòng đăng nhập để sử dụng giỏ hàng.']);
    exit;
}

$action  = $_POST['action'] ?? $_GET['action'] ?? '';
$user_id = (int)$_SESSION['user_id'];

// Helper: đếm tổng quantity trong giỏ
function cartCount($conn, $user_id) {
    $res = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
    return (int)($res->fetch_assoc()['total'] ?? 0);
}

// ── THÊM VÀO GIỎ HÀNG ────────────────────────────────────
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity   = max(1, (int)($_POST['quantity'] ?? 1));

    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ.']); exit;
    }

    $check = $conn->prepare("SELECT id, name FROM products WHERE id = ? AND status = 'active'");
    $check->bind_param("i", $product_id);
    $check->execute();
    $prod = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$prod) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại.']); exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO cart (user_id, product_id, quantity)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)"
    );
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);

    if ($stmt->execute()) {
        $cnt = cartCount($conn, $user_id);
        echo json_encode(['success' => true, 'message' => "Đã thêm \"{$prod['name']}\" vào giỏ hàng!", 'cart_count' => $cnt]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm vào giỏ.']);
    }
    $stmt->close();
    exit;
}

// ── CẬP NHẬT SỐ LƯỢNG ────────────────────────────────────
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity   = (int)($_POST['quantity']   ?? 0);

    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ.']); exit;
    }

    if ($quantity <= 0) {
        // Xóa khỏi giỏ nếu qty <= 0
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE user_id=? AND product_id=?");
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
        $stmt->close();
    }

    $cnt = cartCount($conn, $user_id);
    echo json_encode(['success' => true, 'cart_count' => $cnt]);
    exit;
}

// ── XÓA KHỎI GIỎ HÀNG ───────────────────────────────────
if ($action === 'remove' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
    $cnt = cartCount($conn, $user_id);
    echo json_encode(['success' => true, 'cart_count' => $cnt]);
    exit;
}

// ── XÓA TOÀN BỘ GIỎ HÀNG ────────────────────────────────
if ($action === 'clear') {
    $conn->query("DELETE FROM cart WHERE user_id=$user_id");
    echo json_encode(['success' => true, 'cart_count' => 0]);
    exit;
}

// ── LẤY GIỎ HÀNG ────────────────────────────────────────
if ($action === 'get') {
    $result = $conn->query(
        "SELECT c.product_id, c.quantity, p.name, p.price, p.image
         FROM cart c
         JOIN products p ON c.product_id = p.id
         WHERE c.user_id = $user_id"
    );
    $items = [];
    while ($row = $result->fetch_assoc()) $items[] = $row;
    $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
    echo json_encode(['success' => true, 'items' => $items, 'total' => $total]);
    exit;
}

// ── ĐẶT HÀNG ─────────────────────────────────────────────
if ($action === 'checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $note    = trim($_POST['note']    ?? '');

    if (!$address) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập địa chỉ nhận hàng.']); exit;
    }

    $cartResult = $conn->query(
        "SELECT c.product_id, c.quantity, p.name, p.price, p.image, p.stock
         FROM cart c JOIN products p ON c.product_id = p.id
         WHERE c.user_id = $user_id"
    );
    $cartItems = [];
    while ($row = $cartResult->fetch_assoc()) $cartItems[] = $row;

    if (empty($cartItems)) {
        echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống.']); exit;
    }

    $total      = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cartItems));
    $order_code = 'LK' . date('ymd') . strtoupper(substr(uniqid(), -5));

    // Phương thức thanh toán
    $payment_method = trim($_POST['payment_method'] ?? 'cod');
    if (!in_array($payment_method, ['cod','bank_transfer','momo','vnpay'])) $payment_method = 'cod';

    $shipping_fee = 0;
    $discount     = 0;

    $conn->begin_transaction();
    try {
        // Dùng đúng tên cột theo schema DB: ship_address, subtotal, discount, shipping_fee, total, payment_method, payment_status, STATUS
        $stmt = $conn->prepare(
            "INSERT INTO orders
                (user_id, order_code, ship_address, note,
                 subtotal, discount, shipping_fee, total,
                 payment_method, payment_status, STATUS, ordered_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'unpaid', 'pending', NOW())"
        );
        $stmt->bind_param(
            "isssddddds",
            $user_id, $order_code, $address, $note,
            $total, $discount, $shipping_fee, $total,
            $payment_method
        );
        $stmt->execute();
        $order_id = $conn->insert_id;
        $stmt->close();

        // order_items: product_name, product_image, unit_price, quantity, line_total
        $stmtItem = $conn->prepare(
            "INSERT INTO order_items
                (order_id, product_id, product_name, product_image, unit_price, quantity, line_total)
             VALUES (?,?,?,?,?,?,?)"
        );
        foreach ($cartItems as $item) {
            $line_total = $item['price'] * $item['quantity'];
            $stmtItem->bind_param(
                "iissdid",
                $order_id, $item['product_id'], $item['name'], $item['image'],
                $item['price'], $item['quantity'], $line_total
            );
            $stmtItem->execute();
        }
        $stmtItem->close();

        // Trừ stock
        foreach ($cartItems as $item) {
            $conn->query("UPDATE products SET stock = GREATEST(0, stock - {$item['quantity']}) WHERE id = {$item['product_id']}");
        }

        $conn->query("DELETE FROM cart WHERE user_id=$user_id");
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Đặt hàng thành công!', 'order_code' => $order_code]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Lỗi khi đặt hàng: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action không hợp lệ.']);
