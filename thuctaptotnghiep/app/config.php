<?php
// app/config.php

// ==== CONFIG DB ====
define('DB_HOST', 'localhost');
define('DB_NAME', 'thuctaptotnghiep');
define('DB_USER', 'root');
define('DB_PASS', 'Ai1000!');

// BASE_URL: đường dẫn tới thư mục public trên web server
// VD: nếu truy cập là http://localhost/bookstore/public/index.php
// thì BASE_URL là '/bookstore/public'
define('BASE_URL', '/thuctaptotnghiep/public');

// ==== PATHS ====
define('APP_PATH', dirname(__DIR__) . '/app');
define('VIEW_PATH', APP_PATH . '/views');

// Bật session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
