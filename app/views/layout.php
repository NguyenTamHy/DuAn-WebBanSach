<?php
// app/views/layout.php

?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bookstore</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<header>
    <h1><a href="<?= base_url('index.php') ?>"> H&K</a></h1>

    <nav>
        <a href="<?= base_url('index.php') ?>">Trang chủ</a>
        <a href="<?= base_url('index.php?c=cart&a=index') ?>">Giỏ hàng</a>

        <?php if (is_logged_in()): ?>
            <span>Xin chào, <?= e(current_user_name() ?? '') ?></span>
            <a href="<?= base_url('index.php?c=order&a=my') ?>">Đơn hàng</a>
            <?php if (is_admin()): ?>
                <a href="<?= base_url('index.php?c=admin&a=index') ?>">Admin</a>
            <?php endif; ?>
            <a href="<?= base_url('index.php?c=auth&a=logout') ?>">Đăng xuất</a>
        <?php else: ?>
            <a href="<?= base_url('index.php?c=auth&a=login') ?>">Đăng nhập</a>
            <a href="<?= base_url('index.php?c=auth&a=register') ?>">Đăng ký</a>
        <?php endif; ?>
    </nav>
    <hr>
</header>

<main>
    <?php if (!empty($_SESSION['message'])): ?>
        <p style="color:green"><?= e($_SESSION['message']); unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red"><?= e($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php
    // $viewFile đã có từ helpers.php
    include $viewFile;
    ?>
</main>
<!-- tôi mới xóa phần footer cũ rồi nha -->

<?php include __DIR__ . '/../../public/parts/footer.php'; ?>
</body>
</html>
