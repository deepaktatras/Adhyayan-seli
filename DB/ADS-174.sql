
INSERT INTO `d_review_notification_template` (`id`, `template_text`, `template_type`) VALUES
(99, 'Dear <b>_name_</b>,\r\n\r\nHappy birthday from the Adhyayan team. \r\n\r\nWishing you all the best for the coming year.\r\n\r\n--img--\r\n\r\nWarm wishes,\r\n\r\nThe Adhyayan Team.\r\n', 99);

INSERT INTO `h_review_notification_mail_users` (`id`, `notification_id`, `subject`, `sender`, `cc`, `status`) VALUES
(99, 99, 'Happy Birthday', 'developer@tatrasdata.com', '', 0);

UPDATE `h_review_notification_mail_users` SET `sender` = 'info@adhyayan.asia' WHERE `h_review_notification_mail_users`.`id` = 99;
UPDATE `d_review_notification_template` SET `template_text` = 'Dear <b>_name_</b>,\r\n\r\nHappy birthday from the Adhyayan team. \r\n\r\nWishing you all the best for the coming year.\r\n\r\nWarm wishes,\r\n\r\nThe Adhyayan Team.\r\n\r\n--img--\r\n' WHERE `d_review_notification_template`.`id` = 99;


CREATE TABLE `h_birthday_greetings_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `sent_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `h_review_notification_mail_users` ADD `sender_name` VARCHAR(255) NOT NULL AFTER `sender`;
UPDATE `h_review_notification_mail_users` SET `sender_name` = 'Adhyayan' WHERE `h_review_notification_mail_users`.`id` = 99;
--
-- Indexes for table `h_birthday_greetings_log`
--
ALTER TABLE `h_birthday_greetings_log`
  ADD PRIMARY KEY (`id`);









