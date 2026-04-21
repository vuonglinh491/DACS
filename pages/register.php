<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LK Secure - Tạo tài khoản mới</title>
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

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
            <h1>Tạo tài khoản mới</h1>
            <p>Hoặc <a href="login.php">đăng nhập nếu đã có tài khoản</a></p>
        </div>

        <div class="login-card">
            <form action="../actions/register_action.php" method="POST" id="registerForm">
                
                <div class="form-group">
                    <label>Email <span class="text-red">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope left-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="Nhập email của bạn" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Họ và tên <span class="text-red">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user left-icon"></i>
                        <input type="text" name="fullname" class="form-control" placeholder="Nhập họ và tên" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Số điện thoại</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-phone left-icon"></i>
                        <input type="tel" name="phone" class="form-control" placeholder="Ví dụ: 0912345678">
                    </div>
                </div>

                <div class="form-group">
                    <label>Mật khẩu <span class="text-red">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock left-icon"></i>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required id="reg-pass">
                        <i class="fa-regular fa-eye right-icon toggle-pass" data-target="reg-pass"></i>
                    </div>
                    <div class="password-requirements">
                        <p class="req-8"><i class="fa-solid fa-circle-dot"></i> Ít nhất 8 ký tự</p>
                        <p class="req-upper"><i class="fa-solid fa-circle-dot"></i> 1 chữ hoa</p>
                        <p class="req-lower"><i class="fa-solid fa-circle-dot"></i> 1 chữ thường</p>
                        <p class="req-number"><i class="fa-solid fa-circle-dot"></i> 1 chữ số</p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Xác nhận mật khẩu <span class="text-red">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock left-icon"></i>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu" required id="confirm-pass">
                        <i class="fa-regular fa-eye right-icon toggle-pass" data-target="confirm-pass"></i>
                    </div>
                </div>

                <label class="term-condition">
                    <input type="checkbox" name="terms" required>
                    <span>Tôi đồng ý với <a href="#">Điều khoản sử dụng</a> và <a href="#">Chính sách bảo mật</a></span>
                </label>

                <button type="submit" class="btn-submit">Đăng ký</button>
            </form>

            <div class="divider"><span>Hoặc đăng ký với</span></div>

            <div class="social-login">
                <button class="btn-social"><i class="fa-brands fa-google" style="color: #DB4437;"></i> Google</button>
                <button class="btn-social"><i class="fa-brands fa-facebook" style="color: #1877F2;"></i> Facebook</button>
            </div>
        </div>
    </main>

    <button class="fab-chat"><i class="fa-regular fa-message"></i></button>

    <script>
    // 1. Hàm kiểm tra định dạng
    const validateEmail = (email) => /^\S+@\S+\.\S+$/.test(email);
    
    // Fix lỗi Phone: Đầu số 03, 05, 07, 08, 09 và đủ 10 số
    const validatePhone = (phone) => /^(0[3|5|7|8|9])([0-9]{8})$/.test(phone);

    const updateRequirement = (regex, value, elementId) => {
        const el = document.querySelector(elementId);
        if (!el) return false;
        if (regex.test(value)) {
            el.style.color = '#10b981';
            el.querySelector('i').className = 'fa-solid fa-check';
            return true;
        } else {
            el.style.color = '#6b7280';
            el.querySelector('i').className = 'fa-solid fa-circle-dot';
            return false;
        }
    };

    // 2. Xử lý kiểm tra khi nhập liệu (Real-time Validation)
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            const wrapper = this.parentElement;
            const val = this.value.trim();
            let isValid = false;

            // Nếu người dùng xóa hết chữ -> Xóa màu viền (không báo đỏ)
            if (val === "") {
                wrapper.classList.remove('success', 'error');
                if (this.name === 'password') {
                    ['.req-8', '.req-upper', '.req-lower', '.req-number'].forEach(id => {
                        const el = document.querySelector(id);
                        if(el) {
                            el.style.color = '#6b7280';
                            el.querySelector('i').className = 'fa-solid fa-circle-dot';
                        }
                    });
                }
                return;
            }

            // Logic kiểm tra theo từng loại trường nhập
            if (this.name === 'email') {
                isValid = validateEmail(val);
            } 
            else if (this.name === 'fullname') {
                isValid = val.length >= 2;
            } 
            else if (this.name === 'phone') {
                isValid = validatePhone(val);
            } 
            else if (this.name === 'password') {
                const r1 = updateRequirement(/.{8,}/, val, '.req-8');
                const r2 = updateRequirement(/[A-Z]/, val, '.req-upper');
                const r3 = updateRequirement(/[a-z]/, val, '.req-lower');
                const r4 = updateRequirement(/[0-9]/, val, '.req-number');
                isValid = r1 && r2 && r3 && r4;
            } 
            else if (this.name === 'confirm_password') {
                const passVal = document.querySelector('input[name="password"]').value;
                isValid = (val === passVal && val !== "");
            }

            // Cập nhật class CSS để đổi màu viền
            if (isValid) {
                wrapper.classList.add('success');
                wrapper.classList.remove('error');
            } else {
                wrapper.classList.add('error');
                wrapper.classList.remove('success');
            }
        });
    });

    // 3. Logic ẩn/hiện mật khẩu
    document.querySelectorAll('.toggle-pass').forEach(icon => {
        icon.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                this.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
    </script>
</body>
</html>