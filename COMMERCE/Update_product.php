<?php
session_start();

// Check if a user is logged in (Crucial for admin access)
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
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Edit The  Product</title>
   <script src="https://cdn.tailwindcss.com"></script>
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="bg-gray-100">
        <div class="container mx-auto p-4">
            <h1 class="text-4xl font-bold mb-6 text-center">Edit Product</h1>
        <header class="mb-10 flex justify-end">
    <a href="logout.php"
         class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
        Logout
     </a>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <form action="update_product.php" method="POST" class="space-y-4">
                <div>
                    <label for="title" class="block text-gray-700 font-semibold mb-2">Name:</label>
                    <input type="text" id="name" name="name" class="w-full p-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="image" class="block text-gray-700 font-semibold mb-2">Image URL:</label>
                    <input type="text" id="image" name="image" class="w-full p-2 border border-gray-300 rounded" required>
                </div>
                <div>
                     <label for="description" class="block text-gray-700 font-semibold mb-2">Description:</label>
                    <textarea id="description" name="description" class="w-full p-2 border border-gray-300 rounded" rows="4" required></textarea>
                </div>
                <div>
                    <label for="price" class="block text-gray-700 font-semibold mb-2">Price:</label>
                    <input type="number" step="0.01" id="price" name="price" class="w-full p-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="stock" class="block text-gray-700 font-semibold mb-2">Stock Quantity:</label>
                    <input type="number" id="stock" name="stock" class="w-full p-2 border border-gray-300 rounded" required>                   
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Update Product</button>
            </form>
        </div>
    </div>
</body>
</html>