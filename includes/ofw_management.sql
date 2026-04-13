-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2026 at 11:08 AM
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
(33, 10, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 18:08:00');

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
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiration_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `benefits`
--

INSERT INTO `benefits` (`id`, `title`, `description`, `created_by`, `created_at`, `expiration_date`) VALUES
(5, 'wewaeae', 'waewaewaewae', 4, '2026-02-05 05:18:06', '2026-03-03'),
(7, 'eaweawe', 'aweawewaewaewa', 4, '2026-02-05 05:38:01', '2026-02-27'),
(8, 'ewasewa', 'ewaewasdwae', 4, '2026-02-05 07:59:36', '2026-04-15');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `benefit_applications`
--

INSERT INTO `benefit_applications` (`id`, `user_id`, `benefit_id`, `application_type`, `status`, `remarks`, `applied_at`, `documents`) VALUES
(6, 10, 8, 'Death Assistance', 'pending', NULL, '2026-02-05 09:15:54', '1770282954_death_cert_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770282954_burial_permit_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770282954_valid_ids_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770282954_cenomar_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770'),
(7, 10, 7, 'Death Assistance', 'pending', NULL, '2026-02-06 04:57:27', '1770353847_death_cert_1770282954_death_cert_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770353847_burial_permit_1770282954_cenomar_23b989b0-0020-424f-a80f-c2af0feb7579.jpg,1770353847_valid_ids_sampling-docs-ngP-1.docx,1770353847_cenomar_make me a background');

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
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `job_title`, `company_name`, `created_at`, `location`, `preferred_sex`, `salary`, `requirements`, `image`) VALUES
(2, 'LOOKING FOR MANAGER', 'Smart', '2025-03-28 06:13:47', 'DUBAI', 'Any', '10000', NULL, '1770268263_90c7a6b46a35e461e63d4ca4bd2dbc82.jpg'),
(5, 'waewa', 'weasdwae', '2026-02-05 05:08:14', 'sdaweawe', 'Male', '12000', NULL, '1770268094_90c7a6b46a35e461e63d4ca4bd2dbc82.jpg'),
(6, 'LOOKING FOR MANAGER', 'Smart', '2026-02-06 08:52:36', 'sdaweawe', 'Male', '12000', 'Resume\\nValid ID\\nDiploma\\nCertificate of Employment', '1770367956_3e93086e-ee1e-4028-b625-be877b2bac2c.jpg'),
(7, 'LOOKING FOR MANAGER', 'eawe', '2026-02-06 09:18:41', 'waewa', 'Male', '12000', 'Resume\\nValid ID\\nMedical Certificate\\nTranscript of Records', '1770369521_5d74a97d-c4b7-417a-b5a7-fd4ec3fcca9c (1).jpg');

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
  `resume` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`id`, `ofw_id`, `job_id`, `applied_at`, `full_name`, `email`, `phone`, `documents`, `resume`) VALUES
(4, 7, 2, '2025-03-31 04:41:30', 'joshua', 'joshua@example.com', '09703357775', NULL, 'uploads/sk trans with appendix.docx'),
(7, 8, 2, '2025-03-31 06:24:31', 'perlyn pastor', 'perlyn@gmail.com', '09187083685', NULL, 'uploads/sampling-docs-ngP-1.docx'),
(8, 10, 6, '2026-02-06 09:01:51', 'Linard Cordero', 'linardtipagad@gmail.com', '09062685355', '1770368511_resume_nvalid_id_ndiploma_ncertificate_of_employment_1dd8dbf2-1fb6-4abf-a215-622dbf72f156.jpg', '');

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
  `media_type` enum('image','video','both') DEFAULT 'image',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image`, `created_by`, `created_at`) VALUES
(10, 'Current Top Headlines', 'Aviation Investigations: Authorities are in the final stages of investigating the 2023 VSR Learjet 45 crash, with a final report expected soon.\\\\r\\\\nAirline Safety: Air India has grounded a Boeing 787 after a pilot flagged a potential defect in a fuel control switch.\\\\r\\\\nEntertainment Highlights', '1770280888_Breaking-News_Infographic_FINAL-1.jpg', 4, '2026-02-05 08:41:28'),
(11, 'JOB HIRING', 'SAMPLE CONTENT', '1770354941_OFW Hiring Announcement with Philippine Map and Couple.jpg', 4, '2026-02-06 05:15:41');

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
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `profile_picture`, `contact`, `gender`, `address`) VALUES
(2, 'asd', 'employee@gmail.com', '$2y$10$yZJdK/lHROt05WNOKdRK/.PQ0qENmf700TCeW87NqhT78jbpbq8o2', 'ofw', 'approved', 'uploads/1743389274_logo.jpg', NULL, 'Male', NULL),
(4, 'admin', 'admin@example.com', '$2y$10$kfYoBQ18pN5Hs4LKb/4ytOGRop3dkxygYCv2skzbdanRGWb9r1CFW', 'admin', 'approved', NULL, '0909', 'Male', 'manapla'),
(6, 'ralph belandres', 'ralph@example.com', '$2y$10$ecQgcPiX5NMSmZ6lqTE8SeLzfteewsBUMy2DVZOCS.TlOs.GXX6Y2', 'ofw', 'approved', NULL, '09703357773', 'Male', 'victorias'),
(7, 'joshua', 'joshua@example.com', '$2y$10$NGT6K8ZSSRlPgzG4Y3usKuqvjA7DW9albYsVq.CSpug8vt7hQ8kYa', 'ofw', 'approved', NULL, '09703357775', 'Male', 'victorias'),
(8, 'perlyn', 'perlyn@gmail.com', '$2y$10$OHfuonsJ5n/BjBmrlpRGQ.RHQaQ7ByZrT/WZHcMxjFxRxRc9T86su', 'ofw', 'approved', 'uploads/1743402162_1.jpg', '0909', 'Female', 'manapla'),
(10, 'Linard Cordero', 'linardtipagad@gmail.com', '$2y$10$11e6vtkvkwZJ/huLJjNaB.CWsE7Z/M0iRb95QtH1rfEj2TiR6lVVy', 'ofw', 'approved', 'uploads/1770282359_1770270703_cenomar_Mikha_Lim_in_September_2025.jpg', '09213123123', 'Male', 'VICTORIAS CITY'),
(13, 'sample', 'derialjoshua23@gmail.com', '$2y$10$aGrXjAEz2D0JqBUaIHsAaufMCb0UhRGxpujSbW3z6pGOLbBhn4FRe', 'ofw', 'approved', NULL, NULL, 'Male', 'VICTORIAS CITY');

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
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `unique_application` (`ofw_id`,`job_id`),
  ADD KEY `job_id` (`job_id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `benefits`
--
ALTER TABLE `benefits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `benefit_applications`
--
ALTER TABLE `benefit_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`ofw_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
