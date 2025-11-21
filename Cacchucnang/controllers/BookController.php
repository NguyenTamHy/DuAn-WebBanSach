<?php
require_once './includes/config.php';
require_once './models/Book.php';

$bookModel = new Book($conn);

if (isset($_GET['id'])) {
    $book = $bookModel->getBookById($_GET['id']);
} else {
    $books = $bookModel->getAllBooks();
}
?>
