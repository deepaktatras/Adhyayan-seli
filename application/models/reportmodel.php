<?php

class reportModel extends Model{
	function getReportOutput($reportId=0){
		
	}
	static function getRecommendationRow($type,$instance_id,$sno=1,$text=''){
		$removeBtn = $sno>1?'<a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a>':'';
		$html = "<tr class='recRow'>";
		$html .= "<td class='s_no'>$sno</td>
		<td><textarea class='form-control' name='recommendations_".$type."[".$instance_id."][]' id='recommendation_".$type."_".$sno."' placeholder='Please enter your recommendation here' required>".$text."</textarea></td>
				<td>".$removeBtn."</td>";
		$html .= "</tr>";
		return $html;
	}	
	function saveTeacherOverviewRecommendations($group_assessment_id,$diagnostic_id,$instance_type,$instance_id,$recommendations,$dept_id=Null){	
		if($this->db->insert('h_group_assessment_recommendations',array("group_assessment_id"=>$group_assessment_id,"diagnostic_id"=>$diagnostic_id,$instance_type."_instance_id"=>$instance_id,"recommendations"=>$recommendations,"dept_id"=>$dept_id)))
			return $this->db->get_last_insert_id();		
		return false;
	}
	function updateTeacherOverviewRecommendations($group_assessment_id,$diagnostic_id,$instance_type,$instance_id,$recommendations,$dept_id=Null){		
		if($dept_id!=Null){
                if($this->db->update('h_group_assessment_recommendations',array("recommendations"=>$recommendations,"dept_id"=>$dept_id),array("group_assessment_id"=>$group_assessment_id,$instance_type."_instance_id"=>$instance_id,"diagnostic_id"=>$diagnostic_id,"dept_id"=>$dept_id)))
			return true;
		return false;    
                }else{
                if($this->db->update('h_group_assessment_recommendations',array("recommendations"=>$recommendations),array("group_assessment_id"=>$group_assessment_id,$instance_type."_instance_id"=>$instance_id,"diagnostic_id"=>$diagnostic_id)))
			return true;
		return false;
                }
	}
	function isExistingRecommendation($group_assessment_id,$diagnostic_id,$instance_type,$instance_id,$dept_id=Null){		
		$sql = "SELECT * from h_group_assessment_recommendations where group_assessment_id=? and {$instance_type}_instance_id=? and diagnostic_id=?";		
		
                if($dept_id>=0 && $dept_id!=Null){
                     $sql.=" &&  dept_id =? "; 
                }
              
                if($dept_id>=0  && $dept_id!=Null){
                $res = $this->db->get_row($sql,array($group_assessment_id,$instance_id,$diagnostic_id,$dept_id));    
                }else{
                $res = $this->db->get_row($sql,array($group_assessment_id,$instance_id,$diagnostic_id));
                }
		return $res?$res:0;
	}
}