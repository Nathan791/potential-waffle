<?php
// content_moderation.php

session_start();

// --- CONFIG ---
$db_servername = "localhost";
$db_username = "root";
$db_password = "";
$db_database = "commerce";
$perPage = 10;
// ---------------

// Require admin
if (!isset($_SESSION["email"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Connect DB (mysqli)
$connection = new mysqli($db_servername, $db_username, $db_password, $db_database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Messages pour l'UI
$messages = [];
$errors = [];

// --- Handle POST actions (approve / reject) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $posted_token)) {
        $errors[] = "Token CSRF invalide. Réessayez.";
    } else {
        $action = $_POST['action'] ?? '';
        $content_id = $_POST['content_id'] ?? '';

        // basic validation
        if (!in_array($action, ['approve', 'reject'], true) || !ctype_digit((string)$content_id)) {
            $errors[] = "Requête invalide.";
        } else {
            // Update status using prepared statement
            $new_status = ($action === 'approve') ? 'approved' : 'rejected';
            $stmt = $connection->prepare("UPDATE user_content SET status = ? WHERE content_id = ? LIMIT 1");
            if ($stmt === false) {
                $errors[] = "Erreur serveur (préparation requête).";
            } else {
                $stmt->bind_param("si", $new_status, $content_id);
                if ($stmt->execute()) {
                    $messages[] = "Contenu #{$content_id} " . ($action === 'approve' ? "approuvé" : "rejeté") . " avec succès.";

                    // Optionnel : enregistrer dans system_logs
                    $log_stmt = $connection->prepare("INSERT INTO system_logs (user_email, action, details) VALUES (?, ?, ?)");
                    if ($log_stmt) {
                        $user_email = $_SESSION['email'];
                        $log_action = "moderation_{$new_status}";
                        $details = "content_id={$content_id}";
                        $log_stmt->bind_param("sss", $user_email, $log_action, $details);
                        $log_stmt->execute();
                        $log_stmt->close();
                    }
                } else {
                    $errors[] = "Impossible de mettre à jour le statut (ID: {$content_id}).";
                }
                $stmt->close();
            }
        }
    }
}

// --- Pagination: calculer offset ---
$page = isset($_GET['page']) && ctype_digit($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $perPage;

// Count total pending rows (pour pagination)
$count_stmt = $connection->prepare("SELECT COUNT(*) FROM user_content WHERE status = 'pending'");
$total_pending = 0;
if ($count_stmt) {
    $count_stmt->execute();
    $count_stmt->bind_result($total_pending);
    $count_stmt->fetch();
    $count_stmt->close();
}

// Fetch pending rows paginated
$rows = [];
// NOTE: MySQLi accepts parameters for LIMIT/OFFSET when using prepare + bind_param
$stmt = $connection->prepare("SELECT content_id, user_name, content, created_at FROM user_content WHERE status = 'pending' ORDER BY content_id DESC LIMIT ? OFFSET ?");
if ($stmt) {
    // bind as integers
    $stmt->bind_param("ii", $perPage, $offset);
    $stmt->execute();
    $stmt->bind_result($fetched_content_id, $fetched_user_name, $fetched_content, $fetched_created_at);
    while ($stmt->fetch()) {
        $rows[] = [
            'content_id' => (int) $fetched_content_id,
            'user_name'  => (string)$fetched_user_name,
            'content'    => (string)$fetched_content,
            'created_at' => (string)$fetched_created_at,
        ];
    }
    $stmt->close();
}

$connection->close();

// Helper: truncate text safely
function truncate_text($text, $max = 150) {
    if (mb_strlen($text) <= $max) return $text;
    return mb_substr($text, 0, $max) . '...';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modération de contenu — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <header class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">Modération de contenu</h1>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600">Connecté : <?= htmlspecialchars($_SESSION['email']) ?></span>
                <a href="logout.php" class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600">Se déconnecter</a>
            </div>
        </header>

        <!-- Messages -->
        <?php if (!empty($messages)): ?>
            <div class="mb-4">
                <?php foreach ($messages as $m): ?>
                    <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded mb-2">
                        <?= htmlspecialchars($m) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="mb-4">
                <?php foreach ($errors as $e): ?>
                    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded mb-2">
                        <?= htmlspecialchars($e) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Contenus en attente (<?= $total_pending ?>)</h2>

            <?php if (empty($rows)): ?>
                <p class="text-gray-600">Aucun contenu à modérer sur cette page.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 border text-left">ID</th>
                                <th class="px-4 py-2 border text-left">Utilisateur</th>
                                <th class="px-4 py-2 border text-left">Contenu</th>
                                <th class="px-4 py-2 border text-left">Créé le</th>
                                <th class="px-4 py-2 border text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr class="align-top">
                                    <td class="px-4 py-3 border align-top"><?= htmlspecialchars($row['content_id']) ?></td>
                                    <td class="px-4 py-3 border align-top"><?= htmlspecialchars($row['user_name']) ?></td>
                                    <td class="px-4 py-3 border align-top">
                                        <div class="max-w-xl break-words">
                                            <div class="text-sm text-gray-800"><?= htmlspecialchars(truncate_text($row['content'], 200)) ?></div>
                                            <?php if (mb_strlen($row['content']) > 200): ?>
                                                <details class="text-xs text-gray-600 mt-1">
                                                    <summary class="cursor-pointer">Voir le contenu complet</summary>
                                                    <div class="mt-2 whitespace-pre-wrap"><?= htmlspecialchars($row['content']) ?></div>
                                                </details>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border align-top"><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                                    <td class="px-4 py-3 border align-top">
                                        <form method="POST" class="inline-block mr-2">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="content_id" value="<?= htmlspecialchars($row['content_id']) ?>">
                                            <button type="submit" name="action" value="approve" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Approuver</button>
                                        </form>

                                        <form method="POST" class="inline-block">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="content_id" value="<?= htmlspecialchars($row['content_id']) ?>">
                                            <button type="submit" name="action" value="reject" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Rejeter</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php
                $totalPages = ($total_pending > 0) ? (int)ceil($total_pending / $perPage) : 1;
                ?>
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">Page <?= $page ?> / <?= $totalPages ?></div>
                    <div class="flex gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Précédente</a>
                        <?php endif; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Suivante</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
