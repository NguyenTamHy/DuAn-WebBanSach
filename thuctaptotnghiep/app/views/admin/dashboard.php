<?php
/** @var array $stats */
?>
<h2>Admin Dashboard</h2>

<ul>
    <li>Tổng số đơn: <?= (int)$stats['totOrders'] ?></li>
    <li>Đơn Pending: <?= (int)$stats['pending'] ?></li>
    <li>Doanh thu (Completed): <?= number_format($stats['revenue'], 0, ',', '.') ?> đ</li>
</ul>

<h3>Top sách bán chạy</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Tên sách</th>
        <th>Số lượng bán</th>
    </tr>
    <?php foreach ($stats['topBooks'] as $b): ?>
        <tr>
            <td><?= e($b['title_snapshot']) ?></td>
            <td><?= (int)$b['sold_qty'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Sách sắp hết hàng (≤5)</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Tên sách</th>
        <th>Tồn kho</th>
    </tr>
    <?php foreach ($stats['lowStock'] as $b): ?>
        <tr>
            <td><?= e($b['title']) ?></td>
            <td><?= (int)$b['stock_qty'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
