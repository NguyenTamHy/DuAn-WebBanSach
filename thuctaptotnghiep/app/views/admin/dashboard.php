<?php
// public/admin/dashboard.php
declare(strict_types=1);
require_once __DIR__ . '/../../app/controllers/AdminController.php';
AdminController::requireAdmin();
$stats = AdminController::getStats();
?>
<section class="admin-dashboard wrap">
  <h2>Admin Dashboard</h2>
  <div class="admin-cards">
    <div class="card">Tổng đơn: <b><?= (int)($stats['totOrders'] ?? 0) ?></b></div>
    <div class="card">Đơn chờ: <b><?= (int)($stats['pending'] ?? 0) ?></b></div>
    <div class="card">Doanh thu: <b><?= money($stats['revenue'] ?? 0) ?></b></div>
  </div>

  <h3>Top sách bán chạy</h3>
  <ul>
    <?php foreach($stats['topBooks'] ?? [] as $b): ?>
      <li><?= e($b['title_snapshot']) ?> — <?= (int)$b['sold'] ?> bán</li>
    <?php endforeach; ?>
  </ul>

  <h3>Tồn kho thấp</h3>
  <ul>
    <?php foreach($stats['lowStock'] ?? [] as $s): ?>
      <li><?= e($s['title']) ?> — <?= (int)$s['stock_qty'] ?> còn</li>
    <?php endforeach; ?>
  </ul>
</section>
