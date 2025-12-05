<?php
/** @var array $cart */
/** @var float $shipping */
/** @var float $discount */
/** @var float $total */
?>
<h2>Thanh toán</h2>

<h3>Thông tin đơn hàng</h3>
<ul>
    <?php foreach ($cart['lines'] as $line): ?>
        <li>
            <?= e($line['book']['title']) ?> x <?= (int)$line['qty'] ?> =
            <?= money($line['line_total']) ?>
        </li>
    <?php endforeach; ?>
</ul>

<p>Tạm tính: <?= money($cart['subtotal']) ?></p>
<p>Phí ship: <?= money($shipping) ?></p>
<p>Giảm giá: <?= money($discount) ?></p>
<p><strong>Tổng cộng: <?= money($total) ?></strong></p>

<h3>Thông tin nhận hàng</h3>
<form method="post" action="<?= base_url('index.php?c=checkout&a=placeOrder') ?>">
    <?= csrf_field(); ?>
    <label>Họ tên:</label><br>
    <input type="text" name="name" required><br>

    <label>Điện thoại:</label><br>
    <input type="text" name="phone" required><br>

    <label>Địa chỉ:</label><br>
    <textarea name="address" rows="3" required></textarea><br>

    <label>Phương thức thanh toán:</label><br>
    <select name="payment_method">
        <option value="COD">Thanh toán khi nhận hàng (COD)</option>
        <option value="MOMO">QR MoMo</option>
        <option value="BANK">Chuyển khoản ngân hàng (QR)</option>
    </select>

    <p>Nếu chọn MOMO/BANK, bạn có thể hiển thị ảnh mã QR cố định ở đây (hardcode).</p>

    <button type="submit">Đặt hàng</button>
</form>
