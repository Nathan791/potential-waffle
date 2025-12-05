<?php
session_start();

// ---------------------------------------------------------
// CLEAR CART IF REQUESTED
// ---------------------------------------------------------
if (isset($_GET['clear_cart']) && $_GET['clear_cart'] == 1) {
    $_SESSION['cart'] = [];
    header("Location: cart.php"); // Change le nom si nécessaire
    exit();
}

//---------------------------------------------------------
// 1. CHECK LOGIN (Admin or User)
//---------------------------------------------------------
if (!isset($_SESSION["email"])) {
    header("Location: /COMMERCE/login.php");
    exit();
}

//---------------------------------------------------------
// 2. DATABASE CONNECTION
//---------------------------------------------------------
$db_servername = "localhost";
$db_username = "root";
$db_password = "";
$db_database = "commerce";

$connection = new mysqli($db_servername, $db_username, $db_password, $db_database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

//---------------------------------------------------------
// 3. ADMIN: FETCH ALL PRODUCTS
//---------------------------------------------------------
$stmt = $connection->prepare("SELECT id, name, price, stock, image FROM products ORDER BY id DESC");

$stmt->execute();

if(!$stmt){
    die("Query failed: " . $connection->error);
}

$result = $stmt->get_result();

//---------------------------------------------------------
// 4. USER CART LOGIC
//---------------------------------------------------------

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        1 => 2,
        3 => 1
    ];
}

$cart_total = 0;
$cart_items_data = [];

$product_ids_in_cart = array_keys($_SESSION['cart']);

if (!empty($product_ids_in_cart)) {

    $placeholders = implode(',', array_fill(0, count($product_ids_in_cart), '?'));
    $types = str_repeat('i', count($product_ids_in_cart));

    $stmt = $connection->prepare("SELECT id, name, price FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$product_ids_in_cart);
    $stmt->execute();
    $cart_result = $stmt->get_result();

    while ($row = $cart_result->fetch_assoc()) {

        $product_id = (int)$row['id'];
        $quantity   = $_SESSION['cart'][$product_id];
        $price      = (float)$row['price'];
        $subtotal   = $price * $quantity;
        $cart_total += $subtotal;

        $cart_items_data[] = [
            'name'     => htmlspecialchars($row['name']),
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Product Management & Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">

        <h1 class="text-4xl font-bold mb-6 text-center text-gray-800">
            🛠️ Product Management & User Cart
        </h1>

        <div class="flex items-center gap-3">
        <!-- BACK ICON -->
        <i id="goBackBtn" class='bx bx-arrow-back text-3xl cursor-pointer hover:text-blue-600 transition'></i>
        </div>
        <!-- Navigation -->
        <header class="mb-10 flex justify-end space-x-4">
            <a href="admin-dashboard.php" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Admin Dashboard</a>
            <a href="create_product.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Create Product</a>
            <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</a>
        </header>

        <!-- PRODUCT TABLE -->
        <div class="bg-white p-6 rounded-lg shadow-xl mb-10">
            <h2 class="text-2xl font-semibold mb-4 border-b pb-2 text-indigo-600">Product Editor (Admin View)</h2>

            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">ID</th>
                        <th class="py-2 px-4 border-b">Product Name</th>
                        <th class="py-2 px-4 border-b">Image</th>
                        <th class="py-2 px-4 border-b">Price</th>
                        <th class="py-2 px-4 border-b">Stock</th>
                        <th class="py-2 px-4 border-b text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>

                            <tr>
                                <td class="py-4 px-4 border-b"><?= $row['id'] ?></td>

                                <td class="py-4 px-4 border-b font-medium">
                                    <?= htmlspecialchars($row['name']) ?>
                                </td>

                                <td class="py-4 px-4 border-b">
                                    <img src="<?= htmlspecialchars($row['image']) ?>" class="w-16 h-16 rounded object-cover">
                                </td>

                                <td class="py-4 px-4 border-b">$<?= number_format($row['price'], 2) ?></td>
                                <td class="py-4 px-4 border-b"><?= htmlspecialchars($row['stock']) ?></td>

                                <td class="py-4 px-4 border-b text-center space-x-4">
                                    <a href="Update_product.php?id=<?= $row['id'] ?>" class="bg-blue-600 text-white rounded hover:rounded-blue-800 font-semibold">Edit</a>
                                    
                                    <a href="delete_product.php?id=<?= $row['id'] ?>"
                                       class="bg-red-600 text-white rounded hover:bg-red-800 font-semibold"
                                       onclick="return confirm('Are you sure you want to delete product ID <?= $row['id'] ?>?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- CART SUMMARY -->
        <div class="bg-white p-6 rounded-lg shadow-xl">
            <h2 class="text-2xl font-semibold mb-4 border-b pb-2 text-green-600">Current Cart Summary (User View)</h2>

            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Product</th>
                        <th class="py-2 px-4 border-b text-center">Qty</th>
                        <th class="py-2 px-4 border-b text-right">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($cart_items_data)): ?>
                        <?php foreach ($cart_items_data as $item): ?>

                            <tr>
                                <td class="py-4 px-4 border-b"><?= $item['name'] ?></td>
                                <td class="py-4 px-4 border-b text-center"><?= $item['quantity'] ?></td>
                                <td class="py-4 px-4 border-b text-right">$<?= number_format($item['subtotal'], 2) ?></td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">Your cart is empty.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="flex justify-end mt-6">
                <div class="text-lg font-bold">
                    <p class="mb-2">
                        Cart Total:
                        <span class="text-green-600 ml-4">$<?= number_format($cart_total, 2) ?></span>
                    </p>

                    <a href="cart.php"
                       class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition block text-center">
                        Go to Cart / Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Back button icon event
    document.getElementById('goBackBtn').addEventListener('click', function() {
        window.history.back();
    });
    </script>
</body>
</html>
