<?php
// app/controllers/AdminController.php

require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Category.php';

class AdminController
{
    public function orders()
{
    $status = $_GET['status'] ?? '';
    $orders = Order::findAll($status);

    render('admin/Oders', ['orders' => $orders]);
}

    public function __construct()
    {
        if (!is_admin()) {
            $_SESSION['error'] = 'Bạn phải đăng nhập với quyền ADMIN.';
            redirect('index.php?c=auth&a=login');
        }
    }

    public function index()
    {
        render('admin/dashboard');
    }

    public function books()
    {
        $books = Book::allForAdmin();
        render('admin/books', ['books' => $books]);
    }

    public function bookCreate()
    {
        $categories = Category::all();
        $book = [
            'id'               => null,
            'title'            => '',
            'slug'             => '',
            'isbn'             => '',
            'price'            => 0,
            'stock_qty'        => 0,
            'cover_url'        => '',
            'description'      => '',
            'publisher_id'     => null,
            'published_at'     => '',
            'discount_percent' => 0,
        ];
        $selectedCategories = [];

        render('admin/book_form', compact('book', 'categories', 'selectedCategories'));
    }

    public function bookStore()
    {
        csrf_check();

        $title       = trim($_POST['title'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $stockQty    = (int)($_POST['stock_qty'] ?? 0);
        $discount    = (int)($_POST['discount_percent'] ?? 0);
        $isbn        = trim($_POST['isbn'] ?? '');
        $slug        = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $publishedAt = $_POST['published_at'] ?? null;
        $categoryIds = $_POST['category_ids'] ?? [];

        if ($title === '' || $price <= 0) {
            $_SESSION['error'] = 'Tiêu đề và giá sách là bắt buộc.';
            redirect('index.php?c=admin&a=bookCreate');
        }

        $coverUrl = null;
        if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $coverUrl = $this->handleUploadCover($_FILES['cover']);
        }

        if ($slug === '') {
            $slug = $this->slugify($title);
        }

        $data = [
            'title'            => $title,
            'slug'             => $slug,
            'isbn'             => $isbn ?: null,
            'price'            => $price,
            'stock_qty'        => $stockQty,
            'cover_url'        => $coverUrl,
            'description'      => $description,
            'publisher_id'     => null,
            'published_at'     => $publishedAt ?: null,
            'discount_percent' => $discount,
        ];

        Book::create($data, $categoryIds);

        $_SESSION['message'] = 'Thêm sách mới thành công.';
        redirect('index.php?c=admin&a=books');
    }

    public function bookEdit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $book = Book::find($id);
        if (!$book) {
            $_SESSION['error'] = 'Không tìm thấy sách.';
            redirect('index.php?c=admin&a=books');
        }

        $categories         = Category::all();
        $selectedCategories = Book::categoryIds($id);

        render('admin/book_form', compact('book', 'categories', 'selectedCategories'));
    }

    public function bookUpdate()
    {
        csrf_check();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0 || !Book::find($id)) {
            $_SESSION['error'] = 'Sách không tồn tại.';
            redirect('index.php?c=admin&a=books');
        }

        $title       = trim($_POST['title'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $stockQty    = (int)($_POST['stock_qty'] ?? 0);
        $discount    = (int)($_POST['discount_percent'] ?? 0);
        $isbn        = trim($_POST['isbn'] ?? '');
        $slug        = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $publishedAt = $_POST['published_at'] ?? null;
        $categoryIds = $_POST['category_ids'] ?? [];

        if ($title === '' || $price <= 0) {
            $_SESSION['error'] = 'Tiêu đề và giá sách là bắt buộc.';
            redirect('index.php?c=admin&a=bookEdit&id=' . $id);
        }

        $coverUrl = $_POST['old_cover_url'] ?? null;

        if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $coverUrl = $this->handleUploadCover($_FILES['cover']);
        }

        if ($slug === '') {
            $slug = $this->slugify($title);
        }

        $data = [
            'title'            => $title,
            'slug'             => $slug,
            'isbn'             => $isbn ?: null,
            'price'            => $price,
            'stock_qty'        => $stockQty,
            'cover_url'        => $coverUrl,
            'description'      => $description,
            'publisher_id'     => null,
            'published_at'     => $publishedAt ?: null,
            'discount_percent' => $discount,
        ];

        Book::updateById($id, $data, $categoryIds);

        $_SESSION['message'] = 'Cập nhật sách thành công.';
        redirect('index.php?c=admin&a=books');
    }

    public function bookDelete()
    {
        csrf_check();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            Book::deleteById($id);
            $_SESSION['message'] = 'Đã xóa sách.';
        }
        redirect('index.php?c=admin&a=books');
    }

    protected function handleUploadCover(array $file): ?string
    {
        $uploadDir = __DIR__ . '/../../public/uploads/covers';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed, true)) {
            return null;
        }

        $filename = uniqid('cover_', true) . '.' . $ext;
        $path     = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return null;
        }

        // đường dẫn public
        return base_url('uploads/covers/' . $filename);
    }

    protected function slugify(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $str);
        $str = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $str);
        $str = preg_replace('/[íìỉĩị]/u', 'i', $str);
        $str = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $str);
        $str = preg_replace('/[úùủũụưứừửữự]/u', 'u', $str);
        $str = preg_replace('/[ýỳỷỹỵ]/u', 'y', $str);
        $str = preg_replace('/đ/u', 'd', $str);

        $str = preg_replace('/[^a-z0-9]+/u', '-', $str);
        $str = trim($str, '-');
        return $str ?: uniqid('book-');
    }
}
