-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2025 at 05:28 AM
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
-- Database: `bd-ventas`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `oauth_google_id` varchar(255) DEFAULT NULL COMMENT 'ID de usuario de Google',
  `oauth_provider` varchar(50) DEFAULT NULL COMMENT 'Proveedor OAuth (google, facebook, github, etc.)',
  `oauth_created_at` timestamp NULL DEFAULT NULL COMMENT 'Fecha de creación de la cuenta OAuth',
  `oauth_updated_at` timestamp NULL DEFAULT NULL COMMENT 'Fecha de última actualización de OAuth'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `oauth_google_id`, `oauth_provider`, `oauth_created_at`, `oauth_updated_at`) VALUES
(9, 'die90', '$2y$10$RJ8Xf5NlaujUj8ues6xUL.9J6ed7CY0lWhLvYvpbs46GaitMI1Cpa', 'ramoscortez@gmail.com', NULL, NULL, NULL, NULL),
(10, 'fabri1907', '$2y$10$/Y/kSfk.A0kqFudjMGjjquElTid0V.05AKOOJvfsnJhLb61JK9pEy', 'fabricio@gmail.com', NULL, NULL, NULL, NULL),
(11, 'ramos20', '$2y$10$FrpT6GuZX6dnj5jpM8PEbeanxOqcSaeIX5qrrgYo4Kj.iCcrel7JG', 'ramos20@gmail.com', NULL, NULL, NULL, NULL),
(12, 'fabricio2025', '$2y$10$vV7vPxvCmPYnLioYf/6xKuh0GHhIrWyJ8zj.FdbwYY5QAl/9MCDQa', 'fabricioramos@gmail.com', NULL, NULL, NULL, NULL),
(13, '2411070173@undc.edu.pe', '$2y$10$1tNPiVksGUlFKpviONtLw.XTrdmfbzVR.jyens/I6tZERZQxONX7q', '2411070173@undc.edu.pe', '106334840604627987548', 'google', '2025-11-26 04:27:29', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `oauth_google_id` (`oauth_google_id`),
  ADD KEY `idx_oauth_google_id` (`oauth_google_id`),
  ADD KEY `idx_oauth_provider` (`oauth_provider`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
