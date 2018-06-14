ALTER TABLE `d_client` ADD `client_institution_id` INT NOT NULL AFTER `client_id`;

update `d_client` set `client_institution_id`=1;

ALTER TABLE `d_client` ADD FOREIGN KEY (`client_institution_id`) REFERENCES `d_client_institution`(`client_institution_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

UPDATE `d_user_role` SET `role_name` = 'Admin' WHERE `d_user_role`.`role_id` = 5;
UPDATE `d_user_role` SET `role_name` = 'Principal' WHERE `d_user_role`.`role_id` = 6;

INSERT INTO `d_assessment_type` (`assessment_type_id`, `assessment_type_name`) VALUES (NULL, 'College');

INSERT INTO `d_diagnostic_limit_matrix` (`limit_question_type`, `limit_assessment_type`, `limit_min`, `limit_max`) VALUES
( 'kpa', '5', '1', '7'),
('kq', '5', '3', '3'),
( 'cq', '5', '3', '3'),
('jss', '5', '3', '3');


INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES (5, 1, 5, 1), (5, 1, 6, 2), (5, 1, 7, 3), (5, 1, 8, 4), (5, 2, 5, 1), (5, 2, 6, 2), (5, 2, 7, 3), (5, 2, 8, 4), (5, 3, 5, 1), (5, 3, 6, 2), (5, 3, 7, 3), (5, 3, 8, 4), (5, 4, 1, 1), (5, 4, 2, 2), (5, 4, 3, 3), (5, 4, 4, 4);


INSERT INTO `d_reports` (`report_name`, `assessment_type_id`, `isIndividualAssessmentReport`) VALUES
('CRR College report', 5, 1);

////////////////////////////

INSERT INTO `d_school_type` (`school_type_id`, `school_type`) VALUES (NULL, 'State Govt'), (NULL, 'Central Govt');
