<?php
session_start();
//check if the user is logged in
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
    <title>Report Issue</title>
    <link rel="stylesheet" href="styles.css">  
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100"></body>
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-6 text-center">Report an Issue</h1>
       <header class="mb-10 flex justify-end">
    <a href="logout.php" 
       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
        Logout
    </a>
</header>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Submit a Report</h2>
            <form action="submit-report.php" method="POST" class="space-y-4">
                <div>
                    <label for="issue_type" class="block text-gray-700">Issue Type:</label>
                    <select id="issue_type" name="issue_type" class="w-full border border-gray-300 p-2 rounded" required>
                        <option value="">Select an issue type</option>
                        <option value="bug">Bug</option>
                        <option value="feature_request">Feature Request</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label for="description" class="block text-gray-700">Description:</label>
                    <textarea id="description" name="description" rows="5" class="w-full border border-gray-300 p-2 rounded" required></textarea>
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>