<?php
/**
 * Get Notifications Count API Endpoint
 * Used by the header notification bell to display unread count
 */

// Start output buffering to catch any unwanted output
ob_start();

// Set headers for API response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Include database connection
    require_once __DIR__ . '/../includes/db.php';
    
    // Check if user is logged in
    $user_id = $_SESSION['user_id'] ?? $_SESSION['admin_id'] ?? $_SESSION['id'] ?? null;
    
    if (!$user_id) {
        // Return 0 count for non-logged in users
        ob_clean();
        echo json_encode([
            'success' => true,
            'count' => 0,
            'unread' => 0,
            'total' => 0
        ]);
        exit;
    }
    
    // Check if notifications table exists
    $table_check = $conn->prepare("SHOW TABLES LIKE 'tbl_notifications'");
    $table_check->execute();
    $table_exists = $table_check->rowCount() > 0;
    
    if (!$table_exists) {
        // Return 0 count if table doesn't exist
        ob_clean();
        echo json_encode([
            'success' => true,
            'count' => 0,
            'unread' => 0,
            'total' => 0,
            'message' => 'Notifications table not found'
        ]);
        exit;
    }
    
    // Get notification counts
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread
        FROM tbl_notifications 
        WHERE recipient_id = ? AND is_active = 1
    ");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $unread_count = $result['unread'] ?? 0;
    $total_count = $result['total'] ?? 0;
    
    // Clear any unwanted output
    ob_clean();
    
    // Return the counts
    echo json_encode([
        'success' => true,
        'count' => $unread_count, // For backward compatibility
        'unread' => $unread_count,
        'total' => $total_count
    ]);
    
} catch (Exception $e) {
    // Clear any unwanted output
    ob_clean();
    
    // Return error response
    echo json_encode([
        'success' => true, // Return success to prevent JS errors
        'count' => 0,
        'unread' => 0,
        'total' => 0,
        'message' => 'Notifications not available'
    ]);
}

// End output buffering
ob_end_flush();
?> 