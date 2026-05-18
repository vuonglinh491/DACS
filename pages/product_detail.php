<?php
// ============================================================
//  LKSecure — Trang chi tiết sản phẩm
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';

$product_id = (int)($_GET['id'] ?? 0);
if (!$product_id) {
    header("Location: products.php"); exit;
}

// Lấy thông tin sản phẩm
$stmt = $conn->prepare(
    "SELECT p.*, c.name AS category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.id = ? AND p.status != 'discontinued'"
);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: products.php?error=notfound"); exit;
}

// Thông số kỹ thuật
$specs = [];
$sRes = $conn->prepare("SELECT spec_name, spec_value FROM product_specs WHERE product_id = ? ORDER BY sort_order ASC");
$sRes->bind_param("i", $product_id);
$sRes->execute();
$specResult = $sRes->get_result();
while ($row = $specResult->fetch_assoc()) $specs[] = $row;
$sRes->close();

// Đánh giá sản phẩm
$reviews = [];
$rRes = $conn->prepare(
    "SELECT r.rating, r.comment, r.created_at, u.full_name
     FROM reviews r
     JOIN users u ON r.user_id = u.id
     WHERE r.product_id = ? AND r.is_approved = 1
     ORDER BY r.created_at DESC LIMIT 10"
);
$rRes->bind_param("i", $product_id);
$rRes->execute();
$reviewResult = $rRes->get_result();
while ($row = $reviewResult->fetch_assoc()) $reviews[] = $row;
$rRes->close();

// Sản phẩm liên quan
$related = [];
if ($product['category_id']) {
    $relRes = $conn->prepare(
        "SELECT id, name, price, original_price, image
         FROM products
         WHERE category_id = ? AND id != ? AND status = 'active'
         ORDER BY RAND() LIMIT 4"
    );
    $relRes->bind_param("ii", $product['category_id'], $product_id);
    $relRes->execute();
    $relResult = $relRes->get_result();
    while ($row = $relResult->fetch_assoc()) $related[] = $row;
    $relRes->close();
}

$avg_rating = count($reviews) > 0
    ? round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1)
    : 0;

$is_logged_in = isset($_SESSION['user_id']) ? 'true' : 'false';
$discount_pct = ($product['original_price'] > $product['price'] && $product['original_price'] > 0)
    ? round((1 - $product['price'] / $product['original_price']) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - LKSecure</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
    <style>
        .product-detail-wrap { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .breadcrumb { font-size: 13px; color: #6b7280; margin-bottom: 24px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .breadcrumb a { color: #4f46e5; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .product-main { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        @media(max-width: 768px) { .product-main { grid-template-columns: 1fr; gap: 24px; } }

        /* Gallery */
        .product-gallery { position: sticky; top: 90px; align-self: start; }
        .main-image-box { border-radius: 16px; overflow: hidden; background: #f8fafc; border: 1px solid #e5e7eb; aspect-ratio: 1; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; }
        .main-image-box img { width: 100%; height: 100%; object-fit: contain; padding: 20px; }
        .main-image-box .no-img { font-size: 80px; color: #d1d5db; }

        /* Info */
        .product-badge-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-cat   { background: #ede9fe; color: #6d28d9; }
        .badge-stock { background: #d1fae5; color: #065f46; }
        .badge-out   { background: #fee2e2; color: #991b1b; }
        .badge-sale  { background: #fef3c7; color: #92400e; }

        .product-title { font-size: 26px; font-weight: 800; color: #111827; line-height: 1.3; margin-bottom: 8px; }
        .product-brand { font-size: 13px; color: #6b7280; margin-bottom: 16px; }
        .product-brand span { color: #4f46e5; font-weight: 600; }

        .rating-row { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .stars { color: #f59e0b; font-size: 16px; }
        .rating-text { font-size: 14px; color: #6b7280; }

        .price-block { background: linear-gradient(135deg, #f5f3ff, #ede9fe); border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .current-price { font-size: 34px; font-weight: 800; color: #4f46e5; }
        .original-price { font-size: 18px; color: #9ca3af; text-decoration: line-through; margin-left: 12px; }
        .save-badge { display: inline-block; background: #dc2626; color: #fff; font-size: 12px; font-weight: 700; padding: 2px 8px; border-radius: 6px; margin-left: 10px; }

        .short-desc { color: #374151; font-size: 15px; line-height: 1.7; margin-bottom: 24px; }

        .qty-row { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
        .qty-label { font-weight: 600; color: #374151; }
        .qty-control { display: flex; align-items: center; border: 1.5px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
        .qty-btn { width: 40px; height: 40px; border: none; background: #f9fafb; cursor: pointer; font-size: 18px; color: #374151; transition: background .15s; }
        .qty-btn:hover { background: #e5e7eb; }
        .qty-num { width: 50px; text-align: center; border: none; border-left: 1.5px solid #e5e7eb; border-right: 1.5px solid #e5e7eb; font-size: 16px; font-weight: 700; height: 40px; outline: none; }

        .action-btns { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
        .btn-cart-main { flex: 1; min-width: 160px; padding: 14px 24px; background: #4f46e5; color: #fff; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background .2s, transform .1s; }
        .btn-cart-main:hover { background: #4338ca; transform: translateY(-1px); }
        .btn-cart-main:active { transform: translateY(0); }
        .btn-cart-main:disabled { background: #9ca3af; cursor: not-allowed; transform: none; }
        .btn-buy-now { flex: 1; min-width: 160px; padding: 14px 24px; background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity .2s; }
        .btn-buy-now:hover { opacity: 0.9; }

        .features-list { list-style: none; padding: 0; margin: 0 0 20px; }
        .features-list li { display: flex; align-items: center; gap: 10px; padding: 6px 0; font-size: 14px; color: #374151; border-bottom: 1px dashed #f3f4f6; }
        .features-list li:last-child { border: none; }
        .features-list i { color: #10b981; width: 16px; }

        /* Tabs */
        .detail-tabs { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; margin-bottom: 48px; }
        .tab-nav { display: flex; border-bottom: 2px solid #f3f4f6; }
        .tab-nav-btn { flex: 1; padding: 16px; border: none; background: none; font-size: 15px; font-weight: 600; color: #6b7280; cursor: pointer; transition: all .2s; border-bottom: 2px solid transparent; margin-bottom: -2px; }
        .tab-nav-btn.active { color: #4f46e5; border-bottom-color: #4f46e5; }
        .tab-nav-btn:hover:not(.active) { color: #374151; background: #f9fafb; }
        .tab-content { padding: 32px; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        /* Specs table */
        .specs-table { width: 100%; border-collapse: collapse; }
        .specs-table tr:nth-child(even) td { background: #f9fafb; }
        .specs-table td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
        .specs-table td:first-child { font-weight: 600; color: #374151; width: 40%; }
        .specs-table td:last-child { color: #6b7280; }

        /* Reviews */
        .review-summary { display: flex; align-items: center; gap: 32px; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid #f3f4f6; }
        .big-rating { font-size: 56px; font-weight: 800; color: #111827; line-height: 1; }
        .big-rating sub { font-size: 20px; color: #9ca3af; font-weight: 400; }
        .review-card { background: #f9fafb; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
        .review-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
        .reviewer-name { font-weight: 700; color: #111827; font-size: 15px; }
        .review-date { font-size: 12px; color: #9ca3af; }
        .review-text { color: #4b5563; font-size: 14px; line-height: 1.6; }

        /* Related */
        .section-heading { font-size: 22px; font-weight: 800; color: #111827; margin-bottom: 24px; }
        .related-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        @media(max-width: 900px) { .related-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(max-width: 500px) { .related-grid { grid-template-columns: 1fr; } }
        .related-card { background: #fff; border-radius: 12px; border: 1.5px solid #e5e7eb; overflow: hidden; transition: box-shadow .2s, transform .2s; cursor: pointer; }
        .related-card:hover { box-shadow: 0 8px 24px rgba(79,70,229,.12); transform: translateY(-3px); }
        .related-img { aspect-ratio: 1; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .related-img img { width: 100%; height: 100%; object-fit: contain; padding: 12px; }
        .related-img .no-img { font-size: 40px; color: #d1d5db; }
        .related-info { padding: 12px 14px; }
        .related-name { font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 6px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .related-price { font-size: 15px; font-weight: 800; color: #4f46e5; }

        /* Toast */
        .toast-popup { position: fixed; bottom: 32px; right: 32px; background: #1f2937; color: #fff; padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; z-index: 9999; transform: translateY(80px); opacity: 0; transition: all .3s; max-width: 300px; box-shadow: 0 8px 24px rgba(0,0,0,.3); }
        .toast-popup.show { transform: translateY(0); opacity: 1; }
        .toast-popup.success i { color: #34d399; }
        .toast-popup.error i { color: #f87171; }
    </style>
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

<div class="product-detail-wrap">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="home.php"><i class="fas fa-home"></i> Trang chủ</a>
        <i class="fas fa-chevron-right" style="font-size:10px;"></i>
        <a href="products.php">Sản phẩm</a>
        <i class="fas fa-chevron-right" style="font-size:10px;"></i>
        <span><?= htmlspecialchars($product['category_name'] ?? 'Sản phẩm') ?></span>
        <i class="fas fa-chevron-right" style="font-size:10px;"></i>
        <span style="color:#111827;"><?= htmlspecialchars($product['name']) ?></span>
    </div>

    <!-- Main product block -->
    <div class="product-main">

        <!-- Gallery -->
        <div class="product-gallery">
            <div class="main-image-box">
                <?php if ($product['image']): ?>
                    <img src="../assets/imgs/<?= htmlspecialchars($product['image']) ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         onerror="this.parentElement.innerHTML='<span class=\'no-img\'><i class=\'fas fa-camera\'></i></span>'">
                <?php else: ?>
                    <span class="no-img"><i class="fas fa-camera"></i></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info -->
        <div class="product-info">
            <div class="product-badge-row">
                <?php if ($product['category_name']): ?>
                    <span class="badge badge-cat"><i class="fas fa-tag"></i> <?= htmlspecialchars($product['category_name']) ?></span>
                <?php endif; ?>
                <?php if ($product['status'] === 'active' && $product['stock'] > 0): ?>
                    <span class="badge badge-stock"><i class="fas fa-check-circle"></i> Còn hàng (<?= $product['stock'] ?>)</span>
                <?php elseif ($product['status'] === 'out_of_stock' || $product['stock'] == 0): ?>
                    <span class="badge badge-out"><i class="fas fa-times-circle"></i> Hết hàng</span>
                <?php endif; ?>
                <?php if ($discount_pct > 0): ?>
                    <span class="badge badge-sale"><i class="fas fa-percent"></i> Giảm <?= $discount_pct ?>%</span>
                <?php endif; ?>
            </div>

            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
            <?php if ($product['brand']): ?>
                <div class="product-brand">Thương hiệu: <span><?= htmlspecialchars($product['brand']) ?></span>
                    <?php if ($product['sku']): ?> &nbsp;|&nbsp; SKU: <span><?= htmlspecialchars($product['sku']) ?></span><?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Rating -->
            <?php if (count($reviews) > 0): ?>
            <div class="rating-row">
                <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa<?= $i <= round($avg_rating) ? 's' : 'r' ?> fa-star"></i>
                    <?php endfor; ?>
                </div>
                <span class="rating-text"><?= $avg_rating ?>/5 (<?= count($reviews) ?> đánh giá)</span>
                <span class="rating-text">| <i class="fas fa-shopping-bag"></i> <?= number_format($product['sold_count']) ?> đã bán</span>
            </div>
            <?php endif; ?>

            <!-- Price -->
            <div class="price-block">
                <span class="current-price"><?= number_format($product['price']) ?>đ</span>
                <?php if ($product['original_price'] > $product['price']): ?>
                    <span class="original-price"><?= number_format($product['original_price']) ?>đ</span>
                    <span class="save-badge">-<?= $discount_pct ?>%</span>
                <?php endif; ?>
            </div>

            <?php if ($product['short_desc']): ?>
                <p class="short-desc"><?= htmlspecialchars($product['short_desc']) ?></p>
            <?php endif; ?>

            <!-- Quick features -->
            <ul class="features-list">
                <li><i class="fas fa-shield-halved"></i> Bảo hành chính hãng 24 tháng</li>
                <li><i class="fas fa-truck-fast"></i> Giao hàng toàn quốc, lắp đặt tại nhà</li>
                <li><i class="fas fa-headset"></i> Hỗ trợ kỹ thuật 24/7</li>
                <li><i class="fas fa-rotate-left"></i> Đổi trả trong 7 ngày nếu lỗi sản xuất</li>
            </ul>

            <!-- Qty & Buttons -->
            <?php $in_stock = ($product['status'] === 'active' && $product['stock'] > 0); ?>

            <div class="qty-row">
                <span class="qty-label">Số lượng:</span>
                <div class="qty-control">
                    <button class="qty-btn" id="qtyMinus" <?= !$in_stock ? 'disabled' : '' ?>>−</button>
                    <input type="number" class="qty-num" id="qtyInput" value="1" min="1" max="<?= $product['stock'] ?>" <?= !$in_stock ? 'disabled' : '' ?>>
                    <button class="qty-btn" id="qtyPlus"  <?= !$in_stock ? 'disabled' : '' ?>>+</button>
                </div>
            </div>

            <div class="action-btns">
                <button class="btn-cart-main" id="addToCartBtn"
                    data-product-id="<?= $product['id'] ?>"
                    data-product-name="<?= htmlspecialchars($product['name']) ?>"
                    <?= !$in_stock ? 'disabled' : '' ?>>
                    <i class="fas fa-cart-plus"></i>
                    <?= $in_stock ? 'Thêm vào giỏ hàng' : 'Hết hàng' ?>
                </button>
                <?php if ($in_stock): ?>
                <button class="btn-buy-now" id="buyNowBtn">
                    <i class="fas fa-bolt"></i> Mua ngay
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Detail Tabs -->
    <div class="detail-tabs">
        <div class="tab-nav">
            <button class="tab-nav-btn active" data-tab="tab-desc">Mô tả sản phẩm</button>
            <?php if (!empty($specs)): ?>
            <button class="tab-nav-btn" data-tab="tab-specs">Thông số kỹ thuật</button>
            <?php endif; ?>
            <button class="tab-nav-btn" data-tab="tab-reviews">Đánh giá (<?= count($reviews) ?>)</button>
        </div>
        <div class="tab-content">
            <!-- Mô tả -->
            <div class="tab-pane active" id="tab-desc">
                <?php if ($product['description']): ?>
                    <?= $product['description'] ?>
                <?php else: ?>
                    <p style="color:#6b7280;"><?= htmlspecialchars($product['short_desc'] ?? 'Chưa có mô tả chi tiết.') ?></p>
                <?php endif; ?>
            </div>

            <!-- Thông số -->
            <?php if (!empty($specs)): ?>
            <div class="tab-pane" id="tab-specs">
                <table class="specs-table">
                    <?php foreach ($specs as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['spec_name']) ?></td>
                        <td><?= htmlspecialchars($s['spec_value']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endif; ?>

            <!-- Đánh giá -->
            <div class="tab-pane" id="tab-reviews">
                <?php if (count($reviews) > 0): ?>
                <div class="review-summary">
                    <div>
                        <div class="big-rating"><?= $avg_rating ?><sub>/5</sub></div>
                        <div class="stars" style="font-size:20px;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa<?= $i <= round($avg_rating) ? 's' : 'r' ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <div style="font-size:13px;color:#6b7280;margin-top:4px;"><?= count($reviews) ?> đánh giá</div>
                    </div>
                </div>
                <?php foreach ($reviews as $rv): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div>
                            <div class="reviewer-name"><?= htmlspecialchars($rv['full_name']) ?></div>
                            <div class="stars" style="font-size:13px;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa<?= $i <= $rv['rating'] ? 's' : 'r' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="review-date"><?= date('d/m/Y', strtotime($rv['created_at'])) ?></div>
                    </div>
                    <?php if ($rv['comment']): ?>
                        <p class="review-text"><?= htmlspecialchars($rv['comment']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#6b7280;text-align:center;padding:40px 0;">
                        <i class="fas fa-star" style="font-size:32px;color:#e5e7eb;display:block;margin-bottom:12px;"></i>
                        Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sản phẩm liên quan -->
    <?php if (!empty($related)): ?>
    <div style="margin-bottom: 48px;">
        <h2 class="section-heading">Sản Phẩm Liên Quan</h2>
        <div class="related-grid">
            <?php foreach ($related as $rel): ?>
            <div class="related-card" onclick="window.location='product_detail.php?id=<?= $rel['id'] ?>'">
                <div class="related-img">
                    <?php if ($rel['image']): ?>
                        <img src="../assets/imgs/<?= htmlspecialchars($rel['image']) ?>"
                             alt="<?= htmlspecialchars($rel['name']) ?>"
                             onerror="this.parentElement.innerHTML='<span class=\'no-img\'><i class=\'fas fa-camera\'></i></span>'">
                    <?php else: ?>
                        <span class="no-img"><i class="fas fa-camera"></i></span>
                    <?php endif; ?>
                </div>
                <div class="related-info">
                    <div class="related-name"><?= htmlspecialchars($rel['name']) ?></div>
                    <div class="related-price"><?= number_format($rel['price']) ?>đ</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- Footer -->
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

<!-- Toast -->
<div class="toast-popup" id="toast">
    <i class="fas fa-check-circle"></i>
    <span id="toastMsg"></span>
</div>

<script>
var USER_LOGGED_IN = <?= $is_logged_in ?>;
var LOGIN_URL = 'login.php';
var PRODUCT_ID = <?= $product_id ?>;
var MAX_STOCK  = <?= $product['stock'] ?>;
</script>
<script src="../assets/js/main.js"></script>
<script>
// Tab switcher
document.querySelectorAll('.tab-nav-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var target = this.dataset.tab;
        document.querySelectorAll('.tab-nav-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        var pane = document.getElementById(target);
        if (pane) pane.classList.add('active');
    });
});

// Qty control
var qtyInput = document.getElementById('qtyInput');
document.getElementById('qtyMinus')?.addEventListener('click', function() {
    var v = parseInt(qtyInput.value) || 1;
    if (v > 1) qtyInput.value = v - 1;
});
document.getElementById('qtyPlus')?.addEventListener('click', function() {
    var v = parseInt(qtyInput.value) || 1;
    if (v < MAX_STOCK) qtyInput.value = v + 1;
});

// Toast helper
function showToast(msg, type) {
    var t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.className = 'toast-popup ' + (type || 'success');
    t.classList.add('show');
    setTimeout(function() { t.classList.remove('show'); }, 3200);
}

// Add to cart
document.getElementById('addToCartBtn')?.addEventListener('click', function() {
    if (!USER_LOGGED_IN) {
        window.location.href = LOGIN_URL + '?redirect_to=product_detail.php?id=' + PRODUCT_ID;
        return;
    }
    var qty = parseInt(qtyInput?.value) || 1;
    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';

    fetch('../actions/cart_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=add&product_id=' + PRODUCT_ID + '&quantity=' + qty
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.success) {
            showToast(data.message || 'Đã thêm vào giỏ hàng!', 'success');
            // Update cart badge
            var badge = document.querySelector('.cart-badge');
            if (badge && data.cart_count > 0) {
                badge.textContent = data.cart_count;
                badge.style.display = 'flex';
            }
        } else {
            showToast(data.message || 'Có lỗi xảy ra.', 'error');
        }
    })
    .catch(function() { showToast('Lỗi kết nối.', 'error'); })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng';
    });
});

// Buy now
document.getElementById('buyNowBtn')?.addEventListener('click', function() {
    if (!USER_LOGGED_IN) {
        window.location.href = LOGIN_URL + '?redirect_to=checkout.php?buy_now=' + PRODUCT_ID;
        return;
    }
    var qty = parseInt(qtyInput?.value) || 1;
    // Thêm vào giỏ rồi chuyển sang checkout
    fetch('../actions/cart_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=add&product_id=' + PRODUCT_ID + '&quantity=' + qty
    })
    .then(r => r.json())
    .then(function(data) {
        window.location.href = 'checkout.php';
    })
    .catch(function() { window.location.href = 'checkout.php'; });
});
</script>
</body>
</html>
