<?php
session_start();
header('Content-Type: application/json');
require_once('../db_conn.php');
require_once('notification_handler.php');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['car_id']) || !isset($data['services']) || !isset($data['appointment_date']) || !isset($data['appointment_time'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();

    $user_id = $_SESSION['id'];
    $car_id = intval($data['car_id']);
    $services = $data['services'];
    $appointment_date = $data['appointment_date'];
    $appointment_time = $data['appointment_time'];
    $priority = $data['priority'] ?? 'low';
    $notes = $data['notes'] ?? '';

    // Create the main appointment
    $stmt = $conn->prepare("INSERT INTO tbl_appointments (user_id, car_id, appointment_date, appointment_time, 
                           multiple_services, customer_notes, priority, status, notification_sent) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', FALSE)");
    
    $services_json = json_encode($services);
    $stmt->bind_param("iissssss", $user_id, $car_id, $appointment_date, $appointment_time, 
                     $services_json, $notes, $priority);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to create appointment: " . $stmt->error);
    }

    $appointment_id = $conn->insert_id;

    // Handle worker assignments
    foreach ($services as $service) {
        // Find available worker for the service
        $worker_stmt = $conn->prepare("SELECT w.id FROM tbl_worker w 
                                     LEFT JOIN tbl_worker_assignments wa 
                                     ON w.id = wa.worker_id 
                                     AND wa.assigned_date = ? 
                                     AND wa.assigned_time = ?
                                     WHERE wa.id IS NULL
                                     LIMIT 1");
        
        $worker_stmt->bind_param("ss", $appointment_date, $appointment_time);
        $worker_stmt->execute();
        $worker_result = $worker_stmt->get_result();
        
        if ($worker_row = $worker_result->fetch_assoc()) {
            $worker_id = $worker_row['id'];
            
            // Create worker assignment
            $assign_stmt = $conn->prepare("INSERT INTO tbl_worker_assignments 
                                         (worker_id, appointment_id, assigned_date, assigned_time, status)
                                         VALUES (?, ?, ?, ?, 'assigned')");
            
            $assign_stmt->bind_param("iiss", $worker_id, $appointment_id, $appointment_date, $appointment_time);
            $assign_stmt->execute();
            $assign_stmt->close();
        }
        $worker_stmt->close();
    }

    // Handle image uploads if any were provided
    if (isset($data['uploaded_images']) && is_array($data['uploaded_images'])) {
        $image_stmt = $conn->prepare("INSERT INTO tbl_appointment_images 
                                    (appointment_id, image_path, original_name) 
                                    VALUES (?, ?, ?)");
        
        foreach ($data['uploaded_images'] as $image) {
            $image_stmt->bind_param("iss", $appointment_id, $image['path'], $image['original_name']);
            $image_stmt->execute();
        }
        $image_stmt->close();
    }

    // Create notification for admins
    $notificationHandler = new NotificationHandler($conn);
    $notificationHandler->createServiceBookingNotification($appointment_id, $user_id, $services);

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'appointment_id' => $appointment_id,
        'message' => 'Service booking created successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?> 