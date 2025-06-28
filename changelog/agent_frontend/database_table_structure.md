# Database Table Structure for Frontend - Agent Frontend

## Frontend-Related Tables

### Table: tbl_blog
- id (int, PK) - Blog post ID
- image_url (varchar 255) - Blog post featured image
- title (varchar 400) - Blog post title
- s_article (text) - Short article preview
- article (text) - Full article content
- writer (varchar 100) - Author name
- date (varchar 255) - Publication date
- status (varchar 255) - Post status (published/draft)

### Table: company_branding
- id (int, PK) - Branding record ID
- company_name (varchar 255) - Company name
- logo_url (varchar 255) - Company logo URL
- favicon_url (varchar 255) - Favicon URL
- tagline (varchar 255) - Company tagline
- primary_color (varchar 50) - Primary brand color
- secondary_color (varchar 50) - Secondary brand color
- created_at (timestamp) - Creation timestamp
- updated_at (timestamp) - Last update timestamp

### Table: company_information
- id (int, PK) - Information record ID
- address (varchar 255) - Company address
- phone (varchar 50) - Contact phone number
- email (varchar 100) - Contact email
- website (varchar 100) - Website URL
- about (text) - About company description
- created_at (timestamp) - Creation timestamp
- updated_at (timestamp) - Last update timestamp

### Table: company_settings
- id (int, PK) - Setting record ID
- setting_key (varchar 100) - Setting name/key
- setting_value (text) - Setting value
- created_at (timestamp) - Creation timestamp
- updated_at (timestamp) - Last update timestamp

## Frontend Data Requirements

### Blog System:
- Dynamic blog listing with pagination
- Blog post detail pages
- Blog search and filtering
- Blog categories and tags (future enhancement)

### Company Information:
- Dynamic company details display
- Contact information management
- Branding customization
- Settings management for frontend display

### User Interaction:
- Contact form submissions
- Newsletter subscriptions (future enhancement)
- User testimonials (future enhancement)
- Appointment booking system integration 