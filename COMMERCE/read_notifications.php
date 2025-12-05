<?php
session_start();
if (!isset($_SESSION["user_id"])) exit();

$connection = new mysqli("localhost", "root", "", "commerce");

if (isset($_POST["id"])) {
    $stmt = $connection->prepare("UPDATE notifications SET is_read=1 WHERE id=?");
    $stmt->bind_param("i", $_POST["id"]);
    $stmt->execute();
}

?>