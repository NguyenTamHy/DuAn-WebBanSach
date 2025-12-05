<?php
/** @var array $latest */
/** @var array $discounted */
/** @var array $bestSellers */
/** @var array $categories */
?>
<h2>Sách mới nhất</h2>
<div class="book-list">
    <?php foreach ($latest as $b): ?>
        <div class="book-card">
            <?php if (!empty($b['cover_url'])): ?>
                <img src="<?= e($b['cover_url']) ?>" alt="" style="height:120px;">
            <?php endif; ?>
            <h3><a href="<?= base_url('index.php?c=book&a=detail&id=' . $b['id']) ?>"><?= e($b['title']) ?></a></h3>
            <p>Giá: 
                <?php if (book_has_discount($b)): ?>
                    <del><?= money($b['price']) ?></del>
                    <strong><?= money(book_effective_price($b)) ?></strong>
                    (-<?= (int)$b['discount_percent'] ?>%)
                <?php else: ?>
                    <strong><?= money($b['price']) ?></strong>
                <?php endif; ?>
            </p>
        </div>
    <?php endforeach; ?>
</div>

<h2>Sách giảm giá</h2>
<div class="book-list">
    <?php foreach ($discounted as $b): ?>
        <div class="book-card">
            <?php if (!empty($b['cover_url'])): ?>
                <img src="<?= e($b['cover_url']) ?>" alt="" style="height:120px;">
            <?php endif; ?>
            <h3><a href="<?= base_url('index.php?c=book&a=detail&id=' . $b['id']) ?>"><?= e($b['title']) ?></a></h3>
            <p><del><?= money($b['price']) ?></del>
               <strong><?= money(book_effective_price($b)) ?></strong>
               (-<?= (int)$b['discount_percent'] ?>%)
            </p>
        </div>
    <?php endforeach; ?>
</div>

<h2>Bán chạy</h2>
<div class="book-list">
    <?php foreach ($bestSellers as $b): ?>
        <div class="book-card">
            <?php if (!empty($b['cover_url'])): ?>
                <img src="<?= e($b['cover_url']) ?>" alt="" style="height:120px;">
            <?php endif; ?>
            <h3><a href="<?= base_url('index.php?c=book&a=detail&id=' . $b['id']) ?>"><?= e($b['title']) ?></a></h3>
            <p>Giá: <?= money(book_effective_price($b)) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<h2>Thể loại</h2>
<ul>
    <?php foreach ($categories as $cat): ?>
        <li><?= e($cat['name']) ?></li>
    <?php endforeach; ?>
</ul>
