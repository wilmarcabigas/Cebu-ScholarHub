-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2026 at 03:10 AM
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `scholar_id`, `billing_period`, `amount_due`, `due_date`, `status`, `remarks`, `posted_by`, `created_at`, `updated_at`) VALUES
(1, 3, 'asdasd', 2000.00, '2026-02-03', 'paid', 'asdasd', 11, '2026-01-30 06:12:27', '2026-01-30 06:24:28'),
(2, 19, 'january 2025', 2000.00, '2026-01-31', 'paid', 'needed', 11, '2026-01-30 07:15:17', '2026-01-30 07:17:21'),
(3, 20, 'january 2025', 2000.00, '2026-01-31', 'paid', 'qSFASFS', 11, '2026-01-30 07:23:43', '2026-01-30 07:24:24'),
(4, 19, 'jUANY', 0.00, '2026-01-14', 'paid', 'ASDASDASD', 11, '2026-01-30 08:49:57', '2026-01-30 09:29:26'),
(5, 19, 'jUANY', 2000.00, '2026-01-21', '', '12312', 11, '2026-01-30 09:08:05', '2026-01-30 09:22:47'),
(6, 19, 'january 2025', 5000.00, '2026-01-06', 'paid', '12312', 11, '2026-01-30 09:31:03', '2026-01-30 09:31:14'),
(7, 20, 'january 2025', 1000.00, '2026-01-14', '', '12321', 11, '2026-01-30 09:32:01', '2026-01-30 09:38:04'),
(8, 19, 'jUANY', 0.00, '2026-01-10', 'paid', '12312', 11, '2026-01-30 09:39:07', '2026-01-30 09:43:05'),
(9, 19, 'january 2025', 200.00, '2025-12-31', '', '123', 11, '2026-01-30 09:39:56', '2026-01-30 10:09:43'),
(10, 19, 'january 2025', 10000.00, '2026-01-18', 'paid', '12321', 11, '2026-01-30 09:51:33', '2026-01-30 09:52:22'),
(11, 20, 'january 2025', 10000.00, '2026-01-02', 'paid', '123213', 11, '2026-01-30 09:52:42', '2026-01-30 09:54:13'),
(12, 19, 'january 2025', 200.00, '2026-01-12', 'paid', '12321', 11, '2026-01-30 09:56:31', '2026-01-30 10:09:12'),
(13, 19, 'asdasd', 2000.00, '2026-01-07', '', '12312', 11, '2026-01-30 10:19:55', '2026-02-25 09:53:17'),
(14, 19, 'january 2025', 800.00, '2026-02-01', '', 'asfhg', 11, '2026-01-31 02:33:25', '2026-01-31 02:34:01'),
(15, 20, '151', 10000.00, '2026-01-08', 'pending', '12312', 11, '2026-01-31 05:10:37', '2026-01-31 05:10:37'),
(16, 22, 'first sem', 5000.00, '2003-12-03', '', 'asggshdfhf', 12, '2026-02-20 09:07:08', '2026-02-20 09:07:27'),
(17, 20, 'january 2025', 0.00, '2026-02-27', 'paid', '', 11, '2026-02-25 03:17:16', '2026-02-25 03:17:57');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `receiver_id` int(10) UNSIGNED NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message_body` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp()
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
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `update_date` datetime NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL
) ;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `bill_id`, `amount_paid`, `payment_date`, `updated_by`, `update_date`, `remarks`) VALUES
(1, 1, 200.00, '2026-02-04', 11, '2026-01-30 14:24:28', 'asdasdasd'),
(2, 2, 1000.00, '2026-01-31', 11, '2026-01-30 15:16:40', 'done'),
(3, 2, 1000.00, '2026-01-31', 11, '2026-01-30 15:17:21', 'needed'),
(4, 3, 1000.00, '2026-01-31', 11, '2026-01-30 15:23:59', '58858WDFS'),
(5, 3, 2000.00, '2026-01-31', 11, '2026-01-30 15:24:24', 'UHKHJ'),
(6, 4, 10000.00, '2026-01-30', 11, '2026-01-30 16:50:31', 'ASDSADAS'),
(7, 4, 5000.00, '2026-01-14', 11, '2026-01-30 17:04:53', '13123'),
(8, 5, 5000.00, '2026-01-10', 11, '2026-01-30 17:08:17', '123123'),
(9, 4, 10000.00, '2026-01-15', 11, '2026-01-30 17:08:47', '123123'),
(10, 5, 3000.00, '2026-01-04', 11, '2026-01-30 17:22:47', '123123'),
(11, 4, 5000.00, '2026-01-06', 11, '2026-01-30 17:27:50', '12321'),
(12, 4, 3000.00, '2026-01-07', 11, '2026-01-30 17:28:40', '123123'),
(13, 4, 2000.00, '2026-01-07', 11, '2026-01-30 17:29:26', ''),
(14, 6, 5000.00, '2026-01-22', 11, '2026-01-30 17:31:14', '12312312'),
(15, 7, 5000.00, '2026-01-19', 11, '2026-01-30 17:32:13', '123213'),
(16, 7, 3000.00, '2026-01-05', 11, '2026-01-30 17:35:02', '12312'),
(17, 7, 1000.00, '2026-01-12', 11, '2026-01-30 17:38:04', ''),
(18, 8, 5000.00, '2026-01-02', 11, '2026-01-30 17:39:22', '123'),
(19, 9, 5000.00, '2026-01-21', 11, '2026-01-30 17:41:28', '123'),
(20, 8, 5000.00, '2026-01-06', 11, '2026-01-30 17:43:05', '12312'),
(21, 10, 5000.00, '2026-01-14', 11, '2026-01-30 17:51:43', '23323'),
(22, 10, 5000.00, '2026-01-06', 11, '2026-01-30 17:52:22', '123213'),
(23, 11, 5000.00, '2026-01-13', 11, '2026-01-30 17:52:50', '1321'),
(24, 11, 5000.00, '2026-01-10', 11, '2026-01-30 17:54:13', '123'),
(25, 12, 5000.00, '2026-01-08', 11, '2026-01-30 17:56:40', '12312'),
(26, 12, 3000.00, '2026-01-14', 11, '2026-01-30 18:03:47', '12312'),
(27, 12, 1000.00, '2026-01-08', 11, '2026-01-30 18:04:06', ''),
(28, 12, 500.00, '2026-01-15', 11, '2026-01-30 18:08:24', '123'),
(29, 12, 500.00, '2026-01-15', 11, '2026-01-30 18:08:40', '123'),
(30, 12, 300.00, '2026-01-09', 11, '2026-01-30 18:09:12', '123123'),
(31, 9, 4800.00, '2026-01-05', 11, '2026-01-30 18:09:43', '12321'),
(32, 13, 3000.00, '2026-01-02', 11, '2026-01-30 18:20:03', '123'),
(33, 13, 1000.00, '2026-01-09', 11, '2026-01-30 18:20:36', '123123'),
(34, 14, 1000.00, '2026-01-31', 11, '2026-01-31 10:33:45', 'sggsdf'),
(35, 14, 200.00, '2026-01-31', 11, '2026-01-31 10:34:01', 'sdgsg'),
(36, 16, 5000.00, '2026-02-19', 12, '2026-02-20 17:07:27', ''),
(37, 17, 5000.00, '2026-02-26', 11, '2026-02-25 11:17:43', ''),
(38, 17, 5000.00, '2026-02-27', 11, '2026-02-25 11:17:57', ''),
(39, 13, 2000.00, '2026-02-26', 11, '2026-02-25 17:53:17', '');

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
(3, 5, 'wilmar', 'asd', 'lagnas', 'male', 'BSIT', 4, 'active', '2003-03-09', '1james@gmail.com', '2025-12-01 16:24:55', '2026-02-24 14:40:04', NULL),
(4, 4, 'kenjie', 'lagnas', 'manego', 'male', 'BSIT', 4, 'active', '2001-11-05', 'kenjie@gmail.com', '2025-12-19 05:19:22', '2025-12-19 05:20:44', '2025-12-19 05:20:44'),
(7, 4, 'MARK', 'cabigas', 'manego', 'male', 'BSIT', 2, 'active', '2001-11-05', 'junfall@gmail.com', '2025-12-19 05:21:49', '2025-12-19 05:41:18', '2025-12-19 05:41:18'),
(12, 4, 'junfall', 'cabigas', 'manego', 'male', 'BSIT', 2, 'active', '2001-11-05', 'junfall@gmail.com', '2025-12-19 05:40:38', '2025-12-19 05:41:20', '2025-12-19 05:41:20'),
(13, 4, 'MARK', 'cabigas', 'manego', 'male', 'BSIT', 2, 'active', '2001-11-05', 'junfall@gmail.com', '2025-12-19 05:41:10', '2025-12-19 05:41:21', '2025-12-19 05:41:21'),
(14, 4, 'MARK', 'cabigas', 'manego', 'male', 'BSIT', 2, 'active', '2001-11-05', 'junfall@gmail.com', '2025-12-19 05:41:11', '2025-12-19 05:41:21', '2025-12-19 05:41:21'),
(15, 4, 'wilmar', 'asd', 'lagnas', 'male', 'BSIT', 4, 'active', '2003-03-09', '1james@gmail.com', '2025-12-19 05:42:56', '2025-12-19 06:00:03', '2025-12-19 06:00:03'),
(16, 4, 'kenjie', 'asd', 'lagnas', 'male', 'BSIT', 4, 'active', '2003-03-09', '1james@gmail.com', '2025-12-19 05:44:43', '2026-02-24 14:40:17', NULL),
(17, 4, 'clint', 'lagnas', 'cabigas', 'male', 'BSHM', 1, 'active', '2003-11-25', 'james@gmail.com', '2025-12-19 06:00:58', '2025-12-19 06:41:12', NULL),
(18, 1, 'wilmar ', 'nacua', 'cabigas', 'male', 'BSIT', 4, 'active', '2003-10-09', 'wcabigas.shs@gmail.com', '2026-01-14 15:44:17', '2026-01-14 15:44:17', NULL),
(19, 5, 'wilmar ', 'lagnas', 'gabato', 'male', 'BSIT', 4, 'active', '2003-01-09', 'wilmar@gmail.com', '2026-01-29 11:20:46', '2026-01-29 11:20:46', NULL),
(20, 5, 'junfall', 'asdagh', 'cabigas', 'male', 'BSIT', 4, 'active', '2003-05-09', 'wilmarl.shs@gmail.com', '2026-01-29 11:21:13', '2026-01-29 11:21:13', NULL),
(21, 5, 'john4', 'cabigas1', NULL, 'male', 'BSIT', 4, 'active', '2003-12-09', 'J@gmail.com', '2026-02-18 08:09:19', '2026-02-18 08:13:53', NULL),
(22, 5, 'john', 'cabigas', NULL, 'male', 'BSIT', 4, 'active', '2003-01-09', 'Jhjfo@gmail.com', '2026-02-18 08:22:00', '2026-02-20 07:55:35', NULL),
(23, 5, 'john2', 'cabigas3', NULL, 'male', 'BSIT', 4, 'active', '2003-04-09', 'Jhi@gmail.com', '2026-02-18 08:22:00', '2026-02-20 07:55:35', NULL),
(24, 5, 'john3', 'cabigas4', NULL, 'male', 'BSIT', 4, 'active', '2003-07-09', 'Joh@gmail.com', '2026-02-18 08:22:00', '2026-02-20 07:55:35', NULL),
(25, 5, 'john4', 'cabigas1', NULL, 'male', 'BSIT', 4, 'active', '2003-12-09', 'Jmg@gmail.com', '2026-02-18 08:22:00', '2026-02-20 07:55:35', NULL),
(26, 7, 'lagnas', 'cabigas', NULL, 'male', 'BSIT', 4, 'active', '2003-01-09', 'jkdf@gmail.com', '2026-02-20 07:58:00', '2026-02-20 08:27:43', NULL),
(27, 7, 'james', 'cabigas3', NULL, 'male', 'BSIT', 4, 'active', '2003-04-09', 'nbxcv@gmail.com', '2026-02-20 07:58:00', '2026-02-20 08:27:43', NULL),
(28, 7, 'umpad', 'cabigas4', NULL, 'male', 'BSIT', 4, 'active', '2003-07-09', 'dfjnt@gmail.com', '2026-02-20 07:58:00', '2026-02-20 08:27:43', NULL),
(29, 7, 'manego', 'cabigas1', NULL, 'male', 'BSIT', 4, 'active', '2003-12-09', 'myujt@gmail.com', '2026-02-20 07:58:00', '2026-02-20 08:27:43', NULL);

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
(1, 'Cebu Eastern College', 'CEC', '2025-09-16 13:05:26', '2025-12-19 03:18:48', '2025-12-19 03:18:48', '', '', '', ''),
(4, 'Cebu Eastern College', 'cece', '2025-12-19 03:14:08', '2025-12-31 14:58:39', NULL, 'asdasdasd', 'james', 'j@gmail.com', '0912352345235'),
(5, 'Cebu Technological University', 'CTU', '2026-01-21 02:56:49', '2026-01-21 02:56:49', NULL, 'cebu city', 'john ', 'john@gmail.com', '09537231981'),
(7, 'Cebu Normal University', 'CNU', '2026-02-20 08:26:26', '2026-02-24 14:39:53', NULL, '                      jones Cebu City', 'kenjie manego', 'kenjie.manego@gmail.com', '09289479044');

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
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `full_name`, `role`, `school_id`, `status`, `last_login_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin@cebu-scholar.gov', '$2y$10$7w8oE.IHuZfD/ffkeZrfP.BmYi4xq2BERYieQregKKUNjJFkYkkWa', 'System Admin', 'admin', NULL, 'active', '2026-02-27 01:56:44', '2025-09-16 13:05:26', '2026-02-27 01:56:44', NULL),
(2, 'staff@cebu-scholar.gov', '$2y$10$r5VtWVhgaHFSfcNn9AXJ8OfmD8wc4lggOzUlWb4CwYBzc6nxIf8UW', 'Office Staff', 'staff', NULL, 'active', '2026-02-27 01:58:39', '2025-09-16 13:05:26', '2026-02-27 01:58:39', NULL),
(3, 'schooladmin@cec.edu.ph', '$2y$10$MGKyAhWx35SKgVAh1B4awublLaB9Sh4agWyrIuJPZZqlmVXgLYILW', 'CEC School Admin', 'school_admin', 1, 'active', '2026-01-29 11:12:13', '2025-09-16 13:05:26', '2026-01-29 11:12:13', NULL),
(4, 'schoolstaff@cec.edu.ph', '$2y$10$WF4bgD/bX882dZzbDHXqEuUR15YNrnV5MlEUE4DdmybrnfJ439mVy', 'CEC School Staff', 'school_staff', NULL, 'active', '2026-01-22 05:11:17', '2025-09-16 13:05:26', '2026-01-22 05:11:17', NULL),
(5, 'scholar1@students.ph', '$2y$10$KzHeJQTcdK13J9nyCLeeTuumBUwculPTq4hwxx.DR/FssZb8pYImG', 'Juan Dela Cruz', 'scholar', NULL, 'active', '2025-09-16 13:31:35', '2025-09-16 13:05:26', '2025-09-16 13:31:35', NULL),
(6, 'asd@gmail.com', '', 'asd asd ', 'admin', NULL, 'active', NULL, '2025-10-03 11:47:06', '2025-10-03 11:47:21', '2025-10-03 11:47:21'),
(7, 'newadmin@gmail.com', '', 'new admin', 'school_admin', NULL, 'active', NULL, '2026-01-14 16:01:18', '2026-01-21 02:33:46', '2026-01-21 02:33:46'),
(8, 'admincec@gmail.com', '', 'wilmar cabigas', 'school_admin', 4, 'active', NULL, '2026-01-21 02:26:44', '2026-01-21 02:33:48', '2026-01-21 02:33:48'),
(9, 'admincec1@gmail.com', '$2y$10$6Zskq/KdL7x6mkZvaU/C2eMp5zrZpsFO6oq8PTs6GK/PtqhDWXZiK', 'john john', 'school_admin', 4, 'active', '2026-01-22 05:07:12', '2026-01-21 02:34:45', '2026-01-22 05:07:12', NULL),
(10, 'wilmarstaff@gmail.com', '$2y$10$bOAixiDfZFMdRxdCmvW6su7EqbSDDdMe1aNt8Qyp0UUw.yJDNzieO', 'wilmar cabigas', 'school_staff', 4, 'active', '2026-01-22 03:31:34', '2026-01-22 03:31:18', '2026-01-22 03:31:58', '2026-01-22 03:31:58'),
(11, 'ctuadmin@gmail.com', '$2y$10$UjeSz/.2etDR9en6T.Wjt.qlf0do5uR6zsixrlcCbZLvDMU5UV.LS', 'wilmar', 'school_admin', 5, 'active', '2026-02-27 02:01:49', '2026-01-29 11:19:26', '2026-02-27 02:01:49', NULL),
(12, 'ctustaff@gmail.com', '$2y$10$qXQzqFjod.SReNuNM8uQG.2tazD72k8xnHx0FGoYhDOuKItEyoRta', 'john', 'school_staff', 5, 'active', '2026-02-27 02:06:38', '2026-01-29 11:19:47', '2026-02-27 02:06:38', NULL);

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
  ADD KEY `fk_bills_posted_by` (`posted_by`),
  ADD KEY `idx_bills_status` (`status`),
  ADD KEY `idx_bills_due_date` (`due_date`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_messages_sender` (`sender_id`),
  ADD KEY `fk_messages_receiver` (`receiver_id`);

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
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `idx_payments_bill_id` (`bill_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

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
