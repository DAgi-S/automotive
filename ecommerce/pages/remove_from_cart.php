<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['password'])) {
    header("location: ../../login.php?message=login first");
    exit;
}

// Check if cart item ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: profile.php?error=invalid_item#cart-content");
    exit;
}

include("../includes/db_con.php");

$db = new DB_Ecom();
$cart_id = (int)$_GET['id'];
$user_id = $_SESSION['id'];

// Remove from cart
$result = $db->remove_from_cart($cart_id, $user_id);

if ($result) {
    header("location: profile.php?success=removed_from_cart#cart-content");
} else {
    header("location: profile.php?error=remove_from_cart_failed#cart-content");
}
exit;
?> 