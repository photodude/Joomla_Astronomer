CREATE TABLE IF NOT EXISTS `#__formmaker_sessions` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(20) NOT NULL,
  `group_id` int(20) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `ord_date` datetime NOT NULL,
  `ord_last_modified` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `mobile_phone` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `address` varchar(300) NOT NULL,
  `paypal_info` text NOT NULL,
  `without_paypal_info` text NOT NULL,
  `ipn` varchar(20) NOT NULL,
  `checkout_method` varchar(20) NOT NULL,
  `tax` float NOT NULL,
  `shipping` float NOT NULL,
  `shipping_type` varchar(200) NOT NULL,
  `read` int(11) NOT NULL,
  `total` float NOT NULL,
  `currency` varchar(24) NOT NULL,
  PRIMARY KEY (`id`),

  UNIQUE KEY `id` (`id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__formmaker_blocked` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__formmaker_query` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `query` text NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;