ALTER TABLE ``h_network_report_student`` DROP INDEX ``client_id``;

/////////////////////////////////////////////////////////////////////////////

CREATE TABLE `h_network_report_student_client` (
  `report_client_id` int(11) NOT NULL,
  `h_network_report_student_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_network_report_student_client`
--
ALTER TABLE `h_network_report_student_client`
  ADD PRIMARY KEY (`report_client_id`),
  ADD KEY `h_network_report_student_id` (`h_network_report_student_id`),
  ADD KEY `client_id` (`client_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_network_report_student_client`
--
ALTER TABLE `h_network_report_student_client`
  MODIFY `report_client_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `h_network_report_student_client`
--
ALTER TABLE `h_network_report_student_client`
  ADD CONSTRAINT `h_network_report_student_client_ibfk_1` FOREIGN KEY (`h_network_report_student_id`) REFERENCES `h_network_report_student` (`h_network_report_student_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `h_network_report_student_client_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `d_client` (`client_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;


//////////////////////////////////////////////////////////////////////////////


CREATE TABLE `h_network_report_student_province` (
  `report_province_id` int(11) NOT NULL,
  `h_network_report_student_id` int(11) NOT NULL,
  `province_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_network_report_student_province`
--
ALTER TABLE `h_network_report_student_province`
  ADD PRIMARY KEY (`report_province_id`),
  ADD KEY `province_id` (`province_id`),
  ADD KEY `h_network_report_student_id` (`h_network_report_student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_network_report_student_province`
--
ALTER TABLE `h_network_report_student_province`
  MODIFY `report_province_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `h_network_report_student_province`
--
ALTER TABLE `h_network_report_student_province`
  ADD CONSTRAINT `h_network_report_student_province_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `d_province` (`province_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `h_network_report_student_province_ibfk_2` FOREIGN KEY (`h_network_report_student_id`) REFERENCES `h_network_report_student` (`h_network_report_student_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;


////////////////////////////

insert into `h_network_report_student_province` (h_network_report_student_id,province_id) 
select h_network_report_student_id,province_id from h_network_report_student where province_id>0;


insert into `h_network_report_student_client` (h_network_report_student_id,client_id) 
select h_network_report_student_id,client_id from h_network_report_student where client_id>0;





