<?php

class diagnosticController extends controller {

    function diagnosticAction() {
        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif (in_array("manage_diagnostic", $this->user['capabilities'])) {
            $order_by = empty($_POST['order_by']) ? "name" : $_POST['order_by'];
            $order_type = empty($_POST['order_type']) ? "asc" : $_POST['order_type'];
            $param = array(
                "name_like" => empty($_POST['name']) ? "" : $_POST['name'],
                "isPublished" => empty($_POST['isPublished']) ? "" : $_POST['isPublished'],
                "assessment_type_id" => empty($_POST['assessment_type_id']) ? "" : $_POST['assessment_type_id'],
                "order_by" => $order_by,
                "order_type" => $order_type,
            );
            $this->set("filterParam", $param);
            $diagnosticModel = new diagnosticModel();
            $this->set("diagnostics", $diagnosticModel->getDiagnostics($param));
            $this->set("orderBy", $order_by);
            $this->set("orderType", $order_type);
            $this->set("assessment_types", $diagnosticModel->getAssessmentTypes());
            
        } else
            $this->_notPermitted = 1;
    }

    function diagnosticFormAction() {
        $diagnostic_id = empty($_GET['id']) ? 0 : $_GET['id'];
        $diagnosticModel = new diagnosticModel();

        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif ($diagnostic_id > 0 && $diagnostic = $diagnosticModel->getDiagnostic($diagnostic_id)) {
            if (in_array("manage_diagnostic", $this->user['capabilities'])) {
                $this->set("kpas", $this->db->array_col_to_key($diagnosticModel->getKpasForDiagnostic($diagnostic_id), "kpa_instance_id"));
                $this->set("kqs", $this->db->array_grouping($diagnosticModel->getKeyQuestionsForDiagnostic($diagnostic_id), "kpa_instance_id", "key_question_instance_id"));
                $this->set("cqs", $this->db->array_grouping($diagnosticModel->getCoreQuestionsForDiagnostic($diagnostic_id), "key_question_instance_id", "core_question_instance_id"));
                $this->set("jss", $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForDiagnostic($diagnostic_id), "core_question_instance_id", "judgement_statement_instance_id"));
                $this->set("diagnostic", $diagnostic);
                $this->set('ddiagnosticId', $diagnostic['diagnostic_id']);
                $this->set('kpaRecommendations',$diagnostic['kpa_recommendations']);
                $this->set('kqRecommendations',$diagnostic['kq_recommendations']);
                $this->set('cqRecommendations',$diagnostic['cq_recommendations']);
                $this->set('jsRecommendations',$diagnostic['js_recommendations']);
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

    function assessmentFormAction() {

    	$assessment_id = empty($_GET['assessment_id']) ? 0 : $_GET['assessment_id'];
    	$assessor_id = empty($_GET['assessor_id']) ? $this->user['user_id'] : $_GET['assessor_id'];
    	$isAdmin = in_array("view_all_assessments", $this->user['capabilities']) ? true : false;
    	$isNetworkAdmin = in_array("view_own_network_assessment", $this->user['capabilities']) && $this->user['network_id'] > 0 ? true : false;
    	$isSchoolAdmin = in_array("view_own_institute_assessment", $this->user['capabilities']) ? true : false;
    	$diagnosticModel = new diagnosticModel();
    	if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
    		$this->_notPermitted = 1;
    		elseif ($assessment_id > 0 && $assessor_id > 0 && $assessment = $diagnosticModel->getAssessmentByUser($assessment_id, $assessor_id)) {
    			$this->set('ddiagnosticId', $assessment['diagnostic_id']);
    			$diagData = $diagnosticModel->getDiagnosticName($assessment['diagnostic_id']);
    			$this->set('diagData',$diagData[0]);
    			if ($assessment['aqs_status'] != 1) {
    				$this->_notPermitted = 1;
    			} else if ($assessor_id == $this->user['user_id'] || ($assessment['status'] == 1 && ( $isAdmin || ($isSchoolAdmin && $assessment['client_id'] == $this->user['client_id'] && $assessment['role'] == 3) || ($isNetworkAdmin && $assessment['network_id'] == $this->user['network_id'] && $assessment['role'] == 3)
    					)
    					)
    					) {
    						$subAssessmentType = empty($assessment['subAssessmentType']) ? 0 : $assessment['subAssessmentType'];
    						$assessmentModel = new assessmentModel();
    						$subAssessmentType == 1 ? ($isApproved = $assessment['isApproved']) : '';
    	
    						if ($subAssessmentType == 1 && ($isApproved == 0 || $isApproved == 2) && !in_array("edit_all_submitted_assessments", $this->user['capabilities'])) {
    							$this->_notPermitted = 1;
    							return;
    						}
    	
    						$isReadOnly = $assessment['report_published'] == 1 || ($assessment['status'] == 1 && !in_array("edit_all_submitted_assessments", $this->user['capabilities'])) ? 1 : 0;
    	
    						$this->set("isReadOnly", $isReadOnly);
    						$this->set("kpas", $this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $assessor_id), "kpa_instance_id"));
    						$this->set("kqs", $this->db->array_grouping($diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $assessor_id), "kpa_instance_id", "key_question_instance_id"));
    						$this->set("cqs", $this->db->array_grouping($diagnosticModel->getCoreQuestionsForAssessment($assessment_id, $assessor_id), "key_question_instance_id", "core_question_instance_id"));
    						$this->set("jss", $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForAssessment($assessment_id, $assessor_id), "core_question_instance_id", "judgement_statement_instance_id"));
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
    						$this->set("assessment_id", $assessment_id);
    						$this->set("assessor_id", $assessor_id);
    						$this->set("assessment", $assessment);
    	
    						$this->set("isAdmin", $isAdmin);
    						$this->set("isNetworkAdmin", $isNetworkAdmin);
    						$this->set("isSchoolAdmin", $isSchoolAdmin);
    	
    						$dig_image = $diagnosticModel->getDiagnosticImage($assessment['diagnostic_id']);
    						$image_name = $dig_image[0]['file_name'];
    						$this->set("image_name", $image_name);
    	
    						$this->_template->addHeaderStyle('bootstrap-select.min.css');
    						$this->_template->addHeaderStyle('assessment-form.css');
    						$this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
    						$this->_template->addHeaderScript('bootstrap-multiselect-0.9.13.js');
    						$this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
    						$this->_template->addHeaderStyle('bootstrap-multiselect-0.9.13.css');
    						//$this->_template->addHeaderScript('bootstrap-select.min.js');
    						$this->_template->addHeaderScript('assessment.js');
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

        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif ($assmntId_or_grpAssmntId == 0 || $assessment_type_id == 0) {
            $this->_notPermitted = 1;
            return;
        }
        $assessment = $assessment_type_id == 1 ? $diagnosticModel->getAssessmentById($assmntId_or_grpAssmntId) : $diagnosticModel->getGroupAssessmentByGAId($assmntId_or_grpAssmntId);
        //if ashoka changemaker diagnostic(32) is assigned, additional information table has to be filled
        if (!empty($assessment['table_name'])) {
            $aqs_additional_id = !empty($assessment['aqs_additional_id']) ? $assessment['aqs_additional_id'] : 0;
            $this->set('aqs_additional_id', $aqs_additional_id);
            $aqsDataModel = new aqsDataModel();
            include ROOT . DS . 'application' . DS . 'helpers' . DS . 'aqs-additional-tab-forms.php';
            $this->set('form_elements', $form_elements);
            $this->set('school_community_id', $diagnosticModel->getSchoolCommunities());
            $this->set('review_medium_instrn_id', $diagnosticModel->getInstructionMedium($assessment['diagnostic_id']));
            $this->set('additional_data', $aqsDataModel->getAqsAdditionalData($assessment['aqsdata_id'], 'd_aqs_additional_questions'));
            $this->set('aqs_additional_ref', $aqsDataModel->getAqsAdditionalRefTeam($assessment['aqsdata_id']));

            //$aqs_additional_ref
        }
        //if online self-review, check if online payment has been made or offline payment has been received
        $subAssessmentType = empty($assessment['subAssessmentType']) ? 0 : $assessment['subAssessmentType'];
        $assessmentModel = new assessmentModel();
        $subAssessmentType == 1 ? ($isApproved = $assessment['isApproved']) : '';

        if ($subAssessmentType == 1 && ($isApproved == 0 || $isApproved == 2) && !in_array("edit_all_submitted_assessments", $this->user['capabilities'])) {
            $this->_notPermitted = 1;
            return;
        }        
        if ($assessment && ($assessment_type_id == 1 || $assessment['assessmentAssigned'] > 0)) {
            $isNetworkAdmin = in_array("view_own_network_assessment", $this->user['capabilities']) && $assessment['network_id'] == $this->user['network_id'] && $this->user['network_id'] > 0 ? 1 : 0;
            $isSchoolAdmin = in_array("view_own_institute_assessment", $this->user['capabilities']) && $assessment['client_id'] == $this->user['client_id'] ? 1 : 0;
            $isPrincipal = $isSchoolAdmin && in_array(6, $this->user['role_ids']) ? 1 : 0;
            //also check if user is in external team for the review
            $checkExternalTeam = $diagnosticModel->isUserinExternalTeamAssessment($assessment['assessment_id'],$this->user['user_id']);
           // print_r($checkExternalTeam);echo $checkExternalTeam['num'];
            //die;
            if ( ( ($checkExternalTeam = $diagnosticModel->isUserinExternalTeamAssessment($assessment['assessment_id'],$this->user['user_id'])) && $checkExternalTeam['num']>0 && in_array('take_external_assessment',$this->user['capabilities'])) || in_array($this->user['user_id'], $assessment['user_ids']) || in_array("view_all_assessments", $this->user['capabilities']) || $isSchoolAdmin || $isNetworkAdmin) {
                $isReadOnly = 1;
                if ($assessment_type_id == 1) {
                    $isReadOnly = $assessment['report_published'] != 1 && ( ($assessment['aqs_status'] == 1 && in_array("edit_all_submitted_assessments", $this->user['capabilities'])) || ($assessment['aqs_status'] == 0 && ( $assessment['userIdByRole'][3] == $this->user['user_id'] || $isSchoolAdmin || $isNetworkAdmin )
                            )) ? 0 : 1;
                } else {
                    $isReadOnly = ($assessment['aqs_status'] == 1 && in_array("edit_all_submitted_assessments", $this->user['capabilities'])) || ($assessment['aqs_status'] == 0 && ( $assessment['admin_user_id'] == $this->user['user_id'] || $isPrincipal || $isNetworkAdmin)) ? 0 : 1;
                }


                $this->set("isReadOnly", $isReadOnly);

                $this->set("assmntId_or_grpAssmntId", $assmntId_or_grpAssmntId);
                $this->set("assessment_type_id", $assessment_type_id);
                $this->set("assessment", $assessment);

                $aqsDataModel = new aqsDataModel();
                // aqs versions of past-filled Data
                //getSchoolRegionList

                $this->set("medium_instrn_langs", $diagnosticModel->getAllLanguages());
                $this->set("school_region_list", $aqsDataModel->getSchoolRegionList());
                $aqsVersions = $aqsDataModel->getAQSversion($assessment['client_id']);
                $this->set("AQSversions", $aqsVersions);
                $this->set("referrer_list", $aqsDataModel->getReferrerList());
                $this->set("principal", $this->userModel->getPrincipal($assessment['client_id']));
                $this->set("board_list", $aqsDataModel->getBoardList());
                $this->set("school_type_list", $aqsDataModel->getSchoolTypeList());
                $this->set("school_it_support_list", $aqsDataModel->getSchoolItSupportList());
                $this->set("school_class_list", $aqsDataModel->getSchoolClassList());
                $this->set("school_level_list", $aqsDataModel->getSchoolLevelList());
                $this->set("student_type_list", $aqsDataModel->getStudentTypeList());

                $aqs = $aqsDataModel->getAqsData($assmntId_or_grpAssmntId, $assessment_type_id);
                $this->set("aqs", $aqs);
                $team = isset($aqs['id']) ? $aqsDataModel->getAQSTeam($aqs['id']) : array("school" => array());
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
        $assessment_id = empty($_GET['assessment_id']) ? 0 : $_GET['assessment_id'];
        $assessor_id = empty($_GET['assessor_id']) ? $this->user['user_id'] : $_GET['assessor_id'];
        $isAdmin = in_array("view_all_assessments", $this->user['capabilities']) ? true : false;
        $isNetworkAdmin = in_array("view_own_network_assessment", $this->user['capabilities']) && $this->user['network_id'] > 0 ? true : false;
        $isSchoolAdmin = in_array("view_own_institute_assessment", $this->user['capabilities']) ? true : false;
        $diagnosticModel = new diagnosticModel();
        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif ($assessment_id > 0 && $assessor_id > 0 && $assessment = $diagnosticModel->getAssessmentByUser($assessment_id, $assessor_id)) {

            $subAssessmentType = empty($assessment['subAssessmentType']) ? 0 : $assessment['subAssessmentType'];
            $assessmentModel = new assessmentModel();
            $subAssessmentType == 1 ? ($isApproved = $assessment['isApproved']) : '';

            if ($subAssessmentType == 1 && ($isApproved == 0 || $isApproved == 2) && !in_array("edit_all_submitted_assessments", $this->user['capabilities'])) {
                $this->_notPermitted = 1;
                return;
            }

            if ($assessor_id == $this->user['user_id'] || ($assessment['status'] == 1 && ( $isAdmin || ($isSchoolAdmin && $assessment['client_id'] == $this->user['client_id'] && $assessment['role'] == 3) || ($isNetworkAdmin && $assessment['network_id'] == $this->user['network_id'] && $assessment['role'] == 3)
                    )
                    )
            ) {
                $kpa_id = empty($_GET['kpa_id']) ? 0 : $_GET['kpa_id'];
                $this->set("kpas", $this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $assessor_id, $kpa_id), "kpa_instance_id"));
                $this->set("kqs", $this->db->array_grouping($diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $assessor_id, $kpa_id), "kpa_instance_id", "key_question_instance_id"));
                $this->set("cqs", $this->db->array_grouping($diagnosticModel->getCoreQuestionsForAssessment($assessment_id, $assessor_id, $kpa_id), "key_question_instance_id", "core_question_instance_id"));
                $this->set("jss", $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForAssessment($assessment_id, $assessor_id, $kpa_id), "core_question_instance_id", "judgement_statement_instance_id"));
                if (isset($assessment) && $assessment['role'] == 4) {
                    $this->set("akns", $this->db->array_grouping($diagnosticModel->getAssessorKeyNotes($assessment_id), "kpa_instance_id", "id"));
                    $this->set("diagnosticModel", $diagnosticModel);
                }
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
            $diagnosticId = empty($_GET['diagnosticId']) ? 0 : $_GET['diagnosticId']; //get diagnostic
            $this->set("assessmentId", $assessmentId); //get type of assessment
            $this->set("diagnosticId", $diagnosticId); //get type of assessment
            $this->set("assessmentType", ''); //get type of assessment
            $this->set("diagnosticName", ''); //get type of assessment
            $this->set("teacherCategory", '');
            $this->set("isDiagnosticPublished", 0);
            if ($diagnosticId > 0) {
                $diagName = $diagnosticModel->getDiagnosticName($diagnosticId);
                $assmtType = $diagnosticModel->getAssessmentTypeById($assessmentId);
                $diagImage = $diagnosticModel->getDiagnosticImage($diagnosticId);
                //print_r($diagImage);
                if ($diagName == '' || $diagName == NULL || !$assmtType) {
                    $this->_is404 = 1;
                    exit;
                }
                $this->set('assessmentType', ($assmtType));
                $isDiagnosticPublished = $diagnosticModel->getDiagnostic($diagnosticId);
                $teacherCategory = $diagnosticModel->getDiagnosticTeacherCategory($diagnosticId);
                $isDiagnosticPublished = $isDiagnosticPublished['isPublished'];
                if($isDiagnosticPublished>0)
                	$this->_notPermitted = 1;
                $this->set("isDiagnosticPublished", $isDiagnosticPublished);
                $this->set('diagnosticName', $diagName[0]['name']);
                $this->set('kpaRecommendations',$diagName[0]['kpa_recommendations']);
                $this->set('kqRecommendations',$diagName[0]['kq_recommendations']);
                $this->set('cqRecommendations',$diagName[0]['cq_recommendations']);
                $this->set('jsRecommendations',$diagName[0]['js_recommendations']);
                $this->set('teacherCategory', $teacherCategory);


                $this->set('kpas', $this->db->array_col_to_key($diagnosticModel->getKpasForDiagnostic($diagnosticId), "kpa_instance_id"));
                $this->set('kqs', $this->db->array_grouping($diagnosticModel->getKeyQuestionsForDiagnostic($diagnosticId), "kpa_instance_id", "key_question_instance_id"));
                $this->set('cqs', $this->db->array_grouping($diagnosticModel->getCoreQuestionsForDiagnostic($diagnosticId), "key_question_instance_id", "core_question_instance_id"));
                $this->set('jss', $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForDiagnostic($diagnosticId), "core_question_instance_id", "judgement_statement_instance_id"));
                $this->set('image_name', $diagImage[0]['file_name']);
                $this->set('imageId', $diagImage[0]['diagnostic_image_id']);
            }
            $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
            $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
            $this->_template->addHeaderScript('bootstrap-multiselect-0.9.13.js');
            $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
            $this->_template->addHeaderStyle('bootstrap-multiselect-0.9.13.css');
            $this->_template->addHeaderScript('diagnostic.js');            
        } else
            $this->_notPermitted = 1;}

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
            $this->set("kpaId", $kpaId);
            $this->set("kqId", $kqId);
            $this->set("cqId", $cqId);
            $this->set("assessmentId", $assessmentId); //get type of assessment
            $this->set("type", $type);
            $this->set("for", 'add_diagnostic_form');
            $this->set("isDiagnosticPublished", 0);

            $diagnosticModel = new diagnosticModel();
            if ($diagnosticId > 0) {
                $isDiagnosticPublished = $diagnosticModel->getDiagnostic($diagnosticId);
                $this->set("dig_image_id", $isDiagnosticPublished['diagnostic_image_id']);

                $isDiagnosticPublished = $isDiagnosticPublished['isPublished'];
                $this->set("isDiagnosticPublished", $isDiagnosticPublished);
            }
            //echo $type."tt";
            //if($type=='kpa')
            //$this->set("currQuestions",$diagnosticModel->getKpasForAssessmentType($assessmentId));
            switch ($type) {
                case 'kpa' : $this->set("currQuestions", $diagnosticModel->getKpasForAssessmentType($assessmentId, $diagnosticId));
                    $this->set("formTitle", "KPA");
                    $this->set("diagnosticId", $diagnosticId);
                    $this->set("selectedQuestions", $diagnosticModel->getSelectedKpasForDiagnostic($diagnosticId));
                    $diagImage = $diagnosticModel->getDiagnosticImage($diagnosticId);
                    if (!empty($diagImage)) {
                        $this->set('image_name', isset($diagImage[0]['file_name']) ? $diagImage[0]['file_name'] : '');
                    }
                    break;
                case 'kq' : $this->set("currQuestions", $diagnosticModel->getAllKeyQuestions($diagnosticId));
                    $this->set("formTitle", "Key Question");
                    $this->set("diagnosticId", $diagnosticId);
                    $this->set("selectedQuestions", $diagnosticModel->getSelectedKeyQuestionsForDiagnostic($diagnosticId, $kpaId));
                    break;
                case 'cq' : $this->set("currQuestions", $diagnosticModel->getAllCoreQuestions($diagnosticId));
                    $this->set("formTitle", "Sub Question");
                    $this->set("diagnosticId", $diagnosticId);
                    $this->set("selectedQuestions", $diagnosticModel->getSelectedCoreQuestionsForDiagnostic($diagnosticId, $kpaId, $kqId));
                    break;
                case 'jss' : $this->set("currQuestions", $diagnosticModel->getAllJudgementStatements($diagnosticId));
                    $this->set("formTitle", "Judgement Statement");
                    $this->set("diagnosticId", $diagnosticId);
                    $this->set("selectedQuestions", $diagnosticModel->getSelectedJSSForDiagnostic($diagnosticId, $kpaId, $kqId, $cqId));
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
        }
    }

    function postReviewAction() {
        $isAdmin = in_array("view_all_assessments", $this->user['capabilities']) ? true : false;  
        $isExternalReviewer = in_array("take_external_assessment", $this->user['capabilities']) ? true : false;

        $diagnosticModel = new diagnosticModel();
        $assessment_id = $_GET['assessment_id'];
        $aqsdata_id = $_GET['aqsdata_id'];
        $assessor_id = empty($_GET['assessor_id']) ? $this->user['user_id'] : $_GET['assessor_id'];
        $this->set('assessor_id',$assessor_id);
        $assessment = $diagnosticModel->getAssessmentByUser($assessment_id, $assessor_id);
		if ($assessment['status'] == 1 && ($isAdmin || ($assessor_id == $this->user['user_id'] && $assessment['role'] == 4 && $isExternalReviewer) ) ){
			 $this->set('assessment', $assessment);
			$this->set("assessment_id", $assessment_id);
                        $this->set("diagnostic_id", $assessment['diagnostic_id']);
			$currentData = $diagnosticModel->getPostReviewData($assessment_id);
                        $fields = $diagnosticModel->getCommentsFieldPostReview();
                        $this->set('fields',$fields);
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
        $this->set("actionPlanningData",$actionPlanningData);

			$this->set("schoolLevelList", $schoolLevelList);
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
        $this->_template->addHeaderStyleURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css' );
        $this->_template->addHeaderScriptURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js' );
        $this->_template->addHeaderScript('postreview.js');
    	}
    	else
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
        $diagnosticModel = new diagnosticModel();

        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif ($diagnostic_id > 0 && $diagnostic = $diagnosticModel->getDiagnostic($diagnostic_id)) {
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
                $kpas = $this->db->array_col_to_key($diagnosticModel->getKpasForDiagnostic($diagnostic_id), "kpa_instance_id");
                $kqs = $this->db->array_grouping($diagnosticModel->getKeyQuestionsForDiagnostic($diagnostic_id), "kpa_instance_id", "key_question_instance_id");
                $cqs = $this->db->array_grouping($diagnosticModel->getCoreQuestionsForDiagnostic($diagnostic_id), "key_question_instance_id", "core_question_instance_id");
                $jss = $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForDiagnostic($diagnostic_id), "core_question_instance_id", "judgement_statement_instance_id");
                $header_kpa = array('bold' => true);
                $header_js = array();
                $numToAlph = array(1 => "a", 2 => "b", 3 => "c", 4 => "d");

                $section = $phpWord->addSection(array('orientation' => 'landscape', 'pageNumberingStart' => 1));
                
                $dig_img = $diagnosticModel->getDiagnosticImage($diagnostic_id); 

                if ($dig_img[0]['file_name'] != '' && file_exists(ROOT . "uploads/diagnostic/" . $dig_img[0]['file_name'])) {
                    $textrun = $section->addTextRun('pstyle');
                    $textrun->addImage(SITEURL . "uploads/diagnostic/" . $dig_img[0]['file_name'], array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'width' => 180));
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
                $targetFile = ROOT . "public/wordFile/" . $diag_name . ".docx";
                //echo file_get_contents($temp_file);3
                header("Content-Disposition: attachment; filename=" . $diag_name . ".docx");
                readfile($targetFile); // or echo file_get_contents($temp_file);
                unlink($targetFile);  // remove temp file
            } else
                $this->_notPermitted = 1;
        } else
            $this->_is404 = 1;
    }
    function keyrecommendationsAction(){
    	$assessment_id = empty($_GET['assessment_id']) ? 0 : $_GET['assessment_id'];
    	$type = empty($_GET['type']) ? 0 : $_GET['type'];
    	$instance_id = empty($_GET['instance_id']) ? 0 : $_GET['instance_id'];
    	$assessor_id = empty($_GET['assessor_id']) ? 0 : $_GET['assessor_id'];
    	$isAdmin = in_array("view_all_assessments", $this->user['capabilities']) ? true : false;
    	$isNetworkAdmin = in_array("view_own_network_assessment", $this->user['capabilities']) && $this->user['network_id'] > 0 ? true : false;
    	$isSchoolAdmin = in_array("view_own_institute_assessment", $this->user['capabilities']) ? true : false;
    	$diagnosticModel = new diagnosticModel();
    	if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
    		$this->_notPermitted = 1;
    		elseif ($assessment_id > 0 && $assessment = $diagnosticModel->getAssessmentByUser($assessment_id,$assessor_id)) {
    			$assessor_id = $assessment['user_id'];
    			//print_r($assessment);
    			if ($assessment['aqs_status'] != 1) {
    				$this->_notPermitted = 1;
    			} else if ($assessor_id == $this->user['user_id'] || ($assessment['status'] == 1 && ( $isAdmin || ($isSchoolAdmin && $assessment['client_id'] == $this->user['client_id'] && $assessment['role'] == 3) || ($isNetworkAdmin && $assessment['network_id'] == $this->user['network_id'] && $assessment['role'] == 3)
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
    						$this->set('tab_type_kn',$tab_type_kn);
    						$this->set('sourceLink',$source_link);
    						$this->set("isAdmin", $isAdmin);
    						$this->set("isNetworkAdmin", $isNetworkAdmin);
    						$this->set("isSchoolAdmin", $isSchoolAdmin);
    						$this->_template->addHeaderStyle('bootstrap-select.min.css');
    						$this->_template->addHeaderStyle('assessment-form.css');
    						$this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
			                $this->_template->addHeaderScript('bootstrap-multiselect-0.9.13.js');
			                $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
			                $this->_template->addHeaderStyle('bootstrap-multiselect-0.9.13.css');   
    						$this->_template->addHeaderScript('assessment.js');
    					} else
    						$this->_notPermitted = 1;
    		}
    }
    function cloneDiagnosticAction(){
    	//to be accessible if the user has manage_diagnostic rights-currently adhyayan admin and superadmin
    	if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
    		$this->_notPermitted = 1;
    		elseif (in_array("manage_diagnostic", $this->user['capabilities'])) {
    			$diagnostic_id = empty($_GET['diagnostic_id']) ? 0 : $_GET['diagnostic_id'];
    			$diagnosticModel = new diagnosticModel();
    			$diagData = $diagnosticModel->getDiagnostic($diagnostic_id);
    			if($diagData['isPublished']!=1)
    				$this->_notPermitted = 1;
    			else{
    				$this->set('diagnosticName',$diagData['name']);
    				$this->set('diagnosticId',$diagnostic_id);
    				$this->set('diagnosticType',$diagData['assessment_type_name']);
    				//$this->_template->addHeaderScript('diagnostic.js');
    			}
    		} else
    			$this->_notPermitted = 1;
    }    
}
