-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2025 at 12:11 PM
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
-- Database: `mine_ease`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `type` text NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `location` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `type`, `title`, `content`, `location`, `created_at`, `updated_at`) VALUES
(7, 'admin', 'kwiga', 'bba', 'Kigali', '2025-05-30 09:34:52', '2025-05-30 09:34:52'),
(8, 'stock', 'Kwiba', 'Kureba neza', 'huye', '2025-05-30 09:39:06', '2025-05-30 09:39:06'),
(9, 'stake', 'Kwinjira', 'Kureba imigenzereze yabakozi', 'Nyanza', '2025-05-30 09:39:58', '2025-05-30 09:39:58'),
(10, 'hr', 'Abakoze Management yabo', 'Abakozi bari ku managingwa neza', 'Kigali', '2025-05-30 09:40:39', '2025-05-30 09:40:39'),
(11, 'All', 'check Data', 'murebe neza niba data zarabaye sent', 'all', '2025-05-30 09:45:05', '2025-05-30 09:45:05'),
(12, 'emp', 'Payment Check', 'reba niza kuno kwezi warahembwe', 'all', '2025-05-30 09:45:51', '2025-05-30 09:45:51');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_forwards`
--

CREATE TABLE `announcement_forwards` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_comment` text NOT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `forward_type` varchar(50) NOT NULL,
  `status` enum('pending','read','responded','closed') DEFAULT 'pending',
  `admin_response` text DEFAULT NULL,
  `admin_response_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_forwards`
--

INSERT INTO `announcement_forwards` (`id`, `announcement_id`, `user_id`, `user_comment`, `priority`, `forward_type`, `status`, `admin_response`, `admin_response_date`, `created_at`, `updated_at`) VALUES
(1, 12, NULL, 'barahebwe reba neza', 'high', 'suggestion', 'pending', NULL, NULL, '2025-06-01 11:41:18', '2025-06-01 11:41:18'),
(2, 8, 4, 'abo nibande bibye ?', 'high', 'concern', 'pending', 'ni abanyeshuri bo muri UR', '2025-06-01 13:35:51', '2025-06-01 12:01:54', '2025-06-01 13:35:51'),
(3, 7, 4, 'yizehe', 'high', 'clarification', 'pending', 'yize KASS karemge', '2025-06-01 12:27:07', '2025-06-01 12:26:12', '2025-06-01 12:33:02'),
(4, 7, 4, 'nyuma yo kwiga se byagenze gute ?', 'medium', 'concern', 'pending', 'nago hanzwi', '2025-06-01 12:36:06', '2025-06-01 12:28:03', '2025-06-01 12:36:09'),
(5, 7, 4, 'yize hehe waa ?', 'medium', 'concern', 'pending', 'ntago hazwi nezaaa', '2025-06-01 12:36:50', '2025-06-01 12:33:31', '2025-06-01 12:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('present','absent','late','on_leave') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `date`, `time_in`, `time_out`, `status`) VALUES
(1, 6, '2025-04-19', '22:19:43', NULL, 'present'),
(2, 5, '2025-04-19', '22:21:32', '22:21:36', 'present'),
(3, 6, '2025-05-28', '12:39:59', '12:40:57', 'present'),
(4, 5, '2025-05-28', '12:40:10', '12:40:59', 'present'),
(5, 4, '2025-05-28', '12:43:36', '12:43:39', 'present'),
(6, 3, '2025-05-28', '12:43:58', NULL, 'present'),
(7, 6, '2025-05-29', '16:38:56', '16:39:13', 'present'),
(8, 1, '2025-05-31', '18:44:03', '18:44:21', 'present'),
(9, 7, '2025-05-31', '19:33:14', '19:33:27', 'present'),
(10, 18, '2025-06-01', '21:41:36', '21:41:38', 'present'),
(11, 17, '2025-06-01', '21:41:40', '21:41:41', 'present'),
(12, 16, '2025-06-01', '21:41:43', '21:41:44', 'present'),
(13, 9, '2025-06-01', '21:41:46', '21:41:48', 'present'),
(14, 8, '2025-06-01', '21:41:50', '21:41:52', 'present'),
(15, 7, '2025-06-01', '21:51:31', '21:51:32', 'present'),
(16, 6, '2025-06-01', '22:07:35', '22:07:37', 'present');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `location` varchar(100) NOT NULL,
  `department_id` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `date_added` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `item_name`, `description`, `serial_number`, `location`, `department_id`, `notes`, `date_added`, `last_updated`) VALUES
(1, 'dd', 'ddd', '33', 'rrr', '0', 'ddd', '2025-04-11 23:27:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hr_details`
--

CREATE TABLE `hr_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_details`
--

INSERT INTO `hr_details` (`id`, `user_id`, `specialization`) VALUES
(1, 18, 'Recruitment'),
(2, 19, 'Benefits'),
(3, 20, 'Training'),
(4, 21, 'Performance Review'),
(5, 22, 'Employee Relations'),
(6, 23, 'Compliance'),
(7, 24, 'Payroll'),
(8, 2, '');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `employee_name` varchar(100) NOT NULL,
  `department` varchar(50) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','denied') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `added_by` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tel` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `employee_id`, `employee_name`, `department`, `leave_type`, `start_date`, `end_date`, `reason`, `status`, `created_at`, `updated_at`, `added_by`, `email`, `tel`) VALUES
(3, '1', 'TUGIRIMANA Danny', 'Mining', 'Study Leave', '2025-05-31', '2025-05-31', 'I want to see my Parents', 'approved', '2025-05-31 17:45:40', '2025-05-31 17:45:58', '1', 'dannytugirimana12@gmail.com', '0790365857'),
(4, '1', 'TUGIRIMANA Danny', 'Mining', 'Paternity Leave', '2025-05-31', '2025-06-03', 'to gooooo', 'denied', '2025-05-31 17:49:54', '2025-05-31 18:16:57', '1', 'dannytugirimana12@gmail.com', '0790365857'),
(5, '1', 'TUGIRIMANA Danny', 'Mining', 'Sick Leave', '2025-05-31', '2025-06-07', 'I have Marallia', 'approved', '2025-05-31 18:16:31', '2025-05-31 18:17:53', '1', 'dannytugirimana12@gmail.com', '0790365857');

-- --------------------------------------------------------

--
-- Table structure for table `mattendance`
--

CREATE TABLE `mattendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `year` smallint(6) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '1=attended, 0=not attended',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mattendance`
--

INSERT INTO `mattendance` (`id`, `user_id`, `month`, `year`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, 4, 2025, 1, '2025-04-20 07:21:01', '2025-04-20 07:21:01'),
(3, 2, 4, 2025, 1, '2025-04-20 07:21:04', '2025-04-20 07:21:04'),
(4, 3, 5, 2025, 0, '2025-05-28 10:41:23', '2025-05-29 14:38:07'),
(5, 2, 5, 2025, 0, '2025-05-28 10:41:25', '2025-05-29 14:37:28'),
(6, 1, 5, 2025, 0, '2025-05-28 10:41:26', '2025-05-28 10:41:44'),
(7, 4, 5, 2025, 0, '2025-05-28 10:41:28', '2025-05-29 14:38:17'),
(8, 5, 5, 2025, 0, '2025-05-28 10:41:30', '2025-05-31 15:52:51'),
(9, 6, 5, 2025, 0, '2025-05-28 10:41:31', '2025-05-31 15:52:53');

-- --------------------------------------------------------

--
-- Table structure for table `mining_requests`
--

CREATE TABLE `mining_requests` (
  `id` int(11) NOT NULL COMMENT 'Unique request identifier',
  `user_id` int(11) NOT NULL COMMENT 'User who submitted the request',
  `request_text` text NOT NULL COMMENT 'Detailed request content',
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending' COMMENT 'Current request status',
  `request_date` datetime DEFAULT current_timestamp() COMMENT 'When request was submitted',
  `response` text DEFAULT NULL COMMENT 'Admin response to the request',
  `response_date` datetime DEFAULT NULL COMMENT 'When response was provided'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores mining requests from users';

--
-- Dumping data for table `mining_requests`
--

INSERT INTO `mining_requests` (`id`, `user_id`, `request_text`, `status`, `request_date`, `response`, `response_date`) VALUES
(3, 1, 'dfghgfdsfghgfdfghjhgfdfg', 'Pending', '2025-04-21 23:04:54', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT 'general',
  `is_read` tinyint(1) DEFAULT 0,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `content`, `type`, `is_read`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'New employee added', 'employee', 1, NULL, '2025-04-16 08:02:51', '2025-04-16 08:12:41'),
(2, 'Payroll updated', 'payroll', 0, NULL, '2025-04-16 08:02:51', '2025-04-16 08:02:51'),
(3, 'Stock level alert: Laptops below threshold', 'stock', 0, NULL, '2025-04-16 08:02:51', '2025-04-16 08:02:51'),
(4, 'Meeting scheduled for tomorrow', 'calendar', 0, NULL, '2025-04-16 08:02:51', '2025-04-16 08:02:51'),
(5, 'System maintenance scheduled', 'system', 0, NULL, '2025-04-16 08:02:51', '2025-04-16 08:02:51');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `is_paid` tinyint(1) DEFAULT 0,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `user_id`, `month`, `year`, `is_paid`, `payment_date`, `created_at`, `updated_at`) VALUES
(17, 1, 6, 2025, 1, '2025-06-01 19:09:07', '2025-06-01 19:09:07', '2025-06-01 20:48:55'),
(18, 2, 6, 2025, 1, '2025-06-01 19:27:23', '2025-06-01 19:27:23', '2025-06-01 19:27:23'),
(19, 3, 6, 2025, 1, '2025-06-01 19:36:30', '2025-06-01 19:36:30', '2025-06-01 19:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stockout`
--

CREATE TABLE `stockout` (
  `id` int(11) NOT NULL,
  `in_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `reason` varchar(200) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stockout`
--

INSERT INTO `stockout` (`id`, `in_id`, `quantity`, `unit_price`, `reason`, `created_at`) VALUES
(1, 20, 0, 6000, 'sale', '2025-05-30'),
(2, 24, 40, 500000, 'sale', '2025-05-30');

-- --------------------------------------------------------

--
-- Table structure for table `stock_description`
--

CREATE TABLE `stock_description` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_description`
--

INSERT INTO `stock_description` (`id`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'The grand balancing of this chart is the most vital for us for balancing the same space with stock. In order to understand our distribution ratios in a shared view distributed in a good manner, we track stock movements across all warehouses and monitor value changes over time. This helps us optimize inventory levels and improve cash flow management.', NULL, '2025-04-16 07:44:33', '2025-04-16 07:44:33');

-- --------------------------------------------------------

--
-- Table structure for table `stock_items`
--

CREATE TABLE `stock_items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_value` decimal(15,2) GENERATED ALWAYS AS (`quantity` * `unit_price`) STORED,
  `location` varchar(100) DEFAULT NULL,
  `status` enum('in-stock','low-stock','out-of-stock') DEFAULT 'in-stock',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_items`
--

INSERT INTO `stock_items` (`id`, `item_name`, `category`, `quantity`, `unit_price`, `location`, `status`, `created_at`, `updated_at`) VALUES
(16, 'Item11', 'Furniture', 34, 173.92, 'Storage Room', 'in-stock', '2025-04-16 07:44:33', '2025-04-16 07:44:33'),
(17, 'Item12', 'Office Supplies', 53, 217.43, 'Warehouse A', 'in-stock', '2025-04-16 07:44:33', '2025-04-16 07:44:33'),
(18, 'Item13', 'Electronics', 23, 394.61, 'Warehouse B', 'in-stock', '2025-04-16 07:44:33', '2025-04-16 07:44:33'),
(24, 'platinum', 'Gold', 60, 500000.00, 'Huye', 'in-stock', '2025-05-30 14:24:57', '2025-06-01 21:02:38'),
(25, 'gasegeriti', 'stone', 1, 5000.00, 'Nyamagabe', 'in-stock', '2025-06-01 21:03:52', '2025-06-01 21:03:52'),
(26, 'gasegeriti', 'stone', 1, 5000.00, 'Nyamagabe', 'in-stock', '2025-06-01 21:04:55', '2025-06-01 21:04:55');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `transaction_type` enum('in','out') NOT NULL,
  `quantity_in` int(11) DEFAULT 0,
  `quantity_out` int(11) DEFAULT 0,
  `value_in` decimal(15,2) DEFAULT 0.00,
  `value_out` decimal(15,2) DEFAULT 0.00,
  `transaction_date` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_transactions`
--

INSERT INTO `stock_transactions` (`id`, `item_id`, `transaction_type`, `quantity_in`, `quantity_out`, `value_in`, `value_out`, `transaction_date`, `notes`, `created_by`, `created_at`) VALUES
(1, 1, 'in', 50, 0, 60000.00, 0.00, '2025-04-16 09:44:33', 'Initial stock receipt', NULL, '2025-04-16 07:44:33'),
(2, 1, 'out', 0, 25, 0.00, 30000.00, '2025-04-16 09:44:33', 'Distributed to IT department', NULL, '2025-04-16 07:44:33'),
(3, 2, 'in', 100, 0, 15000.00, 0.00, '2025-04-16 09:44:33', 'Bulk purchase from supplier', NULL, '2025-04-16 07:44:33'),
(4, 2, 'out', 0, 60, 0.00, 9000.00, '2025-04-16 09:44:33', 'Office setup', NULL, '2025-04-16 07:44:33'),
(5, 3, 'in', 30, 0, 10500.00, 0.00, '2025-04-16 09:44:33', 'New inventory arrival', NULL, '2025-04-16 07:44:33'),
(6, 3, 'out', 0, 15, 0.00, 5250.00, '2025-04-16 09:44:33', 'Department allocation', NULL, '2025-04-16 07:44:33'),
(7, 4, 'in', 80, 0, 20000.00, 0.00, '2025-04-16 09:44:33', 'Quarterly stock replenishment', NULL, '2025-04-16 07:44:33'),
(8, 4, 'out', 0, 50, 0.00, 12500.00, '2025-04-16 09:44:33', 'Office expansion project', NULL, '2025-04-16 07:44:33'),
(9, 5, 'in', 45, 0, 9000.00, 0.00, '2025-04-16 09:44:33', 'Tech upgrade initiative', NULL, '2025-04-16 07:44:33'),
(10, 5, 'out', 0, 25, 0.00, 5000.00, '2025-04-16 09:44:33', 'Employee equipment provision', NULL, '2025-04-16 07:44:33'),
(11, 6, 'in', 33, 0, 18129.26, 0.00, '2025-04-16 09:44:33', 'Transaction for Item6', NULL, '2025-04-16 07:44:33'),
(12, 7, 'in', 67, 0, 21950.91, 0.00, '2025-04-16 09:44:33', 'Transaction for Item16', NULL, '2025-04-16 07:44:33'),
(13, 8, 'out', 0, 52, 0.00, 4120.54, '2025-04-16 09:44:33', 'Transaction for Item7', NULL, '2025-04-16 07:44:33'),
(14, 9, 'in', 116, 0, 44523.96, 0.00, '2025-04-16 09:44:33', 'Transaction for Item17', NULL, '2025-04-16 07:44:33'),
(15, 10, 'out', 0, 43, 0.00, 4556.65, '2025-04-16 09:44:33', 'Transaction for Item8', NULL, '2025-04-16 07:44:33'),
(16, 11, 'in', 30, 0, 24824.54, 0.00, '2025-04-16 09:44:33', 'Transaction for Item18', NULL, '2025-04-16 07:44:33'),
(17, 12, 'in', 105, 0, 4972.11, 0.00, '2025-04-16 09:44:33', 'Transaction for Item9', NULL, '2025-04-16 07:44:33'),
(18, 13, 'out', 0, 22, 0.00, 12368.54, '2025-04-16 09:44:33', 'Transaction for Item19', NULL, '2025-04-16 07:44:33'),
(19, 14, 'out', 0, 19, 0.00, 15415.81, '2025-04-16 09:44:33', 'Transaction for Item10', NULL, '2025-04-16 07:44:33'),
(20, 15, 'out', 0, 17, 0.00, 6603.34, '2025-04-16 09:44:33', 'Transaction for Item20', NULL, '2025-04-16 07:44:33'),
(21, 16, 'in', 34, 0, 20192.76, 0.00, '2025-04-16 09:44:33', 'Transaction for Item11', NULL, '2025-04-16 07:44:33'),
(22, 17, 'in', 58, 0, 4727.94, 0.00, '2025-04-16 09:44:33', 'Transaction for Item12', NULL, '2025-04-16 07:44:33'),
(23, 18, 'out', 0, 57, 0.00, 17405.21, '2025-04-16 09:44:33', 'Transaction for Item13', NULL, '2025-04-16 07:44:33'),
(24, 19, 'in', 77, 0, 15938.10, 0.00, '2025-04-16 09:44:33', 'Transaction for Item14', NULL, '2025-04-16 07:44:33'),
(25, 20, 'out', 0, 25, 0.00, 4273.62, '2025-04-16 09:44:33', 'Transaction for Item15', NULL, '2025-04-16 07:44:33'),
(29, 1, 'in', 125000, 0, 150000000.00, 0.00, '2025-04-16 09:50:11', 'Bulk purchase Q1', NULL, '2025-04-16 07:50:11'),
(30, 2, 'in', 85000, 0, 12750000.00, 0.00, '2025-04-16 09:50:11', 'Restock from supplier', NULL, '2025-04-16 07:50:11'),
(31, 3, 'in', 65000, 0, 22750000.00, 0.00, '2025-04-16 09:50:11', 'Inventory expansion', NULL, '2025-04-16 07:50:11'),
(32, 1, 'out', 0, 75000, 0.00, 90000000.00, '2025-04-16 09:50:11', 'Distribution to branches', NULL, '2025-04-16 07:50:11'),
(33, 2, 'out', 0, 45000, 0.00, 6750000.00, '2025-04-16 09:50:11', 'Client fulfillment', NULL, '2025-04-16 07:50:11'),
(34, 3, 'out', 0, 38000, 0.00, 13300000.00, '2025-04-16 09:50:11', 'Quarterly allocation', NULL, '2025-04-16 07:50:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'employee',
  `site` varchar(50) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `salary` decimal(12,2) DEFAULT 0.00,
  `providence` decimal(12,2) DEFAULT 0.00,
  `tax` decimal(12,2) DEFAULT 0.00,
  `loan` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `username`, `user_type`, `site`, `department`, `phone`, `profile_image`, `registration_date`, `created_at`, `updated_at`, `salary`, `providence`, `tax`, `loan`) VALUES
(1, 'John Doe', 'employee@gmail.com', '$2y$10$MKeM/jS0qsdS9HkDnCLHM.bi1qfTEQuxSJ9g2T3ZWWRAgmsxUMxem', 'johndoe', 'employee', 'Kigali', 'Mining', '+250788123456', NULL, '2025-03-21', '2025-04-17 20:17:48', '2025-04-23 13:13:18', 300000.00, 10000.00, 20000.00, 5000.00),
(2, 'Jane Smith', 'stock@gmail.com', '$2y$10$onPxS/IbEBRPROlZJHKeD.dmT0vZuKwWgnUF/WKLl/YEqPi/xlwwu', 'janesmith', 'stock', 'Burera', 'Processing', '+250788654321', NULL, '2023-01-15', '2025-04-17 20:17:48', '2025-05-30 08:31:56', 0.00, 0.00, 0.00, 0.00),
(3, 'TUGIRIMANA Danny', 'danny.t@example.com', '$2y$10$3x87Qgno/KxXDZWAhDvBZOfRiiqWwn9Z64//5tUCXJ9/H3aRlRUNm', 'dannyt', 'employee', 'Kigali', 'Mining', '+250788112233', NULL, '2012-05-21', '2025-04-17 20:17:48', '2025-05-29 14:33:01', 300000.00, 10000.00, 20000.00, 5000.00),
(4, 'Robert Johnson', 'stakeholder@gmail.com', '$2y$10$dEVrHP.e5IUrPVouqhgx4uIv/z6OmI78tBfMSlUFVikh4Wgu7T56W', 'robertj', 'stakeholder', 'Nyanza', 'Finance', '+250788445566', NULL, '2024-02-10', '2025-04-17 20:17:48', '2025-04-23 13:13:59', 400000.00, 15000.00, 30000.00, 20000.00),
(5, 'Emma Watson', 'humanresource@gmail.com', '$2y$10$1Nuud2grNvxm/52IvAno8.n1ExH2kGKOyg/naJfiSjg/Oyt2YEu9C', 'emmaw', 'hr', 'Kigali', 'HR', '+250788778899', NULL, '2022-04-05', '2025-04-17 20:17:48', '2025-04-23 13:20:37', 320000.00, 11000.00, 22000.00, 0.00),
(6, 'Admin User', 'admin@gmail.com', '$2y$10$aJOfZbe0rYDTLt1IrlqLWuNMnX/vNvsC8Ip41rKYLfTXVL9tHkt0q', 'admin', 'admin', 'Head Office', 'Administration', '+250788000000', NULL, '2020-01-01', '2025-04-17 20:17:48', '2025-04-23 13:14:45', 0.00, 0.00, 0.00, 0.00),
(7, 'emp', 'emp12@gmail.com', '$2y$10$3nXRwfWDcfD8kCQW0iUSge39OXRJf8Zo4MBHLujDGgAjLphneR.Nu', 'Kimomo', 'employee', 'kigali', 'Emp', '0790365857', 'uploads/default-profile.png', '2025-05-30', '2025-05-30 08:48:50', '2025-05-31 18:35:02', 0.00, 0.00, 0.00, 0.00),
(8, 'humanreso', 'human@gmail.com', '$2y$10$oQVaOFQ050ko.3iMxpomB.VvKdnP8dItfGLVkvtfZXBscZBONueIG', 'human12', 'hr', 'huye', 'HR', '0790365857', 'uploads/default-profile.png', '2025-05-30', '2025-05-30 08:52:17', '2025-05-30 08:52:17', 0.00, 0.00, 0.00, 0.00),
(9, 'habakuki', 'habakuki12@gmail.com', '$2y$10$cnR7pl6S1kxldCSDUTRl4e0S4BLYY.7bf2AMD/eUd7bE4YAQO/bnq', 'hababa', 'Admin', 'kabuga', 'office', '0790365857', 'uploads/default-profile.png', '2025-05-30', '2025-05-30 10:19:12', '2025-05-30 10:19:12', 0.00, 0.00, 0.00, 0.00),
(16, 'fab Mukino', 'fabu@gmail.com', '12345678', 'fab', 'admin', 'Kigali', 'Administration', '0790365851', NULL, '2025-06-01', '2025-06-01 15:36:55', '2025-06-01 15:39:24', 0.00, 0.00, 0.00, 0.00),
(17, 'emp', 'employee12@gmail.com', '12345678', 'emp12', 'employee', NULL, 'Mining', '0736430436', NULL, '2025-06-01', '2025-06-01 16:03:39', '2025-06-01 16:03:39', 0.00, 0.00, 0.00, 0.00),
(18, 'TUGIRI Dan', 'dan12@gmail.com', '12345678', 'tdan', 'hr', NULL, 'HR', '0790365857', NULL, '2025-06-01', '2025-06-01 17:16:41', '2025-06-01 17:16:41', 0.00, 0.00, 0.00, 0.00),
(19, 'Emmanuel', 'example@gmail.com', '123', 'Emmanuel', 'admin', NULL, 'Administration', '0123456789', NULL, '2025-06-01', '2025-06-01 20:29:58', '2025-06-01 20:29:58', 0.00, 0.00, 0.00, 0.00),
(20, 'TUGIRIMANA Danny', 'dannytugirimana12@gmail.com', '12345678', 'dan_drizzy_', 'admin', NULL, 'HR', '0790365857', NULL, '2025-06-01', '2025-06-01 20:30:07', '2025-06-01 20:30:07', 0.00, 0.00, 0.00, 0.00),
(21, 'xxxxx', 'emp1@gmail.com', '123', 'emp1', 'employee', NULL, 'Processing', '0123456789', NULL, '2025-06-01', '2025-06-01 20:36:04', '2025-06-01 20:36:04', 0.00, 0.00, 0.00, 0.00),
(22, 'bbbbbbbbbbbb', 'HR@gmail.com', '123', 'HR', 'hr', NULL, 'HR', '0123456789', NULL, '2025-06-01', '2025-06-01 20:44:34', '2025-06-01 20:44:34', 0.00, 0.00, 0.00, 0.00),
(23, 'ccccccccccc', 'newstock@gmail.com', '123', 'newstock', 'stock', NULL, 'Stock', '0123456789', NULL, '2025-06-01', '2025-06-01 20:58:38', '2025-06-01 20:58:38', 0.00, 0.00, 0.00, 0.00),
(24, 'eeeeeee', 'stakeholderr@gmail.com', '123', 'stakeholderr', 'stakeholder', NULL, 'Head Office', '0123456789', NULL, '2025-06-01', '2025-06-01 21:11:06', '2025-06-01 21:11:06', 0.00, 0.00, 0.00, 0.00),
(25, 'test', 'test@gmail.com', '123', 'test', 'stakeholder', NULL, 'Finance', '0123456789', NULL, '2025-06-01', '2025-06-01 21:18:53', '2025-06-01 21:18:53', 0.00, 0.00, 0.00, 0.00),
(26, 'xxxxxx', 'emma@gmail.com', '123', 'emma', 'admin', NULL, 'Administration', '0123456789', NULL, '2025-06-01', '2025-06-01 21:27:41', '2025-06-01 21:27:41', 0.00, 0.00, 0.00, 0.00),
(27, 'wertyu', 'stok123@gmail.com', '123', 'wertyu', 'stock', NULL, 'Mining', '0123456789', NULL, '2025-06-01', '2025-06-01 21:52:28', '2025-06-01 21:52:28', 0.00, 0.00, 0.00, 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcement_forwards`
--
ALTER TABLE `announcement_forwards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_id` (`announcement_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `priority` (`priority`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`date`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_details`
--
ALTER TABLE `hr_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mattendance`
--
ALTER TABLE `mattendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`month`,`year`);

--
-- Indexes for table `mining_requests`
--
ALTER TABLE `mining_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_month_year` (`user_id`,`month`,`year`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `stockout`
--
ALTER TABLE `stockout`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_description`
--
ALTER TABLE `stock_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_items`
--
ALTER TABLE `stock_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `announcement_forwards`
--
ALTER TABLE `announcement_forwards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hr_details`
--
ALTER TABLE `hr_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mattendance`
--
ALTER TABLE `mattendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `mining_requests`
--
ALTER TABLE `mining_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique request identifier', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stockout`
--
ALTER TABLE `stockout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stock_description`
--
ALTER TABLE `stock_description`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_items`
--
ALTER TABLE `stock_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `mattendance`
--
ALTER TABLE `mattendance`
  ADD CONSTRAINT `mattendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
