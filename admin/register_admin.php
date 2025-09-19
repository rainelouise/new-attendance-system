<?php
session_start();
require_once __DIR__ . "/../core/User.php"; 

if(isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = new User();
    $success = $user->register($name, $email, $password, 'admin');

    if($success) {
        $_SESSION['success'] = "Admin registration successful! You can now log in.";
        header("Location: ../index.php"); 
        exit();
    } else {
        $error = "Registration failed. Email may already exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register as Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-red-600 to-yellow-200 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl text-red-600 font-bold mb-6 text-center">Register as Admin</h2>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <input type="text" name="name" placeholder="Full Name" required
                       class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>
            <div class="mb-4">
                <input type="email" name="email" placeholder="Email" required
                       class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>
            <div class="mb-6">
                <input type="password" name="password" placeholder="Password" required
                       class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>
            <button type="submit" name="register"
                    class="w-full bg-red-600 text-white p-3 rounded hover:bg-red-700 transition">
                Register
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="../index.php" class="text-red-600 hover:underline">Back to Login</a>
        </div>
    </div>
</body>
</html>