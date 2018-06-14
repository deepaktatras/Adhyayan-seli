<?php

class groupReport extends reportClass{
	protected $assessments;
	function __construct($group_assessment_id,$report_id,$diagnostic_id,$conductedDate='',$validDate='',$centre_id=0,$dept_id=0){
		$this->diagnosticId=$diagnostic_id;
		$diagnosticModel = new diagnosticModel();
		$assessmentModel = new assessmentModel();
		$diagnosticName = $diagnosticModel->getDiagnosticBYLang($diagnostic_id);
		$teacherCatData = $assessmentModel->getTeacherCategoryForDiagnostic($diagnostic_id);
		$this->teacherCategoryName = isset($teacherCatData['teacher_category'])?$teacherCatData['teacher_category']:0;
		$this->diagnosticName = $diagnosticName['name'];
		$this->groupAssessmentId=$group_assessment_id;
		$this->assessments ='';
		$this->rankArray = array();
		$this->SQRatingsArr = null;
                $this->centre_id = $centre_id;
                $this->dept_id = $dept_id;
                $this->rankArrayCentre = array();
		$this->SQRatingsArrCentre = null;
                $this->SQRatingsArrCentre_final = null;
                $this->SQRatingsArrCentre_batchesfinal=null;
                $this->SQRatingsArrCentre_date=null;
                $this->student_round=isset($_GET['round_id'])?$_GET['round_id']:null;
                
                $this->groupAssessmentId_r1=null;
		parent::__construct($report_id,$conductedDate,$validDate);
	}
	
	public function generateOutput(){
                set_time_limit(0);
                ini_set('memory_limit', '1024M');        
		switch($this->reportId){
			case 4:
				return $this->generateOverviewTeacherOutput();
				break;
                        case 8:
				return $this->generateOverviewStudentOutput();
				break;
                        case 11:
				return $this->generateOverviewStudentCenterOutput();
				break;
                        case 12:
				return $this->generateOverviewStudentOrgOutput();
				break;
		}
	}
        
        public function generateExcelOutput(){
		switch($this->reportId){
                        case 8:
				return $this->generateOverviewStudentExcelOutput();
				break;    
		}
	}
        
        public function generateRound2Output(){
            
        }
	protected function generateOverviewTeacherOutput(){
                $this->loadAqsData();
		$this->loadJudgementalStatements();
		$this->loadCoreQuestions();
		$this->loadKeyQuestions();
		$this->loadKpas();
		$assessmentModel=new assessmentModel();
		$assessments=$assessmentModel->getAssessmentsInGroupAssessmentDiagnostic($this->groupAssessmentId,$this->diagnosticId,DEFAULT_LANGUAGE,$this->dept_id);
		$this->assessments = $assessments;
		$numTeachersCompletedAsst = $assessmentModel->getAssessmentCountInGroupAssessmentDiagnostic($this->groupAssessmentId,$this->diagnosticId,$this->dept_id);
		$departmentText='';
                $logo_reviewers=$assessmentModel->ExternalAssessorsGrouped($this->aqsData['client_id'],$this->groupAssessmentId);
                $logo_count_reviewers=count($logo_reviewers);
                if(!empty($this->dept_id)){
                    $department=$assessmentModel->getSchoolDepartment($this->dept_id);
                    $departmentText='<div><b>Department - '.$department['school_level'].'</b></div><br>';
                }
                
                $pdf = new reporttcpdf ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		$pdf->footer_text = $this->config['coverAddress'];
		$pdf->other_footer_text = 'Teacher Performance Review: Overview Report for '.$this->aqsData['school_name'].' '.$this->aqsData['school_address'].', '.date("d-m-Y").' (generated on)';// 'AQS Teacher Performance - Overview Report for '.$this->aqsData['school_name'].', '.date("d-m-Y").' (generated on)';
		$pdf->footerBG = $this->config['footerBG'];
		$pdf->footerColor = $this->config['footerColor'];
		$pdf->footerHeight = $this->config['footerHeight'];
		$pdf->pageNoBarHeight = $this->config['pageNoBarHeight'];
                $pdf->assessemnt_type = 2;
		$pdf->SetTitle('Teacher Performance Overview Report '.' - '.$this->teacherCategoryName.' - '.$this->aqsData['school_name']);
		$pdf->SetHeaderData ( '', '', 'Teacher Performance Overview Report - '.$this->teacherCategoryName.' - '.$this->aqsData['school_name'] );
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
		$numTeachers = count($this->assessments);
		$incompleteReviewUsers = $assessmentModel->getAssessmentCountInGroupAssessmentDiagnostic($this->groupAssessmentId,$this->diagnosticId,$this->dept_id);
		
                $teachers ='';
		foreach($incompleteReviewUsers as $key=>$val)
			$teachers .= $val['name'].', ';
		$teachers!=''?($teachers='<span style="font-size:8;"><br/>Note - Review is not completed by the following teachers:<br/>'.rtrim($teachers,', ').'</span>'):'';
		//$pdf->SetTextColor ( 255, 255, 255 );
		//$pdf->SetFillColor ( 108, 13, 16 );
		$firstpagehtml = '';
		if($this->aqsData['client_id']==11)
			$firstpagehtml = '<table class="pdfHdr broad"> <tbody><tr><td class="halfSec fl" align="left"><a href=""><img src="' . SITEURL . 'public/images/logo.png"alt="" style="height:55px;"></a></td><td class="halfSec fr" align="right" ><a href=""><img src="'.SITEURL.'/public/images/shishuvan.jpg" alt=""></a></td></tr>
		<tr><td colspan="2" style="font-weight:bold;text-align:center;font-size:13px;">The Teacher Performance Review has been initiated and validated by '.($this->aqsData['school_name']).'</td></tr>
		</table>';
                else if($this->aqsData['client_id']==27 && $logo_count_reviewers>0)
			$firstpagehtml = '<table class="pdfHdr broad" border="0" style="padding:5px;"> <tbody><tr><td class="halfSec fl" align="left"><a href=""><img src="' . SITEURL . 'public/images/logo.png"alt="" style="height:55px;"></a></td><td class="halfSec fr" align="right"><a href="" ><img src="'.SITEURL.'/public/images/dominicsavio.jpg"  height="70px" alt=""></a></td></tr>
		<tr><td colspan="2" style="font-weight:bold;text-align:center;font-size:13px;">The Teacher Performance Review has been initiated and validated by '.($this->aqsData['school_name']).'</td></tr>
		</table>';
		else
		 $firstpagehtml = '<br/><div style="text-align:center;"><img src="' . SITEURL . 'public/images/logo.png"></div><br/><br/>';
		
                $this->aqsData['school_aqs_pref_start_date']=(!empty($this->aqsData['school_aqs_pref_start_date']))?date("d-M-Y",strtotime($this->aqsData['school_aqs_pref_start_date'])):'';
                $this->aqsData['school_aqs_pref_end_date']=(!empty($this->aqsData['school_aqs_pref_start_date']))?date("d-M-Y",strtotime($this->aqsData['school_aqs_pref_end_date'])):'';
               
                $firstpagehtml .= <<<EOD
		<div style="background-color:#800000;color:#FFFFFF;text-align:center;"><br/><br/><br/>
		<h2 style="text-align:center">Teacher Performance Review: Overview Report <br/></h2><div></div>
                $departmentText
                       
		School level analysis of teacher's performance against the standard<br/><br/><br/><br/><br/>
		<b>$numTeachers</b> teacher(s) reviewed between <b>{$this->aqsData['school_aqs_pref_start_date']}</b> to <b>{$this->aqsData['school_aqs_pref_end_date']}</b><br/><br/><br/><br/><br/>	
		</div>
		$teachers
EOD;
//echo $firstpagehtml;die;
				$pdf->writeHTMLCell ( 0, 0, '', '', $firstpagehtml, 0, 1, 0, true, 'J', true );
				// ---------------------------------------------------------
				// create some content ...
				// add a page
				// first page start
				//award
				$sectionNum = 1;
				$pdf->addPage ();
				$pdf->Bookmark ( $sectionNum.'. Teacher Performance Review: Process and Grades', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 108, 13, 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Teacher Performance Review: Process and Grades', 0, 1, 'C', true );
				$pdf->SetFont ( 'times', '', 10 );
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->ln(3);
				$html = 'Adhyayan undertook the review of '.$numTeachers.' teachers of '.$this->aqsData['school_name'].'.  Adhyayan worked alongside the teachers to facilitate their understanding of \'What Good Teaching & Learning Look Like\' across the world through the Adhyayan review process explained below.';				
				$pdf->Write ( 0, $html, '', 0, '', true, 0, false, false, 0 );
				$pdf->ln(1);				
				$html = $this->getPDFreviewProcess();				
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				$pdf->AddPage();
				$pdf->Bookmark ( $sectionNum.'. Teacher Performance Review: Grade definition', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 108, 13, 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Teacher Performance Review: Grade definition', 0, 1, 'C', true );
				$pdf->Ln(2);
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->SetTextColor ( 0, 0, 0 );
				$html = $this->getTeacherOverviewAwardDefinition();
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				/* $pdf->AddPage();
				$pdf->Bookmark ( '2. Key for reading the report', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 108, 13, 16 );
				$pdf->Ln(3);
				$pdf->Cell ( 0, 8, 'Key for reading the report', 0, 1, 'C', true );
				$html = '<div style="page-break-inside:avoid;">'.$this->getPDFKeyForReadingReport().'</div>';
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->Ln(3);
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true ); */
				//awards				
				//sub-question wise performance
				$pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 108, 13, 16 );
				$pdf->Bookmark ( $sectionNum.'. Performance Review: Sub question wise', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Performance Review: Sub question wise', 0, 1, 'C', true );
				$html_tchrperf = $this->generateTchrs_PerfReview();
				$html = $this->getTeacherSQRatings();
				$this->tableNum++;
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->ln(3);
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				//teacher wise performance
	       		$pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 108, 13, 16 );
				$pdf->Bookmark ( $sectionNum.'. Performance Review: Teacher wise', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Performance Review: Teacher wise', 0, 1, 'C', true );				
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;"><br/>'.$html_tchrperf.'</div>', 0, 1, 0, true, 'J', true );
				//judgement distance
				$pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 108, 13, 16 );
				$pdf->Bookmark ( $sectionNum.'. Teacher\'s effectiveness in applying the teacher review diagnostic tool', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Teacher\'s effectiveness in applying the teacher review diagnostic tool', 0, 1, 'C', true );
				$pdf->Ln(2);
				$html = 'The following graph depicts the level of agreements and disagreements the Self-Review Rating (SRR) team shared with the External Review Rating (ERR) team on the judgment statements. Limitations (explained in the next section) of the use of the diagnostic may also contribute to agreements and difference in agreements between the Self-Review Rating (SRR) and External Review Rating (ERR), which is often indicative of the level of experience between the two teams regarding global standards.<br/>
						<br/>The expectation is that JD=0 and 1 would increase significantly when the teacher does its self-review validation for the second time; and by the third validation, be almost 100%. In this manner the teacher would achieve the rigour and objectivity of an external reviewer.';
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				
				//create jd graph
				//$url ='?';
				$url_count = 0;
				$url = array("?");				
				$rankArray = array();
				$urlArray = array();
				$i=0;$k=0;
				$jd0 = 0;$jd1 = 0;$jd2 = 0;$jd3 = 0;
				foreach($this->assessments as $a){
                                        /*echo"<pre>";
                                        print_r($a);
                                        echo"</pre>";*/
					$i++;
					if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1 && isset($this->kpas[$a['assessment_id']])){						
						$jd0 = 0;$jd1 = 0;$jd2 = 0;$jd3 = 0;
						foreach($this->kpas[$a['assessment_id']] as $kpa){
							if(isset($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']])){
								foreach($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']] as $kq){
									if(isset($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']])){
										foreach($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']] as $cq){
											foreach($this->judgementStatement[$a['assessment_id']][$cq['core_question_instance_id']] as $statment){
												$jd = abs($statment['internalRating']['score'] - $statment['externalRating']['score']);
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
											}
										}
									}
				
								}
							}
						}
						$weightage=0;						
						$weightage = $jd0*4 +$jd1*3 +$jd2*2 +$jd3*1;
						$rankArray[$a['data_by_role']['3']['user_name']] = $weightage;
						//$url[$url_count] .= $i.'='.urlencode($a['data_by_role']['3']['user_name']).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
						$urlArray[$a['data_by_role']['3']['user_name']] = $i.'='.urlencode($a['data_by_role']['3']['user_name']).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
					}
				}
				
				arsort($rankArray,SORT_NUMERIC);				
				//url			
				$isGraphPage1 = 1;
				foreach($rankArray as $key=>$val){					
					$k++;
					if(($k%13==0 && $isGraphPage1==1)|| (($k+8)%21==0 && $isGraphPage1==0)){//after 13 schools create a new image
						$isGraphPage1 =0;
						$url_count++;
						$url[$url_count]="?";
					}
					$url[$url_count] .= $urlArray[$key];
				}				
				$this->rankArray = $rankArray;
				//print_r($rankArray);die;
				//$uri = $this->createURISqJdTeachers_AccuracySelfReview();
				$graph='';
				$this->tableNum++;
				for($i=0;$i<=$url_count;$i++){
					$graph .= '<div style="page-break-inside:avoid;"><div style="text-align:center;font-weight:bold;"><span style="font-weight:normal;">Table '.$this->tableNum.'</span><br/>Judgement distance between teacher and external assessor from highest to lowest</div><img style="border:solid 1px #000000;" src="' . SITEURL . 'library/stacked.chart_jd_gen.php' . $url[$i] . '" /></div>';													
				}
				//echo $graph;die;
				$pdf->writeHTMLCell ( 0, 0, '', '', $html.$graph, 0, 1, 0, true, 'J', true );
				//Accuracy of self-review (ranked from highest to lowest)
				/* $pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 108, 13, 16 );
				$pdf->Bookmark ( '5. Accuracy of self-review (ranked from highest to lowest)', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, 'Accuracy of self-review (ranked from highest to lowest)', 0, 1, 'C', true );
				$pdf->Ln(2);
				$html = $this->getTchrRank();
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;"><br/>'.$html.'</div>', 0, 1, 0, true, 'J', true ); */
				//limitations of first use of diagnostic
				$pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 108, 13, 16 );
				$pdf->Bookmark ( $sectionNum.'. Limitations of first use of the diagnostic', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Limitations of first use of the diagnostic', 0, 1, 'C', true );
				$pdf->Ln(2);
				$html = $this->getLimitationsFirstUse_Diagnostic();
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;"><br/>'.$html.'</div>', 0, 1, 0, true, 'J', true );
				//$pdf->AddPage();
				$this->getPDFIndex($pdf);
				$pdf->Output ('Teacher Performance Overview Report - '.$this->aqsData['school_name'].'.pdf', 'I' );
	}
        
                       function hex2rgb($hex) {
                                $hex = str_replace("#", "", $hex);

                                if(strlen($hex) == 3) {
                                   $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                                   $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                                   $b = hexdec(substr($hex,2,1).substr($hex,2,1));
                                } else {
                                   $r = hexdec(substr($hex,0,2));
                                   $g = hexdec(substr($hex,2,2));
                                   $b = hexdec(substr($hex,4,2));
                                }
                                $rgb = array($r, $g, $b);
                                //return implode(",", $rgb); // returns the rgb values separated by commas
                                return $rgb; // returns an array with the rgb values
                        }
        
        function cellColor($objPHPExcel,$cells,$color){

        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
        ));
       }
       
        protected function generateOverviewStudentExcelOutput(){
                                                                                                       
                $this->loadAqsDataStudent();
                //print_r($this->aqsData);
		$this->loadJudgementalStatements();
		$this->loadCoreQuestions();
		$this->loadKeyQuestions();
		$this->loadKpas();
                
                
                 
		$assessmentModel=new assessmentModel();
		$assessments=$assessmentModel->getAssessmentsInGroupAssessmentDiagnostic($this->groupAssessmentId,$this->diagnosticId);
		$this->assessments = $assessments;
		$numTeachersCompletedAsst = $assessmentModel->getAssessmentCountInGroupAssessmentDiagnostic($this->groupAssessmentId,$this->diagnosticId);
		//$pdf->assessemnt_type = 4;
                $numTeachers = count($this->assessments);
		$incompleteReviewUsers = $assessmentModel->getAssessmentCountInGroupAssessmentDiagnostic($this->groupAssessmentId,$this->diagnosticId);
		$teachers ='';
		$round=$this->aqsData['student_round'];
                $school=$this->aqsData['school_name'];
                //$html_tchrperf = $this->generateStu_PerfReview();
                //die("ddd");
                
                $student_round=isset($assessments[0]['student_round'])?$assessments[0]['student_round']:1;
                $this->student_round=$student_round;
                
                if($this->student_round==2){
                $batch=isset($assessments[0]['client_id'])?$assessments[0]['client_id']:0;
                $groupdata=$assessmentModel->getGAIdfromClientandRound($batch,1);
                $gaid=$groupdata['group_assessment_id'];
                
                //$assessments_r1=$assessmentModel->getAssessmentsInGroupAssessmentDiagnostic_rounds($gaid,$this->groupAssessmentId,$this->diagnosticId,$ids_allow);
                //$assessments_r1=$this->db->array_grouping($assessments_r1,"user_ids");
                
		//$this->assessments_r1 = $assessments_r1;
                $this->groupAssessmentId_r1=$gaid;
                
                }
                
		//echo $html = $this->getStudentKQRatings();  
                require_once(ROOT."library/PHPExcel/Classes/PHPExcel.php");
                $objPHPExcel = new PHPExcel();
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="'.$school.'_round'.$round.'.xlsx"');
                header('Cache-Control: max-age=0');
                $objPHPExcel->getProperties()
                        ->setCreator("PHPOffice")
                        ->setLastModifiedBy("PHPOffice")
                        ->setTitle("PHPExcel Test Document")
                        ->setSubject("PHPExcel Test Document")
                        ->setDescription("Test document for PHPExcel, generated using PHP classes.")
                        ->setKeywords("Office PHPExcel php")
                        ->setCategory("Test result file");
                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->setTitle("CRR_Batch level report_Round ".$round."");
                $objPHPExcel->getSheetByName("CRR_Batch level report_Round ".$round."")->setSheetState(PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN);
                
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A1", 'Career Readiness Review Overview Report '.'-'.$this->aqsData['school_name'].'');
                $objPHPExcel->getActiveSheet()->getStyle("A1:A1")->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A1:A1")->getFont()->setSize(15);
                $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
                
                $row=19;
                $i=1;
		$overallArray = null;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Performance Review: Participants");
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':J'.$row.'');
                $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':A'.$row.'')->getFont()->setBold(true);

                $row++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Participants");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row."", "SQ1");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row."", "SQ2");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row."", "SQ3");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E".$row."", "SQ4");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("F".$row."", "SQ5");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("G".$row."", "SQ6");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("H".$row."", "SQ7");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("I".$row."", "SQ8");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("J".$row."", "SQ9");
                                $row++;
                $array_aplha=array("B","C","D","E","F","G","H","I","J");
                $array_rating=array("5"=>"Exceptional","4"=>"Proficient","3"=>"Developing","2"=>"Emerging","1"=>"Foundation");
                $color_rating=array("5"=>"2e5ce6","4"=>"47b247","3"=>"e8bf19","2"=>"ce7230","1"=>"ff0000");
                $array_key_result=array();
                $exceptional_1=0;
                $proficient_1=0;
                $developing_1=0;
                $emerging_1=0;
                $foundation_1=0;
                
                $exceptional_2=0;
                $proficient_2=0;
                $developing_2=0;
                $emerging_2=0;
                $foundation_2=0;
                
                $exceptional_3=0;
                $proficient_3=0;
                $developing_3=0;
                $emerging_3=0;
                $foundation_3=0;
                $tot_stu=0;
		foreach($this->assessments as $a){								
			if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1 && isset($this->kpas[$a['assessment_id']])){
				//$rows .= '<tr><td width="28%" style="border:solid 1px #000000;">'.$a['data_by_role']['3']['user_name'].'</td>';
			          $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", $a['data_by_role']['3']['user_name']);
                                                                                            
                            foreach($this->kpas[$a['assessment_id']] as $kpa){
					if(isset($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']])){
						$k=0;
                                                $kk=0;
						foreach($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']] as $kq){
							$k++;
							$overallArray['Key Question '.$k]['text'] = $kq['key_question_text'];
                                                        
                                                        //$array_key_result_1=array();
                                                        $array_key_result[$k]['text']=$kq['key_question_text'];
							//$html .= '<table><tr><td rowspan="2"><b>Overall</b></td><td>'.$kq['key_question_text'].'</td></tr>';							
							if(isset($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']])){	
								$j=0;
                                                                $tot_key=0;
								foreach($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']] as $cq){		
									$j++;
                                                                       /* $overallArray['Key Question '.$k]['Sub Question '.$j]['score1']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score2']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score3']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score4']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score5']=0;*/
									//core_question_stmt
									$overallArray['Key Question '.$k]['Sub Question '.$j]['text_stmt'] = $cq['core_question_stmt'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['text'] = $cq['core_question_text'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['key_question_instance_id'] =  $kq['key_question_instance_id'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['score'.$cq['externalRating']['score']]++;
									//$html .= '<td>SQ'.$i.'</td>';
									//$rows .='<td width="8%" style="border:solid 1px #000000;" class="scheme-2-score-'.$cq['externalRating']['score'].'"></td>';
			                                                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("".$array_aplha[($kk)]."".$row."", $cq['externalRating']['rating']);
                                                                        $this->cellColor($objPHPExcel,"".$array_aplha[($kk)]."".$row."","".$color_rating[$cq['externalRating']['score']]."");
                                                                        
                                                                        $tot_key=$tot_key+$cq['externalRating']['score'];
                                                                         //echo "".$array_aplha[($kk-1)]."".$row."";
                                                                        $i++;
                                                                        $kk++;
                                                                       
								}
                                                                $c=round($tot_key/3);
                                                                if($c==5 && $k==1) $exceptional_1=$exceptional_1+1;
                                                                if($c==4 && $k==1) $proficient_1=$proficient_1+1;
                                                                if($c==3 && $k==1) $developing_1=$developing_1+1;
                                                                if($c==2 && $k==1) $emerging_1=$emerging_1+1;
                                                                if($c==1 && $k==1) $foundation_1=$foundation_1+1;
                                                                
                                                                if($c==5 && $k==2) $exceptional_2=$exceptional_2+1;
                                                                if($c==4 && $k==2) $proficient_2=$proficient_2+1;
                                                                if($c==3 && $k==2) $developing_2=$developing_2+1;
                                                                if($c==2 && $k==2) $emerging_2=$emerging_2+1;
                                                                if($c==1 && $k==2) $foundation_2=$foundation_2+1;
                                                                
                                                                if($c==5 && $k==3) $exceptional_3=$exceptional_3+1;
                                                                if($c==4 && $k==3) $proficient_3=$proficient_3+1;
                                                                if($c==3 && $k==3) $developing_3=$developing_3+1;
                                                                if($c==2 && $k==3) $emerging_3=$emerging_3+1;
                                                                if($c==1 && $k==3) $foundation_3=$foundation_3+1;
							}
						}
                                                $row++;
					}
					//$rows .= '</tr>';
                                         
				}
			$tot_stu++;			
			}			
		}
                
                $BStyle = array(
                'borders' => array(
                'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
                )
                )
                );
                
                $kquestion="Key Questions\n";
                foreach($array_key_result as $key=>$val){
                
                $kquestion.="".$key.") ".$val['text']."\n";    
                }
                $objPHPExcel->getActiveSheet()->getStyle('A20:J'.($row-1).'')->applyFromArray($BStyle);
		$row2=$row;
                $this->SQRatingsArr = $overallArray;
                
                
                $row=2;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Performance Review: Key questions");
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':J'.$row.'');
                $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':A'.$row.'')->getFont()->setBold(true);
                $row++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Overall");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row."", "Key question 1: Self-awareness");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row."", "Key question 2: Career-awareness");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row."", "Key question 3: Skills and mindsets");
                $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':D'.$row.'')->getAlignment()->setWrapText(true);
                $row++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Exceptional");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row."", round(($exceptional_1*100/$tot_stu),1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row."", round(($exceptional_2*100/$tot_stu),1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row."", round(($exceptional_3*100/$tot_stu),1));
                $this->cellColor($objPHPExcel,"A".$row.":A".$row."",$color_rating[5]);
                
                $row++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Proficient");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row."", round(($proficient_1*100/$tot_stu),1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row."", round(($proficient_2*100/$tot_stu),1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row."", round(($proficient_3*100/$tot_stu),1));
                $this->cellColor($objPHPExcel,"A".$row.":A".$row."",$color_rating[4]);
                $row++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Developing");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row."", round(($developing_1*100/$tot_stu),1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row."", round(($developing_2*100/$tot_stu),1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row."", round(($developing_3*100/$tot_stu),1));
                $this->cellColor($objPHPExcel,"A".$row.":A".$row."",$color_rating[3]);
                $row++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Emerging");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row."", round(($emerging_1*100/$tot_stu),1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row."", round(($emerging_2*100/$tot_stu),1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row."", round(($emerging_3*100/$tot_stu),1));
                $this->cellColor($objPHPExcel,"A".$row.":A".$row."",$color_rating[2]);
                $row++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Foundation");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row."", round(($foundation_1*100/$tot_stu),1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row."", round(($foundation_2*100/$tot_stu),1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row."", round(($foundation_3*100/$tot_stu),1));
                $this->cellColor($objPHPExcel,"A".$row.":A".$row."",$color_rating[1]);
                
                $objPHPExcel->getActiveSheet()->getStyle('A3:D'.($row).'')->applyFromArray($BStyle);
                
                $objPHPExcel->getActiveSheet()->getStyle('E3:J8')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->mergeCells('E3:J8');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E3", $kquestion);
                $row++;
                $row++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Performance Review: Sub questions");
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':J'.$row.'');
                $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':A'.$row.'')->getFont()->setBold(true);
                $row++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Overall");
                $i=1;
                foreach($this->SQRatingsArr as $key=>$arr){			
		//	print_r($arr);
			//$total = intval($arr['Sub Question 1']['score1']) + intval($arr['Sub Question 1']['score2']) + intval($arr['Sub Question 1']['score3']) + intval($arr['Sub Question 1']['score4']) + intval($arr['Sub Question 1']['score5']);
			$total = floatval($arr['Sub Question 1']['score1']) + floatval($arr['Sub Question 1']['score2']) + floatval($arr['Sub Question 1']['score3']) + floatval($arr['Sub Question 1']['score4']) + floatval($arr['Sub Question 1']['score5']);			
			$html="";
                        
                $row1=$row;        
                if($i==1){
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row1."", 'Sub question '.(++$sqnum).'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row1."", 'Sub question '.(++$sqnum).'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row1."", 'Sub question '.(++$sqnum).'');
                
                $row1++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row1."", ''.$arr['Sub Question 1']['text'].'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row1."", ''.$arr['Sub Question 2']['text'].'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row1."", ''.$arr['Sub Question 3']['text'].'');
                
                $objPHPExcel->getActiveSheet()->getStyle('A'.$row1.':J'.$row1.'')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle("A".$row1.":J".$row1."")->getFont()->setSize(8);

               
                $row1++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row1."", "Exceptional");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row1."", round($arr['Sub Question 1']['score5']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row1."", round($arr['Sub Question 2']['score5']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row1."", round($arr['Sub Question 3']['score5']*100/$total,1));
                $this->cellColor($objPHPExcel,"A".$row1.":A".$row1."",$color_rating[5]);
                $row1++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row1."", "Proficient");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row1."", round($arr['Sub Question 1']['score4']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row1."", round($arr['Sub Question 2']['score4']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row1."", round($arr['Sub Question 3']['score4']*100/$total,1));
                $this->cellColor($objPHPExcel,"A".$row1.":A".$row1."",$color_rating[4]);
                $row1++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row1."", "Developing");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row1."", round($arr['Sub Question 1']['score3']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row1."", round($arr['Sub Question 2']['score3']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row1."", round($arr['Sub Question 3']['score3']*100/$total,1));
                $this->cellColor($objPHPExcel,"A".$row1.":A".$row1."",$color_rating[3]);
                $row1++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row1."", "Emerging");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row1."", round($arr['Sub Question 1']['score2']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row1."", round($arr['Sub Question 2']['score2']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row1."", round($arr['Sub Question 3']['score2']*100/$total,1));
                $this->cellColor($objPHPExcel,"A".$row1.":A".$row1."",$color_rating[2]);
                $row1++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row1."", "Foundation");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row1."", round($arr['Sub Question 1']['score1']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row1."", round($arr['Sub Question 2']['score1']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row1."", round($arr['Sub Question 3']['score1']*100/$total,1));
                $this->cellColor($objPHPExcel,"A".$row1.":A".$row1."",$color_rating[1]);
                }else if($i==2){
                    
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E".$row1."", 'Sub question '.(++$sqnum).'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("F".$row1."", 'Sub question '.(++$sqnum).'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("G".$row1."", 'Sub question '.(++$sqnum).'');
                $row1++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E".$row1."", ''.$arr['Sub Question 1']['text'].'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("F".$row1."", ''.$arr['Sub Question 2']['text'].'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("G".$row1."", ''.$arr['Sub Question 3']['text'].'');
               
                $row1++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E".$row1."", round($arr['Sub Question 1']['score5']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("F".$row1."", round($arr['Sub Question 2']['score5']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("G".$row1."", round($arr['Sub Question 3']['score5']*100/$total,1));
                
                $row1++;
                
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E".$row1."", round($arr['Sub Question 1']['score4']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("F".$row1."", round($arr['Sub Question 2']['score4']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("G".$row1."", round($arr['Sub Question 3']['score4']*100/$total,1));
                
                $row1++;
             
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E".$row1."", round($arr['Sub Question 1']['score3']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("F".$row1."", round($arr['Sub Question 2']['score3']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("G".$row1."", round($arr['Sub Question 3']['score3']*100/$total,1));
                
                $row1++;
               
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E".$row1."", round($arr['Sub Question 1']['score2']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("F".$row1."", round($arr['Sub Question 2']['score2']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("G".$row1."", round($arr['Sub Question 3']['score2']*100/$total,1));
                
                $row1++;
                
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E".$row1."", round($arr['Sub Question 1']['score1']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("F".$row1."", round($arr['Sub Question 2']['score1']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("G".$row1."", round($arr['Sub Question 3']['score1']*100/$total,1));
                }else if($i==3){
                    
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("H".$row1."", 'Sub question '.(++$sqnum).'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("I".$row1."", 'Sub question '.(++$sqnum).'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("J".$row1."", 'Sub question '.(++$sqnum).'');
                $row1++;
                
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("H".$row1."", ''.$arr['Sub Question 1']['text'].'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("I".$row1."", ''.$arr['Sub Question 2']['text'].'');
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("J".$row1."", ''.$arr['Sub Question 3']['text'].'');
               
                $row1++;
                
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("H".$row1."", round($arr['Sub Question 1']['score5']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("I".$row1."", round($arr['Sub Question 2']['score5']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("J".$row1."", round($arr['Sub Question 3']['score5']*100/$total,1));
                
                $row1++;
                
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("H".$row1."", round($arr['Sub Question 1']['score4']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("I".$row1."", round($arr['Sub Question 2']['score4']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("J".$row1."", round($arr['Sub Question 3']['score4']*100/$total,1));
                
                $row1++;
             
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("H".$row1."", round($arr['Sub Question 1']['score3']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("I".$row1."", round($arr['Sub Question 2']['score3']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("J".$row1."", round($arr['Sub Question 3']['score3']*100/$total,1));
                
                $row1++;
               
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("H".$row1."", round($arr['Sub Question 1']['score2']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("I".$row1."", round($arr['Sub Question 2']['score2']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("J".$row1."", round($arr['Sub Question 3']['score2']*100/$total,1));
                
                $row1++;
                
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("H".$row1."", round($arr['Sub Question 1']['score1']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("I".$row1."", round($arr['Sub Question 2']['score1']*100/$total,1));
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("J".$row1."", round($arr['Sub Question 3']['score1']*100/$total,1));
                }
                $i++;
                       /* $html .= '<table cellspacing="0" cellpadding="4" border="0" nobr="true">
					
						<tr><td colspan="4" align="center">Table '.++$this->tableNum.'</td></tr>';
                        
						//$html .= '<tr style="border:solid 1px #000000;"><td rowspan="2" width="16%" style="border:solid 1px #000000;">Overall</td><td colspan="3" width="84%" style="border:solid 1px #000000;">'.$key.': '.$arr['text'].'</td></tr>';
                        
						$html .= '<tr style="text-align:center;" style="border:solid 1px #000000;"><td  width="16%" style="border:solid 1px #000000;">Overall</td><td style="border:solid 1px #000000;text-align:left;" width="28%" ><b>Sub Question '.(++$sqnum).':</b> <span style="font-size:12px;">'.$arr['Sub Question 1']['text'].'</span></td><td style="border:solid 1px #000000;text-align:left;" width="28%" ><b>Sub Question '.(++$sqnum).':</b> <span style="font-size:12px;">'.$arr['Sub Question 2']['text'].'</span></td><td style="border:solid 1px #000000;text-align:left;" width="28%" ><b>Sub Question '.(++$sqnum).':</b> <span style="font-size:12px;">'.$arr['Sub Question 3']['text'].'</span></td></tr>										
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-5" width="16%">Exceptional</td><td width="28%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score5']*100/$total,1).'%</td><td width="28%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score5']*100/$total,1).'%</td><td width="28%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score5']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-4">Proficient</td><td  style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score4']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score4']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score4']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-3">Developing</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score3']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score3']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score3']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-2">Emerging</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score2']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score2']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score2']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-1">Foundation</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score1']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score1']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score1']*100/$total,1).'%</td></tr>
					';
			echo $html .='</table>';
                       	*/
			
		}
                                $objPHPExcel->getActiveSheet()->getStyle('A11:J'.($row1).'')->applyFromArray($BStyle);

                $row=$row2;
                $row++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Performance Review: Judgement Distance");
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':J'.$row.'');
                $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':A'.$row.'')->getFont()->setBold(true);
                $row++;
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", "Participants");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row."", "Agreements");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row."", "Disagreements By 1");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row."", "Disagreements By 2");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E".$row."", "Disagreements By 3");
                
                                $row++;

                
                                $url_count = 0;
				$url = array("?");				
				$rankArray = array();
				$urlArray = array();
				$i=0;$k=0;
				$jd0 = 0;$jd1 = 0;$jd2 = 0;$jd3 = 0;
				foreach($this->assessments as $a){
                                        /*echo"<pre>";
                                        print_r($a);
                                        echo"</pre>";*/
					$i++;
					if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1 && isset($this->kpas[$a['assessment_id']])){						
						$jd0 = 0;$jd1 = 0;$jd2 = 0;$jd3 = 0;
						foreach($this->kpas[$a['assessment_id']] as $kpa){
							if(isset($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']])){
								foreach($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']] as $kq){
									if(isset($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']])){
										foreach($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']] as $cq){
											foreach($this->judgementStatement[$a['assessment_id']][$cq['core_question_instance_id']] as $statment){
												$jd = abs($statment['internalRating']['score'] - $statment['externalRating']['score']);
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
											}
										}
									}
				
								}
							}
						}
						$weightage=0;						
						$weightage = $jd0*4 +$jd1*3 +$jd2*2 +$jd3*1;
						$rankArray[$a['data_by_role']['3']['user_name']] = $weightage;
						//$url[$url_count] .= $i.'='.urlencode($a['data_by_role']['3']['user_name']).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
						//$urlArray[$a['data_by_role']['3']['user_name']] = $i.'='.urlencode($a['data_by_role']['3']['user_name']).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
					
                                                $urlArray[$a['data_by_role']['3']['user_name']]['name']=$a['data_by_role']['3']['user_name'];
                                                $urlArray[$a['data_by_role']['3']['user_name']]['jd0']=$jd0;
                                                $urlArray[$a['data_by_role']['3']['user_name']]['jd1']=$jd1;
                                                $urlArray[$a['data_by_role']['3']['user_name']]['jd2']=$jd2;
                                                $urlArray[$a['data_by_role']['3']['user_name']]['jd3']=$jd3;
                                                                                                }
				}
				
				arsort($rankArray,SORT_NUMERIC);
                                //print_r($rankArray);
                                foreach($rankArray as $key=>$val){					
					$k++;
					$url_count++;
					$name=$urlArray[$key]['name'];
                                        $jd0=$urlArray[$key]['jd0']/27*100;
                                        $jd1=$urlArray[$key]['jd1']/27*100;
                                        $jd2=$urlArray[$key]['jd2']/27*100;
                                        $jd3=$urlArray[$key]['jd3']/27*100;
                                        
                                        $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A".$row."", $name);
                                        $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B".$row."", number_format($jd0,2,".",""));
                                        $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C".$row."", number_format($jd1,2,".",""));
                                        $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D".$row."", number_format($jd2,2,".",""));
                                        $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E".$row."", number_format($jd3,2,".",""));
                                        $row++;
                                        
				}
                                        $objPHPExcel->getActiveSheet()->getStyle('A'.($row2+2).':E'.($row-1).'')->applyFromArray($BStyle);

               $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(15);
               $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(15);
               $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
               $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(15);
               $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(15);
               $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(15);
               $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(15);
               $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(15);
               $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(15);
               $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(15);
               
               
               
                $newSheet = $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex(1);
                $newSheet->setTitle("External Assessor Evidence");
                $assessments_ids=$this->getAllAssessment($this->groupAssessmentId);
                
                if($this->student_round==2){
                $assessments_ids_r1=$this->get_section_Array($this->getAllAssessment($this->groupAssessmentId_r1),'teacher_id');
                 //echo"<pre>";
                //print_r($assessments_ids_r1);
                //echo "<pre>";
                }
                
                //print_r($assessments_ids);
                $row_2_1=2;
                $objPHPExcel->setActiveSheetIndex(1)->SetCellValue("A1","External Assessor Evidence - ".$this->aqsData['school_name']."");
                $objPHPExcel->getActiveSheet(1)->mergeCells('A1:J1');
                $objPHPExcel->getActiveSheet()->getStyle("A1:A1")->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A1:A1")->getFont()->setSize(15);
                $data_batch=array();
                foreach($assessments_ids as $key=>$val){
                    $assessment_id=$val['assessment_id'];
                    $data_evi=$this->loadJudgementEvidence($assessment_id);
                    if(count($data_evi)>0){
                    $objPHPExcel->setActiveSheetIndex(1)->SetCellValue("A".$row_2_1."", $val['name']);
                    $objPHPExcel->getActiveSheet(1)->mergeCells('A'.$row_2_1.':J'.$row_2_1.'');
                    $row_2_1++;
                    }
                    foreach($data_evi as $key1=>$val1){
                    $objPHPExcel->setActiveSheetIndex(1)->SetCellValue("A".$row_2_1."", $val1['srno']);
                    //$row_2_1++;
                    //$objPHPExcel->setActiveSheetIndex(1)->SetCellValue("B".$row_2_1."", $val1['to_show']);
                    //$row_2_1++;
                    $objPHPExcel->setActiveSheetIndex(1)->SetCellValue("B".$row_2_1."", $val1['evidence_text']);
                    $objPHPExcel->getActiveSheet(1)->mergeCells("B".$row_2_1.":J".$row_2_1."");
                    $objPHPExcel->getActiveSheet(1)->getStyle('A'.($row_2_1).':J'.($row_2_1).'')->applyFromArray($BStyle);
                    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_2_1.':J'.$row_2_1.'')->getAlignment()->setWrapText(true);
                    $row_2_1++;
                    }
                    //print_r($data_evi);
                    if(count($data_evi)>0){
                          $row_2_1++;
                          $row_2_1++;
                    }
                }
                
               $newSheet2 = $objPHPExcel->createSheet();
               $objPHPExcel->setActiveSheetIndex(2);
               $newSheet2->setTitle("BaseLine Data");
                $objPHPExcel->setActiveSheetIndex(2)->SetCellValue("A1","Overall Performance Award - ".$this->aqsData['school_name']."");
                $objPHPExcel->getActiveSheet(2)->mergeCells('A1:J1');
                $objPHPExcel->getActiveSheet()->getStyle("A1:A1")->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A1:A1")->getFont()->setSize(15);
                 $objPHPExcel->setActiveSheetIndex(2)->SetCellValue("A2","Student-UID");
                 $objPHPExcel->setActiveSheetIndex(2)->SetCellValue("B2","Name");
                 $objPHPExcel->setActiveSheetIndex(2)->SetCellValue("C2","Award");
                 
                if($this->student_round==2){
                $objPHPExcel->setActiveSheetIndex(2)->SetCellValue("D2","Award (Round-1)");
                $objPHPExcel->setActiveSheetIndex(2)->SetCellValue("E2","Rating Differential"); 
                //$assessments_ids_r1=$this->getAllAssessment($this->groupAssessmentId_r1);
                }
                
                 $objPHPExcel->getActiveSheet(1)->getStyle('A2:C2')->applyFromArray($BStyle);
                 $row_2_1=3;
                 //echo"<pre>";
                 //print_r($assessments_ids);
                 //echo"</pre>";
                 //die();
               foreach($assessments_ids as $key=>$val){
                   //print_r($val);
                   
                   $objPHPExcel->setActiveSheetIndex(2)->SetCellValue("A".$row_2_1."", $val['student_uid']); 
                    $objPHPExcel->setActiveSheetIndex(2)->SetCellValue("B".$row_2_1."", $val['name']);
                    $objPHPExcel->setActiveSheetIndex(2)->SetCellValue("C".$row_2_1."", $val['rating']);
                    
                if($this->student_round==2){
                    
                $round1_details=isset($assessments_ids_r1[$val['teacher_id']])?$assessments_ids_r1[$val['teacher_id']]:array(); 
                
                //print_r($round1_details);
                $objPHPExcel->setActiveSheetIndex(2)->SetCellValue("D".$row_2_1."",isset($round1_details['externalRating']['rating'])?$round1_details['externalRating']['rating']:'');
                $objPHPExcel->setActiveSheetIndex(2)->SetCellValue("E".$row_2_1."",isset($round1_details['externalRating']['score'])?(($val['numericRating']-$round1_details['externalRating']['score'])):''); 
                //$assessments_ids_r1=$this->getAllAssessment($this->groupAssessmentId_r1);
                if(isset($color_rating[$round1_details['externalRating']['score']])){
               $this->cellColor($objPHPExcel,"D".$row_2_1.":D".$row_2_1."",$color_rating[$round1_details['externalRating']['score']]);
                }

                }
                
                
                    
                    $this->cellColor($objPHPExcel,"C".$row_2_1.":C".$row_2_1."",$color_rating[$val['numericRating']]);
                    if($this->student_round==2){
                    $objPHPExcel->getActiveSheet(1)->getStyle('A'.($row_2_1).':E'.($row_2_1).'')->applyFromArray($BStyle);
                        
                    }else{                                                                                    
                    $objPHPExcel->getActiveSheet(1)->getStyle('A'.($row_2_1).':C'.($row_2_1).'')->applyFromArray($BStyle);
                    }
                    
                    
                 $row_2_1++;  
               }
               //die();
              $objPHPExcel->getActiveSheet(2)->getColumnDimension("A")->setWidth(15);
              $objPHPExcel->getActiveSheet(2)->getColumnDimension("B")->setWidth(15);
              $objPHPExcel->getActiveSheet(2)->getColumnDimension("C")->setWidth(15);
              if($this->student_round==2){
              $objPHPExcel->getActiveSheet(2)->getColumnDimension("D")->setWidth(15);
              $objPHPExcel->getActiveSheet(2)->getColumnDimension("E")->setWidth(15);
              }
              $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
              //$objWriter->save('tmp/temp_upload/'.$school.'_round'.$round.'.xlsx');
              $objWriter->save('php://output');
        }
        
        protected function generateOverviewStudentOutput(){
                //error_reporting(E_ALL);
                $this->loadAqsDataStudent();
                //print_r($this->aqsData);
		$this->loadJudgementalStatements();
		$this->loadCoreQuestions();
		$this->loadKeyQuestions();
		$this->loadKpas();
                
		$assessmentModel=new assessmentModel();
		$assessments=$assessmentModel->getAssessmentsInGroupAssessmentDiagnostic($this->groupAssessmentId,$this->diagnosticId);
		$this->assessments = $assessments;
                //echo"<pre>";
                //print_r($this->assessments);
                //echo"</pre>";
                
                $ids_allow=array();
                
                foreach($this->assessments as $key=>$val){
                    
                    $student_id=$val['data_by_role'][3]['user_id'];
                    $ids_allow[]=$student_id;
                    
                }
                //print_r($ids_allow);
                $student_round=isset($assessments[0]['student_round'])?$assessments[0]['student_round']:1;
                $this->student_round=$student_round;
                $numTeachers_r1=0;
                if($this->student_round==2){
                $batch=isset($assessments[0]['client_id'])?$assessments[0]['client_id']:0;
                $groupdata=$assessmentModel->getGAIdfromClientandRound($batch,1);
                $gaid=$groupdata['group_assessment_id'];
                
                $assessments_r1=$assessmentModel->getAssessmentsInGroupAssessmentDiagnostic_rounds($gaid,$this->groupAssessmentId,$this->diagnosticId,$ids_allow);
                //$assessments_r1=$this->db->array_grouping($assessments_r1,"user_ids");
                
		$this->assessments_r1 = $assessments_r1;
                $this->groupAssessmentId_r1=$gaid;
                $completeReviewUsers = $assessmentModel->getAssessmentCompletedCountInGroupAssessmentDiagnostic($gaid,$this->diagnosticId);
		$numTeachers_r1=count($completeReviewUsers);
                //echo"<pre>";
                //print_r($completeReviewUsers);
                //echo"</pre>";
                
                $this->loadJudgementalStatements(2,DEFAULT_LANGUAGE,$ids_allow);
                //echo"<pre>";
                //print_r($this->judgementStatement_r1);
                //echo"</pre>";
                
                $this->loadCoreQuestions(2,DEFAULT_LANGUAGE,$ids_allow);
		$this->loadKeyQuestions(2,DEFAULT_LANGUAGE,$ids_allow);
		$this->loadKpas(2,DEFAULT_LANGUAGE,$ids_allow);
                /*echo"<pre>";
                print_r($this->kpas);
                echo"</pre>";*/
                }
                
		//$numTeachersCompletedAsst = $assessmentModel->getAssessmentCountInGroupAssessmentDiagnostic($this->groupAssessmentId,$this->diagnosticId);
		$pdf = new reporttcpdf ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		$pdf->footer_text = $this->config['coverAddress'];
		$pdf->other_footer_text = 'Career Readiness Review: Overview Report for '.$this->aqsData['school_name'].' '.$this->aqsData['city_name'].' '.$this->aqsData['state_name'].' '.$this->aqsData['country_name'].', '.date("d-m-Y").' (generated on)';// 'AQS Teacher Performance - Overview Report for '.$this->aqsData['school_name'].', '.date("d-m-Y").' (generated on)';
		$pdf->footerBG = $this->config['footerBG'];
		$pdf->footerColor = $this->config['footerColor'];
		$pdf->footerHeight = $this->config['footerHeight'];
		$pdf->pageNoBarHeight = $this->config['pageNoBarHeight'];
                $pdf->coverAddressAntarang=$this->config['coverAddressAntarang'];
                $pdf->coverAddressAdhyayanFoundation=$this->config['coverAddressAdhyayanFoundation'];
		$pdf->assessemnt_type = 4;
                $pdf->SetTitle('Career Readiness Review Overview Report '.'-'.$this->aqsData['school_name']);
		$pdf->SetHeaderData ( '', '', 'Career Readiness Review Overview Report: '.$this->aqsData['school_name'].', '.$this->aqsData['province_name'].', '.$this->aqsData['network_name'].', '.$this->aqsData['city_name'].'' );
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
                $pdf->setListIndentWidth(4);
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont ( PDF_FONT_MONOSPACED );
		// set margins
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
		$numTeachers = (count($this->assessments)>$numTeachers_r1)?count($this->assessments):$numTeachers_r1;
		$incompleteReviewUsers = $assessmentModel->getAssessmentCountInGroupAssessmentDiagnostic($this->groupAssessmentId,$this->diagnosticId);
		$teachers ='';
                
		foreach($incompleteReviewUsers as $key=>$val)
			$teachers .= $val['name'].', ';
		$teachers!=''?($teachers='<span style="font-size:8;"><br/>Note - Review is not completed for the following participants:<br/>'.rtrim($teachers,', ').'</span>'):'';
		//$pdf->SetTextColor ( 255, 255, 255 );
		//$pdf->SetFillColor ( 108, 13, 16 );
		$firstpagehtml = '';
		/*if($this->aqsData['client_id']==11)
			$firstpagehtml = '<table class="pdfHdr broad"> <tbody><tr><td class="halfSec fl" align="left"><a href=""><img src="' . SITEURL . 'public/images/logo.png"alt="" style="height:55px;"></a></td><td class="halfSec fr" align="right" ><a href=""><img src="'.SITEURL.'/public/images/shishuvan.jpg" alt=""></a></td></tr>
		<tr><td colspan="2" style="font-weight:bold;text-align:center;font-size:13px;">The Teacher Performance Review has been initiated and validated by '.($this->aqsData['school_name']).'</td></tr>
		</table>';
		else*/
                $firstpagehtml = '<br/><table class="pdfHdr broad"><tr><td class="hSec fl" align="left"><a href=""><img src="'.$this->config['headerStudentImgAdh'].'" alt=""></a></td><td class="hSec fr" align="right"><a href=""><img src="'.$this->config['isStudentReviewImg'].'" alt="" height="90px;"></a></td></tr>  
</table><br/><br/>';
		 //$firstpagehtml = '<br/><div style="text-align:center;"><img src="' . SITEURL . 'public/images/logo.png"></div><br/><br/>';
		/*$firstpagehtml .= <<<EOD
		<div style="background-color:#00b050;color:#FFFFFF;text-align:center;border-style: solid;border:10px solid #ffc000;"><br/><br/><br/>
		<h2 style="text-align:center">CAREER READINESS REVIEW FOR INDIVIDUALS: OVERVIEW REPORT <br/></h2><div></div>
		Batch level analysis of participants’ performance against the Career Readiness Standard<br/><br/><br/><br/><br/>
		<b>$numTeachers</b> participant(s) reviewed between <b>{$this->conductedDate}</b> to <b>{$this->conductedDate}</b><br/><br/><br/><br/><br/>	
		</div>
		$teachers
EOD;*/
                $firstpagehtml .= <<<EOD
		<div style="background-color:#00b050;color:#FFFFFF;text-align:center;border-style: solid;border:10px solid #ffc000;"><br/><br/><br/>
		<h2 style="text-align:center">CAREER READINESS REVIEW FOR INDIVIDUALS: OVERVIEW REPORT <br/></h2><div></div>
		<h3>Batch level analysis of participants’<br> performance against the Career Readiness<br> Standard</h3><div></div><div></div><br/><br/><br/><br/>
		<b>$numTeachers</b> participant(s) reviewed on <b>{$this->conductedDate}</b><br/><br/><br/>	
		</div>
		$teachers
EOD;
//echo $firstpagehtml;die;
				$pdf->writeHTMLCell ( 0, 0, '', '', $firstpagehtml, 0, 1, 0, true, 'J', true );
				// ---------------------------------------------------------
				// create some content ...
				// add a page
				// first page start
				//award
				$sectionNum = 1;
				$pdf->addPage ();
				$pdf->Bookmark ( $sectionNum.'. Career Readiness Review: Process', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
                                
				$pdf->SetFont ( 'times', 'B', 16 );
                                $pdf->setCellHeightRatio(1.50);
                                $pdf->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 192, 0)));
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 0, 176 , 80 );
                                //$rgb = $this->hex2rgb("#ffc000");
                                //print_r($rgb);
                                

				$pdf->Cell ( 0, 8, ($sectionNum++).'. Career Readiness Review: Process ', 1, 1, 'C', true );
				$pdf->setCellHeightRatio(1.25);
                                $pdf->SetFont ( 'times', '', 12 );
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->ln(3);
                                $html='<div>Antarang Foundation and Adhyayan Foundation undertook the Career Readiness Review for '.$numTeachers.' participants in '.$this->aqsData['school_name'].', '.$this->aqsData['province_name'].', '.$this->aqsData['city_name'].' under '.$this->aqsData['network_name'].'. The process is detailed below.</div>';
				//$html = 'Adhyayan undertook the review of '.$numTeachers.' teachers of '.$this->aqsData['school_name'].'.  Adhyayan worked alongside the teachers to facilitate their understanding of \'What Good Teaching & Learning Look Like\' across the world through the Adhyayan review process explained below.';				
				//$pdf->Write ( 0, $html, '', 0, '', true, 0, false, false, 0 );
                                $pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				$pdf->ln(1);
                                
				$html = $this->getPDFStudentreviewProcess();				
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				$pdf->AddPage();
				$pdf->Bookmark ( $sectionNum.'.  Career Readiness Review: Grades', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->SetTextColor ( 255, 255, 255 );
				//$pdf->SetFillColor ( 108, 13, 16 );
                                $pdf->SetFillColor ( 0, 176 , 80 );
                                $pdf->setCellHeightRatio(1.50);
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Career Readiness Review: Grades ', 1, 1, 'C', true );
				$pdf->Ln(2);
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->SetTextColor ( 0, 0, 0 );
                                $pdf->setCellHeightRatio(1.25);
				$html = $this->getStudentOverviewAwardDefinition();
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				/* $pdf->AddPage();
				$pdf->Bookmark ( '2. Key for reading the report', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 108, 13, 16 );
				$pdf->Ln(3);
				$pdf->Cell ( 0, 8, 'Key for reading the report', 0, 1, 'C', true );
				$html = '<div style="page-break-inside:avoid;">'.$this->getPDFKeyForReadingReport().'</div>';
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->Ln(3);
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true ); */
                                
                                //key questions
                                
                                $pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				//$pdf->SetFillColor ( 108, 13, 16 );
                                $pdf->SetFillColor ( 0, 176 , 80 );
                                $pdf->setCellHeightRatio(1.50);
				$pdf->Bookmark ( $sectionNum.'. Performance Review: Key questions', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Performance Review: Key questions', 1, 1, 'C', true );
                                $pdf->setCellHeightRatio(1.25);
                                if($this->student_round==2){
                                $html_tchrperf = $this->generateStu_PerfReview();
                                $html = $this->getStudentKQRatings_r1();
                                }else{
                                $html_tchrperf = $this->generateStu_PerfReview_r1();
                                $html = $this->getStudentKQRatings();
                                }
				
                                //$this->tableNum++;
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->ln(3);
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				
                                
				//awards				
				//sub-question wise performance
				$pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				//$pdf->SetFillColor ( 108, 13, 16 );
                                $pdf->SetFillColor ( 0, 176 , 80 );
                                $pdf->setCellHeightRatio(1.50);
				$pdf->Bookmark ( $sectionNum.'. Performance Review: Sub questions', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Performance Review: Sub questions', 1, 1, 'C', true );
                                $pdf->setCellHeightRatio(1.25);
				if($this->student_round==2){
				$html = $this->getStudentSQRatings_r1();
                                }else{
                                $html = $this->getStudentSQRatings();    
                                }
				//echo $html;
                                
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->ln(3);
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				//teacher wise performance
	       		        $pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				//$pdf->SetFillColor ( 108, 13, 16 );
                                 $pdf->SetFillColor ( 0, 176 , 80 );
                                $pdf->setCellHeightRatio(1.50);
				$pdf->Bookmark ( $sectionNum.'. Performance Review: Participants', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Performance Review: Participants ', 1, 1, 'C', true );
                                $pdf->setCellHeightRatio(1.25);
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;"><br/>'.$html_tchrperf.'</div>', 0, 1, 0, true, 'J', true );
				//judgement distance
				$pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				//$pdf->SetFillColor ( 108, 13, 16 );
                                $this->tableNum++;
                                $pdf->SetFillColor ( 0, 176 , 80 );
                                $pdf->setCellHeightRatio(1.50);
				$pdf->Bookmark ( $sectionNum.'. Performance Review: Judgement Distance', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Performance Review: Judgement Distance', 1, 1, 'C', true );
                                $pdf->setCellHeightRatio(1.50);
				$pdf->Ln(2);
				$html = 'The judgement distance refers to the difference between the self-review and external validation ratings. The following table depicts the judgement distance between the two sets of ratings..<br/>
						<br/>The expectation is that the JD=0 or 1 would increase significantly as participants begin to understand what good practice for each statement looks like and learn how to rate themselves accurately against the career readiness standard, that is benchmarked against international research and best practice. In this manner, the participant would develop the level of objectivity and rigour demonstrated by external reviewers.';
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				
				//create jd graph
				//$url ='?';
				$url_count = 0;
				$url = array("?");				
				$rankArray = array();
				$urlArray = array();
                                $rankArray_r1 = array();
				$urlArray_r1 = array();
				$i=0;$k=0;
				$jd0 = 0;$jd1 = 0;$jd2 = 0;$jd3 = 0;
                                //echo"vikas1";
                                //die();
                                if($this->student_round==2){
				foreach($this->assessments_r1 as $a){
                                        /*echo"<pre>";
                                        print_r($a);
                                        echo"</pre>";*/
                                        if($a['student_round']==1){
                                         $this->kpas_f=$this->kpas_r1;
                                         $this->keyQuestions_f=$this->keyQuestions_r1;
                                         $this->coreQuestions_f=$this->coreQuestions_r1;
                                         $this->judgementStatement_f=$this->judgementStatement_r1;
                                         $line="Round-1";
                                        }else{
                                         $this->kpas_f=$this->kpas;
                                         $this->keyQuestions_f=$this->keyQuestions;
                                         $this->coreQuestions_f=$this->coreQuestions;
                                         $this->judgementStatement_f=$this->judgementStatement;
                                         $line="Round-2";$i++;
                                        }
                                        
					$i++;
					if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1 && isset($this->kpas_f[$a['assessment_id']])){						
						$jd0 = 0;$jd1 = 0;$jd2 = 0;$jd3 = 0;
						foreach($this->kpas_f[$a['assessment_id']] as $kpa){
							if(isset($this->keyQuestions_f[$a['assessment_id']][$kpa['kpa_instance_id']])){
								foreach($this->keyQuestions_f[$a['assessment_id']][$kpa['kpa_instance_id']] as $kq){
									if(isset($this->coreQuestions_f[$a['assessment_id']][$kq['key_question_instance_id']])){
										foreach($this->coreQuestions_f[$a['assessment_id']][$kq['key_question_instance_id']] as $cq){
											foreach($this->judgementStatement_f[$a['assessment_id']][$cq['core_question_instance_id']] as $statment){
												$jd = abs($statment['internalRating']['score'] - $statment['externalRating']['score']);
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
											}
										}
									}
				
								}
							}
						}
						$weightage=0;						
						$weightage = $jd0*4 +$jd1*3 +$jd2*2 +$jd3*1;
                                                if($a['student_round']==1){
						$rankArray_r1[$a['data_by_role']['3']['user_name']] = $weightage;
						//$url[$url_count] .= $i.'='.urlencode($a['data_by_role']['3']['user_name']).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
						$urlArray_r1[$a['data_by_role']['3']['user_name']] = $i.'='.urlencode($a['data_by_role']['3']['user_name'].' '.$line).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
                                                }else{
                                                $rankArray[$a['data_by_role']['3']['user_name']] = $weightage;
						//$url[$url_count] .= $i.'='.urlencode($a['data_by_role']['3']['user_name']).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
						$urlArray[$a['data_by_role']['3']['user_name']] = $i.'='.urlencode($a['data_by_role']['3']['user_name'].' '.$line).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
                                                 
                                                }
					}
				}
				
				arsort($rankArray,SORT_NUMERIC);				
				//url			
				$isGraphPage1 = 1;
				foreach($rankArray as $key=>$val){					
					$k++;
                                        $k++;
					if(($k%12==0 && $isGraphPage1==1)|| (($k+8)%21==0 && $isGraphPage1==0)){//after 12 students create a new image
						$isGraphPage1 =0;
						$url_count++;
						$url[$url_count]="?";
					}
                                        $url[$url_count] .= $urlArray_r1[$key];
					$url[$url_count] .= $urlArray[$key];
                                        
				}
                                
                                }else{
                                    
                                    foreach($this->assessments as $a){
                                        
                                        
					$i++;
					if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1 && isset($this->kpas[$a['assessment_id']])){						
						$jd0 = 0;$jd1 = 0;$jd2 = 0;$jd3 = 0;
						foreach($this->kpas[$a['assessment_id']] as $kpa){
							if(isset($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']])){
								foreach($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']] as $kq){
									if(isset($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']])){
										foreach($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']] as $cq){
											foreach($this->judgementStatement[$a['assessment_id']][$cq['core_question_instance_id']] as $statment){
												$jd = abs($statment['internalRating']['score'] - $statment['externalRating']['score']);
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
											}
										}
									}
				
								}
							}
						}
						$weightage=0;						
						$weightage = $jd0*4 +$jd1*3 +$jd2*2 +$jd3*1;
                                                
                                                $rankArray[$a['data_by_role']['3']['user_name']] = $weightage;
						//$url[$url_count] .= $i.'='.urlencode($a['data_by_role']['3']['user_name']).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
						$urlArray[$a['data_by_role']['3']['user_name']] = $i.'='.urlencode($a['data_by_role']['3']['user_name']).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
                                                 
                                                
					}
				}
				
				arsort($rankArray,SORT_NUMERIC);				
				//url			
				$isGraphPage1 = 1;
				foreach($rankArray as $key=>$val){					
					$k++;
                                        
					if(($k%13==0 && $isGraphPage1==1)|| (($k+8)%21==0 && $isGraphPage1==0)){//after 13 students create a new image
						$isGraphPage1 =0;
						$url_count++;
						$url[$url_count]="?";
					}
                                      
					$url[$url_count] .= $urlArray[$key];
                                        
				}
                                    
                                }
                                
				$this->rankArray = $rankArray;
				//print_r($rankArray);die;
				//$uri = $this->createURISqJdTeachers_AccuracySelfReview();
				$graph='';
				$this->tableNum++;
				for($i=0;$i<=$url_count;$i++){
					$graph .= '<div style="page-break-inside:avoid;"><div style="text-align:center;font-weight:bold;"><span style="font-weight:normal;">Table '.$this->tableNum.'</span><br/>Judgement distance between participant and external assessor from highest to lowest</div><img style="border:solid 1px #000000;" src="' . SITEURL . 'library/stacked.chart_jd_gen.php' . $url[$i] . '" /></div>';													
				}
				//echo $graph;die;
				$pdf->writeHTMLCell ( 0, 0, '', '', $html.$graph, 0, 1, 0, true, 'J', true );
				//Accuracy of self-review (ranked from highest to lowest)
				/* $pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 108, 13, 16 );
				$pdf->Bookmark ( '5. Accuracy of self-review (ranked from highest to lowest)', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, 'Accuracy of self-review (ranked from highest to lowest)', 0, 1, 'C', true );
				$pdf->Ln(2);
				$html = $this->getTchrRank();
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;"><br/>'.$html.'</div>', 0, 1, 0, true, 'J', true ); */
				//limitations of first use of diagnostic
				$pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				//$pdf->SetFillColor ( 108, 13, 16 );
                                $pdf->SetFillColor ( 0, 176 , 80 );
                                $pdf->setCellHeightRatio(1.50);
				$pdf->Bookmark ( $sectionNum.'. Limitations when using the diagnostic for the first time ', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Limitations when using the diagnostic for the first time', 1, 1, 'C', true );
				$pdf->Ln(2);
				$pdf->setCellHeightRatio(1.25);
                                $html = $this->getLimitationsStuFirstUse_Diagnostic();
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;"><br/>'.$html.'</div>', 0, 1, 0, true, 'J', true );
				//$pdf->AddPage();
				$this->getPDFIndex($pdf);
                                
				$pdf->Output ('CRR Batch level report Round '.$this->student_round.'-'.$this->aqsData['school_name'].'.pdf', 'I' );
	}
        
        
       function loadRatingsCentre($round=0,$lang_id=DEFAULT_LANGUAGE){
        if(empty($this->rankArrayCentre)){    
        $sql="select e.rating_id,e.rating from (select * from d_diagnostic where diagnostic_id=?) a
left join h_diagnostic_rating_level_scheme b on a.diagnostic_id=b.diagnostic_id
left join (select * from h_rating_level_scheme where rating_level_id=3) d on d.rating_scheme_id=b.rating_level_scheme_id
left join (select dr.*,hlt.translation_text  as rating from d_rating dr inner join h_lang_translation hlt on dr.equivalence_id=hlt.equivalence_id where hlt.language_id=?) e on d.rating_id=e.rating_id where e.rating_id!=20 order by e.rating_id desc";
        return $this->rankArrayCentre=$this->db->get_results($sql,array($this->diagnosticId,$lang_id));
        }
        
        }
        
        function loadKeyQuestionsCentre($round=1,$round_id=1,$batch_ids='',$lang_id=DEFAULT_LANGUAGE){
            
            $sql_first="select a.province_id,a.province_name,c.client_name,c.client_id as batch_id,gi.user_id as student_id,f.diagnostic_id,j.key_question_instance_id,assessor_id,j.key_question_id,kq_order,key_question_text,kh.key_heading,i.rating_id,i.rating,count(i.rating) as rating_tot,max(g.ratingInputDate) as maxdate, min(g.ratingInputDate) as mindate from ";
            
            if(gettype($this->centre_id)=="array" && count($this->centre_id)>0){
             $sql_first.="(select * from d_province where province_id IN (".(implode(",",$this->centre_id)).")) a ";   
            }else{        
            $sql_first.="(select * from d_province where province_id=?) a ";
            }
                    
$sql_first.=" inner join h_client_province b on  a.province_id=b.province_id
inner join d_client c on b.client_id=c.client_id
inner join (select * from d_group_assessment where assessment_type_id=4 && student_round=?) d on c.client_id=d.client_id
inner join h_assessment_ass_group e on e.group_assessment_id=d.group_assessment_id
inner join d_assessment f on e.assessment_id=f.assessment_id
inner join (select * from `h_assessment_user` where role=4 && isFilled=1) g on g.assessment_id=f.assessment_id
inner join (select * from `h_assessment_user` where role=3) gi on gi.assessment_id=f.assessment_id
inner join h_kq_instance_score h on g.assessment_id=h.assessment_id && h.assessor_id=g.user_id
inner join h_kpa_kq j on h.key_question_instance_id = j.key_question_instance_id
inner join (select dkq.*,hlt.translation_text  as key_question_text from d_key_question dkq inner join h_lang_translation hlt on dkq.equivalence_id=hlt.equivalence_id where hlt.language_id=?) k on k.key_question_id = j.key_question_id 
inner join d_key_question_heading kh on k.key_question_id = kh.key_question_id 
inner join (select dr.*,hlt.translation_text  as rating from d_rating dr inner join h_lang_translation hlt on dr.equivalence_id=hlt.equivalence_id where hlt.language_id=?) i on h.d_rating_rating_id=i.rating_id 
where f.diagnostic_id=? ";
            
            if(!empty($batch_ids)){
            $sql_first.=" && c.client_id IN (".$batch_ids.")";
            }
            
   $sql_last=" group by b.client_id,gi.user_id,j.key_question_instance_id,rating order by c.client_name,b.client_id,j.`kq_order` asc";      
         
         if($round==2){
         $sql="".$sql_first." ".$sql_last."";
         
         if(empty($this->SQRatingsArrCentre)){
                if(gettype($this->centre_id)=="array" && count($this->centre_id)>0){
                $this->SQRatingsArrCentre=$this->db->get_results($sql,array($round_id,$lang_id,$lang_id,$this->diagnosticId));    
                }else{                                                                                       
                $this->SQRatingsArrCentre=$this->db->get_results($sql,array($this->centre_id,$round_id,$lang_id,$lang_id,$this->diagnosticId));
                }
        
         }    
         
         //$arr=$this->db->array_grouping($this->SQRatingsArrCentre,"batch_id");
         $arr=$this->db->array_grouping($this->SQRatingsArrCentre,"student_id");
         $arru=array_unique(array_keys($arr));
         $count_ids=count($arru);
         $batches_ids=implode(",",$arru);
        
         $round2_text="";
         if($count_ids>0){
         //$round2_text=" && c.client_id IN (?)";
          $round2_text=" && gi.user_id IN (".$batches_ids.")";   
         }
         
         $sql="".$sql_first." ".$round2_text." ".$sql_last."";
         $sql_1="".$sql_first." ".$sql_last."";
         
         if(empty($this->SQRatingsArrCentre_r1)){
                if(gettype($this->centre_id)=="array" && count($this->centre_id)>0){
                $this->SQRatingsArrCentre_r1=$this->db->get_results($sql,array($round_id,$lang_id,$lang_id,$this->diagnosticId));
                $SQRatingsArrCentre_tot=$this->db->get_results($sql_1,array($round_id,$lang_id,$lang_id,$this->diagnosticId));
                }else{                                                                                       
                $this->SQRatingsArrCentre_r1=$this->db->get_results($sql,array($this->centre_id,$round_id,$lang_id,$lang_id,$this->diagnosticId));
                $SQRatingsArrCentre_tot=$this->db->get_results($sql_1,array($this->centre_id,$round_id,$lang_id,$lang_id,$this->diagnosticId));
                }
                $arr_r1=$this->db->array_grouping($SQRatingsArrCentre_tot,"student_id");
                $arru_r1=array_unique(array_keys($arr_r1));
                $count_ids_r1=count($arru_r1);
                $this->tot_show_stu=($count_ids_r1>$count_ids)?$count_ids_r1:$count_ids;
                
         }
         
         }else{
          $sql="".$sql_first." ".$sql_last."";   
          if(empty($this->SQRatingsArrCentre)){
                if(gettype($this->centre_id)=="array" && count($this->centre_id)>0){                                                                                        
                $this->SQRatingsArrCentre=$this->db->get_results($sql,array($round_id,$lang_id,$lang_id,$this->diagnosticId));
                }else{
                $this->SQRatingsArrCentre=$this->db->get_results($sql,array($this->centre_id,$round_id,$lang_id,$lang_id,$this->diagnosticId));

                }
                $arr=$this->db->array_grouping($this->SQRatingsArrCentre,"student_id");
                $arru=array_unique(array_keys($arr));
                $count_ids=count($arru);
                $count_ids_r1=0;
                $this->tot_show_stu=($count_ids_r1>$count_ids)?$count_ids_r1:$count_ids;                                                                                        
         }
         
         }
         
        }
        
        function loadKeyQuestionsOrg($round=1,$round_id=1,$center_ids='',$batch_ids='',$lang_id=DEFAULT_LANGUAGE){
           
           $sql_first="select a1.network_id as province_id,a1.network_name as province_name,a.province_id as client_id,a.province_name as client_name,c.client_id as batch_id,gi.user_id as student_id,f.diagnostic_id,j.key_question_instance_id,assessor_id,j.key_question_id,kq_order,key_question_text,kh.key_heading,i.rating_id,i.rating,count(i.rating) as rating_tot,max(g.ratingInputDate) as maxdate, min(g.ratingInputDate) as mindate from 
            (select * from d_network where network_id=?) a1
            inner join h_province_network b1 on a1.network_id=b1.network_id
            inner join d_province  a on b1.province_id=a.province_id         
            inner join h_client_province b on  a.province_id=b.province_id
            inner join d_client c on b.client_id=c.client_id
            inner join (select * from d_group_assessment where assessment_type_id=4 && student_round=?) d on c.client_id=d.client_id
            inner join h_assessment_ass_group e on e.group_assessment_id=d.group_assessment_id
            inner join d_assessment f on e.assessment_id=f.assessment_id
            inner join (select * from `h_assessment_user` where role=4 && isFilled=1) g on g.assessment_id=f.assessment_id
            inner join (select * from `h_assessment_user` where role=3) gi on gi.assessment_id=f.assessment_id
            inner join h_kq_instance_score h on g.assessment_id=h.assessment_id && h.assessor_id=g.user_id
            inner join h_kpa_kq j on h.key_question_instance_id = j.key_question_instance_id
            inner join (select dkq.*,hlt.translation_text  as key_question_text from d_key_question dkq inner join h_lang_translation hlt on dkq.equivalence_id=hlt.equivalence_id where hlt.language_id=?) k on k.key_question_id = j.key_question_id 
            inner join d_key_question_heading kh on k.key_question_id = kh.key_question_id 
            inner join (select dr.*,hlt.translation_text  as rating from d_rating dr inner join h_lang_translation hlt on dr.equivalence_id=hlt.equivalence_id where hlt.language_id=?) i on h.d_rating_rating_id=i.rating_id 
            where f.diagnostic_id=? ";
           
            if(!empty($batch_ids)){
            $sql_first.=" && c.client_id IN (".$batch_ids.")";
            }
            
            if(!empty($center_ids)){
            $sql_first.=" && a.province_id IN (".$center_ids.")";
            }
            
            $sql_last="group by b1.province_id,b.client_id,gi.user_id,j.key_question_instance_id,rating order by a.province_name,b.client_id,j.`kq_order` asc";                                                                                            
         
         if($round==2){
         $sql="".$sql_first." ".$sql_last."";
         
         if(empty($this->SQRatingsArrCentre)){
                   $this->SQRatingsArrCentre=$this->db->get_results($sql,array($this->centre_id,$round_id,$lang_id,$lang_id,$this->diagnosticId));
        
         }
         
         //echo"<pre>";                                                                                               
         //print_r($this->SQRatingsArrCentre);
         //echo"</pre>"; 
         $arr=$this->db->array_grouping($this->SQRatingsArrCentre,"student_id");
         //$arr=$this->db->array_grouping($this->SQRatingsArrCentre,"batch_id");
         $arru=array_unique(array_keys($arr));
         $count_ids=count($arru);
         $batches_ids=implode(",",$arru);
        
         $round2_text="";
         if($count_ids>0){
         //$round2_text=" && c.client_id IN (?)";
          $round2_text=" && gi.user_id IN (".$batches_ids.")";  
         }
         
         $sql="".$sql_first." ".$round2_text." ".$sql_last."";
         $sql_1="".$sql_first." ".$sql_last."";
         if(empty($this->SQRatingsArrCentre_r1)){
                
                $this->SQRatingsArrCentre_r1=$this->db->get_results($sql,array($this->centre_id,$round_id,$lang_id,$lang_id,$this->diagnosticId));   
                $SQRatingsArrCentre_tot=$this->db->get_results($sql_1,array($this->centre_id,$round_id,$lang_id,$lang_id,$this->diagnosticId));
                $arr_r1=$this->db->array_grouping($SQRatingsArrCentre_tot,"student_id");
                $arru_r1=array_unique(array_keys($arr_r1));
                $count_ids_r1=count($arru_r1);
                $this->tot_show_stu=($count_ids_r1>$count_ids)?$count_ids_r1:$count_ids;                                                                                       
        
         }
         //echo"<pre>";
         //print_r($this->SQRatingsArrCentre_r1);
         //echo"</pre>";
         
         }else{
             
         $sql="".$sql_first." ".$sql_last."";   
         if(empty($this->SQRatingsArrCentre)){
                $this->SQRatingsArrCentre=$this->db->get_results($sql,array($this->centre_id,$round_id,$lang_id,$lang_id,$this->diagnosticId));
                $arr=$this->db->array_grouping($this->SQRatingsArrCentre,"student_id");
                $arru=array_unique(array_keys($arr));
                $count_ids=count($arru);
                $count_ids_r1=0;
                $this->tot_show_stu=($count_ids_r1>$count_ids)?$count_ids_r1:$count_ids;                                                                                     
         }
         
         }
         
        }
        
        function reFrameKeyQuestions(){
            $arr=$this->db->array_grouping($this->SQRatingsArrCentre,"kq_order");
            $this->rankArrayCentre;
            $array_f=array();
            $batches_f=array();
            $min_date=array();
            $max_date=array();
            //echo '<pre>';
            //print_r($arr);
            //echo '</pre>';
            
            foreach($arr as $key=>$val){
            foreach($val as $key_1=>$val_1){
                /*echo '<pre>';
                print_r($val_1);
                echo '</pre>';
                 
                 */
            $min_date[]= date("Y-m-d",strtotime($val_1['mindate']));
            $max_date[]= date("Y-m-d",strtotime($val_1['maxdate']));
            $array_f['Key Question '.$key.'']['key_question_text']=$val_1['key_question_text'];
            $array_f['Key Question '.$key.'']['key_question_heading']=$val_1['key_heading'];
            foreach($this->rankArrayCentre as $key_2=>$val_2){
                //echo $array_f['Key Question '.$key.'']['grades'][$val_2['rating']];
            //if(!isset($array_f['Key Question '.$key.'']['grades'][$val_2['rating']]) || (isset($array_f['Key Question '.$key.'']['grades'][$val_2['rating']]) && $array_f['Key Question '.$key.'']['grades'][$val_2['rating']]==0)){    
            $array_f['Key Question '.$key.'']['grades'][$val_2['rating']]=($val_1['rating']==$val_2['rating'])?(isset($array_f['Key Question '.$key.'']['grades'][$val_2['rating']])?$array_f['Key Question '.$key.'']['grades'][$val_2['rating']]:0)+$val_1['rating_tot']:(isset($array_f['Key Question '.$key.'']['grades'][$val_2['rating']])?$array_f['Key Question '.$key.'']['grades'][$val_2['rating']]:0);
            //}
            $batches_f[$val_1['client_name']]['Key Question '.$key.''][$val_2['rating']]=($val_1['rating']==$val_2['rating'])?(isset($batches_f[$val_1['client_name']]['Key Question '.$key.''][$val_2['rating']])?$batches_f[$val_1['client_name']]['Key Question '.$key.''][$val_2['rating']]:0)+$val_1['rating_tot']:(isset($batches_f[$val_1['client_name']]['Key Question '.$key.''][$val_2['rating']])?$batches_f[$val_1['client_name']]['Key Question '.$key.''][$val_2['rating']]:0);
            }
            }
            
            }
            $this->SQRatingsArrCentre_final=$array_f;
            $this->SQRatingsArrCentre_batchesfinal=$batches_f;
            $max_date_f=max($max_date);
            $min_date_f=min($min_date);
            
            $this->SQRatingsArrCentre_date=array("max_date"=>$max_date_f,"min_date"=>$min_date_f);
            
        }
        
        function reFrameKeyQuestionsR1(){
            $arr=$this->db->array_grouping($this->SQRatingsArrCentre_r1,"kq_order");
            $this->rankArrayCentre;
            $array_f=array();
            $batches_f=array();
            //$min_date=array();
            //$max_date=array();
            //echo '<pre>';
            //print_r($arr);
            //echo '</pre>';
            
            foreach($arr as $key=>$val){
            foreach($val as $key_1=>$val_1){
                /*echo '<pre>';
                print_r($val_1);
                echo '</pre>';
                 
                 */
            //$min_date[]= date("Y-m-d",strtotime($val_1['mindate']));
            //$max_date[]= date("Y-m-d",strtotime($val_1['maxdate']));
            $array_f['Key Question '.$key.'']['key_question_text']=$val_1['key_question_text'];
            $array_f['Key Question '.$key.'']['key_question_heading']=$val_1['key_heading'];
            foreach($this->rankArrayCentre as $key_2=>$val_2){
                //echo $array_f['Key Question '.$key.'']['grades'][$val_2['rating']];
            //if(!isset($array_f['Key Question '.$key.'']['grades'][$val_2['rating']]) || (isset($array_f['Key Question '.$key.'']['grades'][$val_2['rating']]) && $array_f['Key Question '.$key.'']['grades'][$val_2['rating']]==0)){    
            $array_f['Key Question '.$key.'']['grades'][$val_2['rating']]=($val_1['rating']==$val_2['rating'])?(isset($array_f['Key Question '.$key.'']['grades'][$val_2['rating']])?$array_f['Key Question '.$key.'']['grades'][$val_2['rating']]:0)+$val_1['rating_tot']:(isset($array_f['Key Question '.$key.'']['grades'][$val_2['rating']])?$array_f['Key Question '.$key.'']['grades'][$val_2['rating']]:0);
            //}
            $batches_f[$val_1['client_name']]['Key Question '.$key.''][$val_2['rating']]=($val_1['rating']==$val_2['rating'])?(isset($batches_f[$val_1['client_name']]['Key Question '.$key.''][$val_2['rating']])?$batches_f[$val_1['client_name']]['Key Question '.$key.''][$val_2['rating']]:0)+$val_1['rating_tot']:(isset($batches_f[$val_1['client_name']]['Key Question '.$key.''][$val_2['rating']])?$batches_f[$val_1['client_name']]['Key Question '.$key.''][$val_2['rating']]:0);
            }
            }
            
            }
            $this->SQRatingsArrCentre_final_r1=$array_f;
            $this->SQRatingsArrCentre_batchesfinal_r1=$batches_f;
            //$max_date_f=max($max_date);
            //$min_date_f=min($min_date);
            
            //$this->SQRatingsArrCentre_date=array("max_date"=>$max_date_f,"min_date"=>$min_date_f);
            
        }
        
        protected function generateOverviewStudentCenterOutput(){
		$this->loadAqsDataStudentCentre();
                //print_r($this->aqsData);
		//$this->loadDiagnosticCentre();
		$this->loadRatingsCentre();
                $batch_ids=isset($_GET['batch_id'])?$_GET['batch_id']:'';
		$this->loadKeyQuestionsCentre(0,$this->student_round,$batch_ids);
		if(count($this->SQRatingsArrCentre)<=0) die("No Assessment completed for this id for Round-".$this->student_round."");
                $this->reFrameKeyQuestions();
                //print_r($this->SQRatingsArrCentre_final);
                //print_r($this->SQRatingsArrCentre_date);
                $start_date=date("d-M-Y",strtotime($this->SQRatingsArrCentre_date['min_date']));
                $end_date=date("d-M-Y",strtotime($this->SQRatingsArrCentre_date['max_date']));
                $this->tableNum=2;
                //die();
                //$this->student_round=$student_round;
                
                $this->aqsData['schools_name']="";
                
                if(gettype($this->centre_id)=="array" && count($this->centre_id)>1){
                $customreportModel=new customreportModel();
                $this->aqsData['province_name']=$customreportModel->getCentresName($this->centre_id);
                //echo count(explode(",",$batch_ids));
                
                if($batch_ids!="" && count(explode(",",$batch_ids))>0){
                $this->aqsData['schools_name']=" (".$customreportModel->getSchoolsName(explode(",",$batch_ids)).")";
                }
                
                }else{
                $customreportModel=new customreportModel();    
                if($batch_ids!="" && count(explode(",",$batch_ids))>0){
                $this->aqsData['schools_name']=" (".$customreportModel->getSchoolsName(explode(",",$batch_ids)).")";
                }
                
                }
                
                if($this->student_round==2){
               
		$this->loadKeyQuestionsCentre(2,1);
                
                $this->reFrameKeyQuestionsR1();
                }
                
                if($this->student_round==2){
                $html_kq = $this->getStudentCentreKQRatings_r2();
                }else{
                $html_kq = $this->getStudentCentreKQRatings();
                }
                
                $tot_students=$this->aqsData['tot_students'];
                $tot_batches=$this->aqsData['tot_batches'];
                
                
                
		$pdf = new reporttcpdf ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		$pdf->footer_text = $this->config['coverAddress'];
                if(gettype($this->centre_id)=="array" && count($this->centre_id)>1){
                $pdf->other_footer_text = 'Career Readiness Review: Overview Report for '.$this->aqsData['network_name'].' '.$this->aqsData['city_name'].' '.$this->aqsData['state_name'].' '.$this->aqsData['country_name'].', '.date("d-m-Y").' (generated on)';// 'AQS Teacher Performance - Overview Report for '.$this->aqsData['school_name'].', '.date("d-m-Y").' (generated on)';
                }else{
		$pdf->other_footer_text = 'Career Readiness Review: Overview Report for '.$this->aqsData['province_name'].' '.$this->aqsData['network_name'].' '.$this->aqsData['city_name'].' '.$this->aqsData['state_name'].' '.$this->aqsData['country_name'].', '.date("d-m-Y").' (generated on)';// 'AQS Teacher Performance - Overview Report for '.$this->aqsData['school_name'].', '.date("d-m-Y").' (generated on)';
                }
                
                $pdf->footerBG = $this->config['footerBG'];
		$pdf->footerColor = $this->config['footerColor'];
		$pdf->footerHeight = $this->config['footerHeight'];
		$pdf->pageNoBarHeight = $this->config['pageNoBarHeight'];
                $pdf->coverAddressAntarang=$this->config['coverAddressAntarang'];
                $pdf->coverAddressAdhyayanFoundation=$this->config['coverAddressAdhyayanFoundation'];
		$pdf->assessemnt_type = 4;
                
                
                $pdf->SetTitle('Career Readiness Review Overview Report '.'-'.$this->aqsData['province_name']);
		//$pdf->SetHeaderData ( '', '', 'Career Readiness Review Overview Report: '.$this->aqsData['province_name'].', '.$this->aqsData['network_name'].', '.$this->aqsData['city_name'].'' );
		if(gettype($this->centre_id)=="array" && count($this->centre_id)>1){
                $pdf->SetHeaderData ( '', '', 'Career Readiness Review Overview Report: '.$this->aqsData['province_name'].' '.$this->aqsData['schools_name'].'' );    
                }else{
                $pdf->SetHeaderData ( '', '', 'Career Readiness Review Overview Report: '.$this->aqsData['province_name'].''.$this->aqsData['schools_name'].', '.$this->aqsData['network_name'].', '.$this->aqsData['city_name'].'' );
                }
                
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
                $pdf->setListIndentWidth(4);
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont ( PDF_FONT_MONOSPACED );
		// set margins
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
		$firstpagehtml = '';
		/*if($this->aqsData['client_id']==11)
			$firstpagehtml = '<table class="pdfHdr broad"> <tbody><tr><td class="halfSec fl" align="left"><a href=""><img src="' . SITEURL . 'public/images/logo.png"alt="" style="height:55px;"></a></td><td class="halfSec fr" align="right" ><a href=""><img src="'.SITEURL.'/public/images/shishuvan.jpg" alt=""></a></td></tr>
		<tr><td colspan="2" style="font-weight:bold;text-align:center;font-size:13px;">The Teacher Performance Review has been initiated and validated by '.($this->aqsData['school_name']).'</td></tr>
		</table>';
		else*/
                $firstpagehtml = '<br/><table class="pdfHdr broad"><tr><td class="hSec fl" align="left"><a href=""><img src="'.$this->config['headerStudentImgAdh'].'" alt=""></a></td><td class="hSec fr" align="right"><a href=""><img src="'.$this->config['isStudentReviewImg'].'" alt="" height="90px;"></a></td></tr>  
</table><br/><br/>';
		 //$firstpagehtml = '<br/><div style="text-align:center;"><img src="' . SITEURL . 'public/images/logo.png"></div><br/><br/>';
		/*$firstpagehtml .= <<<EOD
		<div style="background-color:#00b050;color:#FFFFFF;text-align:center;border-style: solid;border:10px solid #ffc000;"><br/><br/><br/>
		<h2 style="text-align:center">CAREER READINESS REVIEW FOR INDIVIDUALS: OVERVIEW REPORT <br/></h2><div></div>
		Batch level analysis of participants’ performance against the Career Readiness Standard<br/><br/><br/><br/><br/>
		<b>$numTeachers</b> participant(s) reviewed between <b>{$this->conductedDate}</b> to <b>{$this->conductedDate}</b><br/><br/><br/><br/><br/>	
		</div>
		$teachers
EOD;*/
                $firstpagehtml .= <<<EOD
		<div style="background-color:#00b050;color:#FFFFFF;text-align:center;border-style: solid;border:10px solid #ffc000;"><br/><br/><br/>
		<h2 style="text-align:center">CAREER READINESS REVIEW FOR INDIVIDUALS: OVERVIEW REPORT <br/></h2><div></div>
		<h3>Centre level analysis of batch-wise <br>performance against the Career Readiness <br>Standard</h3><div></div><div></div><br/><br/><br/><br/>
                {$this->tot_show_stu} participant(s) across {$tot_batches} batch(es) reviewed from {$start_date} to  {$end_date}<br/><br/><br/>	
		</div>
		
EOD;
//echo $firstpagehtml;die;
				$pdf->writeHTMLCell ( 0, 0, '', '', $firstpagehtml, 0, 1, 0, true, 'J', true );
				// ---------------------------------------------------------
				// create some content ...
				// add a page
				// first page start
				//award
				$sectionNum = 1;
				$pdf->addPage ();
				$pdf->Bookmark ( $sectionNum.'. Career Readiness Review: Process', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
                                
				$pdf->SetFont ( 'times', 'B', 16 );
                                $pdf->setCellHeightRatio(1.50);
                                $pdf->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 192, 0)));
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 0, 176 , 80 );
                                //$rgb = $this->hex2rgb("#ffc000");
                                //print_r($rgb);
                                

				$pdf->Cell ( 0, 8, ($sectionNum++).'. Career Readiness Review: Process ', 1, 1, 'C', true );
				$pdf->setCellHeightRatio(1.25);
                                $pdf->SetFont ( 'times', '', 12 );
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->ln(3);
                                //$html='<div>Antarang Foundation and Adhyayan Foundation undertook the Career Readiness Review for '.$tot_students.' participants in '.$this->aqsData['province_name'].', '.$this->aqsData['city_name'].' under '.$this->aqsData['network_name'].'. The process is detailed below.</div>';
				if(gettype($this->centre_id)=="array" && count($this->centre_id)>1){
                                $html='<div>Antarang Foundation and Adhyayan Foundation undertook the Career Readiness Review for '.$tot_students.' participants in '.$this->aqsData['province_name'].'  under '.$this->aqsData['network_name'].'. The process is detailed below.</div>';    
                                }else{
                                $html='<div>Antarang Foundation and Adhyayan Foundation undertook the Career Readiness Review for '.$tot_students.' participants in '.$this->aqsData['province_name'].', '.$this->aqsData['city_name'].' under '.$this->aqsData['network_name'].'. The process is detailed below.</div>';
                                }
                                //$html = 'Adhyayan undertook the review of '.$numTeachers.' teachers of '.$this->aqsData['school_name'].'.  Adhyayan worked alongside the teachers to facilitate their understanding of \'What Good Teaching & Learning Look Like\' across the world through the Adhyayan review process explained below.';				
				//$pdf->Write ( 0, $html, '', 0, '', true, 0, false, false, 0 );
                                $pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				$pdf->ln(1);
                                 $this->tableNum=1;
				$html = $this->getPDFStudentreviewProcess();				
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				$pdf->AddPage();
				$pdf->Bookmark ( $sectionNum.'.  Career Readiness Review: Grades', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->SetTextColor ( 255, 255, 255 );
				//$pdf->SetFillColor ( 108, 13, 16 );
                                $pdf->SetFillColor ( 0, 176 , 80 );
                                $pdf->setCellHeightRatio(1.50);
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Career Readiness Review: Grades ', 1, 1, 'C', true );
				$pdf->Ln(2);
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->SetTextColor ( 0, 0, 0 );
                                $pdf->setCellHeightRatio(1.25);
				$html = $this->getStudentOverviewAwardDefinition();
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
                                
                                $pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				//$pdf->SetFillColor ( 108, 13, 16 );
                                $pdf->SetFillColor ( 0, 176 , 80 );
                                $pdf->setCellHeightRatio(1.50);
				$pdf->Bookmark ( $sectionNum.'. Performance Review: Key questions', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Performance Review: Key questions', 1, 1, 'C', true );
                                $pdf->setCellHeightRatio(1.25);
                                
				
                                //$this->tableNum++;
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->ln(3);
				$pdf->writeHTMLCell ( 0, 0, '', '', $html_kq, 0, 1, 0, true, 'J', true );
                                
				
                                $this->getPDFIndex($pdf);
				$pdf->Output ('CRR Centre level report Round '.$this->student_round.'-'.$this->aqsData['province_name'].'.pdf', 'I' );
	}
        
        protected function generateOverviewStudentOrgOutput(){
            $this->loadAqsDataStudentOrg();
            //print_r($this->aqsData);
            $this->loadRatingsCentre();
            
            $center_ids=isset($_GET['centre_id'])?$_GET['centre_id']:'';
            $batch_ids=isset($_GET['batch_id'])?$_GET['batch_id']:'';
            
            $customreportModel=new customreportModel();
              $this->aqsData['schools_name']="";
              $this->aqsData['province_name']="";
                if($batch_ids!="" && count(explode(",",$batch_ids))>0){
                $this->aqsData['schools_name']=" (Batches: ".$customreportModel->getSchoolsName(explode(",",$batch_ids)).")";
                }
                
                if($center_ids!="" && count(explode(",",$center_ids))>0){
                 $this->aqsData['province_name']=" (Centres: ".$customreportModel->getCentresName(explode(",",$center_ids)).")";
                }
                
            
	    $this->loadKeyQuestionsOrg(0,$this->student_round,$center_ids,$batch_ids);
	    if(count($this->SQRatingsArrCentre)<=0) die("No Assessment completed for this id for Round-".$this->student_round."");
            //echo"<pre>";
            //print_r($this->SQRatingsArrCentre);
            //echo"</pre>";
            $this->reFrameKeyQuestions();
            //echo"<pre>";
            //print_r($this->SQRatingsArrCentre_final);
            //print_r($this->SQRatingsArrCentre_date);
            //echo"</pre>";
                $start_date=date("d-M-Y",strtotime($this->SQRatingsArrCentre_date['min_date']));
                $end_date=date("d-M-Y",strtotime($this->SQRatingsArrCentre_date['max_date']));
                $this->tableNum=2;
                if($this->student_round==2){
               
		$this->loadKeyQuestionsOrg(2,1);
                
                $this->reFrameKeyQuestionsR1();
                }
                
                if($this->student_round==2){
                $html_kq = $this->getStudentCentreKQRatings_r2();
                }else{
                $html_kq = $this->getStudentCentreKQRatings();
                }
                
                $tot_students=$this->aqsData['tot_students'];
                $tot_batches=$this->aqsData['tot_batches'];
                
                $pdf = new reporttcpdf ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		$pdf->footer_text = $this->config['coverAddress'];
		$pdf->other_footer_text = 'Career Readiness Review: Overview Report for '.$this->aqsData['network_name'].' '.$this->aqsData['city_name'].' '.$this->aqsData['country_name'].', '.date("d-m-Y").' (generated on)';// 'AQS Teacher Performance - Overview Report for '.$this->aqsData['school_name'].', '.date("d-m-Y").' (generated on)';
		$pdf->footerBG = $this->config['footerBG'];
		$pdf->footerColor = $this->config['footerColor'];
		$pdf->footerHeight = $this->config['footerHeight'];
		$pdf->pageNoBarHeight = $this->config['pageNoBarHeight'];
                $pdf->coverAddressAntarang=$this->config['coverAddressAntarang'];
                $pdf->coverAddressAdhyayanFoundation=$this->config['coverAddressAdhyayanFoundation'];
		$pdf->assessemnt_type = 4;
                
                $pdf->SetTitle('Career Readiness Review Overview Report '.'-'.$this->aqsData['network_name']);
                
		$pdf->SetHeaderData ( '', '', 'Career Readiness Review Overview Report: '.$this->aqsData['network_name'].''.$this->aqsData['province_name'].''.$this->aqsData['schools_name'].', '.$this->aqsData['city_name'].'' );
		
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
                $pdf->setListIndentWidth(4);
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont ( PDF_FONT_MONOSPACED );
		// set margins
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
		$firstpagehtml = '';
		/*if($this->aqsData['client_id']==11)
			$firstpagehtml = '<table class="pdfHdr broad"> <tbody><tr><td class="halfSec fl" align="left"><a href=""><img src="' . SITEURL . 'public/images/logo.png"alt="" style="height:55px;"></a></td><td class="halfSec fr" align="right" ><a href=""><img src="'.SITEURL.'/public/images/shishuvan.jpg" alt=""></a></td></tr>
		<tr><td colspan="2" style="font-weight:bold;text-align:center;font-size:13px;">The Teacher Performance Review has been initiated and validated by '.($this->aqsData['school_name']).'</td></tr>
		</table>';
		else*/
                $firstpagehtml = '<br/><table class="pdfHdr broad"><tr><td class="hSec fl" align="left"><a href=""><img src="'.$this->config['headerStudentImgAdh'].'" alt=""></a></td><td class="hSec fr" align="right"><a href=""><img src="'.$this->config['isStudentReviewImg'].'" alt="" height="90px;"></a></td></tr>  
</table><br/><br/>';
		 //$firstpagehtml = '<br/><div style="text-align:center;"><img src="' . SITEURL . 'public/images/logo.png"></div><br/><br/>';
		/*$firstpagehtml .= <<<EOD
		<div style="background-color:#00b050;color:#FFFFFF;text-align:center;border-style: solid;border:10px solid #ffc000;"><br/><br/><br/>
		<h2 style="text-align:center">CAREER READINESS REVIEW FOR INDIVIDUALS: OVERVIEW REPORT <br/></h2><div></div>
		Batch level analysis of participants’ performance against the Career Readiness Standard<br/><br/><br/><br/><br/>
		<b>$numTeachers</b> participant(s) reviewed between <b>{$this->conductedDate}</b> to <b>{$this->conductedDate}</b><br/><br/><br/><br/><br/>	
		</div>
		$teachers
EOD;*/
                $firstpagehtml .= <<<EOD
		<div style="background-color:#00b050;color:#FFFFFF;text-align:center;border-style: solid;border:10px solid #ffc000;"><br/><br/><br/>
		<h2 style="text-align:center">CAREER READINESS REVIEW FOR INDIVIDUALS: OVERVIEW REPORT <br/></h2><div></div>
		<h3>Organization level analysis of <br>centre-wise performance against <br>the Career Readiness Standard</h3><div></div><div></div><br/><br/><br/><br/>
                {$this->tot_show_stu} participant(s) across {$tot_batches} centre(s) reviewed from {$start_date} to  {$end_date}<br/><br/><br/>	
		</div>
		
EOD;
//echo $firstpagehtml;die;
				$pdf->writeHTMLCell ( 0, 0, '', '', $firstpagehtml, 0, 1, 0, true, 'J', true );
				// ---------------------------------------------------------
				// create some content ...
				// add a page
				// first page start
				//award
				$sectionNum = 1;
				$pdf->addPage ();
				$pdf->Bookmark ( $sectionNum.'. Career Readiness Review: Process', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
                                
				$pdf->SetFont ( 'times', 'B', 16 );
                                $pdf->setCellHeightRatio(1.50);
                                $pdf->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 192, 0)));
				$pdf->SetTextColor ( 255, 255, 255 );
				$pdf->SetFillColor ( 0, 176 , 80 );
                                //$rgb = $this->hex2rgb("#ffc000");
                                //print_r($rgb);
                                

				$pdf->Cell ( 0, 8, ($sectionNum++).'. Career Readiness Review: Process ', 1, 1, 'C', true );
				$pdf->setCellHeightRatio(1.25);
                                $pdf->SetFont ( 'times', '', 12 );
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->ln(3);
                                $html='<div>Antarang Foundation and Adhyayan Foundation undertook the Career Readiness Review for '.$tot_batches.' Centres in '.$this->aqsData['city_name'].' under '.$this->aqsData['network_name'].'. The process is detailed below.</div>';
				//$html = 'Adhyayan undertook the review of '.$numTeachers.' teachers of '.$this->aqsData['school_name'].'.  Adhyayan worked alongside the teachers to facilitate their understanding of \'What Good Teaching & Learning Look Like\' across the world through the Adhyayan review process explained below.';				
				//$pdf->Write ( 0, $html, '', 0, '', true, 0, false, false, 0 );
                                $pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				$pdf->ln(1);
                                 $this->tableNum=1;
				$html = $this->getPDFStudentreviewProcess();				
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
				$pdf->AddPage();
				$pdf->Bookmark ( $sectionNum.'.  Career Readiness Review: Grades', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->SetTextColor ( 255, 255, 255 );
				//$pdf->SetFillColor ( 108, 13, 16 );
                                $pdf->SetFillColor ( 0, 176 , 80 );
                                $pdf->setCellHeightRatio(1.50);
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Career Readiness Review: Grades ', 1, 1, 'C', true );
				$pdf->Ln(2);
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->SetTextColor ( 0, 0, 0 );
                                $pdf->setCellHeightRatio(1.25);
				$html = $this->getStudentOverviewAwardDefinition();
				$pdf->writeHTMLCell ( 0, 0, '', '', $html, 0, 1, 0, true, 'J', true );
                                
                                $pdf->AddPage();
				$pdf->SetTextColor ( 255, 255, 255 );
				//$pdf->SetFillColor ( 108, 13, 16 );
                                $pdf->SetFillColor ( 0, 176 , 80 );
                                $pdf->setCellHeightRatio(1.50);
				$pdf->Bookmark ( $sectionNum.'. Performance Review: Key questions', 0, 0, '', 'B', array (
						0,
						64,
						128
				) );
				$pdf->SetFont ( 'times', 'B', 16 );
				$pdf->Cell ( 0, 8, ($sectionNum++).'. Performance Review: Key questions', 1, 1, 'C', true );
                                $pdf->setCellHeightRatio(1.25);
                                
				
                                //$this->tableNum++;
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'times', '', 12 );
				$pdf->ln(3);
				$pdf->writeHTMLCell ( 0, 0, '', '', $html_kq, 0, 1, 0, true, 'J', true );
                                
				
                                $this->getPDFIndex($pdf);
				$pdf->Output ('CRR Org. level report Round '.$this->student_round.'-'.$this->aqsData['network_name'].'.pdf', 'I' );

            
        }
        
	protected function getTeacherSQRatings(){
		$html = '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>
				The following tables represent performance of all teachers on every sub-question based on the external review ratings. This will help the school leaders to identify common strengths and areas of professional development for all their teachers.<br/>';
		//print_r($this->SQRatingsArr);die;
		$sqnum=0;$sqnum1=0;
                $dept_id=empty($this->dept_id)?0:$this->dept_id;
		foreach($this->SQRatingsArr as $key=>$arr){			
		//	print_r($arr);
			//$total = intval($arr['Sub Question 1']['score1']) + intval($arr['Sub Question 1']['score2']) + intval($arr['Sub Question 1']['score3']) + intval($arr['Sub Question 1']['score4']) + intval($arr['Sub Question 1']['score5']);
			$total = floatval($arr['Sub Question 1']['score1']) + floatval($arr['Sub Question 1']['score2']) + floatval($arr['Sub Question 1']['score3']) + floatval($arr['Sub Question 1']['score4']) + floatval($arr['Sub Question 1']['score5']);			
			$html .= '<table cellspacing="0" cellpadding="4" border="0" nobr="true">
					
						<tr><td colspan="4" align="center">Table '.++$this->tableNum.'</td></tr>
						<tr style="border:solid 1px #000000;"><td rowspan="2" width="16%" style="border:solid 1px #000000;">Overall</td><td colspan="3" width="84%" style="border:solid 1px #000000;">'.$key.': '.$arr['text'].'</td></tr>
						<tr style="text-align:center;" style="border:solid 1px #000000;"><td style="border:solid 1px #000000;">Sub Question '.(++$sqnum).'</td><td style="border:solid 1px #000000;">Sub Question '.(++$sqnum).'</td><td style="border:solid 1px #000000;">Sub Question '.(++$sqnum).'</td></tr>										
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-5" width="16%">Exceptional</td><td width="28%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score5']*100/$total,1).'%</td><td width="28%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score5']*100/$total,1).'%</td><td width="28%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score5']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-4">Proficient</td><td  style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score4']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score4']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score4']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-3">Developing</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score3']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score3']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score3']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-2">Emerging</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score2']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score2']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score2']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-1">Foundation</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score1']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score1']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score1']*100/$total,1).'%</td></tr>
					';
			$html .='</table><br/><br/>Sub Questions:<br/>'.(++$sqnum1).'. '.$arr['Sub Question 1']['text_stmt'].'<br/>'.(++$sqnum1).'. '.$arr['Sub Question 2']['text_stmt'].'<br/>'.(++$sqnum1).'. '.$arr['Sub Question 3']['text_stmt'].'<br/><br/>';
			//print_r($arr);
			//echo $arr['Sub Question 1']['key_question_instance_id'];
			$reportModel = new reportModel();			
			$recomm = $reportModel->isExistingRecommendation($this->groupAssessmentId,$this->diagnosticId, 'key_question', $arr['Sub Question 1']['key_question_instance_id'],$dept_id);			
			
                        if ($recomm != 0) {
				$html .= "<p><b>Recommendations:</b> </p>";				
				$recomm = explode ( '~', $recomm['recommendations'] );			
				$section_recomm = '
				<div>
				    <table width="100%" >';
							
							foreach ( $recomm as $value ) {
								$section_recomm .= '<tr>
				                        <td width="12%" style="color:#6c0d10;font-size:12" align="center" valign="top">
				                        <img src="' . SITEURL . 'public/images/tick.png" width="25" height="25">
				                        </td>
				                        <td width="88%" style="font-size:12" align="left">' . $value . '<br/></td>
				                    </tr>';
							}
							$section_recomm .= '</table>
				</div>
				';							
				$html .= $section_recomm;
				
			}			
			
		}
		//die;
		//echo $html;die;
		return $html;
	}
        
        protected function getStudentSQRatings(){
		$html = '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>
				The following represents the performance of all participants within '.$this->aqsData['school_name'].', '.$this->aqsData['province_name'].', '.$this->aqsData['network_name'].' on the level of each sub question, based on the external validation.<br/>';
		//print_r($this->SQRatingsArr);die;
		$sqnum=0;$sqnum1=0;
		foreach($this->SQRatingsArr as $key=>$arr){			
		//	print_r($arr);
			//$total = intval($arr['Sub Question 1']['score1']) + intval($arr['Sub Question 1']['score2']) + intval($arr['Sub Question 1']['score3']) + intval($arr['Sub Question 1']['score4']) + intval($arr['Sub Question 1']['score5']);
			$total = floatval($arr['Sub Question 1']['score1']) + floatval($arr['Sub Question 1']['score2']) + floatval($arr['Sub Question 1']['score3']) + floatval($arr['Sub Question 1']['score4']) + floatval($arr['Sub Question 1']['score5']);			
			$html .= '<table cellspacing="0" cellpadding="4" border="0" nobr="true">
					
						<tr><td colspan="4" align="center">Table '.++$this->tableNum.'</td></tr>';
                        
						//$html .= '<tr style="border:solid 1px #000000;"><td rowspan="2" width="16%" style="border:solid 1px #000000;">Overall</td><td colspan="3" width="84%" style="border:solid 1px #000000;">'.$key.': '.$arr['text'].'</td></tr>';
                        
						$html .= '<tr style="text-align:center;" style="border:solid 1px #000000;"><td  width="16%" style="border:solid 1px #000000;">Overall</td><td style="border:solid 1px #000000;text-align:left;" width="28%" ><b>Sub Question '.(++$sqnum).':</b> <span style="font-size:12px;">'.$arr['Sub Question 1']['text'].'</span></td><td style="border:solid 1px #000000;text-align:left;" width="28%" ><b>Sub Question '.(++$sqnum).':</b> <span style="font-size:12px;">'.$arr['Sub Question 2']['text'].'</span></td><td style="border:solid 1px #000000;text-align:left;" width="28%" ><b>Sub Question '.(++$sqnum).':</b> <span style="font-size:12px;">'.$arr['Sub Question 3']['text'].'</span></td></tr>										
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-5" width="16%">Exceptional</td><td width="28%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score5']*100/$total,1).'%</td><td width="28%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score5']*100/$total,1).'%</td><td width="28%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score5']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-4">Proficient</td><td  style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score4']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score4']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score4']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-3">Developing</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score3']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score3']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score3']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-2">Emerging</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score2']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score2']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score2']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-1">Foundation</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score1']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score1']*100/$total,1).'%</td><td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score1']*100/$total,1).'%</td></tr>
					';
			$html .='</table>';
                        //$html .='<br/><br/>Sub Questions:<br/>'.(++$sqnum1).'. '.$arr['Sub Question 1']['text_stmt'].'<br/>'.(++$sqnum1).'. '.$arr['Sub Question 2']['text_stmt'].'<br/>'.(++$sqnum1).'. '.$arr['Sub Question 3']['text_stmt'].'<br/><br/>';
			//print_r($arr);
			//echo $arr['Sub Question 1']['key_question_instance_id'];
			/*$reportModel = new reportModel();			
			$recomm = $reportModel->isExistingRecommendation($this->groupAssessmentId,$this->diagnosticId, 'key_question', $arr['Sub Question 1']['key_question_instance_id']);			
			if ($recomm != 0) {
				$html .= "<p><b>Recommendations:</b> </p>";				
				$recomm = explode ( '~', $recomm['recommendations'] );			
				$section_recomm = '
				<div>
				    <table width="100%" >';
							
							foreach ( $recomm as $value ) {
								$section_recomm .= '<tr>
				                        <td width="12%" style="color:#6c0d10;font-size:12" align="center" valign="top">
				                        <img src="' . SITEURL . 'public/images/tick.png" width="25" height="25">
				                        </td>
				                        <td width="88%" style="font-size:12" align="left">' . $value . '<br/></td>
				                    </tr>';
							}
							$section_recomm .= '</table>
				</div>
				';							
				$html .= $section_recomm;
				
			}*/			
			
		}
		//die;
		//echo $html;die;
		return $html;
	}
        
        protected function getStudentSQRatings_r1(){
		$html = '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>
				The following represents the comparative performance of all participants within '.$this->aqsData['school_name'].', '.$this->aqsData['province_name'].', '.$this->aqsData['network_name'].' on the level of each sub question, based on the external validation.<br/><br/>';
		//print_r($this->SQRatingsArr_r1);die;
		$sqnum=0;$sqnum1=0;
		foreach($this->SQRatingsArr as $key=>$arr){			
		//	print_r($arr);
			//$total = intval($arr['Sub Question 1']['score1']) + intval($arr['Sub Question 1']['score2']) + intval($arr['Sub Question 1']['score3']) + intval($arr['Sub Question 1']['score4']) + intval($arr['Sub Question 1']['score5']);
			$arr_r1=$this->SQRatingsArr_r1[$key];
                        $total = floatval($arr['Sub Question 1']['score1']) + floatval($arr['Sub Question 1']['score2']) + floatval($arr['Sub Question 1']['score3']) + floatval($arr['Sub Question 1']['score4']) + floatval($arr['Sub Question 1']['score5']);			
			$total_r1 = floatval($arr_r1['Sub Question 1']['score1']) + floatval($arr_r1['Sub Question 1']['score2']) + floatval($arr_r1['Sub Question 1']['score3']) + floatval($arr_r1['Sub Question 1']['score4']) + floatval($arr_r1['Sub Question 1']['score5']);			
                        $sq1='Sub Question '.(++$sqnum).'';  
                        $sq2='Sub Question '.(++$sqnum).''; 
                        $sq3='Sub Question '.(++$sqnum).'';
                        /*$html .= '<table cellspacing="0" cellpadding="4" border="0" nobr="true">
					
						<tr><td colspan="4" align="center">Table '.++$this->tableNum.'</td></tr>';
                        
						//$html .= '<tr style="border:solid 1px #000000;"><td rowspan="2" width="16%" style="border:solid 1px #000000;">Overall</td><td colspan="3" width="84%" style="border:solid 1px #000000;">'.$key.': '.$arr['text'].'</td></tr>';
                                        $sq1='Sub Question '.(++$sqnum).'';  
                                        $sq2='Sub Question '.(++$sqnum).''; 
                                        $sq3='Sub Question '.(++$sqnum).'';
					$html .= '<tr style="text-align:center;" style="border:solid 1px #000000;"><td  width="16%" style="border:solid 1px #000000;">Overall</td><td style="border:solid 1px #000000;text-align:left;" width="28%" colspan="2"><b>'.$sq1.':</b> <span style="font-size:12px;">'.$arr['Sub Question 1']['text'].'</span></td><td style="border:solid 1px #000000;text-align:left;" width="28%" colspan="2"><b>'.$sq2.':</b> <span style="font-size:12px;">'.$arr['Sub Question 2']['text'].'</span></td><td style="border:solid 1px #000000;text-align:left;" width="28%" colspan="2"><b>'.$sq3.':</b> <span style="font-size:12px;">'.$arr['Sub Question 3']['text'].'</span></td></tr>										
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" width="16%"></td><td width="14%" style="text-align:center;border:solid 1px #000000;">Baseline</td><td width="14%" style="text-align:center;border:solid 1px #000000;">Endline</td><td width="14%" style="text-align:center;border:solid 1px #000000;">Baseline</td><td width="14%" style="text-align:center;border:solid 1px #000000;">Endline</td><td width="14%" style="text-align:center;border:solid 1px #000000;">Baseline</td><td width="14%" style="text-align:center;border:solid 1px #000000;">Endline</td></tr>
					
                                        <tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-5" width="16%">Exceptional</td><td width="14%" style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 1']['score5']*100/$total_r1,1).'%</td><td width="14%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score5']*100/$total,1).'%</td>'
                                                . '<td width="14%" style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 2']['score5']*100/$total_r1,1).'%</td><td width="14%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score5']*100/$total,1).'%</td><td width="14%" style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 3']['score5']*100/$total_r1,1).'%</td>'
                                                . '<td width="14%" style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score5']*100/$total,1).'%</td></tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-4">Proficient</td>
                                        <td  style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 1']['score4']*100/$total_r1,1).'%</td>
                                                <td  style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score4']*100/$total,1).'%</td>'
                                                . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 2']['score4']*100/$total_r1,1).'%</td>'
                                                . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score4']*100/$total,1).'%</td>'
                                                . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 3']['score4']*100/$total_r1,1).'%</td>
                                                   <td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score4']*100/$total,1).'%</td></tr>
                                                    
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-3">Developing</td>
                                        <td style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 1']['score3']*100/$total_r1,1).'%</td>'
                                                . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score3']*100/$total,1).'%</td>'
                                                . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 2']['score3']*100/$total_r1,1).'%</td>'
                                                . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score3']*100/$total,1).'%</td>'
                                                . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 3']['score3']*100/$total_r1,1).'%</td>
                                                  <td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score3']*100/$total,1).'%</td>
                                                    </tr>
                                                    
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-2">Emerging</td>
                                        <td style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 1']['score2']*100/$total_r1,1).'%</td>'
                                                . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score2']*100/$total,1).'%</td>'
                                                . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 2']['score2']*100/$total_r1,1).'%</td>'
                                                . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score2']*100/$total,1).'%</td>'
                                                
                                                . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 3']['score2']*100/$total_r1,1).'%</td>
                                                   <td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score2']*100/$total,1).'%</td> 
                                                   </tr>
					<tr style="border:solid 1px #000000;"><td style="border:solid 1px #000000;" class="scheme-2-score-1">Foundation</td>
                                        <td style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 1']['score1']*100/$total_r1,1).'%</td>'
                                        . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 1']['score1']*100/$total,1).'%</td>'
                                        . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 2']['score1']*100/$total_r1,1).'%</td>'
                                        . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 2']['score1']*100/$total,1).'%</td>'
                                                
                                        . '<td style="text-align:center;border:solid 1px #000000;">'.round($arr_r1['Sub Question 3']['score1']*100/$total_r1,1).'%</td>
                                            <td style="text-align:center;border:solid 1px #000000;">'.round($arr['Sub Question 3']['score1']*100/$total,1).'%</td>
                                            </tr>
					';
			$html .='</table>';*/
                        $key_g1= str_replace(" ", "", $sq1);
                        $$key_g1.="Exceptional=".round($arr_r1['Sub Question 1']['score5']*100/$total_r1,1)."~".round($arr['Sub Question 1']['score5']*100/$total,1)."&"
                                . "Proficient=".round($arr_r1['Sub Question 1']['score4']*100/$total_r1,1)."~".round($arr['Sub Question 1']['score4']*100/$total,1)."&"
                                . "Developing=".round($arr_r1['Sub Question 1']['score3']*100/$total_r1,1)."~".round($arr['Sub Question 1']['score3']*100/$total,1)."&"
                                . "Emerging=".round($arr_r1['Sub Question 1']['score2']*100/$total_r1,1)."~".round($arr['Sub Question 1']['score2']*100/$total,1)."&"
                                . "Foundation=".round($arr_r1['Sub Question 1']['score1']*100/$total_r1,1)."~".round($arr['Sub Question 1']['score1']*100/$total,1).""; 
                        
                        $key_g2= str_replace(" ", "", $sq2);
                        $$key_g2.="Exceptional=".round($arr_r1['Sub Question 2']['score5']*100/$total_r1,1)."~".round($arr['Sub Question 2']['score5']*100/$total,1)."&"
                                . "Proficient=".round($arr_r1['Sub Question 2']['score4']*100/$total_r1,1)."~".round($arr['Sub Question 2']['score4']*100/$total,1)."&"
                                . "Developing=".round($arr_r1['Sub Question 2']['score3']*100/$total_r1,1)."~".round($arr['Sub Question 2']['score3']*100/$total,1)."&"
                                . "Emerging=".round($arr_r1['Sub Question 2']['score2']*100/$total_r1,1)."~".round($arr['Sub Question 2']['score2']*100/$total,1)."&"
                                . "Foundation=".round($arr_r1['Sub Question 2']['score1']*100/$total_r1,1)."~".round($arr['Sub Question 2']['score1']*100/$total,1).""; 
                        
                        $key_g3= str_replace(" ", "", $sq3);
                        $$key_g3.="Exceptional=".round($arr_r1['Sub Question 3']['score5']*100/$total_r1,1)."~".round($arr['Sub Question 3']['score5']*100/$total,1)."&"
                                . "Proficient=".round($arr_r1['Sub Question 3']['score4']*100/$total_r1,1)."~".round($arr['Sub Question 3']['score4']*100/$total,1)."&"
                                . "Developing=".round($arr_r1['Sub Question 3']['score3']*100/$total_r1,1)."~".round($arr['Sub Question 3']['score3']*100/$total,1)."&"
                                . "Emerging=".round($arr_r1['Sub Question 3']['score2']*100/$total_r1,1)."~".round($arr['Sub Question 3']['score2']*100/$total,1)."&"
                                . "Foundation=".round($arr_r1['Sub Question 3']['score1']*100/$total_r1,1)."~".round($arr['Sub Question 3']['score1']*100/$total,1).""; 
			
		}
		//die;
		//echo $html;die;
             
                $sqnum=0;$sqnum1=0;
                $i=1;
                $graph=4;
                $html .= '<table cellspacing="0" cellpadding="0" border="0">';
		foreach($this->SQRatingsArr as $key=>$arr){
                    //print_r($arr);
                    $sq1='Sub Question '.(++$sqnum).'';  
                    $sq2='Sub Question '.(++$sqnum).''; 
                    $sq3='Sub Question '.(++$sqnum).'';
                    
                    $key_g1= str_replace(" ", "", $sq1);
                    $key_g2= str_replace(" ", "", $sq2);
                    $key_g3= str_replace(" ", "", $sq3);
                    
                    /*echo $$key_g1;
                    echo"<br>";
                    echo $$key_g2;
                    echo"<br>";
                    echo $$key_g3;
                    echo"<br>";*/
                    $url1="".$$key_g1."&type=SQ";
                    $url2="".$$key_g2."&type=SQ";
                    $url3="".$$key_g3."&type=SQ";
                    
                    /*$html .= '<table cellspacing="0" cellpadding="4" border="0" nobr="true">';
                    $html .= '<tr><td align="center">graph '.$graph++.'</td><td  align="center" >graph '.$graph++.'</td><td  align="center">graph '.$graph++.'</td></tr>';
                    
                    $html .= '<tr><td align="center" style="border:solid 1px #000000;"><b>'.$sq1.':</b></td><td  align="center"  style="border:solid 1px #000000;"><b>'.$sq2.':</b></td><td  align="center"  style="border:solid 1px #000000;"><b>'.$sq3.':</b></td></tr>';
                    $html .= '<tr><td  style="border:solid 1px #000000;"><span style="font-size:12px;">'.$arr['Sub Question 1']['text'].'</span></td>'
                            . '<td  style="border:solid 1px #000000;"><span style="font-size:12px;">'.$arr['Sub Question 2']['text'].'</span></td>'
                            . '<td  style="border:solid 1px #000000;"><span style="font-size:12px;">'.$arr['Sub Question 3']['text'].'</span></td></tr>';
                    $html.='<tr><td  style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url1.'" ></div></td>';
                    $html.='<td  style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url2.'" ></div></td>';
                    $html.='<td  style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url3.'"></div></td></tr>';
                    $html .= '</table><br><br>';*/
                    
                    if($i==1){
                    $html.='<tr><td nobr="true"><table  cellspacing="0" cellpadding="4" border="0" nobr="true"><tr><td align="center" style="border:solid 1px #000000;">graph '.$graph++.'</td></tr><tr><td align="center" style="border:solid 1px #000000;" ><b>'.$sq1.':</b></td></tr><tr><td  style="border:solid 1px #000000;" height="60px;"><span style="font-size:12px;">'.$arr['Sub Question 1']['text'].'</span></td></tr><tr><td style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url1.'" style="height:170px;"></div></td></tr></table></td>';
                    $html.='<td nobr="true"><table  cellspacing="0" cellpadding="4" border="0" nobr="true"><tr><td align="center" style="border:solid 1px #000000;">graph '.$graph++.'</td></tr><tr><td align="center" style="border:solid 1px #000000;" ><b>'.$sq2.':</b></td></tr><tr><td  style="border:solid 1px #000000;" height="60px;"><span style="font-size:12px;">'.$arr['Sub Question 2']['text'].'</span></td></tr><tr><td style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url2.'" style="height:170px;"></div></td></tr></table></td></tr>';
                    $html.='<tr><td nobr="true"><table  cellspacing="0" cellpadding="4" border="0" nobr="true"><tr><td align="center" style="border:solid 1px #000000;">graph '.$graph++.'</td></tr><tr><td align="center" style="border:solid 1px #000000;" ><b>'.$sq3.':</b></td></tr><tr><td  style="border:solid 1px #000000;" height="60px;"><span style="font-size:12px;">'.$arr['Sub Question 3']['text'].'</span></td></tr><tr><td style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url3.'" style="height:170px;"></div></td></tr></table></td>';
                    }else if($i==2){
                    $html.='<td nobr="true"><table  cellspacing="0" cellpadding="4" border="0" nobr="true"><tr><td align="center" style="border:solid 1px #000000;">graph '.$graph++.'</td></tr><tr><td align="center" style="border:solid 1px #000000;" ><b>'.$sq1.':</b></td></tr><tr><td  style="border:solid 1px #000000;" height="60px;"><span style="font-size:12px;">'.$arr['Sub Question 1']['text'].'</span></td></tr><tr><td style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url1.'" style="height:170px;"></div></td></tr></table></td></tr>';
                    $html.='<tr><td nobr="true"><table  cellspacing="0" cellpadding="4" border="0" nobr="true"><tr><td align="center" style="border:solid 1px #000000;">graph '.$graph++.'</td></tr><tr><td align="center" style="border:solid 1px #000000;" ><b>'.$sq2.':</b></td></tr><tr><td  style="border:solid 1px #000000;" height="60px;"><span style="font-size:12px;">'.$arr['Sub Question 2']['text'].'</span></td></tr><tr><td style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url2.'" style="height:170px;"></div></td></tr></table></td>';
                    $html.='<td nobr="true"><table  cellspacing="0" cellpadding="4" border="0" nobr="true"><tr><td align="center" style="border:solid 1px #000000;">graph '.$graph++.'</td></tr><tr><td align="center" style="border:solid 1px #000000;" ><b>'.$sq3.':</b></td></tr><tr><td  style="border:solid 1px #000000;" height="60px;"><span style="font-size:12px;">'.$arr['Sub Question 3']['text'].'</span></td></tr><tr><td style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url3.'" style="height:170px;"></div></td></tr></table></td></tr>';
                       
                    }else if($i==3){
                    $html.='<tr><td nobr="true"><table  cellspacing="0" cellpadding="4" border="0" nobr="true"><tr><td align="center" style="border:solid 1px #000000;">graph '.$graph++.'</td></tr><tr><td align="center" style="border:solid 1px #000000;" ><b>'.$sq1.':</b></td></tr><tr><td  style="border:solid 1px #000000;" height="60px;"><span style="font-size:12px;">'.$arr['Sub Question 1']['text'].'</span></td></tr><tr><td style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url1.'" style="height:170px;"></div></td></tr></table></td>';
                    $html.='<td nobr="true"><table  cellspacing="0" cellpadding="4" border="0" nobr="true"><tr><td align="center" style="border:solid 1px #000000;">graph '.$graph++.'</td></tr><tr><td align="center" style="border:solid 1px #000000;" ><b>'.$sq2.':</b></td></tr><tr><td  style="border:solid 1px #000000;" height="60px;"><span style="font-size:12px;">'.$arr['Sub Question 2']['text'].'</span></td></tr><tr><td style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url2.'" style="height:170px;"></div></td></tr></table></td></tr>';
                    $html.='<tr><td nobr="true"><table  cellspacing="0" cellpadding="4" border="0" nobr="true"><tr><td align="center" style="border:solid 1px #000000;">graph '.$graph++.'</td></tr><tr><td align="center" style="border:solid 1px #000000;" ><b>'.$sq3.':</b></td></tr><tr><td  style="border:solid 1px #000000;" height="60px;"><span style="font-size:12px;">'.$arr['Sub Question 3']['text'].'</span></td></tr><tr><td style="border:solid 1px #000000;"><div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url3.'" style="height:170px;"></div></td></tr></table></td></tr>';
                    }
                    
                    /*$html .= '<table cellspacing="0" cellpadding="4" border="0" nobr="true">';
                    $html .= '<tr><td style="text-align:center;background-color: #d8e7de ;"><b>'.$key.': '.$arr['key_heading'].'</b></td></tr>';
                    $html .= '<tr><td><b>'.$sq1.':</b> <span>'.$arr['Sub Question 1']['text'].'</span></td></tr>';
                    $html .= '<tr><td style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url1.'" ></td></tr>';
                    
                    $html .= '<tr><td><b>'.$sq2.':</b> <span>'.$arr['Sub Question 2']['text'].'</span></td></tr>';
                    $html .= '<tr><td  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url2.'" ></td></tr>';
                    
                    $html .= '<tr><td><b>'.$sq3.':</b> <span>'.$arr['Sub Question 3']['text'].'</span></td></tr>';
                    $html .= '<tr><td style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url3.'" ></td></tr>';
                    $html .= '</table>';*/
                   $i++;
                    
                }
                $html .= '</table>';
               
                
		return $html;
                
                
                
                
	}
        
        protected function getStudentKQRatings(){
                 $html = '<style>' . file_get_contents(ROOT . 'public' . DS . 'css' . DS . 'reports.css') . '</style>';

       

                $html .= 'The following represents the performance of all participants within ' . $this->aqsData['school_name'] . ', ' . $this->aqsData['province_name'] . ', ' . $this->aqsData['network_name'] . ' on the level of each key question, based on the external validation.<br/>';
        
        //print_r($this->SQRatingsArr);die;
		$sqnum=0;$sqnum1=0;
                $array_final=array();
                $array_final_class=array("Exceptional"=>5,"Proficient"=>4,"Developing"=>3,"Emerging"=>2,"Foundation"=>1);
		foreach($this->SQRatingsArr as $key=>$arr){			
			$total = floatval($arr['score1']) + floatval($arr['score2']) + floatval($arr['score3']) + floatval($arr['score4']) + floatval($arr['score5']);			
					
                                        $array_final['Exceptional']['Key Question '.(++$sqnum).'']['rating']=round($arr['score5']*100/$total,1);
                                        $array_final['Exceptional']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final['Exceptional']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['rating']=round($arr['score4']*100/$total,1);
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final['Developing']['Key Question '.($sqnum).'']['rating']=round($arr['score3']*100/$total,1);
                                        $array_final['Developing']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final['Developing']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['rating']=round($arr['score2']*100/$total,1);
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['rating']=round($arr['score1']*100/$total,1);
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                          
                        		
			
		}
                
                
                $html .= '<table cellspacing="0" cellpadding="4" border="0" nobr="true">
			  <tr><td colspan="4" align="center">Table '.++$this->tableNum.'</td></tr>';
                $html .= '<tr style="text-align:center;" style="border:solid 1px #000000;"><td  width="16%" style="border:solid 1px #000000;">Overall</td>';
                //$array_question_text=array("Key Question 1"=>"Self-awareness","Key Question 2"=>"Career-awareness","Key Question 3"=>"Skills and mindsets");
                $kq_text='<table width="100%" style="padding:5px;"><tr><td width="100%"><b>Key questions :</b></td></tr>';                                                                                        
                $i=1;
                foreach($array_final['Exceptional'] as $key=>$val){
                $html .= '<td style="border:solid 1px #000000;text-align:left;" width="28%" ><b>'.$key.': '.$val['heading'].'</b></td>';
                $kq_text.='<tr><td><div>'.($i).'. '.$val['text'].'</div></td></tr>';
                $i++;
                }
                $kq_text.='</table>';
                $html .= '</tr>';
                $i=1;
                foreach($array_final as $key=>$val){
                        									
					$html .= '<tr style="border:solid 1px #000000;">
                                                <td style="border:solid 1px #000000;" class="scheme-2-score-'.$array_final_class[$key].'" width="16%">'.$key.'</td>';
                                                foreach($val as $key_1=>$val_1){
                                                 $html .= '<td width="28%" style="text-align:center;border:solid 1px #000000;">'.$val_1['rating'].'%</td>';
                                                }                                                        
                                                $html .= '</tr>';
                                                $i++;
					
                }
                $html .='</table><br><br>'.$kq_text.'';
                //echo"<pre>";
                //print_r($array_final);
                //echo"</pre>";
		
		return $html;
	}
        
        protected function getStudentKQRatings_r1(){
                 $html = '<style>' . file_get_contents(ROOT . 'public' . DS . 'css' . DS . 'reports.css') . '</style>';

        
            $html .= 'The following represents the comparative performance of all participants within ' . $this->aqsData['school_name'] . ', ' . $this->aqsData['province_name'] . ', ' . $this->aqsData['network_name'] . ' on the level of each key question, based on the external validation.<br/>';
        
        //print_r($this->SQRatingsArr);die;
                $graph=1;                                                                                        
		$sqnum=0;$sqnum1=0;
                $array_final=array();
                $array_final_r1=array();
                $array_final_class=array("Exceptional"=>5,"Proficient"=>4,"Developing"=>3,"Emerging"=>2,"Foundation"=>1);
		foreach($this->SQRatingsArr as $key=>$arr){			
			$total = floatval($arr['score1']) + floatval($arr['score2']) + floatval($arr['score3']) + floatval($arr['score4']) + floatval($arr['score5']);			
					
                                        $array_final['Exceptional']['Key Question '.(++$sqnum).'']['rating']=round($arr['score5']*100/$total,1);
                                        $array_final['Exceptional']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final['Exceptional']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['rating']=round($arr['score4']*100/$total,1);
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final['Developing']['Key Question '.($sqnum).'']['rating']=round($arr['score3']*100/$total,1);
                                        $array_final['Developing']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final['Developing']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['rating']=round($arr['score2']*100/$total,1);
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['rating']=round($arr['score1']*100/$total,1);
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                          
                        		
			
		}
                $sqnum=0;$sqnum1=0;
                foreach($this->SQRatingsArr_r1 as $key=>$arr){			
			$total = floatval($arr['score1']) + floatval($arr['score2']) + floatval($arr['score3']) + floatval($arr['score4']) + floatval($arr['score5']);			
					
                                        $array_final_r1['Exceptional']['Key Question '.(++$sqnum).'']['rating']=round($arr['score5']*100/$total,1);
                                        $array_final_r1['Exceptional']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final_r1['Exceptional']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final_r1['Proficient']['Key Question '.($sqnum).'']['rating']=round($arr['score4']*100/$total,1);
                                        $array_final_r1['Proficient']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final_r1['Proficient']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final_r1['Developing']['Key Question '.($sqnum).'']['rating']=round($arr['score3']*100/$total,1);
                                        $array_final_r1['Developing']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final_r1['Developing']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final_r1['Emerging']['Key Question '.($sqnum).'']['rating']=round($arr['score2']*100/$total,1);
                                        $array_final_r1['Emerging']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final_r1['Emerging']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                                        
                                        $array_final_r1['Foundation']['Key Question '.($sqnum).'']['rating']=round($arr['score1']*100/$total,1);
                                        $array_final_r1['Foundation']['Key Question '.($sqnum).'']['text']=$arr['text'];
                                        $array_final_r1['Foundation']['Key Question '.($sqnum).'']['heading']=$arr['key_heading'];
                          
                        		
			
		}
                
                /*$html .= '<table cellspacing="0" cellpadding="4" border="0" nobr="true">
			  <tr><td colspan="4" align="center">Table '.++$this->tableNum.'</td></tr>';*/
                //$html .= '<tr style="text-align:center;" style="border:solid 1px #000000;"><td  width="16%" style="border:solid 1px #000000;">Overall</td>';
                //$array_question_text=array("Key Question 1"=>"Self-awareness","Key Question 2"=>"Career-awareness","Key Question 3"=>"Skills and mindsets");
                $kq_text='<table width="100%" style="padding:5px;"><tr><td width="100%"><b>Key questions :</b></td></tr>';                                                                                        
                $i=1;
                $html_1='';
                foreach($array_final['Exceptional'] as $key=>$val){
                //$html .= '<td style="border:solid 1px #000000;text-align:left;" width="28%" colspan="2"><b>'.$key.': '.$val['heading'].'</b></td>';
                $kq_text.='<tr><td><div>'.($i).'. '.$val['text'].'</div></td></tr>';
                //$html_1.='<td width="14%" style="border:solid 1px #000000;text-align:left;">Baseline</td><td  width="14%" style="border:solid 1px #000000;text-align:left;">Endline</td>';
                $i++;
                }
                
                $kq_text.='</table>';
               
                
                $i=1;
                foreach($array_final as $key=>$val){
                                        
					/*$html .= '<tr style="border:solid 1px #000000;">
                                                <td style="border:solid 1px #000000;" class="scheme-2-score-'.$array_final_class[$key].'" width="16%">'.$key.'</td>';
                                                */foreach($val as $key_1=>$val_1){
                                                 $key_g= str_replace(" ", "", $key_1);
                                                 if(isset($$key_g)){
                                                 $$key_g.="".$key."=".$array_final_r1[$key][$key_1]['rating']."~".$val_1['rating']."&"; 
                                                 }else{
                                                 $$key_g="".$key."=".$array_final_r1[$key][$key_1]['rating']."~".$val_1['rating']."&"; 
                                                 }
                                                 //$html .= '<td width="14%" style="text-align:center;border:solid 1px #000000;">'.$array_final_r1[$key][$key_1]['rating'].'%</td>';   
                                                 //$html .= '<td width="14%" style="text-align:center;border:solid 1px #000000;">'.$val_1['rating'].'%</td>';
                                                }                                                        
                                                //$html .= '</tr>';
                                                $i++;
					
                }
                
                $i=1;
                $html .= '<br><b>Key questions:</b><br><table cellspacing="0" cellpadding="4" border="0">';
                foreach($array_final['Exceptional'] as $key=>$val){
                $key_g= str_replace(" ", "", $key);
                $$key_g.='head_show='.urlencode($key).':'.urlencode($val['heading']).'';
                $url=$$key_g;
                //echo $$key_g;
                //echo"<br>";
                $html.='<tr nobr="true"><td><div>'.($i).'. '.$val['text'].'</div><div style="text-align:center;"><b>'.$key.':'.$val['heading'].'</b></div><div style="text-align:center;">graph '.$graph++.'</div>';
                $html .= '<div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url.'"></div></td></tr>';
                $i++;
                }
                $html .='</table>';
                //echo"<pre>";
                //print_r($array_final);
                //echo"</pre>";
		
		return $html;
	}
        
         protected function getStudentCentreKQRatings(){
            if($this->reportId==12){
            $html = '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>The following represents the performance of all participants within the given time frame at '.$this->aqsData['network_name'].' on the level of each key question, based on the external validation.<br/>';    
            }else{
            $html = '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>The following represents the performance of all participants within the given time frame at '.$this->aqsData['province_name'].', '.$this->aqsData['network_name'].' on the level of each key question, based on the external validation.<br/>';
            }
            $kq_text='<table width="100%" style="padding:5px;"><tr><td width="100%"><b>Key questions :</b></td></tr>';
            $i=1;
            $array_final_class=array("Exceptional"=>5,"Proficient"=>4,"Developing"=>3,"Emerging"=>2,"Foundation"=>1);
            $html .= '<table cellspacing="0" cellpadding="4" border="0" nobr="true">
			  <tr><td colspan="4" align="center">Table '.++$this->tableNum.'</td></tr>';
            
           
            $sqnum=0;$sqnum1=0;
            $array_final=array();
            foreach($this->SQRatingsArrCentre_final as $key=>$arr){
            //print_r($arr);
            $total=array_sum($arr['grades']);
                                        $array_final['Exceptional']['Key Question '.(++$sqnum).'']['rating']=round($arr['grades']['Exceptional']*100/$total,1);
                                        $array_final['Exceptional']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final['Exceptional']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Proficient']*100/$total,1);
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final['Developing']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Developing']*100/$total,1);
                                        $array_final['Developing']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final['Developing']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Emerging']*100/$total,1);
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Foundation']*100/$total,1);
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
            $kq_text.='<tr><td><div>'.($i).'. '.$arr['key_question_text'].'</div></td></tr>';   
            $i++;
            }
                $this->aqsData['tot_students']=isset($total)?$total:0;
                $i=1;
                $html .= '<tr style="text-align:center;" style="border:solid 1px #000000;"><td  width="16%" style="border:solid 1px #000000;">Overall</td>';
                                                                                                       
                foreach($array_final['Exceptional'] as $key=>$val){
                $html .= '<td style="border:solid 1px #000000;text-align:left;" width="28%" ><b>'.$key.': '.$val['heading'].'</b></td>';
                
                $i++;
                }
                $html .= '</tr>';
                
                $i=1;
                foreach($array_final as $key=>$val){
                        									
					$html .= '<tr style="border:solid 1px #000000;">
                                                <td style="border:solid 1px #000000;" class="scheme-2-score-'.$array_final_class[$key].'" width="16%">'.$key.'</td>';
                                                foreach($val as $key_1=>$val_1){
                                                 $html .= '<td width="28%" style="text-align:center;border:solid 1px #000000;">'.$val_1['rating'].'%</td>';
                                                }                                                        
                                                $html .= '</tr>';
                                                $i++;
					
                }
                
            $kq_text.='</table>';
            $html.='</table>'.$kq_text.'';
            /*echo"<pre>";
            print_r($this->SQRatingsArrCentre_batchesfinal);
            echo"</pre>";
            */
                if($this->reportId==12){                                                                                            
                $html .= '<br><div>The following table represents the performance of participants in each centre at the key question level based on the external validation. The centres are organized in the order of performance.
                </div>';
                } else{ 
                $html .= '<br><div>The following table represents the performance of participants in each batch at the key question level based on the external validation. The batches are organized in the order of performance.
                </div>';    
                }
                $html .= '<table cellspacing="0" cellpadding="4" border="0"  width="100%"><thead>
			  <tr><td colspan="16" align="center" width="100%">Table '.++$this->tableNum.'</td></tr>';
                $i=1;
                $html .= '<tr style="text-align:center;" style="border:solid 1px #000000;"><td  width="17.5%" style="border:solid 1px #000000;">Overall (%)</td>';
                                                                                                       
                foreach($array_final['Exceptional'] as $key=>$val){
                $html .= '<td style="border:solid 1px #000000;text-align:left;" width="27.5%" colspan="5"><b>'.$key.': '.$val['heading'].'</b></td>';
                
                $i++;
                }
                $html .= '</tr></thead><tbody>';
                $i=1;
                $order=array();
                /*echo"<pre>";
                print_r($this->SQRatingsArrCentre_batchesfinal);
                echo"</pre>";*/
                foreach($this->SQRatingsArrCentre_batchesfinal as $key=>$val){
                    $order[$key]=$val['Key Question 1']['Exceptional']+$val['Key Question 2']['Exceptional']+$val['Key Question 3']['Exceptional'];
                    
                }
                //$order['Govandi'] = 2;
                //$order['Chandigarh'] = 1;
                $this->aksort($order,true);
                
                //print_r($order);
                $this->SQRatingsArrCentre_batchesfinal=$sorted=$this->sortArrayByArray($this->SQRatingsArrCentre_batchesfinal,$order);
                /*echo"<pre>";
                print_r( $this->SQRatingsArrCentre_batchesfinal);
                echo"</pre>";*/
                foreach($this->SQRatingsArrCentre_batchesfinal as $key=>$val){
                                               $length=strlen($key);
                                               $font_size="";
                                               
                                               if($length>12 && $length<=14){
                                               $font_size="font-size:12px;";
                                               }else if($length>14){
                                               $font_size="font-size:11px;";   
                                               }
                                               
                                               $html .= '<tr style="border:solid 1px #000000;" nobr="true">
                                                <td style="border:solid 1px #000000;'.$font_size.'"  width="17.5%">'.$key.'</td>';
                                                foreach($val as $key_1=>$val_1){
                                                 $tot=array_sum($val_1); 
                                                 //print_r($val_1);
                                                 foreach($val_1 as $key_2=>$val_2){   
                                                 $html .= '<td width="5.5%" style="text-align:center;border:solid 1px #000000;" class="scheme-2-score-'.$array_final_class[$key_2].'">'.round($val_2*100/$tot,1).'</td>';
                                                 }
                                                 
                                                }                                                        
                                                $html .= '</tr>';
                                                
                                                $i++;
                }
                
                $this->aqsData['tot_batches']=$i-1;
                $html.='</tbody></table>';
            
            return $html;
        }
        
        
        protected function getStudentCentreKQRatings_r2(){
            if($this->reportId==12){
            $html = '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>The following represents the comparative performance of all participants within the given time frame at '.$this->aqsData['network_name'].' on the level of each key question, based on the external validation .<br/>';    
            }else{
            $html = '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>The following represents the comparative performance of all participants within the given time frame at '.$this->aqsData['province_name'].', '.$this->aqsData['network_name'].' on the level of each key question, based on the external validation.<br/>';
            }
            $kq_text='<table width="100%" style="padding:5px;"><tr><td width="100%"><b>Key questions :</b></td></tr>';
            $i=1;
            $array_final_class=array("Exceptional"=>5,"Proficient"=>4,"Developing"=>3,"Emerging"=>2,"Foundation"=>1);
            /*$html .= '<table cellspacing="0" cellpadding="4" border="0" nobr="true">
			  <tr><td colspan="4" align="center">Table '.++$this->tableNum.'</td></tr>';
            */
           
            $sqnum=0;$sqnum1=0;
            $array_final=array();
            
            foreach($this->SQRatingsArrCentre_final as $key=>$arr){
            //print_r($arr);
            $total=array_sum($arr['grades']);
                                        $array_final['Exceptional']['Key Question '.(++$sqnum).'']['rating']=round($arr['grades']['Exceptional']*100/$total,1);
                                        $array_final['Exceptional']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final['Exceptional']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Proficient']*100/$total,1);
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final['Proficient']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final['Developing']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Developing']*100/$total,1);
                                        $array_final['Developing']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final['Developing']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Emerging']*100/$total,1);
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final['Emerging']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Foundation']*100/$total,1);
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final['Foundation']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
            $kq_text.='<tr><td><div>'.($i).'. '.$arr['key_question_text'].'</div></td></tr>';   
            $i++;
            }
            
            $sqnum=0;$sqnum1=0;
            $array_final_r1=array();
            $i=1;
            foreach($this->SQRatingsArrCentre_final_r1 as $key=>$arr){
            //print_r($arr);
                                        $total=array_sum($arr['grades']);
                                        $array_final_r1['Exceptional']['Key Question '.(++$sqnum).'']['rating']=round($arr['grades']['Exceptional']*100/$total,1);
                                        $array_final_r1['Exceptional']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final_r1['Exceptional']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final_r1['Proficient']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Proficient']*100/$total,1);
                                        $array_final_r1['Proficient']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final_r1['Proficient']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final_r1['Developing']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Developing']*100/$total,1);
                                        $array_final_r1['Developing']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final_r1['Developing']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final_r1['Emerging']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Emerging']*100/$total,1);
                                        $array_final_r1['Emerging']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final_r1['Emerging']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
                                        $array_final_r1['Foundation']['Key Question '.($sqnum).'']['rating']=round($arr['grades']['Foundation']*100/$total,1);
                                        $array_final_r1['Foundation']['Key Question '.($sqnum).'']['text']=$arr['key_question_text'];
                                        $array_final_r1['Foundation']['Key Question '.($sqnum).'']['heading']=$arr['key_question_heading'];
                                        
             
            $i++;
            }
                $this->aqsData['tot_students']=isset($total)?$total:0;
                /*$i=1;
                $html .= '<tr style="text-align:center;" style="border:solid 1px #000000;"><td  width="16%" style="border:solid 1px #000000;">Overall</td>';
                                                                                                       
                foreach($array_final['Exceptional'] as $key=>$val){
                $html .= '<td style="border:solid 1px #000000;text-align:left;" width="28%" ><b>'.$key.': '.$val['heading'].'</b></td>';
                
                $i++;
                }
                $html .= '</tr>';
                
                $html .= '<tr style="text-align:center;" style="border:solid 1px #000000;"><td  width="16%" style="border:solid 1px #000000;">&nbsp;</td>';
                foreach($array_final['Exceptional'] as $key=>$val){
                $html .= '<td style="border:solid 1px #000000;text-align:center;" width="14%" >Baseline</td><td style="border:solid 1px #000000;text-align:center;" width="14%" >Endline</td>';
                
                $i++;
                }
                $html .= '</tr>';
                
                $i=1;
                foreach($array_final as $key=>$val){
                        									
					$html .= '<tr style="border:solid 1px #000000;">
                                                <td style="border:solid 1px #000000;" class="scheme-2-score-'.$array_final_class[$key].'" width="16%">'.$key.' </td>';
                                                foreach($val as $key_1=>$val_1){
                                                 //$array_final[$key][$key_1]['rating'];
                                                 $html .= '<td width="14%" style="text-align:center;border:solid 1px #000000;">'.$array_final_r1[$key][$key_1]['rating'].'%</td>';
   
                                                 $html .= '<td width="14%" style="text-align:center;border:solid 1px #000000;">'.$val_1['rating'].'%</td>';
                                                }                                                        
                                                $html .= '</tr>';
                                                $i++;
					
                }
                
            $kq_text.='</table>';
            $html.='</table>'.$kq_text.'';
            */
                
                $i=1;
                foreach($array_final as $key=>$val){
                                        
					/*$html .= '<tr style="border:solid 1px #000000;">
                                                <td style="border:solid 1px #000000;" class="scheme-2-score-'.$array_final_class[$key].'" width="16%">'.$key.'</td>';
                                                */foreach($val as $key_1=>$val_1){
                                                 $key_g= str_replace(" ", "", $key_1);
                                                 if(isset($$key_g)){
                                                 $$key_g.="".$key."=".$array_final_r1[$key][$key_1]['rating']."~".$val_1['rating']."&"; 
                                                    
                                                 }else{
                                                 $$key_g="".$key."=".$array_final_r1[$key][$key_1]['rating']."~".$val_1['rating']."&"; 
                                                 }
                                                 //$html .= '<td width="14%" style="text-align:center;border:solid 1px #000000;">'.$array_final_r1[$key][$key_1]['rating'].'%</td>';   
                                                 //$html .= '<td width="14%" style="text-align:center;border:solid 1px #000000;">'.$val_1['rating'].'%</td>';
                                                }                                                        
                                                //$html .= '</tr>';
                                                $i++;
					
                }
                $graph=1;
                $i=1;
                $html .= '<br><b>Key questions:</b><br><table cellspacing="0" cellpadding="4" border="0">';
                foreach($array_final['Exceptional'] as $key=>$val){
                $key_g= str_replace(" ", "", $key);
                $$key_g.='head_show='.urlencode($key).':'.urlencode($val['heading']).'';
                $url=$$key_g;
                //echo $$key_g;
                //echo"<br>";
                $html.='<tr nobr="true"><td><div>'.($i).'. '.$val['text'].'</div><div style="text-align:center;"><b>'.$key.':'.$val['heading'].'</b></div><div style="text-align:center;">graph '.$graph++.'</div>';
                $html .= '<div  style="text-align:center;"><img src="' . SITEURL . 'library/student_bar_graph.php?'.$url.'"></div></td></tr>';
                $i++;
                }
                $html .='</table>';
            
            
            /*echo"<pre>";
            print_r($this->SQRatingsArrCentre_batchesfinal);
            echo"</pre>";
            */
                if($this->reportId==12){                                                                                            
                $html_head = '<br><div>The following table represents the comparative performance of participants in each centre at the key question level based on the external validation. The centres are organized in the order of performance.
                </div>';
                } else{ 
                $html_head = '<br><div>The following table represents the comparative performance of participants in each batch at the key question level based on the external validation. The batches are organized in the order of performance on the endline.';
                
                $html_head .= '</div>';
                
                }
                
                
                $html .= '<table cellspacing="0" cellpadding="4" border="0"  width="100%">
                          <tr><td colspan="16" align="left" width="100%">'.$html_head.'</td></tr>
                          <thead>
                          <tr><td colspan="16" align="center" width="100%">Table '.++$this->tableNum.'</td></tr>';
                $i=1;
                $html .= '<tr style="text-align:center;" style="border:solid 1px #000000;"><td  width="12.5%" style="border:solid 1px #000000;">Overall (%)</td><td width="5%" style="border:solid 1px #000000;">R#</td>';
                                                                                                       
                foreach($array_final['Exceptional'] as $key=>$val){
                $html .= '<td style="border:solid 1px #000000;text-align:left;" width="27.5%" colspan="5"><b>'.$key.': '.$val['heading'].'</b></td>';
                
                $i++;
                }
                $html .= '</tr></thead><tbody>';
                $i=1;
                $order=array();
                /*echo"<pre>";
                print_r($this->SQRatingsArrCentre_batchesfinal);
                echo"</pre>";*/
                foreach($this->SQRatingsArrCentre_batchesfinal as $key=>$val){
                    $order[$key]=$val['Key Question 1']['Exceptional']+$val['Key Question 2']['Exceptional']+$val['Key Question 3']['Exceptional'];
                    
                }
                //$order['Govandi'] = 2;
                //$order['Chandigarh'] = 1;
                $this->aksort($order,true);
                
                //print_r($order);
                $this->SQRatingsArrCentre_batchesfinal=$sorted=$this->sortArrayByArray($this->SQRatingsArrCentre_batchesfinal,$order);
                /*echo"<pre>";
                print_r( $this->SQRatingsArrCentre_batchesfinal);
                print_r( $this->SQRatingsArrCentre_batchesfinal_r1);
                echo"</pre>";*/
                foreach($this->SQRatingsArrCentre_batchesfinal as $key=>$val){
                                               $length=strlen(''.$key.'');
                                               $font_size="";
                                               
                                               if($length>8 && $length<=10){
                                               $font_size="font-size:12px;";
                                               }else if($length>10){
                                               $font_size="font-size:11px;";   
                                               }
                                               
                                                $html .= '<tr style="border:solid 1px #000000;" nobr="true">
                                                <td style="border:solid 1px #000000;'.$font_size.'"  width="12.5%" rowspan="2" >'.$key.'</td><td width="5%" style="border:solid 1px #000000;">R1</td>';
                                                if(isset($this->SQRatingsArrCentre_batchesfinal_r1[$key]) && count($this->SQRatingsArrCentre_batchesfinal_r1[$key])>0){
                                                foreach($this->SQRatingsArrCentre_batchesfinal_r1[$key] as $key_1=>$val_1){
                                                 $tot=array_sum($val_1); 
                                                 //print_r($val_1);
                                                 foreach($val_1 as $key_2=>$val_2){   
                                                 $html .= '<td width="5.5%" style="text-align:center;border:solid 1px #000000;" class="scheme-2-score-'.$array_final_class[$key_2].'">'.round($val_2*100/$tot,1).'</td>';
                                                 }
                                                 
                                                } 
                                                }
                                                $html .= '</tr>';
                                               
                                               $html .= '<tr style="border:solid 1px #000000;" nobr="true"><td width="5%" style="border:solid 1px #000000;">R2</td>';
                                                foreach($val as $key_1=>$val_1){
                                                 $tot=array_sum($val_1); 
                                                 //print_r($val_1);
                                                 foreach($val_1 as $key_2=>$val_2){   
                                                 $html .= '<td width="5.5%" style="text-align:center;border:solid 1px #000000;" class="scheme-2-score-'.$array_final_class[$key_2].'">'.round($val_2*100/$tot,1).'</td>';
                                                 }
                                                 
                                                }                                                        
                                                $html .= '</tr>';
                                                
                                                $i++;
                }
                
                $this->aqsData['tot_batches']=$i-1;
                $html.='</tbody></table>';
            
            return $html;
        }
        
        function aksort(&$array, $valrev = false, $keyrev = false) {
        if ($valrev) {
            arsort($array);
        } else {
            asort($array);
        }
        $vals = array_count_values($array);
        $i = 0;
        foreach ($vals AS $val => $num) {
            $first = array_splice($array, 0, $i);
            $tmp = array_splice($array, 0, $num);
            if ($keyrev) {
                krsort($tmp);
            } else {
                ksort($tmp);
            }
            $array = array_merge($first, $tmp, $array);
            unset($tmp);
            $i = $num;
        }
    }
    
    function sortArrayByArray($array, $orderArray) {
        $ordered = array();
        foreach ($orderArray as $key => $value) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        return $ordered + $array;
    }

    protected function getLimitationsFirstUse_Diagnostic(){
		$html = 'It is not always possible for teachers to \'know\' what good looks like before they undergo the first review.  The differences in judgment between self-review and external review on the diagnostic statements could be attributed to:';
		 $html .= '<ul>
					<li>an inaccurate interpretation of the statement ("oh, we had read it to mean something else")</li> 
					<li>partial reading of the statement ("we realise now that every word in the statement counts")</li> 
					<li>making a judgement with reference to the teacher\'s current practice, wherein the practice itself did not include all aspects of the statement in the diagnostic ("we have not documented it, but&#8230;") </li> 
					<li>making a judgement with reference to other teachers in the school or in neighbourhood, rather than against the statement in the diagnostic ("other teachers don\'t even&#8230;") </li> 
					<li>school did not have a good and secure system to support evidence collection for all teachers that helped them in making accurate judgement about their performance.  This is demonstrated by any one or many of the following situation(s):
		 				<ul>
		 					<li>Feedback from the stakeholders(parent, student, peers) is absent or inadequate</li>
		 					<li>Evidence of self-review is absent or inadequate</li>
		 					<li>Validation by the Head of the department, school leadership is absent or inadequate</li>
		 					<li>The evidence or feedback is not pertaining to the current academic year under study</li>
		 				</ul>
		 			</li>
					<li>leaders did not share the purpose of teacher performance review and its importance in teacher\'s professional development journey </li>
					<li>teachers conducted the review to merely follow their leader\'s instructions and were not self-motivated to do so as they concluded this exercise as a threat to their job security </li>				
			    </ul>';
		$html .= '<b>Recommendations:</b>
					<p>All teachers benefit from a regular use of the Self-Review Diagnostic throughout their development journey, taking steps to engage with resources and resource people to increasing their knowledge of What Good Looks Like. If the teacher wishes, it will be possible for Adhyayan to arrange visits to &#39;Good&#39; schools within the Adhyayan network to experiences aspects of good practice in teaching and learning.</p>';
		 
		return $html;
	}
        
        protected function getLimitationsStuFirstUse_Diagnostic(){
		$html = 'It is not always possible for young adults to \'know\' what good looks like before they undergo the first review.  The differences in judgment between self-review and external review on the diagnostic statements could be attributed to:';
		 $html .= '<ul>
					<li>an inaccurate interpretation of the statement ("oh, we had read it to mean something else")</li> 
					<li>partial reading of the statement ("we realise now that every word in the statement counts")</li> 
					<li>not understanding the difference between impact and provision ("we have taken a test to map interests but don\'t understand how it relates to building a career…") </li> 
					 
					<li>providing high ratings on the basis of ineffective evidence that does not demonstrate consistent practice/behaviour</li>
					
					<li>the purpose of the review and its value to the participants was not clearly explained </li>				
			    </ul>';
		$html .= '<b>Recommendations:</b>
					<p>All young adults benefit from a regular use of the Career Readiness Diagnostic throughout their development journey in increasing their knowledge of What Good Looks Like and understanding what next steps to take to build a career. If an organization chooses, Antarang and Adhyayan are willing to expose members from your organization to best practice in the field.</p>';
		 
		return $html;
	}
        
	final protected function getTchrRank(){		
		$html = '<table cellspacing="0" cellpadding="4" border="0" nobr="true">
		<thead>
		<tr><td colspan="2" align="center">Table '.++$this->tableNum.'</td></tr>
		<tr style="border:solid 1px #000000;"><td style="width:10%;border:solid 1px #000000;"><b>Rank</b></td><td style="width:90%;border:solid 1px #000000;"><b>Teacher\'s name</b></td></tr></thead><tbody>';
		$k=1;
		$rank = 1;
		$awardPrev = 0;
		arsort($this->rankArray,SORT_NUMERIC);
		foreach($this->rankArray as $key=>$val)
		{
			if($k>1 && $awardPrev != $val)
				$rank++;
					
				$html .= '<tr style="border:solid 1px #000000;"><td style="width:10%;border:solid 1px #000000;">'.$rank.'</td><td style="width:90%;border:solid 1px #000000;">'.$key.'</td></tr>';
				$awardPrev = $val;
				$k++;
		}
		$html .= '</tbody></table>';
		return $html;
	}
	final protected function createURISqJdTeachers_AccuracySelfReview(){
		//format is ?sq1=title;jd0;jd1;jd2;jd3&sq2=title;jd0;jd1;jd2;jd3
		//jd 0 means number of judgement statemnts in the sub question  with judgement distance 0
		//jd 1 means number of judgement statemnts in the sub question  with judgement distance 1
		//same goes for the rest
		$url ='?';	
		$rankArray = array();		
		$i=0;		
		foreach($this->assessments as $a){
			$i++;
			if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1 && isset($this->kpas[$a['assessment_id']])){
				$jd0 = 0;$jd1 = 0;$jd2 = 0;$jd3 = 0;
				foreach($this->kpas[$a['assessment_id']] as $kpa){
						if(isset($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']])){
						foreach($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']] as $kq){	
							if(isset($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']])){														
								foreach($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']] as $cq){																		
										foreach($this->judgementStatement[$a['assessment_id']][$cq['core_question_instance_id']] as $statment){
											$jd = abs($statment['internalRating']['score'] - $statment['externalRating']['score']);
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
										}										
									}
								}
				
							}
						}
					}
				$weightage = $jd0*4 +$jd1*3 +$jd2*2 +$jd*1;				
				$rankArray[$a['data_by_role']['3']['user_name']] = $weightage;				
				$url .= $i.'='.urlencode($a['data_by_role']['3']['user_name']).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
			}
		}
		//echo $url;die;		
		arsort($rankArray,SORT_NUMERIC);
		$this->rankArray = $rankArray;
		//print_r($this->rankArray);die;
		return $url;
	}
	protected function generateTchrs_PerfReview(){
		$html = '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>The following table represents performance of individual teacher on every sub-

question based on the external review ratings. This will help the school leaders to identify the strengths as well as areas of professional development for every teacher. It will aid the school to grow its champion teachers, create buddy systems and organise need based training programmes thereby developing a culture of continuous professional development.<br/>'.
		'<table border="0" cellpadding="4" nobr="true">
				<thead>
				<tr style="border:solid 1px #ffffff;"><td colspan="4" align="center">Table '.($this->tableNum+4).'</td></tr>
				<tr style="font-weight:bold;border:solid 1px #000000;"><td style="border:solid 1px #000000;" width="28%">Teacher Name</td>';
		$row1 = '<td width="8%"  style="text-align:center;border:solid 1px #000000;">SQ1</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ2</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ3</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ4</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ5</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ6</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ7</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ8</td><td  width="8%" style="text-align:center;border:solid 1px #000000;">SQ9</td></tr></thead>';
		$rows = '<tbody>';
		$i=1;
		$overallArray = null;
		foreach($this->assessments as $a){								
			if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1 && isset($this->kpas[$a['assessment_id']])){
				$rows .= '<tr><td width="28%" style="border:solid 1px #000000;">'.$a['data_by_role']['3']['user_name'].'</td>';
				foreach($this->kpas[$a['assessment_id']] as $kpa){
					if(isset($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']])){
						$k=0;
						foreach($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']] as $kq){
							$k++;
							$overallArray['Key Question '.$k]['text'] = $kq['key_question_text'];
							//$html .= '<table><tr><td rowspan="2"><b>Overall</b></td><td>'.$kq['key_question_text'].'</td></tr>';							
							if(isset($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']])){	
								$j=0;
								foreach($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']] as $cq){		
									$j++;
                                                                       /* $overallArray['Key Question '.$k]['Sub Question '.$j]['score1']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score2']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score3']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score4']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score5']=0;*/
									//core_question_stmt
									$overallArray['Key Question '.$k]['Sub Question '.$j]['text_stmt'] = $cq['core_question_stmt'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['text'] = $cq['core_question_text'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['key_question_instance_id'] =  $kq['key_question_instance_id'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['score'.$cq['externalRating']['score']]++;
									//$html .= '<td>SQ'.$i.'</td>';
									$rows .='<td width="8%" style="border:solid 1px #000000;" class="scheme-2-score-'.$cq['externalRating']['score'].'"></td>';
									$i++;
								}								
							}
						}
					}
					$rows .= '</tr>';
				}
						
			}			
		}
		
		$html .= $row1.$rows.'</tbody></table>';		
		$this->SQRatingsArr = $overallArray; 
		return $html;
	}
        
        protected function generateStu_PerfReview(){
		$html = '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>The following table represents the comparative performance of each participant across all sub questions.<br/>'.
		'<table width="100%" cellpadding="4" style="margin-top:10px;">
				<thead>
				<tr style="border:solid 1px #ffffff;"><td colspan="12" align="center">Table '.($this->tableNum+5).'</td></tr>
				<tr style="font-weight:bold;border:solid 1px #000000;"><td style="border:solid 1px #000000;" width="20%">Participant Name</td><td  style="border:solid 1px #000000;text-align:center;" width="16%">Award</td><td style="border:solid 1px #000000;text-align:center;" width="10%">Round</td>';
		$row1 = '<td width="6%"  style="text-align:center;border:solid 1px #000000;">SQ1</td><td width="6%" style="text-align:center;border:solid 1px #000000;">SQ2</td><td width="6%" style="text-align:center;border:solid 1px #000000;">SQ3</td><td width="6%" style="text-align:center;border:solid 1px #000000;">SQ4</td><td width="6%" style="text-align:center;border:solid 1px #000000;">SQ5</td><td width="6%" style="text-align:center;border:solid 1px #000000;">SQ6</td><td width="6%" style="text-align:center;border:solid 1px #000000;">SQ7</td><td width="6%" style="text-align:center;border:solid 1px #000000;">SQ8</td><td  width="6%" style="text-align:center;border:solid 1px #000000;">SQ9</td></tr></thead>';
		$rows = '<tbody>';
		$i=1;
		$overallArray = null;
                $overallArray_r1 = null;
                $name="";
                $jj=0;
                $array_name=array();
                foreach($this->assessments_r1 as $key=>$a){
                    if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1){
                    $array_name[$a['data_by_role']['3']['user_id']][]=$a['assessment_id'];   
                    }
                    
                }
                
		foreach($this->assessments_r1 as $key=>$a){
                        //echo $key; 
                        if($a['student_round']==1){
                         $this->kpas_f=$this->kpas_r1;
                         $this->keyQuestions_f=$this->keyQuestions_r1;
                         $this->coreQuestions_f=$this->coreQuestions_r1;
                         $line="Baseline";
                        }else{
                         $this->kpas_f=$this->kpas;
                         $this->keyQuestions_f=$this->keyQuestions;
                         $this->coreQuestions_f=$this->coreQuestions;
                         $line="Endline";
                        }
                        //echo"<pre>";
                        //print_r($this->kpas_f[$a['assessment_id']]);
                        //echo"</pre>";
                        //die();
			if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1 && isset($this->kpas_f[$a['assessment_id']])){
				$rows .= '<tr nobr="true">';
                                
                                if(count($array_name[$a['data_by_role']['3']['user_id']])>1 && $jj==0){
                                $rows.= '<td width="20%" style="border:solid 1px #000000;" rowspan="2">'.$a['data_by_role']['3']['user_name'].'</td>';
                                $jj=1;
                                }else{
                                $jj=0;
                                }
                                
                                if(count($array_name[$a['data_by_role']['3']['user_id']])==1 && $jj==0){
                                $rows.= '<td width="20%" style="border:solid 1px #000000;">'.$a['data_by_role']['3']['user_name'].'</td>';
                                 $jj=0;
                                }
                                
                                $name=$a['data_by_role']['3']['user_name'];
				                                foreach($this->kpas_f[$a['assessment_id']] as $kpa){
                                                                  $rows.='<td  width="16%" style="text-align:center;border:solid 1px #000000;" class="scheme-2-fontcolor-score-'.$kpa['externalRating']['score'].'">'.$kpa['externalRating']['rating'].'</td><td width="10%" style="border:solid 1px #000000;">Round '.$a['student_round'].'</td>';  
					if(isset($this->keyQuestions_f[$a['assessment_id']][$kpa['kpa_instance_id']])){
						$k=0;
						foreach($this->keyQuestions_f[$a['assessment_id']][$kpa['kpa_instance_id']] as $kq){
							$k++;
                                                        if($a['student_round']==1){
                                                        $overallArray_r1['Key Question '.$k]['text'] = $kq['key_question_text'];
                                                        $overallArray_r1['Key Question '.$k]['key_heading'] = $kq['key_heading'];
                                                        $overallArray_r1['Key Question '.$k]['score'.$kq['externalRating']['score']]++;    
                                                        }else{
							$overallArray['Key Question '.$k]['text'] = $kq['key_question_text'];
                                                        $overallArray['Key Question '.$k]['key_heading'] = $kq['key_heading'];
                                                        $overallArray['Key Question '.$k]['score'.$kq['externalRating']['score']]++;
                                                        }
							//$html .= '<table><tr><td rowspan="2"><b>Overall</b></td><td>'.$kq['key_question_text'].'</td></tr>';							
							if(isset($this->coreQuestions_f[$a['assessment_id']][$kq['key_question_instance_id']])){	
								$j=0;
								foreach($this->coreQuestions_f[$a['assessment_id']][$kq['key_question_instance_id']] as $cq){		
									$j++;
                                                                       /* $overallArray['Key Question '.$k]['Sub Question '.$j]['score1']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score2']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score3']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score4']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score5']=0;*/
									//core_question_stmt
									if($a['student_round']==1){
                                                                        $overallArray_r1['Key Question '.$k]['Sub Question '.$j]['text_stmt'] = $cq['core_question_stmt'];
									$overallArray_r1['Key Question '.$k]['Sub Question '.$j]['text'] = $cq['core_question_text'];
									$overallArray_r1['Key Question '.$k]['Sub Question '.$j]['key_question_instance_id'] =  $kq['key_question_instance_id'];
									$overallArray_r1['Key Question '.$k]['Sub Question '.$j]['score'.$cq['externalRating']['score']]++;
                                                                           
                                                                        }else{
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['text_stmt'] = $cq['core_question_stmt'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['text'] = $cq['core_question_text'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['key_question_instance_id'] =  $kq['key_question_instance_id'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['score'.$cq['externalRating']['score']]++;
                                                                        }
									//$html .= '<td>SQ'.$i.'</td>';
									$rows .='<td width="6%" style="border:solid 1px #000000;" class="scheme-2-score-'.$cq['externalRating']['score'].'"></td>';
                                                                        $i++;
								}								
							}
						}
					}
					$rows .= '</tr>';
                                        
                                        //$jj++;
                                        
				}
						
			}			
		}
		//$rows=''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.'';
		$html .= $row1.$rows.'<tr><td colspan="12"><br><br><table width="100%"><tr><td class="scheme-2-score-5" align="center">Exceptional</td><td class="scheme-2-score-4"  align="center">Proficient</td><td class="scheme-2-score-3"  align="center">Developing</td><td class="scheme-2-score-2"  align="center">Emerging</td><td class="scheme-2-score-1"  align="center">Foundation</td></tr></table></td></tr></tbody></table>';		
		$this->SQRatingsArr = $overallArray;
                $this->SQRatingsArr_r1 = $overallArray_r1;
                //echo"<pre>";
                //print_r($overallArray);
                //print_r($overallArray_r1);
                //echo"</pre>";
		return $html;
	}
        
        
        protected function generateStu_PerfReview_r1(){
		$html = '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>The following table represents the performance of each participant across all sub questions. This will enable the facilitators to identify the areas and participants where attention needs to be directed.<br/>'.
		'<table border="0" cellpadding="4" >
				<thead>
				<tr style="border:solid 1px #ffffff;"><td colspan="10" align="center">Table '.($this->tableNum+5).'</td></tr>
				<tr style="font-weight:bold;border:solid 1px #000000;"><td style="border:solid 1px #000000;" width="28%">Participant Name</td>';
		$row1 = '<td width="8%"  style="text-align:center;border:solid 1px #000000;">SQ1</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ2</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ3</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ4</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ5</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ6</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ7</td><td width="8%" style="text-align:center;border:solid 1px #000000;">SQ8</td><td  width="8%" style="text-align:center;border:solid 1px #000000;">SQ9</td></tr></thead>';
		$rows = '<tbody>';
		$i=1;
		$overallArray = null;
                
		foreach($this->assessments as $key=>$a){
                        
                    
			if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1 && isset($this->kpas[$a['assessment_id']])){
				$rows .= '<tr nobr="true"><td width="28%" style="border:solid 1px #000000;">'.$a['data_by_role']['3']['user_name'].'</td>';
				                                foreach($this->kpas[$a['assessment_id']] as $kpa){
					if(isset($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']])){
						$k=0;
						foreach($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']] as $kq){
							$k++;
							$overallArray['Key Question '.$k]['text'] = $kq['key_question_text'];
                                                        $overallArray['Key Question '.$k]['key_heading'] = $kq['key_heading'];
                                                        $overallArray['Key Question '.$k]['score'.$kq['externalRating']['score']]++;
							//$html .= '<table><tr><td rowspan="2"><b>Overall</b></td><td>'.$kq['key_question_text'].'</td></tr>';							
							if(isset($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']])){	
								$j=0;
								foreach($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']] as $cq){		
									$j++;
                                                                       /* $overallArray['Key Question '.$k]['Sub Question '.$j]['score1']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score2']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score3']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score4']=0;
                                                                        $overallArray['Key Question '.$k]['Sub Question '.$j]['score5']=0;*/
									//core_question_stmt
									$overallArray['Key Question '.$k]['Sub Question '.$j]['text_stmt'] = $cq['core_question_stmt'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['text'] = $cq['core_question_text'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['key_question_instance_id'] =  $kq['key_question_instance_id'];
									$overallArray['Key Question '.$k]['Sub Question '.$j]['score'.$cq['externalRating']['score']]++;
									//$html .= '<td>SQ'.$i.'</td>';
									$rows .='<td width="8%" style="border:solid 1px #000000;" class="scheme-2-score-'.$cq['externalRating']['score'].'"></td>';
                                                                        $i++;
								}								
							}
						}
					}
					$rows .= '</tr>';
                                        
				}
						
			}			
		}
		//$rows=''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.''.$rows.'';
		$html .= $row1.$rows.'<tr><td colspan="10"><br><br><table width="100%"><tr><td class="scheme-2-score-5" align="center">Exceptional</td><td class="scheme-2-score-4"  align="center">Proficient</td><td class="scheme-2-score-3"  align="center">Developing</td><td class="scheme-2-score-2"  align="center">Emerging</td><td class="scheme-2-score-1"  align="center">Foundation</td></tr></table></td></tr></tbody></table>';		
		$this->SQRatingsArr = $overallArray;
                //echo"<pre>";
                //print_r($overallArray);
                //echo"</pre>";
		return $html;
	}
	
	protected function generateTeacherOutput(){
		$this->loadAqsData();
		$this->loadJudgementalStatements();
		$this->loadCoreQuestions();
		$this->loadKeyQuestions();
		$this->loadKpas();
		
		$this->config['reportTitle']="Teacher Review Report Card";
		$this->config["footerText"]="Adhyayan Teacher Review report for {schoolName}, {dateToday} (generated on)";
	
		$this->sectionArray=array();
		$this->indexArray=array();
		
		$this->generateSection_TchrAss();	
		$this->generateSection_TchrRecomm();
		$this->generateIndexAndCover();
		$output=array(
				"config"=>$this->config,
				"data"=>$this->sectionArray
			);
		return $output;
	}
	
	protected function loadAqsData(){
		if(empty($this->aqsData)){
			$sql="
				select school_name,principal_name,school_address,principal_phone_no,date(c.publishDate) as publishDate,date(valid_until) as valid_until,a.school_aqs_pref_start_date,a.school_aqs_pref_end_date,b.client_id
				from d_AQS_data a
				inner join (select aa.*,ag.group_assessment_id from d_assessment aa inner join h_assessment_ass_group ag on aa.assessment_id=ag.assessment_id and ag.group_assessment_id=? limit 1)  b on b.aqsdata_id=a.id
				left join h_group_assessment_report c on b.group_assessment_id = c.group_assessment_id and c.report_id= $this->reportId
				group by a.id;";
			$this->aqsData=$this->db->get_row($sql,array($this->groupAssessmentId));
                        
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
                        
			$this->conductedDate=empty($this->aqsData['publishDate'])?$this->conductedDate:implode("-",array_reverse(explode("-",substr($this->aqsData['publishDate'],0,7))));
			$this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:implode("-",array_reverse(explode("-",substr($this->aqsData['valid_until'],0,7))));
		}
	}	
	
         protected function loadAqsDataStudent(){
		if(empty($this->aqsData)){
			$sql="
				select dc.client_name as school_name,dg.student_round,dc.street as school_address,'' as region_name, ctr.country_name,st.state_name,cty.city_name,b.award_scheme_id,date(c.publishDate) as publishDate,date(valid_until) as valid_until,b.tier_id,b.client_id,p.province_name,n.network_name
				from (select aa.*,ag.group_assessment_id from d_assessment aa inner join h_assessment_ass_group ag on aa.assessment_id=ag.assessment_id and ag.group_assessment_id=? limit 1) b
				inner join d_client dc on dc.client_id = b.client_id
                                inner join d_group_assessment dg on b.group_assessment_id = dg.group_assessment_id
				left join d_countries ctr on ctr.country_id = dc.country_id
				left join d_states st on dc.state_id = st.state_id and st.country_id = dc.country_id
				left join d_cities cty on cty.city_id = dc.city_id and cty.state_id = dc.state_id
                                left join h_client_province hcp on hcp.client_id=b.client_id
                                left join d_province p on p.province_id=hcp.province_id
                                left join h_province_network hpn on hpn.province_id=hcp.province_id
                                left join d_network n on n.network_id=hpn.network_id
				left join h_group_assessment_report c on b.group_assessment_id = c.group_assessment_id and c.report_id= $this->reportId";
                        
			$this->aqsData=$this->db->get_row($sql,array($this->groupAssessmentId));
			
                        //$this->conductedDate=(empty($this->aqsData['school_aqs_pref_end_date']) || $this->aqsData['school_aqs_pref_end_date']=="0000-00-00")?$this->conductedDate:implode("-",array_reverse(explode("-",substr($this->aqsData['school_aqs_pref_end_date'],0,7))));
			//$this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:implode("-",array_reverse(explode("-",substr($this->aqsData['valid_until'],0,7))));
			
                        $this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:date("M-Y",strtotime($this->aqsData['valid_until']));
                        $this->conductedDate=empty($this->aqsData['publishDate'])?$this->conductedDate:implode("-",array_reverse(explode("-",substr($this->aqsData['publishDate'],0,7))));
			$this->schoolLocation = $this->aqsData['region_name'];
			$this->schoolCity = $this->aqsData['city_name'];
			$this->schoolState = $this->aqsData['state_name'];
			$this->schoolCountry = $this->aqsData['country_name'];
                        $this->province = $this->aqsData['province_name'];
                        $this->network = $this->aqsData['network_name'];
                        //$this->round = $this->aqsData['student_round'];
		}
	}
        
        protected function loadAqsDataStudentCentre(){
		if(empty($this->aqsData)){
                        if(gettype($this->centre_id)=="array" && count($this->centre_id)>0){
                           $sql="select dc.client_name as school_name,p.province_name,n.network_name,cty.city_name, ctr.country_name,st.state_name from 
                              (select * from d_province where province_id IN (".(implode(",",$this->centre_id)).")) p 
                              left join h_province_network hpn on hpn.province_id=p.province_id 
                              left join d_network n on n.network_id=hpn.network_id 
                              left join h_client_province hcp on hcp.province_id=p.province_id 
                              left join d_client dc on dc.client_id = hcp.client_id 
                              left join d_cities cty on cty.city_id = dc.city_id and cty.state_id = dc.state_id
                              left join d_countries ctr on ctr.country_id = dc.country_id
			      left join d_states st on dc.state_id = st.state_id and st.country_id = dc.country_id  where dc.client_name!=''";
                        
			$this->aqsData=$this->db->get_row($sql); 
                        }else{                                                                        
			$sql="select dc.client_name as school_name,p.province_name,n.network_name,cty.city_name, ctr.country_name,st.state_name from 
                              (select * from d_province where province_id=?) p 
                              left join h_province_network hpn on hpn.province_id=p.province_id 
                              left join d_network n on n.network_id=hpn.network_id 
                              left join h_client_province hcp on hcp.province_id=p.province_id 
                              left join d_client dc on dc.client_id = hcp.client_id 
                              left join d_cities cty on cty.city_id = dc.city_id and cty.state_id = dc.state_id
                              left join d_countries ctr on ctr.country_id = dc.country_id
			      left join d_states st on dc.state_id = st.state_id and st.country_id = dc.country_id  where dc.client_name!=''";
                        
			$this->aqsData=$this->db->get_row($sql,array($this->centre_id));
                        }
			
                        //$this->conductedDate=(empty($this->aqsData['school_aqs_pref_end_date']) || $this->aqsData['school_aqs_pref_end_date']=="0000-00-00")?$this->conductedDate:implode("-",array_reverse(explode("-",substr($this->aqsData['school_aqs_pref_end_date'],0,7))));
			//$this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:implode("-",array_reverse(explode("-",substr($this->aqsData['valid_until'],0,7))));
			
                        $this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:date("M-Y",strtotime($this->aqsData['valid_until']));
                        $this->conductedDate=empty($this->aqsData['publishDate'])?$this->conductedDate:implode("-",array_reverse(explode("-",substr($this->aqsData['publishDate'],0,7))));
			
			$this->schoolCity = $this->aqsData['city_name'];
			$this->schoolState = $this->aqsData['state_name'];
			$this->schoolCountry = $this->aqsData['country_name'];
                        $this->province = $this->aqsData['province_name'];
                        $this->network = $this->aqsData['network_name'];
                        //$this->round = $this->aqsData['student_round'];
		}
	}
        
        protected function loadAqsDataStudentOrg(){
		if(empty($this->aqsData)){
			$sql="select dc.client_name as school_name,p.province_name,a1.network_name,cty.city_name, ctr.country_name,st.state_name from 
                              (select * from d_network where network_id=?) a1
                              inner join h_province_network b1 on a1.network_id=b1.network_id
                              inner join d_province  p on b1.province_id=p.province_id 
                              left join h_client_province hcp on hcp.province_id=p.province_id 
                              left join d_client dc on dc.client_id = hcp.client_id 
                              left join d_cities cty on cty.city_id = dc.city_id and cty.state_id = dc.state_id
                              left join d_countries ctr on ctr.country_id = dc.country_id
			      left join d_states st on dc.state_id = st.state_id and st.country_id = dc.country_id where dc.client_name!=''";
                        
			$this->aqsData=$this->db->get_row($sql,array($this->centre_id));
			
                        //$this->conductedDate=(empty($this->aqsData['school_aqs_pref_end_date']) || $this->aqsData['school_aqs_pref_end_date']=="0000-00-00")?$this->conductedDate:implode("-",array_reverse(explode("-",substr($this->aqsData['school_aqs_pref_end_date'],0,7))));
			//$this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:implode("-",array_reverse(explode("-",substr($this->aqsData['valid_until'],0,7))));
			
                        $this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:date("M-Y",strtotime($this->aqsData['valid_until']));
                        $this->conductedDate=empty($this->aqsData['publishDate'])?$this->conductedDate:implode("-",array_reverse(explode("-",substr($this->aqsData['publishDate'],0,7))));
			
			$this->schoolCity = $this->aqsData['city_name'];
			$this->schoolState = $this->aqsData['state_name'];
			$this->schoolCountry = $this->aqsData['country_name'];
                        $this->province = $this->aqsData['province_name'];
                        $this->network = $this->aqsData['network_name'];
                        //$this->round = $this->aqsData['student_round'];
		}
	}
        
	protected function loadKpas($round=0,$lang_id=DEFAULT_LANGUAGE,$students_allow=array()){
                $sql="select a.kpa_instance_id,hlt.translation_text as KPA_name,r.rating,role,hls.rating_level_order as numericRating,g.assessment_id
					from h_kpa_instance_score a
					inner join h_kpa_diagnostic c on a.kpa_instance_id = c.kpa_instance_id 
					inner join d_kpa d on d.kpa_id = c.kpa_id
                                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id
					inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					inner join d_assessment g on a.assessment_id = g.assessment_id ".($this->diagnosticId>0?"and g.diagnostic_id=$this->diagnosticId":"")."
                                        inner join (select user_id,assessment_id from `h_assessment_user` where role=3) bu on a.assessment_id=bu.assessment_id
                                        inner join h_assessment_ass_group ag on ag.assessment_id=g.assessment_id
					inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.d_rating_rating_id=hls.rating_id and hls.rating_level_id=1
					inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					inner join (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=? ) r on a.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id	
					left  join h_group_assessment_teacher hgat on hgat.group_assessment_id=ag.group_assessment_id && hgat.teacher_id=bu.user_id
			                left  join d_school_level dsl on hgat.school_level_id=dsl.school_level_id
                                        where ag.group_assessment_id = ? and hlt.language_id=? ";
                                        
                                         
                                        
                                        if(!empty($this->dept_id)){
                                         $sql.=" && hgat.school_level_id=? ";   
                                        }
                                                                                                
                                         if(count($students_allow)>0){
                           
                                          $sql.=" && bu.user_id IN (".(implode(",",$students_allow)).") ";  
                                           }
                                           
					$sql.="group by a.kpa_instance_id,role,g.assessment_id
                    order by c.`kpa_order` asc";                                                                                
		if(empty($this->kpas)){
			if(!empty($this->dept_id)){
                        $this->kpas=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId,$lang_id,$this->dept_id)),"kpa_instance_id");    
                        }else{
			$this->kpas=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId,$lang_id)),"kpa_instance_id");
                        }
		}
                
                if($round==2){
                    if(empty($this->kpas_r1)){
			if(!empty($this->dept_id)){
                        $this->kpas_r1=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId_r1,$lang_id,$this->dept_id)),"kpa_instance_id");    
                        }else{
			$this->kpas_r1=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId_r1,$lang_id)),"kpa_instance_id");
                        }
		}
                }
	}
	
	protected function loadKeyQuestions($round=0,$lang_id=DEFAULT_LANGUAGE,$students_allow=array()){
                $sql="select c.kpa_instance_id,a.key_question_instance_id,hlt.translation_text as key_question_text,r.rating,role,hls.rating_level_order as numericRating,g.assessment_id,kh.key_heading
					from h_kq_instance_score a
					inner join h_kpa_kq c on a.key_question_instance_id = c.key_question_instance_id
					inner join d_key_question d on d.key_question_id = c.key_question_id	
                                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id
					inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					inner join d_assessment g on a.assessment_id = g.assessment_id ".($this->diagnosticId>0?"and g.diagnostic_id=$this->diagnosticId":"")."
					inner join (select user_id,assessment_id from `h_assessment_user` where role=3) bu on a.assessment_id=bu.assessment_id
                                        inner join h_assessment_ass_group ag on ag.assessment_id=g.assessment_id
					inner join h_kpa_diagnostic i on i.diagnostic_id = g.diagnostic_id and i.kpa_instance_id=c.kpa_instance_id
					inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.d_rating_rating_id=hls.rating_id and hls.rating_level_id=2
					inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					inner join (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=? ) r on a.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id
                                        left join d_key_question_heading kh on d.key_question_id=kh.key_question_id
                                        left  join h_group_assessment_teacher hgat on hgat.group_assessment_id=ag.group_assessment_id && hgat.teacher_id=bu.user_id
			                left  join d_school_level dsl on hgat.school_level_id=dsl.school_level_id
					where ag.group_assessment_id = ? and hlt.language_id=? ";
                                        
                                        if(!empty($this->dept_id)){
                                         $sql.=" && hgat.school_level_id=? ";   
                                        }
                                          
                                         if(count($students_allow)>0){
                           
                                          $sql.=" && bu.user_id IN (".(implode(",",$students_allow)).") ";  
                                           }                                                       
                                                                                                
					$sql.=" group by a.key_question_instance_id,role,g.assessment_id
					order by c.`kq_order` asc";                                                                                
		if(empty($this->keyQuestions)){
			if(!empty($this->dept_id)){
                        $this->keyQuestions=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId,$lang_id,$this->dept_id)),"key_question_instance_id","kpa_instance_id");    
                        }else{
			$this->keyQuestions=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId,$lang_id)),"key_question_instance_id","kpa_instance_id");
                        }
		}
                
                if($round==2){
                if(empty($this->keyQuestions_r1)){
			if(!empty($this->dept_id)){
                        $this->keyQuestions_r1=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId_r1,$lang_id,$this->dept_id)),"key_question_instance_id","kpa_instance_id");    
                        }else{
			$this->keyQuestions_r1=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId_r1,$lang_id)),"key_question_instance_id","kpa_instance_id");
                        }
		}
                }
	}
	
	protected function loadCoreQuestions($round=0,$lang_id=DEFAULT_LANGUAGE,$students_allow=array()){
                 $sql="select c.key_question_instance_id,a.core_question_instance_id,hlt.translation_text as core_question_text,ds.statement as core_question_stmt,r.rating,role,hls.rating_level_order as numericRating,g.assessment_id
					 from h_cq_score a
					 inner join h_kq_cq c on a.core_question_instance_id = c.core_question_instance_id
					 inner join d_core_question d on d.core_question_id = c.core_question_id
                                         inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id
					 inner join d_rating e on a.d_rating_rating_id = e.rating_id
					 inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					 inner join d_assessment g on a.assessment_id = g.assessment_id ".($this->diagnosticId>0?"and g.diagnostic_id=$this->diagnosticId":"")."
					 inner join (select user_id,assessment_id from `h_assessment_user` where role=3) bu on a.assessment_id=bu.assessment_id
                                         inner join h_assessment_ass_group ag on ag.assessment_id=g.assessment_id
					 inner join h_kpa_diagnostic i on i.diagnostic_id = g.diagnostic_id
					 inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and c.key_question_instance_id = j.key_question_instance_id
					 inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					 inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.d_rating_rating_id=hls.rating_id and hls.rating_level_id=3
					 inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					 inner join (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=? ) r on a.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id	
					 left join d_core_question_stmt ds on ds.core_question_id = d.core_question_id
                                         left  join h_group_assessment_teacher hgat on hgat.group_assessment_id=ag.group_assessment_id && hgat.teacher_id=bu.user_id
			                 left  join d_school_level dsl on hgat.school_level_id=dsl.school_level_id
                                         where ag.group_assessment_id = ? and hlt.language_id=? ";
                                         
                                          if(!empty($this->dept_id)){
                                          $sql.=" && hgat.school_level_id=? ";   
                                          }
                                         
                                          if(count($students_allow)>0){
                           
                                          $sql.=" && bu.user_id IN (".(implode(",",$students_allow)).") ";  
                                           } 
                                           
					  $sql.="group by a.core_question_instance_id,role,g.assessment_id
					 order by c.`cq_order` asc;";                                                                                
		if(empty($this->coreQuestions)){
			if(!empty($this->dept_id)){
                        $this->coreQuestions=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId,$lang_id,$this->dept_id)),"core_question_instance_id","key_question_instance_id");
    
                        }else{
			$this->coreQuestions=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId,$lang_id)),"core_question_instance_id","key_question_instance_id");
                        }
		}
                
                if($round==2){
                    if(empty($this->coreQuestions_r1)){
			if(!empty($this->dept_id)){
                        $this->coreQuestions_r1=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId_r1,$lang_id,$this->dept_id)),"core_question_instance_id","key_question_instance_id");    
                        }else{
			$this->coreQuestions_r1=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId_r1,$lang_id)),"core_question_instance_id","key_question_instance_id");
                        }
			}
                }
	}
	
	protected function loadJudgementalStatements($round=0,$lang_id=DEFAULT_LANGUAGE,$students_allow=array()){
                                //$this->dept_id;
                                $sql="
				select c.core_question_instance_id,c.judgement_statement_instance_id,hlt.translation_text as judgement_statement_text,role,r.rating,hls.rating_level_order as numericRating,g.assessment_id
					 from f_score a
					 inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id 
					 inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
					 inner join d_judgement_statement d on d.judgement_statement_id = c.judgement_statement_id
                                         inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id
					 inner join d_assessment g on a.assessment_id = g.assessment_id ".($this->diagnosticId>0?"and g.diagnostic_id=$this->diagnosticId":"")."
					 inner join (select user_id,assessment_id from `h_assessment_user` where role=3) bu on a.assessment_id=bu.assessment_id
                                         inner join h_assessment_ass_group ag on ag.assessment_id=g.assessment_id
					 inner join h_kpa_diagnostic h on h.diagnostic_id = g.diagnostic_id 
					 inner join h_kpa_kq i on h.kpa_instance_id = i.kpa_instance_id
					 inner join h_kq_cq j on i.key_question_instance_id = j.key_question_instance_id and j.core_question_instance_id = c.core_question_instance_id
					 inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
                                         inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.rating_id=hls.rating_id and hls.rating_level_id=4
					 inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					 inner join (select hlt.translation_text as rating,rt.rating_id from d_rating rt INNER JOIN h_lang_translation hlt on rt.equivalence_id = hlt.equivalence_id where hlt.language_id=? )  r on a.rating_id = r.rating_id and hls.rating_id=r.rating_id	
					 left  join h_group_assessment_teacher hgat on hgat.group_assessment_id=ag.group_assessment_id && hgat.teacher_id=bu.user_id
			                 left  join d_school_level dsl on hgat.school_level_id=dsl.school_level_id
                                         
                                         where a.isFinal = 1 and ag.group_assessment_id = ? and hlt.language_id=? ";
                                
                                         if(!empty($this->dept_id)){
                                          $sql.=" && hgat.school_level_id=? ";   
                                         }
                                          
                                         if(count($students_allow)>0){
                           
                                          $sql.=" && bu.user_id IN (".(implode(",",$students_allow)).") ";  
                                         }                                                       
                                   
					 $sql.=" group by c.judgement_statement_instance_id,role,g.assessment_id
					 order by c.`js_order` asc;";
                 //print_r($student_data);          
		if(empty($this->judgementStatement)){
			
			if(!empty($this->dept_id)){
                        $this->judgementStatement=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId,$lang_id,$this->dept_id)),"judgement_statement_instance_id","core_question_instance_id");    
                        }else{	
			$this->judgementStatement=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId,$lang_id)),"judgement_statement_instance_id","core_question_instance_id");
                        }
		}
                
                if($round==2){
                    if(empty($this->judgementStatement_r1)){
			
			if(!empty($this->dept_id)){
                        $this->judgementStatement_r1=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId_r1,$lang_id,$this->dept_id)),"judgement_statement_instance_id","core_question_instance_id");    
                        }else{	
			$this->judgementStatement_r1=$this->get_assmnt_based_section_Array($this->db->get_results($sql,array($lang_id,$this->groupAssessmentId_r1,$lang_id)),"judgement_statement_instance_id","core_question_instance_id");
                        }
		    }
                }
	}
        
         function loadJudgementEvidence($assessment_id,$lang_id=DEFAULT_LANGUAGE){
            //echo $assessment_id;
            $sql="drop table if exists round1JD;
create temporary table round1JD
select i.client_id,client_name,i.kpa_id,kpa.kpa_name,i.`js_order`, (i.numericRating-e.numericRating) as JD,network_name,kpa_order,i.assessment_id,i.judgement_statement_id,i.judgement_statement_text,e.evidence_text from (select distinct h.kpa_id,c.`js_order`,ga.client_id,c.core_question_instance_id,d.judgement_statement_id,judgement_statement_text,role,a.rating_id as numericRating,network_name,kpa_order,a.assessment_id,a.evidence_text from f_score a
inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id and b.role=3
inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
inner join (select djs.*,hlt.translation_text as judgement_statement_text from d_judgement_statement djs inner join h_lang_translation hlt on djs.equivalence_id=hlt.equivalence_id where language_id=?) d on d.judgement_statement_id = c.judgement_statement_id
inner join d_assessment ga on a.assessment_id = ga.assessment_id
inner join h_kpa_diagnostic h on h.diagnostic_id = ga.diagnostic_id and h.kpa_order<7
inner join h_kpa_kq i on h.kpa_instance_id = i.kpa_instance_id
inner join h_kq_cq j on i.key_question_instance_id = j.key_question_instance_id and j.core_question_instance_id = c.core_question_instance_id 
left join h_client_network k on ga.client_id=k.client_id 
left join d_network net on k.network_id=net.network_id
where a.isFinal = 1 and a.assessment_id in (?)
order by c.`js_order` asc) i inner join (select distinct h.kpa_id,c.`js_order`,g.client_id,c.core_question_instance_id,d.judgement_statement_id,judgement_statement_text,role,a.rating_id as numericRating,a.assessment_id,a.evidence_text
from f_score a inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id and b.role=4
inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
inner join (select djs.*,hlt.translation_text as judgement_statement_text from d_judgement_statement djs inner join h_lang_translation hlt on djs.equivalence_id=hlt.equivalence_id where language_id=?) d on d.judgement_statement_id = c.judgement_statement_id
inner join d_assessment g on a.assessment_id = g.assessment_id
inner join h_kpa_diagnostic h on h.diagnostic_id = g.diagnostic_id and h.kpa_order<7
inner join h_kpa_kq i on h.kpa_instance_id = i.kpa_instance_id
inner join h_kq_cq j on i.key_question_instance_id = j.key_question_instance_id and j.core_question_instance_id = c.core_question_instance_id
left join h_client_network k on g.client_id=k.client_id left join d_network net on k.network_id=net.network_id
where a.isFinal = 1 and a.assessment_id in (?) order by c.`js_order` asc) e
on i.client_id = e.client_id and e.judgement_statement_id=i.judgement_statement_id 
inner join (select dka.*,hlt.translation_text as kpa_name  from d_kpa dka inner join h_lang_translation hlt on dka.equivalence_id=hlt.equivalence_id where language_id=?) kpa on i.kpa_id=kpa.kpa_id and e.kpa_id=kpa.kpa_id 
inner join d_client cl on i.client_id=cl.client_id and e.client_id=cl.client_id and i.assessment_id=e.assessment_id;";
$this->db->get_results($sql,array($lang_id,$assessment_id,$lang_id,$assessment_id,$lang_id));

$query_1="SET @position := 0;";
$this->db->get_results($query_1,array());
$query="select a.*,b.to_show,b.id_show,CONCAT_WS(b.to_show,b.id_show,' ') as srno from (select assessment_id, kpa_name,judgement_statement_id,judgement_statement_text,JD,if(evidence_text!='',evidence_text,'NIL') as evidence_text,js_order,(@position := @position + 1) as row from round1JD order by js_order asc) a left join d_alphabets b on a.row=b.id where (JD>0 || JD<0) order by id_show,to_show";
return $this->db->get_results($query,array());

      }
      
      
       function loadJudgementEvidenceAllRounds($assessment_id,$lang_id=DEFAULT_LANGUAGE){
            //echo $assessment_id;
            $sql="drop table if exists round1JD;
create temporary table round1JD
select i.client_id,client_name,i.kpa_id,kpa.kpa_name,i.`js_order`, (i.numericRating-e.numericRating) as JD,network_name,kpa_order,i.assessment_id,i.judgement_statement_id,i.judgement_statement_text,e.evidence_text from (select distinct h.kpa_id,c.`js_order`,ga.client_id,c.core_question_instance_id,d.judgement_statement_id,judgement_statement_text,role,a.rating_id as numericRating,network_name,kpa_order,a.assessment_id,a.evidence_text from f_score a
inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id and b.role=3
inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
inner join (select djs.*,hlt.translation_text as judgement_statement_text from d_judgement_statement djs inner join h_lang_translation hlt on djs.equivalence_id=hlt.equivalence_id where language_id=?) d on d.judgement_statement_id = c.judgement_statement_id
inner join d_assessment ga on a.assessment_id = ga.assessment_id
inner join h_kpa_diagnostic h on h.diagnostic_id = ga.diagnostic_id and h.kpa_order<7
inner join h_kpa_kq i on h.kpa_instance_id = i.kpa_instance_id
inner join h_kq_cq j on i.key_question_instance_id = j.key_question_instance_id and j.core_question_instance_id = c.core_question_instance_id 
left join h_client_network k on ga.client_id=k.client_id 
left join d_network net on k.network_id=net.network_id
where a.isFinal = 1 and a.assessment_id in (?)
order by c.`js_order` asc) i inner join (select distinct h.kpa_id,c.`js_order`,g.client_id,c.core_question_instance_id,d.judgement_statement_id,judgement_statement_text,role,a.rating_id as numericRating,a.assessment_id,a.evidence_text
from f_score a inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id and b.role=4
inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
inner join (select djs.*,hlt.translation_text as judgement_statement_text from d_judgement_statement djs inner join h_lang_translation hlt on djs.equivalence_id=hlt.equivalence_id where language_id=?) d on d.judgement_statement_id = c.judgement_statement_id
inner join d_assessment g on a.assessment_id = g.assessment_id
inner join h_kpa_diagnostic h on h.diagnostic_id = g.diagnostic_id and h.kpa_order<7
inner join h_kpa_kq i on h.kpa_instance_id = i.kpa_instance_id
inner join h_kq_cq j on i.key_question_instance_id = j.key_question_instance_id and j.core_question_instance_id = c.core_question_instance_id
left join h_client_network k on g.client_id=k.client_id left join d_network net on k.network_id=net.network_id
where a.isFinal = 1 and a.assessment_id in (?) order by c.`js_order` asc) e
on i.client_id = e.client_id and e.judgement_statement_id=i.judgement_statement_id 
inner join (select dka.*,hlt.translation_text as kpa_name  from d_kpa dka inner join h_lang_translation hlt on dka.equivalence_id=hlt.equivalence_id where language_id=?) kpa on i.kpa_id=kpa.kpa_id and e.kpa_id=kpa.kpa_id 
inner join d_client cl on i.client_id=cl.client_id and e.client_id=cl.client_id and i.assessment_id=e.assessment_id;";
$this->db->get_results($sql,array($lang_id,$assessment_id,$lang_id,$assessment_id,$lang_id));

$query_1="SET @position := 0;";
$this->db->get_results($query_1,array());
$query="select a.*,b.to_show,b.id_show,CONCAT_WS(b.to_show,b.id_show,' ') as srno from (select assessment_id, kpa_name,judgement_statement_id,judgement_statement_text,JD,if(evidence_text!='',evidence_text,'NIL') as evidence_text,js_order,(@position := @position + 1) as row from round1JD order by js_order asc) a left join d_alphabets b on a.row=b.id where 1=1 order by id_show,to_show";
return $this->db->get_results($query,array());

      }
      
       function getAllAssessment($gaid,$lang_id=DEFAULT_LANGUAGE){
         $sql="SELECT xyz.assessment_id,xyz.teacher_id,xyz.student_uid,xyz.name,r.rating,hls.rating_level_order as numericRating from (SELECT ga.group_assessment_id,hau.assessment_id,ga.client_id,ga.creation_date,at.teacher_id,at.teacher_category_id,at.assessor_id,u.name,ad.diagnostic_id,dtd.value as student_uid,hau.percComplete as internalreview,hauex.percComplete as exreview
FROM `d_group_assessment` ga
inner join h_group_assessment_teacher at on ga.group_assessment_id=at.group_assessment_id
inner join h_group_assessment_diagnostic ad on at.group_assessment_id=ad.group_assessment_id and at.teacher_category_id=ad.teacher_category_id  
left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=3) hau on hau.user_id=at.teacher_id 
left join d_user u on hau.user_id=u.user_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=4) hauex on hauex.user_id=at.assessor_id and hauex.assessment_id=hau.assessment_id 
left join d_student_data dtd on dtd.student_id=hau.user_id && dtd.assessment_id=hau.assessment_id && dtd.attr_id=4  where ga.group_assessment_id=? group by at.teacher_id) xyz left join h_kpa_instance_score a on xyz.assessment_id=a.assessment_id && a.assessor_id=xyz.assessor_id

inner join h_kpa_diagnostic c on a.kpa_instance_id = c.kpa_instance_id 
					inner join d_kpa d on d.kpa_id = c.kpa_id
                      left join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id
					inner join d_assessment g on a.assessment_id = g.assessment_id 
                    inner join h_assessment_ass_group ag on ag.assessment_id=g.assessment_id
					inner join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = g.diagnostic_id
					inner join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and a.d_rating_rating_id=hls.rating_id and hls.rating_level_id=1
					inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
					inner join (select dr.*,hlt.translation_text as rating from  d_rating dr inner join h_lang_translation hlt on dr.equivalence_id=hlt.equivalence_id where hlt.language_id=?) r on a.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id
                              
					
					where ag.group_assessment_id = ? && f.isFilled=1
					group by a.kpa_instance_id,f.role,g.assessment_id
                    order by c.`kpa_order` asc";
 return $this->db->get_results($sql,array($gaid,$gaid,$gaid,$lang_id,$gaid));         
          
      }
      
        
	protected function generateIndexAndCover(){
		$sections=array();
		$coverSection=array("sectionBody"=>array());
		$keysToReplace=array("{schoolName}","{schoolAddress}","{conductedOn}","{validTill}","{awardName}","{dateToday}");
		$valuesToReplace=array($this->aqsData['school_name'],$this->aqsData['school_address'],$this->conductedDate,$this->validDate,$this->awardName,date("d-m-Y"));
		$this->config["footerText"]=str_replace( $keysToReplace,$valuesToReplace,$this->config["footerText"]);
		
		$headBlock=array(
					"blockBody"=>array(
						"dataArray"=>array(
							array(array("text"=>"School Name","style"=>"text-bold"),$this->aqsData['school_name']),
							array(array("text"=>"School Address","style"=>"text-bold"),$this->aqsData['school_address'])
						)
					),
				"style"=>"bordered awardBlock mt20"
			);
		
		$coverSection['sectionBody'][]=$headBlock;
		
		$indexBlock=array(
					"blockHeading"=>array(
						"data"=>array(
							array("text"=>"INDEX","style"=>"greyHead","cSpan"=>3)
						)
					),
					"blockBody"=>array(
						"dataArray"=>array(
							array("SR. NO.","PARTICULARS","PAGE NO.")
						)
					),
				"style"=>"bordered reportIndex"
			);
		foreach($this->indexArray as $k=>$v){
			$indexBlock["blockBody"]["dataArray"][]=array($k+1,$v,'<span id="indexKey-'.($k+1).'"></span>');
		}
		
		
		$sections[]=$coverSection;
		$this->sectionArray=array_merge($sections,$this->sectionArray);
	}
	function generateTchr_Awards(){		
		$html = '<table width="100%" border="0"  cellpadding="4">
					<thead>
					<tr><td colspan="3" align="center">Table '.++$this->tableNum.'</td></tr>
					<tr style="border:1px solid #000000;"><td  width="10%" style="border:1px solid #000000;"><b>Sr. No.</b></td><td style="border:1px solid #000000;" width="50%"><b>Teacher Name</b></td><td style="border:1px solid #000000;" width="40%"><b>Award</b></td></tr></thead>';
		$i=1;					
		$assessmentModel=new assessmentModel();
		$tchrAwardArray = '';
		foreach($this->assessments as $a){
					
			$assessments=$assessmentModel->getAssessmentsInGroupAssessmentDiagnostic($this->groupAssessmentId,$this->diagnosticId);
			if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1 && isset($this->kpas[$a['assessment_id']])){				
				foreach($this->kpas[$a['assessment_id']] as $kpa){
					$score = $kpa['externalRating']['rating'];
					$rating_id = $kpa['externalRating']['score'];					
					$tchrAwardArray[$rating_id] = array("tchr"=>$tchrAwardArray[$rating_id]["tchr"].($tchrAwardArray[$rating_id]["tchr"]?'~':'').$a['data_by_role']['3']['user_name'],"award"=>$score);//array("tchr"=>$a['data_by_role']['3']['user_name'],"rating"=>$rating_id,"award"=>$score);
				}								
			}			
		}
		if(!empty($tchrAwardArray)){
			krsort($tchrAwardArray,SORT_NUMERIC );
			foreach($tchrAwardArray as $t):
				$teachers = explode('~',$t['tchr']);
				$award = $t['award'];				
				foreach($teachers as $k=>$tchr)
					$html .= '<tr style="border:1px solid #000000;"><td style="border:1px solid #000000;" width="10%">'.$i++.'</td><td width="50%" style="border:1px solid #000000;">'.$tchr.'</td><td width="40%" style="border:1px solid #000000;">'.$award.'</td></tr>';								
			endforeach;	
		}
				
		$html .= '</table>';		
		return $html;
	}
	function generateSection_TchrAss(){
		$section=array("sectionHeading"=>array("text"=>"Teacher Review for the year ".substr($this->conductedDate,3),"style"=>"greyHead"),"sectionBody"=>array());
		$assessmentModel=new assessmentModel();
		$assessments=$assessmentModel->getAssessmentsInGroupAssessmentDiagnostic($this->groupAssessmentId,$this->diagnosticId);
		$this->assessments = $assessments;
		$kQData=array();
		foreach($assessments as $a){
			if($a['isTchrInfoFilled']==1 && $a['data_by_role'][4]['status']==1 && isset($this->kpas[$a['assessment_id']])){
				foreach($this->kpas[$a['assessment_id']] as $kpa){
					if(isset($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']])){
						foreach($this->keyQuestions[$a['assessment_id']][$kpa['kpa_instance_id']] as $kq){
							if(empty($kQData[$kq['key_question_instance_id']])){
								$kQData[$kq['key_question_instance_id']]=array(
										"text"=>$kq['key_question_text'],
										'kQRating'=>array(1=>array(),2=>array(),3=>array(),4=>array()),
										'cQData'=>array()
									);
							}
							if(isset($kq['externalRating']['score']) && isset($kQData[$kq['key_question_instance_id']]['kQRating'][$kq['externalRating']['score']])){
								$kQData[$kq['key_question_instance_id']]['kQRating'][$kq['externalRating']['score']][]=$a['data_by_role'][3]['user_name'];
							}
							if(isset($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']])){
								foreach($this->coreQuestions[$a['assessment_id']][$kq['key_question_instance_id']] as $cq){
									if(empty($kQData[$kq['key_question_instance_id']]['cQData'][$cq['core_question_instance_id']])){
										$kQData[$kq['key_question_instance_id']]['cQData'][$cq['core_question_instance_id']]=array(
												"text"=>$cq['core_question_text'],
												'cQRating'=>array(1=>array(),2=>array(),3=>array(),4=>array())
											);
									}
									if(isset($cq['externalRating']['score']) && isset($kQData[$kq['key_question_instance_id']]['cQData'][$cq['core_question_instance_id']]['cQRating'][$kq['externalRating']['score']])){
										$kQData[$kq['key_question_instance_id']]['cQData'][$cq['core_question_instance_id']]['cQRating'][$cq['externalRating']['score']][]=$a['data_by_role'][3]['user_name'];
									}
								}
							}
						}
					}
				}
			}
		}
		
		$kq_cnt=0;
		$heading=array(array("text"=>'Outstanding',"style"=>"text-bold grHead"),array("text"=>'Proficient',"style"=>"text-bold grHead"),array("text"=>'Developing',"style"=>"text-bold grHead"),array("text"=>'Emerging',"style"=>"text-bold grHead"));
		foreach($kQData as $kd){
			$kq_cnt++;
			$block=array(
				"blockHeading"=>array(
					"data"=>array(array("text"=>"KQ$kq_cnt:- ".$kd['text'],"cSpan"=>4,"style"=>"brownHead seqHead fontNormal fs-16"))
				),
				"blockBody"=>array(
					"dataArray"=>array(
						$heading,
						array(implode("<br>",$kd['kQRating'][4]),implode("<br>",$kd['kQRating'][3]),implode("<br>",$kd['kQRating'][2]),implode("<br>",$kd['kQRating'][1]))
					)
				),
				"style"=>"bordered col-4 mb15 grBox"
			);
			$cq_cnt=0;
			foreach($kd['cQData'] as $cd){
				$cq_cnt++;
				$block['blockBody']['dataArray'][]=array(array("text"=>"Sub Q$cq_cnt:- ".$cd['text'],"cSpan"=>4,"style"=>"greyHead seqHead fontNormal fs-14"));
				$block['blockBody']['dataArray'][]=$heading;
				$block['blockBody']['dataArray'][]=array(implode("<br>",$cd['cQRating'][4]),implode("<br>",$cd['cQRating'][3]),implode("<br>",$cd['cQRating'][2]),implode("<br>",$cd['cQRating'][1]));
			}			
			$section['sectionBody'][]=$block;
		}
		
		$this->sectionArray[]=$section;
	}
	function generateSection_TchrRecomm(){
		$diagnosticModel = new diagnosticModel();
		
		$section=array("sectionHeading"=>array("text"=>"AQS Recommendations","style"=>"greyHead","config"=>array("repeatHead"=>1)),"sectionBody"=>array());
		$heading=array(array("text"=>'Teacher Name',"style"=>"text-bold greyHead tchr","cSpan"=>"1"),array("text"=>'Assessor Key Recommendations',"style"=>"text-bold greyHead","cSpan"=>"3"));
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
		$celebrateBlock = "<dl>Celebrate:<dt>2</dt><dt>3</dt><dt>4</dt>";
		$ImproveBlock = "<dl>Improve	:<dt>2</dt><dt>3</dt><dt>4</dt>";
		
		foreach($this->assessments as $asmt)
		{	
			
			if($asmt['diagnostic_id']!=$this->diagnosticId || ( empty($asmt['data_by_role'][4]['status']) || $asmt['data_by_role'][4]['status']!=1 || empty($asmt['isTchrInfoFilled']) || $asmt['isTchrInfoFilled']!=1))
				continue;
			$celebrateData = $diagnosticModel->getAssessorKeyNotesType($asmt['assessment_id'],'celebrate');
			$celebrateBlock = "<b>Celebrate:</b><ul>";
			foreach($celebrateData as $cel)
				$celebrateBlock .= "<li>".$cel['text_data']."</li>";
				$celebrateBlock .= "</ul>";
			
			$improveData = $diagnosticModel->getAssessorKeyNotesType($asmt['assessment_id'],'recommendation');
			$ImproveBlock = "<b>Recommendations:</b><ul>";
			foreach($improveData as $improve)
				$ImproveBlock .= "<li>".$improve['text_data']."</li>";
			$ImproveBlock .= "</ul>";
			
			$block['blockBody']['dataArray'][] = array(array("text"=>$asmt['data_by_role']['3']['user_name'],"rSpan"=>1),array("text"=>$celebrateBlock.$ImproveBlock,"style"=>'assessornoteTbl'));
			//$block['blockBody']['dataArray'][] = array(array("text"=>$ImproveBlock,"style"=>'assessornoteTbl'));
		}
		
		$section['sectionBody'][]=$block;
		
		$this->sectionArray[]=$section;
	}
}