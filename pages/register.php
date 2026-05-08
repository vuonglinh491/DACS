<?php
session_start();
if (isset($_SESSION['user_id'])) { header("Location: home.php"); exit; }
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
            <a href="login.php" class="btn-login-nav">
                <i class="fa-regular fa-user"></i> Đăng nhập
            </a>
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
                <button class="btn-social">
                    <i class="fa-brands fa-google" style="color:#DB4437;font-size:16px;"></i>
                    Google
                </button>
                <button class="btn-social">
                    <i class="fa-brands fa-facebook" style="color:#1877F2;font-size:16px;"></i>
                    Facebook
                </button>
            </div>
        </div>
    </main>

    <button class="fab-chat" title="Chat với chúng tôi">
        <i class="fa-regular fa-message"></i>
    </button>


    
<script src="../assets/js/main.js"></script>
</body>
</html>