<?php
session_start();
require_once __DIR__ . "/../core/Student.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student = new Student();
$message = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = $_POST['reason'];
    $course_id = $_POST['course_id'];
    $year_level = $_POST['year_level']; 

    $file_path = null;
    if(!empty($_FILES['file']['name'])) {
        $targetDir = __DIR__ . "/uploads/";
        if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['file']['name']);
        $targetFile = $targetDir . $fileName;

        if(move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            $file_path = "uploads/" . $fileName; 
        }
    }

    // Correct session key
    $student_id = $_SESSION['user_id']; 

    // Call the method with student_id and year_level
    $student->submitExcuseLetter(
        $student_id, 
        $reason, 
        $file_path, 
        $course_id, 
    );

    $message = "Your excuse letter has been submitted!";
}

$courses = $student->getCourses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Excuse Letter</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-red-700 text-white p-4 flex justify-between">
        <span class="font-bold">EAC Student Dashboard</span>
        <a href="../logout.php" class="hover:underline">Logout</a>
    </nav>

    <div class="container mx-auto mt-6 p-4">
        <div class="bg-white shadow rounded p-6 max-w-lg mx-auto">
            <h2 class="text-xl font-bold mb-4">Submit Excuse Letter</h2>

            <?php if($message): ?>
                <p class="mb-4 text-green-600"><?= $message ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block mb-1 font-medium">Reason</label>
                    <textarea name="reason" required class="w-full border rounded p-2"></textarea>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Course</label>
                    <select name="course_id" required class="w-full border rounded p-2">
                        <option value="">Select course</option>
                        <?php foreach($courses as $c): ?>
                            <option value="<?= $c['id'] ?>">
                                <?= htmlspecialchars($c['name']) ?> (Year <?= $c['year_level'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                <div>
                    <label class="block mb-1 font-medium">Year Level</label>
                    <select name="year_level" required class="w-full border rounded p-2">
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Upload File (optional)</label>
                    <input type="file" name="file" class="w-full border rounded p-2">
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>