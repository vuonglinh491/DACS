<?php
// ============================================================
//  LKSecure — Xử lý thông tin cá nhân & địa chỉ người dùng
// ============================================================
// ✅ ob_start() PHẢI là dòng đầu tiên — bắt mọi PHP warning/notice
//    không cho chúng phá vỡ JSON response
ob_start();

// auth_check.php đã gọi session_start() bên trong nó rồi
// KHÔNG gọi session_start() ở đây nữa
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth_check.php';

// Xoá bỏ mọi output thừa (PHP notices/warnings) trước khi set header
ob_clean();
header('Content-Type: application/json; charset=utf-8');

requireLogin(true);

$action  = $_POST['action'] ?? $_GET['action'] ?? ''; // Đọc POST trước, GET sau
$user_id = (int)$_SESSION['user_id'];

// ── LẤY THÔNG TIN ─────────────────────────────────────────
if ($action === 'get') {
    $stmt = $conn->prepare("SELECT full_name, email, phone, address, province, district, ward, avatar, oauth_provider FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $addresses = [];
    $tableCheck = $conn->query("SHOW TABLES LIKE 'user_addresses'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $addrStmt = $conn->prepare("SELECT id, user_id, address, province, district, ward, recipient_name, recipient_phone, is_default, created_at FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, id ASC");
        $addrStmt->bind_param("i", $user_id);
        $addrStmt->execute();
        $addrResult = $addrStmt->get_result();
        while ($row = $addrResult->fetch_assoc()) $addresses[] = $row;
        $addrStmt->close();
    } else {
        if (!empty($user['address']) || !empty($user['province'])) {
            $addresses[] = [
                'id' => 0, 'address' => $user['address'] ?? '',
                'province' => $user['province'] ?? '', 'district' => $user['district'] ?? '',
                'ward' => $user['ward'] ?? '', 'is_default' => 1,
            ];
        }
    }

    echo json_encode(['success' => true, 'user' => $user, 'addresses' => $addresses]);
    exit;
}

// ── CẬP NHẬT THÔNG TIN CÁ NHÂN ──────────────────────────
if ($action === 'update_profile') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone']     ?? '');
    $email     = trim($_POST['email']     ?? '');

    if (!$full_name) {
        echo json_encode(['success' => false, 'message' => 'Họ tên không được để trống.']);
        exit;
    }

    if ($email) {
        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $checkEmail->bind_param("si", $email, $user_id);
        $checkEmail->execute();
        if ($checkEmail->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email này đã được sử dụng bởi tài khoản khác.']);
            $checkEmail->close();
            exit;
        }
        $checkEmail->close();
    }

    $avatarName = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
        $mime    = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Định dạng ảnh không hợp lệ.']);
            exit;
        }
        if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Ảnh quá lớn. Tối đa 2MB.']);
            exit;
        }
        $uploadDir = __DIR__ . '/../assets/imgs/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext        = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $avatarName = 'avatar_' . $user_id . '_' . time() . '.' . strtolower($ext);
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

    $_SESSION['user_name']  = $full_name;
    $_SESSION['user_email'] = $email;
    $initial = mb_strtoupper(mb_substr($full_name, 0, 1, 'UTF-8'), 'UTF-8');
    echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin thành công!', 'full_name' => $full_name, 'initial' => $initial, 'avatar' => $avatarName]);
    exit;
}

// ── THÊM ĐỊA CHỈ MỚI ─────────────────────────────────────
if ($action === 'add_address') {
    $address         = trim($_POST['address']          ?? '');
    $province        = trim($_POST['province']         ?? '');
    $district        = trim($_POST['district']         ?? '');
    $ward            = trim($_POST['ward']             ?? '');
    $recipient_name  = trim($_POST['recipient_name']   ?? '');
    $recipient_phone = trim($_POST['recipient_phone']  ?? '');

    if (!$address) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập số nhà, tên đường.']);
        exit;
    }

    $conn->query("CREATE TABLE IF NOT EXISTS user_addresses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        address VARCHAR(255),
        province VARCHAR(100),
        district VARCHAR(100),
        ward VARCHAR(100),
        recipient_name VARCHAR(150),
        recipient_phone VARCHAR(20),
        is_default TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Tự động thêm cột nếu bảng cũ chưa có
    @$conn->query("ALTER TABLE user_addresses ADD COLUMN recipient_name VARCHAR(150) AFTER ward");
    @$conn->query("ALTER TABLE user_addresses ADD COLUMN recipient_phone VARCHAR(20) AFTER recipient_name");

    $countRes = $conn->prepare("SELECT COUNT(*) as cnt FROM user_addresses WHERE user_id=?");
    $countRes->bind_param("i", $user_id);
    $countRes->execute();
    $cnt = $countRes->get_result()->fetch_assoc()['cnt'];
    $countRes->close();
    $isDefault = ($cnt == 0) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO user_addresses (user_id,address,province,district,ward,recipient_name,recipient_phone,is_default) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("issssssi", $user_id, $address, $province, $district, $ward, $recipient_name, $recipient_phone, $isDefault);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Đã thêm địa chỉ mới!']);
    exit;
}

// ── CẬP NHẬT ĐỊA CHỈ ────────────────────────────────────
if ($action === 'update_address') {
    $address_id      = (int)($_POST['address_id']         ?? 0);
    $address         = trim($_POST['address']             ?? '');
    $province        = trim($_POST['province']            ?? '');
    $district        = trim($_POST['district']            ?? '');
    $ward            = trim($_POST['ward']                ?? '');
    $recipient_name  = trim($_POST['recipient_name']      ?? '');
    $recipient_phone = trim($_POST['recipient_phone']     ?? '');

    // Đảm bảo cột tồn tại
    @$conn->query("ALTER TABLE user_addresses ADD COLUMN recipient_name VARCHAR(150) AFTER ward");
    @$conn->query("ALTER TABLE user_addresses ADD COLUMN recipient_phone VARCHAR(20) AFTER recipient_name");

    if ($address_id > 0) {
        $stmt = $conn->prepare("UPDATE user_addresses SET address=?,province=?,district=?,ward=?,recipient_name=?,recipient_phone=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssssssii", $address, $province, $district, $ward, $recipient_name, $recipient_phone, $address_id, $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("UPDATE users SET address=?,province=?,district=?,ward=? WHERE id=?");
        $stmt->bind_param("ssssi", $address, $province, $district, $ward, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    echo json_encode(['success' => true, 'message' => 'Cập nhật địa chỉ thành công!']);
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
    $stmtReset = $conn->prepare("UPDATE user_addresses SET is_default=0 WHERE user_id=?");
    $stmtReset->bind_param("i", $user_id);
    $stmtReset->execute();
    $stmtReset->close();
    $stmtSet = $conn->prepare("UPDATE user_addresses SET is_default=1 WHERE id=? AND user_id=?");
    $stmtSet->bind_param("ii", $address_id, $user_id);
    $stmtSet->execute();
    $stmtSet->close();
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
    if ($user['oauth_provider'] === null && !password_verify($old_pass, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu cũ không đúng.']); exit;
    }
    $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
    $stmt   = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $hashed, $user_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công!']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action không hợp lệ.']);
