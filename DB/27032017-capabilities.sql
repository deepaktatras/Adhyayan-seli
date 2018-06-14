INSERT INTO `adh`.`d_user_capability` (`capability_id`, `slug`, `description`) VALUES ('29', 'view_published_own_school_reports', 'user can view published reports for his school');

alter table d_assessment add column is_approved int(1) default 1;

alter table d_assessment add column rejection_reason text ;
