<?php
require_once 'db.php';
require_once 'User.php';
require_once 'Question.php';

session_start();

$user = new User($pdo);
$questionObj = new Question($pdo);

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$profile = $user->searchUser($_SESSION['username']);
$notificationCount = $questionObj->alert($profile['id']);

// Получаем все вопросы
$questions = $questionObj->getAllQuestions();
$themes = $questionObj->getAllTheme();

// Обработка отправки ответа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Добавление или обновление ответа
    if (isset($_POST['answer'], $_POST['question_id'])) {
        $question_id = (int)$_POST['question_id'];
        $answer = trim($_POST['answer']);
        if ($questionObj->addAnswer($question_id, $answer)) {
            // При добавлении ответа сбросим статус оценки
            $questionObj->updateAnswerStatus($question_id, null);
            header('Location: ViewQuestion.php');
            exit();
        } else {
            $error = "Ошибка при добавлении ответа";
        }
    }

    // Обновление статуса ответа (принят/отклонён)
    elseif (isset($_POST['accept_answer'], $_POST['question_id'])) {
        $question_id = (int)$_POST['question_id'];
        $isAccepted = ($_POST['accept_answer'] === '1') ? 1 : 2;

        // Проверяем, что текущий пользователь — автор вопроса
        $questionForCheck = null;
        foreach ($questions as $q) {
            if ($q['id'] == $question_id) {
                $questionForCheck = $q;
                break;
            }
        }

        if ($questionForCheck && $profile['id'] === $questionForCheck['id_user']) {
            if ($isAccepted == 1) {
                // Если ответ принят, просто обновляем статус
                if ($questionObj->updateAnswerStatus($question_id, $isAccepted)) {
                    header('Location: ViewQuestion.php');
                    exit();
                } else {
                    $error = "Ошибка при обновлении статуса ответа";
                }
            } else {
                // Если ответ отклонён, сбрасываем ответ и статус
                if ($questionObj->updateAnswerStatus($question_id, $isAccepted)) {
                    header('Location: ViewQuestion.php');
                    exit();
                } else {
                    $error = "Ошибка при сбросе ответа";
                }
            }
        } else {
            $error = "У вас нет прав для изменения статуса ответа этого вопроса";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Все вопросы</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .success { color: green; }
        .error { color: red; }
        .accepted { color: green; font-weight: bold; }
        .rejected { color: red; font-weight: bold; }
        .pending { color: gray; font-style: italic; }
        header a {
            margin-right: 10px;
        }
    </style>
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

<h2 style="text-align: center; font-size: 30px">Все вопросы</h2>
<?php
if ($notificationCount > 0): ?>
    <p class="notification" style="background-color: blue; padding: 5px 10px; border-radius: 5px; margin: 10px 0; color: yellow; font-size: 20px; font-family: Amiri">
        У вас <?php if($notificationCount > 1 && $notificationCount < 5){
            echo $notificationCount. ' уведомления';
        }elseif ($notificationCount == 1){
            echo $notificationCount. ' уведомление';
        }else{
            echo $notificationCount. ' уведомлений';
        }?>
    </p>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <p class="success">
        <?php
        if ($_GET['success'] == 1) echo "Ответ успешно добавлен!";
        elseif ($_GET['success'] == 2) echo "Статус ответа обновлён!";
        elseif ($_GET['success'] == 3) echo "Ответ отклонён и сброшен!";
        ?>
    </p>
<?php endif; ?>

<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if (!empty($questions)): ?>
    <ol>
        <?php foreach ($questions as $question): ?>
            <li>
                <strong style="margin: 10px; font-size: 18px; "><?php echo htmlspecialchars($question['question']); ?></strong><br>
                Тема: <?php
                $themeName = $questionObj->getTheme($question['id_theme']);
                echo htmlspecialchars($themeName);
                ?><br>
                <strong style="margin: 10px;">Автор:</strong> <?php echo htmlspecialchars($user->searchUserId($question['id_user'])); ?>
                <? echo htmlspecialchars($question['answer']) ?>
                <?php if (empty($question['answer'])): ?>
                    <?php if ($user->isAdmin($profile['role'])): ?>
                        <form method="POST" action="ViewQuestion.php">
                            <input type="hidden" name="question_id" value="<?php echo (int)$question['id']; ?>">
                            <textarea name="answer" placeholder="Ваш ответ" required></textarea><br>
                            <button type="submit">Ответить</button>
                        </form>
                    <?php else: ?>
                        <span>Ответа нет</span>
                    <?php endif; ?>
                <?php else: ?>
                    <div>
                        <strong>Ответ:</strong> <?= htmlspecialchars($question['answer']) ?><br>
                        <strong>Статус ответа:</strong>
                        <?php
                        if ($question['status'] === null) {
                            echo '<span class="pending">Не оценён</span>';
                        } elseif ($question['status'] == 1) {
                            echo '<span class="accepted">Принят</span>';
                        } else {
                            echo '<span class="rejected">Отклонён</span>';
                        }
                        ?>

                        <?php if ($profile['id'] === $question['id_user'] && $question['status'] !== 1): ?>
                            <form method="POST" action="ViewQuestion.php" style="margin-top:5px;">
                                <input type="hidden" name="question_id" value="<?php echo (int)$question['id']; ?>">
                                <?php if ($question['status'] === 0): ?>
                                    <button type="submit" name="accept_answer" value="1" class="true">Принять</button>
                                <?php endif; ?>
                                <button type="submit" name="accept_answer" value="2" class="false">Отклонить</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </li>
            <hr>
        <?php endforeach; ?>
    </ol>
<?php else: ?>
    <p>Нет вопросов для отображения.</p>
<?php endif; ?>

</body>
</html>