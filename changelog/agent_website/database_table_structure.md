# Database Table Structure (agent_website)

This document tracks the database tables used by the website pages and their structure.

## Database: automotive

### Tables Used by Website

#### tbl_services
Used by: services.php, contact.php, api/get_services.php
```sql
+--------------+---------------------------+------+-----+---------------------+----------------+
| Field        | Type                      | Null | Key | Default             | Extra          |
+--------------+---------------------------+------+-----+---------------------+----------------+
| service_id   | int(11)                   | NO   | PRI | NULL                | auto_increment |
| service_name | varchar(255)              | NO   |     | NULL                |                |
| icon_class   | varchar(100)              | YES  |     | NULL                |                |
| description  | text                      | YES  |     | NULL                |                |
| price        | decimal(10,2)             | YES  |     | NULL                |                |
| duration     | varchar(50)               | YES  |     | NULL                |                |
| status       | enum('active','inactive') | YES  |     | active              |                |
| created_at   | timestamp                 | NO   |     | current_timestamp() |                |
+--------------+---------------------------+------+-----+---------------------+----------------+
```

#### tbl_blog
Used by: index.php, api/get_blogs.php
```sql
+-----------+-------------------------+------+-----+---------------------+----------------+
| Field     | Type                    | Null | Key | Default             | Extra          |
+-----------+-------------------------+------+-----+---------------------+----------------+
| id        | int(11)                 | NO   | PRI | NULL                | auto_increment |
| title     | varchar(255)            | NO   |     | NULL                |                |
| writer    | varchar(100)            | YES  |     | Admin               |                |
| date      | timestamp               | NO   |     | current_timestamp() |                |
| s_article | text                    | YES  |     | NULL                |                |
| content   | text                    | YES  |     | NULL                |                |
| image_url | varchar(255)            | YES  |     | NULL                |                |
| status    | enum('featured','none') | YES  |     | none                |                |
+-----------+-------------------------+------+-----+---------------------+----------------+
```

#### tbl_worker
Used by: about.php, api/get_team_members.php
```sql
+------------+----------------+------+-----+---------------------+----------------+
| Field      | Type           | Null | Key | Default             | Extra          |
+------------+----------------+------+-----+---------------------+----------------+
| id         | int(11)        | NO   | PRI | NULL                | auto_increment |
| full_name  | varchar(255)   | NO   |     | NULL                |                |
| username   | varchar(100)   | NO   | UNI | NULL                |                |
| password   | varchar(255)   | NO   |     | NULL                |                |
| position   | enum('worker') | NO   |     | worker              |                |
| created_at | timestamp      | NO   |     | current_timestamp() |                |
| image_url  | varchar(255)   | YES  |     | NULL                |                |
+------------+----------------+------+-----+---------------------+----------------+
```

#### company_information
Used by: contact.php, api/get_company_info.php
```sql
+------------+--------------+------+-----+---------------------+-------------------------------+
| Field      | Type         | Null | Key | Default             | Extra                         |
+------------+--------------+------+-----+---------------------+-------------------------------+
| id         | int(11)      | NO   | PRI | NULL                | auto_increment                |
| address    | varchar(255) | YES  |     | NULL                |                               |
| phone      | varchar(50)  | YES  |     | NULL                |                               |
| email      | varchar(100) | YES  |     | NULL                |                               |
| website    | varchar(100) | YES  |     | NULL                |                               |
| about      | text         | YES  |     | NULL                |                               |
| created_at | timestamp    | NO   |     | current_timestamp() |                               |
| updated_at | timestamp    | NO   |     | current_timestamp() | on update current_timestamp() |
+------------+--------------+------+-----+---------------------+-------------------------------+
```

## Usage Notes

### Services (tbl_services)
- Only active services are displayed on the website
- icon_class field stores Font Awesome icon classes (e.g., 'fa-wrench', 'fa-oil-can')
- price field is displayed as "Starting at Br X" format
- duration field shows estimated service time

### Blogs (tbl_blog)
- Latest 3 blogs are shown on homepage
- s_article field is used as excerpt/preview text
- image_url stores filename only, full path is uploads/blogs/
- status can be 'featured' or 'none'

### Workers/Team (tbl_worker)
- Used to display team members on about page
- image_url stores filename only, full path is admin/uploads/workers/
- position field is enum but website displays as "Automotive Technician" for better UX

### Company Information (company_information)
- Single record table for company contact details
- address field supports multi-line addresses
- Used for contact page and contact forms

#### tbl_products
Used by: index.php, api/get_homepage_data.php
```sql
+-------------+---------------------------+------+-----+---------------------+-------------------------------+
| Field       | Type                      | Null | Key | Default             | Extra                         |
+-------------+---------------------------+------+-----+---------------------+-------------------------------+
| id          | int(11)                   | NO   | PRI | NULL                | auto_increment                |
| name        | varchar(255)              | NO   |     | NULL                |                               |
| description | text                      | YES  |     | NULL                |                               |
| price       | decimal(10,2)             | NO   |     | NULL                |                               |
| image_url   | varchar(255)              | YES  |     | NULL                |                               |
| category_id | int(11)                   | YES  | MUL | NULL                |                               |
| stock       | int(11)                   | YES  |     | 0                   |                               |
| status      | enum('active','inactive') | YES  |     | active              |                               |
| created_at  | timestamp                 | NO   |     | current_timestamp() |                               |
| updated_at  | timestamp                 | NO   |     | current_timestamp() | on update current_timestamp() |
+-------------+---------------------------+------+-----+---------------------+-------------------------------+
```

#### tbl_user
Used by: index.php (clients section), api/get_homepage_data.php
```sql
+--------------+--------------+------+-----+---------------------+----------------+
| Field        | Type         | Null | Key | Default             | Extra          |
+--------------+--------------+------+-----+---------------------+----------------+
| id           | int(11)      | NO   | PRI | NULL                | auto_increment |
| name         | varchar(255) | NO   |     | NULL                |                |
| email        | varchar(255) | NO   | UNI | NULL                |                |
| new_img_name | varchar(255) | YES  |     | NULL                |                |
| car_brand    | varchar(100) | YES  |     | NULL                |                |
| role         | varchar(20)  | YES  |     | user                |                |
| phonenum     | varchar(20)  | NO   |     | NULL                |                |
| password     | varchar(255) | NO   |     | NULL                |                |
| created_at   | timestamp    | NO   |     | current_timestamp() |                |
+--------------+--------------+------+-----+---------------------+----------------+
```

### Products (tbl_products)
- Latest 3 active products are shown on homepage
- image_url stores filename only, full path is uploads/products/
- price field displayed with 2 decimal places and "Br" currency prefix
- Only active products are displayed

### Users/Clients (tbl_user)
- Users with profile images (new_img_name not null) are shown as clients on homepage
- new_img_name stores filename only, full path is uploads/users/
- Limited to 4 most recent users for homepage display
- Used for customer testimonials and client showcase

#### tbl_appointments
Used by: index.php (analytics), api/get_analytics.php
```sql
+----------------+------------------+------+-----+---------------------+----------------+
| Field          | Type             | Null | Key | Default             | Extra          |
+----------------+------------------+------+-----+---------------------+----------------+
| appointment_id | int(11)          | NO   | PRI | NULL                | auto_increment |
| user_id        | int(11)          | YES  | MUL | NULL                |                |
| service_id     | int(11)          | YES  | MUL | NULL                |                |
| appointment_date| date            | YES  |     | NULL                |                |
| appointment_time| time            | YES  |     | NULL                |                |
| status         | varchar(20)      | YES  |     | pending             |                |
| notes          | text             | YES  |     | NULL                |                |
| created_at     | timestamp        | NO   |     | current_timestamp() |                |
| worker_id      | int(11)          | YES  | MUL | NULL                |                |
+----------------+------------------+------+-----+---------------------+----------------+
```

#### tbl_vehicles
Used by: index.php (analytics), api/get_analytics.php
```sql
+------------+--------------+------+-----+---------------------+----------------+
| Field      | Type         | Null | Key | Default             | Extra          |
+------------+--------------+------+-----+---------------------+----------------+
| vehicle_id | int(11)      | NO   | PRI | NULL                | auto_increment |
| user_id    | int(11)      | YES  | MUL | NULL                |                |
| make       | varchar(100) | YES  |     | NULL                |                |
| model      | varchar(100) | YES  |     | NULL                |                |
| year       | int(4)       | YES  |     | NULL                |                |
| plate_no   | varchar(20)  | YES  |     | NULL                |                |
| created_at | timestamp    | NO   |     | current_timestamp() |                |
+------------+--------------+------+-----+---------------------+----------------+
```

### Appointments (tbl_appointments)
- Used for analytics: completed services, total bookings, active users
- Status values: 'pending', 'confirmed', 'completed', 'cancelled'
- Links users to services and assigns workers
- Tracks appointment dates and times for scheduling

### Vehicles (tbl_vehicles)
- Stores customer vehicle information for service tracking
- Links to users for vehicle ownership
- Used in analytics for total vehicles serviced count
- Includes make, model, year, and license plate information

#### tbl_chats
Used by: chat system (admin, user, worker, guest chat management)
```sql
+------------+---------------------+------+-----+---------------------+-------------------------------+
| Field      | Type                | Null | Key | Default             | Extra                         |
+------------+---------------------+------+-----+---------------------+-------------------------------+
| chat_id    | int(11)             | NO   | PRI | NULL                | auto_increment                |
| user_id    | int(11)             | YES  | MUL | NULL                |                               |
| guest_id   | int(11)             | YES  | MUL | NULL                |                               |
| admin_id   | int(11)             | YES  | MUL | NULL                |                               |
| worker_id  | int(11)             | YES  | MUL | NULL                |                               |
| assigned_by| int(11)             | YES  | MUL | NULL                |                               |
| status     | enum('open','closed','pending','escalated') | YES |   | open |                |
| created_at | timestamp           | NO   |     | current_timestamp() |                               |
| updated_at | timestamp           | NO   |     | current_timestamp() | on update current_timestamp()  |
+------------+---------------------+------+-----+---------------------+-------------------------------+
```

#### tbl_chat_messages
Used by: chat system (message storage)
```sql
+-------------+---------------------+------+-----+---------------------+-------------------------------+
| Field       | Type                | Null | Key | Default             | Extra                         |
+-------------+---------------------+------+-----+---------------------+-------------------------------+
| message_id  | int(11)             | NO   | PRI | NULL                | auto_increment                |
| chat_id     | int(11)             | NO   | MUL | NULL                |                               |
| sender_type | enum('user','admin','worker','guest') | NO | | NULL |                |
| sender_id   | int(11)             | YES  |     | NULL                |                               |
| message     | text                | NO   |     | NULL                |                               |
| is_read     | boolean             | YES  |     | 0                   |                               |
| created_at  | timestamp           | NO   |     | current_timestamp() |                               |
+-------------+---------------------+------+-----+---------------------+-------------------------------+
```

#### tbl_guest_sessions
Used by: chat system (guest chat sessions)
```sql
+-------------+---------------------+------+-----+---------------------+-------------------------------+
| Field       | Type                | Null | Key | Default             | Extra                         |
+-------------+---------------------+------+-----+---------------------+-------------------------------+
| guest_id    | int(11)             | NO   | PRI | NULL                | auto_increment                |
| session_token| varchar(255)       | NO   |     | NULL                |                               |
| created_at  | timestamp           | NO   |     | current_timestamp() |                               |
+-------------+---------------------+------+-----+---------------------+-------------------------------+
```

---
*Last updated: 2025-01-21*
*Agent: agent_website* 