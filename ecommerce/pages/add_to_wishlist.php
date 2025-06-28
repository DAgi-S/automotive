<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['password'])) {
    header("location: ../../login.php?message=login first");
    exit;
}

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: profile.php?error=invalid_product#wishlist-content");
    exit;
}

include("../includes/db_con.php");

$db = new DB_Ecom();
$product_id = (int)$_GET['id'];
$user_id = $_SESSION['id'];

// Add to wishlist
$result = $db->add_to_wishlist($user_id, $product_id);

if ($result) {
    header("location: profile.php?success=added_to_wishlist#wishlist-content");
} else {
    header("location: profile.php?error=add_to_wishlist_failed#wishlist-content");
}
exit;
?> 