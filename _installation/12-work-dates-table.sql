CREATE TABLE IF NOT EXISTS `workflow`.`work_dates` (
  `wd_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing work date, unique index',
  `request_id` int(11) NOT NULL COMMENT 'pull this from the request table, this is the fk_ and join',
  `scope_id` int(11) NOT NULL COMMENT 'pull this from the scope table, this is the fk_ and join',
  `wd_submitted` varchar(64) COMMENT 'this is the date task submitted to Asana',
  `wd_start` varchar(64) COMMENT 'this is the date to start work',
  `wd_end` varchar(64) COMMENT 'this is the date to finish',
  PRIMARY KEY (`wd_id`),
  INDEX `request_id` (`request_id`),
  INDEX `scope_id` (`scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This is to create subtasks for tasks when scoping';

ALTER TABLE `work_dates` ADD CONSTRAINT `fk_wd_rq_id` FOREIGN KEY (`request_id`) REFERENCES `workflow`.`requests`(`request_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `work_dates` ADD CONSTRAINT `fk_wd_sc_id` FOREIGN KEY (`scope_id`) REFERENCES `workflow`.`scope_record`(`scope_id`) ON DELETE RESTRICT ON UPDATE CASCADE;