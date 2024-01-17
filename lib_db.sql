-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2024 at 05:39 AM
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
-- Database: `lib_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `approval_tb`
--

CREATE TABLE `approval_tb` (
  `aid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `status` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blacklst_tb`
--

CREATE TABLE `blacklst_tb` (
  `blid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books_tb`
--

CREATE TABLE `books_tb` (
  `isbn` varchar(15) NOT NULL,
  `bname` varchar(80) NOT NULL,
  `author` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books_tb`
--

INSERT INTO `books_tb` (`isbn`, `bname`, `author`, `category`, `status`) VALUES
('9780307476463', '1Q84', 'Haruki Murakami', 'Drama', 'available'),
('9780553296983', 'The Diary of a Young Girl', 'Anne Frank', 'History', 'unavailable'),
('9780590129275', 'The Little Price', 'Antoine de Saint-Exupery', 'Fantasy', 'unavailable'),
('9781862302884', 'Dance on My Grave', 'Aidan Chambers', 'Young adult', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `lend_tb`
--

CREATE TABLE `lend_tb` (
  `bid` int(11) NOT NULL,
  `isbn` varchar(15) NOT NULL,
  `uid` int(11) NOT NULL,
  `ldate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lend_tb`
--

INSERT INTO `lend_tb` (`bid`, `isbn`, `uid`, `ldate`) VALUES
(6, '9780590129275', 1, '2024-01-17'),
(7, '9780553296983', 1, '2024-01-17');

-- --------------------------------------------------------

--
-- Table structure for table `user_tb`
--

CREATE TABLE `user_tb` (
  `uid` int(11) NOT NULL,
  `fname` varchar(80) NOT NULL,
  `lname` varchar(80) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(200) NOT NULL,
  `type` varchar(80) NOT NULL,
  `ecount` int(11) NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_tb`
--

INSERT INTO `user_tb` (`uid`, `fname`, `lname`, `email`, `pass`, `type`, `ecount`) VALUES
(1, 'Sayaka', 'Maki', 'sayaka@mail.com', 'sayaka', 'customer', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approval_tb`
--
ALTER TABLE `approval_tb`
  ADD PRIMARY KEY (`aid`),
  ADD KEY `uid_cons` (`uid`);

--
-- Indexes for table `blacklst_tb`
--
ALTER TABLE `blacklst_tb`
  ADD PRIMARY KEY (`blid`),
  ADD KEY `uidcons` (`uid`);

--
-- Indexes for table `books_tb`
--
ALTER TABLE `books_tb`
  ADD PRIMARY KEY (`isbn`);

--
-- Indexes for table `lend_tb`
--
ALTER TABLE `lend_tb`
  ADD PRIMARY KEY (`bid`),
  ADD KEY `isbn_cons` (`isbn`),
  ADD KEY `uid__cons` (`uid`);

--
-- Indexes for table `user_tb`
--
ALTER TABLE `user_tb`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approval_tb`
--
ALTER TABLE `approval_tb`
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blacklst_tb`
--
ALTER TABLE `blacklst_tb`
  MODIFY `blid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lend_tb`
--
ALTER TABLE `lend_tb`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_tb`
--
ALTER TABLE `user_tb`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_tb`
--
ALTER TABLE `approval_tb`
  ADD CONSTRAINT `uid_cons` FOREIGN KEY (`uid`) REFERENCES `user_tb` (`uid`);

--
-- Constraints for table `blacklst_tb`
--
ALTER TABLE `blacklst_tb`
  ADD CONSTRAINT `uidcons` FOREIGN KEY (`uid`) REFERENCES `user_tb` (`uid`);

--
-- Constraints for table `lend_tb`
--
ALTER TABLE `lend_tb`
  ADD CONSTRAINT `isbn_cons` FOREIGN KEY (`isbn`) REFERENCES `books_tb` (`isbn`),
  ADD CONSTRAINT `uid__cons` FOREIGN KEY (`uid`) REFERENCES `user_tb` (`uid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
