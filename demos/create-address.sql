CREATE TABLE `address` (
   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
   `street_number` varchar(65) DEFAULT NULL,
   `route` varchar(128) DEFAULT NULL,
   `locality` varchar(128) DEFAULT NULL,
   `sublocality_level_1` varchar(128) DEFAULT NULL,
   `postal_town` varchar(128) DEFAULT NULL,
   `administrative_area_level_1` varchar(128) DEFAULT NULL,
   `administrative_area_level_2` varchar(128) DEFAULT NULL,
   `country` varchar(128) DEFAULT NULL,
   `postal_code` varchar(128) DEFAULT NULL,
   `lat` decimal(10,8) DEFAULT NULL,
   `lng` decimal(11,8) DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB;
