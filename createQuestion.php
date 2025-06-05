<?php
require_once 'db.php';
require_once 'User.php';
require_once 'Question.php';

session_start();

$user = new User($pdo);
$Question = new Question($pdo);


if (!isset($_SESSION['username'])) {
    header('Location login.php');

}

$profile = $user->searchUser($_SESSION['username']);

// Получаем все темы
$themes = $Question->getAllTheme();


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $question = $_POST['question'];
    $id_theme = $_POST['id_theme'];

    $id_user = $profile['id'];


    if($Question->createQuestion($question, $id_theme, $id_user)){

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
        if ($user->isAdmin($profile['role'])) {
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
    <h2>Создать вопрос</h2>
    <input type="text" name="question" placeholder="вопрос" required>
    <select name="id_theme" id="topic" required>
        <option value="">Выберите тему</option>
        <?php foreach ($themes as $theme): ?>
            <option value="<?php echo htmlspecialchars($theme['id']); ?>">
                <?php echo htmlspecialchars($theme['text']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="create">Создать</button>
</form>
</body>
</html>
