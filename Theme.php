<?php
class Theme
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function createTheme($text) {
        $stmt = $this->db->prepare("INSERT INTO themes (text) VALUES (:text)");
        $stmt->bindParam(':text', $text);

        return $stmt->execute();
    }

}
