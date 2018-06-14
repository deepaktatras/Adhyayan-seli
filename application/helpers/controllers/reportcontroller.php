<?php
class reportController extends controller{
	
	function reportAction(){
		$report_id=empty($_GET['report_id'])?0:$_GET['report_id'];
		$assessment_id=empty($_GET['assessment_id'])?0:$_GET['assessment_id'];
		$diagnostic_id=empty($_GET['diagnostic_id'])?0:$_GET['diagnostic_id'];
		$group_assessment_id=empty($_GET['group_assessment_id'])?0:$_GET['group_assessment_id'];
		$pYears = empty($_GET['years'])?0:$_GET['years'];
		$pMonths = empty($_GET['months'])?0:$_GET['months'];
		$diagnosticModel=new diagnosticModel();
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;		
			//else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || (in_array("take_external_assessment",$this->user['capabilities']) && $report_id==1) || $report_id==5)){
                else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || (in_array("take_external_assessment",$this->user['capabilities'])) || $report_id==5)){	
			$this->_notPermitted=1;
		}else if($group_assessment_id==0 && $assessment_id==0){
			$this->_is404=1;
		}else if($report_id>0 && $assessment=$assessment_id>0?$diagnosticModel->getAssessmentById($assessment_id):$diagnosticModel->getGroupAssessmentByGAId($group_assessment_id)){									
//			if((!empty($assessment['statusByRole'][4]) && $assessment['report_published']==1 && in_array("take_external_assessment",$this->user['capabilities'])) ||(($report_id==5||$report_id==4)&& (in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) &&  $assessment['report_published']==1))
//				{					
//					$this->_notPermitted=1;
//					return;
//				}                               
				// compute and save award for school review only - check diagnostic is for school by assessment_type_id=1				
				//$assessmentRow = $diagnosticModel->getAssessmentById($assessment_id);                                       
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
                       //teacher reports - principal and school admin
                        elseif($isSchoolReview==0 && (in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) &&  $assessment['report_published']==1);
                       //teacher reports - reviewer                        
                        elseif($assessment['assessment_type_id']==2 && $assessment_id>0 && ($this->user['user_id']==$group_asmt_external||$this->user['user_id']==$group_asmt_internal));
                        //teacher reports - admin
                        elseif($isSchoolReview==0 && in_array("view_all_assessments",$this->user['capabilities']));
                        else{
                            $this->_notPermitted=1;
                            return;
                        }
                        //teacher review
                       // elseif( !($isSchoolReview==0 && ) )
				if($assessment_id>0 && $isSchoolReview>0){
					$awardModel = new awardModel();
					if($awardModel->createDataSingleAssessment($assessment_id)){						
							$tkpas=$awardModel->getData($assessment_id);
						if(count($tkpas)>=6){	
							$temp=array();						
							$awardHelper = new awardHelper();
							$awardHelper->findKey($tkpas,'internalRating')==true?$awardHelper->computeAward($assessment_id,'internalRating',$tkpas):'';
							$awardHelper->findKey($tkpas,'externalRating')==true?$awardHelper->computeAward($assessment_id,'externalRating',$tkpas):'';
						}
					}
				}					
			$this->set("report_id",$report_id);
			$this->set("assessment",$assessment);
			$this->set("assessment_id",$assessment_id);
			$this->set("diagnostic_id",$diagnostic_id);
			$this->set("group_assessment_id",$group_assessment_id);
			$this->set("pMonths",$pMonths);
			$this->set("pYears",$pYears);
		}else{
			$this->_is404=1;
		}
	}
	function networkAction(){
		if(in_array("view_all_assessments",$this->user['capabilities']))
		{
			
		}
		else
			$this->_notPermitted=1;
	}
	function teacherAction(){
		$report_id=empty($_GET['report_id'])?0:$_GET['report_id'];
		$assessment_id=empty($_GET['assessment_id'])?0:$_GET['assessment_id'];
		$diagnostic_id=empty($_GET['diagnostic_id'])?0:$_GET['diagnostic_id'];
		$group_assessment_id=empty($_GET['group_assessment_id'])?0:$_GET['group_assessment_id'];
		$pYears = empty($_GET['years'])?0:$_GET['years'];
		$pMonths = empty($_GET['months'])?0:$_GET['months'];
		$diagnosticModel=new diagnosticModel();
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
			//else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || (in_array("take_external_assessment",$this->user['capabilities']) && $report_id==1) || $report_id==5)){
			if($group_assessment_id==0 && $assessment_id==0){
				$this->_is404=1;
			}else if($report_id>0 && $assessment=$assessment_id>0?$diagnosticModel->getAssessmentById($assessment_id):$diagnosticModel->getGroupAssessmentByGAId($group_assessment_id)){
				//if((!empty($assessment['statusByRole'][4]) && $assessment['report_published']==1 && in_array("take_external_assessment",$this->user['capabilities']))||(!in_array(1,$this->user['role_ids'])||!in_array(2,$this->user['role_ids'])) ||!((($report_id==5 || $report_id==4) && (in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids']) || $assessment['user_ids'][1]==$this->user['user_id'])) &&  $assessment['report_published']!=1))
				$group_asmt_external= empty($assessment['userIdByRole'][4])?0:$assessment['userIdByRole'][4];	
                                $group_asmt_internal= empty($assessment['userIdByRole'][3])?0:$assessment['userIdByRole'][3];
                               // print_r($assessment);
                                //echo 'hi'.$assessment['assessment_type_id'];
                                if($assessment['assessment_type_id']==2 && ((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) ));
                                //admin - reviewer
                                elseif($assessment['assessment_type_id']==2 && $assessment_id>0 && ($this->user['user_id']==$group_asmt_external||$this->user['user_id']==$group_asmt_internal));
                                //teacher reports - admin
                                elseif($assessment['assessment_type_id']==2 && in_array("view_all_assessments",$this->user['capabilities']));
                                else{
                                 $this->_notPermitted=1;
                                        return;
                                    }
				$this->_render=false;
				$reportObject = null;
				$tMonths=$pMonths+($pYears*12);				
				$conductedDate=date("M-Y");				
				$validDate=date("M-Y",strtotime("+$tMonths month"));
				if($report_id==7 || $report_id==10)
					$reportObject=new individualReport($assessment_id,2,$report_id,$conductedDate,$validDate);
				else if($report_id==4 || $report_id==8)
					$reportObject=new groupReport($group_assessment_id,$report_id,$diagnostic_id,$conductedDate,$validDate);
					//include (ROOT . 'application' . DS . 'views' . DS . "report" . DS . 'singleteacher.php');
				//include ROOT . 'library' . DS . 'tcpdf' . DS . 'tcpdf.php';				
				$reportObject->generateOutput();
				
			}else{
				$this->_is404=1;
			}
	}
	function recommendationsAction(){
		$report_id=empty($_GET['report_id'])?0:$_GET['report_id'];
		$assessment_id=empty($_GET['assessment_id'])?0:$_GET['assessment_id'];
		$diagnostic_id=empty($_GET['diagnostic_id'])?0:$_GET['diagnostic_id'];
		$group_assessment_id=empty($_GET['group_assessment_id'])?0:$_GET['group_assessment_id'];
		$pYears = empty($_GET['years'])?0:$_GET['years'];
		$pMonths = empty($_GET['months'])?0:$_GET['months'];
		$diagnosticModel=new diagnosticModel();
		
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;			
			else if(!(in_array("edit_all_submitted_assessments",$this->user['capabilities']) )){
				$this->_notPermitted=1;
			}else if($group_assessment_id==0 && $assessment_id==0){
				$this->_is404=1;
			}else if($report_id==4 && $assessment=$assessment_id>0?$diagnosticModel->getAssessmentById($assessment_id):$diagnosticModel->getGroupAssessmentByGAId($group_assessment_id)){				
				if((!empty($assessment['statusByRole'][4]) && $assessment['report_published']==1))
				{
					$this->_notPermitted=1;
					return;
				}				
				$reportModel = new reportModel(4);							
				$diagnostic = $diagnosticModel->getDiagnostic($diagnostic_id);					
				//get relevant questions of diagnostic				
				$kpas = $diagnostic['kpa_recommendations']==1?$diagnosticModel->getKpasForDiagnostic($diagnostic_id):array();
				$kqs = $diagnostic['kq_recommendations']==1?$diagnosticModel->getKeyQuestionsForDiagnostic($diagnostic_id):array();
				$cqs = $diagnostic['cq_recommendations']==1?$diagnosticModel->getCoreQuestionsForDiagnostic($diagnostic_id):array();
				$js = $diagnostic['js_recommendations']==1?$diagnosticModel->getJudgementalStatementsForDiagnostic($diagnostic_id):array();
				$this->set('kpas',$kpas);
				$this->set('kqs',$kqs);
				$this->set('cqs',$cqs);
				$this->set('js',$js);			
				$this->set('DiagnosticName',$diagnostic['name']);
				$this->set('client_name',$assessment['client_name']);			
				$this->set('reportObj',$reportModel);				
				$this->set('group_assessment_id',$group_assessment_id);
				$this->set('diagnostic_id',$diagnostic_id);		
			}else{
				$this->_is404=1;
			}
	}

}