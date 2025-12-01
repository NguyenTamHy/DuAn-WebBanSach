<?php /** @var array $books */ ?>
<h2>Quản lý sách</h2>

<p><a href="<?= base_url('index.php?c=admin&a=bookForm') ?>">+ Thêm sách</a></p>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Tiêu đề</th>
        <th>Giá</th>
        <th>Tồn kho</th>
        <th>Hành động</th>
    </tr>
    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= (int)$b['id'] ?></td>
            <td><?= e($b['title']) ?></td>
            <td><?= number_format($b['price'], 0, ',', '.') ?> đ</td>
            <td><?= (int)$b['stock_qty'] ?></td>
            <td>
                <a href="<?= base_url('index.php?c=admin&a=bookForm&id='.$b['id']) ?>">Sửa</a>
                <form method="post" action="<?= base_url('index.php?c=admin&a=bookDelete') ?>"
                      style="display:inline"
                      onsubmit="return confirm('Xóa sách này?')">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= $b['id'] ?>">
                    <button type="submit">Xóa</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
