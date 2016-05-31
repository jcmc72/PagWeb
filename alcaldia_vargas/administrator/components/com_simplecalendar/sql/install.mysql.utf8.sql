DROP TABLE IF EXISTS #__simplecalendar;
DROP TABLE IF EXISTS #__simplecalendar_organizers;
DROP TABLE IF EXISTS #__simplecalendar_statuses;

CREATE TABLE IF NOT EXISTS `#__simplecalendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `start_dt` date DEFAULT NULL,
  `end_dt` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `reserve_dt` date DEFAULT NULL,
  `name` varchar(150) DEFAULT '',
  `alias` varchar(150) DEFAULT '',
  `venue` varchar(150) DEFAULT '',
  `address` varchar(150) NOT NULL,
  `catid` int(11) DEFAULT '0',
  `statusid` int(11) DEFAULT '0',
  `latlon` varchar(150) DEFAULT '',
  `organizer_id` int(11) DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `contact_name` varchar(64) DEFAULT '',
  `contact_email` varchar(64) DEFAULT '',
  `contact_website` varchar(64) DEFAULT '',
  `contact_telephone` varchar(32) DEFAULT '',
  `created_dt` datetime DEFAULT NULL,
  `modified_dt` datetime DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `featured` int(1) NOT NULL DEFAULT '0',
  `customfield1` varchar(128) NOT NULL,
  `customfield2` varchar(128) NOT NULL,
  `price` varchar(128) NOT NULL,
  `params` varchar(255) NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__simplecalendar_organizers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abbr` varchar(6) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `alias` varchar(64) DEFAULT NULL,
  `latlon` varchar(64) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_name` varchar(64) DEFAULT NULL,
  `contact_email` varchar(64) DEFAULT NULL,
  `contact_website` varchar(64) DEFAULT NULL,
  `contact_telephone` varchar(32) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `state` int(1) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `created_dt` date DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_dt` date DEFAULT NULL,
  `params` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;


CREATE TABLE IF NOT EXISTS `#__simplecalendar_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `color` varchar(8) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `created_dt` date NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_dt` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `#__simplecalendar_statuses` (`id`, `name`, `alias`, `color`, `ordering`, `state`, `created_by`, `created_dt`, `modified_by`, `modified_dt`) VALUES
(1, 'Confirmed', 'confirmed', '35FF1F', 1, 1, 89, '2013-04-25', 89, '2013-04-25');