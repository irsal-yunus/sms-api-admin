-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: 10.32.6.242:3306
-- Generation Time: Oct 08, 2010 at 10:59 AM
-- Server version: 5.0.77
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `SMS_API_V22`
--

-- --------------------------------------------------------

--
-- Table structure for table `COUNTRY`
--

CREATE TABLE IF NOT EXISTS `COUNTRY` (
  `COUNTRY_CODE` varchar(3) NOT NULL,
  `COUNTRY_NAME` varchar(30) character set utf8 NOT NULL,
  PRIMARY KEY  (`COUNTRY_CODE`),
  UNIQUE KEY `COUNTRY_NAME` (`COUNTRY_NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `COUNTRY`
--

INSERT INTO `COUNTRY` (`COUNTRY_CODE`, `COUNTRY_NAME`) VALUES
('AUS', 'Australia'),
('DEU', 'Germany'),
('IDN', 'Indonesia'),
('JPN', 'Japan'),
('SGP', 'Singapore'),
('GBR', 'United Kingdom'),
('USA', 'United States');