<?php

session_start();
// Check if the user is already logged in. If yes, redirect them to the home page.
if(isset($_SESSION["email"])){
    header("Location: /COMMERCE/index.html");
    exit();
}

// Initialize variables for form data and potential error messages
$name = "";
$email = "";
$password = "";
$errormessage = "";

// Variables to hold fetched data from the database
$fetched_id = null;
$fetched_name = null;
$fetched_pnumber = null;
$fetched_email = null;
$hashed_password = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? ""; // Password is not trimmed for accurate verification

    if(empty($email) || empty($password)){
        $errormessage = "Email and password are required.";
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
            // Using die() is acceptable for catastrophic errors like connection failure
            die("Connection failed: " . $connection->connect_error);
        }

        // Prepare the query to find the user by email (standard login practice)
        // Select all required fields, including the hashed password
        $stmt = $connection->prepare("SELECT id, name, pnumber, email, password FROM shop WHERE email = ?");
        
        // Bind the parameter (s for string)
        $stmt->bind_param("s", $email);

        // Execute the query
        $stmt->execute();

        // Bind the result variables (MUST match the 5 columns selected: id, name, pnumber, email, password)
        $stmt->bind_result($fetched_id, $fetched_name, $fetched_pnumber, $fetched_email, $hashed_password);

        // Fetch the result (if a user with that email exists)
        if ($stmt->fetch()) {
            // Verify the password against the stored hash (ensure it's a string to avoid null type errors)
            if (is_string($hashed_password) && password_verify($password, $hashed_password)) {
                // Password is correct. Set the session variables.
                $_SESSION["id"] = $fetched_id;
                $_SESSION["name"] = $fetched_name;
                $_SESSION["pnumber"] = $fetched_pnumber;
                $_SESSION["email"] = $fetched_email;
                
                // Redirect to the home page
                header("Location: \COMMERCE\index.php");
                exit();
            } else {
                // Invalid password or missing password hash
                $errormessage = "Invalid email or password.";
            }
        } else {
            // User not found
            $errormessage = "Invalid email or password.";
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
    <title>User Login</title>
    <!-- Load Tailwind CSS via CDN for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
     <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    <link rel="stylesheet" href="site.css">
</head>
<body>
<div class="login-container">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Login</h2>
    <hr class="mb-6"/>
    
    <!-- CORRECTED: Form tag was misspelled -->
    <form method="post" class="space-y-4">
    
        <?php if(!empty($errormessage)): ?>
        <!-- Custom alert styled with Tailwind -->
        <div class='alert-danger' role='alert'>
            <?= $errormessage ?>
        </div>
        <?php endif; ?>
        
        <!-- Name field is unusual for login, but kept to match user's original logic -->
        <div class="my-3">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>" />
        </div>
        
        <div class="my-3">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
            <!-- CORRECTED: Added name="email" so the value is sent via POST -->
            <input class="form-control" type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" />
        </div>
        
        <div class="my-3">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password:</label>
            <input class="form-control" type="password" id="password" name="password" />
        </div>
        
        <div class="flex flex-row justify-between pt-4">
            <div class="flex space-x-4">
                <button class="btn btn-primary" type="submit">Login</button>
                <button class="btn btn-danger" type="reset">Cancel</button>
            </div>
        </div>
        
        <div class="text-sm text-center pt-4 text-gray-600">
            If You Don't Have An Account, 
            <a href="/COMMERCE/create.php" class="font-medium text-blue-600 hover:text-blue-500">Click here</a>
        </div>
    </form>
</div>
<script src="script.js"></script>
</body>
</html>