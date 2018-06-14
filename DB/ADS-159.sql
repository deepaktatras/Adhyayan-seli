/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  deepak
 * Created: 6 Dec, 2017
 */

CREATE TABLE `d_notification_queue` (
  `id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `d_review_notification_template` (
  `id` int(11) NOT NULL,
  `template_text` longtext NOT NULL,
  `template_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `d_review_notification_template`
--

INSERT INTO `d_review_notification_template` (`id`, `template_text`, `template_type`) VALUES
(1, 'Dear <b>_name_</b>,\n\nGreetings from The Assessor Programme (TAP).\n\nThank you for submitting your ‘Assessor log’ for the AQS review conducted in  <b>_school_</b>. This will help to support your further in your learning journey in The Assessor Programme (TAP).\n\nIf you would like to access your Assessor logs from previous AQS reviews, you can log in your account on the Adhyayan Online Portal by clicking _link_. Click on <span style="color:green"><b>‘Manage My Reviews’</b> </span>, search for the school that you reviewed and then click on <span style="color:green"><b>‘Feedback’</b> </span>which is located on the right side of the school name. You can view your log under the tab ‘Assessor log’.\n\nPlease feel free to reach out to Shraddha Khedekar on 9867447741 or shraddha.adhyayan@gmail.com if you have any queries.\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\n<b>The Assessor Programme\n\nAdhyayan Quality Education Services Pvt. Ltd.</b>', 1),
(2, 'Dear<b> _name_</b>,\n\nGreetings from The Assessor Programme (TAP).\n\nThank you for submitting feedback for your peers for the AQS review conducted in <b>_school_</b>. Your feedback is valuable and will be shared with your peers to support them in their professional growth. \n\nYou will be notified when you receive feedback from your peers.\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\n<b>The Assessor Programme\n\nAdhyayan Quality Education Services Pvt. Ltd.</b>', 2),
(3, 'Dear _name_,\n\nGreetings from The Assessor Programme (TAP).\n\nYou have received feedback from your peers for the AQS review conducted in _school_. \n\nTo view feedback, log in to your account on the Adhyayan Online Portal _link_.  You can view your feedback under the tab ‘Feedback Received’. Add your goals for the next review after you view the feedback. This will help you focus on your areas of development.\n\nAlternatively, you can click on <span style="color:green"><b>‘Manage My Reviews’<b></span> on the home page, search for the school that you reviewed and then click on <span style="color:green"><b>‘Feedback’</b></span> which is located on the right side of the school name to view feedback. \n\nPlease feel to reach out to Shraddha Khedekar on 9867447741 or shraddha.adhyayan@gmail.com if you have any queries.\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\n<b>The Assessor Programme\n\nAdhyayan Quality Education Services Pvt. Ltd.</b>', 3),
(4, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n1) <span style="color:green"><b>_school_, dated _sdate_ to  _edate_.</b></span>.\n\nWe hope you have returned feeling as enriched as the school you have worked with. The next step is to complete the following:\n \n<b>1. Please return the following original documents in hard copy to the Adhyayan Office, Mumbai within <span style="color:green">10 days </span>of your return:</b>\n<ul>\n<li>Boarding Passes</li>\n<li>Travel Expense Receipts</li>\n<li>Food Expense Receipts</li>\n<li>Completed Expenses Sheet (attached to this email)</li>\n</ul>\n<span style="color:green">Important guidelines for reimbursement:</span>\n<ul>\n<li>Please send the original invoices and avoid printouts of screen shots</li>\n<li>Please include the date and purpose of journey in the Travel expense sheet</li>\n<li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li>\n</ul>\n\n \nYou may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.\n\n<b><u>Please send the documents at the following address:</u></b>\n \nUjwala Punjabi\nA 17 Royal Industrial Estate\nNaigoan Crossroad\nWadala West\nMumbai - 400 031\nPh: 022 24174463\n\n \n<b>2. Please complete the following online: </b>\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:green"><b>Assessor Bank Details: </b></span>Click on <span style="color:green"><b>\'My Profile\' </b></span>button and select the tab <span style="color:green"><b>\'Personal Details\'</b></span> and fill in your Bank details and Pan card number <span style="color:green">(if you haven\'t done so already)</span>.  This will help us to process reimbursements in a timely manner.</li>\n\n<li><span style="color:green"><b>Assessor log and Assessor peer feedback form:</b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.</li>\n</ul>\n\nPlease click on <b>‘Manage My Reviews’ </b>on home page, search for the school that you reviewed and then click on <b>‘Feedback’</b> across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on <span style="color:green"><b>‘Save’</b></span> to continue at a later time and click on <span style="color:green"><b>‘Submit’</b></span> to lock your responses.\n\n\n<span style="color:green">The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</span>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n \nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 4),
(5, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n1) <span style="color:green"><b>_school_, dated _sdate_ to  _edate_.</b></span>.\n\nWe hope you have returned feeling as enriched as the school you have worked with. The next step is to complete the following:\n<b>1. Please return the following original documents in hard copy to the Adhyayan Office, Mumbai within <span style="color:green">10 days </span>of your return:</b>\n<ul>\n<li>Boarding Passes</li>\n<li>Travel Expense Receipts</li>\n<li>Food Expense Receipts</li>\n<li>Completed Expenses Sheet (attached to this email)</li>\n</ul>\n<span style="color:green">Important guidelines for reimbursement:</span>\n<ul>\n<li>Please send the original invoices and avoid printouts of screen shots</li>\n<li>Please include the date and purpose of journey in the Travel expense sheet</li>\n<li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li>\n</ul>\n\n \nYou may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.\n\n<b><u>Please send the documents at the following address:</u></b>\n \nUjwala Punjabi\nA 17 Royal Industrial Estate\nNaigoan Crossroad\nWadala West\nMumbai - 400 031\nPh: 022 24174463\n\n \n<b>2. Please complete the following online: </b>\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n\n<ul><li><span style="color:green"><b>Assessor Bank Details: </b></span>Click on <span style="color:green"><b>\'My Profile\' </b></span>button and select the tab <span style="color:green"><b>\'Personal Details\'</b></span> and fill in your Bank details and Pan card number <span style="color:green">(if you haven\'t done so already)</span>.  This will help us to process reimbursements in a timely manner.</li>\n\n<li><span style="color:green"><b>Post review form:</b></span> The Post review form is used to record data of the school review which helps us to support the school in it’s action plan.\n\nPlease click on <b>‘Manage My Reviews’</b> on home page, search for the school that you reviewed and then click on edit under <span style="color:green"><b>‘0 %’</b></span> (Post review) across the review. Fill in the details. Click on <span style="color:green"><b>‘Save’ </b></span>to continue at a later time and click on <span style="color:green"><b>‘Submit’</b></span> to lock your responses.\n\n<li><span style="color:green"><b>Assessor log and Assessor peer feedback form:</b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.</li>\n</ul>\n\nPlease click on <b>‘Manage My Reviews’ </b>on home page, search for the school that you reviewed and then click on <b>‘Feedback’</b> across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on <span style="color:green"><b>‘Save’</b></span> to continue at a later time and click on <span style="color:green"><b>‘Submit’</b></span> to lock your responses.\n\n\n<span style="color:green">The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</span>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n \nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 5),
(6, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:green"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\nWe hope you have returned feeling as enriched as the school you have worked with. \n\n<span style="color:green"><b>Please complete the following online:</b></span>\n\nLog in to the Adhyayan Online Portal by clicking  _link_ to complete the details given below. \n\nAssessor log and Assessor peer feedback form: The Assessor log is designed for you to <b>reflect on your own contribution to the review process</b>. A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members</b>. This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\nPlease click on <b>‘Manage My Reviews’</b> on home page, search for the school that you reviewed and then click on <span style="color:green"><b>‘Feedback’ </b></span>across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on <span style="color:green"><b>‘Save’</b></span> to continue at a later time and click on <span style="color:green"><b>‘Submit’ </b></span>to lock your responses.\n\nWe look forward to your continued commitment and contribution to achieving<b> \'a good school for every child\'.</b>\n \nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 6),
(7, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n1) <span style="color:green"><b>_school_, dated _sdate_ to  _edate_.</b></span>.\n\nWe hope you have returned feeling as enriched as the school you have worked with. \n\nWe look forward to your continued commitment and contribution to achieving \'<b>\'a good school for every child\'.</b>\n \nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 7);

-- --------------------------------------------------------

--
-- Table structure for table `h_review_notification_mail_users`
--

CREATE TABLE `h_review_notification_mail_users` (
  `id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `subject` text NOT NULL,
  `sender` varchar(255) NOT NULL,
  `cc` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `h_review_notification_mail_users`
--

INSERT INTO `h_review_notification_mail_users` (`id`, `notification_id`, `subject`, `sender`, `cc`, `status`) VALUES
(1, 3, 'AQS review in _school_ – Feedback received', 'amisha.modi@adhyayan.asia', '', 1),
(2, 2, 'AQS review in _school_ - Feedback for Peers submitted ', 'amisha.modi@adhyayan.asia', 'amisha.modi@adhyayan.asia', 1),
(3, 1, 'AQS review in _school_ – Assessor log submitted', 'amisha.modi@adhyayan.asia', 'amisha.modi@adhyayan.asia', 1),
(4, 4, 'Post AQS Review Feedback and Reimbursement - _school_', 'info@adhyayan.asia', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(5, 5, 'Post AQS Review Feedback and Reimbursement - _school_', 'info@adhyayan.asia', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(6, 6, 'Post AQS Review Feedback - _school_', 'info@adhyayan.asia', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(7, 7, 'AQS Review - _school_', 'info@adhyayan.asia', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `d_notification_queue`
--
ALTER TABLE `d_notification_queue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `d_review_notification_template`
--
ALTER TABLE `d_review_notification_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `h_review_notification_mail_users`
--
ALTER TABLE `h_review_notification_mail_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notification_id` (`notification_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `d_notification_queue`
--
ALTER TABLE `d_notification_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;
--
-- AUTO_INCREMENT for table `d_review_notification_template`
--
ALTER TABLE `d_review_notification_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `h_review_notification_mail_users`
--
ALTER TABLE `h_review_notification_mail_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `h_review_notification_mail_users`
--
ALTER TABLE `h_review_notification_mail_users`
  ADD CONSTRAINT `fk_notification_id` FOREIGN KEY (`notification_id`) REFERENCES `d_review_notification_template` (`id`);


ALTER TABLE `h_review_notification_mail_users`
  ADD CONSTRAINT `fk_notification_id` FOREIGN KEY (`notification_id`) REFERENCES `d_review_notification_template` (`id`);

ALTER TABLE h_review_notification_mail_users ADD CONSTRAINT fk_notification_id FOREIGN KEY (notification_id) REFERENCES d_review_notification_template(id);
ALTER TABLE `d_notifications` ADD `notification_type` VARCHAR(255) NOT NULL AFTER `status`;
INSERT INTO `d_notifications` (`id`, `notification_label`, `notification_name`, `status`, `notification_type`) VALUES (NULL, 'Assessor log/Peer feedback', 'assessor_peer_feedback', '1', 'review_notification');
UPDATE `d_notifications` SET `status` = '0' WHERE `d_notifications`.`id` = 8;


-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 11, 2017 at 03:12 PM
-- Server version: 10.0.31-MariaDB-0ubuntu0.16.04.2
-- PHP Version: 5.6.31-4+ubuntu16.04.1+deb.sury.org+4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `adh`
--

-- --------------------------------------------------------

--
-- Table structure for table `d_review_submit_notification_queue`
--

CREATE TABLE `d_review_submit_notification_queue` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `d_review_submit_notification_queue`
--
ALTER TABLE `d_review_submit_notification_queue`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `d_review_submit_notification_queue`
--
ALTER TABLE `d_review_submit_notification_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;