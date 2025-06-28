<?php
session_start();

// Include database connection
require_once('../../config/database.php');

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

// Set proper content type
header('Content-Type: application/json');

$vehicle_id = isset($_GET['vehicle_id']) ? intval($_GET['vehicle_id']) : 0;

if (!$vehicle_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Vehicle ID is required']);
    exit;
}

try {
    $query = "SELECT u.id, u.name, u.email, u.phonenum 
              FROM tbl_info i 
              JOIN tbl_user u ON i.userid = u.id 
              WHERE i.id = :vehicle_id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':vehicle_id', $vehicle_id);
    $stmt->execute();
    
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        http_response_code(404);
        echo json_encode(['error' => 'Customer not found']);
        exit;
    }
    
    echo json_encode($customer);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'details' => $e->getMessage()
    ]);
}
?> 