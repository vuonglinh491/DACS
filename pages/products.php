<?php include $_SERVER['DOCUMENT_ROOT'] . '/DACS/config/connect.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LKSecure - Sản phẩm</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style_home.css">
    <link rel="stylesheet" href="../assets/css/style_products.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-logo">
        <i class="fas fa-shield-halved"></i>
        <span>LKSecure</span>
    </div>
    <div class="nav-links">
        <a href="home.php">Trang chủ</a>
        <a href="products.php" class="active">Sản phẩm</a>
        <a href="#">Giới thiệu</a>
        <a href="#">Liên hệ</a>
    </div>
    <div class="nav-actions">
        <button class="icon-btn"><i class="fas fa-search"></i></button>
        <button class="icon-btn"><i class="fas fa-shopping-cart"></i></button>
        <a href="login.php" class="btn-login-nav">Đăng nhập</a>
    </div>
</nav>

<!-- HERO -->
<section class="product-hero">
    <h1>Sản phẩm an ninh</h1>
    <p>Thiết bị bảo vệ hiện đại cho gia đình bạn</p>
</section>

<!-- FILTER -->
<section class="product-filter">
    <button class="active">Tất cả</button>
    <button>Camera</button>
    <button>Khóa</button>
    <button>Báo động</button>
</section>

<!-- PRODUCT GRID -->
<section class="product-grid">

<?php
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
?>

    <div class="product-card">
        <img src="../assets/imgs/<?php echo $row['image']; ?>" alt="">
        <h3><?php echo $row['name']; ?></h3>
        <p><?php echo $row['description']; ?></p>
        <span><?php echo number_format($row['price']); ?>đ</span>
        <button>Mua ngay</button>
    </div>

<?php
    }
} else {
?>

    <!-- HIỂN THỊ KHI CHƯA CÓ SẢN PHẨM -->
    <div class="no-product">
        <i class="fas fa-box-open"></i>
        <h2>Chưa có sản phẩm nào</h2>
        <p>Admin sẽ sớm cập nhật!</p>
    </div>

<?php } ?>

</section>

<!-- FOOTER (FIX CHUẨN - KHÔNG LẶP) -->
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
                <li><a href="#">Liên hệ</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Hỗ trợ</h4>
            <ul>
                <li><a href="#">Bảo hành</a></li>
                <li><a href="#">Chính sách</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Liên hệ</h4>
            <p>0123 456 789</p>
            <p>info@lksecure.vn</p>
        </div>

    </div>

    <div class="footer-bottom">
        © 2026 LKSecure
    </div>
</footer>

</body>
</html>