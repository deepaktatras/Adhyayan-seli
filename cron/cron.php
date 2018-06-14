<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
//require_once ( '../config/config.php');
define("DEVELOPMENT_ENVIRONMENT",FALSE);
if(DEVELOPMENT_ENVIRONMENT){
        define("DB_USER","root");
	define("DB_PASSWORD","");
	define("DB_NAME","adh");
	define("DB_HOST","localhost");
}else{	
	define("DB_USER","adhyayan_s_app");
	define("DB_PASSWORD","ckLn5Ub_v_%k");
	define("DB_NAME","adhyayan_staging_app_adhyayan_reloaded");
	define("DB_HOST","localhost");
	define("SITEURL","http://staging-app.adhyayan.asia/");
}
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__).DS.'../');
//define("DEVELOPMENT_ENVIRONMENT",true);
 //require_once  ''.'application' . DS . 'helpers' . DS . "functionshelper.php" ;
require_once ( ROOT.'application' . DS .'helpers'.DS. 'functionshelper.php');
class Cron {

    function testCron() {

        try {
            $servername = DB_HOST;
            $dbname = DB_NAME;
            $username = DB_USER;
            $password = DB_PASSWORD;
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // prepare sql and bind parameters
            $stmt = $conn->prepare("INSERT INTO d_dummy (name)  VALUES ( :name)");
            $stmt->bindParam(':name', $firstname);
      

            // insert a row
            $firstname = "John";
           /* $lastname = "Doe";
            $email = "john@example.com";
            $stmt->execute();
        */
            $stmt->execute();

           // echo "New records created successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }
     function feedbackNotification() {

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
            /* $sql = " SELECT u.name,u.email,u.user_id,au.assessment_id FROM d_user u 
                    INNER JOIN (SELECT r.sub_role_name ,et.user_id,et.assessment_id FROM `h_assessment_external_team` et 
                    INNER JOIN d_user_sub_role r ON et.user_sub_role = r.sub_role_id AND et.user_role!=3                     
                    UNION SELECT r.role_name ,et.user_id ,et.assessment_id
                    FROM `h_assessment_user` et INNER JOIN d_user_role r ON et.role = r.role_id 
                    AND et.role!=3 AND et.isFilled!=1) au ON au.user_id = u.user_id ORDER by au.assessment_id";*/
          
            $notificationSql = " SELECT dn.notification_name,dn.id ,hn.assessment_id,hn.status,hn.user_id FROM `d_notifications` dn 
                                INNER JOIN h_user_review_notification hn ON hn.notification_id = dn.id WHERE hn.status = 1  ";
            $stmt = $conn->prepare($notificationSql);
            $stmt->execute();
            $allNotifications =  $stmt->fetchAll();
            //echo "<pre>";print_r($allNotifications);die;
              $notificationAssessorLogReviews = array();
              $notificationAssessors = array();
            foreach($allNotifications as  $notification){
                if(isset($notification['status']) && $notification['status'] == 1 && $notification['notification_name'] == 'assessor_log') {
                        $notificationAssessorLogReviews[] = $notification['assessment_id'];
                         $notificationAssessors[] = $notification['user_id'];
                    
                }
                
            }
            //echo "<pre>";print_r($notificationAssessors);die;
            /*$feedbackSql = " SELECT dn.notification_name,dn.id ,hn.assessment_id,hn.status 
                FROM `d_notifications` dn INNER JOIN h_user_review_notification hn ON hn.notification_id = dn.id
                LEFT JOIN d_feedback_submit fs ON fs.assessment_id = hn.assessment_id 
                WHERE fs.is_submit IS null or fs.is_submit !=1 ";
            $stmt = $conn->prepare($feedbackSql);
            $stmt->execute();
            $allFeedbacks =  $stmt->fetchAll();
            
          
            $allFeedbacks = array_column($allFeedbacks, null, 'assessment_id');
            $notificationAssessorLogReviews = array();
            foreach ($allFeedbacks as $notification ) {
                //echo "<pre>";print_r($notification);die;
                if(isset($notification['status']) && $notification['status'] == 1 && $notification['notification_name'] == 'assessor_log') {
                        $notificationAssessorLogReviews[] = $notification['assessment_id'];
                    
                }
                
            }*/
            
            
           /* echo "<pre>";print_r($allFeedbacks);die;
            
            $notificationSql = " SELECT dn.notification_label,dn.notification_name,dn.id ,hn.notification_id,hn.assessment_id,hn.status FROM `d_notifications` dn "
                    . "LEFT JOIN h_user_review_notification hn ON hn.notification_id = dn.id WHERE hn.status = 1  ";
            $stmt = $conn->prepare($notificationSql);
            $stmt->execute();
            $allNotifications =  $stmt->fetchAll();
            */
            
                 $sql = " SELECT ru.name, ru.email, ru.user_id, ru.assessment_id,ru.mail_content, ru.ratingInputDate, ru.isFilled FROM h_user_review_notification hru"
                    . " INNER JOIN (SELECT fass.name, fass.email, fass.user_id, fass.assessment_id,fass.mail_content, ass_u.ratingInputDate, ass_u.isFilled FROM h_assessment_user ass_u "
                    . "INNER JOIN ( SELECT u.name, u.email, u.user_id, au.assessment_id,au.mail_content FROM d_user u "
                    . "INNER JOIN ( SELECT r.sub_role_name, et.user_id, et.assessment_id,mcu.mail_content FROM `h_assessment_external_team` et "
                    ." LEFT JOIN (SELECT mc.mail_content,mrc.sub_role FROM d_mail_content mc INNER JOIN h_school_review_mail_sub_role mrc ON mrc.mail_content_id = mc.id) mcu ON mcu.sub_role=et.user_sub_role"
                    ." LEFT JOIN d_feedback_submit fs ON fs.assessment_id = et.assessment_id "
                    . "INNER JOIN d_user_sub_role r ON et.user_sub_role = r.sub_role_id AND et.user_role != 3 WHERE (fs.is_submit is null or fs.is_submit !=1) AND et.user_id IN ( ". implode(",",$notificationAssessors).") AND  et.assessment_id IN ( ". implode(",",$notificationAssessorLogReviews).")"
                    . "UNION SELECT r.role_name, et.user_id, et.assessment_id,mcu.mail_content FROM `h_assessment_user` et "
                    ." LEFT JOIN (SELECT mc.mail_content,mrc.role FROM d_mail_content mc INNER JOIN h_school_review_mail_role mrc ON mrc.mail_content_id = mc.id) mcu ON mcu.role=et.role"
                    ." LEFT JOIN d_feedback_submit fs ON fs.assessment_id = et.assessment_id "
                    . "INNER JOIN d_user_role r ON et.role = r.role_id AND et.role != 3 AND et.isFilled = 1 WHERE (fs.is_submit is null or fs.is_submit !=1) AND et.user_id IN ( ". implode(",",$notificationAssessors).") AND et.assessment_id IN ( ". implode(",",$notificationAssessorLogReviews).") ) au ON au.user_id = u.user_id "
                    . "ORDER BY au.assessment_id ) fass ON fass.assessment_id = ass_u.assessment_id "
                    . "WHERE ass_u.isFilled = 1 GROUP BY fass.assessment_id,fass.user_id ) ru ON ru.user_id = hru.user_id AND ru.assessment_id = hru.assessment_id  ";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $assessments =  $stmt->fetchAll();
           // echo "<pre>";print_r($assessments);die;
            //$assessments = $assessmentModel->getFeedbackAssessment(current($this->user['role_ids']));
            $body_mail = "Please give feedback for your assessment.";
            $subject = "Assessment Feedback";
            $i = 0;
            $notificationMailSql = " SELECT from_email,from_name,time_interval FROM d_notification_mail_details WHERE notification_id = 8 AND email_num = 1 ";
            $stmt = $conn->prepare($notificationMailSql);
            $stmt->execute();
            $allMailDetails =  $stmt->fetchAll();
            
            //email interval for second time
                $notificationMailSql2 = " SELECT from_email,from_name,time_interval FROM d_notification_mail_details WHERE notification_id = 8 AND email_num = 2 ";
                $stmt = $conn->prepare($notificationMailSql2);
                $stmt->execute();
                $allMailDetails2 = $stmt->fetchAll();

           // echo "<pre>";print_r($assessments);
            foreach($assessments as $data) {
                
                
                $notificationDate = '';
                if(!empty($data['ratingInputDate'])) {
                    
                    // fetch from notification log to send anotger reminder email .
                    $notificationMailLogSql = " SELECT assessment_id,user_id,date FROM h_review_notification_log WHERE assessment_id =". $data['assessment_id']."  AND user_id = ".$data['user_id']." ORDER BY date desc ";
                    $stmt = $conn->prepare($notificationMailLogSql);
                    $stmt->execute();
                    $notificationMailLogDetails =  $stmt->fetchAll();                     
                    if(!empty($notificationMailLogDetails)) {
                        
                        $time_interval = "+".$allMailDetails2[0]['time_interval'];
                        $previousEmailDate = $notificationMailLogDetails[0]['date'];
                        $notificationDate =new DateTime( date('Y-m-d h:i:s',strtotime($time_interval,strtotime($previousEmailDate))));
                    }else {
                        //print_r($notificationMailLogDetails);die;
                        //$notificationDate = date("Y-m-d H:i:s", strtotime('+48 hours'));
                         $time_interval = "+".$allMailDetails[0]['time_interval'];
                         $notificationDate =new DateTime( date('Y-m-d h:i:s',strtotime($time_interval,strtotime($data['ratingInputDate']))));
                    }
                    //print_r($notificationMailLogDetails);die;
                    if( new DateTime("now") >= $notificationDate){
                        
                        //echo "lll";
                        $i++;
                        $body_mail = '';
                        $link = '';
                        $fromEmail = $allMailDetails[0]['from_email'];
                        $fromName = $allMailDetails[0]['from_name'];
                        $toEmail = 'kalpesh@adhyayan.asia';
                        $toName = 'Adhyayan';
                        $cc = 'pooja.s@tatrasdata.com';  
                        $link = 'http://staging-app.adhyayan.asia/index.php?controller=diagnostic&action=feedbackForm&assessment_id='.$data['assessment_id'].'&user_id='.$data['user_id'];
                        //$link = "<a href='".$link."'>Click here</a>";
                        //$body_mail =  'Hi '.$data['name'].",".'<br>'.$body_mail;
                        //$body_mail .= $link." for feedback.";
                        $body_mail = str_replace('_name', $data['name'], $data['mail_content']);
                        $body_mail = str_replace('_link', $link, $body_mail);
                       // if($i < 5) {
                           
                           // echo $body_mail;
                            if(sendEmail($fromEmail,$fromName,$toEmail,$toName,$subject,$body_mail,$cc)) {
                                 //echo "k";
                                $statement = $conn->prepare("INSERT INTO h_review_notification_log(notification_id, assessment_id, user_id,mail_content,date)
                                    VALUES(:n_id, :a_id,:u_id,:mail,:date)");
                                $statement->execute(array(
                                    "n_id" => "8",
                                    "a_id" => $data['assessment_id'],
                                    "u_id" => $data['user_id'],
                                    "mail" => $body_mail,
                                    "date" => date("Y-m-d h:i:s")
                                ));
                                //$stmt = $conn->prepare($sql);
                                //$stmt->execute();
                                //$assessments =  $stmt->fetchAll();
                            }
                        //}
                    }
                }
            }
            //echo "New records created successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }

}

$obj = new Cron();
$obj->feedbackNotification();
