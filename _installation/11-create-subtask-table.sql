CREATE TABLE IF NOT EXISTS `workflow`.`scope_subtask` (
  `st_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `request_id` int(11) NOT NULL COMMENT 'pull this from the request table, this is the fk_ and join',
  `scope_id` int(11) NOT NULL COMMENT 'pull this from the scope table, this is the fk_ and join',
  `st_date_created` varchar(64) COMMENT 'this is the date created',
  `st_date_required` varchar(64) COMMENT 'this is the date the subtask is due',
  `st_name` varchar(252) COLLATE utf8_unicode_ci NOT NULL COMMENT 'any comment, used for declined requests',
  `st_comment` varchar(2048) COLLATE utf8_unicode_ci NOT NULL COMMENT 'comment about the subtask',
  PRIMARY KEY (`st_id`),
  INDEX `request_id` (`request_id`),
  INDEX `scope_id` (`scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This is to create subtasks for tasks when scoping';

ALTER TABLE `scope_subtask` ADD CONSTRAINT `fk_st_rq_id` FOREIGN KEY (`request_id`) REFERENCES `workflow`.`requests`(`request_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `scope_subtask` ADD CONSTRAINT `fk_st_sc_id` FOREIGN KEY (`scope_id`) REFERENCES `workflow`.`scope_record`(`scope_id`) ON DELETE RESTRICT ON UPDATE CASCADE;