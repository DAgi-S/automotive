-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2025 at 02:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `automotive`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `activity_type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `admin_id`, `activity_type`, `description`, `created_at`) VALUES
(1, 1, 'appointment_update', 'Updated appointment #6 status to completed', '2025-03-21 22:57:03'),
(2, 1, 'appointment_update', 'Updated appointment #7 status to confirmed', '2025-03-21 22:58:29'),
(3, 1, 'appointment_update', 'Updated appointment #4 status to completed', '2025-03-21 22:59:43'),
(4, 1, 'appointment_update', 'Updated appointment #7 status to completed', '2025-03-21 22:59:47'),
(5, 1, 'appointment_update', 'Updated appointment #5 status to confirmed', '2025-03-21 23:01:28'),
(6, 1, 'appointment_update', 'Updated appointment #1 status to completed', '2025-03-21 23:01:35'),
(7, 1, 'appointment_update', 'Updated appointment #3 status to confirmed', '2025-03-21 23:01:40'),
(8, 1, 'appointment_update', 'Updated appointment #2 status to completed', '2025-03-22 00:07:14'),
(9, 1, 'appointment_update', 'Updated appointment #8 status to confirmed', '2025-03-22 23:47:51'),
(10, 1, 'appointment_update', 'Updated appointment #9 status to confirmed', '2025-03-22 23:47:54'),
(11, 1, 'appointment_update', 'Updated appointment #10 status to confirmed', '2025-03-22 23:47:58'),
(12, 1, 'appointment_update', 'Updated appointment #11 status to confirmed', '2025-03-22 23:48:01'),
(13, 1, 'appointment_update', 'Updated appointment #12 status to confirmed', '2025-03-22 23:48:04'),
(14, 1, 'appointment_update', 'Updated appointment #13 status to confirmed', '2025-03-22 23:48:07'),
(15, 1, 'appointment_delete', 'Deleted appointment #15', '2025-03-23 00:20:43'),
(16, 1, 'appointment_delete', 'Deleted appointment #16', '2025-03-23 00:20:45'),
(17, 1, 'appointment_delete', 'Deleted appointment #17', '2025-03-23 00:20:46'),
(18, 1, 'appointment_delete', 'Deleted appointment #18', '2025-03-23 00:20:48'),
(19, 1, 'appointment_delete', 'Deleted appointment #19', '2025-03-23 00:20:49'),
(20, 1, 'appointment_delete', 'Deleted appointment #20', '2025-03-23 00:20:50'),
(21, 1, 'appointment_delete', 'Deleted appointment #21', '2025-03-23 00:20:52'),
(22, 1, 'appointment_delete', 'Deleted appointment #22', '2025-03-23 00:20:53'),
(23, 1, 'appointment_delete', 'Deleted appointment #14', '2025-03-23 00:20:55'),
(24, 1, 'appointment_update', 'Updated appointment #23 status to confirmed', '2025-03-23 00:24:07');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `description`, `logo`, `created_at`) VALUES
(1, 'Mnab', 'Mnab', '67cf31a0aaf62.jpg', '2025-03-10 18:38:24');

-- --------------------------------------------------------

--
-- Table structure for table `car_brands`
--

CREATE TABLE `car_brands` (
  `id` int(11) NOT NULL,
  `brand_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_brands`
--

INSERT INTO `car_brands` (`id`, `brand_name`, `created_at`) VALUES
(1, 'Toyota', '2025-03-10 15:48:50'),
(2, 'Honda', '2025-03-10 15:48:50'),
(3, 'Ford', '2025-03-10 15:48:50'),
(4, 'Chevrolett', '2025-03-10 15:48:50'),
(5, 'Nissan', '2025-03-10 15:48:50'),
(6, 'Hyundai', '2025-03-10 15:48:50'),
(7, 'Kia', '2025-03-10 15:48:50'),
(8, 'Volkswagen', '2025-03-10 15:48:50'),
(17, 'Sino Truck', '2025-03-10 18:42:39');

-- --------------------------------------------------------

--
-- Table structure for table `car_models`
--

CREATE TABLE `car_models` (
  `id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `year_from` int(4) DEFAULT NULL,
  `year_to` int(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_models`
--

INSERT INTO `car_models` (`id`, `brand_id`, `model_name`, `year_from`, `year_to`, `created_at`) VALUES
(1, 1, 'Camry', 1982, 2024, '2025-03-10 15:48:50'),
(2, 1, 'Corolla', 1966, 2024, '2025-03-10 15:48:50'),
(3, 1, 'RAV4', 1994, 2024, '2025-03-10 15:48:50'),
(4, 1, 'Highlander', 2000, 2024, '2025-03-10 15:48:50'),
(5, 1, 'Prius', 1997, 2024, '2025-03-10 15:48:50'),
(6, 2, 'Civic', 1972, 2024, '2025-03-10 15:48:50'),
(7, 2, 'Accord', 1976, 2024, '2025-03-10 15:48:50'),
(8, 2, 'CR-V', 1995, 2024, '2025-03-10 15:48:50'),
(9, 2, 'Pilot', 2002, 2024, '2025-03-10 15:48:50'),
(10, 3, 'F-150', 1975, 2024, '2025-03-10 15:48:50'),
(11, 3, 'Mustang', 1964, 2024, '2025-03-10 15:48:50'),
(12, 3, 'Explorer', 1990, 2024, '2025-03-10 15:48:50'),
(13, 3, 'Escape', 2000, 2024, '2025-03-10 15:48:50'),
(14, 4, 'Silverado', 1998, 2024, '2025-03-10 15:48:50'),
(15, 4, 'Malibu', 1964, 2024, '2025-03-10 15:48:50'),
(16, 4, 'Equinox', 2004, 2024, '2025-03-10 15:48:50'),
(17, 4, 'Tahoe', 1994, 2024, '2025-03-10 15:48:50'),
(18, 5, 'Altima', 1992, 2024, '2025-03-10 15:48:50'),
(19, 5, 'Maxima', 1981, 2024, '2025-03-10 15:48:50'),
(20, 5, 'Rogue', 2007, 2024, '2025-03-10 15:48:50'),
(21, 5, 'Sentra', 1982, 2024, '2025-03-10 15:48:50'),
(22, 6, 'Elantra', 1990, 2024, '2025-03-10 15:48:50'),
(23, 6, 'Sonata', 1985, 2024, '2025-03-10 15:48:50'),
(24, 6, 'Tucson', 2004, 2024, '2025-03-10 15:48:50'),
(25, 6, 'Santa Fe', 2000, 2024, '2025-03-10 15:48:50'),
(26, 7, 'Forte', 2008, 2024, '2025-03-10 15:48:50'),
(27, 7, 'Optima', 2000, 2024, '2025-03-10 15:48:50'),
(28, 7, 'Sorento', 2002, 2024, '2025-03-10 15:48:50'),
(29, 7, 'Sportage', 1993, 2024, '2025-03-10 15:48:50'),
(30, 8, 'Jetta', 1979, 2024, '2025-03-10 15:48:50'),
(31, 8, 'Passat', 1973, 2024, '2025-03-10 15:48:50'),
(32, 8, 'Golf', 1974, 2024, '2025-03-10 15:48:50'),
(33, 8, 'Tiguan', 2007, 2024, '2025-03-10 15:48:50'),
(67, 17, 'Dump Truck', 2000, 2025, '2025-03-10 18:43:04'),
(68, 4, 'asd', 1990, 1995, '2025-03-21 23:47:51');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `icon`, `parent_id`, `created_at`) VALUES
(1, 'GPS', '', 'fas fa-location', NULL, '2025-03-10 18:33:32'),
(2, 'Enginee', 'Engine', 'fas fa-car', NULL, '2025-03-10 18:37:42'),
(3, 'Piston', 'Piston', 'fas fa-car', 2, '2025-03-10 18:37:55');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `permission_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission_name`, `permission_description`, `created_at`) VALUES
(1, 'manage_appointments', 'Can manage service appointments', '2025-03-22 00:00:24'),
(2, 'manage_services', 'Can manage service offerings', '2025-03-22 00:00:24'),
(3, 'manage_products', 'Can manage product catalog', '2025-03-22 00:00:24'),
(4, 'manage_orders', 'Can manage customer orders', '2025-03-22 00:00:24'),
(5, 'manage_users', 'Can manage user accounts', '2025-03-22 00:00:24'),
(6, 'manage_workers', 'Can manage worker profiles', '2025-03-22 00:00:24'),
(7, 'manage_settings', 'Can modify system settings', '2025-03-22 00:00:24'),
(8, 'view_reports', 'Can view system reports and analytics', '2025-03-22 00:00:24'),
(9, 'manage_inventory', 'Can manage product inventory', '2025-03-22 00:00:24'),
(10, 'manage_brands', 'Can manage car brands and models', '2025-03-22 00:00:24'),
(11, 'manage_customers', 'Can manage customer accounts', '2025-03-22 00:10:26');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `created_at`) VALUES
(1, 'admin', '2025-03-22 00:00:43'),
(2, 'manager', '2025-03-22 00:10:26'),
(3, 'worker', '2025-03-22 00:10:26');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`) VALUES
(1, 1, 1, '2025-03-22 00:00:43'),
(2, 1, 10, '2025-03-22 00:00:43'),
(3, 1, 9, '2025-03-22 00:00:43'),
(4, 1, 4, '2025-03-22 00:00:43'),
(5, 1, 3, '2025-03-22 00:00:43'),
(6, 1, 2, '2025-03-22 00:00:43'),
(7, 1, 7, '2025-03-22 00:00:43'),
(8, 1, 5, '2025-03-22 00:00:43'),
(9, 1, 6, '2025-03-22 00:00:43'),
(10, 1, 8, '2025-03-22 00:00:43');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'new_appointment', '1', '2025-03-21 23:57:31', '2025-03-21 23:57:31'),
(2, 'appointment_status', '1', '2025-03-21 23:57:31', '2025-03-21 23:58:30'),
(3, 'new_order', '1', '2025-03-21 23:57:31', '2025-03-21 23:58:30'),
(4, 'low_stock', '1', '2025-03-21 23:57:31', '2025-03-21 23:58:30'),
(9, 'notify_new_appointment', '1', '2025-03-22 00:13:47', '2025-03-22 00:13:47'),
(10, 'notify_appointment_status', '1', '2025-03-22 00:13:47', '2025-03-22 00:13:47'),
(11, 'notify_new_order', '0', '2025-03-22 00:13:47', '2025-03-22 00:13:47'),
(12, 'notify_low_stock', '0', '2025-03-22 00:13:47', '2025-03-22 00:13:47'),
(13, 'smtp_host', 'mail.lebawi.net', '2025-03-22 22:12:19', '2025-03-22 22:12:19'),
(14, 'smtp_port', '465', '2025-03-22 22:12:19', '2025-03-22 22:12:19'),
(15, 'smtp_username', 'swapcapital@lebawi.net', '2025-03-22 22:12:19', '2025-03-22 22:12:19'),
(16, 'smtp_password', 'swapcapital@0924', '2025-03-22 22:12:19', '2025-03-22 22:12:19'),
(17, 'smtp_encryption', 'tls', '2025-03-22 22:12:19', '2025-03-22 22:12:19'),
(18, 'from_email', 'swapcapital@lebawi.net', '2025-03-22 22:12:19', '2025-03-22 22:12:19'),
(19, 'from_name', 'Nati Automotive', '2025-03-22 22:12:19', '2025-03-22 22:12:19');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `position` enum('admin','superadmin') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `full_name`, `username`, `password`, `position`, `created_at`) VALUES
(1, 'Super Admin', 'admin', '$2y$10$KhDnHsgxRFsTIp7hyBQ47.myHFSNBHnpCGKwIiOs9v.c8B7/Fkhem', 'superadmin', '2025-03-10 04:49:53'),
(2, 'System Administrator', 'superadmin', '$2y$10$gYgD4pi/gaG1DvGWpBoUsOCs2VMqMwDH2xM/PhUxiohrO3kldYWDq', 'superadmin', '2025-03-21 21:22:17');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ads`
--

CREATE TABLE `tbl_ads` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_name` varchar(255) DEFAULT NULL,
  `position` enum('home_top','home_middle','home_bottom','sidebar','service_page') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive','scheduled') DEFAULT 'inactive',
  `click_count` int(11) DEFAULT 0,
  `target_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_ads`
--

INSERT INTO `tbl_ads` (`id`, `title`, `description`, `image_name`, `position`, `start_date`, `end_date`, `status`, `click_count`, `target_url`, `created_at`, `updated_at`) VALUES
(1, 'GPS add', 'GPS', 'ad_67df39dc57f67.png', 'home_top', '2025-03-23', '2025-03-31', 'active', 0, 'https://t.me/SwapTechnology', '2025-03-22 22:29:48', '2025-03-22 22:29:48'),
(2, 'GPS add', 'G', 'ad_67df3ad746bae.png', 'home_middle', '2025-03-30', '2025-03-31', 'active', 0, 'https://t.me/SwapTechnology', '2025-03-22 22:33:59', '2025-03-22 22:33:59'),
(3, 'GPS add', 'g', 'ad_67df3ae704592.png', 'home_bottom', '2025-03-23', '2025-03-31', 'active', 1, 'https://t.me/SwapTechnology', '2025-03-22 22:34:15', '2025-03-22 22:49:50'),
(4, 'Location Top Ad', '', 'ad_67df3c6298b2f.png', 'sidebar', '0000-00-00', '0000-00-00', 'active', 0, 'https://t.me/SwapTechnology', '2025-03-22 22:38:30', '2025-03-22 22:40:34'),
(5, 'Location Bottom Ad', NULL, NULL, '', '0000-00-00', '0000-00-00', 'active', 0, NULL, '2025-03-22 22:38:30', '2025-03-22 22:38:30'),
(6, 'Small Banner Ad', NULL, NULL, '', '0000-00-00', '0000-00-00', 'active', 0, NULL, '2025-03-22 22:38:30', '2025-03-22 22:38:30'),
(7, 'Products Top Ad', NULL, NULL, '', '0000-00-00', '0000-00-00', 'active', 0, NULL, '2025-03-22 22:38:30', '2025-03-22 22:38:30'),
(8, 'Products Grid Ad', 'Products Grid Ad', 'ad_67df44ae532b6.png', 'service_page', '0000-00-00', '0000-00-00', 'active', 0, 'https://t.me/SwapTechnology', '2025-03-22 22:38:30', '2025-03-22 23:16:24'),
(9, 'Products Bottom Ad', 'Products Bottom Ad', 'ad_67df44a97ad0d.png', 'home_top', '0000-00-00', '0000-00-00', 'active', 1, 'https://t.me/SwapTechnology', '2025-03-22 22:38:30', '2025-03-22 23:16:18'),
(10, 'Mini Square Ad', 'Mini Square Ad', 'ad_67df44a3aa4f9.png', 'sidebar', '0000-00-00', '0000-00-00', 'active', 1, 'https://t.me/SwapTechnology', '2025-03-22 22:38:30', '2025-03-22 23:16:11'),
(11, 'Location Top Ad', NULL, NULL, '', '0000-00-00', '0000-00-00', 'active', 0, NULL, '2025-03-22 22:40:41', '2025-03-22 22:40:41'),
(12, 'Location Bottom Ad', NULL, NULL, '', '0000-00-00', '0000-00-00', 'active', 0, NULL, '2025-03-22 22:40:41', '2025-03-22 22:40:41'),
(13, 'Small Banner Ad', NULL, NULL, '', '0000-00-00', '0000-00-00', 'active', 0, NULL, '2025-03-22 22:40:41', '2025-03-22 22:40:41'),
(14, 'Products Top Ad', NULL, NULL, '', '0000-00-00', '0000-00-00', 'active', 0, NULL, '2025-03-22 22:40:41', '2025-03-22 22:40:41'),
(15, 'Products Grid Ad', NULL, NULL, '', '0000-00-00', '0000-00-00', 'active', 0, NULL, '2025-03-22 22:40:41', '2025-03-22 22:40:41'),
(16, 'Products Bottom Ad', NULL, NULL, '', '0000-00-00', '0000-00-00', 'active', 0, NULL, '2025-03-22 22:40:41', '2025-03-22 22:40:41'),
(17, 'Mini Square Ad', NULL, NULL, '', '0000-00-00', '0000-00-00', 'active', 0, NULL, '2025-03-22 22:40:41', '2025-03-22 22:40:41');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_appointments`
--

CREATE TABLE `tbl_appointments` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `worker_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_appointments`
--

INSERT INTO `tbl_appointments` (`appointment_id`, `user_id`, `service_id`, `appointment_date`, `appointment_time`, `status`, `notes`, `created_at`, `worker_id`) VALUES
(1, 1, 10, '2025-03-11', '10:10:00', 'completed', NULL, '2025-03-10 20:42:14', NULL),
(2, 1, 2, '2025-03-11', NULL, 'completed', NULL, '2025-03-10 20:42:14', NULL),
(3, 1, 7, '2025-03-11', NULL, 'confirmed', NULL, '2025-03-10 20:42:14', NULL),
(4, 1, 1, '2025-03-18', NULL, 'completed', NULL, '2025-03-10 21:22:29', NULL),
(5, 1, 2, '2025-03-18', NULL, 'completed', NULL, '2025-03-10 21:22:29', NULL),
(6, 4, 9, '2025-03-22', NULL, 'completed', NULL, '2025-03-21 18:56:41', NULL),
(7, 4, 5, '2025-03-22', NULL, 'completed', NULL, '2025-03-21 18:56:41', NULL),
(8, 1, 5, '2025-03-25', '15:00:00', 'confirmed', '', '2025-03-22 23:46:43', NULL),
(9, 1, 10, '2025-03-25', '15:00:00', 'confirmed', '', '2025-03-22 23:46:43', NULL),
(10, 1, 3, '2025-03-25', '15:00:00', 'confirmed', '', '2025-03-22 23:46:43', NULL),
(11, 1, 8, '2025-03-25', '15:00:00', 'confirmed', '', '2025-03-22 23:46:43', NULL),
(12, 1, 4, '2025-03-25', '15:00:00', 'confirmed', '', '2025-03-22 23:46:43', NULL),
(13, 1, 7, '2025-03-25', '15:00:00', 'confirmed', '', '2025-03-22 23:46:43', NULL),
(23, 1, 9, '2025-03-26', '15:00:00', 'confirmed', '', '2025-03-23 00:22:52', NULL),
(24, 1, 5, '2025-03-26', '15:00:00', 'pending', '', '2025-03-23 00:22:52', NULL),
(25, 1, 5, '2025-03-27', '14:00:00', 'pending', '', '2025-03-23 00:30:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_appointment_images`
--

CREATE TABLE `tbl_appointment_images` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_articles`
--

CREATE TABLE `tbl_articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `author` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_articles`
--

INSERT INTO `tbl_articles` (`id`, `title`, `content`, `category_id`, `featured_image`, `status`, `author`, `created_at`, `updated_at`) VALUES
(1, 'heckC', 'Check', 1, 'cc', 'draft', 1, '2025-03-23 00:54:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_article_categories`
--

CREATE TABLE `tbl_article_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_article_categories`
--

INSERT INTO `tbl_article_categories` (`id`, `category_name`, `created_at`) VALUES
(1, 'Check', '2025-03-23 00:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_blog`
--

CREATE TABLE `tbl_blog` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `writer` varchar(100) DEFAULT 'Admin',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `s_article` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('featured','none') DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_blog`
--

INSERT INTO `tbl_blog` (`id`, `title`, `writer`, `date`, `s_article`, `content`, `image_url`, `status`) VALUES
(1, 'When to Change Your Car Oil', 'Admin', '2025-03-10 04:59:53', 'Learn when it\'s time to change your car oil and why it\'s important.', 'Full article content here...', NULL, 'featured'),
(2, 'Essential Car Maintenance Tips', 'Admin', '2025-03-10 04:59:53', 'Essential tips for maintaining your car in top condition.', 'Full article content here...', NULL, 'featured'),
(3, 'The most common car warning lights', 'Admin', '2025-03-10 04:59:53', 'Understanding the most common warning lights on your dashboard.', 'Full article content here...', NULL, 'featured'),
(4, 'Genpro', 'Dad', '2025-03-23 01:44:46', 'ds', 'xf', 'uploads/blogs/67df678e53848.png', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_blog_comments`
--

CREATE TABLE `tbl_blog_comments` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_blog_comments`
--

INSERT INTO `tbl_blog_comments` (`id`, `blog_id`, `user_id`, `parent_id`, `comment`, `created_at`) VALUES
(1, 1, 1, 0, 'asdjk\nalskjd\nadsj', '2025-03-10 09:27:45'),
(2, 1, 1, 1, 'wow', '2025-03-10 09:27:51'),
(3, 1, 1, 0, 'wow wowow wowow', '2025-03-10 09:50:25'),
(4, 1, 4, 0, 'thank you', '2025-03-21 19:07:51'),
(5, 1, 5, 0, 'Check', '2025-03-23 01:49:46'),
(6, 1, 5, 1, 'check', '2025-03-23 01:49:55'),
(7, 2, 5, 0, 'CCheck', '2025-03-23 01:50:10');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_blog_likes`
--

CREATE TABLE `tbl_blog_likes` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_like` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_blog_likes`
--

INSERT INTO `tbl_blog_likes` (`id`, `blog_id`, `user_id`, `is_like`, `created_at`) VALUES
(1, 1, 1, 1, '2025-03-10 09:27:09'),
(2, 3, 1, 1, '2025-03-10 09:50:46'),
(3, 2, 4, 1, '2025-03-10 17:53:10'),
(4, 1, 4, 1, '2025-03-21 19:07:44'),
(5, 1, 5, 1, '2025-03-23 01:49:45'),
(6, 2, 5, 0, '2025-03-23 01:50:07');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cart`
--

CREATE TABLE `tbl_cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_categories`
--

CREATE TABLE `tbl_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_categories`
--

INSERT INTO `tbl_categories` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Suzuki', 'Suzukif', 'active', '2025-03-22 22:07:33', '2025-03-22 22:10:44'),
(3, 'Toyota', 'Toyota', 'active', '2025-03-22 22:11:02', '2025-03-22 22:11:02');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feedback`
--

CREATE TABLE `tbl_feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_info`
--

CREATE TABLE `tbl_info` (
  `id` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `car_brand` varchar(100) DEFAULT NULL,
  `car_year` varchar(10) DEFAULT NULL,
  `car_model` varchar(100) DEFAULT NULL,
  `plate_number` varchar(20) DEFAULT NULL,
  `trailer_number` varchar(20) DEFAULT NULL,
  `service_date` varchar(20) DEFAULT NULL,
  `mile_age` varchar(20) DEFAULT NULL,
  `oil_change` varchar(20) DEFAULT NULL,
  `insurance` varchar(10) DEFAULT NULL,
  `bolo` varchar(10) DEFAULT NULL,
  `rd_wegen` varchar(10) DEFAULT NULL,
  `yemenged_fend` varchar(10) DEFAULT NULL,
  `img_name1` varchar(255) DEFAULT NULL,
  `img_name2` varchar(255) DEFAULT NULL,
  `img_name3` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `oil_change_date` date DEFAULT NULL,
  `insurance_date` date DEFAULT NULL,
  `bolo_date` date DEFAULT NULL,
  `rd_wegen_date` date DEFAULT NULL,
  `yemenged_fend_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_info`
--

INSERT INTO `tbl_info` (`id`, `userid`, `car_brand`, `car_year`, `car_model`, `plate_number`, `trailer_number`, `service_date`, `mile_age`, `oil_change`, `insurance`, `bolo`, `rd_wegen`, `yemenged_fend`, `img_name1`, `img_name2`, `img_name3`, `created_at`, `image1`, `image2`, `image3`, `oil_change_date`, `insurance_date`, `bolo_date`, `rd_wegen_date`, `yemenged_fend_date`) VALUES
(1, 1, '4', '2004', '16', 'AA3214', 'AA31412', '06-03-2025', '5000', '1111', '11-03-2025', '12-03-2025', '09-03-2025', '20-03-2025', 'IMG-67cf0b49027842.94784637.png', 'IMG-67cf0b4902d2b8.15636761.png', 'IMG-67cf0b49035102.97181759.png', '2025-03-10 15:54:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, '3', '2023', '10', 'AA3265', 'AA3654', '12-03-2025', '2544', '231', '06-03-2025', '27-03-2025', '20-03-2025', '20-03-2025', 'IMG-67cf1173c09b89.79669284.png', 'IMG-67cf1173c0bd63.66553985.png', 'IMG-67cf1173c0d931.28945574.png', '2025-03-10 16:21:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, '4', '2006', '16', NULL, NULL, '12-03-2025', '432', '2344', '05-03-2025', '20-03-2025', '18-03-2025', '28-03-2025', 'IMG-67cf1523428f80.15241822.png', 'IMG-67cf152342b1e0.82057875.png', 'IMG-67cf152342d2e0.33667249.png', '2025-03-10 16:36:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 1, '4', '2016', '15', 'OR 87878', '', '05-03-2025', '65444', '5455', '12-03-2025', '12-04-2025', '18-04-2025', '07-11-2025', 'IMG-67cf165c819824.99900926.png', 'IMG-67cf165c81ba68.75368149.png', 'IMG-67cf165c81cf73.39607141.png', '2025-03-10 16:42:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 4, '1', '2024', '2', 'AA73138', '', '09-05-2025', '855755', '855000', '12-03-2025', '28-03-2025', '20-03-2025', '29-03-2025', 'IMG-67cf277e3096a5.92103562.jpg', 'IMG-67cf277e30c7c5.34810628.jpg', 'IMG-67cf277e30e562.68685967.jpg', '2025-03-10 17:55:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 4, '3', '2022', '10', '11010', '10100', '01-03-2025', '50100', '50100', '29-03-2025', '29-03-2025', '29-03-2025', '29-03-2025', 'IMG-67ddd298e144f7.26541177.png', 'IMG-67ddd298e178f0.77078142.png', 'IMG-67ddd298e19e41.73293057.png', '2025-03-21 20:56:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 5, '4', '2024', '16', '124578', '', '20-03-2025', '100000', '25000', '05-04-2025', '05-04-2025', '12-04-2025', '19-04-2025', 'IMG-67df380950faa4.46805100.png', 'IMG-67df3809511d94.54950821.png', 'IMG-67df3809512ec3.93906878.png', '2025-03-22 22:22:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_message`
--

CREATE TABLE `tbl_message` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `status` enum('read','unread') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notifications`
--

CREATE TABLE `tbl_notifications` (
  `id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notification_preferences`
--

CREATE TABLE `tbl_notification_preferences` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `notification_type` varchar(50) DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT 1,
  `email_enabled` tinyint(1) DEFAULT 0,
  `sound_enabled` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_orders`
--

CREATE TABLE `tbl_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_order_status_log`
--

CREATE TABLE `tbl_order_status_log` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_package`
--

CREATE TABLE `tbl_package` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_packages`
--

CREATE TABLE `tbl_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_products`
--

CREATE TABLE `tbl_products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_products`
--

INSERT INTO `tbl_products` (`id`, `name`, `description`, `price`, `image_url`, `category_id`, `stock`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Check', 'ASD', 1000.00, 'uploads/products/product_67df3689beab3.png', 2, 100, 'active', '2025-03-22 22:15:37', '2025-03-22 22:15:37');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_services`
--

CREATE TABLE `tbl_services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_services`
--

INSERT INTO `tbl_services` (`service_id`, `service_name`, `icon_class`, `description`, `price`, `duration`, `status`, `created_at`) VALUES
(1, 'Oil Change', NULL, 'Complete oil change service with filter replacement', 490.99, '30 minutes', 'active', '2025-03-10 04:35:20'),
(2, 'Tire Rotation', NULL, 'Rotate and balance all tires', 290.99, '45 minutes', 'active', '2025-03-10 04:35:20'),
(3, 'Brake Service', NULL, 'Brake pad replacement and rotor inspection', 1909.99, '2 hours', 'active', '2025-03-10 04:35:20'),
(4, 'Engine Tune-up', NULL, 'Comprehensive engine maintenance and optimization', 1490.99, '1.5 hours', 'active', '2025-03-10 04:35:20'),
(5, 'Air Conditioning Service', NULL, 'AC system check and recharge', 890.99, '1 hour', 'active', '2025-03-10 04:35:20'),
(6, 'Oil Change', NULL, 'Complete oil change service with filter replacement', 490.99, '30 minutes', 'active', '2025-03-10 04:37:17'),
(7, 'Tire Rotation', NULL, 'Rotate and balance all tires', 209.99, '45 minutes', 'active', '2025-03-10 04:37:17'),
(8, 'Brake Service', NULL, 'Brake pad replacement and rotor inspection', 199.99, '2 hours', 'active', '2025-03-10 04:37:17'),
(9, 'Air Conditioning Repair', NULL, 'Ensure your vehicle stays cool with our expert air conditioning repair services.', 909.99, '1-2 hours', 'active', '2025-03-10 19:53:00'),
(10, 'Brake Repair', 'fas fa-car', 'Your safety on the road is our priority. We offer comprehensive brake repair and replacement services.', 1409.99, '1-3 hours', 'active', '2025-03-10 19:53:00'),
(11, 'Engine Tune-Up', NULL, 'Keep your engine running smoothly with our comprehensive tune-up services.', 1290.99, '1-2 hours', 'active', '2025-03-10 19:53:00'),
(13, 'Tow Truck', 'fas fa-brake-system', 'Starting price', 2500.00, '1hr', 'active', '2025-03-22 23:50:07'),
(14, 'Air Conditioning Service', 'fas fa-snowflake', 'Expert AC system diagnosis and repair, including refrigerant leaks and compressor issues.', 150.00, '2-3 hours', 'active', '2025-03-23 00:00:25'),
(15, 'Brake System Service', NULL, 'Complete brake service including pad replacement, rotor resurfacing, and hydraulic system repair.', 200.00, '2-4 hours', 'active', '2025-03-23 00:00:25'),
(16, 'Engine Tune-Up', 'fas fa-cog', 'Complete engine tune-up service to maintain optimal performance and efficiency.', 180.00, '3-4 hours', 'active', '2025-03-23 00:00:25');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_service_history`
--

CREATE TABLE `tbl_service_history` (
  `history_id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tiktok_videos`
--

CREATE TABLE `tbl_tiktok_videos` (
  `id` int(11) NOT NULL,
  `video_url` varchar(255) NOT NULL,
  `video_id` varchar(100) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_tiktok_videos`
--

INSERT INTO `tbl_tiktok_videos` (`id`, `video_url`, `video_id`, `title`, `description`, `created_at`) VALUES
(1, 'https://www.tiktok.com/@daglas240/video/7251346532165864710?is_from_webapp=1&sender_device=pc&web_id=7475727632627140101', '7251346532165864710', 'GPS', 'SWAP GPS', '2025-03-10 12:13:39');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `new_img_name` varchar(255) DEFAULT NULL,
  `car_brand` varchar(100) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  `phonenum` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `name`, `email`, `new_img_name`, `car_brand`, `role`, `phonenum`, `password`, `created_at`) VALUES
(1, 'Admin User', 'admin@automotive.com', 'IMG-67ceba77a88b02.67896166.png', 'Toyota', 'admin', '912143538', '25d55ad283aa400af464c76d713c07ad', '2025-03-10 04:36:22'),
(4, 'Daglas', 'daglasmohamed@gmail.com', 'IMG-67cf1b9dec7779.16846842.png', 'Remote', 'user', '924067895', '25d55ad283aa400af464c76d713c07ad', '2025-03-10 17:04:29'),
(5, 'Jak Bekele', 'jakbekelehaile@gmail.com', 'IMG-67df37c5986962.87265902.png', 'Remote', 'user', '924067895', '25d55ad283aa400af464c76d713c07ad', '2025-03-22 22:20:53');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vehicles`
--

CREATE TABLE `tbl_vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `make` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `license_plate` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_service_date` date DEFAULT NULL,
  `service_history_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_wishlist`
--

CREATE TABLE `tbl_wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_worker`
--

CREATE TABLE `tbl_worker` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `position` enum('worker') NOT NULL DEFAULT 'worker',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_worker`
--

INSERT INTO `tbl_worker` (`id`, `full_name`, `username`, `password`, `position`, `created_at`, `image_url`) VALUES
(1, 'Hailab', 'Hailab', '$2y$10$LWB7RTNWlZVHc.mZFGtq1OVeETSTPofnLM.3oEGjynGY39EAw5YaC', 'worker', '2025-03-21 23:34:51', 'uploads/workers/67ddf79b70f33.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `car_brands`
--
ALTER TABLE `car_brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brand_name` (`brand_name`);

--
-- Indexes for table `car_models`
--
ALTER TABLE `car_models`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_model_per_brand` (`brand_id`,`model_name`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `tbl_ads`
--
ALTER TABLE `tbl_ads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_appointments`
--
ALTER TABLE `tbl_appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `worker_id` (`worker_id`);

--
-- Indexes for table `tbl_appointment_images`
--
ALTER TABLE `tbl_appointment_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `tbl_articles`
--
ALTER TABLE `tbl_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `author` (`author`);

--
-- Indexes for table `tbl_article_categories`
--
ALTER TABLE `tbl_article_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_blog`
--
ALTER TABLE `tbl_blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_blog_comments`
--
ALTER TABLE `tbl_blog_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_blog_likes`
--
ALTER TABLE `tbl_blog_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`blog_id`,`user_id`);

--
-- Indexes for table `tbl_cart`
--
ALTER TABLE `tbl_cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tbl_categories`
--
ALTER TABLE `tbl_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `tbl_info`
--
ALTER TABLE `tbl_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_message`
--
ALTER TABLE `tbl_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `tbl_notification_preferences`
--
ALTER TABLE `tbl_notification_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_admin_type` (`admin_id`,`notification_type`);

--
-- Indexes for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tbl_order_status_log`
--
ALTER TABLE `tbl_order_status_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `tbl_package`
--
ALTER TABLE `tbl_package`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_packages`
--
ALTER TABLE `tbl_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_products`
--
ALTER TABLE `tbl_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbl_services`
--
ALTER TABLE `tbl_services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `tbl_service_history`
--
ALTER TABLE `tbl_service_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `tbl_tiktok_videos`
--
ALTER TABLE `tbl_tiktok_videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tbl_vehicles`
--
ALTER TABLE `tbl_vehicles`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_wishlist`
--
ALTER TABLE `tbl_wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tbl_worker`
--
ALTER TABLE `tbl_worker`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `car_brands`
--
ALTER TABLE `car_brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `car_models`
--
ALTER TABLE `car_models`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_ads`
--
ALTER TABLE `tbl_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tbl_appointments`
--
ALTER TABLE `tbl_appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_appointment_images`
--
ALTER TABLE `tbl_appointment_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_articles`
--
ALTER TABLE `tbl_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_article_categories`
--
ALTER TABLE `tbl_article_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_blog`
--
ALTER TABLE `tbl_blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_blog_comments`
--
ALTER TABLE `tbl_blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_blog_likes`
--
ALTER TABLE `tbl_blog_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_cart`
--
ALTER TABLE `tbl_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_categories`
--
ALTER TABLE `tbl_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_info`
--
ALTER TABLE `tbl_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_message`
--
ALTER TABLE `tbl_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_notification_preferences`
--
ALTER TABLE `tbl_notification_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_order_status_log`
--
ALTER TABLE `tbl_order_status_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_package`
--
ALTER TABLE `tbl_package`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_packages`
--
ALTER TABLE `tbl_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_products`
--
ALTER TABLE `tbl_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_services`
--
ALTER TABLE `tbl_services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_service_history`
--
ALTER TABLE `tbl_service_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_tiktok_videos`
--
ALTER TABLE `tbl_tiktok_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_vehicles`
--
ALTER TABLE `tbl_vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_wishlist`
--
ALTER TABLE `tbl_wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_worker`
--
ALTER TABLE `tbl_worker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `car_models`
--
ALTER TABLE `car_models`
  ADD CONSTRAINT `car_models_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `car_brands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `tbl_admin` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_appointments`
--
ALTER TABLE `tbl_appointments`
  ADD CONSTRAINT `tbl_appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`),
  ADD CONSTRAINT `tbl_appointments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `tbl_services` (`service_id`),
  ADD CONSTRAINT `tbl_appointments_ibfk_3` FOREIGN KEY (`worker_id`) REFERENCES `tbl_worker` (`id`);

--
-- Constraints for table `tbl_appointment_images`
--
ALTER TABLE `tbl_appointment_images`
  ADD CONSTRAINT `tbl_appointment_images_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `tbl_appointments` (`appointment_id`);

--
-- Constraints for table `tbl_articles`
--
ALTER TABLE `tbl_articles`
  ADD CONSTRAINT `tbl_articles_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `tbl_article_categories` (`id`),
  ADD CONSTRAINT `tbl_articles_ibfk_2` FOREIGN KEY (`author`) REFERENCES `tbl_admin` (`id`);

--
-- Constraints for table `tbl_cart`
--
ALTER TABLE `tbl_cart`
  ADD CONSTRAINT `tbl_cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`),
  ADD CONSTRAINT `tbl_cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tbl_products` (`id`);

--
-- Constraints for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD CONSTRAINT `tbl_feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`),
  ADD CONSTRAINT `tbl_feedback_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `tbl_services` (`service_id`);

--
-- Constraints for table `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  ADD CONSTRAINT `tbl_notifications_ibfk_1` FOREIGN KEY (`recipient_id`) REFERENCES `tbl_admin` (`id`);

--
-- Constraints for table `tbl_notification_preferences`
--
ALTER TABLE `tbl_notification_preferences`
  ADD CONSTRAINT `tbl_notification_preferences_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `tbl_admin` (`id`);

--
-- Constraints for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD CONSTRAINT `tbl_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`),
  ADD CONSTRAINT `tbl_orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tbl_products` (`id`);

--
-- Constraints for table `tbl_order_status_log`
--
ALTER TABLE `tbl_order_status_log`
  ADD CONSTRAINT `tbl_order_status_log_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tbl_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_order_status_log_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `tbl_admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_products`
--
ALTER TABLE `tbl_products`
  ADD CONSTRAINT `tbl_products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `tbl_service_history`
--
ALTER TABLE `tbl_service_history`
  ADD CONSTRAINT `tbl_service_history_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `tbl_vehicles` (`vehicle_id`),
  ADD CONSTRAINT `tbl_service_history_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `tbl_services` (`service_id`);

--
-- Constraints for table `tbl_vehicles`
--
ALTER TABLE `tbl_vehicles`
  ADD CONSTRAINT `tbl_vehicles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`);

--
-- Constraints for table `tbl_wishlist`
--
ALTER TABLE `tbl_wishlist`
  ADD CONSTRAINT `tbl_wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`),
  ADD CONSTRAINT `tbl_wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tbl_products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
