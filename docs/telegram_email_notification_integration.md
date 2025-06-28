# Telegram Bot & Email Notification Integration

## Overview

This document outlines the integration of Telegram bot and email notifications for the Nati Automotive system. The system automatically sends notifications when specific events occur, ensuring administrators and relevant parties are promptly informed of important activities.

## Notification Events

### 1. New Service Created (`services.php`)
- **Trigger**: When an admin creates a new service in the admin panel
- **Recipients**: Admin users, service managers
- **Channels**: Telegram + Email
- **Information Included**:
  - Service name and description
  - Service price and duration
  - Creation timestamp
  - Created by (admin name)

### 2. New Appointment Created (`appointments.php`)
- **Trigger**: When a customer books a new appointment
- **Recipients**: Admin users, service coordinators
- **Channels**: Telegram + Email
- **Information Included**:
  - Customer details (name, phone, email)
  - Service requested
  - Appointment date and time
  - Booking timestamp

### 3. Vehicle Insurance & Document Expiry Reminders
- **Trigger**: 10 days before expiry date
- **Recipients**: Vehicle owners, admin users
- **Channels**: Telegram + Email
- **Information Included**:
  - Vehicle details (make, model, plate number)
  - Document type (insurance, registration, etc.)
  - Expiry date
  - Days remaining
  - Renewal instructions

## Implementation Architecture

### Database Schema Requirements

#### Notification Templates Table
```sql
CREATE TABLE notification_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_key VARCHAR(100) UNIQUE NOT NULL,
    template_name VARCHAR(255) NOT NULL,
    email_subject VARCHAR(255),
    email_body TEXT,
    telegram_message TEXT,
    variables JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Vehicle Documents Table
```sql
CREATE TABLE vehicle_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vehicle_id INT NOT NULL,
    document_type ENUM('insurance', 'registration', 'inspection', 'license') NOT NULL,
    document_number VARCHAR(100),
    issue_date DATE,
    expiry_date DATE NOT NULL,
    reminder_sent BOOLEAN DEFAULT FALSE,
    last_reminder_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES tbl_vehicles(id) ON DELETE CASCADE
);
```

#### Notification Log Table
```sql
CREATE TABLE notification_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    notification_type VARCHAR(50) NOT NULL,
    recipient_type ENUM('admin', 'customer', 'system') NOT NULL,
    recipient_id INT,
    channel ENUM('email', 'telegram', 'web') NOT NULL,
    status ENUM('pending', 'sent', 'failed', 'delivered') DEFAULT 'pending',
    message_content TEXT,
    error_message TEXT,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Code Implementation

### 1. Service Creation Notification (`admin/services.php`)

#### Modified Service Creation Handler
```php
case 'add':
    try {
        // Begin transaction
        $conn->beginTransaction();
        
        // Insert new service
        $stmt = $conn->prepare("INSERT INTO tbl_services (service_name, description, price, duration, status, icon_class) VALUES (:name, :description, :price, :duration, 'active', :icon_class)");
        $stmt->execute([
            'name' => $_POST['service_name'],
            'description' => $_POST['description'],
            'price' => floatval($_POST['price']),
            'duration' => $_POST['duration'],
            'icon_class' => $_POST['icon_class']
        ]);
        
        $serviceId = $conn->lastInsertId();
        
        // Send notifications
        require_once('../includes/NotificationManager.php');
        $notificationManager = new NotificationManager($conn);
        
        // Get admin details
        $adminStmt = $conn->prepare("SELECT name, email FROM admin WHERE admin_id = ?");
        $adminStmt->execute([$_SESSION['admin_id']]);
        $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);
        
        // Prepare notification data
        $notificationData = [
            'template_key' => 'new_service_created',
            'variables' => [
                'service_name' => $_POST['service_name'],
                'service_description' => $_POST['description'],
                'service_price' => number_format($_POST['price'], 2),
                'service_duration' => $_POST['duration'],
                'admin_name' => $admin['name'] ?? 'Admin',
                'creation_date' => date('Y-m-d H:i:s'),
                'service_id' => $serviceId
            ]
        ];
        
        // Send to all admin users
        $adminUsers = $conn->query("SELECT admin_id, name, email FROM admin WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($adminUsers as $adminUser) {
            $notificationManager->sendNotification(
                'service_management',
                $adminUser['admin_id'],
                $notificationData,
                ['telegram', 'email', 'web']
            );
        }
        
        $conn->commit();
        $_SESSION['success'] = "Service added successfully and notifications sent!";
        
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Failed to add service: " . $e->getMessage();
    }
    break;
```

### 2. Appointment Creation Notification (`admin/appointments.php`)

#### New Appointment Handler
```php
// In appointment creation process (usually from customer booking)
function createAppointmentWithNotification($conn, $appointmentData) {
    try {
        $conn->beginTransaction();
        
        // Insert appointment
        $stmt = $conn->prepare("
            INSERT INTO tbl_appointments (user_id, service_id, appointment_date, appointment_time, notes, status) 
            VALUES (:user_id, :service_id, :appointment_date, :appointment_time, :notes, 'pending')
        ");
        $stmt->execute($appointmentData);
        
        $appointmentId = $conn->lastInsertId();
        
        // Get appointment details with related data
        $stmt = $conn->prepare("
            SELECT 
                a.*,
                u.name as customer_name,
                u.email as customer_email,
                u.phonenum as customer_phone,
                s.service_name,
                s.price,
                s.duration
            FROM tbl_appointments a
            JOIN tbl_user u ON a.user_id = u.id
            JOIN tbl_services s ON a.service_id = s.service_id
            WHERE a.appointment_id = ?
        ");
        $stmt->execute([$appointmentId]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Send notifications
        require_once('../includes/NotificationManager.php');
        $notificationManager = new NotificationManager($conn);
        
        // Notification data
        $notificationData = [
            'template_key' => 'new_appointment_created',
            'variables' => [
                'customer_name' => $appointment['customer_name'],
                'customer_email' => $appointment['customer_email'],
                'customer_phone' => $appointment['customer_phone'],
                'service_name' => $appointment['service_name'],
                'appointment_date' => date('M d, Y', strtotime($appointment['appointment_date'])),
                'appointment_time' => date('h:i A', strtotime($appointment['appointment_time'])),
                'service_price' => number_format($appointment['price'], 2),
                'service_duration' => $appointment['duration'],
                'booking_date' => date('Y-m-d H:i:s'),
                'appointment_id' => $appointmentId
            ]
        ];
        
        // Send to admin users
        $adminUsers = $conn->query("SELECT admin_id FROM admin WHERE status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($adminUsers as $adminId) {
            $notificationManager->sendNotification(
                'appointment_management',
                $adminId,
                $notificationData,
                ['telegram', 'email', 'web']
            );
        }
        
        // Send confirmation to customer
        $customerNotificationData = [
            'template_key' => 'appointment_confirmation',
            'variables' => $notificationData['variables']
        ];
        
        $notificationManager->sendNotification(
            'appointment_confirmation',
            $appointment['user_id'],
            $customerNotificationData,
            ['email', 'web']
        );
        
        $conn->commit();
        return ['success' => true, 'appointment_id' => $appointmentId];
        
    } catch (Exception $e) {
        $conn->rollBack();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
```

### 3. Vehicle Document Expiry Reminder System

#### Cron Job Script (`cron/check_document_expiry.php`)
```php
<?php
require_once('../config/database.php');
require_once('../includes/NotificationManager.php');

// Check for documents expiring in 10 days
$expiryDate = date('Y-m-d', strtotime('+10 days'));

$stmt = $conn->prepare("
    SELECT 
        vd.*,
        v.car_brand,
        v.car_model,
        v.car_year,
        v.plate_number,
        u.id as user_id,
        u.name as owner_name,
        u.email as owner_email,
        u.phonenum as owner_phone
    FROM vehicle_documents vd
    JOIN tbl_vehicles v ON vd.vehicle_id = v.id
    JOIN tbl_user u ON v.user_id = u.id
    WHERE vd.expiry_date = ? 
    AND vd.reminder_sent = FALSE
    AND vd.expiry_date > CURDATE()
");

$stmt->execute([$expiryDate]);
$expiringDocuments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$notificationManager = new NotificationManager($conn);

foreach ($expiringDocuments as $document) {
    $notificationData = [
        'template_key' => 'document_expiry_reminder',
        'variables' => [
            'owner_name' => $document['owner_name'],
            'vehicle_info' => "{$document['car_year']} {$document['car_brand']} {$document['car_model']}",
            'plate_number' => $document['plate_number'],
            'document_type' => ucfirst($document['document_type']),
            'document_number' => $document['document_number'],
            'expiry_date' => date('M d, Y', strtotime($document['expiry_date'])),
            'days_remaining' => '10',
            'renewal_instructions' => getDocumentRenewalInstructions($document['document_type'])
        ]
    ];
    
    // Send to vehicle owner
    $notificationManager->sendNotification(
        'document_expiry',
        $document['user_id'],
        $notificationData,
        ['email', 'telegram']
    );
    
    // Send to admin for tracking
    $adminNotificationData = [
        'template_key' => 'admin_document_expiry_alert',
        'variables' => array_merge($notificationData['variables'], [
            'customer_phone' => $document['owner_phone'],
            'customer_email' => $document['owner_email']
        ])
    ];
    
    $adminUsers = $conn->query("SELECT admin_id FROM admin WHERE status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($adminUsers as $adminId) {
        $notificationManager->sendNotification(
            'admin_document_alert',
            $adminId,
            $adminNotificationData,
            ['telegram', 'web']
        );
    }
    
    // Mark reminder as sent
    $updateStmt = $conn->prepare("
        UPDATE vehicle_documents 
        SET reminder_sent = TRUE, last_reminder_date = CURDATE() 
        WHERE id = ?
    ");
    $updateStmt->execute([$document['id']]);
}

function getDocumentRenewalInstructions($documentType) {
    $instructions = [
        'insurance' => 'Contact your insurance provider or visit their office to renew your vehicle insurance policy.',
        'registration' => 'Visit the nearest transport authority office with required documents to renew registration.',
        'inspection' => 'Schedule a vehicle inspection at an authorized inspection center.',
        'license' => 'Visit the licensing authority office with required documents to renew your driving license.'
    ];
    
    return $instructions[$documentType] ?? 'Contact the relevant authority for renewal procedures.';
}

echo "Document expiry check completed. Processed " . count($expiringDocuments) . " expiring documents.\n";
?>
```

## Notification Templates

### 1. New Service Created Template
```json
{
    "template_key": "new_service_created",
    "email_subject": "🔧 New Service Added - {{service_name}}",
    "email_body": "Dear {{admin_name}},\n\nA new service has been added to the system:\n\n**Service Details:**\n- Name: {{service_name}}\n- Description: {{service_description}}\n- Price: ETB {{service_price}}\n- Duration: {{service_duration}}\n- Created by: {{admin_name}}\n- Date: {{creation_date}}\n\nService ID: #{{service_id}}\n\nBest regards,\nNati Automotive System",
    "telegram_message": "🔧 **New Service Added**\n\n**{{service_name}}**\n💰 Price: ETB {{service_price}}\n⏱️ Duration: {{service_duration}}\n👤 Added by: {{admin_name}}\n📅 {{creation_date}}\n\n📝 {{service_description}}"
}
```

### 2. New Appointment Template
```json
{
    "template_key": "new_appointment_created",
    "email_subject": "📅 New Appointment Booked - {{customer_name}}",
    "email_body": "Dear Team,\n\nA new appointment has been booked:\n\n**Customer Information:**\n- Name: {{customer_name}}\n- Phone: {{customer_phone}}\n- Email: {{customer_email}}\n\n**Appointment Details:**\n- Service: {{service_name}}\n- Date: {{appointment_date}}\n- Time: {{appointment_time}}\n- Price: ETB {{service_price}}\n- Duration: {{service_duration}}\n\nAppointment ID: #{{appointment_id}}\nBooked on: {{booking_date}}\n\nPlease prepare accordingly.\n\nBest regards,\nNati Automotive System",
    "telegram_message": "📅 **New Appointment**\n\n👤 **{{customer_name}}**\n📞 {{customer_phone}}\n\n🔧 **{{service_name}}**\n📅 {{appointment_date}} at {{appointment_time}}\n💰 ETB {{service_price}}\n⏱️ {{service_duration}}\n\n🆔 #{{appointment_id}}"
}
```

### 3. Document Expiry Reminder Template
```json
{
    "template_key": "document_expiry_reminder",
    "email_subject": "⚠️ Document Expiry Reminder - {{document_type}}",
    "email_body": "Dear {{owner_name}},\n\nThis is a reminder that your {{document_type}} is expiring soon:\n\n**Vehicle Information:**\n- Vehicle: {{vehicle_info}}\n- Plate Number: {{plate_number}}\n\n**Document Details:**\n- Type: {{document_type}}\n- Number: {{document_number}}\n- Expiry Date: {{expiry_date}}\n- Days Remaining: {{days_remaining}}\n\n**Renewal Instructions:**\n{{renewal_instructions}}\n\nPlease renew your {{document_type}} before the expiry date to avoid any inconvenience.\n\nBest regards,\nNati Automotive Team",
    "telegram_message": "⚠️ **Document Expiry Alert**\n\n👤 {{owner_name}}\n🚗 {{vehicle_info}} ({{plate_number}})\n\n📄 **{{document_type}}**\n🆔 {{document_number}}\n📅 Expires: {{expiry_date}}\n⏰ {{days_remaining}} days remaining\n\n🔄 Please renew before expiry!"
}
```

## Configuration Setup

### 1. Telegram Bot Configuration
```php
// In admin/settings.php - Telegram Settings Tab
$telegram_settings = [
    'telegram_bot_token' => 'YOUR_BOT_TOKEN',
    'telegram_chat_id' => 'YOUR_CHAT_ID',
    'telegram_enabled' => true,
    'telegram_notifications' => ['appointments', 'services', 'document_expiry', 'system']
];
```

### 2. Email Configuration
```php
// In admin/settings.php - Email Settings Tab
$email_settings = [
    'smtp_host' => 'mail.yourdomain.com',
    'smtp_port' => 587,
    'smtp_username' => 'noreply@yourdomain.com',
    'smtp_password' => 'your_password',
    'smtp_encryption' => 'tls',
    'from_email' => 'noreply@yourdomain.com',
    'from_name' => 'Nati Automotive'
];
```

## Cron Job Setup

### Linux/Unix Crontab Entry
```bash
# Check document expiry daily at 9:00 AM
0 9 * * * /usr/bin/php /path/to/your/project/cron/check_document_expiry.php

# Alternative: Check twice daily (9 AM and 6 PM)
0 9,18 * * * /usr/bin/php /path/to/your/project/cron/check_document_expiry.php
```

### Windows Task Scheduler
```cmd
# Command to run
php.exe "C:\xampp\htdocs\Automotive\cron\check_document_expiry.php"

# Schedule: Daily at 9:00 AM
```

## Testing & Verification

### 1. Test Service Creation Notification
```php
// Create a test service in admin panel
// Verify notifications are sent to:
// - Telegram channel/group
// - Admin email addresses
// - Web notifications in admin panel
```

### 2. Test Appointment Notification
```php
// Book a test appointment
// Verify notifications are sent to:
// - Admin Telegram
// - Admin email
// - Customer email (confirmation)
// - Web notifications
```

### 3. Test Document Expiry
```php
// Insert test data with expiry date = today + 10 days
// Run cron job manually
// Verify notifications are sent to vehicle owner and admin
```

## Troubleshooting

### Common Issues
1. **Telegram notifications not working**:
   - Verify bot token is correct
   - Check chat ID is valid
   - Ensure bot is added to group/channel
   - Test connection using admin panel

2. **Email notifications not sending**:
   - Verify SMTP settings
   - Check email credentials
   - Test email configuration
   - Review server mail logs

3. **Cron job not running**:
   - Verify cron job syntax
   - Check file permissions
   - Review cron logs
   - Test script manually

### Logging & Monitoring
- All notifications are logged in `notification_log` table
- Check notification status and error messages
- Monitor delivery rates and failed notifications
- Set up alerts for notification system failures

## Security Considerations

1. **API Keys Protection**:
   - Store Telegram bot token securely
   - Use environment variables for sensitive data
   - Implement proper access controls

2. **Data Privacy**:
   - Encrypt sensitive customer information
   - Follow GDPR/data protection guidelines
   - Implement opt-out mechanisms

3. **Rate Limiting**:
   - Implement notification rate limits
   - Prevent spam and abuse
   - Monitor API usage

## Future Enhancements

1. **SMS Integration**: Add SMS notifications for critical alerts
2. **Push Notifications**: Implement mobile app push notifications
3. **Notification Preferences**: Allow users to customize notification settings
4. **Advanced Templates**: Rich HTML email templates with branding
5. **Analytics**: Notification delivery analytics and reporting
6. **Multi-language**: Support for multiple languages in notifications

---

*Last Updated: 2025-01-21*
*Version: 1.0*
*Author: agent_website* 