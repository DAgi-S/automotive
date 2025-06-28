## 2024-03-21

### SQL Commands
```sql
-- Add missing columns to tbl_admin
ALTER TABLE `tbl_admin` ADD `status` varchar(50) NOT NULL DEFAULT 'active' AFTER `position`;
ALTER TABLE `tbl_admin` ADD `permissions` TEXT NULL AFTER `status`;

-- Update existing admin password to use proper hashing
UPDATE `tbl_admin` SET `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE `username` = 'bz';
-- The above hash is for password "12"
``` 