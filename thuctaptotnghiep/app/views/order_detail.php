<?php
/** @var array $order */
/** @var array $items */
/** @var array $addr */
?>
<h2>Chi tiết đơn hàng #<?= e($order['code']) ?></h2>

<p>Trạng thái: <?= e($order['status']) ?></p>
<p>Ngày tạo: <?= e($order['created_at']) ?></p>

<h3>Thông tin giao hàng</h3>
<p><?= e($addr['name'] ?? '') ?> - <?= e($addr['phone'] ?? '') ?></p>
<p><?= nl2br(e($addr['address'] ?? '')) ?></p>

<h3>Sản phẩm</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Sách</th>
        <th>Số lượng</th>
        <th>Đơn giá</th>
        <th>Thành tiền</th>
    </tr>
    <?php foreach ($items as $it): ?>
        <tr>
            <td><?= e($it['title_snapshot']) ?></td>
            <td><?= (int)$it['qty'] ?></td>
            <td><?= number_format($it['unit_price'], 0, ',', '.') ?> đ</td>
            <td><?= number_format($it['line_total'], 0, ',', '.') ?> đ</td>
        </tr>
    <?php endforeach; ?>
</table>

<p>Tạm tính: <?= number_format($order['subtotal'], 0, ',', '.') ?> đ</p>
<p>Giảm giá: <?= number_format($order['discount'], 0, ',', '.') ?> đ</p>
<p>Phí ship: <?= number_format($order['shipping_fee'], 0, ',', '.') ?> đ</p>
<p><strong>Tổng cộng: <?= number_format($order['total'], 0, ',', '.') ?> đ</strong></p>
