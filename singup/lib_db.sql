-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2024 at 04:22 AM
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
  `sid` int(11) NOT NULL,
  `status` varchar(50) NOT NULL
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
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers_tb`
--

CREATE TABLE `customers_tb` (
  `cid` int(11) NOT NULL,
  `fname` varchar(80) NOT NULL,
  `lname` varchar(80) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(200) NOT NULL,
  `type` varchar(80) NOT NULL,
  `ecount` int(11) NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lend_tb`
--

CREATE TABLE `lend_tb` (
  `bid` int(11) NOT NULL,
  `isbn` varchar(15) NOT NULL,
  `cid` int(11) NOT NULL,
  `ldate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_tb`
--

CREATE TABLE `staff_tb` (
  `sid` int(11) NOT NULL,
  `fname` varchar(80) NOT NULL,
  `lname` varchar(80) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(200) NOT NULL,
  `type` varchar(80) NOT NULL,
  `ecount` int(11) NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approval_tb`
--
ALTER TABLE `approval_tb`
  ADD PRIMARY KEY (`aid`),
  ADD KEY `sid_cons` (`sid`);

--
-- Indexes for table `blacklst_tb`
--
ALTER TABLE `blacklst_tb`
  ADD PRIMARY KEY (`blid`),
  ADD KEY `uid_cons2` (`uid`);

--
-- Indexes for table `books_tb`
--
ALTER TABLE `books_tb`
  ADD PRIMARY KEY (`isbn`);

--
-- Indexes for table `customers_tb`
--
ALTER TABLE `customers_tb`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `lend_tb`
--
ALTER TABLE `lend_tb`
  ADD PRIMARY KEY (`bid`),
  ADD KEY `isbn_cons` (`isbn`),
  ADD KEY `cid_cons` (`cid`);

--
-- Indexes for table `staff_tb`
--
ALTER TABLE `staff_tb`
  ADD PRIMARY KEY (`sid`);

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
-- AUTO_INCREMENT for table `customers_tb`
--
ALTER TABLE `customers_tb`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lend_tb`
--
ALTER TABLE `lend_tb`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_tb`
--
ALTER TABLE `staff_tb`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_tb`
--
ALTER TABLE `approval_tb`
  ADD CONSTRAINT `sid_cons` FOREIGN KEY (`sid`) REFERENCES `staff_tb` (`sid`);

--
-- Constraints for table `blacklst_tb`
--
ALTER TABLE `blacklst_tb`
  ADD CONSTRAINT `uid_cons1` FOREIGN KEY (`uid`) REFERENCES `customers_tb` (`cid`) ON DELETE CASCADE,
  ADD CONSTRAINT `uid_cons2` FOREIGN KEY (`uid`) REFERENCES `staff_tb` (`sid`) ON DELETE CASCADE;

--
-- Constraints for table `lend_tb`
--
ALTER TABLE `lend_tb`
  ADD CONSTRAINT `cid_cons` FOREIGN KEY (`cid`) REFERENCES `customers_tb` (`cid`),
  ADD CONSTRAINT `isbn_cons` FOREIGN KEY (`isbn`) REFERENCES `books_tb` (`isbn`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
