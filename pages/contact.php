<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên Hệ - LKSecure</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">

    <style>
        /* ===== CONTACT PAGE STYLES ===== */
        body.page-contact {
            background: #f8f9fa;
            color: #1f2937;
        }

        body.page-contact .navbar {
            background: rgba(17, 24, 39, 0.95);
            position: sticky;
            margin-bottom: 0;
        }

        /* Hero */
        .contact-hero {
            background: linear-gradient(135deg, #6d28d9 0%, #a855f7 60%, #0ea5e9 100%);
            padding: 70px 0 80px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .contact-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 50% 0%, rgba(255,255,255,0.12) 0%, transparent 65%);
            pointer-events: none;
        }

        .contact-hero h1 {
            font-size: 46px;
            font-weight: 800;
            margin-bottom: 14px;
            letter-spacing: -0.5px;
        }

        .contact-hero p {
            font-size: 17px;
            color: rgba(255,255,255,0.82);
            max-width: 520px;
            margin: 0 auto;
            line-height: 1.65;
        }

        /* Info cards row */
        .contact-info-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            max-width: 1100px;
            margin: -44px auto 0;
            padding: 0 24px;
            position: relative;
            z-index: 10;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 36px 28px;
            text-align: center;
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
            border: 1px solid #f0f0f0;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .info-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(139,92,246,0.12);
        }

        .info-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 26px;
            color: white;
        }

        .info-icon.purple { background: linear-gradient(135deg, #8b5cf6, #a855f7); }
        .info-icon.blue   { background: linear-gradient(135deg, #3b82f6, #0ea5e9); }
        .info-icon.green  { background: linear-gradient(135deg, #10b981, #34d399); }

        .info-card h3 {
            font-size: 17px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .info-card p, .info-card a {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.65;
            text-decoration: none;
            display: block;
        }

        .info-card a:hover { color: #8b5cf6; }

        /* Main section */
        .contact-main {
            max-width: 1100px;
            margin: 60px auto 80px;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 40px;
            align-items: start;
        }

        /* Form card */
        .contact-form-card {
            background: white;
            border-radius: 24px;
            padding: 44px 44px 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.07);
            border: 1px solid #f0f0f0;
        }

        .form-card-title {
            font-size: 24px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .form-card-sub {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 32px;
        }

        /* Contact form inputs (light theme) */
        .cf-group { margin-bottom: 22px; }

        .cf-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .cf-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .cf-input-wrap { position: relative; }

        .cf-input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 14px;
            pointer-events: none;
        }

        .cf-input-wrap textarea ~ i { top: 18px; transform: none; }

        .cf-input {
            width: 100%;
            padding: 13px 16px 13px 42px;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            color: #1f2937;
            background: #fafafa;
            outline: none;
            transition: all 0.3s;
        }

        .cf-input:focus {
            border-color: #8b5cf6;
            background: white;
            box-shadow: 0 0 0 3px rgba(139,92,246,0.12);
        }

        .cf-input::placeholder { color: #d1d5db; }

        textarea.cf-input {
            resize: vertical;
            min-height: 140px;
            padding-top: 13px;
        }

        .cf-topic-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .topic-btn {
            padding: 10px 12px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            background: #fafafa;
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: all 0.25s;
        }

        .topic-btn:hover, .topic-btn.active {
            border-color: #8b5cf6;
            background: #faf5ff;
            color: #8b5cf6;
        }

        .cf-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #8b5cf6, #a855f7);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(139,92,246,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
        }

        .cf-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139,92,246,0.55);
        }

        /* Sidebar */
        .contact-sidebar { display: flex; flex-direction: column; gap: 24px; }

        .sidebar-card {
            background: white;
            border-radius: 20px;
            padding: 30px 28px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.07);
            border: 1px solid #f0f0f0;
        }

        .sidebar-card h4 {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-card h4 i { color: #8b5cf6; }

        /* Working hours */
        .hours-list { list-style: none; }

        .hours-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            font-size: 13px;
            border-bottom: 1px solid #f3f4f6;
            color: #6b7280;
        }

        .hours-list li:last-child { border-bottom: none; }

        .hours-list .day { font-weight: 600; color: #374151; }
        .hours-list .badge-open {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            background: #d1fae5;
            color: #065f46;
        }
        .hours-list .badge-closed {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            background: #fee2e2;
            color: #991b1b;
        }

        /* Social */
        .social-links {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .social-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s;
            border: 1.5px solid #e5e7eb;
            color: #374151;
            background: #fafafa;
        }

        .social-link:hover { transform: translateY(-2px); }
        .social-link.fb:hover { border-color: #1877F2; color: #1877F2; background: #eff6ff; }
        .social-link.yt:hover { border-color: #ff0000; color: #ff0000; background: #fff1f1; }
        .social-link.zalo:hover { border-color: #0068ff; color: #0068ff; background: #eff6ff; }

        /* Map placeholder */
        .map-section {
            max-width: 1100px;
            margin: 0 auto 80px;
            padding: 0 24px;
        }

        .map-section h3 {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 18px;
        }

        .map-embed {
            width: 100%;
            height: 320px;
            border-radius: 20px;
            border: none;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(80px);
            background: #1f2937;
            color: white;
            padding: 14px 28px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
            z-index: 9999;
            transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast i { color: #10b981; font-size: 18px; }

        /* Responsive tweaks */
        @media (max-width: 900px) {
            .contact-info-row { grid-template-columns: 1fr; margin-top: 30px; }
            .contact-main { grid-template-columns: 1fr; }
            .cf-row { grid-template-columns: 1fr; }
            .cf-topic-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body class="page-contact">

<!-- NAVBAR -->
<header class="navbar">
    <a href="home.php" class="nav-logo">
        <i class="fa-solid fa-shield-halved"></i> LK Secure
    </a>
    <nav class="nav-links">
        <a href="home.php">TRANG CHỦ</a>
        <a href="products.php">SẢN PHẨM</a>
        <a href="about.php">GIỚI THIỆU</a>
        <a href="contact.php" class="active">LIÊN HỆ</a>
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

<!-- HERO -->
<section class="contact-hero">
    <h1>Liên hệ với chúng tôi</h1>
    <p>Đội ngũ hỗ trợ của LKSecure luôn sẵn sàng giải đáp mọi thắc mắc và tư vấn giải pháp phù hợp nhất cho bạn.</p>
</section>

<!-- INFO CARDS -->
<div class="contact-info-row">
    <div class="info-card">
        <div class="info-icon purple"><i class="fas fa-phone-volume"></i></div>
        <h3>Hotline hỗ trợ</h3>
        <a href="tel:0123456789">0393 860 031</a>
        <a href="tel:0987654321">0987 654 321</a>
        <p style="margin-top:8px;font-size:12px;">Thứ 2 – Thứ 7 &nbsp;|&nbsp; 8:00 – 18:00</p>
    </div>
    <div class="info-card">
        <div class="info-icon blue"><i class="fas fa-envelope-open-text"></i></div>
        <h3>Email liên hệ</h3>
        <a href="mailto:info@lksecure.vn">ngo91168@gmail.com</a>
        <p style="margin-top:8px;font-size:12px;">Phản hồi trong vòng 24 giờ</p>
    </div>
    <div class="info-card">
        <div class="info-icon green"><i class="fas fa-location-dot"></i></div>
        <h3>Địa chỉ cửa hàng</h3>
        <p>Thôn Khôn Duy - Xã Trần Phú<br> TP. Hà Nội </p>
        <a href="https://maps.app.goo.gl/w3JmmCNVVHRHviTo9" target="_blank" style="margin-top:8px;color:#8b5cf6;font-weight:600;">
            <i class="fas fa-map"></i> Xem bản đồ
        </a>
    </div>
</div>

<!-- MAIN: FORM + SIDEBAR -->
<div class="contact-main">

    <!-- Form -->
    <div class="contact-form-card">
        <p class="form-card-title">Gửi tin nhắn cho chúng tôi</p>
        <p class="form-card-sub">Điền thông tin bên dưới, chúng tôi sẽ phản hồi trong thời gian sớm nhất.</p>

        <form id="contactForm">
            <div class="cf-row">
                <div class="cf-group">
                    <label>Họ và tên <span style="color:#ef4444;">*</span></label>
                    <div class="cf-input-wrap">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" class="cf-input" placeholder="Nguyễn Văn A" required>
                    </div>
                </div>
                <div class="cf-group">
                    <label>Số điện thoại</label>
                    <div class="cf-input-wrap">
                        <i class="fas fa-phone"></i>
                        <input type="tel" class="cf-input" placeholder="0912 345 678">
                    </div>
                </div>
            </div>

            <div class="cf-group">
                <label>Email <span style="color:#ef4444;">*</span></label>
                <div class="cf-input-wrap">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" class="cf-input" placeholder="example@email.com" required>
                </div>
            </div>

            <div class="cf-group">
                <label>Chủ đề</label>
                <div class="cf-topic-grid">
                    <button type="button" class="topic-btn active" onclick="selectTopic(this)">Tư vấn sản phẩm</button>
                    <button type="button" class="topic-btn" onclick="selectTopic(this)">Bảo hành / Sửa chữa</button>
                    <button type="button" class="topic-btn" onclick="selectTopic(this)">Lắp đặt</button>
                    <button type="button" class="topic-btn" onclick="selectTopic(this)">Báo giá</button>
                    <button type="button" class="topic-btn" onclick="selectTopic(this)">Khiếu nại</button>
                    <button type="button" class="topic-btn" onclick="selectTopic(this)">Khác</button>
                </div>
            </div>

            <div class="cf-group">
                <label>Nội dung <span style="color:#ef4444;">*</span></label>
                <div class="cf-input-wrap">
                    <i class="fa-regular fa-comment-dots"></i>
                    <textarea class="cf-input" placeholder="Mô tả chi tiết yêu cầu của bạn..." required></textarea>
                </div>
            </div>

            <button type="submit" class="cf-submit">
                <i class="fas fa-paper-plane"></i>
                Gửi tin nhắn
            </button>
        </form>
    </div>

    <!-- Sidebar -->
    <div class="contact-sidebar">

        <!-- Giờ làm việc -->
        <div class="sidebar-card">
            <h4><i class="fas fa-clock"></i> Giờ làm việc</h4>
            <ul class="hours-list">
                <li><span class="day">Thứ 2 – Thứ 6</span><span class="badge-open">08:00 – 18:00</span></li>
                <li><span class="day">Thứ 7</span><span class="badge-open">08:00 – 17:00</span></li>
                <li><span class="day">Chủ nhật</span><span class="badge-closed">Nghỉ</span></li>
                <li><span class="day">Lễ / Tết</span><span class="badge-closed">Theo thông báo</span></li>
            </ul>
        </div>

        <!-- Kết nối mạng xã hội -->
        <div class="sidebar-card">
            <h4><i class="fas fa-share-nodes"></i> Mạng xã hội</h4>
            <div class="social-links">
                <a href="https://www.facebook.com/share/1EApU6ENs1/" class="social-link fb">
                    <i class="fa-brands fa-facebook" style="color:#1877F2;"></i> Facebook
                </a>
               <a href="https://www.instagram.com/linhvuong294?igsh=OHZnMTVuOXQ3c2h2" class="social-link ig">
                    <i class="fa-brands fa-instagram" style="color:#E4405F;"></i> Instagram
                </a>
                <a href="https://zalo.me/0393860031" class="social-link zalo">
                    <i class="fas fa-comment-dots" style="color:#0068ff;"></i> Zalo
                </a>
            </div>
        </div>

        <!-- Hỗ trợ nhanh -->
        <div class="sidebar-card" style="background:linear-gradient(135deg,#8b5cf6,#a855f7);color:white;border:none;">
            <h4 style="color:white;"><i class="fas fa-headset"></i> Hỗ trợ khẩn cấp</h4>
            <p style="font-size:13px;color:rgba(255,255,255,0.85);margin-bottom:18px;line-height:1.6;">
                Cần hỗ trợ gấp về camera hay hệ thống báo động? Gọi ngay hotline 24/7 của chúng tôi.
            </p>
            <a href="tel:0123456789" style="
                display:flex;align-items:center;justify-content:center;gap:8px;
                background:white;color:#8b5cf6;
                padding:12px 20px;border-radius:10px;
                font-weight:700;font-size:14px;text-decoration:none;
                transition:0.25s;
            ">
                <i class="fas fa-phone"></i> 0123 456 789
            </a>
        </div>
    </div>
</div>

<!-- MAP -->
<div class="map-section">
    <h3><i class="fas fa-map-location-dot" style="color:#8b5cf6;margin-right:8px;"></i>Vị trí showroom</h3>
    <iframe
        class="map-embed"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4559!2d106.6981!3d10.7769!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTDCsDQ2JzM3LjEiTiAxMDbCsDQxJzUzLjIiRQ!5e0!3m2!1svi!2s!4v1620000000000"
        allowfullscreen
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</div>

<!-- FOOTER -->
<footer class="main-footer-dark">
    <div class="container">
        <div class="footer-content">
            <div class="footer-info">
                <div class="nav-logo white-text"><i class="fas fa-shield-halved"></i> LKSecure</div>
                <p>An tâm cho mọi gia đình Việt với giải pháp an ninh toàn diện.</p>
            </div>
            <div class="footer-links">
                <h5>Liên kết</h5>
                <a href="home.php">Trang chủ</a>
                <a href="products.php">Sản phẩm</a>
                <a href="about.php">Giới thiệu</a>
                <a href="contact.php">Liên hệ</a>
            </div>
            <div class="footer-support">
                <h5>Hỗ trợ</h5>
                <ul>
                    <li><a href="warranty.php">Bảo hành</a></li>
                    <li><a href="policy.php">Chính sách</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h5>Thông tin liên hệ</h5>
                <p><i class="fas fa-phone"></i> 0393 860 031</p>
                <p><i class="fas fa-envelope"></i> ngo91168@gmail.com</p>
            </div>
        </div>
        <div class="footer-line"></div>
        <p class="copyright">© 2026 LKSecure. Tất cả quyền được bảo lưu.</p>
    </div>
</footer>

<!-- TOAST NOTIFICATION -->
<div class="toast" id="toast">
    <i class="fas fa-check-circle"></i>
    Tin nhắn đã được gửi! Chúng tôi sẽ phản hồi sớm nhất.
</div>

<button class="fab-chat" title="Chat với chúng tôi">
    <i class="fa-regular fa-message"></i>
</button>



<script src="../assets/js/main.js"></script>
</body>
</html>