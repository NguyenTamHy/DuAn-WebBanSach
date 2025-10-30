<?php
header("Content-Type: application/json; charset=UTF-8");
include_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["name"], $data["email"], $data["password"])) {
    http_response_code(400);
    echo json_encode(["message" => "Thiếu thông tin"]);
    exit;
}

$db = (new Database())->getConnection();

$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$data["email"]]);
if ($stmt->rowCount() > 0) {
    echo json_encode(["message" => "Email đã tồn tại"]);
    exit;
}

$password_hash = password_hash($data["password"], PASSWORD_BCRYPT);

$stmt = $db->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'User')");
if ($stmt->execute([$data["name"], $data["email"], $password_hash])) {
    echo json_encode(["message" => "Đăng ký thành công"]);
} else {
    echo json_encode(["message" => "Đăng ký thất bại"]);
}
?>
