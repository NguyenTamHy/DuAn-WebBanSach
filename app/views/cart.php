<?php /** @var array $cart */ ?>
<h2>Giỏ hàng</h2>

<?php if (empty($cart['lines'])): ?>
    <p>Giỏ hàng trống.</p>
<?php else: ?>
    <form method="post" action="<?= base_url('index.php?c=cart&a=update') ?>">
        <?= csrf_field(); ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>Sách</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
            <?php foreach ($cart['lines'] as $line): ?>
                <tr>
                    <td><?= e($line['book']['title']) ?></td>
                    <td><?= money($line['unit_price']) ?></td>
                    <td>
                        <input type="number" name="qty[<?= $line['book']['id'] ?>]"
                               value="<?= $line['qty'] ?>" min="0">
                    </td>
                    <td><?= money($line['line_total']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p>Tạm tính: <strong><?= money($cart['subtotal']) ?></strong></p>
        <button type="submit">Cập nhật giỏ</button>
        <a href="<?= base_url('index.php?c=checkout&a=index') ?>">Tiến hành thanh toán</a>
    </form>

    <form method="post" action="<?= base_url('index.php?c=cart&a=clear') ?>" style="margin-top:10px;">
        <?= csrf_field(); ?>
        <button type="submit">Xóa giỏ hàng</button>
    </form>
<?php endif; ?>
