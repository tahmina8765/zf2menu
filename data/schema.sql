CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) CHARACTER SET utf8 NOT NULL,
  `icon` varchar(500) CHARACTER SET utf8 NOT NULL,
  `type` tinyint(1) NOT NULL,
  `resource_id` int(10) unsigned DEFAULT NULL,
  `url` text CHARACTER SET utf8,
  `target` varchar(100) NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;