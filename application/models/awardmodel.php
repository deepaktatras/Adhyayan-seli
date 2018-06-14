<?php
class awardModel extends Model {
	function createData($lang_id=9) {
		$sql = "drop table if exists assessor_rating;
		create temporary table assessor_rating
		select g.assessment_id,a.kpa_instance_id,hlt.translation_text as KPA_name,e.rating,role,h.order as numericRating,g.tier_id
							from h_kpa_instance_score a
							inner join h_kpa_diagnostic c on a.kpa_instance_id = c.kpa_instance_id and c.kpa_order <7
							inner join d_kpa d on d.kpa_id = c.kpa_id
                                                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id  
							inner join (select lt.translation_text as rating,r.rating_id from d_rating r inner join h_lang_translation lt on r.equivalence_id = lt.equivalence_id  where lt.language_id=?) e on a.d_rating_rating_id = e.rating_id
							inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id and f.isFilled=1
							inner join d_assessment g on a.assessment_id = g.assessment_id
							inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = g.diagnostic_id
		                    inner join d_diagnostic i on i.diagnostic_id = c.diagnostic_id and i.assessment_type_id=1							
		                    where g.d_sub_assessment_type_id!=1
							order by g.assessment_id,c.`kpa_order` asc;";
		
		if (! $this->db->query ( $sql ,array($lang_id)))
			return 'fail';
		return 'success';
	}
	function createDataSingleAssessment($assessmentId,$lang_id=9) {
		$sql = "drop table if exists assessor_rating;
		create temporary table assessor_rating
		select g.assessment_id,a.kpa_instance_id,hlt.translation_text as KPA_name,e.rating,role,h.order as numericRating,g.tier_id
							from h_kpa_instance_score a
							inner join h_kpa_diagnostic c on a.kpa_instance_id = c.kpa_instance_id and c.kpa_order <7
							inner join d_kpa d on d.kpa_id = c.kpa_id
                                                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id
							inner join (select lt.translation_text as rating,r.rating_id from d_rating r inner join h_lang_translation lt on r.equivalence_id = lt.equivalence_id   where lt.language_id=? ) e on a.d_rating_rating_id = e.rating_id
							inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id and f.isFilled=1
							inner join d_assessment g on a.assessment_id = g.assessment_id
							inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = g.diagnostic_id
		                    inner join d_diagnostic i on i.diagnostic_id = c.diagnostic_id and i.assessment_type_id=1
		                    where g.d_sub_assessment_type_id!=1 and g.assessment_id=?
							order by g.assessment_id,c.`kpa_order` asc;";
		
		if (! $this->db->query ( $sql, array (
				$lang_id,$assessmentId 
		) ))
			return false;
		return true;
	}
	function getAssessmentIds() {
		$sql = "select distinct assessment_id from assessor_rating";
		return $this->db->get_results ( $sql );
	}
	function getData($aid) {
		$sql = "select * from assessor_rating where assessment_id=?";
		$kpas = $this->get_section_Array ( $this->db->get_results ( $sql, array (
				$aid 
		) ), "kpa_instance_id" );
		return $kpas;
	}
	function get_section_Array($arr, $instanceIdKey, $groupingIdKey = "") {
		$res = array ();
		if (count ( $arr )) {
			if ($groupingIdKey == "") {
				foreach ( $arr as $v ) {
					if (isset ( $res [$v [$instanceIdKey]] )) {
						$res [$v [$instanceIdKey]] [$v ['role'] == 3 ? 'internalRating' : 'externalRating'] = array (
								"rating" => $v ['rating'],
								"score" => $v ['numericRating'] 
						);
					} else {
						$v [$v ['role'] == 3 ? 'internalRating' : 'externalRating'] = array (
								"rating" => $v ['rating'],
								"score" => $v ['numericRating'] 
						);
						unset ( $v ['numericRating'] );
						unset ( $v ['rating'] );
						unset ( $v ['role'] );
						$res [$v [$instanceIdKey]] = $v;
					}
				}
			} else {
				foreach ( $arr as $v ) {
					if (isset ( $res [$v [$groupingIdKey]] ) && isset ( $res [$v [$groupingIdKey]] [$v [$instanceIdKey]] )) {
						$res [$v [$groupingIdKey]] [$v [$instanceIdKey]] [$v ['role'] == 3 ? 'internalRating' : 'externalRating'] = array (
								"rating" => $v ['rating'],
								"score" => $v ['numericRating'] 
						);
					} else {
						$v [$v ['role'] == 3 ? 'internalRating' : 'externalRating'] = array (
								"rating" => $v ['rating'],
								"score" => $v ['numericRating'] 
						);
						unset ( $v ['numericRating'] );
						unset ( $v ['rating'] );
						unset ( $v ['role'] );
						$res [$v [$groupingIdKey]] [$v [$instanceIdKey]] = $v;
					}
				}
			}
		}
		return $res;
	}
	function getAwardName($assessmentId, $awardNo, $lang_id=9) {
		$sql = "select replace(replace(award_name_template,'<Tier>',standard_name ),'<Award>',hlt.translation_text)
		from d_assessment a
		inner join h_award_scheme b on a.award_scheme_id = b.award_scheme_id
		inner join d_award_scheme c on c.award_scheme_id = a.award_scheme_id
		inner join d_award d on d.award_id = b.award_id
                inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id
		left join d_tier e on e.standard_id = b.tier_id
		where assessment_id = ? and hlt.language_id=? and b.order = $awardNo";
		return $this->db->get_var ( $sql, array (
				$assessmentId , $lang_id
		) );
	}
	function storeInternalAward($assessmentId, $award) {
		// /$sql = "update d_assessment set internal_award=? and external_award=?";
		$this->db->update ( "d_assessment", array (
				"internal_award" => $award 
		), array (
				"assessment_id" => $assessmentId 
		) );
	}
	function storeExternalAward($assessmentId, $award) {
		// /$sql = "update d_assessment set internal_award=? and external_award=?";
		$this->db->update ( "d_assessment", array (
				"external_award" => $award 
		), array (
				"assessment_id" => $assessmentId 
		) );
	}
}