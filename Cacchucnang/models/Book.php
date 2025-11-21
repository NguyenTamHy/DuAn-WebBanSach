<?php
class Book {
    private $conn;
    private $table = "books";
    public function __construct($db) { $this->conn = $db; }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM {$this->table} ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]); return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($title, $author, $price, $stock, $category_id, $cover_image, $description) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (title, author, price, stock, category_id, cover_image, description) VALUES (?,?,?,?,?,?,?)");
        return $stmt->execute([$title, $author, $price, $stock, $category_id, $cover_image, $description]);
    }

    public function update($id, $title, $author, $price, $stock, $category_id, $description) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET title=?, author=?, price=?, stock=?, category_id=?, description=? WHERE id=?");
        return $stmt->execute([$title, $author, $price, $stock, $category_id, $description, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>
