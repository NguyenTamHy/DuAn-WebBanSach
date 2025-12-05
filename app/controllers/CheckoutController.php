<?php
// app/controllers/CheckoutController.php

require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../cart.php';
require_once __DIR__ . '/../models/Orders.php';
require_once __DIR__ . '/../models/OrderItem.php';


class CheckoutController
{
    public function index()
    {
        if (!is_logged_in()) {
            $_SESSION['error'] = 'Bạn cần đăng nhập trước khi thanh toán.';
            redirect('index.php?c=auth&a=login');
        }

        $cart = cart_detailed();
        if (empty($cart['lines'])) {
            $_SESSION['error'] = 'Giỏ hàng trống.';
            redirect('index.php?c=cart&a=index');
        }

        $shipping = 20000; // ship fix
        $discount = 0;     // có thể áp mã coupon sau này
        $total    = $cart['subtotal'] + $shipping - $discount;

        render('checkout', [
            'cart'     => $cart,
            'shipping' => $shipping,
            'discount' => $discount,
            'total'    => $total,
        ]);
    }

    public function placeOrder()
    {
        if (!is_logged_in()) {
            $_SESSION['error'] = 'Bạn cần đăng nhập trước khi thanh toán.';
            redirect('index.php?c=auth&a=login');
        }

        $cart = cart_detailed();
        if (empty($cart['lines'])) {
            $_SESSION['error'] = 'Giỏ hàng trống.';
            redirect('index.php?c=cart&a=index');
        }

        $name    = trim($_POST['name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $payment = $_POST['payment_method'] ?? 'COD';

        if ($name === '' || $phone === '' || $address === '') {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin nhận hàng.';
            redirect('index.php?c=checkout&a=index');
        }

        $shipping = 20000;
        $discount = 0;
        $total    = $cart['subtotal'] + $shipping - $discount;

        $addrJson = [
            'name'    => $name,
            'phone'   => $phone,
            'address' => $address,
        ];

        $orderId = Order::create(
            current_user_id(),
            $addrJson,
            $cart['subtotal'],
            $discount,
            $shipping,
            $total,
            $payment
        );

        OrderItem::createItems($orderId, $cart['lines']);
        cart_clear();

        $_SESSION['message'] = 'Đặt hàng thành công!';
        redirect('index.php?c=order&a=detail&id=' . $orderId);
    }
}
