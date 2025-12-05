<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION["email"])) {
    header("Location: /COMMERCE/login.php");
    exit();
}

// Allow only admins
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: /COMMERCE/user-dashboard.php");
    exit();
}

// Enable mysqli exception mode
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $connection = new mysqli("localhost", "root", "", "commerce");
    $connection->set_charset("utf8mb4");

    // Correct and secure SQL query
    $stmt = $connection->prepare("
        SELECT
            o.id,
            u.name AS customer_name,
            p.name AS product_name,
            o.quantity,
            o.status
        FROM orders o
        JOIN users u ON o.user_id = u.id        -- FIXED JOIN HERE
        JOIN products p ON o.product_id = p.id
        ORDER BY o.id DESC
    ");

    $stmt->execute();
    $result = $stmt->get_result();

} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Order Management</title>
   <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<div class="container mx-auto p-6">

    <!-- HEADER -->
    <header class="flex justify-between items-center mb-8">
    <div class="flex items-center gap-3">
        <!-- BACK ICON -->
        <i id="goBackBtn" class='bx bx-arrow-back text-3xl cursor-pointer hover:text-blue-600 transition'></i>

        <h1 class="text-4xl font-bold">Order Management</h1>
    </div>

    <a href="logout.php"
       class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">
        Logout
    </a>
</header>


    <!-- CARD -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Manage Orders</h2>
        <p class="text-gray-600 mb-4">View and manage all customer orders.</p>

        <!-- TABLE -->
        <table class="min-w-full bg-white border rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-3 px-4 border">Order ID</th>
                    <th class="py-3 px-4 border">Customer Name</th>
                    <th class="py-3 px-4 border">Product</th>
                    <th class="py-3 px-4 border">Quantity</th>
                    <th class="py-3 px-4 border">Status</th>
                    <th class="py-3 px-4 border">Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-2 px-4 border text-center"><?= htmlspecialchars($row['id']) ?></td>
                        <td class="py-2 px-4 border"><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td class="py-2 px-4 border"><?= htmlspecialchars($row['product_name']) ?></td>
                        <td class="py-2 px-4 border text-center"><?= htmlspecialchars($row['quantity']) ?></td>
                        <td class="py-2 px-4 border text-center"><?= htmlspecialchars($row['status']) ?></td>

                        <td class="py-2 px-4 border text-center">
                            <a href="edit-order.php?id=<?= urlencode($row['id']) ?>"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>

            <?php else: ?>
                <tr>
                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                        No orders found.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
<script>
    // Back button icon event
    document.getElementById('goBackBtn').addEventListener('click', function() {
        window.history.back();
    });
</script>

</body>
</html>
