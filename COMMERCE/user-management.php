<?php
session_start();

// Redirect if user not logged in
if (!isset($_SESSION["email"])) {
    header("Location: /COMMERCE/login.php");
    exit();
}

// Restrict access to admins only
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: /COMMERCE/user-dashboard.php");
    exit();
}

// Database connection (safe mode)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $connection = new mysqli("localhost", "root", "", "commerce");
    $connection->set_charset("utf8mb4");

    // Fetch users
    $stmt = $connection->prepare("SELECT id, name, email, role FROM users ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="container mx-auto p-6">

    <!-- HEADER -->
    <header class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold">User Management</h1>

        <a href="logout.php"
           class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">
            Logout
        </a>
    </header>

    <!-- USER TABLE CARD -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Manage Users</h2>
        <p class="mb-4 text-gray-600">Here you can manage user accounts, roles, and permissions.</p>

        <table class="min-w-full bg-white border">
            <thead class="bg-gray-200">
            <tr>
                <th class="py-2 px-4 border">User ID</th>
                <th class="py-2 px-4 border">Name</th>
                <th class="py-2 px-4 border">Email</th>
                <th class="py-2 px-4 border">Role</th>
                <th class="py-2 px-4 border">Actions</th>
            </tr>
            </thead>

            <tbody>
            <?php 
            
            if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border text-center"><?= htmlspecialchars($row['id']) ?></td>
                        <td class="py-2 px-4 border"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="py-2 px-4 border"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="py-2 px-4 border text-center"><?= htmlspecialchars($row['role']) ?></td>

                        <td class="py-2 px-4 border text-center">
                            <a href="edit-user.php?id=<?= urlencode($row['id']) ?>"
                               class="bg-blue-600 text-white rounded hover:underline">Edit</a>
                            |
                            <a href="delete-user.php?id=<?= urlencode($row['id']) ?>"
                               onclick="return confirm('Are you sure?')"
                               class="bg-red-600 text-white rounded hover:underline">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                        No users found.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
