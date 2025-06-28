# Automotive Project

## Overview
A comprehensive automotive management and ecommerce platform for workshops, service providers, and customers. Features include appointment booking, ecommerce, blog, notifications, and admin management.

## Features
- Service booking and management
- Ecommerce with cart, checkout, and payment
- Blog and articles
- Admin dashboard
- Notification system
- Mobile-first responsive design

## Tech Stack
- PHP (Backend)
- MySQL (Database)
- HTML, CSS, JavaScript (Frontend)
- Bootstrap, Font Awesome

## Getting Started
### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Composer (for vendor dependencies)
- CPanel shared hosting or local XAMPP

### Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/DAgi-S/automotive.git
   ```
2. Upload files to your CPanel `public_html` directory or use XAMPP `htdocs`.
3. Import the database:
   - Use `automotive.sql` or files in `/sql/`.
   - In CPanel: Go to phpMyAdmin, create a database, and import the SQL file.
4. Configure database connection:
   - Copy `config/database.php.example` to `config/database.php`.
   - Update with your DB credentials.
5. Set file/folder permissions as needed.

### Deployment to CPanel
- Upload all files to `public_html`.
- Ensure `.htaccess` is present for URL rewriting.
- Import the database via phpMyAdmin.
- Update `config/database.php` with CPanel DB credentials.
- Test the site via your domain.

### Contribution
See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## License
MIT License. See [LICENSE](LICENSE). 