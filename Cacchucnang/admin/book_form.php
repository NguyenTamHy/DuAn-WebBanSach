<?php include __DIR__ . '/../includes/admin_header.php'; ?>
<?php $isEdit = isset($book); ?>
<h1><?= $isEdit ? 'Sửa sách' : 'Thêm sách' ?></h1>
<form method="post" enctype="multipart/form-data" action="">
  <?php if($isEdit): ?><input type="hidden" name="id" value="<?= $book['id'] ?>"><?php endif; ?>
  <label>Tiêu đề</label><br><input type="text" name="title" value="<?= $isEdit ? htmlspecialchars($book['title']) : '' ?>" required><br>
  <label>Tác giả</label><br><input type="text" name="author" value="<?= $isEdit ? htmlspecialchars($book['author']) : '' ?>"><br>
  <label>Giá</label><br><input type="number" name="price" value="<?= $isEdit ? $book['price'] : 0 ?>" step="0.01"><br>
  <label>Tồn kho</label><br><input type="number" name="stock" value="<?= $isEdit ? $book['stock'] : 0 ?>"><br>
  <label>Mô tả</label><br><textarea name="description"><?= $isEdit ? htmlspecialchars($book['description']) : '' ?></textarea><br>
  <label>Ảnh bìa</label><br><input type="file" name="cover"><br>
  <button type="submit"><?= $isEdit ? 'Cập nhật' : 'Thêm' ?></button>
</form>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
