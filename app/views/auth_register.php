<h2>Đăng ký</h2>

<form method="post" action="<?= base_url('index.php?c=auth&a=registerPost') ?>">
    <?= csrf_field(); ?>

    <label>Họ tên:</label><br>
    <input type="text" name="name" required><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br>

    <label>Mật khẩu:</label><br>
    <input type="password" name="password" required><br>

    <label>Nhập lại mật khẩu:</label><br>
    <input type="password" name="password_confirmation" required><br>

    <button type="submit">Đăng ký</button>
</form>
