<?php
// app/controllers/HomeController.php

require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../helpers.php';

class HomeController
{
    public function index()
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 12;
        $offset = ($page - 1) * $limit;

        $keyword = trim($_GET['q'] ?? '');
        if ($keyword !== '') {
            $books = Book::search($keyword, null, $limit, $offset);
        } else {
            $books = Book::all($limit, $offset);
        }

        $categories = Category::all();

        render('home', [
            'books'      => $books,
            'categories' => $categories,
            'keyword'    => $keyword,
            'page'       => $page,
        ]);
    }
}
