/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  deepak
 * Created: 20 Dec, 2017
 */


CREATE TABLE `h_user_review_reim_sheet_status` (
  `id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sheet_status` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `h_user_review_reim_sheet_status`
--
ALTER TABLE `h_user_review_reim_sheet_status`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_user_review_reim_sheet_status`
--
ALTER TABLE `h_user_review_reim_sheet_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


INSERT INTO `d_review_notification_template` (`id`, `template_text`, `template_type`) VALUES (NULL, 'Dear <b>_name_</b>, 

Thank you for your contribution to the AQS review held at following school :

<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>

Please confirm if the reimbursement sheet has been received from following Assessors.

_form_

Thanks,
 
Best wishes,

Adhyayan Quality Education Services Pvt. Ltd.y', '8');

INSERT INTO `h_review_notification_mail_users` (`id`, `notification_id`, `subject`, `sender`, `cc`, `status`) VALUES (NULL, '8', 'Confirmation regarding Reimbursement sheet for _school_', 'info@adhyayan.asia', 'amisha.modi@adhyayan.asia', '1');