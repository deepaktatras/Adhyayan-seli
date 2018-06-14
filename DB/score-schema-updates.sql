CREATE DEFINER=`algoinsi_adh`@`%` PROCEDURE `assign_diagnostic_rating_level_scheme`()
BEGIN
DECLARE rating_level_scheme_id INT;
DECLARE diag_id INT;
DECLARE ast_type_id INT;
DECLARE inserted_id INT;
DECLARE done INT DEFAULT FALSE;
DECLARE diagnostic_rows CURSOR FOR SELECT `diagnostic_id`,`assessment_type_id` FROM d_diagnostic;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  
  OPEN diagnostic_rows;
	loop_diag: LOOP
		FETCH diagnostic_rows INTO diag_id,ast_type_id;       
         IF done THEN
		  LEAVE loop_diag;
		END IF;
        IF ast_type_id=1 THEN
			SET rating_level_scheme_id=1;
		 else
			SET rating_level_scheme_id=2;
		 END IF;
         INSERT INTO h_diagnostic_rating_level_scheme(`diagnostic_id`,`rating_level_scheme_id`) values(diag_id,rating_level_scheme_id);
         SET inserted_id = LAST_INSERT_ID();
	END LOOP;
  CLOSE diagnostic_rows;
  SELECT inserted_id;
END


CREATE TABLE `d_rating_level` (
  `rating_level_id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `rating_level_text` VARCHAR(45) NOT NULL COMMENT '',
  `rating_order` INT NOT NULL COMMENT '',
  PRIMARY KEY (`rating_level_id`)  COMMENT '');

  
INSERT INTO `d_rating_level` (`rating_level_id`, `rating_level_text`, `rating_order`) VALUES ('1', 'KPA', '1');
INSERT INTO `d_rating_level` (`rating_level_id`, `rating_level_text`, `rating_order`) VALUES ('2', 'Key Question', '2');
INSERT INTO `d_rating_level` (`rating_level_id`, `rating_level_text`, `rating_order`) VALUES ('3', 'Sub Question or Core Question', '3');
INSERT INTO `d_rating_level` (`rating_level_id`, `rating_level_text`, `rating_order`) VALUES ('4', 'Judgement Statement', '4');


CREATE TABLE `h_rating_level_scheme` (
  `rating_scheme_id` INT(4) NOT NULL COMMENT '',
  `rating_level_id` INT(4) NOT NULL COMMENT '',
  `rating_id` INT(4) NOT NULL COMMENT '',
  `rating_level_order` INT NULL COMMENT '',
  INDEX `fkx_rating_level_id_idx` (`rating_level_id` ASC)  COMMENT '',
  INDEX `fkx_rating_id_idx` (`rating_id` ASC)  COMMENT '',
  CONSTRAINT `fkx_rating_level_id`
    FOREIGN KEY (`rating_level_id`)
    REFERENCES `d_rating_level` (`rating_level_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fkx_rating_id`
    FOREIGN KEY (`rating_id`)
    REFERENCES `d_rating` (`rating_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
  
  
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '1', '5', '1');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '1', '6', '2');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '1', '7', '3');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '1', '8', '4');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '2', '5', '1');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '2', '6', '2');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '2', '7', '3');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '2', '8', '4');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '3', '5', '1');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '3', '6', '2');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '3', '7', '3');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '3', '8', '4');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '4', '1', '1');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '4', '2', '2');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '4', '3', '3');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('1', '4', '4', '4');


ALTER TABLE `h_rating_level_scheme` 
ADD INDEX `idx_rating_scheme_id_idx` (`rating_scheme_id` ASC)  COMMENT '';


CREATE TABLE `h_diagnostic_rating_level_scheme` (
  `id` INT(11) NOT NULL COMMENT '',
  `diagnostic_id` INT(11) NOT NULL COMMENT '',
  `rating_level_scheme_id` INT(11) NOT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fkx_diagnostic_id_idx` (`diagnostic_id` ASC)  COMMENT '',
  CONSTRAINT `fkx_d_diagnostic_id`
    FOREIGN KEY (`diagnostic_id`)
    REFERENCES `d_diagnostic` (`diagnostic_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


ALTER TABLE `h_diagnostic_rating_level_scheme` 
ADD INDEX `fkx_h_rating_level_scheme_id_idx` (`rating_level_scheme_id` ASC)  COMMENT '';
ALTER TABLE `h_diagnostic_rating_level_scheme` 
ADD CONSTRAINT `fkx_h_rating_level_scheme_id`
  FOREIGN KEY (`rating_level_scheme_id`)
  REFERENCES `h_rating_level_scheme` (`rating_scheme_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


INSERT INTO `d_rating` (`rating_id`, `rating`) VALUES ('21', 'Foundation');
INSERT INTO `d_rating` (`rating_id`, `rating`) VALUES ('22', 'Emerging');
INSERT INTO `d_rating` (`rating_id`, `rating`) VALUES ('23', 'Developing');
INSERT INTO `d_rating` (`rating_id`, `rating`) VALUES ('24', 'Proficient');
INSERT INTO `d_rating` (`rating_id`, `rating`) VALUES ('25', 'Exceptional');

INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '1', '21', '1');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '1', '22', '2');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '1', '23', '3');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '1', '24', '4');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '1', '25', '5');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '2', '21', '1');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '2', '22', '2');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '2', '23', '3');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '2', '24', '4');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '2', '25', '5');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '3', '21', '1');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '3', '22', '2');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '3', '23', '3');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '3', '24', '4');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '3', '25', '5');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '4', '1', '1');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '4', '2', '2');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '4', '3', '3');
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '4', '4', '4');


ALTER TABLE `h_diagnostic_rating_level_scheme` 
CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '' ;

call assign_diagnostic_rating_level_scheme();

  
  
UPDATE `d_rating_level` SET `rating_level_text`='kpa' WHERE `rating_level_id`='1';
UPDATE `d_rating_level` SET `rating_level_text`='kq' WHERE `rating_level_id`='2';
UPDATE `d_rating_level` SET `rating_level_text`='sq' WHERE `rating_level_id`='3';
UPDATE `d_rating_level` SET `rating_level_text`='js' WHERE `rating_level_id`='4';
  
INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '2', '20', '6');

UPDATE `h_rating_level_scheme` SET `rating_level_id`='3' WHERE `rating_level_id`='2' and rating_scheme_id=2 and rating_id=20;

INSERT INTO `h_rating_level_scheme` (`rating_scheme_id`, `rating_level_id`, `rating_id`, `rating_level_order`) VALUES ('2', '2', '20', '6');

  