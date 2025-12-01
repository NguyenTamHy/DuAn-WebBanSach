<?php
// app/controllers/BookController.php

require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../auth.php';

class BookController
{
    public function detail()
    {
        $id = (int)($_GET['id'] ?? 0);
        $book = Book::find($id);
        if (!$book) {
            http_response_code(404);
            echo "Book not found";
            return;
        }

        $authors    = Book::getAuthors($id);
        $categories = Book::getCategories($id);

        $reviews     = Review::forBook($id);
        $ratingStats = Review::avgRating($id);

        render('book_detail', [
            'book'        => $book,
            'reviews'     => $reviews,
            'ratingStats' => $ratingStats,
            'authors'     => $authors,
            'categories'  => $categories,
        ]);
    }

    public function addReview()
    {
        csrf_check();
        $user = auth_user();
        if (!$user) {
            redirect('index.php?c=auth&a=login');
        }
        $book_id = (int)($_POST['book_id'] ?? 0);
        $rating  = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($book_id > 0 && $rating >= 1 && $rating <= 5) {
            Review::create($user['id'], $book_id, $rating, $comment);
        }
        redirect('index.php?c=book&a=detail&id=' . $book_id);
    }
}
