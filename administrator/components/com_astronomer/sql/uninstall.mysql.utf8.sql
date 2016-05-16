DROP TABLE IF EXISTS `#__astronomer_astrometry`;

DELETE FROM `#__content_types` WHERE (type_alias LIKE 'com_astronomer.%');