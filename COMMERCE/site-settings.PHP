<?php
session_start();
// Check if the user is logged in as admin
if (!isset($_SESSION["email"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
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
    <title>Site Settings</title>
    <link rel="stylesheet" href="styles.css">  
    <script src="https://cdn.tailwindcss.com"></script>
    </head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-6 text-center">Site Settings</h1>
       <header class="mb-10 flex justify-end">
    <a href="logout.php" 
       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
        Logout 
    </a>
</header>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Manage Site Settings</h2>
            <p>This is where you can manage site-wide settings and configurations.</p>
            <!-- Add site settings functionalities here -->
             <div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Update Site Settings</h2>
    <form action="update-settings.php" method="POST" class="space-y-4">
        <div>
            <label for="site_name" class="block text-gray-700">Site Name:</label>
            <input type="text" id="site_name" name="site_name" class="w-full border border-gray-300 p-2 rounded" required>
        </div>
        <div>
            <label for="admin_email" class="block text-gray-700">Admin Email:</label>
            <input type="email" id="admin_email" name="admin_email" class="w-full border border-gray-300 p-2 rounded" required>
        </div>
        <div>
            <label for="maintenance_mode" class="block text-gray-700">Maintenance Mode:</label>
            <select id="maintenance_mode" name="maintenance_mode" class="w-full border border-gray-300 p-2 rounded" required>
                <option value="off">Off</option>
                <option value="on">On</option>
            </select>
        </div>
    </form>
        </div>
    </div>
</body>
</html>