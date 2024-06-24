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

// Получение данных о продуктах
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    echo json_encode(['error' => 'No products found']);
    exit;
}

// Возвращаем данные в формате JSON
echo json_encode($products);

// Закрытие соединения с базой данных
$conn->close();
?>
