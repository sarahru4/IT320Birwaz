-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 04, 2025 at 04:57 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `birwaz`
--

-- --------------------------------------------------------

--
-- Table structure for table `customizedesign`
--

CREATE TABLE `customizedesign` (
  `CustomizeID` int(11) NOT NULL,
  `DesignID` int(11) NOT NULL,
  `Background` text NOT NULL,
  `Lighting` text NOT NULL,
  `Decorations` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customizedesign`
--

INSERT INTO `customizedesign` (`CustomizeID`, `DesignID`, `Background`, `Lighting`, `Decorations`) VALUES
(1, 111, 'Blue', 'Yellow', 'Chair, Graduateion stick, Curtains '),
(2, 222, 'Pink', 'White', 'Toys, Blanket, Flowers ');

-- --------------------------------------------------------

--
-- Table structure for table `decoration`
--

CREATE TABLE `decoration` (
  `DecorationID` int(11) NOT NULL,
  `CustomizeID` int(11) NOT NULL,
  `name` text NOT NULL,
  `img_url` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `decoration`
--

INSERT INTO `decoration` (`DecorationID`, `CustomizeID`, `name`, `img_url`) VALUES
(1, 1, 'Gradution', 'https://img1.png'),
(2, 2, 'Newborn girl ', 'https://img2.png');

-- --------------------------------------------------------

--
-- Table structure for table `design`
--

CREATE TABLE `design` (
  `DesignID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Description` text,
  `Image_URL` text NOT NULL,
  `Type` varchar(6) NOT NULL DEFAULT 'Ready'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `design`
--

INSERT INTO `design` (`DesignID`, `Name`, `Description`, `Image_URL`, `Type`) VALUES
(111, 'Dark Style', 'Dark light and decor for graduation', 'https://img1.png', 'Ready'),
(222, 'Pink style', 'Pink decoration with toys for newborn girl.', 'https://img2.png', 'Make'),
(333, 'Elegant Style', 'White and classic decor for new married couple.', 'https://img3.png', 'Ready');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `NotificationID` int(11) NOT NULL,
  `ReservationID` int(11) NOT NULL,
  `Message` text NOT NULL,
  `Scheduled_At` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`NotificationID`, `ReservationID`, `Message`, `Scheduled_At`) VALUES
(1, 1, 'Your reservation is Tomorrow', '2025-11-15'),
(2, 2, 'Your reservation is Tomorrow', '2025-11-26');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `ReservationID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `DesignID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Start_Time` time NOT NULL,
  `End_Time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`ReservationID`, `UserID`, `DesignID`, `Date`, `Start_Time`, `End_Time`) VALUES
(1, 1, 111, '2025-11-15', '15:00:00', '16:00:00'),
(2, 2, 222, '2025-11-26', '18:35:00', '19:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Email` text NOT NULL,
  `Password` text NOT NULL,
  `Phone` int(10) NOT NULL,
  `Role` varchar(5) NOT NULL DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Name`, `Email`, `Password`, `Phone`, `Role`) VALUES
(1, 'Ahmed Khalid', 'Ahmed@gmail.com', '12341234', 552389245, 'User'),
(2, 'Maram Salim', 'Maram@gmail.com', '111222', 564534466, 'User'),
(3, 'Raghad Mohammad', 'Raghad@gmail.com', '123123', 575443645, 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customizedesign`
--
ALTER TABLE `customizedesign`
  ADD PRIMARY KEY (`CustomizeID`),
  ADD KEY `DesignID` (`DesignID`);

--
-- Indexes for table `decoration`
--
ALTER TABLE `decoration`
  ADD PRIMARY KEY (`DecorationID`),
  ADD KEY `CustomizeID` (`CustomizeID`);

--
-- Indexes for table `design`
--
ALTER TABLE `design`
  ADD PRIMARY KEY (`DesignID`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`NotificationID`),
  ADD KEY `ReservationID` (`ReservationID`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`ReservationID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `DesignID` (`DesignID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
