<?php
// app/controllers/HomeController.php

require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Category.php';

class HomeController
{
    public function index()
    {
        $latest       = Book::latest(8);
        $discounted   = Book::discounted(8);
        $bestSellers  = Book::bestSellers(8);
        $categories   = Category::all();

        render('home', compact('latest', 'discounted', 'bestSellers', 'categories'));
    }
}
