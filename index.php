<?php
require_once 'db.php';
require_once 'User.php';

$User = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    if ($User->register($username, $password, $firstName, $lastName)) {
        echo "Регистрация прошла успешно!";
        header('Location: login.php');
    } else {
        echo "Ошибка регистрации!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация и авторизация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="home.php">Главная</a>
    <a href="login.php">Войти</a>
    <a href="index.php">Зарегестрироваться</a>
</header>

<main>

<form method="POST" class="login-form">
    <h2>Регистрация</h2>
    <input type="text" name="username" placeholder="Имя пользователя" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <input type="text" name="firstName" placeholder="Имя" required>
    <input type="text" name="lastName" placeholder="Фамилия" required>
    <button type="submit" name="register">Зарегистрироваться</button>
</form>

<p>уже зарегестрированы?<a href="login.php">Войти</a></p>
</main>
</body>
</html>
