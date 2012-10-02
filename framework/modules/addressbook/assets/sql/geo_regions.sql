-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 25, 2011 at 06:14 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `oiclient_centec`
--

-- --------------------------------------------------------

--
-- Table structure for table `exponent_geo_region`
--

CREATE TABLE IF NOT EXISTS `exponent_geo_region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=182 ;

--
-- Dumping data for table `exponent_geo_region`
--

INSERT INTO `exponent_geo_region` VALUES(1, 'Alabama', 'AL', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(2, 'Alaska', 'AK', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(3, 'American Samoa', 'AS', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(4, 'Arizona', 'AZ', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(5, 'Arkansas', 'AR', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(6, 'Armed Forces Africa', 'AF', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(7, 'Armed Forces Americas', 'AA', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(8, 'Armed Forces Canada', 'AC', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(9, 'Armed Forces Europe', 'AE', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(10, 'Armed Forces Middle East', 'AM', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(11, 'Armed Forces Pacific', 'AP', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(12, 'California', 'CA', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(13, 'Colorado', 'CO', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(14, 'Connecticut', 'CT', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(15, 'Delaware', 'DE', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(16, 'District of Columbia', 'DC', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(17, 'Federated States Of Micronesia', 'FM', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(18, 'Florida', 'FL', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(19, 'Georgia', 'GA', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(20, 'Guam', 'GU', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(21, 'Hawaii', 'HI', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(22, 'Idaho', 'ID', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(23, 'Illinois', 'IL', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(24, 'Indiana', 'IN', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(25, 'Iowa', 'IA', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(26, 'Kansas', 'KS', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(27, 'Kentucky', 'KY', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(28, 'Louisiana', 'LA', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(29, 'Maine', 'ME', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(30, 'Marshall Islands', 'MH', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(31, 'Maryland', 'MD', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(32, 'Massachusetts', 'MA', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(33, 'Michigan', 'MI', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(34, 'Minnesota', 'MN', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(35, 'Mississippi', 'MS', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(36, 'Missouri', 'MO', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(37, 'Montana', 'MT', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(38, 'Nebraska', 'NE', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(39, 'Nevada', 'NV', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(40, 'New Hampshire', 'NH', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(41, 'New Jersey', 'NJ', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(42, 'New Mexico', 'NM', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(43, 'New York', 'NY', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(44, 'North Carolina', 'NC', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(45, 'North Dakota', 'ND', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(46, 'Northern Mariana Islands', 'MP', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(47, 'Ohio', 'OH', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(48, 'Oklahoma', 'OK', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(49, 'Oregon', 'OR', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(50, 'Palau', 'PW', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(51, 'Pennsylvania', 'PA', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(52, 'Puerto Rico', 'PR', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(53, 'Rhode Island', 'RI', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(54, 'South Carolina', 'SC', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(55, 'South Dakota', 'SD', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(56, 'Tennessee', 'TN', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(57, 'Texas', 'TX', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(58, 'Utah', 'UT', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(59, 'Vermont', 'VT', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(60, 'Virgin Islands', 'VI', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(61, 'Virginia', 'VA', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(62, 'Washington', 'WA', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(63, 'West Virginia', 'WV', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(64, 'Wisconsin', 'WI', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(65, 'Wyoming', 'WY', 223, 0, 1);
INSERT INTO `exponent_geo_region` VALUES(66, 'Alberta', 'AB', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(67, 'British Columbia', 'BC', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(68, 'Manitoba', 'MB', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(69, 'Newfoundland', 'NF', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(70, 'New Brunswick', 'NB', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(71, 'Nova Scotia', 'NS', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(72, 'Northwest Territories', 'NT', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(73, 'Nunavut', 'NU', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(74, 'Ontario', 'ON', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(75, 'Prince Edward Island', 'PE', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(76, 'Quebec', 'QC', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(77, 'Saskatchewan', 'SK', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(78, 'Yukon Territory', 'YT', 38, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(79, 'Niedersachsen', 'NDS', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(81, 'Bayern', 'BAY', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(82, 'Berlin', 'BER', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(83, 'Brandenburg', 'BRG', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(84, 'Bremen', 'BRE', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(85, 'Hamburg', 'HAM', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(86, 'Hessen', 'HES', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(87, 'Mecklenburg-Vorpommern', 'MEC', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(88, 'Nordrhein-Westfalen', 'NRW', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(89, 'Rheinland-Pfalz', 'RHE', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(90, 'Saarland', 'SAR', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(91, 'Sachsen', 'SAS', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(92, 'Sachsen-Anhalt', 'SAC', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(93, 'Schleswig-Holstein', 'SCN', 81, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(95, 'Wien', 'WI', 14, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(98, 'Salzburg', 'SB', 14, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(100, 'Steiermark', 'ST', 14, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(101, 'Tirol', 'TI', 14, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(102, 'Burgenland', 'BL', 14, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(103, 'Voralberg', 'VB', 14, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(104, 'Aargau', 'AG', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(105, 'Appenzell Innerrhoden', 'AI', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(106, 'Appenzell Ausserrhoden', 'AR', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(107, 'Bern', 'BE', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(108, 'Basel-Landschaft', 'BL', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(109, 'Basel-Stadt', 'BS', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(110, 'Freiburg', 'FR', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(111, 'Genf', 'GE', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(112, 'Glarus', 'GL', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(114, 'Jura', 'JU', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(115, 'Luzern', 'LU', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(116, 'Neuenburg', 'NE', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(117, 'Nidwalden', 'NW', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(118, 'Obwalden', 'OW', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(119, 'St. Gallen', 'SG', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(120, 'Schaffhausen', 'SH', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(121, 'Solothurn', 'SO', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(122, 'Schwyz', 'SZ', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(123, 'Thurgau', 'TG', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(124, 'Tessin', 'TI', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(125, 'Uri', 'UR', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(126, 'Waadt', 'VD', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(127, 'Wallis', 'VS', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(128, 'Zug', 'ZG', 204, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(131, 'Alava', 'Alava', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(132, 'Albacete', 'Albacete', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(133, 'Alicante', 'Alicante', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(134, 'Almeria', 'Almeria', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(135, 'Asturias', 'Asturias', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(136, 'Avila', 'Avila', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(137, 'Badajoz', 'Badajoz', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(138, 'Baleares', 'Baleares', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(139, 'Barcelona', 'Barcelona', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(140, 'Burgos', 'Burgos', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(141, 'Caceres', 'Caceres', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(142, 'Cadiz', 'Cadiz', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(143, 'Cantabria', 'Cantabria', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(144, 'Castellon', 'Castellon', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(145, 'Ceuta', 'Ceuta', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(146, 'Ciudad Real', 'Ciudad Real', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(147, 'Cordoba', 'Cordoba', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(148, 'Cuenca', 'Cuenca', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(149, 'Girona', 'Girona', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(150, 'Granada', 'Granada', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(151, 'Guadalajara', 'Guadalajara', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(152, 'Guipuzcoa', 'Guipuzcoa', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(153, 'Huelva', 'Huelva', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(154, 'Huesca', 'Huesca', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(155, 'Jaen', 'Jaen', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(156, 'La Rioja', 'La Rioja', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(157, 'Las Palmas', 'Las Palmas', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(158, 'Leon', 'Leon', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(159, 'Lleida', 'Lleida', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(160, 'Lugo', 'Lugo', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(161, 'Madrid', 'Madrid', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(162, 'Malaga', 'Malaga', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(163, 'Melilla', 'Melilla', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(164, 'Murcia', 'Murcia', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(165, 'Navarra', 'Navarra', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(166, 'Ourense', 'Ourense', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(167, 'Palencia', 'Palencia', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(168, 'Pontevedra', 'Pontevedra', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(169, 'Salamanca', 'Salamanca', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(170, 'Santa Cruz de Tenerife', 'Santa Cruz de Teneri', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(171, 'Segovia', 'Segovia', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(172, 'Sevilla', 'Sevilla', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(173, 'Soria', 'Soria', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(174, 'Tarragona', 'Tarragona', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(175, 'Teruel', 'Teruel', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(176, 'Toledo', 'Toledo', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(177, 'Valencia', 'Valencia', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(178, 'Valladolid', 'Valladolid', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(179, 'Vizcaya', 'Vizcaya', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(180, 'Zamora', 'Zamora', 195, 0, 0);
INSERT INTO `exponent_geo_region` VALUES(181, 'Zaragoza', 'Zaragoza', 195, 0, 0);
