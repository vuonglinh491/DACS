<?php
// ============================================================
//  LKSecure — Nav Partial
//  Include trong <div class="nav-actions"> của mọi trang
//  Tự phục hồi session từ cookie "Ghi nhớ đăng nhập"
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth_check.php'; // Session timeout check

// Khôi phục từ cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user_id'])) {
    $_SESSION['user_id']    = $_COOKIE['remember_user_id'];
    $_SESSION['user_email'] = $_COOKIE['remember_user_email'] ?? '';
    $_SESSION['user_name']  = $_COOKIE['remember_user_name']  ?? '';
    $_SESSION['user_role']  = $_COOKIE['remember_user_role']  ?? 'customer';
}

$_auth_logged_in    = isset($_SESSION['user_id']);
$_auth_user_name    = htmlspecialchars($_SESSION['user_name']  ?? '');
$_auth_user_email   = htmlspecialchars($_SESSION['user_email'] ?? '');
$_auth_user_role    = $_SESSION['user_role'] ?? 'customer';
$_auth_initial      = $_auth_user_name
    ? mb_strtoupper(mb_substr($_auth_user_name, 0, 1, 'UTF-8'), 'UTF-8') : '?';
$_auth_role_label   = $_auth_user_role === 'admin' ? 'Quản trị viên' : 'Khách hàng';

// Lấy avatar từ DB nếu chưa có trong session
$_auth_avatar = $_SESSION['user_avatar'] ?? '';
if ($_auth_logged_in && empty($_auth_avatar)) {
    // Lazy-load avatar từ DB lần đầu
    if (isset($conn) && $conn) {
        $avStmt = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
        if ($avStmt) {
            $avStmt->bind_param("i", $_SESSION['user_id']);
            $avStmt->execute();
            $avRow = $avStmt->get_result()->fetch_assoc();
            $avStmt->close();
            $_auth_avatar = $avRow['avatar'] ?? '';
            $_SESSION['user_avatar'] = $_auth_avatar;
        }
    }
}
// Xác định src avatar: URL đầy đủ (OAuth) hoặc path local
$_auth_avatar_src = '';
if ($_auth_avatar) {
    $_auth_avatar_src = (str_starts_with($_auth_avatar, 'http://') || str_starts_with($_auth_avatar, 'https://'))
        ? htmlspecialchars($_auth_avatar)
        : '../assets/imgs/avatars/' . htmlspecialchars($_auth_avatar);
}
?>

<?php if ($_auth_logged_in): ?>
<!-- ══ ICON ĐƠN HÀNG ══ -->
<button class="icon-btn orders-icon-btn" id="navOrdersIconBtn" title="Đơn hàng của tôi" style="position:relative;">
    <i class="fas fa-receipt"></i>
    <span class="orders-badge" id="ordersBadge" style="display:none;position:absolute;top:-6px;right:-6px;background:#f59e0b;color:#fff;font-size:10px;font-weight:700;min-width:18px;height:18px;border-radius:9px;align-items:center;justify-content:center;padding:0 4px;pointer-events:none;line-height:1;"></span>
</button>

<!-- ══ NAV USER TRIGGER ══ -->
<div class="nav-user" id="navUser">
    <div class="nav-user-trigger" id="navUserTrigger">
        <div class="nav-avatar" id="navAvatarEl">
            <?php if ($_auth_avatar_src): ?>
                <img src="<?= $_auth_avatar_src ?>" class="nav-avatar-img" alt="avatar"
                     onerror="this.parentElement.innerHTML='<?= $_auth_initial ?>'">
            <?php else: ?>
                <?= $_auth_initial ?>
            <?php endif; ?>
        </div>
        <span class="nav-user-name"><?= $_auth_user_name ?></span>
        <i class="fas fa-chevron-down nav-chevron"></i>
    </div>

    <div class="nav-user-dropdown" id="navUserDropdown">
        <!-- Header -->
        <div class="nav-dropdown-header">
            <div class="dd-avatar" id="ddAvatarEl">
                <?php if ($_auth_avatar_src): ?>
                    <img src="<?= $_auth_avatar_src ?>" class="dd-avatar-img" alt="avatar"
                         onerror="this.parentElement.innerHTML='<?= $_auth_initial ?>'">
                <?php else: ?>
                    <?= $_auth_initial ?>
                <?php endif; ?>
            </div>
            <div class="dd-info">
                <div class="dd-name"><?= $_auth_user_name ?></div>
                <div class="dd-email"><?= $_auth_user_email ?></div>
                <div class="dd-role">
                    <?php if ($_auth_user_role === 'admin'): ?>
                        <i class="fas fa-shield-halved"></i> <?= $_auth_role_label ?>
                    <?php else: ?>
                        <i class="fas fa-user"></i> <?= $_auth_role_label ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Menu items -->
        <div class="nav-dropdown-body">
            <?php if ($_auth_user_role === 'admin'): ?>
            <a href="admin.php" class="nav-dropdown-item admin-item">
                <i class="fas fa-gauge-high"></i>
                <span>Trang quản trị</span>
                <i class="fas fa-arrow-right dd-arrow"></i>
            </a>
            <div class="nav-dropdown-divider"></div>
            <?php endif; ?>

            <a href="profile.php" class="nav-dropdown-item">
                <i class="fas fa-user-circle"></i>
                <span>Tài khoản của tôi</span>
            </a>
            <a href="#" class="nav-dropdown-item" id="ddBtnAddress">
                <i class="fas fa-location-dot"></i>
                <span>Địa chỉ nhận hàng</span>
            </a>
            <a href="#" class="nav-dropdown-item" id="ddBtnOrders">
                <i class="fas fa-clipboard-list"></i>
                <span>Đơn hàng của tôi</span>
            </a>
        </div>

        <div class="nav-dropdown-divider"></div>
        <a href="../actions/logout.php" class="nav-dropdown-item logout-item">
            <i class="fas fa-right-from-bracket"></i>
            <span>Đăng xuất</span>
        </a>
    </div>
</div>

<?php else: ?>
<a href="login.php" class="btn-login-nav">
    <i class="fa-regular fa-user"></i> Đăng nhập
</a>
<?php endif; ?>


<?php if ($_auth_logged_in): ?>
<!-- ══════════════════════════════════════════════════════════
     MODALS — injected directly into <body> via JS to avoid
     backdrop-filter stacking context breaking position:fixed
════════════════════════════════════════════════════════════ -->
<script>
(function () {
    var modalsHtml =
        /* MODAL: THÔNG TIN CÁ NHÂN */
        '<div class="lk-modal-overlay" id="modalProfile">' +
            '<div class="lk-modal">' +
                '<div class="lk-modal-header">' +
                    '<div class="lk-modal-title"><i class="fas fa-user-circle"></i> Thông tin cá nhân</div>' +
                    '<button class="lk-modal-close" id="closeModalProfile"><i class="fas fa-times"></i></button>' +
                '</div>' +
                '<div class="lk-modal-body" id="profileBody">' +
                    '<div class="lk-loading"><i class="fas fa-circle-notch fa-spin"></i><p>Đang tải...</p></div>' +
                '</div>' +
            '</div>' +
        '</div>' +

        /* MODAL: ĐỊA CHỈ NHẬN HÀNG */
        '<div class="lk-modal-overlay" id="modalAddress">' +
            '<div class="lk-modal">' +
                '<div class="lk-modal-header">' +
                    '<div class="lk-modal-title"><i class="fas fa-location-dot"></i> Địa chỉ nhận hàng</div>' +
                    '<button class="lk-modal-close" id="closeModalAddress"><i class="fas fa-times"></i></button>' +
                '</div>' +
                '<div class="lk-modal-body" id="addressBody">' +
                    '<div class="lk-loading"><i class="fas fa-circle-notch fa-spin"></i><p>Đang tải...</p></div>' +
                '</div>' +
            '</div>' +
        '</div>' +

        /* MODAL: GIỎ HÀNG */
        '<div class="lk-modal-overlay" id="modalCart">' +
            '<div class="lk-modal lk-modal-wide">' +
                '<div class="lk-modal-header">' +
                    '<div class="lk-modal-title"><i class="fas fa-shopping-cart"></i> Giỏ hàng của tôi</div>' +
                    '<button class="lk-modal-close" id="closeModalCart"><i class="fas fa-times"></i></button>' +
                '</div>' +
                '<div class="lk-modal-body" id="cartBody">' +
                    '<div class="lk-loading"><i class="fas fa-circle-notch fa-spin"></i><p>Đang tải...</p></div>' +
                '</div>' +
            '</div>' +
        '</div>' +

        /* MODAL: ĐƠN HÀNG */
        '<div class="lk-modal-overlay" id="modalOrders">' +
            '<div class="lk-modal lk-modal-wide">' +
                '<div class="lk-modal-header">' +
                    '<div class="lk-modal-title"><i class="fas fa-clipboard-list"></i> Đơn hàng của tôi</div>' +
                    '<button class="lk-modal-close" id="closeModalOrders"><i class="fas fa-times"></i></button>' +
                '</div>' +
                '<div class="lk-modal-body" id="ordersBody">' +
                    '<div class="lk-loading"><i class="fas fa-circle-notch fa-spin"></i><p>Đang tải...</p></div>' +
                '</div>' +
            '</div>' +
        '</div>' +

        /* TOAST */
        '<div class="lk-toast" id="lkToast">' +
            '<i class="lk-toast-icon" id="lkToastIcon"></i>' +
            '<span id="lkToastMsg"></span>' +
        '</div>';

    /* Append to <body> so position:fixed is relative to viewport,
       not trapped inside navbar's backdrop-filter stacking context */
    var wrapper = document.createElement('div');
    wrapper.id = 'lk-modals-root';
    wrapper.innerHTML = modalsHtml;
    document.body.appendChild(wrapper);
})();
</script>

<!-- ══════════════════════════════════════════════════════════
     SCRIPT — Dropdown · Modals · Profile · Address · Orders · Cart
════════════════════════════════════════════════════════════ -->
<script>
(function () {
'use strict';

/* ─── 1. DROPDOWN ─────────────────────────────────────────── */
var navUser    = document.getElementById('navUser');
var navTrigger = document.getElementById('navUserTrigger');
if (navUser && navTrigger) {
    navTrigger.addEventListener('click', function (e) {
        e.stopPropagation();
        navUser.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
        if (navUser && !navUser.contains(e.target)) navUser.classList.remove('open');
    });
    navUser.querySelectorAll('.nav-dropdown-item').forEach(function (item) {
        item.addEventListener('click', function () { navUser.classList.remove('open'); });
    });
}

/* ─── 1b. ICON ĐƠN HÀNG TRÊN NAVBAR ──────────────────────── */
var navOrdersBtn = document.getElementById('navOrdersIconBtn');
if (navOrdersBtn) {
    navOrdersBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        openModal('modalOrders');
        loadOrders('all');
    });
}
/* Tải số đơn đang chờ để hiện badge */
(function loadOrdersBadge() {
    apiGet('../actions/order_action.php?action=list', function (data) {
        if (!data.success) return;
        var pending = (data.orders || []).filter(function (o) {
            return o.status === 'pending' || o.status === 'confirmed';
        }).length;
        var badge = document.getElementById('ordersBadge');
        if (badge && pending > 0) {
            badge.textContent = pending;
            badge.style.display = 'flex';
        }
    });
})();

/* ─── 2. TOAST ───────────────────────────────────────────── */
function showToast(msg, ok) {
    var toast = document.getElementById('lkToast');
    var icon  = document.getElementById('lkToastIcon');
    var text  = document.getElementById('lkToastMsg');
    if (!toast) return;
    icon.className = 'lk-toast-icon ' + (ok ? 'ok fas fa-check-circle' : 'err fas fa-triangle-exclamation');
    toast.classList.toggle('error', !ok);
    text.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toast._t);
    toast._t = setTimeout(function () { toast.classList.remove('show'); }, 3200);
}

/* ─── 3. MODAL HELPERS ───────────────────────────────────── */
var MODAL_IDS = ['modalProfile','modalAddress','modalCart','modalOrders'];
function openModal(id)  { var el = document.getElementById(id); if (el) el.classList.add('open'); }
function closeModal(id) { var el = document.getElementById(id); if (el) el.classList.remove('open'); }

MODAL_IDS.forEach(function (id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('click', function (e) { if (e.target === el) closeModal(id); });
});
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') MODAL_IDS.forEach(closeModal); });
var CLOSE_MAP = {
    closeModalProfile:'modalProfile', closeModalAddress:'modalAddress',
    closeModalCart:'modalCart', closeModalOrders:'modalOrders'
};
Object.keys(CLOSE_MAP).forEach(function (btnId) {
    var btn = document.getElementById(btnId);
    if (btn) btn.addEventListener('click', function () { closeModal(CLOSE_MAP[btnId]); });
});

/* ─── 4. API HELPERS ─────────────────────────────────────── */
function apiPost(url, data, cb) {
    var form = new FormData();
    Object.keys(data).forEach(function (k) { form.append(k, data[k]); });
    fetch(url, { method:'POST', body:form })
        .then(function (r) { return r.json(); })
        .then(cb)
        .catch(function () { showToast('Lỗi kết nối máy chủ.', false); });
}
function apiGet(url, cb) {
    fetch(url)
        .then(function (r) { return r.json(); })
        .then(cb)
        .catch(function () { showToast('Lỗi kết nối máy chủ.', false); });
}
function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function loading(bodyId) {
    document.getElementById(bodyId).innerHTML =
        '<div class="lk-loading"><i class="fas fa-circle-notch fa-spin"></i><p>Đang tải...</p></div>';
}

/* ─── 5. THÔNG TIN CÁ NHÂN ──────────────────────────────── */
var btnProfile = document.getElementById('ddBtnProfile');
if (btnProfile) {
    btnProfile.addEventListener('click', function (e) {
        e.preventDefault();
        openModal('modalProfile');
        loading('profileBody');
        apiGet('../actions/user_action.php?action=get', function (data) {
            if (data.success) renderProfileForm(data.user);
            else document.getElementById('profileBody').innerHTML =
                '<p class="lk-err-msg">Không thể tải dữ liệu.</p>';
        });
    });
}

function renderProfileForm(u) {
    var isOAuth = !!(u.oauth_provider);
    var missingPhone = isOAuth && !u.phone;
    var missingEmail = isOAuth && (!u.email || u.email.endsWith('@noemail.local'));

    var avatarHtml = '';
    if (u.avatar) {
        var avatarSrc = (u.avatar.startsWith('http://') || u.avatar.startsWith('https://'))
            ? u.avatar
            : '../assets/imgs/avatars/' + escHtml(u.avatar);
        avatarHtml =
            '<div class="pf-avatar-wrap">' +
            '<img src="' + avatarSrc + '" class="pf-avatar-img" ' +
            'onerror="this.parentElement.style.display=\'none\'">' +
            '</div>';
    } else {
        avatarHtml =
            '<div class="pf-avatar-wrap">' +
            '<div class="pf-avatar-initials">' + escHtml('<?= $_auth_initial ?>') + '</div>' +
            '</div>';
    }

    var oauthNote = isOAuth
        ? '<div class="lk-oauth-badge ' + u.oauth_provider + '">' +
          '<i class="fab fa-' + u.oauth_provider + '"></i> Tài khoản ' +
          (u.oauth_provider === 'google' ? 'Google' : 'Facebook') + '</div>'
        : '';

    var missingList = [missingPhone ? 'số điện thoại' : '', missingEmail ? 'email liên hệ' : ''].filter(Boolean).join(' và ');
    var completeBanner = (missingPhone || missingEmail)
        ? '<div class="lk-complete-banner">' +
            '<div class="lk-complete-banner-icon"><i class="fas fa-circle-exclamation"></i></div>' +
            '<div>' +
              '<div class="lk-complete-banner-title">Hoàn thiện thông tin tài khoản</div>' +
              '<div class="lk-complete-banner-sub">Bổ sung ' + missingList + ' để sử dụng đầy đủ tính năng.</div>' +
            '</div>' +
          '</div>'
        : '';

    document.getElementById('profileBody').innerHTML =
        oauthNote +
        completeBanner +
        avatarHtml +
        '<div class="lk-upload-wrap">' +
        '<label class="lk-upload-label" for="pf_avatar"><i class="fas fa-camera"></i> Đổi ảnh đại diện</label>' +
        '<input type="file" id="pf_avatar" accept="image/*" style="display:none">' +
        '<small>JPG / PNG · Tối đa 2 MB</small>' +
        '</div>' +

        '<div class="lk-section-label"><i class="fas fa-user"></i> Thông tin cơ bản</div>' +

        '<div class="lk-form-row">' +
            '<div class="lk-form-group">' +
                '<label>Họ và tên <span class="req">*</span></label>' +
                '<div class="lk-input-wrap"><i class="fas fa-user"></i>' +
                '<input class="lk-input" id="pf_name" value="' + escHtml(u.full_name||'') + '" placeholder="Họ và tên"></div>' +
            '</div>' +
            '<div class="lk-form-group' + (missingPhone ? ' lk-field-highlight' : '') + '">' +
                '<label>Số điện thoại' + (missingPhone ? ' <span class="lk-badge-missing">Chưa có</span>' : '') + '</label>' +
                '<div class="lk-input-wrap"><i class="fas fa-phone"></i>' +
                '<input class="lk-input' + (missingPhone ? ' lk-input-highlight' : '') + '" id="pf_phone" value="' + escHtml(u.phone||'') + '" placeholder="0912 345 678"></div>' +
            '</div>' +
        '</div>' +

        '<div class="lk-form-group' + (missingEmail ? ' lk-field-highlight' : '') + '">' +
            '<label>Email' + (missingEmail ? ' <span class="lk-badge-missing">Chưa có</span>' : '') + '</label>' +
            '<div class="lk-input-wrap"><i class="fas fa-envelope"></i>' +
            '<input class="lk-input' + (missingEmail ? ' lk-input-highlight' : '') + '" id="pf_email" value="' + escHtml(missingEmail ? '' : (u.email||'')) + '" placeholder="email@example.com"></div>' +
        '</div>' +

        '<div class="lk-section-label"><i class="fas fa-lock"></i> Đổi mật khẩu ' + (isOAuth ? '(tuỳ chọn — tạo thêm mật khẩu)' : '') + '</div>' +

        (isOAuth ? '' :
            '<div class="lk-form-group">' +
                '<label>Mật khẩu hiện tại</label>' +
                '<div class="lk-input-wrap"><i class="fas fa-lock"></i>' +
                '<input class="lk-input" type="password" id="pf_oldpass" placeholder="Nhập mật khẩu hiện tại"></div>' +
            '</div>'
        ) +

        '<div class="lk-form-row">' +
            '<div class="lk-form-group">' +
                '<label>Mật khẩu mới</label>' +
                '<div class="lk-input-wrap"><i class="fas fa-lock-open"></i>' +
                '<input class="lk-input" type="password" id="pf_newpass" placeholder="Tối thiểu 6 ký tự"></div>' +
            '</div>' +
            '<div class="lk-form-group">' +
                '<label>Xác nhận mật khẩu</label>' +
                '<div class="lk-input-wrap"><i class="fas fa-lock"></i>' +
                '<input class="lk-input" type="password" id="pf_cfpass" placeholder="Nhập lại"></div>' +
            '</div>' +
        '</div>' +

        '<div class="lk-btn-row">' +
            '<button class="lk-btn lk-btn-ghost" id="btnCancelProfile"><i class="fas fa-times"></i> Hủy</button>' +
            '<button class="lk-btn lk-btn-primary" id="btnSaveProfile" data-oauth="' + escHtml(u.oauth_provider||'') + '">' +
                '<i class="fas fa-floppy-disk"></i> Lưu thay đổi' +
            '</button>' +
        '</div>';

    document.getElementById('btnCancelProfile').addEventListener('click', function () { closeModal('modalProfile'); });
    document.getElementById('btnSaveProfile').addEventListener('click', saveProfile);
}

function saveProfile() {
    var name    = (document.getElementById('pf_name')?.value  || '').trim();
    var phone   = (document.getElementById('pf_phone')?.value || '').trim();
    var email   = (document.getElementById('pf_email')?.value || '').trim();
    var avatarFile = document.getElementById('pf_avatar')?.files[0] || null;
    if (!name) { showToast('Họ tên không được để trống!', false); return; }

    var newPass = (document.getElementById('pf_newpass')?.value || '').trim();
    var cfPass  = (document.getElementById('pf_cfpass')?.value  || '').trim();
    if (newPass || cfPass) {
        if (newPass.length < 6) { showToast('Mật khẩu mới phải ít nhất 6 ký tự!', false); return; }
        if (newPass !== cfPass) { showToast('Xác nhận mật khẩu không khớp!', false); return; }
    }

    var btn = document.getElementById('btnSaveProfile');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Đang lưu...';

    var postData = { action:'update_profile', full_name:name, phone:phone, email:email };
    if (avatarFile) postData['avatar'] = avatarFile;

    apiPost('../actions/user_action.php', postData, function (d) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-floppy-disk"></i> Lưu thay đổi';
        if (d.success) {
            // Cập nhật tên & avatar trong navbar
            var nameEl  = document.querySelector('#navUser .nav-user-name');
            var avEl    = document.querySelector('#navUser .nav-avatar');
            var ddAvEl  = document.querySelector('#navUser .dd-avatar');
            var ddName  = document.querySelector('#navUser .dd-name');
            var ddEmail = document.querySelector('#navUser .dd-email');
            var initial = d.initial || (d.full_name || '').charAt(0).toUpperCase();
            if (nameEl) nameEl.textContent = d.full_name;
            if (ddName) ddName.textContent = d.full_name;
            if (ddEmail && email) ddEmail.textContent = email;
            /* Nếu có avatar mới từ upload thì cập nhật ảnh, không thì giữ nguyên */
            if (d.avatar_url && avEl) {
                avEl.innerHTML = '<img src="' + d.avatar_url + '" class="nav-avatar-img" onerror="this.parentElement.textContent=\'' + initial + '\'">';
            } else if (avEl && !avEl.querySelector('img')) {
                avEl.textContent = initial;
            }
            if (d.avatar_url && ddAvEl) {
                ddAvEl.innerHTML = '<img src="' + d.avatar_url + '" class="dd-avatar-img" onerror="this.parentElement.textContent=\'' + initial + '\'">';
            } else if (ddAvEl && !ddAvEl.querySelector('img')) {
                ddAvEl.textContent = initial;
            }
            showToast('Cập nhật thông tin thành công!', true);
            closeModal('modalProfile');
        } else {
            showToast(d.message || 'Lỗi cập nhật!', false);
        }
    });

    if (newPass) {
        var oldPass = (document.getElementById('pf_oldpass')?.value || '').trim();
        apiPost('../actions/user_action.php', {
            action:'change_password', old_password:oldPass, new_password:newPass
        }, function (d) { showToast(d.message, d.success); });
    }
}

/* ─── 6. ĐỊA CHỈ NHẬN HÀNG ─────────────────────────────── */
var btnAddress = document.getElementById('ddBtnAddress');
if (btnAddress) {
    btnAddress.addEventListener('click', function (e) {
        e.preventDefault();
        openModal('modalAddress');
        loadAddressList();
    });
}

function loadAddressList() {
    loading('addressBody');
    apiGet('../actions/user_action.php?action=get', function (data) {
        if (data.success) renderAddressList(data.addresses || []);
        else document.getElementById('addressBody').innerHTML = '<p class="lk-err-msg">Không thể tải dữ liệu.</p>';
    });
}

function renderAddressList(addresses) {
    var html = '';
    if (addresses.length > 0) {
        html = '<div class="lk-address-list">';
        addresses.forEach(function (addr) {
            var parts = [addr.address, addr.ward, addr.district, addr.province].filter(Boolean);
            var full  = parts.join(', ');
            var recipientName  = addr.recipient_name  || '';
            var recipientPhone = addr.recipient_phone || '';
            html +=
                '<div class="lk-address-card' + (addr.is_default ? ' is-default' : '') + '" data-id="' + addr.id + '">' +
                    '<div class="addr-top">' +
                        '<div class="addr-meta">' +
                            (addr.is_default ? '<span class="addr-badge"><i class="fas fa-star"></i> Mặc định</span>' : '') +
                            (recipientName || recipientPhone
                                ? '<div class="addr-recipient">' +
                                    (recipientName  ? '<span class="addr-recip-name"><i class="fas fa-user"></i> ' + escHtml(recipientName) + '</span>' : '') +
                                    (recipientPhone ? '<span class="addr-recip-phone"><i class="fas fa-phone"></i> ' + escHtml(recipientPhone) + '</span>' : '') +
                                  '</div>'
                                : '') +
                            '<span class="addr-location-icon"><i class="fas fa-map-marker-alt"></i></span>' +
                            '<p class="addr-text">' + escHtml(full) + '</p>' +
                        '</div>' +
                        '<div class="addr-actions">' +
                            '<button class="lk-btn-xs lk-btn-outline" onclick="openEditAddress(' + addr.id + ', ' + JSON.stringify(addr).replace(/"/g,'&quot;') + ')"><i class="fas fa-pen"></i> Sửa</button>' +
                            (!addr.is_default ? '<button class="lk-btn-xs lk-btn-ghost" onclick="setDefaultAddress(' + addr.id + ')"><i class="fas fa-star"></i> Mặc định</button>' : '') +
                            (!addr.is_default ? '<button class="lk-btn-xs lk-btn-danger" onclick="deleteAddress(' + addr.id + ')"><i class="fas fa-trash"></i></button>' : '') +
                        '</div>' +
                    '</div>' +
                '</div>';
        });
        html += '</div>';
    } else {
        html = '<div class="lk-empty-state"><i class="fas fa-map-marker-alt"></i><p>Chưa có địa chỉ nào</p></div>';
    }
    html += '<button class="lk-btn lk-btn-primary lk-btn-full" id="btnAddNewAddress"><i class="fas fa-plus"></i> Thêm địa chỉ mới</button>';
    document.getElementById('addressBody').innerHTML = html;
    document.getElementById('btnAddNewAddress').addEventListener('click', function () { showAddressForm(null, null); });
}

function showAddressForm(id, addr) {
    var isEdit = !!id;
    document.getElementById('addressBody').innerHTML =
        '<div class="lk-form-title">' + (isEdit ? '<i class="fas fa-pen"></i> Sửa địa chỉ' : '<i class="fas fa-plus"></i> Thêm địa chỉ mới') + '</div>' +

        '<div class="lk-section-label"><i class="fas fa-user"></i> Thông tin người nhận</div>' +

        '<div class="lk-form-row">' +
            '<div class="lk-form-group">' +
                '<label>Họ và tên <span class="req">*</span></label>' +
                '<div class="lk-input-wrap"><i class="fas fa-user"></i>' +
                '<input class="lk-input" id="addr_recip_name" value="' + escHtml((addr && addr.recipient_name)||'') + '" placeholder="Nguyễn Văn A"></div>' +
            '</div>' +
            '<div class="lk-form-group">' +
                '<label>Số điện thoại <span class="req">*</span></label>' +
                '<div class="lk-input-wrap"><i class="fas fa-phone"></i>' +
                '<input class="lk-input" id="addr_recip_phone" value="' + escHtml((addr && addr.recipient_phone)||'') + '" placeholder="0912 345 678"></div>' +
            '</div>' +
        '</div>' +

        '<div class="lk-section-label"><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng</div>' +

        '<div class="lk-form-row">' +
            '<div class="lk-form-group">' +
                '<label>Tỉnh / Thành phố <span class="req">*</span></label>' +
                '<div class="lk-input-wrap"><i class="fas fa-city"></i>' +
                '<input class="lk-input" id="addr_province" value="' + escHtml((addr && addr.province)||'') + '" placeholder="Hà Nội"></div>' +
            '</div>' +
            '<div class="lk-form-group">' +
                '<label>Quận / Huyện <span class="req">*</span></label>' +
                '<div class="lk-input-wrap"><i class="fas fa-map"></i>' +
                '<input class="lk-input" id="addr_district" value="' + escHtml((addr && addr.district)||'') + '" placeholder="Cầu Giấy"></div>' +
            '</div>' +
        '</div>' +

        '<div class="lk-form-row">' +
            '<div class="lk-form-group">' +
                '<label>Phường / Xã <span class="req">*</span></label>' +
                '<div class="lk-input-wrap"><i class="fas fa-location-crosshairs"></i>' +
                '<input class="lk-input" id="addr_ward" value="' + escHtml((addr && addr.ward)||'') + '" placeholder="Dịch Vọng Hậu"></div>' +
            '</div>' +
            '<div class="lk-form-group">' +
                '<label>Số nhà, tên đường <span class="req">*</span></label>' +
                '<div class="lk-input-wrap"><i class="fas fa-road"></i>' +
                '<input class="lk-input" id="addr_detail" value="' + escHtml((addr && addr.address)||'') + '" placeholder="123 Xuân Thủy"></div>' +
            '</div>' +
        '</div>' +

        '<div class="lk-btn-row">' +
            '<button class="lk-btn lk-btn-ghost" id="btnBackAddress"><i class="fas fa-arrow-left"></i> Quay lại</button>' +
            '<button class="lk-btn lk-btn-primary" id="btnSaveAddress" data-id="' + (id||'') + '">' +
                '<i class="fas fa-location-dot"></i> ' + (isEdit ? 'Cập nhật' : 'Lưu địa chỉ') +
            '</button>' +
        '</div>';

    document.getElementById('btnBackAddress').addEventListener('click', loadAddressList);
    document.getElementById('btnSaveAddress').addEventListener('click', saveAddress);
}

window.openEditAddress = function (id, addr) { showAddressForm(id, addr); };

window.deleteAddress = function (id) {
    if (!confirm('Xóa địa chỉ này?')) return;
    apiPost('../actions/user_action.php', { action:'delete_address', address_id:id }, function (d) {
        showToast(d.message || (d.success ? 'Đã xóa!' : 'Lỗi!'), d.success);
        if (d.success) loadAddressList();
    });
};

window.setDefaultAddress = function (id) {
    apiPost('../actions/user_action.php', { action:'set_default_address', address_id:id }, function (d) {
        showToast(d.message || (d.success ? 'Đã đặt mặc định!' : 'Lỗi!'), d.success);
        if (d.success) loadAddressList();
    });
};

function saveAddress() {
    var id             = this.dataset.id || '';
    var recipName      = (document.getElementById('addr_recip_name')?.value  || '').trim();
    var recipPhone     = (document.getElementById('addr_recip_phone')?.value || '').trim();
    var province       = (document.getElementById('addr_province')?.value    || '').trim();
    var district       = (document.getElementById('addr_district')?.value    || '').trim();
    var ward           = (document.getElementById('addr_ward')?.value        || '').trim();
    var detail         = (document.getElementById('addr_detail')?.value      || '').trim();
    if (!recipName) { showToast('Vui lòng nhập họ và tên người nhận!', false); return; }
    if (!recipPhone) { showToast('Vui lòng nhập số điện thoại người nhận!', false); return; }
    if (!province || !district || !ward || !detail) {
        showToast('Vui lòng điền đầy đủ thông tin địa chỉ!', false); return;
    }
    var btn = document.getElementById('btnSaveAddress');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Đang lưu...';
    apiPost('../actions/user_action.php', {
        action: id ? 'update_address' : 'add_address',
        address_id: id,
        recipient_name:  recipName,
        recipient_phone: recipPhone,
        province: province, district: district, ward: ward, address: detail
    }, function (d) {
        showToast(d.message || (d.success ? 'Đã lưu!' : 'Lỗi!'), d.success);
        if (d.success) loadAddressList();
        else { btn.disabled = false; btn.innerHTML = '<i class="fas fa-location-dot"></i> ' + (id ? 'Cập nhật' : 'Lưu địa chỉ'); }
    });
}

/* ─── 7. GIỎ HÀNG ───────────────────────────────────────── */
document.querySelectorAll('.cart-icon-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        openModal('modalCart');
        loadCart();
    });
});

function loadCart() {
    loading('cartBody');
    apiGet('../actions/cart_action.php?action=get', function (data) {
        if (data.success) renderCart(data.items, data.total);
        else document.getElementById('cartBody').innerHTML = '<p class="lk-err-msg">Không thể tải giỏ hàng.</p>';
    });
}

function renderCart(items, total) {
    if (!items || items.length === 0) {
        document.getElementById('cartBody').innerHTML =
            '<div class="lk-empty-state">' +
            '<i class="fas fa-shopping-cart"></i><p>Giỏ hàng trống</p>' +
            '<a href="products.php" class="lk-btn lk-btn-primary" onclick="closeModal(\'modalCart\')">' +
            '<i class="fas fa-shopping-bag"></i> Mua sắm ngay</a></div>';
        updateCartBadge(0); return;
    }
    var html = '<div class="lk-cart-list">';
    var totalQty = 0;
    items.forEach(function (item) {
        totalQty += parseInt(item.quantity);
        html +=
            '<div class="lk-cart-item">' +
                '<img src="../assets/imgs/' + escHtml(item.image||'') + '" class="cart-item-img" onerror="this.src=\'../assets/imgs/logo.png\'">' +
                '<div class="cart-item-info">' +
                    '<div class="cart-item-name">' + escHtml(item.name) + '</div>' +
                    '<div class="cart-item-price">' + Number(item.price).toLocaleString('vi-VN') + 'đ</div>' +
                '</div>' +
                '<div class="cart-item-qty">' +
                    '<button class="qty-btn" onclick="updateCartQty(' + item.product_id + ',' + (parseInt(item.quantity)-1) + ')">−</button>' +
                    '<span class="qty-num">' + item.quantity + '</span>' +
                    '<button class="qty-btn" onclick="updateCartQty(' + item.product_id + ',' + (parseInt(item.quantity)+1) + ')">+</button>' +
                '</div>' +
                '<button class="cart-remove-btn" onclick="removeCartItem(' + item.product_id + ')"><i class="fas fa-trash"></i></button>' +
            '</div>';
    });
    html += '</div>';
    html +=
        '<div class="lk-cart-footer">' +
            '<div class="cart-total-row"><span>Tổng cộng</span><strong>' + Number(total).toLocaleString('vi-VN') + 'đ</strong></div>' +
            '<div class="lk-btn-row">' +
                '<button class="lk-btn lk-btn-ghost" onclick="closeModal(\'modalCart\')"><i class="fas fa-arrow-left"></i> Tiếp tục mua</button>' +
                '<button class="lk-btn lk-btn-primary" id="btnCheckout"><i class="fas fa-credit-card"></i> Đặt hàng</button>' +
            '</div>' +
        '</div>';
    document.getElementById('cartBody').innerHTML = html;
    updateCartBadge(totalQty);
    document.getElementById('btnCheckout').addEventListener('click', showCheckoutForm);
}

function showCheckoutForm() {
    loading('cartBody');
    apiGet('../actions/user_action.php?action=get', function (data) {
        var defaultAddr = '';
        if (data.success && data.addresses && data.addresses.length > 0) {
            var def = data.addresses.find(function (a) { return a.is_default; }) || data.addresses[0];
            defaultAddr = [def.address, def.ward, def.district, def.province].filter(Boolean).join(', ');
        }
        document.getElementById('cartBody').innerHTML =
            '<div class="lk-form-title"><i class="fas fa-map-marker-alt"></i> Xác nhận đặt hàng</div>' +
            '<div class="lk-form-group">' +
                '<label>Địa chỉ nhận hàng <span class="req">*</span></label>' +
                '<div class="lk-input-wrap"><i class="fas fa-location-dot"></i>' +
                '<input class="lk-input" id="checkout_addr" value="' + escHtml(defaultAddr) + '" placeholder="Số nhà, đường, phường, quận, tỉnh..."></div>' +
            '</div>' +
            '<div class="lk-form-group">' +
                '<label>Ghi chú</label>' +
                '<div class="lk-input-wrap"><i class="fas fa-note-sticky"></i>' +
                '<input class="lk-input" id="checkout_note" placeholder="Ghi chú cho đơn hàng (tuỳ chọn)"></div>' +
            '</div>' +
            '<div class="lk-btn-row">' +
                '<button class="lk-btn lk-btn-ghost" id="btnBackCart"><i class="fas fa-arrow-left"></i> Quay lại giỏ hàng</button>' +
                '<button class="lk-btn lk-btn-primary" id="btnConfirmOrder"><i class="fas fa-check"></i> Xác nhận đặt hàng</button>' +
            '</div>';
        document.getElementById('btnBackCart').addEventListener('click', loadCart);
        document.getElementById('btnConfirmOrder').addEventListener('click', confirmOrder);
    });
}

function confirmOrder() {
    var addr = (document.getElementById('checkout_addr')?.value || '').trim();
    var note = (document.getElementById('checkout_note')?.value || '').trim();
    if (!addr) { showToast('Vui lòng nhập địa chỉ nhận hàng!', false); return; }
    var btn = document.getElementById('btnConfirmOrder');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Đang xử lý...';
    apiPost('../actions/cart_action.php', { action:'checkout', address:addr, note:note }, function (d) {
        if (d.success) {
            document.getElementById('cartBody').innerHTML =
                '<div class="lk-success-state">' +
                '<div class="success-icon"><i class="fas fa-check-circle"></i></div>' +
                '<h3>Đặt hàng thành công!</h3>' +
                '<p class="order-code-display">Mã đơn: <strong>' + escHtml(d.order_code) + '</strong></p>' +
                '<p>Chúng tôi sẽ liên hệ xác nhận sớm nhất.</p>' +
                '<button class="lk-btn lk-btn-primary" onclick="closeModal(\'modalCart\')">Đóng</button>' +
                '</div>';
            updateCartBadge(0);
        } else {
            showToast(d.message || 'Lỗi đặt hàng!', false);
            btn.disabled = false; btn.innerHTML = '<i class="fas fa-check"></i> Xác nhận đặt hàng';
        }
    });
}

window.updateCartQty = function (productId, newQty) {
    if (newQty <= 0) { removeCartItem(productId); return; }
    apiPost('../actions/cart_action.php', { action:'update', product_id:productId, quantity:newQty }, function (d) {
        if (d.success) { loadCart(); updateCartBadge(d.cart_count); }
        else showToast(d.message || 'Lỗi!', false);
    });
};
window.removeCartItem = function (productId) {
    apiPost('../actions/cart_action.php', { action:'remove', product_id:productId }, function (d) {
        if (d.success) { loadCart(); updateCartBadge(d.cart_count); }
        else showToast(d.message || 'Lỗi!', false);
    });
};
function updateCartBadge(count) {
    document.querySelectorAll('.cart-badge').forEach(function (el) {
        el.textContent = count > 0 ? count : '';
        el.style.display = count > 0 ? 'flex' : 'none';
    });
}
/* Khởi tải badge ngay khi page load */
apiGet('../actions/cart_action.php?action=get', function (data) {
    if (data.success) {
        var qty = data.items.reduce(function (s, i) { return s + parseInt(i.quantity); }, 0);
        updateCartBadge(qty);
    }
});

/* ─── 8. ĐƠN HÀNG ───────────────────────────────────────── */
var btnOrders = document.getElementById('ddBtnOrders');
if (btnOrders) {
    btnOrders.addEventListener('click', function (e) {
        e.preventDefault();
        openModal('modalOrders');
        loadOrders('all');
    });
}

var STATUS_LABEL = { pending:'Chờ xác nhận', confirmed:'Đã xác nhận', shipping:'Đang giao', delivered:'Đã giao', cancelled:'Đã huỷ' };
var STATUS_COLOR = { pending:'#f59e0b', confirmed:'#8b5cf6', shipping:'#3b82f6', delivered:'#10b981', cancelled:'#ef4444' };
var STATUS_ICON  = { pending:'fa-clock', confirmed:'fa-check', shipping:'fa-truck', delivered:'fa-box-open', cancelled:'fa-ban' };

var _allOrders = [];

function loadOrders(filter) {
    loading('ordersBody');
    apiGet('../actions/order_action.php?action=list', function (data) {
        if (data.success) { _allOrders = data.orders || []; renderOrders(filter); }
        else document.getElementById('ordersBody').innerHTML = '<p class="lk-err-msg">Không thể tải đơn hàng.</p>';
    });
}

function renderOrders(filter) {
    var orders = filter === 'all' ? _allOrders : _allOrders.filter(function (o) { return o.status === filter; });

    /* Tab bar */
    var tabs = [
        { key:'all',       label:'Tất cả' },
        { key:'pending',   label:'Chờ XN' },
        { key:'shipping',  label:'Đang giao' },
        { key:'delivered', label:'Đã giao' },
        { key:'cancelled', label:'Đã huỷ' },
    ];
    var tabHtml = '<div class="order-tabs">';
    tabs.forEach(function (t) {
        var cnt = t.key === 'all' ? _allOrders.length : _allOrders.filter(function (o) { return o.status === t.key; }).length;
        tabHtml += '<button class="order-tab' + (filter === t.key ? ' active' : '') + '" onclick="filterOrders(\'' + t.key + '\')">' +
            t.label + (cnt > 0 ? ' <span class="tab-count">' + cnt + '</span>' : '') + '</button>';
    });
    tabHtml += '</div>';

    if (!orders || orders.length === 0) {
        document.getElementById('ordersBody').innerHTML =
            tabHtml +
            '<div class="lk-empty-state"><i class="fas fa-clipboard-list"></i>' +
            '<p>' + (filter === 'all' ? 'Chưa có đơn hàng nào' : 'Không có đơn hàng trong mục này') + '</p>' +
            '<a href="products.php" class="lk-btn lk-btn-primary" onclick="closeModal(\'modalOrders\')">' +
            '<i class="fas fa-shopping-bag"></i> Mua sắm ngay</a></div>';
        return;
    }

    var html = tabHtml + '<div class="lk-order-list">';
    orders.forEach(function (order) {
        var color = STATUS_COLOR[order.status] || '#8b5cf6';
        var label = STATUS_LABEL[order.status] || order.status;
        var icon  = STATUS_ICON[order.status]  || 'fa-circle';

        /* Items preview */
        var itemsHtml = '';
        if (order.items && order.items.length > 0) {
            itemsHtml = '<div class="order-items-preview">';
            order.items.slice(0, 3).forEach(function (item) {
                itemsHtml += '<div class="order-item-row">' +
                    '<span class="order-item-name">' + escHtml(item.product_name) + '</span>' +
                    '<span class="order-item-info">x' + item.quantity + ' · ' + Number(item.price).toLocaleString('vi-VN') + 'đ</span>' +
                    '</div>';
            });
            if (order.items.length > 3) {
                itemsHtml += '<p class="order-more-items">+ ' + (order.items.length - 3) + ' sản phẩm khác</p>';
            }
            itemsHtml += '</div>';
        }

        /* Action buttons — Chỉ được hủy khi pending | confirmed */
        var actions = '';
        if (order.status === 'pending' || order.status === 'confirmed') {
            actions = '<button class="lk-btn-xs lk-btn-danger" onclick="cancelOrder(' + order.id + ', \'' + escHtml(order.order_code) + '\')"><i class="fas fa-ban"></i> Huỷ đơn</button>';
        } else if (order.status === 'shipping') {
            actions = '<span class="tracking-badge"><i class="fas fa-truck"></i> Đang vận chuyển</span>';
        } else if (order.status === 'delivered') {
            actions = '<span class="tracking-badge" style="color:#10b981;border-color:#10b98130;background:#10b98112;"><i class="fas fa-check-circle"></i> Hoàn thành</span>';
        }

        html +=
            '<div class="lk-order-card">' +
                '<div class="order-card-head">' +
                    '<div class="order-code-date">' +
                        '<span class="order-code"><i class="fas fa-hashtag"></i> ' + escHtml(order.order_code) + '</span>' +
                        '<span class="order-date"><i class="fas fa-calendar-days"></i> ' + escHtml(order.created_at||'') + '</span>' +
                    '</div>' +
                    '<span class="order-status-badge" style="color:' + color + ';background:' + color + '18;border-color:' + color + '40">' +
                        '<i class="fas ' + icon + '"></i> ' + label +
                    '</span>' +
                '</div>' +
                itemsHtml +
                '<div class="order-card-foot">' +
                    '<div class="order-addr"><i class="fas fa-map-marker-alt"></i> ' + escHtml(order.address || 'Chưa có địa chỉ') + '</div>' +
                    '<div class="order-foot-right">' +
                        '<span class="order-total-amount"><strong>' + Number(order.total).toLocaleString('vi-VN') + 'đ</strong></span>' +
                        actions +
                    '</div>' +
                '</div>' +
            '</div>';
    });
    html += '</div>';
    document.getElementById('ordersBody').innerHTML = html;
}

window.filterOrders = function (filter) { renderOrders(filter); };

window.cancelOrder = function (orderId, orderCode) {
    var reason = prompt('Lý do hủy đơn #' + orderCode + ' (bỏ trống = "Khách hàng yêu cầu"):', '');
    if (reason === null) return; // Bấm Cancel
    reason = reason.trim() || 'Khách hàng yêu cầu hủy';
    apiPost('../actions/order_action.php', { action:'cancel', order_id:orderId, reason:reason }, function (d) {
        showToast(d.message || (d.success ? 'Đã huỷ đơn!' : 'Không thể huỷ!'), d.success);
        if (d.success) loadOrders('all');
    });
};

/* ─── 9. ADD TO CART — gọi API thật ─────────────────────── */
document.querySelectorAll('.add-cart-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        var isLoggedIn = (typeof USER_LOGGED_IN !== 'undefined') ? USER_LOGGED_IN : false;
        if (!isLoggedIn) {
            var currentPage = window.location.pathname.split('/').pop() || 'products.php';
            var loginUrl = (typeof LOGIN_URL !== 'undefined') ? LOGIN_URL : 'login.php';
            window.location.href = loginUrl + '?redirect_to=' + encodeURIComponent(currentPage);
            return;
        }
        var productId = this.dataset.productId;
        if (!productId) return;
        var btnEl = this;
        btnEl.disabled = true;
        btnEl.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';

        apiPost('../actions/cart_action.php', { action:'add', product_id:productId, quantity:1 }, function (d) {
            if (d.success) {
                btnEl.innerHTML = '<i class="fas fa-check"></i> Đã thêm!';
                btnEl.style.background = '#10b981';
                updateCartBadge(d.cart_count);
                showToast(d.message || 'Đã thêm vào giỏ!', true);
                setTimeout(function () {
                    btnEl.innerHTML = 'Thêm vào giỏ';
                    btnEl.style.background = '';
                    btnEl.disabled = false;
                }, 1800);
            } else if (d.require_login) {
                var currentPage = window.location.pathname.split('/').pop() || 'products.php';
                window.location.href = 'login.php?redirect_to=' + encodeURIComponent(currentPage);
            } else {
                showToast(d.message || 'Lỗi thêm vào giỏ!', false);
                btnEl.innerHTML = 'Thêm vào giỏ';
                btnEl.style.background = '';
                btnEl.disabled = false;
            }
        });
    });
});

/* ─── EXPORTS ────────────────────────────────────────────── */
window.lkShowToast  = showToast;
window.lkOpenModal  = openModal;
window.lkCloseModal = closeModal;
window.lkLoadCart   = loadCart;

})();
</script>
<?php endif; ?>
