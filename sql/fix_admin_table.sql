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
  `image_url` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert admin user with hashed password
INSERT INTO `tbl_admin` (`full_name`, `username`, `password`, `position`, `status`, `image_url`, `description`) 
VALUES ('abel negatu', 'bz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 'IMG-66d58c761593d3.44996186.jpg', 'admin');

-- Add missing columns if they don't exist
ALTER TABLE `tbl_admin` 
ADD COLUMN IF NOT EXISTS `status` varchar(50) NOT NULL DEFAULT 'active' AFTER `position`,
ADD COLUMN IF NOT EXISTS `permissions` TEXT NULL AFTER `status`;

-- Update existing admin password
UPDATE `tbl_admin` SET 
  `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  `status` = 'active'
WHERE `username` = 'bz'; 