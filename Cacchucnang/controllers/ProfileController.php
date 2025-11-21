<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/User.php';

require_login();

$userModel = new User($conn);
$uid = $_SESSION['user']['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullname = $_POST['fullname'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';

    $stmt = $conn->prepare("UPDATE users SET fullname=?, address=?, phone=? WHERE id=?");
    $stmt->execute([$fullname, $address, $phone, $uid]);

    // refresh session data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$uid]);
    $_SESSION['user'] = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Location: /bookstore/profile.php?update=1');
    exit;
}

// show profile view
$user = $_SESSION['user'];
include __DIR__ . '/../profile.php';
