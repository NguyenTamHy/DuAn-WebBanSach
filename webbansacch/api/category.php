<?php
header("Content-Type: application/json; charset=UTF-8");
include_once "../config/db.php";
$db = (new Database())->getConnection();

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        $stmt = $db->query("SELECT * FROM categories");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute([$data["name"], $data["description"]]);
        echo json_encode(["message" => "Thêm thể loại thành công"]);
        break;

    case "PUT":
        parse_str($_SERVER["QUERY_STRING"], $params);
        $id = $params["id"] ?? null;
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $db->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
        $stmt->execute([$data["name"], $data["description"], $id]);
        echo json_encode(["message" => "Cập nhật thành công"]);
        break;

    case "DELETE":
        parse_str($_SERVER["QUERY_STRING"], $params);
        $id = $params["id"] ?? null;
        $stmt = $db->prepare("DELETE FROM categories WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "Xóa thành công"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Phương thức không được hỗ trợ"]);
}
?>
