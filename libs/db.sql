-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mer 25 Mars 2015 à 10:40
-- Version du serveur: 5.5.41-0ubuntu0.14.04.1
-- Version de PHP: 5.5.9-1ubuntu4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `testorrent4`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `position` varchar(255) NOT NULL,
  `c_name` varchar(255) NOT NULL,
  `c_desc` tinytext NOT NULL,
  `c_icon` varchar(255) NOT NULL,
  `c_group` varchar(255) NOT NULL DEFAULT '0',
  `url_strip` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_strip` (`url_strip`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `categories`
--

INSERT INTO `categories` (`id`, `position`, `c_name`, `c_desc`, `c_icon`, `c_group`, `url_strip`) VALUES
(1, '1>', 'Documentary', 'Documentary opensource', 'themes/asset/img/cat/4.gif', '0', 'documentary-opensource'),
(2, '2>', 'Software', 'Software opensource', 'themes/asset/img/cat/7.gif', '0', 'software-opensource'),
(3, '3>', 'Porn', 'Porn', 'themes/asset/img/cat/533.gif', '0', 'Porn'),
(4, '1>4>', 'World', 'world', 'themes/asset/img/cat/4.gif', '0', 'world'),
(5, '1>5>', 'Animals', 'animals', 'themes/asset/img/cat/4.gif', '0', 'animals');

-- --------------------------------------------------------

--
-- Structure de la table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `peers`
--

CREATE TABLE IF NOT EXISTS `peers` (
  `info_hash` binary(20) NOT NULL,
  `peer_id` binary(20) NOT NULL,
  `compact` binary(6) NOT NULL,
  `ip` char(15) NOT NULL,
  `port` smallint(5) unsigned NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updated` int(10) unsigned NOT NULL,
  PRIMARY KEY (`info_hash`,`peer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `plugins`
--

CREATE TABLE IF NOT EXISTS `plugins` (
  `filename` varchar(127) COLLATE utf8_bin NOT NULL DEFAULT '',
  `action` tinyint(1) DEFAULT '0',
  `installed_sql` enum('true','false') COLLATE utf8_bin NOT NULL DEFAULT 'false',
  PRIMARY KEY (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(30) NOT NULL,
  `value` varchar(3000) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `settings`
--

INSERT INTO `settings` (`key`, `value`) VALUES
('baseurl', 'http://localhost'),
('title', 'Bittytorrent'),
('timecache', '2'),
('lang', 'en'),
('theme', 'boot3'),
('open_tracker', 'true'),
('announce_interval', '1800'),
('min_interval', '900'),
('default_peers', '50'),
('max_peers', '50'),
('see_uploader', 'true'),
('external_ip', 'false'),
('force_compact', 'true'),
('full_scrape', 'false'),
('rewrite_url', 'true'),
('metad', 'Meta descriptionn'),
('metak', 'Meta keywords'),
('torrentSize', ''),
('submit', 'Submit'),
('version', 'a:2:{s:7:"version";s:1:"1";s:6:"commit";s:1:"3";}'),
('salt', 'qskSXJcYN9KcPpiiONqQ4K4GrOtnkTSS'),
('sessionName', 'sessionLocalhost'),
('donate', 'Y6458Z2DDGX7W');

-- --------------------------------------------------------

--
-- Structure de la table `tasks`
--

CREATE TABLE IF NOT EXISTS `tasks` (
  `name` varchar(5) NOT NULL,
  `value` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `tasks`
--

INSERT INTO `tasks` (`name`, `value`) VALUES
('prune', 1427027828);

-- --------------------------------------------------------

--
-- Table structure for table `torrents`
--

CREATE TABLE `torrents` (
  `id` int(200) NOT NULL,
  `userid` int(50) NOT NULL,
  `info_hash` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT 'Untitled',
  `url_title` varchar(100) NOT NULL,
  `categorie` varchar(50) NOT NULL DEFAULT '0',
  `torrent_desc` longtext CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `date` varchar(10) NOT NULL,
  `announce` longtext NOT NULL,
  `hits` varchar(100) NOT NULL DEFAULT '0',
  `seeds` int(11) DEFAULT NULL,
  `leechers` int(11) DEFAULT NULL,
  `finished` int(11) DEFAULT NULL,
  `size` bigint(11) DEFAULT NULL,
  `last_scrape` int(11) DEFAULT NULL,
  `images64` longtext,
  `imgExt` varchar(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `torrents`
--
ALTER TABLE `torrents`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `torrents`
--
ALTER TABLE `torrents`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `pass` varchar(200) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `level` varchar(5) NOT NULL,
  `seemail` enum('true','false') NOT NULL,
  `seeMytorrents` enum('true','false') NOT NULL DEFAULT 'true',
  `signature` varchar(250) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `private_id` varchar(32) NOT NULL,
  `upload` int(11) DEFAULT NULL,
  `download` int(11) DEFAULT NULL,
  `joined` int(11) DEFAULT NULL,
  `fakeuser` enum('true','false') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `name`, `pass`, `mail`, `level`, `seemail`, `seeMytorrents`, `signature`, `location`, `website`, `private_id`, `upload`, `download`, `joined`, `fakeuser`) VALUES
(0, 'Anonymous', '', '', '1', 'true', 'true', '', '', '', '0', 0, 0, 0, 'true');

-- --------------------------------------------------------

--
-- Structure de la table `users_group`
--

CREATE TABLE IF NOT EXISTS `users_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `group` varchar(50) NOT NULL DEFAULT '',
  `view_torrents` enum('true','false') NOT NULL DEFAULT 'true',
  `edit_torrents` enum('true','false') NOT NULL DEFAULT 'false',
  `delete_torrents` enum('true','false') NOT NULL DEFAULT 'false',
  `view_users` enum('true','false') NOT NULL DEFAULT 'true',
  `edit_users` enum('true','false') NOT NULL DEFAULT 'false',
  `delete_users` enum('true','false') NOT NULL DEFAULT 'false',
  `can_upload` enum('true','false') NOT NULL DEFAULT 'false',
  `can_download` enum('true','false') NOT NULL DEFAULT 'true',
  `can_scrape` enum('true','false') NOT NULL,
  `can_be_deleted` enum('true','false') NOT NULL DEFAULT 'true',
  `admin_access` enum('true','false') NOT NULL DEFAULT 'false',
  UNIQUE KEY `base` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `users_group`
--

INSERT INTO `users_group` (`id`, `group`, `view_torrents`, `edit_torrents`, `delete_torrents`, `view_users`, `edit_users`, `delete_users`, `can_upload`, `can_download`, `can_scrape`, `can_be_deleted`, `admin_access`) VALUES
(1, 'Visitor', 'true', 'false', 'false', 'false', 'false', 'false', 'false', 'true', 'false', 'true', 'false'),
(2, 'Member', 'true', 'true', 'true', 'true', 'false', 'false', 'false', 'true', 'false', 'true', 'false'),
(3, 'Uploader', 'true', 'true', 'true', 'true', 'false', 'false', 'true', 'true', 'true', 'true', 'false'),
(4, 'Premium', 'true', 'true', 'true', 'true', 'false', 'false', 'true', 'true', 'true', 'true', 'false'),
(5, 'Moderator', 'true', 'true', 'true', 'true', 'true', 'true', 'true', 'true', 'true', 'true', 'false'),
(6, 'Owner', 'true', 'true', 'false', 'true', 'true', 'true', 'true', 'false', 'false', 'false', 'true');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
