<?php
// app/controllers/AdminController.php
declare(strict_types=1);

if (!class_exists('AdminController')) {
    class AdminController
    {
        public static function requireAdmin()
        {
            if (!is_admin()) {
                header('Location: /login');
                exit;
            }
        }

        public static function getStats(): array
        {
            try {
                $db = db();
                $totOrders = (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
                $pending = (int)$db->query("SELECT COUNT(*) FROM orders WHERE status='Pending'")->fetchColumn();
                $revenue = (float)$db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ('Processing','Shipped','Completed')")->fetchColumn();
                $topBooks = $db->query("SELECT oi.book_id, oi.title_snapshot, SUM(oi.qty) as sold FROM order_items oi GROUP BY oi.book_id, oi.title_snapshot ORDER BY sold DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
                $lowStock = $db->query("SELECT id, title, stock_qty FROM books WHERE stock_qty < 5 ORDER BY stock_qty ASC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

                return [
                    'totOrders'=>$totOrders,
                    'pending'=>$pending,
                    'revenue'=>$revenue,
                    'topBooks'=>$topBooks,
                    'lowStock'=>$lowStock
                ];
            } catch (Throwable $e) {
                return [];
            }
        }
    }
}
