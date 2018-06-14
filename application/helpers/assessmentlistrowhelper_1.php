<?php
class assessmentListRowHelper{
	protected $user;
	
	protected $assRow;
	protected $columns=array(
		"GACollapse"=>array("label"=>"&nbsp;"),
		"ClientName"=>array("label"=>"School/ Teacher/ Batch Name", "sortable"=>"client_name"),
		"AssmntType"=>array("label"=>"Review Type", "sortable"=>"assessment_type"),
		"DiagnosticName"=>array("label"=>"Diagnostic Name"),
		"AssmntDate"=>array("label"=>"Date of Review", "sortable"=>"create_date"),
                "AQSDate"=>array("label"=>"AQS Dates", "sortable"=>"aqs_start_date"),
		"AqsStatus"=>array("label"=>"School/ Teacher/ Student Profile Status"),
		//"IntAssProgress"=>array("label"=>"Self-Review Status(%)&nbsp;&nbsp;&nbsp;"),
		//"ExtAssProgress"=>array("label"=>"External Review Status(%)"),
		//"PostReviewProgress"=>array("label"=>"Post Review Status(%)"),
		"ReviewProgress"=>array("label"=>"Self/External/Post Review Status(%)"),
		"Feedback"=>array("label"=>"Feedback"),
		"Report"=>array("label"=>"Reports"),
		"Edit"=>array("label"=>"Edit/ View"),
		"Payment"=>array("label"=>"Review Request"),
		"ApprovePayment"=>array("label"=>"Review Requests"),
		//"UploadedReview"=>array("label"=>"Uploaded Reviews")
		);
	
	public $isAdmin;
	public $isNetworkAdmin;
	public $isSchoolAdmin;
	public $isPrincipal;
	public $isAdminOrNadminOrPrincipal;
	public $isAnyAdmin;
	public $canEditAfterSubmit;
	public $disableAssRow;
	public $assIsUploaded;
	
	protected $currentRowIs=0;
	const SCHOOL_ASSESSMENT_ROW=1,GROUP_ASSESSMENT_ROW=2,GROUP_ASSESSMENT_STUDENT_ROW=4,CHILDROW_OF_STUDENT_ASSESSMENT=5,CHILDROW_OF_GROUP_ASSESSMENT=3;
	
	
	protected static $rowCount=0;
	
	
	
	function __construct($curUser){
		$this->user=$curUser;
		$this->canCreate=in_array("create_assessment",$this->user['capabilities'])?true:false;
		$this->isAdmin=in_array("view_all_assessments",$this->user['capabilities'])?true:false;
		$this->isNetworkAdmin=in_array("view_own_network_assessment",$this->user['capabilities'])?true:false;
		$this->isSchoolAdmin=in_array(5,$this->user['role_ids'])?true:false;
		$this->isPrincipal=in_array(6,$this->user['role_ids'])?true:false;
		$this->isAdminOrNadminOrPrincipal=$this->isNetworkAdmin || $this->isPrincipal || $this->isAdmin?true:false;
		$this->isAnyAdmin=$this->isAdminOrNadminOrPrincipal || $this->isSchoolAdmin;
		$this->canEditAfterSubmit=in_array("edit_all_submitted_assessments",$this->user['capabilities'])?true:false;
		$this->isExternalReviewer=in_array("take_external_assessment",$this->user['capabilities'])?true:false;               
		$this->disableAssRow = 0;
		if(!($this->isAdmin || $this->isExternalReviewer)){
			unset($this->columns['PostReviewProgress']);
		}
		if(!($this->isAnyAdmin || $this->isExternalReviewer)){
			unset($this->columns['Reports']);
		}		
		if(!($this->isAdmin)){
			unset($this->columns['ApprovePayment']);	
		}
		if(!($this->isAnyAdmin)){
			unset($this->columns['Edit']);
		}
		if(!($this->isPrincipal || $this->isSchoolAdmin)){
			unset($this->columns['Payment']);
		}
	}
	
	public function printHeaderRow($sortBy='create_date',$sortType='desc'){
		//print_r($this->columns);
		$text="<tr>";
		foreach($this->columns as $val){
			if(empty($val['sortable']))
				$text.= "<th>".$val['label']."</th>";
			else
				$text.='<th data-value="'.$val['sortable'].'" class="sort '.($sortBy==$val['sortable']?"sorted_".$sortType:'').'">'.$val['label'].'</th>';
		}
		$text.='</tr>';
		return $text;
	}
	
	public function printBodyRow($assessment){
		if(!$this->isAdmin)
		$this->disableAssRow =  $assessment['assessment_type_id']==1 && $assessment['subAssessmentType']==1 && ($assessment['isApproved']==0 || $assessment['isApproved']==2) ?1:0 ;				
		$this->assRow=$assessment;
                //echo "<pre>";print_r($assessment);die;
		$this->assIsUploaded=isset($assessment['is_uploaded'])?$assessment['is_uploaded']:'';
		$this->assRow['assessment_type_name'] = $assessment['assessment_type_id']==1 && $assessment['subAssessmentType']==1?'School (Self-Review)':ucfirst($this->assRow['assessment_type_name']);
		$cssCls=array('ass_type_'.$assessment['assessment_type_id']);
                /*echo"<pre>";
                print_r($assessment);
                echo"</pre>";*/
		if($assessment['group_assessment_id'] && $assessment['assessment_id'] && $assessment['assessment_type_id']==2){
			$this->currentRowIs=$this::CHILDROW_OF_GROUP_ASSESSMENT;
			$cssCls[]='gpChild ga-rows-'.$assessment['group_assessment_id'];
		}else if($assessment['group_assessment_id'] && $assessment['assessment_id'] && $assessment['assessment_type_id']==4){
			$this->currentRowIs=$this::CHILDROW_OF_STUDENT_ASSESSMENT;
			$cssCls[]='gpChild ga-rows-'.$assessment['group_assessment_id'];
		}else{
                        if($assessment['assessment_type_id']==4){
                        $this->currentRowIs=$assessment['assessment_id']>0?$this::SCHOOL_ASSESSMENT_ROW:$this::GROUP_ASSESSMENT_STUDENT_ROW;   
                        }else{
			$this->currentRowIs=$assessment['assessment_id']>0?$this::SCHOOL_ASSESSMENT_ROW:$this::GROUP_ASSESSMENT_ROW;
                        }
			$this::$rowCount++;
			$cssCls[]=$this::$rowCount%2==1?'odd':'even';
		}
                
		$text='<tr class="'.implode(" ",$cssCls).'" data-gaid="'.$assessment['group_assessment_id'].'">';
		foreach($this->columns as $key=>$val){
			$funName="print".$key."Column";
			$text.=$this->$funName();
		}
		$text.='</tr>';
		return $text;
	}
	
	public function printNoResultRow(){
		return '<tr><td colspan="'.count($this->columns).'">No Review found</td></tr>';
	}
	
	private function printGACollapseColumn(){
		$text='';               
		if($this->currentRowIs==$this::CHILDROW_OF_GROUP_ASSESSMENT){
			$text='<span class="subGARow">&nbsp;</span>';
		}else if(($this->currentRowIs==$this::GROUP_ASSESSMENT_ROW || $this->currentRowIs==$this::GROUP_ASSESSMENT_STUDENT_ROW) && $this->assRow['assessments_count']>0){
			$text='<span class="collapseGA vtip fa fa-plus-circle" title="View Reviews"></span>';
		}
		return $this->printDataCell($text);
	}
	
	private function printClientNameColumn(){
                /*$batch="";
                if(isset($this->assRow['student_review_batch']) && $this->assRow['student_review_batch']!="0" && !empty($this->assRow['student_review_batch'])) $batch=" (".$this->assRow['student_review_batch'].")";
		return $this->printDataCell(($this->currentRowIs==$this::CHILDROW_OF_GROUP_ASSESSMENT || $this->currentRowIs==$this::CHILDROW_OF_STUDENT_ASSESSMENT)?$this->assRow['data_by_role'][3]['user_name']:"".$this->assRow['client_name']."".$batch."");*/
               return $this->printDataCell(($this->currentRowIs==$this::CHILDROW_OF_GROUP_ASSESSMENT || $this->currentRowIs==$this::CHILDROW_OF_STUDENT_ASSESSMENT)?$this->assRow['data_by_role'][3]['user_name']:$this->assRow['client_name']);
	}
	
        private function printAQSDateColumn(){
                $dates="";
                $dates.=(empty($this->assRow['aqs_start_date']) || $this->assRow['aqs_start_date']=="0000-00-00")?'-':"<span class='nowrap'>".$this->assRow['aqs_start_date']."</span>";
                $dates.=(empty($this->assRow['aqs_end_date']) ||  $this->assRow['aqs_end_date']=="0000-00-00" )?'':"<br>to<br><span class='nowrap'>".$this->assRow['aqs_end_date']."</span>";
		return $this->printDataCell($dates);
	}
        
	private function printDiagnosticNameColumn(){
	
		if($this->assRow['assessment_type_id']==1 || $this->currentRowIs==$this::CHILDROW_OF_GROUP_ASSESSMENT || $this->currentRowIs==$this::CHILDROW_OF_STUDENT_ASSESSMENT || $this->currentRowIs==$this::GROUP_ASSESSMENT_STUDENT_ROW) 
		return $this->printDataCell($this->assRow['diagnosticName']);
		return $this->printDataCell('&nbsp;');
	}
	private function printAssmntTypeColumn(){				
		return $this->printDataCell($this->assRow['assessment_type_name']);
	}
	
	private function printAssmntDateColumn(){
		return $this->printDataCell("<span class='nowrap'>".substr($this->assRow['create_date'],0,10)."</span>");
	}
        private function printFeedbackColumn(){
           
            //echo $this->user['user_id'];
            //echo "<pre>";print_r($this->assRow);
            if(!empty($this->assRow['data_by_role'])) {
                $feedbackRoles = explode(',', FEEDBACK_ROLES);
                foreach($this->assRow['data_by_role'] as $key=>$roleData) {
                    $ratingInputDateStatus = !empty($roleData['ratingInputDate'])?1:1;
                    $user_id= !empty($roleData['user_id'])?$roleData['user_id']:0;
                    if($key ==4 && isset($roleData['user_id']) && $roleData['user_id'] == $this->user['user_id'] && $ratingInputDateStatus )            
                       $text = '<a title="Click to view/edit Teacher information form" class="vtip" href="?controller=diagnostic&action=feedbackForm&assessment_id='.$this->assRow['assessment_id'].'&user_id='.$this->user['user_id'].'">Feedback</a>';
                    else if( isset($this->assRow['externalTeam']) && $this->assRow['externalTeam'] == $this->user['user_id'] && $ratingInputDateStatus )            
                       $text = '<a title="Click to view/edit Teacher information form" class="vtip" href="?controller=diagnostic&action=feedbackForm&assessment_id='.$this->assRow['assessment_id'].'&user_id='.$this->user['user_id'].'">Feedback</a>';

                    else if( $key ==4 && !empty($this->user['role_ids']) && count(array_intersect($feedbackRoles, $this->user['role_ids'])) && $ratingInputDateStatus && $user_id)            
                       $text = '<a title="Click to view/edit Teacher information form" class="vtip" href="?controller=diagnostic&action=feedbackForm&assessment_id='.$this->assRow['assessment_id'].'&user_id='.$user_id.'">Feedback</a>';
                    else
                        $text='<span class="subGARow">&nbsp;</span>';
                }
            }else 
                $text='<span class="subGARow">&nbsp;</span>';
            return $this->printDataCell($text);
	}
	
	private function printAqsStatusColumn(){		
		$isReportPublished=empty($this->assRow['report_data']) || !empty($this->assRow['report_data'][0]['isPublished']) && $this->assRow['report_data'][0]['isPublished']!=1?false:true;
		$text='&nbsp;';		
                if($this->assIsUploaded) {
                    
                    //$text='<br/><a class="vtip" href="?controller=diagnostic&action=aqsForm&assmntId_or_grpAssmntId='.($this->assRow['assessment_id']>0?$this->assRow['assessment_id']:$this->assRow['group_assessment_id']).'&assessment_type_id='.$this->assRow['assessment_type_id'].'"'. ' title="View/Edit School Profile Data">View'.'</a>';
                    $text='<span class="vtip" title="School Profile filled percentage">'.($this->assRow['aqspercent']?$this->assRow['aqspercent']:($this->assRow['aqs_status']?'100':'0')).'%</span>'
                            . '<br/><a class="vtip" href="?controller=diagnostic&action=aqsForm&assmntId_or_grpAssmntId='.($this->assRow['assessment_id']>0?$this->assRow['assessment_id']:$this->assRow['group_assessment_id']).'&assessment_type_id='.$this->assRow['assessment_type_id'].'"'. ' title="View/Edit School Profile Data">Edit'.'</a>';
                    	
                }else if($this->currentRowIs==$this::CHILDROW_OF_STUDENT_ASSESSMENT){
			$text='<a title="Click to view/edit Student information form" class="vtip" href="?controller=assessment&action=createStudentProfileForm&assessment_id='.$this->assRow['assessment_id'].'&assessor_id='.$this->assRow['data_by_role'][3]['user_id'].'">'. ($this->assRow['isTchrInfoFilled']==1?"Filled":"Not filled").'</a>';
		}else if($this->currentRowIs==$this::CHILDROW_OF_GROUP_ASSESSMENT){
			$text='<a title="Click to view/edit Teacher information form" class="vtip" href="?controller=diagnostic&action=teacherInfoForm&assessment_id='.$this->assRow['assessment_id'].'&assessor_id='.$this->assRow['data_by_role'][3]['user_id'].'">'. ($this->assRow['isTchrInfoFilled']==1?"Filled":"Not filled").'</a>';
		}else if($this->assRow['assessments_count'] && $this->canEditAfterSubmit && $this->currentRowIs==$this::GROUP_ASSESSMENT_STUDENT_ROW){						
			$text='-';
		}else if($this->assRow['assessments_count'] && $this->currentRowIs==$this::GROUP_ASSESSMENT_STUDENT_ROW){						
			$text='-';
		}else if($this->assRow['assessments_count'] && $this->canEditAfterSubmit){						
			$text='<span class="vtip" title="School Profile filled percentage">'.($this->assRow['aqspercent']?$this->assRow['aqspercent']:($this->assRow['aqs_status']?'100':'0')).'%</span>
			<br/><a class="vtip" href="?controller=diagnostic&action=aqsForm&assmntId_or_grpAssmntId='.($this->assRow['assessment_id']>0?$this->assRow['assessment_id']:$this->assRow['group_assessment_id']).'&assessment_type_id='.$this->assRow['assessment_type_id'].'"'.($this->assRow['aqs_status']==1 && !$isReportPublished?' title="View/Edit School Profile Data">Edit':' title="View School Profile Data">View').'</a>';
		}
		else if($this->assRow['assessments_count']){	//echo 'aa ';
			$text='<span class="vtip" title="School Profile filled percentage">'.($this->assRow['aqspercent']?$this->assRow['aqspercent']:($this->assRow['aqs_status']?'100':'0')).'%</span>
			<br/><a class="vtip" href="?controller=diagnostic&action=aqsForm&assmntId_or_grpAssmntId='.($this->assRow['assessment_id']>0?$this->assRow['assessment_id']:$this->assRow['group_assessment_id']).'&assessment_type_id='.$this->assRow['assessment_type_id'].'"'. ($this->assRow['aqs_status']==1?' title="View School Profile Data">View':' title="View/Edit School Profile Data">Edit').'</a>';
			
		}
		return $this->printDataCell($text);
	}
	
	private function printIntAssProgressColumn(){
		return $this->printDataCell($this->getAssProgressColumn(3));
	}
	
	private function printExtAssProgressColumn(){
		return $this->printDataCell($this->getAssProgressColumn(4));
	}
        /*private function  printUploadedReviewColumn(){
		$isReportPublished=empty($this->assRow['report_data']) || !empty($this->assRow['report_data'][0]['isPublished']) && $this->assRow['report_data'][0]['isPublished']!=1?false:true;
		$text='&nbsp;';		
                    if($this->assIsUploaded) {
			$text='<br/><a class="vtip" href="?controller=diagnostic&action=aqsForm&assmntId_or_grpAssmntId='.($this->assRow['assessment_id']>0?$this->assRow['assessment_id']:$this->assRow['group_assessment_id']).'&assessment_type_id='.$this->assRow['assessment_type_id'].'"'. ' title="View/Edit School Profile Data">Edit'.'</a>';
                    }	
                    return $this->printDataCell($text);
	}*/
	
	private function printReportColumn(){	
		//$reviewers = explode(',',$this->assRow['user_ids']);
		//$group_asmt_external = empty($reviewers[1])?0:$reviewers[1];
		$group_asmt_external= empty($this->assRow['data_by_role'][4]['user_id'])?0:$this->assRow['data_by_role'][4]['user_id'];	
                $group_asmt_internal= empty($this->assRow['data_by_role'][3]['user_id'])?0:$this->assRow['data_by_role'][3]['user_id'];	
		/*if(($this->isAdmin && $this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && $this->assRow['subAssessmentType']!=1 && $this->assRow['aqs_status']==1 && $this->assRow['data_by_role'][4]['status']==1) || ($this->currentRowIs==$this::CHILDROW_OF_GROUP_ASSESSMENT && $this->assRow['isTchrInfoFilled']==1 && ($this->user['user_id']==$group_asmt_external && $this->assRow['data_by_role'][4]['percComplete']=='100' &&  $this->assRow['data_by_role'][4]['status']!=1)||($this->isAnyAdmin &&  !empty($this->assRow['data_by_role'][4]['status']) && $this->assRow['data_by_role'][4]['status']==1) ) || $this->printReportColInGA()){
			return $this->printDataCell('<a class="execUrl manageReportBtn vtip iconBtn" data-size="700" href="?controller=assessment&action=reportList&assessment_id='.$this->assRow['assessment_id'].'&group_assessment_id='.$this->assRow['group_assessment_id'].'" title="Print/View Report"><i class="fa fa-print"></i></a>');
		}
		elseif($this->isExternalReviewer && $this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && $this->assRow['subAssessmentType']!=1 && $this->assRow['aqs_status']==1 && intval($this->assRow['data_by_role'][4]['percComplete'])=='100' && (empty($this->assRow['report_data']) || $this->assRow['report_data'][0]['isPublished']!=1)){
			return $this->printDataCell('<a class="execUrl manageReportBtn vtip iconBtn" data-size="700" href="?controller=assessment&action=reportList&assessment_id='.$this->assRow['assessment_id'].'&group_assessment_id='.$this->assRow['group_assessment_id'].'" title="Print/View Report"><i class="fa fa-print"></i></a>');
		}
		elseif($this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && $this->assRow['aqs_status']==1 && $this->assRow['data_by_role'][3]['status']==1 && $this->assRow['subAssessmentType']==1 && ($this->isAdmin || $this->isSchoolAdmin|| $this->isPrincipal)){
			return $this->printDataCell('<a class="execUrl manageReportBtn vtip iconBtn" data-size="700" href="?controller=assessment&action=reportList&assessment_id='.$this->assRow['assessment_id'].'&group_assessment_id='.$this->assRow['group_assessment_id'].'" title="Print/View Report"><i class="fa fa-print"></i></a>');
		}*/
		//print_r($this->assRow['report_data'][0]['isPublished']);
		//if school review and admin
		if(($this->isAdmin && $this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && $this->assRow['subAssessmentType']!=1 && in_array('generate_submitted_asmt_reports',$this->user['capabilities']) && $this->assRow['aqs_status']==1 && !empty($this->assRow['data_by_role'][3]['status']) && $this->assRow['data_by_role'][4]['status']==1))
			return $this->printDataCell('<a class="execUrl manageReportBtn vtip iconBtn" data-size="880" href="?isPop=1&controller=assessment&action=reportList&assessment_id='.$this->assRow['assessment_id'].'&group_assessment_id='.$this->assRow['group_assessment_id'].'" title="Print/View Report"><i class="fa fa-print"></i></a>');
		//if school review and external reviewer
		elseif($this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && $this->assRow['subAssessmentType']!=1 && in_array('generate_unsubmitted_asmt_reports',$this->user['capabilities']) && $this->assRow['aqs_status']==1 && intval($this->assRow['data_by_role'][3]['status'])==1 && intval($this->assRow['data_by_role'][4]['percComplete'])=='100' && $this->user['user_id']==$this->assRow['data_by_role'][4]['user_id'] && (empty($this->assRow['report_data']) || $this->assRow['report_data'][0]['isPublished']!=1))
			return $this->printDataCell('<a class="execUrl manageReportBtn vtip iconBtn" data-size="880" href="?isPop=1&controller=assessment&action=reportList&assessment_id='.$this->assRow['assessment_id'].'&group_assessment_id='.$this->assRow['group_assessment_id'].'" title="Print/View Report"><i class="fa fa-print"></i></a>');
                //view published school reports of his own school
                elseif( $this->assRow['client_id']==$this->user['client_id'] && $this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && $this->assRow['subAssessmentType']!=1 && in_array('view_published_own_school_reports',$this->user['capabilities']) && $this->assRow['aqs_status']==1 && intval($this->assRow['data_by_role'][3]['status'])==1 && intval($this->assRow['data_by_role'][4]['status'])==1  && (!empty($this->assRow['report_data']) && $this->assRow['report_data'][0]['isPublished']==1))
			return $this->printDataCell('<a class="execUrl manageReportBtn vtip iconBtn" data-size="880" href="?isPop=1&controller=assessment&action=reportList&assessment_id='.$this->assRow['assessment_id'].'&group_assessment_id='.$this->assRow['group_assessment_id'].'" title="Print/View Report"><i class="fa fa-print"></i></a>');
		//if school online self-review
		elseif($this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && $this->assRow['aqs_status']==1 && $this->assRow['data_by_role'][3]['status']==1 && $this->assRow['subAssessmentType']==1 && ($this->isAdmin || $this->isSchoolAdmin|| $this->isPrincipal||$this->isNetworkAdmin))
			return $this->printDataCell('<a class="execUrl manageReportBtn vtip iconBtn" data-size="880" href="?isPop=1&controller=assessment&action=reportList&assessment_id='.$this->assRow['assessment_id'].'&group_assessment_id='.$this->assRow['group_assessment_id'].'" title="Print/View Report"><i class="fa fa-print"></i></a>');		
		elseif( (($this->currentRowIs==$this::CHILDROW_OF_GROUP_ASSESSMENT && $this->assRow['isTchrInfoFilled']==1 && (($this->user['user_id']==$group_asmt_external||$this->user['user_id']==$group_asmt_internal||$this->isPrincipal||$this->isSchoolAdmin||$this->isNetworkAdmin) && intval($this->assRow['data_by_role'][4]['percComplete'])=='100' ))||($this->currentRowIs==$this::CHILDROW_OF_GROUP_ASSESSMENT && $this->isAdmin &&  !empty($this->assRow['data_by_role'][4]['status']) && $this->assRow['data_by_role'][4]['status']==1) ) || $this->printReportColInGA())
		return $this->printDataCell('<a class="execUrl manageReportBtn vtip iconBtn" data-size="880" href="?isPop=1&controller=assessment&action=reportList&assessment_id='.$this->assRow['assessment_id'].'&group_assessment_id='.$this->assRow['group_assessment_id'].'&greporttype='.$this->assRow['assessment_type_id'].'" title="Print/View Report"><i class="fa fa-print"></i></a>');
                elseif( (($this->currentRowIs==$this::CHILDROW_OF_STUDENT_ASSESSMENT && $this->assRow['isTchrInfoFilled']==1 && (($this->user['user_id']==$group_asmt_external||$this->user['user_id']==$group_asmt_internal||$this->isPrincipal||$this->isSchoolAdmin||$this->isNetworkAdmin) && intval($this->assRow['data_by_role'][4]['percComplete'])=='100' ))||($this->currentRowIs==$this::CHILDROW_OF_STUDENT_ASSESSMENT && $this->isAdmin &&  !empty($this->assRow['data_by_role'][4]['status']) && $this->assRow['data_by_role'][4]['status']==1) ) || $this->printReportColInGA())
		return $this->printDataCell('<a class="execUrl manageReportBtn vtip iconBtn" data-size="880" href="?isPop=1&controller=assessment&action=reportList&assessment_id='.$this->assRow['assessment_id'].'&group_assessment_id='.$this->assRow['group_assessment_id'].'&greporttype='.$this->assRow['assessment_type_id'].'" title="Print/View Report"><i class="fa fa-print"></i></a>');
		
		return $this->printDataCell('&nbsp;');
	}
	
	private function printEditColumn(){
                
		if($this->currentRowIs==$this::GROUP_ASSESSMENT_ROW && ($this->canCreate)){
			//return $this->printDataCell('<a href="?isPop=1&controller=assessment&action=editTeacherAssessment&amp;gaid='.$this->assRow['group_assessment_id'].'">'.($this->assRow['assessments_count']==0?'<i title="Edit" class="vtip glyphicon glyphicon-pencil"></i>':'<i title="View" class="vtip glyphicon glyphicon-eye-open"></i>').'</a>');
                        return $this->printDataCell('<a href="?isPop=1&controller=assessment&action=editTeacherAssessment&amp;gaid='.$this->assRow['group_assessment_id'].'">'.($this->assRow['assessments_count']==0?'<i title="Edit" class="vtip glyphicon glyphicon-pencil"></i>':'<i title="View/Edit" class="vtip glyphicon glyphicon-pencil"></i>').'</a>');
		}
		else if($this->currentRowIs==$this::GROUP_ASSESSMENT_ROW && ($this->isAdminOrNadminOrPrincipal || $this->assRow['admin_user_id']==$this->user['user_id'])){
			//return $this->printDataCell('<a href="?isPop=1&controller=assessment&action=createTeacherAssessor&amp;taid='.$this->assRow['group_assessment_id'].'">'.($this->assRow['assessments_count']==0?'<i title="Edit" class="vtip glyphicon glyphicon-pencil"></i>':'<i title="View" class="vtip glyphicon glyphicon-eye-open"></i>').'</a>');
		
                    return $this->printDataCell('<a href="?isPop=1&controller=assessment&action=createTeacherAssessor&amp;taid='.$this->assRow['group_assessment_id'].'">'.($this->assRow['assessments_count']==0?'<i title="Edit" class="vtip glyphicon glyphicon-pencil"></i>':'<i title="View/Edit" class="vtip glyphicon glyphicon-pencil"></i>').'</a>');

                }else if($this->currentRowIs==$this::GROUP_ASSESSMENT_STUDENT_ROW && ($this->canCreate)){
			//return $this->printDataCell('<a href="?isPop=1&controller=assessment&action=editTeacherAssessment&amp;gaid='.$this->assRow['group_assessment_id'].'">'.($this->assRow['assessments_count']==0?'<i title="Edit" class="vtip glyphicon glyphicon-pencil"></i>':'<i title="View" class="vtip glyphicon glyphicon-eye-open"></i>').'</a>');
                        return $this->printDataCell('<a href="?isPop=1&controller=assessment&action=editStudentAssessment&amp;gaid='.$this->assRow['group_assessment_id'].'">'.($this->assRow['assessments_count']==0?'<i title="Edit" class="vtip glyphicon glyphicon-pencil"></i>':'<i title="View/Edit" class="vtip glyphicon glyphicon-pencil"></i>').'</a>');
		}
		else if($this->currentRowIs==$this::GROUP_ASSESSMENT_STUDENT_ROW && ($this->isAdminOrNadminOrPrincipal || $this->assRow['admin_user_id']==$this->user['user_id'])){
			//return $this->printDataCell('<a href="?isPop=1&controller=assessment&action=createTeacherAssessor&amp;taid='.$this->assRow['group_assessment_id'].'">'.($this->assRow['assessments_count']==0?'<i title="Edit" class="vtip glyphicon glyphicon-pencil"></i>':'<i title="View" class="vtip glyphicon glyphicon-eye-open"></i>').'</a>');
		
                    return $this->printDataCell('<a href="?isPop=1&controller=assessment&action=createStudentAssessor&amp;taid='.$this->assRow['group_assessment_id'].'">'.($this->assRow['assessments_count']==0?'<i title="Edit" class="vtip glyphicon glyphicon-pencil"></i>':'<i title="View/Edit" class="vtip glyphicon glyphicon-pencil"></i>').'</a>');

                }
		else if($this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && ($this->isAdminOrNadminOrPrincipal || $this->assRow['admin_user_id']==$this->user['user_id']) && $this->assRow['subAssessmentType']==1){
			return $this->printDataCell('<a href="?isPop=1&controller=assessment&action=editSchoolSelfAssessment&amp;said='.$this->assRow['assessment_id'].'">'.($this->assRow['percCompletes']!='0.00'?'<i title="View" class="vtip glyphicon glyphicon-eye-open"></i>':'<i title="Edit" class="vtip glyphicon glyphicon-pencil"></i>').'</a>');
		}
		else if($this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && ($this->isAdmin || $this->assRow['admin_user_id']==$this->user['user_id'])){
			return $this->printDataCell('<a href="?isPop=1&controller=assessment&action=editSchoolAssessment&amp;said='.$this->assRow['assessment_id'].'"><i title="Edit" class="vtip glyphicon glyphicon-pencil"></i></a>');
		}
		return $this->printDataCell('&nbsp;');
	}
	
private function getAssProgressColumn($roleId){	
		$text='&nbsp;';		
		if($this->assRow['assessment_id']>0){
                    
                        $row = $roleId==4 ?(empty($this->assRow['data_by_role'][$roleId]['user_name'])? ('<span class="vtip">-</span>'):('<span class="vtip" title="'.$this->assRow['data_by_role'][$roleId]['user_name'].'">'.$this->assRow['data_by_role'][$roleId]['percComplete'].'%</span>')) :('<span class="vtip" title="'.$this->assRow['data_by_role'][$roleId]['user_name'].'">'.$this->assRow['data_by_role'][$roleId]['percComplete'].'%</span>') ;
			$text.= $row;			
			//if($this->assRow['aqs_status']!=1){
                            
			//}else
                        
                        if(isset($this->assRow['data_by_role'][$roleId]['status']) && $this->assRow['data_by_role'][$roleId]['status']==1){
				$isReportPublished=empty($this->assRow['report_data']) || $this->assRow['report_data'][0]['isPublished']!=1?false:true;
				$text.='<br><span class="assComplete vtip" title="'.$this->assRow['data_by_role'][$roleId]['ratingInputDate'].'"></span>';
				if($this->user['user_id']==$this->assRow['data_by_role'][$roleId]['user_id'] || $this->isAdmin || ($roleId==3 && (($this->isSchoolAdmin && $this->assRow['client_id']==$this->user['client_id']) || ($this->isNetworkAdmin && $this->assRow['network_id']==$this->user['network_id'])) )){
					$editViewText=$this->canEditAfterSubmit && !$isReportPublished ?'Edit':'View';
					$text.='<br><a data-modalclass="modal-lg aPreview" title="Click to view the snapshot of ratings for KPAs." href="?isPop=1&controller=diagnostic&action=assessmentPreview&assessment_id='.$this->assRow['assessment_id'].'&assessor_id='.$this->assRow['data_by_role'][$roleId]['user_id'].'" class="linkBtn vtip execUrl">Preview</a> <a title="Click to '.$editViewText.' ratings." href="?controller=diagnostic&action=assessmentForm&assessment_id='.$this->assRow['assessment_id'].'&assessor_id='.$this->assRow['data_by_role'][$roleId]['user_id'].'" class="linkBtn vtip">'.$editViewText.'</a>';
				}
			}else if(isset($this->assRow['aqs_start_date']) && isset($this->assRow['data_by_role'][$roleId]['user_id']) && $this->user['user_id']==$this->assRow['data_by_role'][$roleId]['user_id'] ){
				//echo date('Y-m-d',strtotime($this->assRow['aqs_start_date']));
				$aqsDate  = DateTime::createFromFormat('Y-m-d', date('Y-m-d',strtotime($this->assRow['aqs_start_date'])));
				$today  = DateTime::createFromFormat('Y-m-d',date('Y-m-d'));
				
				$text.= $this->assRow['subAssessmentType']==1 || $today>=$aqsDate ?'<br><a title="Click here to fill the ratings to complete review" href="?isPop=1&controller=diagnostic&action=assessmentForm&assessment_id='.$this->assRow['assessment_id'].'&assessor_id='.$this->assRow['data_by_role'][$roleId]['user_id'].'" class="linkBtn vtip">Take Review</a>':'';
			}else if($this->currentRowIs==$this::CHILDROW_OF_GROUP_ASSESSMENT && $this->user['user_id']==$this->assRow['data_by_role'][$roleId]['user_id']){
				$text.='<br><a href="?isPop=1&controller=diagnostic&action=assessmentForm&assessment_id='.$this->assRow['assessment_id'].'&assessor_id='.$this->assRow['data_by_role'][$roleId]['user_id'].'" class="linkBtn">Take Review</a>';
			}else if($this->currentRowIs==$this::CHILDROW_OF_STUDENT_ASSESSMENT && $this->user['user_id']==$this->assRow['data_by_role'][$roleId]['user_id']){
				$text.='<br><a href="?isPop=1&controller=diagnostic&action=assessmentForm&assessment_id='.$this->assRow['assessment_id'].'&assessor_id='.$this->assRow['data_by_role'][$roleId]['user_id'].'" class="linkBtn">Take Review</a>';
			}
		}else if($this->assRow['assessments_count']>0){
			$text.='<span>'.$this->assRow['data_by_role'][$roleId]['percComplete'].'%</span>'.($this->assRow['data_by_role'][$roleId]['status']==1?'<br><span class="assComplete"></span>':'');
		}
		return $text;		
	}
        
         private function printReviewProgressColumn() {

                    $text = '
                        <div class="merge">100.00%
                        <div class="subOptions"><a href="#" style="border-top: 0;">Post Review</a>
                    <a href="#">Preview</a>
                    <a href="#">Edit</a>
                    <a href="#">Feedback</a>    
                        </div>
                       </div>
                        <div class="merge">100%
                        <div class="subOptions"><a href="#" style="
                        border-top: 0;
                    ">Post Review</a>
                    <a href="#">Preview</a>
                    <a href="#">Edit</a>
                    <a href="#">Feedback</a>    
                        </div>
                       </div>    
                        <div class="merge">50%
                        <div class="subOptions"><a href="#" style="
                        border-top: 0;
                    ">Post Review</a>
                    <a href="#">Preview</a>
                    <a href="#">Edit</a>
                    <a href="#">Feedback</a>    
                        </div>
                       </div>
                       
        ';
                    return $this->printDataCell($text,'merged');
                    

    }
	
	private function printDataCell($text,$cssCls=''){
		return "<td".($cssCls==''?'':' class="'.$cssCls.'"').($this->disableAssRow? ' disabled ' : '').">$text</td>";
	}
	
	private function printReportColInGA(){			
		if($this->currentRowIs==$this::GROUP_ASSESSMENT_ROW && $this->assRow['assessments_count']>0 && ($this->isAdmin)){					
			for($i=0;$i<$this->assRow['assessments_count'];$i++){
				if(isset($this->assRow['data_by_role'][4]['allStatuses'][$i]) && $this->assRow['data_by_role'][4]['allStatuses'][$i]==1 && $this->assRow['teacherInfoStatuses'][$i]==1){					
					return true;
				}
			}
		}
		
                if($this->currentRowIs==$this::GROUP_ASSESSMENT_STUDENT_ROW && $this->assRow['assessments_count']>0 && ($this->isAdmin)){					
			for($i=0;$i<$this->assRow['assessments_count'];$i++){
				if(isset($this->assRow['data_by_role'][4]['allStatuses'][$i]) && $this->assRow['data_by_role'][4]['allStatuses'][$i]==1 && $this->assRow['teacherInfoStatuses'][$i]==1){					
					return true;
				}
			}
		}
                
		if($this->currentRowIs==$this::GROUP_ASSESSMENT_ROW && $this->assRow['assessments_count']>0 && ($this->isSchoolAdmin || $this->isPrincipal  || $this->isNetworkAdmin )){
                    
			$anyReportPublished = 0;
			$report_data = explode(',',$this->assRow['report_data']);                        
			for($i=0;$i<$this->assRow['assessments_count'];$i++){ 
				if($this->assRow['teacherInfoStatuses'][$i]==1 && $this->assRow['data_by_role'][4]['allStatuses'][$i]==1  && intval($this->assRow['data_by_role'][4]['percComplete'])>0){					
					$check_published = empty($report_data[$i])?array():explode('|',$report_data[$i]);
					$anyReportPublished = $anyReportPublished || (empty($check_published[1])?0:$check_published[1]);	
					return !$anyReportPublished;
				}
			}
			//return $anyReportPublished==1?false:true;
		}
                
                if($this->currentRowIs==$this::GROUP_ASSESSMENT_STUDENT_ROW && $this->assRow['assessments_count']>0 && ($this->isSchoolAdmin || $this->isPrincipal || $this->isNetworkAdmin )){
                    
			$anyReportPublished = 0;
			$report_data = explode(',',$this->assRow['report_data']);                        
			for($i=0;$i<$this->assRow['assessments_count'];$i++){ 
				if($this->assRow['teacherInfoStatuses'][$i]==1 && $this->assRow['data_by_role'][4]['allStatuses'][$i]==1  && intval($this->assRow['data_by_role'][4]['percComplete'])>0){					
					$check_published = empty($report_data[$i])?array():explode('|',$report_data[$i]);
					$anyReportPublished = $anyReportPublished || (empty($check_published[1])?0:$check_published[1]);	
					return !$anyReportPublished;
				}
			}
			//return $anyReportPublished==1?false:true;
		}
                //echo $this->currentRowIs;
		return false;
	}
	private function printPaymentColumn()
	{
		$text = '';
		if($this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && $this->disableAssRow)
		{
			
			if($this->assRow['isApproved']==0 && !$this->isAdmin)
				$text='<td><span class="vtip" title="Review is pending for approval by Admin." href="#">Pending Approval</span></td>';
                        elseif($this->assRow['isApproved']==2 && !$this->isAdmin)
				$text='<td><span class="vtip" title="Review rejected by Admin." href="#">Rejected</span></td>';
			/*else if($this->assRow['isPmtApproved']==0)
				$text='<td><span class="vtip" title="Offline payment is pending for approval by admin." href="#">Approval pending</span></td>';*/
			return $text;
		}
		else
		{
			return $this->printDataCell('&nbsp;');
		}
		
	}
	private function printApprovePaymentColumn()
	{	
                if($this->assIsUploaded) {
                    
                    //$text='<br/><a class="vtip" href="?controller=diagnostic&action=aqsForm&assmntId_or_grpAssmntId='.($this->assRow['assessment_id']>0?$this->assRow['assessment_id']:$this->assRow['group_assessment_id']).'&assessment_type_id='.$this->assRow['assessment_type_id'].'"'. ' title="View/Edit School Profile Data">View'.'</a>';
                   return $text='<td class="act"><label class="flashAnim">Adhyayan Action Required</label></td>';	
                    	
                }
		if($this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && $this->assRow['subAssessmentType']==1)
		{			
			if($this->assRow['isApproved']==0)
                            return $text='<td><a class="vtip apr-pmt" data-asmt-id="'.$this->assRow['assessment_id'].'" data-approve="1" title="Click to approve the self-review." href="javascript:void(0);"><i class="glyphicon glyphicon-ok"></i> </a>&nbsp<a class="vtip apr-pmt" data-asmt-id="'.$this->assRow['assessment_id'].'"  data-approve="2" title="Click to reject the self-review." href="javascript:void(0);"><i class="glyphicon glyphicon-remove"></i> </a></td>';		
                        if($this->assRow['isApproved']==2)
                            return $text='<td><span class="vtip" title="Review was rejected by Admin." href="#">Rejected</span></td>';				
		}
		
		return $this->printDataCell('&nbsp;');
		
	
	}
	private function printPostReviewProgressColumn(){
	
		if(($this->isAdmin && $this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && $this->assRow['subAssessmentType']!=1 && $this->assRow['aqs_status']==1 && !empty($this->assRow['data_by_role'][4]['status']) && $this->assRow['data_by_role'][4]['status']==1) ){
			return $this->printDataCell('<span class="vtip" title="Post Review filled percentage">'.($this->assRow['postreviewpercent']?$this->assRow['postreviewpercent']:'0').'%</span>
					<br/><a class="vtip" href="?isPop=1&controller=diagnostic&action=postreview&assessment_id='.($this->assRow['assessment_id']>0?$this->assRow['assessment_id']:'').'&aqsdata_id='.($this->assRow['aqsdata_id']>0?$this->assRow['aqsdata_id']:'').('&assessor_id='.$this->assRow['data_by_role'][4]['user_id']).'" title="View/Edit Post Review Form">'.($this->assRow['post_rev_status']==1?'Edit':'View').'</a>');
		}
		elseif($this->isExternalReviewer && $this->currentRowIs==$this::SCHOOL_ASSESSMENT_ROW && $this->assRow['subAssessmentType']!=1 && $this->assRow['aqs_status']==1 && isset($this->assRow['data_by_role'][4]) && intval($this->assRow['data_by_role'][4]['percComplete'])=='100' && $this->assRow['data_by_role'][4]['status']==1 && $this->user['user_id']==$this->assRow['data_by_role'][4]['user_id']){
			return $this->printDataCell('<span class="vtip" title="Post Review filled percentage">'.($this->assRow['postreviewpercent']?$this->assRow['postreviewpercent']:'0').'%</span>
					<br/><a class="vtip" href="?isPop=1&controller=diagnostic&action=postreview&assessment_id='.($this->assRow['assessment_id']>0?$this->assRow['assessment_id']:'').'&aqsdata_id='.($this->assRow['aqsdata_id']>0?$this->assRow['aqsdata_id']:'').('&assessor_id='.$this->assRow['data_by_role'][4]['user_id']).'" title="View/Edit Post Review Form">'.($this->assRow['post_rev_status']==1?'View':'Edit').'</a>');
		}
		return $this->printDataCell('&nbsp;');
		
	}
       

}