<?php
require_once './models/Cart.php';
session_start();

$cart = new Cart();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $cart->addToCart($_POST['book_id'], $_POST['quantity']);
    } elseif (isset($_POST['remove'])) {
        $cart->removeItem($_POST['book_id']);
    }
}
?>
