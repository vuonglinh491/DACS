<?php
// ============================================================
//  LKSecure — Xử lý đơn hàng (API JSON)
//  Điều kiện hủy: chỉ khi status = pending | confirmed
//  KHÔNG cho hủy khi: shipping | delivered | cancelled
// ============================================================
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập.']);
    exit;
}

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

echo json_encode(['success' => false, 'message' => 'Action không hợp lệ.']);
