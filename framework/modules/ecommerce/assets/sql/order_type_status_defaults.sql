-- phpMyAdmin SQL Dump
-- version 3.3.10.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 26, 2011 at 12:58 AM
-- Server version: 5.0.92
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `oiclient_kitchen`
--

-- --------------------------------------------------------

--
-- Table structure for table `exponent_order_status`
--

CREATE TABLE IF NOT EXISTS `exponent_order_status` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) collate utf8_unicode_ci NOT NULL,
  `rank` int(8) NOT NULL,
  `treat_as_closed` tinyint(1) NOT NULL,
  `is_default` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=6 ;

--
-- Dumping data for table `exponent_order_status`
--

INSERT INTO `exponent_order_status` (`id`, `title`, `rank`, `treat_as_closed`, `is_default`) VALUES
(1, 'New order has been received', 1, 0, 1),
(2, 'Processing', 2, 0, 0),
(3, 'Order has been shipped', 3, 1, 0),
(4, 'Order Complete', 4, 1, 0),
(5, 'Cancelled', 5, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `exponent_order_type`
--

CREATE TABLE IF NOT EXISTS `exponent_order_type` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) collate utf8_unicode_ci NOT NULL,
  `is_default` int(8) NOT NULL,
  `creates_new_user` int(8) NOT NULL,
  `emails_customer` int(8) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

--
-- Dumping data for table `exponent_order_type`
--

INSERT INTO `exponent_order_type` (`id`, `title`, `is_default`, `creates_new_user`, `emails_customer`) VALUES
(1, 'User', 1, 1, 1);
