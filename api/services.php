<?php
header('Content-Type: application/json');
require_once('../db_conn.php');

// Check if service_id is provided
if (!isset($_GET['service_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Service ID is required']);
    exit;
}

$service_id = intval($_GET['service_id']);

try {
    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT service_id, service_name, description, price, duration, status, 
                           (SELECT CONCAT('assets/images/dashboard/', LOWER(REPLACE(service_name, ' ', '_')), '.png')) as image_url 
                           FROM tbl_services WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
        echo json_encode([
            'id' => $service['service_id'],
            'name' => $service['service_name'],
            'description' => $service['description'],
            'price' => $service['price'],
            'duration' => $service['duration'],
            'status' => $service['status'],
            'image_url' => $service['image_url']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Service not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}

$stmt->close();
$conn->close();
?> 