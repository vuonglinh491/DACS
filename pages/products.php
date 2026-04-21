<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LKSecure - Giải Pháp An Ninh Thông Minh</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style_home.css">
    <link rel="stylesheet" href="../assets/css/style_products.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
</head>
<body>
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

    <main class="product-body-section">
        <div class="product-container">
            <div class="product-page-header">
                <h1>Sản Phẩm An Ninh</h1>
                <p>Khám phá bộ sưu tập thiết bị an ninh chất lượng cao với công nghệ hiện đại</p>
            </div>

            <div class="search-filter-bar">
                <div class="search-box-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Tìm kiếm sản phẩm...">
                </div>
                <div class="filter-controls-right">
                    <select class="sort-dropdown">
                        <option>Sắp xếp theo tên</option>
                    </select>
                    <div class="view-toggle">
                        <button class="btn-view active"><i class="fas fa-th-large"></i></button>
                        <button class="btn-view"><i class="fas fa-list"></i></button>
                    </div>
                </div>
            </div>

            <div class="product-main-layout">
                <aside class="product-sidebar">
                    <div class="filter-group">
                        <h3 class="filter-label">Danh mục</h3>
                        <h3 class="filter-label">Thương hiệu</h3>
                        <h3 class="filter-label">Khoảng giá</h3>
                        <div class="price-filter-inputs">
                            <p class="input-hint">Giá tối thiểu (₫)</p>
                            <input type="number" value="0" class="f-input">
                            <p class="input-hint">Giá tối đa (₫)</p>
                            <input type="text" placeholder="Không giới hạn" class="f-input">
                            <div class="f-action-btns">
                                <button class="btn-apply-f">Áp dụng</button>
                                <button class="btn-reset-f">Xóa</button>
                            </div>
                        </div>
                        <h3 class="filter-label">Tình trạng</h3>
                        <div class="status-tags-container">
                            <div class="status-tag active">Tất cả</div>
                            <div class="status-tag">Còn hàng</div>
                            <div class="status-tag">Hết hàng</div>
                        </div>
                    </div>
                </aside>

                <section class="product-listing-content">
                    <p class="results-summary">Hiển thị 0 / 0 sản phẩm</p>
                    <div class="no-product-found">
                        Không tìm thấy sản phẩm nào
                    </div>
                </section>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="nav-logo" style="margin-bottom: 30px;">
                    <i class="fas fa-shield-halved"></i>
                    <span>LKSecure</span>
                </div>
                <p>An tâm cho mọi gia đình Việt với giải pháp an ninh toàn diện.</p>
            </div>
            <div class="footer-col">
                <h2>Liên kết</h2>
                <ul>
                    <li><a href="home.php">TRANG CHỦ</a></li>
                    <li><a href="products.php">SẢN PHẨM</a></li>
                    <li><a href="#">LIÊN HỆ</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h2>Hỗ trợ</h2>
                <ul>
                    <li><a href="#">Bảo hành</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Thông tin liên hệ</h3>
                <p><i class="fas fa-phone" style="margin-right: 10px;"></i> 0123 456 789</p>
                <p><i class="fas fa-envelope" style="margin-right: 10px;"></i> info@lksecure.vn</p>
            </div>
        </div>
        <div style="text-align: center; padding-top: 40px; font-size: 13px;">
            <p>&copy; 2026 LKSecure. Tất cả quyền được bảo lưu.</p>
        </div>
    </footer>

    <button class="fab-chat">
        <i class="fas fa-comment-dots"></i>
    </button>

</body>
</html>