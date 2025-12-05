<?php
session_start();

// Check if the user is logged in
if(!isset($_SESSION["email"])){
    header("Location: /COMMERCE/login.php");
    exit();
}

// Allow only admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: /COMMERCE/user-dashboard.php");
    exit();
}

// Database connection
$connection = new mysqli("localhost", "root", "", "commerce");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if ID is provided
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    die("Invalid user ID.");
}

$user_id = intval($_GET["id"]);

// Fetch user data securely
$stmt = $connection->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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

            <form action="edit-user.php" method="POST" class="space-y-4">
                
                <input type="hidden" name="id" value="<?= htmlspecialchars($user["id"]) ?>">

                <div>
                    <label for="name" class="block text-gray-700">Name:</label>
                    <input type="text" id="name" name="name"
                           class="w-full border border-gray-300 p-2 rounded"
                           value="<?= htmlspecialchars($user["name"]) ?>" required>
                </div>

                <div>
                    <label for="email" class="block text-gray-700">Email:</label>
                    <input type="email" id="email" name="email"
                           class="w-full border border-gray-300 p-2 rounded"
                           value="<?= htmlspecialchars($user["email"]) ?>" required>
                </div>

                <div>
                    <label for="password" class="block text-gray-700">New Password (optional):</label>
                    <input type="password" id="password" name="password"
                           class="w-full border border-gray-300 p-2 rounded"
                           placeholder="Leave blank to keep current password">
                </div>

                <div>
                    <label for="role" class="block text-gray-700">Role:</label>
                    <select id="role" name="role"
                            class="w-full border border-gray-300 p-2 rounded" required>
                        <option value="user" <?= $user["role"] === "user" ? "selected" : "" ?>>User</option>
                        <option value="admin" <?= $user["role"] === "admin" ? "selected" : "" ?>>Admin</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                        Update User
                    </button>

                    <button type="reset"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
                        Reset
                    </button>

                    <button type="button" onclick="window.location.href='user-management.php'"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                        Cancel
                    </button>
                </div>

            </form>

        </div>
    </div>
</body>
</html>
