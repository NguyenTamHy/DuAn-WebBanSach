<?php
/** @var array $books */
/** @var array $categories */
/** @var string $keyword */
?>
<h2>Danh sách sách</h2>

<form method="get" action="">
    <input type="hidden" name="c" value="home">
    <input type="hidden" name="a" value="index">
    <input type="text" name="q" placeholder="Tìm kiếm sách..." value="<?= e($keyword) ?>">
    <button type="submit">Tìm</button>
</form>

<h3>Thể loại</h3>
<ul>
    <?php foreach ($categories as $cat): ?>
        <li>
            <a href="<?= base_url('index.php?c=category&a=show&slug=' . urlencode($cat['slug'])) ?>">
                <?= e($cat['name']) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<div class="book-grid">
    <?php foreach ($books as $b): ?>
        <div class="book-item">
            <?php if ($b['cover_url']): ?>
                <img src="<?= e($b['cover_url']) ?>" alt="<?= e($b['title']) ?>" class="book-cover">
            <?php endif; ?>
            <h3>
                <a href="<?= base_url('index.php?c=book&a=detail&id='.$b['id']) ?>">
                    <?= e($b['title']) ?>
                </a>
            </h3>
            <p>Giá: <?= number_format($b['price'], 0, ',', '.') ?> đ</p>
            <form method="post" action="<?= base_url('index.php?c=cart&a=add') ?>">
                <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                <input type="hidden" name="qty" value="1">
                <button type="submit">Thêm vào giỏ</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
