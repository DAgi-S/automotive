<?php
/**
 * Admin Notification System Integration
 * This file provides notification functionality for admin users
 */

require_once(__DIR__ . '/../../includes/NotificationManager.php');

class AdminNotificationSystem {
    private $pdo;
    private $notificationManager;
    private $admin_id;
    
    public function __construct($pdo, $admin_id) {
        $this->pdo = $pdo;
        $this->admin_id = $admin_id;
        $this->notificationManager = new NotificationManager($pdo);
    }
    
    /**
     * Get admin notifications
     */
    public function getAdminNotifications($limit = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT n.*, 
                       DATE_FORMAT(n.created_at, '%M %d, %Y at %h:%i %p') as formatted_date,
                       CASE 
                           WHEN n.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN 'just now'
                           WHEN n.created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY) THEN 'today'
                           WHEN n.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 'this week'
                           ELSE 'older'
                       END as time_category
                FROM tbl_notifications n
                WHERE n.recipient_id = ? OR n.type = 'system'
                ORDER BY n.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$this->admin_id, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting admin notifications: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get unread notification count for admin
     */
    public function getUnreadCount() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as unread_count
                FROM tbl_notifications n
                WHERE (n.recipient_id = ? OR n.type = 'system') 
                AND n.is_read = 0
            ");
            $stmt->execute([$this->admin_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['unread_count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting unread count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Send notification to all admins
     */
    public function notifyAllAdmins($message, $type = 'system') {
        try {
            // Get all admin users
            $stmt = $this->pdo->prepare("SELECT id, email, name FROM tbl_admin WHERE status = 'active'");
            $stmt->execute();
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($admins as $admin) {
                $notification_data = [
                    'type' => $type,
                    'recipient_id' => $admin['id'],
                    'message' => $message,
                    'methods' => ['web'],
                    'email' => $admin['email'],
                    'name' => $admin['name'],
                    'subject' => 'Admin System Notification'
                ];
                
                $this->notificationManager->sendNotification($notification_data, $admin['email'], 'Admin System Notification');

            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error notifying all admins: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send notification about new appointment
     */
    public function notifyNewAppointment($appointment_data) {
        $message = "New appointment scheduled: {$appointment_data['customer_name']} for {$appointment_data['service_name']} on {$appointment_data['appointment_date']}";
        return $this->notifyAllAdmins($message, 'appointment');
    }
    
    /**
     * Send notification about new order
     */
    public function notifyNewOrder($order_data) {
        $message = "New service order created: Order #{$order_data['order_id']} for {$order_data['customer_name']}'s {$order_data['car_model']}";
        return $this->notifyAllAdmins($message, 'order');
    }
    
    /**
     * Send notification about new customer registration
     */
    public function notifyNewCustomer($customer_data) {
        $message = "New customer registered: {$customer_data['name']} ({$customer_data['email']})";
        return $this->notifyAllAdmins($message, 'customer');
    }
    
    /**
     * Send notification about system events
     */
    public function notifySystemEvent($event_message, $priority = 'normal') {
        $message = "[SYSTEM] {$event_message}";
        return $this->notifyAllAdmins($message, 'system');
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notification_id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE tbl_notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE id = ? AND (recipient_id = ? OR type = 'system')
            ");
            $stmt->execute([$notification_id, $this->admin_id]);
            return true;
        } catch (Exception $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead() {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE tbl_notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE (recipient_id = ? OR type = 'system') AND is_read = 0
            ");
            $stmt->execute([$this->admin_id]);
            return true;
        } catch (Exception $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get notification statistics for dashboard
     */
    public function getNotificationStats() {
        try {
            $stats = [];
            
            // Today's notifications
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count
                FROM tbl_notifications 
                WHERE (recipient_id = ? OR type = 'system') 
                AND DATE(created_at) = CURDATE()
            ");
            $stmt->execute([$this->admin_id]);
            $stats['today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // This week's notifications
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count
                FROM tbl_notifications 
                WHERE (recipient_id = ? OR type = 'system') 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ");
            $stmt->execute([$this->admin_id]);
            $stats['week'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Unread notifications
            $stats['unread'] = $this->getUnreadCount();
            
            // Notifications by type (last 30 days)
            $stmt = $this->pdo->prepare("
                SELECT type, COUNT(*) as count
                FROM tbl_notifications 
                WHERE (recipient_id = ? OR type = 'system') 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY type
            ");
            $stmt->execute([$this->admin_id]);
            $stats['by_type'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (Exception $e) {
            error_log("Error getting notification stats: " . $e->getMessage());
            return [];
        }
    }
}

/**
 * Helper function to get notification icon based on type
 */
function getNotificationIcon($type) {
    switch ($type) {
        case 'appointment':
            return 'fas fa-calendar-alt text-primary';
        case 'order':
        case 'service_order':
            return 'fas fa-wrench text-info';
        case 'customer':
            return 'fas fa-user-plus text-success';
        case 'system':
            return 'fas fa-cog text-warning';
        case 'work_assignment':
            return 'fas fa-tasks text-secondary';
        default:
            return 'fas fa-bell text-primary';
    }
}

/**
 * Helper function to get notification color based on type
 */
function getNotificationColor($type) {
    switch ($type) {
        case 'appointment':
            return 'primary';
        case 'order':
        case 'service_order':
            return 'info';
        case 'customer':
            return 'success';
        case 'system':
            return 'warning';
        case 'work_assignment':
            return 'secondary';
        default:
            return 'light';
    }
}
?> 