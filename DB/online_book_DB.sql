-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2025 at 08:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `date`, `title`, `description`, `department`, `author`, `cover`, `file`, `is_read`, `is_download`, `created_at`) VALUES
(62, '2025-04-05', 'Eyewitness', 'Ethiopian wonder full historical book based on true story', 'Art', 'Mr. Abebe belay', 'uploads/covers/67f1c9c9c548e.jpg', 'uploads/files/67f1c9c9c5497.pdf', 1, 0, '2025-04-06 00:24:41'),
(63, '2025-04-06', 'ፍቅር እስከ መቃብር', 'Ethiopian Greate love fiction', 'Jornalist', 'Adis Alemayew', 'uploads/covers/67f1ca4a5e891.jpg', 'uploads/files/67f1ca4a5e896.pdf', 1, 1, '2025-04-06 00:26:50'),
(64, '2025-04-07', 'ገመና 3', 'good life learner book for reader', 'philosophy', 'Selomon shumye', 'uploads/covers/67f1cae3b9168.jpg', 'uploads/files/67f1cae3b916d.pdf', 1, 0, '2025-04-06 00:29:23'),
(65, '2025-04-08', 'Civics and Ethical', 'Ethio ethical education book', 'Teaching', 'ETHIOPIAN EDUCATION ASSOCATION', 'uploads/covers/67f1cbcc7d8a8.jpg', 'uploads/files/67f1cbcc7d8ac.pdf', 1, 1, '2025-04-06 00:33:16'),
(66, '2025-04-08', 'Get smart', 'To the advice to taker our life and success way of life', 'philosophy', 'Mark Anstine', 'uploads/covers/67f1ccc383cf5.jpg', 'uploads/files/67f1ccc383cfa.pdf', 1, 0, '2025-04-06 00:37:23'),
(67, '2025-04-09', 'TIME TOP', 'How to use our time the best accountant book', 'IT', 'Mr. Maharish bela', 'uploads/covers/67f1cddcdf76b.jpg', 'uploads/files/67f1cddcdf76f.pdf', 1, 1, '2025-04-06 00:42:04'),
(68, '2025-04-12', 'Buisness plan', 'How to use our time and how to work and invest our money', 'Political science', 'Mr. Michael Marko', 'uploads/covers/67f1ce5329559.jpg', 'uploads/files/67f1ce532955e.pdf', 0, 1, '2025-04-06 00:44:03'),
(69, '2025-04-18', 'Electrical power', 'based on the international research to explain Detail about Electrical power', 'Electrical engineer', 'Anvertan Stayin', 'uploads/covers/67f1cf0065c5e.jpg', 'uploads/files/67f1cf0065c63.pdf', 1, 0, '2025-04-06 00:46:56'),
(70, '2025-04-13', 'How-to-Talk-to-Anyone', 'How-to-Talk-to-Anyone psychological book', 'philosophy', 'Berlin Angelo', 'uploads/covers/67f1cf5ec0e5e.jpg', 'uploads/files/67f1cf5ec0e65.pdf', 0, 1, '2025-04-06 00:48:30'),
(71, '2025-04-09', 'Dopamine', 'Health science book about health protection', 'Health', 'Dr.Andersen alene', 'uploads/covers/67f1cfd042dba.jpg', 'uploads/files/67f1cfd042dc1.pdf', 1, 1, '2025-04-06 00:50:24');

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

--
-- Dumping data for table `book_ratings`
--

INSERT INTO `book_ratings` (`id`, `book_id`, `user_id`, `rating`, `created_at`) VALUES
(1, 69, 4, 1, '2025-04-12 17:04:09'),
(2, 65, 4, 5, '2025-04-12 17:04:34'),
(3, 71, 4, 5, '2025-04-12 17:18:43'),
(4, 70, 4, 4, '2025-04-12 17:19:12'),
(5, 69, 5, 4, '2025-04-12 17:19:57'),
(6, 70, 5, 5, '2025-04-12 17:26:03'),
(7, 68, 5, 5, '2025-04-12 17:30:34'),
(8, 67, 5, 5, '2025-04-12 17:31:13'),
(9, 65, 5, 5, '2025-04-12 17:40:18'),
(10, 71, 5, 5, '2025-04-12 17:41:32'),
(11, 66, 5, 5, '2025-04-12 17:43:33'),
(12, 64, 5, 5, '2025-04-12 17:44:29'),
(13, 63, 5, 5, '2025-04-12 17:46:30'),
(14, 62, 5, 5, '2025-04-12 17:48:21'),
(15, 68, 4, 4, '2025-04-12 17:51:25'),
(16, 67, 4, 3, '2025-04-12 17:51:53'),
(17, 66, 4, 3, '2025-04-12 18:02:11');

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

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `full_name`, `username`, `department`, `subject`, `message`, `date`) VALUES
(14, 'dejach worku', 'ugr/10343/13', 'IT', 'about book', 'all book Greate continue on this', '2025-04-06 06:05:02'),
(15, 'dejach worku', 'ugr/10343/13', 'IT', 'about book', 'all book Greate continue on this', '2025-04-06 06:05:07');

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
(13, 'DAGIM WORKU TASSEW', 'UGR/143/13', 'dagiman216@gmail.com', '0987654321', 'DAgi4356', '$2y$10$wpL6yMRIFIqf65Q0yO2axu681esPzQcjCcgI3/rj1B1FWk4vi95Qu', 'GOOD DAY', 'uploads/profile_images/67f5539b45279.jpg', '2025-04-08 16:49:31'),
(14, 'DAGIM WORKU TAEW', 'UGR/10343/13', 'dagiman16@gmail.com', '9089765479', 'D$#@768', '$2y$10$om1nCUxj2c584mVLen5Gk.KZJRy4rAc7LlyJd9gsAdqQINz2WrJNG', 'GOOD DAY', 'uploads/profile_images/67f558c82b396.jpg', '2025-04-08 17:11:36'),
(15, 'DAGIM WORKU TSEW', 'UGR/1043/13', 'dagiman1@gmail.com', '9087654356', 'D$#@68', '$2y$10$nBgZ2itIKYmUasxqpB48f.mW9PKHZohd2BL5XDLxVhu1/OKS/NyyK', 'GOOD DAY', 'uploads/profile_images/67f5591994f40.jpg', '2025-04-08 17:12:57'),
(16, 'DAGIM RKU TSEW', 'UGR/343/13', 'dagiman160@gmail.com', '9809897609', 'D$#768', '$2y$10$p9KYoRxWwoBU14KFSnJCde81FBGf0hC9Nhg7DhNqFgD8HaMkmGmZm', 'GOOD DAY', '', '2025-04-08 17:14:03'),
(17, 'DIM WORKU TSEW', 'UGR/1030043/13', 'dagiman1600@gmail.com', '098976543', 'D$#@76878', '$2y$10$c4iOX7Wzh1RDWSHqiiTfHezEVpIr79k041n/0CbbuMfwisEyOJZM.', 'GOOD DAY', '', '2025-04-08 17:14:44');

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
(4, '2025-04-12', '2025', 'Dejach Worku Tasew', 'UGR/10343/13', 'CS', '4th', '2nd', '0995785658', 'UGR/10343/13', '$2y$10$6uaT0jCXiLz8yIktlEcst.6kT2rCaXYE3MnNl.r7rWgTf230TzEqC', 'users/user_67fa3fe7a5ab7.jpg', 'GOOD DAY', '1 Week', '2025-04-12 10:26:47'),
(5, '2025-04-12', '2025', 'Adey Worku Tasew', 'UGR/10043/13', 'CS', '4th', '2nd', '0996785658', 'UGR/10043/13', '$2y$10$e9GHaE1W9wV3Wxsiwfx3uOg227eWOAp0eMttpfURyCFnqzMeQg4ZG', 'users/user_67fa4018eb578.jpg', 'GOOD DAY', '1 Month', '2025-04-12 10:27:37'),
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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book_ratings`
--
ALTER TABLE `book_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
