-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 15, 2025 at 10:00 PM
-- Server version: 5.7.39
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `btikp`
--

-- --------------------------------------------------------

--
-- Table structure for table `import_log`
--

CREATE TABLE `import_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `process_type` varchar(50) NOT NULL,
  `url_induk_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `total_processed` int(11) DEFAULT '0',
  `total_success` int(11) DEFAULT '0',
  `total_failed` int(11) DEFAULT '0',
  `error_message` text,
  `started_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `import_log`
--

INSERT INTO `import_log` (`id`, `user_id`, `process_type`, `url_induk_id`, `status`, `total_processed`, `total_success`, `total_failed`, `error_message`, `started_at`, `completed_at`, `updated_at`, `created_at`) VALUES
(1, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 05:46:22', NULL, NULL, '2025-09-30 22:46:22'),
(2, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 06:10:51', NULL, NULL, '2025-09-30 23:10:51'),
(3, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 07:46:38', NULL, NULL, '2025-10-01 00:46:38'),
(4, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 07:48:04', NULL, NULL, '2025-10-01 00:48:04'),
(6, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:01:34', NULL, NULL, '2025-10-01 01:01:34'),
(7, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:04:07', NULL, NULL, '2025-10-01 01:04:07'),
(8, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:04:21', NULL, NULL, '2025-10-01 01:04:21'),
(9, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:14:09', NULL, NULL, '2025-10-01 01:14:09'),
(10, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:14:16', NULL, NULL, '2025-10-01 01:14:15'),
(11, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:16:25', NULL, NULL, '2025-10-01 01:16:25'),
(12, 1, 'kabupaten', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 08:17:04', '2025-10-01 08:25:37', NULL, '2025-10-01 01:17:04'),
(13, 1, 'kabupaten', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 08:18:21', '2025-10-01 08:20:31', NULL, '2025-10-01 01:18:21'),
(14, 1, 'kabupaten', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 08:20:00', '2025-10-01 08:20:26', NULL, '2025-10-01 01:20:00'),
(15, 1, 'kabupaten', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 08:23:37', '2025-10-01 08:25:30', NULL, '2025-10-01 01:23:37'),
(16, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:25:48', NULL, NULL, '2025-10-01 01:25:48'),
(17, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:27:55', NULL, NULL, '2025-10-01 01:27:55'),
(18, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:27:56', NULL, NULL, '2025-10-01 01:27:56'),
(19, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:34:06', NULL, NULL, '2025-10-01 01:34:06'),
(20, 1, 'kabupaten', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 08:34:08', '2025-10-01 08:34:40', NULL, '2025-10-01 01:34:08'),
(21, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:37:06', NULL, NULL, '2025-10-01 01:37:06'),
(22, 1, 'kabupaten', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 08:37:09', '2025-10-01 08:37:38', NULL, '2025-10-01 01:37:09'),
(23, 1, 'kecamatan', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:38:02', NULL, NULL, '2025-10-01 01:38:02'),
(24, 1, 'kecamatan', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:44:39', NULL, NULL, '2025-10-01 01:44:39'),
(25, 1, 'kecamatan', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:48:20', NULL, NULL, '2025-10-01 01:48:20'),
(26, 1, 'kecamatan', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:49:28', NULL, NULL, '2025-10-01 01:49:28'),
(27, 1, 'kecamatan', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 08:51:19', '2025-10-01 09:03:17', NULL, '2025-10-01 01:51:19'),
(28, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 08:57:55', NULL, NULL, '2025-10-01 01:57:55'),
(29, 1, 'kabupaten', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 08:57:57', '2025-10-01 08:59:50', NULL, '2025-10-01 01:57:57'),
(30, 1, 'kecamatan', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 09:01:47', '2025-10-01 09:02:22', NULL, '2025-10-01 02:01:47'),
(31, 1, 'transfer', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 10:03:59', '2025-10-01 10:03:59', NULL, '2025-10-01 03:03:59'),
(32, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:06:41', NULL, NULL, '2025-10-01 03:06:41'),
(33, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:06:43', NULL, NULL, '2025-10-01 03:06:43'),
(34, 1, 'kecamatan', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:10:18', NULL, NULL, '2025-10-01 03:10:18'),
(35, 1, 'kecamatan', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 10:13:20', '2025-10-01 10:13:50', NULL, '2025-10-01 03:13:20'),
(36, 1, 'kecamatan', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:17:18', NULL, NULL, '2025-10-01 03:17:18'),
(37, 1, 'kecamatan', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:18:03', NULL, NULL, '2025-10-01 03:18:03'),
(38, 1, 'kecamatan', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:18:22', NULL, NULL, '2025-10-01 03:18:22'),
(39, 1, 'kecamatan', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 10:20:10', '2025-10-01 10:20:40', NULL, '2025-10-01 03:20:10'),
(40, 1, 'kecamatan', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:21:12', NULL, NULL, '2025-10-01 03:21:12'),
(41, 1, 'transfer', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 10:22:09', '2025-10-01 10:22:09', NULL, '2025-10-01 03:22:09'),
(42, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:23:45', NULL, NULL, '2025-10-01 03:23:45'),
(43, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:23:48', NULL, NULL, '2025-10-01 03:23:48'),
(44, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:24:06', NULL, NULL, '2025-10-01 03:24:06'),
(45, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:24:07', NULL, NULL, '2025-10-01 03:24:07'),
(46, 1, 'kecamatan', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:24:27', NULL, NULL, '2025-10-01 03:24:27'),
(47, 1, 'kecamatan', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:24:33', NULL, NULL, '2025-10-01 03:24:33'),
(48, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:25:26', NULL, NULL, '2025-10-01 03:25:26'),
(49, 1, 'kabupaten', 1, 'completed', 1, 1, 0, NULL, '2025-10-01 10:25:30', '2025-10-01 10:26:01', NULL, '2025-10-01 03:25:30'),
(50, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:26:31', NULL, NULL, '2025-10-01 03:26:31'),
(51, 1, 'kabupaten', 1, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:26:33', NULL, NULL, '2025-10-01 03:26:33'),
(52, 1, 'kabupaten', 2, 'cancelled', 0, 0, 0, NULL, '2025-10-01 10:28:14', NULL, NULL, '2025-10-01 03:28:14'),
(53, 1, 'kabupaten', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 10:28:16', '2025-10-01 10:31:24', NULL, '2025-10-01 03:28:16'),
(54, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 10:31:37', '2025-10-01 10:32:05', NULL, '2025-10-01 03:31:37'),
(55, 1, 'transfer', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 10:34:57', '2025-10-01 10:34:57', NULL, '2025-10-01 03:34:57'),
(56, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 18:48:50', '2025-10-01 18:49:32', NULL, '2025-10-01 11:48:50'),
(57, 1, 'kecamatan', 2, 'cancelled', 0, 0, 0, NULL, '2025-10-01 18:50:46', NULL, NULL, '2025-10-01 11:50:46'),
(58, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 18:51:08', '2025-10-01 18:51:32', NULL, '2025-10-01 11:51:08'),
(59, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 18:51:57', '2025-10-01 18:52:23', NULL, '2025-10-01 11:51:57'),
(60, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 18:52:56', '2025-10-01 18:53:35', NULL, '2025-10-01 11:52:56'),
(61, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 18:56:44', '2025-10-01 18:57:34', NULL, '2025-10-01 11:56:44'),
(62, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 18:57:54', '2025-10-01 18:59:40', NULL, '2025-10-01 11:57:54'),
(63, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 19:00:26', '2025-10-01 19:03:04', NULL, '2025-10-01 12:00:26'),
(64, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 19:07:01', '2025-10-01 19:07:35', NULL, '2025-10-01 12:07:01'),
(65, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 19:08:14', '2025-10-01 19:12:23', NULL, '2025-10-01 12:08:14'),
(66, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-01 23:55:10', '2025-10-01 23:56:16', NULL, '2025-10-01 16:55:10'),
(67, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 00:03:33', '2025-10-02 00:06:31', NULL, '2025-10-01 17:03:33'),
(68, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 00:09:45', '2025-10-02 00:10:14', NULL, '2025-10-01 17:09:45'),
(69, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 00:13:07', '2025-10-02 00:13:45', NULL, '2025-10-01 17:13:07'),
(70, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 00:16:32', '2025-10-02 00:16:55', NULL, '2025-10-01 17:16:32'),
(71, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 00:28:19', '2025-10-02 00:28:52', NULL, '2025-10-01 17:28:19'),
(72, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 00:29:28', '2025-10-02 00:30:39', NULL, '2025-10-01 17:29:28'),
(73, 1, 'kecamatan', 2, 'cancelled', 0, 0, 0, NULL, '2025-10-02 00:58:37', NULL, NULL, '2025-10-01 17:58:37'),
(74, 1, 'kecamatan', 2, 'cancelled', 3, 0, 0, NULL, '2025-10-02 01:07:23', NULL, NULL, '2025-10-01 18:07:23'),
(75, 1, 'kecamatan', 2, 'cancelled', 1, 0, 0, NULL, '2025-10-02 01:12:37', NULL, NULL, '2025-10-01 18:12:37'),
(76, 1, 'kecamatan', 2, 'cancelled', 2, 0, 0, NULL, '2025-10-02 01:13:17', NULL, NULL, '2025-10-01 18:13:17'),
(77, 1, 'kecamatan', 2, 'cancelled', 3, 0, 0, NULL, '2025-10-02 01:29:18', NULL, NULL, '2025-10-01 18:29:18'),
(78, 1, 'kecamatan', 2, 'cancelled', 1, 0, 0, NULL, '2025-10-02 01:30:07', NULL, NULL, '2025-10-01 18:30:07'),
(79, 1, 'kecamatan', 2, 'cancelled', 1, 0, 0, NULL, '2025-10-02 01:31:45', NULL, NULL, '2025-10-01 18:31:45'),
(80, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 01:32:12', '2025-10-02 01:32:13', NULL, '2025-10-01 18:32:12'),
(81, 1, 'kecamatan', 2, 'cancelled', 1, 0, 0, NULL, '2025-10-02 01:34:21', NULL, NULL, '2025-10-01 18:34:21'),
(82, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 01:34:54', '2025-10-02 01:34:55', NULL, '2025-10-01 18:34:54'),
(83, 1, 'kecamatan', 2, 'cancelled', 0, 0, 0, NULL, '2025-10-02 01:36:14', NULL, NULL, '2025-10-01 18:36:14'),
(84, 1, 'kecamatan', 2, 'cancelled', 1, 0, 0, NULL, '2025-10-02 01:36:15', NULL, NULL, '2025-10-01 18:36:15'),
(85, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 01:37:52', '2025-10-02 01:37:54', NULL, '2025-10-01 18:37:52'),
(86, 1, 'kecamatan', 2, 'cancelled', 0, 0, 0, NULL, '2025-10-02 01:48:28', NULL, NULL, '2025-10-01 18:48:28'),
(87, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 01:48:31', '2025-10-02 03:19:14', NULL, '2025-10-01 18:48:31'),
(88, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 01:50:09', '2025-10-02 01:50:10', NULL, '2025-10-01 18:50:09'),
(89, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 01:50:48', '2025-10-02 01:51:18', NULL, '2025-10-01 18:50:48'),
(90, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 02:34:10', '2025-10-02 02:34:41', NULL, '2025-10-01 19:34:10'),
(91, 1, 'transfer', 2, 'completed', 24, 24, 0, NULL, '2025-10-02 02:48:34', '2025-10-02 02:48:34', NULL, '2025-10-01 19:48:34'),
(92, 1, 'kecamatan', 2, 'running', 1, 0, 0, NULL, '2025-10-02 05:02:12', NULL, NULL, '2025-10-01 22:02:12'),
(93, 1, 'kecamatan', 2, 'completed', 1, 1, 0, NULL, '2025-10-02 05:02:37', '2025-10-02 05:02:37', '2025-10-02 05:02:37', '2025-10-01 22:02:37'),
(94, 1, 'kecamatan', 2, 'running', 3, 0, 0, NULL, '2025-10-02 05:19:21', NULL, NULL, '2025-10-01 22:19:21'),
(95, 1, 'kecamatan', 2, 'running', 3, 0, 0, NULL, '2025-10-02 05:19:52', NULL, NULL, '2025-10-01 22:19:52'),
(96, 1, 'kecamatan', 2, 'running', 3, 0, 0, NULL, '2025-10-02 05:20:12', NULL, NULL, '2025-10-01 22:20:12'),
(97, 1, 'kecamatan', 2, 'completed', 3, 3, 0, NULL, '2025-10-02 05:21:38', '2025-10-02 05:21:38', '2025-10-02 05:21:38', '2025-10-01 22:21:38'),
(98, 1, 'kabupaten', 3, 'cancelled', 0, 0, 0, NULL, '2025-10-02 05:45:22', NULL, NULL, '2025-10-01 22:45:22'),
(99, 1, 'kabupaten', 3, 'completed', 1, 1, 0, NULL, '2025-10-02 05:45:24', '2025-10-02 05:47:16', NULL, '2025-10-01 22:45:24'),
(100, 1, 'kecamatan', 3, 'cancelled', 2, 0, 0, NULL, '2025-10-02 05:49:27', NULL, NULL, '2025-10-01 22:49:27'),
(101, 1, 'kecamatan', 3, 'cancelled', 2, 0, 0, NULL, '2025-10-02 05:51:15', NULL, NULL, '2025-10-01 22:51:15'),
(102, 1, 'kecamatan', 3, 'cancelled', 2, 0, 0, NULL, '2025-10-02 05:51:56', NULL, NULL, '2025-10-01 22:51:56'),
(103, 1, 'kecamatan', 3, 'completed', 2, 2, 0, NULL, '2025-10-02 05:54:50', '2025-10-02 05:54:50', '2025-10-02 05:54:50', '2025-10-01 22:54:50'),
(104, 1, 'transfer', 3, 'completed', 4, 4, 0, NULL, '2025-10-02 06:05:36', '2025-10-02 06:05:36', NULL, '2025-10-01 23:05:36'),
(105, 1, 'kecamatan', 3, 'cancelled', 2, 0, 0, NULL, '2025-10-02 08:28:11', NULL, NULL, '2025-10-02 01:28:11'),
(106, 1, 'kecamatan', 3, 'cancelled', 2, 0, 0, NULL, '2025-10-02 08:29:48', NULL, NULL, '2025-10-02 01:29:48'),
(107, 1, 'kecamatan', 3, 'completed', 2, 2, 0, NULL, '2025-10-02 08:30:20', '2025-10-02 08:30:20', '2025-10-02 08:30:20', '2025-10-02 01:30:20'),
(108, 1, 'kecamatan', 3, 'cancelled', 1, 0, 0, NULL, '2025-10-02 08:41:55', NULL, NULL, '2025-10-02 01:41:55'),
(109, 1, 'kecamatan', 3, 'failed', 1, 0, 1, '1 kabupaten failed to process.', '2025-10-02 08:45:20', '2025-10-02 08:45:20', '2025-10-02 08:45:20', '2025-10-02 01:45:20'),
(110, 1, 'transfer', 3, 'completed', 36, 32, 0, NULL, '2025-10-02 09:25:19', '2025-10-02 09:25:19', NULL, '2025-10-02 02:25:19'),
(111, 1, 'transfer', 3, 'completed', 15, 15, 0, NULL, '2025-10-02 09:25:59', '2025-10-02 09:25:59', NULL, '2025-10-02 02:25:59'),
(112, 1, 'kecamatan', 3, 'cancelled', 2, 0, 0, NULL, '2025-10-02 19:53:08', NULL, NULL, '2025-10-02 12:53:08'),
(113, 1, 'kecamatan', 3, 'cancelled', 2, 0, 0, NULL, '2025-10-02 19:58:23', NULL, NULL, '2025-10-02 12:58:23'),
(114, 1, 'kabupaten', 4, 'cancelled', 0, 0, 0, NULL, '2025-10-03 01:01:21', NULL, NULL, '2025-10-02 18:01:21'),
(115, 1, 'kabupaten', 4, 'completed', 1, 1, 0, NULL, '2025-10-03 01:01:27', '2025-10-03 01:03:19', NULL, '2025-10-02 18:01:27'),
(116, 1, 'kecamatan', 4, 'cancelled', 1, 0, 0, NULL, '2025-10-03 01:03:31', NULL, NULL, '2025-10-02 18:03:31'),
(117, 1, 'kecamatan', 4, 'completed', 1, 1, 0, NULL, '2025-10-03 01:05:10', '2025-10-03 01:05:10', '2025-10-03 01:05:10', '2025-10-02 18:05:10'),
(118, 1, 'kecamatan', 4, 'cancelled', 1, 0, 0, NULL, '2025-10-03 01:06:13', NULL, NULL, '2025-10-02 18:06:13'),
(119, 1, 'kecamatan', 4, 'completed', 1, 1, 0, NULL, '2025-10-03 01:07:45', '2025-10-03 01:07:46', '2025-10-03 01:07:46', '2025-10-02 18:07:45'),
(120, 1, 'transfer', 4, 'completed', 1, 1, 0, NULL, '2025-10-03 01:44:01', '2025-10-03 01:44:01', NULL, '2025-10-02 18:44:01'),
(121, 1, 'transfer', 4, 'completed', 1, 1, 0, NULL, '2025-10-03 01:45:16', '2025-10-03 01:45:16', NULL, '2025-10-02 18:45:16'),
(122, 1, 'kabupaten', 4, 'running', 0, 0, 0, NULL, '2025-10-05 14:07:39', NULL, NULL, '2025-10-05 07:07:39'),
(123, 1, 'kabupaten', 4, 'completed', 1, 1, 0, NULL, '2025-10-05 14:09:57', '2025-10-05 14:12:32', NULL, '2025-10-05 07:09:57'),
(124, 1, 'transfer', 4, 'completed', 26, 26, 0, NULL, '2025-10-05 14:46:04', '2025-10-05 14:46:04', NULL, '2025-10-05 07:46:04'),
(125, 1, 'transfer', 4, 'completed', 1, 1, 0, NULL, '2025-10-06 06:32:46', '2025-10-06 06:32:46', NULL, '2025-10-05 23:32:46'),
(126, 1, 'transfer', 4, 'completed', 24, 22, 0, NULL, '2025-10-06 06:33:57', '2025-10-06 06:33:57', NULL, '2025-10-05 23:33:57');

-- --------------------------------------------------------

--
-- Table structure for table `kabupaten_kota`
--

CREATE TABLE `kabupaten_kota` (
  `id_kabupaten` int(11) NOT NULL,
  `nama_kabupaten` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_provinsi_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kabupaten_kota`
--

INSERT INTO `kabupaten_kota` (`id_kabupaten`, `nama_kabupaten`, `id_provinsi_fk`) VALUES
(2, 'Banjar', 2),
(3, 'Balangan', 2);

-- --------------------------------------------------------

--
-- Table structure for table `kabupaten_scrape`
--

CREATE TABLE `kabupaten_scrape` (
  `id` int(11) NOT NULL,
  `kode_kabupaten` varchar(255) NOT NULL,
  `nama_kabupaten` varchar(255) NOT NULL,
  `url` varchar(500) NOT NULL,
  `url_induk_id` int(11) NOT NULL,
  `status` enum('active','inactive','processed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel untuk menyimpan data kabupaten hasil scraping';

--
-- Dumping data for table `kabupaten_scrape`
--

INSERT INTO `kabupaten_scrape` (`id`, `kode_kabupaten`, `nama_kabupaten`, `url`, `url_induk_id`, `status`, `created_at`, `updated_at`) VALUES
(92, '1', 'Kab. Banjar', 'https://dapo.kemendikdasmen.go.id/sp/2/150100', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(93, '2', 'Kota Banjarmasin', 'https://dapo.kemendikdasmen.go.id/sp/2/156000', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(94, '3', 'Kab. Barito Kuala', 'https://dapo.kemendikdasmen.go.id/sp/2/150300', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(95, '4', 'Kab. Kotabaru', 'https://dapo.kemendikdasmen.go.id/sp/2/150900', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(96, '5', 'Kab. Tanah Laut', 'https://dapo.kemendikdasmen.go.id/sp/2/150200', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(97, '6', 'Kab. Tanah Bumbu', 'https://dapo.kemendikdasmen.go.id/sp/2/151100', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(98, '7', 'Kab. Tabalong', 'https://dapo.kemendikdasmen.go.id/sp/2/150800', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(99, '8', 'Kab. Hulu Sungai Tengah', 'https://dapo.kemendikdasmen.go.id/sp/2/150600', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(100, '9', 'Kab. Hulu Sungai Selatan', 'https://dapo.kemendikdasmen.go.id/sp/2/150500', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(101, '10', 'Kab. Hulu Sungai Utara', 'https://dapo.kemendikdasmen.go.id/sp/2/150700', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(102, '11', 'Kab. Balangan', 'https://dapo.kemendikdasmen.go.id/sp/2/151000', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(103, '12', 'Kota Banjarbaru', 'https://dapo.kemendikdasmen.go.id/sp/2/156100', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19'),
(104, '13', 'Kab. Tapin', 'https://dapo.kemendikdasmen.go.id/sp/2/150400', 4, 'active', '2025-10-02 18:03:19', '2025-10-02 18:03:19');

-- --------------------------------------------------------

--
-- Table structure for table `kecamatan`
--

CREATE TABLE `kecamatan` (
  `id_kecamatan` int(11) NOT NULL,
  `nama_kecamatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kabupaten_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kecamatan`
--

INSERT INTO `kecamatan` (`id_kecamatan`, `nama_kecamatan`, `id_kabupaten_fk`) VALUES
(2, 'Aranio', 2),
(3, 'Awayan', 3),
(4, 'Aluh-Aluh', 2),
(5, 'Batu Mandi', 3),
(6, 'Paringin', 3);

-- --------------------------------------------------------

--
-- Table structure for table `kecamatan_scrape`
--

CREATE TABLE `kecamatan_scrape` (
  `id` int(11) NOT NULL,
  `kode_kecamatan` varchar(255) NOT NULL,
  `nama_kecamatan` varchar(255) NOT NULL,
  `url` varchar(500) NOT NULL,
  `kabupaten_scrape_id` int(11) NOT NULL,
  `status` enum('active','inactive','processed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel untuk menyimpan data kecamatan hasil scraping';

--
-- Dumping data for table `kecamatan_scrape`
--

INSERT INTO `kecamatan_scrape` (`id`, `kode_kecamatan`, `nama_kecamatan`, `url`, `kabupaten_scrape_id`, `status`, `created_at`, `updated_at`) VALUES
(316, '1', 'Kec. Halong', 'https://dapo.kemendikdasmen.go.id/sp/3/151006', 102, 'active', '2025-10-02 18:05:09', '2025-10-02 18:05:09'),
(317, '2', 'Kec. Lampihong', 'https://dapo.kemendikdasmen.go.id/sp/3/151001', 102, 'active', '2025-10-02 18:05:09', '2025-10-02 18:05:09'),
(318, '3', 'Kec. Paringin Selatan', 'https://dapo.kemendikdasmen.go.id/sp/3/151008', 102, 'active', '2025-10-02 18:05:09', '2025-10-02 18:05:09'),
(319, '4', 'Kec. Batu Mandi', 'https://dapo.kemendikdasmen.go.id/sp/3/151002', 102, 'active', '2025-10-02 18:05:09', '2025-10-02 18:05:09'),
(320, '5', 'Kec. Awayan', 'https://dapo.kemendikdasmen.go.id/sp/3/151003', 102, 'active', '2025-10-02 18:05:09', '2025-10-02 18:05:09'),
(321, '6', 'Kec. Paringin', 'https://dapo.kemendikdasmen.go.id/sp/3/151004', 102, 'active', '2025-10-02 18:05:09', '2025-10-02 18:05:09'),
(322, '7', 'Kec. Juai', 'https://dapo.kemendikdasmen.go.id/sp/3/151005', 102, 'active', '2025-10-02 18:05:09', '2025-10-02 18:05:09'),
(323, '8', 'Kec. Tebing Tinggi', 'https://dapo.kemendikdasmen.go.id/sp/3/151007', 102, 'active', '2025-10-02 18:05:09', '2025-10-02 18:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_pengguna` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `password`, `nama_pengguna`) VALUES
(1, 'admin@gmail.com', '$2y$10$0HGZXj991dVsDExHnij9ju2uxoHcdNgUOdDg9oRlLOqC40sTzcsgO', 'Administrator'),
(2, 'Lycoris@gmail.com', '$2y$10$PfyyXUMWbNBznEMClwjF.OvYt9/9HFZIQF0KcEh6IsJUyOe2cZeWK', 'Lycoris');

-- --------------------------------------------------------

--
-- Table structure for table `negara`
--

CREATE TABLE `negara` (
  `id_negara` int(11) NOT NULL,
  `nama_negara` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `negara`
--

INSERT INTO `negara` (`id_negara`, `nama_negara`) VALUES
(1, 'Indonesia');

-- --------------------------------------------------------

--
-- Table structure for table `provinsi`
--

CREATE TABLE `provinsi` (
  `id_provinsi` int(11) NOT NULL,
  `nama_provinsi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_negara_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provinsi`
--

INSERT INTO `provinsi` (`id_provinsi`, `nama_provinsi`, `id_negara_fk`) VALUES
(2, 'Kalimantan Selatan', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rekap_ptk_pd`
--

CREATE TABLE `rekap_ptk_pd` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guru` int(11) DEFAULT '0',
  `tendik` int(11) DEFAULT '0',
  `ptk_total` int(11) DEFAULT '0',
  `pd_total` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rekap_ptk_pd`
--

INSERT INTO `rekap_ptk_pd` (`id`, `npsn_fk`, `deskripsi`, `guru`, `tendik`, `ptk_total`, `pd_total`) VALUES
(5, '30305358', 'Laki - Laki', 7, 0, 7, 16),
(6, '30305358', 'Perempuan', 3, 1, 4, 23),
(7, '30304025', 'Laki - Laki', 2, 3, 5, 11),
(8, '30304025', 'Perempuan', 4, 0, 4, 5),
(9, '30300398', 'Laki - Laki', 3, 0, 3, 25),
(10, '30300398', 'Perempuan', 5, 1, 6, 15),
(16, '30300408', 'Perempuan', 5, 0, 5, 32),
(43, '30311582', 'Laki - Laki', 2, 2, 4, 22),
(44, '30311582', 'Perempuan', 6, 2, 8, 25),
(45, '30304028', 'Laki - Laki', 2, 2, 4, 13),
(46, '30304028', 'Perempuan', 6, 2, 8, 16),
(47, '30311576', 'Laki - Laki', 1, 3, 4, 56),
(48, '30311576', 'Perempuan', 6, 2, 8, 38),
(49, '30303948', 'Laki - Laki', 3, 1, 4, 46),
(50, '30303948', 'Perempuan', 5, 4, 9, 33),
(53, '30304037', 'Laki - Laki', 1, 2, 3, 48),
(54, '30304037', 'Perempuan', 7, 2, 9, 56),
(55, '30303991', 'Laki - Laki', 4, 2, 6, 15),
(56, '30303991', 'Perempuan', 3, 2, 5, 10),
(57, '30304011', 'Laki - Laki', 6, 2, 8, 25),
(58, '30304011', 'Perempuan', 4, 2, 6, 18),
(59, '30303836', 'Laki - Laki', 2, 2, 4, 50),
(60, '30303836', 'Perempuan', 5, 2, 7, 44),
(61, '30303835', 'Laki - Laki', 2, 3, 5, 30),
(62, '30303835', 'Perempuan', 6, 1, 7, 21),
(63, '30303850', 'Laki - Laki', 3, 2, 5, 32),
(64, '30303850', 'Perempuan', 4, 3, 7, 32),
(65, '30303843', 'Laki - Laki', 4, 3, 7, 33),
(66, '30303843', 'Perempuan', 4, 2, 6, 32),
(67, '30303809', 'Laki - Laki', 2, 2, 4, 48),
(68, '30303809', 'Perempuan', 4, 3, 7, 52),
(69, '30303993', 'Laki - Laki', 1, 1, 2, 26),
(70, '30303993', 'Perempuan', 6, 3, 9, 29),
(73, '30303808', 'Laki - Laki', 2, 3, 5, 25),
(74, '30303808', 'Perempuan', 6, 1, 7, 7),
(75, '30303902', 'Laki - Laki', 4, 1, 5, 19),
(76, '30303902', 'Perempuan', 4, 3, 7, 17),
(77, '30303815', 'Laki - Laki', 4, 1, 5, 61),
(78, '30303815', 'Perempuan', 4, 3, 7, 44),
(79, '30303893', 'Laki - Laki', 1, 2, 3, 27),
(80, '30303893', 'Perempuan', 7, 2, 9, 40),
(81, '30303895', 'Laki - Laki', 5, 1, 6, 51),
(82, '30303895', 'Perempuan', 2, 2, 4, 40),
(83, '30303905', 'Laki - Laki', 3, 0, 3, 24),
(84, '30303905', 'Perempuan', 6, 4, 10, 26),
(85, '30311473', 'Laki - Laki', 1, 2, 3, 44),
(86, '30311473', 'Perempuan', 9, 1, 10, 35),
(87, '30303867', 'Laki - Laki', 2, 1, 3, 19),
(88, '30303867', 'Perempuan', 6, 2, 8, 9),
(89, '30303886', 'Laki - Laki', 4, 4, 8, 104),
(90, '30303886', 'Perempuan', 10, 2, 12, 80),
(91, '30303880', 'Laki - Laki', 6, 3, 9, 28),
(92, '30303880', 'Perempuan', 4, 2, 6, 21),
(93, '30311634', 'Laki - Laki', 4, 2, 6, 35),
(94, '30311634', 'Perempuan', 8, 2, 10, 47),
(104, '30300408', 'Laki-laki', 8, 6, 4, 7),
(107, '30311478', 'Laki - Laki', 5, 1, 6, 11),
(108, '30311478', 'Perempuan', 4, 2, 6, 21),
(109, '30304977', 'Laki - Laki', 20, 11, 31, 350),
(110, '30304977', 'Perempuan', 29, 7, 36, 385),
(111, '30305425', 'Laki - Laki', 6, 5, 11, 123),
(112, '30305425', 'Perempuan', 12, 4, 16, 96),
(113, '69774538', 'Laki - Laki', 2, 1, 3, 51),
(114, '69774538', 'Perempuan', 7, 5, 12, 38),
(115, '30303824', 'Laki - Laki', 1, 1, 2, 48),
(116, '30303824', 'Perempuan', 7, 0, 7, 44),
(117, '30303873', 'Laki - Laki', 2, 5, 7, 34),
(118, '30303873', 'Perempuan', 9, 3, 12, 47),
(119, '30311636', 'Laki - Laki', 5, 2, 7, 14),
(120, '30311636', 'Perempuan', 5, 1, 6, 12);

-- --------------------------------------------------------

--
-- Table structure for table `rekap_rombel`
--

CREATE TABLE `rekap_rombel` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tingkat_kelas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_laki_laki` int(11) DEFAULT '0',
  `jumlah_perempuan` int(11) DEFAULT '0',
  `jumlah_total` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rekap_rombel`
--

INSERT INTO `rekap_rombel` (`id`, `npsn_fk`, `tingkat_kelas`, `jumlah_laki_laki`, `jumlah_perempuan`, `jumlah_total`) VALUES
(13, '30305358', 'Kelas 7', 9, 12, 21),
(14, '30305358', 'Kelas 8', 3, 5, 8),
(15, '30305358', 'Kelas 9', 4, 6, 10),
(16, '30304025', 'Kelas 1', 1, 2, 3),
(17, '30304025', 'Kelas 2', 3, 1, 4),
(18, '30304025', 'Kelas 3', 3, 1, 4),
(19, '30304025', 'Kelas 5', 1, 4, 5),
(20, '30300398', 'Kelas 1', 1, 2, 3),
(21, '30300398', 'Kelas 2', 4, 5, 9),
(22, '30300398', 'Kelas 3', 3, 2, 5),
(23, '30300398', 'Kelas 4', 5, 1, 6),
(24, '30300398', 'Kelas 5', 3, 2, 5),
(25, '30300398', 'Kelas 6', 9, 3, 12),
(38, '30300408', 'Kelas 1', 9, 8, 17),
(39, '30300408', 'Kelas 2', 3, 4, 7),
(40, '30300408', 'Kelas 3', 6, 5, 11),
(41, '30300408', 'Kelas 4', 2, 7, 9),
(42, '30300408', 'Kelas 5', 8, 4, 12),
(43, '30300408', 'Kelas 6', 4, NULL, 4),
(92, '30311582', 'Kelas 1', 2, 2, 4),
(93, '30311582', 'Kelas 2', 3, 6, 9),
(94, '30311582', 'Kelas 3', 5, 4, 9),
(95, '30311582', 'Kelas 4', 3, 3, 6),
(96, '30311582', 'Kelas 5', 6, 4, 10),
(97, '30311582', 'Kelas 6', 3, 6, 9),
(98, '30304028', 'Kelas 1', 2, 2, 4),
(99, '30304028', 'Kelas 2', 1, 2, 3),
(100, '30304028', 'Kelas 3', 1, 3, 4),
(101, '30304028', 'Kelas 4', 3, 2, 5),
(102, '30304028', 'Kelas 5', 2, 4, 6),
(103, '30304028', 'Kelas 6', 4, 3, 7),
(104, '30311576', 'Kelas 1', 7, 6, 13),
(105, '30311576', 'Kelas 2', 9, 5, 14),
(106, '30311576', 'Kelas 3', 10, 6, 16),
(107, '30311576', 'Kelas 4', 9, 8, 17),
(108, '30311576', 'Kelas 5', 12, 9, 21),
(109, '30311576', 'Kelas 6', 9, 4, 13),
(110, '30303948', 'Kelas 1', 5, 8, 13),
(111, '30303948', 'Kelas 2', 8, 4, 12),
(112, '30303948', 'Kelas 3', 6, 7, 13),
(113, '30303948', 'Kelas 4', 4, 8, 12),
(114, '30303948', 'Kelas 5', 8, 4, 12),
(115, '30303948', 'Kelas 6', 15, 2, 17),
(122, '30304037', 'Kelas 1', 7, 9, 16),
(123, '30304037', 'Kelas 2', 14, 13, 27),
(124, '30304037', 'Kelas 3', 7, 5, 12),
(125, '30304037', 'Kelas 4', 8, 13, 21),
(126, '30304037', 'Kelas 5', 4, 6, 10),
(127, '30304037', 'Kelas 6', 8, 10, 18),
(128, '30303991', 'Kelas 1', 4, 1, 5),
(129, '30303991', 'Kelas 2', 3, 2, 5),
(130, '30303991', 'Kelas 3', 3, 1, 4),
(131, '30303991', 'Kelas 4', 1, 2, 3),
(132, '30303991', 'Kelas 5', 1, 2, 3),
(133, '30303991', 'Kelas 6', 5, NULL, 5),
(134, '30304011', 'Kelas 1', 3, 2, 5),
(135, '30304011', 'Kelas 2', 2, 4, 6),
(136, '30304011', 'Kelas 3', 5, 5, 10),
(137, '30304011', 'Kelas 4', 7, 6, 13),
(138, '30304011', 'Kelas 5', 4, 1, 5),
(139, '30304011', 'Kelas 6', 4, NULL, 4),
(140, '30303836', 'Kelas 1', 7, 8, 15),
(141, '30303836', 'Kelas 2', 7, 7, 14),
(142, '30303836', 'Kelas 3', 5, 6, 11),
(143, '30303836', 'Kelas 4', 9, 13, 22),
(144, '30303836', 'Kelas 5', 13, 6, 19),
(145, '30303836', 'Kelas 6', 9, 4, 13),
(146, '30303835', 'Kelas 1', 4, 4, 8),
(147, '30303835', 'Kelas 2', 4, 6, 10),
(148, '30303835', 'Kelas 3', 6, 5, 11),
(149, '30303835', 'Kelas 4', 1, 4, 5),
(150, '30303835', 'Kelas 5', 6, 1, 7),
(151, '30303835', 'Kelas 6', 9, 1, 10),
(152, '30303850', 'Kelas 1', 7, 9, 16),
(153, '30303850', 'Kelas 2', 4, 3, 7),
(154, '30303850', 'Kelas 3', 4, 3, 7),
(155, '30303850', 'Kelas 4', 6, 2, 8),
(156, '30303850', 'Kelas 5', 4, 7, 11),
(157, '30303850', 'Kelas 6', 7, 8, 15),
(158, '30303843', 'Kelas 1', 4, 3, 7),
(159, '30303843', 'Kelas 2', 5, 5, 10),
(160, '30303843', 'Kelas 3', 8, 7, 15),
(161, '30303843', 'Kelas 4', 8, 5, 13),
(162, '30303843', 'Kelas 5', 3, 5, 8),
(163, '30303843', 'Kelas 6', 5, 7, 12),
(164, '30303809', 'Kelas 1', 8, 12, 20),
(165, '30303809', 'Kelas 2', 10, 10, 20),
(166, '30303809', 'Kelas 3', 7, 13, 20),
(167, '30303809', 'Kelas 4', 8, 3, 11),
(168, '30303809', 'Kelas 5', 9, 7, 16),
(169, '30303809', 'Kelas 6', 6, 7, 13),
(170, '30303993', 'Kelas 1', 1, 5, 6),
(171, '30303993', 'Kelas 2', 2, 7, 9),
(172, '30303993', 'Kelas 3', 4, 2, 6),
(173, '30303993', 'Kelas 4', 5, 2, 7),
(174, '30303993', 'Kelas 5', 10, 5, 15),
(175, '30303993', 'Kelas 6', 4, 8, 12),
(182, '30303808', 'Kelas 1', 4, 3, 7),
(183, '30303808', 'Kelas 2', 1, 5, 6),
(184, '30303808', 'Kelas 4', 4, 5, 9),
(185, '30303808', 'Kelas 5', 2, 1, 3),
(186, '30303808', 'Kelas 6', 7, NULL, 7),
(187, '30303902', 'Kelas 1', 4, 4, 8),
(188, '30303902', 'Kelas 2', 5, 1, 6),
(189, '30303902', 'Kelas 3', 6, 5, 11),
(190, '30303902', 'Kelas 4', 1, 3, 4),
(191, '30303902', 'Kelas 5', 3, 4, 7),
(192, '30303815', 'Kelas 1', 9, 6, 15),
(193, '30303815', 'Kelas 2', 11, 6, 17),
(194, '30303815', 'Kelas 3', 8, 6, 14),
(195, '30303815', 'Kelas 4', 11, 7, 18),
(196, '30303815', 'Kelas 5', 14, 8, 22),
(197, '30303815', 'Kelas 6', 8, 11, 19),
(198, '30303893', 'Kelas 1', 4, 4, 8),
(199, '30303893', 'Kelas 2', 2, 4, 6),
(200, '30303893', 'Kelas 3', 5, 6, 11),
(201, '30303893', 'Kelas 4', 3, 11, 14),
(202, '30303893', 'Kelas 5', 4, 9, 13),
(203, '30303893', 'Kelas 6', 9, 6, 15),
(204, '30303895', 'Kelas 1', 8, 9, 17),
(205, '30303895', 'Kelas 2', 7, 4, 11),
(206, '30303895', 'Kelas 3', 8, 10, 18),
(207, '30303895', 'Kelas 4', 13, 9, 22),
(208, '30303895', 'Kelas 5', 5, 5, 10),
(209, '30303895', 'Kelas 6', 10, 3, 13),
(210, '30303905', 'Kelas 1', 4, 5, 9),
(211, '30303905', 'Kelas 2', 4, 4, 8),
(212, '30303905', 'Kelas 3', 2, 1, 3),
(213, '30303905', 'Kelas 4', 3, 4, 7),
(214, '30303905', 'Kelas 5', 2, 4, 6),
(215, '30303905', 'Kelas 6', 9, 8, 17),
(216, '30311473', 'Kelas 1', 7, 4, 11),
(217, '30311473', 'Kelas 2', 6, 5, 11),
(218, '30311473', 'Kelas 3', 9, 6, 15),
(219, '30311473', 'Kelas 4', 10, 8, 18),
(220, '30311473', 'Kelas 5', 4, 6, 10),
(221, '30311473', 'Kelas 6', 8, 6, 14),
(222, '30303867', 'Kelas 1', 1, 3, 4),
(223, '30303867', 'Kelas 3', 5, 2, 7),
(224, '30303867', 'Kelas 4', 4, 4, 8),
(225, '30303867', 'Kelas 5', 2, 4, 6),
(226, '30303867', 'Kelas 6', 3, NULL, 3),
(227, '30303886', 'Kelas 7', 39, 37, 76),
(228, '30303886', 'Kelas 8', 35, 21, 56),
(229, '30303886', 'Kelas 9', 30, 22, 52),
(230, '30303880', 'Kelas 7', 15, 5, 20),
(231, '30303880', 'Kelas 8', 6, 11, 17),
(232, '30303880', 'Kelas 9', 7, 5, 12),
(233, '30311634', 'Kelas 7', 14, 18, 32),
(234, '30311634', 'Kelas 8', 16, 21, 37),
(235, '30311634', 'Kelas 9', 5, 8, 13),
(243, '30311478', 'Kelas 7', 2, 2, 4),
(244, '30311478', 'Kelas 8', 7, 10, 17),
(245, '30311478', 'Kelas 9', 2, 9, 11),
(246, '30304977', 'Kelas 10', 185, 151, 336),
(247, '30304977', 'Kelas 11', 84, 105, 189),
(248, '30304977', 'Kelas 12', 81, 129, 210),
(249, '30305425', 'Kelas 10', 41, 31, 72),
(250, '30305425', 'Kelas 11', 40, 28, 68),
(251, '30305425', 'Kelas 12', 42, 37, 79),
(252, '69774538', 'Kelas 7', 22, 9, 31),
(253, '69774538', 'Kelas 8', 14, 13, 27),
(254, '69774538', 'Kelas 9', 15, 16, 31),
(255, '30303824', 'Kelas 1', 8, 7, 15),
(256, '30303824', 'Kelas 2', 6, 4, 10),
(257, '30303824', 'Kelas 3', 6, 5, 11),
(258, '30303824', 'Kelas 4', 9, 6, 15),
(259, '30303824', 'Kelas 5', 11, 8, 19),
(260, '30303824', 'Kelas 6', 8, 14, 22),
(261, '30303873', 'Kelas 7', 12, 25, 37),
(262, '30303873', 'Kelas 8', 13, 11, 24),
(263, '30303873', 'Kelas 9', 9, 11, 20),
(264, '30311636', 'Kelas 7', 3, 3, 6),
(265, '30311636', 'Kelas 8', 6, 3, 9),
(266, '30311636', 'Kelas 9', 5, 6, 11);

-- --------------------------------------------------------

--
-- Table structure for table `rekap_sarpras`
--

CREATE TABLE `rekap_sarpras` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sarana` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rekap_sarpras`
--

INSERT INTO `rekap_sarpras` (`id`, `npsn_fk`, `sarana`, `jumlah`) VALUES
(7, '30305358', 'Ruang Kelas', 3),
(8, '30305358', 'Ruang Lab', 1),
(9, '30305358', 'Ruang Perpus', 1),
(10, '30304025', 'Ruang Kelas', 6),
(11, '30304025', 'Ruang Lab', 0),
(12, '30304025', 'Ruang Perpus', 1),
(13, '30300398', 'Ruang Kelas', 0),
(14, '30300398', 'Ruang Lab', 0),
(15, '30300398', 'Ruang Perpus', 0),
(22, '30300408', 'Ruang Kelas', 5),
(23, '30300408', 'Ruang Lab', 0),
(24, '30300408', 'Ruang Perpus', 1),
(64, '30311582', 'Ruang Kelas', 6),
(65, '30311582', 'Ruang Lab', 0),
(66, '30311582', 'Ruang Perpus', 1),
(67, '30304028', 'Ruang Kelas', 8),
(68, '30304028', 'Ruang Lab', 0),
(69, '30304028', 'Ruang Perpus', 1),
(70, '30311576', 'Ruang Kelas', 8),
(71, '30311576', 'Ruang Lab', 1),
(72, '30311576', 'Ruang Perpus', 1),
(73, '30303948', 'Ruang Kelas', 6),
(74, '30303948', 'Ruang Lab', 0),
(75, '30303948', 'Ruang Perpus', 1),
(79, '30304037', 'Ruang Kelas', 6),
(80, '30304037', 'Ruang Lab', 0),
(81, '30304037', 'Ruang Perpus', 1),
(82, '30303991', 'Ruang Kelas', 6),
(83, '30303991', 'Ruang Lab', 0),
(84, '30303991', 'Ruang Perpus', 1),
(85, '30304011', 'Ruang Kelas', 7),
(86, '30304011', 'Ruang Lab', 0),
(87, '30304011', 'Ruang Perpus', 1),
(88, '30303836', 'Ruang Kelas', 6),
(89, '30303836', 'Ruang Lab', 0),
(90, '30303836', 'Ruang Perpus', 1),
(91, '30303835', 'Ruang Kelas', 6),
(92, '30303835', 'Ruang Lab', 0),
(93, '30303835', 'Ruang Perpus', 1),
(94, '30303850', 'Ruang Kelas', 7),
(95, '30303850', 'Ruang Lab', 1),
(96, '30303850', 'Ruang Perpus', 1),
(97, '30303843', 'Ruang Kelas', 6),
(98, '30303843', 'Ruang Lab', 0),
(99, '30303843', 'Ruang Perpus', 1),
(100, '30303809', 'Ruang Kelas', 6),
(101, '30303809', 'Ruang Lab', 3),
(102, '30303809', 'Ruang Perpus', 1),
(103, '30303993', 'Ruang Kelas', 5),
(104, '30303993', 'Ruang Lab', 0),
(105, '30303993', 'Ruang Perpus', 2),
(109, '30303808', 'Ruang Kelas', 6),
(110, '30303808', 'Ruang Lab', 0),
(111, '30303808', 'Ruang Perpus', 0),
(112, '30303902', 'Ruang Kelas', 6),
(113, '30303902', 'Ruang Lab', 0),
(114, '30303902', 'Ruang Perpus', 1),
(115, '30303815', 'Ruang Kelas', 7),
(116, '30303815', 'Ruang Lab', 0),
(117, '30303815', 'Ruang Perpus', 1),
(118, '30303893', 'Ruang Kelas', 6),
(119, '30303893', 'Ruang Lab', 0),
(120, '30303893', 'Ruang Perpus', 1),
(121, '30303895', 'Ruang Kelas', 6),
(122, '30303895', 'Ruang Lab', 0),
(123, '30303895', 'Ruang Perpus', 1),
(124, '30303905', 'Ruang Kelas', 6),
(125, '30303905', 'Ruang Lab', 0),
(126, '30303905', 'Ruang Perpus', 1),
(127, '30311473', 'Ruang Kelas', 0),
(128, '30311473', 'Ruang Lab', 0),
(129, '30311473', 'Ruang Perpus', 0),
(130, '30303867', 'Ruang Kelas', 6),
(131, '30303867', 'Ruang Lab', 0),
(132, '30303867', 'Ruang Perpus', 1),
(133, '30303886', 'Ruang Kelas', 12),
(134, '30303886', 'Ruang Lab', 4),
(135, '30303886', 'Ruang Perpus', 1),
(136, '30303880', 'Ruang Kelas', 6),
(137, '30303880', 'Ruang Lab', 2),
(138, '30303880', 'Ruang Perpus', 1),
(139, '30311634', 'Ruang Kelas', 3),
(140, '30311634', 'Ruang Lab', 2),
(141, '30311634', 'Ruang Perpus', 1),
(148, '30311478', 'Ruang Kelas', 3),
(149, '30311478', 'Ruang Lab', 2),
(150, '30311478', 'Ruang Perpus', 1),
(151, '30304977', 'Ruang Kelas', 23),
(152, '30304977', 'Ruang Lab', 2),
(153, '30304977', 'Ruang Perpus', 1),
(154, '30305425', 'Ruang Kelas', 11),
(155, '30305425', 'Ruang Lab', 4),
(156, '30305425', 'Ruang Perpus', 1),
(157, '69774538', 'Ruang Kelas', 3),
(158, '69774538', 'Ruang Lab', 2),
(159, '69774538', 'Ruang Perpus', 1),
(160, '30303824', 'Ruang Kelas', 6),
(161, '30303824', 'Ruang Lab', 0),
(162, '30303824', 'Ruang Perpus', 0),
(163, '30303873', 'Ruang Kelas', 8),
(164, '30303873', 'Ruang Lab', 3),
(165, '30303873', 'Ruang Perpus', 1),
(166, '30311636', 'Ruang Kelas', 0),
(167, '30311636', 'Ruang Lab', 0),
(168, '30311636', 'Ruang Perpus', 0);

-- --------------------------------------------------------

--
-- Table structure for table `scraping_logs`
--

CREATE TABLE `scraping_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `scraping_url_id` int(11) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `batch_name` varchar(100) NOT NULL,
  `total_urls` int(11) DEFAULT '0',
  `url_ids` text,
  `processed_urls` int(11) DEFAULT '0',
  `success_count` int(11) DEFAULT '0',
  `failed_count` int(11) DEFAULT '0',
  `status` enum('running','completed','failed','cancelled') DEFAULT 'running',
  `started_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  `error_message` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `scraping_logs`
--

INSERT INTO `scraping_logs` (`id`, `user_id`, `scraping_url_id`, `pid`, `batch_name`, `total_urls`, `url_ids`, `processed_urls`, `success_count`, `failed_count`, `status`, `started_at`, `completed_at`, `error_message`) VALUES
(1, 1, NULL, 14984, 'Update Pilihan - 1 URL', 1, '[1]', 1, 1, 0, 'completed', '2025-10-01 03:04:22', '2025-10-01 03:11:07', NULL),
(2, 1, NULL, 724, 'Update Semua URL Aktif', 1, '[\"1\"]', 1, 1, 0, 'completed', '2025-10-01 03:12:37', '2025-10-01 03:16:13', NULL),
(3, 1, NULL, 25236, 'Update Semua URL Aktif', 3, '[\"1\",\"2\",\"3\"]', 3, 3, 0, 'completed', '2025-10-01 03:35:31', '2025-10-01 03:39:07', NULL),
(4, 2, NULL, 13960, 'Update Semua URL Aktif', 3, '[\"1\",\"2\",\"3\"]', 0, 0, 0, 'cancelled', '2025-10-01 03:37:18', '2025-10-01 03:37:23', 'Proses dibatalkan oleh pengguna.'),
(5, 2, NULL, 15036, 'Update Semua URL Aktif', 3, '[\"1\",\"2\",\"3\"]', 0, 0, 0, 'cancelled', '2025-10-01 03:37:35', '2025-10-01 03:37:52', 'Proses dibatalkan oleh pengguna.'),
(6, 2, NULL, 22044, 'Update Semua URL Aktif', 3, '[\"1\",\"2\",\"3\"]', 0, 0, 0, 'cancelled', '2025-10-01 03:37:41', '2025-10-01 03:37:44', 'Proses dibatalkan oleh pengguna.'),
(7, 1, NULL, 24880, 'Update Pilihan - 4 URL', 4, '[27,28,29,30]', 4, 4, 0, 'completed', '2025-10-01 23:05:59', '2025-10-01 23:13:20', NULL),
(8, 1, NULL, 25868, 'Update Semua URL Aktif', 2, '[\"76\",\"77\"]', 2, 2, 0, 'completed', '2025-10-02 19:16:55', '2025-10-02 19:23:56', NULL),
(9, 1, NULL, 24196, 'Update Pilihan - 28 URL', 28, '[76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103]', 28, 28, 23, 'completed', '2025-10-05 07:46:24', '2025-10-05 08:42:10', NULL),
(10, 1, NULL, 5824, 'Update Pilihan - 3 URL', 3, '[99,104,124]', 3, 3, 0, 'completed', '2025-10-05 23:34:38', '2025-10-05 23:42:13', NULL),
(11, 1, NULL, 9720, 'Update Pilihan - 4 URL', 4, '[91,103,125,126]', 4, 4, 0, 'completed', '2025-10-06 03:00:54', '2025-10-06 03:07:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `scraping_urls`
--

CREATE TABLE `scraping_urls` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sekolah_scrape_id` int(11) DEFAULT NULL,
  `kecamatan_scrape_id` int(11) DEFAULT NULL,
  `kabupaten_scrape_id` int(11) DEFAULT NULL,
  `url` varchar(500) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','processed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `scraping_urls`
--

INSERT INTO `scraping_urls` (`id`, `user_id`, `sekolah_scrape_id`, `kecamatan_scrape_id`, `kabupaten_scrape_id`, `url`, `description`, `status`, `created_at`, `updated_at`) VALUES
(76, 1, 1224, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/A1E192E00C0113901AEA', 'SD NEGERI AMBAKIANG HULU', 'active', '2025-10-02 18:44:01', '2025-10-02 18:44:01'),
(77, 1, 1225, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/7D48320A02662874853E', 'SD NEGERI BADALUNGGA', 'active', '2025-10-02 18:45:16', '2025-10-02 18:45:16'),
(78, 1, 1334, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/C10D1B294B13B79EE291', 'SD NEGERI BAKUNG', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(79, 1, 1335, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/2B495794D274B4B21CF7', 'SD NEGERI BANUA HANYAR', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(80, 1, 1336, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/7229047CB2E3EC34D190', 'SD NEGERI BUNGUR', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(81, 1, 1337, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/65FA6D60CFDDCC24356B', 'SD NEGERI GUHA 1', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(82, 1, 1338, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/B7E86AC0C81029D86FA3', 'SD NEGERI GUHA 2', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(83, 1, 1339, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/DF4262EBD73342C04717', 'SD NEGERI GUNUNG MANAU', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(84, 1, 1340, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/A5D1D9C2706A0345CD54', 'SD NEGERI HAMPARAYA', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(85, 1, 1341, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/0C2069AB3C100D7AEAE7', 'SD NEGERI KARUH', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(86, 1, 1342, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/03392A64B2534E9FA94C', 'SD NEGERI KASAI', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(87, 1, 1343, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/2131DADE4F6C358DC9F3', 'SD NEGERI LOK BATU', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(88, 1, 1344, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/6599E8A9DF10BF197C5D', 'SD NEGERI MAMPARI', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(89, 1, 1345, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/D298E6095AA79B89B9FA', 'SD NEGERI MANTIMIN 1', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(90, 1, 1346, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/6CE5DE48572759CCD9D3', 'SD NEGERI MANTIMIN 2', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(91, 1, 1347, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/69378FDD376B19DC0FDB', 'SD NEGERI MUNJUNG', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(92, 1, 1348, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/119180010CED5243A330', 'SD NEGERI PELAJAU', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(93, 1, 1349, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/7698868D893D7CFD4FDE', 'SD NEGERI RIWA', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(94, 1, 1350, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/87F6034C9B1D1579DDCD', 'SD NEGERI SUMPUNG', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(95, 1, 1351, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/8B72133B80A0BE77EDE1', 'SD NEGERI SUNGAI HANYAR', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(96, 1, 1352, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/C001D2BB072818C833A3', 'SD NEGERI SUNGAI KUSI', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(97, 1, 1353, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/AC92070B028E944ADD13', 'SD NEGERI TELUK MESJID 1', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(98, 1, 1354, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/1A26E8CFA3B2CE7492AB', 'SD NEGERI TIMBUN TULANG', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(99, 1, 1359, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/D604772E0F347E9CB15F', 'SMKN 1 BATUMANDI', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(100, 1, 1355, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/2CE1108C99C9410D988D', 'SMP NEGERI 1 BATUMANDI', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(101, 1, 1356, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/7792DA7D572070040E45', 'SMP NEGERI 2 BATUMANDI', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(102, 1, 1357, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/B1699952ED89667E36C3', 'SMP NEGERI 3 BATUMANDI', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(103, 1, 1358, 319, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/23D0E0AD66B4BD7FC552', 'SMP NEGERI 4 BATUMANDI', 'active', '2025-10-05 07:46:04', '2025-10-05 07:46:04'),
(104, 1, 1268, 321, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/A862041AA01A7B952EB7', 'SMPS AL QURAN IKHWANUL MUSLIMIN', 'active', '2025-10-05 23:32:46', '2025-10-05 23:32:46'),
(105, 1, 1226, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/2ABB3BB2692592F0A4B6', 'SD NEGERI BADALUNGGA HILIR', 'active', '2025-10-05 23:33:56', '2025-10-05 23:33:56'),
(106, 1, 1227, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/0E89F888761FE1C1F945', 'SD NEGERI BALANTI', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(107, 1, 1228, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/54A3FA1469D535C5AA6B', 'SD NEGERI BARAMBAN', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(108, 1, 1229, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/0DAA18AA95946F7944C8', 'SD NEGERI BARU', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(109, 1, 1230, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/C0F31D7AE0C893516EC3', 'SD NEGERI BAYUR', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(110, 1, 1231, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/25A6D01E11ABBEE8552B', 'SD NEGERI MANINGAU', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(111, 1, 1232, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/1A914B53A50E4AA8B704', 'SD NEGERI MERAH', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(112, 1, 1233, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/A072C199899A43385B97', 'SD NEGERI MUARA JAYA', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(113, 1, 1234, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/B1D85725F7C5A53CD4A7', 'SD NEGERI NUNGKA', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(114, 1, 1235, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/F9BDEEAF8AFC410F4658', 'SD NEGERI PEMATANG', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(115, 1, 1236, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/8C9AB2B35F93D0E4C62A', 'SD NEGERI PIYAIT', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(116, 1, 1237, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/7005533830312B2F0149', 'SD NEGERI PULANTAN', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(117, 1, 1238, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/D3203ED44347A2E0249B', 'SD NEGERI PULAU KAMBANG', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(118, 1, 1239, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/AAF96392C2E0DBFAF3BC', 'SD NEGERI PUTAT BASIUN', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(119, 1, 1240, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/16934B35A7E458C71777', 'SD NEGERI SUNGAI PUMPUNG', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(120, 1, 1241, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/76E025FE217F73943498', 'SD NEGERI TUNDAKAN', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(121, 1, 1242, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/1D2FEE468EC69586FA6D', 'SD NEGERI TUNDI', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(122, 1, 1243, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/07AD9DF22C778EF0F51B', 'SD NEGERI UUNGAN', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(123, 1, 1244, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/348BEF4FA1FFC75E7D6A', 'SD NEGERI WANGKILI', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(124, 1, 1247, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/F41946F8CCAF24B2BC6D', 'SMAN 1 AWAYAN', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(125, 1, 1245, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/72EF0F390075682ED4E4', 'SMP NEGERI 1 AWAYAN', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57'),
(126, 1, 1246, 320, 102, 'https://dapo.kemendikdasmen.go.id/sekolah/96167D84966910DB9ED9', 'SMP NEGERI 4 AWAYAN', 'active', '2025-10-05 23:33:57', '2025-10-05 23:33:57');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_identitas`
--

CREATE TABLE `sekolah_identitas` (
  `npsn` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_sekolah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenjang_pendidikan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_sekolah` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_jalan` text COLLATE utf8mb4_unicode_ci,
  `rt` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelurahan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lintang` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bujur` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_kecamatan_fk` int(11) DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah_identitas`
--

INSERT INTO `sekolah_identitas` (`npsn`, `nama_sekolah`, `jenjang_pendidikan`, `status_sekolah`, `alamat_jalan`, `rt`, `rw`, `kode_pos`, `kelurahan`, `lintang`, `bujur`, `id_kecamatan_fk`, `last_updated`) VALUES
('30300398', 'SD NEGERI ARANIO 1', 'SD', 'Negeri', 'Jl Ir Pangeran M.noor', '3', '1.0', '70662', 'Aranio', '-3.512000000000', '115.002700000000', 2, '2025-10-01 03:16:13'),
('30300408', 'SD NEGERI ALUH-ALUH KECIL 1', 'SD', 'Negeri', 'Jl. Panggang Rt.04', '3', '1.0', '70655', 'Aluh Aluh Kecil', '-3.440300000000', '114.564200000000', 4, '2025-10-01 23:13:19'),
('30303808', 'SD NEGERI MANTIMIN 2', 'SD', 'Negeri', 'SIMPANG TIGA MANTIMIN', '4', '0.0', '71663', 'Mantimin', '-2.407400000000', '115.431600000000', 5, '2025-10-05 08:42:10'),
('30303809', 'SD NEGERI MANTIMIN 1', 'SD', 'Negeri', 'Desa Mantimin', '1', '1.0', '71663', 'Mantimin', '-2.402600000000', '115.430400000000', 5, '2025-10-05 08:42:09'),
('30303815', 'SD NEGERI PELAJAU', 'SD', 'Negeri', 'Desa Pelajau', '1', '0.0', '71663', 'Pelajau', '-2.437200000000', '115.451200000000', 5, '2025-10-05 08:42:10'),
('30303824', 'SD NEGERI MUNJUNG', 'SD', 'Negeri', 'Desa Munjung', '2', '2.0', '71663', 'Munjung', '-2.449300000000', '115.466600000000', 5, '2025-10-05 08:42:10'),
('30303835', 'SD NEGERI KASAI', 'SD', 'Negeri', 'Desa Kasai', '4', '0.0', '71663', 'KASAI', '-2.430700000000', '115.438200000000', 5, '2025-10-05 08:42:09'),
('30303836', 'SD NEGERI KARUH', 'SD', 'Negeri', 'Desa Karuh', '1', '1.0', '71663', 'Karuh', '-2.458700000000', '115.475900000000', 5, '2025-10-05 08:42:09'),
('30303843', 'SD NEGERI MAMPARI', 'SD', 'Negeri', 'Desa Mampari', '0', '0.0', '71663', 'Mampari', '-2.376600000000', '115.414000000000', 5, '2025-10-05 08:42:09'),
('30303850', 'SD NEGERI LOK BATU', 'SD', 'Negeri', 'Desa Lok Batu', '2', '1.0', '71663', 'Lok Batu', '-2.461900000000', '115.499800000000', 5, '2025-10-05 08:42:09'),
('30303867', 'SD NEGERI TIMBUN TULANG', 'SD', 'Negeri', 'Desa Timbun Tulang', '1', '1.0', '71663', 'Timbun Tulang', '-2.433900000000', '115.419900000000', 5, '2025-10-05 08:42:10'),
('30303873', 'SMP NEGERI 1 AWAYAN', 'SMP', 'Negeri', 'Kompleks Kecamatan Awayan', '1', '1.0', '71664', 'Putat Basiun', '-2.415600000000', '115.527200000000', 3, '2025-10-06 03:07:06'),
('30303880', 'SMP NEGERI 2 BATUMANDI', 'SMP', 'Negeri', 'Desa Lok Batu', '1', '0.0', '71663', 'LOK BATU', '-2.458700000000', '115.503500000000', 5, '2025-10-05 08:42:10'),
('30303886', 'SMP NEGERI 1 BATUMANDI', 'SMP', 'Negeri', 'Desa Batumandi', '1', '1.0', '71663', 'Batu Mandi', '-2.425200000000', '115.424600000000', 5, '2025-10-05 08:42:10'),
('30303893', 'SD NEGERI SUNGAI HANYAR', 'SD', 'Negeri', 'Desa Batumandi', '5', '2.0', '71663', 'Batu Mandi', '-2.417600000000', '115.410300000000', 5, '2025-10-05 08:42:10'),
('30303895', 'SD NEGERI SUMPUNG', 'SD', 'Negeri', 'Jl. Ahmad Yani No. 237', '7', '0.0', '71663', 'Mampari', '-2.387000000000', '115.453400000000', 5, '2025-10-05 08:42:10'),
('30303902', 'SD NEGERI RIWA', 'SD', 'Negeri', 'Desa Riwa', '1', '1.0', '71663', 'Riwa', '-2.417000000000', '115.426500000000', 5, '2025-10-05 08:42:10'),
('30303905', 'SD NEGERI SUNGAI KUSI', 'SD', 'Negeri', 'RT.005', '5', '3.0', '71663', 'Gunung Manau', '-2.465100000000', '115.529600000000', 5, '2025-10-05 08:42:10'),
('30303948', 'SD NEGERI BAKUNG', 'SD', 'Negeri', 'Desa Bakung', '2', '1.0', '71463', 'Bakung', '-2.453900000000', '115.483000000000', 5, '2025-10-05 08:42:08'),
('30303991', 'SD NEGERI GUNUNG MANAU', 'SD', 'Negeri', 'Desa Gunung Manau', '1', '2.0', '71663', 'Gunung Manau', '-2.471700000000', '115.519000000000', 5, '2025-10-05 08:42:09'),
('30303993', 'SD NEGERI GUHA 2', 'SD', 'Negeri', 'Desa Guha', '1', '1.0', '71663', 'GUHA', '-2.411800000000', '115.465300000000', 5, '2025-10-05 08:42:09'),
('30304011', 'SD NEGERI HAMPARAYA', 'SD', 'Negeri', 'Desa Hamparaya', '3', '1.0', '71663', 'Hamparaya', '-2.451600000000', '115.413200000000', 5, '2025-10-05 08:42:09'),
('30304025', 'SD NEGERI BALANTI', 'SD', 'Negeri', 'Jl. Raya Belanti Desa Baru RT.004 Kec.Awayan Kab. Balangan', '4', '0.0', '71664', 'Baru', '-2.382700000000', '115.585200000000', 3, '2025-10-01 03:39:07'),
('30304028', 'SD NEGERI BADALUNGGA', 'SD', 'Negeri', 'Desa Badalungga', '1', '0.0', '71664', 'Badalungga', '-2.414400000000', '115.532500000000', 3, '2025-10-02 19:23:56'),
('30304037', 'SD NEGERI BUNGUR', 'SD', 'Negeri', 'Jalan A. Yani RT. 04', '4', '2.0', '71663', 'Bungur', '-2.424600000000', '115.423200000000', 5, '2025-10-05 08:42:09'),
('30304977', 'SMKN 1 BATUMANDI', 'SMK', 'Negeri', 'Jl. A.Yani Rt.05 Rw.02 Desa Mantimin Kec.Batumandi Kab.Balangan Prov.Kalsel', '5', '0.0', '71663', 'Mantimin', '-2.403600000000', '115.432900000000', 5, '2025-10-05 08:42:10'),
('30305358', 'SMP NEGERI 4 ARANIO', 'SMP', 'Negeri', 'Jl. Utama RT 3', '3', '1.0', '70662', 'Rantau Bujur', '-3.438300000000', '115.140900000000', 2, '2025-10-01 03:39:07'),
('30305425', 'SMAN 1 AWAYAN', 'SMA', 'Negeri', 'JL. TUNDAKAN NO. 01 KEC. AWAYAN KAB. BALANGAN', '1', '1.0', '71664', 'Piyait', '-2.409900000000', '115.550800000000', 3, '2025-10-05 23:42:13'),
('30311473', 'SD NEGERI TELUK MESJID 1', 'SD', 'Negeri', 'Jln. A. Yani Desa Teluk Mesjid RT 03', '3', '0.0', '71663', 'Teluk Mesjid', '-2.430200000000', '115.422000000000', 5, '2025-10-05 08:42:10'),
('30311478', 'SMPS AL QURAN IKHWANUL MUSLIMIN', 'SMP', 'Swasta', 'Jalan Merdeka RT.8 No.4  Paringin Barat', '8', '4.0', '71161', 'Kelurahan Paringin Kota', '-2.335800000000', '115.456000000000', 6, '2025-10-05 23:42:12'),
('30311576', 'SD NEGERI BANUA HANYAR', 'SD', 'Negeri', 'Desa Banua Hanyar', '2', '0.0', '71663', 'Banua Hanyar', '-2.397300000000', '115.431000000000', 5, '2025-10-05 08:42:08'),
('30311582', 'SD NEGERI AMBAKIANG HULU', 'SD', 'Negeri', 'Desa Ambakiang', '0', '0.0', '71664', 'Ambakiyang', '-2.398000000000', '115.567500000000', 3, '2025-10-02 19:23:55'),
('30311634', 'SMP NEGERI 3 BATUMANDI', 'SMP', 'Negeri', 'Desa Munjung', '1', '0.0', '71663', 'Munjung', '-2.430300000000', '115.438400000000', 5, '2025-10-05 08:42:10'),
('30311636', 'SMP NEGERI 4 AWAYAN', 'SMP', 'Negeri', 'Temenggung Jalil', '3', '1.0', '71664', 'Tundi', '-2.376900000000', '115.557100000000', 3, '2025-10-06 03:07:06'),
('69774538', 'SMP NEGERI 4 BATUMANDI', 'SMP', 'Negeri', 'JL. MAMPARI', '1', '1.0', '71663', 'Mampari', '-2.380000000000', '115.412400000000', 5, '2025-10-05 08:42:10');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_kontak`
--

CREATE TABLE `sekolah_kontak` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_telepon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_fax` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah_kontak`
--

INSERT INTO `sekolah_kontak` (`id`, `npsn_fk`, `nomor_telepon`, `nomor_fax`, `email`, `website`) VALUES
(2, '30300398', 'None', 'None', 'sdn_aranio1@yahoo.com', 'http://'),
(3, '30305358', '2147483647', 'None', 'smpnegri4aranio@gmail.com', 'http://'),
(4, '30304025', '2147483647', 'None', 'sdnbalanti105@gmail.com', 'http://'),
(7, '30300408', '2147483647', 'None', 'hadi_slank@yahoo.com', 'http://sdnaluhaluhkecil1.mysch.id/'),
(19, '30311582', '2147483647', 'None', 'sdnambakianghuluawy@gmail.com', 'http://'),
(20, '30304028', '2147483647', 'None', 'sdnbadalungga09@gmail.com', 'http://'),
(21, '30311576', 'None', 'None', 'sdnbanuahanyar.btm@gmail.com', 'http://'),
(22, '30303948', '2147483647', 'None', 'sdn_bakung@yahoo.com', 'http://'),
(24, '30304037', '2147483647', 'None', 'sdnbungur.btm@gmail.com', 'http://'),
(25, '30303991', '2147483647', 'None', 'sdngunungmanauoke@gmail.com', 'http://'),
(26, '30304011', '2147483647', 'None', 'sdnhamparaya@gmail.com', 'http://'),
(27, '30303836', '2147483647', 'None', 'sdnkaruhbatumandi@gmail.com', 'http://'),
(28, '30303835', 'None', 'None', 'sdn.kasai@yahoo.com', 'http://'),
(29, '30303850', '2147483647', 'None', 'sdnlokbatu@yahoo.co.id', 'http://'),
(30, '30303843', '2147483647', 'None', 'sdn_mampari@yahoo.com', 'http://'),
(31, '30303809', '2147483647', 'None', 'sdnmantimin1@yahoo.com', 'http://'),
(32, '30303993', '2147483647', 'None', 'sdn_guha2@yahoo.com', 'http://'),
(33, '30303824', '2147483647', 'None', 'sdnmunjung6@gmail.com', 'http://'),
(34, '30303808', '2147483647', 'None', 'sdnmantimin2blg@gmail.com', 'http://'),
(35, '30303902', '2147483647', 'None', 'sdnriwa4@gmail.com', 'None'),
(36, '30303815', '2147483647', 'None', 'sdnpelajau@gmail.com', 'http://'),
(37, '30303893', '2147483647', 'None', 'sdnsungaihanyarbtm@gmail.com', 'http://'),
(38, '30303895', 'None', 'None', 'sdnsumpung.batumandi@gmail.com', 'http://'),
(39, '30303905', '2147483647', 'None', 'sdnsungaikusi@gmail.com', 'http://'),
(40, '30311473', '2147483647', 'None', 'sdn.telukmesjid1@yahoo.com', 'http://'),
(41, '30303867', '2147483647', 'None', 'Timbuntulang@yahoo.co.id', 'http://'),
(42, '30303886', 'None', 'None', 'smpn1batumandi@yahoo.co.id', 'http://smpn.1batumandi@yahoo.com'),
(43, '30303880', '2147483647', 'None', 'smpnbatumandi02@gmail.com', 'http://'),
(44, '30311634', '2147483647', 'None', 'smpn3batumandi2022@gmail.com', 'http://'),
(45, '30304977', '2147483647', 'None', 'sbatumandi@gmail.com', 'http://www.smkn1batumandi.sch.id'),
(46, '69774538', '2147483647', 'None', 'smpn.4batumandi@gmail.com', 'http://'),
(47, '30311478', '2147483647', 'None', 'smpalquranikhwanulmuslimin@gmail.com', 'http://'),
(48, '30305425', 'None', 'None', 'smansaawy@gmail.com', 'http://sman1awayan.sch.id'),
(49, '30303873', '2147483647', 'None', 'smpnawayan@gmail.com', 'http://'),
(50, '30311636', '2147483647', 'None', 'smpn4wayanblg@gmail.com', 'http://');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_lainnya`
--

CREATE TABLE `sekolah_lainnya` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kepala_sekolah` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operator_pendataan` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `akreditasi` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kurikulum` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah_lainnya`
--

INSERT INTO `sekolah_lainnya` (`id`, `npsn_fk`, `kepala_sekolah`, `operator_pendataan`, `akreditasi`, `kurikulum`) VALUES
(2, '30300398', 'Grecenawati Kartini', 'Fachmi Salim', 'Tidak diisi', 'Kurikulum Merdeka'),
(3, '30305358', 'Nurus Saadah', 'Hafizh Rumaidi', 'C', 'Kurikulum Merdeka'),
(4, '30304025', 'Muhammad Noorkhalis', 'ANITA KAMILA', 'B', 'Kurikulum Merdeka'),
(7, '30300408', 'H.iberamsyah', 'Siti Annisah', 'B', 'Kurikulum Merdeka'),
(19, '30311582', 'Qasthalani B', 'HILYATUN NIDA', 'B', 'Kurikulum Merdeka'),
(20, '30304028', 'Muhammad As\'ad', 'Muhammad Arnie', 'Tidak diisi', 'Kurikulum Merdeka'),
(21, '30311576', 'Abdul Hair', 'MUHAMMAD RUSPIANSYAH', 'B', 'Kurikulum Merdeka'),
(22, '30303948', 'Rika Rahim', 'Linda Agustina', 'B', 'Kurikulum Merdeka'),
(24, '30304037', 'Hasan Basri', 'MAIMUNAH', 'B', 'Kurikulum Merdeka'),
(25, '30303991', 'Zainal Abidin', 'SHERLYANI', 'B', 'Kurikulum Merdeka'),
(26, '30304011', 'Padlian Sarianto', 'Rahmiadi', 'B', 'Kurikulum Merdeka'),
(27, '30303836', 'Wartinah', 'Ahmad Rionaldi', 'B', 'Kurikulum Merdeka'),
(28, '30303835', 'Subhan Saputra', 'M. HAIRI AHYAT', 'B', 'Kurikulum Merdeka'),
(29, '30303850', 'Rahmadi', 'Norlaila Hayati', 'B', 'Kurikulum Merdeka'),
(30, '30303843', 'Sadikin', 'Ahmad Zulkarnain', 'B', 'Kurikulum Merdeka'),
(31, '30303809', 'Muriatun Nikmah', 'Helda Yuliasari', 'A', 'Kurikulum Merdeka'),
(32, '30303993', 'Jumi Puspitasari', 'Misriyani', 'B', 'Kurikulum Merdeka'),
(33, '30303824', 'Saifullah', 'FATIMATUL ZAHRA', 'B', 'Kurikulum Merdeka'),
(34, '30303808', 'Muhammad Raili, S.pd', 'MUHAMMAD MUJAHIDIN', 'B', 'Kurikulum Merdeka'),
(35, '30303902', 'Rusmahayati', 'Aulia', 'B', 'Kurikulum Merdeka'),
(36, '30303815', 'Salasiah', 'Hariyanti', 'B', 'Kurikulum Merdeka'),
(37, '30303893', 'Hernani', 'Ahmad Juliadi Rahman', 'B', 'Kurikulum Merdeka'),
(38, '30303895', 'Pahriani', 'DWI AGUSTINA SUSANTIE', 'B', 'Kurikulum Merdeka'),
(39, '30303905', 'Rusdiana', 'MUNAWARATUN NISA', 'B', 'Kurikulum Merdeka'),
(40, '30311473', 'Binti Masamah', 'fendy haryadi', 'B', 'Kurikulum Merdeka'),
(41, '30303867', 'Zulaiha', 'Normayati', 'B', 'Kurikulum Merdeka'),
(42, '30303886', 'Akhilin Amimos', 'Rusdiansyah', 'A', 'Kurikulum Merdeka'),
(43, '30303880', 'Ahmad Riyadie', 'Syafriansyah', 'B', 'Kurikulum Merdeka'),
(44, '30311634', 'Ahmadiyanto', 'IIN FITRYANIE', 'A', 'Kurikulum Merdeka'),
(45, '30304977', 'Ernawati', 'Muhammad Syafi\'e', 'B', 'Kurikulum Merdeka'),
(46, '69774538', 'Handera Wartinah', 'Salasiah', 'B', 'Kurikulum Merdeka'),
(47, '30311478', 'Rahmidah', 'SOFIA', 'C', 'Kurikulum Merdeka'),
(48, '30305425', 'Abdul Haliq', 'Abdul Samad', 'Tidak diisi', 'Kurikulum Merdeka'),
(49, '30303873', 'Muhamad Adi Suriadi Syofyan', 'M. IRNADI', 'A', 'Kurikulum Merdeka'),
(50, '30311636', 'Syaiful Bahri', 'Herlina Helnayanti', 'Tidak diisi', 'Kurikulum Merdeka');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_pelengkap`
--

CREATE TABLE `sekolah_pelengkap` (
  `id` int(11) NOT NULL,
  `npsn_fk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sk_pendirian` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_sk_pendirian` date DEFAULT NULL,
  `status_kepemilikan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sk_izin_operasional` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_sk_izin_operasional` date DEFAULT NULL,
  `kebutuhan_khusus_dilayani` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_rekening` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_bank` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cabang_kcp_unit` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rekening_atas_nama` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mbs` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `luas_tanah_milik_m2` int(11) DEFAULT NULL,
  `luas_tanah_bukan_milik_m2` int(11) DEFAULT NULL,
  `nama_wajib_pajak` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah_pelengkap`
--

INSERT INTO `sekolah_pelengkap` (`id`, `npsn_fk`, `sk_pendirian`, `tgl_sk_pendirian`, `status_kepemilikan`, `sk_izin_operasional`, `tgl_sk_izin_operasional`, `kebutuhan_khusus_dilayani`, `nomor_rekening`, `nama_bank`, `cabang_kcp_unit`, `rekening_atas_nama`, `mbs`, `luas_tanah_milik_m2`, `luas_tanah_bukan_milik_m2`, `nama_wajib_pajak`, `npwp`) VALUES
(2, '30300398', '421.2/208-AR/SK-SD/DISDIK/2017', '2017-09-13', 'Pemerintah Daerah', '421.2/208-AR/SKIO-SD/DISDIK/2017', '2017-09-13', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG MARTAPURA...', 'SDNARANIO1...', 'Ya', 3, 0, 'None', '004873675732000'),
(3, '30305358', '199 Tahun 2006', '2006-07-17', 'Pemerintah Daerah', '199 TAHUN 2006', '2006-07-17', 'Tidak ada', '009.03.01.20445.2', 'Bank KALSEL', 'MARTAPURA', 'SMPN 4 Aranio', 'Ya', 3, 0, 'BEND. SMPN 4 ARANIO', '005923248732000'),
(4, '30304025', '421/155/BK.Disdikbud/2023', '1983-05-09', 'Pemerintah Daerah', '421/155/BK.Disdikbud/2023', '1983-05-09', 'Tidak ada', '2147483647', 'BPD KAL-SEL', 'PARINGIN', 'BENDAHARA BOS SDN BELANTI', 'Ya', 3, 0, 'SDN BALANTI', '001220631735000'),
(7, '30300408', 'None', '1950-08-18', 'Pemerintah Daerah', 'None', '1910-01-01', 'Tidak ada', '2147483647', 'BPD Kalsel', 'Martapura', 'SDN Aluh Aluh Kecil 1', 'Ya', 3, 0, 'None', '0013720857320000125'),
(19, '30311582', '421/137/Bk.Sarpras/Disdikbud/2022', '1979-09-01', 'Pemerintah Daerah', '421/137/BK.Sarpras/Disdikbud/2022', '1978-06-06', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG...', 'BENDAHARABOSSDNAMBAKIANGHULU...', 'Ya', 3, 0, 'None', '001220631735000'),
(20, '30304028', '421/1202/BK.Sarpras/Disdikbud/2022', '2002-07-25', 'Pemerintah Daerah', '421/1202/BK.Sapras/Disdikbud/2022', '2022-07-25', 'Tidak ada', '2147483647', 'BPD Kal-Sel', 'Paringin', 'Bendahara BOS SDN Badalungga', 'Ya', 3, 0, 'SDN Badalungga', '001220631735000'),
(21, '30311576', '421/788/BK.Sarpras/Disdikbud/2022', '1965-01-01', 'Pemerintah Daerah', '421/788/BK.Sarpras/Disdikbud/2022', '1965-01-01', 'Tidak ada', '013.03.02.00677.7', 'BPD KALSEL', 'Paringin', 'SDN BANUA HANYAR', 'Ya', 3, 0, 'None', '0012206317350000032'),
(22, '30303948', '037/BAP-S/M/PROV-15/LL/2012', '2012-09-07', 'Pemerintah Daerah', '421/165/DISDIK/2018', '2018-02-05', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG  PARINGIN...', 'BENDAHARABOSSDNBAKUNG...', 'Ya', 3, 0, 'None', '0012206317350000042'),
(24, '30304037', '421/165/DISDIK/2018', '2018-02-05', 'Pemerintah Daerah', '421/165/DISDIK/2018', '2018-02-05', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSDNBUNGUR...', 'Ya', 3, 0, 'None', '0012206317350000046'),
(25, '30303991', '421/155/BK.Sarpras/Disdikbud/2023', '1957-07-01', 'Pemerintah Daerah', '421/155/BK.Sarpras/Disdikbud/2023', '1957-07-01', 'Tidak ada', '2147483647', 'BANK KALSEL', 'Cabang Paringin', 'Bendahara BOS SDN Gunung Manau', 'Ya', 3, 0, 'None', '0012206317350000037'),
(26, '30304011', '421/1202/BK.Sapras/Disdik /2022', '2013-08-12', 'Pemerintah Daerah', '421/1202/BK.Sapras/Disdik /2022', '2022-07-25', 'Tidak ada', '013.03.02.00650.7', 'BANK BPD KALSEL', 'Paringin', 'BENDAHARA BOS SDN HAMPARAYA', 'Ya', 3, 0, 'None', '001220631735000'),
(27, '30303836', '421/175/DISDIK/2018', '1980-07-01', 'Pemerintah Daerah', '421/175/DISDIK/2018', '1980-07-01', 'Tidak ada', '2147483647', 'BPD KAL-SEL', 'PARINGIN', 'BENDAHARA BOS', 'Ya', 3, 0, 'None', '001220631735000'),
(28, '30303835', 'II-1/100/1/1982', '1982-06-30', 'Pemerintah Daerah', 'II-1-100/1/1982', '1982-06-30', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSDNKASAI...', 'Ya', 3, 0, 'None', '0012206317350000038'),
(29, '30303850', '421/001/Komite SDN-10/2023', '1910-01-01', 'Pemerintah Daerah', '421/175/DISDIK/2018', '1910-01-01', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG...', 'BENDAHARABOSSDNLOKBATU...', 'Ya', 3, 0, 'SDN Lok Batu', '001220631735000'),
(30, '30303843', '421/155/BK.Sarpras/Disdikbud/2023', '1978-01-01', 'Pemerintah Daerah', '421/155/BK.Sarpras/Disdikbud/2023', '2023-02-09', 'Tidak ada', '013.03.02.00670.1', 'BPD KALSEL', 'Cabang Paringin', 'BENDAHARA BOS SDN MAMPARI', 'Ya', 3, 0, 'None', '001220631735000'),
(31, '30303809', '421/1202//BK.Sarpras/Disdikbud/2022', '1976-01-01', 'Pemerintah Daerah', '421/1202//BK.Sarpras/Disdikbud/2022', '1976-01-01', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSDNMANTIMIN1...', 'Ya', 3, 0, 'None', '0012206317350000035'),
(32, '30303993', 'II-I/1827/1984', '1984-06-01', 'Pemerintah Daerah', 'II-I/1827/1984', '1984-06-01', 'Tidak ada', '013-03-02-00666-1', 'BANK KALSEL', 'cabang', 'SDN GUHA 2', 'Ya', 3, 0, 'SDN GUHA 2', '0012206317350000043'),
(33, '30303824', '421/155/BK.sarpras/disdikbud/2023', '1955-07-01', 'Pemerintah Daerah', '421/155/BK.sarpras/disdikbud/2023', '2023-02-10', 'Tidak ada', '2147483647', 'BANK KALSEL', 'PARINGIN', 'BENDAHARA BOS SDN MUNJUNG', 'Ya', 3, 0, 'BEND. SDN MUNJUNG', '0012206317350000029'),
(34, '30303808', '421/155/BK.Sarpras/Disdikbd/2023', '1983-07-01', 'Pemerintah Daerah', '421/155/BK.Sarpras/Disdikbd/2023', '2023-02-09', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSDNMANTIMIN2...', 'Ya', 3, 0, 'SDN MANTIMIN 2', '001220631735000'),
(35, '30303902', 'SD-470116/1980', '1980-07-01', 'Pemerintah Daerah', '421/155/Bk.Sarpras/Disdikbud/2023', '2023-02-09', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSDNRIWA...', 'Ya', 3, 0, 'None', '0012206317350000044'),
(36, '30303815', '421/155/BK.Sarpras/Disdikbud/2023', '1975-07-01', 'Pemerintah Daerah', '421/155/BK.Sarpras/Disdikbud/2023', '2023-01-10', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSDNPELAJAU...', 'Ya', 3, 0, 'SDN PELAJAU', '0012206317350000039'),
(37, '30303893', 'None', '1910-01-01', 'Pemerintah Daerah', '421/155/BK.Sarpras/Disdikbud/2023', '1984-01-01', 'Tidak ada', '2147483647', 'BANK KALSEL', 'PARINGIN', 'BENDAHARA BOS SDN SUNGAI HANYAR', 'Tidak', 3, 0, 'None', '001220631735000'),
(38, '30303895', '421/155/BK.Sarpras/Disdikbud/2023', '1980-07-01', 'Pemerintah Daerah', 'Surat Pernyataan Operasional Sekolah', '1980-07-01', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSDNSUMPUNG...', 'Ya', 3, 0, 'None', '0012206317350000030'),
(39, '30303905', '421/175/DISDIK/2018', '1980-07-01', 'Pemerintah Daerah', '421/175/DISDIK/2018', '2018-02-06', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSDNSUNGAIKUSI...', 'Ya', 3, 0, 'SDN SUNGAI KUSI', '001220631735000'),
(40, '30311473', '421/1155/BK.Sarpras/Disdikbud/2023', '1982-06-01', 'Pemerintah Daerah', '421/1155/BK.Sarpras/Disdikbud/2023', '1982-06-01', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSDNTELUKMESJID1...', 'Ya', 3, 0, 'BEND.SDN TELUK MESJID 1', '0012206317350000045'),
(41, '30303867', '421/224/BK.Sarpras/Disdikbud/2023', '1979-07-02', 'Pemerintah Pusat', '421/224/BK.Sarpras/Disdikbud/2023', '1979-07-02', 'Tidak ada', '2147483647', 'BANK BPD KAL-SEL', 'PARINGIN', 'BENDAHARA SDN TIMBUN TULANG', 'Ya', 3, 0, 'None', '0012206317350000027'),
(42, '30303886', '0190/O/1979', '1979-09-01', 'Pemerintah Daerah', '0190/O/1979', '1979-09-01', 'Tidak ada', '2147483647', 'BPD KALSEL', 'PARINGIN', 'BENDAHARA BOS SMPN 1 BATUMANDI', 'Ya', 3, 0, 'SMPN 1 BATUMANDI', '001220631735000'),
(43, '30303880', 'None', '1910-01-01', 'Pemerintah Daerah', 'None', '1910-01-01', 'Tidak ada', '2147483647', 'BPD KALSEL', 'PARINGIN', 'Bendahara SMPN 2 Batumandi', 'Ya', 3, 0, 'SMPN 2 Batumandi', '008584328735000'),
(44, '30311634', '112.a', '2007-05-01', 'Pemerintah Daerah', '421/155/BK.Sarpras/Disdikbud/2023', '2023-03-09', 'Tidak ada', '013.03.02.05018.4', 'BPD KALSEL', 'Paringin', 'Bendahara BOS SMP Negeri 3 Batumandi', 'Tidak', 3, 0, 'None', '0012206317350000033'),
(45, '30304977', '188.45/156/KUM 2007', '2007-08-01', 'Pemerintah Daerah', '188.45/156/KUM 2007', '2007-08-01', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG KCP PARINGIN...', 'BENDAHARABOSSMKN1BATUMANDI...', 'Ya', 3, 0, 'Bendahara Rutin SMKN 1 Batumandi', '001196799732000'),
(46, '69774538', '188.45/308/Kum Tahun 2013', '2013-07-01', 'Pemerintah Daerah', '188.45/308/Kum Tahun 2013', '2013-07-01', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSMPN4BATUMANDI...', 'Ya', 3, 0, 'None', '001220631735000'),
(47, '30311478', '420/1645/P&K/2005ttt', '2004-04-14', 'Yayasan', '420/1645/P&K/2005', '2005-11-29', 'Tidak ada', '2147483647', 'BANK KALSEL', 'PARINGIN', 'BENDAHARA BOS SMPS IKHWANUL MUSLIMIN', 'Ya', 3, 0, 'None', '001220631735000'),
(48, '30305425', '252/42.1/PK', '2005-12-02', 'Pemerintah Daerah', '252/42.1/PK', '2005-12-02', 'Tidak ada', '2147483647', 'BPD KALSEL', 'PARINGIN', 'BENDAHARA BOS SMAN 1 AWAYAN', 'Ya', 3, 0, 'None', '0011967997320000153'),
(49, '30303873', '2147483647', '1981-07-14', 'Pemerintah Daerah', '2147483647', '1981-07-14', 'Tidak ada', '2147483647', 'BPD KALIMANTAN SE...', 'BPD KALIMANTAN SELATAN CABANG PARINGIN...', 'BENDAHARABOSSMPN1AWAYAN...', 'Ya', 3, 0, 'Bendahara SMPN 1 Awayan', '0012206317350000181'),
(50, '30311636', '800/326/SKT/Disdik/2019', '2007-05-01', 'Pemerintah Daerah', '422/08/SMPN.4-AW/2019', '2019-03-04', 'Tidak ada', '2147483647', 'BPD KAL - SEL', 'Paringin', 'BENDAHARA BOS SMPN 4 AWAYAN', 'Ya', 3, 0, 'BENDAHARA BOS SMPN 4 AWAYAN', '0012206317350000007');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_scrape`
--

CREATE TABLE `sekolah_scrape` (
  `id` int(11) NOT NULL,
  `npsn` varchar(255) NOT NULL,
  `nama_sekolah` varchar(255) NOT NULL,
  `url` varchar(500) NOT NULL,
  `jenjang` varchar(10) NOT NULL,
  `kecamatan_scrape_id` int(11) NOT NULL,
  `status` enum('active','inactive','processed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel untuk menyimpan data sekolah hasil scraping';

--
-- Dumping data for table `sekolah_scrape`
--

INSERT INTO `sekolah_scrape` (`id`, `npsn`, `nama_sekolah`, `url`, `jenjang`, `kecamatan_scrape_id`, `status`, `created_at`, `updated_at`) VALUES
(1224, '30311582', 'SD NEGERI AMBAKIANG HULU', 'https://dapo.kemendikdasmen.go.id/sekolah/A1E192E00C0113901AEA', 'sd', 320, 'active', '2025-10-02 18:35:44', '2025-10-05 23:33:57'),
(1225, '30304028', 'SD NEGERI BADALUNGGA', 'https://dapo.kemendikdasmen.go.id/sekolah/7D48320A02662874853E', 'sd', 320, 'active', '2025-10-02 18:35:45', '2025-10-05 23:33:57'),
(1226, '30304027', 'SD NEGERI BADALUNGGA HILIR', 'https://dapo.kemendikdasmen.go.id/sekolah/2ABB3BB2692592F0A4B6', 'sd', 320, 'active', '2025-10-02 18:35:45', '2025-10-05 23:33:57'),
(1227, '30304025', 'SD NEGERI BALANTI', 'https://dapo.kemendikdasmen.go.id/sekolah/0E89F888761FE1C1F945', 'sd', 320, 'active', '2025-10-02 18:35:45', '2025-10-05 23:33:57'),
(1228, '30311603', 'SD NEGERI BARAMBAN', 'https://dapo.kemendikdasmen.go.id/sekolah/54A3FA1469D535C5AA6B', 'sd', 320, 'active', '2025-10-02 18:35:46', '2025-10-05 23:33:57'),
(1229, '30311602', 'SD NEGERI BARU', 'https://dapo.kemendikdasmen.go.id/sekolah/0DAA18AA95946F7944C8', 'sd', 320, 'active', '2025-10-02 18:35:46', '2025-10-05 23:33:57'),
(1230, '30311584', 'SD NEGERI BAYUR', 'https://dapo.kemendikdasmen.go.id/sekolah/C0F31D7AE0C893516EC3', 'sd', 320, 'active', '2025-10-02 18:35:46', '2025-10-05 23:33:57'),
(1231, '30311586', 'SD NEGERI MANINGAU', 'https://dapo.kemendikdasmen.go.id/sekolah/25A6D01E11ABBEE8552B', 'sd', 320, 'active', '2025-10-02 18:35:46', '2025-10-05 23:33:57'),
(1232, '30303796', 'SD NEGERI MERAH', 'https://dapo.kemendikdasmen.go.id/sekolah/1A914B53A50E4AA8B704', 'sd', 320, 'active', '2025-10-02 18:35:46', '2025-10-05 23:33:57'),
(1233, '30303799', 'SD NEGERI MUARA JAYA', 'https://dapo.kemendikdasmen.go.id/sekolah/A072C199899A43385B97', 'sd', 320, 'active', '2025-10-02 18:35:46', '2025-10-05 23:33:57'),
(1234, '30303821', 'SD NEGERI NUNGKA', 'https://dapo.kemendikdasmen.go.id/sekolah/B1D85725F7C5A53CD4A7', 'sd', 320, 'active', '2025-10-02 18:35:46', '2025-10-05 23:33:57'),
(1235, '30303814', 'SD NEGERI PEMATANG', 'https://dapo.kemendikdasmen.go.id/sekolah/F9BDEEAF8AFC410F4658', 'sd', 320, 'active', '2025-10-02 18:35:46', '2025-10-05 23:33:57'),
(1236, '30303812', 'SD NEGERI PIYAIT', 'https://dapo.kemendikdasmen.go.id/sekolah/8C9AB2B35F93D0E4C62A', 'sd', 320, 'active', '2025-10-02 18:35:47', '2025-10-05 23:33:57'),
(1237, '30303857', 'SD NEGERI PULANTAN', 'https://dapo.kemendikdasmen.go.id/sekolah/7005533830312B2F0149', 'sd', 320, 'active', '2025-10-02 18:35:47', '2025-10-05 23:33:57'),
(1238, '30303920', 'SD NEGERI PULAU KAMBANG', 'https://dapo.kemendikdasmen.go.id/sekolah/D3203ED44347A2E0249B', 'sd', 320, 'active', '2025-10-02 18:35:47', '2025-10-05 23:33:57'),
(1239, '30303904', 'SD NEGERI PUTAT BASIUN', 'https://dapo.kemendikdasmen.go.id/sekolah/AAF96392C2E0DBFAF3BC', 'sd', 320, 'active', '2025-10-02 18:35:47', '2025-10-05 23:33:57'),
(1240, '30303906', 'SD NEGERI SUNGAI PUMPUNG', 'https://dapo.kemendikdasmen.go.id/sekolah/16934B35A7E458C71777', 'sd', 320, 'active', '2025-10-02 18:35:48', '2025-10-05 23:33:57'),
(1241, '30303866', 'SD NEGERI TUNDAKAN', 'https://dapo.kemendikdasmen.go.id/sekolah/76E025FE217F73943498', 'sd', 320, 'active', '2025-10-02 18:35:48', '2025-10-05 23:33:57'),
(1242, '30303865', 'SD NEGERI TUNDI', 'https://dapo.kemendikdasmen.go.id/sekolah/1D2FEE468EC69586FA6D', 'sd', 320, 'active', '2025-10-02 18:35:48', '2025-10-05 23:33:57'),
(1243, '30303863', 'SD NEGERI UUNGAN', 'https://dapo.kemendikdasmen.go.id/sekolah/07AD9DF22C778EF0F51B', 'sd', 320, 'active', '2025-10-02 18:35:49', '2025-10-05 23:33:57'),
(1244, '30303862', 'SD NEGERI WANGKILI', 'https://dapo.kemendikdasmen.go.id/sekolah/348BEF4FA1FFC75E7D6A', 'sd', 320, 'active', '2025-10-02 18:35:49', '2025-10-05 23:33:57'),
(1245, '30303873', 'SMP NEGERI 1 AWAYAN', 'https://dapo.kemendikdasmen.go.id/sekolah/72EF0F390075682ED4E4', 'smp', 320, 'active', '2025-10-02 18:36:11', '2025-10-05 23:33:57'),
(1246, '30311636', 'SMP NEGERI 4 AWAYAN', 'https://dapo.kemendikdasmen.go.id/sekolah/96167D84966910DB9ED9', 'smp', 320, 'active', '2025-10-02 18:36:11', '2025-10-05 23:33:57'),
(1247, '30305425', 'SMAN 1 AWAYAN', 'https://dapo.kemendikdasmen.go.id/sekolah/F41946F8CCAF24B2BC6D', 'sma', 320, 'active', '2025-10-02 18:36:21', '2025-10-05 23:33:57'),
(1248, '30304024', 'SD NEGERI BALIDA', 'https://dapo.kemendikdasmen.go.id/sekolah/DE08636FB1B6F1C06E42', 'sd', 321, 'active', '2025-10-02 18:39:13', '2025-10-02 18:39:13'),
(1249, '30304034', 'SD NEGERI DAHAI', 'https://dapo.kemendikdasmen.go.id/sekolah/1D3B7279EF55FA67CCCE', 'sd', 321, 'active', '2025-10-02 18:39:13', '2025-10-02 18:39:13'),
(1250, '30304017', 'SD NEGERI DANAU BANTA', 'https://dapo.kemendikdasmen.go.id/sekolah/7EA5426B2BF5B820F351', 'sd', 321, 'active', '2025-10-02 18:39:13', '2025-10-02 18:39:13'),
(1251, '30303990', 'SD NEGERI GUNUNG PANDAU', 'https://dapo.kemendikdasmen.go.id/sekolah/756870AF4997A4DC5A92', 'sd', 321, 'active', '2025-10-02 18:39:13', '2025-10-02 18:39:13'),
(1252, '30304007', 'SD NEGERI HUJAN AMAS 1', 'https://dapo.kemendikdasmen.go.id/sekolah/A6A418164F541450308D', 'sd', 321, 'active', '2025-10-02 18:39:13', '2025-10-02 18:39:13'),
(1253, '30304006', 'SD NEGERI HUJAN AMAS 2', 'https://dapo.kemendikdasmen.go.id/sekolah/C988061CC68E60348D74', 'sd', 321, 'active', '2025-10-02 18:39:14', '2025-10-02 18:39:14'),
(1254, '30303840', 'SD NEGERI KALAHIANG', 'https://dapo.kemendikdasmen.go.id/sekolah/1954933D917CA7797928', 'sd', 321, 'active', '2025-10-02 18:39:14', '2025-10-02 18:39:14'),
(1255, '30311592', 'SD NEGERI LASUNG BATU 1', 'https://dapo.kemendikdasmen.go.id/sekolah/170EFC641BCB8EFE3F43', 'sd', 321, 'active', '2025-10-02 18:39:14', '2025-10-02 18:39:14'),
(1256, '30303852', 'SD NEGERI LASUNG BATU 2', 'https://dapo.kemendikdasmen.go.id/sekolah/15ECAF41EFF0EDAC16CD', 'sd', 321, 'active', '2025-10-02 18:39:14', '2025-10-02 18:39:14'),
(1257, '30303847', 'SD NEGERI LOK BATUNG', 'https://dapo.kemendikdasmen.go.id/sekolah/C50430105AE6A4A5C5D8', 'sd', 321, 'active', '2025-10-02 18:39:14', '2025-10-02 18:39:14'),
(1258, '30303845', 'SD NEGERI MALIHU', 'https://dapo.kemendikdasmen.go.id/sekolah/D29AC886601D028A4F46', 'sd', 321, 'active', '2025-10-02 18:39:14', '2025-10-02 18:39:14'),
(1259, '30303827', 'SD NEGERI MANGKAYAHU', 'https://dapo.kemendikdasmen.go.id/sekolah/15BBD9ADCB943D642891', 'sd', 321, 'active', '2025-10-02 18:39:14', '2025-10-02 18:39:14'),
(1260, '30303822', 'SD NEGERI MURUNG ILUNG', 'https://dapo.kemendikdasmen.go.id/sekolah/727C57CCEE157BFCBF3D', 'sd', 321, 'active', '2025-10-02 18:39:15', '2025-10-02 18:39:15'),
(1261, '30311590', 'SD NEGERI PARINGIN 1', 'https://dapo.kemendikdasmen.go.id/sekolah/FDFD0651CE5443145647', 'sd', 321, 'active', '2025-10-02 18:39:15', '2025-10-02 18:39:15'),
(1262, '30311630', 'SD NEGERI PARINGIN 2', 'https://dapo.kemendikdasmen.go.id/sekolah/FCFE1D57BC3401C34D22', 'sd', 321, 'active', '2025-10-02 18:39:15', '2025-10-02 18:39:15'),
(1263, '30303816', 'SD NEGERI PARINGIN TIMUR', 'https://dapo.kemendikdasmen.go.id/sekolah/6DC04A6EBEB2A830BED2', 'sd', 321, 'active', '2025-10-02 18:39:15', '2025-10-02 18:39:15'),
(1264, '30303892', 'SD NEGERI SUNGAI KETAPI', 'https://dapo.kemendikdasmen.go.id/sekolah/BE001E93C997A6D8A2EC', 'sd', 321, 'active', '2025-10-02 18:39:16', '2025-10-02 18:39:16'),
(1265, '70003433', 'SD ISLAM AL ISTIQOMAH', 'https://dapo.kemendikdasmen.go.id/sekolah/0E457074366304EA404B', 'sd', 321, 'active', '2025-10-02 18:39:16', '2025-10-02 18:39:16'),
(1266, '30311638', 'SMP NEGERI 3 PARINGIN', 'https://dapo.kemendikdasmen.go.id/sekolah/AEB4EA9732C2C1DB71CE', 'smp', 321, 'active', '2025-10-02 18:39:38', '2025-10-02 18:39:38'),
(1267, '69756868', 'SMP NEGERI 5 PARINGIN', 'https://dapo.kemendikdasmen.go.id/sekolah/69159EB16DDE9C89A2B5', 'smp', 321, 'active', '2025-10-02 18:39:38', '2025-10-02 18:39:38'),
(1268, '30311478', 'SMPS AL QURAN IKHWANUL MUSLIMIN', 'https://dapo.kemendikdasmen.go.id/sekolah/A862041AA01A7B952EB7', 'smp', 321, 'active', '2025-10-02 18:39:38', '2025-10-05 23:32:46'),
(1269, '30305424', 'SMAS AL - QUR AN IKHWANUL MUSLIMIN PARINGIN', 'https://dapo.kemendikdasmen.go.id/sekolah/501E7EDBBF66EC5C2306', 'sma', 321, 'active', '2025-10-02 18:39:47', '2025-10-02 18:39:47'),
(1270, '30312933', 'SMKPP NEGERI PARINGIN', 'https://dapo.kemendikdasmen.go.id/sekolah/100AEF2453FF9A7EA16F', 'smk', 321, 'active', '2025-10-02 18:40:14', '2025-10-02 18:40:14'),
(1271, '30311425', 'SD KECIL RANTAU PAKU', 'https://dapo.kemendikdasmen.go.id/sekolah/8447C7287D72135D0651', 'sd', 323, 'active', '2025-10-05 07:31:31', '2025-10-05 07:31:31'),
(1272, '30305493', 'SD KECIL RARANUM', 'https://dapo.kemendikdasmen.go.id/sekolah/5572548939C4441001F1', 'sd', 323, 'active', '2025-10-05 07:31:31', '2025-10-05 07:31:31'),
(1273, '69947887', 'SD KECIL SIMPANG BUMBUAN', 'https://dapo.kemendikdasmen.go.id/sekolah/2BF767F99AC1A8BFF1F6', 'sd', 323, 'active', '2025-10-05 07:31:31', '2025-10-05 07:31:31'),
(1274, '30304018', 'SD NEGERI AJUNG', 'https://dapo.kemendikdasmen.go.id/sekolah/112C63F536CFEF4D2EA5', 'sd', 323, 'active', '2025-10-05 07:31:31', '2025-10-05 07:31:31'),
(1275, '30304029', 'SD NEGERI AUH', 'https://dapo.kemendikdasmen.go.id/sekolah/16FA4D6628595247E612', 'sd', 323, 'active', '2025-10-05 07:31:31', '2025-10-05 07:31:31'),
(1276, '30303997', 'SD NEGERI DAYAK PITAP', 'https://dapo.kemendikdasmen.go.id/sekolah/A58FFD640F166BD2799E', 'sd', 323, 'active', '2025-10-05 07:31:31', '2025-10-05 07:31:31'),
(1277, '30304003', 'SD NEGERI JUUH', 'https://dapo.kemendikdasmen.go.id/sekolah/A1C40406FE7858B9708D', 'sd', 323, 'active', '2025-10-05 07:31:31', '2025-10-05 07:31:31'),
(1278, '30311585', 'SD NEGERI KAMBIYAIN', 'https://dapo.kemendikdasmen.go.id/sekolah/908C0A6995377276C02D', 'sd', 323, 'active', '2025-10-05 07:31:32', '2025-10-05 07:31:32'),
(1279, '30303854', 'SD NEGERI LANGKAP', 'https://dapo.kemendikdasmen.go.id/sekolah/7BB894B73F64BE71135B', 'sd', 323, 'active', '2025-10-05 07:31:32', '2025-10-05 07:31:32'),
(1280, '30303803', 'SD NEGERI MAYANAU', 'https://dapo.kemendikdasmen.go.id/sekolah/25BA38345CA16195A428', 'sd', 323, 'active', '2025-10-05 07:31:32', '2025-10-05 07:31:32'),
(1281, '30311593', 'SD NEGERI PANIKIN', 'https://dapo.kemendikdasmen.go.id/sekolah/9901906AA7EDBFD07FA2', 'sd', 323, 'active', '2025-10-05 07:31:32', '2025-10-05 07:31:32'),
(1282, '30311587', 'SD NEGERI SUNGSUM', 'https://dapo.kemendikdasmen.go.id/sekolah/6FB1444FF628C333A6A3', 'sd', 323, 'active', '2025-10-05 07:31:32', '2025-10-05 07:31:32'),
(1283, '30303915', 'SD NEGERI TEBING TINGGI', 'https://dapo.kemendikdasmen.go.id/sekolah/CF81422EB945336D043A', 'sd', 323, 'active', '2025-10-05 07:31:32', '2025-10-05 07:31:32'),
(1284, '30303881', 'SMP NEGERI 2 AWAYAN', 'https://dapo.kemendikdasmen.go.id/sekolah/C7273E9E04B44D120B3C', 'smp', 323, 'active', '2025-10-05 07:32:03', '2025-10-05 07:32:03'),
(1285, '30304972', 'SMP NEGERI 3 AWAYAN', 'https://dapo.kemendikdasmen.go.id/sekolah/75D8EC99E1F8F594CD4C', 'smp', 323, 'active', '2025-10-05 07:32:03', '2025-10-05 07:32:03'),
(1286, '69786628', 'SMAN 1 TEBING TINGGI', 'https://dapo.kemendikdasmen.go.id/sekolah/56F4A1E7B2334FAF1877', 'sma', 323, 'active', '2025-10-05 07:32:12', '2025-10-05 07:32:12'),
(1287, '69947884', 'SD KECIL Ambata', 'https://dapo.kemendikdasmen.go.id/sekolah/213F0D42E2D9BD29D5B4', 'sd', 316, 'active', '2025-10-05 07:37:52', '2025-10-05 07:37:52'),
(1288, '70001805', 'SD KECIL AMBATUNIN', 'https://dapo.kemendikdasmen.go.id/sekolah/074F0D7C413829DCBFAC', 'sd', 316, 'active', '2025-10-05 07:37:52', '2025-10-05 07:37:52'),
(1289, '30313963', 'SD KECIL AMPINANG', 'https://dapo.kemendikdasmen.go.id/sekolah/DAA9A91792AABFC1D11F', 'sd', 316, 'active', '2025-10-05 07:37:52', '2025-10-05 07:37:52'),
(1290, '69786703', 'SD Kecil ANDAMAI', 'https://dapo.kemendikdasmen.go.id/sekolah/32792D0436FD1F1D9482', 'sd', 316, 'active', '2025-10-05 07:37:52', '2025-10-05 07:37:52'),
(1291, '30313964', 'SD KECIL HAMPANG', 'https://dapo.kemendikdasmen.go.id/sekolah/4C75743BAFCC8E555302', 'sd', 316, 'active', '2025-10-05 07:37:52', '2025-10-05 07:37:52'),
(1292, '69947886', 'SD KECIL KURIHAI', 'https://dapo.kemendikdasmen.go.id/sekolah/9E477CF0F10CABD2B6FD', 'sd', 316, 'active', '2025-10-05 07:37:52', '2025-10-05 07:37:52'),
(1293, '30313965', 'SD KECIL LIBARU SUNGKAI', 'https://dapo.kemendikdasmen.go.id/sekolah/755CEAD750612D727E95', 'sd', 316, 'active', '2025-10-05 07:37:52', '2025-10-05 07:37:52'),
(1294, '30313968', 'SD KECIL MABULAN', 'https://dapo.kemendikdasmen.go.id/sekolah/69718F2F841F9A7E58D3', 'sd', 316, 'active', '2025-10-05 07:37:52', '2025-10-05 07:37:52'),
(1295, '69756869', 'SD KECIL MAPAT', 'https://dapo.kemendikdasmen.go.id/sekolah/515D99FD4EA0C30B2275', 'sd', 316, 'active', '2025-10-05 07:37:52', '2025-10-05 07:37:52'),
(1296, '69756870', 'SD KECIL SAWANG', 'https://dapo.kemendikdasmen.go.id/sekolah/8F9B3D1CBC5435988939', 'sd', 316, 'active', '2025-10-05 07:37:52', '2025-10-05 07:37:52'),
(1297, '69786704', 'SD Kecil SISIRIN', 'https://dapo.kemendikdasmen.go.id/sekolah/02ED4205A84CC9F1B588', 'sd', 316, 'active', '2025-10-05 07:37:53', '2025-10-05 07:37:53'),
(1298, '69947885', 'SD KECIL Tampaan', 'https://dapo.kemendikdasmen.go.id/sekolah/E6A999A5B1E4E9EBAF27', 'sd', 316, 'active', '2025-10-05 07:37:53', '2025-10-05 07:37:53'),
(1299, '69947876', 'SD KECIL Tanjungan Jelamu', 'https://dapo.kemendikdasmen.go.id/sekolah/EA3022F852F968F7EF69', 'sd', 316, 'active', '2025-10-05 07:37:53', '2025-10-05 07:37:53'),
(1300, '30313969', 'SD KECIL TENGGAR', 'https://dapo.kemendikdasmen.go.id/sekolah/2847E0DFC60566A6AEB1', 'sd', 316, 'active', '2025-10-05 07:37:53', '2025-10-05 07:37:53'),
(1301, '30304030', 'SD NEGERI ANIYUNGAN', 'https://dapo.kemendikdasmen.go.id/sekolah/C9D8C8CEA86E14AFE03A', 'sd', 316, 'active', '2025-10-05 07:37:53', '2025-10-05 07:37:53'),
(1302, '30304033', 'SD NEGERI BARUH PANYAMBARAN 1', 'https://dapo.kemendikdasmen.go.id/sekolah/74553CAA2BEDD5C42E46', 'sd', 316, 'active', '2025-10-05 07:37:53', '2025-10-05 07:37:53'),
(1303, '30304032', 'SD NEGERI BARUH PANYAMBARAN 2', 'https://dapo.kemendikdasmen.go.id/sekolah/82D04DA9F5B450D6E56D', 'sd', 316, 'active', '2025-10-05 07:37:53', '2025-10-05 07:37:53'),
(1304, '30304041', 'SD NEGERI BINJAI PUNGGAL 1', 'https://dapo.kemendikdasmen.go.id/sekolah/812AFD380B33BD54A440', 'sd', 316, 'active', '2025-10-05 07:37:53', '2025-10-05 07:37:53'),
(1305, '30304040', 'SD NEGERI BINJAI PUNGGAL 2', 'https://dapo.kemendikdasmen.go.id/sekolah/DD0BA63289577669C1B3', 'sd', 316, 'active', '2025-10-05 07:37:54', '2025-10-05 07:37:54'),
(1306, '30304039', 'SD NEGERI BINJU', 'https://dapo.kemendikdasmen.go.id/sekolah/B65CC79117F425228CDE', 'sd', 316, 'active', '2025-10-05 07:37:54', '2025-10-05 07:37:54'),
(1307, '30311577', 'SD NEGERI BINUANG SANTANG', 'https://dapo.kemendikdasmen.go.id/sekolah/44F8AECC00C3B93E6342', 'sd', 316, 'active', '2025-10-05 07:37:54', '2025-10-05 07:37:54'),
(1308, '30303989', 'SD NEGERI GUNUNG RIUT 1', 'https://dapo.kemendikdasmen.go.id/sekolah/0A3DA1B1755DDC918847', 'sd', 316, 'active', '2025-10-05 07:37:54', '2025-10-05 07:37:54'),
(1309, '30303988', 'SD NEGERI GUNUNG RIUT 2', 'https://dapo.kemendikdasmen.go.id/sekolah/A11FA5CA424FC2844660', 'sd', 316, 'active', '2025-10-05 07:37:54', '2025-10-05 07:37:54'),
(1310, '30303987', 'SD NEGERI HALONG 1', 'https://dapo.kemendikdasmen.go.id/sekolah/11FE2C6D3EC95E8F2018', 'sd', 316, 'active', '2025-10-05 07:37:54', '2025-10-05 07:37:54'),
(1311, '30303986', 'SD NEGERI HALONG 2', 'https://dapo.kemendikdasmen.go.id/sekolah/83E412B2CCAAEC6ED9B6', 'sd', 316, 'active', '2025-10-05 07:37:54', '2025-10-05 07:37:54'),
(1312, '30303985', 'SD NEGERI HALONG 3', 'https://dapo.kemendikdasmen.go.id/sekolah/2C56122D0B2ACA2DA3E2', 'sd', 316, 'active', '2025-10-05 07:37:54', '2025-10-05 07:37:54'),
(1313, '30304010', 'SD NEGERI HAUWAI 1', 'https://dapo.kemendikdasmen.go.id/sekolah/97B7309F677895C032D7', 'sd', 316, 'active', '2025-10-05 07:37:55', '2025-10-05 07:37:55'),
(1314, '30304009', 'SD NEGERI HAUWAI 2', 'https://dapo.kemendikdasmen.go.id/sekolah/A0F5A0984C09FE0CA368', 'sd', 316, 'active', '2025-10-05 07:37:55', '2025-10-05 07:37:55'),
(1315, '30303837', 'SD NEGERI KAPUL', 'https://dapo.kemendikdasmen.go.id/sekolah/F86824576F560E68D8A2', 'sd', 316, 'active', '2025-10-05 07:37:55', '2025-10-05 07:37:55'),
(1316, '30303844', 'SD NEGERI MAMANTANG', 'https://dapo.kemendikdasmen.go.id/sekolah/632CF840FB5343814A7A', 'sd', 316, 'active', '2025-10-05 07:37:55', '2025-10-05 07:37:55'),
(1317, '30303807', 'SD NEGERI MANTUYAN', 'https://dapo.kemendikdasmen.go.id/sekolah/0FDCC30D9DBD496420FF', 'sd', 316, 'active', '2025-10-05 07:37:55', '2025-10-05 07:37:55'),
(1318, '30303802', 'SD NEGERI MAUYA', 'https://dapo.kemendikdasmen.go.id/sekolah/C084AFF02370E264D562', 'sd', 316, 'active', '2025-10-05 07:37:55', '2025-10-05 07:37:55'),
(1319, '30303798', 'SD NEGERI MIHU 2', 'https://dapo.kemendikdasmen.go.id/sekolah/02BE28F7FA927A946AFF', 'sd', 316, 'active', '2025-10-05 07:37:55', '2025-10-05 07:37:55'),
(1320, '30303903', 'SD NEGERI PUYUN', 'https://dapo.kemendikdasmen.go.id/sekolah/660A139BBE1227015762', 'sd', 316, 'active', '2025-10-05 07:37:55', '2025-10-05 07:37:55'),
(1321, '30303914', 'SD NEGERI TABUAN 1', 'https://dapo.kemendikdasmen.go.id/sekolah/1234C57621F03D2FC788', 'sd', 316, 'active', '2025-10-05 07:37:56', '2025-10-05 07:37:56'),
(1322, '30303931', 'SD NEGERI TABUAN 2', 'https://dapo.kemendikdasmen.go.id/sekolah/C7E5676281519AC58D5B', 'sd', 316, 'active', '2025-10-05 07:37:56', '2025-10-05 07:37:56'),
(1323, '30303864', 'SD NEGERI UREN 1', 'https://dapo.kemendikdasmen.go.id/sekolah/C3B54FAA81C9ADB40BA5', 'sd', 316, 'active', '2025-10-05 07:37:56', '2025-10-05 07:37:56'),
(1324, '70001236', 'SDIT AL BUSYRA', 'https://dapo.kemendikdasmen.go.id/sekolah/8DA59AC393BF114933E1', 'sd', 316, 'active', '2025-10-05 07:37:56', '2025-10-05 07:37:56'),
(1325, '30303885', 'SMP NEGERI 1 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/EED785DA17C0BFAFC7C4', 'smp', 316, 'active', '2025-10-05 07:38:17', '2025-10-05 07:38:17'),
(1326, '30303879', 'SMP NEGERI 2 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/66548072313FB4DE127F', 'smp', 316, 'active', '2025-10-05 07:38:18', '2025-10-05 07:38:18'),
(1327, '30303877', 'SMP NEGERI 3 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/088A859DB16024099336', 'smp', 316, 'active', '2025-10-05 07:38:18', '2025-10-05 07:38:18'),
(1328, '30304974', 'SMP NEGERI 4 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/61E9D74BD2E9BCBDC22A', 'smp', 316, 'active', '2025-10-05 07:38:18', '2025-10-05 07:38:18'),
(1329, '30311641', 'SMP NEGERI 5 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/9DD25F305FDDA0633E48', 'smp', 316, 'active', '2025-10-05 07:38:18', '2025-10-05 07:38:18'),
(1330, '69900718', 'SMP NEGERI 6 HALONG SATU ATAP', 'https://dapo.kemendikdasmen.go.id/sekolah/07696553358D086CFB20', 'smp', 316, 'active', '2025-10-05 07:38:18', '2025-10-05 07:38:18'),
(1331, '69984786', 'SMP SATU ATAP LIBARU SUNGKAI', 'https://dapo.kemendikdasmen.go.id/sekolah/6F04B999F9492ADD9776', 'smp', 316, 'active', '2025-10-05 07:38:18', '2025-10-05 07:38:18'),
(1332, '30304976', 'SMAN 1 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/916442C496404828BC0F', 'sma', 316, 'active', '2025-10-05 07:38:28', '2025-10-05 07:38:28'),
(1333, '69851427', 'SMAN 2 HALONG', 'https://dapo.kemendikdasmen.go.id/sekolah/8FDC047DB16AFD7DD9A3', 'sma', 316, 'active', '2025-10-05 07:38:28', '2025-10-05 07:38:28'),
(1334, '30303948', 'SD NEGERI BAKUNG', 'https://dapo.kemendikdasmen.go.id/sekolah/C10D1B294B13B79EE291', 'sd', 319, 'active', '2025-10-05 07:42:05', '2025-10-05 07:46:04'),
(1335, '30311576', 'SD NEGERI BANUA HANYAR', 'https://dapo.kemendikdasmen.go.id/sekolah/2B495794D274B4B21CF7', 'sd', 319, 'active', '2025-10-05 07:42:06', '2025-10-05 07:46:04'),
(1336, '30304037', 'SD NEGERI BUNGUR', 'https://dapo.kemendikdasmen.go.id/sekolah/7229047CB2E3EC34D190', 'sd', 319, 'active', '2025-10-05 07:42:06', '2025-10-05 07:46:04'),
(1337, '30303940', 'SD NEGERI GUHA 1', 'https://dapo.kemendikdasmen.go.id/sekolah/65FA6D60CFDDCC24356B', 'sd', 319, 'active', '2025-10-05 07:42:06', '2025-10-05 07:46:04'),
(1338, '30303993', 'SD NEGERI GUHA 2', 'https://dapo.kemendikdasmen.go.id/sekolah/B7E86AC0C81029D86FA3', 'sd', 319, 'active', '2025-10-05 07:42:06', '2025-10-05 07:46:04'),
(1339, '30303991', 'SD NEGERI GUNUNG MANAU', 'https://dapo.kemendikdasmen.go.id/sekolah/DF4262EBD73342C04717', 'sd', 319, 'active', '2025-10-05 07:42:06', '2025-10-05 07:46:04'),
(1340, '30304011', 'SD NEGERI HAMPARAYA', 'https://dapo.kemendikdasmen.go.id/sekolah/A5D1D9C2706A0345CD54', 'sd', 319, 'active', '2025-10-05 07:42:06', '2025-10-05 07:46:04'),
(1341, '30303836', 'SD NEGERI KARUH', 'https://dapo.kemendikdasmen.go.id/sekolah/0C2069AB3C100D7AEAE7', 'sd', 319, 'active', '2025-10-05 07:42:06', '2025-10-05 07:46:04'),
(1342, '30303835', 'SD NEGERI KASAI', 'https://dapo.kemendikdasmen.go.id/sekolah/03392A64B2534E9FA94C', 'sd', 319, 'active', '2025-10-05 07:42:06', '2025-10-05 07:46:04'),
(1343, '30303850', 'SD NEGERI LOK BATU', 'https://dapo.kemendikdasmen.go.id/sekolah/2131DADE4F6C358DC9F3', 'sd', 319, 'active', '2025-10-05 07:42:07', '2025-10-05 07:46:04'),
(1344, '30303843', 'SD NEGERI MAMPARI', 'https://dapo.kemendikdasmen.go.id/sekolah/6599E8A9DF10BF197C5D', 'sd', 319, 'active', '2025-10-05 07:42:07', '2025-10-05 07:46:04'),
(1345, '30303809', 'SD NEGERI MANTIMIN 1', 'https://dapo.kemendikdasmen.go.id/sekolah/D298E6095AA79B89B9FA', 'sd', 319, 'active', '2025-10-05 07:42:07', '2025-10-05 07:46:04'),
(1346, '30303808', 'SD NEGERI MANTIMIN 2', 'https://dapo.kemendikdasmen.go.id/sekolah/6CE5DE48572759CCD9D3', 'sd', 319, 'active', '2025-10-05 07:42:07', '2025-10-05 07:46:04'),
(1347, '30303824', 'SD NEGERI MUNJUNG', 'https://dapo.kemendikdasmen.go.id/sekolah/69378FDD376B19DC0FDB', 'sd', 319, 'active', '2025-10-05 07:42:07', '2025-10-05 07:46:04'),
(1348, '30303815', 'SD NEGERI PELAJAU', 'https://dapo.kemendikdasmen.go.id/sekolah/119180010CED5243A330', 'sd', 319, 'active', '2025-10-05 07:42:07', '2025-10-05 07:46:04'),
(1349, '30303902', 'SD NEGERI RIWA', 'https://dapo.kemendikdasmen.go.id/sekolah/7698868D893D7CFD4FDE', 'sd', 319, 'active', '2025-10-05 07:42:07', '2025-10-05 07:46:04'),
(1350, '30303895', 'SD NEGERI SUMPUNG', 'https://dapo.kemendikdasmen.go.id/sekolah/87F6034C9B1D1579DDCD', 'sd', 319, 'active', '2025-10-05 07:42:07', '2025-10-05 07:46:04'),
(1351, '30303893', 'SD NEGERI SUNGAI HANYAR', 'https://dapo.kemendikdasmen.go.id/sekolah/8B72133B80A0BE77EDE1', 'sd', 319, 'active', '2025-10-05 07:42:07', '2025-10-05 07:46:04'),
(1352, '30303905', 'SD NEGERI SUNGAI KUSI', 'https://dapo.kemendikdasmen.go.id/sekolah/C001D2BB072818C833A3', 'sd', 319, 'active', '2025-10-05 07:42:08', '2025-10-05 07:46:04'),
(1353, '30311473', 'SD NEGERI TELUK MESJID 1', 'https://dapo.kemendikdasmen.go.id/sekolah/AC92070B028E944ADD13', 'sd', 319, 'active', '2025-10-05 07:42:08', '2025-10-05 07:46:04'),
(1354, '30303867', 'SD NEGERI TIMBUN TULANG', 'https://dapo.kemendikdasmen.go.id/sekolah/1A26E8CFA3B2CE7492AB', 'sd', 319, 'active', '2025-10-05 07:42:08', '2025-10-05 07:46:04'),
(1355, '30303886', 'SMP NEGERI 1 BATUMANDI', 'https://dapo.kemendikdasmen.go.id/sekolah/2CE1108C99C9410D988D', 'smp', 319, 'active', '2025-10-05 07:42:29', '2025-10-05 07:46:04'),
(1356, '30303880', 'SMP NEGERI 2 BATUMANDI', 'https://dapo.kemendikdasmen.go.id/sekolah/7792DA7D572070040E45', 'smp', 319, 'active', '2025-10-05 07:42:30', '2025-10-05 07:46:04'),
(1357, '30311634', 'SMP NEGERI 3 BATUMANDI', 'https://dapo.kemendikdasmen.go.id/sekolah/B1699952ED89667E36C3', 'smp', 319, 'active', '2025-10-05 07:42:30', '2025-10-05 07:46:04'),
(1358, '69774538', 'SMP NEGERI 4 BATUMANDI', 'https://dapo.kemendikdasmen.go.id/sekolah/23D0E0AD66B4BD7FC552', 'smp', 319, 'active', '2025-10-05 07:42:30', '2025-10-05 07:46:04'),
(1359, '30304977', 'SMKN 1 BATUMANDI', 'https://dapo.kemendikdasmen.go.id/sekolah/D604772E0F347E9CB15F', 'smk', 319, 'active', '2025-10-05 07:42:56', '2025-10-05 07:46:04'),
(1360, '69973171', 'SD NEGERI BATA', 'https://dapo.kemendikdasmen.go.id/sekolah/1124F0DFFAE954280BEA', 'sd', 322, 'active', '2025-10-05 07:43:23', '2025-10-05 07:43:23'),
(1361, '30311624', 'SD NEGERI BUNTU KARAU 1', 'https://dapo.kemendikdasmen.go.id/sekolah/CCB33F32C1373A463906', 'sd', 322, 'active', '2025-10-05 07:43:23', '2025-10-05 07:43:23'),
(1362, '30311476', 'SD NEGERI GELUMBANG', 'https://dapo.kemendikdasmen.go.id/sekolah/B10CB2981CCDB61CF423', 'sd', 322, 'active', '2025-10-05 07:43:24', '2025-10-05 07:43:24'),
(1363, '30303992', 'SD NEGERI GULINGGANG', 'https://dapo.kemendikdasmen.go.id/sekolah/E7493E18B0392BA4F209', 'sd', 322, 'active', '2025-10-05 07:43:24', '2025-10-05 07:43:24'),
(1364, '30304015', 'SD NEGERI HAMARUNG 1', 'https://dapo.kemendikdasmen.go.id/sekolah/672E16284FF2FB39AE07', 'sd', 322, 'active', '2025-10-05 07:43:24', '2025-10-05 07:43:24'),
(1365, '30304014', 'SD NEGERI HAMARUNG 2', 'https://dapo.kemendikdasmen.go.id/sekolah/2462CEA153D676BEAA24', 'sd', 322, 'active', '2025-10-05 07:43:24', '2025-10-05 07:43:24'),
(1366, '30304005', 'SD NEGERI HUKAI', 'https://dapo.kemendikdasmen.go.id/sekolah/D224D942F5A15B985E47', 'sd', 322, 'active', '2025-10-05 07:43:24', '2025-10-05 07:43:24'),
(1367, '30311591', 'SD NEGERI JUAI', 'https://dapo.kemendikdasmen.go.id/sekolah/0213DC1EEC91BD35B829', 'sd', 322, 'active', '2025-10-05 07:43:24', '2025-10-05 07:43:24'),
(1368, '30303829', 'SD NEGERI LALAYAU 1', 'https://dapo.kemendikdasmen.go.id/sekolah/3666603402E50AEE6B45', 'sd', 322, 'active', '2025-10-05 07:43:24', '2025-10-05 07:43:24'),
(1369, '30303828', 'SD NEGERI LALAYAU 2', 'https://dapo.kemendikdasmen.go.id/sekolah/7456CDC745192EC67C9A', 'sd', 322, 'active', '2025-10-05 07:43:24', '2025-10-05 07:43:24'),
(1370, '30303805', 'SD NEGERI MARIAS', 'https://dapo.kemendikdasmen.go.id/sekolah/C66C42A6E2D51625CF04', 'sd', 322, 'active', '2025-10-05 07:43:24', '2025-10-05 07:43:24'),
(1371, '30303797', 'SD NEGERI MIHU 1', 'https://dapo.kemendikdasmen.go.id/sekolah/86E046F192AAB3306D69', 'sd', 322, 'active', '2025-10-05 07:43:24', '2025-10-05 07:43:24'),
(1372, '30303800', 'SD NEGERI MUARA NINIAN', 'https://dapo.kemendikdasmen.go.id/sekolah/7EE55858C7C5EBE694BA', 'sd', 322, 'active', '2025-10-05 07:43:24', '2025-10-05 07:43:24'),
(1373, '30303811', 'SD NEGERI MUNGKUR UYAM', 'https://dapo.kemendikdasmen.go.id/sekolah/465778393BE9A9EBB649', 'sd', 322, 'active', '2025-10-05 07:43:25', '2025-10-05 07:43:25'),
(1374, '30303820', 'SD NEGERI PAMURUS', 'https://dapo.kemendikdasmen.go.id/sekolah/FD400B637C47215B3514', 'sd', 322, 'active', '2025-10-05 07:43:25', '2025-10-05 07:43:25'),
(1375, '30303898', 'SD NEGERI SIRAP 1', 'https://dapo.kemendikdasmen.go.id/sekolah/6857A1A5A3BC969B2641', 'sd', 322, 'active', '2025-10-05 07:43:25', '2025-10-05 07:43:25'),
(1376, '30303894', 'SD NEGERI SUNGAI BATUNG', 'https://dapo.kemendikdasmen.go.id/sekolah/089C3AAAAF739EEECE7B', 'sd', 322, 'active', '2025-10-05 07:43:25', '2025-10-05 07:43:25'),
(1377, '30303891', 'SD NEGERI TAWAHAN', 'https://dapo.kemendikdasmen.go.id/sekolah/22B5C77F5D1AD2E74129', 'sd', 322, 'active', '2025-10-05 07:43:25', '2025-10-05 07:43:25'),
(1378, '30303871', 'SD NEGERI TELUK BAYUR 2', 'https://dapo.kemendikdasmen.go.id/sekolah/4C0508CC5969AF390144', 'sd', 322, 'active', '2025-10-05 07:43:25', '2025-10-05 07:43:25'),
(1379, '30303870', 'SD NEGERI TELUK BAYUR 3', 'https://dapo.kemendikdasmen.go.id/sekolah/6171A99B582DBADD4B8E', 'sd', 322, 'active', '2025-10-05 07:43:25', '2025-10-05 07:43:25'),
(1380, '30303868', 'SD NEGERI TIGARUN', 'https://dapo.kemendikdasmen.go.id/sekolah/17927E8B8861977DBC8E', 'sd', 322, 'active', '2025-10-05 07:43:25', '2025-10-05 07:43:25'),
(1381, '30303884', 'SMP NEGERI 1 JUAI', 'https://dapo.kemendikdasmen.go.id/sekolah/29FED54D1AE0A3174888', 'smp', 322, 'active', '2025-10-05 07:43:47', '2025-10-05 07:43:47'),
(1382, '30303878', 'SMP NEGERI 2 JUAI', 'https://dapo.kemendikdasmen.go.id/sekolah/1C6640C316449DC98647', 'smp', 322, 'active', '2025-10-05 07:43:47', '2025-10-05 07:43:47'),
(1383, '30303861', 'SMAN 1 JUAI', 'https://dapo.kemendikdasmen.go.id/sekolah/14F94D080B194E741819', 'sma', 322, 'active', '2025-10-05 07:43:56', '2025-10-05 07:43:56'),
(1384, '69851428', 'SMAN 2 JUAI', 'https://dapo.kemendikdasmen.go.id/sekolah/471169CB1301B012B2D2', 'sma', 322, 'active', '2025-10-05 07:43:56', '2025-10-05 07:43:56');

-- --------------------------------------------------------

--
-- Table structure for table `url_induk_scrape`
--

CREATE TABLE `url_induk_scrape` (
  `id` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','processed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel untuk menyimpan URL induk yang diinput melalui website';

--
-- Dumping data for table `url_induk_scrape`
--

INSERT INTO `url_induk_scrape` (`id`, `url`, `description`, `status`, `created_at`, `updated_at`) VALUES
(4, 'https://dapo.kemendikdasmen.go.id/sp/1/150000', 'URL Induk Dapodik - 2025-10-03 01:01:19', 'active', '2025-10-02 18:01:19', '2025-10-02 18:01:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `import_log`
--
ALTER TABLE `import_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_import_user` (`user_id`);

--
-- Indexes for table `kabupaten_kota`
--
ALTER TABLE `kabupaten_kota`
  ADD PRIMARY KEY (`id_kabupaten`),
  ADD KEY `id_provinsi_fk` (`id_provinsi_fk`);

--
-- Indexes for table `kabupaten_scrape`
--
ALTER TABLE `kabupaten_scrape`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_kabupaten_unique` (`kode_kabupaten`,`url_induk_id`),
  ADD KEY `url_induk_id` (`url_induk_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_kabupaten_url_induk` (`url_induk_id`,`status`);

--
-- Indexes for table `kecamatan`
--
ALTER TABLE `kecamatan`
  ADD PRIMARY KEY (`id_kecamatan`),
  ADD KEY `id_kabupaten_fk` (`id_kabupaten_fk`);

--
-- Indexes for table `kecamatan_scrape`
--
ALTER TABLE `kecamatan_scrape`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_kecamatan_unique` (`kode_kecamatan`,`kabupaten_scrape_id`),
  ADD KEY `kabupaten_scrape_id` (`kabupaten_scrape_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_kecamatan_kabupaten` (`kabupaten_scrape_id`,`status`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `negara`
--
ALTER TABLE `negara`
  ADD PRIMARY KEY (`id_negara`),
  ADD UNIQUE KEY `nama_negara` (`nama_negara`);

--
-- Indexes for table `provinsi`
--
ALTER TABLE `provinsi`
  ADD PRIMARY KEY (`id_provinsi`),
  ADD KEY `id_negara_fk` (`id_negara_fk`);

--
-- Indexes for table `rekap_ptk_pd`
--
ALTER TABLE `rekap_ptk_pd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `rekap_rombel`
--
ALTER TABLE `rekap_rombel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `rekap_sarpras`
--
ALTER TABLE `rekap_sarpras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `scraping_logs`
--
ALTER TABLE `scraping_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_logs_user` (`user_id`),
  ADD KEY `fk_logs_url` (`scraping_url_id`);

--
-- Indexes for table `scraping_urls`
--
ALTER TABLE `scraping_urls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sekolah_scrape_id` (`sekolah_scrape_id`),
  ADD KEY `fk_scraping_urls_to_kecamatan` (`kecamatan_scrape_id`),
  ADD KEY `fk_urls_user` (`user_id`);

--
-- Indexes for table `sekolah_identitas`
--
ALTER TABLE `sekolah_identitas`
  ADD PRIMARY KEY (`npsn`),
  ADD KEY `id_kecamatan_fk` (`id_kecamatan_fk`);

--
-- Indexes for table `sekolah_kontak`
--
ALTER TABLE `sekolah_kontak`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `sekolah_lainnya`
--
ALTER TABLE `sekolah_lainnya`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `sekolah_pelengkap`
--
ALTER TABLE `sekolah_pelengkap`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `npsn_fk` (`npsn_fk`);

--
-- Indexes for table `sekolah_scrape`
--
ALTER TABLE `sekolah_scrape`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `npsn_unique` (`npsn`,`kecamatan_scrape_id`),
  ADD KEY `kecamatan_scrape_id` (`kecamatan_scrape_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_jenjang` (`jenjang`),
  ADD KEY `idx_sekolah_kecamatan` (`kecamatan_scrape_id`,`status`);

--
-- Indexes for table `url_induk_scrape`
--
ALTER TABLE `url_induk_scrape`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url_unique` (`url`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `import_log`
--
ALTER TABLE `import_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `kabupaten_kota`
--
ALTER TABLE `kabupaten_kota`
  MODIFY `id_kabupaten` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kabupaten_scrape`
--
ALTER TABLE `kabupaten_scrape`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `kecamatan`
--
ALTER TABLE `kecamatan`
  MODIFY `id_kecamatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kecamatan_scrape`
--
ALTER TABLE `kecamatan_scrape`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=324;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `negara`
--
ALTER TABLE `negara`
  MODIFY `id_negara` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `provinsi`
--
ALTER TABLE `provinsi`
  MODIFY `id_provinsi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rekap_ptk_pd`
--
ALTER TABLE `rekap_ptk_pd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `rekap_rombel`
--
ALTER TABLE `rekap_rombel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=267;

--
-- AUTO_INCREMENT for table `rekap_sarpras`
--
ALTER TABLE `rekap_sarpras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `scraping_logs`
--
ALTER TABLE `scraping_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `scraping_urls`
--
ALTER TABLE `scraping_urls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `sekolah_kontak`
--
ALTER TABLE `sekolah_kontak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `sekolah_lainnya`
--
ALTER TABLE `sekolah_lainnya`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `sekolah_pelengkap`
--
ALTER TABLE `sekolah_pelengkap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `sekolah_scrape`
--
ALTER TABLE `sekolah_scrape`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1385;

--
-- AUTO_INCREMENT for table `url_induk_scrape`
--
ALTER TABLE `url_induk_scrape`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `import_log`
--
ALTER TABLE `import_log`
  ADD CONSTRAINT `fk_import_user` FOREIGN KEY (`user_id`) REFERENCES `login` (`id`);

--
-- Constraints for table `kabupaten_kota`
--
ALTER TABLE `kabupaten_kota`
  ADD CONSTRAINT `kabupaten_kota_ibfk_1` FOREIGN KEY (`id_provinsi_fk`) REFERENCES `provinsi` (`id_provinsi`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kabupaten_scrape`
--
ALTER TABLE `kabupaten_scrape`
  ADD CONSTRAINT `kabupaten_scrape_ibfk_1` FOREIGN KEY (`url_induk_id`) REFERENCES `url_induk_scrape` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kecamatan`
--
ALTER TABLE `kecamatan`
  ADD CONSTRAINT `kecamatan_ibfk_1` FOREIGN KEY (`id_kabupaten_fk`) REFERENCES `kabupaten_kota` (`id_kabupaten`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kecamatan_scrape`
--
ALTER TABLE `kecamatan_scrape`
  ADD CONSTRAINT `kecamatan_scrape_ibfk_1` FOREIGN KEY (`kabupaten_scrape_id`) REFERENCES `kabupaten_scrape` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provinsi`
--
ALTER TABLE `provinsi`
  ADD CONSTRAINT `provinsi_ibfk_1` FOREIGN KEY (`id_negara_fk`) REFERENCES `negara` (`id_negara`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `rekap_ptk_pd`
--
ALTER TABLE `rekap_ptk_pd`
  ADD CONSTRAINT `rekap_ptk_pd_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rekap_rombel`
--
ALTER TABLE `rekap_rombel`
  ADD CONSTRAINT `rekap_rombel_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rekap_sarpras`
--
ALTER TABLE `rekap_sarpras`
  ADD CONSTRAINT `rekap_sarpras_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `scraping_logs`
--
ALTER TABLE `scraping_logs`
  ADD CONSTRAINT `fk_logs_url` FOREIGN KEY (`scraping_url_id`) REFERENCES `scraping_urls` (`id`),
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `login` (`id`);

--
-- Constraints for table `scraping_urls`
--
ALTER TABLE `scraping_urls`
  ADD CONSTRAINT `fk_scraping_urls_to_kecamatan` FOREIGN KEY (`kecamatan_scrape_id`) REFERENCES `kecamatan_scrape` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_urls_user` FOREIGN KEY (`user_id`) REFERENCES `login` (`id`),
  ADD CONSTRAINT `scraping_urls_ibfk_1` FOREIGN KEY (`sekolah_scrape_id`) REFERENCES `sekolah_scrape` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sekolah_identitas`
--
ALTER TABLE `sekolah_identitas`
  ADD CONSTRAINT `sekolah_identitas_ibfk_1` FOREIGN KEY (`id_kecamatan_fk`) REFERENCES `kecamatan` (`id_kecamatan`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sekolah_kontak`
--
ALTER TABLE `sekolah_kontak`
  ADD CONSTRAINT `sekolah_kontak_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sekolah_lainnya`
--
ALTER TABLE `sekolah_lainnya`
  ADD CONSTRAINT `sekolah_lainnya_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sekolah_pelengkap`
--
ALTER TABLE `sekolah_pelengkap`
  ADD CONSTRAINT `sekolah_pelengkap_ibfk_1` FOREIGN KEY (`npsn_fk`) REFERENCES `sekolah_identitas` (`npsn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sekolah_scrape`
--
ALTER TABLE `sekolah_scrape`
  ADD CONSTRAINT `sekolah_scrape_ibfk_1` FOREIGN KEY (`kecamatan_scrape_id`) REFERENCES `kecamatan_scrape` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
