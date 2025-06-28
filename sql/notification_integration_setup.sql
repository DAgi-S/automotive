-- ============================================================================
-- Telegram Bot & Email Notification Integration Database Setup
-- ============================================================================
-- This file creates the necessary tables and inserts default data for the
-- notification system integration with services, appointments, and document expiry
-- ============================================================================

-- Create notification templates table
CREATE TABLE IF NOT EXISTS notification_templates (
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

-- Create vehicle documents table for tracking expiry dates
CREATE TABLE IF NOT EXISTS vehicle_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vehicle_id INT NOT NULL,
    document_type ENUM('insurance', 'registration', 'inspection', 'license', 'permit', 'roadworthy') NOT NULL,
    document_number VARCHAR(100),
    issue_date DATE,
    expiry_date DATE NOT NULL,
    reminder_sent BOOLEAN DEFAULT FALSE,
    last_reminder_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES tbl_vehicles(id) ON DELETE CASCADE,
    INDEX idx_expiry_date (expiry_date),
    INDEX idx_reminder_sent (reminder_sent),
    INDEX idx_vehicle_type (vehicle_id, document_type)
);

-- Create notification log table for tracking sent notifications
CREATE TABLE IF NOT EXISTS notification_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    notification_type VARCHAR(50) NOT NULL,
    recipient_type ENUM('admin', 'customer', 'system') NOT NULL,
    recipient_id INT,
    channel ENUM('email', 'telegram', 'web', 'sms') NOT NULL,
    status ENUM('pending', 'sent', 'failed', 'delivered') DEFAULT 'pending',
    message_content TEXT,
    error_message TEXT,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notification_type (notification_type),
    INDEX idx_recipient (recipient_type, recipient_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Create activity log table for admin actions
CREATE TABLE IF NOT EXISTS activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id) ON DELETE CASCADE,
    INDEX idx_admin_id (admin_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created_at (created_at)
);

-- Insert default notification templates
INSERT INTO notification_templates (template_key, template_name, email_subject, email_body, telegram_message, variables) VALUES
-- New Service Created Template
('new_service_created', 'New Service Created Notification', 
'🔧 New Service Added - {{service_name}}',
'Dear {{admin_name}},

A new service has been added to the system:

**Service Details:**
- Name: {{service_name}}
- Description: {{service_description}}
- Price: ETB {{service_price}}
- Duration: {{service_duration}}
- Created by: {{admin_name}}
- Date: {{creation_date}}

Service ID: #{{service_id}}

You can view and manage this service in the admin panel.

Best regards,
Nati Automotive System',
'🔧 **New Service Added**

**{{service_name}}**
💰 Price: ETB {{service_price}}
⏱️ Duration: {{service_duration}}
👤 Added by: {{admin_name}}
📅 {{creation_date}}

📝 {{service_description}}

🆔 Service ID: #{{service_id}}',
'["service_name", "service_description", "service_price", "service_duration", "admin_name", "creation_date", "service_id"]'),

-- New Appointment Created Template
('new_appointment_created', 'New Appointment Booked Notification',
'📅 New Appointment Booked - {{customer_name}}',
'Dear Team,

A new appointment has been booked:

**Customer Information:**
- Name: {{customer_name}}
- Phone: {{customer_phone}}
- Email: {{customer_email}}

**Appointment Details:**
- Service: {{service_name}}
- Date: {{appointment_date}}
- Time: {{appointment_time}}
- Price: ETB {{service_price}}
- Duration: {{service_duration}}

Appointment ID: #{{appointment_id}}
Booked on: {{booking_date}}

Please prepare accordingly and ensure all necessary resources are available.

Best regards,
Nati Automotive System',
'📅 **New Appointment**

👤 **{{customer_name}}**
📞 {{customer_phone}}
✉️ {{customer_email}}

🔧 **{{service_name}}**
📅 {{appointment_date}} at {{appointment_time}}
💰 ETB {{service_price}}
⏱️ {{service_duration}}

🆔 #{{appointment_id}}
📋 Booked: {{booking_date}}',
'["customer_name", "customer_phone", "customer_email", "service_name", "appointment_date", "appointment_time", "service_price", "service_duration", "appointment_id", "booking_date"]'),

-- Document Expiry Reminder Template
('document_expiry_reminder', 'Document Expiry Reminder',
'⚠️ Document Expiry Reminder - {{document_type}}',
'Dear {{owner_name}},

This is a reminder that your {{document_type}} is expiring soon:

**Vehicle Information:**
- Vehicle: {{vehicle_info}}
- Plate Number: {{plate_number}}

**Document Details:**
- Type: {{document_type}}
- Number: {{document_number}}
- Issue Date: {{issue_date}}
- Expiry Date: {{expiry_date}}
- Days Remaining: {{days_remaining}}

**Renewal Instructions:**
{{renewal_instructions}}

**Important:** Please renew your {{document_type}} before the expiry date to avoid any legal issues or service interruptions.

If you need assistance with the renewal process, please contact us:
- Phone: {{contact_phone}}
- Email: {{contact_email}}

Best regards,
Nati Automotive Team',
'⚠️ **Document Expiry Alert**

👤 {{owner_name}}
🚗 {{vehicle_info}} ({{plate_number}})

📄 **{{document_type}}**
🆔 {{document_number}}
📅 Expires: {{expiry_date}}
⏰ {{days_remaining}} days remaining

🔄 Please renew before expiry!

📞 Need help? Call {{contact_phone}}',
'["owner_name", "vehicle_info", "plate_number", "document_type", "document_number", "issue_date", "expiry_date", "days_remaining", "renewal_instructions", "contact_phone", "contact_email"]'),

-- Admin Document Expiry Alert Template
('admin_document_expiry_alert', 'Admin Document Expiry Alert',
'🚨 Customer Document Expiry Alert - {{owner_name}}',
'Dear Admin,

A customer''s vehicle document is expiring in 10 days:

**Customer Information:**
- Name: {{owner_name}}
- Phone: {{customer_phone}}
- Email: {{customer_email}}

**Vehicle Information:**
- Vehicle: {{vehicle_info}}
- Plate Number: {{plate_number}}

**Document Details:**
- Type: {{document_type}}
- Number: {{document_number}}
- Expiry Date: {{expiry_date}}
- Days Remaining: {{days_remaining}}

**Action Required:**
- Customer has been notified via email
- Consider following up with a phone call
- Offer renewal assistance if needed

Vehicle ID: {{vehicle_id}}
Document ID: {{document_id}}

Best regards,
Nati Automotive System',
'🚨 **Customer Document Alert**

👤 **{{owner_name}}**
📞 {{customer_phone}}
🚗 {{vehicle_info}} ({{plate_number}})

📄 {{document_type}} expires in {{days_remaining}} days
📅 Expiry: {{expiry_date}}

✅ Customer notified via email
📞 Consider follow-up call

🆔 Vehicle: {{vehicle_id}} | Doc: {{document_id}}',
'["owner_name", "customer_phone", "customer_email", "vehicle_info", "plate_number", "document_type", "document_number", "expiry_date", "days_remaining", "vehicle_id", "document_id"]'),

-- Appointment Confirmation Template (for customers)
('appointment_confirmation', 'Appointment Confirmation',
'✅ Appointment Confirmed - {{service_name}}',
'Dear {{customer_name}},

Your appointment has been confirmed:

**Appointment Details:**
- Service: {{service_name}}
- Date: {{appointment_date}}
- Time: {{appointment_time}}
- Duration: {{service_duration}}
- Price: ETB {{service_price}}

**Our Location:**
Nati Automotive Service Center
[Address will be provided]

**Important Notes:**
- Please arrive 15 minutes before your appointment time
- Bring your vehicle registration and any relevant documents
- If you need to reschedule, please contact us at least 24 hours in advance

**Contact Information:**
- Phone: +251 911 123 456
- Email: support@natiautomotive.com

We look forward to serving you!

Best regards,
Nati Automotive Team',
'✅ **Appointment Confirmed**

👤 {{customer_name}}
🔧 {{service_name}}
📅 {{appointment_date}} at {{appointment_time}}
💰 ETB {{service_price}}

📍 Please arrive 15 minutes early
📞 Questions? Call +251 911 123 456

🆔 #{{appointment_id}}',
'["customer_name", "service_name", "appointment_date", "appointment_time", "service_duration", "service_price", "appointment_id"]'),

-- System Summary Template
('system_summary', 'System Summary Report',
'📊 Daily Document Expiry Check Summary',
'System Summary Report

**Daily Document Expiry Check - {{check_date}}**

**Summary:**
- Total documents checked: {{total_documents}}
- Successfully processed: {{success_count}}
- Errors encountered: {{error_count}}
- Documents expiring on: {{expiry_date}}

**Status:** Check completed successfully

This is an automated system report.

Nati Automotive System',
'📊 **Daily Summary**

📅 {{check_date}}
📄 Checked: {{total_documents}} documents
✅ Success: {{success_count}}
❌ Errors: {{error_count}}

⚠️ Expiring: {{expiry_date}}',
'["check_date", "total_documents", "success_count", "error_count", "expiry_date"]');

-- Insert sample vehicle documents (for testing)
-- Note: This assumes you have vehicles in tbl_vehicles table
INSERT INTO vehicle_documents (vehicle_id, document_type, document_number, issue_date, expiry_date, notes) VALUES
(1, 'insurance', 'INS-2024-001', '2024-01-15', '2025-01-15', 'Comprehensive insurance policy'),
(1, 'registration', 'REG-2024-001', '2024-01-15', '2025-01-15', 'Vehicle registration certificate'),
(2, 'insurance', 'INS-2024-002', '2024-02-01', '2025-02-01', 'Third party insurance'),
(2, 'inspection', 'INSP-2024-001', '2024-02-01', '2025-02-01', 'Annual safety inspection');

-- Update notification_config table with new templates
INSERT INTO notification_config (config_key, config_value, description) VALUES
('notification_templates_installed', 'true', 'Indicates that notification templates have been installed'),
('document_expiry_check_enabled', 'true', 'Enable/disable document expiry checking'),
('document_expiry_days_before', '10', 'Number of days before expiry to send reminder'),
('admin_notification_channels', '["telegram", "email", "web"]', 'Default notification channels for admin alerts'),
('customer_notification_channels', '["email", "web"]', 'Default notification channels for customer alerts')
ON DUPLICATE KEY UPDATE 
config_value = VALUES(config_value),
description = VALUES(description);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_notification_templates_key ON notification_templates(template_key);
CREATE INDEX IF NOT EXISTS idx_notification_templates_active ON notification_templates(is_active);
CREATE INDEX IF NOT EXISTS idx_vehicle_documents_expiry ON vehicle_documents(expiry_date, reminder_sent);
CREATE INDEX IF NOT EXISTS idx_notification_log_type_status ON notification_log(notification_type, status);

-- Create a view for upcoming document expiries
CREATE OR REPLACE VIEW upcoming_document_expiries AS
SELECT 
    vd.*,
    v.car_brand,
    v.car_model,
    v.car_year,
    v.plate_number,
    u.name as owner_name,
    u.email as owner_email,
    u.phonenum as owner_phone,
    DATEDIFF(vd.expiry_date, CURDATE()) as days_until_expiry
FROM vehicle_documents vd
JOIN tbl_vehicles v ON vd.vehicle_id = v.id
JOIN tbl_user u ON v.user_id = u.id
WHERE vd.expiry_date > CURDATE()
AND DATEDIFF(vd.expiry_date, CURDATE()) <= 30
ORDER BY vd.expiry_date ASC;

-- Create a view for notification statistics
CREATE OR REPLACE VIEW notification_statistics AS
SELECT 
    notification_type,
    channel,
    status,
    COUNT(*) as count,
    DATE(created_at) as date
FROM notification_log
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY notification_type, channel, status, DATE(created_at)
ORDER BY date DESC, notification_type, channel;

-- ============================================================================
-- Verification Queries
-- ============================================================================

-- Check if tables were created successfully
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME IN ('notification_templates', 'vehicle_documents', 'notification_log', 'activity_log');

-- Check notification templates
SELECT 
    template_key,
    template_name,
    is_active
FROM notification_templates
ORDER BY template_key;

-- Check vehicle documents
SELECT 
    COUNT(*) as total_documents,
    document_type,
    COUNT(CASE WHEN expiry_date <= DATE_ADD(CURDATE(), INTERVAL 10 DAY) THEN 1 END) as expiring_soon
FROM vehicle_documents
GROUP BY document_type;

-- ============================================================================
-- Maintenance Queries
-- ============================================================================

-- Clean up old notification logs (older than 90 days)
-- DELETE FROM notification_log WHERE created_at < DATE_SUB(CURDATE(), INTERVAL 90 DAY);

-- Reset reminder flags for testing
-- UPDATE vehicle_documents SET reminder_sent = FALSE, last_reminder_date = NULL WHERE reminder_sent = TRUE;

-- Check notification system health
-- SELECT 
--     DATE(created_at) as date,
--     status,
--     COUNT(*) as count
-- FROM notification_log
-- WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
-- GROUP BY DATE(created_at), status
-- ORDER BY date DESC, status;

-- ============================================================================
-- End of Setup Script
-- ============================================================================ 