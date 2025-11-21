<?php include __DIR__ . '/../includes/admin_header.php'; ?>
<h1>Quản lý người dùng</h1>
<table border="1" cellpadding="6">
<tr><th>ID</th><th>Họ tên</th><th>Email</th><th>Vai trò</th><th>Hành động</th></tr>
<?php foreach($users as $u): ?>
<tr>
  <td><?= $u['id'] ?></td>
  <td><?= htmlspecialchars($u['fullname'] ?? $u['name'] ?? '') ?></td>
  <td><?= htmlspecialchars($u['email']) ?></td>
  <td><?= htmlspecialchars($u['role'] ?? 'user') ?></td>
  <td><a href="?action=user_delete&id=<?= $u['id'] ?>" onclick="return confirm('Xóa user?')">Xóa</a></td>
</tr>
<?php endforeach; ?>
</table>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
