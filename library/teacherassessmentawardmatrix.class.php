<?php

class teacherAssessmentAwardMatrix{
	var $noOfScore1234s=array(1=>0,2=>0,3=>0,4=>0);
	function teacherAssessmentAwardMatrix($values){
		foreach($values as $v)
			$this->noOfScore1234InKpas[$v]++;
	}
}