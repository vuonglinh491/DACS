<?php
// ============================================================
//  LKSecure — Trang Tài Khoản / Profile
//  Yêu cầu đăng nhập; nếu chưa → redirect về login
// ============================================================
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth_check.php';
requireLogin(); // Redirect nếu chưa đăng nhập

$user_id = (int)$_SESSION['user_id'];

// Lấy thông tin user từ DB
$stmt = $conn->prepare(
    "SELECT full_name, email, phone, address, province, district, ward, avatar, oauth_provider
     FROM users WHERE id = ?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$full_name = htmlspecialchars($user['full_name'] ?? '');
$initial   = mb_strtoupper(mb_substr($full_name, 0, 1, 'UTF-8'), 'UTF-8') ?: '?';
$avatar    = $user['avatar'] ?? '';
$avatar_src = '';
if ($avatar) {
    $avatar_src = (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://'))
        ? htmlspecialchars($avatar)
        : '../assets/imgs/avatars/' . htmlspecialchars($avatar);
}
$is_oauth = !empty($user['oauth_provider']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản của tôi - LKSecure</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
    <style>
        .profile-page { background: #f8fafc; min-height: calc(100vh - 70px); padding: 32px 16px; }
        .profile-container { max-width: 900px; margin: 0 auto; }
        .profile-header-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 16px; padding: 32px; color: #fff;
            display: flex; align-items: center; gap: 24px; margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(79,70,229,.25);
        }
        .ph-avatar { width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center; font-size: 32px;
            font-weight: 700; overflow: hidden; border: 3px solid rgba(255,255,255,.4); flex-shrink: 0; }
        .ph-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .ph-info h2 { margin: 0 0 4px; font-size: 22px; }
        .ph-info p  { margin: 0; opacity: .8; font-size: 14px; }
        .ph-badge { display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.2); border-radius: 20px; padding: 4px 12px;
            font-size: 12px; font-weight: 600; margin-top: 8px; }

        .profile-tabs { display: flex; gap: 4px; background: #fff; border-radius: 12px;
            padding: 6px; box-shadow: 0 2px 8px rgba(0,0,0,.06); margin-bottom: 20px; }
        .ptab { flex: 1; padding: 10px; border: none; border-radius: 8px; background: transparent;
            cursor: pointer; font-size: 14px; font-weight: 500; color: #6b7280; transition: all .2s;
            display: flex; align-items: center; justify-content: center; gap: 8px; }
        .ptab.active { background: #4f46e5; color: #fff; }
        .ptab:hover:not(.active) { background: #f3f4f6; }

        .tab-panel { display: none; background: #fff; border-radius: 12px;
            padding: 28px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .tab-panel.active { display: block; }

        .section-title { font-size: 16px; font-weight: 700; color: #1f2937;
            margin: 0 0 20px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb;
            display: flex; align-items: center; gap: 8px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        @media(max-width:600px){ .form-row { grid-template-columns: 1fr; } }
        .form-group label { display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 6px; }
        .form-group input, .form-group select {
            width: 100%; padding: 10px 14px; border: 1.5px solid #e5e7eb;
            border-radius: 8px; font-size: 14px; transition: border .2s; box-sizing: border-box; }
        .form-group input:focus { outline: none; border-color: #4f46e5; }
        .btn-save { background: #4f46e5; color: #fff; border: none;
            padding: 12px 28px; border-radius: 10px; font-size: 15px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; gap: 8px; margin-top: 8px; }
        .btn-save:hover { background: #4338ca; }
        .btn-save:disabled { opacity: .6; cursor: not-allowed; }

        /* Địa chỉ */
        .addr-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; }
        .addr-card { border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 14px 16px; }
        .addr-card.default { border-color: #4f46e5; background: #f5f3ff; }
        .addr-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
        .addr-text { color: #374151; font-size: 14px; margin: 4px 0 0; }
        .addr-actions { display: flex; gap: 8px; flex-shrink: 0; }
        .btn-sm { padding: 5px 12px; border-radius: 6px; border: 1.5px solid #e5e7eb;
            font-size: 12px; font-weight: 600; cursor: pointer; background: #fff; color: #374151; }
        .btn-sm.danger { border-color: #fca5a5; color: #dc2626; }
        .btn-sm.primary { border-color: #4f46e5; color: #4f46e5; }
        .default-badge { display: inline-flex; align-items: center; gap: 4px;
            background: #ede9fe; color: #4f46e5; border-radius: 4px;
            padding: 2px 8px; font-size: 11px; font-weight: 700; }
        .btn-add-addr { width: 100%; padding: 12px; border: 2px dashed #c4b5fd;
            border-radius: 10px; background: transparent; color: #4f46e5;
            font-size: 14px; font-weight: 600; cursor: pointer; display: flex;
            align-items: center; justify-content: center; gap: 8px; margin-top: 4px; }
        .btn-add-addr:hover { background: #f5f3ff; }

        /* Form thêm/sửa địa chỉ */
        .addr-form { background: #f9fafb; border-radius: 10px; padding: 20px; margin-top: 16px; }
        .addr-form-title { font-size: 15px; font-weight: 700; margin-bottom: 16px; color: #1f2937; display:flex; align-items:center; gap:8px; }
        .addr-form select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%236b7280' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px; cursor: pointer; }
        .addr-form select:focus { outline: none; border-color: #4f46e5; }
        .addr-form input:focus { outline: none; border-color: #4f46e5; }

        /* Đơn hàng */
        .order-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px; }
        .otab { padding: 7px 14px; border-radius: 20px; border: 1.5px solid #e5e7eb;
            font-size: 13px; font-weight: 600; cursor: pointer; background: #fff; color: #6b7280; }
        .otab.active { background: #4f46e5; color: #fff; border-color: #4f46e5; }
        .order-card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 12px; }
        .order-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 8px; }
        .order-code-span { font-weight: 700; color: #4f46e5; font-size: 14px; }
        .order-date-span { font-size: 12px; color: #9ca3af; margin-left: 12px; }
        .status-badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .order-items-list { font-size: 13px; color: #374151; margin-bottom: 10px; }
        .order-item-row { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px dashed #f3f4f6; }
        .order-foot { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
        .order-total { font-weight: 700; font-size: 15px; color: #1f2937; }
        .btn-cancel { padding: 6px 14px; border-radius: 6px; border: 1.5px solid #fca5a5;
            color: #dc2626; background: #fff; font-size: 12px; font-weight: 600; cursor: pointer; }
        .empty-state { text-align: center; padding: 40px; color: #9ca3af; }
        .empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }

        /* Toast */
        .profile-toast { position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            padding: 12px 20px; border-radius: 10px; background: #1f2937; color: #fff;
            font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px;
            transform: translateY(80px); opacity: 0; transition: all .3s;
            box-shadow: 0 4px 20px rgba(0,0,0,.2); }
        .profile-toast.show { transform: translateY(0); opacity: 1; }
        .profile-toast.ok { background: #065f46; }
        .profile-toast.err { background: #7f1d1d; }

        /* Mật khẩu */
        .pass-section { margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        .oauth-note { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;
            padding: 10px 14px; font-size: 13px; color: #14532d; margin-bottom: 16px;
            display: flex; align-items: center; gap: 8px; }
        /* Avatar upload */
        .avatar-upload-area { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
        .avatar-preview { width: 72px; height: 72px; border-radius: 50%; overflow: hidden;
            border: 3px solid #e5e7eb; background: #4f46e5; color: #fff;
            display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; }
        .avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
        .btn-change-avatar { padding: 8px 16px; border-radius: 8px; border: 1.5px solid #4f46e5;
            color: #4f46e5; font-size: 13px; font-weight: 600; cursor: pointer; background: #fff; }

        /* Warranty tab */
        .warranty-form { max-width: 600px; }
        .wr-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px; }
        .wr-card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 16px; }
        .wr-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 6px; }
        .wr-name { font-weight: 700; color: #1f2937; }
        .wr-desc { font-size: 13px; color: #6b7280; margin: 4px 0 0; }
        .wr-meta { font-size: 12px; color: #9ca3af; margin-top: 6px; }
        .wr-note { font-size: 13px; color: #059669; margin-top: 4px; font-style: italic; }
    </style>
</head>
<body>

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
        <button class="icon-btn cart-icon-btn" title="Giỏ hàng">
            <i class="fas fa-shopping-cart"></i><span class="cart-badge" style="display:none;"></span>
        </button>
        <?php include __DIR__ . '/../config/nav_partial.php'; ?>
    </div>
</nav>

<div class="profile-page">
    <div class="profile-container">

        <!-- Header card -->
        <div class="profile-header-card">
            <div class="ph-avatar">
                <?php if ($avatar_src): ?>
                    <img src="<?= $avatar_src ?>" alt="avatar" onerror="this.parentElement.textContent='<?= $initial ?>'">
                <?php else: ?>
                    <?= $initial ?>
                <?php endif; ?>
            </div>
            <div class="ph-info">
                <h2><?= $full_name ?></h2>
                <p><?= htmlspecialchars($user['email'] ?? '') ?></p>
                <div class="ph-badge">
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <i class="fas fa-shield-halved"></i> Quản trị viên
                    <?php else: ?>
                        <i class="fas fa-user"></i> Khách hàng
                    <?php endif; ?>
                    <?php if ($is_oauth): ?>
                        &nbsp;·&nbsp; <i class="fab fa-<?= htmlspecialchars($user['oauth_provider']) ?>"></i>
                        <?= ucfirst(htmlspecialchars($user['oauth_provider'])) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="profile-tabs">
            <button class="ptab active" data-tab="info">
                <i class="fas fa-user"></i> Thông tin
            </button>
            <button class="ptab" data-tab="address">
                <i class="fas fa-map-marker-alt"></i> Địa chỉ
            </button>
            <button class="ptab" data-tab="orders">
                <i class="fas fa-clipboard-list"></i> Đơn hàng
            </button>
            <button class="ptab" data-tab="warranty">
                <i class="fas fa-shield-check"></i> Bảo hành
            </button>
        </div>

        <!-- Panel: Thông tin cá nhân -->
        <div class="tab-panel active" id="panel-info">
            <div class="section-title"><i class="fas fa-user-circle"></i> Thông tin cá nhân</div>

            <div class="avatar-upload-area">
                <div class="avatar-preview" id="avatarPreview">
                    <?php if ($avatar_src): ?>
                        <img src="<?= $avatar_src ?>" id="avatarImg" alt="avatar">
                    <?php else: ?>
                        <?= $initial ?>
                    <?php endif; ?>
                </div>
                <div>
                    <button class="btn-change-avatar" onclick="document.getElementById('avatarFile').click()">
                        <i class="fas fa-camera"></i> Đổi ảnh đại diện
                    </button>
                    <input type="file" id="avatarFile" accept="image/*" style="display:none">
                    <div style="font-size:12px;color:#9ca3af;margin-top:6px;">JPG / PNG · Tối đa 2MB</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Họ và tên <span style="color:#ef4444">*</span></label>
                    <input type="text" id="inf_name" value="<?= $full_name ?>" placeholder="Nguyễn Văn A">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" id="inf_phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="0912 345 678">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:16px">
                <label>Email</label>
                <input type="email" id="inf_email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="email@example.com">
            </div>

            <button class="btn-save" id="btnSaveInfo">
                <i class="fas fa-floppy-disk"></i> Lưu thay đổi
            </button>

            <!-- Đổi mật khẩu -->
            <div class="pass-section">
                <div class="section-title" style="margin-bottom:16px;">
                    <i class="fas fa-lock"></i>
                    Đổi mật khẩu
                    <?php if ($is_oauth): ?><small style="font-weight:400;font-size:13px;">(tuỳ chọn)</small><?php endif; ?>
                </div>
                <?php if ($is_oauth): ?>
                <div class="oauth-note">
                    <i class="fas fa-circle-info"></i>
                    Tài khoản <?= ucfirst(htmlspecialchars($user['oauth_provider'])) ?> — không cần nhập mật khẩu cũ.
                </div>
                <?php else: ?>
                <div class="form-group" style="margin-bottom:16px;">
                    <label>Mật khẩu hiện tại</label>
                    <input type="password" id="old_pass" placeholder="Nhập mật khẩu hiện tại">
                </div>
                <?php endif; ?>
                <div class="form-row">
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password" id="new_pass" placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <div class="form-group">
                        <label>Xác nhận mật khẩu</label>
                        <input type="password" id="cf_pass" placeholder="Nhập lại">
                    </div>
                </div>
                <button class="btn-save" id="btnChangePass">
                    <i class="fas fa-key"></i> Đổi mật khẩu
                </button>
            </div>
        </div>

        <!-- Panel: Địa chỉ -->
        <div class="tab-panel" id="panel-address">
            <div class="section-title"><i class="fas fa-map-marker-alt"></i> Địa chỉ nhận hàng</div>
            <div id="addrContent">
                <div class="empty-state"><i class="fas fa-circle-notch fa-spin"></i></div>
            </div>
            <!-- Form thêm/sửa địa chỉ (ẩn mặc định) -->
            <div id="addrFormWrap" style="display:none;"></div>
        </div>

        <!-- Panel: Đơn hàng -->
        <div class="tab-panel" id="panel-orders">
            <div class="section-title"><i class="fas fa-clipboard-list"></i> Đơn hàng của tôi</div>
            <div class="order-tabs">
                <button class="otab active" onclick="filterOrders('all',this)">Tất cả</button>
                <button class="otab" onclick="filterOrders('pending',this)">Chờ xác nhận</button>
                <button class="otab" onclick="filterOrders('confirmed',this)">Đã xác nhận</button>
                <button class="otab" onclick="filterOrders('shipping',this)">Đang giao</button>
                <button class="otab" onclick="filterOrders('delivered',this)">Đã giao</button>
                <button class="otab" onclick="filterOrders('cancelled',this)">Đã huỷ</button>
            </div>
            <div id="ordersContent">
                <div class="empty-state"><i class="fas fa-circle-notch fa-spin"></i></div>
            </div>
        </div>

        <!-- Panel: Bảo hành -->
        <div class="tab-panel" id="panel-warranty">
            <div class="section-title"><i class="fas fa-shield-check"></i> Yêu cầu bảo hành</div>
            <div id="warrantyList"><div class="empty-state"><i class="fas fa-circle-notch fa-spin"></i></div></div>
            <hr style="margin: 20px 0; border-color:#e5e7eb;">
            <div class="section-title" style="margin-top:0;">
                <i class="fas fa-plus-circle"></i> Gửi yêu cầu bảo hành mới
            </div>
            <div class="warranty-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Tên sản phẩm <span style="color:#ef4444">*</span></label>
                        <input type="text" id="wr_product" placeholder="VD: Camera LKSecure X200">
                    </div>
                    <div class="form-group">
                        <label>Số serial (nếu có)</label>
                        <input type="text" id="wr_serial" placeholder="LK-XXXXXX">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:16px;">
                    <label>Ngày mua</label>
                    <input type="date" id="wr_date" max="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group" style="margin-bottom:16px;">
                    <label>Mô tả lỗi / vấn đề <span style="color:#ef4444">*</span></label>
                    <textarea id="wr_issue" rows="4" style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;resize:vertical;box-sizing:border-box;font-family:inherit;" placeholder="Mô tả chi tiết sự cố bạn gặp phải..."></textarea>
                </div>
                <button class="btn-save" id="btnSubmitWarranty">
                    <i class="fas fa-paper-plane"></i> Gửi yêu cầu
                </button>
            </div>
        </div>

    </div><!-- /profile-container -->
</div><!-- /profile-page -->

<div class="profile-toast" id="pToast"></div>

<footer class="main-footer">
    <div class="footer-grid">
        <div class="footer-col"><h4>LKSecure</h4><p>Giải pháp an ninh thông minh cho mọi gia đình Việt.</p></div>
        <div class="footer-col"><h4>Liên kết</h4><ul>
            <li><a href="home.php">Trang chủ</a></li>
            <li><a href="products.php">Sản phẩm</a></li>
            <li><a href="about.php">Giới thiệu</a></li>
        </ul></div>
        <div class="footer-col"><h4>Hỗ trợ</h4><ul>
            <li><a href="warranty.php">Bảo hành</a></li>
            <li><a href="policy.php">Chính sách</a></li>
        </ul></div>
        <div class="footer-col"><h4>Liên hệ</h4><p>0393 860 031</p><p>ngo91168@gmail.com</p></div>
    </div>
    <div class="footer-bottom">© 2026 LKSecure</div>
</footer>

<script src="../assets/js/main.js"></script>
<script>
var USER_LOGGED_IN = true;
var LOGIN_URL = 'login.php';

// ── Toast ──────────────────────────────────────────────────
function toast(msg, ok) {
    var el = document.getElementById('pToast');
    el.textContent = msg;
    el.className = 'profile-toast show ' + (ok ? 'ok' : 'err');
    clearTimeout(el._t);
    el._t = setTimeout(function() { el.classList.remove('show'); }, 3200);
}

// ── Tab switching ──────────────────────────────────────────
document.querySelectorAll('.ptab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.ptab').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.tab-panel').forEach(function(p) { p.classList.remove('active'); });
        tab.classList.add('active');
        var panel = document.getElementById('panel-' + tab.dataset.tab);
        if (panel) panel.classList.add('active');
        // Lazy load
        if (tab.dataset.tab === 'address') loadAddresses();
        if (tab.dataset.tab === 'orders')  loadOrders();
        if (tab.dataset.tab === 'warranty') loadWarranty();
    });
});

// ── API helpers ────────────────────────────────────────────
function apiPost(url, data, cb) {
    var form = new FormData();
    Object.keys(data).forEach(function(k) { form.append(k, data[k]); });
    fetch(url, { method:'POST', body:form }).then(function(r) { return r.json(); }).then(cb)
    .catch(function() { toast('Lỗi kết nối máy chủ.', false); });
}
function apiGet(url, cb) {
    fetch(url).then(function(r) { return r.json(); }).then(cb)
    .catch(function() { toast('Lỗi kết nối máy chủ.', false); });
}
function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// ── Thông tin cá nhân ──────────────────────────────────────
document.getElementById('avatarFile').addEventListener('change', function() {
    var file = this.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var prev = document.getElementById('avatarPreview');
        prev.innerHTML = '<img id="avatarImg" src="' + e.target.result + '">';
    };
    reader.readAsDataURL(file);
});

document.getElementById('btnSaveInfo').addEventListener('click', function() {
    var name  = (document.getElementById('inf_name').value  || '').trim();
    var phone = (document.getElementById('inf_phone').value || '').trim();
    var email = (document.getElementById('inf_email').value || '').trim();
    var file  = document.getElementById('avatarFile').files[0];
    if (!name) { toast('Họ tên không được để trống!', false); return; }
    var btn = this; btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Đang lưu...';
    var postData = { action:'update_profile', full_name:name, phone:phone, email:email };
    if (file) postData['avatar'] = file;
    apiPost('../actions/user_action.php', postData, function(d) {
        toast(d.message || (d.success ? 'Đã lưu!' : 'Lỗi!'), d.success);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-floppy-disk"></i> Lưu thay đổi';
    });
});

document.getElementById('btnChangePass').addEventListener('click', function() {
    var newPass = (document.getElementById('new_pass').value || '').trim();
    var cfPass  = (document.getElementById('cf_pass').value  || '').trim();
    var oldPass = (document.getElementById('old_pass') ? document.getElementById('old_pass').value : '');
    if (!newPass) { toast('Vui lòng nhập mật khẩu mới!', false); return; }
    if (newPass.length < 6) { toast('Mật khẩu mới phải ít nhất 6 ký tự!', false); return; }
    if (newPass !== cfPass) { toast('Mật khẩu xác nhận không khớp!', false); return; }
    var btn = this; btn.disabled = true;
    apiPost('../actions/user_action.php', { action:'change_password', old_password:oldPass, new_password:newPass }, function(d) {
        toast(d.message, d.success);
        if (d.success) { document.getElementById('new_pass').value=''; document.getElementById('cf_pass').value=''; if(document.getElementById('old_pass')) document.getElementById('old_pass').value=''; }
        btn.disabled = false;
    });
});

// ── Địa chỉ — với dropdown tỉnh/phường tự động ──────────

// ---- Dữ liệu tỉnh/thành & quận/huyện/xã Việt Nam --------
// Nguồn: provinces.open-api.vn (build-in để offline)
var VN_PROVINCES = [
  "An Giang","Bà Rịa - Vũng Tàu","Bắc Giang","Bắc Kạn","Bạc Liêu","Bắc Ninh",
  "Bến Tre","Bình Định","Bình Dương","Bình Phước","Bình Thuận","Cà Mau",
  "Cần Thơ","Cao Bằng","Đà Nẵng","Đắk Lắk","Đắk Nông","Điện Biên","Đồng Nai",
  "Đồng Tháp","Gia Lai","Hà Giang","Hà Nam","Hà Nội","Hà Tĩnh","Hải Dương",
  "Hải Phòng","Hậu Giang","Hòa Bình","Hưng Yên","Khánh Hòa","Kiên Giang",
  "Kon Tum","Lai Châu","Lâm Đồng","Lạng Sơn","Lào Cai","Long An","Nam Định",
  "Nghệ An","Ninh Bình","Ninh Thuận","Phú Thọ","Phú Yên","Quảng Bình",
  "Quảng Nam","Quảng Ngãi","Quảng Ninh","Quảng Trị","Sóc Trăng","Sơn La",
  "Tây Ninh","Thái Bình","Thái Nguyên","Thanh Hóa","Thừa Thiên Huế",
  "Tiền Giang","TP. Hồ Chí Minh","Trà Vinh","Tuyên Quang","Vĩnh Long",
  "Vĩnh Phúc","Yên Bái"
];

// Dữ liệu quận/huyện theo tỉnh (subset phổ biến, gọi API cho đầy đủ)
var VN_DISTRICTS = {
  "Hà Nội":["Ba Đình","Hoàn Kiếm","Tây Hồ","Long Biên","Cầu Giấy","Đống Đa","Hai Bà Trưng","Hoàng Mai","Thanh Xuân","Hà Đông","Bắc Từ Liêm","Nam Từ Liêm","Sóc Sơn","Đông Anh","Gia Lâm","Thường Tín","Thanh Oai","Mỹ Đức","Ứng Hòa","Chương Mỹ","Đan Phượng","Hoài Đức","Phú Xuyên","Quốc Oai","Thạch Thất","Ba Vì","Mê Linh","Phúc Thọ","Sơn Tây"],
  "TP. Hồ Chí Minh":["Quận 1","Quận 3","Quận 4","Quận 5","Quận 6","Quận 7","Quận 8","Quận 10","Quận 11","Quận 12","Bình Thạnh","Gò Vấp","Phú Nhuận","Tân Bình","Tân Phú","Bình Tân","Thủ Đức","Bình Chánh","Hóc Môn","Củ Chi","Cần Giờ","Nhà Bè"],
  "Đà Nẵng":["Hải Châu","Thanh Khê","Sơn Trà","Ngũ Hành Sơn","Liên Chiểu","Cẩm Lệ","Hòa Vang","Hoàng Sa"],
  "Hải Phòng":["Hồng Bàng","Lê Chân","Ngô Quyền","Kiến An","Hải An","Đồ Sơn","Dương Kinh","An Dương","An Lão","Kiến Thụy","Tiên Lãng","Vĩnh Bảo","Cát Hải","Bạch Long Vĩ"],
  "Cần Thơ":["Ninh Kiều","Ô Môn","Bình Thuỷ","Cái Răng","Thốt Nốt","Vĩnh Thạnh","Cờ Đỏ","Phong Điền","Thới Lai"]
};

// Phường/xã theo quận/huyện (subset Hà Nội + HCM)
var VN_WARDS = {
  "Cầu Giấy":["Dịch Vọng","Dịch Vọng Hậu","Mai Dịch","Mỹ Đình 1","Mỹ Đình 2","Nghĩa Đô","Nghĩa Tân","Quan Hoa","Trung Hoà","Yên Hoà"],
  "Đống Đa":["Cát Linh","Hàng Bột","Khâm Thiên","Kim Liên","Láng Hạ","Láng Thượng","Nam Đồng","Ngã Tư Sở","Ô Chợ Dừa","Phương Liên","Phương Mai","Quốc Tử Giám","Thịnh Quang","Thổ Quan","Trung Liệt","Trung Phụng","Trung Tự","Văn Chương","Văn Miếu"],
  "Hai Bà Trưng":["Bách Khoa","Bạch Đằng","Bạch Mai","Bùi Thị Xuân","Cầu Dền","Đồng Nhân","Đồng Tâm","Lê Đại Hành","Minh Khai","Nguyễn Du","Phạm Đình Hổ","Phố Huế","Thanh Lương","Thanh Nhàn","Trương Định","Vĩnh Tuy","Quỳnh Lôi","Quỳnh Mai"],
  "Ba Đình":["Cống Vị","Điện Biên","Đội Cấn","Giảng Võ","Kim Mã","Liễu Giai","Ngọc Hà","Ngọc Khánh","Nguyễn Trung Trực","Phúc Xá","Quán Thánh","Thành Công","Trúc Bạch","Vĩnh Phúc"],
  "Hoàn Kiếm":["Chương Dương","Cửa Đông","Cửa Nam","Đồng Xuân","Hàng Bạc","Hàng Bài","Hàng Bồ","Hàng Buồm","Hàng Đào","Hàng Gai","Hàng Mã","Hàng Trống","Lý Thái Tổ","Phan Chu Trinh","Phúc Tân","Tràng Tiền","Trần Hưng Đạo"],
  "Tây Hồ":["Bưởi","Nhật Tân","Phú Thượng","Quảng An","Tứ Liên","Xuân La","Yên Phụ","Thụy Khuê"],
  "Thanh Xuân":["Hạ Đình","Khương Đình","Khương Mai","Khương Trung","Kim Giang","Nhân Chính","Phương Liệt","Thanh Xuân Bắc","Thanh Xuân Nam","Thanh Xuân Trung"],
  "Hoàng Mai":["Đại Kim","Định Công","Giáp Bát","Hoàng Liệt","Hoàng Văn Thụ","Lĩnh Nam","Mai Động","Tân Mai","Thanh Trì","Thịnh Liệt","Trần Phú","Tương Mai","Vĩnh Hưng","Yên Sở"],
  "Hà Đông":["Biên Giang","Đồng Mai","Dương Nội","Hà Cầu","La Khê","Mộ Lao","Nguyễn Trãi","Phú La","Phú Lãm","Phú Lương","Phúc La","Quang Trung","Vạn Phúc","Văn Quán","Yên Nghĩa","Yết Kiêu"],
  "Quận 1":["Bến Nghé","Bến Thành","Cầu Kho","Cầu Ông Lãnh","Cô Giang","Đa Kao","Nguyễn Cư Trinh","Nguyễn Thái Bình","Phạm Ngũ Lão","Tân Định"],
  "Quận 7":["Bình Thuận","Phú Mỹ","Phú Thuận","Tân Hưng","Tân Kiểng","Tân Phong","Tân Phú","Tân Quy","Tân Thuận Đông","Tân Thuận Tây"],
  "Bình Thạnh":["An Bình","Đông Xương","Hiệp Bình","Phú Lợi","Phường 1","Phường 2","Phường 3","Phường 5","Phường 6","Phường 7","Phường 11","Phường 12","Phường 13","Phường 14","Phường 15","Phường 17","Phường 19","Phường 21","Phường 22","Phường 24","Phường 25","Phường 26","Phường 27","Phường 28"],
  "Thủ Đức":["An Khánh","An Lợi Đông","An Phú","Bình Chiểu","Bình Thọ","Bình Trưng Đông","Bình Trưng Tây","Cát Lái","Hiệp Bình Chánh","Hiệp Bình Phước","Hiệp Phú","Linh Chiểu","Linh Đông","Linh Tây","Linh Trung","Linh Xuân","Long Bình","Long Phước","Long Thạnh Mỹ","Long Trường","Phú Hữu","Phước Bình","Phước Long A","Phước Long B","Tăng Nhơn Phú A","Tăng Nhơn Phú B","Tam Bình","Tam Phú","Thảo Điền","Thủ Thiêm","Trường Thạnh","Trường Thọ"],
  "Ninh Kiều":["An Bình","An Cư","An Hòa","An Khánh","An Lạc","An Nghiệp","An Phú","Cái Khế","Hưng Lợi","Tân An","Xuân Khánh"],
  "Hải Châu":["Bình Hiên","Bình Thuận","Hải Châu 1","Hải Châu 2","Hòa Cường Bắc","Hòa Cường Nam","Hòa Thuận Đông","Hòa Thuận Tây","Nam Dương","Phước Ninh","Thạch Thang","Thanh Bình","Thuận Phước"]
};

function getDistrictsOf(province) {
  if (VN_DISTRICTS[province]) return VN_DISTRICTS[province];
  // Generic fallback
  return ["Huyện 1","Huyện 2","Huyện 3","Quận trung tâm","Thị trấn trung tâm"];
}
function getWardsOf(district) {
  if (VN_WARDS[district]) return VN_WARDS[district];
  return ["Phường 1","Phường 2","Phường 3","Xã Trung Tâm","Thị trấn"];
}

function buildSelect(id, opts, val, placeholder, onchange) {
  var s = '<select id="' + id + '" style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;box-sizing:border-box;background:#fff;color:#374151;"' + (onchange ? ' onchange="' + onchange + '"' : '') + '>';
  s += '<option value="">' + placeholder + '</option>';
  opts.forEach(function(o) {
    s += '<option value="' + o + '"' + (o === val ? ' selected' : '') + '>' + o + '</option>';
  });
  s += '</select>';
  return s;
}

var _addresses = [];
function loadAddresses() {
    document.getElementById('addrContent').innerHTML = '<div class="empty-state"><i class="fas fa-circle-notch fa-spin"></i></div>';
    // Sử dụng POST để tránh cache
    apiPost('../actions/user_action.php', {action:'get'}, function(data) {
        if (data.success) { _addresses = data.addresses || []; renderAddresses(); }
        else document.getElementById('addrContent').innerHTML = '<p style="color:#dc2626">Không thể tải địa chỉ.</p>';
    });
}
function renderAddresses() {
    var html = '<div class="addr-list">';
    if (_addresses.length === 0) {
        html += '<div class="empty-state"><i class="fas fa-map-marker-alt"></i><p>Chưa có địa chỉ nào</p></div>';
    }
    _addresses.forEach(function(a) {
        var loc = [a.address, a.ward, a.district, a.province].filter(Boolean).join(', ');
        html += '<div class="addr-card' + (a.is_default ? ' default' : '') + '">' +
            '<div class="addr-top"><div>' +
                (a.is_default ? '<span class="default-badge"><i class="fas fa-star"></i> Mặc định</span> ' : '') +
                (a.recipient_name ? '<strong>' + esc(a.recipient_name) + '</strong>' + (a.recipient_phone ? ' · ' + esc(a.recipient_phone) : '') + '<br>' : '') +
                '<p class="addr-text">' + esc(loc) + '</p>' +
            '</div><div class="addr-actions">' +
                '<button class="btn-sm primary" onclick="showAddrForm(' + a.id + ')"><i class="fas fa-pen"></i> Sửa</button>' +
                (!a.is_default ? '<button class="btn-sm" onclick="setDefault(' + a.id + ')"><i class="fas fa-star"></i></button>' : '') +
                (!a.is_default ? '<button class="btn-sm danger" onclick="deleteAddr(' + a.id + ')"><i class="fas fa-trash"></i></button>' : '') +
            '</div></div></div>';
    });
    html += '</div><button class="btn-add-addr" onclick="showAddrForm(null)"><i class="fas fa-plus"></i> Thêm địa chỉ mới</button>';
    document.getElementById('addrContent').innerHTML = html;
    document.getElementById('addrFormWrap').style.display = 'none';
    document.getElementById('addrFormWrap').innerHTML = '';
}

window.showAddrForm = function(id) {
    var a = id ? _addresses.find(function(x) { return x.id == id; }) : null;
    var prov = a ? (a.province || '') : '';
    var dist = a ? (a.district || '') : '';
    var ward = a ? (a.ward || '') : '';

    var distOpts = prov ? getDistrictsOf(prov) : [];
    var wardOpts = dist ? getWardsOf(dist) : [];

    var html = '<div class="addr-form">' +
        '<div class="addr-form-title">' + (id ? '<i class="fas fa-pen"></i> Sửa địa chỉ' : '<i class="fas fa-plus"></i> Thêm địa chỉ mới') + '</div>' +

        // Họ tên + SĐT
        '<div class="form-row">' +
            '<div class="form-group"><label>Họ và tên <span style="color:#ef4444">*</span></label>' +
                '<input id="af_name" value="' + esc(a ? (a.recipient_name||'') : '') + '" placeholder="Nguyễn Văn A" style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;box-sizing:border-box;"></div>' +
            '<div class="form-group"><label>Số điện thoại <span style="color:#ef4444">*</span></label>' +
                '<input id="af_phone" value="' + esc(a ? (a.recipient_phone||'') : '') + '" placeholder="0912 345 678" style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;box-sizing:border-box;"></div>' +
        '</div>' +

        // Tỉnh/thành + Quận/huyện
        '<div class="form-row">' +
            '<div class="form-group"><label>Tỉnh / Thành phố <span style="color:#ef4444">*</span></label>' +
                buildSelect('af_province', VN_PROVINCES, prov, '-- Chọn tỉnh/thành --', 'addrProvinceChange()') +
            '</div>' +
            '<div class="form-group"><label>Quận / Huyện <span style="color:#ef4444">*</span></label>' +
                buildSelect('af_district', distOpts, dist, '-- Chọn quận/huyện --', 'addrDistrictChange()') +
            '</div>' +
        '</div>' +

        // Phường/xã + Địa chỉ chi tiết
        '<div class="form-row">' +
            '<div class="form-group"><label>Phường / Xã <span style="color:#ef4444">*</span></label>' +
                buildSelect('af_ward', wardOpts, ward, '-- Chọn phường/xã --', '') +
            '</div>' +
            '<div class="form-group"><label>Địa chỉ chi tiết <span style="color:#ef4444">*</span></label>' +
                '<input id="af_detail" value="' + esc(a ? (a.address||'') : '') + '" placeholder="Số nhà, tên đường..." style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;box-sizing:border-box;"></div>' +
        '</div>' +

        '<div style="display:flex;gap:10px;margin-top:8px;">' +
            '<button class="btn-sm" onclick="renderAddresses()"><i class="fas fa-arrow-left"></i> Quay lại</button>' +
            '<button class="btn-save" id="btnSaveAddr" style="margin:0;" data-id="' + (id||'') + '">' +
                '<i class="fas fa-location-dot"></i> ' + (id ? 'Cập nhật' : 'Lưu địa chỉ') +
            '</button>' +
        '</div></div>';

    // Hiển thị form bên dưới danh sách
    document.getElementById('addrContent').innerHTML = html;
    document.getElementById('btnSaveAddr').addEventListener('click', saveAddr);
};

// Khi đổi tỉnh → cập nhật dropdown quận
window.addrProvinceChange = function() {
    var prov = document.getElementById('af_province').value;
    var dists = prov ? getDistrictsOf(prov) : [];
    var sel = document.getElementById('af_district');
    sel.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
    dists.forEach(function(d) { sel.innerHTML += '<option value="' + d + '">' + d + '</option>'; });
    // Reset phường/xã
    var wardSel = document.getElementById('af_ward');
    wardSel.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
};

// Khi đổi quận → cập nhật dropdown phường/xã
window.addrDistrictChange = function() {
    var dist = document.getElementById('af_district').value;
    var wards = dist ? getWardsOf(dist) : [];
    var sel = document.getElementById('af_ward');
    sel.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
    wards.forEach(function(w) { sel.innerHTML += '<option value="' + w + '">' + w + '</option>'; });
};

function saveAddr() {
    var id   = this.dataset.id;
    var name = (document.getElementById('af_name').value||'').trim();
    var ph   = (document.getElementById('af_phone').value||'').trim();
    var prov = (document.getElementById('af_province').value||'').trim();
    var dist = (document.getElementById('af_district').value||'').trim();
    var ward = (document.getElementById('af_ward').value||'').trim();
    var det  = (document.getElementById('af_detail').value||'').trim();

    if (!name) { toast('Vui lòng nhập họ tên!', false); return; }
    if (!ph)   { toast('Vui lòng nhập số điện thoại!', false); return; }
    if (!prov) { toast('Vui lòng chọn tỉnh/thành phố!', false); return; }
    if (!dist) { toast('Vui lòng chọn quận/huyện!', false); return; }
    if (!ward) { toast('Vui lòng chọn phường/xã!', false); return; }
    if (!det)  { toast('Vui lòng nhập địa chỉ chi tiết!', false); return; }

    var btn = document.getElementById('btnSaveAddr');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Đang lưu...';

    apiPost('../actions/user_action.php', {
        action:          id ? 'update_address' : 'add_address',
        address_id:      id,
        recipient_name:  name,
        recipient_phone: ph,
        province:        prov,
        district:        dist,
        ward:            ward,
        address:         det
    }, function(d) {
        toast(d.message || (d.success ? 'Đã lưu!' : 'Lỗi không xác định!'), d.success);
        if (d.success) {
            loadAddresses();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-location-dot"></i> ' + (id ? 'Cập nhật' : 'Lưu địa chỉ');
        }
    });
}

window.setDefault = function(id) {
    apiPost('../actions/user_action.php', { action:'set_default_address', address_id:id }, function(d) {
        toast(d.message, d.success); if (d.success) loadAddresses();
    });
};
window.deleteAddr = function(id) {
    if (!confirm('Xóa địa chỉ này?')) return;
    apiPost('../actions/user_action.php', { action:'delete_address', address_id:id }, function(d) {
        toast(d.message, d.success); if (d.success) loadAddresses();
    });
};

// ── Đơn hàng ──────────────────────────────────────────────
var _orders = [];
var STATUS_LABEL = { pending:'Chờ xác nhận', confirmed:'Đã xác nhận', processing:'Đang xử lý', shipping:'Đang giao', delivered:'Đã giao', cancelled:'Đã huỷ' };
var STATUS_COLOR = { pending:'#f59e0b', confirmed:'#8b5cf6', processing:'#3b82f6', shipping:'#3b82f6', delivered:'#10b981', cancelled:'#ef4444' };

function loadOrders() {
    document.getElementById('ordersContent').innerHTML = '<div class="empty-state"><i class="fas fa-circle-notch fa-spin"></i></div>';
    apiGet('../actions/order_action.php?action=list', function(data) {
        if (data.success) { _orders = data.orders||[]; renderOrders('all'); }
        else document.getElementById('ordersContent').innerHTML = '<p style="color:#dc2626">Không thể tải đơn hàng.</p>';
    });
}
function renderOrders(filter) {
    var list = filter==='all' ? _orders : _orders.filter(function(o){ return o.status===filter; });
    if (!list.length) {
        document.getElementById('ordersContent').innerHTML = '<div class="empty-state"><i class="fas fa-box-open"></i><p>Không có đơn hàng</p><a href="products.php" class="btn-save" style="text-decoration:none;display:inline-flex;"><i class="fas fa-shopping-bag"></i> Mua sắm ngay</a></div>';
        return;
    }
    var html = '';
    list.forEach(function(o) {
        var color = STATUS_COLOR[o.status]||'#8b5cf6';
        var label = STATUS_LABEL[o.status]||o.status;
        var itemsHtml = '';
        if (o.items && o.items.length) {
            o.items.slice(0,3).forEach(function(it) {
                itemsHtml += '<div class="order-item-row"><span>' + esc(it.product_name) + ' x' + it.quantity + '</span><span>' + Number(it.price||it.unit_price).toLocaleString('vi-VN') + 'đ</span></div>';
            });
            if (o.items.length > 3) itemsHtml += '<p style="font-size:12px;color:#9ca3af;margin:4px 0 0;">+ ' + (o.items.length-3) + ' sản phẩm khác</p>';
        }
        var cancelBtn = (o.status==='pending'||o.status==='confirmed')
            ? '<button class="btn-cancel" onclick="cancelOrder(' + o.id + ',\'' + esc(o.order_code) + '\')"><i class="fas fa-ban"></i> Huỷ</button>' : '';
        html += '<div class="order-card">' +
            '<div class="order-head">' +
                '<div><span class="order-code-span"><i class="fas fa-hashtag"></i> ' + esc(o.order_code) + '</span><span class="order-date-span">' + esc(o.created_at||'') + '</span></div>' +
                '<span class="status-badge" style="background:' + color + '18;color:' + color + ';border:1px solid ' + color + '40;">' + label + '</span>' +
            '</div>' +
            '<div class="order-items-list">' + itemsHtml + '</div>' +
            '<div class="order-foot">' +
                '<div style="font-size:13px;color:#6b7280;"><i class="fas fa-map-marker-alt"></i> ' + esc(o.address||'') + '</div>' +
                '<div style="display:flex;align-items:center;gap:12px;">' +
                    '<span class="order-total">' + Number(o.total).toLocaleString('vi-VN') + 'đ</span>' +
                    cancelBtn +
                '</div>' +
            '</div></div>';
    });
    document.getElementById('ordersContent').innerHTML = html;
}

window.filterOrders = function(filter, btn) {
    document.querySelectorAll('.otab').forEach(function(t) { t.classList.remove('active'); });
    if (btn) btn.classList.add('active');
    renderOrders(filter);
};

window.cancelOrder = function(id, code) {
    var reason = prompt('Lý do hủy đơn #' + code + ':', '');
    if (reason === null) return;
    reason = reason.trim() || 'Khách hàng yêu cầu hủy';
    apiPost('../actions/order_action.php', { action:'cancel', order_id:id, reason:reason }, function(d) {
        toast(d.message||(d.success?'Đã huỷ!':'Lỗi!'), d.success);
        if (d.success) loadOrders();
    });
};

// ── Bảo hành ──────────────────────────────────────────────
function loadWarranty() {
    document.getElementById('warrantyList').innerHTML = '<div class="empty-state"><i class="fas fa-circle-notch fa-spin"></i></div>';
    apiGet('../actions/warranty_action.php?action=my_list', function(data) {
        if (!data.success || !data.requests.length) {
            document.getElementById('warrantyList').innerHTML = '<div class="empty-state"><i class="fas fa-shield-check"></i><p>Chưa có yêu cầu bảo hành nào</p></div>';
            return;
        }
        var html = '<div class="wr-list">';
        data.requests.forEach(function(r) {
            html += '<div class="wr-card">' +
                '<div class="wr-head">' +
                    '<span class="wr-name">' + esc(r.product_name) + '</span>' +
                    '<span class="status-badge" style="background:' + r.status_color + '18;color:' + r.status_color + ';border:1px solid ' + r.status_color + '40;">' + esc(r.status_label) + '</span>' +
                '</div>' +
                '<p class="wr-desc">' + esc(r.issue_desc) + '</p>' +
                (r.admin_note ? '<p class="wr-note"><i class="fas fa-comment-dots"></i> ' + esc(r.admin_note) + '</p>' : '') +
                '<p class="wr-meta">Ngày gửi: ' + esc(r.created_at) + (r.serial_number ? ' · Serial: ' + esc(r.serial_number) : '') + '</p>' +
            '</div>';
        });
        html += '</div>';
        document.getElementById('warrantyList').innerHTML = html;
    });
}

document.getElementById('btnSubmitWarranty').addEventListener('click', function() {
    var product = (document.getElementById('wr_product').value||'').trim();
    var serial  = (document.getElementById('wr_serial').value||'').trim();
    var date    = (document.getElementById('wr_date').value||'').trim();
    var issue   = (document.getElementById('wr_issue').value||'').trim();
    if (!product||!issue) { toast('Vui lòng điền tên sản phẩm và mô tả lỗi!', false); return; }
    var btn = this; btn.disabled=true; btn.innerHTML='<i class="fas fa-circle-notch fa-spin"></i> Đang gửi...';
    apiPost('../actions/warranty_action.php', { action:'submit', product_name:product, serial_number:serial, purchase_date:date, issue_desc:issue }, function(d) {
        toast(d.message||(d.success?'Đã gửi!':'Lỗi!'), d.success);
        if (d.success) {
            document.getElementById('wr_product').value='';
            document.getElementById('wr_serial').value='';
            document.getElementById('wr_date').value='';
            document.getElementById('wr_issue').value='';
            loadWarranty();
        }
        btn.disabled=false; btn.innerHTML='<i class="fas fa-paper-plane"></i> Gửi yêu cầu';
    });
});
</script>
</body>
</html>
