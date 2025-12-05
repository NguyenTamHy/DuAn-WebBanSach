<?php
// app/controllers/OrderController.php

require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../models/Orders.php';
require_once __DIR__ . '/../models/OrderItem.php';

class OrderController
{
    /**
     * Trang: Đơn hàng của tôi
     * URL: index.php?c=order&a=my
     */
    public function my()
    {
        if (!is_logged_in()) {
            redirect('index.php?c=auth&a=login');
        }

        $orders = Order::findByUser(current_user_id(), 20);

        // dùng view app/views/orders_list.php
        render('orders_list', ['orders' => $orders]);
    }

    /**
     * Trang: Chi tiết đơn hàng
     * URL: index.php?c=order&a=detail&id=...
     */
    public function detail()
    {
        if (!is_logged_in()) {
            redirect('index.php?c=auth&a=login');
        }

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "ID không hợp lệ";
            exit;
        }

        $order = Order::findById($id);
        // Chỉ cho xem đơn hàng của chính mình
        if (!$order || (int)$order['user_id'] !== current_user_id()) {
            http_response_code(404);
            echo "Order not found";
            exit;
        }

        $items = OrderItem::findByOrder($id);

        // dùng view app/views/order_detail.php
        render('order_detail', compact('order', 'items'));
    }
}
