<?php

class pdfReport extends reportClass{
	
	protected $subAssessmentType;
	protected $awardBreakdown;
        protected $assessmentIdRound2;	
        protected $sameRatingKey = 0;
        protected $improvedRatingKey = 0;
        protected $decreasedRatingKey = 0;
        protected $sameRatingCore = 0;
        protected $improvedRatingCore = 0;
        protected $decreasedRatingCore = 0;
        protected $sameRatingSt = 0;
        protected $improvedRatingSt = 0;
        protected $decreasedRatingSt = 0;
	function __construct($assessment_id,$subAssessmentType,$report_id,$conductedDate='',$validDate=''){
		$this->assessmentId=$assessment_id;
		$this->subAssessmentType = $subAssessmentType;
		$this->sameRatingPercentage = 0;
		parent::__construct($report_id,$conductedDate,$validDate);
	}
	
	public function generateOutput($lang_id=DEFAULT_LANGUAGE,$round=1){
		$this->config['isChangeMaker'] = $this->isChangeMaker();
		switch($this->reportId){
			case 1:$diagId = $this->getDiagnosticId();
                                 $diagType = $this->getDiagnosticType();
                                 $this->config['isCollobrative'] = $this->isCollobrative();
                                if($diagType == 1) {
                                  $this->config['isChildProt']=1 && $this->config['childProtImg']= '.'.DS.'public'.DS.'images'.DS.'diagnostic_adhyayan.png';//for Don-Bosco show logo in reports  
                                }else
				//$diagId==1?($this->config['isCoBranded']=1 && $this->config['coBrandedImg']= '.'.DS.'uploads'.DS.'diagnostic'.DS.'diagnostic_image_'.$diagId.'.jpg'):'';//for Don-Bosco show logo in reports
				$diagId==1?($this->config['isCoBranded']=1 && $this->config['coBrandedImg']= ''.UPLOAD_URL_DIAGNOSTIC.'diagnostic_image_'.$diagId.'.jpg'):'';//for Don-Bosco show logo in reports
				return $this->generateAqsOutput($lang_id);
				break;
			case 2:$diagId = $this->getDiagnosticId();
                                $diagType = $this->getDiagnosticType();
                                if($diagType == 1) {
                                  $this->config['isChildProt']=1 && $this->config['childProtImg']= '.'.DS.'public'.DS.'images'.DS.'diagnostic_adhyayan.png';//for Don-Bosco show logo in reports  
                                }else
				//$diagId==1?($this->config['isCoBranded']=1 && $this->config['coBrandedImg']= '.'.DS.'uploads'.DS.'diagnostic'.DS.'diagnostic_image_'.$diagId.'.jpg'):'';//for Don-Bosco show logo in reports
				$diagId==1?($this->config['isCoBranded']=1 && $this->config['coBrandedImg']= ''.UPLOAD_URL_DIAGNOSTIC.'diagnostic_image_'.$diagId.'.jpg'):'';//for Don-Bosco show logo in reports
				
                                return $this->generate7thKpaAqsOutput();
				break;
                        case 13:$diagId = $this->getDiagnosticId();
                                 $this->config['iscollegeReview']=1;   
				//$diagId==1?($this->config['isCoBranded']=1 && $this->config['coBrandedImg']= '.'.DS.'uploads'.DS.'diagnostic'.DS.'diagnostic_image_'.$diagId.'.jpg'):'';//for Don-Bosco show logo in reports
				return $this->generateCollegeAqsOutput();
				break;    
			case 3:$diagId = $this->getDiagnosticId();
				//$diagId==1?($this->config['isCoBranded']=1 && $this->config['coBrandedImg']= '.'.DS.'uploads'.DS.'diagnostic'.DS.'diagnostic_image_'.$diagId.'.jpg'):'';//for Don-Bosco show logo in reports
				$diagId==1?($this->config['isCoBranded']=1 && $this->config['coBrandedImg']= ''.UPLOAD_URL_DIAGNOSTIC.'diagnostic_image_'.$diagId.'.jpg'):'';//for Don-Bosco show logo in reports
				
                                return $this->generateRecommendationOutput();
				break;
			case 9:
                                return $this->generateStudentAssessmentOutput();
                                break;
                        case 5:
				return $this->generateTeacherAssessmentOutput();
				break;
                        case 7:
				return $this->singleTeacherOutput();
				break;
		}
	}
        
        protected function singleTeacherOutput(){
		$this->loadAqsData();
		$this->loadAwardScheme();
		$this->loadJudgementalStatements();
		$this->loadTeacherData();
		$this->loadCoreQuestions();
		$this->loadKeyQuestions();
		$this->loadKpas();
		$this->loadAward();
                
                
                //$groupeddata['group_assessment_id'];
                
              
		$pdf = new reporttcpdf ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		//$pdf->SetMargins(15, $pdf->top_margin, 15);
		$pdf->footer_text = $this->config['coverAddress'];		
		$pdf->other_footer_text = 'Teacher Performance Review: Recommendation Report of '.$this->teacherInfo['name']['value'].', '.$this->aqsData['school_name'].' '.$this->schoolCity.', '.$this->schoolState.', '.$this->schoolCountry.', '.date("d-m-Y").' (generated on)';//'AQS Single Teacher Recommendation Report, '.$this->teacherInfo['name']['value'].' - '.$this->aqsData['school_name'].', '.date("d-m-Y").' (generated on)';
		$pdf->footerBG = $this->config['footerBG'];
		$pdf->footerColor = $this->config['footerColor'];
		$pdf->footerHeight = $this->config['footerHeight'];
		$pdf->pageNoBarHeight = $this->config['pageNoBarHeight'];
		$pdf->SetTitle(($this->teacherInfo['name']['value']).'-Recommendation Report');
		$pdf->SetHeaderData ( '', '', 'Teacher Performance Review: Recommendation Report' );
		$pdf->setHeaderFont ( Array (
				PDF_FONT_NAME_MAIN,
				'',
				PDF_FONT_SIZE_MAIN
				) );
		$pdf->setFooterFont ( Array (
				PDF_FONT_NAME_DATA,
				'',
				9
				) );
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont ( PDF_FONT_MONOSPACED );
		// set margins
		//$pdf->SetMargins ( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
		$pdf->SetMargins ( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
		$pdf->SetHeaderMargin ( PDF_MARGIN_HEADER );
		$pdf->SetFooterMargin (PDF_MARGIN_FOOTER);
		
		// set auto page breaks
		//$pdf->SetAutoPageBreak ( TRUE, PDF_MARGIN_BOTTOM );
		$pdf->SetAutoPageBreak ( TRUE,PDF_MARGIN_BOTTOM );
		// set image scale factor
		$pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );
		//echo $this->teacherInfo['name']['value'] ;die;
		// set some language-dependent strings (optional)
		if (@file_exists ( dirname ( __FILE__ ) . '/lang/eng.php' )) {
			require_once (dirname ( __FILE__ ) . '/lang/eng.php');
			$pdf->setLanguageArray ( $l );
		}
		
		// set font
		$pdf->SetFont ( 'helvetica', '', 14 );
		$pdf->addPage ();
		//$pdf->SetTextColor ( 255, 255, 255 );
		//$pdf->SetFillColor ( 108, 13, 16 );
                $assessmentModel=new assessmentModel();
                $diagnosticModel=new diagnosticModel();
                $groupeddata=$diagnosticModel->getGroupAssessmentByAssmntId($this->assessmentId);
                $group_assessment_id=isset($groupeddata['group_assessment_id'])?$groupeddata['group_assessment_id']:0;
                $logo_reviewers=$assessmentModel->ExternalAssessorsGrouped($this->aqsData['client_id'],$group_assessment_id);
		$logo_count_reviewers=count($logo_reviewers);
                $firstpagehtml = '';
		if($this->aqsData['client_id']==11)
			$firstpagehtml = '<table class="pdfHdr broad"> <tbody><tr><td class="halfSec fl" align="left"><a href=""><img src="' . SITEURL . 'public/images/logo.png"alt="" style="height:55px;"></a></td><td class="halfSec fr" align="right" ><a href=""><img src="'.SITEURL.'/public/images/shishuvan.jpg" alt=""></a></td></tr>
			<tr><td colspan="2" style="font-weight:bold;text-align:center;font-size:13px;">The Teacher Performance Review has been initiated and validated by '.($this->aqsData['school_name']).'</td></tr></table>';
		else if($this->aqsData['client_id']==27 && $logo_count_reviewers>0)
			$firstpagehtml = '<table class="pdfHdr broad"  style="padding:5px;"> <tbody><tr><td class="halfSec fl" align="left"><a href=""><img src="' . SITEURL . 'public/images/logo.png"alt="" style="height:55px;"></a></td><td class="halfSec fr" align="right" ><a href=""><img src="'.SITEURL.'/public/images/dominicsavio.jpg" alt="" height="70px"></a></td></tr>
			<tr><td colspan="2" style="font-weight:bold;text-align:center;font-size:13px;">The Teacher Performance Review has been initiated and validated by '.($this->aqsData['school_name']).'</td></tr></table>';
		else		 
			$firstpagehtml = '<br/><div style="text-align:center;"><img src="' . SITEURL . 'public/images/logo.png"></div><br/>';
		$firstpagehtml .= <<<EOD
		<div style="background-color:#800000;color:#FFFFFF;text-align:center;"><br/>
		<h2 style="text-align:center">CONTINUOUS PROFESSIONAL DEVELOPMENT: Recommendation Report<br/></h2>
		Recommendations for professional development for<br/><br/><br/>		
		<span style="font-family:dejavuserifb;"><b>{$this->teacherInfo['name']['value']}</b></span>{$this->awardName}<br/><br/><br/>
		Valid until: $this->validDate <br/><br/><br/>
		{$this->aqsData['school_name']}<br/><br/><br/>
		{$this->aqsData['school_address']}		
		<br/><br/><br/>				
		</div>
EOD;
		$pdf->writeHTMLCell ( 0, 0, '', '', $firstpagehtml, 0, 1, 0, true, 'J', true );
		// ---------------------------------------------------------
		// create some content ...
		// add a page
		// first page start
		//award
		$pdf->addPage ();
		$sectionNum = 1;
		$pdf->Bookmark ( $sectionNum.'. Teacher Performance Review: Process', 0, 0, '', 'B', array (
				0,
				64,
				128
		) );
		$pdf->SetFont ( 'times', 'B', 16 );
		$pdf->SetTextColor ( 255, 255, 255 );
		$pdf->SetFillColor ( 108, 13, 16 );
		$pdf->Cell ( 0, 8, ($sectionNum++).'. Teacher Performance Review: Process', 0, 1, 'C', true );
		$pdf->SetFont ( 'times', '', 10 );
		$pdf->SetTextColor ( 0, 0, 0 );
		$pdf->ln(3);
		$html = $this->getPDFreviewProcess();		
		$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
		
		$pdf->addPage ();	
		$pdf->SetFont ( 'times', '', 12 );
		$pdf->Bookmark ( $sectionNum.'. Teacher Performance Review: Grade Definition', 0, 0, '', 'B', array (
				0,
				64,
				128
		) );
		$pdf->SetFont ( 'times', 'B', 16 );
		$pdf->SetTextColor ( 255, 255, 255 );
		$pdf->SetFillColor ( 108, 13, 16 );
		$pdf->Cell ( 0, 8, ($sectionNum++).'. Teacher Performance Review: Grade Definition', 0, 1, 'C', true );
		$pdf->SetFont ( 'times', '', 12 );
		$pdf->SetTextColor ( 0, 0, 0 );		
		$pdf->ln(3);
		$html = $this->getTeacherAwardDefinition();
		$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
		$pdf->AddPage();
		$pdf->Bookmark ( $sectionNum.'. Key for reading the report', 0, 0, '', 'B', array (
				0,
				64,
				128
		) );
		$pdf->SetFont ( 'times', 'B', 16 );
		$pdf->SetTextColor ( 255, 255, 255 );
		$pdf->SetFillColor ( 108, 13, 16 );
		$pdf->Ln(3);
		$pdf->Cell ( 0, 8, ($sectionNum++).'. Key for reading the report', 0, 1, 'C', true );
		$html = '<div style="page-break-inside:avoid;">'.$this->getPDFKeyForReadingReport().'</div>';
		$pdf->SetTextColor ( 0, 0, 0 );
		$pdf->SetFont ( 'times', '', 12 );
		$pdf->Ln(3);
		$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
		//key question wise review
		$pdf->AddPage();
		$pdf->Bookmark ( $sectionNum.'. Teacher Performance Analysis: Key Question wise', 0, 0, '', 'B', array (
				0,
				64,
				128
		) );
		$pdf->SetFont ( 'times', 'B', 16 );
		$pdf->SetTextColor ( 255, 255, 255 );
		$pdf->SetFillColor ( 108, 13, 16 );
		$pdf->Ln(3);
		$pdf->Cell ( 0, 8, ($sectionNum++).'. Teacher Performance Analysis: Key Question wise', 0, 1, 'C', true );
		//create table
		$html = $this->getPDFtchrPerformance();
		//echo $html;die;
		$pdf->SetTextColor ( 0, 0, 0 );
		$pdf->SetFont ( 'times', '', 12 );
		$pdf->Ln(3);
		$pdf->writeHTMLCell ( 0, 0, '', '', 'The following tables depicts the performance of the teacher on every sub-question based on the external review rating. This will help him/her to identify his/her strengths and areas of improvement across the judgement statements and self-navigate his/her professional development journey.<br/>'.$html, 0, 1, 0, true, 'C', true );
		//jd
		$pdf->AddPage();
		$pdf->Bookmark ( $sectionNum.'. Teacher\'s effectiveness in applying the self-review diagnostic', 0, 0, '', 'B', array (
				0,
				64,
				128
		) );
		$pdf->SetFont ( 'times', 'B', 16 );
		$pdf->SetTextColor ( 255, 255, 255 );
		$pdf->SetFillColor ( 108, 13, 16 );
		$pdf->Ln(3);
		$uri = $this->createURISqJd();
		$graph = 'The following graph depicts the levels of agreements and distance between the Self-Review Rating (SRR) and External Review Rating (ERR). <br/>The expectation is that JD=0 and 1 would increase significantly when the teacher does its
 				self-review validation for the second time; and by the third validation, be almost 100%. In
				this manner the teacher would achieve the rigour and objectivity of an external
				reviewer.<br/><div style="text-align:center;font-weight:bold;"><span style="font-weight:normal;">Table '.++$this->tableNum.'</span><br/>Judgement Distance (JD) Gap</div><img style="border:solid 1px #000000;" src="' . SITEURL . 'library/stacked.chart_jd_gen.php?' . $uri . '">';		
		$pdf->Cell ( 0, 8, ($sectionNum++).'. Teacher\'s effectiveness in applying the self-review diagnostic', 0, 1, 'C', true );
		$html = '<div style="page-break-inside:avoid;">'.$graph.'
				</div>';
		$pdf->SetTextColor ( 0, 0, 0 );
		$pdf->SetFont ( 'times', '', 12 );
		$pdf->Ln(3);
		$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );			
		
		//recommendations
		/* $pdf->AddPage();
		$pdf->Bookmark ( '7. Teacher Performance Analysis: Recommendations', 0, 0, '', 'B', array (
				0,
				64,
				128
		) );
		$pdf->SetFont ( 'times', 'B', 16 );
		$pdf->SetTextColor ( 255, 255, 255 );
		$pdf->SetFillColor ( 108, 13, 16 );
		$pdf->Ln(3);
		$pdf->Cell ( 0, 8, 'Teacher Performance Analysis: Recommendations', 0, 1, 'C', true );
		//create table
		$html = $this->teacher_Recomm($this->assessmentId);
		//echo $html;die;
		$pdf->SetTextColor ( 0, 0, 0 );
		$pdf->SetFont ( 'times', '', 12 );
		$pdf->Ln(3);
		$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'L', true ); */
		//teacher_Recomm
		$this->getPDFIndex($pdf);
                $teacher_value=$this->teacherInfo['name']['value'];
                $teacher_value = preg_replace(array('~[^0-9a-z]~i', '~[ -]+~'), ' ', $teacher_value);
                if($_GET['action']=='teacher' && $_GET['report_id']==7){
		$pdf->Output (($this->teacherInfo['name']['value']).'-Recommendation Report'.'.pdf', 'I' );
                }else if($_GET['action']=='reportall' && $_GET['report_id']==7){
                $pdf->Output (''.__DIR__ . '/../'.UPLOAD_PATH.'download_pdf/'.('Single Teacher Recommendation Report '.ucwords($teacher_value)).'.pdf', 'F' );
                }
	}	
	
	protected function generateAqsOutput($lang_id=DEFAULT_LANGUAGE,$round){
                $isCollobrative=$this->config['isCollobrative'];  
                $this->loadAqsData();
		$this->loadAwardScheme();
		$this->loadJudgementalStatements('',$lang_id);
		$this->loadCoreQuestions('',$lang_id);
		$this->loadKeyQuestions('',$lang_id);
		$this->loadKpas('',$lang_id);
		$this->loadAward($lang_id);
                
                $diagnosticLabels=array();
                $languageLabels = $this->getDiagnosticLabels($lang_id);
                foreach($languageLabels as $data) {
                    $diagnosticLabels[$data['label_key']] = $data['label_text'];
                }
		

		/*
		$this->aqsInfo='<div class="bigBold">{schoolName}, {schoolAddress}</div>
		<div class="reportInfo">A compilation of scores based on<br>School Self-Review & Evaluation (SSRE team - School Assessors)'.($this->subAssessmentType!=1?'<br>And<br>School External Review & Evaluation (SERE team - Adhyayan` Assessors)':'N/A').'<br>conducted in: {conductedOn}<br>Valid until: {validTill} </div>';

			
		$this->config['reportTitle']="ADHYAYAN REPORT CARD";
		$this->config["footerText"]="Adhyayan Quality Standard award report for {schoolName}, {dateToday} (generated on)";
	        */
                $this->config['getCreateNetType']=$this->getCreateNetType();
                if($isCollobrative==1){
                 $this->aqsInfo='<div class="bigBold">{schoolName}, {schoolAddress}</div>
		<div class="reportInfo">Collaborative School Review and Evaluation (CSRE team – School and External Assessors)<br>'.str_replace("&",'{conductedOn}',$diagnosticLabels['Conducted_On']).'<br>'.str_replace("&",'{validTill}',$diagnosticLabels['Valid_Until']).' </div>';
      
                }else{
                if($lang_id==9){
                 $this->aqsInfo='<div class="bigBold">{schoolName}, {schoolAddress}</div>
		<div class="reportInfo">'.$diagnosticLabels['Compilation_Scores'].'<br>'.$diagnosticLabels['School_Self_Review_Evaluation_title'].($this->subAssessmentType!=1?'<br>'.$diagnosticLabels['And'].'<br>'.$diagnosticLabels['School_External_Review_Evaluation_title']:'').'<br>'.str_replace("&",'{conductedOn}',$diagnosticLabels['Conducted_On']).'<br>'.str_replace("&",'{validTill}',$diagnosticLabels['Valid_Until']).' </div>';
                  
                }else{
                $this->aqsInfo='<div class="bigBold">{schoolName}, {schoolAddress}</div>
		<div class="reportInfo">'.$diagnosticLabels['School_Self_Review_Evaluation_title'].($this->subAssessmentType!=1?'<br>'.$diagnosticLabels['And'].'<br>'.$diagnosticLabels['School_External_Review_Evaluation_title']:'').'<br>'.$diagnosticLabels['Compilation_Scores'].'<br>'.str_replace("&",'{conductedOn}',$diagnosticLabels['Conducted_On']).'<br>'.str_replace("&",'{validTill}',$diagnosticLabels['Valid_Until']).' </div>';
                }
                }
		$this->config['reportTitle']=$diagnosticLabels['Adhyayan_Report_Card_Title'];
		$this->config["footerText"]="Adhyayan Quality Standard award report for {schoolName}, {dateToday} (generated on)";
	
		$this->sectionArray=array();
		$this->indexArray=array();
		
                if($round==2){
                    $this->generateSection_KeyQuestionRound2('',$lang_id,$diagnosticLabels,0,$isCollobrativeR1,$isCollobrativeR2);
                }
		$this->generateSection_ScoreCardForKPAs('',$lang_id,$diagnosticLabels,$round);
		//self-review waiting for client to send final text
		/*if($this->subAssessmentType==1)
		{
			//$this->loadSameRatingPercentage();
			$this->generateAwardBreakDown();
			$this->loadLastPage();
		}*/
		$this->generateIndexAndCover($diagnosticLabels);
		
		$output=array(
					"config"=>$this->config,
					"data"=>$this->sectionArray
				);
		return $output;
	}
         protected function generateSection_KeyQuestionRound2($skipComparisonSection=0,$lang_id,$diagnosticLabels=array(),$i,$isCollobrativeR1,$isCollobrativeR2){
             
                $totalKpas=count($this->kpas);
		$schemeId = ($this->reportId==5 || $this->reportId==9)? 2:1;
		$comparisonSection=array();
                $isCollobrative=$this->config['isCollobrative'];
                
                //echo $this->assessmentIdRound2;die;
               // $this->loadCoreQuestions('',$lang_id);
               // echo "<pre>";print_r($this->keyQuestions);die;
                if(!empty($this->assessmentIdRound2) && !empty($this->assessmentId)) {
                    
                    //$this->loadKpasRound2('',$lang_id);
                    //echo $this->assessmentId;die;
                   // echo "<pre>";print_r($this->kpas);die;
                    $assessmentKpas = isset($this->kpas[0])?$this->kpas[0]:array();
                    $twoRoundKeyQuestion = $this->loadKeyQuestions('',$lang_id,2);
                    $twoRoundCoreQuestion = $this->loadCoreQuestions('',$lang_id,2);
                    $twoRoundJudgemntStmnt = $this->loadJudgementalStatements('',$lang_id,2);
                   // echo "<pre>";print_r($assessmentKpas);die;
               
                    foreach($assessmentKpas as $kpa_id=>$kpaData){
                       // echo $kpa_id;die;
                        //echo "<pre>";print_r($twoRoundKeyQuestion[$this->assessmentId][$kpa_id]);die;
                        foreach($twoRoundKeyQuestion[$this->assessmentId][$kpa_id] as $key=> $data){
                            
                            //echo $key;
                            //echo '<pre>';print_r($data);
                            $keyQstnInstanceId = $data['key_question_instance_id'];
                            if($data['externalRating']['score'] == $twoRoundKeyQuestion[$this->assessmentIdRound2][$kpa_id][$keyQstnInstanceId]['externalRating']['score']){
                                 //echo $twoRoundKeyQuestion[$this->assessmentIdRound2][$kpa_id][$keyQstnInstanceId]['externalRating']['score'];
                                $this->sameRatingKey++;
                            }else if($data['externalRating']['score'] > $twoRoundKeyQuestion[$this->assessmentIdRound2][$kpa_id][$keyQstnInstanceId]['externalRating']['score']){
                                 $this->improvedRatingKey++;
                            }else if($data['externalRating']['score'] < $twoRoundKeyQuestion[$this->assessmentIdRound2][$kpa_id][$keyQstnInstanceId]['externalRating']['score']){
                                $this->decreasedRatingKey++;
                            }
                            //if($data[$key][$data[]])
                        }
                    }
                    if(!empty($twoRoundCoreQuestion)){
                        foreach($twoRoundCoreQuestion[$this->assessmentId] as $key=>$data ){
                           
                            foreach($data as $corekey=>$coreData ){
                                //echo "<pre>";print_r($coreData);
                                $keyQstnInstanceId = $coreData['key_question_instance_id'];
                                $coreQstnInstanceId = $coreData['core_question_instance_id'];
                                if($coreData['externalRating']['score'] == $twoRoundCoreQuestion[$this->assessmentIdRound2][$keyQstnInstanceId][$coreQstnInstanceId]['externalRating']['score']){
                                     //echo $twoRoundKeyQuestion[$this->assessmentIdRound2][$kpa_id][$keyQstnInstanceId]['externalRating']['score'];
                                    $this->sameRatingCore++;
                                }else if($coreData['externalRating']['score'] > $twoRoundCoreQuestion[$this->assessmentIdRound2][$keyQstnInstanceId][$coreQstnInstanceId]['externalRating']['score']){
                                    $this->improvedRatingCore++;
                                }else if($coreData['externalRating']['score'] < $twoRoundCoreQuestion[$this->assessmentIdRound2][$keyQstnInstanceId][$coreQstnInstanceId]['externalRating']['score']){
                                    $this->decreasedRatingCore++;
                                }
                            }
                            
                        }
                        
                    }
                    if(!empty($twoRoundJudgemntStmnt)){
                        foreach($twoRoundJudgemntStmnt[$this->assessmentId] as $key=>$data ){
                           
                            foreach($data as $corekey=>$coreData ){
                                //echo "<pre>";print_r($coreData);
                                $jsInstanceId = $coreData['judgement_statement_instance_id'];
                                $coreQstnInstanceId = $coreData['core_question_instance_id'];
                                if($coreData['externalRating']['score'] == $twoRoundJudgemntStmnt[$this->assessmentIdRound2][$coreQstnInstanceId][$jsInstanceId]['externalRating']['score']){
                                     //echo $twoRoundKeyQuestion[$this->assessmentIdRound2][$kpa_id][$keyQstnInstanceId]['externalRating']['score'];
                                    $this->sameRatingSt++;
                                }else if($coreData['externalRating']['score'] > $twoRoundJudgemntStmnt[$this->assessmentIdRound2][$coreQstnInstanceId][$jsInstanceId]['externalRating']['score']){
                                    $this->improvedRatingSt++;
                                }else if($coreData['externalRating']['score'] < $twoRoundJudgemntStmnt[$this->assessmentIdRound2][$coreQstnInstanceId][$jsInstanceId]['externalRating']['score']){
                                    $this->decreasedRatingSt++;
                                }
                            }
                            
                        }
                        
                    }
                    
                }
                //echo  $this->sameRatingCore;die;
                //calculate same ratings
                if(!empty($this->sameRatingKey) || !empty($this->improvedRatingKey) || !empty($this->decreasedRatingKey) ){
                    $file_name = $this->assessmentId."-".$this->assessmentIdRound2."-key-comparison-report.png";
                    $this->createComparisionPieChart($this->sameRatingKey,$this->improvedRatingKey,$this->decreasedRatingKey,$file_name);
                }
                if(!empty($this->sameRatingCore) || !empty($this->improvedRatingCore) || !empty($this->decreasedRatingCore) ){
                    $file_name = $this->assessmentId."-".$this->assessmentIdRound2."-corequstn-comparison-report.png";
                    $this->createComparisionPieChart($this->sameRatingCore,$this->improvedRatingCore,$this->decreasedRatingCore,$file_name);
                }
                if(!empty($this->sameRatingSt) || !empty($this->improvedRatingSt) || !empty($this->decreasedRatingSt) ){
                    $file_name = $this->assessmentId."-".$this->assessmentIdRound2."-judgemntstmnt-comparison-report.png";
                    $this->createComparisionPieChart($this->sameRatingSt,$this->improvedRatingSt,$this->decreasedRatingSt,$file_name);
                }
			
             
         }
         
         protected function createComparisionPieChart($sameRatingKey,$improvedRatingKey,$decreasedRatingKey,$file_name){
                
                $size = 250;
                $character = '{"options":'
                        . '{"chart":{"plotBackgroundColor":null,"plotBorderWidth":null,"plotShadow":false,"type":"pie","width":430,"height":300},'
                        . '"title":{"text":""},'
                        . '"credits":{"enabled":false},'
                        . '"legend": {"fontSize": "15px","fontWeight":"bold","align":"right","layout":"vertical","verticalAlign":"top","x": 15,"y": 110},'
                        . '"tooltip":{"pointFormat":"{series.name}: <b>{point.percentage:.1f}%</b>"},'
                        . '"series":[{"name":"Browser share","data":[{"name":"Same Ratings","y":'.$sameRatingKey.',"sliced":false,"selected":true},'
                        . '["Improve Rating",'.$improvedRatingKey.'],["Decreased Rating",'.$decreasedRatingKey.']]}],'
                        . '"colors":["#4F81BD", "#9BBB59", "#C0504D"],'
                        . '"plotOptions":{"pie":{"size":'.$size.',"allowPointSelect":true,"cursor":"pointer",'
                        . '"dataLabels":{"enabled":true,"format":"{point.percentage:.1f} %","distance":-50,'
                        . '"filter":{"property":"percentage","operator":">","value":4}},"showInLegend":false}}}}';
                $url_new=DOWNLOAD_CHART_URL;
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

                    curl_exec($ch);
                    $fileName = curl_exec($ch); 
                   // echo "ddd".ROOT;die;
                    //$upload_path = trim(ROOT,'library');
                    $s3_upload_url='';
                    //if(isset($daterange) && !empty($daterange)){                
                       $upload_url=ROOT.UPLOAD_PATH."charts/".$file_name;
                       $s3_upload_url="".UPLOAD_PATH."charts/".$file_name;

                    //echo $s3_upload_url;die;
                    file_put_contents($upload_url,$fileName);
                    if(upload_file($s3_upload_url,$upload_url)){                        
                         @unlink($upload_url);
                    }
                }
                 
         }
       
	
        protected function generateCollegeAqsOutput($lang_id=DEFAULT_LANGUAGE){
		$this->loadAqsData();
		$this->loadAwardScheme();
		$this->loadJudgementalStatements();
		$this->loadCoreQuestions();
		$this->loadKeyQuestions();
		$this->loadKpas();
		
                $diagnosticLabels=array();
                $languageLabels = $this->getDiagnosticLabels($lang_id);
                foreach($languageLabels as $data) {
                    $diagnosticLabels[$data['label_key']] = $data['label_text'];
                }
                
		$this->aqsInfo='<div class="bigBold">{schoolName}, {schoolAddress}</div>
		<div class="reportInfo">A compilation of scores based on<br><br>College Self-Review & Evaluation (SSRE team - College Assessors)<br><br>And<br><br>College External Review & Evaluation (SERE team - Adhyayan` Assessors)<br><br>conducted in: {conductedOn}<br><br>Valid until: {validTill} </div>';
	
		$this->config['reportTitle']="ADHYAYAN REPORT CARD";
		$this->config["footerText"]="College Performance Review award report for {schoolName}, {dateToday} (generated on)";
	
		$this->sectionArray=array();
		$this->indexArray=array();
		if(count($this->kpas)){
			$kpa=current($this->kpas);
			reset($this->kpas);
			$this->awardName=isset($kpa['externalRating'])?$kpa['externalRating']['rating']:'';
			$this->generateSection_ScoreCardForKPAs('',$lang_id,$diagnosticLabels);
		}
		$this->generateIndexAndCover($diagnosticLabels);
		$output=array(
					"config"=>$this->config,
					"data"=>$this->sectionArray
				);
		return $output;
	}
        
	protected function generate7thKpaAqsOutput($lang_id=DEFAULT_LANGUAGE){
		$this->loadAqsData();
		$this->loadAwardScheme();
		$this->loadJudgementalStatements(true);
		$this->loadCoreQuestions(true);
		$this->loadKeyQuestions(true);
		$this->loadKpas(true);
		
                $diagnosticLabels=array();
                $languageLabels = $this->getDiagnosticLabels($lang_id);
                foreach($languageLabels as $data) {
                    $diagnosticLabels[$data['label_key']] = $data['label_text'];
                }
                
		$this->aqsInfo='<div class="bigBold">{schoolName}, {schoolAddress}</div>
		<div class="reportInfo">A compilation of scores based on<br><br>School Self-Review & Evaluation (SSRE team - School Assessors)<br><br>And<br><br>School External Review & Evaluation (SERE team - Adhyayan` Assessors)<br><br>conducted in: {conductedOn}<br><br>Valid until: {validTill} </div>';
	
		$this->config['reportTitle']="ADHYAYAN REPORT CARD";
		$this->config["footerText"]="Adhyayan Quality Standard award report for {schoolName}, {dateToday} (generated on)";
	       
                
                /*$this->aqsInfo='<div class="bigBold">{schoolName}, {schoolAddress}</div>
		<div class="reportInfo">'.$diagnosticLabels['School_Self_Review_Evaluation_title'].($this->subAssessmentType!=1?'<br>'.$diagnosticLabels['And'].'<br>'.$diagnosticLabels['School_External_Review_Evaluation_title']:'').'<br>'.$diagnosticLabels['Compilation_Scores'].'<br>'.str_replace("&",'{conductedOn}',$diagnosticLabels['Conducted_On']).'<br>'.str_replace("&",'{validTill}',$diagnosticLabels['Valid_Until']).' </div>';
	
		$this->config['reportTitle']=$diagnosticLabels['Adhyayan_Report_Card_Title'];
		$this->config["footerText"]="Adhyayan Quality Standard award report for {schoolName}, {dateToday} (generated on)";
	        */
                
		$this->sectionArray=array();
		$this->indexArray=array();
		if(count($this->kpas)){
			$kpa=current($this->kpas);
			reset($this->kpas);
			$this->awardName=isset($kpa['externalRating'])?$kpa['externalRating']['rating']:'';
			$this->generateSection_ScoreCardForKPAs('',$lang_id,$diagnosticLabels);
		}
		$this->generateIndexAndCover($diagnosticLabels);
		$output=array(
					"config"=>$this->config,
					"data"=>$this->sectionArray
				);
		return $output;
	}
	
	protected function generateRecommendationOutput(){
            
                $diagnosticLabels=array();
                $languageLabels = $this->getDiagnosticLabels();
                foreach($languageLabels as $data) {
                    $diagnosticLabels[$data['label_key']] = $data['label_text'];
                }
                
		$this->loadAqsData();
		$this->loadAwardScheme();
		$this->loadJudgementalStatements();
		$this->loadCoreQuestions();
		$this->loadKeyQuestions();
		$this->loadKpas();
		$this->loadAward();
		$this->loadAqsTeam();
		$this->loadAssessorKeyNotes();
		$this->loadRecommendations();
		
		$this->aqsInfo='
		<table class="recomCoverBlock"><tr><td>
			<div class="bigText">Adhyayan Quality Standard Report</div><br><br><br><br>
			<div class="mediumText">Recommendations for School Improvement following the award of </div><br><br>
			<div class="mBigText">{awardName}</div><br>
			<div class="">Valid until: {validTill}</div><br><br><br>
			<div class="mediumText">To</div><br><br><br>
			<div class="mBigText">{schoolName}, {schoolAddress}</div><br>
			<div class="">Based on Adhyayan\'s School External Review and Evaluation (SERE)</div>
		</td></tr></table>';
	
		$this->config['reportTitle']="ADHYAYAN QUALITY STANDARD REPORT";
		
		$this->config["footerText"]="Adhyayan Quality Standard award report for {schoolName}, {dateToday} (generated on)";
		
		$this->sectionArray=array();
		$this->indexArray=array();

		$this->adhayayanQualityStandard();
		$this->schoolEffectivenessInApplyingSelfReview();
		$this->assessorKeyNotes();
		$this->aSchoolImplovementJourney();
		$this->keyForReadingRecommendationReport();
		$this->recommendationsOnKpa();
		$this->generateIndexAndCover();
		
		$output=array(
					"config"=>$this->config,
					"data"=>$this->sectionArray
				);
		return $output;
	}
	
	function generateTeacherAssessmentOutput($lang_id=DEFAULT_LANGUAGE){
                
		$this->loadAqsData();
		$this->loadAwardScheme();
		$this->loadJudgementalStatements();
		$this->loadCoreQuestions();
		$this->loadKeyQuestions();
		$this->loadKpas();
		$this->loadTeacherData();
		$this->loadAssessmentObject();
		$this->loadAward();
                $diagnosticLabels=array();
                $languageLabels = $this->getDiagnosticLabels($lang_id);
                foreach($languageLabels as $data) {
                    $diagnosticLabels[$data['label_key']] = $data['label_text'];
                }
                
                $assessmentModel=new assessmentModel();
                $diagnosticModel=new diagnosticModel();
                $groupeddata=$diagnosticModel->getGroupAssessmentByAssmntId($this->assessmentId);
                $group_assessment_id=isset($groupeddata['group_assessment_id'])?$groupeddata['group_assessment_id']:0;
                $logo_reviewers=$assessmentModel->ExternalAssessorsGrouped($this->aqsData['client_id'],$group_assessment_id);
		$logo_count_reviewers=count($logo_reviewers);
                $this->config["schoolName"] = $this->aqsData['school_name'];
		$this->config["isShishuvanTeacherReview"] = $this->aqsData['client_id']==11?1:0;
                $this->config["isDominicSavioTeacherReview"] = ($this->aqsData['client_id']==27 && $logo_count_reviewers>0)?1:0;
		$this->aqsInfo='<div class="reportInfo"><b>A compilation of scores based on<br>Teacher Self-Review & Evaluation<br>&<br>Teacher External Review & Evaluation</b></div>';
	
		$this->config['reportTitle']="ADHYAYAN REPORT CARD";
		$this->config["footerText"]="Teacher Performance Review: Report Card of {teacherName}, {schoolName}, {schoolCity}, {schoolState}, {schoolCountry}, {dateToday} (generated on)";
	
		$this->sectionArray=array();
		$this->indexArray=array();
		$this->generateSection_TchrAwardDefintion();
		$this->generateSection_ScoreCardForKPAs(1,$lang_id,$diagnosticLabels);
		//$this->generateSection_ComGraphForTA();
		//$this->generateSection_TchrRecomm();
		$this->generateIndexAndCover($diagnosticLabels);
		$output=array(
				"config"=>$this->config,
				"data"=>$this->sectionArray
			);
		return $output;
	}
        
        function generateStudentAssessmentOutput($lang_id=DEFAULT_LANGUAGE){
		$this->loadAqsDataStudent();
		$this->loadAwardScheme();
		$this->loadJudgementalStatements();
		$this->loadCoreQuestions();
		$this->loadKeyQuestions();
		$this->loadKpas();
		$this->loadStudentData();
		$this->loadAssessmentObject();
		$this->loadAward();
                
                $diagnosticLabels=array();
                $languageLabels = $this->getDiagnosticLabels($lang_id);
                foreach($languageLabels as $data) {
                    $diagnosticLabels[$data['label_key']] = $data['label_text'];
                }
                
                $this->config["schoolName"] = $this->aqsData['school_name'];
                
                $this->config["fileName_Student"] = !empty($this->studentInfo['student-UID']['value'])?"".$this->studentInfo['student-UID']['value']."":$this->studentInfo['name']['value'];
                
		$this->config["isShishuvanTeacherReview"] = $this->aqsData['client_id']==11?1:0;
                $this->config["isStudentReview"] =1;
		//$this->aqsInfo='<div class="reportInfo"><b>A compilation of scores based on<br>Student Self-Review & Evaluation<br>&<br>Student External Review & Evaluation</b></div>';
                $this->aqsInfo='<div class="reportInfo"><b>A compilation of scores based on<br>Self-Review and Evaluation<br>&<br>External Review and Evaluation</b></div><div class="reportInfo"><b>Conducted in:  {conductedOn}<br>Valid until: {validTill}</b></div><br><br>';
	        
		$this->config['reportTitle']="ADHYAYAN REPORT CARD";
		$this->config["footerText"]="Student Performance Review: Report Card of {teacherName}, {schoolName}, {schoolCity}, {schoolState}, {schoolCountry}, {dateToday} (generated on)";    
                
		$this->sectionArray=array();
		$this->indexArray=array();
		$this->generateSection_StuAwardDefintion();
		$this->generateSection_ScoreCardForKPAs(1,$lang_id,$diagnosticLabels);
		//$this->generateSection_ComGraphForTA();
		//$this->generateSection_TchrRecomm();
		$this->generateIndexAndCover($diagnosticLabels);
                
		$output=array(
				"config"=>$this->config,
				"data"=>$this->sectionArray
			);
                
		return $output;
	}
	
         protected function getAQSRound(){
		$sql = "select aqs_round from d_assessment where assessment_id=?";
		$res = $this->db->get_row($sql,array($this->assessmentId));
		return $res['aqs_round']>0?$res['aqs_round']:0;
	}
        
        protected function isCollobrativeR1($assessmentId){		
			$sql="select * from d_assessment where assessment_id=?";			
			$res = $this->db->get_row($sql,array($assessmentId));
			return $res['iscollebrative']>0?1:0;		
	}
        
        protected function isCollobrativeR2($assessmentId){		
			$sql="select * from d_assessment where assessment_id=?";			
			$res = $this->db->get_row($sql,array($assessmentId));
			return $res['iscollebrative']>0?1:0;		
	}
        protected function getClientId(){
		$sql = "select client_id from d_assessment where assessment_id=?";
		$res = $this->db->get_row($sql,array($this->assessmentId));
		return $res['client_id']>0?$res['client_id']:0;
	}
        
        protected function getRound2AssessmentIds($clId,$dgId){
		$sql = "select SQL_CALC_FOUND_ROWS z.* from ( ( SELECT a.collaborativepercntg as avg,b.isFilled,a.iscollebrative,d.diagnostic_id,dt.group_assessment_id,dt.admin_user_id,dt.student_round as assessment_round,q.is_uploaded,q.percComplete as aqspercent,STR_TO_DATE(q.school_aqs_pref_start_date, '%d-%m-%Y') as aqs_start_date,STR_TO_DATE(q.school_aqs_pref_end_date, '%d-%m-%Y') as aqs_end_date, 0 as assessment_id, dt.assessment_type_id, dt.client_id,dt.creation_date as create_date ,c.client_name ,t.assessment_type_name, cn.network_id, group_concat(b.isFilled order by b.role,a.assessment_id) as statuses, group_concat(distinct b.role order by b.role) as roles, group_concat(b.percComplete order by b.role,a.assessment_id) as percCompletes, group_concat(if(b.ratingInputDate is null,'',date(b.ratingInputDate)) order by b.role,a.assessment_id) as ratingInputDates, group_concat(b.user_id order by b.role,a.assessment_id) as user_ids, group_concat(u.name order by b.role,a.assessment_id) as user_names, q.status as aqs_status,group_concat(concat(r.report_id,'|',r.isPublished,'|',r.publishDate)) as report_data,count(distinct s.assessment_id) as assessments_count, CASE WHEN dt.assessment_type_id = 2 THEN group_concat(if(td.value is null,'',td.value) order by b.role,a.assessment_id) WHEN dt.assessment_type_id = 4 THEN group_concat(if(sd.value is null,'',sd.value) order by b.role,a.assessment_id) END as teacherInfoStatuses, ifnull(a.d_sub_assessment_type_id,0) as 'subAssessmentType', aqsdata_id,p.status as 'post_rev_status',p.percComplete as 'postreviewpercent',a.is_approved as 'isApproved', hlt.translation_text as diagnosticName, '' as externalTeam,'' as externalPercntage,'' as extFilled,'' as kpa,'' as leader_ids,'' as kpa_user FROM 

                d_group_assessment dt left join h_assessment_ass_group s on s.group_assessment_id = dt.group_assessment_id 
                left join `d_assessment` a on a.assessment_id = s.assessment_id 
                left join `h_assessment_user` b on a.assessment_id=b.assessment_id 
                left join d_teacher_data td on td.teacher_id=b.user_id and b.role=3 and td.assessment_id=b.assessment_id and td.attr_id=11 left join d_student_data sd on sd.student_id=b.user_id and b.role=3 and sd.assessment_id=b.assessment_id and sd.attr_id=49 left join `d_client` c on c.client_id=dt.client_id 
                left join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id 
                left join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id && hlt.language_id=a.language_id 
                left join `d_assessment_type` t on dt.assessment_type_id=t.assessment_type_id 
                left join d_user u on u.user_id=b.user_id 
                left join h_client_network cn on cn.client_id=c.client_id 
                left join d_AQS_data q on q.id=a.aqsdata_id 
                left join h_assessment_report r on r.assessment_id=a.assessment_id 
                left join d_assessment_kpa kp on kp.assessment_id=a.assessment_id 
                left join d_post_review p on p.assessment_id=a.assessment_id 
                left join h_client_province cp on cp.client_id = c.client_id
                left join h_province_network pn on pn.network_id = cn.network_id and cp.province_id = pn.province_id where 1=1 && dt.isGroupAssessmentActive=1 && c.is_guest!=1 and a.diagnostic_id = '$dgId' and 1=0 group by dt.group_assessment_id having 1 ) union ( SELECT a.collaborativepercntg as avg,b.isFilled,a.iscollebrative,d.diagnostic_id,0 as group_assessment_id,0 as admin_user_id,a.aqs_round as assessment_round,q.is_uploaded,q.percComplete as aqspercent,STR_TO_DATE(q.school_aqs_pref_start_date, '%d-%m-%Y') as aqs_start_date,STR_TO_DATE(q.school_aqs_pref_end_date, '%d-%m-%Y') as aqs_end_date,a.assessment_id, d.assessment_type_id, a.client_id,a.create_date as create_date ,CONCAT(c.client_name,IF((a.review_criteria IS NOT NULL && a.review_criteria!=''),' - ',''),IF((a.review_criteria IS NOT NULL && a.review_criteria!=''),a.review_criteria,'')) ,t.assessment_type_name, cn.network_id, group_concat(b.isFilled order by b.role) as statuses, group_concat(b.role order by b.role) as roles, group_concat(b.percComplete order by b.role) as percCompletes, group_concat(if(b.ratingInputDate is null,'',date(b.ratingInputDate)) order by b.role) as ratingInputDates, group_concat(b.user_id order by b.role) as user_ids, group_concat(u.name order by b.role) as user_names, q.status as aqs_status, group_concat(concat(r.report_id,'|',r.isPublished,'|',r.publishDate)) as report_data, 1 as assessments_count,'' as teacherInfoStatuses, ifnull(a.d_sub_assessment_type_id,0) as 'subAssessmentType' ,aqsdata_id,p.status as 'post_rev_status',p.percComplete as 'postreviewpercent', a.is_approved as 'isApproved', hlt.translation_text as diagnosticName ,group_concat(distinct ext.user_id) as externalTeam,ext.percComplete as externalPercntage,ext.isFilled as extFilled,group_concat(distinct kp.kpa_instance_id) as kpa,group_concat(haa1.leader) as leader_ids,group_concat(distinct kp.user_id) as kpa_user FROM `d_assessment` a inner join `h_assessment_user` b on a.assessment_id=b.assessment_id inner join `d_client` c on c.client_id=a.client_id inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id inner join d_user u on u.user_id=b.user_id inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id && hlt.language_id=a.language_id left join h_client_network cn on cn.client_id=c.client_id left join d_AQS_data q on q.id=a.aqsdata_id left join h_assessment_report r on r.assessment_id=a.assessment_id left join d_post_review p on p.assessment_id=a.assessment_id left join h_client_province cp on cp.client_id = c.client_id left join h_province_network pn on pn.network_id = cn.network_id and cp.province_id = pn.province_id left join h_assessment_external_team ext on ext.assessment_id = a.assessment_id and ext.user_id = 0 left join assessor_key_notes akn on a.assessment_id=akn.assessment_id && akn.type='recommendation' left join h_assessor_action1 haa1 on akn.id=haa1.assessor_key_notes_id left join d_assessment_kpa kp on kp.assessment_id=a.assessment_id 

                where a.client_id='$clId'  &&  (d.assessment_type_id=1 || d.assessment_type_id=5) && a.isAssessmentActive=1 && c.is_guest!=1 and  a.diagnostic_id = '$dgId' and a.aqs_round IN(1,2)and a.d_sub_assessment_type_id!=1 and d.assessment_type_id=1 group by a.assessment_id having 1 ) ) as z  order by assessment_round asc limit 0,2";
		$res = $this->db->get_results($sql);
		return $res;
	}
	protected function loadAqsData(){
		if(empty($this->aqsData)){
			$sql="
				select a.school_name,ctr.country_name,st.state_name,cty.city_name,sr.region_name,a.principal_name,a.school_address,a.principal_phone_no,STR_TO_DATE(a.school_aqs_pref_end_date, '%d-%m-%Y') as school_aqs_pref_end_date,b.award_scheme_id,date(c.publishDate) as publishDate,date(valid_until) as valid_until,b.tier_id,b.client_id,b.review_criteria
				from d_AQS_data a
				inner join d_assessment b on a.id = b.aqsdata_id
				inner join d_client dc on dc.client_id = b.client_id
				left join d_countries ctr on ctr.country_id = dc.country_id
				left join d_states st on dc.state_id = st.state_id and st.country_id = dc.country_id
				left join d_cities cty on cty.city_id = dc.city_id and cty.state_id = dc.state_id
				left join h_assessment_report c on b.assessment_id = c.assessment_id and c.report_id= $this->reportId
				left join d_school_region sr on sr.region_id = a.school_region_id			
				where b.assessment_id = ?  
				group by a.id;";
			$this->aqsData=$this->db->get_row($sql,array($this->assessmentId));
                        
                        if((isset($this->aqsData) && empty($this->aqsData['school_name'])) || !isset($this->aqsData)){
                                $sql="select dc.client_name as school_name,du.name as principal_name,CONCAT(COALESCE(`street`,''),' ',COALESCE(`addressLine2`,''),', ',COALESCE(cty.city_name,''),', ',COALESCE(st.state_name,'')) as school_address,ctr.country_name,st.state_name,cty.city_name,DATE(b.create_date) as create_date,b.award_scheme_id,date(c.publishDate) as publishDate,date(valid_until) as valid_until,b.tier_id,b.client_id,b.review_criteria
				from d_assessment b
				inner join d_client dc on dc.client_id = b.client_id
				left join d_countries ctr on ctr.country_id = dc.country_id
				left join d_states st on dc.state_id = st.state_id and st.country_id = dc.country_id
				left join d_cities cty on cty.city_id = dc.city_id and cty.state_id = dc.state_id
				left join h_assessment_report c on b.assessment_id = c.assessment_id and c.report_id= $this->reportId 
                                left join d_user du on dc.client_id = du.client_id    
                                left join h_user_user_role dur on du.user_id = dur.user_id && dur.role_id=6
				where  dur.role_id=6 && b.assessment_id = ?  
				group by b.assessment_id;";
                                
                            $this->aqsData=$this->db->get_row($sql,array($this->assessmentId));
                        }
                        
                        if(isset($this->aqsData['school_name']) && empty($this->aqsData['school_name'])){
                        $this->aqsData['school_name']="{School Name}";    
                        }else if(!isset($this->aqsData['school_name'])){
                        $this->aqsData['school_name']="{School Name}";    
                        }
                        
                        if(isset($this->aqsData['principal_name']) && empty($this->aqsData['principal_name'])){
                        $this->aqsData['principal_name']="{Principal Name}";    
                        }else if(!isset($this->aqsData['principal_name'])){
                        $this->aqsData['principal_name']="{Principal Name}";    
                        }
                        
                        if(isset($this->aqsData['school_address']) && empty($this->aqsData['school_address'])){
                        $this->aqsData['school_address']="{School Address}";    
                        }else if(!isset($this->aqsData['school_address'])){
                        $this->aqsData['school_address']="{School Address}";    
                        }
                        
                        if(isset($this->aqsData['region_name']) && empty($this->aqsData['region_name'])){
                        $this->aqsData['region_name']="";    
                        }else if(!isset($this->aqsData['region_name'])){
                        $this->aqsData['region_name']="";    
                        }
                        
                        if(isset($this->aqsData['city_name']) && empty($this->aqsData['city_name'])){
                        $this->aqsData['city_name']="";    
                        }else if(!isset($this->aqsData['city_name'])){
                        $this->aqsData['city_name']="";    
                        }
                        
                        if(isset($this->aqsData['state_name']) && empty($this->aqsData['state_name'])){
                        $this->aqsData['state_name']="";    
                        }else if(!isset($this->aqsData['state_name'])){
                        $this->aqsData['state_name']="";    
                        }
                        
                        if(isset($this->aqsData['country_name']) && empty($this->aqsData['country_name'])){
                        $this->aqsData['country_name']="";    
                        }else if(!isset($this->aqsData['country_name'])){
                        $this->aqsData['country_name']="";    
                        }
                        
                        if(isset($this->aqsData['principal_phone_no']) && empty($this->aqsData['principal_phone_no'])){
                        $this->aqsData['principal_phone_no']="";    
                        }else if(!isset($this->aqsData['principal_phone_no'])){
                        $this->aqsData['principal_phone_no']="";    
                        }
                        
			//$this->conductedDate=(empty($this->aqsData['school_aqs_pref_end_date']) || $this->aqsData['school_aqs_pref_end_date']=="0000-00-00")?$this->conductedDate:implode("-",array_reverse(explode("-",substr($this->aqsData['school_aqs_pref_end_date'],0,7))));
                        //$this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:implode("-",array_reverse(explode("-",substr($this->aqsData['valid_until'],0,7))));
			$this->conductedDate=(empty($this->aqsData['school_aqs_pref_end_date']) || $this->aqsData['school_aqs_pref_end_date']=="0000-00-00")?$this->conductedDate:date("M-Y",strtotime($this->aqsData['school_aqs_pref_end_date']));
			$this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:date("M-Y",strtotime($this->aqsData['valid_until']));
			
                        $this->schoolLocation = $this->aqsData['region_name'];
			$this->schoolCity = $this->aqsData['city_name'];
			$this->schoolState = $this->aqsData['state_name'];
			$this->schoolCountry = $this->aqsData['country_name'];
		}
	}
        protected function loadAqsDataStudent(){
		if(empty($this->aqsData)){
			$sql="
				select dc.client_name as school_name,dc.street as school_address,'' as region_name, ctr.country_name,st.state_name,cty.city_name,b.award_scheme_id,date(c.publishDate) as publishDate,date(valid_until) as valid_until,b.tier_id,b.client_id
				from d_assessment b
				inner join d_client dc on dc.client_id = b.client_id
				left join d_countries ctr on ctr.country_id = dc.country_id
				left join d_states st on dc.state_id = st.state_id and st.country_id = dc.country_id
				left join d_cities cty on cty.city_id = dc.city_id and cty.state_id = dc.state_id
				left join h_assessment_report c on b.assessment_id = c.assessment_id and c.report_id= $this->reportId
							
				where b.assessment_id = ?  ";
                        
			$this->aqsData=$this->db->get_row($sql,array($this->assessmentId));
			
                        //$this->conductedDate=(empty($this->aqsData['school_aqs_pref_end_date']) || $this->aqsData['school_aqs_pref_end_date']=="0000-00-00")?$this->conductedDate:implode("-",array_reverse(explode("-",substr($this->aqsData['school_aqs_pref_end_date'],0,7))));
			//$this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:implode("-",array_reverse(explode("-",substr($this->aqsData['valid_until'],0,7))));
			$this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:date("M-Y",strtotime($this->aqsData['valid_until']));
                        $this->schoolLocation = $this->aqsData['region_name'];
			$this->schoolCity = $this->aqsData['city_name'];
			$this->schoolState = $this->aqsData['state_name'];
			$this->schoolCountry = $this->aqsData['country_name'];
		}
	}
	protected function isChangeMaker(){
		$sql="select count(*) as num from d_assessment where assessment_id=? and diagnostic_id=32";
		$res = $this->db->get_row($sql,array($this->assessmentId));
		return $res['num']>0?1:0;
	}
	protected function loadSameRatingPercentage(){
		$sql="
			drop table if exists temp2_same_rating_in_assessment;
			create temporary table temp2_same_rating_in_assessment
			 SELECT a.client_id,a.diagnostic_id,fs.score_id,fs.judgement_statement_instance_id,fs.assessment_id,
			   if(count(distinct fs.rating_id)=1 and count(fs.rating_id)=2,1,0) as same_rating
	
			 FROM f_score fs inner join d_assessment a on a.assessment_id=fs.assessment_id
	
			 where fs.isFinal=1
			 group by fs.judgement_statement_instance_id, fs.assessment_id;";
			
		if($this->db->query($sql))
		{
			$sql ="select round((sum(if(percentage>50,1,0))/count(client_id))*100,2) as count from
					 (select client_id,group_concat(distinct assessment_id) assessment_ids,sum(same_rating) as same_rating,count(same_rating) total_statements,(sum(same_rating)/count(same_rating))*100 AS 'percentage' from
					 temp2_same_rating_in_assessment temp
					  inner join d_diagnostic diag on diag.diagnostic_id=temp.diagnostic_id
					  where diag.assessment_type_id=1   and diag.diagnostic_id in (2)
	
					 group by client_id) tbl";
			$row = $this->db->get_row($sql);
			$this->sameRatingPercentage = $row['count'];
		}
	}
        
	protected function loadAwardScheme($lang_id=DEFAULT_LANGUAGE){
		if(empty($this->awardSchemes) && $this->aqsData['award_scheme_id']>0){
			$sql="SELECT a.award_id,hlt.translation_text as award_name,s.`order` FROM `h_award_scheme` s 
					inner join d_award a on s.award_id=a.award_id
                                        inner join h_lang_translation hlt on a.equivalence_id = hlt.equivalence_id
					where s.award_scheme_id= ? && hlt.language_id=?
					order by s.`order`";
			$this->awardSchemes=$this->db->array_col_to_key($this->db->get_results($sql,array($this->aqsData['award_scheme_id'],$lang_id)),'order');
		}
	}
	
	protected function loadJudgementalStatements($is7thKpaReport=false,$lang_id=DEFAULT_LANGUAGE,$round=1){
		if(empty($this->judgementStatement) || $round==2){
                    
                        $whrCond = 'a.assessment_id=? and';
                        //$columnCond = '';
                        $params = array($lang_id,$this->assessmentId);
                        if($round == 2){
                            
                            $whrCond = 'a.assessment_id in(?,?) and';
                            $params[] = $this->assessmentIdRound2;
                           // $columnCond = 'g.assessment_id';
                        }
                        $params[]= $lang_id;
			 $sql="
				select a.assessment_id, c.core_question_instance_id,c.judgement_statement_instance_id,hlt.translation_text as judgement_statement_text,role,r.rating,hls.rating_level_order as numericRating
					 from f_score a                                         	
					 inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id 
					 inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
					 inner join d_judgement_statement d on d.judgement_statement_id = c.judgement_statement_id					 
					 inner join d_assessment g on a.assessment_id = g.assessment_id
                                         inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
					 inner join h_kpa_diagnostic h on h.diagnostic_id = g.diagnostic_id and h.kpa_order ".($is7thKpaReport?"=":"<")."7
					 inner join h_kpa_kq i on h.kpa_instance_id = i.kpa_instance_id
					 inner join h_kq_cq j on i.key_question_instance_id = j.key_question_instance_id and j.core_question_instance_id = c.core_question_instance_id					 
					 inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					 inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.rating_id=hls.rating_id and hls.rating_level_id=4
                                         inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					 inner join  (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=? ) r on a.rating_id = r.rating_id and hls.rating_id=r.rating_id			
					 where a.isFinal = 1 and $whrCond hlt.language_id=?
					 order by c.`js_order` asc ;";
			if($round == 2){
                            
                             return $this->getTwoRoundsArray($this->db->get_results($sql,$params),"judgement_statement_instance_id","core_question_instance_id");
                        }else{	
                            $this->judgementStatement=$this->get_section_Array($this->db->get_results($sql,array($lang_id,$this->assessmentId,$lang_id)),"judgement_statement_instance_id","core_question_instance_id");
                        }
		}
	}
	
	protected function loadCoreQuestions($is7thKpaReport=false,$lang_id=DEFAULT_LANGUAGE,$round=1){
		if(empty($this->coreQuestions) || $round==2){
                    
                        $whrCond = 'a.assessment_id=? and';
                        //$columnCond = '';
                        $params = array($lang_id,$this->assessmentId);
                        if($round == 2){
                            
                            $whrCond = 'a.assessment_id in(?,?) and';
                            $params[] = $this->assessmentIdRound2;
                           // $columnCond = 'g.assessment_id';
                        }
                        $params[]= $lang_id;
			$sql="select a.assessment_id ,c.key_question_instance_id,a.core_question_instance_id,hlt.translation_text as core_question_text,r.rating,role,hls.rating_level_order as numericRating
					 from h_cq_score a
					 inner join h_kq_cq c on a.core_question_instance_id = c.core_question_instance_id
					 inner join d_core_question d on d.core_question_id = c.core_question_id
                                         inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
					 inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					 inner join d_assessment g on a.assessment_id = g.assessment_id
					 inner join h_kpa_diagnostic i on i.diagnostic_id = g.diagnostic_id and i.kpa_order ".($is7thKpaReport?"=":"<")."7
					 inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and c.key_question_instance_id = j.key_question_instance_id
					 inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					 inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.d_rating_rating_id=hls.rating_id and hls.rating_level_id=3
                                        inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					 inner join (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=? ) r on a.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id		
					 where $whrCond hlt.language_id=?
					 order by c.`cq_order` asc;";
                        if($round == 2){
                            return  $this->getTwoRoundsArray($this->db->get_results($sql,$params),"core_question_instance_id","key_question_instance_id");
                        }else {
                            $this->coreQuestions=$this->get_section_Array($this->db->get_results($sql,array($lang_id,$this->assessmentId,$lang_id)),"core_question_instance_id","key_question_instance_id");
                        }
		}
	}
	
	protected function loadKeyQuestions($is7thKpaReport=false,$lang_id=DEFAULT_LANGUAGE,$round=1){
		if(empty($this->keyQuestions) || $round==2){
                    
                         $whrCond = 'g.assessment_id=? and';
                        //$columnCond = '';
                        $params = array($lang_id,$this->assessmentId);
                        if($round == 2){
                            
                            $whrCond = 'g.assessment_id in(?,?) and';
                            $params[] = $this->assessmentIdRound2;
                           // $columnCond = 'g.assessment_id';
                        }
                        $params[]= $lang_id;
			$sql="select a.assessment_id,c.kpa_instance_id,a.key_question_instance_id,hlt.translation_text as key_question_text,r.rating,role,hls.rating_level_order as numericRating
					from h_kq_instance_score a
					inner join h_kpa_kq c on a.key_question_instance_id = c.key_question_instance_id
					inner join d_key_question d on d.key_question_id = c.key_question_id	
                                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
					inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					inner join d_assessment g on a.assessment_id = g.assessment_id
					inner join h_kpa_diagnostic i on i.diagnostic_id = g.diagnostic_id and i.kpa_instance_id=c.kpa_instance_id and i.kpa_order ".($is7thKpaReport?"=":"<")."7
					inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.d_rating_rating_id=hls.rating_id and hls.rating_level_id=2
				    inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					inner join (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=?) r on a.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id	
					where $whrCond hlt.language_id=?
					order by c.`kq_order` asc;";
                        if($round == 2){
                              return $this->getTwoRoundsArray($this->db->get_results($sql,$params),"key_question_instance_id","kpa_instance_id");
                         }else{
                            $this->keyQuestions=$this->get_section_Array($this->db->get_results($sql,array($lang_id,$this->assessmentId,$lang_id)),"key_question_instance_id","kpa_instance_id");
                         }
		}
	}
	
	protected function loadKpas($is7thKpaReport=false,$lang_id=DEFAULT_LANGUAGE){
		if(empty($this->kpas)){
			 $sql="select a.kpa_instance_id,hlt.translation_text as KPA_name,r.rating,role,hls.rating_level_order as numericRating
					from h_kpa_instance_score a
					inner join h_kpa_diagnostic c on a.kpa_instance_id = c.kpa_instance_id and c.kpa_order ".($is7thKpaReport?"=":"<")."7
					inner join d_kpa d on d.kpa_id = c.kpa_id
                                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
					inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					inner join d_assessment g on a.assessment_id = g.assessment_id
					inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.d_rating_rating_id=hls.rating_id and hls.rating_level_id=1
				        inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					inner join (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=?) r on a.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id
					where a.assessment_id = ? and hlt.language_id=?
					order by c.`kpa_order` asc;";
			$this->kpas=$this->get_section_Array($this->db->get_results($sql,array($lang_id,$this->assessmentId,$lang_id)),"kpa_instance_id");
                       // echo "<pre>";print_r($this->db->get_results($sql,array($this->assessmentId)));
		}
	}
	
	protected function loadAward($lang_id=DEFAULT_LANGUAGE){
                $isCollobrative=$this->config['isCollobrative'];        
		if($this->subAssessmentType==1)//for self review, no award					
			return $this->awardName='N/A';		
		if(empty($this->awardNo)){//reviews with more than one kpa whether teacher or school
			if(count($this->kpas)>1){
				switch($this->reportId){
					case 1:
					case 2:
					case 3:
						$temp=array();
												
							foreach($this->kpas as $kpa){
								//$temp[]=$kpa['externalRating']['score'];
                                                                if($isCollobrative==1){
                                                                $temp[]=$kpa['internalRating']['score'];    
                                                                }else{
								$temp[]=$kpa['externalRating']['score'];
                                                                }
							}
							$compulsoryKpaScore1=$temp[0]; //We have assumes that L & T KPA are the top two KPAs. So we are hard coding it. We need to find a better way.
							$compulsoryKpaScore2=$temp[1];
							$this->noOfScore1234InKpas=array_count_values($temp);
						
						$this->awardNo=$this->calculateSchoolAssessmentAwardValue($compulsoryKpaScore1,$compulsoryKpaScore2);
						
						$sql="select replace(replace(award_name_template,'<Tier>',standard_name),'<Award>',hlt.translation_text)
							 from d_assessment a
							 inner join h_award_scheme b on a.award_scheme_id = b.award_scheme_id
							 inner join d_award_scheme c on c.award_scheme_id = a.award_scheme_id
							 inner join d_award d on d.award_id = b.award_id
                                                         inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id
							 left join d_tier e on e.standard_id = b.tier_id
							 where assessment_id = ? and hlt.language_id=? and b.order = $this->awardNo;";
						$this->awardName=$this->db->get_var($sql,array($this->assessmentId,$lang_id));
					
						break;
                                        case 9:        
					case 5:	/*$noOf3n4inEachCQ=array();
						$noOf3n4inEachKQ=array();
						foreach($this->kpas as $kpa){
							if(isset($this->keyQuestions[$kpa['kpa_instance_id']])){
								foreach($this->keyQuestions[$kpa['kpa_instance_id']] as $keyQ){
									$count3_4inKQ=0;
									if(isset($this->coreQuestions[$keyQ['key_question_instance_id']])){
										foreach($this->coreQuestions[$keyQ['key_question_instance_id']] as $coreQ){
											$count3_4inCQ=0;
											foreach($this->judgementStatement[$coreQ['core_question_instance_id']] as $statment){
												if($statment['externalRating']['score']>2){
													$count3_4inCQ++;
													$count3_4inKQ++;
												}
											}
											$noOf3n4inEachCQ[]=$count3_4inCQ;
										}
									}
									$noOf3n4inEachKQ[]=$count3_4inKQ;
								}
							}
						}
						$minVal=min($noOf3n4inEachCQ)+1;
						$awards=array(4=>"Outstanding",3=>"Proficient",2=>"Developing",1=>"Emerging",0=>"Emerging");
						if($minVal==1 && min($noOf3n4inEachKQ)==0){
							$minVal=0;
						}
						$this->awardNo=$minVal;
						$this->awardName=$awards[$minVal];*/
                                                $kpa=current($this->kpas);//echo $kpa['externalRating']['rating'];die;
                                                reset($this->kpas);			
                                                $teacherAward = isset($kpa['externalRating'])?$kpa['externalRating']['score']:'';
                                                $this->awardName=$teacherAward<4?' working towards \'Proficiency\'':' awarded \'Proficient\'';
						break;						
				}
			}
			elseif(count($this->kpas)==1){
                                switch($this->reportId){
                                    case 9 : $kpa=current($this->kpas);//echo $kpa['externalRating']['rating'];die;
                                             reset($this->kpas);			
                                             $teacherAward = isset($kpa['externalRating'])?$kpa['externalRating']['score']:'';
                                             //$this->awardName=$teacherAward<4?' working towards \'Proficiency\'':' \'Proficient\'';
                                             $this->awardName=isset($kpa['externalRating'])?$kpa['externalRating']['rating']:'';
                                             break;
                                    case 5 : $kpa=current($this->kpas);//echo $kpa['externalRating']['rating'];die;
                                             reset($this->kpas);			
                                             $teacherAward = isset($kpa['externalRating'])?$kpa['externalRating']['score']:'';
                                             $this->awardName=$teacherAward<4?' working towards \'Proficiency\'':' awarded \'Proficient\'';
                                             break;
                                    case 1 : $kpa=current($this->kpas);
                                              reset($this->kpas);
                                              $this->awardName=isset($kpa['externalRating'])?$kpa['externalRating']['rating']:'';                                                    
                                              break;  
                                }
			}
		}
		
	}
        
	protected function loadAssessorKeyNotes(){
		if(empty($this->keyNotes)){
			$sql="SELECT * FROM assessor_key_notes where assessment_id=? ;";
			$this->keyNotes=$this->db->array_grouping($this->db->get_results($sql,array($this->assessmentId)),"kpa_instance_id");
		}
	}
	
	protected function loadRecommendations(){
		if(empty($this->recommendationText)){
			$sql="select c.judgement_statement_instance_id,hlt.translation_text as recommendation_text,a.rating_id
				from f_score a
				inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id and role = 4 
				inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
				inner join d_judgement_statement d on d.judgement_statement_id = c.judgement_statement_id				
				inner join d_assessment g on a.assessment_id = g.assessment_id
				inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
				inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.rating_id=hls.rating_id and hls.rating_level_id=4
				inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
				inner join d_rating r on a.rating_id = r.rating_id and hls.rating_id=r.rating_id	
				inner join h_jstatement_recommendation h on h.rating_id = a.rating_id and h.judgement_statement_id = d.judgement_statement_id and h.isActive=1
				inner join d_recommendation i on i.recommendation_id = h.recommendation_id
                                inner join h_lang_translation hlt on i.equivalence_id = hlt.equivalence_id
				where a.isFinal = 1 and a.assessment_id = $this->assessmentId
				order by c.`js_order` asc;";
			$this->recommendationText=$this->db->array_grouping($this->db->get_results($sql,array($this->assessmentId)),"judgement_statement_instance_id");
		}
	}
	
	protected function loadAqsTeam(){
		if(empty($this->aqsTeam)){
			$sql="select name,c.designation,email,isInternal
				from d_AQS_data ad
				inner join d_assessment a on a.aqsdata_id=ad.id
				inner join d_AQS_team b on b.AQS_data_id = ad.id
                                left join d_designation c on b.designation_id = c.designation_id
				where a.assessment_id = $this->assessmentId
				group by b.id
				;";
			$this->aqsTeam=$this->db->get_results($sql);
		}
	}
	protected function generateAwardBreakDown(){
		if(empty($this->awardBreakdown)){
			$sql="drop table if exists internalAwards;
					create temporary table internalAwards
					select a.*,replace(replace(award_name_template,'<Award>',award_name),'<Tier>',standard_name) as internal_award_text
							from d_assessment a
							inner join h_award_scheme b on a.award_scheme_id = b.award_scheme_id
							inner join d_award_scheme c on c.award_scheme_id = a.award_scheme_id
							inner join d_award d on d.award_id = b.award_id
							left join d_tier e on e.standard_id = b.tier_id
							where b.order = a.internal_award and d_sub_assessment_type_id!=1;
			
					drop table if exists externalAwards;
					create temporary table externalAwards
					select a.*,replace(replace(award_name_template,'<Award>',award_name),'<Tier>',standard_name) as external_award_text
							from d_assessment a
							inner join h_award_scheme b on a.award_scheme_id = b.award_scheme_id
							inner join d_award_scheme c on c.award_scheme_id = a.award_scheme_id
							inner join d_award d on d.award_id = b.award_id
							left join d_tier e on e.standard_id = b.tier_id
							where b.order = a.external_award and d_sub_assessment_type_id!=1;
			
					drop table if exists finalaward;
					create temporary table finalaward
					select i.internal_award,(select replace(replace(award_name_template,'<Award>',award_name),'<Tier>',standard_name) as Internalaward
							from d_award_scheme a inner join h_award_scheme b on a.award_scheme_id = b.award_scheme_id
							inner join d_award d on d.award_id = b.award_id
							left join d_tier e on e.standard_id = b.tier_id
							where b.order = i.internal_award and b.tier_id is not null and b.award_scheme_id=1) as internalAwardText,i.external_award,(select replace(replace(award_name_template,'<Award>',award_name),'<Tier>',standard_name) as Internalaward
							from d_award_scheme a inner join h_award_scheme b on a.award_scheme_id = b.award_scheme_id
							inner join d_award d on d.award_id = b.award_id
							left join d_tier e on e.standard_id = b.tier_id
							where b.order = ex.external_award and b.tier_id is not null and b.award_scheme_id=1) as externalAwardText,count(*) total from internalAwards i inner join externalAwards ex
					on i.assessment_id=ex.assessment_id
					inner join d_client c on c.client_id=i.client_id and c.client_id=ex.client_id
					where i.assessment_id!=33 and i.internal_award is not null and ex.external_award is not null and client_name not in ('Algorithmic Insight School','Sarabjit','xyz','Testschool','aischool','Adhschool')
					group by ex.external_award,i.internal_award
					;";
			if(!$this->db->query($sql))
				return array();
				$sql = "select internal_award,internalAwardText,group_concat(externalAwardText,':',total) as externalAwardNum,sum(total) as totalAwards from finalaward
					where internal_award='".$this->awardNo."'
					group by internal_award";
				$this->awardBreakdown=$this->db->get_row($sql);
		}
	}
	
	protected function loadAssessmentObject(){
		if(empty($this->assessmentObject)){
			$sql="select a.assessment_id,au.user_id,au.role,date(au.ratingInputDate) as ratingInputDate,u.name as user_name,u.email
					from d_assessment a
					inner join h_assessment_user au on a.assessment_id=au.assessment_id
					inner join d_user u on au.user_id=u.user_id
					where a.assessment_id= $this->assessmentId
				;";
			$this->assessmentObject=$this->db->array_col_to_key($this->db->get_results($sql),"role");
		}
	}
	
	protected function loadTeacherData(){
		if(empty($this->teacherInfo)){
			$assessmentModel=new assessmentModel();
			$this->teacherInfo=$assessmentModel->getTeacherInfo($this->assessmentId);
		}
	}
        
        protected function loadStudentData(){
		if(empty($this->studentInfo)){
			$assessmentModel=new assessmentModel();
			$this->studentInfo=$assessmentModel->getStudentInfo($this->assessmentId);
		}
	}
	
	private function calculateSchoolAssessmentAwardValue($compulsoryKpaScore1,$compulsoryKpaScore2){
		$matrix=new schoolAssessmentAwardMatrix($compulsoryKpaScore1,$compulsoryKpaScore2,$this->noOfScore1234InKpas,$this->aqsData['tier_id']);
		return $matrix->firstLevel();
	}
	
	protected function generateIndexAndCover($diagnosticLabels = array()){
		$sections=array();
		$coverSection=array("sectionBody"=>array());
		$keysToReplace=array("{schoolName}","{schoolAddress}","{conductedOn}","{validTill}","{awardName}","{dateToday}","{schoolLocation}", "{schoolCity}", "{schoolState}", "{schoolCountry}", "{teacherName}");
		if($this->reportId==9){
                $valuesToReplace=array($this->aqsData['school_name'],$this->aqsData['school_address'],$this->conductedDate,$this->validDate,$this->awardName,date("d-m-Y"),$this->schoolLocation,$this->schoolCity,$this->schoolState,$this->schoolCountry,isset($this->studentInfo['name']['value'])?$this->studentInfo['name']['value']:'');
                
                }else{
                $valuesToReplace=array($this->aqsData['school_name'],$this->aqsData['school_address'],$this->conductedDate,$this->validDate,$this->awardName,date("d-m-Y"),$this->schoolLocation,$this->schoolCity,$this->schoolState,$this->schoolCountry,isset($this->teacherInfo['name']['value'])?$this->teacherInfo['name']['value']:'');
                }
                $aqsinfo= str_replace($keysToReplace,$valuesToReplace,$this->aqsInfo);
		$this->config["footerText"]=str_replace( $keysToReplace,$valuesToReplace,$this->config["footerText"]);
		$aqsBlock=array(
				"blockBody"=>array(
					"dataArray"=>array(
						array($aqsinfo)
					)
				),
				"style"=>"coverInfoBlock"
			);
		$coverSection['sectionBody'][]=$aqsBlock;
		
		$indexBlock=array(
					"blockHeading"=>array(
						"data"=>array(
							array("text"=>$diagnosticLabels['INDEX'],"style"=>"greyHead","cSpan"=>3)
						)
					),
					"blockBody"=>array(
						"dataArray"=>array(
							array($diagnosticLabels["SR_No"],$diagnosticLabels['PARTICULARS'],$diagnosticLabels["PAGE_NO"])
						)
					),
				"style"=>"bordered reportIndex"
			);
		foreach($this->indexArray as $k=>$v){
                        /*if($this->reportId==9 && ($k==1 || $k==2) ){
                        if($k==2){
                        $indexBlock["blockBody"]["dataArray"][]=array($k+1,$v,'<span id="indexKey-'.($k).'"></span>');    
                        }else{    
                        $indexBlock["blockBody"]["dataArray"][]=array($k+1,$v,'');
                        }
                        }else{*/            
			$indexBlock["blockBody"]["dataArray"][]=array($k+1,$v,'<span id="indexKey-'.($k+1).'"></span>');
                        //}
		}
		
		switch($this->reportId){
			case 1:
			case 2:
                                if(empty($this->aqsData['review_criteria'])){
				$awardBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels["Principal_Name"],array("text"=>$this->aqsData['principal_name'],"style"=>"textBold")),
								array($diagnosticLabels["Adhyayan_Award"],array("text"=>$this->awardName,"style"=>"blueColor textBold"))
							)
						),
						"style"=>"bordered awardBlock"
					);
                                }else{
                                    $awardBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels["Principal_Name"],array("text"=>$this->aqsData['principal_name'],"style"=>"textBold")),
								array($diagnosticLabels["Adhyayan_Award"],array("text"=>$this->awardName,"style"=>"blueColor textBold")),
                                                                array('Review Criteria',array("text"=>$this->aqsData['review_criteria'],"style"=>"textBold"))
							)
						),
						"style"=>"bordered awardBlock"
					);
                                }
				$coverSection['sectionBody'][]=$awardBlock;
				$coverSection['sectionBody'][]=$indexBlock;
				$sections[]=$coverSection;
			break;
                        case 13:
				$awardBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array("Name of the Principal",array("text"=>$this->aqsData['principal_name'],"style"=>"textBold")),
								array("Career Readiness Review Awarded",array("text"=>$this->awardName,"style"=>"blueColor textBold"))
							)
						),
						"style"=>"bordered awardBlock"
					);
				$coverSection['sectionBody'][]=$awardBlock;
				$coverSection['sectionBody'][]=$indexBlock;
				$sections[]=$coverSection;
			break;
			case 3:
				$sections[]=$coverSection;
				$section=array("sectionHeading"=>array("text"=>"Adhyayan Quality Standard Award Report"),"sectionBody"=>array());
				$indexBlock["style"].=" recomIndex";
				$section['sectionBody'][]=$indexBlock;
				
				if(count($this->aqsTeam)){
					$internalHtml='';
					$externalHtml="";
					foreach($this->aqsTeam as $member){
						$row='<tr><td>'.$member['name'].'</td><td>'.$member['designation'].'</td></tr>';
						if($member['isInternal']==1)
							$internalHtml.=$row;
						else
							$externalHtml.=$row;
					}
					$block=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array(
								'<div class="team-head">SSRE Team Member/s:</div><table class="bordered"><thead><tr><td>Name</td><td>Designation</td></tr></thead>'.$internalHtml.'</table>',
								'<div class="team-head">SERE Team Member/s:</div><table class="bordered"><thead><tr><td>Name</td><td>Designation</td></tr></thead>'.$externalHtml.'</table>'
								)
							)
						),
						"style"=>"border-outer aqsTeam"
					);
					$section['sectionBody'][]=$block;
				}
				$sections[]=$section;
			break;
                        case 9:
                            $coverSection['sectionBody']=array_merge($coverSection['sectionBody'],$this->getStudentInfoBlocks());
                            $indexBlock["style"].=" recomIndex";
			    $coverSection['sectionBody'][]=$indexBlock;
			    $sections[]=$coverSection;
                            
                            break;
			case 5:
				$coverSection['sectionBody']=array_merge($coverSection['sectionBody'],$this->getTeacherInfoBlocks());
				//$coverSection['sectionBody'][]=$indexBlock;
				$sections[]=$coverSection;
			break;
		}
		
		$this->sectionArray=array_merge($sections,$this->sectionArray);
	}
	
	protected function getTeacherInfoBlocks(){
		$blocks=array();
		$block=array(
				"blockBody"=>array(
					"dataArray"=>array(
						array('Candidate Name',$this->teacherInfo['name']['value']),
						array('Designation',$this->teacherInfo['designation']['value']),
						array('Mobile No.',$this->teacherInfo['mobile']['value']),
						array('Email contact',$this->teacherInfo['email']['value'])
					)
				),
				"style"=>"bordered kpablock firstColBold"
			);
		$blocks[]=$block;
		$block=array(
				"blockBody"=>array(
					"dataArray"=>array(
						array("<b>Basic Information about the candidate:<b>")
					)
				)
			);
			
		$blocks[]=$block;
		$block=array(
				"blockBody"=>array(
					"dataArray"=>array(
						array('School Name',$this->aqsData['school_name']),
						array('School Address',$this->aqsData['school_address']),
						array('School Phone No.',$this->aqsData['principal_phone_no']),
						array('Name of the Principal',$this->aqsData['principal_name']),
						array("Your Educational Qualifications",$this->teacherInfo['qualification']['value']),
						array('Total years of teaching experience',($this->teacherInfo['experience']['value']==0?'Less than 1':$this->teacherInfo['experience']['value']).' years'),
						array('Year of joining current school',$this->teacherInfo['joinning_year']['value']),
						array('Position when joined the school',$this->teacherInfo['position_when_joined']['value']),
						array('No. of promotions since joining',$this->teacherInfo['no_of_promotions']['value']),
						array('No. of subjects taught',$this->teacherInfo['no_of_subjects_taught']['value']),
						array('No. of classes taught per week',$this->teacherInfo['no_of_classes_per_week']['value']),
					)
				),
				"style"=>"bordered kpablock firstColBold"
			);
		$noToRoman=array(1=>"I",2=>"II",3=>"III",4=>"IV",5=>"V",6=>"VI",7=>"VII",8=>"VIII",9=>"IX",10=>"X",11=>"XI",12=>"XII",13=>"XIII",14=>"XIV",15=>"XV",16=>"XVI",17=>"XVII",18=>"XVIII",19=>"XIX",20=>"XX");
		$i=0;
		$total=count($this->teacherInfo['other_roles']['value']);
		foreach($this->teacherInfo['other_roles']['value'] as $val){
			$i++;
			$block['blockBody']['dataArray'][]=array("Other role in the school".($total>1?" (".$noToRoman[$i].")":""),$val);
		}
		$i=0;
		foreach($this->teacherInfo['supervisors']['value'] as $val){
			$i++;
			$block['blockBody']['dataArray'][]=array("Your Supervisor name ".($i==1?"(primary)":"(others)"),$val);
		}
		$block['blockBody']['dataArray'][]=array("Date of self-review",ChangeFormat($this->assessmentObject[3]['ratingInputDate']));
		$block['blockBody']['dataArray'][]=array("Date of external review",ChangeFormat($this->assessmentObject[4]['ratingInputDate']));
		$blocks[]=$block;
		
		return $blocks;
	}
        
        protected function getStudentInfoBlocks(){
		$blocks=array();
		$block=array(
				"blockBody"=>array(
					"dataArray"=>array(
						array('Name',$this->studentInfo['name']['value']),
                                                array("Career Readiness Rating",array("text"=>$this->awardName,"style"=>"blueColor textBold")),
						array('Student UID',$this->studentInfo['student-UID']['value']),
						//array('Mobile No.',$this->studentInfo['contact_num1']['value']),
						//array('Email contact',$this->studentInfo['email']['value'])				
						//array("Overall grade awarded",array("text"=>$this->awardName,"style"=>"blueColor textBold"))
					)
				),
				"style"=>"bordered kpablock firstColBold"
			);
		$blocks[]=$block;
		/*$block=array(
				"blockBody"=>array(
					"dataArray"=>array(
						array("<b>Basic Information about the candidate:<b>")
					)
				)
			);
			
		$blocks[]=$block;
		$block=array(
				"blockBody"=>array(
					"dataArray"=>array(
						array('School Name',$this->aqsData['school_name']),
						array('School Address',$this->aqsData['school_address']),
						array('School Phone No.',$this->aqsData['principal_phone_no']),
						array('Name of the Principal',$this->aqsData['principal_name']),
						array("Your Educational Qualifications",$this->studentInfo['current_edu']['value']),
						//array('Total years of teaching experience',($this->studentInfo['experience']['value']==0?'Less than 1':$this->studentInfo['experience']['value']).' years'),
						//array('Year of joining current school',$this->studentInfo['joinning_year']['value']),
						//array('Position when joined the school',$this->studentInfo['position_when_joined']['value']),
						//array('No. of promotions since joining',$this->studentInfo['no_of_promotions']['value']),
						//array('No. of subjects taught',$this->studentInfo['no_of_subjects_taught']['value']),
						//array('No. of classes taught per week',$this->studentInfo['no_of_classes_per_week']['value']),
					)
				),
				"style"=>"bordered kpablock firstColBold"
			);
		$noToRoman=array(1=>"I",2=>"II",3=>"III",4=>"IV",5=>"V",6=>"VI",7=>"VII",8=>"VIII",9=>"IX",10=>"X",11=>"XI",12=>"XII",13=>"XIII",14=>"XIV",15=>"XV",16=>"XVI",17=>"XVII",18=>"XVIII",19=>"XIX",20=>"XX");
		$i=0;
		$total=count($this->studentInfo['other_roles']['value']);
		foreach($this->studentInfo['other_roles']['value'] as $val){
			$i++;
			$block['blockBody']['dataArray'][]=array("Other role in the school".($total>1?" (".$noToRoman[$i].")":""),$val);
		}
		$i=0;
		foreach($this->studentInfo['supervisors']['value'] as $val){
			$i++;
			$block['blockBody']['dataArray'][]=array("Your Supervisor name ".($i==1?"(primary)":"(others)"),$val);
		}
		$block['blockBody']['dataArray'][]=array("Date of self-review",$this->assessmentObject[3]['ratingInputDate']);
		$block['blockBody']['dataArray'][]=array("Date of external review",$this->assessmentObject[4]['ratingInputDate']);
		$blocks[]=$block;
		*/
		return $blocks;
	}
	
	/*protected function generateSection_ScoreCardForKPAs($skipComparisonSection=0){
		$totalKpas=count($this->kpas);
		$schemeId = ($this->reportId==5 || $this->reportId==9)? 2:1;
		$comparisonSection=array();
		if($skipComparisonSection==0){
			$indexKey=$this->addIndex("Comparison of Reviews ".($totalKpas>1?'across':'of')." ".$totalKpas." Key Performance Areas");
			$comparisonSection=array("sectionHeading"=>array("text"=>"1. Comparison of Reviews across ".($totalKpas>1?$totalKpas:'')." Key Performance Areas ","style"=>"greyHead"),"sectionBody"=>array(),"indexKey"=>$indexKey);
		}
		$kpaComparisonBlock=array(
								"blockHeading"=>array(
									"data"=>array("KPA. No.","Key Performance Area (KPA)","Self-Review Rating(SSRE)","External Review Rating(SERE)")
								),
								"blockBody"=>array(
									"dataArray"=>array()
								),
							"style"=>"bordered comparisonBlock"
						);
		
		$kpa_count=0;
		$kpaSectionArray=array();
		$numberToAlpha=array(1=>"a",2=>"b",3=>"c",4=>"d");
		$kpaValuesForGraph=array();
		foreach($this->kpas as $kpa){
			$kpa_count++;
			
			$kpaValuesForGraph[]=array("values"=>array(isset($kpa['internalRating'])?$kpa['internalRating']['score']:0,isset($kpa['externalRating'])?$kpa['externalRating']['score']:0),"name"=>$kpa['KPA_name']);
			
			$kpaComparisonBlock['blockBody']['dataArray'][]=array(
																$kpa_count,
																$kpa['KPA_name'],
																'<span class="'.(isset($kpa['internalRating'])?"score-".$kpa['internalRating']['score']:"").'">'.(isset($kpa['internalRating'])?$kpa['internalRating']['rating']:"").'</span>',
					$this->subAssessmentType==1?'N/A':'<span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"")."</span>"
																);
			$indexKey=$this->addIndex("Score card for KPA$kpa_count - ".$kpa['KPA_name']);
	
			$section=array("sectionHeading"=>array("text"=>"Key Peformance Area (KPA $kpa_count) - ".$kpa['KPA_name'],"style"=>"greyHead"),"sectionBody"=>array(),"indexKey"=>$indexKey);
			if($this->reportId!=5 && $this->reportId!=9){
				$kpaBlock=array(
							"blockBody"=>array(
								"dataArray"=>array(
									array(
										"Assessment for ".$kpa['KPA_name'],
										'<div>Self-Review Rating (SSRE)&nbsp;&nbsp;&nbsp;&nbsp;<span class="'.(isset($kpa['internalRating'])?"score-".$kpa['internalRating']['score']:"").'">'.(isset($kpa['internalRating'])?$kpa['internalRating']['rating']:"").'</span></div><div class="pull-left">External Review Rating (SERE)&nbsp;&nbsp;&nbsp;&nbsp;'.($this->subAssessmentType==1?'N/A':'<span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"")."</span>")."</div>")
								)
							),
							"style"=>"bordered kpablock"
						);
				$section['sectionBody'][]=$kpaBlock;
			}
			$keyQ_count=0;
			$coreQsInKPA=0;
			if(isset($this->keyQuestions[$kpa['kpa_instance_id']])){
				foreach($this->keyQuestions[$kpa['kpa_instance_id']] as $keyQ){
					$keyQ_count++;
					$jsBlock=array(
							"blockHeading"=>array(
									"data"=>array(
											array(
													"text"=>"Key Question (K.Q $keyQ_count) : &nbsp;&nbsp;".$keyQ['key_question_text'],
													"cSpan"=>10
											)
									)
							),
							"blockBody"=>array(
									"dataArray"=>array(
											array("<span>Sub Questions (S.Q)</span>"),
											array("<span>Judgement Statements</span>"),
											array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span>Self-Review Rating (SRR)</span>":"<span>Self-Review Rating (SSRE)</span>"),
											array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span>External Review Rating (ERR)</span>":"<span>External Review Rating (SERE)</span>"),
											array("<span>Judgement Distance</span>")
									)
							),
							"style"=>"bordered kpaStyle".(($this->reportId==5 || $this->reportId==9)?' mb25':''),
							"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
					);
					$cqBlock=array(
							"blockBody"=>array(
									"dataArray"=>array(
											array("<span>Self-Review Grade for S.Q</span>"),
											array("<span>External Review Grade for S.Q</span>")
									)
							),
							"style"=>"bordered kpaStyle",
							"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
					);
					$kqBlock=array(
							"blockBody"=>array(
									"dataArray"=>array(
											array("<span>Self-Review Grade for K.Q</span>"),
											array("<span>External Review Grade for K.Q</span>")
									)
							),
							"style"=>"bordered kpaStyle",
							"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
					);
					$coreQ_count=0;
					if(isset($this->coreQuestions[$keyQ['key_question_instance_id']])){
						foreach($this->coreQuestions[$keyQ['key_question_instance_id']] as $coreQ){
							$coreQ_count++;
							$coreQsInKPA++;
							$jsBlock['blockBody']['dataArray'][0][]=array("text"=>"<span class=\"cQn\">$coreQsInKPA. ".$coreQ['core_question_text'].'</span>',"cSpan"=>3);
							$satatement_count=0;
							foreach($this->judgementStatement[$coreQ['core_question_instance_id']] as $statment){
								$satatement_count++;
								$jsBlock['blockBody']['dataArray'][1][]=$coreQsInKPA.$numberToAlpha[$satatement_count];
								$jsBlock['blockBody']['dataArray'][2][]=array("text"=>isset($statment['internalRating'])?$statment['internalRating']['rating']:"","style"=>"colSize-1");
								$jsBlock['blockBody']['dataArray'][3][]=array("text"=>$this->subAssessmentType==1?'N/A':(isset($statment['externalRating'])?$statment['externalRating']['rating']:""));
								$jsBlock['blockBody']['dataArray'][4][]=$this->subAssessmentType==1?'<span>N/A</span>':(isset($statment['externalRating']) && isset($statment['internalRating'])?($statment['internalRating']['score']-$statment['externalRating']['score']?$statment['internalRating']['score']-$statment['externalRating']['score']:" 0 "):"");
							}
							$cqBlock['blockBody']['dataArray'][0][]=array("text"=>'<span  class="scheme-'.$schemeId.'-'.(isset($coreQ['internalRating'])?"score-".$coreQ['internalRating']['score']:"").'">'.(isset($coreQ['internalRating'])?$coreQ['internalRating']['rating']:'').'</span>',"style"=>"colSize-3");
							$cqBlock['blockBody']['dataArray'][1][]=$this->subAssessmentType==1?'<span>N/A</span>':('<span class="scheme-'.$schemeId.'-'.(isset($coreQ['externalRating'])?"score-".$coreQ['externalRating']['score']:"").'">'.(isset($coreQ['externalRating'])?$coreQ['externalRating']['rating']:'').'</span>');
						}
					}
					$kqBlock['blockBody']['dataArray'][0][]='<span class="'.(isset($keyQ['internalRating'])?"score-".$keyQ['internalRating']['score']:"").'">'.(isset($keyQ['internalRating'])?$keyQ['internalRating']['rating']:'').'</span>';
					$kqBlock['blockBody']['dataArray'][1][]=$this->subAssessmentType==1?'<span>N/A</span>':('<span class="'.(isset($keyQ['externalRating'])?"score-".$keyQ['externalRating']['score']:"").'">'.(isset($keyQ['externalRating'])?$keyQ['externalRating']['rating']:'').'</span>');
					$section['sectionBody'][]=$jsBlock;
					if($this->reportId!=5 && $this->reportId!=9){
						$section['sectionBody'][]=$cqBlock;
						$section['sectionBody'][]=$kqBlock;
					}
					if($this->reportId==5 || $this->reportId==9){
						$section['sectionBody'][]=$cqBlock;						
					}
				}
			}
			$kpaSectionArray[]=$section;
			}
		if($skipComparisonSection==0){
			$comparisonSection['sectionBody'][]=$kpaComparisonBlock;
			
			$graphBlock=array(
						"blockHeading"=>array(
							"data"=>array("Bar graph representation of above comparison")
						),
						"blockBody"=>array(
							"dataArray"=>array(
								array($this->getGraphHTML($kpaValuesForGraph,array(4=>"Outstanding",3=>"Good",2=>"Variable",1=>"Needs Attention"),4,1,array("SSRE","SERE"),"				
	Key Performance Areas (KPAs)","Grades"))
							)
						),
					"style"=>"bordered barGraph"
				);
			$comparisonSection['sectionBody'][]=$graphBlock;
			
			$keysHeadBlock=array(
						"blockHeading"=>array(
							"data"=>array("2. Key for reading the report")
						),
						"style"=>"onlyGreyHead",
						"config"=>array('groupby'=>"keyInfoBlock")
				);
			$comparisonSection['sectionBody'][]=$keysHeadBlock;
                        if($this->reportId==13){
			$keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array("Always","There is evidence of robust systems of good practice throughout the college, across all sections from the beginning of the academic year to the end.  These are documented and known to all stakeholders."),
								array("Mostly","There is evidence of systemic good practice in most part of the college, in most of the sections for most of the year.  Everyone in the college is aware of these systems and processes."),
								array("Sometimes","There is evidence of good practice in some parts of the college, in some of the sections for some time of the year.  It is known to few stakeholders in the college."),
								array("Rarely","There is little or no evidence of good practise in the college.")
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);
                        }else{
                           $keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array("Always","There is evidence of robust systems of good practice throughout the school, across all sections from the beginning of the academic year to the end.  These are documented and known to all stakeholders."),
								array("Mostly","There is evidence of systemic good practice in most part of the school, in most of the sections for most of the year.  Everyone in the school is aware of these systems and processes."),
								array("Sometimes","There is evidence of good practice in some parts of the school, in some of the sections for some time of the year.  It is known to few stakeholders in the school."),
								array("Rarely","There is little or no evidence of good practise in the school.")
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				); 
                        }
                        
			$comparisonSection['sectionBody'][]=$keysBodyBlock;
			$this->sectionArray=array_merge($this->sectionArray,array($comparisonSection),$kpaSectionArray);
		}else{
			$this->sectionArray=array_merge($this->sectionArray,$kpaSectionArray);
		}
	}*/
        
        /*protected function generateSection_ScoreCardForKPAs($skipComparisonSection=0,$lang_id,$diagnosticLabels=array()){
		$totalKpas=count($this->kpas);
		$schemeId = ($this->reportId==5 || $this->reportId==9)? 2:1;
		$comparisonSection=array();
                
		if($skipComparisonSection==0 && $this->config['getCreateNetType']!=1){
			
                        $kpaNum = ($totalKpas)>1?str_replace('&',$totalKpas,$diagnosticLabels['kpa_performance_area_title']):str_replace('&','',$diagnosticLabels['kpa_performance_area_title']);
                        $indexKey=$this->addIndex($kpaNum);
			$comparisonSection=array("sectionHeading"=>array("text"=>"1. $kpaNum","style"=>"greyHead"),"sectionBody"=>array(),"indexKey"=>$indexKey);
		}
		$kpaComparisonBlock=array(
								"blockHeading"=>array(
									"data"=>array($diagnosticLabels['KPA'].". ".$diagnosticLabels['No']."",$diagnosticLabels['KPA2']."(KPA)",$diagnosticLabels['self_review_rating']."(SSRE)",$diagnosticLabels['external_review_rating']."(SERE)")
								),
								"blockBody"=>array(
									"dataArray"=>array()
								),
							"style"=>"bordered comparisonBlock"
						);
		
                if($this->reportId==1 && $this->subAssessmentType!=1){
		$judgedistance[]=$this->JDistanceOnKpa($lang_id,1);
                $ratingperformance[]=$this->JRatingOnKpa($lang_id,$diagnosticLabels,1);
                }
                
		$kpa_count=0;
		$kpaSectionArray=array();
		$numberToAlpha=array(1=>"a",2=>"b",3=>"c",4=>"d");
		$kpaValuesForGraph=array();
		
               // echo "<pre>";print_r($diagnosticLabels);
		foreach($this->kpas as $kpa){
			$kpa_count++;
			
			$kpaValuesForGraph[]=array("values"=>array(isset($kpa['internalRating'])?$kpa['internalRating']['score']:0,isset($kpa['externalRating'])?$kpa['externalRating']['score']:0),"name"=>$kpa['KPA_name']);
			
                        $KPAScoreCard = str_replace('&',$kpa_count,$diagnosticLabels['Score_Card_KPA_Title']);	
			$kpaComparisonBlock['blockBody']['dataArray'][]=array(
																$kpa_count,
																$kpa['KPA_name'],
																'<span class="'.(isset($kpa['internalRating'])?"score-".$kpa['internalRating']['score']:"").'">'.(isset($kpa['internalRating'])?$kpa['internalRating']['rating']:"").'</span>',
																$this->subAssessmentType==1?'N/A':'<span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"")."</span>"
                        											);
			$indexKey=$this->addIndex($KPAScoreCard." - ".$kpa['KPA_name']);
	
			$section=array("sectionHeading"=>array("text"=>"".$diagnosticLabels['KPA2']." (KPA $kpa_count) - ".$kpa['KPA_name'],"style"=>"greyHead"),"sectionBody"=>array(),"indexKey"=>$indexKey);
			
                        
                        
                        if($this->reportId!=5 && $this->reportId!=9 && $this->config['getCreateNetType']!=1){
				$kpaBlock=array(
							"blockBody"=>array(
								"dataArray"=>array(
									array(
										"".$kpa['KPA_name']." ".$diagnosticLabels['assessment_for']."",
										'<div>'.$diagnosticLabels['self_review_rating'].' (SSRE)&nbsp;&nbsp;&nbsp;&nbsp;<span class="'.(isset($kpa['internalRating'])?"score-".$kpa['internalRating']['score']:"").'">'.(isset($kpa['internalRating'])?$kpa['internalRating']['rating']:"").'</span></div><div class="pull-left">'.$diagnosticLabels['external_review_rating'].'&nbsp;&nbsp;&nbsp;&nbsp;'.($this->subAssessmentType==1?'N/A':'<span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"")."</span>")."</div>")
								)
							),
							"style"=>"bordered kpablock"
						);
				$section['sectionBody'][]=$kpaBlock;
			}
                        
			$keyQ_count=0;
			$coreQsInKPA=0;
			if(isset($this->keyQuestions[$kpa['kpa_instance_id']])){
                            
				foreach($this->keyQuestions[$kpa['kpa_instance_id']] as $keyQ){
					$keyQ_count++;
					$jsBlock=array(
								"blockHeading"=>array(
									"data"=>array(
										array(
											"text"=>"".$diagnosticLabels['Key_Question']." (K.Q $keyQ_count) : &nbsp;&nbsp;".$keyQ['key_question_text'],
											"cSpan"=>10
										)
									)
								),
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['Sub_Question']." (S.Q)</span>"),
										array("<span>".$diagnosticLabels['Judgement_Statements']."</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span> ".$diagnosticLabels['self_review_rating']."(SRR)</span>":"<span>".$diagnosticLabels['self_review_rating']."(SSRE)</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span>".$diagnosticLabels['external_review_rating']." (ERR)</span>":"<span>".$diagnosticLabels['external_review_rating']." (SERE)</span>"),
										array("<span>".$diagnosticLabels['Judgement_Distance']."</span>")
									)
								),
							"style"=>"bordered kpaStyle".(($this->reportId==5)?' mb25':''),
							"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
						);
					$cqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['self_review_sq']."</span>"),
										array("<span>".$diagnosticLabels['external_review_sq']."</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							);
					$kqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['self_review_kq']."</span>"),
										array("<span>".$diagnosticLabels['external_review_kq']."</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							);
					$coreQ_count=0;
					if(isset($this->coreQuestions[$keyQ['key_question_instance_id']])){
						foreach($this->coreQuestions[$keyQ['key_question_instance_id']] as $coreQ){
							$coreQ_count++;
							$coreQsInKPA++;
							
							$jsBlock['blockBody']['dataArray'][0][]=array("text"=>"<span class=\"cQn\">$coreQsInKPA. ".$coreQ['core_question_text'].'</span>',"cSpan"=>3);
							$satatement_count=0;
							foreach($this->judgementStatement[$coreQ['core_question_instance_id']] as $statment){
								$satatement_count++;
								//$jsBlock['blockBody']['dataArray'][1][]=$coreQsInKPA.$numberToAlpha[$satatement_count];
								//$jsBlock['blockBody']['dataArray'][2][]=array("text"=>isset($statment['internalRating'])?$statment['internalRating']['rating']:"","style"=>"colSize-1");
								//$jsBlock['blockBody']['dataArray'][3][]=array("text"=>$this->subAssessmentType==1?'N/A':(isset($statment['externalRating'])?$statment['externalRating']['rating']:""));
								//$jsBlock['blockBody']['dataArray'][4][]=$this->subAssessmentType==1?'<span>N/A</span>':(isset($statment['externalRating']) && isset($statment['internalRating'])?$statment['internalRating']['score']-$statment['externalRating']['score']:"");
							
                                                                $jsBlock['blockBody']['dataArray'][1][]=$coreQsInKPA.$numberToAlpha[$satatement_count];
								$jsBlock['blockBody']['dataArray'][2][]=array("text"=>isset($statment['internalRating'])?$statment['internalRating']['rating']:"","style"=>"colSize-1");
								$jsBlock['blockBody']['dataArray'][3][]=array("text"=>$this->subAssessmentType==1?'N/A':(isset($statment['externalRating'])?$statment['externalRating']['rating']:""));
								$jsBlock['blockBody']['dataArray'][4][]=$this->subAssessmentType==1?'<span>N/A</span>':(isset($statment['externalRating']) && isset($statment['internalRating'])?($statment['internalRating']['score']-$statment['externalRating']['score']?$statment['internalRating']['score']-$statment['externalRating']['score']:" 0 "):"");
                                                        }
                                                        
							$cqBlock['blockBody']['dataArray'][0][]=array("text"=>'<span class="scheme-'.$schemeId.'-'.(isset($coreQ['internalRating'])?"score-".$coreQ['internalRating']['score']:"").'">'.(isset($coreQ['internalRating'])?$coreQ['internalRating']['rating']:'').'</span>',"style"=>"colSize-3");
							$cqBlock['blockBody']['dataArray'][1][]=$this->subAssessmentType==1?'<span>N/A</span>':('<span class="scheme-'.$schemeId.'-'.(isset($coreQ['externalRating'])?"score-".$coreQ['externalRating']['score']:"").'">'.(isset($coreQ['externalRating'])?$coreQ['externalRating']['rating']:'').'</span>');
						}
					}
					$kqBlock['blockBody']['dataArray'][0][]='<span class="scheme-'.$schemeId.'-'.(isset($keyQ['internalRating'])?"score-".$keyQ['internalRating']['score']:"").'">'.(isset($keyQ['internalRating'])?$keyQ['internalRating']['rating']:'').'</span>';
					$kqBlock['blockBody']['dataArray'][1][]=$this->subAssessmentType==1?'<span>N/A</span>':('<span class="scheme-'.$schemeId.'-'.(isset($keyQ['externalRating'])?"score-".$keyQ['externalRating']['score']:"").'">'.(isset($keyQ['externalRating'])?$keyQ['externalRating']['rating']:'').'</span>');
					$section['sectionBody'][]=$jsBlock;
					if($this->reportId!=5){
						$section['sectionBody'][]=$cqBlock;
						$section['sectionBody'][]=$kqBlock;
					}
					if($this->reportId==5){
						$section['sectionBody'][]=$cqBlock;						
					}
				}
			}
			$kpaSectionArray[]=$section;
		}
		if($skipComparisonSection==0  && $this->config['getCreateNetType']!=1){
			$comparisonSection['sectionBody'][]=$kpaComparisonBlock;
			//$bar_keys=count($this->getDiagnosticGraphkeys($lang_id))>0?$this->getDiagnosticGraphkeys($lang_id):array(4=>"Outstanding",3=>"Good",2=>"Variable",1=>"Needs Attention");
                        $bar_keys=array(4=>isset($diagnosticLabels['Outstanding'])?$diagnosticLabels['Outstanding']:"Outstanding",3=>isset($diagnosticLabels['Good'])?$diagnosticLabels['Good']:"Good",2=>isset($diagnosticLabels['Variable'])?$diagnosticLabels['Variable']:"Variable",1=>isset($diagnosticLabels['Needs_Attention'])?$diagnosticLabels['Needs_Attention']:"Needs Attention");
                        
			$graphBlock=array(
						"blockHeading"=>array(
							"data"=>array($diagnosticLabels['bar_graph_representation_title'])
						),
						"blockBody"=>array(
							"dataArray"=>array(
								array($this->getGraphHTML($kpaValuesForGraph,$bar_keys,4,1,array("SSRE","SERE"),isset($diagnosticLabels['KPA_FULLFORM'])?$diagnosticLabels['KPA_FULLFORM']:"Key Performance Areas (KPAs)","Grades"))
							)
						),
					"style"=>"bordered barGraph"
				);
			$comparisonSection['sectionBody'][]=$graphBlock;
			
			$keysHeadBlock=array(
						"blockHeading"=>array(
							"data"=>array($diagnosticLabels['report_reading_key'])
						),
						"style"=>"onlyGreyHead",
						"config"=>array('groupby'=>"keyInfoBlock")
				);
			$comparisonSection['sectionBody'][]=$keysHeadBlock;
                        if($this->reportId==13){
			$keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels['Always'],str_replace("school", "college", $diagnosticLabels['Always_value'])),
								array($diagnosticLabels['Mostly'],str_replace("school", "college", $diagnosticLabels['Mostly_value'])),
								array($diagnosticLabels['Sometimes'],str_replace("school", "college", $diagnosticLabels['Sometimes_value'])),
								array($diagnosticLabels['Rarely'],str_replace("school", "college", $diagnosticLabels['Rarely_value']))
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);
                        }else{
                            $keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels['Always'],$diagnosticLabels['Always_value']),
								array($diagnosticLabels['Mostly'],$diagnosticLabels['Mostly_value']),
								array($diagnosticLabels['Sometimes'],$diagnosticLabels['Sometimes_value']),
								array($diagnosticLabels['Rarely'],$diagnosticLabels['Rarely_value'])
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);
                        }
			$comparisonSection['sectionBody'][]=$keysBodyBlock;
			//$this->sectionArray=array_merge($this->sectionArray,array($comparisonSection),$kpaSectionArray);
                        $this->sectionArray=array_merge($this->sectionArray,array($comparisonSection));
                        
                        if($this->reportId==1 && $this->subAssessmentType!=1){
                        $this->sectionArray= array_merge($this->sectionArray,$judgedistance);
                        $this->sectionArray= array_merge($this->sectionArray,$ratingperformance);
                        }
                        
                        $this->sectionArray= array_merge($this->sectionArray,$kpaSectionArray);
		}else{
                        if($this->config['getCreateNetType']==1){
                            //$comparisonSection['sectionBody'][]=$kpaComparisonBlock;
                            $keysHeadBlock=array(
						"blockHeading"=>array(
							"data"=>array($diagnosticLabels['report_reading_key'])
						),
						"style"=>"onlyGreyHead",
						"config"=>array('groupby'=>"keyInfoBlock")
				);
			$comparisonSection['sectionBody'][]=$keysHeadBlock;
                          $keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels['Always'],$diagnosticLabels['Always_value']),
								array($diagnosticLabels['Mostly'],$diagnosticLabels['Mostly_value']),
								array($diagnosticLabels['Sometimes'],$diagnosticLabels['Sometimes_value']),
								array($diagnosticLabels['Rarely'],$diagnosticLabels['Rarely_value'])
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);  
                          $comparisonSection['sectionBody'][]=$keysBodyBlock;  
                        }

			//$this->sectionArray=array_merge($this->sectionArray,array($comparisonSection),$kpaSectionArray);

                        if($this->config['getCreateNetType']==1){
                        $this->sectionArray=array_merge($this->sectionArray,array($comparisonSection));
                        }
                        
                        if($this->reportId==1 && $this->subAssessmentType!=1){
                        $this->sectionArray= array_merge($this->sectionArray,$judgedistance);
                        $this->sectionArray= array_merge($this->sectionArray,$ratingperformance);
                        }
                        
                        $this->sectionArray= array_merge($this->sectionArray,$kpaSectionArray);
		}
	}*/
	
       protected function generateSection_ScoreCardForKPAs($skipComparisonSection=0,$lang_id,$diagnosticLabels=array(),$round=1){
		$totalKpas=count($this->kpas);
		$schemeId = ($this->reportId==5 || $this->reportId==9)? 2:1;
		$comparisonSection=array();
                $isCollobrative=$this->config['isCollobrative'];
                
		if($skipComparisonSection==0 && $this->config['getCreateNetType']!=1){
			if($isCollobrative==1){
                        $kpaNum = 'Performance across '.$totalKpas.' Key Performance Areas';
                        }else{
                        $kpaNum = ($totalKpas)>1?str_replace('&',$totalKpas,$diagnosticLabels['kpa_performance_area_title']):str_replace('&','',$diagnosticLabels['kpa_performance_area_title']);
                        }
                        $indexKey=$this->addIndex($kpaNum);
			$comparisonSection=array("sectionHeading"=>array("text"=>"1. $kpaNum","style"=>"greyHead"),"sectionBody"=>array(),"indexKey"=>$indexKey);
		}
                
                if($isCollobrative==1){
                 $kpaComparisonBlock=array(
								"blockHeading"=>array(
                        
									"data"=>array($diagnosticLabels['KPA'].". ".$diagnosticLabels['No']."","KPA","Collaborative Review Rating (CSRE)")
								),
								"blockBody"=>array(
									"dataArray"=>array()
								),
							"style"=>"bordered comparisonBlock"
						);   
                }else{
		$kpaComparisonBlock=array(
								"blockHeading"=>array(
                        
									"data"=>array($diagnosticLabels['KPA'].". ".$diagnosticLabels['No']."",$diagnosticLabels['KPA2']."(KPA)",$diagnosticLabels['self_review_rating']."(SSRE)",$diagnosticLabels['external_review_rating']."(SERE)")
								),
								"blockBody"=>array(
									"dataArray"=>array()
								),
							"style"=>"bordered comparisonBlock"
						);
                }
                
                if($this->reportId==1 && $this->subAssessmentType!=1){
                if($isCollobrative==0){    
		$judgedistance[]=$this->JDistanceOnKpa($lang_id,1);
                }
                $ratingperformance[]=$this->JRatingOnKpa($lang_id,$diagnosticLabels,1);
                }
                
		$kpa_count=0;
		$kpaSectionArray=array();
		$numberToAlpha=array(1=>"a",2=>"b",3=>"c",4=>"d");
		$kpaValuesForGraph=array();
		
               // echo "<pre>";print_r($diagnosticLabels);
		foreach($this->kpas as $kpa){
			$kpa_count++;
			if($isCollobrative==1){
                        $kpaValuesForGraph[]=array("values"=>array(isset($kpa['internalRating'])?$kpa['internalRating']['score']:0,0),"name"=>$kpa['KPA_name']);
                         $KPAScoreCard = str_replace('&',$kpa_count,$diagnosticLabels['Score_Card_KPA_Title']);	
			
                        $kpaComparisonBlock['blockBody']['dataArray'][]=array(
																$kpa_count,
																$kpa['KPA_name'],
																'<span class="'.(isset($kpa['internalRating'])?"score-".$kpa['internalRating']['score']:"").'">'.(isset($kpa['internalRating'])?$kpa['internalRating']['rating']:"").'</span>'
                        											);    
                        }else{
			$kpaValuesForGraph[]=array("values"=>array(isset($kpa['internalRating'])?$kpa['internalRating']['score']:0,isset($kpa['externalRating'])?$kpa['externalRating']['score']:0),"name"=>$kpa['KPA_name']);
                        
                        
                         $KPAScoreCard = str_replace('&',$kpa_count,$diagnosticLabels['Score_Card_KPA_Title']);	
			
                        $kpaComparisonBlock['blockBody']['dataArray'][]=array(
																$kpa_count,
																$kpa['KPA_name'],
																'<span class="'.(isset($kpa['internalRating'])?"score-".$kpa['internalRating']['score']:"").'">'.(isset($kpa['internalRating'])?$kpa['internalRating']['rating']:"").'</span>',
																$this->subAssessmentType==1?'N/A':'<span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"")."</span>"
                        											);
                        }
                        
                       
			$indexKey=$this->addIndex($KPAScoreCard." - ".$kpa['KPA_name']);
	
			$section=array("sectionHeading"=>array("text"=>"".$diagnosticLabels['KPA2']." (KPA $kpa_count) - ".$kpa['KPA_name'],"style"=>"greyHead"),"sectionBody"=>array(),"indexKey"=>$indexKey);
			if($isCollobrative==1){
                         if($this->reportId!=5 && $this->reportId!=9 && $this->config['getCreateNetType']!=1){
				$kpaBlock=array(
							"blockBody"=>array(
								"dataArray"=>array(
									array(
										 $kpa['KPA_name']." ".$diagnosticLabels['assessment_for'],
										'<div class="pull-left">Collaborative-Review Rating (CSRE) &nbsp; <span class="'.(isset($kpa['internalRating'])?"score-".$kpa['internalRating']['score']:"").'">'.(isset($kpa['internalRating'])?$kpa['internalRating']['rating']:"")."</span></div>")
								)
							),
							"style"=>"bordered kpablock"
						);
				$section['sectionBody'][]=$kpaBlock;
			}   
                        }else{
                            
                        if($this->reportId!=5 && $this->reportId!=9 && $this->config['getCreateNetType']!=1){
				$kpaBlock=array(
							"blockBody"=>array(
								"dataArray"=>array(
									array(
										 $kpa['KPA_name']." ".$diagnosticLabels['assessment_for'],
										'<div class="pull-left">'.$diagnosticLabels['self_review_rating'].' (SSRE)<br>'.$diagnosticLabels['external_review_rating'].' (SERE)</div><div class="pull-left"><span class="'.(isset($kpa['internalRating'])?"score-".$kpa['internalRating']['score']:"").'">'.(isset($kpa['internalRating'])?$kpa['internalRating']['rating']:"").'</span><br/>'.($this->subAssessmentType==1?'<span>N/A</span>':'<span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"").'</span>')."</div>")
								)
							),
							"style"=>"bordered kpablock"
						);
				$section['sectionBody'][]=$kpaBlock;
			}
                        
                        }
			$keyQ_count=0;
			$coreQsInKPA=0;
			if(isset($this->keyQuestions[$kpa['kpa_instance_id']])){
                            
				foreach($this->keyQuestions[$kpa['kpa_instance_id']] as $keyQ){
					$keyQ_count++;
                                        if($isCollobrative==1){
                                         $jsBlock=array(
								"blockHeading"=>array(
									"data"=>array(
										array(
											"text"=>"".$diagnosticLabels['Key_Question']." (K.Q $keyQ_count) : &nbsp;&nbsp;".$keyQ['key_question_text'],
											"cSpan"=>10
										)
									)
								),
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['Sub_Question']." (S.Q)</span>"),
										array("<span>".$diagnosticLabels['Judgement_Statements']."</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span> ".$diagnosticLabels['self_review_rating']."(SRR)</span>":"<span>Collaborative-Review Rating (CSRE)</span>"),
										//array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span>".$diagnosticLabels['external_review_rating']." (ERR)</span>":"<span>".$diagnosticLabels['external_review_rating']." (SERE)</span>"),
										//array("<span>".$diagnosticLabels['Judgement_Distance']."</span>")
									)
								),
							"style"=>"bordered kpaStyle".(($this->reportId==5)?' mb25':''),
							"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
						);   
                                        }else{
					$jsBlock=array(
								"blockHeading"=>array(
									"data"=>array(
										array(
											"text"=>"".$diagnosticLabels['Key_Question']." (K.Q $keyQ_count) : &nbsp;&nbsp;".$keyQ['key_question_text'],
											"cSpan"=>10
										)
									)
								),
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['Sub_Question']." (S.Q)</span>"),
										array("<span>".$diagnosticLabels['Judgement_Statements']."</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span> ".$diagnosticLabels['self_review_rating']."(SRR)</span>":"<span>".$diagnosticLabels['self_review_rating']."(SSRE)</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span>".$diagnosticLabels['external_review_rating']." (ERR)</span>":"<span>".$diagnosticLabels['external_review_rating']." (SERE)</span>"),
										array("<span>".$diagnosticLabels['Judgement_Distance']."</span>")
									)
								),
							"style"=>"bordered kpaStyle".(($this->reportId==5)?' mb25':''),
							"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
						);
                                        }
                                        if($isCollobrative==1){
					$cqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>Collaborative-Review Grade for S.Q.</span>"),
										//array("<span>".$diagnosticLabels['external_review_sq']."</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							);
                                        }else{
                                            $cqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['self_review_sq']."</span>"),
										array("<span>".$diagnosticLabels['external_review_sq']."</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							);
                                        }
                                        
                                        if($isCollobrative==1){
					$kqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>Collaborative-Review Grade for K.Q.</span>"),
										//array("<span>".$diagnosticLabels['external_review_kq']."</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							);
                                        }else{
                                           $kqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['self_review_kq']."</span>"),
										array("<span>".$diagnosticLabels['external_review_kq']."</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							); 
                                        }
					$coreQ_count=0;
					if(isset($this->coreQuestions[$keyQ['key_question_instance_id']])){
						foreach($this->coreQuestions[$keyQ['key_question_instance_id']] as $coreQ){
							$coreQ_count++;
							$coreQsInKPA++;
							
							$jsBlock['blockBody']['dataArray'][0][]=array("text"=>"<span class=\"cQn\">$coreQsInKPA. ".$coreQ['core_question_text'].'</span>',"cSpan"=>3);
							$satatement_count=0;
							foreach($this->judgementStatement[$coreQ['core_question_instance_id']] as $statment){
								$satatement_count++;
								$jsBlock['blockBody']['dataArray'][1][]=$coreQsInKPA.$numberToAlpha[$satatement_count];
								$jsBlock['blockBody']['dataArray'][2][]=array("text"=>isset($statment['internalRating'])?$statment['internalRating']['rating']:"","style"=>"colSize-1");
								if($isCollobrative==0){
                                                                $jsBlock['blockBody']['dataArray'][3][]=array("text"=>$this->subAssessmentType==1?'N/A':(isset($statment['externalRating'])?$statment['externalRating']['rating']:""));
								$jsBlock['blockBody']['dataArray'][4][]=$this->subAssessmentType==1?'<span>N/A</span>':(isset($statment['externalRating']) && isset($statment['internalRating'])?$statment['internalRating']['score']-$statment['externalRating']['score']:"");
                                                                }
                                                                
                                                                }
                                                        if($this->reportId==5){        
                                                            $cqBlock['blockBody']['dataArray'][0][]=array("text"=>'<span class="scheme-'.$schemeId.''.(isset($coreQ['internalRating'])?"-score-".$coreQ['internalRating']['score']:"").'">'.(isset($coreQ['internalRating'])?$coreQ['internalRating']['rating']:'').'</span>',"style"=>"colSize-3");
                                                        }else{
                                                            $cqBlock['blockBody']['dataArray'][0][]=array("text"=>'<span class="scheme-'.$schemeId.' '.(isset($coreQ['internalRating'])?"score-".$coreQ['internalRating']['score']:"").'">'.(isset($coreQ['internalRating'])?$coreQ['internalRating']['rating']:'').'</span>',"style"=>"colSize-3");
                                                        }
							if($isCollobrative==0){
                                                            if($this->reportId==5){ 
                                                                 $cqBlock['blockBody']['dataArray'][1][]=$this->subAssessmentType==1?'<span>N/A</span>':('<span class="scheme-'.$schemeId.''.(isset($coreQ['externalRating'])?"-score-".$coreQ['externalRating']['score']:"").'">'.(isset($coreQ['externalRating'])?$coreQ['externalRating']['rating']:'').'</span>');
                                                            }else{
                                                                 $cqBlock['blockBody']['dataArray'][1][]=$this->subAssessmentType==1?'<span>N/A</span>':('<span class="scheme-'.$schemeId.' '.(isset($coreQ['externalRating'])?"score-".$coreQ['externalRating']['score']:"").'">'.(isset($coreQ['externalRating'])?$coreQ['externalRating']['rating']:'').'</span>');
                                                            }
                                                        }
						}
					}
                                        if($this->reportId==5){ 
                                            $kqBlock['blockBody']['dataArray'][0][]='<span class="scheme-'.$schemeId.''.(isset($keyQ['internalRating'])?"-score-".$keyQ['internalRating']['score']:"").'">'.(isset($keyQ['internalRating'])?$keyQ['internalRating']['rating']:'').'</span>';
                                        } else{
                                            $kqBlock['blockBody']['dataArray'][0][]='<span class="scheme-'.$schemeId.' '.(isset($keyQ['internalRating'])?"score-".$keyQ['internalRating']['score']:"").'">'.(isset($keyQ['internalRating'])?$keyQ['internalRating']['rating']:'').'</span>';
                                        }
					if($isCollobrative==0){
                                            if($this->reportId==5){ 
                                                    $kqBlock['blockBody']['dataArray'][1][]=$this->subAssessmentType==1?'<span>N/A</span>':('<span class="scheme-'.$schemeId.''.(isset($keyQ['externalRating'])?"-score-".$keyQ['externalRating']['score']:"").'">'.(isset($keyQ['externalRating'])?$keyQ['externalRating']['rating']:'').'</span>');
                                            }else{
                                                $kqBlock['blockBody']['dataArray'][1][]=$this->subAssessmentType==1?'<span>N/A</span>':('<span class="scheme-'.$schemeId.' '.(isset($keyQ['externalRating'])?"score-".$keyQ['externalRating']['score']:"").'">'.(isset($keyQ['externalRating'])?$keyQ['externalRating']['rating']:'').'</span>');
                                            }
                                        }
                                        $section['sectionBody'][]=$jsBlock;
					if($this->reportId!=5){
						$section['sectionBody'][]=$cqBlock;
						$section['sectionBody'][]=$kqBlock;
					}
					if($this->reportId==5){
						$section['sectionBody'][]=$cqBlock;						
					}
				}
			}
			$kpaSectionArray[]=$section;
		}
                
                
                
		if($skipComparisonSection==0 && $this->config['getCreateNetType']!=1){
                        $bar_keys=array(4=>isset($diagnosticLabels['Outstanding'])?$diagnosticLabels['Outstanding']:"Outstanding",3=>isset($diagnosticLabels['Good'])?$diagnosticLabels['Good']:"Good",2=>isset($diagnosticLabels['Variable'])?$diagnosticLabels['Variable']:"Variable",1=>isset($diagnosticLabels['Needs_Attention'])?$diagnosticLabels['Needs_Attention']:"Needs Attention");
                        
                        
                        //print_r($bar_keys);
			$comparisonSection['sectionBody'][]=$kpaComparisonBlock;
			
			$graphBlock=array(
						"blockHeading"=>array(
							"data"=>$isCollobrative==1?array("Bar graph representation of above ratings"):array($diagnosticLabels['bar_graph_representation_title'])
						),
						"blockBody"=>array(
							"dataArray"=>array(
                                                          
								array($this->getGraphHTML($kpaValuesForGraph,$bar_keys,4,1,$isCollobrative==1?array("CSRE<br>Rating"):array("SSRE","SERE"),isset($diagnosticLabels['KPA_FULLFORM'])?$diagnosticLabels['KPA_FULLFORM']:"Key Performance Areas (KPAs)","Grades"))
							)
						),
					"style"=>"bordered barGraph"
				);
			$comparisonSection['sectionBody'][]=$graphBlock;
			
			$keysHeadBlock=array(
						"blockHeading"=>array(
							"data"=>array($diagnosticLabels['report_reading_key'])
						),
						"style"=>"onlyGreyHead",
						"config"=>array('groupby'=>"keyInfoBlock")
				);
			$comparisonSection['sectionBody'][]=$keysHeadBlock;
                        if($this->reportId==13){
			$keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels['Always'],$diagnosticLabels['Always_value']),
								array($diagnosticLabels['Mostly'],$diagnosticLabels['Mostly_value']),
								array($diagnosticLabels['Sometimes'],$diagnosticLabels['Sometimes_value']),
								array($diagnosticLabels['Rarely'],$diagnosticLabels['Rarely_value'])
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);
                        }else{
                            $keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels['Always'],$diagnosticLabels['Always_value']),
								array($diagnosticLabels['Mostly'],$diagnosticLabels['Mostly_value']),
								array($diagnosticLabels['Sometimes'],$diagnosticLabels['Sometimes_value']),
								array($diagnosticLabels['Rarely'],$diagnosticLabels['Rarely_value'])
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);
                        }
			$comparisonSection['sectionBody'][]=$keysBodyBlock;
                        
                        
			$this->sectionArray=array_merge($this->sectionArray,array($comparisonSection));
                        
                        if($this->reportId==1  && $this->subAssessmentType!=1){
                        if($isCollobrative==0){    
                        $this->sectionArray= array_merge($this->sectionArray,$judgedistance);
                        }
                        $this->sectionArray= array_merge($this->sectionArray,$ratingperformance);
                        }
                        
                        $this->sectionArray= array_merge($this->sectionArray,$kpaSectionArray);
                        
                        //$this->sectionArray=array_merge($this->sectionArray,array($comparisonSection),$kpaSectionArray);
                        
		}else{
                        if($this->config['getCreateNetType']==1){
                            //$comparisonSection['sectionBody'][]=$kpaComparisonBlock;
                            $keysHeadBlock=array(
						"blockHeading"=>array(
							"data"=>array($diagnosticLabels['report_reading_key'])
						),
						"style"=>"onlyGreyHead",
						"config"=>array('groupby'=>"keyInfoBlock")
				);
			$comparisonSection['sectionBody'][]=$keysHeadBlock;
                          $keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels['Always'],$diagnosticLabels['Always_value']),
								array($diagnosticLabels['Mostly'],$diagnosticLabels['Mostly_value']),
								array($diagnosticLabels['Sometimes'],$diagnosticLabels['Sometimes_value']),
								array($diagnosticLabels['Rarely'],$diagnosticLabels['Rarely_value'])
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);  
                          $comparisonSection['sectionBody'][]=$keysBodyBlock;  
                        }
                        
                        if($this->config['getCreateNetType']==1){
                        $this->sectionArray=array_merge($this->sectionArray,array($comparisonSection));
                        }
                        
                        if($this->reportId==1 && $this->subAssessmentType!=1){
                        $this->sectionArray= array_merge($this->sectionArray,$judgedistance);
                        $this->sectionArray= array_merge($this->sectionArray,$ratingperformance);
                        }
                        
                        $this->sectionArray= array_merge($this->sectionArray,$kpaSectionArray);
                        
			//$this->sectionArray=array_merge($this->sectionArray,array($comparisonSection),$kpaSectionArray);

		}
                if($round == 2){
                //get pi chart 
                        $piChartBlockHead=array(
						"blockHeading"=>array(
							"data"=>array('Comparative analysis for Round 1 and Round 2')
						),
						"style"=>"onlyGreyHead",
						"config"=>array('groupby'=>"keyInfoBlock")
				);
			$graphSection['sectionBody'][]=$piChartBlockHead;
                          $piChartBlockBody=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($this->getPiChartHTML())
							)
						),
					"style"=>"pieGraph keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);  
                        $graphSection['sectionBody'][]=$piChartBlockBody;
                       // echo "<pre>";print_r($piChartBlock);die;
                       // $graphSection['sectionBody'][]=$piChartBlockBody;
                       // $this->sectionArray=array_merge($this->sectionArray,array($comparisonSection));
                         $this->sectionArray= array_merge($this->sectionArray,array($graphSection));
                         
                          $piChartBlock2=array(
						
						"blockBody"=>array(
							"dataArray"=>array(
                                                            
								array($this->getPiChartHTML(2))
							)
						),
					"style"=>"pieGraph keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
                        );
                        
                       // echo "<pre>";print_r($piChartBlock);die;
                        $graphSection2['sectionBody'][]=$piChartBlock2;
                       // $this->sectionArray=array_merge($this->sectionArray,array($comparisonSection));
                        $this->sectionArray= array_merge($this->sectionArray,array($graphSection2));
                }
	}
        
         protected function getPiChartHTML($block=1){
		/*$lnth=count($valuesArray);
		if($lnth==0)
			return '';
		else if($lnth<5){
			$to=5-$lnth;
			$emptyArray=array();
			for($i=0;$i<count($valuesArray[0]['values']);$i++)
				$emptyArray[]=0;
			for($i=0;$i<$to;$i++){
				$valuesArray[]=array("values"=>$emptyArray,"empty"=>1);
			}
			$lnth=count($valuesArray);
		}
		$cols=count($valuesArray[0]["values"]);
		if($cols==0)
			return '';
		$extraTopSpaceInGraph=44;
		$oneStepHeight=21*4; //21 is the difference in 2 lines in image and we are adding 4 lines in one step
		$graphHeight=$oneStepHeight*($maxValue-$minValue)+$extraTopSpaceInGraph; // we are adding 2 line space as buffer
		$bottomBarHeight=50;
		$topBarHeight=30;
		$totalHeight=$topBarHeight+$bottomBarHeight+$graphHeight;*/
                $html='<div class="">';
               
                if($block == 1){
               
                
                    //$html='<div class="graphWrap">';
                    $s3_upload_url_key=UPLOAD_URL."charts/".$this->assessmentId."-".$this->assessmentIdRound2."-key-comparison-report.png";
                    if(!empty($s3_upload_url_key)){

                        $html .='<table width="100%"><tr bgcolor="orange" width="100%"><td  colspan="3" align="center" style="font-size: 20px;font-weight: bold;">At Key Question Level:</td></tr>';
                        $html .= '<tr><td width="30%"   align="left">'
                         . '<table width="100%"><tr><td width="100%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;">K.Q. with Improved Ratings:'.$this->improvedRatingKey.'</td></tr>'
                            . '<tr><td width="100%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;">K.Q. with Same Ratings:'.$this->sameRatingKey.'</td></tr>'
                            . '<tr><td width="100%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;">K.Q. with Decreased Ratings:'.$this->decreasedRatingKey.'</td></tr></table></td>'
                            . '<td width="45%" align="left"><img style="height:300px;width:430px;" src="'.$s3_upload_url_key.'"></td>'
                            . '<td width="25%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;">'
                            . '<table width="100%"><tr><td width="100%"  align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;"><img height="10px" width="10px" src=./public/images/green3.png>  Improved Ratings</td></tr>'
                            . '<tr><td width="100%"  align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;"><img height="10px" width="10px" src=./public/images/blue2.png>  Same Ratings</td></tr>'
                            . '<tr><td width="100%"  align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;"><img height="10px" width="10px" src=./public/images/red1.png>  Decreased Ratings</td></tr></table>'
                             .'</td></tr>'
                                . '</table>';
                        
                    }
                    $s3_upload_url_core=UPLOAD_URL."charts/".$this->assessmentId."-".$this->assessmentIdRound2."-corequstn-comparison-report.png";
                    if(!empty($s3_upload_url_core)){

                        $html .='<table width="100%"><tr bgcolor="orange" width="100%" ><td colspan="3" align="center" style="font-size: 20px;font-weight: bold;">At Sub Question Level:</td></tr>';
                        $html .= '<tr><td width="30%" align="left">'
                             
                                . '<table width="100%"><tr><td width="100%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;">S.Q. with Improved Ratings:'.$this->improvedRatingCore.'</td></tr>'
                            . '<tr><td width="100%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;">S.Q. with Same Ratings:'.$this->sameRatingCore.'</td></tr>'
                            . '<tr><td width="100%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;">S.Q. with Decreased Ratings:'.$this->decreasedRatingCore.'</td></tr></table></td>'
                            . '<td width="45%" align="left"><img style="height:300px;width:430px;" src="'.$s3_upload_url_core.'"></td>'
                            . '<td width="25%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;">'
                            . '<table width="100%"><tr><td width="100%"  align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;"><img height="10px" width="10px" src=./public/images/green3.png>  Improved Ratings</td></tr>'
                            . '<tr><td width="100%"  align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;"><img height="10px" width="10px" src=./public/images/blue2.png>  Same Ratings</td></tr>'
                            . '<tr><td width="100%"  align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;"><img height="10px" width="10px" src=./public/images/red1.png>  Decreased Ratings</td></tr></table>'
                             .'</td></tr>'
                                . '</table>';
                    }
                    
                }else{
                    $s3_upload_url_js=UPLOAD_URL."charts/".$this->assessmentId."-".$this->assessmentIdRound2."-judgemntstmnt-comparison-report.png";
                    if(!empty($s3_upload_url_js)){

                        $html ='<table width="100%"><tr bgcolor="orange" width="100%" ><td colspan="3" align="center" style="font-size: 20px;font-weight: bold;">At Judgement Statement Level:</td></tr>';
                        $html .= '<tr><td width="30%" align="left">'
                                . '<table width="100%"><tr><td width="100%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;">J.S. with Improved Ratings:'.$this->improvedRatingSt.'</td></tr>'
                            . '<tr><td width="100%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;">J.S. with Same Ratings:'.$this->sameRatingSt.'</td></tr>'
                            . '<tr><td width="100%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;">J.S. with Decreased Ratings:'.$this->decreasedRatingSt.'</td></tr></table></td>'
                            . '<td width="45%" align="left"><img style="height:300px;width:430px;" src="'.$s3_upload_url_js.'"></td>'
                            . '<td width="25%" align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;">'
                            . '<table width="100%"><tr><td width="100%"  align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;"><img height="10px" width="10px" src=./public/images/green3.png>  Improved Ratings</td></tr>'
                            . '<tr><td width="100%"  align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;"><img height="10px" width="10px" src=./public/images/blue2.png>  Same Ratings</td></tr>'
                            . '<tr><td width="100%"  align="left" style="font-size: 11px;font-weight: bold;font-family:Helvetica,Arial;line-height:1.2;"><img height="10px" width="10px" src=./public/images/red1.png>  Decreased Ratings</td></tr></table>'
                             .'</td></tr>'
                                . '</table>';

                    }                   
                
                }
                $html .='</div>';
		return $html;
	}
	protected function getGraphHTML($valuesArray,$steps,$maxValue,$minValue=0,$barDes=array(),$infoBelowGraph="",$infoOnYAxis=""){
		$lnth=count($valuesArray);
                $isCollobrative=$this->config['isCollobrative'];
		if($lnth==0)
			return '';
		else if($lnth<5){
			$to=5-$lnth;
			$emptyArray=array();
			for($i=0;$i<count($valuesArray[0]['values']);$i++)
				$emptyArray[]=0;
			for($i=0;$i<$to;$i++){
				$valuesArray[]=array("values"=>$emptyArray,"empty"=>1);
			}
			$lnth=count($valuesArray);
		}
		$cols=count($valuesArray[0]["values"]);
		if($cols==0)
			return '';
		$extraTopSpaceInGraph=44;
		$oneStepHeight=18*4; //21 is the difference in 2 lines in image and we are adding 4 lines in one step
		$graphHeight=$oneStepHeight*($maxValue-$minValue)+$extraTopSpaceInGraph; // we are adding 2 line space as buffer
		$bottomBarHeight=50;
		$topBarHeight=30;
		$totalHeight=$topBarHeight+$bottomBarHeight+$graphHeight;
		$html='<div class="graphWrap" style="align:left">
		<table><tr><td style="border:0;padding:0;margin:0;">
		<table class="stepDesc" style="border:0;">';
		foreach($steps as $k=>$step){
			$html.='<tr class="graphSteps"><td style="border:0;padding:0;height:76px;vertical-align: bottom;" class="graphSteps"><span class="score-'.$k.'">'.$step.'</span></td></tr>';
		}
		$html.="</table></td>";
	//	$html.="<td style='border:0;'><div style='transform: rotate(270deg);'>".$infoOnYAxis."</div></td>";
		
		/*$html.='<td style="border:0;width:10px;vertical-align:middle;letter-spacing:12px;"><svg width="34px" height="230px" version="1.1" xmlns="http://www.w3.org/2000/svg">
   <text transform="rotate(270)" style="text-anchor:end;" x="-121" y="10" fill="black" font-size="10" font-weight="normal" font-family="Arial">
   '.$infoOnYAxis.'</text>
</svg></td>';*/
                
                $html.='<td style="border:0;width:10px;vertical-align:middle;letter-spacing:12px;">&nbsp;</td>';
		$html.="<td style='border:0;'>";
		$html.='			
		<div  class="theBarGraph">
			<table class="theBarGraphTbl" style="border: solid #000 1px;"><tr>
		';
		$barNamesTbl='';
		$addBarNames=false;

		$widthOfColumn=100/($cols*$lnth + $lnth -1); //total no. of columns + no. of space columns (in %)
		
                $widthOfBarNameCol=floor(10000/$lnth)/100;
		for($i=0;$i<$lnth;$i++){
			if($i>0){
				$html.='<td style="width:'.$widthOfColumn.'%;"></td>';				
			}
                        if($isCollobrative==1){
			for($j=0;$j<1;$j++){
				$height=$oneStepHeight*(isset($valuesArray[$i]['values'][$j])?$valuesArray[$i]['values'][$j]-$minValue:0);				
				$html.='<td '.(isset($valuesArray[$i]['empty']) && $valuesArray[$i]['empty']?'class="emptyBar"':'').' style="border:0;height:296px;width:'.$widthOfColumn.'%;">
					<div class="graph-bar1">'.($height>=0?'
                                        <div><img src="./public/images/reports/graph-top'.($j+1).'.png" /></div>                                       
										<div class="graph-rep'.$j.'" style="height:'.$height.'px;" ><table class="graph-rep'.$j.' variables-bar-graph" style="height:'.$height.'px;"><tr style="height:'.$height.'px;"><td style="height:'.$height.'px;"></td></tr></table></div>
                                ':'').'</div>
				</td>';
			}
                        }else{
                           for($j=0;$j<$cols;$j++){
				$height=$oneStepHeight*(isset($valuesArray[$i]['values'][$j])?$valuesArray[$i]['values'][$j]-$minValue:0);				
				$html.='<td '.(isset($valuesArray[$i]['empty']) && $valuesArray[$i]['empty']?'class="emptyBar"':'').' style="border:0;height:296px;width:'.$widthOfColumn.'%;">
					<div class="graph-bar1">'.($height>=0?'
                                        <div><img src="./public/images/reports/graph-top'.($j+1).'.png" /></div>                                       
										<div class="graph-rep'.$j.'" style="height:'.$height.'px;" ><table class="graph-rep'.$j.' variables-bar-graph" style="height:'.$height.'px;"><tr style="height:'.$height.'px;"><td style="height:'.$height.'px;"></td></tr></table></div>
                                ':'').'</div>
				</td>';
			} 
                        }
			$barNamesTbl.='<td class="barNameCol" style="border:1px 0 0 0;border-top: 1px solid #000;width:77px">	';
			if(isset($valuesArray[$i]['name']) && $valuesArray[$i]['name']!=""){
				$addBarNames=true;
				$barNamesTbl.=$valuesArray[$i]['name'];
			}
			$barNamesTbl.='</td>';
		}
		
		$html.='
			</tr></table>';
		$html.="<td style='border:0;'>";
		$html.='<div class="barDesc" style="margin-top:'.($totalHeight/2).'px;margin-left:30px;"><table style="border:0;margin-left:30px;">';
		
		for($i=0;$i<count($barDes);$i++){
			$html.='
			<tr><td class="barDesc-'.$i.' barDescItem" style="border:0;padding:10px;">'.$barDes[$i].'</td></tr>
			';
		}
		$html.='</table></div>';
		
		$html.="</td>";
		$html .="</td></tr></table>";
		
		/*$html.= '<div>'.($addBarNames?'<div style="margin-left:-'.($widthOfColumn/2).'%;margin-right:-'.($widthOfColumn/2).'%;" class="barNameWrap">'.$barNamesTbl.'<div class="clear"></div></div>':'').'
			<table class="infoBelowGraph" style="border:1px solid #FFF;width:100%;margin-top:-2px;"><tr><td style="border-top: 1px solid #000;width:100%;">'.$infoBelowGraph.'</td></tr></table>			
		</div>
		';*/
		
		/*$html.= '<div>
			<table class="infoBelowGraph" style="border:1px solid #FFF;width:100%;margin-top:-2px;"><tr><td style="border-top: 1px solid #000;border-right: 1px solid #fff;width:100%;">'.$infoBelowGraph.'</td></tr></table>			
		</div>
		';*/
		$html.= '<div style="margin-top:-4px;">'.($addBarNames?'<div style="margin-top:-4px;margin-left:-'.($widthOfColumn/2).'%;margin-right:-'.($widthOfColumn/2).'%;" class="barNameWrap"><table style="margin-top:-4px;"><tr><td class="barNameCol" style="width:142px;border:0;"></td>'.$barNamesTbl.'<td class="barNameCol" style="width:82px;border:0;"></td></tr></table></div>':'').'
			<table class="infoBelowGraph" style="border:1px solid #FFF;width:100%;margin-top:-2px;"><tr><td style="border-top: 1px solid #000;border-right: 1px solid #fff;width:100%;">'.$infoBelowGraph.'</td></tr></table>
		</div>
		';		
		
		$html .="</div>";		
		return $html;
	}
	
        protected function getGraphHTMLRound2($valuesArray,$steps,$maxValue,$minValue=0,$barDes=array(),$infoBelowGraph="",$infoOnYAxis=""){
		$lnth=count($valuesArray);
                $isCollobrative=$this->config['isCollobrative'];
		if($lnth==0)
			return '';
		else if($lnth<5){
			$to=5-$lnth;
			$emptyArray=array();
			for($i=0;$i<count($valuesArray[0]['values']);$i++)
				$emptyArray[]=0;
			for($i=0;$i<$to;$i++){
				$valuesArray[]=array("values"=>$emptyArray,"empty"=>1);
			}
			$lnth=count($valuesArray);
		}
		$cols=count($valuesArray[0]["values"]);
		if($cols==0)
			return '';
		$extraTopSpaceInGraph=44;
		$oneStepHeight=18*4; //21 is the difference in 2 lines in image and we are adding 4 lines in one step
		$graphHeight=$oneStepHeight*($maxValue-$minValue)+$extraTopSpaceInGraph; // we are adding 2 line space as buffer
		$bottomBarHeight=50;
		$topBarHeight=30;
		$totalHeight=$topBarHeight+$bottomBarHeight+$graphHeight;
		$html='<div class="graphWrap" style="align:left">
		<table><tr><td style="border:0;padding:0;margin:0;">
		<table class="stepDesc" style="border:0;">';
		foreach($steps as $k=>$step){
			$html.='<tr class="graphSteps"><td style="border:0;padding:0;height:76px;vertical-align: bottom;" class="graphSteps"><span class="score-'.$k.'">'.$step.'</span></td></tr>';
		}
		$html.="</table></td>";
                
	//	$html.="<td style='border:0;'><div style='transform: rotate(270deg);'>".$infoOnYAxis."</div></td>";
		
		/*$html.='<td style="border:0;width:10px;vertical-align:middle;letter-spacing:12px;"><svg width="34px" height="230px" version="1.1" xmlns="http://www.w3.org/2000/svg">
   <text transform="rotate(270)" style="text-anchor:end;" x="-121" y="10" fill="black" font-size="10" font-weight="normal" font-family="Arial">
   '.$infoOnYAxis.'</text>
</svg></td>';*/
                
                $html.='<td style="border:0;width:10px;vertical-align:middle;letter-spacing:12px;">&nbsp;</td>';
		$html.="<td style='border:0;'>";
		$html.='			
		<div  class="theBarGraph">
			<table class="theBarGraphTbl" style="border: solid #000 1px;"><tr>
		';
		$barNamesTbl='';
		$addBarNames=false;

		$widthOfColumn=100/($cols*$lnth + $lnth -1); //total no. of columns + no. of space columns (in %)
		
                $widthOfBarNameCol=floor(10000/$lnth)/100;
		for($i=0;$i<$lnth;$i++){
			if($i>0){
				$html.='<td style="width:'.$widthOfColumn.'%;"></td>';				
			}
                       
                           for($j=0;$j<$cols;$j++){
				$height=$oneStepHeight*(isset($valuesArray[$i]['values'][$j])?$valuesArray[$i]['values'][$j]-$minValue:0);				
				$html.='<td'.(isset($valuesArray[$i]['empty']) && $valuesArray[$i]['empty']?'class="emptyBar"':'').' style="height:296px;width:'.$widthOfColumn.'%;">
					<div class="graph-bar1">'.($height>=0?'
                                        <div><img src="./public/images/reports/graph-top'.($j+1).'.png" /></div>                                       
										<div class="graph-rep'.$j.'" style="height:'.$height.'px;" ><table class="graph-rep'.$j.' variables-bar-graph" style="height:'.$height.'px;"><tr style="height:'.$height.'px;"><td style="height:'.$height.'px;"></td></tr></table></div>
                                ':'').'</div>
				</td>';
			} 
                        
			$barNamesTbl.='<td style="color: #273497;font-family: CalibriRegular;line-height:1;font-size:11px;width:85px;text-align:left;border:0px;border-top:solid 1px #000000;"><div>';
			if(isset($valuesArray[$i]['name']) && $valuesArray[$i]['name']!=""){
				$addBarNames=true;
				$barNamesTbl.=$valuesArray[$i]['name'];
			}
			$barNamesTbl.='</div></td>';
		}
		
		$html.='
			</tr></table>';
		$html.="<td style='border:0;'>";
		$html.='<div class="barDesc" style="margin-top:'.($totalHeight/2).'px;margin-left:30px;"><table style="border:0;margin-left:30px;">';
                
                for($i=0;$i<count($barDes);$i++){
                    if($i==0){
                    $style_icon='style="display:inline-block;width:15px;height:6px;margin-right:8px;background-color:#c13b37;"';
                    }else if($i==1){
                    $style_icon='style="display:inline-block;width:15px;height:6px;margin-right:8px;background-color:#df8d8c;"';    
                    }
			$html.='
			<tr><td  style="border:0;padding:10px;color: #1051a6;font-family: CalibriRegular;font-size: 12px;text-align: left;margin-left: 25%;padding-left: 15px;"><span '.$style_icon.'>&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;'.$barDes[$i].'</td></tr>
			';

		}
		$html.='</table></div>';
		
		$html.="</td>";
		$html .="</td></tr></table>";
		
		/*$html.= '<div>'.($addBarNames?'<div style="margin-left:-'.($widthOfColumn/2).'%;margin-right:-'.($widthOfColumn/2).'%;" class="barNameWrap">'.$barNamesTbl.'<div class="clear"></div></div>':'').'
			<table class="infoBelowGraph" style="border:1px solid #FFF;width:100%;margin-top:-2px;"><tr><td style="border-top: 1px solid #000;width:100%;">'.$infoBelowGraph.'</td></tr></table>			
		</div>
		';*/
		
		/*$html.= '<div>
			<table class="infoBelowGraph" style="border:1px solid #FFF;width:100%;margin-top:-2px;"><tr><td style="border-top: 1px solid #000;border-right: 1px solid #fff;width:100%;">'.$infoBelowGraph.'</td></tr></table>			
		</div>
		';*/
		$html.= '<div style="margin-top:-4px;">'.($addBarNames?'<div style="margin-top:-4px;margin-left:-'.($widthOfColumn/2).'%;margin-right:-'.($widthOfColumn/2).'%;" class="barNameWrap"><table style="margin-top:-4px;"><tr><td class="barNameCol" style="width:142px;border:0;"></td>'.$barNamesTbl.'<td class="barNameCol" style="width:82px;border:0;"></td></tr></table></div>':'').'
			<table class="infoBelowGraph" style="border:1px solid #FFF;width:100%;margin-top:-2px;"><tr><td style="border-top: 1px solid #000;border-right: 1px solid #fff;width:100%;">'.$infoBelowGraph.'</td></tr></table>
		</div>
		';		
		
		$html .="</div>";		
		return $html;
	}
        
        
        
	protected function recommendationsOnKpa(){
		$kpa_count=0;
		foreach($this->kpas as $kpa){
			$kpa_count++;			
	
			$section=array("sectionHeading"=>array("text"=>$kpa['KPA_name'],"config"=>array("repeatHead"=>1)),"sectionBody"=>array());
			$recomSection=array("sectionBody"=>array());
			
			if($kpa_count==1){
				$indexKey=$this->addIndex("Key Performance Area 1 to ".count($this->kpas));
				$section['indexKey']=$indexKey;
			}
			
			$textBlock['score_1']=array(
						"blockHeading"=>array("data"=>array(array("text"=>'Recommendations for areas that <span class="color-red italic">needs attention</span>',"style"=>"brownHead"))),
						"blockBody"=>array(
							"dataArray"=>array(
							)
						),
						"style"=>"rTextblock"
					);
			$textBlock['score_2']=array(
						"blockHeading"=>array("data"=>array(array("text"=>'Recommendations for areas that <span class="color-yellow italic">are variable</span>',"style"=>"brownHead"))),
						"blockBody"=>array(
							"dataArray"=>array(
							)
						),
						"style"=>"rTextblock"
					);
			$textBlock['score_3']=array(
						"blockHeading"=>array("data"=>array(array("text"=>'Recommendations for areas that <span class="color-green italic">are good</span>',"style"=>"brownHead"))),
						"blockBody"=>array(
							"dataArray"=>array(
							)
						),
						"style"=>"rTextblock"
					);
			
			$keyQ_count=0;
			$coreQsInKPA=0;
			if(isset($this->keyQuestions[$kpa['kpa_instance_id']])){
				$numberToAlpha=array(1=>"a",2=>"b",3=>"c",4=>"d");
				foreach($this->keyQuestions[$kpa['kpa_instance_id']] as $keyQ){
					$keyQ_count++;
					
					$keyQBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array("Key Question $keyQ_count",array("text"=>"&nbsp;","rSpan"=>6),array("text"=>$keyQ['key_question_text'],"cSpan"=> isset($this->coreQuestions[$keyQ['key_question_instance_id']])?count($this->coreQuestions[$keyQ['key_question_instance_id']]):0 ,"style"=>(isset($keyQ['externalRating'])?"score-bg-".$keyQ['externalRating']['score']." scoreB-".$keyQ['externalRating']['score']:""))),
								array('Sub Questions'),
								array(array("text"=>"Outstanding","style"=>"score-4")),
								array(array("text"=>"Good","style"=>"score-3")),
								array(array("text"=>"Variable","style"=>"score-2")),
								array(array("text"=>"Needs Action","style"=>"score-1"))
							)
						),
						"style"=>"keyQblock",
						"config"=>array("minRows"=>6)
					);
										
					$coreQ_count=0;
					if(isset($this->coreQuestions[$keyQ['key_question_instance_id']])){
						foreach($this->coreQuestions[$keyQ['key_question_instance_id']] as $coreQ){
							$coreQ_count++;
							$coreQsInKPA++;
							$keyQBlock['blockBody']['dataArray'][1][]=array("text"=>'<span class="min-h">'.$coreQsInKPA.". ".$coreQ['core_question_text'].'</span>',"style"=>(isset($coreQ['externalRating'])?"scoreB-".$coreQ['externalRating']['score']:""));
							$satatement_count=0;
							$values=array(1=>array(),2=>array(),3=>array(),4=>array());
							foreach($this->judgementStatement[$coreQ['core_question_instance_id']] as $statment){
								$satatement_count++;
								if(isset($statment['externalRating'])){
									$values[$statment['externalRating']['score']][]=$coreQsInKPA.$numberToAlpha[$satatement_count];
								}
								
								if(isset($this->recommendationText[$statment['judgement_statement_instance_id']]) ){
									$recm='<b>'.$coreQsInKPA.$numberToAlpha[$satatement_count].'. '.$statment['judgement_statement_text'].'</b><div class="italic">Recommendation for improvement - </div>';
									
									$rows=array(1=>"",2=>"",3=>"");
									foreach($this->recommendationText[$statment['judgement_statement_instance_id']] as $text){
										if($text['rating_id']<4)
											$rows[$text['rating_id']].='<div class="recmText">&#8226; '.$text['recommendation_text']."</div>";
									}
									foreach($rows as $k=>$row){
										if($row!="")
											$textBlock['score_'.$k]['blockBody']['dataArray'][][]=$recm.$row;
									}
								}
							}
							foreach($values as $k=>$v)
								if($k>0)
									$keyQBlock['blockBody']['dataArray'][6-$k][]=array("text"=>implode(", ",$v),"style"=>"score-bg-".$k);
						}
					}
					$section['sectionBody'][]=$keyQBlock;
				}
				foreach($textBlock as $blk){
					if(count($blk['blockBody']['dataArray']))
						$recomSection['sectionBody'][]=$blk;
				}
			}
			$this->sectionArray[]=$section;
			if(count($recomSection['sectionBody']))
				$this->sectionArray[]=$recomSection;
		}
	}
        
        protected function JDistanceOnKpa($lang_id=DEFAULT_LANGUAGE,$return=0){
            
                $indexKey=$this->addIndex("Judgement Distance Between Self Review and External Review");
                //$indexKey='';
                $headstyle=$this->reportId==1?'greyHead':'';
		$section=array("sectionHeading"=>array("text"=>"Judgement Distance Between Self Review and External Review","style"=>$headstyle),"sectionBody"=>array(),'indexKey'=>$indexKey);
		
		$kpaValuesForGraph=array();
		$kpa_count=0;
		
		
		
		$textBlock=array(
					"blockBody"=>array(
						"dataArray"=>array(
							array("The following graph depicts the level of agreements and disagreements the SSRE team shared with the SERE team on the judgment statements. Limitations (explained in the next section) of the use of the diagnostic may also contribute to agreements and difference in agreements between the SSRE and
        SERE, which is often indicative of the level of experience between the two teams regarding global standards.<br><br><br>")
						)
					),
				"style"=>"textBlock"
			);
		$section['sectionBody'][]=$textBlock;
		
		//$this->sectionArray[]=$section;
                
		$kpa_count=0;
                $i = 0;
                $urlArray = array();
                $rankArray = array();
                $url_count = 0;
		$url = array("");
                $graph = '';
		foreach($this->kpas as $kpa){
                    $jd0=$jd1=$jd2=$jd3=0;
			$kpa_count++;			
                        $i++;
			$keyQ_count=0;
			$coreQsInKPA=0;
			if(isset($this->keyQuestions[$kpa['kpa_instance_id']])){
				$numberToAlpha=array(1=>"a",2=>"b",3=>"c",4=>"d");
				foreach($this->keyQuestions[$kpa['kpa_instance_id']] as $keyQ){
					$keyQ_count++;
					
										
					$coreQ_count=0;
					if(isset($this->coreQuestions[$keyQ['key_question_instance_id']])){
						foreach($this->coreQuestions[$keyQ['key_question_instance_id']] as $coreQ){
							$coreQ_count++;
							$coreQsInKPA++;
							$keyQBlock['blockBody']['dataArray'][1][]=array("text"=>'<span class="min-h">'.$coreQsInKPA.". ".$coreQ['core_question_text'].'</span>',"style"=>(isset($coreQ['externalRating'])?"scoreB-".$coreQ['externalRating']['score']:""));
							$satatement_count=0;
							$values=array(1=>array(),2=>array(),3=>array(),4=>array());
							foreach($this->judgementStatement[$coreQ['core_question_instance_id']] as $statment){
                                                            //echo "<pre>";print_r($statment);
								$satatement_count++;
								if(isset($statment['externalRating']) && isset($statment['internalRating'])){
                                                                    
                                                                    
                                                                    $jd = abs($statment['externalRating']['score']-$statment['internalRating']['score']);
                                                                    switch($jd){
                                                                                case 0 : $jd0++;
                                                                                break;
                                                                                case 1 : $jd1++;
                                                                                break;
                                                                                case 2 : $jd2++;
                                                                                break;
                                                                                case 3 : $jd3++;
                                                                                break;
                                                                    }
                                                                   /* if(isset($statment['externalRating']['rating']) && $statment['externalRating']['rating'] == 'Mostly'){
                                                                        $jsm++;
                                                                    }else if(isset($statment['externalRating']['rating']) && $statment['externalRating']['rating'] == 'Always'){
                                                                        $jsa++;
                                                                    }else if(isset($statment['externalRating']['rating']) && $statment['externalRating']['rating'] == 'Sometimes'){
                                                                        $jss++;
                                                                    }else if(isset($statment['externalRating']['rating']) && $statment['externalRating']['rating'] == 'Rarely'){
                                                                        $jsr++;
                                                                    }*/
									//$values[$statment['externalRating']['score']][]=$coreQsInKPA.$numberToAlpha[$satatement_count];
								}
								
								
							}
							
						}
					}
				}
				
			}
			
                        
                        $weightage=0;						
                        $weightage = $jd0*4 +$jd1*3 +$jd2*2 +$jd3*1;
                        $kpaName = str_replace(' & ', '_', $kpa['KPA_name']);
                        $rankArray[$kpaName] = $weightage;
                        
                        //$jsArr[$kpa['kpa_instance_id']] = array('sometimes'=>$jss,'always'=>$jsa,'mostly'=>$jsm,'rarely'=>$jsr);
                        $urlArray[$kpaName] =$kpaName."=".urlencode($kpa['KPA_name']).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
                         //echo "<pre>";print_r($urlArray);die;
                        
		}
                            
                                arsort($rankArray,SORT_NUMERIC);				
				//url	
                               // echo "<pre>";print_r($urlArray);
				$isGraphPage1 = 1;
                                $k = 0;
				foreach($rankArray as $key=>$val){					
					$k++;
					
					$url[$url_count] .= $urlArray[$key];
				}				
				$this->rankArray = $rankArray;
				//print_r($url);die;
				//$uri = $this->createURISqJdTeachers_AccuracySelfReview();
				$graph='';
				$this->tableNum++;
				for($i=0;$i<=$url_count;$i++){
                                  //echo  $url[$i];
                                        $file=''.$url[$i] . '&jd=1&lang_id='.$lang_id.'';
					//$response = file_get_contents($file, false);
                                        $data = explode("&",$file);
                                        $result=$this->curlResultAction('' . SITEURL . 'library/stacked.chart_jd.php',$data);
                                        $graph .= '<div style="page-break-inside:avoid;"><div style="text-align:center;font-weight:bold;"><span style="font-weight:normal;">Table '.$this->tableNum.'</span>
                                            <br/>Judgement distance between the self-review and external review ratings arranged from highest to lowest</div><img style="border:solid 0px #000000;" src="data:image/png;base64,'.base64_encode($result).'" /></div>';													
				}
                                //echo $graph;
                                //die();
                                $comparisonSection = array();
                                
                                
                                $graphBlock=array(
						
						"blockBody"=>array(
							"dataArray"=>array(
								array($graph)
							)
						),
					"style"=>"bordered barGraph"
				);
                               // $this->sectionArray[]=$section;
                                $section['sectionBody'][]=$graphBlock;
                              
                                
			
			    //$this->sectionArray[]=$section;
                            if($return==1){
                            return $section;
                            }else{
                            $this->sectionArray[]=$section;    
                            }
                        //echo $graph;
                        
				// $graph;
				//$pdf->writeHTMLCell ( 0, 0, '', '', $html.$graph, 0, 1, 0, true, 'J', true );
                //echo $jsm;
               
	}
        
        protected function JRatingOnKpa($lang_id=DEFAULT_LANGUAGE,$diagnosticLabels=array(),$return=0){
                $isCollobrative=$this->config['isCollobrative'];                                                                
                $headstyle=$this->reportId==1?'greyHead':'';
                //$indexKey=$this->addIndex("Rating Performance Data At KPA Level From Highest To Lowest");
		//$section=array("sectionHeading"=>array("text"=>"Rating Performance Data At KPA Level From Highest To Lowest","style"=>$headstyle),"sectionBody"=>array(),'indexKey'=>$indexKey);
		if($isCollobrative==1){
                 $indexKey=$this->addIndex("Rating analysis at Judgement Statement level across KPAs arranged from highest to lowest");
                $section=array("sectionHeading"=>array("text"=>"Rating analysis at Judgement Statement level across KPAs arranged from highest to lowest","style"=>$headstyle),"sectionBody"=>array(),'indexKey'=>$indexKey);
		   
                }else{
                
                
                 $indexKey=$this->addIndex("Rating Performance Data At KPA Level From Highest To Lowest");
                $section=array("sectionHeading"=>array("text"=>"Rating Performance Data At KPA Level From Highest To Lowest","style"=>$headstyle),"sectionBody"=>array(),'indexKey'=>$indexKey);
		   
                }
		$kpaValuesForGraph=array();
		$kpa_count=0;
		
		$kpa_count=0;
                $i = 0;
                $urlArray = array();
                $rankArray = array();
                $url_count = 0;
				$url = array("");
                                
		foreach($this->kpas as $kpa){
                    $jsm=$jss=$jsa=$jsr=0;
			$kpa_count++;			
                        $i++;
			
			
			$keyQ_count=0;
			$coreQsInKPA=0;
			if(isset($this->keyQuestions[$kpa['kpa_instance_id']])){
				$numberToAlpha=array(1=>"a",2=>"b",3=>"c",4=>"d");
				foreach($this->keyQuestions[$kpa['kpa_instance_id']] as $keyQ){
					$keyQ_count++;
					
										
					$coreQ_count=0;
					if(isset($this->coreQuestions[$keyQ['key_question_instance_id']])){
						foreach($this->coreQuestions[$keyQ['key_question_instance_id']] as $coreQ){
							$coreQ_count++;
							$coreQsInKPA++;
							$keyQBlock['blockBody']['dataArray'][1][]=array("text"=>'<span class="min-h">'.$coreQsInKPA.". ".$coreQ['core_question_text'].'</span>',"style"=>(isset($coreQ['externalRating'])?"scoreB-".$coreQ['externalRating']['score']:""));
							$satatement_count=0;
							$values=array(1=>array(),2=>array(),3=>array(),4=>array());
							foreach($this->judgementStatement[$coreQ['core_question_instance_id']] as $statment){
                                                           //print_r($statment);
								$satatement_count++;
								if(isset($statment['externalRating'])){
                                                                    if(isset($statment['externalRating']['score']) && $statment['externalRating']['score'] == '3'){
                                                                        $jsm++;
                                                                    }else if(isset($statment['externalRating']['score']) && $statment['externalRating']['score'] == '4'){
                                                                        $jsa++;
                                                                    }else if(isset($statment['externalRating']['score']) && $statment['externalRating']['score'] == '2'){
                                                                        $jss++;
                                                                    }else if(isset($statment['externalRating']['score']) && $statment['externalRating']['score'] == '1'){
                                                                        $jsr++;
                                                                    }
									$values[$statment['externalRating']['score']][]=$coreQsInKPA.$numberToAlpha[$satatement_count];
								}
								
								
							}
							
						}
					}
				}
				
			}
			
                        
                        $weightage=0;						
                        $weightage = $jss*4 +$jsa*3 +$jsm*2 +$jsr*1;
                        $kpaName = str_replace(' & ', '_', $kpa['KPA_name']);
                        $rankArray[$kpaName] = $weightage;
                        
                        $jsArr[$kpa['kpa_instance_id']] = array('sometimes'=>$jss,'always'=>$jsa,'mostly'=>$jsm,'rarely'=>$jsr);
                        $urlArray[$kpaName] =$kpaName."=".urlencode($kpa['KPA_name']).';'.$jsa.';'.$jss.';'.$jsm.';'.$jsr.'&';
                         //echo "<pre>";print_r($urlArray);die;
                        
		}
                            
                                arsort($rankArray,SORT_NUMERIC);				
				//url	
                               // echo "<pre>";print_r($urlArray);
				$isGraphPage1 = 1;
                                $k = 0;
				foreach($rankArray as $key=>$val){					
					$k++;
					
					$url[$url_count] .= $urlArray[$key];
				}				
				$this->rankArray = $rankArray;
				//print_r($url);die;
				//$uri = $this->createURISqJdTeachers_AccuracySelfReview();
				$graph='';
				$this->tableNum++;
				for($i=0;$i<=$url_count;$i++){
                                  //echo  $url[$i];
                                  $file=''.$url[$i] . '&lang_id='.$lang_id.'';
					//$response = file_get_contents($file, false);
                                        $data = explode("&",$file);
                                        
                                        $result=$this->curlResultAction('' . SITEURL . 'library/stacked.chart_jr.php',$data);

					/*$graph .= '<div style="page-break-inside:avoid;"><div style="text-align:center;font-weight:bold;"><span style="font-weight:normal;">Table '.$this->tableNum.'</span><br/> Rating analysis at KPA level arranged from
highest to lowest</div><img style="border:solid 0px #000000;" src="data:image/png;base64,'.base64_encode($result).'" /></div>';	*/
                                if($isCollobrative==1){
                                    $graph .= '<div style="page-break-inside:avoid;"><div style="text-align:center;font-weight:bold;"><span style="font-weight:normal;">Table '.$this->tableNum.'</span><br/> Rating analysis at Judgement Statement level across KPAs arranged from highest to lowest</div><img style="border:solid 0px #000000;" src="data:image/png;base64,'.base64_encode($result).'" /></div>';
                                }else{
					$graph .= '<div style="page-break-inside:avoid;"><div style="text-align:center;font-weight:bold;"><span style="font-weight:normal;">Table '.$this->tableNum.'</span><br/> Rating analysis at KPA level arranged from
highest to lowest</div><img style="border:solid 0px #000000;" src="data:image/png;base64,'.base64_encode($result).'" /></div>';
                                }
				}
                                $comparisonSection = array();
                                 
                                $graphBlock=array(
						
						"blockBody"=>array(
							"dataArray"=>array(
								array($graph)
							)
						),
					"style"=>"bordered barGraph"
				);
                                
			$section['sectionBody'][]=$graphBlock;
			
				$block=array(
				"blockHeading"=>array(
						"data"=>array("Code","Performance Level","What this means:")
					),
					"blockBody"=>array(
						"dataArray"=>array(
								array(array("text"=>"&nbsp;","style"=>"score-bg-4"),isset($diagnosticLabels['Always'])?$diagnosticLabels['Always']:"Always",isset($diagnosticLabels['Always_value'])?$diagnosticLabels['Always_value']:"There is evidence of robust systems of good practice throughout the school, across all sections from the beginning of the academic year to the end.  These are documented and known to all stakeholders."),
								array(array("text"=>"&nbsp;","style"=>"score-bg-3"),isset($diagnosticLabels['Mostly'])?$diagnosticLabels['Mostly']:"Mostly",isset($diagnosticLabels['Mostly_value'])?$diagnosticLabels['Mostly_value']:"There is evidence of systemic good practice in most part of the school, in most of the sections for most of the year.  Everyone in the school is aware of these systems and processes."),
								array(array("text"=>"&nbsp;","style"=>"score-bg-2"),isset($diagnosticLabels['Sometimes'])?$diagnosticLabels['Sometimes']:"Sometimes",isset($diagnosticLabels['Sometimes_value'])?$diagnosticLabels['Sometimes_value']:"There is evidence of good practice in some parts of the school, in some of the sections for some time of the year.  It is known to few stakeholders in the school."),
								array(array("text"=>"&nbsp;","style"=>"score-bg-1"),isset($diagnosticLabels['Rarely'])?$diagnosticLabels['Rarely']:"Rarely",isset($diagnosticLabels['Rarely_value'])?$diagnosticLabels['Rarely_value']:"There is little or no evidence of good practise in the school.")
							)
					),
					"style"=>"bordered keysForRecmBlock"
				);
		$section['sectionBody'][]=$block;
			
			//$comparisonSection['sectionBody'][]=$keysBodyBlock;
			//$this->sectionArray[]=$section;
                        //return $section;
                        //echo $graph;
                        if($return==1){
                            return $section;
                            }else{
                            $this->sectionArray[]=$section;    
                            }
                        
				// $graph;
				//$pdf->writeHTMLCell ( 0, 0, '', '', $html.$graph, 0, 1, 0, true, 'J', true );
                //echo $jsm;
               
	}
	
        public function curlResultAction($url,$data){
            
             $params = '';
             foreach($data as $key=>$value)
             $params .= $value.'&';   
             $ch = curl_init();
             curl_setopt($ch,CURLOPT_URL, $url);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
             curl_setopt($ch,CURLOPT_POST, count($data));
             curl_setopt($ch,CURLOPT_POSTFIELDS, $params);
             $result=curl_exec($ch);
             curl_close($ch);
             
             return $result;
             
        }


        protected function schoolEffectivenessInApplyingSelfReview(){
		$indexKey=$this->addIndex("School's effectiveness in applying the Self-Review Diagnostic");
		$section=array("sectionHeading"=>array("text"=>"School's effectiveness in applying the self-review diagnostic"),"sectionBody"=>array(),'indexKey'=>$indexKey);
		
		$kpaComparisonBlock=array(
								"blockHeading"=>array(
									"data"=>array("Key Performance Area (KPA)","SSRE Judgements<br>(Self-Review)","SERE Judgements<br>(External Review)")
								),
								"blockBody"=>array(
									"dataArray"=>array()
								),
							"style"=>"bordered comparisonBlock lineHeightSm mb0"
						);
		$kpaValuesForGraph=array();
		$kpa_count=0;
		foreach($this->kpas as $kpa){
			$kpa_count++;
			$kpaValuesForGraph[]=array("values"=>array(isset($kpa['internalRating'])?$kpa['internalRating']['score']:0,isset($kpa['externalRating'])?$kpa['externalRating']['score']:0),"name"=>$kpa['KPA_name']);
			$keyQ_count=0;
			$kpaComparisonBlock['blockBody']['dataArray'][]=array(
																$kpa['KPA_name'],
																'<span class="'.(isset($kpa['internalRating'])?"score-".$kpa['internalRating']['score']:"").'">'.(isset($kpa['internalRating'])?$kpa['internalRating']['rating']:"").'</span>',
																'<span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"")."</span>"
																);
		}
		$section['sectionBody'][]=$kpaComparisonBlock;
		
		$textBlock=array(
					"blockBody"=>array(
						"dataArray"=>array(
							array("When the numerical scores for the external review results were applied to the Adhyayan Quality Standard rubric, the school was identified as achieving $this->awardName status."),
							array("The following graph identifies the level of agreement between the SSRE and SERE teams.")
						)
					),
				"style"=>"textBlock"
			);
		$section['sectionBody'][]=$textBlock;
		
		$graphBlock=array(
					"blockHeading"=>array(
						"data"=>array("JUDGEMENT DISTANCE ON KPAs")
					),
					"blockBody"=>array(
						"dataArray"=>array(
							array($this->getGraphHTML($kpaValuesForGraph,array(4=>"Outstanding",3=>"Good",2=>"Variable",1=>"Needs Attention"),4,1,array("SSRE","SERE"),"				
Key Performance Areas (KPAs)","Grades"))
						)
					),
				"style"=>"bordered barGraph"
			);
		$section['sectionBody'][]=$graphBlock;
		
		$textBlock=array(
					"blockBody"=>array(
						"dataArray"=>array(
							array("The most effective schools are consistently accurate in judging their own performance. Their judgements are often the same or only one level different to that of the Adhyayan SERE team. As a consequence, each school should review the robustness of the evidence they have collected where there is more than one level difference between the SSRE and SERE judgements. "),
							array('Adhyayan\'s experience is that schools that choose to review themselves are keen to give students the most successful education experience possible. They desire and strive to constantly improve and embrace challenge and change. In preparing to set school improvement targets it will be important forthe school to study the diagnostic carefully. Some exemplars on the Adhyayan website that would help can be found on <a target="_blank" href="http://adhyayan.asia/site/ssre-image-gallery/" style="text-decoration:underline;">http://adhyayan.asia/site/ssre-image-gallery/</a>')
						)
					),
				"style"=>"textBlock"
			);
		$section['sectionBody'][]=$textBlock;
		
		$this->sectionArray[]=$section;
	}
	
	protected function assessorKeyNotes(){
		if(count($this->keyNotes)==0)
			return;
		$indexKey=$this->addIndex("Assessor Key Notes");
		$section=array("sectionHeading"=>array("text"=>"Assessor Key Notes for Celebrations & Improvements across ".count($this->kpas)." Key Performance Areas (KPAs) "),"sectionBody"=>array(),'indexKey'=>$indexKey);

		foreach($this->kpas as $kpa){
			if(isset($this->keyNotes[$kpa['kpa_instance_id']])){
				$block=array(
						"blockHeading"=>array(
							"data"=>array(array("text"=>$kpa['KPA_name'],"cSpan"=>2))
						),
						"blockBody"=>array(
							"dataArray"=>array()
						),
						"style"=>"bordered keyNotesBlock"
					);
				$kn_count=0;
				foreach($this->keyNotes[$kpa['kpa_instance_id']] as $kn){
					$kn_count++;
					$block['blockBody']['dataArray'][]=array($kn_count.".",$kn['text_data']);
				}
				$section['sectionBody'][]=$block;
			}
		}
		if(count($section['sectionBody']))
			$this->sectionArray[]=$section;
	}
	
	protected function adhayayanQualityStandard(){
		$indexKey=$this->addIndex("The Adhyayan Quality Standard Awarded");
		$section=array("sectionHeading"=>array("text"=>"The Adhyayan Quality Standard Awarded"),"sectionBody"=>array(),'indexKey'=>$indexKey);
		
		$block=array(
				"blockBody"=>array(
					"dataArray"=>array(
						array('Following an external review undertaken by the Adhyayan team, we are delighted to award <b>'.$this->aqsData['school_name'].', '.$this->aqsData['school_address'].'</b> with the Adhyayan Quality Standard <b>'.$this->awardName.'</b> Congratulations!<br><br>'),
						array('This award is valid until '.$this->validDate.', at which point the school will require a subsequent external review. If the school wishes, it can identify an earlier date for external review to determine its performance.'),
					)
				),
				"style"=>"textBlock"
			);
		$section['sectionBody'][]=$block;
		$indexKey=$this->addIndex("The Adhyayan Quality Standard Award Process");
		$block=array(
				"blockHeading"=>array(
					"data"=>array(array("text"=>'The Adhyayan Quality Standard Award Process',"style"=>"brownHead"))
				),
				'indexKey'=>$indexKey,
				"blockBody"=>array(
					"dataArray"=>array(
						array('<ol style="list-style-type: decimal;padding-left: 23px;">
             		<li  >During the school\'s orientation day, its School Self-Review & Evaluation (SSRE) Team was trained on how to use Adhyayan\'s School Review Diagnostic. The SSRE Team was informed of the importance of <i>making collective decisions based on substantiated, clearly available and visible evidence.</i></li><br>
             		<li>The school divided its SSRE Team into three teams. Each team undertook separate self-reviews focussing on two Key Performance Areas (KPAs). Their judgements were then distilled into a single judgement of the school\'s performance against each of the Adhyayan Key Performance Areas (KPAs).</li><br>
             		<li>The SERE Team awarded the school its Adhyayan Quality Standard Award.</li><br>
             		<li>The SSRE and SERE teams:<br><br>
             			<ol style="list-style-type: circle;margin-left: 15px;padding:0;">
             				<li>Shared their judgements on each of the KPAsand the evidence on which they were based</li><br>
             				<li>Discussed the similarities and differences in the judgement of the SSRE and SERE Teams</li><br>
             				<li>Identified key areas for celebration and improvement</li> <br>
             				<li>Began planning for improvement based on the self and external review findings</li>		
             			</ol>
             		</li>
             	</ol>'),
						array('The following report provides a confidential summary of the achievements and areas for development identified through the AQS process. It contains recommendations for school improvement within each of the KPAs.'),
					)
				),
				"style"=>"textBlock"
			);
		$section['sectionBody'][]=$block;
				
		$section['sectionBody'][]=$this->aqsData['award_scheme_id']==2?$this->qualityStandardDefinitionBlock_schemeDonBosco():$this->qualityStandardDefinitionBlock_standard();
		
		$this->sectionArray[]=$section;
	}
	
	protected function qualityStandardDefinitionBlock_schemeDonBosco(){
		$indexKey=$this->addIndex("Adhyayan Quality Standard Definitions");
		$block=array(
				"blockHeading"=>array(
					"data"=>array(array("text"=>'The Adhyayan Quality Standard Award Process',"style"=>"brownHead","cSpan"=>4))
				),
				'indexKey'=>$indexKey,
				"blockBody"=>array(
					"dataArray"=>array(
						array(array("text"=>'Benchmarked at','style'=>"head-td"),array("text"=>'Getting to Good','style'=>"head-td"),array("text"=>'Good Schools','style'=>"head-td"),array("text"=>'Outstanding Schools','style'=>"head-td"))
					)
				),
				"config"=>array("minRows"=>5),
				"style"=>"bordered qualityDefDonBos"
			);
		if(count($this->awardSchemes)==10){
			$block['blockBody']['dataArray'][]=array("International",$this->awardSchemes[8]['award_name'],$this->awardSchemes[9]['award_name'],$this->awardSchemes[10]['award_name']);
			$block['blockBody']['dataArray'][]=array("National",$this->awardSchemes[5]['award_name'],$this->awardSchemes[6]['award_name'],$this->awardSchemes[7]['award_name']);
			$block['blockBody']['dataArray'][]=array(array("text"=>"State","rSpan"=>2),$this->awardSchemes[2]['award_name'],$this->awardSchemes[3]['award_name'],$this->awardSchemes[4]['award_name']);
			$block['blockBody']['dataArray'][]=array(array("text"=>$this->awardSchemes[1]['award_name'],"cSpan"=>3,"style"=>"text-center"));
		}
		return $block;
	}
	
	protected function qualityStandardDefinitionBlock_standard(){
		$indexKey=$this->addIndex("Adhyayan Quality Standard Definitions");
		$block=array(
				"blockHeading"=>array(
					"data"=>array(array("text"=>'The Adhyayan Quality Standard Award Process',"style"=>"brownHead","cSpan"=>2))
				),
				'indexKey'=>$indexKey,
				"blockBody"=>array(
					"dataArray"=>array(
						array('<img  src="/assets/images/platinum.png" height="40" width="40"/>','<b>Platinum:</b> The Platinum award confirms that the school\'s performance is outstanding on most KPAs as measured against their chosen tier.'),
						array('<img  src="/assets/images/gold.png" height="40" width="40"/>','<b>Gold:</b>Gold is the bedrock of the Adhyayan Quality Standard. The Gold award confirms that the school\'s performance is good and strong on most KPAs.'),
						array('<img  src="/assets/images/silver.png" height="40" width="40"/>','<b>Silver:</b> The Silver award indicates that the school\'s practice is variable. The Silver award confirms that while some aspects of the school\'s performance may be good, others may be satisfactory or less.'),
						array('<img  src="/assets/images/bronze.png" height="40" width="40"/>','<b>Bronze:</b> The Bronze award is an entry grade for schools aspiring to become high performing. While the school confirms that certain aspects of its performance within the KPAs are at least satisfactory, it is working towards greater consistency.')
					)
				),
				"style"=>"border-outer qualityDefStand"
			);
		return $block;
	}
	
	protected function aSchoolImplovementJourney(){
		$indexKey=$this->addIndex("A School Improvement Journey");
		$section=array("sectionHeading"=>array("text"=>"A School Improvement Journey "),"sectionBody"=>array(),'indexKey'=>$indexKey);
		$block=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array("The Adhyayan Mantra:"),
								array(array("text"=>'It\'s all about execution.<br>
It is not enough to get the ideas right; they have to be adopted.<br>
And it is not enough to adopt them; they have to be implemented.<br>
And it is not enough to implement them correctly; they have to be constantly reviewed and adjusted over time as we see what works and what doesn\'t."',"style"=>"italic text-center")),
								array(array("text"=>'NandanNilekani<br><br><br>',"style"=>"text-bold italic text-right")),
								array("The Adhyayan Mantra describes the spiral of school improvement. It begins with a vision that is transformed into a plan. Once implemented it is regularly reviewed and annually revised. It is the task of the school's leadership and management, supported by its staff, students andparent body to prioritise what is important, what is urgent and needs to be done straight away, and what will have the biggest impact on the quality of teaching, learning and achievement.<br><br>"),
								array("The <strong>Quality Dialogue</strong> was effective in establishing:<br><br>"),
								array('<ol style="list-style-type:lower-alpha;padding-left:30px;line-height: 25px;">
								 <li>The need for consistency of practice across all KPAs.</li>
								 <li>The importance of collecting tangible evidence to ensure accurate, evidence-based, judgements </li>
								 <li>The benefit of a diagnostic that has the capacity to achieve common understanding of \'what good looks like\'if applied throughout the school year</li>
								 <li>The school\'s ability to recognise the need for consistency of practice across all classes</li>
								 <li>The importance of each of the words in the diagnostic and the thoughtful interpretation of the terms</li>
							 </ol><br>'),
								array('Following the AQS process, the school recognises that for improvement to be successful and sustained,its leadership team understands the importance of:<br><br>'),
								array('<ol style="list-style-type:lower-alpha;padding-left:30px;line-height: 25px;"> 
									 <li>Planning and documentation.</li>
									 <li>The visibility of the school\'s leadership and direction. </li>
									 <li>Clarity in staff about their responsibilities in turning planning into effective implementation</li>
									 <li>The important role that students play in shaping schools</li>
									 <li>The monitoring, reporting and evaluation of the impact of actions undertaken within each KPA</li>
								 </ol><br>'),
								 array('When the school is deciding whether to include any of the recommendations as a priority for action in its improvements, it should ask itself the question,<br><br><br>'),
								array(array("text"=>"'What impact will this action have on the quality of children's understanding, confidence, learning, progress or<br> achievement?'<br><br><br>","style"=>"italic text-center")),
								array('This document defines the strengths and the areas for development of <b>'.$this->aqsData['school_name'].', '.$this->aqsData['school_address'].'</b>. It will be important for the school to embrace the lessons learned during the review process, so that the plans it creates will enable it to achieve the next level. The school should be thoughtful and sparing about what it includes within the plan to ensure it can achieve its objectives.')
							)
						),
						"style"=>"onlytext"
					);
		$section['sectionBody'][]=$block;
		$this->sectionArray[]=$section;
	}
	
	protected function keyForReadingRecommendationReport(){
		$indexKey=$this->addIndex("Key for Reading the KPA Report");
		$section=array("sectionHeading"=>array("text"=>"Key for Reading the Report"),"sectionBody"=>array(),'indexKey'=>$indexKey);
		
		$block=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array("This colour coding system below, which is used throughout this document, is designed to highlight areas for both celebration and improvement.")
							)
						),
						"style"=>"onlytext"
					);
		$section['sectionBody'][]=$block;
		
		$block=array(
				"blockHeading"=>array(
						"data"=>array("Code","Performance Level","What this means:")
					),
					"blockBody"=>array(
						"dataArray"=>array(
							array(array("text"=>"&nbsp;","style"=>"score-bg-4"),"Outstanding","Best practice is consistently and visibly embedded in the culture of the school, is documented well and known to all stakeholders."),
							array(array("text"=>"&nbsp;","style"=>"score-bg-3"),"Good","There are consistently visible examples of good practice that have become part of the school's culture and are known to all stakeholders. The leadership and management ensures secure system and processes."),
							array(array("text"=>"&nbsp;","style"=>"score-bg-2"),"Variable","Some examples of good practice are visible. These are not embedded in school culture and are known or practiced by only a few."),
							array(array("text"=>"&nbsp;","style"=>"score-bg-1"),"Needs Attention","Actions need to be taken immediately. There is little or no evidence of good practice in the school.")
						)
					),
					"style"=>"bordered keysForRecmBlock"
				);
		$section['sectionBody'][]=$block;
		
		$this->sectionArray[]=$section;
	}
	
	protected function generateSection_ComGraphForTA(){
		$indexKey=$this->addIndex("Graph comparison");
		$section=array("sectionHeading"=>array("text"=>""),"sectionBody"=>array(),"indexKey"=>$indexKey);
		foreach($this->kpas as $kpa){
			$kq_cnt=0;
			if(isset($this->keyQuestions[$kpa['kpa_instance_id']])){
				$figCount=0;
				foreach($this->keyQuestions[$kpa['kpa_instance_id']] as $keyQ){
					$kq_cnt++;
					$externalPoints=array();
					$internalPoints=array();
					$js_cnt_in_kq=0;
					$bottomLabels="";
					$diffInScore=array(0,0,0,0);
					if(isset($this->coreQuestions[$keyQ['key_question_instance_id']])){
						foreach($this->coreQuestions[$keyQ['key_question_instance_id']] as $coreQ){
							foreach($this->judgementStatement[$coreQ['core_question_instance_id']] as $statment){
								$js_cnt_in_kq++;
								$internalPoints[]=(10 * $js_cnt_in_kq).",".(95 - (20*(isset($statment['internalRating'])?$statment['internalRating']['score']:0)));
								$externalPoints[]=(10 * $js_cnt_in_kq).",".(95-(20*(isset($statment['externalRating'])?$statment['externalRating']['score']:0)));
								$bottomLabels.='<text x="'.(10 * $js_cnt_in_kq - 1.5).'" y="100" font-size="5" fill="#000000">'.$js_cnt_in_kq.'</text>';
								if(isset($statment['internalRating']) && isset($statment['externalRating'])){
									$diffInScore[$statment['internalRating']['score']>$statment['externalRating']['score']?$statment['internalRating']['score']-$statment['externalRating']['score']:$statment['externalRating']['score']-$statment['internalRating']['score']]++;
								}
							}
						}
					}
					$angles=array(0,0,0,0);
					$sum_of_diffInScore=array_sum($diffInScore);
					if($sum_of_diffInScore==9){
						$angles=array($diffInScore[0]*40,$diffInScore[1]*40,$diffInScore[2]*40,$diffInScore[3]*40);
					}
					$pieChart='<table style="width:372px;"><tr><td style="border:0;cellpadding:0;cellspacing:0;"><svg height="150px" viewBox="0 0 100 100"  preserveAspectRatio="none" width="150px">';
					$startX=0;
					$startY=0;
					$radius=50;
					$middleX=$radius+$startX;
					$middleY=$radius+$startY;
					$sPoints=array($middleX+$radius,$middleY);
					$aSum=0;
					$colors=array("#356EB3","#C23A37","#8BAF40","#704F97","red");
					$labels=array("Agreement","Disagreement by one degree","Disagreement by two degree","Disagreement by three degree","");
					$i=0;
					$PCLabels='';
					if(in_array(360,$angles)){
						$pieChart.='<ellipse cx="50" cy="50" rx="49" ry="49" fill-opacity="0.9"  fill="'.$colors[array_search(360,$angles)].'"/>
							<text x="45" y="52" font-size="6" fill="#000000">100%</text>';
					}else{
						foreach($angles as $a){
							$aSum+=$a;
							$ra=deg2rad($aSum);
							$ePoints=array((cos($ra)*$radius)+$middleX,$middleY-(sin($ra)*$radius));
							$laf=$a>180?1:0;
							$pieChart.='<path d="M'.$middleX.",".$middleY.' L'.$sPoints[0].",".$sPoints[1].' A'."$radius,$radius,0,$laf,0,".$ePoints[0].",".$ePoints[1].' Z"  fill="'.$colors[$i].'" fill-opacity="0.9" />';
							if($a>5){
								$na=$aSum-$a/2;
								$na=$na+(7*sin(deg2rad($na-15)));
								$ra=deg2rad($na);
								$ra2=deg2rad($na<180?$na/2:($na/2 +180));
								$pieChart.='<text x="'.((cos($ra)*($radius-5-(cos($ra2)*12)))+$middleX).'" y="'.($middleY-(sin($ra)*($radius-5-(cos($ra2)*12)))).'" font-size="6" fill="#000000">'.round($a/3.6,1).'%</text>';
							}
							$sPoints=$ePoints;
							$i++;
						}
					}
					$i=0;
					foreach($angles as $a){
						$PCLabels.='<tr><td style="border:0;cellpadding:0;cellspacing:0;width:20%"><div class="pieLbRow"><div style="width:13px;height:13px;background-color:'.$colors[$i].'" class="pieLbColor">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td><td style="width:80%;border:0;cellpadding:0;cellspacing:0;"><div class="pieLbText">'.$labels[$i].'</div></div></td></tr>';
						$i++;
					}
					
					$pieChart.='</svg></td><td style="border:0;cellpadding:0;cellspacing:0;">
						<table><div class="pieLb">'.$PCLabels.'</div></table></td></tr></table>';
					
					$block=array(
						"blockHeading"=>array("data"=>array(array("text"=>"<strong>Key Question $kq_cnt:-</strong> ".$keyQ['key_question_text'],"cSpan"=>2,"style"=>"redHead"))),
						"blockBody"=>array(
							"dataArray"=>array(
								array(
									'<table  style="width:372px;"><tr><td style="border:0;cellpadding:0;cellspacing:0;"><svg viewBox="0 0 100 100"  preserveAspectRatio="none" width="200" >
										<line x1="5" y1="5" x2="5" y2="95" style="stroke:#666;stroke-width:0.2" />
										<line x1="5" y1="95" x2="95" y2="95" style="stroke:#666;stroke-width:0.2" />
										
										<line x1="5" y1="75" x2="95" y2="75" style="stroke:#666;stroke-width:0.2" />
										<line x1="5" y1="55" x2="95" y2="55" style="stroke:#666;stroke-width:0.2" />
										<line x1="5" y1="35" x2="95" y2="35" style="stroke:#666;stroke-width:0.2" />
										<line x1="5" y1="15" x2="95" y2="15" style="stroke:#666;stroke-width:0.2" />
										
										<polyline points="'.implode(" ",$internalPoints).'" style="fill:transparent;stroke:red;stroke-width:1.4" />
										<polyline points="'.implode(" ",$externalPoints).'" style="fill:transparent;stroke:#7C7EB3;stroke-width:1.4" />
										
										'.$bottomLabels.'
										<text x="0" y="97" font-size="5" fill="#666">0</text>
										<text x="0" y="77" font-size="5" fill="#666">1</text>
										<text x="0" y="57" font-size="5" fill="#666">2</text>
										<text x="0" y="37" font-size="5" fill="#666">3</text>
										<text x="0" y="17" font-size="5" fill="#666">4</text>
									</svg></td><td style="border:0;cellpadding:0;cellspacing:0;width:20px;">
									<div class="lcLabels">
										<div class="lcLabel">
											<table><tr><td style="border:0;">
										<hr style="height:3px;width:15px;border:none;color:#ff0000;"/>										
											</td><td style="border:0;padding-right:2px;">
											<div class="lcText">KQ'.$kq_cnt.'SSRE</div>
											</td></tr></table>
										</div>
										<div class="lcLabel">
											<table><tr><td style="border:0">
										<hr style="height:3px;width:15px;border:none;color:#7C7EB3;"/>											
										</td><td style="border:0;">
											<div class="lcText">KQ'.$kq_cnt.'SERE</div>
										</td></tr></table>
										</div>
									</div></td></tr></table>',
									$pieChart),
								array(array("text"=>"Fig".(++$figCount).": The SSRE and SERE scores for Key Question $kq_cnt","style"=>"fs-sm"),array("text"=>"Fig".(++$figCount).": Percentage of agreement and disagreements between SSRE and SERE scores in Key Question $kq_cnt","style"=>"fs-sm"))
							)
						),
						"style"=>" mt20 borderedGrp kpablock a-grph",
						"config"=>array("minRows"=>3)
					);
					
					$section['sectionBody'][]=$block;
				}
			}
		}
		$this->sectionArray=array_merge($this->sectionArray,array($section));
	}
	protected function loadLastPage()
	{
		$indexKey=$this->addIndex("Provisional Award");		
		//$indexKey=$this->addIndex("Key Performance Area 1 to ".count($this->kpas));
		$section['indexKey']=$indexKey;
		$section=array("sectionHeading"=>array("text"=>"PROVISIONAL/CONDITIONAL","config"=>array("repeatHead"=>1)),"sectionBody"=>array(),"indexKey"=>$indexKey);
		/*$block = array(
		 "blockHeading"=>array("data"=>array(array("text"=>'ADHYAYAN QUALITY STANDARD AWARD',"style"=>"section_head greyHead"))),
		 "blockBody"=>array(
		 "dataArray"=>array(
		 array('<br/>If the school wishes to validate their self-review judgements against the global standards, and if their self-review ratings of this report match the Adhyayan&#39;s external review ratings, the school will be awarded <Tier-Award> by Adhyayan.<br/><br/>'),
		 array('* Conditional here means that the AQS award mentioned above will be true for the school, subject to one or more conditions or requirements being met.<br/>'),
		 array('<ol>
		 <li>The ratings of the school self-review team recorded in this report should match 100% with the ratings of Adhyayan&#39;s external review team for 162 statements of the diagnostic tool</li>
		 <li>The ratings given by the Adhyayan&#39;s external review team will be based on face-to-face validation of the school&#39;s practices and performance.</li>
		 </ol>'),
		 array('Things to be mindful of for the judgement difference between school self-review and external review team:<br/><br/>'),
		 array('<ol>
		 <li>Our research suggests that only 1% of the schools who has undertaken self-review match the external review ratings 100%.</li>
		 <li>These schools are the ones who are undertaking the review for the second time whose understanding of the tool and process is deeper.</li>
		 <li><span style="color:#F00;"><b>Only '.$this->sameRatingPercentage.'% schools have 50%</b></span> statements (out of 162)matching the judgements of the external review team.</li>
		 <li>It takes repeated use of the diagnostic, an understanding of its terminology, and exposure visits of the leadership and teaching teams to fully understand and appreciate the meaning of each of the statements in the diagnostic. As a result, the differences in judgement between self-review teams and Adhyayan&#39;s external review team could be attributed to:<br/><br/>
	
		 <div class="recmText">&#8226;an inaccurate interpretation of the statement by the self-review team ($quot;oh, we had read it to mean something else&quot;)</div>
		 <div class="recmText">&#8226;partial reading of the statement by the self-review team ($quot;we realize now that every word in the statement counts&quot;)</div>
		 <div class="recmText">&#8226;a disagreement on how the current practice of the school could be judged as variable or consistently good based on the teachers own practice (&quot;&hellip;but I think we are doing it � I certainly am!&quot;)</div>
		 <div class="recmText">&#8226;making a judgement with reference to the school&#39;s current practice, wherein the practice itself did not include all aspects of the statement in the diagnostic (&quot;we have not documented it, but&hellip;&quot;)</div>
		 <div class="recmText">&#8226;making a judgement with reference to other schools in the neighbourhood, rather than against the statement in the diagnostic (&quot;other schools don&#39;t even&hellip;&quot;)</div>
	
		 </li>
		 </ol>')
		 )
		 ),
		 "style"=>"onlytext"
	
			);*/
		//print_r($this->awardBreakdown);
		$internalAwardText = $this->awardBreakdown['internalAwardText'];
		$externalAwards = explode(',',$this->awardBreakdown['externalAwardNum']);//
		$totalAwards = $this->awardBreakdown['totalAwards'];
		//print_r($externalAwards);
		$externalAward='';
		foreach($externalAwards as $extAward)
		{
			$currAwardNum =substr($extAward,strpos($extAward,':')+1);
			$currPercent = ($currAwardNum/$totalAwards)*100;
			$externalAward .= number_format($currPercent,2)."% schools were awarded ".substr($extAward,0,strpos($extAward,':')).'. ';
			//echo "extAwards: ".$extAward;
		}
		// print_r($externalAward);
		$block = array(
				"blockHeading"=>array("data"=>array(array("text"=>'ADHYAYAN QUALITY STANDARD AWARD',"style"=>"section_head greyHead"))),
				"blockBody"=>array(
						"dataArray"=>array(
								array('<br/> You have got '.$internalAwardText.' internally. '.$externalAward.'If the school wishes to validate their self-review judgements against the global standards, and if their self-review ratings of this report match the Adhyayan&#39;s external review ratings, the school will be awarded '.$internalAwardText.' by Adhyayan.<br/><br/>')
						)
				),
				"style"=>"onlytext"
	
		);
		$section['sectionBody'][]=$block;
		$this->sectionArray[]=$section;
	}
	function generateSection_TchrRecomm(){
		$diagnosticModel = new diagnosticModel();
		$section=array("sectionHeading"=>array("text"=>"AQS Recommendations","style"=>"greyHead","config"=>array("repeatHead"=>1)),"sectionBody"=>array());
		$heading=array(array("text"=>'Teacher Name',"style"=>"greyHead tchr "),array("text"=>'Assessor Key Recommendations',"style"=>"greyHead "));
		$block=array(
				"blockHeading"=>array(
						"data"=>$heading
				),
				"blockBody"=>array(
						"dataArray"=>array(
						)
				),
				"style"=>"bordered col-4 mb15 grBox"
		);
		//print_r($this->assessments);
		$celebrateBlock = "";
		$ImproveBlock = "";			
	
					
				$celebrateData = $diagnosticModel->getAssessorKeyNotesType($this->assessmentId,'celebrate');
				$celebrateBlock = "<b>Celebrate:</b><ul>";
				foreach($celebrateData as $cel)
					$celebrateBlock .= "<li>".$cel['text_data']."</li>";
					$celebrateBlock .= "</ul>";
						
					$improveData = $diagnosticModel->getAssessorKeyNotesType($this->assessmentId,'recommendation');
					$ImproveBlock = "<b>Recommendations:</b><ul>";
				foreach($improveData as $improve)
					$ImproveBlock .= "<li>".$improve['text_data']."</li>";
					$ImproveBlock .= "</ul>";
				//$recBlk = "<table style='width:100%;text-align:left;border:solid 1px #000000;'><tr><td>".$celebrateBlock.$ImproveBlock."</td></tr></table>";
					$recBlk =$celebrateBlock.$ImproveBlock;
							
			$block['blockBody']['dataArray'][] = array(array("text"=>$this->teacherInfo['name']['value'],"rSpan"=>1),array("text"=>$recBlk,"style"=>'assessornoteTbl tcClass '));
			//$block['blockBody']['dataArray'][] = array(array("text"=>$ImproveBlock,"style"=>'assessornoteTbl'));
		
	
		$section['sectionBody'][]=$block;
	
		$this->sectionArray[]=$section;
	}
	protected function getDiagnosticId(){
		$sql = "select diagnostic_id from d_assessment where assessment_id=?";
		$res = $this->db->get_row($sql,array($this->assessmentId));
		return $res['diagnostic_id']>0?$res['diagnostic_id']:0;
	}
        protected function isCollobrative(){		
			$sql="select * from d_assessment where assessment_id=?";			
			$res = $this->db->get_row($sql,array($this->assessmentId));
			return $res['iscollebrative']>0?1:0;		
	}
         protected function getDiagnosticType(){
		$sql = "select a.diagnostic_id,d.diagnostic_type from d_assessment a INNER JOIN d_diagnostic d ON"
                        . " a.diagnostic_id = d.diagnostic_id where a.assessment_id=? AND d.diagnostic_type = ?";
		$res = $this->db->get_row($sql,array($this->assessmentId,1));
		return $res['diagnostic_type']>0?$res['diagnostic_type']:0;
	}
        
        protected function getCreateNetType(){
		$sql = "select a.diagnostic_id,d.iscreateNet from d_assessment a INNER JOIN d_diagnostic d ON"
                        . " a.diagnostic_id = d.diagnostic_id where a.assessment_id=? AND d.iscreateNet = ?";
		$res = $this->db->get_row($sql,array($this->assessmentId,1));
		return $res['iscreateNet']>0?$res['iscreateNet']:0;
	}
        protected function generateSection_StuAwardDefintion(){
		$indexKey=$this->addIndex("Student Performance Review: Grade Definition");
		$section=array("sectionHeading"=>array("text"=>"Student Performance Review: Grade Definition"),"sectionBody"=>array(),'indexKey'=>$indexKey);						
		/*$block=array(
				"blockHeading"=>array(
						"data"=>array("Grade","Definition")
					),
					"blockBody"=>array(
						"dataArray"=>array(
							array(array("style"=>"scheme-2-score-5-bg","text"=>'Exceptional'),' I possess a strong sense of self-awareness, career awareness, and the skills and mindsets required in a 21st century work place. I understand that careers require a long term perspective and that I must make intentional choices to reach my long-term career goals. I have reviewed my career readiness in an objective manner. I have actively begun to seek out opportunities, and have mapped out careers best suited for me that I constantly update.'),
							array(array("style"=>"scheme-2-score-4-bg","text"=>'Proficient'),'I possess a strong sense of self-awareness, career awareness, and the skills and mindsets required in a 21st century work place. I understand that careers require a long-term perspective and that I must make intentional choices to reach my long-term career goals. I possess the ability to review my career readiness in an objective manner. I have begun to take some steps towards seeking out opportunities to build my career. I actively conduct research on my field(s) of interest.'),
							array(array("style"=>"scheme-2-score-3-bg","text"=>'Developing'),'I possess a sense of self awareness, although I have never formally mapped it out. I am job ready and understand what skills, knowledge and attitudes are required to perform the jobs that I am interested in. I have some understanding of how the jobs that I wish to pursue will help me build my career. My ability to review my career readiness objectively is variable. I have begun thinking about how I can find more opportunities that will help me move forward.'),
							array(array("style"=>"scheme-2-score-2-bg","text"=>'Emerging'),'I sometimes think about my interests, aptitudes, and aspirations. I am beginning to understand what skills, knowledge and attitudes are required to secure and keep a job. I am not sure of how the jobs that I wish to pursue will help me build my career. I am beginning to understand how to gather evidence to review my career readiness effectively. I take whatever job comes my way.'),
							array(array("style"=>"scheme-2-score-1-bg","text"=>'Foundation'),'This grade is presented to those who are just beginning their journey of career readiness. I rarely think about my interests, aptitudes and aspirations. I am not sure of what skills, knowledge and attitudes are required to secure a job. I am not sure of how jobs are connected to career readiness. I am not sure of how to review myself. I am eager to build a career and willing to learn how to do it.')
						)
					),
					"style"=>"bordered tchrAwardInfoBlock"
				);*/
                
                $block=array("blockBody"=>array(
						"dataArray"=>array(
								array('<img src="'.$this->config['StudentAwardDefi'].'" style="width:100%">')
						)));
		$section['sectionBody'][]=$block;
		//$this->sectionArray[]=$section;
		//$indexKey=$this->addIndex("Key for reading the report");		
		/*$block=array(
				"blockHeading"=>array(
						"data"=>array(array("text"=>'Key for reading the report',"style"=>"brownHead")),
                                   
				),
				"blockBody"=>array(
						"dataArray"=>array(
								array("<b>Student performance diagnostic focuses on the following key performance aspects:</b><br/>Planning, preparation, delivery and student evaluation and support. Understanding and application of the curricular and co-curricular areas, communication with all stakeholders and embraces school vision and culture and adopts the school's systems and processes.")
                                                                
						)
				),
                                "blockBody"=>array(
						"dataArray"=>array(
								array("<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
                                                                    . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>"
                                                                    . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
                                                                
						)
				),
				"style"=>"onlytext textBlock"
		);*/
		//$section['sectionBody'][]=$block;
		/*$block=array(
				"blockHeading"=>array(
						"data"=>array("Rating","To be read as")
				),
				"blockBody"=>array(
						"dataArray"=>array(
								array("Always",'Exemplifies high quality or best practice within the key performance area which is worth sharing.'),
								array("Mostly",'Consistently effective within the key performance area and acts as a mentor and champion.'),
								array("Sometimes",'Evidence of some strong practice and still needs to develop key skills and strategies to become securely effective.'),
								array("Rarely",'The student is in need of significant support in order for them to aspire to be effective in the classroom.')
						)
				),
				"style"=>"bordered tchrAwardInfoBlock"
		);
		$section['sectionBody'][]=$block;*/
		$this->sectionArray[]=$section;
	}
	protected function generateSection_TchrAwardDefintion(){
		$indexKey=$this->addIndex("Teacher Performance Review: Award Definition");
		$section=array("sectionHeading"=>array("text"=>"Teacher Performance Review: Award Definition"),"sectionBody"=>array(),'indexKey'=>$indexKey);
		$block=array(
				"blockHeading"=>array(
						"data"=>array("Grade","Definition")
					),
					"blockBody"=>array(
						"dataArray"=>array(
							array(array("style"=>"scheme-2-score-5-bg","text"=>'Exceptional'),'The performance of the teacher in planning, preparation and lesson delivery is consistently, highly effective and at times exceptional. The teacher forms strong, collaborative professional relationships with colleagues and plays a leading role in their section and subject. Students enjoy their lessons, are confident in the subject knowledge. They respond well to the teacher\'s high expectations knowing that the teacher will always provide support and guidance in co-curricular and academic endeavours.'),
							array(array("style"=>"scheme-2-score-4-bg","text"=>'Proficient'),'The teacher\'s performance in planning, preparation and lesson delivery is consistently effective with moments of outstanding and less effective practice. The teacher willingly collaborates with colleagues and at times takes the lead in their section and subject. Students usually enjoy their lessons and are confident in their subject knowledge, skills and respond well to the teacher\'s expectations. Students have confidence in the teacher\'s motivation and commitment and the manner in which they consistently provide academic, co-curricular and social support and guidance.'),
							array(array("style"=>"scheme-2-score-3-bg","text"=>'Developing'),'The teacher\'s performance in planning, preparation and lesson delivery is increasingly effective with a significant minority of evidence of secure practice. The teacher may need prompting to collaborate with colleagues and to take lead in their section and subject without being asked. Students enjoy and are engaged in some lessons. Increasingly they are confident in their subject knowledge, and skills and respond well to the teacher\'s expectations. The teacher does not always provide students with cocurricular, academic and social support and guidance.'),
							array(array("style"=>"scheme-2-score-2-bg","text"=>'Emerging'),'The teacher is beginning to show a consistent understanding of the breadth of their role leading teaching and learning. They may already be displaying early signs of effectiveness in planning, preparation and lesson delivery. As yet their grasp of the strategies and skills necessary for an effective teacher are still developing. While they may reveal significant, their performance is marked most significantly by its variability.'),
							array(array("style"=>"scheme-2-score-1-bg","text"=>'Foundation'),'This grade is presented to teachers who are just beginning their self-improvement journey. While they may display effective relationships and content knowledge, they will be at an early stage in developing their effectiveness in the key areas of lesson planning, lesson delivery, assessment and tracking of students or the critical strategies and skills necessary to promote engaged student learning.')
						)
					),
					"style"=>"bordered tchrAwardInfoBlock"
				);
		$section['sectionBody'][]=$block;
		//$this->sectionArray[]=$section;
		$indexKey=$this->addIndex("Key for reading the report");
		$block=array(
				"blockHeading"=>array(
						"data"=>array(array("text"=>'Key for reading the report',"style"=>"brownHead"))
				),
				"blockBody"=>array(
						"dataArray"=>array(
								array("<b>Teacher performance diagnostic focuses on the following key performance aspects:</b><br/>Planning, preparation, delivery and student evaluation and support. Understanding and application of the curricular and co-curricular areas, communication with all stakeholders and embraces school vision and culture and adopts the school's systems and processes.")
						)
				),
				"style"=>"onlytext textBlock"
		);
		$section['sectionBody'][]=$block;
		$block=array(
				"blockHeading"=>array(
						"data"=>array("Rating","To be read as")
				),
				"blockBody"=>array(
						"dataArray"=>array(
								array("Always",'Exemplifies high quality or best practice within the key performance area which is worth sharing.'),
								array("Mostly",'Consistently effective within the key performance area and acts as a mentor and champion.'),
								array("Sometimes",'Evidence of some strong practice and still needs to develop key skills and strategies to become securely effective.'),
								array("Rarely",'The teacher is in need of significant support in order for them to aspire to be effective in the classroom.')
						)
				),
				"style"=>"bordered tchrAwardInfoBlock"
		);
		$section['sectionBody'][]=$block;
		$this->sectionArray[]=$section;
	}
        
        protected function getDiagnosticLabels($language_id=DEFAULT_LANGUAGE){	
                if(isset($this->lang) ) {
                   $language_id = $this->lang;
                }
		 $sql="select d.label_name,d.label_key,a.label_text
			from d_assessment_labels d 
			inner join h_assessment_labels a on d.id=a.label_id				
			where a.language_id=?  ";
		return $this->db->get_results($sql,array($language_id));
	}
        
        protected function getDiagnosticGraphkeys($language_id=DEFAULT_LANGUAGE){	
                if(isset($this->lang) ) {
                   $language_id = $this->lang;
                }
		 $sql="select a.rating_id,hlt.translation_text from d_rating a inner join h_lang_translation hlt on a.equivalence_id=hlt.equivalence_id				
			where hlt.language_id=? && rating_id IN (5,6,7,8)  order by rating_id desc";
		$res=$this->db->get_results($sql,array($language_id));
                $array_return=array();
                $k=4;
                foreach($res as $key=>$val){
                    $array_return[$k]=$val['translation_text'];
                    $k--;
                }
                
                return $array_return;
                
	}
        
/*******************************Functions of comaparitive report round 2 Starts******************/
         public function generateRound2Output($lang_id=DEFAULT_LANGUAGE){
		$this->config['isChangeMaker'] = $this->isChangeMaker();
                
		switch($this->reportId){ 
			case 1: $diagId = $this->getDiagnosticId();
                                $diagType = $this->getDiagnosticType();
                                $this->config['isCollobrative'] = $this->isCollobrative();
                                if($diagType == 1) {
                                  $this->config['isChildProt']=1 && $this->config['childProtImg']= '.'.DS.'public'.DS.'images'.DS.'diagnostic_adhyayan.png';//for Don-Bosco show logo in reports  
                                }else
				//$diagId==1?($this->config['isCoBranded']=1 && $this->config['coBrandedImg']= '.'.DS.'uploads'.DS.'diagnostic'.DS.'diagnostic_image_'.$diagId.'.jpg'):'';//for Don-Bosco show logo in reports
				$diagId==1?($this->config['isCoBranded']=1 && $this->config['coBrandedImg']= ''.UPLOAD_URL_DIAGNOSTIC.'diagnostic_image_'.$diagId.'.jpg'):'';//for Don-Bosco show logo in reports
                                return $this->generateAqsRound2Output($lang_id);
                                break;
		}
	}
        
        protected function generateAqsRound2Output($lang_id=DEFAULT_LANGUAGE){
                $isCollobrative=$this->config['isCollobrative'];    
                $aqsRound=$this->getAQSRound();
                $clientId=$this->getClientId();
                $diagId = $this->getDiagnosticId();
                
            
                
                foreach($this->getRound2AssessmentIds($clientId,$diagId) as $key=>$val){
                    $assessment_id=$val['assessment_id'];
                    if($assessment_id != $this->assessmentId){
                        $this->assessmentIdRound2 = $assessment_id;
                    }
                    $ids_allow[]=$assessment_id;                     
                }//print_r($ids_allow);
                
                $isCollobrativeR1 = $this->isCollobrativeR1($ids_allow[0]);
                $isCollobrativeR2 = $this->isCollobrativeR2($ids_allow[1]);
                
		$this->loadAqsDataRound2($ids_allow[0]);
                $this->loadAqsDataRound2($ids_allow[1]);
              
                //echo '<pre>'; print_r($this->aqsData);
                //echo $this->aqsData[1]['award_scheme_id'];
                
		$this->loadAwardSchemeRound2($this->aqsData[0]['award_scheme_id'],$lang_id);
                $this->loadAwardSchemeRound2($this->aqsData[1]['award_scheme_id'],$lang_id);
                //echo '<pre>'; print_r($this->awardSchemes);
                //
                
                //$this->loadJudgementalStatements('',$lang_id);
		//$this->loadCoreQuestions('',$lang_id);
                $this->loadCoreQuestionsRound2('',$lang_id,$ids_allow[0]);
                $this->loadCoreQuestionsRound2('',$lang_id,$ids_allow[1]);
                
		//$this->loadKeyQuestions('',$lang_id);
                $this->loadKeyQuestionsRound2('',$lang_id,$ids_allow[0]);
                $this->loadKeyQuestionsRound2('',$lang_id,$ids_allow[1]);
                
		$this->loadJudgementalStatementsRound2('',$lang_id,$ids_allow[0]);
                $this->loadJudgementalStatementsRound2('',$lang_id,$ids_allow[1]);
//                
//		$this->loadCoreQuestionsRound2('',$lang_id,$ids_allow[0]);
//                $this->loadCoreQuestionsRound2('',$lang_id,$ids_allow[1]);
//                
//		$this->loadKeyQuestionsRound2('',$lang_id,$ids_allow[0]);
//                $this->loadKeyQuestionsRound2('',$lang_id,$ids_allow[1]);
                
		$this->loadKpasRound2('',$lang_id,$ids_allow[0]);
                $this->loadKpasRound2('',$lang_id,$ids_allow[1]);
                //echo '<pre>'; print_r($this->kpas[0]);
                //echo count($this->kpas[0]);
		$this->loadAwardRound2($lang_id,$ids_allow[0],0);
                $this->loadAwardRound2($lang_id,$ids_allow[1],1);
                //echo '<pre>'; print_r($this->awardName);
                
                $this->config['getCreateNetType']=$this->getCreateNetType();
                $diagnosticLabels=array();
                $languageLabels = $this->getDiagnosticLabels($lang_id);
                foreach($languageLabels as $data) {//print_r($data);
                    $diagnosticLabels[$data['label_key']] = $data['label_text'];
                }
                
                 //echo '<pre>';print_r($this->aqsData);
                 //echo '<pre>';print_r($this->awardSchemes);
		$round1_date=date('M Y',strtotime($this->aqsData[0]['create_date']));
                $round2_date=date('M Y',strtotime($this->aqsData[1]['create_date']));
                
                //$this->config["schoolName"] = $this->aqsData[0]['school_name'];
                
		if($lang_id==9){
                 $this->aqsInfo='<div class="bigBold">'.$this->aqsData[1]['school_name'].',&nbsp;'.$this->aqsData[1]['school_address'].'</div>
                         <div class="reportInfo"><span style="font-size: 15px;">'.$diagnosticLabels['Compilation_Scores'].'</span><br><span style="font-weight: bold;font-size:15px;">Comparison between performance in Round 1 and Round 2 of AQS<br>Round 1  '.str_replace("&",$round1_date,$diagnosticLabels['Conducted_On']).'<br>Round 2 '.str_replace("&","$round2_date",$diagnosticLabels['Conducted_On']).'<br> </span></div>';
                }else{
                  $this->aqsInfo='<div class="bigBold">'.$this->aqsData[1]['school_name'].',&nbsp;'.$this->aqsData[1]['school_address'].'</div>
                         <div class="reportInfo"><span style="font-size: 15px;">'.$diagnosticLabels['Compilation_Scores'].'</span><br><span style="font-weight: bold;font-size:15px;">Comparison between performance in Round 1 and Round 2 of AQS<br>Round 1  '.str_replace("&",$round1_date,$diagnosticLabels['Conducted_On']).'<br>Round 2 '.str_replace("&","$round2_date",$diagnosticLabels['Conducted_On']).'<br></span></div>';
   
                }
                
	
		$this->config['reportTitle']=$diagnosticLabels['Adhyayan_Report_Card_Title'];
		$this->config["footerText"]="Adhyayan Quality Standard award report for ".$this->aqsData[0]['school_name'].", {dateToday} (generated on)";
	
		$this->sectionArray=array();
		$this->indexArray=array();
                $this->generateSection_KeyQuestionRound2('',$lang_id,$diagnosticLabels,0,$isCollobrativeR1,$isCollobrativeR2);
		$this->generateSection_ScoreCardForKPAsRound2('',$lang_id,$diagnosticLabels,0,$isCollobrativeR1,$isCollobrativeR2);
                 
                //$this->generateSection_ScoreCardForKPAsRound2('',$lang_id,$diagnosticLabels,1);
                
		//self-review waiting for final text from client
		/*if($this->subAssessmentType==1)
		{
			//$this->loadSameRatingPercentage();
			$this->generateAwardBreakDown();
			$this->loadLastPage();
		}*/
		$this->generateIndexAndCoverRound2($diagnosticLabels);
                
		
		$output=array(
					"config"=>$this->config,
					"data"=>$this->sectionArray
				);
                //echo "<pre>";print_r($output);die;
		return $output;
	}
        
        protected function loadAqsDataRound2($assessmentId){
             if(empty($assessmentId)){
                    $assessmentId=$this->assessmentId;
                }else{
                    $assessmentId=$assessmentId ;
                }//echo $assessmentId;
		//if(empty($this->aqsData)){
			     $sql="select a.school_name,ctr.country_name,st.state_name,cty.city_name,sr.region_name,a.principal_name,a.school_address,a.principal_phone_no,STR_TO_DATE(a.school_aqs_pref_end_date, '%d-%m-%Y') as school_aqs_pref_end_date,DATE(b.create_date) as create_date,b.award_scheme_id,date(c.publishDate) as publishDate,date(valid_until) as valid_until,b.tier_id,b.client_id,b.review_criteria
				from d_AQS_data a
				inner join d_assessment b on a.id = b.aqsdata_id
				inner join d_client dc on dc.client_id = b.client_id
				left join d_countries ctr on ctr.country_id = dc.country_id
				left join d_states st on dc.state_id = st.state_id and st.country_id = dc.country_id
				left join d_cities cty on cty.city_id = dc.city_id and cty.state_id = dc.state_id
				left join h_assessment_report c on b.assessment_id = c.assessment_id and c.report_id= $this->reportId
				left join d_school_region sr on sr.region_id = a.school_region_id			
				where b.assessment_id=?
				group by a.id;";
                        
                                //$this->aqsData=$this->db->get_results($sql);//print_r($this->aqsData);
                                $this->aqsData[]=$this->db->get_row($sql,array($assessmentId));
                        
                        /*if((isset($this->aqsData) && empty($this->aqsData['school_name'])) || !isset($this->aqsData)){
                               $sql="select dc.client_name as school_name,du.name as principal_name,CONCAT(COALESCE(`street`,''),' ',COALESCE(`addressLine2`,''),', ',COALESCE(cty.city_name,''),', ',COALESCE(st.state_name,'')) as school_address,ctr.country_name,st.state_name,cty.city_name,DATE(b.create_date) as create_date,b.award_scheme_id,date(c.publishDate) as publishDate,date(valid_until) as valid_until,b.tier_id,b.client_id,b.review_criteria
				from d_assessment b
				inner join d_client dc on dc.client_id = b.client_id
				left join d_countries ctr on ctr.country_id = dc.country_id
				left join d_states st on dc.state_id = st.state_id and st.country_id = dc.country_id
				left join d_cities cty on cty.city_id = dc.city_id and cty.state_id = dc.state_id
				left join h_assessment_report c on b.assessment_id = c.assessment_id and c.report_id= $this->reportId 
                                left join d_user du on dc.client_id = du.client_id    
                                left join h_user_user_role dur on du.user_id = dur.user_id && dur.role_id=6
				where  dur.role_id=6 && b.assessment_id=?
				group by b.assessment_id;";
                                
                                $this->aqsData[]=$this->db->get_row($sql,array($assessmentId));
                        }*/
                        
			if(isset($this->aqsData['school_name']) && empty($this->aqsData['school_name'])){
                        $this->aqsData['school_name']="{School Name}";    
                        }else if(!isset($this->aqsData['school_name'])){
                        $this->aqsData['school_name']="{School Name}";    
                        }
                        
                        if(isset($this->aqsData['principal_name']) && empty($this->aqsData['principal_name'])){
                        $this->aqsData['principal_name']="{Principal Name}";    
                        }else if(!isset($this->aqsData['principal_name'])){
                        $this->aqsData['principal_name']="{Principal Name}";    
                        }
                        
                        if(isset($this->aqsData['school_address']) && empty($this->aqsData['school_address'])){
                        $this->aqsData['school_address']="{School Address}";    
                        }else if(!isset($this->aqsData['school_address'])){
                        $this->aqsData['school_address']="{School Address}";    
                        }
                        
                        if(isset($this->aqsData['region_name']) && empty($this->aqsData['region_name'])){
                        $this->aqsData['region_name']="";    
                        }else if(!isset($this->aqsData['region_name'])){
                        $this->aqsData['region_name']="";    
                        }
                        
                        if(isset($this->aqsData['city_name']) && empty($this->aqsData['city_name'])){
                        $this->aqsData['city_name']="";    
                        }else if(!isset($this->aqsData['city_name'])){
                        $this->aqsData['city_name']="";    
                        }
                        
                        if(isset($this->aqsData['state_name']) && empty($this->aqsData['state_name'])){
                        $this->aqsData['state_name']="";    
                        }else if(!isset($this->aqsData['state_name'])){
                        $this->aqsData['state_name']="";    
                        }
                        
                        if(isset($this->aqsData['country_name']) && empty($this->aqsData['country_name'])){
                        $this->aqsData['country_name']="";    
                        }else if(!isset($this->aqsData['country_name'])){
                        $this->aqsData['country_name']="";    
                        }
                        
                        if(isset($this->aqsData['principal_phone_no']) && empty($this->aqsData['principal_phone_no'])){
                        $this->aqsData['principal_phone_no']="";    
                        }else if(!isset($this->aqsData['principal_phone_no'])){
                        $this->aqsData['principal_phone_no']="";    
                        }
                        
                        //$this->conductedDate=(empty($this->aqsData['school_aqs_pref_end_date']) || $this->aqsData['school_aqs_pref_end_date']=="0000-00-00")?$this->conductedDate:implode("-",array_reverse(explode("-",substr($this->aqsData['school_aqs_pref_end_date'],0,7))));
			//$this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:implode("-",array_reverse(explode("-",substr($this->aqsData['valid_until'],0,7))));
			$this->conductedDate=(empty($this->aqsData['school_aqs_pref_end_date']) || $this->aqsData['school_aqs_pref_end_date']=="0000-00-00")?$this->conductedDate:date("M-Y",strtotime($this->aqsData['school_aqs_pref_end_date']));
			$this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:date("M-Y",strtotime($this->aqsData['valid_until']));
			
                        $this->schoolLocation = $this->aqsData['region_name'];
			$this->schoolCity = $this->aqsData['city_name'];
			$this->schoolState = $this->aqsData['state_name'];
			$this->schoolCountry = $this->aqsData['country_name'];
		//}
	}
        
        protected function loadAwardSchemeRound2($award_sch_id,$lang_id=DEFAULT_LANGUAGE){ 
		//if(empty($this->awardSchemes) && $this->aqsData['award_scheme_id']>0){
		
             /*if(empty($award_sch_id)){
                    $award_sch_id=$this->aqsData[0]['award_scheme_id'];
                }else{
                    $award_sch_id=$award_sch_id ;
                }*/
             $sql="SELECT a.award_id,hlt.translation_text as award_name,s.`order` FROM `h_award_scheme` s 
					inner join d_award a on s.award_id=a.award_id
                                        inner join h_lang_translation hlt on a.equivalence_id = hlt.equivalence_id
					where s.award_scheme_id=? && hlt.language_id=?
					order by s.`order`";

			//$this->awardSchemes=$this->db->array_col_to_key($this->db->get_results($sql,array($lang_id,$award_ids)),'order');
                        $this->awardSchemes[]=$this->db->array_col_to_key($this->db->get_results($sql,array($award_sch_id,$lang_id)),'order');
                        //$this->awardSchemes=$this->db->get_results($sql);
                        //echo"<pre>"; print_r($this->awardSchemes);
		//}
	}
        
        protected function loadJudgementalStatementsRound2($is7thKpaReport=false,$lang_id=DEFAULT_LANGUAGE,$assessmentId){
		//if(empty($this->judgementStatement)){
			 $sql="
				select c.core_question_instance_id,c.judgement_statement_instance_id,hlt.translation_text as judgement_statement_text,role,r.rating,hls.rating_level_order as numericRating
					 from f_score a                                         	
					 inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id 
					 inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
					 inner join d_judgement_statement d on d.judgement_statement_id = c.judgement_statement_id					 
					 inner join d_assessment g on a.assessment_id = g.assessment_id
                                         inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
					 inner join h_kpa_diagnostic h on h.diagnostic_id = g.diagnostic_id and h.kpa_order ".($is7thKpaReport?"=":"<")."7
					 inner join h_kpa_kq i on h.kpa_instance_id = i.kpa_instance_id
					 inner join h_kq_cq j on i.key_question_instance_id = j.key_question_instance_id and j.core_question_instance_id = c.core_question_instance_id					 
					 inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					 inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.rating_id=hls.rating_id and hls.rating_level_id=4
                                         inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					 inner join  (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=? ) r on a.rating_id = r.rating_id and hls.rating_id=r.rating_id			
					 where a.isFinal = 1 and a.assessment_id = ?  and hlt.language_id=?
					 order by c.`js_order` asc ;";
				
			$this->judgementStatement[]=$this->get_section_Array($this->db->get_results($sql,array($lang_id,$assessmentId,$lang_id)),"judgement_statement_instance_id","core_question_instance_id");
		//}
                        //echo '<pre>'; print_r($this->judgementStatement[0]);
	}
        
        protected function loadCoreQuestionsRound2($is7thKpaReport=false,$lang_id=DEFAULT_LANGUAGE,$assessmentId){
		//if(empty($this->coreQuestions)){
			$sql="select c.key_question_instance_id,a.core_question_instance_id,hlt.translation_text as core_question_text,r.rating,role,hls.rating_level_order as numericRating
					 from h_cq_score a
					 inner join h_kq_cq c on a.core_question_instance_id = c.core_question_instance_id
					 inner join d_core_question d on d.core_question_id = c.core_question_id
                                         inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
					 inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					 inner join d_assessment g on a.assessment_id = g.assessment_id
					 inner join h_kpa_diagnostic i on i.diagnostic_id = g.diagnostic_id and i.kpa_order ".($is7thKpaReport?"=":"<")."7
					 inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and c.key_question_instance_id = j.key_question_instance_id
					 inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					 inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.d_rating_rating_id=hls.rating_id and hls.rating_level_id=3
                                        inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					 inner join (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=? ) r on a.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id		
					 where a.assessment_id=? and hlt.language_id=?
					 order by c.`cq_order` asc;";
			$this->coreQuestionsR[]=$this->get_section_Array($this->db->get_results($sql,array($lang_id,$assessmentId,$lang_id)),"core_question_instance_id","key_question_instance_id");
                        //echo '<pre>'; print_r($this->coreQuestionsR[1]); 
		//}
	}
        
        protected function loadKeyQuestionsRound2($is7thKpaReport=false,$lang_id=DEFAULT_LANGUAGE,$assessmentId){
		//if(empty($this->keyQuestions)){
			$sql="select c.kpa_instance_id,a.key_question_instance_id,hlt.translation_text as key_question_text,r.rating,role,hls.rating_level_order as numericRating
					from h_kq_instance_score a
					inner join h_kpa_kq c on a.key_question_instance_id = c.key_question_instance_id
					inner join d_key_question d on d.key_question_id = c.key_question_id	
                                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
					inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					inner join d_assessment g on a.assessment_id = g.assessment_id
					inner join h_kpa_diagnostic i on i.diagnostic_id = g.diagnostic_id and i.kpa_instance_id=c.kpa_instance_id and i.kpa_order ".($is7thKpaReport?"=":"<")."7
					inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.d_rating_rating_id=hls.rating_id and hls.rating_level_id=2
				    inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					inner join (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=?) r on a.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id	
					where a.assessment_id = ? and hlt.language_id=?
					order by c.`kq_order` asc;";
			$this->keyQuestionsR[]=$this->get_section_Array($this->db->get_results($sql,array($lang_id,$assessmentId,$lang_id)),"key_question_instance_id","kpa_instance_id");
		//}
                        //echo '<pre>'; print_r($this->keyQuestionsR);
	}
        
        protected function loadKpasRound2($is7thKpaReport=false,$lang_id=DEFAULT_LANGUAGE,$assessmentId){
                if(empty($assessmentId)){
                    $assessmentId=$this->assessmentId;
                }else{
                    $assessmentId=$assessmentId ;
                }
                        
		//if(empty($this->kpas)){
			 /*$sql="select a.kpa_instance_id,hlt.translation_text as KPA_name,r.rating,role,hls.rating_level_order as numericRating
					from h_kpa_instance_score a
					inner join h_kpa_diagnostic c on a.kpa_instance_id = c.kpa_instance_id and c.kpa_order ".($is7thKpaReport?"=":"<")."7
					inner join d_kpa d on d.kpa_id = c.kpa_id
                                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
					inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					inner join d_assessment g on a.assessment_id = g.assessment_id
					inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.d_rating_rating_id=hls.rating_id and hls.rating_level_id=1
				        inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					inner join (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=?) r on a.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id
					where a.assessment_id = ? and hlt.language_id=?
					order by c.`kpa_order` asc;";*/
                
                        $sql="select g.iscollebrative,a.kpa_instance_id,hlt.translation_text as KPA_name,r.rating,role,hls.rating_level_order as numericRating
					from h_kpa_instance_score a
					inner join h_kpa_diagnostic c on a.kpa_instance_id = c.kpa_instance_id and c.kpa_order ".($is7thKpaReport?"=":"<")."7
					inner join d_kpa d on d.kpa_id = c.kpa_id
                                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
					inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					inner join d_assessment g on a.assessment_id = g.assessment_id
					inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.d_rating_rating_id=hls.rating_id and hls.rating_level_id=1
				        inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					inner join (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=?) r on a.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id
					where a.assessment_id = ? and hlt.language_id=?
					order by c.`kpa_order` asc;";
                        
			$this->kpas[]=$this->get_section_Array($this->db->get_results($sql,array($lang_id,$assessmentId,$lang_id)),"kpa_instance_id");
                        //echo "<pre>";print_r($this->db->get_results($sql,array($this->assessmentId)));
                        //echo $assessmentId; echo "<pre>"; print_r($this->kpas);
		//}
	}
        
        protected function loadAwardRound2($lang_id=DEFAULT_LANGUAGE,$assessmentId,$i){ 
                //echo 'arti'; echo $i;
                if(empty($assessmentId)){
                    $assessmentId=$this->assessmentId;
                }else{
                    $assessmentId=$assessmentId ;
                }
                
                
                //echo count($this->kpas[$i]);
                $isCollobrative=$this->config['isCollobrative'];        
		if($this->subAssessmentType==1)//for self review, no award					
			return $this->awardName[]='N/A';	
                //echo $this->awardNo;
		//if(empty($this->awardNo)){//reviews with more than one kpa whether teacher or school
                    //echo "<pre>";print_r($this->kpas[$i]);
			if(count($this->kpas[$i])>1){
				switch($this->reportId){
					case 1:
					case 2:
					case 3:
						$temp=array();
												
							foreach($this->kpas[$i] as $kpa){
                                                                if($isCollobrative==1){
                                                                $temp[]=$kpa['externalRating']['score'];
                                                                }else{
								$temp[]=$kpa['externalRating']['score'];
                                                                }
							}//print_r($temp);
							$compulsoryKpaScore1=$temp[0]; //We have assumes that L & T KPA are the top two KPAs. So we are hard coding it. We need to find a better way.
							$compulsoryKpaScore2=$temp[1];
							$this->noOfScore1234InKpas=array_count_values($temp);
						
                                                        $this->awardNo=$this->calculateSchoolAssessmentAwardValueRound2($compulsoryKpaScore1,$compulsoryKpaScore2,$i);
						
						$sql="select replace(replace(award_name_template,'<Tier>',standard_name),'<Award>',hlt.translation_text)
							 from d_assessment a
							 inner join h_award_scheme b on a.award_scheme_id = b.award_scheme_id
							 inner join d_award_scheme c on c.award_scheme_id = a.award_scheme_id
							 inner join d_award d on d.award_id = b.award_id
                                                         inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id
							 left join d_tier e on e.standard_id = b.tier_id
							 where assessment_id = ? and hlt.language_id=? and b.order = $this->awardNo;";
						$this->awardName[]=$this->db->get_var($sql,array($assessmentId,$lang_id));
						break;
					case 9:
                                        case 5:	
                                                $kpa=current($this->kpas[$i]);//echo $kpa['externalRating']['rating'];die;
                                                reset($this->kpas[$i]);			
                                                $teacherAward = isset($kpa['externalRating'])?$kpa['externalRating']['score']:'';
                                                $this->awardName[]=$teacherAward<4?' working towards \'Proficiency\'':' awarded \'Proficient\'';
						break;						
				}//print_r($this->awardName);echo 'arti';
			}
			elseif(count($this->kpas[$i])==1){ 
                                switch($this->reportId){
                                    case 9 : $kpa=current($this->kpas[$i]);//echo $kpa['externalRating']['rating'];die;
                                             reset($this->kpas[$i]);			
                                             $teacherAward = isset($kpa['externalRating'])?$kpa['externalRating']['score']:'';
                                             //$this->awardName=$teacherAward<4?' working towards \'Proficiency\'':' \'Proficient\'';
                                             $this->awardName[]=isset($kpa['externalRating'])?$kpa['externalRating']['rating']:'';
                                             break;
                                                
                                    case 5 : $kpa=current($this->kpas[$i]);//echo $kpa['externalRating']['rating'];die;
                                             reset($this->kpas[$i]);			
                                             $teacherAward = isset($kpa['externalRating'])?$kpa['externalRating']['score']:'';
                                             $this->awardName[]=$teacherAward<4?' working towards \'Proficiency\'':' awarded \'Proficient\'';
                                             break;
                                    case 1 : $kpa=current($this->kpas[$i]);
                                              reset($this->kpas[$i]);
                                              $this->awardName[]=isset($kpa['externalRating'])?$kpa['externalRating']['rating']:'';                                                    
                                              break;
                                    case 10:      
                                    case 7 :  $kpa=current($this->kpas[$i]);//single teacher recommendation report
                                                reset($this->kpas[$i]);			
                                                $teacherAward = isset($kpa['externalRating'])?$kpa['externalRating']['score']:'';
                                                $this->awardName[]=$teacherAward<4?' working towards \'Proficiency\'':' awarded \'Proficient\'';
						break;
                                }
			}
               
		//}
		
	}
        
        private function calculateSchoolAssessmentAwardValueRound2($compulsoryKpaScore1,$compulsoryKpaScore2,$i){
		$matrix=new schoolAssessmentAwardMatrix($compulsoryKpaScore1,$compulsoryKpaScore2,$this->noOfScore1234InKpas,$this->aqsData[$i]['tier_id']);
                //echo $this->aqsData[$i]['tier_id'];
		return $matrix->firstLevel();
	}
        
        protected function generateIndexAndCoverRound2($diagnosticLabels = array()){ //print_r($this->awardName);
		$sections=array();
                $isCollobrative=$this->config['isCollobrative'];
		$coverSection=array("sectionBody"=>array());
		$keysToReplace=array("{schoolName}","{schoolAddress}","{conductedOn}","{validTill}","{awardName}","{dateToday}","{schoolLocation}", "{schoolCity}", "{schoolState}", "{schoolCountry}", "{teacherName}");
		if($this->reportId==9){
                $valuesToReplace=array($this->aqsData[0]['school_name'],$this->aqsData[0]['school_address'],$this->conductedDate,$this->validDate,$this->awardName[0],date("d-m-Y"),$this->schoolLocation,$this->schoolCity,$this->schoolState,$this->schoolCountry,!empty($this->studentInfo['name']['value'])?$this->studentInfo['name']['value']:'');
                    
                }else{
                $valuesToReplace=array($this->aqsData[0]['school_name'],$this->aqsData[0]['school_address'],$this->conductedDate,$this->validDate,$this->awardName[0],date("d-m-Y"),$this->schoolLocation,$this->schoolCity,$this->schoolState,$this->schoolCountry,!empty($this->teacherInfo['name']['value'])?$this->teacherInfo['name']['value']:'');
                }
                //echo $this->aqsInfo;
                $aqsinfo= str_replace($keysToReplace,$valuesToReplace,$this->aqsInfo);
		$this->config["footerText"]=str_replace( $keysToReplace,$valuesToReplace,$this->config["footerText"]);
		$aqsBlock=array(
				"blockBody"=>array(
					"dataArray"=>array(
						array($aqsinfo)
					)
				),
				"style"=>"coverInfoBlock"
			);
		$coverSection['sectionBody'][]=$aqsBlock;
		
		$indexBlock=array(
					"blockHeading"=>array(
						"data"=>array(
							array("text"=>$diagnosticLabels['INDEX'],"style"=>"greyHead","cSpan"=>3)
						)
					),
					"blockBody"=>array(
						"dataArray"=>array(
							array($diagnosticLabels["SR_No"],$diagnosticLabels['PARTICULARS'],$diagnosticLabels["PAGE_NO"])
						)
					),
				"style"=>"bordered reportIndex"
			);
                //print_r($this->indexArray);
		foreach($this->indexArray as $k=>$v){
			$indexBlock["blockBody"]["dataArray"][]=array($k+1,$v,'<span id="indexKey-'.($k+1).'"></span>');
		}
		//echo $this->awardName;
                //echo $this->reportId;
		switch($this->reportId){ 
			case 1:
			case 2:
                                if(empty($this->aqsData[0]['review_criteria'])){
				$awardBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels["Principal_Name"],array("text"=>$this->aqsData[1]['principal_name'],"style"=>"textBold")),
								array($diagnosticLabels["Adhyayan_Award"]." Round1",array("text"=>$this->awardName[0],"style"=>"blueColor textBold")),
								array($diagnosticLabels["Adhyayan_Award"]." Round2",array("text"=>$this->awardName[1],"style"=>"blueColor textBold"))
							)
						),
						"style"=>"bordered awardBlock"
					);
                                }else{
                                    $awardBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels["Principal_Name"],array("text"=>$this->aqsData[0]['principal_name'],"style"=>"textBold")),
								array($diagnosticLabels["Adhyayan_Award"],array("text"=>$this->awardName[0],"style"=>"blueColor textBold")),
                                                                array($diagnosticLabels["Adhyayan_Award"],array("text"=>$this->awardName[1],"style"=>"blueColor textBold")),
                                                                array('Review Criteria',array("text"=>$this->aqsData[0]['review_criteria'],"style"=>"textBold"))
							)
						),
						"style"=>"bordered awardBlock"
					);
                                }
				$coverSection['sectionBody'][]=$awardBlock;
				$coverSection['sectionBody'][]=$indexBlock;
				$sections[]=$coverSection;
                                //print_r($sections);
			break;
                        case 13:
				$awardBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array("Name of the Principal",array("text"=>$this->aqsData['principal_name'],"style"=>"textBold")),
								array("Career Readiness Review Awarded",array("text"=>$this->awardName,"style"=>"blueColor textBold"))
							)
						),
						"style"=>"bordered awardBlock"
					);
				$coverSection['sectionBody'][]=$awardBlock;
				$coverSection['sectionBody'][]=$indexBlock;
				$sections[]=$coverSection;
			break;
			case 3:
				$sections[]=$coverSection;
				$section=array("sectionHeading"=>array("text"=>"Adhyayan Quality Standard Award Report"),"sectionBody"=>array());
				$indexBlock["style"].=" recomIndex";
				$section['sectionBody'][]=$indexBlock;
				if(count($this->aqsTeam)){
					$internalHtml='';
					$externalHtml="";
					foreach($this->aqsTeam as $member){
						$row='<tr><td>'.$member['name'].'</td><td>'.$member['designation'].'</td></tr>';
						if($member['isInternal']==1)
							$internalHtml.=$row;
						else
							$externalHtml.=$row;
					}
                                        
					if($isCollobrative==1){
                                         $block=array(
							"blockBody"=>array(
									"dataArray"=>array(
											array(
													'<div class="team-head">CSRE School Member/s:</div><table class="bordered"><tr class="bold"><td>Name</td><td>Designation</td></tr>'.$internalHtml.'</table>',
													'<div class="team-head">CSRE External Member/s:</div><table class="bordered"><tr class="bold"><td>Name</td><td>Designation</td></tr>'.$externalHtml.'</table>'
											)
									)
							),
							"style"=>"border-outer aqsTeam"
					);   
                                        }else{
					$block=array(
							"blockBody"=>array(
									"dataArray"=>array(
											array(
													'<div class="team-head">SSRE Team Member/s:</div><table class="bordered"><tr class="bold"><td>Name</td><td>Designation</td></tr>'.$internalHtml.'</table>',
													'<div class="team-head">SERE Team Member/s:</div><table class="bordered"><tr class="bold"><td>Name</td><td>Designation</td></tr>'.$externalHtml.'</table>'
											)
									)
							),
							"style"=>"border-outer aqsTeam"
					);
                                        }
					$section['sectionBody'][]=$block;
				}
				$sections[]=$section;
			break;
			case 9:
                            $coverSection['sectionBody']=array_merge($coverSection['sectionBody'],$this->getStudentInfoBlocks());
			    $coverSection['sectionBody'][]=$indexBlock;
			    $sections[]=$coverSection;
                            
                            break;
                        case 5:
				$coverSection['sectionBody']=array_merge($coverSection['sectionBody'],$this->getTeacherInfoBlocks());
				//$coverSection['sectionBody'][]=$indexBlock;
				$sections[]=$coverSection;
			break;
		}
		
		$this->sectionArray=array_merge($sections,$this->sectionArray);
                //echo "<pre>";print_r($this->sectionArray);
	}
        
        protected function generateSection_ScoreCardForKPAsRound2($skipComparisonSection=0,$lang_id,$diagnosticLabels=array(),$i,$isCollobrativeR1,$isCollobrativeR2){
                //$this->kpas1[]=$this->kpas[0];
                //$this->kpas1[]=$this->kpas[1];
                //echo "<pre>" ; print_r($this->kpas[1]);
               //echo "<pre>" ;print_r($this->kpas[0]);
  
		$totalKpas=count($this->kpas[0]);
		$schemeId = ($this->reportId==5 || $this->reportId==9)? 2:1;
		$comparisonSection=array();
                $isCollobrative=$this->config['isCollobrative'];
                //echo $isCollobrativeR1;
                //echo $isCollobrativeR2;
                
		if($skipComparisonSection==0 && $this->config['getCreateNetType']!=1){
			if($isCollobrative==1){
                        $kpaNum = 'Comparison of Reviews across '.$totalKpas.' Key Performance Areas';
                        }else{
                        $kpaNum = ($totalKpas)>1?str_replace('&',$totalKpas,$diagnosticLabels['kpa_performance_area_title']):str_replace('&','',$diagnosticLabels['kpa_performance_area_title']);
                        }
                        $indexKey=$this->addIndex($kpaNum);
			$comparisonSection=array("sectionHeading"=>array("text"=>"1. $kpaNum","style"=>"greyHead"),"sectionBody"=>array(),"indexKey"=>$indexKey);
		}
                
                
                if($isCollobrativeR1==1 && $isCollobrativeR2==0){
                 $kpaComparisonBlock=array(
								"blockHeading"=>array(
                        
									"data"=>array($diagnosticLabels['KPA'].". ".$diagnosticLabels['No']."",$diagnosticLabels['KPA2']."(KPA)","Collaborative Review Rating (Round1)",$diagnosticLabels['external_review_rating']."(Round2)")
								),
                     
								"blockBody"=>array(
									"dataArray"=>array()
								),
							"style"=>"bordered comparisonBlock"
						);   
                 
                }else if($isCollobrativeR1==0 && $isCollobrativeR2==1){
                 $kpaComparisonBlock=array(
								"blockHeading"=>array(
                        
									"data"=>array($diagnosticLabels['KPA'].". ".$diagnosticLabels['No']."",$diagnosticLabels['KPA2']."(KPA)",$diagnosticLabels['external_review_rating']."(Round1)","Collaborative Review Rating (Round2)")
								),
                     
								"blockBody"=>array(
									"dataArray"=>array()
								),
							"style"=>"bordered comparisonBlock"
						);   
                 
                }else if($isCollobrativeR1==1 && $isCollobrativeR2==1){
                 $kpaComparisonBlock=array(
								"blockHeading"=>array(
                        
									"data"=>array($diagnosticLabels['KPA'].". ".$diagnosticLabels['No']."",$diagnosticLabels['KPA2']."(KPA)","Collaborative Review Rating (Round1)","Collaborative Review Rating (Round2)")
								),
                     
								"blockBody"=>array(
									"dataArray"=>array()
								),
							"style"=>"bordered comparisonBlock"
						);   
                 
                }else if($isCollobrativeR1==0 && $isCollobrativeR2==0){
		$kpaComparisonBlock=array(
								"blockHeading"=>array(
                        
									"data"=>array($diagnosticLabels['KPA'].". ".$diagnosticLabels['No']."",$diagnosticLabels['KPA2']."(KPA)",$diagnosticLabels['external_review_rating']."(Round1)",$diagnosticLabels['external_review_rating']."(Round2)")
								),
								"blockBody"=>array(
									"dataArray"=>array()
								),
							"style"=>"bordered comparisonBlock"
						);
                }
                
              
		$kpa_count=0;
		$kpaSectionArray=array();
		$numberToAlpha=array(1=>"a",2=>"b",3=>"c",4=>"d");
		$kpaValuesForGraph=array();

          
              foreach($this->kpas[1] as $kpakey=>$kpa){
                  $this->kpas[0][$kpakey]['ExternalR2'] = $kpa['externalRating'];
                  //$this->kpas[0][$kpakey]['InternalR2'] = $kpa['internalRating'];
                  
              }//echo "<pre>";print_r($this->kpas[0]);
              
            //echo '<pre>'; print_r($this->keyQuestionsR);
            
              foreach($this->kpas[0] as $kpakey=>$kpa){
                //echo "<pre>";print_r($kpakey);

                       // echo "<pre>";print_r($this->kpas[0]);
                        //echo $this->kpas[0][$kpakey]['iscollebrative'];
                        //echo $this->kpas[1][$kpakey]['iscollebrative'];
                        
			$kpa_count++;
			
			$kpaValuesForGraph[]=array("values"=>array(isset($kpa['externalRating'])?$kpa['externalRating']['score']:0,isset($kpa['ExternalR2'])?$kpa['ExternalR2']['score']:0),"name"=>$kpa['KPA_name']);
                        $KPAScoreCard = str_replace('&',$kpa_count,$diagnosticLabels['Score_Card_KPA_Title']);
                        $kpaComparisonBlock['blockBody']['dataArray'][]=array(
                                                                            $kpa_count,
                                                                            $kpa['KPA_name'],
                                                                            '<span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"").'</span>',
                                                                            $this->subAssessmentType==1?'N/A':'<span class="'.(isset($kpa['ExternalR2'])?"score-".$kpa['ExternalR2']['score']:"").'">'.(isset($kpa['ExternalR2'])?$kpa['ExternalR2']['rating']:"")."</span>"

                                                                             );
                        
                        //echo"<pre>"; print_r($kpaComparisonBlock);
                        // echo "<pre>";//print_r($kpaComparisonBlock);
                       
			$indexKey=$this->addIndex($KPAScoreCard." - ".$kpa['KPA_name']);
                        
	
			$section=array("sectionHeading"=>array("text"=>"".$diagnosticLabels['KPA2']." (KPA $kpa_count) - ".$kpa['KPA_name'],"style"=>"greyHead"),"sectionBody"=>array(),"indexKey"=>$indexKey);
			//if($this->kpas[0][$kpakey]['iscollebrative']==1 || $this->kpas[1][$kpakey]['iscollebrative']==1){
                            //if($this->kpas[0][$kpakey]['iscollebrative']==1){
                            if($kpa['ExternalR2']['score'] > $kpa['externalRating']['score']){
                                $rating_img_kpa='<img src="'.SITEURL.'public/images/status-up.png" height="15" width="6">';
                            }
                            if($kpa['ExternalR2']['score'] < $kpa['externalRating']['score']){
                                $rating_img_kpa= '<img src="'.SITEURL.'public/images/status-down.png" height="15" width="6">';
                            }
                            if($kpa['ExternalR2']['score'] == $kpa['externalRating']['score']){
                                $rating_img_kpa= '';
                            }
                            if($isCollobrativeR1==1 && $isCollobrativeR2==0){
                                if($this->reportId!=5 && $this->reportId!=9 && $this->config['getCreateNetType']!=1){
                                        $kpaBlock=array(
                                                                "blockBody"=>array(
                                                                        "dataArray"=>array(
                                                                                array(
                                                                                      $kpa['KPA_name']." ".$diagnosticLabels['assessment_for'],
										'<span>Collaborative-Review Rating Round1&nbsp;</span><span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"").'</span><br><span>'.$diagnosticLabels['external_review_rating'].' Round2 &nbsp;</span><span class="'.(isset($kpa['ExternalR2'])?"score-".$kpa['ExternalR2']['score']:"").'">'.(isset($kpa['ExternalR2'])?$kpa['ExternalR2']['rating']:"").' '.$rating_img_kpa.'</span>')
                                                                                        
                                                                        )
                                                                ),
                                                                "style"=>"bordered kpablock"
                                                        );
                                        $section['sectionBody'][]=$kpaBlock;
                                }
    
                        }else if($isCollobrativeR1==0 && $isCollobrativeR2==1){
                                if($this->reportId!=5 && $this->reportId!=9 && $this->config['getCreateNetType']!=1){
                                        $kpaBlock=array(
                                                                "blockBody"=>array(
                                                                        "dataArray"=>array(
                                                                                array(
                                                                                      $kpa['KPA_name']." ".$diagnosticLabels['assessment_for'],
										'<span>'.$diagnosticLabels['external_review_rating'].' Round1 </span><span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"").'</span><br><span>Collaborative-Review Rating Round2 </span><span class="'.(isset($kpa['ExternalR2'])?"score-".$kpa['ExternalR2']['score']:"").'">'.(isset($kpa['ExternalR2'])?$kpa['ExternalR2']['rating']:"").' '.$rating_img_kpa.'</span><br/>')
                                                                                        
                                                                        )
                                                                ),
                                                                "style"=>"bordered kpablock"
                                                        );
                                        $section['sectionBody'][]=$kpaBlock;
                                }
    
                        }else if($isCollobrativeR1==1 && $isCollobrativeR2==1){
                                if($this->reportId!=5 && $this->reportId!=9 && $this->config['getCreateNetType']!=1){
                                        $kpaBlock=array(
                                                                "blockBody"=>array(
                                                                        "dataArray"=>array(
                                                                                array(
                                                                                      $kpa['KPA_name']." ".$diagnosticLabels['assessment_for'],
										'<span>Collaborative-Review Rating Round1</span> <span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"").'</span><br><span>Collaborative-Review Rating Round2 </span><span class="'.(isset($kpa['ExternalR2'])?"score-".$kpa['ExternalR2']['score']:"").'">'.(isset($kpa['ExternalR2'])?$kpa['ExternalR2']['rating']:"").' '.$rating_img_kpa.'</span>')
                                                                                        
                                                                        )
                                                                ),
                                                                "style"=>"bordered kpablock"
                                                        );
                                        $section['sectionBody'][]=$kpaBlock;
                                }
    
                        }else if($isCollobrativeR1==0 && $isCollobrativeR2==0){
                            
                        if($this->reportId!=5 && $this->reportId!=9 && $this->config['getCreateNetType']!=1){
				$kpaBlock=array(
							"blockBody"=>array(
								"dataArray"=>array(
									array(
										 $kpa['KPA_name']." ".$diagnosticLabels['assessment_for'],
										'<span>'.$diagnosticLabels['external_review_rating'].' Round1 &nbsp;</span><span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"").'</span><br><span>'.$diagnosticLabels['external_review_rating'].' Round2</span>&nbsp;&nbsp;<span class="'.(isset($kpa['ExternalR2'])?"score-".$kpa['ExternalR2']['score']:"").'">'.(isset($kpa['ExternalR2'])?$kpa['ExternalR2']['rating']:"").' '.$rating_img_kpa.'</span>')
								)
							),
							"style"=>"bordered kpablock"
						);
				$section['sectionBody'][]=$kpaBlock;
			}
                        
                        }
			$keyQ_count=0;
			$coreQsInKPA=0;//echo $kpa['kpa_instance_id'];
                        
                        foreach($this->keyQuestionsR[1][$kpa['kpa_instance_id']] as $kpakey=>$keyquestion){
                                    $this->keyQuestionsR[0][$kpa['kpa_instance_id']][$kpakey]['ExternalR2'] = $keyquestion['externalRating'];
                                    $this->keyQuestionsR[0][$kpa['kpa_instance_id']][$kpakey]['InternalR2'] = $keyquestion['internalRating'];
                        }
                                        
			if(isset($this->keyQuestionsR[0][$kpa['kpa_instance_id']])){
                            
				foreach($this->keyQuestionsR[0][$kpa['kpa_instance_id']] as $keyQ){
					$keyQ_count++;
                                        if($isCollobrativeR1==1 && $isCollobrativeR2==0){
                                         $jsBlock=array(
								"blockHeading"=>array(
									"data"=>array(
										array(
											"text"=>"".$diagnosticLabels['Key_Question']." (K.Q $keyQ_count) : &nbsp;&nbsp;".$keyQ['key_question_text'],
											"cSpan"=>10
										)
									)
								),
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['Sub_Question']." (S.Q)</span>"),
										array("<span>".$diagnosticLabels['Judgement_Statements']."</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span> ".$diagnosticLabels['external_review_rating']."(ERR)</span>":"<span>Collaborative-Review Rating R1</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span>".$diagnosticLabels['external_review_rating']." (ERR)</span>":"<span>".$diagnosticLabels['external_review_rating']." R2</span>"),
                                                                                array("<span class='nobr'>Change in Rating</span>")
										
									)
								),
							"style"=>"bordered kpaStyle".(($this->reportId==5)?' mb25':''),
							"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
						);   
                                        }else if($isCollobrativeR1==0 && $isCollobrativeR2==1){
					$jsBlock=array(
								"blockHeading"=>array(
									"data"=>array(
										array(
											"text"=>"".$diagnosticLabels['Key_Question']." (K.Q $keyQ_count) : &nbsp;&nbsp;".$keyQ['key_question_text'],
											"cSpan"=>10
										)
									)
								),
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['Sub_Question']." (S.Q)</span>"),
										array("<span>".$diagnosticLabels['Judgement_Statements']."</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span> ".$diagnosticLabels['external_review_rating']."(ERR)</span>":"<span>".$diagnosticLabels['external_review_rating']." R1</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span>".$diagnosticLabels['external_review_rating']." (ERR)</span>":"<span> Collaborative-Review Rating R2</span>"),
                                                                                array("<span class='nobr'>Change in Rating</span>")
										
									)
								),
							"style"=>"bordered kpaStyle".(($this->reportId==5)?' mb25':''),
							"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
						);
                                        }else if($isCollobrativeR1==1 && $isCollobrativeR2==1){
					$jsBlock=array(
								"blockHeading"=>array(
									"data"=>array(
										array(
											"text"=>"".$diagnosticLabels['Key_Question']." (K.Q $keyQ_count) : &nbsp;&nbsp;".$keyQ['key_question_text'],
											"cSpan"=>10
										)
									)
								),
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['Sub_Question']." (S.Q)</span>"),
										array("<span>".$diagnosticLabels['Judgement_Statements']."</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span> ".$diagnosticLabels['external_review_rating']."(SRR)</span>":"<span>Collaborative-Review Rating R1</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span>".$diagnosticLabels['external_review_rating']." (ERR)</span>":"<span>Collaborative-Review Rating R2</span>"),
                                                                                array("<span class='nobr'>Change in Rating</span>")
										
									)
								),
							"style"=>"bordered kpaStyle".(($this->reportId==5)?' mb25':''),
							"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
						);
                                        }else if($isCollobrativeR1==0 && $isCollobrativeR2==0){
					$jsBlock=array(
								"blockHeading"=>array(
									"data"=>array(
										array(
											"text"=>"".$diagnosticLabels['Key_Question']." (K.Q $keyQ_count) : &nbsp;&nbsp;".$keyQ['key_question_text'],
											"cSpan"=>10
										)
									)
								),
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['Sub_Question']." (S.Q)</span>"),
										array("<span>".$diagnosticLabels['Judgement_Statements']."</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span> ".$diagnosticLabels['external_review_rating']."(SRR)</span>":"<span>".$diagnosticLabels['external_review_rating']." R1</span>"),
										array($this->reportId==5||$this->reportId==7 || $this->reportId==9 || $this->reportId==10?"<span>".$diagnosticLabels['external_review_rating']." (ERR)</span>":"<span>".$diagnosticLabels['external_review_rating']." R2</span>"),
                                                                                array("<span class='nobr'>Change in Rating</span>")
										
									)
								),
							"style"=>"bordered kpaStyle".(($this->reportId==5)?' mb25':''),
							"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
						);
                                        }
                                        if($isCollobrativeR1==1 && $isCollobrativeR2==0){
					$cqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>Collaborative-Review Grade for S.Q. R1</span>"),
										array("<span>".$diagnosticLabels['external_review_sq']." R2</span>")
									)
                                                                    
								),
								"style"=>"bordered kpaStyle ",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							);
                                        }else if($isCollobrativeR1==0 && $isCollobrativeR2==1){
                                            $cqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['external_review_sq']." R1</span>"),
										array("<span>Collaborative-Review Grade for S.Q. R2</span>")
                                                                                
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							);
                                        }else if($isCollobrativeR1==1 && $isCollobrativeR2==1){
                                            $cqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>Collaborative-Review Grade for S.Q. R1</span>"),
										array("<span>Collaborative-Review Grade for S.Q. R2</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							);
                                        }else if($isCollobrativeR1==0 && $isCollobrativeR2==0){
                                            $cqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['external_review_sq']." R1</span>"),
										array("<span>".$diagnosticLabels['external_review_sq']." R2</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							);
                                        }
                                        
                                        if($isCollobrativeR1==1 && $isCollobrativeR2==0){
					$kqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span  class='nobr'>Collaborative-Review Grade for K.Q. R1</span>"),
										array("<span>".$diagnosticLabels['external_review_kq']." R2</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							);
                                        }else if($isCollobrativeR1==0 && $isCollobrativeR2==1){
                                           $kqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['external_review_kq']." R1</span>"),
										array("<span>Collaborative-Review Grade for K.Q. R2</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							); 
                                        }else if($isCollobrativeR1==1 && $isCollobrativeR2==1){
                                           $kqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>Collaborative-Review Grade for K.Q. R1</span>"),
										array("<span>Collaborative-Review Grade for K.Q. R2</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							); 
                                        }else if($isCollobrativeR1==0 && $isCollobrativeR2==0){
                                           $kqBlock=array(
								"blockBody"=>array(
									"dataArray"=>array(
										array("<span>".$diagnosticLabels['external_review_kq']." R1</span>"),
										array("<span>".$diagnosticLabels['external_review_kq']." R2</span>")
									)
								),
								"style"=>"bordered kpaStyle",
								"config"=>array('groupby'=>"kpa-".$keyQ['key_question_instance_id'])
							); 
                                        }//echo "<pre>";print_r($this->judgementStatement[1]);
                                        //echo $keyQ['key_question_instance_id'];echo '<br>';
                                        //echo $this->coreQuestions[$keyQ['key_question_instance_id']];echo '<br>';
                                        
                                        foreach($this->coreQuestionsR[1][$keyQ['key_question_instance_id']] as $kpakey=>$subquestion){
                                                $this->coreQuestionsR[0][$keyQ['key_question_instance_id']][$kpakey]['ExternalR2'] = $subquestion['externalRating'];
                                                $this->coreQuestionsR[0][$keyQ['key_question_instance_id']][$kpakey]['InternalR2'] = $subquestion['internalRating'];
                                        }
					$coreQ_count=0;
					if(isset($this->coreQuestionsR[0][$keyQ['key_question_instance_id']])){
						foreach($this->coreQuestionsR[0][$keyQ['key_question_instance_id']] as $coreQ){
                                                        //echo "<pre>";print_r($coreQ);
                                                        
							$coreQ_count++;
							$coreQsInKPA++;
							//echo $this->judgementStatement[1][$coreQ['core_question_instance_id']]['internalRating'];
							$jsBlock['blockBody']['dataArray'][0][]=array("text"=>"<span class=\"cQn\">$coreQsInKPA. ".$coreQ['core_question_text'].'</span>',"cSpan"=>3);
							$satatement_count=0;
                                                        
                                                        foreach($this->judgementStatement[1][$coreQ['core_question_instance_id']] as $kpakey=>$statment){
                                                                //echo "<pre>";print_r($statment);
                                                                 $this->judgementStatement[0][$coreQ['core_question_instance_id']][$kpakey]['ExternalR2'] = $statment['externalRating'];
                                                                
                                                           }
                                                           
                                                        
                                                           //echo "<pre>";print_r($this->judgementStatement[0]);
                                                        
							foreach($this->judgementStatement[0][$coreQ['core_question_instance_id']] as $statment){
                                                                //echo '<pre>';print_r($statment);
								$satatement_count++;
								
                                                                if($statment['ExternalR2']['score'] > $statment['externalRating']['score']){
                                                                    $rating_img_js='<img src="'.SITEURL.'public/images/status-up.png" height="15" width="6">';
                                                                }
                                                                if($statment['ExternalR2']['score'] < $statment['externalRating']['score']){
                                                                    $rating_img_js= '<img src="'.SITEURL.'public/images/status-down.png" height="15" width="6">';
                                                                }
                                                                if($statment['ExternalR2']['score'] == $statment['externalRating']['score']){
                                                                    $rating_img_js= '-';
                                                                }
                                                                
								//if($isCollobrativeR1==0 && $isCollobrativeR2==0){
                                                                $jsBlock['blockBody']['dataArray'][1][]=$coreQsInKPA.$numberToAlpha[$satatement_count];
								$jsBlock['blockBody']['dataArray'][2][]=array("text"=>isset($statment['externalRating'])?$statment['externalRating']['rating']:"","style"=>"colSize-1");
                                                                $jsBlock['blockBody']['dataArray'][3][]=array("text"=>$this->subAssessmentType==1?'N/A':(isset($statment['ExternalR2'])?$statment['ExternalR2']['rating']:""));
                                                                $jsBlock['blockBody']['dataArray'][4][]=array("text"=>$rating_img_js);
                                                                //}
                                                                //print_r($jsBlock['blockBody']['dataArray'][4]);
                                                                }
                                                                //echo '<pre>';print_r($coreQ);
                                                                
                                                        //echo $schemeId;   
                                                        if($coreQ['ExternalR2']['score'] > $coreQ['externalRating']['score']){
                                                            $rating_img_cq='<img src="'.SITEURL.'public/images/status-up.png" height="15" width="6">';
                                                        }
                                                        if($coreQ['ExternalR2']['score'] < $coreQ['externalRating']['score']){
                                                            $rating_img_cq= '<img src="'.SITEURL.'public/images/status-down.png" height="15" width="6">';
                                                        }
                                                        if($coreQ['ExternalR2']['score'] == $coreQ['externalRating']['score']){
                                                            $rating_img_cq= '';
                                                        }
                                                        
							$cqBlock['blockBody']['dataArray'][0][]=array("text"=>'<span class="scheme-'.$schemeId.' '.(isset($coreQ['externalRating'])?"score-".$coreQ['externalRating']['score']:"").'">'.(isset($coreQ['externalRating'])?$coreQ['externalRating']['rating']:'').'</span>',"style"=>"colSize-3");
                                                        $cqBlock['blockBody']['dataArray'][1][]=$this->subAssessmentType==1?'<span>N/A</span>':('<span class="scheme-'.$schemeId.' '.(isset($coreQ['ExternalR2'])?"score-".$coreQ['ExternalR2']['score']:"").'">'.(isset($coreQ['ExternalR2'])?$coreQ['ExternalR2']['rating']:'').' '.$rating_img_cq.'</span>');
                                                        
						}//echo '<pre>';print_r($cqBlock);
					}
                                        
                                        if($keyQ['ExternalR2']['score'] > $keyQ['externalRating']['score']){
                                            $rating_img_kq='<img src="'.SITEURL.'public/images/status-up.png" height="15" width="6">';
                                        }
                                        if($keyQ['ExternalR2']['score'] < $keyQ['externalRating']['score']){
                                            $rating_img_kq= '<img src="'.SITEURL.'public/images/status-down.png" height="15" width="6">';
                                        }
                                        if($keyQ['ExternalR2']['score'] == $keyQ['externalRating']['score']){
                                            $rating_img_kq= '';
                                        }
                                        
					$kqBlock['blockBody']['dataArray'][0][]='<span class="scheme-'.$schemeId.' '.(isset($keyQ['externalRating'])?"score-".$keyQ['externalRating']['score']:"").'">'.(isset($keyQ['externalRating'])?$keyQ['externalRating']['rating']:'').'</span>';
                                        $kqBlock['blockBody']['dataArray'][1][]=$this->subAssessmentType==1?'<span>N/A</span>':('<span class="scheme-'.$schemeId.' '.(isset($keyQ['ExternalR2'])?"score-".$keyQ['ExternalR2']['score']:"").'">'.(isset($keyQ['ExternalR2'])?$keyQ['ExternalR2']['rating']:'').' '.$rating_img_kq.'</span>');
                                        
                                        $section['sectionBody'][]=$jsBlock;
					if($this->reportId!=5){
						$section['sectionBody'][]=$cqBlock;
						$section['sectionBody'][]=$kqBlock;
					}
					if($this->reportId==5){
						$section['sectionBody'][]=$cqBlock;						
					}
                                        //echo '<pre>';print_r($cqBlock);
                                        //echo '<pre>';print_r($kqBlock);
				}
			}
			$kpaSectionArray[]=$section;
                    
		}
                
                //echo "<pre>";print_r($this->kpas[0]);
           
                
              
                
                
                
		if($skipComparisonSection==0 && $this->config['getCreateNetType']!=1){
                        $bar_keys=array(4=>isset($diagnosticLabels['Outstanding'])?$diagnosticLabels['Outstanding']:"Outstanding",3=>isset($diagnosticLabels['Good'])?$diagnosticLabels['Good']:"Good",2=>isset($diagnosticLabels['Variable'])?$diagnosticLabels['Variable']:"Variable",1=>isset($diagnosticLabels['Needs_Attention'])?$diagnosticLabels['Needs_Attention']:"Needs Attention");
                        
                        
                        //print_r($bar_keys);
			$comparisonSection['sectionBody'][]=$kpaComparisonBlock;
                        
			if($isCollobrativeR1==1 && $isCollobrativeR2==0){
			$graphBlock=array(
						"blockHeading"=>array(
							"data"=>$isCollobrative==1?array("Bar graph representation of above comparison"):array($diagnosticLabels['bar_graph_representation_title'])
						),
						"blockBody"=>array(
							"dataArray"=>array(
                                                            
								array($this->getGraphHTMLRound2($kpaValuesForGraph,$bar_keys,4,1,array("CSRE R1","SERE R2"),isset($diagnosticLabels['KPA_FULLFORM'])?$diagnosticLabels['KPA_FULLFORM']:"Key Performance Areas (KPAs)","Grades"))
							)
						),
					"style"=>"bordered barGraph"
				);
                        }else if($isCollobrativeR1==0 && $isCollobrativeR2==1){
			$graphBlock=array(
						"blockHeading"=>array(
							"data"=>$isCollobrative==1?array("Bar graph representation of above comparison"):array($diagnosticLabels['bar_graph_representation_title'])
						),
						"blockBody"=>array(
							"dataArray"=>array(
                                                            
								array($this->getGraphHTMLRound2($kpaValuesForGraph,$bar_keys,4,1,array("SERE R1","CSRE R2"),isset($diagnosticLabels['KPA_FULLFORM'])?$diagnosticLabels['KPA_FULLFORM']:"Key Performance Areas (KPAs)","Grades"))
							)
						),
					"style"=>"bordered barGraph"
				);
                        }else if($isCollobrativeR1==1 && $isCollobrativeR2==1){
			$graphBlock=array(
						"blockHeading"=>array(
							"data"=>$isCollobrative==1?array("Bar graph representation of above comparison"):array($diagnosticLabels['bar_graph_representation_title'])
						),
						"blockBody"=>array(
							"dataArray"=>array(
                                                            
								array($this->getGraphHTMLRound2($kpaValuesForGraph,$bar_keys,4,1,array("CSRE R1","CSRE R2"),isset($diagnosticLabels['KPA_FULLFORM'])?$diagnosticLabels['KPA_FULLFORM']:"Key Performance Areas (KPAs)","Grades"))
							)
						),
					"style"=>"bordered barGraph"
				);
                        }else if($isCollobrativeR1==0 && $isCollobrativeR2==0){
			$graphBlock=array(
						"blockHeading"=>array(
							"data"=>$isCollobrative==1?array("Bar graph representation of above comparison"):array($diagnosticLabels['bar_graph_representation_title'])
						),
						"blockBody"=>array(
							"dataArray"=>array(
                                                            
								array($this->getGraphHTMLRound2($kpaValuesForGraph,$bar_keys,4,1,array("SERE R1","SERE R2"),isset($diagnosticLabels['KPA_FULLFORM'])?$diagnosticLabels['KPA_FULLFORM']:"Key Performance Areas (KPAs)","Grades"))
							)
						),
					"style"=>"bordered barGraph"
				);
                        }
			$comparisonSection['sectionBody'][]=$graphBlock;
			
			$keysHeadBlock=array(
						"blockHeading"=>array(
							"data"=>array($diagnosticLabels['report_reading_key'])
						),
						"style"=>"onlyGreyHead",
						"config"=>array('groupby'=>"keyInfoBlock")
				);
			$comparisonSection['sectionBody'][]=$keysHeadBlock;
                        if($this->reportId==13){
			$keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels['Always'], str_replace("school", "college", $diagnosticLabels['Always_value'])),
								array($diagnosticLabels['Mostly'],str_replace("school", "college", $diagnosticLabels['Mostly_value'])),
								array($diagnosticLabels['Sometimes'],str_replace("school", "college", $diagnosticLabels['Sometimes_value'])),
								array($diagnosticLabels['Rarely'],str_replace("school", "college", $diagnosticLabels['Rarely_value']))
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);
                        }else{
                            $keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels['Always'],$diagnosticLabels['Always_value']),
								array($diagnosticLabels['Mostly'],$diagnosticLabels['Mostly_value']),
								array($diagnosticLabels['Sometimes'],$diagnosticLabels['Sometimes_value']),
								array($diagnosticLabels['Rarely'],$diagnosticLabels['Rarely_value'])
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);
                        }
			$comparisonSection['sectionBody'][]=$keysBodyBlock;
                        
                        
			$this->sectionArray=array_merge($this->sectionArray,array($comparisonSection));
                        
                        if($this->reportId==1  && $this->subAssessmentType!=1){
                        if($isCollobrative==0){    
                        //$this->sectionArray= array_merge($this->sectionArray,$judgedistance);
                        }
                        //$this->sectionArray= array_merge($this->sectionArray,$ratingperformance);
                        }
                        
                        $this->sectionArray= array_merge($this->sectionArray,$kpaSectionArray);
                        
                        //$this->sectionArray=array_merge($this->sectionArray,array($comparisonSection),$kpaSectionArray);
                        
		}else{
                        if($this->config['getCreateNetType']==1){
                            //$comparisonSection['sectionBody'][]=$kpaComparisonBlock;
                            $keysHeadBlock=array(
						"blockHeading"=>array(
							"data"=>array($diagnosticLabels['report_reading_key'])
						),
						"style"=>"onlyGreyHead",
						"config"=>array('groupby'=>"keyInfoBlock")
				);
			$comparisonSection['sectionBody'][]=$keysHeadBlock;
                          $keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($diagnosticLabels['Always'],$diagnosticLabels['Always_value']),
								array($diagnosticLabels['Mostly'],$diagnosticLabels['Mostly_value']),
								array($diagnosticLabels['Sometimes'],$diagnosticLabels['Sometimes_value']),
								array($diagnosticLabels['Rarely'],$diagnosticLabels['Rarely_value'])
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);  
                          $comparisonSection['sectionBody'][]=$keysBodyBlock;  
                        }
                        
                        if($this->config['getCreateNetType']==1){
                        $this->sectionArray=array_merge($this->sectionArray,array($comparisonSection));
                        }
                        
                        if($this->reportId==1 && $this->subAssessmentType!=1){
                        //$this->sectionArray= array_merge($this->sectionArray,$judgedistance);
                        //$this->sectionArray= array_merge($this->sectionArray,$ratingperformance);
                        }
                        
                        $this->sectionArray= array_merge($this->sectionArray,$kpaSectionArray);
                        
			//$this->sectionArray=array_merge($this->sectionArray,array($comparisonSection),$kpaSectionArray);

		}
                // if($round == 2){
                //get pi chart 
                        $piChartBlockHead=array(
						"blockHeading"=>array(
							"data"=>array('Comparative analysis for Round 1 and Round 2')
						),
						"style"=>"onlyGreyHead",
						"config"=>array('groupby'=>"keyInfoBlock")
				);
			$graphSection['sectionBody'][]=$piChartBlockHead;
                          $piChartBlockBody=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array($this->getPiChartHTML())
							)
						),
					"style"=>"pieGraph keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);  
                        $graphSection['sectionBody'][]=$piChartBlockBody;
                       // echo "<pre>";print_r($piChartBlock);die;
                       // $graphSection['sectionBody'][]=$piChartBlockBody;
                       // $this->sectionArray=array_merge($this->sectionArray,array($comparisonSection));
                         $this->sectionArray= array_merge($this->sectionArray,array($graphSection));
                         
                          $piChartBlock2=array(
						
						"blockBody"=>array(
							"dataArray"=>array(
                                                            
								array($this->getPiChartHTML(2))
							)
						),
					"style"=>"pieGraph keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
                        );
                        
                       // echo "<pre>";print_r($piChartBlock);die;
                        $graphSection2['sectionBody'][]=$piChartBlock2;
                       // $this->sectionArray=array_merge($this->sectionArray,array($comparisonSection));
                        $this->sectionArray= array_merge($this->sectionArray,array($graphSection2));
                //}
                
	}
/********************************Functions of comaparitive report round 2 Ends ******************/
}