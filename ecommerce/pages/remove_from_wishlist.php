<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['password'])) {
    header("location: ../../login.php?message=login first");
    exit;
}

// Check if wishlist item ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: profile.php?error=invalid_item#wishlist-content");
    exit;
}

include("../includes/db_con.php");

$db = new DB_Ecom();
$wishlist_id = (int)$_GET['id'];
$user_id = $_SESSION['id'];

// Remove from wishlist
$result = $db->remove_from_wishlist($wishlist_id, $user_id);

if ($result) {
    header("location: profile.php?success=removed_from_wishlist#wishlist-content");
} else {
    header("location: profile.php?error=remove_from_wishlist_failed#wishlist-content");
}
exit;
?> 