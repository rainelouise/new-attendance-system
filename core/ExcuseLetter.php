<?php
require_once __DIR__ . "/../config/db.php";

class ExcuseLetter {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Student: submit new excuse letter
    public function submit($user_id, $course_id, $reason, $file_path = null) {
        $stmt = $this->conn->prepare("
            INSERT INTO excuse_letters (user_id, course_id, reason, file_path)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$user_id, $course_id, $reason, $file_path]);
    }

    // Student: fetch all excuse letters by this student
    public function getByStudent($user_id) {
        $stmt = $this->conn->prepare("
            SELECT e.*, c.name as course_name, c.year_level
            FROM excuse_letters e
            JOIN courses c ON e.course_id = c.id
            WHERE e.user_id = ?
            ORDER BY e.submitted_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Admin: fetch all excuse letters
    public function getAll() {
        $stmt = $this->conn->query("
            SELECT e.*, u.name as student_name, u.email, c.name as course_name, c.year_level
            FROM excuse_letters e
            JOIN users u ON e.user_id = u.id
            JOIN courses c ON e.course_id = c.id
            ORDER BY e.submitted_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Admin: filter by program
    public function getByProgram($programName) {
        $stmt = $this->conn->prepare("
            SELECT e.*, u.name as student_name, u.email, c.name as course_name, c.year_level
            FROM excuse_letters e
            JOIN users u ON e.user_id = u.id
            JOIN courses c ON e.course_id = c.id
            WHERE c.name LIKE ?
            ORDER BY e.submitted_at DESC
        ");
        $stmt->execute(["%$programName%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Admin: approve or reject
    public function updateStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE excuse_letters SET status=? WHERE id=?");
        return $stmt->execute([$status, $id]);
    }
}