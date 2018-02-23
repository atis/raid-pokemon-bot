-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 23. Feb 2018 um 13:27
-- Server Version: 5.5.58-0+deb8u1
-- PHP-Version: 5.6.33-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `raidbot`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attendance`
--

CREATE TABLE IF NOT EXISTS `attendance` (
`id` int(10) unsigned NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `raid_id` int(10) unsigned DEFAULT NULL,
  `attend_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `team` enum('mystic','valor','instinct') DEFAULT NULL,
  `extra_people` int(10) unsigned DEFAULT NULL,
  `extra_1_team` enum('mystic','valor','instinct') DEFAULT NULL,
  `extra_2_team` enum('mystic','valor','instinct') DEFAULT NULL,
  `extra_3_team` enum('mystic','valor','instinct') DEFAULT NULL,
  `extra_4_team` enum('mystic','valor','instinct') DEFAULT NULL,
  `arrived` tinyint(1) unsigned DEFAULT '0',
  `raid_done` tinyint(1) unsigned DEFAULT '0',
  `cancel` tinyint(1) unsigned DEFAULT '0',
  `pokemon` varchar(12) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cleanup`
--

CREATE TABLE IF NOT EXISTS `cleanup` (
`id` int(10) unsigned NOT NULL,
  `raid_id` int(10) unsigned NOT NULL,
  `chat_id` bigint(20) NOT NULL,
  `message_id` bigint(20) unsigned NOT NULL,
  `cleaned` int(10) unsigned DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gyms`
--

CREATE TABLE IF NOT EXISTS `gyms` (
`id` int(10) unsigned NOT NULL,
  `lat` varchar(11) DEFAULT NULL,
  `lon` varchar(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `gym_name` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `overview`
--

CREATE TABLE IF NOT EXISTS `overview` (
`id` int(10) unsigned NOT NULL,
  `chat_id` bigint(20) NOT NULL,
  `message_id` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `raids`
--

CREATE TABLE IF NOT EXISTS `raids` (
`id` int(10) unsigned NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `pokemon` varchar(12) DEFAULT NULL,
  `lat` varchar(11) DEFAULT NULL,
  `lon` varchar(11) DEFAULT NULL,
  `first_seen` datetime DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `timezone` char(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `gym_name` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `gym_team` enum('mystic','valor','instinct') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(10) unsigned NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `nick` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 DEFAULT NULL,
  `team` enum('mystic','valor','instinct') DEFAULT NULL,
  `moderator` tinyint(1) unsigned DEFAULT NULL,
  `timezone` int(10) DEFAULT NULL,
  `lang` varchar(5) DEFAULT NULL,
  `alert_lat` varchar(12) DEFAULT NULL,
  `alert_lon` varchar(12) DEFAULT NULL,
  `level` int(10) unsigned DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `attendance`
--
ALTER TABLE `attendance`
 ADD PRIMARY KEY (`id`), ADD KEY `raid_id` (`raid_id`);

--
-- Indizes für die Tabelle `cleanup`
--
ALTER TABLE `cleanup`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `gyms`
--
ALTER TABLE `gyms`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `overview`
--
ALTER TABLE `overview`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `raids`
--
ALTER TABLE `raids`
 ADD PRIMARY KEY (`id`), ADD KEY `end_time` (`end_time`), ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `i_userid` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
