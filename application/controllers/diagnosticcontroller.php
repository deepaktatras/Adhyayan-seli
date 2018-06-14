<?php

class diagnosticController extends controller {

    function diagnosticAction() {
        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif (in_array("manage_diagnostic", $this->user['capabilities'])) {
            
          $preferredLanguage = isset($_REQUEST['lang_id'])?$_REQUEST['lang_id']:'all';
            
            $order_by = empty($_POST['order_by']) ? "name" : $_POST['order_by'];
            $order_type = empty($_POST['order_type']) ? "asc" : $_POST['order_type'];
            $param = array(
                "name_like" => empty($_POST['dia_name']) ? "" : $_POST['dia_name'],
                "isPublished" => empty($_POST['isPublished']) ? "" : $_POST['isPublished'],
                "assessment_type_id" => empty($_POST['assessment_type_id']) ? "" : $_POST['assessment_type_id'],
                "lang_id" => empty($preferredLanguage) ? "" : $preferredLanguage,
                "order_by" => $order_by,
                "order_type" => $order_type,
            );
            //$languageCode = array('hi','en');
            $languageCode = explode(",",DIAGNOSTIC_LANG);
            $this->set("filterParam", $param);
            $diagnosticModel = new diagnosticModel();
            //echo $this->lang;die;
            $this->set("diagnostics", $diagnosticModel->getDiagnostics($param,$preferredLanguage));
            $this->set("diagnosticsLanguage", $this->userModel->getTranslationLanguale($languageCode));
            $this->set("orderBy", $order_by);
            $this->set("orderType", $order_type);
            $this->set("preferredLanguage", $preferredLanguage);
            $reviewType=$diagnosticModel->getAssessmentTypes();
            $reviewType_array=array();
            foreach($reviewType as $key=>$val){
            if($val['assessment_type_id']==3) continue;   
            $reviewType_array[]=$val;    
            }
            //unset($reviewType[2]);
            //print_r($reviewType_array);
            $this->set("assessment_types", $reviewType_array);
            
        } else
            $this->_notPermitted = 1;
        $this->_template->addHeaderScript('localize.js');
    }

    function diagnosticFormAction() {
        $langId = empty($_GET['langId']) ? 0 : $_GET['langId'];
        $diagnostic_id = empty($_GET['id']) ? 0 : $_GET['id'];
        $diagnosticModel = new diagnosticModel();

        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif ($diagnostic_id > 0 && $diagnostic = $diagnosticModel->getDiagnosticBYLang($diagnostic_id,$langId)) {
            if (in_array("manage_diagnostic", $this->user['capabilities'])) {
                $this->set("kpas", $this->db->array_col_to_key($diagnosticModel->getKpasForDiagnosticLang($diagnostic_id,$langId), "kpa_instance_id"));
                $this->set("kqs", $this->db->array_grouping($diagnosticModel->getKeyQuestionsForDiagnosticLang($diagnostic_id,$langId), "kpa_instance_id", "key_question_instance_id"));
                $this->set("cqs", $this->db->array_grouping($diagnosticModel->getCoreQuestionsForDiagnosticLang($diagnostic_id,$langId), "key_question_instance_id", "core_question_instance_id"));
                $this->set("jss", $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForDiagnosticLang($diagnostic_id,$langId), "core_question_instance_id", "judgement_statement_instance_id"));
                $this->set("diagnostic", $diagnostic);
                $this->set('ddiagnosticId', $diagnostic['diagnostic_id']);
                $this->set('kpaRecommendations', $diagnostic['kpa_recommendations']);
                $this->set('kqRecommendations', $diagnostic['kq_recommendations']);
                $this->set('cqRecommendations', $diagnostic['cq_recommendations']);
                $this->set('jsRecommendations', $diagnostic['js_recommendations']);
                $diagnosticLabels = array();                                               
                $diagnosticLabelsData = $diagnosticModel->getDiagnosticLabels($diagnostic_id,$langId);
                foreach($diagnosticLabelsData as $data) {
                    $diagnosticLabels[$data['label_key']] = $data['label_text'];
                }
                $this->set("diagnosticLabels", $diagnosticLabels);
                $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
                $this->_template->addHeaderScript('bootstrap-multiselect-0.9.13.js');
                $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
                $this->_template->addHeaderStyle('bootstrap-multiselect-0.9.13.css');
                // $this->_template->addHeaderStyleURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css' );
                // $this->_template->addHeaderScriptURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js' );
                $this->_template->addHeaderStyle('assessment-form.css');
                $this->_template->addFooterScript('assessment.js');
            } else
                $this->_notPermitted = 1;
        } else
            $this->_is404 = 1;
    }
    
    //function to view all feedback of a assessment
    function allFeedbackAction() {

        $assessment_id = $_GET['assessment_id'];
        $user_id = $_GET['user_id'];
        $selfFeedbackSubmitStatus = 0;
        $diagnosticModel = new diagnosticModel();
        $assessmentTeam = $diagnosticModel->getSchoolAssessmentAllUser($assessment_id);
        foreach($assessmentTeam as $key=>$data) {
            
            if($data['user_sub_role'] == 8) {
                unset($assessmentTeam[$key]);
            }
        }
        $feedbackAssessmentMembers = array();
        foreach($assessmentTeam as $user) {
               // print_r($asscssorFeedbackAnswer[$user['user_id']]);
                if(!empty($asscssorFeedbackAnswer[$user['user_id']]) && $user['user_sub_role'] !=8) {
                        $feedbackAssessmentMembers []= $user;
                }
            }
        if (!empty($assessmentTeam)) {

            //print_r(array_column($assessmentTeam,'user_id'));
            $asscssorFeedbackQuestion = $diagnosticModel->getAssessorFeedbackQuestions(0, $assessment_id);
            $teamQuestions = array();
            foreach ($assessmentTeam as $teamData) {
                $teamQuestions[$teamData['user_id']] = $this->createOption($asscssorFeedbackQuestion);
            }
            //echo "<pre>"; print_r(array_column($teamQuestions,'user_id'));die;
            $answers = $diagnosticModel->getAssessorFeedbackAnswer(0, $assessment_id);
            $asscssorFeedbackAnswer = array();
            foreach ($assessmentTeam as $teamData) {
                foreach ($answers as $id => $dataset) {

                    if ($teamData['user_id'] == $dataset['user_id']) {
                        $peer_id = !empty($dataset['peer_id']) ? $dataset['peer_id'] : $dataset['user_id'];
                        $asscssorFeedbackAnswer[$teamData['user_id']][$peer_id][$dataset['q_id']] = $dataset;
                    }
                }
            }
           // echo "<pre>"; print_r($asscssorFeedbackAnswer);die;
            $selfFeedbackQuestion = $diagnosticModel->getAllSelfFeedbackQuestion($assessment_id, array_column($assessmentTeam, 'user_id'));
            foreach ($selfFeedbackQuestion as $questionData) {
                $allSelfFeedbackQuestion[$questionData['user_id']][] = $questionData;
            }
            //echo '<pre>';print_r($selfFeedbackQuestion);die;
            $selfFeedbackSubmitStatus = $diagnosticModel->getSelfFeedbackStatus($assessment_id, 0, 2);
            $selfFeedbackStatus = array();
            foreach ($selfFeedbackSubmitStatus as $data) {
                $selfFeedbackStatus[$data['user_id']] = $data;
            }
            $peerFeedbackSubmitStatus = $diagnosticModel->getSelfFeedbackStatus($assessment_id, 0, 1);
            $peerFeedbackStatus = array();
            foreach ($peerFeedbackSubmitStatus as $data) {
                $peerFeedbackStatus[$data['user_id']] = $data;
            }

            $goals = array();
            if(!empty($peerFeedbackStatus)) {
                $goals = $diagnosticModel->getReviewGoals($assessment_id,'', array_keys ($peerFeedbackStatus));
                $goals = array_column($goals, 'goal', 'user_id') ;
            }   

            /* $selfIsSubmit = 0;
              $selfFeedbackStatus = 0;
              print_r($peerFeedbackSubmitStatus);
              if(!empty($selfFeedbackSubmitStatus)) {
              $selfIsSubmit = $selfFeedbackSubmitStatus['is_submit'];
              $selfFeedbackStatus = $selfFeedbackSubmitStatus['feedback_status'];
              }
              $peerIsSubmit = 0;
              $peerFeedbackStatus = 0;
              if(!empty($peerFeedbackSubmitStatus)) {
              $peerIsSubmit = $peerFeedbackSubmitStatus['is_submit'];
              $peerFeedbackStatus = $peerFeedbackSubmitStatus['feedback_status'];
              } */
            // $assessmentTeam = $diagnosticModel->getSchoolAssessmentAllUser($assessment_id,$user_id);
            // echo "<pre>";print_r($asscssorFeedbackAnswer);
            $self_review = 1;
            if (!empty($assessmentTeam) && count($assessmentTeam) > 1) {
                $self_review = 0;
            }
            // echo $self_review;
            //print_r($diagnosticModel->getAssessmentSchool($assessment_id));
            $this->set("assessment_id", $assessment_id);
            $this->set("self_review", $self_review);
            //$this->set("is_submit", $selfIsSubmit);
            // $this->set("is_submit_peer", $peerIsSubmit);
            $this->set("peerFeedbackStatus", $peerFeedbackStatus);
            $this->set("schoolName", $diagnosticModel->getAssessmentSchool($assessment_id));
            $this->set("selfFeedbackStatus", $selfFeedbackStatus);
            $this->set("assessmentTeam", $assessmentTeam);
            $this->set("user_id", $user_id);
            $this->set("selfFeedbackQuestion", $allSelfFeedbackQuestion);
            $this->set("allFeedbackQuestion", $teamQuestions);
            $this->set("peerFeedbackAnswers", $asscssorFeedbackAnswer);
            $this->set("userRoleIds", $this->user['role_ids']);
            $this->set("goals", $goals);
            $this->set("feedbackAssessmentMembers", $feedbackAssessmentMembers);
            //$this->set("diagnostic_id", $assessment['diagnostic_id']);
            // $fields = $diagnosticModel->getCommentsFieldPostReview();
            //$this->set('fields', $fields);
            $assessmentModel = new assessmentModel();

            // $this->set("assessorsFeedbackQuestions", $this->createOption($asscssorFeedbackQuestion));
            // $this->set("asscssmentExternalTeam", $diagnosticModel->getAssessmentExternalTeam($assessment_id, $this->user['user_id']));

            $this->_template->addHeaderStyleURL('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css');
            $this->_template->addHeaderScriptURL('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js');
            $this->_template->addHeaderScript('postreview.js');
            $this->_template->addHeaderStyle('assessment-form.css');
            $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
            $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
        } else {
            $this->_notPermitted = 1;
        }
    }

    function feedbackFormAction() {
       
        $assessment_id = $_GET['assessment_id'];
        $user_id = $_GET['user_id'];
        
        if($user_id != $this->user['user_id'])
            $this->_notPermitted = 1;
        $selfFeedbackSubmitStatus = 0;
        $diagnosticModel = new diagnosticModel();
        $asscssorFeedbackQuestion = $diagnosticModel->getAssessorFeedbackQuestions($user_id,$assessment_id);
        $answers = $diagnosticModel->getAssessorFeedbackAnswer($user_id,$assessment_id);
        $asscssorSelfFeedbackAnswer = array();
        foreach ($answers as $id => $dataset) {
            $asscssorSelfFeedbackAnswer[$dataset['peer_id']][$dataset['q_id']] = $dataset;
        }
        $selfFeedbackQuestion = $diagnosticModel->getSelfFeedbackQuestion($assessment_id,$user_id);
       // echo '<pre>';print_r($answers);
        $selfFeedbackSubmitStatus = $diagnosticModel->getSelfFeedbackStatus($assessment_id,$user_id,2);
        $peerFeedbackSubmitStatus = $diagnosticModel->getSelfFeedbackStatus($assessment_id,$user_id,1);
         //echo '<pre>';print_r($selfFeedbackSubmitStatus);
        // echo '<pre>';print_r($peerFeedbackSubmitStatus);
        $selfIsSubmit = 0;
        $selfFeedbackStatus = 0;
        //print_r($selfFeedbackSubmitStatus);
        if(!empty($selfFeedbackSubmitStatus)) {
            $selfIsSubmit = $selfFeedbackSubmitStatus['is_submit'];
            $selfFeedbackStatus = $selfFeedbackSubmitStatus['feedback_status'];
        }
        $peerIsSubmit = 0;
        $peerFeedbackStatus = 0;
        if(!empty($peerFeedbackSubmitStatus)) {
            $peerIsSubmit = $peerFeedbackSubmitStatus['is_submit'];
            $peerFeedbackStatus = $peerFeedbackSubmitStatus['feedback_status'];
        }
        $assessmentTeam = $diagnosticModel->getSchoolAssessmentAllUser($assessment_id);
        foreach($assessmentTeam as $key=>$data) {
            
            if($data['user_sub_role'] == 8) {
                unset($assessmentTeam[$key]);
            }
        }
        
        if(!empty($assessmentTeam)) {
             $asscssorFeedbackQuestion = $diagnosticModel->getAssessorFeedbackQuestions(0, $assessment_id);
             $peerFeedbackQuestion = $this->createOption($asscssorFeedbackQuestion);
            $teamQuestions = array();
            foreach ($assessmentTeam as $teamData) {
                $teamQuestions[$teamData['user_id']] = $this->createOption($asscssorFeedbackQuestion);
            }
        }
        
           $answers = $diagnosticModel->getAssessorFeedbackAnswer(0, $assessment_id,$user_id,1);
            $asscssorFeedbackAnswer = array();
            $feedbackAssessmentMembers = array();
            foreach ($assessmentTeam as $teamData) {
                foreach ($answers as $id => $dataset) {

                    if ($teamData['user_id'] == $dataset['user_id']) {
                        $peer_id = !empty($dataset['peer_id']) ? $dataset['peer_id'] : $dataset['user_id'];
                        $asscssorFeedbackAnswer[$teamData['user_id']][$dataset['q_id']] = $dataset;
                    }
                }
            }
            foreach($assessmentTeam as $user) {
               // print_r($asscssorFeedbackAnswer[$user['user_id']]);
                if(!empty($asscssorFeedbackAnswer[$user['user_id']]) && $user['user_sub_role'] !=8) {
                        $feedbackAssessmentMembers []= $user;
                }
            }
            
            
       // echo "<pre>";print_r($assessmentTeam);
       // print_r( array_column($assessmentTeam, 'user_id'));
        $accessorFeedbackDisabled = 0;
        if(empty($asscssorFeedbackAnswer)) {
            $accessorFeedbackDisabled = 1;
        }
        $accessorDetails = array();
        $self_review = 1;
        if(!empty($assessmentTeam)) {
            
            foreach($assessmentTeam as $key=>$val) {
               // print_r($asscssorFeedbackAnswer[$user['user_id']]);
                if($val['user_id'] == $user_id) {
                        $accessorDetails ['user_id']= $val['user_id'];
                        $accessorDetails ['name']= $val['name'];
                        $accessorDetails ['sub_role_name']= $val['sub_role_name'];
                        $accessorDetails ['user_sub_role']= $val['user_sub_role'];
                        unset($assessmentTeam[$key]);
                }
            }
            $teamUserArray = array_column($assessmentTeam, 'user_id');
            //echo "<pre>";print_r($accessorDetails);
            if(!empty($teamUserArray) && !in_array($user_id,$teamUserArray)) {
                $self_review = 0;
            }
        }
        $goals = $diagnosticModel->getReviewGoals($assessment_id,$user_id);
        //print_r($goals);
       // echo $self_review;
         //echo "<pre>"; print_r($assessmentTeam);die;
        $this->set("assessment_id", $assessment_id);
        $this->set("goals", $goals);
        $this->set("self_review", $self_review);
        $this->set("is_submit", $selfIsSubmit);
        $this->set("is_submit_peer", $peerIsSubmit);
        $this->set("peerFeedbackStatus", $peerFeedbackStatus);
        $this->set("selfFeedbackStatus", $selfFeedbackStatus);
        $team = $diagnosticModel->getSchoolAssessmentAllUser($assessment_id,$user_id);
        foreach($team as $key=>$data) {
            
            if($data['user_sub_role'] == 8) {
                unset($team[$key]);
            }
        }
        $this->set("assessmentTeam", $team);
        $this->set("user_id", $user_id);
        $this->set("selfFeedbackQuestion", $selfFeedbackQuestion);
        $this->set("peerFeedbackAnswers", $asscssorSelfFeedbackAnswer);
        $this->set("userRoleIds", $this->user['role_ids']);
        $this->set("userEmail", $this->user['email']);
        $this->set("schoolName", $diagnosticModel->getAssessmentSchool($assessment_id));
        
        $this->set("allFeedbackQuestion", $peerFeedbackQuestion);
        $this->set("peerReceivedFeedbackAnswers", $asscssorFeedbackAnswer);
        $this->set("feedbackAssessmentMembers", $feedbackAssessmentMembers);
        $this->set("assessorDetails", $accessorDetails);
        $this->set("accessorFeedbackDisabled", $accessorFeedbackDisabled);
        //$this->set("diagnostic_id", $assessment['diagnostic_id']);
       // $fields = $diagnosticModel->getCommentsFieldPostReview();
        //$this->set('fields', $fields);
        $des = isset($_REQUEST['des'])?$_REQUEST['des']:'';
        $assessmentModel = new assessmentModel();        
        $this->set("assessorsFeedbackQuestions", $this->createOption($asscssorFeedbackQuestion));
        $this->set("destination", $des);
       // $this->set("asscssmentExternalTeam", $diagnosticModel->getAssessmentExternalTeam($assessment_id, $this->user['user_id']));
       
        $this->_template->addHeaderStyleURL('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css');
        $this->_template->addHeaderScriptURL('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js');
        $this->_template->addHeaderScript('postreview.js');
    }

    function assessmentFormAction() {
        
        $lang_id = empty($_GET['lang_id']) ? 0 : $_GET['lang_id'];
    	$assessment_id = empty($_GET['assessment_id']) ? 0 : $_GET['assessment_id'];
    	$assessor_id = empty($_GET['assessor_id']) ? $this->user['user_id'] : $_GET['assessor_id'];
    	$isAdmin = in_array("view_all_assessments", $this->user['capabilities']) ? true : false;
    	$isNetworkAdmin = in_array("view_own_network_assessment", $this->user['capabilities']) && $this->user['network_id'] > 0 ? true : false;
    	$isSchoolAdmin = in_array("view_own_institute_assessment", $this->user['capabilities']) ? true : false;
    	$diagnosticModel = new diagnosticModel(); 
        $prefferedLanguage = $diagnosticModel->getAssessmentPrefferedLanguage($assessment_id);
        $lang_id_show = empty($lang_id) ? $prefferedLanguage['language_id'] : $lang_id;
        $external = empty($_GET['external']) ? 0 : $_GET['external'];
        $externalTeamStatus = $diagnosticModel->checkAssessorExternalTeam($assessment_id,$assessor_id);
        //print_r($externalStatus);
        if(isset($externalStatus['isExternal']) && $externalStatus['isExternal'] == 0){
           $external = 1;
        }
                
         
        //echo "<pre>";print_r($externalStatus);
    	if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
    		$this->_notPermitted = 1;
    		elseif ($assessment_id > 0 && $assessor_id > 0 && $assessment = $diagnosticModel->getAssessmentByUser($assessment_id, $assessor_id,$lang_id_show,$external)) {
    			$this->set('ddiagnosticId', $assessment['diagnostic_id']);
    			$diagData = $diagnosticModel->getDiagnosticName($assessment['diagnostic_id'],$lang_id_show);
    			$this->set('diagData',$diagData[0]);
    			//if ($assessment['aqs_status'] != 1) {
    				//$this->_notPermitted = 1;
    			//} else
                            $is_collaborative = isset($assessment['iscollebrative'])?$assessment['iscollebrative']:0;
                            $isLeadSave = isset($assessment['isLeadSave'])?$assessment['isLeadSave']:0;
                            $isLeadAssessor = 0;
                            
                            //check if assessor is lead or not
                            
                            
                            /*$assessmentLeadData = array();
                            if($external && $is_collaborative && !empty( $assessment_id )) {
                    
                                 $assessmentLeadData =  $diagnosticModel->getAssessmentLead($assessment_id);
                            }
                            $ratingUser = $assessor_id;
                            $ratingUserExternalStatus = $external;
                            */
                            
                            //echo "<pre>";print_r($assessment);
                            if ($assessor_id == $this->user['user_id'] || ($assessment['status'] == 1 && ( $isAdmin || ($isSchoolAdmin && $assessment['client_id'] == $this->user['client_id'] && $assessment['role'] == 3) || ($assessment['role']==3 && $this->user['user_id']==$assessment['external'] && $assessment['assessment_type_id']==2 && $assessment['status']==1) || ($isNetworkAdmin && $assessment['network_id'] == $this->user['network_id'] && $assessment['role'] == 3)))) {
    						$subAssessmentType = empty($assessment['subAssessmentType']) ? 0 : $assessment['subAssessmentType'];
    						$assessmentModel = new assessmentModel();
    						$subAssessmentType == 1 ? ($isApproved = $assessment['isApproved']) : '';
    	
    						if ($subAssessmentType == 1 && ($isApproved == 0 || $isApproved == 2) && !in_array("edit_all_submitted_assessments", $this->user['capabilities'])) {
    							$this->_notPermitted = 1;
    							return;
    						}
    	
    						$isReadOnly = $assessment['report_published'] == 1 || ($assessment['status'] == 1 && !in_array("edit_all_submitted_assessments", $this->user['capabilities'])) ? 1 : 0;
                                               // if($is_collaborative) {
                                                    //$kpas = $this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $assessor_id,0,$lang_id_show,$is_collaborative), "kpa_instance_id");
//                                                   / echo "<pre>";print_r($diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $assessor_id,0,$lang_id_show,$external));
                                               // }
                                                 /* if(!empty($assessmentLeadData)) {
                                                    $assessor_id = $assessmentLeadData['user_id']; 
                                                    $external = 0;
                                                 }*/
    						$this->set("isReadOnly", $isReadOnly);
    						$this->set("is_collaborative", $is_collaborative);
    						$this->set("external", $external);
    						$this->set("submitStatus", $assessment['status']);
                                                $this->set("isLeadAssessorKpa",0);
                                               // echo "<pre>";print_r($this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id,58,0,$lang_id_show,$is_collaborative,$external), "kpa_instance_id"));
                                                
                                                //die;
                                                //get all 
                                                $leadPercentage = 0;
                                                $percentageData = array();
                                                $allAccessorsId = array();
                                                $numAssessmentTeamMembers = 0;
                                                $totalPercentage = 0;
                                                $allTeamPercentage = 0;
                                                $isFilledStatus = 0;
                                                $isExternalFilled = 0;
                                                if($is_collaborative){
                                                    
                                                   // $externalStatus = $diagnosticModel->checkAssessorExternalTeam($assessment_id,$assessor_id);
                                                    $externalStatus = $diagnosticModel->checkAssessorIsLead($assessment_id,$assessor_id);
                                                    //print_r($externalStatus);
                                                    if(isset($externalStatus['isLead']) && $externalStatus['isLead'] >= 1){
                                                       $isLeadAssessor = 1; 
                                                       $leadPercentage = $assessment['percComplete'];
                                                       $numAssessmentTeamMembers = 1;
                                                    }
                                                    $this->set("isLeadAssessor",$isLeadAssessor);
                                                    //get external team rating percentage
                                                    if($isLeadAssessor){                                        
                                                        $percentageData = $diagnosticModel->getExternalTeamRatingPerc($assessment_id,$assessor_id);
                                                        //print_r($percentageData);
                                                        if(!empty($percentageData)){
                                                            $filledStatus = isset($percentageData['filledStatus'])?explode(",",$percentageData['filledStatus']):array();
                                                            if(!in_array(0,$filledStatus)){
                                                                if(!empty($percentageData) && $percentageData['percentageSum']>=1){

                                                                    $numAssessmentTeamMembers += $percentageData['numTeamMembers'];
                                                                    $allTeamPercentage = $leadPercentage+$percentageData['percentageSum'];
                                                                    $isExternalFilled = 1;
                                                                    $totalPercentage = $numAssessmentTeamMembers*100;
                                                                    $allAccessorsId = explode(",",$percentageData['user_ids']);
                                                                    //$allAccessorsId[] = $assessor_id;
                                                                    //if()
                                                                }
                                                            }
                                                        }else{
                                                            
                                                            $isFilledStatus = 1;
                                                        }
                                                    }
                                                }
                                                if( $isLeadAssessor &&  $totalPercentage >= 1 && $totalPercentage == $allTeamPercentage && $isExternalFilled == 1){

                                                       // print_r($allAccessorsId);
                                                    
                                                        $kpas = array();
                                                        $allKpas = array();
                                                        $kqs = array();
                                                        $cqs = array();
                                                        $jss = array();
                                                        $isFilledStatus = 1;
                                                        //echo $isLeadSave;die;
                                                        if($assessment['status'] == 0 && $isLeadSave==0){
                                                            //echo "<pre>";print_r($this->db->array_grouping($diagnosticModel->getKeyQuestionsForAssessment($assessment_id, 58,10,$lang_id_show,1), "kpa_instance_id", "key_question_instance_id"));
                                                            foreach($allAccessorsId as $key=>$val) {

                                                               //echo $val;
                                                             $kpas =   $this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $val,0,$lang_id_show,$is_collaborative,1), "kpa_instance_id");
                                                             $userKpas = array_keys($kpas);
                                                             $allKpas = $allKpas+$kpas;
                                                             // print_r($userKpas);
                                                             $kqs  = $kqs+ $this->db->array_grouping($diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $val,0,$lang_id_show,1,$userKpas), "kpa_instance_id", "key_question_instance_id");
                                                             $cqs  = $cqs+ $this->db->array_grouping($diagnosticModel->getCoreQuestionsForAssessment($assessment_id, $val,0,$lang_id_show,1,$userKpas), "key_question_instance_id", "core_question_instance_id");
                                                             $jss  = $jss+ $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForAssessment($assessment_id, $val,0,$lang_id_show,1,$userKpas), "core_question_instance_id", "judgement_statement_instance_id");
                                                            // ksort($kqs);
                                                             //echo "<pre>";print_r($kqs);die;
                                                             //die;
                                                            }
                                                           // die;
                                                            //echo $isLeadSave;die;
                                                            $leadKpas =   $this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $assessor_id,0,$lang_id_show,$is_collaborative,0), "kpa_instance_id");
                                                            $allKpas = $allKpas+$leadKpas;
                                                            
                                                            ksort($allKpas);
                                                           
                                                            $kqs = $kqs + $this->db->array_grouping($diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $assessor_id,0,$lang_id_show,0,array_keys($leadKpas)), "kpa_instance_id", "key_question_instance_id");
                                                            ksort($kqs);
                                                            $cqs  = $cqs + $this->db->array_grouping($diagnosticModel->getCoreQuestionsForAssessment($assessment_id, $assessor_id,0,$lang_id_show,0,array_keys($leadKpas)), "key_question_instance_id", "core_question_instance_id");
                                                            ksort($cqs);
                                                            $jss  = $jss + $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForAssessment($assessment_id, $assessor_id,0,$lang_id_show,0,array_keys($leadKpas)), "core_question_instance_id", "judgement_statement_instance_id");
                                                            ksort($jss);

                                                           // echo "<pre>";print_r($kqs);die;
                                                            //echo "<pre>";print_r($this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id,58,0,$lang_id_show,$is_collaborative,1), "kpa_instance_id"));
                                                            $this->set("kpas",$allKpas);
                                                            $this->set("isLeadAssessorKpa",1);
                                                            
                                                        }else {
                                                              $kpas =  $this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $assessor_id,0,$lang_id_show,0,$external), "kpa_instance_id");
                                                             //echo "<pre>";print_r($this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $assessor_id,0,$lang_id_show,0,$external), "kpa_instance_id"));
                                                              $kqs =  $this->db->array_grouping($diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $assessor_id,0,$lang_id_show,$external), "kpa_instance_id", "key_question_instance_id");
                                                              $cqs = $this->db->array_grouping($diagnosticModel->getCoreQuestionsForAssessment($assessment_id, $assessor_id,0,$lang_id_show,$external), "key_question_instance_id", "core_question_instance_id");
                                                              $jss = $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForAssessment($assessment_id, $assessor_id,0,$lang_id_show,$external), "core_question_instance_id", "judgement_statement_instance_id");
                                                              $this->set("kpas",$kpas);
                                                              if($is_collaborative == 1 && $assessment['status'] == 1 && $isLeadAssessor == 1 &&  $assessment['percComplete'] == 100){
                                                                  $this->set("isLeadAssessorKpa",1);
                                                              }else if($is_collaborative == 1 && $isLeadSave == 1 && $assessment['status'] == 0 && $isLeadAssessor == 1 &&  $assessment['percComplete'] == 100){
                                                                $this->set("isLeadAssessorKpa",1);
                                                              }
                                                        }
                                                       // echo "<pre>";print_r($kqs);
                                                        $this->set("kqs",$kqs);
                                                        $this->set("cqs",$cqs);
                                                        $this->set("jss",$jss);
                                                        
                                                        
                                                }else{
                                                        //echo "aaaa".$external;
                                                        $this->set("kpas", $this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $assessor_id,0,$lang_id_show,$is_collaborative,$external), "kpa_instance_id"));
                                                        //echo "<pre>";print_r($this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $assessor_id,0,$lang_id_show,0,$external), "kpa_instance_id"));
                                                        $this->set("kqs", $this->db->array_grouping($diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $assessor_id,0,$lang_id_show,$external), "kpa_instance_id", "key_question_instance_id"));
                                                        $this->set("cqs", $this->db->array_grouping($diagnosticModel->getCoreQuestionsForAssessment($assessment_id, $assessor_id,0,$lang_id_show,$external), "key_question_instance_id", "core_question_instance_id"));
                                                        $this->set("jss", $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForAssessment($assessment_id, $assessor_id,0,$lang_id_show,$external), "core_question_instance_id", "judgement_statement_instance_id"));
                                                }
    						if (isset($assessment) && $assessment['role'] == 4) {
    							$diagData[0]['js_recommendations']==1?$this->set("ajsns",$this->db->array_grouping($diagnosticModel->getAssessorKeyNotesLevel($assessment_id,'judgement_statement_instance_id'),'judgement_statement_instance_id','id')):$this->set("ajsns",0);
    							$diagData[0]['kpa_recommendations']==1?$this->set("akpans",$this->db->array_grouping($diagnosticModel->getAssessorKeyNotesLevel($assessment_id,'kpa_instance_id'),'kpa_instance_id','id')):$this->set("akpans",0);
    							$diagData[0]['kq_recommendations']==1?$this->set("akqns",$this->db->array_grouping($diagnosticModel->getAssessorKeyNotesLevel($assessment_id,'key_question_instance_id'),'key_question_instance_id','id')):$this->set("akqns",0);
    							$diagData[0]['cq_recommendations']==1?$this->set("acqns",$this->db->array_grouping($diagnosticModel->getAssessorKeyNotesLevel($assessment_id,'core_question_instance_id'),'core_question_instance_id','id')):$this->set("acqns",0);
    							//	$this->set("ajsns",$this->db->array_grouping($diagnosticModel->getAssessorKeyNotesLevel($assessment_id,'judgement_statement_instance_id'),'judgement_statement_instance_id','id'));
    							/* $this->set("akqns",$diagnosticModel->getAssessorKeyNotesLevel($assessment_id,'key_question_instance_id'));
    							 $this->set("acqns",$diagnosticModel->getAssessorKeyNotesLevel($assessment_id,'core_question_instance_id'));
    							 $this->set("ajsns",$diagnosticModel->getAssessorKeyNotesLevel($assessment_id,'judgement_statement_instance_id')); */
    							//$this->set("akns", $diagnosticModel->getAssessorKeyNotes($assessment_id));
    							$this->set("diagnosticModel", $diagnosticModel);
    						}
                                                $isRevCompleteNtSubmitted = 0;
                                                if($is_collaborative){
                                                    if($assessment['status']==0 && $external == 0 && $isFilledStatus == 1){ 
                                                       $isRevCompleteNtSubmitted = 1;
                                                    }
                                                }else{

                                                    if($assessment['status']==0 && intval($assessment['percComplete'])=='100'){
                                                                        $isRevCompleteNtSubmitted = 1;
                                                    }
                                                }
                                                //echo $isFilledStatus;
                                                $self_review=$diagnosticModel->getAssessmentByRole($assessment_id,3,$lang_id_show);
                                                $this->set("self_review", $self_review);                                                
                                                $this->set("isRevCompleteNtSubmitted", $isRevCompleteNtSubmitted);                                                
    						$this->set("assessment_id", $assessment_id);
    						$this->set("assessor_id", $assessor_id);
    						$this->set("assessment", $assessment);
                                                $this->set("isFilledStatus",$isFilledStatus);
    						$this->set("isAdmin", $isAdmin);
    						$this->set("isNetworkAdmin", $isNetworkAdmin);
    						$this->set("isSchoolAdmin", $isSchoolAdmin);
    						$this->set("prefferedLanguage", $lang_id_show);
    	
    						$dig_image = $diagnosticModel->getDiagnosticImage($assessment['diagnostic_id']);
    						$image_name = $dig_image[0]['file_name'];
    						$this->set("image_name", $image_name);
                                                $this->set("diagnosticLanguages",$diagnosticModel->getDiagnosticLanguages($assessment['diagnostic_id']));
                                                $diagnosticLabels = array();
                                               
                                                $diagnosticLabelsData = $diagnosticModel->getDiagnosticLabels($assessment['diagnostic_id'],$lang_id_show);
                                                foreach($diagnosticLabelsData as $data) {
                                                    $diagnosticLabels[$data['label_key']] = $data['label_text'];
                                                }
                                                $this->set("diagnosticLabels", $diagnosticLabels);
                                                $this->set("isLeadAssessor", $isLeadAssessor);
                                               //echo "<pre>"; print_r($diagnosticLabels);;
                                                //echo  $assessment['language_id'];
    						$this->_template->addHeaderStyle('bootstrap-select.min.css');
    						$this->_template->addHeaderStyle('assessment-form.css');
    						$this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
    						//$this->_template->addHeaderScript('bootstrap-multiselect-0.9.13.js');
    						$this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
    						//$this->_template->addHeaderStyle('bootstrap-multiselect-0.9.13.css');
    						//$this->_template->addHeaderScript('bootstrap-select.min.js');
                                                $this->_template->addHeaderStyle('bootstrap-multiselect.css');
                                                $this->_template->addHeaderScript('bootstrap-multiselect.js');
                                                
                                                $this->_template->addHeaderStyle('jquery-confirm.min.css');
                                                $this->_template->addHeaderScript('jquery-confirm.min.js');
    						$this->_template->addHeaderScript('assessment.js');
                                                $this->_template->addHeaderScript('localize.js');
                                                $this->_template->addHeaderScript('assessment_rec.js');
    					} else
    						$this->_notPermitted = 1;
    		}else {
    			$this->_is404 = 1;
    		}
    }

    function aqsFormAction() {
        $assmntId_or_grpAssmntId = empty($_GET['assmntId_or_grpAssmntId']) ? 0 : $_GET['assmntId_or_grpAssmntId'];
        $assessment_type_id = empty($_GET['assessment_type_id']) ? 0 : $_GET['assessment_type_id'];
        $diagnosticModel = new diagnosticModel();
        $objClientModel = new clientModel();
        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif ($assmntId_or_grpAssmntId == 0 || $assessment_type_id == 0) {
            $this->_notPermitted = 1;
            return;
        }
        $aqsDataModel = new aqsDataModel();
        $assessment = ($assessment_type_id == 1 || $assessment_type_id == 5) ? $diagnosticModel->getAssessmentById($assmntId_or_grpAssmntId) : $diagnosticModel->getGroupAssessmentByGAId($assmntId_or_grpAssmntId);
        //echo "<pre>";print_r($assessment);die;
        //if ashoka changemaker diagnostic(32) is assigned, additional information table has to be filled
        if (!empty($assessment['table_name'])) {
            $aqs_additional_id = !empty($assessment['aqs_additional_id']) ? $assessment['aqs_additional_id'] : 0;
            $this->set('aqs_additional_id', $aqs_additional_id);
            
            include ROOT . DS . 'application' . DS . 'helpers' . DS . 'aqs-additional-tab-forms.php';
            $this->set('form_elements', $form_elements);
            $this->set('school_community_id', $diagnosticModel->getSchoolCommunities());
            $this->set('review_medium_instrn_id', $diagnosticModel->getInstructionMedium($assessment['diagnostic_id']));
            $this->set('additional_data', $aqsDataModel->getAqsAdditionalData($assessment['aqsdata_id'], 'd_aqs_additional_questions'));
            $this->set('aqs_additional_ref', $aqsDataModel->getAqsAdditionalRefTeam($assessment['aqsdata_id']));
            //$aqs_additional_ref
        }
        $this->set('aqs_school_type', $aqsDataModel->getAqsSchoolType($assessment['assessment_id']));
        //if online self-review, check if online payment has been made or offline payment has been received
        $subAssessmentType = empty($assessment['subAssessmentType']) ? 0 : $assessment['subAssessmentType'];
        $assessmentModel = new assessmentModel();
        $subAssessmentType == 1 ? ($isApproved = $assessment['isApproved']) : '';

        if ($subAssessmentType == 1 && ($isApproved == 0 || $isApproved == 2) && !in_array("edit_all_submitted_assessments", $this->user['capabilities'])) {
            $this->_notPermitted = 1;
            return;
        }
        if ($assessment && ($assessment_type_id == 1 || $assessment_type_id == 5  || $assessment['assessmentAssigned'] > 0)) {
            $isNetworkAdmin = in_array("view_own_network_assessment", $this->user['capabilities']) && $assessment['network_id'] == $this->user['network_id'] && $this->user['network_id'] > 0 ? 1 : 0;
            $isSchoolAdmin = in_array("view_own_institute_assessment", $this->user['capabilities']) && $assessment['client_id'] == $this->user['client_id'] ? 1 : 0;
            $isPrincipal = $isSchoolAdmin && in_array(6, $this->user['role_ids']) ? 1 : 0;
            //also check if user is in external team for the review
            if ($assessment_type_id == 1 || $assessment_type_id == 5)
                $checkExternalTeam = $diagnosticModel->isUserinExternalTeamAssessment($assessment['assessment_id'], $this->user['user_id']);
            // print_r($checkExternalTeam);echo $checkExternalTeam['num'];
            //die;
            if (( (($assessment_type_id == 1  || $assessment_type_id == 5 ) && ($checkExternalTeam = $diagnosticModel->isUserinExternalTeamAssessment($assessment['assessment_id'], $this->user['user_id']))) && $checkExternalTeam['num'] > 0 && in_array('take_external_assessment', $this->user['capabilities'])) || in_array($this->user['user_id'], $assessment['user_ids']) || in_array("view_all_assessments", $this->user['capabilities']) || $isSchoolAdmin || $isNetworkAdmin) {
                $isReadOnly = 1;
                if ($assessment_type_id == 1 || $assessment_type_id == 5) {
                    $isReadOnly = $assessment['report_published'] != 1 && ( ($assessment['aqs_status'] == 1 && in_array("edit_all_submitted_assessments", $this->user['capabilities'])) || ($assessment['aqs_status'] == 0 && ( $assessment['userIdByRole'][3] == $this->user['user_id'] || $isSchoolAdmin || $isNetworkAdmin )
                            )) ? 0 : 1;
                } else {
                    $isReadOnly = ($assessment['aqs_status'] == 1 && in_array("edit_all_submitted_assessments", $this->user['capabilities'])) || ($assessment['aqs_status'] == 0 && ( $assessment['admin_user_id'] == $this->user['user_id'] || $isPrincipal || $isNetworkAdmin)) ? 0 : 1;
                }


                $this->set("isReadOnly", $isReadOnly);

                $this->set("assmntId_or_grpAssmntId", $assmntId_or_grpAssmntId);
                $this->set("assessment_type_id", $assessment_type_id);
                $this->set("assessment", $assessment);
                $this->set("countryCodeList",$objClientModel->getCountryWithCode());
                $aqsDataModel = new aqsDataModel();
                // aqs versions of past-filled Data
                //getSchoolRegionList

                $this->set("medium_instrn_langs", $diagnosticModel->getAllLanguages());
                $this->set("school_region_list", $aqsDataModel->getSchoolRegionList());
                $aqsVersions = $aqsDataModel->getAQSversion($assessment['client_id']);
               // print_r($aqsDataModel->getAQSSchoolList($assmntId_or_grpAssmntId));
                $this->set("AQSversions", $aqsVersions);
                $this->set("referrer_list", $aqsDataModel->getReferrerList());
                $this->set("principal", $this->userModel->getPrincipal($assessment['client_id']));
                $this->set("board_list", $aqsDataModel->getBoardList(isset($aqsVersions[0]['country_id'])?$aqsVersions[0]['country_id']:0));
                $this->set("school_type_list", $aqsDataModel->getSchoolTypeList());
               // $this->set("aqs_school_list", $aqsDataModel->getAQSSchoolList($assmntId_or_grpAssmntId));
                $this->set("school_it_support_list", $aqsDataModel->getSchoolItSupportList());
                $this->set("school_class_list", $aqsDataModel->getSchoolClassList());
                $this->set("school_level_list", $aqsDataModel->getSchoolLevelList());
                $this->set("student_type_list", $aqsDataModel->getStudentTypeList());

                $aqs = $aqsDataModel->getAqsData($assmntId_or_grpAssmntId, $assessment_type_id);
                //echo "<pre>";print_r($aqs);
                $this->set("aqs", $aqs);
                $team = isset($aqs['id']) ? $aqsDataModel->getAQSTeam($aqs['id']) : array("school" => array());
                
                //echo '<pre>'; print_r($team);
                $this->set("school_team", $team['school']);

                $this->_template->addHeaderStyle('bootstrap-select.min.css');
                $this->_template->addHeaderStyle('assessment-form.css');
                $this->_template->addHeaderScript('bootstrap-select.min.js');
                $this->_template->addHeaderScript('aqsForm.js');
                $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
                $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
            } else
                $this->_notPermitted = 1;
        }else {
            $this->_is404 = 1;
        }
    }

    function assessmentPreviewAction() {
        $diagnosticModel = new diagnosticModel();
        $assessment_id = empty($_GET['assessment_id']) ? 0 : $_GET['assessment_id'];
        $assessor_id = empty($_GET['assessor_id']) ? $this->user['user_id'] : $_GET['assessor_id'];
        
        $external = empty($_GET['external']) ? 0 : $_GET['external'];
        //$externalTeamStatus = $diagnosticModel->checkAssessorExternalTeam($assessment_id,$assessor_id);
        //print_r($externalStatus);
        $is_collaborative = 0;
        if(isset($external) && $external == 1){
           $external = 1;
            $is_collaborative = 1;
        }
        $prefferedLanguage = $diagnosticModel->getAssessmentPrefferedLanguage($assessment_id);
        //print_r($prefferedLanguage);
        if(count($prefferedLanguage)==0){
        $lang_id = empty($_GET['lang_id']) ? DEFAULT_LANGUAGE : $_GET['lang_id'];
        }else{
        $lang_id=$prefferedLanguage['language_id'];    
        }
        
        $isAdmin = in_array("view_all_assessments", $this->user['capabilities']) ? true : false;
        $isNetworkAdmin = in_array("view_own_network_assessment", $this->user['capabilities']) && $this->user['network_id'] > 0 ? true : false;
        $isSchoolAdmin = in_array("view_own_institute_assessment", $this->user['capabilities']) ? true : false;
        
        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif ($assessment_id > 0 && $assessor_id > 0 && $assessment = $diagnosticModel->getAssessmentByUser($assessment_id, $assessor_id,$lang_id,$external)) {

            $subAssessmentType = empty($assessment['subAssessmentType']) ? 0 : $assessment['subAssessmentType'];
            $assessmentModel = new assessmentModel();
            $subAssessmentType == 1 ? ($isApproved = $assessment['isApproved']) : '';

            if ($subAssessmentType == 1 && ($isApproved == 0 || $isApproved == 2) && !in_array("edit_all_submitted_assessments", $this->user['capabilities'])) {
                $this->_notPermitted = 1;
                return;
            }

            if ($assessor_id == $this->user['user_id'] || ($assessment['status'] == 1 && ( $isAdmin || ($isSchoolAdmin && $assessment['client_id'] == $this->user['client_id'] && $assessment['role'] == 3) || ($assessment['role']==3 && $this->user['user_id']==$assessment['external'] && $assessment['assessment_type_id']==2 && $assessment['status']==1) || ($isNetworkAdmin && $assessment['network_id'] == $this->user['network_id'] && $assessment['role'] == 3)
                    )
                    )
            ) {
                $kpa_id = empty($_GET['kpa_id']) ? 0 : $_GET['kpa_id'];
                $kpas = $this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $assessor_id, $kpa_id,$lang_id,$is_collaborative,$external,$isLeadAssessorKpa=0), "kpa_instance_id");
                $kpas = array_column($kpas,'kpa_instance_id');
                $this->set("kpas", $this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $assessor_id, $kpa_id,$lang_id,$is_collaborative,$external,$isLeadAssessorKpa=0), "kpa_instance_id"));
                $this->set("kqs", $this->db->array_grouping($diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $assessor_id, $kpa_id,$lang_id,$external,$kpas), "kpa_instance_id", "key_question_instance_id"));
                $this->set("cqs", $this->db->array_grouping($diagnosticModel->getCoreQuestionsForAssessment($assessment_id, $assessor_id, $kpa_id,$lang_id,$external,$kpas), "key_question_instance_id", "core_question_instance_id"));
                $this->set("jss", $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForAssessment($assessment_id, $assessor_id, $kpa_id,$lang_id,$external,$kpas), "core_question_instance_id", "judgement_statement_instance_id"));
                if (isset($assessment) && $assessment['role'] == 4) {
                    $this->set("akns", $this->db->array_grouping($diagnosticModel->getAssessorKeyNotes($assessment_id), "kpa_instance_id", "id"));
                    $this->set("diagnosticModel", $diagnosticModel);
                }
                
                $diagnosticLabels = array();
                                               
                $diagnosticLabelsData = $diagnosticModel->getDiagnosticLabels($assessment['diagnostic_id'],$lang_id);
                foreach($diagnosticLabelsData as $data) {
                $diagnosticLabels[$data['label_key']] = $data['label_text'];
                }
                $this->set("diagnosticLabels", $diagnosticLabels);
                //echo $external; echo "<pre>"; print_r($this->db->array_grouping($diagnosticModel->getJudgementalStatementsForAssessment($assessment_id, $assessor_id, $kpa_id,$lang_id,$external,$kpas), "core_question_instance_id", "judgement_statement_instance_id"));
                //print_r($diagnosticLabels);
                
                $this->set("assessment_id", $assessment_id);
                $this->set("assessor_id", $assessor_id);
                $this->set("assessment", $assessment);

                $this->set("isAdmin", $isAdmin);
                $this->set("isNetworkAdmin", $isNetworkAdmin);
                $this->set("isSchoolAdmin", $isSchoolAdmin);

                $this->_template->addHeaderStyle('assessment-form.css');
                $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
                $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
                //$this->_template->addHeaderScript('assessment.js');
            } else
                $this->_notPermitted = 1;
        }else {
            $this->_is404 = 1;
        }
    }

    function teacherInfoFormAction() {
        $assessment_id = empty($_GET['assessment_id']) ? 0 : $_GET['assessment_id'];
        $diagnosticModel = new diagnosticModel();
        $assessmentModel = new assessmentModel();
        $objClientModel = new clientModel();
        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif ($assessment_id > 0 && ($groupAssmt = $diagnosticModel->getGroupAssessmentByAssmntId($assessment_id)) && ($teacherInfo = $assessmentModel->getTeacherInfo($assessment_id))) {
            $isNetworkAdmin = in_array("view_own_network_assessment", $this->user['capabilities']) && $groupAssmt['network_id'] == $this->user['network_id'] && $this->user['network_id'] > 0 ? 1 : 0;
            $isSchoolAdmin = in_array("view_own_institute_assessment", $this->user['capabilities']) && $groupAssmt['client_id'] == $this->user['client_id'] ? 1 : 0;

            if (in_array($this->user['user_id'], $groupAssmt['user_ids']) || in_array("view_all_assessments", $this->user['capabilities']) || $isSchoolAdmin || $isNetworkAdmin) {

                $isReadOnly = $groupAssmt['report_published'] != 1 && ( ($teacherInfo['isTeacherInfoFilled']['value'] == 1 && in_array("edit_all_submitted_assessments", $this->user['capabilities'])) || ($teacherInfo['isTeacherInfoFilled']['value'] != 1 && ( $groupAssmt['user_ids'][0] == $this->user['user_id'] )
                        )) ? 0 : 1;
                $this->set("assessment_id", $assessment_id);
                $this->set("isReadOnly", $isReadOnly);
                $this->set("groupAssmt", $groupAssmt);
                $this->set("tchrInfo", $teacherInfo);
                $this->set("countryCodeList",$objClientModel->getCountryWithCode());
                $this->_template->addHeaderScript('teacherInfoForm.js');
                $this->_template->addHeaderStyle('bootstrap-select.min.css');
            } else {
                $this->_notPermitted = 1;
            }
        } else {
            $this->_is404 = 1;
        }
    }

    function addDiagnosticAction() {
        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif (in_array("manage_diagnostic", $this->user['capabilities'])) {
            $diagnosticModel = new diagnosticModel();
            $assessmentId = empty($_GET['assessmentId']) ? 0 : $_GET['assessmentId']; //get type of assessment
            $diagnosticId = empty($_GET['langId']) ? 0 : $_GET['diagnosticId']; //get diagnostic
            $langId = empty($_GET['langId']) ? DEFAULT_LANGUAGE : $_GET['langId']; //get lang
            $this->set("assessmentId", $assessmentId); //get type of assessment
            $this->set("diagnosticId", $diagnosticId); //get type of assessment
            $this->set("assessmentType", ''); //get type of assessment
            $this->set("diagnosticName", ''); //get type of assessment
            $this->set("langId", $langId); //get type of lang
            $this->set("teacherCategory", '');
            $this->set("isDiagnosticPublished", 0);
            if ($diagnosticId > 0) {
                $diagName = $diagnosticModel->getDiagnosticNameByLang($diagnosticId,$langId);
                $assmtType = $diagnosticModel->getAssessmentTypeById($assessmentId);
                $diagImage = $diagnosticModel->getDiagnosticImage($diagnosticId);
                //print_r($diagImage);
                if ($diagName == '' || $diagName == NULL || !$assmtType) {
                    $this->_is404 = 1;
                    exit;
                }
                $this->set('assessmentType', ($assmtType));
                $isDiagnosticPublished = $diagnosticModel->getDiagnosticBYLang($diagnosticId,$langId);
                $teacherCategory = $diagnosticModel->getDiagnosticTeacherCategory($diagnosticId);
                $parent_lang_translation_id= $isDiagnosticPublished['parent_lang_translation_id'];
                $this->set('languageName', $isDiagnosticPublished['language_name']);
                $isDiagnosticPublished = $isDiagnosticPublished['isPublished'];
                if ($isDiagnosticPublished > 0)
                    $this->_notPermitted = 1;
                $this->set("isDiagnosticPublished", $isDiagnosticPublished);
                $this->set('diagnosticName', $diagName[0]['name']);
                $this->set('kpaRecommendations', $diagName[0]['kpa_recommendations']);
                $this->set('kqRecommendations', $diagName[0]['kq_recommendations']);
                $this->set('cqRecommendations', $diagName[0]['cq_recommendations']);
                $this->set('jsRecommendations', $diagName[0]['js_recommendations']);
                $this->set('teacherCategory', $teacherCategory);
                
                $diagnosticLabels = array();                                               
                $diagnosticLabelsData = $diagnosticModel->getDiagnosticLabels($diagnosticId,$langId);
                foreach($diagnosticLabelsData as $data) {
                    $diagnosticLabels[$data['label_key']] = $data['label_text'];
                }
                $this->set("diagnosticLabels", $diagnosticLabels);
                
                $this->set('kpas', $this->db->array_col_to_key($diagnosticModel->getKpasForDiagnosticLang($diagnosticId,$langId), "kpa_instance_id"));
                $this->set('kqs', $this->db->array_grouping($diagnosticModel->getKeyQuestionsForDiagnosticLang($diagnosticId,$langId), "kpa_instance_id", "key_question_instance_id"));
                $this->set('cqs', $this->db->array_grouping($diagnosticModel->getCoreQuestionsForDiagnosticLang($diagnosticId,$langId), "key_question_instance_id", "core_question_instance_id"));
                $this->set('jss', $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForDiagnosticLang($diagnosticId,$langId), "core_question_instance_id", "judgement_statement_instance_id"));
                $this->set('image_name', $diagImage[0]['file_name']);
                $this->set('imageId', $diagImage[0]['diagnostic_image_id']);
                
                $diagnostic_type=$diagnosticModel->getDiagnosticDetailsfromTransLang($parent_lang_translation_id);
                $this->set("langIdOriginal",(isset($diagnostic_type['language_id']) && $diagnostic_type['language_id']>0)?$diagnostic_type['language_id']:0);
                $this->set("equivalenceId",(isset($diagnostic_type['equivalence_id']) && $diagnostic_type['equivalence_id']>0)?$diagnostic_type['equivalence_id']:0);
                $this->set("parentId",(isset($parent_lang_translation_id) && $parent_lang_translation_id>0)?$parent_lang_translation_id:0);
                
                
                
            }
            $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
            $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
            $this->_template->addHeaderScript('bootstrap-multiselect-0.9.13.js');
            $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
            $this->_template->addHeaderStyle('bootstrap-multiselect-0.9.13.css');
            $this->_template->addHeaderScript('diagnostic.js');
        } else
            $this->_notPermitted = 1;
    }

    function addMoreFormAction() {
        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif (in_array("manage_diagnostic", $this->user['capabilities'])) {
            $type = $_GET['type']; //get type of adding request kpa/kq/cq/js
            $assessmentId = $_GET['assessmentId']; //get type of assessment
            $diagnosticId = empty($_GET['diagnosticId']) ? 0 : $_GET['diagnosticId']; //get type of assessment
            $kpaId = empty($_GET['kpaId']) ? 0 : $_GET['kpaId'];
            $kqId = empty($_GET['kqid']) ? 0 : $_GET['kqid'];
            $cqId = empty($_GET['cqId']) ? 0 : $_GET['cqId'];
            $langId = empty($_GET['langId']) ? 0 : $_GET['langId'];
            
            $parentId = empty($_GET['parentId']) ? 0 : trim($_GET['parentId']);
            $equivalenceId = empty($_GET['equivalenceId']) ? 0 : trim($_GET['equivalenceId']);
            $langIdOriginal = empty($_GET['langIdOriginal']) ? 0 : trim($_GET['langIdOriginal']);

            $this->set("kpaId", $kpaId);
            $this->set("kqId", $kqId);
            $this->set("cqId", $cqId);
            $this->set("assessmentId", $assessmentId); //get type of assessment
            $this->set("type", $type);
            $this->set("for", 'add_diagnostic_form');
            $this->set("isDiagnosticPublished", 0);
            $this->set("langId",$langId);
            $this->set("parentId",$parentId);
            $this->set("equivalenceId",$equivalenceId);
            $this->set("langIdOriginal",$langIdOriginal);
            
            $diagnosticModel = new diagnosticModel();
            if ($diagnosticId > 0) {
                $isDiagnosticPublished = $diagnosticModel->getDiagnosticBYLang($diagnosticId,$langId);
                $this->set("dig_image_id", $isDiagnosticPublished['diagnostic_image_id']);

                $isDiagnosticPublished = $isDiagnosticPublished['isPublished'];
                $this->set("isDiagnosticPublished", $isDiagnosticPublished);
            }
            //echo $type."tt";
            //if($type=='kpa')
            //$this->set("currQuestions",$diagnosticModel->getKpasForAssessmentType($assessmentId));
            switch ($type) {
                case 'kpa' : $this->set("currQuestions", $diagnosticModel->getKpasForAssessmentType($assessmentId, $diagnosticId, $langId));
                    $this->set("formTitle", "KPA");
                    $this->set("diagnosticId", $diagnosticId);
                    if($langIdOriginal>0){
                    $this->set("selectedQuestions", $this->db->array_col_to_key($diagnosticModel->getSelectedKpasForDiagnostic($diagnosticId,$langId),"kpa_id"));    
                    }else{
                    $this->set("selectedQuestions", $diagnosticModel->getSelectedKpasForDiagnostic($diagnosticId,$langId));
                    }
                    $this->set("selectedQuestionsOriginal", $diagnosticModel->getSelectedKpasForDiagnostic($diagnosticId,$langIdOriginal));
                    $diagImage = $diagnosticModel->getDiagnosticImage($diagnosticId);
                    if (!empty($diagImage)) {
                        $this->set('image_name', isset($diagImage[0]['file_name']) ? $diagImage[0]['file_name'] : '');
                    }
                    break;
                case 'kq' : $this->set("currQuestions", $diagnosticModel->getAllKeyQuestions($diagnosticId,$langId));
                    $this->set("formTitle", "Key Question");
                    $this->set("diagnosticId", $diagnosticId);
                    if($langIdOriginal>0){
                    $this->set("selectedQuestions", $this->db->array_col_to_key($diagnosticModel->getSelectedKeyQuestionsForDiagnostic($diagnosticId,$kpaId,$langId),"key_question_id"));    
                    }else{
                    $this->set("selectedQuestions", $diagnosticModel->getSelectedKeyQuestionsForDiagnostic($diagnosticId, $kpaId, $langId));
                    }
                    $this->set("selectedQuestionsOriginal", $diagnosticModel->getSelectedKeyQuestionsForDiagnostic($diagnosticId,$kpaId,$langIdOriginal));
                    break;
                case 'cq' : $this->set("currQuestions", $diagnosticModel->getAllCoreQuestions($diagnosticId,$langId));
                    $this->set("formTitle", "Sub Question");
                    $this->set("diagnosticId", $diagnosticId);
                    if($langIdOriginal>0){
                    $this->set("selectedQuestions", $this->db->array_col_to_key($diagnosticModel->getSelectedCoreQuestionsForDiagnostic($diagnosticId,$kpaId,$kqId,$langId),"core_question_id"));    
                        
                    }else{
                    $this->set("selectedQuestions", $diagnosticModel->getSelectedCoreQuestionsForDiagnostic($diagnosticId, $kpaId, $kqId,$langId));
                    }
                    $this->set("selectedQuestionsOriginal", $diagnosticModel->getSelectedCoreQuestionsForDiagnostic($diagnosticId, $kpaId, $kqId,$langIdOriginal));
                    break;
                case 'jss' : $this->set("currQuestions", $diagnosticModel->getAllJudgementStatements($diagnosticId,$langId));
                    $this->set("formTitle", "Judgement Statement");
                    $this->set("diagnosticId", $diagnosticId);
                    if($langIdOriginal>0){
                    $this->set("selectedQuestions", $this->db->array_col_to_key($diagnosticModel->getSelectedJSSForDiagnostic($diagnosticId,$kpaId,$kqId,$cqId,$langId),"judgement_statement_id"));    
                        
                    }else{
                    $this->set("selectedQuestions", $diagnosticModel->getSelectedJSSForDiagnostic($diagnosticId, $kpaId, $kqId, $cqId,$langId));
                    }
                    $this->set("selectedQuestionsOriginal", $diagnosticModel->getSelectedJSSForDiagnostic($diagnosticId, $kpaId, $kqId, $cqId,$langIdOriginal));
                    break;
            }
        } else
            $this->_notPermitted = 1;
           //echo "hiiii";
    }

    function assessmentTypeAction() {
        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif (in_array("manage_diagnostic", $this->user['capabilities'])) {
            $diagnosticModel = new diagnosticModel();
            $this->set("assessmentTypes", $diagnosticModel->getAssessmentTypes());
            $assessmentModel = new assessmentModel();
            $this->set("teacherCategories", $assessmentModel->getTeacherCategoryList());
            $this->set("diagnostics", $diagnosticModel->getAllDiagnosticsList());
            $this->set("diagnosticsLanguage", $this->userModel->getTranslationLanguale(explode(",",DIAGNOSTIC_LANG)));
        }
    }

    function postReviewAction() {
        $isAdmin = in_array("view_all_assessments", $this->user['capabilities']) ? true : false;
        $isExternalReviewer = in_array("take_external_assessment", $this->user['capabilities']) ? true : false;

        $diagnosticModel = new diagnosticModel();
        $assessment_id = $_GET['assessment_id'];
        $aqsdata_id = $_GET['aqsdata_id'];
        $assessor_id = empty($_GET['assessor_id']) ? $this->user['user_id'] : $_GET['assessor_id'];
        $this->set('assessor_id', $assessor_id);
        
        $prefferedLanguage = $diagnosticModel->getAssessmentPrefferedLanguage($assessment_id);
        //print_r($prefferedLanguage);
        if(count($prefferedLanguage)==0){
        $lang_id = empty($_GET['lang_id']) ? DEFAULT_LANGUAGE : $_GET['lang_id'];
        }else{
        $lang_id=$prefferedLanguage['language_id'];    
        }
        
        $assessment = $diagnosticModel->getAssessmentByUser($assessment_id, $assessor_id,$lang_id);
        if ($assessment['status'] == 1 && ($isAdmin || ($assessor_id == $this->user['user_id'] && $assessment['role'] == 4 && $isExternalReviewer) )) {
            $this->set('assessment', $assessment);
            $this->set("assessment_id", $assessment_id);
            $this->set("diagnostic_id", $assessment['diagnostic_id']);
            $currentData = $diagnosticModel->getPostReviewData($assessment_id);
            $fields = $diagnosticModel->getCommentsFieldPostReview();
            //print_r($fields);
            $this->set('fields', $fields);
            $subAssessmentType = empty($assessment['subAssessmentType']) ? 0 : $assessment['subAssessmentType'];
            $assessmentModel = new assessmentModel();
            $subAssessmentType == 1 ? ($isApproved = $assessment['isApproved']) : '';

            if ($subAssessmentType == 1 && ($isApproved == 0 || $isApproved == 2) && !in_array("view_all_assessments", $this->user['capabilities'])) {
                $this->_notPermitted = 1;
                return;
            }

            $this->set("currentData", $currentData);
            $isReadOnly = ($currentData['status'] != 1 && in_array("view_all_assessments", $this->user['capabilities'])) || ($currentData['status'] == 1 && in_array("take_external_assessment", $this->user['capabilities'])) ? 1 : 0;
            //$isReadOnly = ($currentData['status']!=1 && in_array("edit_all_submitted_assessments",$this->user['capabilities']))||($currentData['status']==1 && in_array("take_external_assessment",$this->user['capabilities']))?1:0;

            $this->set("isReadOnly", $isReadOnly);
            $this->set("aqsdata_id", $aqsdata_id);
            $this->set("lang_id", $lang_id);
            $this->set("postReviewDecisionList", $diagnosticModel->getPostReviewDecisionList());
            $this->set("postReviewEngMgmtList", $diagnosticModel->getPostReviewEngMgmtList());
            $this->set("postReviewActionList", $diagnosticModel->getPostReviewActionList());
            $this->set("postReviewPrinTenure", $diagnosticModel->getPostReviewPrinTenure());
            $this->set("postReviewVision", $diagnosticModel->getPostReviewVision());
            $this->set("postReviewInvolvement", $diagnosticModel->getPostReviewInvolvement());
            $this->set("postReviewOpenness", $diagnosticModel->getPostReviewOpenness());
            $this->set("postReviewMidLeaders", $diagnosticModel->getPostReviewMidLeaders());
            $this->set("postReviewParentTeacherAssoc", $diagnosticModel->getPostReviewParentTeacherAssoc());
            $this->set("postReviewAlumniAssoc", $diagnosticModel->getPostReviewAlumniAssoc());
            $this->set("postReviewStaffTenure", $diagnosticModel->getPostReviewStaffTenure());
            $this->set("postReviewClassRooms", $diagnosticModel->getPostReviewClassRooms());
            $this->set("postReviewAvgStudents", $diagnosticModel->getPostReviewAvgStudents());
            $this->set("postReviewAvgTeachers", $diagnosticModel->getPostReviewAvgTeachers());
            $this->set("postReviewRatioClass", $diagnosticModel->getPostReviewRatioClass());
            $this->set("postReviewTeachingStaffCount", $diagnosticModel->getPostReviewStaffCount('teaching'));
            $this->set("postReviewNonTeachingStaffCount", $diagnosticModel->getPostReviewStaffCount('non-teaching'));
            $this->set("postReviewStudentBody", $diagnosticModel->getPostReviewStudentBody());
            // $this->set("instructionMedium", $diagnosticModel->getInstructionMedium());
            //$this->set("allLanguages", $diagnosticModel->getAllLanguages());
            //$playgrounds = array(1, 2, 3, 4, 5, 'more than 5');
            //$this->set("playgrounds", $playgrounds);
            $schoolLevelList = $diagnosticModel->getValidSchoolLevels($aqsdata_id, $currentData['post_review_id']);
            $actionPlanningData = $diagnosticModel->getPostReviewActionPlanningData($currentData['post_review_id']);
            $this->set("actionPlanningData", $actionPlanningData);

            $this->set("schoolLevelList", $schoolLevelList);
            
            $studentTeacherLevelList = $diagnosticModel->getValidSchoolLevelsStudentTeacherClass($aqsdata_id, $currentData['post_review_id']);
            //print_r($studentTeacherLevelList);
            $this->set("studentTeacherLevelList", $studentTeacherLevelList);
            
            $hasPrep = $this->findKey($schoolLevelList, 'school_level_id', 1);

            $this->set("hasPrep", $hasPrep);
            $hasNonPrep = 0;
            foreach ($schoolLevelList as $level) {
                if ($level['school_level_id'] == 1)
                    continue;

                $hasNonPrep = 1;
            }
            $this->set("hasNonPrep", $hasNonPrep);
            //$this->_template->addHeaderScriptURL('//cloud.tinymce.com/stable/tinymce.min.js');
            // $this->_template->addHeaderScriptURL(SITEURL.'public'.DS.'js'.DS.'tinymce'.DS.'tinymce.min.js');
            // $this->_template->addHeaderStyle ( 'selectize.default.css' );
            // $this->_template->addHeaderScript ( 'selectize.min.js' );
            $this->_template->addHeaderStyleURL('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css');
            $this->_template->addHeaderScriptURL('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js');
            $this->_template->addHeaderScript('postreview.js');
        } else
            $this->_notPermitted;
    }
    
    function findKey($array, $key, $value) {
        foreach ($array as $item)
            if (isset($item[$key]) && $item[$key] == $value)
                return true;
        return false;
    }

    function uploadDiagImageAction() {
        
    }

    function convertToWordFileAction() {
        $this->_render = false;
        $diagnostic_id = empty($_GET['id']) ? 0 : $_GET['id'];
        $langId = empty($_GET['langId']) ? DEFAULT_LANGUAGE : $_GET['langId'];
        $diagnosticModel = new diagnosticModel();

        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif ($diagnostic_id > 0 && $diagnostic = $diagnosticModel->getDiagnosticBYLang($diagnostic_id,$langId)) {
            if (in_array("manage_diagnostic", $this->user['capabilities'])) {

                ini_set('max_execution_time', 1200);
                //die(ROOT . "library/vendor/phpoffice/phpword/bootstrap.php");
                require_once(ROOT . "library/vendor/phpoffice/phpword/bootstrap.php");
                require_once(ROOT . "library/vendor/phpoffice/phpword/src/PhpWord/Settings.php");
                $Settings = new PhpOffice\PhpWord\Settings;

                define('CLI', (PHP_SAPI == 'cli') ? true : false);
                define('EOL', CLI ? PHP_EOL : '<br />');
                define('SCRIPT_FILENAME', basename($_SERVER['SCRIPT_FILENAME'], '.php'));
                define('IS_INDEX', SCRIPT_FILENAME == 'index');

                $Settings::loadConfig();
//die('test');
// Set writers
                $writers = array('Word2007' => 'docx');

                // Turn output escaping on
                $Settings::setOutputEscapingEnabled(true);

// Return to the caller script when runs by CLI
                if (CLI) {
                    return;
                }
                //$pageTitle = $diagnostic['name'];
                //$pageHeading = "<h1>{$pageHeading}</h1>";

                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                $phpWord->setDefaultFontName('Cambria');
                $phpWord->setDefaultFontSize(11);
                $phpWord->addTitleStyle(1, array('size' => 22, 'bold' => true), array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
                //$section->addPageBreak();
                $kpas = $this->db->array_col_to_key($diagnosticModel->getKpasForDiagnosticLang($diagnostic_id,$langId), "kpa_instance_id");
                $kqs = $this->db->array_grouping($diagnosticModel->getKeyQuestionsForDiagnosticLang($diagnostic_id,$langId), "kpa_instance_id", "key_question_instance_id");
                $cqs = $this->db->array_grouping($diagnosticModel->getCoreQuestionsForDiagnosticLang($diagnostic_id,$langId), "key_question_instance_id", "core_question_instance_id");
                $jss = $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForDiagnosticLang($diagnostic_id,$langId), "core_question_instance_id", "judgement_statement_instance_id");
                $header_kpa = array('bold' => true);
                $header_js = array();
                $numToAlph = array(1 => "a", 2 => "b", 3 => "c", 4 => "d");

                $section = $phpWord->addSection(array('orientation' => 'landscape', 'pageNumberingStart' => 1));

                $dig_img = $diagnosticModel->getDiagnosticImage($diagnostic_id);

                if ($dig_img[0]['file_name'] != '' && file_exists(UPLOAD_URL_DIAGNOSTIC . "" . $dig_img[0]['file_name'])) {
                    $textrun = $section->addTextRun('pstyle');
                    $textrun->addImage(UPLOAD_URL_DIAGNOSTIC . "" . $dig_img[0]['file_name'], array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'width' => 180));
                    $textrun->addText("                                                                                                              ");
                    $textrun->addImage(SITEURL . "public/images/logo.png", array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT));
                } else {
                    $section->addImage(SITEURL . "public/images/logo.png", array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT));
                }

                $section->addTitleStyle(1, array());
                $section->addTitle($diagnostic['name']);
                $section->addText("");
                $footer = $section->addFooter();

                $table_footer = $footer->addTable(array('width' => 50 * 100, 'unit' => 'pct'));
                $table_footer->addRow();
                $cell = $table_footer->addCell(4500);
                $cell->addPreserveText('© Bespoke Solutions – Licensed to Adhyayan Quality Education Services Private Limited', array('size' => 8, 'valign' => 'center'), array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
                $cell1 = $table_footer->addCell(500);
                $cell1->addPreserveText('{PAGE}', array('size' => 8), array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT));
                foreach ($kpas as $kpa_id => $kpa) {


                    $kq_count = 0;
                    $cq_count = 0;
                    foreach ($kqs[$kpa_id] as $kq_id => $kq) {
                        $kq_count++;
                        $section->addText($kpa['kpa_name'] . ': Key Question ' . $kq_count . ' : ' . $kq['key_question_text'], $header_kpa);

                        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
                        $cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
                        $cellHLeft = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT);
                        $fancyTableStyle = array('borderSize' => 6, 'cellMargin' => 50, 'width' => 5000, 'cantSplit' => true);
                        $cellColSpan_rt = array('gridSpan' => 4, 'valign' => 'center');
                        $spanTableStyleName = 'Colspan Rowspan';
                        $cellRowContinue = array('vMerge' => 'continue');
                        $cellVCentered = array('valign' => 'center');


                        foreach ($cqs[$kq_id] as $cq_id => $cq) {

                            $cq_count++;
                            $jss_count = 1;
                            $phpWord->addTableStyle($spanTableStyleName, $fancyTableStyle);
                            $table = $section->addTable($spanTableStyleName);
                            $table->addRow(null, array());
                            $cell0 = $table->addCell(150, $cellRowSpan);
                            $textrun0 = $cell0->addTextRun($cellHLeft);
                            $textrun0->addText($cq_count, $header_kpa);

                            $cell1 = $table->addCell(8000, $cellRowSpan);
                            $textrun1 = $cell1->addTextRun($cellHLeft);
                            $textrun1->addText(htmlspecialchars($cq['core_question_text']), $header_kpa);

                            $cell2 = $table->addCell(4000, $cellColSpan_rt);
                            $textrun2 = $cell2->addTextRun($cellHCentered);
                            $textrun2->addText('Ratings: Please tick any one', $header_kpa);

                            $table->addCell(4000, $cellRowSpan)->addText('Evidence to support the rating', $header_kpa, $cellHCentered);

                            $table->addRow(null, array('cantSplit' => true));
                            $table->addCell(null, $cellRowContinue);
                            $table->addCell(null, $cellRowContinue);
                            $table->addCell(1000, $cellVCentered)->addText('Always', $header_kpa, $cellHCentered);
                            $table->addCell(1000, $cellVCentered)->addText('Mostly', $header_kpa, $cellHCentered);
                            $table->addCell(1000, $cellVCentered)->addText('Sometimes', $header_kpa, $cellHCentered);
                            $table->addCell(1000, $cellVCentered)->addText('Rarely', $header_kpa, $cellHCentered);
                            $table->addCell(null, $cellRowContinue);
                            foreach ($jss[$cq_id] as $js_id => $js) {

                                $table->addRow(null, array('cantSplit' => true));
                                $cell0 = $table->addCell(150, $cellRowSpan);
                                $textrun0 = $cell0->addTextRun($cellHCentered);
                                $textrun0->addText($cq_count . $numToAlph[$jss_count], $header_js);

                                $cell1 = $table->addCell(8000, $cellRowSpan);
                                $textrun1 = $cell1->addTextRun($cellHLeft);
                                $textrun1->addText(htmlspecialchars($js['judgement_statement_text']), $header_js);

                                $table->addCell(1000);
                                $table->addCell(1000);
                                $table->addCell(1000);
                                $table->addCell(1000);
                                $table->addCell(4000, $cellRowSpan);

                                $jss_count++;
                            }
                            $section->addText("");
                        }
                    }
                }
// Save file
                $diag_name = str_replace(' ', '_', $diagnostic['name']);
                //$diag_name = str_replace('-', '', $diag_name);
                //$diag_name = str_replace('__', '_', $diag_name);
                echo write($phpWord, $diag_name, $writers);
                $targetFile = ROOT . "".DOWNLOAD_DIAGNOSTIC."" . $diag_name . ".docx";
                //echo file_get_contents($temp_file);3
                header("Content-Disposition: attachment; filename=" . $diag_name . ".docx");
                readfile($targetFile); // or echo file_get_contents($temp_file);
                unlink($targetFile);  // remove temp file
            } else
                $this->_notPermitted = 1;
        } else
            $this->_is404 = 1;
    }

   function cloneDiagnosticAction(){
        $langId = empty($_GET['langId']) ? DEFAULT_LANGUAGE : $_GET['langId'];
    	//to be accessible if the user has manage_diagnostic rights-currently adhyayan admin and superadmin
    	if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
    		$this->_notPermitted = 1;
    		elseif (in_array("manage_diagnostic", $this->user['capabilities'])) {
    			$diagnostic_id = empty($_GET['diagnostic_id']) ? 0 : $_GET['diagnostic_id'];
    			$diagnosticModel = new diagnosticModel();
    			$diagData = $diagnosticModel->getDiagnosticBYLang($diagnostic_id,$langId);
    			if($diagData['isPublished']!=1)
    				$this->_notPermitted = 1;
    			else{
    				$this->set('diagnosticName',$diagData['name']);
    				$this->set('diagnosticId',$diagnostic_id);
    				$this->set('diagnosticType',$diagData['assessment_type_name']);
                                $this->set('langId',$langId);
    				//$this->_template->addHeaderScript('diagnostic.js');
    			}
    		} else
    			$this->_notPermitted = 1;
    }




    function keyrecommendationsAction(){
    	$assessment_id = empty($_GET['assessment_id']) ? 0 : $_GET['assessment_id'];
    	$lang_id = empty($_GET['lang_id']) ? DEFAULT_LANGUAGE : $_GET['lang_id'];
    	$type = empty($_GET['type']) ? 0 : $_GET['type'];
    	$instance_id = empty($_GET['instance_id']) ? 0 : $_GET['instance_id'];
    	$assessor_id = empty($_GET['assessor_id']) ? 0 : $_GET['assessor_id'];
    	$external= empty($_GET['external']) ? 0 : $_GET['external'];
    	$is_collaborative = empty($_GET['is_collaborative']) ? 0 : $_GET['is_collaborative'];
    	$kpa7id = empty($_GET['kpa7id']) ? 0 : $_GET['kpa7id'];
    	$isAdmin = in_array("view_all_assessments", $this->user['capabilities']) ? true : false;
    	$isNetworkAdmin = in_array("view_own_network_assessment", $this->user['capabilities']) && $this->user['network_id'] > 0 ? true : false;
    	$isSchoolAdmin = in_array("view_own_institute_assessment", $this->user['capabilities']) ? true : false;
    	$diagnosticModel = new diagnosticModel();
    	if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
    		$this->_notPermitted = 1;
    		elseif ($assessment_id > 0 && $assessment = $diagnosticModel->getAssessmentByUser($assessment_id,$assessor_id,$lang_id,$external)) {
    			$assessor_id = $assessment['user_id'];
    			//print_r($assessment);
//    			if ($assessment['aqs_status'] != 1) {
//    				$this->_notPermitted = 1;
//    			} else 
                            if ($assessor_id == $this->user['user_id'] || ($assessment['status'] == 1 && ( $isAdmin || ($isSchoolAdmin && $assessment['client_id'] == $this->user['client_id'] && $assessment['role'] == 3) || ($isNetworkAdmin && $assessment['network_id'] == $this->user['network_id'] && $assessment['role'] == 3)
    					)
    					)
    					) {
    							
    
    						$isReadOnly = $assessment['report_published'] == 1 || ($assessment['status'] == 1 && !in_array("edit_all_submitted_assessments", $this->user['capabilities'])) ? 1 : 0;
    
    						$this->set("isReadOnly", $isReadOnly);
    						$this->set('type',$type);
    						$this->set('instance_id',$instance_id);
    						$diagnosticModel = new diagnosticModel();
    						$this->set("diagnosticModel", $diagnosticModel);
    							
    						$this->set("akns", $diagnosticModel->getAssessorKeyNotesForType($assessment_id, $type, $instance_id));
    						$this->set("assessment_id", $assessment_id);
    						$this->set("assessment", $assessment);
    						$source_link = '';
    						switch(strtolower($type)){
    							case 'kpa':$source_link = 'kpa'; $tab_type_kn = 'kpa';
    							break;
    							case 'key_question': $source_link = 'kq'; $tab_type_kn = 'keyQ';
    							break;
    							case 'core_question':$source_link = 'cq'; $tab_type_kn = 'coreQ';
    							break;
    							case 'judgement_statement':$source_link = 'js'; $tab_type_kn ='judgementS';
    						}
                                                $this->set('kpa7',"0");
                                                if($kpa7id == $instance_id){
                                                    $this->set('kpa7',"1");
                                                }
    						$this->set('tab_type_kn',$tab_type_kn);
    						$this->set('sourceLink',$source_link);
    						$this->set("isAdmin", $isAdmin);
    						$this->set("isNetworkAdmin", $isNetworkAdmin);
    						$this->set("isSchoolAdmin", $isSchoolAdmin);
                                                $this->set("lang_id", $lang_id);
    						$this->set("external", $external);
    						$this->set("is_collaborative", $is_collaborative);
    						$this->set("assessor_id", $assessor_id);
    						$this->_template->addHeaderStyle('bootstrap-select.min.css');
    						$this->_template->addHeaderStyle('assessment-form.css');
    						$this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
			                //$this->_template->addHeaderScript('bootstrap-multiselect-0.9.13.js');
			                $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
			                //$this->_template->addHeaderStyle('bootstrap-multiselect-0.9.13.css');   
    						
                                                
                                                $this->_template->addHeaderStyle('bootstrap-multiselect.css');
                                                $this->_template->addHeaderScript('bootstrap-multiselect.js');
                                                $this->_template->addHeaderScript('assessment.js');
                                                $this->_template->addHeaderScript('assessment_rec.js');
    					} else
    						$this->_notPermitted = 1;
    		}
    }
    
    function goodlookslikeAction(){      
    	$assessment_id = empty($_GET['assessment_id']) ? 0 : $_GET['assessment_id'];
    	$lang_id = empty($_GET['lang_id']) ? DEFAULT_LANGUAGE : $_GET['lang_id'];
    	$type = empty($_GET['type']) ? 0 : $_GET['type'];
    	$instance_id = empty($_GET['instance_id']) ? 0 : $_GET['instance_id'];
    	$assessor_id = empty($_GET['assessor_id']) ? 0 : $_GET['assessor_id'];
    	$external= empty($_GET['external']) ? 0 : $_GET['external'];
    	$is_collaborative = empty($_GET['is_collaborative']) ? 0 : $_GET['is_collaborative'];
    	$isAdmin = in_array("view_all_assessments", $this->user['capabilities']) ? true : false;
    	$isNetworkAdmin = in_array("view_own_network_assessment", $this->user['capabilities']) && $this->user['network_id'] > 0 ? true : false;
    	$isSchoolAdmin = in_array("view_own_institute_assessment", $this->user['capabilities']) ? true : false;
    	
        $diagnosticModel = new diagnosticModel();
        
        $js_instance_id=$_GET['instance_id']; //echo '<br>';
        $sqljs="select * FROM h_cq_js_instance where judgement_statement_instance_id in($js_instance_id)";
        $resjs=$this->db->get_results($sqljs);
        //echo '<pre>';print_r($resjs);
        
        foreach($resjs as $resultjs){
            $js_id_arr=$resultjs['judgement_statement_id'];
            $sqljs1="select * FROM d_judgement_statement where judgement_statement_id in ($js_id_arr)";
            $resjs1=$this->db->get_results($sqljs1);
            $js_statement_text[]= $resjs1[0]['judgement_statement_text1'];
            $js_id[]= $resjs1[0]['judgement_statement_id'];
            
            
        }  //echo '<pre>';print_r($resjs1);
   
            $js_ids=implode(',' , $js_id); 
            //echo '<pre>';print_r($js_id);
        
        
        $sql_new="select a.translation_text,e.translation_text,b.judgement_statement_text1 
            from h_lang_translation a
            inner join d_judgement_statement b on a.equivalence_id=b.equivalence_id
            inner join h_js_mostly_statements c on b.judgement_statement_id=c.judgement_statement_id
            inner join d_good_look_like_statement d on c.mostly_statements_id=d.good_looks_like_statement_id
            inner join h_lang_translation e on d.equivalence_id=e.equivalence_id
            inner join h_cq_js_instance f on c.judgement_statement_id=f.judgement_statement_id
            where b.judgement_statement_id IN ($js_ids) and judgement_statement_instance_id IN ($js_instance_id) and a.language_id=9 and e.language_id=9";
        $res_new=$this->db->get_results($sql_new); //print_r($res_new);
        $this->set("res_new", $res_new);
        
        //$this->set("mostly_statements1", str_replace(array('*','Mostly:'),array('<br><span>-</span>',''),$res_new[0]['translation_text'])); 
        //$this->set("mostly_statements2", str_replace('*','<br>',$res_new[1]['translation_text'])); 
        //$this->set("judgement_statements", str_replace('*','<br>',$res_new[0]['judgement_statement_text1']));
         $this->set("noHeader","1");
         $this->_template->clearHeaderFooter();
        
    }
    
    function createOption(array &$introductory_question, $parent_id = 0) {

        $branch = array();
        foreach ($introductory_question as $element) {
            if ($element['parent_id'] == $parent_id) {

                //$directoryFile = $this->getDirectoryFiles($element['directory_id'], $fileList);
                $children = $this->createOption($introductory_question, $element['q_id']);
                if ($children) {
                    $element['child_question'] = $children;
                }
                $branch[$element['q_id']] = $element;
            }
        }
        return $branch;
    }
    
    /*
     * function to send feedback notification for Reviews
     */
    function feedbackNotificationAction() {
        
        $assessmentModel = new assessmentModel();
        //echo current($this->user['role_ids']);die;
        $assessments = $assessmentModel->getFeedbackAssessment(current($this->user['role_ids']));
        $body_mail = "Please give feedback for your assessment.";
        $subject = "Assessment Feedback";
        $i = 0;
        foreach($assessments as $data) {
            
            $i++;
            $body_mail = '';
            $link = '';
            $fromEmail = 'deepak.t@tatrasdata.com';
            $fromName = 'Adhyayan';
            $toEmail = 'deepakchauhan89@gmail.com';
            $toName = 'deepakchauhan89@gmail.com';
            $cc = 'vikas@tatrasdata.com';  
            $link = 'http://staging-app.adhyayan.asia/index.php?controller=diagnostic&action=feedbackForm&assessment_id='.$data['assessment_id'].'&user_id='.$data['user_id'];
            $link = "<a href='".$link."'>Click here</a>";
            $body_mail =  'Hi '.$data['name'].",".'<br>'.$body_mail;
            $body_mail .= $link." for feedback.";
            if($i < 5) {
                sendEmail($fromEmail,$fromName,$toEmail,$toName,$subject,$body_mail,$cc);
            }
            
        }
       
       // sendEmail(''.$user_facilitator['email'].'',''.$user_facilitator['name'].'','shraddha.adhyayan@gmail.com','Shraddha Khedekar','Adhyayan:: Workshop - '.$_POST ['workshop_name'].'',$body_mail,'poonam.choksi@adhyayan.asia');
        
        //echo "<pre>";print_r($assessments);
    }

    function resetTableforHindiAction(){
        $diagnosticModel = new diagnosticModel();
        $diagnosticModel->resetTableforHindi();
        //$this->_notPermitted = 1;
       
        return;
    }
    
    function resetStudentAwardAction(){
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", "20000M");
        $diagnosticModel = new diagnosticModel();
        $diagnosticModel->resetDataStudentReviewNewLogic();
        //$this->_notPermitted = 1;
        return;
    }
    
}
