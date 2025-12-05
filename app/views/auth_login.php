<h2>Đăng nhập</h2>

<form method="post" action="<?= base_url('index.php?c=auth&a=loginPost') ?>">
    <?= csrf_field(); ?>

    <label>Email:</label><br>
    <input type="email" name="email" required><br>

    <label>Mật khẩu:</label><br>
    <input type="password" name="password" required><br>

    <button type="submit">Đăng nhập</button>
</form>

<p><a href="<?= base_url('index.php?c=auth&a=forgot') ?>">Quên mật khẩu?</a></p>
<p>Chưa có tài khoản? <a href="<?= base_url('index.php?c=auth&a=register') ?>">Đăng ký</a></p>
