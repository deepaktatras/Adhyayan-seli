CREATE TABLE `h_group_assessment_recommendations` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `group_assessment_id` INT(11) NOT NULL COMMENT '',
  `recommendations` TEXT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_d_group_assessment_id_idx` (`group_assessment_id` ASC)  COMMENT '',
  CONSTRAINT `fk_d_group_assessment_id`
    FOREIGN KEY (`group_assessment_id`)
    REFERENCES `d_group_assessment` (`group_assessment_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

	
ALTER TABLE `h_group_assessment_recommendations` 
ADD COLUMN `type` VARCHAR(45) NULL COMMENT '' AFTER `recommendations`;

ALTER TABLE `h_group_assessment_recommendations` 
DROP COLUMN `type`;

ALTER TABLE `h_group_assessment_recommendations` 
ADD COLUMN `kpa_instance_id` INT(11) NULL COMMENT '' AFTER `recommendations`,
ADD COLUMN `key_question_instance_id` INT(11) NULL COMMENT '' AFTER `kpa_instance_id`,
ADD COLUMN `core_question_instance_id` INT(11) NULL COMMENT '' AFTER `key_question_instance_id`,
ADD COLUMN `judgement_statement_instance_id` INT(11) NULL COMMENT '' AFTER `core_question_instance_id`;


ALTER TABLE `h_group_assessment_recommendations` 
ADD INDEX `fkk_h_kpa_diagnostic_id_idx` (`kpa_instance_id` ASC)  COMMENT '',
ADD INDEX `fkk_h_kpa_q_id_idx` (`key_question_instance_id` ASC)  COMMENT '',
ADD INDEX `fkk_h_kq_cq_id_idx` (`core_question_instance_id` ASC)  COMMENT '',
ADD INDEX `fkk_h_cq_js_instance_idx` (`judgement_statement_instance_id` ASC)  COMMENT '';
ALTER TABLE `h_group_assessment_recommendations` 
ADD CONSTRAINT `fkk_h_kpa_diagnostic_id`
  FOREIGN KEY (`kpa_instance_id`)
  REFERENCES `h_kpa_diagnostic` (`kpa_instance_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fkk_h_kpa_kq_id`
  FOREIGN KEY (`key_question_instance_id`)
  REFERENCES `h_kpa_kq` (`key_question_instance_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fkk_h_kq_cq_id`
  FOREIGN KEY (`core_question_instance_id`)
  REFERENCES `h_kq_cq` (`core_question_instance_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fkk_h_cq_js_instance`
  FOREIGN KEY (`judgement_statement_instance_id`)
  REFERENCES `h_cq_js_instance` (`judgement_statement_instance_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
	
	
ALTER TABLE `h_group_assessment_recommendations` 
ADD COLUMN `diagnostic_id` INT(11) NOT NULL COMMENT '' AFTER `judgement_statement_instance_id`,
ADD INDEX `fkk_diagnostic_id_idx` (`diagnostic_id` ASC)  COMMENT '';
ALTER TABLE `h_group_assessment_recommendations` 
ADD CONSTRAINT `fkk_diagnostic_id`
  FOREIGN KEY (`diagnostic_id`)
  REFERENCES `d_diagnostic` (`diagnostic_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
	
	