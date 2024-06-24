<?php
session_start();

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    exit('Доступ запрещен');
}

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "photo_orders";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL запрос для получения данных о продажах каждого товара
$sql = "SELECT p.product_id, p.name AS product_name, 
               SUM(oi.quantity) AS total_sold, 
               SUM(oi.quantity * oi.unit_price) AS total_revenue
        FROM products p
        LEFT JOIN order_items oi ON p.product_id = oi.product_id
        LEFT JOIN orders o ON oi.order_id = o.order_id
        GROUP BY p.product_id
        ORDER BY total_sold DESC";

$result = $conn->query($sql);

if ($result) {
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'total_sold' => $row['total_sold'] ? $row['total_sold'] : 0,
            'total_revenue' => $row['total_revenue'] ? number_format($row['total_revenue'], 2) : '0.00'
        );
    }
    
    // Подсчет общей выручки
    $total_revenue_sql = "SELECT SUM(total_price) AS total_revenue FROM orders";
    $total_revenue_result = $conn->query($total_revenue_sql);
    $total_revenue = 0;
    if ($total_revenue_result && $total_revenue_row = $total_revenue_result->fetch_assoc()) {
        $total_revenue = $total_revenue_row['total_revenue'] ? number_format($total_revenue_row['total_revenue'], 2) : '0.00';
    }

    echo json_encode(array(
        'products' => $data,
        'total_revenue' => $total_revenue
    ));
} else {
    echo json_encode(array('error' => 'Не удалось получить данные о продажах'));
}

$conn->close();
?>