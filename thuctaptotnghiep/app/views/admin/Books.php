<?php
// public/admin/books.php
declare(strict_types=1);
require_once __DIR__ . '/../../app/controllers/AdminController.php';
AdminController::requireAdmin();

$books = [];
try {
    $books = db()->query("SELECT id,title,price,stock_qty FROM books ORDER BY id DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) { $books = []; }
?>
<section class="admin-books wrap">
  <h2>Quản lý Sách</h2>
  <p><a class="btn" href="/admin?p=books&action=create">Thêm sách mới</a></p>
  <table class="admin-table">
    <thead><tr><th>ID</th><th>Tiêu đề</th><th>Giá</th><th>Tồn</th><th></th></tr></thead>
    <tbody>
    <?php foreach($books as $b): ?>
      <tr>
        <td><?= (int)$b['id'] ?></td>
        <td><?= e($b['title']) ?></td>
        <td><?= money($b['price']) ?></td>
        <td><?= (int)$b['stock_qty'] ?></td>
        <td><a class="btn" href="/admin?p=books&action=edit&id=<?= (int)$b['id'] ?>">Sửa</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>
