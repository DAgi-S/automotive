<?php
/**
 * Mark Notification as Read API Endpoint
 * POST /api/notifications/mark-read.php
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
require_once '../../includes/NotificationManager.php';

try {
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    if (!isset($input['notification_id']) || !isset($input['user_id'])) {
        throw new Exception('Missing required fields: notification_id and user_id');
    }
    
    $notification_id = (int)$input['notification_id'];
    $user_id = (int)$input['user_id'];
    
    // Use the correct database connection
    $pdo = isset($pdo) ? $pdo : $conn;
    
    // Initialize notification manager
    $notificationManager = new NotificationManager($pdo);
    
    // Mark notification as read
    $result = $notificationManager->markAsRead($notification_id, $user_id);
    
    if ($result) {
        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    } else {
        throw new Exception('Failed to mark notification as read');
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