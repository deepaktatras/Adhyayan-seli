CREATE TABLE `d_frequency` (
  `frequency_id` int(11) NOT NULL,
  `frequecy_text` varchar(100) NOT NULL,
  `frequency_days` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `d_frequency`
--

INSERT INTO `d_frequency` (`frequency_id`, `frequecy_text`, `frequency_days`) VALUES
(1, 'Fortnightly', 1),
(2, 'Weekly', 7),
(3, 'Monthly', 30),
(4, 'Quarterly', 90),
(5, 'Half yearly', 180),
(6, 'Annually', 365);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `d_frequency`
--
ALTER TABLE `d_frequency`
  ADD PRIMARY KEY (`frequency_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `d_frequency`
--
ALTER TABLE `d_frequency`
  MODIFY `frequency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;COMMIT;

///////////////////////////////////////////////////

CREATE TABLE `h_assessor_action1` (
  `h_assessor_action1_id` int(11) NOT NULL,
  `assessor_key_notes_id` int(11) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `leader` int(11) DEFAULT NULL,
  `frequency_report` int(11) DEFAULT NULL,
  `reporting_authority` text NOT NULL,
  `action_status` int(11) NOT NULL,
  `createDate` datetime NOT NULL,
  `modifyDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedBy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_assessor_action1`
--
ALTER TABLE `h_assessor_action1`
  ADD PRIMARY KEY (`h_assessor_action1_id`),
  ADD KEY `assessor_key_notes_id` (`assessor_key_notes_id`),
  ADD KEY `leader` (`leader`),
  ADD KEY `frequency_report` (`frequency_report`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_assessor_action1`
--
ALTER TABLE `h_assessor_action1`
  MODIFY `h_assessor_action1_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `h_assessor_action1`
--
ALTER TABLE `h_assessor_action1`
  ADD CONSTRAINT `h_assessor_action1_ibfk_1` FOREIGN KEY (`assessor_key_notes_id`) REFERENCES `assessor_key_notes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `h_assessor_action1_ibfk_2` FOREIGN KEY (`leader`) REFERENCES `d_user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `h_assessor_action1_ibfk_3` FOREIGN KEY (`frequency_report`) REFERENCES `d_frequency` (`frequency_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;


///////////////////////////////////////////////////////////////////////

CREATE TABLE `h_assessor_action1_impact` (
  `assessor_action1_impact_id` int(11) NOT NULL,
  `assessor_action1_id` int(11) NOT NULL,
  `designation_id` int(11) NOT NULL,
  `impact_statement` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_assessor_action1_impact`
--
ALTER TABLE `h_assessor_action1_impact`
  ADD PRIMARY KEY (`assessor_action1_impact_id`),
  ADD KEY `designation_id` (`designation_id`),
  ADD KEY `h_assessor_action1_impact_ibfk_1` (`assessor_action1_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_assessor_action1_impact`
--
ALTER TABLE `h_assessor_action1_impact`
  MODIFY `assessor_action1_impact_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `h_assessor_action1_impact`
--
ALTER TABLE `h_assessor_action1_impact`
  ADD CONSTRAINT `h_assessor_action1_impact_ibfk_1` FOREIGN KEY (`assessor_action1_id`) REFERENCES `h_assessor_action1` (`h_assessor_action1_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `h_assessor_action1_impact_ibfk_2` FOREIGN KEY (`designation_id`) REFERENCES `d_designation` (`designation_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;





///////////////////////////////////////////////////////////////////////////////////

ALTER TABLE `assessor_key_notes` ADD `recommendation_id` INT NULL DEFAULT NULL AFTER `judgement_statement_instance_id`;
ALTER TABLE `assessor_key_notes` ADD `rec_type` INT NOT NULL DEFAULT '0' AFTER `recommendation_id`;

ALTER TABLE `h_assessment_user` ADD `action_planning_status` INT NOT NULL DEFAULT '0' AFTER `percComplete`;

//////////////////////////////////////////////////////////////////////////////////////////
ALTER TABLE `d_frequency` CHANGE `frequency_days` `frequency_days` VARCHAR(100) NOT NULL;

UPDATE `d_frequency` SET `frequency_days` = '+1 day' WHERE `d_frequency`.`frequency_id` = 1;
UPDATE `d_frequency` SET `frequency_days` = '+1 week' WHERE `d_frequency`.`frequency_id` = 2;
UPDATE `d_frequency` SET `frequency_days` = '+1 month' WHERE `d_frequency`.`frequency_id` = 3;
UPDATE `d_frequency` SET `frequency_days` = '+3 month' WHERE `d_frequency`.`frequency_id` = 4;
UPDATE `d_frequency` SET `frequency_days` = '+6 month' WHERE `d_frequency`.`frequency_id` = 5;
UPDATE `d_frequency` SET `frequency_days` = '+1 year' WHERE `d_frequency`.`frequency_id` = 6;


////////////////////////////////

ALTER TABLE `h_review_action2_activity` DROP `activity_stackholder`;
ALTER TABLE `h_review_action2_activity_postponed` DROP `activity_stackholder`;


CREATE TABLE `h_review_action2_activity_stackholder` (
  `h_review_action2_activity_stackholder_id` int(11) NOT NULL,
  `h_review_action2_activity_id` int(11) NOT NULL,
  `activity_stackholder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_review_action2_activity_stackholder`
--
ALTER TABLE `h_review_action2_activity_stackholder`
  ADD PRIMARY KEY (`h_review_action2_activity_stackholder_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_review_action2_activity_stackholder`
--
ALTER TABLE `h_review_action2_activity_stackholder`
  MODIFY `h_review_action2_activity_stackholder_id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;


////////////////////////////////////////////////////////////////


CREATE TABLE `h_review_action2_activity_stackholder_postponed` (
  `h_review_action2_activity_stackholder_postponed_id` int(11) NOT NULL,
  `h_review_action2_activity_postponed_id` int(11) NOT NULL,
  `activity_stackholder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_review_action2_activity_stackholder_postponed`
--
ALTER TABLE `h_review_action2_activity_stackholder_postponed`
  ADD PRIMARY KEY (`h_review_action2_activity_stackholder_postponed_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_review_action2_activity_stackholder_postponed`
--
ALTER TABLE `h_review_action2_activity_stackholder_postponed`
  MODIFY `h_review_action2_activity_stackholder_postponed_id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;


//////////////////////////////////////////////////////


INSERT INTO `d_activity` (`activity`, `active`) VALUES ('Research', '1');
INSERT INTO `d_activity` (`activity`, `active`) VALUES ('Planning', '1');
INSERT INTO `d_activity` (`activity`, `active`) VALUES ('Orientation', '1');
INSERT INTO `d_activity` (`activity`, `active`) VALUES ('Exposure', '1');
INSERT INTO `d_activity` (`activity`, `active`) VALUES ('Professional Development', '1');
INSERT INTO `d_activity` (`activity`, `active`) VALUES ('Review', '1');

/////////////////////////////////////////////////////////////////////////
ALTER TABLE `d_activity` ADD `symbol` VARCHAR(50) NOT NULL AFTER `active`;
ALTER TABLE `d_activity` CHANGE `symbol` `symbol` VARCHAR(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;







