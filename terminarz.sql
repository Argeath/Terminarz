-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb5+lenny9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 12, 2015 at 04:39 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6-1+lenny16

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `terminarz`
--

-- --------------------------------------------------------

--
-- Table structure for table `numerek`
--

CREATE TABLE IF NOT EXISTS `numerek` (
  `id` int(11) NOT NULL auto_increment,
  `numer` int(3) NOT NULL,
  `data` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `reset` int(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `term_accounts`
--

CREATE TABLE IF NOT EXISTS `term_accounts` (
  `id` int(30) NOT NULL auto_increment,
  `login` varchar(30) default NULL,
  `haslo` varchar(255) default NULL COMMENT 'kodowane w SHA1',
  `imie` varchar(30) NOT NULL,
  `nazwisko` varchar(30) NOT NULL,
  `dostep` int(11) NOT NULL default '1' COMMENT '0 - brak dostepu, 1 - konto zwykle, 2 - administrator',
  `starehaslo` text COMMENT 'Na wypadek, gdyby ktos jednak chcial stare haslo',
  `hasl` varchar(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `term_klasy`
--

CREATE TABLE IF NOT EXISTS `term_klasy` (
  `id` int(11) NOT NULL auto_increment,
  `nazwa` varchar(11) collate utf8_unicode_ci NOT NULL,
  `laczona` int(2) NOT NULL default '0',
  `specjalna` int(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=40 ;

--
-- Dumping data for table `term_klasy`
--

INSERT INTO `term_klasy` (`id`, `nazwa`, `laczona`, `specjalna`) VALUES
(1, '1A', 0, 0),
(2, '1B', 0, 0),
(3, '1C', 0, 0),
(4, '1D', 0, 0),
(5, '1E', 0, 0),
(6, '1F', 0, 0),
(7, '1H', 0, 0),
(8, '2A', 0, 0),
(9, '2B', 0, 0),
(10, '2C', 0, 0),
(11, '2D', 0, 0),
(12, '2E', 0, 0),
(13, '2F', 0, 0),
(14, '2H', 0, 0),
(15, '3A', 0, 0),
(16, '3B', 0, 0),
(17, '3C', 0, 0),
(18, '3D', 0, 0),
(19, '3E', 0, 0),
(20, '3F', 0, 0),
(21, '3H', 0, 0),
(22, '4A', 0, 0),
(23, '4B', 0, 0),
(24, '4C', 0, 0),
(25, '4D', 0, 0),
(26, '4E', 0, 0),
(27, '4F', 0, 0),
(28, '4H', 0, 0),
(30, '4AB', 1, 0),
(34, '4CDE', 1, 0),
(33, '1G', 0, 0),
(35, 'Ogłoszenia', 0, 1),
(36, 'Etyka', 1, 0),
(37, 'WF/dz', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `term_wpisy`
--

CREATE TABLE IF NOT EXISTS `term_wpisy` (
  `id` int(255) NOT NULL auto_increment,
  `data` date NOT NULL,
  `klasa` int(11) NOT NULL,
  `dodajacy` int(11) NOT NULL,
  `kategoria` enum('OGL','ZAD','KARTK','SPR','PPK','INNE','POW') collate utf8_unicode_ci NOT NULL,
  `text` text collate utf8_unicode_ci NOT NULL,
  `przedmiot` varchar(50) character set utf8 NOT NULL,
  `dodane` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `grupowy` int(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `term_zastepstwa`
--

CREATE TABLE IF NOT EXISTS `term_zastepstwa` (
  `id` int(11) NOT NULL auto_increment,
  `data` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `nauczyciel` int(11) NOT NULL,
  `klasa` int(11) NOT NULL,
  `godzina_lekcyjna` varchar(5) collate utf8_unicode_ci NOT NULL,
  `z` text character set utf8 NOT NULL,
  `na` text character set utf8,
  `nauczyciel_na` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;