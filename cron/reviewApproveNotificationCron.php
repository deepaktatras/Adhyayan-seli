<?php

require_once ( 'config.php');

class NotificationCron {

    function connectToDB() {

        try {
            $servername = DB_HOST;
            $dbname = DB_NAME;
            $username = DB_USER;
            $password = DB_PASSWORD;
            return new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }

    function sendNotificationMail($notificationsMailTemplates,$data,$attachmentStatus,$conn){
        
            $link = "<a href=".SITEURL.'index.php?controller=diagnostic&action=feedbackForm&assessment_id='.$data['assessment_id'].'&user_id='.$data['user_id'].'&des=received_feedback'.">here</a>";
            $body = str_replace('_link_',$link,$notificationsMailTemplates['template_text']) ;
            $school_name = $data['client_name'].", ".$data['city_name'].", ".$data['state_name'];
            $body = str_replace('_school_',$school_name,$body) ;
            $body = str_replace('_sdate_',date('d M Y',strtotime($data['sdate'])),$body) ;
            $body = str_replace('_edate_',date('d M Y',strtotime($data['edate'])),$body) ;
            $mail_body = nl2br(str_replace('_name_',ucwords($data['name']),$body)) ;
            $subject = str_replace('_school_',$school_name,$notificationsMailTemplates['subject']) ;
            $sender = $notificationsMailTemplates['sender'];
            //$senderName = 'Adhyayan';
            $senderName=$notificationsMailTemplates['sender_name'];
            $toEmail = $data['email'];
            $toName = $data['name'];
            //$ccEmail = 'deepak.t@tatrasdata.com';
            //print_r($link);die;
            $ccEmail = $notificationsMailTemplates['cc'];
            $ccName = '';
            $attachmentPath = array();
            if($attachmentStatus) {
                $attachmentPath []= 'notification_files/AQSExpensesGuidelines.pdf';
                $attachmentPath []= 'notification_files/ReimbursementSheet.xls';
            }
            if(sendEmail($sender,$senderName,$toEmail,$toName,$ccEmail,$ccName,$subject,$mail_body,$attachmentPath)){
                
               
               $sql = "UPDATE d_review_submit_notification_queue SET status = :status
                WHERE user_id = :user_id AND assessment_id = :assessment_id";
            $stmt = $conn->prepare($sql);
            $status = 0;
            $stmt->bindParam(':status', $status, $conn::PARAM_INT);
            $stmt->bindParam(':user_id', $data['user_id'], $conn::PARAM_INT);
            $stmt->bindParam(':assessment_id', $data['assessment_id'], $conn::PARAM_INT);
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


            $sql = " SELECT un.sub_role_name,un.sub_role_id,u.name, u.email, u.user_id,aq.school_aqs_pref_start_date as sdate,
                        aq.school_aqs_pref_end_date as edate,aq.client_name,aq.city_name,aq.state_name,aq.assessment_id 
                        FROM d_user u INNER JOIN d_review_submit_notification_queue q on u.user_id = q.user_id INNER JOIN  
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
                        INNER JOIN d_review_submit_notification_queue q1 ON q1.assessment_id = d.assessment_id 
                        INNER JOIN d_AQS_data a ON d.aqsdata_id = a.id
                        GROUP BY d.assessment_id) aq on aq.assessment_id = q.assessment_id  
                        
                        WHERE q.status = 1  ";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $notifications =  $stmt->fetchAll();            
            
            $templateSql="SELECT nm.sender,nm.sender_name,nm.cc,nm.subject,nt.template_text,nt.id FROM d_review_notification_template nt INNER JOIN h_review_notification_mail_users nm "
                     . " ON nt.id = nm.notification_id WHERE nt.id=:type AND  nm.status = :status";                        
            $stmt = $conn->prepare($templateSql);
            $type = 3;
            $status = 1;
            $stmt->bindParam(':type', $type, $conn::PARAM_INT);
            $stmt->bindParam(':status', $status, $conn::PARAM_INT);
            $stmt->execute();
            $notificationsTemplates = $stmt->fetch();
            $notificationsUsers = array_slice($notifications, 0, 3);
            $attachmentStatus = 0;
            foreach ($notificationsUsers as $userData) {
                if (!empty($userData)) {
                    $this->sendNotificationMail($notificationsTemplates, $userData, $attachmentStatus, $conn);
                }
            }
           // echo "New records created successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }

//function to send mail Confirmation regarding Reimbursement sheet
    function reminderForReimbursementSheet() {

        try {


            $conn = $this->connectToDB();

            $sql = " SELECT date(h.ratingInputDate) as rating_date,q.first_mail_status,date(q.mail_sent_date) as mail_sent_date,q.sheet_status,q.assessment_id,q.user_id ,c.client_name,st.state_name,ct.city_name,u.name as user_name,
                        d.school_aqs_pref_start_date as sdate,d.school_aqs_pref_end_date as edate
                        FROM d_user u INNER JOIN h_user_review_reim_sheet_status q on u.user_id = q.user_id
                        INNER JOIN d_assessment a ON q.assessment_id = a.assessment_id                        
                        INNER JOIN d_client c ON a.client_id = c.client_id
                        INNER JOIN d_cities ct ON c.city_id = ct.city_id
                        INNER JOIN d_states st ON c.state_id = st.state_id
                        INNER JOIN d_AQS_data d ON a.aqsdata_id = d.id
                        INNER JOIN h_assessment_user h ON h.assessment_id = a.assessment_id AND h.role = :role
                        WHERE q.sheet_status = :sheet_status AND h.isFilled = :isFilled 
                      ";
            $stmt = $conn->prepare($sql);
            $role = 4;
            $sheet_status = 0;
            $isFilled = 1;
            $stmt->bindParam(':sheet_status', $sheet_status, $conn::PARAM_INT);
            $stmt->bindParam(':role', $role, $conn::PARAM_INT);
            $stmt->bindParam(':isFilled', $isFilled, $conn::PARAM_INT);
            $stmt->execute();
            $sheetData = $stmt->fetchAll(PDO::FETCH_ASSOC);
           // echo "<pre>";print_r($sheetData);die;
             
            $templateSql = "SELECT nm.sender,nm.sender_name,nm.cc,nm.subject,nt.template_text,nt.id FROM d_review_notification_template nt INNER JOIN h_review_notification_mail_users nm "
                    . " ON nt.id = nm.notification_id WHERE nt.id=:type AND  nm.status = :status";
            $stmt = $conn->prepare($templateSql);
            $type = 8;
            $status = 1;
            $stmt->bindParam(':type', $type, $conn::PARAM_INT);
            $stmt->bindParam(':status', $status, $conn::PARAM_INT);
            $stmt->execute();
            $notificationsTemplates = $stmt->fetch(PDO::FETCH_ASSOC);
           // print_r($notificationsTemplates);die;
            //$sheetData = array_unique(array_column($sheetData,'assessment_id'));
            $assessmentSheetData = array();
            $rating_date = '';
            $first_mail_status = 0;
            $mail_sent_date = '';
            foreach($sheetData as $data) {
               // echo date("Y-m-d");
                //echo date("Y-m-d",strtotime("+5 day", strtotime($data['rating_date'])));
                //$data['rating_date'] = date("Y-m-d",$data['rating_date']);
                //$rating_date = "2018-01-03";
                //$data['rating_date'] = "2018-01-03";
                //$data['mail_sent_date'] = "2018-01-01";
                //echo date("Y-m-d",strtotime("+5 day", strtotime($data['rating_date'])));
                //$rating_date = isset($data['rating_date'])?$data['rating_date']:'';
                $first_mail_status = isset($data['first_mail_status'])?$data['first_mail_status']:0;
                $mail_sent_date = isset($data['mail_sent_date'])?$data['mail_sent_date']:'';
                 if (!empty($data['edate']) && empty($first_mail_status) && strtotime(date("Y-m-d")) == strtotime("+5 day", strtotime($data['edate']))) {
                    // print_r($data);die;
                        $assessmentSheetData[$data['assessment_id']][] = $data;
                 }else if (!empty($data['edate']) && !empty($first_mail_status) && strtotime(date("Y-m-d")) == strtotime("+7 day", strtotime($data['mail_sent_date']))) {
                    // print_r($data);die;
                        $assessmentSheetData[$data['assessment_id']][] = $data;
                 }
                }
           //echo "<pre>";print_r($assessmentSheetData);die;
            if(!empty($assessmentSheetData)) {
                  //$actionUrl = SITEURL . 'index.php?controller=assessment&action=reimSheetConfirmation';
                  $actionUrl = SITEURL . 'cron/reviewApproveNotificationCron.php';
                   $senderName = $notificationsTemplates['sender'];
                   // $toEmail = 'amisha.modi@adhyayan.asia';
                    $toEmail = SHEET_TO_EMAIL;
                    $toName = SHEET_TO_NAME;
                foreach($assessmentSheetData as $key=>$assessment ) {
                    
                    $mail_body = "<form id='sheet_confirmation_from' method='post' action='$actionUrl'><table>";
                    $i = 1;
                    foreach($assessment as $data) {                      

                        $mail_body .= '<tr><td><span><b>'.$i.".  ".$data['user_name'].'</b></span></td>';
                        $mail_body .= '<td><input autocomplete="off" id="reim_sheet_yes_'.$data['user_id'].'" type="radio" value="1" name="reim_sheet_'.$data['user_id'].'"  ><label><span>Yes</span></label>';
                        $mail_body .= ' <input autocomplete="off" id="reim_sheet_no_'.$data['user_id'].'" type="radio" value="0" checked="checked" name="reim_sheet_'.$data['user_id'].'"  ><label><span>No</span></label>';
                        $mail_body .= '</td></tr>';
                        $i++;
                    }                
                    if($i>1){
                       $mail_body .= "<tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr><td><input type='submit' name='confirm_sheet' value='Confirm'></td></tr>"; 
                       $mail_body .= "<input type='hidden' name='assessment_id' value='$key'>"; 
                    }
                    $mail_body .= "</form>";
                    
                    $body = str_replace('_form_', $mail_body, $notificationsTemplates['template_text']);
                    $school_name = $data['client_name'] . ", " . $data['city_name'] . ", " . $data['state_name'];
                    $body = str_replace('_school_', $school_name, $body);
                    $body = str_replace('_sdate_', $data['sdate'], $body);
                    $body = str_replace('_edate_', $data['edate'], $body);
                    $body = str_replace('_name_', $toName, $body);
                    //$body = str_replace('_sdate_', date('d M Y', strtotime($data['sdate'])), $body);
                    //$body = str_replace('_edate_', date('d M Y', strtotime($data['edate'])), $body);
                    $mail_body = nl2br( $body);
                    $subject = str_replace('_school_', $school_name, $notificationsTemplates['subject']);
                    $sender = $notificationsTemplates['sender'];
                    $senderName = $notificationsTemplates['sender_name'];
                    $ccEmail = $notificationsTemplates['cc'];
                 
                    $ccName = '';
                    //sendEmail($sender, $senderName, $toEmail, $toName, $ccEmail, $ccName, $subject, $mail_body, '')
                    if (sendEmail($sender, $senderName, $toEmail, $toName, $ccEmail, $ccName, $subject, $mail_body, '')) {
                        //echo "yes";
                        foreach($assessment as $data){
                            $sql = "UPDATE h_user_review_reim_sheet_status SET first_mail_status  = :mail_status,mail_sent_date = :mail_sent_date
                            WHERE user_id = :user_id AND assessment_id = :assessment_id";
                            $stmt1 = $conn->prepare($sql);
                            $status = 1;

                            $date = date("Y-m-d h:i:s");
                            $stmt1->bindParam(':mail_status', $status, $conn::PARAM_INT);
                            $stmt1->bindParam(':mail_sent_date', $date, $conn::PARAM_INT);
                            $stmt1->bindParam(':user_id', $data['user_id'], $conn::PARAM_INT);
                            $stmt1->bindParam(':assessment_id', $data['assessment_id'], $conn::PARAM_INT);
                            $stmt1->execute();
                        }
                       // echo $stmt1->fullQuery;                        
                    }
                      //echo $mail_body;die;
                    }
                    
               
            }
            
           
            
            
            
        //$link = "<a href=" . SITEURL . 'index.php?controller=diagnostic&action=feedbackForm&assessment_id=' . $data['assessment_id'] . '&user_id=' . $data['user_id'] . '&des=received_feedback' . ">here</a>";
       

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }
    
    //function to update status of reimbursement sheet
    function updateReimSheetStatus($params) {
        
        $conn = $this->connectToDB();
        $status = 0;
        $assessmentSql = "SELECT user_id FROM h_user_review_reim_sheet_status WHERE assessment_id = :assessment_id AND sheet_status = :status";
        //print_r($params);die;
        $stmt = $conn->prepare($assessmentSql);
        $stmt->bindParam(':assessment_id', $params['assessment_id'], $conn::PARAM_INT);
        $stmt->bindParam(':status', $status, $conn::PARAM_INT);
        $stmt->execute();
        $sheetAssessmentData = $stmt->fetchAll(PDO::FETCH_ASSOC);
       // print_r($sheetAssessmentData);die;
        if(!empty($sheetAssessmentData)) {
            $updateStatusSql = "UPDATE h_user_review_reim_sheet_status SET sheet_status = :sheet_status WHERE user_id IN ('";
            $sqlCond = '';
            foreach($sheetAssessmentData as $data) {
                if($_POST['reim_sheet_'.$data['user_id']] == 1) {
                    
                    $sqlCond .= $data['user_id']."','";
                } 
                
            }
             $updateStatus = 0;
            if(!empty($sqlCond)) {
                $updateStatusSql .= trim($sqlCond,",'")."') AND assessment_id=:assessment_id";
               
                //echo $updateStatusSql;die;
                $sheet_status= 1;
                $stmt = $conn->prepare($updateStatusSql);
                $stmt->bindParam(':assessment_id', $params['assessment_id'], $conn::PARAM_INT);
                $stmt->bindParam(':sheet_status', $sheet_status, $conn::PARAM_INT);
                if($stmt->execute()) {
                    $updateStatus = 1;
                    
                }
            }else {
                $updateStatus = 1;
            }
            if($updateStatus == 1) {
                $url =  SITEURL . 'reimsheetconfirmation.php?status=1';
                header("location:$url");
            }
                //$sheetAssessmentData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }else {
            $url =  SITEURL . 'reimsheetconfirmation.php?status=0';
             header("location:$url");
        }
       // echo "<pre>";print_r($sheetAssessmentData);
        
    }
    
}

 
$obj = new NotificationCron();

if(isset($_POST['assessment_id']) && isset($_POST['confirm_sheet'])) {
    
      $obj->updateReimSheetStatus($_POST);
}else {
    $type = $argv[1];
    if ($type == 2) {
        $obj->reminderForReimbursementSheet();
    } else {

        $obj->reviewNotification();
    }
}

