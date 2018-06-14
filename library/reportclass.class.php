<?php

abstract class reportClass{
	var $db;
	var $aqsInfo='';
	var $tableNum = 1;
	var $config=array(
		"reportTitle"=>"",
		"containerID"=>"reportContainer",
		
		"pageHeight"=>1200,
		"pageWidth"=>827,
		"pageLeftRightPadding"=>20,
		"bodyTopBottomPadding"=>10,
		
		"coverHeaderHeight"=>180,
		"coverHeaderPadding"=>40,
		"coverAddress"=>'Adhyayan Quality Education Services Private Limited<br>A-17, 1st Floor, Royal Industrial Estate, Sewree Wadala Cross Road, Wadala West, Mumbai - 400 031 <br>Email: info@adhyayan.asia | Website: www.adhyayan.asia<br>22 2417 44 63 | 09773187331',
		
                "coverAddressAntarang"=>'<span class="antHead">Antarang Foundation</span><br><br>C231, Tawari Pada,<br>Dr SS Rao Road, Lalbaug,<br>Mumbai, Maharashtra 400075<br>www.antarangfoundation.org',
		"coverAddressAdhyayanFoundation"=>'<span class="adhHead">Adhyayan Foundation</span><br><br>A-17, Royal Industrial Estate, 1<sup>st</sup> Floor,<br>Sewree Wadal Cross Road, Wadala (W),<br>Mumbai, Maharashtra 400031<br>www.adhyayanfoundation.org',
		                                                                
		"headerImg"=> "./public/images/reports/Rep-logo.png",
		"headerImgAdh"=> "./public/images/login/logo_login.png",
		"headerImgCh"=> "./public/images/changemaker.jpg",
		"headerImgChsmall"=> "./public/images/changemaker.png",
		"headerHeight"=>90,
		"headerBG"=>"#6c0d10",
		"headerPadding"=>15,
		
		"footerHeight"=>40,
		"pageNoBarHeight"=>25,
		"footerBG"=>"#29201a",
		"footerColor"=>"#908076",
		"footerText"=>"",
		"isChangeMaker"=>0,
                "isShishuvanTeacherReview"=>0,
                "isDominicSavioTeacherReview"=>0,                                                                
                "isStudentReview"=>0,                                                                
                "isShishuvanTeacherImg"=>"./public/images/shishuvan.jpg",
                "isDominicSavioTeacherImg"=>"./public/images/dominicsavio.jpg",                                                                
                "isStudentReviewImg"=>'./public/images/diagnostic_adhyayan.png',
                "headerStudentImgAdh"=>'./public/images/antarang.png',
                "iscollegeReview"=>0,
                "isCollegeReviewImg"=>'./public/images/diagnostic_adhyayan.png',
                "headerCollegeImgAdh"=>'./public/images/antarang.png', 
                "iscollegeReview"=>0,
                "isCollegeReviewImg"=>'./public/images/diagnostic_adhyayan.png',
                "headerCollegeImgAdh"=>'./public/images/antarang.png',
                "StudentAwardDefi"=>'./public/images/Career_Readiness_Key_definitions.png',                                                                   
		"isCoBranded"=>0,
		"coBrandedImg"=>'',
                "schoolName"=>'',
                "fileName_Student"=>''  ,                                                              
                "isChildProt"=>'' ,
                "getCreateNetType"=>0 ,
                "isCollobrative"=>0                                                                
	);
	
	var $kpas=array();
	var $keyQuestions=array();
	var $coreQuestions=array();
	var $judgementStatement=array();
	var $aqsData=array();
	var $sectionArray=array();
	var $indexArray=array();
	var $noOfScore1234InKpas=array();
	var $keyNotes=array();
	var $awardNo=0;
	var $awardName="";
	var $assessmentId;
	var $groupAssessmentId;
	var $awardSchemes=array();
	var $aqsTeam=array();
	var $recommendationText=array();
	var $teacherInfo=array();
        var $studentInfo=array();
	var $assessmentObject=array();
	var $reportId=0;
	var $diagnosticId=0;
	var $validDate = '';
	var $conductedDate = '';
	var $errorArray=array();
        
        var $kpas_r1=array();
	var $keyQuestions_r1=array();
	var $coreQuestions_r1=array();
	var $judgementStatement_r1=array();
	
	public function __construct($reportId,$conductedDate,$validDate){
		$this->db=db::getInstance();
		$this->reportId=$reportId;
		$this->conductedDate=$conductedDate;
		$this->validDate=$validDate;
	}
	
	protected function addIndex($text){
		$indexKey=count($this->indexArray);
		$this->indexArray[$indexKey]=$text;
		return $indexKey+1;
	}
	
	protected function get_section_Array($arr,$instanceIdKey,$groupingIdKey=""){
		$res=array();
		if(count($arr)){
			if($groupingIdKey==""){
				foreach($arr as $v){
					if(isset($res[$v[$instanceIdKey]])){
						$res[$v[$instanceIdKey]][$v['role']==3?'internalRating':'externalRating']=array("rating"=>$v['rating'],"score"=>$v['numericRating']);
					}else{
						$v[$v['role']==3?'internalRating':'externalRating']=array("rating"=>$v['rating'],"score"=>$v['numericRating']);
						unset($v['numericRating']);
						unset($v['rating']);
						unset($v['role']);
						$res[$v[$instanceIdKey]]=$v;
					}
				}
			}else{
				foreach($arr as $v){
					if(isset($res[$v[$groupingIdKey]]) && isset($res[$v[$groupingIdKey]][$v[$instanceIdKey]])){
						$res[$v[$groupingIdKey]][$v[$instanceIdKey]][$v['role']==3?'internalRating':'externalRating']=array("rating"=>$v['rating'],"score"=>$v['numericRating']);
					}else{
						$v[$v['role']==3?'internalRating':'externalRating']=array("rating"=>$v['rating'],"score"=>$v['numericRating']);
						unset($v['numericRating']);
						unset($v['rating']);
						unset($v['role']);
						$res[$v[$groupingIdKey]][$v[$instanceIdKey]]=$v;
					}
				}
			}
		}
		return $res;
	}
	protected function getTwoRoundsArray($arr,$instanceIdKey,$groupingIdKey=""){
		$res=array();
		if(count($arr)){
			if($groupingIdKey==""){
				foreach($arr as $v){
					if(isset($res[$v['assessment_id']][$v[$instanceIdKey]])){
						$res[$v['assessment_id']][$v[$instanceIdKey]][$v['role']==3?'internalRating':'externalRating']=array("rating"=>$v['rating'],"score"=>$v['numericRating']);
					}else{
						$v[$v['role']==3?'internalRating':'externalRating']=array("rating"=>$v['rating'],"score"=>$v['numericRating']);
						unset($v['numericRating']);
						unset($v['rating']);
						unset($v['role']);
						$res[$v['assessment_id']][$v[$instanceIdKey]]=$v;
					}
				}
			}else{
				foreach($arr as $v){
					if(isset($res[$v['assessment_id']][$v[$groupingIdKey]]) && isset($res[$v['assessment_id']][$v[$groupingIdKey]][$v[$instanceIdKey]])){
						$res[$v['assessment_id']][$v[$groupingIdKey]][$v[$instanceIdKey]][$v['role']==3?'internalRating':'externalRating']=array("rating"=>$v['rating'],"score"=>$v['numericRating']);
					}else{
						$v[$v['role']==3?'internalRating':'externalRating']=array("rating"=>$v['rating'],"score"=>$v['numericRating']);
						unset($v['numericRating']);
						unset($v['rating']);
						unset($v['role']);
						$res[$v['assessment_id']][$v[$groupingIdKey]][$v[$instanceIdKey]]=$v;
					}
				}
			}
		}
		return $res;
	}
        
        
        
	protected function get_assmnt_based_section_Array($arr,$instanceIdKey,$groupingIdKey=""){
		$arr=$this->db->array_grouping($arr,"assessment_id");
		foreach($arr as $k=>$a){
			$arr[$k]=$this->get_section_Array($a,$instanceIdKey,$groupingIdKey);
		}
		return $arr;
	}
	final protected function getPDFtchrPerformance(){
		$diagnosticModel = new diagnosticModel();
		$html = '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>';
		//$dashedImage = '<img src="'.ROOT.'public'.DS.'images'.DS.'reports'.DS.'dashed.png'.'" />';
		$dashedImage = ROOT.'public'.DS.'images'.DS.'reports'.DS.'dashed.png';
		$html .= '<style>
				.score-bg-4 {
				    background-color: #2e5ce6 ;
				}
				.score-bg-3 {
				    background-color: #47b247;
				}
				.score-bg-2 {
				    background-color: #e8bf19;
				}
				.score-bg-1 {
				    background-color: #ff0000;
				}
				</style>';
	
		$kpa_count=0;
		$always = array(1=>'','','','','','','','','');
		$mostly = array(1=>'','','','','','','','','');
		$sometimes = array(1=>'','','','','','','','','');
		$rarely = array(1=>'','','','','','','','','');
		$grade = array(1=>'','','','','','','','','');
		$gradeColor = array(1=>'','','','','','','','','');
		foreach($this->kpas as $kpa){
				
			$kpa_count++;
			$keyQ_count=0;
			$coreQsInKPA=0;
			if(isset($this->keyQuestions[$kpa['kpa_instance_id']])){
				$numberToAlpha=array(1=>"a",2=>"b",3=>"c",4=>"d");
				foreach($this->keyQuestions[$kpa['kpa_instance_id']] as $keyQ){
					$recommendations = '';
					$keyQ_count++;
					$html .= '<div style="page-break-inside:avoid;">
					<div align="center">Table '.++$this->tableNum.'</div>		
					<table width="100%" cellpadding="4" nobr="true">
					<tr>
						<td style="border:1px solid #000;" width="18%"><b>Key Question '.$keyQ_count.'</b></td>
						<td rowspan="7" width="2%" style="background-image:url('.$dashedImage.')">df</td>
						<td colspan="3" style="border:1px solid #000;" width="80%"><b>'.$keyQ['key_question_text'].'</b></td>
					</tr>
					<tr>
						<td style="border:1px solid #000;" >Sub Questions</td>';
					if(isset($this->coreQuestions[$keyQ['key_question_instance_id']])){
						$coreQ_count=0;
						foreach($this->coreQuestions[$keyQ['key_question_instance_id']] as $coreQ){
							//print_r($coreQ);;die;
							$coreQ_count++;
							$coreQsInKPA++;
							$gradeColor[$coreQsInKPA] = $coreQ['externalRating']['score'];
							$grade[$coreQsInKPA] = $coreQ['externalRating']['rating'];//scheme-2-score-'.$gradeColor[$coreQsInKPA].'
							$html .= '<td style="border:1px solid #000;" class="">'.$coreQsInKPA.'. '.$coreQ['core_question_text'].'</td>';
							$satatement_count=0;
							foreach($this->judgementStatement[$coreQ['core_question_instance_id']] as $statment){
								$satatement_count++;
								if(isset($statment['externalRating']))
									switch($statment['externalRating']['score']){
										case 4: $always[$coreQsInKPA].= $coreQsInKPA.$numberToAlpha[$satatement_count].',';
										break;
										case 3: $mostly[$coreQsInKPA].= $coreQsInKPA.$numberToAlpha[$satatement_count].',';
										break;
										case 2: $sometimes[$coreQsInKPA].= $coreQsInKPA.$numberToAlpha[$satatement_count].',';
										break;
										case 1: $rarely[$coreQsInKPA].= $coreQsInKPA.$numberToAlpha[$satatement_count].',';
										break;
								}
							}
						}
						$html .='</tr>
								<tr>
									<td style="border:1px solid #000;" >Always</td>
									<td style="border:1px solid #000;" >'.substr($always[$coreQsInKPA-2],0,-1).'</td>
									<td style="border:1px solid #000;" >'.substr($always[$coreQsInKPA-1],0,-1).'</td>
									<td style="border:1px solid #000;" >'.substr($always[$coreQsInKPA],0,-1).'</td>
								</tr>
								<tr>
									<td style="border:1px solid #000;" >Mostly</td>
									<td style="border:1px solid #000;" >'.substr($mostly[$coreQsInKPA-2],0,-1).'</td>
									<td style="border:1px solid #000;" >'.substr($mostly[$coreQsInKPA-1],0,-1).'</td>
									<td style="border:1px solid #000;" >'.substr($mostly[$coreQsInKPA],0,-1).'</td>
								</tr>
								<tr>
									<td style="border:1px solid #000;" >Sometimes</td>
									<td style="border:1px solid #000;" >'.substr($sometimes[$coreQsInKPA-2],0,-1).'</td>
									<td style="border:1px solid #000;" >'.substr($sometimes[$coreQsInKPA-1],0,-1).'</td>
									<td style="border:1px solid #000;" >'.substr($sometimes[$coreQsInKPA],0,-1).'</td>
								</tr>
								<tr>
									<td style="border:1px solid #000;" >Rarely</td>
									<td style="border:1px solid #000;" >'.substr($rarely[$coreQsInKPA-2],0,-1).'</td>
									<td style="border:1px solid #000;" >'.substr($rarely[$coreQsInKPA-1],0,-1).'</td>
									<td style="border:1px solid #000;" >'.substr($rarely[$coreQsInKPA],0,-1).'</td>
								</tr>
								<tr>
									<td style="border:1px solid #000;">Sub Question Grade</td>
									<td style="border:1px solid #000;" class="scheme-2-score-'.$gradeColor[$coreQsInKPA-2].'">'.$grade[$coreQsInKPA-2].'</td>
									<td style="border:1px solid #000;" class="scheme-2-score-'.$gradeColor[$coreQsInKPA-1].'">'.$grade[$coreQsInKPA-1].'</td>
									<td style="border:1px solid #000;" class="scheme-2-score-'.$gradeColor[$coreQsInKPA].'">'.$grade[$coreQsInKPA].'</td>
								</tr>';
						$rcs = $diagnosticModel->getAssessorKeyNotesForType($this->assessmentId,'key_question',$keyQ['key_question_instance_id']);
						if(!empty($rcs)){
							//$celebrate = $rcs['text_data']
							$celebrate = array_filter($rcs,function($var){
								if($var['type']=='celebrate')
									return $var;
							});
							$improve = array_filter($rcs,function($var){
									if($var['type']=='recommendation')
										return $var;
							});
							
							if(!empty($celebrate)){
								$recommendations = 'Celebrate:<ol>';
								foreach($celebrate as $cel)
									$recommendations .= '<li>'.$cel['text_data'].'</li>';
								$recommendations .= '</ol>';
							}
							if(!empty($improve)){
								$recommendations .= 'Recommendations:<ol>';
								foreach($improve as $cel)
									$recommendations .= '<li>'.$cel['text_data'].'</li>';
									$recommendations .= '</ol>';
							}
							
						}
					}
					
					$html.='</table>'.($recommendations?('<p style="text-align:left;"><b>Assessor Key Recommendations</b><br/><br/>'.$recommendations.'</p>'):'').'</div>';
						
				}
			}
	
		}
		return $html;
	}
	final protected function createURISqJd(){
		//format is ?sq1=title;jd0;jd1;jd2;jd3&sq2=title;jd0;jd1;jd2;jd3
		//jd 0 means number of judgement statemnts in the sub question  with judgement distance 0
		//jd 1 means number of judgement statemnts in the sub question  with judgement distance 1
		//same goes for the rest
		$url ='?';
		$coreQ_count = 0;
		foreach($this->kpas as $kpa){
			if(isset($this->keyQuestions[$kpa['kpa_instance_id']])){
				foreach($this->keyQuestions[$kpa['kpa_instance_id']] as $keyQ){
					if(isset($this->coreQuestions[$keyQ['key_question_instance_id']])){
						foreach($this->coreQuestions[$keyQ['key_question_instance_id']] as $coreQ){
							$coreQ_count++;
							$jd0 = 0;$jd1 = 0;$jd2 = 0;$jd3 = 0;
							foreach($this->judgementStatement[$coreQ['core_question_instance_id']] as $statment){
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
							$url .= $coreQ_count.'='.urlencode('Sub Question '.$coreQ_count).';'.$jd0.';'.$jd1.';'.$jd2.';'.$jd3.'&';
						}
					}
	
				}
			}
		}	
		return $url;
	}
	final protected function getPDFKeyForReadingReport(){
		return '<b>Teacher performance diagnostic focuses on the following key performance aspects:</b><br/><br/>
				Planning, preparation, delivery and student evaluation and support. Understanding and application of the curricular and co-curricular areas, communication with all stakeholders and embraces school vision and culture and adopts the school\'s systems and processes.
				<br/>
				<div align="center">Table '.++$this->tableNum.'</div>						
				<table width="100%" border="1" style="border:1px solid #c0c0c0;" cellpadding="4" nobr="true">
						<tr><td width="12%"><b>Rating</b></td><td width="88%"><b>To be read as</b></td></tr>
						<tr><td>Always</td><td>Exemplifies high quality or best practice within the key performance area which is worth sharing.</td></tr>
						<tr><td>Mostly</td><td>Consistently effective within the key performance area and acts as a mentor and champion.</td></tr>
						<tr><td>Sometimes</td><td>Evidence of some strong practice and still needs to develop key skills and strategies to become securely effective.</td></tr>
						<tr><td>Rarely</td><td>The teacher is in need of significant support in order for them to aspire to be effective in the classroom.</td></tr>
					</table>';
	}
	final protected function getTeacherAwardDefinition(){		
				return	'<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>'.'
						<div style="page-break-inside:avoid;">
						<div align="center">Table '.++$this->tableNum.'</div>
						<table width="100%" border="1" style="border:1px solid #c0c0c0;" cellpadding="4" nobr="true"><tr><td width="15%"><b>Grade</b></td><td width="85%"><b>Definition</b></td></tr>
						<tr><td class="scheme-2-score-5">Exceptional</td><td>The performance of the teacher in planning, preparation and lesson delivery is consistently, highly effective and at times exceptional. The teacher forms strong, collaborative professional relationships with colleagues and plays a leading role in their section and subject. Students enjoy their lessons, are confident in the subject knowledge. They respond well to the teacher\'s high expectations knowing that the teacher will always provide support and guidance in co-curricular and academic endeavours.</td></tr>
						<tr><td class="scheme-2-score-4">Proficient </td><td>The teacher\'s performance in planning, preparation and lesson delivery is consistently effective with moments of outstanding and less effective practice. The teacher willingly collaborates with colleagues and at times takes the lead in their section and subject. Students usually enjoy their lessons and are confident in their subject knowledge, skills and respond well to the teacher\'s expectations. Students have confidence in the teacher\'s motivation and commitment and the manner in which they consistently provide academic, co-curricular and social support and guidance. </td></tr>
						<tr><td class="scheme-2-score-3">Developing </td><td>The teacher\'s performance in planning, preparation and lesson delivery is increasingly effective with a significant minority of evidence of secure practice. The teacher may need prompting to collaborate with colleagues and to take lead in their section and subject without being asked. Students enjoy and are engaged in some lessons. Increasingly they are confident in their subject knowledge, and skills and respond well to the teacher\'s expectations. The teacher does not always provide students with co-curricular, academic and social support and guidance.</td></tr>
						<tr><td class="scheme-2-score-2">Emerging </td><td>The teacher is beginning to show a consistent understanding of the breadth of their role leading teaching and learning. They may already be displaying early signs of effectiveness in planning, preparation and lesson delivery. As yet their grasp of the strategies and skills necessary for an effective teacher are still developing. While they may reveal significant, their performance is marked most significantly by its variability.</td></tr>
						<tr><td class="scheme-2-score-1">Foundation </td><td>This grade is presented to teachers who are just beginning their self-improvement journey. While they may display effective relationships and content knowledge, they will be at an early stage in developing their effectiveness in the key areas of lesson planning, lesson delivery, assessment and tracking of students or the critical strategies and skills necessary to promote engaged student learning.</td></tr>
					</table>
					</div>';
	}
	final protected function getTeacherOverviewAwardDefinition(){
		return '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>'.'								
				<table width="100%" border="0"  cellpadding="4">
						<thead><tr><th colspan="2" align="center">Table '.++$this->tableNum.'</th></tr>
						<tr style="border:1px solid #000000;"><th style="border:1px solid #000000;" width="13%"><b>Grade</b></th><th style="border:1px solid #000000;" width="87%"><b>Definition</b></th></tr>
						</thead>		
						<tr style="border:1px solid #000000;"><td  width="13%" style="border:1px solid #000000;" class="scheme-2-score-5">Exceptional</td><td  width="87%" style="border:1px solid #000000;">The performance of the teacher in planning, preparation and lesson delivery is consistently, highly effective and at times exceptional. The teacher forms strong, collaborative professional relationships with colleagues and plays a leading role in their section and subject. Students enjoy their lessons, are confident in the subject knowledge. They respond well to the teacher\'s high expectations knowing that the teacher will always provide support and guidance in co-curricular and academic endeavours.</td></tr>
						<tr style="border:1px solid #000000;"><td style="border:1px solid #000000;" class="scheme-2-score-4">Proficient </td><td style="border:1px solid #000000;">The teacher\'s performance in planning, preparation and lesson delivery is consistently effective with moments of outstanding and less effective practice. The teacher willingly collaborates with colleagues and at times takes the lead in their section and subject. Students usually enjoy their lessons and are confident in their subject knowledge, skills and respond well to the teacher\'s expectations. Students have confidence in the teacher\'s motivation and commitment and the manner in which they consistently provide academic, co-curricular and social support and guidance. </td></tr>
						<tr style="border:1px solid #000000;"><td style="border:1px solid #000000;" class="scheme-2-score-3">Developing </td><td style="border:1px solid #000000;">The teacher\'s performance in planning, preparation and lesson delivery is increasingly effective with a significant minority of evidence of secure practice. The teacher may need prompting to collaborate with colleagues and to take lead in their section and subject without being asked. Students enjoy and are engaged in some lessons. Increasingly they are confident in their subject knowledge, and skills and respond well to the teacher\'s expectations. The teacher does not always provide students with co-curricular, academic and social support and guidance.</td></tr>
						<tr style="border:1px solid #000000;"><td style="border:1px solid #000000;" class="scheme-2-score-2">Emerging </td><td style="border:1px solid #000000;">The teacher is beginning to show a consistent understanding of the breadth of their role leading teaching and learning. They may already be displaying early signs of effectiveness in planning, preparation and lesson delivery. As yet their grasp of the strategies and skills necessary for an effective teacher are still developing. While they may reveal significant, their performance is marked most significantly by its variability.</td></tr>
						<tr style="border:1px solid #c0c0c0;"><td style="border:1px solid #000000;" class="scheme-2-score-1">Foundation </td><td style="border:1px solid #000000;">This grade is presented to teachers who are just beginning their self-improvement journey. While they may display effective relationships and content knowledge, they will be at an early stage in developing their effectiveness in the key areas of lesson planning, lesson delivery, assessment and tracking of students or the critical strategies and skills necessary to promote engaged student learning.</td></tr>
					</table>
				';
	}
        final protected function getStudentOverviewAwardDefinition(){
		return '<style>'.file_get_contents(ROOT.'public'.DS.'css'.DS.'reports.css').'</style>'.'								
				<table width="100%" border="0"  cellpadding="4">
						<thead><tr><th colspan="2" align="center">Table '.++$this->tableNum.'</th></tr>
						<tr style="border:1px solid #000000;"><th style="border:1px solid #000000;" width="13%"><b>Grade</b></th><th style="border:1px solid #000000;" width="87%"><b>Definition</b></th></tr>
						</thead>		
						<tr style="border:1px solid #000000;"><td  width="13%" style="border:1px solid #000000;" class="scheme-2-score-5">Exceptional</td><td  width="87%" style="border:1px solid #000000;">I possess a strong sense of self-awareness, career awareness, and the skills and mindsets required in a 21st century work place. I understand that careers require a long term perspective and that I must make intentional choices to reach my long-term career goals. I have reviewed my career readiness in an objective manner. I have actively begun to seek out opportunities, and have mapped out careers best suited for me that I constantly update.</td></tr>
						<tr style="border:1px solid #000000;"><td style="border:1px solid #000000;" class="scheme-2-score-4">Proficient </td><td style="border:1px solid #000000;">I possess a strong sense of self-awareness, career awareness, and the skills and mindsets required in a 21st century work place. I understand that careers require a long-term perspective and that I must make intentional choices to reach my long-term career goals. I possess the ability to review my career readiness in an objective manner. I have begun to take some steps towards seeking out opportunities to build my career. I actively conduct research on my field(s) of interest.</td></tr>
						<tr style="border:1px solid #000000;"><td style="border:1px solid #000000;" class="scheme-2-score-3">Developing </td><td style="border:1px solid #000000;">I possess a sense of self awareness, although I have never formally mapped it out. I am job ready and understand what skills, knowledge and attitudes are required to perform the jobs that I am interested in. I have some understanding of how the jobs that I wish to pursue will help me build my career. My ability to review my career readiness objectively is variable. I have begun thinking about how I can find more opportunities that will help me move forward.</td></tr>
						<tr style="border:1px solid #000000;"><td style="border:1px solid #000000;" class="scheme-2-score-2">Emerging </td><td style="border:1px solid #000000;">I sometimes think about my interests, aptitudes, and aspirations. I am beginning to understand what skills, knowledge and attitudes are required to secure and keep a job. I am not sure of how the jobs that I wish to pursue will help me build my career. I am beginning to understand how to gather evidence to review my career readiness effectively. I take whatever job comes my way.</td></tr>
						<tr style="border:1px solid #c0c0c0;"><td style="border:1px solid #000000;" class="scheme-2-score-1">Foundation </td><td style="border:1px solid #000000;">This grade is presented to those who are just beginning their journey of career readiness. I rarely think about my interests, aptitudes and aspirations. I am not sure of what skills, knowledge and attitudes are required to secure a job. I am not sure of how jobs are connected to career readiness. I am not sure of how to review myself. I am eager to build a career and willing to learn how to do it.</td></tr>
					</table>
				';
	}
	final protected function getPDFIndex($tcpdfObj){
		$tcpdfObj->addTOCPage ();
		// write the TOC title and/or other elements on the TOC page
		$tcpdfObj->SetFont ( 'times', 'B', 16 );
		$tcpdfObj->SetTextColor ( 255, 255, 255 );
		
                if($this->reportId==8 || $this->reportId==11 ||  $this->reportId==12){
                //$tcpdfObj->SetFillColor ( 108, 13, 16 );
                $tcpdfObj->SetFillColor ( 0, 176 , 80 );
                $tcpdfObj->setCellHeightRatio(1.50);
                $tcpdfObj->MultiCell ( 0, 0, 'Table Of Contents', 1, 'C', true, 1, '', '', true, 1 );
                $tcpdfObj->setCellHeightRatio(1.25);
                }else{
                $tcpdfObj->SetFillColor ( 108, 13, 16 );    
		$tcpdfObj->MultiCell ( 0, 0, 'Table Of Contents', 1, 'C', true, 1, '', '', true, 1 );
                }
		$tcpdfObj->Ln ( 5 );
		$tcpdfObj->SetTextColor ( 0, 0, 0 );
		$tcpdfObj->Write ( 0,'Table 1', '', 0, 'C', true, 0, false, false, 0 );
		$tcpdfObj->Ln ( 5 );
		$tcpdfObj->SetFont ( 'times', '', 10 );
	
		// define styles for various bookmark levels
		$secondpagehtml = array ();
	
		$tcpdfObj->Ln ( 5 );
		$tcpdfObj->SetTextColor ( 108, 13, 16 );
		$tcpdfObj->SetFont ( 'times', '', 15 );
		//$tcpdfObj->Write ( 0,'Table 1', '', 0, 'C', true, 0, false, false, 0 );
		//$tcpdfObj->Ln ( 5 );
		$secondpagehtml [0] = '<table border="1" cellpadding="5" cellspacing="0" style="background-color:#fff">' . '<tr style="text-align: left">' . '<td width="155mm">' . '<span style="font-family:times;font-weight:normal;font-size:12pt;color:black;">#TOC_DESCRIPTION#</span>' . '</td>' . '<td width="25mm" style="text-align:center;">' . '<span style="font-weight:normal;font-size:12pt;color:black;">#TOC_PAGE_NUMBER#</span>' . '</td>' . '</tr>' . '</table>';
	
		// add table of content at page 1
		// (check the example n. 45 for a text-only TOC
		$tcpdfObj->addHTMLTOC ( 2, 'INDEX', $secondpagehtml, true, 'B', array (
				128,
				0,
				0
		) );
	
		// end of TOC page
		$tcpdfObj->endTOCPage ();
	}
	final protected function getPDFreviewProcess(){
		$html = '
				<b>Brief about the programme:</b>
				<ul>
					<li>each teacher knows what is expected of them from the school management </li>
					<li>all teachers are able to self-assess their progress, development and learning to determine where \'I am\' and \'where do I want to get to\', what has improved and what areas still need improvement.</li>
					<li>each teacher is able to identify the support that is available from the organisation to support them to meet the agreed expectations</li>
					<li>all teachers know the basis on which performance will be assessed and that it is the same criteria for all</li>
				</ul>
				<b>The Orientation:</b>
				<ul>
					<li>teachers are introduced to the standards and the framework for the rubric</li>
					<li>sharing of formats for feedback with the school team</li>
					<li>the school leaders and the teachers work out processes to obtain feedback from the stakeholders: parents, students and peers.</li>
				</ul>
				<b>The Self-Review: </b><br/>
				Each teacher is expected to collect detailed evidence and prepare a portfolio using:
				<ul>
					<li>her/his documentation of work (including lesson plans and assessments) done in the organisation including:
						<ul>
							<li>periodic discussions with their supervisors and peers on their performance</li>
							<li>periodic interviews with parents at PTMs on what the feedback from their children has been</li>
							<li>regular feedback from students (class III onwards) on what helps them learn best</li>
							<li>book look (class work, homework, journals, projects, registers)</li>
						</ul>
					</li>
					<li>use the portfolio collected to be able to make a judgment</li>
					<li>ask buddies chosen by the teachers themselves, to undertake class observation</li>
					<li>discuss their judgements with buddies with a view to gaining helpful feedback.</li>
					<li>the evidence collected to be attached onto the software.</li>
				</ul>								
					<b>Validation:</b>
					 <ul>
							<li>the validator will conduct two classroom observations (3, where necessary) and the book look for every teacher.</li>
							<li>the validator will examine feedback from the school leaders/in-charge, peers, students, parents pertaining to each teacher.</li>
							<li>the evidence collected to be attached onto the software. </li>							
					</ul>				
					<br/>
					<b>Report Card:</b><br/>
					Adhyayan provides access to software that generates following reports for the school leadership team to view and analyse their teacher\'s performance and make informed decisions to support their professional development journey. 
					 <ol>
							<li>the individual teacher report card reflects the ratings of self review and the external review thereby specifying the judgement distance for a quick analysis on the evaluation.</li>
							<li>the individual teacher recommendations report is generated with an option to add celebrations and recommendations for every teacher for further professional development.</li>
					</ol>				
				';
		return $html;
	}
        
        final protected function getPDFStudentreviewProcess(){
		$html = '<br><br>
                        <div><b>Orientation:</b> The facilitators undergo an orientation programme to help them understand how to objectively gather evidence and make evidence based judgements. They are also introduced to the rating descriptors that would support them and the participants in providing accurate ratings.
			</div>
                        <div><b>Self-Review:</b> The facilitators administer the diagnostic to students. The students assess their own career readiness by providing evidence and rating their performance.
                        </div>
                        <div><b>Validation:</b> Facilitators review the evidence provided by the participants and validate their evidence based on the triangulation of data as well as effectiveness of evidence provided.
                        </div>
                        <div><b>Report Card:</b> A report card is generated on Adhyayan’s software for each student that compares the self-review and validation ratings. The report card also provides the student with a baseline award based on their performance.
                        </div>
                        <div><b>Quality Dialogue:</b> The facilitators use the participants’ report cards to have a developmental conversation with them to enable them to understand the standard against which they are being benchmarked, as well as to understand the way forward.
                        </div>                        
                        ';
                if($this->reportId=="11"){
                
                $html .= '<br><br>This report details the performance across batches within '.$this->aqsData['province_name'].' centre.';    
                }else if($this->reportId=="12"){
                
                $html .= '<br><br>This report details the performance across centres within '.$this->aqsData['network_name'].' organization.';    
                }
		return $html;
	}
	function teacher_Recomm($assessmentId){
		$diagnosticModel = new diagnosticModel ();
		$celebrateBlock = "";
		$ImproveBlock = "";
		
		$celebrateData = $diagnosticModel->getAssessorKeyNotesType ( $assessmentId, 'celebrate' );
		$celebrateBlock = "<b>Celebrate:</b><ul>";
		foreach ( $celebrateData as $cel )
			$celebrateBlock .= "<li>" . $cel ['text_data'] . "</li>";
		$celebrateBlock .= "</ul>";
		
		$improveData = $diagnosticModel->getAssessorKeyNotesType ( $assessmentId, 'recommendation' );
		$ImproveBlock = "<b>Recommendations:</b><ul>";
		foreach ( $improveData as $improve )
			$ImproveBlock .= "<li>" . $improve ['text_data'] . "</li>";
		$ImproveBlock .= "</ul>";
				
		return $celebrateBlock.'<br/>'.$ImproveBlock;		
	}
}