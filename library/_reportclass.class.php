<?php

class reportClass extends reportModel{
	
	function reportClass($assessment_id,$report_id,$conductedDate='',$validDate=''){
		parent::__construct($assessment_id,$report_id,$conductedDate,$validDate);
	}
	
	public function generateOutput(){
		switch($this->reportId){
			case 1:
				return $this->generateAqsOutput();
			case 2:
				return $this->generate7thKpaAqsOutput();
			case 3:
				return $this->generateRecommendationOutput();
			case 5:
				return $this->generateTeacherAssessmentOutput();
		}
	}
	
	protected function generateAqsOutput(){
		$this->loadAqsData();
		$this->loadAwardScheme();
		$this->loadJudgementalStatements();
		$this->loadCoreQuestions();
		$this->loadKeyQuestions();
		$this->loadKpas();
		$this->loadAward();
		
		
		$this->aqsInfo='<div class="bigBold">{schoolName}, {schoolAddress}</div>
		<div class="reportInfo">A compilation of scores based on<br>School Self-Review & Evaluation (SSRE team - School Assessors)<br>And<br>School External Review & Evaluation (SERE team - Adhyayan` Assessors)<br>conducted on: {conductedOn}<br>Valid until: {validTill} </div>';
	
		$this->config['reportTitle']="ADHYAYAN REPORT CARD";
		$this->config["footerText"]="Adhyayan Quality Standard award report for {schoolName}, {dateToday} (generated on)";
	
		$this->sectionArray=array();
		$this->indexArray=array();
		
		$this->generateSection_ScoreCardForKPAs();
		$this->generateIndexAndCover();
		$output=array(
					"config"=>$this->config,
					"data"=>$this->sectionArray
				);
		return $output;
	}
	
	protected function generate7thKpaAqsOutput(){
		$this->loadAqsData();
		$this->loadAwardScheme();
		$this->loadJudgementalStatements(true);
		$this->loadCoreQuestions(true);
		$this->loadKeyQuestions(true);
		$this->loadKpas(true);
				
		$this->aqsInfo='<div class="bigBold">{schoolName}, {schoolAddress}</div>
		<div class="reportInfo">A compilation of scores based on<br>School Self-Review & Evaluation (SSRE team - School Assessors)<br>And<br>School External Review & Evaluation (SERE team - Adhyayan` Assessors)<br>conducted on: {conductedOn}<br>Valid until: {validTill} </div>';
	
		$this->config['reportTitle']="ADHYAYAN REPORT CARD";
		$this->config["footerText"]="Adhyayan Quality Standard award report for {schoolName}, {dateToday} (generated on)";
	
		$this->sectionArray=array();
		$this->indexArray=array();
		if(count($this->kpas)){
			$kpa=current($this->kpas);
			reset($this->kpas);
			$this->awardName=isset($kpa['externalRating'])?$kpa['externalRating']['rating']:'';
			$this->generateSection_ScoreCardForKPAs();
		}
		$this->generateIndexAndCover();
		$output=array(
					"config"=>$this->config,
					"data"=>$this->sectionArray
				);
		return $output;
	}
	
	protected function generateRecommendationOutput(){
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
		<div class="recomCoverBlock">
			<div class="bigText">Adhyayan Quality Standard Report</div><br><br><br><br>
			<div class="mediumText">Recommendations for School Improvement following the award of </div><br><br>
			<div class="mBigText">{awardName}</div><br>
			<div class="">Valid until: {validTill}</div><br><br><br>
			<div class="mediumText">To</div><br><br><br>
			<div class="mBigText">{schoolName}, {schoolAddress}</div><br>
			<div class="">Based on Adhyayan\'s School External Review and Evaluation (SERE)</div>
		</div>';
	
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
	
	function generateTeacherAssessmentOutput(){
		$this->loadAqsData();
		$this->loadAwardScheme();
		$this->loadJudgementalStatements();
		$this->loadCoreQuestions();
		$this->loadKeyQuestions();
		$this->loadKpas();
		$this->loadTeacherData();
		$this->loadAssessmentObject();
		$this->loadAward();
		
		$this->aqsInfo='<div class="reportInfo"><b>A compilation of scores based on<br>Teacher Self-Review & Evaluation<br>&<br>Teacher External Review & Evaluation</b></div>';
	
		$this->config['reportTitle']="ADHYAYAN REPORT CARD";
		$this->config["footerText"]="Adhyayan Quality Standard award report for {schoolName}, {dateToday} (generated on)";
	
		$this->sectionArray=array();
		$this->indexArray=array();
		
		$this->generateSection_ScoreCardForKPAs(1);
		$this->generateSection_ComGraphForTA();
		$this->generateIndexAndCover();
		$output=array(
				"config"=>$this->config,
				"data"=>$this->sectionArray
			);
		return $output;
	}
}