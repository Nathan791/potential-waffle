<?php
session_start();
//check if user is logged in
if(!isset($_SESSION["email"])){
    header("Location: /COMMERCE/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="styles.css">  
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-6 text-center">User Management</h1>
       <header class="mb-10 flex justify-end">
    <a href="logout.php" 
       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
        Logout
    </a>
</header>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Manage Users</h2>
            <p>This is where you can manage user accounts, roles, and permissions.</p>
            <!-- Add user management functionalities here -->
             <table class="min-w-full bg-white">
    <thead>
        <tr>
            <th class="py-2 px-4 border-b">User ID</th>
            <th class="py-2 px-4 border-b">Name</th>
            <th class="py-2 px-4 border-b">Email</th>
            <th class="py-2 px-4 border-b">Role</th>
            <th class="py-2 px-4 border-b">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Database connection
        $db_servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $db_database = "commerce";
        $connection = new mysqli($db_servername, $db_username, $db_password, $db_database);
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }
        // Fetch users from database
        $result = $connection->query("SELECT id, name, email, role FROM shop");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['role']) . "</td>";
                echo "<td class='py-2 px-4 border-b'>
                        <a href='edit-user.php?id=" . urlencode($row['id']) . "' class='text-blue-500 hover:underline'>Edit</a> | 
                        <a href='delete-user.php?id=" . urlencode($row['id']) . "' class='text-red-500 hover:underline' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='py-2 px-4 border-b text-center'>No users found.</td></tr>";
        }
        $connection->close();
        ?>
        </table>
        </div>
    </div>
</body>