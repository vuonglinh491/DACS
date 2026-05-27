<?php
ob_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth_check.php';
ob_clean();
header('Content-Type: application/json; charset=utf-8');
requireLogin(true);

$action  = $_POST['action'] ?? $_GET['action'] ?? '';
$user_id = (int)$_SESSION['user_id'];

// ── LẤY THÔNG TIN ─────────────────────────────────────────
if ($action === 'get') {
    $stmt = $conn->prepare("SELECT full_name, email, phone, address, province, district, ward, avatar, oauth_provider FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $addresses = [];
    $addrStmt = $conn->prepare("SELECT id, address, province, district, ward, recipient_name, recipient_phone, is_default FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, id ASC");
    $addrStmt->bind_param("i", $user_id);
    $addrStmt->execute();
    $addrResult = $addrStmt->get_result();
    while ($row = $addrResult->fetch_assoc()) $addresses[] = $row;
    $addrStmt->close();

    echo json_encode(['success' => true, 'user' => $user, 'addresses' => $addresses]);
    exit;
}

// ── CẬP NHẬT THÔNG TIN CÁ NHÂN ──────────────────────────
if ($action === 'update_profile') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone']     ?? '');
    $email     = trim($_POST['email']     ?? '');
    if (!$full_name) { echo json_encode(['success' => false, 'message' => 'Họ tên không được để trống.']); exit; }
    if ($email) {
        $chk = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $chk->bind_param("si", $email, $user_id);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) { echo json_encode(['success' => false, 'message' => 'Email đã được dùng bởi tài khoản khác.']); $chk->close(); exit; }
        $chk->close();
    }
    $avatarName = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
        $mime    = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed)) { echo json_encode(['success' => false, 'message' => 'Định dạng ảnh không hợp lệ.']); exit; }
        if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) { echo json_encode(['success' => false, 'message' => 'Ảnh quá lớn (tối đa 2MB).']); exit; }
        $uploadDir = __DIR__ . '/../assets/imgs/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $avatarName = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $avatarName);
    }
    if ($avatarName) {
        $stmt = $conn->prepare("UPDATE users SET full_name=?, phone=?, email=?, avatar=? WHERE id=?");
        $stmt->bind_param("ssssi", $full_name, $phone, $email, $avatarName, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name=?, phone=?, email=? WHERE id=?");
        $stmt->bind_param("sssi", $full_name, $phone, $email, $user_id);
    }
    $stmt->execute();
    $stmt->close();
    $_SESSION['user_name'] = $full_name;
    $_SESSION['user_email'] = $email;
    echo json_encode(['success' => true, 'message' => 'Cập nhật thành công!', 'full_name' => $full_name, 'avatar' => $avatarName]);
    exit;
}

// ── THÊM ĐỊA CHỈ MỚI ─────────────────────────────────────
if ($action === 'add_address') {
    $address         = trim($_POST['address']         ?? '');
    $province        = trim($_POST['province']        ?? '');
    $district        = trim($_POST['district']        ?? '');
    $ward            = trim($_POST['ward']            ?? '');
    $recipient_name  = trim($_POST['recipient_name']  ?? '');
    $recipient_phone = trim($_POST['recipient_phone'] ?? '');

    if (!$recipient_name)  { echo json_encode(['success' => false, 'message' => 'Vui lòng nhập tên người nhận.']); exit; }
    if (!$recipient_phone) { echo json_encode(['success' => false, 'message' => 'Vui lòng nhập số điện thoại.']); exit; }
    if (!$province)        { echo json_encode(['success' => false, 'message' => 'Vui lòng chọn tỉnh/thành phố.']); exit; }
    if (!$district)        { echo json_encode(['success' => false, 'message' => 'Vui lòng chọn quận/huyện.']); exit; }
    if (!$ward)            { echo json_encode(['success' => false, 'message' => 'Vui lòng chọn phường/xã.']); exit; }
    if (!$address)         { echo json_encode(['success' => false, 'message' => 'Vui lòng nhập số nhà, tên đường.']); exit; }

    // Kiểm tra số lượng — địa chỉ đầu tiên tự động là mặc định
    $countRes = $conn->prepare("SELECT COUNT(*) AS cnt FROM user_addresses WHERE user_id = ?");
    $countRes->bind_param("i", $user_id);
    $countRes->execute();
    $cnt = (int)$countRes->get_result()->fetch_assoc()['cnt'];
    $countRes->close();
    $isDefault = ($cnt === 0) ? 1 : 0;

    $stmt = $conn->prepare(
        "INSERT INTO user_addresses (user_id, address, province, district, ward, recipient_name, recipient_phone, is_default)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("issssssi", $user_id, $address, $province, $district, $ward, $recipient_name, $recipient_phone, $isDefault);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã thêm địa chỉ mới!', 'new_id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi lưu địa chỉ: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// ── CẬP NHẬT ĐỊA CHỈ ────────────────────────────────────
if ($action === 'update_address') {
    $address_id      = (int)($_POST['address_id']     ?? 0);
    $address         = trim($_POST['address']         ?? '');
    $province        = trim($_POST['province']        ?? '');
    $district        = trim($_POST['district']        ?? '');
    $ward            = trim($_POST['ward']            ?? '');
    $recipient_name  = trim($_POST['recipient_name']  ?? '');
    $recipient_phone = trim($_POST['recipient_phone'] ?? '');

    if (!$address_id) { echo json_encode(['success' => false, 'message' => 'Địa chỉ không hợp lệ.']); exit; }
    if (!$address)    { echo json_encode(['success' => false, 'message' => 'Vui lòng nhập địa chỉ chi tiết.']); exit; }

    $stmt = $conn->prepare(
        "UPDATE user_addresses SET address=?, province=?, district=?, ward=?, recipient_name=?, recipient_phone=?
         WHERE id=? AND user_id=?"
    );
    $stmt->bind_param("ssssssii", $address, $province, $district, $ward, $recipient_name, $recipient_phone, $address_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật địa chỉ thành công!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// ── XÓA ĐỊA CHỈ ─────────────────────────────────────────
if ($action === 'delete_address') {
    $address_id = (int)($_POST['address_id'] ?? 0);
    if (!$address_id) { echo json_encode(['success' => false, 'message' => 'Không hợp lệ.']); exit; }
    $stmt = $conn->prepare("DELETE FROM user_addresses WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $address_id, $user_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Đã xóa địa chỉ.']);
    exit;
}

// ── ĐẶT ĐỊA CHỈ MẶC ĐỊNH ────────────────────────────────
if ($action === 'set_default_address') {
    $address_id = (int)($_POST['address_id'] ?? 0);
    if (!$address_id) { echo json_encode(['success' => false, 'message' => 'Không hợp lệ.']); exit; }
    $s1 = $conn->prepare("UPDATE user_addresses SET is_default=0 WHERE user_id=?");
    $s1->bind_param("i", $user_id); $s1->execute(); $s1->close();
    $s2 = $conn->prepare("UPDATE user_addresses SET is_default=1 WHERE id=? AND user_id=?");
    $s2->bind_param("ii", $address_id, $user_id); $s2->execute(); $s2->close();
    echo json_encode(['success' => true, 'message' => 'Đã đặt địa chỉ mặc định!']);
    exit;
}

// ── ĐỔI MẬT KHẨU ────────────────────────────────────────
if ($action === 'change_password') {
    $old_pass = trim($_POST['old_password'] ?? '');
    $new_pass = trim($_POST['new_password'] ?? '');
    if (strlen($new_pass) < 6) { echo json_encode(['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự.']); exit; }
    $stmt = $conn->prepare("SELECT password, oauth_provider FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (empty($user['oauth_provider']) && !password_verify($old_pass, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu cũ không đúng.']); exit;
    }
    $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
    $stmt   = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $hashed, $user_id);
    $stmt->execute(); $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công!']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action không hợp lệ: ' . htmlspecialchars($action)]);