<?php
require_once __DIR__ . "/User.php";
require_once __DIR__ . "/../config/db.php";

class Student extends User {

    public function __construct() {
        parent::__construct();
    }

    // File attendance
    public function fileAttendance($student_id, $course_id, $year_level) {
        $status = $this->checkIfLate() ? 'late' : 'present';
        $stmt = $this->conn->prepare("
        INSERT INTO attendance (student_id, course_id, date, time, status, year_level)
        VALUES (:student_id, :course_id, CURDATE(), CURTIME(), :status, :year_level)
        ");
        return $stmt->execute([
            ':student_id' => $student_id,
            ':course_id' => $course_id,
            ':year_level' => $year_level,
            ':status' => $status
        ]);
    }

    // Get attendance history
    public function checkAttendanceHistory($student_id) {
        $stmt = $this->conn->prepare("
            SELECT a.date, a.time, a.status, c.name AS course_name, a.year_level
            FROM attendance a
            JOIN courses c ON a.course_id = c.id
            WHERE a.student_id = :student_id
            ORDER BY a.date DESC, a.time DESC
        ");
        $stmt->execute([':student_id' => $student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Submit excuse letter
public function submitExcuseLetter($student_id, $reason, $file_path, $course_id) {
    $stmt = $this->conn->prepare("
        INSERT INTO excuse_letters (student_id, course_id, reason, file_path, status, submitted_at)
        VALUES (:student_id, :course_id, :reason, :file_path, 'pending', NOW())
    ");
    return $stmt->execute([
        ':student_id' => $student_id,
        ':course_id' => $course_id,
        ':reason' => $reason,
        ':file_path' => $file_path
    ]);
}

    // Get student excuse letters
public function getStudentExcuseLetters($student_id) {
    $stmt = $this->conn->prepare("
        SELECT e.id, e.course_id, e.reason, e.file_path, e.status, e.submitted_at,
               c.name AS course_name
        FROM excuse_letters e
        JOIN courses c ON e.course_id = c.id
        WHERE e.student_id = :student_id
        ORDER BY e.submitted_at DESC
    ");
    $stmt->execute([':student_id' => $student_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Get all courses
    public function getCourses() {
        $stmt = $this->conn->query("SELECT * FROM courses ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Helper to check lateness
    private function checkIfLate() {
        date_default_timezone_set('Asia/Manila');
        $currentHour = date('H');
        $currentMinute = date('i');
        return ($currentHour > 8 || ($currentHour == 8 && $currentMinute > 0));
    }
}