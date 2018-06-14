<?php

class reportModel{
	var $db;
	var $aqsInfo='';
	
	var $config=array(
		"reportTitle"=>"",
		"containerID"=>"reportContainer",
		
		"pageHeight"=>1200,
		"pageWidth"=>827,
		"pageLeftRightPadding"=>20,
		"bodyTopBottomPadding"=>10,
		
		"coverHeaderHeight"=>180,
		"coverHeaderPadding"=>40,
		"coverAddress"=>'Adhyayan Quality Education Services Private Limited<br>A-17, 1st Floor, Royal Industrial Estate, Sewree Wadala Cross Road, Wadala West, Mumbai - 400 031 <br>Email: info@adhyayan.asia | Website: www.adhyayan.asia<br>22 2417 44 63 | 22 2417 44 63',
		
		"headerImg"=> "/assets/images/Rep-logo.png",
		"headerHeight"=>90,
		"headerBG"=>"#6c0d10",
		"headerPadding"=>15,
		
		"footerHeight"=>40,
		"pageNoBarHeight"=>25,
		"footerBG"=>"#29201a",
		"footerColor"=>"#908076",
		"footerText"=>""
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
	var $awardSchemes=array();
	var $aqsTeam=array();
	var $recommendationText=array();
	var $teacherInfo=array();
	var $assessmentObject=array();
	var $reportId=0;
	var $validDate = '';
	var $conductedDate = '';
	var $errorArray=array();
	
	function reportModel($assessmentId,$reportId,$conductedDate='',$validDate=''){
		$this->db=db::getInstance();
		$this->assessmentId=$assessmentId;
		$this->reportId=$reportId;
		$this->conductedDate=$conductedDate;
		$this->validDate=$validDate;
	}
	
	protected function loadAqsData(){
		if(empty($this->aqsData)){
			$sql="
				select school_name,principal_name,school_address,principal_phone_no,b.award_scheme_id,date(c.publishDate) as publishDate,date(valid_until) as valid_until,b.tier_id
				from d_AQS_data a
				inner join d_assessment b on a.id = b.aqsdata_id
				left join h_assessment_report c on b.assessment_id = c.assessment_id and c.report_id= $this->reportId
				where b.assessment_id = ?  
				group by a.id;";
			$this->aqsData=$this->db->get_row($sql,array($this->assessmentId));
			$this->conductedDate=empty($this->aqsData['publishDate'])?$this->conductedDate:implode("-",array_reverse(explode("-",substr($this->aqsData['publishDate'],0,7))));
			$this->validDate=empty($this->aqsData['valid_until'])?$this->validDate:implode("-",array_reverse(explode("-",substr($this->aqsData['valid_until'],0,7))));
		}
	}
	
	protected function loadAwardScheme(){
		if(empty($this->awardSchemes) && $this->aqsData['award_scheme_id']>0){
			$sql="SELECT a.award_id,a.award_name,s.`order` FROM `h_award_scheme` s 
					inner join d_award a on s.award_id=a.award_id
					where s.award_scheme_id= ?
					order by s.`order`";
			$this->awardSchemes=$this->db->array_col_to_key($this->db->get_results($sql,array($this->aqsData['award_scheme_id'])),'order');
		}
	}
	
	protected function loadJudgementalStatements($is7thKpaReport=false){
		if(empty($this->judgementStatement)){
			$sql="
				select c.core_question_instance_id,c.judgement_statement_instance_id,judgement_statement_text,role,rating,f.order as numericRating
					 from f_score a
					 inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id 
					 inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
					 inner join d_judgement_statement d on d.judgement_statement_id = c.judgement_statement_id
					 inner join d_rating e on a.rating_id = e.rating_id
					 inner join d_assessment g on a.assessment_id = g.assessment_id
					 inner join h_kpa_diagnostic h on h.diagnostic_id = g.diagnostic_id and h.kpa_order ".($is7thKpaReport?"=":"<")."7
					 inner join h_kpa_kq i on h.kpa_instance_id = i.kpa_instance_id
					 inner join h_kq_cq j on i.key_question_instance_id = j.key_question_instance_id and j.core_question_instance_id = c.core_question_instance_id
					 inner join h_diagnostic_rating_scheme f on f.rating_id = a.rating_id and f.diagnostic_id = g.diagnostic_id
					 where a.isFinal = 1 and a.assessment_id = ?
					 order by c.`js_order` asc;";
				
			$this->judgementStatement=$this->get_section_Array($this->db->get_results($sql,array($this->assessmentId)),"judgement_statement_instance_id","core_question_instance_id");
		}
	}
	
	protected function loadCoreQuestions($is7thKpaReport=false){
		if(empty($this->coreQuestions)){
			$sql="select c.key_question_instance_id,a.core_question_instance_id,core_question_text,rating,role,h.order as numericRating
					 from h_cq_score a
					 inner join h_kq_cq c on a.core_question_instance_id = c.core_question_instance_id
					 inner join d_core_question d on d.core_question_id = c.core_question_id
					 inner join d_rating e on a.d_rating_rating_id = e.rating_id
					 inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					 inner join d_assessment g on a.assessment_id = g.assessment_id
					 inner join h_kpa_diagnostic i on i.diagnostic_id = g.diagnostic_id and i.kpa_order ".($is7thKpaReport?"=":"<")."7
					 inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and c.key_question_instance_id = j.key_question_instance_id
					 inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = g.diagnostic_id
					 where a.assessment_id = ?
					 order by c.`cq_order` asc;";
			$this->coreQuestions=$this->get_section_Array($this->db->get_results($sql,array($this->assessmentId)),"core_question_instance_id","key_question_instance_id");
		}
	}
	
	protected function loadKeyQuestions($is7thKpaReport=false){
		if(empty($this->keyQuestions)){
			$sql="select c.kpa_instance_id,a.key_question_instance_id,key_question_text,rating,role, h.order as numericRating
					from h_kq_instance_score a
					inner join h_kpa_kq c on a.key_question_instance_id = c.key_question_instance_id
					inner join d_key_question d on d.key_question_id = c.key_question_id
					inner join d_rating e on a.d_rating_rating_id = e.rating_id
					inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					inner join d_assessment g on a.assessment_id = g.assessment_id
					inner join h_kpa_diagnostic i on i.diagnostic_id = g.diagnostic_id and i.kpa_instance_id=c.kpa_instance_id and i.kpa_order ".($is7thKpaReport?"=":"<")."7
					inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = g.diagnostic_id
					where a.assessment_id = ?
					order by c.`kq_order` asc;";
			$this->keyQuestions=$this->get_section_Array($this->db->get_results($sql,array($this->assessmentId)),"key_question_instance_id","kpa_instance_id");
		}
	}
	
	protected function loadKpas($is7thKpaReport=false){
		if(empty($this->kpas)){
			$sql="select a.kpa_instance_id,KPA_name,rating,role,h.order as numericRating
					from h_kpa_instance_score a
					inner join h_kpa_diagnostic c on a.kpa_instance_id = c.kpa_instance_id and c.kpa_order ".($is7thKpaReport?"=":"<")."7
					inner join d_kpa d on d.kpa_id = c.kpa_id
					inner join d_rating e on a.d_rating_rating_id = e.rating_id
					inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id 
					inner join d_assessment g on a.assessment_id = g.assessment_id
					inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = g.diagnostic_id
					where a.assessment_id = ?
					order by c.`kpa_order` asc;";
			$this->kpas=$this->get_section_Array($this->db->get_results($sql,array($this->assessmentId)),"kpa_instance_id");
		}
	}
	
	protected function loadAward(){
		if(empty($this->awardNo)){
			if(count($this->kpas)){
				switch($this->reportId){
					case 1:
					case 2:
					case 3:
						$temp=array();
						foreach($this->kpas as $kpa){
							$temp[]=$kpa['externalRating']['score'];
						}
						$compulsoryKpaScore1=$temp[0]; //We have assumes that L & T KPA are the top two KPAs. So we are hard coding it. We need to find a better way.
						$compulsoryKpaScore2=$temp[1];
						$this->noOfScore1234InKpas=array_count_values($temp);
						
						$this->awardNo=$this->calculateSchoolAssessmentAwardValue($compulsoryKpaScore1,$compulsoryKpaScore2);
						$sql="select replace(replace(award_name_template,'<Award>',award_name),'<Tier>',standard_name)
							 from d_assessment a
							 inner join h_award_scheme b on a.award_scheme_id = b.award_scheme_id
							 inner join d_award_scheme c on c.award_scheme_id = a.award_scheme_id
							 inner join d_award d on d.award_id = b.award_id
							 left join d_tier e on e.standard_id = b.tier_id
							where assessment_id = ? and b.order = $this->awardNo;";
						$this->awardName=$this->db->get_var($sql,array($this->assessmentId));
						break;
					case 5:
						$noOf3n4inEachCQ=array();
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
						$awards=array(4=>"Outstanding",3=>"Proficient",2=>"Developing",1=>"Emerging",0=>"Needs attention");
						if($minVal==1 && min($noOf3n4inEachKQ)==0){
							$minVal=0;
						}
						$this->awardNo=$minVal;
						$this->awardName=$awards[$minVal];
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
			$sql="select c.judgement_statement_instance_id,recommendation_text,a.rating_id
				from f_score a
				inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id and role = 4 
				inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
				inner join d_judgement_statement d on d.judgement_statement_id = c.judgement_statement_id
				inner join d_rating e on a.rating_id = e.rating_id
				inner join d_assessment g on a.assessment_id = g.assessment_id
				inner join h_diagnostic_rating_scheme f on f.rating_id = a.rating_id and f.diagnostic_id = g.diagnostic_id
				inner join h_jstatement_recommendation h on h.rating_id = a.rating_id and h.judgement_statement_id = d.judgement_statement_id and h.isActive=1
				inner join d_recommendation i on i.recommendation_id = h.recommendation_id 
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
	
	private function calculateSchoolAssessmentAwardValue($compulsoryKpaScore1,$compulsoryKpaScore2){
		$matrix=new schoolAssessmentAwardMatrix($compulsoryKpaScore1,$compulsoryKpaScore2,$this->noOfScore1234InKpas,$this->aqsData['tier_id']);
		return $matrix->firstLevel();
	}
	
	protected function addIndex($text){
		$indexKey=count($this->indexArray);
		$this->indexArray[$indexKey]=$text;
		return $indexKey+1;
	}
	
	protected function generateIndexAndCover(){
		$sections=array();
		$coverSection=array("sectionBody"=>array());
		$keysToReplace=array("{schoolName}","{schoolAddress}","{conductedOn}","{validTill}","{awardName}","{dateToday}");
		$valuesToReplace=array($this->aqsData['school_name'],$this->aqsData['school_address'],$this->conductedDate,$this->validDate,$this->awardName,date("d-m-Y"));
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
		
		switch($this->reportId){
			case 1:
			case 2:
				$awardBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array("Name of the Principal",array("text"=>$this->aqsData['principal_name'],"style"=>"textBold")),
								array("Adhyayan Quality Standard Awarded",array("text"=>$this->awardName,"style"=>"blueColor textBold"))
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
						array('Email contact',$this->teacherInfo['email']['value']),
						array("Overall grade awarded",$this->awardName)
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
						array('Principal Name',$this->aqsData['principal_name']),
						array("Your educational qualification",$this->teacherInfo['qualification']['value']),
						array('Total years of teaching experience',$this->teacherInfo['experience']['value']==0?'Less than 1':$this->teacherInfo['experience']['value']),
						array('School joining year',$this->teacherInfo['joinning_year']['value']),
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
		$block['blockBody']['dataArray'][]=array("Date of self-review",$this->assessmentObject[3]['ratingInputDate']);
		$block['blockBody']['dataArray'][]=array("Date of external review",$this->assessmentObject[4]['ratingInputDate']);
		$blocks[]=$block;
		
		return $blocks;
	}
	
	protected function generateSection_ScoreCardForKPAs($skipComparisonSection=0){
		$totalKpas=count($this->kpas);
		$comparisonSection=array();
		if($skipComparisonSection==0){
			$indexKey=$this->addIndex("Comparison of Assessments ".($totalKpas>1?'across':'of')." ".$totalKpas." Key Performance Areas");
			$comparisonSection=array("sectionHeading"=>array("text"=>"1. Comparison of Assessments across ".($totalKpas>1?$totalKpas:'')." Key Performance Areas ","style"=>"greyHead"),"sectionBody"=>array(),"indexKey"=>$indexKey);
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
			
			$kpaValuesForGraph[]=array("values"=>array(isset($kpa['internalRating'])?$kpa['internalRating']['score']:0,isset($kpa['externalRating'])?$kpa['externalRating']['score']:0),"text"=>"");
			
			$kpaComparisonBlock['blockBody']['dataArray'][]=array(
																$kpa_count,
																$kpa['KPA_name'],
																'<span class="'.(isset($kpa['internalRating'])?"score-".$kpa['internalRating']['score']:"").'">'.(isset($kpa['internalRating'])?$kpa['internalRating']['rating']:"").'</span>',
																'<span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"")."</span>"
																);
			$indexKey=$this->addIndex("Score card for KPA$kpa_count - ".$kpa['KPA_name']);
	
			$section=array("sectionHeading"=>array("text"=>"Key Peformance Area (KPA $kpa_count) - ".$kpa['KPA_name'],"style"=>"greyHead"),"sectionBody"=>array(),"indexKey"=>$indexKey);
			if($this->reportId!=5){
				$kpaBlock=array(
							"blockBody"=>array(
								"dataArray"=>array(
									array(
										"Assessment for ".$kpa['KPA_name'],
										'<div class="pull-left">Self-Review Rating (SSRE)<br>External Review Rating (SERE)</div><div class="pull-left"><span class="'.(isset($kpa['internalRating'])?"score-".$kpa['internalRating']['score']:"").'">'.(isset($kpa['internalRating'])?$kpa['internalRating']['rating']:"").'</span><br><span class="'.(isset($kpa['externalRating'])?"score-".$kpa['externalRating']['score']:"").'">'.(isset($kpa['externalRating'])?$kpa['externalRating']['rating']:"")."</span></div>")
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
										array("<span>Self-Review Rating (SSRE)</span>"),
										array("<span>External Review Rating (SERE)</span>"),
										array("<span>Judgement Distance</span>")
									)
								),
							"style"=>"bordered kpaStyle".($this->reportId==5?' mb25':''),
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
							$jsBlock['blockBody']['dataArray'][0][]=array("text"=>"<span class=\"cQn\">$coreQ_count. ".$coreQ['core_question_text'].'</span>',"cSpan"=>3);
							$satatement_count=0;
							foreach($this->judgementStatement[$coreQ['core_question_instance_id']] as $statment){
								$satatement_count++;
								$jsBlock['blockBody']['dataArray'][1][]=$coreQsInKPA.$numberToAlpha[$satatement_count];
								$jsBlock['blockBody']['dataArray'][2][]=array("text"=>isset($statment['internalRating'])?$statment['internalRating']['rating']:"","style"=>"colSize-1");
								$jsBlock['blockBody']['dataArray'][3][]=array("text"=>isset($statment['externalRating'])?$statment['externalRating']['rating']:"");
								$jsBlock['blockBody']['dataArray'][4][]=isset($statment['externalRating']) && isset($statment['internalRating'])?$statment['internalRating']['score']-$statment['externalRating']['score']:"";
							}
							$cqBlock['blockBody']['dataArray'][0][]=array("text"=>'<span class="'.(isset($coreQ['internalRating'])?"score-".$coreQ['internalRating']['score']:"").'">'.(isset($coreQ['internalRating'])?$coreQ['internalRating']['rating']:'').'</span>',"style"=>"colSize-3");
							$cqBlock['blockBody']['dataArray'][1][]='<span class="'.(isset($coreQ['externalRating'])?"score-".$coreQ['externalRating']['score']:"").'">'.(isset($coreQ['externalRating'])?$coreQ['externalRating']['rating']:'').'</span>';
						}
					}
					$kqBlock['blockBody']['dataArray'][0][]='<span class="'.(isset($keyQ['internalRating'])?"score-".$keyQ['internalRating']['score']:"").'">'.(isset($keyQ['internalRating'])?$keyQ['internalRating']['rating']:'').'</span>';
					$kqBlock['blockBody']['dataArray'][1][]='<span class="'.(isset($keyQ['externalRating'])?"score-".$keyQ['externalRating']['score']:"").'">'.(isset($keyQ['externalRating'])?$keyQ['externalRating']['rating']:'').'</span>';
					$section['sectionBody'][]=$jsBlock;
					if($this->reportId!=5){
						$section['sectionBody'][]=$cqBlock;
						$section['sectionBody'][]=$kqBlock;
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
			$keysBodyBlock=array(
						"blockBody"=>array(
							"dataArray"=>array(
								array("Always","There is evidence of robust systems of good practice across all sections from the beginning of the academic year to the end."),
								array("Mostly","There is evidence of systemic good practice in most of the sections for most of the year."),
								array("Sometimes","There is some evidence of good practice in some sections, sometimes of the year."),
								array("Rarely","There is rare or no occurrence of good practice in the school.")
							)
						),
					"style"=>"bordered keyInfoBlock",
					"config"=>array('groupby'=>"keyInfoBlock")
				);
			$comparisonSection['sectionBody'][]=$keysBodyBlock;
			$this->sectionArray=array_merge($this->sectionArray,array($comparisonSection),$kpaSectionArray);
		}else{
			$this->sectionArray=array_merge($this->sectionArray,$kpaSectionArray);
		}
	}
	
	protected function getGraphHTML($valuesArray,$steps,$maxValue,$minValue=0,$barDes=array(),$infoBelowGraph="",$infoOnYAxis=""){
		$lnth=count($valuesArray);
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
		$totalHeight=$topBarHeight+$bottomBarHeight+$graphHeight;
		$html='<div class="graphWrap">
		<div style="height:'.$totalHeight.'px" class="stepDesc">';
		foreach($steps as $k=>$step){
			$html.='<div style="top:'.(($maxValue-$k)*$oneStepHeight+$topBarHeight+$extraTopSpaceInGraph-15).'px;" class="graphSteps"><span class="score-'.$k.'">'.$step.'</span></div>';
		}
		$html.='
			<div class="infoOnYAxis" style="margin-top:'.($totalHeight/2).'px;">'.$infoOnYAxis.'</div>
		</div>
		<div style="height:'.$totalHeight.'px" class="theBarGraph">
			<table style="margin-top:'.$topBarHeight.'px;height:'.$graphHeight.'px" class="theBarGraphTbl">
		';
		$barNamesTbl='';
		$addBarNames=false;

		$widthOfColumn=100/($cols*$lnth + $lnth -1); //total no. of columns + no. of space columns (in %)
		$widthOfBarNameCol=floor(10000/$lnth)/100;
		for($i=0;$i<$lnth;$i++){
			if($i>0){
				$html.='<td style="width:'.$widthOfColumn.'%;"></td>';
				//$barNamesTbl.='<div class="barNameCol" style="width:'.$widthOfColumn.'%;">&nbsp;</div>';
			}
			for($j=0;$j<$cols;$j++){
				$height=$oneStepHeight*(isset($valuesArray[$i]['values'][$j])?$valuesArray[$i]['values'][$j]-$minValue:0);
				$html.='<td '.(isset($valuesArray[$i]['empty']) && $valuesArray[$i]['empty']?'class="emptyBar"':'').' style="width:'.$widthOfColumn.'%;">
					<div class="barStyle-'.$j.' gScore-'.(isset($valuesArray[$i]['values'][$j])?$valuesArray[$i]['values'][$j]:0).'">
						<div class="barBody">
							<div class="barBodyLeft">
								<div class="barBodyRight" style="height:'.($height>0?$height+1:0).'px;">
									<div class="barHead">
										<div class="barHeadLeft">
											<div class="barHeadRight"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</td>';
			}
			$barNamesTbl.='<div class="barNameCol" style="width:'.$widthOfBarNameCol.'%;">';
			if(isset($valuesArray[$i]['name']) && $valuesArray[$i]['name']!=""){
				$addBarNames=true;
				$barNamesTbl.=$valuesArray[$i]['name'];
			}
			$barNamesTbl.='</div>';
		}
		
		$html.='
			</table>
			'.($addBarNames?'<div style="margin-left:-'.($widthOfColumn/2).'%;margin-right:-'.($widthOfColumn/2).'%;" class="barNameWrap">'.$barNamesTbl.'<div class="clear"></div></div>':'').'
			<div class="infoBelowGraph">'.$infoBelowGraph.'</div>
		</div>
		<div class="barDesc" style="margin-top:'.($totalHeight/2).'px">
		';
		
		for($i=0;$i<count($barDes);$i++){
			$html.='
			<div class="barDesc-'.$i.' barDescItem">'.$barDes[$i].'</div>
			';
		}
		
		$html.='
		</div>
			<div style="clear:both;"></div>
		</div>';
		return $html;
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
							array(array("text"=>"&nbsp;","style"=>"score-bg-4"),"Outstanding","These good practices are consistently embedded in the culture of the school, and are evident throughout the year and across all classes."),
							array(array("text"=>"&nbsp;","style"=>"score-bg-3"),"Good","There are many examples of good practice that help to create the culture of the school. These are evidence throughout the year and across most classes."),
							array(array("text"=>"&nbsp;","style"=>"score-bg-2"),"Variable","While some examples of good practice exist, these are not consistently evident across the school or the year."),
							array(array("text"=>"&nbsp;","style"=>"score-bg-1"),"Needs Attention","Something action needs to be taken immediately. There is significant variability of practice within and across the school.")
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
								$internalPoints[]=(7.5 * $js_cnt_in_kq).",".(95 - (20*(isset($statment['internalRating'])?$statment['internalRating']['score']:0)));
								$externalPoints[]=(7.5 * $js_cnt_in_kq).",".(95-(20*(isset($statment['externalRating'])?$statment['externalRating']['score']:0)));
								$bottomLabels.='<text x="'.(7.5 * $js_cnt_in_kq - 1.5).'" y="100" font-size="5" fill="#000000">'.$js_cnt_in_kq.'</text>';
								if(isset($statment['internalRating']) && isset($statment['externalRating'])){
									$diffInScore[$statment['internalRating']['score']>$statment['externalRating']['score']?$statment['internalRating']['score']-$statment['externalRating']['score']:$statment['externalRating']['score']-$statment['internalRating']['score']]++;
								}
							}
						}
					}
					$angles=array(360,0,0,0);
					$sum_of_diffInScore=array_sum($diffInScore);
					if($sum_of_diffInScore==9){
						$angles=array($diffInScore[0]*40,$diffInScore[1]*40,$diffInScore[2]*40,$diffInScore[3]*40);
					}
					$pieChart='<svg height="180px" viewBox="0 0 100 100"  preserveAspectRatio="none" width="180px" style="margin-top:5px;">';
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
						$PCLabels.='<div class="pieLbRow"><div style="background-color:'.$colors[$i].'" class="pieLbColor"></div><div class="pieLbText">'.$labels[$i].'</div></div>';
						$i++;
					}
					
					$pieChart.='</svg>
						<div class="pieLb">'.$PCLabels.'</div>';
					
					$block=array(
						"blockHeading"=>array("data"=>array(array("text"=>"<strong>Key Question $kq_cnt:-</strong> ".$keyQ['key_question_text'],"cSpan"=>2,"style"=>"redHead"))),
						"blockBody"=>array(
							"dataArray"=>array(
								array(
									'<svg height="180" viewBox="0 0 100 100"  preserveAspectRatio="none" width="100%">
										<line x1="5" y1="5" x2="5" y2="95" style="stroke:#000000;stroke-width:0.2" />
										<line x1="5" y1="95" x2="70" y2="95" style="stroke:#000000;stroke-width:0.2" />
										
										<line x1="5" y1="75" x2="70" y2="75" style="stroke:#000000;stroke-width:0.2" />
										<line x1="5" y1="55" x2="70" y2="55" style="stroke:#000000;stroke-width:0.2" />
										<line x1="5" y1="35" x2="70" y2="35" style="stroke:#000000;stroke-width:0.2" />
										<line x1="5" y1="15" x2="70" y2="15" style="stroke:#000000;stroke-width:0.2" />
										
										<polyline points="'.implode(" ",$internalPoints).'" style="fill:transparent;stroke:red;stroke-width:1.4" />
										<polyline points="'.implode(" ",$externalPoints).'" style="fill:transparent;stroke:#7C7EB3;stroke-width:1.4" />
										<text x="80" y="40" font-size="4.5" fill="#000000">KQ'.$kq_cnt.'SSRE</text>
										<text x="80" y="50" font-size="4.5" fill="#000000">KQ'.$kq_cnt.'SERE</text>
										<line x1="72" y1="39" x2="79" y2="39" style="stroke:red;stroke-width:0.7" />
										<line x1="72" y1="49" x2="79" y2="49" style="stroke:#7C7EB3;stroke-width:0.7" />
										
										'.$bottomLabels.'
										<text x="0" y="97" font-size="5" fill="#000000">0</text>
										<text x="0" y="77" font-size="5" fill="#000000">1</text>
										<text x="0" y="57" font-size="5" fill="#000000">2</text>
										<text x="0" y="37" font-size="5" fill="#000000">3</text>
										<text x="0" y="17" font-size="5" fill="#000000">4</text>
									</svg>',
									$pieChart),
								array(array("text"=>"Fig".(++$figCount).": The SSRE and SERE scores for Key Question $kq_cnt","style"=>"fs-sm"),array("text"=>"Fig".(++$figCount).": Percentage of agreement and disagreements between SSRE and SERE scores in Key Question $kq_cnt","style"=>"fs-sm"))
							)
						),
						"style"=>"bordered mt20 kpablock",
						"config"=>array("minRows"=>3)
					);
					
					$section['sectionBody'][]=$block;
				}
			}
		}
		$this->sectionArray=array_merge($this->sectionArray,array($section));
	}
}