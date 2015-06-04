/* This table is used to generate the work component of the workflow App */

CREATE TABLE IF NOT EXISTS `workflow`.`capacity` (
  `capacity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing capacity_id, unique index',
  `cap_product` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily capacity of product team' DEFAULT 0,   
  `cap_coms` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily capacity of coms team' DEFAULT 0,    
  `cap_adv` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily capacity of pr and advertising' DEFAULT 0,   
  `cap_digital` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily capacity of digital team' DEFAULT 0,   
  `cap_design` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily capacity of design team' DEFAULT 0,
  `cap_video` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily capacity of video team' DEFAULT 0,
  `cap_external` int(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'daily capacity of external people' DEFAULT 0,
  PRIMARY KEY (`capacity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This table is used to reference and update capacity';

/* This table is used to generate the work component of the workflow App */
INSERT INTO `workflow`.`capacity` (`capacity_id`, `cap_product`, `cap_coms`, `cap_adv`, `cap_digital`, `cap_design`, `cap_video`, `cap_external`) VALUES (NULL, '20.06', '17.37', '6.79', '13.03', '8.69', '6.79', '0');
