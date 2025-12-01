<?php
// app/controllers/CategoryController.php

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../helpers.php';

class CategoryController
{
    public function show()
    {
        $slug = $_GET['slug'] ?? '';
        $category = Category::findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            echo "Category not found";
            return;
        }

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 12;
        $offset = ($page - 1) * $limit;

        $books = Book::search('', $category['id'], $limit, $offset);

        render('category_show', [
            'category' => $category,
            'books'    => $books,
            'page'     => $page,
        ]);
    }
}
