<?php
session_start();
require_once __DIR__ . "/../core/Student.php";
require_once __DIR__ . "/../config/db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student = new Student();
$student_id = $_SESSION['user_id'];

if(isset($_POST['file_attendance'])) {
    $course_id = $_POST['course_id'];
    $year_level = $_POST['year_level']; // Get year level from form
    $student->fileAttendance($student_id, $course_id, $year_level);
    $msg = "Attendance filed.";
}

$attendance = $student->checkAttendanceHistory($student_id);

$conn = (new Database())->connect();
$courses = $conn->query("SELECT * FROM courses ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-red-700 text-white p-4 flex justify-between">
        <span class="font-bold">EAC Student Dashboard</span>
        <div>
            <a href="submit_excuse.php" class="mr-4 hover:underline">Submit Excuse</a>
            <a href="../logout.php" class="hover:underline">Logout</a>
        </div>
    </nav>

    <div class="container mx-auto mt-6 p-4">
        <?php if(isset($msg)): ?>
            <div class="bg-green-500 text-white p-3 rounded mb-4"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <div class="bg-white shadow rounded p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">File Attendance</h2>
            <form method="POST" class="flex gap-4 items-center">
                <select name="course_id" required class="p-2 border rounded flex-1">
                    <option value="">Select Course</option>
                    <?php foreach($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (Y<?= $c['year_level'] ?>)</option>
                    <?php endforeach; ?>
                </select>

                <select name="year_level" required class="p-2 border rounded flex-1">
                    <option value="">Select Year Level</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>

                <button type="submit" name="file_attendance" class="bg-red-600 text-white px-4 py-2 rounded">Submit</button>
            </form>
        </div>

        <div class="bg-white shadow rounded p-6">
            <h2 class="text-xl font-bold mb-4">Attendance History</h2>
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">Date</th>
                        <th class="border px-4 py-2">Time</th>
                        <th class="border px-4 py-2">Course</th>
                        <th class="border px-4 py-2">Year Level</th>
                        <th class="border px-4 py-2">Status</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($attendance)): ?>
                        <tr><td class="p-4" colspan="5">No attendance records yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach($attendance as $att): ?>
                        <tr>
                            <td class="border px-4 py-2"><?= htmlspecialchars($att['date']) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($att['time']) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($att['course_name']) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($att['year_level']) ?></td>
                            <td class="border px-4 py-2">
                                <?php if($att['status'] === 'late'): ?>
                                    <span class="text-yellow-700 font-semibold">Late</span>
                                <?php else: ?>
                                    <?= ucfirst($att['status']) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="mt-4">
                <a href="view_excuse_status.php" class="text-blue-600 hover:underline">View my excuse letters</a>
            </div>
        </div>
    </div>
</body>
</html>