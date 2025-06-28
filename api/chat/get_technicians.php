<?php
// /api/chat/get_technicians.php
// Returns all technicians (workers) as JSON for chat assignment.

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$response = ["success" => false, "error" => null, "technicians" => []];

try {
    // Fetch all workers (technicians)
    $stmt = $conn->prepare("SELECT id, full_name, image_url FROM tbl_worker ORDER BY full_name ASC");
    $stmt->execute();
    $response['technicians'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>