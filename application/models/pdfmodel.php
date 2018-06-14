<?php

class pdfModel extends Model{
	function getReportName($reportType)
	{
		$sql = "SELECT report_name FROM d_reports where report_id=?";
		return $this->db->get_row($sql,array($reportType));
	}
}