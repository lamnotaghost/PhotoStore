<?php
$servername = "127.0.0.1:3306
";
$username = "root";
$password = "";
$dbname = "photo_orders";

header('Content-Type: application/json');

try {
    // Создаем соединение
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Проверяем соединение
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Устанавливаем кодировку UTF-8
    $conn->set_charset("utf8");

    // Получаем категорию из запроса
    $category = isset($_GET['category']) ? $_GET['category'] : '';

    // Получаем товары по категории
    $sql = "SELECT * FROM products WHERE category=?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = array();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode($products);
} catch (Exception $e) {
    echo json_encode(array("error" => $e->getMessage()));
    http_response_code(500);
    exit;
}
?>
