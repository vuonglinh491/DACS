<?php
// ============================================================
//  LKSecure — Facebook OAuth Callback
// ============================================================
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../env.php';

$app_id      = env('FB_APP_ID');
$app_secret  = env('FB_APP_SECRET');
$redirect_uri = env('FB_REDIRECT_URI');

$code = $_GET['code'] ?? '';
if (!$code) {
    header("Location: ../pages/login.php?error=oauth_failed");
    exit;
}

// Đổi code lấy access token
$token_url = "https://graph.facebook.com/v18.0/oauth/access_token?" . http_build_query([
    'client_id'     => $app_id,
    'redirect_uri'  => $redirect_uri,
    'client_secret' => $app_secret,
    'code'          => $code,
]);

$ch = curl_init($token_url);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false]);
$tokenResponse = json_decode(curl_exec($ch), true);
curl_close($ch);

if (empty($tokenResponse['access_token'])) {
    header("Location: ../pages/login.php?error=oauth_failed");
    exit;
}

$access_token = $tokenResponse['access_token'];

// Lấy thông tin user
$user_url = "https://graph.facebook.com/me?" . http_build_query([
    'fields'       => 'id,name,email,picture',
    'access_token' => $access_token,
]);

$ch = curl_init($user_url);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false]);
$userInfo = json_decode(curl_exec($ch), true);
curl_close($ch);

if (empty($userInfo['id'])) {
    header("Location: ../pages/login.php?error=oauth_failed");
    exit;
}

$oauth_id  = $userInfo['id'];
$full_name = $userInfo['name']  ?? 'Facebook User';
$email     = $userInfo['email'] ?? null;
$avatar    = $userInfo['picture']['data']['url'] ?? null;

// Tìm hoặc tạo user
$stmt = $conn->prepare("SELECT id, full_name, email, role FROM users WHERE oauth_provider = 'facebook' AND oauth_id = ?");
$stmt->bind_param("s", $oauth_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    if ($email) {
        $stmt = $conn->prepare("SELECT id, full_name, email, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user) {
            $stmt = $conn->prepare("UPDATE users SET oauth_provider = 'facebook', oauth_id = ?, avatar = ? WHERE id = ?");
            $stmt->bind_param("ssi", $oauth_id, $avatar, $user['id']);
            $stmt->execute();
            $stmt->close();
        }
    }

    if (!$user) {
        $safe_email = $email ?? 'fb_' . $oauth_id . '@noemail.local';
        $stmt = $conn->prepare(
            "INSERT INTO users (full_name, email, password, role, oauth_provider, oauth_id, avatar, is_active)
             VALUES (?, ?, '', 'customer', 'facebook', ?, ?, 1)"
        );
        $stmt->bind_param("ssss", $full_name, $safe_email, $oauth_id, $avatar);
        $stmt->execute();
        $new_id = $conn->insert_id;
        $stmt->close();
        $user = ['id' => $new_id, 'full_name' => $full_name, 'email' => $safe_email, 'role' => 'customer'];
    }
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