<?php
// ============================================================
//  LKSecure — Trang mặc định
//  Truy cập http://localhost/DACS/ sẽ tự chuyển về trang chủ
// ============================================================
header("Location: pages/home.php", true, 301);
exit;