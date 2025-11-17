<?php
// public/home.php
try {
    $books = db()->query("SELECT id, title, price, cover_url, stock_qty FROM books ORDER BY created_at DESC LIMIT 12")->fetchAll();
} catch (Throwable $e) {
    $books = [];
}
?>
<section class="wrap">
  <h2>Sách mới</h2>
  <div class="grid">
    <?php if (empty($books)): ?>
      <p>Chưa có sách trong hệ thống.</p>
    <?php else: foreach($books as $b): ?>
      <article class="card">
        <img src="<?= e($b['cover_url'] ?: '/assets/images/placeholder.png') ?>" alt="<?= e($b['title']) ?>">
        <h4><?= e($b['title']) ?></h4>
        <p class="price"><?= money($b['price']) ?></p>
        <p class="stock"><?= (int)$b['stock_qty'] > 0 ? 'Còn: '.(int)$b['stock_qty'] : '<span class="out">Hết hàng</span>' ?></p>
        <p><a class="btn" href="/book?id=<?= (int)$b['id'] ?>">Xem chi tiết</a></p>
      </article>
    <?php endforeach; endif; ?>
  </div>
</section>
