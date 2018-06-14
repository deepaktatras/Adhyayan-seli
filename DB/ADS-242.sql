/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  deepak
 * Created: 5 Feb, 2018
 */

ALTER TABLE `d_countries` ADD `phonecode` INT NOT NULL AFTER `country_name`;
UPDATE `d_countries` SET `phonecode` = '91' WHERE `d_countries`.`country_id` = 101;
UPDATE `d_countries` SET `phonecode` = '977' WHERE `d_countries`.`country_id` = 153;
UPDATE `d_countries` SET `phonecode` = '27' WHERE `d_countries`.`country_id` = 202;