CREATE TABLE `adh`.`d_transaltion_type` (
  `translation_type_id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `translation_type_text` VARCHAR(45) NOT NULL COMMENT '',
  `translation_type_table` VARCHAR(45) NOT NULL COMMENT '',
  PRIMARY KEY (`translation_type_id`)  COMMENT '')
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

ALTER TABLE `adh`.`d_transaltion_type` 
RENAME TO  `adh`.`d_translation_type` ;

ALTER TABLE `adh`.`h_lang_translation` 
CHANGE COLUMN `table_id` `equivalence_id` INT(11) NOT NULL COMMENT '' ;

INSERT INTO `adh`.`d_translation_type` (`translation_type_id`, `translation_type_text`, `translation_type_table`) VALUES ('1', 'KPA', 'd_kpa');
INSERT INTO `adh`.`d_translation_type` (`translation_type_id`, `translation_type_text`, `translation_type_table`) VALUES ('2', 'Key Question', 'd_key_question');
INSERT INTO `adh`.`d_translation_type` (`translation_type_id`, `translation_type_text`, `translation_type_table`) VALUES ('3', 'Sub Question/Core Question', 'd_core_question');
INSERT INTO `adh`.`d_translation_type` (`translation_type_id`, `translation_type_text`, `translation_type_table`) VALUES ('4', 'Judgement Statement', 'd_judgement_statement');
INSERT INTO `adh`.`d_translation_type` (`translation_type_id`, `translation_type_text`, `translation_type_table`) VALUES ('5', 'Award', 'd_award');
INSERT INTO `adh`.`d_translation_type` (`translation_type_id`, `translation_type_text`, `translation_type_table`) VALUES ('6', 'Recommendations', 'd_recommendation');



CREATE TABLE `adh`.`h_lang_translation` (
  `language_id` INT NOT NULL COMMENT '',
  `translation_type_id` INT NOT NULL COMMENT '',
  `translation_text` TEXT NOT NULL COMMENT '',
  `table_id` INT NOT NULL COMMENT '',
  INDEX `fk_h_lang_translation_d_translation_type_idxx` (`translation_type_id` ASC)  COMMENT '',
  CONSTRAINT `fk_h_lang_translation_d_languagee`
    FOREIGN KEY (`language_id`)
    REFERENCES `adh`.`d_language` (`language_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_h_lang_translation_d_translation_typee`
    FOREIGN KEY (`translation_type_id`)
    REFERENCES `adh`.`d_translation_type` (`translation_type_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

ALTER TABLE `adh`.`h_lang_translation` 
DROP COLUMN `table_id`;

ALTER TABLE `adh`.`h_lang_translation` 
ADD COLUMN `equivalence_id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '' FIRST,
ADD PRIMARY KEY (`equivalence_id`)  COMMENT '';


ALTER TABLE `adh`.`d_kpa` 
ADD COLUMN `equivalence_id` INT NULL COMMENT 'equivalence id referring h_lang_translation for translation of statements' AFTER `isActive`,
ADD INDEX `fk_d_kpa_h_lang_translation_idx` (`equivalence_id` ASC)  COMMENT '';
/*ALTER TABLE `adh`.`d_kpa` 
ADD CONSTRAINT `fk_d_kpa_h_lang_translation`
  FOREIGN KEY (`equivalence_id`)
  REFERENCES `adh`.`h_lang_translation` (`equivalence_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;*/