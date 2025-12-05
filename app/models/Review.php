<?php
// app/models/Review.php

require_once __DIR__ . '/../db.php';

class Review
{
    public static function forBook(int $bookId): array
    {
        $pdo = db();
        $stmt = $pdo->prepare("
            SELECT r.*, u.name AS user_name
            FROM reviews r
            LEFT JOIN users u ON u.id = r.user_id
            WHERE r.book_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }

    public static function create(int $userId, int $bookId, int $rating, string $comment): void
    {
        $rating = max(1, min(5, $rating));
        $pdo = db();
        $stmt = $pdo->prepare("
            INSERT INTO reviews (user_id, book_id, rating, comment)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $bookId, $rating, $comment]);
    }

    public static function avgRating(int $bookId): float
    {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT AVG(rating) AS avg_rating FROM reviews WHERE book_id = ?");
        $stmt->execute([$bookId]);
        $row = $stmt->fetch();
        return (float)($row['avg_rating'] ?? 0);
    }
}
