<?php
session_start();
// Nếu đã đăng nhập thì chuyển thẳng về home
if (isset($_SESSION['user_id'])) {
    header("Location: home.php"); exit;
}
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

// Bản đồ mã lỗi
$errors = [
    'empty'       => 'Vui lòng nhập email và mật khẩu.',
    'notfound'    => 'Không tìm thấy tài khoản với email này.',
    'wrongpass'   => 'Mật khẩu không chính xác.',
    'oauth_failed'=> 'Đăng nhập mạng xã hội thất bại. Vui lòng thử lại.',
];
$error_msg   = $errors[$_GET['error'] ?? ''] ?? '';
$success_msg = ($_GET['success'] ?? '') === 'registered' ? 'Đăng ký thành công! Hãy đăng nhập.' : '';

// Lưu lại trang cần quay về sau khi đăng nhập
$redirect_to = trim($_GET['redirect_to'] ?? '');
// Giữ lại email đã nhập khi có lỗi
$prefill_email = htmlspecialchars(trim($_GET['email'] ?? ''));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - LKSecure</title>
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
            <h1>Chào mừng trở lại</h1>
            <p>Chưa có tài khoản? <a href="register.php<?= $redirect_to ? '?redirect_to='.urlencode($redirect_to) : '' ?>">Đăng ký miễn phí</a></p>
        </div>

        <div class="login-card">
            <form action="../actions/login_action.php" method="POST" id="loginForm">
                <?php if ($redirect_to): ?>
                <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirect_to) ?>">
                <?php endif; ?>

                <?php if ($error_msg): ?>
                <div style="background:#fef2f2;border:1px solid #fca5a5;color:#dc2626;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:14px;">
                    <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i><?= htmlspecialchars($error_msg) ?>
                </div>
                <?php endif; ?>
                <?php if ($success_msg): ?>
                <div style="background:#f0fdf4;border:1px solid #86efac;color:#16a34a;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:14px;">
                    <i class="fas fa-check-circle" style="margin-right:6px;"></i><?= htmlspecialchars($success_msg) ?>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Email</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope left-icon"></i>
                        <input type="email" name="email" class="form-control"
                               placeholder="Nhập email của bạn" value="<?= $prefill_email ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mật khẩu</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock left-icon"></i>
                        <input type="password" name="password" class="form-control"
                               placeholder="Nhập mật khẩu" required id="pass-input">
                        <i class="fa-regular fa-eye right-icon" id="toggle-pass"></i>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                    </label>
                    <a href="#" class="forgot-pass">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-right-to-bracket" style="margin-right:8px;"></i>
                    Đăng nhập
                </button>
            </form>

            <div class="divider"><span>Hoặc tiếp tục với</span></div>

            <div class="social-login">
                <a href="<?= htmlspecialchars($google_url) ?>" class="btn-social btn-social-google">
                    <svg width="18" height="18" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" fill="#FFC107"/>
                        <path d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z" fill="#FF3D00"/>
                        <path d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" fill="#4CAF50"/>
                        <path d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" fill="#1976D2"/>
                    </svg>
                    Đăng nhập Google
                </a>
                <a href="<?= htmlspecialchars($fb_url) ?>" class="btn-social btn-social-facebook">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.887v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                    </svg>
                    Đăng nhập Facebook
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