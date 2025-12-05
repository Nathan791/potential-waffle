<?php
session_start();

// Check if user is admin
if (!isset($_SESSION["email"]) || $_SESSION["role"] !== "admin") {
    header("Location: /COMMERCE/login.php");
    exit();
}

// Database connection
$connection = new mysqli("localhost", "root", "", "commerce");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = $_POST["price"];
    $stock = $_POST["stock"];

    // --------------------------
    //  IMAGE HANDLING (UPLOAD OR URL)
    // --------------------------

    $imagePath = "";

    // Case 1: Upload local image
    if (!empty($_FILES["image_upload"]["name"])) {

        $targetDir = "../uploads/products/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "-" . basename($_FILES["image_upload"]["name"]);
        $targetFile = $targetDir . $fileName;

        // Allowed formats
        $allowedTypes = ["image/jpeg", "image/png", "image/webp"];
        if (!in_array($_FILES["image_upload"]["type"], $allowedTypes)) {
            die("Invalid image type. Only JPG, PNG, WEBP allowed.");
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES["image_upload"]["tmp_name"], $targetFile)) {
            $imagePath = "/COMMERCE/uploads/products/" . $fileName;
        } else {
            die("Image upload failed.");
        }

    } 
    // Case 2: External URL
    else if (!empty($_POST["image_url"])) {
        $imagePath = trim($_POST["image_url"]);
    } 
    else {
        die("You must upload an image or provide an image URL.");
    }

    // Insert product
    $stmt = $connection->prepare("INSERT INTO products (name, image, description, price, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdi", $name, $imagePath, $description, $price, $stock);

    if ($stmt->execute()) {
        header("Location: /COMMERCE/product-management.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
<<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Create a Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6">Create a New Product</h1>
    <header>
        <div class="flex items-center gap-3">
        <!-- BACK ICON -->
        <i id="goBackBtn" class='bx bx-arrow-back text-3xl cursor-pointer hover:text-blue-600 transition'></i>
        </div>

    </header>

    <?php
    // Display success message if set
    if (isset($_SESSION['add_success'])) {
        echo '<div class="bg-green-100 text-green-800 p-4 rounded mb-4">' . $_SESSION['add_success'] . '</div>';
        unset($_SESSION['add_success']);
    }
    ?>
    <?php
    // Display error message if set
    if (isset($_SESSION['add_error'])) {
        echo '<div class="bg-red-100 text-red-800 p-4 rounded mb-4">' . $_SESSION['add_error'] . '</div>';
        unset($_SESSION['add_error']);
    }
    ?>
    <?php
    
    ?>
   <form action="create_product.php" method="POST" enctype="multipart/form-data" class="space-y-4">

    <div>
        <label for="name" class="block text-gray-700">Product Name:</label>
        <input type="text" id="name" name="name" required class="w-full px-3 py-2 border rounded">
    </div>

    <div>
        <label class="block text-gray-700">Upload Image:</label>
        <input type="file" name="image_upload" accept="image/*" class="w-full px-3 py-2 border rounded">
    </div>

    <div>
        <label for="image_url" class="block text-gray-700">OR Image URL:</label>
        <input type="text" id="image_url" name="image_url" class="w-full px-3 py-2 border rounded" placeholder="https://example.com/image.jpg">
    </div>

    <div>
        <label for="description" class="block text-gray-700">Description:</label>
        <textarea id="description" name="description" required class="w-full px-3 py-2 border rounded"></textarea>
    </div>

    <div>
        <label for="price" class="block text-gray-700">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required class="w-full px-3 py-2 border rounded">
    </div>

    <div>
        <label for="stock" class="block text-gray-700">Stock Quantity:</label>
        <input type="number" id="stock" name="stock" required class="w-full px-3 py-2 border rounded">
    </div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
        Create Product
    </button>
</form>
</body>
</html>
