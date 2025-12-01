<?php /** @var array $cart */ ?>
<h2>Giỏ hàng</h2>

<?php if (empty($cart['lines'])): ?>
    <p>Giỏ hàng trống.</p>
<?php else: ?>
    <form method="post" action="<?= base_url('index.php?c=cart&a=update') ?>">
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
                    <td><?= number_format($line['book']['price'], 0, ',', '.') ?> đ</td>
                    <td>
                        <input type="number" name="qty[<?= $line['book']['id'] ?>]"
                               value="<?= $line['qty'] ?>" min="0">
                    </td>
                    <td><?= number_format($line['line_total'], 0, ',', '.') ?> đ</td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p>Tạm tính: <strong><?= number_format($cart['subtotal'], 0, ',', '.') ?> đ</strong></p>
        <button type="submit">Cập nhật giỏ</button>
        <a href="<?= base_url('index.php?c=checkout&a=index') ?>">Tiến hành thanh toán</a>
        <a href="<?= base_url('index.php?c=cart&a=clear') ?>">Xóa giỏ hàng</a>
    </form>
<?php endif; ?>
