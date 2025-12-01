<?php
// app/helpers.php
require_once __DIR__ . '/config.php';

function base_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return rtrim(BASE_URL, '/') . '/' . $path;
}

function redirect(string $path)
{
    header('Location: ' . base_url($path));
    exit;
}

function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function render(string $view, array $data = [])
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

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            die('Invalid CSRF token');
        }
    }
}
