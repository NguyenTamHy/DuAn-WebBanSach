<h2>Quên mật khẩu</h2>

<form method="post" action="<?= base_url('index.php?c=auth&a=forgotPost') ?>">
    <?= csrf_field(); ?>

    <label>Nhập email đã đăng ký:</label><br>
    <input type="email" name="email" required><br>

    <button type="submit">Gửi yêu cầu</button>
</form>

<p><a href="<?= base_url('index.php?c=auth&a=login') ?>">Quay lại đăng nhập</a></p>
