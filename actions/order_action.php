<?php
// ============================================================
//  LKSecure — Xử lý đăng nhập
// ============================================================
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/login.php");
    exit;
}

$email    = trim($_POST['email']   ?? '');
$password = trim($_POST['password'] ?? '');

if (!$email || !$password) {
    header("Location: ../pages/login.php?error=empty");
    exit;
}

// Prepared statement — chống SQL injection
$stmt = $conn->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = ? AND is_active = 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../pages/login.php?error=notfound");
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

if (!password_verify($password, $user['password'])) {
    header("Location: ../pages/login.php?error=wrongpass");
    exit;
}

// Lưu session
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name']  = $user['full_name'] ?? $user['email'];
$_SESSION['user_role']  = $user['role'];

// Điều hướng theo role
if ($user['role'] === 'admin') {
    header("Location: ../pages/admin.php");
} else {
    header("Location: ../pages/home.php");
}
exit;
