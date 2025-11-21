<?php
require_once "includes/config.php";

$sql = "SELECT * FROM books";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo $row['title'] . " - " . $row['price'] . "<br>";
    }
} else {
    echo "Không có sách nào!";
}
?>
