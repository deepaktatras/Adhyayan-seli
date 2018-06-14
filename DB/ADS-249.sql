INSERT INTO `d_teacher_attribute` (`attr_id`, `attr_name`) VALUES (NULL, 'other_experience');

INSERT INTO `d_school_level` (`school_level_id`, `school_level`) VALUES (NULL, 'Overall School');
ALTER TABLE `d_school_level` ADD `dept_type` INT NOT NULL AFTER `school_level`;
UPDATE `d_school_level` SET `dept_type` = '1' WHERE `d_school_level`.`school_level_id` = 6;

INSERT INTO `d_teacher_category` (`teacher_category`) VALUES ('Principal');

