<?php

$servername = "localhost";
$username   = "root";
$password   = "Ai1000!";
$dbname     = "thuctaptotnghiep";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die(" Kết nối thất bại: " . mysqli_connect_error());
}

if (!mysqli_set_charset($conn, "utf8mb4")) {
    die(" Không thể thiết lập charset: " . mysqli_error($conn));
}

?>
