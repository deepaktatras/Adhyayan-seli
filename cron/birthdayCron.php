<?php
date_default_timezone_set('Asia/Calcutta');
require_once ( 'config.php');
class BirthdayCron {

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

            //echo "New records created successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }
    
    function birhdayGreetings() {

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
            $todayDay=date("d");
            $todayMonth=date("m");
            
            //$todayDay=23;
            //$todayMonth=12;
            
            
            $sql = "select hup.date_of_birth,du.name,du.email,du.user_id from h_user_profile hup inner join d_user du on hup.user_id=du.user_id left join d_client 
                     dc on du.client_id=dc.client_id where  ";
            
            if( date("Y") % 4 != 0  && $todayDay==28 && $todayMonth==02){
                $sql.=" MONTH(hup.date_of_birth)=$todayMonth && (DAY(hup.date_of_birth)=$todayDay || DAY(hup.date_of_birth)=29)";  
            }else{
                $sql.=" MONTH(hup.date_of_birth)=$todayMonth && DAY(hup.date_of_birth)=$todayDay";
            }
            //echo $sql;
            $stmt = $conn->prepare($sql);
                
            $stmt->execute();
            $notifications =  $stmt->fetchAll();            
           
            $sqlTemplate = " SELECT u.template_text,q.subject,q.sender,q.sender_name ,q.cc,q.status FROM d_review_notification_template u "
                        . "INNER JOIN h_review_notification_mail_users q on u.id = q.notification_id where u.template_type=99";
            
                $sqlQuery = $sqlTemplate;
                $stmtTemplate = $conn->prepare($sqlQuery);
                $stmtTemplate->execute();
                $notificationsMailTemplates =  $stmtTemplate->fetch();
                $templates_message=$notificationsMailTemplates['template_text'];
                
                if($notificationsMailTemplates['status']==1){
                //print_r($notificationsMailTemplates);
                //print_r($notifications);
                foreach($notifications as $users) {
                
                $toName=$users['name'];
                $toEmail=$users['email'];
                $subject=$notificationsMailTemplates['subject'];
                $sender=$notificationsMailTemplates['sender'];
                $ccEmail=$notificationsMailTemplates['cc'];
                $ccName="";
                $mail_body = str_replace('_name_',$toName,$templates_message) ;
                $mail_body = nl2br(str_replace('--img--',"<img src='cid:birthday_image'>",$mail_body)) ;
                $senderName = $notificationsMailTemplates['sender_name'];
                $inlineImage=array();
                $inlineImage['birthday_image']='notification_files/birthday.png';;
                if(sendEmail($sender,$senderName,$toEmail,$toName,$ccEmail,$ccName,$subject,$mail_body,array(),$inlineImage)){
                $sql = "INSERT INTO h_birthday_greetings_log (user_id,status,sent_date ) values (:user_id,:status,:sent_date)";
                $stmt = $conn->prepare($sql); 
                $status = 1;
                $stmt->execute(array("user_id"=>$users['user_id'],"status"=>$status,"sent_date"=>date("Y-m-d H:i:s"))); 
                }else{
                $sql = "INSERT INTO h_birthday_greetings_log (user_id,status,sent_date ) values (:user_id,:status,:sent_date)";
                $stmt = $conn->prepare($sql); 
                $status = 0;
                $stmt->execute(array("user_id"=>$users['user_id'],"status"=>$status,"sent_date"=>date("Y-m-d H:i:s")));
                }
                
                }
                
                }
            
                
                
               // echo "<pre>";print_r($notificationsUsers);die;
         

            //echo "New records created successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }
     

}

$obj = new BirthdayCron();
$obj->birhdayGreetings();
