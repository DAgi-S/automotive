<?php
session_start();
include('includes/db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;

if ($appointment_id) {
    $query = "SELECT a.*, c.id as client_id, c.name as client_name, 
              v.license_plate, v.model as car_model
              FROM appointments a
              JOIN clients c ON a.client_id = c.id
              LEFT JOIN vehicles v ON a.vehicle_id = v.id
              WHERE a.id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $appointment_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'status' => 'success',
            'client_id' => $row['client_id'],
            'client_name' => $row['client_name'],
            'license_plate' => $row['license_plate'],
            'car_model' => $row['car_model']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Appointment not found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid appointment ID']);
}
?> 