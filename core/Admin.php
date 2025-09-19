<?php
require_once __DIR__ . "/User.php";
require_once __DIR__ . "/../config/db.php"; 

class Admin extends User {

    public function __construct() {
        parent::__construct();
    }

    // Add a new course
    public function addCourse($name, $year_level) {
        $stmt = $this->conn->prepare("INSERT INTO courses (name, year_level) VALUES (?, ?)");
        return $stmt->execute([$name, $year_level]);
    }

    // Delete a course
    public function deleteCourse($id) {
        $stmt = $this->conn->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Get all courses 
    public function getAllCourses() {
        $stmt = $this->conn->query("SELECT * FROM courses ORDER BY name ASC, year_level ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // View attendance by course and year
    public function getAttendanceByCourseYear($course_id, $year_level) {
        $stmt = $this->conn->prepare("
            SELECT a.date, a.time, u.name AS student_name, c.name AS course_name, u.year_level, a.status
            FROM attendance a
            JOIN users u ON a.user_id = u.id
            JOIN courses c ON a.course_id = c.id
            WHERE c.id = ? AND u.year_level = ?
            ORDER BY a.date DESC, a.time DESC
        ");
        $stmt->execute([$course_id, $year_level]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Approve or reject excuse letters
    public function updateExcuseStatus($excuse_id, $status) {
        $stmt = $this->conn->prepare("UPDATE excuse_letters SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $excuse_id]);
    }

    // Get all excuse letters (pending/approved/rejected)
public function getAllExcuseLetters($course_id = null) {
    $sql = "
        SELECT e.id, e.student_id, e.course_id, e.reason, e.file_path, e.status, e.submitted_at,
               u.name AS student_name,
               c.name AS course_name, c.year_level
        FROM excuse_letters e
        JOIN users u ON e.student_id = u.id
        JOIN courses c ON e.course_id = c.id
    ";

    if($course_id) {
        $sql .= " WHERE e.course_id = :course_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':course_id' => $course_id]);
    } else {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}