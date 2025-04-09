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
(20, 'DAGIM WORKU TASSEW', 'UGR/105434/43', 'dagiman216@gmail.com', '0998989898', 'DAgi4356', '$2y$10$l3dPnzZ7FQwcH6NsbfLALuL4YhSAixk1RJ9.Jh64WXWpXsGiq1cdO', 'GOOD DAY', 'uploads/profile_images/67f547c90a1b2.jpg', '2025-04-08 15:59:05'),
(21, 'DAGIM RKU TASSEW', 'UGR/1434/43', 'dagiman16@gmail.com', '0998989809', 'D@#4356', '$2y$10$aKBJ2nULm4DBl.X9A934G.KQO0T43b.GtbZSrUWMIr0Tt/jgX9Rke', 'GOOD DAY', 'uploads/profile_images/67f55a8b3499a.jpg', '2025-04-08 17:19:07');

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
  `id` int(11) UNSIGNED NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_ratings`
--

INSERT INTO `book_ratings` (`id`, `book_id`, `user_id`, `rating`, `created_at`) VALUES
(1, 70, 1, 2, '2025-04-09 09:49:53'),
(3, 62, 1, 3, '2025-04-09 09:57:38'),
(4, 62, 1, 3, '2025-04-09 10:06:32'),
(5, 70, 1, 5, '2025-04-09 10:06:39'),
(6, 70, 1, 5, '2025-04-09 10:06:59'),
(7, 70, 1, 5, '2025-04-09 10:07:05'),
(8, 70, 1, 5, '2025-04-09 10:07:12'),
(9, 70, 1, 5, '2025-04-09 10:07:17'),
(10, 70, 1, 5, '2025-04-09 10:07:23'),
(11, 70, 1, 5, '2025-04-09 10:07:29'),
(12, 70, 1, 3, '2025-04-09 10:07:34'),
(13, 70, 1, 5, '2025-04-09 10:07:39'),
(14, 69, 1, 5, '2025-04-09 10:10:57');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Art'),
(2, 'CS'),
(3, 'Economics'),
(4, 'Electrical engineer'),
(5, 'English'),
(6, 'Health'),
(7, 'IT'),
(8, 'Jornalist'),
(9, 'philosophy'),
(10, 'Political science'),
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
  `id` int(11) UNSIGNED NOT NULL,
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
(3, '2025-04-09', '2034', 'Salamalak Kasaw Tgabu', 'UGR/12387/98', 'IT', '3rd', '1st', '0997867564', 'SALEa#2', '$2y$10$Pq0RSA.3c.cmqC3wm2jbJefHGThyOqq.tD3biW9pZNkDTXDS6WmY2', 'users/user_67f651d29a763.jpg', '1234567', '1 Week', '2025-04-09 10:54:10'),
(4, '2025-04-09', '2023', 'Salamal Kasaw Tgabu', 'UGR/1237/98', 'IT', '5th', '1st', '0990967564', 'SALEa#@', '$2y$10$aySiGCkxaId.qsGLVyPUreHe77tjJrN5rdolapWOIMD5HnRvc6n.W', 'users/user_67f6520befea3.jpg', 'MOM', '1 Month', '2025-04-09 10:55:08'),
(5, '2025-04-16', '2034', 'Salama Kasaw Tgabu', 'UGR/12309/98', 'cs', '3rd', '2nd', '0989867564', 'SALEa#2##', '$2y$10$YoMwqfNZJndujq8XAEURGujBbDVqnfp76eXNrflZqb2ySQmXpWl/.', 'users/user_67f6524900333.jpg', 'good', '4 Months', '2025-04-09 10:56:09');

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
  ADD KEY `fk_book_id` (`book_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`name`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `librarian`
--
ALTER TABLE `librarian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD UNIQUE KEY `expiry_date` (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `book_ratings`
--
ALTER TABLE `book_ratings`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `librarian`
--
ALTER TABLE `librarian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book_ratings`
--
ALTER TABLE `book_ratings`
  ADD CONSTRAINT `fk_book_id` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
