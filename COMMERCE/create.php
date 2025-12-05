<?php
// Initialization of variables
global $hashed_password;
$name = "";
$lname = "";
$pnumber = "";
$email = "";
$password = "";
$role = ""; // New variable for role

$errormessage = "";
$successmessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"]; // Retrieve role

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $errormessage = "All fields are required.";
    } else {
        // Database connection
        $db_servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $db_database = "commerce";

        $connection = new mysqli($db_servername, $db_username, $db_password, $db_database);

        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // Prepare query to avoid SQL injection
        $stmt = $connection->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            $errormessage = "Query preparation error: " . $connection->error;
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Bind parameters (s = string, i = integer)
            $stmt->bind_param("ssss", $name,  $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $successmessage = "Account created successfully!";
                 // Redirect based on role
                if($role === "admin"){
                    header("Location: /COMMERCE/admin-dashboard.php");
                } else {
                    header("Location: /COMMERCE/user-dashboard.php");
                }
                exit();
            } else {
                $errormessage = "Invalid email or password.";
            }

          
            $stmt->close();
        }
        $connection->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark:bg-gray-950 scheme-light dark:scheme-dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create a New Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" />
</head>
<body>
<div class="register-container p-6 max-w-lg mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Create New Account</h2>
    <hr class="mb-6 border-gray-300">
    
    <form method="post" class="space-y-4">
        <?php if (!empty($errormessage)): ?>
            <div class='alert alert-warning' role='alert'>
                <strong><?= htmlspecialchars($errormessage) ?></strong>
            </div>
        <?php endif; ?>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">First Name:</label>
            <input type="text" class="form-input form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
            <input type="email" class="form-input form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password:</label>
            <input type="password" class="form-input form-control" id="password" name="password" required>
        </div>

        <!-- New role selection -->
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role:</label>
            <select name="role" id="role" class="form-select form-control" required>
                <option value="" disabled selected>Select role</option>
                <option value="user" <?= $role === "user" ? "selected" : "" ?>>User</option>
                <option value="admin" <?= $role === "admin" ? "selected" : "" ?>>Admin</option>
            </select>
        </div>

        <?php if (!empty($successmessage)): ?>
            <div class='alert alert-success' role='alert'>
                <strong><?= htmlspecialchars($successmessage) ?></strong>
            </div>
        <?php endif; ?>

        <div class="flex flex-col space-y-4 pt-6">
            <button type="submit" class="btn btn-primary">Create Account</button>
            <button type="reset" class="btn btn-secondary">Clear Form</button>
        </div>
        
        <div class="mt-6 text-sm text-center text-gray-600">
            Already have an account? 
            <a href="/COMMERCE/login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in here.</a>
        </div>
        
        
    </form>
</div>
</body>
</html>
