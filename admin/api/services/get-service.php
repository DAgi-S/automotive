<?php
session_start();
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

require_once('../../../config/database.php');

// Get service ID from query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Invalid service ID']));
}

try {
    $stmt = $conn->prepare("SELECT * FROM tbl_services WHERE service_id = :id");
    $stmt->execute(['id' => $id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        http_response_code(404);
        die(json_encode(['success' => false, 'error' => 'Service not found']));
    }

    echo json_encode([
        'success' => true,
        'data' => $service
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]));
} 