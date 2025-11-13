<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "book_store_db";  // 👈 Tên đúng như trong file SQL của bạn

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
