<?php
session_start();

// Check if the user is logged in
if(!isset($_SESSION["email"])) {
    header("Location: /COMMERCE/login.php");
    exit();
}

// Database connection
$db_servername = "localhost";
$db_username = "root";
$db_password = "";
$db_database = "commerce";

$connection = new mysqli($db_servername, $db_username, $db_password, $db_database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

function addNotification($connection, $user_id, $message) {
    $stmt = $connection->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $message);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Notifications</title>
   <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">

        <!-- HEADER -->
        <header class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Notifications Admin</h1>

            <div class="flex items-center space-x-6">

                <!-- Notification Bell -->
                <div id="notifBell" class="relative cursor-pointer text-3xl">
                    🔔
                    <span id="notifCount" 
                          class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2"></span>
                </div>

                <a href="logout.php" 
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                    Logout
                </a>
            </div>
        </header>

        <!-- Notification Popup -->
        <div id="notifBox" 
             class="hidden bg-white p-4 rounded-lg shadow-md border w-80 absolute right-10 top-20 z-10">
        </div>

        <!-- TABLE -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Toutes les notifications</h2>

            <table class="min-w-full bg-white border">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 border">ID</th>
                        <th class="py-2 px-4 border">User ID</th>
                        <th class="py-2 px-4 border">Message</th>
                        <th class="py-2 px-4 border">Date</th>
                        <th class="py-2 px-4 border">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    // Ensure column name matches your DB (created_at)
                    $result = $connection->query("SELECT id, user_id, message, created_at FROM notifications ORDER BY id DESC");

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td class='py-2 px-4 border'>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td class='py-2 px-4 border'>" . htmlspecialchars($row['user_id']) . "</td>";
                            echo "<td class='py-2 px-4 border'>" . htmlspecialchars($row['message']) . "</td>";
                            echo "<td class='py-2 px-4 border'>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo"<a href='delete.php?id=".$row['id']."' class='text-red-500 hover:underline onclick='return confirm(\"Delete this product?\")'>Delete</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='py-4 text-center border'>Aucune notification.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JAVASCRIPT NOTIFICATIONS -->
    <script>
        function loadNotifications() {
            fetch("get_notifications.php")
            .then(res => res.json())
            .then(data => {
                let box = document.getElementById("notifBox");
                let count = document.getElementById("notifCount");

                box.innerHTML = "";
                count.textContent = data.length > 0 ? data.length : "";

                data.forEach(n => {
                    box.innerHTML += `
                        <div class="p-3 border-b">
                            <strong>${n.message}</strong><br>
                            <small>${n.created_at}</small>
                            <button onclick="readNotification(${n.id})"
                                    class="mt-2 px-2 py-1 bg-blue-500 text-white rounded">
                                Marquer comme lu
                            </button>
                        </div>
                    `;
                });

                if (data.length === 0) {
                    box.innerHTML = "<p class='text-gray-500'>Aucune nouvelle notification.</p>";
                }
            });
        }

        function readNotification(id) {
            fetch("read_notification.php", {
                method: "POST",
                body: new URLSearchParams({ id: id })
            }).then(() => loadNotifications());
        }

        document.getElementById("notifBell").addEventListener("click", function(){
            let box = document.getElementById("notifBox");
            box.classList.toggle("hidden");
        });

        // Auto refresh
        setInterval(loadNotifications, 5000);

        // First load
        loadNotifications();
    </script>

</body>
</html>
