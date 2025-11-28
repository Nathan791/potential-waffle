<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Order-Management</title>
   <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        
        <h1 class="text-4xl font-bold mb-6 text-center">Order Management</h1>

        <header class="mb-10 flex justify-end">
            <a href="logout.php" 
               class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                Logout 
            </a>
        </header>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Manage Orders</h2>
            <p>This is where you can view and manage customer orders.</p>

            <table class="min-w-full bg-white mt-6">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Order ID</th>
                        <th class="py-2 px-4 border-b">Customer Name</th>
                        <th class="py-2 px-4 border-b">Product</th>
                        <th class="py-2 px-4 border-b">Quantity</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    // Fetch orders from database
                    $query = "
                        SELECT o.id, s.name AS customer_name, p.name AS product_name, o.quantity, o.status 
                        FROM orders o
                        JOIN shop s ON o.customer_id = s.id
                        JOIN products p ON o.product_id = p.id
                    ";
                    $result = $connection->query($query);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['customer_name']) . "</td>";
                            echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['product_name']) . "</td>";
                            echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td class='py-2 px-4 border-b'>
                                    <a href='edit-order.php?id=" . urlencode($row['id']) . "' 
                                       class='text-blue-500 hover:underline'>
                                       Edit
                                    </a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='py-2 px-4 border-b text-center'>No orders found.</td></tr>";
                    }

                    $connection->close();
                    ?>
                </tbody>
            </table>

        </div>

    </div>
</body>
</html>
