/*
SQLyog Community v8.8 Beta2
MySQL - 5.7.19-0ubuntu0.16.04.1-log : Database - 437562092
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `attendance` */

CREATE TABLE `attendance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `arrived` tinyint(1) unsigned DEFAULT NULL,
  `raid_done` tinyint(1) unsigned DEFAULT NULL,
  `cancel` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `i_raidid` (`raid_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13871 DEFAULT CHARSET=utf8;

/*Table structure for table `help` */

CREATE TABLE `help` (
  `id` bigint(20) NOT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `raids` */

CREATE TABLE `raids` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `pokemon` varchar(12) DEFAULT NULL,
  `lat` varchar(11) DEFAULT NULL,
  `lon` varchar(11) DEFAULT NULL,
  `first_seen` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `timezone` char(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `gym_name` varchar(255) DEFAULT NULL,
  `gym_team` enum('mystic','valor','instinct') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `i_endtime` (`end_time`),
  KEY `i_userid` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3682 DEFAULT CHARSET=utf8;

/*Table structure for table `users` */

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `nick` varchar(100) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `team` enum('mystic','valor','instinct') DEFAULT NULL,
  `moderator` tinyint(1) unsigned DEFAULT NULL,
  `timezone` int(10) DEFAULT NULL,
  `lang` varchar(5) DEFAULT NULL,
  `alert_lat` varchar(12) DEFAULT NULL,
  `alert_lon` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `i_userid` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=91175 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


delete from raids where first_seen<"2020-08-27";
alter table raids change pokemon pokemon varchar(15);

