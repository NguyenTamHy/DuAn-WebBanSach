<?php
/** @var array $book */
/** @var array $categories */
/** @var array $selectedCategories */
$isEdit = !empty($book['id']);
?>
<h2><?= $isEdit ? 'Sửa sách' : 'Thêm sách mới' ?></h2>

<form method="post"
      action="<?= base_url('index.php?c=admin&a=' . ($isEdit ? 'bookUpdate' : 'bookStore')) ?>"
      enctype="multipart/form-data">
    <?= csrf_field(); ?>

    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
        <input type="hidden" name="old_cover_url" value="<?= e($book['cover_url'] ?? '') ?>">
    <?php endif; ?>

    <div>
        <label>Tiêu đề *</label><br>
        <input type="text" name="title" value="<?= e($book['title'] ?? '') ?>" required>
    </div>

    <div>
        <label>Slug</label><br>
        <input type="text" name="slug" value="<?= e($book['slug'] ?? '') ?>">
    </div>

    <div>
        <label>ISBN</label><br>
        <input type="text" name="isbn" value="<?= e($book['isbn'] ?? '') ?>">
    </div>

    <div>
        <label>Giá bán *</label><br>
        <input type="number" name="price" min="0" step="1000"
               value="<?= e($book['price'] ?? 0) ?>" required>
    </div>

    <div>
        <label>Tồn kho</label><br>
        <input type="number" name="stock_qty" min="0"
               value="<?= e($book['stock_qty'] ?? 0) ?>">
    </div>

    <div>
        <label>Giảm giá (%)</label><br>
        <input type="number" name="discount_percent" min="0" max="100"
               value="<?= e($book['discount_percent'] ?? 0) ?>">
    </div>

    <div>
        <label>Ngày xuất bản</label><br>
        <input type="date" name="published_at" value="<?= e($book['published_at'] ?? '') ?>">
    </div>

    <div>
        <label>Thể loại</label><br>
        <?php foreach ($categories as $cat): ?>
            <label style="margin-right:10px;">
                <input type="checkbox" name="category_ids[]"
                       value="<?= (int)$cat['id'] ?>"
                    <?= in_array($cat['id'], $selectedCategories ?? [], true) ? 'checked' : '' ?>>
                <?= e($cat['name']) ?>
            </label>
        <?php endforeach; ?>
    </div>

    <div>
        <label>Mô tả</label><br>
        <textarea name="description" rows="5" cols="60"><?= e($book['description'] ?? '') ?></textarea>
    </div>

    <div>
        <label>Ảnh bìa</label><br>
        <input type="file" name="cover" accept="image/*">
        <?php if (!empty($book['cover_url'])): ?>
            <p>Ảnh hiện tại:</p>
            <img src="<?= e($book['cover_url']) ?>" style="height:120px;">
        <?php endif; ?>
    </div>

    <div style="margin-top:10px;">
        <button type="submit"><?= $isEdit ? 'Cập nhật' : 'Thêm mới' ?></button>
        <a href="<?= base_url('index.php?c=admin&a=books') ?>">Hủy</a>
    </div>
</form>
