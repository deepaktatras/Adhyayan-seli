<?php

require_once ( 'config.php');
class JsStmntCron {

    function testCron() {

        try {
            ini_set('max_execution_time', 0);
            $servername = DB_HOST;
            $dbname = DB_NAME;
            $username = DB_USER;
            $password = DB_PASSWORD;
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            include_once (ROOT . "library/PHPExcel/Classes/PHPExcel/IOFactory.php");
            define ( 'CLI', (PHP_SAPI == 'cli') ? true : false );
            define ( 'EOL', CLI ? PHP_EOL : '<br />' );
            // echo date('H:i:s'), " Load from Excel2007 file", EOL;
            $objReader = PHPExcel_IOFactory::createReader ( 'Excel2007' );
            $objPHPExcel = $objReader->load ( ROOT . "uploads/AQSExcel/CtJ_(Only Mostlies)_WGLL.xlsx" );
            $clientModel = new clientModel;
            $assessmentModel = new assessmentModel();
            $aqsModel = new aqsDataModel();
            $column_array=array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');                                              
            $roles = array(3,6);
           // echo max($column_array);die;
            $sheetData = $objPHPExcel->getActiveSheet(0)->rangeToArray('A3:FI3');
            
            if(!empty($sheetData)){
                  $numRecords = count($sheetData[0]);
           // while($numRecords){  
             $sql = " SELECT MAX(equivalence_id) as equivalence_id FROM h_lang_translation  ";
            $stmt = $conn->prepare($sql);
                
            $stmt->execute();
            $equRes =  $stmt->fetch(PDO::FETCH_ASSOC);
            $current_equivalence_id = $equRes['equivalence_id'];
            $equivalence_id = $current_equivalence_id;
              // echo "<pre>"; print_r($sheetData);die;
               // echo "<pre>"; print_r($sheetData);die;
            //$insertStatementLangSql = "INSERT INTO h_lang_translation (equivalence_id,language_id,translation_type_id,translation_text,isActive)"
              //          . " VALUES (:equivalence_id,:language_id,:translation_type_id,:translation_text,:isActive)";
             
            $insertStatementLangSql = "INSERT INTO h_lang_translation (equivalence_id,language_id,translation_type_id,translation_text,isActive)"
                        . " VALUES ";
             //$stmt = $conn->prepare($insertStatementLangSql);
             
            //$insertStatementSql = 'INSERT INTO d_good_look_like_statement (isActive,equivalence_id) VALUES(:isActive,:equivalence_id)';
            $insertStatementSql = 'INSERT INTO d_good_look_like_statement (isActive,equivalence_id) VALUES';
              //$stmt2 = $conn->prepare($insertStatementSql);  
              
           // echo "coun".count($sheetData[0]);die;
           
                
                
                $start = 0;$last=5;$numRecord=5;
                $inserFlag=0;$equivalence_id=0;
                //for($i=$start;$i<=$last;$i++){
                foreach($sheetData[0] as $key=>$val){
                   // $val = $sheetData[0][$i];
                    if(!empty($val)){
                        //$equivalence_id++;
                        $insertEquiSql = "INSERT INTO d_equivalence (equivalence_id) VALUES(0) ";
                        if($conn->exec($insertEquiSql)){
                                $equivalence_id=$conn->lastInsertId(); 
                        }
                        $val = $conn->quote(trim($val));
                        $insertStatementSql .= '(1,'.$equivalence_id.'),';                    
                        $insertStatementLangSql .= '('.$equivalence_id.',9,4,'."$val".',1),'; 
                        $inserFlag=1;
                       // $stmt->bindValue(':equivalence_id', $equivalence_id);
                           // $stmt->bindValue(':language_id',9);
                           // $stmt->bindValue(':translation_type_id', 4);
                            //$stmt->bindValue(':translation_text', $val);
                            //$stmt->bindValue(':isActive', 1);
                            
                            //$stmt2->bindValue(':isActive', 1);
                            //$stmt2->bindValue(':equivalence_id', $equivalence_id);
                            //$stmt->execute();die;
                        }
                        //break;  
                           
                          
                        }
                        if($inserFlag){
                            $insertStatementSql = trim($insertStatementSql,",");
                            $insertStatementLangSql = trim($insertStatementLangSql,",");
                        }
                       
                          /* $start = $last+1;
                           if($numRecords >=$numRecord){
                               $numRecords = $numRecords-$numRecord;
                               $last = $last+$numRecord;
                           }else if($numRecords) {
                               $last = $last+$numRecords;
                               $numRecords=0;
                           }*/
                        
                        
                        //die;
                        
            }
                        //$stmt2->execute();
                    
                //echo $insertStatementLangSql;
                      //  echo $insertStatementSql;
                        $conn->exec($insertStatementLangSql);
                        $conn->exec($insertStatementSql);
                
          

            //echo "New records created successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }
    
     function insertMostlyHelperData() {

        try {
            ini_set('max_execution_time', 0);
            $servername = DB_HOST;
            $dbname = DB_NAME;
            $username = DB_USER;
            $password = DB_PASSWORD;
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $mostlySql = " select good_looks_like_statement_id from d_good_look_like_statement ";
            $stmt = $conn->prepare($mostlySql);
                
            $stmt->execute();
            $mostlyRes =  $stmt->fetchAll(PDO::FETCH_ASSOC);
           
            if(!empty($mostlyRes)){
                
                
                    $sql = " select e.judgement_statement_id,translation_text from h_kpa_diagnostic a
                           inner join h_kpa_kq b on a.kpa_instance_id=b.kpa_instance_id
                           inner join h_kq_cq c on b.key_question_instance_id=c.key_question_instance_id
                           inner join h_cq_js_instance d on d.core_question_instance_id=c.core_question_instance_id
                           inner join d_judgement_statement e on d.judgement_statement_id=e.judgement_statement_id
                           inner join h_lang_translation f on e.equivalence_id=f.equivalence_id and language_id=9                   
                           where a.diagnostic_id=2 order by kpa_order,kq_order,cq_order,js_order;  ";
                   $stmt = $conn->prepare($sql);

                   $stmt->execute();
                   $jsRes =  $stmt->fetchAll(PDO::FETCH_ASSOC);
                   // echo "<pre>";print_r($jsRes);die;
                   $sqlFlag = 0;
                   $insertStatementHelperSql = 'INSERT INTO h_js_mostly_statements (mostly_statements_id,judgement_statement_id) VALUES';

                   foreach($mostlyRes as $key=>$data){

                           $sqlFlag = 1;
                          $insertStatementHelperSql .= '('.$data['good_looks_like_statement_id'].','.$jsRes[$key]['judgement_statement_id'].'),';
                   }
                   if($sqlFlag){
                           $insertStatementHelperSql = trim($insertStatementHelperSql,",");
                           //echo $insertStatementHelperSql;die;
                           $conn->exec($insertStatementHelperSql);
                   }
                     
            }
                
            //echo "New records created successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }
    function sendNotificationMail($notificationsMailTemplates,$data,$attachmentStatus,$conn,$sheetDate){
        
            //print_r($data);die;
            $link = "<a href=".SITEURL.'index.php?controller=login&action=login'.">here</a>";
            $body = str_replace('_link_',$link,$notificationsMailTemplates['template_text']) ;
            $school_name = $data['client_name'].", ".$data['city_name'].", ".$data['state_name'];
            $body = str_replace('_school_',$school_name,$body) ;
            $body = str_replace('_sdate_',date('d M Y',strtotime($data['sdate'])),$body) ;
            $body = str_replace('_edate_',date('d M Y',strtotime($data['edate'])),$body) ;
            
            if($sheetDate) {
                $sheetDate = "+".REIMBURSEMENT_SHEET_DAYS." day";
                $body = str_replace('_sheet_date_', date('d M Y', strtotime($sheetDate, strtotime($data['edate']))), $body); 
            }           
            $mail_body = nl2br(str_replace('_name_',ucwords($data['name']),$body)) ;
            $subject = str_replace('_school_',$school_name,$notificationsMailTemplates['subject']) ;
            $sender = $notificationsMailTemplates['sender'];
            $senderName = $notificationsMailTemplates['sender_name'];
            $toEmail = $data['email'];
            $toName = $data['name'];       
            //$ccEmail = 'deepak.t@tatrasdata.com';
            $ccEmail = $notificationsMailTemplates['cc'];
            $ccName = '';
            $attachmentPath = array();
            if($attachmentStatus) {
                $attachmentPath []= 'notification_files/AQSExpensesGuidelines.pdf';
                $attachmentPath []= 'notification_files/ReimbursementSheet.xls';
            }
            if(sendEmail($sender,$senderName,$toEmail,$toName,$ccEmail,$ccName,$subject,$mail_body,$attachmentPath)){
                
                $sql = "UPDATE d_notification_queue SET status = :status
                WHERE user_id = :user_id AND assessment_id = :assessment_id AND type = :type";
                $stmt = $conn->prepare($sql); 
                $status = 0;
                $type = 1;
                $stmt->bindParam(':status', $status, $conn::PARAM_INT);       
                $stmt->bindParam(':user_id', $data['user_id'], $conn::PARAM_INT);    
                $stmt->bindParam(':assessment_id', $data['assessment_id'], $conn::PARAM_INT);    
                $stmt->bindParam(':type', $type, $conn::PARAM_INT);
                $stmt->execute(); 
            }  
           // sendEmail($sender,$sender,$data['email'],$data['name'],$subject,$mail_body,'');   
    }
     function reviewNotification() {

        try {
           
            //$assessmentModel = new assessmentModel();
            //echo current($this->user['role_ids']);die;
            $servername = DB_HOST;
            $dbname = DB_NAME;
            $username = DB_USER;
            $password = DB_PASSWORD;
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           
            
           /* $sql = " SELECT un.sub_role_name,un.sub_role_id,u.name, u.email, u.user_id,q.notification_id,aq.school_aqs_pref_start_date as sdate,
                        aq.school_aqs_pref_end_date as edate,aq.client_name,aq.city_name,aq.state_name,aq.assessment_id 
                        FROM d_user u INNER JOIN d_notification_queue q on u.user_id = q.user_id INNER JOIN  
                       (SELECT r.sub_role_name,r.sub_role_id ,et.user_id,et.assessment_id FROM `h_assessment_external_team` et 
                         INNER JOIN d_user_sub_role r ON et.user_sub_role = r.sub_role_id                    
                         UNION SELECT 'Lead' as sub_role_name ,1 as sub_role_id ,et.user_id ,et.assessment_id
                         FROM `h_assessment_user` et INNER JOIN d_user_role r ON et.role = r.role_id  WHERE  et.role=4
                        ) un ON un.assessment_id = q.assessment_id  AND un.user_id = q.user_id  INNER JOIN
                        (SELECT a.school_aqs_pref_start_date,a.school_aqs_pref_end_date,cl.client_name,ct.city_name,
                        st.state_name ,d.assessment_id FROM `d_assessment` d
                        INNER JOIN d_client cl ON cl.client_id = d.client_id
                        INNER JOIN d_cities ct ON ct.city_id = cl.city_id
                        INNER JOIN d_states st ON st.state_id = cl.state_id
                        INNER JOIN d_notification_queue q1 ON q1.assessment_id = d.assessment_id 
                        INNER JOIN d_AQS_data a ON d.aqsdata_id = a.id
                        GROUP BY d.assessment_id) aq on aq.assessment_id = q.assessment_id  
                        
                        WHERE q.status = 1 and q.assessment_id = 957 ";*/
            
             $sql = " SELECT date(rt.ratingInputDate) as ratingInputDate,q1.type,un.sub_role_name,un.sub_role_id,q1.user_id as user,q1.notification_id,a.school_aqs_pref_start_date as sdate,a.school_aqs_pref_end_date as edate,cl.client_name,ct.city_name,
                        st.state_name ,d.assessment_id,u.name, u.email, u.user_id FROM `d_assessment` d
                        INNER JOIN d_client cl ON cl.client_id = d.client_id
                        INNER JOIN d_cities ct ON ct.city_id = cl.city_id
                        INNER JOIN d_states st ON st.state_id = cl.state_id
                        INNER JOIN d_notification_queue q1 ON q1.assessment_id = d.assessment_id 
                        INNER JOIN h_assessment_user rt ON q1.assessment_id = rt.assessment_id AND rt.isFilled = 1 AND rt.role=4
                        INNER JOIN d_AQS_data a ON d.aqsdata_id = a.id
                        INNER JOIN d_user u ON u.user_id = q1.user_id
                        INNER JOIN (SELECT r.sub_role_name,r.sub_role_id ,et.user_id,et.assessment_id FROM `h_assessment_external_team` et 
                         INNER JOIN d_user_sub_role r ON et.user_sub_role = r.sub_role_id                    
                         UNION SELECT 'Lead' as sub_role_name ,1 as sub_role_id ,et.user_id ,et.assessment_id
                         FROM `h_assessment_user` et INNER JOIN d_user_role r ON et.role = r.role_id  WHERE  et.role=4 
                        ) un ON un.assessment_id = q1.assessment_id  AND un.user_id = q1.user_id
                                            
                       	WHERE q1.status = 1 AND  q1.type=1  ";
            $stmt = $conn->prepare($sql);
                
            $stmt->execute();
            $notifications =  $stmt->fetchAll(PDO::FETCH_ASSOC);   
            $notificationsUsers = array();
            
            foreach($notifications as $users) {
                //if(isset($notificationsUsers[$users['user_id']])) {
                   // $notificationsUsers[$users['user_id']][] = $users; 
                //}else
                    $notificationsUsers[$users['user_id']][$users['assessment_id']][] = $users;
            }
            $sqlTemplate = " SELECT u.template_text,q.subject,q.sender,q.sender_name,q.cc FROM d_review_notification_template u "
                        . "INNER JOIN h_review_notification_mail_users q on u.id = q.notification_id ";
           
            
            //$notificationsUsers = array_slice($notificationsUsers,0,3);
           // echo "<pre>";print_r($notificationsUsers);die;
            foreach($notificationsUsers as $data) {
                
                foreach($data as $userData) {
                 //echo "<pre>";print_r($userData);die;
                 $sqlQuery = '';
                 $attachmentStatus = 0;
                 $sheetDate = 0;
                 $whrCond = '';
               /* if(!empty($userData) && count($userData) == 2) {
                   $whrCond =  " WHERE u.template_type = 4 " ;
                }else*/  

                if(!empty($userData[0]['edate']) && strtotime(date("Y-m-d")) == strtotime("+1 day", strtotime($userData[0]['edate']))) {
                    if(!empty($userData) &&  isset($userData[0]['sub_role_name']) && $userData[0]['sub_role_name'] == 'Observer') {
                         $whrCond =  " WHERE u.template_type = 7 " ;
                    } else if(!empty($userData) &&  isset($userData[0]['sub_role_name']) && $userData[0]['sub_role_name'] != 'Observer' && empty($userData[0]['notification_id'])) {
                         $whrCond =  " WHERE u.template_type = 7 " ; 

                    }else if(!empty($userData) && count($userData) == 1 && !empty($userData[0]['notification_id'])) {
                        $whrCond =  " WHERE u.template_type = 6 " ;
                    }else if(!empty($userData) && count($userData) == 2 && isset($userData[0]['sub_role_name']) && $userData[0]['sub_role_name']=='Lead' ) {
                        $whrCond =  " WHERE u.template_type = 5 " ;
                        $sheetDate = 1;
                        $attachmentStatus = 1;
                    }else if(!empty($userData) && count($userData) == 2  ) {
                        $whrCond =  " WHERE u.template_type = 4 " ;
                        $attachmentStatus = 1;
                         $sheetDate = 1;
                    }
                }
                if(!empty($whrCond)) {
                    $sqlQuery = $sqlTemplate.$whrCond;
                    $stmtTemplate = $conn->prepare($sqlQuery);
                    $stmtTemplate->execute();
                    $notificationsMailTemplates =  $stmtTemplate->fetch();
               
                    foreach($userData as $data) {
                      if(!empty($userData)) {
                            //echo "aaaa";
                            $this->sendNotificationMail($notificationsMailTemplates,$data,$attachmentStatus,$conn,$sheetDate);
                            break;
                      }

                    }
                 }
            }

            }
           // echo "<pre>";print_r($notificationsUsers);die;
         

           // echo "New records created successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }

}

$obj = new JsStmntCron();
//$obj->testCron();
$obj->insertMostlyHelperData();
