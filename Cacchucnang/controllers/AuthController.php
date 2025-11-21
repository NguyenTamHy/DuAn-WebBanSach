<?php
require_once './includes/config.php';
require_once './models/Auth.php';
session_start();

$auth = new Auth($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        $auth->register($_POST['fullname'], $_POST['email'], $_POST['password']);
        header("Location: login.php");
    } elseif (isset($_POST['login'])) {
        $user = $auth->login($_POST['email'], $_POST['password']);
        if ($user) {
            $_SESSION['user'] = $user;
            header("Location: home.php");
        } else {
            echo "Sai tài khoản hoặc mật khẩu!";
        }
    }
}
?>
