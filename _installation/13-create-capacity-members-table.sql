CREATE TABLE IF NOT EXISTS `workflow`.`capacity_members` (
  `cm_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing cm_id of each user, unique index',
  `cm_team` varchar(64) COMMENT 'this is team',
  `cm_name` varchar(64) COMMENT 'this is team',
  `cm_hours` decimal(8,2) COLLATE utf8_unicode_ci NOT NULL COMMENT 'weekly capacity of member' DEFAULT 0,  
  `cm_mon` boolean COLLATE utf8_unicode_ci NOT NULL COMMENT 'work mondays?',
  `cm_tues` boolean COLLATE utf8_unicode_ci NOT NULL COMMENT 'work tuesdays?',
  `cm_weds` boolean COLLATE utf8_unicode_ci NOT NULL COMMENT 'work wednesdays?',
  `cm_thurs` boolean COLLATE utf8_unicode_ci NOT NULL COMMENT 'work thursdays?',
  `cm_fri` boolean COLLATE utf8_unicode_ci NOT NULL COMMENT 'work fridays?',

  PRIMARY KEY (`cm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='People who work table';
