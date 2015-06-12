/* This table is used to generate the work component of the workflow App */
/*
CREATE TABLE IF NOT EXISTS `workflow`.`work` (
  `work_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `asana_id` int(11) NOT NULL COMMENT 'pull this from ASANA',
  `request_id` int(11) COMMENT 'this is the id from the request table',
  `date_started` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'date started from Asana',
  `date_due` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'date due from Asana',
  `date_completed` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Complete date from Asana',
  `project` varchar(252) COLLATE utf8_unicode_ci NOT NULL COMMENT 'project task is in in Asana',
  `asignee` varchar(252) COLLATE utf8_unicode_ci NOT NULL COMMENT 'person who the task is assigned to in Asana',
  `load_product` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'workload per day for product team - total workload / number of workdays' DEFAULT 0,   
  `load_coms` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'workload per day for coms team - total workload / number of workdays' DEFAULT 0,   
  `load_digital` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'workload per day for digital team - total workload / number of workdays' DEFAULT 0,   
  `load_design` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'workload per day for design team - total workload / number of workdays' DEFAULT 0,
  `load_video` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'workload per day for video team - total workload / number of workdays' DEFAULT 0,
  `load_external` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'workload per day for external team - total workload / number of workdays' DEFAULT 0,
  PRIMARY KEY (`work_id`),
  INDEX `asana_id` (`asana_id`),
  INDEX `request_id` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This table is used to generate the work component of the workflow App';*/


/* This table is used to generate the task list for the work component of the workflow App */

CREATE TABLE IF NOT EXISTS `workflow`.`work_load` (
  `asana_id` bigint(25) NOT NULL COMMENT 'unique index, primary key, from Asana',
  `request_id` int(11) COMMENT 'pull this from the request table, this is the fk_ and join',
  `scope_id` int(11) COMMENT 'pull this from the scope table, this is the fk_ and join',
  `date_started` varchar(64) COMMENT 'this is the date start',
  `date_due` varchar(64) COMMENT 'this is the date due',
  `work_days` int(11) COMMENT 'number of weekdays between start and end dates',
  `asana_name` varchar(252) COLLATE utf8_unicode_ci NOT NULL COMMENT 'task name in Asana',
  `project_assigned` varchar(252) COLLATE utf8_unicode_ci NOT NULL COMMENT 'project task is asigned in Asana',
  `person_assigned` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'email of person task is asigned to',
    
  `asana_product` decimal(8,2) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily time for product team' DEFAULT 0,   
  `asana_coms` decimal(8,2) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily time for coms team' DEFAULT 0,   
  `asana_digital` decimal(8,2) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily time for digital team' DEFAULT 0,   
  `asana_design` decimal(8,2) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily time for design team' DEFAULT 0,
  `asana_video` decimal(8,2) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily time for video team' DEFAULT 0,
  `asana_external` decimal(8,2) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily time for external team' DEFAULT 0,
  PRIMARY KEY (`asana_id`),
  INDEX `request_id` (`request_id`),
  INDEX `scope_id` (`scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This table is used to generate the task list for the work component of the workflow App. The numbers are the daily time required = total time / number of workdays between sDate and eDate';

ALTER TABLE `work_load` ADD CONSTRAINT `fk_wl_rq` FOREIGN KEY (`request_id`) REFERENCES `workflow`.`requests`(`request_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `work_load` ADD CONSTRAINT `fk_wl_scope` FOREIGN KEY (`scope_id`) REFERENCES `workflow`.`scope_record`(`scope_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

/*ALTER TABLE `work_load` ADD UNIQUE(`asana_id`);*/