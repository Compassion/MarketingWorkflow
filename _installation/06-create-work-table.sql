/* This table is used to generate the work component of the workflow App */

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This table is used to generate the work component of the workflow App';
