<?php
session_start();

// Check if user is logged in
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

// Check for successful 'Add to Cart' action and display a message (Optional)
$message = '';
if (isset($_SESSION['add_success'])) {
    $message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Success!</p>
                    <p>' . htmlspecialchars($_SESSION['add_success']) . ' has been added to your cart.</p>
                </div>';
    unset($_SESSION['add_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Browse Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
    </head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">

        <header class="mb-10 flex justify-between items-center">
            <h1 class="text-4xl font-extrabold text-gray-800">🛍️ Product Marketplace</h1>
            <div class="space-x-4 flex items-center">
                <a href="cart.php" 
                   class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition flex items-center">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                   </svg>
                   View Cart
                </a>
                <a href="logout.php" 
                   class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                    Logout 
                </a>
            </div>
        </header>

        <?php echo $message; ?>

        <div class="bg-white p-6 rounded-lg shadow-xl">
            <h2 class="text-3xl font-semibold mb-6 border-b pb-3">Available Products</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                
                <?php
                // Fetch products
                $query = "SELECT * FROM products ORDER BY id DESC";
                $result = $connection->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $product_id = htmlspecialchars($row['id']);
                        $name = htmlspecialchars($row['name']);
                        $description = htmlspecialchars($row['description'] ?? "No description available.");
                        $price = number_format($row['price'], 2);
                        $stock = htmlspecialchars($row['stock']);
                        // Assuming 'image' column holds a URL or path
                        $image_url = htmlspecialchars($row['image'] ?? "https://via.placeholder.com/400x300?text=Product+Image");
                        $is_in_stock = $stock > 0;
                        
                        echo '
                        <div class="bg-white border rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <img src="' . $image_url . '" alt="' . $name . '" class="w-full h-48 object-cover">
                            
                            <div class="p-5">
                                <h3 class="text-xl font-bold mb-2">' . $name . '</h3>
                                
                                <p class="text-gray-600 mb-3 text-sm">' . substr($description, 0, 70) . '...</p>
                                
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-2xl font-extrabold text-indigo-600">$ ' . $price . '</span>
                                    
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full ' . ($is_in_stock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . '">
                                        ' . ($is_in_stock ? 'In Stock (' . $stock . ')' : 'Sold Out') . '
                                    </span>
                                </div>
                                
                                <form action="cart.php" method="GET">
                                    <input type="hidden" name="add_id" value="' . $product_id . '">
                                    <button type="submit" 
                                            class="w-full px-4 py-2 text-white font-semibold rounded-lg transition ' . ($is_in_stock ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed') . '"
                                            ' . ($is_in_stock ? '' : 'disabled') . '>
                                        ' . ($is_in_stock ? 'Add to Cart' : 'Out of Stock') . '
                                    </button>
                                </form>
                            </div>
                        </div>
                        ';
                    }
                } else {
                    echo '
                    <div class="col-span-full text-center py-10">
                        <p class="text-xl text-gray-500">No products are currently listed in the shop.</p>
                    </div>
                    ';
                }
                ?>
            </div>
            
        </div>
    </div>
</body>
</html>