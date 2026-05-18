<?php
// ============================================================
//  LKSecure — Thêm / Xóa sản phẩm (dùng bởi admin panel)
// ============================================================

// ✅ ob_start() ngăn PHP warning/notice phá vỡ JSON output
ob_start();
require_once __DIR__ . '/../config/database.php';
ob_clean(); // xoá output thừa trước JSON
header('Content-Type: application/json; charset=utf-8');

// Chỉ admin mới được phép
require_once __DIR__ . '/../config/auth_check.php';
requireAdmin(true); // true = trả JSON thay vì redirect

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ── THÊM SẢN PHẨM ────────────────────────────────────────
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $name          = trim($_POST['name']          ?? '');
    $category_id   = (int)($_POST['category_id']  ?? 0);
    $brand         = trim($_POST['brand']          ?? '');
    $short_desc    = trim($_POST['short_desc']     ?? '');
    $description   = trim($_POST['description']    ?? '');
    $price         = (int)str_replace(['.', ',', ' '], '', $_POST['price'] ?? 0);
    $original_price= (int)str_replace(['.', ',', ' '], '', $_POST['original_price'] ?? 0);
    $stock         = (int)($_POST['stock']         ?? 0);
    $sku           = trim($_POST['sku']            ?? '') ?: null;
    $status        = $_POST['status']              ?? 'active';

    if (!$name || !$category_id || !$price) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc.']);
        exit;
    }

    // Upload ảnh chính
    $image_name = null;
    if (!empty($_FILES['image']['name'])) {
        $ext        = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed    = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5 * 1024 * 1024) {
            $image_name = 'product_' . uniqid() . '.' . $ext;
            $upload_dir = __DIR__ . '/../assets/imgs/';
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
        }
    }

    $stmt = $conn->prepare(
        "INSERT INTO products (category_id, sku, name, brand, short_desc, description, price, original_price, stock, image, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "isssssiiiiss",
        $category_id, $sku, $name, $brand, $short_desc, $description,
        $price, $original_price, $stock, $image_name, $status
    );

    if ($stmt->execute()) {
        $product_id = $conn->insert_id;
        $stmt->close();

        // Lưu thông số kỹ thuật nếu có
        $spec_names  = $_POST['spec_name']  ?? [];
        $spec_values = $_POST['spec_value'] ?? [];
        if (!empty($spec_names)) {
            $s = $conn->prepare("INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order) VALUES (?,?,?,?)");
            foreach ($spec_names as $i => $sname) {
                $sname  = trim($sname);
                $svalue = trim($spec_values[$i] ?? '');
                if ($sname && $svalue) {
                    $order = $i;
                    $s->bind_param("issi", $product_id, $sname, $svalue, $order);
                    $s->execute();
                }
            }
            $s->close();
        }

        echo json_encode(['success' => true, 'message' => 'Thêm sản phẩm thành công!', 'product_id' => $product_id]);
    } else {
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $conn->error]);
    }
    exit;
}

// ── XÓA SẢN PHẨM ─────────────────────────────────────────
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ.']);
        exit;
    }
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã xóa sản phẩm.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa.']);
    }
    $stmt->close();
    exit;
}

// ── LẤY DANH SÁCH SẢN PHẨM (JSON cho admin) ─────────────
if ($action === 'list' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query(
        "SELECT p.id, p.name, p.price, p.stock, p.status, p.image, c.name AS category
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         ORDER BY p.id DESC"
    );
    $products = [];
    while ($row = $result->fetch_assoc()) $products[] = $row;
    echo json_encode(['success' => true, 'data' => $products]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action không hợp lệ.']);
