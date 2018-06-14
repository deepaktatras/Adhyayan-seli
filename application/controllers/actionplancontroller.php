<?php
class actionplanController extends controller {

    
    function actionplan1Action(){
        
        //echo"dsd";
        $assessment_id = empty($_REQUEST['assessment_id']) ? 0 : $_REQUEST['assessment_id'];
    	$lang_id = empty($_REQUEST['lang_id']) ? DEFAULT_LANGUAGE : $_REQUEST['lang_id'];
    	$type = "recommendation";
        $this->set("assessment_id",$assessment_id);
        $diagnosticModel = new diagnosticModel();
        $assessmentModel = new assessmentModel();
        //$this->set("akns",$diagnosticModel->getAssessorKeyNotesType($assessment_id, $type));
         $aqsDataModel = new aqsDataModel();
        
        $prefferedLanguage = $diagnosticModel->getAssessmentPrefferedLanguage($assessment_id);
        $lang_id = isset($prefferedLanguage['language_id'])?$prefferedLanguage['language_id']:DEFAULT_LANGUAGE;
        $assessment_details=$diagnosticModel->getAssessmentById($assessment_id,$lang_id,1);
        $isSchoolSelfReview = 0;
        if(!empty($assessment_details)){
            
            if($assessment_details['assessment_type_id'] == 1 && $assessment_details['subAssessmentType'] == 1){
                 $isSchoolSelfReview = 1;
            }
        }
        $this->set("isSchoolSelfReview",$isSchoolSelfReview);
        //echo "<pre>";print_r($assessment_details);

        $roleids=$this->user['role_ids'];
        $user_id=$this->user['user_id'];
        
        
        if(in_array("1",$roleids) || in_array("2",$roleids) || in_array("5",$roleids) || in_array("6",$roleids)){
                    $isleader=false;
                    $disabled=0;
                }else if(in_array("3",$roleids)){
                    $isleader=true;
                    $disabled=1;
                }
                
        if( (in_array("1",$roleids) || in_array("2",$roleids)) || (in_array("3",$roleids) || in_array("5",$roleids) || in_array("6",$roleids) && $assessment_details['client_id']==$this->user['client_id'])){
                
       // echo "<pre>";print_r($assessment_details);
        $school_name = $assessment_details['client_name'];
        if(!empty($assessment_details['city'])){
            $school_name .= ",".$assessment_details['city'];
        }
        if(!empty($assessment_details['state'])){
            $school_name .= ",".$assessment_details['state'];
        }
        $this->set("akns",$diagnosticModel->getAssessorKeyNotesTypeOrder($assessment_id, $type,DEFAULT_LANGUAGE, $this->user['role_ids'],$this->user['user_id']));
               
        $designations=$aqsDataModel->getDesignations();
        
        $users=$assessmentModel->getAllSchoolUsers($assessment_details['client_id']);
        
        $this->set("designations",$designations);	
        $this->set("school_name",$school_name);	
        $this->set("assessment_details",$assessment_details);	
        $this->set("users",$users);
        $frequency=$assessmentModel->getfrequency();
        $this->set("frequency",$frequency);
        
        $this->set("assessmentModel",$assessmentModel);
        $this->set("assessment_id",$assessment_id);
        $this->set("isleader",$isleader);
        $this->set("disabled",$disabled);
        
        $this->_template->addHeaderStyle ( 'jquery.mCustomScrollbar.min.css' );
        $this->_template->addHeaderScript ( 'jquery.mCustomScrollbar.concat.min.js' );
        $this->_template->addHeaderStyle ( 'bootstrap-select-n.css' );
        $this->_template->addHeaderScript ( 'bootstrap-select.min-n.js' );
        
        $this->_template->addHeaderStyle('bootstrap-multiselect.css');
        $this->_template->addHeaderScript('bootstrap-multiselect.js');
        $this->_template->addHeaderScript ( 'highcharts.js' );
        $this->_template->addHeaderScript ( 'exporting.js' );
        
        $this->_template->addHeaderScript ( 'actionplan.js' );
        }else{
            $this->_notPermitted=1;
            return;
        }
    }
    
    
    function action1newAction(){
        
        $assessment_id = empty($_REQUEST['assessment_id']) ? 0 : $_REQUEST['assessment_id'];
    	$lang_id = empty($_REQUEST['lang_id']) ? DEFAULT_LANGUAGE : $_REQUEST['lang_id'];
    	$type = "recommendation";
        $this->set("assessment_id",$assessment_id);
        $actionModel = new actionModel();
        $this->set("actionModel",$actionModel);
        $this->set("lang_id",$lang_id);
        
        $kpas = $actionModel->getKpasForAssessment($assessment_id,0,$lang_id);
        $this->set("kpas",$kpas);
        
        //echo"dfdfd";
        $this->_template->addHeaderStyle ( 'jquery.mCustomScrollbar.min.css' );
        $this->_template->addHeaderScript ( 'jquery.mCustomScrollbar.concat.min.js' );
        $this->_template->addHeaderStyle ( 'bootstrap-select-n.css' );
        $this->_template->addHeaderScript ( 'bootstrap-select.min-n.js' );
        $this->_template->addHeaderScript ( 'actionplan.js' );
    }
    
    
    function kparecommendationAction(){
        $diagnosticModel = new diagnosticModel();
        $rec_id=isset($_GET['rec_id'])?$_GET['rec_id']:0;
        
        $recommendation=$diagnosticModel->getAssessorKeyNoteById($rec_id);
        //print_r($recommendation);
        $this->set("recommendation",$recommendation);
        
    }
    
    
    function actionplan2Action(){
        //print_r($_REQUEST);
        $assessment_id = empty($_REQUEST['assessment_id']) ? 0 : $_REQUEST['assessment_id'];
    	$lang_id = empty($_REQUEST['lang_id']) ? DEFAULT_LANGUAGE : $_REQUEST['lang_id'];
    	$type = "recommendation";
        $this->set("assessment_id",$assessment_id);
        $id_c = empty($_REQUEST['id_c']) ? 0 : $_REQUEST['id_c'];
        $this->set("id_c",$id_c);
        $actionModel = new actionModel();
        $diagnosticModel= new diagnosticModel();
        
        $assessment_details=$diagnosticModel->getAssessmentById($assessment_id,$lang_id);
        
        $roleids=$this->user['role_ids'];
        $user_id=$this->user['user_id'];
        if(in_array("1",$roleids) || in_array("2",$roleids) || in_array("5",$roleids) || in_array("6",$roleids)){
                    $isleader=false;
                }else if(in_array("3",$roleids)){
                    $isleader=true;
                }
        if( (in_array("1",$roleids) || in_array("2",$roleids)) || (in_array("3",$roleids) || in_array("5",$roleids) || in_array("6",$roleids) && $assessment_details['client_id']==$this->user['client_id'])){
        
        $details = $actionModel->getDetailsofAssessment($id_c);
        //echo "<pre>";print_r($assessment_details);
        $impactStatements = $actionModel->getDetailsofImpactStmnt($id_c);
        //fetching impact statement data
        $impactStatementsDataRaw = $actionModel->getimpactStmntData($id_c,$assessment_id);
        //echo "<pre>";  print_r($impactStatementsDataRaw);
        if(!empty($impactStatementsDataRaw)) {
            $statementData = array();
            foreach($impactStatementsDataRaw as $data) {
                $statementData[$data['statement_id']][] = $data;

            }
            $this->set("statementData",$statementData);
        }
        $rating_date = '';
        if(!empty($assessment_details)) {
            if($assessment_details['assessment_type_id'] == 1 && $assessment_details['subAssessmentType'] == 1){
                $rating_date = !empty($assessment_details['rating_date'])?date("d-m-Y",strtotime($assessment_details['rating_date'])):'';
            }else{
                $rating_date = !empty($assessment_details['school_aqs_pref_end_date'])?date("d-m-Y",strtotime($assessment_details['school_aqs_pref_end_date'])):'';
            }           
        }
        
        $this->set("methods", $actionModel->getImpactMethod());
        $this->set("rating_date", $rating_date);
        //echo "<pre>";print_r($methodList);
        $this->set("details",$details);
        $this->set("assessment_details",$assessment_details);
        $this->set("impactStatements",$impactStatements);
        $h_assessor_action1_id=isset($details['h_assessor_action1_id'])?$details['h_assessor_action1_id']:'';
        $this->set("h_assessor_action1_id",$h_assessor_action1_id);
        
        $teamDetails=$actionModel->getTeamAction2($h_assessor_action1_id);
        $activityDetails=$actionModel->getActivityAction2($h_assessor_action1_id);
        
        $this->set("teamDetails",$teamDetails);
        $this->set("activityDetails",$activityDetails);
        
        $this->_template->addHeaderStyle ( 'jquery.mCustomScrollbar.min.css' );
        $this->_template->addHeaderScript ( 'jquery.mCustomScrollbar.concat.min.js' );
        $this->_template->addHeaderStyle ( 'bootstrap-select-n.css' );
        $this->_template->addHeaderScript ( 'bootstrap-select.min-n.js' );
        $this->_template->addHeaderStyle('bootstrap-multiselect.css');
        $this->_template->addHeaderScript('bootstrap-multiselect.js');
        $this->_template->addHeaderScript ( 'code/highcharts.js' );
        $this->_template->addHeaderScript ( 'code/modules/exporting.js' );
        $this->_template->addHeaderScript ( 'actionplan.js' );
        //$this->_template->addHeaderScript ( 'http://code.highcharts.com/4.1.9/highcharts.js' );
        //$this->_template->addHeaderScript ( 'http://code.highcharts.com/4.1.9/modules/exporting.js' );
        }else{
            $this->_notPermitted=1;
            return;
        }
    }
    
    
    
    
}
