-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2026 at 04:54 AM
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
-- Database: `scholarhub`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `action_type` varchar(100) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) NOT NULL,
  `action_details` text NOT NULL,
  `action_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` int(10) UNSIGNED NOT NULL,
  `scholar_id` int(10) UNSIGNED NOT NULL,
  `billing_period` varchar(50) NOT NULL,
  `amount_due` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('pending','paid','overdue') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `posted_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(11) UNSIGNED NOT NULL,
  `receiver_id` int(11) UNSIGNED NOT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(3, '2025-09-04-142111', 'App\\Database\\Migrations\\CreateSchools', 'default', 'App', 1758027920, 1),
(4, '2025-09-04-142151', 'App\\Database\\Migrations\\CreateUsers', 'default', 'App', 1758027920, 1),
(5, '2025-10-09-134759', 'App\\Database\\Migrations\\UpdateScholarsTable', 'default', 'App', 1760017721, 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `bill_id` int(10) UNSIGNED NOT NULL,
  `status_update` enum('pending','paid','overdue') NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `update_date` datetime NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scholars`
--

CREATE TABLE `scholars` (
  `id` int(10) UNSIGNED NOT NULL,
  `school_id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `course` varchar(100) NOT NULL,
  `year_level` int(11) NOT NULL,
  `status` enum('active','on-hold','graduated','disqualified') NOT NULL,
  `date_of_birth` date NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholars`
--

INSERT INTO `scholars` (`id`, `school_id`, `first_name`, `last_name`, `middle_name`, `gender`, `course`, `year_level`, `status`, `date_of_birth`, `email`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'wilmar ', 'cabigas', 'libando ', 'male', 'BSIT', 4, 'active', '2003-04-09', 'wcabigas.shs@gmail.com', '2025-12-01 14:32:53', '2025-12-16 08:26:16', '2025-12-16 08:26:16'),
(2, 1, 'asd', 'asdagh', 'KAS', 'male', 'BSIT', 4, 'active', '2003-12-09', 'wilmarl.shs@gmail.com', '2025-12-01 15:03:49', '2025-12-16 08:26:23', '2025-12-16 08:26:23'),
(3, 4, 'wilmar', 'asd', 'lagnas', 'male', 'BSIT', 4, 'active', '2003-03-09', '1james@gmail.com', '2025-12-01 16:24:55', '2025-12-19 05:42:55', NULL),
(4, 4, 'kenjie', 'lagnas', 'manego', 'male', 'BSIT', 4, 'active', '2001-11-05', 'kenjie@gmail.com', '2025-12-19 05:19:22', '2025-12-19 05:20:44', '2025-12-19 05:20:44'),
(7, 4, 'MARK', 'cabigas', 'manego', 'male', 'BSIT', 2, 'active', '2001-11-05', 'junfall@gmail.com', '2025-12-19 05:21:49', '2025-12-19 05:41:18', '2025-12-19 05:41:18'),
(12, 4, 'junfall', 'cabigas', 'manego', 'male', 'BSIT', 2, 'active', '2001-11-05', 'junfall@gmail.com', '2025-12-19 05:40:38', '2025-12-19 05:41:20', '2025-12-19 05:41:20'),
(13, 4, 'MARK', 'cabigas', 'manego', 'male', 'BSIT', 2, 'active', '2001-11-05', 'junfall@gmail.com', '2025-12-19 05:41:10', '2025-12-19 05:41:21', '2025-12-19 05:41:21'),
(14, 4, 'MARK', 'cabigas', 'manego', 'male', 'BSIT', 2, 'active', '2001-11-05', 'junfall@gmail.com', '2025-12-19 05:41:11', '2025-12-19 05:41:21', '2025-12-19 05:41:21'),
(15, 4, 'wilmar', 'asd', 'lagnas', 'male', 'BSIT', 4, 'active', '2003-03-09', '1james@gmail.com', '2025-12-19 05:42:56', '2025-12-19 06:00:03', '2025-12-19 06:00:03'),
(16, 4, 'kenjie', 'asd', 'lagnas', 'male', 'BSIT', 4, 'active', '2003-03-09', '1james@gmail.com', '2025-12-19 05:44:43', '2025-12-19 06:00:10', NULL),
(17, 4, 'clint', 'lagnas', 'cabigas', 'male', 'BSHM', 1, 'active', '2003-11-25', 'james@gmail.com', '2025-12-19 06:00:58', '2025-12-19 06:41:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `address` text NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `name`, `code`, `created_at`, `updated_at`, `deleted_at`, `address`, `contact_person`, `contact_email`, `contact_number`) VALUES
(1, 'Cebu Eastern College', 'CEC', '2026-03-07 03:29:00', '2025-12-19 03:18:48', '2025-12-19 03:18:48', '', '', '', ''),
(2, 'University of San Jose–Recoletos', 'usjr', '2025-12-16 10:16:19', '2025-12-19 03:07:37', '2025-12-19 03:07:37', '', '', '', ''),
(3, 'Cebu Eastern College', 'cec', '2025-12-19 03:09:12', '2025-12-19 03:13:32', '2025-12-19 03:13:32', 'lowersouthhillstisa cebu city ', 'wilmar', 'wcabigas@gmail.com', '0912534234'),
(4, 'Cebu Eastern College', 'cece', '2025-12-19 03:14:08', '2025-12-19 05:23:23', NULL, 'asdasdasd', 'james', 'j@gmail.com', '0912352345235'),
(5, 'Cebu Eastern College', 'CEC', '2026-03-03 06:24:32', '2026-03-03 06:42:47', '2026-03-03 06:42:47', '', '', '', ''),
(6, 'Cebu Eastern College', 'CEC', '2026-03-03 06:24:56', '2026-03-03 06:42:45', '2026-03-03 06:42:45', '', '', '', ''),
(7, 'Cebu Eastern College', 'CEC', '2026-03-03 06:25:08', '2026-03-03 06:42:41', '2026-03-03 06:42:41', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(191) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(191) NOT NULL,
  `role` varchar(32) NOT NULL DEFAULT 'staff',
  `school_id` int(10) UNSIGNED DEFAULT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `unlock_code` varchar(10) DEFAULT NULL,
  `login_code` varchar(6) DEFAULT NULL,
  `login_code_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `full_name`, `role`, `school_id`, `status`, `last_login_at`, `created_at`, `updated_at`, `deleted_at`, `failed_attempts`, `unlock_code`, `login_code`, `login_code_expires_at`) VALUES
(2, 'staff@cebu-scholar.gov', '$2y$10$3RnwkVyz82094NH7NsScsevU4PkevFh41ku9CNiJSxOXf7ymLDT7G', 'Office Staff', 'staff', NULL, 'active', '2026-03-03 06:47:58', '2026-03-03 06:40:24', '2026-03-03 06:47:58', NULL, 0, NULL, NULL, NULL),
(3, 'schooladmin@cec.edu.ph', '$2y$10$40cFh0LKOLnhjDicBFCyOObO5N3PjKzuqgM/W7wr8654wHMWDxhMe', 'CEC School Admin', 'school_admin', 1, 'active', '2025-10-27 03:47:40', '2026-03-07 03:29:00', '2026-03-03 05:30:35', '2026-03-03 05:30:35', 0, NULL, NULL, NULL),
(4, 'schoolstaff@cec.edu.ph', '$2y$10$WS7PjhCdBLTYDCBn7jUBXe0RIiUnIxV0Sz52vfHE8lRqJQs0V2sUe', 'CEC School Staff', 'school_staff', 1, 'active', '2025-12-16 11:39:41', '2026-03-07 03:29:00', '2026-03-03 06:59:43', '2026-03-03 06:59:43', 0, NULL, NULL, NULL),
(5, 'scholar1@students.ph', '$2y$10$2AqYjpfnXE4PxKUseZ/KW.X2wVcpiXqowDS4r8Bdsd5gGB/xoL2ly', 'Juan Dela Cruz', 'scholar', NULL, 'active', '2025-09-16 13:31:35', '2026-03-07 03:29:01', '2026-03-03 06:59:25', '2026-03-03 06:59:25', 0, NULL, NULL, NULL),
(6, 'asd@gmail.com', '', 'asd asd ', 'admin', NULL, 'active', NULL, '2025-10-03 11:47:06', '2025-10-03 11:47:21', '2025-10-03 11:47:21', 0, NULL, NULL, NULL),
(7, 'james123@gmail.com', '', 'james lagnas', 'school_admin', 4, 'active', NULL, '2026-03-03 05:29:06', '2026-03-03 05:30:25', '2026-03-03 05:30:25', 0, NULL, NULL, NULL),
(8, 'JamesLagnas@cebueasterncollege.edu.ph', '', 'james lagnas', 'school_admin', 4, 'active', NULL, '2026-03-03 05:31:55', '2026-03-03 05:33:36', '2026-03-03 05:33:36', 0, NULL, NULL, NULL),
(9, 'Jamesladas@cebueasterncollege.edu.ph', '', 'james lagnas', 'school_admin', 4, 'active', NULL, '2026-03-03 05:34:51', '2026-03-03 06:59:01', '2026-03-03 06:59:01', 0, NULL, NULL, NULL),
(10, 'James@cebueasterncollege.edu.ph', '$2y$10$wxEwWr3uDRU3/Oj./KPTL.QMBooWGXQ6URM.ON3HE/xQq1PrnwRc2', 'james lagnas', 'school_admin', 4, 'active', NULL, '2026-03-03 05:45:06', '2026-03-03 06:59:05', '2026-03-03 06:59:05', 0, NULL, NULL, NULL),
(11, 'wilmar@gmail.com', '$2y$10$qD6EgQon0HpFGQRIo5SGZOSP0dcoorQhJdY5sm.vhKOj2ju.1Mrf6', 'wilmarcabigas', 'school_staff', 4, 'active', NULL, '2026-03-03 05:51:28', '2026-03-03 06:59:56', '2026-03-03 06:59:56', 0, NULL, NULL, NULL),
(12, 'kenjie123@gmail.com', '$2y$10$IQj.ibqm24c.ZAG1laln0OxRnhMtEAsuf4/EkjIsE/dUGITKjBWdC', 'Kenjie Pugoy', 'school_staff', 4, 'active', '2026-03-03 06:55:45', '2026-03-03 05:52:44', '2026-03-03 06:55:45', NULL, 0, NULL, NULL, NULL),
(23, 'wilmarshs@gmail.com', '$2y$10$V7vaQhbhuZE2At5uFwhqhe6NGJtPeHxQ1FTElqyuQ4Sp6HecF96Ga', 'wilmarcabigas', 'school_admin', 4, 'active', '2026-03-03 07:02:00', '2026-03-03 07:01:37', '2026-03-05 14:04:52', NULL, 3, '845863', NULL, NULL),
(27, 'cwilmar.shs@gmail.com', '$2y$10$wnf1JtEarlXLnRA0Rdx4g.2vXw5KUBJs6aVCx1DohFKESluVl2TgG', 'wilmar cabigas', 'school_admin', 4, 'active', '2026-03-05 14:12:08', '2026-03-05 14:04:30', '2026-03-05 14:12:59', NULL, 0, NULL, NULL, NULL),
(28, 'jamestrocio842@gmail.com', '$2y$10$wIfMcD1xgcWHVZ.ju7UOtOr6qfDrofunFIU.4zMYItEDocu49f6IO', 'System Admin', 'admin', NULL, 'active', '2026-03-07 03:46:06', '2026-03-07 03:29:00', '2026-03-07 03:46:06', NULL, 0, NULL, NULL, NULL),
(29, 'trocioj914@gmail.com', '$2y$10$8fZhIWyU72tFX/wuQd0eyufpMePEAl3Ym32WCzWY40RESrEdo4lv6', 'Office Staff', 'staff', NULL, 'active', NULL, '2026-03-07 03:29:00', NULL, NULL, 0, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_logs_user` (`user_id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bills_scholar` (`scholar_id`),
  ADD KEY `fk_bills_posted_by` (`posted_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_user` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payments_bill` (`bill_id`),
  ADD KEY `fk_payments_updated_by` (`updated_by`);

--
-- Indexes for table `scholars`
--
ALTER TABLE `scholars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_scholars_schools` (`school_id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `users_school_id_foreign` (`school_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scholars`
--
ALTER TABLE `scholars`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `fk_bills_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_bills_scholar` FOREIGN KEY (`scholar_id`) REFERENCES `scholars` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_bill` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`),
  ADD CONSTRAINT `fk_payments_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `scholars`
--
ALTER TABLE `scholars`
  ADD CONSTRAINT `fk_scholars_schools` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
