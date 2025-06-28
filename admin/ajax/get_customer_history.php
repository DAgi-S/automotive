<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once('../../config/database.php');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$userId = isset($input['user_id']) ? intval($input['user_id']) : 0;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid user ID']);
    exit();
}

try {
    // Fetch customer's appointment history
    $stmt = $conn->prepare("
        SELECT 
            a.appointment_date,
            s.service_name,
            s.price,
            a.status
        FROM tbl_appointments a
        LEFT JOIN tbl_services s ON a.service_id = s.service_id
        WHERE a.user_id = :user_id
        ORDER BY a.appointment_date DESC
    ");
    
    $stmt->execute(['user_id' => $userId]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dates and ensure proper data types
    foreach ($history as &$appointment) {
        $appointment['appointment_date'] = date('M d, Y', strtotime($appointment['appointment_date']));
        $appointment['price'] = floatval($appointment['price']);
    }
    
    header('Content-Type: application/json');
    echo json_encode($history);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 