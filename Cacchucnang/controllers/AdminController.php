<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';

require_admin();

$bookModel = new Book($conn);
$userModel = new User($conn);
$orderModel = new Order($conn);

// Simple router via query param
$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'dashboard':
        // collect stats
        $stats = [
            'total_orders' => $orderModel->countOrders(),
            'pending_orders' => $orderModel->countOrdersByStatus('Pending'),
            'completed_orders' => $orderModel->countOrdersByStatus('Completed'),
            'total_revenue' => $orderModel->sumRevenue()
        ];
        include __DIR__ . '/../admin/dashboard.php';
        break;

    case 'books':
        $books = $bookModel->getAll();
        include __DIR__ . '/../admin/books.php';
        break;

    case 'book_add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $author = $_POST['author'];
            $price = $_POST['price'];
            $stock = $_POST['stock'];
            $category_id = $_POST['category_id'] ?? null;
            $description = $_POST['description'] ?? '';

            // handle image upload
            $imageName = null;
            if (!empty($_FILES['cover']['name'])) {
                $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
                $imageName = uniqid('book_') . '.' . $ext;
                $target = __DIR__ . '/../uploads/' . $imageName;
                move_uploaded_file($_FILES['cover']['tmp_name'], $target);
            }

            $bookModel->create($title, $author, $price, $stock, $category_id, $imageName, $description);
            header('Location: ?action=books');
            exit;
        }
        include __DIR__ . '/../admin/book_form.php';
        break;

    case 'book_edit':
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ?action=books'); exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookModel->update($_POST['id'], $_POST['title'], $_POST['author'], $_POST['price'], $_POST['stock'], $_POST['category_id'], $_POST['description']);
            header('Location: ?action=books');
            exit;
        }
        $book = $bookModel->getById($id);
        include __DIR__ . '/../admin/book_form.php';
        break;

    case 'book_delete':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $bookModel->delete($id);
        }
        header('Location: ?action=books');
        break;

    case 'users':
        $users = $userModel->getAll();
        include __DIR__ . '/../admin/users.php';
        break;

    case 'user_delete':
        $id = $_GET['id'] ?? null;
        if ($id) $userModel->delete($id);
        header('Location: ?action=users');
        break;

    case 'orders':
        $orders = $orderModel->getAll();
        include __DIR__ . '/../admin/orders.php';
        break;

    case 'order_update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderModel->updateStatus($_POST['order_id'], $_POST['status']);
        }
        header('Location: ?action=orders');
        break;

    default:
        echo "Action không hợp lệ.";
}
