<?php
class Question
{
    private $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Получить все темы
    public function getAllTheme()
    {
        $stmt = $this->db->prepare("SELECT * FROM themes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Создать вопрос
    public function createQuestion($question, $id_theme, $id_user)
    {
        $stmt = $this->db->prepare("INSERT INTO questions (question, id_user, id_theme, status) VALUES (:question, :id_user, :id_theme, false)");
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id_theme', $id_theme);
        return $stmt->execute();
    }

    // Получить все вопросы с ответами и статусом
    public function getAllQuestions()
    {
        $stmt = $this->db->prepare("SELECT * FROM questions");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получить название темы по ID
    public function getTheme($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM themes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['text'] : null;
    }

    // Добавить/обновить ответ на вопрос
    public function addAnswer($question_id, $answer)
    {
        $stmt = $this->db->prepare("UPDATE questions SET answer = :answer WHERE id = :question_id");
        $stmt->bindParam(':answer', $answer);
        $stmt->bindParam(':question_id', $question_id);
        return $stmt->execute();
    }

    // Обновить статус ответа (принят/отклонён)
    public function updateAnswerStatus($questionId, $isAccepted)
    {

        if($isAccepted == 2){
            $stmt = $this->db->prepare("UPDATE questions SET status = NULL, answer = NULL WHERE id = :question_id");

            $stmt->bindParam(':question_id', $questionId);


            return $stmt->execute();

        }else{
            $stmt = $this->db->prepare("UPDATE questions SET status = :status WHERE id = :question_id");
            $stmt->bindParam(':status', $isAccepted, PDO::PARAM_INT);
            $stmt->bindParam(':question_id', $questionId);

            return $stmt->execute();
        }
        return null;
    }




    public function alert($userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM questions 
                               WHERE id_user = :user_id 
                               AND answer IS NOT NULL 
                               AND status = 0");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

}
