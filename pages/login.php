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
            <a href="login.php" class="btn-login-nav">
                <i class="fa-regular fa-user"></i> Đăng nhập
            </a>
        </div>
    </header>

    <main class="main-container">
        <div class="header-title">
            <h1>Chào mừng trở lại</h1>
            <p>Chưa có tài khoản? <a href="register.php">Đăng ký miễn phí</a></p>
        </div>

        <div class="login-card">
            <form action="../actions/login_action.php" method="POST">

                <div class="form-group">
                    <label>Email</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope left-icon"></i>
                        <input type="email" name="email" class="form-control"
                               placeholder="Nhập email của bạn" required>
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