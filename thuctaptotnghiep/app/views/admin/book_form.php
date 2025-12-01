<?php /** @var array|null $book */ ?>
<h2><?= $book ? 'Sửa sách' : 'Thêm sách mới' ?></h2>

<?php if (!empty($_SESSION['admin_error'])): ?>
    <p style="color:red"><?= e($_SESSION['admin_error']) ?></p>
    <?php unset($_SESSION['admin_error']); ?>
<?php endif; ?>

<form method="post" action="<?= base_url('index.php?c=admin&a=bookSave') ?>">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    <?php if ($book): ?>
        <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
    <?php endif; ?>

    <label>Tiêu đề:
        <input type="text" name="title" value="<?= e($book['title'] ?? '') ?>">
    </label><br>

    <label>Slug:
        <input type="text" name="slug" value="<?= e($book['slug'] ?? '') ?>">
    </label><br>

    <label>ISBN:
        <input type="text" name="isbn" value="<?= e($book['isbn'] ?? '') ?>">
    </label><br>

    <label>Giá:
        <input type="number" step="1000" name="price" value="<?= e($book['price'] ?? '0') ?>">
    </label><br>

    <label>Tồn kho:
        <input type="number" name="stock_qty" value="<?= e($book['stock_qty'] ?? '0') ?>">
    </label><br>

    <label>Ảnh bìa (URL):
        <input type="text" name="cover_url" value="<?= e($book['cover_url'] ?? '') ?>">
    </label><br>

    <label>Mô tả:<br>
        <textarea name="description" rows="5"><?= e($book['description'] ?? '') ?></textarea>
    </label><br>

    <button type="submit">Lưu</button>
</form>
