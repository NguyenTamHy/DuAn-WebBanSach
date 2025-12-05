<?php
// app/controllers/OrderController.php
declare(strict_types=1);

if (!class_exists('OrderController')) {
    class OrderController
    {
        public static function showOrderForUser(string $code, int $userId)
        {
            return OrderModel::findByCodeForUser($code, $userId);
        }

        public static function listOrdersForAdmin(): array
        {
            return OrderModel::listAll();
        }

        public static function updateOrderStatus(int $orderId, string $status): void
        {
            OrderModel::updateStatus($orderId, $status);
        }
    }
}
