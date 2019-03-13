-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2016 at 11:48 AM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

-- WAMPSERVER Version: 3.0.6 64bit

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `widget`
--
CREATE DATABASE IF NOT EXISTS `widget` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `widget`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `createOrder`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `createOrder` (IN `p_email` VARCHAR(255), IN `p_quantity` INT, IN `p_color_id` INT, IN `p_needed` DATE, IN `p_type_id` INT, IN `p_unique_id` VARCHAR(13))  MODIFIES SQL DATA
BEGIN
INSERT INTO `order` (email, quantity, color_id, needed, type_id, unique_id) VALUES (p_email, p_quantity, p_color_id, p_needed, p_type_id, p_unique_id);
CALL getOrder(p_unique_id);
END$$

DROP PROCEDURE IF EXISTS `getAllOrders`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAllOrders` ()  NO SQL
SELECT `order`.*, color.name AS color_name, type.name AS type_name FROM `order`
LEFT JOIN color ON color.id = `order`.color_id
LEFT JOIN type on type.id = `order`.type_id$$

DROP PROCEDURE IF EXISTS `getColor`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getColor` ()  NO SQL
SELECT * FROM color$$

DROP PROCEDURE IF EXISTS `getOrder`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrder` (IN `p_unique_id` VARCHAR(255))  NO SQL
SELECT `order`.*, color.name AS color_name, type.name AS type_name, status.name AS status_name FROM `order`
LEFT JOIN color ON color.id = `order`.color_id
LEFT JOIN type on type.id = `order`.type_id
LEFT JOIN status ON status.id = `order`.status_id
WHERE unique_id = p_unique_id$$

DROP PROCEDURE IF EXISTS `getStatus`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getStatus` ()  NO SQL
SELECT * FROM status$$

DROP PROCEDURE IF EXISTS `getType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getType` ()  NO SQL
SELECT * FROM type$$

DROP PROCEDURE IF EXISTS `getUser`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUser` (IN `p_email` VARCHAR(255), IN `p_password` VARCHAR(255))  NO SQL
SELECT * FROM user WHERE email = p_email AND password = p_password$$

DROP PROCEDURE IF EXISTS `getUserByToken`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserByToken` (IN `p_token` VARCHAR(13))  NO SQL
SELECT * FROM user WHERE token = p_token$$

DROP PROCEDURE IF EXISTS `updateOrderStatus`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateOrderStatus` (IN `p_unique_id` VARCHAR(13), IN `p_status_id` INT)  NO SQL
UPDATE `order` set status_id = p_status_id WHERE unique_id = p_unique_id$$

DROP PROCEDURE IF EXISTS `updateUserToken`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserToken` (IN `p_user_id` INT, IN `p_token` VARCHAR(255))  NO SQL
UPDATE user set token = p_token WHERE id = p_user_id$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `color`
--

DROP TABLE IF EXISTS `color`;
CREATE TABLE IF NOT EXISTS `color` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hex` varchar(8) NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `color`
--

INSERT INTO `color` (`id`, `hex`, `name`) VALUES
(1, 'FF0000', 'Red'),
(2, '00FF00', 'Green'),
(3, '0000FF', 'Blue');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL,
  `needed` date NOT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  `unique_id` varchar(13) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id` (`unique_id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `name`) VALUES
(1, 'Received'),
(2, 'Processed'),
(3, 'Shipped'),
(4, 'Archived');

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

DROP TABLE IF EXISTS `type`;
CREATE TABLE IF NOT EXISTS `type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`id`, `name`) VALUES
(1, 'Widget'),
(2, 'Widget Pro'),
(3, 'Widget Xtreme');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(64) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `token` varchar(13) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  UNIQUE KEY `email_password` (`email`,`password`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `admin`, `token`) VALUES
(1, 'admin@widget.com', '0cb6cf62a0ea25a7a8a32f892702032bbf790b47d99d9b023b0827bb921af90a', 1, '57edb8173b7ca');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
