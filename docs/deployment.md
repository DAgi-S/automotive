# Deployment Guide: CPanel Shared Hosting

## 1. Upload Files
- Upload all project files to your `public_html` directory using CPanel File Manager or FTP.

## 2. Database Setup
- Open CPanel > MySQL Databases
- Create a new database and user, assign user to database
- Go to phpMyAdmin, select your database, and import `automotive.sql` or files in `/sql/`

## 3. Configure Database Connection
- Copy `config/database.php.example` to `config/database.php`
- Edit with your CPanel database credentials

## 4. Set Permissions
- Ensure `uploads/` and other writable directories have correct permissions (usually 755 or 775)

## 5. Test the Application
- Visit your domain to verify everything works

## 6. Troubleshooting
- Check error logs in CPanel if issues arise
- Ensure `.htaccess` is present for URL rewriting 