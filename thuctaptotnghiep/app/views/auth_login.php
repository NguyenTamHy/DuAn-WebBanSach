<?php
/** @var string|null $error */
/** @var array|null $old */
?>
<h2>Đăng nhập</h2>

<?php if (!empty($error)): ?>
    <p style="color:red"><?= e($error) ?></p>
<?php endif; ?>

<form method="post" action="">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    <label>Email:
        <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>">
    </label><br>
    <label>Mật khẩu:
        <input type="password" name="password">
    </label><br>
    <button type="submit">Đăng nhập</button>
</form>
