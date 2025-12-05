<?php
// app/controllers/CartController.php

require_once __DIR__ . '/../cart.php';
require_once __DIR__ . '/../helpers.php';

class CartController
{
    public function index()
    {
        $cart = cart_detailed();
        render('cart', ['cart' => $cart]);
    }

    public function add()
    {
        $id = (int)($_POST['book_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 1);
        if ($id > 0) {
            cart_add($id, max(1, $qty));
        }
        redirect('index.php?c=cart&a=index');
    }

    public function update()
    {
        foreach ($_POST['qty'] ?? [] as $book_id => $qty) {
            cart_update((int)$book_id, (int)$qty);
        }
        redirect('index.php?c=cart&a=index');
    }

    public function clear()
    {
        cart_clear();
        redirect('index.php?c=cart&a=index');
    }
}
