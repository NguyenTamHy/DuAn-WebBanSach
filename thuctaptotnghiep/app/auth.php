<?php
// app/auth.php
require_once __DIR__ . '/db.php';

function auth_user()
{
    if (!empty($_SESSION['user_id'])) {
        static $user = null;
        if ($user === null) {
            $stmt = db()->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        }
        return $user;
    }
    return null;
}

function is_admin(): bool
{
    $user = auth_user();
    return $user && in_array($user['role'], ['ADMIN', 'STAFF'], true);
}

function auth_check()
{
    if (!auth_user()) {
        redirect('index.php?c=auth&a=login');
    }
}

function auth_check_admin()
{
    if (!is_admin()) {
        http_response_code(403);
        echo "Forbidden";
        exit;
    }
}
