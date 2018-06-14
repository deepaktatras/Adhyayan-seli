/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  deepak
 * Created: 2 Jan, 2018
 */
-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 02, 2018 at 05:23 PM
-- Server version: 10.0.31-MariaDB-0ubuntu0.16.04.2
-- PHP Version: 5.6.31-4+ubuntu16.04.1+deb.sury.org+4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `adh`
--

-- --------------------------------------------------------

--
-- Table structure for table `d_notification_type`
--

DROP TABLE IF EXISTS `d_notification_type`;
CREATE TABLE `d_notification_type` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `d_notification_type`
--

INSERT INTO `d_notification_type` (`id`, `type`, `status`) VALUES
(1, 'notifications', 1),
(2, 'reminders', 1);

-- --------------------------------------------------------

--
-- Table structure for table `d_review_notification_template`
--

DROP TABLE IF EXISTS `d_review_notification_template`;
CREATE TABLE `d_review_notification_template` (
  `id` int(11) NOT NULL,
  `template_text` longtext NOT NULL,
  `template_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `d_review_notification_template`
--

INSERT INTO `d_review_notification_template` (`id`, `template_text`, `template_type`) VALUES
(1, 'Dear <b>_name_</b>,\n\nGreetings from The Assessor Programme (TAP).\n\nThank you for submitting your ‘Assessor log’ for the AQS review conducted in  <b>_school_</b>.This will help to support your learning further in your journey in The Assessor Programme (TAP).\n\nIf you would like to access your Assessor logs from previous AQS reviews, you can log in your account on the Adhyayan Online Portal by clicking _link_. Click on ‘<span style="background-color:#eed00f"><b>Manage My Reviews</b> </span>’, search for the school that you reviewed and then click on ‘<span style="background-color:red; color:white"><b>Feedback</b> </span>’ which is located on the right side of the school name. You can view your log under the tab ‘Assessor log’.\n\nPlease feel free to reach out to Shraddha Khedekar on 9867447741 or shraddha.adhyayan@gmail.com if you have any queries.\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\n<b>The Assessor Programme\n\nAdhyayan Quality Education Services Pvt. Ltd.</b>', 1),
(2, 'Dear<b> _name_</b>,\n\nGreetings from The Assessor Programme (TAP).\n\nThank you for submitting feedback for your peers for the AQS review conducted in <b>_school_</b></span>. Your feedback is valuable and will be shared with your peers to support them in their professional growth. \n\nYou will be notified when you receive feedback from your peers.\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\n<b>The Assessor Programme\n\nAdhyayan Quality Education Services Pvt. Ltd.</b>', 2),
(3, 'Dear _name_,\n\nGreetings from The Assessor Programme (TAP).\n\nYou have received feedback from your peers for the AQS review conducted in  <span style="color:#d00101"><b>_school_</b></span>. \n\nTo view feedback, click  _link_.  You can view your feedback under the tab ‘Feedback Received’. Add your goals for the next review after you view the feedback. This will help you focus on your areas of development.\n\nAlternatively, you can click on ‘<span style="background-color:#eed00f"><b>Manage My Reviews<b></span>’ on the home page, search for the school that you reviewed and then click on ‘<span style="background-color:red; color:white"><b>Feedback</b></span> ‘ which is located on the right side of the school name to view feedback. \n\nPlease feel to reach out to Shraddha Khedekar on 9867447741 or shraddha.adhyayan@gmail.com if you have any queries.\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\n<b>The Assessor Programme\n\nAdhyayan Quality Education Services Pvt. Ltd.</b>', 3),
(4, '\nDear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\nWe hope you have returned feeling as enriched as the school you have worked with. The next step is to complete the following:\n\n<b>1. Please return the following original documents in hard copy to the Adhyayan Office, Mumbai within <span style="background-color:#ebebe9;color:#ffcc00"><u>10 days</u> </span>of your return:</b>\n<ul>\n<li>Boarding Passes</li>\n<li>Travel Expense Receipts</li>\n<li>Food Expense Receipts</li>\n<li>Completed Expenses Sheet (attached to this email)</li>\n</ul>\n<span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>\n<ul>\n<li>Please send the original invoices and avoid printouts of screen shots</li>\n<li>Please include the date and purpose of journey in the Travel expense sheet</li>\n<li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li>\n</ul>\n \nYou may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.\n\n<b><u>Please send the documents at the following address:</u></b>\n \nUjwala Punjabi\nA 17 Royal Industrial Estate\nNaigoan Crossroad\nWadala West\nMumbai - 400 031\nPh: 022 24174463\n \n<b>2. Please complete the following online: </b>\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’ and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li>\n\n<li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white"><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span> ‘ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li>\n</ul>\n\n<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 4),
(5, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n <span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\nWe hope you have returned feeling as enriched as the school you have worked with. The next step is to complete the following:\n\n<b>1. Please return the following original documents in hard copy to the Adhyayan Office, Mumbai within <span style="background-color:#ebebe9;color:#ffcc00"><u>10 days</u> </span>of your return:</b>\n<ul>\n<li>Boarding Passes</li>\n<li>Travel Expense Receipts</li>\n<li>Food Expense Receipts</li>\n<li>Completed Expenses Sheet (attached to this email)</li>\n</ul>\n<span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>\n<ul>\n<li>Please send the original invoices and avoid printouts of screen shots</li>\n<li>Please include the date and purpose of journey in the Travel expense sheet</li>\n<li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li>\n</ul>\n \nYou may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.\n\n<b><u>Please send the documents at the following address:</u></b>\n \nUjwala Punjabi\nA 17 Royal Industrial Estate\nNaigoan Crossroad\nWadala West\nMumbai - 400 031\nPh: 022 24174463\n \n<b>2. Please complete the following online: </b>\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n\n<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’  and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li>\n\n<li><span style="color:#53628b"><b><u>Post review form:</u></b></span> The Post review form is used to record data of the school review which helps us to support the school in it’s action plan.\n\nPlease click on ‘<span style="background-color:#eebb40"> <b>Manage My Reviews</b> </span>’ on home page, search for the school that you reviewed and then click on edit under ‘<span style="background-color:#ff950e;color:white"><b>0 %</b></span>’ (Post review) across the review. Fill in the details. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span>’ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.\n\n<li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white "><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white "><b>Save</b></span>’ to continue at a later time and click on  ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li>\n</ul>\n\n<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n \nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 5),
(6, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\nWe hope you have returned feeling as enriched as the school you have worked with. \n\n<b>Please complete the following online:</b>\n\nLog in to the Adhyayan Online Portal by clicking  _link_ to complete the details given below. \n\n<ul><li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form</u></b></span>: The Assessor log is designed for you to <b>reflect on your own contribution to the review process</b>. A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members</b>. This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b> </span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white"><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span>’  to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li></ul>\n\nWe look forward to your continued commitment and contribution to achieving<b> \'a good school for every child\'.</b>\n \nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 6),
(7, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\nWe hope you have returned feeling as enriched as the school you have worked with. \n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n \nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 7),
(8, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\nPlease confirm if the reimbursement sheet has been received from following Assessors.\n\n_form_\n\nThanks,\n \nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 8),
(9, 'Dear <b>_name_</b>,\n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\n\n<b>We would like to remind you to please complete the following online:</b>\n\nLog in to the Adhyayan Online Portal by clicking _link_  to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form</u></b></span>: The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white"><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span>’ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li></ul>\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b> \n\nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 9),
(10, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\n We would like to remind you to please complete the following:\n\n<b>1. Please return the following original documents in hard copy to the Adhyayan Office, Mumbai within <span style="background-color:#ebebe9;color:#ffcc00"><u>7 days</u> </span>of your return:</b><ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>\n<ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li></ul> \nYou may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.\n\n<b><u>Please send the documents at the following address:</u></b> \n\nUjwala Punjabi\nA 17 Royal Industrial Estate\nNaigoan Crossroad\nWadala West\nMumbai - 400 031\nPh: 022 24174463\n\n \n<b>2. Please complete the following online: </b>\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’ and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li>\n<li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white"><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span> ‘ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li>\n</ul>\n<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 10),
(11, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\n We would like to remind you to please complete the following:\n\n<b>1. Please return the following original documents in hard copy to the Adhyayan Office, Mumbai within <span style="background-color:#ebebe9;color:#ffcc00"><u>7 days</u> </span>:</b><ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>\n<ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li>\n</ul> \nYou may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.\n\n<b><u>Please send the documents at the following address:</u></b>\n\nUjwala Punjabi\nA 17 Royal Industrial Estate\nNaigoan Crossroad\nWadala West\nMumbai - 400 031\nPh: 022 24174463\n\n \n<b>2. Please complete the following online: </b>\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’ and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li></ul>\n\n<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 11),
(12, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\n\n We would like to remind you to please complete the following:\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white"><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span> ‘ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li></ul>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 12),
(13, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\n We would like to remind you to please complete the following:\n\n<b>1. Please return the following original documents in hard copy to the Adhyayan Office, Mumbai within <span style="background-color:#ebebe9;color:#ffcc00"><u>7 days</u> </span>of your return:</b><ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>\n<ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li></ul> \nYou may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.\n\n<b><u>Please send the documents at the following address:</u></b> \n\nUjwala Punjabi\nA 17 Royal Industrial Estate\nNaigoan Crossroad\nWadala West\nMumbai - 400 031\nPh: 022 24174463\n\n \n<b>2. Please complete the following online: </b>\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’ and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li>\n<li><span style="color:#53628b"><b><u>Post review form:</u> </b></span>The Post review form is used to record data of the school review which helps us to support the school in it’s action plan.\n\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on edit under <span style="background-color:#ff950e;color:white"><b>0 %</b></span>’  (Post review) across the review.Fill in the details. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span> ‘ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.\n</li>\n<li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white"><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span> ‘ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li>\n</ul>\n<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 13),
(14, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n <span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\nWe would like to remind you to please complete the following:\n\n<b>1. Please return the following original documents in hard copy to the Adhyayan Office, Mumbai within <span style="background-color:#ebebe9;color:#ffcc00"><u>7 days</u> </span>of your return:</b><ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span><ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li></ul> \nYou may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.\n\n<b><u>Please send the documents at the following address:</u></b>\n \nUjwala Punjabi\nA 17 Royal Industrial Estate\nNaigoan Crossroad\nWadala West\nMumbai - 400 031\nPh: 022 24174463\n\n \n<b>2. Please complete the following online: </b>\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’  and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li>\n<li><span style="color:#53628b"><b><u>Post review form:</u></b></span> The Post review form is used to record data of the school review which helps us to support the school in it’s action plan.\n\n\nPlease click on ‘<span style="background-color:#eebb40"> <b>Manage My Reviews</b> </span>’ on home page, search for the school that you reviewed and then click on edit under ‘<span style="background-color:#ff950e;color:white"><b>0 %</b></span>’ (Post review) across the review. Fill in the details. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span>’ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li></ul>\n<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n \nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 14),
(15, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n <span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\nWe would like to remind you to please complete the following:\n\n<b>1. Please return the following original documents in hard copy to the Adhyayan Office, Mumbai within <span style="background-color:#ebebe9;color:#ffcc00"><u>7 days</u> </span>of your return:</b><ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>\n<ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li></ul> \nYou may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.\n\n<b><u>Please send the documents at the following address:</u></b>\n \nUjwala Punjabi\nA 17 Royal Industrial Estate\nNaigoan Crossroad\nWadala West\nMumbai - 400 031\nPh: 022 24174463\n \n\n<b>2. Please complete the following online: </b>\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’  and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li>\n<li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white "><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white "><b>Save</b></span>’ to continue at a later time and click on  ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li></ul>\n<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n \nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.\n', 15),
(16, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\n We would like to remind you to please complete the following:\n\n<b>1. Please return the following original documents in hard copy to the Adhyayan Office, Mumbai within <span style="background-color:#ebebe9;color:#ffcc00"><u>7 days</u> </span>of your return:</b><ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>\n<ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li></ul> \nYou may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.\n\n<b><u>Please send the documents at the following address:</u></b> \n\nUjwala Punjabi\nA 17 Royal Industrial Estate\nNaigoan Crossroad\nWadala West\nMumbai - 400 031\nPh: 022 24174463\n\n \n<b>2. Please complete the following online: </b>\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’ and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li></ul>\n<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 16),
(17, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n <span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\nWe would like to remind you to please complete the following:\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Post review form:</u></b></span> The Post review form is used to record data of the school review which helps us to support the school in it’s action plan.\n\nPlease click on ‘<span style="background-color:#eebb40"> <b>Manage My Reviews</b> </span>’ on home page, search for the school that you reviewed and then click on edit under ‘<span style="background-color:#ff950e;color:white"><b>0 %</b></span>’ (Post review) across the review. Fill in the details. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span>’ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.\n\n<li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white "><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white "><b>Save</b></span>’ to continue at a later time and click on  ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li>\n</ul>\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b> \n\nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 17),
(18, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n <span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\nWe would like to remind you to please complete the following:\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white "><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white "><b>Save</b></span>’ to continue at a later time and click on  ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b> \n\nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 18),
(19, '\nDear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n <span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\nWe would like to remind you to please complete the following:\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul><li><span style="color:#53628b"><b><u>Post review form:</u></b></span> The Post review form is used to record data of the school review which helps us to support the school in it’s action plan.\n\n\nPlease click on ‘<span style="background-color:#eebb40"> <b>Manage My Reviews</b> </span>’ on home page, search for the school that you reviewed and then click on edit under ‘<span style="background-color:#ff950e;color:white"><b>0 %</b></span>’ (Post review) across the review. Fill in the details. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span>’ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li></ul>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b> \n\nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.', 19);

-- --------------------------------------------------------

--
-- Table structure for table `h_notification_type`
--

DROP TABLE IF EXISTS `h_notification_type`;
CREATE TABLE `h_notification_type` (
  `id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `notification_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `h_notification_type`
--

INSERT INTO `h_notification_type` (`id`, `notification_id`, `notification_type_id`) VALUES
(1, 9, 1),
(2, 9, 2),
(3, 10, 1),
(4, 10, 2);

-- --------------------------------------------------------

--
-- Table structure for table `h_review_notification_mail_users`
--

DROP TABLE IF EXISTS `h_review_notification_mail_users`;
CREATE TABLE `h_review_notification_mail_users` (
  `id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `subject` text NOT NULL,
  `sender` varchar(255) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `cc` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `h_review_notification_mail_users`
--

INSERT INTO `h_review_notification_mail_users` (`id`, `notification_id`, `subject`, `sender`, `sender_name`, `cc`, `status`) VALUES
(1, 3, 'AQS review in _school_ - Feedback received', 'amisha.modi@adhyayan.asia', 'Adhyayan', '', 1),
(2, 2, 'AQS review in _school_ - Feedback for Peers submitted ', 'amisha.modi@adhyayan.asia', 'Adhyayan', 'amisha.modi@adhyayan.asia', 1),
(3, 1, 'AQS review in _school_  - Assessor log submitted', 'amisha.modi@adhyayan.asia', 'Adhyayan', 'amisha.modi@adhyayan.asia', 1),
(4, 4, 'Post AQS Review Feedback and Reimbursement - _school_', 'info@adhyayan.asia', 'Adhyayan', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(5, 5, 'Post AQS Review Feedback and Reimbursement - _school_', 'info@adhyayan.asia', 'Adhyayan', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(6, 6, 'Post AQS Review Feedback - _school_', 'info@adhyayan.asia', 'Adhyayan', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(7, 7, 'AQS Review - _school_', 'info@adhyayan.asia', 'Adhyayan', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(8, 8, 'Confirmation regarding Reimbursement sheet for _school_', 'info@adhyayan.asia', 'Adhyayan', 'amisha.modi@adhyayan.asia', 1),
(9, 10, 'Reminder - Post AQS Review Feedback and Reimbursement-  _school_', 'info@adhyayan.asia', 'Adhyayan', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com)', 1),
(10, 11, 'Reminder - Post AQS Review Reimbursement-  _school_', 'info@adhyayan.asia', 'Adhyayan', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(11, 12, 'Reminder - Post AQS Review Feedback -  _school_', 'info@adhyayan.asia', 'Adhyayan', 'amisha.modi@adhyayan.asia', 1),
(12, 13, 'Reminder - Post AQS Review Feedback and Reimbursement -  _school_', 'info@adhyayan.asia', 'Adhyayan', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(13, 14, 'Reminder - Post AQS Review feedback and Reimbursement -  _school_', 'info@adhyayan.asia', 'Adhyayan', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(14, 15, 'Reminder - Post AQS Review Feedback and Reimbursement - _school_', 'info@adhyayan.asia', 'Adhyayan', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(15, 16, 'Reminder - Post AQS Review Reimbursement - _school_', 'info@adhyayan.asia', 'Adhyayan', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(16, 17, 'Reminder - Post AQS Review Feedback - _school_', 'info@adhyayan.asia', 'Adhyayan', 'amisha.modi@adhyayan.asia', 1),
(17, 18, 'Reminder - Post AQS Review Feedback - _school_', 'info@adhyayan.asia', 'Adhyayan', 'amisha.modi@adhyayan.asia', 1),
(18, 19, 'Reminder - Post AQS Review Feedback - _school_', 'info@adhyayan.asia', 'Adhyayan', 'amisha.modi@adhyayan.asia', 1),
(19, 9, 'Reminder - Post AQS Review Feedback - _school_', 'info@adhyayan.asia', 'Adhyayan', 'amisha.modi@adhyayan.asia', 1);

-- --------------------------------------------------------

--
-- Table structure for table `h_user_review_reminders`
--

DROP TABLE IF EXISTS `h_user_review_reminders`;
CREATE TABLE `h_user_review_reminders` (
  `id` int(11) NOT NULL,
  `reminder_id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `h_user_review_reminders`
--

INSERT INTO `h_user_review_reminders` (`id`, `reminder_id`, `assessment_id`, `user_id`, `status`, `date`) VALUES
(9, 9, 955, 58, 1, '2017-12-21 06:44:07'),
(10, 10, 955, 58, 1, '2017-12-21 06:44:07'),
(11, 9, 955, 528, 1, '2017-12-21 06:44:07'),
(12, 10, 955, 528, 1, '2017-12-21 06:44:07'),
(13, 9, 955, 315, 1, '2017-12-21 06:44:07'),
(14, 10, 955, 315, 1, '2017-12-21 06:44:07'),
(15, 9, 955, 1052, 1, '2017-12-21 06:44:07'),
(16, 10, 955, 1052, 1, '2017-12-21 06:44:07'),
(17, 9, 810, 53, 1, '2017-12-21 06:57:05'),
(18, 10, 810, 53, 1, '2017-12-21 06:57:05'),
(25, 9, 197, 61, 1, '2017-12-21 07:42:44'),
(26, 10, 197, 61, 1, '2017-12-21 07:42:44'),
(27, 9, 862, 1107, 1, '2017-12-22 10:52:09'),
(28, 10, 862, 1107, 1, '2017-12-22 10:52:09'),
(38, 9, 849, 1107, 1, '2017-12-22 12:58:59'),
(39, 10, 849, 1107, 1, '2017-12-22 12:58:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `d_notification_type`
--
ALTER TABLE `d_notification_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `d_review_notification_template`
--
ALTER TABLE `d_review_notification_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `h_notification_type`
--
ALTER TABLE `h_notification_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_type_id` (`notification_type_id`),
  ADD KEY `notification_id` (`notification_id`);

--
-- Indexes for table `h_review_notification_mail_users`
--
ALTER TABLE `h_review_notification_mail_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notification_id` (`notification_id`);

--
-- Indexes for table `h_user_review_reminders`
--
ALTER TABLE `h_user_review_reminders`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `d_notification_type`
--
ALTER TABLE `d_notification_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `d_review_notification_template`
--
ALTER TABLE `d_review_notification_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `h_notification_type`
--
ALTER TABLE `h_notification_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `h_review_notification_mail_users`
--
ALTER TABLE `h_review_notification_mail_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `h_user_review_reminders`
--
ALTER TABLE `h_user_review_reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `h_notification_type`
--
ALTER TABLE `h_notification_type`
  ADD CONSTRAINT `h_notification_type_ibfk_1` FOREIGN KEY (`notification_type_id`) REFERENCES `d_notification_type` (`id`),
  ADD CONSTRAINT `h_notification_type_ibfk_2` FOREIGN KEY (`notification_id`) REFERENCES `d_notifications` (`id`);

--
-- Constraints for table `h_review_notification_mail_users`
--
ALTER TABLE `h_review_notification_mail_users`
  ADD CONSTRAINT `fk_notification_id` FOREIGN KEY (`notification_id`) REFERENCES `d_review_notification_template` (`id`);

ALTER TABLE `h_notification_type` ADD FOREIGN KEY (`notification_type_id`) REFERENCES d_notification_type (`id` )
ALTER TABLE `h_notification_type` ADD FOREIGN KEY (`notification_id`) REFERENCES d_notifications (`id` )
ALTER TABLE `h_user_review_notification` ADD FOREIGN KEY (`type`) REFERENCES d_notification_type (`id` )
ALTER TABLE `d_notification_queue` ADD `type` INT NOT NULL AFTER `status`;
ALTER TABLE `h_user_review_notification` ADD `type` INT NOT NULL AFTER `status`;