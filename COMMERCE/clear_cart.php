<?php
session_start();

// vider le panier
unset($_SESSION['cart']);

header("Location: product_manager.php"); // change le chemin selon ta page
exit();
?>