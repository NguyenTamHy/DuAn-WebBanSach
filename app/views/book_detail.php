<?php
/** @var array $book */
/** @var array $related */
/** @var array $reviews */
/** @var float $avgRating */
?>
<h2><?= e($book['title']) ?></h2>

<?php if (!empty($book['cover_url'])): ?>
    <img src="<?= e($book['cover_url']) ?>" alt="" style="height:180px;">
<?php endif; ?>

<p>
    Giá:
    <?php if (book_has_discount($book)): ?>
        <del><?= money($book['price']) ?></del>
        <strong><?= money(book_effective_price($book)) ?></strong>
        (-<?= (int)$book['discount_percent'] ?>%)
    <?php else: ?>
        <strong><?= money($book['price']) ?></strong>
    <?php endif; ?>
</p>

<p>Mô tả: <?= nl2br(e($book['description'] ?? '')) ?></p>

<p>Đánh giá trung bình: <?= number_format($avgRating, 1) ?>/5</p>

<form method="post" action="<?= base_url('index.php?c=cart&a=add') ?>">
    <?= csrf_field(); ?>
    <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
    <label>Số lượng:</label>
    <input type="number" name="qty" value="1" min="1">
    <button type="submit">Thêm vào giỏ</button>
</form>

<hr>

<h3>Đánh giá & bình luận</h3>

<?php if (is_logged_in()): ?>
    <form method="post" action="<?= base_url('index.php?c=book&a=reviewPost') ?>">
        <?= csrf_field(); ?>
        <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
        <label>Chọn sao:</label>
        <select name="rating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?> sao</option>
            <?php endfor; ?>
        </select>
        <br>
        <label>Bình luận:</label><br>
        <textarea name="comment" rows="3" cols="50"></textarea><br>
        <button type="submit">Gửi đánh giá</button>
    </form>
<?php else: ?>
    <p><a href="<?= base_url('index.php?c=auth&a=login') ?>">Đăng nhập</a> để đánh giá.</p>
<?php endif; ?>

<ul>
    <?php foreach ($reviews as $r): ?>
        <li>
            <strong><?= e($r['user_name'] ?? 'Ẩn danh') ?></strong>
            - <?= (int)$r['rating'] ?>/5 sao
            <br>
            <small><?= e($r['created_at']) ?></small>
            <p><?= nl2br(e($r['comment'] ?? '')) ?></p>
        </li>
    <?php endforeach; ?>
</ul>

<hr>

<h3>Sách liên quan</h3>
<div class="book-list">
    <?php foreach ($related as $b): ?>
        <?php if ($b['id'] == $book['id']) continue; ?>
        <div class="book-card">
            <?php if (!empty($b['cover_url'])): ?>
                <img src="<?= e($b['cover_url']) ?>" alt="" style="height:100px;">
            <?php endif; ?>
            <h4><a href="<?= base_url('index.php?c=book&a=detail&id=' . $b['id']) ?>"><?= e($b['title']) ?></a></h4>
        </div>
    <?php endforeach; ?>
</div>
