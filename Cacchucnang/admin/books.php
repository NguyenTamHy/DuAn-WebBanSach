<?php include __DIR__ . '/../includes/admin_header.php'; ?>
<h1>Quản lý sách</h1>
<p><a href="?action=book_add">Thêm sách mới</a></p>
<table border="1" cellpadding="6" cellspacing="0">
  <tr><th>ID</th><th>Ảnh</th><th>Tiêu đề</th><th>Tác giả</th><th>Giá</th><th>Tồn</th><th>Hành động</th></tr>
  <?php foreach($books as $b): ?>
    <tr>
      <td><?= $b['id'] ?></td>
      <td><?php if($b['cover_image']): ?><img src="/bookstore/uploads/<?= htmlspecialchars($b['cover_image']) ?>" width="60"><?php endif; ?></td>
      <td><?= htmlspecialchars($b['title']) ?></td>
      <td><?= htmlspecialchars($b['author']) ?></td>
      <td><?= number_format($b['price'],0,",",".") ?></td>
      <td><?= $b['stock'] ?></td>
      <td>
        <a href="?action=book_edit&id=<?= $b['id'] ?>">Sửa</a> |
        <a href="?action=book_delete&id=<?= $b['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
