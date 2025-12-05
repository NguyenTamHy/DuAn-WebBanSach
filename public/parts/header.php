<?php
// public/parts/header.php
declare(strict_types=1);

// Nếu bạn chạy file này trực tiếp trong editor, giúp cho Intelephense nhận diện các hàm,
// hãy đảm bảo helpers được include. Nếu index.php đã require helpers, require_once sẽ bỏ qua.
$appRoot = realpath(__DIR__ . '/../../') ?: __DIR__ . '/..';
if (file_exists($appRoot . '/app/helpers.php')) {
    require_once $appRoot . '/app/helpers.php';
} else {
    // fallback nhẹ để editor không báo lỗi khi mở file đơn lẻ
    if (!function_exists('e')) {
        function e($s) { return htmlspecialchars((string)($s ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
    }
    if (!function_exists('user')) {
        function user() { return $_SESSION['user'] ?? null; }
    }
    if (!function_exists('base_url')) {
        function base_url($p='') { $p = ltrim((string)$p, '/'); return ($p === '' ? '/' : '/'.$p); }
    }
}
?><!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= isset($title) ? e($title) : 'Bookstore' ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <script defer src="/assets/js/app.js"></script>
</head>
<body>
<header class="site-header">
  <div class="wrap header-inner">
    <div class="logo"><a href="/"><strong>H&K</strong></a></div>
    <nav class="main-nav">
      <a href="/">Trang chủ</a>
      <a href="/cart">Giỏ hàng</a>
      <a href="/checkout">Thanh toán</a>
      <a href="/admin">Admin</a>
    </nav>
    <div class="user-area">
      <?php if (user()): ?>
        <span>Xin chào, <?= e(user()['name'] ?? user()['email']) ?></span>
        <a href="/logout">Đăng xuất</a>
      <?php else: ?>
        <a href="/login">Đăng nhập</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="container">
