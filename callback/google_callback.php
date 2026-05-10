<?php
// ============================================================
//  LKSecure — Google OAuth Callback
//  Cần cài: composer require google/apiclient
//  HOẶC dùng cURL thuần như bên dưới (không cần composer)
// ============================================================
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../env.php';

$client_id     = env('GOOGLE_CLIENT_ID');
$client_secret = env('GOOGLE_CLIENT_SECRET');
$redirect_uri  = env('GOOGLE_REDIRECT_URI');

$code = $_GET['code'] ?? '';
if (!$code) {
    header("Location: ../pages/login.php?error=oauth_failed");
    exit;
}

// Đổi code lấy access token
$token_url  = 'https://oauth2.googleapis.com/token';
$token_data = http_build_query([
    'code'          => $code,
    'client_id'     => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri'  => $redirect_uri,
    'grant_type'    => 'authorization_code',
]);

$ch = curl_init($token_url);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $token_data,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$tokenResponse = json_decode(curl_exec($ch), true);
curl_close($ch);

if (empty($tokenResponse['access_token'])) {
    header("Location: ../pages/login.php?error=oauth_failed");
    exit;
}

// Lấy thông tin user từ Google
$ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $tokenResponse['access_token']],
    CURLOPT_SSL_VERIFYPEER => false,
]);
$userInfo = json_decode(curl_exec($ch), true);
curl_close($ch);

if (empty($userInfo['id'])) {
    header("Location: ../pages/login.php?error=oauth_failed");
    exit;
}

$oauth_id   = $userInfo['id'];
$email      = $userInfo['email']      ?? '';
$full_name  = $userInfo['name']       ?? $email;
$avatar     = $userInfo['picture']    ?? null;

// Tìm hoặc tạo user
$stmt = $conn->prepare("SELECT id, full_name, email, role FROM users WHERE oauth_provider = 'google' AND oauth_id = ?");
$stmt->bind_param("s", $oauth_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user && $email) {
    // Kiểm tra email đã tồn tại chưa
    $stmt = $conn->prepare("SELECT id, full_name, email, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Gắn OAuth vào tài khoản có sẵn
        $stmt = $conn->prepare("UPDATE users SET oauth_provider = 'google', oauth_id = ?, avatar = ? WHERE id = ?");
        $stmt->bind_param("ssi", $oauth_id, $avatar, $user['id']);
        $stmt->execute();
        $stmt->close();
    } else {
        // Tạo tài khoản mới
        $stmt = $conn->prepare(
            "INSERT INTO users (full_name, email, password, role, oauth_provider, oauth_id, avatar, is_active)
             VALUES (?, ?, '', 'customer', 'google', ?, ?, 1)"
        );
        $stmt->bind_param("ssss", $full_name, $email, $oauth_id, $avatar);
        $stmt->execute();
        $new_id = $conn->insert_id;
        $stmt->close();

        $user = ['id' => $new_id, 'full_name' => $full_name, 'email' => $email, 'role' => 'customer'];
    }
}

if (!$user) {
    header("Location: ../pages/login.php?error=oauth_failed");
    exit;
}

$_SESSION['user_id']     = $user['id'];
$_SESSION['user_email']  = $user['email'];
$_SESSION['user_name']   = $user['full_name'];
$_SESSION['user_role']   = $user['role'];
$_SESSION['user_avatar'] = $avatar ?? '';

if ($user['role'] === 'admin') {
    header("Location: ../pages/admin.php");
} else {
    header("Location: ../pages/home.php?login=success");
}
exit;