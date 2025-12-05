<?php
/** @var array $order */
/** @var array $items */
$addr = json_decode($order['addr_json'] ?? '{}', true) ?: [];
?>
<h2>Chi tiết đơn hàng #<?= e($order['code']) ?></h2>

<p>Ngày đặt: <?= e($order['created_at']) ?></p>
<p>Trạng thái: <?= e($order['status']) ?></p>
<p>Thanh toán: <?= e($order['payment_method']) ?></p>

<h3>Người nhận</h3>
<ul>
    <li>Họ tên: <?= e($addr['name'] ?? '') ?></li>
    <li>Điện thoại: <?= e($addr['phone'] ?? '') ?></li>
    <li>Địa chỉ: <?= e($addr['address'] ?? '') ?></li>
</ul>

<h3>Sản phẩm</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Sách</th>
        <th>SL</th>
        <th>Đơn giá</th>
        <th>Thành tiền</th>
    </tr>
    <?php foreach ($items as $it): ?>
        <tr>
            <td><?= e($it['title']) ?></td>
            <td><?= (int)$it['qty'] ?></td>
            <td><?= money($it['price']) ?></td>
            <td><?= money($it['price'] * $it['qty']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<p>Tạm tính: <?= money($order['subtotal']) ?></p>
<p>Phí ship: <?= money($order['shipping_fee']) ?></p>
<p>Giảm giá: <?= money($order['discount']) ?></p>
<p><strong>Tổng: <?= money($order['total']) ?></strong></p>
