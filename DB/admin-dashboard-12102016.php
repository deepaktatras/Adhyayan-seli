<?php
CREATE  PROCEDURE `Pivot`(
		IN tbl_name VARCHAR(99),       -- table name (or db.tbl)
		IN base_cols VARCHAR(99),      -- column(s) on the left, separated by commas
		IN pivot_col VARCHAR(64),      -- name of column to put across the top
		IN tally_col VARCHAR(64),      -- name of column to SUM up
		IN where_clause VARCHAR(99),   -- empty string or "WHERE ..."
		IN order_by VARCHAR(99)        -- empty string or "ORDER BY ..."; usually the base_cols
		)
		DETERMINISTIC
		SQL SECURITY INVOKER
		BEGIN
		-- Find the distinct values
		-- Build the SUM()s
		SET SESSION group_concat_max_len = 999999;
		SET @subq = CONCAT('SELECT DISTINCT ', pivot_col, ' AS val ',
				' FROM ', tbl_name, ' ', where_clause, ' ORDER BY 1');
		-- select @subq;

		SET @cc1 = "CONCAT('sum( distinct IF(&p = ', &v, ', &t, 0)) AS ', &v)";
		SET @cc2 = REPLACE(@cc1, '&p', pivot_col);
		SET @cc3 = REPLACE(@cc2, '&t', tally_col);
		-- select @cc2, @cc3;
		SET @qval = CONCAT("'\"', val, '\"'");
		-- select @qval;
		SET @cc4 = REPLACE(@cc3, '&v', @qval);
		-- select @cc4;


		SET @stmt = CONCAT(
				'SELECT  GROUP_CONCAT(', @cc4, ' SEPARATOR ",\n")  INTO @sums',
				' FROM ( ', @subq, ' ) AS top');
		--  select @stmt;
		PREPARE _sql FROM @stmt;
		EXECUTE _sql;                      -- Intermediate step: build SQL for columns
		DEALLOCATE PREPARE _sql;
		-- Construct the query and perform it
		SET @stmt2 = CONCAT(
				'SELECT ',
				'ifnull(',base_cols,',"Total")',base_cols, ',\n',
				@sums,
				',\n sum( ', tally_col, ') AS Total'
				'\n FROM ', '(SELECT pivotCol,pivotRow, Count(Distinct ',tally_col,') ',tally_col,'
 FROM ',tbl_name,' where ',pivot_col,' is not null and ',base_cols,' is not null and ',tally_col,' is not null  GROUP BY ',pivot_col,',',base_cols,'
 ) As tbl', ' ',
				where_clause,
				' GROUP BY ', base_cols,
				'\n WITH ROLLUP',
				'\n', order_by
				);
		-- select @stmt2;                    -- The statement that generates the result
		PREPARE _sql FROM @stmt2;
		EXECUTE _sql;                     -- The resulting pivot table ouput
		DEALLOCATE PREPARE _sql;
		-- For debugging / tweaking, SELECT the various @variables after CALLing.
		END
		
		
---------

UPDATE `d_filter_attr` SET `active`='1' WHERE `filter_attr_id`='13';
UPDATE `d_filter_attr` SET `active`='1' WHERE `filter_attr_id`='23';
UPDATE `d_filter_attr` SET `active`='1' WHERE `filter_attr_id`='24';
UPDATE `d_filter_attr` SET `active`='1' WHERE `filter_attr_id`='25';




INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('23', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('23', '6');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('23', '8');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('24', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('24', '6');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('24', '8');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('25', '1');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('25', '6');
INSERT INTO `h_filter_attr_operator` (`filter_attr_id`, `operator_id`) VALUES ('25', '8');


INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `filter_table`, `filter_table_col_id`, `filter_table_col_name`, `active`) VALUES ('26', 'Ratings', 'd_rating', 'rating_id', 'rating', '1');

DELETE FROM `h_filter_sub_attr_operator` WHERE `operator_id`='12';

INSERT INTO `d_filter_attr` (`filter_attr_id`, `filter_attr_name`, `active`) VALUES ('27', 'Judgement Distance', '1');

INSERT INTO `h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('27', '9');
INSERT INTO `h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('27', '10');
INSERT INTO `h_filter_sub_attr_operator` (`filter_sub_attr_id`, `operator_id`) VALUES ('27', '11');

UPDATE `d_filter_attr` SET `filter_table`='t_jd', `filter_table_col_id`='judgement_statement_id', `filter_table_col_name`='jd' WHERE `filter_attr_id`='27';

UPDATE `adhyayan_test_01082016`.`d_filter_attr` SET `filter_table_col_id`='jd' WHERE `filter_attr_id`='27';
