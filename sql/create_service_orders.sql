USE automotive;

-- Create service orders table
CREATE TABLE IF NOT EXISTS tbl_service_orders (
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
    FOREIGN KEY (appointment_id) REFERENCES tbl_appointments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create service order services table
CREATE TABLE IF NOT EXISTS tbl_order_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    service_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES tbl_service_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES tbl_services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 