<?php
// ============================================================
//  LKSecure — Auth Guard & Session Timeout
//  Include file này ở đầu mọi trang/action cần bảo vệ.
//
//  Cách dùng:
//    require_once __DIR__ . '/../config/auth_check.php';
//    requireLogin();          // Bắt buộc đăng nhập
//    requireAdmin();          // Bắt buộc là admin
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Cấu hình session ─────────────────────────────────────
define('SESSION_TIMEOUT', 3600);      // 1 giờ không hoạt động → đăng xuất
define('SESSION_NAME_KEY', 'lksecure_session');

// ── Tự khôi phục session từ cookie "Ghi nhớ đăng nhập" ──
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user_id'])) {
    $_SESSION['user_id']    = (int)$_COOKIE['remember_user_id'];
    $_SESSION['user_email'] = $_COOKIE['remember_user_email'] ?? '';
    $_SESSION['user_name']  = $_COOKIE['remember_user_name']  ?? '';
    $_SESSION['user_role']  = $_COOKIE['remember_user_role']  ?? 'customer';
    $_SESSION['last_activity'] = time();
}

// ── Kiểm tra & cập nhật timeout session ──────────────────
if (isset($_SESSION['user_id'])) {
    $last = $_SESSION['last_activity'] ?? time();
    if ((time() - $last) > SESSION_TIMEOUT) {
        // Session quá hạn → đăng xuất
        _destroySession();
    } else {
        $_SESSION['last_activity'] = time();
    }
}

/**
 * Xóa toàn bộ session + cookie nhớ đăng nhập
 */
function _destroySession(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();

    // Xóa cookie "Ghi nhớ đăng nhập"
    foreach (['remember_user_id', 'remember_user_email', 'remember_user_name', 'remember_user_role'] as $key) {
        setcookie($key, '', time() - 3600, '/', '', false, true);
    }
}

/**
 * Yêu cầu người dùng đã đăng nhập.
 * Nếu chưa → redirect về login (cho trang HTML) hoặc JSON (cho API).
 *
 * @param bool $isApi  true = trả JSON thay vì redirect
 */
function requireLogin(bool $isApi = false): void
{
    if (!isset($_SESSION['user_id'])) {
        if ($isApi) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'require_login' => true, 'message' => 'Vui lòng đăng nhập.']);
            exit;
        }
        $current = urlencode($_SERVER['REQUEST_URI'] ?? '');
        header("Location: /DACS/pages/login.php?redirect_to={$current}");
        exit;
    }
}

/**
 * Yêu cầu người dùng là admin.
 * Gọi requireLogin() trước, sau đó kiểm tra role.
 *
 * @param bool $isApi  true = trả JSON thay vì redirect
 */
function requireAdmin(bool $isApi = false): void
{
    requireLogin($isApi);
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        if ($isApi) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập. Chỉ dành cho quản trị viên.']);
            exit;
        }
        header("Location: /DACS/pages/home.php?error=forbidden");
        exit;
    }
}

/**
 * Kiểm tra đăng nhập không redirect (dùng trong view).
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Kiểm tra là admin không redirect.
 */
function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
}
