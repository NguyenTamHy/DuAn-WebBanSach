<?php
// app/cart.php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/models/Book.php';

const CART_KEY = 'cart_items';

/** Lấy mảng thuần: [book_id => qty] */
function cart_raw(): array
{
    return $_SESSION[CART_KEY] ?? [];
}

/** Ghi mảng cart */
function cart_save(array $cart): void
{
    $_SESSION[CART_KEY] = $cart;
}

/** Thêm sách vào giỏ */
function cart_add(int $bookId, int $qty = 1): void
{
    $cart = cart_raw();
    if (isset($cart[$bookId])) {
        $cart[$bookId] += max(1, $qty);
    } else {
        $cart[$bookId] = max(1, $qty);
    }
    cart_save($cart);
}

/** Cập nhật số lượng */
function cart_update(int $bookId, int $qty): void
{
    $cart = cart_raw();
    if ($qty <= 0) {
        unset($cart[$bookId]);
    } else {
        $cart[$bookId] = $qty;
    }
    cart_save($cart);
}

/** Xóa giỏ */
function cart_clear(): void
{
    unset($_SESSION[CART_KEY]);
}

/** Tổng quan giỏ hàng (kèm info sách) */
function cart_detailed(): array
{
    $cart = cart_raw();
    if (empty($cart)) {
        return [
            'lines'    => [],
            'subtotal' => 0,
            'count'    => 0,
        ];
    }

    $bookIds = array_keys($cart);
    $books   = Book::findMany($bookIds);
    $booksById = [];
    foreach ($books as $b) {
        $booksById[$b['id']] = $b;
    }

    $lines    = [];
    $subtotal = 0;
    $count    = 0;

    foreach ($cart as $bookId => $qty) {
        if (!isset($booksById[$bookId])) {
            continue;
        }
        $book  = $booksById[$bookId];
        $price = book_effective_price($book);
        $lineTotal = $price * $qty;

        $lines[] = [
            'book'       => $book,
            'qty'        => $qty,
            'unit_price' => $price,
            'line_total' => $lineTotal,
        ];
        $subtotal += $lineTotal;
        $count    += $qty;
    }

    return [
        'lines'    => $lines,
        'subtotal' => $subtotal,
        'count'    => $count,
    ];
}
