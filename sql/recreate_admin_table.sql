-- First drop the foreign key constraints
ALTER TABLE `tbl_notifications` DROP FOREIGN KEY IF EXISTS `tbl_notifications_ibfk_1`;
ALTER TABLE `tbl_notification_preferences` DROP FOREIGN KEY IF EXISTS `tbl_notification_preferences_ibfk_1`;
ALTER TABLE `tbl_order_status_log` DROP FOREIGN KEY IF EXISTS `tbl_order_status_log_ibfk_2`;

-- Drop and recreate tbl_admin table
DROP TABLE IF EXISTS `tbl_admin`;
CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `permissions` TEXT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert admin user with hashed password (password is "12")
INSERT INTO `tbl_admin` (`full_name`, `username`, `password`, `position`, `status`) 
VALUES ('abel negatu', 'bz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Recreate the foreign key constraints
ALTER TABLE `tbl_notifications` 
ADD CONSTRAINT `tbl_notifications_ibfk_1` 
FOREIGN KEY (`recipient_id`) REFERENCES `tbl_admin` (`id`) ON DELETE CASCADE;

ALTER TABLE `tbl_notification_preferences` 
ADD CONSTRAINT `tbl_notification_preferences_ibfk_1` 
FOREIGN KEY (`admin_id`) REFERENCES `tbl_admin` (`id`) ON DELETE CASCADE;

ALTER TABLE `tbl_order_status_log` 
ADD CONSTRAINT `tbl_order_status_log_ibfk_2` 
FOREIGN KEY (`changed_by`) REFERENCES `tbl_admin` (`id`) ON DELETE SET NULL; 