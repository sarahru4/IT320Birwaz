-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 24, 2025 at 02:17 AM
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
  `CustomizeID` int(10) UNSIGNED NOT NULL,
  `DesignID` int(11) NOT NULL,
  `Background` text NOT NULL,
  `Lighting` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customizedesign`
--

INSERT INTO `customizedesign` (`CustomizeID`, `DesignID`, `Background`, `Lighting`) VALUES
(1, 111, 'Blue', 'Yellow'),
(2, 222, 'Pink', 'White');

-- --------------------------------------------------------

--
-- Table structure for table `decoration`
--

CREATE TABLE `decoration` (
  `DecorationID` int(10) UNSIGNED NOT NULL,
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
  `DesignID` int(10) UNSIGNED NOT NULL,
  `Name` text NOT NULL,
  `Description` text,
  `Image_URL` text NOT NULL,
  `Type` varchar(6) NOT NULL DEFAULT 'Ready'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `design`
--

INSERT INTO `design` (`DesignID`, `Name`, `Description`, `Image_URL`, `Type`) VALUES
(334, 'Pink Theme', 'A soft pink backdrop adorned with flowers and gentle drapes, perfect for elegant portraits', 'stu2.JPG', 'Ready'),
(336, 'Graduation Theme', 'A classy graduation themed backdrop with navy curtains, golden balloons, and elegant lighting, designed for unforgettable graduation memories.', 'stu16.png', 'Ready'),
(337, 'Birthday Theme', 'A warm beige and gold setup with balloon clusters and a cute cake stand , ideal for birthdays and baby milestones.', 'stu11.JPG', 'Ready'),
(338, 'White Theme', 'Minimal and modern, this all white setup with a single chair and soft fabric evokes a calm and timeless aesthetic.', 'stu5.jpg', 'Ready'),
(339, 'Dark Theme', 'Dark draped fabrics and dramatic shadows build a cinematic atmosphere , ideal for artistic and fashion-inspired photoshoots.', 'stu6.JPG', 'Ready'),
(340, 'Boho Theme', 'A bohemian-inspired setup featuring soft daylight, flowing white curtains, and natural textures , creating a relaxed and artistic atmosphere.', 'stu8.JPG', 'Ready');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `NotificationID` int(10) UNSIGNED NOT NULL,
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
  `ReservationID` int(10) UNSIGNED NOT NULL,
  `UserID` int(11) UNSIGNED NOT NULL,
  `DesignID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Start_Time` time NOT NULL,
  `End_Time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`ReservationID`, `UserID`, `DesignID`, `Date`, `Start_Time`, `End_Time`) VALUES
(1, 4, 334, '2025-11-15', '15:00:00', '16:00:00'),
(2, 7, 339, '2025-11-26', '18:35:00', '19:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(10) UNSIGNED NOT NULL,
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
(4, 'sarah', 'sarah@gmail.com', '$2y$10$bQtaMkI0hQn8lS5.eB6QFehtHZl0YvZcGCeCMVC9fLIdUJcYJuq/C', 504567890, 'user'),
(6, 'reema', 'reema@gmail.com', '$2y$10$.WqVS9ZAslvPrmxpglvKRuyXfU.0aW8rjNmd6EQ7rwpJ/BhokoAh6', 507689567, 'Admin'),
(7, 'Asma', 'asma@gmail.com', '$2y$10$AppyKd8yMvp7XSMluOinCeyJYwdkXUvUTNEYs8e5oyhmyUUHusvOy', 504567999, 'User');

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

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customizedesign`
--
ALTER TABLE `customizedesign`
  MODIFY `CustomizeID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `decoration`
--
ALTER TABLE `decoration`
  MODIFY `DecorationID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `design`
--
ALTER TABLE `design`
  MODIFY `DesignID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=341;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `NotificationID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `ReservationID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_reservation_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
