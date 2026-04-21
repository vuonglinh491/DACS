<?php
include("../config/database.php");

$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// check tồn tại
$check = $conn->query("SELECT * FROM users WHERE email='$email'");
if ($check->num_rows > 0) {
    echo "Email đã tồn tại!";
    exit;
}

$sql = "INSERT INTO users(email, password) VALUES('$email','$password')";

if ($conn->query($sql)) {
    header("Location: ../pages/login.php");
} else {
    echo "Lỗi đăng ký";
}
?>