<?php
// Removed: session_start() and login check - A cart view is typically public or customer-facing, 
// and the admin login check is unnecessary here.

// Database connection
$db_servername = "localhost";
$db_username = "root";
$db_password = "";
$db_database = "commerce";

$connection = new mysqli($db_servername, $db_username, $db_password, $db_database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Removed: Product insertion logic as it's not part of a cart view.

// --- CART LOGIC SIMULATION ---
// In a real application, you would fetch items from a $_SESSION['cart'] array.
// For this example, we'll fetch all products and display them as if they are in the cart
// with a simulated quantity of 1.
$cart_total = 0; // Initialize total
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-6 text-center text-blue-700">🛒 Your Shopping Cart</h1>

        <header class="mb-10 flex justify-end">
            <a href="index.php" 
               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                Continue Shopping
            </a>
        </header>

        <div class="bg-white p-6 rounded-lg shadow-xl">

            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">Items</h2>

            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b text-left">Product</th>
                        <th class="py-2 px-4 border-b">Price</th>
                        <th class="py-2 px-4 border-b">Quantity</th>
                        <th class="py-2 px-4 border-b">Subtotal</th>
                        <th class="py-2 px-4 border-b"></th> </tr>
                </thead>

                <tbody>
                    <?php
                    // Fetch all products from the database (simulating all items being in the cart)
                    $result = $connection->query("SELECT * FROM products ORDER BY id DESC");
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // --- Cart Specific Calculations ---
                            $quantity = 1; // Simulated quantity for display
                            $price = (float)$row['price'];
                            $subtotal = $price * $quantity;
                            $cart_total += $subtotal;
                            
                            echo "<tr>";
                            // Product Name (and description/image if desired)
                            echo "<td class='py-4 px-4 border-b font-medium'>".$row['name']."</td>";
                            // Price
                            echo "<td class='py-4 px-4 border-b text-center'>$".number_format($price, 2)."</td>";
                            // Quantity (Input for real cart, static for this demo)
                            echo "<td class='py-4 px-4 border-b text-center'>
                                    <input type='number' value='1' min='1' class='w-16 p-1 border rounded text-center' disabled>
                                  </td>";
                            // Subtotal
                            echo "<td class='py-4 px-4 border-b text-right font-bold'>$".number_format($subtotal, 2)."</td>";
                            // Action: Remove
                            echo "<td class='py-4 px-4 border-b text-center'>
                                    <a href='remove-from-cart.php?id=".$row['id']."' 
                                       class='text-red-500 hover:text-red-700 font-semibold' 
                                       onclick='return confirm(\"Remove this item?\")'>Remove</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='py-4 px-4 border-b text-center text-gray-500'>Your cart is empty.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <div class="flex justify-end mt-6">
                <div class="text-lg font-bold">
                    <p class="mb-2">Cart Total: <span class="text-green-600 ml-4">$<?php echo number_format($cart_total, 2); ?></span></p>
                    <button class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition w-full">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
            
        </div>
        
    </div>
</body>
</html>