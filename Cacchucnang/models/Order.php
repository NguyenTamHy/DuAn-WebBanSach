<?php
class Order {
    private $conn; private $table = "orders";
    public function __construct($db) { $this->conn = $db; }

    public function countOrders() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }

    public function countOrdersByStatus($status) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE status = ?");
        $stmt->execute([$status]); return $stmt->fetchColumn();
    }

    public function sumRevenue() {
        $stmt = $this->conn->query("SELECT COALESCE(SUM(total),0) FROM {$this->table} WHERE status='Completed'");
        return $stmt->fetchColumn();
    }

    public function getAll() {
        // join with users for email
        $stmt = $this->conn->query("SELECT o.*, u.email as user_email FROM {$this->table} o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($orderId, $status) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }
}
?>
