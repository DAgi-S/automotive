<?php
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

// Check if user is logged in and has admin privileges
if (!isLoggedIn() || !hasAdminPrivileges()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Service ID is required']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT 
            a.appointment_id as history_id,
            a.appointment_date as service_date,
            a.notes,
            s.price as cost,
            a.status,
            i.car_brand,
            i.car_model,
            i.car_year,
            i.plate_number,
            s.service_name,
            s.description as service_description,
            u.name,
            u.phonenum as phone,
            u.email
        FROM tbl_appointments a
        JOIN tbl_services s ON a.service_id = s.service_id
        JOIN tbl_info i ON a.info_id = i.id
        JOIN tbl_user u ON i.userid = u.id
        WHERE a.appointment_id = :id
    ");
    
    $stmt->execute(['id' => $_GET['id']]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        throw new Exception('Service not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $service
    ]);
    
} catch (Exception $e) {
    error_log("Error in get-service-details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 