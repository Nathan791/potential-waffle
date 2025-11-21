<?php
// Initialisation of variables
global $hashed_password;
$name = "";
$lname = "";
$pnumber = "";
$email = "";
$password = "";

$errormessage = "";
$successmessage = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération of form data
    $name = $_POST["name"];
    $lname = $_POST["lname"];
    $pnumber = $_POST["pnumber"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    // Validation of fields
    if (empty($name) || empty($lname) || empty($pnumber) || empty($email) || empty($password)) {
        $errormessage = "All The fields are required.";

    } else {
        // Connexion to the database
        $db_servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $db_database = "commerce";

        // Create connection
        $connection = new mysqli($db_servername, $db_username, $db_password, $db_database);

        // Vérifying the connection
        if ($connection->connect_error) {
            die("Connection failed : " . $connection->connect_error);
        }

        // Prépare the query to avoid SQL injection
        $stmt = $connection->prepare("INSERT INTO shop (name, lname, pnumber, email, password) VALUES (?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $errormessage = "Erreur de préparation de la requête : " . $connection->error;
        } else {
        //SECURITY - HASHING
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        // Link the parameters
        $stmt->bind_param("ssiss", $name, $lname, $pnumber, $email, $hashed_password);

            // Exécute the query
            if ($stmt->execute()) {
                $successmessage = "User added succesufully  !";
                // Réinitialise the variables to clear the form
                $name = "";
                $lname = "";
                $pnumber = "";
                $email = "";
                $password = "";
                $hashed_password = "";
                // Redirction for avoiding multiple submissions
                header("Location: index.html");
                exit();
            } else {
                $errormessage = "Error during insertion : " . $stmt->error;
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
    <title>Create a New User Account</title>
    <!-- Load Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="white.css">
</head>
<body>
<<div class="register-container">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Create New Account</h2>
    <hr class="mb-6 border-gray-300">
    
    <form method="post" class="space-y-4">
        
        <?php if (!empty($errormessage)): ?>
            <div class='alert-warning' role='alert'>
                <strong><?= htmlspecialchars($errormessage) ?></strong>
            </div>
        <?php endif; ?>
        
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">First Name:</label>
            <input type="text" class="form-input" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
        </div>
        
        <div>
            <label for="lname" class="block text-sm font-medium text-gray-700 mb-1">Last Name:</label>
            <input type="text" class="form-input" id="lname" name="lname" value="<?= htmlspecialchars($lname) ?>" required>
        </div>
        
        <div>
            <label for="pnumber" class="block text-sm font-medium text-gray-700 mb-1">Phone Number:</label>
            <input type="text" class="form-input" id="pnumber" name="pnumber" value="<?= htmlspecialchars($pnumber) ?>" required pattern="\d*">
        </div>
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
            <input type="email" class="form-input" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password:</label>
            <input type="password" class="form-input" name="password" id="password" required>
        </div>

        <?php if (!empty($successmessage)): ?>
            <div class='alert-success' role='alert'>
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
        
        <div class="mt-3 text-sm text-center text-gray-600">
            <a href="index.html" class="font-medium text-indigo-600 hover:text-indigo-500">Return to the list</a> |
            <a href="/Formulaire/index.html" class="font-medium text-indigo-600 hover:text-indigo-500">Return to Home</a>
        </div>
    </form>
</div>
</body>
</html>