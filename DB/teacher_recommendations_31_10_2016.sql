ALTER TABLE `adh`.`d_diagnostic` 
ADD COLUMN `kpa_recommenations` INT(1) NULL DEFAULT 0 COMMENT '' AFTER `assessment_type_id`,
ADD COLUMN `kq_recommendations` INT(1) NULL DEFAULT 0 COMMENT '' AFTER `kpa_recommenations`,
ADD COLUMN `cq_recommendations` INT(1) NULL DEFAULT 0 COMMENT '' AFTER `kq_recommendations`,
ADD COLUMN `js_recommendations` INT(1) NULL DEFAULT 0 COMMENT '' AFTER `cq_recommendations`;


ALTER TABLE `adh`.`d_diagnostic` 
CHANGE COLUMN `kpa_recommenations` `kpa_recommendations` INT(1) NULL DEFAULT '0' COMMENT '' ;

update d_diagnostic set kpa_recommendations=1;


ALTER TABLE `adh`.`assessor_key_notes` 
DROP FOREIGN KEY `fk_assessor_key_notes_h_kpa_diagnostic1`;
ALTER TABLE `adh`.`assessor_key_notes` 
CHANGE COLUMN `kpa_instance_id` `kpa_instance_id` INT(11) NULL COMMENT '' ,
ADD COLUMN `key_question_instance_id` INT(11) NULL COMMENT '' AFTER `type`,
ADD COLUMN `core_question_instance_id` INT(11) NULL COMMENT '' AFTER `key_question_instance_id`,
ADD COLUMN `judgement_statement_instance_id` INT(11) NULL COMMENT '' AFTER `core_question_instance_id`,
ADD INDEX `fk_assessor_key_notes_h_kpa_kq_idx` (`key_question_instance_id` ASC)  COMMENT '',
ADD INDEX `fk_assessor_key_notes_h_kq_cq_idx` (`core_question_instance_id` ASC)  COMMENT '',
ADD INDEX `fk_assessor_key_notes_h_cq_js_instance_idx` (`judgement_statement_instance_id` ASC)  COMMENT '';
ALTER TABLE `adh`.`assessor_key_notes` 
ADD CONSTRAINT `fk_assessor_key_notes_h_kpa_diagnostic1`
  FOREIGN KEY (`kpa_instance_id`)
  REFERENCES `adh`.`h_kpa_diagnostic` (`kpa_instance_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_assessor_key_notes_h_kpa_kq`
  FOREIGN KEY (`key_question_instance_id`)
  REFERENCES `adh`.`h_kpa_kq` (`key_question_instance_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_assessor_key_notes_h_kq_cq`
  FOREIGN KEY (`core_question_instance_id`)
  REFERENCES `adh`.`h_kq_cq` (`core_question_instance_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_assessor_key_notes_h_cq_js_instance`
  FOREIGN KEY (`judgement_statement_instance_id`)
  REFERENCES `adh`.`h_cq_js_instance` (`judgement_statement_instance_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

  
  