<?php

class Auth {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }
    public function validateApiKey() {
        $apiKey = null;
        if (isset($_SERVER['HTTP_X_API_KEY'])) {
            $apiKey = $_SERVER['HTTP_X_API_KEY'];
        } elseif (isset($_SERVER['X_API_KEY'])) {
            $apiKey = $_SERVER['X_API_KEY'];
        }
        if (!$apiKey) {
            return false;
        }
        try {
            $query = "SELECT id, user_id, is_active FROM api_keys WHERE api_key = :api_key LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':api_key', $apiKey);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();

                if ($row['is_active'] == 1) {
                    return [
                        'id' => $row['id'],
                        'user_id' => $row['user_id'],
                        'is_active' => true
                    ];
                }
            }

            return false;
        } catch(PDOException $e) {
            error_log("Ошибка проверки API-ключа: " . $e->getMessage());
            return false;
        }
    }
    public function requireAuth() {
        $auth = $this->validateApiKey();
        
        if (!$auth) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Неавторизован. Требуется валидный API-ключ в заголовке X-API-Key.'
            ]);
            exit;
        }

        return $auth;
    }
}
?>

