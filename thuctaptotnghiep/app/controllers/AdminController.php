<?php
// app/controllers/AdminController.php

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/AuditLog.php';

class AdminController
{
    private function requireAdmin()
    {
        auth_check_admin();
    }

    public function dashboard()
    {
        $this->requireAdmin();
        $stats = Order::getStats();
        render('admin/dashboard', ['stats' => $stats]);
    }

    public function books()
    {
        $this->requireAdmin();
        $books = Book::all(200, 0);
        render('admin/books', ['books' => $books]);
    }

    public function bookForm()
    {
        $this->requireAdmin();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $book = $id ? Book::find($id) : null;
        render('admin/book_form', ['book' => $book]);
    }

    public function bookSave()
    {
        $this->requireAdmin();
        csrf_check();

        $id   = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
        $data = [
            'title'       => trim($_POST['title'] ?? ''),
            'slug'        => trim($_POST['slug'] ?? ''),
            'isbn'        => trim($_POST['isbn'] ?? ''),
            'price'       => (float)($_POST['price'] ?? 0),
            'stock_qty'   => (int)($_POST['stock_qty'] ?? 0),
            'cover_url'   => trim($_POST['cover_url'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'publisher_id'=> null, // có publisher thì thêm trường form
        ];

        if ($data['title'] === '' || $data['price'] <= 0) {
            $_SESSION['admin_error'] = 'Tiêu đề và giá phải hợp lệ.';
            redirect('index.php?c=admin&a=bookForm' . ($id ? '&id='.$id : ''));
        }

        $savedId = Book::save($data, $id);
        AuditLog::log(auth_user()['id'], $id ? 'update_book' : 'create_book', 'books', $savedId, [
            'title' => $data['title'],
        ]);

        redirect('index.php?c=admin&a=books');
    }

    public function bookDelete()
    {
        $this->requireAdmin();
        csrf_check();

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            Book::delete($id);
            AuditLog::log(auth_user()['id'], 'delete_book', 'books', $id, []);
        }

        redirect('index.php?c=admin&a=books');
    }

    public function orders()
    {
        $this->requireAdmin();
        $status = $_GET['status'] ?? null;
        $orders = Order::all($status ?: null);
        render('admin/orders', [
            'orders' => $orders,
            'status' => $status,
        ]);
    }

    public function updateOrderStatus()
    {
        $this->requireAdmin();
        csrf_check();

        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'Pending';

        if ($id > 0) {
            Order::updateStatus($id, $status, auth_user()['id']);
        }

        redirect('index.php?c=admin&a=orders');
    }
}
