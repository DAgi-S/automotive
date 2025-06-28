<?php
header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once(__DIR__ . '/../../../config/database.php');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$notificationId = $input['id'] ?? null;

if (!$notificationId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Notification ID is required']);
    exit();
}

try {
    // Update notification status
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND admin_id = ?");
    $result = $stmt->execute([$notificationId, $_SESSION['admin_id']]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update notification']);
    }
} catch (PDOException $e) {
    error_log("Error updating notification: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?> 