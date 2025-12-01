<?php
/** @var array $book */
/** @var array $reviews */
/** @var array|null $ratingStats */
/** @var array $authors */
/** @var array $categories */
?>
<h2><?= e($book['title']) ?></h2>

<p>
    <?php if (!empty($authors)): ?>
        Tác giả:
        <?php foreach ($authors as $idx => $a): ?>
            <?= $idx > 0 ? ', ' : '' ?><?= e($a['name']) ?>
        <?php endforeach; ?><br>
    <?php endif; ?>

    <?php if (!empty($categories)): ?>
        Thể loại:
        <?php foreach ($categories as $idx => $c): ?>
            <?= $idx > 0 ? ', ' : '' ?>
            <a href="<?= base_url('index.php?c=category&a=show&slug=' . urlencode($c['slug'])) ?>">
                <?= e($c['name']) ?>
            </a>
        <?php endforeach; ?><br>
    <?php endif; ?>

    <?php if (!empty($book['publisher_name'])): ?>
        Nhà xuất bản: <?= e($book['publisher_name']) ?><br>
    <?php endif; ?>
</p>

<p>Giá: <?= number_format($book['price'], 0, ',', '.') ?> đ</p>
<?php if ($book['cover_url']): ?>
    <img src="<?= e($book['cover_url']) ?>" alt="<?= e($book['title']) ?>" style="max-width:200px">
<?php endif; ?>
<p><?= nl2br(e($book['description'] ?? '')) ?></p>

<form method="post" action="<?= base_url('index.php?c=cart&a=add') ?>">
    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
    <label>Số lượng:
        <input type="number" name="qty" value="1" min="1">
    </label>
    <button type="submit">Thêm vào giỏ</button>
</form>

<hr>
<h3>Đánh giá</h3>
<?php if ($ratingStats && $ratingStats['cnt'] > 0): ?>
    <p>Điểm trung bình: <?= number_format($ratingStats['avg_rating'], 1) ?>/5 (<?= $ratingStats['cnt'] ?> lượt)</p>
<?php endif; ?>

<?php foreach ($reviews as $r): ?>
    <div class="review-item">
        <strong><?= e($r['user_name']) ?></strong>
        <span>(<?= (int)$r['rating'] ?>/5)</span>
        <p><?= nl2br(e($r['comment'])) ?></p>
        <small><?= e($r['created_at']) ?></small>
    </div>
<?php endforeach; ?>

<?php if (auth_user()): ?>
    <h4>Viết đánh giá</h4>
    <form method="post" action="<?= base_url('index.php?c=book&a=addReview') ?>">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
        <label>Điểm:
            <select name="rating">
                <?php for ($i=5;$i>=1;$i--): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </label><br>
        <label>Nhận xét:<br>
            <textarea name="comment" rows="3"></textarea>
        </label><br>
        <button type="submit">Gửi đánh giá</button>
    </form>
<?php else: ?>
    <p><a href="<?= base_url('index.php?c=auth&a=login') ?>">Đăng nhập</a> để viết đánh giá.</p>
<?php endif; ?>
