<?php
// app/models/Review.php

require_once __DIR__ . '/../db.php';

class Review
{
    public static function forBook(int $book_id)
    {
        $stmt = db()->prepare("
            SELECT r.*, u.name AS user_name
            FROM reviews r
            JOIN users u ON u.id = r.user_id
            WHERE r.book_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$book_id]);
        return $stmt->fetchAll();
    }

    public static function create(int $user_id, int $book_id, int $rating, string $comment)
    {
        $stmt = db()->prepare("
            INSERT INTO reviews (user_id, book_id, rating, comment)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $book_id, $rating, $comment]);
    }

    public static function avgRating(int $book_id)
    {
        $stmt = db()->prepare("
            SELECT AVG(rating) AS avg_rating, COUNT(*) AS cnt
            FROM reviews
            WHERE book_id = ?
        ");
        $stmt->execute([$book_id]);
        return $stmt->fetch();
    }
}
