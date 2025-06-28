-- ============================================================================
-- Website Content Management System Database Setup
-- ============================================================================
-- This file creates tables for managing all dynamic content on the website
-- including hero sections, analytics, testimonials, FAQs, and other content
-- ============================================================================

-- Create website sections table for managing different content areas
CREATE TABLE IF NOT EXISTS website_sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_key VARCHAR(100) UNIQUE NOT NULL,
    section_name VARCHAR(255) NOT NULL,
    section_description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create hero carousel content table
CREATE TABLE IF NOT EXISTS hero_carousel (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    subtitle TEXT,
    button_text VARCHAR(100),
    button_link VARCHAR(255),
    background_image VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create testimonials table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(255) NOT NULL,
    customer_title VARCHAR(255),
    testimonial_text TEXT NOT NULL,
    customer_image VARCHAR(255),
    rating INT DEFAULT 5,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create FAQ table
CREATE TABLE IF NOT EXISTS faqs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100) DEFAULT 'general',
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create social media links table
CREATE TABLE IF NOT EXISTS social_media_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    platform_name VARCHAR(100) NOT NULL,
    platform_icon VARCHAR(100) NOT NULL,
    platform_url VARCHAR(500) NOT NULL,
    platform_color VARCHAR(50),
    description TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create business hours table
CREATE TABLE IF NOT EXISTS business_hours (
    id INT PRIMARY KEY AUTO_INCREMENT,
    day_name VARCHAR(20) NOT NULL,
    opening_time TIME,
    closing_time TIME,
    is_closed BOOLEAN DEFAULT FALSE,
    special_note VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create quick links table
CREATE TABLE IF NOT EXISTS quick_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    link_title VARCHAR(255) NOT NULL,
    link_url VARCHAR(500) NOT NULL,
    link_icon VARCHAR(100),
    description TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create website settings table for general configuration
CREATE TABLE IF NOT EXISTS website_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'number', 'boolean', 'image', 'url') DEFAULT 'text',
    setting_description TEXT,
    setting_group VARCHAR(100) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create analytics display settings table
CREATE TABLE IF NOT EXISTS analytics_display (
    id INT PRIMARY KEY AUTO_INCREMENT,
    metric_key VARCHAR(100) UNIQUE NOT NULL,
    metric_title VARCHAR(255) NOT NULL,
    metric_icon VARCHAR(100) NOT NULL,
    metric_description VARCHAR(255),
    metric_color VARCHAR(50),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default website sections
INSERT INTO website_sections (section_key, section_name, section_description, display_order) VALUES
('hero_carousel', 'Hero Carousel', 'Main carousel section at the top of homepage', 1),
('analytics', 'Business Analytics', 'Statistics and metrics display section', 2),
('featured_services', 'Featured Services', 'Showcase of main services', 3),
('latest_products', 'Latest Products', 'Recent products display', 4),
('recent_blogs', 'Recent Blogs', 'Latest blog posts', 5),
('clients', 'Our Clients', 'Client testimonials and logos', 6),
('testimonials', 'Customer Testimonials', 'Customer reviews and feedback', 7),
('faqs', 'Frequently Asked Questions', 'Common questions and answers', 8),
('social_connect', 'Social Media Connect', 'Social media platforms links', 9);

-- Insert default hero carousel content
INSERT INTO hero_carousel (title, subtitle, button_text, button_link, background_image, display_order) VALUES
('Welcome to Nati Automotive', 'Your trusted partner for quality auto parts and professional services.', 'Shop Now', 'ecommerce/pages/products.php', 'assets/images/homescreen/auto1.jpg', 1),
('Expert Automotive Services', 'Professional care for your car, every time.', 'Our Services', 'services.php', 'assets/images/homescreen/auto1.jpg', 2);

-- Insert default testimonials
INSERT INTO testimonials (customer_name, customer_title, testimonial_text, rating, display_order) VALUES
('Samuel T.', 'Customer', 'Nati Automotive provided excellent service and quality parts. Highly recommended!', 5, 1),
('Helen M.', 'Customer', 'Fast, reliable, and professional. My go-to for car maintenance.', 5, 2),
('David K.', 'Customer', 'Outstanding customer service and competitive prices. Very satisfied with their work.', 5, 3);

-- Insert default FAQs
INSERT INTO faqs (question, answer, category, display_order) VALUES
('What services do you offer?', 'We offer a wide range of automotive services including maintenance, diagnostics, oil changes, brake services, engine repairs, and more.', 'services', 1),
('Do you sell genuine auto parts?', 'Yes, we only sell genuine and high-quality auto parts for all car models. All parts come with manufacturer warranties.', 'products', 2),
('How can I book a service appointment?', 'You can book a service appointment online through our website, by calling our service center, or by visiting us in person.', 'appointments', 3),
('What are your business hours?', 'We are open Monday to Friday from 8:00 AM to 6:00 PM, Saturday from 9:00 AM to 4:00 PM, and closed on Sundays.', 'general', 4);

-- Insert default social media links
INSERT INTO social_media_links (platform_name, platform_icon, platform_url, platform_color, description, display_order) VALUES
('WhatsApp', 'fab fa-whatsapp', 'https://wa.me/251911123456', '#25D366', 'Quick support & instant answers', 1),
('Telegram', 'fab fa-telegram', 'https://t.me/natiautomotive', '#0088cc', 'Join our community channel', 2),
('TikTok', 'fab fa-tiktok', 'https://tiktok.com/@natiautomotive', '#ff0050', 'Auto tips & behind the scenes', 3);

-- Insert default business hours
INSERT INTO business_hours (day_name, opening_time, closing_time, display_order) VALUES
('Monday', '08:00:00', '18:00:00', 1),
('Tuesday', '08:00:00', '18:00:00', 2),
('Wednesday', '08:00:00', '18:00:00', 3),
('Thursday', '08:00:00', '18:00:00', 4),
('Friday', '08:00:00', '18:00:00', 5),
('Saturday', '09:00:00', '16:00:00', 6),
('Sunday', NULL, NULL, 7);

-- Update Sunday to be closed
UPDATE business_hours SET is_closed = TRUE WHERE day_name = 'Sunday';

-- Insert default quick links
INSERT INTO quick_links (link_title, link_url, link_icon, description, display_order) VALUES
('Our Services', 'services.php', 'fas fa-wrench', 'Browse our automotive services', 1),
('Shop Parts', 'ecommerce/pages/products.php', 'fas fa-shopping-cart', 'Purchase auto parts online', 2),
('About Us', 'about.php', 'fas fa-info-circle', 'Learn more about our company', 3),
('Contact Us', 'contact.php', 'fas fa-envelope', 'Get in touch with us', 4);

-- Insert default website settings
INSERT INTO website_settings (setting_key, setting_value, setting_type, setting_description, setting_group) VALUES
('site_title', 'Nati Automotive', 'text', 'Main website title', 'general'),
('site_tagline', 'Your trusted automotive partner', 'text', 'Website tagline/subtitle', 'general'),
('contact_phone', '+251 911 123 456', 'text', 'Main contact phone number', 'contact'),
('contact_email', 'info@natiautomotive.com', 'text', 'Main contact email address', 'contact'),
('company_address', 'Addis Ababa, Ethiopia', 'text', 'Company physical address', 'contact'),
('show_analytics_section', '1', 'boolean', 'Display business analytics section', 'homepage'),
('analytics_section_title', 'Our Business Impact', 'text', 'Title for analytics section', 'homepage'),
('analytics_section_subtitle', 'Real-time statistics from our automotive services', 'text', 'Subtitle for analytics section', 'homepage'),
('featured_services_limit', '3', 'number', 'Number of featured services to display', 'homepage'),
('latest_products_limit', '3', 'number', 'Number of latest products to display', 'homepage'),
('recent_blogs_limit', '3', 'number', 'Number of recent blogs to display', 'homepage'),
('clients_limit', '4', 'number', 'Number of client logos to display', 'homepage'),
('hero_carousel_autoplay', '1', 'boolean', 'Enable carousel autoplay', 'homepage'),
('hero_carousel_interval', '5000', 'number', 'Carousel slide interval in milliseconds', 'homepage');

-- Insert default analytics display settings
INSERT INTO analytics_display (metric_key, metric_title, metric_icon, metric_description, metric_color, display_order) VALUES
('completed_services', 'Services Provided', 'fa-tools', 'Completed services', '#28a745', 1),
('total_clients', 'Registered Clients', 'fa-users', 'Total customers', '#17a2b8', 2),
('total_vehicles', 'Vehicles Serviced', 'fa-car', 'Registered vehicles', '#ffc107', 3),
('total_workers', 'Expert Technicians', 'fa-user-tie', 'Professional staff', '#dc3545', 4),
('active_users', 'App Users', 'fa-calendar-check', 'Using our reminder system', '#6f42c1', 5),
('total_appointments', 'Total Bookings', 'fa-bell', 'Service appointments', '#6c757d', 6);

-- Create indexes for better performance
CREATE INDEX idx_website_sections_active ON website_sections(is_active, display_order);
CREATE INDEX idx_hero_carousel_active ON hero_carousel(is_active, display_order);
CREATE INDEX idx_testimonials_active ON testimonials(is_active, display_order);
CREATE INDEX idx_faqs_active ON faqs(is_active, display_order);
CREATE INDEX idx_social_media_active ON social_media_links(is_active, display_order);
CREATE INDEX idx_quick_links_active ON quick_links(is_active, display_order);
CREATE INDEX idx_website_settings_key ON website_settings(setting_key);
CREATE INDEX idx_website_settings_group ON website_settings(setting_group);
CREATE INDEX idx_analytics_display_active ON analytics_display(is_active, display_order);
CREATE INDEX idx_business_hours_order ON business_hours(display_order);

-- Create views for easy content retrieval
CREATE OR REPLACE VIEW active_hero_slides AS
SELECT * FROM hero_carousel 
WHERE is_active = TRUE 
ORDER BY display_order ASC;

CREATE OR REPLACE VIEW active_testimonials AS
SELECT * FROM testimonials 
WHERE is_active = TRUE 
ORDER BY display_order ASC;

CREATE OR REPLACE VIEW active_faqs AS
SELECT * FROM faqs 
WHERE is_active = TRUE 
ORDER BY display_order ASC;

CREATE OR REPLACE VIEW active_social_links AS
SELECT * FROM social_media_links 
WHERE is_active = TRUE 
ORDER BY display_order ASC;

CREATE OR REPLACE VIEW active_quick_links AS
SELECT * FROM quick_links 
WHERE is_active = TRUE 
ORDER BY display_order ASC;

CREATE OR REPLACE VIEW active_analytics_metrics AS
SELECT * FROM analytics_display 
WHERE is_active = TRUE 
ORDER BY display_order ASC;

CREATE OR REPLACE VIEW website_business_hours AS
SELECT * FROM business_hours 
ORDER BY display_order ASC;

-- ============================================================================
-- Verification Queries
-- ============================================================================

-- Check if tables were created successfully
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME IN (
    'website_sections', 'hero_carousel', 'testimonials', 'faqs', 
    'social_media_links', 'business_hours', 'quick_links', 
    'website_settings', 'analytics_display'
);

-- Check default data insertion
SELECT 'Hero Carousel' as Content_Type, COUNT(*) as Records FROM hero_carousel
UNION ALL
SELECT 'Testimonials', COUNT(*) FROM testimonials
UNION ALL
SELECT 'FAQs', COUNT(*) FROM faqs
UNION ALL
SELECT 'Social Media', COUNT(*) FROM social_media_links
UNION ALL
SELECT 'Quick Links', COUNT(*) FROM quick_links
UNION ALL
SELECT 'Website Settings', COUNT(*) FROM website_settings
UNION ALL
SELECT 'Analytics Display', COUNT(*) FROM analytics_display;

-- ============================================================================
-- End of Website Content Management Setup
-- ============================================================================ 