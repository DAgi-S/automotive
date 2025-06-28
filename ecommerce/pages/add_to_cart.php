<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['password'])) {
    header("location: ../../login.php?message=login first");
    exit;
}

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: profile.php?error=invalid_product#cart-content");
    exit;
}

include("../includes/db_con.php");

$db = new DB_Ecom();
$product_id = (int)$_GET['id'];
$user_id = $_SESSION['id'];
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

// Add to cart
$result = $db->add_to_cart($user_id, $product_id, $quantity);

if ($result) {
    header("location: profile.php?success=added_to_cart#cart-content");
} else {
    header("location: profile.php?error=add_to_cart_failed#cart-content");
}
exit;
?> 