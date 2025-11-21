<?php
session_start();

function require_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: /bookstore/login.php');
        exit();
    }
}

function require_admin() {
    require_login();
    // assuming role field is 'role' and admin value is 'admin' (lowercase)
    if (!isset($_SESSION['user']['role']) || strtolower($_SESSION['user']['role']) !== 'admin') {
        header('HTTP/1.1 403 Forbidden');
        echo "Bạn không có quyền truy cập trang này.";
        exit();
    }
}
?>
