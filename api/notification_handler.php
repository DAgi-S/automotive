<?php
require_once('../db_conn.php');

class NotificationHandler {
    private $conn;
    
    // Define notification types
    const TYPE_SERVICE_BOOKING = 'service_booking';
    const TYPE_APPOINTMENT_UPDATE = 'appointment_update';
    const TYPE_APPOINTMENT_CANCEL = 'appointment_cancel';
    const TYPE_WORKER_ASSIGNED = 'worker_assigned';
    const TYPE_SERVICE_COMPLETE = 'service_complete';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function createServiceBookingNotification($appointment_id, $user_id, $services) {
        try {
            // Get user details
            $user_stmt = $this->conn->prepare("SELECT name FROM tbl_user WHERE id = ?");
            $user_stmt->bind_param("i", $user_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            $user = $user_result->fetch_assoc();
            
            $message = sprintf(
                "New service booking from %s. Appointment ID: %d. Services: %s",
                $user['name'],
                $appointment_id,
                is_string($services) ? $services : json_encode($services)
            );
            
            return $this->createNotification(self::TYPE_SERVICE_BOOKING, $appointment_id, $message);
        } catch (Exception $e) {
            error_log("Error creating service booking notification: " . $e->getMessage());
            return false;
        }
    }

    public function createAppointmentUpdateNotification($appointment_id, $user_id, $status, $details = '') {
        try {
            // Get user and appointment details
            $stmt = $this->conn->prepare(
                "SELECT u.name, a.appointment_date, a.appointment_time 
                 FROM tbl_appointments a 
                 JOIN tbl_user u ON a.user_id = u.id 
                 WHERE a.appointment_id = ?"
            );
            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            
            $message = sprintf(
                "Appointment update for %s on %s at %s - Status: %s. %s",
                $data['name'],
                $data['appointment_date'],
                $data['appointment_time'],
                strtoupper($status),
                $details
            );
            
            return $this->createNotification(self::TYPE_APPOINTMENT_UPDATE, $appointment_id, $message);
        } catch (Exception $e) {
            error_log("Error creating appointment update notification: " . $e->getMessage());
            return false;
        }
    }

    public function createAppointmentCancelNotification($appointment_id, $user_id, $reason = '') {
        try {
            // Get user and appointment details
            $stmt = $this->conn->prepare(
                "SELECT u.name, a.appointment_date, a.appointment_time 
                 FROM tbl_appointments a 
                 JOIN tbl_user u ON a.user_id = u.id 
                 WHERE a.appointment_id = ?"
            );
            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            
            $message = sprintf(
                "Appointment cancelled by %s for %s at %s. %s",
                $data['name'],
                $data['appointment_date'],
                $data['appointment_time'],
                $reason ? "Reason: " . $reason : ""
            );
            
            return $this->createNotification(self::TYPE_APPOINTMENT_CANCEL, $appointment_id, $message);
        } catch (Exception $e) {
            error_log("Error creating appointment cancellation notification: " . $e->getMessage());
            return false;
        }
    }

    public function createWorkerAssignmentNotification($appointment_id, $worker_id) {
        try {
            // Get worker and appointment details
            $stmt = $this->conn->prepare(
                "SELECT w.full_name, a.appointment_date, a.appointment_time, u.name as customer_name
                 FROM tbl_worker w
                 JOIN tbl_worker_assignments wa ON w.id = wa.worker_id
                 JOIN tbl_appointments a ON wa.appointment_id = a.appointment_id
                 JOIN tbl_user u ON a.user_id = u.id
                 WHERE a.appointment_id = ? AND w.id = ?"
            );
            $stmt->bind_param("ii", $appointment_id, $worker_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            
            $message = sprintf(
                "Worker %s assigned to appointment for %s on %s at %s",
                $data['full_name'],
                $data['customer_name'],
                $data['appointment_date'],
                $data['appointment_time']
            );
            
            return $this->createNotification(self::TYPE_WORKER_ASSIGNED, $appointment_id, $message);
        } catch (Exception $e) {
            error_log("Error creating worker assignment notification: " . $e->getMessage());
            return false;
        }
    }

    public function createServiceCompleteNotification($appointment_id) {
        try {
            // Get appointment details
            $stmt = $this->conn->prepare(
                "SELECT u.name, a.appointment_date, a.appointment_time
                 FROM tbl_appointments a
                 JOIN tbl_user u ON a.user_id = u.id
                 WHERE a.appointment_id = ?"
            );
            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            
            $message = sprintf(
                "Service completed for customer %s's appointment on %s at %s",
                $data['name'],
                $data['appointment_date'],
                $data['appointment_time']
            );
            
            return $this->createNotification(self::TYPE_SERVICE_COMPLETE, $appointment_id, $message);
        } catch (Exception $e) {
            error_log("Error creating service completion notification: " . $e->getMessage());
            return false;
        }
    }

    private function createNotification($type, $reference_id, $message) {
        try {
            // Get all admin IDs and their preferences
            $admin_stmt = $this->conn->prepare(
                "SELECT a.id, np.is_enabled, np.email_enabled, np.sound_enabled 
                 FROM tbl_admin a 
                 LEFT JOIN tbl_notification_preferences np 
                 ON a.id = np.admin_id AND np.notification_type = ?"
            );
            $admin_stmt->bind_param("s", $type);
            $admin_stmt->execute();
            $admin_result = $admin_stmt->get_result();
            
            // Insert notification for each admin based on their preferences
            $insert_stmt = $this->conn->prepare(
                "INSERT INTO tbl_notifications (type, reference_id, message, recipient_id, is_read) 
                 VALUES (?, ?, ?, ?, FALSE)"
            );
            
            $success = true;
            while ($admin = $admin_result->fetch_assoc()) {
                // If no preference exists or notification is enabled
                if (!isset($admin['is_enabled']) || $admin['is_enabled']) {
                    $insert_stmt->bind_param("sisi", $type, $reference_id, $message, $admin['id']);
                    if (!$insert_stmt->execute()) {
                        $success = false;
                    }
                    
                    // Send email if enabled
                    if (isset($admin['email_enabled']) && $admin['email_enabled']) {
                        $this->sendEmailNotification($admin['id'], $type, $message);
                    }
                }
            }
            
            if ($success && $type === self::TYPE_SERVICE_BOOKING) {
                // Update appointment notification status
                $update_stmt = $this->conn->prepare(
                    "UPDATE tbl_appointments SET notification_sent = TRUE WHERE appointment_id = ?"
                );
                $update_stmt->bind_param("i", $reference_id);
                $update_stmt->execute();
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("Error creating notification: " . $e->getMessage());
            return false;
        }
    }
    
    private function sendEmailNotification($admin_id, $type, $message) {
        try {
            // Get admin email
            $stmt = $this->conn->prepare("SELECT email FROM tbl_admin WHERE id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
            
            if ($admin && $admin['email']) {
                $subject = "Nati Automotive - " . ucwords(str_replace('_', ' ', $type));
                $headers = "From: noreply@natiautomotive.com\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                
                $emailBody = $this->getEmailTemplate($type, $message);
                
                mail($admin['email'], $subject, $emailBody, $headers);
            }
        } catch (Exception $e) {
            error_log("Error sending email notification: " . $e->getMessage());
        }
    }
    
    private function getEmailTemplate($type, $message) {
        $typeLabel = ucwords(str_replace('_', ' ', $type));
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4e73df; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fc; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>$typeLabel</h2>
                </div>
                <div class='content'>
                    <p>$message</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message from Nati Automotive. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    public function getAdminNotifications($admin_id, $limit = 10) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM tbl_notifications 
                 WHERE recipient_id = ? 
                 ORDER BY created_at DESC 
                 LIMIT ?"
            );
            $stmt->bind_param("ii", $admin_id, $limit);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $notifications = [];
            
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
            
            return $notifications;
            
        } catch (Exception $e) {
            error_log("Error fetching notifications: " . $e->getMessage());
            return [];
        }
    }
    
    public function markNotificationAsRead($notification_id, $admin_id) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE tbl_notifications 
                 SET is_read = TRUE 
                 WHERE id = ? AND recipient_id = ?"
            );
            $stmt->bind_param("ii", $notification_id, $admin_id);
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUnreadCount($admin_id) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as count 
                 FROM tbl_notifications 
                 WHERE recipient_id = ? AND is_read = FALSE"
            );
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row['count'];
            
        } catch (Exception $e) {
            error_log("Error getting unread count: " . $e->getMessage());
            return 0;
        }
    }
}

// Create API endpoint for fetching notifications
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    session_start();
    
    if (!isset($_SESSION['admin_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
    
    $handler = new NotificationHandler($conn);
    $admin_id = $_SESSION['admin_id'];
    
    switch ($_GET['action']) {
        case 'get_notifications':
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
            $limit = 10;
            $offset = ($page - 1) * $limit;
            
            $notifications = $handler->getAdminNotifications($admin_id, $limit, $offset, $filter);
            echo json_encode([
                'success' => true,
                'notifications' => $notifications['items'],
                'has_more' => $notifications['has_more']
            ]);
            break;
            
        case 'get_unread_count':
            $count = $handler->getUnreadCount($admin_id);
            echo json_encode(['success' => true, 'count' => $count]);
            break;
            
        case 'mark_read':
            if (!isset($_GET['notification_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing notification ID']);
                break;
            }
            $success = $handler->markNotificationAsRead($_GET['notification_id'], $admin_id);
            echo json_encode(['success' => $success]);
            break;
            
        case 'mark_all_read':
            $success = $handler->markAllAsRead($admin_id);
            echo json_encode(['success' => $success]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    exit;
}
?> 