<?php
require_once 'db.php';
require_once 'User.php';

$method = new User($pdo);

session_start();

if(isset($_SESSION['username'])){
    $user = $method->searchUser($_SESSION['username']);
}


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Домашняя</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header style="display: flex">
    <a href="home.php">Главная</a>
    <?php if(is_array($user) && isset($user)){
        if($method->isAdmin($user['role'])){
            echo '<a href="createTheme.php">Создание темы админом </a>';
        }
        echo '<a href="ViewQuestion.php">Посмотреть вопросы</a>';
        echo '<a href="createQuestion.php">Задать вопрос</a>';
        echo '<a href="logout.php">Выйти</a>';
        echo '<p>' . $user['username'] . ' ' . $user['firstName'] .' '. $user['lastName'] . '</p>';
    }else{
        echo '<a href="login.php">Войти</a>';
        echo '<a href="index.php">Зарегестрироваться</a>';

    }


    ?>
</header>

<main>

    <form method="post">

    </form>
    <?php
    if (is_array($user) && isset($user['username'])) {

        echo '<p style="font-size: 20px">'. $user['username'] . '</p>';
        echo '<p style="font-size: 20px">'. $user['firstName'] . '</p>';
        echo '<p style="font-size: 20px">'. $user['lastName'] . '</p>';

    } else {
        echo '<p style="font-size: 20px">Гость</p>';
    }


    ?>
</main>
</body>
</html>
