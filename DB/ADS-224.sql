/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  deepak
 * Created: 19 Jan, 2018
 */

ALTER TABLE `h_workshops_user` ADD `payment_to_facilitator` VARCHAR(255) NOT NULL AFTER `attendance_status`;
ALTER TABLE `d_resource_directory` ADD `tags` TEXT NOT NULL AFTER `directory_name`;