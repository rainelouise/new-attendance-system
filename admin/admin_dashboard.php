<?php
session_start();
require_once __DIR__ . "/../core/Admin.php";
require_once __DIR__ . "/../config/db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$admin = new Admin();
$courses = $admin->getAllCourses();
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-red-700 text-white p-4 flex justify-between">
        <span class="font-bold">EAC Admin Dashboard</span>
        <a href="../logout.php" class="hover:underline">Logout</a>
    </nav>

    <div class="container mx-auto mt-6 p-4">
        <div class="bg-white shadow rounded p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
            <div class="flex gap-4">
                <a href="manage_courses.php" class="bg-red-600 text-white px-4 py-2 rounded">Manage Courses</a>
                <a href="view_by_program.php" class="bg-red-600 text-white px-4 py-2 rounded">View Attendance by Program</a>
                <a href="manage_excuse.php" class="bg-red-600 text-white px-4 py-2 rounded">Manage Excuse Letters</a>
            </div>
        </div>

        <!-- show stats or list of courses -->
        <div class="bg-white shadow rounded p-6">
            <h2 class="text-xl font-bold mb-4">Courses</h2>
            <ul>
                <?php foreach($courses as $c): ?>
                    <li class="py-1"><?= htmlspecialchars($c['name']) ?> (Y<?= $c['year_level'] ?: 'N/A' ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>