-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2025 at 04:08 PM
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
-- Database: `book_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `BookID` int(11) NOT NULL,
  `ISBN` varchar(45) DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Author` varchar(45) DEFAULT NULL,
  `Category` varchar(255) DEFAULT NULL,
  `Publisher` varchar(255) DEFAULT NULL,
  `CopiesAvailable` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`BookID`, `ISBN`, `Title`, `Author`, `Category`, `Publisher`, `CopiesAvailable`) VALUES
(101, '111-111', 'Harry Potter: The Philosophers Stone', 'J.K. Rowling', 'Fantasy', 'London Publishing Corp.', 48),
(102, '222-222', 'Harry Potter and the Chamber of Secrets', 'J.K. Rowling', 'Fantasy', 'London Publishing Corp.', 48),
(103, '333-333', 'Harry Potter and the Prisoner of Azkaban', 'J.K. Rowling', 'Fantasy', 'London Publishing Corp.', 28);

-- --------------------------------------------------------

--
-- Table structure for table `bookrecords`
--

CREATE TABLE `bookrecords` (
  `RecordID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `BookID` int(11) DEFAULT NULL,
  `BorrowDate` date DEFAULT NULL,
  `DueDate` date DEFAULT NULL,
  `ReturnDate` date DEFAULT NULL,
  `Status` enum('Borrowed','Returned','Overdue') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookrecords`
--

INSERT INTO `bookrecords` (`RecordID`, `UserID`, `BookID`, `BorrowDate`, `DueDate`, `ReturnDate`, `Status`) VALUES
(8, 2, 101, '2025-05-22', '2025-05-29', '2025-05-22', 'Returned'),
(9, 2, 103, '2025-05-22', '2025-05-29', '2025-05-22', 'Returned');

--
-- Triggers `bookrecords`
--
DELIMITER $$
CREATE TRIGGER `trg_update_return_date` BEFORE UPDATE ON `bookrecords` FOR EACH ROW BEGIN
    IF NEW.Status = 'Returned' AND (NEW.ReturnDate IS NULL OR NEW.ReturnDate = '') THEN
        SET NEW.ReturnDate = CURDATE();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

CREATE TABLE `fines` (
  `FineID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `RecordID` int(11) DEFAULT NULL,
  `FineAmount` decimal(10,2) DEFAULT NULL,
  `Paid` tinyint(4) DEFAULT NULL,
  `PaymentDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fines`
--

INSERT INTO `fines` (`FineID`, `UserID`, `RecordID`, `FineAmount`, `Paid`, `PaymentDate`) VALUES
(1, 2, 0, 400.00, 1, '2025-05-22'),
(2, 2, 3, 500.00, 1, '2025-05-22');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `ReservationID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `BookID` int(11) DEFAULT NULL,
  `ReservationDate` date DEFAULT NULL,
  `Status` enum('Reserved','Cancelled','Completed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`ReservationID`, `UserID`, `BookID`, `ReservationDate`, `Status`) VALUES
(7, 2, 102, '2025-05-22', 'Reserved'),
(8, 2, 103, '2025-05-22', 'Reserved');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Phone` varchar(11) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Name`, `Email`, `Phone`, `Address`) VALUES
(1, 'Princess Arnante', 'arnanteprincess@gmail.com', '09091346874', 'Sto Nino, Iriga City'),
(2, 'Conrad Fisher', 'conrad@gmail.com', '09345678902', 'Cousins Beach');

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `WalletID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`WalletID`, `UserID`, `Balance`) VALUES
(6, 1, 0.00),
(7, 2, 1100.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`BookID`);

--
-- Indexes for table `bookrecords`
--
ALTER TABLE `bookrecords`
  ADD PRIMARY KEY (`RecordID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `BookID` (`BookID`);

--
-- Indexes for table `fines`
--
ALTER TABLE `fines`
  ADD PRIMARY KEY (`FineID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `RecordID` (`RecordID`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`ReservationID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `BookID` (`BookID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `wallet`
--
ALTER TABLE `wallet`
  ADD PRIMARY KEY (`WalletID`),
  ADD KEY `UserID` (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `BookID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `bookrecords`
--
ALTER TABLE `bookrecords`
  MODIFY `RecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `ReservationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wallet`
--
ALTER TABLE `wallet`
  MODIFY `WalletID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `wallet`
--
ALTER TABLE `wallet`
  ADD CONSTRAINT `wallet_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
