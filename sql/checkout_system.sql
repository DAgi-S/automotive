-- Checkout System Database Tables
-- Created: 2025-01-21
-- Purpose: Handle orders, order items, and payment processing

-- Orders table to store order information
CREATE TABLE IF NOT EXISTS `tbl_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL UNIQUE,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `order_number` (`order_number`),
  KEY `status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order items table to store individual products in each order
CREATE TABLE IF NOT EXISTS `tbl_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(500) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  FOREIGN KEY (`order_id`) REFERENCES `tbl_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table (if not exists) for order item references
CREATE TABLE IF NOT EXISTS `tbl_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_image` varchar(500) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payment transactions table
CREATE TABLE IF NOT EXISTS `tbl_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed','cancelled') DEFAULT 'pending',
  `gateway_response` text DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `transaction_id` (`transaction_id`),
  FOREIGN KEY (`order_id`) REFERENCES `tbl_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Customer addresses table for shipping/billing
CREATE TABLE IF NOT EXISTS `tbl_customer_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('shipping','billing','both') DEFAULT 'both',
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `company` varchar(200) DEFAULT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Ethiopia',
  `phone` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some sample products
INSERT IGNORE INTO `tbl_products` (`id`, `name`, `description`, `price`, `product_image`, `stock_quantity`) VALUES
(1, 'Premium Brake Pads - High Performance', 'High-quality brake pads for superior stopping power and durability', 89.99, 'https://partsouq.com/assets/tesseract/assets/global/TOYOTA00/source/11/110642.gif', 50),
(2, 'Engine Oil Filter - Long Life Protection', 'Premium oil filter for extended engine protection and performance', 12.99, 'https://www.ebaymotorsblog.com/motors/blog/wp-content/uploads/2023/08/evo_crankshaft-592x400.jpg', 100),
(3, 'Performance Boost Kit', 'Complete performance enhancement kit for increased power and efficiency', 199.99, 'https://sp-ao.shortpixel.ai/client/to_auto,q_glossy,ret_img,w_495,h_450/https://dizz.com/wp-content/uploads/2023/04/car-turbocharger-isolated-on-white-background-tur-2021-08-27-08-38-29-utc-PhotoRoom.png-PhotoRoom.webp', 25),
(4, 'Premium Suspension System', 'Advanced suspension system for improved ride quality and handling', 299.99, 'https://w7.pngwing.com/pngs/260/445/png-transparent-automotive-engine-parts-automotive-engine-parts-car-parts-auto-parts.png', 15);

-- Create indexes for better performance
CREATE INDEX idx_orders_created_at ON tbl_orders(created_at);
CREATE INDEX idx_orders_user_status ON tbl_orders(user_id, status);
CREATE INDEX idx_order_items_order_product ON tbl_order_items(order_id, product_id);
CREATE INDEX idx_payments_status ON tbl_payments(status);
CREATE INDEX idx_addresses_user_default ON tbl_customer_addresses(user_id, is_default); 