<?php
require_once 'db.php';
require_once 'User.php';

session_start();

$user = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $loggedInUser  = $user->login($username, $password);
        if ($loggedInUser ) {
            $_SESSION['username'] = $username;
            header('location: home.php');

        } else {
            echo "Неверный логин или пароль.";
        }

}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="home.php">Главная</a>
    <?php if(is_array($user) && isset($user)){
        if($method->isAdmin($user['role'])){
            echo '<a href="">Админка </a>';
        }
        echo '<a href="logout.php">Выйти</a>';
    }else{
        echo '<a href="login.php">Войти</a>';
        echo '<a href="index.php">Зарегестрироваться</a>';

    }


    ?>
</header>

<form method="POST" class="login-form">
    <h2>Авторизация</h2>
    <input type="text" name="username" placeholder="Имя пользователя" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit" name="login">Войти</button>
</form>
<p>Нет аккаунта?<a href="index.php">зарегестрироваться</a></p>
</body>
</html>
