<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
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
        <button class="icon-btn cart-icon-btn" title="Giỏ hàng">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-badge" style="display:none;"></span>
        </button>
        <?php include __DIR__ . '/../config/nav_partial.php'; ?>
    </div>
</header>

<!-- HERO -->
<section class="contact-hero">
    <div class="contact-hero-inner">
        <div class="contact-hero-badge">
            <i class="fas fa-headset"></i> Hỗ trợ 24/7
        </div>
        <h1>Liên hệ với chúng tôi</h1>
        <p>Đội ngũ hỗ trợ của LKSecure luôn sẵn sàng giải đáp mọi thắc mắc và tư vấn giải pháp phù hợp nhất cho bạn.</p>
    </div>
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
        <div class="sidebar-card urgent">
            <h4><i class="fas fa-headset"></i> Hỗ trợ khẩn cấp</h4>
            <p class="urgent-desc">
                Cần hỗ trợ gấp về camera hay hệ thống báo động? Gọi ngay hotline 24/7 của chúng tôi.
            </p>
            <a href="tel:0393860031" class="urgent-call">
                <i class="fas fa-phone"></i> 0393 860 031
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
<div class="contact-toast" id="contact-toast">
    <i class="fas fa-check-circle"></i>
    Tin nhắn đã được gửi! Chúng tôi sẽ phản hồi sớm nhất.
</div>

<button class="fab-chat" title="Chat với chúng tôi">
    <i class="fa-regular fa-message"></i>
</button>



<script src="../assets/js/main.js"></script>
<script>
    var USER_LOGGED_IN = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    var LOGIN_URL = 'login.php';
</script>
</body>
</html>