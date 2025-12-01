<?php
// app/controllers/OrderController.php

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../models/Order.php';

class OrderController
{
    public function detail()
    {
        auth_check();
        $id = (int)($_GET['id'] ?? 0);
        $order = Order::find($id);
        if (!$order || $order['user_id'] != auth_user()['id'] && !is_admin()) {
            http_response_code(404);
            echo "Order not found";
            return;
        }
        $items = Order::items($id);
        $addr = json_decode($order['addr_json'], true);

        render('order_detail', [
            'order' => $order,
            'items' => $items,
            'addr'  => $addr,
        ]);
    }

    public function list()
    {
        auth_check();
        $orders = Order::forUser(auth_user()['id']);
        render('orders_list', ['orders' => $orders]);
    }
}
