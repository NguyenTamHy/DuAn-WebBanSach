<?php /** @var array $books */ ?>
<h2>Quản lý sách</h2>

<p><a href="<?= base_url('index.php?c=admin&a=bookCreate') ?>">+ Thêm sách mới</a></p>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Ảnh bìa</th>
        <th>Tiêu đề</th>
        <th>Giá</th>
        <th>Giảm (%)</th>
        <th>Giá sau giảm</th>
        <th>Tồn kho</th>
        <th>Đã bán</th>
        <th>Rating</th>
        <th>Hành động</th>
    </tr>
    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= (int)$b['id'] ?></td>
            <td>
                <?php if (!empty($b['cover_url'])): ?>
                    <img src="<?= e($b['cover_url']) ?>" style="height:60px;">
                <?php endif; ?>
            </td>
            <td><?= e($b['title']) ?></td>
            <td><?= money($b['price']) ?></td>
            <td><?= (int)$b['discount_percent'] ?>%</td>
            <td><?= money(book_effective_price($b)) ?></td>
            <td><?= (int)$b['stock_qty'] ?></td>
            <td><?= (int)($b['sold_qty'] ?? 0) ?></td>
            <td><?= number_format((float)($b['avg_rating'] ?? 0), 1) ?>/5</td>
            <td>
                <a href="<?= base_url('index.php?c=admin&a=bookEdit&id=' . $b['id']) ?>">Sửa</a>
                |
                <form method="post" action="<?= base_url('index.php?c=admin&a=bookDelete') ?>" style="display:inline"
                      onsubmit="return confirm('Xóa sách này?');">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                    <button type="submit">Xóa</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
