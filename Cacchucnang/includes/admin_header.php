<?php
require_once __DIR__ . '/session.php';
require_admin();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin - Bookstore</title>
  <link rel="stylesheet" href="/bookstore/assets/css/admin.css">
</head>
<body>
<header>
  <h2>Admin Panel</h2>
  <nav><a href="?action=dashboard">Dashboard</a> | <a href="?action=books">Books</a> | <a href="?action=users">Users</a> | <a href="?action=orders">Orders</a> | <a href="/bookstore/logout.php">Logout</a></nav>
</header>
<main>
