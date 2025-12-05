<?php
// app/models/Review.php
declare(strict_types=1);

if (!class_exists('ReviewModel')) {
    class ReviewModel
    {
        public static function create(int $userId, int $bookId, int $rating, string $comment): int
        {
            $st = db()->prepare("INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?,?,?,?)");
            $st->execute([$userId, $bookId, $rating, $comment]);
            $id = (int)db()->lastInsertId();

            // audit
            try {
                $a = db()->prepare("INSERT INTO audit_logs (actor_id, action, entity, entity_id, payload_json) VALUES (?,?,?,?,?)");
                $a->execute([
                    $userId, 'create_review', 'reviews', $id,
                    json_encode(['book'=>$bookId,'rating'=>$rating], JSON_UNESCAPED_UNICODE)
                ]);
            } catch (Throwable $e) {}

            return $id;
        }

        public static function listForBook(int $bookId): array
        {
            $st = db()->prepare("SELECT r.*, u.name FROM reviews r LEFT JOIN users u ON u.id=r.user_id WHERE r.book_id=? ORDER BY r.created_at DESC");
            $st->execute([$bookId]);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
