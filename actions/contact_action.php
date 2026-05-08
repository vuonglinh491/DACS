<?php
// ============================================================
//  LKSecure — Lưu form liên hệ vào bảng contacts
// ============================================================
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$full_name = trim($_POST['full_name'] ?? '');
$email     = trim($_POST['email']     ?? '');
$phone     = trim($_POST['phone']     ?? '');
$subject   = trim($_POST['subject']   ?? '');
$message   = trim($_POST['message']   ?? '');

if (!$full_name || !$email || !$message) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đủ thông tin bắt buộc.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email không hợp lệ.']);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO contacts (full_name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssss", $full_name, $email, $phone, $subject, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Tin nhắn đã được gửi thành công!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại.']);
}
$stmt->close();
exit;
