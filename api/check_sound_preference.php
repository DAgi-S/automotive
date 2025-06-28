<?php
header('Content-Type: application/json');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../config/database.php';

try {
    $admin_id = $_GET['admin_id'];
    
    // Validate admin_id matches session
    if ($admin_id != $_SESSION['admin_id']) {
        throw new Exception('Invalid admin ID');
    }
    
    // Get sound preferences for all notification types
    $stmt = $conn->prepare(
        "SELECT notification_type, sound_enabled 
         FROM tbl_notification_preferences 
         WHERE admin_id = ?"
    );
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $preferences = [];
    while ($row = $result->fetch_assoc()) {
        $preferences[$row['notification_type']] = (bool)$row['sound_enabled'];
    }
    
    // If no preferences are set, default to true
    if (empty($preferences)) {
        echo json_encode(['sound_enabled' => true]);
    } else {
        // If any notification type has sound enabled, return true
        $sound_enabled = in_array(true, $preferences);
        echo json_encode(['sound_enabled' => $sound_enabled]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 