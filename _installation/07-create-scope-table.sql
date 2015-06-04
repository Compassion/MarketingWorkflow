/* This table is used to generate the work component of the workflow App */

CREATE TABLE IF NOT EXISTS `workflow`.`scope_record` (
  `scope_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `request_id` int(11) NOT NULL COMMENT 'pull this from the request table, this is the fk_ and join',
  `date_scoped` varchar(64) COMMENT 'this is the date scoped',
  
  `project_assigned` varchar(252) COLLATE utf8_unicode_ci NOT NULL COMMENT 'project task is scoped to to be submitted in Asana',
  `scoper` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'person who scoped the task',
  `scope_product` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'scope for total time required for product team' DEFAULT 0,   
  `scope_coms` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'scope for total time required for coms team' DEFAULT 0,   
  `scope_digital` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'scope for total time required for digital team' DEFAULT 0,   
  `scope_design` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'scope for total time required for design team' DEFAULT 0,
  `scope_video` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'scope for total time required for video team' DEFAULT 0,
  `scope_external` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'scope for total time required for external team' DEFAULT 0,
  PRIMARY KEY (`scope_id`),
  INDEX `request_id` (`request_id`),
  INDEX `project_assigned` (`project_assigned`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This table is used to generate the work component of the workflow App';

ALTER TABLE `scope_record` ADD CONSTRAINT `fk_rq_id` FOREIGN KEY (`request_id`) REFERENCES `workflow`.`requests`(`request_id`) ON DELETE RESTRICT ON UPDATE CASCADE;