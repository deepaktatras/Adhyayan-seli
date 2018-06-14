/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  deepak
 * Created: 20 Apr, 2018
 */




CREATE TABLE `h_impact_statement_stakeholders` (
  `id` int(11) NOT NULL,
  `impact_statement_id` int(11) NOT NULL,
  `designation_id` int(11) NOT NULL,
  `comments` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_impact_statement_stakeholders`
--
ALTER TABLE `h_impact_statement_stakeholders`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_impact_statement_stakeholders`
--
ALTER TABLE `h_impact_statement_stakeholders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;



--
-- Table structure for table `h_impact_statement`
--

CREATE TABLE `h_impact_statement` (
  `id` int(11) NOT NULL,
  `activity_method_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `comments` text NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `action_plan_id` int(11) NOT NULL,
  `statement_id` int(11) NOT NULL,
  `row_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_impact_statement`
--
ALTER TABLE `h_impact_statement`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_impact_statement`
--
ALTER TABLE `h_impact_statement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



-
-- Table structure for table `h_impact_statement_files`
--

CREATE TABLE `h_impact_statement_files` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `impact_statement_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_impact_statement_files`
--
ALTER TABLE `h_impact_statement_files`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_impact_statement_files`
--
ALTER TABLE `h_impact_statement_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



--
-- Table structure for table `h_impact_statement_classes`
--

CREATE TABLE `h_impact_statement_classes` (
  `id` int(11) NOT NULL,
  `impact_statement_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `comments` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_impact_statement_classes`
--
ALTER TABLE `h_impact_statement_classes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_impact_statement_classes`
--
ALTER TABLE `h_impact_statement_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

