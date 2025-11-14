<?php
class CourseModel {
    private $conn;
    private $table_name = 'courses';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT id, title, instructor, duration_hours, price FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getById($id) {
        $query = "SELECT id, title, instructor, duration_hours, price FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }

        return false;
    }
    public function create($data) {
        if (empty($data['title']) || empty($data['instructor'])) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (title, instructor, duration_hours, price) 
                  VALUES (:title, :instructor, :duration_hours, :price)";

        $stmt = $this->conn->prepare($query);
        $title = htmlspecialchars(strip_tags($data['title']));
        $instructor = htmlspecialchars(strip_tags($data['instructor']));
        $duration_hours = isset($data['duration_hours']) ? (int)$data['duration_hours'] : 0;
        $price = isset($data['price']) ? (float)$data['price'] : 0.00;

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':instructor', $instructor);
        $stmt->bindParam(':duration_hours', $duration_hours, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);

        if ($stmt->execute()) {
            $data['id'] = $this->conn->lastInsertId();
            return $data;
        }

        return false;
    }

    public function update($id, $data) {
        $existing = $this->getById($id);
        if (!$existing) {
            return false;
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET title = :title, 
                      instructor = :instructor, 
                      duration_hours = :duration_hours, 
                      price = :price 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $title = htmlspecialchars(strip_tags($data['title'] ?? $existing['title']));
        $instructor = htmlspecialchars(strip_tags($data['instructor'] ?? $existing['instructor']));
        $duration_hours = isset($data['duration_hours']) ? (int)$data['duration_hours'] : (int)$existing['duration_hours'];
        $price = isset($data['price']) ? (float)$data['price'] : (float)$existing['price'];

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':instructor', $instructor);
        $stmt->bindParam(':duration_hours', $duration_hours, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);

        if ($stmt->execute()) {
            return $this->getById($id);
        }

        return false;
    }

    public function delete($id) {
        $existing = $this->getById($id);
        if (!$existing) {
            return false;
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>

