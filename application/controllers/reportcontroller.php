<?php
class reportController extends controller{
	
	function reportAction(){
		$report_id=empty($_GET['report_id'])?0:$_GET['report_id'];
		$assessment_id=empty($_GET['assessment_id'])?0:$_GET['assessment_id'];
		$diagnostic_id=empty($_GET['diagnostic_id'])?0:$_GET['diagnostic_id'];
		$group_assessment_id=empty($_GET['group_assessment_id'])?0:$_GET['group_assessment_id'];
		$lang_id=empty($_GET['lang_id'])?DEFAULT_LANGUAGE:$_GET['lang_id'];
		$pYears = empty($_GET['years'])?0:$_GET['years'];
		$pMonths = empty($_GET['months'])?0:$_GET['months'];
		$diagnosticModel=new diagnosticModel();
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;		
			//else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || (in_array("take_external_assessment",$this->user['capabilities']) && $report_id==1) || $report_id==5)){
                else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || (in_array("take_external_assessment",$this->user['capabilities'])) || $report_id==5 || $report_id==9)){	
			$this->_notPermitted=1;
		}else if($group_assessment_id==0 && $assessment_id==0){
			$this->_is404=1;
		}else if($report_id>0 && $assessment=$assessment_id>0?$diagnosticModel->getAssessmentById($assessment_id,$lang_id):$diagnosticModel->getGroupAssessmentByGAId($group_assessment_id)){									
//			if((!empty($assessment['statusByRole'][4]) && $assessment['report_published']==1 && in_array("take_external_assessment",$this->user['capabilities'])) ||(($report_id==5||$report_id==4)&& (in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) &&  $assessment['report_published']==1))
//				{					
//					$this->_notPermitted=1;
//					return;
//				}                               
				// compute and save award for school review only - check diagnostic is for school by assessment_type_id=1				
				//$assessmentRow = $diagnosticModel->getAssessmentById($assessment_id);
                                //print_r($assessment);
                                //print_r($this->user);
                                $isNetworkAdmin = ($assessment['network_id']==$this->user['network_id'] && in_array(7,$this->user['role_ids']))==1?1:0;
				$isSchoolReview = $assessment['assessment_type_id']==1?1:0;
                                $isCollegeReview = $assessment['assessment_type_id']==5?1:0;
                                $group_asmt_external= empty($assessment['userIdByRole'][4])?0:$assessment['userIdByRole'][4];	
                                $group_asmt_internal= empty($assessment['userIdByRole'][3])?0:$assessment['userIdByRole'][3];                                
                        //Admin can view all the school review reports for the submitted reviews                                   
                        if( in_array("view_all_assessments",$this->user['capabilities']) && in_array("generate_submitted_asmt_reports",$this->user['capabilities']) && $isSchoolReview>0 && $assessment['subAssessmentType']!=1 && $assessment['aqs_status']==1 && $assessment['statuses'][0]=='1' && $assessment['statuses'][1]=='1') ;
                        //External reviewer can view unsubmitted assessment reports of school reviews where he was the external reviewer if he has generate_unsubmitted_asmt_reports capability
                        elseif( in_array("generate_unsubmitted_asmt_reports",$this->user['capabilities']) && ($isSchoolReview>0 || $isCollegeReview>0) && $assessment['subAssessmentType']!=1 && $assessment['aqs_status']==1 && $assessment['statuses'][0]=='1' && intval(explode(',',$assessment['perc'])[1])==100 && $this->user['user_id']== $assessment['user_ids'][1] && $assessment['report_published']!=1) ;
                        //view published school reports of his own school if the capabilities contain view_published_own_school_reports                                    
                        elseif( $this->user['client_id']==$assessment['client_id'] && ($isSchoolReview>0 || $isCollegeReview>0) && $assessment['subAssessmentType']!=1 && in_array("view_published_own_school_reports",$this->user['capabilities']) && $assessment['aqs_status']==1 && $assessment['statuses'][0]=='1' && $assessment['statuses'][1]=='1' && $assessment['report_published']==1) ;
                        //for online self-review, school-admin,principal and admin must be able to view report
                        elseif( ($isSchoolReview>0 || $isCollegeReview>0) && $assessment['aqs_status']==1 && $assessment['subAssessmentType']==1 && $assessment['statuses'][0]==1 && (in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])||in_array(1,$this->user['role_ids']) ||in_array(2,$this->user['role_ids']) )) ;
                       //teacher reports - principal and school admin
                        elseif($isSchoolReview==0 && (in_array(6,$this->user['role_ids'])|| in_array(5,$this->user['role_ids'])) &&  $assessment['report_published']==1);
                       //teacher reports - reviewer                        
                        elseif(($assessment['assessment_type_id']==2 || $assessment['assessment_type_id']==4) && $assessment_id>0 && ($this->user['user_id']==$group_asmt_external||$this->user['user_id']==$group_asmt_internal));
                        elseif($isNetworkAdmin && $isSchoolReview==0 && (in_array("generate_unsubmitted_asmt_reports",$this->user['capabilities']) || in_array("generate_submitted_asmt_reports",$this->user['capabilities'])));
                        //teacher reports - admin
                        elseif($isSchoolReview==0 && in_array("view_all_assessments",$this->user['capabilities']));
                        else{
                            $this->_notPermitted=1;
                            return;
                        }
                        //teacher review
                       // elseif( !($isSchoolReview==0 && ) )
				if($assessment_id>0 && ($isSchoolReview>0 || $isCollegeReview>0)){
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
			$this->set("lang_id",$lang_id);
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
        function reportRound2Action(){
		$report_id=empty($_GET['report_id'])?0:$_GET['report_id'];
		$assessment_id=empty($_GET['assessment_id'])?0:$_GET['assessment_id'];
		$diagnostic_id=empty($_GET['diagnostic_id'])?0:$_GET['diagnostic_id'];
                $client_id=empty($_GET['client_id'])?0:$_GET['client_id'];
		$group_assessment_id=empty($_GET['group_assessment_id'])?0:$_GET['group_assessment_id'];
		$lang_id=empty($_GET['lang_id'])?DEFAULT_LANGUAGE:$_GET['lang_id'];
		$pYears = empty($_GET['years'])?0:$_GET['years'];
		$pMonths = empty($_GET['months'])?0:$_GET['months'];
		$diagnosticModel=new diagnosticModel();
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;		
			//else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || (in_array("take_external_assessment",$this->user['capabilities']) && $report_id==1) || $report_id==5)){
                else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || (in_array("take_external_assessment",$this->user['capabilities'])) || $report_id==5 || $report_id==9)){	
			$this->_notPermitted=1;
		}else if($group_assessment_id==0 && $assessment_id==0){
			$this->_is404=1;
		}else if($report_id>0 && $assessment=$assessment_id>0?$diagnosticModel->getAssessmentById($assessment_id,$lang_id):$diagnosticModel->getGroupAssessmentByGAId($group_assessment_id)){									
//			if((!empty($assessment['statusByRole'][4]) && $assessment['report_published']==1 && in_array("take_external_assessment",$this->user['capabilities'])) ||(($report_id==5||$report_id==4)&& (in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) &&  $assessment['report_published']==1))
//				{					
//					$this->_notPermitted=1;
//					return;
//				}                               
				// compute and save award for school review only - check diagnostic is for school by assessment_type_id=1				
				//$assessmentRow = $diagnosticModel->getAssessmentById($assessment_id);
                                //print_r($assessment);
                                //print_r($this->user);
                                $isNetworkAdmin = ($assessment['network_id']==$this->user['network_id'] && in_array(7,$this->user['role_ids']))==1?1:0;
				$isSchoolReview = $assessment['assessment_type_id']==1?1:0;
                                $isCollegeReview = $assessment['assessment_type_id']==5?1:0;
                                $group_asmt_external= empty($assessment['userIdByRole'][4])?0:$assessment['userIdByRole'][4];	
                                $group_asmt_internal= empty($assessment['userIdByRole'][3])?0:$assessment['userIdByRole'][3];                                
                        //Admin can view all the school review reports for the submitted reviews                                   
                        if( in_array("view_all_assessments",$this->user['capabilities']) && in_array("generate_submitted_asmt_reports",$this->user['capabilities']) && $isSchoolReview>0 && $assessment['subAssessmentType']!=1 && $assessment['aqs_status']==1 && $assessment['statuses'][0]=='1' && $assessment['statuses'][1]=='1') ;
                        //External reviewer can view unsubmitted assessment reports of school reviews where he was the external reviewer if he has generate_unsubmitted_asmt_reports capability
                        elseif( in_array("generate_unsubmitted_asmt_reports",$this->user['capabilities']) && ($isSchoolReview>0 || $isCollegeReview>0) && $assessment['subAssessmentType']!=1 && $assessment['aqs_status']==1 && $assessment['statuses'][0]=='1' && intval(explode(',',$assessment['perc'])[1])==100 && $this->user['user_id']== $assessment['user_ids'][1] && $assessment['report_published']!=1) ;
                        //view published school reports of his own school if the capabilities contain view_published_own_school_reports                                    
                        elseif( $this->user['client_id']==$assessment['client_id'] && ($isSchoolReview>0 || $isCollegeReview>0) && $assessment['subAssessmentType']!=1 && in_array("view_published_own_school_reports",$this->user['capabilities']) && $assessment['aqs_status']==1 && $assessment['statuses'][0]=='1' && $assessment['statuses'][1]=='1' && $assessment['report_published']==1) ;
                        //for online self-review, school-admin,principal and admin must be able to view report
                        elseif( ($isSchoolReview>0 || $isCollegeReview>0) && $assessment['aqs_status']==1 && $assessment['subAssessmentType']==1 && $assessment['statuses'][0]==1 && (in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])||in_array(1,$this->user['role_ids']) ||in_array(2,$this->user['role_ids']) )) ;
                       //teacher reports - principal and school admin
                        elseif($isSchoolReview==0 && (in_array(6,$this->user['role_ids'])|| in_array(5,$this->user['role_ids'])) &&  $assessment['report_published']==1);
                       //teacher reports - reviewer                        
                        elseif(($assessment['assessment_type_id']==2 || $assessment['assessment_type_id']==4) && $assessment_id>0 && ($this->user['user_id']==$group_asmt_external||$this->user['user_id']==$group_asmt_internal));
                        elseif($isNetworkAdmin && $isSchoolReview==0 && (in_array("generate_unsubmitted_asmt_reports",$this->user['capabilities']) || in_array("generate_submitted_asmt_reports",$this->user['capabilities'])));
                        //teacher reports - admin
                        elseif($isSchoolReview==0 && in_array("view_all_assessments",$this->user['capabilities']));
                        else{
                            $this->_notPermitted=1;
                            return;
                        }
                        //teacher review
                       // elseif( !($isSchoolReview==0 && ) )
				if($assessment_id>0 && ($isSchoolReview>0 || $isCollegeReview>0)){
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
			$this->set("lang_id",$lang_id);
			$this->set("assessment",$assessment);
			$this->set("assessment_id",$assessment_id);
			$this->set("diagnostic_id",$diagnostic_id);
                        $this->set("client_id",$client_id);
			$this->set("group_assessment_id",$group_assessment_id);
			$this->set("pMonths",$pMonths);
			$this->set("pYears",$pYears);
		}else{
			$this->_is404=1;
		}
	}
        
        function reportallAction(){
            ini_set('max_execution_time', 1200000);
            $diagnosticModel=new diagnosticModel();
            $group_assessment_id=empty($_GET['group_assessment_id'])?0:$_GET['group_assessment_id'];
            $report_id=$_GET['report_id'];
            if($group_assessment_id>0 && $reports=$diagnosticModel->getSubAssReportsByGroupAssessmentId($group_assessment_id)){
                		$assessment=$diagnosticModel->getGroupAssessmentByGAId($group_assessment_id);
                                //print_r($assessment);
                                $client_name=isset($assessment['client_name'])?$assessment['client_name']:'reports';
				$reportsIndividual = array_filter($reports,function($var){
					if($var['report_id']==5 || $var['report_id']==9)
						return array($var['assessment_id']=>$var['user_names'][0]);
				});
                                
                                if($_GET['report_id']==5){
                                $suffix = '';
                                $pdfModel = new pdfModel();
                                $reportType=$_GET['report_id'];
                                $suffix =$pdfModel->getReportName($reportType);
                                $suffix = $suffix['report_name'];
                                   
                                $i=0;
                                $zip = new ZipArchive();
                                $zip_path="tmp/".$client_name." ".$suffix.".zip";
                                $source="".ROOT."uploads".DS."download_pdf".DS."";
                                $tmpPath = "".$client_name." ".$suffix."".DS."";
                                if ($zip->open($zip_path, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
                                die ("An error occurred creating your ZIP file.");
                                }
                                $pdf=new pdfController('pdf','pdf');
                                foreach($reportsIndividual as $key=>$tchr){
                                
                                 
                                $teacher_data=$this->loadTeacherData($tchr['assessment_id']);
                                //echo"<pre>";
                                //print_r($teacher_data);
                                //echo"</pre>";
                               
                                $pdf->pdfAction(array("report_id"=>$_GET['report_id'],
                                                       "group_assessment_id"=>$_GET['group_assessment_id'],
                                                       "assessment_id"=>$tchr['assessment_id'],
                                                       "diagnostic_id"=>$_GET['diagnostic_id'],
                                                       "years"=>isset($_GET['years'])?$_GET['years']:'',
                                                       "months"=>isset($_GET['months'])?$_GET['months']:''
                                                       ));
                                $file=$teacher_data['name']['value']; echo '<br>';//die;
                                //echo $dstfile1="".$tmpPath."".$this->getFileName($_GET['report_id'],$file).".pdf";echo '<br>';
                                //echo $source1="".$source."".$this->getFileName($_GET['report_id'],$file).".pdf";echo '<br>';
                                $dstfile1="".$tmpPath."".$this->getFileName($_GET['report_id'],$file).".pdf";echo '<br>';
                                $source1="".$source."".$this->getFileName($_GET['report_id'],$file).".pdf";echo '<br>';
                                //echo $dstfile1="Tatrasdata/Pratibha Teacher Test.pdf";
                                //echo $source1="/opt/lampp/htdocs/Adhyayan/uploads/download_pdf/Pratibha Teacher Test.pdf";
                                if(is_file($source1)){
                                $zip->addFile($source1,$dstfile1);
                                $i++;
                                }
                               
                                }
                                  
                                  $zip->close();
                                  if($i>0){
                                  header('Content-type: application/zip');
                                  header('Content-Disposition: attachment; filename="'.$client_name.' '.$suffix.'.zip"');
                                  readfile("tmp/".$client_name." ".$suffix.".zip"); 
                                  unlink(ROOT."tmp/".$client_name." ".$suffix.".zip");
                                  }else{
                                  echo"No PDF to show";    
                                  }
                
                                }
                                else if($_GET['report_id']==7){
                                $suffix = '';
                                $pdfModel = new pdfModel();
                                $reportType=$_GET['report_id'];
                                $suffix =$pdfModel->getReportName($reportType);
                                $suffix = $suffix['report_name'];
                                   
                                $i=0;
                                $zip = new ZipArchive();
                                $zip_path="tmp/".$client_name." ".$suffix.".zip";
                                $source="".ROOT."uploads".DS."download_pdf".DS."";
                                $tmpPath = "".$client_name." ".$suffix.DS."";
                                if ($zip->open($zip_path, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
                                die ("An error occurred creating your ZIP file.");
                                }
                                $pdf=new pdfController('pdf','pdf');
                                foreach($reportsIndividual as $key=>$tchr){
                                
                                 
                                $teacher_data=$this->loadTeacherData($tchr['assessment_id']);
                                //echo"<pre>";
                                //print_r($teacher_data);
                                //echo"</pre>";
                               
                                $pdf->pdfAction(array("report_id"=>$_GET['report_id'],
                                                       "group_assessment_id"=>$_GET['group_assessment_id'],
                                                       "assessment_id"=>$tchr['assessment_id'],
                                                       "diagnostic_id"=>$_GET['diagnostic_id'],
                                                       "years"=>isset($_GET['years'])?$_GET['years']:'',
                                                       "months"=>isset($_GET['months'])?$_GET['months']:''
                                                       ));
                                
                                $file=$teacher_data['name']['value']; //echo '<br>';
                                //echo $this->getFileName($_GET['report_id']);echo '<br>';
                                //echo $dstfile1="".$tmpPath."".$this->getFileName($_GET['report_id'],$file).".pdf";echo '<br>';die;
                                //echo $source1="".$source."".$this->getFileName($_GET['report_id'],$file).".pdf";echo '<br>';
                                $dstfile1="".$tmpPath."".$this->getFileName($_GET['report_id'],$file).".pdf";echo '<br>';//die;
                                $source1="".$source."".$this->getFileName($_GET['report_id'],$file).".pdf";echo '<br>';
                            
                                if(is_file($source1)){
                                $zip->addFile($source1,$dstfile1);
                                $i++;
                                }
                               
                                }
                                  
                                  $zip->close();
                                  if($i>0){
                                  header('Content-type: application/zip');
                                  header('Content-Disposition: attachment; filename="'.$client_name.' '.$suffix.'.zip"');
                                  readfile("tmp/".$client_name." ".$suffix.".zip"); 
                                  unlink(ROOT."tmp/".$client_name." ".$suffix.".zip");
                                  }else{
                                  echo"No PDF to show";    
                                  }
                
                                }
                                
                                else if($_GET['report_id']==9){
                                $i=0;
                                $zip = new ZipArchive();
                                $zip_path="tmp/".$client_name.".zip";
                                $source="".ROOT."uploads".DS."download_pdf".DS."";
                                $tmpPath = "".$client_name."".DS."";
                                if ($zip->open($zip_path, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
                                die ("An error occurred creating your ZIP file.");
                                }
                                $pdf=new pdfController('pdf','pdf');
                                foreach($reportsIndividual as $key=>$tchr){
                                
                                 
                                $student_data=$this->loadStudentData($tchr['assessment_id']);
//                                echo"<pre>";
//                                print_r($student_data);
//                                echo"</pre>";
                                $pdf->pdfAction(array("report_id"=>$_GET['report_id'],
                                                       "group_assessment_id"=>$_GET['group_assessment_id'],
                                                       "assessment_id"=>$tchr['assessment_id'],
                                                       "diagnostic_id"=>$_GET['diagnostic_id'],
                                                       "years"=>isset($_GET['years'])?$_GET['years']:'',
                                                       "months"=>isset($_GET['months'])?$_GET['months']:''
                                                       ));
                                $file=!empty($student_data['student-UID']['value'])?"".$student_data['student-UID']['value']."":$student_data['name']['value'];; 
                                $dstfile1="".$tmpPath."".$this->getFileName($_GET['report_id'],$file).".pdf";
                                $source1="".$source."".$this->getFileName($_GET['report_id'],$file).".pdf";
                                if(is_file($source1)){
                                $zip->addFile($source1,$dstfile1);
                                $i++;
                                }
                               
                                }
                                  
                                  $zip->close();
                                  if($i>0){
                                  header('Content-type: application/zip');
                                  header('Content-Disposition: attachment; filename="'.$client_name.'.zip"');
                                  readfile("tmp/".$client_name.".zip"); 
                                  unlink(ROOT."tmp/".$client_name.".zip");
                                  }else{
                                  echo"No PDF to show";    
                                  }
                } 
            }else{
            echo"Problem while downloading";    
            }
            
            $this->_render = false;
        }
        
        protected function loadStudentData($assessmentId){
		
			$assessmentModel=new assessmentModel();
			return $assessmentModel->getStudentInfo($assessmentId);
		
	}
        
        
        protected function loadTeacherData($assessmentId){
		
			$assessmentModel=new assessmentModel();
			return $assessmentModel->getTeacherInfo($assessmentId);
		
	}
        
        public function getFileName($reportType,$schoolName)
	{
		$suffix = '';
		$pdfModel = new pdfModel();
		$suffix =$pdfModel->getReportName($reportType);
		$suffix = $suffix['report_name'];
                if($reportType==9){
                $fileName =  $schoolName;    
                }else if($reportType==5 || $reportType==7){
                $fileName =  $suffix." ".$schoolName;  
                }else{
		$fileName =  $schoolName.' '.$suffix.' Card';
                }
		$fileName = htmlentities($fileName, ENT_QUOTES, 'UTF-8');
		$fileName = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $fileName);
		$fileName = html_entity_decode($fileName, ENT_QUOTES, 'UTF-8');
                if($reportType==5 || $reportType==7){
		$fileName = preg_replace(array('~[^0-9a-z]~i', '~[ -]+~'), ' ', $fileName);
                }
                 if($reportType==5 || $reportType==7){
                    $fileName = ucwords($fileName);
                 }
		return trim($fileName, ' -');
	}
	function networkAction(){
		if(in_array("view_all_assessments",$this->user['capabilities']))
		{
			
		}
		else
			$this->_notPermitted=1;
	}
	function teacherAction(){
                error_reporting(0);
		$report_id=empty($_GET['report_id'])?0:$_GET['report_id'];
		$assessment_id=empty($_GET['assessment_id'])?0:$_GET['assessment_id'];
		$diagnostic_id=empty($_GET['diagnostic_id'])?0:$_GET['diagnostic_id'];
                $dept_id=empty($_GET['dept_id'])?0:$_GET['dept_id'];
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
                                if(($assessment['assessment_type_id']==2 || $assessment['assessment_type_id']==4) && ((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) ));
                                //admin - reviewer
                                elseif(($assessment['assessment_type_id']==2 || $assessment['assessment_type_id']==4) && $assessment_id>0 && ($this->user['user_id']==$group_asmt_external||$this->user['user_id']==$group_asmt_internal));
                                //teacher reports - admin
                                elseif(($assessment['assessment_type_id']==2 || $assessment['assessment_type_id']==4) && in_array("view_all_assessments",$this->user['capabilities']));
                                else{
                                 $this->_notPermitted=1;
                                        return;
                                    }
				$this->_render=false;
				$reportObject = null;
				$tMonths=$pMonths+($pYears*12);				
				//$conductedDate=date("m-Y");				
				//$validDate=date("m-Y",strtotime("+$tMonths month"));
                                if($assessment['school_aqs_pref_end_date']=="" || $assessment['school_aqs_pref_end_date']=="0000-00-00"){
                                $conductedDate = date ( "m-Y", strtotime($assessment['create_date']));
				$validDate = date ( "m-Y", strtotime ( "+$tMonths month", strtotime($assessment['create_date'])) );
                                }else{
                                $conductedDate = date ( "m-Y", strtotime($assessment['school_aqs_pref_end_date']));
				$validDate = date ( "m-Y", strtotime ( "+$tMonths month", strtotime($assessment['school_aqs_pref_end_date'])) );
                                }
                                
				if($report_id==7 || $report_id==10)
					$reportObject=new individualReport($assessment_id,2,$report_id,$conductedDate,$validDate);
				else if($report_id==4 || $report_id==8)
					$reportObject=new groupReport($group_assessment_id,$report_id,$diagnostic_id,$conductedDate,$validDate,0,$dept_id);
					//include (ROOT . 'application' . DS . 'views' . DS . "report" . DS . 'singleteacher.php');
				//include ROOT . 'library' . DS . 'tcpdf' . DS . 'tcpdf.php';				
				$reportObject->generateOutput();
				
			}else{
				$this->_is404=1;
			}
	}
	function actionPlanAction(){
                error_reporting(0);
		//$report_id=empty($_GET['report_id'])?0:$_GET['report_id'];
		$assessment_id=empty($_GET['assessment_id'])?0:$_GET['assessment_id'];
		//$diagnostic_id=empty($_GET['diagnostic_id'])?0:$_GET['diagnostic_id'];
                //$dept_id=empty($_GET['dept_id'])?0:$_GET['dept_id'];
		$group_assessment_id=empty($_GET['group_assessment_id'])?0:$_GET['group_assessment_id'];
		$action_plan_id=empty($_GET['id_c'])?0:$_GET['id_c'];
		$datesrange=empty($_GET['datesrange'])?0:$_GET['datesrange'];
                
                $actionModel = new actionModel();
                $details = $actionModel->getDetailsofAssessment($action_plan_id);
                //echo "<pre>";print_r($details);
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
                //$date = date ("Y-m-d", strtotime("".$details['frequency_days']."", strtotime($date)));
                //$datefinal = date ("Y-m-d", strtotime("-1 day", strtotime($date)));
                $dateex = date ("Y-m-d", strtotime($end_date));
                $array_dates[$ii]['fromDate']=$sdate;
                $array_dates[$ii]['endDate']=$dateex;
                $date = date ("Y-m-d", strtotime($dateex));
                $nextDate=date("d-m-Y",strtotime($date));
                }else{
                $date = date ("Y-m-d", strtotime("-1 day", strtotime($dateex)));
                $nextDate=date("d-m-Y",strtotime($date));
                if($date<date("Y-m-d")){
                $nextDate="Last Date is over";   
                }
                }

                //echo"<pre>";
                //print_r($array_dates);
                $array_dates_f=array();
                foreach($array_dates as $key=>$val){

                    $array_dates_f[]=$val;

                }
               // echo "<pre>";print_r($array_dates_f);
                $reportDates = array();
                if(!empty($datesrange)){
                    $reportDates= explode('/', $datesrange);
                     $start_date = $reportDates[0];
                     $end_date = $reportDates[1];
                }else{
                     $start_date = $date_start_real;
                     $end_date = $date_end_real;
                }
                
                $palnnedDate = array();
                if(!empty($datesrange)){
                if(!empty($array_dates_f)){
                    
                    $palnnedDateKey = -1;
                    foreach($array_dates_f as $key=>$val){
                        if($val['fromDate'] == $start_date && $val['endDate'] == $end_date){
                            //echo "yes";
                            $palnnedDateKey = $key;
                            break;
                           
                        }
                    }if($palnnedDateKey!=-1){
                        $palnnedDate['fromDate'] = $array_dates_f[$palnnedDateKey+1]['fromDate'];
                        $palnnedDate['endDate'] = $array_dates_f[$palnnedDateKey+1]['endDate'];
                    }
                }
                }else{
                    //$start_date = $date_start_real;
                    
                    foreach($array_dates_f as $key=>$val){
                        
                        if($val['endDate']<=date("Y-m-d")){
                         $end_date = $val['endDate'];   
                        }else{
                          $planned_start_date = $val['fromDate'];
                          $palnnedDate['fromDate'] = $planned_start_date;
                          $palnnedDate['endDate'] = $date_end_real;
                          break;
                        }
                    }
                    
                    
                }
                
               // print_r($reportDates);
               //print_r($palnnedDate);
               //echo $start_date;
               //echo $end_date;
                //$start_date = '2018-04-07';
               // $end_date = '2018-04-14';
		//$pYears = empty($_GET['years'])?0:$_GET['years'];
		//$pMonths = empty($_GET['months'])?0:$_GET['months'];
		$diagnosticModel=new diagnosticModel();
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
			//else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || (in_array("take_external_assessment",$this->user['capabilities']) && $report_id==1) || $report_id==5)){
			if($group_assessment_id==0 && $assessment_id==0){
				$this->_is404=1;
			}else if($assessment=$assessment_id>0?$diagnosticModel->getAssessmentById($assessment_id):$diagnosticModel->getGroupAssessmentByGAId($group_assessment_id)){
				//if((!empty($assessment['statusByRole'][4]) && $assessment['report_published']==1 && in_array("take_external_assessment",$this->user['capabilities']))||(!in_array(1,$this->user['role_ids'])||!in_array(2,$this->user['role_ids'])) ||!((($report_id==5 || $report_id==4) && (in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids']) || $assessment['user_ids'][1]==$this->user['user_id'])) &&  $assessment['report_published']!=1))
				$group_asmt_external= empty($assessment['userIdByRole'][4])?0:$assessment['userIdByRole'][4];	
                                $group_asmt_internal= empty($assessment['userIdByRole'][3])?0:$assessment['userIdByRole'][3];
                                /*echo 'hi'.$assessment['assessment_type_id'];
                                echo "<pre>";print_r($this->user['capabilities']);
                                if(($assessment['assessment_type_id']==1) && in_array("view_all_assessments",$this->user['capabilities']) && ((in_array(1,$this->user['role_ids'])||in_array(2,$this->user['role_ids'])) ));
                               
                                else{
                                 $this->_notPermitted=1;
                                        return;
                                    }
                                print_r($assessment);*/
                                
                                $rating_date = '';
                                if(!empty($assessment)) {
                                    if($assessment['assessment_type_id'] == 1 && $assessment['subAssessmentType'] == 1){
                                        $rating_date = !empty($assessment['rating_date'])?date("d-m-Y",strtotime($assessment['rating_date'])):'';
                                    }else{
                                        $rating_date = !empty($assessment['school_aqs_pref_end_date'])?date("d-m-Y",strtotime($assessment['school_aqs_pref_end_date'])):'';
                                    }           
                                }
				$this->_render=false;
				$reportObject = null;
				$tMonths=$pMonths+($pYears*12);				
				//$conductedDate=date("m-Y");				
				//$validDate=date("m-Y",strtotime("+$tMonths month"));
                                if($assessment['school_aqs_pref_end_date']=="" || $assessment['school_aqs_pref_end_date']=="0000-00-00"){
                                    $conductedDate = date ( "m-Y", strtotime($assessment['create_date']));
                                    $validDate = date ( "m-Y", strtotime ( "+$tMonths month", strtotime($assessment['create_date'])) );
                                }else{
                                    $conductedDate = date ( "m-Y", strtotime($assessment['school_aqs_pref_end_date']));
                                    $validDate = date ( "m-Y", strtotime ( "+$tMonths month", strtotime($assessment['school_aqs_pref_end_date'])) );
                                }
                                
				/*if($report_id==7 || $report_id==10)
					$reportObject=new individualReport($assessment_id,2,$report_id,$conductedDate,$validDate);
				else if($report_id==4 || $report_id==8)
					$reportObject=new groupReport($group_assessment_id,$report_id,$diagnostic_id,$conductedDate,$validDate,0,$dept_id);
					//include (ROOT . 'application' . DS . 'views' . DS . "report" . DS . 'singleteacher.php');
				//include ROOT . 'library' . DS . 'tcpdf' . DS . 'tcpdf.php';	*/	
                                $reportObject=new individualReport($assessment_id,0);
				$reportObject->actionPlanOutput($action_plan_id,$start_date,$end_date,$palnnedDate,$datesrange,$details,$rating_date);
				
			}else{
				$this->_is404=1;
			}
	}
        
        function studentAction(){
                error_reporting(1);
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
                                if(($assessment['assessment_type_id']==2 || $assessment['assessment_type_id']==4) && ((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) ));
                                //admin - reviewer
                                elseif(($assessment['assessment_type_id']==2 || $assessment['assessment_type_id']==4) && $assessment_id>0 && ($this->user['user_id']==$group_asmt_external||$this->user['user_id']==$group_asmt_internal));
                                //teacher reports - admin
                                elseif(($assessment['assessment_type_id']==2 || $assessment['assessment_type_id']==4) && in_array("view_all_assessments",$this->user['capabilities']));
                                else{
                                 $this->_notPermitted=1;
                                        return;
                                    }
				$this->_render=false;
				$reportObject = null;
				$tMonths=$pMonths+($pYears*12);				
				//$conductedDate=date("m-Y");				
				//$validDate=date("m-Y",strtotime("+$tMonths month"));
                                if($assessment['school_aqs_pref_end_date']=="" || $assessment['school_aqs_pref_end_date']=="0000-00-00"){
                                if($report_id==8){
                                $conductedDate = date ( "d-M-Y", strtotime($assessment['create_date']));    
                                }else{
                                $conductedDate = date ( "M-Y", strtotime($assessment['create_date']));
                                }
				$validDate = date ( "M-Y", strtotime ( "+$tMonths month", strtotime($assessment['create_date'])) );
                                }else{
                                $conductedDate = date ( "M-Y", strtotime($assessment['school_aqs_pref_end_date']));
				$validDate = date ( "M-Y", strtotime ( "+$tMonths month", strtotime($assessment['school_aqs_pref_end_date'])) );
                                }
                                
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
        
        function studentCentreAction(){
                //error_reporting(1);
		$report_id=empty($_GET['report_id'])?0:$_GET['report_id'];
                
		$centre_id=empty($_GET['centre_id'])?0:$_GET['centre_id'];
                
                $cid=explode(",",$centre_id);
                
                $centre_id=$cid;    
                //die();
                
		$diagnosticModel=new diagnosticModel();
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
			//else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || (in_array("take_external_assessment",$this->user['capabilities']) && $report_id==1) || $report_id==5)){
			if(count($centre_id)==0){
                            $this->_is404=1;
                        }else if($centre_id>0){
				$this->_render=false;
				$reportObject = null;
                                $diagnosticModel=new diagnosticModel();
                                $diagnostic_id=$diagnosticModel->getDiagnosticCentre($centre_id);
                                $diagnostic_id=$diagnostic_id['diagnostic_id'];
                                $group_assessment_id=0;
                                $conductedDate="";
                                $validDate="";
                                $reportObject=new groupReport($group_assessment_id,$report_id,$diagnostic_id,$conductedDate,$validDate,$centre_id);
				$reportObject->generateOutput();
                        }else{
                            $this->_is404=1;
                        }
				
			
	}
        
        function studentOrgAction(){
                //error_reporting(1);
		$report_id=empty($_GET['report_id'])?0:$_GET['report_id'];
		$org_id=empty($_GET['org_id'])?0:$_GET['org_id'];
		$diagnosticModel=new diagnosticModel();
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
			//else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || (in_array("take_external_assessment",$this->user['capabilities']) && $report_id==1) || $report_id==5)){
			if($org_id==0){
                            $this->_is404=1;
                        }else if($org_id>0){
				$this->_render=false;
				$reportObject = null;
                                $diagnosticModel=new diagnosticModel();
                                $diagnostic_id=$diagnosticModel->getDiagnosticOrg($org_id);
                                $diagnostic_id=$diagnostic_id['diagnostic_id'];
                                $group_assessment_id=0;
                                $conductedDate="";
                                $validDate="";
                                $reportObject=new groupReport($group_assessment_id,$report_id,$diagnostic_id,$conductedDate,$validDate,$org_id);
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
                $dept_id = empty($_GET['dept_id'])?0:$_GET['dept_id'];
		$diagnosticModel=new diagnosticModel();
		$lang_id=DEFAULT_LANGUAGE;
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
				$diagnostic = $diagnosticModel->getDiagnosticBYLang($diagnostic_id,$lang_id);					
				//get relevant questions of diagnostic				
				$kpas = $diagnostic['kpa_recommendations']==1?$diagnosticModel->getKpasForDiagnosticLang($diagnostic_id,$lang_id):array();
				$kqs = $diagnostic['kq_recommendations']==1?$diagnosticModel->getKeyQuestionsForDiagnosticLang($diagnostic_id,$lang_id):array();
				$cqs = $diagnostic['cq_recommendations']==1?$diagnosticModel->getCoreQuestionsForDiagnosticLang($diagnostic_id,$lang_id):array();
				$js = $diagnostic['js_recommendations']==1?$diagnosticModel->getJudgementalStatementsForDiagnosticLang($diagnostic_id,$lang_id):array();
				$this->set('kpas',$kpas);
				$this->set('kqs',$kqs);
				$this->set('cqs',$cqs);
				$this->set('js',$js);			
				$this->set('DiagnosticName',$diagnostic['name']);
				$this->set('client_name',$assessment['client_name']);			
				$this->set('reportObj',$reportModel);				
				$this->set('group_assessment_id',$group_assessment_id);
				$this->set('diagnostic_id',$diagnostic_id);
                                $this->set('dept_id',$dept_id);
			}else{
				$this->_is404=1;
			}
	}

}