<?php
session_start();
include('includes/db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<option value="">Unauthorized access</option>';
    exit;
}

// Get pending appointments
$query = "SELECT a.id, a.appointment_date, c.name as client_name 
          FROM appointments a 
          JOIN clients c ON a.client_id = c.id 
          WHERE a.status = 'pending' 
          ORDER BY a.appointment_date DESC";

$result = mysqli_query($conn, $query);

echo '<option value="">No Appointment</option>';

while ($row = mysqli_fetch_assoc($result)) {
    $appointment_date = date('M d, Y h:i A', strtotime($row['appointment_date']));
    echo '<option value="' . $row['id'] . '">' . 
         htmlspecialchars($row['client_name']) . ' - ' . $appointment_date . 
         '</option>';
}
?> 