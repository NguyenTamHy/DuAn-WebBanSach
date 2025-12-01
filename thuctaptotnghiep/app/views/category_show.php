<?php
/** @var array $category */
/** @var array $books */
?>
<h2>Thể loại: <?= e($category['name']) ?></h2>

<?php if (empty($books)): ?>
    <p>Chưa có sách trong thể loại này.</p>
<?php else: ?>
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
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
