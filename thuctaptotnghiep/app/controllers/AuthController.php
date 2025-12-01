<?php
// app/controllers/AuthController.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../auth.php';

class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = User::findByEmail($email);
            if ($user && password_verify($password, $user['password_hash']) && $user['is_active']) {
                $_SESSION['user_id'] = $user['id'];
                redirect('index.php');
            } else {
                $error = 'Email hoặc mật khẩu không đúng.';
                render('auth_login', ['error' => $error, 'old' => ['email' => $email]]);
                return;
            }
        } else {
            render('auth_login');
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $email            = trim($_POST['email'] ?? '');
            $password         = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $name             = trim($_POST['name'] ?? '');
            $phone            = trim($_POST['phone'] ?? '');

            $errors = [];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            }
            if (strlen($password) < 6) {
                $errors[] = 'Mật khẩu phải ≥ 6 ký tự';
            }
            if ($password !== $password_confirm) {
                $errors[] = 'Xác nhận mật khẩu không trùng khớp';
            }
            if (User::findByEmail($email)) {
                $errors[] = 'Email đã tồn tại';
            }

            if (empty($errors)) {
                $user_id = User::create([
                    'email'    => $email,
                    'password' => $password,
                    'name'     => $name,
                    'phone'    => $phone,
                ]);
                $_SESSION['user_id'] = $user_id;
                redirect('index.php');
            } else {
                render('auth_register', [
                    'errors' => $errors,
                    'old'    => compact('email', 'name', 'phone'),
                ]);
                return;
            }
        } else {
            render('auth_register');
        }
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        redirect('index.php');
    }
}
