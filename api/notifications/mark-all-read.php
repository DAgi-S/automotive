<?php
/**
 * Mark All Notifications as Read API Endpoint
 * POST /api/notifications/mark-all-read.php
 */

// Start output buffering and disable error display
ob_start();
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once '../../config/database.php';

try {
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    if (!isset($input['user_id'])) {
        throw new Exception('Missing required field: user_id');
    }
    
    $user_id = (int)$input['user_id'];
    
    // Use the correct database connection
    $pdo = isset($pdo) ? $pdo : $conn;
    
    // Mark all notifications as read for the user
    $stmt = $pdo->prepare("
        UPDATE tbl_notifications 
        SET is_read = 1 
        WHERE recipient_id = ? AND is_read = 0
    ");
    
    $result = $stmt->execute([$user_id]);
    $affected_rows = $stmt->rowCount();
    
    if ($result) {
        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => "Marked {$affected_rows} notifications as read"
        ]);
    } else {
        throw new Exception('Failed to mark notifications as read');
    }
    
} catch (Exception $e) {
    ob_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

ob_end_flush(); 