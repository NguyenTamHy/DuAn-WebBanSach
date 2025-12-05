<?php /** @var array $orders */ ?>
<h2>Quản lý đơn hàng</h2>

<form method="get" action="">
    <input type="hidden" name="c" value="admin">
    <input type="hidden" name="a" value="orders">
    <label>Lọc trạng thái:
        <select name="status" onchange="this.form.submit()">
            <option value="">(Tất cả)</option>
            <?php
            $statuses = ['Pending','Processing','Shipped','Completed','Cancelled'];
            foreach ($statuses as $st):
            ?>
                <option value="<?= $st ?>" <?= (($_GET['status'] ?? '') === $st) ? 'selected' : '' ?>>
                    <?= $st ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
</form>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Mã</th>
        <th>Người dùng</th>
        <th>Tổng tiền</th>
        <th>Trạng thái</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
    </tr>
    <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= (int)$o['id'] ?></td>
            <td><?= e($o['code']) ?></td>
            <td><?= (int)$o['user_id'] ?></td>
            <td><?= number_format($o['total'], 0, ',', '.') ?> đ</td>
            <td><?= e($o['status']) ?></td>
            <td><?= e($o['created_at']) ?></td>
            <td>
                <a href="<?= base_url('index.php?c=order&a=detail&id='.$o['id']) ?>" target="_blank">
                    Xem
                </a>
                <form method="post" action="<?= base_url('index.php?c=admin&a=updateOrderStatus') ?>"
                      style="display:inline">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= $o['id'] ?>">
                    <select name="status">
                        <?php foreach (['Pending','Processing','Shipped','Completed','Cancelled'] as $st): ?>
                            <option value="<?= $st ?>" <?= $o['status'] === $st ? 'selected' : '' ?>>
                                <?= $st ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Cập nhật</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
