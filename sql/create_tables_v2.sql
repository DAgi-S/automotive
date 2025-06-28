USE automotive;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS tbl_order_services;
DROP TABLE IF EXISTS tbl_service_orders;

-- Create service orders table
CREATE TABLE tbl_service_orders (
    id INT(11) NOT NULL AUTO_INCREMENT,
    appointment_id INT(11) NULL,
    client_id INT(11) NOT NULL,
    license_plate VARCHAR(20) NOT NULL,
    car_model VARCHAR(100) NOT NULL,
    technician_id INT(11) NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_client (client_id),
    INDEX idx_technician (technician_id),
    INDEX idx_appointment (appointment_id),
    CONSTRAINT fk_service_order_client FOREIGN KEY (client_id) REFERENCES tbl_user(id),
    CONSTRAINT fk_service_order_technician FOREIGN KEY (technician_id) REFERENCES tbl_worker(id),
    CONSTRAINT fk_service_order_appointment FOREIGN KEY (appointment_id) REFERENCES tbl_appointments(appointment_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create order services table
CREATE TABLE tbl_order_services (
    id INT(11) NOT NULL AUTO_INCREMENT,
    order_id INT(11) NOT NULL,
    service_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_order (order_id),
    INDEX idx_service (service_id),
    CONSTRAINT fk_order_service_order FOREIGN KEY (order_id) REFERENCES tbl_service_orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_service_service FOREIGN KEY (service_id) REFERENCES tbl_services(service_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 