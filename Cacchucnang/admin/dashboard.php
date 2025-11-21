<?php include __DIR__ . '/../includes/admin_header.php'; ?>
<h1>Admin Dashboard</h1>
<div class="cards">
  <div>Total Orders: <?= htmlspecialchars($stats['total_orders']) ?></div>
  <div>Pending: <?= htmlspecialchars($stats['pending_orders']) ?></div>
  <div>Completed: <?= htmlspecialchars($stats['completed_orders']) ?></div>
  <div>Total Revenue: <?= number_format($stats['total_revenue'],0,",",".") ?> VNĐ</div>
</div>
<p><a href="?action=books">Quản lý sách</a> | <a href="?action=users">Quản lý người dùng</a> | <a href="?action=orders">Quản lý đơn</a></p>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
