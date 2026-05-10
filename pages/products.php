<?php
session_start();
require_once __DIR__ . '/../config/database.php';
// Truyền trạng thái đăng nhập ra JS
$is_logged_in = isset($_SESSION['user_id']) ? 'true' : 'false';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản Phẩm - LKSecure</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="page-products">

<nav class="navbar">
    <div class="nav-logo">
        <i class="fas fa-shield-halved"></i>
        <span>LKSecure</span>
    </div>
    <div class="nav-links">
        <a href="home.php">Trang chủ</a>
        <a href="products.php" class="active">Sản phẩm</a>
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

<div class="category-strip">
    <div class="container">
        <div class="cat-grid">
            <div class="cat-item" onclick="selectCategory(this)">Tất cả</div>
            <div class="cat-item" onclick="selectCategory(this)">Camera Giám Sát</div>
            <div class="cat-item" onclick="selectCategory(this)">Khóa Cửa Vân Tay</div>
            <div class="cat-item" onclick="selectCategory(this)">Cảm Biến Báo Động</div>
            <div class="cat-item" onclick="selectCategory(this)">Sản Phẩm Khác</div>
        </div>
    </div>
</div>

<main class="product-page-content">
    <div class="container main-layout">
        
        <aside class="filter-sidebar">
            <div class="filter-card">
                <div class="filter-section">
                    <h4>KHOẢNG GIÁ (₫)</h4>
                    <div class="price-inputs">
                        <input type="text" class="money-input" value="" placeholder="Từ (₫)" inputmode="numeric">
                        <input type="text" class="money-input" placeholder="Đến (₫)" inputmode="numeric">
                        <button class="btn-purple-wide">Áp dụng</button>
                    </div>
                </div>

                <div class="filter-section">
                    <h4>ƯU TIÊN HIỂN THỊ</h4>
                    <ul class="selectable-list">
                        <li onclick="selectOption(this)"><i class="far fa-circle"></i> Bán chạy nhất</li>
                        <li onclick="selectOption(this)"><i class="far fa-circle"></i> Đánh giá cao</li>
                        <li onclick="selectOption(this)"><i class="far fa-circle"></i> Giá thấp đến cao</li>
                        <li onclick="selectOption(this)"><i class="far fa-circle"></i> Giá cao đến thấp</li>
                    </ul>
                </div>
            </div>
        </aside>

        <section class="products-display">
            <div class="product-grid-main">
                <?php
                if (isset($conn)) {
                    $sql = "SELECT * FROM products ORDER BY id DESC";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<div class="product-item-box">
                                <img src="../assets/imgs/'.$row['image'].'">
                                <h5>'.$row['name'].'</h5>
                                <span class="price-tag">'.number_format($row['price']).'đ</span>
                                <button class="add-cart-btn" data-product-id="'.$row['id'].'" data-product-name="'.htmlspecialchars($row['name']).'">Thêm vào giỏ</button>
                            </div>';
                        }
                    } else {
                        echo '<p class="empty-msg">Chưa có sản phẩm nào được cập nhật.</p>';
                    }
                }
                ?>
            </div>
        </section>
    </div>
</main>

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


<script>
    // Biến trạng thái đăng nhập — được inject từ PHP
    var USER_LOGGED_IN = <?= $is_logged_in ?>;
    var LOGIN_URL = 'login.php';
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>