/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  deepak
 * Created: 30 Jan, 2018
 */

ALTER TABLE `d_resources` ADD INDEX(`resource_title`);
ALTER TABLE `d_resources` ADD `tags` TEXT NOT NULL AFTER `resource_type`;
ALTER TABLE `d_resource_directory` ADD `tags` TEXT NOT NULL AFTER `directory_name`;