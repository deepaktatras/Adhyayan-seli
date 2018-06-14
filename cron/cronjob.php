<?php

//define('ROOT', $_SERVER['DOCUMENT_ROOT']."/Adhyayan/adhyayanReloaded/");
//require ROOT.'config/config.php';
//require ROOT.'cron/config_offline.php';
//require ROOT.'library/db.class.php';
ini_set('max_execution_time', 0);
echo '<pre>';
//set values for local database server
define("DB_USER", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "adhyayan_170517");
define("DB_HOST", "localhost");
define("SITEURL", "http://localhost:8080/Adhyayan/adhyayanReloaded/");
define("DEVELOPMENT_ENVIRONMENT", true);

// define("DB_USER1", "root");
// define("DB_PASSWORD1", "");
// define("DB_NAME1", "adh_offline");
// define("DB_HOST1", "localhost");
// define("LIVEROOTURL", "/projects/adhyayan_testing/adhyayanReloaded/uploads/");
// define("FTP_USER", "nisha@algoinsighttest.com");
// define("FTP_PASSWORD", "JvOpbeB}]0fV");
// define("FTP_SERVER", "algoinsighttest.com");
// define("DEVELOPMENT_ENVIRONMENT1", true); 

//set values for live server
define("DB_USER1", "techPHP");
define("DB_PASSWORD1", "@PPl!c@Tion");
define("DB_NAME1", "adhyayan_prod");
define("DB_HOST1", "158.69.36.205");
/*define("LIVEROOTURL", "/home/adhyayan/app.adhyayan.asia/uploads/");
define("FTP_USER", "");
define("FTP_PASSWORD", "");
define("FTP_SERVER", "app.adhyayan.asia");*/
define("DEVELOPMENT_ENVIRONMENT1", true);

class db {

    protected $db;
    protected $stm;
//    private static $instance = null;

    public function __construct($host = NULL, $dbName = NULL, $user = NULL, $password = NULL) {
        try {
            $host = $host != '' ? $host : DB_HOST;
            $dbName = $dbName != '' ? $dbName : DB_NAME;
            $user = $user != '' ? $user : DB_USER;
            $password = $password != '' ? $password : DB_PASSWORD;
            $this->db = new PDO('mysql:host=' . $host . ';dbname=' . $dbName, $user, $password);
        } catch (PDOException $e) {
            if (DEVELOPMENT_ENVIRONMENT1)
                echo 'there is no internet connection!';
            else if (DEVELOPMENT_ENVIRONMENT)
                die($e->getMessage());
            else
                die('Error while connecting to database..');
        }
    }

    public function get_results($sql = "", $data = array()) {
        if ($sql != "") {
            $this->stm = $this->db->prepare($sql);
        }

        if ($this->stm->execute($data)) {
            return $this->stm->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $this->log_error("Error while executing query (" . $this->regenerateQuery($sql, $data) . ") \n");
            return null;
        }
    }

    public function insert($tableName, $data = array()) {
        $size = count($data);
        if ($size) {
            $this->db->query('SET FOREIGN_KEY_CHECKS=0');
            $sql = "INSERT INTO `$tableName` (`" . (implode("`, `", array_keys($data))) . "`) VALUES(" . implode(",", array_fill(0, $size, "?")) . ");";
//            echo $this->regenerateQuery($sql, array_values($data));
//            echo '<br/>';
//            die;
            if ($this->db->prepare($sql)->execute(array_values($data))) {
                return true;
            } else {
                $this->log_error("Error while executing query (" . $this->regenerateQuery($sql, array_values($data)) . ") \n");
            }
        }
        return false;
    }

    public function get_row($sql, $data = array()) {
        if ($sql != "") {
            $this->stm = $this->db->prepare($sql);
        }
        if ($this->stm->execute($data)) {
            return $this->stm->fetch(PDO::FETCH_ASSOC);
        } else {
            $this->log_error("Error while executing query (" . $this->regenerateQuery($sql, $data) . ") \n");
            return null;
        }
    }

    public function get_last_insert_id() {
        return $this->db->lastInsertId();
    }

    protected function log_error($msg) {
        if (DEVELOPMENT_ENVIRONMENT1) {
            $msg;
        } else if (DEVELOPMENT_ENVIRONMENT) {
            die($msg);
        } else
            file_put_contents(ROOT . 'tmp' . DS . 'logs' . DS . 'error.txt', PHP_EOL . PHP_EOL . date(DATE_RFC822) . ' ' . $msg, FILE_APPEND);
    }

    private function regenerateQuery($string, $data) {
        $indexed = $data == array_values($data);
        foreach ($data as $k => $v) {
            if (is_string($v))
                $v = "'$v'";
            if ($indexed)
                $string = preg_replace('/\?/', $v, $string, 1);
            else
                $string = str_replace(":$k", $v, $string);
        }
        return $string;
    }
    
    public function delete($tableName, $data = array()) {
        if (count($data)) {
            $this->db->query('SET FOREIGN_KEY_CHECKS=0');
            $sql = "DELETE FROM `$tableName` where `" . implode("`= ? and `", array_keys($data)) . "`= ? ;";
            if ($this->db->prepare($sql)->execute(array_values($data))) {
                return true;
            } else {
                $this->log_error("Error while executing query (" . $this->regenerateQuery($sql, array_values($data)) . ") \n");
            }
        }
        return false;
    }

    public function update($tableName, $data = array(), $where = array()) {
        if (count($data) && count($where)) {
            $this->db->query('SET FOREIGN_KEY_CHECKS=0');
            $sql = "UPDATE $tableName SET `" . implode("`= ? , `", array_keys($data)) . "`= ? WHERE `" . implode("`= ? and `", array_keys($where)) . "`= ? ;";
//            echo $this->regenerateQuery($sql, array_merge(array_values($data), array_values($where)));
//            echo '<br/>';
//            die;
            if ($this->db->prepare($sql)->execute(array_merge(array_values($data), array_values($where)))) {
                return true;
            } else {
                $this->log_error("Error while executing query (" . $this->regenerateQuery($sql, array_merge(array_values($data), array_values($where))) . ") \n");
            }
        }
        return false;
    }
    
    public function array_grouping($arr, $grouping_key, $unique_key = "") {
        $res = array();
        if (count($arr) && isset($arr[0][$grouping_key])) {
            if ($unique_key != "" && isset($arr[0][$unique_key])) {
                foreach ($arr as $a)
                    $res[$a[$grouping_key]][$a[$unique_key]] = $a;
            } else {
                foreach ($arr as $a)
                    $res[$a[$grouping_key]][] = $a;
            }
        }
        return $res;
    }
    
    public function start_transaction() {
        return $this->db->beginTransaction();
    }

    public function commit() {
        return $this->db->commit();
    }

    public function rollback() {
        return $this->db->rollBack();
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

}

$error = '';
//$res = '';
$objDB = new db(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
$objDBLive = new db(DB_HOST1, DB_NAME1, DB_USER1, DB_PASSWORD1);
//echo '<pre>';
//$objDB->insert('z_sync_status',array('sync_status'=>1,'start_time'=>date('Y-m-d H:i:s')));
//die;
ignore_user_abort(true);
ini_set('memory_limit', '-1');
//mssql_min_error_severity(1);
ini_set('default_socket_timeout',   10000);
ini_set('mysql.connect_timeout', 10000);
ini_set('user_ini.cache_ttl', 6000);

ini_set('max_allowed_packet', '512*1024*1024');
set_time_limit(0);

ini_set('max_execution_time', '0'); 

function checkInternet1() {
//    if ($sock = @fsockopen('www.google.com', 80)) {
    if (!$sock = @fsockopen('www.google.com', 80)) {
        $internet_connection = 0;
    } else {
        $internet_connection = 1;
    }	
    return $internet_connection;
}

function checkInternet($objDB){
    $StatusQuery="Select sync_status,end_time,id from z_sync_status order by id desc limit 0,1";
    $statusValue = $objDB->get_row($StatusQuery);
    
    if(checkInternet1()==1){
        if(!empty($statusValue)){
            if($statusValue['end_time']==''){
                $flag = 1; // script is in running stage
            } else {
                $flag = 2; // script is in starting to run stage
            }
        } else {
             $flag = 0; // script is in starting to run stage
        }
    } else {
        $flag=3; // no internet connectivity
    }
	//echo $flag;die;
    return $flag;
}
// fetch last insert id from sync status table
function getSyncStatusId($objDB){
    $StatusQuery="SELECT MAX(id) as id FROM z_sync_status";
    $id = $objDB->get_row($StatusQuery);
    return $id;
}
// find the end time for sycn table
function endTimeByID($objDB){
    $id = getSyncStatusId($objDB);
    $StatusQuery="SELECT end_time FROM z_sync_status Where id='".$id['id']."'";
    $time = $objDB->get_row($StatusQuery);
    return $time;
}

// check history table status table there is any data for sync on live server
function checkHistoryData($objDB){
    $StatusQuery="Select id from z_history where 1 and action_flag='0' GROUP BY `action_unique_id` ORDER BY `id` ASC";
    $id = $objDB->get_row($StatusQuery);
    if(!empty($id)){
        return $id;
    } else {
        return array();
    }
}


if (checkInternet1() == 1 && (checkInternet($objDB)==0 || checkInternet($objDB)==2)) {  
 
    // check history table has data or not    
    $SQL = "Select * from z_history where 1 and action_flag='0' GROUP BY `action_unique_id` ORDER BY `id` ASC ";
    $action = $objDB->get_results($SQL);
    
    $finalSetNetwork=array();
    $finalSetClientNetwork=array();
    $finalUpdateSetNetwork=array();
    $finalSetSchool = array();
    $finalSetEditSchool = array();
    $finalSetUser = array();
    $finalSetEditUser = array();
    $finalSetSchoolAssessment = array();
    $finalSetAQSData = array();
    $finalSet = array();
    $array = array();
    $finalSetAQSDataUpdate = array();
    $finalSetInternalAssessment = array();
    $finalSetInternalAssessmentUpdate = array();
    $finalSetschoolAssessmentUpdate = array();
    $finalSetPublishReportInsert = array();
    $finalSetaAssessorProfile = array();
    if(checkInternet1() == 1){
        
        if (!empty($action)) {
            foreach ($action as $key => $value) {
                
                switch ($value['action']) {
                    case 'addNetwork':
                        $SQL1="Select * from z_history where action='".$value['action']."' and action_flag='0' and "
                            . " action_unique_id='" . $value['action_unique_id'] . "'";
                        $networkSet = $objDB->get_results($SQL1);
                        if(!empty($networkSet)){
                            $networkSetArray = $objDB->array_grouping($networkSet,'action');
                            $finalSetNetwork=$networkSetArray;
                        } else {
                            $finalSetNetwork[$value['action']][]=array();
                        }
                        break;
                        
                    case 'updateNetwork':
                        $SQL1="Select * from z_history where action='".$value['action']."' and action_flag='0' and "
                            . " action_unique_id='" . $value['action_unique_id'] . "'";
                        $networkSet = $objDB->get_results($SQL1);
                        if(!empty($networkSet)){
                            $networkSet1=array();
                            foreach ($networkSet as $i => $a) {
                                $SQL2="Select action_content from z_history where action='addNetwork' and table_id='".$a['table_id']."'";
                                $oldNetworkName=$objDB->get_row($SQL2);
                                if(!empty($oldNetworkName)){
                                    $a['action_content']=$oldNetworkName['action_content'];                                    
                                }
                                $networkSet1[]=$a;
                            }
                            $networkSetArray = $objDB->array_grouping($networkSet1,'action');
                            $finalUpdateSetNetwork=$networkSetArray;
                        } else {
                            $finalUpdateSetNetwork[$value['action']][]=array();
                        }
                        break;
                        
                    case 'addSchool':
                        $SQL1 = "Select * from z_history where action='addSchoolPrincipal' and action_flag='0' and action_id='".$value['action_id']."'"
                                . " and action_unique_id='" . $value['action_unique_id'] . "'";
                        $principalSet = $objDB->get_results($SQL1);
                        $principalRoleSet = array();
                        $principalRoleSet1 = array();
                        if (!empty($principalSet)) {
                            foreach ($principalSet as $i => $princal) {
                                $SQL2 = "Select * from z_history where action='addSchoolPrincipalRole' and action_id='".$princal['table_id']."' "
                                        . " and action_unique_id='".$value['action_unique_id']."' and action_flag='0'";
                                $principalRoleSet = $objDB->get_results($SQL2);
                                if (!empty($principalRoleSet)) {
                                    $principalRoleSet1[] = array_merge($princal, array('Role' => $principalRoleSet));
                                }
                            }
                        }
                        $SQL3 = "Select * from z_history where action='addSchoolNetwork' and action_id='".$value['action_id']."' and "
                                . " action_unique_id='" . $value['action_unique_id']."' and action_flag='0'";
                        $networkData = $objDB->get_results($SQL3);
                        if (!empty($networkData)) {
                            $finalSetSchool[$value['action']][] = array_merge($value,array('User'=>$principalRoleSet1),array('Network'=>$networkData));
                        } else {
                            $finalSetSchool[$value['action']][] = array_merge($value,array('User'=>$principalRoleSet1));
                        }
                        break;

                    case 'editSchool':
                        $SQL1 = "Select * from z_history where action='editSchoolPrincipal' and action_flag='0' and action_id='".$value['action_id']."'"
                                . "  and action_unique_id='" . $value['action_unique_id'] . "'";
                        $principalSet = $objDB->get_results($SQL1);
                        $SQL2 = "Select * from z_history where (action='addSchoolNetwork' Or action='editSchoolNetwork' Or "
                                . "action='removeSchoolNetwork') and action_flag='0'"
                                . "and action_id='" . $value['action_id'] . "' and action_unique_id='" . $value['action_unique_id'] . "'";
                        $networkData = $objDB->get_results($SQL2);
                        if (!empty($networkData)) {
                            if ($networkData[0]['action'] == '') {

                            }
                        }
                        if (!empty($networkData)) {
                            $finalSetEditSchool[$value['action']][] = array_merge($value,array('User'=>$principalSet),array('Network'=>$networkData));
                        } else {
                            $finalSetEditSchool[$value['action']][] = array_merge($value,array('User'=>$principalSet));
                        }
                        break;
                        
                    /*  
                    case 'addSchoolToNetwork':
                        $SQL1="Select * from z_history where action='".$value['action']."' and action_flag='0' and "
                            . " action_unique_id='" . $value['action_unique_id'] . "'";
                        $networkSet = $objDB->get_results($SQL1);
                        if(!empty($networkSet)){
                            $networkSetArray = $objDB->array_grouping($networkSet,'action');
                            $finalSetClientNetwork=$networkSetArray;
                        } else {
                            $finalSetClientNetwork[$value['action']][]=array();
                        }
                        break;
                    */
                        
                    case 'addUser':
                        $SQL1 = "Select * from z_history where action Like 'addUser%' and action_id='".$value['action_id']."' and action_flag='0' and "
                                . "action_unique_id='" . $value['action_unique_id'] . "' and action != 'addUser' ";
                        $userRole = $objDB->get_results($SQL1);
                        $user = $objDB->array_grouping($userRole,'action');
                        $finalSetUser[$value['action']][] = array_merge($value, $user);
                        break;

                    case 'editUser':
                        $SQL1 = "Select * from z_history where action!='editUser' and action_id='".$value['action_id']."' and action_flag='0' and "
                                . "action_unique_id='".$value['action_unique_id']."'";
                        $userRole = $objDB->get_results($SQL1);
                        $addUserRole = array();
                        $deleteUserRole = array();
                        foreach ($userRole as $i => $a) {
                            if ($a['action'] == 'editUserRole') {
                                $addUserRole[] = $a;
                            }
                            if ($a['action'] == 'removeUserRole') {
                                $deleteUserRole[] = $a;
                            }
                        }
                        if (!empty($addUserRole)) {
                            $addUserRole = array('EditRole' => $addUserRole);
                        }
                        if (!empty($deleteUserRole)) {
                            $deleteUserRole = array('RemoveRole' => $deleteUserRole);
                        }

                        $finalSetEditUser[$value['action']][] = array_merge($value, $addUserRole, $deleteUserRole);

                        break;
                        
                    case 'updateAssessorUserAdd':
                        $SQL1 = "Select * from z_history where action_flag='0' and action_unique_id='" . $value['action_unique_id'] . "' ";
                        $assessorProfile = $objDB->get_results($SQL1);
                        if(!empty($assessorProfile)){
                            $assessorProfile = $objDB->array_grouping($assessorProfile,'action');
                            $finalSetaAssessorProfile[$value['action']][] = $assessorProfile;
                        } else {
                            $finalSetaAssessorProfile[$value['action']][] = array();
                        }
                        break;

                    case 'createSchoolAssessment':
                        $SQL1 = "Select * from z_history where action_unique_id='" . $value['action_unique_id'] . "' and action_flag='0' and "
                            . "action='createSchoolAssessmentInternal' and action_content='" . $value['table_id'] . "'";
                        $internal = $objDB->get_results($SQL1);
                        if (!empty($internal)) {
                            $internal1 = array('Internal' => $internal);
                        } else {
                            $internal1 = array();
                        }
                        
                        $SQL2 = "Select * from z_history where action_unique_id='" . $value['action_unique_id'] . "' and action_flag='0' and "
                                . "action='createSchoolAssessmentExternal' and action_content='" . $value['table_id'] . "'";
                        $external = $objDB->get_results($SQL2);
                        if (!empty($external)) {
                            $external1 = array('External' => $external);
                        } else {
                            $external1 = array();
                        }
                        
                        $SQL3 = "Select * from z_history where action_unique_id='" . $value['action_unique_id'] . "' and action_flag='0' and "
                                . "action='createSchoolAssessmentExternalTeam' and action_content='" . $value['table_id'] . "'";
                        $externalTeam = $objDB->get_results($SQL3);
                        if (!empty($externalTeam)) {
                            $externalTeam1 = array('ExternalTeam' => $externalTeam);
                        } else {
                            $externalTeam1 = array();
                        }
                        
                        $SQL4 = "Select t1.email From d_user t1 Left Join h_user_user_role t2 On (t1.user_id=t2.user_id) Where "
                                . "t1.client_id='" . $value['action_id'] . "' and t2.role_id='6'";
                        $clientEmail = $objDB->get_results($SQL4);
                        
                        $SQL5 = "Select * from z_history where action_unique_id='" . $value['action_unique_id'] . "' and action_flag='0' and "
                            . "action='createSchoolAssessmentAlert' and action_content='" . $value['table_id'] . "'";
                        $alert = $objDB->get_results($SQL5);
                        if (!empty($alert)) {
                            $alert1 = array('Alert' => $alert);
                        } else {
                            $alert1 = array();
                        }
                        
                        $finalSetSchoolAssessment[$value['action']][] = array_merge($clientEmail[0], $value, $internal1, $external1, $externalTeam1,$alert1);
                        
                        break;
                        
                    case 'updateSchoolAssessment':
                        
                        $SQL1="Select * from z_history where action_unique_id='" . $value['action_unique_id'] . "' and action_flag='0'";
                        $updateSchoolAssessment = $objDB->get_results($SQL1);
                        if(!empty($updateSchoolAssessment)){
                            $updateSchoolAssessment = $objDB->array_grouping($updateSchoolAssessment,'action');
                            $finalSetschoolAssessmentUpdate[$value['action']][] = $updateSchoolAssessment;
                        } else {
                            $finalSetschoolAssessmentUpdate[$value['action']][] = array();
                        }
                        
                        break;

                    case 'assessmentAQSDataInsert':
                        $SQL1 = "Select * from z_history where action_unique_id='" . $value['action_unique_id'] . "' and action_flag='0' "
                                . " and action!='" . $value['action'] . "'";
                        $AQSData = $objDB->get_results($SQL1);
                        if (!empty($AQSData)) {
                            $array = assessmentArray($AQSData, 'insert');
                            $finalSetAQSData[$value['action']][] = array_merge($value,$array);
                        } else {
                            $finalSetAQSData[$value['action']][] = $value;
                        }

                        break;
                        
                    case 'assessmentAQSDataUpdate':

                        $SQL1 = "Select * from z_history where action_unique_id='" . $value['action_unique_id'] . "' and action_flag='0' "
                                . " and action!='" . $value['action'] . "'";
                        $AQSData = $objDB->get_results($SQL1);
                        if (!empty($AQSData)) {
                            $array = assessmentArray($AQSData, 'update');
                            $finalSetAQSDataUpdate[$value['action']][] = array_merge($value,$array);
                        } else {
                            $finalSetAQSDataUpdate[$value['action']][] = $value;
                        }

                        break;
                        
                    case 'internalAssessmentJudgementStatementInsert':
                        $SQL1="Select * From z_history Where action_unique_id='".$value['action_unique_id']."' and action_flag='0'";
                        $internalAssessment = $objDB->get_results($SQL1);
                        if(!empty($internalAssessment)){
                            $internalAssessmentArray = $objDB->array_grouping($internalAssessment,'action');
                            if(!empty($internalAssessmentArray['internalAssessmentAssessorKeyNoteUpdate'])){
                                $internalAssessmentAssessorKeyNoteUpdate = array();
                                $a=$internalAssessmentArray['internalAssessmentAssessorKeyNoteUpdate'];
                                $SQL2="Select assessment_id from assessor_key_notes where id='".$a[0]['table_id']."'";
                                $assesmentId=$objDB->get_row($SQL2);
                                if(!empty($assesmentId)){
                                    $keyNotesArray=array();
                                    $SQL3="Select * from assessor_key_notes where assessment_id='".$assesmentId['assessment_id']."'";
                                    $assessor_key_notes = $objDB->get_results($SQL3);
                                    foreach ($assessor_key_notes as $j => $b) {
                                        $keyNotesArray[]=array(
                                            'id'=>'',
                                            'table_id'=>$b['id'],
                                            'table_name' => $a[0]['table_name'],
                                            'action_unique_id' => $a[0]['action_unique_id'],
                                            'action'=>$a[0]['action'],
                                            'action_id'=>$b['id'],
                                            'action_content'=>$b['text_data'],
                                            'action_json'=>  json_encode(array(
                                                'id'=>$b['id'],
                                                'text_data'=>$b['text_data'],
                                                'type'=>$b['type'],
                                                'kpa_instance_id'=>$b['kpa_instance_id']
                                            )),
                                            'action_flag'=>$a[0]['action_flag'],
                                            'creation_date'=>$a[0]['creation_date'],
                                            'modification_date'=>$a[0]['modification_date']
                                        );
                                    }
                                    $internalAssessmentArray['internalAssessmentAssessorKeyNoteUpdate']=$keyNotesArray;
                                }                                
                            }
                            $finalSetInternalAssessment['internalAssessment'][] = $internalAssessmentArray;
                        } else {
                            $finalSetInternalAssessment['internalAssessment'][] = array();
                        }
                        break;
                        
                    case ($value['action']=='internalAssessmentJudgementStatementUpdate' ||
                            $value['action']=='internalAssessmentCoreQuestionUpdate' ||
                            $value['action']=='internalAssessmentKeyQuestionUpdate' ||
                            $value['action']=='internalAssessmentKpaUpdate' || 
                            $value['action']=='internalAssessmentDAssessmentStatusUpdate' ||
                            $value['action']=='internalAssessmentJudgementStatementUpdateInsert' ||
                            $value['action']=='internalAssessmentAssessorKeyNoteUpdate' ||
                            $value['action']=='internalAssessmentPercentageAndStatusUpdate' || 
                            $value['action']=='internalAssessmentAssessorKeyNoteDelete') :
                        $SQL1="Select * from z_history where action_unique_id='".$value['action_unique_id']."' and action_flag='0'";
                        $internalAssessmentUpdate = $objDB->get_results($SQL1);
                        
                        if(!empty($internalAssessmentUpdate)){
                            $internalAssessmentArray = $objDB->array_grouping($internalAssessmentUpdate,'action');
                            if(!empty($internalAssessmentArray['internalAssessmentAssessorKeyNoteUpdate'])){
                                $assessorData=$internalAssessmentArray['internalAssessmentAssessorKeyNoteUpdate'];
                            } else if(!empty($internalAssessmentArray['internalAssessmentAssessorKeyNoteDelete'])) {
                                $assessorData=$internalAssessmentArray['internalAssessmentAssessorKeyNoteDelete'];
                            }
                            if(!empty($assessorData)){
                                
                                $internalAssessmentAssessorKeyNoteUpdate = array();
                                $a=$assessorData;
                                $assesmentId=  json_decode($a[0]['action_json'],true);
//                                print_r($assessorData);
//                                 print_r($assesmentId);
//                            die;
                                if(!empty($assesmentId)){
                                    $keyNotesArray=array();
                                    $SQL3="Select * from assessor_key_notes where assessment_id='".$assesmentId['assessment_id']."'";
                                    $assessor_key_notes = $objDB->get_results($SQL3);
                                    
                                    foreach ($assessor_key_notes as $j => $b) {
                                        $keyNotesArray[]=array(
                                            'id'=>'',
                                            'table_id'=>$b['id'],
                                            'table_name' => $a[0]['table_name'],
                                            'action_unique_id' => $a[0]['action_unique_id'],
                                            'action'=>'internalAssessmentAssessorKeyNoteUpdate',
                                            'action_id'=>$b['id'],
                                            'action_content'=>$b['text_data'],
                                            'action_json'=>  json_encode(array(
                                                'id'=>$b['id'],
                                                'text_data'=>$b['text_data'],
                                                'type'=>$b['type'],
                                                'kpa_instance_id'=>$b['kpa_instance_id']
                                            )),
                                            'action_flag'=>$a[0]['action_flag'],
                                            'creation_date'=>$a[0]['creation_date'],
                                            'modification_date'=>$a[0]['modification_date']
                                        );
                                    }
                                    $internalAssessmentArray['internalAssessmentAssessorKeyNoteUpdate']=$keyNotesArray;
                                }                                
                            }
                            if(!empty($internalAssessmentArray['internalAssessmentScoreFileInsert'])){
                                $fileInsert = $internalAssessmentArray['internalAssessmentScoreFileInsert'];
                                unset($internalAssessmentArray['internalAssessmentScoreFileInsert']);
                                $internalAssessmentArray = array_merge($internalAssessmentArray,
                                    array('internalAssessmentScoreFileInsert'=>$fileInsert));
                            }
                            if(!empty($internalAssessmentArray['internalAssessmentPercentageAndStatusUpdate'])){
                                $percentStatus = $internalAssessmentArray['internalAssessmentPercentageAndStatusUpdate'];
                                unset($internalAssessmentArray['internalAssessmentPercentageAndStatusUpdate']);
                                $internalAssessmentArray = array_merge($internalAssessmentArray,
                                    array('internalAssessmentPercentageAndStatusUpdate'=>$percentStatus));
                            }
                            
                            if(!empty($internalAssessmentArray['internalAssessmentAssessorKeyNoteDelete'])){
                                unset($internalAssessmentArray['internalAssessmentAssessorKeyNoteDelete']);
                            }
                            $finalSetInternalAssessmentUpdate['internalAssessmentUpdate'][] = $internalAssessmentArray;
                        } else {
                            $finalSetInternalAssessmentUpdate['internalAssessmentUpdate'][] = array();
                        }
                        
                        break;
                        
                    case 'publishReportInsert':
                        $SQL1="Select * From z_history Where action_unique_id='".$value['action_unique_id']."' and action_flag='0'";
                        $publishReport = $objDB->get_results($SQL1);
                        if(!empty($publishReport)){
                            $publishReportArray = $objDB->array_grouping($publishReport,'action');
                            $finalSetPublishReportInsert['publishReportInsert'][] = $publishReportArray;
                        } else {
                            $finalSetPublishReportInsert['publishReportInsert'][] = array();
                        }
                        
                        break;

    //                default:
    //                    break;
                }
                
                $finalSet = array_merge($finalSetNetwork,$finalUpdateSetNetwork,$finalSetSchool, $finalSetEditSchool,$finalSetClientNetwork,
                        $finalSetUser, $finalSetEditUser,$finalSetaAssessorProfile, $finalSetSchoolAssessment,$finalSetschoolAssessmentUpdate, 
                        $finalSetAQSData,
                        $finalSetAQSDataUpdate,$finalSetInternalAssessment, $finalSetInternalAssessmentUpdate, $finalSetPublishReportInsert);
                
                if(!empty($finalSet) && (checkInternet($objDB)==0 || checkInternet($objDB)==2) && checkInternet1() == 1){
                    $objDB->insert('z_sync_status',array('sync_status'=>1,'start_time'=>date('Y-m-d H:i:s')));
                }
            }
        } else {
            if(checkInternet($objDB)==1){
                $id=getSyncStatusId($objDB);
                $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
            }
            echo 'there is no data!';
            return false;
        }
    } else {
        if(checkInternet($objDB)==1){
            $id=getSyncStatusId($objDB);
            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
        }
        echo 'there is no internet connectivity!';
        return false;
    }
//   print_r($finalSet);
//   die;
    if (!empty($finalSet) && checkInternet1() == 1 && checkInternet($objDB)<3) {
        foreach ($finalSet as $key => $value) {
            if (checkInternet1() == 1) {
                
                switch ($key) {
                    case 'addNetwork':
                        $errorArrayAddNetworkMsg = array();
                        $successArrayAddNetworkMsg = array();
                        $nidArray=array();
                        
                        foreach ($value as $i => $a) {
                            $objDBLive->start_transaction();
                            $objDB->start_transaction();
                            $SQL1="Select network_id from d_network where network_name='".$a['action_content']."'";
                            $checkNetworkName=$objDBLive->get_row($SQL1);
                            
                            if(empty($checkNetworkName) && checkInternet($objDB)==1){
                                if($objDBLive->insert('d_network', array('network_name'=>$a['action_content'])) && checkInternet($objDB)==1){
                                    if(checkInternet($objDB)==1){
                                        $nid = $objDBLive->get_last_insert_id();
                                        $objDBLive->saveHistoryData($nid,$a['table_name'],$a['action_unique_id'],'addNetwork',$nid,
                                                $a['action_content'],$a['action_json'], 1, date('Y-m-d H:i:s'));

                                        // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                        $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['id'], 'action' => 'addNetwork',
                                            'action_unique_id' => $a['action_unique_id']));
                                    } else {
                                        $nid=0;
                                        $errorArrayAddNetworkMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                    $nid=0;
                                    $errorArrayAddNetworkMsg[]="There is an error while sync the data on live server or maybe there is no internet "
                                            . "connectivity!";
                                }
                                if($nid>0 && checkInternet($objDB)==1){
                                    $objDBLive->commit();
                                    $objDB->commit();
                                    $successArrayAddSchoolMsg[]="Network '".$a['action_content']."' data executed  while school data is added!";
                                } else {
                                    $objDBLive->rollback();
                                    $objDB->rollback();
                                    $action_unique_id=$a['action_unique_id'];
                                    $errorArrayAddSchoolMsg[]="There is an error for sync the '".$a['action_content']."' for data while school data is"
                                            . " added or maybe there is no internet connectivity or maybe sync flag is off now!";
                                }
                                
                            } else {
                                $errorArrayAddNetworkMsg[]="This network name '".$a['action_content']."' is already exist on live server or maybe "
                                        . "there is no internet connectivity or maybe sync flag is off now!";
                            }
                        }
                        foreach ($successArrayAddSchoolMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayAddNetworkMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;
                        
                    case 'updateNetwork':
                        $errorArrayAddNetworkMsg = array();
                        $successArrayAddNetworkMsg = array();
                        $nidArray=array();
                        
                        foreach ($value as $i => $a) {
                            $objDBLive->start_transaction();
                            $objDB->start_transaction();
                            $SQL1="Select network_id from d_network where network_name='".$a['action_content']."'";
                            $checkNetworkName=$objDBLive->get_row($SQL1);
                            
                            if(!empty($checkNetworkName) && checkInternet($objDB)==1){
                                $actionJson = json_decode($a['action_json'],true);
                                if($objDBLive->update('d_network', array('network_name'=>$actionJson['network_name']),
                                        array('network_id'=>$checkNetworkName['network_id'])) && checkInternet($objDB)==1){
                                    if(checkInternet($objDB)==1){
                                        $nid = $checkNetworkName['network_id'];
                                        $objDBLive->saveHistoryData($nid,$a['table_name'],$a['action_unique_id'],'updateNetwork',$nid,
                                                $actionJson['network_name'],$a['action_json'], 1, date('Y-m-d H:i:s'));

                                        // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                        $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['id'], 'action' => 'updateNetwork',
                                            'action_unique_id' => $a['action_unique_id']));
                                    } else {
                                        $nid=0;
                                        $errorArrayAddNetworkMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                    $nid=0;
                                    $errorArrayAddNetworkMsg[]="There is an error while sync the data on live server or maybe there is no internet "
                                            . "connectivity!";
                                }
                                if($nid>0 && checkInternet($objDB)==1){
                                    $objDBLive->commit();
                                    $objDB->commit();
                                    $successArrayAddSchoolMsg[]="Network  data executed  while school data is updating!";
                                } else {
                                    $objDBLive->rollback();
                                    $objDB->rollback();
                                    $action_unique_id=$a['action_unique_id'];
                                    $errorArrayAddSchoolMsg[]="There is an error for sync the network data while school data is"
                                            . " added or maybe there is no internet connectivity or maybe sync flag is off now!";
                                }
                                
                            } else {
                                $errorArrayAddNetworkMsg[]="There is no network data on live server or maybe there is no internet connectivity or maybe sync flag is off now!";
                            }
                        }
                        foreach ($successArrayAddSchoolMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayAddNetworkMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;
                        
                    case 'addSchool':
                        
                        $errorArrayAddSchoolMsg = array();
                        $successArrayAddSchoolMsg = array();
                        foreach ($value as $i => $a) {
                            $SQL1 = "Select t1.user_id,t1.user_name,t1.password,t1.name,t1.email as user_email,t1.client_id,t1.aqs_status_id,"
                                . "t1.has_view_video,t2.*,t3.role_id from d_user t1 inner join d_client t2 On (t1.client_id=t2.client_id) inner join "
                                . "h_user_user_role t3 on (t1.user_id=t3.user_id) where t1.email='".$a['action_content']."' ";
                            $userData = json_decode($a['User'][0]['action_json'], true);
                            $schoolActionJson = json_decode($a['action_json'], true);
                            $data = array($a['action_content']);
                            $checkData = $objDBLive->get_row($SQL1);
//                                print_r($schoolActionJson);
//                                die;
                            if (!empty($checkData)) {
                                $objDB->update('z_history', array('action_flag' => 1), array('action_unique_id' => $a['action_unique_id']));
                                $action_unique_id=$a['action_unique_id'];
                                $errorArrayAddSchoolMsg[]="School '".$schoolActionJson['client_name']."' for '".$a['action_content']
                                        ."' already exists on live server while school data is added!";
    //                                $error = 1;
    //                                break;
                            } else {
                                $objDBLive->start_transaction();
                                $objDB->start_transaction();
                                // client data insert on 07-03-2016
                                if(checkInternet($objDB)==1){
                                    if ($objDBLive->insert("d_client", array('client_name' => $schoolActionJson['client_name'],
                                        'street' => $schoolActionJson['street'], 'city_id' => $schoolActionJson['city_id'],
                                        'state_id' => $schoolActionJson['state_id'],
                                        "province"=>$schoolActionJson['province'],
                                        'create_date' => $a['creation_date'],
                                        "principal_phone_no" => $schoolActionJson['principal_phone_no'],'remarks' => $schoolActionJson['remarks'],
                                        'is_web' => 0,'country_id' => $schoolActionJson['country_id'],
                                        'addressLine2' => $schoolActionJson['addressLine2'])) && checkInternet($objDB)==1)
                                    {
                                        if(checkInternet($objDB)==1){
                                            // get last insert id from d_client table on 07-03-2016
                                            $cid = $objDBLive->get_last_insert_id();
                                            // insert client logs into history table on live server on 07-03-2016 

                                            $objDBLive->saveHistoryData($cid,$a['table_name'],$a['action_unique_id'],'addSchool',$cid,
                                                    $a['action_content'],$a['action_json'], 1, date('Y-m-d H:i:s'));

                                            // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                            $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['id'], 'action' => 'addSchool',
                                                'action_unique_id' => $a['action_unique_id']));
                                        } else {
                                            $cid=0;
                                            $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now when school data is sync with live server!"; 
                                        }                                        
                                    } else {
                                        $cid=0;
                                        $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now Or there is an error for school data insertion "
                                                . " when school data is sync with live server!"; 
                                    }
                                } else {
                                    $cid=0;
                                    $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now when school data is sync with live server!";                                    
                                }

                                if(checkInternet($objDB)==1){
                                    if ($objDBLive->insert('d_user', array('password' => md5(trim($userData['password'])), 'name' => $userData['name'],
                                            'client_id' => $cid, 'aqs_status_id' => 0, 'email' => $userData['email']))
                                         && checkInternet($objDB)==1) {
                                        if(checkInternet($objDB)==1){
                                            // get the last insert id from d_user table on live server on 07-03-2016
                                            $uid = $objDBLive->get_last_insert_id();
                                            // insert user log into history table on live server on 07-03-2016

                                            $objDBLive->saveHistoryData($uid,$a['User'][0]['table_name'],$a['action_unique_id'],'addSchoolPrincipal',
                                                    $cid,$a['User'][0]['action_content'],$a['User'][0]['action_json'],1,date('Y-m-d H:i:s'));

                                            // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                            $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['User'][0]['id'], 
                                                'action' => 'addSchoolPrincipal','action_unique_id' => $a['action_unique_id']));
                                        } else {
                                            $uid=0;
                                            $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now when school data is sync with live server!"; 
                                        }                                        
                                    } else {
                                        $uid=0;
                                        $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now Or there is an error for principal data insertion "
                                                . " when school data is sync with live server!"; 
                                    }
                                } else {
                                    $uid=0;
                                    $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now when school data is sync with live server!"; 
                                }
                                // user principal data insert into d_user data on live server on 07-03-2016

                                if(checkInternet($objDB)==1){
                                    // user principal role data insert into h_user_user_role data on live server on 07-03-2016
                                    if ($objDBLive->insert('h_user_user_role', array("role_id" => $a['User'][0]['Role'][0]['action_content'],
                                                "user_id" => $uid)) && checkInternet($objDB)==1) {
                                        if(checkInternet($objDB)==1){
                                            // get the last insert id from h_user_user_role table on live server on 07-03-2016
                                            $rid = $objDBLive->get_last_insert_id();
                                            // insert user log into history table on live server on 07-03-2016

                                            $objDBLive->saveHistoryData($rid, $a['User'][0]['Role'][0]['table_name'], $a['action_unique_id'], 
                                                    'addSchoolPrincipalRole', $uid, $a['User'][0]['Role'][0]['action_content'], 
                                                    $a['User'][0]['Role'][0]['action_json'], 1, date('Y-m-d H:i:s'));

                                            // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                            $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['User'][0]['Role'][0]['id'],
                                                'action' => 'addSchoolPrincipalRole', 'action_unique_id' => $a['action_unique_id']));
                                        } else {
                                            $rid=0;
                                            $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now when school data is sync with live server!";
                                        }                                        
                                    } else {
                                        $rid=0;
                                        $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now Or there is an error for principal role "
                                            . "data insertion when school data is sync with live server!"; 
                                    }
                                } else {
                                    $rid=0;
                                    $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now when school data is sync with live server!"; 
                                }
                                if(checkInternet($objDB)==1){
                                    // add network data regarding to school on live server on 07-03-2016 by Mohit Kumar
                                    if (!empty($a['Network']) && checkInternet($objDB)==1) {
                                        $SQL4 = "Select t1.network_name from d_network t1 where t1.network_id='".$a['Network'][0]['action_content']."'";
                                        $networkData = $objDB->get_results($SQL4);
                                        if (!empty($networkData) && checkInternet($objDB)==1) {
                                            $SQL5 = "Select network_id from d_network where network_name='" . $networkData[0]['network_name'] . "'";
                                            $checkNetworkData = $objDBLive->get_results($SQL5);
                                            if (!empty($checkNetworkData)) {
                                                $network_id = $checkNetworkData[0]['network_id'];
                                            } else {
                                                if ($objDBLive->insert("d_network", array("network_name" => $networkData[0]['network_name']))) {
                                                    $network_id = $objDBLive->get_last_insert_id();
                                                }
                                            }
                                            if($network_id!=''  && checkInternet($objDB)==1){
                                                if ($objDBLive->insert("h_client_network", array("client_id" => $cid, "network_id" => $network_id))
                                                        && checkInternet($objDB)==1) {
                                                    $nid = 1;
                                                    // insert user log into history table on live server on 07-03-2016

                                                    $objDBLive->saveHistoryData($cid, $a['Network'][0]['table_name'], $a['action_unique_id'], 
                                                            'addSchoolNetwork',$cid, $network_id, $a['Network'][0]['action_json'], 1,
                                                            date('Y-m-d H:i:s'));

                                                    // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                    $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['Network'][0]['id'],
                                                        'action' => 'addSchoolNetwork', 'action_unique_id' => $a['action_unique_id']));
                                                } else {
                                                    $nid = 0;
                                                    $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now Or there is an error for network "
                                                        . "data insertion when school data is sync with live server!";
                                                }
                                            } else {
                                                $nid = 0;
                                                $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now Or there is an error for network "
                                                    . "data insertion when school data is sync with live server!"; 
                                            }

                                        } else {
                                            $nid = 0;
                                            $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now Or there is an error for network "
                                                . "data insertion when school data is sync with live server!"; 
                                        }
                                    } else {
                                        $nid = 1;
                                    }
                                } else {
                                    $nid=0;
                                    $errorArrayAddSchoolMsg[]="There is no internet connectivity or maybe sync flag is off now when school data is sync with live server!"; 
                                }

                                if ($cid > 0 && $uid > 0 && $rid > 0 && $nid > 0 && checkInternet($objDB)==1) {
    //                                    $res = 1;
                                    $objDBLive->commit();
                                    $objDB->commit();
                                    $successArrayAddSchoolMsg[]="School '".$schoolActionJson['client_name']."' for '".$a['action_content']."' data"
                                            . "executed  while school data is added!";
                                } else {
                                    $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                    $objDBLive->rollback();
                                    $objDB->rollback();
                                    $action_unique_id=$a['action_unique_id'];
                                    $errorArrayAddSchoolMsg[]="There is an error for sync the '".$schoolActionJson['client_name']."' for '"
                                        .$a['action_content']."' data while school data is added!";
                                }
                            }
                        }
                        foreach ($successArrayAddSchoolMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayAddSchoolMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;

                    case 'editSchool':
                        
                        $errorArrayEditSchoolMsg = array();
                        $successArrayEditSchoolMsg = array();
                        foreach ($value as $i => $a) {
                            $SQL1 = "Select email from d_user where user_id='" . $a['action_content'] . "' and client_id='" . $a['action_id'] . "'";
                            $userEmail = $objDB->get_results($SQL1);

                            if (!empty($userEmail) && checkInternet($objDB)==1) {
                                $a = array_merge($userEmail[0], $a);
                                $SQL2 = "Select t1.user_id,t1.user_name,t1.password,t1.name,t1.email as user_email,t1.client_id,t1.aqs_status_id,"
                                        . "t1.has_view_video,t2.*,t3.role_id from d_user t1 inner join d_client t2 On (t1.client_id=t2.client_id) "
                                        . "inner join h_user_user_role t3 on (t1.user_id=t3.user_id) where t1.email=? ";
                                $userData = json_decode($a['User'][0]['action_json'], true);
                                $schoolActionJson = json_decode($a['action_json'], true);
                                $data = array($a['email']);
                                $checkData = $objDBLive->get_row($SQL2, $data);

                                if (empty($checkData)) {
                                    $errorArrayEditSchoolMsg[]="There is no data for the School'".$schoolActionJson['client_name']."' for '"
                                            . $a['email']."' while school data is updated!";
                                } else {
                                    $objDBLive->start_transaction();
                                    $objDB->start_transaction();
                                    // edit client data on 09-03-2016
                                    if(checkInternet($objDB)==1){
                                        $editSchool = $objDBLive->update('d_client',
                                            array('client_name' => $schoolActionJson['client_name'],
                                                    'street' => $schoolActionJson['street'], 'city_id' => $schoolActionJson['city_id'],
                                                    'state_id' => $schoolActionJson['state_id'],
                                                    "province"=>$schoolActionJson['province'],
                                                    'create_date' => $a['creation_date'],
                                                    "principal_phone_no" => $schoolActionJson['principal_phone_no'],
                                                    'remarks' => $schoolActionJson['remarks'],'country_id' => $schoolActionJson['country_id'],
                                                    'addressLine2' => $schoolActionJson['addressLine2']),
                                            array('client_id' => $checkData['client_id']));

                                        if ($editSchool == true && checkInternet($objDB)==1) {
                                            // insert client logs into history table on live server on 08-03-2016 

                                            $objDBLive->saveHistoryData($checkData['client_id'],$a['table_name'],$a['action_unique_id'],'editSchool',
                                                    $checkData['client_id'], $checkData['client_id'], $a['action_json'], 1, date('Y-m-d H:i:s'));

                                            // update the sync flag to 1 when local data are sync with live server on 08-03-2016
                                            $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['id'], 'action' => 'editSchool',
                                                'action_unique_id' => $a['action_unique_id']));
                                            // user principal data edit into d_user data on live server on 08-03-2016
                                            $editSchoolPrincipal = $objDBLive->update('d_user', array('name' => $userData['name']),
                                                    array('user_id' => $checkData['user_id']));

                                            if ($editSchoolPrincipal == true && checkInternet($objDB)==1) {
                                                // insert user log into history table on live server on 08-03-2016

                                                $objDBLive->saveHistoryData($checkData['client_id'], $a['User'][0]['table_name'], $a['action_unique_id'],
                                                        'editSchoolPrincipal', $checkData['client_id'], $a['User'][0]['action_content'],
                                                        $a['User'][0]['action_json'], 1, date('Y-m-d H:i:s'));

                                                // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['User'][0]['id'],
                                                    'action' => 'editSchoolPrincipal','action_unique_id' => $a['action_unique_id']));
                                            }
                                            if (!empty($a['Network']) && checkInternet($objDB)==1) {
                                                $SQL4 = "Select t1.network_name from d_network t1 where "
                                                        . "t1.network_id='".$a['Network'][0]['action_content']."'";
                                                $networkData = $objDB->get_results($SQL4);

                                                if (!empty($networkData) && checkInternet($objDB)==1) {
                                                    $SQL5 = "Select network_id from d_network where network_name='".$networkData[0]['network_name']."'";
                                                    $checkNetworkData = $objDBLive->get_results($SQL5);
                                                    if (!empty($checkNetworkData)) {
                                                        $network_id = $checkNetworkData[0]['network_id'];
                                                    } else {
                                                        if ($objDBLive->insert("d_network", array("network_name" => $networkData[0]['network_name']))) {
                                                            $network_id = $objDBLive->get_last_insert_id();
                                                        }
                                                    }

                                                    if ($a['Network'][0]['action'] == 'addSchoolNetwork') {
                                                        if ($objDBLive->insert("h_client_network", array("client_id" => $checkData['client_id'],
                                                                    "network_id" => $network_id)) && checkInternet($objDB)==1) {
                                                            $nid = 1;
                                                            // insert user log into history table on live server on 08-03-2016

                                                            $objDBLive->saveHistoryData($checkData['client_id'], $a['Network'][0]['table_name'], 
                                                                    $a['action_unique_id'], 'addSchoolNetwork', $checkData['client_id'], $network_id, 
                                                                    $a['Network'][0]['action_json'], 1, date('Y-m-d H:i:s'));

                                                            // update the sync flag to 1 when local data are sync with live server on 08-03-2016
                                                            $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['Network'][0]['id'],
                                                                'action' => 'addSchoolNetwork', 'action_unique_id' => $a['action_unique_id']));
                                                        } else {
                                                            $nid = 0;
                                                        }
                                                    } else if ($a['Network'][0]['action'] == 'editSchoolNetwork') {
                                                        if ($objDBLive->update("h_client_network", array("network_id" => $network_id),
                                                                array("client_id" => $checkData['client_id'])) && checkInternet($objDB)==1) {
                                                            // insert user log into history table on live server on 08-03-2016

                                                            $objDBLive->saveHistoryData($checkData['client_id'], $a['Network'][0]['table_name'],
                                                                    $a['action_unique_id'], 'editSchoolNetwork', $checkData['client_id'], $network_id,
                                                                    $a['Network'][0]['action_json'], 1, date('Y-m-d H:i:s'));

                                                            // update the sync flag to 1 when local data are sync with live server on 08-03-2016
                                                            $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['Network'][0]['id'],
                                                                'action' => 'editSchoolNetwork', 'action_unique_id' => $a['action_unique_id']));
                                                            $nid = 1;
                                                        } else {
                                                            $nid = 0;
                                                        }
                                                    } else if ($a['Network'][0]['action'] == 'removeSchoolNetwork') {
                                                        $deleteNetwork = $objDBLive->delete("h_client_network", 
                                                                array("client_id" => $checkData['client_id'],"network_id" => $network_id));
                                                        if ($deleteNetwork == true && checkInternet($objDB)==1) {
                                                            // insert user log into history table on live server on 08-03-2016

                                                            $objDBLive->saveHistoryData($checkData['client_id'], $a['Network'][0]['table_name'], 
                                                                    $a['action_unique_id'], 'removeSchoolNetwork', $checkData['client_id'], $network_id, 
                                                                    $a['Network'][0]['action_json'], 1, date('Y-m-d H:i:s'));

                                                            // update the sync flag to 1 when local data are sync with live server on 08-03-2016
                                                            $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['Network'][0]['id'],
                                                                'action' => 'removeSchoolNetwork', 'action_unique_id' => $a['action_unique_id']));
                                                            $nid = 1;
                                                        } else {
                                                            $nid = 0;
                                                        }
                                                    } else {
                                                        $nid = 0;
                                                    }
                                                } else {
                                                    $nid = 0;
                                                }
                                            } else {
                                                $nid = 1;
                                            }
                                        }
                                        if ($editSchool == true && $editSchoolPrincipal == true && $nid > 0 && checkInternet($objDB)==1) {
    //                                        $res = 1;
                                            $objDBLive->commit();
                                            $objDB->commit();
                                            $successArrayEditSchoolMsg[]="School '".$schoolActionJson['client_name']."' data"
                                                . "updated while school data is updated! ";
                                        } else {

                                            $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                            $objDBLive->rollback();
                                            $objDB->rollback();
                                            $action_unique_id=$a['action_unique_id'];
    //                                        $error = 7;
    //                                        break;
                                            $errorArrayEditSchoolMsg[]="There is an error for sync the '".$schoolActionJson['client_name']."' for '"
                                                .$a['email']."' data while school data is updated!";
                                        }
                                    } else {

                                    }
                                }
                            } else {
                                $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
    //                                $error = 6;
    //                                break;
                                $errorArrayEditSchoolMsg[]="There is an error for sync the School data while school data is updated!";
                            }
                        }
                        foreach ($successArrayEditSchoolMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayEditSchoolMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;
                    
                    /*
                        
                    case 'addSchoolToNetwork':
                        $errorArrayAddClientNetworkMsg = array();
                        $successArrayAddClientNetworkMsg = array();
                        $nidArray=array();
                        foreach ($value as $i => $a) {
                            $objDBLive->start_transaction();
                            $objDB->start_transaction();
                            $SQL1="Select t1.email,t2.client_name from d_user t1 Left Join d_client t2 On (t1.client_id=t2.client_id) left Join h_user_user_role t3 "
                                    . "On (t1.user_id=t3.user_id) where t1.client_id='".$a['action_id']."' and t3.role_id='6'";
                            $schoolEmail=$objDB->get_row($SQL1);
                            if(!empty($schoolEmail) && checkInternet1()==1){
                                $SQL2="Select client_id from d_user where email='".$schoolEmail['email']."'";
                                $liveSchoolId=$objDBLive->get_row($SQL2);
                                if(!empty($liveSchoolId) && checkInternet1()==1){
                                    $SQL3="Select network_name from d_network where network_id='".$a['action_content']."'";
                                    $networkName = $objDB->get_row($SQL3);
                                    if(!empty($networkName) && checkInternet1()==1){
                                        $SQL4="Select network_id from d_network where network_name='".$networkName['network_name']."'";
                                        $liveNetworkId=$objDBLive->get_row($SQL4);
                                        if(!empty($liveNetworkId) && checkInternet1()==1){
                                            $network_id=$liveNetworkId['network_id'];
                                        } else {
                                            if($objDBLive->insert('d_network',array('network_name'=>$networkName['network_name'])) && 
                                                    checkInternet1()==1)
                                            {
                                                $network_id=$objDBLive->get_last_insert_id();
                                            } else {
                                                $network_id=0;
                                                $errorArrayAddClientNetworkMsg[]="There is no network data in live server and maybe there is no internet "
                                                    . "connectivity or sync flag is off now!";
                                            }
                                        }
                                        $SQL5="Select network_id from ".$a['table_name']." where client_id='".$liveSchoolId['client_id']."' ";
                                        $checkData = $objDBLive->get_row($SQL5);
                                        
                                        if(empty($checkData) && $network_id>0 && checkInternet1()==1){
                                            if($objDBLive->insert($a['table_name'],array("client_id"=>$liveSchoolId['client_id'],
                                                "network_id"=>$network_id)) && checkInternet1()==1)
                                            {
                                                $objDBLive->saveHistoryData($liveSchoolId['client_id'],$a['table_name'],$a['action_unique_id'],
                                                        $a['action'],$liveSchoolId['client_id'],$network_id,$a['action_json'], 1, date('Y-m-d H:i:s'));

                                                // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['id'], 'action' => $a['action'],
                                                    'action_unique_id' => $a['action_unique_id']));
                                                $objDBLive->commit();
                                                $objDB->commit();
                                                $successArrayAddClientNetworkMsg[]="School is added to the network!";
                                            } else {
                                                $objDBLive->rollback();
                                                $objDB->rollback();
                                                $errorArrayAddClientNetworkMsg[]="There is an error for sync the data while school data is added "
                                                        . "to network or maybe there is no internet connectivity!";
                                            }
                                        } else if($checkData['network_id']==$network_id && checkInternet1()==1){
                                            $errorArrayAddClientNetworkMsg[]="This school '".$schoolEmail['client_name']."' is already to added to "
                                                . "the network or maybe there is no internet connectivity!";
                                        } else if($checkData['network_id']!=$network_id && checkInternet1()==1){
                                            $errorArrayAddClientNetworkMsg[]="Tise school '".$schoolEmail['client_name']."' is already to added to "
                                                . "the other network so you can't add this school to '".$networkName['network_name']."'"
                                                . "or maybe there is no internet connectivity!";
                                        } else {
                                            $errorArrayAddClientNetworkMsg[]="There is no network data in live server and maybe there is no internet "
                                                . "connectivity or sync flag is off now!";
                                        }
                                    } else {
                                        $errorArrayAddClientNetworkMsg[]="There is no network data in live server and maybe there is no internet "
                                                . "connectivity or sync flag is off now!";
                                    }
                                } else {
                                    $errorArrayAddClientNetworkMsg[]="There is no school data in live server and maybe there is no internet connectivity"
                                        . " or sync flag is off now!";
                                }
                            } else {
                                $errorArrayAddClientNetworkMsg[]="There is no school data in local server and maybe there is no internet connectivity"
                                        . " or sync flag is off now!";
                            }
                        }
                        foreach ($successArrayAddClientNetworkMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayAddClientNetworkMsg as $value) {
                            echo $value."<br/>";
                        }
                        break;
                    
                    */
                    
                    case 'addUser':
                        $errorArrayAddUserMsg=array();
                        $successArrayAddUserMsg=array();
                        
                        foreach ($value as $i => $a) {

                            $SQL1 = "Select t1.*,t3.client_name from d_user t1 Left Join d_client t3 On (t1.client_id=t3.client_id) "
                                    . "where t1.email='" . $a['action_content'] . "' ";
                            $userData = $objDB->get_results($SQL1);
//                                print_r($a['addUserAlert']);
//                                die;
                            if (!empty($userData) && checkInternet($objDB)==1) {
                                $SQL2="Select t1.client_id,t1.email,t2.client_name from d_user t1 Left Join d_client t2 On (t1.client_id=t2.client_id) "
                                        ." Left Join h_user_user_role t3 On (t1.user_id=t3.user_id) where t1.client_id='".$userData[0]['client_id']."'"
                                        ." and t3.role_id='6'";
                                $clientData = $objDB->get_results($SQL2);
//                                    print_r($clientData);
//                                die;
                                if (!empty($clientData) && checkInternet($objDB)==1) {

                                    $SQL3 = "Select t1.client_id from d_client t1 Left Join d_user t2 On (t1.client_id=t2.client_id) where "
                                            . "t2.email='".$clientData[0]['email']."' and t1.client_name='".$clientData[0]['client_name']."' ";
                                    $clientId = $objDBLive->get_results($SQL3);

                                    if (!empty($clientId) && checkInternet($objDB)==1) {
                                        $objDBLive->start_transaction();
                                        $objDB->start_transaction();
                                        $userData = json_decode($a['action_json'], true);

                                        if (($objDBLive->insert('d_user', array('email' => $userData['email'], 'name' => $userData['name'],
                                                    'password' => md5(trim($userData['password'])), 'client_id' => $clientId[0]['client_id'])))
                                                 && checkInternet($objDB)==1) 
                                        {

                                            // get last insert id from d_user table on 07-03-2016
                                            $uid = $objDBLive->get_last_insert_id();
                                            // insert client logs into history table on live server on 07-03-2016 

                                            $objDBLive->saveHistoryData($uid, $a['table_name'], $a['action_unique_id'], 'addUser', $uid, 
                                                    $a['action_content'], $a['action_json'], 1, date('Y-m-d H:i:s'));

                                            // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                            $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['id'], 'action' => 'addUser',
                                                'action_unique_id' => $a['action_unique_id']));
                                        } else {
                                            $uid=0;
                                        }
                                        if (!empty($a['addUserRole']) && checkInternet($objDB)==1) {
                                            $idr = array();
                                            foreach ($a['addUserRole'] as $j => $b) {
                                                if ($objDBLive->insert('h_user_user_role', array('user_id' => $uid, 'role_id' => $b['action_content']))
                                                         && checkInternet($objDB)==1)
                                                {
                                                    // get last insert id from h_user_user_role table on 07-03-2016
                                                    $rid = $objDBLive->get_last_insert_id();
                                                    $idr[] = $rid;
                                                    // insert client logs into history table on live server on 07-03-2016 

                                                    $objDBLive->saveHistoryData($rid, $b['table_name'], $a['action_unique_id'], 'addUserRole', $uid,
                                                            $b['action_content'], $b['action_json'], 1, date('Y-m-d H:i:s'));

                                                    // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                    $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 
                                                        'action' => 'addUserRole','action_unique_id' => $a['action_unique_id']));
                                                }
                                            }
                                            if(count($idr)==count($a['addUserRole']) && checkInternet($objDB)==1){
                                                $rid=1;
                                            } else {
                                                $rid=0;
                                            }
                                        } else {
                                            $rid = 0;
                                        }
                                        if(!empty($a['addUserAlert'])){
                                            if (checkInternet($objDB)==1) {
                                                $ida = array();
                                                foreach ($a['addUserAlert'] as $j => $b) {
                                                    $json = json_decode($b['action_json'],true);
                                                    if ($objDBLive->insert('d_alerts', array('table_name' => $json['table_name'],'content_id'=>$uid, 
                                                        'content_title' => $json['content_title'],'content_description'=>$json['content_description'],
                                                        'type'=>$json['type'],'status'=>0,'creation_date'=>date('Y-m-d H:i:s'))) && checkInternet($objDB)==1)
                                                    {
                                                        // get last insert id from h_user_user_role table on 07-03-2016
                                                        $aid = $objDBLive->get_last_insert_id();
                                                        $ida[] = $aid;
                                                        // insert client logs into history table on live server on 07-03-2016 

                                                        $objDBLive->saveHistoryData($aid, $b['table_name'], $a['action_unique_id'], $b['action'], $uid,
                                                                $b['action_content'], $b['action_json'], 1, date('Y-m-d H:i:s'));

                                                        // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                        $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 
                                                            'action' => $b['action'],'action_unique_id' => $a['action_unique_id']));
                                                    }
                                                }
                                                if(count($ida)==count($a['addUserAlert']) && checkInternet($objDB)==1){
                                                    $aid=1;
                                                } else {
                                                    $aid=0;
                                                }

                                            }
                                        } else {
                                            $aid=1;
                                        }
                                        if(!empty($a['addUserTabAssessorAlert'])){
                                            if (checkInternet($objDB)==1) {
                                                $idt = array();
                                                foreach ($a['addUserTabAssessorAlert'] as $j => $b) {
                                                    $json = json_decode($b['action_json'],true);
                                                    if ($objDBLive->insert($b['table_name'], array('tap_program_status' => 1,'user_id'=>$uid)) &&
                                                            checkInternet($objDB)==1)
                                                    {
                                                        // get last insert id from h_user_user_role table on 07-03-2016
                                                        $tid = $objDBLive->get_last_insert_id();
                                                        $idt[] = $tid;
                                                        // insert client logs into history table on live server on 07-03-2016 

                                                        $objDBLive->saveHistoryData($tid, $b['table_name'], $a['action_unique_id'], $b['action'], $uid,
                                                                $b['action_content'], $b['action_json'], 1, date('Y-m-d H:i:s'));

                                                        // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                        $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 
                                                            'action' => $b['action'],'action_unique_id' => $a['action_unique_id']));
                                                    }
                                                }
                                                if(count($idt)==count($a['addUserTabAssessorAlert']) && checkInternet($objDB)==1){
                                                    $tid=1;
                                                } else {
                                                    $tid=0;
                                                }

                                            }
                                        } else {
                                            $tid=1;
                                        }
                                        
                                        if ($uid > 0 && $rid > 0 && checkInternet($objDB)==1 && $aid>0 && $tid>0) {
                                            $res = 2;
                                            $objDBLive->commit();
                                            $objDB->commit();
                                            $successArrayAddUserMsg[]=$userData['name']." for ".$userData['email']." data executed while user data "
                                                    . "is added!";
                                        } else {

                                            $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                            $objDBLive->rollback();
                                            $objDB->rollback();
                                            $action_unique_id=$a['action_unique_id'];
    //                                            $error = 3;
                                            $errorArrayAddSchoolMsg[]="There is an error for sync the '".$userData['name']."' for '"
                                                    .$userData['email']."' data while user data is added!";
    //                                            break;
                                        }
                                    } else {
                                        $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
    //                                        $error = 4;
                                        $errorArrayAddUserMsg[]="There is no school data for this '".$clientData[0]['email']."' for this user '"
                                                . $a['action_content']."' on live server while user data is added!";
    //                                        break;
                                    }
                                } else {
                                    $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
    //                                    $error = 5;
                                    $errorArrayAddUserMsg[]="There is no data for the School for ".$a['action_content']." on local server"
                                            . " while user data is added!";
    //                                    break;
                                }
                            } else {
                                $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
    //                                $error = 5;
    //                                break;
                                $errorArrayAddUserMsg[]="There is no data for the user for '".$a['action_content']."' on local server "
                                        . "while user data is added!";
                            }
                        }
                        foreach ($successArrayAddUserMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayAddUserMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;

                    case 'editUser':
                        $successArrayEditUserMsg=array();
                        $errorArrayEditUserMsg=array();
    //                        print_r($value);
    //                        die;
                        foreach ($value as $i => $a) {
                            $SQL1 = "Select t1.user_id from d_user t1 where t1.email='" . $a['action_content'] . "' ";
                            $userData = $objDBLive->get_results($SQL1);

                            $editUserPostData = json_decode($a['action_json'], true);
                            if (!empty($userData) && !empty($editUserPostData) && checkInternet($objDB)==1) {
                                $data = array("name" => $editUserPostData['name']);
                                if ($editUserPostData['password'] != "")
                                    $data['password'] = md5(trim($editUserPostData['password']));
                                else
                                    $data['password'] = $editUserPostData['password'];
                                $objDBLive->start_transaction();
                                $objDB->start_transaction();
                                $editUser = $objDBLive->update('d_user', $data, array("user_id" => $userData[0]['user_id']));
                                if ($editUser == true && checkInternet($objDB)==1) {
                                    // insert client logs into history table on live server on 07-03-2016 

                                    $objDBLive->saveHistoryData($userData[0]['user_id'], $a['table_name'], $a['action_unique_id'], 'editUser',
                                            $userData[0]['user_id'], $a['action_content'], $a['action_json'], 1, date('Y-m-d H:i:s'));

                                    // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                    $objDB->update('z_history', array('action_flag' => 1), array('id' => $a['id'], 'action' => 'editUser',
                                        'action_unique_id' => $a['action_unique_id']));
                                }
                                if (!empty($a['EditRole']) && checkInternet($objDB)==1) {
                                    $idr = array();
                                    foreach ($a['EditRole'] as $j => $b) {
                                        $editRole = $objDBLive->insert('h_user_user_role', array('user_id' => $userData[0]['user_id'],
                                            'role_id' => $b['action_content']));
                                        if ($editRole == true && checkInternet($objDB)==1) {
                                            // get last insert id from h_user_user_role table on 07-03-2016
                                            $rid = $objDBLive->get_last_insert_id();
                                            $idr[]=$rid;
                                            // insert client logs into history table on live server on 07-03-2016 

                                            $objDBLive->saveHistoryData($rid, $b['table_name'], $a['action_unique_id'], 'editUserRole', 
                                                    $userData[0]['user_id'], $b['action_content'], $b['action_json'], 1, date('Y-m-d H:i:s'));

                                            // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                            $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 'action' => 'editUserRole',
                                                'action_unique_id' => $a['action_unique_id']));
                                        }
                                    }
                                    if(count($idr)==count($a['EditRole']) && checkInternet($objDB)==1){
                                        $editRole = true;
                                    } else {
                                        $editRole = false;
                                    }
                                } else {
                                    $editRole = true;
                                }
                                if (!empty($a['RemoveRole']) && checkInternet($objDB)==1) {
                                    $removeStatus = array();
                                    foreach ($a['RemoveRole'] as $k => $c) {
                                        $SQL2 = "Select user_user_role_id from h_user_user_role where user_id='" . $userData[0]['user_id'] . "' and "
                                                . " role_id='" . $c['action_content'] . "'";
                                        $roleId = $objDBLive->get_results($SQL2);
                                        $deleteRole = $objDBLive->delete('h_user_user_role', array("role_id" => $c['action_content'],
                                            "user_id" => $userData[0]['user_id']));
                                        if ($deleteRole == true && checkInternet($objDB)==1) {
                                            $removeStatus[] = $c['action_content'];
                                            // insert client logs into history table on live server on 07-03-2016 

                                            $objDBLive->saveHistoryData($roleId[0]['user_user_role_id'], $c['table_name'], $a['action_unique_id'],
                                                    'removeUserRole', $userData[0]['user_id'], $c['action_content'], $c['action_json'], 1,
                                                    date('Y-m-d H:i:s'));

                                            // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                            $objDB->update('z_history', array('action_flag' => 1), array('id' => $c['id'], 'action' => 'removeUserRole',
                                                'action_unique_id' => $a['action_unique_id']));
                                        }
                                    }
                                    if(count($removeStatus)==count($a['RemoveRole']) && checkInternet($objDB)==1){
                                        $deleteRole = true;
                                    } else {
                                        $deleteRole = false;
                                    }
                                } else {
                                    $deleteRole = true;
                                }
                                if ($editUser && $editRole && $deleteRole && checkInternet($objDB)==1) {
    //                                    $res = 3;
                                    $objDBLive->commit();
                                    $objDB->commit();
                                    $successArrayEditUserMsg[]="User '".$editUserPostData['name']."' for '".$a['action_content']."' data executed"
                                            . " while user data is updated!";
                                } else {
                                    $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                    $objDBLive->rollback();
                                    $objDB->rollback();
                                    $action_unique_id=$a['action_unique_id'];
    //                                    $error = 3;
    //                                    break;
                                    $errorArrayEditUserMsg[]="There is an error for sync the '".$editUserPostData['name']."' for '"
                                                    .$a['action_content']."' data while user data is updated!";
                                }
                            } else {
                                $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
    //                                $error = 6;
    //                                break;
                                $errorArrayEditUserMsg[]="There is no data for '".$editUserPostData['name']."' for '"
                                                    .$a['action_content']."' data while user data is updated!";
                            }
                        }
                        foreach ($successArrayEditUserMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayEditUserMsg as $value) {
                            echo $value."<br/>";
                        }
                        
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;
                        
                    case 'updateAssessorUserAdd':
                        $errorArrayAssessorMsg=array();
                        $successArrayAssessorMsg=array();
                        
                        foreach ($value as $i => $a) {
                            $objDBLive->start_transaction();
                            $objDB->start_transaction();
                            
                            if(!empty($a['updateAssessorUserAdd'])){
                                $ida = array();
                                foreach ($a['updateAssessorUserAdd'] as $j => $b) {
                                    $json = json_decode($b['action_json'],true);
                                    $SQL1="Select user_id from d_user where email = '".$b['action_content']."'";
                                    $userId = $objDBLive->get_row($SQL1);
                                    
                                    if(!empty($userId) && checkInternet($objDB)==1){
                                        if($json['password']!=''){
                                            $json['password']=md5(trim($json['password']));
                                        } else {
                                            unset($json['password']);
                                        }
                                        unset($json['user_id']);
                                        if($objDBLive->update($b['table_name'],$json,array('user_id'=>$userId['user_id'])) && checkInternet($objDB)==1){
                                            $uid = $userId['user_id'];
                                            $ida[] = $uid;
                                            $objDBLive->saveHistoryData($uid, $b['table_name'], $b['action_unique_id'], $b['action'], $uid,
                                                    $b['action_content'], $b['action_json'], 1, date('Y-m-d H:i:s'));

                                            // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                            $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 
                                                'action' => $b['action'],'action_unique_id' => $b['action_unique_id']));
                                        } else {
                                            $uid=0;
                                            $errorArrayAssessorMsg[]="There is an error while updated the assessor profile or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $SQL2 = "Select t1.email from d_user t1 Left Join d_client t2 ON (t1.client_id=t2.client_id) Left Join 
                                            h_user_user_role t3 ON (t1.user_id=t3.user_id) Where t3.role_id = 6 and 
                                            t1.client_id='".$json['client_id']."' ";
                                        $princal_email = $objDB->get_row($SQL2);
                                        if(!empty($princal_email) && checkInternet($objDB)==1){
                                            $SQL3="Select client_id from d_user where email = '".$princal_email['email']."'";
                                            $client_id = $objDBLive->get_row($SQL3);
                                            if(!empty($client_id) && checkInternet($objDB)==1){
                                                $json['password'] = $json['password']!=''?md5(trim($json['password'])):$json['password'];
                                                $json['client_id'] = $client_id['client_id'];
                                                if($objDBLive->insert($b['table_name'], $json) && checkInternet($objDB)==1){
                                                    $uid = $objDBLive->get_last_insert_id();
                                                    $ida[] = $uid;
                                                     $objDBLive->saveHistoryData($uid, $b['table_name'], $b['action_unique_id'], $b['action'], $uid,
                                                    $b['action_content'], $b['action_json'], 1, date('Y-m-d H:i:s'));

                                                    // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                    $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 
                                                        'action' => $b['action'],'action_unique_id' => $b['action_unique_id']));
                                                } else {
                                                    $uid = 0;
                                                    $errorArrayAssessorMsg[]="There is an error while inserting the assessor profile or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                                }
                                            } else {
                                                $uid = 0;
                                                $errorArrayAssessorMsg[]="There is no school data for this assessor or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                            }
                                        } else {
                                            $uid=0;
                                            $errorArrayAssessorMsg[]="There is no principal email for this assessor's school or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                        }
                                    }                                    
                                }
                                if(count($ida)==count($a['updateAssessorUserAdd']) && checkInternet($objDB)==1){
                                    $uid=1;
                                } else {
                                    $uid=0;
                                }                                
                            } else {
                                $uid = 1;
                            }
                            
                            if(!empty($a['updateAssessorUserProfileUpdate'])){
                                $idp = array();
                                foreach ($a['updateAssessorUserProfileUpdate'] as $j => $b) {
                                    $json = json_decode($b['action_json'],true);
                                    $SQL4="Select email from d_user where user_id = '".$b['action_content']."'";
                                    $userEmail = $objDB->get_row($SQL4);
                                    
                                    if(!empty($userEmail) && checkInternet($objDB)==1){
                                        $SQL5="Select user_id from d_user where email = '".$userEmail['email']."'";
                                        $userId = $objDBLive->get_row($SQL5);
                                        $json['user_id']=$userId['user_id'];
                                        if(!empty($userId) && checkInternet($objDB)==1){
                                            $SQL6="Select id from h_user_profile where user_id='".$userId['user_id']."'";
                                            $checkProfile = $objDBLive->get_row($SQL6);
                                            if(!empty($checkProfile) && checkInternet($objDB)==1){
                                                if($objDBLive->update($b['table_name'],$json,array('user_id'=>$json['user_id'])) && checkInternet($objDB)==1){
                                                    $pid = $userId['user_id'];
                                                    $idp[] = $pid;
                                                    $objDBLive->saveHistoryData($pid, $b['table_name'], $b['action_unique_id'], $b['action'], $pid,
                                                    $pid, $b['action_json'], 1, date('Y-m-d H:i:s'));

                                                    // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                    $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 
                                                        'action' => $b['action'],'action_unique_id' => $b['action_unique_id']));
                                                } else {
                                                    $pid = 0;
                                                    $errorArrayAssessorMsg[]="There is an error while updated the assessor profile or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                                }
                                            } else {
                                                if($objDBLive->insert($b['table_name'],$json) && checkInternet($objDB)==1){
                                                    $pid = $objDBLive->get_last_insert_id();
                                                    $idp[] = $pid;
                                                    $objDBLive->saveHistoryData($pid, $b['table_name'], $b['action_unique_id'], $b['action'], $pid,
                                                    $pid, $b['action_json'], 1, date('Y-m-d H:i:s'));

                                                    // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                    $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 
                                                        'action' => $b['action'],'action_unique_id' => $b['action_unique_id']));
                                                } else {
                                                    $pid=0;
                                                    $errorArrayAssessorMsg[]="There is an error while inserting the assessor profile or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                                }
                                            }
                                        } else {
                                            $pid=0;
                                            $errorArrayAssessorMsg[]="There is no assessor data or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $pid=0;
                                        $errorArrayAssessorMsg[]="There is no assessor profile data or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                    }                              
                                }
                                if(count($idp)==count($a['updateAssessorUserProfileUpdate']) && checkInternet($objDB)==1){
                                    $pid=1;
                                } else {
                                    $pid=0;
                                }                                
                            } else {
                                $pid = 1;
                            }
                            
                            
                            if(!empty($a['updateAssessorLanguageDelete'])){
                                $idld = array();
                                foreach ($a['updateAssessorLanguageDelete'] as $j => $b) {
                                    
                                    $SQL4="Select email from d_user where user_id = '".$b['action_content']."'";
                                    $userEmail = $objDB->get_row($SQL4);
                                    if(!empty($userEmail) && checkInternet($objDB)==1){
                                        $SQL5="Select user_id from d_user where email = '".$userEmail['email']."'";
                                        $userId = $objDBLive->get_row($SQL5);
                                        if(!empty($userId) && checkInternet($objDB)==1){
                                            if($objDBLive->delete($b['table_name'], array('user_id'=>$userId['user_id'])) && checkInternet($objDB)==1){
                                                $ldid = $userId['user_id'];
                                                $idld[] = $ldid;
                                                $objDBLive->saveHistoryData($ldid, $b['table_name'], $b['action_unique_id'], $b['action'], $ldid,
                                                $ldid, $b['action_json'], 1, date('Y-m-d H:i:s'));

                                                // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 
                                                    'action' => $b['action'],'action_unique_id' => $b['action_unique_id']));
                                            } else {
                                                $ldid=0;
                                                $errorArrayAssessorMsg[]="There is an error while deleting the assessor language data or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                            }
                                        } else {
                                            $ldid=0;
                                            $errorArrayAssessorMsg[]="There is no assessor profile data or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $ldid=0;
                                        $errorArrayAssessorMsg[]="There no assessor profile data or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                    }                              
                                }
                                if(count($idld)==count($a['updateAssessorLanguageDelete']) && checkInternet($objDB)==1){
                                    $ldid=1;
                                } else {
                                    $ldid=0;
                                }                                
                            } else {
                                $ldid = 1;
                            }
                            
                            if(!empty($a['updateAssessorUserLanguageAdd'])){
                                $idla = array();
                                foreach ($a['updateAssessorUserLanguageAdd'] as $j => $b) {
                                    $json = json_decode($b['action_json'],true);
                                    $SQL4="Select email from d_user where user_id = '".$b['action_content']."'";
                                    $userEmail = $objDB->get_row($SQL4);
                                    if(!empty($userEmail) && checkInternet($objDB)==1){
                                        $SQL5="Select user_id from d_user where email = '".$userEmail['email']."'";
                                        $userId = $objDBLive->get_row($SQL5);
                                        if(!empty($userId) && checkInternet($objDB)==1){
                                            $json['user_id']=$userId['user_id'];
                                            if(addLanguage($json,$objDBLive)){
                                                $laid = $objDBLive->get_last_insert_id();
                                                $idla[] = $laid;
                                                $objDBLive->saveHistoryData($laid, $b['table_name'], $b['action_unique_id'], $b['action'], $laid,
                                                $json['user_id'], $b['action_json'], 1, date('Y-m-d H:i:s'));

                                                // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 
                                                    'action' => $b['action'],'action_unique_id' => $b['action_unique_id']));
                                            } else {
                                                $laid=0;
                                                $errorArrayAssessorMsg[]="There is an error while inserting the assessor language data or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                            }
                                        } else {
                                            $laid=0;
                                            $errorArrayAssessorMsg[]="There is no assessor profile data or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $laid=0;
                                        $errorArrayAssessorMsg[]="There is no assessor profile data or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                    }                              
                                }
                                if(count($laid)==count($a['updateAssessorUserLanguageAdd']) && checkInternet($objDB)==1){
                                    $laid=1;
                                } else {
                                    $laid=0;
                                }                                
                            } else {
                                $laid = 1;
                            }
                            
                            if(!empty($a['updateAssessorIntroductoryAssessment'])){
                                $idia = array();
                                foreach ($a['updateAssessorIntroductoryAssessment'] as $j => $b) {
                                    $json = json_decode($b['action_json'],true);
                                    $SQL4="Select email from d_user where user_id = '".$b['action_content']."'";
                                    $userEmail = $objDB->get_row($SQL4);
                                   
                                    if(!empty($userEmail) && checkInternet($objDB)==1){
                                        $SQL5="Select user_id from d_user where email = '".$userEmail['email']."'";
                                        $userId = $objDBLive->get_row($SQL5);
                                        if(!empty($userId) && checkInternet($objDB)==1){
                                            $json['user_id']=$userId['user_id'];
                                            $iaid=saveAssessorIntroductoryAssessment($json,$objDBLive);
                                            
                                            if($iaid!=false){
//                                                $iaid = $objDBLive->get_last_insert_id();
                                                $idia[] = $iaid;
                                                $objDBLive->saveHistoryData($json['user_id'], $b['table_name'], $b['action_unique_id'], $b['action'], 
                                                        $json['user_id'], $json['user_id'], $b['action_json'], 1, date('Y-m-d H:i:s'));

                                                // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 
                                                    'action' => $b['action'],'action_unique_id' => $b['action_unique_id']));
                                            } else {
                                                $iaid=0;
                                                $errorArrayAssessorMsg[]="There is an error while inserting the assessor profile or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                            }
                                        } else {
                                            $iaid=0;
                                            $errorArrayAssessorMsg[]="There is no assessor profile data or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $iaid=0;
                                        $errorArrayAssessorMsg[]="There is no assessor profile data or maybe there
                                                 is no internet connectivity or maybe sync flag is off now!";
                                    }                              
                                }
                                if(count($idia)==count($a['updateAssessorIntroductoryAssessment']) && checkInternet($objDB)==1){
                                    $iaid=1;
                                } else {
                                    $iaid=0;
                                }                                
                            } else {
                                $iaid = 1;
                            }
                            
                            if ($uid > 0 && $pid > 0 && $ldid > 0 && checkInternet($objDB)==1 && $laid>0 && $iaid>0) {
                                $objDBLive->commit();
                                $objDB->commit();
                                $successArrayAssessorMsg[]="External Asseesor is updated now.";
                            } else {
                                $objDBLive->rollback();
                                $objDB->rollback();
                                $errorArrayAssessorMsg[]="There is an error while sync External Assessor data on live server or"
                                        . " maybe there is no internet connectivity or maybe sync flag is off now!";
                            }
                        }
                        foreach ($successArrayAssessorMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayAssessorMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;

                    case 'createSchoolAssessment':
                        
                        $errorArrayCreateSchoolAssessmentMsg = array();
                        $successArrayCreateSchoolAssessmentMsg = array();
                        foreach ($value as $i => $a) {
                            
                            $objDBLive->start_transaction();
                            $objDB->start_transaction();
                            // get user id and client id from the live database server on 10-03-2016 by Mohit Kumar
                            $SQL1 = "Select t1.client_id from d_user t1 Left Join h_user_user_role t2 On (t1.user_id=t2.user_id) Where "
                                    . "  t2.role_id='6' and t1.email='" . $a['email'] . "' ";
                            $clientId = $objDBLive->get_results($SQL1);
                           
                            if (!empty($clientId) && checkInternet($objDB)==1) {

                                $assessementData = json_decode($a['action_json'], true);
                                $assessementData['client_id'] = $clientId[0]['client_id'];
                                $Query1="Select name from d_diagnostic where diagnostic_id='".$assessementData['diagnostic_id']."'";
                                $localDiagnosticName=$objDB->get_row($Query1);
                                
                                
                                $Query2="Select diagnostic_id from d_diagnostic where diagnostic_id='".$assessementData['diagnostic_id']."' and "
                                        . " name='".$localDiagnosticName['name']."' ";
                                $liveDiagnosticId=$objDBLive->get_row($Query2);
                               // print_r($liveDiagnosticId);
                                //die;
                                // insert the school assessment data on live server on 10-03-2016 by Mohit Kumar
                                if(!empty($liveDiagnosticId)){
                                    if ($objDBLive->insert('d_assessment', $assessementData) && checkInternet($objDB)==1) {
                                        // get the last insert id of d_assessment table from the live server on 10-03-2016
                                        if(checkInternet($objDB)==1){
                                            $assessmentId = $objDBLive->get_last_insert_id();
                                            // save live action activity on live server on 10-03-2016 by Mohit Kumar
                                            $objDBLive->saveHistoryData($assessmentId, 'd_assessment', $a['action_unique_id'], 'createSchoolAssessment',
                                                    $clientId[0]['client_id'], $clientId[0]['client_id'], $a['action_json'], 1, date('Y-m-d H:i:s'));
                                            $objDB->update('z_history', array('action_flag' => 1), array('action_unique_id' => $a['action_unique_id'], 
                                                'id' => $a['id'],'action' => 'createSchoolAssessment'));
                                        } else {
                                            $assessmentId = 0;
                                            $errorArrayCreateSchoolAssessmentMsg[] = "There is no internet connectivity or maybe sync flag is off now!";
                                        }                                    
                                    } else {
                                        $assessmentId = 0;
                                        $errorArrayCreateSchoolAssessmentMsg[]="There is no school data for this principal '".$a['email']."' Or maybe"
                                                . " there is no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                    $assessmentId = 0;
                                    $errorArrayCreateSchoolAssessmentMsg[]="There is no Diagnostic data on live server Or maybe"
                                            . " there is no internet connectivity or maybe sync flag is off now!";
                                }
                                
                                // insert internal assessor data on live server on 10-03-2016 by Mohit Kumar
                                if (!empty($a['Internal']) && checkInternet($objDB)==1) {
                                    $b = $a['Internal'][0];
                                    $internal = json_decode($b['action_json'], true);
                                    //get user email id for getting user id on live server on 10-03-2016 by Mohit Kumar
                                    $SQL2 = "Select email from d_user where user_id='" . $internal['user_id'] . "'";
                                    $email = $objDB->get_results($SQL2);
                                    //get user id of internal assessor from live server on 10-03-2016 by Mohit Kumar
                                    $SQL3 = "Select user_id from d_user where email='" . $email[0]['email'] . "'";
                                    $userId = $objDBLive->get_results($SQL3);

                                    if (!empty($userId) && checkInternet($objDB)==1) {
                                        // check internal assessor is not giving his assessment for any other assessment
                                        $SQL4 = "select u.user_id,u.name,group_concat(if(au.isFilled=0 && assessment_type_id=1,0,1)) as filleds,
                                            group_concat(au.isFilled) as filleds2,group_concat(assessment_type_id) as assm_ids
                                            from d_user u
                                            inner join h_user_user_role ur on u.user_id=ur.user_id
                                            inner join h_user_role_user_capability rc on rc.role_id=ur.role_id
                                            inner join d_user_capability c on rc.capability_id=c.capability_id
                                            left join h_assessment_user au on au.user_id=u.user_id and au.role=3
                                            left join d_assessment a on au.assessment_id=a.assessment_id
                                            left join d_diagnostic d on a.diagnostic_id=d.diagnostic_id
                                            where u.client_id=? and slug='take_internal_assessment'
                                            group by u.user_id
                                            having filleds  not rlike concat('[[:<:]]',0,'[[:>:]]') or filleds is null
                                            order by u.name;";
                                        $checkInternalAssessor = $objDBLive->get_results($SQL4, array($clientId[0]['client_id']));

//                                        if (!empty($checkInternalAssessor) &&
//                                                in_array($userId[0]['user_id'], array_column($checkInternalAssessor, 'user_id'))
//                                                && checkInternet($objDB)==1) {
                                        if ( checkInternet($objDB)==1){
                                            $internal['user_id'] = $userId[0]['user_id'];
                                            $internal['assessment_id'] = $assessmentId;

                                            // insert school internal reviwer details on live server on 10-03-2016 by Mohit Kumar
                                            if ($objDBLive->insert('h_assessment_user', $internal) && checkInternet($objDB)==1) {
                                                //get the last insert id of h_assessment_user table from live server on 10-03-2016 by Mohit Kumar
                                                if(checkInternet($objDB)==1){
                                                    $internalId = $objDBLive->get_last_insert_id();
                                                    // save live action activity on live server on 10-03-2016 by Mohit Kumar
                                                    $objDBLive->saveHistoryData($internalId, 'h_assessment_user', $a['action_unique_id'],
                                                            'createSchoolAssessmentInternal', $internal['user_id'], $assessmentId, $b['action_json'], 1,
                                                            date('Y-m-d H:i:s'));
                                                    $objDB->update('z_history', array('action_flag'=>1),array('action_unique_id' =>$a['action_unique_id'],
                                                        'id' => $b['id'], 'action' => 'createSchoolAssessmentInternal'));
                                                } else {
                                                    $errorArrayCreateSchoolAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $internalId = 0;
                                                }

                                            } else {
                                                $internalId = 0;
                                                $errorArrayCreateSchoolAssessmentMsg[]="There is an error while inserting the data or maybe there is"
                                                        . " no internet connectivity or maybe sync flag is off now!";
                                            }
                                        } else {
                                            $internalId = 0;
                                            $errorArrayCreateSchoolAssessmentMsg[]="There is no internal assessor in live server or maybe there is"
                                                        . " no internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $internalId = 0;
                                        $errorArrayCreateSchoolAssessmentMsg[]="There is no internal assessor in live server or maybe there is"
                                                        . " no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                    $internalId = 0;
                                    $errorArrayCreateSchoolAssessmentMsg[]="There is no internal assessor in local server or maybe there is"
                                            . " no internet connectivity or maybe sync flag is off now!";;
                                }
                                // insert the school external assessor data on live server on 10-03-2016 by Mohit Kumar
                                
                                if (!empty($a['External']) ) {
                                    $b = $a['External'][0];
                                    $external = json_decode($b['action_json'], true);
                                    // get the external email from local server for getting live server user id on 10-03-2016 by Mohit Kumar
                                    $SQL2 = "Select email from d_user where user_id='" . $external['user_id'] . "'";
                                    $email = $objDB->get_results($SQL2);
                                    // get live server user id for external user on 10-03-2016 by Mohit Kumar

                                    $userId = $objDBLive->get_results("Select t1.user_id from d_user t1 Left join h_user_user_role t2 "
                                            . " on (t1.user_id=t2.user_id) where t1.email=? and t2.role_id=? ", array($email[0]['email'], 4));
                                   
                                    if (!empty($userId) && checkInternet($objDB)==1) {
                                        $external['user_id'] = $userId[0]['user_id'];
                                        $external['assessment_id'] = $assessmentId;
                                        // insert school internal reviwer details on live server on 10-03-2016 by Mohit Kumar
                                        if ($objDBLive->insert('h_assessment_user', $external) && checkInternet($objDB)==1) {
                                            //get the last insert id of h_assessment_user table from live server on 10-03-2016 by Mohit Kumar
                                            if(checkInternet($objDB)==1){
                                                $externalId = $objDBLive->get_last_insert_id();
                                                // save live action activity on live server on 10-03-2016 by Mohit Kumar
                                                $objDBLive->saveHistoryData($externalId, 'h_assessment_user', $a['action_unique_id'],
                                                        'createSchoolAssessmentExternal', $external['user_id'], $assessmentId, $b['action_json'], 1,
                                                        date('Y-m-d H:i:s'));
                                                $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$a['action_unique_id'],
                                                    'id' => $b['id'], 'action' => 'createSchoolAssessmentExternal'));
                                            } else {
                                                $externalId = 0;
                                                $errorArrayCreateSchoolAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                            }                                            
                                        } else {
                                            $externalId = 0;
                                            $errorArrayCreateSchoolAssessmentMsg[]="There is an error while inserting the data or maybe there is"
                                                        . " no internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $externalId = 0;
                                        $errorArrayCreateSchoolAssessmentMsg[]="There is no external assessor in live server or maybe there is"
                                                        . " no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                    $externalId = 1;
                                }
                                // insert the school external assessor team members data on live server on 10-03-2016 by Mohit Kumar
                                if (!empty($a['ExternalTeam'])) {
                                    $idet = array();
                                    foreach ($a['ExternalTeam'] as $j => $b) {
                                        $externalTeam = json_decode($b['action_json'], true);
                                        // get external team user email from local server on 10-03-2016 by Mohit Kumar
                                        $SQL2 = "Select email from d_user where user_id='" . $externalTeam['user_id'] . "'";
                                        $email = $objDB->get_results($SQL2);
                                        // get external team user email from live server on 10-03-2016 by Mohit Kumar
                                        //
                                        $SQL3 = "Select user_id from d_user where email='" . $email[0]['email'] . "'";
                                        $userId = $objDBLive->get_results($SQL3);
                                        //
                                        $userId = $objDBLive->get_results("Select t1.user_id from d_user t1 Left join h_user_user_role t2 "
                                                . " on (t1.user_id=t2.user_id) where t1.email=? and t2.role_id=? ", array($email[0]['email'], 4));

                                        if (!empty($userId) && checkInternet($objDB)==1) {
                                            // get external client id from live server on 10-03-2016 by Mohit Kumar
                                            $SQL4 = "Select client_id from d_user Where user_id='" . $userId[0]['user_id'] . "' ";
                                            $externalClientId = $objDBLive->get_results($SQL4);
                                            if (!empty($externalClientId) && checkInternet($objDB)==1) {
                                                $externalTeam['user_id'] = $userId[0]['user_id'];
                                                $externalTeam['assessment_id'] = $assessmentId;
                                                $externalTeam['external_client_id'] = $externalClientId[0]['client_id'];

                                                // insert school internal reviwer details on live server on 10-03-2016 by Mohit Kumar
                                                if ($objDBLive->insert('h_assessment_external_team', $externalTeam) && checkInternet($objDB)==1) {
                                                    //get the last insert id of h_assessment_user table from live server on 10-03-2016 by Mohit Kumar
                                                    if(checkInternet($objDB)==1){
                                                        $externalTeamId = $objDBLive->get_last_insert_id();
                                                        $idet[] = $externalTeamId;
                                                        // save live action activity on live server on 10-03-2016 by Mohit Kumar
                                                        $objDBLive->saveHistoryData($externalTeamId,'h_assessment_external_team',$a['action_unique_id'],
                                                                'createSchoolAssessmentExternalTeam', $externalTeam['user_id'], $assessmentId,
                                                                $b['action_json'], 1, date('Y-m-d H:i:s'));
                                                        $objDB->update('z_history', array('action_flag' => 1),
                                                                array('action_unique_id' => $a['action_unique_id'],'id' => $b['id'],
                                                                'action' => 'createSchoolAssessmentExternalTeam'));
                                                    } else {
                                                        $errorArrayCreateSchoolAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    }                                                    
                                                } else {
                                                    $errorArrayCreateSchoolAssessmentMsg[]="There is an error while inserting the external team data or "
                                                            . "maybe here is no internet connectivity or maybe sync flag is off now!";
                                                }
                                            } else {
                                                $errorArrayCreateSchoolAssessmentMsg[]="There is no school data for external team user or "
                                                            . "maybe here is no internet connectivity or maybe sync flag is off now!";
                                            }
                                        }
                                    }
                                    if(count($idet)==count($a['ExternalTeam']) && checkInternet($objDB)==1){
                                        $externalTeamId = 1; 
                                    } else {
                                        $externalTeamId = 0;
                                        $errorArrayCreateSchoolAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                    $externalTeamId = 1;
                                }
                                
                                if(!empty($a['Alert'])){
                                    if (checkInternet($objDB)==1) {
                                        $ida = array();
                                        foreach ($a['Alert'] as $j => $b) {
                                            $json = json_decode($b['action_json'],true);
                                            if ($objDBLive->insert('d_alerts', array('table_name' => $json['table_name'],'content_id'=>$assessmentId, 
                                                'content_title' => $clientId[0]['client_id'],'content_description'=>$assessmentId,
                                                'type'=>$json['type'],'status'=>0,'creation_date'=>date('Y-m-d H:i:s'))) && checkInternet($objDB)==1)
                                            {
                                                // get last insert id from h_user_user_role table on 07-03-2016
                                                $alertid = $objDBLive->get_last_insert_id();
                                                $ida[] = $alertid;
                                                // insert client logs into history table on live server on 07-03-2016 

                                                $objDBLive->saveHistoryData($alertid, $b['table_name'], $a['action_unique_id'], $b['action'], $assessmentId,
                                                        $assessmentId, $b['action_json'], 1, date('Y-m-d H:i:s'));

                                                // update the sync flag to 1 when local data are sync with live server on 07-03-2016
                                                $objDB->update('z_history', array('action_flag' => 1), array('id' => $b['id'], 
                                                    'action' => $b['action'],'action_unique_id' => $a['action_unique_id']));
                                            }
                                        }
                                        if(count($ida)==count($a['Alert']) && checkInternet($objDB)==1){
                                            $alertid=1;
                                        } else {
                                            $alertid=0;
                                        }

                                    }
                                } else {
                                    $alertid=1;
                                }
                                
//                                print_r(array($assessmentId,$internalId,$externalId,$externalTeamId,$alertid));
                                if ($assessmentId > 0 && $internalId > 0 && $externalId > 0 && $externalTeamId > 0 && checkInternet($objDB)==1
                                        && $alertid>0) {
//                                    $res = 5;
                                    $objDBLive->commit();
                                    $objDB->commit();
                                    $successArrayCreateSchoolAssessmentMsg[]="School assessment is created for this principal '".$a['email']."'";
                                } else {
                                    $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                    $objDBLive->rollback();
                                    $objDB->rollback();
                                    $action_unique_id=$a['action_unique_id'];
                                    $errorArrayCreateSchoolAssessmentMsg[]="There is an error while sync the school assessment data on live server or"
                                            . " maybe there is no internet connectivity or maybe sync flag is off now!";
    //                                    $error = 8;
    //                                    break;
                                }
                            } else {
                                $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                $errorArrayCreateSchoolAssessmentMsg[]="There is no school assessment data on live server or"
                                            . " maybe there is no internet connectivity or maybe sync flag is off now!";
    //                                $error = 9;
    //                                break;
                            }
                        }
                        foreach ($successArrayCreateSchoolAssessmentMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayCreateSchoolAssessmentMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;
                        
                    case 'updateSchoolAssessment':
                        $errorArrayUpdateSchoolAssessmentMsg = array();
                        $successArrayUpdateSchoolAssessmentMsg = array();
                        
                        foreach ($value as $i => $a) {
                            $objDBLive->start_transaction();
                            $objDB->start_transaction();
                            $keys = array_keys($a);
                            $m = $a[$keys[0]][0];
                            $SQL1="Select action_unique_id from z_history where action='createSchoolAssessment' and table_id='".$m['action_content']."'";
                            $local_action_unique_id = $objDB->get_row($SQL1);
//                            print_r($local_action_unique_id);
//                            die;
                            if(!empty($local_action_unique_id) && checkInternet($objDB)==1){
                                $SQL2="Select table_id as assessment_id,action_content as client_id from z_history where action='createSchoolAssessment'"
                                        . " and action_unique_id='".$local_action_unique_id['action_unique_id']."'";
                                $liveData=$objDBLive->get_row($SQL2);
//                                print_r($liveData);
//                            die;
                                
                            } else {
                                $liveData=array('assessment_id'=>$m['action_content']);
//                                $errorArrayUpdateSchoolAssessmentMsg[]="There is no assessment data on local server or maybe there is no interner "
//                                        . "connectivity!";
                            }
                            if(!empty($liveData) && checkInternet($objDB)==1){
                                if(!empty($a['updateSchoolAssessmentUserRemove'])){
                                    $urid=array();
                                    foreach ($a['updateSchoolAssessmentUserRemove'] as $j => $b) {
                                        if($objDBLive->delete($b['table_name'],array('assessment_id'=>$liveData['assessment_id'])) && 
                                                checkInternet($objDB)==1){
                                            $urid[]=$liveData['assessment_id'];
                                            $objDBLive->saveHistoryData($liveData['assessment_id'],$b['table_name'],$b['action_unique_id'],
                                                    $b['action'],$liveData['assessment_id'],$liveData['assessment_id'],$b['action_json'],
                                                    1,date('Y-m-d H:i:s'));
                                            $objDB->update('z_history',array('action_flag'=>1),array('id'=>$b['id'],
                                                'action_unique_id'=>$b['action_unique_id'],'action'=>$b['action']));
                                        }
                                    }
                                    if(count($urid)==count($a['updateSchoolAssessmentUserRemove']) && checkInternet($objDB)==1){
                                        $userRemove=1;
                                    } else {
                                        $userRemove=0;
                                        $errorArrayUpdateSchoolAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                    $userRemove=1;
                                }

                                if(!empty($a['updateSchoolAssessmentInternal'])){
                                    $b = $a['updateSchoolAssessmentInternal'][0];
                                    $internal = json_decode($b['action_json'], true);
                                    //get user email id for getting user id on live server on 10-03-2016 by Mohit Kumar
                                    $SQL2 = "Select email from d_user where user_id='" . $internal['user_id'] . "'";
                                    $email = $objDB->get_results($SQL2);
                                    //get user id of internal assessor from live server on 10-03-2016 by Mohit Kumar
                                    $SQL3 = "Select user_id from d_user where email='" . $email[0]['email'] . "'";
                                    $userId = $objDBLive->get_results($SQL3);

                                    if (!empty($userId) && checkInternet($objDB)==1) {
                                        // check internal assessor is not giving his assessment for any other assessment
                                        $SQL4 = "select u.user_id,u.name,group_concat(if(au.isFilled=0 && assessment_type_id=1,0,1)) as filleds,
                                            group_concat(au.isFilled) as filleds2,group_concat(assessment_type_id) as assm_ids
                                            from d_user u
                                            inner join h_user_user_role ur on u.user_id=ur.user_id
                                            inner join h_user_role_user_capability rc on rc.role_id=ur.role_id
                                            inner join d_user_capability c on rc.capability_id=c.capability_id
                                            left join h_assessment_user au on au.user_id=u.user_id and au.role=3
                                            left join d_assessment a on au.assessment_id=a.assessment_id
                                            left join d_diagnostic d on a.diagnostic_id=d.diagnostic_id
                                            where u.client_id=? and slug='take_internal_assessment'
                                            group by u.user_id
                                            having filleds  not rlike concat('[[:<:]]',0,'[[:>:]]') or filleds is null
                                            order by u.name;";
                                        $checkInternalAssessor = $objDBLive->get_results($SQL4, array($liveData['client_id']));

//                                        if (!empty($checkInternalAssessor) && 
//                                                in_array($userId[0]['user_id'], array_column($checkInternalAssessor, 'user_id'))
//                                                && checkInternet($objDB)==1) {
                                        if ( checkInternet($objDB)==1){
                                            $internal['user_id'] = $userId[0]['user_id'];
                                            $internal['assessment_id'] = $liveData['assessment_id'];

                                            // insert school internal reviwer details on live server on 10-03-2016 by Mohit Kumar
                                            if ($objDBLive->insert('h_assessment_user', $internal) && checkInternet($objDB)==1) {
                                                //get the last insert id of h_assessment_user table from live server on 10-03-2016 by Mohit Kumar
                                                if(checkInternet($objDB)==1){
                                                    $internalId = $objDBLive->get_last_insert_id();
                                                    // save live action activity on live server on 10-03-2016 by Mohit Kumar
                                                    $objDBLive->saveHistoryData($internalId,$b['table_name'], $b['action_unique_id'],
                                                        $b['action'], $internal['user_id'], $liveData['assessment_id'],
                                                        $b['action_json'], 1, date('Y-m-d H:i:s'));
                                                    $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$b['action_unique_id'],
                                                        'id' => $b['id'], 'action' => $b['action']));
                                                } else {
                                                    $errorArrayUpdateSchoolAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $internalId = 0;
                                                }

                                            } else {
                                                $internalId = 0;
                                                $errorArrayUpdateSchoolAssessmentMsg[]="There is an error while inserting the data or maybe there is"
                                                        . " no internet connectivity or maybe sync flag is off now!";
                                            }
                                        } else {
                                            $internalId = 0;
                                            $errorArrayUpdateSchoolAssessmentMsg[]="There is no internal assessor in live server or maybe there is"
                                                        . " no internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $internalId = 0;
                                        $errorArrayUpdateSchoolAssessmentMsg[]="There is no internal assessor in live server or maybe there is"
                                                        . " no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                    $internalId=1;
                                }
                                // insert the school external assessor data on live server on 10-03-2016 by Mohit Kumar
                                if(!empty($a['updateSchoolAssessmentExternal'])){
                                    if (!empty($a['updateSchoolAssessmentExternal']) && checkInternet($objDB)==1) {
                                        $b = $a['updateSchoolAssessmentExternal'][0];
                                        $external = json_decode($b['action_json'], true);
                                        // get the external email from local server for getting live server user id on 10-03-2016 by Mohit Kumar
                                        $SQL2 = "Select email from d_user where user_id='" . $external['user_id'] . "'";
                                        $email = $objDB->get_results($SQL2);
                                        // get live server user id for external user on 10-03-2016 by Mohit Kumar

                                        $userId = $objDBLive->get_results("Select t1.user_id from d_user t1 Left join h_user_user_role t2 "
                                                . " on (t1.user_id=t2.user_id) where t1.email=? and t2.role_id=? ", array($email[0]['email'], 4));
                                        if (!empty($userId) && checkInternet($objDB)==1) {
                                            $external['user_id'] = $userId[0]['user_id'];
                                            $external['assessment_id'] = $liveData['assessment_id'];
                                            // insert school internal reviwer details on live server on 10-03-2016 by Mohit Kumar
                                            if ($objDBLive->insert('h_assessment_user', $external) && checkInternet($objDB)==1) {
                                                //get the last insert id of h_assessment_user table from live server on 10-03-2016 by Mohit Kumar
                                                if(checkInternet($objDB)==1){
                                                    $externalId = $objDBLive->get_last_insert_id();
                                                    // save live action activity on live server on 10-03-2016 by Mohit Kumar
                                                    $objDBLive->saveHistoryData($externalId,$b['table_name'], $b['action_unique_id'],
                                                        $b['action'], $external['user_id'], $liveData['assessment_id'],
                                                        $b['action_json'], 1, date('Y-m-d H:i:s'));
                                                    $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$b['action_unique_id'],
                                                        'id' => $b['id'], 'action' => $b['action']));
                                                } else {
                                                    $externalId = 0;
                                                    $errorArrayCreateSchoolAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                }                                            
                                            } else {
                                                $externalId = 0;
                                                $errorArrayCreateSchoolAssessmentMsg[]="There is an error while inserting the data or maybe there is"
                                                            . " no internet connectivity or maybe sync flag is off now!";
                                            }
                                        } else {
                                            $externalId = 0;
                                            $errorArrayCreateSchoolAssessmentMsg[]="There is no external assessor in live server or maybe there is"
                                                            . " no internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $externalId = 0;
                                        $errorArrayCreateSchoolAssessmentMsg[]="There is no external assessor in live server or maybe there is"
                                                            . " no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                    $externalId = 1;
                                }

                                // insert the school external assessor team members data on live server on 10-03-2016 by Mohit Kumar

                                if(!empty($a['updateSchoolAssessmentExternalRemove'])){
                                    $urid=array();
                                    foreach ($a['updateSchoolAssessmentExternalRemove'] as $j => $b) {
                                        if($objDBLive->delete($b['table_name'],array('assessment_id'=>$liveData['assessment_id'])) && 
                                                checkInternet($objDB)==1){
                                            $urid[]=$liveData['assessment_id'];
                                            $objDBLive->saveHistoryData($liveData['assessment_id'],$b['table_name'],$b['action_unique_id'],
                                                    $b['action'],$liveData['assessment_id'],$liveData['assessment_id'],$b['action_json'],
                                                    1,date('Y-m-d H:i:s'));
                                            $objDB->update('z_history',array('action_flag'=>1),array('id'=>$b['id'],
                                                'action_unique_id'=>$b['action_unique_id'],'action'=>$b['action']));
                                        }
                                    }
                                    if(count($urid)==count($a['updateSchoolAssessmentExternalRemove']) && checkInternet($objDB)==1){
                                        $externalRemove=1;
                                    } else {
                                        $externalRemove=0;
                                        $errorArrayUpdateSchoolAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                    $externalRemove=1;
                                }
                                if(!empty($a['updateSchoolAssessment'])){
                                    if(!empty($a['updateSchoolAssessment']) && checkInternet($objDB)==1){
                                        $b=$a['updateSchoolAssessment'][0];
                                        $actionJson = json_decode($b['action_json'],true);
                                        $Query1="Select name from d_diagnostic where diagnostic_id='".$actionJson['diagnostic_id']."'";
                                        $localDiagnosticName=$objDB->get_row($Query1);


                                        $Query2="Select diagnostic_id from d_diagnostic where diagnostic_id='".$actionJson['diagnostic_id']."' and "
                                                . " name='".$localDiagnosticName['name']."' ";
                                        $liveDiagnosticId=$objDBLive->get_row($Query2);
                                        if(!empty($liveDiagnosticId) && checkInternet($objDB)==1){
                                            $actionJson['diagnostic_id'] = $liveDiagnosticId['diagnostic_id'];
                                            if($objDBLive->update('d_assessment',array('diagnostic_id'=>$liveDiagnosticId['diagnostic_id'],
                                                    'tier_id'=>$actionJson['tier_id'],'award_scheme_id'=>$actionJson['award_scheme_id'],
                                                    'create_date'=>$actionJson['create_date']), array('assessment_id' => $liveData['assessment_id']))
                                                    && checkInternet($objDB)==1){
                                                $objDBLive->saveHistoryData($liveData['assessment_id'],$b['table_name'],$b['action_unique_id'],
                                                        $b['action'],$liveData['assessment_id'],$liveData['assessment_id'],$b['action_json'],
                                                        1,date('Y-m-d H:i:s'));
                                                $objDB->update('z_history',array('action_flag'=>1),array('id'=>$b['id'],
                                                    'action_unique_id'=>$b['action_unique_id'],'action'=>$b['action']));
                                                $updateAssessment=1;
                                            } else {
                                                $updateAssessment=0;
                                                $errorArrayUpdateSchoolAssessmentMsg[]="There is an error while sync data on live server Or maybe"
                                                    . " there is no internet connectivity or maybe sync flag is off now!";
                                            }
                                        } else {
                                            $updateAssessment=0;
                                            $errorArrayUpdateSchoolAssessmentMsg[]="There is no Diagnostic data on live server Or maybe"
                                                . " there is no internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $updateAssessment=0;
                                    }
                                } else {
                                    $updateAssessment=1;
                                }

                                if(!empty($a['updateSchoolAssessmentExternalTeam'])){
                                    if (!empty($a['updateSchoolAssessmentExternalTeam']) && checkInternet($objDB)==1) {
                                        $idet = array();
                                        foreach ($a['updateSchoolAssessmentExternalTeam'] as $j => $b) {
                                            $externalTeam = json_decode($b['action_json'], true);
                                            // get external team user email from local server on 10-03-2016 by Mohit Kumar
                                            $SQL2 = "Select email from d_user where user_id='" . $externalTeam['user_id'] . "'";
                                            $email = $objDB->get_results($SQL2);
                                            // get external team user email from live server on 10-03-2016 by Mohit Kumar

//                                            $SQL3 = "Select user_id from d_user where email='" . $email[0]['email'] . "'";
//                                            $userId = $objDBLive->get_results($SQL3);
                                            //
                                            $userId = $objDBLive->get_results("Select t1.user_id from d_user t1 Left join h_user_user_role t2 "
                                                    . " on (t1.user_id=t2.user_id) where t1.email=? and t2.role_id=? ", array($email[0]['email'], 4));

                                            if (!empty($userId) && checkInternet($objDB)==1) {
                                                // get external client id from live server on 10-03-2016 by Mohit Kumar
                                                $SQL4 = "Select client_id from d_user Where user_id='" . $userId[0]['user_id'] . "' ";
                                                $externalClientId = $objDBLive->get_results($SQL4);

                                                if (!empty($externalClientId) && checkInternet($objDB)==1) {
                                                    $externalTeam['user_id'] = $userId[0]['user_id'];
                                                    $externalTeam['assessment_id'] = $liveData['assessment_id'];
                                                    $externalTeam['external_client_id'] = $externalClientId[0]['client_id'];

                                                    // insert school internal reviwer details on live server on 10-03-2016 by Mohit Kumar
                                                    if ($objDBLive->insert('h_assessment_external_team', $externalTeam) && checkInternet($objDB)==1) {
                                                        //get the last insert id of h_assessment_user table from live server on 10-03-2016 by Mohit Kumar
                                                        if(checkInternet($objDB)==1){
                                                            $externalTeamId = $objDBLive->get_last_insert_id();
                                                            $idet[] = $externalTeamId;
                                                            // save live action activity on live server on 10-03-2016 by Mohit Kumar
                                                            $objDBLive->saveHistoryData($liveData['assessment_id'],$b['table_name'],$b['action_unique_id'],
                                                                $b['action'],$liveData['assessment_id'],$liveData['assessment_id'],$b['action_json'],
                                                                1,date('Y-m-d H:i:s'));
                                                            $objDB->update('z_history',array('action_flag'=>1),array('id'=>$b['id'],
                                                                'action_unique_id'=>$b['action_unique_id'],'action'=>$b['action']));
                                                        } else {
                                                            $errorArrayUpdateSchoolAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                        }                                                    
                                                    } else {
                                                        $errorArrayUpdateSchoolAssessmentMsg[]="There is an error while inserting the external team data or "
                                                                . "maybe here is no internet connectivity or maybe sync flag is off now!";
                                                    }
                                                } else {
                                                    $errorArrayUpdateSchoolAssessmentMsg[]="There is no school data for external team user or "
                                                                . "maybe here is no internet connectivity or maybe sync flag is off now!";
                                                }
                                            }
                                        }
                                        if(count($idet)==count($a['updateSchoolAssessmentExternalTeam']) && checkInternet($objDB)==1){
                                            $externalTeamId = 1; 
                                        } else {
                                            $externalTeamId = 0;
                                            $errorArrayUpdateSchoolAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $externalTeamId = 0;
                                    }
                                } else {
                                    $externalTeamId = 1;
                                }


                                if ($userRemove > 0 && $internalId > 0 && $externalId > 0 && $externalTeamId > 0 && checkInternet($objDB)==1 && 
                                        $externalRemove>0 && $updateAssessment>0) {
                                    //$res = 5;
                                    $objDBLive->commit();
                                    $objDB->commit();
                                    $successArrayUpdateSchoolAssessmentMsg[]="School assessment is updated ";
                                } else {
                                    $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $m['action_unique_id']));
                                    $objDBLive->rollback();
                                    $objDB->rollback();
                                    //$action_unique_id=$a['action_unique_id'];
                                    $errorArrayUpdateSchoolAssessmentMsg[]="There is an error while sync the school assessment data on live server or"
                                            . " maybe there is no internet connectivity or maybe sync flag is off now!";
    //                                    $error = 8;
    //                                    break;
                                }
                            } else {
                                $errorArrayUpdateSchoolAssessmentMsg[]="There is no assessment data on live server or maybe there is no interner "
                                    . "connectivity!";
                            }
                            
                        }
                        foreach ($successArrayUpdateSchoolAssessmentMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayUpdateSchoolAssessmentMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;

                    case 'assessmentAQSDataInsert':
                        
                        $checkInternet = checkInternet1();
                        $errorAssessmentAQSDataMsg = array();
                        $successAssessmentAQSDataMsg = array();
                        foreach ($value as $i => $a) {

                            $SQL1 = "Select t1.client_id,t1.user_id from d_user t1 left join d_client t2 On (t1.client_id=t2.client_id) where "
                                    . "t1.email='" . $a['action_content'] . "'";
                            $clientPrincipalId = $objDBLive->get_row($SQL1);
                            
                            if (!empty($clientPrincipalId) && $checkInternet==1) {

                                $aqsData = json_decode($a['action_json'], true);
                                if (!empty($aqsData) && $checkInternet==1) {
                                    
                                    if ($checkInternet==1) {
                                        // insert data into d_AQS_data table on live server on 14-03-2016 by Mohit Kumar
                                        $objDBLive->start_transaction();
                                        $objDB->start_transaction();
    //                                        if (empty($checkAQSData)) {
                                        if ($objDBLive->insert('d_AQS_data', $aqsData) && $checkInternet==1) {
                                            // get last insert id of d_AQS_data table on 14-03-2016 by Mohit Kumar
                                            if($checkInternet==1){
                                                $aqsID = $objDBLive->get_last_insert_id();
                                                //start---> save the history for insert aqs data into history table on 14-03-2016 By Mohit Kumar
                                                $objDBLive->saveHistoryData($aqsID, 'd_AQS_data', $a['action_unique_id'], 'assessmentAQSDataInsert',
                                                        $aqsID,$a['action_content'], $a['action_json'], 1, date('Y-m-d H:i:s'));
                                                //end---> save the history for insert aqs data into history table on 14-03-2016 By Mohit Kumar
                                                //update local server history table status 1 for action assessmentAQSDataInsert on 14-03-2016 by Mohit
                                                // Kumar
                                                $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$a['action_unique_id'],
                                                    'id' => $a['id'],'action'=>'assessmentAQSDataInsert'));

                                                // update aqs data id into d_assessment table on 14-03-2016 by Mohit Kumar
                                                if(!empty($a['UpdateAQSAssessorData'])){
                                                    if (!empty($a['UpdateAQSAssessorData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $aqsAssessment=saveAQSAssessmentData($a['UpdateAQSAssessorData'],'UpdateAQSAssessorData',$aqsID,
                                                                $clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);                                                
                                                    } else {
                                                        $aqsAssessment = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for updating the assessor data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $aqsAssessment = 1;
                                                }                                                

                                                // update principle user information on d_user table on 14-03-2016 by Mohit Kumar
                                                if(!empty($a['UpdateAQSUserData'])){
                                                    if (!empty($a['UpdateAQSUserData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $principalUpdate=saveAQSAssessmentData($a['UpdateAQSUserData'],'UpdateAQSUserData',$aqsID,
                                                                $clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);                                                
                                                    } else {
                                                        $principalUpdate = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for updating the principal data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }  
                                                } else {
                                                    $principalUpdate = 1;
                                                }                                                                                          

                                                // update school data on live server on 14-03-2016 by Mohit Kumar
                                                if(!empty($a['UpdateAQSSchoolData'])){
                                                    if (!empty($a['UpdateAQSSchoolData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $schoolUpdate=saveAQSAssessmentData($a['UpdateAQSSchoolData'],'UpdateAQSSchoolData',$aqsID,
                                                                $clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);  
                                                    } else {
                                                        $schoolUpdate = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for updating the school data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $schoolUpdate = 1;
                                                }                                                                                            

                                                // remove IT support data from h_aqsdata_itsupport on live server on 14-03-2016 by Mohit Kumar
                                                if(!empty($a['RemoveAQSITSupportData'])){
                                                    if (!empty($a['RemoveAQSITSupportData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $removeITSupport=saveAQSAssessmentData($a['RemoveAQSITSupportData'],'RemoveAQSITSupportData',$aqsID,
                                                                $clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet); 
                                                    } else {
                                                        $removeITSupport = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for removing IT support data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $removeITSupport = 1;
                                                }                                                

                                                // add IT support data from h_aqsdata_itsupport on live server on 14-03-2016 by Mohit Kumar
                                                if(!empty($a['AddAQSITSupportData'])){
                                                    if (!empty($a['AddAQSITSupportData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idSupportID=saveAQSAssessmentData($a['AddAQSITSupportData'],'AddAQSITSupportData',$aqsID,
                                                                $clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet); 
                                                    } else {
                                                        $idSupportID = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for adding IT support data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idSupportID = 1;
                                                }                                                                                           

                                                // remove aqs school timing from h_AQS_school_level table on 15-03-2016 by Mohit Kumar
                                                if(!empty($a['RemoveAQSSchoolTimingData']) ){
                                                    if(!empty($a['RemoveAQSSchoolTimingData']) && $checkInternet==1){
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $removeSchoolTiming=saveAQSAssessmentData($a['RemoveAQSSchoolTimingData'],'RemoveAQSSchoolTimingData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);                                            
                                                    } else {
                                                        $removeSchoolTiming=0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for removing school timing data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $removeSchoolTiming=1;
                                                }                                                

                                                // add aqs school timing in h_AQS_school_level table on 15-03-2016 by Mohit Kumar
                                                if(!empty($a['AddAQSSchoolTimingData'])){
                                                    if (!empty($a['AddAQSSchoolTimingData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idSchoolTiming=saveAQSAssessmentData($a['AddAQSSchoolTimingData'],'AddAQSSchoolTimingData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idSchoolTiming = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for adding school timing data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    } 
                                                } else {
                                                    $idSchoolTiming = 1;
                                                }
                                                                                            

                                                // remove aqs team from d_AQS_team table on 15-03-2016 by Mohit Kumar
                                                if(!empty($a['RemoveAQSAQSTeamData'])){
                                                    if(!empty($a['RemoveAQSAQSTeamData']) && $checkInternet==1){
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $removeAQSTeam=saveAQSAssessmentData($a['RemoveAQSAQSTeamData'],'RemoveAQSAQSTeamData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $removeAQSTeam=0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for removing aqs team data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $removeAQSTeam=1;
                                                }
                                                // add aqs team in d_AQS_team table on 15-03-2016 by Mohit Kumar
                                                if(!empty($a['AddAQSAQSTeamData'])){
                                                    if (!empty($a['AddAQSAQSTeamData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idAQSTeam=saveAQSAssessmentData($a['AddAQSAQSTeamData'],'AddAQSAQSTeamData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idAQSTeam = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for adding aqs team data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idAQSTeam = 1;
                                                } 
                                                
                                                if(!empty($a['AddAQSDataAdditionalRefTeam'])){
                                                    if (!empty($a['AddAQSDataAdditionalRefTeam']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idAddRef=saveAQSAssessmentData($a['AddAQSDataAdditionalRefTeam'],'AddAQSDataAdditionalRefTeam',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idAddRef = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for adding aqs additional ref team data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idAddRef = 1;
                                                }
                                                if(!empty($a['RemoveAQSDataAdditionalMediumInstruction'])){
                                                    if (!empty($a['RemoveAQSDataAdditionalMediumInstruction']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idRemoveMedInst=saveAQSAssessmentData($a['RemoveAQSDataAdditionalMediumInstruction'],'RemoveAQSDataAdditionalMediumInstruction',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idRemoveMedInst = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for removing addditional medium instruction on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idRemoveMedInst = 1;
                                                }
                                                if(!empty($a['RemoveAQSDataAdditionalSchoolCommunity'])){
                                                    if (!empty($a['RemoveAQSDataAdditionalSchoolCommunity']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idSchoolCom=saveAQSAssessmentData($a['RemoveAQSDataAdditionalSchoolCommunity'],'RemoveAQSDataAdditionalSchoolCommunity',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idSchoolCom = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for removing aqs additional school community on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idSchoolCom = 1;
                                                }
                                                if(!empty($a['AddAQSDataAdditionalSchoolCommunity'])){
                                                    if (!empty($a['AddAQSDataAdditionalSchoolCommunity']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idSchoolComAdd=saveAQSAssessmentData($a['AddAQSDataAdditionalSchoolCommunity'],'AddAQSDataAdditionalSchoolCommunity',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idSchoolComAdd = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for adding aqs additional school community data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idSchoolComAdd = 1;
                                                }
                                                if(!empty($a['AddAQSDataAdditionalQuestionsData'])){
                                                    if (!empty($a['AddAQSDataAdditionalQuestionsData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idQuestData=saveAQSAssessmentData($a['AddAQSDataAdditionalQuestionsData'],'AddAQSDataAdditionalQuestionsData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idQuestData = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for adding additional questions data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idQuestData = 1;
                                                }
                                                if(!empty($a['AddAQSDataAdditionalMediumInstruction'])){
                                                    if (!empty($a['AddAQSDataAdditionalMediumInstruction']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idAddMedInst=saveAQSAssessmentData($a['AddAQSDataAdditionalMediumInstruction'],'AddAQSDataAdditionalMediumInstruction',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idAddMedInst = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for adding additional medium instruction data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idAddMedInst = 1;
                                                }
                                                if(!empty($a['RemoveAQSDataAdditionalRefTeam'])){
                                                    if (!empty($a['RemoveAQSDataAdditionalRefTeam']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idRemMedInst=saveAQSAssessmentData($a['RemoveAQSDataAdditionalRefTeam'],'RemoveAQSDataAdditionalRefTeam',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idRemMedInst = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for removing additional ref team on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idRemMedInst = 1;
                                                }
                                                if(!empty($a['UpdateAQSDataAdditionalQuestionsData'])){
                                                    if (!empty($a['UpdateAQSDataAdditionalQuestionsData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idUpdateQuestion=saveAQSAssessmentData($a['UpdateAQSDataAdditionalQuestionsData'],'UpdateAQSDataAdditionalQuestionsData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idUpdateQuestion = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for updating additional question data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idUpdateQuestion = 1;
                                                }
                                            } else {
                                                $errorAssessmentAQSDataMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                            }                                            
                                        } else {
                                            $aqsID = 0;
                                            $errorAssessmentAQSDataMsg[]="There is an error for adding aqs assessor data on live server or "
                                                            . "maybe there is no internet connectivity!";
                                        }
                                        // commit and roll back the queries
                                        /*print_r(array($aqsID,$aqsAssessment,$principalUpdate,$schoolUpdate,$removeITSupport,$idSupportID,
                                            $removeSchoolTiming,$idSchoolTiming,$removeAQSTeam,$idAQSTeam,$idAddRef,$idRemoveMedInst,
                                            $idSchoolCom,$idSchoolComAdd,$idQuestData,$idAddMedInst,$idRemMedInst));*/
										
                                        if($aqsID>0 && $aqsAssessment>0 && $principalUpdate>0 && $schoolUpdate>0 && $removeITSupport>0 && $idSupportID>0
                                                && $removeSchoolTiming>0 && $idSchoolTiming>0 && $removeAQSTeam>0 && $idAQSTeam>0 && $idAddRef>0 &&
                                                $idRemoveMedInst>0 && $idSchoolCom>0 && $idSchoolComAdd>0 && $idQuestData>0 && $idAddMedInst>0
                                                && $idRemMedInst>0 && $checkInternet==1 && $idUpdateQuestion>0)
                                        {
                                            $res = 5;
                                            $objDBLive->commit();
                                            $objDB->commit();
                                            $successAssessmentAQSDataMsg[]="Assessment data is added on live server successfully!";
                                        } else {
                                            $objDB->update('z_history',array('action_flag'=>0),array('action_unique_id'=>$a['action_unique_id']));
                                            $objDBLive->rollback();
                                            $objDB->rollback();
//                                            print_r(array($aqsID,$aqsAssessment,$principalUpdate,$schoolUpdate,$removeITSupport,$idSupportID,$removeSchoolTiming,$idSchoolTiming,$removeAQSTeam,$idAQSTeam));
                                            $action_unique_id=$a['action_unique_id'];
                                            $errorArrayCreateSchoolAssessmentMsg[]="There is an error while sync the school aqs assessment data on live "
                                                    . "server or maybe there is no internet connectivity!";
    //                                            $error = 11;
    //                                            break;
                                        }                                        
                                    } else {
                                        $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                        $errorAssessmentAQSDataMsg[]="There is no internet connectivity or maybe sync flag is off now!";
    //                                        $error = 10;
    //                                        break;
                                    }
                                } else {
                                    $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                    $errorAssessmentAQSDataMsg[]="There is an error for adding aqs json data on local server or "
                                                            . "maybe there is no internet connectivity!";
    //                                    $error = 10;
    //                                    break;
                                }
                            } else {
                                $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                $errorAssessmentAQSDataMsg[]="There is no school data for this principal '".$a['action_content']."' or "
                                                            . "maybe there is no internet connectivity!";
    //                                $error = 4;
    //                                break;
                            }
                        }
                        foreach ($successAssessmentAQSDataMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorAssessmentAQSDataMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;

                    case 'assessmentAQSDataUpdate':
                        $checkInternet = checkInternet1();
                        $errorAssessmentAQSDataUpdateMsg = array();
                        $successAssessmentAQSDataUpdateMsg = array();
                        
                        foreach ($value as $i => $a) {
                            
                            $SQL1 = "Select t1.client_id,t1.user_id from d_user t1 left join d_client t2 On (t1.client_id=t2.client_id) where "
                                    . "t1.email='" . $a['action_content'] . "'";
                            $clientPrincipalId = $objDBLive->get_row($SQL1);
                            
                            if(!empty($clientPrincipalId) && ($objDBLive->get_row($SQL1))!=NULL && $checkInternet==1){
                                $aqsData = json_decode($a['action_json'], true);
                                
                                if (!empty($aqsData)) {
                                    //echo '</pre>';
                                     $Query1="Select action_unique_id,action_json from z_history where table_id='".$a['table_id']."' and "
                                        . "table_name='".$a['table_name']."' and action='assessmentAQSDataUpdate' ";
                                    $localActionuniqueId = $objDB->get_row($Query1);
//                                    print_r($localActionuniqueId);
//                                    die;
                                    $SQL2 = "Select table_id as id from z_history where (action_unique_id='".$localActionuniqueId['action_unique_id']."'"
                                        . " and action='assessmentAQSDataInsert' and action_json='".$localActionuniqueId['action_json']."') Or "
                                        . " action_unique_id='".$localActionuniqueId['action_unique_id']."'"
                                           . " and action='assessmentAQSDataUpdate'  order by id desc";
                                    $checkAQSData = $objDBLive->get_row($SQL2);
//                                    print_r($localActionuniqueId);
//                                    die;
                                    if(!empty($checkAQSData) && ($objDBLive->get_row($SQL2))!=NULL && $checkInternet==1){
                                        $aqsID = $checkAQSData['id'];
                                        // update data into d_AQS_data table on live server on 14-03-2016 by Mohit Kumar
                                        $objDBLive->start_transaction();
                                        $objDB->start_transaction();
                                        if($objDBLive->update("d_AQS_data", $aqsData, array("id" => $aqsID)) && $checkInternet==1){
                                            //start---> save the history for insert aqs data into history table on 14-03-2016 By Mohit Kumar
                                            if($checkInternet==1){
                                                $objDBLive->saveHistoryData($aqsID, 'd_AQS_data', $a['action_unique_id'], 'assessmentAQSDataUpdate', 
                                                        $aqsID,$a['action_content'], $a['action_json'], 1, date('Y-m-d H:i:s'));
                                                //end---> save the history for insert aqs data into history table on 14-03-2016 By Mohit Kumar
                                                //update local server history table status 1 for action assessmentAQSDataInsert on 14-03-2016 by Mohit
                                                // Kumar
                                                $objDB->update('z_history',array('action_flag' =>1),array('action_unique_id'=>$a['action_unique_id'],
                                                    'id' => $a['id'], 'action' => 'assessmentAQSDataUpdate'));

                                                // update principle user information on d_user table on 14-03-2016 by Mohit Kumar
                                                if(!empty($a['UpdateAQSUserData'])){
                                                    if (!empty($a['UpdateAQSUserData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $principalUpdate=saveAQSAssessmentData($a['UpdateAQSUserData'],'UpdateAQSUserData',$aqsID,
                                                                $clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);                                                
                                                    } else {
                                                        $principalUpdate = 0;
                                                        $errorAssessmentAQSDataUpdateMsg[]="There is an error for updating the principal data on live server"
                                                                . " or maybe there is no internet connectivity!";
                                                    } 
                                                } else {
                                                    $principalUpdate = 1;
                                                }                                       

                                                // update school data on live server on 14-03-2016 by Mohit Kumar
                                                if(!empty($a['UpdateAQSSchoolData'])){
                                                    if (!empty($a['UpdateAQSSchoolData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $schoolUpdate=saveAQSAssessmentData($a['UpdateAQSSchoolData'],'UpdateAQSSchoolData',$aqsID,
                                                                $clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);  
                                                    } else {
                                                        $schoolUpdate = 0;
                                                        $errorAssessmentAQSDataUpdateMsg[]="There is an error for updating the school data on live server "
                                                                . "or maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $schoolUpdate = 1;
                                                }                                        

                                                // remove IT support data from h_aqsdata_itsupport on live server on 14-03-2016 by Mohit Kumar
                                                if(!empty($a['RemoveAQSITSupportData'])){
                                                    if (!empty($a['RemoveAQSITSupportData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $removeITSupport=saveAQSAssessmentData($a['RemoveAQSITSupportData'],'RemoveAQSITSupportData',$aqsID,
                                                                $clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet); 
                                                    } else {
                                                        $removeITSupport = 0;
                                                        $errorAssessmentAQSDataUpdateMsg[]="There is an error for removing IT support data on live server "
                                                                . "or maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $removeITSupport = 1;
                                                }
                                                
                                                // add IT support data from h_aqsdata_itsupport on live server on 14-03-2016 by Mohit Kumar
                                                if(!empty($a['AddAQSITSupportData'])){
                                                    if (!empty($a['AddAQSITSupportData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idSupportID=saveAQSAssessmentData($a['AddAQSITSupportData'],'AddAQSITSupportData',$aqsID,
                                                                $clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet); 
                                                    } else {
                                                        $idSupportID = 0;
                                                        $errorAssessmentAQSDataUpdateMsg[]="There is an error for adding IT support data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    } 
                                                } else {
                                                    $idSupportID = 1;
                                                }                                        

                                                // remove aqs school timing from h_AQS_school_level table on 15-03-2016 by Mohit Kumar
                                                if(!empty($a['RemoveAQSSchoolTimingData'])){
                                                    if(!empty($a['RemoveAQSSchoolTimingData']) && $checkInternet==1){
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $removeSchoolTiming=saveAQSAssessmentData($a['RemoveAQSSchoolTimingData'],'RemoveAQSSchoolTimingData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);                                            
                                                    } else {
                                                        $removeSchoolTiming=0;
                                                        $errorAssessmentAQSDataUpdateMsg[]="There is an error for removing school timing data on live server"
                                                                . " or maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $removeSchoolTiming=1;
                                                }

                                                // add aqs school timing in h_AQS_school_level table on 15-03-2016 by Mohit Kumar
                                                if(!empty($a['AddAQSSchoolTimingData'])){
                                                    if (!empty($a['AddAQSSchoolTimingData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idSchoolTiming=saveAQSAssessmentData($a['AddAQSSchoolTimingData'],'AddAQSSchoolTimingData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idSchoolTiming = 0;
                                                        $errorAssessmentAQSDataUpdateMsg[]="There is an error for adding school timing data on live server"
                                                                . " or maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idSchoolTiming = 1;
                                                }                                          

                                                // remove aqs team from d_AQS_team table on 15-03-2016 by Mohit Kumar
                                                if(!empty($a['RemoveAQSAQSTeamData'])){
                                                    if(!empty($a['RemoveAQSAQSTeamData']) && $checkInternet==1){
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $removeAQSTeam=saveAQSAssessmentData($a['RemoveAQSAQSTeamData'],'RemoveAQSAQSTeamData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $removeAQSTeam=0;
                                                        $errorAssessmentAQSDataUpdateMsg[]="There is an error for removing aqs team data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $removeAQSTeam=1;
                                                }

                                                // add aqs team in d_AQS_team table on 15-03-2016 by Mohit Kumar
                                                if(!empty($a['AddAQSAQSTeamData'])){
                                                    if (!empty($a['AddAQSAQSTeamData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idAQSTeam=saveAQSAssessmentData($a['AddAQSAQSTeamData'],'AddAQSAQSTeamData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idAQSTeam = 0;
                                                        $errorAssessmentAQSDataUpdateMsg[]="There is an error for adding aqs team data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idAQSTeam = 1;
                                                }
                                                
                                                if(!empty($a['AddAQSDataAdditionalRefTeam'])){
                                                    if (!empty($a['AddAQSDataAdditionalRefTeam']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idAddRef=saveAQSAssessmentData($a['AddAQSDataAdditionalRefTeam'],'AddAQSDataAdditionalRefTeam',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idAddRef = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for adding aqs additional ref team data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idAddRef = 1;
                                                }
                                                if(!empty($a['RemoveAQSDataAdditionalMediumInstruction'])){
                                                    if (!empty($a['RemoveAQSDataAdditionalMediumInstruction']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idRemoveMedInst=saveAQSAssessmentData($a['RemoveAQSDataAdditionalMediumInstruction'],'RemoveAQSDataAdditionalMediumInstruction',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idRemoveMedInst = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for removing addditional medium instruction on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idRemoveMedInst = 1;
                                                }
                                                if(!empty($a['RemoveAQSDataAdditionalSchoolCommunity'])){
                                                    if (!empty($a['RemoveAQSDataAdditionalSchoolCommunity']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idSchoolCom=saveAQSAssessmentData($a['RemoveAQSDataAdditionalSchoolCommunity'],'RemoveAQSDataAdditionalSchoolCommunity',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idSchoolCom = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for removing aqs additional school community on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idSchoolCom = 1;
                                                }
                                                if(!empty($a['AddAQSDataAdditionalSchoolCommunity'])){
                                                    if (!empty($a['AddAQSDataAdditionalSchoolCommunity']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idSchoolComAdd=saveAQSAssessmentData($a['AddAQSDataAdditionalSchoolCommunity'],'AddAQSDataAdditionalSchoolCommunity',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idSchoolComAdd = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for adding aqs additional school community data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idSchoolComAdd = 1;
                                                }
                                                if(!empty($a['AddAQSDataAdditionalQuestionsData'])){
                                                    if (!empty($a['AddAQSDataAdditionalQuestionsData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idQuestData=saveAQSAssessmentData($a['AddAQSDataAdditionalQuestionsData'],'AddAQSDataAdditionalQuestionsData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idQuestData = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for adding additional questions data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idQuestData = 1;
                                                }
                                                if(!empty($a['AddAQSDataAdditionalMediumInstruction'])){
                                                    if (!empty($a['AddAQSDataAdditionalMediumInstruction']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idAddMedInst=saveAQSAssessmentData($a['AddAQSDataAdditionalMediumInstruction'],'AddAQSDataAdditionalMediumInstruction',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idAddMedInst = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for adding additional medium instruction data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idAddMedInst = 1;
                                                }
                                                if(!empty($a['RemoveAQSDataAdditionalRefTeam'])){
                                                    if (!empty($a['RemoveAQSDataAdditionalRefTeam']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idRemMedInst=saveAQSAssessmentData($a['RemoveAQSDataAdditionalRefTeam'],'RemoveAQSDataAdditionalRefTeam',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idRemMedInst = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for removing additional ref team on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idRemMedInst = 1;
                                                }
                                                if(!empty($a['UpdateAQSDataAdditionalQuestionsData'])){
                                                    if (!empty($a['UpdateAQSDataAdditionalQuestionsData']) && $checkInternet==1) {
                                                        // function for save AQS Assessment data on 16-03-2016 by Mohit Kumar
                                                        $idUpdateQuestion=saveAQSAssessmentData($a['UpdateAQSDataAdditionalQuestionsData'],'UpdateAQSDataAdditionalQuestionsData',
                                                                $aqsID,$clientPrincipalId,$objDB,$objDBLive,$a['action_unique_id'],$checkInternet);
                                                    } else {
                                                        $idUpdateQuestion = 0;
                                                        $errorAssessmentAQSDataMsg[]="There is an error for updating additional question data on live server or "
                                                                . "maybe there is no internet connectivity!";
                                                    }
                                                } else {
                                                    $idUpdateQuestion = 1;
                                                }
                                                
                                            } else {
                                                $errorAssessmentAQSDataUpdateMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                            }

                                        } else {
                                            $aqsID = 0;
                                            $errorAssessmentAQSDataUpdateMsg[]="There is an error for updating aqs assessor data on live server or "
                                                            . "maybe there is no internet connectivity!";
                                        }
                                        
//                                        print_r(array($aqsID,$principalUpdate,$schoolUpdate,$removeITSupport,$idSupportID,
//                                            $removeSchoolTiming,$idSchoolTiming,$removeAQSTeam,$idAQSTeam,$idAddRef,$idRemoveMedInst,
//                                            $idSchoolCom,$idSchoolComAdd,$idQuestData,$idAddMedInst,$idRemMedInst,$idUpdateQuestion));
                                        // commit and roll back the queries
                                        if($aqsID>0 && $principalUpdate>0 && $schoolUpdate>0 && $removeITSupport>0 && $idSupportID>0
                                                && $removeSchoolTiming>0 && $idSchoolTiming>0 && $removeAQSTeam>0 && $idAQSTeam>0 && checkInternet1()==1
                                                && $idAddRef>0 && $idRemoveMedInst>0 && $idSchoolCom>0 && $idSchoolComAdd>0 && $idQuestData>0
                                                && $idAddMedInst>0 && $idRemMedInst>0 && $idUpdateQuestion>0)
                                        {
                                            //$res = 6;
                                            $objDBLive->commit();
                                            $objDB->commit();
                                            $successAssessmentAQSDataUpdateMsg[]="Assessment data is added on live server successfully!";
                                        } else {
                                            $objDB->update('z_history',array('action_flag'=>0),array('action_unique_id'=>$a['action_unique_id']));
                                            $objDBLive->rollback();
                                            $objDB->rollback();
//                                            print_r(array($aqsID,$principalUpdate,$schoolUpdate,$removeITSupport,$idSupportID,$removeSchoolTiming,
//                                                    $idSchoolTiming,$removeAQSTeam,$idAQSTeam));
                                            
                                            $action_unique_id=$a['action_unique_id'];
                                            $errorAssessmentAQSDataUpdateMsg[]="There is an error while sync tha data on live server or "
                                                            . "maybe there is no internet connectivity!";
                                        } 
                                    } else {
                                        $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                        $errorAssessmentAQSDataUpdateMsg[]="There is no aqs assessor data on live server or "
                                                            . "maybe there is no internet connectivity!";
                                    }
                                } else {
                                    $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                    $errorAssessmentAQSDataUpdateMsg[]="There is no aqs json data on live server or "
                                                            . "maybe there is no internet connectivity!";
                                }
                            } else {
                                $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                $errorAssessmentAQSDataUpdateMsg[]="There is no client data for this principal '".$a['action_content']."' on live"
                                        . " server or maybe there is no internet connectivity!";
                            }

                        }
                        foreach ($successAssessmentAQSDataUpdateMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorAssessmentAQSDataUpdateMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;

                    case 'internalAssessment':
                        
                        $errorArrayInternalAssessmentMsg = array();
                        $successArrayInternalAssessmentMsg = array();
                        foreach ($value as $i => $a) {
//                            print_r($value);
//                            die;
                            if(!empty($a['internalAssessmentJudgementStatementInsert']) && checkInternet($objDB)==1){
                                $m=$a['internalAssessmentJudgementStatementInsert'][0];
                                
                                // query for getting client data from local server by Mohit Kumar
                                $Query1="Select t1.client_id as assessment_client_id,t2.email as assessment_client_principal_email,t1.aqsdata_id from "
                                    . "d_assessment t1 Left Join d_user t2 On (t1.client_id=t2.client_id) Left Join h_user_user_role t3 On "
                                    . "(t2.user_id=t3.user_id) where t1.assessment_id='".$m['action_id']."' and t3.role_id='6'";
                                $clientData = $objDB->get_row($Query1);
                                
                                if(!empty($clientData) && checkInternet($objDB)==1){
                                    // query for getting internal assessor user id from local server by Mohit
                                    $Query2="Select t1.email as assessor_email,t1.client_id as assessor_client_id,t3.role_id from d_user t1 Left Join"
                                        . " d_client t2 On (t1.client_id=t2.client_id) Left Join h_user_user_role t3 On (t1.user_id=t3.user_id) where "
                                        . "t1.user_id='".$m['action_content']."' ";
                                    $userData = $objDB->get_row($Query2);
                                    if($userData['role_id']==3){
                                        $assessmentType="Internal";
                                    } else if($userData['role_id']==4){
                                        $assessmentType="External";
                                    }
                                    
                                    if(!empty($userData) && checkInternet($objDB)==1){
                                        
                                        if($clientData['aqsdata_id']!='' && checkInternet($objDB)==1){
                                            if($clientData['assessment_client_id']!='' && $userData['assessor_client_id']!=''){
                                                //merge the internal assessor,client and history data by Mohit Kumar
                                                $m = array_merge($m,$clientData,$userData);
                                                // query for getting internal assessor user data from live server by Mohit Kumar
                                                $Query3="Select t1.user_id,t1.client_id from d_user t1 Left Join h_user_user_role t2 On "
                                                    . "(t1.user_id=t2.user_id) where t1.email='".$m['assessor_email']."' ";
                                                $userLiveData = $objDBLive->get_row($Query3);
                                                
                                                if(!empty($userLiveData) && checkInternet($objDB)==1){
                                                    // query for getting unique id from history table for getting assessment id from live server
                                                    // by Mohit Kumar
                                                    $Query4="Select action_unique_id from z_history where table_id='".$m['action_id']."'"
                                                        . " and action='createSchoolAssessment'";
                                                    $local_action_unique_id=$objDB->get_row($Query4);
                                                    if(!empty($local_action_unique_id) && checkInternet($objDB)==1){
                                                        // query for getting assessment id from live server by using local history table create assessment
                                                        // data  by Mohit Kumar
                                                        $Query5="Select table_id from z_history where action='createSchoolAssessment'"
                                                            . " and action_unique_id='".$local_action_unique_id['action_unique_id']."'";
                                                        $live_assessment_id=$objDBLive->get_row($Query5);
                                                        
                                                        
                                                    } else if(empty($local_action_unique_id) && checkInternet($objDB)==1){
                                                        $live_assessment_id=array('table_id'=>$m['action_id']);
                                                        //$errorArrayInternalAssessmentMsg[]='There is no internet connectivity or maybe sync flag is off now!';
                                                    }
                                                    
                                                    if(!empty($live_assessment_id) && checkInternet($objDB)==1){
                                                        $liveData['assessment_id']=$live_assessment_id['table_id'];
                                                        $liveData['assessor_id']=$userLiveData['user_id'];
                                                        $liveData['client_id']=$userLiveData['client_id'];

                                                        $objDBLive->start_transaction();
                                                        $objDB->start_transaction();
                                                        // call function for sync the judgement statement rating on live server by Mohit Kumar
                                                        if(checkInternet($objDB)==1 && !empty($a['internalAssessmentJudgementStatementInsert'])){
                                                            $scoreId=saveScore($objDB,$objDBLive,$a['internalAssessmentJudgementStatementInsert'],
                                                                    $liveData,'f_score','insert','isFinal');
                                                        } else {
                                                            $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                            $scoreId=0;
                                                        }                                                                        

                                                        // call function for sync the core questions rating on live server by Mohit Kumar
                                                        if(!empty($a['internalAssessmentCoreQuestionInsert'])){
                                                            if(checkInternet($objDB)==1 && !empty($a['internalAssessmentCoreQuestionInsert'])){                                                                            
                                                                $coreId=saveScore($objDB,$objDBLive,$a['internalAssessmentCoreQuestionInsert'],
                                                                        $liveData,'h_cq_score','insert');
                                                            } else {
                                                                $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                                $coreId=0;
                                                            }
                                                        } else {
                                                            $coreId=2;
                                                        }


                                                        // call function for sync the key questions rating on live server by Mohit Kumar
                                                        if(!empty($a['internalAssessmentKeyQuestionInsert'])){
                                                            if(checkInternet($objDB)==1 && !empty($a['internalAssessmentKeyQuestionInsert'])){                                                                            
                                                                $keyId=saveScore($objDB,$objDBLive,$a['internalAssessmentKeyQuestionInsert'],
                                                                        $liveData,'h_kq_instance_score','insert');
                                                            } else {
                                                                $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                                $keyId=0;
                                                            }
                                                        } else {
                                                            $keyId=2;
                                                        }

                                                        // call function for sync the kpa questions rating on live server by Mohit Kumar
                                                        if(!empty($a['internalAssessmentKpaInsert'])){
                                                            if(checkInternet($objDB)==1 && !empty($a['internalAssessmentKpaInsert'])){                                                                            
                                                                $kpaId=saveScore($objDB,$objDBLive,$a['internalAssessmentKpaInsert'],$liveData,
                                                                        'h_kpa_instance_score','insert');
                                                            } else {
                                                                $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                                $kpaId=0;
                                                            }
                                                        } else {
                                                            $kpaId=1;
                                                        }

                                                        // call function for sync the assessment % and satatus on live server by Mohit Kumar
                                                        if(!empty($a['internalAssessmentPercentageAndStatusUpdate'])){
                                                            if(checkInternet($objDB)==1 && !empty($a['internalAssessmentPercentageAndStatusUpdate'])){                                                                            
                                                                $percentageId=saveScore($objDB,$objDBLive,$a['internalAssessmentPercentageAndStatusUpdate'],
                                                                        $liveData,'h_assessment_user','update','user_id');
                                                            } else {
                                                                $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                                $percentageId=0;
                                                            }
                                                        } else {
                                                            $percentageId=1;
                                                        }

                                                        if(!empty($a['internalAssessmentScoreFileInsert'])){
                                                            if(checkInternet($objDB)==1){
                                                                $scoreFileId= saveScore($objDB, $objDBLive, $a['internalAssessmentScoreFileInsert'],
                                                                        $liveData,'h_score_file','insertScoreFile');
                                                            } else {
                                                                $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                                $scoreFileId=0;
                                                            }
                                                        } else {
                                                            $scoreFileId = 2;
                                                        }

                                                        // update assessor key notes on live server on 28-03-2016 by Mohit Kumar
                                                        if(!empty($a['internalAssessmentAssessorKeyNoteUpdate'])){
                                                            if(checkInternet($objDB)==1){
                                                                $assessorKeyNotes=  updateRating($objDB, $objDBLive, $liveData, 
                                                                $a['internalAssessmentAssessorKeyNoteUpdate'],'assessor_key_notes','updateNote');
                                                            } else {
                                                                $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                                $assessorKeyNotes=0;
                                                            }
                                                        } else {
                                                            $assessorKeyNotes=2;
                                                        }
                                                        //call function for delete the assessor key notes on 29-03-2016 by Mohit Kumar
                                                        if(!empty($a['internalAssessmentAssessorKeyNoteDelete'])){
                                                            if(checkInternet($objDB)==1){
                                                                $deleteAssessorNote = updateRating($objDB,$objDBLive,$liveData,
                                                                    $a['internalAssessmentAssessorKeyNoteDelete'],'assessor_key_notes','deleteNote');
                                                            } else {
                                                                $deleteAssessorNote = 0;
                                                            }
                                                        } else {
                                                            $deleteAssessorNote = 1;
                                                        }
                                                        if($scoreId>0 && $coreId>0 && $keyId>0 && $kpaId>0 && $percentageId>0 && $scoreFileId>0 
                                                                && checkInternet($objDB)==1 && $assessorKeyNotes>0 && $deleteAssessorNote>0)
                                                        {
                                                            $objDBLive->commit();
                                                            $objDB->commit();
                                                            $successArrayInternalAssessmentMsg[]=$assessmentType." assessment is sync to live server!";
                                                        } else {
                                                            $objDBLive->rollback();
                                                            $objDB->rollback();
//                                                                print_r(array($scoreId,$coreId,$keyId,$kpaId,$percentageId,$scoreFileId,
//                                                                    $assessorKeyNotes,checkInternet($objDB)));
                                                            $errorArrayInternalAssessmentMsg[]="There is an error while sync the "
                                                                .$assessmentType. " assessor's assessment data on live server or maybe there is no "
                                                                . "internet connectivity or maybe sync flag is off now!";                                                            
                                                        }

                                                    } else {
                                                        $errorArrayInternalAssessmentMsg[]='There is no assessment created on live'
                                                            . 'server or maybe there is no internet connectivity or maybe sync flag is off now!';
                                                    }
                                                } else {
                                                    $errorArrayInternalAssessmentMsg[]="There is no internal assessor data on live server or maybe "
                                                        . "there is no internet connectivity or maybe sync flag is off now!";
                                                }
                                            } else {
                                                $errorArrayInternalAssessmentMsg[]="This internal assessor '".$userData['assessor_email']."' "
                                                    . "is a invalid user for this assessment or maybe there is no internet connectivity or maybe sync flag is off now!";
                                            }
                                        } else {
                                            $errorArrayInternalAssessmentMsg[]="There is no aqs data on local server or maybe there is no "
                                                . "internet connectivity or maybe sync flag is off now!";
                                        }
                                    } else {
                                        $errorArrayInternalAssessmentMsg[]="There is no internal user data on local server or maybe there is no internet"
                                            . " connectivity!";
                                    }
                                } else {
                                    $errorArrayInternalAssessmentMsg[]="There is no school data on local server or maybe is no internet connectivity or maybe sync flag is off now!";
                                }
                            } else {
                                $errorArrayInternalAssessmentMsg[]="There is no data or maybe there is no internet connectivity or maybe sync flag is off now!";
                            }
                        }
                        foreach ($successArrayInternalAssessmentMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayInternalAssessmentMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;
                        
                    case 'internalAssessmentUpdate':
                        $errorArrayInternalAssessmentUpdateMsg = array();
                        $successArrayInternalAssessmentUpdateMsg = array();
                        
                        foreach ($value as $i => $a) {
                            if(!empty($a) && checkInternet($objDB)==1){
                                $key = array_keys($a);
                                $m=$a[$key[0]][0];
                                
                                //query for getting internal assessor user email id from local server on 28-03-2016 by Mohit Kumar
                                $Query1="Select t1.email,t2.role_id from d_user t1 Left Join h_user_user_role t2 On (t1.user_id=t2.user_id) "
                                    . "where t1.user_id='".$m['action_content']."' ";
                                $userLocalEmail = $objDB->get_row($Query1);
                                if($userLocalEmail['role_id']==3){
                                    $assessmentType="Internal";
                                } else if($userLocalEmail['role_id']==4){
                                    $assessmentType="External";
                                }
                                
                                if(!empty($userLocalEmail) && checkInternet($objDB)==1){
                                    //query for getting user and client id from live server on 28-03-2016 by Mohit Kumar
                                    $Query2="Select user_id,client_id from d_user where email='".$userLocalEmail['email']."'";
                                    $userLiveData = $objDBLive->get_row($Query2);
                                    
                                    if(!empty($userLiveData) && checkInternet($objDB)==1){
                                        // query for getting unique id from history table for getting assessment id from live server on 28-03-2016
                                        // by Mohit Kumar
                                        $Query4="Select action_unique_id from z_history where table_id='".$m['action_id']."'"
                                            . " and action='createSchoolAssessment'";
                                        $local_action_unique_id=$objDB->get_row($Query4);
//                                        print_r($local_action_unique_id);
//                                        die;
                                        if(!empty($local_action_unique_id) && checkInternet($objDB)==1){                                            
                                            // query for getting assessment id from live server by using local history table create assessment
                                            // on 28-03-2016 data  by Mohit Kumar
                                            $Query5="Select table_id from z_history where action='createSchoolAssessment'"
                                                . " and action_unique_id='".$local_action_unique_id['action_unique_id']."'";
                                            $live_assessment_id=$objDBLive->get_row($Query5);
                                            
                                        } else if(empty($local_action_unique_id) && checkInternet($objDB)==1){
                                            $live_assessment_id=array('table_id'=>$m['action_id']);
                                            //$errorArrayInternalAssessmentUpdateMsg[]='There is no internet connectivity or maybe sync flag is off now!';
                                        }
                                        if(!empty($live_assessment_id) && checkInternet($objDB)==1){
                                            $liveData['assessment_id']=$live_assessment_id['table_id'];
                                            $liveData['assessor_id']=$userLiveData['user_id'];
                                            $liveData['client_id']=$userLiveData['client_id'];

                                            $objDBLive->start_transaction();
                                            $objDB->start_transaction();

                                            // call function for sync the judgement statement rating on live server by Mohit Kumar
                                            if(!empty($a['internalAssessmentJudgementStatementInsert'])){
                                                if(checkInternet($objDB)==1 && !empty($a['internalAssessmentJudgementStatementInsert'])){
                                                    $scoreId1=saveScore($objDB,$objDBLive,$a['internalAssessmentJudgementStatementInsert'],
                                                            $liveData,'f_score','insert','isFinal');
                                                } else {
                                                    $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $scoreId1=0;
                                                } 
                                            } else {
                                                $scoreId1=1;
                                            }

                                            // call function for sync the core questions rating on live server by Mohit Kumar
                                            if(!empty($a['internalAssessmentCoreQuestionInsert'])){
                                                if(checkInternet($objDB)==1 && !empty($a['internalAssessmentCoreQuestionInsert'])){                                                                            
                                                    $coreId=saveScore($objDB,$objDBLive,$a['internalAssessmentCoreQuestionInsert'],
                                                            $liveData,'h_cq_score','insert');
                                                } else {
                                                    $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $coreId=0;
                                                }
                                            } else {
                                                $coreId=2;
                                            }

                                            // call function for sync the key questions rating on live server by Mohit Kumar
                                            if(!empty($a['internalAssessmentKeyQuestionInsert'])){
                                                if(checkInternet($objDB)==1 && !empty($a['internalAssessmentKeyQuestionInsert'])){                                                                            
                                                    $keyId=saveScore($objDB,$objDBLive,$a['internalAssessmentKeyQuestionInsert'],
                                                            $liveData,'h_kq_instance_score','insert');
                                                } else {
                                                    $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $keyId=0;
                                                }
                                            } else {
                                                $keyId=2;
                                            }

                                            // call function for sync the kpa questions rating on live server by Mohit Kumar
                                            if(!empty($a['internalAssessmentKpaInsert'])){
                                                if(checkInternet($objDB)==1 && !empty($a['internalAssessmentKpaInsert'])){                                                                            
                                                    $kpaId=saveScore($objDB,$objDBLive,$a['internalAssessmentKpaInsert'],$liveData,
                                                            'h_kpa_instance_score','insert');
                                                } else {
                                                    $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $kpaId=0;
                                                }
                                            } else {
                                                $kpaId=1;
                                            }

                                            //call the function for updating the judgement rating on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentJudgementStatementUpdate'])){
                                                if(checkInternet($objDB)==1){                                                    
                                                    $scoreUpdateId=updateRating($objDB,$objDBLive,$liveData,
                                                            $a['internalAssessmentJudgementStatementUpdate'],'f_score','update');
                                                } else {
                                                    $errorArrayInternalAssessmentUpdateMsg[]="There is no internet connectivity or maybe sync flag is off now1!";
                                                    $scoreUpdateId=0;
                                                }
                                            } else {
                                                $scoreUpdateId=1;
                                            }
                                            //call the function for inserting the judgement rating on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentJudgementStatementUpdateInsert'])){
                                                if(checkInternet($objDB)==1){                                                    
                                                    $scoreId=saveScore($objDB,$objDBLive,$a['internalAssessmentJudgementStatementUpdateInsert'],
                                                                        $liveData,'f_score','insert','isFinal');
                                                } else {
                                                    $errorArrayInternalAssessmentUpdateMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $scoreId=0;
                                                }
                                            } else {
                                                $scoreId=1;
                                            }
                                            //call the function for updating the core questions rating on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentCoreQuestionUpdate'])){
                                                if(checkInternet($objDB)==1){
                                                    $coreUpdateId=updateRating($objDB,$objDBLive,$liveData,
                                                            $a['internalAssessmentCoreQuestionUpdate'],'h_cq_score','update');
                                                } else {
                                                    $errorArrayInternalAssessmentUpdateMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $coreUpdateId=0;
                                                }
                                            } else {
                                                $coreUpdateId=1;
                                            }
                                            //call the function for updating the key questions rating on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentKeyQuestionUpdate'])){
                                                if(checkInternet($objDB)==1){
                                                    $keyUpdateId=updateRating($objDB,$objDBLive,$liveData,
                                                            $a['internalAssessmentKeyQuestionUpdate'],'h_kq_instance_score','update');
                                                } else {
                                                    $errorArrayInternalAssessmentUpdateMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $keyUpdateId=0;
                                                }
                                            } else {
                                                $keyUpdateId=1;
                                            }
                                            //call the function for updating the kpa rating on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentKpaUpdate'])){
                                                if(checkInternet($objDB)==1){
                                                    $kpaUpdateId=updateRating($objDB,$objDBLive,$liveData,
                                                            $a['internalAssessmentKpaUpdate'],'h_kpa_instance_score','update');
                                                } else {
                                                    $errorArrayInternalAssessmentUpdateMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $kpaUpdateId=0;
                                                }
                                            } else {
                                                $kpaUpdateId=1;
                                            }

                                            //call the function for updating assessment % and satatus rating on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentPercentageAndStatusUpdate'])){
                                                if(checkInternet($objDB)==1){                                                                            
                                                    $percentageId=saveScore($objDB,$objDBLive,$a['internalAssessmentPercentageAndStatusUpdate'],
                                                            $liveData,'h_assessment_user','update','user_id');
                                                } else {
                                                    $errorArrayInternalAssessmentUpdateMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $percentageId=0;
                                                }
                                            } else {
                                                $percentageId=1;
                                            }
                                            // call the function for saving score file id on live server on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentScoreFileInsert'])){
                                                if(checkInternet($objDB)==1){
                                                    $scoreFileId= saveScore($objDB, $objDBLive, $a['internalAssessmentScoreFileInsert'],
                                                            $liveData,'h_score_file','insertScoreFile');
                                                } else {
                                                    $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                    $scoreFileId=0;
                                                }
                                            } else {
                                                $scoreFileId = 1;
                                            }

                                            // call function for deleting core question rating on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentCoreQuestionDelete'])){
                                                if(checkInternet($objDB)==1){
                                                    $deleteCoreQuestion=  updateRating($objDB,$objDBLive,$liveData,
                                                            $a['internalAssessmentCoreQuestionDelete'],'h_cq_score','delete');
                                                } else {
                                                    $deleteCoreQuestion = 0;
                                                    $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                }
                                            } else {
                                                $deleteCoreQuestion = 1;
                                            }
                                            // call function for deleting key question rating on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentKeyQuestionDelete'])){
                                                if(checkInternet($objDB)==1){
                                                    $deleteKeyQuestion=  updateRating($objDB,$objDBLive,$liveData,
                                                            $a['internalAssessmentKeyQuestionDelete'],'h_kq_instance_score','delete');
                                                } else {
                                                    $deleteKeyQuestion = 0;
                                                    $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                }
                                            } else {
                                                $deleteKeyQuestion = 1;
                                            }
                                            // call function for deleting kpa rating on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentKpaDelete'])){
                                                if(checkInternet($objDB)==1){
                                                    $deleteKpaQuestion=  updateRating($objDB,$objDBLive,$liveData,
                                                            $a['internalAssessmentKpaDelete'],'h_kpa_instance_score','delete');
                                                } else {
                                                    $deleteKpaQuestion = 0;
                                                    $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                }
                                            } else {
                                                $deleteKpaQuestion = 1;
                                            }

                                            // call function for updating the assessor key notes on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentAssessorKeyNoteUpdate'])){
                                                if(checkInternet($objDB)==1){
                                                    $updateAssessorNotes=updateRating($objDB,$objDBLive,$liveData,
                                                        $a['internalAssessmentAssessorKeyNoteUpdate'],'assessor_key_notes','updateNote');
                                                } else {
                                                    $updateAssessorNotes = 0;
                                                    $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                }
                                            } else {
                                                $updateAssessorNotes = 1;
                                            }

                                            // call function for updating the assessor key notes on 28-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentDAssessmentStatusUpdate'])){
                                                if(checkInternet($objDB)==1){
                                                    $updateAssessmentStatement=updateRating($objDB,$objDBLive,$liveData,
                                                        $a['internalAssessmentDAssessmentStatusUpdate'],'d_assessment','update');
                                                } else {
                                                    $updateAssessmentStatement = 0;
                                                    $errorArrayInternalAssessmentMsg[]="There is no internet connectivity or maybe sync flag is off now!";
                                                }
                                            } else {
                                                $updateAssessmentStatement = 1;
                                            }

                                            //call function for delete the assessor key notes on 29-03-2016 by Mohit Kumar
                                            if(!empty($a['internalAssessmentAssessorKeyNoteDelete'])){
                                                if(checkInternet($objDB)==1){
                                                    $deleteAssessorNote = updateRating($objDB,$objDBLive,$liveData,
                                                        $a['internalAssessmentAssessorKeyNoteDelete'],'assessor_key_notes','deleteNote');
                                                } else {
                                                    $deleteAssessorNote = 0;
                                                }
                                            } else {
                                                $deleteAssessorNote = 1;
                                            }



                                            if($scoreUpdateId>0 && $scoreId>0 && $coreUpdateId>0 && $keyUpdateId>0 && $kpaUpdateId>0 && 
                                                checkInternet($objDB)==1 && $percentageId>0 && $scoreFileId>0 && $deleteKeyQuestion>0 && 
                                                $deleteKpaQuestion>0 && $updateAssessorNotes>0 && $updateAssessmentStatement>0 && 
                                                $deleteAssessorNote>0 && $deleteCoreQuestion>0 && $scoreId1>0 && $coreId>0 && $keyId>0 && $kpaId>0)
                                            {
                                                $objDBLive->commit();
                                                $objDB->commit();
                                                $successArrayInternalAssessmentUpdateMsg[]=$assessmentType." assessment is sync to live server!";
                                            } else {
                                                $objDBLive->rollback();
                                                $objDB->rollback();
//                                                    print_r(array($scoreUpdateId,$scoreId,$coreUpdateId,$keyUpdateId,$kpaUpdateId,$percentageId,
//                                                        $scoreFileId,$deleteKeyQuestion,$deleteKpaQuestion,$updateAssessorNotes,
//                                                        $updateAssessmentStatement,$deleteAssessorNote,$scoreId1,$coreId,$keyId,$kpaId));
                                                $errorArrayInternalAssessmentUpdateMsg[]="There is an error while sync the ".$assessmentType
                                                    . " assessor's assessment data on live server or maybe there is no "
                                                    . "internet connectivity or maybe sync flag is off now!";
                                            }
                                        } else {
                                            $errorArrayInternalAssessmentUpdateMsg[]='There is no assessment created on live server or maybe '
                                                . 'there is no internet connectivity or maybe sync flag is off now!';
                                        }
                                    } else {
                                        $errorArrayInternalAssessmentUpdateMsg[]="There is no data for user or maybe there is no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                    $errorArrayInternalAssessmentUpdateMsg[]="There is no data for user or maybe there is no internet connectivity or maybe sync flag is off now!";
                                }
                                
                            } else {
                                $errorArrayInternalAssessmentUpdateMsg[]="There is no data for updation or maybe there is no internet connectivity or maybe sync flag is off now!";
                            }
                            
                        }
                        foreach ($successArrayInternalAssessmentUpdateMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayInternalAssessmentUpdateMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;
                        
                    case 'publishReportInsert':
                        $errorArrayInsertPublishReportMsg = array();
                        $successArrayInsertPublishReportMsg = array();
                        foreach ($value as $i => $a) {
                            $key = array_keys($a);
                            $m=$a[$key[0]][0];
                            $SQL1="Select action_unique_id from z_history where table_id='".$m['action_id']."' and action='createSchoolAssessment'";
                            $local_action_unique_id=$objDB->get_row($SQL1);
                            if(!empty($local_action_unique_id) && checkInternet($objDB)==1){
                                $SQL2="Select table_id from z_history where action_unique_id='".$local_action_unique_id['action_unique_id']."' "
                                    . " and action='createSchoolAssessment'";
                                $assessmentId=$objDBLive->get_row($SQL2);
                                
                            } else if(empty($local_action_unique_id) && checkInternet($objDB)==1){
                                $assessmentId=array('table_id'=>$m['action_id']);
                                //$errorArrayInsertPublishReportMsg[]="There is no report to publish or there is no internet connectivity or maybe sync flag is off now!";
                            }
                            if(!empty($assessmentId) && checkInternet($objDB)==1){
                                    $objDBLive->start_transaction();
                                    $objDB->start_transaction();
                                    if(!empty($a['publishReportInsert']) && checkInternet($objDB)==1){
                                        $publishId=array();
                                        foreach ($a['publishReportInsert'] as $j => $b) {
                                            $actionJson=  json_decode($b['action_json'],true);
                                            $actionJson['assessment_id']=$assessmentId['table_id'];
                                            if($objDBLive->insert("h_assessment_report",$actionJson) && checkInternet($objDB)==1){
                                                $pid=$objDBLive->get_last_insert_id();
                                                $publishId[]=$pid;
                                                $objDBLive->saveHistoryData($pid,$b['table_name'],$b['action_unique_id'],$b['action'],
                                                    $actionJson['assessment_id'],$actionJson['report_id'],$b['action_json'],1,date('Y-m-d H:i:s'));
                                                $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$b['action_unique_id'],
                                                    'action'=>$b['action'],'id'=>$b['id']));
                                            }
                                        }
                                        if(count($publishId)==count($a['publishReportInsert']) && checkInternet($objDB)==1){
                                            $publishReportStatus=1;
                                        } else {
                                            $publishReportStatus=0;
                                        }
                                    } else {
                                        $publishReportStatus=0;
                                        $errorArrayInsertPublishReportMsg[]="There is no report to publish or there is no internet connectivity or maybe sync flag is off now!";
                                    }
                                    if(!empty($a['publishReportAssessorKeyNotesStatusUpdate']) && checkInternet($objDB)==1){
                                        $keyNotesStatus=array();
                                        foreach ($a['publishReportAssessorKeyNotesStatusUpdate'] as $j => $b) {
                                            $actionJson=  json_decode($b['action_json'],true);
                                            if($objDBLive->update("d_assessment",$actionJson,array('assessment_id'=>$assessmentId['table_id'])) 
                                                    && checkInternet($objDB)==1){
                                                $pid=$assessmentId['table_id'];
                                                $keyNotesStatus[]=$pid;
                                                $objDBLive->saveHistoryData($pid,$b['table_name'],$b['action_unique_id'],$b['action'],
                                                    $assessmentId['table_id'],$actionJson['isAssessorKeyNotesApproved'],$b['action_json'],1,
                                                    date('Y-m-d H:i:s'));
                                                $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$b['action_unique_id'],
                                                    'action'=>$b['action'],'id'=>$b['id']));
                                            }
                                        }
                                        if(count($keyNotesStatus)==count($a['publishReportAssessorKeyNotesStatusUpdate']) && checkInternet($objDB)==1){
                                            $keyNoteStatus=1;
                                        } else {
                                            $keyNoteStatus=0;
                                        }
                                    } else {
                                        $keyNoteStatus=0;
                                        $errorArrayInsertPublishReportMsg[]="There is no report to publish or there is no internet connectivity or maybe sync flag is off now!";
                                    }
                                    if ($keyNoteStatus > 0 && $publishReportStatus > 0 && checkInternet($objDB)==1) {
                                        $objDBLive->commit();
                                        $objDB->commit();
                                        $successArrayInsertPublishReportMsg[]="Report is published now!";
                                    } else {
                                        $objDB->update('z_history', array('action_flag' => 0), array('action_unique_id' => $a['action_unique_id']));
                                        $objDBLive->rollback();
                                        $objDB->rollback();
                                        $action_unique_id=$a['action_unique_id'];
                                        $errorArrayInsertPublishReportMsg[]="There is an error while sync the publish report data on live server or"
                                                . " maybe there is no internet connectivity or maybe sync flag is off now!";
                                    }
                                } else {
                                $errorArrayInsertPublishReportMsg[]="There is no report to publish or there is no internet connectivity or maybe sync flag is off now!";
                            }
                        }
                        foreach ($successArrayInsertPublishReportMsg as $value) {
                            echo "<b>".$value."</b><br/>";
                        }
                        foreach ($errorArrayInsertPublishReportMsg as $value) {
                            echo $value."<br/>";
                        }
                        if((empty(checkHistoryData($objDB)) && checkInternet($objDB)==1 && checkInternet1()==1) || checkInternet1()==0)
                        {
                            $id=getSyncStatusId($objDB);
                            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                        }
                        break;
                }
            } else {
                $id=getSyncStatusId($objDB);
                $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
                $error = 'no internet';
                break;
            }
        }
        if ($error == 'no internet'){
            $id=getSyncStatusId($objDB);
            $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
            echo 'there is no internet connectivity!';
        } 
    } else {
        $id=getSyncStatusId($objDB);
        $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
        echo 'There is no updated records in local server!';
    }
//print_r($finalSet);
} else {
    $endTime=endTimeByID($objDB);
    if($endTime['end_time']=='' && (checkInternet($objDB)!=1 || checkInternet($objDB)==3)){
        $id=getSyncStatusId($objDB);
        $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
    }
    
    if($endTime['end_time']=='' && (checkInternet($objDB)==1) ){
        $id=getSyncStatusId($objDB);
        $objDB->update('z_sync_status',array('sync_status'=>1,'end_time'=>date('Y-m-d H:i:s')),array('id'=>$id['id']));
    }
    echo 'there is no internet connectivity!';
}

// function for managing the aqs data on 16-03-2016 by Mohit Kumar
function assessmentArray($AQSData,$type){
    $updateAQSAssessorData = array();
    $updateAQSUserData = array();
    $updateAQSSchoolData = array();
    $RemoveAQSITSupportData = array();
    $AddAQSITSupportData = array();
    $RemoveAQSSchoolTimingData = array();
    $AddAQSSchoolTimingData = array();
    $RemoveAQSAQSTeamData = array();
    $AddAQSAQSTeamData = array();
    foreach ($AQSData as $i => $a) {
        if($type=='insert'){
            if ($a['action'] == 'assessmentAQSDataAssessmentUpdate') {
                $updateAQSAssessorData['UpdateAQSAssessorData'][] = $a;
            }
        }
        if ($a['action'] == 'assessmentAQSDataUserUpdate') {
            $updateAQSUserData['UpdateAQSUserData'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataSchoolUpdate') {
            $updateAQSSchoolData['UpdateAQSSchoolData'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataITSupportRemove') {
            $RemoveAQSITSupportData['RemoveAQSITSupportData'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataITSupportAdd') {
            $AddAQSITSupportData['AddAQSITSupportData'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataSchoolTimingRemove') {
            $RemoveAQSSchoolTimingData['RemoveAQSSchoolTimingData'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataSchoolTimingAdd') {
            $AddAQSSchoolTimingData['AddAQSSchoolTimingData'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataAQSTeamRemove') {
            $RemoveAQSAQSTeamData['RemoveAQSAQSTeamData'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataAQSTeamAdd') {
            $AddAQSAQSTeamData['AddAQSAQSTeamData'][] = $a;
        }
        
        if ($a['action'] == 'assessmentAQSDataAdditionalRefTeamRemove') {
            $AddAQSSchoolTimingData['RemoveAQSDataAdditionalRefTeam'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataAdditionalRefTeamAdd') {
            $RemoveAQSAQSTeamData['AddAQSDataAdditionalRefTeam'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataAdditionalSchoolCommunityRemove') {
            $AddAQSAQSTeamData['RemoveAQSDataAdditionalSchoolCommunity'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataAdditionalMediumInstructionAdd') {
            $AddAQSSchoolTimingData['AddAQSDataAdditionalMediumInstruction'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataAdditionalMediumInstructionRemove') {
            $RemoveAQSAQSTeamData['RemoveAQSDataAdditionalMediumInstruction'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataAdditionalQuestionsDataAdd') {
            $AddAQSAQSTeamData['AddAQSDataAdditionalQuestionsData'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataAdditionalSchoolCommunityAdd') {
            $AddAQSAQSTeamData['AddAQSDataAdditionalSchoolCommunity'][] = $a;
        }
        if ($a['action'] == 'assessmentAQSDataAdditionalQuestionsDataUpdate') {
            $AddAQSAQSTeamData['UpdateAQSDataAdditionalQuestionsData'][] = $a;
        }
    }
    $array = array_merge($updateAQSAssessorData, $updateAQSUserData, $updateAQSSchoolData,
            $RemoveAQSITSupportData, $AddAQSITSupportData, $RemoveAQSSchoolTimingData, $AddAQSSchoolTimingData, 
            $RemoveAQSAQSTeamData, $AddAQSAQSTeamData);
    return $array;
}

// function for saving the aqs all data into live server on 16-03-2016 by Mohit Kumar'
function saveAQSAssessmentData($assessData,$arrayType,$aqsID,$clientPrincipalId,$objDB,$objDBLive,$action_unique_id,$checkInternet){
    
    if($arrayType=='UpdateAQSAssessorData'){
        //get the assessment id from live server on 14-03-2016 by Mohit Kumar
        $updateA = array();
        foreach ($assessData as $j => $b) {
            // get action unique id from local history table for getting live unique id
            $SQL3 = "Select action_unique_id from z_history where table_name='d_assessment' and table_id='" . $b['action_id'] . "' "
                    . "and action='createSchoolAssessment'";
            $assesment_unique_id = $objDB->get_row($SQL3);
            if (!empty($assesment_unique_id) && $checkInternet==1) {
                // get the assessment id from history table with the relation on d_assessment table
                $SQL4 = "Select t1.table_id as assessment_id from z_history t1 Left Join d_assessment t2 On t1.table_id=t2.assessment_id "
                        . "where t1.action_flag='1' and t2.client_id='" . $clientPrincipalId['client_id'] . "' and "
                        . "action_unique_id='" . $assesment_unique_id['action_unique_id'] . "'";
                $live_assessment_id = $objDBLive->get_row($SQL4);
                if (!empty($live_assessment_id) && ($objDBLive->get_row($SQL4))!=NULL && $checkInternet==1) {
                    // update aqs data is on assessment table on 14-03-2016 by Mohit Kumar
                    if ($objDBLive->update('d_assessment', array('aqsdata_id' => $aqsID),
                            array('assessment_id' => $live_assessment_id['assessment_id'])) && $checkInternet==1) {
                        $aqsID = $aqsID;
                        $updateA[] = $aqsID;
                        // save history data on live server on 14-03-2016 by Mohit Kumar
                        $objDBLive->saveHistoryData($aqsID, 'd_assessment', $action_unique_id, 'assessmentAQSDataAssessmentUpdate', 
                                $live_assessment_id['assessment_id'],$aqsID, $b['action_json'], 1, date('Y-m-d H:i:s'));
                        //update history flag  on local server on 14-03-2016 by Mohit Kumar
                        $objDB->update('z_history', array('action_flag' => 1),array('action_unique_id' => $action_unique_id,
                            'id' => $b['id'], 'action' => 'assessmentAQSDataAssessmentUpdate'));
                    }
                }
            } else if(empty ($assesment_unique_id) && $checkInternet==1){
                
                $live_assessment_id = array('assessment_id'=>$b['action_id']);
                if (!empty($live_assessment_id) && $checkInternet==1) {
                    // update aqs data is on assessment table on 14-03-2016 by Mohit Kumar
                    if ($objDBLive->update('d_assessment', array('aqsdata_id' => $aqsID),
                            array('assessment_id' => $live_assessment_id['assessment_id'])) && $checkInternet==1) {
                        $aqsID = $aqsID;
                        $updateA[] = $aqsID;
                        // save history data on live server on 14-03-2016 by Mohit Kumar
                        $objDBLive->saveHistoryData($aqsID, 'd_assessment', $action_unique_id, 'assessmentAQSDataAssessmentUpdate', 
                                $live_assessment_id['assessment_id'],$aqsID, $b['action_json'], 1, date('Y-m-d H:i:s'));
                        //update history flag  on local server on 14-03-2016 by Mohit Kumar
                        $objDB->update('z_history', array('action_flag' => 1),array('action_unique_id' => $action_unique_id,
                            'id' => $b['id'], 'action' => 'assessmentAQSDataAssessmentUpdate'));
                    }
                }
            }
        }
        if(count($updateA)==count($assessData) && $checkInternet==1){
            
            $returnValue = 1;
        } else {
            
            $returnValue = 0;
        }
    } else if($arrayType=='UpdateAQSUserData'){
        // update principle user information on d_user table on 14-03-2016 by Mohit Kumar
        $b = $assessData[0];
        $SQL5 = "Select user_id from d_user where email='" . $b['action_content'] . "'";
        $principalUserId = $objDBLive->get_row($SQL5);
        if (!empty($principalUserId) && ($objDBLive->get_row($SQL5))!=NULL && $checkInternet==1) {
            $principalName = json_decode($b['action_json'], true);
            // update principal name on d_user table on 14-03-2016 by Mohit Kumar
            if($checkInternet==1){
                $objDBLive->update('d_user', array('name' => $principalName['principal_name']), 
                    array('email' => $b['action_content'], 'user_id' => $principalUserId['user_id']));
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($principalUserId['user_id'], 'd_user', $action_unique_id,'assessmentAQSDataUserUpdate', 
                            $principalUserId['user_id'], $b['action_content'],$b['action_json'], 1, date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1),
                            array('action_unique_id' => $action_unique_id, 'id' => $b['id'],'action' => 'assessmentAQSDataUserUpdate'));
                    $returnValue = 1;
            } else {
                $returnValue = 0;
            }            
        } else {
            $returnValue = 0;
        }
    } else if($arrayType=='UpdateAQSSchoolData'){
        // update school data on live server on 14-03-2016 by Mohit Kumar
        $b = $assessData[0];
        $SQL6 = "Select t1.client_id from d_user t1 LEFT JOIN h_user_user_role t2 on (t1.user_id=t2.user_id) where t1.email='".$b['action_content']."'";
        $schoolId = $objDBLive->get_row($SQL6);
        if (!empty($schoolId) && ($objDBLive->get_row($SQL6))!=NULL && $checkInternet==1) {
            $schoolData = json_decode($b['action_json'], true);
            // update principal name on d_user table on 14-03-2016 by Mohit Kumar
            if($checkInternet==1){
                $objDBLive->update('d_client', array('client_name' => $schoolData['school_name']),array('client_id' => $schoolId['client_id']));
                // save history on live server on 14-03-2016 by Mohit Kumar
                $objDBLive->saveHistoryData($schoolId['client_id'], 'd_client', $action_unique_id,'assessmentAQSDataSchoolUpdate', 
                        $schoolId['client_id'], $b['action_content'],$b['action_json'], 1, date('Y-m-d H:i:s'));
                // update status on history table on local server on 14-03-2016 by Mohit Kumar
                $objDB->update('z_history', array('action_flag' => 1), 
                        array('action_unique_id' => $action_unique_id, 'id' => $b['id'],'action' => 'assessmentAQSDataSchoolUpdate'));
                $returnValue = 1;
            } else {
                $returnValue = 0;
            }            
        } else {
            $returnValue = 0;
        }
    } else if($arrayType=='RemoveAQSITSupportData'){
        // remove IT support data from h_aqsdata_itsupport on live server on 14-03-2016 by Mohit Kumar
        $b = $assessData[0];
        if ($objDBLive->delete('h_aqsdata_itsupport', array('aqs_id' => $aqsID)) && $checkInternet==1) {
            
            // save history on live server on 14-03-2016 by Mohit Kumar
            if($checkInternet==1){
                $returnValue = 1;
                $objDBLive->saveHistoryData($aqsID, 'h_aqsdata_itsupport', $action_unique_id,'assessmentAQSDataITSupportRemove', 
                    $aqsID, $aqsID, $b['action_json'], 1,date('Y-m-d H:i:s'));
                // update status on history table on local server on 14-03-2016 by Mohit Kumar
                $objDB->update('z_history', array('action_flag' => 1), 
                        array('action_unique_id' => $action_unique_id, 'id' => $b['id'],'action' => 'assessmentAQSDataITSupportRemove'));
            } else {
                $returnValue = 0; 
            }           
        } else {
            $returnValue = 0;
        }    
    } else if($arrayType=='AddAQSITSupportData'){
        // add IT support data from h_aqsdata_itsupport on live server on 14-03-2016 by Mohit Kumar
        $ids = array();
        foreach ($assessData as $k => $c) {
            $itSupportData = json_decode($c['action_json'], true);
            if ($objDBLive->insert('h_aqsdata_itsupport', array('aqs_id' => $aqsID,'itsupport_id' => $itSupportData['itsupport_id']))
                    && $checkInternet==1) {
                // get last insert id for h_aqsdata_itsupport table on 14-03-2016 by Mohit Kumar
                if($checkInternet==1){
                    $idSupportID = $objDBLive->get_last_insert_id();
                    $ids[] = $idSupportID;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($idSupportID, 'h_aqsdata_itsupport', $action_unique_id,'assessmentAQSDataITSupportAdd', 
                            $idSupportID, $aqsID, $c['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $c['id'],'action' => 'assessmentAQSDataITSupportAdd'));
                }                
            }                                                    
        }
        if (count($ids) == count($assessData) && $checkInternet==1) {
            $returnValue = 1;
        } else {
            $returnValue = 0;
        }
    } else if($arrayType=='RemoveAQSSchoolTimingData'){
        // remove aqs school timing from h_AQS_school_level table on 15-03-2016 by Mohit Kumar
        $b = $assessData[0];
//        $SQL_Query = "Select id from h_AQS_school_level where AQS_data_id='".$aqsID."'";
//        $id = $objDBLive->get_row($SQL_Query);
//        print_r($id);
//        die;
        if ($objDBLive->delete('h_AQS_school_level', array('AQS_data_id' => $aqsID)) && $checkInternet==1) {
            if($checkInternet==1){
                $returnValue = 1;
                // save history on live server on 14-03-2016 by Mohit Kumar
                $objDBLive->saveHistoryData($aqsID, 'h_AQS_school_level', $action_unique_id,'assessmentAQSDataSchoolTimingRemove', $aqsID, 
                        $aqsID, $b['action_json'], 1,date('Y-m-d H:i:s'));
                // update status on history table on local server on 14-03-2016 by Mohit Kumar
                $objDB->update('z_history', array('action_flag' => 1), 
                        array('action_unique_id' => $action_unique_id, 'id' => $b['id'],'action' => 'assessmentAQSDataSchoolTimingRemove'));
            } else {
                $returnValue = 0;
            }            
        } else {
            $returnValue = 0;
        } 
    } else if($arrayType=='AddAQSSchoolTimingData'){
        // add aqs school timing in h_AQS_school_level table on 15-03-2016 by Mohit Kumar
        $idst = array();
        foreach ($assessData as $k => $c) {
            $schoolTiming = json_decode($c['action_json'], true);
            if ($objDBLive->insert('h_AQS_school_level', array('AQS_data_id' => $aqsID,'school_level_id' => $schoolTiming['school_level_id'],
                        'start_time'=>$schoolTiming['start_time'],'end_time'=>$schoolTiming['end_time'])) && $checkInternet==1) 
            {
                // get last insert id for h_aqsdata_itsupport table on 14-03-2016 by Mohit Kumar
                if($checkInternet==1){
                    $idSchoolTiming = $objDBLive->get_last_insert_id();
                    $idst[] = $idSchoolTiming;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($idSchoolTiming, 'h_AQS_school_level', $action_unique_id,'assessmentAQSDataSchoolTimingAdd', 
                            $idSchoolTiming, $aqsID, $c['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $c['id'],'action' => 'assessmentAQSDataSchoolTimingAdd'));
                }                
            }                                                    
        }
        if (count($idst) == count($assessData) && $checkInternet==1) {
            $returnValue = 1;
        } else {
            $returnValue = 0;
        } 
    } else if($arrayType=='AddAQSAQSTeamData'){
        // add aqs school timing in h_AQS_school_level table on 15-03-2016 by Mohit Kumar
        $idt = array();
        foreach ($assessData as $k => $c) {
            $json = json_decode($c['action_json'], true);
            $json['AQS_data_id']=$aqsID;
            if ($objDBLive->insert('d_AQS_team', $json)
                    && $checkInternet==1) 
            {
                // get last insert id for h_aqsdata_itsupport table on 14-03-2016 by Mohit Kumar
                if($checkInternet==1){
                    $idAQSTeam = $objDBLive->get_last_insert_id();
                    $idt[] = $idAQSTeam;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($idAQSTeam, $c['table_name'], $action_unique_id,$c['action'], $idAQSTeam,
                            $aqsID, $c['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $c['id'],'action' => $c['action']));
                }
            }
        }
        if (count($idt) == count($assessData) && $checkInternet==1) {
            $returnValue = 1;
        } else {
            $returnValue = 0;
        } 
        
    } else if($arrayType=='RemoveAQSAQSTeamData'){
        // remove aqs team from d_AQS_team table on 15-03-2016 by Mohit Kumar
        $removeTeam=array();
        foreach ($assessData as $j => $b) {
            //$aqsID
            $aqsTeam = json_decode($b['action_json'], true);
            $condition = array('AQS_data_id' => $aqsID);
            $condition['isInternal'] = $aqsTeam['isInternal'] > 0 ? 1 : 2;
            if ($objDBLive->delete('d_AQS_team', $condition) && $checkInternet==1) {
                $returnValue = 1;
                if($checkInternet==1){
                    $removeTeam[] = $aqsID;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($aqsID, 'd_AQS_team', $action_unique_id,'assessmentAQSDataAQSTeamRemove', $aqsID, 
                            $aqsID, $b['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $b['id'],'action' => 'assessmentAQSDataAQSTeamRemove'));
                } 
            }
        }
        if(count($removeTeam)==count($assessData) && $checkInternet==1){
            $returnValue=1;
        } else {
            $returnValue=0;
        }
        
    } else if($arrayType=='AddAQSDataAdditionalRefTeam'){
        // add aqs team in d_AQS_team table on 15-03-2016 by Mohit Kumar
        $idt = array();
        
        foreach ($assessData as $k => $c) {
            $json = json_decode($c['action_json'], true);
            $json['aqsdata_id']=$aqsID;
            if ($objDBLive->insert($c['table_name'], $json)
                    && $checkInternet==1) 
            {
                
                // get last insert id for h_aqsdata_itsupport table on 14-03-2016 by Mohit Kumar
                if($checkInternet==1){
                    $idAQSTeam = $objDBLive->get_last_insert_id();
                    $idt[] = $idAQSTeam;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($idAQSTeam, $c['table_name'], $action_unique_id,$c['action'], $idAQSTeam,
                            $aqsID, $c['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $c['id'],'action' => $c['action']));
                }
            }
        }
        if (count($idt) == count($assessData) && $checkInternet==1) {
            $returnValue = 1;
        } else {
            $returnValue = 0;
        } 
    } else if($arrayType=='RemoveAQSDataAdditionalRefTeam'){
        // add aqs team in d_AQS_team table on 15-03-2016 by Mohit Kumar
        $idt = array();
        foreach ($assessData as $k => $c) {
            $json = json_decode($c['action_json'], true);
            $json['aqsdata_id']=$aqsID;
            if ($objDBLive->delete($c['table_name'], $json) && $checkInternet==1) 
            {
                // get last insert id for h_aqsdata_itsupport table on 14-03-2016 by Mohit Kumar
                if($checkInternet==1){
                    $idt[] = $aqsID;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($aqsID, $c['table_name'], $action_unique_id,$c['action'], $aqsID,
                            $aqsID, $c['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $c['id'],'action' => $c['action']));
                }
            }
        }
        if (count($idt) == count($assessData) && $checkInternet==1) {
            $returnValue = 1;
        } else {
            $returnValue = 0;
        } 
    } else if($arrayType=='AddAQSDataAdditionalMediumInstruction'){
        // add aqs team in d_AQS_team table on 15-03-2016 by Mohit Kumar
        $idt = array();
        foreach ($assessData as $k => $c) {
            $json = json_decode($c['action_json'], true);
            $json['aqsdata_id']=$aqsID;
            if ($objDBLive->insert($c['table_name'], $json)
                    && $checkInternet==1) 
            {
                // get last insert id for h_aqsdata_itsupport table on 14-03-2016 by Mohit Kumar
                if($checkInternet==1){
                    $idAQSTeam = $objDBLive->get_last_insert_id();
                    $idt[] = $idAQSTeam;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($idAQSTeam, $c['table_name'], $action_unique_id,$c['action'], $idAQSTeam,
                            $aqsID, $c['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $c['id'],'action' => $c['action']));
                }
            }
        }
        if (count($idt) == count($assessData) && $checkInternet==1) {
            $returnValue = 1;
        } else {
            $returnValue = 0;
        } 
    } else if($arrayType=='RemoveAQSDataAdditionalMediumInstruction'){
        // add aqs team in d_AQS_team table on 15-03-2016 by Mohit Kumar
        $idt = array();
        foreach ($assessData as $k => $c) {
            $json = json_decode($c['action_json'], true);
            $json['aqsdata_id']=$aqsID;
            if ($objDBLive->delete($c['table_name'], $json)
                    && $checkInternet==1) 
            {
                // get last insert id for h_aqsdata_itsupport table on 14-03-2016 by Mohit Kumar
                if($checkInternet==1){
                    $idt[] = $aqsID;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($aqsID, $c['table_name'], $action_unique_id,$c['action'], $aqsID,
                            $aqsID, $c['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $c['id'],'action' => $c['action']));
                }
            }
        }
        if (count($idt) == count($assessData) && $checkInternet==1) {
            $returnValue = 1;
        } else {
            $returnValue = 0;
        } 
    } else if($arrayType=='RemoveAQSDataAdditionalSchoolCommunity'){
        // add aqs team in d_AQS_team table on 15-03-2016 by Mohit Kumar
        $idt = array();
        foreach ($assessData as $k => $c) {
            $aqsTeam = json_decode($c['action_json'], true);
            $aqsTeam['aqsdata_id']=$aqsID;
            if ($objDBLive->insert($c['table_name'], $aqsTeam)
                    && $checkInternet==1) 
            {
                // get last insert id for h_aqsdata_itsupport table on 14-03-2016 by Mohit Kumar
                if($checkInternet==1){
                    $idt[] = $aqsID;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($aqsID, $c['table_name'], $action_unique_id,$c['action'], $aqsID,
                            $aqsID, $c['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $c['id'],'action' => $c['action']));
                }
            }
        }
        if (count($idt) == count($assessData) && $checkInternet==1) {
            $returnValue = 1;
        } else {
            $returnValue = 0;
        } 
    } else if($arrayType=='AddAQSDataAdditionalSchoolCommunity'){
        // add aqs team in d_AQS_team table on 15-03-2016 by Mohit Kumar
        $idt = array();
        foreach ($assessData as $k => $c) {
            $aqsTeam = json_decode($c['action_json'], true);
            $aqsTeam['aqsdata_id']=$aqsID;
            if ($objDBLive->insert($c['table_name'], $aqsTeam)
                    && $checkInternet==1) 
            {
                // get last insert id for h_aqsdata_itsupport table on 14-03-2016 by Mohit Kumar
                if($checkInternet==1){
                    $idAQSTeam = $objDBLive->get_last_insert_id();
                    $idt[] = $idAQSTeam;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($idAQSTeam, $c['table_name'], $action_unique_id,$c['action'], $idAQSTeam,
                            $aqsID, $c['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $c['id'],'action' => $c['action']));
                }
            }
        }
        if (count($idt) == count($assessData) && $checkInternet==1) {
            $returnValue = 1;
        } else {
            $returnValue = 0;
        } 
    } else if($arrayType=='AddAQSDataAdditionalQuestionsData'){
        // add aqs team in d_AQS_team table on 15-03-2016 by Mohit Kumar
        $idt = array();
        foreach ($assessData as $k => $c) {
            $aqsTeam = json_decode($c['action_json'], true);
            $aqsTeam['aqs_data_id']=$aqsID;
            if ($objDBLive->insert($c['table_name'], $aqsTeam)
                    && $checkInternet==1) 
            {
                // get last insert id for h_aqsdata_itsupport table on 14-03-2016 by Mohit Kumar
                if($checkInternet==1){
                    $idAQSTeam = $objDBLive->get_last_insert_id();
                    $idt[] = $idAQSTeam;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($idAQSTeam, $c['table_name'], $action_unique_id,$c['action'], $idAQSTeam,
                            $aqsID, $c['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $c['id'],'action' => $c['action']));
                }
            }
        }
        if (count($idt) == count($assessData) && $checkInternet==1) {
            $returnValue = 1;
        } else {
            $returnValue = 0;
        } 
    } else if($arrayType=='UpdateAQSDataAdditionalQuestionsData'){
        // add aqs team in d_AQS_team table on 15-03-2016 by Mohit Kumar
        $idt = array();
        foreach ($assessData as $k => $c) {
            $aqsTeam = json_decode($c['action_json'], true);
            $aqsTeam['aqs_data_id']=$aqsID;
            if ($objDBLive->update($c['table_name'], $aqsTeam,array("aqs_data_id"=>$aqsID))
                    && $checkInternet==1) 
            {
                // get last insert id for h_aqsdata_itsupport table on 14-03-2016 by Mohit Kumar
                if($checkInternet==1){
                    $idAQSTeam = $aqsID;
                    $idt[] = $idAQSTeam;
                    // save history on live server on 14-03-2016 by Mohit Kumar
                    $objDBLive->saveHistoryData($aqsID, $c['table_name'], $action_unique_id,$c['action'], $aqsID,
                            $aqsID, $c['action_json'], 1,date('Y-m-d H:i:s'));
                    // update status on history table on local server on 14-03-2016 by Mohit Kumar
                    $objDB->update('z_history', array('action_flag' => 1), 
                            array('action_unique_id' => $action_unique_id, 'id' => $c['id'],'action' => $c['action']));
                }
            }
        }
        if (count($idt) == count($assessData) && $checkInternet==1) {
            $returnValue = 1;
        } else {
            $returnValue = 0;
        } 
    } else {
        $returnValue = 0;
    }
    return $returnValue;
}

// function for save rating score into live server on 23-03-2016
function saveScore($objDB,$objDBLive,$b,$liveData,$table,$type,$isFinal=''){
    $arrayScoreId = array(); 
    $score_id='';
    foreach ($b as $k => $c) {
        
        $actionJson = json_decode($c['action_json'], true);
        if($isFinal=='isFinal'){
            if(array_key_exists("isFinal",$actionJson) || array_key_exists("0",$actionJson)){
                unset($actionJson[0]);
                $actionJson['isFinal']="1";                
            } else {
                $actionJson['isFinal']="1"; 
            }
            $actionJson['added_by']=$liveData['assessor_id'];
            $actionJson['assessor_id']=$liveData['assessor_id'];
        } else if($isFinal=='user_id'){
            $actionJson['user_id']=$liveData['assessor_id'];
        } else if($isFinal==''){
            $actionJson['assessor_id']=$liveData['assessor_id'];
        }
        $actionJson['assessment_id']=$liveData['assessment_id'];
        
        if($type=='insertScoreFile' && checkInternet1()==1){
            //$fileId=array();
            //foreach ($b as $j => $c) {
            
                $Query1="Select t1.judgement_statement_instance_id,t1.rating_id,t1.isFinal,t1.evidence_text,t3.file_name,t3.uploaded_by "
                    . " from f_score t1 Left join h_score_file t2 On (t2.score_id=t1.score_id) Left Join d_file t3 On (t2.file_id=t3.file_id)"
                    . " Where t1.score_id='".$c['action_id']."' and t3.file_id='".$c['action_content']."'";
                $getLocalData = $objDB->get_row($Query1);
                
                if(!empty($getLocalData) && checkInternet1()==1){
                    $Query2="Select score_id from f_score where judgement_statement_instance_id='".$getLocalData['judgement_statement_instance_id']."'"
                        . " and rating_id='".$getLocalData['rating_id']."' and isFinal='".$getLocalData['isFinal']."' and "
                        . "evidence_text='".$getLocalData['evidence_text']."' and added_by='".$liveData['assessor_id']."' "
                        . "and assessor_id='".$liveData['assessor_id']."' and assessment_id='".$liveData['assessment_id']."'";
                    $liveScoreId=$objDBLive->get_row($Query2);
                    
                    if(!empty($liveScoreId) && checkInternet1()==1){
                        $Query3="Select file_id,score_file_id from h_score_file where score_id='".$liveScoreId['score_id']."'";
                        $liveFileId=$objDBLive->get_row($Query3);
//                        $saveFile=true;
                        $saveFile = saveEvidenceFile(FTP_SERVER,FTP_USER,FTP_PASSWORD,$_SERVER['DOCUMENT_ROOT']."/Adhyayan/adhyayanReloaded/uploads/", 
                                    LIVEROOTURL,$getLocalData['file_name']);
                        if(empty($liveFileId) && checkInternet1()==1 && $liveFileId['file_id']==''){
                            if($objDBLive->insert('d_file',array('file_name'=>$getLocalData['file_name'],'uploaded_by'=>$liveData['assessor_id'],
                                'upload_date'=>$c['creation_date'])) && checkInternet1()==1 && $saveFile==true)
                            {
                                $lastInsertFileId=$objDBLive->get_last_insert_id();
                                if($lastInsertFileId!='' && checkInternet1()==1){
                                    if($objDBLive->insert('h_score_file',array('score_id'=>$liveScoreId['score_id'],
                                        'file_id'=>$lastInsertFileId)) && checkInternet1()==1){
                                        $score_id=$objDBLive->get_last_insert_id();
                                    }
                                }
                            }
                            
                        } else {
                            if($saveFile==true && checkInternet1()==1)
                            {
                                if($k==0){
                                    $objDBLive->delete('d_file',array('file_id'=>$liveFileId['file_id']));
                                    $objDBLive->delete('h_score_file',array('file_id'=>$liveFileId['file_id']));
                                }
                                if($objDBLive->insert('d_file',array('file_name'=>$getLocalData['file_name'],'uploaded_by'=>$liveData['assessor_id'],
                                    'upload_date'=>$c['creation_date'])) && checkInternet1()==1)
                                {
                                    $lastInsertFileId=$objDBLive->get_last_insert_id();
                                    if($lastInsertFileId!='' && checkInternet1()==1){
                                        if($objDBLive->insert('h_score_file',array('score_id'=>$liveScoreId['score_id'],
                                            'file_id'=>$lastInsertFileId)) && checkInternet1()==1){
                                            $score_id=$objDBLive->get_last_insert_id();
                                        }
                                    }
                                }
                            }
                        }
                        if(!empty($lastInsertFileId) && checkInternet1()==1){
                            //$fileId[]=$score_id;
                            $objDBLive->saveHistoryData($score_id,$c['table_name'],$c['action_unique_id'],$c['action'],$liveScoreId['score_id'],
                                    $lastInsertFileId,$c['action_json'],1,date('Y-m-d H:i:s'));
                            $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$c['action_unique_id'],
                                'action'=>$c['action'],'id'=>$c['id']));
                        }
                    }
                }
            //}
             
        } else if($type=='insert' && checkInternet1()==1){
            if($objDBLive->insert($table,$actionJson) && checkInternet1()==1){
                $score_id=$objDBLive->get_last_insert_id();
            }
        } else if($type=='update' && checkInternet1()==1){
            if(array_key_exists("isFilled",$actionJson)){
                if($actionJson['isFilled']==1){
                    $data = array('percComplete'=>$actionJson['percComplete'],'isFilled'=>1);
                } else if($actionJson['isFilled']==0){
                    $data = array('percComplete'=>$actionJson['percComplete']);
                }
            } else {
                $data = array('percComplete'=>$actionJson['percComplete']);
            }
            //$data = array('percComplete'=>$actionJson['percComplete'],'isFilled'=>$actionJson['isFilled']);
            $where = array('assessment_id'=>$actionJson['assessment_id'],'user_id'=>$actionJson['user_id']);
            if($objDBLive->update($table,$data,$where) && checkInternet1()==1){
                $score_id=$actionJson['assessment_id'];
            }
            
//            if(isset($actionJson['isFilled'])){
//                $data = array('percComplete'=>$actionJson['percComplete'],'isFilled'=>$actionJson['isFilled']);                
//            } else {
//                $data = array('percComplete'=>$actionJson['percComplete']);
//            }
//            //$data = array('percComplete'=>$actionJson['percComplete'],'isFilled'=>$actionJson['isFilled']);
//            $where = array('assessment_id'=>$actionJson['assessment_id'],'user_id'=>$actionJson['user_id']);
//            $Select="Select percComplete,isFilled from ".$table." where assessment_id='".$actionJson['assessment_id']."' and "
//                    . "user_id='".$actionJson['user_id']."'";
//            $percent = $objDB->get_row($Select);
//            if(!empty($percent) && $percent['percComplete']==100.00 && $percent['isFilled']==1){
//                if($objDBLive->update($table,array('percComplete'=>100,'isFilled'=>1),$where) && checkInternet1()==1){
//                    $score_id=$actionJson['assessment_id'];
//                }
//            } else {
//                if($data['percComplete']==100 && ($data['isFilled']==1 || $data['isFilled']==0)){
//                    if($objDBLive->update($table,array('percComplete'=>100,'isFilled'=>$data['percComplete']),$where) && checkInternet1()==1){
//                        $score_id=$actionJson['assessment_id'];
//                    }
//                } else {
//                    if($objDBLive->update($table,$data,$where) && checkInternet1()==1){
//                        $score_id=$actionJson['assessment_id'];
//                    }
//                }
//            }
                     
        }
        //echo $table."-->".$score_id."<br/>";
        if($score_id>0 && checkInternet1()==1){
            $arrayScoreId[]=$score_id;
            $objDBLive->saveHistoryData($score_id,$c['table_name'],$c['action_unique_id'],$c['action'],$actionJson['assessment_id'],
                    $liveData['assessor_id'],$c['action_json'],1,date('Y-m-d H:i:s'));
            $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$c['action_unique_id'],'action'=>$c['action'],'id'=>$c['id']));
        }
    }
    if(count($arrayScoreId)==count($b) && checkInternet1()==1 && !in_array(0, $arrayScoreId)){
        $scoreId=1;
    } else {
        $scoreId=0;
    }
    return $scoreId;
}

// function for updating rating score into live server on 28-03-2016
function updateRating($objDB,$objDBLive,$liveData,$b,$table,$type){
    $arrayScoreId = array(); 
    
    foreach ($b as $k => $c) {
        $actionJson = json_decode($c['action_json'], true);
        
        if($table=='f_score' && $type=='update'){
            $actionJson['assessor_id']=$liveData['assessor_id'];
            $actionJson['assessment_id']=$liveData['assessment_id'];
            $data = array(
                'isFinal'=>0
            );
            $where = array(
                'judgement_statement_instance_id'=>$actionJson['judgement_statement_instance_id'],
                'assessment_id'=>$actionJson['assessment_id'],
                'assessor_id'=>$actionJson['assessor_id']
            );
            $score_id=$actionJson['assessment_id'];
        } else if($table=='h_cq_score'){
            $actionJson['assessor_id']=$liveData['assessor_id'];
            $actionJson['assessment_id']=$liveData['assessment_id'];
            if($type=='update'){
                $data = array(
                    'd_rating_rating_id'=>$actionJson['d_rating_rating_id']
                );
                $where = array(
                    'core_question_instance_id'=>$actionJson['core_question_instance_id'],
                    'assessment_id'=>$actionJson['assessment_id'],
                    'assessor_id'=>$actionJson['assessor_id']
                );
            } else if($type=='delete'){
                $where = $actionJson;
            }            
            $score_id=$actionJson['core_question_instance_id'];
        } else if($table=='h_kq_instance_score'){
            $actionJson['assessor_id']=$liveData['assessor_id'];
            $actionJson['assessment_id']=$liveData['assessment_id'];
            if($type=='update'){
                $data = array(
                    'd_rating_rating_id'=>$actionJson['d_rating_rating_id']
                );
                $where = array(
                    'key_question_instance_id'=>$actionJson['key_question_instance_id'],
                    'assessment_id'=>$actionJson['assessment_id'],
                    'assessor_id'=>$actionJson['assessor_id']
                );
            } else if($type=='delete'){
                $where = $actionJson;
            }
            
            $score_id=$actionJson['key_question_instance_id'];
        } else if($table=='h_kpa_instance_score'){
            $actionJson['assessor_id']=$liveData['assessor_id'];
            $actionJson['assessment_id']=$liveData['assessment_id'];
            if($type=='update'){
                $data = array(
                    'd_rating_rating_id'=>$actionJson['d_rating_rating_id']
                );
                $where = array(
                    'kpa_instance_id'=>$actionJson['kpa_instance_id'],
                    'assessment_id'=>$actionJson['assessment_id'],
                    'assessor_id'=>$actionJson['assessor_id']
                );
            } else if($type=='delete'){
                $where = $actionJson;
            }            
            $score_id=$actionJson['kpa_instance_id'];
        } else if($table=='assessor_key_notes'){
            if($type=='updateNote'){
                
            } else if($type=='deleteNote'){
                $SQL3="Select table_id from z_history where action_json like '%".$c['action_id']."%' and table_name='".$table."' and "
                        . "action like '%Update%'";
                $assessorNote = $objDBLive->get_row($SQL3);                
            }
        } else if($table=='d_assessment'){
            $actionJson['assessment_id']=$liveData['assessment_id'];
            if($type=='update'){
                $data = array(
                    'isAssessorKeyNotesApproved'=>$actionJson['isAssessorKeyNotesApproved']
                );
                $where = array(
                    'assessment_id'=>$actionJson['assessment_id']
                );
            }
            $score_id=$actionJson['assessment_id'];
        }
        if($type=='update'){
            
            if($objDBLive->update($table,$data,$where) && checkInternet1()==1){
                $arrayScoreId[]=$actionJson['assessment_id'];
                $objDBLive->saveHistoryData($score_id,$c['table_name'],$c['action_unique_id'],$c['action'],$actionJson['assessment_id'],
                        $liveData['assessor_id'],$c['action_json'],1,date('Y-m-d H:i:s'));
                $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$c['action_unique_id'],'action'=>$c['action'],
                    'id'=>$c['id']));
            }
        } else if($type=='delete'){
            if($objDBLive->delete($table,$where) && checkInternet1()==1){
                $arrayScoreId[]=$actionJson['assessment_id'];
                $objDBLive->saveHistoryData($score_id,$c['table_name'],$c['action_unique_id'],$c['action'],$actionJson['assessment_id'],
                        $liveData['assessor_id'],$c['action_json'],1,date('Y-m-d H:i:s'));
                $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$c['action_unique_id'],'action'=>$c['action'],
                    'id'=>$c['id']));
            }
        } else if($type=='updateNote'){
            
            $kpa_instance_id=  json_decode($c['action_json'],TRUE);
//            print_r($kpa_instance_id);
//            die;
            if(!empty($kpa_instance_id) && checkInternet1()==1){
                if($k==0){
                    $objDBLive->delete($table,array('assessment_id'=>$liveData['assessment_id']));
                }
                if($objDBLive->insert($table,array('assessment_id'=>$liveData['assessment_id'],'kpa_instance_id'=>$kpa_instance_id['kpa_instance_id'],
                    'text_data'=>$c['action_content'],'type'=>$kpa_instance_id['type'])) && checkInternet1()==1){
                    $noteId = $objDBLive->get_last_insert_id();                                            
                }
                if(checkInternet1()==1 && $noteId>0){
                    $arrayScoreId[]=$noteId;
                    $objDBLive->saveHistoryData($noteId,$c['table_name'],$c['action_unique_id'],$c['action'],$noteId,
                        $c['action_content'],$c['action_json'],1,date('Y-m-d H:i:s'));
                    $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$c['action_unique_id'],'action'=>$c['action']));
                    $SQL5="Select id from z_history where action_flag='0' and action_unique_id='".$c['action_unique_id']."' and "
                            . "action='internalAssessmentAssessorKeyNoteDelete'";
                    $checkDeleteNote=$objDB->get_row($SQL5);
                    if(!empty($checkDeleteNote)){
                        $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$c['action_unique_id'],
                            'action'=>'internalAssessmentAssessorKeyNoteDelete'));
                    }
                }
                
                //echo $table."-->".$noteId."<br/>";
            }           
        } else if($type=='deleteNote'){
//            if(!empty($assessorNote) && $objDBLive->delete($table,array('id'=>$assessorNote['table_id'],'assessment_id'=>$liveData['assessment_id']))
//                    &&  checkInternet1()==1){
//                $objDBLive->delete($table,array('id'=>$assessorNote['table_id'],'text_data'=>'','assessment_id'=>$liveData['assessment_id']));
//                $arrayScoreId[]=$assessorNote['table_id'];
//                $objDBLive->saveHistoryData($assessorNote['table_id'],$c['table_name'],$c['action_unique_id'],$c['action'],$assessorNote['table_id'],
//                    $assessorNote['table_id'],$c['action_json'],1,date('Y-m-d H:i:s'));
//                $objDB->update('z_history',array('action_flag'=>1),array('action_unique_id'=>$c['action_unique_id'],'action'=>$c['action'],
//                    'id'=>$c['id']));
//            }
        } 
        
    }
    if(count($arrayScoreId)==count($b) && checkInternet1()==1 && !in_array(0, $arrayScoreId)){
        $scoreId=1;
    } else {
        $scoreId=0;
    }
    return $scoreId;
    
}

function saveEvidenceFile($ftp_server, $ftp_username, $ftp_userpass, $localDirectory, $liveDirectory, $fileName) {
    $ftp_conn = ftp_connect($ftp_server);
    $login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);
    if ($login == true) {
        $file = $localDirectory . '/' . $fileName;
        if (file_exists($file)) {
            ftp_site($ftp_conn,"CHMOD 0755 ".$liveDirectory);
            if (ftp_put($ftp_conn, $liveDirectory . $fileName, $file, FTP_BINARY)) {
                
                $a = true;
            } else {
                $a = false;
            }
        } else {
            $a = false;
        }
        // upload file
        ftp_close($ftp_conn);
    } else {
        $a = false;
    }

    return $a;
    // close connection
}

function addLanguage($language,$objDBLive) {
    if ($objDBLive->insert("h_user_language", $language)) {
        return true;
    } else {
        return false;
    }
}

function saveAssessorIntroductoryAssessment($data,$objDBLive){
    if($data['user_id']==''){
        return false;
    } else {
        $SQL="Select id from h_user_introductory_assessment where user_id='".$data['user_id']."'";
        $id=  $objDBLive->get_row($SQL);
        if(empty($id)){
            $data['create_date']=date('Y-m-d H:i:s');
            if($objDBLive->insert('h_user_introductory_assessment',$data)){
                return $objDBLive->get_last_insert_id();;
            } else {
                return false;
            }
        } else {
            $where = array('user_id'=>$data['user_id']);
            if($objDBLive->update('h_user_introductory_assessment',$data,$where)){
                return $data['user_id'];
            } else {
                return false;
            }
        }
    }
}
?>