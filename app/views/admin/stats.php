<?php
// public/admin/stats.php
declare(strict_types=1);
require_once __DIR__ . '/../../app/controllers/AdminController.php';
AdminController::requireAdmin();
$stats = AdminController::getStats();
?>
<section class="admin-stats wrap">
  <h2>Thống kê</h2>
  <p>Tổng đơn: <?= (int)($stats['totOrders'] ?? 0) ?></p>
  <p>Đơn chờ: <?= (int)($stats['pending'] ?? 0) ?></p>
  <p>Doanh thu: <?= money($stats['revenue'] ?? 0) ?></p>
  <h4>Top sách</h4>
  <ul><?php foreach($stats['topBooks'] ?? [] as $b): ?><li><?= e($b['title_snapshot']) ?> — <?= (int)$b['sold'] ?></li><?php endforeach; ?></ul>
</section>
