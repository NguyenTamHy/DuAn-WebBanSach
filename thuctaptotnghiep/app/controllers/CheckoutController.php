<?php
// app/controllers/CheckoutController.php

require_once __DIR__ . '/../cart.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Coupon.php';

class CheckoutController
{
    public function index()
    {
        auth_check();
        $cart = cart_detailed();
        if (empty($cart['lines'])) {
            redirect('index.php?c=cart&a=index');
        }

        $coupon_code = $_SESSION['coupon_code'] ?? '';
        $coupon      = null;
        $discount    = 0.0;

        if ($coupon_code !== '') {
            $coupon = Coupon::findByCode($coupon_code);
            if ($coupon) {
                $discount = Coupon::calcDiscount($coupon, (float)$cart['subtotal']);
            } else {
                $coupon_code = '';
                unset($_SESSION['coupon_code']);
            }
        }

        $shipping_fee = ($cart['subtotal'] >= 300000) ? 0 : 30000;

        render('checkout', [
            'cart'         => $cart,
            'user'         => auth_user(),
            'coupon_code'  => $coupon_code,
            'coupon'       => $coupon,
            'discount'     => $discount,
            'shipping_fee' => $shipping_fee,
        ]);
    }

    public function applyCoupon()
    {
        auth_check();
        $cart = cart_detailed();
        if (empty($cart['lines'])) {
            redirect('index.php?c=cart&a=index');
        }

        csrf_check();
        $code = strtoupper(trim($_POST['coupon_code'] ?? ''));

        if ($code === '') {
            unset($_SESSION['coupon_code']);
        } else {
            $coupon = Coupon::findByCode($code);
            if ($coupon && Coupon::calcDiscount($coupon, (float)$cart['subtotal']) > 0) {
                $_SESSION['coupon_code'] = $code;
                unset($_SESSION['coupon_error']);
            } else {
                $_SESSION['coupon_error'] = 'Mã giảm giá không hợp lệ hoặc chưa đủ điều kiện áp dụng.';
            }
        }

        redirect('index.php?c=checkout&a=index');
    }

    public function placeOrder()
    {
        auth_check();
        csrf_check();

        $cart = cart_detailed();
        if (empty($cart['lines'])) {
            redirect('index.php?c=cart&a=index');
        }

        $name    = trim($_POST['name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $payment_method = $_POST['payment_method'] ?? 'COD';

        if ($name === '' || $phone === '' || $address === '') {
            $_SESSION['checkout_error'] = 'Vui lòng nhập đầy đủ thông tin giao hàng.';
            redirect('index.php?c=checkout&a=index');
        }

        $coupon_code = $_SESSION['coupon_code'] ?? '';
        $coupon      = $coupon_code ? Coupon::findByCode($coupon_code) : null;
        $discount    = 0.0;
        if ($coupon) {
            $discount = Coupon::calcDiscount($coupon, (float)$cart['subtotal']);
        }

        $shipping_fee = ($cart['subtotal'] >= 300000) ? 0 : 30000;

        $order_id = Order::create(auth_user()['id'], $cart, [
            'name'           => $name,
            'phone'          => $phone,
            'address'        => $address,
            'payment_method' => $payment_method,
            'discount'       => $discount,
            'shipping_fee'   => $shipping_fee,
        ]);

        cart_clear();
        unset($_SESSION['coupon_code'], $_SESSION['coupon_error'], $_SESSION['checkout_error']);

        redirect('index.php?c=order&a=detail&id=' . $order_id);
    }
}
