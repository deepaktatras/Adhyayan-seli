ALTER TABLE `h_award_scheme` 
ADD INDEX `idx_order` (`order` ASC)  COMMENT '';


ALTER TABLE `d_assessment` 
CHANGE COLUMN `internal_award` `internal_award` INT(11) NULL DEFAULT NULL COMMENT '' ,
CHANGE COLUMN `external_award` `external_award` INT(11) NULL DEFAULT NULL COMMENT '' ;

ALTER TABLE `d_assessment` 
ADD INDEX `fk_d_assessment_h_award_schme_idx` (`internal_award` ASC)  COMMENT '',
ADD INDEX `fk_d_assessment_h_award_scheme_idx1` (`external_award` ASC)  COMMENT '';

ALTER TABLE `d_assessment` 
ADD CONSTRAINT `fk_d_assessment_h_award_scheme`
  FOREIGN KEY (`internal_award`)
  REFERENCES `h_award_scheme` (`order`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_d_assessment_h_award_scheme1`
  FOREIGN KEY (`external_award`)
  REFERENCES `h_award_scheme` (`order`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;