<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>

<h1>Xin chào <?php echo $_SESSION['user']; ?></h1>
<a href="../actions/logout.php">Đăng xuất</a>