<?php

require_once ( 'config.php');

class RemindersCron {

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

    function sendNotificationMail($notificationsMailTemplates, $data, $attachmentStatus, $conn, $lastReminder = 0, $lastReimbursementStatus) {

        //print_r($notificationsMailTemplates);die;
        $body = '';
        $body = str_replace('_name_', ucwords($data['name']), $notificationsMailTemplates['template_text']);
        $school_name = $data['client_name'] . ", " . $data['city_name'] . ", " . $data['state_name'];
        $subject = str_replace('_school_', $school_name, $notificationsMailTemplates['subject']);         
        $sender = $notificationsMailTemplates['sender'];
        $senderName = $notificationsMailTemplates['sender_name'];
        $toEmail = $data['email'];
        $toName = $data['name'];
        //$ccEmail = 'deepak.t@tatrasdata.com';
        $ccEmail = $notificationsMailTemplates['cc'];
        $ccName = '';
        $setCond = '';
        if(!$lastReminder){
            $link = "<a href=" . SITEURL . 'index.php?controller=login&action=login' . ">here</a>";
            $body = str_replace('_link_', $link, $body);
            
            $body = str_replace('_school_', $school_name, $body);
            $body = str_replace('_sdate_', date('d M Y', strtotime($data['sdate'])), $body);
            $body = str_replace('_edate_', date('d M Y', strtotime($data['edate'])), $body); 

            $sheetDaya = "+".REIMBURSEMENT_SHEET_DAYS." day";  
            $body = str_replace('_sheet_date_', date('d M Y', strtotime($sheetDaya, strtotime($data['edate']))), $body); 

           
        }else if($lastReminder){
            
            $sheetDaya = "+".LAST_REIMBURSEMENT_SHEET_DAYS." day";  
            $body = str_replace('_sheet_date_', date('d M Y', strtotime($sheetDaya, strtotime(date("Y-m-d")))), $body); 
        }
        if($lastReimbursementStatus) {            
            
            $sql = "SELECT * from d_notification_reminders_template_data`";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $remindersTemplateData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($data)) {
                
                $reimbursementdata = 0;
                $postReviewData = 0;
                $assessorPeerFeeback = 0;
                $body = str_replace('_bank_details_','', $body);
                $body = str_replace('_reimbursement_sheet_info_','', $body);
                if( empty($data['postReviewStatus']) && $data['sub_role_id'] == 1){
                    
                   $body =  str_replace('_post_review_', $remindersTemplateData[3]['template_data'], $body);
                }else {
                   $body = str_replace('_post_review_','', $body); 
                }
                if( empty($data['is_submit'])&& empty($data['is_peer_feedback'])){
                   $body =  str_replace('_assessor_self_peer_feedback_', $remindersTemplateData[2]['template_data'], $body);
                }else {
                   $body = str_replace('_assessor_self_peer_feedback_','', $body); 
                }
            }
           // die;
           
             //echo '<pre>';print_r($data);die;
        }
        $body = nl2br($body);
        if (sendEmail($sender, $senderName, $toEmail, $toName, $ccEmail, $ccName, $subject, $body, $attachmentPath = array())) {
            
           if($lastReminder)
                $setCond = ", final_email = :final_email";
            
             $sql = "UPDATE d_notification_queue SET date = :date $setCond
              WHERE user_id = :user_id AND assessment_id = :assessment_id AND type = :type";
              $stmt = $conn->prepare($sql);
              $date = date("Y-m-d");
              $type = 2;
              $stmt->bindParam(':date', $date, $conn::PARAM_INT);
              $stmt->bindParam(':type', $type, $conn::PARAM_INT);
              if(!empty($setCond)) {
                $final_email = 1;
                $stmt->bindParam(':final_email', $final_email, $conn::PARAM_INT);
              }
              $stmt->bindParam(':user_id', $data['user_id'], $conn::PARAM_INT);
              $stmt->bindParam(':assessment_id', $data['assessment_id'], $conn::PARAM_INT);
              
              $stmt->execute(); 
              
             /* if($lastReminder){
                  
                    $sql = "UPDATE d_notification_queue SET status = :status
                WHERE user_id = :user_id AND assessment_id = :assessment_id AND type = :type";
                $stmt = $conn->prepare($sql);
                $status = 0;
                $type = 2;
                $stmt->bindParam(':status', $status, $conn::PARAM_INT);
                $stmt->bindParam(':type', $type, $conn::PARAM_INT);
                $stmt->bindParam(':user_id', $data['user_id'], $conn::PARAM_INT);
                $stmt->bindParam(':assessment_id', $data['assessment_id'], $conn::PARAM_INT);
                $stmt->execute(); 
              }*/
        }
        // sendEmail($sender,$sender,$data['email'],$data['name'],$subject,$mail_body,'');   
    }

    function reviewReminders() {

        try {

            $conn = $this->connectToDB();
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $sql = " SELECT * FROM (SELECT date(rt.ratingInputDate) as ratingInputDate,q1.final_email,q1.date as rating_input_date,q1.type,un.sub_role_name,un.sub_role_id,q1.user_id as user,q1.notification_id,a.school_aqs_pref_start_date as sdate,a.school_aqs_pref_end_date as edate,cl.client_name,ct.city_name,
                        st.state_name ,d.assessment_id,pt.status as postReviewStatus,rm.sheet_status,fb.is_submit,fb2.is_submit as is_peer_feedback,u.name, u.email, u.user_id FROM `d_assessment` d
                        INNER JOIN d_client cl ON cl.client_id = d.client_id
                        INNER JOIN d_cities ct ON ct.city_id = cl.city_id
                        INNER JOIN d_states st ON st.state_id = cl.state_id
                        INNER JOIN d_notification_queue q1 ON q1.assessment_id = d.assessment_id 
                        INNER JOIN h_assessment_user rt ON q1.assessment_id = rt.assessment_id AND rt.isFilled = 1 AND rt.role = 4
                        
                        INNER JOIN d_AQS_data a ON d.aqsdata_id = a.id
                        INNER JOIN d_user u ON u.user_id = q1.user_id
                        INNER JOIN (SELECT r.sub_role_name,r.sub_role_id ,et.user_id,et.assessment_id FROM `h_assessment_external_team` et 
                         INNER JOIN d_user_sub_role r ON et.user_sub_role = r.sub_role_id                    
                         UNION SELECT 'Lead' as sub_role_name ,1 as sub_role_id ,et.user_id ,et.assessment_id
                         FROM `h_assessment_user` et INNER JOIN d_user_role r ON et.role = r.role_id  WHERE  et.role=4 
                        ) un ON un.assessment_id = q1.assessment_id  AND un.user_id = q1.user_id
                        LEFT JOIN d_feedback_submit fb ON fb.assessment_id = q1.assessment_id AND q1.user_id=fb.user_id AND fb.type=1 
                        LEFT JOIN d_feedback_submit fb2 ON fb2.assessment_id = q1.assessment_id AND q1.user_id=fb.user_id AND fb2.type= 2
                        LEFT JOIN d_post_review pt ON pt.assessment_id = d.assessment_id 
                        LEFT JOIN h_user_review_reim_sheet_status rm ON rm.assessment_id = d.assessment_id AND rm.user_id = un.user_id                                             
                       	WHERE q1.type=2 AND q1.status=1 ) t INNER JOIN h_user_review_notification q2 ON q2.assessment_id = t.assessment_id 
                        AND t.user_id=q2.user_id AND t.notification_id=q2.notification_id 
                        WHERE q2.type=2 ";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // echo "<pre>";print_r($reminders);die;
            $notificationsUsers = array();

            foreach ($reminders as $users) {
                //if(isset($notificationsUsers[$users['user_id']])) {
                // $notificationsUsers[$users['user_id']][] = $users; 
                //}else
                $notificationsUsers[$users['user_id']][$users['assessment_id']][$users['notification_id']] = $users;
            }
            
            //fetch template data
            // print_r($notificationsUsers);die;
            $sqlTemplate = " SELECT u.template_text,q.subject,q.sender,q.sender_name,q.cc FROM d_review_notification_template u "
                    . "INNER JOIN h_review_notification_mail_users q on u.id = q.notification_id ";
           

            // $notificationsUsers = array_slice($notificationsUsers,0,8);
            // 
            //echo "<pre>";print_r($notificationsUsers);die;
            $lastRemindersDays = "+".LAST_REMINDERS_DAYS." day";
            foreach ($notificationsUsers as $users) {


                foreach ($users as $userData) {
                    $sqlQuery = '';
                    $whrCond = '';
                    $ratingArray = array();
                    $attachmentStatus = 0;
                    $lastReimbursementStatus = 0;
                    $lastReminder = 0;
                    if (!empty($userData)) {
                        $ratingArray = array_column($userData, 'rating_input_date');
                        // print_r(array_column($userData,'rating_input_date'));
                    }
                    if(!empty($userData) && count($userData) == 1) {
                        $userData[10] = $userData[9];
                        //$lastReimbursementStatus = 0;
                        unset($userData[9]);
                    } 
                    if(!empty($userData[10]['is_submit']) && $userData[10]['is_submit'] == 1 && empty($userData[10]['is_peer_feedback'])) {
                            $userData[10]['is_submit'] = 0;
                        }else if(!empty($userData[10]['is_peer_feedback']) && $userData[10]['is_peer_feedback'] == 1 && empty($userData[10]['is_submit'])) {
                            $userData[10]['is_peer_feedback'] = 0;
                   }
                     //echo "<pre>";print_r($userData);
                    // echo $userData[0]['sub_role_name'];die;
                    /* if(!empty($userData) && count($userData) == 2) {
                      $whrCond =  " WHERE u.template_type = 4 " ;
                      }else */
                   // echo date("Y-m-d",strtotime("+8 day", strtotime($ratingArray[0])));
                    //die;
                    if (!empty($ratingArray[0]) && strtotime(date("Y-m-d")) == strtotime("+8 day", strtotime($ratingArray[0])) && empty($userData[10]['final_email']) ) {
                        
                        if(!empty($userData) && count($userData) == 1 && $userData[10]['sub_role_id']!= 8 && empty($userData[10]['is_submit']) && empty($userData[10]['is_peer_feedback']) ){
                            
                            $whrCond = " WHERE u.template_type = 9 ";
                        }else if (!empty($userData) && array_key_exists(10, $userData) && !in_array($userData[10]['sub_role_id'], array(1, 8))) {
                            if (empty($userData[10]['sheet_status']) && empty($userData[10]['is_submit']) && empty($userData[10]['is_peer_feedback'])) {
                                $whrCond = " WHERE u.template_type = 10 ";
                            } else if (empty($userData[10]['sheet_status']) && $userData[10]['is_submit'] == 1 && $userData[10]['is_peer_feedback'] == 1) {
                                $whrCond = " WHERE u.template_type = 11 ";
                            } else if ($userData[10]['sheet_status'] == 1 && empty($userData[10]['is_submit']) && empty($userData[10]['is_peer_feedback'])) {
                                $whrCond = " WHERE u.template_type = 12 ";
                            }
                        } else if (!empty($userData) && array_key_exists(10, $userData) && $userData[10]['sub_role_id'] == 1) {

                            if (empty($userData[10]['sheet_status']) && empty($userData[10]['postReviewStatus']) && empty($userData[10]['is_submit']) && empty($userData[10]['is_peer_feedback'])) {
                                $whrCond = " WHERE u.template_type = 13 ";
                            } else if (empty($userData[10]['sheet_status']) && empty($userData[10]['postReviewStatus']) && $userData[10]['is_submit'] == 1 && $userData[10]['is_peer_feedback'] == 1) {
                                $whrCond = " WHERE u.template_type = 14 ";
                            } else if (empty($userData[10]['sheet_status']) && $userData[10]['postReviewStatus'] == 1 && empty($userData[10]['is_submit']) && empty($userData[10]['is_peer_feedback'])) {
                                $whrCond = " WHERE u.template_type = 15 ";
                            } else if (empty($userData[10]['sheet_status']) && $userData[10]['postReviewStatus'] == 1 && $userData[10]['is_submit'] == 1 && $userData[10]['is_peer_feedback'] == 1) {
                                $whrCond = " WHERE u.template_type = 16 ";
                            } else if ($userData[10]['sheet_status'] == 1 && empty($userData[10]['postReviewStatus']) && empty($userData[10]['is_submit']) && empty($userData[10]['is_peer_feedback'])) {
                                $whrCond = " WHERE u.template_type = 17 ";
                            } else if ($userData[10]['sheet_status'] == 1 && $userData[10]['postReviewStatus'] == 1 && empty($userData[10]['is_submit']) && empty($userData[10]['is_peer_feedback'])) {
                                $whrCond = " WHERE u.template_type = 18 ";
                            } else if ($userData[10]['sheet_status'] == 1 && empty($userData[10]['postReviewStatus']) && $userData[10]['is_submit'] == 1 && $userData[10]['is_peer_feedback'] == 1) {
                                $whrCond = " WHERE u.template_type = 19 ";
                            }
                        } else if (!empty($userData) && !array_key_exists(10, $userData) && !in_array($userData[9]['sub_role_id'], array(1, 8))) {

                            if (empty($userData[10]['is_submit']) && empty($userData[10]['is_peer_feedback'])) {
                                $whrCond = " WHERE u.template_type = 9 ";
                            }
                        }
                    } /*else if (!empty($userData[10]['edate']) && strtotime(date("Y-m-d")) == strtotime($lastRemindersDays, strtotime($userData[10]['edate'])) && empty($userData[10]['sheet_status']) &&  empty($userData[10]['final_email'])) {
                        
                        $whrCond = " WHERE u.template_type = 20 ";
                        $lastReminder = 1;
                    }*/ else if (!empty($ratingArray[0]) && strtotime(date("Y-m-d")) ==  strtotime("+8 day", strtotime($ratingArray[0])) && empty($userData[10]['is_submit']) && empty($userData[10]['is_peer_feedback']) && !empty($userData[10]['final_email'])) {
                        
                        $whrCond = " WHERE u.template_type = 9 ";
                        $lastReimbursementStatus = 1;
                        $lastReminder = 0;
                    }
                   // echo $whrCond;die;
                    //$whrCond =  " WHERE u.template_type = 10" ;
                    if (!empty($whrCond)) {
                        $sqlQuery = $sqlTemplate . $whrCond;
                        $stmtTemplate = $conn->prepare($sqlQuery);
                        $stmtTemplate->execute();
                        $notificationsMailTemplates = $stmtTemplate->fetch();
                        foreach ($userData as $data) {
                            if (!empty($userData)) {
                                //echo "aaaa";
                                $this->sendNotificationMail($notificationsMailTemplates, $data, $attachmentStatus, $conn, $lastReminder,$lastReimbursementStatus);
                                break;
                            }
                        }
                    }
                }
                    
                    
                
                // echo "<pre>";print_r($notificationsUsers);die;
            }

           // echo "New records created successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }

}

$obj = new RemindersCron();
$obj->reviewReminders();
