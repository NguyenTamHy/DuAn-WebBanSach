<?php
// app/models/Order.php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/AuditLog.php';

class Order
{
    public static function create(int $user_id, array $cart, array $checkoutData): int
    {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $subtotal = (float)$cart['subtotal'];
            $discount = (float)($checkoutData['discount'] ?? 0);
            $shipping_fee = (float)($checkoutData['shipping_fee'] ?? 0);
            $total = max(0, $subtotal - $discount + $shipping_fee);

            $addr = [
                'name'    => $checkoutData['name'],
                'phone'   => $checkoutData['phone'],
                'address' => $checkoutData['address'],
            ];

            $code = 'OD' . time() . rand(100, 999);

            $stmt = $pdo->prepare("
                INSERT INTO orders (code, user_id, addr_json, subtotal, discount, shipping_fee, total, payment_method, status)
                VALUES (:code, :user_id, :addr_json, :subtotal, :discount, :shipping_fee, :total, :payment_method, 'Pending')
            ");
            $stmt->execute([
                ':code'           => $code,
                ':user_id'        => $user_id,
                ':addr_json'      => json_encode($addr, JSON_UNESCAPED_UNICODE),
                ':subtotal'       => $subtotal,
                ':discount'       => $discount,
                ':shipping_fee'   => $shipping_fee,
                ':total'          => $total,
                ':payment_method' => $checkoutData['payment_method'] ?? 'COD',
            ]);

            $order_id = (int)$pdo->lastInsertId();

            $stmtItem = $pdo->prepare("
                INSERT INTO order_items (order_id, book_id, title_snapshot, qty, unit_price, line_total)
                VALUES (:order_id, :book_id, :title_snapshot, :qty, :unit_price, :line_total)
            ");

            foreach ($cart['lines'] as $line) {
                $book = $line['book'];
                $qty  = (int)$line['qty'];
                $stmtItem->execute([
                    ':order_id'       => $order_id,
                    ':book_id'        => $book['id'],
                    ':title_snapshot' => $book['title'],
                    ':qty'            => $qty,
                    ':unit_price'     => $book['price'],
                    ':line_total'     => $line['line_total'],
                ]);

                $stmtStock = $pdo->prepare("UPDATE books SET stock_qty = stock_qty - ? WHERE id = ?");
                $stmtStock->execute([$qty, $book['id']]);
            }

            AuditLog::log($user_id, 'create_order', 'orders', $order_id, [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping' => $shipping_fee,
                'total'    => $total,
            ]);

            $pdo->commit();
            return $order_id;

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function find(int $id)
    {
        $stmt = db()->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function items(int $order_id)
    {
        $stmt = db()->prepare("
            SELECT oi.*, b.cover_url
            FROM order_items oi
            LEFT JOIN books b ON b.id = oi.book_id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll();
    }

    public static function forUser(int $user_id)
    {
        $stmt = db()->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public static function all(?string $status = null): array
    {
        if ($status) {
            $stmt = db()->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = db()->query("SELECT * FROM orders ORDER BY created_at DESC");
        }
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $order_id, string $status, int $admin_id): bool
    {
        $allowed = ['Pending', 'Processing', 'Shipped', 'Completed', 'Cancelled'];
        if (!in_array($status, $allowed, true)) return false;

        $stmt = db()->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $ok = $stmt->execute([$status, $order_id]);

        if ($ok) {
            AuditLog::log($admin_id, 'update_status', 'orders', $order_id, ['status' => $status]);
        }
        return $ok;
    }

    public static function getStats(): array
    {
        $pdo = db();

        $totOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $pending   = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
        $revenue   = (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status = 'Completed'")->fetchColumn();

        $sqlTop = "
            SELECT oi.book_id, oi.title_snapshot, SUM(oi.qty) AS sold_qty
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id AND o.status IN ('Completed','Shipped')
            GROUP BY oi.book_id, oi.title_snapshot
            ORDER BY sold_qty DESC
            LIMIT 5
        ";
        $topBooks = $pdo->query($sqlTop)->fetchAll();

        $lowStock = $pdo->query("
            SELECT id, title, stock_qty
            FROM books
            WHERE stock_qty <= 5
            ORDER BY stock_qty ASC
            LIMIT 10
        ")->fetchAll();

        return compact('totOrders', 'pending', 'revenue', 'topBooks', 'lowStock');
    }
}
