/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  deepak
 * Created: 4 May, 2017
 */

ALTER TABLE `d_AQS_data` ADD `num_class_rooms` INT(11) NOT NULL AFTER `no_of_students`;
ALTER TABLE `d_AQS_data` ADD `airport_distance` DOUBLE NOT NULL AFTER `rail_station_name`, ADD `rail_station_distance` DOUBLE NOT NULL AFTER `airport_distance`;


ALTER TABLE `d_AQS_data` ADD `aqs_school_recognised` INT NOT NULL AFTER `school_region_id`, ADD `aqs_school_registration_num` VARCHAR(255) NOT NULL AFTER `aqs_school_recognised`, ADD `aqs_school_minority` INT NOT NULL AFTER `aqs_school_registration_num`;

INSERT INTO h_assessment_school_type (school_type_id, assessment_id)
SELECT aq.school_type_id,d.assessment_id FROM `d_AQS_data` aq INNER JOIN d_assessment d ON aq.id = d.aqsdata_id


-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 04, 2017 at 11:57 AM
-- Server version: 10.0.29-MariaDB-0ubuntu0.16.04.1
-- PHP Version: 5.6.30-10+deb.sury.org~xenial+2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `adh`
--

-- --------------------------------------------------------

--
-- Table structure for table `h_assessment_school_type`
--

CREATE TABLE `h_assessment_school_type` (
  `id` int(10) NOT NULL,
  `school_type_id` int(10) NOT NULL,
  `assessment_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_assessment_school_type`
--
ALTER TABLE `h_assessment_school_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_type_id` (`school_type_id`,`assessment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_assessment_school_type`
--
ALTER TABLE `h_assessment_school_type`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1024;



