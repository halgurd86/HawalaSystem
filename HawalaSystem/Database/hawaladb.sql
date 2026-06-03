-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2026 at 01:33 PM
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
-- Database: `hawaladb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` int(11) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `UserType` varchar(50) NOT NULL DEFAULT 'Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `UserName`, `Password`, `UserType`) VALUES
(1, '1', '1', 'SuperAdmin'),
(2, '2', '2', 'Editor'),
(3, '2', '$3', 'Editor'),
(4, '4', '4', 'SuperAdmin');

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `CurrencyID` int(11) NOT NULL,
  `CurrencyName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`CurrencyID`, `CurrencyName`) VALUES
(1, 'دۆلار'),
(2, 'دینار'),
(3, 'یورو'),
(4, 'تومان'),
(5, 'درهم'),
(6, 'پاوەند'),
(7, 'لیرە');

-- --------------------------------------------------------

--
-- Table structure for table `nusenga`
--

CREATE TABLE `nusenga` (
  `NusengaID` int(11) NOT NULL,
  `Code` varchar(50) DEFAULT NULL,
  `Naw` varchar(100) NOT NULL,
  `PhoneNo` varchar(20) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nusenga`
--

INSERT INTO `nusenga` (`NusengaID`, `Code`, `Naw`, `PhoneNo`, `Address`) VALUES
(1, 'N001', 'نوسینگەی سەنتەر', '07501111111', 'سلێمانی'),
(2, 'N002', 'نوسینگەی رانیە', '07502222222', 'رانیە'),
(3, 'N003', 'نوسینگەی کەرکوک', '07503333333', 'کەرکوک'),
(4, 'N004', 'نوسینگەی هەولێر', '07504444444', 'هەولێر'),
(5, 'N005', 'نوسینگەی دهۆک', '07505555555', 'دهۆک'),
(6, 'N006', 'نوسینگەی کفری', '07506666666', 'کفری'),
(7, 'N007', 'نوسینگەی چەمچەماڵ', '07507777777', 'چەمچەماڵ'),
(8, 'N008', 'نوسینگەی پێنجوین', '07508888888', 'پێنجوین'),
(9, 'N009', 'نوسینگەی شارەزوور', '07509999999', 'شارەزوور'),
(10, 'N010', 'نوسینگەی قەڵادزێ', '07501010101', 'قەڵادزێ'),
(11, 'N011', 'نوسینگەی شێروانە', '07508785858', 'عالیاوا');

-- --------------------------------------------------------

--
-- Table structure for table `shar`
--

CREATE TABLE `shar` (
  `SharID` int(11) NOT NULL,
  `SharName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shar`
--

INSERT INTO `shar` (`SharID`, `SharName`) VALUES
(1, 'سلێمانی'),
(2, 'هەولێر'),
(3, 'کەرکوک'),
(4, 'دهۆک'),
(5, 'رانیە'),
(6, 'چەمچەماڵ'),
(7, 'کفری'),
(8, 'پێنجوین');

-- --------------------------------------------------------

--
-- Table structure for table `wargrtn`
--

CREATE TABLE `wargrtn` (
  `WargrtnID` int(11) NOT NULL,
  `NusengaID` int(11) NOT NULL,
  `NawyWergr` varchar(100) DEFAULT NULL,
  `PhoneNo` varchar(20) DEFAULT NULL,
  `NawyNerar` varchar(100) DEFAULT NULL,
  `Note` varchar(500) DEFAULT NULL,
  `BarwarA` date DEFAULT NULL,
  `TimeA` time DEFAULT NULL,
  `BarwarB` date DEFAULT NULL,
  `TimeB` time DEFAULT NULL,
  `SharID` int(11) DEFAULT NULL,
  `IsReceived` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wargrtn`
--

INSERT INTO `wargrtn` (`WargrtnID`, `NusengaID`, `NawyWergr`, `PhoneNo`, `NawyNerar`, `Note`, `BarwarA`, `TimeA`, `BarwarB`, `TimeB`, `SharID`, `IsReceived`) VALUES
(1, 1, 'علی', '07501111001', 'ئارام', 'تێبینی 1', '2026-01-01', '10:00:00', '2026-01-01', '10:05:00', 1, 0),
(2, 2, 'حەمە', '07501111002', 'سامان', 'تێبینی 2', '2026-01-01', '10:01:00', '2026-01-01', '10:06:00', 2, 0),
(3, 3, 'سەردار', '07501111003', 'دیار', 'تێبینی 3', '2026-01-01', '10:02:00', '2026-01-01', '10:07:00', 3, 0),
(4, 4, 'کەریم', '07501111004', 'جەمال', 'تێبینی 4', '2026-01-01', '10:03:00', '2026-01-01', '10:08:00', 4, 0),
(5, 5, 'نەوزاد', '07501111005', 'ڕێبوار', 'تێبینی 5', '2026-01-01', '10:04:00', '2026-01-01', '10:09:00', 5, 0),
(6, 1, 'ئازاد', '07501111006', 'پێشەوا', 'تێبینی 6', '2026-01-01', '10:05:00', '2026-01-01', '10:10:00', 1, 0),
(7, 2, 'ڕەحیم', '07501111007', 'لەتیف', 'تێبینی 7', '2026-01-01', '10:06:00', '2026-01-01', '10:11:00', 2, 0),
(8, 3, 'هێمن', '07501111008', 'شاخەوان', 'تێبینی 8', '2026-01-01', '10:07:00', '2026-01-01', '10:12:00', 3, 0),
(9, 4, 'بەهرام', '07501111009', 'هەردی', 'تێبینی 9', '2026-01-01', '10:08:00', '2026-01-01', '10:13:00', 4, 0),
(10, 5, 'شوان', '07501111010', 'نەبی', 'تێبینی 10', '2026-01-01', '10:09:00', '2026-01-01', '10:14:00', 5, 0),
(11, 7, 'عثمان علی', '07701126687', 'سس', '؛؛', '2026-06-02', '03:31:00', '2026-06-01', '03:31:00', 1, 0),
(12, 1, 'ئاری', '0750', 'علی', 'ییی', '2026-06-02', '18:06:00', '2026-06-02', '18:06:00', 1, 0),
(13, 1, 'ئاری', '07501126687', 'هەڵگورد', 'نیە', '2026-06-02', '18:09:00', '2026-06-02', '18:09:00', 1, 0),
(14, 1, 'ئاری', '07507610705', 'احمد', 'نیە', '2026-06-02', '18:12:00', '2026-06-02', '18:12:00', 2, 1),
(15, 1, 'شوان', '0780014855', 'محمد', 'هه', '2026-06-02', '22:44:00', '2026-06-02', '22:44:00', 1, 0),
(16, 3, 'محمد علی', '07801122544', 'هەڵگورد', 'ف', '2026-06-02', '22:50:00', '2026-06-02', '22:50:00', 1, 0),
(17, 9, 'وەیسی', '07501129988', 'وەتمان', 'ییی', '2026-06-02', '23:41:00', '2026-06-02', '23:41:00', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `wargrtndetails`
--

CREATE TABLE `wargrtndetails` (
  `DetailID` int(11) NOT NULL,
  `WargrtnID` int(11) NOT NULL,
  `CurrencyID` int(11) NOT NULL,
  `Amount` decimal(18,2) DEFAULT 0.00,
  `Commission` decimal(18,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wargrtndetails`
--

INSERT INTO `wargrtndetails` (`DetailID`, `WargrtnID`, `CurrencyID`, `Amount`, `Commission`) VALUES
(1, 1, 1, 500.00, 10.00),
(2, 1, 2, 300.00, 5.00),
(3, 2, 1, 700.00, 12.00),
(4, 2, 3, 200.00, 3.00),
(5, 3, 2, 400.00, 6.00),
(6, 4, 4, 900.00, 15.00),
(7, 5, 1, 1000.00, 20.00),
(8, 6, 2, 250.00, 4.00),
(9, 7, 3, 600.00, 9.00),
(10, 8, 1, 800.00, 11.00),
(16, 11, 1, 600.00, 60.00),
(17, 11, 2, 5000.00, 50.00),
(18, 11, 3, 9000.00, 900.00),
(19, 12, 2, 2000.00, 20.00),
(20, 12, 2, 5000000.00, 5000.00),
(27, 14, 1, 5000.00, 50.00),
(28, 14, 1, 600000.00, 6000.00),
(31, 15, 1, 500.00, 12.00),
(32, 15, 2, 2000000.00, 2000.00),
(36, 16, 1, 800.00, 8.00),
(37, 16, 2, 8000000.00, 8000.00),
(38, 16, 3, 500.00, 50.00),
(39, 13, 1, 5000.00, 50.00),
(40, 13, 2, 10000000.00, 10000.00),
(43, 17, 1, 7000.00, 70.00),
(44, 17, 2, 80000000.00, 80000.00),
(45, 17, 3, 1000.00, 10.00),
(46, 17, 4, 50000000.00, 500000.00),
(47, 17, 5, 20.00, 1.00),
(48, 17, 6, 45.00, 12.00),
(49, 17, 7, 50.00, 52.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`CurrencyID`);

--
-- Indexes for table `nusenga`
--
ALTER TABLE `nusenga`
  ADD PRIMARY KEY (`NusengaID`),
  ADD UNIQUE KEY `Code` (`Code`);

--
-- Indexes for table `shar`
--
ALTER TABLE `shar`
  ADD PRIMARY KEY (`SharID`);

--
-- Indexes for table `wargrtn`
--
ALTER TABLE `wargrtn`
  ADD PRIMARY KEY (`WargrtnID`),
  ADD KEY `FK_Wargrtn_Nusenga` (`NusengaID`),
  ADD KEY `FK_Wargrtn_Shar` (`SharID`);

--
-- Indexes for table `wargrtndetails`
--
ALTER TABLE `wargrtndetails`
  ADD PRIMARY KEY (`DetailID`),
  ADD KEY `FK_WargrtnDetails_Wargrtn` (`WargrtnID`),
  ADD KEY `FK_WargrtnDetails_Currency` (`CurrencyID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `CurrencyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `nusenga`
--
ALTER TABLE `nusenga`
  MODIFY `NusengaID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `shar`
--
ALTER TABLE `shar`
  MODIFY `SharID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wargrtn`
--
ALTER TABLE `wargrtn`
  MODIFY `WargrtnID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `wargrtndetails`
--
ALTER TABLE `wargrtndetails`
  MODIFY `DetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `wargrtn`
--
ALTER TABLE `wargrtn`
  ADD CONSTRAINT `FK_Wargrtn_Nusenga` FOREIGN KEY (`NusengaID`) REFERENCES `nusenga` (`NusengaID`),
  ADD CONSTRAINT `FK_Wargrtn_Shar` FOREIGN KEY (`SharID`) REFERENCES `shar` (`SharID`);

--
-- Constraints for table `wargrtndetails`
--
ALTER TABLE `wargrtndetails`
  ADD CONSTRAINT `FK_WargrtnDetails_Currency` FOREIGN KEY (`CurrencyID`) REFERENCES `currency` (`CurrencyID`),
  ADD CONSTRAINT `FK_WargrtnDetails_Wargrtn` FOREIGN KEY (`WargrtnID`) REFERENCES `wargrtn` (`WargrtnID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
