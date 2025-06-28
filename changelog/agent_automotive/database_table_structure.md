# Database Table Structure for 'automotive'

## Table: tbl_admin
- id (int, PK)
- full_name (varchar 100)
- username (varchar 100)
- password (varchar 255)
- position (varchar 255)
- image_url (varchar 255)
- description (text)

## Table: tbl_blog
- id (int, PK)
- image_url (varchar 255)
- title (varchar 400)
- s_article (text)
- article (text)
- writer (varchar 100)
- date (varchar 255)
- status (varchar 255)

## Table: tbl_info
- id (int, PK)
- userid (int)
- car_brand (varchar 255)
- year (varchar 255)
- car_model (varchar 255)
- service_date (varchar 255)
- mile_age (varchar 255)
- oil_change (varchar 255)
- insurance (varchar 255)
- bolo (varchar 255)
- rd_wegen (varchar 255)
- yemenged_fend (varchar 255)
- img_name1 (varchar 255)
- img_name2 (varchar 255)
- img_name3 (varchar 255)

## Table: tbl_user
- id (int, PK)
- name (varchar 255)
- phonenum (varchar 255)
- password (varchar 255)
- car_brand (varchar 255)
- email (varchar 255)
- rege_date (timestamp)
- new_img_name (varchar 255)

## Table: tbl_worker
- id (int, PK)
- full_name (varchar 225)
- username (varchar 225)
- password (varchar 225)
- position (varchar 255)
- image_url (varchar 255)

## Table: company_branding
- id (int, PK)
- company_name (varchar 255)
- logo_url (varchar 255)
- favicon_url (varchar 255)
- tagline (varchar 255)
- primary_color (varchar 50)
- secondary_color (varchar 50)
- created_at (timestamp)
- updated_at (timestamp)

## Table: company_information
- id (int, PK)
- address (varchar 255)
- phone (varchar 50)
- email (varchar 100)
- website (varchar 100)
- about (text)
- created_at (timestamp)
- updated_at (timestamp)

## Table: company_settings
- id (int, PK)
- setting_key (varchar 100)
- setting_value (text)
- created_at (timestamp)
- updated_at (timestamp)

## Table: tbl_service_history
| Field        | Type          | Null | Key | Default             | Extra          |
|--------------|--------------|------|-----|---------------------|----------------|
| history_id   | int(11)      | NO   | PRI | NULL                | auto_increment |
| vehicle_id   | int(11)      | YES  | MUL | NULL                |                |
| mechanic_id  | int(11)      | YES  | MUL | NULL                |                |
| service_id   | int(11)      | YES  | MUL | NULL                |                |
| service_date | date         | YES  |     | NULL                |                |
| notes        | text         | YES  |     | NULL                |                |
| cost         | decimal(10,2)| YES  |     | NULL                |                |
| created_at   | timestamp    | NO   |     | current_timestamp() |                |

- `mechanic_id` references `tbl_worker(id)` (ON DELETE SET NULL, ON UPDATE CASCADE)

## Foreign Keys
- `tbl_service_history.mechanic_id` → `tbl_worker.id` 