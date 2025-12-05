<?php /** @var string $token */ ?>
<h2>Đặt lại mật khẩu</h2>

<form method="post" action="<?= base_url('index.php?c=auth&a=resetPost') ?>">
    <?= csrf_field(); ?>
    <input type="hidden" name="token" value="<?= e($token) ?>">

    <label>Mật khẩu mới:</label><br>
    <input type="password" name="password" required><br>

    <label>Nhập lại mật khẩu mới:</label><br>
    <input type="password" name="password_confirmation" required><br>

    <button type="submit">Lưu mật khẩu mới</button>
</form>
