CREATE TABLE `adh`.`d_province` (
  `province_id` INT NOT NULL COMMENT '',
  `province_name` VARCHAR(100) NOT NULL COMMENT '',
  `is_active` TINYINT NOT NULL COMMENT '',
  PRIMARY KEY (`province_id`)  COMMENT '');
ALTER TABLE `adh`.`d_province` 
CHANGE COLUMN `province_id` `province_id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '' ;
  

CREATE TABLE `adh`.`h_province_network` (
  `province_network_id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `network_id` INT NULL COMMENT '',
  `province_id` INT NULL COMMENT '',
  PRIMARY KEY (`province_network_id`)  COMMENT '',
  INDEX `fk_d_network_d_province_idx` (`network_id` ASC)  COMMENT '',
  INDEX `fk_d_province_h_province_network_idx` (`province_id` ASC)  COMMENT '',
  CONSTRAINT `fk_d_network_h_province_network`
    FOREIGN KEY (`network_id`)
    REFERENCES `adh`.`d_network` (`network_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_d_province_h_province_network`
    FOREIGN KEY (`province_id`)
    REFERENCES `adh`.`d_province` (`province_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

ALTER TABLE `adh`.`d_province` 
CHANGE COLUMN `is_active` `is_active` TINYINT(1) NOT NULL COMMENT '' ;

CREATE TABLE `adh`.`h_client_province` (
  `client_province_id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `province_id` INT(11) NOT NULL COMMENT '',
  `client_id` INT(11) NOT NULL COMMENT '',
  PRIMARY KEY (`client_province_id`)  COMMENT '',
  INDEX `fk_d_province_h_client_province_idx` (`province_id` ASC)  COMMENT '',
  INDEX `fk_d_client_h_client_province_idx` (`client_id` ASC)  COMMENT '',
  CONSTRAINT `fk_d_province_h_client_province`
    FOREIGN KEY (`province_id`)
    REFERENCES `adh`.`d_province` (`province_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_d_client_h_client_province`
    FOREIGN KEY (`client_id`)
    REFERENCES `adh`.`d_client` (`client_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_h_client_network_h_client_province`
    FOREIGN KEY (`client_id`)
    REFERENCES `adh`.`h_client_network` (`client_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


UPDATE `adh`.`d_review_midleaders` SET `midleaders_id`='1', `status`='Active supportive' WHERE `midleaders_id`='2';
UPDATE `adh`.`d_review_midleaders` SET `midleaders_id`='2', `status`='Active combative' WHERE `midleaders_id`='4';
UPDATE `adh`.`d_review_midleaders` SET `midleaders_id`='3', `status`='Passive supportive' WHERE `midleaders_id`='5';
UPDATE `adh`.`d_review_midleaders` SET `midleaders_id`='4', `status`='Passive combative' WHERE `midleaders_id`='6';
INSERT INTO `adh`.`d_review_midleaders` (`midleaders_id`, `status`) VALUES ('5', 'Doesn\'t exist');


INSERT INTO `adh`.`d_review_association` (`association_id`, `status`) VALUES ('5', 'Doesn\'t exist');



ALTER TABLE `adh`.`d_post_review` 
ADD COLUMN `alumni_association` INT(11) NULL COMMENT '' AFTER `teaching_staff_comment`;


INSERT INTO `adh`.`d_student_body` (`student_body_id`, `student_body_text`) VALUES ('3', 'Doesn\'t exist');

ALTER TABLE `adh`.`d_review_staff` 
ADD COLUMN `type` VARCHAR(45) NOT NULL COMMENT '' AFTER `staff_count`;


UPDATE `adh`.`d_review_staff` SET `staff_count`='1:15', `type`='teaching' WHERE `staff_id`='1';
UPDATE `adh`.`d_review_staff` SET `staff_count`='1:25', `type`='teaching' WHERE `staff_id`='2';
UPDATE `adh`.`d_review_staff` SET `staff_count`='1:45', `type`='teaching' WHERE `staff_id`='3';
UPDATE `adh`.`d_review_staff` SET `staff_count`='1:65', `type`='teaching' WHERE `staff_id`='4';
UPDATE `adh`.`d_review_staff` SET `staff_count`='1:85', `type`='teaching' WHERE `staff_id`='5';
INSERT INTO `adh`.`d_review_staff` (`staff_id`, `staff_count`, `type`) VALUES ('6', '100:10', 'non-teaching');
INSERT INTO `adh`.`d_review_staff` (`staff_id`, `staff_count`, `type`) VALUES ('7', '100:25', 'non-teaching');
INSERT INTO `adh`.`d_review_staff` (`staff_id`, `staff_count`, `type`) VALUES ('8', '100:50', 'non-teaching');
INSERT INTO `adh`.`d_review_staff` (`staff_id`, `staff_count`, `type`) VALUES ('9', '100:75', 'non-teaching');
INSERT INTO `adh`.`d_review_staff` (`staff_id`, `staff_count`, `type`) VALUES ('10', '100:100', 'non-teaching');


ALTER TABLE `adh`.`d_post_review` 
ADD COLUMN `action_planning` TEXT NULL COMMENT '' AFTER `create_date`;

ALTER TABLE `adh`.`d_post_review` 
CHANGE COLUMN `action_planning` `action_planning` TEXT NULL DEFAULT NULL COMMENT '' AFTER `rte`;



ALTER TABLE `d_post_review` 
CHANGE COLUMN `decision_maker` `decision_maker` INT(11) NULL COMMENT 'Decision for review taken by' ,
CHANGE COLUMN `decision_maker_other` `decision_maker_other` VARCHAR(45) NULL DEFAULT NULL COMMENT 'Decision for review taken by - Text' ,
CHANGE COLUMN `management_engagement` `management_engagement` INT(11) NULL DEFAULT NULL COMMENT 'Engagement of management in school' ,
CHANGE COLUMN `principal_tenure` `principal_tenure` INT(11) NULL DEFAULT NULL COMMENT 'Principal Tenure (in years)' ,
CHANGE COLUMN `principal_vision` `principal_vision` INT(11) NULL DEFAULT NULL COMMENT 'Principal\'s vision' ,
CHANGE COLUMN `principal_involvement` `principal_involvement` INT(11) NULL DEFAULT NULL COMMENT 'Principal\'s involvement in the process' ,
CHANGE COLUMN `principal_openness` `principal_openness` INT(11) NULL DEFAULT NULL COMMENT 'Principal\'s openness to review' ,
CHANGE COLUMN `middle_leaders` `middle_leaders` INT(11) NULL DEFAULT NULL COMMENT 'Middle leaders' ,
CHANGE COLUMN `parent_teacher_association` `parent_teacher_association` INT(11) NULL DEFAULT NULL COMMENT 'Parent Teacher Association' ,
CHANGE COLUMN `student_body_activity` `student_body_activity` INT(11) NULL DEFAULT NULL COMMENT 'Student body' ,
CHANGE COLUMN `student_body_school_level` `student_body_school_level` INT(11) NULL DEFAULT NULL COMMENT 'Student body - Level' ,
CHANGE COLUMN `average_staff_tenure` `average_staff_tenure` INT(11) NULL DEFAULT NULL COMMENT 'Average Staff Tenure (in years)' ,
CHANGE COLUMN `average_number_students_class` `average_number_students_class` INT(11) NULL DEFAULT NULL COMMENT 'Average number of students in each class' ,
CHANGE COLUMN `ratio_students_class_size` `ratio_students_class_size` INT(11) NULL DEFAULT NULL COMMENT 'Ratio of students to class size' ,
CHANGE COLUMN `number_teaching_staff` `number_teaching_staff` INT(11) NULL DEFAULT NULL COMMENT 'Average teacher student ratio' ,
CHANGE COLUMN `number_non_teaching_staff_prep` `number_non_teaching_staff_prep` INT(11) NULL DEFAULT NULL COMMENT 'Teaching - Non teaching staff ratio (Prep)' ,
CHANGE COLUMN `number_non_teaching_staff_rest` `number_non_teaching_staff_rest` INT(11) NULL DEFAULT NULL COMMENT 'Teaching - Non teaching staff ratio (Other)' ,
CHANGE COLUMN `alumni_association` `alumni_association` INT(11) NULL DEFAULT NULL COMMENT 'Alumni Association' ,
CHANGE COLUMN `rte` `rte` TINYINT(1) NULL DEFAULT NULL COMMENT '25% RTE Reservations' ,
CHANGE COLUMN `action_planning` `action_planning` TEXT NULL DEFAULT NULL COMMENT 'Action planning area chosen by the school' ;


ALTER TABLE `d_post_review` 
CHANGE COLUMN `action_management_decision` `action_management_decision` INT(11) NULL DEFAULT NULL COMMENT 'Action by management on decisions for improvement reported by Principal' ;

ALTER TABLE `adh`.`h_province_network` 
DROP FOREIGN KEY `fk_d_network_h_province_network`,
DROP FOREIGN KEY `fkk_d_province_h_network_province`;
ALTER TABLE `adh`.`h_province_network` 
CHANGE COLUMN `network_id` `network_id` INT(11) NOT NULL COMMENT '' ,
CHANGE COLUMN `province_id` `province_id` INT(11) NOT NULL COMMENT '' ;
ALTER TABLE `adh`.`h_province_network` 
ADD CONSTRAINT `fk_d_network_h_province_network`
  FOREIGN KEY (`network_id`)
  REFERENCES `adh`.`d_network` (`network_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fkk_d_province_h_network_province`
  FOREIGN KEY (`province_id`)
  REFERENCES `adh`.`d_province` (`province_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


alter table d_review_engagement add column engagement_type_text varchar(255) after engagement_type;
UPDATE `adh`.`d_review_engagement` SET `engagement_type_text`='The management is actively involved in the functioning of the school and leads all the decision making for every aspect of the school and provides direction to the Principal for implementation.  ' WHERE `engagement_id`='1';
UPDATE `adh`.`d_review_engagement` SET `engagement_type_text`='Management oversees the functioning of the school and involves key stakeholders in the decision making for every aspect of the school and ensures that they are implemented.' WHERE `engagement_id`='2';
UPDATE `adh`.`d_review_engagement` SET `engagement_type_text`='Management is available when needed by the school team & Principal, but the Principal is accountable for smooth functioning and decision making for all aspects of the school.' WHERE `engagement_id`='3';
UPDATE `adh`.`d_review_engagement` SET `engagement_type_text`='is hardly involved in any aspects of the school functioning.' WHERE `engagement_id`='4';

alter table d_review_midleaders add column midleaders_text varchar(255) after status;
alter table d_review_association add column( parent_association_text varchar(255), alumni_association_text varchar(255));

UPDATE `adh`.`d_review_association` SET `parent_association_text`='Parents are actively involved in the life of the school and support the School Principal and Management for all the activities and events.', `alumni_association_text`='Alumni are actively involved in the life of the school and support the School Principal and Management for all the activities and events.' WHERE `association_id`='1';
UPDATE `adh`.`d_review_association` SET `parent_association_text`='Parents involvement in the school is limited to any complaint or concern they have about their child.  They actively question and challenge the functioning of the school.' WHERE `association_id`='2';
UPDATE `adh`.`d_review_association` SET `parent_association_text`='Parents participate in the activities and events of the school when they are invited by the Principal and Management.', `alumni_association_text`='Alumni participate in the activities and events of the school when they are invited by the Principal and Management.' WHERE `association_id`='3';
UPDATE `adh`.`d_review_association` SET `parent_association_text`='Parents are hardly involved in any aspects of the school life and have no forum to share their grievances.  They complaint about the school outside the school gates.' WHERE `association_id`='4';


UPDATE `adh`.`d_student_body` SET `student_body_text`='Active Supportive' WHERE `student_body_id`='1';
UPDATE `adh`.`d_student_body` SET `student_body_text`='Active Combative' WHERE `student_body_id`='2';
UPDATE `adh`.`d_student_body` SET `student_body_text`='Passive Supportive' WHERE `student_body_id`='3';
INSERT INTO `adh`.`d_student_body` (`student_body_id`, `student_body_text`) VALUES ('4', 'Passive Combative');
INSERT INTO `adh`.`d_student_body` (`student_body_id`, `student_body_text`) VALUES ('5', 'Doesn\'t exist');

alter table d_student_body add column student_body_text_desc varchar(255);

UPDATE `adh`.`d_student_body` SET `student_body_text_desc`='Students are actively involved in the life of the school to lead and support all the activities and events.  They have an active voice in the running of the school and contribute effectively in their role as student leaders.' WHERE `student_body_id`='1';
UPDATE `adh`.`d_student_body` SET `student_body_text_desc`='Student involvement in the school is limited to any complaint or concern they have about their peers.  They actively question and challenge the functioning of the school to each other.' WHERE `student_body_id`='2';
UPDATE `adh`.`d_student_body` SET `student_body_text_desc`='Students participate or support the activities and events of the school based on their skillset and follow the instructions of their teachers and Principal in conducting them.' WHERE `student_body_id`='3';
UPDATE `adh`.`d_student_body` SET `student_body_text_desc`='Students are hardly involved in any aspects of the school life and have no voice to share their concerns and feedback.  They complaint about the school to each other.' WHERE `student_body_id`='4';

alter table d_post_review change column action_planning `comments` text;
UPDATE `adh`.`d_review_engagement` SET `engagement_type_text`='Management is hardly involved in any aspects of the school functioning.' WHERE `engagement_id`='4';

CREATE TABLE `adh`.`h_post_review_action_planning` (
  `h_post_review_action_id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `post_review_id` INT(11) NOT NULL COMMENT '',
  `kpa_instance_id` INT(11) NOT NULL COMMENT '',
  `key_question_instance_id` INT(11) NOT NULL COMMENT '',
  `action_planning` INT(11) NOT NULL COMMENT '',
  PRIMARY KEY (`h_post_review_action_id`)  COMMENT '',
  INDEX `fk_d_post_review_h_post_review_action_planning_idx` (`post_review_id` ASC)  COMMENT '',
  INDEX `fk_h_post_review_action_planning_h_kpa_diagnostic_idx` (`kpa_instance_id` ASC)  COMMENT '',
  INDEX `fk_h_post_review_action_planning_h_kpa_kq_idx` (`key_question_instance_id` ASC)  COMMENT '',
  CONSTRAINT `fk_d_post_review_h_post_review_action_planning`
    FOREIGN KEY (`post_review_id`)
    REFERENCES `adh`.`d_post_review` (`post_review_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_h_post_review_action_planning_h_kpa_diagnostic`
    FOREIGN KEY (`kpa_instance_id`)
    REFERENCES `adh`.`h_kpa_diagnostic` (`kpa_instance_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_h_post_review_action_planning_h_kpa_kq`
    FOREIGN KEY (`key_question_instance_id`)
    REFERENCES `adh`.`h_kpa_kq` (`key_question_instance_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


CREATE TABLE `adh`.`h_post_review_action_planning_core_question` (
  `h_post_review_cq_id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '',
  `h_post_review_action_id` INT(11) NOT NULL COMMENT '',
  `core_question_instance_id` INT(11) NOT NULL COMMENT '',
  PRIMARY KEY (`h_post_review_cq_id`)  COMMENT '',
  INDEX `fk_h_post_review_action_planning_core_question_h_kq_cq_idx` (`core_question_instance_id` ASC)  COMMENT '',
  INDEX `fk_h_post_review_action_planning_core_question_h_post_revie_idx` (`h_post_review_action_id` ASC)  COMMENT '',
  CONSTRAINT `fk_h_post_review_action_planning_core_question_h_kq_cq`
    FOREIGN KEY (`core_question_instance_id`)
    REFERENCES `adh`.`h_kq_cq` (`core_question_instance_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_h_post_review_action`
    FOREIGN KEY (`h_post_review_action_id`)
    REFERENCES `adh`.`h_post_review_action_planning` (`h_post_review_action_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


ALTER TABLE `adh`.`h_post_review_action_planning_core_question` 
DROP FOREIGN KEY `fk_h_post_review_action`;
ALTER TABLE `adh`.`h_post_review_action_planning_core_question` 
CHANGE COLUMN `h_post_review_cq_id` `post_review_id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '' ;
ALTER TABLE `adh`.`h_post_review_action_planning_core_question` 
ADD CONSTRAINT `fk_d_post_review_post_review_id`
  FOREIGN KEY (`post_review_id`)
  REFERENCES `adh`.`d_post_review` (`post_review_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


ALTER TABLE `adh`.`h_post_review_action_planning_core_question` 
DROP FOREIGN KEY `fk_d_post_review_post_review_id`;
ALTER TABLE `adh`.`h_post_review_action_planning_core_question` 
CHANGE COLUMN `post_review_id` `post_review_id` INT(11) NOT NULL COMMENT '' ,
CHANGE COLUMN `h_post_review_action_id` `h_post_review_action_id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '' ,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`h_post_review_action_id`)  COMMENT '';
ALTER TABLE `adh`.`h_post_review_action_planning_core_question` 
ADD CONSTRAINT `fk_d_post_review_post_review_id`
  FOREIGN KEY (`post_review_id`)
  REFERENCES `adh`.`d_post_review` (`post_review_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


ALTER TABLE `adh`.`h_post_review_action_planning` 
CHANGE COLUMN `action_planning` `action_planning` TEXT NULL COMMENT '' ;


ALTER TABLE `adh`.`h_post_review_action_planning_core_question` 
ADD COLUMN `key_question_instance_id` INT(11) NOT NULL COMMENT '' AFTER `core_question_instance_id`,
ADD INDEX `fk_h_kpa_kq_key_question_instance_id_idx` (`key_question_instance_id` ASC)  COMMENT '';
ALTER TABLE `adh`.`h_post_review_action_planning_core_question` 
ADD CONSTRAINT `fk_h_kpa_kq_key_question_instance_id`
  FOREIGN KEY (`key_question_instance_id`)
  REFERENCES `adh`.`h_kpa_kq` (`key_question_instance_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;



alter table d_post_review add column student_count int(5) after comments;

alter table d_post_review modify column student_count int(5) comment 'Student enrolment count for the whole school' after comments;