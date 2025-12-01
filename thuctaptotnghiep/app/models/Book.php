<?php
// app/models/Book.php

require_once __DIR__ . '/../db.php';

class Book
{
    public static function all(int $limit = 20, int $offset = 0)
    {
        $sql = "SELECT b.*, p.name AS publisher_name
                FROM books b
                LEFT JOIN publishers p ON p.id = b.publisher_id
                ORDER BY b.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = db()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function find(int $id)
    {
        $sql = "SELECT b.*, p.name AS publisher_name
                FROM books b
                LEFT JOIN publishers p ON p.id = b.publisher_id
                WHERE b.id = ?";
        $stmt = db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function search(string $keyword = '', ?int $categoryId = null, int $limit = 20, int $offset = 0)
    {
        $params = [];
        $where = "1=1";

        if ($keyword !== '') {
            $where .= " AND b.title LIKE :q";
            $params[':q'] = '%' . $keyword . '%';
        }

        if ($categoryId) {
            $where .= " AND EXISTS (
                SELECT 1 FROM book_categories bc
                WHERE bc.book_id = b.id AND bc.category_id = :cid
            )";
            $params[':cid'] = $categoryId;
        }

        $sql = "SELECT b.*
                FROM books b
                WHERE $where
                ORDER BY b.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = db()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getAuthors(int $book_id): array
    {
        $sql = "SELECT a.*
                FROM authors a
                JOIN book_authors ba ON ba.author_id = a.id
                WHERE ba.book_id = ?";
        $stmt = db()->prepare($sql);
        $stmt->execute([$book_id]);
        return $stmt->fetchAll();
    }

    public static function getCategories(int $book_id): array
    {
        $sql = "SELECT c.*
                FROM categories c
                JOIN book_categories bc ON bc.category_id = c.id
                WHERE bc.book_id = ?";
        $stmt = db()->prepare($sql);
        $stmt->execute([$book_id]);
        return $stmt->fetchAll();
    }

    public static function save(array $data, ?int $id = null): int
    {
        if ($id === null) {
            $stmt = db()->prepare("
                INSERT INTO books (title, slug, isbn, price, stock_qty, cover_url, description, publisher_id)
                VALUES (:title, :slug, :isbn, :price, :stock_qty, :cover_url, :description, :publisher_id)
            ");
        } else {
            $stmt = db()->prepare("
                UPDATE books
                SET title=:title, slug=:slug, isbn=:isbn, price=:price, stock_qty=:stock_qty,
                    cover_url=:cover_url, description=:description, publisher_id=:publisher_id
                WHERE id=:id
            ");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }

        $stmt->execute([
            ':title'       => $data['title'],
            ':slug'        => $data['slug'] ?? null,
            ':isbn'        => $data['isbn'] ?? null,
            ':price'       => $data['price'],
            ':stock_qty'   => $data['stock_qty'] ?? 0,
            ':cover_url'   => $data['cover_url'] ?? null,
            ':description' => $data['description'] ?? null,
            ':publisher_id'=> $data['publisher_id'] ?? null,
        ]);

        return $id ?? (int)db()->lastInsertId();
    }

    public static function delete(int $id): bool
    {
        $stmt = db()->prepare("DELETE FROM books WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
