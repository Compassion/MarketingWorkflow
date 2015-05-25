CREATE TABLE IF NOT EXISTS `workflow`.`user_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `group_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user group list - essentially user permissions';


/* Groups */
INSERT INTO `user_groups`(`group_name`) VALUES ('Admin');
INSERT INTO `user_groups`(`group_name`) VALUES ('Workflow');

INSERT INTO `user_groups`(`group_name`) VALUES ('Marketing Manager');
INSERT INTO `user_groups`(`group_name`) VALUES ('Coms Manager');
INSERT INTO `user_groups`(`group_name`) VALUES ('Creative Manager');

INSERT INTO `user_groups`(`group_name`) VALUES ('Product Area 1');
INSERT INTO `user_groups`(`group_name`) VALUES ('Product Area 2');
INSERT INTO `user_groups`(`group_name`) VALUES ('Product Area 3');

INSERT INTO `user_groups`(`group_name`) VALUES ('Requester');
