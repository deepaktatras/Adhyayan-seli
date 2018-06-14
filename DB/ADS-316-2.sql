CREATE TABLE `h_review_action2_activity` (
  `h_review_action2_activity_id` int(11) NOT NULL,
  `h_assessor_action1_id` int(11) NOT NULL,
  `activity_stackholder` int(11) NOT NULL,
  `activity` int(11) NOT NULL,
  `activity_details` text NOT NULL,
  `activity_status` int(11) NOT NULL DEFAULT '0',
  `activity_date` date NOT NULL,
  `activity_actual_date` date NOT NULL,
  `activity_comments` text NOT NULL,
  `createDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifyDate` datetime NOT NULL,
  `modifiedBy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_review_action2_activity`
--
ALTER TABLE `h_review_action2_activity`
  ADD PRIMARY KEY (`h_review_action2_activity_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_review_action2_activity`
--
ALTER TABLE `h_review_action2_activity`
  MODIFY `h_review_action2_activity_id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/////////////////////////////////////////////////////////////////////////////////

CREATE TABLE `h_review_action2_activity_postponed` (
  `h_review_action2_activity_postponed_id` int(11) NOT NULL,
  `h_review_action2_activity_id` int(11) NOT NULL,
  `h_assessor_action1_id` int(11) NOT NULL,
  `activity_stackholder` int(11) NOT NULL,
  `activity` int(11) NOT NULL,
  `activity_details` text NOT NULL,
  `activity_status` int(11) NOT NULL DEFAULT '0',
  `activity_date` date NOT NULL,
  `activity_actual_date` date NOT NULL,
  `activity_comments` text NOT NULL,
  `createDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifyDate` datetime NOT NULL,
  `modifiedBy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_review_action2_activity_postponed`
--
ALTER TABLE `h_review_action2_activity_postponed`
  ADD PRIMARY KEY (`h_review_action2_activity_postponed_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_review_action2_activity_postponed`
--
ALTER TABLE `h_review_action2_activity_postponed`
  MODIFY `h_review_action2_activity_postponed_id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;


////////////////////////////////////////////////////////////////////////////////////////

CREATE TABLE `h_review_action2_team` (
  `h_review_action2_team_id` int(11) NOT NULL,
  `h_assessor_action1_id` int(11) NOT NULL,
  `team_designation` int(11) NOT NULL,
  `team_member_name` varchar(255) NOT NULL,
  `createDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifyDate` datetime NOT NULL,
  `modifiedBy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_review_action2_team`
--
ALTER TABLE `h_review_action2_team`
  ADD PRIMARY KEY (`h_review_action2_team_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_review_action2_team`
--
ALTER TABLE `h_review_action2_team`
  MODIFY `h_review_action2_team_id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

///////////////////////////////////////////////////////////////////////////


CREATE TABLE `d_activity` (
  `activity_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `d_activity`
--
ALTER TABLE `d_activity`
  ADD PRIMARY KEY (`activity_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `d_activity`
--
ALTER TABLE `d_activity`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;


