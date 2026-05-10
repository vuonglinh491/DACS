<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản Phẩm - LKSecure</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
</head>
<body class="page-about">

<!-- NAVBAR -->
<header class="navbar">
    <a href="home.php" class="nav-logo">
        <i class="fa-solid fa-shield-halved"></i> LK Secure
    </a>
    <nav class="nav-links">
        <a href="home.php">TRANG CHỦ</a>
        <a href="products.php">SẢN PHẨM</a>
        <a href="about.php" class="active">GIỚI THIỆU</a>
        <a href="contact.php">LIÊN HỆ</a>
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

<!-- ===== HERO GIỚI THIỆU ===== -->
<section class="about-hero">
    <div class="about-hero-content">
        <span class="about-badge"><i class="fas fa-shield-halved"></i> Về Chúng Tôi</span>
        <h1>Bảo Vệ Những Gì <span>Quan Trọng Nhất</span></h1>
        <p>LKSecure ra đời với sứ mệnh mang đến giải pháp an ninh thông minh, hiện đại và đáng tin cậy cho mọi gia đình và doanh nghiệp Việt Nam.</p>
        <div class="about-hero-stats">
            <div class="stat-item">
                <span class="stat-number" data-target="5000">0</span><span>+</span>
                <p>Khách hàng tin dùng</p>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="8">0</span><span>+</span>
                <p>Năm kinh nghiệm</p>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="63">0</span><span></span>
                <p>Tỉnh thành phủ sóng</p>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="98">0</span><span>%</span>
                <p>Khách hàng hài lòng</p>
            </div>
        </div>
    </div>
    <div class="about-hero-visual">
        <div class="shield-animation">
            <i class="fas fa-shield-halved"></i>
            <div class="ring ring-1"></div>
            <div class="ring ring-2"></div>
            <div class="ring ring-3"></div>
        </div>
    </div>
</section>

<!-- ===== CÂU CHUYỆN ===== -->
<section class="about-story">
    <div class="about-container">
        <div class="story-text">
            <span class="section-label">Câu Chuyện Của Chúng Tôi</span>
            <h2>Từ Ý Tưởng Đến Niềm Tin</h2>
            <p>Năm 2026, hai người bạn với niềm đam mê công nghệ và mong muốn mang đến những sản phẩm an ninh chất lượng cao đã cùng nhau sáng lập LKSecure.</p>
            <p>Thay vì tự sản xuất, chúng tôi chọn con đường xây dựng mạng lưới liên kết với các nhà sản xuất và phân phối lớn, uy tín hàng đầu trong ngành — đảm bảo mỗi sản phẩm đến tay khách hàng đều đạt chuẩn chất lượng quốc tế với mức giá hợp lý nhất.</p>
            <p>LKSecure đang từng bước mở rộng hệ thống đối tác, hướng tới trở thành cầu nối tin cậy giữa những thương hiệu an ninh hàng đầu thế giới và người tiêu dùng Việt Nam.</p>
            <a href="products.php" class="about-btn-primary">Xem Sản Phẩm <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="story-image">
            <div class="story-img-wrapper">
                <img src="../assets/imgs/home1.png" alt="LKSecure Team">
                <div class="story-badge-float">
                    <i class="fas fa-award"></i>
                    <span>Top 10 thương hiệu<br>an ninh Việt Nam</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== GIÁ TRỊ CỐT LÕI ===== -->
<section class="about-values">
    <div class="about-container">
        <div class="section-header">
            <span class="section-label">Giá Trị Cốt Lõi</span>
            <h2>Những Gì Định Hình Chúng Tôi</h2>
            <p>Mỗi quyết định và sản phẩm đều được xây dựng trên nền tảng những giá trị vững chắc.</p>
        </div>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon" style="background: linear-gradient(135deg, #6d28d9, #a855f7)">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3>Chất Lượng Được Kiểm Chứng</h3>
                <p>Chúng tôi chỉ hợp tác với những nhà sản xuất đã được kiểm định uy tín, đảm bảo mọi sản phẩm đạt chuẩn chất lượng quốc tế trước khi đến tay khách hàng.</p>
            </div>
            <div class="value-card">
                <div class="value-icon" style="background: linear-gradient(135deg, #0ea5e9, #22d3ee)">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h3>Mạng Lưới Đối Tác Mạnh</h3>
                <p>LKSecure liên kết với các nhà phân phối và thương hiệu an ninh hàng đầu, mang đến đa dạng lựa chọn sản phẩm với mức giá cạnh tranh nhất thị trường.</p>
            </div>
            <div class="value-card">
                <div class="value-icon" style="background: linear-gradient(135deg, #10b981, #34d399)">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Tin Cậy & Minh Bạch</h3>
                <p>Chính sách giá rõ ràng, bảo hành đúng cam kết, không có chi phí ẩn — chúng tôi xây dựng niềm tin bằng sự minh bạch trong từng giao dịch.</p>
            </div>
            <div class="value-card">
                <div class="value-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24)">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Khách Hàng Là Trọng Tâm</h3>
                <p>Từng tư vấn và lựa chọn sản phẩm đều xuất phát từ nhu cầu thực tế của khách hàng — chúng tôi không bán thứ bạn không cần, chỉ mang đến thứ bạn thực sự cần.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== ĐỘI NGŨ ===== -->
<section class="about-team">
    <div class="about-container">
        <div class="section-header">
            <span class="section-label">Đội Ngũ Lãnh Đạo</span>
            <h2>Những Người Đứng Sau LKSecure</h2>
            <p>Đội ngũ sáng lập với hơn 20 năm kinh nghiệm trong lĩnh vực công nghệ và an ninh.</p>
        </div>
        <div class="team-grid">
            <div class="team-card">
                <div class="team-avatar-wrap">
                    <!-- ĐẶT ẢNH: thay đường dẫn bên dưới, ví dụ: ../assets/imgs/linh.jpg -->
                    <img src="...\assets\imgs\linh.png" alt="" class="team-photo">
                    <div class="team-avatar-fallback" id="fallback-linh">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
                <h4>Ngô Vương Linh</h4>
                <span>CEO & Co-founder</span>
                <p>Đồng sáng lập LKSecure với tầm nhìn xây dựng hệ sinh thái an ninh thông minh toàn diện cho người Việt.</p>
                <div class="team-socials">
                    <a href="https://www.facebook.com/share/1EApU6ENs1/"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/linhvuong294?igsh=OHZnMTVuOXQ3c2h2"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="team-card">
                <div class="team-avatar-wrap">
                    <!-- ĐẶT ẢNH: thay đường dẫn bên dưới, ví dụ: ../assets/imgs/khai.jpg -->
                    <img src="../assets/imgs/khai.jpg" alt="" class="team-photo">
                    <div class="team-avatar-fallback" id="fallback-khai" style="background: linear-gradient(135deg, #0ea5e9, #22d3ee)">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
                <h4>Nguyễn Văn Khải</h4>
                <span>CTO & Co-founder</span>
                <p>Chuyên gia kỹ thuật, phụ trách nghiên cứu và phát triển sản phẩm công nghệ cao tại LKSecure.</p>
                <div class="team-socials">
                    <a href="https://www.facebook.com/share/18rPpC74xu/"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/nguyenvkhair?igsh=ZWVneTZtNXJ4ZWhh"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== DÒNG THỜI GIAN ===== -->
<section class="about-timeline">
    <div class="about-container">
        <div class="section-header">
            <span class="section-label">Hành Trình Phát Triển</span>
            <h2>Những Cột Mốc Đáng Nhớ</h2>
        </div>
        <div class="timeline">
            <div class="timeline-item left">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <span class="timeline-year">2026</span>
                    <h4>Thành Lập</h4>
                    <p>LKSecure ra đời với định hướng trở thành nền tảng phân phối thiết bị an ninh chất lượng cao tại Việt Nam.</p>
                </div>
            </div>
            <div class="timeline-item right">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <span class="timeline-year">2027</span>
                    <h4>Ký Kết Đối Tác Đầu Tiên</h4>
                    <p>Hợp tác chính thức với các nhà phân phối lớn, đưa những thương hiệu camera và khóa thông minh hàng đầu về Việt Nam.</p>
                </div>
            </div>
            <div class="timeline-item left">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <span class="timeline-year">2028</span>
                    <h4>Ra Mắt LKSecure App</h4>
                    <p>Ứng dụng hỗ trợ khách hàng tra cứu sản phẩm, đặt hàng và theo dõi bảo hành trực tuyến.</p>
                </div>
            </div>
            <div class="timeline-item right">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <span class="timeline-year">2029</span>
                    <h4>Mở Rộng Danh Mục</h4>
                    <p>Bổ sung thêm các dòng sản phẩm AI camera, cảm biến thông minh từ các thương hiệu quốc tế uy tín.</p>
                </div>
            </div>
            <div class="timeline-item left">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <span class="timeline-year">2030</span>
                    <h4>Phủ Sóng Toàn Quốc</h4>
                    <p>Mục tiêu xây dựng mạng lưới đại lý và đối tác phân phối trải dài 63 tỉnh thành trên cả nước.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA ===== -->
<section class="about-cta">
    <div class="about-container">
        <div class="cta-box">
            <i class="fas fa-shield-halved cta-icon"></i>
            <h2>Sẵn Sàng Bảo Vệ Ngôi Nhà Của Bạn?</h2>
            <p>Liên hệ với chúng tôi ngay hôm nay để được tư vấn miễn phí và nhận ưu đãi lắp đặt trọn gói.</p>
            <div class="cta-btns">
                <a href="products.php" class="about-btn-primary">Xem Sản Phẩm</a>
                <a href="contact.php" class="about-btn-outline">Liên Hệ Ngay</a>
            </div>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="main-footer">
    <div class="footer-grid">
        <div class="footer-col">
            <h4>LKSecure</h4>
            <p>Giải pháp an ninh thông minh cho mọi gia đình Việt.</p>
        </div>
        <div class="footer-col">
            <h4>Liên kết</h4>
            <ul>
                <li><a href="home.php">Trang chủ</a></li>
                <li><a href="products.php">Sản phẩm</a></li>
                <li><a href="about.php">Giới thiệu</a></li>
                <li><a href="contact.php">Liên hệ</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Hỗ trợ</h4>
            <ul>
                    <li><a href="warranty.php">Bảo hành</a></li>
                    <li><a href="policy.php">Chính sách</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Liên hệ</h4>
            <p>0393 860 031</p>
            <p>ngo91168@gmail.com</p>
        </div>
    </div>
    <div class="footer-bottom">
        © 2026 LKSecure
    </div>
</footer>


</html>
<script src="../assets/js/main.js"></script>
<script>
    var USER_LOGGED_IN = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    var LOGIN_URL = 'login.php';
</script>
</body>
</html>
