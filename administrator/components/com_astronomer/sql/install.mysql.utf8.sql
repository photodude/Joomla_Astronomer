CREATE TABLE IF NOT EXISTS `#__joomla_astronomer` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`humandate` DATETIME(5)  NOT NULL ,
`designation` VARCHAR(8)  NOT NULL ,
`year` CHAR(4)  NOT NULL ,
`month` CHAR(2)  NOT NULL ,
`day` CHAR(2)  NOT NULL ,
`mag` CHAR(4)  NOT NULL ,
`observatory` CHAR(3)  NOT NULL ,
`entry` CHAR(80)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Astronomer','com_astronomer.astronomer','{"special":{"dbtable":"#__joomla_astronomer","key":"id","type":"Astronomer","prefix":"Joomla AstronomerTable"}}', '{"formFile":"administrator\/components\/com_astronomer\/models\/forms\/astronomer.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_astronomer.astronomer')
) LIMIT 1;
