/* This table is used to generate the work component of the workflow App */

CREATE TABLE IF NOT EXISTS `workflow`.`work_last_updated` (
  `w_updated_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing w_updated_id, unique index',
  `w_updated` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'last updated',  
  PRIMARY KEY (`w_updated_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='When was the work DB last updated?';

/*SELECT `w_updated` FROM work_last_updated ORDER BY `w_updated` DESC LIMIT 1*/