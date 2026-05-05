<?php 
include '../config/database.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản Phẩm - LKSecure</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style_home.css">
    <link rel="stylesheet" href="../assets/css/style_products.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-logo">
        <i class="fas fa-shield-halved"></i> <span>LKSecure</span>
    </div>
    <div class="nav-links">
        <a href="home.php">Trang chủ</a>
        <a href="products.php" class="active">Sản phẩm</a>
        <a href="#">Giới thiệu</a>
        <a href="#">Liên hệ</a>
    </div>
    <div class="nav-actions">
        <div class="search-box-dynamic">
            <input type="text" id="navSearchInput" placeholder="Tìm sản phẩm...">
            <button class="icon-btn" id="searchToggle"><i class="fas fa-search"></i></button>
        </div>
        <button class="icon-btn"><i class="fas fa-shopping-cart"></i></button>
        <a href="login.php" class="btn-login-nav">Đăng nhập</a>
    </div>
</nav>

<div class="category-strip-bg">
    <div class="container cat-flex-wrapper">
        <div class="cat-card-small active" onclick="selectTopCategory(this)">
            <i class="fas fa-th-large"></i> <span>Tất cả sản phẩm</span>
        </div>
        <div class="cat-card-small" onclick="selectTopCategory(this)">
            <i class="fas fa-camera"></i> <span>Camera</span>
        </div>
        <div class="cat-card-small" onclick="selectTopCategory(this)">
            <i class="fas fa-fingerprint"></i> <span>Khóa cửa</span>
        </div>
        <div class="cat-card-small" onclick="selectTopCategory(this)">
            <i class="fas fa-bell"></i> <span>Báo động</span>
        </div>
        <div class="cat-card-small" onclick="selectTopCategory(this)">
            <i class="fas fa-microchip"></i> <span>Phụ kiện</span>
        </div>
    </div>
</div>

<main class="container product-page-content">
    <div class="main-layout">
        <aside class="filter-sidebar">
            <div class="filter-card">
                <h4 class="filter-title">Bộ lọc sản phẩm</h4>
                <div class="filter-section">
                    <p class="filter-label">Khoảng giá (₫)</p>
                    <div class="price-inputs">
                        <input type="text" class="money-input" placeholder="Từ (₫)">
                        <input type="text" class="money-input" placeholder="Đến (₫)">
                    </div>
                </div>
                <div class="filter-section" style="margin-top: 25px;">
                    <p class="filter-label">Ưu tiên hiển thị</p>
                    <ul class="priority-list">
                        <li onclick="selectPriority(this)"><i class="far fa-circle"></i> Bán chạy nhất</li>
                        <li onclick="selectPriority(this)"><i class="far fa-circle"></i> Mới nhất</li>
                        <li onclick="selectPriority(this)"><i class="far fa-circle"></i> Giá thấp đến cao</li>
                        <li onclick="selectPriority(this)"><i class="far fa-circle"></i> Giá cao đến thấp</li>
                    </ul>
                </div>
                <button class="btn-purple-wide" style="margin-top: 25px;">Áp dụng lọc</button>
            </div>
        </aside>

        <section class="products-display">
            <div class="product-grid-main">
                <?php
                $sql = "SELECT * FROM products ORDER BY id DESC";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="product-item-box">
                            <img src="../assets/imgs/'.$row['image'].'">
                            <h5>'.$row['name'].'</h5>
                            <span class="price-tag">'.number_format($row['price']).'đ</span>
                            <button class="add-cart-btn">Thêm vào giỏ</button>
                        </div>';
                    }
                }
                ?>
            </div>
        </section>
    </div>
</main>

<footer class="main-footer-dark">
    <div class="container footer-content">
        <div class="footer-info">
            <div style="color: white; font-weight: bold; font-size: 20px; margin-bottom: 15px;">
                <i class="fas fa-shield-halved"></i> LKSecure
            </div>
            <p>Giải pháp an ninh thông minh hàng đầu cho ngôi nhà của bạn.</p>
        </div>
        <div class="footer-links">
            <h5>Liên kết</h5>
            <a href="home.php">Trang chủ</a>
            <a href="products.php">Sản phẩm</a>
            <a href="#">Giới thiệu</a>
        </div>
        <div class="footer-support">
            <h5>Hỗ trợ</h5>
            <p>Chính sách bảo hành</p>
            <p>Chính sách đổi trả</p>
            <p>Bảo mật thông tin</p>
        </div>
        <div class="footer-contact">
            <h5>Liên hệ</h5>
            <p><i class="fas fa-phone"></i> 0123 456 789</p>
            <p><i class="fas fa-envelope"></i> support@lksecure.vn</p>
            <p><i class="fas fa-location-dot"></i> Hà Nội, Việt Nam</p>
        </div>
    </div>
    <div class="footer-line" style="border-top: 1px solid rgba(255,255,255,0.1); margin: 30px 0;"></div>
    <p class="copyright" style="text-align: center; font-size: 13px; color: #94a3b8;">© 2026 LKSecure. All rights reserved.</p>
</footer>

<script src="../assets/js/script.js"></script>
</body>
</html>