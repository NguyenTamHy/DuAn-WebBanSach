<?php
// app/helpers.php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

function base_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return rtrim(BASE_URL, '/') . ($path ? '/' . $path : '');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function render(string $view, array $data = []): void
{
    extract($data);
    $viewFile = VIEW_PATH . '/' . $view . '.php';

    if (!file_exists($viewFile)) {
        http_response_code(500);
        echo "View not found: " . e($view);
        exit;
    }

    include VIEW_PATH . '/layout.php';
}

/** CSRF */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

// Alias nếu bạn quen dùng csrf_input()
function csrf_input(): void
{
    echo csrf_field();
}

function csrf_check(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        $formToken    = $_POST['_token'] ?? '';

        if (!$sessionToken || !$formToken || !hash_equals($sessionToken, $formToken)) {
            $_SESSION['error'] = 'Phiên đã hết hạn, vui lòng gửi lại form.';
            redirect('index.php?c=auth&a=login');
        }
    }
}

/** Format tiền VNĐ */
function money(float|int $amount): string
{
    return number_format((float)$amount, 0, ',', '.') . ' đ';
}

/** Tính giá sau giảm */
function book_effective_price(array $book): float
{
    $price = (float)($book['price'] ?? 0);
    $discountPercent = (int)($book['discount_percent'] ?? 0);

    if ($discountPercent <= 0) {
        return $price;
    }
    $discountPercent = max(0, min(100, $discountPercent));
    $final = $price * (100 - $discountPercent) / 100;
    return round($final, 2);
}

function book_has_discount(array $book): bool
{
    return ((int)($book['discount_percent'] ?? 0)) > 0;
}
