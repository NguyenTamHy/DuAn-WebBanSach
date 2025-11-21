<?php include __DIR__ . '/../includes/admin_header.php'; ?>
<h1>Quản lý đơn hàng</h1>
<table border="1" cellpadding="6">
<tr><th>ID</th><th>Người đặt</th><th>Tổng</th><th>Trạng thái</th><th>Hành động</th></tr>
<?php foreach($orders as $o): ?>
<tr>
  <td><?= $o['id'] ?></td>
  <td><?= htmlspecialchars($o['user_email'] ?? $o['user_id']) ?></td>
  <td><?= number_format($o['total'],0,",",".") ?></td>
  <td><?= $o['status'] ?></td>
  <td>
    <form method="post" action="?action=order_update" style="display:inline">
      <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
      <select name="status">
        <option <?= $o['status']=='Pending'?'selected':'' ?>>Pending</option>
        <option <?= $o['status']=='Processing'?'selected':'' ?>>Processing</option>
        <option <?= $o['status']=='Completed'?'selected':'' ?>>Completed</option>
        <option <?= $o['status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
      </select>
      <button type="submit">Cập nhật</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
</table>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
