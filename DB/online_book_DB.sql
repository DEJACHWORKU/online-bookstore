-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2025 at 04:54 PM
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
-- Database: `online_book_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `admin_id` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `remember_me` text NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `full_name`, `admin_id`, `email`, `phone`, `username`, `password`, `remember_me`, `profile_image`, `created_at`) VALUES
(1, 'Banch Kasaw Tgabu', '2323BA', 'banchkasaw@gmail.com', '0942326791', 'UGR/143/13', '$2y$10$EJ3xXHWM8TagSGgJ7cnP9OzDtZc4TUaZXRW7kVyJgDvrXujOlmoGi', 'MOM', 'uploads/profile_images/67fb32e49219a.jpg', '2025-04-13 03:58:12'),
(2, 'Adey Kasaw Tgabu', '2303BA', 'adeykasaw@gmail.com', '0943326791', 'UGR/144/13', '$2y$10$sDFXW1V2AlcCpKEyjMn9FO7n2DRM1EC7biEfwZpuGOPMhfI0nnqBS', 'MOM', 'uploads/profile_images/67fb339ec2645.jpg', '2025-04-13 03:58:36');

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`) VALUES
(1, 'Mr alebachew shemse'),
(2, 'kmbj'),
(3, 'Biniam'),
(4, 'ETHIOPIAN EDUCATION ASSOCATION'),
(5, 'Mr. Abebe belay'),
(6, 'Adis Alemayew'),
(7, 'Selomon shumye'),
(8, 'Mark Anstine'),
(9, 'Mr. Maharish bela'),
(10, 'Mr. Michael Marko'),
(11, 'Anvertan Stayin'),
(12, 'Berlin Angelo'),
(13, 'Dr.Andersen alene');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `department` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `cover` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `is_read` tinyint(1) NOT NULL,
  `is_download` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `date`, `title`, `description`, `department`, `author`, `cover`, `file`, `is_read`, `is_download`, `created_at`) VALUES
(2, '2025-04-05', 'Eyewitness', 'Ethiopian wonder full historical book based on true story', 'Art', 'Mr. Abebe belay', 'uploads/covers/67f1c9c9c548e.jpg', 'uploads/files/67f1c9c9c5497.pdf', 1, 0, '2025-04-06 00:24:41'),
(3, '2025-04-06', 'ፍቅር እስከ መቃብር', 'Ethiopian Greate love fiction', 'Jornalist', 'Adis Alemayew', 'uploads/covers/67f1ca4a5e891.jpg', 'uploads/files/67f1ca4a5e896.pdf', 1, 1, '2025-04-06 00:26:50'),
(4, '2025-04-08', 'Civics and Ethical', 'Ethio ethical education book', 'Teaching', 'ETHIOPIAN EDUCATION ASSOCATION', 'uploads/covers/67f1cbcc7d8a8.jpg', 'uploads/files/67f1cbcc7d8ac.pdf', 1, 1, '2025-04-06 00:33:16'),
(5, '2025-04-08', 'Get smart', 'To the advice to taker our life and success way of life', 'philosophy', 'Mark Anstine', 'uploads/covers/67f1ccc383cf5.jpg', 'uploads/files/67f1ccc383cfa.pdf', 1, 0, '2025-04-06 00:37:23'),
(6, '2025-04-09', 'TIME TOP', 'How to use our time the best accountant book', 'IT', 'Mr. Maharish bela', 'uploads/covers/67f1cddcdf76b.jpg', 'uploads/files/67f1cddcdf76f.pdf', 1, 1, '2025-04-06 00:42:04'),
(7, '2025-04-12', 'Buisness plan', 'How to use our time and how to work and invest our money', 'Political science', 'Mr. Michael Marko', 'uploads/covers/67f1ce5329559.jpg', 'uploads/files/67f1ce532955e.pdf', 0, 1, '2025-04-06 00:44:03'),
(8, '2025-04-18', 'Electrical power', 'based on the international research to explain Detail about Electrical power', 'Electrical engineer', 'Anvertan Stayin', 'uploads/covers/67f1cf0065c5e.jpg', 'uploads/files/67f1cf0065c63.pdf', 1, 0, '2025-04-06 00:46:56'),
(9, '2025-04-13', 'How-to-Talk-to-Anyone', 'How-to-Talk-to-Anyone psychological book', 'philosophy', 'Berlin Angelo', 'uploads/covers/67f1cf5ec0e5e.jpg', 'uploads/files/67f1cf5ec0e65.pdf', 0, 1, '2025-04-06 00:48:30'),
(10, '2025-04-09', 'Dopamine', 'Health science book about health protection', 'Health', 'Dr.Andersen alene', 'uploads/covers/67f1cfd042dba.jpg', 'uploads/files/67f1cfd042dc1.pdf', 1, 1, '2025-04-06 00:50:24'),
(13, '2025-05-07', 'BIOLOGY omm', 'fdh;flfhfbafifdbfu;isfkughw', 'Electrical', 'Biniam', 'Uploads/covers/681b7365bb817.jpg', 'Uploads/files/681b7365bb81e.pdf', 1, 0, '2025-05-07 14:51:34');

-- --------------------------------------------------------

--
-- Table structure for table `book_ratings`
--

CREATE TABLE `book_ratings` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Art'),
(2, 'CS'),
(3, 'Economics'),
(4, 'Electrical'),
(5, 'English'),
(6, 'Health'),
(7, 'IT'),
(8, 'Jornalist'),
(9, 'philosophy'),
(10, 'Political '),
(11, 'Teaching');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `librarian`
--

CREATE TABLE `librarian` (
  `id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `personal_id` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `remember_me` text NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `librarian`
--

INSERT INTO `librarian` (`id`, `full_name`, `personal_id`, `email`, `phone`, `username`, `password`, `remember_me`, `profile_image`, `created_at`) VALUES
(4, 'DAGI MAN WOLF', 'UGR/101023/17', 'dagiman2116@gmaail.com', '0998564323', 'DAGI1234', '$2y$10$6.DiLw/m.03llbVvcSdIQeZ2LHyIxf/ZWzamYxAFcdQye/rOTD9Xi', 'MOM', 'Uploads/profile_images/681b73138d2fa.jpg', '2025-05-07 14:49:55');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `availability` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `expiry_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `academic_year` varchar(4) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `year` enum('1st','2nd','3rd','4th','5th','6th','7th') NOT NULL,
  `semester` enum('1st','2nd') NOT NULL,
  `phone` varchar(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `remember_me` varchar(100) NOT NULL,
  `access_permission` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `date`, `academic_year`, `full_name`, `id_number`, `department`, `year`, `semester`, `phone`, `username`, `password`, `profile_image`, `remember_me`, `access_permission`, `created_at`) VALUES
(6, '2025-04-12', '2025', 'Banch Worku Tasew', 'UGR/10143/13', 'CS', '4th', '1st', '0997785658', 'UGR/10143/13', '$2y$10$S6q.Xi0Zc3CqSrWODFtfTeBFudfiE/jwbl9zwBp5kzZ3b/M8JV5Vm', 'users/user_67fa40509c4ec.jpg', 'GOOD DAY', '2 Months', '2025-04-12 10:28:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_ratings`
--
ALTER TABLE `book_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book_id` (`book_id`,`user_id`),
  ADD KEY `user_id_fk` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `librarian`
--
ALTER TABLE `librarian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `book_ratings`
--
ALTER TABLE `book_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `librarian`
--
ALTER TABLE `librarian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book_ratings`
--
ALTER TABLE `book_ratings`
  ADD CONSTRAINT `book_id_fk` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
