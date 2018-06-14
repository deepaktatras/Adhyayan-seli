ALTER TABLE `d_aqs_data` 
ADD COLUMN `medium_instruction` INT(11) NULL COMMENT '' AFTER `school_region_id`,
ADD INDEX `fk_d_review_medium_inst_idx` (`medium_instruction` ASC)  COMMENT '';
ALTER TABLE `d_aqs_data` 
ADD CONSTRAINT `fk_d_review_medium_inst`
  FOREIGN KEY (`medium_instruction`)
  REFERENCES `d_review_medium_instrn` (`review_medium_instrn_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
 
ALTER TABLE `d_aqs_data` 
DROP FOREIGN KEY `fk_d_review_medium_inst`;

ALTER TABLE `d_aqs_data` 
DROP INDEX `fk_d_review_medium_inst_idx` ;

ALTER TABLE `d_aqs_data` 
ADD INDEX `fk_d_language_idx` (`medium_instruction` ASC)  COMMENT '';

ALTER TABLE `d_language` 
ENGINE = InnoDB ;

ALTER TABLE `d_aqs_data` 
ADD CONSTRAINT `fkkx_d_language`
  FOREIGN KEY (`medium_instruction`)
  REFERENCES `d_language` (`language_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
INSERT INTO `d_language` (`language_id`, `language_name`) VALUES ('0', '--Not Specified--');

-- UPDATE `d_language` SET `language_id`='0' WHERE `language_id`='31';

  