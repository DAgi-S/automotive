<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first");
    exit;
}

// Check if appointment ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: profile.php?error=invalid_appointment");
    exit;
}

include("partial-front/db_con.php");

$db = new DB_con();
$appointment_id = (int)$_GET['id'];
$user_id = $_SESSION['id'];

// Cancel the appointment
$result = $db->cancel_appointment($appointment_id, $user_id);

if ($result) {
    header("location: profile.php?success=appointment_cancelled#reviews-content");
} else {
    header("location: profile.php?error=cancel_failed#reviews-content");
}
exit;
?> 