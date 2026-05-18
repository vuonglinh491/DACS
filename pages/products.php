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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
</head>
<body class="page-products">

<!-- NAVBAR -->
<header class="navbar">
    <a href="home.php" class="nav-logo">
        <i class="fa-solid fa-shield-halved"></i> LK Secure
    </a>
    <nav class="nav-links">
        <a href="home.php">TRANG CHỦ</a>
        <a href="products.php" class="active">SẢN PHẨM</a>
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
        <button class="icon-btn cart-icon-btn" title="Giỏ hàng">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-badge" style="display:none;"></span>
        </button>
        <?php include __DIR__ . '/../config/nav_partial.php'; ?>
    </div>
</header>

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
            <div class="product-grid-main" id="productGrid">
                <?php
                if (isset($conn)) {
                    $sql = "SELECT p.id, p.name, p.price, p.original_price, p.image, p.stock, p.status,
                                   p.sold_count, p.is_featured, c.name AS category_name
                            FROM products p
                            LEFT JOIN categories c ON p.category_id = c.id
                            WHERE p.status != 'discontinued'
                            ORDER BY p.is_featured DESC, p.sold_count DESC, p.id DESC";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $disc = ($row['original_price'] > $row['price'] && $row['original_price'] > 0)
                                ? round((1 - $row['price'] / $row['original_price']) * 100) : 0;
                            $out_of_stock = ($row['status'] === 'out_of_stock' || $row['stock'] == 0);
                            echo '<div class="product-item-box" data-category="'.htmlspecialchars($row['category_name'] ?? '').'" data-price="'.$row['price'].'" data-sold="'.$row['sold_count'].'">';
                            if ($row['is_featured']) echo '<div class="product-badge-featured">Nổi bật</div>';
                            if ($disc > 0) echo '<div class="product-badge-sale">-'.$disc.'%</div>';
                            echo '<a href="product_detail.php?id='.$row['id'].'" class="product-img-link">';
                            if ($row['image']) {
                                echo '<img src="../assets/imgs/'.htmlspecialchars($row['image']).'" alt="'.htmlspecialchars($row['name']).'" loading="lazy" onerror="this.style.opacity=0">';
                            } else {
                                echo '<div class="no-product-img"><i class="fas fa-camera"></i></div>';
                            }
                            echo '</a><div class="product-item-info">';
                            if ($row['category_name']) echo '<div class="product-item-cat">'.htmlspecialchars($row['category_name']).'</div>';
                            echo '<h5><a href="product_detail.php?id='.$row['id'].'" style="color:inherit;text-decoration:none;">'.htmlspecialchars($row['name']).'</a></h5>';
                            echo '<div class="product-price-row"><span class="price-tag">'.number_format($row['price']).'đ</span>';
                            if ($disc > 0) echo '<span class="price-orig">'.number_format($row['original_price']).'đ</span>';
                            echo '</div>';
                            if ($row['sold_count'] > 0) echo '<div class="sold-count"><i class="fas fa-shopping-bag"></i> '.number_format($row['sold_count']).' đã bán</div>';
                            if ($out_of_stock) {
                                echo '<button class="add-cart-btn" disabled style="background:#9ca3af;cursor:not-allowed;">Hết hàng</button>';
                            } else {
                                echo '<button class="add-cart-btn" data-product-id="'.$row['id'].'" data-product-name="'.htmlspecialchars($row['name']).'">Thêm vào giỏ</button>';
                            }
                            echo '</div></div>';
                        }
                    } else {
                        echo '<div style="grid-column:1/-1;text-align:center;padding:80px 20px;color:#6b7280;"><i class="fas fa-box-open" style="font-size:48px;color:#e5e7eb;display:block;margin-bottom:16px;"></i><p>Chưa có sản phẩm nào.</p></div>';
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