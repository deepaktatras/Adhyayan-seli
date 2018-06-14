<?php

class assessmentController extends controller{

	function createSchoolAssessmentAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review		

			$this->_notPermitted=1;					

		else if(in_array("create_assessment",$this->user['capabilities'])){

			$clientModel=new clientModel();

			$this->set("clients",$clientModel->getClients(array("max_rows"=>-1)));

			$disabled=!in_array('assign_external_review_team',$this->user['capabilities'])?'disabled':0;
                        $this->set('disabled',$disabled);

			$diagnosticModel=new diagnosticModel();

			$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>1,"isPublished"=>"yes")));			

			$assessmentModel=new assessmentModel();

			$this->set('externalReviewRoles',$assessmentModel->getReviewerSubRoles(4));

			$this->set("tiers",$assessmentModel->getTiers());

			$this->set("awardSchemes",$assessmentModel->getAwardSchemes());
			
			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

		}else

			$this->_notPermitted=1;

	}

	function chooseReviewTypeAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

			else if(in_array("create_self_review",$this->user['capabilities'])){

				$assessmentModel=new assessmentModel();

				$this->set('reviewTypes',$assessmentModel->getSubReviewsType(1));//for school				

			}else

				$this->_notPermitted=1;

	}

	function createSchoolSelfAssessmentAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

			else if(in_array("create_self_review",$this->user['capabilities'])){

				$clientModel=new clientModel();

				$clientId = $this->user['client_id'];

				$this->set("client",$clientModel->getClientById($clientId));
				
				$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

				$assessmentModel = new assessmentModel();

				/*$ReviewTypeProductsAvailed = $assessmentModel->getReviewTypeProductsAvailed(1,$clientId);

				

				foreach($ReviewTypeProductsAvailed as $availedProduct)

				{

					//print_r($availedProduct);die;

					if($availedProduct['active']<=0 || $availedProduct['isPmtApproved']<=0)

					{

						//$this->_notPermitted=1;

						$this->set("paynow",1);

						return;

					}

				}*/

				

				$this->set("internalAssessors",$assessmentModel->getInternalAssessorsforSchoolSelfAssmt($clientId));

					

				$diagnosticModel=new diagnosticModel();

				$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>1,"isPublished"=>"yes")));

				$this->set("clientReviews",$assessmentModel->getClientReviews($clientId));

				$this->set("tiers",$assessmentModel->getTiers());

				$this->set("awardSchemes",$assessmentModel->getAwardSchemes());
				
				$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

			}else

				$this->_notPermitted=1;

	}

	function editSchoolSelfAssessmentAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;		   

			else if(in_array("create_self_review",$this->user['capabilities'])||in_array("view_all_assessments",$this->user['capabilities'])){

				$clientModel=new clientModel();
                                       
				$assessment_id = empty($_GET['said'])?0:$_GET['said'];
				
				$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

				$assessmentModel = new assessmentModel();

				$assessment = $assessmentModel->getSchoolAssessment($assessment_id);

				$this->set("assessment",$assessment);

				

				$clientId = $assessment['client_id'];

				

				$this->set("client",$clientModel->getClientById($clientId));

				

				$this->set("internalAssessors",$assessmentModel->getEditInternalAssessorsforSchoolSelfAssmt($clientId,$assessment['user_ids']));

				$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

				$diagnosticModel=new diagnosticModel();

				$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>1,"isPublished"=>"yes")));

				$this->set("clientReviews",$assessmentModel->getClientReviews($clientId));

				$this->set("tiers",$assessmentModel->getTiers());

				$this->set("awardSchemes",$assessmentModel->getAwardSchemes());

			}else

				$this->_notPermitted=1;

	}

	function paymentAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

			else if(in_array("create_self_review",$this->user['capabilities'])){

				$assessmentModel = new assessmentModel();

				$this->set('products',$assessmentModel->getProducts(1));

				$this->set('paymentModes',$assessmentModel->getPaymentModes());

				

			}else

				$this->_notPermitted=1;

	}	

	function editSchoolAssessmentAction(){		

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review		

			$this->_notPermitted=1;		

		else if(in_array("create_assessment",$this->user['capabilities'])){

			$assessment_id = empty($_GET['said'])?0:$_GET['said'];

			$clientModel=new clientModel();

			$this->set("clients",$clientModel->getClients(array("max_rows"=>-1)));

			$this->set("assessment_id",$assessment_id);

			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

			$diagnosticModel=new diagnosticModel();
                        $disabled= !in_array('assign_external_review_team',$this->user['capabilities'])?'disabled':0;
                        $this->set('disabled',$disabled);     
			

			$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>1,"isPublished"=>"yes")));

			

			$assessmentModel=new assessmentModel();

			$this->set('externalReviewRoles',$assessmentModel->getReviewerSubRoles(4));

			$this->set("tiers",$assessmentModel->getTiers());

			$this->set("awardSchemes",$assessmentModel->getAwardSchemes());

			$assessment = $assessmentModel->getSchoolAssessment($assessment_id,1);

			$this->set("assessment",$assessment);

			$externalAssessors = $assessmentModel->getExternalAssessors($assessment['external_client']);						

			$this->set("externalAssessors",$externalAssessors);

			$subroles = $assessment['subroles'];

			$subroles = explode(',',$subroles);

			$externalAssessorsTeam = array();

			foreach($subroles as $role=>$row)

			{

				$exTeamClientId = explode('_',$row);

				$exTeamClientId = $exTeamClientId[0];

				array_push($externalAssessorsTeam,$assessmentModel->getExternalAssessors($exTeamClientId));

			}

			$this->set("externalAssessorsTeam",$externalAssessorsTeam);			

			$assessors = explode(',',$assessment['user_ids']);

			$this->set("hideDiagnostics",$assessmentModel->getDiagnosticsForInternalAssessor($assessment['client_id'],$assessors[0]));



		}else

			$this->_notPermitted=1;

	}

	

	function editTeacherAssessmentAction(){		

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review		

			$this->_notPermitted=1;		

		else

		if(in_array("create_assessment",$this->user['capabilities'])){

			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
			
			$diagnosticModel = new diagnosticModel();

			$assessmentModel=new assessmentModel();

			if(empty($_GET['gaid']) || !($teacherAssessment=$diagnosticModel->getGroupAssessmentByGAId($_GET['gaid'])) ){

				$this->_is404=1;

				return;

			}	

			$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>2,"isPublished"=>"yes")));

			$this->set("teacherCategories",$assessmentModel->getTeacherCategoryList());	

			$assessment = $assessmentModel->getTeacherAssessment($_GET['gaid'],'2');				

			$this->set("assessment",$assessment);

			$this->set("schoolAdmins",$assessmentModel->getSchoolAdmins($assessment['client_id']));

			$this->set("eassessors",$assessmentModel->getExternalAssessorsInGroupAssessment($_GET['gaid']));

			$this->set("tdiagnostics",$assessmentModel->getDiagnosticTypeForTeacherType($_GET['gaid']));

			$this->set("gaid",($_GET['gaid']));

			

			$isAdmin=in_array("create_assessment",$this->user['capabilities']);

			$isSchoolAdmin=($this->user['client_id']==$teacherAssessment['client_id'] && ($teacherAssessment['admin_user_id']==$this->user['user_id'] || in_array(6,$this->user['role_ids']))) || ($this->user['network_id']==$teacherAssessment['network_id'] && in_array("view_own_network_assessment",$this->user['capabilities']))?1:0;

			if($isAdmin || $isSchoolAdmin){

			$this->set("teacherAssessment",$teacherAssessment);

			$this->set("isAdmin",$isAdmin);

			$this->set("isSchoolAdmin",$isSchoolAdmin);

			$assessmentModel=new assessmentModel();

			$this->set("assessors",$assessmentModel->getExternalAssessorsInGroupAssessment($_GET['gaid']));

			$this->_template->addHeaderStyle('bootstrap-select.min.css');

			$this->_template->addHeaderScript('bootstrap-select.min.js');

			$this->_template->addHeaderScript('plupload/plupload.full.min.js');

		}

			

		}else

			$this->_notPermitted=1;

	}

	

	function createTeacherAssessmentAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review		

			$this->_notPermitted=1;		

		else

		if(in_array("create_assessment",$this->user['capabilities'])){

			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
			
			$diagnosticModel=new diagnosticModel();

			$assessmentModel=new assessmentModel();

			$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>2,"isPublished"=>"yes")));

			$this->set("teacherCategories",$assessmentModel->getTeacherCategoryList());

		}else

			$this->_notPermitted=1;

	}

	

	/*function selectExternalAssessorAction(){

		$networkModel=new networkModel();

		$this->set("networks",$networkModel->getNetworkList());

	}*/

	

	function assessmentAction(){

		$cPage=empty($_POST['page'])?1:$_POST['page'];

		$order_by=empty($_POST['order_by'])?"create_date":$_POST['order_by'];

		$order_type=empty($_POST['order_type'])?"desc":$_POST['order_type'];
                
                // get aqs team review listing for tab admin on 02-08-2016 by Mohit Kumar
                $ref=!empty($_REQUEST['ref'])?$_REQUEST['ref']:0;
                $ref_key="REVIEW".md5(time());
                if(isset($_REQUEST['ref']) && $_REQUEST['ref']==1 && current($this->user['role_ids'])==8){

                    $alertIds = $this->db->getAlertContentIds('d_assessment','CREATE_REVIEW');
                    $alertIds = !empty($alertIds)?$alertIds['content_id']:array();

                    if(!empty($alertIds)){
                        $checkAlertRelation = $this->db->getAlertRelationIds(current($this->user['role_ids']),'REVIEW');
                        if(!empty($checkAlertRelation)){
                            $this->db->update('h_alert_relation',array('alert_ids'=>trim($alertIds)),
                                    array('login_user_role'=>current($this->user['role_ids']),'type'=>'REVIEW','id'=>$checkAlertRelation['id']));
                        } else {
                            $this->db->insert('h_alert_relation',array('alert_ids'=>trim($alertIds),'ref_key'=>$ref_key,'flag'=>1,
                                'login_user_role'=>current($this->user['role_ids']),'type'=>'REVIEW'));
                        }
                    }
                } else if(empty ($_REQUEST['ref']) && current($this->user['role_ids'])==8) {
                    $this->db->delete('h_alert_relation',array('type'=>'REVIEW','login_user_role'=>current($this->user['role_ids'])));
                }
                if($ref==1 && $ref_key!=''){
                    $this->db->update('d_alerts',array('status'=>1,'ref_key'=>$ref_key),array('type'=>'CREATE_REVIEW','table_name'=>'d_assessment'));
                }
		$param=array(

				"client_name_like"=>empty($_POST['client_name'])?"":$_POST['client_name'],
				"name_like"=>empty($_POST['name'])?"":$_POST['name'],

				"status"=>empty($_POST['status'])?"":$_POST['status'],

				"network_id"=>empty($_POST['network_id'])?"":$_POST['network_id'],
                                "province_id"=>empty($_POST['province_id'])?"":$_POST['province_id'],
				"assessment_type_id"=>empty($_POST['assessment_type_id'])?"":$_POST['assessment_type_id'],

				"user_id"=>"",
                                "sub_role_user_id"=>"",

				"page"=>$cPage,

				"order_by"=>$order_by,

				"order_type"=>$order_type,

			);

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review		

			$this->_notPermitted=1;					

		else if(!empty($_REQUEST['myAssessment'])){ 

			$param['user_id']=$this->user['user_id'];

			$param['client_id']=0;

			$param['network_id']=0;

		}else if(in_array("view_all_assessments",$this->user['capabilities'])){
			
			

		}else if(in_array("view_own_network_assessment",$this->user['capabilities'])){

			$param['user_id']=$this->user['user_id'];

			$param['network_id']=$this->user['network_id'];

		}else if(in_array("view_own_institute_assessment",$this->user['capabilities']) ){

			$param['user_id']=$this->user['user_id'];

			$param['client_id']=$this->user['client_id'];

			$param['network_id']=0;
		
                }
                else {

			$param['user_id']=$this->user['user_id'];

			$param['client_id']=0;

			$param['network_id']=0;

		}
		
                if(in_array("take_external_assessment",$this->user['capabilities']) )
			$param['sub_role_user_id']=$this->user['user_id'];                        
		
		//print_r($param);

		$this->set("filterParam",$param);

		$assessmentModel=new assessmentModel();
                // make condition for tap admin on 18-05-2016 by Mohit Kumar
                if(in_array('8',$this->user['role_ids']) && count($this->user['role_ids'])==1){
                    $tap_admin_id = $this->user['role_ids'][0];
                } else {
                    $tap_admin_id='';
                }
                if(isset($_REQUEST['uid']) && $_REQUEST['uid']!='' && isset($_REQUEST['rid']) && $_REQUEST['rid']!=''){
                    $user_id = $_REQUEST['uid'];
                    $rid = $_REQUEST['rid'];
                } else {
                    $user_id='';
                    $rid='';
                }
                $this->set("assessmentList",$assessmentModel->getAssessmentList($param,$tap_admin_id,$user_id,$rid,current($this->user['role_ids']),$ref,$ref_key));
		
		
                
                
                $this->set("pages",$assessmentModel->getPageCount());

		$this->set("cPage",$cPage);

		$this->set("orderBy",$order_by);

		$this->set("orderType",$order_type);

		$networkModel=new networkModel();

		$this->set("networks",$networkModel->getNetworkList(array("max_rows"=>-1)));
                $this->set("provinces",empty($_POST['network_id'])?array():$networkModel->getProvinces($_POST['network_id']));
		
		$this->set("isFilter",empty($_GET['filter'])?0:$_GET['filter']);

		$this->_template->addHeaderStyle('assessment-form.css');

		$this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');

		$this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');

	}

	function reportListAction(){
	
		$diagnosticModel=new diagnosticModel();
	
		$assessmentModel=new assessmentModel();
	
		$assessment_id=empty($_GET['assessment_id'])?0:$_GET['assessment_id'];
	
		$group_assessment_id=empty($_GET['group_assessment_id'])?0:$_GET['group_assessment_id'];                
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review	
			$this->_notPermitted=1;                	                                       
	
//			else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || in_array("take_external_assessment",$this->user['capabilities']) ||  $group_assessment_id>0 )){
//	
//				$this->_notPermitted=1;
//	
//			}else                             
                            if($assessment_id>0 && $assessment=$diagnosticModel->getAssessmentById($assessment_id)){
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
                            elseif($isSchoolReview==0 && ((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids']))));
                            //admin - reviewer
                            elseif($assessment['assessment_type_id']==2 && $assessment_id>0 && ($this->user['user_id']==$group_asmt_external||$this->user['user_id']==$group_asmt_internal));
                            //teacher reports - admin
                            elseif($isSchoolReview==0 && in_array("view_all_assessments",$this->user['capabilities']));
                            else{
                                 $this->_notPermitted=1;
                                        return;
                            }
				//get number of kpas for review
				//print_r($assessment);
				$res = $diagnosticModel->getNumberOfKpasDiagnostic($assessment['diagnostic_id']);
				//print_r($res);
				$this->set('numKpas',$res['num']);				
				$this->set("assessment",$assessment);
	
				$this->set("reports",$diagnosticModel->getReportsByAssessmentId($assessment_id,false));
	
			}else if($group_assessment_id>0 && $reports=$diagnosticModel->getSubAssReportsByGroupAssessmentId($group_assessment_id)){
				$reportsType = $diagnosticModel->getReportsType(2);//teacher
				$assessment=$diagnosticModel->getTeacherAssessmentReports($group_assessment_id);
	
				$grs=array();
	
				foreach($assessment['diagnostic_ids'] as $dId=>$cat_name){
	
					$temp=$assessment;
	
					$temp['diagnostic_id']=$dId;
	
					$temp['report_name'].=' - '.$cat_name;
	
					$temp['teacher_category']=$cat_name;
	
					$grs[]=$temp;
	
				}
				//print_r($reports);
								
				$reportsIndividual = array_filter($reports,function($var){
					if($var['report_id']==5)
						return array($var['assessment_id']=>$var['user_names'][0]);
				});
				//print_r($reportsIndividual);
				$reportsSingleTeacher = array_filter($reports,function($var){
					if($var['report_id']==7)
						return $var;
				});
				//print_r($reportsIndividual);
				 //asort($reportsIndividual,)
				$reports=array_merge($grs,$reports);
				$this->set('diagnosticsForGroup',$grs[0]['diagnostic_ids']);				
				$this->set('reportsIndividual',$reportsIndividual);
				$this->set('reportsSingleTeacher',$reportsSingleTeacher);
				if(in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids']))
	
					foreach($reports as $rep){
	
						if(!empty($rep['isPublished']) && $rep['isPublished']==1){
	
							$this->_notPermitted=1;
	
							return;
	
						}
	
				};
	
				$this->set("reports",$reports);
				$this->set("reportsType",$reportsType);
	
				$this->set("assessment",$assessment);
				$this->set("groupAssessmentId",$group_assessment_id);
	
			}else
	
				$this->_is404=1;
	
				$this->set("assessment_id",$assessment_id);
	
				$this->set("group_assessment_id",$group_assessment_id);
	
	}

	/* function reportListAction(){

		$diagnosticModel=new diagnosticModel();

		$assessmentModel=new assessmentModel();

		$assessment_id=empty($_GET['assessment_id'])?0:$_GET['assessment_id'];

		$group_assessment_id=empty($_GET['group_assessment_id'])?0:$_GET['group_assessment_id'];		

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review		

			$this->_notPermitted=1;		

		else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || in_array("take_external_assessment",$this->user['capabilities']) ||  $group_assessment_id>0 )){

			$this->_notPermitted=1;

		}else if($assessment_id>0 && $assessment=$diagnosticModel->getAssessmentById($assessment_id)){

			//get number of kpas for review
			//print_r($assessment);
			$res = $diagnosticModel->getNumberOfKpasDiagnostic($assessment['diagnostic_id']);	
			//print_r($res);
			$this->set('numKpas',$res['num']);			
			if(!empty($assessment['statusByRole'][4]) && $assessment['report_published']==1 && in_array("take_external_assessment",$this->user['capabilities']))

			{

				$this->_notPermitted=1;

				return;

			}

				

			$this->set("assessment",$assessment);

			$this->set("reports",$diagnosticModel->getReportsByAssessmentId($assessment_id,false));

		}else if($group_assessment_id>0 && $reports=$diagnosticModel->getSubAssReportsByGroupAssessmentId($group_assessment_id)){			

			$assessment=$diagnosticModel->getTeacherAssessmentReports($group_assessment_id);			

			$grs=array();

			foreach($assessment['diagnostic_ids'] as $dId=>$cat_name){

				$temp=$assessment;

				$temp['diagnostic_id']=$dId;

				$temp['report_name'].=' - '.$cat_name;

				$temp['teacher_category']=$cat_name;

				$grs[]=$temp;

			}

			$reports=array_merge($grs,$reports);
			print_r($reports);
			//print_r($grs[0]['diagnostic_ids']);
			$this->set($diagnosticsForGroup,$grs[0]['diagnostic_ids']); 
			if(in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids']))

			foreach($reports as $rep){

				if(!empty($rep['isPublished']) && $rep['isPublished']==1){

					$this->_notPermitted=1;

					return;

				}	

			};

			$this->set("reports",$reports);

			$this->set("assessment",$assessment);

		}else

			$this->_is404=1;

		$this->set("assessment_id",$assessment_id);

		$this->set("group_assessment_id",$group_assessment_id);

	} */

	

	function createTeacherAssessorAction(){

		

		$diagnosticModel=new diagnosticModel();

		$teacherAssessment=null;

		if(empty($_GET['taid']) || !($teacherAssessment=$diagnosticModel->getGroupAssessmentByGAId($_GET['taid'])) ){

			$this->_is404=1;

			return;

		}

		$isAdmin=in_array("create_assessment",$this->user['capabilities']);

		$isSchoolAdmin=($this->user['client_id']==$teacherAssessment['client_id'] && ($teacherAssessment['admin_user_id']==$this->user['user_id'] || in_array(6,$this->user['role_ids']))) || ($this->user['network_id']==$teacherAssessment['network_id'] && in_array("view_own_network_assessment",$this->user['capabilities']))?1:0;

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

		else if($isAdmin || $isSchoolAdmin){

			$this->set("teacherAssessment",$teacherAssessment);

			$this->set("isAdmin",$isAdmin);

			$this->set("isSchoolAdmin",$isSchoolAdmin);

			$assessmentModel=new assessmentModel();

			$this->set("assessors",$assessmentModel->getExternalAssessorsInGroupAssessment($_GET['taid']));

			$this->_template->addHeaderStyle('bootstrap-select.min.css');

			$this->_template->addHeaderScript('bootstrap-select.min.js');

			$this->_template->addHeaderScript('plupload/plupload.full.min.js');

		}else{

			$this->_notPermitted=1;

		}

	}

	

	function assessorListUploadAction(){

		$diagnosticModel=new diagnosticModel();

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

		elseif(!empty($_GET['taid']) && $teacherAssessment=$diagnosticModel->getGroupAssessmentByGAId($_GET['taid']) ){

			$isTeacherType=isset($_GET['type']) && $_GET['type']=='teacher'?1:0;

			$this->set("isTeacherType",$isTeacherType);

			$this->set("type",$isTeacherType?'Teacher':'Assessor');

			$this->set("teacherAssessment",$teacherAssessment);

		}else

			$this->_is404=1;

	}

	

	function addTeacherToTeacherAssessmentAction(){

		$diagnosticModel=new diagnosticModel();

		$teacherAssessment=null;

		if(empty($_GET['taid']) || !($teacherAssessment=$diagnosticModel->getGroupAssessmentByGAId($_GET['taid'])) ){

			$this->_is404=1;

			return;

		}

		

		$isSchoolAdmin=($this->user['client_id']==$teacherAssessment['client_id'] && ($teacherAssessment['admin_user_id']==$this->user['user_id'] || in_array(6,$this->user['role_ids']))) || ($this->user['network_id']==$teacherAssessment['network_id'] && in_array("view_own_network_assessment",$this->user['capabilities']))?1:0;

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

		elseif($isSchoolAdmin){

			$this->set("teacherAssessment",$teacherAssessment);

			$this->set("isSchoolAdmin",$isSchoolAdmin);

			$assessmentModel=new assessmentModel();

			$this->set("assessmentModel",$assessmentModel);

			$this->set("assessors",$assessmentModel->getExternalAssessorsInGroupAssessment($_GET['taid']));

			$this->set("teachers",$assessmentModel->getTeachersInTeacherAssessment($_GET['taid']));

			$this->_template->addHeaderStyle('bootstrap-select.min.css');

			$this->_template->addHeaderScript('bootstrap-select.min.js');

			$this->_template->addHeaderScript('plupload/plupload.full.min.js');

		}else{

			$this->_notPermitted=1;

		}

	}

}