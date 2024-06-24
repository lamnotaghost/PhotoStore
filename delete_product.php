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

// Проверка наличия product_id
if (!isset($product_id)) {
    echo "Ошибка: ID товара не указан.";
    exit;
}

// Удаление товара из таблицы products
$sql = "DELETE FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Ошибка при удалении товара: " . $conn->error;
}

// Закрытие соединения с базой данных
$stmt->close();
$conn->close();
?>
