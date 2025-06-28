<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['password'])) {
    header("location: ../../login.php?message=login first");
    exit;
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: profile.php?error=invalid_order");
    exit;
}

include("../includes/db_con.php");

$db = new DB_Ecom();
$order_id = (int)$_GET['id'];
$user_id = $_SESSION['id'];

// Cancel the order
$result = $db->cancel_order($order_id, $user_id);

if ($result) {
    header("location: profile.php?success=order_cancelled#orders-content");
} else {
    header("location: profile.php?error=cancel_failed#orders-content");
}
exit;
?> 