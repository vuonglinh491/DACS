<?php
// ============================================================
//  LKSecure — Trang Thanh Toán (Checkout)
// ============================================================
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth_check.php';
requireLogin(); // Bắt buộc đăng nhập

$user_id = (int)$_SESSION['user_id'];

// Lấy thông tin user
$stmt = $conn->prepare(
    "SELECT full_name, email, phone, address, province, district, ward
     FROM users WHERE id = ?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Lấy địa chỉ mặc định
$defaultAddr = null;
$addrRes = $conn->prepare(
    "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, id ASC LIMIT 1"
);
$addrRes->bind_param("i", $user_id);
$addrRes->execute();
$defaultAddr = $addrRes->get_result()->fetch_assoc();
$addrRes->close();

// Lấy giỏ hàng
$cartItems = [];
$cartRes = $conn->prepare(
    "SELECT c.product_id, c.quantity, p.name, p.price, p.original_price, p.image, p.stock, p.status
     FROM cart c
     JOIN products p ON c.product_id = p.id
     WHERE c.user_id = ?
     ORDER BY c.added_at DESC"
);
$cartRes->bind_param("i", $user_id);
$cartRes->execute();
$cartResult = $cartRes->get_result();
while ($row = $cartResult->fetch_assoc()) $cartItems[] = $row;
$cartRes->close();

if (empty($cartItems)) {
    header("Location: products.php?msg=empty_cart"); exit;
}

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping_fee = $subtotal >= 5000000 ? 0 : 30000;
$total = $subtotal + $shipping_fee;

$ship_name  = $defaultAddr['recipient_name'] ?? $user['full_name'] ?? '';
$ship_phone = $defaultAddr['recipient_phone'] ?? $user['phone'] ?? '';
$ship_addr  = trim(implode(', ', array_filter([
    $defaultAddr['address']  ?? $user['address']  ?? '',
    $defaultAddr['ward']     ?? $user['ward']     ?? '',
    $defaultAddr['district'] ?? $user['district'] ?? '',
    $defaultAddr['province'] ?? $user['province'] ?? '',
])));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - LKSecure</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
    <style>
        body { background: #f1f5f9; }
        .checkout-wrap { max-width: 1080px; margin: 0 auto; padding: 32px 16px 60px; }
        .checkout-header { font-size: 26px; font-weight: 800; color: #111827; margin-bottom: 28px; display: flex; align-items: center; gap: 12px; }
        .checkout-grid { display: grid; grid-template-columns: 1fr 380px; gap: 24px; }
        @media(max-width: 900px) { .checkout-grid { grid-template-columns: 1fr; } }

        .card { background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-bottom: 20px; }
        .card-title { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 8px; }
        .card-title i { color: #4f46e5; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-group label .req { color: #dc2626; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 11px 14px; border: 1.5px solid #e5e7eb;
            border-radius: 10px; font-size: 14px; transition: border .2s; box-sizing: border-box;
            font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none; border-color: #4f46e5;
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        @media(max-width: 500px) { .form-row { grid-template-columns: 1fr; } }

        /* Payment methods */
        .pay-options { display: flex; flex-direction: column; gap: 10px; }
        .pay-option { display: flex; align-items: center; gap: 14px; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; cursor: pointer; transition: all .2s; }
        .pay-option:hover { border-color: #a5b4fc; background: #f5f3ff; }
        .pay-option.selected { border-color: #4f46e5; background: #ede9fe; }
        .pay-option input[type=radio] { accent-color: #4f46e5; width: 18px; height: 18px; }
        .pay-option-info { flex: 1; }
        .pay-option-name { font-weight: 700; font-size: 14px; color: #111827; }
        .pay-option-desc { font-size: 12px; color: #6b7280; margin-top: 2px; }
        .pay-option-icon { font-size: 24px; width: 40px; text-align: center; }

        /* Order summary */
        .order-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
        .order-item:last-of-type { border: none; }
        .order-item-img { width: 56px; height: 56px; border-radius: 8px; overflow: hidden; background: #f8fafc; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #e5e7eb; }
        .order-item-img img { width: 100%; height: 100%; object-fit: contain; padding: 4px; }
        .order-item-info { flex: 1; min-width: 0; }
        .order-item-name { font-size: 13px; font-weight: 600; color: #111827; line-height: 1.4; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .order-item-qty  { font-size: 12px; color: #6b7280; }
        .order-item-price { font-size: 14px; font-weight: 700; color: #4f46e5; flex-shrink: 0; }

        .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 14px; color: #6b7280; }
        .summary-row.total { font-size: 18px; font-weight: 800; color: #111827; border-top: 2px solid #f3f4f6; margin-top: 8px; padding-top: 16px; }
        .summary-row.total .val { color: #4f46e5; }
        .free-ship { color: #10b981; font-weight: 600; }

        .btn-place-order { width: 100%; padding: 16px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; border: none; border-radius: 12px; font-size: 17px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: opacity .2s, transform .15s; margin-top: 16px; }
        .btn-place-order:hover { opacity: .95; transform: translateY(-1px); }
        .btn-place-order:active { transform: translateY(0); }
        .btn-place-order:disabled { background: #9ca3af; cursor: not-allowed; transform: none; }

        .secure-note { display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 12px; color: #6b7280; margin-top: 10px; }

        /* Toast */
        .toast-popup { position: fixed; bottom: 32px; right: 32px; background: #1f2937; color: #fff; padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; z-index: 9999; transform: translateY(80px); opacity: 0; transition: all .3s; max-width: 320px; box-shadow: 0 8px 24px rgba(0,0,0,.3); }
        .toast-popup.show { transform: translateY(0); opacity: 1; }
        .toast-popup.success i { color: #34d399; }
        .toast-popup.error i { color: #f87171; }

        /* Success overlay */
        .success-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 10000; align-items: center; justify-content: center; }
        .success-overlay.show { display: flex; }
        .success-box { background: #fff; border-radius: 20px; padding: 48px 40px; text-align: center; max-width: 400px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,.3); }
        .success-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 36px; color: #fff; }
        .success-title { font-size: 22px; font-weight: 800; color: #111827; margin-bottom: 8px; }
        .success-sub { color: #6b7280; font-size: 15px; margin-bottom: 24px; }
        .success-code { font-family: monospace; font-size: 18px; font-weight: 700; color: #4f46e5; background: #ede9fe; padding: 8px 20px; border-radius: 8px; display: inline-block; margin-bottom: 24px; }
        .success-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn-continue { padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: 2px solid #4f46e5; color: #4f46e5; background: #fff; }
        .btn-orders  { padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; background: #4f46e5; color: #fff; border: 2px solid #4f46e5; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<header class="navbar">
    <a href="home.php" class="nav-logo">
        <i class="fa-solid fa-shield-halved"></i> LK Secure
    </a>
    <nav class="nav-links">
        <a href="home.php">TRANG CHỦ</a>
        <a href="products.php">SẢN PHẨM</a>
        <a href="about.php">GIỚI THIỆU</a>
        <a href="contact.php">LIÊN HỆ</a>
    </nav>
    <div class="nav-actions">
        <?php include __DIR__ . '/../config/nav_partial.php'; ?>
    </div>
</header>

<div class="checkout-wrap">
    <div class="checkout-header">
        <i class="fas fa-credit-card" style="color:#4f46e5;"></i>
        Thanh Toán
    </div>

    <div class="checkout-grid">
        <!-- Left: Form -->
        <div>
            <!-- Thông tin giao hàng -->
            <div class="card">
                <div class="card-title"><i class="fas fa-map-marker-alt"></i> Thông tin giao hàng</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Họ và tên <span class="req">*</span></label>
                        <input type="text" id="shipName" value="<?= htmlspecialchars($ship_name) ?>" placeholder="Nguyễn Văn A">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại <span class="req">*</span></label>
                        <input type="tel" id="shipPhone" value="<?= htmlspecialchars($ship_phone) ?>" placeholder="0987654321">
                    </div>
                </div>
                <div class="form-group">
                    <label>Địa chỉ giao hàng <span class="req">*</span></label>
                    <input type="text" id="shipAddress" value="<?= htmlspecialchars($ship_addr) ?>" placeholder="Số nhà, tên đường, phường, quận, tỉnh/thành phố">
                </div>
                <div class="form-group">
                    <label>Ghi chú đơn hàng</label>
                    <textarea id="orderNote" rows="3" placeholder="Ghi chú cho người giao hàng (không bắt buộc)..."></textarea>
                </div>
            </div>

            <!-- Phương thức thanh toán -->
            <div class="card">
                <div class="card-title"><i class="fas fa-wallet"></i> Phương thức thanh toán</div>
                <div class="pay-options">
                    <label class="pay-option selected" id="pay-cod">
                        <input type="radio" name="payment" value="cod" checked>
                        <span class="pay-option-icon" style="color:#f59e0b;"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="pay-option-info">
                            <div class="pay-option-name">Thanh toán khi nhận hàng (COD)</div>
                            <div class="pay-option-desc">Trả tiền mặt khi nhận hàng, không phí phụ thu</div>
                        </div>
                    </label>
                    <label class="pay-option" id="pay-bank">
                        <input type="radio" name="payment" value="bank_transfer">
                        <span class="pay-option-icon" style="color:#3b82f6;"><i class="fas fa-building-columns"></i></span>
                        <div class="pay-option-info">
                            <div class="pay-option-name">Chuyển khoản ngân hàng</div>
                            <div class="pay-option-desc">MB Bank: 0393860031 — LKSecure Technology</div>
                        </div>
                    </label>
                    <label class="pay-option" id="pay-momo">
                        <input type="radio" name="payment" value="momo">
                        <span class="pay-option-icon" style="color:#d72f8f;"><i class="fas fa-mobile-screen"></i></span>
                        <div class="pay-option-info">
                            <div class="pay-option-name">Ví MoMo</div>
                            <div class="pay-option-desc">Thanh toán nhanh qua MoMo</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Right: Summary -->
        <div>
            <div class="card" style="position: sticky; top: 90px;">
                <div class="card-title"><i class="fas fa-receipt"></i> Đơn hàng của bạn</div>

                <?php foreach ($cartItems as $item): ?>
                <div class="order-item">
                    <div class="order-item-img">
                        <?php if ($item['image']): ?>
                            <img src="../assets/imgs/<?= htmlspecialchars($item['image']) ?>"
                                 onerror="this.style.display='none'">
                        <?php else: ?>
                            <i class="fas fa-box" style="color:#d1d5db;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="order-item-info">
                        <div class="order-item-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="order-item-qty">x<?= $item['quantity'] ?></div>
                    </div>
                    <div class="order-item-price"><?= number_format($item['price'] * $item['quantity']) ?>đ</div>
                </div>
                <?php endforeach; ?>

                <div style="margin-top: 16px;">
                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <span><?= number_format($subtotal) ?>đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span><?= $shipping_fee == 0 ? '<span class="free-ship">Miễn phí</span>' : number_format($shipping_fee) . 'đ' ?></span>
                    </div>
                    <?php if ($shipping_fee > 0): ?>
                    <div style="font-size:12px;color:#10b981;margin-bottom:4px;text-align:right;">
                        <i class="fas fa-info-circle"></i> Miễn phí ship đơn từ 5.000.000đ
                    </div>
                    <?php endif; ?>
                    <div class="summary-row total">
                        <span>Tổng cộng</span>
                        <span class="val"><?= number_format($total) ?>đ</span>
                    </div>
                </div>

                <button class="btn-place-order" id="placeOrderBtn">
                    <i class="fas fa-check-circle"></i>
                    Đặt hàng ngay
                </button>
                <div class="secure-note">
                    <i class="fas fa-lock"></i> Thông tin được bảo mật tuyệt đối
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success overlay -->
<div class="success-overlay" id="successOverlay">
    <div class="success-box">
        <div class="success-icon"><i class="fas fa-check"></i></div>
        <div class="success-title">Đặt hàng thành công!</div>
        <div class="success-sub">Cảm ơn bạn đã tin tưởng LKSecure. Chúng tôi sẽ liên hệ xác nhận đơn sớm nhất.</div>
        <div class="success-code" id="orderCodeDisplay"></div>
        <div class="success-btns">
            <a href="products.php" class="btn-continue"><i class="fas fa-arrow-left"></i> Tiếp tục mua</a>
            <a href="profile.php" class="btn-orders"><i class="fas fa-receipt"></i> Xem đơn hàng</a>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast-popup" id="toast">
    <i class="fas fa-exclamation-circle"></i>
    <span id="toastMsg"></span>
</div>

<script>
// Payment option selection
document.querySelectorAll('.pay-option').forEach(function(opt) {
    opt.addEventListener('click', function() {
        document.querySelectorAll('.pay-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
    });
});

function showToast(msg, type) {
    var t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.className = 'toast-popup ' + (type || 'error');
    t.classList.add('show');
    setTimeout(function() { t.classList.remove('show'); }, 3200);
}

// Place order
document.getElementById('placeOrderBtn').addEventListener('click', function() {
    var shipName    = document.getElementById('shipName').value.trim();
    var shipPhone   = document.getElementById('shipPhone').value.trim();
    var shipAddress = document.getElementById('shipAddress').value.trim();
    var note        = document.getElementById('orderNote').value.trim();
    var payment     = document.querySelector('input[name="payment"]:checked')?.value || 'cod';

    if (!shipName)    { showToast('Vui lòng nhập họ tên người nhận.', 'error'); return; }
    if (!shipPhone)   { showToast('Vui lòng nhập số điện thoại.', 'error'); return; }
    if (!shipAddress) { showToast('Vui lòng nhập địa chỉ giao hàng.', 'error'); return; }
    if (!/^(0[35789])([0-9]{8})$/.test(shipPhone)) {
        showToast('Số điện thoại không hợp lệ.', 'error'); return;
    }

    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

    var body = new URLSearchParams({
        action: 'place',
        ship_name: shipName,
        ship_phone: shipPhone,
        ship_address: shipAddress,
        note: note,
        payment_method: payment
    });

    fetch('../actions/order_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body.toString()
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.success) {
            document.getElementById('orderCodeDisplay').textContent = data.order_code || '';
            document.getElementById('successOverlay').classList.add('show');
        } else {
            showToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
        }
    })
    .catch(function() { showToast('Lỗi kết nối. Vui lòng thử lại.', 'error'); })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Đặt hàng ngay';
    });
});
</script>
</body>
</html>
