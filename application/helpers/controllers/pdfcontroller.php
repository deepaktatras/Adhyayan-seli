<?php
class pdfController extends Controller{
	
	function __construct( $controller, $action) {
		parent::__construct($controller, $action,$ajaxRequest=0,$isPDF=1);			
	}
	function pdfAction(){				
		$report_id=empty($_GET['report_id'])?0:$_GET['report_id'];
		$assessment_id=empty($_GET['assessment_id'])?0:$_GET['assessment_id'];
		$diagnostic_id=empty($_GET['diagnostic_id'])?0:$_GET['diagnostic_id'];
		$group_assessment_id=empty($_GET['group_assessment_id'])?0:$_GET['group_assessment_id'];
		$diagnosticModel=new diagnosticModel();
		
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
			//else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || (in_array("take_external_assessment",$this->user['capabilities']) && $report_id==1) || ($report_id==5))){
		 if($group_assessment_id==0 && $assessment_id==0){
			$this->_is404=1;
		}else if($report_id>0 && $assessment=$assessment_id>0?$diagnosticModel->getAssessmentById($assessment_id):$diagnosticModel->getGroupAssessmentByGAId($group_assessment_id)){
			$isSchoolReview = $assessment['assessment_type_id']==1?1:0;
                        $group_asmt_external= empty($assessment['userIdByRole'][4])?0:$assessment['userIdByRole'][4];	
                                $group_asmt_internal= empty($assessment['userIdByRole'][3])?0:$assessment['userIdByRole'][3];
                        //Admin can view all the school review reports for the submitted reviews                                   
                        if( in_array("view_all_assessments",$this->user['capabilities']) && in_array("generate_submitted_asmt_reports",$this->user['capabilities']) && $isSchoolReview>0 && $assessment['subAssessmentType']!=1 && $assessment['aqs_status']==1 && $assessment['statuses'][0]=='1' && $assessment['statuses'][1]=='1') ;
                        //External reviewer can view unsubmitted assessment reports of school reviews where he was the external reviewer if he has generate_unsubmitted_asmt_reports capability
                        elseif( in_array("generate_unsubmitted_asmt_reports",$this->user['capabilities']) && $isSchoolReview>0 && $assessment['subAssessmentType']!=1 && $assessment['aqs_status']==1 && $assessment['statuses'][0]=='1' && intval(explode(',',$assessment['perc'])[1])==100 && $this->user['user_id']== $assessment['user_ids'][1] && $assessment['report_published']!=1) ;
                        //view published school reports of his own school if the capabilities contain view_published_own_school_reports                                    
                        elseif( $this->user['client_id']==$assessment['client_id'] && $isSchoolReview>0 && $assessment['subAssessmentType']!=1 && in_array("view_published_own_school_reports",$this->user['capabilities']) && $assessment['aqs_status']==1 && $assessment['statuses'][0]=='1' && $assessment['statuses'][1]=='1' && $assessment['report_published']==1) ;
                        //for online self-review, school-admin,principal and admin must be able to view report
                        elseif( $isSchoolReview>0 && $assessment['aqs_status']==1 && $assessment['subAssessmentType']==1 && $assessment['statuses'][0]==1 && (in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])||in_array(1,$this->user['role_ids']) ||in_array(2,$this->user['role_ids']) )) ;
                       //admin - reviewer
                        elseif($assessment['assessment_type_id']==2 && $assessment_id>0 && ($this->user['user_id']==$group_asmt_external||$this->user['user_id']==$group_asmt_internal));
                        //teacher reports - principal and school admin
                      //  elseif($isSchoolReview==0 && ($report_id==5||$report_id==4)&& (in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) &&  $assessment['report_published']==1);
                        //teacher reports - admin
                        elseif($isSchoolReview==0 && in_array("view_all_assessments",$this->user['capabilities']));
                        else{
                            $this->_notPermitted=1;
                            return;
                        }
			$this->set("report_id",$report_id);
			$this->set("assessment",$assessment);
			$this->set("assessment_id",$assessment_id);
			$this->set("diagnostic_id",$diagnostic_id);
			$this->set("group_assessment_id",$group_assessment_id);
			if($assessment_id>0){
				$report=isset($report[$_GET['report_id']])?$report[$_GET['report_id']]:null;
			}else{
				$report=isset($report['report_id']) && $report['report_id']==$_GET['report_id']?$report:null;
			}
//print_r($assessment);die;
			/*if(!$report){
				$this->apiResult["message"] = "Wrong report id\n";
			}else if($report['aqs_status']!=1){
				$this->apiResult["message"] = "Assessment not completed yet\n";
			}else if($report['isGenerated']==0 && empty($_GET['years']) && empty($_GET['months'])){
				$this->apiResult["message"] = "Report not generated yet\n";
			}else*/{
				$years=empty($_GET['years'])?0:$_GET['years'];
				$months=empty($_GET['months'])?0:$_GET['months'];
				$tMonths=$months+($years*12);
				$conductedDate=date("M-Y");
				$validDate=date("M-Y",strtotime("+$tMonths month"));	
				$subAssessmentType = $assessment['subAssessmentType'];
				$diagnostic_id=empty($_GET['diagnostic_id'])?0:$_GET['diagnostic_id'];
				$reportObject=$assessment_id>0?new pdfReport($assessment_id,$subAssessmentType,$_GET['report_id'],$conductedDate,$validDate):new groupReport($group_assessment_id,$_GET['report_id'],$diagnostic_id,$conductedDate,$validDate);
				$data= $reportObject->generateOutput();
				
				$reportType = $_GET['report_id'];
				
				
			}
			include (ROOT . 'application' . DS . 'views' . DS . "pdf" . DS . 'pdf.php');
		}else{
			$this->_is404=1;
		}	
	}	
}