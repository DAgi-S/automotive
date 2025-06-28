<?php
header('Content-Type: application/json');
require_once('../db_conn.php');

if (!isset($_GET['date'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Date parameter is required']);
    exit;
}

$date = $_GET['date'];

// Validate date format
if (!strtotime($date)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}

try {
    // Business hours configuration
    $start_hour = 9; // 9 AM
    $end_hour = 17; // 5 PM
    $slot_duration = 60; // 60 minutes per slot
    
    // Get existing appointments for the date
    $stmt = $conn->prepare("SELECT appointment_time FROM tbl_appointments WHERE appointment_date = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $booked_slots = [];
    while ($row = $result->fetch_assoc()) {
        $booked_slots[] = $row['appointment_time'];
    }
    
    // Generate all possible time slots
    $slots = [];
    for ($hour = $start_hour; $hour < $end_hour; $hour++) {
        $time = sprintf("%02d:00", $hour);
        $slots[] = [
            'time' => $time,
            'available' => !in_array($time, $booked_slots)
        ];
    }
    
    echo json_encode($slots);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}

$stmt->close();
$conn->close();
?> 