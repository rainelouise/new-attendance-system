<?php
require_once __DIR__ . "/../config/db.php";

class User {
    protected $conn;
    protected $table = 'users';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    /**
     * Register a user.
     * $extra is an associative array for optional fields like course_id, year_level.
     */
    public function register($name, $email, $password, $role, $extra = []) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Build base query and params
        $fields = ['name','email','password','role'];
        $placeholders = ['?','?','?','?'];
        $params = [$name, $email, $hash, $role];

        // optional course_id, year_level
        if (isset($extra['course_id'])) {
            $fields[] = 'course_id';
            $placeholders[] = '?';
            $params[] = $extra['course_id'];
        }
        if (isset($extra['year_level'])) {
            $fields[] = 'year_level';
            $placeholders[] = '?';
            $params[] = $extra['year_level'];
        }

        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);
        try {
            return $stmt->execute($params);
        } catch (PDOException $e) {
            // optionally log $e->getMessage()
            return false;
        }
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}