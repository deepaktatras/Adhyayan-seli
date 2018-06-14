/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  deepak
 * Created: 10 Apr, 2018
 */

-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 10, 2018 at 06:57 PM
-- Server version: 10.0.34-MariaDB-0ubuntu0.16.04.1
-- PHP Version: 5.6.34-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `adh`
--

-- --------------------------------------------------------

--
-- Table structure for table `d_assessment_kpa`
--

CREATE TABLE `d_assessment_kpa` (
  `id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kpa_instance_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `d_assessment_kpa`
--
ALTER TABLE `d_assessment_kpa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kpa_instance_id` (`kpa_instance_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `d_assessment_kpa`
--
ALTER TABLE `d_assessment_kpa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `d_assessment_kpa`
--
ALTER TABLE `d_assessment_kpa`
  ADD CONSTRAINT `d_assessment_kpa_ibfk_1` FOREIGN KEY (`kpa_instance_id`) REFERENCES `h_kpa_diagnostic` (`kpa_instance_id`);

ALTER TABLE `h_assessment_external_team` ADD `ratingInputDate` DATETIME NOT NULL AFTER `external_client_id`, ADD `isFilled` INT(2) NOT NULL AFTER `ratingInputDate`;
ALTER TABLE `h_assessment_external_team` ADD `percComplete` VARCHAR(50) NOT NULL AFTER `isFilled`;
CREATE TABLE `h_assessor_key_notes_js` (  `h_assessor_key_notes_js_id` int(11) NOT NULL,  `assessor_key_notes_id` int(11) NOT NULL,  `rec_judgement_instance_id` int(11) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1
;---- Indexes for dumped tables------ Indexes for table `h_assessor_key_notes_js`--ALTER TABLE `h_assessor_key_notes_js`  ADD PRIMARY KEY (`h_assessor_key_notes_js_id`),  ADD KEY `assessor_key_notes_id` (`assessor_key_notes_id`),  ADD KEY `rec_judgement_instance_id` (`rec_judgement_instance_id`);---- AUTO_INCREMENT for dumped tables------ AUTO_INCREMENT for table `h_assessor_key_notes_js`--ALTER TABLE `h_assessor_key_notes_js`  MODIFY `h_assessor_key_notes_js_id` int(11) NOT NULL AUTO_INCREMENT;---- Constraints for dumped tables------ Constraints for table `h_assessor_key_notes_js`--ALTER TABLE `h_assessor_key_notes_js`  ADD CONSTRAINT `h_assessor_key_notes_js_ibfk_1` FOREIGN KEY (`assessor_key_notes_id`) REFERENCES `assessor_key_notes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,  ADD CONSTRAINT `h_assessor_key_notes_js_ibfk_2` FOREIGN KEY (`rec_judgement_instance_id`) REFERENCES `h_cq_js_instance` (`judgement_statement_instance_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;COMMIT;Sent on:4:13 pm