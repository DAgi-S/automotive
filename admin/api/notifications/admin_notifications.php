<?php
/**
 * Admin Notifications API Endpoint
 * Handles admin-specific notification requests
 */

session_start();
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../../includes/admin_notifications.php');

// Set JSON header
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$adminNotifications = new AdminNotificationSystem($conn, $admin_id);

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'unread_count':
                        $count = $adminNotifications->getUnreadCount();
                        echo json_encode([
                            'success' => true,
                            'unread_count' => $count
                        ]);
                        break;
                        
                    case 'list':
                        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
                        $notifications = $adminNotifications->getAdminNotifications($limit);
                        echo json_encode([
                            'success' => true,
                            'notifications' => $notifications,
                            'unread_count' => $adminNotifications->getUnreadCount()
                        ]);
                        break;
                        
                    case 'stats':
                        $stats = $adminNotifications->getNotificationStats();
                        echo json_encode([
                            'success' => true,
                            'stats' => $stats
                        ]);
                        break;
                        
                    default:
                        // Default: return notifications list
                        $notifications = $adminNotifications->getAdminNotifications(10);
                        echo json_encode([
                            'success' => true,
                            'notifications' => $notifications,
                            'unread_count' => $adminNotifications->getUnreadCount()
                        ]);
                        break;
                }
            } else {
                // Default: return notifications list
                $notifications = $adminNotifications->getAdminNotifications(10);
                echo json_encode([
                    'success' => true,
                    'notifications' => $notifications,
                    'unread_count' => $adminNotifications->getUnreadCount()
                ]);
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (isset($input['action'])) {
                switch ($input['action']) {
                    case 'mark_read':
                        if (isset($input['notification_id'])) {
                            $success = $adminNotifications->markAsRead($input['notification_id']);
                            echo json_encode([
                                'success' => $success,
                                'message' => $success ? 'Notification marked as read' : 'Failed to mark notification as read'
                            ]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Notification ID required']);
                        }
                        break;
                        
                    case 'mark_all_read':
                        $success = $adminNotifications->markAllAsRead();
                        echo json_encode([
                            'success' => $success,
                            'message' => $success ? 'All notifications marked as read' : 'Failed to mark notifications as read'
                        ]);
                        break;
                        
                    case 'send_system_notification':
                        if (isset($input['message'])) {
                            $success = $adminNotifications->notifySystemEvent($input['message'], $input['priority'] ?? 'normal');
                            echo json_encode([
                                'success' => $success,
                                'message' => $success ? 'System notification sent' : 'Failed to send system notification'
                            ]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Message required']);
                        }
                        break;
                        
                    default:
                        echo json_encode(['success' => false, 'message' => 'Invalid action']);
                        break;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Action required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?> 