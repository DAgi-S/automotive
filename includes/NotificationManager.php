<?php
/**
 * Notification Manager Class
 * Handles all notification types: Telegram, Email, Web App
 */

class NotificationManager {
    private $pdo;
    private $config;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loadConfig();
    }
    
    /**
     * Load notification configuration from database
     */
    private function loadConfig() {
        try {
            $stmt = $this->pdo->prepare("SELECT config_key, config_value FROM notification_config WHERE is_active = 1");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->config = [];
            foreach ($results as $row) {
                $this->config[$row['config_key']] = $row['config_value'];
            }
        } catch (Exception $e) {
            error_log("Failed to load notification config: " . $e->getMessage());
            $this->config = [];
        }
    }
    
    /**
     * Send notification via multiple channels
     */
    public function sendNotification($type, $recipient_id, $data, $methods = ['web']) {
        $notification_id = $this->createNotification($type, $recipient_id, $data);
        
        if (!$notification_id) {
            return false;
        }
        
        $results = [];
        foreach ($methods as $method) {
            switch ($method) {
                case 'telegram':
                    $results['telegram'] = $this->sendTelegramNotification($notification_id, $data);
                    break;
                case 'email':
                    $results['email'] = $this->sendEmailNotification($notification_id, $data);
                    break;
                case 'web':
                    $results['web'] = $this->sendWebNotification($notification_id, $data);
                    break;
            }
        }
        
        return $results;
    }
    
    /**
     * Create notification record in database
     */
    private function createNotification($type, $recipient_id, $data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO tbl_notifications (type, recipient_id, message, reference_id, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $type,
                $recipient_id,
                $data['message'] ?? '',
                $data['reference_id'] ?? null
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("Failed to create notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send Telegram notification
     */
    private function sendTelegramNotification($notification_id, $data) {
        if (!$this->isConfigEnabled('telegram_enabled')) {
            return ['success' => false, 'message' => 'Telegram notifications disabled'];
        }
        
        $bot_token = $this->config['telegram_bot_token'] ?? '';
        $chat_id = $data['telegram_chat_id'] ?? $this->config['telegram_chat_id'] ?? '';
        
        if (empty($bot_token) || empty($chat_id)) {
            return ['success' => false, 'message' => 'Telegram configuration incomplete'];
        }
        
        $message = $this->formatTelegramMessage($data);
        
        try {
            $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
            $postData = [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $this->updateNotificationStatus($notification_id, 'telegram', 'sent');
                return ['success' => true, 'response' => $response];
            } else {
                $this->updateNotificationStatus($notification_id, 'telegram', 'failed', "HTTP {$httpCode}");
                return ['success' => false, 'message' => "HTTP {$httpCode}", 'response' => $response];
            }
            
        } catch (Exception $e) {
            $this->updateNotificationStatus($notification_id, 'telegram', 'failed', $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Send Email notification
     */
    private function sendEmailNotification($notification_id, $data) {
        if (!$this->isConfigEnabled('email_enabled')) {
            return ['success' => false, 'message' => 'Email notifications disabled'];
        }
        
        $smtp_host = $this->config['smtp_host'] ?? '';
        $smtp_port = $this->config['smtp_port'] ?? 587;
        $smtp_username = $this->config['smtp_username'] ?? '';
        $smtp_password = $this->config['smtp_password'] ?? '';
        $from_email = $this->config['email_from_address'] ?? '';
        $from_name = $this->config['email_from_name'] ?? 'Nati Automotive';
        
        if (empty($smtp_host) || empty($smtp_username) || empty($smtp_password)) {
            return ['success' => false, 'message' => 'Email configuration incomplete'];
        }
        
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_username;
            $mail->Password = $smtp_password;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $smtp_port;
            
            // Recipients
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($data['email'], $data['name'] ?? '');
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $data['subject'] ?? 'Notification from Nati Automotive';
            $mail->Body = $this->formatEmailMessage($data);
            
            $mail->send();
            $this->updateNotificationStatus($notification_id, 'email', 'sent');
            return ['success' => true, 'message' => 'Email sent successfully'];
            
        } catch (Exception $e) {
            $this->updateNotificationStatus($notification_id, 'email', 'failed', $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Send Web notification (store in database for web app)
     */
    private function sendWebNotification($notification_id, $data) {
        try {
            $this->updateNotificationStatus($notification_id, 'web', 'sent');
            return ['success' => true, 'message' => 'Web notification stored'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Format message for Telegram
     */
    private function formatTelegramMessage($data) {
        $template = $this->getTemplate($data['template_key'] ?? '', 'telegram');
        if ($template) {
            return $this->replaceTemplateVariables($template['message_template'], $data);
        }
        
        // Default format
        return $data['message'] ?? 'New notification from Nati Automotive';
    }
    
    /**
     * Format message for Email
     */
    private function formatEmailMessage($data) {
        $template = $this->getTemplate($data['template_key'] ?? '', 'email');
        if ($template) {
            return $this->replaceTemplateVariables($template['message_template'], $data);
        }
        
        // Default format
        return $data['message'] ?? 'New notification from Nati Automotive';
    }
    
    /**
     * Get notification template
     */
    private function getTemplate($template_key, $type) {
        if (empty($template_key)) {
            return null;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM notification_templates 
                WHERE template_key = ? AND template_type = ? AND is_active = 1
            ");
            $stmt->execute([$template_key, $type]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to get template: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Replace template variables
     */
    private function replaceTemplateVariables($template, $data) {
        $message = $template;
        
        // Replace variables in format {{variable_name}}
        foreach ($data as $key => $value) {
            $message = str_replace('{{' . $key . '}}', $value, $message);
        }
        
        return $message;
    }
    
    /**
     * Update notification delivery status
     */
    private function updateNotificationStatus($notification_id, $method, $status, $error = null) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE tbl_notifications 
                SET notification_method = ?, delivery_status = ?, sent_at = NOW(), error_message = ?
                WHERE id = ?
            ");
            $stmt->execute([$method, $status, $error, $notification_id]);
        } catch (Exception $e) {
            error_log("Failed to update notification status: " . $e->getMessage());
        }
    }
    
    /**
     * Check if notification method is enabled
     */
    private function isConfigEnabled($key) {
        return isset($this->config[$key]) && $this->config[$key] == '1';
    }
    
    /**
     * Get user's notification preferences
     */
    public function getUserNotificationPreferences($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT notification_preferences 
                FROM tbl_user 
                WHERE id = ?
            ");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['notification_preferences']) {
                return json_decode($result['notification_preferences'], true);
            }
            
            // Default preferences
            return [
                'email' => true,
                'telegram' => false,
                'web' => true
            ];
        } catch (Exception $e) {
            error_log("Failed to get user preferences: " . $e->getMessage());
            return ['email' => true, 'telegram' => false, 'web' => true];
        }
    }
    
    /**
     * Send appointment notification
     */
    public function sendAppointmentNotification($appointment_data, $type = 'created') {
        $user_id = $appointment_data['user_id'];
        $preferences = $this->getUserNotificationPreferences($user_id);
        
        $methods = [];
        if ($preferences['web']) $methods[] = 'web';
        if ($preferences['email']) $methods[] = 'email';
        if ($preferences['telegram']) $methods[] = 'telegram';
        
        $notification_data = [
            'template_key' => "appointment_{$type}_" . (in_array('telegram', $methods) ? 'telegram' : 'email'),
            'customer_name' => $appointment_data['customer_name'],
            'appointment_date' => $appointment_data['appointment_date'],
            'appointment_time' => $appointment_data['appointment_time'],
            'service_name' => $appointment_data['service_name'],
            'vehicle_info' => $appointment_data['vehicle_info'],
            'email' => $appointment_data['email'],
            'subject' => 'Appointment ' . ucfirst($type),
            'message' => "Your appointment has been {$type}",
            'reference_id' => $appointment_data['appointment_id']
        ];
        
        return $this->sendNotification('appointment', $user_id, $notification_data, $methods);
    }
    
    /**
     * Send admin notification
     */
    public function sendAdminNotification($data, $type) {
        $admin_methods = ['telegram', 'web']; // Admins typically get Telegram + Web
        
        $notification_data = [
            'template_key' => "admin_{$type}",
            'message' => $data['message'],
            'telegram_chat_id' => $this->config['telegram_chat_id'] ?? ''
        ];
        
        // Merge additional data
        $notification_data = array_merge($notification_data, $data);
        
        return $this->sendNotification($type, 1, $notification_data, $admin_methods); // Admin ID = 1
    }
    
    /**
     * Get unread notifications for user
     */
    public function getUnreadNotifications($user_id, $limit = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM tbl_notifications 
                WHERE recipient_id = ? AND is_read = 0 
                ORDER BY created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$user_id, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to get notifications: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notification_id, $user_id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE tbl_notifications 
                SET is_read = 1 
                WHERE id = ? AND recipient_id = ?
            ");
            return $stmt->execute([$notification_id, $user_id]);
        } catch (Exception $e) {
            error_log("Failed to mark notification as read: " . $e->getMessage());
            return false;
        }
    }
} 