<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LK Secure - Đăng nhập</title>
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div style="background-image: url('assets/imgs/backgroud.png'); ">
    </div>

    <header class="navbar">
        <a href="home.php" class="nav-logo">
            <i class="fa-solid fa-shield-halved"></i> LK Secure
        </a>
        <nav class="nav-links">
            <a href="home.php">TRANG CHỦ</a>
            <a href="#">SẢN PHẨM</a>
            <a href="#">GIỚI THIỆU</a>
            <a href="#">LIÊN HỆ</a>
        </nav>
        <div class="nav-actions">
            <button class="icon-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
            <button class="icon-btn"><i class="fa-solid fa-cart-shopping"></i></button>
            <a href="login.php" class="btn-login-nav">
                <i class="fa-regular fa-user"></i> Đăng nhập
            </a>
        </div>
    </header>

    <main class="main-container">
        <div class="header-title">
            <h1>Đăng nhập tài khoản</h1>
            <p>Hoặc <a href="register.php">tạo tài khoản mới</a></p>
        </div>

        <div class="login-card">
            <form action="../actions/login_action.php" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope left-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="Nhập email của bạn" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mật khẩu</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock left-icon"></i>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required id="pass-input">
                        <i class="fa-regular fa-eye right-icon" id="toggle-pass"></i>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                    </label>
                    <a href="#" class="forgot-pass">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-submit">Đăng nhập</button>
            </form>

            <div class="divider"><span>Hoặc đăng nhập với</span></div>

            <div class="social-login">
                <button class="btn-social"><i class="fa-brands fa-google" style="color: #DB4437;"></i> Google</button>
                <button class="btn-social"><i class="fa-brands fa-facebook" style="color: #1877F2;"></i> Facebook</button>
            </div>
        </div>
    </main>

    <button class="fab-chat"><i class="fa-regular fa-message"></i></button>

    <script>
        document.getElementById('toggle-pass').addEventListener('click', function() {
            const input = document.getElementById('pass-input');
            input.type = input.type === 'password' ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        <script src="../assets/js/script.js"></script>
    </script>
</body>
</html>
<script>
const validateEmail = (email) => {
    return String(email).toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
};

document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('input', function() {
        const wrapper = this.parentElement;
        let isValid = false;

        if (this.type === 'email') {
            isValid = validateEmail(this.value);
        } else if (this.type === 'password' || this.type === 'text') {
            isValid = this.value.length >= 6; // Ví dụ login chỉ cần >= 6 ký tự
        }

        if (isValid) {
            wrapper.classList.add('success');
            wrapper.classList.remove('error');
        } else {
            wrapper.classList.add('error');
            wrapper.classList.remove('success');
        }
        
        if(this.value === "") wrapper.classList.remove('success', 'error');
    });
});
</script>