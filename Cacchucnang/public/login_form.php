<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        "email" => $_POST["email"],
        "password" => $_POST["password"]
    ];

    $url = "http://localhost/book_store/api/login.php";
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

    if (!empty($response["user"])) {
        $_SESSION["user"] = $response["user"];
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Đăng nhập</h2>
  <form method="POST">
    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Mật khẩu:</label>
    <input type="password" name="password" required>

    <button type="submit">Đăng nhập</button>
  </form>

  <?php if (!empty($response["message"])): ?>
    <p class="message"><?= htmlspecialchars($response["message"]) ?></p>
  <?php endif; ?>

  <p>Chưa có tài khoản? <a href="register_form.php">Đăng ký</a></p>
</div>
</body>
</html>
