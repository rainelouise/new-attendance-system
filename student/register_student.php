<?php
session_start();
require_once __DIR__ . "/../core/User.php";

$user = new User();
$message = '';
$error = '';

if(isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $course_id = $_POST['course_id'] ?: null;
    $year_level = $_POST['year_level'] ?: null;

    $extra = [];
    if ($course_id) $extra['course_id'] = $course_id;
    if ($year_level) $extra['year_level'] = $year_level;

    $success = $user->register($name, $email, $password, 'student', $extra);

    if($success) {
        $_SESSION['success'] = "Registration successful! You can now log in.";
        header("Location: ../index.php");
        exit();
    } else {
        $error = "Registration failed. Email may already exist.";
    }
}

// fetch courses for dropdown
require_once __DIR__ . "/../config/db.php";
$db = new Database();
$conn = $db->connect();
$courses = $conn->query("SELECT * FROM courses ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Register as Student</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-red-600 to-yellow-200 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl text-red-600 font-bold mb-6 text-center">Register as Student</h2>

        <?php if($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="text" name="name" placeholder="Full Name" required class="w-full p-3 border rounded">
            <input type="email" name="email" placeholder="Email" required class="w-full p-3 border rounded">
            <input type="password" name="password" placeholder="Password" required class="w-full p-3 border rounded">

            <label class="block">Course / Program</label>
            <select name="course_id" class="w-full p-2 border rounded">
                <option value="">-- Select course --</option>
                <?php foreach($courses as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label class="block">Year Level</label>
            <select name="year_level" class="w-full p-2 border rounded">
                <option value="">-- Select year --</option>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
            </select>

            <button type="submit" name="register" class="w-full bg-red-600 text-white p-3 rounded">Register</button>
        </form>

        <div class="mt-4 text-center">
            <a href="../index.php" class="text-red-600 hover:underline">Back to Login</a>
        </div>
    </div>
</body>
</html>