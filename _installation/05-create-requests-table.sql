/* Still pending for decisions */

CREATE TABLE IF NOT EXISTS `workflow`.`requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing request_id of each request, unique index',
  `request_name` varchar(252) COLLATE utf8_unicode_ci NOT NULL COMMENT 'request name',
  `date_created` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'date created',
  `date_due` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'requested due date',
  `description` varchar(2048) COLLATE utf8_unicode_ci NOT NULL COMMENT 'description of request',
  `request_maker` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'requester email',
  `request_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'request type',
  `request_category` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'request category',
  `request_assigned` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'request assigned',
  `status` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'request status',
  PRIMARY KEY (`request_id`),
  INDEX `request_maker` (`request_maker`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='request data';
