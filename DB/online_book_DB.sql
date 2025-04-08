-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2025 at 06:07 AM
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
(18, 'INT FAT DFT', 'Ugr/2345/13', 'dagiman216@gmail.com', '0995785658', 'Dagi1234', '$2y$10$j.jzjx6ES3ml4MM6g8lgCuRopndoQltJAHu3mC2Od6AlknstUeq8G', 'mom', 'uploads/profile_images/67f3e4cd74396.jpg', '2025-04-07 14:44:29');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `full_name` varchar(10) NOT NULL,
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
(8, 'INT', '1234bcd/g', 'dagiman216@gmail.com', '0995785658', 'Dagi1234', '$2y$10$JsNHbYBbZBu2OalBob5wfe5yvHJv4LdA8iDSg6I6supVtTikmXnzW', 'mom', 'uploads/profile_images/67f3e4b23ca4f.jpg', '2025-04-07 14:44:02');

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
  `access_permission` enum('1 months','2 months','3 months','4 months','5 months','6 months','7 months','8 months','9 months','10 months','11 months','12 months') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `date`, `academic_year`, `full_name`, `id_number`, `department`, `year`, `semester`, `phone`, `username`, `password`, `profile_image`, `remember_me`, `access_permission`, `created_at`) VALUES
(11, '2025-04-08', '2024', 'Dejch Worku Tasew', 'UGR/10343/13', 'IT', '2nd', '1st', '0995785658', 'Dagi1234', '$2y$10$a0ibVVnN0wqQec9Hm6ce0uw8LNzOhWnKtWzFZSUx/n9a6jgvFOuK6', 'users/user_67f4a03cf0faf.jpg', 'mom', '6 months', '2025-04-08 04:04:13');

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
