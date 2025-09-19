<?php
session_start();
require_once __DIR__ . "/../core/Student.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student = new Student();
$letters = $student->getStudentExcuseLetters($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Excuse Letters</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-red-700 text-white p-4 flex justify-between">
        <span class="font-bold">EAC Student Dashboard</span>
        <a href="../logout.php" class="hover:underline">Logout</a>
    </nav>

    <div class="container mx-auto mt-6 p-4">
        <div class="bg-white shadow rounded p-6">
            <h2 class="text-xl font-bold mb-4">My Excuse Letters</h2>

            <?php if(empty($letters)): ?>
                <p class="text-gray-600">You haven’t submitted any excuse letters yet.</p>
            <?php else: ?>
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">Reason</th>
                        <th class="border px-4 py-2">Course</th>
                        <th class="border px-4 py-2">File</th>
                        <th class="border px-4 py-2">Status</th>
                        <th class="border px-4 py-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($letters as $l): ?>
                        <tr>
                            <td class="border px-4 py-2"><?= htmlspecialchars($l['reason']) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($l['course_name']) ?></td>
                            <td class="border px-4 py-2">
                                <?php if($l['file_path']): ?>
                                    <a href="uploads/<?= htmlspecialchars($l['file_path']) ?>" target="_blank" class="text-blue-600 hover:underline">View</a>
                                <?php else: ?>
                                    No File
                                <?php endif; ?>
                            </td>
                            <td class="border px-4 py-2">
                                <span class="
                                    px-2 py-1 rounded text-white 
                                    <?= $l['status'] === 'approved' ? 'bg-green-600' : ($l['status'] === 'rejected' ? 'bg-red-600' : 'bg-yellow-500') ?>
                                ">
                                    <?= ucfirst($l['status']) ?>
                                </span>
                            </td>
                            <td class="border px-4 py-2"><?= $l['submitted_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>