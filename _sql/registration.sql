-- ---------------------------------------------------------------
--  MGL E&C — Арга хэмжээний бүртгэлийн модуль
--  Хүснэгтүүд. CP Admin дээр модулийг нээхэд эдгээр автоматаар
--  үүсдэг (class/registration.class.php :: install), гэхдээ
--  phpMyAdmin дээр гараар ажиллуулж бас болно.
--
--  Хүснэгтийн угтвар: db_   ($tbl_pref, const.php)
-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `db_reg_setting` (
  `setKey` varchar(64) NOT NULL DEFAULT '',
  `setVal` longtext,
  PRIMARY KEY (`setKey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `db_reg_field` (
  `fieldID` int(11) NOT NULL AUTO_INCREMENT,
  `fieldKey` varchar(64) NOT NULL DEFAULT '',
  `fieldLabel` varchar(255) NOT NULL DEFAULT '',
  `fieldType` varchar(32) NOT NULL DEFAULT 'text',
  `fieldPlaceholder` varchar(255) NOT NULL DEFAULT '',
  `fieldHelp` varchar(255) NOT NULL DEFAULT '',
  `fieldOptions` text,
  `fieldRequired` tinyint(1) NOT NULL DEFAULT '0',
  `fieldWidth` varchar(16) NOT NULL DEFAULT 'full',
  `fieldCore` varchar(16) NOT NULL DEFAULT '',
  `fieldStatus` tinyint(1) NOT NULL DEFAULT '1',
  `fieldOrder` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`fieldID`),
  UNIQUE KEY `fieldKey` (`fieldKey`),
  KEY `fieldOrder` (`fieldOrder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `db_reg_block` (
  `blockID` int(11) NOT NULL AUTO_INCREMENT,
  `parentID` int(11) NOT NULL DEFAULT '0',
  `blockType` varchar(32) NOT NULL DEFAULT 'text',
  `blockData` longtext,
  `blockStatus` tinyint(1) NOT NULL DEFAULT '1',
  `blockOrder` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`blockID`),
  KEY `parentID` (`parentID`),
  KEY `blockOrder` (`blockOrder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `db_reg_entry` (
  `entryID` int(11) NOT NULL AUTO_INCREMENT,
  `entryName` varchar(255) NOT NULL DEFAULT '',
  `entryPhone` varchar(64) NOT NULL DEFAULT '',
  `entryEmail` varchar(255) NOT NULL DEFAULT '',
  `entryData` longtext,
  `entryIP` varchar(64) NOT NULL DEFAULT '',
  `entryDate` datetime DEFAULT NULL,
  `entryStatus` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`entryID`),
  KEY `entryPhone` (`entryPhone`),
  KEY `entryEmail` (`entryEmail`),
  KEY `entryDate` (`entryDate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
