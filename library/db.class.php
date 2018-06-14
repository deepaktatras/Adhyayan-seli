<?php

class db{
	protected $db;
	protected $stm;
	private static $instance=null;
	
	private  function __construct() {
        try {
			$this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',DB_USER,DB_PASSWORD);
		}
		catch(PDOException $e)
		{
			if(DEVELOPMENT_ENVIRONMENT)
				die($e->getMessage());
			else
				die('Error while connecting to database..');
		}
    }      
	
	public static function getInstance() {
        {
            if (self::$instance===null)
                self::$instance = new self();
            return self::$instance;
        }
    }
	
	public function get_results($sql="",$data=array()){
		if($sql!=""){
			$this->stm=$this->db->prepare($sql);
		}

		if($this->stm->execute($data)){
			return $this->stm->fetchAll(PDO::FETCH_ASSOC);
		}else{
			$this->log_error("Error while executing query (".$this->regenerateQuery($sql,$data).") \n");
			return null;
		}
	}
	
	public function get_results_withKey($sql="",$data=array(),$key=""){
		return $this->array_col_to_key($this->get_results($sql,$data),$key);
	}
	
	public function get_results_by_grouping($sql="",$data=array(),$grouping_key,$unique_key=""){
		return $this->array_grouping($this->get_results($sql,$data),$grouping_key,$unique_key);
	}
	
	public function get_row($sql,$data=array()){
		if($sql!=""){
			$this->stm=$this->db->prepare($sql);
		}
		if($this->stm->execute($data)){
			return $this->stm->fetch(PDO::FETCH_ASSOC);
		}else{
			$this->log_error("Error while executing query (".$this->regenerateQuery($sql,$data).") \n");
			return null;
		}
	}
	
	public function get_var($sql,$data=array()){
		if($sql!=""){
			$this->stm=$this->db->prepare($sql);
		}
		if($this->stm->execute($data)){
			$r=$this->stm->fetch(PDO::FETCH_NUM);
			return isset($r[0])?$r[0]:null;
		}else{
			$this->log_error("Error while executing query (".$this->regenerateQuery($sql,$data).") \n");
			return null;
		}
	}
	
	public function insert($tableName,$data=array()){
		$size=count($data);
		if($size){
			$sql="INSERT INTO `$tableName` (`".(implode("`, `",array_keys($data)))."`) VALUES(".implode(",",array_fill(0,$size,"?")).");";
			if($this->db->prepare($sql)->execute(array_values($data))){
				return true;
			}else{
				$this->log_error("Error while executing query (".$this->regenerateQuery($sql,array_values($data)).") \n");
			}
		}
		return false;
	}
	
	public function delete($tableName,$data=array()){
		if(count($data)){
			$sql="DELETE FROM `$tableName` where `".implode("`= ? and `",array_keys($data))."`= ? ;";
			if($this->db->prepare($sql)->execute(array_values($data))){
				return true;
			}else{
				$this->log_error("Error while executing query (".$this->regenerateQuery($sql,array_values($data)).") \n");
			}
		}
		return false;
	}
	
	public function update($tableName,$data=array(),$where=array()){		
		if(count($data) && count($where)){			
			$sql="UPDATE $tableName SET `".implode("`= ? , `",array_keys($data))."`= ? WHERE `".implode("`= ? and `",array_keys($where))."`= ? ;";
			if($this->db->prepare($sql)->execute(array_merge(array_values($data),array_values($where)))){
				return true;
			}else{
				$this->log_error("Error while executing query (".$this->regenerateQuery($sql,array_merge(array_values($data),array_values($where))).") \n");
			}
		}
		return false;
	}
	
	public function query($sql,$data=array()){
		if($this->db->prepare($sql)->execute($data))
			return true;
		else{
			$this->log_error("Error while executing query (".$this->regenerateQuery($sql,$data).") \n");
			return false;
		}
	}
	
	public function array_col_to_key($arr,$key){
		$res=array();
		if(count($arr) && isset($arr[0][$key])){
			foreach($arr as $r){
				$res[$r[$key]]=$r;
			}
		}
		return $res;
	}
	
	public function array_col_to_array($arr,$key){
		$res=array();
		if(count($arr) && isset($arr[0][$key])){
			foreach($arr as $r){
				$res[]=$r[$key];
			}
		}
		return $res;
	}
	
	public function array_grouping($arr,$grouping_key,$unique_key=""){
		$res=array();
		if(count($arr) && isset($arr[0][$grouping_key])){
			if($unique_key!="" && isset($arr[0][$unique_key])){
				foreach($arr as $a)
					$res[$a[$grouping_key]][$a[$unique_key]]=$a;
			}else{
				foreach($arr as $a)
					$res[$a[$grouping_key]][]=$a;
			}
		}
		return $res;
	}
	
	public function get_array_value($key,$array){
		return empty($array[$key])?false:$array[$key];
	}
	
	public function start_transaction(){
		return $this->db->beginTransaction();
	}
	
	public function commit(){
		return $this->db->commit();
	}
	
	public function rollback(){		
		return $this->db->rollBack();
	}
	
	public function get_last_insert_id(){
		return $this->db->lastInsertId();
	}
	
	protected function log_error($msg){
		if(DEVELOPMENT_ENVIRONMENT){
			die($msg);
		}else
			file_put_contents(ROOT.'tmp'.DS.'logs'.DS.'error.txt',PHP_EOL.PHP_EOL.date(DATE_RFC822).' '.$msg,FILE_APPEND);
	}
	
	public function regenerateQuery($string,$data) {
        $indexed=$data==array_values($data);
        //echo $sql;
        foreach($data as $k=>$v) {
            if(is_string($v)) $v="'$v'";
            if($indexed) $string=preg_replace('/\?/',$v,$string,1);
            else $string=str_replace(":$k",$v,$string);
        }
        return $string;
    }
    
    // function for adding aqs team users as external review into system on 13-05-2016 by Mohit Kumar
    public function addAlerts($table,$content_id,$content_title,$content_description,$type){
        if($table!='' && $content_id!='' && $content_title!='' && $content_description!='' && $type!=''){
            if($this->insert('d_alerts',array('table_name'=>$table,'content_id'=>$content_id,'content_title'=>$content_title,
                'content_description'=>$content_description,'type'=>$type,'status'=>0,'creation_date'=>date('Y-m-d H:i:s'))))
            {
                return true;
            }
        }
    }
    
    // function for getting updated alert count on 01-06-2016 by Mohit Kumar
    public function getAlertCount(){
        $SQL="Select (Select count(id) from d_alerts where status=0 and type='CREATE_EXTERNAL_ASSESSOR') as assessor_count,
                (Select count(id) from d_alerts where status=0 and type='CREATE_REVIEW') as review_count  ";
        return $this->get_row($SQL);
    }
    
    // function for getting content_id according to table_name column on 02-08-2016 by Mohit Kumar
    public function getAlertContentIds($table_name,$type){
        $SQL="Select group_concat(content_id) as content_id From d_alerts Where status='0' and type='".$type."' and table_name='".$table_name."'";
        return $this->get_row($SQL);
    }
    
    // function for getting alert_ids on 02-08-2016 by Mohit Kumar
    public function getAlertRelationIds($role_id,$type){
        $SQL="Select id,alert_ids From h_alert_relation Where flag='1' and type='".$type."' and login_user_role='".$role_id."'";         
        return $this->get_row($SQL);
    }
    
    /*
     * @Purpose: Save history for every action
     * @Method: saveHistoryData
     * @Parameters: Table Name, data values
     * @Return: True or False
     * @Date: 03-03-2016 
     * @By: Mohit Kumar
     */

    public function saveHistoryData($table_id, $table, $uniqueID, $action, $action_id, $action_content, $action_json, $action_flag, $creation_date) {
        if ($this->insert("z_history", array('table_id' => $table_id, 'table_name' => $table, 'action_unique_id' => $uniqueID,
                    'action' => $action, 'action_id' => $action_id, 'action_content' => $action_content, 'action_json' => $action_json,
                    'action_flag' => $action_flag, 'creation_date' => $creation_date)))
            return true;
        else
            return false;
    }

    /*
     * @Purpose: create and get unique id
     * @Method: createUniqueID
     * @Parameters: prefix
     * @Return: unique id
     * @Date: 07-03-2016 
     * @By: Mohit Kumar
     */

    public function createUniqueID($prefix) {
        $b = uniqid($prefix . rand('08', '15'), FALSE).  md5(time());
        return $b;
    }
}