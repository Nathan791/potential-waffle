<?php
session_start();
header("Content-Type: application/json");

// verifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION["user_id"];

// DB
$connection = new mysqli("localhost", "root", "", "commerce");

$stmt = $connection->prepare("SELECT id, message, created_at FROM notifications WHERE user_id=? AND is_read=0 ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);
?>