<?php
session_start();
require_once __DIR__ . "/../core/Admin.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$admin = new Admin();
$message = '';

// add course
if(isset($_POST['add_course'])) {
    $name = trim($_POST['name']);
    $year_level = isset($_POST['year_level']) && $_POST['year_level'] !== '' ? (int)$_POST['year_level'] : null;
    if($name) {
        $admin->addCourse($name, $year_level);
        $message = "Course added.";
    }
}

// delete course
if(isset($_POST['delete_course'])) {
    $course_id = (int)$_POST['course_id'];
    $admin->deleteCourse($course_id);
    $message = "Course deleted.";
}

$courses = $admin->getAllCourses();
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-red-700 text-white p-4 flex justify-between">
        <span class="font-bold">EAC Admin Dashboard</span>
        <a href="admin_dashboard.php" class="hover:underline">Back</a>
    </nav>

    <div class="container mx-auto mt-6 p-4">
        <?php if($message): ?>
            <div class="bg-green-500 text-white p-3 rounded mb-4"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="bg-white shadow rounded p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Add New Course</h2>
            <form method="POST" class="flex gap-2 items-center">
                <input type="text" name="name" placeholder="Course name (e.g. BS in Computer Science)" required class="p-2 border rounded flex-1">
                <input type="number" name="year_level" placeholder="Year" class="p-2 border rounded w-32">
                <button type="submit" name="add_course" class="bg-red-600 text-white px-4 py-2 rounded">Add</button>
            </form>
        </div>

        <div class="bg-white shadow rounded p-6">
            <h2 class="text-xl font-bold mb-4">Existing Courses</h2>
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Course</th>
                        <th class="border px-4 py-2">Year Level</th>
                        <th class="border px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($courses as $c): ?>
                        <tr>
                            <td class="border px-4 py-2"><?= $c['id'] ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($c['name']) ?></td>
                            <td class="border px-4 py-2"><?= $c['year_level'] ?: '-' ?></td>
                            <td class="border px-4 py-2">
                                <form method="POST" onsubmit="return confirm('Delete this course?');">
                                    <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
                                    <button type="submit" name="delete_course" class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>