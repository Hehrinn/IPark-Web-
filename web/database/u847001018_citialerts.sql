-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 13, 2026 at 05:20 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u847001018_citialerts`
--

-- --------------------------------------------------------

--
-- Table structure for table `emergency_requests`
--

CREATE TABLE `emergency_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `emergency_type` varchar(50) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `location_name` text DEFAULT NULL,
  `status` enum('pending','help_coming','completed','cancelled') DEFAULT 'pending',
  `responder_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `emergency_requests`
--

INSERT INTO `emergency_requests` (`id`, `user_id`, `emergency_type`, `latitude`, `longitude`, `location_name`, `status`, `responder_id`, `created_at`, `updated_at`) VALUES
(1, 11, 'Criminal Activity', 37.4219983, -122.084, 'Google Building 40, 1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA', 'completed', 10, '2025-09-28 04:16:40', '2025-09-28 10:26:04'),
(2, 11, 'Medical Emergency (Stroke, etc.)', 37.4219983, -122.084, 'Google Building 40, 1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA', 'help_coming', 10, '2025-09-28 04:33:30', '2025-09-28 05:32:28'),
(3, 11, 'Medical Emergency (Stroke, etc.)', 37.4219983, -122.084, 'Google Building 40, 1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA', 'cancelled', NULL, '2025-09-28 04:37:22', '2025-09-28 04:43:19'),
(4, 11, 'Flood', 37.4219983, -122.084, 'Google Building 40, 1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA', 'cancelled', NULL, '2025-09-28 04:43:35', '2025-09-28 04:43:54'),
(5, 11, 'Medical Emergency (Stroke, etc.)', 37.4219983, -122.084, 'Google Building 40, 1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA', 'cancelled', NULL, '2025-09-28 04:50:52', '2025-09-28 04:55:07'),
(6, 11, 'Medical Emergency (Stroke, etc.)', 10.7425357, 122.9661408, '39 Arsenio Diaz St, Talisay, Negros Occidental, Philippines', 'cancelled', 10, '2025-09-28 05:26:51', '2025-09-28 05:46:06'),
(7, 11, 'Medical Emergency (Stroke, etc.)', 10.7425232, 122.9661413, '39 Arsenio Diaz St, Talisay, Negros Occidental, Philippines', 'cancelled', 10, '2025-09-28 05:46:10', '2025-09-28 05:55:58'),
(8, 11, 'Flood', 10.7425291, 122.9661395, '39 Arsenio Diaz St, Talisay, Negros Occidental, Philippines', 'cancelled', 10, '2025-09-28 05:56:03', '2025-09-28 07:03:06'),
(9, 11, 'Medical Emergency (Stroke, etc.)', 37.4220486, -122.0840099, 'Google Building 40, 1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA', 'completed', 10, '2025-09-28 06:10:56', '2025-09-28 10:25:58'),
(10, 11, 'Medical Emergency (Stroke, etc.)', 10.7424422, 122.9661443, 'PXR8+XHV, Talisay, Negros Occidental, Philippines', 'cancelled', NULL, '2025-09-28 07:03:08', '2025-09-28 07:25:03'),
(11, 11, 'Other', 10.7425316, 122.9661416, '39 Arsenio Diaz St, Talisay, Negros Occidental, Philippines', 'help_coming', 10, '2025-09-28 07:25:06', '2025-09-28 11:45:03'),
(12, 14, 'Medical Emergency (Stroke, etc.)', 10.6782159, 122.9622098, 'MXH6+6VP, C.L. Montelibano Ave, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-09-30 02:50:25', '2025-09-30 02:50:29'),
(13, 16, 'Medical Emergency (Stroke, etc.)', 10.6777618, 122.9620299, 'MXH6+3R8, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-09-30 04:47:53', '2025-09-30 04:48:53'),
(14, 16, 'Medical Emergency (Stroke, etc.)', 10.6777758, 122.9619854, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-09-30 04:52:39', '2025-09-30 04:52:47'),
(15, 16, 'Fire', 10.6797651, 122.9606063, 'MXH6+W68, La Salle Ave, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-09-30 06:48:09', '2025-09-30 06:48:16'),
(16, 18, 'Flood', 10.6954897, 122.9757332, 'Blk5 Lot 11 Pinecrest 2, Blk 5 Lot 11 Pinecrest 2, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-02 07:47:35', '2025-10-02 07:48:31'),
(17, 18, 'Medical Emergency (Stroke, etc.)', 10.6955504, 122.9756764, 'Blk5 Lot 11 Pinecrest 2, Blk 5 Lot 11 Pinecrest 2, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', 17, '2025-10-02 07:50:42', '2025-10-02 08:19:53'),
(18, 18, 'Medical Emergency (Stroke, etc.)', 10.6801383, 122.9588083, 'MXJ5+29H, Bacolod, Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-04 14:09:27', '2025-10-04 14:09:30'),
(19, 18, 'Flood', 10.6752407, 122.9572965, 'St. John\'s Institute, 6100 Tops Rd, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-04 15:11:08', '2025-10-04 15:11:45'),
(20, 18, 'Fire', 10.6797658, 122.9606957, 'MXH6+W7G, La Salle Ave, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-06 07:33:15', '2025-10-06 07:33:20'),
(21, 18, 'Other', 10.6797651, 122.960694, 'MXH6+W7G, La Salle Ave, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-06 07:33:45', '2025-10-06 07:33:48'),
(22, 18, 'Fire', 10.679228, 122.9634572, 'MXH7+Q9J, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-06 09:28:56', '2025-10-06 09:29:17'),
(23, 19, 'Criminal Activity', 10.6789504, 122.9602672, 'Leonardo Gallardo St Pacita arcade building, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-06 10:42:05', '2025-10-06 10:42:09'),
(24, 18, 'Criminal Activity', 10.6790294, 122.9639406, 'MXH7+JMM, C.L. Montelibano Ave, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-07 03:35:57', '2025-10-07 03:36:09'),
(25, 18, 'Fire', 10.7469278, 122.9715089, 'PXWC+QJ5 San Lorenzo Ruiz Village, Talisay - Concepcion Rd, Talisay, Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-12 08:46:22', '2025-10-12 08:46:26'),
(26, 18, 'Medical Emergency (Stroke, etc.)', 10.747005, 122.9715017, 'PXWC+QJ5 San Lorenzo Ruiz Village, Talisay - Concepcion Rd, Talisay, Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-12 08:49:09', '2025-10-12 08:49:11'),
(27, 18, 'Criminal Activity', 10.7383667, 122.97091, 'PXQC+89Q, Talisay, Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-12 11:29:31', '2025-10-12 11:30:15'),
(28, 21, 'Medical Emergency (Stroke, etc.)', 10.6797731, 122.9604784, '9 La Salle Ave, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-19 10:45:49', '2025-10-19 10:45:55'),
(29, 18, 'Criminal Activity', 10.6787543, 122.959244, 'MXH5+FPC, BS Aquino Dr, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-20 09:22:47', '2025-10-20 09:23:09'),
(30, 18, 'Fire', 10.6777767, 122.9619449, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', 17, '2025-10-21 04:41:56', '2025-10-21 05:09:50'),
(31, 18, 'Flood', 10.6777736, 122.9619534, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'completed', 17, '2025-10-21 05:10:52', '2025-10-21 05:11:20'),
(32, 22, 'Fire', 10.6778215, 122.96203, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'completed', 17, '2025-10-21 06:01:51', '2025-10-21 06:02:19'),
(33, 18, 'Flood', 10.677772, 122.961954, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-23 04:47:45', '2025-10-23 04:47:53'),
(34, 18, 'Flood', 10.6777645, 122.9619898, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', 17, '2025-10-23 04:47:56', '2025-10-23 04:49:03'),
(35, 18, 'Medical Emergency (Stroke, etc.)', 10.6777534, 122.9619425, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', 17, '2025-10-23 04:49:07', '2025-10-23 04:49:38'),
(36, 24, 'Flood', 10.6777654, 122.9619586, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'completed', 17, '2025-10-23 04:53:15', '2025-10-23 04:53:28'),
(37, 26, 'Criminal Activity', 10.6777656, 122.9619685, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'completed', 17, '2025-10-23 04:59:35', '2025-10-23 05:00:07'),
(38, 26, 'Flood', 10.677765, 122.9619858, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'completed', 17, '2025-10-23 05:01:14', '2025-10-23 05:01:27'),
(39, 26, 'Flood', 10.6777661, 122.9619787, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', 17, '2025-10-23 05:21:30', '2025-10-23 05:21:57'),
(40, 26, 'Criminal Activity', 10.6777713, 122.9619197, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-23 05:22:05', '2025-10-30 04:49:04'),
(41, 18, 'Medical Emergency (Stroke, etc.)', 10.6777131, 122.9622753, 'Wester Hall, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-28 03:50:48', '2025-10-28 03:50:50'),
(42, 18, 'Medical Emergency (Stroke, etc.)', 10.6777066, 122.9622909, 'Wester Hall, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-30 03:44:37', '2025-10-30 03:44:42'),
(43, 18, 'Criminal Activity', 10.6777299, 122.9620798, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-10-30 04:45:30', '2025-10-30 04:45:37'),
(44, 26, 'Medical Emergency (Stroke, etc.)', 10.6777555, 122.9619687, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', 17, '2025-10-30 04:49:37', '2025-10-30 04:51:09'),
(45, 26, 'Fire', 10.6777585, 122.9619634, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'completed', 17, '2025-10-30 04:51:35', '2025-10-30 04:52:18'),
(46, 26, 'Criminal Activity', 10.6777683, 122.9619768, 'Science and Engineering, Mc Arthur Dr, Bacolod, 6100 Negros Occidental, Philippines', 'completed', 17, '2025-10-30 05:03:08', '2025-10-30 05:03:55'),
(47, 18, 'Flood', 0, 0, NULL, 'cancelled', NULL, '2025-11-09 14:19:45', '2025-11-09 14:19:51'),
(48, 26, 'Criminal Activity', 10.677972, 122.9622964, 'MXH6+6VP, C.L. Montelibano Ave, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-11-17 09:01:40', '2025-11-17 09:06:27'),
(49, 26, 'Criminal Activity', 10.6779637, 122.962294, 'MXH6+6VP, C.L. Montelibano Ave, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', 27, '2025-11-17 09:08:46', '2025-11-17 09:12:59'),
(50, 26, 'Flood', 10.6779725, 122.9622969, 'MXH6+6VP, C.L. Montelibano Ave, Bacolod, 6100 Negros Occidental, Philippines', 'completed', 27, '2025-11-17 09:13:09', '2025-11-17 09:16:55'),
(51, 14, 'Medical Emergency (Stroke, etc.)', 10.6686117, 122.9804896, 'MX9J+96M, Bacolod, 6100 Negros Occidental, Philippines', 'cancelled', NULL, '2025-11-27 16:14:37', '2025-11-27 16:14:55');

-- --------------------------------------------------------

--
-- Table structure for table `evacuation_centers`
--

CREATE TABLE `evacuation_centers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evacuation_centers`
--

INSERT INTO `evacuation_centers` (`id`, `name`, `address`, `latitude`, `longitude`, `description`, `capacity`, `contact_number`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Manila Evac center villamonet', 'Rizal Avenue, Santa Cruz, Third District, Manila, Capital District, Metro Manila, 1003, Philippines', 14.604515, 120.98196, 'asdasdasd', 123123, '123123123', 'active', '2025-10-12 09:07:23', '2025-11-17 08:44:56'),
(2, 'Barangay 15 Evacuation Center', 'BDO, Rosario Street, Tanay, Barangay 32, Bacolod-2, Bacolod, Negros Island Region, 6100, Philippines', 10.663476, 122.94817, 'adsdasdasdasd', 100, '0', 'active', '2025-10-12 09:31:33', '2025-10-12 09:31:33'),
(3, 'Cadiz', 'Luna, Cadiz, Negros Occidental, Negros Island Region, Philippines', 10.95063, 123.21991, 'asdasdas1', 123123, '0', 'active', '2025-10-12 09:33:07', '2025-10-12 09:33:07'),
(4, 'Villamonte Covered Court ', '7-Eleven, B. S. Aquino Drive, Purok 15, Villamonte, Bacolod-1, Bacolod, Negros Island Region, 6100, Philippines', 10.668642, 122.964242, 'Evacuation center', 100, '917578178941', 'active', '2025-11-02 11:09:59', '2025-11-02 11:09:59'),
(5, 'Old airport evacuation center', 'Singcang-Airport, Bacolod-2, Bacolod, Negros Island Region, Philippines', 10.650127, 122.929276, 'Evacuation center', 100, '9996782051', 'active', '2025-11-17 09:23:07', '2025-11-17 09:23:07');

-- --------------------------------------------------------

--
-- Table structure for table `ipark_admins`
--

CREATE TABLE `ipark_admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `role` enum('super_admin','admin','operator') DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ipark_admins`
--

INSERT INTO `ipark_admins` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `created_at`, `updated_at`, `is_active`) VALUES
(4, 'admin_test', 'test@gmail.com', '$2y$12$K76O/M.E3q.pW.M7p/nFeS4A0eUnF6.G7Xv4W1G8.r5r5r5r5r5r', 'Test Admin', 'admin', '2026-03-13 05:08:05', '2026-03-13 05:08:05', 1),
(5, 'admin', 'admin@gmail.com', '$2y$12$K76O/M.E3q.pW.M7p/nFeS4A0eUnF6.G7Xv4W1G8.r5r5r5r5r5r', 'System Admin', 'super_admin', '2026-03-13 05:11:12', '2026-03-13 05:11:12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ipark_audit_logs`
--

CREATE TABLE `ipark_audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_value`)),
  `new_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_value`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ipark_audit_logs`
--

INSERT INTO `ipark_audit_logs` (`id`, `user_id`, `admin_id`, `action`, `table_name`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, NULL, NULL, 'user_registration', 'ipark_users', 1, NULL, NULL, '120.28.237.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-11 15:29:40'),
(2, NULL, NULL, 'user_registration', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-12 17:29:10'),
(3, NULL, NULL, 'failed_login_attempt', 'ipark_users', 4, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-12 17:42:54'),
(4, NULL, NULL, 'failed_login_attempt', 'ipark_users', 4, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-12 17:43:11'),
(5, NULL, NULL, 'failed_login_attempt', 'ipark_users', 4, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-12 17:43:39'),
(6, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 02:24:28'),
(7, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 02:53:15'),
(8, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 02:55:48'),
(9, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 02:57:13'),
(10, 2, NULL, 'reservation_created', 'ipark_reservations', 1, NULL, '{\"user_id\":2,\"slot_id\":1,\"total_amount\":169.83333333333334}', '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 02:57:59'),
(11, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:07:35'),
(12, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:07:47'),
(13, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:07:56'),
(14, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:21:34'),
(15, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:24:51'),
(16, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:24:54'),
(17, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:25:02'),
(18, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:29:53'),
(19, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:29:59'),
(20, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:30:09'),
(21, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:30:19'),
(22, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:34:44'),
(23, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:34:52'),
(24, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:35:30'),
(25, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:35:33'),
(26, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:39:34'),
(27, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:43:39'),
(28, 2, NULL, 'reservation_created', 'ipark_reservations', 2, NULL, '{\"user_id\":2,\"slot_id\":1,\"total_amount\":49.75}', '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:44:23'),
(29, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:44:56'),
(30, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:48:11'),
(31, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:55:00'),
(32, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:55:21'),
(33, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:56:18'),
(34, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:58:55'),
(35, 2, NULL, 'reservation_created', 'ipark_reservations', 5, NULL, '{\"user_id\":2,\"slot_id\":1,\"total_amount\":10}', '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 03:59:18'),
(36, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:00:39'),
(37, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:00:41'),
(38, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:00:47'),
(39, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:00:48'),
(40, 2, NULL, 'reservation_created', 'ipark_reservations', 6, NULL, '{\"user_id\":2,\"slot_id\":1,\"total_amount\":10.166666666666666}', '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:05:00'),
(41, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:05:48'),
(42, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:08:15'),
(43, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:19:54'),
(44, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:20:24'),
(45, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:20:40'),
(46, NULL, NULL, 'user_registration', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:21:12'),
(47, 5, NULL, 'user_login', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:21:24'),
(48, 5, NULL, 'reservation_created', 'ipark_reservations', 7, NULL, '{\"user_id\":5,\"slot_id\":1,\"total_amount\":60}', '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:21:39'),
(49, 5, NULL, 'user_logout', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:21:43'),
(50, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:21:46'),
(51, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:25:08'),
(52, 5, NULL, 'user_login', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:25:10'),
(53, 5, NULL, 'reservation_created', 'ipark_reservations', 8, NULL, '{\"user_id\":5,\"slot_id\":1,\"total_amount\":60}', '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:32:32'),
(54, 5, NULL, 'user_logout', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:32:39'),
(55, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:32:42'),
(56, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:32:47'),
(57, 5, NULL, 'user_login', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:32:50'),
(58, 5, NULL, 'reservation_created', 'ipark_reservations', 9, NULL, '{\"user_id\":5,\"slot_id\":1,\"total_amount\":60}', '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:39:41'),
(59, 5, NULL, 'reservation_created', 'ipark_reservations', 10, NULL, '{\"user_id\":5,\"slot_id\":1,\"total_amount\":60}', '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:40:44'),
(60, 5, NULL, 'user_logout', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:42:19'),
(61, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:42:27'),
(62, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:52:33'),
(63, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:52:41'),
(64, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:55:43'),
(65, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:55:49'),
(66, 5, NULL, 'user_login', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:55:51'),
(67, 5, NULL, 'user_logout', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:55:55'),
(68, 5, NULL, 'user_login', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:56:05'),
(69, 5, NULL, 'user_logout', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:56:08'),
(70, 5, NULL, 'user_login', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:58:35'),
(71, 5, NULL, 'user_logout', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:58:39'),
(72, 2, NULL, 'user_login', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:58:42'),
(73, 2, NULL, 'user_logout', 'ipark_users', 2, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:59:11'),
(74, 5, NULL, 'user_login', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 04:59:13'),
(75, 5, NULL, 'reservation_created', 'ipark_reservations', 11, NULL, '{\"user_id\":5,\"slot_id\":3,\"total_amount\":60.5}', '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 05:03:25'),
(76, 5, NULL, 'user_logout', 'ipark_users', 5, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 05:04:42'),
(77, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 05:04:53'),
(78, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 05:08:51'),
(79, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 05:09:35'),
(80, NULL, NULL, 'failed_login_attempt', 'ipark_users', 0, NULL, NULL, '216.247.18.236', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', '2026-03-13 05:11:50');

-- --------------------------------------------------------

--
-- Table structure for table `ipark_messages`
--

CREATE TABLE `ipark_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `sender_type` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `attachment_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipark_parking_slots`
--

CREATE TABLE `ipark_parking_slots` (
  `id` int(11) NOT NULL,
  `slot_number` varchar(50) NOT NULL,
  `floor_level` int(11) NOT NULL,
  `parking_lot` varchar(100) NOT NULL,
  `vehicle_type` enum('car','motorcycle','ev_charging') DEFAULT 'car',
  `status` enum('available','occupied','reserved','maintenance') DEFAULT 'available',
  `hourly_rate` decimal(8,2) NOT NULL DEFAULT 5.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ipark_parking_slots`
--

INSERT INTO `ipark_parking_slots` (`id`, `slot_number`, `floor_level`, `parking_lot`, `vehicle_type`, `status`, `hourly_rate`, `created_at`, `updated_at`) VALUES
(1, 'Slot 1', 1, 'USLS', 'car', 'available', 30.00, '2026-03-11 14:12:10', '2026-03-13 05:03:37'),
(2, 'Slot 2', 1, 'USLS', 'car', 'available', 30.00, '2026-03-11 14:12:10', '2026-03-13 04:19:18'),
(3, 'Slot 3', 1, 'USLS', 'car', 'available', 30.00, '2026-03-11 14:12:10', '2026-03-13 05:04:00'),
(4, 'Slot 4', 1, 'USLS', 'car', 'available', 30.00, '2026-03-11 14:12:10', '2026-03-13 04:13:18'),
(5, 'Slot 5', 1, 'USLS', 'car', 'available', 30.00, '2026-03-11 14:12:10', '2026-03-13 04:19:18'),
(6, 'Slot 6', 1, 'USLS', 'car', 'available', 30.00, '2026-03-11 14:12:10', '2026-03-13 04:13:18'),
(7, 'Slot 7', 1, 'USLS', 'car', 'available', 30.00, '2026-03-11 14:12:10', '2026-03-13 04:13:18'),
(8, 'Slot 8', 1, 'USLS', 'car', 'available', 30.00, '2026-03-11 14:12:10', '2026-03-13 04:19:18');

-- --------------------------------------------------------

--
-- Table structure for table `ipark_reservations`
--

CREATE TABLE `ipark_reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parking_slot_id` int(11) NOT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `check_in_time` timestamp NULL DEFAULT NULL,
  `check_out_time` timestamp NULL DEFAULT NULL,
  `duration_hours` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `reservation_status` enum('pending_approval','approved','rejected','active','completed','cancelled') DEFAULT 'pending_approval',
  `admin_approved_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipark_staff_approvals`
--

CREATE TABLE `ipark_staff_approvals` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipark_users`
--

CREATE TABLE `ipark_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `verification_token` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ipark_users`
--

INSERT INTO `ipark_users` (`id`, `username`, `email`, `password_hash`, `full_name`, `phone`, `created_at`, `updated_at`, `is_active`, `verification_token`, `email_verified_at`) VALUES
(1, 'jsmilano31_5d76a123', 'jsmilano31@gmail.com', '$2y$12$0eicuwjWUdGOzPkYtVZRC.cv0/TTAUottKEqh/TeUuK.3AXOurp4K', 'John Milano', '9174987194', '2026-03-11 15:29:40', '2026-03-13 04:58:29', 1, 'd8f09cf93d2344e3b48d68e457566f64265f815a8d2b760998783e160be7417c', NULL),
(2, 'try_ca1e4be0', 'try@gmail.com', '$2y$12$zCxUXTMIDZ/s/i3G3UtVguMAlq3BQKtylWpCkMzEJBKUhRbHKc4Lq', 'Nap', '09153824671', '2026-03-12 17:29:10', '2026-03-13 04:59:05', 1, 'd8f7789f59134327d6b8eb69523ba84dcc802417ea9f5f2f691b478d520b3cf3', NULL),
(4, 'user_test', 'user@test.com', '$2y$12$8K765N6.n3pM7is9p.mNFeS4A0eUnF6.G7Xv4W1G8.r5r5r5r5r5r', 'Test User', NULL, '2026-03-12 17:42:22', '2026-03-12 17:42:22', 1, NULL, '2026-03-12 17:42:22'),
(5, 'trial_2a33c08c', 'trial@gmail.com', '$2y$12$D37wi6cfAb5KZFxJ8dKDxeYutTL7zGqOABErn/JoP0QK1sSA9R9qe', 'Spen', '09153824671', '2026-03-13 04:21:12', '2026-03-13 04:21:12', 1, 'f08840c436e82d50c7fdd1f9ad0b35f9676a567f4cea79262cd83f0f2e2ede8a', '2026-03-13 04:21:12');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `moderator_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('active','archived','deleted') DEFAULT 'active',
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `moderator_id`, `title`, `description`, `image_path`, `status`, `views`, `created_at`, `updated_at`) VALUES
(9, 17, 'Heavy Rain Causes Flooding at HiStrip, Bacolod City', 'A sudden downpour led to flooding along the area of Histrip in Bacolod City. The photo shows vehicles, including private cars and public utility vehicles, struggling to pass through the waterlogged road. The heavy rain caused poor drainage to overflow, resulting in traffic congestion and difficulty for motorists and pedestrians. This incident highlights the city’s recurring drainage and flood management issues during intense rainfall.', 'uploads/posts/68f9b859bc206_post.jpg', 'active', 0, '2025-10-23 05:08:41', '2025-10-23 05:08:41'),
(10, 17, 'Flooding Hits Mandalagan Area in Bacolod City After Heavy Rainfall', 'The photo shows severe flooding in the Mandalagan area of Bacolod City following continuous heavy rainfall. Streets became submerged, making it difficult for vehicles and motorcycles to pass. A motorist in a raincoat can be seen carefully driving through the high water, while cars and utility vehicles struggle to move. The flooding caused traffic delays and raised concerns over the city’s drainage system, which often gets overwhelmed during strong downpours.', 'uploads/posts/68f9b904a645e_post.jpg', 'active', 0, '2025-10-23 05:11:32', '2025-10-23 05:11:32'),
(11, 17, 'Emergency Responders help civilian\'s', 'This rescue operation took place in Bacolod City after heavy and continuous rainfall caused severe flooding in several barangays, particularly in low-lying and flood-prone areas such as Barangay 10, Barangay 40, and parts of Mandalagan and Tangub. The city’s drainage system was unable to handle the large volume of rainwater, resulting in ankle to waist-deep floods that stranded residents in their homes and along major roads.', 'uploads/posts/68f9b946798fc_post.jpg', 'active', 0, '2025-10-23 05:12:38', '2025-10-23 05:12:38'),
(12, 17, 'Bacolod Fire destroys 21 houses', 'Fire spread due to the gasoline being left unguarded. Then the fire went out of control and spread like a wildfire causing a lot of house getting burned.', 'uploads/posts/68f9b9a4e9d6c_post.jpg', 'active', 0, '2025-10-23 05:14:12', '2025-10-23 05:14:12'),
(13, 27, 'LaSalle na baha', 'Flooding in the USLS', 'uploads/posts/691ae0517f096_post.jpg', 'archived', 0, '2025-11-17 08:44:01', '2025-11-17 09:21:58');

-- --------------------------------------------------------

--
-- Table structure for table `responder_locations`
--

CREATE TABLE `responder_locations` (
  `id` int(11) NOT NULL,
  `responder_id` int(11) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shinzo_appointments`
--

CREATE TABLE `shinzo_appointments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `appointment_date` datetime NOT NULL,
  `service` varchar(150) DEFAULT NULL,
  `doctor` varchar(150) DEFAULT NULL,
  `status` enum('scheduled','confirmed','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shinzo_appointments`
--

INSERT INTO `shinzo_appointments` (`id`, `user_id`, `appointment_date`, `service`, `doctor`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-11-21 14:30:00', 'Whitening', NULL, '', 'Sakit unto ko', '2025-11-14 03:46:55', '2025-11-14 05:04:22'),
(2, 1, '2025-11-16 17:43:00', 'Braces', NULL, 'scheduled', 'kabila sa unto', '2025-11-14 06:41:26', '2025-11-14 06:41:26'),
(3, 2, '2025-11-28 09:00:00', 'Root Canal', NULL, '', 'Broken tooth', '2025-11-14 13:39:02', '2025-11-14 13:46:21'),
(4, 2, '2025-11-25 12:51:00', 'Other', NULL, 'scheduled', '12:51, And I thought my feelings were gone, But I&#039;m lying on my bed, Thinking of you again', '2025-11-14 13:52:43', '2025-11-14 13:52:43'),
(5, 2, '2025-11-14 21:57:00', 'Checkup', NULL, 'scheduled', '', '2025-11-14 13:58:41', '2025-11-14 13:58:41'),
(6, 2, '2025-11-14 21:57:00', 'Checkup', NULL, 'scheduled', '', '2025-11-14 13:58:41', '2025-11-14 13:58:41');

-- --------------------------------------------------------

--
-- Table structure for table `shinzo_login_attempts`
--

CREATE TABLE `shinzo_login_attempts` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shinzo_login_attempts`
--

INSERT INTO `shinzo_login_attempts` (`id`, `username`, `ip_address`, `attempt_time`, `success`) VALUES
(1, 'spencer', '124.83.13.156', '2025-11-14 03:33:24', 0),
(2, 'mark', '124.83.13.156', '2025-11-14 03:33:37', 1),
(3, 'mark', '124.83.13.156', '2025-11-14 03:36:28', 1),
(4, 'mark', '124.83.13.156', '2025-11-14 03:53:29', 1),
(5, 'mark', '143.44.168.201', '2025-11-14 04:18:08', 1),
(6, 'mark', '124.83.13.156', '2025-11-14 05:59:12', 1),
(7, 'mark', '124.83.13.156', '2025-11-14 06:25:52', 1),
(8, 'mark', '124.83.13.156', '2025-11-14 06:40:47', 1),
(9, 'KimOcampo', '143.44.169.138', '2025-11-14 12:47:02', 1),
(10, 'KimOcampo', '143.44.169.138', '2025-11-14 13:34:13', 1),
(11, 'KimOcampo', '143.44.169.138', '2025-11-14 13:45:23', 1),
(12, 'KimOcampo', '143.44.169.138', '2025-11-14 13:46:16', 1),
(13, 'mark', '124.83.13.156', '2025-11-17 08:12:28', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shinzo_password_resets`
--

CREATE TABLE `shinzo_password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shinzo_transactions`
--

CREATE TABLE `shinzo_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `appointment_id` int(10) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `method` varchar(50) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shinzo_users`
--

CREATE TABLE `shinzo_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shinzo_users`
--

INSERT INTO `shinzo_users` (`id`, `username`, `password`, `firstname`, `lastname`, `email`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'mark', '$2y$10$S4eHTQQd8lO21Akun0JsU.VMpTmmIUOUtEm1CqdnGGsTjuilUjcu.', 'mark', 'motejo', 'markmotejo@gmail.com', NULL, 1, '2025-11-14 03:32:44', '2025-11-14 03:32:44'),
(2, 'KimOcampo', '$2y$10$7GuqsDsTctS1PL2444Z4We3lu6kmGs1OoEb1h4eVoCF/ERmOJj9EO', 'Kim', 'Ocampo', 'TheOriginalOne@gmail.com', NULL, 1, '2025-11-14 12:46:51', '2025-11-14 12:46:51');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$qrF5NP7xU610mQexfIU0futnJ4do5WgpBD9J6otjGVBMOM3cdi/nq', 'admin@citialerts.ph', '2025-09-27 17:18:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('user','moderator') NOT NULL DEFAULT 'user',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `verification_documents` text DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `user_type`, `first_name`, `last_name`, `phone`, `organization`, `profile_image`, `verification_documents`, `is_verified`, `created_at`, `updated_at`) VALUES
(2, 'janesmith', 'jane.smith@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Jane', 'Smith', '09234567890', NULL, NULL, NULL, 1, '2024-01-16 09:15:00', '2024-01-16 09:15:00'),
(3, 'moderator1', 'mod1@citialerts.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'moderator', 'Maria', 'Santos', '09345678901', 'Default Department', NULL, '[\"doc1.pdf\",\"id1.jpg\"]', 1, '2024-01-17 10:00:00', '2025-09-28 10:46:17'),
(4, 'pendingmod', 'pending@citialerts.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'moderator', 'Carlos', 'Reyes', '09456789012', 'Default Department', NULL, '[\"cert1.pdf\",\"license1.jpg\"]', 0, '2024-01-18 11:45:00', '2025-09-28 10:46:17'),
(5, 'user1', 'user1@gmail.com', '$2y$10$zOxtoOMkDnVAjALc51zAGOa5q8NV3odLzTsdt4AEYpw0iGyb3qH0u', 'user', 'user', 'one', '', NULL, NULL, NULL, 1, '2025-09-27 15:42:23', '2025-09-27 15:42:23'),
(6, 'moderator2', 'moderator2@gmail.com', '$2y$10$Y50L9cua3sHk5bBPAbiEfu/Mn.lA8YDbmVJc/yaMv.EmZ4EnnQ44C', 'moderator', 'mod', 'erator', '', 'Default Department', NULL, NULL, 0, '2025-09-27 17:07:57', '2025-09-28 10:46:17'),
(7, 'moderator3', 'moderator3@gmail.com', '$2y$10$M4jxm1qXYZqM0Xb.xhvOteKggllLJI/UE4umL.gu1.RC3wrd9kmUS', 'moderator', 'moderator', 'three', '09394712731', 'Default Department', NULL, NULL, 0, '2025-09-27 17:38:21', '2025-09-28 10:46:17'),
(8, 'mod3', 'mod3@gmail.com', '$2y$10$OOF/x7AZ8DLLsUYEqIvIHOJb2jzYbXPpyzz/vCMCH1yFzSUvF/pCO', 'moderator', 'mod', 'three', '12039123', 'Default Department', NULL, '68d822ede690d_verification.jpg', 0, '2025-09-27 17:46:21', '2025-09-28 10:46:17'),
(9, 'modtest1', 'modtest1@gmail.com', '$2y$10$4DJVuJS/4V6tH5jwUQ9iKeYVAj10P1Yq6ei6HmcJMp6nA7qler/S.', 'moderator', 'mod', 'testone', '2398239', 'Default Department', NULL, '68d8239dc5255_verification.jpg', 1, '2025-09-27 17:49:17', '2025-09-28 10:46:17'),
(10, 'takod', 'takod@gmail.coms', '$2y$10$lzZMqvwpvvVAt8DZJvMPsucpGUwIWd54YMMTtcXF7tbHilDNGFgya', 'moderator', 'ta', 'kod', '09123129', 'Default Department', 'uploads/profile/avatar_68d92befc59667.67347037.jpg', '68d825070e9f2_verification.jpg', 0, '2025-09-27 17:55:19', '2025-11-18 09:18:55'),
(11, 'user2', 'user2@gmail.com', '$2y$10$gUG71lCLZFXriEezcQ6VauQbSqr4jeRPT.0gIyaz9hho6LYHDhAnS', 'user', 'user', 'last', '19023120', NULL, NULL, NULL, 0, '2025-09-27 18:25:26', '2025-09-27 18:25:26'),
(12, 'juandeladcruz', 'juandelacruz@gmail.com', '$2y$10$pOSQ9iTI7nHMqH524qC.lueQzS3KCrHNnv7XjhpA9RfK2fzbBt4re', 'user', 'Juan', 'de la Cruz', '09394714444', NULL, NULL, NULL, 0, '2025-09-28 10:31:24', '2025-09-28 10:31:24'),
(13, 'mods123', 'mods@gmail.com', '$2y$10$mVYAoQqPMM4P5V2qNdQ4tu5yyWhqSLzYd4zR3Tb/Xs03dBmp1UEQC', 'moderator', 'moder', 'ator', '7489496', 'DPWH', NULL, NULL, 0, '2025-09-28 11:10:53', '2025-09-28 11:10:53'),
(14, 'spencer', 'jsmilano31@gmail.com', '$2y$10$yOhjozO6oUQIunQ55Dyhn.kauVA53Pguo7WwLZX.yJrzIF2IzK1kO', 'user', 'John Spencer', 'Milano', '09694015399', '', NULL, NULL, 0, '2025-09-30 02:42:02', '2025-09-30 02:42:02'),
(15, 'kenjieagi', 'kenjieagi@gmail.com', '$2y$10$9VGml5idGPCH3r5YTgfVzOU3G0kXzre0wq7GibUbb9z8xpnshznuS', 'user', 'kenjie', 'agi', '09154956608', '', NULL, NULL, 0, '2025-09-30 04:31:54', '2025-09-30 04:31:54'),
(16, 'john', 'john@gmail.com', '$2y$10$/xnpgOWeVnevinjfNdmkbu25sH0EADItLSmDnSYTW4QBTktuK4TS2', 'user', 'john', 'milano', '09463561996', '', NULL, NULL, 0, '2025-09-30 04:46:30', '2025-09-30 04:46:30'),
(17, 'benjo', 'benjo@gmail.com', '$2y$10$tk/R3zRNwoPt4CV3bSHQd.gT14t/PgpEsNsli4cEM7xxNdbrvJGKS', 'moderator', 'Joben', 'Piornato', '0995242556', 'Amethyst', 'uploads/profile/avatar_68de2dc8373c75.31315801.jpg', NULL, 1, '2025-10-02 07:43:13', '2025-10-07 03:44:09'),
(18, 'sevi', 'sevi@gmail.com', '$2y$10$CJ47lsGxeqDH5LOjr9p6Ge/BbRl10iS6VaCRkKxqRg5I3e4qiCaZS', 'user', 'spencer', 'milano', '09168943785', '', 'uploads/profile/avatar_68f70eda14cd65.77331099.jpg', NULL, 0, '2025-10-02 07:44:46', '2025-10-21 04:41:00'),
(19, 'azehct', 'azehct@gmail.com', '$2y$10$krq4C0f1pfBRZL73Y1yeEu1CU5EWYbOtCHX0XHuBLXRzXdq223sqe', 'user', 'azehct', 'acob', '09562188464', '', NULL, NULL, 0, '2025-10-06 10:41:00', '2025-10-06 10:41:00'),
(21, 'oke', 'oke@gmail.con', '$2y$10$H5KiFkEo7U2fSE3hl3tq7eDEhX2PSKMjX/v3pVC.PSl5hUt7ePdV.', 'user', 'oke', 'wow', '09583218632', '', NULL, NULL, 0, '2025-10-14 02:15:38', '2025-10-14 02:15:38'),
(22, 'Ant', 's2300417@usls.edu.ph', '$2y$10$kSfT7HPfZanSx8.9yb1.me9o64DFfeT28i7gabV.1NN4yJZuq.js6', 'user', 'Anthony', 'Moleño', '09998330879', '', NULL, NULL, 0, '2025-10-21 06:01:06', '2025-10-21 06:01:06'),
(23, 'nicos', 'nicosarsaga@gmail.com', '$2y$10$ACfD1IdHRgBa.USooFo.0.PvBgGe5GaQ3J0.XTBevQIIJ9FZZGERW', 'user', 'Keith Nicolas', 'Arsaga', '9947948644', '', NULL, NULL, 0, '2025-10-23 04:51:31', '2025-10-23 04:51:31'),
(24, 'nicosss', 's2302113@usls.edu.ph', '$2y$10$nJpnCXNp7miqoqGlU9pED.GxQOQlFDxCvohFSXh4Rl14VHm6/0xea', 'user', 'Keith', 'Arsaga', '9947948644', '', NULL, NULL, 0, '2025-10-23 04:52:42', '2025-10-23 04:52:42'),
(25, 'keith', 'keitharsaga@gmail.com', '$2y$10$SsZR5Zdvq0AsuMFCi5yi9ugcSBgfnkiBJHXfufO/7A8sFRn6jSuSO', 'user', 'nicos', 'arsaga', '639318262673', '', NULL, NULL, 0, '2025-10-23 04:56:47', '2025-10-23 04:56:47'),
(26, 'haha', 'nicos@gmail.com', '$2y$10$hVldQ2nj6TOs2DDXxGi.PuZ0t.8qvFQy5O37/k2YaigGo0gTef87G', 'user', 'koys', 'koys', '09318262643', '', NULL, NULL, 0, '2025-10-23 04:58:07', '2025-11-17 08:43:28'),
(27, 'Spencer/K5 News Bacolod', 'spencermilano1031@gmail.com', '$2y$10$zq0QQoBKwcZ729AtHrsrEutpPbopXohVxrF3mL4StVbRHEv.5I/xu', 'moderator', 'spencer', 'Milano', '0946894648', 'K5 News Bacolod', 'uploads/profile/avatar_691ae2bf2ad688.45093214.jpg', NULL, 1, '2025-11-17 08:42:23', '2025-11-17 08:54:23');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `users_updated_at` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_feedback`
--

CREATE TABLE `user_feedback` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `overall_rating` tinyint(1) NOT NULL CHECK (`overall_rating` >= 1 and `overall_rating` <= 5),
  `emergency_alerts_rating` tinyint(1) DEFAULT NULL CHECK (`emergency_alerts_rating` >= 0 and `emergency_alerts_rating` <= 5),
  `evacuation_centers_rating` tinyint(1) DEFAULT NULL CHECK (`evacuation_centers_rating` >= 0 and `evacuation_centers_rating` <= 5),
  `news_updates_rating` tinyint(1) DEFAULT NULL CHECK (`news_updates_rating` >= 0 and `news_updates_rating` <= 5),
  `app_performance_rating` tinyint(1) DEFAULT NULL CHECK (`app_performance_rating` >= 0 and `app_performance_rating` <= 5),
  `feedback_category` enum('Bug Report','Feature Request','General Feedback','Complaint','Compliment') NOT NULL,
  `would_recommend` enum('Yes','No','Maybe') NOT NULL,
  `comments` text DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `app_version` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_feedback`
--

INSERT INTO `user_feedback` (`id`, `user_name`, `user_email`, `overall_rating`, `emergency_alerts_rating`, `evacuation_centers_rating`, `news_updates_rating`, `app_performance_rating`, `feedback_category`, `would_recommend`, `comments`, `device_info`, `app_version`, `created_at`, `updated_at`) VALUES
(1, 'bcbcf', 'gkvv@gggg.jjk', 5, NULL, NULL, NULL, NULL, 'Bug Report', 'No', 'ghh', 'okhttp/4.12.0', NULL, '2025-10-12 09:49:08', '2025-10-12 09:49:08'),
(2, 'si kenjie nong', 'kenjieoplok@gmail.com', 5, NULL, NULL, NULL, NULL, 'Complaint', 'Maybe', 'palangga ko miga ko pero i dont know lang.', 'okhttp/4.12.0', NULL, '2025-10-14 00:39:04', '2025-10-14 00:39:04'),
(3, 'keith', 'keti@hotmail.com', 5, NULL, NULL, NULL, NULL, 'Compliment', 'Yes', 'na', 'okhttp/4.12.0', NULL, '2025-11-17 09:02:58', '2025-11-17 09:02:58'),
(4, 'Keith', 'nicosarsaga@gmail.com', 5, NULL, NULL, NULL, NULL, 'Compliment', 'Yes', '', 'okhttp/4.12.0', NULL, '2025-11-17 09:09:54', '2025-11-17 09:09:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `responder_id` (`responder_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `evacuation_centers`
--
ALTER TABLE `evacuation_centers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `ipark_admins`
--
ALTER TABLE `ipark_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `ipark_audit_logs`
--
ALTER TABLE `ipark_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_admin` (`admin_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `ipark_messages`
--
ALTER TABLE `ipark_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_admin` (`admin_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `ipark_parking_slots`
--
ALTER TABLE `ipark_parking_slots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slot_number` (`slot_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_lot` (`parking_lot`),
  ADD KEY `idx_floor` (`floor_level`);

--
-- Indexes for table `ipark_reservations`
--
ALTER TABLE `ipark_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_approved_by` (`admin_approved_by`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_slot` (`parking_slot_id`),
  ADD KEY `idx_status` (`reservation_status`);

--
-- Indexes for table `ipark_staff_approvals`
--
ALTER TABLE `ipark_staff_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `idx_status` (`approval_status`),
  ADD KEY `idx_reservation` (`reservation_id`);

--
-- Indexes for table `ipark_users`
--
ALTER TABLE `ipark_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `moderator_id` (`moderator_id`);

--
-- Indexes for table `responder_locations`
--
ALTER TABLE `responder_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `responder_id` (`responder_id`),
  ADD KEY `idx_last_update` (`last_update`);

--
-- Indexes for table `shinzo_appointments`
--
ALTER TABLE `shinzo_appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `shinzo_login_attempts`
--
ALTER TABLE `shinzo_login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `shinzo_password_resets`
--
ALTER TABLE `shinzo_password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `shinzo_transactions`
--
ALTER TABLE `shinzo_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `shinzo_users`
--
ALTER TABLE `shinzo_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_type` (`user_type`),
  ADD KEY `idx_is_verified` (`is_verified`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_user_email_type` (`email`,`user_type`),
  ADD KEY `idx_username_verified` (`username`,`is_verified`),
  ADD KEY `idx_users_organization` (`organization`);

--
-- Indexes for table `user_feedback`
--
ALTER TABLE `user_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_overall_rating` (`overall_rating`),
  ADD KEY `idx_feedback_category` (`feedback_category`),
  ADD KEY `idx_would_recommend` (`would_recommend`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `evacuation_centers`
--
ALTER TABLE `evacuation_centers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ipark_admins`
--
ALTER TABLE `ipark_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ipark_audit_logs`
--
ALTER TABLE `ipark_audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `ipark_messages`
--
ALTER TABLE `ipark_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipark_parking_slots`
--
ALTER TABLE `ipark_parking_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ipark_reservations`
--
ALTER TABLE `ipark_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ipark_staff_approvals`
--
ALTER TABLE `ipark_staff_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipark_users`
--
ALTER TABLE `ipark_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `responder_locations`
--
ALTER TABLE `responder_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shinzo_appointments`
--
ALTER TABLE `shinzo_appointments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shinzo_login_attempts`
--
ALTER TABLE `shinzo_login_attempts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `shinzo_password_resets`
--
ALTER TABLE `shinzo_password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shinzo_transactions`
--
ALTER TABLE `shinzo_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shinzo_users`
--
ALTER TABLE `shinzo_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user_feedback`
--
ALTER TABLE `user_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  ADD CONSTRAINT `emergency_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `emergency_requests_ibfk_2` FOREIGN KEY (`responder_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `ipark_messages`
--
ALTER TABLE `ipark_messages`
  ADD CONSTRAINT `ipark_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `ipark_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipark_messages_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `ipark_admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipark_reservations`
--
ALTER TABLE `ipark_reservations`
  ADD CONSTRAINT `ipark_reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `ipark_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipark_reservations_ibfk_2` FOREIGN KEY (`parking_slot_id`) REFERENCES `ipark_parking_slots` (`id`),
  ADD CONSTRAINT `ipark_reservations_ibfk_3` FOREIGN KEY (`admin_approved_by`) REFERENCES `ipark_admins` (`id`);

--
-- Constraints for table `ipark_staff_approvals`
--
ALTER TABLE `ipark_staff_approvals`
  ADD CONSTRAINT `ipark_staff_approvals_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `ipark_reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipark_staff_approvals_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `ipark_admins` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`moderator_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `responder_locations`
--
ALTER TABLE `responder_locations`
  ADD CONSTRAINT `responder_locations_ibfk_1` FOREIGN KEY (`responder_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `shinzo_appointments`
--
ALTER TABLE `shinzo_appointments`
  ADD CONSTRAINT `fk_shinzo_appointments_user` FOREIGN KEY (`user_id`) REFERENCES `shinzo_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shinzo_password_resets`
--
ALTER TABLE `shinzo_password_resets`
  ADD CONSTRAINT `fk_shinzo_password_resets_user` FOREIGN KEY (`user_id`) REFERENCES `shinzo_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shinzo_transactions`
--
ALTER TABLE `shinzo_transactions`
  ADD CONSTRAINT `fk_shinzo_transactions_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `shinzo_appointments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_shinzo_transactions_user` FOREIGN KEY (`user_id`) REFERENCES `shinzo_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
