<?php
header('Content-Type: application/json');

// Подключение к базе данных (замените данными вашего сервера)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "photo_orders";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Получение данных о пользователе и корзине из POST запроса
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $conn->real_escape_string($_POST['first_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $password = $conn->real_escape_string($_POST['password']);
    $cart = json_decode($_POST['cart'], true);

    if (!$firstName || !$lastName || !$email || !$address || empty($cart)) {
        echo json_encode(['success' => false, 'error' => 'Все поля формы и корзина должны быть заполнены.']);
        exit;
    }

    // Начинаем транзакцию
    $conn->begin_transaction();

    try {
        // Проверяем, существует ли пользователь с указанным email
        $sqlCheckUser = "SELECT * FROM users WHERE email = '$email' FOR UPDATE";
        $result = $conn->query($sqlCheckUser);

        if ($result->num_rows > 0) {
            // Пользователь уже существует, получаем его user_id
            $row = $result->fetch_assoc();
            $userId = $row['user_id'];
        } else {
            // Пользователь не существует, добавляем его в таблицу users
            $sqlInsertUser = "INSERT INTO users (first_name, last_name, email, address, password) VALUES ('$firstName', '$lastName', '$email', '$address', '$password')";

            if ($conn->query($sqlInsertUser) === TRUE) {
                $userId = $conn->insert_id;
            } else {
                throw new Exception("Ошибка при добавлении пользователя: " . $conn->error);
            }
        }

        // Вычисляем общую стоимость заказа
        $totalPrice = 0;
        foreach ($cart as $item) {
            $productId = $conn->real_escape_string($item['id']);
            $quantity = $conn->real_escape_string($item['quantity']);
            $unitPrice = $conn->real_escape_string($item['price']);

            // Рассчитываем общую стоимость каждой позиции в корзине
            $itemTotalPrice = $quantity * $unitPrice;
            $totalPrice += $itemTotalPrice;
        }

        // SQL запрос для добавления заказа в таблицу orders
        $orderDate = date('Y-m-d H:i:s');
        $sqlOrder = "INSERT INTO orders (user_id, order_date, total_price) VALUES ('$userId', '$orderDate', '$totalPrice')";

        if ($conn->query($sqlOrder) === TRUE) {
            $orderId = $conn->insert_id;

            // SQL запрос для добавления товаров в таблицу order_items
            foreach ($cart as $item) {
                $productId = $conn->real_escape_string($item['id']);
                $quantity = $conn->real_escape_string($item['quantity']);
                $unitPrice = $conn->real_escape_string($item['price']);

                $sqlOrderItems = "INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES ('$orderId', '$productId', '$quantity', '$unitPrice')";

                if ($conn->query($sqlOrderItems) !== TRUE) {
                    throw new Exception("Ошибка при добавлении товара в заказ: " . $conn->error);
                }
            }

            // Завершаем транзакцию
            $conn->commit();

            echo json_encode(['success' => true, 'message' => 'Заказ успешно оформлен!']);
        } else {
            throw new Exception("Ошибка при добавлении заказа: " . $conn->error);
        }
    } catch (Exception $e) {
        // Откатываем транзакцию в случае ошибки
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка: Неверный метод запроса.']);
}

// Закрытие соединения с базой данных
$conn->close();
?>
