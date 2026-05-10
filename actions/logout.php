<?php
// ============================================================
//  LKSecure — Đăng xuất
// ============================================================
session_start();
session_unset();
session_destroy();

// Xóa cookie "Ghi nhớ đăng nhập"
foreach (['remember_user_id', 'remember_user_email', 'remember_user_name', 'remember_user_role'] as $key) {
    setcookie($key, '', time() - 3600, '/', '', false, true);
}

header("Location: ../pages/login.php");
exit;
