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
$password  = trim($_POST['password']  ?? '');
$confirm   = trim($_POST['confirm_password'] ?? '');

// Validate cơ bản
if (!$email || !$password) {
    header("Location: ../pages/register.php?error=empty");
    exit;
}

if ($password !== $confirm && $confirm !== '') {
    header("Location: ../pages/register.php?error=mismatch");
    exit;
}

if (strlen($password) < 6) {
    header("Location: ../pages/register.php?error=weakpass");
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

// Hash mật khẩu & lưu vào DB
$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
$stmt->bind_param("ssss", $full_name, $email, $phone, $hashed);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../pages/login.php?success=registered");
} else {
    $stmt->close();
    header("Location: ../pages/register.php?error=dbfail");
}
exit;
