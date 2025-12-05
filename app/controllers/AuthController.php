<?php
// app/controllers/AuthController.php

require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../Mailer.php';

class AuthController
{
    public function login()
    {
        render('auth_login');
    }

    public function loginPost()
    {
        csrf_check();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Vui lòng nhập email và mật khẩu.';
            redirect('index.php?c=auth&a=login');
        }

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng.';
            redirect('index.php?c=auth&a=login');
        }

        if ((int)$user['is_active'] !== 1) {
            $_SESSION['error'] = 'Tài khoản đã bị khóa.';
            redirect('index.php?c=auth&a=login');
        }

        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'] ?? $user['email'];
        $_SESSION['user_role'] = $user['role'] ?? 'USER';

        redirect('index.php');
    }

    public function register()
    {
        render('auth_register');
    }

    public function registerPost()
    {
        csrf_check();

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirmation'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
            redirect('index.php?c=auth&a=register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ.';
            redirect('index.php?c=auth&a=register');
        }

        if ($password !== $confirm) {
            $_SESSION['error'] = 'Mật khẩu nhập lại không khớp.';
            redirect('index.php?c=auth&a=register');
        }

        if (User::findByEmail($email)) {
            $_SESSION['error'] = 'Email đã tồn tại.';
            redirect('index.php?c=auth&a=register');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $id = User::create([
            'email'         => $email,
            'password_hash' => $hash,
            'name'          => $name,
            'role'          => 'USER',
            'is_active'     => 1,
        ]);

        $_SESSION['user_id']   = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = 'USER';

        redirect('index.php');
    }

    public function logout()
    {
        session_destroy();
        redirect('index.php');
    }

    /** Form quên mật khẩu */
    public function forgot()
    {
        render('auth_forgot');
    }

    /** Xử lý form quên mật khẩu */
    public function forgotPost()
    {
        csrf_check();

        $email = trim($_POST['email'] ?? '');

        if ($email === '') {
            $_SESSION['error'] = 'Vui lòng nhập email.';
            redirect('index.php?c=auth&a=forgot');
        }

        $user = User::findByEmail($email);

        if ($user) {
            $token = User::createResetToken((int)$user['id']);
            $resetLink = base_url('index.php?c=auth&a=reset&token=' . urlencode($token));

            $ok = sendResetEmail($user['name'] ?? $user['email'], $user['email'], $resetLink);

            // Nếu gửi email lỗi, chỉ ghi log – không báo lỗi ra giao diện
            if (!$ok) {
                error_log('Không gửi được email reset mật khẩu cho: ' . $user['email']);
                // KHÔNG set $_SESSION['error'] để tránh hiện thông báo "Không gửi được email, vui lòng thử lại sau."
            }
        }

        $_SESSION['message'] = 'Nếu email tồn tại, chúng tôi đã gửi hướng dẫn đặt lại mật khẩu.';
        redirect('index.php?c=auth&a=forgot');
    }

    /** Form reset mật khẩu */
    public function reset()
    {
        $token = $_GET['token'] ?? '';
        if ($token === '') {
            http_response_code(400);
            echo 'Token không hợp lệ.';
            exit;
        }

        $user = User::findByResetToken($token);
        if (!$user) {
            echo 'Liên kết đã hết hạn hoặc không hợp lệ.';
            exit;
        }

        render('auth_reset', ['token' => $token]);
    }

    /** Xử lý reset mật khẩu */
    public function resetPost()
    {
        csrf_check();

        $token    = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirmation'] ?? '';

        if ($token === '' || $password === '' || $password !== $confirm) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
            redirect('index.php?c=auth&a=reset&token=' . urlencode($token));
        }

        $user = User::findByResetToken($token);
        if (!$user) {
            echo 'Token đã hết hạn hoặc không hợp lệ.';
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        User::updatePassword((int)$user['id'], $hash);
        User::clearResetToken((int)$user['id']);

        $_SESSION['message'] = 'Đặt lại mật khẩu thành công, vui lòng đăng nhập.';
        redirect('index.php?c=auth&a=login');
    }
}
