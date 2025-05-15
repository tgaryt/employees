-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 15, 2025 at 11:32 AM
-- Server version: 11.7.2-MariaDB-deb12
-- PHP Version: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tgaryt`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','manager','hr') NOT NULL DEFAULT 'hr',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$ZMBb8kVKvrXJPcBlhZUT/uIwWxMQSEBJ7S5Aj2cRWmL.oJ93ZdMDa', 'admin@ez-ad.com', 'System Administrator', 'admin', 1, NULL, '2025-05-15 10:46:27', '2025-05-15 10:46:27');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `job_position` varchar(100) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `location` varchar(100) NOT NULL,
  `residential_address` text DEFAULT NULL,
  `payment_method` text DEFAULT NULL,
  `employment_type` varchar(50) NOT NULL,
  `work_hours_per_day` int(11) NOT NULL,
  `work_schedule` text DEFAULT NULL,
  `pst_work_hours` varchar(100) DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `local_cell_number` varchar(20) DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `start_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `first_name`, `last_name`, `email`, `job_position`, `salary`, `currency`, `location`, `residential_address`, `payment_method`, `employment_type`, `work_hours_per_day`, `work_schedule`, `pst_work_hours`, `passport_number`, `local_cell_number`, `emergency_contact`, `start_date`, `created_at`, `updated_at`) VALUES
(1, 'Ahmed', 'Khalid', 'ahmed@example.com', 'Full Stack Developer', 2000.00, 'USD', 'Online', 'Irbid, Jordan', 'Bank transfer, details will be provided next week', 'Monthly', 8, 'Monday through Friday', '06:00 AM - 3:00 PM PST', NULL, '+962 791802238', '+962 799390614', '2025-05-15', '2025-05-15 10:46:28', '2025-05-15 10:46:28'),
(2, 'Sarah', 'Johnson', 'sarah@example.com', 'UI/UX Designer', 1800.00, 'USD', 'Online', 'Amman, Jordan', 'Bank transfer', 'Monthly', 8, 'Monday through Friday', '07:00 AM - 4:00 PM PST', 'P12345678', '+962 791234567', '+962 798765432', '2025-05-10', '2025-05-15 10:46:28', '2025-05-15 10:46:28'),
(3, 'Mohamed', 'Ali', 'mohamed@example.com', 'Project Manager', 2500.00, 'USD', 'Online', 'Cairo, Egypt', 'International wire transfer', 'Monthly', 8, 'Monday through Friday', '08:00 AM - 5:00 PM PST', 'A87654321', '+20 1012345678', '+20 1098765432', '2025-05-01', '2025-05-15 10:46:28', '2025-05-15 10:46:28'),
(4, 'ff', 'ff', 'ff@gg.om', 'f', 222.00, 'USD', 'dfasdf', 'fasdfa', 'dfsada', 'Full-time', 8, 'fasd', 'fas', 'ff', 'dfa', 'sdfasdf', '2025-05-15', '2025-05-15 11:16:39', '2025-05-15 11:16:39');

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `document_type` enum('offer_letter','id_front','id_back') NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_documents`
--

INSERT INTO `employee_documents` (`id`, `employee_id`, `document_type`, `file_name`, `file_path`, `upload_date`) VALUES
(1, 4, 'offer_letter', 'Mohammed Tubaishat CV.pdf', '../../assets/uploads/offer_letters/6825d00b10e38.pdf', '2025-05-15 11:29:15');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'company_name', 'EZ-AD', 'Company name displayed in the system', NULL, '2025-05-15 10:46:27'),
(2, 'company_email', 'info@ez-ad.com', 'Default company email address', NULL, '2025-05-15 10:46:27'),
(3, 'upload_max_size', '5242880', 'Maximum file upload size in bytes (5MB)', NULL, '2025-05-15 10:46:27'),
(4, 'session_timeout', '1800', 'Session timeout in seconds (30 minutes)', NULL, '2025-05-15 10:46:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `updated_by` (`updated_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `employee_documents_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
