<?php
// public/admin/orders.php
declare(strict_types=1);
require_once __DIR__ . '/../../app/controllers/OrderController.php';
require_once __DIR__ . '/../../app/controllers/AdminController.php';
AdminController::requireAdmin();

$orders = OrderController::listOrdersForAdmin();
?>
<section class="admin-orders wrap">
  <h2>Quản lý đơn</h2>
  <table class="admin-table">
    <thead><tr><th>ID</th><th>Mã</th><th>Khách</th><th>Ngày</th><th>Trạng thái</th><th>Tổng</th><th>Hành động</th></tr></thead>
    <tbody>
    <?php foreach($orders as $o):
        // get user email/name
        $u = null;
        try {
            $st = db()->prepare("SELECT name,email FROM users WHERE id=? LIMIT 1");
            $st->execute([$o['user_id']]);
            $u = $st->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) { $u = null; }
    ?>
      <tr>
        <td><?= (int)$o['id'] ?></td>
        <td><a href="/order?code=<?= e($o['code']) ?>"><?= e($o['code']) ?></a></td>
        <td><?= e($u['name'] ?? $u['email'] ?? '—') ?></td>
        <td><?= e($o['created_at']) ?></td>
        <td><?= e($o['status']) ?></td>
        <td><?= money($o['total']) ?></td>
        <td>
          <form method="post" style="display:flex;gap:.4rem">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
            <select name="status">
              <?php foreach(['Pending','Processing','Shipped','Completed','Cancelled'] as $s): ?>
                <option value="<?= e($s) ?>" <?= $s === $o['status'] ? 'selected' : '' ?>><?= e($s) ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn">Cập nhật</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>
