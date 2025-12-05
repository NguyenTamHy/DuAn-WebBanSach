<?php
// app/Mailer.php

require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Đường dẫn tới thư viện PHPMailer giống như bạn dùng trong forgot_password.php
require_once __DIR__ . '/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';

/**
 * Gửi email đặt lại mật khẩu
 * - $username: tên hiển thị trong email (có thể là name hoặc email)
 * - $email: email người nhận
 * - $link: link reset (được AuthController tạo bằng base_url(...))
 */
function sendResetEmail(string $username, string $email, string $link): bool
{
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug  = 2;            // Bật debug SMTP
$mail->Debugoutput = 'error_log'; // Ghi log vào php_error_log

        // ================= CẤU HÌNH SMTP (TÁI SỬ DỤNG TỪ forgot_password.php) =================
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';

        // Lấy cấu hình từ config.php (thay vì hard-code)
        $mail->Host       = MAIL_HOST_GMAIL;          // 'smtp.gmail.com'
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USER_GMAIL;          // 'tuankhai886@gmail.com'
        $mail->Password   = MAIL_PASS_GMAIL;          // 'aade unxh gjto ubww'
        $mail->SMTPSecure = 'tls';                    // giống code cũ của bạn
        $mail->Port       = MAIL_PORT_GMAIL;          // 587

        // Fix một số môi trường localhost / XAMPP bị lỗi SSL tự ký
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        // BẬT DEBUG khi cần test (xem log trong php_error_log)
        // Khi debug xong thì comment 2 dòng dưới lại
        // $mail->SMTPDebug  = 2;
        // $mail->Debugoutput = 'error_log';

        // ================= NGƯỜI GỬI / NHẬN =================
        // Tái sử dụng ý tưởng setFrom() từ code cũ, nhưng dùng email thật
        $mail->setFrom(MAIL_FROM_EMAIL_GMAIL, MAIL_FROM_NAME_GMAIL);
        $mail->addAddress($email, $username);

        // ================= NỘI DUNG EMAIL =================
        $mail->isHTML(true);
        $mail->Subject = 'Đặt lại mật khẩu tài khoản Bookstore';

        // Dùng style email cũ từ forgot_password.php
        $mail->Body = "
            <h3>Chào {$username},</h3>
            <p>Bạn đã yêu cầu đặt lại mật khẩu. Nhấn vào liên kết dưới đây để tiếp tục:</p>
            <p><a href=\"{$link}\">{$link}</a></p>
            <p>Liên kết có hiệu lực trong 1 giờ.</p>
        ";

        $mail->AltBody = "Chào {$username},\n\n"
            . "Bạn đã yêu cầu đặt lại mật khẩu.\n"
            . "Nhấn vào liên kết sau để tiếp tục: {$link}\n"
            . "Liên kết có hiệu lực trong 1 giờ.\n";

        // Gửi mail
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Ghi lỗi ra log để bạn xem trong file php_error_log
        error_log('Mail error: ' . $mail->ErrorInfo);
        return false;
    }
}
