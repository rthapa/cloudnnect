-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2014 at 03:55 AM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `social`
--

-- --------------------------------------------------------

--
-- Table structure for table `blockedusers`
--

CREATE TABLE IF NOT EXISTS `blockedusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blocker` varchar(16) NOT NULL,
  `blockee` varchar(16) NOT NULL,
  `blockdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_size` int(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `type` enum('a','b','c','d') NOT NULL DEFAULT 'a',
  `description` varchar(255) DEFAULT NULL,
  `keywords` varchar(25) DEFAULT NULL,
  `uploaddate` datetime NOT NULL,
  `totalDownloads` int(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=75 ;

-- --------------------------------------------------------

--
-- Table structure for table `filegroups`
--

CREATE TABLE IF NOT EXISTS `filegroups` (
  `fgId` int(11) NOT NULL AUTO_INCREMENT,
  `fId` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `createDate` datetime NOT NULL,
  PRIMARY KEY (`fgId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `follow`
--

CREATE TABLE IF NOT EXISTS `follow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `follower` varchar(16) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `following` varchar(16) NOT NULL,
  `following_id` int(11) NOT NULL,
  `datemade` date NOT NULL,
  `accepted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `follow`
--

INSERT INTO `follow` (`id`, `follower`, `follower_id`, `following`, `following_id`, `datemade`, `accepted`) VALUES
(9, 'rabi', 3, 'jhon', 4, '2014-03-31', '1'),
(11, 'rabi', 3, 'sheela', 5, '2014-03-31', '1'),
(12, 'something2', 6, 'rabi', 3, '2014-04-04', '1'),
(17, 'jhon', 4, 'rabi', 3, '2014-04-20', '0');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user1` varchar(16) NOT NULL,
  `user2` varchar(16) NOT NULL,
  `datemade` datetime NOT NULL,
  `accepted` enum('0','1') NOT NULL DEFAULT '0',
  `follower` varchar(16) NOT NULL,
  `following` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `user1`, `user2`, `datemade`, `accepted`, `follower`, `following`) VALUES
(2, 'sheela', 'rabi', '2014-01-03 01:16:37', '1', '', ''),
(3, 'sheela', 'jhon', '2014-01-03 01:17:22', '1', '', ''),
(6, 'rabi', 'jhon', '2014-02-12 23:29:42', '1', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `groupId` int(11) NOT NULL AUTO_INCREMENT,
  `groupTitle` varchar(16) NOT NULL,
  `groupDesc` text NOT NULL,
  `groupAvatar` varchar(255) NOT NULL,
  `privacy` varchar(6) NOT NULL DEFAULT '0',
  `creator` varchar(255) NOT NULL,
  `createDate` datetime NOT NULL,
  PRIMARY KEY (`groupId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `likedislike`
--

CREATE TABLE IF NOT EXISTS `likedislike` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `fileId` int(16) NOT NULL,
  `type` enum('like','dislike') NOT NULL DEFAULT 'like',
  `dateAdded` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL,
  `initiator` varchar(16) NOT NULL,
  `app` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `did_read` enum('0','1') NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(16) NOT NULL,
  `gallery` varchar(16) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `uploaddate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `osid` int(11) NOT NULL,
  `account_name` varchar(16) NOT NULL,
  `author` varchar(16) NOT NULL,
  `type` enum('a','b','c') NOT NULL,
  `data` text NOT NULL,
  `postdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `useroptions`
--

CREATE TABLE IF NOT EXISTS `useroptions` (
  `id` int(11) NOT NULL,
  `username` varchar(16) NOT NULL,
  `background` varchar(255) NOT NULL,
  `question` varchar(255) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `temp_pass` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `useroptions`
--

INSERT INTO `useroptions` (`id`, `username`, `background`, `question`, `answer`, `temp_pass`) VALUES
(1, 'rabi', 'original', NULL, NULL, 'fffcb879e8ee5beb79936cf9122553cd'),
(4, 'jhon', 'original', NULL, NULL, ''),
(5, 'sheela', 'original', NULL, NULL, ''),
(6, 'something2', 'original', NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('m','f') NOT NULL,
  `dob` date NOT NULL,
  `country` varchar(255) DEFAULT NULL,
  `userlevel` enum('a','b','c','d') NOT NULL DEFAULT 'a',
  `userRank` varchar(16) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `about` text NOT NULL,
  `ip` varchar(255) NOT NULL,
  `signup` datetime NOT NULL,
  `lastlogin` datetime NOT NULL,
  `notesCheck` datetime NOT NULL,
  `activated` enum('0','1') NOT NULL DEFAULT '0',
  `totalDownloads` int(16) NOT NULL DEFAULT '0',
  `totalUploads` int(16) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `gender`, `dob`, `country`, `userlevel`, `userRank`, `avatar`, `about`, `ip`, `signup`, `lastlogin`, `notesCheck`, `activated`, `totalDownloads`, `totalUploads`) VALUES
(3, 'rabi', 'rthapa90@gmail.com', '96b9c62c86f35c209c5b8c302ba34175', 'm', '1990-09-16', 'United Kingdom', 'a', 'Rookie', '241636226.jpg', 'My name is bla and bla', '127.0.0.1', '2013-12-30 17:45:43', '2014-04-25 02:53:21', '2014-04-20 19:43:47', '1', 6, 75),
(4, 'jhon', 'something@gmail.com', '96b9c62c86f35c209c5b8c302ba34175', 'm', '1999-12-30', 'United Kingdom', 'a', 'Rookie', '-306826584.jpg', 'My name is Jhon and I am a AI... Hhihiihi I share stuff to the internet. lol', '127.0.0.1', '2014-01-02 21:09:37', '2014-04-20 19:43:18', '2014-04-13 18:55:16', '1', 0, 9),
(5, 'sheela', 'something1@gmail.com', '96b9c62c86f35c209c5b8c302ba34175', 'm', '1999-02-12', 'United Kingdom', 'a', 'Rookie', NULL, '', '127.0.0.1', '2014-01-03 01:15:13', '2014-03-31 21:37:08', '2014-03-31 16:33:23', '1', 0, 8),
(6, 'something2', 'something2@gmail.com', '96b9c62c86f35c209c5b8c302ba34175', 'm', '1990-09-16', 'United Kingdom', 'a', 'Rookie', NULL, '', '127.0.0.1', '2014-04-02 23:14:18', '2014-04-14 03:34:33', '2014-04-07 18:32:50', '1', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `usersgroups`
--

CREATE TABLE IF NOT EXISTS `usersgroups` (
  `ugId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `createDate` datetime NOT NULL,
  `accepted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`ugId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
