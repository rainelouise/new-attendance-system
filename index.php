<?php
session_start();
require_once __DIR__ . "/core/User.php"; 

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = new User();
    $loggedInUser = $user->login($email, $password);

    if($loggedInUser) {
        $_SESSION['user_id'] = $loggedInUser['id'];
        $_SESSION['role'] = $loggedInUser['role'];
        $_SESSION['name'] = $loggedInUser['name'];

        if($loggedInUser['role'] === 'admin') {
            header("Location: admin/admin_dashboard.php"); 
            exit();
        } else {
            header("Location: student/student_dashboard.php"); 
            exit();
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance System Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-red-600 to-yellow-200 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl text-red-600 font-bold mb-6 text-center">Login</h2>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <input type="email" name="email" placeholder="Email" required 
                       class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>
            <div class="mb-6">
                <input type="password" name="password" placeholder="Password" required
                       class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>
            <button type="submit" name="login" 
                    class="w-full bg-red-600 text-white p-3 rounded hover:bg-red-700 transition">
                Login
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="student/register_student.php" class="text-red-600 hover:underline">Register as Student</a> | 
            <a href="admin/register_admin.php" class="text-red-600 hover:underline">Register as Admin</a>
        </div>
    </div>
</body>
</html>