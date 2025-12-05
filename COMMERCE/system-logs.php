<?php
session_start();
// Check if the user is logged in as admin
if (!isset($_SESSION["email"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Connection database
$db_servername = "localhost";
$db_username = "root";
$db_password = "";
$db_database = "commerce";
$connection = new mysqli($db_servername, $db_username, $db_password, $db_database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Fetch system logs
$logs = [];
$sql = "SELECT * FROM system_logs ORDER BY timestamp DESC";
$result = $connection->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}

$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs</title>
    <link rel="stylesheet" href="styles.css">  
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        
        <h1 class="text-4xl font-bold mb-6 text-center">System Logs</h1>

        <header class="mb-10 flex justify-end">
            <a href="logout.php" 
               class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                Logout
            </a>
        </header>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Recent System Logs</h2>

            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Timestamp</th>
                        <th class="py-2 px-4 border-b">User Email</th>
                        <th class="py-2 px-4 border-b">Action</th>
                        <th class="py-2 px-4 border-b">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($log['timestamp']) ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($log['user_email']) ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($log['action']) ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($log['details']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</body>
</html>
