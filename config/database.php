<?php
$conn = new mysqli("localhost", "root", "", "dacs1");

if ($conn->connect_error) {
    die("Kết nối DB thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>