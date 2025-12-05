<?php
// app/models/OrderItem.php

require_once __DIR__ . '/../db.php';

class OrderItem
{
    public static function createItems(int $orderId, array $lines): void
    {
        $pdo = db();
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, book_id, qty, price)
            VALUES (:order_id, :book_id, :qty, :price)
        ");

        foreach ($lines as $line) {
            $stmt->execute([
                ':order_id' => $orderId,
                ':book_id'  => $line['book']['id'],
                ':qty'      => $line['qty'],
                ':price'    => $line['unit_price'],
            ]);
        }
    }

    public static function findByOrder(int $orderId): array
    {
        $pdo = db();
        $stmt = $pdo->prepare("
            SELECT oi.*, b.title, b.cover_url
            FROM order_items oi
            JOIN books b ON b.id = oi.book_id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
