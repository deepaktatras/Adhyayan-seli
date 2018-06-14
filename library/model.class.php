<?php
class Model{
	protected $_model;
	protected $db;
	
	protected $numberOfPages=0;
        protected $totalFoundRecords=0;
	function __construct() {
		$this->db=db::getInstance();
                $this->lang = isset($_COOKIE['ADH_LANG'])?$_COOKIE['ADH_LANG']:9;
	}
	
	function parse_arg($args=array(),$default=array()){
		if(is_array($args))
			foreach($args as $k=>$arg)
				$default[$k]=$arg;
		return $default;
	}
	
	function limit_query($max=10,$page=1){
		if($max==-1)
			return "";
		if(!is_numeric($max) || $max<1)
			$max=10;
		if(!is_numeric($page) || $page<1)
			$page=1;
		$offset=$max*($page-1);
		return " limit $offset,$max";
	}
	
	protected function setPageCount($maxPerPage=10){
		 $noOfRows=$this->db->get_var("SELECT FOUND_ROWS()");
		 $this->numberOfPages=ceil($noOfRows/$maxPerPage);
                $this->totalFoundRecords=$noOfRows;
	}
	
	function getPageCount(){
		return $this->numberOfPages;
	}
        
        function getTotalCount(){
		return $this->totalFoundRecords;
                
	}

	function __destruct() {
	}
}
