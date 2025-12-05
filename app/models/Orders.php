<?php
// app/models/Order.php

require_once __DIR__ . '/../db.php';

class Order
{
    /**
     * Tạo đơn hàng mới
     */
    public static function create(
        int $userId,
        array $addrData,
        float $subtotal,
        float $discount,
        float $shipping,
        float $total,
        string $payment
    ): int {
        $pdo = db();

        // Mã đơn hàng: ORD + yyyyMMddHHmmss + random
        $code = 'ORD' . date('YmdHis') . rand(100, 999);

        $stmt = $pdo->prepare("
            INSERT INTO orders
                (code, user_id, addr_json, subtotal, discount, shipping_fee, total, status, payment_method, created_at)
            VALUES
                (:code, :user_id, :addr_json, :subtotal, :discount, :shipping_fee, :total, :status, :payment_method, NOW())
        ");

        $stmt->execute([
            ':code'           => $code,
            ':user_id'        => $userId,
            ':addr_json'      => json_encode($addrData, JSON_UNESCAPED_UNICODE),
            ':subtotal'       => $subtotal,
            ':discount'       => $discount,
            ':shipping_fee'   => $shipping,
            ':total'          => $total,
            ':status'         => 'Pending',   // trạng thái mặc định
            ':payment_method' => $payment,
        ]);

        return (int)$pdo->lastInsertId();
    }

    /**
     * Lấy đơn hàng theo user hiện tại (trang "Đơn hàng của tôi")
     */
    public static function findByUser(int $userId, int $limit = 20): array
    {
        $pdo = db();
        $stmt = $pdo->prepare("
            SELECT *
            FROM orders
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy đơn hàng theo ID
     */
    public static function findById(int $id): ?array
    {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * (Dùng cho admin) Lấy tất cả đơn hàng, có thể lọc theo status
     */
    public static function findAll(?string $status = null): array
    {
        $pdo = db();

        if ($status !== null && $status !== '') {
            $stmt = $pdo->prepare("
                SELECT *
                FROM orders
                WHERE status = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->query("
                SELECT *
                FROM orders
                ORDER BY created_at DESC
            ");
        }

        return $stmt->fetchAll();
    }

    /**
     * (Dùng cho admin) Cập nhật trạng thái đơn hàng
     */
    public static function updateStatus(int $id, string $status): void
    {
        $pdo = db();
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
}
