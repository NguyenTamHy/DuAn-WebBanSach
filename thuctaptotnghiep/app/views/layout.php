<?php
// app/views/layout.php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../helpers.php';

$user = auth_user();
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Bookstore</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<header class="site-header">
    <h1><a href="<?= base_url('index.php') ?>">Bookstore</a></h1>
    <nav>
        <a href="<?= base_url('index.php') ?>">Trang chủ</a>
        <a href="<?= base_url('index.php?c=cart&a=index') ?>">Giỏ hàng</a>
        <?php if ($user): ?>
            <a href="<?= base_url('index.php?c=order&a=list') ?>">Đơn hàng của tôi</a>
            <?php if (is_admin()): ?>
                <a href="<?= base_url('index.php?c=admin&a=dashboard') ?>">Admin</a>
            <?php endif; ?>
            <span>Xin chào, <?= e($user['name'] ?: $user['email']) ?></span>
            <a href="<?= base_url('index.php?c=auth&a=logout') ?>">Đăng xuất</a>
        <?php else: ?>
            <a href="<?= base_url('index.php?c=auth&a=login') ?>">Đăng nhập</a>
            <a href="<?= base_url('index.php?c=auth&a=register') ?>">Đăng ký</a>
        <?php endif; ?>
    </nav>
</header>

<main class="site-main">
    <?php include $viewFile; ?>
</main>

<footer class="site-footer">
    <p>Bookstore &copy; <?= date('Y') ?></p>
</footer>
</body>
</html>
