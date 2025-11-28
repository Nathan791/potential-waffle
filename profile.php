<?php
session_start();
//check if user is logged on
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
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css">  
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-6 text-center">User Profile</h1>
       <header class="mb-10 flex justify-end">
    <a href="logout.php" 
       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
        Logout
    </a>
</header>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Profile Information</h2>
            <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION["name"]) ?></p>
            <p><strong>Phone Number:</strong> <?= htmlspecialchars($_SESSION["pnumber"]) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION["email"]) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($_SESSION["role"]) ?></p>
        </div>
    </div>
</body>
</html>