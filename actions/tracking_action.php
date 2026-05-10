<?php
// ============================================================
//  LKSecure — Xử lý theo dõi & hủy đơn hàng
// ============================================================
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'require_login' => true]);
    exit;
}

$action  = $_POST['action'] ?? $_GET['action'] ?? '';
$user_id = (int)$_SESSION['user_id'];

// ── LẤY DANH SÁCH ĐƠN HÀNG ──────────────────────────────
if ($action === 'list') {
    $status_filter = $_GET['status'] ?? '';
    $where = "WHERE o.user_id = $user_id";
    if ($status_filter) $where .= " AND o.status = '" . $conn->real_escape_string($status_filter) . "'";

    $result = $conn->query(
        "SELECT o.id, o.order_code, o.total, o.status, o.address, o.note,
                o.created_at, o.updated_at,
                COUNT(oi.id) AS item_count
         FROM orders o
         LEFT JOIN order_items oi ON o.id = oi.order_id
         $where
         GROUP BY o.id
         ORDER BY o.created_at DESC"
    );

    $orders = [];
    while ($row = $result->fetch_assoc()) $orders[] = $row;
    echo json_encode(['success' => true, 'orders' => $orders]);
    exit;
}

// ── LẤY CHI TIẾT ĐƠN HÀNG ───────────────────────────────
if ($action === 'detail') {
    $order_id = (int)($_GET['id'] ?? 0);

    $stmt = $conn->prepare(
        "SELECT o.*, u.full_name, u.email, u.phone
         FROM orders o JOIN users u ON o.user_id = u.id
         WHERE o.id = ? AND o.user_id = ?"
    );
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Đơn hàng không tồn tại.']);
        exit;
    }

    $items = [];
    $res   = $conn->query("SELECT * FROM order_items WHERE order_id = $order_id");
    while ($row = $res->fetch_assoc()) $items[] = $row;

    echo json_encode(['success' => true, 'order' => $order, 'items' => $items]);
    exit;
}

// ── HỦY ĐƠN HÀNG ────────────────────────────────────────
if ($action === 'cancel' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = (int)($_POST['order_id'] ?? 0);

    // Lấy đơn hàng và kiểm tra quyền
    $stmt = $conn->prepare("SELECT id, status FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Đơn hàng không tồn tại.']);
        exit;
    }

    // Chỉ hủy được nếu chưa chuyển sang "đang giao" hoặc đã giao/hủy
    $cannot_cancel = ['shipping', 'delivered', 'cancelled'];
    if (in_array($order['status'], $cannot_cancel)) {
        echo json_encode([
            'success' => false,
            'message' => $order['status'] === 'cancelled'
                ? 'Đơn hàng đã được hủy trước đó.'
                : 'Không thể hủy đơn hàng đang giao hoặc đã giao.'
        ]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Đơn hàng đã được hủy thành công.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action không hợp lệ.']);