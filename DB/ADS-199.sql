ALTER TABLE `h_workshops_user` ADD `leader_srno` INT NOT NULL AFTER `user_id`;
update  `h_workshops_user` set `leader_srno`=1 where `workshops_user_role_id`=1;
