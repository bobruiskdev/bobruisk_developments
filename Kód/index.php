<?php
session_start();

// Проверка, залогинен ли пользователь
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Перенаправление на страницу входа, если пользователь не вошел
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Подключение к базе данных и запрос на получение имени пользователя
$servername = "localhost";
$db_username = "pryhazha";
$db_password = "Tis*7291911";
$dbname = "pryhazha";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

$sql = "SELECT username FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
}

$conn->close();
?>


<nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-between">
        <p class="navbar-text mr-3">Здравствуйте, <?php echo $username; ?>!</p>
        <a href="logout.php" class="btn btn-secondary">Разлогиниться</a>
    </nav>