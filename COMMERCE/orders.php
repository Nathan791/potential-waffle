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
//Fetch orders from database
$stmt = $connection->prepare("SELECT id, user_id, total_amount, status, created_at FROM orders ORDER BY created_at DESC");


$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <h2>Orders</h2>
    <p>Views yours orders and their status</p>
    <table class="table-auto w-full bg-white shadow-md rounded-lg mt-6">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php
$stmt->close();
$connection->close();
?>
