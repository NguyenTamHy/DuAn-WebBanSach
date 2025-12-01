<?php
/** @var array $cart */
/** @var array|null $user */
/** @var string $coupon_code */
/** @var float $discount */
/** @var float $shipping_fee */
?>
<h2>Thanh toán</h2>

<?php if (!empty($_SESSION['coupon_error'])): ?>
    <p style="color:red"><?= e($_SESSION['coupon_error']) ?></p>
    <?php unset($_SESSION['coupon_error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['checkout_error'])): ?>
    <p style="color:red"><?= e($_SESSION['checkout_error']) ?></p>
    <?php unset($_SESSION['checkout_error']); ?>
<?php endif; ?>

<p>Tạm tính: <strong><?= number_format($cart['subtotal'], 0, ',', '.') ?> đ</strong></p>

<form method="post" action="<?= base_url('index.php?c=checkout&a=applyCoupon') ?>">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    <label>Mã giảm giá:
        <input type="text" name="coupon_code" value="<?= e($coupon_code) ?>">
    </label>
    <button type="submit">Áp dụng</button>
</form>

<p>Giảm giá: <strong><?= number_format($discount, 0, ',', '.') ?> đ</strong></p>
<p>Phí ship: <strong><?= number_format($shipping_fee, 0, ',', '.') ?> đ</strong></p>
<p>
    Tổng phải trả:
    <strong><?= number_format($cart['subtotal'] - $discount + $shipping_fee, 0, ',', '.') ?> đ</strong>
</p>

<hr>

<form method="post" action="<?= base_url('index.php?c=checkout&a=placeOrder') ?>">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    <label>Họ tên người nhận:
        <input type="text" name="name" value="<?= e($user['name'] ?? '') ?>">
    </label><br>
    <label>Số điện thoại:
        <input type="text" name="phone" value="<?= e($user['phone'] ?? '') ?>">
    </label><br>
    <label>Địa chỉ:
        <textarea name="address" rows="3"></textarea>
    </label><br>
    <label>Phương thức thanh toán:
        <select name="payment_method">
            <option value="COD">Thanh toán khi nhận hàng (COD)</option>
            <option value="MOMO">MOMO</option>
            <option value="BANK">Chuyển khoản ngân hàng</option>
        </select>
    </label><br>
    <button type="submit">Đặt hàng</button>
</form>
