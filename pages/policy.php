<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính Sách - LKSecure</title>
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
        <button class="icon-btn cart-icon-btn" title="Giỏ hàng"><i class="fas fa-shopping-cart"></i><span class="cart-badge" style="display:none;"></span></button>
        <?php include __DIR__ . '/../config/nav_partial.php'; ?>
    </div>
</nav>

<!-- ===== HERO ===== -->
<section class="wp-hero policy-hero">
    <div class="container">
        <div class="wp-hero-inner">
            <span class="wp-badge"><i class="fas fa-file-shield"></i> Chính sách & Điều khoản</span>
            <h1>Minh Bạch &amp; <span>Tin Cậy</span></h1>
            <p>LKSecure cam kết bảo vệ quyền lợi khách hàng qua các chính sách rõ ràng, công bằng và nhất quán.</p>
            <p class="wp-updated"><i class="fas fa-clock"></i> Cập nhật lần cuối: 01/01/2026</p>
        </div>
    </div>
</section>

<!-- ===== TAB NAVIGATION ===== -->
<div class="policy-tabs-wrap">
    <div class="container">
        <div class="policy-tabs">
            <button class="tab-btn active" data-tab="tab-privacy"><i class="fas fa-lock"></i> Bảo mật thông tin</button>
            <button class="tab-btn" data-tab="tab-return"><i class="fas fa-rotate-left"></i> Đổi trả hàng</button>
            <button class="tab-btn" data-tab="tab-payment"><i class="fas fa-credit-card"></i> Thanh toán</button>
            <button class="tab-btn" data-tab="tab-shipping"><i class="fas fa-truck"></i> Vận chuyển</button>
            <button class="tab-btn" data-tab="tab-terms"><i class="fas fa-gavel"></i> Điều khoản sử dụng</button>
        </div>
    </div>
</div>

<!-- ===== TAB PANELS ===== -->
<div class="policy-content">
    <div class="container">

        <!-- TAB: BẢO MẬT THÔNG TIN -->
        <div class="tab-panel active" id="tab-privacy">
            <div class="policy-doc">
                <div class="policy-intro">
                    <i class="fas fa-user-shield policy-intro-icon"></i>
                    <div>
                        <h2>Chính Sách Bảo Mật Thông Tin</h2>
                        <p>LKSecure tôn trọng quyền riêng tư của khách hàng. Chính sách này mô tả cách chúng tôi thu thập, sử dụng và bảo vệ thông tin cá nhân của bạn.</p>
                    </div>
                </div>

                <div class="accordion-group">
                    <div class="accordion-item open">
                        <div class="accordion-header">
                            <span><i class="fas fa-database"></i> 1. Thông tin chúng tôi thu thập</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <p>Chúng tôi thu thập các thông tin sau khi bạn sử dụng dịch vụ:</p>
                            <ul class="policy-list">
                                <li><strong>Thông tin định danh:</strong> Họ tên, số điện thoại, địa chỉ email, địa chỉ giao hàng.</li>
                                <li><strong>Thông tin giao dịch:</strong> Lịch sử đặt hàng, phương thức thanh toán (không lưu thông tin thẻ đầy đủ).</li>
                                <li><strong>Thông tin kỹ thuật:</strong> Địa chỉ IP, loại trình duyệt, thời gian truy cập — chỉ dùng để cải thiện dịch vụ.</li>
                                <li><strong>Thông tin bảo hành:</strong> Mã sản phẩm, ngày mua, lịch sử bảo trì.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <span><i class="fas fa-cogs"></i> 2. Mục đích sử dụng thông tin</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <ul class="policy-list">
                                <li>Xử lý đơn hàng và cung cấp dịch vụ hỗ trợ sau bán hàng.</li>
                                <li>Gửi thông tin đơn hàng, xác nhận thanh toán và cập nhật vận chuyển.</li>
                                <li>Thực hiện bảo hành và hỗ trợ kỹ thuật khi cần thiết.</li>
                                <li>Gửi thông tin khuyến mãi (chỉ khi bạn đã đồng ý nhận).</li>
                                <li>Cải thiện chất lượng sản phẩm và dịch vụ dựa trên phản hồi.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <span><i class="fas fa-share-nodes"></i> 3. Chia sẻ thông tin với bên thứ ba</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <p>LKSecure <strong>không bán, không cho thuê</strong> thông tin cá nhân của bạn cho bất kỳ bên nào. Chúng tôi chỉ chia sẻ thông tin trong các trường hợp:</p>
                            <ul class="policy-list">
                                <li>Đối tác vận chuyển (chỉ tên, số điện thoại, địa chỉ giao hàng).</li>
                                <li>Đơn vị xử lý thanh toán được chứng nhận PCI-DSS.</li>
                                <li>Yêu cầu của cơ quan nhà nước có thẩm quyền theo quy định pháp luật.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <span><i class="fas fa-user-check"></i> 4. Quyền của khách hàng</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <p>Bạn có quyền:</p>
                            <ul class="policy-list">
                                <li><strong>Truy cập:</strong> Yêu cầu xem toàn bộ thông tin chúng tôi đang lưu trữ về bạn.</li>
                                <li><strong>Chỉnh sửa:</strong> Cập nhật thông tin cá nhân bất cứ lúc nào qua tài khoản.</li>
                                <li><strong>Xóa:</strong> Yêu cầu xóa tài khoản và toàn bộ dữ liệu cá nhân.</li>
                                <li><strong>Từ chối nhận marketing:</strong> Hủy đăng ký email khuyến mãi mọi lúc.</li>
                            </ul>
                            <p>Liên hệ: <strong>info@lksecure.vn</strong> để thực hiện các quyền trên.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: ĐỔI TRẢ HÀNG -->
        <div class="tab-panel" id="tab-return">
            <div class="policy-doc">
                <div class="policy-intro">
                    <i class="fas fa-rotate-left policy-intro-icon"></i>
                    <div>
                        <h2>Chính Sách Đổi Trả Hàng</h2>
                        <p>LKSecure áp dụng chính sách đổi trả linh hoạt, đảm bảo quyền lợi tối đa cho khách hàng.</p>
                    </div>
                </div>

                <div class="return-highlight-grid">
                    <div class="rh-card green">
                        <i class="fas fa-7"></i>
                        <h4>7 ngày đổi hàng</h4>
                        <p>Lỗi kỹ thuật nhà sản xuất kể từ ngày nhận hàng</p>
                    </div>
                    <div class="rh-card blue">
                        <i class="fas fa-30"></i>
                        <h4>30 ngày hoàn tiền</h4>
                        <p>Sản phẩm lỗi không thể sửa chữa được</p>
                    </div>
                    <div class="rh-card purple">
                        <i class="fas fa-infinity"></i>
                        <h4>Miễn phí vận chuyển</h4>
                        <p>Chi phí gửi về khi thuộc diện đổi trả hợp lệ</p>
                    </div>
                </div>

                <div class="accordion-group">
                    <div class="accordion-item open">
                        <div class="accordion-header">
                            <span><i class="fas fa-circle-check"></i> Điều kiện được đổi trả</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <ul class="policy-list">
                                <li>Sản phẩm còn trong thời hạn đổi trả (7 ngày với đổi hàng, 30 ngày với hoàn tiền).</li>
                                <li>Sản phẩm còn nguyên hộp, đầy đủ phụ kiện, hóa đơn và tem bảo hành.</li>
                                <li>Lỗi do nhà sản xuất, không phải do người dùng tác động.</li>
                                <li>Sản phẩm chưa qua sửa chữa bên ngoài LKSecure.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <span><i class="fas fa-circle-xmark"></i> Trường hợp không được đổi trả</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <ul class="policy-list">
                                <li>Đã qua thời hạn đổi trả quy định.</li>
                                <li>Sản phẩm bị hư hỏng do người dùng: va đập, nước, điện áp không đúng.</li>
                                <li>Thiếu phụ kiện, hộp hoặc hóa đơn mua hàng.</li>
                                <li>Lý do đổi trả là "không thích" hoặc "mua nhầm" không phải lỗi kỹ thuật.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <span><i class="fas fa-list-ol"></i> Quy trình đổi trả</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <ol class="policy-list ordered">
                                <li>Liên hệ hotline <strong>0393 860 031</strong> hoặc email <strong>ngo91168@gmail.com</strong> để mở yêu cầu đổi trả.</li>
                                <li>Cung cấp hình ảnh/video mô tả lỗi sản phẩm.</li>
                                <li>Gửi sản phẩm theo hướng dẫn của nhân viên hỗ trợ.</li>
                                <li>LKSecure kiểm tra và xác nhận trong 2–3 ngày làm việc.</li>
                                <li>Đổi hàng mới hoặc hoàn tiền trong 5–7 ngày làm việc sau xác nhận.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: THANH TOÁN -->
        <div class="tab-panel" id="tab-payment">
            <div class="policy-doc">
                <div class="policy-intro">
                    <i class="fas fa-credit-card policy-intro-icon"></i>
                    <div>
                        <h2>Chính Sách Thanh Toán</h2>
                        <p>LKSecure hỗ trợ nhiều phương thức thanh toán tiện lợi, an toàn và bảo mật.</p>
                    </div>
                </div>
                <div class="payment-methods">
                    <div class="pm-card"><i class="fas fa-money-bill-wave"></i><span>Tiền mặt khi nhận hàng (COD)</span></div>
                    <div class="pm-card"><i class="fas fa-building-columns"></i><span>Chuyển khoản ngân hàng</span></div>
                    <div class="pm-card"><i class="fab fa-cc-visa"></i><span>Thẻ Visa / Mastercard</span></div>
                    <div class="pm-card"><i class="fas fa-mobile-screen-button"></i><span>Ví MoMo / ZaloPay / VNPay</span></div>
                </div>
                <div class="accordion-group">
                    <div class="accordion-item open">
                        <div class="accordion-header">
                            <span><i class="fas fa-shield-halved"></i> Bảo mật thanh toán</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <p>Mọi giao dịch thanh toán trực tuyến đều được mã hóa theo chuẩn <strong>SSL/TLS 256-bit</strong>. LKSecure không lưu trữ thông tin thẻ tín dụng — dữ liệu được xử lý trực tiếp bởi cổng thanh toán được chứng nhận <strong>PCI-DSS</strong>.</p>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <span><i class="fas fa-rotate-left"></i> Chính sách hoàn tiền</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <ul class="policy-list">
                                <li>Hoàn tiền trong vòng <strong>5–7 ngày làm việc</strong> sau khi xác nhận yêu cầu hợp lệ.</li>
                                <li>Hoàn về đúng phương thức thanh toán ban đầu.</li>
                                <li>Phí giao dịch (nếu có) do cổng thanh toán giữ lại sẽ không được hoàn.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: VẬN CHUYỂN -->
        <div class="tab-panel" id="tab-shipping">
            <div class="policy-doc">
                <div class="policy-intro">
                    <i class="fas fa-truck policy-intro-icon"></i>
                    <div>
                        <h2>Chính Sách Vận Chuyển</h2>
                        <p>LKSecure giao hàng toàn quốc qua các đối tác vận chuyển uy tín, nhanh chóng và an toàn.</p>
                    </div>
                </div>
                <div class="shipping-table-wrap">
                    <table class="shipping-table">
                        <thead>
                            <tr><th>Khu vực</th><th>Thời gian</th><th>Phí ship</th><th>Miễn phí khi</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Nội thành TP.HCM / Hà Nội</td><td>1–2 ngày</td><td>25.000đ</td><td>Đơn từ 500.000đ</td></tr>
                            <tr><td>Tỉnh thành lân cận</td><td>2–3 ngày</td><td>35.000đ</td><td>Đơn từ 1.000.000đ</td></tr>
                            <tr><td>Các tỉnh thành khác</td><td>3–5 ngày</td><td>45.000đ</td><td>Đơn từ 1.500.000đ</td></tr>
                            <tr><td>Vùng sâu / hải đảo</td><td>5–7 ngày</td><td>Theo thực tế</td><td>—</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="accordion-group" style="margin-top:32px">
                    <div class="accordion-item open">
                        <div class="accordion-header">
                            <span><i class="fas fa-box"></i> Đóng gói và kiểm tra hàng</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <p>Tất cả sản phẩm được đóng gói cẩn thận với lớp foam chống sốc trước khi giao. Khách hàng có quyền <strong>kiểm tra hàng trước khi ký nhận</strong>. Nếu phát hiện hộp bị móp méo, rách, đề nghị từ chối nhận và liên hệ LKSecure ngay.</p>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <span><i class="fas fa-map-location-dot"></i> Theo dõi đơn hàng</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <p>Sau khi đơn được xác nhận và giao cho đơn vị vận chuyển, bạn sẽ nhận được <strong>mã vận đơn qua SMS/email</strong> để theo dõi hành trình đơn hàng trực tiếp trên website của đối tác vận chuyển.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: ĐIỀU KHOẢN SỬ DỤNG -->
        <div class="tab-panel" id="tab-terms">
            <div class="policy-doc">
                <div class="policy-intro">
                    <i class="fas fa-gavel policy-intro-icon"></i>
                    <div>
                        <h2>Điều Khoản Sử Dụng</h2>
                        <p>Bằng việc sử dụng website và dịch vụ LKSecure, bạn đồng ý tuân thủ các điều khoản dưới đây.</p>
                    </div>
                </div>
                <div class="accordion-group">
                    <div class="accordion-item open">
                        <div class="accordion-header">
                            <span><i class="fas fa-globe"></i> 1. Sử dụng website</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <p>Website LKSecure chỉ được sử dụng cho mục đích hợp pháp. Nghiêm cấm mọi hành vi: tấn công, hack, thu thập dữ liệu trái phép, đăng nội dung vi phạm pháp luật hoặc quyền sở hữu trí tuệ.</p>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <span><i class="fas fa-copyright"></i> 2. Quyền sở hữu trí tuệ</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <p>Toàn bộ nội dung trên website (logo, hình ảnh, mô tả sản phẩm, giao diện) là tài sản của LKSecure. Nghiêm cấm sao chép, phân phối hoặc sử dụng thương mại khi chưa được phép bằng văn bản.</p>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <span><i class="fas fa-triangle-exclamation"></i> 3. Giới hạn trách nhiệm</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <p>LKSecure không chịu trách nhiệm về các thiệt hại gián tiếp, hậu quả phát sinh từ việc sử dụng sản phẩm không đúng hướng dẫn, hoặc các sự cố do yếu tố bên ngoài như thiên tai, mất điện, lỗi hạ tầng viễn thông.</p>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <span><i class="fas fa-pen-to-square"></i> 4. Thay đổi điều khoản</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <p>LKSecure có quyền cập nhật điều khoản này bất cứ lúc nào. Mọi thay đổi sẽ được thông báo trên website và có hiệu lực sau <strong>7 ngày</strong> kể từ ngày đăng. Việc tiếp tục sử dụng dịch vụ đồng nghĩa với việc chấp nhận điều khoản mới.</p>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <span><i class="fas fa-scale-balanced"></i> 5. Luật áp dụng</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="accordion-body">
                            <p>Các điều khoản này được điều chỉnh bởi <strong>pháp luật Việt Nam</strong>. Mọi tranh chấp phát sinh sẽ được giải quyết tại Tòa án nhân dân có thẩm quyền tại TP. Hồ Chí Minh.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /container -->
</div><!-- /policy-content -->

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
                    <li><a href="warranty.php">Bảo hành</a></li>
                    <li><a href="policy.php" class="active">Chính sách</a></li>
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
<script>
    var USER_LOGGED_IN = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    var LOGIN_URL = 'login.php';
</script>
</body>
</html>
