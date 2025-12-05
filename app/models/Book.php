<?php
// app/models/Book.php

require_once __DIR__ . '/../db.php';

class Book
{
    public static function find(int $id): ?array
    {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findMany(array $ids): array
    {
        if (empty($ids)) return [];
        $pdo = db();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        return $stmt->fetchAll();
    }

    /** Sách mới */
    public static function latest(int $limit = 8): array
    {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT * FROM books ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Sách giảm giá */
    public static function discounted(int $limit = 8): array
    {
        $pdo = db();
        $stmt = $pdo->prepare("
            SELECT * FROM books
            WHERE discount_percent > 0
            ORDER BY discount_percent DESC, created_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Bestseller dựa vào order_items */
    public static function bestSellers(int $limit = 8): array
    {
        $pdo = db();
        $stmt = $pdo->prepare("
            SELECT b.*, COALESCE(SUM(oi.qty), 0) AS sold_qty
            FROM books b
            LEFT JOIN order_items oi ON oi.book_id = b.id
            GROUP BY b.id
            ORDER BY sold_qty DESC, b.created_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Lấy sách theo category */
    public static function byCategory(int $categoryId, int $limit = 12): array
    {
        $pdo = db();
        $stmt = $pdo->prepare("
            SELECT b.*
            FROM books b
            JOIN book_categories bc ON bc.book_id = b.id
            WHERE bc.category_id = ?
            ORDER BY b.created_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Thông tin admin */
    public static function allForAdmin(): array
    {
        $pdo = db();
        $stmt = $pdo->query("
            SELECT b.*,
                   COALESCE(SUM(oi.qty), 0) AS sold_qty,
                   COALESCE(AVG(r.rating), 0) AS avg_rating
            FROM books b
            LEFT JOIN order_items oi ON oi.book_id = b.id
            LEFT JOIN reviews r      ON r.book_id = b.id
            GROUP BY b.id
            ORDER BY b.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function categoryIds(int $bookId): array
    {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT category_id FROM book_categories WHERE book_id = ?");
        $stmt->execute([$bookId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public static function create(array $data, array $categoryIds = []): int
    {
        $pdo = db();
        $stmt = $pdo->prepare("
            INSERT INTO books (title, slug, isbn, price, stock_qty, cover_url, description,
                               publisher_id, published_at, discount_percent)
            VALUES (:title, :slug, :isbn, :price, :stock_qty, :cover_url, :description,
                    :publisher_id, :published_at, :discount_percent)
        ");
        $stmt->execute([
            ':title'            => $data['title'],
            ':slug'             => $data['slug'] ?? null,
            ':isbn'             => $data['isbn'] ?? null,
            ':price'            => $data['price'],
            ':stock_qty'        => $data['stock_qty'] ?? 0,
            ':cover_url'        => $data['cover_url'] ?? null,
            ':description'      => $data['description'] ?? null,
            ':publisher_id'     => $data['publisher_id'] ?? null,
            ':published_at'     => $data['published_at'] ?? null,
            ':discount_percent' => $data['discount_percent'] ?? 0,
        ]);

        $bookId = (int)$pdo->lastInsertId();
        self::syncCategories($bookId, $categoryIds);
        return $bookId;
    }

    public static function updateById(int $id, array $data, array $categoryIds = []): bool
    {
        $pdo = db();
        $stmt = $pdo->prepare("
            UPDATE books
               SET title            = :title,
                   slug             = :slug,
                   isbn             = :isbn,
                   price            = :price,
                   stock_qty        = :stock_qty,
                   cover_url        = :cover_url,
                   description      = :description,
                   publisher_id     = :publisher_id,
                   published_at     = :published_at,
                   discount_percent = :discount_percent
             WHERE id = :id
        ");

        $ok = $stmt->execute([
            ':title'            => $data['title'],
            ':slug'             => $data['slug'] ?? null,
            ':isbn'             => $data['isbn'] ?? null,
            ':price'            => $data['price'],
            ':stock_qty'        => $data['stock_qty'] ?? 0,
            ':cover_url'        => $data['cover_url'] ?? null,
            ':description'      => $data['description'] ?? null,
            ':publisher_id'     => $data['publisher_id'] ?? null,
            ':published_at'     => $data['published_at'] ?? null,
            ':discount_percent' => $data['discount_percent'] ?? 0,
            ':id'               => $id,
        ]);

        self::syncCategories($id, $categoryIds);
        return $ok;
    }

    public static function deleteById(int $id): bool
    {
        $pdo = db();
        $pdo->prepare("DELETE FROM book_categories WHERE book_id = ?")->execute([$id]);
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        return $stmt->execute([$id]);
    }

    protected static function syncCategories(int $bookId, array $categoryIds): void
    {
        $pdo = db();
        $pdo->prepare("DELETE FROM book_categories WHERE book_id = ?")->execute([$bookId]);
        $categoryIds = array_unique(array_filter(array_map('intval', $categoryIds)));

        if (!$categoryIds) return;

        $stmt = $pdo->prepare("INSERT INTO book_categories (book_id, category_id) VALUES (:book_id, :category_id)");
        foreach ($categoryIds as $cid) {
            $stmt->execute([
                ':book_id'     => $bookId,
                ':category_id' => $cid,
            ]);
        }
    }
}
