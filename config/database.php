<?php
$conn = new mysqli("localhost", "root", "", "dacs1");
if ($conn->connect_error) {
    die("DB error");
}
?>