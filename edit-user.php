<?php
session_start();
//check  if the user is logged on
if(!isset($_SESSION["email"])){
    header("Location: /COMMERCE/login.php");
    exit();
}
//connection database
$db_servername = "localhost";
$db_username = "root";
$db_password = "";
$db_database = "commerce";
$connection = new mysqli($db_servername, $db_username, $db_password, $db_database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css">  
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-6 text-center">Edit User</h1>
       <header class="mb-10 flex justify-end">
    <a href="logout.php" 
       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
        Logout
    </a>
</header>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Edit User Information</h2>
            <p>This is where you can edit user account information.</p>
            <!-- Add user editing functionalities here -->
             <div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Edit User</h2>
    <form action="update-user.php" method="POST" class="space-y-4">
        <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id']) ?>">
        <div>
            <label for="name" class="block text-gray-700">Name:</label>
            <input type="text" id="name" name="name" class="w-full border border-gray-300 p-2 rounded" required>
        </div>
        <div>
            <label for="email" class="block text-gray-700">Email:</label>
            <input type="email" id="email" name="email" class="w-full border border-gray-300 p-2 rounded" required>
        </div>
        <div>
            <label for="password" class="block text-gray-700">Password:</label>
            <input type="password" id="password" name="password" class="w-full border border-gray-300 p-2 rounded" required>
        </div>
        <div>
            <label for="role" class="block text-gray-700">Role:</label>
            <select id="role" name="role" class="w-full border border-gray-300 p-2 rounded" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Update User</button>
            <button type="reset" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">Reset</button>
            <button onclick="window.location.href='user-management.php'" type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">Cancel</button>
        </div>
    </form>
</div>
        </div>
    </div>
</body>
</html>           