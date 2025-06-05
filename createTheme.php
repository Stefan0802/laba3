<?php
require_once 'db.php';
require_once 'User.php';
require_once 'Question.php';
require_once 'Theme.php';

session_start();

$User = new User($pdo);
$Question = new Question($pdo);
$Theme = new Theme($pdo);

if (!isset($_SESSION['username'])) {
    header('Location login.php');
}

$profile = $User->searchUser($_SESSION['username']);

if(!$User->isAdmin($profile['role'])){
    header('Location: home.php');
}


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $textTheme = $_POST['text'];

    if($Theme->createTheme($textTheme)){

    };
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создать вопрос</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="home.php">Главная</a>
    <?php if (is_array($profile) && isset($profile)) {
        if ($User->isAdmin($profile['role'])) {
            echo '<a href="createTheme.php">Создание темы админа</a>';
        }
        echo '<a href="ViewQuestion.php">Посмотреть вопросы</a>';
        echo '<a href="createQuestion.php">Задать вопрос</a>';
        echo '<a href="logout.php">Выйти</a>';
    } else {
        echo '<a href="login.php">Войти</a>';
        echo '<a href="index.php">Зарегистрироваться</a>';
    } ?>
</header>

<form method="POST" class="login-form">
    <h2>Создать тему</h2>
    <input type="text" name="text" placeholder="тема" required>
    <button type="submit" name="create">Создать</button>
</form>
</body>
</html>
