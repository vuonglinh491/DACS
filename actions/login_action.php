<?php
session_start();
include("../config/database.php");

$email = $_POST['email'];
$password = $_POST['password'];

$result = $conn->query("SELECT * FROM users WHERE email='$email'");

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = $email;
        header("Location: ../pages/home.php");
    } else {
        echo "Sai mật khẩu!";
    }
} else {
    echo "Không tìm thấy tài khoản!";
}
?>