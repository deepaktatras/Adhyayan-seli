-- add new attributes to filter
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('13', 'KPA', 'd_kpa', 'kpa_id', 'kpa_name', '1');
-- kpa
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('13', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('13', '6');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('13', '8');

-- medium instruction
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('14', 'Medium of instruction', 'd_language', 'language_id', 'language_name', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('14', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('14', '6');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('14', '8');

-- school location
drop table d_school_location;
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('15', 'School Location', 'd_school_region', 'region_id', 'region_name', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('15', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('15', '6');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('15', '8');

-- year of review
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('16', 'Year of Review', 'd_AQS_data', 'id', 'school_aqs_pref_start_date', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('16', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('16', '6');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('16', '8');
-- month of review
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('17', 'Year of Review', 'd_AQS_data', 'id', 'school_aqs_pref_start_date', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('17', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('17', '6');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('17', '8');
--date of review
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('18', 'Date of Review', 'd_AQS_data', 'id', 'school_aqs_pref_start_date', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('18', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('18', '6');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('18', '8');

UPDATE `d_filter_attr` SET `filter_attr_name`='Month of Review' WHERE `filter_attr_id`='17';

--school classes
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('19', 'Classes', 'd_school_class', 'class_id', 'class_name', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('19', '7');

--delete operators for date of review
DELETE FROM `h_filter_attr_operator` WHERE `filter_attr_id`='18';
--insert between operator for date of review
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('18', '7');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('18', '1');

UPDATE `d_filter_attr` SET `filter_table_col_id`='school_aqs_pref_start_date' WHERE `filter_attr_id`='18';
UPDATE `d_filter_attr` SET `filter_table_col_id`='school_aqs_pref_start_date' WHERE `filter_attr_id`='17';
UPDATE `d_filter_attr` SET `filter_table_col_id`='school_aqs_pref_start_date' WHERE `filter_attr_id`='16';

INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('16', '7');
-- year add between
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('17', '7');
--remove = for date of review
DELETE FROM `h_filter_attr_operator` WHERE `filter_attr_id`='18';
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('18', '7');

-- change data type
ALTER TABLE `h_filter_attr` 
CHANGE COLUMN `filter_f_value` `filter_f_value` VARCHAR(100) NULL DEFAULT NULL COMMENT '' ,
CHANGE COLUMN `filter_s_value` `filter_s_value` VARCHAR(100) NULL DEFAULT NULL COMMENT '' ;

--add award
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('20', 'Award', 'd_award', 'award_id', 'award_name', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('20', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('20', '8');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('20', '6');

--add external reviewer
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('21', 'External Reviewer', 'd_user', 'user_id', 'name', '1');

INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('21', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('21', '6');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('21', '8');

-- add number of reviews
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('22', 'Number of Reviews', 'num_review_id', 'num_reviews', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('22', '1');

-- key question
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('23', 'Key Question', 'd_key_question', 'key_question_id', 'key_question_text', '1');
-- sq
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('24', 'Sub Question', 'd_core_question', 'core_question_id', 'core_question_text', '1');
-- js
INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`) VALUES ('25', 'Judgement Statement', 'd_judgement_statement', 'judgement_statement_id', 'judgement_statement_text');
UPDATE `d_filter_attr` SET `active`='1' WHERE `filter_attr_id`='25';

-- new operators
INSERT INTO `d_filter_operator` (`operator_id`, `operator_text`) VALUES ('9', 'At least');
INSERT INTO `d_filter_operator` (`operator_id`, `operator_text`) VALUES ('10', 'At most');
INSERT INTO `d_filter_operator` (`operator_id`, `operator_text`) VALUES ('11', 'Exactly');
INSERT INTO `d_filter_operator` (`operator_id`, `operator_text`) VALUES ('12', 'All Data');


-- new table
CREATE TABLE `h_filter_sub_attr` (
  `filter_instance_id` INT(11) NOT NULL COMMENT '',
  `filter_sub_attr_id` INT(11) NOT NULL COMMENT '',
  `filter_sub_attr_operator` INT(11) NULL COMMENT '',
  `filter_sub_attr_value` VARCHAR(500) NOT NULL COMMENT '',
  `filter_sub_attr_rating` INT(11) NULL COMMENT '',
  `filter_sub_attr_cardinality` INT(11) NULL COMMENT '');

ALTER TABLE `h_filter_sub_attr` 
ADD INDEX `fk_h_filter_attr_instance_idx` (`filter_instance_id` ASC) COMMENT '',
ADD INDEX `fk_d_filter_attr_id_idx` (`filter_sub_attr_id` ASC)  COMMENT '',
ADD INDEX `fk_d_filter_operator_idx` (`filter_sub_attr_operator` ASC)  COMMENT '';
ALTER TABLE `h_filter_sub_attr` 
ADD CONSTRAINT `fk_h_filter_attr_instance`
  FOREIGN KEY (`filter_instance_id`)
  REFERENCES `h_filter_attr` (`filter_instance_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_sub_d_filter_attr_id`
  FOREIGN KEY (`filter_sub_attr_id`)
  REFERENCES `d_filter_attr` (`filter_attr_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_sub_d_filter_operator`
  FOREIGN KEY (`filter_sub_attr_operator`)
  REFERENCES `d_filter_operator` (`operator_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
  
  CREATE TABLE `adh`.`h_filter_sub_attr_operator` (
  `filter_sub_attr_id` INT NOT NULL COMMENT '',
  `operator_id` INT(11) NOT NULL COMMENT '',
  INDEX `fk_sub_operator_idx` (`operator_id` ASC)  COMMENT '',
  CONSTRAINT `fkx_sub_d_filter_attr_id`
    FOREIGN KEY (`filter_sub_attr_id`)
    REFERENCES `adh`.`d_filter_attr` (`filter_attr_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fkx_sub_operator`
    FOREIGN KEY (`operator_id`)
    REFERENCES `adh`.`d_filter_operator` (`operator_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
    
    
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('13', '9');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('13', '10');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('13', '11');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('13', '12');
    

INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('23', '9');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('23', '10');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('23', '11');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('23', '12');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('24', '9');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('24', '10');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('24', '11');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('24', '12');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('25', '9');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('25', '10');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('25', '11');
INSERT INTO `adh`.`h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('25', '12');


ALTER TABLE `adh`.`h_filter_sub_attr` 
CHANGE COLUMN `filter_sub_attr_value` `filter_sub_attr_value` VARCHAR(500) NULL COMMENT '' ;


ALTER TABLE `adh`.`h_filter_sub_attr` 
ADD COLUMN `filter_sub_att_maxr_cardinality` INT(11) NULL COMMENT '' AFTER `filter_sub_attr_cardinality`;

ALTER TABLE `adh`.`h_filter_sub_attr` 
CHANGE COLUMN `filter_sub_att_maxr_cardinality` `filter_sub_attr_max_cardinality` INT(11) NULL DEFAULT NULL COMMENT '' ;

UPDATE `d_filter_attr` SET `active`='0' WHERE `filter_attr_id`='17';
