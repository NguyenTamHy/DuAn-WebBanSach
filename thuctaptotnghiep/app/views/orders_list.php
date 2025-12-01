<?php /** @var array $orders */ ?>
<h2>Đơn hàng của tôi</h2>

<?php if (empty($orders)): ?>
    <p>Bạn chưa có đơn hàng nào.</p>
<?php else: ?>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Mã</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Chi tiết</th>
        </tr>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= (int)$o['id'] ?></td>
                <td><?= e($o['code']) ?></td>
                <td><?= number_format($o['total'], 0, ',', '.') ?> đ</td>
                <td><?= e($o['status']) ?></td>
                <td><?= e($o['created_at']) ?></td>
                <td>
                    <a href="<?= base_url('index.php?c=order&a=detail&id='.$o['id']) ?>">Xem</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
