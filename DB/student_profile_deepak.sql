--
-- Table structure for table `d_student_review_form`
--

CREATE TABLE `d_student_review_form` (
  `id` int(11) NOT NULL,
  `form_name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `d_student_review_form`
--

INSERT INTO `d_student_review_form` (`id`, `form_name`, `status`) VALUES
(1, 'student profile', 1);

-- --------------------------------------------------------

--
-- Table structure for table `d_student_review_form_attributes`
--

CREATE TABLE `d_student_review_form_attributes` (
  `field_id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_label` varchar(100) NOT NULL,
  `field_type` varchar(100) NOT NULL,
  `value_type` int(11) NOT NULL DEFAULT '1',
  `rank` int(10) NOT NULL,
  `visibility` int(11) NOT NULL DEFAULT '1',
  `class` varchar(255) NOT NULL,
  `parent` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `d_student_review_form_attributes`
--

INSERT INTO `d_student_review_form_attributes` (`field_id`, `field_name`, `field_label`, `field_type`, `value_type`, `rank`, `visibility`, `class`, `parent`) VALUES
(1, 'batch_code', 'Batch code', 'text', 1, 1, 1, '', 0),
(2, 'site', 'Site', 'text', 1, 2, 1, '', 0),
(3, 'city', 'City', 'city', 1, 4, 1, '', 0),
(4, 'student-UID', 'Student UID', 'text', 1, 5, 1, '', 0),
(5, 'name', 'Name', 'text', 1, 6, 1, '', 0),
(8, 'gender', 'Gender', 'radio', 1, 8, 1, '', 0),
(9, 'address', 'Address', 'text_area', 1, 10, 1, '', 0),
(10, 'contact_num1', 'Contact Number 1', 'text', 3, 11, 1, '', 0),
(11, 'contact_num2', 'Contact Number 2', 'text', 3, 12, 1, '', 0),
(12, 'current_edu', 'Current Education', 'text', 1, 13, 1, '', 0),
(13, 'vocational_course', 'Vocational Course (If any)', 'text', 1, 14, 1, '', 0),
(14, 'dropped_out_school', 'Dropped out from school', 'radio', 1, 15, 1, 'drop_out', 0),
(15, 'dropped_reason', 'Reason for dropping out', 'text_area', 1, 16, 0, 'drop_out', 14),
(16, 'currently_working', 'Working right now?', 'radio', 1, 17, 1, 'working', 0),
(17, 'employment_sector', 'Sector of employment', 'text', 1, 18, 0, 'working', 16),
(18, 'position', 'Position', 'text', 1, 19, 0, 'working', 16),
(19, 'current_ctc', 'Current Salary CTC (Monthly)', 'text', 1, 20, 0, 'working', 16),
(20, 'working_since', 'Working since (MM/YYYY)', 'date', 1, 21, 0, 'working', 16),
(21, 'previous_work_experience', 'Any previous work experience?', 'radio', 1, 22, 1, 'previous_exp', 0),
(22, 'previous_employment_sector', 'Sector of employment', 'text', 1, 23, 0, 'previous_exp', 21),
(23, 'previous_position', 'Position', 'text', 1, 24, 0, 'previous_exp', 21),
(24, 'previous_ctc', 'Last Drawn Salary CTC (Monthly)', 'text', 1, 25, 0, 'previous_exp', 21),
(25, 'previous_worked_since', 'Working since (MM/YYYY)', 'date', 1, 26, 0, 'previous_exp', 21),
(26, 'mother_education', 'Mother''s education', 'text', 1, 27, 1, '', 0),
(27, 'mother_position', 'Mother''s Position', 'text', 1, 28, 1, '', 0),
(28, 'mother_employment_sector', 'Mother''s sector of employment', 'text', 1, 29, 1, '', 0),
(29, 'mother_ctc', 'Mother''s Monthly Income CTC', 'text', 1, 30, 1, '', 0),
(30, 'father_education', 'Father''s education', 'text', 1, 31, 1, '', 0),
(31, 'father_position', 'Father''s Position', 'text', 1, 32, 1, '', 0),
(32, 'father_employment_sector', 'Father''s sector of employment', 'text', 1, 33, 1, '', 0),
(33, 'father_ctc', 'Father''s Monthly Income CTC', 'text', 1, 34, 1, '', 0),
(34, 'sibling1_education', 'Sibling 1 education', 'text', 1, 35, 1, '', 0),
(35, 'sibling1_employment_sctor', 'Sibling 1 sector of employment', 'text', 1, 36, 1, '', 0),
(36, 'sibling1_ctc', 'Sibling 1 Monthly Income CTC', 'text', 1, 37, 1, '', 0),
(37, 'sibling2_education', 'Sibling 2 education', 'text', 1, 38, 1, '', 0),
(38, 'sibling2_position', 'Sibling 2 Position', 'text', 1, 39, 1, '', 0),
(39, 'sibling2_employment_sector', 'Sibling 2 sector of employment', 'text', 1, 40, 1, '', 0),
(40, 'sibling2_ctc', 'Sibling 2 Monthly Income CTC', 'text', 1, 41, 1, '', 0),
(41, 'others_employment_sector', 'Others- Sector of employment', 'text', 1, 42, 1, '', 0),
(42, 'others_ctc', 'Others-Monthly Income CTC', 'text', 1, 43, 1, '', 0),
(43, 'sibling1_position', 'Sibling 1 Position', 'text', 1, 36, 1, '', 0),
(45, 'previous_worked_till', 'Worked till (MM/YYYY)', 'date', 4, 26, 0, 'previous_exp', 21),
(46, 'DOB', 'DOB (MM/DD/YYYY)', 'dob', 4, 7, 1, '', 0),
(47, 'state', 'State', 'state', 1, 3, 1, '', 0),
(49, 'is_submit', '', 'hidden', 1, 52, 0, '', 0),
(50, 'email', '', 'hidden', 1, 53, 1, '', 0);
-- --------------------------------------------------------

--
-- Table structure for table `h_form_student_mapping`
--

CREATE TABLE `h_form_student_mapping` (
  `mapping_id` int(11) NOT NULL,
  `review_form_id` int(11) NOT NULL,
  `student_form_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `h_student_review_form_attributes`
--

CREATE TABLE `h_student_review_form_attributes` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `h_student_review_form_attributes`
--

INSERT INTO `h_student_review_form_attributes` (`id`, `form_id`, `attribute_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 1, 10),
(11, 1, 11),
(12, 1, 12),
(13, 1, 13),
(14, 1, 14),
(15, 1, 15),
(16, 1, 16),
(17, 1, 17),
(18, 1, 18),
(19, 1, 20),
(20, 1, 21),
(21, 1, 22),
(22, 1, 23),
(23, 1, 24),
(24, 1, 25),
(25, 1, 26),
(26, 1, 19),
(27, 1, 27),
(28, 1, 28),
(29, 1, 29),
(30, 1, 30),
(31, 1, 31),
(32, 1, 32),
(33, 1, 33),
(34, 1, 34),
(35, 1, 35),
(36, 1, 36),
(37, 1, 37),
(38, 1, 38),
(39, 1, 39),
(40, 1, 40),
(41, 1, 41),
(42, 1, 42),
(43, 1, 43),
(44, 1, 44),
(45, 1, 45),
(46, 1, 46),
(47, 1, 47),
(48, 1, 49),
(49, 1, 50);

ALTER TABLE `d_student_review_form`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `d_student_review_form_attributes`
--
ALTER TABLE `d_student_review_form_attributes`
  ADD PRIMARY KEY (`field_id`);

--
-- Indexes for table `h_form_student_mapping`
--
ALTER TABLE `h_form_student_mapping`
  ADD PRIMARY KEY (`mapping_id`);

--
-- Indexes for table `h_student_review_form_attributes`
--
ALTER TABLE `h_student_review_form_attributes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `d_student_review_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `d_student_review_form_attributes`
--
ALTER TABLE `d_student_review_form_attributes`
  MODIFY `field_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
--
-- AUTO_INCREMENT for table `h_form_student_mapping`
--
ALTER TABLE `h_form_student_mapping`
  MODIFY `mapping_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `h_student_review_form_attributes`
--
ALTER TABLE `h_student_review_form_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;


CREATE TABLE `d_student_data` (
  `student_data_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attr_id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `d_student_data`
  ADD PRIMARY KEY (`student_data_id`),
  ADD KEY `teacher_id` (`student_id`,`attr_id`,`assessment_id`),
  ADD KEY `attr_id` (`attr_id`),
  ADD KEY `assessment_id` (`assessment_id`);

ALTER TABLE `d_student_data`
  MODIFY `student_data_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `adhyayan_staging_app_adhyayan_reloaded`.`d_student_data` 
ADD UNIQUE INDEX `student_data_id_UNIQUE` (`student_data_id` ASC),
ADD PRIMARY KEY (`student_data_id`);


ALTER TABLE `d_student_review_form_attributes`
  ADD PRIMARY KEY (`field_id`);

ALTER TABLE `d_student_review_form_attributes`
  MODIFY `field_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `d_group_assessment` ADD `aqsdata_id` INT NULL AFTER `student_review_form_id`;
ALTER TABLE `d_group_assessment` CHANGE `aqsdata_id` `grp_aqsdata_id` INT(11) NULL DEFAULT NULL;

SELECT a.group_assessment_id,aqsdata_id from d_group_assessment a left join  h_assessment_ass_group b on a.group_assessment_id=b.group_assessment_id left join d_assessment c on b.assessment_id=c.assessment_id where aqsdata_id IS NOT NULL GROUP BY a.group_assessment_id