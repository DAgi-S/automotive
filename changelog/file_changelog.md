# File Changelog

## 2024-01-17
- 2024-01-17 (agent_frontend): Initial assessment of website frontend structure and creation of agent reports
- changelog/agent_frontend/file_changelog.md (Created) - Frontend file change tracking
- changelog/agent_frontend/database_table_structure.md (Created) - Frontend database requirements
- changelog/agent_frontend/agent_frontend_summery_report.md (Created) - Frontend agent summary report

## 2024-03-19
- admin/orders.php - Fixed Add New Order button functionality and form submission
- admin/get_services.php - Updated to handle service selection and pre-selected services
- admin/process_order.php - Updated to handle order creation and service assignments
- admin/get_order_details.php - Updated to return service IDs for editing
- admin/orders.php - Fixed SQL query to use correct column name 'full_name' from tbl_worker table
- admin/orders.php - Updated to use new service orders tables
- admin/process_order.php - Updated to use new service orders tables
- admin/get_order_details.php - Updated to use new service orders tables

## 2024-03-21
- admin/fetchEditBlog.php - Created new file to handle blog edit data fetching
- admin/blogs.php - Updated to use new fetchEditBlog.php endpoint
- admin/get_blog.php - Fixed blog data fetching and HTML content handling
- `recreate_admin_table.sql` - Created to fix admin table structure
- `sql_changelog.md` - Created to track SQL changes 
- website/includes/db_connect.php (Created)
- website/includes/blog_functions.php (Created)
- website/index.php (Updated)
- website/blog-single.php (Created)
- admin/orders.php (Updated modal functionality and Bootstrap 5 compatibility)
- admin/get_technicians.php (Updated to fetch from tbl_worker table)
- admin/get_clients.php (Fixed car information fetching from tbl_user and tbl_info)
- admin/orders.php (Updated client selection handling and car information validation)
- admin/get_order_details.php (Updated to fetch complete order details with car information)
- admin/orders.php (Fixed edit modal functionality)

## 2024-03-17
- website/contact.php (Updated contact information, enhanced design and functionality) 

## 2024-03-26
- assets/svg/animated-logo.svg (Created and updated with embedded animations)
- website/animated-logo-demo.html (Created and updated)
- assets/css/logo-animation.css (Created and later removed - animations moved to SVG)
- admin/blogs.php - Updated comments modal and added JavaScript functionality
- admin/get_blog_comments.php - Refactored to return JSON data instead of HTML

## March 26, 2024
- admin/blogs.php - Fixed blog deletion to prevent header warning
- admin/blogs.php - Updated TinyMCE with valid API key and added image upload functionality
- admin/blogs.php - Updated comments modal and added JavaScript functionality
- admin/get_blog_comments.php - Refactored to return JSON data instead of HTML 

## 2024-03-19
- admin/orders.php - Fixed SQL query to use correct column name 'full_name' from tbl_worker table
- admin/orders.php - Updated to use new service orders tables
- admin/process_order.php - Updated to use new service orders tables
- admin/get_order_details.php - Updated to use new service orders tables
- admin/get_appointments.php (Created)
- admin/get_services.php (Created)
- admin/get_technicians.php (Created)
- admin/get_clients.php (Created)
- admin/get_appointment_details.php (Created)
- changelog/sql_changelog.md (Created) 

## 2025-01-26 - Agent Website

### Location & Profile Page Enhancements
- **location.php** - Enhanced with mobile-first design, interactive map, and professional contact system (2025-01-26)
- **profile.php** - Enhanced with improved mobile architecture and performance optimization (2025-01-26)

### Dashboard Page Enhancement
- **dashboard.php** - Enhanced with mobile-first design using home CSS architecture (2025-01-26) 