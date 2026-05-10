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

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$remember = isset($_POST['remember']);

// Lấy trang redirect (nếu có), validate an toàn
$redirect_to = trim($_POST['redirect_to'] ?? '');
if ($redirect_to && (strpos($redirect_to, '..') !== false || strpos($redirect_to, 'http') !== false)) {
    $redirect_to = '';
}

if (!$email || !$password) {
    $qs = ($redirect_to ? '&redirect_to=' . urlencode($redirect_to) : '') . ($email ? '&email=' . urlencode($email) : '');
    header("Location: ../pages/login.php?error=empty$qs");
    exit;
}

// Prepared statement — chống SQL injection
$stmt = $conn->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = ? AND is_active = 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $qs = ($redirect_to ? '&redirect_to=' . urlencode($redirect_to) : '') . '&email=' . urlencode($email);
    header("Location: ../pages/login.php?error=notfound$qs");
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

if (!password_verify($password, $user['password'])) {
    $qs = ($redirect_to ? '&redirect_to=' . urlencode($redirect_to) : '') . '&email=' . urlencode($email);
    header("Location: ../pages/login.php?error=wrongpass$qs");
    exit;
}

// Lưu session
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name']  = $user['full_name'] ?: $user['email'];
$_SESSION['user_role']  = $user['role'];

// Ghi nhớ đăng nhập — cookie 30 ngày
if ($remember) {
    $token = bin2hex(random_bytes(32));
    // Lưu token vào DB (bảng remember_tokens nếu có), hoặc dùng cookie session mở rộng
    setcookie('remember_user_id',    $user['id'],    time() + 86400 * 30, '/', '', false, true);
    setcookie('remember_user_email', $user['email'], time() + 86400 * 30, '/', '', false, true);
    setcookie('remember_user_name',  $user['full_name'] ?: $user['email'], time() + 86400 * 30, '/', '', false, true);
    setcookie('remember_user_role',  $user['role'],  time() + 86400 * 30, '/', '', false, true);
}

// Redirect về trang trước (nếu bị chặn) hoặc theo role
if ($redirect_to) {
    header("Location: " . $redirect_to);
} elseif ($user['role'] === 'admin') {
    header("Location: ../pages/admin.php");
} else {
    header("Location: ../pages/home.php");
}
exit;
