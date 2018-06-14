/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  deepak
 * Created: 16 Jan, 2018
 */

CREATE TABLE `d_notification_reminders_template_data` (
  `template_id` int(11) NOT NULL,
  `template_data` longtext NOT NULL,
  `status` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `d_notification_reminders_template_data`
--

INSERT INTO `d_notification_reminders_template_data` (`template_id`, `template_data`, `status`, `type`) VALUES
(1, '<b>1. Please return the following original documents in hard copy to the Adhyayan Office, Mumbai within <span style="background-color:#ebebe9;color:#ffcc00"><u>7 days</u> </span>of your return:</b><ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>\n<ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li></ul> \nYou may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.\n\n<b><u>Please send the documents at the following address:</u></b> \n\nUjwala Punjabi\nA 17 Royal Industrial Estate\nNaigoan Crossroad\nWadala West\nMumbai - 400 031\nPh: 022 24174463\n', 1, 21),
(3, '<li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’ and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li>', 1, 21),
(4, '<li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.\n\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white"><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span> ‘ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li>', 1, 21),
(5, '<li><span style="color:#53628b"><b><u>Post review form:</u> </b></span>The Post review form is used to record data of the school review which helps us to support the school in it’s action plan.\n\n\nPlease click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on edit under <span style="background-color:#ff950e;color:white"><b>0 %</b></span>’  (Post review) across the review.Fill in the details. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span> ‘ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li>', 1, 21),
(6, 'Please click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on edit under <span style="background-color:#ff950e;color:white"><b>0 %</b></span>’  (Post review) across the review.Fill in the details. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span> ‘ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.', 1, 21);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `d_notification_reminders_template_data`
--
ALTER TABLE `d_notification_reminders_template_data`
  ADD PRIMARY KEY (`template_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `d_notification_reminders_template_data`
--
ALTER TABLE `d_notification_reminders_template_data`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


ALTER TABLE `d_notification_queue` ADD `final_email` INT NOT NULL AFTER `type`;
ALTER TABLE `d_review_notification_template` ADD UNIQUE(`template_type`);


INSERT INTO `d_review_notification_template` (`id`, `template_text`, `template_type`) VALUES
(20, 'Dear <b>_name_</b>, \r\n\r\nGreetings from Adhyayan !\r\n\r\nAs communicated via earlier emails, expense receipts and boarding passes have to be returned within 10 days of your return. The deadline for that has already passed. We will make an exception for you this time if you are able to send across your receipts by <b>(date of the 10th day from today)</b>. However, if we do not receive it by then, we will not be able to process your reimbursements.\r\n\r\nGoing forward we would really appreciate that you adhere to our 10 day deadline, as there are many moving  pieces here and we need to ensure that we don\'t inconvenience those involved. If you have already sent the receipts please immediately inform _inform_  over telephone or through email.\r\n\r\nWe look forward to you joining many more reviews and we hope to receive your reimbursement receipts soon.\r\n\r\nBest wishes,\r\n\r\nAdhyayan Quality Education Services Pvt. Ltd.', 20),
(22, 'Dear <b>_name_</b>, \n\nThank you for your contribution to the AQS review held at following school :\n\n<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>\n\n<b> Please complete the following online: </b>\n\nLog in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. \n<ul>_bank_details_\n_post_review_\n_assessor_self_peer_feedback_\n</ul>\n<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>\n\nWe look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>\n\nBest wishes,\n\nAdhyayan Quality Education Services Pvt. Ltd.\n', 21);

INSERT INTO `h_review_notification_mail_users` (`id`, `notification_id`, `subject`, `sender`, `sender_name`, `cc`, `status`) VALUES
(20, 20, 'Reimbursement - _school_', 'info@adhyayan.asia', 'Adhyayan', ' ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com', 1),
(21, 22, 'Reminder - Post AQS Review Feedback and Reimbursement-  _school_', 'info@adhyayan.asia', 'Adhyayan', 'ujwala.punjabi@adhyayan.asia, pritesh.chheda@adhyayan.asia, amisha.modi@adhyayan.asia, rinku.adhyayan@gmail.com, nilima.adhyayan@gmail.com)', 1);






UPDATE `d_review_notification_template` SET `template_text` = 'Dear <b>_name_</b>, 

Thank you for your contribution to the AQS review held at following school :

<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>

 We would like to remind you to please complete the following:

<b>1. The due date for returning the following original documents in hard copy to the Adhyayan Office is  <span style="background-color:#ebebe9;color:#ffcc00"><u>_sheet_date_</u> </span>  .Do note that we will be unable to process any reimbursement claim after the specified due date.</b>

<ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>
<ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li></ul> 
You may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.

<b><u>Please send the documents at the following address:</u></b> 

Ujwala Punjabi
A 17 Royal Industrial Estate
Naigoan Crossroad
Wadala West
Mumbai - 400 031
Ph: 022 24174463

 
<b>2. Please complete the following online: </b>

Log in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. 
<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’ and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li>
<li><span style="color:#53628b"><b><u>Post review form:</u> </b></span>The Post review form is used to record data of the school review which helps us to support the school in it’s action plan.


Please click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on edit under <span style="background-color:#ff950e;color:white"><b>0 %</b></span>’  (Post review) across the review.Fill in the details. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span> ‘ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.
</li>
<li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.


Please click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white"><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span> ‘ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li>
</ul>
<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>

We look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>

Best wishes,

Adhyayan Quality Education Services Pvt. Ltd.' WHERE `d_review_notification_template`.`id` = 13;



UPDATE `d_review_notification_template` SET `template_text` = 'Dear <b>_name_</b>, 

Thank you for your contribution to the AQS review held at following school :

 <span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>

We would like to remind you to please complete the following:

<b>1. The due date for returning the following original documents in hard copy to the Adhyayan Office is  <span style="background-color:#ebebe9;color:#ffcc00"><u>_sheet_date_</u> </span>  Do note that we will be unable to process any reimbursement claim after the specified due date.</b>

<ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span><ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li></ul> 
You may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.

<b><u>Please send the documents at the following address:</u></b>
 
Ujwala Punjabi
A 17 Royal Industrial Estate
Naigoan Crossroad
Wadala West
Mumbai - 400 031
Ph: 022 24174463

 
<b>2. Please complete the following online: </b>

Log in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. 
<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’  and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li>
<li><span style="color:#53628b"><b><u>Post review form:</u></b></span> The Post review form is used to record data of the school review which helps us to support the school in it’s action plan.


Please click on ‘<span style="background-color:#eebb40"> <b>Manage My Reviews</b> </span>’ on home page, search for the school that you reviewed and then click on edit under ‘<span style="background-color:#ff950e;color:white"><b>0 %</b></span>’ (Post review) across the review. Fill in the details. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span>’ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li></ul>
<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>

We look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>
 
Best wishes,

Adhyayan Quality Education Services Pvt. Ltd.' WHERE `d_review_notification_template`.`id` = 14;



UPDATE `d_review_notification_template` SET `template_text` = 'Dear <b>_name_</b>, 

Thank you for your contribution to the AQS review held at following school :

 <span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>

We would like to remind you to please complete the following:


<b>1. The due date for returning the following original documents in hard copy to the Adhyayan Office is  <span style="background-color:#ebebe9;color:#ffcc00"><u>_sheet_date_</u> </span>  .Do note that we will be unable to process any reimbursement claim after the specified due date.</b>

<ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>
<ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li></ul> 
You may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.

<b><u>Please send the documents at the following address:</u></b>
 
Ujwala Punjabi
A 17 Royal Industrial Estate
Naigoan Crossroad
Wadala West
Mumbai - 400 031
Ph: 022 24174463
 

<b>2. Please complete the following online: </b>

Log in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. 
<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’  and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li>
<li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.


Please click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white "><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white "><b>Save</b></span>’ to continue at a later time and click on  ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li></ul>
<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>

We look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>
 
Best wishes,

Adhyayan Quality Education Services Pvt. Ltd.
' WHERE `d_review_notification_template`.`id` = 15;


UPDATE `d_review_notification_template` SET `template_text` = 'Dear <b>_name_</b>, 

Thank you for your contribution to the AQS review held at following school :

<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>

 We would like to remind you to please complete the following:

<b>1. The due date for returning the following original documents in hard copy to the Adhyayan Office is  <span style="background-color:#ebebe9;color:#ffcc00"><u>_sheet_date_</u> </span>  .Do note that we will be unable to process any reimbursement claim after the specified due date.</b>

<ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>
<ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li></ul> 
You may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.

<b><u>Please send the documents at the following address:</u></b> 

Ujwala Punjabi
A 17 Royal Industrial Estate
Naigoan Crossroad
Wadala West
Mumbai - 400 031
Ph: 022 24174463

 
<b>2. Please complete the following online: </b>

Log in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. 
<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’ and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\\\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li></ul>
<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>

We look forward to your continued commitment and contribution to achieving <b>\\\'a good school for every child\\\'.</b>

Best wishes,

Adhyayan Quality Education Services Pvt. Ltd.' WHERE `d_review_notification_template`.`id` = 16;


UPDATE `d_review_notification_template` SET `template_text` = 'Dear <b>_name_</b>, 

Thank you for your contribution to the AQS review held at following school :

<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>

 We would like to remind you to please complete the following:

<b>1. The due date for returning the following original documents in hard copy to the Adhyayan Office is  <span style="background-color:#ebebe9;color:#ffcc00"><u>_sheet_date_</u> </span>  .Do note that we will be unable to process any reimbursement claim after the specified due date.</b>

<ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>
<ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li></ul> 
You may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.

<b><u>Please send the documents at the following address:</u></b> 

Ujwala Punjabi
A 17 Royal Industrial Estate
Naigoan Crossroad
Wadala West
Mumbai - 400 031
Ph: 022 24174463

 
<b>2. Please complete the following online: </b>

Log in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. 
<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’ and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li>
<li><span style="color:#53628b"><b><u>Assessor log and Assessor peer feedback form:</u></b></span> The Assessor log is designed for you to <b>reflect on your own contribution to the review process.</b> A hard copy is included in your Assessor Notepad for reference. The Assessor peer feedback form is a form through which you <b>provide feedback for your team members.</b> This helps them to take their next steps in their Assessor Journey. This feedback will be completely anonymous for everyone.


Please click on ‘<span style="background-color:#eebb40"><b>Manage My Reviews</b></span>’ on home page, search for the school that you reviewed and then click on ‘<span style="background-color:#fd0606;color:white"><b>Feedback</b></span>’ across the review. Fill in the ‘Assessor log’ and ‘Feedback for peers’ listed under the tab ‘Post – Review Reflections’. Click on ‘<span style="background-color:#d00101;color:white"><b>Save</b></span> ‘ to continue at a later time and click on ‘<span style="background-color:#d00101;color:white"><b>Submit</b></span>’ to lock your responses.</li>
</ul>
<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>

We look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>

Best wishes,

Adhyayan Quality Education Services Pvt. Ltd.' WHERE `d_review_notification_template`.`id` = 10;


UPDATE `d_review_notification_template` SET `template_text` = 'Dear <b>_name_</b>, 

Thank you for your contribution to the AQS review held at following school :

<span style="color:#d00101"><b>1) _school_, dated _sdate_ to  _edate_.</b></span>

 We would like to remind you to please complete the following:

<b>1. The due date for returning the following original documents in hard copy to the Adhyayan Office is  <span style="background-color:#ebebe9;color:#ffcc00"><u>_sheet_date_</u> </span>  .Do note that we will be unable to process any reimbursement claim after the specified due date.</b>

<ul style="margin-top:0;"><li>Boarding Passes</li><li>Travel Expense Receipts</li><li>Food Expense Receipts</li><li>Completed Expenses Sheet (attached to this email)</li></ul><span style="color:#d00101"><b><u>Important guidelines for reimbursement:</u></b></span>
<ul><li>Please send the original invoices and avoid printouts of screen shots</li><li>Please include the date and purpose of journey in the Travel expense sheet</li><li>In case you don’t have any expenses to be reimbursed, please confirm the same via e-mail</li>
</ul> 
You may refer to the attached AQS Expenses Guidance Document when completing your expenses for reimbursement.

<b><u>Please send the documents at the following address:</u></b>

Ujwala Punjabi
A 17 Royal Industrial Estate
Naigoan Crossroad
Wadala West
Mumbai - 400 031
Ph: 022 24174463

 
<b>2. Please complete the following online: </b>

Log in to the Adhyayan Online Portal by clicking _link_ to complete the details given below. 
<ul><li><span style="color:#53628b"><b><u>Assessor Bank Details:</u> </b></span>Click on ‘<span style="background-color:#eebb40"><b>My Profile </b></span>’ button and select the tab ‘<span style="background-color:#eebb40"><b>Personal Details</b></span>’ and fill in your Bank details and Pan card number <span style="color:#d00101"><b>(if you haven\'t done so already)</b></span>.  This will help us to process reimbursements in a timely manner.</li></ul>

<span style="color:#d00101"><b>The above requested details are urgently required so that we can process your Reimbursement of expenses and other payments without delay.</b></span>

We look forward to your continued commitment and contribution to achieving <b>\'a good school for every child\'.</b>

Best wishes,

Adhyayan Quality Education Services Pvt. Ltd.' WHERE `d_review_notification_template`.`id` = 11;