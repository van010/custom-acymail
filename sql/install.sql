CREATE TABLE IF NOT EXISTS `#__tt_config` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NULL default NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__tt_credentials` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NULL default NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__tt_accounts` (
  `id` int(11) unsigned,
  `name` varchar(255) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__tt_instruments` (
  `id` int(11) unsigned,
  `name` varchar(255) NOT NULL default '',
  `productId` int(11) unsigned,
  `productName` varchar(255) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__tt_transactions` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `portfolio_id` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `customer_id` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `invest` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payout` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__tt_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `portfolio_id` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `accountId` mediumtext NOT NULL,
  `instrumentId` varchar(255) DEFAULT NULL,
  `avgBuy` decimal(15,10) NOT NULL DEFAULT '0.00',
  `avgSell` decimal(15,10) NOT NULL DEFAULT '0.00',
  `buyFillQty` decimal(15,10) NOT NULL DEFAULT '0.00',
  `buyWorkingQty` decimal(15,10) NOT NULL DEFAULT '0.00',
  `netPosition` varchar(100) DEFAULT NULL,
  `openAvgPrice` varchar(100) DEFAULT NULL,
  `pnl` decimal(15,10) NOT NULL DEFAULT '0.00',
  `pnlPrice` decimal(15,10) NOT NULL DEFAULT '0.00',
  `pnlPriceType` varchar(100) DEFAULT NULL,
  `realizedPnl` decimal(15,10) NOT NULL DEFAULT '0.00',
  `sellFillQty` decimal(15,10) NOT NULL DEFAULT '0.00',
  `sellWorkingQty` decimal(15,10) NOT NULL DEFAULT '0.00',
  `sodNetPos` varchar(100) DEFAULT NULL,
  `sodPriceType` varchar(100) DEFAULT NULL,
  `date` date NOT NULL default '0000-00-00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `#__tt_config` VALUES
(1, '', '0000-00-00 00:00:00', '{}')
ON DUPLICATE KEY UPDATE ID = 1;

INSERT INTO `#__tt_credentials` VALUES (1, 'token', '0000-00-00 00:00:00', '{}') ON DUPLICATE KEY UPDATE ID = 1;










