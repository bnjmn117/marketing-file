-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 07:31 AM
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
-- Database: `marketing`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin_type` enum('main','college','highschool') NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `admin_type`, `fullname`, `email`, `last_login`, `created_at`) VALUES
(1, 'mainadmin', '$2y$10$XtRCR51DgX8FW6dhM3IKtutOJyn7fu7SqNWeswgVH5Ylv4Z2kyUBW', 'main', 'Main Administrator', 'mainadmin@coursereg.com', '2026-03-15 19:22:42', '2026-03-14 13:28:25'),
(2, 'collegeadmin', '$2y$10$XtRCR51DgX8FW6dhM3IKtutOJyn7fu7SqNWeswgVH5Ylv4Z2kyUBW', 'college', 'College Administrator', 'collegeadmin@coursereg.com', '2026-03-15 19:24:08', '2026-03-14 13:28:25'),
(3, 'hsadmin', '$2y$10$XtRCR51DgX8FW6dhM3IKtutOJyn7fu7SqNWeswgVH5Ylv4Z2kyUBW', 'highschool', 'High School Administrator', 'hsadmin@coursereg.com', '2026-03-14 21:52:35', '2026-03-14 13:28:25');

-- --------------------------------------------------------

--
-- Table structure for table `college_courses`
--

CREATE TABLE `college_courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(10) NOT NULL,
  `course_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `college_courses`
--

INSERT INTO `college_courses` (`id`, `course_code`, `course_name`) VALUES
(1, 'BSIT', 'Bachelor of Science in Information Technology'),
(2, 'HM', 'Hospitality Management'),
(3, 'OAD', 'Office Administration'),
(4, 'CRIM', 'Criminology'),
(5, 'EDUC', 'Education');

-- --------------------------------------------------------

--
-- Table structure for table `highschool_strands`
--

CREATE TABLE `highschool_strands` (
  `id` int(11) NOT NULL,
  `strand_code` varchar(10) NOT NULL,
  `strand_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `highschool_strands`
--

INSERT INTO `highschool_strands` (`id`, `strand_code`, `strand_name`) VALUES
(1, 'STEM', 'Science, Technology, Engineering and Mathematics'),
(2, 'HUMMS', 'Humanities and Social Sciences'),
(3, 'TECHVOC', 'Technical-Vocational Livelihood');

-- --------------------------------------------------------

--
-- Table structure for table `program_heads`
--

CREATE TABLE `program_heads` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `program_code` varchar(20) NOT NULL,
  `program_type` enum('college','highschool') NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_heads`
--

INSERT INTO `program_heads` (`id`, `username`, `password`, `program_code`, `program_type`, `fullname`, `email`, `last_login`, `created_at`) VALUES
(1, 'bsit_head', '$2y$10$xbu.lIkDVI/RqXqgZCt0Guh2/vlu.FmkvpsliRtEY5T50ZoWTiVx2', 'BSIT', 'college', 'BSIT Program Head', 'bsit.head@coursereg.com', '2026-03-15 19:25:23', '2026-03-14 14:04:03'),
(2, 'hm_head', '$2y$10$xbu.lIkDVI/RqXqgZCt0Guh2/vlu.FmkvpsliRtEY5T50ZoWTiVx2', 'HM', 'college', 'HM Program Head', 'hm.head@coursereg.com', NULL, '2026-03-14 14:04:03'),
(3, 'oad_head', '$2y$10$xbu.lIkDVI/RqXqgZCt0Guh2/vlu.FmkvpsliRtEY5T50ZoWTiVx2', 'OAD', 'college', 'OAD Program Head', 'oad.head@coursereg.com', '2026-03-14 22:13:52', '2026-03-14 14:04:03'),
(4, 'crim_head', '$2y$10$xbu.lIkDVI/RqXqgZCt0Guh2/vlu.FmkvpsliRtEY5T50ZoWTiVx2', 'CRIM', 'college', 'CRIM Program Head', 'crim.head@coursereg.com', NULL, '2026-03-14 14:04:03'),
(5, 'educ_head', '$2y$10$xbu.lIkDVI/RqXqgZCt0Guh2/vlu.FmkvpsliRtEY5T50ZoWTiVx2', 'EDUC', 'college', 'EDUC Program Head', 'educ.head@coursereg.com', NULL, '2026-03-14 14:04:03'),
(6, 'stem_head', '$2y$10$xbu.lIkDVI/RqXqgZCt0Guh2/vlu.FmkvpsliRtEY5T50ZoWTiVx2', 'STEM', 'highschool', 'STEM Program Head', 'stem.head@coursereg.com', NULL, '2026-03-14 14:04:03'),
(7, 'humms_head', '$2y$10$xbu.lIkDVI/RqXqgZCt0Guh2/vlu.FmkvpsliRtEY5T50ZoWTiVx2', 'HUMMS', 'highschool', 'HUMMS Program Head', 'humms.head@coursereg.com', NULL, '2026-03-14 14:04:03'),
(8, 'techvoc_head', '$2y$10$xbu.lIkDVI/RqXqgZCt0Guh2/vlu.FmkvpsliRtEY5T50ZoWTiVx2', 'TECHVOC', 'highschool', 'TECHVOC Program Head', 'techvoc.head@coursereg.com', NULL, '2026-03-14 14:04:03');

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(11) NOT NULL,
  `school_name` varchar(200) NOT NULL,
  `school_type` enum('college','highschool','both') NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `school_name`, `school_type`, `address`, `is_active`) VALUES
(1, 'Guimba Municipal College - Main Campus', 'college', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(2, 'Guimba Municipal College - Satellite Campus', 'college', 'Brgy. San Jose, Guimba, Nueva Ecija', 1),
(3, 'Nueva Ecija University of Science and Technology - Guimba Extension', 'college', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(4, 'Philippine Merchant Marine School - Guimba Branch', 'college', 'Brgy. San Roque, Guimba, Nueva Ecija', 1),
(5, 'STI College - Guimba', 'college', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(6, 'AMA Computer College - Guimba', 'college', 'Brgy. San Jose, Guimba, Nueva Ecija', 1),
(7, 'Guimba Institute of Technology', 'college', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(8, 'Guimba National High School - Main', 'highschool', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(9, 'Guimba National High School - San Jose Annex', 'highschool', 'Brgy. San Jose, Guimba, Nueva Ecija', 1),
(10, 'Guimba National High School - San Roque Extension', 'highschool', 'Brgy. San Roque, Guimba, Nueva Ecija', 1),
(11, 'Nuestra Señora Del Carmen Academy', 'highschool', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(12, 'Guimba Christian School', 'highschool', 'Brgy. San Jose, Guimba, Nueva Ecija', 1),
(13, 'Guimba Vocational High School', 'highschool', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(14, 'San Jose Integrated School', 'highschool', 'Brgy. San Jose, Guimba, Nueva Ecija', 1),
(15, 'San Roque National High School', 'highschool', 'Brgy. San Roque, Guimba, Nueva Ecija', 1),
(16, 'St. Mary\'s High School of Guimba', 'highschool', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(17, 'Guimba East Integrated School', 'highschool', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(18, 'Guimba West National High School', 'highschool', 'Brgy. San Jose, Guimba, Nueva Ecija', 1),
(19, 'Don Eufemio F. Eriguel Memorial National High School', 'highschool', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(20, 'Guimba Central School', 'both', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(21, 'Guimba Institute - Basic Education Department', 'both', 'Brgy. San Jose, Guimba, Nueva Ecija', 1),
(22, 'Developmental Institute of Guimba', 'both', 'Brgy. Poblacion, Guimba, Nueva Ecija', 1),
(23, 'Guimba Progressive School', 'both', 'Brgy. San Roque, Guimba, Nueva Ecija', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `course_type` enum('college','highschool') NOT NULL,
  `course` varchar(50) NOT NULL,
  `previous_school_id` int(11) DEFAULT NULL,
  `previous_school_other` varchar(200) DEFAULT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `is_verified` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `course_type`, `course`, `previous_school_id`, `previous_school_other`, `otp_code`, `otp_expires`, `is_verified`, `created_at`) VALUES
(1, 'cscsccsss', 'johnantonyarsuwa@gmail.com', '$2y$10$dagfy4M9R/6sLERn/3C2euT6HFQESuhSZRgXRkVvZo7bSLm5j5msW', 'college', 'BSIT', 0, '', '741109', '2026-03-14 10:48:00', 0, '2026-03-14 09:38:00'),
(2, 'cssc', 'johnantonyarsuwa0@gmail.com', '$2y$10$OfwKhJd4P76Xj64kqXXqp.PR22Jgs.dXR1xgehae7Dpq1hQFNBsEe', 'college', 'CRIM', 1, '', '543795', '2026-03-14 10:49:04', 0, '2026-03-14 09:39:04'),
(3, 'add', 'johnantonyarsuwa1@gmail.com', '$2y$10$8muobh/eCLCUKX5Qj41Noes3Oqj4DAR5GA4wiXp2oOa3cvJZRBQUO', 'college', 'BSIT', 21, '', NULL, '2026-03-14 10:50:45', 1, '2026-03-14 09:40:45'),
(4, 'add', 'johnantonyarsuwa2@gmail.com', '$2y$10$ctLUHZVsby.NgPHvAdZ4K./Sd8s/bFyceHVYAd7h7lJp.FeZkta9e', 'college', 'CRIM', 19, '', NULL, '2026-03-14 14:07:44', 1, '2026-03-14 12:57:44'),
(5, 'add', 'johnantonyarssssuwa2@gmail.com', '$2y$10$NuUolHi9CbJrX47b/nq8deEIxVn19QCFsgZRV5k6UJUp9AmD9uBr6', 'college', 'HM', 17, '', '968491', '2026-03-14 14:16:13', 0, '2026-03-14 13:06:13'),
(6, 'test', 'johnantonyarsuwa3@gmail.com', '$2y$10$1YKymGviTtZ8s5vpxOxw6.Y6..FZ9AC/PoA6Piz7DjzTb66sBtLwa', 'college', 'HM', 2, '', NULL, '2026-03-15 02:00:09', 1, '2026-03-15 00:50:09'),
(7, 'haha', 'haha@gmail.com', '$2y$10$fNq96D3J28nPpH1Lsy/1ZeljzDVanXKxXxvD6ptTW28Rju1Mb41hK', 'highschool', 'STEM', 12, '', NULL, '2026-03-15 12:27:06', 1, '2026-03-15 11:17:06');

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
-- Indexes for table `college_courses`
--
ALTER TABLE `college_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `highschool_strands`
--
ALTER TABLE `highschool_strands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `strand_code` (`strand_code`);

--
-- Indexes for table `program_heads`
--
ALTER TABLE `program_heads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_school_type` (`school_type`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_school` (`previous_school_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `college_courses`
--
ALTER TABLE `college_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `highschool_strands`
--
ALTER TABLE `highschool_strands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `program_heads`
--
ALTER TABLE `program_heads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
