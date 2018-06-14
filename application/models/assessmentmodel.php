<?php

class assessmentModel extends Model{

	

	function getTiers(){

		$res=$this->db->get_results("select * from d_tier where isActive=1 order by standard_id desc;");

		return $res?$res:array();

	}

	

	function getAwardSchemes(){

		$res=$this->db->get_results("select * from d_award_scheme;");

		return $res?$res:array();

	}
        
        function getAwardSchemeById($award_scheme_id){

		$res=$this->db->get_row("select * from d_award_scheme where award_scheme_id=?;",array($award_scheme_id));

		return $res?$res:array();

	}
        
        function getTierById($tireid){

		$res=$this->db->get_row("select * from d_tier where standard_id=? && isActive=1 order by standard_id desc;",array($tireid));

		return $res?$res:array();

	}

                                
        function getRounds(){

		$res=$this->db->get_results("select * from d_aqs_rounds;");

		return $res?$res:array();

	}
        
        function getAssessmentTypes(){

		$res=$this->db->get_results("select * from d_assessment_type where assessment_type_id IN (1,4);");

		return $res?$res:array();

	}
        function getAllAssessmentTypes(){

		$res=$this->db->get_results("select * from d_assessment_type where assessment_type_id NOT IN (3);");

		return $res?$res:array();

	}
        
        function getStudentRounds($batch_id,$student_round=0){

		$res=$this->db->get_results("select * from d_aqs_rounds where aqs_round<=2 && aqs_round NOT IN (select student_round from d_group_assessment where client_id=? && student_round!=?);",array($batch_id,$student_round));

		return $res?$res:array();

	}
        
        function getStudentRoundsAll(){

		$res=$this->db->get_results("select * from d_aqs_rounds where aqs_round<=2",array());

		return $res?$res:array();

	}
	

	function getInternalAssessors($client_id){

		$res=$this->db->get_results("select u.user_id,u.name,group_concat(if(au.isFilled=0 && assessment_type_id=1,0,1)) as filleds,group_concat(au.isFilled) as filleds2,group_concat(assessment_type_id) as assm_ids,

	  if(group_concat(d.diagnostic_id order by d.diagnostic_id)=(select group_concat(diagnostic_id order by diagnostic_id) from d_diagnostic where isPublished=1 and assessment_type_id=d.assessment_type_id group by assessment_type_id),1,0) as allDiagUsed

	   from d_user u 

	   inner join h_user_user_role ur on u.user_id=ur.user_id 

	   inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

	   inner join d_user_capability c on rc.capability_id=c.capability_id

		left join h_assessment_user au on au.user_id=u.user_id and au.role=3

		left join d_assessment a on au.assessment_id=a.assessment_id

		left join d_diagnostic d on a.diagnostic_id=d.diagnostic_id  and a.d_sub_assessment_type_id!=1

		   where u.client_id=? and slug='take_internal_assessment'

            group by u.user_id 

			having allDiagUsed=0 or (allDiagUsed=1 and filleds rlike concat('[[:<:]]',1,'[[:>:]]') )           

            order by u.name;",array($client_id));

		/*$res=$this->db->get_results("select u.user_id,u.name 

			from d_user u 

			inner join h_user_user_role ur on u.user_id=ur.user_id 

			inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

			inner join d_user_capability c on rc.capability_id=c.capability_id

			where u.client_id=? and slug='take_internal_assessment'  group by u.user_id order by u.name",array($client_id));*/

		return $res?$res:array();

	}

	function getInternalAssessorsforSchoolSelfAssmt($client_id){

		$res=$this->db->get_results("select u.user_id,u.name,group_concat(if(au.isFilled=0 && assessment_type_id=1,0,1)) as filleds,group_concat(au.isFilled) as filleds2,group_concat(assessment_type_id) as assm_ids	 

	   from d_user u

	   inner join h_user_user_role ur on u.user_id=ur.user_id

	   inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

	   inner join d_user_capability c on rc.capability_id=c.capability_id

		left join h_assessment_user au on au.user_id=u.user_id and au.role=3

		left join d_assessment a on au.assessment_id=a.assessment_id

		left join d_diagnostic d on a.diagnostic_id=d.diagnostic_id

		   where u.client_id=? and slug='take_internal_assessment'

            group by u.user_id			

            order by u.name;",array($client_id));

		/*$res=$this->db->get_results("select u.user_id,u.name

		 from d_user u

		 inner join h_user_user_role ur on u.user_id=ur.user_id

		 inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

		 inner join d_user_capability c on rc.capability_id=c.capability_id

		 where u.client_id=? and slug='take_internal_assessment'  group by u.user_id order by u.name",array($client_id));*/

		return $res?$res:array();

	}
        
        function getGAIdfromClientandRound($client_id,$round){
            
            $sql="select * from d_group_assessment where client_id=? && student_round=? && assessment_type_id=?";
            
	   $res=$this->db->get_row($sql,array($client_id,$round,4));
          return $res;
        }
        
        function getGAIdfromClientandRoundUpdate($client_id,$round,$gaid){
            
            $sql="select * from d_group_assessment where client_id=? && student_round=? && assessment_type_id=? && group_assessment_id!=?";
            
	   $res=$this->db->get_row($sql,array($client_id,$round,4,$gaid));
          return $res;
        }

	function getDiagnosticsForInternalAssessor($client_id,$assessor_id,$assessment_id=0){

		$sql = "select u.user_id,u.name,

		group_concat(if(au.isFilled=1 && assessment_type_id=1,0,d.diagnostic_id)) as hidediagnostics

	   from d_user u 

	   inner join h_user_user_role ur on u.user_id=ur.user_id 

	   inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

	   inner join d_user_capability c on rc.capability_id=c.capability_id

		left join h_assessment_user au on au.user_id=u.user_id and au.role=3

		left join d_assessment a on au.assessment_id=a.assessment_id

		left join d_diagnostic d on a.diagnostic_id=d.diagnostic_id 

		   where u.client_id=? and slug='take_internal_assessment'                  

             and u.user_id=? and a.assessment_id!=?

             group by u.user_id";

		

		$res=$this->db->get_row($sql,array($client_id,$assessor_id,$assessment_id));

		return $res;
                //return $res?$res:array();

	}
        
        
        function getCollegeDiagnosticsForInternalAssessor($client_id,$assessor_id,$assessment_id=0){

		$sql = "select u.user_id,u.name,

		group_concat(if(au.isFilled=1 && assessment_type_id=5,0,d.diagnostic_id)) as hidediagnostics

	   from d_user u 

	   inner join h_user_user_role ur on u.user_id=ur.user_id 

	   inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

	   inner join d_user_capability c on rc.capability_id=c.capability_id

		left join h_assessment_user au on au.user_id=u.user_id and au.role=3

		left join d_assessment a on au.assessment_id=a.assessment_id

		left join d_diagnostic d on a.diagnostic_id=d.diagnostic_id 

		   where u.client_id=? and slug='take_internal_assessment'                  

             and u.user_id=? and a.assessment_id!=?

             group by u.user_id";

		

		$res=$this->db->get_row($sql,array($client_id,$assessor_id,$assessment_id));

		//return $res;
                return $res?$res:array("hidediagnostics"=>"");

	}

	function getEditInternalAssessors($client_id,$user_id){

		$res=$this->db->get_results("select u.user_id,u.name,group_concat(if(au.isFilled=0 && assessment_type_id=1,0,1)) as filleds,group_concat(au.isFilled) as filleds2,group_concat(assessment_type_id) as assm_ids,

	  if(group_concat(d.diagnostic_id order by d.diagnostic_id)=(select group_concat(diagnostic_id order by diagnostic_id) from d_diagnostic where isPublished=1 and assessment_type_id=d.assessment_type_id group by assessment_type_id),1,0) as allDiagUsed

	   from d_user u 

	   inner join h_user_user_role ur on u.user_id=ur.user_id 

	   inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

	   inner join d_user_capability c on rc.capability_id=c.capability_id

		left join h_assessment_user au on au.user_id=u.user_id and au.role=3

		left join d_assessment a on au.assessment_id=a.assessment_id and a.d_sub_assessment_type_id!=1

		left join d_diagnostic d on a.diagnostic_id=d.diagnostic_id 

		   where u.client_id=? and slug='take_internal_assessment'

            group by u.user_id 

			having allDiagUsed=0 or (allDiagUsed=1 and filleds rlike concat('[[:<:]]',1,'[[:>:]]') )           

            order by u.name;         

		union        



	   select u.user_id,u.name,group_concat(if(au.isFilled=0 && assessment_type_id=1,0,1)) as filleds,group_concat(au.isFilled) as filleds2,group_concat(assessment_type_id) as assm_ids,

		'' as allDiagUsed

	   from d_user u 

	   inner join h_user_user_role ur on u.user_id=ur.user_id 

	   inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

	   inner join d_user_capability c on rc.capability_id=c.capability_id

		left join h_assessment_user au on au.user_id=u.user_id and au.role=3

		left join d_assessment a on au.assessment_id=a.assessment_id

		left join d_diagnostic d on a.diagnostic_id=d.diagnostic_id 

		   where u.client_id=? and slug='take_internal_assessment' and u.user_id = ?

            group by u.user_id              

           order by 2; ",array($client_id,$client_id,$user_id));

		

		return $res?$res:array();

	}

	function getEditInternalAssessorsforSchoolSelfAssmt($client_id,$user_id){

		$res=$this->db->get_results("select u.user_id,u.name,group_concat(if(au.isFilled=0 && assessment_type_id=1,0,1)) as filleds,group_concat(au.isFilled) as filleds2,group_concat(assessment_type_id) as assm_ids

	   from d_user u

	   inner join h_user_user_role ur on u.user_id=ur.user_id

	   inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

	   inner join d_user_capability c on rc.capability_id=c.capability_id

		left join h_assessment_user au on au.user_id=u.user_id and au.role=3

		left join d_assessment a on au.assessment_id=a.assessment_id

		left join d_diagnostic d on a.diagnostic_id=d.diagnostic_id

		   where u.client_id=? and slug='take_internal_assessment'

            group by u.user_id

            -- having filleds  not rlike concat('[[:<:]]',0,'[[:>:]]') or filleds is null

		union

	

	   select u.user_id,u.name,group_concat(if(au.isFilled=0 && assessment_type_id=1,0,1)) as filleds,group_concat(au.isFilled) as filleds2,group_concat(assessment_type_id) as assm_ids

	   from d_user u

	   inner join h_user_user_role ur on u.user_id=ur.user_id

	   inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

	   inner join d_user_capability c on rc.capability_id=c.capability_id

		left join h_assessment_user au on au.user_id=u.user_id and au.role=3

		left join d_assessment a on au.assessment_id=a.assessment_id

		left join d_diagnostic d on a.diagnostic_id=d.diagnostic_id

		   where u.client_id=? and slug='take_internal_assessment' and u.user_id = ?

            group by u.user_id

           order by 2; ",array($client_id,$client_id,$user_id));

	

		return $res?$res:array();

	}
        
        //function to get school review facilitators data
	function getFacilitatorsDetails($assessment_id){

		$res=$this->db->get_results("select u.user_id,u.name ,c.client_id,c.client_name,r.sub_role_id,r.sub_role_name

			from d_user u 
			inner join h_user_user_role ur on u.user_id=ur.user_id 
			inner join h_facilitator_user f on f.user_id=u.user_id
			inner join d_client c on c.client_id=f.client_id
			inner join d_user_sub_role r on r.sub_role_id=f.sub_role_id
			where f.assessment_id= ? group by user_id",array($assessment_id));

		return $res?$res:array();

	}

	function getExternalAssessors($client_id){

		$res=$this->db->get_results("select u.user_id,u.name 

			from d_user u 

			inner join h_user_user_role ur on u.user_id=ur.user_id 

			inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

			inner join d_user_capability c on rc.capability_id=c.capability_id

			where u.client_id=? and slug='take_external_assessment'  group by u.user_id order by u.name",array($client_id));

		return $res?$res:array();

	}
        
        //Added By Vikas
        function getFacilitators($client_id){

		$res=$this->db->get_results("select u.user_id,u.name 

			from d_user u 

			inner join h_user_user_role ur on u.user_id=ur.user_id 

			inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

			inner join d_user_capability c on rc.capability_id=c.capability_id

			where u.client_id=? and ur.role_id='9'  group by u.user_id order by u.name",array($client_id));

		return $res?$res:array();

	}
        
        
        
        //Added By vikas

	function getAllSchoolUsers($client_id){

		$res=$this->db->get_results("select u.user_id,u.name 

			from d_user u 

			inner join h_user_user_role ur on u.user_id=ur.user_id 

			inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

			inner join d_user_capability c on rc.capability_id=c.capability_id

			where u.client_id=? group by u.user_id order by u.name",array($client_id));

		return $res?$res:array();

	}
        
        
        function getfrequency(){

		$res=$this->db->get_results("select * from d_frequency");

		return $res?$res:array();

	}

	

	function getSchoolAdmins($client_id){

		$res=$this->db->get_results("select u.user_id,u.name 

			from d_user u 

			inner join h_user_user_role ur on u.user_id=ur.user_id 

			inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

			inner join d_user_capability c on rc.capability_id=c.capability_id

			where u.client_id=? and slug='add_n_assign_tchr_to_tchr_asmnt'  group by u.user_id order by u.name",array($client_id));

		return $res?$res:array();

	}

	

	function createSchoolAssessment($client_id,$internal_assessor_id,$external_assessor_id,$diagnostic_id,$tier_id,$award_scheme_id,$ext_team){
            if(OFFLINE_STATUS==TRUE){
                //start---> call function for creating unique id for creating assessment on 10-03-2016 by Mohit Kumar
                $uniqueID = $this->db->createUniqueID('createSchoolAssessment');
                //end---> call function for creating unique id for creating assessment on 10-03-2016 by Mohit Kumar
            }
		if($this->db->insert("d_assessment",array('client_id'=>$client_id, 'diagnostic_id'=>$diagnostic_id, 'tier_id'=>$tier_id, 'award_scheme_id'=>$award_scheme_id,'d_sub_assessment_type_id'=>'2', 'create_date'=>date("Y-m-d H:i:s")))){

			$aid=$this->db->get_last_insert_id();
                        if(OFFLINE_STATUS==TRUE){    
                            //start---> save the history for insert school assessment data into d_assessment table on 10-03-2016 By Mohit Kumar
                            $action_assessment_json = json_encode(array(
                                'client_id' => $client_id,
                                'diagnostic_id' => $diagnostic_id,
                                'tier_id' => $tier_id,
                                'award_scheme_id' => $award_scheme_id,
                                'd_sub_assessment_type_id' => '2',
                                'create_date' => date("Y-m-d H:i:s")
                            ));
                            $this->db->saveHistoryData($aid, 'd_assessment', $uniqueID, 'createSchoolAssessment', $client_id, $client_id, $action_assessment_json, 0, date('Y-m-d H:i:s'));
                            //end---> save the history for insert school assessment data into d_assessment table on 10-03-2016 By Mohit Kumar
                        }
			if($this->db->insert("h_assessment_user",array("user_id"=>$internal_assessor_id,"role"=>3,"assessment_id"=>$aid))){

				$auid=$this->db->get_last_insert_id();
                                if(OFFLINE_STATUS==TRUE){    
                                    //start---> save the history for insert internal school assessment data into h_assessment_user table on 10-03-2016 By Mohit Kumar
                                    $action_internal_assessment_json = json_encode(array(
                                        'user_id' => $internal_assessor_id,
                                        'role' => 3,
                                        'assessment_id' => $aid
                                    ));
                                    $this->db->saveHistoryData($auid, 'h_assessment_user', $uniqueID, 'createSchoolAssessmentInternal', $internal_assessor_id, $aid, $action_internal_assessment_json, 0, date('Y-m-d H:i:s'));
                                    //end---> save the history for insert internal school assessment data into h_assessment_user table on 10-03-2016 By Mohit Kumar
                                }
				if($this->db->insert("h_assessment_user",array("user_id"=>$external_assessor_id,"role"=>4,"assessment_id"=>$aid))){
                                        if(OFFLINE_STATUS==TRUE){    
                                            // get last insert id for external assessor on 10-03-2016 by Mohit Kumar
                                            $euid = $this->db->get_last_insert_id();
                                            //start---> save the history for insert external school assessment data into h_assessment_user table on 10-03-2016 By Mohit Kumar
                                            $action_external_assessment_json = json_encode(array(
                                                'user_id' => $external_assessor_id,
                                                'role' => 4,
                                                'assessment_id' => $aid
                                            ));
                                            $this->db->saveHistoryData($auid, 'h_assessment_user', $uniqueID, 'createSchoolAssessmentExternal', $external_assessor_id, $aid, $action_external_assessment_json, 0, date('Y-m-d H:i:s'));
                                            //end---> save the history for insert external school assessment data into h_assessment_user table on 10-03-2016 By Mohit Kumar
                                        }
					//return $aid;
                                        
					if(!empty($ext_team))

					foreach($ext_team as $member_user_id=>$roleClient)

					{

						$roleClient = explode('_',$roleClient);

						$externalClientId = $roleClient[0];

						$roleId = $roleClient[1];						

						if(! $this->db->insert("h_assessment_external_team",array("assessment_id"=>$aid,"user_role"=>4,"user_sub_role"=>$roleId,"user_id"=>$member_user_id,"external_client_id"=>$externalClientId)))

						{

							$this->db->delete("d_assessment",array("assessment_id"=>$aid));

							$this->db->delete("h_assessment_user",array("assessment_user_id"=>$auid));

							$this->db->delete("h_assessment_external_team",array("assessment_id"=>$aid));

							return false;

                                                } else {
                                                    if(OFFLINE_STATUS==TRUE){    
                                                        // get last insert id for external assessor team on 10-03-2016 by Mohit Kumar
							$etuid = $this->db->get_last_insert_id();
							//start---> save the history for insert external school assessment team data into h_assessment_user table on 10-03-2016 By Mohit Kumar
							$action_external_assessment_team_json = json_encode(array(
								'user_role' => 4,
								'assessment_id' => $aid,
								'user_sub_role' => $roleId,
								'user_id' => $member_user_id,
								"external_client_id" => $externalClientId
							));
							$this->db->saveHistoryData($etuid, 'h_assessment_external_team', $uniqueID, 
                                                            'createSchoolAssessmentExternalTeam', $member_user_id, $aid, 
                                                                $action_external_assessment_team_json, 0, date('Y-m-d H:i:s'));
							//end---> save the history for insert external school assessment team data into h_assessment_user table on 10-03-2016 By Mohit Kumar
                                                    
                                                        $this->db->addAlerts('d_assessment',$aid,$client_id,$aid,'CREATE_REVIEW');
                                                        $alertid = $this->db->get_last_insert_id();
                                                        $action_alert_json = json_encode(array(
								'table_name' => 'd_assessment',
								'content_id' => $aid,
								'content_title' => $client_id,
								'content_description' => $aid,
								"type" => 'CREATE_REVIEW'
							));
                                                        $this->db->saveHistoryData($alertid, 'd_alerts', $uniqueID, 
                                                            'createSchoolAssessmentAlert', $member_user_id, $aid, 
                                                                $action_alert_json, 0, date('Y-m-d H:i:s'));
                                                    }
                                                }	

						

					}
                                        
                                        if(OFFLINE_STATUS==TRUE){    
                                            $this->db->addAlerts('d_assessment',$aid,$client_id,$aid,'CREATE_REVIEW');
                                            $alertid = $this->db->get_last_insert_id();
                                            $action_alert_json = json_encode(array(
                                                    'table_name' => 'd_assessment',
                                                    'content_id' => $aid,
                                                    'content_title' => $client_id,
                                                    'content_description' => $aid,
                                                    "type" => 'CREATE_REVIEW'
                                            ));
                                            $this->db->saveHistoryData($alertid, 'd_alerts', $uniqueID,'createSchoolAssessmentAlert', $member_user_id, 
                                                    $aid, $action_alert_json, 0, date('Y-m-d H:i:s'));
                                        }

					return $aid;

				}else{

					$this->db->delete("d_assessment",array("assessment_id"=>$aid));

					$this->db->delete("h_assessment_user",array("assessment_user_id"=>$auid));
                                        $this->db->delete('z_history',array('table_name'=>'d_assessment','table_id'=>$aid,'action_unique_id' => $uniqueID,
                                            'action'=>'createSchoolAssessment','action_id' => $client_id));
                                        $this->db->delete('z_history',array('table_name'=>'h_assessment_user','table_id'=>$auid,
                                            'action_unique_id' => $uniqueID,'action'=>'createSchoolAssessmentInternal','action_id' => $internal_assessor_id));
				}

			}else{

				$this->db->delete("d_assessment",array("assessment_id"=>$aid));
                                $this->db->delete('z_history',array('table_name'=>'d_assessment','table_id'=>$aid,'action_unique_id' => $uniqueID,
                                            'action'=>'createSchoolAssessment','action_id' => $client_id));

			}

		}

		return false;

	}

	

	function createSchoolSelfAssessment($client_id,$internal_assessor_id,$diagnostic_id,$tier_id,$award_scheme_id,$reviewType,$isApproved,$lang_id=0){

		if($this->db->insert("d_assessment",array('client_id'=>$client_id, 'diagnostic_id'=>$diagnostic_id, 'tier_id'=>$tier_id, 'award_scheme_id'=>$award_scheme_id, 'd_sub_assessment_type_id'=>$reviewType , 'create_date'=>date("Y-m-d H:i:s"), "is_approved"=>$isApproved,'language_id'=>$lang_id))){

			$aid=$this->db->get_last_insert_id();

			if($this->db->insert("h_assessment_user",array("user_id"=>$internal_assessor_id,"role"=>3,"assessment_id"=>$aid))){

				$auid=$this->db->get_last_insert_id();

				return $aid;				

			}else{

				$this->db->delete("d_assessment",array("assessment_id"=>$aid));

			}

		}

		return false;

	}
        
        function editAssessmentStatus($assessment_id){
            return $this->db->update("h_assessment_user",array('isLeadSave'=>0),array('assessment_id'=>$assessment_id,'role'=>4));

        }
	function updateSchoolSelfAssessment($assessment_id,$client_id,$internal_assessor_id,$diagnostic_id,$tier_id,$award_scheme_id,$langId=0){

		if($this->db->update("d_assessment",array('diagnostic_id'=>$diagnostic_id, 'tier_id'=>$tier_id, 'award_scheme_id'=>$award_scheme_id,'language_id'=>$langId),array('assessment_id'=>$assessment_id))){

			if($this->db->delete("h_assessment_user",array("assessment_id"=>$assessment_id))){

					

				if($this->db->insert("h_assessment_user",array("user_id"=>$internal_assessor_id,"role"=>3,"assessment_id"=>$assessment_id))){

					$auid=$this->db->get_last_insert_id();

					return $auid;

					

				}

			}	

		}

		return false;

	}
        
        function getExternalAssessor($external_assessor_id,$role,$assessment_id) {
            
            $res=$this->db->get_results("SELECT user_id FROM h_assessment_user WHERE  user_id = ? and role = ? and assessment_id = ?",array($external_assessor_id,$role,$assessment_id));
            return $res?$res:array();
        }
        
        function getInternalAssessor($assessment_id) {
            
            $res=$this->db->get_row("SELECT a.user_id,b.name,d.client_name FROM h_assessment_user a left join d_user b on a.user_id=b.user_id left join d_assessment c on a.assessment_id=c.assessment_id left join d_client d on c.client_id=d.client_id WHERE  a.role = ? and a.assessment_id = ?",array(3,$assessment_id));
            return $res?$res:array();
        }
        
        function getExternalAssessorUpdate($role,$assessment_id) {
            
            $res=$this->db->get_row("SELECT a.user_id,b.name,d.client_name FROM h_assessment_user a left join d_user b on a.user_id=b.user_id left join d_assessment c on a.assessment_id=c.assessment_id left join d_client d on c.client_id=d.client_id WHERE  a.role = ? and a.assessment_id = ?",array($role,$assessment_id));
            return $res?$res:array();
        }
        function getReviewNotificationMembers($assessment_id) {
            $res=$this->db->get_results("SELECT user_id FROM h_user_review_notification WHERE assessment_id = ?",array($assessment_id));
            return $res?$res:array();
        }
        function getReviewTeamMembers($assessment_id) {
            $res=$this->db->get_results("SELECT user_id FROM h_assessment_external_team WHERE assessment_id = ?",array($assessment_id));
            return $res?$res:array();
        }
        function updateReimSheetSettings($assessment_id,$remSheetData = array(),$status = 2){
            
            if($status == 2)
                $this->db->delete("h_user_review_reim_sheet_status",array("assessment_id"=>$assessment_id));
            if($assessment_id && !empty($remSheetData)) {
                
                        $sheetData = array();         
                        $sheetSql = "INSERT INTO h_user_review_reim_sheet_status (assessment_id,user_id,sheet_status,date) VALUES ";
                        foreach($remSheetData as $key=>$val) {

                               $sheetSql .=  "(?,?,?,?),";
                               $sheetData[] = $assessment_id;
                               $sheetData[] = $key;
                               $sheetData[] = $val;
                               $sheetData[] = date('Y-m-d:h:i:s');
                        }
                        $sheetSql = trim($sheetSql,",");  
                      //  print_r($sheetData);die;
                        return   $this->db->query("$sheetSql", $sheetData);
             }
             return false;
        }
        function updateNotificationSettings($assessment_id,$notificationsArray,$type=1){
            
                    $this->db->delete("h_user_review_notification",array("assessment_id"=>$assessment_id,'type'=>$type));
                    if(empty($notificationsArray) && $type == 2) {
                        return true;
                    }
                    $notificationsData = array();                         
                    $notificationsSql = "INSERT INTO h_user_review_notification (notification_id,assessment_id,user_id,status,date,type) VALUES ";
                    if($assessment_id && !empty($notificationsArray)) {                         
                         $i=0;
                         foreach($notificationsArray as $key=>$val) {
                             
                             foreach($val as $k=>$v) {
                                $notificationsSql .=  "(?,?,?,?,?,?),";
                                $notificationsData[] = $v;
                                $notificationsData[] = $assessment_id;
                                $notificationsData[] = $key;
                                $notificationsData[] = 1;
                                $notificationsData[] = date('Y-m-d:h:i:s');
                                $notificationsData[] = $type;
                             }
                             $i++;
                         }
                        
                    }else if($type == 1){
                                $notificationsSql .=  "(?,?,?,?,?,?),";
                                $notificationsData[] = 0;
                                $notificationsData[] = $assessment_id;
                                $notificationsData[] = 0;
                                $notificationsData[] = 1;
                                $notificationsData[] = date('Y-m-d:h:i:s');
                                $notificationsData[] = $type;
                    }
                    $notificationsSql = trim($notificationsSql,",");  
                    //echo '<pre>'; print_r($notificationsData);die;
                    return $this->db->query("$notificationsSql", $notificationsData);
                    //return true;
        }
        function updateReminderSettings($assessment_id,$notificationsArray){
            
             $this->db->delete("h_user_review_reminders",array("assessment_id"=>$assessment_id));
                    if($assessment_id && !empty($notificationsArray)) {
                         $notificationsData = array();                         
                         $notificationsSql = "INSERT INTO h_user_review_reminders (reminder_id,assessment_id,user_id,status,date) VALUES ";
                         $i=0;
                         //print_r($notificationsArray);
                         foreach($notificationsArray as $key=>$val) {
                             
                             foreach($val as $k=>$v) {
                                $notificationsSql .=  "(?,?,?,?,?),";
                                $notificationsData[] = $v;
                                $notificationsData[] = $assessment_id;
                                $notificationsData[] = $key;
                                $notificationsData[] = 1;
                                $notificationsData[] = date('Y-m-d:h:i:s');
                             }
                             $i++;
                         }
                         $notificationsSql = trim($notificationsSql,",");  
                        //echo '<pre>'; print_r($notificationsData);
                        return $this->db->query("$notificationsSql", $notificationsData);
                    }else {
                        //return  $this->db->query("INSERT INTO h_user_review_notification (assessment_id) values(?)", array($assessment_id));
                    }
                    return false;
        }
        function addReviewNotificationSettings($assessment_id,$notificationsArray){
            
                    if($assessment_id && !empty($notificationsArray)) {
                         $notificationsData = array();                         
                         $notificationsSql = "INSERT INTO h_user_review_notification (notification_id,assessment_id,user_id,status,date,type) VALUES ";
                         $i=0;
                         //print_r($notificationsArray);
                         foreach($notificationsArray as $key=>$val) {
                             
                             foreach($val as $k=>$v) {
                                $notificationsSql .=  "(?,?,?,?,?,?),";
                                $notificationsData[] = $v;
                                $notificationsData[] = $assessment_id;
                                $notificationsData[] = $key;
                                $notificationsData[] = 1;
                                $notificationsData[] = date('Y-m-d:h:i:s');
                                $notificationsData[] = 1;
                             }
                             $i++;
                         }
                          $notificationsSql = trim($notificationsSql,",");  
                        //echo '<pre>'; print_r($notificationsArray);
                        return $this->db->query("$notificationsSql", $notificationsData);
                    }
        }
        
	function updateSchoolAssessment($assessment_id,$internal_assessor_id,$external_assessor_id,$facilitator_id,$diagnostic_id,$tier_id,$award_scheme_id,$aqs_round,$ext_team,$start_date='',$end_date='',$aqsdata_id='',$facilitatorDataArray=array(),$notificationID=0,$notificationsArray=array(),$notificationUsers = array(),$lang_id=DEFAULT_LANGUAGE,$review_criteria=""){
            //echo 'hi'.$ext_team;
            $review_criteria= trim($review_criteria);
            if(OFFLINE_STATUS==TRUE){
                    //start---> call function for creating unique id for creating assessment on 01-04-2016 by Mohit Kumar
                    $uniqueID = $this->db->createUniqueID('updateSchoolAssessment');
                    //end---> call function for creating unique id for creating assessment on 01-04-2016 by Mohit Kumar
                }
		if($this->db->update("d_assessment",array('facilitator_id'=>$facilitator_id, 'diagnostic_id'=>$diagnostic_id, 'tier_id'=>$tier_id, 'award_scheme_id'=>$award_scheme_id, 'aqs_round'=>$aqs_round,'language_id'=>$lang_id,'review_criteria'=>$review_criteria),array('assessment_id'=>$assessment_id))){
                    
                    if($assessment_id && !empty($facilitatorDataArray)) {
                         $this->db->delete("h_facilitator_user",array("assessment_id"=>$assessment_id));
                         $facilitatorSql = "INSERT INTO h_facilitator_user (assessment_id,client_id,sub_role_id,user_id) VALUES ";
                         $i=0;
                         $facilitatorData = array();
                         foreach($facilitatorDataArray as $data) {

                             $facilitatorSql .=  "(?,?,?,?),";
                             $facilitatorData[] = $assessment_id;
                             $facilitatorData[] = $data['client_id'];
                             $facilitatorData[] = $data['role_id'];
                             $facilitatorData[] = $data['user_id'];
                             $i++;
                         }
                        $facilitatorSql = trim($facilitatorSql,",");                        
                        $this->db->query("$facilitatorSql", $facilitatorData);
                    }
                   /* $this->db->delete("h_user_review_notification",array("assessment_id"=>$assessment_id));
                    if($assessment_id && !empty($notificationsArray)) {
                         $notificationsData = array();                         
                         $notificationsSql = "INSERT INTO h_user_review_notification (notification_id,assessment_id,user_id,status,date) VALUES ";
                         $i=0;
                         //print_r($notificationsArray);
                         foreach($notificationsArray as $key=>$val) {
                             
                             foreach($val as $k=>$v) {
                                $notificationsSql .=  "(?,?,?,?,?),";
                                $notificationsData[] = $v;
                                $notificationsData[] = $assessment_id;
                                $notificationsData[] = $key;
                                $notificationsData[] = 1;
                                $notificationsData[] = date('Y-m-d:h:i:s');
                             }
                             $i++;
                         }
                         $notificationsSql = trim($notificationsSql,",");  
                        //echo '<pre>'; print_r($notificationsData);
                        $this->db->query("$notificationsSql", $notificationsData);
                    }else {
                         $this->db->query("INSERT INTO h_user_review_notification (assessment_id) values(?)", array($assessment_id));
                    }*/
                    //echo $aqsdata_id;
                    $this->db->update("d_AQS_data", array('school_aqs_pref_start_date' => $start_date, 'school_aqs_pref_end_date' =>$end_date),array('id'=>$aqsdata_id));
                    //$aqs_ass_id = $this->db->get_last_insert_id();
			if(OFFLINE_STATUS==TRUE){
                            //start---> save the history for insert school assessment data into d_assessment table on 01-04-2016 By Mohit Kumar
                            $action_assessment_json = json_encode(array(
                                'diagnostic_id' => $diagnostic_id,
                                'tier_id' => $tier_id,
                                'award_scheme_id' => $award_scheme_id,
                                'create_date' => date("Y-m-d H:i:s")
                            ));
                            $this->db->saveHistoryData($assessment_id, 'd_assessment', $uniqueID, 'updateSchoolAssessment', $assessment_id, 
                                    $assessment_id, $action_assessment_json, 0, date('Y-m-d H:i:s'));
                            //end---> save the history for insert school assessment data into d_assessment table on 01-04-2016 By Mohit Kumar                    
                        }  
                        $checkExternalUser = $this->getExternalAssessorUpdate(4,$assessment_id);
                        if(count($checkExternalUser)<1) {
                            $this->db->insert("h_assessment_user",array("user_id"=>$external_assessor_id,"assessment_id"=>$assessment_id,"role"=>4));
                        }
                       // print_r($checkExternalUser);die;
                        //$this->db->update("h_assessment_user",array("user_id"=>$external_assessor_id),array("assessment_id"=>$assessment_id,"role"=>4));
                       
			if($this->db->update("h_assessment_user",array("user_id"=>$internal_assessor_id),array("assessment_id"=>$assessment_id,"role"=>3)) && $this->db->update("h_assessment_user",array("user_id"=>$external_assessor_id),array("assessment_id"=>$assessment_id,"role"=>4))){

                            if(OFFLINE_STATUS==TRUE){
                                //start---> save the history for insert school assessment data into d_assessment table on 10-03-2016 By Mohit Kumar
                                $action_assessment_user_json = json_encode(array(
                                    'assessment_id' => $assessment_id
                                ));
                                $this->db->saveHistoryData($assessment_id, 'h_assessment_user', $uniqueID, 'updateSchoolAssessmentUserRemove', 
                                        $assessment_id, $assessment_id, $action_assessment_user_json, 0, date('Y-m-d H:i:s'));
                                //end---> save the history for insert school assessment data into d_assessment table on 10-03-2016 By Mohit Kumar
                            }

                                                if($ext_team===0)
                                                    return true;
						if($this->db->delete("h_assessment_external_team",array("assessment_id"=>$assessment_id)))
						{
                                                    if(OFFLINE_STATUS==TRUE){
                                                        //start---> save the history for insert school assessment data into d_assessment table on 10-03-2016 By Mohit Kumar
                                                        $action_assessment_user_json = json_encode(array(
                                                            'assessment_id' => $assessment_id
                                                        ));
                                                        $this->db->saveHistoryData($assessment_id, 'h_assessment_external_team', $uniqueID, 
                                                                'updateSchoolAssessmentExternalRemove', 
                                                                $assessment_id, $assessment_id, $action_assessment_user_json, 0, date('Y-m-d H:i:s'));
                                                    }
							if(!empty($ext_team))

							foreach($ext_team as $member_user_id=>$roleClient)

							{

								$roleClient = explode('_',$roleClient);

								$externalClientId = $roleClient[0];

								$roleId = $roleClient[1];	

								if(! $this->db->insert("h_assessment_external_team",array("assessment_id"=>$assessment_id,"user_role"=>4,"user_sub_role"=>$roleId,"user_id"=>$member_user_id)))

								{									

									return false;

                                                                } else {
                                                                    if(OFFLINE_STATUS==TRUE){
                                                                        // get last insert id for external assessor team on 10-03-2016 by Mohit Kumar
                                                                        $etuid = $this->db->get_last_insert_id();
                                                                        //start---> save the history for insert external school assessment team data into h_assessment_user table on 10-03-2016 By Mohit Kumar
                                                                        $action_external_assessment_team_json = json_encode(array(
                                                                                'user_role' => 4,
                                                                                'assessment_id' => $assessment_id,
                                                                                'user_sub_role' => $roleId,
                                                                                'user_id' => $member_user_id,
                                                                                "external_client_id" => $externalClientId
                                                                        ));
                                                                        $this->db->saveHistoryData($etuid, 'h_assessment_external_team', $uniqueID, 'updateSchoolAssessmentExternalTeam',
                                                                                        $member_user_id, $assessment_id, $action_external_assessment_team_json, 0, date('Y-m-d H:i:s'));
                                                                    }
                                                                }	

								

							}

						}
                                              return true;  

			}		

		}

		return false;

	}

	function createStudentAssessment($client_id,$school_admin_id,$student_review_type_id,$student_profile_form_id,$student_round){

		if($this->db->insert("d_group_assessment",array('assessment_type_id'=>4,'client_id'=>$client_id, 'admin_user_id'=>$school_admin_id,'student_review_type_id'=>$student_review_type_id,'student_review_form_id'=>$student_profile_form_id,"student_round"=>$student_round,'creation_date'=>date("Y-m-d H:i:s")))){

			return $this->db->get_last_insert_id();

		}else

			return false;

	}
        
        function updateStudentAssessment($client_id,$school_admin_id,$student_review_type_id,$student_profile_form_id,$group_assessment_id,$student_round){

		if($this->db->update("d_group_assessment",array('assessment_type_id'=>4,'client_id'=>$client_id, 'admin_user_id'=>$school_admin_id,'student_review_type_id'=>$student_review_type_id,'student_review_form_id'=>$student_profile_form_id,"student_round"=>$student_round),array('group_assessment_id'=>$group_assessment_id))){

			return true;

		}else

			return false;

	}

	function createTeacherAssessment($client_id,$school_admin_id,$student_round=0){

		if($this->db->insert("d_group_assessment",array('assessment_type_id'=>2,'client_id'=>$client_id, 'admin_user_id'=>$school_admin_id,'student_round'=>$student_round,'creation_date'=>date("Y-m-d H:i:s")))){

			return $this->db->get_last_insert_id();

		}else

			return false;

	}

	function updateTeacherAssessment($client_id,$school_admin_id,$group_assessment_id,$student_round=0){

		if($this->db->update("d_group_assessment",array('assessment_type_id'=>2,'client_id'=>$client_id, 'admin_user_id'=>$school_admin_id,'student_round'=>$student_round),array('group_assessment_id'=>$group_assessment_id))){

			return true;

		}else

			return false;

	}

	

	function addExternalAssessorToGroupAssessment($group_assessment_id,$externalAssessorId,$addedByAdmin=0){

		if($this->db->insert("h_group_assessment_external_assessor",array('group_assessment_id'=>$group_assessment_id,'user_id'=>$externalAssessorId,"added_by_admin"=>$addedByAdmin))){

			return $this->db->get_last_insert_id();

		}else

			return false;

	}

	

	function removeExternalAssessorFromGroupAssessment($group_assessment_id,$externalAssessorId){

		return $this->db->delete("h_group_assessment_external_assessor",array('group_assessment_id'=>$group_assessment_id,'user_id'=>$externalAssessorId));

	}

	function removeAllExternalAssessorFromGroupAssessment($group_assessment_id){

		return $this->db->delete("h_group_assessment_external_assessor",array('group_assessment_id'=>$group_assessment_id));

	}

	

	function addTeacherToTeacherAssessment($group_assessment_id,$teacher_id,$cat_id,$assessor_id,$department_id=0,$update=0){

		if($this->db->insert("h_group_assessment_teacher",array('group_assessment_id'=>$group_assessment_id,'teacher_id'=>$teacher_id,"teacher_category_id"=>$cat_id,"assessor_id"=>$assessor_id,"school_level_id"=>$department_id))){

			//$this->db->get_last_insert_id();
                        $last_id=$this->db->get_last_insert_id();
                        if($update>0){
                        $sql_data="SELECT ga.group_assessment_id,ga.client_id,ga.creation_date,ga.grp_aqsdata_id,at.teacher_id,at.teacher_category_id,at.assessor_id,ad.diagnostic_id
                                        FROM `d_group_assessment` ga inner join h_group_assessment_teacher at on ga.group_assessment_id=at.group_assessment_id inner join h_group_assessment_diagnostic ad on at.group_assessment_id=ad.group_assessment_id and at.teacher_category_id=ad.teacher_category_id where ga.group_assessment_id=? && at.teacher_id=?";
                                        if($res_data= $this->db->get_row($sql_data,array($group_assessment_id,$teacher_id))){
                                          
                                            if($this->db->insert("d_assessment",array("client_id"=>$res_data['client_id'],"diagnostic_id"=>$res_data['diagnostic_id'],"diagnostic_id"=>$res_data['diagnostic_id'],"aqsdata_id"=>$res_data['grp_aqsdata_id'],"create_date"=>date("Y-m-d H:i:s")))){
                                            $last_id_assessment=$this->db->get_last_insert_id();
                                               
                                            }else{
                                            return false;    
                                            }
                                            
                                            if($this->db->insert("h_assessment_ass_group",array("group_assessment_id"=>$group_assessment_id,"assessment_id"=>$last_id_assessment))){
                                            
                                                
                                            }else{
                                            return false;    
                                            }
                                            
                                            if($this->db->insert("h_assessment_user",array("user_id"=>$res_data['teacher_id'],"role"=>3,"assessment_id"=>$last_id_assessment)) && $this->db->insert("h_assessment_user",array("user_id"=>$res_data['assessor_id'],"role"=>4,"assessment_id"=>$last_id_assessment))){
                                            
                                                
                                            }else{
                                            return false;    
                                            }
                                            
                                            
                                        }
                            
                        }
                        
                        return $last_id;        
		}else

			return false;

	}

	

	function removeTeacherFromTeacherAssessment($group_assessment_id,$teacher_id,$update=0){
                
                if($update>0){
                        $queryUser="select * from h_assessment_user where assessment_id=(select b.assessment_id from h_assessment_ass_group a inner join h_assessment_user b on a.assessment_id=b.assessment_id where a.group_assessment_id=? && user_id=?) && role=4";
                        if($res= $this->db->get_row($queryUser,array($group_assessment_id,$teacher_id))){
                          $assessment_id=$res['assessment_id'];
                                if(!$this->db->delete("h_assessment_user",array('assessment_id'=>$assessment_id))){
                                return false;
                                }
                                
                                if(!$this->db->delete("h_assessment_ass_group",array('assessment_id'=>$assessment_id))){
                                return false;
                                }
                                
                                if(!$this->db->delete("d_assessment",array('assessment_id'=>$assessment_id))){
                                return false;
                                }
                                
                        }else{
                        return false;    
                        }
                }
                                
		return $this->db->delete("h_group_assessment_teacher",array('group_assessment_id'=>$group_assessment_id,'teacher_id'=>$teacher_id));

	}

	

	function updateTeacherInTeacherAssessment($group_assessment_id,$teacher_id,$cat_id,$assessor_id,$department_id=0,$update=0){
                               
		if($this->db->update("h_group_assessment_teacher",array("teacher_category_id"=>$cat_id,"assessor_id"=>$assessor_id,'school_level_id'=>$department_id),array('group_assessment_id'=>$group_assessment_id,'teacher_id'=>$teacher_id))){
                        if($update>0){
                                $queryUser="select * from h_assessment_user where assessment_id=(select b.assessment_id from h_assessment_ass_group a inner join h_assessment_user b on a.assessment_id=b.assessment_id where a.group_assessment_id=? && user_id=?) && role=4";
                                if($res= $this->db->get_row($queryUser,array($group_assessment_id,$teacher_id))){
                                        if(!$this->db->update("h_assessment_user",array("user_id"=>$assessor_id),array("assessment_user_id"=>$res['assessment_user_id']))){
                                        return false;    
                                        }

                                        $sql_data="SELECT ga.group_assessment_id,ga.client_id,ga.creation_date,ga.grp_aqsdata_id,at.teacher_id,at.teacher_category_id,at.assessor_id,ad.diagnostic_id
                                        FROM `d_group_assessment` ga inner join h_group_assessment_teacher at on ga.group_assessment_id=at.group_assessment_id inner join h_group_assessment_diagnostic ad on at.group_assessment_id=ad.group_assessment_id and at.teacher_category_id=ad.teacher_category_id where ga.group_assessment_id=? && at.teacher_id=?";
                                        if($res_data= $this->db->get_row($sql_data,array($group_assessment_id,$teacher_id))){

                                                if(!$this->db->update("d_assessment",array("diagnostic_id"=>$res_data['diagnostic_id'],"aqsdata_id"=>$res_data['grp_aqsdata_id']),array("assessment_id"=>$res['assessment_id']))){
                                                return false;    
                                                }    

                                        }else{
                                        return false;    
                                        }

                                }else{
                                return false;    
                                }

                        }
                        return true;
                }
                
               return false;                         
	}

	

	function addDignosticToGroupAssessment($group_assessment_id,$teacher_cat_id,$diagnostic_id){

		if($this->db->insert("h_group_assessment_diagnostic",array('group_assessment_id'=>$group_assessment_id,'diagnostic_id'=>$diagnostic_id,"teacher_category_id"=>$teacher_cat_id))){

			return $this->db->get_last_insert_id();

		}else

			return false;

	}

	function removeDignosticToGroupAssessment($group_assessment_id){

		if($this->db->delete("h_group_assessment_diagnostic",array('group_assessment_id'=>$group_assessment_id))){

			return true;	

			return false;

		}

	}

	/*function getAssessmentList($args=array()){

		$args=$this->parse_arg($args,array("user_id"=>0,"client_id"=>0,"network_id"=>0,"client_name_like"=>"","status"=>"","reviewer"=>"","max_rows"=>10,"page"=>1,"order_by"=>"diagnostic_name","order_type"=>"asc"));

		$order_by=array("client_name"=>"c.client_name","reviewer"=>"b.role","assessment_type"=>"t.assessment_type_name","create_date"=>"a.create_date","filled_date"=>"b.ratingInputDate","status"=>"status");

		

		$sql="SELECT SQL_CALC_FOUND_ROWS a.assessment_id, a.client_id,date(a.create_date) as create_date ,c.client_name ,t.assessment_type_name, cn.network_id, group_concat(b.isFilled order by b.role) as  statuses, group_concat(b.role order by b.role) as roles, group_concat(b.percComplete order by b.role) as percCompletes, group_concat(if(b.ratingInputDate is null,'',date(b.ratingInputDate)) order by b.role) as ratingInputDates, group_concat(b.user_id order by b.role) as user_ids, group_concat(u.name order by b.role) as user_names, q.status as aqs_status,

		group_concat(concat(r.report_id,'|',r.isPublished,'|',r.publishDate)) as report_data

			FROM `d_assessment` a 

			inner join `h_assessment_user` b on a.assessment_id=b.assessment_id 

            inner join `d_client` c on c.client_id=a.client_id

			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id

			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id

			inner join d_user u on u.user_id=b.user_id

			left join h_client_network cn on cn.client_id=c.client_id

			left join d_AQS_data q on q.id=a.aqsdata_id

			left join h_assessment_report r on r.assessment_id=a.assessment_id

			where 1=1 ";

		$sqlArgs=array();

	

		if($args['client_name_like']!=""){

			$sql.="and c.client_name like ? ";

			$sqlArgs[]="%".$args['client_name_like']."%";

		}

		if($args['reviewer']>0){

			$sql.="and b.role = ? ";

			$sqlArgs[]=$args['reviewer'];

		}

		

		$sql.=" group by a.assessment_id ";

		$havHaving=false;

		if($args['network_id']>0 && $args['user_id']>0){

			$sql.=" having (cn.network_id = ".$args['network_id']." or user_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]')) ";

			$havHaving=true;

		}else if($args['client_id']>0 && $args['user_id']>0){

			$sql.=" having (a.client_id = ".$args['client_id']." or user_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]')) ";

			$havHaving=true;

		}else if($args['network_id']>0){

			$sql.=" having cn.network_id = ".$args['network_id']." ";

			$havHaving=true;

		}else if($args['client_id']>0){

			$sql.=" having a.client_id = ".$args['client_id']." ";

			$havHaving=true;

		}else if($args['user_id']>0){

			$sql.=" having user_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]') ";

			$havHaving=true;

		}

		if($args['status']!=""){

			$prefix=$havHaving?" and ":" having ";

			switch($args['status']){

				case 'iFilled':

					$sql.=$prefix."statuses rlike '^1'";

				break;

				case 'eFilled':

					$sql.=$prefix."statuses rlike '1$'";

				break;

				case 'iNotFilled':

					$sql.=$prefix."statuses rlike '^0'";

				break;

				case 'eNotFilled':

					$sql.=$prefix."statuses rlike '0$'";

				break;

				case 'bFilled':

					$sql.=$prefix."statuses = '1,1'";

				break;

				case 'bNotFilled':

					$sql.=$prefix."statuses = '0,0'";

				break;

			}

		}

		

		$sql.=" order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"a.create_date").($args["order_type"]=="desc"?" desc ":" asc ").$this->limit_query($args['max_rows'],$args['page']);

		$res= $this->db->get_results($sql,$sqlArgs);

		$lnt=count($res);

		for($j=0;$j<$lnt;$j++){

			$res[$j]['report_data']=$res[$j]['report_data']==""?array():explode(",",$res[$j]['report_data']);

			$rdc=count($res[$j]['report_data']);

			for($k=0;$k<$rdc;$k++){

				$tm=explode("|",$res[$j]['report_data'][$k]);

				$res[$j]['report_data'][$k]=array("report_id"=>$tm[0],"isPublished"=>$tm[1],"publishDate"=>$tm[2]);

			}

			

			$roles=$res[$j]['roles']!=""?explode(',',$res[$j]['roles']):array();

			$statuses=$res[$j]['statuses']!=""?explode(',',$res[$j]['statuses']):array();

			$percCompletes=$res[$j]['percCompletes']!=""?explode(',',$res[$j]['percCompletes']):array();

			$ratingInputDates=$res[$j]['ratingInputDates']!=""?explode(',',$res[$j]['ratingInputDates']):array();

			

			$user_ids=$res[$j]['user_ids']!=""?explode(',',$res[$j]['user_ids']):array();

			$user_names=$res[$j]['user_names']!=""?explode(',',$res[$j]['user_names']):array();

			

			$ln=count($roles);

			for($i=0;$i<$ln;$i++)

				$res[$j]['data_by_role'][$roles[$i]]=array("status"=>$statuses[$i],"percComplete"=>$percCompletes[$i],"ratingInputDate"=>empty($ratingInputDates[$i])?'':$ratingInputDates[$i],"user_id"=>$user_ids[$i],"user_name"=>$user_names[$i]);

		}

		$this->setPageCount($args['max_rows']); 

		return $res;

	}*/
	function getAssessmentList($args=array(),$tap_admin_id='',$user_id='',$rid='',$tap_admin_role,$ref='',$ref_key='',$is_guest=0,$logged_user=0){

                
		$args=$this->parse_arg($args,array("user_id"=>0,"sub_role_user_id"=>0,"client_id"=>0,"network_id"=>0,"province_id"=>0,"client_name_like"=>"","diagnostic_id"=>0,"name_like"=>"","status"=>"","fdate_like" => "","edate_like" => "","max_rows"=>10,"page"=>1,"order_by"=>"diagnostic_name","order_type"=>"asc"));

                $order_by=array("client_name"=>"client_name","name"=>"name","assessment_type"=>"assessment_type_name","create_date"=>"create_date","aqs_start_date"=>"aqs_start_date");

                 $schoolSqlArgs=array();

		$teacherSqlArgs=array();

		$schoolWhereClause="";

		$teacherWhereClause="";

		$teacherHavingClause=" having 1 ";

		$schoolHavingClause=" having 1 ";
                $pendingAssessmentCondition = "";
		if(isset($ref) && $ref == 2) {
                    
                    $pendingAssessmentCondition = " AND STR_TO_DATE(q.school_aqs_pref_start_date, '%d-%m-%Y') BETWEEN CURRENT_DATE() and DATE_ADD(CURRENT_DATE(), INTERVAL 7 DAY)  AND q.status is null ";
                    
                }
		

		if($args['client_name_like']!=""){

			$schoolWhereClause.="and CONCAT(c.client_name,IF(a.review_criteria IS NOT NULL,' ',''),IF(a.review_criteria IS NOT NULL,a.review_criteria,'')) like ? ";

			$teacherWhereClause.="and c.client_name like ? ";

			$schoolSqlArgs[]="%".$args['client_name_like']."%";

			$teacherSqlArgs[]="%".$args['client_name_like']."%";

		}
                
                if($args['diagnostic_id']>0){

			$schoolWhereClause.="and a.diagnostic_id = ? ";

			$teacherWhereClause.="and a.diagnostic_id = ? ";

			$schoolSqlArgs[]=$args['diagnostic_id'];

			$teacherSqlArgs[]=$args['diagnostic_id'];

		}
                
                if($args['fdate_like']!='' && $args['edate_like']==''){
                    //$sql.="and w.workshop_date_from>=? ";
                    $schoolWhereClause.="and ( (STR_TO_DATE(q.school_aqs_pref_start_date, '%d-%m-%Y') Between ? And ?) || (STR_TO_DATE(q.school_aqs_pref_end_date, '%d-%m-%Y') Between ? And ?) ) ";
                    $schoolSqlArgs[]="".$args['fdate_like']."";
                    $schoolSqlArgs[]="".$args['fdate_like']."";
                    $schoolSqlArgs[]="".$args['fdate_like']."";
                    $schoolSqlArgs[]="".$args['fdate_like']."";
                    
                    $teacherWhereClause.="and ( (STR_TO_DATE(q.school_aqs_pref_start_date, '%d-%m-%Y') Between ? And ?) || (STR_TO_DATE(q.school_aqs_pref_end_date, '%d-%m-%Y') Between ? And ?) ) ";
                    $teacherSqlArgs[]="".$args['fdate_like']."";
                    $teacherSqlArgs[]="".$args['fdate_like']."";
                    $teacherSqlArgs[]="".$args['fdate_like']."";
                    $teacherSqlArgs[]="".$args['fdate_like']."";
                    
                } else if($args['fdate_like']=='' && $args['edate_like']!=''){
                    //$sql.="and w.workshop_date_from=? ";
                    $schoolWhereClause.="and ( (STR_TO_DATE(q.school_aqs_pref_start_date, '%d-%m-%Y') Between ? And ?) || (STR_TO_DATE(q.school_aqs_pref_end_date, '%d-%m-%Y') Between ? And ?) ) ";
                    $schoolSqlArgs[]="".$args['edate_like']."";
                    $schoolSqlArgs[]="".$args['edate_like']."";
                    $schoolSqlArgs[]="".$args['edate_like']."";
                    $schoolSqlArgs[]="".$args['edate_like']."";
                    
                    $teacherWhereClause.="and ( (STR_TO_DATE(q.school_aqs_pref_start_date, '%d-%m-%Y') Between ? And ?) || (STR_TO_DATE(q.school_aqs_pref_end_date, '%d-%m-%Y') Between ? And ?) ) ";
                    $teacherSqlArgs[]="".$args['edate_like']."";
                    $teacherSqlArgs[]="".$args['edate_like']."";
                    $teacherSqlArgs[]="".$args['edate_like']."";
                    $teacherSqlArgs[]="".$args['edate_like']."";
                    
                } else if($args['fdate_like']!='' && $args['edate_like']!=''){
                    $schoolWhereClause.="and ( (STR_TO_DATE(q.school_aqs_pref_start_date, '%d-%m-%Y') Between ? And ?) || (STR_TO_DATE(q.school_aqs_pref_end_date, '%d-%m-%Y') Between ? And ?) ) ";
                    $schoolSqlArgs[]="".$args['fdate_like']."";
                    $schoolSqlArgs[]="".$args['edate_like']."";
                    $schoolSqlArgs[]="".$args['fdate_like']."";
                    $schoolSqlArgs[]="".$args['edate_like']."";
                    
                    $teacherWhereClause.="and ( (STR_TO_DATE(q.school_aqs_pref_start_date, '%d-%m-%Y') Between ? And ?) || (STR_TO_DATE(q.school_aqs_pref_end_date, '%d-%m-%Y') Between ? And ?) ) ";
                    $teacherSqlArgs[]="".$args['fdate_like']."";
                    $teacherSqlArgs[]="".$args['edate_like']."";
                    $teacherSqlArgs[]="".$args['fdate_like']."";
                    $teacherSqlArgs[]="".$args['edate_like']."";
                }
                
		if($args['name_like']!=""){
		
			$schoolHavingClause.=" and group_concat(u.name order by b.role) like ?";
		
			$teacherHavingClause.=" and group_concat(u.name order by b.role) like ?";
		
			$schoolSqlArgs[]="%".$args['name_like']."%";
		
			$teacherSqlArgs[]="%".$args['name_like']."%";
		
		}                		
		if($args['network_id']>0 && $args['user_id']>0){

			$teacherHavingClause.=" and (cn.network_id = ".$args['network_id']." or user_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]')) ";

			//$schoolHavingClause.=" and (cn.network_id = ".$args['network_id']." or user_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]')) ";
		        $schoolHavingClause.=" and (cn.network_id = ".$args['network_id']." or user_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]') or externalTeam rlike concat('[[:<:]]',".($args['sub_role_user_id']?$args['sub_role_user_id']:0).",'[[:>:]]')) ";

		}else if($args['client_id']>0 && $args['user_id']>0){

			$teacherHavingClause.=" and (client_id = ".$args['client_id']." or user_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]')) ";

			//$schoolHavingClause.=" and (client_id = ".$args['client_id']." or user_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]')) ";
			$schoolHavingClause.=" and (client_id = ".$args['client_id']." or user_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]') or externalTeam rlike concat('[[:<:]]',".($args['sub_role_user_id']?$args['sub_role_user_id']:0).",'[[:>:]]')) ";

		}else if($args['network_id']>0){

			$teacherHavingClause.=" and cn.network_id = ".$args['network_id']." ";

			$schoolHavingClause.=" and cn.network_id = ".$args['network_id']." ";

		}else if($args['client_id']>0){

			$teacherHavingClause.=" and client_id = ".$args['client_id']." ";

			$schoolHavingClause.=" and client_id = ".$args['client_id']." ";

		}else if($args['user_id']>0){

			$teacherHavingClause.=" and user_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]') ";

			$schoolHavingClause.=" and user_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]') or externalTeam rlike concat('[[:<:]]',".($args['sub_role_user_id']?$args['sub_role_user_id']:0).",'[[:>:]]') or leader_ids  rlike concat('[[:<:]]',".$args['user_id'].",'[[:>:]]') ";

		}
                $kpaCond='';
                //if($args['user_id']>0){

			//$kpaCond = 'left join d_assessment_kpa kp on kp.assessment_id=a.assessment_id and kp.user_id='.$args['user_id'];
			$kpaCond = 'left join d_assessment_kpa kp on kp.assessment_id=a.assessment_id ';


		//}
                
                if($args['province_id']>0){

			$teacherWhereClause.=" and pn.province_id = ".$args['province_id']." ";

			$schoolWhereClause.=" and pn.province_id = ".$args['province_id']." ";

		}
		//type of review
		if($args['assessment_type_id']!="")
		{			
			switch($args['assessment_type_id']){
				case 'sch' :
                                case 1     :    
					$schoolWhereClause.=" and a.d_sub_assessment_type_id!=1 and d.assessment_type_id=1";
					$teacherWhereClause.=" and 1=0";
					break;
				case 'schs' :
					$schoolWhereClause.=" and a.d_sub_assessment_type_id=1 and d.assessment_type_id=1";
					$teacherWhereClause.=" and 1=0";
					break;
				case 'tchr' :
                                case 2      :
					$schoolWhereClause.=" and 1=0";
                                        $teacherWhereClause.=" and dt.assessment_type_id=2 ";
					break;
                                case 'stu' :
                                case 4     :    
					$schoolWhereClause.=" and 1=0";
                                        $teacherWhereClause.=" and dt.assessment_type_id=4 ";
					break;
                                case 'col' :
                                case 5     :     
					$schoolWhereClause.=" and a.d_sub_assessment_type_id!=1 and d.assessment_type_id=5";
					$teacherWhereClause.=" and 1=0";
					break;    
    
			}
		}		
                // make condition for getting only school reviews for tap admin on 18-05-2016 by Mohit Kumar
                if($tap_admin_id==8){
                    $tap_condition = " and d.assessment_type_id='1' and d_sub_assessment_type_id!='1' ";
                } else {
                    $tap_condition='';
                }
                if($user_id!='' && $rid!=''){
                    $getExternalReviewsId = $this->getAssessmentIDsNew($user_id,$rid);
                   
                    if($getExternalReviewsId['assessment_id']!=''){
                        $externalAssessorCondition = " and a.assessment_id IN (".$getExternalReviewsId['assessment_id'].") ";
                    } else {
                        $externalAssessorCondition='';
                    }
                } else {
                    $externalAssessorCondition='';
                }
                $condition='';
                
                if(isset($ref) && $ref==1 && $ref_key!=''){
                    $SQL1="Select alert_ids as assessment_id from h_alert_relation where login_user_role='".$tap_admin_role."' and type='REVIEW'";
                    $assessment_id = $this->db->get_row($SQL1);
                    if(!empty($assessment_id) && $assessment_id['assessment_id']!=''){
                        $condition = " and  a.assessment_id In (".$assessment_id['assessment_id'].")  ";
                    }
                }
                
                $langCond ='';
                
                //echo"aaa". $this->lang;
                if($this->lang !='all') {
                    $langCond = "and a.language_id=".$this->lang;
                }


               
               $guestCond=$is_guest?" && c.is_guest=1 ":" && c.is_guest!=1 ";
               $isActive_Inactive_Assessment=" && a.isAssessmentActive=1 ";
               $isActive_Inactive_Group_Assessment=" && dt.isGroupAssessmentActive=1 ";


               $sql="
		select SQL_CALC_FOUND_ROWS z.* 

		from (

			(


				SELECT  a.collaborativepercntg as avg,b.isFilled,a.iscollebrative,d.diagnostic_id,dt.group_assessment_id,dt.admin_user_id,dt.student_round as assessment_round,q.is_uploaded,q.percComplete as aqspercent,STR_TO_DATE(q.school_aqs_pref_start_date, '%d-%m-%Y') as aqs_start_date,STR_TO_DATE(q.school_aqs_pref_end_date, '%d-%m-%Y') as aqs_end_date, 0 as assessment_id, dt.assessment_type_id, dt.client_id,dt.creation_date as create_date ,c.client_name ,t.assessment_type_name, cn.network_id, group_concat(b.isFilled order by b.role,a.assessment_id) as  statuses, group_concat(distinct b.role order by b.role) as roles, group_concat(b.percComplete order by b.role,a.assessment_id) as percCompletes, group_concat(if(b.ratingInputDate is null,'',date(b.ratingInputDate)) order by b.role,a.assessment_id) as ratingInputDates, group_concat(b.user_id order by b.role,a.assessment_id) as user_ids, group_concat(u.name order by b.role,a.assessment_id) as user_names, q.status as aqs_status,group_concat(concat(r.report_id,'|',r.isPublished,'|',r.publishDate)) as report_data,count(distinct s.assessment_id) as assessments_count, CASE WHEN dt.assessment_type_id = 2 THEN group_concat(if(td.value is null,'',td.value) order by b.role,a.assessment_id)  WHEN dt.assessment_type_id = 4 THEN  group_concat(if(sd.value is null,'',sd.value) order by b.role,a.assessment_id) END as teacherInfoStatuses, ifnull(a.d_sub_assessment_type_id,0) as 'subAssessmentType',

				aqsdata_id,p.status as 'post_rev_status',p.percComplete as 'postreviewpercent',a.is_approved as 'isApproved', hlt.translation_text as diagnosticName, '' as externalTeam,'' as externalPercntage,'' as extFilled,'' as kpa,'' as leader_ids,'' as kpa_user

				FROM d_group_assessment dt

				left join h_assessment_ass_group s on s.group_assessment_id = dt.group_assessment_id

				left join `d_assessment` a  on a.assessment_id = s.assessment_id

				left join `h_assessment_user` b on a.assessment_id=b.assessment_id

				left join d_teacher_data td on td.teacher_id=b.user_id and b.role=3 and td.assessment_id=b.assessment_id and td.attr_id=11
                                
                                left join d_student_data sd on sd.student_id=b.user_id and b.role=3 and sd.assessment_id=b.assessment_id and sd.attr_id=49

				left join `d_client` c on c.client_id=dt.client_id

				left join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
                                left join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id && hlt.language_id=a.language_id

				left join `d_assessment_type` t on dt.assessment_type_id=t.assessment_type_id

				left join d_user u on u.user_id=b.user_id

				left join h_client_network cn on cn.client_id=c.client_id

				left join d_AQS_data q on q.id=a.aqsdata_id

				left join h_assessment_report r on r.assessment_id=a.assessment_id
                                $kpaCond
				left join d_post_review p on p.assessment_id=a.assessment_id
                                left join h_client_province cp on cp.client_id = c.client_id	
                                left join h_province_network pn on pn.network_id = cn.network_id and cp.province_id = pn.province_id	
                                
                                where 1=1  ".$isActive_Inactive_Group_Assessment." ".$guestCond." ".$langCond." ".$tap_condition.$externalAssessorCondition.$condition." ".$teacherWhereClause. $pendingAssessmentCondition."


				
                                group by dt.group_assessment_id

				$teacherHavingClause

			)

			union

			(


				SELECT a.collaborativepercntg as avg,b.isFilled,a.iscollebrative,d.diagnostic_id,0 as group_assessment_id,0 as admin_user_id,a.aqs_round as assessment_round,q.is_uploaded,q.percComplete as aqspercent,STR_TO_DATE(q.school_aqs_pref_start_date, '%d-%m-%Y') as aqs_start_date,STR_TO_DATE(q.school_aqs_pref_end_date, '%d-%m-%Y') as aqs_end_date,a.assessment_id, d.assessment_type_id, a.client_id,a.create_date as create_date ,CONCAT(c.client_name,IF((a.review_criteria IS NOT NULL && a.review_criteria!=''),' - ',''),IF((a.review_criteria IS NOT NULL  && a.review_criteria!=''),a.review_criteria,'')) ,t.assessment_type_name, cn.network_id, group_concat(b.isFilled order by b.role) as  statuses, group_concat(b.role order by b.role) as roles, group_concat(b.percComplete order by b.role) as percCompletes, group_concat(if(b.ratingInputDate is null,'',date(b.ratingInputDate)) order by b.role) as ratingInputDates, group_concat(b.user_id order by b.role) as user_ids, group_concat(u.name order by b.role) as user_names, q.status as aqs_status,
				group_concat(concat(r.report_id,'|',r.isPublished,'|',r.publishDate)) as report_data, 1 as assessments_count,'' as teacherInfoStatuses, ifnull(a.d_sub_assessment_type_id,0) as 'subAssessmentType'				

                ,aqsdata_id,p.status as 'post_rev_status',p.percComplete as 'postreviewpercent',

                a.is_approved as 'isApproved',

                 hlt.translation_text as diagnosticName ,group_concat(distinct ext.user_id) as externalTeam,ext.percComplete as externalPercntage,ext.isFilled as extFilled,group_concat(distinct kp.kpa_instance_id) as kpa,group_concat(haa1.leader) as leader_ids,group_concat(distinct kp.user_id) as kpa_user

					FROM `d_assessment` a 

					inner join `h_assessment_user` b on a.assessment_id=b.assessment_id 

					inner join `d_client` c on c.client_id=a.client_id

					inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id

					inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id

					inner join d_user u on u.user_id=b.user_id
                                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id && hlt.language_id=a.language_id

					left join h_client_network cn on cn.client_id=c.client_id

					left join d_AQS_data q on q.id=a.aqsdata_id

					left join h_assessment_report r on r.assessment_id=a.assessment_id					

					left join d_post_review p on p.assessment_id=a.assessment_id
                                        left join h_client_province cp on cp.client_id = c.client_id	
                                        left join h_province_network pn on pn.network_id = cn.network_id and cp.province_id = pn.province_id
                                        left join h_assessment_external_team ext on ext.assessment_id = a.assessment_id and ext.user_id = ".(!empty($args['sub_role_user_id'])?$args['sub_role_user_id']:0)."					


                                        left join assessor_key_notes akn on a.assessment_id=akn.assessment_id && akn.type='recommendation'
                                        
                                        left join h_assessor_action1 haa1 on akn.id=haa1.assessor_key_notes_id

                                        $kpaCond
                                        where (d.assessment_type_id=1 || d.assessment_type_id=5) ".$isActive_Inactive_Assessment." ".$guestCond." ".$langCond."  " .$tap_condition.$externalAssessorCondition.$condition." ".$schoolWhereClause . $pendingAssessmentCondition."
					
                                        group by a.assessment_id

				$schoolHavingClause

			)

		) as z";
		
		
	         $sql.=" order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"create_date").($args["order_type"]=="desc"?" desc ":" asc ").$this->limit_query($args['max_rows'],$args['page']);

		$res= $this->db->get_results($sql,array_merge($teacherSqlArgs,$schoolSqlArgs));

		//echo $sql;
                $this->setPageCount($args['max_rows']); 
                //get external team roles
                $roleRes = array();
                if(!empty($logged_user) && !in_array($tap_admin_role,array(1,2,5,8))) {
                      $roleSql = "SELECT ex.assessment_id,ex.user_sub_role,ex.user_id FROM h_assessment_external_team ex "
                             . " INNER JOIN d_assessment d ON d.assessment_id = ex.assessment_id "
                             . "WHERE ex.user_id = '$logged_user'";
                     $roleRes = $this->db->get_results($roleSql);
                     $roleRes = array_column($roleRes,'user_sub_role','assessment_id');
                }
                /*if(!empty($logged_user) && !in_array($tap_admin_role,array(1,2,5,8))) {
                      $externalTeamSql = "SELECT u.name,ex.assessment_id,ex.user_sub_role,ex.user_id,ex.percComplete,ex.ratingInputDate,ex.user_role FROM h_assessment_external_team ex "
                             . " INNER JOIN d_assessment d ON d.assessment_id = ex.assessment_id "
                             . " INNER JOIN d_user u ON u.user_id = ex.user_id  WHERE d.iscollebrative = ?";
                             
                     $team = $this->db->get_results($externalTeamSql,array(1));
                     $externalTeam = array();
                    // foreach($team as $data){
                         //$externalTeam[$data['assessment_id']] = $data;
                    // }
                    //$externalTeam =  array_filter(array_column($externalTeam, 'assessment_id'));
                     echo "<pre>";print_r($externalTeam);
                }*/
                
                //echo "<pre>";print_r($roleRes);

		$lnt=count($res);

		for($j=0;$j<$lnt;$j++){

			if($res[$j]['assessment_type_id']==1 || $res[$j]['assessment_type_id']==5){

				$res[$j]['report_data']=$res[$j]['report_data']==""?array():explode(",",$res[$j]['report_data']);

				$rdc=count($res[$j]['report_data']);

				for($k=0;$k<$rdc;$k++){

					$tm=explode("|",$res[$j]['report_data'][$k]);

					$res[$j]['report_data'][$k]=array("report_id"=>$tm[0],"isPublished"=>$tm[1],"publishDate"=>$tm[2]);

				}
				
				

				$roles=$res[$j]['roles']!=""?explode(',',$res[$j]['roles']):array();

				$statuses=$res[$j]['statuses']!=""?explode(',',$res[$j]['statuses']):array();

				$percCompletes=$res[$j]['percCompletes']!=""?explode(',',$res[$j]['percCompletes']):array();

				$ratingInputDates=$res[$j]['ratingInputDates']!=""?explode(',',$res[$j]['ratingInputDates']):array();
				
				

				$user_ids=$res[$j]['user_ids']!=""?explode(',',$res[$j]['user_ids']):array();

				$user_names=$res[$j]['user_names']!=""?explode(',',$res[$j]['user_names']):array();
				
				

				$ln=count($roles);

				for($i=0;$i<$ln;$i++){
                                        
                                        //echo $roleRes[$res[$j]['assessment_id']];
					$res[$j]['data_by_role'][$roles[$i]]=array("status"=>$statuses[$i],"percComplete"=>$percCompletes[$i],"ratingInputDate"=>empty($ratingInputDates[$i])?'':$ratingInputDates[$i],"user_id"=>$user_ids[$i],"user_name"=>$user_names[$i]);
                                        if(!empty($roleRes) && isset($roleRes[$res[$j]['assessment_id']])) { 
                                            //echo"<br>". $roleRes[$res[$j]['assessment_id']];
                                            $res[$j]['data_by_role'][$roleRes[$res[$j]['assessment_id']]]=array("status"=>$statuses[$i],"percComplete"=>$percCompletes[$i],"ratingInputDate"=>empty($ratingInputDates[$i])?'':$ratingInputDates[$i],"user_id"=>$user_ids[$i],"user_name"=>$user_names[$i]);
                                        }
                                }
			}else{

				$roles=$res[$j]['roles']!=""?explode(',',$res[$j]['roles']):array();

				$percCompletes=$res[$j]['percCompletes']!=""?explode(',',$res[$j]['percCompletes']):array();

				$statuses=$res[$j]['statuses']!=""?explode(',',$res[$j]['statuses']):array();

				$sz=count($percCompletes);

				$allStatuses=array(array(),array());
                                
                                $user_ids=$res[$j]['user_ids']!=""?explode(',',$res[$j]['user_ids']):array();

				$user_names=$res[$j]['user_names']!=""?explode(',',$res[$j]['user_names']):array();
				

				if($sz && $sz%2==0){

					$sz=$sz/2;

					$percCompletes=array_chunk($percCompletes,$sz);

					$percCompletes=array(round(array_sum($percCompletes[0])/$sz,2),round(array_sum($percCompletes[1])/$sz,2));
					
					

					$allStatuses=array_chunk($statuses,$sz);

					$statuses=array(in_array(0,$allStatuses[0])?0:1,in_array(0,$allStatuses[1])?0:1);
                                        
                                        $allUsers=array_chunk($user_ids,$sz);

					$user_ids=array($allUsers[0],$allUsers[1]);

				}
                                
				$ln=count($roles);

				for($i=0;$i<$ln;$i++)

				$res[$j]['data_by_role'][$roles[$i]]=array("status"=>$statuses[$i],"percComplete"=>$percCompletes[$i],"allStatuses"=>$allStatuses[$i],"user_ids"=>$user_ids[$i]);

				$res[$j]['teacherInfoStatuses']=explode(",",$res[$j]['teacherInfoStatuses']);

			}

		}

		
                
		return $res;

	}

	

	function getAssessmentsInGroupAssessment($group_assessment_id,$user_id=0,$lang_id=DEFAULT_LANGUAGE){
                //echo $group_assessment_id;                
		     $sql="SELECT a.iscollebrative,ag.group_assessment_id,0 as admin_user_id,a.assessment_id, d.assessment_type_id, a.client_id,a.create_date as create_date ,c.client_name ,t.assessment_type_name, cn.network_id, group_concat(b.isFilled order by b.role) as  statuses, group_concat(b.role order by b.role) as roles, group_concat(b.percComplete order by b.role) as percCompletes, group_concat(if(b.ratingInputDate is null,'',date(b.ratingInputDate)) order by b.role) as ratingInputDates, group_concat(b.user_id order by b.role) as user_ids, group_concat(CONCAT(u.name,IF(sd_1.value IS NOT NULL ,' (',''),IFNULL(sd_1.value, ''),IF(sd_1.value IS NOT NULL ,')','')) order by b.role) as user_names, q.status as aqs_status, CASE WHEN d.assessment_type_id = 2 THEN if(sum(td.value)>0,1,0) WHEN d.assessment_type_id = 4 THEN if(sum(sd.value)>0,1,0) END as isTchrInfoFilled,

			group_concat(concat(r.report_id,'|',r.isPublished,'|',r.publishDate)) as report_data, 1 as assessments_count,d.diagnostic_id,hlt.translation_text as diagnosticName

			FROM h_assessment_ass_group ag

			inner join `d_assessment` a on ag.assessment_id=a.assessment_id

			inner join `h_assessment_user` b on a.assessment_id=b.assessment_id 

			inner join `d_client` c on c.client_id=a.client_id

			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
                        
                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id

			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id

			inner join d_user u on u.user_id=b.user_id

			left join h_client_network cn on cn.client_id=c.client_id

			left join d_AQS_data q on q.id=a.aqsdata_id

			left join h_assessment_report r on r.assessment_id=a.assessment_id
                      
                        left join d_teacher_data td on td.teacher_id=b.user_id and b.role=3 and td.assessment_id=a.assessment_id and td.attr_id=11
                       
                        left join d_student_data sd on sd.student_id=b.user_id and b.role=3 and sd.assessment_id=a.assessment_id and sd.attr_id=49
                        
                        left join d_student_data sd_1 on sd_1.student_id=b.user_id and b.role=3 and sd_1.assessment_id=a.assessment_id and sd_1.attr_id=4

			where d.assessment_type_id>1 and ag.group_assessment_id=? and hlt.language_id=?

			group by a.assessment_id";

		$sqlArgs=array($group_assessment_id,$lang_id);

		if($user_id>0){

			$sql.=" having user_ids  rlike concat('[[:<:]]',?,'[[:>:]]')";

			$sqlArgs[]=$user_id;

		}

		$res=$this->db->get_results($sql,$sqlArgs);

		$lnt=count($res);

		for($j=0;$j<$lnt;$j++){

			$res[$j]['report_data']=$res[$j]['report_data']==""?array():explode(",",$res[$j]['report_data']);

			$rdc=count($res[$j]['report_data']);

			for($k=0;$k<$rdc;$k++){

				$tm=explode("|",$res[$j]['report_data'][$k]);

				$res[$j]['report_data'][$k]=array("report_id"=>$tm[0],"isPublished"=>$tm[1],"publishDate"=>$tm[2]);

			}

			

			$roles=$res[$j]['roles']!=""?explode(',',$res[$j]['roles']):array();

			$statuses=$res[$j]['statuses']!=""?explode(',',$res[$j]['statuses']):array();

			$percCompletes=$res[$j]['percCompletes']!=""?explode(',',$res[$j]['percCompletes']):array();

			$ratingInputDates=$res[$j]['ratingInputDates']!=""?explode(',',$res[$j]['ratingInputDates']):array();

			

			$user_ids=$res[$j]['user_ids']!=""?explode(',',$res[$j]['user_ids']):array();

			$user_names=$res[$j]['user_names']!=""?explode(',',$res[$j]['user_names']):array();

			

			$ln=count($roles);

			for($i=0;$i<$ln;$i++)

				$res[$j]['data_by_role'][$roles[$i]]=array("status"=>$statuses[$i],"percComplete"=>$percCompletes[$i],"ratingInputDate"=>empty($ratingInputDates[$i])?'':$ratingInputDates[$i],"user_id"=>$user_ids[$i],"user_name"=>$user_names[$i]);

		}

		return $res;

	}
	function getAssessmentCountInGroupAssessmentDiagnostic($group_assessment_id,$diagnostic_id,$dept_id=0){
		$sql = "select b.name from h_assessment_user a inner join d_user b on a.user_id=b.user_id where a.role=3 and assessment_id in(
			SELECT distinct a.assessment_id			
	
			FROM h_assessment_ass_group ag
	
			inner join `d_assessment` a on ag.assessment_id=a.assessment_id
	
			inner join `h_assessment_user` b on a.assessment_id=b.assessment_id
                        
                        inner join (select user_id,assessment_id from `h_assessment_user` where role=3) bu on a.assessment_id=bu.assessment_id 
	
			inner join `d_client` c on c.client_id=a.client_id
	
			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
	
			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id
	
			inner join d_user u on u.user_id=b.user_id
                        
                        left  join h_group_assessment_teacher hgat on hgat.group_assessment_id=ag.group_assessment_id && hgat.teacher_id=bu.user_id
			left  join d_school_level dsl on hgat.school_level_id=dsl.school_level_id
	
			where (d.assessment_type_id=2 || d.assessment_type_id=4) and ag.group_assessment_id=? and d.diagnostic_id=? and b.isFilled!=1 ";
                        
                        if(!empty($dept_id)){
                        $sql.=" && hgat.school_level_id=? ";   
                        }
	
			$sql .= " group by a.assessment_id)";
                if(!empty($dept_id)){
		$res = $this->db->get_results($sql,array($group_assessment_id,$diagnostic_id,$dept_id));
                }else{
                $res = $this->db->get_results($sql,array($group_assessment_id,$diagnostic_id));    
                }
		return $res?$res:array();
	}
        
        function getAssessmentCompletedCountInGroupAssessmentDiagnostic($group_assessment_id,$diagnostic_id){
		$sql = "select b.name from h_assessment_user a inner join d_user b on a.user_id=b.user_id where a.role=3 and assessment_id in(
			SELECT distinct a.assessment_id			
	
			FROM h_assessment_ass_group ag
	
			inner join `d_assessment` a on ag.assessment_id=a.assessment_id
	
			inner join `h_assessment_user` b on a.assessment_id=b.assessment_id
	
			inner join `d_client` c on c.client_id=a.client_id
	
			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
	
			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id
	
			inner join d_user u on u.user_id=b.user_id				
	
			where (d.assessment_type_id=2 || d.assessment_type_id=4) and ag.group_assessment_id=? and d.diagnostic_id=? and b.isFilled=1
	
			group by a.assessment_id)";
		$res = $this->db->get_results($sql,array($group_assessment_id,$diagnostic_id));
		return $res?$res:array();
	}
        
        function getSchoolDepartment($level_id){
         $sql="select school_level from d_school_level where school_level_id=?"; 
         return $this->db->get_row($sql,array($level_id));
         
        }
        
	function getAssessmentsInGroupAssessmentDiagnostic($group_assessment_id,$diagnostic_id,$lang_id=DEFAULT_LANGUAGE,$dept_id=0){
	
		$sql="SELECT ag.group_assessment_id,0 as admin_user_id,dga.student_round,a.assessment_id, d.assessment_type_id, a.client_id,a.create_date as create_date ,c.client_name ,t.assessment_type_name, cn.network_id, group_concat(b.isFilled order by b.role) as  statuses, group_concat(b.role order by b.role) as roles, group_concat(b.percComplete order by b.role) as percCompletes, group_concat(if(b.ratingInputDate is null,'',date(b.ratingInputDate)) order by b.role) as ratingInputDates, group_concat(b.user_id order by b.role) as user_ids, group_concat(u.name order by b.role) as user_names, q.status as aqs_status, CASE WHEN d.assessment_type_id = 2 THEN if(sum(td.value)>0,1,0) WHEN d.assessment_type_id = 4 THEN if(sum(sd.value)>0,1,0) END as isTchrInfoFilled,
	
			group_concat(concat(r.report_id,'|',r.isPublished,'|',r.publishDate)) as report_data, 1 as assessments_count,d.diagnostic_id,hlt.translation_text as diagnosticName
	
			FROM h_assessment_ass_group ag
	                
                        inner join `d_group_assessment` dga on ag.group_assessment_id=dga.group_assessment_id
                        
			inner join `d_assessment` a on ag.assessment_id=a.assessment_id
	
			inner join `h_assessment_user` b on a.assessment_id=b.assessment_id
                        
                        inner join (select user_id,assessment_id from `h_assessment_user` where role=3) bu on a.assessment_id=bu.assessment_id
	
			inner join `d_client` c on c.client_id=a.client_id
	
			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id
	
			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id
	
			inner join d_user u on u.user_id=b.user_id
	
			left join h_client_network cn on cn.client_id=c.client_id
	
			left join d_AQS_data q on q.id=a.aqsdata_id
	
			left join h_assessment_report r on r.assessment_id=a.assessment_id
	
			left join d_teacher_data td on td.teacher_id=b.user_id and b.role=3 and td.assessment_id=a.assessment_id and td.attr_id=11
	                left join d_student_data sd on sd.student_id=b.user_id and b.role=3 and sd.assessment_id=a.assessment_id and sd.attr_id=49 ";
                        
                        $sql.=" left  join h_group_assessment_teacher hgat on hgat.group_assessment_id=ag.group_assessment_id && hgat.teacher_id=bu.user_id
			left  join d_school_level dsl on hgat.school_level_id=dsl.school_level_id ";    
                
			
                        $sql.=" where d.assessment_type_id>1 and hlt.language_id=? and ag.group_assessment_id=? and d.diagnostic_id=?";
                        
                        if(!empty($dept_id)){
                        $sql.=" && hgat.school_level_id=? ";   
                        }
	
			$sql.=" group by a.assessment_id";
                        
                if(!empty($dept_id)){                
		$sqlArgs=array($lang_id,$group_assessment_id,$diagnostic_id,$dept_id);
                }else{
                $sqlArgs=array($lang_id,$group_assessment_id,$diagnostic_id);    
                }
	
		if(isset($user_id) && $user_id>0){
	
			$sql.=" having user_ids  rlike concat('[[:<:]]',?,'[[:>:]]')";
	
			$sqlArgs[]=$user_id;
	
		}
	
		$res=$this->db->get_results($sql,$sqlArgs);
	
		$lnt=count($res);
	
		for($j=0;$j<$lnt;$j++){
	
			$res[$j]['report_data']=$res[$j]['report_data']==""?array():explode(",",$res[$j]['report_data']);
	
			$rdc=count($res[$j]['report_data']);
	
			for($k=0;$k<$rdc;$k++){
	
				$tm=explode("|",$res[$j]['report_data'][$k]);
	
				$res[$j]['report_data'][$k]=array("report_id"=>$tm[0],"isPublished"=>$tm[1],"publishDate"=>$tm[2]);
	
			}
	
				
	
			$roles=$res[$j]['roles']!=""?explode(',',$res[$j]['roles']):array();
	
			$statuses=$res[$j]['statuses']!=""?explode(',',$res[$j]['statuses']):array();
	
			$percCompletes=$res[$j]['percCompletes']!=""?explode(',',$res[$j]['percCompletes']):array();
	
			$ratingInputDates=$res[$j]['ratingInputDates']!=""?explode(',',$res[$j]['ratingInputDates']):array();
	
				
	
			$user_ids=$res[$j]['user_ids']!=""?explode(',',$res[$j]['user_ids']):array();
	
			$user_names=$res[$j]['user_names']!=""?explode(',',$res[$j]['user_names']):array();
	
				
	
			$ln=count($roles);
	
			for($i=0;$i<$ln;$i++)
	
				$res[$j]['data_by_role'][$roles[$i]]=array("status"=>$statuses[$i],"percComplete"=>$percCompletes[$i],"ratingInputDate"=>empty($ratingInputDates[$i])?'':$ratingInputDates[$i],"user_id"=>$user_ids[$i],"user_name"=>$user_names[$i]);
	
		}
	
		return $res;
	
	}
        
        function getAssessmentsInGroupAssessmentDiagnostic_rounds($group_assessment_id_r1,$group_assessment_id_r2,$diagnostic_id,$students_allow=array(),$lang_id=DEFAULT_LANGUAGE){
	
		$sql="SELECT ag.group_assessment_id,0 as admin_user_id,dga.student_round,a.assessment_id, d.assessment_type_id, a.client_id,a.create_date as create_date ,c.client_name ,t.assessment_type_name, cn.network_id, group_concat(b.isFilled order by b.role) as  statuses, group_concat(b.role order by b.role) as roles, group_concat(b.percComplete order by b.role) as percCompletes, group_concat(if(b.ratingInputDate is null,'',date(b.ratingInputDate)) order by b.role) as ratingInputDates,bu.user_id, group_concat(b.user_id order by b.role) as user_ids, group_concat(u.name order by b.role) as user_names, q.status as aqs_status, CASE WHEN d.assessment_type_id = 2 THEN if(sum(td.value)>0,1,0) WHEN d.assessment_type_id = 4 THEN if(sum(sd.value)>0,1,0) END as isTchrInfoFilled,
	
			group_concat(concat(r.report_id,'|',r.isPublished,'|',r.publishDate)) as report_data, 1 as assessments_count,d.diagnostic_id,hlt.translation_text as diagnosticName
	
			FROM h_assessment_ass_group ag
	                
                        inner join `d_group_assessment` dga on ag.group_assessment_id=dga.group_assessment_id
                        
			inner join `d_assessment` a on ag.assessment_id=a.assessment_id
	
			inner join `h_assessment_user` b on a.assessment_id=b.assessment_id
                        
                        inner join (select * from `h_assessment_user` where role=3) bu on a.assessment_id=bu.assessment_id
	                  
			inner join `d_client` c on c.client_id=a.client_id
	
			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id
	
			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id
	
			inner join d_user u on u.user_id=b.user_id
	
			left join h_client_network cn on cn.client_id=c.client_id
	
			left join d_AQS_data q on q.id=a.aqsdata_id
	
			left join h_assessment_report r on r.assessment_id=a.assessment_id
	
			left join d_teacher_data td on td.teacher_id=b.user_id and b.role=3 and td.assessment_id=a.assessment_id and td.attr_id=11
	                left join d_student_data sd on sd.student_id=b.user_id and b.role=3 and sd.assessment_id=a.assessment_id and sd.attr_id=49
			where d.assessment_type_id>1 and hlt.language_id=? and (ag.group_assessment_id=? || ag.group_assessment_id=?) and d.diagnostic_id=? ";
                        
                       if(count($students_allow)>0){
                           
                         $sql.=" && bu.user_id IN (".(implode(",",$students_allow)).") ";  
                       }
	
			$sql.=" group by a.assessment_id ";
	
		$sqlArgs=array($lang_id,$group_assessment_id_r1,$group_assessment_id_r2,$diagnostic_id);
	
		if(isset($user_id) && $user_id>0){
	
			$sql.=" having user_ids  rlike concat('[[:<:]]',?,'[[:>:]]')";
	
			$sqlArgs[]=$user_id;
	
		}
                
                $sql.=" order by bu.user_id,dga.student_round";
	
		$res=$this->db->get_results($sql,$sqlArgs);
	
		$lnt=count($res);
	
		for($j=0;$j<$lnt;$j++){
	
			$res[$j]['report_data']=$res[$j]['report_data']==""?array():explode(",",$res[$j]['report_data']);
	
			$rdc=count($res[$j]['report_data']);
	
			for($k=0;$k<$rdc;$k++){
	
				$tm=explode("|",$res[$j]['report_data'][$k]);
	
				$res[$j]['report_data'][$k]=array("report_id"=>$tm[0],"isPublished"=>$tm[1],"publishDate"=>$tm[2]);
	
			}
	
				
	
			$roles=$res[$j]['roles']!=""?explode(',',$res[$j]['roles']):array();
	
			$statuses=$res[$j]['statuses']!=""?explode(',',$res[$j]['statuses']):array();
	
			$percCompletes=$res[$j]['percCompletes']!=""?explode(',',$res[$j]['percCompletes']):array();
	
			$ratingInputDates=$res[$j]['ratingInputDates']!=""?explode(',',$res[$j]['ratingInputDates']):array();
	
				
	
			$user_ids=$res[$j]['user_ids']!=""?explode(',',$res[$j]['user_ids']):array();
	
			$user_names=$res[$j]['user_names']!=""?explode(',',$res[$j]['user_names']):array();
	
				
	
			$ln=count($roles);
	
			for($i=0;$i<$ln;$i++)
	
				$res[$j]['data_by_role'][$roles[$i]]=array("status"=>$statuses[$i],"percComplete"=>$percCompletes[$i],"ratingInputDate"=>empty($ratingInputDates[$i])?'':$ratingInputDates[$i],"user_id"=>$user_ids[$i],"user_name"=>$user_names[$i]);
	
		}
	
		return $res;
	
	}

	

	function getExternalAssessorsInGroupAssessment($group_assessment_id,$return_group_array=1,$only_status_ids=array()){

		$sql="SELECT u.user_id,u.name,u.email,u.aqs_status_id as original_status_id,if(group_concat(ur.role_id) rlike concat('[[:<:]]',4,'[[:>:]]'),1, u.aqs_status_id) as status_id,ea.added_by_admin

			FROM `h_group_assessment_external_assessor` ea

			inner join d_user u on u.user_id=ea.user_id

			inner join h_user_user_role ur on ur.user_id=u.user_id

			where ea.group_assessment_id=?

			group by u.user_id";

		if(count($only_status_ids)){

			$sql.=" having status_id in (".implode(",",$only_status_ids).")";

		}

		$res=$this->db->get_results($sql,array($group_assessment_id));

		if($res)

			return $return_group_array>0?$this->db->array_grouping($res,"status_id"):$res;

		else

			return array();

	}

	

	function addTeacherAttributeValue($user_id,$value,$attr_id_or_name){

		if(is_numeric($attr_id_or_name) && $attr_id_or_name>0){

			return $this->db->insert('h_user_teacher_attr',array('user_id'=>$user_id,"value"=>$value,"attr_id"=>$attr_id_or_name));

		}else{

			return $this->db->query('insert into `h_user_teacher_attr` (user_id,value,attr_id) values(?,?,(select attr_id from d_teacher_attribute where attr_name=?))',array($user_id,$value,$attr_id_or_name));

		}

	}

	

	function updateTeacherAttributeValue($user_id,$value,$attr_id_or_name){
                
                if($attr_id_or_name=="doj" && (empty($attr_id_or_name) || $attr_id_or_name=="")){
                      return $this->db->query('delete from `h_user_teacher_attr` where user_id=? and attr_id =(select attr_id from d_teacher_attribute where attr_name=?)',array($user_id,$attr_id_or_name));         
                }                
                else if(is_numeric($attr_id_or_name) && $attr_id_or_name>0){
                        $res=$this->db->get_results("select * from h_user_teacher_attr where user_id=? && attr_id=?",array($user_id,$attr_id_or_name));                
		        $res?$res:array();
                        $tot=count($res);
                        
                        if($tot>0){
			return $this->db->update('h_user_teacher_attr',array("value"=>$value),array('user_id'=>$user_id,"attr_id"=>$attr_id_or_name));
                        }else{
                        return $this->db->insert('h_user_teacher_attr',array('user_id'=>$user_id,"value"=>$value,"attr_id"=>$attr_id_or_name));
                        }        
		}else{
                        
                        $res=$this->db->get_results("select * from h_user_teacher_attr where user_id=? && attr_id=(select attr_id from d_teacher_attribute where attr_name=?)",array($user_id,$attr_id_or_name));                
		        $res?$res:array();
                        //print_r($res);
                        $tot=count($res);
                        //die();
                        if($tot>0){
			return $this->db->query('update `h_user_teacher_attr` set value=? where user_id=? and attr_id =(select attr_id from d_teacher_attribute where attr_name=?)',array($value,$user_id,$attr_id_or_name));
                        }else{
                         return $this->db->query('insert into `h_user_teacher_attr` (user_id,value,attr_id) values(?,?,(select attr_id from d_teacher_attribute where attr_name=?))',array($user_id,$value,$attr_id_or_name));   
                        }        
		}

	}

	

	function addTeacherInfoData($teacher_id,$attr_id,$assessment_id,$value){

		return $this->db->insert('d_teacher_data',array('teacher_id'=>$teacher_id,"value"=>$value,"attr_id"=>$attr_id,"assessment_id"=>$assessment_id));

	}

	

	function updateTeacherInfoData($teacher_data_id,$value){

		return $this->db->update('d_teacher_data',array("value"=>$value),array('teacher_data_id'=>$teacher_data_id));

	}

	

	function deleteTeacherInfoData($teacher_data_id){

		return $this->db->delete('d_teacher_data',array('teacher_data_id'=>$teacher_data_id));

	}

	

	function assignTeacherAssessment($group_ass_id){

		$create_date = date('Y-m-d h:i:s');

		return $this->db->get_row("call assign_teacher_assessment(?,?);",array($group_ass_id,$create_date));

	}

	

	function getTeacherCategoryList(){

		$res=$this->db->get_results("select * from d_teacher_category where teacher_category!='Student' order by teacher_category");

		return $res?$res:array();

	}
        
        function getStudentReviewType(){
            $res=$this->db->get_results("select * from  d_student_review_type");
            return $res?$res:array();
        }
        
        /*function getStudentBatchType(){
            $res=$this->db->get_results("select * from  d_student_review_batch");
            return $res?$res:array();
        }*/
        
        function getStudentCategoryList(){

		$res=$this->db->get_results("select * from d_teacher_category where teacher_category='Student' order by teacher_category");

		return $res?$res:array();

	}

	

	function getTeacherCategoryListForTchrAsmnt($teacher_assessment_id){

		$res=$this->db->get_results("select tc.* 

			from d_teacher_category tc

			inner join h_group_assessment_diagnostic ad on tc.teacher_category_id=ad.teacher_category_id and ad.group_assessment_id=?

			group by tc.teacher_category_id

			order by teacher_category",array($teacher_assessment_id));

		return $res?$res:array();

	}

	

	function getTeachersInTeacherAssessment($teacher_assessment_id){

		$res=$this->db->get_results("SELECT at.*,t.name,t.email,ua.value as doj

			FROM `h_group_assessment_teacher` at

			inner join d_user t on at.teacher_id=t.user_id

			left join h_user_teacher_attr ua on t.user_id=ua.user_id

			left join d_teacher_attribute ta on ta.attr_id=ua.attr_id and ta.attr_name='doj'

			where at.group_assessment_id=?

			group by at.teacher_id",array($teacher_assessment_id));

		return $res?$res:array();

	}

	function getStudentInfo($assessment_id){//($teacher_id,$assessment_id){

		/*$res=$this->db->get_results("SELECT ta.*, td.value, td.teacher_data_id

			FROM `d_teacher_attribute` ta

			left join d_teacher_data td on ta.attr_id=td.attr_id and td.teacher_id=? and td.assessment_id=?;",array($teacher_id,$assessment_id));*/

		$res=$this->db->get_results("SELECT sa.*, sd.value, sd.student_data_id, au.user_id,u.name as uname

			FROM d_assessment a

            inner join h_assessment_user au on a.assessment_id=au.assessment_id and au.role=3
            inner join d_user u on au.user_id=u.user_id
            inner join `d_student_review_form_attributes` sa            

			left join d_student_data sd on sa.field_id=sd.attr_id and sd.student_id=au.user_id and sd.assessment_id=a.assessment_id

            where a.assessment_id=?;",array($assessment_id));

		if($res){

			$tmp=array();

			$OR=array();

			$SV=array();

			foreach($res as $r){

				if($r['field_name']=='other_roles' && $r['student_data_id']>0){

					$OR[$r['student_data_id']]=$r['value'];

				}else if($r['field_name']=='supervisors' && $r['student_data_id']>0){

					$SV[$r['student_data_id']]=$r['value'];

				}
                                if($r['field_name']=="name"){
                                $r['value']=empty($r['value'])?$r['uname']:$r['value'];
                                //print_r($r);
                                }
                                //print_r($r);
				$tmp[$r['field_name']]=$r;
                                

			}
                        //echo print_r($tmp);        
			$tmp['other_roles']['value']=$OR;

			$tmp['supervisors']['value']=$SV;
                        

			return $tmp;

		}

		return array();

	}

	function getTeacherInfo($assessment_id){//($teacher_id,$assessment_id){

		/*$res=$this->db->get_results("SELECT ta.*, td.value, td.teacher_data_id

			FROM `d_teacher_attribute` ta

			left join d_teacher_data td on ta.attr_id=td.attr_id and td.teacher_id=? and td.assessment_id=?;",array($teacher_id,$assessment_id));*/

		$res=$this->db->get_results("SELECT ta.*, td.value, td.teacher_data_id, au.user_id

			FROM d_assessment a

            inner join h_assessment_user au on a.assessment_id=au.assessment_id and au.role=3

            inner join `d_teacher_attribute` ta            

			left join d_teacher_data td on ta.attr_id=td.attr_id and td.teacher_id=au.user_id and td.assessment_id=a.assessment_id

            where a.assessment_id=?;",array($assessment_id));

		if($res){

			$tmp=array();

			$OR=array();

			$SV=array();

			foreach($res as $r){

				if($r['attr_name']=='other_roles' && $r['teacher_data_id']>0){

					$OR[$r['teacher_data_id']]=$r['value'];

				}else if($r['attr_name']=='supervisors' && $r['teacher_data_id']>0){

					$SV[$r['teacher_data_id']]=$r['value'];

				}

				$tmp[$r['attr_name']]=$r;

			}

			$tmp['other_roles']['value']=$OR;

			$tmp['supervisors']['value']=$SV;

			return $tmp;

		}

		return array();

	}

	

	static function getTeacherAssessorHTMLRow($sno,$firstName,$lastName,$email,$doj,$addDelete=1){

		if($sno==0 && $firstName=='' && $lastName=='' && $email=='' && $doj=='')

			return '';

		else

			return '

		<tr class="team_row">

			<td class="s_no">'.$sno.'</td> 

			<td><input type="text" data-type="onlyAlpha" value="'.$firstName.'" name="teacherAssessors[first_name][]" autocomplete="off" class="tableTxtFld firstname"></td>

			<td><input type="text" data-type="onlyAlpha" value="'.$lastName.'" name="teacherAssessors[last_name][]" autocomplete="off" class="tableTxtFld lastname"></td>

			<td><input type="text" data-type="email" value="'.$email.'" name="teacherAssessors[email][]" autocomplete="off" class="tableTxtFld email"></td>

			<td>

				<div class="input-group date-Picker">

					<input type="text" data-type="date" value="'.$doj.'" placeholder="DD-MM-YYYY" name="teacherAssessors[doj][]" autocomplete="off" class="tableTxtFld doj">

					<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>

				</div>

			</td>

			<td>'.($addDelete?'<a class="delete_row" href="javascript:void(0)"><i class="fa fa-times"></i></a>':'').'</td>

		</tr>';

	}
        
        
	static function getExternalImpactTeamHTMLRow($sn,$id_c,$dropval="",$textval="",$alreadyexists_id=0,$disabled=0){
            
            $aqsDataModel = new aqsDataModel();
            $designations=$aqsDataModel->getDesignations();
            $disabled_text=$disabled?'disabled="disabled"':'';
            $row = '<tr class="teamrow">';
            $row.='<td><input type="hidden" name="assessor_action1_impact_id['.$id_c.'][]" value="'.$alreadyexists_id.'"><select class="selectpicker sholder" name="stackholder['.$id_c.'][]" data-width="200px" ><option value="">--Stakeholder--</option>';
            foreach($designations as $key_desig=>$val_desig ){
                $row.='<option value="'.$val_desig['designation_id'].'" ';
                if($dropval==$val_desig['designation_id']) $row.=' selected="selected" ';
                $row.='>'.$val_desig['designation'].'</option>';
            }
                        $row.='</select>
            
            </td>
                        <td style="width:75%;"><textarea class="form-control iholder" name="stackholderimpact['.$id_c.'][]" style="resize: both;" placeholder="Enter Impact Statement" >'.$textval.'</textarea></td>';
            if($sn!=1 && $disabled==0){           
            $row.='<td style="width:4%"><a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a></td>';
            }else{
            $row.='<td style="width:4%"></td>';    
            }
            $row .= '</tr>';
            
            return $row;
        }
        
        static function getActionTeamHTMLRow($sn,$val=array()){
            $aqsDataModel = new aqsDataModel();
            $designations=$aqsDataModel->getDesignations();
            $row='<tr class="teamrow2">';
            $row.='<td class="s_no">'.$sn.'</td>';
             $row.='<td><input type="hidden" name="team_designation1[]" value=""><select class="selectpicker dholder" name="team_designation[]"><option value="">--Designation--</option>';
            foreach($designations as $key_desig=>$val_desig ){
                $row.='<option value="'.$val_desig['designation_id'].'" ';
                if(isset($val['team_designation']) && $val['team_designation']==$val_desig['designation_id']) $row.=' selected="selected" ';
                $row.='>'.$val_desig['designation'].'</option>';
            }
                        $row.='</select>
            
            </td>';
                                                            
                                                            
                                                     $row.='<td>
                                                        <input type="text" name="team_member_name[]" class="form-control tholder" value="'.(isset($val['team_member_name'])?$val['team_member_name']:'').'">
                                                    </td>';
            if($sn!=1){           
            $row.='<td><a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a></td>';
            }else{
            $row.='<td></td>';  
            
            }
            
            $row.='</tr>';
            
            return $row;
        }
        
        static function getActionImpactStmntHTMLRow($sn,$val=array(),$impactStmntId,$designations=array(),$classes=array(),$statementData=array(),$methods=array()){
          
              
            $row='<tr class="teamrowac2">';
            $row.='<td class="s_no" style="vertical-align:top;">'.$sn.'</td>';
             $row.='<td style="vertical-align:top;"><div class="datePicker impact_date"><input type="text"  class="form-control date" placeholder="dd-mm-yyyy" name="impactStmnt['.$impactStmntId.']['.$sn.']['.'date'.']"></div></td>';
                            
                        $row.='<td style="vertical-align:top;">';
                        
                        $row.='<select  class="selectpicker methodType impact_activity_method" id="'.$sn.'-'.$impactStmntId.'" name="impactStmnt['.$impactStmntId.']['.$sn.']['.'activity_method'.']">
                                <option value="">--Activity Method--</option>';
                       foreach($methods as $key=>$val){
                           
                        $row .= "<option value=".$val['id'].">".$val['method']."</option>";
                       }
                        $row.='</select></td>';
                           
                        $row.='<td style="vertical-align:top;">';
                         
                        
                        $row.='<div class="inlContBox fullW"><div id="actopt-'.$sn.'-'.$impactStmntId.'" class="inlCBItm cmntsDD" style=" display: none;">'
                                . '<select class="selectpicker impact_activity_option" name="impactStmnt['.$impactStmntId.']['.$sn.']['.'activity_option'.']"><option value="">--Class--</option>';
                                foreach($classes as $key_class=>$val_class ){
                                $row.='<option value="'.$val_class['class_id'].'" ';
                                    if(isset($stmntData['class_id']) && $stmntData['class_id']==$val_class['class_id']) $row.=' selected="selected" ';
                                         $row.='>'.$val_class['class_name'].'</option>';
                              }
                            $row.='</select></div>';
                        $row.='<div id="stake-'.$sn.'-'.$impactStmntId.'" class="inlCBItm cmntsDD " style=" display: none;">'
                                 . '<select class="selectpicker impact_stakeholder" name="impactStmnt['.$impactStmntId.']['.$sn.']['.'stakeholder'.']"><option value="">--Stakeholder--</option>';
                            foreach($designations as $key_desig=>$val_desig ){
                                $row.='<option value="'.$val_desig['designation_id'].'" ';
                                    if(isset($val['activity_stackholder']) && $val['activity_stackholder']==$val_desig['designation_id']) $row.=' selected="selected" ';
                                         $row.='>'.$val_desig['designation'].'</option>';
                              }
                            $row.='</select></div>';
                            $row .= '<textarea  class="form-control ad impact_comments areasize" id="cmnt-'.$sn.'-'.$impactStmntId.'" name="impactStmnt['.$impactStmntId.']['.$sn.']['.'comments'.']">'.(isset($val['activity_details'])?$val['activity_details']:'').'</textarea>
                            </div></td>
                            <td style="vertical-align:top;">
                            <dd class="judgementS" style="background-color: transparent;">
                                <div class="upldHldr">
                                    <div class="fileUpload btn btn-primary mr0 vtip" title="Only jpeg, png, gif, jpg, avi, mp4, mov, doc, docx, txt, xls, xlsx, pdf, cvs, xml, pptx, ppt, cdr, mp3, wav type of files are allowed">
                                        <i class="glyphicon glyphicon-folder-open"></i> <span>Attach File</span>  
                                        <input type="file" autocomplete="off" id="'.$impactStmntId.'-'.$sn.'" title="" class="upload uploadImpactStmntBtn">
                                    </div>                                    
                                    <div class="filesWrapper" style="margin-top: 10px;">                                               

                                    </div>
                                </div>                                        

                            </dd>
                            </td>';
                        
                                                     
           // if($sn!=1){           
            $row.='<td><a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a></td>';
            //}else{
           // $row.='<td></td>';  
            
           // }
            
            $row.='</tr>';
            
            return $row;
        }
        static function getActionImpactStmntDataRow($sn,$val=array(),$impactStmntId,$designations,$classes,$statementData=array(),$methods=array()){
           
            //$activities=$aqsDataModel->getActivity();
            //echo "<pre>";print_r($statementData);die;
            $row = '';
            $sn = 1;
            foreach($statementData[$impactStmntId] as $key => $stmntData){
             
            $comments = '';
            if(isset($stmntData['activity_method_id']) && $stmntData['activity_method_id'] == 2) {
               $comments =   isset($stmntData['comments'])?$stmntData['comments']:'';
            } else if(isset($stmntData['activity_method_id']) && $stmntData['activity_method_id'] == 4) {
               $comments =   isset($stmntData['stk_comments'])?$stmntData['stk_comments']:'';
            }else{
                $comments =   isset($stmntData['im_comments'])?$stmntData['im_comments']:'';
            }
            $files ='';
            if(isset($stmntData['files'])){
                $files = $stmntData['files'];
            }
            $files = diagnosticModel::decodeFileArray($files);  
            $name = " impactStmnt[files][".$impactStmntId.']['.$sn.'][]';
           // print_r($files);
            $row.='<tr class="teamrowac2">';
            $row.='<td class="s_no" style="vertical-align:top;">'.$sn.'</td>';
             $row.='<td style="vertical-align:top;"><div class="datePicker impact_date"><input type="text"  class="form-control date" placeholder="dd-mm-yyyy" value="'.date("d-m-Y",strtotime($stmntData['date'])).'" name="impactStmnt['.$impactStmntId.']['.$sn.']['.'date'.']"></div></td>';
                            
                        $row.='<td style="vertical-align:top;">';
                        
                        $row.='<select  class="selectpicker methodType impact_activity_method" id="'.$sn.'-'.$impactStmntId.'" name="impactStmnt['.$impactStmntId.']['.$sn.']['.'activity_method'.']">
                                <option  value="">--Activity Method--</option>';
                        foreach($methods as $key=>$val){
                               $row.=' <option value="'.$val['id'].'" '. (isset($stmntData['activity_method_id'])&& $stmntData['activity_method_id']==$val['id']?"selected='selected'":''). '>'.$val['method'].'</option>';
                        }
                        $row.='</select>';
                       
                        $row.='</td>';
                           
                        $row.='<td style="vertical-align:top;">';
                         
                        
                        $row.='<div class="inlContBox fullW"><div id="actopt-'.$sn.'-'.$impactStmntId.'" class="inlCBItm cmntsDD" style=" '. (isset($stmntData['activity_method_id'])&& $stmntData['activity_method_id']==2?"":'display: none').'">'
                                . '<select class="selectpicker impact_stakeholder" name="impactStmnt['.$impactStmntId.']['.$sn.']['.'activity_option'.']"> <option  value="">--Class--</option>';
                            foreach($classes as $key_class=>$val_class ){
                                $row.='<option value="'.$val_class['class_id'].'" ';
                                    if(isset($stmntData['class_id']) && $stmntData['class_id']==$val_class['class_id']) $row.=' selected="selected" ';
                                         $row.='>'.$val_class['class_name'].'</option>';
                              }
                            $row.='</select></div>';
                        $row.='<div id="stake-'.$sn.'-'.$impactStmntId.'" class="inlCBItm cmntsDD" style=" '. (isset($stmntData['activity_method_id'])&& $stmntData['activity_method_id']==4?"":'display: none').'">'
                                 . '<select class="selectpicker impact_activity_option" name="impactStmnt['.$impactStmntId.']['.$sn.']['.'stakeholder'.']"> <option  value="">--Stakeholder--</option>';
                            foreach($designations as $key_desig=>$val_desig ){
                                $row.='<option value="'.$val_desig['designation_id'].'" ';
                                    if(isset($stmntData['designation_id']) && $stmntData['designation_id']==$val_desig['designation_id']) $row.=' selected="selected" ';
                                         $row.='>'.$val_desig['designation'].'</option>';
                              }
                            $row.='</select></div>';
                            $row .= '<textarea class="form-control ad impact_comments areasize" id="cmnt-'.$sn.'-'.$impactStmntId.'" name="impactStmnt['.$impactStmntId.']['.$sn.']['.'comments'.']">'.$comments.'</textarea>
                            </div></td>
                            <td style="vertical-align:top;">
                            <dd class="judgementS" style="background-color: transparent;">
                                <div class="upldHldr">
                                    <div class="fileUpload btn btn-primary mr0 vtip" title="Only jpeg, png, gif, jpg, avi, mp4, mov, doc, docx, txt, xls, xlsx, pdf, cvs, xml, pptx, ppt, cdr, mp3, wav type of files are allowed">
                                        <i class="glyphicon glyphicon-folder-open"></i> <span>Attach File</span>  
                                        <input type="file" autocomplete="off" id="'.$impactStmntId.'-'.$sn.'" title="" class="upload uploadImpactStmntBtn">
                                    </div>
                                    <div class="filesWrapper" style="margin-top: 10px;">  ';                                             
                                    
                                    
                                    foreach ($files as $file_id => $file_name) {
                                        $row .= '<div class="filePrev uploaded vtip ext-' . diagnosticModel::getFileExt($file_name) . '" id="file-' . $file_id . '" title="' . $file_name . '">' . '<span class="delete fa"></span><div class="inner"><a href="' . UPLOAD_URL . '' . $file_name . '" target="_blank"> </a></div><input type="hidden" name="' . $name . '" value="' . $file_id . '"></div>';
                                    }
                                                                        
                                    $row .= "</div></div> </dd>
                            </td>";
                                                                        

                        
                                                     
            //if($sn!=1){           
            $row.='<td><a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a></td>';
           // }else{
            //$row.='<td></td>';  
            
            //}
            
            $row.='</tr>';
            $sn++;
            }
            return $row;
        }
        
        static function getActionActivityHTMLRow($sn,$val=array()){
            $aqsDataModel = new aqsDataModel();
            $designations=$aqsDataModel->getDesignations();
            $activities=$aqsDataModel->getActivity();
            
            //print_r($val);
            
            $row='<tr class="teamrowac2"  data-id="'.(isset($val['h_review_action2_activity_id'])?$val['h_review_action2_activity_id']:'').'">';
            //$row.='<td class="s_no">'.$sn.'</td>';
            if(isset($val['postponed_ids']) && explode(",",$val['postponed_ids'])>0){
                
            $row.='<td class="s_no" style="vertical-align:top;"><span class="collapseGA vtip fa fa-plus-circle" title="View Postponed"></span></td>';
            }else{
            $row.='<td class="s_no" style="vertical-align:top;"></td>';   
            }
            
             $row.='<td class="tdcaret" style="vertical-align:top;"><input type="hidden" name="activity_old_id[]" value="'.(isset($val['h_review_action2_activity_id'])?$val['h_review_action2_activity_id']:'').'"><input type="hidden" name="activity_stackholder_check['.$sn.'][]" value=""><select class="form-control aholder" name="activity_stackholder['.$sn.'][]" multiple="multiple">';
             //$row.='<option value="">--Stackholder--</option>';                   
             foreach($designations as $key_desig=>$val_desig ){
                $row.='<option value="'.$val_desig['designation_id'].'" ';
                
                if(isset($val['activity_stackholder_ids']) && in_array($val_desig['designation_id'],explode(",",$val['activity_stackholder_ids']))) $row.=' selected="selected" ';
                
                $row.='>'.$val_desig['designation'].'</option>';
            }
                        $row.='</select>
            
            </td>';
                            
                        $row.='<td  style="vertical-align:top;">';
                        
                        $row.='<select name="activity[]" class="selectpicker act">';
                        $row.='<option value="">--Activity--</option>';
                        foreach($activities as $key_a=>$val_a ){
                            
                        $row.='<option value="'.$val_a['activity_id'].'" ';
                        if(isset($val['activity']) && $val['activity']==$val_a['activity_id']) $row.=' selected="selected" ';
                        $row.='>'.$val_a['activity'].'</option>';    
                        
                        
                        }
                        $row.='</select>';
                        $row.='</td>';
                           
                        $row.='<td style="vertical-align:top;"> 
                                                        <textarea  class="form-control ad areasize" name="activity_details[]">'.(isset($val['activity_details'])?$val['activity_details']:'').'</textarea>

                                                    </td>
                                                    <td style="vertical-align:top;">
                                                        <select class="selectpicker astatus" name="activity_status[]">
                                                            <option value="">--Status--</option>
                                                            <option value="0" '.((isset($val['activity_status']) && $val['activity_status']=="0")?"Selected='Selected'":"").'>Not Started</option>
                                                            <option value="1" '.((isset($val['activity_status']) && $val['activity_status']=="1")?"Selected='Selected'":"").'>Started</option>
                                                            <option value="2"  '.((isset($val['activity_status']) && $val['activity_status']=="2")?"Selected='Selected'":"").'>Completed</option>
                                                            <option value="3"  '.((isset($val['activity_status']) && $val['activity_status']=="3")?"Selected='Selected'":"").'>Postponed</option>
                                                        </select>
                                                    </td>
                                                    <td style="vertical-align:top;">
                                                        <div class="datePicker adate"><input type="text" class="form-control date  adateda" placeholder="dd-mm-yyyy"  name="activity_date[]" value="'.((isset($val['activity_date']) && $val['activity_date']!="0000-00-00")?date("d-m-Y",strtotime($val['activity_date'])):"").'">
                                                          
                                                      </div> 
                                                      <div class="date-show"  style="display:none;"><span>Earlier Date :</span> <span class="arealdate"></span></div> 
                                                    </td>
                                                    
                                                    <td style="vertical-align:top;">
                                                        <div class="datePicker fdate"><input type="text" class="form-control date " placeholder="dd-mm-yyyy" name="activity_actual_date[]" value="'.((isset($val['activity_actual_date']) && $val['activity_actual_date']!="0000-00-00")?date("d-m-Y",strtotime($val['activity_actual_date'])):"").'"></div>                                                
                                                    </td>
                                                    
                                                    <td style="vertical-align:top;">
                                                       <textarea  class="form-control acomments areasize" name="activity_comments[]">'.(isset($val['activity_comments'])?$val['activity_comments']:'').'</textarea>
                                                       
                                                       <a href="Javascript:void(0);" class="activity_comments-show vtip" title="" style="display:none;">view comment</a>   
                                                    </td>';
                        
            //if($sn!=1){           
            $row.='<td><a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a></td>';
            //}else{
            //$row.='<td></td>';  

            
            //}
            
            $row.='</tr>';
            
            return $row;
        }
        
        
         static function getActionActivityViewRow($val=array()){
            
            
            $row='<tr class="teamrowac2 ga-rows-'.$val['h_review_action2_activity_id'].'">';
            
            $row.='<td class="s_no"></td>';   
            
            
            $row.='<td>'.$val['designation'].'</td>';
                            
            $row.='<td>';
            $row.=$val['activity'];
            $row.='</td>';
            $row.='<td>'.nl2br($val['activity_details']).'</td>';
            $row.='<td>Postponed</td>';
            
            if($val['activity_date']!="0000-00-00" && !empty($val['activity_date'])){
               $row.='<td>'.date("d-m-Y",strtotime($val['activity_date'])).'</td>';
            }else{
                $row.='<td></td>'; 
            }
            
            if($val['activity_actual_date']!="0000-00-00" && !empty($val['activity_actual_date'])){
            $row.='<td>'.date("d-m-Y",strtotime($val['activity_actual_date'])).'</td>';
            }else{
            $row.='<td></td>';    
            }
            
            $row.='<td>'.nl2br($val['activity_comments']).'</td>';
                        
                                                     
            
            $row.='<td></td>';  
            $row.='</tr>';
            
            return $row;
        }

	static function getExternalReviewTeamHTMLRow($sn,$ty='all'){

		$clientModel=new clientModel();
                
               if($ty=="college"){                 
		$clients = $clientModel->getClients(array("client_institution_id"=>2,"max_rows"=>-1,'school_ids'=>array("'Adhyayan'","'Independent Consultant'")));
               }else if($ty=="school"){                 
		$clients = $clientModel->getClients(array("client_institution_id"=>1,"max_rows"=>-1));
               }else{
                $clients = $clientModel->getClients(array("max_rows"=>-1));   
               }

		$assessmentModel = new assessmentModel();

		$externalReviewRoles = $assessmentModel->getReviewerSubRoles(4);

		$row = '<tr class="team_row">

					<td class="s_no">'.$sn.'</td>';

		$row .= '<td><select class="form-control team_external_client_id" id="team_external_client_id'.$sn.'" required name="externalReviewTeam[clientId][]">';
                                if($ty=="college"){
                                 $row.='<option value=""> - Select College - </option>';    
                                }else{
				$row.='<option value=""> - Select School - </option>';
                                }

		foreach($clients as $client)

			$row .= "<option value=\"".$client['client_id']."\">".$client['client_name'].($client['city']!=""?", ".$client['city']:'')."</option>\n";

		

		$row .= '</select></td>';

		

		$row .=	'<td><select class="form-control " name="externalReviewTeam[role][]" required>

						<option value=""> - Select Role - </option>						

						';	

		foreach($externalReviewRoles as $externalReviewer)													

			$row .= $externalReviewer['sub_role_id']=='1'?'':"<option value=\"".$externalReviewer['sub_role_id']."\">".$externalReviewer['sub_role_name']."</option>";													

																

						

		$row .= '</select></td>

					<td><select class="form-control team_external_assessor_id" name="externalReviewTeam[member][]" id="team_external_assessor_id'.$sn.'" required>

						<option value=""> - Select Member - </option>

						</select>

					</td>

					<td><a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a></td>

				</tr>';			

			return $row;

	}
        
        
        static function getFacilitatorReviewTeamHTMLRow($sn,$ty='school'){

		$clientModel=new clientModel();
                
               if($ty=="college"){                 
		$clients = $clientModel->getClients(array("client_institution_id"=>2,"max_rows"=>-1,'school_ids'=>array("'Adhyayan'","'Independent Consultant'")));
               }else if($ty=="school"){                 
		$clients = $clientModel->getClients(array("client_institution_id"=>1,"max_rows"=>-1));
               }else{
		$clients = $clientModel->getClients(array("max_rows"=>-1));
               }

		$assessmentModel = new assessmentModel();

		$externalReviewRoles = $assessmentModel->getReviewerSubRoles(4);

		$row = '<tr class="facilitator_row">

					<td class="s_no">'.$sn.'</td>';

		$row .= '<td><select class="form-control team_facilitator_client_id" id="team_facilitator_client_id'.$sn.'" required name="facilitatorReviewTeam[clientId][]">';

				if($ty=="college"){
                                 $row.='<option value=""> - Select College - </option>';    
                                }else{
				$row.='<option value=""> - Select School - </option>';
                                }

		foreach($clients as $client)

			$row .= "<option value=\"".$client['client_id']."\">".$client['client_name'].($client['city']!=""?", ".$client['city']:'')."</option>\n";

		

		$row .= '</select></td>';

		

		$row .=	'<td><select class="form-control " name="facilitatorReviewTeam[role][]" required>

						<option value=""> - Select Role - </option>						

						';	

		foreach($externalReviewRoles as $externalReviewer)													

			$row .= $externalReviewer['sub_role_id']=='1' || $externalReviewer['sub_role_id']=='2'?'':"<option value=\"".$externalReviewer['sub_role_id']."\">".$externalReviewer['sub_role_name']."</option>";													

																

						

		$row .= '</select></td>

					<td><select class="form-control team_external_facilitator_id" name="facilitatorReviewTeam[member][]" id="team_external_facilitator_id'.$sn.'" required>

						<option value=""> - Select Member - </option>

						</select>

					</td>

					<td><a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a></td>

				</tr>';			

			return $row;

	}
        
        function getReviewerListforSchool($tchr_ass_id){
            
        return $this->getExternalAssessorsInGroupAssessment($tchr_ass_id,0,array(1,2));    
        }
        
        function getDepartmentList(){
            $sql = "select * from d_school_level";
            $res=$this->db->get_results($sql);
            return $res?$res:array();
            
        }
        
        
        function getAllUsedDiagnostics($gaid,$type=0){
        if($type==1){
         $sql="select group_concat(allT.diagnostic_id) as all_diagnostic,group_concat(allT.assessor_id) as all_validators  from (SELECT ga.group_assessment_id,ga.client_id,ga.creation_date,at.teacher_id,at.teacher_category_id,at.assessor_id,ad.diagnostic_id,dtd.teacher_data_id,hau.percComplete as internalreview,hauex.percComplete as exreview
FROM `d_group_assessment` ga
inner join h_group_assessment_teacher at on ga.group_assessment_id=at.group_assessment_id
inner join h_group_assessment_diagnostic ad on at.group_assessment_id=ad.group_assessment_id and at.teacher_category_id=ad.teacher_category_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=3) hau on hau.user_id=at.teacher_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=4) hauex on hauex.user_id=at.assessor_id and hauex.assessment_id=hau.assessment_id  left join d_teacher_data dtd on dtd.teacher_id=hau.user_id && dtd.assessment_id=hau.assessment_id  where ga.group_assessment_id=? group by at.teacher_id having hau.percComplete >0) allT";   
        }else if($type==2){
          $sql="select group_concat(allT.diagnostic_id) as all_diagnostic,group_concat(allT.assessor_id) as all_validators  from (SELECT ga.group_assessment_id,ga.client_id,ga.creation_date,at.teacher_id,at.teacher_category_id,at.assessor_id,ad.diagnostic_id,dtd.teacher_data_id,hau.percComplete as internalreview,hauex.percComplete as exreview
FROM `d_group_assessment` ga
inner join h_group_assessment_teacher at on ga.group_assessment_id=at.group_assessment_id
inner join h_group_assessment_diagnostic ad on at.group_assessment_id=ad.group_assessment_id and at.teacher_category_id=ad.teacher_category_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=3) hau on hau.user_id=at.teacher_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=4) hauex on hauex.user_id=at.assessor_id and hauex.assessment_id=hau.assessment_id  left join d_teacher_data dtd on dtd.teacher_id=hau.user_id && dtd.assessment_id=hau.assessment_id  where ga.group_assessment_id=? group by at.teacher_id having hauex.percComplete>0) allT";  
        }else{    
        $sql="select group_concat(allT.diagnostic_id) as all_diagnostic,group_concat(allT.assessor_id) as all_validators  from (SELECT ga.group_assessment_id,ga.client_id,ga.creation_date,at.teacher_id,at.teacher_category_id,at.assessor_id,ad.diagnostic_id,dtd.teacher_data_id,hau.percComplete as internalreview,hauex.percComplete as exreview
FROM `d_group_assessment` ga
inner join h_group_assessment_teacher at on ga.group_assessment_id=at.group_assessment_id
inner join h_group_assessment_diagnostic ad on at.group_assessment_id=ad.group_assessment_id and at.teacher_category_id=ad.teacher_category_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=3) hau on hau.user_id=at.teacher_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=4) hauex on hauex.user_id=at.assessor_id and hauex.assessment_id=hau.assessment_id  left join d_teacher_data dtd on dtd.teacher_id=hau.user_id && dtd.assessment_id=hau.assessment_id  where ga.group_assessment_id=? group by at.teacher_id having hau.percComplete >0 || hauex.percComplete>0) allT";
        }
        $res=$this->db->get_row($sql,array($gaid,$gaid,$gaid));
        //return $res?explode(",",$res['all_diagnostic']):array();
        return $res?$res:array();
        }
        
        
        function getAllUsedDiagnosticsCategory($gaid){
        $sql="select group_concat(allT.teacher_cat_id) as all_diagnostic_cat  from (SELECT ga.group_assessment_id,ga.client_id,ga.creation_date,at.teacher_id,at.teacher_category_id,at.assessor_id,ad.diagnostic_id,dtd.teacher_data_id,hau.percComplete as internalreview,hauex.percComplete as exreview,hdtc.teacher_cat_id
FROM `d_group_assessment` ga
inner join h_group_assessment_teacher at on ga.group_assessment_id=at.group_assessment_id
inner join h_group_assessment_diagnostic ad on at.group_assessment_id=ad.group_assessment_id and at.teacher_category_id=ad.teacher_category_id inner join h_diagnostic_teacher_cat hdtc on ad.diagnostic_id=hdtc.diagnostic_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=3) hau on hau.user_id=at.teacher_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=4) hauex on hauex.user_id=at.assessor_id  and hauex.assessment_id=hau.assessment_id left join d_teacher_data dtd on dtd.teacher_id=hau.user_id && dtd.assessment_id=hau.assessment_id  where ga.group_assessment_id=? group by at.teacher_id) allT";
        $res=$this->db->get_row($sql,array($gaid,$gaid,$gaid));
        return $res?explode(",",$res['all_diagnostic_cat']):array();
        }
        
        
        function getTeacherStatus($gaid,$tid){
       $sql="SELECT ga.group_assessment_id,ga.client_id,ga.creation_date,at.teacher_id,at.teacher_category_id,at.assessor_id,ad.diagnostic_id,dtd.teacher_data_id,hau.percComplete as internalreview,hauex.percComplete as exreview
FROM `d_group_assessment` ga
inner join h_group_assessment_teacher at on ga.group_assessment_id=at.group_assessment_id
inner join h_group_assessment_diagnostic ad on at.group_assessment_id=ad.group_assessment_id and at.teacher_category_id=ad.teacher_category_id  left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=3) hau on hau.user_id=at.teacher_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=4) hauex on hauex.user_id=at.assessor_id and hauex.assessment_id=hau.assessment_id left join d_teacher_data dtd on dtd.teacher_id=hau.user_id && dtd.assessment_id=hau.assessment_id  where ga.group_assessment_id=? && at.teacher_id=?  group by at.teacher_id";
        $res=$this->db->get_row($sql,array($gaid,$gaid,$gaid,$tid));
        return $res?$res:array();
        }
        
        function getStudentStatus($gaid,$tid){
       $sql="SELECT ga.group_assessment_id,ga.client_id,ga.creation_date,at.teacher_id,at.teacher_category_id,at.assessor_id,ad.diagnostic_id,dtd.student_data_id as teacher_data_id,hau.percComplete as internalreview,hauex.percComplete as exreview
FROM `d_group_assessment` ga
inner join h_group_assessment_teacher at on ga.group_assessment_id=at.group_assessment_id
inner join h_group_assessment_diagnostic ad on at.group_assessment_id=ad.group_assessment_id and at.teacher_category_id=ad.teacher_category_id  left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=3) hau on hau.user_id=at.teacher_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=4) hauex on hauex.user_id=at.assessor_id and hauex.assessment_id=hau.assessment_id left join d_student_data dtd on dtd.student_id=hau.user_id && dtd.assessment_id=hau.assessment_id  where ga.group_assessment_id=? && at.teacher_id=?  group by at.teacher_id";
        $res=$this->db->get_row($sql,array($gaid,$gaid,$gaid,$tid));
        return $res?$res:array();
        }
        

        function getassessmentIdByEmail($gaid,$email){
            $sql="SELECT ga.group_assessment_id,hau.assessment_id,ga.client_id,ga.creation_date,at.teacher_id,at.teacher_category_id,at.assessor_id
FROM `d_group_assessment` ga
inner join h_group_assessment_teacher at on ga.group_assessment_id=at.group_assessment_id left join (select * from h_assessment_user where assessment_id in (select assessment_id from h_assessment_ass_group where group_assessment_id=?) && role=3) hau on hau.user_id=at.teacher_id left join d_user du on du.user_id=at.teacher_id  where ga.group_assessment_id=? && du.email=?  group by at.teacher_id";
        $res=$this->db->get_row($sql,array($gaid,$gaid,$email));
        return $res?$res:array();   
        }
        
	function getTeacherInTeacherAssessmentHTMLRow($tchr_ass_id,$sno,$firstName,$lastName,$email,$doj,$department_id=0,$categoty_id=0,$assessor_id=0,$teacher_id=0,$editable=1){
                
		if($sno==0 && $firstName=='' && $lastName=='' && $email=='' && $doj=='')

			return '';

		static $t_cat_list=false;

		global $t_assessor_list;

		if($t_cat_list===false){

			$t_cat_list= $this->getTeacherCategoryListForTchrAsmnt($tchr_ass_id);

			$t_assessor_list=$this->db->array_col_to_key($this->getExternalAssessorsInGroupAssessment($tchr_ass_id,0,array(1,2)),"user_id");

		}
                
                //print_r($t_assessor_list);
                $external_reviewer=array();
                $internal_reviewer=array();
                foreach($t_assessor_list as $external_internal){
                if($external_internal['added_by_admin']==1){
                $external_reviewer[]=$external_internal;    
                }else{
                $internal_reviewer[]=$external_internal;    
                }
                
                }
                $department=$this->getDepartmentList();

		$attr=$editable?'':'readonly="readonly"';

		$prefix=$teacher_id>0?"[old][$teacher_id]":'[new]';

		$sufix=$teacher_id>0?"":'[]';
                $category_edit_allowed=1;
                $validator_edit_allowed=1;
                $delete_allowed=1;
                
                if($tchr_ass_id>0 && $teacher_id>0){
                 $teacherstatus=$this->getTeacherStatus($tchr_ass_id,$teacher_id);
                 //print_r($teacherstatus);
                if(count($teacherstatus)>0){
                 if(!empty($teacherstatus['teacher_data_id']) || $teacherstatus['internalreview']>0 || $teacherstatus['exreview']>0){
                   $delete_allowed=0;  
                 }
                 
                 if($teacherstatus['internalreview']>0 || $teacherstatus['exreview']>0){
                   $category_edit_allowed=0;  
                 }
                 
                 if($teacherstatus['exreview']>0){
                   $validator_edit_allowed=0;  
                 }
                }
                }
			$ret= '

		<tr class="team_row">

			<td class="s_no">'.$sno.'</td> 

			<td><input type="text" data-type="onlyAlpha" '.$attr.' value="'.$firstName.'" name="teachers'.$prefix.'[first_name]'.$sufix.'" autocomplete="off" class="tableTxtFld firstname"></td>

			<td><input type="text" data-type="onlyAlpha" '.$attr.' value="'.$lastName.'" name="teachers'.$prefix.'[last_name]'.$sufix.'" autocomplete="off" class="tableTxtFld lastname"></td>

			<td><input type="text" data-type="email" '.($teacher_id>0 || $editable==0?'readonly="readonly"':'').' value="'.$email.'" name="teachers'.$prefix.'[email]'.$sufix.'" autocomplete="off" class="tableTxtFld email"></td>

			<td>

				<div class="input-group date-Picker">

					<input type="text" data-type="date" '.$attr.' value="'.$doj.'" placeholder="DD-MM-YYYY" name="teachers'.$prefix.'[doj]'.$sufix.'" autocomplete="off" class="tableTxtFld doj">

					<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>

				</div>

			</td>
                        <td>
                        <select class="tableDdFld selectpicker form-control department" '.($editable?'':'disabled="disabled"').' name="teachers'.$prefix.'[department]'.$sufix.'">
                        <option value=""> -- Select Dept. -- </option>';
                        foreach($department as $department_name){
                        $ret.='<option  '.($department_id==$department_name['school_level_id']?'selected="selected"':'').' value="'.$department_name['school_level_id'].'">'.$department_name['school_level'].'</option>';
    
                        }
                        $ret.='</select>
                        </td> 
			<td>';
                                if(!$category_edit_allowed){
                                $ret.='<input type="hidden"  name="teachers'.$prefix.'[cat_id]'.$sufix.'" value="'.$categoty_id.'">';
                                }
				$ret.='<select class="tableDdFld selectpicker form-control categoty_id" '.(($editable && $category_edit_allowed)?'':'disabled="disabled"').' name="teachers'.$prefix.'[cat_id]'.$sufix.'" autocomplete="off" >

					<option value=""> -- Select Category -- </option>

					';

			foreach($t_cat_list as $c)

				$ret.='<option '.($categoty_id==$c['teacher_category_id']?'selected="selected"':'').' value="'.$c['teacher_category_id'].'">'.$c['teacher_category'].'</option>';

			$ret.='

				</select>';
                                
                        
			$ret.='</td>

			<td>';
                                if(!$validator_edit_allowed){
                                $ret.='<input type="hidden"  name="teachers'.$prefix.'[assessor_id]'.$sufix.'" value="'.$assessor_id.'">';
                                }
				$ret.='<select class="tableDdFld selectpicker form-control assessor_id" '.(($editable && $validator_edit_allowed)?'':'disabled="disabled"').' name="teachers'.$prefix.'[assessor_id]'.$sufix.'" autocomplete="off" id="assessor_id">

					<option value=""> -- Select Assessor -- </option>

					<optgroup label="External">';

			foreach($external_reviewer as $a)

				$ret.='<option '.($assessor_id==$a['user_id']?'selected="selected"':'').' value="'.$a['user_id'].'">'.$a['name'].'</option>

				';

			$ret.='</optgroup><optgroup label="Internal">';
                        foreach($internal_reviewer as $a)

				$ret.='<option '.($assessor_id==$a['user_id']?'selected="selected"':'').' value="'.$a['user_id'].'">'.$a['name'].'</option>

				';
                        
                          $ret.='</optgroup>

				</select>';
                                
			$ret.='</td>

			<td>'.(($editable && $delete_allowed)?'<a class="delete_row" href="javascript:void(0)"><i class="fa fa-times"></i></a>':'').'</td>

		</tr>';

		return $ret;

	}
        
        
        function getStudentInStudentAssessmentHTMLRow($tchr_ass_id,$sno,$firstName,$lastName,$email,$doj,$categoty_id=0,$assessor_id=0,$teacher_id=0,$editable=1){
                $diagnosticModel = new diagnosticModel ();
                            
		if($sno==0 && $firstName=='' && $lastName=='' && $email=='' && $doj=='')

			return '';

		static $t_cat_list=false;

		global $t_assessor_list;

		if($t_cat_list===false){

			$t_cat_list= $this->getTeacherCategoryListForTchrAsmnt($tchr_ass_id);

			$t_assessor_list=$this->db->array_col_to_key($this->getExternalAssessorsInGroupAssessment($tchr_ass_id,0,array(1,2)),"user_id");

		}
                
                //print_r($t_assessor_list);
                $external_reviewer=array();
                $internal_reviewer=array();
                foreach($t_assessor_list as $external_internal){
                if($external_internal['added_by_admin']==1){
                $external_reviewer[]=$external_internal;    
                }else{
                $internal_reviewer[]=$external_internal;    
                }
                
                }
                $department=$this->getDepartmentList();

		$attr=$editable?'':'readonly="readonly"';

		$prefix=$teacher_id>0?"[old][$teacher_id]":'[new]';

		$sufix=$teacher_id>0?"":'[]';
                $category_edit_allowed=1;
                $validator_edit_allowed=1;
                $delete_allowed=1;
                
                if($tchr_ass_id>0 && $teacher_id>0){
                 $teacherstatus=$this->getStudentStatus($tchr_ass_id,$teacher_id);
                 //print_r($teacherstatus);
                if(count($teacherstatus)>0){
                 if(!empty($teacherstatus['teacher_data_id']) || $teacherstatus['internalreview']>0 || $teacherstatus['exreview']>0){
                   $delete_allowed=0;  
                 }
                 
                 if($teacherstatus['internalreview']>0 || $teacherstatus['exreview']>0){
                   $category_edit_allowed=0;  
                 }
                 
                 if($teacherstatus['exreview']>0){
                   $validator_edit_allowed=0;  
                 }
                }
                }
			$ret= '

		<tr class="team_row">

			<td class="s_no">'.$sno.'</td> 

			<td><input type="text" data-type="onlyAlpha" '.$attr.' value="'.$firstName.'" name="teachers'.$prefix.'[first_name]'.$sufix.'" autocomplete="off" class="tableTxtFld firstname"></td>

			<td><input type="text" data-type="onlyAlpha" '.$attr.' value="'.$lastName.'" name="teachers'.$prefix.'[last_name]'.$sufix.'" autocomplete="off" class="tableTxtFld lastname"></td>

			<td><input type="text" data-type="email" '.($teacher_id>0 || $editable==0?'readonly="readonly"':'').' value="'.$email.'" name="teachers'.$prefix.'[email]'.$sufix.'" autocomplete="off" class="tableTxtFld email">
<input type="hidden" data-type="date" '.$attr.' value="'.$doj.'" placeholder="DD-MM-YYYY" name="teachers'.$prefix.'[doj]'.$sufix.'" autocomplete="off" class="tableTxtFld doj">

<input type="hidden" class="department"  name="teachers'.$prefix.'[department]'.$sufix.'" value="1">  				
<input type="hidden" class="categoty_id"  name="teachers'.$prefix.'[cat_id]'.$sufix.'" value="'.$diagnosticModel->getStudentCatId().'">                                  
</td>';
                              
                                 //$ret.='<input type="hidden"  name="teachers'.$prefix.'[cat_id]'.$sufix.'" value="'.$categoty_id.'">';
                                
				
                                
                        
			$ret.='

			<td>';
                                if(!$validator_edit_allowed){
                                $ret.='<input type="hidden"  name="teachers'.$prefix.'[assessor_id]'.$sufix.'" value="'.$assessor_id.'">';
                                }
				$ret.='<select class="tableDdFld selectpicker form-control assessor_id" '.(($editable && $validator_edit_allowed)?'':'disabled="disabled"').' name="teachers'.$prefix.'[assessor_id]'.$sufix.'" autocomplete="off" id="assessor_id">

					<option value=""> -- Select Assessor -- </option>

					<optgroup label="External">';

			foreach($external_reviewer as $a)

				$ret.='<option '.($assessor_id==$a['user_id']?'selected="selected"':'').' value="'.$a['user_id'].'">'.$a['name'].'</option>

				';

			$ret.='</optgroup><optgroup label="Internal">';
                        foreach($internal_reviewer as $a)

				$ret.='<option '.($assessor_id==$a['user_id']?'selected="selected"':'').' value="'.$a['user_id'].'">'.$a['name'].'</option>

				';
                        
                          $ret.='</optgroup>

				</select>';
                                
			$ret.='</td>

			<td>'.(($editable && $delete_allowed)?'<a class="delete_row" href="javascript:void(0)"><i class="fa fa-times"></i></a>':'').'</td>

		</tr>';

		return $ret;

	}

	function getSchoolAssessment($assessment_id,$external_team =0){

		

			if($external_team==1)

			{

				$sql = "SELECT a.iscollebrative,a.review_criteria,a.language_id,a.aqsdata_id,aq.school_aqs_pref_start_date,aq.school_aqs_pref_end_date,a.assessment_id, a.facilitator_id, u3.client_id as f_client_id, group_concat(distinct concat(u2.client_id,'_',et.user_id,'_',et.user_sub_role))  as subroles,a.tier_id , a.award_scheme_id, a.aqs_round ,d.assessment_type_id,d.diagnostic_id, a.client_id,a.create_date as create_date ,

c.client_name ,t.assessment_type_name, 

group_concat(distinct b.user_id order by b.role) as user_ids,group_concat(distinct ifnull(b.percComplete,'0') order by b.role) as percCompletes,(SELECT tc.client_id FROM 

h_assessment_user hc, d_user tc where tc.user_id=hc.user_id and hc.assessment_id=a.assessment_id and hc.role=4) as external_client, group_concat(distinct u.name order by b.role) as user_names,

				 1 as assessments_count

					FROM `d_assessment` a 

                    left join d_tier tr on a.tier_id = tr.standard_id

                    left join d_award_scheme das on a.award_scheme_id = das.award_scheme_id

					inner join `h_assessment_user` b on a.assessment_id=b.assessment_id 

					inner join `d_client` c on c.client_id=a.client_id

					inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id

					inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id

					inner join d_user u on u.user_id=b.user_id
                                        
                                        left join d_user u3 on u3.user_id=a.facilitator_id
                                        left join d_AQS_data aq  on aq.id=a.aqsdata_id

                    left join h_assessment_external_team et on et.assessment_id=a.assessment_id
                    left join d_user u2 on u2.user_id=et.user_id

where a.assessment_id=?;";

			}

			else

			{

			$sql = "SELECT a.iscollebrative,a.review_criteria,a.language_id ,a.aqsdata_id,aq.school_aqs_pref_start_date,aq.school_aqs_pref_end_date, a.assessment_id, a.facilitator_id, a.tier_id , a.award_scheme_id, a.aqs_round ,d.assessment_type_id,d.diagnostic_id, a.client_id,a.create_date as create_date ,

c.client_name ,t.assessment_type_name,

group_concat(b.user_id order by b.role) as user_ids,group_concat(b.percComplete order by b.role) as percCompletes,(SELECT tc.client_id FROM 

h_assessment_user hc, d_user tc where tc.user_id=hc.user_id and hc.assessment_id=a.assessment_id and hc.role=4) as external_client, group_concat(u.name order by b.role) as user_names,

				 1 as assessments_count

					FROM `d_assessment` a 

                    left join d_tier tr on a.tier_id = tr.standard_id

                    left join d_award_scheme das on a.award_scheme_id = das.award_scheme_id

					inner join `h_assessment_user` b on a.assessment_id=b.assessment_id 

					inner join `d_client` c on c.client_id=a.client_id

					inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id

					inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id

					inner join d_user u on u.user_id=b.user_id	
                                        left join d_AQS_data aq  on aq.id=a.aqsdata_id

where a.assessment_id= ?;";

			}

			

			$res=$this->db->get_row($sql,array($assessment_id));

		return $res?$res:array();

		

	}
        
        
        function getCollegeAssessment($assessment_id,$external_team =0){

		

			if($external_team==1)

			{

				$sql = "SELECT a.aqsdata_id,aq.school_aqs_pref_start_date,aq.school_aqs_pref_end_date,a.assessment_id, a.facilitator_id, u3.client_id as f_client_id, group_concat(distinct concat(u2.client_id,'_',et.user_id,'_',et.user_sub_role))  as subroles,a.tier_id , a.award_scheme_id, a.aqs_round ,d.assessment_type_id,d.diagnostic_id, a.client_id,a.create_date as create_date ,

c.client_name ,t.assessment_type_name, 

group_concat(distinct b.user_id order by b.role) as user_ids,group_concat(distinct ifnull(b.percComplete,'0') order by b.role) as percCompletes,(SELECT tc.client_id FROM 

h_assessment_user hc, d_user tc where tc.user_id=hc.user_id and hc.assessment_id=a.assessment_id and hc.role=4) as external_client, group_concat(distinct u.name order by b.role) as user_names,

				 1 as assessments_count

					FROM `d_assessment` a 

                    left join d_tier tr on a.tier_id = tr.standard_id

                    left join d_award_scheme das on a.award_scheme_id = das.award_scheme_id

					inner join `h_assessment_user` b on a.assessment_id=b.assessment_id 

					inner join `d_client` c on c.client_id=a.client_id

					inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id

					inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id

					inner join d_user u on u.user_id=b.user_id
                                        
                                        left join d_user u3 on u3.user_id=a.facilitator_id
                                        left join d_AQS_data aq  on aq.id=a.aqsdata_id

                    left join h_assessment_external_team et on et.assessment_id=a.assessment_id
                    left join d_user u2 on u2.user_id=et.user_id

where a.assessment_id=?;";

			}

			else

			{

			$sql = "SELECT a.aqsdata_id,aq.school_aqs_pref_start_date,aq.school_aqs_pref_end_date, a.assessment_id, a.facilitator_id, a.tier_id , a.award_scheme_id, a.aqs_round ,d.assessment_type_id,d.diagnostic_id, a.client_id,a.create_date as create_date ,

c.client_name ,t.assessment_type_name,

group_concat(b.user_id order by b.role) as user_ids,group_concat(b.percComplete order by b.role) as percCompletes,(SELECT tc.client_id FROM 

h_assessment_user hc, d_user tc where tc.user_id=hc.user_id and hc.assessment_id=a.assessment_id and hc.role=4) as external_client, group_concat(u.name order by b.role) as user_names,

				 1 as assessments_count

					FROM `d_assessment` a 

                    left join d_tier tr on a.tier_id = tr.standard_id

                    left join d_award_scheme das on a.award_scheme_id = das.award_scheme_id

					inner join `h_assessment_user` b on a.assessment_id=b.assessment_id 

					inner join `d_client` c on c.client_id=a.client_id

					inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id

					inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id

					inner join d_user u on u.user_id=b.user_id	
                                        left join d_AQS_data aq  on aq.id=a.aqsdata_id

where a.assessment_id= ?;";

			}

			

			$res=$this->db->get_row($sql,array($assessment_id));

		return $res?$res:array();

		

	}

	function getTeacherAssessment($assessment_id,$assessment_type_id){

			$sql = "SELECT dt.group_assessment_id,dt.admin_user_id,dt.student_round,dt.student_review_type_id, 0 as assessment_id, dt.assessment_type_id, dt.client_id,dt.creation_date as create_date ,c.client_name ,t.assessment_type_name, cn.network_id, group_concat(b.isFilled order by b.role,a.assessment_id) as  statuses, group_concat(distinct b.role order by b.role) as roles, group_concat(b.percComplete order by b.role,a.assessment_id) as percCompletes, group_concat(if(b.ratingInputDate is null,'',date(b.ratingInputDate)) order by b.role,a.assessment_id) as ratingInputDates, group_concat(b.user_id order by b.role,a.assessment_id) as user_ids, group_concat(u.name order by b.role,a.assessment_id) as user_names,group_concat(concat(r.report_id,'|',r.isPublished,'|',r.publishDate)) as report_data,count(distinct s.assessment_id) as assessments_count, group_concat(if(td.value is null,'',td.value) order by b.role,a.assessment_id) as teacherInfoStatuses

				FROM d_group_assessment dt

				left join h_assessment_ass_group s on s.group_assessment_id = dt.group_assessment_id

				left join `d_assessment` a  on a.assessment_id = s.assessment_id

				left join `h_assessment_user` b on a.assessment_id=b.assessment_id

				left join d_teacher_data td on td.teacher_id=b.user_id and b.role=3 and td.assessment_id=b.assessment_id and td.attr_id=11

				left join `d_client` c on c.client_id=dt.client_id

				left join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id

				left join `d_assessment_type` t on dt.assessment_type_id=t.assessment_type_id

				left join d_user u on u.user_id=b.user_id

				left join h_client_network cn on cn.client_id=c.client_id				

				left join h_assessment_report r on r.assessment_id=a.assessment_id

				where dt.group_assessment_id=? and t.assessment_type_id=?;";

			

			$res=$this->db->get_row($sql,array($assessment_id,$assessment_type_id));

		return $res?$res:array();

		

	}
        
        function getStudentAssessment($assessment_id,$assessment_type_id){

			$sql = "SELECT dt.group_assessment_id,dt.admin_user_id,dt.student_review_type_id, 0 as assessment_id, dt.assessment_type_id, dt.client_id,dt.creation_date as create_date ,c.client_name ,t.assessment_type_name, cn.network_id, group_concat(b.isFilled order by b.role,a.assessment_id) as  statuses, group_concat(distinct b.role order by b.role) as roles, group_concat(b.percComplete order by b.role,a.assessment_id) as percCompletes, group_concat(if(b.ratingInputDate is null,'',date(b.ratingInputDate)) order by b.role,a.assessment_id) as ratingInputDates, group_concat(b.user_id order by b.role,a.assessment_id) as user_ids, group_concat(u.name order by b.role,a.assessment_id) as user_names,group_concat(concat(r.report_id,'|',r.isPublished,'|',r.publishDate)) as report_data,count(distinct s.assessment_id) as assessments_count, group_concat(if(sd.value is null,'',sd.value) order by b.role,a.assessment_id) as teacherInfoStatuses

				FROM d_group_assessment dt

				left join h_assessment_ass_group s on s.group_assessment_id = dt.group_assessment_id

				left join `d_assessment` a  on a.assessment_id = s.assessment_id

				left join `h_assessment_user` b on a.assessment_id=b.assessment_id

				left join d_student_data sd on sd.student_id=b.user_id and b.role=3 and sd.assessment_id=b.assessment_id and sd.attr_id=49

				left join `d_client` c on c.client_id=dt.client_id

				left join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id

				left join `d_assessment_type` t on dt.assessment_type_id=t.assessment_type_id

				left join d_user u on u.user_id=b.user_id

				left join h_client_network cn on cn.client_id=c.client_id				

				left join h_assessment_report r on r.assessment_id=a.assessment_id

				where dt.group_assessment_id=? and t.assessment_type_id=?;";

			

			$res=$this->db->get_row($sql,array($assessment_id,$assessment_type_id));

		return $res?$res:array();

		

	}

	function getDiagnosticTypeForTeacherType($group_assessment_id){

		$sql = "select ga.teacher_category_id,d.diagnostic_id from

				h_group_assessment_diagnostic ga inner join d_teacher_category t

				on ga.teacher_category_id = t.teacher_category_id

				inner join d_diagnostic d on d.diagnostic_id=ga.diagnostic_id
                                inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 

				where ga.group_assessment_id = ?";

	

		$res=$this->db->get_results($sql,array($group_assessment_id));

		return $res?$res:array();

	}
	function getTeacherCategoryForDiagnostic($diagnostic_id){
	
		$sql = "SELECT teacher_category FROM d_teacher_category tc inner join h_diagnostic_teacher_cat htc
				on tc.teacher_category_id=htc.teacher_cat_id 
				where htc.diagnostic_id=?";
	
		$res=$this->db->get_row($sql,array($diagnostic_id));
	
		return $res?$res:array();
	
	}

	function getReviewerSubRoles($roleid){

		$sql = "Select  sub_role_id,sub_role_name from d_user_sub_role us

				where user_role_id=? order by sub_role_order";

	

		$res=$this->db->get_results($sql,array($roleid));

		return $res?$res:array();

	}

	function getClientReviews($client_id)

	{

		$sql = "SELECT a.client_id,d.assessment_type_id,a.assessment_id,a.diagnostic_id,a.tier_id,a.award_scheme_id,ifnull(ar.isPublished,0) as 'isPublished',group_concat(au.user_id) as 'users',group_concat(au.role order by au.user_id) as 'roles',group_concat(au.isFilled order by au.user_id) as 'filledStatus',ifnull(a.d_sub_assessment_type_id,1)as 'sub_assessment_type',dad.status as AQS_status,a.is_approved FROM 

				d_assessment a inner join h_assessment_user au on a.assessment_id = au.assessment_id

				inner join d_diagnostic d on d.diagnostic_id = a.diagnostic_id and d.assessment_type_id=1
                                
				left join h_assessment_report ar ON au.assessment_id = ar.assessment_id
                                
                                left join d_AQS_data dad ON dad.id = a.aqsdata_id   
				where client_id=? 

				GROUP BY a.assessment_id order by a.create_date;

	";

		$res=$this->db->get_results($sql,array($client_id));

		return $res?$res:array();

	}

	function getSubReviewsType($assessment_type_id=1)

	{

		$sql = "SELECT * FROM d_sub_assessment_type where assessment_type_id =?;";

		$res=$this->db->get_results($sql,array($assessment_type_id));

		return $res?$res:array();

	}

	function getProducts($active=1,$product_id=0)

	{

		$sql = "SELECT * FROM d_product where active =?";

		if($product_id>0)

		{

			$sql.= " AND product_id = ?";

			$res=$this->db->get_row($sql,array($active,$product_id));

			return $res?$res:array();

		}	

		$res=$this->db->get_results($sql,array($active));

		return $res?$res:array();

	}

	function getPaymentModes()

	{

		$sql = "SELECT * FROM d_payment_mode;";

		$res=$this->db->get_results($sql);

		return $res?$res:array();

	}

	function saveClientProduct($clientId,$productId,$transactionId,$transStatus,$date,$payment_mode,$isApproved=0)

	{

		if($this->db->insert('h_client_product',array("client_id"=>$clientId,"product_id"=>$productId,"transaction_id"=>$transactionId,"transaction_status"=>$transStatus,"purchase_date"=>$date,"payment_mode"=>$payment_mode,"is_approved"=>$isApproved)))

			return true;

		return false;

	}

	/*function updateClientProduct($reviewIds,$transaction_row_id)

	{

		if($this->db->update('h_client_product',array("d_assessment_ids"=>$reviewIds),array("transaction_row_id"=>$transaction_row_id)))

			return true;

			return false;

	}*/

	function updateClientTransaction($transaction_id,$review_id)

	{

		if($this->db->insert('h_transaction_assessment',array("transaction_id"=>$transaction_id,"assessment_id"=>$review_id)))

			return true;

			return false;

	}

	function getClientProducts($client_id,$validity=0)

	{

		if($validity>0)

		{

			/*$sql = "select cp.*,LENGTH(cp.d_assessment_ids) - LENGTH(REPLACE(cp.d_assessment_ids, ',', '')) + 1 as 'selfRevsDone',

				 if((LENGTH(cp.d_assessment_ids) - LENGTH(REPLACE(cp.d_assessment_ids, ',', '')) + 1)= d.self_reviews,0,d.self_reviews-(LENGTH(cp.d_assessment_ids) - LENGTH(REPLACE(cp.d_assessment_ids, ',', '')) + 1)) as 'selfRevsLeft'

				 from

				h_client_product cp left join d_product d on d.product_id=cp.product_id

				and (curdate() between cp.purchase_date and adddate(cp.purchase_date, interval d.validity year)) 

				where client_id=?;";*/

				$sql = " SELECT hc.transaction_id,p.self_reviews - (select count(*) from h_transaction_assessment where transaction_id=hc.transaction_id) as 'selfRevsLeft' 

					 from

					 h_client_product hc

					inner join d_product p on hc.product_id=p.product_id 

					 where hc.client_id=?

					 order by transaction_row_id desc;  ";

			

		}

		else 

		{

			/*$sql = "select cp.*,LENGTH(cp.d_assessment_ids) - LENGTH(REPLACE(cp.d_assessment_ids, ',', '')) + 1 as 'selfRevsDone',

				 if((LENGTH(cp.d_assessment_ids) - LENGTH(REPLACE(cp.d_assessment_ids, ',', '')) + 1)= d.self_reviews,0,d.self_reviews-(LENGTH(cp.d_assessment_ids) - LENGTH(REPLACE(cp.d_assessment_ids, ',', '')) + 1)) as 'selfRevsLeft'

				 from

				h_client_product cp left join d_product d on d.product_id=cp.product_id

				where client_id=?;";*/

			$sql = " SELECT hc.transaction_id,p.self_reviews - (select count(*) from h_transaction_assessment where transaction_id=hc.transaction_id) as 'selfRevsLeft' 

				 from

				 h_client_product hc

				inner join d_product p on hc.product_id=p.product_id 

				 where hc.client_id=?

				 order by transaction_row_id desc;  ";

			

		}

		$res=$this->db->get_results($sql,array($client_id));

		return $res?$res:array();

	}

	function getSubAssessmentTypeReviews($client_id,$subAssessmentType_id,$num_reviews)

	{

		$sql = "select  assessment_id from d_assessment where client_id=? and d_sub_assessment_type_id=? order by create_date desc limit $num_reviews;";

		$res=$this->db->get_results($sql,array($client_id,$subAssessmentType_id));

		return $res?$res:array();

	}

	function getReviewTypeProductsAvailed($sub_assessment_type_id,$client_id)

	{

		$sql = "select (select count(*) from h_transaction_assessment ha inner join h_client_product hc on ha.transaction_id=hc.transaction_id where ha.assessment_id=a.assessment_id) as 'active',

				(select count(*) from h_transaction_assessment ha inner join h_client_product hc on ha.transaction_id=hc.transaction_id where ha.assessment_id=a.assessment_id and is_approved=1) as 'isPmtApproved'

				 from d_assessment a  

				where a.d_sub_assessment_type_id=? and  a.client_id=?;";

		$res=$this->db->get_results($sql,array($sub_assessment_type_id,$client_id));

		return $res?$res:array();

		

	}

	function saveSubAssessmentTypeRequest($sub_assessment_type_id,$user_id)

	{

		$create_date = date('Y-m-d h:i:s');

		return $this->db->insert("h_sub_assessment_request",array("sub_assessment_type_id"=>$sub_assessment_type_id,"user_id"=>$user_id,"create_date"=>$create_date));

	}

	function isReviewPaymentApproved($reviewId)

	{

		$sql = "select count(*) as isApproved from h_transaction_assessment ha inner join h_client_product hc on ha.transaction_id=hc.transaction_id where ha.assessment_id=? and is_approved=1";

		$res=$this->db->get_row($sql,array($reviewId));

		return $res?$res:array();

	}

	function approvePayment($reviewId)
	{

		$sql = "update h_client_product hc,h_transaction_assessment ta set hc.is_approved=1 where hc.transaction_id=ta.transaction_id and ta.assessment_id=?";

		return $this->db->query($sql,array($reviewId));

	}
        function updateReviewApproval($reviewId,$isApproved,$reason)
	{
		$sql = "update d_assessment set is_approved=?,rejection_reason=? where assessment_id=?";
		return $this->db->query($sql,array($isApproved,$reason,$reviewId));
	}

	// function for getting review count by role for a user
        public function getReviewCountByRole($email=''){
            $condition = '';
            $condition1 = '';
            if($email!=''){
                $condition = "AND t2.email = '".$email."'";
                //$condition1 = "FROM h_assessment_external_team a Left Join d_user u On (a.user_id=u.user_id) where u.email = '".$email."' GROUP by u.email";
            }
           /*  $SQL="SELECT 
                    (SELECT COUNT(t1.assessment_id) FROM h_assessment_user t1 Left Join d_user t2 On (t1.user_id=t2.user_id)
                        Left Join d_assessment t3 On (t3.assessment_id=t1.assessment_id) Left Join d_diagnostic t4 on (t3.diagnostic_id=t4.diagnostic_id)
                        WHERE t1.role = '4' ".$condition." and t4.assessment_type_id='1' and t3.d_sub_assessment_type_id!='1')
                            as 'Lead_Assessor/Sr. Associate_Assessor',
                    (SELECT COUNT(t1.assessment_row_id) FROM h_assessment_external_team t1 Left Join d_user t2 On (t1.user_id=t2.user_id)
                        WHERE t1.user_sub_role = '3' ".$condition.") as Intern,
                    (SELECT COUNT(t1.assessment_row_id) FROM h_assessment_external_team t1 Left Join d_user t2 On (t1.user_id=t2.user_id)
                        WHERE t1.user_sub_role = '4' ".$condition.") as Associate_Assessor,
                    (SELECT COUNT(t1.assessment_row_id) FROM h_assessment_external_team t1 Left Join d_user t2 On (t1.user_id=t2.user_id)
                        WHERE t1.user_sub_role = '2' ".$condition.") as Intern_Assessor
                ;"; */
                     $SQL="SELECT sub_role_name,ifnull(num,0) num,sub_role_id from (SELECT sub_role_name,num,sub_role_order,sub_role_id FROM d_user_sub_role a left JOIN "
                    . "(SELECT COUNT(distinct t1.assessment_id) num,t1.user_sub_role from d_assessment u INNER JOIN d_diagnostic d on u.diagnostic_id = d.diagnostic_id  INNER JOIN h_assessment_external_team t1 ON t1.assessment_id = u.assessment_id LEFT JOIN d_user t2 on "
                    . " t1.user_id = t2.user_id WHERE 1=1 ".$condition." group by t1.user_sub_role ) b on a.sub_role_id=b.user_sub_role where a.sub_role_id!=1 AND a.user_role_id=4
                        UNION
			SELECT sub_role_name,num,sub_role_order,sub_role_id FROM d_user_sub_role a left JOIN 
                        (SELECT COUNT(t1.assessment_id) num,1 as user_sub_role FROM h_assessment_user t1 
                        Left Join d_user t2 On (t1.user_id=t2.user_id) Left Join d_assessment t3 On
                        (t3.assessment_id=t1.assessment_id) Left Join d_diagnostic t4 on (t3.diagnostic_id=t4.diagnostic_id) 
                        WHERE t1.role = '4' ".$condition." and t4.assessment_type_id='1' and t3.d_sub_assessment_type_id!='1') b 
                        on a.sub_role_id=b.user_sub_role where a.sub_role_id=1 ) a	order by sub_role_order    	
                ";
           // echo $SQL;
            $result = $this->db->get_results($SQL);
            return $result;
            
        }
	
        public function getReviewCountByRoleNew($email=''){
            $condition = '';
            $condition1 = '';
            if($email!=''){
                $condition = "AND t2.email = '".$email."'";
                //$condition1 = "FROM h_assessment_external_team a Left Join d_user u On (a.user_id=u.user_id) where u.email = '".$email."' GROUP by u.email";
            }
           /*  $SQL="SELECT 
                    (SELECT COUNT(t1.assessment_id) FROM h_assessment_user t1 Left Join d_user t2 On (t1.user_id=t2.user_id)
                        Left Join d_assessment t3 On (t3.assessment_id=t1.assessment_id) Left Join d_diagnostic t4 on (t3.diagnostic_id=t4.diagnostic_id)
                        WHERE t1.role = '4' ".$condition." and t4.assessment_type_id='1' and t3.d_sub_assessment_type_id!='1')
                            as 'Lead_Assessor/Sr. Associate_Assessor',
                    (SELECT COUNT(t1.assessment_row_id) FROM h_assessment_external_team t1 Left Join d_user t2 On (t1.user_id=t2.user_id)
                        WHERE t1.user_sub_role = '3' ".$condition.") as Intern,
                    (SELECT COUNT(t1.assessment_row_id) FROM h_assessment_external_team t1 Left Join d_user t2 On (t1.user_id=t2.user_id)
                        WHERE t1.user_sub_role = '4' ".$condition.") as Associate_Assessor,
                    (SELECT COUNT(t1.assessment_row_id) FROM h_assessment_external_team t1 Left Join d_user t2 On (t1.user_id=t2.user_id)
                        WHERE t1.user_sub_role = '2' ".$condition.") as Intern_Assessor
                ;"; */
                     /*$SQL="SELECT assessment_type_id,d_sub_assessment_type_id,sub_role_name,ifnull(num,0) num,sub_role_id from (SELECT b.assessment_type_id ,0 as d_sub_assessment_type_id,sub_role_name,num,sub_role_order,sub_role_id FROM d_user_sub_role a left JOIN "
                    . "(SELECT dd.assessment_type_id,COUNT(distinct t1.assessment_id) num,t1.user_sub_role from d_assessment u INNER JOIN d_diagnostic d on u.diagnostic_id = d.diagnostic_id  INNER JOIN h_assessment_external_team t1 ON t1.assessment_id = u.assessment_id LEFT JOIN d_user t2 on "
                    . " t1.user_id = t2.user_id left join d_diagnostic dd on u.diagnostic_id=dd.diagnostic_id  WHERE 1=1 ".$condition." group by assessment_type_id,t1.user_sub_role ) b on a.sub_role_id=b.user_sub_role where a.sub_role_id!=1 AND a.user_role_id=4
                        UNION
			SELECT b.assessment_type_id,b.d_sub_assessment_type_id,sub_role_name,num,sub_role_order,sub_role_id FROM d_user_sub_role a left JOIN 
                        (SELECT t4.assessment_type_id,t3.d_sub_assessment_type_id,COUNT(t1.assessment_id) num,1 as user_sub_role FROM h_assessment_user t1 
                        Left Join d_user t2 On (t1.user_id=t2.user_id) Left Join d_assessment t3 On
                        (t3.assessment_id=t1.assessment_id) Left Join d_diagnostic t4 on (t3.diagnostic_id=t4.diagnostic_id) 
                        WHERE t1.role = '4' ".$condition." and (t4.assessment_type_id='1' ||  t4.assessment_type_id='5') and t3.d_sub_assessment_type_id!='1' group by assessment_type_id) b 
                        on a.sub_role_id=b.user_sub_role where a.sub_role_id=1) a	order by sub_role_order";
                      
                      */
                       $SQL="select assessment_type_id,d_sub_assessment_type_id,sub_role_name,num,sub_role_order,sub_role_id,group_assessment_id,sum(school) as `School Review`,sum(teacher) as `Teacher Review`,sum(student)  as `Student Review`,sum(college) as `College Review` from (
                            (select *,0 as group_assessment_id,sum(if(assessment_type_id=1,num,0)) as school,sum(if(assessment_type_id=2,1,0)) as teacher ,sum(if(assessment_type_id=4,1,0)) as student,sum(if(assessment_type_id=5,num,0)) as college  
                            from (SELECT b.assessment_type_id ,0 as d_sub_assessment_type_id,sub_role_name,num,sub_role_order,sub_role_id 
                            FROM d_user_sub_role a 
                            left JOIN (SELECT dd.assessment_type_id,COUNT(distinct t1.assessment_id) num,t1.user_sub_role from d_assessment u 
                            INNER JOIN d_diagnostic d on u.diagnostic_id = d.diagnostic_id 
                            INNER JOIN h_assessment_external_team t1 ON t1.assessment_id = u.assessment_id 
                            LEFT JOIN d_user t2 on t1.user_id = t2.user_id 
                            left join d_diagnostic dd on u.diagnostic_id=dd.diagnostic_id 
                            WHERE 1=1 ".$condition." && u.isAssessmentActive=1
                            group by assessment_type_id,t1.user_sub_role ) b on a.sub_role_id=b.user_sub_role where a.sub_role_id!=1 AND a.user_role_id=4
                            ) xyz1 group by xyz1.assessment_type_id,sub_role_id)
                            UNION
                           (select *,sum(if(assessment_type_id=1,num,0)) as school,sum(if(assessment_type_id=2,1,0)) as teacher ,sum(if(assessment_type_id=4,1,0)) as student,sum(if(assessment_type_id=5,num,0)) as student 
                           from (SELECT b.assessment_type_id,b.d_sub_assessment_type_id,sub_role_name,num,sub_role_order,sub_role_id,group_assessment_id 
                           FROM d_user_sub_role a 
                           left JOIN (SELECT t4.assessment_type_id,t3.d_sub_assessment_type_id,count(t1.assessment_id) num,1 as user_sub_role,dga.group_assessment_id 
                           FROM h_assessment_user t1 Left Join d_user t2 On (t1.user_id=t2.user_id) 
                           Left Join d_assessment t3 On (t3.assessment_id=t1.assessment_id) 
                           Left Join d_diagnostic t4 on (t3.diagnostic_id=t4.diagnostic_id) 
                           LEFT JOIN h_assessment_ass_group hag on hag.assessment_id=t3.assessment_id
                           LEFT JOIN d_group_assessment dga on hag.group_assessment_id=dga.group_assessment_id
                           WHERE (t1.role = '4' ".$condition." ) && (((t4.assessment_type_id=1 || t4.assessment_type_id=5 ) && t3.isAssessmentActive=1 && dga.group_assessment_id IS NULL) || ((t4.assessment_type_id=2 || t4.assessment_type_id=4 ) && dga.isGroupAssessmentActive=1 && dga.group_assessment_id IS NOT NULL))
                           group by assessment_type_id,dga.group_assessment_id) b on a.sub_role_id=b.user_sub_role where a.sub_role_id=1) xyz group by xyz.assessment_type_id
                           )) xxx group by sub_role_id order by sub_role_order";
                          //&& t3.isAssessmentActive=1 && dga.isGroupAssessmentActive=1
           // echo $SQL;
            $result = $this->db->get_results($SQL);
            return $result;
            
        }
        
	// function for getting workshop count by role for a user
        public function getWorkshopCountByUserId($user_id=''){
            $condition = '';
            if($user_id!=''){
                $condition = " wu.user_id = '".$user_id."'";
                //$condition1 = "FROM h_assessment_external_team a Left Join d_user u On (a.user_id=u.user_id) where u.email = '".$email."' GROUP by u.email";
            }
           
            $SQL="SELECT wr.workshop_sub_role_name, ifnull(a.num,0) num ,a.workshops_user_role_id from d_workshops_role wr 
                LEFT JOIN (SELECT sb.workshop_sub_role_name as role_name,wu.workshops_user_role_id ,count(w.workshop_id) as num  FROM d_workshops w 
                INNER JOIN h_workshops_user wu on wu.workshop_id = w.workshop_id 
                INNER JOIN d_workshops_role sb on sb.id = wu.workshops_user_role_id 
                WHERE 1=1 AND $condition   group by sb.workshop_sub_role_name ) a on a.`workshops_user_role_id` = wr.id";
            
            $result = $this->db->get_results($SQL);
            return $result;
            
        }
        
        // function for getting group concat of assessment ids on 25-05-2016 by Mohit Kumar
        public function getAssessmentIDs($user_id,$rid){
            if($rid==1){
                $SQL="SELECT group_concat(distinct t1.assessment_id) as assessment_id FROM h_assessment_user t1 Left Join d_user t2 On 
                    (t1.user_id=t2.user_id) Left Join d_assessment t3 On (t3.assessment_id=t1.assessment_id) Left Join d_diagnostic t4 on 
                    (t3.diagnostic_id=t4.diagnostic_id) WHERE t1.role = '4' AND t2.user_id = '".$user_id."' 
                    and t4.assessment_type_id='1' and t3.d_sub_assessment_type_id!='1'";
            } else {
                 $SQL="SELECT group_concat(distinct t1.assessment_id) as assessment_id  "
                         . "FROM d_assessment u INNER JOIN d_diagnostic d on u.diagnostic_id = d.diagnostic_id "
                         . "INNER JOIN h_assessment_external_team t1 on t1.assessment_id = u.assessment_id "
                         . "Left Join d_user t2 On (t1.user_id=t2.user_id)  WHERE  t1.user_sub_role = '".$rid."' "
                         . "AND t2.user_id = '".$user_id."' AND d.assessment_type_id = 1;";
            }
            $result = $this->db->get_row($SQL);
            return $result;
        }
        
        public function getAssessmentIDsNew($user_id,$rid){
            if($rid==1){
                $SQL="SELECT group_concat(distinct t1.assessment_id) as assessment_id FROM h_assessment_user t1 Left Join d_user t2 On 
                    (t1.user_id=t2.user_id) Left Join d_assessment t3 On (t3.assessment_id=t1.assessment_id) Left Join d_diagnostic t4 on 
                    (t3.diagnostic_id=t4.diagnostic_id) WHERE t1.role = '4' AND t2.user_id = '".$user_id."' 
                    and (t4.assessment_type_id='1' || t4.assessment_type_id='2' || t4.assessment_type_id='4' ||  t4.assessment_type_id='5') and t3.d_sub_assessment_type_id!='1'";
            } else {
                 $SQL="SELECT group_concat(distinct t1.assessment_id) as assessment_id  "
                         . "FROM d_assessment u INNER JOIN d_diagnostic d on u.diagnostic_id = d.diagnostic_id "
                         . "INNER JOIN h_assessment_external_team t1 on t1.assessment_id = u.assessment_id "
                         . "Left Join d_user t2 On (t1.user_id=t2.user_id)  WHERE  t1.user_sub_role = '".$rid."' "
                         . "AND t2.user_id = '".$user_id."' AND (d.assessment_type_id = 1 || d.assessment_type_id = 5);";
            }
            $result = $this->db->get_row($SQL);
            return $result;
        }
        
        // new function for creating school assessment according to tap admin functionality on 06-06-2016 bt Mohit Kumar
        function createSchoolAssessmentNew($client_id, $internal_assessor_id, $facilitator_id ,$diagnostic_id, $tier_id, $award_scheme_id, $aqs_round,$external_assessor_id,$ext_team,$start_date='',$end_date='',$facilitatorDataArray = array(),$notificationSettingData = array(),$notificationTeam = array(),$lang_id=DEFAULT_LANGUAGE,$review_criteria="",$review_type=0) {
            $aid= 0;
            $review_criteria=trim($review_criteria);
            if(OFFLINE_STATUS==TRUE){
                //start---> call function for creating unique id for creating assessment on 10-03-2016 by Mohit Kumar
                $uniqueID = $this->db->createUniqueID('createSchoolAssessment');
                //end---> call function for creating unique id for creating assessment on 10-03-2016 by Mohit Kumar
            }
            
            if ($this->db->insert("d_assessment", array('client_id' => $client_id,'review_criteria'=>$review_criteria, 'facilitator_id' =>$facilitator_id ,'diagnostic_id' => $diagnostic_id, 'tier_id' => $tier_id,
                'award_scheme_id' => $award_scheme_id,'aqs_round' => $aqs_round, 'd_sub_assessment_type_id' => '2', 'create_date' => date("Y-m-d H:i:s"),'language_id'=>$lang_id,'iscollebrative'=>$review_type))) 
            {
                $aid = $this->db->get_last_insert_id();
                
                if($aid) {
                    $this->db->insert("d_AQS_data", array('school_aqs_pref_start_date' => $start_date, 'school_aqs_pref_end_date' =>$end_date));
                    $aqs_ass_id = $this->db->get_last_insert_id();
                    $this->db->update("d_assessment",array('aqsdata_id'=>$aqs_ass_id),array("assessment_id"=>$aid));
                    
                }
                if($aid && !empty($facilitatorDataArray)) {
                          $facilitatorSql = "INSERT INTO h_facilitator_user (assessment_id,client_id,sub_role_id,user_id) VALUES ";
                          $i=0;
                          $facilitatorData = array();
                          foreach($facilitatorDataArray as $data) {
                             
                              $facilitatorSql .=  "(?,?,?,?),";
                              $facilitatorData[] = $aid;
                              $facilitatorData[] = $data['client_id'];
                              $facilitatorData[] = $data['role_id'];
                              $facilitatorData[] = $data['user_id'];
                              
                              
                              $i++;
                          }
                          $facilitatorSql = trim($facilitatorSql,",");
                          //echo $facilitatorSql;
                          //echo"<pre>";print_r($facilitatorData);
                    $this->db->query("$facilitatorSql", $facilitatorData);
                    //$aqs_ass_id = $this->db->get_last_insert_id();
                   // $this->db->update("d_assessment",array('aqsdata_id'=>$aqs_ass_id),array("assessment_id"=>$aid));
                    
                }

                
                if($aid && !empty($notificationSettingData)){
                    foreach($notificationSettingData as $key=>$val){
                        foreach($notificationTeam as $team) {
                        $notificationDataArray[] = array('assessment_id'=>$aid,
                            'notification_id'=>$val,
                            'user_id'=>$team,
                            'status'=>1,
                             'date'=>date('Y-m-d:h:i:s'));
                        }
                    }
                }
                
                //echo "<pre>"; print_r($notificationDataArray);
              /*  if($aid && !empty($notificationDataArray)) {
                          $notificationSql = "INSERT INTO h_user_review_notification (assessment_id,notification_id,user_id,status,date) VALUES ";
                          $i=0;
                          $notificationData = array();
                          foreach($notificationDataArray as $data) {
                             
                              $notificationSql .=  "(?,?,?,?,?),";
                              $notificationData[] =  $data['assessment_id'];
                              $notificationData[] = $data['notification_id'];
                              $notificationData[] = $data['user_id'];
                              $notificationData[] = $data['status'];
                              $notificationData[] = date("Y-m-d:h:i:s");
                              
                              
                              //$i++;
                          }
                          $notificationSql = trim($notificationSql,",");
                          //echo $notificationSql;
                          //echo"<pre>";print_r($facilitatorData);
                          $this->db->query("$notificationSql", $notificationData);
                          //$aqs_ass_id = $this->db->get_last_insert_id();
                         // $this->db->update("d_assessment",array('aqsdata_id'=>$aqs_ass_id),array("assessment_id"=>$aid));
                    
                }*/
                
                if(OFFLINE_STATUS==TRUE){    
                    //start---> save the history for insert school assessment data into d_assessment table on 10-03-2016 By Mohit Kumar
                    $action_assessment_json = json_encode(array(
                        'client_id' => $client_id,
                        'diagnostic_id' => $diagnostic_id,
                        'tier_id' => $tier_id,
                        'award_scheme_id' => $award_scheme_id,
                        'd_sub_assessment_type_id' => '2',
                        'create_date' => date("Y-m-d H:i:s")
                    ));
                    $this->db->saveHistoryData($aid, 'd_assessment', $uniqueID, 'createSchoolAssessment', $client_id, $client_id, $action_assessment_json, 0, date('Y-m-d H:i:s'));
                    //end---> save the history for insert school assessment data into d_assessment table on 10-03-2016 By Mohit Kumar
                }
                if ($this->db->insert("h_assessment_user", array("user_id" => $external_assessor_id, "role" => 4, "assessment_id" => $aid))) {
                if (OFFLINE_STATUS == TRUE) {
                    // get last insert id for external assessor on 01-042016 by Mohit Kumar
                    $euid = $this->db->get_last_insert_id();
                    //start---> save the history for insert external school assessment data into h_assessment_user table on 01-04-2016 By Mohit Kumar
                    $action_external_assessment_json = json_encode(array(
                        'user_id' => $external_assessor_id,
                        'role' => 4,
                        'assessment_id' => $aid
                    ));
                    $this->db->saveHistoryData($auid, 'h_assessment_user', $uniqueID, 'updateSchoolAssessmentExternal', $external_assessor_id, $aid, $action_external_assessment_json, 0, date('Y-m-d H:i:s'));
                    //end---> save the history for insert external school assessment data into h_assessment_user table on 01-04-2016 By Mohit Kumar
                }                                                           
            }
             if (!empty($ext_team))
                    foreach ($ext_team as $member_user_id => $roleClient) {

                        $roleClient = explode('_', $roleClient);

                        $externalClientId = $roleClient[0];

                        $roleId = $roleClient[1];

                        if (!$this->db->insert("h_assessment_external_team", array("assessment_id" => $aid, "user_role" => 4, "user_sub_role" => $roleId, "user_id" => $member_user_id))) {

                            return false;
                        } else {
                            if (OFFLINE_STATUS == TRUE) {
                                // get last insert id for external assessor team on 10-03-2016 by Mohit Kumar
                                $etuid = $this->db->get_last_insert_id();
                                //start---> save the history for insert external school assessment team data into h_assessment_user table on 10-03-2016 By Mohit Kumar
                                $action_external_assessment_team_json = json_encode(array(
                                    'user_role' => 4,
                                    'assessment_id' => $aid,
                                    'user_sub_role' => $roleId,
                                    'user_id' => $member_user_id
                                ));
                                $this->db->saveHistoryData($etuid, 'h_assessment_external_team', $uniqueID, 'updateSchoolAssessmentExternalTeam', $member_user_id, $aid, $action_external_assessment_team_json, 0, date('Y-m-d H:i:s'));
                            }
                        }
                    }

            if ($this->db->insert("h_assessment_user", array("user_id" => $internal_assessor_id, "role" => 3, "assessment_id" => $aid))) {
                    $auid = $this->db->get_last_insert_id();
                    if(OFFLINE_STATUS==TRUE){    
                        //start---> save the history for insert internal school assessment data into h_assessment_user table on 10-03-2016 By Mohit Kumar
                        $action_internal_assessment_json = json_encode(array(
                            'user_id' => $internal_assessor_id,
                            'role' => 3,
                            'assessment_id' => $aid
                        ));
                        $this->db->saveHistoryData($auid, 'h_assessment_user', $uniqueID, 'createSchoolAssessmentInternal', $internal_assessor_id, $aid,
                                $action_internal_assessment_json, 0, date('Y-m-d H:i:s'));
                        //end---> save the history for insert internal school assessment data into h_assessment_user table on 10-03-2016 By Mohit Kumar
                        
                        $this->db->addAlerts('d_assessment',$aid,$client_id,$aid,'CREATE_REVIEW');
                        $alertid = $this->db->get_last_insert_id();
                        $action_alert_json = json_encode(array(
                                'table_name' => 'd_assessment',
                                'content_id' => $aid,
                                'content_title' => $client_id,
                                'content_description' => $aid,
                                "type" => 'CREATE_REVIEW'
                        ));
                        $this->db->saveHistoryData($alertid, 'd_alerts', $uniqueID,'createSchoolAssessmentAlert', $aid, 
                                $aid, $action_alert_json, 0, date('Y-m-d H:i:s'));
                    }                   
                return $aid;
                } else {
                    $this->db->delete("d_assessment", array("assessment_id" => $aid));
                    $this->db->delete('z_history',array('table_name'=>'d_assessment','table_id'=>$aid,'action_unique_id' => $uniqueID,
                                            'action'=>'createSchoolAssessment','action_id' => $client_id));
                }
            }
            return false;
        }
        
        function getKPAratingsforAssessment($assessment_id,$role=4){
        	
        	$sql = "SELECT ks.id as score_id, k.kpa_name,k.kpa_id,kd.kpa_instance_id,r.rating,hls.rating_level_order as numericRating,r.rating_id,kd.`kpa_order` as kpa_no
			FROM `d_kpa` k
			inner join h_kpa_diagnostic kd on k.kpa_id=kd.kpa_id
			inner join d_assessment a on kd.diagnostic_id=a.diagnostic_id
			inner join h_assessment_user au on au.assessment_id=a.assessment_id
			left join `h_kpa_instance_score` ks on kd.kpa_instance_id=ks.kpa_instance_id and a.assessment_id=ks.assessment_id and ks.assessor_id=au.user_id
			left join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = a.diagnostic_id
            left join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and ks.d_rating_rating_id=hls.rating_id  and hls.rating_level_id=1
            left join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
			left join d_rating r on ks.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id      
			where a.assessment_id=? and au.role=?";
        	$sqlArgs=array($assessment_id,$role);	
        	$sql.=" order by kd.`kpa_order` asc;";
        	$res=$this->db->get_results($sql,$sqlArgs);
        	return $res?$res:array();
        }
        

        //function to create school review for upload aqs
        function uploadSchoolReviews($client_id,$internal_assessor_id,$diagnostic_id,$tier_id,$award_scheme_id,$aqs_round){
            
            ///$this->db->insert("d_assessment",array('client_id'=>$client_id, 'diagnostic_id'=>$diagnostic_id, 'tier_id'=>$tier_id, 'award_scheme_id'=>$award_scheme_id,'d_sub_assessment_type_id'=>'2', 'create_date'=>date("Y-m-d H:i:s")));
            //if($this->db->insert("h_assessment_user",array("user_id"=>$internal_assessor_id,"role"=>3,"assessment_id"=>$aid))){
            if($this->db->insert("d_assessment",array('client_id'=>$client_id, 'diagnostic_id'=>$diagnostic_id,'tier_id'=>$tier_id ,'award_scheme_id'=>$award_scheme_id,'aqs_round'=>$aqs_round,'d_sub_assessment_type_id'=>'2','create_date'=>date("Y-m-d H:i:s"))))
            $aid=$this->db->get_last_insert_id();
            $assUser = $this->db->insert("h_assessment_user",array("user_id"=>$internal_assessor_id,"role"=>3,"assessment_id"=>$aid));
            if($assUser && $aid) {
                return $aid;
            }else 
                return false;
        }
         //function to  upload aqs data
        function uploadReviewsAQS($finalAqsData,$assessment_id,$aqsid=0){
           
            //echo $finalAqsData['school_type_id'];
            $school_type_ids = (!empty($finalAqsData['school_type_id']))?explode(",",$finalAqsData['school_type_id']):array();
            $it_support = (!empty($finalAqsData['it_support']))?explode(",",$finalAqsData['it_support']):array();
            //print_r($it_support);
            $sql = '';
            if(count($school_type_ids)>=1) {
                $sql = "INSERT IGNORE INTO h_assessment_school_type VALUES ";
                foreach($school_type_ids as $id) {
                    $sql .= "('',$id,$assessment_id),";
                }
                $sql = rtrim($sql,",");
            }
            
            
            //h_assessment_school_type
            unset($finalAqsData['school_type_id']);
            unset($finalAqsData['it_support']);
            $finalAqsData['is_uploaded'] = 1;
            if($this->db->insert("d_AQS_data",$finalAqsData)) {
                $aqs_id = $this->db->get_last_insert_id();
                if($sql) {
                    $this->db->query($sql);
                }
                $this->db->update("d_assessment",array('aqsdata_id'=>$aqs_id),array("assessment_id"=>$assessment_id));
                if(count($it_support)>=1) {
                    $sqlSupport = "INSERT INTO h_aqsdata_itsupport VALUES ";
                    foreach($it_support as $id) {
                        $sqlSupport .= "('',$aqs_id,$id),";
                    }
                    $sqlSupport = rtrim($sqlSupport,",");
                    $this->db->query($sqlSupport);
                }
                return $aqs_id;
            }else 
                return false;
            
        }

        //function to get introductory assessment questions
        function getAssessorIntroductoryQuestions() {
            
                $sql = " SELECT q.rank,q.q_id,q.value_field,q.field_name,q.option_field,q.question,pq.question as parent_question,pq.q_id as parent_id ,GROUP_CONCAT(CONCAT_WS('-',o.question_option,o.o_id) SEPARATOR '/') as options "
                   . "FROM d_introductory_assessment_question q LEFT JOIN d_introductory_assessment_question pq ON q.parent_id = pq.q_id "
                   . "LEFT JOIN h_intro_assess_que_option qo ON q.q_id = qo.q_id LEFT JOIN d_intro_assess_que_option o ON qo.o_id = o.o_id"
                   . " GROUP BY q.q_id ";
            $res=$this->db->get_results($sql);
            return $res?$res:array();
        }
         //function to get student assessment form attributes
        function getStudentFormAttributes($student_id,$assessment_id) {
            
            //$sql = " SELECT field_id,field_name,field_type,field_label,visibility,class,value_type FROM d_student_review_form ORDER BY rank asc  ";
            $sql = "SELECT at.parent,at.field_id,at.field_name,at.field_type,at.field_label,at.visibility,at.class,at.value_type,d.value FROM d_student_review_form_attributes at"
                    . " INNER JOIN h_student_review_form_attributes f ON f.attribute_id = at.field_id "
                    . " LEFT JOIN (SELECT * from d_student_data WHERE student_id = ? AND assessment_id = ? )  d ON d.attr_id = at.field_id"
                    . " WHERE f.form_id = 1 ORDER BY at.rank,field_id asc";
            $res=$this->db->get_results($sql,array($student_id,$assessment_id));
            return $res?$res:array();
        }
        
        //function to insert student profile data 
        function insertStudentProfile($data,$student_id,$assessment_id){
            
            $sql = "SELECT student_data_id FROM d_student_data WHERE student_id = ? AND assessment_id = ? AND attr_id = ?";
            $res=$this->db->get_results($sql,array($student_id,$assessment_id,49));
           // if(empty($res)) {
               // print_r($data);die;
            $this->db->delete("d_student_data",array('student_id'=>$student_id,'assessment_id'=>$assessment_id));
            
            $data = array_map('trim', $data);
            $sql = "INSERT INTO d_student_data (`student_id`,`attr_id`,`assessment_id`,`value`) VALUES ";
            $inner_array=array();
            foreach($data as $key=>$val) {
              //$sql .= "(".  $student_id.",".$key.",".$assessment_id.","."'$val'"."),";
                $sql .= "("."?,?,?,?"."),";
                $inner_array[] = $student_id;
                $inner_array[] = $key;
                $inner_array[] = $assessment_id;
                $inner_array[] = $val;
            }
            $sql = trim($sql,",");
            if($this->db->query($sql,$inner_array))
               $aid=$this->db->get_last_insert_id();
               if( $aid) {
                   return $aid;
               }else 
                   return false;
            
        }
        
       // function to get assessment kpa
        //function to insert student profile data 
        function getAssessmentKpa($assessment_id){
            
            $sql = "SELECT user_id,kpa_instance_id as kpa_id FROM d_assessment_kpa WHERE assessment_id = ? ";
           return $this->db->get_results($sql,array($assessment_id));
        }
        //function to insert assessment kpa's 
        function addAssessmentKpa($data,$assessment_id){
            
            
           // $data = array_map('trim', $data);
            $sql = "INSERT INTO d_assessment_kpa (`assessment_id`,`user_id`,`kpa_instance_id`) VALUES ";
            $param = array();
            $inner_array=array();
            foreach($data as $key=>$val) {
              //$sql .= "(".  $student_id.",".$key.",".$assessment_id.","."'$val'"."),";
                foreach($val as $user=>$kpa){
                $sql .= "("."?,?,?"."),";
                $param[] = $assessment_id;
                $param[] = $key;
                $param[] = $kpa;
                }
            }
            //print_r($param);
             $sql = trim($sql,",");
            if($this->db->query($sql,$param))
               $aid=$this->db->get_last_insert_id();
               if( $aid) {
                   return $aid;
               }else 
                   return false;
            
        }
        
        //function to edit assessment kpa's 
        function editAssessmentKpa($data,$assessment_id){
            
            
           // $data = array_map('trim', $data);
            $this->db->delete("d_assessment_kpa",array('assessment_id'=>$assessment_id));
            $sql = "INSERT INTO d_assessment_kpa (`assessment_id`,`user_id`,`kpa_instance_id`) VALUES ";
            $param = array();
            $inner_array=array();
            foreach($data as $key=>$val) {
              //$sql .= "(".  $student_id.",".$key.",".$assessment_id.","."'$val'"."),";
                foreach($val as $user=>$kpa){
                $sql .= "("."?,?,?"."),";
                $param[] = $assessment_id;
                $param[] = $key;
                $param[] = $kpa;
                }
            }
           // print_r($param);
             $sql = trim($sql,",");
            if($this->db->query($sql,$param))
               $aid=$this->db->get_last_insert_id();
               if( $aid) {
                   return $aid;
               }else 
                   return false;
            
        }
        
        function prepareValues($data,$parent = 0) {
            
            $finalValues = array();
            foreach($data as $key=>$values) {
                //if($values['field_id'] = )
                
                $finalValues[$values['field_id']] = $values;
                if($values['parent']>=1) {
                    
                    if(isset($finalValues[$values['parent']]['child'])) {
                        array_push($finalValues[$values['parent']]['child'],$values);
//                        /echo $values['field_id'];
                        unset($finalValues[$values['field_id']]);
                        //$finalValues[$values['parent']]['child'][] = $values;
                    }else {
                        $finalValues[$values['parent']]['child'][] = $values;
                        unset($finalValues[$values['field_id']]);
                    }
                }
            }
            return $finalValues;
        }
        
        function createValidationFields($studentProfileAttributes,$postData) {
            
            $finalValues = array();
            foreach($postData as $values) {
                $finalValues[$values['field_id']] = $values;
            }
            return $finalValues;
        }
        
         public function studentProfileData($post,$student_id,$assessment_id) {
        
             //print_r($post);
             $saveData= array();
             $studentProfileAttributes = $this->getStudentFormAttributes($student_id,$assessment_id);
             //echo '<pre>';print_r($studentProfileAttributes);
             $studentProfileAttributes = $this->prepareValues($studentProfileAttributes,0);
             foreach($studentProfileAttributes as $key=>$data) {
                 //if(isset($studentProfileAttributes[$key])) {
                    //$data = $studentProfileAttributes[$key];
                      //echo $key;print_r($data);
                    if($data['field_id'] != DEFAULT_STUDENT_PROFILE_ATTRIBUTE) {
                        if(isset($data['child']) ) {                    

                            //$profileField[$key]= array("type" => "string", "isRequired" => 1, "name" => $data['field_label']);
                             if(isset($post[$key]) && $post[$key] == 1) {
                                  //$profileField[$key]= array("type" => "string", "isRequired" => 1, "name" => $data['field_label']);
                                 $saveData[$key] = $post[$key];
                                foreach($data['child'] as $child) {

                                    $saveData[$child['field_id']] = isset($post[$child['field_id']])?$post[$child['field_id']]:'';
                                    //$profileField[$child['field_id']]= array("type" => "string", "isRequired" => 1, "name" => $child['field_label']);
                                }
                             }else
                                $saveData[$key] = isset($post[$key])?$post[$key]:'';
                        }else {
                            //$profileField[$key]= array("type" => "string", "isRequired" => 1, "name" => $data['field_label']);
                            $saveData[$key] = isset($post[$key])?$post[$key]:'';
                        }
                    }
                 
             }
             //print_r($saveData);die;
             return $saveData;
         }
        //validate student profile data
         public function studentProfileValidation($post,$student_id,$assessment_id) {
             $array_not_mandatory=array("1","2","3","8","9","10","11","12","13","14","16","21","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","46","47");                   
             //print_r($post);
             $profileField = array();
             $studentProfileAttributes = $this->getStudentFormAttributes($student_id,$assessment_id);
             //echo '<pre>';print_r($studentProfileAttributes);
             $studentProfileAttributes = $this->prepareValues($studentProfileAttributes,0);
             foreach($studentProfileAttributes as $key=>$data) {
                 //if(in_array($data['field_id'],$array_not_mandatory)) continue;
                 //if(isset($studentProfileAttributes[$key])) {
                    //$data = $studentProfileAttributes[$key];
                      //echo $key;print_r($data);
                    if($data['field_id'] != DEFAULT_STUDENT_PROFILE_ATTRIBUTE) {
                        if(isset($data['child']) ) {                    

                             $profileField[$key]= array("type" => "string", "isRequired" => 1, "name" => $data['field_label']);
                             if(isset($post[$key]) && $post[$key] == 1) {
                                  //$profileField[$key]= array("type" => "string", "isRequired" => 1, "name" => $data['field_label']);
                                foreach($data['child'] as $child) {


                                    $profileField[$child['field_id']]= array("type" => "string", "isRequired" => 1, "name" => $child['field_label']);
                                }
                             }
                        }else {
                            $profileField[$key]= array("type" => "string", "isRequired" => 1, "name" => $data['field_label']);
                        }
                    }
                  
                 
             }
            // $validationFields = $this->createValidationFields($studentProfileAttributes,$post);
            //echo "<pre>"; print_r($profileField);die;
             $val = 1;
            
        //print_r($post);die;

        $errors = array();
        $values = array();

        foreach ($profileField as $key => $value) {
            
             
            
              
            if ($value["isRequired"] && (!isset($post[$key]) || empty(trim($post[$key])) ) && !in_array($key,$array_not_mandatory)) {
                $errors[$key] = "Field is required: '" . $value['name'] . "'";
            } else if (empty($val)) {
                $values[$key] = ($value["type"] == "int") ? 0 : '';
            } else {
                
                $values[$key] = isset($post[$key])?$post[$key]:'';
            }
                            

        }
        $values[49] = 1;
        return array("errors" => $errors, "values" => $values);
    }
    
    //function to get feedback assessment
        function getFeedbackAssessment($id) {
            
                    $sql = " SELECT u.name,u.email,u.user_id,au.assessment_id FROM d_user u 
                    INNER JOIN (SELECT r.sub_role_name ,et.user_id,et.assessment_id FROM `h_assessment_external_team` et 
                    INNER JOIN d_user_sub_role r ON et.user_sub_role = r.sub_role_id AND et.user_role!=3                     
                    UNION SELECT r.role_name ,et.user_id ,et.assessment_id
                    FROM `h_assessment_user` et INNER JOIN d_user_role r ON et.role = r.role_id 
                    AND et.role!=3 AND et.isFilled!=1) au ON au.user_id = u.user_id ORDER by au.assessment_id";
            
                /*$sql = "SELECT r.sub_role_name ,et.user_id FROM `h_assessment_external_team` et 
                    INNER JOIN d_user_sub_role r ON et.user_sub_role = r.sub_role_id                     
                    UNION SELECT r.role_name ,et.user_id 
                    FROM `h_assessment_user` et INNER JOIN d_user_role r ON et.role = r.role_id ";*/
                    
            $res=$this->db->get_results($sql);
            return $res?$res:array();
        }
    //function to get  assessment kpa's
        function getSchoolAssessmentKpas($aid,$lang_id=9) {

            $params = array($aid,$lang_id);
            $sql = " SELECT e.translation_text as kpa_name,kd.kpa_instance_id as kpa_id FROM d_assessment a "
                   . "INNER JOIN h_kpa_diagnostic kd ON a.diagnostic_id = kd.diagnostic_id "
                   . "INNER JOIN d_kpa k on k.kpa_id = kd.kpa_id "
                   . "INNER JOIN h_lang_translation e ON e.equivalence_id = k.equivalence_id"
                   . " WHERE a.assessment_id=? AND e.language_id = ? ORDER BY kd.kpa_order ";
            
                
            $res=$this->db->get_results($sql,$params);
            return $res?$res:array();
        }
        
        //function to get all notifications
        
        function getNotifications() {
            
            $res=$this->db->get_results("SELECT id,notification_name,notification_label FROM d_notifications WHERE status = 1 ORDER BY notification_name ASC;");
            return $res?$res:array();
        }        
        //function to get all review notifications
        
        function getReviewNotifications($type = 0) {
            
            /*$res=$this->db->get_results("SELECT id,notification_name,notification_label FROM d_notifications "
                    . "WHERE status = 1 AND notification_type='$type' ORDER BY notification_name ASC;");*/
            $params = array(1);
            $sqlCond = '';
            if($type) {
                $sqlCond = 'AND nt.id = ?';
                $params[] = $type;
            }
             $res=$this->db->get_results("SELECT n.id,n.notification_name,n.notification_label,nt.id as type FROM d_notifications n "
                     . " INNER JOIN h_notification_type t ON t.notification_id = n.id "
                     . " INNER JOIN d_notification_type nt ON nt.id = t.notification_type_id "
                     . " WHERE n.status = ? $sqlCond  ORDER BY n.notification_name ASC ",$params);
            return $res?$res:array();
        }
        //function to get all users with notifications
        
        function getNotificationUsers($assessment_id,$type=1) {
            
            $res=$this->db->get_results("SELECT un.sub_role_name,un.sub_role_id,un.user_id,un.assessment_id,un.name,n.notification_id,n.type FROM h_user_review_notification n 
                    RIGHT JOIN (SELECT r.sub_role_name,r.sub_role_id ,et.user_id,et.assessment_id,us.name FROM `h_assessment_external_team` et 
                    INNER JOIN d_user_sub_role r ON et.user_sub_role = r.sub_role_id
                     INNER JOIN d_user us ON us.user_id = et.user_id WHERE et.assessment_id= ?                     
                    UNION SELECT 'Lead' as sub_role_name ,1 as sub_role_id ,et.user_id ,et.assessment_id,us.name
                    FROM `h_assessment_user` et INNER JOIN d_user_role r ON et.role = r.role_id 
                    INNER JOIN d_user us ON us.user_id = et.user_id WHERE et.assessment_id= ? AND et.role=4
                    ) un ON un.assessment_id = n.assessment_id  AND un.user_id = n.user_id  WHERE n.type = ?",array($assessment_id,$assessment_id,$type));
            return $res?$res:array();
        }
        //function to create notifications queue
        
        function createNotificationQueue($notifications,$edate='') {
            
                if(!empty($edate)) {
                    $edate = date("Y-m-d",strtotime($edate));
                }
              $sql = "INSERT INTO d_notification_queue (notification_id,user_id,assessment_id,status,type,date) VALUES ";
              $values = array();
              $delQueryCond = '';
             // echo "<pre>";print_r($notifications);die;
              foreach($notifications as $data) {
                  
                  $this->db->delete("d_notification_queue",array("assessment_id"=>$data['assessment_id'],'type'=>$data['type'],'user_id'=>$data['user_id'],'notification_id'=>$data['notification_id']));
                  if($data['sub_role_name'] != 'Observer' && !empty($data['notification_id'])) {
                    // $values .= "(".$data['notification_id'].",".$data['user_id'].",".$data['assessment_id'].",1"."),";
                      $sql .= '(?,?,?,?,?,?),';
                      $values[] =   $data['notification_id'];
                      $values[] =   $data['user_id'];
                      $values[] =   $data['assessment_id'];
                      $values[] =   1;
                      $values[] =   $data['type'];
                      $values[] =   $edate;
                  }else if ($data['sub_role_name'] == 'Observer'){
                      
                    $this->db->delete("d_notification_queue",array("assessment_id"=>$data['assessment_id'],'type'=>$data['type'],'user_id'=>$data['user_id'],'notification_id'=>'0'));
                 
                       $sql .= '(?,?,?,?,?,?),';
                      //$values .= "("."0,".$data['user_id'].",".$data['assessment_id'].",1"."),";
                      $values[] =   0;
                      $values[] =   $data['user_id'];
                      $values[] =   $data['assessment_id'];
                      $values[] =   1;
                      $values[] =   $data['type'];
                      $values[] =   $edate;
                  }
                  else if($data['sub_role_name'] != 'Observer' && empty($data['notification_id'])) {
                     // $values .= "("."0,".$data['user_id'].",".$data['assessment_id'].",1"."),";
                      $sql .= '(?,?,?,?,?,?),';
                      $values[] =   0;
                      $values[] =   $data['user_id'];
                      $values[] =   $data['assessment_id'];
                      $values[] =   1;
                      $values[] =   $data['type'];
                      $values[] =   $edate;
                  }
              }
              if(!empty($values)){
                $sql = trim($sql,",");
               // $sql .= $values;   
               // echo "<pre>";print_r($values);die;
                return $this->db->query($sql,$values);
              }
                else
                    return false;
        }
        //function to get all users of a review with notifications
        
        function getAssessmentUsers($assessment_id,$type=0) {
            
            $sqlCond = '';
            if($type)
                $sqlCond = 'AND et.role=4';
            
            $res=$this->db->get_results("SELECT r.sub_role_name,r.sub_role_order,et.user_id,et.assessment_id,us.name FROM `h_assessment_external_team` et 
                    INNER JOIN d_user_sub_role r ON et.user_sub_role = r.sub_role_id
                    INNER JOIN d_user us ON us.user_id = et.user_id WHERE et.assessment_id= $assessment_id                     
                    UNION SELECT 'Lead / Sr. Associate' as role_name,1 as sub_role_order ,et.user_id ,et.assessment_id,us.name
                    FROM `h_assessment_user` et INNER JOIN d_user_role r ON et.role = r.role_id 
                    INNER JOIN d_user us ON us.user_id = et.user_id WHERE et.assessment_id= $assessment_id $sqlCond  order by sub_role_order
                    ");
            return $res?$res:array();
        }
        //function to get all users of a review with notifications
        
        function getReviewNotificationUsers($assessment_id) {
            
            $res=$this->db->get_results("SELECT user_id,assessment_id,notification_id,type FROM h_user_review_notification 
                WHERE assessment_id=?",array($assessment_id));
            return $res?$res:array();
        }
        //function to get all users who had send reimbursement Sheet
        
        function getReviewReimSheetUsers($assessment_id) {
            
            $res=$this->db->get_results("SELECT user_id,sheet_status FROM h_user_review_reim_sheet_status 
                WHERE assessment_id=?",array($assessment_id));
            return $res?$res:array();
        }

        function ExternalAssessorsGrouped($client_id,$group_ass_id){        
            
            $sql="select a.* from (select * from d_group_assessment where group_assessment_id=?) a 
              inner join h_assessment_ass_group b on a.group_assessment_id=b.group_assessment_id
              inner join d_assessment c on b.assessment_id=c.assessment_id
              inner join h_assessment_user d on c.assessment_id=d.assessment_id && d.role=4
              inner join d_user e on d.user_id=e.user_id        where e.client_id=?     ";
            $res = $this->db->get_results($sql, array($group_ass_id,$client_id));    
            return $res?$res:array();
            
        }
        function getAssessmentRatingStatus($assessment_id){        
            
            $sql="select isFilled FROM h_assessment_user where assessment_id=? AND role = ?     ";
            $res = $this->db->get_row($sql, array($assessment_id,4));    
            return $res?$res['isFilled']:array();
            
        }
        function getAssessmentRatingPercentage($assessment_id){        
            
            $sql="select collaborativepercntg FROM d_assessment where assessment_id=? ";
            $res = $this->db->get_row($sql, array($assessment_id));    
            return $res?$res['collaborativepercntg']:array();
            
        }
        function getAssessmentRatingKpa($assessment_id){        
            
            $sql="select count(kpa_instance_id) as kpa FROM d_assessment_kpa where assessment_id=? ";
            $res = $this->db->get_row($sql, array($assessment_id));    
            return $res?$res['kpa']:array();
            
        }
        function deleteOldKpaRating($teamKpas,$assessment_id){        
            
            //print_r($kpas);die;
            $newKpas = array();
            $newKqIds = array();
            $newCqIds = array();
            $newJsIds = array();
            //get previous assigned kpas
            foreach($teamKpas as $key=>$data){
                $sql="SELECT kpa_instance_id FROM d_assessment_kpa WHERE assessment_id=? AND user_id = ?     ";
                $res = $this->db->get_results($sql, array($assessment_id,$key)); 
                $res = array_column($res,'kpa_instance_id');
                $newKpas = $newKpas+array_values(array_merge(array_diff($res, $data), array_diff($data, $res)));
               
            }
           // print_r($newKpas);die;
            //get key question instance id
            if(!empty($newKpas)) {
                $sql="SELECT kqs.key_question_instance_id FROM h_kpa_kq  kq INNER JOIN  h_kq_instance_score kqs"
                        . " ON kq.key_question_instance_id =  kqs.key_question_instance_id "
                        . " WHERE kqs.assessment_id = ? AND kq.kpa_instance_id  IN (".implode(",",$newKpas).")    ";
                $newKqIds = $this->db->get_results($sql, array($assessment_id)); 
                $newKqIds = array_column($newKqIds,'key_question_instance_id');
            }
           
            //get core question instance id
            if(!empty($newKqIds)) {
                $sql="SELECT cqs.core_question_instance_id FROM h_kq_cq  kq INNER JOIN  h_cq_score cqs"
                        . " ON kq.core_question_instance_id =  cqs.core_question_instance_id "
                        . " WHERE cqs.assessment_id = ? AND kq.key_question_instance_id  IN (".implode(",",$newKqIds).")    ";
                $newCqIds = $this->db->get_results($sql, array($assessment_id));               
                $newCqIds = array_column($newCqIds,'core_question_instance_id');
            }
            //get judgement statement instance id
            if(!empty($newCqIds)) {
                $sql="SELECT cqs.judgement_statement_instance_id FROM h_cq_js_instance  kq INNER JOIN  f_score cqs"
                        . " ON kq.judgement_statement_instance_id =  cqs.judgement_statement_instance_id "
                        . " WHERE cqs.assessment_id = ? AND kq.core_question_instance_id  IN (".implode(",",$newCqIds).")    ";
                $newJsIds = $this->db->get_results($sql, array($assessment_id)); 
                $newJsIds = array_column($newJsIds,'judgement_statement_instance_id');
            }
            
            $params = array($assessment_id);
            $delKeyNotesSql = '';
           // print_r($newJsIds);die;
                            
            if(!empty($newKpas)){    
                
                $keyNotesIds = array();
                $keyNotesSql="SELECT id  FROM assessor_key_notes where kpa_instance_id  IN (".implode(",",$newKpas).") AND  assessment_id=? ;    ";
                $keyNotesIds = $this->db->get_results($keyNotesSql, array($assessment_id)); 
                $keyNotesIds = array_column($keyNotesIds,'id');
                if(!empty($keyNotesIds)){
                    $asscKeyNotesSql ="delete FROM h_assessor_key_notes_js where assessor_key_notes_id  IN (".implode(",",$keyNotesIds).");";
                    $res = $this->db->query($asscKeyNotesSql);
                }
                //echo "<pre>";print_r($keyNotesIds);die;
                $delKeyNotesSql .="delete FROM assessor_key_notes where kpa_instance_id  IN (".implode(",",$newKpas).") AND  assessment_id=? ;";
                $delKeyNotesSql .="delete FROM h_kpa_instance_score where kpa_instance_id  IN (".implode(",",$newKpas).") AND  assessment_id=? ;";
                //$delKeyNotesSql .="delete FROM h_kq_instance_score where  key_question_instance_id  IN (".implode(",",$newKqIds).") AND  assessment_id=? ;";
                //$delKeyNotesSql .="delete FROM h_cq_score where core_question_instance_id  IN (".implode(",",$newCqIds).") AND  assessment_id=? ;";
                //$delKeyNotesSql .="delete FROM f_score where judgement_statement_instance_id  IN (".implode(",",$newJsIds).") AND  assessment_id=? ;";
                 array_push($params,$assessment_id);
            }
            if(!empty($newKqIds)) {
                 $delKeyNotesSql .="delete FROM h_kq_instance_score where  key_question_instance_id  IN (".implode(",",$newKqIds).") AND  assessment_id=? ;";
                  array_push($params,$assessment_id);
            }
            if(!empty($newCqIds)){
                $delKeyNotesSql .="delete FROM h_cq_score where core_question_instance_id  IN (".implode(",",$newCqIds).") AND  assessment_id=? ;";
                //return $res?$res['isFilled']:array();
                 array_push($params,$assessment_id);
            }
            if(!empty($newJsIds)){
                $delKeyNotesSql .="delete FROM f_score where judgement_statement_instance_id  IN (".implode(",",$newJsIds).") AND  assessment_id=? ;";
                array_push($params,$assessment_id);
            }
            //echo $delKeyNotesSql;
            if(!empty($delKeyNotesSql))
                $res = $this->db->query($delKeyNotesSql, $params); 
            //echo "kkk";
            return true;
        }
        function deleteOldKQRating($key,$kpas){        
            
            $sql="delete FROM h_kq_instance_score where assessor_id = ? AND assessment_id=? AND kpa_instance_id NOT IN ( ".implode(",",$kpas).")";
            $res = $this->db->query($sql, array($assessment_id));    
            return $res?$res['isFilled']:array();
            
        }
        function deleteOldCQRating($key,$kpas){        
            
            $sql="delete FROM h_cq_score where assessor_id = ? AND assessment_id=? AND kpa_instance_id NOT IN ( ".implode(",",$kpas).")";
            $res = $this->db->query($sql, array($assessment_id));    
            return $res?$res['isFilled']:array();
            
        }
        function deleteOldJSRating($key,$kpas){     
            
            
            
            $sql="delete FROM h_kq_instance_score where assessor_id = ? AND assessment_id=? AND kpa_instance_id NOT IN ( ".implode(",",$kpas).")";
            $res = $this->db->query($sql, array($assessment_id));    
            return $res?$res['isFilled']:array();
            
        }
}