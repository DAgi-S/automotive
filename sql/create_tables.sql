USE automotive;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS tbl_order_services;
DROP TABLE IF EXISTS tbl_service_orders;

-- Create service orders table
CREATE TABLE tbl_service_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NULL,
    client_id INT NOT NULL,
    license_plate VARCHAR(20) NOT NULL,
    car_model VARCHAR(100) NOT NULL,
    technician_id INT NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES tbl_user(id),
    FOREIGN KEY (technician_id) REFERENCES tbl_worker(id),
    FOREIGN KEY (appointment_id) REFERENCES tbl_appointments(appointment_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create order services table
CREATE TABLE tbl_order_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    service_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES tbl_service_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES tbl_services(service_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 



CREATE TABLE IF NOT EXISTS company_branding (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(255),
    logo_url VARCHAR(255),
    favicon_url VARCHAR(255),
    tagline VARCHAR(255),
    primary_color VARCHAR(50),
    secondary_color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS company_information (
    id INT PRIMARY KEY AUTO_INCREMENT,
    address VARCHAR(255),
    phone VARCHAR(50),
    email VARCHAR(100),
    website VARCHAR(100),
    about TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS company_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100),
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default row for branding and info if not exists
INSERT INTO company_branding (company_name, logo_url, favicon_url, tagline, primary_color, secondary_color)
SELECT 'Company Name', '', '', '', '#4e73df', '#858796'
WHERE NOT EXISTS (SELECT 1 FROM company_branding);

INSERT INTO company_information (address, phone, email, website, about)
SELECT '123 Main St, City', '+1234567890', 'info@company.com', 'www.company.com', 'Short company description goes here.'
WHERE NOT EXISTS (SELECT 1 FROM company_information);