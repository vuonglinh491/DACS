<?php
// ============================================================
//  LKSecure — Xử lý đăng ký tài khoản
// ============================================================
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/register.php");
    exit;
}

$full_name = trim($_POST['full_name'] ?? '');
$email     = trim($_POST['email']     ?? '');
$phone     = trim($_POST['phone']     ?? '');
$password  = $_POST['password']       ?? '';
$confirm   = $_POST['confirm_password'] ?? '';

// --- Validate ---
if (!$email || !$password || !$full_name) {
    header("Location: ../pages/register.php?error=empty");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../pages/register.php?error=invalidemail");
    exit;
}

// Mật khẩu: ít nhất 8 ký tự, 1 hoa, 1 thường, 1 số
if (strlen($password) < 8
    || !preg_match('/[A-Z]/', $password)
    || !preg_match('/[a-z]/', $password)
    || !preg_match('/[0-9]/', $password)
) {
    header("Location: ../pages/register.php?error=weakpass");
    exit;
}

if ($confirm !== $password) {
    header("Location: ../pages/register.php?error=mismatch");
    exit;
}

// Kiểm tra email đã tồn tại chưa
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    $check->close();
    header("Location: ../pages/register.php?error=exists");
    exit;
}
$check->close();

// Hash & lưu
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt   = $conn->prepare("INSERT INTO users (full_name, email, phone, password, role, is_active) VALUES (?, ?, ?, ?, 'customer', 1)");
$stmt->bind_param("ssss", $full_name, $email, $phone, $hashed);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../pages/login.php?success=registered");
} else {
    $stmt->close();
    header("Location: ../pages/register.php?error=dbfail");
}
exit;
