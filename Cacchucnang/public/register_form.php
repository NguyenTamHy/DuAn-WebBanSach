<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        "name" => $_POST["name"],
        "email" => $_POST["email"],
        "password" => $_POST["password"]
    ];

    $url = "http://localhost/book_store/api/register.php";
    $options = [
        "http" => [
            "header" => "Content-Type: application/json\r\n",
            "method" => "POST",
            "content" => json_encode($data)
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result, true);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng ký tài khoản</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Đăng ký tài khoản</h2>
  <form method="POST">
    <label>Họ tên:</label>
    <input type="text" name="name" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Mật khẩu:</label>
    <input type="password" name="password" required>

    <button type="submit">Đăng ký</button>
  </form>

  <?php if (!empty($response["message"])): ?>
    <p class="message"><?= htmlspecialchars($response["message"]) ?></p>
  <?php endif; ?>

  <p>Đã có tài khoản? <a href="login_form.php">Đăng nhập</a></p>
</div>
</body>
</html>
