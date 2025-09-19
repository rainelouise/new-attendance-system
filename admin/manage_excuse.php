<?php
session_start();
require_once __DIR__ . "/../core/Admin.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$admin = new Admin();

// Handle approve/reject action
if(isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $admin->updateExcuseStatus($id, $status);
    header("Location: manage_excuse.php");
    exit();
}

// Handle program filter
$filter_course_id = $_GET['course_id'] ?? null;

// Get all courses for dropdown
$courses = $admin->getAllCourses();

// Get letters (filtered if course_id is selected)
$letters = $admin->getAllExcuseLetters($filter_course_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Excuse Letters</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<nav class="bg-red-700 text-white p-4 flex justify-between">
    <span class="font-bold">EAC Admin Dashboard</span>
    <a href="../logout.php" class="hover:underline">Logout</a>
</nav>

<div class="container mx-auto mt-6 p-4">
    <div class="bg-white shadow rounded p-6 mb-4">
        <h2 class="text-xl font-bold mb-4">Filter by Program</h2>
        <form method="GET" class="flex gap-4 items-center">
            <select name="course_id" class="border p-2 rounded">
                <option value="">All Programs</option>
                <?php foreach($courses as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($filter_course_id == $c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?> (Year <?= $c['year_level'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Filter</button>
        </form>
    </div>

    <div class="bg-white shadow rounded p-6">
        <h2 class="text-xl font-bold mb-4">All Excuse Letters</h2>
        <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">Student</th>
                    <th class="border px-4 py-2">Course</th>
                    <th class="border px-4 py-2">Reason</th>
                    <th class="border px-4 py-2">File</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($letters as $l): ?>
                    <tr>
                        <td class="border px-4 py-2"><?= htmlspecialchars($l['student_name']) ?></td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($l['course_name']) ?> (Y<?= $l['year_level'] ?>)</td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($l['reason']) ?></td>
                        <td class="border px-4 py-2">
                            <?php if($l['file_path']): ?>
                                <a href="../student/uploads/<?= htmlspecialchars($l['file_path']) ?>" target="_blank" class="text-blue-600 hover:underline">View</a>
                            <?php else: ?>
                                No File
                            <?php endif; ?>
                        </td>
                        <td class="border px-4 py-2"><?= ucfirst($l['status']) ?></td>
                        <td class="border px-4 py-2">
                            <?php if($l['status'] === 'pending'): ?>
                                <form method="POST" class="flex gap-2">
                                    <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                    <input type="hidden" name="status" value="">
                                    <button type="submit" name="update_status" class="bg-green-600 text-white px-2 py-1 rounded"
                                            onclick="this.form.status.value='approved'">Approve</button>
                                    <button type="submit" name="update_status" class="bg-red-600 text-white px-2 py-1 rounded"
                                            onclick="this.form.status.value='rejected'">Reject</button>
                                </form>
                            <?php else: ?>
                                <?= ucfirst($l['status']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>