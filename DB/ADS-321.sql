CREATE TABLE `h_assessor_key_notes_js` (
  `h_assessor_key_notes_js_id` int(11) NOT NULL,
  `assessor_key_notes_id` int(11) NOT NULL,
  `rec_judgement_instance_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_assessor_key_notes_js`
--
ALTER TABLE `h_assessor_key_notes_js`
  ADD PRIMARY KEY (`h_assessor_key_notes_js_id`),
  ADD KEY `assessor_key_notes_id` (`assessor_key_notes_id`),
  ADD KEY `rec_judgement_instance_id` (`rec_judgement_instance_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_assessor_key_notes_js`
--
ALTER TABLE `h_assessor_key_notes_js`
  MODIFY `h_assessor_key_notes_js_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `h_assessor_key_notes_js`
--
ALTER TABLE `h_assessor_key_notes_js`
  ADD CONSTRAINT `h_assessor_key_notes_js_ibfk_1` FOREIGN KEY (`assessor_key_notes_id`) REFERENCES `assessor_key_notes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `h_assessor_key_notes_js_ibfk_2` FOREIGN KEY (`rec_judgement_instance_id`) REFERENCES `h_cq_js_instance` (`judgement_statement_instance_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;


//////////////////////////////////////////////////


CREATE TABLE `d_js_order` (
  `order_id` int(11) NOT NULL,
  `show_text` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `d_js_order`
--

INSERT INTO `d_js_order` (`order_id`, `show_text`) VALUES
(1, '1a'),
(2, '1b'),
(3, '1c'),
(4, '2a'),
(5, '2b'),
(6, '2c'),
(7, '3a'),
(8, '3b'),
(9, '3c'),
(10, '4a'),
(11, '4b'),
(12, '4c'),
(13, '5a'),
(14, '5b'),
(15, '5c'),
(16, '6a'),
(17, '6b'),
(18, '6c'),
(19, '7a'),
(20, '7b'),
(21, '7c'),
(22, '8a'),
(23, '8b'),
(24, '8c'),
(25, '9a'),
(26, '9b'),
(27, '9c');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `d_js_order`
--
ALTER TABLE `d_js_order`
  ADD PRIMARY KEY (`order_id`);
COMMIT;
