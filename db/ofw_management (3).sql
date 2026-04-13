-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2026 at 02:15 PM
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
-- Database: `ofw_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 15:53:42'),
(2, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 15:53:47'),
(3, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 15:53:57'),
(4, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 15:54:01'),
(5, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 15:54:28'),
(6, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 16:29:36'),
(7, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 16:46:39'),
(8, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 16:47:11'),
(9, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 16:48:11'),
(10, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 16:51:55'),
(11, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 16:52:51'),
(12, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 16:52:58'),
(13, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:02:00'),
(14, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:02:08'),
(15, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:03:05'),
(16, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:03:14'),
(17, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:18:05'),
(18, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:18:10'),
(19, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:18:44'),
(20, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:18:50'),
(21, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:19:10'),
(22, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:19:16'),
(23, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:20:26'),
(24, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:25:31'),
(25, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:26:12'),
(26, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:28:46'),
(27, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:31:55'),
(28, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:32:00'),
(29, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:32:25'),
(30, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:50:47'),
(31, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 17:50:58'),
(32, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 18:05:09'),
(33, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 18:08:00'),
(34, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:20:06'),
(35, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:20:33'),
(36, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:22:04'),
(37, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:22:15'),
(38, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:25:15'),
(39, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:25:26'),
(40, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:25:58'),
(41, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:26:17'),
(42, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:40:36'),
(43, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:41:32'),
(44, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:41:41'),
(45, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:42:32'),
(46, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:42:54'),
(47, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:43:22'),
(48, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:43:32'),
(49, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:48:28'),
(50, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:48:39'),
(51, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:50:13'),
(52, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:50:22'),
(53, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:51:38'),
(54, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:51:47'),
(55, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:57:32'),
(56, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:58:34'),
(57, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:59:22'),
(58, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:59:31'),
(59, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:59:52'),
(60, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:00:01'),
(61, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:01:01'),
(62, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:01:10'),
(63, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:01:36'),
(64, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:01:44'),
(65, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:02:51'),
(66, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:03:10'),
(67, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:05:43'),
(68, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:05:56'),
(69, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:08:04'),
(70, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:08:12'),
(71, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:08:39'),
(72, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:08:52'),
(73, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:11:26'),
(74, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:34:39'),
(75, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 15:35:47'),
(76, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:05:04'),
(77, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:05:10'),
(78, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:05:42'),
(79, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:17:49'),
(80, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:18:34'),
(81, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:22:13'),
(82, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:25:14'),
(83, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:25:37'),
(84, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:25:54'),
(85, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:27:05'),
(86, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:36:29'),
(87, 14, 'Login', '192.168.0.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 16:37:35'),
(88, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:38:29'),
(89, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:40:33'),
(90, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:47:14'),
(91, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 16:47:23'),
(92, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:01:57'),
(93, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:02:12'),
(94, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:02:18'),
(95, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:03:09'),
(96, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:03:43'),
(97, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:04:09'),
(98, 14, 'Logout', '192.168.0.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:04:48'),
(99, 14, 'Login', '192.168.0.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:07:16'),
(100, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:08:45'),
(101, 14, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:09:40'),
(102, 14, 'Logout', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', '2026-03-07 17:14:49'),
(103, 14, 'Login', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', '2026-03-07 17:15:30'),
(104, 14, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:16:31'),
(105, 14, 'Login', '192.168.0.101', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/140.0.7339.101 Mobile/15E148 Safari/604.1', '2026-03-07 17:17:30'),
(106, 14, 'Logout', '192.168.0.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:18:28'),
(107, 14, 'Login', '192.168.0.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:18:47'),
(108, 14, 'Login', '192.168.0.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:18:48'),
(109, 14, 'Logout', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:19:02'),
(110, 14, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:19:34'),
(111, 14, 'Logout', '192.168.0.101', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/140.0.7339.101 Mobile/15E148 Safari/604.1', '2026-03-07 17:21:02'),
(112, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:23:40'),
(113, 14, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:23:53'),
(114, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:24:30'),
(115, 14, 'Logout', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:25:15'),
(116, 14, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:26:01'),
(117, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:28:49'),
(118, 14, 'Logout', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:34:21'),
(119, 15, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:36:38'),
(120, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:38:28'),
(121, 15, 'Logout', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:40:38'),
(122, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:40:56'),
(123, 15, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:41:38'),
(124, 15, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 17:41:39'),
(126, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 17:54:17'),
(127, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:15:31'),
(128, 14, 'Logout', '192.168.0.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 18:15:36'),
(129, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:15:43'),
(130, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:15:44'),
(131, 14, 'Login', '192.168.0.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 18:16:13'),
(132, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:24:30'),
(133, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:24:38'),
(134, 15, 'Logout', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 18:34:03'),
(135, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:35:07'),
(136, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:37:41'),
(137, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:37:57'),
(138, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:39:55'),
(139, 15, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 18:40:23'),
(140, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:47:05'),
(141, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:47:51'),
(142, 15, 'Logout', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 18:48:42'),
(143, 15, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 18:48:57'),
(144, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:49:02'),
(145, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:49:19'),
(146, 14, 'Logout', '192.168.0.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 18:50:53'),
(147, 14, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:51:15'),
(148, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:51:20'),
(149, 16, 'Login', '192.168.0.104', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_2_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/146.0.7680.38 Mobile/15E148 Safari/604.1', '2026-03-07 18:52:00'),
(150, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:52:22'),
(151, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:52:50'),
(152, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:53:54'),
(153, 14, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:53:58'),
(154, 14, 'Login', '192.168.0.102', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 18:55:49'),
(155, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:57:17'),
(156, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:57:32'),
(157, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:57:42'),
(158, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:57:46'),
(159, 4, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:58:39'),
(160, 10, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 18:58:54'),
(161, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 19:01:17'),
(162, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 19:02:17'),
(163, 10, 'Login', '192.168.0.101', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/140.0.7339.101 Mobile/15E148 Safari/604.1', '2026-03-07 19:12:00'),
(164, 15, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 19:26:17'),
(165, 10, 'Logout', '192.168.0.101', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/140.0.7339.101 Mobile/15E148 Safari/604.1', '2026-03-07 19:31:46'),
(166, 10, 'Login', '192.168.0.101', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/140.0.7339.101 Mobile/15E148 Safari/604.1', '2026-03-07 19:41:31'),
(167, 16, 'Login', '192.168.0.104', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_2_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/146.0.7680.38 Mobile/15E148 Safari/604.1', '2026-03-07 19:57:06'),
(168, 16, 'Login', '192.168.0.104', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_2_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/146.0.7680.38 Mobile/15E148 Safari/604.1', '2026-03-07 20:12:48'),
(169, 16, 'Login', '192.168.0.104', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_2_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/146.0.7680.38 Mobile/15E148 Safari/604.1', '2026-03-07 20:18:59'),
(170, 4, 'Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 20:22:09'),
(171, 15, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 20:54:09'),
(172, 16, 'Login', '192.168.0.104', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_2_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/146.0.7680.38 Mobile/15E148 Safari/604.1', '2026-03-07 20:54:27'),
(173, 15, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 20:54:59'),
(174, 15, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 20:55:04'),
(175, 15, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 20:55:30'),
(176, 15, 'Login', '192.168.0.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-07 21:05:28'),
(177, 16, 'Login', '192.168.0.104', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_2_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/146.0.7680.38 Mobile/15E148 Safari/604.1', '2026-03-07 21:06:11');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `ofw_id` int(11) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `benefits`
--

CREATE TABLE `benefits` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiration_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `benefits`
--

INSERT INTO `benefits` (`id`, `title`, `description`, `requirements`, `created_by`, `created_at`, `expiration_date`) VALUES
(13, 'SSS BENEFIT', 'SSS provides social security protection to private sector workers, self-employed individuals, and OFWs through cash benefits and loans to replace income lost due to sickness, maternity, disability, retirement, or death. Key benefits include a 105-day maternity leave, pension or lump sums for retirement/disability, funeral grants, and unemployment, salary, or housing loans.', 'Valid IDs', 4, '2026-03-07 11:22:49', '2026-03-18'),
(14, 'PhilHealth', 'PhilHealth provides comprehensive health insurance coverage in the Philippines, utilizing a \\\"Case Rate\\\" system where fixed amounts are directly deducted from hospital bills for accredited inpatient and outpatient services. Coverage includes room and board, professional fees, laboratory tests, medicines, and specialized packages for catastrophic illnesses (Z benefits)', 'Valid IDs\nCENOMAR\nProof of Relationship\nPolice Report\nOfficial Receipt', 4, '2026-03-07 11:24:31', '2029-10-17'),
(15, 'HEALT INSURANCE', 'A Health Insurance Benefits Specialist manages employee health insurance plans, handles enrollment, answers inquiries, and ensures compliance with regulations like ACA, HIPAA, and ERISA. They act as a liaison between employees and providers, manage vendor relationships, and reconcile billing. Key duties include conducting open enrollment, updating records, and advising on benefits options', 'Passport\nPolice Report\nOfficial Receipt', 4, '2026-03-07 11:25:48', '2027-08-19');

-- --------------------------------------------------------

--
-- Table structure for table `benefit_applications`
--

CREATE TABLE `benefit_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `benefit_id` int(11) NOT NULL,
  `application_type` varchar(50) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `documents` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Allows multiple applications per user per benefit (for re-applications after rejection)';

--
-- Dumping data for table `benefit_applications`
--

INSERT INTO `benefit_applications` (`id`, `user_id`, `benefit_id`, `application_type`, `status`, `remarks`, `applied_at`, `documents`) VALUES
(6, 10, 8, 'Death Assistance', 'pending', NULL, '2026-02-05 09:15:54', '1770282954_death_cert_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770282954_burial_permit_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770282954_valid_ids_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770282954_cenomar_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770'),
(7, 10, 7, 'Death Assistance', 'pending', NULL, '2026-02-06 04:57:27', '1770353847_death_cert_1770282954_death_cert_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770353847_burial_permit_1770282954_cenomar_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770353847_valid_ids_sampling-docs-ngP-1.docx,1770353847_cenomar_make me a background'),
(8, 14, 9, 'Death Assistance', 'approved', NULL, '2026-03-07 09:23:08', '1772875388_death_certificate_acer-predator-logo-abstract-uhdpaper.com-4K-39.jpg,1772875388_burial_permit_angel-wings-cyberpunk-anime-girl-8k-wallpaper-uhdpaper.com-130@2@a.jpg,1772875388_valid_ids_angel-wings-cyberpunk-anime-girl-8k-wallpaper-uhdpaper.com'),
(9, 15, 11, 'Death Assistance', 'approved', NULL, '2026-03-07 09:44:06', '1772876646_death_certificate_Screenshot_20260307_171022.jpg,1772876646_burial_permit_Screenshot_20260307_171026.jpg'),
(10, 15, 12, 'Burial Assistance', 'approved', NULL, '2026-03-07 09:47:48', '1772876868_death_certificate_Screenshot_20260307_171007.jpg'),
(11, 14, 15, 'Death Assistance', 'approved', NULL, '2026-03-07 12:04:23', '1772885063_passport_Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg,1772885063_police_report_Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg,1772885063_official_receipt_Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg'),
(12, 16, 15, 'Death Assistance', 'approved', NULL, '2026-03-07 12:04:34', '1772885074_passport_IMG_4305.jpeg,1772885074_police_report_IMG_4306.jpeg,1772885074_official_receipt_IMG_4304.png'),
(13, 14, 13, 'Burial Assistance', 'rejected', NULL, '2026-03-07 12:04:54', '1772885094_valid_ids_Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg'),
(14, 14, 13, 'Burial Assistance', 'pending', NULL, '2026-03-07 12:29:43', '1772886583_valid_ids_Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `location` varchar(255) NOT NULL DEFAULT 'Unknown',
  `preferred_sex` varchar(10) NOT NULL DEFAULT 'Any',
  `salary` varchar(50) NOT NULL,
  `requirements` text DEFAULT NULL,
  `max_applicants` int(11) DEFAULT NULL,
  `years_of_experience` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `job_title`, `company_name`, `created_at`, `location`, `preferred_sex`, `salary`, `requirements`, `max_applicants`, `years_of_experience`, `image`) VALUES
(12, 'DRIVER', 'DRIVER INCS.', '2026-03-07 11:11:14', 'DUBAI', 'Male', '100K MONTHLY', 'Passport: Valid for at least six months.\nEmployment Contract: Signed and verified by the Philippine embassy/labor office in the destination country.\nWork Visa/Permit: Proper documentation to work legally.', NULL, NULL, '1772881874_d51fb86a-11ee-4fa5-b304-f3ba9d239f3b.jpg'),
(13, 'BARISTA', 'GFSHGD', '2026-03-07 11:12:39', 'Singapore', 'Any', '100k-150k /month', 'Passport: Valid for at least six months.\nEmployment Contract: Signed and verified by the Philippine embassy/labor office in the destination country.\nMedical Certificate: Valid, DOH-accredited clinic clearance.\nResume/CV: Updated work history.\nPhotos: 2x2 pictures.', NULL, NULL, '1772881959_ce892fe3-7b0e-429f-bbbf-f7d0921d17f8.jpg'),
(14, ' MEDICAL DOCTOR', 'DOCTORS HOSPITAL', '2026-03-07 11:13:44', 'Saudi Arabia', 'Any', 'Above 300k /month', 'Passport: Valid for at least six months.\nEmployment Contract: Signed and verified by the Philippine embassy/labor office in the destination country.\nMedical Certificate: Valid, DOH-accredited clinic clearance.\nNBI Clearance: Valid for travel/work abroad.\nPSA Birth Certificate: Valid for identification.\nResume/CV: Updated work history.', NULL, NULL, '1772882024_a27e5323-e282-43a8-9203-c8e815691252.jpg'),
(15, 'DOMESTIC HELPER', 'DOMESTIC INC.', '2026-03-07 11:15:18', 'Hong Kong', 'Any', '60k-80k /month', 'Passport: Valid for at least six months.\nMedical Certificate: Valid, DOH-accredited clinic clearance.\nResume/CV: Updated work history.', NULL, NULL, '1772882118_910f6161-cb0e-4664-a492-443a8eb48f51.jpg'),
(16, 'AGRICULTURIST', 'P.A.A', '2026-03-07 11:19:10', 'Germany', 'Any', '150k-200k /month', 'Passport: Valid for at least six months.\nWork Visa/Permit: Proper documentation to work legally.\nMedical Certificate: Valid, DOH-accredited clinic clearance.\nResume/CV: Updated work history.', NULL, NULL, '1772882350_images (2).jpg'),
(17, 'WEB DEVELOPER ', 'CPSU', '2026-03-07 11:19:57', 'South Korea', 'Any', 'Above 300k /month', 'Passport: Valid for at least six months.\nEmployment Contract: Signed and verified by the Philippine embassy/labor office in the destination country.\nMedical Certificate: Valid, DOH-accredited clinic clearance.\nTranscript of Records/Diploma: Educational background.\nResume/CV: Updated work history.', NULL, NULL, '1772882397_81062072-c9fe-4a92-b913-a62db049555b.jpg'),
(18, 'MARKETING MANAGER', 'MANAGERS INCS.', '2026-03-07 11:27:25', 'South Korea', 'Female', '20k-40k /month', 'Passport: Valid for at least six months.\nEmployment Contract: Signed and verified by the Philippine embassy/labor office in the destination country.\nWork Visa/Permit: Proper documentation to work legally.\nMedical Certificate: Valid, DOH-accredited clinic clearance.\nNBI Clearance: Valid for travel/work abroad.\nPSA Birth Certificate: Valid for identification.\nTranscript of Records/Diploma: Educational background.\nResume/CV: Updated work history.\nPhotos: 2x2 pictures.', NULL, NULL, '1772882845_27a35fa4-a891-4515-b8ef-15b381c536a9.jpg'),
(19, 'TEACHER', 'DPED', '2026-03-07 11:28:06', 'Taiwan', 'Female', '60k-80k /month', 'Medical Certificate: Valid, DOH-accredited clinic clearance.\nNBI Clearance: Valid for travel/work abroad.\nPSA Birth Certificate: Valid for identification.', 2, NULL, '1772882886_a64ef1c1-764a-4cd8-8623-688506ec5f60.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL,
  `ofw_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `documents` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `resume` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`id`, `ofw_id`, `job_id`, `applied_at`, `full_name`, `email`, `phone`, `documents`, `status`, `resume`) VALUES
(10, 15, 14, '2026-03-07 12:01:21', 'Perlyn Pastor', 'perlynpastor639@gmail.com', '09187083685', '1772884881_passport__valid_for_at_least_six_months__IMG_20260304_140152.jpg,1772884881_employment_contract__signed_and_verified_by_the_philippine_embassy_labor_office_in_the_destination_country__IMG-20260307-WA0002.jpeg,1772884881_medical_certificate__valid__doh_accredited_clinic_clearance__received_1736254021092905.jpeg,1772884881_nbi_clearance__valid_for_travel_work_abroad__1772114123682.jpg,1772884881_psa_birth_certificate__valid_for_identification__Messenger_creation_8647D928-4397-453D-893E-F1E44C09C88D.jpeg,1772884881_resume_cv__updated_work_history__IMG_20260224_100144.jpg', 'rejected', ''),
(11, 15, 15, '2026-03-07 12:04:04', 'Perlyn Pastor', 'perlynpastor639@gmail.com', '09187083685', '1772885044_passport__valid_for_at_least_six_months__received_34243232048654271.jpeg,1772885044_medical_certificate__valid__doh_accredited_clinic_clearance__received_911268048567915.jpeg,1772885044_resume_cv__updated_work_history__IMG_20260227_161003.jpg', 'rejected', ''),
(12, 14, 12, '2026-03-07 12:08:57', 'Gals', 'ralphbelandres1@gmail.com', '9703357773', '1772885337_passport__valid_for_at_least_six_months__Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg,1772885337_employment_contract__signed_and_verified_by_the_philippine_embassy_labor_office_in_the_destination_country__Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg,1772885337_work_visa_permit__proper_documentation_to_work_legally__Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg', 'rejected', ''),
(13, 14, 15, '2026-03-07 12:09:44', 'Gals', 'ralphbelandres1@gmail.com', '9703357773', '1772885384_passport__valid_for_at_least_six_months__Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg,1772885384_medical_certificate__valid__doh_accredited_clinic_clearance__Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg,1772885384_resume_cv__updated_work_history__Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg', 'pending', ''),
(14, 15, 17, '2026-03-07 12:19:15', 'Perlyn Pastor', 'perlynpastor639@gmail.com', '09187083685', '1772885955_passport__valid_for_at_least_six_months__IMG_20260305_131947.jpg,1772885955_employment_contract__signed_and_verified_by_the_philippine_embassy_labor_office_in_the_destination_country__1772874070845.png,1772885955_medical_certificate__valid__doh_accredited_clinic_clearance__Screenshot_20260307_171007.jpg,1772885955_transcript_of_records_diploma__educational_background__Screenshot_20260307_171022.jpg,1772885955_resume_cv__updated_work_history__Screenshot_20260307_171026.jpg', 'rejected', ''),
(24, 15, 15, '2026-03-07 12:28:51', 'Perlyn Pastor', 'perlynpastor639@gmail.com', '09187083685', '1772886531_passport__valid_for_at_least_six_months__Screenshot_20260307_171026.jpg,1772886531_medical_certificate__valid__doh_accredited_clinic_clearance__Screenshot_20260307_171022.jpg,1772886531_resume_cv__updated_work_history__Screenshot_20260307_171007.jpg', 'rejected', ''),
(25, 15, 15, '2026-03-07 12:56:25', 'Perlyn Pastor', 'perlynpastor639@gmail.com', '09187083685', '1772888185_passport__valid_for_at_least_six_months__Screenshot_20260307_171026.jpg,1772888185_medical_certificate__valid__doh_accredited_clinic_clearance__Screenshot_20260307_171022.jpg,1772888185_resume_cv__updated_work_history__Screenshot_20260307_171007.jpg', 'rejected', ''),
(26, 14, 19, '2026-03-07 13:05:44', 'Gals', 'ralphbelandres1@gmail.com', '9703357773', '1772888744_medical_certificate__valid__doh_accredited_clinic_clearance__Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg,1772888744_nbi_clearance__valid_for_travel_work_abroad__Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg,1772888744_psa_birth_certificate__valid_for_identification__Messenger_creation_C16CBC79-FBF8-4E72-B629-06FCE094573D.jpeg', 'pending', ''),
(27, 15, 19, '2026-03-07 13:05:59', 'Perlyn Pastor', 'perlynpastor639@gmail.com', '09187083685', '1772888759_medical_certificate__valid__doh_accredited_clinic_clearance__Screenshot_20260307_171026.jpg,1772888759_nbi_clearance__valid_for_travel_work_abroad__Screenshot_20260307_171022.jpg,1772888759_psa_birth_certificate__valid_for_identification__Screenshot_20260307_171007.jpg', 'rejected', '');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `media_type` enum('image','video') DEFAULT 'image',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiration_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image`, `video`, `media_type`, `created_by`, `created_at`, `expiration_date`) VALUES
(17, 'What is OWWA', '\\\"OWA\\\" most commonly refers to the Overseas Workers Welfare Administration (OWWA), a Philippine government agency protecting Overseas Filipino Workers (OFWs)', '1772883335_475971066_937297648559900_6975334461801932421_n.jpg', '1772868901_video_AQMyUaTdM6DXIyBvqgb9DD1_-6V8YBPJnwAqNdqul8Wn_sS8Tw5Cj7blTxQfpvIKf82SCyxfGnArSnxFwFVnrSf8YEuveAOmPUU6w1dPtdAj3A.mp4', '', 4, '2026-03-07 07:35:01', NULL),
(18, 'Marcos voices solidarity with UAE, hopes for end to hostilities', 'NO TO VIOLENCE. President Ferdinand R. Marcos Jr. expresses solidarity with the United Arab Emirates and calls for a peaceful resolution to tensions in the Gulf region during a phone call with UAE President Sheikh Mohamed bin Zayed Al Nahyan on Friday (March 6, 2026). Marcos also conveyed confidence in the UAE government’s ability to ensure the safety of its residents, including nearly 1 million Filipinos living and working in the country. (Photo from the Presidential Communications Office)', '1772883414_img1876.jpeg', '1772883414_video_Marcos to call for peace in Middle East during UN visit _ ANC.mp4', '', 4, '2026-03-07 11:36:54', NULL),
(19, 'Kuwaiti court convicts killer of OFW', 'MANILA – Department of Migrant Workers (DMW) Secretary Hans Leo Cacdac confirmed Tuesday that the main suspect in the killing of overseas Filipino worker (OFW) Dafnie Nacalaban had been convicted to 14 years imprisonment by a Kuwaiti court.\\\\r\\\\n\\\\r\\\\n“The new development here is the other case, the OFW who had been slain as well, found in the garden. Three other accessories were also convicted in their respective sentences as accomplices,\\\\\\\" Cacdac said in a briefing at the DMW office in Mandaluyong City.\\\\r\\\\n\\\\r\\\\nOn Dec. 31, 2024, the Department of Foreign Affairs reported that Nacalaban’s body had been discovered in the yard of her employer’s home in Saad Al-Abdullah, Jahra.\\\\r\\\\n\\\\r\\\\nNacalaban was reported missing by her second employer in Oct. 2024, after losing contact with her.\\\\r\\\\n\\\\r\\\\nNacalaban, who began working in Kuwait in Dec. 2019, was later found in an advanced state of decomposition following a police report filed by a sibling of the suspect.\\\\r\\\\n\\\\r\\\\nThe suspect, a Kuwaiti national with a known criminal record, later on admitted to killing Nacalaban.\\\\r\\\\n\\\\r\\\\nAfter months of coordination, Nacalaban’s remains were repatriated to the Philippines on Feb. 21, 2025, through the Overseas Workers Welfare Administration and the DMW.', '1772884000_0-02-06-f328860116008edcbafc9e6d30c0762e6eb9b8b46a107b5a47e90c0534e1639f15648145ae2759b7.png', '1772884278_video_AQMDzvkv_WtCfvGzUNVKQJ1X7F-m2M8Iszgvx_k4vop8EYZYwJmW_qp9a6dxHVj5nyzd8N2yCaSf1pxAw3RAT3rfgLs-HYTGUSIc-u-fWQ.mp4', '', 4, '2026-03-07 11:46:40', NULL),
(20, '12 Koreans arrested, suspected scam hub in Pampanga discovered', 'MANILA – Twelve Koreans who are supposedly working in a suspected scamming hub were arrested in an operation in Angeles City, Pampanga, the Bureau of Immigration (BI) reported Friday.\\\\r\\\\n\\\\r\\\\nThe raid on Feb. 25 resulted in the arrest of 33-year-old Korean national Yu Jaemin.\\\\r\\\\n\\\\r\\\\nThe operation stemmed from intelligence information indicating that the Korean was involved in the operation of a suspected scam hub targeting fellow Korean nationals and possibly other foreign victims.\\\\r\\\\n\\\\r\\\\nThe other 11 foreigners were discovered working at an office located along Poinsettia Street in Angeles City, where they are stationed at individual work areas and actively manning operational equipment.\\\\r\\\\n\\\\r\\\\nThe setup, including designated workstations and ongoing digital activities, was consistent with reported scamming operations, according to the BI.\\\\r\\\\n\\\\r\\\\nThe BI found out that three of the foreign nationals are fugitives with existing derogatory records, while several others were found to be overstaying.\\\\r\\\\n\\\\r\\\\nDuring the operation, another Korean arrived at the premises and claimed to be the lessor or manager of the property.\\\\r\\\\n\\\\r\\\\nHe reportedly interfered and falsely claimed that he was sent by Korean authorities.\\\\r\\\\n\\\\r\\\\nBut verification with the Korean government confirmed that no such representation was made. (PNA)', '1772884518_img0901.jpeg', '1772884518_video_Korean scam hub operator in Pampanga arrested _ DZMM Teleradyo (06 March 2026).mp4', '', 4, '2026-03-07 11:55:18', '2026-03-07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('ofw','admin') DEFAULT NULL,
  `status` enum('pending','approved') DEFAULT 'pending',
  `profile_picture` varchar(255) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `civil_status` enum('Single','Married','Widowed','Divorced','Separated') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `profile_picture`, `contact`, `gender`, `address`, `contact_number`, `birth_date`, `civil_status`) VALUES
(4, 'admin', 'admin@example.com', '$2y$10$kfYoBQ18pN5Hs4LKb/4ytOGRop3dkxygYCv2skzbdanRGWb9r1CFW', 'admin', 'approved', NULL, '0909', 'Male', 'manapla', NULL, NULL, NULL),
(10, 'Linard Cordero', 'linardtipagad@gmail.com', '$2y$10$11e6vtkvkwZJ/huLJjNaB.CWsE7Z/M0iRb95QtH1rfEj2TiR6lVVy', 'ofw', 'approved', 'uploads/profile_10_1772878568.jpg', '09213123123', 'Male', 'VICTORIAS CITY', NULL, NULL, NULL),
(14, 'ralph', 'ralphbelandres1@gmail.com', '$2y$10$lrqWXE9iAj2Sin4s/.Jnhul6.WDqMxwkJziADN3Xp.JERIc3HKcE6', 'ofw', 'approved', 'uploads/profile_14_1772878625.jpg', '', 'Male', 'VICTORIAS CITY', NULL, NULL, NULL),
(15, 'Perlyn Pastor', 'perlynpastor639@gmail.com', '$2y$10$uOgFwLvpleLjsi07tvoih.CTH3D2n8iqK/Sc63M3SllSNFHF7b2nu', 'ofw', 'approved', 'uploads/profile_15_1772878566.jpg', '', 'Female', 'Manapla', NULL, '2026-12-19', 'Single'),
(16, 'JOSHUA DERIAL', 'derialjoshua23@gmail.com', '$2y$10$TSCBfiC8mXSjj85odin9jecrmseHuX4zT7I4o1iFGNkQXsC.OIzBC', 'ofw', 'approved', 'uploads/profile_16_1772885155.jpeg', NULL, 'Male', 'Had. Los Angeles Syding Uno Victorias City', '', '2003-08-03', 'Married');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity`
--

CREATE TABLE `user_activity` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_activity` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity`
--

INSERT INTO `user_activity` (`id`, `user_id`, `last_activity`) VALUES
(1, 4, '2026-03-07 14:14:22'),
(2, 14, '2026-03-07 14:12:44'),
(3, 15, '2026-03-07 14:06:02'),
(4, 16, '2026-03-07 14:12:04'),
(5, 10, '2026-03-07 12:47:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `ofw_id` (`ofw_id`);

--
-- Indexes for table `benefits`
--
ALTER TABLE `benefits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `benefit_applications`
--
ALTER TABLE `benefit_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_benefit_applications_user` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `fk_job_applications_ofw` (`ofw_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `last_activity` (`last_activity`),
  ADD KEY `idx_last_activity` (`last_activity`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `benefits`
--
ALTER TABLE `benefits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `benefit_applications`
--
ALTER TABLE `benefit_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`),
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`ofw_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `benefits`
--
ALTER TABLE `benefits`
  ADD CONSTRAINT `benefits_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `benefit_applications`
--
ALTER TABLE `benefit_applications`
  ADD CONSTRAINT `fk_benefit_applications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `fk_job_applications_job` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_job_applications_ofw` FOREIGN KEY (`ofw_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD CONSTRAINT `user_activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
