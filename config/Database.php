<?php
/**
 * Класс для подключения к базе данных
 */
class Database {
    private $host = '127.0.0.1';
    private $port = '3307';
    private $db_name = 'api_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    /**
     * Получить подключение к базе данных
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            error_log("Ошибка подключения к БД: " . $e->getMessage());
        }

        return $this->conn;
    }
}
?>

