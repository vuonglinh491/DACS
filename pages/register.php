<?php
session_start();
if (isset($_SESSION['user_id'])) { header("Location: home.php"); exit; }
require_once __DIR__ . '/../env.php';

// ── Tạo URL đăng nhập Google ─────────────────────────────────
$google_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'redirect_uri'  => env('GOOGLE_REDIRECT_URI'),
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'access_type'   => 'online',
    'prompt'        => 'select_account',
]);

// ── Tạo URL đăng nhập Facebook ──────────────────────────────
$fb_url = 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query([
    'client_id'     => env('FB_APP_ID'),
    'redirect_uri'  => env('FB_REDIRECT_URI'),
    'scope'         => 'email,public_profile',
    'response_type' => 'code',
]);

$errors = [
    'empty'    => 'Vui lòng nhập đầy đủ email và mật khẩu.',
    'mismatch' => 'Mật khẩu xác nhận không khớp.',
    'weakpass' => 'Mật khẩu phải có ít nhất 6 ký tự.',
    'exists'   => 'Email này đã được đăng ký.',
    'dbfail'   => 'Lỗi hệ thống, vui lòng thử lại.',
];
$error_msg = $errors[$_GET['error'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - LKSecure</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
</head>
<body class="page-auth">

    <header class="navbar">
        <a href="home.php" class="nav-logo">
            <i class="fa-solid fa-shield-halved"></i> LK Secure
        </a>
        <nav class="nav-links">
            <a href="home.php">TRANG CHỦ</a>
            <a href="products.php">SẢN PHẨM</a>
            <a href="about.php">GIỚI THIỆU</a>
            <a href="contact.php">LIÊN HỆ</a>
        </nav>
        <div class="nav-actions">
            <div class="search-box-dynamic">
                <button class="icon-btn" id="searchToggle" title="Tìm kiếm">
                    <i class="fas fa-search"></i>
                </button>
                <input type="text" id="navSearchInput" placeholder="Tìm sản phẩm...">
            </div>
            <button class="icon-btn" title="Giỏ hàng">
                <i class="fas fa-shopping-cart"></i>
            </button>
            <?php include __DIR__ . '/../config/nav_partial.php'; ?>
        </div>
    </header>

    <main class="main-container">
        <div class="header-title">
            <h1>Tạo tài khoản mới</h1>
            <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
        </div>

        <div class="login-card">
            <form action="../actions/register_action.php" method="POST" id="registerForm">

                <?php if ($error_msg): ?>
                <div style="background:#fef2f2;border:1px solid #fca5a5;color:#dc2626;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:14px;">
                    <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i><?= htmlspecialchars($error_msg) ?>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Email <span class="text-red">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope left-icon"></i>
                        <input type="email" name="email" class="form-control"
                               placeholder="Nhập email của bạn" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Họ và tên <span class="text-red">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user left-icon"></i>
                        <input type="text" name="full_name" class="form-control"
                               placeholder="Nhập họ và tên" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Số điện thoại</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-phone left-icon"></i>
                        <input type="tel" name="phone" class="form-control"
                               placeholder="Ví dụ: 0912 345 678">
                    </div>
                </div>

                <div class="form-group">
                    <label>Mật khẩu <span class="text-red">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock left-icon"></i>
                        <input type="password" name="password" class="form-control"
                               placeholder="Tạo mật khẩu mạnh" required id="reg-pass">
                        <i class="fa-regular fa-eye right-icon toggle-pass" data-target="reg-pass"></i>
                    </div>
                    <div class="password-requirements">
                        <p class="req-8"><i class="fa-solid fa-circle-dot"></i> Ít nhất 8 ký tự</p>
                        <p class="req-upper"><i class="fa-solid fa-circle-dot"></i> 1 chữ hoa (A–Z)</p>
                        <p class="req-lower"><i class="fa-solid fa-circle-dot"></i> 1 chữ thường (a–z)</p>
                        <p class="req-number"><i class="fa-solid fa-circle-dot"></i> 1 chữ số (0–9)</p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Xác nhận mật khẩu <span class="text-red">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock left-icon"></i>
                        <input type="password" name="confirm_password" class="form-control"
                               placeholder="Nhập lại mật khẩu" required id="confirm-pass">
                        <i class="fa-regular fa-eye right-icon toggle-pass" data-target="confirm-pass"></i>
                    </div>
                </div>

                <label class="term-condition">
                    <input type="checkbox" name="terms" required>
                    <span>Tôi đồng ý với <a href="#">Điều khoản sử dụng</a> và <a href="#">Chính sách bảo mật</a></span>
                </label>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-user-plus" style="margin-right:8px;"></i>
                    Tạo tài khoản
                </button>
            </form>

            <div class="divider"><span>Hoặc đăng ký với</span></div>

            <div class="social-login">
                <a href="<?= htmlspecialchars($google_url) ?>" class="btn-social btn-social-google">
                    <svg width="18" height="18" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" fill="#FFC107"/>
                        <path d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z" fill="#FF3D00"/>
                        <path d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" fill="#4CAF50"/>
                        <path d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" fill="#1976D2"/>
                    </svg>
                    Tiếp tục với Google
                </a>
                <a href="<?= htmlspecialchars($fb_url) ?>" class="btn-social btn-social-facebook">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.887v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                    </svg>
                    Tiếp tục với Facebook
                </a>
            </div>
        </div>
    </main>

    <button class="fab-chat" title="Chat với chúng tôi">
        <i class="fa-regular fa-message"></i>
    </button>


    
<script src="../assets/js/main.js"></script>
</body>
</html>