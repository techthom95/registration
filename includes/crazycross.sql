-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server versie:                5.6.20 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Versie:              9.2.0.4947
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Structuur van  tabel crazycross.cc_classes wordt geschreven
CREATE TABLE `cc_classes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `location` ENUM('bergeijk', 'lommel') NOT NULL DEFAULT 'bergeijk',
  `name` varchar(255) DEFAULT NULL,
  `intro` text,
  `dates` text,
  `num_participants` int(11) DEFAULT NULL,
  `max_groups` int(11) DEFAULT NULL,
  `age_limit_type` ENUM('min','max') NULL,
  `age_limit` INT NULL,
  `driving_license` tinyint(1) DEFAULT '0',
  `driving_license_upload` tinyint(1) DEFAULT '0',
  `footer_note` text,
  `price_text` text,
  `price_per_person` DECIMAL(10,2) DEFAULT NULL,
  `price_fixed` DECIMAL(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporteren was gedeselecteerd


-- Structuur van  tabel crazycross.cc_groups wordt geschreven
CREATE TABLE `cc_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `date` text,
  `theme` text,
  `year` year,
  `location` ENUM('bergeijk', 'lommel') NOT NULL DEFAULT 'bergeijk',
  `status` tinyint(1) NOT NULL DEFAULT 2,
  `start_nr` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporteren was gedeselecteerd


-- Structuur van  tabel crazycross.cc_participants wordt geschreven
CREATE TABLE `cc_participants` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `phonenumber` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `insertion` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `driving_license_nr` varchar(255) DEFAULT NULL,
  `driving_license_path` varchar(255) DEFAULT NULL,
  `notice` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cc_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cc_payment_transactions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL,
  `status` varchar(255) NOT NULL,
  `currency` varchar(255) NOT NULL DEFAULT 'EUR',
  `amount` DECIMAL(10,2) NOT NULL,
  `mollie_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mollie_id` (`mollie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporteren was gedeselecteerd
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
