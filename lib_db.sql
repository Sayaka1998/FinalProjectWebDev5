-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 18, 2024 at 08:54 AM
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

--
-- Dumping data for table `approval_tb`
--

INSERT INTO `approval_tb` (`aid`, `uid`, `status`) VALUES
(6, 5, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `blacklst_tb`
--

CREATE TABLE `blacklst_tb` (
  `blid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blacklst_tb`
--

INSERT INTO `blacklst_tb` (`blid`, `uid`, `time`) VALUES
(2, 8, '2024-01-18 05:49:06');

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
('9780553296983', 'The Diary of a Young Girl', 'Anne Frank', 'History', 'available'),
('9780590129275', 'The Little Price', 'Antoine de Saint-Exupery', 'Fantasy', 'available'),
('9781862302884', 'Dance on My Grave', 'Aidan Chambers', 'Young adult', 'unavailable');

-- --------------------------------------------------------

--
-- Table structure for table `lend_tb`
--

CREATE TABLE `lend_tb` (
  `bid` int(11) NOT NULL,
  `isbn` varchar(15) NOT NULL,
  `uid` int(11) NOT NULL,
  `ldate` date NOT NULL,
  `rdata` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lend_tb`
--

INSERT INTO `lend_tb` (`bid`, `isbn`, `uid`, `ldate`, `rdata`) VALUES
(21, '9781862302884', 7, '2024-01-18', '2024-01-25');

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
(3, 'Sayaka', 'Maki', 'sayaka@mail.com', '$2y$10$zWsdHgHwIiRs3EEnVArAMeIvdL0BiwhWX2Srzm0OEBbPSYVB0Op16', 'Admin', 5),
(4, 'Sun', 'An', 'sun@mail.com', '$2y$10$WhWcWMmma8K8gwH7RF48x.PP3/wyG9XyVbV7op7HWKi8CfT5kL.vC', 'Staff', 5),
(5, 'Joao', 'Hibert', 'joao@mail.com', '$2y$10$Kxu2ig8uSrUqItmYSkUre.5AhQaje9i/DcC/xFUUV.UFHuEDJPv76', 'Staff', 5),
(6, 'Matheus', 'Butignol', 'matheus@mail.com', '$2y$10$gAbdTj3xfrxb6H9I.F8TUOn8fC5/KNWxlIe8FpZMMOpikVowpULrq', 'Customer', 5),
(7, 'Isaac', 'Neto', 'isaac@mail.com', '$2y$10$tVV3yKtInJIZMty/Aucci.JkgBcTQryVG2fsV.FymISUc1./jn7HW', 'Customer', 5),
(8, 'John', 'Smith', 'john@mail.com', '$2y$10$wc1OFmWq9FXGUycBLUWxUOuYQJf6mlwlomyzum4ulCOlPYLOrGt/S', 'Customer', 0);

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
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `blacklst_tb`
--
ALTER TABLE `blacklst_tb`
  MODIFY `blid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lend_tb`
--
ALTER TABLE `lend_tb`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_tb`
--
ALTER TABLE `user_tb`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
