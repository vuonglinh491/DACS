<?php
// ============================================================
//  LKSecure — Xử lý yêu cầu bảo hành
//  Bảng: warranty_requests
// ============================================================

// ✅ ob_start() ngăn PHP warning/notice phá vỡ JSON output
ob_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth_check.php';
ob_clean(); // xoá output thừa trước JSON
header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ── GỬI YÊU CẦU BẢO HÀNH ────────────────────────────────
if ($action === 'submit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(true);
    $user_id       = (int)$_SESSION['user_id'];
    $product_name  = trim($_POST['product_name']  ?? '');
    $serial_number = trim($_POST['serial_number'] ?? '');
    $purchase_date = trim($_POST['purchase_date'] ?? '');
    $issue_desc    = trim($_POST['issue_desc']    ?? '');
    $order_item_id = (int)($_POST['order_item_id'] ?? 0);

    if (!$product_name || !$issue_desc) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ tên sản phẩm và mô tả lỗi.']);
        exit;
    }

    // Validate ngày mua
    $pd_value = null;
    if ($purchase_date) {
        $dt = DateTime::createFromFormat('Y-m-d', $purchase_date);
        if ($dt && $dt->format('Y-m-d') === $purchase_date && $dt <= new DateTime()) {
            $pd_value = $purchase_date;
        } else {
            echo json_encode(['success' => false, 'message' => 'Ngày mua không hợp lệ.']);
            exit;
        }
    }

    $order_item_id = $order_item_id > 0 ? $order_item_id : null;

    $stmt = $conn->prepare(
        "INSERT INTO warranty_requests
            (user_id, order_item_id, product_name, serial_number, purchase_date, issue_desc, STATUS, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())"
    );
    $stmt->bind_param(
        "iissss",
        $user_id, $order_item_id, $product_name, $serial_number, $pd_value, $issue_desc
    );

    if ($stmt->execute()) {
        $req_id = $conn->insert_id;
        $stmt->close();
        echo json_encode([
            'success'    => true,
            'message'    => 'Yêu cầu bảo hành đã được gửi! Chúng tôi sẽ phản hồi trong vòng 24 giờ.',
            'request_id' => $req_id,
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi gửi yêu cầu: ' . $conn->error]);
    }
    exit;
}

// ── DANH SÁCH YÊU CẦU CỦA USER ──────────────────────────
if ($action === 'my_list') {
    requireLogin(true);
    $user_id = (int)$_SESSION['user_id'];

    $stmt = $conn->prepare(
        "SELECT id, product_name, serial_number, purchase_date, issue_desc,
                STATUS, admin_note, created_at, updated_at
         FROM warranty_requests
         WHERE user_id = ?
         ORDER BY created_at DESC"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result   = $stmt->get_result();
    $requests = [];
    $status_labels = [
        'pending'    => 'Đang chờ xử lý',
        'processing' => 'Đang xử lý',
        'resolved'   => 'Đã giải quyết',
        'rejected'   => 'Từ chối',
    ];
    $status_colors = [
        'pending'    => '#f59e0b',
        'processing' => '#3b82f6',
        'resolved'   => '#10b981',
        'rejected'   => '#ef4444',
    ];
    while ($row = $result->fetch_assoc()) {
        $row['status_label'] = $status_labels[$row['STATUS']] ?? $row['STATUS'];
        $row['status_color'] = $status_colors[$row['STATUS']]  ?? '#8b5cf6';
        $row['created_at']   = date('d/m/Y H:i', strtotime($row['created_at']));
        $requests[] = $row;
    }
    $stmt->close();
    echo json_encode(['success' => true, 'requests' => $requests]);
    exit;
}

// ── [ADMIN] DANH SÁCH TẤT CẢ YÊU CẦU ────────────────────
if ($action === 'admin_list') {
    requireAdmin(true);

    $status_filter = $_GET['status'] ?? 'all';
    $where = '';
    if ($status_filter !== 'all') {
        $sf    = $conn->real_escape_string($status_filter);
        $where = "WHERE wr.STATUS = '$sf'";
    }

    $result = $conn->query(
        "SELECT wr.id, wr.product_name, wr.serial_number, wr.purchase_date,
                wr.issue_desc, wr.STATUS, wr.admin_note,
                wr.created_at, wr.updated_at,
                u.full_name AS user_name, u.email AS user_email, u.phone AS user_phone
         FROM warranty_requests wr
         JOIN users u ON wr.user_id = u.id
         $where
         ORDER BY wr.created_at DESC"
    );
    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $row['created_at'] = date('d/m/Y H:i', strtotime($row['created_at']));
        $requests[] = $row;
    }
    echo json_encode(['success' => true, 'requests' => $requests]);
    exit;
}

// ── [ADMIN] CẬP NHẬT TRẠNG THÁI ─────────────────────────
if ($action === 'admin_update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireAdmin(true);

    $req_id     = (int)($_POST['request_id'] ?? 0);
    $new_status = trim($_POST['status']      ?? '');
    $admin_note = trim($_POST['admin_note']  ?? '');
    $allowed    = ['pending', 'processing', 'resolved', 'rejected'];

    if (!$req_id || !in_array($new_status, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE warranty_requests
         SET STATUS = ?, admin_note = ?, updated_at = NOW()
         WHERE id = ?"
    );
    $stmt->bind_param("ssi", $new_status, $admin_note, $req_id);
    $stmt->execute();

    if ($stmt->affected_rows >= 0) {
        echo json_encode(['success' => true, 'message' => 'Đã cập nhật trạng thái bảo hành.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy yêu cầu.']);
    }
    $stmt->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action không hợp lệ.']);
