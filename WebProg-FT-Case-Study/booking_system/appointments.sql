-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2024 at 02:19 PM
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
-- Database: `booking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `office_window` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('available','booked') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `office_window`, `appointment_date`, `appointment_time`, `status`) VALUES
(1, 'Window 1', '2024-11-21', '09:00:00', 'booked'),
(2, 'Window 2', '2024-11-21', '08:00:00', 'available'),
(3, 'Window 3', '2024-11-21', '09:00:00', 'available'),
(4, 'Window 1', '2024-11-25', '10:00:00', 'booked'),
(5, 'Window 1', '2024-11-25', '11:00:00', 'available'),
(6, 'Window 2', '2024-11-25', '12:00:00', 'booked'),
(7, 'Window 2', '2024-11-25', '13:00:00', 'booked'),
(8, 'Window 1', '2024-11-23', '09:00:00', 'booked'),
(9, 'Window 1', '2024-11-23', '10:00:00', 'available'),
(10, 'Window 1', '2024-11-23', '11:00:00', 'available'),
(11, 'Window 1', '2024-11-23', '13:00:00', 'available'),
(12, 'Window 2', '2024-11-23', '09:00:00', 'available'),
(13, 'Window 2', '2024-11-23', '10:00:00', 'available'),
(14, 'Window 2', '2024-11-23', '11:00:00', 'available'),
(15, 'Window 3', '2024-11-24', '09:00:00', 'available'),
(16, 'Window 3', '2024-11-24', '10:00:00', 'available'),
(17, 'Window 3', '2024-11-24', '11:00:00', 'available'),
(18, 'Window 4', '2024-11-24', '09:00:00', 'available'),
(19, 'Window 4', '2024-11-24', '10:00:00', 'available'),
(20, 'Window 4', '2024-11-24', '11:00:00', 'available'),
(21, 'Window 5', '2024-11-23', '09:00:00', 'available'),
(22, 'Window 5', '2024-11-23', '10:00:00', 'available'),
(23, 'Window 5', '2024-11-23', '11:00:00', 'available'),
(24, 'Window 5', '2024-11-23', '13:00:00', 'available'),
(25, 'Window 6', '2024-11-23', '09:00:00', 'available'),
(26, 'Window 6', '2024-11-23', '10:00:00', 'available'),
(27, 'Window 6', '2024-11-23', '11:00:00', 'available'),
(28, 'Window 6', '2024-11-24', '09:00:00', 'available'),
(29, 'Window 7', '2024-11-24', '10:00:00', 'available'),
(30, 'Window 7', '2024-11-24', '11:00:00', 'available'),
(31, 'Window 8', '2024-11-24', '09:00:00', 'available'),
(32, 'Window 6', '2024-11-24', '10:00:00', 'available'),
(33, 'Window 9', '2024-11-24', '11:00:00', 'available'),
(34, 'Window 9', '2024-11-23', '09:00:00', 'available'),
(35, 'Window 8', '2024-11-23', '10:00:00', 'available'),
(36, 'Window 7', '2024-11-23', '11:00:00', 'available'),
(37, 'Window 10', '2024-11-23', '13:00:00', 'available'),
(38, 'Window 12', '2024-11-23', '09:00:00', 'available'),
(39, 'Window 12', '2024-11-23', '10:00:00', 'available'),
(40, 'Window 12', '2024-11-23', '11:00:00', 'available'),
(41, 'Window 13', '2024-11-24', '09:00:00', 'available'),
(42, 'Window 13', '2024-11-24', '10:00:00', 'available'),
(43, 'Window 13', '2024-11-24', '11:00:00', 'available'),
(44, 'Window 14', '2024-11-24', '09:00:00', 'booked'),
(45, 'Window 14', '2024-11-24', '10:00:00', 'booked'),
(46, 'Window 14', '2024-11-24', '11:00:00', 'booked'),
(47, 'Window 15', '2024-11-23', '09:00:00', 'available'),
(48, 'Window 11', '2024-11-23', '10:00:00', 'available'),
(49, 'Window 11', '2024-11-23', '11:00:00', 'available'),
(50, 'Window 11', '2024-11-23', '13:00:00', 'available'),
(51, 'Admission Unit', '2024-11-23', '09:00:00', 'available'),
(52, 'Admission Unit', '2024-11-23', '10:00:00', 'available'),
(53, 'Admission Unit', '2024-11-23', '11:00:00', 'available'),
(54, 'Admission Unit', '2024-11-24', '09:00:00', 'available'),
(55, 'Admission Unit', '2024-11-24', '10:00:00', 'available'),
(56, 'Admission Unit', '2024-11-24', '11:00:00', 'available'),
(57, 'Directors Office', '2024-11-24', '09:00:00', 'available'),
(58, 'Directors Office', '2024-11-24', '10:00:00', 'available'),
(59, 'Directors Office', '2024-11-24', '11:00:00', 'available'),
(60, 'Admission Unit', '2024-11-23', '09:30:00', 'booked');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
