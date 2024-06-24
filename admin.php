<?php
session_start();

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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Административная панель</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="admin.php">Админ панель</a></li>
                <li><a href="logout.php">Выход</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="admin-panel">
            <h1>Административная панель</h1>

            <h2>Товары</h2>
                <table id="products-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название товара</th>
                            <th>Описание товара</th>
                            <th>Цена</th>
                            <th>Категория</th>
                            <th>Изображение</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Products will be loaded here -->
                    </tbody>
                </table>

                <div id="update-form-container"></div>
            
                <h2>Добавить новый товар</h2>
    <form id="add-product-form">
        <input type="text" name="name" placeholder="Название товара" required>
        <textarea name="description" placeholder="Описание товара" required></textarea>
        <input type="number" name="price" placeholder="Цена" step="0.01" required>
        <select name="category" required>
            <option value="">Выберите категорию</option>
            <option value="camera">Фотокамера</option>
            <option value="lens">Объектив</option>
        </select>
        <input type="text" name="image" placeholder="Название изображения" required>
        <button type="submit">Добавить товар</button>
    </form>
    
            <div class="sales-summary">
                <h2>Сводка продаж по товарам</h2>
                <table id="sales-data">
                    <thead>
                        <tr>
                            <th>ID товара</th>
                            <th>Название товара</th>
                            <th>Количество проданных</th>
                            <th>Общая сумма продаж</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Данные о продажах будут загружены динамически -->
                    </tbody>
                </table>
            </div>

            <div class="total-revenue" id="total-revenue">
                    <!-- Общая выручка будет загружена динамически -->
                </div>
            </div>
            
            <div>
                <h2>Пользователи</h2>
                <table id="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Фамилия</th>
                            <th>Email</th>
                            <th>Адрес</th>
                            <th>Пароль</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Данные будут загружены динамически -->
                    </tbody>
                </table>
            </div>
            <div>
                <h2>Заказы</h2>
                <table id="orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Дата</th>
                            <th>Пользователь</th>
                            <th>Общая сумма</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Данные будут загружены динамически -->
                    </tbody>
                </table>
            </div>

            <div class="add-product-form">
</div>
        </div>
    </main>

    <script src="admin.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Загрузка данных о продажах
        fetch('get_sales_data.php')
            .then(response => response.json())
            .then(data => {
                const salesDataBody = document.querySelector('#sales-data tbody');
                salesDataBody.innerHTML = '';
                data.products.forEach(item => {
                    const row = `
                        <tr>
                            <td>${item.product_id}</td>
                            <td>${item.product_name}</td>
                            <td>${item.total_sold}</td>
                            <td>${item.total_revenue} руб.</td>
                        </tr>
                    `;
                    salesDataBody.innerHTML += row;
                });
                document.getElementById('total-revenue').textContent = `Общая выручка: ${data.total_revenue} руб.`;
            })
            .catch(error => console.error('Ошибка:', error));

        // Обработка отправки формы добавления товара
        document.getElementById('add-product-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('add_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Товар успешно добавлен');
                    this.reset(); // Очистка формы после успешного добавления
                } else {
                    alert('Ошибка при добавлении товара: ' + data.error);
                }
            })
            .catch(error => console.error('Ошибка:', error));
        });
    });
    </script>
</body>
</html>