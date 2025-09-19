<?php
session_start();
require_once __DIR__ . "/../core/Admin.php";
require_once __DIR__ . "/../config/db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$admin = new Admin();
$db = new Database();
$conn = $db->connect();

// Get all courses for filter
$courses = $admin->getAllCourses();

// Get selected filters
$selected_course = $_GET['course_id'] ?? '';
$selected_year = $_GET['year_level'] ?? '';

$attendance = [];
if($selected_course) {
    $sql = "
        SELECT a.*, u.name AS student_name, c.name AS course_name
        FROM attendance a
        JOIN users u ON a.student_id = u.id
        JOIN courses c ON a.course_id = c.id
        WHERE a.course_id = ?
    ";

    if($selected_year !== '') {
        $sql .= " AND a.year_level = ?";
    }

    $sql .= " ORDER BY a.date DESC, a.time DESC";

    $stmt = $conn->prepare($sql);
    $params = [$selected_course];
    if($selected_year !== '') {
        $params[] = $selected_year;
    }

    $stmt->execute($params);
    $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>View Attendance by Program/Year</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-red-700 text-white p-4 flex justify-between">
        <span class="font-bold">View Attendance</span>
        <a href="admin_dashboard.php" class="hover:underline">Back</a>
    </nav>

    <div class="container mx-auto mt-6 p-4">
        <div class="bg-white shadow rounded p-6 mb-6">
            <form method="GET" class="flex gap-2 items-center">
                <select name="course_id" class="p-2 border rounded" required>
                    <option value="">Select course</option>
                    <?php foreach($courses as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $selected_course == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="year_level" class="p-2 border rounded">
                    <option value="">Year</option>
                    <option value="1" <?= $selected_year == '1' ? 'selected' : '' ?>>1</option>
                    <option value="2" <?= $selected_year == '2' ? 'selected' : '' ?>>2</option>
                    <option value="3" <?= $selected_year == '3' ? 'selected' : '' ?>>3</option>
                    <option value="4" <?= $selected_year == '4' ? 'selected' : '' ?>>4</option>
                </select>

                <button class="bg-red-600 text-white px-4 py-2 rounded">Filter</button>
            </form>
        </div>

        <div class="bg-white shadow rounded p-6">
            <h2 class="text-xl font-bold mb-4">Attendance Records</h2>

            <?php if(empty($attendance)): ?>
                <p class="text-gray-600">No records found for the selected filters.</p>
            <?php else: ?>
                <table class="min-w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">Student</th>
                            <th class="border px-4 py-2">Course</th>
                            <th class="border px-4 py-2">Year Level</th>
                            <th class="border px-4 py-2">Date</th>
                            <th class="border px-4 py-2">Time</th>
                            <th class="border px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($attendance as $a): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($a['student_name']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($a['course_name']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($a['year_level']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($a['date']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($a['time']) ?></td>
                                <td class="border px-4 py-2"><?= ucfirst($a['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>