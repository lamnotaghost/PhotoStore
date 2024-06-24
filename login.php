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

// Обработка данных формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // SQL запрос для получения данных пользователя
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['email'] = $row['email'];
        
        // Проверка наличия поля is_admin
        if (isset($row['is_admin'])) {
            $_SESSION['is_admin'] = ($row['is_admin'] == 1);
            if ($_SESSION['is_admin']) {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
        } else {
            // Если поля is_admin нет, проверяем email администратора
            if ($email === 'admin@example.com') { // Замените на реальный email администратора
                $_SESSION['is_admin'] = true;
                header("Location: admin.php");
            } else {
                $_SESSION['is_admin'] = false;
                header("Location: index.php");
            }
        }
        exit;
    } else {
        $login_error = "Неверный email или пароль.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="login.php">Вход</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="login-form">
            <h1>Вход</h1>
            <?php if (isset($login_error)): ?>
                <p class="error-message"><?php echo $login_error; ?></p>
            <?php endif; ?>
            <form method="post" action="login.php">
                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Войти</button>
            </form>
        </div>
    </main>
</body>
</html>