<?php
// Подключение к базе данных (замените данными вашего сервера)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "photo_orders";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение данных из POST запроса
$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];
$name = $conn->real_escape_string($data['name']);
$description = $conn->real_escape_string($data['description']);
$price = $conn->real_escape_string($data['price']);
$category = $conn->real_escape_string($data['category']);
$image_name = $conn->real_escape_string($data['image_name']);

// Проверка наличия product_id
if (!isset($product_id)) {
    echo "Ошибка: ID товара не указан.";
    exit;
}

// Обновление товара в таблице products
$sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_name = ? WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdssi", $name, $description, $price, $category, $image_name, $product_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Ошибка при обновлении товара: " . $conn->error;
}

// Закрытие соединения с базой данных
$stmt->close();
$conn->close();
?>
