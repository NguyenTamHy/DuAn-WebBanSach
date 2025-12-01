<?php
/** @var array|null $errors */
/** @var array|null $old */
?>
<h2>Đăng ký</h2>

<?php if (!empty($errors)): ?>
    <ul style="color:red">
        <?php foreach ($errors as $er): ?>
            <li><?= e($er) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    <label>Họ tên:
        <input type="text" name="name" value="<?= e($old['name'] ?? '') ?>">
    </label><br>
    <label>Số điện thoại:
        <input type="text" name="phone" value="<?= e($old['phone'] ?? '') ?>">
    </label><br>
    <label>Email:
        <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>">
    </label><br>
    <label>Mật khẩu:
        <input type="password" name="password">
    </label><br>
    <label>Nhập lại mật khẩu:
        <input type="password" name="password_confirm">
    </label><br>
    <button type="submit">Đăng ký</button>
</form>
