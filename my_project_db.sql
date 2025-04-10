-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 01:30 PM
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
-- Database: `my_project_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `claim`
--

CREATE TABLE `claim` (
  `claim_id` int(11) NOT NULL,
  `lost_id` int(11) DEFAULT NULL,
  `found_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `date_claimed` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `found_item`
--

CREATE TABLE `found_item` (
  `found_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `date_found` date DEFAULT NULL,
  `status` enum('unclaimed','claimed') DEFAULT 'unclaimed',
  `reporter_name` varchar(255) NOT NULL,
  `reporter_email` varchar(255) NOT NULL,
  `reporter_phone` varchar(20) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `found_item`
--

INSERT INTO `found_item` (`found_id`, `user_id`, `category`, `description`, `location`, `date_found`, `status`, `reporter_name`, `reporter_email`, `reporter_phone`, `image`, `created_at`) VALUES
(1, 0, 'Books', 'dadadasdsa', 'dasdasdas', '2025-04-15', '', 'adddsa', 'sdf@gmail.com', '09272739668', '../uploads/Oak Essentials Balancing Mist at Nordstrom, Size 3_4 Oz.jfif', '2025-04-09 10:10:50'),
(2, 0, 'Electronics', 'dasdasdsa', 'dsadsadasdsa', '2025-04-19', '', 'Jodi', 'adadsnjsdhj@gmail.com', '09272739668', '../uploads/jr sped logo_colored.png', '2025-04-09 10:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `lost_item`
--

CREATE TABLE `lost_item` (
  `lost_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `date_lost` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `reporter_name` varchar(255) NOT NULL,
  `reporter_email` varchar(255) NOT NULL,
  `reporter_phone` varchar(20) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lost_item`
--

INSERT INTO `lost_item` (`lost_id`, `user_id`, `category`, `description`, `location`, `date_lost`, `status`, `reporter_name`, `reporter_email`, `reporter_phone`, `image`) VALUES
(6, 0, 'Animal', 'Cat, fluffy', 'heb', '2025-04-10', 'pending', 'Jodi', 'adadsnjsdhj@gmail.com', '09272739668', '../uploads/1744194746_cute cat 😺.jfif'),
(7, 0, 'Animal', 'daadad', 'heb', '2025-04-05', 'pending', 'sfd', 'adadsnjsdhj@gmail.com', '09272739668', '../uploads/1744194772_cat in the blanket.jfif'),
(8, 0, 'Animal', 'fasfafa', 'fdsff', '2025-04-16', 'pending', 'fafsa', 'adadsnjsdhj@gmail.com', '09272739668', '../uploads/1744194859_50+ Pet Tricks you don\'t known _ Animal Hacks.jfif'),
(9, 0, 'Clothing', 'Hatdog', 'HEB', '2025-04-15', 'pending', 'Jodi', 'adadsnjsdhj@gmail.com', '09272739668', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `role`) VALUES
(0, 'jods', 'jodinicoledivinagracia@gmail.com', '$2y$10$bA2aTXLF1vh0uc7g1mWAP.ZrmHY./D.PfiPW6nrWb6jsDcWtKlRe6', 'user'),
(0, 'nikol', 'adhdjhajdh@gmail.com', '$2y$10$2NP2XAExAQ7Lo4dyuDPeo.IcSMHmEYN8yn7Mg1ts8GQsneY1Ijy4a', 'user'),
(0, 'nikol', 'michaeld@gmail.com', '$2y$10$rEbDblD2FFJ0DFlXi32pY.vK/VhQL1ZRN1NB3fyPah5fE6fy2m6hi', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `claim`
--
ALTER TABLE `claim`
  ADD PRIMARY KEY (`claim_id`),
  ADD UNIQUE KEY `unique_claim` (`found_id`,`user_id`,`status`),
  ADD KEY `lost_id` (`lost_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `found_item`
--
ALTER TABLE `found_item`
  ADD PRIMARY KEY (`found_id`),
  ADD UNIQUE KEY `unique_found_item` (`category`,`description`,`location`,`date_found`,`user_id`) USING HASH,
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lost_item`
--
ALTER TABLE `lost_item`
  ADD PRIMARY KEY (`lost_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `claim`
--
ALTER TABLE `claim`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `found_item`
--
ALTER TABLE `found_item`
  MODIFY `found_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lost_item`
--
ALTER TABLE `lost_item`
  MODIFY `lost_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
