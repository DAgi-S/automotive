<?php
/**
 * Get User Notifications API Endpoint
 * GET /api/notifications/get.php?user_id=1&limit=10
 */

// Start output buffering to catch any unwanted output
ob_start();

// Disable error display for API
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once '../../config/database.php';
require_once '../../includes/NotificationManager.php';

try {
    // Get parameters
    $user_id = $_GET['user_id'] ?? null;
    $limit = (int)($_GET['limit'] ?? 10);
    $unread_only = isset($_GET['unread_only']) && $_GET['unread_only'] === 'true';
    
    if (!$user_id) {
        throw new Exception('User ID is required');
    }
    
    // Use the correct database connection
    $pdo = isset($pdo) ? $pdo : $conn;
    
    // Initialize notification manager
    $notificationManager = new NotificationManager($pdo);
    
    if ($unread_only) {
        $notifications = $notificationManager->getUnreadNotifications($user_id, $limit);
    } else {
        // Get all notifications
        $stmt = $pdo->prepare("
            SELECT * FROM tbl_notifications 
            WHERE recipient_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$user_id, $limit]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get notification count
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread
        FROM tbl_notifications 
        WHERE recipient_id = ?
    ");
    $stmt->execute([$user_id]);
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Clear any unwanted output
    ob_clean();
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'counts' => $counts
    ]);
    
} catch (Exception $e) {
    // Clear any unwanted output
    ob_clean();
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// End output buffering
ob_end_flush(); 