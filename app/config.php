<?php
// app/config.php

// ==== CONFIG DB ====
define('DB_HOST', 'localhost:3307');
define('DB_NAME', 'thuctaptotnghiep');
define('DB_USER', 'root');
define('DB_PASS', ''); // chỉnh lại nếu khác

// BASE_URL: đường dẫn tới thư mục public trên web server
// VD: http://localhost/thuctaptotnghiep/public/index.php
define('BASE_URL', 'http://localhost:8080/thuctaptotnghiep/public');


// ==== PATHS ====
define('APP_PATH', dirname(__DIR__) . '/app');
define('VIEW_PATH', APP_PATH . '/views');

// Bật session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/// ==== MAIL CONFIG ====
// MAIL_DRIVER: 'gmail' hoặc 'mailtrap'
define('MAIL_DRIVER', 'gmail');

// --- Gmail ---
define('MAIL_HOST_GMAIL', 'smtp.gmail.com');
define('MAIL_PORT_GMAIL', 587);

// Tài khoản Gmail dùng để gửi
define('MAIL_USER_GMAIL', 'tuankhai886@gmail.com');       

// App Password (mật khẩu ứng dụng) của tài khoản trên
define('MAIL_PASS_GMAIL', 'bgvt mqxf ahmw rhqc');         

// Địa chỉ FROM (NÊN trùng với MAIL_USER_GMAIL)
define('MAIL_FROM_EMAIL_GMAIL', 'tuankhai886@gmail.com');
define('MAIL_FROM_NAME_GMAIL', 'Bookstore');

// --- Mailtrap (dev) ---
define('MAIL_HOST_MAILTRAP', 'sandbox.smtp.mailtrap.io');
define('MAIL_PORT_MAILTRAP', 587);
define('MAIL_USER_MAILTRAP', 'your_mailtrap_user');    // nếu có dùng Mailtrap
define('MAIL_PASS_MAILTRAP', 'your_mailtrap_pass');
define('MAIL_FROM_EMAIL_MAILTRAP', 'no-reply@bookstore.test');
define('MAIL_FROM_NAME_MAILTRAP', 'Bookstore Dev');


