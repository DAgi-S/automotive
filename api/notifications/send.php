<?php
/**
 * Send Notification API Endpoint
 * POST /api/notifications/send.php
 */

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
    $required_fields = ['type', 'recipient_id', 'message'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }
    
    // Initialize notification manager
    $notificationManager = new NotificationManager($pdo);
    
    // Prepare notification data
    $notification_data = [
        'message' => $input['message'],
        'subject' => $input['subject'] ?? 'Notification from Nati Automotive',
        'template_key' => $input['template_key'] ?? '',
        'reference_id' => $input['reference_id'] ?? null,
        'email' => $input['email'] ?? '',
        'name' => $input['name'] ?? '',
        'telegram_chat_id' => $input['telegram_chat_id'] ?? ''
    ];
    
    // Add any additional data
    if (isset($input['data']) && is_array($input['data'])) {
        $notification_data = array_merge($notification_data, $input['data']);
    }
    
    // Determine notification methods
    $methods = $input['methods'] ?? ['web'];
    if (!is_array($methods)) {
        $methods = [$methods];
    }
    
    // Send notification
    $result = $notificationManager->sendNotification(
        $input['type'],
        $input['recipient_id'],
        $notification_data,
        $methods
    );
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Notification sent successfully',
            'results' => $result
        ]);
    } else {
        throw new Exception('Failed to send notification');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 