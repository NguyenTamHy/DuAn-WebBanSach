<?php
// app/cart.php

require_once __DIR__ . '/db.php';

/**
 * Lấy dữ liệu giỏ hàng dạng thô từ session
 */
function cart_items(): array
{
    return $_SESSION['cart'] ?? [];
}

/**
 * Thêm sách vào giỏ
 */
function cart_add(int $book_id, int $qty = 1): void
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id] += $qty;
    } else {
        $_SESSION['cart'][$book_id] = max(1, $qty);
    }
}

/**
 * Cập nhật số lượng 1 dòng trong giỏ
 * Nếu qty <= 0 thì xóa luôn dòng đó
 */
function cart_update(int $book_id, int $qty): void
{
    if (!isset($_SESSION['cart'])) {
        return;
    }

    if ($qty <= 0) {
        unset($_SESSION['cart'][$book_id]);
    } else {
        $_SESSION['cart'][$book_id] = $qty;
    }
}

/**
 * Xóa toàn bộ giỏ hàng
 */
function cart_clear(): void
{
    unset($_SESSION['cart']);
}

/**
 * Lấy giỏ hàng chi tiết (join với bảng books)
 * return [
 *   'lines' => [
 *      [
 *        'book'       => [...],
 *        'qty'        => 2,
 *        'line_total' => 123000
 *      ],
 *      ...
 *   ],
 *   'subtotal' => 456000
 * ]
 */
function cart_detailed(): array
{
    $items = cart_items();
    if (empty($items)) {
        return ['lines' => [], 'subtotal' => 0];
    }

    $ids = array_keys($items);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = db()->prepare("SELECT * FROM books WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $books = $stmt->fetchAll();

    // map book_id -> book
    $map = [];
    foreach ($books as $b) {
        $map[$b['id']] = $b;
    }

    $lines = [];
    $subtotal = 0;

    foreach ($items as $book_id => $qty) {
        if (!isset($map[$book_id])) {
            continue; // sách đã bị xóa khỏi DB
        }
        $book = $map[$book_id];
        $line_total = $book['price'] * $qty;
        $subtotal += $line_total;

        $lines[] = [
            'book'       => $book,
            'qty'        => $qty,
            'line_total' => $line_total,
        ];
    }

    return ['lines' => $lines, 'subtotal' => $subtotal];
}
