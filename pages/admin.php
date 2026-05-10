<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth_check.php';
requireAdmin(); // Redirect về login nếu không phải admin
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LKSecure — Trang Quản Trị</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="icon" type="image/png" href="../assets/imgs/logo.png">
</head>
<body class="page-admin">

<!-- ============================================================
     SIDEBAR
     ============================================================ -->
<aside class="admin-sidebar" id="adminSidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-shield-halved"></i></div>
        <div class="brand-text">
            <span class="brand-name">LKSecure</span>
            <span class="brand-role">Admin Panel</span>
        </div>
    </div>

    <!-- Admin user info -->
    <div class="sidebar-user">
        <div class="user-avatar">A</div>
        <div class="user-info-text">
            <div class="user-name">Admin LKSecure</div>
            <div class="user-email">admin@lksecure.vn</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">

        <div class="nav-section-label">Tổng quan</div>

        <button class="admin-nav-item active" data-panel="panel-stats">
            <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
            <span class="nav-label">Thống Kê</span>
        </button>

        <div class="nav-section-label" style="margin-top:12px">Quản lý</div>

        <button class="admin-nav-item" data-panel="panel-users">
            <span class="nav-icon"><i class="fas fa-users"></i></span>
            <span class="nav-label">Quản Lý Người Dùng</span>
            <span class="nav-badge">128</span>
        </button>

        <button class="admin-nav-item" data-panel="panel-products">
            <span class="nav-icon"><i class="fas fa-box-open"></i></span>
            <span class="nav-label">Thêm Sản Phẩm</span>
        </button>

        <button class="admin-nav-item" data-panel="panel-orders">
            <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
            <span class="nav-label">Kiểm Tra Đơn Hàng</span>
            <span class="nav-badge orange">12</span>
        </button>

        <div class="nav-section-label" style="margin-top:12px">Khác</div>

        <a href="../pages/home.php" class="admin-nav-item">
            <span class="nav-icon"><i class="fas fa-globe"></i></span>
            <span class="nav-label">Xem trang web</span>
        </a>

        <button class="admin-nav-item" id="adminLogout">
            <span class="nav-icon"><i class="fas fa-right-from-bracket"></i></span>
            <span class="nav-label">Đăng Xuất</span>
        </button>
    </nav>

    <!-- Sidebar footer -->
    <div class="sidebar-footer">
        <div style="font-size:12px;color:rgba(255,255,255,0.25);text-align:center;">
            LKSecure Admin v1.0 &nbsp;·&nbsp; 2026
        </div>
    </div>

</aside>

<!-- ============================================================
     MAIN CONTENT
     ============================================================ -->
<main class="admin-main">

    <!-- Top Bar -->
    <div class="admin-topbar">
        <div class="topbar-left">
            <button class="topbar-icon-btn" id="menuToggle" style="display:none">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <div class="topbar-title" id="topbarTitle">Thống Kê</div>
                <div class="topbar-breadcrumb">Admin / <span id="topbarBread">Thống Kê</span></div>
            </div>
        </div>
        <div class="topbar-right">
            <span id="adminClock" style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.5);font-variant-numeric:tabular-nums"></span>
            <button class="topbar-icon-btn" title="Thông báo">
                <i class="fas fa-bell"></i>
                <span class="notif-dot"></span>
            </button>
            <button class="topbar-icon-btn" title="Cài đặt">
                <i class="fas fa-gear"></i>
            </button>
        </div>
    </div>

    <!-- ============================================================
         CONTENT
         ============================================================ -->
    <div class="admin-content">

        <!-- ==================== PANEL: THỐNG KÊ ==================== -->
        <div class="admin-panel active" id="panel-stats">

            <div class="panel-header">
                <div>
                    <div class="panel-title">Tổng Quan & Thống Kê</div>
                    <div class="panel-subtitle">Dữ liệu cập nhật theo thời gian thực</div>
                </div>
                <div class="date-filter-tabs">
                    <button class="date-filter-tab active">7 ngày</button>
                    <button class="date-filter-tab">30 ngày</button>
                    <button class="date-filter-tab">3 tháng</button>
                    <button class="date-filter-tab">Năm</button>
                </div>
            </div>

            <!-- Stat Cards -->
            <!-- Stat Cards -->
            <?php
            $total_orders   = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'] ?? 0;
            $total_users    = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='customer'")->fetch_assoc()['c'] ?? 0;
            $total_products = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'] ?? 0;
            $total_revenue  = $conn->query("SELECT COALESCE(SUM(total),0) as c FROM orders WHERE status='delivered'")->fetch_assoc()['c'] ?? 0;
            ?>
            <div class="stats-grid">
                <div class="stat-card purple">
                    <div class="stat-card-top">
                        <div class="stat-card-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="stat-change up"><i class="fas fa-arrow-up"></i> Thực tế</div>
                    </div>
                    <div class="stat-value" data-count="<?= $total_revenue ?>">0</div>
                    <div class="stat-label">Doanh thu (VNĐ)</div>
                </div>
                <div class="stat-card cyan">
                    <div class="stat-card-top">
                        <div class="stat-card-icon"><i class="fas fa-shopping-bag"></i></div>
                        <div class="stat-change up"><i class="fas fa-arrow-up"></i> Thực tế</div>
                    </div>
                    <div class="stat-value" data-count="<?= $total_orders ?>">0</div>
                    <div class="stat-label">Đơn hàng</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-card-top">
                        <div class="stat-card-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-change up"><i class="fas fa-arrow-up"></i> Thực tế</div>
                    </div>
                    <div class="stat-value" data-count="<?= $total_users ?>">0</div>
                    <div class="stat-label">Người dùng</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-card-top">
                        <div class="stat-card-icon"><i class="fas fa-box"></i></div>
                        <div class="stat-change down"><i class="fas fa-arrow-down"></i> 2.1%</div>
                    </div>
                    <div class="stat-value" data-count="86">0</div>
                    <div class="stat-label">Sản phẩm đang bán</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="chart-grid">

                <!-- Bar Chart — Doanh thu theo tháng -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <div class="admin-card-title">Doanh thu theo tháng</div>
                        <span style="font-size:12px;color:rgba(255,255,255,0.35)">Triệu VNĐ</span>
                    </div>
                    <div class="admin-card-body" style="padding:0 16px 20px">
                        <div class="bar-chart">
                            <?php
                            $months = ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'];
                            $data   = [42, 58, 47, 63, 55, 71, 68, 82, 77, 91, 85, 110];
                            $max    = max($data);
                            foreach ($months as $i => $m):
                                $height = round(($data[$i] / $max) * 100);
                                $isCyan = ($i % 3 === 2) ? 'cyan' : '';
                            ?>
                            <div class="bar-group">
                                <div class="bar-wrap">
                                    <div class="bar <?= $isCyan ?>" style="height:<?= $height ?>%"
                                         title="<?= $m ?>: <?= $data[$i] ?>M"></div>
                                </div>
                                <div class="bar-label"><?= $m ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Donut Chart — Danh mục -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <div class="admin-card-title">Theo danh mục</div>
                    </div>
                    <div class="admin-card-body">
                        <div class="donut-chart-wrap">
                            <svg class="donut-svg" viewBox="0 0 160 160">
                                <!-- Donut segments drawn via stroke-dasharray trick -->
                                <circle cx="80" cy="80" r="60" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="28"/>
                                <!-- Camera: 38% = 138/360 * 377 ≈ 144 -->
                                <circle cx="80" cy="80" r="60" fill="none" stroke="#8b5cf6" stroke-width="28"
                                        stroke-dasharray="144 233" stroke-dashoffset="0" stroke-linecap="butt"/>
                                <!-- Khóa: 25% = 94 -->
                                <circle cx="80" cy="80" r="60" fill="none" stroke="#22d3ee" stroke-width="28"
                                        stroke-dasharray="94 283" stroke-dashoffset="-144" stroke-linecap="butt"/>
                                <!-- Báo động: 20% = 75 -->
                                <circle cx="80" cy="80" r="60" fill="none" stroke="#10b981" stroke-width="28"
                                        stroke-dasharray="75 302" stroke-dashoffset="-238" stroke-linecap="butt"/>
                                <!-- Khác: 17% = 64 -->
                                <circle cx="80" cy="80" r="60" fill="none" stroke="#f59e0b" stroke-width="28"
                                        stroke-dasharray="64 313" stroke-dashoffset="-313" stroke-linecap="butt"/>
                                <text x="80" y="76" text-anchor="middle" fill="white" font-size="18" font-weight="800" font-family="Be Vietnam Pro, sans-serif">1,240</text>
                                <text x="80" y="94" text-anchor="middle" fill="rgba(255,255,255,0.4)" font-size="11" font-family="Be Vietnam Pro, sans-serif">đơn hàng</text>
                            </svg>
                            <div class="donut-legend">
                                <?php
                                $cats = [
                                    ['Camera an ninh', '#8b5cf6', '38%', 471],
                                    ['Khóa thông minh', '#22d3ee', '25%', 310],
                                    ['Báo động',        '#10b981', '20%', 248],
                                    ['Phụ kiện khác',   '#f59e0b', '17%', 211],
                                ];
                                foreach ($cats as $c): ?>
                                <div class="legend-item">
                                    <span class="legend-name">
                                        <span class="legend-dot" style="background:<?= $c[1] ?>"></span>
                                        <?= $c[0] ?>
                                    </span>
                                    <span style="font-size:12px;color:rgba(255,255,255,0.4);margin-right:10px"><?= $c[2] ?></span>
                                    <span class="legend-value"><?= number_format($c[3]) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Row: Top sản phẩm + Đơn gần đây -->
            <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:20px">

                <!-- Top sản phẩm bán chạy -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <div class="admin-card-title"><i class="fas fa-trophy" style="color:#f59e0b;margin-right:8px"></i>Sản phẩm bán chạy</div>
                    </div>
                    <div class="admin-card-body" style="padding:16px 20px">
                        <?php
                        $top_products = [
                            ['Camera LK-4K Pro', 312, 89],
                            ['Khóa vân tay LK-X1', 247, 70],
                            ['Bộ báo động LK-Alarm', 201, 57],
                            ['Camera PTZ 360°', 188, 53],
                            ['Khóa mật mã LK-Code', 156, 44],
                        ];
                        $ranks = ['gold','silver','bronze','',''];
                        foreach ($top_products as $i => $p): ?>
                        <div class="top-product-row">
                            <div class="top-product-rank <?= $ranks[$i] ?>"><?= $i+1 ?></div>
                            <div style="flex:1">
                                <div class="top-product-name"><?= $p[0] ?></div>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill" data-width="<?= $p[2] ?>%" style="width:0"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="top-product-sales"><?= $p[1] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Đơn hàng gần đây -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <div class="admin-card-title">Đơn hàng gần đây</div>
                        <button class="btn-admin secondary" onclick="document.querySelector('[data-panel=panel-orders]').click()" style="font-size:13px;padding:6px 12px">
                            Xem tất cả <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_orders = [
                                    ['ORD-2847', 'Nguyễn Văn An', '4.200.000đ', 'delivered'],
                                    ['ORD-2846', 'Trần Thị Bình', '8.900.000đ', 'shipping'],
                                    ['ORD-2845', 'Lê Minh Cường', '2.500.000đ', 'processing'],
                                    ['ORD-2844', 'Phạm Thu Dung', '12.700.000đ', 'pending'],
                                    ['ORD-2843', 'Hoàng Văn Em', '3.600.000đ', 'delivered'],
                                ];
                                $status_labels = [
                                    'delivered'  => 'Đã giao',
                                    'shipping'   => 'Đang giao',
                                    'processing' => 'Đang xử lý',
                                    'pending'    => 'Chờ xử lý',
                                    'cancelled'  => 'Đã hủy',
                                ];
                                foreach ($recent_orders as $o): ?>
                                <tr>
                                    <td><strong style="color:var(--primary-light)"><?= $o[0] ?></strong></td>
                                    <td><?= $o[1] ?></td>
                                    <td style="font-weight:700"><?= $o[2] ?></td>
                                    <td><span class="status-badge <?= $o[3] ?>"><?= $status_labels[$o[3]] ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <!-- /panel-stats -->


        <!-- ==================== PANEL: QUẢN LÝ NGƯỜI DÙNG ==================== -->
        <div class="admin-panel" id="panel-users">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Quản Lý Người Dùng</div>
                    <div class="panel-subtitle" id="userTotalCount">128 người dùng</div>
                </div>
                <button class="btn-admin primary" onclick="window.openAdminModal('addUserModal')">
                    <i class="fas fa-user-plus"></i> Thêm người dùng
                </button>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="filter-bar">
                        <div class="admin-search">
                            <i class="fas fa-search"></i>
                            <input type="text" id="userSearch" placeholder="Tìm tên, email...">
                        </div>
                        <select class="admin-select" id="roleFilter">
                            <option value="">Tất cả vai trò</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Nhân viên</option>
                            <option value="customer">Khách hàng</option>
                        </select>
                        <select class="admin-select">
                            <option>Sắp xếp: Mới nhất</option>
                            <option>Sắp xếp: Cũ nhất</option>
                            <option>Tên A-Z</option>
                        </select>
                    </div>
                    <button class="btn-admin secondary">
                        <i class="fas fa-download"></i> Xuất Excel
                    </button>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Vai trò</th>
                                <th>Điện thoại</th>
                                <th>Ngày đăng ký</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <?php
                            $users = [
                                ['Nguyễn Văn An',   'nvan.an@gmail.com',    'admin',    '0912 345 678', '01/12/2023', 'active'],
                                ['Trần Thị Bình',   'thi.binh@gmail.com',   'staff',    '0934 567 890', '15/01/2024', 'active'],
                                ['Lê Minh Cường',   'minh.cuong@gmail.com', 'customer', '0356 789 012', '22/02/2024', 'active'],
                                ['Phạm Thu Dung',   'thu.dung@gmail.com',   'customer', '0778 901 234', '08/03/2024', 'inactive'],
                                ['Hoàng Văn Em',    'van.em@gmail.com',     'customer', '0587 123 456', '14/04/2024', 'active'],
                                ['Vũ Thị Phương',   'thi.phuong@gmail.com', 'staff',    '0901 234 567', '20/05/2024', 'active'],
                                ['Đặng Minh Quân',  'minh.quan@gmail.com',  'customer', '0765 432 109', '03/06/2024', 'active'],
                                ['Bùi Thị Hoa',     'thi.hoa@gmail.com',    'customer', '0849 876 543', '18/07/2024', 'inactive'],
                            ];
                            $role_labels = ['admin'=>'Admin','staff'=>'Nhân viên','customer'=>'Khách hàng'];
                            foreach ($users as $u):
                                $initial = mb_substr($u[0], 0, 1, 'UTF-8');
                            ?>
                            <tr>
                                <td>
                                    <div class="table-user">
                                        <div class="table-avatar"><?= $initial ?></div>
                                        <div>
                                            <div class="table-user-name"><?= $u[0] ?></div>
                                            <div class="table-user-email"><?= $u[1] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="user-role-badge status-badge <?= $u[2]==='admin'?'processing':($u[2]==='staff'?'shipping':'active') ?>"
                                          data-role="<?= $u[2] ?>">
                                        <?= $role_labels[$u[2]] ?>
                                    </span>
                                </td>
                                <td style="color:rgba(255,255,255,0.65)"><?= $u[3] ?></td>
                                <td style="color:rgba(255,255,255,0.5)"><?= $u[4] ?></td>
                                <td>
                                    <span class="status-badge <?= $u[5] ?>">
                                        <?= $u[5]==='active' ? 'Hoạt động' : 'Đã khóa' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn view btn-view-user"
                                            data-name="<?= $u[0] ?>"
                                            data-email="<?= $u[1] ?>"
                                            data-phone="<?= $u[3] ?>"
                                            data-role="<?= $role_labels[$u[2]] ?>"
                                            data-date="<?= $u[4] ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn <?= $u[5]==='active'?'lock':'edit' ?> btn-toggle-user">
                                            <i class="fas <?= $u[5]==='active'?'fa-lock':'fa-unlock' ?>"></i>
                                            <?= $u[5]==='active'?'Khóa':'Mở' ?>
                                        </button>
                                        <button class="action-btn delete btn-delete-user">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="admin-pagination">
                    <span>Hiển thị 1–8 trong 128 người dùng</span>
                    <div class="page-btns">
                        <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <span style="padding:0 4px;color:rgba(255,255,255,0.3)">…</span>
                        <button class="page-btn">16</button>
                        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /panel-users -->


        <!-- ==================== PANEL: THÊM SẢN PHẨM ==================== -->
        <div class="admin-panel" id="panel-products">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Thêm Sản Phẩm Mới</div>
                    <div class="panel-subtitle">Điền đầy đủ thông tin để đăng sản phẩm</div>
                </div>
                <button class="btn-admin secondary" onclick="document.querySelector('[data-panel=panel-products]').click();location.reload()">
                    <i class="fas fa-list"></i> Danh sách sản phẩm
                </button>
            </div>

            <form id="addProductForm" autocomplete="off">
                <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:24px;align-items:start">

                    <!-- Cột trái -->
                    <div style="display:flex;flex-direction:column;gap:20px">

                        <!-- Thông tin cơ bản -->
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <div class="admin-card-title"><i class="fas fa-info-circle" style="color:var(--primary-light);margin-right:8px"></i>Thông tin cơ bản</div>
                            </div>
                            <div class="admin-card-body" style="padding:20px 24px">
                                <div style="display:flex;flex-direction:column;gap:16px">
                                    <div class="admin-form-group">
                                        <label class="admin-label">Tên sản phẩm <span class="required">*</span></label>
                                        <input id="productName" class="admin-input" type="text" placeholder="VD: Camera LK-4K Pro">
                                    </div>
                                    <div class="form-grid">
                                        <div class="admin-form-group">
                                            <label class="admin-label">Danh mục <span class="required">*</span></label>
                                            <select id="productCategory" name="category_id" class="admin-select-full">
                                                <option value="">Chọn danh mục</option>
                                                <?php
                                                $cats = $conn->query("SELECT id, name FROM categories ORDER BY sort_order ASC");
                                                while ($cat = $cats->fetch_assoc()):
                                                ?>
                                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="admin-form-group">
                                            <label class="admin-label">Thương hiệu</label>
                                            <input class="admin-input" type="text" placeholder="LKSecure, Hikvision...">
                                        </div>
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-label">Mô tả ngắn</label>
                                        <textarea class="admin-textarea" placeholder="Mô tả ngắn về sản phẩm (hiển thị trên danh sách)..." style="min-height:80px"></textarea>
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-label">Mô tả chi tiết</label>
                                        <textarea class="admin-textarea" placeholder="Mô tả đầy đủ tính năng, ưu điểm của sản phẩm..." style="min-height:130px"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông số kỹ thuật -->
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <div class="admin-card-title"><i class="fas fa-microchip" style="color:var(--primary-light);margin-right:8px"></i>Thông số kỹ thuật</div>
                                <button type="button" id="addSpecRow" class="btn-admin secondary" style="font-size:12px;padding:6px 12px">
                                    <i class="fas fa-plus"></i> Thêm dòng
                                </button>
                            </div>
                            <div class="admin-card-body" style="padding:12px 20px 20px">
                                <table class="specs-table">
                                    <thead>
                                        <tr>
                                            <th style="font-size:12px;color:rgba(255,255,255,0.4);padding:8px 4px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px">Tên thông số</th>
                                            <th style="font-size:12px;color:rgba(255,255,255,0.4);padding:8px 4px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px">Giá trị</th>
                                            <th style="width:48px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="specsBody">
                                        <?php
                                        $specs = [
                                            ['Độ phân giải', '4K (3840×2160)'],
                                            ['Góc nhìn', '120°'],
                                            ['Hồng ngoại', '30m IR'],
                                            ['Chuẩn kết nối', 'Wi-Fi 2.4GHz / LAN'],
                                        ];
                                        foreach ($specs as $s): ?>
                                        <tr>
                                            <td><input class="admin-input" value="<?= $s[0] ?>" style="margin:0"></td>
                                            <td><input class="admin-input" value="<?= $s[1] ?>" style="margin:0"></td>
                                            <td><button type="button" class="action-btn delete" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <!-- Cột phải -->
                    <div style="display:flex;flex-direction:column;gap:20px">

                        <!-- Giá & kho -->
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <div class="admin-card-title"><i class="fas fa-tags" style="color:var(--primary-light);margin-right:8px"></i>Giá & Kho hàng</div>
                            </div>
                            <div class="admin-card-body" style="padding:20px 24px">
                                <div style="display:flex;flex-direction:column;gap:16px">
                                    <div class="admin-form-group">
                                        <label class="admin-label">Giá bán (VNĐ) <span class="required">*</span></label>
                                        <input id="productPrice" class="admin-input money-input" type="text" placeholder="4.200.000">
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-label">Giá gốc / Giá so sánh</label>
                                        <input class="admin-input money-input" type="text" placeholder="5.000.000">
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-label">SKU / Mã sản phẩm</label>
                                        <input class="admin-input" type="text" placeholder="LK-CAM-4K-001">
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-label">Số lượng tồn kho <span class="required">*</span></label>
                                        <input id="productStock" class="admin-input" type="number" placeholder="50" min="0">
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-label">Trạng thái</label>
                                        <select class="admin-select-full">
                                            <option>Đang bán</option>
                                            <option>Hết hàng</option>
                                            <option>Ngừng kinh doanh</option>
                                            <option>Sắp ra mắt</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hình ảnh -->
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <div class="admin-card-title"><i class="fas fa-images" style="color:var(--primary-light);margin-right:8px"></i>Hình ảnh sản phẩm</div>
                            </div>
                            <div class="admin-card-body" style="padding:16px 20px 20px">
                                <div class="img-upload-zone" id="uploadZone">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Kéo thả ảnh vào đây hoặc <span>chọn file</span></p>
                                    <p style="font-size:12px;margin-top:4px">PNG, JPG, WEBP — Tối đa 5MB/ảnh</p>
                                    <input type="file" id="productImages" multiple accept="image/*" style="display:none">
                                </div>
                                <div class="img-preview-grid" id="imgPreviewGrid"></div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div style="display:flex;gap:10px">
                            <button type="button" class="btn-admin secondary" style="flex:1">
                                <i class="fas fa-save"></i> Lưu nháp
                            </button>
                            <button type="submit" class="btn-admin primary" style="flex:2">
                                <i class="fas fa-check"></i> Lưu sản phẩm
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <!-- /panel-products -->


        <!-- ==================== PANEL: KIỂM TRA ĐƠN HÀNG ==================== -->
        <div class="admin-panel" id="panel-orders">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Kiểm Tra Đơn Hàng</div>
                    <div class="panel-subtitle">Quản lý và cập nhật trạng thái đơn hàng</div>
                </div>
                <button class="btn-admin secondary">
                    <i class="fas fa-download"></i> Xuất báo cáo
                </button>
            </div>

            <!-- Stat cards nhỏ -->
            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px">
                <?php
                $order_stats = [
                    ['Tất cả', 1240, 'fa-list',           '#8b5cf6'],
                    ['Chờ xử lý', 32, 'fa-clock',         '#f59e0b'],
                    ['Đang giao', 87, 'fa-truck',          '#3b82f6'],
                    ['Đã giao', 1098, 'fa-check-circle',   '#10b981'],
                    ['Đã hủy', 23,   'fa-times-circle',    '#ef4444'],
                ];
                foreach ($order_stats as $s): ?>
                <div style="background:#1e293b;border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:16px;text-align:center;cursor:pointer;transition:0.2s"
                     onmouseover="this.style.borderColor='<?= $s[3] ?>55'" onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'">
                    <i class="fas <?= $s[2] ?>" style="font-size:22px;color:<?= $s[3] ?>;margin-bottom:8px"></i>
                    <div style="font-size:22px;font-weight:800;color:#fff"><?= number_format($s[1]) ?></div>
                    <div style="font-size:12px;color:rgba(255,255,255,0.4)"><?= $s[0] ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Filter bar -->
            <div class="order-search-box">
                <div class="admin-form-group" style="flex:2">
                    <label class="admin-label">Tìm kiếm đơn hàng</label>
                    <div class="admin-search" style="max-width:100%">
                        <i class="fas fa-search"></i>
                        <input type="text" id="orderSearch" placeholder="Mã đơn, tên khách, SĐT...">
                    </div>
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Trạng thái</label>
                    <select class="admin-select-full" id="orderStatusFilter">
                        <option value="">Tất cả</option>
                        <option value="pending">Chờ xử lý</option>
                        <option value="processing">Đang xử lý</option>
                        <option value="shipping">Đang giao</option>
                        <option value="delivered">Đã giao</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Từ ngày</label>
                    <input class="admin-input" type="date" style="margin:0">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Đến ngày</label>
                    <input class="admin-input" type="date" style="margin:0">
                </div>
                <div style="padding-bottom:0">
                    <button class="btn-admin primary" style="height:40px">
                        <i class="fas fa-search"></i> Lọc
                    </button>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="admin-card">
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Khách hàng</th>
                                <th>Sản phẩm</th>
                                <th>Tổng tiền</th>
                                <th>Ngày đặt</th>
                                <th>Trạng thái</th>
                                <th>Cập nhật TT</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="orderTableBody">
                            <?php
                            $orders = [
                                ['ORD-2847', 'Nguyễn Văn An',  '0912 345 678', 'Camera LK-4K Pro',     '4.200.000đ', '08/05/2026', 'delivered'],
                                ['ORD-2846', 'Trần Thị Bình',  '0934 567 890', 'Khóa vân tay LK-X1',   '8.900.000đ', '07/05/2026', 'shipping'],
                                ['ORD-2845', 'Lê Minh Cường',  '0356 789 012', 'Bộ báo động LK-Alarm', '2.500.000đ', '07/05/2026', 'processing'],
                                ['ORD-2844', 'Phạm Thu Dung',  '0778 901 234', 'Camera PTZ 360°',       '12.700.000đ','06/05/2026', 'pending'],
                                ['ORD-2843', 'Hoàng Văn Em',   '0587 123 456', 'Khóa mật mã LK-Code',  '3.600.000đ', '06/05/2026', 'delivered'],
                                ['ORD-2842', 'Vũ Thị Phương',  '0901 234 567', 'Camera LK-4K Pro ×2',  '8.400.000đ', '05/05/2026', 'shipping'],
                                ['ORD-2841', 'Đặng Minh Quân', '0765 432 109', 'Phụ kiện cáp RG59',    '750.000đ',   '05/05/2026', 'cancelled'],
                                ['ORD-2840', 'Bùi Thị Hoa',    '0849 876 543', 'Bộ kit combo LK-Home', '18.500.000đ','04/05/2026', 'delivered'],
                            ];
                            $status_labels = [
                                'delivered'=>'Đã giao','shipping'=>'Đang giao',
                                'processing'=>'Đang xử lý','pending'=>'Chờ xử lý','cancelled'=>'Đã hủy'
                            ];
                            foreach ($orders as $o):
                                $initial = mb_substr($o[1], 0, 1, 'UTF-8');
                            ?>
                            <tr>
                                <td><strong style="color:var(--primary-light)"><?= $o[0] ?></strong></td>
                                <td>
                                    <div class="table-user">
                                        <div class="table-avatar" style="width:30px;height:30px;font-size:12px"><?= $initial ?></div>
                                        <div>
                                            <div style="font-weight:600;font-size:13px;color:#fff"><?= $o[1] ?></div>
                                            <div style="font-size:11px;color:rgba(255,255,255,0.4)"><?= $o[2] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:13px;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= $o[3] ?></td>
                                <td style="font-weight:700;color:#fff"><?= $o[4] ?></td>
                                <td style="color:rgba(255,255,255,0.5);font-size:13px"><?= $o[5] ?></td>
                                <td>
                                    <span class="status-badge <?= $o[6] ?>" data-status="<?= $o[6] ?>">
                                        <?= $status_labels[$o[6]] ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px;align-items:center">
                                        <select class="admin-select" style="padding:5px 8px;font-size:12px">
                                            <option value="pending" <?= $o[6]==='pending'?'selected':'' ?>>Chờ xử lý</option>
                                            <option value="processing" <?= $o[6]==='processing'?'selected':'' ?>>Đang xử lý</option>
                                            <option value="shipping" <?= $o[6]==='shipping'?'selected':'' ?>>Đang giao</option>
                                            <option value="delivered" <?= $o[6]==='delivered'?'selected':'' ?>>Đã giao</option>
                                            <option value="cancelled" <?= $o[6]==='cancelled'?'selected':'' ?>>Đã hủy</option>
                                        </select>
                                        <button class="action-btn view btn-update-status" title="Cập nhật">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn view btn-view-order" data-id="<?= substr($o[0],4) ?>">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="admin-pagination">
                    <span>Hiển thị 1–8 trong 1.240 đơn hàng</span>
                    <div class="page-btns">
                        <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <span style="padding:0 4px;color:rgba(255,255,255,0.3)">…</span>
                        <button class="page-btn">155</button>
                        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /panel-orders -->

    </div><!-- /admin-content -->
</main>

<!-- ============================================================
     MODALS
     ============================================================ -->

<!-- Modal: Chi tiết người dùng -->
<div class="admin-modal-overlay" id="userDetailModal">
    <div class="admin-modal">
        <div class="modal-header">
            <span class="modal-title"><i class="fas fa-user-circle" style="color:var(--primary-light);margin-right:8px"></i>Chi tiết người dùng</span>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding:16px;background:rgba(139,92,246,0.08);border-radius:12px;border:1px solid rgba(139,92,246,0.2)">
                <div class="table-avatar" style="width:54px;height:54px;font-size:22px" id="modalUserAvatarLarge">N</div>
                <div>
                    <div style="font-size:18px;font-weight:800;color:#fff" id="modalUserName">—</div>
                    <div style="font-size:13px;color:rgba(255,255,255,0.5);margin-top:3px" id="modalUserEmail">—</div>
                </div>
            </div>
            <div class="order-info-row"><span class="order-info-label">Điện thoại</span><span class="order-info-value" id="modalUserPhone">—</span></div>
            <div class="order-info-row"><span class="order-info-label">Vai trò</span><span class="order-info-value" id="modalUserRole">—</span></div>
            <div class="order-info-row"><span class="order-info-label">Ngày đăng ký</span><span class="order-info-value" id="modalUserDate">—</span></div>
            <div class="order-info-row"><span class="order-info-label">Tổng đơn hàng</span><span class="order-info-value">7 đơn</span></div>
            <div class="order-info-row"><span class="order-info-label">Tổng chi tiêu</span><span class="order-info-value" style="color:var(--primary-light)">24.500.000đ</span></div>
        </div>
        <div class="modal-footer">
            <button class="btn-admin secondary modal-close">Đóng</button>
            <button class="btn-admin primary">Chỉnh sửa</button>
        </div>
    </div>
</div>

<!-- Modal: Chi tiết đơn hàng -->
<div class="admin-modal-overlay" id="orderDetailModal">
    <div class="admin-modal" style="max-width:680px">
        <div class="modal-header">
            <span class="modal-title"><i class="fas fa-clipboard-list" style="color:var(--primary-light);margin-right:8px"></i>Chi tiết đơn hàng <span id="modalOrderId">#—</span></span>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <!-- Timeline trạng thái -->
            <div style="margin-bottom:24px">
                <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.45);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:14px">Trạng thái đơn hàng</div>
                <div class="order-timeline">
                    <div class="order-timeline-item">
                        <div class="timeline-dot-wrap done"><i class="fas fa-check"></i></div>
                        <div class="timeline-info">
                            <div class="timeline-step-title">Đặt hàng thành công</div>
                            <div class="timeline-step-time">08/05/2026 — 09:32</div>
                        </div>
                    </div>
                    <div class="order-timeline-item">
                        <div class="timeline-dot-wrap done"><i class="fas fa-check"></i></div>
                        <div class="timeline-info">
                            <div class="timeline-step-title">Xác nhận & đang xử lý</div>
                            <div class="timeline-step-time">08/05/2026 — 10:15</div>
                        </div>
                    </div>
                    <div class="order-timeline-item">
                        <div class="timeline-dot-wrap current"><i class="fas fa-truck"></i></div>
                        <div class="timeline-info">
                            <div class="timeline-step-title">Đang giao hàng</div>
                            <div class="timeline-step-time">Dự kiến: 09/05/2026</div>
                        </div>
                    </div>
                    <div class="order-timeline-item">
                        <div class="timeline-dot-wrap upcoming"><i class="fas fa-home"></i></div>
                        <div class="timeline-info">
                            <div class="timeline-step-title upcoming">Giao hàng thành công</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin đơn -->
            <div class="order-detail-grid">
                <div>
                    <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.4);margin-bottom:10px;text-transform:uppercase;letter-spacing:0.8px">Thông tin khách</div>
                    <div class="order-info-row"><span class="order-info-label">Tên</span><span class="order-info-value">Trần Thị Bình</span></div>
                    <div class="order-info-row"><span class="order-info-label">SĐT</span><span class="order-info-value">0934 567 890</span></div>
                    <div class="order-info-row"><span class="order-info-label">Địa chỉ</span><span class="order-info-value" style="text-align:right;font-size:13px">123 Lê Lợi, Q1, TP.HCM</span></div>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.4);margin-bottom:10px;text-transform:uppercase;letter-spacing:0.8px">Thanh toán</div>
                    <div class="order-info-row"><span class="order-info-label">Phương thức</span><span class="order-info-value">COD</span></div>
                    <div class="order-info-row"><span class="order-info-label">Tổng tiền</span><span class="order-info-value" style="color:var(--primary-light)">8.900.000đ</span></div>
                    <div class="order-info-row"><span class="order-info-label">Phí ship</span><span class="order-info-value">Miễn phí</span></div>
                </div>
            </div>

            <!-- Sản phẩm -->
            <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.4);margin:16px 0 10px;text-transform:uppercase;letter-spacing:0.8px">Sản phẩm đặt</div>
            <div class="order-product-row">
                <div class="order-product-img"><i class="fas fa-camera"></i></div>
                <div>
                    <div class="order-product-name">Khóa vân tay LK-X1</div>
                    <div class="order-product-meta">Số lượng: 1 × 8.900.000đ</div>
                </div>
                <div class="order-product-price">8.900.000đ</div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-admin secondary modal-close">Đóng</button>
            <button class="btn-admin danger"><i class="fas fa-times"></i> Hủy đơn</button>
            <button class="btn-admin primary"><i class="fas fa-print"></i> In hóa đơn</button>
        </div>
    </div>
</div>

<!-- Modal: Thêm người dùng -->
<div class="admin-modal-overlay" id="addUserModal">
    <div class="admin-modal">
        <div class="modal-header">
            <span class="modal-title"><i class="fas fa-user-plus" style="color:var(--primary-light);margin-right:8px"></i>Thêm người dùng mới</span>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div style="display:flex;flex-direction:column;gap:14px">
                <div class="admin-form-group">
                    <label class="admin-label">Họ và tên <span class="required">*</span></label>
                    <input class="admin-input" type="text" placeholder="Nguyễn Văn A">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Email <span class="required">*</span></label>
                    <input class="admin-input" type="email" placeholder="example@gmail.com">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Số điện thoại</label>
                    <input class="admin-input" type="tel" placeholder="09xx xxx xxx">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Vai trò</label>
                    <select class="admin-select-full">
                        <option>Khách hàng</option>
                        <option>Nhân viên</option>
                        <option>Admin</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Mật khẩu tạm <span class="required">*</span></label>
                    <input class="admin-input" type="password" placeholder="Tối thiểu 8 ký tự">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-admin secondary modal-close">Hủy</button>
            <button class="btn-admin primary" onclick="window.closeAdminModal('addUserModal');window.showAdminToast('Đã thêm người dùng thành công!','success')">
                <i class="fas fa-check"></i> Tạo tài khoản
            </button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="admin-toast" id="adminToast">
    <i class="fas fa-check-circle toast-icon"></i>
    <span>Thao tác thành công!</span>
</div>

<script src="../assets/js/main.js"></script>
<script src="../assets/js/admin.js"></script>
</body>
</html>