<?php
session_start();
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once '../../includes/config.php';

// Validate customer ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid customer ID']);
    exit();
}

$customerId = (int)$_GET['id'];

try {
    // Prepare and execute query to get customer details
    $stmt = $conn->prepare("SELECT id, name, email, phonenum, car_brand, new_img_name, created_at 
                           FROM tbl_user 
                           WHERE id = :id AND role = 'user'");
    $stmt->bindParam(':id', $customerId, PDO::PARAM_INT);
    $stmt->execute();
    
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Customer not found']);
        exit();
    }
    
    // Prepare the response data
    $response = [
        'success' => true,
        'data' => [
            'id' => $customer['id'],
            'name' => $customer['name'],
            'email' => $customer['email'],
            'phonenum' => $customer['phonenum'],
            'car_brand' => $customer['car_brand'],
            'profile_image' => $customer['new_img_name'] ? '../uploads/' . $customer['new_img_name'] : null,
            'created_at' => $customer['created_at']
        ]
    ];
    
    echo json_encode($response);
    
} catch(PDOException $e) {
    error_log("Error fetching customer details: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching customer details']);
    exit();
} 