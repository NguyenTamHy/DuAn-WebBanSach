<?php
class Cart {
    public function addToCart($bookId, $quantity) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $_SESSION['cart'][$bookId] = ($quantity ?? 1);
    }

    public function removeItem($bookId) {
        unset($_SESSION['cart'][$bookId]);
    }

    public function getCart() {
        return $_SESSION['cart'] ?? [];
    }
}
?>
