<?php

require_once ( 'config.php');

//require_once ( "../../library/individualreport.class.php");
class ActionPlanCron {
    
    protected $db;
    function __construct(){
        try {
            $servername = DB_HOST;
            $dbname = DB_NAME;
            $username = DB_USER;
            $password = DB_PASSWORD;
            $this->db =  new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
        
    }
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

    function sendNotificationMail($notificationsMailTemplates,$leader_email,$leader_name,$school_name,$report_date,$reportName='',$mail_type=0){

        //print_r($actionPlanTemplate);die;
        $body = '';
        $body = str_replace('_name_', ucwords($leader_name), $notificationsMailTemplates['template_text']);
        $body = str_replace('_date_', date("d-m-Y",strtotime($report_date)), $body);
        //$school_name = $data['client_name'] . ", " . $data['city_name'] . ", " . $data['state_name'];
        $subject = str_replace('_school_', $school_name, $notificationsMailTemplates['subject']);         
        $sender = $notificationsMailTemplates['sender'];
        $senderName = $notificationsMailTemplates['sender_name'];
        $toEmail = $leader_email;
        $toName = $leader_name;
        //$ccEmail = 'deepak.t@tatrasdata.com';
        $ccEmail = $notificationsMailTemplates['cc'];
        $ccName = '';
        $setCond = '';
        $attachmentPath = array();
        if(!empty($reportName)) {
            $file_path = trim(CRON_ROOT,"library");
            $file_path .= UPLOAD_NOTIFICATION_PDF_PATH;
            $attachmentPath []= $file_path.$reportName.'.pdf';
            //$attachmentPath []= 'notification_files/ReimbursementSheet.xls';
        }
        //echo CRON_ROOT;
        //print_r($attachmentPath);die;
        $body = nl2br($body);
        if (sendEmail($sender, $senderName, $toEmail, $toName, $ccEmail, $ccName, $subject, $body, $attachmentPath,'',1)) {
          
              //echo "yes";
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

    function getAllActionPlans() {

        try {

           
            //print_r($reportDateArray);die;
            //fetch template data
            // print_r($notificationsUsers);die;
            $allActionPlan = $this->getReportData();
            //echo "<pre>";print_r($allActionPlan);die;
            $notificationsUsers = array();
            $reportDateArray = array();
            foreach ($allActionPlan as $data) {
                
                $todayDate = date("Y-m-d");
                
                $reportingDate = '';
                $addTime = '';
                $reportDate = $data['from_date'];
                //echo "to-".  $data['to_date'];
                if(isset($data['to_date']) && $data['to_date']>= $todayDate ){
                    
                        while($reportDate < $data['to_date']){
                            
                             
                            $from_date = date('Y-m-d', strtotime("-1 day", strtotime($reportDate)));
                            $reportDate = date('Y-m-d', strtotime($from_date . $data['frequency_days']));
                            $from_date = date('Y-m-d', strtotime("+1 day", strtotime($from_date)));
                           // $reportDate =date('Y-m-d', strtotime($reportDate . $data['frequency_days']));
                            if($reportDate <= $data['to_date'] && $reportDate >= $todayDate){
                                
                                if($reportDate == date('Y-m-d', strtotime($todayDate . "+3 days")))  {
                                    
                                    $emailData =array('assessment_id'=>$data['assessment_id'],'leader_name'=>$data['leader_name'],'leader_email'=>$data['leader_email'],'school_name'=>$data['client_name'],'report_date'=>$reportDate) ;
                                    if($data['leader_email'] != $data['email']){
                                      
                                        $emailData['principle_name'] = $data['name'];
                                        $emailData['principle_email'] = $data['email'];
                                    }
                                     $reportDateArray[] = $emailData;
                                   //$reportDateArray[] =array('name'=>$data['name'],'email'=>$data['email'],'school_name'=>$data['client_name'],'report_date'=>$reportDate) ;
                                }
                            }
                            $reportDate = date('Y-m-d', strtotime("+1 day", strtotime($reportDate)));
                        }
                }
               
            }
            $actionPlanTemplateArray = $this->getTemplateData(23);
            $actionPlanTemplate = array();
           // echo "<pre>"; print_r($reportDateArray);die;
            foreach ($reportDateArray as $users) {
                    
                $actionPlanTemplate = $actionPlanTemplateArray;
                $link = "<a href=" . SITEURL . 'index.php?controller=actionplan&action=actionplan1&assessment_id='.$users['assessment_id'] . ">here</a>";
                $actionPlanTemplate['template_text'] = str_replace('_here_', $link, $actionPlanTemplateArray['template_text']);
                if (!empty($users['leader_email'])) {                    
                            //echo "aaaa";
                            $this->sendNotificationMail($actionPlanTemplate,$users['leader_email'],$users['leader_name'],$users['school_name'],$users['report_date']);
                }if (!empty($users['principle_email'])) {                    
                            //echo "aaaa";
                            $this->sendNotificationMail($actionPlanTemplate,$users['principle_email'],$users['principle_name'],$users['school_name'],$users['report_date']);
                }
                    
            }
           // echo "New records created successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }
    
    function getActionPlansReports() {

        try {

           
           
            $allActionPlan = $this->getReportData();
            //echo "<pre>";print_r($allActionPlan);die;
            $notificationsUsers = array();
            $reportDateArray = array();
            foreach ($allActionPlan as $data) {
                
                $todayDate = date("Y-m-d");
                
                $reportingDate = '';
                $addTime = '';
                $data['from_date'];
                //$reportDate =date ("Y-m-d", strtotime("-1 day", strtotime($data['from_date'])));
                $reportDate =$data['from_date'];
                $from_date = '';
                //echo "to-".  $data['to_date'];
               // echo "to-".$data['assessment_id'];
                if(isset($data['to_date']) && $data['to_date']>= $todayDate ){
                    
                        while($reportDate < $data['to_date']){
                            
                            $from_date = date('Y-m-d', strtotime("-1 day", strtotime($reportDate)));
                            $reportDate = date('Y-m-d', strtotime($from_date . $data['frequency_days']));
                            $from_date = date('Y-m-d', strtotime("+1 day", strtotime($from_date)));
                            if($reportDate <= $data['to_date'] && $reportDate == $todayDate){
                                
                                //if($reportDate == date('Y-m-d', strtotime($todayDate . "+3 days")))  {
                                    
                                   
                                    $emailData =array('assessment_id'=>$data['assessment_id'],'action_plan_id'=>$data['id'],'leader_name'=>$data['leader_name'],'leader_email'=>$data['leader_email'],'school_name'=>$data['client_name'],'report_date'=>$reportDate,'from_date'=>$from_date,'to_date'=>$reportDate) ;
                                    if($data['leader_email'] != $data['email']){
                                      
                                        $emailData['principle_name'] = $data['name'];
                                        $emailData['principle_email'] = $data['email'];
                                    }
                                    //echo "to-".$data['assessment_id'];
                                    $emailData['reporting_authority_email']=$data['reporting_authority'];
                                    $reportDateArray[] = $emailData;
                                    // $reportDateArray[] = array('from_date'=>'','to_date'=>'');
                                   //$reportDateArray[] =array('name'=>$data['name'],'email'=>$data['email'],'school_name'=>$data['client_name'],'report_date'=>$reportDate) ;
                                //}
                            }
                            $reportDate = date('Y-m-d', strtotime("+1 day", strtotime($reportDate)));
                        }
                    
                }
                
            // echo "<pre>"; print_r($reportDateArray);
               
            }
           // echo "<pre>"; print_r($reportDateArray);die;
            
            $actionPlanMailTemplate = $this->getTemplateData(24);
           // "<pre>";print_r($actionPlanTemplate);die;
           // print_r($reportDateArray);die;
            $actionModel = new actionModel();
           
            foreach ($reportDateArray as $users) {
                    $actionPlanTemplate = $actionPlanMailTemplate;
                    $details = $actionModel->getDetailsofAssessment($users['action_plan_id']);
                    $reportName = $this->createReportPdf1($users,$details);
                    $actionPlanTemplate['template_text'] = str_replace('_from_date_', date("d-m-Y",strtotime($users['from_date'])),$actionPlanTemplate['template_text']);
                    $actionPlanTemplate['template_text'] = str_replace('_to_date_', date("d-m-Y",strtotime($users['to_date'])),$actionPlanTemplate['template_text']);
            
                    if (!empty($users['leader_email'])) {                    
                                //echo "aaaa";die;
                                $this->sendNotificationMail($actionPlanTemplate,$users['leader_email'],$users['leader_name'],$users['school_name'],$users['report_date'],$reportName,3);
                    }if (!empty($users['principle_email'])) {                    
                                //echo "aaaa";
                                $this->sendNotificationMail($actionPlanTemplate,$users['principle_email'],$users['principle_name'],$users['school_name'],$users['report_date'],$reportName,3);
                    }if(!empty($users['reporting_authority_email'])){
                        
                        $allEmails = explode(',', $users['reporting_authority_email']);
                        foreach($allEmails as $key=>$val){
                            if($val!=$users['leader_email'] ){
                                if(empty($users['principle_email']) || $val!=$users['principle_email'] ){
                                    $name = 'Sir/Madam';
                                    $this->sendNotificationMail($actionPlanTemplate,$val,$name,$users['school_name'],$users['report_date'],$reportName,3); 
                                }
                            }
                            
                        }
                    }
                   // echo "f";
                    
            }
           // echo "New records created successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }
    
    public function createReportPdf1($users,$details=array()){
        
        $from_date = $users['from_date'];
        $to_date = $users['to_date'];
        
        //$apiObject=new apiController('','login');
        $dateRange = $users['from_date']."/".$users['to_date'];
      
        $data = $this->actionplandataAction($users['action_plan_id'],$users['assessment_id'],$dateRange,'image');
        //echo "<pre>";print_r($data);die;
        $datafile = $this->actionplanchartsave($users['action_plan_id'],$users['assessment_id'],$dateRange,$data,'local',$from_date,$to_date);
      // echo "<pre>";print_r($data);die;
        $reportObject=new individualReport($users['assessment_id'],0);   
        $digmodel = new diagnosticModel();
        $assessment_details = $digmodel->getAssessmentById($users['assessment_id'],DEFAULT_LANGUAGE,1);
        $rating_date = '';
        if(!empty($assessment_details)) {
            if($assessment_details['assessment_type_id'] == 1 && $assessment_details['subAssessmentType'] == 1){
                $rating_date = !empty($assessment_details['rating_date'])?date("d-m-Y",strtotime($assessment_details['rating_date'])):'';
            }else{
                $rating_date = !empty($assessment_details['school_aqs_pref_end_date'])?date("d-m-Y",strtotime($assessment_details['school_aqs_pref_end_date'])):'';
            }           
        }
        //echo '<pre>';print_r($assessment);die;
        return  $reportObject->actionPlanOutput($users['action_plan_id'],$users['from_date'],$users['to_date'],'',1,$details,$rating_date,$datafile,1);
        
				
        
    }
     public function actionplandataAction($action_plan_id,$assessment_id,$dateRange,$type){
        
        //print_r($_POST);
        //die();
        $id_c=$action_plan_id;
        $assessment_id=$assessment_id;
        $actionModel=new actionModel();
        $aqsDataModel = new aqsDataModel();
        $details=$actionModel->getDetailsofAssessment($id_c);
        //print_r($details);
        $datesrange=$dateRange;
        $type=$type;
        
        /*if(!isset($_POST['datesrange'])){
        $from_date=$details['from_date'];
        $to_date=$details['to_date'];
        }else{
          $datesrangeex=explode("/",$datesrange);
          $from_date=$datesrangeex['0'];
          $to_date=$datesrangeex['1'];
        }*/
        
        if(isset($datesrange) && !empty($datesrange)){
             $datesrangeex=explode("/",$datesrange);
             $from_date=$datesrangeex['0'];
             $to_date=$datesrangeex['1'];
        }else{
            
            if(isset($type) && $type=="image"){
                $date=(isset($details['from_date']) && $details['from_date']!="0000-00-00")?$details['from_date']:'';
                $date_start_real=(isset($details['from_date']) && $details['from_date']!="0000-00-00")?$details['from_date']:'';
                $date_end_real=(isset($details['to_date']) && $details['to_date']!="0000-00-00")?$details['to_date']:date("Y-m-d");
                $end_date =$date_end_real;

                $array_dates=array();
                $ii=0;
                if(!empty($date)){

                    $ii=0;   
                    while (strtotime($date) <= strtotime($end_date)) {
                             $sdate=date ("Y-m-d",strtotime($date));

                             $dateex = date ("Y-m-d", strtotime("".$details['frequency_days']."", strtotime($date)));

                             $date = date ("Y-m-d", strtotime("-1 day", strtotime($dateex)));

                             if($date>$date_end_real){
                             $dateex = date ("Y-m-d", strtotime("+1 day", strtotime($date_end_real)));
                             $date = date ("Y-m-d", strtotime("-1 day", strtotime($dateex)));  
                             $array_dates[$ii]['fromDate']=$sdate;
                             $array_dates[$ii]['endDate']=$date_end_real;
                             $date = date ("Y-m-d", strtotime("+1 day", strtotime($date_end_real)));
                             $ii++;
                             break;
                             }else{

                             $array_dates[$ii]['fromDate']=$sdate;
                             $array_dates[$ii]['endDate']=$date;
                             $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
                             }
                       $ii++;
                    }

                }


                if($ii==0){
                $sdate=date ("Y-m-d",strtotime($date));
                $dateex = date ("Y-m-d", strtotime($end_date));
                $array_dates[$ii]['fromDate']=$sdate;
                $array_dates[$ii]['endDate']=$dateex;
                
                }

                //echo"<pre>";
                //print_r($array_dates);
                $array_dates_f=array();
                foreach($array_dates as $key=>$val){

                    $array_dates_f[]=$val;

                }
                
                $from_date=$details['from_date'];
                $to_date=$details['to_date'];
                foreach($array_dates_f as $key=>$val){
                        
                        if($val['endDate']<=date("Y-m-d")){
                         $to_date = $val['endDate'];   
                        }
                }
                
                
                
            }else{
            $from_date=$details['from_date'];
            $to_date=$details['to_date'];
            }
            
            
        }
        
        $frequency=$details['frequency_days'];
        
        //echo "<pre>";
        //print_r($details);
        
        $begin = new DateTime($from_date);
        $end = new DateTime($to_date);
        $end = $end->modify('+1 day'); 
        
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        
        //echo "<pre>";print_r($details);
        //print_r($period);
        //die();
        $activity=$aqsDataModel->getActivity();
        
        $h_assessor_action1_id=isset($details['h_assessor_action1_id'])?$details['h_assessor_action1_id']:'';
        $activityDetails=$actionModel->getActivityAction2($h_assessor_action1_id);
        
        
                
        $activityDetails_final=$this->array_grouping($activityDetails,"activity","");
        
       
        //echo"<pre>";
        //print_r($activityDetails_final);
        //echo"</pre>";
        //die();
        
        $activity_data=array();
        $a=array();
        $date_count=array();
        $tickarray=array();
        foreach($activity as $key=>$val){
        $data_p=array();
        $categories=array();
        $data_c=array();
        $data_e=array();
        
        $activity_group=isset($activityDetails_final[$val['activity_id']])?$activityDetails_final[$val['activity_id']]:array();
        //echo"<pre>";
        //print_r($activity_group);
        //echo"</pre>";
        // die();
        if(count($activity_group)>0){
        $activity_data_c=array();
        $activity_data_p=array();
        $activity_data_e=array();
        //echo "aaa";echo "<pre>";print_r($activity_group);die;
        $group_status_overall=$this->array_grouping($activity_group,"activity_status","");
        
        $status_notstarted_overall=isset($group_status_overall[0])?$group_status_overall[0]:array();
        $status_started_overall=isset($group_status_overall[1])?$group_status_overall[1]:array();
        $status_completed_overall=isset($group_status_overall[2])?$group_status_overall[2]:array();
        
        $status_started_overall_date=$this->array_grouping($status_started_overall,"activity_date","");
        $status_notstarted_overall_date=$this->array_grouping($status_notstarted_overall,"activity_date","");
        $exp=0;
        $expp=0;
        foreach($status_started_overall_date as $expkey=>$expval){
            if($expkey<date("Y-m-d")){
                $exp++;
            }else{
              $expp++;  
            }
        }
        
        foreach($status_notstarted_overall_date as $expkey=>$expval){
            if($expkey<date("Y-m-d")){
                $exp++;
            }else{
              $expp++;  
            }
        }
        //echo $expp;
        //echo $exp;
        //print_r($status_started_overall_date);
        //print_r($status_notstarted_overall_date);
        //die();
        if(count($status_completed_overall)>0){
        $activity_data_c["name"]= ''.$val['activity'].'-C';
        $activity_data_c["color"]='rgba(3, 168, 3,1)';
        $activity_data_c["marker"]=array("symbol"=>isset($val['symbol'])?$val['symbol']:'');
        }
        
        
        if((count($status_started_overall)>0 || count($status_notstarted_overall)>0) && $expp>0){
        $activity_data_p=array("name"=> ''.$val['activity'].'-P',
        "color"=> 'rgba(255,140,0,1)');
        $activity_data_p["marker"]=array("symbol"=>isset($val['symbol'])?$val['symbol']:'');
        }
        
        if((count($status_started_overall)>0 || count($status_notstarted_overall)>0) && $exp>0){
        $activity_data_e=array("name"=> ''.$val['activity'].'-Ex',
        "color"=> 'rgba(255,0,0,1)');
        $activity_data_e["marker"]=array("symbol"=>isset($val['symbol'])?$val['symbol']:'');
        }
        
        $activity_group_date=$this->array_grouping($activity_group,"activity_date","");
        
        foreach ($period as $dt) {
            
        $date=$dt->format("Y-m-d");
        
        if($date==date("Y-m-d")){
        $categories[]='Today';
        //$tickarray[]=strtotime($date)*1000;
        }else if($date==$from_date || $date==$to_date){
        $categories[]=date("d-m-Y",strtotime($date));    
        //$tickarray[]=strtotime($date)*1000;
        }else{
        $categories[]='';
        }
        
        $final_date=isset($activity_group_date[$date])?$activity_group_date[$date]:array();
        $group_status=$this->array_grouping($final_date,"activity_status","");
        
        if(count($group_status)>0){
        //echo"<pre>";
        //print_r($group_status);
        //echo"</pre>";
        }
        
        $status_notstarted=isset($group_status[0])?$group_status[0]:array();
        $status_started=isset($group_status[1])?$group_status[1]:array();
        $status_completed=isset($group_status[2])?$group_status[2]:array();
        
        $expcount=0;
        $pendcount=0;
        foreach($status_started as $k1=>$v1){
            if($v1['activity_date']<date("Y-m-d")){
              $expcount++;  
            }else{
              $pendcount++;                                                                                  
            }
        }
        
        foreach($status_notstarted as $k2=>$v2){
            if($v2['activity_date']<date("Y-m-d")){
              $expcount++;  
            }else{
              $pendcount++;                                                                                  
            }
        }
        
        $start=(isset($date_count[$date])?$date_count[$date]:0)*0.25;
        //$gmtTimezone = new DateTimeZone('GMT');
        date_default_timezone_set('UTC');
        $myDateTime = strtotime($date)*1000;
        
        if($date==date("Y-m-d") || $date==$from_date || $date==$to_date){
        if(!in_array($myDateTime,$tickarray)){    
        $tickarray[]=$myDateTime;
        }
        }
        //$myDateTime = strtotime("".$date." 12");
        //$dateutc=DateTime::createFromFormat( 'U',strtotime($date));
        //$myDateTime=$date->getTimestamp();
        $tot_point=0;
        if(count($status_completed)>0){
        $data_c[]=array($myDateTime,$start);
        $start=$start+0.25;
        
        $tot_point++;
        }else{
        $data_c[]=array($myDateTime,-1);    
        }
        
        if(count($status_started)>0 && $pendcount>0){
        $data_p[]=array($myDateTime,$start);
        $start=$start+0.25;
        $tot_point++;
        }else if(count($status_started)==0 && count($status_notstarted)>0 && $pendcount>0){
        $data_p[]=array($myDateTime,$start);
        $start=$start+0.25;
        
        $tot_point++;
        }else{
          $data_p[]=array($myDateTime,-1);  
        }
        
        
        if(count($status_started)>0 && $expcount>0){
        $data_e[]=array($myDateTime,$start);
        $start=$start+0.25;
        $tot_point++;
        }else if(count($status_started)==0 && count($status_notstarted)>0 && $expcount>0){
        $data_e[]=array($myDateTime,$start);
        $start=$start+0.25;
        
        $tot_point++;
        }else{
          $data_e[]=array($myDateTime,-1);  
        }
        
        $date_count[$date]=(isset($date_count[$date])?$date_count[$date]:0)+$tot_point;
        
        }
        
        $activity_data_c['data']=$data_c;
        $activity_data_p['data']=$data_p;
        $activity_data_e['data']=$data_e;
        
        if(count($status_completed_overall)>0){
        $a[]=$activity_data_c;
        }
        
        if((count($status_started_overall)>0 || count($status_notstarted_overall)>0) && $expp>0){
        $a[]=$activity_data_p;
        }
        
        if((count($status_started_overall)>0 || count($status_notstarted_overall)>0) && $exp>0){
        $a[]=$activity_data_e;
        }
        
       
        
        }
        
        }
        
        //print_r($a);
        //die();
        /*$a=array(array( "name"=> 'Research-C',
        "color"=> 'rgba(0,128,0, .5)',
        "data"=> array(0,'', 0, '', 0),
        "marker"=>array("symbol"=> 'triangle')
        ),
        array( "name"=> 'Research-P',
        "color"=> 'rgba(255,165,0, .5)',
        "data"=> array(0.75,'', 0, '', 0),
        "marker"=>array("symbol"=> 'triangle')
        )    
        );*/
        //echo $date;
        //$xais=array('categories'=>$categories);
        // Date.UTC(2015, 1, 12) // feb 12, 2015
        
        
        $xais=array('type'=>'datetime',
            'dateTimeLabelFormats'=>array('second'=>'%H:%M:%S',
                                          'minute'=>'%H:%M',
                                          'hour'=>'%H:%M',
                                          'day' => '%e. %b',
                                          'week' => '%e. %b',
                                          'month' => '%b \'%y',
                                          'year' => '%Y'                                                      
                                     ),
            'tickInterval'=> 24 * 3600 * 1000,
             'tickPositions'=> $tickarray,
            'labels' =>array("format"=>'{value:%e .%m. %y}','style'=>array("color"=>"#000000"),'autoRotation'=>array (-10, -20, -30, -40, -50, -60, -70, -80, -90)),
            'lineColor'=>'#000000',
            'gridLineColor'=>'#000000',
            'tickColor'=> '#000000',
            
            );
        $apiResult ["data"]=$a;
        $apiResult ["xaxis"]=$xais;
        $apiResult ["status"] = 1;
        return $apiResult;
        
    }
    function actionplanchartsave($action_plan_id,$assessment_id,$daterange,$data,$type,$from_date,$to_date){
         
         
  //echo "<pre>"; print_r($data);   
  $exportSettings = array (
  'options' => 
  array (
    'chart' => 
    array (
      'type' => 'scatter',
      'backgroundColor' => 'transparent',
      'zoomType' => 'xy',
      'borderColor' => '#000000',
      'borderWidth' => 0,
    ),
    'title' => 
    array (
      'text' => '',
    ),
    'xAxis' => $data['xaxis'],
   
    'yAxis' => 
    array (
      'visible' => false,
      'title' => 
      array (
        'text' => NULL,
      ),
      'lineWidth' => 0,
      'gridLineWidth' => 0,
      'tickInterval' => NULL,
      'tickPixelInterval' => 1,
      'endOnTick' => false,
      'min' => 0,
    ),
    'legend' => 
    array (
      'layout' => 'horizontal',
      'align' => 'left',
      'verticalAlign' => 'bottom',
      'x' => 10,
      'y' => 0,
      'floating' => false,
      'borderWidth' => 1,
      'backgroundColor' => '#d1c382',
    ),
    'credits' => 
    array (
      'enabled' => false,
    ),
    'tooltip' => 
    array (
      'shared' => true,
      'useHTML' => true,
    ),
    'plotOptions' => 
    array (
      'scatter' => 
      array (
        'marker' => 
        array (
          'radius' => 5,
          'states' => 
          array (
            'hover' => 
            array (
              'enabled' => true,
              'lineColor' => 'rgb(100,100,100)',
            ),
          ),
        ),
        'states' => 
        array (
          'hover' => 
          array (
            'marker' => 
            array (
              'enabled' => false,
            ),
          ),
        ),
      ),
    ),
    'series' => $data['data'] ,
    'exporting' => 
    array (
      'enabled' => false,
      'sourceWidth' => 800,
      'sourceHeight' => 180,
    ),
  ),
);

  
         
 $character = json_encode($exportSettings,TRUE);

        
        //echo "<pre>";print_r($object);
         $url_new=DOWNLOAD_CHART_URL1;
        //$student_data = $user_new;
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $ch = curl_init($url_new);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $character);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "cache-control: no-cache",
        "content-type: application/json",
      ));             
        if(curl_errno($ch)){
            //throw new exception(curl_error($ch));
            return false;
        }else{
            $fileName = curl_exec($ch); 
            $upload_path = trim(CRON_ROOT,'library');
            $s3_upload_url='';
            if(isset($daterange) && !empty($daterange)){                
               $upload_url="".$upload_path.UPLOAD_PATH."charts/".$assessment_id."_".$action_plan_id."_".$from_date."_".$to_date.".png";
               $s3_upload_url="".UPLOAD_PATH."charts/".$assessment_id."_".$action_plan_id."_".$from_date."_".$to_date.".png";
            }else{
                $upload_url="".$upload_path.UPLOAD_PATH."charts/".$assessment_id."_".$id_c.".png";    
                $s3_upload_url="".UPLOAD_PATH."charts/".$assessment_id."_".$id_c.".png";    
            }
            //echo $s3_upload_url;die;
            file_put_contents($upload_url,$fileName);
            upload_file($s3_upload_url,$upload_url);
            @unlink($upload_url);
        }
        //die;
        //return $fileName;
       
    }
    private function getReportData() {
        
            $conn = $this->connectToDB();
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "SELECT k.id,a.assessment_id,u.name,u.email,u1.name as leader_name,u1.email as leader_email,f.frequency_days,ct.city_name,st.state_name,co.country_name,cl.client_name,a1.*
                        FROM  h_assessor_action1  a1    
                        INNER JOIN  assessor_key_notes k ON a1.assessor_key_notes_id = k.id
                        INNER JOIN d_assessment a ON a.assessment_id=k.assessment_id
                        INNER JOIN d_user u ON u.client_id=a.client_id
                        INNER JOIN h_user_user_role r1 on u.user_id = r1.user_id and r1.role_id=6
                        INNER JOIN d_user u1 ON a1.leader=u1.user_id
                        INNER JOIN d_frequency  f ON a1.frequency_report = f.frequency_id
                        INNER JOIN d_client  cl ON cl.client_id = a.client_id                    
                        INNER JOIN d_cities  ct ON ct.city_id = cl.city_id
                        INNER JOIN d_states  st ON st.state_id = cl.state_id
                        INNER JOIN d_countries co ON co.country_id = cl.country_id  WHERE a1.action_status>0
                        group by k.id";
                    //. " INNER JOIN d_user ud ON a1.leader = ud.user_id  ";

            $stmt = $conn->prepare($sql);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            

            
    }
    private function getTemplateData($template_type) {
        
        $conn = $this->connectToDB();
            // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sqlTemplate = " SELECT u.template_text,q.subject,q.sender,q.sender_name,q.cc FROM d_review_notification_template u "
                    . "INNER JOIN h_review_notification_mail_users q on u.id = q.notification_id  WHERE u.template_type=$template_type";
        $stmt = $conn->prepare($sqlTemplate);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

}

$obj = new ActionPlanCron();
$obj->getAllActionPlans();
$obj->getActionPlansReports();
