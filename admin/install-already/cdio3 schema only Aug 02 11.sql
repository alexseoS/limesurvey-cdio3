-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 02, 2011 at 10:27 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `limesurvey_cdio3`
--

-- --------------------------------------------------------

--
-- Table structure for table `lime_cdio3_answers`
--

CREATE TABLE IF NOT EXISTS `lime_cdio3_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` int(11) NOT NULL,
  `answer` text COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lime_cdio3_answers`
--


-- --------------------------------------------------------

--
-- Table structure for table `lime_cdio3_learningoutcomes`
--

CREATE TABLE IF NOT EXISTS `lime_cdio3_learningoutcomes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '-1',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lime_cdio3_learningoutcomes`
--


-- --------------------------------------------------------

--
-- Table structure for table `lime_cdio3_questions`
--

CREATE TABLE IF NOT EXISTS `lime_cdio3_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questiontype_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `rawsurvey_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lime_cdio3_questions`
--


-- --------------------------------------------------------

--
-- Table structure for table `lime_cdio3_questiontypes`
--

CREATE TABLE IF NOT EXISTS `lime_cdio3_questiontypes` (
  `id` int(11) NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `code` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lime_cdio3_questiontypes`
--


-- --------------------------------------------------------

--
-- Table structure for table `lime_cdio3_rawsurveys`
--

CREATE TABLE IF NOT EXISTS `lime_cdio3_rawsurveys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lime_cdio3_rawsurveys`
--

