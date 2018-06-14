alter table d_client add column province varchar(100) null;
alter table d_client modify column province varchar(100) after city_id;


INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('10', 'Country', 'd_country', 'country_id', 'country_name', '1');

UPDATE `d_filter_attr` SET `filter_table`='d_countries' WHERE `filter_attr_id`='10';


INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('10', '1');


INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('11', 'City', 'd_cities', 'city_id', 'city_name', '1');

INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('12', 'Province', 'd_client', 'province', 'province', '1');

INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('11', '1');

INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('12', '1');

ALTER TABLE `adh`.`h_filter_attr` 
CHANGE COLUMN `filter_attr_value` `filter_attr_value` VARCHAR(100) NULL DEFAULT NULL COMMENT '' ;





