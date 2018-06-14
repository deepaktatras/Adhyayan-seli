INSERT INTO `adh`.`d_reports` (`report_id`, `report_name`, `assessment_type_id`, `isIndividualAssessmentReport`) VALUES ('7', 'Single Teacher Recommendation Report', '2', '1');

UPDATE `adh`.`d_reports` SET `isIndividualAssessmentReport`='0' WHERE `report_id`='5';

UPDATE `adhyayan_test_01082016`.`d_reports` SET `report_name`='Teacher Performance Overview report' WHERE `report_id`='4';

UPDATE `adh`.`d_reports` SET `isIndividualAssessmentReport`='1' WHERE `report_id`='5';

