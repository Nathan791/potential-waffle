<?php
session_start();
//check if user is logged in
if(!isset($_SESSION["email"])){
    header("Location: /COMMERCE/login.php");
    exit();
}
//Initialisation variable
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
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css">  
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-6 text-center">User Dashboard</h1>
       <header class="mb-10 flex justify-end">
    <a href="logout.php" 
       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
        Logout
    </a>
    <a href="admin-dashboard.php"
        class="ml-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
        Admin Dashboard
    </a>
     <label class="theme-switch">
            <input type="checkbox" id="theme-toggle">
            <span class="slider"></span>
        </label>
</header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">Profile</h2>
                <p class="mb-4">View and edit your profile information.</p>
                <a href="profile.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to Profile</a>
            </div>
            <!-- Orders Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">Orders</h2>
                <p class="mb-4">View your order history and status.</p>
                <a href="orders.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to Orders</a>
            </div>
            <!-- Settings Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">Settings</h2>
                <p class="mb-4">Manage your account settings and preferences.</p>
                <a href="settings.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to Settings</a>
            </div>
            <!-- Support Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">Support</h2>
                <p class="mb-4">Get help and support for your account.</p>
                <a href="support.html" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to Support</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">Content-Moderation</h2>
                <p class="mb-4">Review and manage user-generated content.</p>
                <a href="content-moderation.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to Content Moderation</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">System Logs</h2>
                <p class="mb-4">View system activity and logs.</p>
                <a href="system-logs.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to System Logs</a>
            </div>
        </div>
        <div>
            <h2 class="text-2xl font-semibold mb-4 mt-10 text-center">Buy Your Product</h2>
            <a href="shop.php" class="inline-block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors mx-auto block">Go to Shop</a>
        </div>
        <div>
            <h2 class="text-2xl font-semibold mb-4 mt-10 text-center">Notifications</h2>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <ul class="space-y-4">
                    <?php
                    // Fetch notifications from database
                    $query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
                    $stmt = $connection->prepare($query);
                    $stmt->bind_param("i", $_SESSION["user_id"]);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<li class='p-4 border-b hover:bg-gray-50 transition-colors'>";
                            echo "<strong>" . htmlspecialchars($row['title']) . "</strong><br>";
                            echo "<span class='text-gray-600'>" . htmlspecialchars($row['message']) . "</span>";
                            echo "<form method='POST' action='read_notifications.php' class='mt-2'>";
                            echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>";
                            echo "<button type='submit' class='text-blue-500 hover:underline'>Mark as Read</button>";
                            echo "</form>";
                            echo "</li>";
                        }
                    } else {
                        echo "<li class='p-4 text-gray-600'>No notifications found.</li>";
                    }
                    ?>
                </ul>
        </div>
        <div class="mt-10 text-center">
            <canvas id="activityChart" class="mx-auto" width="400" height="200"></canvas>
        </div>
        <script>
        //verify if user is admin to show admin dashboard link
        <?php if($_SESSION['role'] !== 'admin'): ?>
            document.querySelector('a[href="admin-dashboard.php"]').style.display = 'none';
        <?php endif; ?>

            const ctx = document.getElementById('activityChart').getContext('2d');
            const activityChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [{
                        label: 'User Activity',
                        data: [12, 19, 3, 5, 2, 3, 7],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        <div class="mt-10 text-center text-gray-600">
            &copy; <?php echo date("Y"); ?> Commerce Platform. All rights reserved.
    </div>
    </div>
    <div class="fixed bottom-4 right-4">
        <a href="help.php" class="bg-green-500 text-white px-4 py-2 rounded-full shadow-lg hover:bg-green-600 transition-colors">
            Help   
        </a>
    </div>
    <script>
        // Theme toggle script
        const themeToggle = document.getElementById('theme-toggle');
        themeToggle.addEventListener('change', function() {
            if (this.checked) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
        </script>
</body>
</html>
