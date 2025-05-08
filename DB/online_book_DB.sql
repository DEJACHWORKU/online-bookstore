-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 08:44 PM
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
(2, 'Adey Kasaw Tgabu', '2303BA', 'adeykasaw@gmail.com', '0943326791', 'UGR/144/13', '$2y$10$sDFXW1V2AlcCpKEyjMn9FO7n2DRM1EC7biEfwZpuGOPMhfI0nnqBS', 'MOM', 'uploads/profile_images/67fb339ec2645.jpg', '2025-04-13 03:58:36'),
(3, 'Iftu Berhanu', '11779', 'efi@gmail.com', '09042092658', 'iftu@', '$2y$10$rAb4nrST7xBjScAM22MjHe2C31YkgCLBaGt4YHidkxlv9/F1ahNYi', 'w', 'Uploads/profile_images/681cdec64b029.jpg', '2025-05-08 16:41:42'),
(4, 'tesfaye anshebo', '1234', 'anshe@gmail.come', '0916575888', 'tesfa@', '$2y$10$77OchkMPTelcY3fL9gNIcOXvCrgCqXtbni6ntcPOTQGZTSqS.8PlK', 'y', 'Uploads/profile_images/681ce9c00847e.jpg', '2025-05-08 17:28:32'),
(5, 'ABEBE KEBEDE BELACHEW', '1237', 'dagi@gmail.come', '0909897654', 'DAGI4321', '$2y$10$i/ldnZ1T.jOl1ANDcQMjEeJ124q7lXcC8goM7UsBQwBUzGZF2z1eK', 'MOM', 'Uploads/profile_images/681cefb97ee54.jpg', '2025-05-08 17:54:01');

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
(0, 'iftu'),
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
(15, '2025-05-08', 'FIKR ESKE MEKABR', 'Love of fiction about Ethiopian Country the origin of Ethiopia Gojam Mankusa', 'Art', 'Adis Alemayew', 'Uploads/covers/681b7c3c41265.jpg', 'Uploads/files/681b7c3c41269.pdf', 1, 0, '2025-05-07 15:29:00'),
(16, '2025-05-07', 'Eyewitness', 'Ethiopian population History Living and life process', 'Art', 'Mr alebachew shemse', 'Uploads/covers/681b7c9fa321a.jpg', 'Uploads/files/681b7c9fa3221.pdf', 1, 1, '2025-05-07 15:30:39'),
(17, '2025-05-08', 'Gemena', 'Fiction book about life and social behavior and environmental factor', 'Art', 'Selomon shumye', 'Uploads/covers/681b7d275658a.jpg', 'Uploads/files/681b7d275658d.pdf', 0, 1, '2025-05-07 15:32:55'),
(18, '2025-04-27', 'HOW-to -talk-without fear', 'Psychological book for reader', 'Health', 'Biniam', 'Uploads/covers/681b7dbd86889.jpg', 'Uploads/files/681b7dbd8688d.pdf', 1, 1, '2025-05-07 15:35:25'),
(19, '2025-05-28', 'TOP-100-English question', 'Self-Training for English grammar book', 'English', 'ETHIOPIAN EDUCATION ASSOCATION', 'Uploads/covers/681b7ec8f031a.jpg', 'Uploads/files/681b7ec8f0320.pdf', 1, 0, '2025-05-07 15:39:52'),
(20, '2025-04-27', 'Civics', 'Citizen and civilization for people learn about rule and law', 'Political', 'Dr.Andersen alene', 'Uploads/covers/681b7f51c4afa.jpg', 'Uploads/files/681b7f51c4b01.pdf', 0, 1, '2025-05-07 15:42:09'),
(21, '2025-05-08', 'programming', 'programming', 'Marketing', 'iftu', 'Uploads/covers/681ce2970b166.jpg', 'Uploads/files/681ce2970b16d.pdf', 1, 1, '2025-05-08 16:59:41'),
(22, '2025-05-21', 'java', 'programing', 'IT', 'ETHIOPIAN EDUCATION ASSOCATION', 'Uploads/covers/681ceb22691cb.jpg', 'Uploads/files/681ceb22691d7.pdf', 1, 1, '2025-05-08 17:34:26'),
(23, '2025-05-12', 'Business plan', 'all neccessary book for starrt business', 'Economics', 'Mr. Abebe belay', 'Uploads/covers/681cf38e409b3.jpg', 'Uploads/files/681cf38e409be.pdf', 1, 0, '2025-05-08 18:10:22'),
(24, '2025-05-16', 'get smart', 'all freak book all maind of access of history', 'Economics', 'ETHIOPIAN EDUCATION ASSOCATION', 'Uploads/covers/681cf3eded6c6.jpg', 'Uploads/files/681cf3eded6cd.pdf', 0, 1, '2025-05-08 18:11:57'),
(26, '2025-05-08', 'MUlti media', 'all technology', 'English', 'Mr. Abebe belay', 'Uploads/covers/681cf7a42f591.jpg', 'Uploads/files/681cf7a42f59b.pdf', 1, 0, '2025-05-08 18:27:48'),
(27, '2025-05-16', 'c++', 'all programing book for learn', 'CS', 'Anvertan Stayin', 'Uploads/covers/681cf7e246fa8.jpg', 'Uploads/files/681cf7e246fb0.pdf', 0, 1, '2025-05-08 18:28:50'),
(28, '2025-05-16', 'Internet programing 2', 'all web based book accomplish', 'English', 'Berlin Angelo', 'Uploads/covers/681cf8d738c08.jpg', 'Uploads/files/681cf8d738c0f.pdf', 1, 0, '2025-05-08 18:32:55');

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
(22, 21, 8, 1, '2025-05-08 17:11:07'),
(23, 22, 9, 5, '2025-05-08 17:38:16'),
(24, 19, 6, 5, '2025-05-08 18:44:32');

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
(9, 'Marketing'),
(10, 'philosophy'),
(11, 'Political '),
(12, 'Teaching');

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
(4, 'hsh', 'HDSJDH', 'KJASJabk', 'nsxSDLK,', 'ASJkanbJDUkj', '2025-05-08 18:22:13');

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
(4, 'DAGI MAN WOLF', 'UGR/101023/17', 'dagiman2116@gmaail.com', '0998564323', 'DAGI1234', '$2y$10$6.DiLw/m.03llbVvcSdIQeZ2LHyIxf/ZWzamYxAFcdQye/rOTD9Xi', 'MOM', 'Uploads/profile_images/681b73138d2fa.jpg', '2025-05-07 14:49:55'),
(5, 'Iftu Berhanu', '11779', 'efi@gmail.com', '0904209265', 'iftu@', '$2y$10$21U0VH3wESCiyFxVZsBL2OmtxBz.m8KzrPglq0oo0D44wDitY/c/i', 'Y', 'Uploads/profile_images/681cdf736785f.jpg', '2025-05-08 16:44:35'),
(6, 'ABEBE KEBEDE BELACHEW', '243RF', 'dagi@gmail.com', '09987689095', 'DAGI4321', '$2y$10$xcXt71IYFequs/1eTeEA4emJc1agUQc3g5OqfokWlp54ZRszKvMLC', 'MOM', 'Uploads/profile_images/681ceedf2a50e.jpg', '2025-05-08 17:50:23'),
(7, 'ABEBE KEBDE BELACHEW', '243iF', 'daii@gmail.com', '0998768903', 'DAGg1234', '$2y$10$DiE5sr8R7ikln9DzWJVgCebRXXe.IFl0NHCexhFGHX8ehHizT8X9G', 'MOM', 'Uploads/profile_images/681cf0c931899.jpg', '2025-05-08 17:58:33');

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `book_id`, `availability`, `created_at`, `expiry_date`) VALUES
(0, 19, '1week', '2025-05-07 20:43:14', '2025-05-07 17:43:14'),
(0, 21, '1day', '2025-05-08 20:18:19', '2025-05-08 17:18:19'),
(0, 22, '1day', '2025-05-08 20:35:03', '2025-05-08 17:35:03');

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
(6, '2025-04-12', '2025', 'Banch Worku Tasew', 'UGR/10143/13', 'CS', '4th', '1st', '0997785658', 'UGR/10143/13', '$2y$10$S6q.Xi0Zc3CqSrWODFtfTeBFudfiE/jwbl9zwBp5kzZ3b/M8JV5Vm', 'users/user_67fa40509c4ec.jpg', 'GOOD DAY', '2 Months', '2025-04-12 10:28:32'),
(8, '2025-05-08', '2025', 'Iftu Berhanu', '11779', 'Marketing', '2nd', '2nd', '0904209265', 'iftu@', '$2y$10$vg9SX4tIZkOkeK9nJwC0t.6a2oDZkDABrVIR0KBcJmuOlMZ5RQvWe', 'users/user_681ce099191f4.jpg', 'w', '7 Months', '2025-05-08 16:49:29'),
(9, '2025-05-21', '2017', 'Tesfaye Anshebo', '11147', 'it', '4th', '2nd', '0916575888', 'tesfa@', '$2y$10$HeshZ2sEcnVLY3Jp0wvGn.pE2Fz8zNoiFXkHyHSVIkM1Xvp1hGva.', 'users/user_681cea7fc68e9.jpg', 'y', '1 Month', '2025-05-08 17:31:43'),
(11, '2025-05-08', '2023', 'Mekwant Liyew Ashagre', 'UGR/1432/12', 'CS', '4th', '2nd', '0910670058', 'MEKE1234', '$2y$10$NDFC2HoWReOMMrNIXmHrWOSe0Hv/9Hy2yuAAl9fOfH5S4GP1aT04S', 'users/user_681cf2aba0cef.jpg', 'FRIEND', '1 Month', '2025-05-08 18:06:35');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `book_ratings`
--
ALTER TABLE `book_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `librarian`
--
ALTER TABLE `librarian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
