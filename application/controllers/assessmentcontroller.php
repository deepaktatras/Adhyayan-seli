<?php

class assessmentController extends controller{

	function createSchoolAssessmentAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review		

			$this->_notPermitted=1;					

		else if(in_array("create_assessment",$this->user['capabilities'])){

			$clientModel=new clientModel();
                        $languageCode = array('hi','en');
			$this->set("clients",$clientModel->getClients(array("client_institution_id"=>1,"max_rows"=>-1)));

			$disabled=!in_array('assign_external_review_team',$this->user['capabilities'])?'disabled':0;
                        $this->set('disabled',$disabled);

			$diagnosticModel=new diagnosticModel();
                        //echo "<pre>";print_r($diagnosticModel->getDiagnostics(array("assessment_type_id"=>1,"isPublished"=>"yes")));

			$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>1,"isPublished"=>"yes"),'all',1));			

			$assessmentModel=new assessmentModel();

			$this->set('externalReviewRoles',$assessmentModel->getReviewerSubRoles(4));
                        //print_r($assessmentModel->getNotifications());
			$this->set('notifications',array());

			$this->set("tiers",$assessmentModel->getTiers());

			$this->set("awardSchemes",$assessmentModel->getAwardSchemes());
                        $this->set("aqsRounds",$assessmentModel->getRounds());
                        //$this->set("languages", $this->userModel->getTranslationLanguale($languageCode));
			
			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
                        $this->_template->addHeaderStyle('bootstrap-multiselect.css');    
                        $this->_template->addHeaderScript('bootstrap-multiselect.js'); 

		}else

			$this->_notPermitted=1;

	}
        
        function createCollegeAssessmentAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review		

			$this->_notPermitted=1;					

		else if(in_array("create_assessment",$this->user['capabilities'])){

			$clientModel=new clientModel();
                        
                        $this->set("clientsCollege",$clientModel->getClients(array("client_institution_id"=>2,"max_rows"=>-1)));
			
                        //$this->set("clients",$clientModel->getClients(array("max_rows"=>-1)));
                        $this->set("clients",$clientModel->getClients(array("client_institution_id"=>2,"max_rows"=>-1,'school_ids'=>array("'Adhyayan'","'Independent Consultant'"))));


			$disabled=!in_array('assign_external_review_team',$this->user['capabilities'])?'disabled':0;
                        $this->set('disabled',$disabled);

			$diagnosticModel=new diagnosticModel();

			$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>5,"isPublished"=>"yes"),'all',1));			

			$assessmentModel=new assessmentModel();

			$this->set('externalReviewRoles',$assessmentModel->getReviewerSubRoles(4));

			//$this->set("tiers",$assessmentModel->getTiers());

			//$this->set("awardSchemes",$assessmentModel->getAwardSchemes());
                        $this->set("aqsRounds",$assessmentModel->getRounds());
			
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
        
        
         /*
         * function to get users roles by schools 
         */
         function schoolAssessmentData($assessment_id) {
            
                $assessment_id = isset($assessment_id)?$assessment_id:$_POST['assessment_id'];
                //print_r($this->user ['capabilities']);
		if (! in_array ( "create_assessment", $this->user ['capabilities'] ))
			$this->_notPermitted=1;
                
			if(!empty($assessment_id) ) {
				//$this->apiResult ["message"] = "School id/User Role cannot be empty.\n";
				//else {
					$resourceModel = new resourceModel ();
				       $assessment_id = $assessment_id;
				        
			//$assessment_id = empty($_GET['said'])?0:$_GET['said'];

			$clientModel=new clientModel();
                        
                        
			$this->set("clients",$clientModel->getClients(array("client_institution_id"=>1,"max_rows"=>-1)));

			$this->set("assessment_id",$assessment_id);
			$this->set("review_type",1);
			$diagnosticModel=new diagnosticModel();
                        
			$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>1,"isPublished"=>"yes"),'all',1));

			

			$assessmentModel=new assessmentModel();
                        $kaps = $assessmentModel->getAssessmentKpa($assessment_id);
                        $assessmentKpas = array();
                        foreach($kaps as $data){
//                            /print_r($data);
                            $assessmentKpas[$data['user_id']][] = $data['kpa_id'];
                            
                        }
                        //$kaps =array_column($kaps, 'kap_id','user_id');
                        //echo "<pre>"; print_r($assessmentKpas);
			$this->set('assignedKpas',$assessmentKpas);
			$this->set('externalReviewRoles',$assessmentModel->getReviewerSubRoles(4));
                        $this->set('assessmentUsers',$assessmentModel->getAssessmentUsers($assessment_id,1));
                    
			$assessment = $assessmentModel->getSchoolAssessment($assessment_id,1);
			if(!empty($assessment['diagnostic_id'])) {
                            $assessmentKpas = $assessmentModel->getSchoolAssessmentKpas($assessment_id,9);
                            $this->set("assessmentKpas",$assessmentKpas);
                        //print_r($assessmentKpas);
                        }
                      //  echo "<pre>"; print_r($assessment['diagnostic_id']);die;

			//$this->set("assessment",$assessment);

			//$externalAssessors = $assessmentModel->getExternalAssessors($assessment['external_client']);						

			//$this->set("externalAssessors",$externalAssessors);

			//$subroles = $assessment['subroles'];

			//$subroles = explode(',',$subroles);

			//$externalAssessorsTeam = array();

			//foreach($subroles as $role=>$row)

			//{

				//$exTeamClientId = explode('_',$row);

				//$exTeamClientId = $exTeamClientId[0];

				//array_push($externalAssessorsTeam,$assessmentModel->getExternalAssessors($exTeamClientId));

			//}
			//$this->set("externalAssessorsTeam",$externalAssessorsTeam);			
			
					
				}
                                
	}
        function schoolAssessmentDataAction() {
            
                $assessment_id = isset($assessment_id)?$assessment_id:$_POST['assessment_id'];
                $editStatus = isset($_POST['editStatus'])?$_POST['editStatus']:0;
                //print_r($this->user ['capabilities']);
		if (! (in_array ( "create_assessment", $this->user ['capabilities']) || $editStatus ))
			$this->_notPermitted=1;
                
			if(!empty($assessment_id) ) {
				//$this->apiResult ["message"] = "School id/User Role cannot be empty.\n";
				//else {
					$resourceModel = new resourceModel ();
				       $assessment_id = $assessment_id;
				        
			//$assessment_id = empty($_GET['said'])?0:$_GET['said'];

			$clientModel=new clientModel();
                        
                        
			$this->set("clients",$clientModel->getClients(array("client_institution_id"=>1,"max_rows"=>-1)));

			$this->set("assessment_id",$assessment_id);
			$this->set("review_type",1);
			$diagnosticModel=new diagnosticModel();
                        
			$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>1,"isPublished"=>"yes"),'all',1));

			

			$assessmentModel=new assessmentModel();
                        $kaps = $assessmentModel->getAssessmentKpa($assessment_id);
                        $assessmentKpas = array();
                        foreach($kaps as $data){
//                            /print_r($data);
                            $assessmentKpas[$data['user_id']][] = $data['kpa_id'];
                            
                        }
                        //$kaps =array_column($kaps, 'kap_id','user_id');
                        //echo "<pre>"; print_r($assessmentKpas);
			$this->set('assignedKpas',$assessmentKpas);
			$this->set('externalReviewRoles',$assessmentModel->getReviewerSubRoles(4));
                        $this->set('assessmentUsers',$assessmentModel->getAssessmentUsers($assessment_id,1));
                    
			$assessment = $assessmentModel->getSchoolAssessment($assessment_id,1);
			if(!empty($assessment['diagnostic_id'])) {
                            $assessmentKpas = $assessmentModel->getSchoolAssessmentKpas($assessment_id,9);
                            $this->set("assessmentKpas",$assessmentKpas);
                        //print_r($assessmentKpas);
                        }
                      //  echo "<pre>"; print_r($assessment['diagnostic_id']);die;

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
			
					
				}
                                
	}

	function createSchoolSelfAssessmentAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

			else if(in_array("create_self_review",$this->user['capabilities'])){

				$clientModel=new clientModel();

				$clientId = $this->user['client_id'];

				$this->set("client",$clientModel->getClientById($clientId));
                                
                                $this->set("clientsList",$clientModel->getClients(array("max_rows"=>-1)));
				
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

				$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>1,"isPublished"=>"yes"),'all',1));
                                $firstdefaultdiagnostic=$diagnosticModel->getFirstDefaultDiagnostic();
                                $dfd=$firstdefaultdiagnostic['diagnostic_id'];
                                $this->set("firstdefaultdiagnostic",$firstdefaultdiagnostic);
                                
                                $this->set("fdl",$diagnosticModel->getDiagnosticLanguages($dfd));
                                
                                $this->set("freediagnostic",$diagnosticModel->getFreeDiagnostic());
                                
                                $this->set("guestdiagnostic",$diagnosticModel->getGuestDiagnostic());
                                
                                $clientReviews=$assessmentModel->getClientReviews($clientId);
				$this->set("clientReviews",$clientReviews);

				$this->set("tiers",$assessmentModel->getTiers());

				$this->set("awardSchemes",$assessmentModel->getAwardSchemes());
                                
                                $selfReviewsPast = 0;
                                $validatedReviews = 0; // all the published reviews except online reviews are validated 
                                $lastReviewSettings = array();
                                //echo"<pre>";
                                //print_r($clientReviews);
                                //echo"</pre>";
                                $aqs_status=array();
                                $review_status=array();
                                $previous_status=0;
                                if(!empty($clientReviews))
                                        foreach($clientReviews as $review)
                                        {
                                                $review['sub_assessment_type']==1 ? $selfReviewsPast++ : '';
                                                if($review['isPublished']==1 && $review['sub_assessment_type']!=1) 
                                                {
                                                        $validatedReviews++ ;
                                                        $lastReviewSettings = $review;
                                                }
                                                
                                                if($review['sub_assessment_type']==1 && $review['is_approved']!=2){
                                                if($review['filledStatus']!=1){
                                                    $aqs_status[]=$review['assessment_id'];
                                                }
                                                if($review['AQS_status']!=1){
                                                    $review_status[]=$review['assessment_id'];
                                                }
                                                }

                                        } 
                                $previous_status=(count($aqs_status)>0 || count($review_status)>0)?1:0;
                                $this->set("previous_status",$previous_status);
				$this->set("selfReviewsPast",$selfReviewsPast);
                                $this->set("validatedReviews",$validatedReviews);
                                $this->set("lastReviewSettings",$lastReviewSettings);
                                $last_review_diagnostic=isset($lastReviewSettings['diagnostic_id'])?$lastReviewSettings['diagnostic_id']:0;
                                $lastandfreediagnostic=$diagnosticModel->getFreeAndLastDiagnostic($last_review_diagnostic);
                                $this->set("lastandfreediagnostic",$lastandfreediagnostic);
                                $admin_role=0;
                                if(in_array(1,$this->user['role_ids'])||in_array(2,$this->user['role_ids'])){
                                $admin_role=1;    
                                }
                                $this->set("admin_role",$admin_role);
                                $this->set("message_guest",MESSAGE_GUEST);
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
				$diagnosticModel = new diagnosticModel;

				$assessment = $assessmentModel->getSchoolAssessment($assessment_id);
                                //echo '<pre>';print_r($diagnosticModel->getDiagnosticLanguages($assessment['diagnostic_id']));
                                
                                $this->set("languages", $diagnosticModel->getDiagnosticLanguages($assessment['diagnostic_id']));

				$this->set("assessment",$assessment);
                                
				

				$clientId = $assessment['client_id'];
                           
				

				$this->set("client",$clientModel->getClientById($clientId));

				$this->set("clientsList",$clientModel->getClients(array("max_rows"=>-1)));

				$this->set("internalAssessors",$assessmentModel->getEditInternalAssessorsforSchoolSelfAssmt($clientId,$assessment['user_ids']));

				$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

				$diagnosticModel=new diagnosticModel();

				$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>1,"isPublished"=>"yes")));
                                $this->set("firstdefaultdiagnostic",$diagnosticModel->getFirstDefaultDiagnosticDrop($assessment['diagnostic_id']));
                                
                                $this->set("freediagnostic",$diagnosticModel->getFreeDiagnostic($assessment['diagnostic_id']));
                                $clientReviews=$assessmentModel->getClientReviews($clientId);
				$this->set("clientReviews",$clientReviews);

				$this->set("tiers",$assessmentModel->getTiers());
                                $this->set("guestdiagnostic",$diagnosticModel->getGuestDiagnostic());
                                $this->set("message_guest",MESSAGE_GUEST);
                                
                                $selfReviewsPast = 0;
                                $validatedReviews = 0; // all the published reviews except online reviews are validated 
                                $lastReviewSettings = array();
                                if(!empty($clientReviews))
                                        foreach($clientReviews as $review)
                                        {
                                                $review['sub_assessment_type']==1 ? $selfReviewsPast++ : '';
		                                $review['isPublished']==1 && $review['sub_assessment_type']!=1 ?$validatedReviews++ && $lastReviewSettings = $review:'';


                                        } 
                                        
				$this->set("selfReviewsPast",$selfReviewsPast);
                                $this->set("validatedReviews",$validatedReviews);
                                $this->set("lastReviewSettings",$lastReviewSettings);
                                $last_review_diagnostic=isset($lastReviewSettings['diagnostic_id'])?$lastReviewSettings['diagnostic_id']:0;
                                $lastandfreediagnostic=$diagnosticModel->getFreeAndLastDiagnostic($last_review_diagnostic,$assessment['diagnostic_id']);
                                $this->set("lastandfreediagnostic",$lastandfreediagnostic);
                                
				$this->set("awardSchemes",$assessmentModel->getAwardSchemes());
                                
                                $admin_role=0;
                                if(in_array(1,$this->user['role_ids'])||in_array(2,$this->user['role_ids'])){
                                $admin_role=1;    
                                }
                                $this->set("admin_role",$admin_role);

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

                $editStatus = 0;
                $isCollebrative  = isset($_REQUEST['iscollebrative'])?$_REQUEST['iscollebrative']:0;
                $assessmentType  = isset($_REQUEST['assessment_type'])?$_REQUEST['assessment_type']:0;
                $isLead  = isset($_REQUEST['isLead'])?$_REQUEST['isLead']:0;
                if($isCollebrative == 1 && $isLead == 1) {
                   $editStatus = 1;
                }
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review		

			$this->_notPermitted=1;		

		else if((in_array("create_assessment",$this->user['capabilities']) || $editStatus == 1)){

			$assessment_id = empty($_GET['said'])?0:$_GET['said'];
                        $review_type = empty($_REQUEST['assessment_type'])?0:$_REQUEST['assessment_type'];
                        $isNewReview = empty($_REQUEST['new'])?0:$_REQUEST['new'];

			$clientModel=new clientModel();

			$this->set("clients",$clientModel->getClients(array("client_institution_id"=>1,"max_rows"=>-1)));

			$this->set("assessment_id",$assessment_id);
			$this->set("review_type",$review_type);
			$this->set("isNewReview",$isNewReview);

			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

			$diagnosticModel=new diagnosticModel();
                        $disabled= !in_array('assign_external_review_team',$this->user['capabilities'])?'disabled':0;
                        $this->set('disabled',$disabled);     
			

			$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>1,"isPublished"=>"yes"),'all',1));

			

			$assessmentModel=new assessmentModel();

			$this->set('externalReviewRoles',$assessmentModel->getReviewerSubRoles(4));
                        $this->set('assessmentStatus' , $assessmentModel->getAssessmentRatingStatus($assessment_id));
                        if($assessmentType){
                            $assessmentModel->getAssessmentRatingPercentage($assessment_id);
                            $assessmentModel->getAssessmentRatingKpa($assessment_id);
                            $this->set('assessmentRating' , $assessmentModel->getAssessmentRatingPercentage($assessment_id));
                            $this->set('assessmentRatingKpa' , $assessmentModel->getAssessmentRatingKpa($assessment_id));
                        }
                       // print_r( $assessmentModel->getAssessmentRatingPercentage($assessment_id));

			$this->set("tiers",$assessmentModel->getTiers());
                        
			$this->set("awardSchemes",$assessmentModel->getAwardSchemes());
                        //echo "<pre>";print_r($assessmentModel->getReviewNotifications());die;
                        $this->set('allNotifications',$assessmentModel->getReviewNotifications());
                        $this->set('editStatus',$editStatus);
                      
                        $notificationUsers = $assessmentModel->getReviewNotificationUsers($assessment_id);
                        //echo "<pre>";print_r($assessmentModel->getReviewNotifications());
                        $assessmentNotifications = array();
                        $assessmentReminders = array();
                        if(!empty($notificationUsers)) {
                            foreach($notificationUsers  as $users) {
                               if(array_key_exists($users['user_id'],$assessmentNotifications) && $users['type'] == 1 ) {
                                   //$notifications[$users['user_id']][] = $users['notification_id'];
                                   array_push($assessmentNotifications[$users['user_id']],$users['notification_id']);                                    
                               }else  if(array_key_exists($users['user_id'],$assessmentReminders) && $users['type'] == 2 ) {
                                   
                                   array_push($assessmentReminders[$users['user_id']],$users['notification_id']); 
                               } else if(!empty($users['notification_id'])  && $users['type'] == 1)
                                   $assessmentNotifications[$users['user_id']][] = $users['notification_id'];  
                                 else if(!empty($users['notification_id'])  && $users['type'] == 2)
                                   $assessmentReminders[$users['user_id']][] = $users['notification_id']; 
                                 else if($users['type'] == 2)
                                     $assessmentReminders['assessment_id'] = $users['assessment_id'];
                                 else if($users['type'] == 1)
                                     $assessmentNotifications['assessment_id'] = $users['assessment_id'];
                            }
                        }
                        //echo "<pre>";print_r($notificationUsers);
                        $reimSheetUsers = $assessmentModel->getReviewReimSheetUsers($assessment_id);
                      
                        //$assessmentNotifications = array();
                        if(!empty($reimSheetUsers)) {
                            $reimSheetUsers = array_column($reimSheetUsers,'sheet_status','user_id');
                             
                        }
                       // echo "<pre>";print_r($assessmentReminders);
                        // $this->set('reviewNotifications',$assessmentModel->getReviewNotifications('review_notification',$assessment_id));
                        $this->set('reviewNotifications',$assessmentNotifications);
                        $this->set('reviewReminders',$assessmentReminders);
                        $this->set('reimSheetUsers',$reimSheetUsers);
                        $this->set('assessmentUsers',$assessmentModel->getAssessmentUsers($assessment_id,1));
                        
                       // echo"<pre>";print_r($assessmentModel->getAssessmentUsers($assessment_id,1));
                        //$this->set('notifications',$assessmentModel->getNotifications());
                       // $this->set('notificationUsers',$assessmentModel->getNotificationUsers($assessment_id));
                        
			$assessment = $assessmentModel->getSchoolAssessment($assessment_id,1);
			$facilitators = $assessmentModel->getFacilitatorsDetails($assessment_id,1);
                        if(!empty($facilitators)) {
                            $facilitatorsData = array();
                            $facilitatorTeam = array();
                            foreach($facilitators as $data) {
                                 //array_push($facilitatorTeam,$assessmentModel->getFacilitators($data['client_id']));
                                $facilitatorTeam[$data['user_id']] = $assessmentModel->getFacilitators($data['client_id']);
                                $facilitatorsData[$data['user_id']] = $data;
                                
                            }
                            //echo "<pre>";print_r($facilitatorTeam);
                            //$facilitators = array_column($facilitators, 'client_id');
                            $this->set("facilitators",$facilitatorsData);
                            $this->set("facilitatorTeam",$facilitatorTeam);
                        }
                        //echo "<pre>"; print_r($assessment);

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
                       // echo"<pre>";print_r($assessmentModel->getReviewNotificationUsers($assessment_id));
			$this->set("hideDiagnostics",$assessmentModel->getDiagnosticsForInternalAssessor($assessment['client_id'],$assessors[0]));
                        $this->set("aqsRounds",$assessmentModel->getRounds());
                        $languageCode = array('hi','en');
                        $this->set("languages", $diagnosticModel->getDiagnosticLanguages($assessment['diagnostic_id']));
                        if(isset($_REQUEST['tab2']) && $_REQUEST['tab2'] == 1) {
                            
                            $this->schoolAssessmentData($assessment['assessment_id']);
                            $this->set('step2',$_REQUEST['tab2']);
                        }

		}else

			$this->_notPermitted=1;
                
                $this->_template->addHeaderStyle('bootstrap-multiselect.css');    
                $this->_template->addHeaderScript('bootstrap-multiselect.js'); 
                $this->_template->addHeaderScript('collaborative.js'); 

	}

	function editCollegeAssessmentAction(){		

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review		

			$this->_notPermitted=1;		

		else if(in_array("create_assessment",$this->user['capabilities'])){

			$assessment_id = empty($_GET['said'])?0:$_GET['said'];

			$clientModel=new clientModel();

			$this->set("clients",$clientModel->getClients(array("client_institution_id"=>2,"max_rows"=>-1,'school_ids'=>array("'Adhyayan'","'Independent Consultant'"))));

			$this->set("assessment_id",$assessment_id);

			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

			$diagnosticModel=new diagnosticModel();
                        $disabled= !in_array('assign_external_review_team',$this->user['capabilities'])?'disabled':0;
                        $this->set('disabled',$disabled);     
			

			$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>5,"isPublished"=>"yes"),'',1));

			

			$assessmentModel=new assessmentModel();

			$this->set('externalReviewRoles',$assessmentModel->getReviewerSubRoles(4));

			//$this->set("tiers",$assessmentModel->getTiers());

			//$this->set("awardSchemes",$assessmentModel->getAwardSchemes());

			$assessment = $assessmentModel->getCollegeAssessment($assessment_id,1);
			$facilitators = $assessmentModel->getFacilitatorsDetails($assessment_id,1);
                        if(!empty($facilitators)) {
                            $facilitatorsData = array();
                            $facilitatorTeam = array();
                            foreach($facilitators as $data) {
                                 //array_push($facilitatorTeam,$assessmentModel->getFacilitators($data['client_id']));
                                $facilitatorTeam[$data['user_id']] = $assessmentModel->getFacilitators($data['client_id']);
                                $facilitatorsData[$data['user_id']] = $data;
                                
                            }
                            //echo "<pre>";print_r($facilitatorTeam);
                            //$facilitators = array_column($facilitators, 'client_id');
                            $this->set("facilitators",$facilitatorsData);
                            $this->set("facilitatorTeam",$facilitatorTeam);
                        }
                        //echo "<pre>"; print_r($facilitatorTeam);

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
                        $this->set("aqsRounds",$assessmentModel->getRounds());


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
                        $tab2=(isset($_GET['tab2']) && $_GET['tab2']==1)?$_GET['tab2']:''; 
                        $this->set("tab2",$tab2);
			//$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>2,"isPublished"=>"yes")));

			//$this->set("teacherCategories",$assessmentModel->getTeacherCategoryList());	
                        $tdiagnostics=$assessmentModel->getDiagnosticTypeForTeacherType($_GET['gaid']);
                        $diagnostic_selected_array=array();
                        foreach($tdiagnostics as $t){
                        $diagnostic_selected_array[$t['teacher_category_id']]=$t['diagnostic_id'];
                        }
                        
                        $diagnostic_array=array();
                        $kid=0;
                        $diagnostics_data=$assessmentModel->getAllUsedDiagnostics($_GET['gaid'],0);
                        $used_diagnostics=explode(",",$diagnostics_data['all_diagnostic']);
                        
                        $reviewers_data=$assessmentModel->getAllUsedDiagnostics($_GET['gaid'],2);
                        $used_reviewers=explode(",",$reviewers_data['all_validators']);
                        //print_r($used_diagnostics);
                        //print_r($used_reviewers);
                        foreach($assessmentModel->getTeacherCategoryList() as $teacherCategory){
                          
                          //print_r($a);
                          $diagnostic_array[$kid]['teacher_category_id']=$teacherCategory['teacher_category_id'];
                          $diagnostic_array[$kid]['teacher_category']=$teacherCategory['teacher_category'];
                          $diagnostic_array[$kid]['teacher_disable']=(isset($diagnostic_selected_array[$teacherCategory['teacher_category_id']]) && in_array($diagnostic_selected_array[$teacherCategory['teacher_category_id']],$used_diagnostics))?1:0;       
                          $diagnostic_array[$kid]['category_diagnostic']=$diagnosticModel->getTeacherDiagnostic($teacherCategory['teacher_category_id']);
                        $kid++;
                          
                        }
                        $this->set("category_diagnostics",$diagnostic_array);
                        
			$assessment = $assessmentModel->getTeacherAssessment($_GET['gaid'],'2');				

			$this->set("assessment",$assessment);

			$this->set("schoolAdmins",$assessmentModel->getSchoolAdmins($assessment['client_id']));

			$this->set("eassessors",$assessmentModel->getExternalAssessorsInGroupAssessment($_GET['gaid']));

			$this->set("tdiagnostics",$tdiagnostics);
                        $this->set("used_reviewers",$used_reviewers);

			$this->set("gaid",($_GET['gaid']));

			

			$isAdmin=in_array("create_assessment",$this->user['capabilities']);

			$isSchoolAdmin=($this->user['client_id']==$teacherAssessment['client_id'] && ($teacherAssessment['admin_user_id']==$this->user['user_id'] || in_array(6,$this->user['role_ids']))) || ($this->user['network_id']==$teacherAssessment['network_id'] && in_array("view_own_network_assessment",$this->user['capabilities']))?1:0;

			if($isAdmin || $isSchoolAdmin){

			$this->set("teacherAssessment",$teacherAssessment);

			$this->set("isAdmin",$isAdmin);

			$this->set("isSchoolAdmin",$isSchoolAdmin);

			$assessmentModel=new assessmentModel();

			$this->set("assessors",$assessmentModel->getExternalAssessorsInGroupAssessment($_GET['gaid']));
                        
                        $this->set("assessmentModel",$assessmentModel);
                        $this->set("teachers",$assessmentModel->getTeachersInTeacherAssessment($_GET['gaid']));
                        $this->set("aqsRounds",$assessmentModel->getRounds());

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

			//$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>2,"isPublished"=>"yes")));

			//$this->set("teacherCategories",$assessmentModel->getTeacherCategoryList());
                        $diagnostic_array=array();
                        $kid=0;
                        foreach($assessmentModel->getTeacherCategoryList() as $teacherCategory){
                          
                          //print_r($a);
                          $diagnostic_array[$kid]['teacher_category_id']=$teacherCategory['teacher_category_id'];
                          $diagnostic_array[$kid]['teacher_category']=$teacherCategory['teacher_category'];
                          $diagnostic_array[$kid]['teacher_disable']=1;
                          $diagnostic_array[$kid]['category_diagnostic']=$diagnosticModel->getTeacherDiagnostic($teacherCategory['teacher_category_id']);
                        $kid++;
                          
                        }
                        $this->set("category_diagnostics",$diagnostic_array);
                        $this->set("aqsRounds",$assessmentModel->getRounds());

		}else

			$this->_notPermitted=1;

	}

	
       function createStudentAssessmentAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review		

			$this->_notPermitted=1;		

		else

		if(in_array("create_assessment",$this->user['capabilities'])){

			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
			
			$diagnosticModel=new diagnosticModel();

			$assessmentModel=new assessmentModel();

			//$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>2,"isPublished"=>"yes")));

			//$this->set("teacherCategories",$assessmentModel->getTeacherCategoryList());
                        $diagnostic_array=array();
                        $kid=0;
                        foreach($assessmentModel->getStudentCategoryList() as $teacherCategory){
                          
                          //print_r($a);
                          $diagnostic_array[$kid]['teacher_category_id']=$teacherCategory['teacher_category_id'];
                          $diagnostic_array[$kid]['teacher_category']=$teacherCategory['teacher_category'];
                          $diagnostic_array[$kid]['teacher_disable']=1;
                          $diagnostic_array[$kid]['category_diagnostic']=$diagnosticModel->getTeacherDiagnostic($teacherCategory['teacher_category_id']);
                          $kid++;
                          
                        }
                        //echo "<pre>";print_r($diagnostic_array);
                        $this->set("category_diagnostics",$diagnostic_array);
                        $StudentReviewType=$assessmentModel->getStudentReviewType();
                        $this->set("StudentReviewType",$StudentReviewType);
                        //$this->set("aqsRounds",$assessmentModel->getStudentRounds($teacherAssessment['client_id'],$teacherAssessment['student_round']));

                        //$StudentBatchType=$assessmentModel->getStudentBatchType();
                        //$this->set("StudentBatchType",$StudentBatchType);

		}else

			$this->_notPermitted=1;

	}
        
                        function createStudentProfileFormAction(){
                                $assessment_id = empty($_GET['assessment_id']) ? 0 : $_GET['assessment_id']; 
                                $this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
                                $diagnosticModel=new diagnosticModel();
                                $clientModel = new clientModel();
                                $assessmentModel=new assessmentModel();
                                $student_data=$assessmentModel->getInternalAssessor($assessment_id);
                                //print_r($student_data);
                                $student_id=$student_data['user_id'];
                                
                                if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review  
                                $this->_notPermitted=1;  
                               elseif ($assessment_id > 0 && ($groupAssmt = $diagnosticModel->getGroupAssessmentByAssmntId($assessment_id)) && ($studentAssessmentFormAttributes = $assessmentModel->getStudentFormAttributes($student_id,$assessment_id))){
                                $isNetworkAdmin = in_array("view_own_network_assessment", $this->user['capabilities']) && $groupAssmt['network_id'] == $this->user['network_id'] && $this->user['network_id'] > 0 ? 1 : 0;
                                $isSchoolAdmin = in_array("view_own_institute_assessment", $this->user['capabilities']) && $groupAssmt['client_id'] == $this->user['client_id'] ? 1 : 0;


                                if(in_array($this->user['user_id'], $groupAssmt['user_ids']) || in_array("view_all_assessments", $this->user['capabilities']) || $isSchoolAdmin || $isNetworkAdmin){
                                        
                                        //$student_id = 125;
                                        $student_name=$student_data['name'];
                                        $batch_code=$student_data['client_name'];
                                        //echo($student_id);
                                        $userType="";
                                        if($student_id==$this->user['user_id']){
                                        $userType="self";    
                                        }else{
                                        $userType="other";    
                                        }
                                        $this->set("userType",$userType);
                                        //echo "<pre>";print_r($studentAssessmentFormAttributes);
                                        $student_array=array();
                                        foreach($studentAssessmentFormAttributes as $key=>$val){
                                        $student_array[$val['field_name']]=$val['value'];    
                                        }
                                        //$states = $clientModel->getStateList ( 101, $stateId );
                                         $this->set("form_attributes",$studentAssessmentFormAttributes);
                                         $this->set("states",$clientModel->getStateList ( 101 ));
                                         $this->set("can_name",$student_name);
                                         $this->set("student_array",$student_array);
                                         $this->set("batch_code",$batch_code);
                                         $isReadOnly = $groupAssmt['report_published'] != 1 && ( ($student_array['is_submit'] == 1 && in_array("edit_all_submitted_assessments", $this->user['capabilities'])) || ($student_array['is_submit'] != 1 && ( $groupAssmt['user_ids'][0] == $this->user['user_id'] )
                        )) ? 0 : 1;
                                        $this->set("assessment_id", $assessment_id);
                                        $this->set("isReadOnly", $isReadOnly);
                                        $this->set("countryCodeList",$clientModel->getCountryWithCode());
                                         
                                         $cities = array();
                                         
                                         if(isset($studentAssessmentFormAttributes[2]['value']) && $studentAssessmentFormAttributes[2]['value'] >= 1) {
                                               // $state_id = $client_data['state_id'];
                                                $cities = $clientModel->getCityList($studentAssessmentFormAttributes[2]['value']);
                                          }
                                            $this->set("cities",$cities);

                                        }else{

                                         $this->_notPermitted=1;
                                        }
                               } else {
                                 $this->_is404 = 1;
                                }

                 }
	/*function selectExternalAssessorAction(){

		$networkModel=new networkModel();

		$this->set("networks",$networkModel->getNetworkList());
                                
	}*/

	function editStudentAssessmentAction(){		

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
                        $tab2=(isset($_GET['tab2']) && $_GET['tab2']==1)?$_GET['tab2']:''; 
                        $this->set("tab2",$tab2);
			//$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("assessment_type_id"=>2,"isPublished"=>"yes")));

			//$this->set("teacherCategories",$assessmentModel->getTeacherCategoryList());	
                        $tdiagnostics=$assessmentModel->getDiagnosticTypeForTeacherType($_GET['gaid']);
                        $diagnostic_selected_array=array();
                        foreach($tdiagnostics as $t){
                        $diagnostic_selected_array[$t['teacher_category_id']]=$t['diagnostic_id'];
                        }
                        
                        $diagnostic_array=array();
                        $kid=0;
                        $diagnostics_data=$assessmentModel->getAllUsedDiagnostics($_GET['gaid'],0);
                        $used_diagnostics=explode(",",$diagnostics_data['all_diagnostic']);
                        
                        $reviewers_data=$assessmentModel->getAllUsedDiagnostics($_GET['gaid'],2);
                        $used_reviewers=explode(",",$reviewers_data['all_validators']);
                        //print_r($used_diagnostics);
                        //print_r($used_reviewers);
                        foreach($assessmentModel->getStudentCategoryList() as $teacherCategory){
                          
                          //print_r($a);
                          $diagnostic_array[$kid]['teacher_category_id']=$teacherCategory['teacher_category_id'];
                          $diagnostic_array[$kid]['teacher_category']=$teacherCategory['teacher_category'];
                          $diagnostic_array[$kid]['teacher_disable']=(isset($diagnostic_selected_array[$teacherCategory['teacher_category_id']]) && in_array($diagnostic_selected_array[$teacherCategory['teacher_category_id']],$used_diagnostics))?1:0;       
                          $diagnostic_array[$kid]['category_diagnostic']=$diagnosticModel->getTeacherDiagnostic($teacherCategory['teacher_category_id']);
                        $kid++;
                          
                        }
                        $this->set("category_diagnostics",$diagnostic_array);
                        
			$assessment = $assessmentModel->getStudentAssessment($_GET['gaid'],'4');				

			$this->set("assessment",$assessment);
                        
                        $StudentReviewType=$assessmentModel->getStudentReviewType();
                        $this->set("StudentReviewType",$StudentReviewType);
                        //$StudentBatchType=$assessmentModel->getStudentBatchType();
                        //$this->set("StudentBatchType",$StudentBatchType);

			$this->set("schoolAdmins",$assessmentModel->getSchoolAdmins($assessment['client_id']));

			$this->set("eassessors",$assessmentModel->getExternalAssessorsInGroupAssessment($_GET['gaid']));

			$this->set("tdiagnostics",$tdiagnostics);
                        $this->set("used_reviewers",$used_reviewers);

			$this->set("gaid",($_GET['gaid']));

			

			$isAdmin=in_array("create_assessment",$this->user['capabilities']);

			$isSchoolAdmin=($this->user['client_id']==$teacherAssessment['client_id'] && ($teacherAssessment['admin_user_id']==$this->user['user_id'] || in_array(6,$this->user['role_ids']))) || ($this->user['network_id']==$teacherAssessment['network_id'] && in_array("view_own_network_assessment",$this->user['capabilities']))?1:0;

			if($isAdmin || $isSchoolAdmin){

			$this->set("teacherAssessment",$teacherAssessment);

			$this->set("isAdmin",$isAdmin);

			$this->set("isSchoolAdmin",$isSchoolAdmin);

			$assessmentModel=new assessmentModel();

			$this->set("assessors",$assessmentModel->getExternalAssessorsInGroupAssessment($_GET['gaid']));
                        
                        $this->set("assessmentModel",$assessmentModel);
                        $this->set("teachers",$assessmentModel->getTeachersInTeacherAssessment($_GET['gaid']));
                        
                        $roundsUnusedi= $assessmentModel->getStudentRounds ( $teacherAssessment['client_id'],$teacherAssessment['student_round'] );
                        $roundsUnusedf=array();
                        foreach($roundsUnusedi as $key=>$val){
                         $roundsUnusedf[]= $val['aqs_round'];  
                        }
                        
                        $this->set("aqsRoundsUnused",$roundsUnusedf);
			
                        $this->set("aqsRounds",$assessmentModel->getStudentRoundsAll());
			
                        $this->_template->addHeaderStyle('bootstrap-select.min.css');

			$this->_template->addHeaderScript('bootstrap-select.min.js');

			$this->_template->addHeaderScript('plupload/plupload.full.min.js');

		}

			

		}else

			$this->_notPermitted=1;

	}

	function assessmentAction(){

		$cPage=empty($_POST['page'])?1:$_POST['page'];

		$order_by=empty($_POST['order_by'])?"create_date":$_POST['order_by'];

		$order_type=empty($_POST['order_type'])?"desc":$_POST['order_type'];
                //print_r($this->user);
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
                
                if(isset($_REQUEST['aid']) && $_REQUEST['aid']>0){
                $_POST['assessment_type_id']= $_REQUEST['aid'];   
                }
		$param=array(

				"client_name_like"=>empty($_POST['client_name'])?"":$_POST['client_name'],
				"name_like"=>empty($_POST['name'])?"":$_POST['name'],
                                
                                "diagnostic_id"=>empty($_POST['diagnostic_id'])?"":$_POST['diagnostic_id'], 
                                "fdate_like"=>empty($_POST['fdate'])?"":ChangeFormat($_POST['fdate'],"Y-m-d"), 
                                "edate_like"=>empty($_POST['edate'])?"":ChangeFormat($_POST['edate'],"Y-m-d"),  
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
                $is_guest=(isset($this->user['is_guest']) && $this->user['is_guest'])?$this->user['is_guest']:0;
                //$assessmentList = $assessmentModel->getAssessmentList($param,$tap_admin_id,$user_id,$rid,current($this->user['role_ids']),$ref,$ref_key);
                //echo "<pre>";print_r($assessmentModel->getAssessmentList($param,$tap_admin_id,$user_id,$rid,current($this->user['role_ids']),$ref,$ref_key,$this->user['user_id']));die;
                $this->set("assessmentList",$assessmentModel->getAssessmentList($param,$tap_admin_id,$user_id,$rid,current($this->user['role_ids']),$ref,$ref_key,$is_guest,$this->user['user_id']));
                //$languageCode = array('hi','en');
                $languageCode =explode(",",DIAGNOSTIC_LANG);
                $this->set("pages",$assessmentModel->getPageCount());

		$this->set("cPage",$cPage);

		$this->set("orderBy",$order_by);

		$this->set("orderType",$order_type);

		$networkModel=new networkModel();
                $diagnosticModel=new diagnosticModel();
                $this->set("isLead",$diagnosticModel->checkIsLead($this->user['user_id']));
		$this->set("networks",$networkModel->getNetworkList(array("max_rows"=>-1)));
                $this->set("provinces",empty($_POST['network_id'])?array():$networkModel->getProvinces($_POST['network_id']));
		$this->set("diagnostics",$diagnosticModel->getDiagnostics(array("isPublished"=>"yes"),0,1));
		$this->set("isFilter",empty($_GET['filter'])?0:$_GET['filter']);
                 $this->set("diagnosticsLanguage", $this->userModel->getTranslationLanguale($languageCode));

		$this->_template->addHeaderStyle('assessment-form.css');
		//$this->_template->addHeaderStyle('bootstrap-datetimepicker.min.css');

		$this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');

		$this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
		$this->_template->addHeaderScript('localize.js');
		//$this->_template->addHeaderScript('bootstrap-datetimepicker.min.js');

	}

	function reportListAction(){
	
		$diagnosticModel=new diagnosticModel();
	
		$assessmentModel=new assessmentModel();
	
		$assessment_id=empty($_GET['assessment_id'])?0:$_GET['assessment_id'];
		$diagnostic_id=empty($_GET['diagnostic_id'])?0:$_GET['diagnostic_id'];
                //print_r($diagnosticModel->getDiagnosticLanguages($diagnostic_id));
                $this->set("diagnosticsLanguage", $diagnosticModel->getDiagnosticLanguages($diagnostic_id));
                
	
		$group_assessment_id=empty($_GET['group_assessment_id'])?0:$_GET['group_assessment_id'];
                $external_download_teacher=isset($_GET['external_download_teacher'])?$_GET['external_download_teacher']:0;                
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review	
			$this->_notPermitted=1; 
                        $prefferedLanguage = $diagnosticModel->getAssessmentPrefferedLanguage($assessment_id);
                        $lang_id = isset($prefferedLanguage['language_id'])?$prefferedLanguage['language_id']:DEFAULT_LANGUAGE;
                            
	
//			else if(!(in_array("view_all_assessments",$this->user['capabilities']) || in_array("create_self_review",$this->user['capabilities']) || in_array("take_external_assessment",$this->user['capabilities']) ||  $group_assessment_id>0 )){
//	
//				$this->_notPermitted=1;
//	
//			}else                             
                            if($assessment_id>0 && $assessment=$diagnosticModel->getAssessmentById($assessment_id,$lang_id)){
                               $isNetworkAdmin = ($assessment['network_id']==$this->user['network_id'] && in_array(7,$this->user['role_ids']))==1?1:0; 
                               $isSchoolReview = ($assessment['assessment_type_id']==1 || $assessment['assessment_type_id']==5)?1:0;                                
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
                            elseif(($assessment['assessment_type_id']==2 || $assessment['assessment_type_id']==4) && $assessment_id>0 && ($this->user['user_id']==$group_asmt_external||$this->user['user_id']==$group_asmt_internal));
                            elseif($isNetworkAdmin && $isSchoolReview==0 && (in_array("generate_unsubmitted_asmt_reports",$this->user['capabilities']) || in_array("generate_submitted_asmt_reports",$this->user['capabilities'])));
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
                                $numRows=$diagnosticModel->getRound2AssessmentIdsCount($assessment['client_id'],$assessment['diagnostic_id']);//print_r($numRows);
                                $this->set("numRowsCount",$numRows);
                                
				$this->set('numKpas',$res['num']);				
				$this->set("assessment",$assessment);
	
				$this->set("reports",$diagnosticModel->getReportsByAssessmentId($assessment_id,false));
                                
	
			}else if($group_assessment_id>0 && $reports=$diagnosticModel->getSubAssReportsByGroupAssessmentId($group_assessment_id,$external_download_teacher)){
				
                                if(isset($_GET['greporttype']) && $_GET['greporttype']==4){
                                 $reportsType = $diagnosticModel->getReportsType(4);//student    
                                }else{
                                $reportsType = $diagnosticModel->getReportsType(2);//teacher
                                }
				$assessment=$diagnosticModel->getTeacherAssessmentReports($group_assessment_id);
	
				$grs=array();
	
				foreach($assessment['diagnostic_ids'] as $dId=>$cat_name){
	
					$temp=$assessment;
	
					$temp['diagnostic_id']=$dId;
	
					$temp['report_name'].=' - '.$cat_name;
	
					$temp['teacher_category']=$cat_name;
	
					$grs[]=$temp;
	
				}
                                
                                $school_cat=isset($assessment['school_level_ids'])?$assessment['school_level_ids']:array();
                                
				//print_r($assessment['school_level_ids']);
								
				$reportsIndividual = array_filter($reports,function($var){
					if($var['report_id']==5 || $var['report_id']==9)
						return array($var['assessment_id']=>$var['user_names'][0]);
				});
				//print_r($reportsIndividual);
				$reportsSingleTeacher = array_filter($reports,function($var){
					if($var['report_id']==7 || $var['report_id']==10)
						return $var;
				});
				//print_r($reportsIndividual);
				 //asort($reportsIndividual,)
				$reports=array_merge($grs,$reports);
                                $grp_diagnostics=isset($grs[0]['diagnostic_ids'])?$grs[0]['diagnostic_ids']:array();
				$this->set('diagnosticsForGroup',$grp_diagnostics);
                                
				$this->set('reportsIndividual',$reportsIndividual);
				$this->set('reportsSingleTeacher',$reportsSingleTeacher);
                                $this->set('school_cat',$school_cat);
                                //print_r($school_cat);
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
                        $reviewers_data=$assessmentModel->getAllUsedDiagnostics($_GET['taid'],2);
                        $used_reviewers=explode(",",$reviewers_data['all_validators']);
                        $this->set("used_reviewers",$used_reviewers);
			$this->set("assessors",$assessmentModel->getExternalAssessorsInGroupAssessment($_GET['taid']));
                        
                        $this->set("assessmentModel",$assessmentModel);
                        $this->set("teachers",$assessmentModel->getTeachersInTeacherAssessment($_GET['taid']));

			$this->_template->addHeaderStyle('bootstrap-select.min.css');

			$this->_template->addHeaderScript('bootstrap-select.min.js');

			$this->_template->addHeaderScript('plupload/plupload.full.min.js');

		}else{

			$this->_notPermitted=1;

		}

	}

	function createStudentAssessorAction(){

		

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
                        $reviewers_data=$assessmentModel->getAllUsedDiagnostics($_GET['taid'],2);
                        $used_reviewers=explode(",",$reviewers_data['all_validators']);
                        $this->set("used_reviewers",$used_reviewers);
			$this->set("assessors",$assessmentModel->getExternalAssessorsInGroupAssessment($_GET['taid']));
                        
                        $this->set("assessmentModel",$assessmentModel);
                        $this->set("teachers",$assessmentModel->getTeachersInTeacherAssessment($_GET['taid']));

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
                        $isStudentType=isset($_GET['type']) && $_GET['type']=='student'?1:0;
			$this->set("isTeacherType",$isTeacherType);
                        if($isStudentType){
                         $this->set("type",$isStudentType?'Student':'Assessor');   
                        }else{
			$this->set("type",$isTeacherType?'Teacher':'Assessor');
                        }

			$this->set("teacherAssessment",$teacherAssessment);

		}else

			$this->_is404=1;

	}

	

	function addTeacherToTeacherAssessmentAction(){

		/*$diagnosticModel=new diagnosticModel();

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
            */
            $this->_notPermitted=1;
	}
        
        /*
         * function to upload school AQS
         */
        function uploadSchoolAssessmentAction(){

		if((in_array(1,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

			else if(in_array("upload_resources",$this->user['capabilities'])){

				//$assessmentModel=new assessmentModel();

				//$this->set('reviewTypes',$assessmentModel->getSubReviewsType(1));//for school				

			}else

				$this->_notPermitted=1;

	}
        
        function uploadStudentProfileAction(){

		if((in_array(1,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

			else if(in_array("upload_resources",$this->user['capabilities'])){

				//$assessmentModel=new assessmentModel();

				//$this->set('reviewTypes',$assessmentModel->getSubReviewsType(1));//for school
                            $this->set('gaid',$_GET['gaid']);
                            $this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

			}else

				$this->_notPermitted=1;

	}
        
         /*
         * function to update reimbursement sheet status
         */
        function reimSheetConfirmationAction(){
            
                $status = isset($_REQUEST['status'])?$_REQUEST['status']:0;
                $this->set("status",$status);
	}
        
        
}