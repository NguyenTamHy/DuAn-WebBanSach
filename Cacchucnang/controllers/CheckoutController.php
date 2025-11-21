<?php
require_once './includes/config.php';
require_once './models/Order.php';
require_once './models/Cart.php';
session_start();

$orderModel = new Order($conn);
$cartModel = new Cart();

if (isset($_POST['checkout'])) {
    $orderId = $orderModel->createOrder($_SESSION['user']['id'], $cartModel->getCart());
    unset($_SESSION['cart']);
    echo "Thanh toán thành công! Mã đơn hàng: #" . $orderId;
}
?>
