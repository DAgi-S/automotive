<?php
session_start();
require_once '../../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get role ID from request
$role_id = isset($_GET['role_id']) ? (int)$_GET['role_id'] : 0;

if ($role_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid role ID']);
    exit();
}

header('Content-Type: application/json');

try {
    // Get permissions for the role
    $stmt = $conn->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
    $stmt->execute([$role_id]);
    $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['permissions' => $permissions]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?> 