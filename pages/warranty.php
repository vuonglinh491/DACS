<?php
$connect_path = $_SERVER['DOCUMENT_ROOT'] . '/DACS/config/connect.php';
if (file_exists($connect_path)) include $connect_path;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảo Hành - LKSecure</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
</head>
<body class="page-light">

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
    <div class="nav-logo">
        <i class="fas fa-shield-halved"></i>
        <span>LKSecure</span>
    </div>
    <div class="nav-links">
        <a href="home.php">Trang chủ</a>
        <a href="products.php">Sản phẩm</a>
        <a href="about.php">Giới thiệu</a>
        <a href="contact.php">Liên hệ</a>
    </div>
    <div class="nav-actions">
        <div class="search-box-dynamic">
            <button class="icon-btn" id="searchToggle"><i class="fas fa-search"></i></button>
            <input type="text" id="navSearchInput" placeholder="Tìm sản phẩm...">
        </div>
        <button class="icon-btn"><i class="fas fa-shopping-cart"></i></button>
        <a href="login.php" class="btn-login-nav">Đăng nhập</a>
    </div>
</nav>

<!-- ===== HERO ===== -->
<section class="wp-hero">
    <div class="container">
        <div class="wp-hero-inner">
            <span class="wp-badge"><i class="fas fa-shield-check"></i> Chính sách bảo hành</span>
            <h1>Bảo Hành <span>Toàn Diện</span></h1>
            <p>LKSecure cam kết bảo hành chính hãng cho tất cả sản phẩm — mang đến sự an tâm tuyệt đối sau mỗi lần mua.</p>
            <div class="wp-hero-stats">
                <div class="wp-stat"><i class="fas fa-calendar-check"></i><strong>24 tháng</strong><span>Bảo hành tối đa</span></div>
                <div class="wp-stat"><i class="fas fa-tools"></i><strong>Miễn phí</strong><span>Sửa chữa chính hãng</span></div>
                <div class="wp-stat"><i class="fas fa-truck-fast"></i><strong>24h</strong><span>Phản hồi xử lý</span></div>
            </div>
        </div>
    </div>
</section>

<!-- ===== THỜI GIAN BẢO HÀNH ===== -->
<section class="wp-section bg-white">
    <div class="container">
        <div class="section-header">
            <h2>Thời Gian Bảo Hành Theo Dòng Sản Phẩm</h2>
            <p>Mỗi dòng sản phẩm có chính sách bảo hành riêng phù hợp với đặc tính kỹ thuật.</p>
        </div>
        <div class="warranty-cards">
            <div class="warranty-card">
                <div class="wc-icon"><i class="fas fa-camera"></i></div>
                <h4>Camera Giám Sát</h4>
                <div class="wc-duration">24 tháng</div>
                <ul>
                    <li><i class="fas fa-check"></i> Lỗi phần cứng nhà sản xuất</li>
                    <li><i class="fas fa-check"></i> Màn hình, cảm biến, bo mạch</li>
                    <li><i class="fas fa-check"></i> Hỗ trợ cập nhật firmware</li>
                    <li><i class="fas fa-times text-muted"></i> Hư hỏng do va đập, nước</li>
                </ul>
            </div>
            <div class="warranty-card featured">
                <div class="wc-badge-top">Phổ biến nhất</div>
                <div class="wc-icon"><i class="fas fa-fingerprint"></i></div>
                <h4>Khóa Cửa Vân Tay</h4>
                <div class="wc-duration">18 tháng</div>
                <ul>
                    <li><i class="fas fa-check"></i> Lỗi cảm biến vân tay</li>
                    <li><i class="fas fa-check"></i> Bo mạch điện tử</li>
                    <li><i class="fas fa-check"></i> Cơ cấu khóa chính</li>
                    <li><i class="fas fa-times text-muted"></i> Pin, ốc vít, phụ kiện nhỏ</li>
                </ul>
            </div>
            <div class="warranty-card">
                <div class="wc-icon"><i class="fas fa-bell"></i></div>
                <h4>Cảm Biến Báo Động</h4>
                <div class="wc-duration">12 tháng</div>
                <ul>
                    <li><i class="fas fa-check"></i> Lỗi cảm biến chính</li>
                    <li><i class="fas fa-check"></i> Module không dây</li>
                    <li><i class="fas fa-check"></i> Còi báo động</li>
                    <li><i class="fas fa-times text-muted"></i> Ăn mòn do môi trường</li>
                </ul>
            </div>
            <div class="warranty-card">
                <div class="wc-icon"><i class="fas fa-box-open"></i></div>
                <h4>Phụ Kiện & Linh Kiện</h4>
                <div class="wc-duration">6 tháng</div>
                <ul>
                    <li><i class="fas fa-check"></i> Lỗi sản xuất ban đầu</li>
                    <li><i class="fas fa-check"></i> Cáp nguồn, đầu nối</li>
                    <li><i class="fas fa-times text-muted"></i> Hao mòn tự nhiên</li>
                    <li><i class="fas fa-times text-muted"></i> Mất mát, thất lạc</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===== QUY TRÌNH BẢO HÀNH ===== -->
<section class="wp-section bg-light">
    <div class="container">
        <div class="section-header">
            <h2>Quy Trình Bảo Hành</h2>
            <p>Chỉ 4 bước đơn giản — LKSecure xử lý nhanh, minh bạch.</p>
        </div>
        <div class="process-steps">
            <div class="process-step">
                <div class="ps-num">01</div>
                <div class="ps-icon"><i class="fas fa-phone-volume"></i></div>
                <h4>Liên hệ hỗ trợ</h4>
                <p>Gọi hotline <strong>0393 860 031</strong> hoặc gửi yêu cầu qua trang Liên hệ. Đội ngũ kỹ thuật phản hồi trong vòng 24 giờ.</p>
            </div>
            <div class="process-arrow"><i class="fas fa-arrow-right"></i></div>
            <div class="process-step">
                <div class="ps-num">02</div>
                <div class="ps-icon"><i class="fas fa-clipboard-check"></i></div>
                <h4>Kiểm tra & xác nhận</h4>
                <p>Kỹ thuật viên kiểm tra tình trạng sản phẩm, xác nhận lỗi và phạm vi bảo hành còn hiệu lực.</p>
            </div>
            <div class="process-arrow"><i class="fas fa-arrow-right"></i></div>
            <div class="process-step">
                <div class="ps-num">03</div>
                <div class="ps-icon"><i class="fas fa-screwdriver-wrench"></i></div>
                <h4>Sửa chữa / Thay mới</h4>
                <p>Tiến hành sửa chữa hoặc thay thế linh kiện bằng hàng chính hãng. Thời gian xử lý từ 3–7 ngày làm việc.</p>
            </div>
            <div class="process-arrow"><i class="fas fa-arrow-right"></i></div>
            <div class="process-step">
                <div class="ps-num">04</div>
                <div class="ps-icon"><i class="fas fa-box-open"></i></div>
                <h4>Bàn giao & kiểm thử</h4>
                <p>Sản phẩm được kiểm thử đầy đủ trước khi bàn giao. Giao tận nơi hoặc nhận tại trung tâm.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== CÁC TRƯỜNG HỢP BẢO HÀNH / KHÔNG BẢO HÀNH ===== -->
<section class="wp-section bg-white">
    <div class="container">
        <div class="section-header">
            <h2>Điều Kiện Bảo Hành</h2>
            <p>Hiểu rõ các trường hợp được và không được bảo hành để chủ động bảo vệ quyền lợi của bạn.</p>
        </div>
        <div class="condition-grid">
            <div class="condition-box covered">
                <div class="cb-header"><i class="fas fa-circle-check"></i> Được bảo hành</div>
                <ul>
                    <li>Lỗi kỹ thuật do nhà sản xuất trong quá trình sản xuất</li>
                    <li>Sản phẩm không hoạt động đúng theo thông số kỹ thuật công bố</li>
                    <li>Linh kiện hỏng hóc trong điều kiện sử dụng bình thường</li>
                    <li>Lỗi phần mềm nhúng (firmware) do nhà sản xuất phát hành</li>
                    <li>Màn hình, cảm biến lỗi không do tác động vật lý bên ngoài</li>
                    <li>Bo mạch chủ, module kết nối lỗi trong điều kiện bình thường</li>
                </ul>
            </div>
            <div class="condition-box not-covered">
                <div class="cb-header"><i class="fas fa-circle-xmark"></i> Không bảo hành</div>
                <ul>
                    <li>Hư hỏng do va đập, rơi vỡ, cháy nổ, thiên tai</li>
                    <li>Tiếp xúc với nước, hóa chất, môi trường ẩm ướt không đúng chuẩn IP</li>
                    <li>Tự ý tháo rời, sửa chữa không qua trung tâm LKSecure</li>
                    <li>Sử dụng nguồn điện không đúng thông số (quá áp, thiếu áp)</li>
                    <li>Hao mòn tự nhiên: pin, vỏ nhựa, dây cáp, ốc vít</li>
                    <li>Mã bảo hành bị xóa, cạo sửa, hoặc sản phẩm không có hóa đơn</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===== FAQ BẢO HÀNH ===== -->
<section class="wp-section bg-light">
    <div class="container">
        <div class="section-header">
            <h2>Câu Hỏi Thường Gặp</h2>
        </div>
        <div class="accordion-group">
            <div class="accordion-item open">
                <div class="accordion-header">
                    <span>Tôi cần chuẩn bị gì khi yêu cầu bảo hành?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="accordion-body">
                    <p>Bạn cần cung cấp: <strong>hóa đơn mua hàng</strong> (bản in hoặc ảnh chụp), <strong>sản phẩm cần bảo hành</strong> cùng toàn bộ phụ kiện đi kèm, và <strong>mô tả lỗi</strong> chi tiết. Tem bảo hành trên sản phẩm phải còn nguyên vẹn.</p>
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header">
                    <span>Thời gian xử lý bảo hành mất bao lâu?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="accordion-body">
                    <p>Thông thường từ <strong>3 đến 7 ngày làm việc</strong> kể từ khi tiếp nhận sản phẩm. Trường hợp cần nhập linh kiện từ nhà sản xuất, thời gian có thể kéo dài đến <strong>15 ngày làm việc</strong> và chúng tôi sẽ thông báo trước cho bạn.</p>
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header">
                    <span>Sản phẩm có được thay mới không?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="accordion-body">
                    <p>LKSecure sẽ <strong>đổi sản phẩm mới</strong> trong trường hợp sản phẩm lỗi ngay từ khi nhận hàng (trong vòng <strong>7 ngày</strong> kể từ ngày mua) hoặc sản phẩm không thể sửa chữa được sau 3 lần bảo hành cho cùng một lỗi.</p>
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header">
                    <span>Bảo hành có mất phí không?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="accordion-body">
                    <p>Trong thời gian bảo hành còn hiệu lực và lỗi thuộc phạm vi bảo hành, <strong>hoàn toàn miễn phí</strong> cả chi phí linh kiện và nhân công. Trường hợp hết bảo hành hoặc lỗi ngoài phạm vi, chúng tôi sẽ báo giá trước khi tiến hành sửa.</p>
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header">
                    <span>Tôi có thể gửi bảo hành qua bưu điện không?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="accordion-body">
                    <p>Có. Bạn có thể gửi sản phẩm qua các dịch vụ vận chuyển uy tín. LKSecure sẽ <strong>thanh toán phí vận chuyển chiều về</strong> cho các trường hợp thuộc diện bảo hành. Vui lòng liên hệ hotline để được hướng dẫn đóng gói đúng cách trước khi gửi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA ===== -->
<section class="wp-cta">
    <div class="container">
        <div class="cta-box">
            <span class="cta-icon">🛡️</span>
            <h2>Cần Hỗ Trợ Bảo Hành?</h2>
            <p>Đội ngũ kỹ thuật LKSecure luôn sẵn sàng hỗ trợ bạn. Liên hệ ngay để được tư vấn và giải quyết nhanh nhất.</p>
            <div class="cta-btns">
                <a href="contact.php" class="about-btn-primary"><i class="fas fa-headset"></i> Liên hệ ngay</a>
                <a href="products.php" class="about-btn-outline"><i class="fas fa-shopping-bag"></i> Xem sản phẩm</a>
            </div>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="main-footer-dark">
    <div class="container">
        <div class="footer-content">
            <div class="footer-info">
                <div class="nav-logo white-text"><i class="fas fa-shield-halved"></i> LKSecure</div>
                <p>An tâm cho mọi gia đình Việt với giải pháp an ninh toàn diện.</p>
            </div>
            <div class="footer-links">
                <h5>Liên kết</h5>
                <ul>
                    <li><a href="home.php">Trang chủ</a></li>
                    <li><a href="products.php">Sản phẩm</a></li>
                    <li><a href="about.php">Giới thiệu</a></li>
                    <li><a href="contact.php">Liên hệ</a></li>
                </ul>
            </div>
            <div class="footer-support">
                <h5>Hỗ trợ</h5>
                <ul>
                    <li><a href="warranty.php" class="active">Bảo hành</a></li>
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

<script src="../assets/js/main.js"></script>
</body>
</html>
