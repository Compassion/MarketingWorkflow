CREATE TABLE IF NOT EXISTS `workflow`.`users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email, unique',
  `user_group` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s group',
  `department` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s department',
  `position` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s position',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`),
  INDEX `user_group` (`user_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data';
