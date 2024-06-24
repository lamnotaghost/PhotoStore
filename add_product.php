<?php
session_start();

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    exit(json_encode(['success' => false, 'error' => 'Доступ запрещен']));
}

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "photo_orders";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    exit(json_encode(['success' => false, 'error' => 'Ошибка подключения к базе данных']));
}

// Получение данных из формы
$name = $conn->real_escape_string($_POST['name']);
$description = $conn->real_escape_string($_POST['description']);
$price = floatval($_POST['price']);
$category = $conn->real_escape_string($_POST['category']);
$image_name = $conn->real_escape_string($_POST['image']);

// SQL запрос для добавления нового товара
$sql = "INSERT INTO products (name, description, price, category, image_name) VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdss", $name, $description, $price, $category, $image_name);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>