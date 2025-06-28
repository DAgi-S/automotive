<?php
session_start();
header('Content-Type: application/json');

// Get JSON data from request body
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!isset($data['service_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Service ID is required']);
    exit;
}

try {
    // Store service ID in session
    $_SESSION['selected_service_id'] = intval($data['service_id']);
    $_SESSION['service_selection_time'] = time();

    echo json_encode([
        'success' => true,
        'message' => 'Service selection stored successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error'
    ]);
}
?> 