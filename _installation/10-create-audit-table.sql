/* This table is used to generate the work component of the workflow App */

CREATE TABLE IF NOT EXISTS `workflow`.`audit_action` (
  `audit_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `request_id` int(11) NOT NULL COMMENT 'pull this from the request table, this is the fk_ and join',
  `audit_date` varchar(64) COMMENT 'this is the audit date',
  `audit_person` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'person who created the audit',
  `audit_assigned` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'person who audit record assigned to if required',
  `audit_status` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'status change of the audit',
  `audit_comment` varchar(252) COLLATE utf8_unicode_ci NOT NULL COMMENT 'any comment, used for declined requests',
  PRIMARY KEY (`audit_id`),
  INDEX `request_id` (`request_id`),
  INDEX `audit_person` (`audit_person`),
  INDEX `audit_assigned` (`audit_assigned`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Audit table';

ALTER TABLE `audit_action` ADD CONSTRAINT `fk_audit_to_rq_id` FOREIGN KEY (`request_id`) REFERENCES `workflow`.`requests`(`request_id`) ON DELETE RESTRICT ON UPDATE CASCADE;