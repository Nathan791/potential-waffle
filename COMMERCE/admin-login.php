<?php
session_start();
// Check if the admin is already logged in. If yes, redirect them to the admin dashboard.
if(isset($_SESSION["admin_email"])){
    header("Location: /COMMERCE/admin-dashboard.php");
    exit();
}
// Initialize variables for form data and potential error messages
$admin_email = "";
$password = "";
$hashed_password = "";
$errormessage = "";
$fetched_admin_id = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $admin_email = trim($_POST["admin_email"] ?? "");
    $password = $_POST["password"] ?? ""; // Password is not trimmed for accurate verification

    if(empty($admin_email) || empty($password)){
        $errormessage = "Admin email and password are required.";
    } else {
        // Database connection details
        $db_servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $db_database = "commerce";

        // Create connection
        $connection = new mysqli($db_servername, $db_username, $db_password, $db_database);

        // Verify the connection
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // Prepare the query to find the admin by email
        $stmt = $connection->prepare("SELECT id, password FROM shop WHERE email = ?");
        
        // Bind the parameter (s for string)
        $stmt->bind_param("s", $admin_email);

        // Execute the query
        $stmt->execute();

        // Bind the result variables (MUST match the 2 columns selected: id, password)
        $stmt->bind_result($fetched_admin_id, $hashed_password);

        // Fetch the result (if an admin with that email exists)
        if ($stmt->fetch()) {
            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Password is correct, start a new session
                $_SESSION["admin_email"] = $admin_email;
                $_SESSION["admin_id"] = $fetched_admin_id;

                // Redirect to admin dashboard
                header("Location: /COMMERCE/admin-dashboard.php");
                exit();
            } else {
                // Password is incorrect
                $errormessage = "Invalid admin email or password.";
            }
        } else {
            // No admin found with that email
            $errormessage = "Invalid admin email or password.";
        }

        // Close statement and connection
        $stmt->close();
        $connection->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-6 text-center">Admin Login</h1>
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <?php
            if (!empty($errormessage)) {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . htmlspecialchars($errormessage) . '</div>';
            }
            ?>
            <form method="POST" action="admin-login.php" class="space-y-4">
                <div>
                    <label for="admin_email" class="block text-gray-700 font-semibold mb-2">Admin Email:</label>
                    <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($admin_email); ?>" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password:</label>
                    <input type="password" id="password" name="password" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300">Login</button>
                </div>
            </form>
        </div>
    </div>