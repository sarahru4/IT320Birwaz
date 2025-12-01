-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 30, 2025 at 11:32 PM
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
  `background_price` decimal(10,0) NOT NULL,
  `Lighting` text NOT NULL,
  `lightning_price` decimal(10,0) NOT NULL,
  `decoration_price` decimal(10,0) NOT NULL,
  `design_data` longtext,
  `design_image` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customizedesign`
--

INSERT INTO `customizedesign` (`CustomizeID`, `DesignID`, `Background`, `background_price`, `Lighting`, `lightning_price`, `decoration_price`, `design_data`, `design_image`) VALUES
(29, 376, 'pink', '15', 'yellow', '15', '25', NULL, NULL),
(30, 377, 'beige', '10', 'white', '0', '45', NULL, NULL),
(31, 378, 'black', '12', 'beige', '10', '35', NULL, NULL),
(34, 381, 'beige', '10', 'beige', '10', '60', NULL, NULL),
(35, 382, 'beige', '10', 'yellow', '15', '50', NULL, NULL),
(36, 383, 'black', '0', 'yellow', '0', '0', '{\"background\":\"black\",\"lighting\":\"yellow\",\"decorations\":[]}', 'design_1764529852.png'),
(37, 384, 'black', '12', 'white', '0', '10', NULL, NULL),
(38, 385, 'black', '12', 'white', '0', '0', NULL, NULL),
(48, 395, 'beige', '10', 'beige', '10', '80', '{\"background\":\"beige\",\"lighting\":\"beige\",\"decorations\":[{\"id\":\"decor-1764542170905\",\"name\":\"Lamp\",\"price\":25,\"src\":\"images/img9.png\",\"x\":436,\"y\":156,\"scale\":0.49950000000000006,\"angle\":0},{\"id\":\"decor-1764542172249\",\"name\":\"Sheer Fabric\",\"price\":20,\"src\":\"images/img12.png\",\"x\":98,\"y\":-3,\"scale\":1.0525,\"angle\":0},{\"id\":\"decor-1764542167874\",\"name\":\"Teddy Bear\",\"price\":20,\"src\":\"images/img1.png\",\"x\":486,\"y\":304,\"scale\":0.24,\"angle\":0},{\"id\":\"decor-1764542166762\",\"name\":\"Chair\",\"price\":15,\"src\":\"images/img3.png\",\"x\":257.75,\"y\":168.00000000000003,\"scale\":0.5804999999999999,\"angle\":0}]}', 'design_1764542222.png'),
(49, 396, 'beige', '10', 'white', '0', '85', '{\"background\":\"beige\",\"lighting\":\"white\",\"decorations\":[{\"id\":\"decor-1764544539905\",\"name\":\"Sheer Fabric\",\"price\":20,\"src\":\"images/img12.png\",\"x\":171.99999999999994,\"y\":-1.0000000000000568,\"scale\":1.1035,\"angle\":0},{\"id\":\"decor-1764544535772\",\"name\":\"Balloons\",\"price\":10,\"src\":\"images/img6.png\",\"x\":456,\"y\":127,\"scale\":0.6445,\"angle\":0},{\"id\":\"decor-1764544536894\",\"name\":\"Table\",\"price\":20,\"src\":\"images/img8.png\",\"x\":395,\"y\":282,\"scale\":0.3835,\"angle\":0},{\"id\":\"decor-1764544534017\",\"name\":\"Cake\",\"price\":35,\"src\":\"images/img2.png\",\"x\":429,\"y\":217,\"scale\":0.24,\"angle\":0}]}', 'design_1764544629.png');

-- --------------------------------------------------------

--
-- Table structure for table `decoration`
--

CREATE TABLE `decoration` (
  `DecorationID` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `img_url` varchar(255) NOT NULL,
  `price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `decoration`
--

INSERT INTO `decoration` (`DecorationID`, `name`, `img_url`, `price`) VALUES
(3, 'Teddy Bear', 'images/img1.png', '20'),
(4, 'Cake', 'images/img2.png', '35'),
(5, 'Chair', 'images/img3.png', '15'),
(6, 'Ocasion Frame', 'images/img4.png', '25'),
(7, 'Curtain', 'images/img5.png', '30'),
(8, 'Balloons', 'images/img6.png', '10'),
(9, 'Plant', 'images/img7.png', '15'),
(10, 'Table', 'images/img8.png', '20'),
(11, 'Lamp', 'images/img9.png', '25'),
(12, 'Flower Bouquet', 'images/img10.png', '30'),
(13, 'Signs Set', 'images/img11.png', '15'),
(14, 'Sheer Fabric', 'images/img12.png', '20'),
(15, 'Light Curtain', 'images/img13.png', '35'),
(16, 'Film Clapper Board', 'images/img14.png', '10'),
(17, 'White Canvas Stand', 'images/img15.png', '25');

-- --------------------------------------------------------

--
-- Table structure for table `design`
--

CREATE TABLE `design` (
  `DesignID` int(10) UNSIGNED NOT NULL,
  `Name` text NOT NULL,
  `Description` text,
  `Image_URL` text NOT NULL,
  `Type` varchar(10) NOT NULL DEFAULT 'Ready'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `design`
--

INSERT INTO `design` (`DesignID`, `Name`, `Description`, `Image_URL`, `Type`) VALUES
(334, 'Pink Theme', 'A soft pink backdrop adorned with flowers and gentle drapes, perfect for elegant portraits', 'stu2.JPG', 'Ready'),
(336, 'Graduation Theme', 'A classy graduation themed backdrop with navy curtains, golden balloons, and elegant lighting, designed for unforgettable graduation memories.', 'stu16.png', 'Ready'),
(337, 'Birthday Theme', 'A warm beige and gold setup with balloon clusters and a cute cake stand , ideal for birthdays and baby milestones.', 'stu11.JPG', 'Ready'),
(338, 'White Theme', 'Minimal and Modern white setup.', 'stu5.jpg', 'Ready'),
(339, 'Dark Theme', 'Dark draped fabrics and dramatic shadows build a cinematic atmosphere , ideal for artistic and fashion-inspired photoshoots.', 'stu6.JPG', 'Ready'),
(340, 'Boho Theme', 'A bohemian-inspired setup.', 'stu8.JPG', 'Ready'),
(376, 'Custom Design - 30/11/2025', 'Custom Design with Pink Background and Yellow Lighting and Balloons, Chair', 'design_1764467829.png', 'customized'),
(381, 'Custom Design - 11/30/2025', 'Custom Design with Beige Background and Beige Lighting and Chair, Teddy Bear, Lamp', 'design_1764511873.png', 'customized'),
(395, 'Custom Design - 12/1/2025', 'Custom Design with Beige Background and Beige Lighting and Lamp, Sheer Fabric, Teddy Bear, Chair', 'design_1764542222.png', 'customized'),
(396, 'Custom Design - 12/1/2025', 'Custom Design with Beige Background and White Lighting and Sheer Fabric, Balloons, Table, Cake', 'design_1764544629.png', 'customized');

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
(2, 2, 'Your reservation is Tomorrow', '2025-11-26'),
(3, 13, 'Your studio reservation #13 is confirmed for 2025-12-06 at 12:00 PM - 1:00 PM', '2025-11-30'),
(4, 14, 'Your studio reservation #14 is confirmed for 2025-12-20 at 12:00 PM - 1:00 PM', '2025-11-30'),
(5, 15, 'Your studio reservation #15 is confirmed for 2025-12-01 at 5:00 PM - 6:00 PM', '2025-11-30'),
(6, 16, 'Your studio reservation #16 is confirmed for 2025-12-01 at 12:00 PM - 1:00 PM', '2025-11-30'),
(7, 17, 'Your studio reservation #17 is confirmed for 2025-12-01 at 12:00 PM - 1:00 PM', '2025-11-30'),
(8, 18, 'Your studio reservation #18 is confirmed for 2025-12-01 at 12:00 PM - 1:00 PM', '2025-11-30'),
(9, 19, 'Your studio reservation #19 is confirmed for 2025-12-01 at 12:00 PM - 1:00 PM', '2025-11-30'),
(10, 20, 'Your studio reservation #20 is confirmed for 2025-12-01 at 5:00 PM - 6:00 PM', '2025-11-30'),
(11, 21, 'Your studio reservation #21 is confirmed for 2025-12-01 at 12:00 PM - 1:00 PM', '2025-11-30'),
(12, 22, 'Your studio reservation #22 is confirmed for 2025-12-01 at 12:00 PM - 1:00 PM', '2025-11-30'),
(13, 23, 'Your studio reservation #23 is confirmed for 2025-12-01 at 5:00 PM - 6:00 PM', '2025-11-30'),
(14, 24, 'Your studio reservation #24 is confirmed for 2025-12-01 at 3:00 PM - 4:00 PM', '2025-11-30'),
(15, 25, 'Your studio reservation #25 is confirmed for 2025-12-01 at 10:00 AM - 11:00 AM', '2025-11-30'),
(16, 26, 'Your studio reservation #26 is confirmed for 2025-12-01 at 12:00 PM - 1:00 PM', '2025-11-30'),
(17, 27, 'Your studio reservation #27 is confirmed for 2025-12-01 at 5:00 PM - 6:00 PM', '2025-11-30'),
(18, 28, 'Your studio reservation #28 is confirmed for 2025-12-24 at 3:00 PM - 4:00 PM', '2025-11-30'),
(19, 29, 'Your studio reservation #29 is confirmed for 2025-12-01 at 3:00 PM - 4:00 PM', '2025-11-30'),
(20, 30, 'Your studio reservation #30 is confirmed for 2025-12-10 at 3:00 PM - 4:00 PM', '2025-11-30'),
(21, 31, 'Your studio reservation #31 is confirmed for 2025-12-18 at 5:00 PM - 6:00 PM', '2025-11-30'),
(22, 32, 'Your studio reservation #32 is confirmed for 2025-12-25 at 5:00 PM - 6:00 PM', '2025-11-30'),
(23, 33, 'Your studio reservation #33 is confirmed for 2025-12-24 at 10:00 AM - 11:00 AM', '2025-11-30'),
(24, 34, 'Your studio reservation #34 is confirmed for 2025-12-19 at 3:00 PM - 4:00 PM', '2025-11-30'),
(25, 35, 'Your studio reservation #35 is confirmed for 2025-12-18 at 3:00 PM - 4:00 PM', '2025-11-30'),
(26, 36, 'Your studio reservation #36 is confirmed for 2025-12-25 at 5:00 PM - 6:00 PM', '2025-11-30'),
(27, 37, 'Your studio reservation #37 is confirmed for 2026-02-21 at 3:00 PM - 4:00 PM', '2025-11-30'),
(28, 38, 'Your studio reservation #38 is confirmed for 2025-12-02 at 3:00 PM - 4:00 PM', '2025-11-30'),
(29, 39, 'Your studio reservation #39 is confirmed for 2026-01-01 at 5:00 PM - 6:00 PM', '2025-11-30'),
(30, 40, 'Your studio reservation #40 is confirmed for 2025-12-02 at 5:00 PM - 6:00 PM', '2025-11-30'),
(31, 41, 'Your studio reservation #41 is confirmed for 2025-12-02 at 12:00 PM - 1:00 PM', '2025-11-30'),
(32, 42, 'Your studio reservation #42 is confirmed for 2025-12-31 at 5:00 PM - 6:00 PM', '2025-11-30'),
(33, 43, 'Your studio reservation #43 is confirmed for 2025-12-02 at 12:00 PM - 1:00 PM', '2025-11-30'),
(34, 44, 'Your studio reservation #44 is confirmed for 2025-12-02 at 12:00 PM - 1:00 PM', '2025-11-30'),
(35, 45, 'Your studio reservation #45 is confirmed for 2025-12-02 at 10:00 AM - 11:00 AM', '2025-11-30'),
(36, 46, 'Your studio reservation #46 is confirmed for 2025-12-03 at 10:00 AM - 11:00 AM', '2025-11-30'),
(37, 47, 'Your studio reservation #47 is confirmed for 2025-12-04 at 10:00 AM - 11:00 AM', '2025-11-30'),
(38, 48, 'Your studio reservation #48 is confirmed for 2025-12-20 at 10:00 AM - 11:00 AM', '2025-11-30'),
(39, 49, 'Your studio reservation #49 is confirmed for 2025-12-19 at 10:00 AM - 11:00 AM', '2025-11-30'),
(40, 50, 'Your studio reservation #50 is confirmed for 2025-12-19 at 10:00 AM - 11:00 AM', '2025-11-30'),
(41, 51, 'Your studio reservation #51 is confirmed for 2025-12-11 at 10:00 AM - 11:00 AM', '2025-11-30'),
(42, 52, 'Your studio reservation #52 is confirmed for 2025-12-11 at 3:00 PM - 4:00 PM', '2025-11-30'),
(43, 53, 'Your studio reservation #53 is confirmed for 2025-12-19 at 12:00 PM - 1:00 PM', '2025-11-30'),
(44, 54, 'Your studio reservation #54 is confirmed for 2025-12-11 at 10:00 AM - 11:00 AM', '2025-11-30'),
(45, 55, 'Your studio reservation #55 is confirmed for 2025-12-10 at 10:00 AM - 11:00 AM', '2025-12-01'),
(46, 56, 'Your studio reservation #56 is confirmed for 2025-12-01 at 5:00 PM - 6:00 PM', '2025-12-01'),
(47, 57, 'Your studio reservation #57 is confirmed for 2025-12-19 at 5:00 PM - 6:00 PM', '2025-12-01'),
(48, 58, 'Your studio reservation #58 is confirmed for 2025-12-14 at 12:00 PM - 1:00 PM', '2025-12-01');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `ReservationID` int(10) UNSIGNED NOT NULL,
  `UserID` int(11) UNSIGNED NOT NULL,
  `DesignID` int(10) UNSIGNED DEFAULT NULL,
  `Date` date NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `total_price` decimal(10,0) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statues` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`ReservationID`, `UserID`, `DesignID`, `Date`, `time_slot`, `total_price`, `created_at`, `statues`) VALUES
(28, 4, 381, '2025-12-24', '3:00 PM - 4:00 PM', '130', '2025-11-30 14:11:43', 'confirmed'),
(29, 4, 376, '2025-12-01', '3:00 PM - 4:00 PM', '50', '2025-11-30 14:16:17', 'confirmed'),
(55, 4, 395, '2025-12-10', '10:00 AM - 11:00 AM', '150', '2025-11-30 22:37:12', 'confirmed'),
(56, 7, 338, '2025-12-01', '5:00 PM - 6:00 PM', '50', '2025-11-30 23:15:14', 'confirmed'),
(57, 7, 396, '2025-12-19', '5:00 PM - 6:00 PM', '145', '2025-11-30 23:17:18', 'confirmed'),
(58, 7, 339, '2025-12-14', '12:00 PM - 1:00 PM', '50', '2025-11-30 23:17:36', 'confirmed');

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
  ADD PRIMARY KEY (`DecorationID`);

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
  ADD KEY `fk_reservation_design` (`DesignID`);

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
  MODIFY `CustomizeID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `decoration`
--
ALTER TABLE `decoration`
  MODIFY `DecorationID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `design`
--
ALTER TABLE `design`
  MODIFY `DesignID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=397;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `NotificationID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `ReservationID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_reservation_design` FOREIGN KEY (`DesignID`) REFERENCES `design` (`DesignID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservation_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
