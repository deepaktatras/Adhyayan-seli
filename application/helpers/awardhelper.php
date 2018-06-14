<?php
class awardHelper {
	function awardAction() {
		$awardModel = new awardModel ();
		$awardModel->createData ();
		$assessments = $awardModel->getAssessmentIds ();		
		foreach ( $assessments as $assessment ) {
			$aId = $assessment ['assessment_id'];
			$kpas = $awardModel->getData ( $aId );
			// print_r($kpas);die;
			$temp = array ();
			$tier = '';
			// print_r($kpas);
			$this->findKey ( $kpas, 'internalRating' ) == true ? $this->computeAward ( $aId, 'internalRating', $kpas ) : '';
			
			$this->findKey ( $kpas, 'externalRating' ) == true ? $this->computeAward ( $aId, 'externalRating', $kpas ) : '';
		}
	}
	function calculateSchoolAssessmentAwardValue($compulsoryKpaScore1, $compulsoryKpaScore2, $noOfScore1234InKpas, $tier) {
		$matrix = new schoolAssessmentAwardMatrix ( $compulsoryKpaScore1, $compulsoryKpaScore2, $noOfScore1234InKpas, $tier );
		return $matrix->firstLevel ();
	}
	function computeAward($aId, $type, $kpas) {
		$awardModel = new awardModel ();
		$temp = array ();
		$tier = '';
		foreach ( $kpas as $kpa ) {
			// print_r($kpa);
			$tier = $kpa ['tier_id'];
			$temp [] = $kpa [$type] ['score'];
		}
		$compulsoryKpaScore1 = $temp [0]; // We have assumes that L & T KPA are the top two KPAs. So we are hard coding it. We need to find a better way.
		$compulsoryKpaScore2 = $temp [1];
		$noOfScore1234InKpas = array_count_values ( $temp );		
		$awardNo = $this->calculateSchoolAssessmentAwardValue ( $compulsoryKpaScore1, $compulsoryKpaScore2, $noOfScore1234InKpas, $tier );		
		$awardName = $awardModel->getAwardName ( $aId, $awardNo );
		$type == 'internalRating' ? $awardModel->storeInternalAward ( $aId, $awardNo ) : '';
		$type == 'externalRating' ? $awardModel->storeExternalAward ( $aId, $awardNo ) : '';		
	}
	function findKey($array, $key) {
		foreach ( $array as $item )
			if (isset ( $item [$key] ))
				return true;
		return false;
	}
}