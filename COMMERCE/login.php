<?php
session_start();

// Redirect if already logged in
if (!empty($_SESSION["role"])) {
    $redirect = ($_SESSION["role"] === "admin") 
        ? "/COMMERCE/admin-dashboard.php" 
        : "/COMMERCE/user-dashboard.php";

    header("Location: $redirect");
    exit();
}

$errormessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $errormessage = "Email and password are required.";
    } else {

        $db = new mysqli("localhost", "root", "", "commerce");

        if ($db->connect_error) {
            die("Database connection failed: " . $db->connect_error);
        }

        $stmt = $db->prepare("
            SELECT id, name, email, password, role 
            FROM users 
            WHERE email = ?
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // If user exists
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $name,  $db_email, $db_password, $role);
            $stmt->fetch();

            if (password_verify($password, $db_password)) {
                
                // Store user session
                $_SESSION["id"] = $id;
                $_SESSION["name"] = $name;
                $_SESSION["email"] = $db_email;
                $_SESSION["role"] = $role;

                // Redirect based on role
                $redirect = ($role === "admin")
                    ? "/COMMERCE/admin-dashboard.php"
                    : "/COMMERCE/user-dashboard.php";

                header("Location: $redirect");
                exit();
            }
        }

        // Default error
        $errormessage = "Invalid email or password.";

        $stmt->close();
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
<div class="max-w-md mx-auto mt-20 p-8 bg-white rounded shadow">

    <h2 class="text-3xl font-bold text-center mb-6">Login</h2>

    <?php if (!empty($errormessage)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errormessage) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">

        <div>
            <label class="block font-medium">Email</label>
            <input class="form-control" type="email" name="email" required>
        </div>

        <div>
            <label class="block font-medium">Password</label>
            <input class="form-control" type="password" name="password" required>
        </div>

        <div class="pt-4">
            <button type="submit" class="btn btn-primary w-full">Login</button>
        </div>

        <p class="text-center text-sm mt-4">
            Don’t have an account?
            <a href="/COMMERCE/create.php" class="text-blue-600">Register</a>
        </p>

    </form>

</div>
</body>
</html>
