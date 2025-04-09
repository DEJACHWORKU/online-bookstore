-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2025 at 06:23 PM
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
-- Database: `attendance`
--

-- --------------------------------------------------------

--
-- Table structure for table `admininfo`
--

CREATE TABLE `admininfo` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admininfo`
--

INSERT INTO `admininfo` (`id`, `username`, `password`) VALUES
(0, 'admin', '123456');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

CREATE TABLE `attendance_records` (
  `id` int(11) NOT NULL,
  `employee_record_number` varchar(5) NOT NULL,
  `date` date NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_records`
--

INSERT INTO `attendance_records` (`id`, `employee_record_number`, `date`, `employee_id`, `full_name`, `position`) VALUES
(1, '1', '2024-12-23', 'YF898', 'ASTER ANDARGE BELACHEW', 'SELEER'),
(8, '2', '2025-02-24', 'YA432', 'ADANE TEKA', 'Technician'),
(9, '3', '2025-03-12', 'YA433', 'BELACHEW TEKA', 'Technician'),
(10, '4', '2025-03-10', 'YA434', 'YONAS  TEKALGN', 'mechanic'),
(11, '5', '2025-03-04', 'YA435', 'YIRGA TEKLE TSADIK', 'mechanic'),
(12, '6', '2025-03-04', 'YA436', 'BINIAM ANTENEH', 'Driver'),
(13, '7', '2025-02-23', 'YA437', 'SHAMBEL ALEBEL', 'accountant'),
(14, '8', '2025-02-25', 'YA438', 'ALEBEL ADANE', 'HRM'),
(15, '9', '2025-02-25', 'YA439', 'TADYOS ADANE', 'presdent'),
(16, '10', '2025-02-23', 'YA4310', 'YARED ABEBE', 'Advisor'),
(17, '11', '2025-03-17', 'YA4311', 'TIGST MOLALGN', 'secretary');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(10) NOT NULL,
  `number` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_id`, `full_name`, `date`, `status`, `number`) VALUES
(17, 'YF898', 'ASTER ANDARGE BELACHEW', '2024-12-25', 'Absent', 1),
(18, 'YS043', 'ABEBE WORKU TASSEW', '2024-12-25', 'Present', 7),
(19, 'YF342', 'DAGI WORKU AYTENEW', '2024-12-25', 'Absent', 3),
(20, 'YF34209', 'BELACHEW WORKU AYTENEW', '2024-12-25', 'Absent', 3),
(21, 'YA45', 'MIKA', '2024-12-25', 'Present', 4),
(22, 'YF898', 'ASTER ANDARGE BELACHEW', '2024-12-11', 'Present', 1),
(23, 'YS043', 'ABEBE WORKU TASSEW', '2024-12-11', 'Present', 7),
(24, 'YF342', 'DAGI WORKU AYTENEW', '2024-12-11', 'Absent', 3),
(25, 'YF34209', 'BELACHEW WORKU AYTENEW', '2024-12-11', 'Present', 3),
(26, 'YA45', 'MIKA', '2024-12-11', 'Absent', 4),
(27, 'YF898', 'ASTER ANDARGE BELACHEW', '2024-12-25', 'Absent', 1),
(28, 'YS043', 'ABEBE WORKU TASSEW', '2024-12-25', 'Present', 7),
(29, 'YF342', 'DAGI WORKU AYTENEW', '2024-12-25', 'Present', 3),
(30, 'YF34209', 'BELACHEW WORKU AYTENEW', '2024-12-25', 'Present', 3),
(31, 'YA45', 'MIKA', '2024-12-25', 'Present', 4),
(32, 'YF898', 'ASTER ANDARGE BELACHEW', '2025-01-04', 'Present', 1),
(33, 'YS043', 'ABEBE WORKU TASSEW', '2025-01-04', 'Absent', 7),
(34, 'YF342', 'DAGI WORKU AYTENEW', '2025-01-04', 'Absent', 3),
(35, 'YF34209', 'BELACHEW WORKU AYTENEW', '2025-01-04', 'Absent', 3),
(36, 'YA45', 'MIKA', '2025-01-04', 'Present', 4),
(37, 'YA4563', 'KETEMA DEBA MESERET', '2025-01-04', 'Present', 21),
(38, 'YF898', 'ASTER ANDARGE BELACHEW', '2025-03-18', 'Present', 1),
(39, 'YA432', 'ADANE TEKA', '2025-03-18', 'Absent', 2),
(40, 'YA433', 'BELACHEW TEKA', '2025-03-18', 'Absent', 3),
(41, 'YA434', 'YONAS  TEKALGN', '2025-03-18', 'Absent', 4),
(42, 'YA435', 'YIRGA TEKLE TSADIK', '2025-03-18', 'Absent', 5),
(43, 'YA436', 'BINIAM ANTENEH', '2025-03-18', 'Present', 6),
(44, 'YA437', 'SHAMBEL ALEBEL', '2025-03-18', 'Present', 7),
(45, 'YA438', 'ALEBEL ADANE', '2025-03-18', 'Absent', 8),
(46, 'YA439', 'TADYOS ADANE', '2025-03-18', 'Absent', 9),
(47, 'YA4310', 'YARED ABEBE', '2025-03-18', 'Present', 10),
(48, 'YA4311', 'TIGST MOLALGN', '2025-03-18', 'Present', 11);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admininfo`
--
ALTER TABLE `admininfo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
