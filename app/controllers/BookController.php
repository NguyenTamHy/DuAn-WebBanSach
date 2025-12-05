<?php
// app/controllers/BookController.php

require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Review.php';

class BookController
{
    public function detail()
    {
        $id = (int)($_GET['id'] ?? 0);
        $book = Book::find($id);
        if (!$book) {
            http_response_code(404);
            echo "Book not found";
            exit;
        }

        $categoryIds = Book::categoryIds($id);
        $related = [];
        if (!empty($categoryIds)) {
            $related = Book::byCategory((int)$categoryIds[0], 6);
        }

        $reviews = Review::forBook($id);
        $avgRating = Review::avgRating($id);

        render('book_detail', compact('book', 'related', 'reviews', 'avgRating'));
    }

    /** Đánh giá sách */
    public function reviewPost()
    {
        if (!is_logged_in()) {
            $_SESSION['error'] = 'Bạn cần đăng nhập để đánh giá.';
            redirect('index.php?c=auth&a=login');
        }

        $bookId  = (int)($_POST['book_id'] ?? 0);
        $rating  = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($bookId <= 0 || $rating <= 0) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
            redirect('index.php?c=book&a=detail&id=' . $bookId);
        }

        Review::create(current_user_id(), $bookId, $rating, $comment);

        $_SESSION['message'] = 'Cảm ơn bạn đã đánh giá!';
        redirect('index.php?c=book&a=detail&id=' . $bookId);
    }
}
