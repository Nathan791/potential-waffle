<?php
session_start();

// If user already logged in, redirect based on role
if(isset($_SESSION["role"])) {
    if($_SESSION["role"] === "admin"){
        header("Location: /COMMERCE/admin-dashboard.php");
        exit();
    } else {
        header("Location: /COMMERCE/user-dashboard.php");
        exit();
    }
}

$errormessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if(empty($email) || empty($password)){
        $errormessage = "Email and password are required.";
    } else {

        // DB connection
        $db = new mysqli("localhost", "root", "", "commerce");

        if ($db->connect_error) {
            die("Database connection failed: " . $db->connect_error);
        }

        // Get user including the role
        $id = null;
        $name = "";
        $pnumber = "";
        $db_email = "";
        $db_password = ""; // <-- ensure string type for static analyzer

        $stmt = $db->prepare("SELECT id, name, pnumber, email, password, role FROM shop WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $stmt->bind_result($id, $name, $pnumber, $db_email, $db_password, $role);

        if ($stmt->fetch()) {

            // validate that $db_password is a non-empty string before verifying
            if (!is_string($db_password) || $db_password === '') {
                $errormessage = "Invalid email or password.";
            } elseif (password_verify($password, $db_password)) {
                // Store session data
                $_SESSION["id"] = $id;
                $_SESSION["name"] = $name;
                $_SESSION["pnumber"] = $pnumber;
                $_SESSION["email"] = $db_email;
                $_SESSION["role"] = $role;

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

        } else {
            $errormessage = "Invalid email or password.";
        }

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
    <title>Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
<div class="max-w-md mx-auto mt-20 p-8 bg-white rounded shadow">

    <h2 class="text-3xl font-bold text-center mb-6">Login</h2>

    <?php if(!empty($errormessage)): ?>
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
