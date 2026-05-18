<?php
// ============================================================
//  LKSecure — Admin: Quản lý đơn hàng (API JSON)
// ============================================================
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth_check.php';
header('Content-Type: application/json; charset=utf-8');

requireAdmin(true); // Chỉ admin

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ── CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG ────────────────────────
if ($action === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id  = (int)($_POST['order_id'] ?? 0);
    $newStatus = $_POST['status'] ?? '';

    $allowed = ['pending', 'confirmed', 'processing', 'shipping', 'delivered', 'cancelled'];
    if (!$order_id || !in_array($newStatus, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ.']);
        exit;
    }

    // Nếu cancelled → hoàn lại stock
    if ($newStatus === 'cancelled') {
        $current = $conn->query("SELECT status FROM orders WHERE id = $order_id")->fetch_assoc();
        if ($current && !in_array($current['status'], ['cancelled', 'delivered'])) {
            $items = $conn->query("SELECT product_id, quantity FROM order_items WHERE order_id = $order_id");
            if ($items) {
                while ($it = $items->fetch_assoc()) {
                    $conn->query("UPDATE products SET stock = stock + {$it['quantity']} WHERE id = {$it['product_id']}");
                }
            }
        }
    }

    // Nếu delivered → đánh dấu paid
    $extraSQL = $newStatus === 'delivered' ? ", payment_status = 'paid'" : '';

    $stmt = $conn->prepare("UPDATE orders SET status = ? $extraSQL WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $order_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Đã cập nhật trạng thái đơn hàng.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng hoặc không có thay đổi.']);
    }
    $stmt->close();
    exit;
}

// ── XEM CHI TIẾT ĐƠN HÀNG ───────────────────────────────
if ($action === 'detail' && isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    $stmt = $conn->prepare(
        "SELECT o.*, u.full_name, u.email, u.phone AS user_phone
         FROM orders o
         JOIN users u ON o.user_id = u.id
         WHERE o.id = ?"
    );
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$order) { echo json_encode(['success' => false, 'message' => 'Không tìm thấy.']); exit; }

    $items = [];
    $iRes = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $iRes->bind_param("i", $order_id);
    $iRes->execute();
    $iResult = $iRes->get_result();
    while ($row = $iResult->fetch_assoc()) $items[] = $row;
    $iRes->close();

    $order['items'] = $items;
    $order['ordered_at'] = date('d/m/Y H:i', strtotime($order['ordered_at']));
    echo json_encode(['success' => true, 'order' => $order]);
    exit;
}

// ── THỐNG KÊ NHANH ───────────────────────────────────────
if ($action === 'stats') {
    $period = $_GET['period'] ?? '7'; // ngày
    $period = (int)$period;

    $stats = [];
    $stats['orders']      = $conn->query("SELECT COUNT(*) c FROM orders WHERE ordered_at >= DATE_SUB(NOW(), INTERVAL $period DAY)")->fetch_assoc()['c'] ?? 0;
    $stats['revenue']     = $conn->query("SELECT COALESCE(SUM(total),0) c FROM orders WHERE status='delivered' AND ordered_at >= DATE_SUB(NOW(), INTERVAL $period DAY)")->fetch_assoc()['c'] ?? 0;
    $stats['new_users']   = $conn->query("SELECT COUNT(*) c FROM users WHERE role='customer' AND created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)")->fetch_assoc()['c'] ?? 0;
    $stats['pending_cnt'] = $conn->query("SELECT COUNT(*) c FROM orders WHERE status='pending'")->fetch_assoc()['c'] ?? 0;

    // Doanh thu theo ngày
    $chartRes = $conn->query(
        "SELECT DATE(ordered_at) AS day, SUM(total) AS revenue
         FROM orders WHERE status='delivered' AND ordered_at >= DATE_SUB(NOW(), INTERVAL $period DAY)
         GROUP BY DATE(ordered_at) ORDER BY day ASC"
    );
    $chart = [];
    if ($chartRes) while ($row = $chartRes->fetch_assoc()) $chart[] = $row;
    $stats['chart'] = $chart;

    echo json_encode(['success' => true, 'stats' => $stats]);
    exit;
}

// ── XÓA ĐƠN HÀNG (admin) ─────────────────────────────────
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    if (!$order_id) { echo json_encode(['success' => false, 'message' => 'ID không hợp lệ.']); exit; }

    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã xóa đơn hàng.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa.']);
    }
    $stmt->close();
    exit;
}

// ── ADMIN KHOÁ / MỞ USER ─────────────────────────────────
if ($action === 'toggle_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id  = (int)($_POST['user_id'] ?? 0);
    $is_active = (int)($_POST['is_active'] ?? 0); // 1 = active, 0 = locked

    if (!$user_id) { echo json_encode(['success' => false, 'message' => 'ID không hợp lệ.']); exit; }

    $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE id = ? AND role != 'admin'");
    $stmt->bind_param("ii", $is_active, $user_id);
    if ($stmt->execute()) {
        $label = $is_active ? 'Đã mở khóa tài khoản.' : 'Đã khóa tài khoản.';
        echo json_encode(['success' => true, 'message' => $label]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật.']);
    }
    $stmt->close();
    exit;
}

// ── LẤY DANH SÁCH LIÊN HỆ ────────────────────────────────
if ($action === 'contacts') {
    $result = $conn->query(
        "SELECT id, full_name, email, phone, subject, message, is_read, created_at
         FROM contacts ORDER BY is_read ASC, created_at DESC LIMIT 50"
    );
    $contacts = [];
    while ($row = $result->fetch_assoc()) {
        $row['created_at'] = date('d/m/Y H:i', strtotime($row['created_at']));
        $contacts[] = $row;
    }
    echo json_encode(['success' => true, 'contacts' => $contacts]);
    exit;
}

// ── LẤY DANH SÁCH BẢO HÀNH ───────────────────────────────
if ($action === 'warranties') {
    $result = $conn->query(
        "SELECT w.id, w.product_name, w.serial_number, w.issue_desc, w.status,
                w.created_at, u.full_name, u.email
         FROM warranty_requests w
         JOIN users u ON w.user_id = u.id
         ORDER BY w.status = 'pending' DESC, w.created_at DESC LIMIT 50"
    );
    $warranties = [];
    while ($row = $result->fetch_assoc()) {
        $row['created_at'] = date('d/m/Y H:i', strtotime($row['created_at']));
        $warranties[] = $row;
    }
    echo json_encode(['success' => true, 'warranties' => $warranties]);
    exit;
}

// ── CẬP NHẬT TRẠNG THÁI BẢO HÀNH ────────────────────────
if ($action === 'update_warranty' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int)($_POST['id'] ?? 0);
    $status    = $_POST['status'] ?? '';
    $adminNote = trim($_POST['admin_note'] ?? '');

    $allowed = ['pending', 'processing', 'resolved', 'rejected'];
    if (!$id || !in_array($status, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']); exit;
    }

    $stmt = $conn->prepare("UPDATE warranty_requests SET status = ?, admin_note = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $status, $adminNote, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã cập nhật yêu cầu bảo hành.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật.']);
    }
    $stmt->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action không hợp lệ.']);
