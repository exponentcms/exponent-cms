-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 21, 2010 at 05:41 PM
-- Server version: 5.1.44
-- PHP Version: 5.2.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `webvolut_silversa`
--

-- --------------------------------------------------------

--
-- Table structure for table `exponent_geo_country`
--

CREATE TABLE `exponent_geo_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `iso_code_2letter` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `iso_code_3letter` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `iso_code_number` int(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=240 ;

--
-- Dumping data for table `exponent_geo_country`
--

INSERT INTO `exponent_geo_country` VALUES(1, 'Afghanistan', 'AF', 'AFG', 971);
INSERT INTO `exponent_geo_country` VALUES(2, 'Albania', 'AL', 'ALB', 8);
INSERT INTO `exponent_geo_country` VALUES(3, 'Algeria', 'DZ', 'DZA', 12);
INSERT INTO `exponent_geo_country` VALUES(4, 'American Samoa', 'AS', 'ASM', 840);
INSERT INTO `exponent_geo_country` VALUES(5, 'Andorra', 'AD', 'AND', 978);
INSERT INTO `exponent_geo_country` VALUES(6, 'Angola', 'AO', 'AGO', 973);
INSERT INTO `exponent_geo_country` VALUES(7, 'Anguilla', 'AI', 'AIA', 951);
INSERT INTO `exponent_geo_country` VALUES(8, 'Antarctica', 'AQ', 'ATA', 0);
INSERT INTO `exponent_geo_country` VALUES(9, 'Antigua and Barbuda', 'AG', 'ATG', 951);
INSERT INTO `exponent_geo_country` VALUES(10, 'Argentina', 'AR', 'ARG', 32);
INSERT INTO `exponent_geo_country` VALUES(11, 'Armenia', 'AM', 'ARM', 51);
INSERT INTO `exponent_geo_country` VALUES(12, 'Aruba', 'AW', 'ABW', 533);
INSERT INTO `exponent_geo_country` VALUES(13, 'Australia', 'AU', 'AUS', 36);
INSERT INTO `exponent_geo_country` VALUES(14, 'Austria', 'AT', 'AUT', 978);
INSERT INTO `exponent_geo_country` VALUES(15, 'Azerbaijan', 'AZ', 'AZE', 31);
INSERT INTO `exponent_geo_country` VALUES(16, 'Bahamas', 'BS', 'BHS', 44);
INSERT INTO `exponent_geo_country` VALUES(17, 'Bahrain', 'BH', 'BHR', 48);
INSERT INTO `exponent_geo_country` VALUES(18, 'Bangladesh', 'BD', 'BGD', 50);
INSERT INTO `exponent_geo_country` VALUES(19, 'Barbados', 'BB', 'BRB', 52);
INSERT INTO `exponent_geo_country` VALUES(20, 'Belarus', 'BY', 'BLR', 974);
INSERT INTO `exponent_geo_country` VALUES(21, 'Belgium', 'BE', 'BEL', 978);
INSERT INTO `exponent_geo_country` VALUES(22, 'Belize', 'BZ', 'BLZ', 84);
INSERT INTO `exponent_geo_country` VALUES(23, 'Benin', 'BJ', 'BEN', 952);
INSERT INTO `exponent_geo_country` VALUES(24, 'Bermuda', 'BM', 'BMU', 60);
INSERT INTO `exponent_geo_country` VALUES(25, 'Bhutan', 'BT', 'BTN', 356);
INSERT INTO `exponent_geo_country` VALUES(26, 'Bolivia', 'BO', 'BOL', 68);
INSERT INTO `exponent_geo_country` VALUES(27, 'Bosnia and Herzegowina', 'BA', 'BIH', 977);
INSERT INTO `exponent_geo_country` VALUES(28, 'Botswana', 'BW', 'BWA', 72);
INSERT INTO `exponent_geo_country` VALUES(29, 'Bouvet Island', 'BV', 'BVT', 578);
INSERT INTO `exponent_geo_country` VALUES(30, 'Brazil', 'BR', 'BRA', 986);
INSERT INTO `exponent_geo_country` VALUES(31, 'British Indian Ocean Territory', 'IO', 'IOT', 840);
INSERT INTO `exponent_geo_country` VALUES(32, 'Brunei Darussalam', 'BN', 'BRN', 96);
INSERT INTO `exponent_geo_country` VALUES(33, 'Bulgaria', 'BG', 'BGR', 975);
INSERT INTO `exponent_geo_country` VALUES(34, 'Burkina Faso', 'BF', 'BFA', 952);
INSERT INTO `exponent_geo_country` VALUES(35, 'Burundi', 'BI', 'BDI', 108);
INSERT INTO `exponent_geo_country` VALUES(36, 'Cambodia', 'KH', 'KHM', 116);
INSERT INTO `exponent_geo_country` VALUES(37, 'Cameroon', 'CM', 'CMR', 950);
INSERT INTO `exponent_geo_country` VALUES(38, 'Canada', 'CA', 'CAN', 124);
INSERT INTO `exponent_geo_country` VALUES(39, 'Cape Verde', 'CV', 'CPV', 132);
INSERT INTO `exponent_geo_country` VALUES(40, 'Cayman Islands', 'KY', 'CYM', 136);
INSERT INTO `exponent_geo_country` VALUES(41, 'Central African Republic', 'CF', 'CAF', 950);
INSERT INTO `exponent_geo_country` VALUES(42, 'Chad', 'TD', 'TCD', 950);
INSERT INTO `exponent_geo_country` VALUES(43, 'Chile', 'CL', 'CHL', 152);
INSERT INTO `exponent_geo_country` VALUES(44, 'China', 'CN', 'CHN', 156);
INSERT INTO `exponent_geo_country` VALUES(45, 'Christmas Island', 'CX', 'CXR', 36);
INSERT INTO `exponent_geo_country` VALUES(46, 'Cocos (Keeling) Islands', 'CC', 'CCK', 36);
INSERT INTO `exponent_geo_country` VALUES(47, 'Colombia', 'CO', 'COL', 170);
INSERT INTO `exponent_geo_country` VALUES(48, 'Comoros', 'KM', 'COM', 174);
INSERT INTO `exponent_geo_country` VALUES(49, 'Congo', 'CG', 'COG', 950);
INSERT INTO `exponent_geo_country` VALUES(50, 'Cook Islands', 'CK', 'COK', 554);
INSERT INTO `exponent_geo_country` VALUES(51, 'Costa Rica', 'CR', 'CRI', 188);
INSERT INTO `exponent_geo_country` VALUES(52, 'Cote D''Ivoire', 'CI', 'CIV', 952);
INSERT INTO `exponent_geo_country` VALUES(53, 'Croatia', 'HR', 'HRV', 191);
INSERT INTO `exponent_geo_country` VALUES(54, 'Cuba', 'CU', 'CUB', 192);
INSERT INTO `exponent_geo_country` VALUES(55, 'Cyprus', 'CY', 'CYP', 196);
INSERT INTO `exponent_geo_country` VALUES(56, 'Czech Republic', 'CZ', 'CZE', 203);
INSERT INTO `exponent_geo_country` VALUES(57, 'Denmark', 'DK', 'DNK', 208);
INSERT INTO `exponent_geo_country` VALUES(58, 'Djibouti', 'DJ', 'DJI', 262);
INSERT INTO `exponent_geo_country` VALUES(59, 'Dominica', 'DM', 'DMA', 951);
INSERT INTO `exponent_geo_country` VALUES(60, 'Dominican Republic', 'DO', 'DOM', 214);
INSERT INTO `exponent_geo_country` VALUES(61, 'East Timor', 'TP', 'TMP', 0);
INSERT INTO `exponent_geo_country` VALUES(62, 'Ecuador', 'EC', 'ECU', 840);
INSERT INTO `exponent_geo_country` VALUES(63, 'Egypt', 'EG', 'EGY', 818);
INSERT INTO `exponent_geo_country` VALUES(64, 'El Salvador', 'SV', 'SLV', 222);
INSERT INTO `exponent_geo_country` VALUES(65, 'Equatorial Guinea', 'GQ', 'GNQ', 950);
INSERT INTO `exponent_geo_country` VALUES(66, 'Eritrea', 'ER', 'ERI', 232);
INSERT INTO `exponent_geo_country` VALUES(67, 'Estonia', 'EE', 'EST', 233);
INSERT INTO `exponent_geo_country` VALUES(68, 'Ethiopia', 'ET', 'ETH', 230);
INSERT INTO `exponent_geo_country` VALUES(69, 'Falkland Islands (Malvinas)', 'FK', 'FLK', 238);
INSERT INTO `exponent_geo_country` VALUES(70, 'Faroe Islands', 'FO', 'FRO', 208);
INSERT INTO `exponent_geo_country` VALUES(71, 'Fiji', 'FJ', 'FJI', 242);
INSERT INTO `exponent_geo_country` VALUES(72, 'Finland', 'FI', 'FIN', 978);
INSERT INTO `exponent_geo_country` VALUES(73, 'France', 'FR', 'FRA', 978);
INSERT INTO `exponent_geo_country` VALUES(74, 'France, Metropolitan', 'FX', 'FXX', 978);
INSERT INTO `exponent_geo_country` VALUES(75, 'French Guiana', 'GF', 'GUF', 978);
INSERT INTO `exponent_geo_country` VALUES(76, 'French Polynesia', 'PF', 'PYF', 953);
INSERT INTO `exponent_geo_country` VALUES(77, 'French Southern Territories', 'TF', 'ATF', 978);
INSERT INTO `exponent_geo_country` VALUES(78, 'Gabon', 'GA', 'GAB', 950);
INSERT INTO `exponent_geo_country` VALUES(79, 'Gambia', 'GM', 'GMB', 270);
INSERT INTO `exponent_geo_country` VALUES(80, 'Georgia', 'GE', 'GEO', 981);
INSERT INTO `exponent_geo_country` VALUES(81, 'Germany', 'DE', 'DEU', 978);
INSERT INTO `exponent_geo_country` VALUES(82, 'Ghana', 'GH', 'GHA', 288);
INSERT INTO `exponent_geo_country` VALUES(83, 'Gibraltar', 'GI', 'GIB', 292);
INSERT INTO `exponent_geo_country` VALUES(84, 'Greece', 'GR', 'GRC', 978);
INSERT INTO `exponent_geo_country` VALUES(85, 'Greenland', 'GL', 'GRL', 208);
INSERT INTO `exponent_geo_country` VALUES(86, 'Grenada', 'GD', 'GRD', 951);
INSERT INTO `exponent_geo_country` VALUES(87, 'Guadeloupe', 'GP', 'GLP', 978);
INSERT INTO `exponent_geo_country` VALUES(88, 'Guam', 'GU', 'GUM', 840);
INSERT INTO `exponent_geo_country` VALUES(89, 'Guatemala', 'GT', 'GTM', 320);
INSERT INTO `exponent_geo_country` VALUES(90, 'Guinea', 'GN', 'GIN', 324);
INSERT INTO `exponent_geo_country` VALUES(91, 'Guinea-bissau', 'GW', 'GNB', 624);
INSERT INTO `exponent_geo_country` VALUES(92, 'Guyana', 'GY', 'GUY', 328);
INSERT INTO `exponent_geo_country` VALUES(93, 'Haiti', 'HT', 'HTI', 332);
INSERT INTO `exponent_geo_country` VALUES(94, 'Heard and Mc Donald Islands', 'HM', 'HMD', 36);
INSERT INTO `exponent_geo_country` VALUES(95, 'Honduras', 'HN', 'HND', 340);
INSERT INTO `exponent_geo_country` VALUES(96, 'Hong Kong', 'HK', 'HKG', 344);
INSERT INTO `exponent_geo_country` VALUES(97, 'Hungary', 'HU', 'HUN', 348);
INSERT INTO `exponent_geo_country` VALUES(98, 'Iceland', 'IS', 'ISL', 352);
INSERT INTO `exponent_geo_country` VALUES(99, 'India', 'IN', 'IND', 356);
INSERT INTO `exponent_geo_country` VALUES(100, 'Indonesia', 'ID', 'IDN', 360);
INSERT INTO `exponent_geo_country` VALUES(101, 'Iran (Islamic Republic of)', 'IR', 'IRN', 364);
INSERT INTO `exponent_geo_country` VALUES(102, 'Iraq', 'IQ', 'IRQ', 368);
INSERT INTO `exponent_geo_country` VALUES(103, 'Ireland', 'IE', 'IRL', 978);
INSERT INTO `exponent_geo_country` VALUES(104, 'Israel', 'IL', 'ISR', 376);
INSERT INTO `exponent_geo_country` VALUES(105, 'Italy', 'IT', 'ITA', 978);
INSERT INTO `exponent_geo_country` VALUES(106, 'Jamaica', 'JM', 'JAM', 388);
INSERT INTO `exponent_geo_country` VALUES(107, 'Japan', 'JP', 'JPN', 392);
INSERT INTO `exponent_geo_country` VALUES(108, 'Jordan', 'JO', 'JOR', 400);
INSERT INTO `exponent_geo_country` VALUES(109, 'Kazakhstan', 'KZ', 'KAZ', 398);
INSERT INTO `exponent_geo_country` VALUES(110, 'Kenya', 'KE', 'KEN', 404);
INSERT INTO `exponent_geo_country` VALUES(111, 'Kiribati', 'KI', 'KIR', 36);
INSERT INTO `exponent_geo_country` VALUES(112, 'Korea, Democratic People''s Republic of', 'KP', 'PRK', 408);
INSERT INTO `exponent_geo_country` VALUES(113, 'Korea, Republic of', 'KR', 'KOR', 410);
INSERT INTO `exponent_geo_country` VALUES(114, 'Kuwait', 'KW', 'KWT', 414);
INSERT INTO `exponent_geo_country` VALUES(115, 'Kyrgyzstan', 'KG', 'KGZ', 417);
INSERT INTO `exponent_geo_country` VALUES(116, 'Lao People''s Democratic Republic', 'LA', 'LAO', 418);
INSERT INTO `exponent_geo_country` VALUES(117, 'Latvia', 'LV', 'LVA', 428);
INSERT INTO `exponent_geo_country` VALUES(118, 'Lebanon', 'LB', 'LBN', 422);
INSERT INTO `exponent_geo_country` VALUES(119, 'Lesotho', 'LS', 'LSO', 710);
INSERT INTO `exponent_geo_country` VALUES(120, 'Liberia', 'LR', 'LBR', 430);
INSERT INTO `exponent_geo_country` VALUES(121, 'Libyan Arab Jamahiriya', 'LY', 'LBY', 434);
INSERT INTO `exponent_geo_country` VALUES(122, 'Liechtenstein', 'LI', 'LIE', 756);
INSERT INTO `exponent_geo_country` VALUES(123, 'Lithuania', 'LT', 'LTU', 440);
INSERT INTO `exponent_geo_country` VALUES(124, 'Luxembourg', 'LU', 'LUX', 978);
INSERT INTO `exponent_geo_country` VALUES(125, 'Macao', 'MO', 'MAC', 446);
INSERT INTO `exponent_geo_country` VALUES(126, 'Macedonia, The Former Yugoslav Republic of', 'MK', 'MKD', 807);
INSERT INTO `exponent_geo_country` VALUES(127, 'Madagascar', 'MG', 'MDG', 969);
INSERT INTO `exponent_geo_country` VALUES(128, 'Malawi', 'MW', 'MWI', 454);
INSERT INTO `exponent_geo_country` VALUES(129, 'Malaysia', 'MY', 'MYS', 458);
INSERT INTO `exponent_geo_country` VALUES(130, 'Maldives', 'MV', 'MDV', 462);
INSERT INTO `exponent_geo_country` VALUES(131, 'Mali', 'ML', 'MLI', 952);
INSERT INTO `exponent_geo_country` VALUES(132, 'Malta', 'MT', 'MLT', 470);
INSERT INTO `exponent_geo_country` VALUES(133, 'Marshall Islands', 'MH', 'MHL', 840);
INSERT INTO `exponent_geo_country` VALUES(134, 'Martinique', 'MQ', 'MTQ', 978);
INSERT INTO `exponent_geo_country` VALUES(135, 'Mauritania', 'MR', 'MRT', 478);
INSERT INTO `exponent_geo_country` VALUES(136, 'Mauritius', 'MU', 'MUS', 480);
INSERT INTO `exponent_geo_country` VALUES(137, 'Mayotte', 'YT', 'MYT', 978);
INSERT INTO `exponent_geo_country` VALUES(138, 'Mexico', 'MX', 'MEX', 484);
INSERT INTO `exponent_geo_country` VALUES(139, 'Micronesia, Federated States of', 'FM', 'FSM', 840);
INSERT INTO `exponent_geo_country` VALUES(140, 'Moldova, Republic of', 'MD', 'MDA', 498);
INSERT INTO `exponent_geo_country` VALUES(141, 'Monaco', 'MC', 'MCO', 978);
INSERT INTO `exponent_geo_country` VALUES(142, 'Mongolia', 'MN', 'MNG', 496);
INSERT INTO `exponent_geo_country` VALUES(143, 'Montserrat', 'MS', 'MSR', 951);
INSERT INTO `exponent_geo_country` VALUES(144, 'Morocco', 'MA', 'MAR', 504);
INSERT INTO `exponent_geo_country` VALUES(145, 'Mozambique', 'MZ', 'MOZ', 508);
INSERT INTO `exponent_geo_country` VALUES(146, 'Myanmar', 'MM', 'MMR', 104);
INSERT INTO `exponent_geo_country` VALUES(147, 'Namibia', 'NA', 'NAM', 710);
INSERT INTO `exponent_geo_country` VALUES(148, 'Nauru', 'NR', 'NRU', 36);
INSERT INTO `exponent_geo_country` VALUES(149, 'Nepal', 'NP', 'NPL', 524);
INSERT INTO `exponent_geo_country` VALUES(150, 'Netherlands', 'NL', 'NLD', 978);
INSERT INTO `exponent_geo_country` VALUES(151, 'Netherlands Antilles', 'AN', 'ANT', 532);
INSERT INTO `exponent_geo_country` VALUES(152, 'New Caledonia', 'NC', 'NCL', 953);
INSERT INTO `exponent_geo_country` VALUES(153, 'New Zealand', 'NZ', 'NZL', 554);
INSERT INTO `exponent_geo_country` VALUES(154, 'Nicaragua', 'NI', 'NIC', 558);
INSERT INTO `exponent_geo_country` VALUES(155, 'Niger', 'NE', 'NER', 952);
INSERT INTO `exponent_geo_country` VALUES(156, 'Nigeria', 'NG', 'NGA', 566);
INSERT INTO `exponent_geo_country` VALUES(157, 'Niue', 'NU', 'NIU', 554);
INSERT INTO `exponent_geo_country` VALUES(158, 'Norfolk Island', 'NF', 'NFK', 36);
INSERT INTO `exponent_geo_country` VALUES(159, 'Northern Mariana Islands', 'MP', 'MNP', 840);
INSERT INTO `exponent_geo_country` VALUES(160, 'Norway', 'NO', 'NOR', 578);
INSERT INTO `exponent_geo_country` VALUES(161, 'Oman', 'OM', 'OMN', 512);
INSERT INTO `exponent_geo_country` VALUES(162, 'Pakistan', 'PK', 'PAK', 586);
INSERT INTO `exponent_geo_country` VALUES(163, 'Palau', 'PW', 'PLW', 840);
INSERT INTO `exponent_geo_country` VALUES(164, 'Panama', 'PA', 'PAN', 590);
INSERT INTO `exponent_geo_country` VALUES(165, 'Papua New Guinea', 'PG', 'PNG', 598);
INSERT INTO `exponent_geo_country` VALUES(166, 'Paraguay', 'PY', 'PRY', 600);
INSERT INTO `exponent_geo_country` VALUES(167, 'Peru', 'PE', 'PER', 604);
INSERT INTO `exponent_geo_country` VALUES(168, 'Philippines', 'PH', 'PHL', 608);
INSERT INTO `exponent_geo_country` VALUES(169, 'Pitcairn', 'PN', 'PCN', 554);
INSERT INTO `exponent_geo_country` VALUES(170, 'Poland', 'PL', 'POL', 985);
INSERT INTO `exponent_geo_country` VALUES(171, 'Portugal', 'PT', 'PRT', 978);
INSERT INTO `exponent_geo_country` VALUES(172, 'Puerto Rico', 'PR', 'PRI', 840);
INSERT INTO `exponent_geo_country` VALUES(173, 'Qatar', 'QA', 'QAT', 634);
INSERT INTO `exponent_geo_country` VALUES(174, 'Reunion', 'RE', 'REU', 978);
INSERT INTO `exponent_geo_country` VALUES(175, 'Romania', 'RO', 'ROM', 642);
INSERT INTO `exponent_geo_country` VALUES(176, 'Russian Federation', 'RU', 'RUS', 810);
INSERT INTO `exponent_geo_country` VALUES(177, 'Rwanda', 'RW', 'RWA', 646);
INSERT INTO `exponent_geo_country` VALUES(178, 'Saint Kitts and Nevis', 'KN', 'KNA', 951);
INSERT INTO `exponent_geo_country` VALUES(179, 'Saint Lucia', 'LC', 'LCA', 951);
INSERT INTO `exponent_geo_country` VALUES(180, 'Saint Vincent and the Grenadines', 'VC', 'VCT', 951);
INSERT INTO `exponent_geo_country` VALUES(181, 'Samoa', 'WS', 'WSM', 882);
INSERT INTO `exponent_geo_country` VALUES(182, 'San Marino', 'SM', 'SMR', 978);
INSERT INTO `exponent_geo_country` VALUES(183, 'Sao Tome and Principe', 'ST', 'STP', 678);
INSERT INTO `exponent_geo_country` VALUES(184, 'Saudi Arabia', 'SA', 'SAU', 682);
INSERT INTO `exponent_geo_country` VALUES(185, 'Senegal', 'SN', 'SEN', 952);
INSERT INTO `exponent_geo_country` VALUES(186, 'Seychelles', 'SC', 'SYC', 690);
INSERT INTO `exponent_geo_country` VALUES(187, 'Sierra Leone', 'SL', 'SLE', 694);
INSERT INTO `exponent_geo_country` VALUES(188, 'Singapore', 'SG', 'SGP', 702);
INSERT INTO `exponent_geo_country` VALUES(189, 'Slovakia (Slovak Republic)', 'SK', 'SVK', 703);
INSERT INTO `exponent_geo_country` VALUES(190, 'Slovenia', 'SI', 'SVN', 705);
INSERT INTO `exponent_geo_country` VALUES(191, 'Solomon Islands', 'SB', 'SLB', 90);
INSERT INTO `exponent_geo_country` VALUES(192, 'Somalia', 'SO', 'SOM', 706);
INSERT INTO `exponent_geo_country` VALUES(193, 'South Africa', 'ZA', 'ZAF', 710);
INSERT INTO `exponent_geo_country` VALUES(194, 'South Georgia and the South Sandwich Islands', 'GS', 'SGS', 0);
INSERT INTO `exponent_geo_country` VALUES(195, 'Spain', 'ES', 'ESP', 978);
INSERT INTO `exponent_geo_country` VALUES(196, 'Sri Lanka', 'LK', 'LKA', 144);
INSERT INTO `exponent_geo_country` VALUES(197, 'St. Helena', 'SH', 'SHN', 654);
INSERT INTO `exponent_geo_country` VALUES(198, 'St. Pierre and Miquelon', 'PM', 'SPM', 978);
INSERT INTO `exponent_geo_country` VALUES(199, 'Sudan', 'SD', 'SDN', 736);
INSERT INTO `exponent_geo_country` VALUES(200, 'Suriname', 'SR', 'SUR', 968);
INSERT INTO `exponent_geo_country` VALUES(201, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM', 578);
INSERT INTO `exponent_geo_country` VALUES(202, 'Swaziland', 'SZ', 'SWZ', 748);
INSERT INTO `exponent_geo_country` VALUES(203, 'Sweden', 'SE', 'SWE', 752);
INSERT INTO `exponent_geo_country` VALUES(204, 'Switzerland', 'CH', 'CHE', 756);
INSERT INTO `exponent_geo_country` VALUES(205, 'Syrian Arab Republic', 'SY', 'SYR', 760);
INSERT INTO `exponent_geo_country` VALUES(206, 'Taiwan', 'TW', 'TWN', 901);
INSERT INTO `exponent_geo_country` VALUES(207, 'Tajikistan', 'TJ', 'TJK', 972);
INSERT INTO `exponent_geo_country` VALUES(208, 'Tanzania, United Republic of', 'TZ', 'TZA', 834);
INSERT INTO `exponent_geo_country` VALUES(209, 'Thailand', 'TH', 'THA', 764);
INSERT INTO `exponent_geo_country` VALUES(210, 'Togo', 'TG', 'TGO', 952);
INSERT INTO `exponent_geo_country` VALUES(211, 'Tokelau', 'TK', 'TKL', 554);
INSERT INTO `exponent_geo_country` VALUES(212, 'Tonga', 'TO', 'TON', 776);
INSERT INTO `exponent_geo_country` VALUES(213, 'Trinidad and Tobago', 'TT', 'TTO', 780);
INSERT INTO `exponent_geo_country` VALUES(214, 'Tunisia', 'TN', 'TUN', 788);
INSERT INTO `exponent_geo_country` VALUES(215, 'Turkey', 'TR', 'TUR', 792);
INSERT INTO `exponent_geo_country` VALUES(216, 'Turkmenistan', 'TM', 'TKM', 795);
INSERT INTO `exponent_geo_country` VALUES(217, 'Turks and Caicos Islands', 'TC', 'TCA', 840);
INSERT INTO `exponent_geo_country` VALUES(218, 'Tuvalu', 'TV', 'TUV', 36);
INSERT INTO `exponent_geo_country` VALUES(219, 'Uganda', 'UG', 'UGA', 800);
INSERT INTO `exponent_geo_country` VALUES(220, 'Ukraine', 'UA', 'UKR', 980);
INSERT INTO `exponent_geo_country` VALUES(221, 'United Arab Emirates', 'AE', 'ARE', 784);
INSERT INTO `exponent_geo_country` VALUES(222, 'United Kingdom', 'GB', 'GBR', 826);
INSERT INTO `exponent_geo_country` VALUES(223, 'United States', 'US', 'USA', 840);
INSERT INTO `exponent_geo_country` VALUES(224, 'United States Minor Outlying Islands', 'UM', 'UMI', 840);
INSERT INTO `exponent_geo_country` VALUES(225, 'Uruguay', 'UY', 'URY', 858);
INSERT INTO `exponent_geo_country` VALUES(226, 'Uzbekistan', 'UZ', 'UZB', 860);
INSERT INTO `exponent_geo_country` VALUES(227, 'Vanuatu', 'VU', 'VUT', 548);
INSERT INTO `exponent_geo_country` VALUES(228, 'Vatican City State (Holy See)', 'VA', 'VAT', 978);
INSERT INTO `exponent_geo_country` VALUES(229, 'Venezuela', 'VE', 'VEN', 862);
INSERT INTO `exponent_geo_country` VALUES(230, 'Viet Nam', 'VN', 'VNM', 704);
INSERT INTO `exponent_geo_country` VALUES(231, 'Virgin Islands (British)', 'VG', 'VGB', 840);
INSERT INTO `exponent_geo_country` VALUES(232, 'Virgin Islands (U.S.)', 'VI', 'VIR', 840);
INSERT INTO `exponent_geo_country` VALUES(233, 'Wallis and Futuna Islands', 'WF', 'WLF', 953);
INSERT INTO `exponent_geo_country` VALUES(234, 'Western Sahara', 'EH', 'ESH', 504);
INSERT INTO `exponent_geo_country` VALUES(235, 'Yemen', 'YE', 'YEM', 886);
INSERT INTO `exponent_geo_country` VALUES(236, 'Yugoslavia', 'YU', 'YUG', 0);
INSERT INTO `exponent_geo_country` VALUES(237, 'Zaire', 'ZR', 'ZAR', 0);
INSERT INTO `exponent_geo_country` VALUES(238, 'Zambia', 'ZM', 'ZMB', 894);
INSERT INTO `exponent_geo_country` VALUES(239, 'Zimbabwe', 'ZW', 'ZWE', 716);
