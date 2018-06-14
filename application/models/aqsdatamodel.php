<?php
class aqsDataModel extends Model{
	
	function getReferrerList(){
		$res=$this->db->get_results("select * from d_referrer where referrer_id>0;");
		return $res?$res:array();
	}
	
	function getBoardList($country_id = '',$aqs = 0,$val = ''){
                $sql = "select * from d_board WHERE ";
                if($aqs == 1) {
                    
                    $sql .= " LOWER(board) = '$val'";
                }else {
                    $sql .= " board_id>0";
                    if($country_id != '202') {

                        $sql.= " AND board NOT IN ('CAPS','IEB') ";
                    }
                }
		$res=$this->db->get_results($sql);
		return $res?$res:array();
	}
	
	function getSchoolTypeList($aqs = 0,$val = ''){
                
                $sql = "select * from d_school_type where ";
                if($aqs == 1) {
                    
                     $sql .= " LOWER(school_type) IN ($val);";
                }else {
                     $sql .= " school_type_id>0 AND school_type NOT IN ('Minority','Unrecognised');";
                }
                //echo $sql;
		$res=$this->db->get_results($sql);
		return $res?$res:array();
	}
	function getSchoolRegionList($aqs = 0,$val = ''){
            
                $sql = "select * from d_school_region where ";
                if($aqs == 1) {
                     $sql .= " region_id = '$val'";
                }else 
                    $sql .= ' region_id>0';
		$res = $this->db->get_results($sql);
		return $res?$res:array();
	}
	
	function getSchoolItSupportList(){
		$res=$this->db->get_results("select * from d_school_it_support;");
		return $res?$res:array();
	}
	
	function getSchoolClassList($aqs=0,$val=''){
                
                $sql = "select * from d_school_class where ";
                if($aqs == 1) {
                    
                    $sql .= " LOWER(class_name) = '$val'";
                }else {
                    $sql .= ' class_id>0;';
                }
		$res=$this->db->get_results($sql);
		return $res?$res:array();
	}
	
	function getSchoolLevelList(){
		$res=$this->db->get_results("select * from d_school_level where dept_type=0;");
		return $res?$res:array();
	}
	
	function getStudentTypeList($aqs = 0,$val = ''){
                
                $sql = "select * from d_student_type where ";
                if($aqs == 1) {
                    $sql .= " student_type_id  = '$val';";
                }else {
                        $sql .= "student_type_id>0;";
                }
		$res=$this->db->get_results($sql);
		return $res?$res:array();
	}
	
	function getAqsData($assmntId_or_grpAssmntId,$assessment_type_id){
		$res=$this->db->get_row("select ad.*, group_concat(distinct it.itsupport_id) as it_support_ids, group_concat(concat(t.school_level_id,'#|',t.id,'#|',t.start_time,'#|',t.end_time) SEPARATOR '#|#') as school_timing,group_concat(distinct st.school_type_id) as school_type_ids
		from d_AQS_data ad
        ".(($assessment_type_id==1 || $assessment_type_id==5)?"inner join d_assessment a on a.aqsdata_id=ad.id and a.assessment_id=?":"inner join (select aa.* from d_assessment aa inner join h_assessment_ass_group ag on aa.assessment_id=ag.assessment_id and ag.group_assessment_id=? limit 1)  a on a.aqsdata_id=ad.id")."
		left join h_aqsdata_itsupport it on ad.id=it.aqs_id
		left join h_AQS_school_level t on t.AQS_data_id=ad.id
                left join h_assessment_school_type st on st.assessment_id=a.assessment_id
		group by ad.id",array($assmntId_or_grpAssmntId));
		if($res){
                        $res['school_type_ids']=$res['school_type_ids']!=""?explode(",",$res['school_type_ids']):array();
			$res['it_support_ids']=$res['it_support_ids']!=""?explode(",",$res['it_support_ids']):array();
			$res['school_timing']=$res['school_timing']!=""?explode("#|#",$res['school_timing']):array();
			$tmp=array();
			foreach($res['school_timing'] as $itm){
				$t=explode("#|",$itm);
				$tmp[$t[0]]=array("school_level_id"=>$t[0],"id"=>$t[1],"start_time"=>$t[2],"end_time"=>$t[3]);
			}
			$res['school_timing']=$tmp;
			return $res;
		}else
			return array();
	}
	function getAqsAdditionalData($aqsData_id,$tableName){
		$sql = "Select aaq.*,
 				(select group_concat(school_community_id) from h_aqs_school_communities where aqsdata_id=aaq.aqs_data_id) school_community_id ,
 				(select group_concat(review_medium_instrn_id) from h_aqs_medium_instruction where aqsdata_id=aaq.aqs_data_id) review_medium_instrn_id 
 				from $tableName aaq
 				where aqs_data_id=?";
		$res = $this->db->get_row($sql,array($aqsData_id));
		return $res?$res:array();
	}
	function getAqsAdditionalRefTeam($aqsData_id){
		$sql = "SELECT * FROM d_aqs_additional_references where aqsdata_id=?";
		$res = $this->db->get_results($sql,array($aqsData_id));
		return $res?$res:array();
	}
	/*function getAqsDataforDropDown($assmntId_or_grpAssmntId,$assessment_type_id){
		$res=$this->db->get_row("select ad.*, group_concat(distinct it.itsupport_id) as it_support_ids, group_concat(concat(t.school_level_id,'#|',t.id,'#|',t.start_time,'#|',t.end_time) SEPARATOR '#|#') as school_timing
		from d_AQS_data ad
        inner join d_assessment a on a.aqsdata_id=ad.id and a.assessment_id=?
		left join h_aqsdata_itsupport it on ad.id=it.aqs_id
		left join h_AQS_school_level t on t.AQS_data_id=ad.id
		group by ad.id",array($assmntId_or_grpAssmntId));
		if($res){
			$res['it_support_ids']=$res['it_support_ids']!=""?explode(",",$res['it_support_ids']):array();
			$res['school_timing']=$res['school_timing']!=""?explode("#|#",$res['school_timing']):array();
			$tmp=array();
			foreach($res['school_timing'] as $itm){
				$t=explode("#|",$itm);
				$tmp[$t[0]]=array("school_level_id"=>$t[0],"id"=>$t[1],"start_time"=>$t[2],"end_time"=>$t[3]);
			}
			$res['school_timing']=$tmp;
			return $res;
		}else
			return array();
	}*/
	
	function updateAqsData($AQS_data_id,$data){
		return $this->db->update("d_AQS_data",$data,array("id"=>$AQS_data_id));
	}
	
	function insertAqsData($data){
		if($this->db->insert("d_AQS_data",$data)){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	function insertSchoolType($ass_id, $data) {
            $condition = array('assessment_id' => $ass_id);
            $this->db->delete("h_assessment_school_type", $condition);
            if (!empty($data)) {
                $values = $this->prepareValues($ass_id, $data);
                $sql_school_type = "INSERT INTO h_assessment_school_type (assessment_id,school_type_id ) VALUES $values";
                if ($this->db->query($sql_school_type)) {
                    return $this->db->get_last_insert_id();
                } else
                    return false;
            }
        }

    function prepareValues($ass_id,$resresult){
        
            $query_values = '';
            foreach ($resresult as $data) {

                $query_values .= "(" . $ass_id . ',' . $data . "),";
            }
            return rtrim($query_values,',');
    }
	
	function getAQSTeam($AQS_data_id){
		$res=$this->db->get_results("select * from d_AQS_team where AQS_data_id=?;",array($AQS_data_id));
//		if($res){
//			$res=$this->db->array_grouping($res,"isInternal");
//			return array("school"=>isset($res[1])?$res[1]:array(),"adhyayan"=>isset($res[2])?$res[2]:array());
//		}else
//			return array("school"=>array(),"adhyayan"=>array());
                if($res){
			$res=$this->db->array_grouping($res,"isInternal");
			return array("school"=>isset($res[1])?$res[1]:array());
		}else
			return array("school"=>array());
                
	}
        
	function getAqsSchoolType($AQS_id){
            
		return $this->db->get_results("select school_type_id from h_assessment_school_type where assessment_id=?;",array($AQS_id));
	}
	
	function addItSupport($aqsData_id,$itsupport_id){
		if($this->db->insert("h_aqsdata_itsupport",array('aqs_id'=>$aqsData_id,'itsupport_id'=>$itsupport_id))){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	
	function removeItSupport($aqsData_id,$itsupport_id=0){
		$condition=array('aqs_id'=>$aqsData_id);
		if($itsupport_id>0)
			$condition['itsupport_id']=$itsupport_id;
		return $this->db->delete("h_aqsdata_itsupport",$condition);
	}
	
	function addSchoolTiming($aqsData_id,$school_level_id,$start_time,$end_time){
		if($this->db->insert("h_AQS_school_level",array('AQS_data_id'=>$aqsData_id,'school_level_id'=>$school_level_id,'start_time'=>$start_time,"end_time"=>$end_time))){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	
	function removeSchoolTiming($aqsData_id,$school_level_id=0){
		$condition=array('AQS_data_id'=>$aqsData_id);
		if($school_level_id>0)
			$condition['school_level_id']=$school_level_id;
		return $this->db->delete("h_AQS_school_level",$condition);
	}
	
	function addAqsTeam($aqsData_id,$name,$designation,$lang_id,$email,$mobile,$isSchoolteam=1,$country_code){
		$teamType=$isSchoolteam?1:2;
                
                 if(!empty($mobile)) {
                                                    
                    $mobile = "(+".$country_code.")". $mobile;
                 }
             
		if($this->db->insert("d_AQS_team",array('AQS_data_id'=>$aqsData_id,'name'=>$name,'designation_id'=>$designation,"lang_id"=>$lang_id,"email"=>$email,"mobile"=>$mobile,"isInternal"=>$teamType))){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	
	function removeAqsTeam($aqsData_id,$removeSchoolTeam=1,$id=0){//remove school team=0 is for removing adhyayan team
		$condition=array('AQS_data_id'=>$aqsData_id);
		$condition['isInternal']=$removeSchoolTeam>0?1:2;
		if($id>0)
			$condition['id']=$id;
		return $this->db->delete("d_AQS_team",$condition);
	}
	function addAdditionalRefTeam($aqsData_id,$name,$phone,$email,$role_stakeholder){		
		if($this->db->insert("d_aqs_additional_references",array('aqsdata_id'=>$aqsData_id,'name'=>$name,'phone'=>$phone,"email"=>$email,"role_stakeholder"=>$role_stakeholder))){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	function removeAdditionalRefTeam($aqsData_id){
		$condition=array('aqsdata_id'=>$aqsData_id);				
		return $this->db->delete("d_aqs_additional_references",$condition);
	}
	function removeAdditionalSchoolCommunity($aqsData_id){
		$condition =array('aqsdata_id'=>$aqsData_id);
		return $this->db->delete("h_aqs_school_communities",$condition);
	}
	function removeAdditionalMediumInstruction($aqsData_id){
		$condition =array('aqsdata_id'=>$aqsData_id);
		return $this->db->delete("h_aqs_medium_instruction",$condition);
	}
	function addAdditionalSchoolCommunity($aqsData_id,$community_id){
		return $this->db->insert("h_aqs_school_communities",array("aqsdata_id"=>$aqsData_id,"school_community_id"=>$community_id));
	}
	function addAdditionalMediumInstruction($aqsData_id,$medium_instruction_id){
		return $this->db->insert("h_aqs_medium_instruction",array("aqsdata_id"=>$aqsData_id,"review_medium_instrn_id"=>$medium_instruction_id));
	}
	function insertAdditionalQuestionsData($data){
		if($this->db->insert("d_aqs_additional_questions",$data))
			return $this->db->get_last_insert_id();
	}
	function updateAdditionalQuestionsData($aqsData_id,$data){
		return $this->db->update("d_aqs_additional_questions",$data,array("aqs_data_id"=>$aqsData_id));
	}
	function aqsTeamValidation($team,$isSchoolRow=1,$checkRequired=1,$isCollegeReview=0){
                if($isCollegeReview){
                $type=$isSchoolRow?'schoolTeam':'adhyayanTeam';
		$teamName=$isSchoolRow?'College Team':'Adhyayan Team';    
                }else{
		$type=$isSchoolRow?'schoolTeam':'adhyayanTeam';
		$teamName=$isSchoolRow?'School Team':'Adhyayan Team';
                }
                
		$errors=array();
		$values=array();
		if($checkRequired && (empty($team) || !is_array($team))){
			$errors[$type]="Fields are required: $teamName";
		}else if(!isset($team['name']) || !is_array($team['name'])){
			$errors[$type."_name"]="Name field is missing in: $teamName";
		}else if(!isset($team['designation']) || !is_array($team['name'])){
			$errors[$type."_designation"]="Designation field is missing in: $teamName";
		}else if(!isset($team['lang_id']) || !is_array($team['lang_id'])){
			$errors[$type."_language"]="Language field is missing in: $teamName";
		}/*else if(!isset($team['email']) || !is_array($team['email'])){
			$errors[$type."_email"]="Email field is missing in: $teamName";
		}else if(!isset($team['mobile']) || !is_array($team['mobile'])){
			$errors[$type."_mobile"]="Mobile field is missing in: $teamName";
		}*/else if(count($team['mobile'])==count($team['email']) && count($team['lang_id'])==count($team['designation']) && count($team['name'])==count($team['designation']) && count($team['name'])==count($team['email']) ){
			$cnt=count($team['name']);
			for($i=0;$i<$cnt;$i++){
				$vl=array("name"=>"","designation"=>"","email"=>"","lang_id"=>0,"mobile"=>"");
				$name=empty($team['name'][$i])?"":trim($team['name'][$i]);
				if(!empty($name)){
					$vl['name']=$name;
				}else if($checkRequired)
					$errors[$type."_".$i."_name"]="Name is required in row ".($i+1)." of: $teamName";
				
				$designation=empty($team['designation'][$i])?"":trim($team['designation'][$i]);
				if(!empty($designation)){
					$vl['designation']=$designation;
				}else if($checkRequired)
					$errors[$type."_".$i."_designation"]="Designation is required in row ".($i+1)." of: $teamName";
				
				$email=empty($team['email'][$i])?"":trim($team['email'][$i]);
				if(!empty($email)){
					if(preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $email)!=1)
						$errors[$type."_".$i."_email"]="Invalid email in row ".($i+1)." of: $teamName";
					else
						$vl['email']=$email;
				}/*else if($checkRequired)
					$errors[$type."_".$i."_email"]="Email is required in row ".($i+1)." of: $teamName";*/
				
				$lang_id=empty($team['lang_id'][$i])?"":trim($team['lang_id'][$i]);
				if(!empty($lang_id)){
					$vl['lang_id']=$lang_id;
				}else if($checkRequired)
					$errors[$type."_".$i."_language"]="Language is required in row ".($i+1)." of: $teamName";
				
                                $c_code=empty($team['c_code'][$i])?"":trim($team['c_code'][$i]);
				if(!empty($c_code)){
					$vl['c_code']=$c_code;
                                }
				$mobile=empty($team['mobile'][$i])?"":trim($team['mobile'][$i]);
				if(!empty($mobile)){
                                    if(preg_match("/^[1-9][0-9]*$/", $mobile)!=1)
						$errors[$type."_".$i."_mobile"]="Invalid mobile in row ".($i+1)." of: $teamName";
					else
					$vl['mobile']=$mobile;
				}/*else if($checkRequired && $designation!=7)
					$errors[$type."_".$i."_mobile"]="Mobile is required in row ".($i+1)." of: $teamName";
				*/
				$values[]=$vl;
			}
		}else{
			$errors[$type]="Unequal no. of fields(name,email,designation,language,mobile) in: $teamName";
		}
		
		return array("errors"=>$errors,"values"=>$values);
	}
	function aqsAdditionalRefTeamValidation($team){		
		$errors=array();
		$values=array();
		//print_r($team);
			$cnt=count($team['name']);
			//echo $cnt;
			for($i=0;$i<$cnt;$i++){
				//print_r($team[$i]);
                            $cCode = '';
				$vl=array("name"=>"","phone"=>"","email"=>"","role_stakeholder"=>0);		
				
						$email=empty($team['email'][$i])?"":trim($team['email'][$i]);	
						//echo $email,$team['name'][$i];
						/*if($email){
							if(preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $email)!=1)
								$errors["additional_email_'.($i+1).'"]="Invalid email in row ".($i+1)." of: $teamName";
							else
								$vl['email']=$email;
						}	*/		
							/*$v1['email']=$team['email'][$i];
							$v1['name']=$team['name'][$i];
							$v1['phone']=$team['phone'][$i];
							$v1['role_stakeholder']=$team['role_stakeholder'][$i];*/
						//print_r($v1);
							//$values[]=$vl;
                                                        if(!empty($team['c_code'][$i])){
                                                            
                                                            $cCode = "(+".trim($team['c_code'][$i]).")";
                                                        }
							$values[$i]['email']=trim($team['email'][$i]);
							$values[$i]['name']=trim($team['name'][$i]);
							$values[$i]['phone']= $cCode.trim($team['phone'][$i]);
							$values[$i]['role_stakeholder']=trim($team['role_stakeholder'][$i]);
							//print_r($values);
		
		}
		//print_r($values);
		return array("errors"=>$errors,"values"=>$values);
	}
	
	function aqsFormValidation($aqsData,$otherData,$additional,$schoolLevels=array(),$checkRequired=1,$isSelfReview=0,$schoolsTypeIds=array(),$isCollegeReview=0){
		$aqsFields=array(
			"terms_agree"=>array("type"=>"int","isRequired"=>1,"name"=>"Accept Terms and Conditions"),	
			"referrer_id"=>array("type"=>"int","isRequired"=>0,"name"=>"Referred by"),
			"referrer_text"=>array("type"=>"string","isRequired"=>($aqsData['referrer_id']==7?0:0),"name"=>"Please specify (referred by)"),//if referred by is other then referrer text is mandatory
			"school_name"=>array("type"=>"string","isRequired"=>1,"name"=>$isCollegeReview?'Name of the institution':'School name'),
			"principal_name"=>array("type"=>"string","isRequired"=>1,"name"=>"Principal name"),
			"principal_phone_no"=>array("type"=>"phone","isRequired"=>1,"name"=>"Principal phone no."),
			//"principal_email"=>array("type"=>"email","isRequired"=>1,"name"=>"Principal email"),
			"coordinator_name"=>array("type"=>"string","isRequired"=>0,"name"=>"Coordinator name"),
			"coordinator_phone_number"=>array("type"=>"phone","isRequired"=>0,"name"=>"Coordinator phone no."),
			"coordinator_email"=>array("type"=>"email","isRequired"=>0,"name"=>"Coordinator email"),
			"accountant_name"=>array("type"=>"string","isRequired"=>0,"name"=>"Accountant name"),
			"accountant_phone_no"=>array("type"=>"phone","isRequired"=>0,"name"=>"Accountant phone no."),
			"accountant_email"=>array("type"=>"email","isRequired"=>0,"name"=>"Accountant email"),
			"school_address"=>array("type"=>"string","isRequired"=>1,"name"=>$isCollegeReview?"Institution Address":"School address"),
			"school_website"=>array("type"=>"url","isRequired"=>0,"name"=>$isCollegeReview?"Institution website":"School website"),
			"school_email"=>array("type"=>"email","isRequired"=>0,"name"=>$isCollegeReview?"Institution email":"School email"),
			"billing_name"=>array("type"=>"string","isRequired"=>0,"name"=>"Billing name"),
			"billing_address"=>array("type"=>"string","isRequired"=>0,"name"=>"Billing address"),
			"board_id"=>array("type"=>"int","isRequired"=>1,"name"=>"Board affiliation"),
			"school_region_id"=>array("type"=>"int","isRequired"=>1,"name"=>$isCollegeReview?"Institution location":"School location"),
			"school_type_id"=>array("type"=>"string","isRequired"=>1,"name"=>$isCollegeReview?"Type of institution":"Type of school"),
			"no_of_gates"=>array("type"=>"int","isRequired"=>0,"name"=>"Number of gates for entry/exit"),
			//"no_of_buildings"=>array("type"=>"int","isRequired"=>!$isSelfReview,"name"=>"Number of buildings"),
			"no_of_buildings"=>array("type"=>"int","isRequired"=>0,"name"=>"Number of buildings"),
			"distance_main_building"=>array("type"=>"string","isRequired"=>0,"name"=>"Distance from the main buildings"),
			"classes_from"=>array("type"=>"string","isRequired"=>1,"name"=>"Classes from"),
			"classes_to"=>array("type"=>"string","isRequired"=>1,"name"=>"Classes to"),
			"no_of_students"=>array("type"=>"string","isRequired"=>1,"name"=>$isCollegeReview?"Total student strength of the institution":"Total student strength of the school"),
			"num_class_rooms"=>array("type"=>"int","isRequired"=>1,"name"=>$isCollegeReview?"Total classrooms in the institution":"Total classrooms in the school"),
			"aqs_school_minority"=>array("type"=>"int","isRequired"=>1,"name"=>$isCollegeReview?"Is your institution a minority?":"Is your school a minority?"),
			"aqs_school_recognised"=>array("type"=>"int","isRequired"=>1,"name"=>$isCollegeReview?"Is your institution recognised?":"Is your school recognised?"),
			"aqs_school_registration_num"=>array("type"=>"string","isRequired"=>1,"name"=>$isCollegeReview?"Institution Registration number":"School Registration number"),
			"student_type_id"=>array("type"=>"int","isRequired"=>1,"name"=>"Student type"),
			"medium_instruction"=>array("type"=>"int","isRequired"=>1,"name"=>"Medium of instruction"),
			"annual_fee"=>array("type"=>"string","isRequired"=>1,"name"=>"Annual fee"),
			//"school_aqs_pref_start_date"=>array("type"=>"date","isRequired"=>!$isSelfReview,"name"=>$isCollegeReview?"Institution preferred dates for AQS":"School preferred dates for AQS"),
			"school_aqs_pref_start_date"=>array("type"=>"date","isRequired"=>0,"name"=>$isCollegeReview?"Institution preferred dates for AQS":"School preferred dates for AQS"),
			"school_aqs_pref_end_date"=>array("type"=>"date","isRequired"=>0,"name"=>$isCollegeReview?"Institution preferred dates for AQS":"School preferred dates for AQS"),
			"travel_arrangement_for_adhyayan"=>array("type"=>"int","isRequired"=>0,"name"=>"Travel arrangements"),
			"airport_name"=>array("type"=>"string","isRequired"=>isset($aqsData['travel_arrangement_for_adhyayan']) && $aqsData['travel_arrangement_for_adhyayan']==2?1:0,"name"=>"Name of the nearest Airport"),
			"rail_station_name"=>array("type"=>"string","isRequired"=>isset($aqsData['travel_arrangement_for_adhyayan']) && $aqsData['travel_arrangement_for_adhyayan']==2?1:0,"name"=>"Name of the nearest railway station"),
			"rail_station_distance"=>array("type"=>"float","isRequired"=>isset($aqsData['travel_arrangement_for_adhyayan']) && $aqsData['travel_arrangement_for_adhyayan']==2?1:0,"name"=>$isCollegeReview?"Railway station distance from institution":"Railway station distance from school "),
			"airport_distance"=>array("type"=>"float","isRequired"=>isset($aqsData['travel_arrangement_for_adhyayan']) && $aqsData['travel_arrangement_for_adhyayan']==2?1:0,"name"=>$isCollegeReview?"Airport distance from institution":"Airport distance from school"),
			"accomodation_arrangement_for_adhyayan"=>array("type"=>"int","isRequired"=>isset($aqsData['accomodation_arrangement_for_adhyayan']) && $aqsData['accomodation_arrangement_for_adhyayan']==2?1:0,"name"=>"Accommodation arrangements"),
			"hotel_name"=>array("type"=>"string","isRequired"=>isset($aqsData['accomodation_arrangement_for_adhyayan']) && $aqsData['accomodation_arrangement_for_adhyayan']==2?1:0,"name"=>"Name of the nearest Hotel"),
			"hotel_school_distance"=>array("type"=>"float","isRequired"=>isset($aqsData['accomodation_arrangement_for_adhyayan']) && $aqsData['accomodation_arrangement_for_adhyayan']==2?1:0,"name"=>$isCollegeReview?"Distance between the hotel and institution":"Distance between the hotel and school"),
			"hotel_station_distance"=>array("type"=>"float","isRequired"=>isset($aqsData['accomodation_arrangement_for_adhyayan']) && $aqsData['accomodation_arrangement_for_adhyayan']==2?1:0,"name"=>"Distance between hotel and railway station"),
			"hotel_airport_distance"=>array("type"=>"float","isRequired"=>isset($aqsData['accomodation_arrangement_for_adhyayan']) && $aqsData['accomodation_arrangement_for_adhyayan']==2?1:0,"name"=>"Distance between hotel and Airport station"),
			"contract_file_name"=>array("type"=>"string","isRequired"=>0,"name"=>"Contract"),
                        "aqs_school_gst"=>array("type"=>"int","isRequired"=>1,"name"=>$isCollegeReview?"Is your institution registered under GST?":"Is your school registered under GST?"),
			"aqs_school_gst_num"=>array("type"=>"string","isRequired"=>1,"name"=>$isCollegeReview?"Institution GST number":"School GST number"),
			
		);
		$errors=array();
		$values=array();
               // print_r($aqsFields);
                if(isset($aqsData['aqs_school_recognised']) && $aqsData['aqs_school_recognised'] == "2") {
                    
                    unset($aqsFields['aqs_school_registration_num']);
                }
                if(isset($aqsData['aqs_school_gst']) && $aqsData['aqs_school_gst'] == "2") {
                    
                    unset($aqsFields['aqs_school_gst_num']);
                }
                
                if($isCollegeReview) {
                    
                    unset($aqsFields['accountant_name']);
                    unset($aqsFields['accountant_phone_no']);
                    unset($aqsFields['accountant_email']);
                    unset($aqsFields['billing_name']);
                    unset($aqsFields['billing_address']);
                    unset($aqsFields['no_of_gates']);
                    unset($aqsFields['no_of_buildings']);
                     unset($aqsFields['distance_main_building']);
                     unset($aqsFields['classes_from']);
                     unset($aqsFields['classes_to']);
                     unset($aqsFields['num_class_rooms']);
                     unset($aqsFields['board_id']);
                     //unset($aqsFields['school_aqs_pref_start_date']);
                     //unset($aqsFields['school_aqs_pref_end_date']);
                     unset($aqsFields['travel_arrangement_for_adhyayan']);
                     unset($aqsFields['travel_arrangement_for_adhyayan']);
                     unset($aqsFields['aqs_school_gst']);
                     unset($aqsFields['aqs_school_gst_num']);
                     
                }
                
		foreach($aqsFields as $k=>$f){
			$val=isset($aqsData[$k])?trim($aqsData[$k]):"";
			if($checkRequired && $f["isRequired"] && empty($val))
				$errors[$k]="Field is required: '".$f['name']."'";
			else if(empty($val)){
				$values[$k]=($f["type"]=="int" || $f["type"]=="float")?0:'';
			}else if(($f["type"]=="int" || $f["type"]=="float") && !is_numeric($val)){
				$errors[$k]="Only numbers are allowed: '".$f['name']."'";
			}else if($f["type"]=="email" && preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $val)!=1){
				$errors[$k]="Invalid email: '".$f['name']."'";
			}else if($f["type"]=="phone" && preg_match("/^[1-9][0-9]*$/", $val)!=1){
				$errors[$k]="Invalid value: '".$f['name']."'";
			}else if($f["type"]=="url" && count(explode('.',$val))<2){
				$errors[$k]="Invalid URL: '".$f['name']."'";
			}else if($f["type"]=="date" && !$this->isValidDate($val)){
				$errors[$k]="Invalid Date: '".$f['name']."'";
			}else{
				$values[$k]=$val;
			}
		}
		if(!$isSelfReview && !empty($aqsData['no_of_buildings']) && $aqsData['no_of_buildings']==1 && isset($errors['distance_main_building'])){
			unset($errors['distance_main_building']);
		}
                if( !empty($values['num_class_rooms']) && isset($values['num_class_rooms']) ) {
                    
                    if( (!preg_match("/^[1-9][0-9]*$/",$values['num_class_rooms']))) {
                        $errors['num_class_rooms']="Invalid value : Number of class rooms ";
                    }else if($values['num_class_rooms']>=1 && $values['num_class_rooms']>200) {
                         $errors['num_class_rooms']="Number of class rooms must be between 1 to 200";
                    }
                }
		if(!$isSelfReview && !empty($values['school_aqs_pref_end_date']) && !empty($values['school_aqs_pref_start_date'])){
			$eDate=explode("-",$values['school_aqs_pref_end_date']);
			$sDate=explode("-",$values['school_aqs_pref_start_date']);
			if($eDate[2]<$sDate[2] || ($eDate[2]==$sDate[2] && $eDate[1]<$sDate[1]) || ($eDate[2]==$sDate[2] && $eDate[1]==$sDate[1] && $eDate[2]<$sDate[2]))
				$errors['school_aqs_pref_end_date']="End date can't be less than Start date in: ".$aqsFields['school_aqs_pref_end_date']['name'];
		}		
		
		if(!empty($otherData['bName_same'])){
			$values['billing_name']=isset($values['school_name'])?$values['school_name']:"";
			unset($errors['billing_name']);
		}
		if(!empty($otherData['bAddress_same'])){
			$values['billing_address']=isset($values['school_address'])?$values['school_address']:"";
			unset($errors['billing_address']);
		}
               /* if(!empty($schoolsTypeIds) && count($schoolsTypeIds) < 1) {
                    
                    $errors['school_type_id']="School Type cannot be blank";
                }*/
		$otherValues=array();
		if(!$isSelfReview && !empty($otherData['it_support'])){
			$valid=true;
			foreach($otherData['it_support'] as $v)
				if(!is_numeric($v))
					$valid=false;
			if($valid)
				$otherValues['it_support']=$otherData['it_support'];
			else
				$errors['it_support']="Only numbers are allowed: 'IT support'";
		}else
			$otherValues['it_support']=array();
		
		$otherValues['timing']=array();
		$additionalValues = array();
		if(!$isSelfReview && !empty($additional)){
			//$otherValues['additional']=$additional;
			foreach($additional as $key=>$val){
				if($key=='school_community_id')
					$additionalValues['school_community'] = ($additional['school_community_id']);
				elseif($key=='review_medium_instrn_id')
					$additionalValues['review_medium_instrn_id'] = ($additional['review_medium_instrn_id']);
				else 
					$additionalValues[$key]=trim($val);
			}
		}
		if(!$isSelfReview && !$isCollegeReview && !empty($otherData['timing'])){
			$notApplCount=0;
			foreach($schoolLevels as $sl){
				if(!empty($otherData['timing'][$sl['school_level_id']])){
					if(empty($otherData['timing'][$sl['school_level_id']]['not_applicable'])){
						$time=array("school_level_id"=>$sl['school_level_id'],"start_time"=>"","end_time"=>"");
						if(!empty($otherData['timing'][$sl['school_level_id']]['start_time'])){
							$tm=explode(":",$otherData['timing'][$sl['school_level_id']]['start_time']);
							if(count($tm)==2 && $tm[0]<23 && $tm[1]<60)
								$time['start_time']=$otherData['timing'][$sl['school_level_id']]['start_time'];
							//else
							//	$errors['other_timing_'.$sl['school_level_id']."_start_time"]="Invalid time: '".$sl['school_level']." start time in School timings'";
						}//else if($checkRequired && empty($otherData['timing'][$sl['school_level_id']]['start_time']))
							//$errors['other_timing_'.$sl['school_level_id']."_start_time"]="Field is required: '".$sl['school_level']." start time in School timings'";
						
						if(!empty($otherData['timing'][$sl['school_level_id']]['end_time'])){
							$tm=explode(":",$otherData['timing'][$sl['school_level_id']]['end_time']);
							if(count($tm)==2 && $tm[0]<23 && $tm[1]<60)
								$time['end_time']=$otherData['timing'][$sl['school_level_id']]['end_time'];
							//else
								//$errors['other_timing_'.$sl['school_level_id']."_end_time"]="Invalid time: '".$sl['school_level']." end time in School timings'";
						}//else if($checkRequired && empty($otherData['timing'][$sl['school_level_id']]['end_time']))
							//$errors['other_timing_'.$sl['school_level_id']."_start_time"]="Field is required: '".$sl['school_level']." start time in School timings'";
						
						$otherValues['timing'][$sl['school_level_id']]=$time;
					}else{
						$notApplCount++;
					}
				}/*else if($checkRequired){
					$errors['other_timing_'.$sl['school_level_id']]="Field is required: 'School timings of ".$sl['school_level']."'";
				}*/
			}
			/*if($notApplCount>0 && $notApplCount==count($schoolLevels) && $checkRequired){
				$errors['other_timing']="Field is required: 'Please fill atleast 1 School timing'";
			}*/
		}/*else if(!$isSelfReview && !$isCollegeReview && $checkRequired){
			$errors['other_timing']="Field is required: 'School timings of all sections'";
		}*/
		
		return array("errors"=>$errors,"values"=>$values,"otherValues"=>$otherValues,"additionalValues"=>$additionalValues);
	}
	
	function getPreferredLanguages($active=1){  
              //$res=$this->db->get_results("select * from preferred_language where lang_id>0 and active=? ;",array($active));  
              $res=$this->db->get_results("select language_id as lang_id,language_name as lang_name  from d_language where language_id>0;",array());
              return $res?$res:array();
        }
        
        function getDesignations($active=1){
        
            $res=$this->db->get_results("select designation_id,designation  from d_designation where designation_id>0 and active=? ORDER BY rank ASC ;",array($active));
            return $res?$res:array();
        }
        
        function getActivity($active=1){
        
            $res=$this->db->get_results("select activity_id,activity,symbol  from d_activity where  active=? ORDER BY activity  ASC ;",array($active));
            return $res?$res:array();
        }
        
	function getAnnualFee($aqs = 0,$val=''){  
              //$res=$this->db->get_results("select * from preferred_language where lang_id>0 and active=? ;",array($active));
                if($aqs == 1) {
                    $res=$this->db->get_results("select fee_id,fee_text as fee from d_fees WHERE fee_text = ?;",array($val));
                }else {
                    $res=$this->db->get_results("select fee_id,fee_text as fee from d_fees;",array());
                }
              return $res?$res:array();
        }
	function getAqsTeamHtmlRow($sn,$isSchoolRow=1,$name,$designation,$lang_id,$email,$mobile,  $attrbutes='',$addDelete=1,$c_code = 91){
		global $pLangList;
		if(empty($pLangList))
			$pLangList=$this->getPreferredLanguages();
                
                $pDesigList=$this->getDesignations();
                $clientModel=new clientModel();	
                $country_code_list =  $clientModel->getCountryWithCode();
		$type=$isSchoolRow?'schoolTeam':'adhyayanTeam';
		$ret= '<tr class="team_row">
				<td class="s_no">'.$sn.'</td> 
				<td><input type="text"  class="tableTxtFld" id="aqsf_'.$type.'_'.($sn-1).'_name" autocomplete="off" name="'.$type.'[name][]" value="'.$name.'" '.$attrbutes.'></td>'; 
				
                                //<td><input type="text" class="tableTxtFld" id="aqsf_'.$type.'_'.($sn-1).'_designation" autocomplete="off" name="'.$type.'[designation][]" value="'.$designation.'" '.$attrbutes.'></td>
				$ret.= '<td><select class="tableDdFld selectpicker form-control" id="aqsf_'.$type.'_'.($sn-1).'_designation" autocomplete="off" name="'.$type.'[designation][]" '.$attrbutes.'><option value=""> - Select designation - </option>';
		foreach($pDesigList as $desig)
			$ret.='<option value="'.$desig['designation_id'].'" '.($desig['designation_id']==$designation?'selected="selected"':'').'>'.$desig['designation'].'</option>';
		$ret.='</select></td>';
                
                                $ret.= '<td><select class="tableDdFld selectpicker form-control" id="aqsf_'.$type.'_'.($sn-1).'_language" autocomplete="off" name="'.$type.'[lang_id][]" '.$attrbutes.' data-size="10"><option value=""> - Select language - </option>';
		foreach($pLangList as $lang)
			$ret.='<option value="'.$lang['lang_id'].'" '.($lang['lang_id']==$lang_id?'selected="selected"':'').'>'.$lang['lang_name'].'</option>';
		$ret.='</select></td>
				<td><input type="email" class="tableTxtFld" id="aqsf_'.$type.'_'.($sn-1).'_email" autocomplete="off" name="'.$type.'[email][]" value="'.$email.'" '.$attrbutes.'></td> ';
               
                
                $ret.= '<td style="width: 250px;"><select class="tableDdFld selectpicker related form-control w90" id="aqsf_'.$type.'_'.($sn-1).'_c_code" autocomplete="off" name="'.$type.'[c_code][]" '.$attrbutes.'>';
		foreach($country_code_list as $country)
			$ret.='<option value="'.$country['phonecode'].'" '.($country['phonecode']==$c_code?'selected="selected"':'').'>'."(+".$country['phonecode'].") ".'</option>';
		$ret.='</select>';
                
		$ret.=	'<input type="text" class="tableTxtFld w90 aqs_ph" id="aqsf_'.$type.'_'.($sn-1).'_mobile" autocomplete="off" name="'.$type.'[mobile][]" value="'.$mobile.'" '.$attrbutes.'></td>
				<td >'.($addDelete>0?'<a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a>':'').'</td>
			</tr>';
		return $ret;
	}
	function getAqsAdditonalRefHtmlRow($sn,$name,$phone,$email,$role_stakeholder,$addDelete,$c_code = 91){	
            
                $clientModel=new clientModel();	
                $country_code_list =  $clientModel->getCountryWithCode();
		$ret = '<tr class="team_row">
				<td class="s_no">'.$sn.'</td>
				<td><input type="text" class="tableTxtFld" id="additional_team_name_'.($sn-1).'" name="additional_ref[name][]" value="'.$name.'"></td>';
                $ret.= '<td style="width: 250px;"><select class="tableDdFld selectpicker related form-control w90" id="additional_team_ccode_'.($sn-1).'" autocomplete="off" name="additional_ref[c_code][]]" >';
                                foreach($country_code_list as $country)
                                        $ret.='<option value="'.$country['phonecode'].'" '.($country['phonecode']==$c_code?'selected="selected"':'').'>'."(+".$country['phonecode'].") ".'</option>';
                                $ret.='</select>';
				$ret.= '<input type="text" class="tableTxtFld aqs_ph w90" id="additional_team_phone_'.($sn-1).'" name="additional_ref[phone][]" value="'.$phone.'"></td>
				<td><input type="email" class="tableTxtFld" id="additional_team_email_'.($sn-1).'" name="additional_ref[email][]" value="'.$email.'"></td>
				<td><input type="text" class="tableTxtFld" id="additional_team_role_stake_'.($sn-1).'" name="additional_ref[role_stakeholder][]" value="'.$role_stakeholder.'"></td>	
				<td>'.($addDelete>0?'<a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a>':'').'</td>		
				</tr>';		
				return $ret;
	}
	
	function isValidDate($date){
		$t=explode("-",$date);
		if(strpos($date,".")===false && count($t)==3 && $t[1]<=12 && $t[0]<=31 && $t[2]>2000)
			return true;
		else
			return false;
	}
        function isValidAQSDate($date){
                //print_r($date);
		$t=array_map('trim',explode("/",$date));
                $t  = array_filter($t);
                //print_r($t);
                $validFlag = 1;
                if(count($t) ==3) {
		if( !count($t)==3 ) {
                    $validFlag = 0;
                }if(!(preg_match('/^[0-9][0-9]*$/', $t[0]) && $t[0]>2016))
                     $validFlag = 0;
                if(!(preg_match('/^[0-9][0-9]*$/', $t[1]) && $t[1]>=1 && $t[1]<=12))
                     $validFlag = 0;
                if(!(preg_match('/^[0-9][0-9]*$/', $t[2]) && $t[2]>=1 && $t[2]<=31))
                     $validFlag = 0;
                if($validFlag == 1)
                    return true;
		else
			return false;
                }return false;
	}
	function getAQSversion($client_id){
		$sql = "Select c.client_id,c.country_id, a.assessment_id,d.assessment_type_id,aqsdata_id,a.create_date,aqs.terms_agree,aqs.status
				from d_assessment a inner join d_client c on a.client_id=c.client_id
				inner join d_diagnostic d on d.diagnostic_id =a.diagnostic_id
				inner join d_AQS_data aqs on aqs.id=a.aqsdata_id
				where c.client_id=? and aqs.terms_agree=1 and status = 1 order by create_date desc";
		$res=$this->db->get_results($sql,array($client_id));
		return $res?$res:array();
	}
        function getSchoolByRegistrationNum($regNum) {
            
                 $sql = "Select * from d_AQS_data WHERE aqs_school_registration_num = ?";
		$res=$this->db->get_results($sql,array($regNum));
		return $res?$res:array();
        }
        function getSchoolByGSTNum($gstNum) {
            
                 $sql = "Select * from d_AQS_data WHERE aqs_school_gst_num = ?";
		$res=$this->db->get_results($sql,array($gstNum));
		return $res?$res:array();
        }
        
        // function to validate AQS data for upload AQS
        function aqsUploadValidation($aqsData,$valueRow){
           //echo "<pre>";print_r($aqsData);
		$aqsFields=array(
			//"terms_agree"=>array("type"=>"int","isRequired"=>1,"name"=>"Accept Terms and Conditions"),	
			//"referrer_id"=>array("type"=>"int","isRequired"=>1,"name"=>"Referred by"),
			//"referrer_text"=>array("type"=>"string","isRequired"=>($aqsData['referrer_id']==7?1:0),"name"=>"Please specify (referred by)"),//if referred by is other then referrer text is mandatory
			//"school_name"=>array("type"=>"string","isRequired"=>1,"name"=>"School name"),
			//"principal_name"=>array("type"=>"string","isRequired"=>1,"name"=>"Principal name"),
			//"principal_phone_no"=>array("type"=>"phone","isRequired"=>1,"name"=>"Principal phone no."),
			//"principal_email"=>array("type"=>"email","isRequired"=>1,"name"=>"Principal email"),
			"coordinator_name"=>array("type"=>"string","isRequired"=>0,"name"=>"Coordinator name"),
			"coordinator_phone_number"=>array("type"=>"phone","isRequired"=>0,"name"=>"Coordinator phone no."),
			"coordinator_email"=>array("type"=>"email","isRequired"=>0,"name"=>"Coordinator email"),
			"accountant_name"=>array("type"=>"string","isRequired"=>0,"name"=>"Accountant name"),
			"accountant_phone_no"=>array("type"=>"phone","isRequired"=>1,"name"=>"Accountant phone no."),
			"accountant_email"=>array("type"=>"email","isRequired"=>0,"name"=>"Accountant email"),
			"school_address"=>array("type"=>"string","isRequired"=>0,"name"=>"School address"),
			"school_website"=>array("type"=>"url","isRequired"=>0,"name"=>"School website"),
			"school_email"=>array("type"=>"email","isRequired"=>0,"name"=>"School email"),
			"billing_name"=>array("type"=>"string","isRequired"=>0,"name"=>"Billing name"),
			"billing_address"=>array("type"=>"string","isRequired"=>0,"name"=>"Billing address"),
			"board_id"=>array("type"=>"string","isRequired"=>0,"name"=>"Board affiliation"),
			"school_region_id"=>array("type"=>"int","isRequired"=>0,"name"=>"School Region"),
			"school_type_id"=>array("type"=>"string","isRequired"=>0,"name"=>"Type of school"),
			"no_of_gates"=>array("type"=>"int","isRequired"=>0,"name"=>"Number of gates for entry/exit"),
			"no_of_buildings"=>array("type"=>"int","isRequired"=>0,"name"=>"Number of buildings"),
			"distance_main_building"=>array("type"=>"int","isRequired"=>0,"name"=>"Distance from the main building"),
			"classes_from"=>array("type"=>"string","isRequired"=>0,"name"=>"Classes from"),
			"classes_to"=>array("type"=>"string","isRequired"=>0,"name"=>"Classes to"),
			"no_of_students"=>array("type"=>"string","isRequired"=>0,"name"=>"Total student strength of the school"),
			"num_class_rooms"=>array("type"=>"int","isRequired"=>0,"name"=>"Total classrooms in the school"),
			"aqs_school_minority"=>array("type"=>"int","isRequired"=>0,"name"=>"Is your school a minority?"),
			"aqs_school_recognised"=>array("type"=>"int","isRequired"=>0,"name"=>"Is your school recognised?"),
			"aqs_school_registration_num"=>array("type"=>"string","isRequired"=>0,"name"=>"School Registration number "),
			"student_type_id"=>array("type"=>"int","isRequired"=>0,"name"=>"Student type"),
			"medium_instruction"=>array("type"=>"string","isRequired"=>0,"name"=>"Medium of instruction"),
			"annual_fee"=>array("type"=>"string","isRequired"=>0,"name"=>"Annual fee"),
			//"school_aqs_pref_start_date"=>array("type"=>"date","isRequired"=>!$isSelfReview,"name"=>"School preferred dates for AQS"),
			//"school_aqs_pref_end_date"=>array("type"=>"date","isRequired"=>!$isSelfReview,"name"=>"School preferred dates for AQS"),
			"travel_arrangement_for_adhyayan"=>array("type"=>"int","isRequired"=>0,"name"=>"Travel arrangements"),
			"airport_name"=>array("type"=>"string","isRequired"=>0,"name"=>"Name of the nearest Airport"),
			"rail_station_name"=>array("type"=>"string","isRequired"=>0,"name"=>"Name of the nearest railway station"),
			"rail_station_distance"=>array("type"=>"float","isRequired"=>0,"name"=>"Railway station distance from school "),
			"airport_distance"=>array("type"=>"float","isRequired"=>0,"name"=>"Airport distance from school"),
			"accomodation_arrangement_for_adhyayan"=>array("type"=>"int","isRequired"=>0,"name"=>"Accommodation arrangements"),
			"hotel_name"=>array("type"=>"string","isRequired"=>0,"name"=>"Name of the nearest Hotel"),
			"hotel_school_distance"=>array("type"=>"float","isRequired"=>0,"name"=>"Distance between the hotel and school"),
			"hotel_station_distance"=>array("type"=>"float","isRequired"=>0,"name"=>"Distance between hotel and railway station"),
			"hotel_airport_distance"=>array("type"=>"float","isRequired"=>0,"name"=>"Distance between hotel and Airport station"),
			"school_aqs_pref_start_date"=>array("type"=>"date","isRequired"=>0,"name"=>"AQS start date"),
			"school_aqs_pref_end_date"=>array("type"=>"date","isRequired"=>0,"name"=>"AQS end date"),
			"it_support"=>array("type"=>"string","isRequired"=>0,"name"=>"IT Support"),
                        "aqs_school_gst"=>array("type"=>"int","isRequired"=>0,"name"=>"Is your school registered under GST?"),
			"aqs_school_gst_num"=>array("type"=>"string","isRequired"=>0,"name"=>"School GST number "),
			
			//"is_school_recognised"=>array("type"=>"int","isRequired"=>0,"name"=>"Is school recognised"),
			//"aqs_school_minority"=>array("type"=>"int","isRequired"=>0,"name"=>"Is school minority"),
			//"contract_file_name"=>array("type"=>"string","isRequired"=>0,"name"=>"Contract")
		);
		$errors=array();
		$values=array();
		$validValues=array(1,2);
		$fieldsForValidation=array('accomodation_arrangement_for_adhyayan','travel_arrangement_for_adhyayan','aqs_school_recognised','aqs_school_minority');
                $no_of_students = array("50-250","251-500","501-1000","1001-1500","1501-2000","2001-3000","3001+");
                $distances = array(25, 50, 100, 500, 1000, "1000+");
                 //echo "<pre>";print_r($aqsData);
                if(isset($aqsData['aqs_school_recognised']) && $aqsData['aqs_school_recognised'] == "2") {
                    
                    unset($aqsFields['aqs_school_registration_num']);
                }
                if(isset($aqsData['aqs_school_gst']) && $aqsData['aqs_school_gst'] == "2") {
                    
                    unset($aqsFields['aqs_school_gst_num']);
                }
                if(isset($aqsData['no_of_buildings']) && $aqsData['no_of_buildings'] ==1) {
                    
                    unset($aqsFields['aqs_distance_main_building']);
                }
                if(isset($aqsData['travel_arrangement_for_adhyayan']) && $aqsData['travel_arrangement_for_adhyayan'] != "2") {
                    
                    unset($aqsFields['airport_name']);
                    unset($aqsFields['rail_station_name']);
                    unset($aqsFields['rail_station_distance']);
                    unset($aqsFields['airport_distance']);
                }
                if(isset($aqsData['accomodation_arrangement_for_adhyayan']) && $aqsData['accomodation_arrangement_for_adhyayan'] == "1") {
                    
                    unset($aqsFields['hotel_name']);
                    unset($aqsFields['hotel_school_distance']);
                    unset($aqsFields['hotel_station_distance']);
                    unset($aqsFields['hotel_airport_distance']);
                }
		foreach($aqsFields as $k=>$f){
			$val=isset($aqsData[$k])?trim($aqsData[$k]):"";
			if( $f["isRequired"] && empty($val))
				$errors[]="Field is required: '".$f['name']."'";
			else if(empty($val)){
				$values[$k]=($f["type"]=="int" || $f["type"]=="float")?0:'';
			}else if(($f["type"]=="int" || $f["type"]=="float") && !is_numeric($val)){
				$errors[]="Only numbers are allowed: '".$f['name']."'";
			}else if($f["type"]=="email" && preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $val)!=1){
				$errors[]="Invalid email: '".$f['name']."'";
			}else if($f["type"]=="url" && count(explode('.',$val))<2){
				$errors[]="Invalid URL: '".$f['name']."'";
			}else if($f["type"]=="date" && !$this->isValidAQSDate($val)){
				$errors[]="Invalid Date: '".$f['name']."'";
			}else if($k == 'annual_fee') {
                                $res = $this->getAnnualFee(1,$val);
                                if(count($res)< 1) {
                                    $errors[]="Invalid: '".$f['name']."'";
                                }else 
                                    $values[$k]=$res[0]['fee'];
                        }else if($k == 'board_id') {
                                $res = $this->getBoardList('',1,strtolower($val));
                                if(count($res)< 1) {
                                    $errors[]="Invalid: '".$f['name']."'";
                                }else {
                                    $values[$k]=$res[0]['board_id'];
                                }
                        }else if($k == 'student_type_id') {
                                $res = $this->getStudentTypeList(1,strtolower($val));
                                if(count($res)< 1) {
                                    $errors[]="Invalid: '".$f['name']."'";
                                }else {
                                    $values[$k]=$res[0]['student_type_id'];
                                }
                        }else if($k == 'school_type_id') {
                                $schoolTypeList = '';
                                $schoolTypeIds = '';
                                $schoolTypesVals = explode(",",$val);
                                    foreach($schoolTypesVals as $data) {
                                        $schoolTypeList = "'".$data."'";                                     
                                        $res = $this->getSchoolTypeList(1,strtolower($schoolTypeList));
                                        if(count($res)< 1) {
                                            $errors[]="Invalid: '".$f['name']. " ".$schoolTypeList." for column'";
                                        }else {
                                            $schoolTypeIds .= $res[0]['school_type_id'].",";
                                        }
                                    }
                                    //else {
                                    $values[$k]=trim($schoolTypeIds,",");
                               // }
                        }else if($k == 'medium_instruction') {
                                $diagModel = new diagnosticModel;
                                $res = $diagModel->getAllLanguages(1,strtolower($val));
                                if(count($res)< 1) {
                                    $errors[]="Invalid: '".$f['name']."'";
                                }else {
                                    $values[$k]=$res[0]['language_id'];
                                }
                        }else if($k == 'classes_from') {
                               // $diagModel = new diagnosticModel;
                                $res = $this->getSchoolClassList(1,strtolower($val));
                                if(count($res)< 1) {
                                    $errors[]="Invalid Class From: '".$f['name']."'";
                                }else {
                                    $values[$k]=$res[0]['class_id'];
                                }
                        }else if($k == 'classes_to') {
                               // $diagModel = new diagnosticModel;
                                $res = $this->getSchoolClassList(1,strtolower($val));
                                if(count($res)< 1) {
                                    $errors[]="Invalid Class To: '".$f['name']."'";
                                }else {
                                    $values[$k]=$res[0]['class_id'];
                                }
                        }else if($k == 'it_support') {
                            $itSupport = explode(",",$val);
                            $itSupportFlag = 1;
                            $validValuesSupport = '';
                            foreach($itSupport as $data) {
                                if(!in_array($data,$validValues)) {
                                    $itSupportFlag = 0;
                                }else {
                                     $validValuesSupport .= $data.",";
                                }
                            }
                            if(!$itSupportFlag) {
                                $errors[]="Invalid : '".$f['name']."'";
                            }
                            if($validValuesSupport)
                             $values[$k]=trim($validValuesSupport,",");
                            
                        } else if($k == 'school_region_id') {
                               // $diagModel = new diagnosticModel;
                                $res = $this->getSchoolRegionList(1,strtolower($val));
                                if(count($res)< 1) {
                                    $errors[]="Invalid : '".$f['name']."'";
                                }else {
                                    $values[$k]=$res[0]['region_id'];
                                }
                        }else if($k == 'no_of_students') {
                               // $diagModel = new diagnosticModel;
                               // $res = $this->getSchoolRegionList(1,strtolower($val));
                                if(!in_array($val,$no_of_students)) {
                                    $errors[]="Invalid : '".$f['name']."'";
                                }else {
                                    $values[$k]=$val;
                                }
                        }else if($k == 'distance_main_building') {
                               // $diagModel = new diagnosticModel;
                               // $res = $this->getSchoolRegionList(1,strtolower($val));
                                if(!in_array($val,$distances)) {
                                    $errors[]="Invalid : '".$f['name']."'";
                                }else {
                                    $values[$k]=$val;
                                }
                        }else if(in_array($k,$fieldsForValidation) && !in_array($val,$validValues)) {
                                    $errors[]="Invalid value for: '".$f['name']."'";
                                
                        }else if( !empty($aqsData['no_of_buildings']) && $aqsData['no_of_buildings']==1 && isset($errors['distance_main_building'])){
			unset($errors[$k]);
                        } else if( $k == 'num_class_rooms' ) {
                            if( (!preg_match("/^[1-9][0-9]*$/",$val))) {
                                $errors[]="Invalid value : Number of class rooms ";
                            }else if($val>=1 && $val>200) {
                                 $errors[]="Number of class rooms must be between 1 to 200";
                            }else 
                                $values[$k]=$val;
                        }               
                        else{
				$values[$k]=$val;
			}
                      
		}
                if(!empty($values['school_aqs_pref_end_date']) && !empty($values['school_aqs_pref_start_date'])){
                            
                            if(strtotime($values['school_aqs_pref_end_date']) < strtotime($values['school_aqs_pref_start_date'])) {
                                //echo "yes";
                                $errors[]="Invalid AQS end date : school_aqs_pref_end_date can't be less than school_aqs_pref_start_date";
                                unset($values['school_aqs_pref_start_date']);
                                unset($values['school_aqs_pref_end_date']);
                                
                            }else {
                                
                                $values['school_aqs_pref_start_date'] = date("d-m-Y",strtotime($values['school_aqs_pref_start_date']));
                                $values['school_aqs_pref_end_date'] = date("d-m-Y",strtotime($values['school_aqs_pref_end_date']));
                            }
                }
                $values['school_name'] = $aqsData['school_name'];
                $values['principal_name'] = $aqsData['principal_name'];
                $values['principal_email'] = $aqsData['principal_email'];
                $values['principal_phone_no'] = "(+91)".$aqsData['principal_phone_no'];
                if(isset($values['coordinator_phone_number']) && $values['coordinator_phone_number']!='') {
                    $values['coordinator_phone_number'] = "(+91)".$values['coordinator_phone_number'];
                }
                if(isset($values['accountant_phone_no']) && $values['accountant_phone_no']!='') {
                    $values['accountant_phone_no'] = "(+91)".$values['accountant_phone_no'];
                }
		
		/*if(!$isSelfReview && !empty($values['school_aqs_pref_end_date']) && !empty($values['school_aqs_pref_start_date'])){
			$eDate=explode("/",$values['school_aqs_pref_end_date']);
			$sDate=explode("/",$values['school_aqs_pref_start_date']);
			if($eDate[2]<$sDate[2] || ($eDate[2]==$sDate[2] && $eDate[0]<$sDate[0]) || ($eDate[2]==$sDate[2] && $eDate[0]==$sDate[0] && $eDate[1]<$sDate[1]))
				$errors['school_aqs_pref_end_date']="End date can't be less than Start date in: ".$aqsFields['school_aqs_pref_end_date']['name'];
		}*/		
		
		/*if(!empty($otherData['bName_same'])){
			$values['billing_name']=isset($values['school_name'])?$values['school_name']:"";
			unset($errors['billing_name']);
		}
		if(!empty($otherData['bAddress_same'])){
			$values['billing_address']=isset($values['school_address'])?$values['school_address']:"";
			unset($errors['billing_address']);
		}*/
               /* if(!empty($schoolsTypeIds) && count($schoolsTypeIds) < 1) {
                    
                    $errors['school_type_id']="School Type cannot be blank";
                }
		$otherValues=array();
		if(!$isSelfReview && !empty($otherData['it_support'])){
			$valid=true;
			foreach($otherData['it_support'] as $v)
				if(!is_numeric($v))
					$valid=false;
			if($valid)
				$otherValues['it_support']=$otherData['it_support'];
			else
				$errors['it_support']="Only numbers are allowed: 'IT support'";
		}else
			$otherValues['it_support']=array();
		
		$otherValues['timing']=array();
		$additionalValues = array();
		if(!$isSelfReview && !empty($additional)){
			//$otherValues['additional']=$additional;
			foreach($additional as $key=>$val){
				if($key=='school_community_id')
					$additionalValues['school_community'] = ($additional['school_community_id']);
				elseif($key=='review_medium_instrn_id')
					$additionalValues['review_medium_instrn_id'] = ($additional['review_medium_instrn_id']);
				else 
					$additionalValues[$key]=trim($val);
			}
		}
		if(!$isSelfReview && !empty($otherData['timing'])){
			$notApplCount=0;
			foreach($schoolLevels as $sl){
				if(!empty($otherData['timing'][$sl['school_level_id']])){
					if(empty($otherData['timing'][$sl['school_level_id']]['not_applicable'])){
						$time=array("school_level_id"=>$sl['school_level_id'],"start_time"=>"","end_time"=>"");
						if(!empty($otherData['timing'][$sl['school_level_id']]['start_time'])){
							$tm=explode(":",$otherData['timing'][$sl['school_level_id']]['start_time']);
							if(count($tm)==2 && $tm[0]<23 && $tm[1]<60)
								$time['start_time']=$otherData['timing'][$sl['school_level_id']]['start_time'];
							else
								$errors['other_timing_'.$sl['school_level_id']."_start_time"]="Invalid time: '".$sl['school_level']." start time in School timings'";
						}else if($checkRequired && empty($otherData['timing'][$sl['school_level_id']]['start_time']))
							$errors['other_timing_'.$sl['school_level_id']."_start_time"]="Field is required: '".$sl['school_level']." start time in School timings'";
						
						if(!empty($otherData['timing'][$sl['school_level_id']]['end_time'])){
							$tm=explode(":",$otherData['timing'][$sl['school_level_id']]['end_time']);
							if(count($tm)==2 && $tm[0]<23 && $tm[1]<60)
								$time['end_time']=$otherData['timing'][$sl['school_level_id']]['end_time'];
							else
								$errors['other_timing_'.$sl['school_level_id']."_end_time"]="Invalid time: '".$sl['school_level']." end time in School timings'";
						}else if($checkRequired && empty($otherData['timing'][$sl['school_level_id']]['end_time']))
							$errors['other_timing_'.$sl['school_level_id']."_start_time"]="Field is required: '".$sl['school_level']." start time in School timings'";
						
						$otherValues['timing'][$sl['school_level_id']]=$time;
					}else{
						$notApplCount++;
					}
				}else if($checkRequired){
					$errors['other_timing_'.$sl['school_level_id']]="Field is required: 'School timings of ".$sl['school_level']."'";
				}
			}
			if($notApplCount>0 && $notApplCount==count($schoolLevels) && $checkRequired){
				$errors['other_timing']="Field is required: 'Please fill atleast 1 School timing'";
			}
		}else if(!$isSelfReview && $checkRequired){
			$errors['other_timing']="Field is required: 'School timings of all sections'";
		}*/
		$values = array_filter($values);
		return array("errors"=>$errors,"values"=>$values);
	}
	
}
