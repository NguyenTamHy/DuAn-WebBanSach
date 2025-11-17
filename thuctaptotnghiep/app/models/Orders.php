<?php
// app/models/Order.php
declare(strict_types=1);

if (!class_exists('OrderModel')) {
    class OrderModel
    {
        // Create order (atomic)
        public static function create(int $userId, array $addr, array $items, array $totals): array
        {
            $pdo = db();
            $code = 'ORD' . strtoupper(bin2hex(random_bytes(4)));
            try {
                $pdo->beginTransaction();
                $st = $pdo->prepare("INSERT INTO orders (code, user_id, addr_json, subtotal, discount, shipping_fee, total, status, payment_method) VALUES (?,?,?,?,?,?,?,?,?)");
                $st->execute([
                    $code,
                    $userId,
                    json_encode($addr, JSON_UNESCAPED_UNICODE),
                    $totals['subtotal'] ?? 0,
                    $totals['discount'] ?? 0,
                    $totals['shipping'] ?? 0,
                    $totals['total'] ?? 0,
                    'Pending',
                    $totals['payment_method'] ?? 'COD'
                ]);
                $orderId = (int)$pdo->lastInsertId();

                $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, book_id, title_snapshot, qty, unit_price, line_total) VALUES (?,?,?,?,?,?)");
                foreach ($items as $it) {
                    $qty = (int)($it['qty'] ?? 1);
                    $unit = (float)($it['price'] ?? $it['unit_price'] ?? 0);
                    $title = (string)($it['title'] ?? '');
                    $line_total = $qty * $unit;
                    $stmtItem->execute([$orderId, $it['id'] ?? null, $title, $qty, $unit, $line_total]);
                }

                // audit log
                try {
                    $alog = db()->prepare("INSERT INTO audit_logs (actor_id, action, entity, entity_id, payload_json) VALUES (?,?,?,?,?)");
                    $alog->execute([
                        $userId,
                        'create_order',
                        'orders',
                        $orderId,
                        json_encode(['totals'=>$totals,'items'=>$items], JSON_UNESCAPED_UNICODE)
                    ]);
                } catch (Throwable $e) { /* ignore audit failure */ }

                $pdo->commit();
                return ['id'=>$orderId, 'code'=>$code];
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }
        }

        public static function findByCodeForUser(string $code, int $userId): ?array
        {
            $st = db()->prepare("SELECT * FROM orders WHERE code=? AND user_id=? LIMIT 1");
            $st->execute([$code, $userId]);
            $r = $st->fetch(PDO::FETCH_ASSOC);
            return $r ?: null;
        }

        public static function itemsOf(int $orderId): array
        {
            $st = db()->prepare("SELECT * FROM order_items WHERE order_id=?");
            $st->execute([$orderId]);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function listAll(int $limit = 200): array
        {
            $st = db()->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT ?");
            $st->bindValue(1, $limit, PDO::PARAM_INT);
            $st->execute();
            return $st->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function updateStatus(int $id, string $status): void
        {
            $st = db()->prepare("UPDATE orders SET status=? WHERE id=?");
            $st->execute([$status, $id]);

            // audit
            try {
                $ulog = db()->prepare("INSERT INTO audit_logs (actor_id, action, entity, entity_id, payload_json) VALUES (?,?,?,?,?)");
                $ulog->execute([
                    user()['id'] ?? null,
                    'update_order_status',
                    'orders',
                    $id,
                    json_encode(['status'=>$status], JSON_UNESCAPED_UNICODE)
                ]);
            } catch (Throwable $e) {}
        }
    }
}
