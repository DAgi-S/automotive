-- Notification System Configuration
-- This file sets up the notification configuration table and enhances existing notification system

-- Create notification_config table for storing API keys and configuration
CREATE TABLE IF NOT EXISTS `notification_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL UNIQUE,
  `config_value` text,
  `config_type` enum('telegram', 'email', 'sms', 'push', 'general') DEFAULT 'general',
  `is_active` tinyint(1) DEFAULT 1,
  `description` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default notification configurations
INSERT INTO `notification_config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
-- Telegram Configuration
('telegram_bot_token', '', 'telegram', 'Telegram Bot API Token'),
('telegram_bot_username', '', 'telegram', 'Telegram Bot Username'),
('telegram_chat_id', '', 'telegram', 'Default Telegram Chat ID for notifications'),
('telegram_enabled', '0', 'telegram', 'Enable/Disable Telegram notifications'),

-- Email Configuration  
('smtp_host', 'smtp.gmail.com', 'email', 'SMTP Server Host'),
('smtp_port', '587', 'email', 'SMTP Server Port'),
('smtp_username', '', 'email', 'SMTP Username/Email'),
('smtp_password', '', 'email', 'SMTP Password/App Password'),
('smtp_encryption', 'tls', 'email', 'SMTP Encryption (tls/ssl)'),
('email_from_name', 'Nati Automotive', 'email', 'Email From Name'),
('email_from_address', '', 'email', 'Email From Address'),
('email_enabled', '0', 'email', 'Enable/Disable Email notifications'),

-- Push Notification Configuration
('push_enabled', '1', 'push', 'Enable/Disable Web Push notifications'),
('push_public_key', '', 'push', 'Web Push Public Key (VAPID)'),
('push_private_key', '', 'push', 'Web Push Private Key (VAPID)'),

-- General Notification Settings
('notification_sound', '1', 'general', 'Enable notification sounds'),
('notification_popup', '1', 'general', 'Enable notification popups'),
('admin_notifications', '1', 'general', 'Enable admin notifications'),
('user_notifications', '1', 'general', 'Enable user notifications');

-- Add telegram_user_id column to tbl_user table if it doesn't exist
ALTER TABLE `tbl_user` 
ADD COLUMN `telegram_user_id` varchar(50) NULL AFTER `phone`,
ADD COLUMN `telegram_username` varchar(100) NULL AFTER `telegram_user_id`,
ADD COLUMN `notification_preferences` JSON NULL AFTER `telegram_username`;

-- Add telegram_user_id column to tbl_admin table if it doesn't exist  
ALTER TABLE `tbl_admin`
ADD COLUMN `telegram_user_id` varchar(50) NULL AFTER `phone`,
ADD COLUMN `telegram_username` varchar(100) NULL AFTER `telegram_user_id`;

-- Enhance tbl_notifications table with delivery status
ALTER TABLE `tbl_notifications`
ADD COLUMN `notification_method` enum('web', 'email', 'telegram', 'sms') DEFAULT 'web' AFTER `type`,
ADD COLUMN `delivery_status` enum('pending', 'sent', 'delivered', 'failed') DEFAULT 'pending' AFTER `is_read`,
ADD COLUMN `sent_at` timestamp NULL AFTER `delivery_status`,
ADD COLUMN `error_message` text NULL AFTER `sent_at`;

-- Create notification_queue table for managing notification delivery
CREATE TABLE IF NOT EXISTS `notification_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `method` enum('email', 'telegram', 'sms', 'push') NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(255) NULL,
  `message` text NOT NULL,
  `data` JSON NULL,
  `status` enum('pending', 'processing', 'sent', 'failed') DEFAULT 'pending',
  `attempts` int(11) DEFAULT 0,
  `max_attempts` int(11) DEFAULT 3,
  `error_message` text NULL,
  `scheduled_at` timestamp NULL,
  `sent_at` timestamp NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `notification_id` (`notification_id`),
  KEY `status` (`status`),
  KEY `method` (`method`),
  FOREIGN KEY (`notification_id`) REFERENCES `tbl_notifications`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create notification_templates table for storing message templates
CREATE TABLE IF NOT EXISTS `notification_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_key` varchar(100) NOT NULL UNIQUE,
  `template_name` varchar(255) NOT NULL,
  `subject` varchar(255) NULL,
  `message_template` text NOT NULL,
  `template_type` enum('email', 'telegram', 'sms', 'web') NOT NULL,
  `variables` JSON NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_key` (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default notification templates
INSERT INTO `notification_templates` (`template_key`, `template_name`, `subject`, `message_template`, `template_type`, `variables`) VALUES
-- Appointment Templates
('appointment_created_email', 'New Appointment - Email', 'Appointment Confirmation', 'Dear {{customer_name}},\n\nYour appointment has been confirmed for {{appointment_date}} at {{appointment_time}}.\n\nService: {{service_name}}\nVehicle: {{vehicle_info}}\n\nThank you for choosing Nati Automotive!', 'email', '["customer_name", "appointment_date", "appointment_time", "service_name", "vehicle_info"]'),
('appointment_created_telegram', 'New Appointment - Telegram', NULL, '🚗 *New Appointment Confirmed*\n\n👤 Customer: {{customer_name}}\n📅 Date: {{appointment_date}}\n⏰ Time: {{appointment_time}}\n🔧 Service: {{service_name}}\n🚙 Vehicle: {{vehicle_info}}\n\n✅ Appointment confirmed successfully!', 'telegram', '["customer_name", "appointment_date", "appointment_time", "service_name", "vehicle_info"]'),
('appointment_status_email', 'Appointment Status Update - Email', 'Appointment Status Update', 'Dear {{customer_name}},\n\nYour appointment status has been updated to: {{status}}\n\nAppointment Details:\nDate: {{appointment_date}}\nTime: {{appointment_time}}\nService: {{service_name}}\n\nFor any questions, please contact us.', 'email', '["customer_name", "status", "appointment_date", "appointment_time", "service_name"]'),
('appointment_status_telegram', 'Appointment Status Update - Telegram', NULL, '🔄 *Appointment Status Update*\n\n👤 Customer: {{customer_name}}\n📊 Status: {{status}}\n📅 Date: {{appointment_date}}\n⏰ Time: {{appointment_time}}\n🔧 Service: {{service_name}}', 'telegram', '["customer_name", "status", "appointment_date", "appointment_time", "service_name"]'),

-- Order Templates
('order_created_email', 'New Order - Email', 'Order Confirmation', 'Dear {{customer_name}},\n\nThank you for your order! Your order #{{order_id}} has been received.\n\nOrder Total: ${{order_total}}\nOrder Items: {{order_items}}\n\nWe will process your order shortly.', 'email', '["customer_name", "order_id", "order_total", "order_items"]'),
('order_created_telegram', 'New Order - Telegram', NULL, '🛒 *New Order Received*\n\n👤 Customer: {{customer_name}}\n🆔 Order ID: #{{order_id}}\n💰 Total: ${{order_total}}\n📦 Items: {{order_items}}\n\n✅ Order confirmed!', 'telegram', '["customer_name", "order_id", "order_total", "order_items"]'),

-- Admin Notification Templates
('admin_new_appointment', 'Admin - New Appointment', NULL, '🚨 *New Appointment Alert*\n\n👤 Customer: {{customer_name}}\n📞 Phone: {{customer_phone}}\n📅 Date: {{appointment_date}}\n⏰ Time: {{appointment_time}}\n🔧 Service: {{service_name}}\n🚙 Vehicle: {{vehicle_info}}\n\n💼 Please review and confirm.', 'telegram', '["customer_name", "customer_phone", "appointment_date", "appointment_time", "service_name", "vehicle_info"]'),
('admin_new_order', 'Admin - New Order', NULL, '🛒 *New Order Alert*\n\n👤 Customer: {{customer_name}}\n📞 Phone: {{customer_phone}}\n🆔 Order ID: #{{order_id}}\n💰 Total: ${{order_total}}\n📦 Items: {{order_items}}\n\n💼 Please process the order.', 'telegram', '["customer_name", "customer_phone", "order_id", "order_total", "order_items"]'); 