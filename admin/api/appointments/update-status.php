<?php
header('Content-Type: application/json');
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

// Include database connection
require_once('../../../config/database.php');

// Log incoming request data
error_log('Received request data: ' . print_r($_POST, true));

// Get POST data
$id = isset($_POST['id']) ? $_POST['id'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;

// Log the received values
error_log("Appointment ID: " . $id);
error_log("New Status: " . $status);

// Validate input
if (!$id || !$status) {
    error_log("Missing required fields - ID: $id, Status: $status");
    die(json_encode(['success' => false, 'error' => 'Missing required fields']));
}

// Validate status
$validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
if (!in_array($status, $validStatuses)) {
    error_log("Invalid status: $status");
    die(json_encode(['success' => false, 'error' => 'Invalid status']));
}

try {
    // Check if appointment exists
    $check = $conn->prepare("SELECT appointment_id FROM tbl_appointments WHERE appointment_id = :id");
    $check->execute(['id' => $id]);
    
    if ($check->rowCount() === 0) {
        error_log("Appointment not found: $id");
        die(json_encode(['success' => false, 'error' => 'Appointment not found']));
    }
    
    // Update appointment status
    $stmt = $conn->prepare("UPDATE tbl_appointments SET status = :status WHERE appointment_id = :id");
    if (!$stmt) {
        error_log("Prepare failed: " . implode(", ", $conn->errorInfo()));
        die(json_encode(['success' => false, 'error' => 'Database prepare error']));
    }
    
    $result = $stmt->execute([
        'status' => $status,
        'id' => $id
    ]);
    
    if (!$result) {
        error_log("Execute failed: " . implode(", ", $stmt->errorInfo()));
        die(json_encode(['success' => false, 'error' => 'Failed to update status']));
    }
    
    if ($stmt->rowCount() > 0) {
        // Log the activity
        $action = "Appointment #$id status updated to $status";
        $activityStmt = $conn->prepare("INSERT INTO activity_log (type, action, reference_id) VALUES (:type, :action, :ref_id)");
        $activityStmt->execute([
            'type' => 'appointment',
            'action' => $action,
            'ref_id' => $id
        ]);
        
        error_log("Successfully updated appointment $id to status $status");
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        error_log("No rows affected for appointment $id");
        echo json_encode(['success' => false, 'error' => 'No changes made']);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()]);
} 