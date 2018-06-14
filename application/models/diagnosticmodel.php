<?php
class diagnosticModel extends Model{
	
	/*function getDiagnostics($args=array()){
		$args=$this->parse_arg($args,array("assessment_type_id"=>0,"name_like"=>"","isPublished"=>"","order_by"=>"name","order_type"=>"asc"));
		$order_by=array("name"=>"d.name","isPublished"=>"d.isPublished","date_created"=>"d.date_created","date_published"=>"d.date_published","assessment_type"=>"t.assessment_type_name");
		$sqlArgs=array();
		
		$sql="select d.*,t.assessment_type_name 
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
			where 1=1 ";
			
		if($args['assessment_type_id']>0){
			$sql.="and d.assessment_type_id=? ";
			$sqlArgs[]=$args['assessment_type_id'];
		}
		if($args['name_like']!=""){
			$sql.="and d.name like ? ";
			$sqlArgs[]="%".$args['name_like']."%";
		}
		if($args['isPublished']!=""){
			$sql.="and d.isPublished=".($args['isPublished']=="yes"?"1 ":"0 ");
		}
		$sql.="order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"d.name").($args["order_type"]=="desc"?" desc ":" asc ");
		
		$res=$this->db->get_results($sql,$sqlArgs);
		return $res?$res:array();
	}*/
	function getAllDiagnostics($args=array()){
		$args=$this->parse_arg($args,array("assessment_type_id"=>0,"name_like"=>"","isPublished"=>"","order_by"=>"name","order_type"=>"asc"));
		$order_by=array("name"=>"d.name","isPublished"=>"d.isPublished","date_created"=>"d.date_created","date_published"=>"d.date_published","assessment_type"=>"t.assessment_type_name");
		$sqlArgs=array();
		
		$sql="select d.*,t.assessment_type_name 
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
			where 1=1 ";
			
		if($args['assessment_type_id']>0){
			$sql.="and d.assessment_type_id=? ";
			$sqlArgs[]=$args['assessment_type_id'];
		}
		if($args['name_like']!=""){
			$sql.="and d.name like ? ";
			$sqlArgs[]="%".$args['name_like']."%";
		}
		if($args['isPublished']!=""){
			$sql.="and d.isPublished=".($args['isPublished']=="yes"?"1 ":"0 ");
		}
		 $sql.="order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"d.name").($args["order_type"]=="desc"?" desc ":" asc ");
		
		$res=$this->db->get_results($sql,$sqlArgs);
                //echo "<pre>";print_r($res);
		return $res?$res:array();
	}
    function getDiagnostics($args=array(),$lang_id=0,$isDiagnosticParent=0){
		$args=$this->parse_arg($args,array("assessment_type_id"=>0,"name_like"=>"","isPublished"=>"","order_by"=>"name","order_type"=>"asc"));
		$order_by=array("name"=>"hlt.translation_text","isPublished"=>"d.isPublished","date_created"=>"d.date_created","date_published"=>"d.date_published","assessment_type"=>"t.assessment_type_name","language_id"=>"dl.language_id");
		$sqlArgs=array();
		$lang = '';
                $whrCond = '';
                $langCond = '';
                if(!empty($lang_id)) {
                    
                    $lang = $lang_id;
                }else {
                    $lang = $this->lang;
                }
                //echo"a". $this->lang;
                if($lang !='all') {
                    $langCond  = "  and hlt.language_id=$lang ";
                }else if($isDiagnosticParent) {
                     $whrCond .= " and parent_lang_translation_id is null";
                }

		$sql="select d.diagnostic_id,hlt.translation_text as name,d.diagnostic_image_id,hltd.isPublished,hltd.date_created,hltd.date_published,hltd.publish_user_id,d.assessment_type_id,d.kpa_recommendations,d.kq_recommendations,d.cq_recommendations,d.js_recommendations,t.assessment_type_name ,hlt.language_id,dl.language_name
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
			inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id
                        inner join h_lang_trans_diagnostics_details hltd on  hltd.lang_translation_id= hlt.lang_translation_id                      
			inner join d_language dl on dl.language_id=hlt.language_id
                        where 1=1  $whrCond  $langCond and hlt.isActive=1 && hlt.translation_type_id=7 ";
			
		if($args['assessment_type_id']>0){
			$sql.="and d.assessment_type_id=? ";
			$sqlArgs[]=$args['assessment_type_id'];
		}
		if($args['name_like']!=""){
			$sql.="and hlt.translation_text like ? ";
			$sqlArgs[]="%".$args['name_like']."%";
		}
		if($args['isPublished']!=""){

			$sql.="and hltd.isPublished=".($args['isPublished']=="yes"?"1 ":"0 ");
		}
                 $sql.="order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"hlt.translation_text").($args["order_type"]=="desc"?" desc ":" asc ");
		
		$res=$this->db->get_results($sql,$sqlArgs);
		return $res?$res:array();
	}

        
        function getAllDiagnosticsList(){
            $sql="select hlt.lang_translation_id,hlt.equivalence_id,d.diagnostic_id,hlt.translation_text as name,d.diagnostic_image_id,hltd.isPublished,hltd.date_created,hltd.date_published,hltd.publish_user_id,d.assessment_type_id,d.kpa_recommendations,d.kq_recommendations,d.cq_recommendations,d.js_recommendations,t.assessment_type_name ,dl.language_code
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
			inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
                        inner join d_language dl on dl.language_id=hlt.language_id
                        inner join h_lang_trans_diagnostics_details hltd on  hltd.lang_translation_id= hlt.lang_translation_id
			where 1=1 and hlt.translation_type_id=7 and hlt.parent_lang_translation_id IS NULL and d.isPublished=1 order by language_code";
            $res=$this->db->get_results($sql);
            return $res?$res:array();
            
        }
        
        function getDiagnostic($diagnostic_id=0){		
		$sql="select d.diagnostic_id,d.equivalence_id,hlt.lang_translation_id,hlt.translation_text as name,d.diagnostic_image_id,hltd.isPublished,hltd.date_created,hltd.date_published,hltd.publish_user_id,d.assessment_type_id,d.kpa_recommendations,d.kq_recommendations,d.cq_recommendations,d.js_recommendations,t.assessment_type_name,hlt.parent_lang_translation_id
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
			inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
                        inner join h_lang_trans_diagnostics_details hltd on  hltd.lang_translation_id= hlt.lang_translation_id
			where diagnostic_id=? and hlt.translation_type_id=7 and hlt.language_id={$this->lang} ";
                        
		return $this->db->get_row($sql,array($diagnostic_id));
	}
        
        function getDiagnosticBYLang($diagnostic_id=0,$lang_id=9){		
		$sql="select d.diagnostic_id,d.equivalence_id,hlt.lang_translation_id,hlt.translation_text as name,d.diagnostic_image_id,hltd.isPublished,hltd.date_created,hltd.date_published,hltd.publish_user_id,d.assessment_type_id,d.kpa_recommendations,d.kq_recommendations,d.cq_recommendations,d.js_recommendations,t.assessment_type_name,d.assessment_type_id ,hlt.parent_lang_translation_id,dl.language_name 
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
			inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
                        inner join h_lang_trans_diagnostics_details hltd on  hltd.lang_translation_id= hlt.lang_translation_id
                        left join d_language dl on dl.language_id=hlt.language_id
			where diagnostic_id=? and hlt.translation_type_id=7 and hlt.language_id=? ";
		return $this->db->get_row($sql,array($diagnostic_id,$lang_id));
	}
        
        function getDiagnosticBYEqui($equivalence_id=0,$lang_id=9){		
		$sql="select d.diagnostic_id,d.equivalence_id,hlt.lang_translation_id,hlt.translation_text as name,d.diagnostic_image_id,hltd.isPublished,hltd.date_created,hltd.date_published,hltd.publish_user_id,d.assessment_type_id,d.kpa_recommendations,d.kq_recommendations,d.cq_recommendations,d.js_recommendations,t.assessment_type_name,d.assessment_type_id ,hlt.parent_lang_translation_id,dl.language_name 
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
			inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
                        inner join h_lang_trans_diagnostics_details hltd on  hltd.lang_translation_id= hlt.lang_translation_id
                        left join d_language dl on dl.language_id=hlt.language_id
			where d.equivalence_id=? and hlt.translation_type_id=7 and hlt.language_id=? ";
		return $this->db->get_row($sql,array($equivalence_id,$lang_id));
	}
        
        function getDiagnosticById($diagnostic_id=0){		
		$sql="select d.diagnostic_id,d.equivalence_id,hlt.lang_translation_id,hlt.translation_text as name,d.diagnostic_image_id,hltd.isPublished,hltd.date_created,hltd.date_published,hltd.publish_user_id,d.assessment_type_id,d.kpa_recommendations,d.kq_recommendations,d.cq_recommendations,d.js_recommendations,t.assessment_type_name,d.assessment_type_id 
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
			left join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
                        inner join h_lang_trans_diagnostics_details hltd on  hltd.lang_translation_id= hlt.lang_translation_id
			where diagnostic_id=?";
		return $this->db->get_row($sql,array($diagnostic_id));
	}
        
        function getLanguageId() {
            
            return $this->lang;
        }
        function getDiagnosticLanguages($diagnostic_id=0){		
		$sql="select d.diagnostic_id,hlt.language_id,lan.language_words ,lan.language_code
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
			inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 		
			inner join d_language lan on lan.language_id = hlt.language_id 	
                        inner join h_lang_trans_diagnostics_details hltd on  hltd.lang_translation_id= hlt.lang_translation_id 
			where diagnostic_id=? AND  hltd.isPublished = 1 ";
		return $this->db->get_results($sql,array($diagnostic_id));
	}
        function getDiagnosticLabels($diagnostic_id=0,$language_id=0){	
                
		 $sql="select d.label_name,d.label_key,a.label_text
			from d_assessment_labels d 
			inner join h_assessment_labels a on d.id=a.label_id				
			where a.language_id=?  ";
		return $this->db->get_results($sql,array($language_id));
	}
        function getLanguages($lang_translation_id,$lang_code){
            $len = count($lang_code);
            $lang_code[]=$lang_translation_id;
            $sql="select * from d_language
			where (language_code in (?" . str_repeat(",?", $len - 1) . ")) && (language_id NOT IN (SELECT language_id from h_lang_translation where equivalence_id IN (select equivalence_id from h_lang_translation where lang_translation_id=?)))";
            
            $res=$this->db->get_results($sql,$lang_code);
            return $res?$res:array();
            
        }
        
        function getLanguagesfrombase($lang_translation_id,$lang_code){
            $len = count($lang_code);
            $lang_code[]=$lang_translation_id;
            $sql="select * from d_language
			where (language_code in (?" . str_repeat(",?", $len - 1) . ")) && (language_id IN (SELECT language_id  from h_lang_translation a inner join h_lang_trans_diagnostics_details b on a.lang_translation_id=b.lang_translation_id where  b.isPublished=1 &&  a.equivalence_id=?) )";
            
            $res=$this->db->get_results($sql,$lang_code);
            return $res?$res:array();
            
        }
	
        function getDiagnosticDetailsfromTransLang($trans_lang_id){
            $sql="select b.diagnostic_id,b.assessment_type_id,b.equivalence_id,c.teacher_cat_id,a.lang_translation_id,a.language_id,a.translation_text,b.kpa_recommendations,b.kq_recommendations,b.cq_recommendations,b.js_recommendations from h_lang_translation a "
                    . "inner join d_diagnostic b on a.equivalence_id=b.equivalence_id "
                    . "left join h_diagnostic_teacher_cat c on b.diagnostic_id=c.diagnostic_id "
                    . "where lang_translation_id=?";
        
            $res=$this->db->get_row($sql,array($trans_lang_id));
            return $res?$res:array();
        }
        
	function getAssessmentByUser($assessment_id,$user_id,$lang_id=DEFAULT_LANGUAGE,$external=0){
            
           /* $sql="SELECT a.assessment_id,d.assessment_type_id,a.client_id,a.diagnostic_id, a.isAssessorKeyNotesApproved,a.is_replicated, c.principal_phone_no, b.percComplete, date(a.create_date) as create_date,date(b.ratingInputDate) as ratingInputDate,d.name as diagnostic_name,c.client_name,t.assessment_type_name,b.isFilled as status, b.role,b.user_id, cn.network_id, n.network_name, q.status as aqs_status,r.isPublished report_published,(select group_concat(y.name order by x.role) from h_assessment_user x inner join d_user y on x.user_id=y.user_id where x.assessment_id=a.assessment_id ) as user_names,a.d_sub_assessment_type_id as 'subAssessmentType',a.is_approved as isApproved
		FROM `d_assessment` a 
			inner join `h_assessment_user` b on a.assessment_id=b.assessment_id 
            inner join `d_client` c on c.client_id=a.client_id
			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id
			left join h_client_network cn on cn.client_id=c.client_id
			left join d_network n on cn.network_id=n.network_id
			left join d_AQS_data q on q.id=a.aqsdata_id
			left join h_assessment_report r on r.assessment_id=a.assessment_id and r.report_id=1
			where b.user_id=? and a.assessment_id=?";*/

                        $cond = 'b.user_id=?';
                        $sql_part = '';
                        $columnCond = 'b.role';
                        $params = array($user_id,4,$assessment_id,$lang_id);
                        if($external == 1) {
                          $cond .= 'AND b.user_role=?';
                          $columnCond = 'b.user_role as role';
                          $sql_part .= ' inner join `h_assessment_external_team` b on a.assessment_id=b.assessment_id ';
                          //unset($params[0]);
                          //array_unshift($params, 4);
                          //print_r($params);
                        }else {
                            $sql_part .= ' inner join `h_assessment_user` b on a.assessment_id=b.assessment_id ';
                            $columnCond .= ',hau.isLeadSave';
                             unset($params[1]);
                        }

                            $sql="SELECT a.iscollebrative,a.assessment_id,a.language_id,d.assessment_type_id,a.client_id,
                            a.diagnostic_id, a.isAssessorKeyNotesApproved,a.is_replicated, c.principal_phone_no,
                            b.percComplete, date(a.create_date) as create_date,date(b.ratingInputDate) as ratingInputDate,
                            hlt.translation_text as diagnostic_name,c.client_name,t.assessment_type_name,b.isFilled as status,b.percComplete,
                            $columnCond,b.user_id, cn.network_id, n.network_name, q.status as aqs_status,r.isPublished report_published,
                            (select group_concat(y.name order by x.role) from h_assessment_user x inner join 
                            d_user y on x.user_id=y.user_id where x.assessment_id=a.assessment_id ) as user_names,
                            a.d_sub_assessment_type_id as 'subAssessmentType',a.is_approved as isApproved,q.school_aqs_pref_start_date as aqs_sdate,q.school_aqs_pref_end_date as aqs_edate,hau.user_id as external
                             FROM `d_assessment` a  ";
                           

                            $sql .= $sql_part ."                 
                             inner join `d_client` c on c.client_id=a.client_id
                             inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
                             inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id
                             inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 		
                             left join h_client_network cn on cn.client_id=c.client_id
                             left join d_network n on cn.network_id=n.network_id
                             left join d_AQS_data q on q.id=a.aqsdata_id
                             left join h_assessment_report r on r.assessment_id=a.assessment_id and r.report_id=1";
                            if($external == 1)
                             $sql .= " left join `h_assessment_external_team` hau on a.assessment_id=hau.assessment_id && hau.user_role=4";
                            else 
                            $sql .= " left join `h_assessment_user` hau on a.assessment_id=hau.assessment_id && hau.role=4";
                                 
                             $sql .= " where $cond and a.assessment_id=? and hlt.translation_type_id=7 and hlt.language_id=? ";
                             $params = (array_values($params));
                             $row=$this->db->get_row($sql,$params);
                             //echo "aaa";  print_r($row);die;
                             if($row){
                                     $row['user_names']=explode(",",$row['user_names']);
                             }
                             return $row;
	}
	
	function getAssessmentByRole($assessment_id,$role=4,$lang_id=DEFAULT_LANGUAGE,$external=0,$is_collaborative=0,$assessor_id=0){
		
           
            $sqlColumn = 'b.role';
            $sqlCond = 'b.role = ?';
            $sql_part = ' inner join `h_assessment_user` b on a.assessment_id=b.assessment_id';
            $params = array($role);
            if($external && $is_collaborative ) {
                $sql_part = ' inner join `h_assessment_external_team` b on a.assessment_id=b.assessment_id';
                $sqlColumn = 'b.user_role ';
                $sqlCond = 'b.user_role = ? AND b.user_id = ? ';
                $params[] = $assessor_id; 
            }
                $sql="SELECT a.assessment_id,a.client_id,a.diagnostic_id, a.isAssessorKeyNotesApproved,a.is_replicated, c.principal_phone_no, b.percComplete, date(a.create_date) as create_date,date(b.ratingInputDate) as ratingInputDate,hlt.translation_text as diagnostic_name,c.client_name,t.assessment_type_name,b.isFilled as status, $sqlColumn as role,b.user_id, q.status as aqs_status,d.assessment_type_id,a.d_sub_assessment_type_id
		FROM `d_assessment` a "; 
                $sql .= $sql_part ."  inner join `d_client` c on c.client_id=a.client_id
			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
			inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 		
			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id
			left join d_AQS_data q on q.id=a.aqsdata_id
			where $sqlCond and a.assessment_id=? and hlt.translation_type_id=7 and hlt.language_id=? ";
              //$params[] = $assessment_id;
             // $params[] = $lang_id;
                $params = array_merge($params,array($assessment_id,$lang_id));
		return $this->db->get_row($sql,$params);
	}
        
        
	
	/*function getDiagnostic($diagnostic_id=0){
		$sql="select d.*,t.assessment_type_name 
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
			where diagnostic_id=? ;";
		return $this->db->get_row($sql,array($diagnostic_id));
	}*/
        
        
        function getFirstDefaultDiagnostic(){
		$sql="select d.*,t.assessment_type_name ,hlt.translation_text  as name
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
			where isdefaultselfreview=? && d.assessment_type_id=? && hlt.parent_lang_translation_id is null limit 0,1;";
		$res=$this->db->get_row($sql,array(1,1));
                return $res?$res:array();
	}
        function getAssessmentTeam($assessment_id){
		$sql="SELECT user_id,percComplete FROM h_assessment_external_team WHERE assessment_id = ? "
                        . " UNION SELECT user_id,percComplete FROM h_assessment_user WHERE assessment_id = ? AND role = ? ";
		$res=$this->db->get_results($sql,array($assessment_id,$assessment_id,4));
                return $res?$res:array();
	}

        function getAssessmentKpa($assessment_id,$user_id = 0){
                
            $sqlCond = '';
            $params = array($assessment_id);
            if(!empty($user_id)){                    
                $sqlCond = ' and user_id=?';
                $params[1] = $user_id;
            }
            $sql="SELECT count(kpa_instance_id) as kpa_num from d_assessment_kpa WHERE assessment_id = ? $sqlCond";
            $res=$this->db->get_row($sql,$params);
            return $res?$res:array();
	}
        
        function getCollAssessmentTeam($assessment_id){
		$sql="SELECT t.user_id,t.percComplete FROM h_assessment_external_team t inner join d_assessment_kpa kp on kp.assessment_id= t.assessment_id "
                        . " AND t.user_id = kp.user_id WHERE kp.assessment_id = ? "
                        . " UNION SELECT u.user_id,u.percComplete FROM h_assessment_user u  inner join d_assessment_kpa kp on kp.assessment_id= u.assessment_id "
                        . " AND u.user_id = kp.user_id WHERE u.assessment_id = ? AND u.role = ? ";
		$res=$this->db->get_results($sql,array($assessment_id,$assessment_id,4));
                return $res?$res:array();
	}
        function updateAvgAssessmentPercentage ( $assessment_id,$avgPercntg){
		
		$data=array("collaborativepercntg"=>$avgPercntg);
		
		return $this->db->update("d_assessment",$data,array("assessment_id"=>$assessment_id));
	}
        
        function checkIsLead($user_id){
		$sql="SELECT count(user_id) as lead_id FROM h_assessment_user WHERE user_id = ? ";
			
		$res=$this->db->get_row($sql,array($user_id));
                return $res['lead_id']>=1?1:0;
	}
        
        function getFirstDefaultDiagnosticDrop($edit_diagnostic_id=0){
		$sql="select d.*,t.assessment_type_name ,hlt.translation_text  as name
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
			where hlt.parent_lang_translation_id is null AND d.assessment_type_id=? && (isdefaultselfreview=? ";
                
                        $value_array=array(1,1);        
                        if($edit_diagnostic_id>0){
                         $sql.=" || d.diagnostic_id=?";
                         $value_array[]=$edit_diagnostic_id;
                        }
                        $sql.=")";
                        
		$res=$this->db->get_results($sql,$value_array);
                return $res?$res:array();
	}
        
        function getFreeDiagnostic($edit_diagnostic_id=0){
		$sql="select d.*,t.assessment_type_name ,hlt.translation_text as name
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
			where hlt.parent_lang_translation_id is null AND  d.assessment_type_id=? && (isfreeselfreview=? ";
                        $value_array=array(1,1);        
                        if($edit_diagnostic_id>0){
                         $sql.=" || d.diagnostic_id=?";
                         $value_array[]=$edit_diagnostic_id;
                        }
                       $sql.=")";
		$res=$this->db->get_results($sql,$value_array);
                
                return $res?$res:array();
	}
        
        function getGuestDiagnostic(){
            $sql="select d.*,t.assessment_type_name ,hlt.translation_text as name
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
			where hlt.parent_lang_translation_id is null AND  d.assessment_type_id=? && isGuestuser=? ";
                        $value_array=array(1,1); 
                        $res=$this->db->get_results($sql,$value_array);
                
                return $res?$res:array();
        }
        
        function getFreeAndLastDiagnostic($last_diagnostic_id=0,$edit_diagnostic_id=0){
		$sql="select d.*,t.assessment_type_name ,hlt.translation_text  as name
			from d_diagnostic d 
			inner join d_assessment_type t on d.assessment_type_id=t.assessment_type_id
                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
			where hlt.parent_lang_translation_id is null AND d.assessment_type_id=? && (isfreeselfreview=? || d.diagnostic_id=?";
                
                        $value_array=array(1,1,$last_diagnostic_id);
                        if($edit_diagnostic_id>0){
                         $sql.=" || d.diagnostic_id=?";
                         $value_array[]=$edit_diagnostic_id;
                        }
                         $sql.=")";
		$res=$this->db->get_results($sql,$value_array);
                return $res?$res:array();
	}
        
        function getTeacherDiagnostic($category_id,$lang_id=DEFAULT_LANGUAGE){
            $sqlArgs=array();
            $sql="select d.*,hlt.translation_text
			from d_diagnostic d 
			inner join h_diagnostic_teacher_cat tt on d.diagnostic_id=tt.diagnostic_id
                        inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 
                         inner join h_lang_trans_diagnostics_details hltd on  hltd.lang_translation_id= hlt.lang_translation_id 
			where 1=1 && hlt.language_id=? && hlt.isActive=1 AND  hltd.isPublished = 1 ";
            $sql.="and tt.teacher_cat_id=? order by hlt.translation_text asc";
            $sqlArgs[]=$lang_id;
	    $sqlArgs[]=$category_id;
            $res=$this->db->get_results($sql,$sqlArgs);
	    return $res?$res:array();
            
        }
	
        function getDiagnosticCentre($province_id){
            
        if(gettype($province_id)=="array"){
            
        $sql="select d.diagnostic_id from (select * from d_province where province_id IN (".(implode(",",$province_id)).")) a 
inner join h_client_province  b on a.province_id=b.province_id
inner join d_client c on c.client_id=b.client_id
inner join d_assessment d on c.client_id=d.client_id
left join d_diagnostic e on d.diagnostic_id=e.diagnostic_id
left join h_diagnostic_teacher_cat f on e.diagnostic_id=f.diagnostic_id
left join d_teacher_category g on g.teacher_category_id=f.teacher_cat_id where g.teacher_category='Student' group by diagnostic_id order by d.diagnostic_id limit 0,1";
      return $this->db->get_row($sql);
      
        }else{    
        $sql="select d.diagnostic_id from (select * from d_province where province_id=?) a 
inner join h_client_province  b on a.province_id=b.province_id
inner join d_client c on c.client_id=b.client_id
inner join d_assessment d on c.client_id=d.client_id
left join d_diagnostic e on d.diagnostic_id=e.diagnostic_id
left join h_diagnostic_teacher_cat f on e.diagnostic_id=f.diagnostic_id
left join d_teacher_category g on g.teacher_category_id=f.teacher_cat_id where g.teacher_category='Student' group by diagnostic_id order by d.diagnostic_id limit 0,1";
       return $this->db->get_row($sql,array($province_id));
        }
        
        
        }
        
        function getDiagnosticOrg($network_id){
        $sql="select d.diagnostic_id from (select * from d_network where network_id=?) a1
            inner join h_province_network b1 on a1.network_id=b1.network_id
            inner join d_province  a on b1.province_id=a.province_id
            inner join h_client_province  b on a.province_id=b.province_id
            inner join d_client c on c.client_id=b.client_id
            inner join d_assessment d on c.client_id=d.client_id
            left join d_diagnostic e on d.diagnostic_id=e.diagnostic_id
            left join h_diagnostic_teacher_cat f on e.diagnostic_id=f.diagnostic_id
            left join d_teacher_category g on g.teacher_category_id=f.teacher_cat_id where g.teacher_category='Student' group by diagnostic_id order by d.diagnostic_id limit 0,1";
            return $this->db->get_row($sql,array($network_id));
        
        }
        
	/*function getAssessmentByUser($assessment_id,$user_id){
		$sql="SELECT a.assessment_id,d.assessment_type_id,a.client_id,a.diagnostic_id, a.isAssessorKeyNotesApproved,a.is_replicated, c.principal_phone_no, b.percComplete, date(a.create_date) as create_date,date(b.ratingInputDate) as ratingInputDate,d.name as diagnostic_name,c.client_name,t.assessment_type_name,b.isFilled as status, b.role,b.user_id, cn.network_id, n.network_name, q.status as aqs_status,r.isPublished report_published,(select group_concat(y.name order by x.role) from h_assessment_user x inner join d_user y on x.user_id=y.user_id where x.assessment_id=a.assessment_id ) as user_names,a.d_sub_assessment_type_id as 'subAssessmentType',a.is_approved as isApproved
		FROM `d_assessment` a 
			inner join `h_assessment_user` b on a.assessment_id=b.assessment_id 
            inner join `d_client` c on c.client_id=a.client_id
			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id
			left join h_client_network cn on cn.client_id=c.client_id
			left join d_network n on cn.network_id=n.network_id
			left join d_AQS_data q on q.id=a.aqsdata_id
			left join h_assessment_report r on r.assessment_id=a.assessment_id and r.report_id=1
			where b.user_id=? and a.assessment_id=?";
		$row=$this->db->get_row($sql,array($user_id,$assessment_id));
		if($row){
			$row['user_names']=explode(",",$row['user_names']);
		}
		return $row;
	}*/
	
        function getAllJudgementScore($assessment_id,$assessor_id){
            $sql="SELECT * from `f_score` where assessment_id=? and assessor_id=? and isFinal=1";
            $sqlArgs=array($assessment_id,$assessor_id);
            $res=$this->db->get_results($sql,$sqlArgs);
	    return $res?$res:array();
        }
        
        function getAllKeyQuestionScore($assessment_id,$assessor_id){
            $sql="SELECT * from `h_kq_instance_score` where assessment_id=? and assessor_id=?";
            $sqlArgs=array($assessment_id,$assessor_id);
            $res=$this->db->get_results($sql,$sqlArgs);
	    return $res?$res:array();
            
        }
        
         function getSingleKeyQuestionScore($kq_id,$assessment_id,$assessor_id){
            $sql="SELECT * from `h_kq_instance_score` where assessment_id=? and assessor_id=? && key_question_instance_id=?";
            $sqlArgs=array($assessment_id,$assessor_id,$kq_id);
            $res=$this->db->get_results($sql,$sqlArgs);                
            return $res?$res:array();                    
	}
        
        function getAllCoreQuestionScore($assessment_id,$assessor_id){
            $sql="SELECT * from `h_cq_score` where assessment_id=? and assessor_id=?";
            $sqlArgs=array($assessment_id,$assessor_id);
            $res=$this->db->get_results($sql,$sqlArgs);
	    return $res?$res:array();
        }
        
        function getSingleCoreQuestionScore($cq_id,$assessment_id,$assessor_id){
            $sql="SELECT * from `h_cq_score` where assessment_id=? and assessor_id=? && core_question_instance_id=?";
            $sqlArgs=array($assessment_id,$assessor_id,$cq_id);
            $res=$this->db->get_results($sql,$sqlArgs);                
            return $res?$res:array();                    
	}
        
        function getAllKpaScore($assessment_id,$assessor_id){
            $sql="SELECT * from `h_kpa_instance_score` where assessment_id=? and assessor_id=?";
            $sqlArgs=array($assessment_id,$assessor_id);
            $res=$this->db->get_results($sql,$sqlArgs);
	    return $res?$res:array();
        }
        
        function getSingleKpaScore($kpa_id,$assessment_id,$assessor_id){
            $sql="SELECT * from `h_kpa_instance_score` where assessment_id=? and assessor_id=? && kpa_instance_id=?";
            $sqlArgs=array($assessment_id,$assessor_id,$kpa_id);
            $res=$this->db->get_results($sql,$sqlArgs);                
            return $res?$res:array();                    
	}
        
        function getKeyNotesPer($assessment_id,$kpa_id){
            $sql="SELECT * from `assessor_key_notes` where assessment_id=? and 	kpa_instance_id=?";
            $sqlArgs=array($assessment_id,$kpa_id);
            $res=$this->db->get_results($sql,$sqlArgs);                
            return $res?$res:array();                    
	}
        
	/*function getAssessmentByRole($assessment_id,$role=4){
		$sql="SELECT a.assessment_id,a.client_id,a.diagnostic_id, a.isAssessorKeyNotesApproved, a.is_replicated, c.principal_phone_no, b.percComplete, date(a.create_date) as create_date,date(b.ratingInputDate) as ratingInputDate,d.name as diagnostic_name,c.client_name,t.assessment_type_name,b.isFilled as status, b.role,b.user_id, q.status as aqs_status
		FROM `d_assessment` a 
			inner join `h_assessment_user` b on a.assessment_id=b.assessment_id 
            inner join `d_client` c on c.client_id=a.client_id
			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id
			left join d_AQS_data q on q.id=a.aqsdata_id
			where b.role=? and a.assessment_id=?";
		return $this->db->get_row($sql,array($role,$assessment_id));
	}*/
	function getAdditionalQuestionsId($aqsData_id,$tableName){
		$sql = " SELECT * from $tableName where aqs_data_id=?";
		$res = $this->db->get_row($sql,array($aqsData_id));
		return $res ? $res : array(); 
	}
        function isUserinExternalTeamAssessment($assessment_id,$user_id){
            $sql = "SELECT count(user_id) as num from d_assessment a INNER JOIN h_assessment_external_team b on a.assessment_id =b.assessment_id "
                    . " where a.assessment_id=? and b.user_id=?";
            $res = $this->db->get_row($sql,array($assessment_id,$user_id));
            return $res ? $res : null; 
        }
       function getAssessmentById($assessment_id,$lang_id=DEFAULT_LANGUAGE,$actionPlan = 0){
                
                if(empty($lang_id)) {
                    //$lang_id = $this->lang;
                }

                $sqlCond = '';
                $fieldCond = '';
                if($actionPlan == 1) {
                    $fieldCond = 'u1.email as pricniple_email,u1.name as principle_name,';
                    $sqlCond = ' inner join d_user u1 on u1.client_id=a.client_id '
                            . ' inner join h_user_user_role r on  u1.user_id = r.user_id and r.role_id=6';
                }
		$sql="SELECT $fieldCond a.aqs_round,b.ratingInputDate as rating_date,dp.province_name,a.assessment_id,ag.group_assessment_id,d.assessment_type_id,a.client_id,a.diagnostic_id, a.isAssessorKeyNotesApproved, c.principal_phone_no, date(a.create_date) as create_date,hlt.translation_text as diagnostic_name,c.client_name,c.street,c.city,c.state,t.assessment_type_name, group_concat(b.role order by b.role) as roles, group_concat(b.user_id order by b.role) as user_ids, group_concat(u.name order by b.role) as user_names,group_concat(b.isFilled order by b.role) as  statuses, cn.network_id, n.network_name, q.status as aqs_status,q.school_aqs_pref_end_date,r.isPublished as report_published,CASE WHEN d.assessment_type_id = 2 THEN if(sum(td.value)>0,1,0) WHEN d.assessment_type_id = 4 THEN if(sum(sd.value)>0,1,0) END as isTchrInfoFilled,a.d_sub_assessment_type_id as subAssessmentType,group_concat(b.percComplete order by b.role) as perc,adnl.*,a.aqsdata_id,a.is_approved as isApproved

		FROM `d_assessment` a 
			inner join `h_assessment_user` b on a.assessment_id=b.assessment_id
			inner join d_user u on b.user_id=u.user_id
                        inner join `d_client` c on c.client_id=a.client_id
                        $sqlCond
			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
			inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 		
			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id
			left join h_assessment_ass_group ag on ag.assessment_id=a.assessment_id
			left join h_client_network cn on cn.client_id=c.client_id
			left join d_network n on cn.network_id=n.network_id
                        left join h_client_province pr on pr.client_id=c.client_id
                        left join d_province dp on dp.province_id=pr.province_id
			left join d_AQS_data q on q.id=a.aqsdata_id
			left join d_teacher_data td on td.teacher_id=b.user_id and b.role=3 and td.assessment_id=a.assessment_id and td.attr_id=11
                        left join d_student_data sd on sd.student_id=b.user_id and b.role=3 and sd.assessment_id=a.assessment_id and sd.attr_id=49
			left join (select * from h_assessment_report r2 where r2.assessment_id=? limit 1) r on r.assessment_id=a.assessment_id
			left join (select aqs_additional_id,table_name,diagnostic_id as diag_id from d_aqs_additional) adnl on adnl.diag_id=a.diagnostic_id
			where a.assessment_id=? group by a.assessment_id and hlt.translation_type_id=7 and hlt.language_id=$lang_id ";
		if($res= $this->db->get_row($sql,array($assessment_id,$assessment_id))){			
			$res['roles']=$res['roles']!=""?explode(',',$res['roles']):array();
			$res['user_ids']=$res['user_ids']!=""?explode(',',$res['user_ids']):array();
			$res['user_names']=$res['user_names']!=""?explode(',',$res['user_names']):array();
			$res['statuses']=$res['statuses']!=""?explode(',',$res['statuses']):array();
			$l=count($res['roles']);
			for($i=0;$i<$l;$i++){
				$res['userIdByRole'][$res['roles'][$i]]=$res['user_ids'][$i];
				$res['statusByRole'][$res['roles'][$i]]=$res['statuses'][$i];
				$res['usernameByRole'][$res['roles'][$i]]=$res['user_names'][$i];
			}			
			return $res;
		}else
			return null;
	}
	/*function getAssessmentById($assessment_id){

		$sql="SELECT dp.province_name,a.assessment_id,ag.group_assessment_id,d.assessment_type_id,a.client_id,a.diagnostic_id, a.isAssessorKeyNotesApproved, c.principal_phone_no, date(a.create_date) as create_date,d.name as diagnostic_name,c.client_name,c.street,c.city,c.state,t.assessment_type_name, group_concat(b.role order by b.role) as roles, group_concat(b.user_id order by b.role) as user_ids, group_concat(u.name order by b.role) as user_names,group_concat(b.isFilled order by b.role) as  statuses, cn.network_id, n.network_name, q.status as aqs_status,q.school_aqs_pref_end_date,r.isPublished as report_published,CASE WHEN d.assessment_type_id = 2 THEN if(sum(td.value)>0,1,0) WHEN d.assessment_type_id = 4 THEN if(sum(sd.value)>0,1,0) END as isTchrInfoFilled,a.d_sub_assessment_type_id as subAssessmentType,group_concat(b.percComplete order by b.role) as perc,adnl.*,a.aqsdata_id,a.is_approved as isApproved
		FROM `d_assessment` a 
			inner join `h_assessment_user` b on a.assessment_id=b.assessment_id
			inner join d_user u on b.user_id=u.user_id
            inner join `d_client` c on c.client_id=a.client_id
			inner join `d_diagnostic` d on d.diagnostic_id=a.diagnostic_id
			inner join `d_assessment_type` t on d.assessment_type_id=t.assessment_type_id
			left join h_assessment_ass_group ag on ag.assessment_id=a.assessment_id
			left join h_client_network cn on cn.client_id=c.client_id
			left join d_network n on cn.network_id=n.network_id
			left join h_client_province pr on pr.client_id=c.client_id
			left join d_province dp on dp.province_id=pr.province_id
			left join d_AQS_data q on q.id=a.aqsdata_id
			left join d_teacher_data td on td.teacher_id=b.user_id and b.role=3 and td.assessment_id=a.assessment_id and td.attr_id=11
                        left join d_student_data sd on sd.student_id=b.user_id and b.role=3 and sd.assessment_id=a.assessment_id and sd.attr_id=49
			left join (select * from h_assessment_report r2 where r2.assessment_id=? limit 1) r on r.assessment_id=a.assessment_id
			left join (select aqs_additional_id,table_name,diagnostic_id as diag_id from d_aqs_additional) adnl on adnl.diag_id=a.diagnostic_id                        
			where a.assessment_id=? group by a.assessment_id";
		if($res= $this->db->get_row($sql,array($assessment_id,$assessment_id))){			
			$res['roles']=$res['roles']!=""?explode(',',$res['roles']):array();
			$res['user_ids']=$res['user_ids']!=""?explode(',',$res['user_ids']):array();
			$res['user_names']=$res['user_names']!=""?explode(',',$res['user_names']):array();
			$res['statuses']=$res['statuses']!=""?explode(',',$res['statuses']):array();
			$l=count($res['roles']);
			for($i=0;$i<$l;$i++){
				$res['userIdByRole'][$res['roles'][$i]]=$res['user_ids'][$i];
				$res['statusByRole'][$res['roles'][$i]]=$res['statuses'][$i];
				$res['usernameByRole'][$res['roles'][$i]]=$res['user_names'][$i];
			}			
			return $res;
		}else
			return null;
	}*/
	
	function getGroupAssessmentByGAId($group_assessment_id){

		$sql="SELECT a.assessment_id,dp.province_name,ga.*,c.client_name,c.client_id, c.principal_phone_no,c.street,c.city,c.state, cn.network_id, n.network_name,count(distinct ag.assessment_id) as assessmentAssigned,q.status as aqs_status,q.school_aqs_pref_end_date,group_concat(au.user_id order by au.role) as user_ids,r.isPublished as report_published,adnl.*, date(a.create_date) as create_date
			FROM d_group_assessment ga
			inner join `d_client` c on c.client_id=ga.client_id
			left join h_client_network cn on cn.client_id=c.client_id
			left join d_network n on cn.network_id=n.network_id
			left join h_assessment_ass_group ag on ag.group_assessment_id=ga.group_assessment_id
			left join d_assessment a on a.assessment_id=ag.assessment_id
			left join d_AQS_data q on q.id=a.aqsdata_id
                        left join h_client_province pr on pr.client_id=c.client_id
			left join d_province dp on dp.province_id=pr.province_id
			left join `h_assessment_user` au on a.assessment_id=au.assessment_id 
			left join (select * from h_group_assessment_report r2 where r2.group_assessment_id=? limit 1) r on r.group_assessment_id=ga.group_assessment_id
			left join (select aqs_additional_id,table_name,diagnostic_id from d_aqs_additional) adnl on adnl.diagnostic_id=a.diagnostic_id
				where ga.group_assessment_id=? 
			group by ga.group_assessment_id;";
		if($res= $this->db->get_row($sql,array($group_assessment_id,$group_assessment_id))){
			$res['user_ids']=$res['user_ids']!=""?explode(',',$res['user_ids']):array();
			return $res;
		}else
			return null;
	}
	
	function getTeacherAssessmentReports($group_assessment_id,$diagnostic_id=0){
		$sql="select r.report_name,r.report_id,ar.isPublished,date(ar.publishDate) as publishDate,date(ar.valid_until) as valid_until,if(ar.report_id>0,1,0) as isGenerated, ga.group_assessment_id,q.status as aqs_status,CASE WHEN  ga.assessment_type_id = 2 THEN if(sum(td.value)/count(distinct ag.assessment_id)=1,1,0) WHEN  ga.assessment_type_id = 4 THEN if(sum(sd.value)/count(distinct ag.assessment_id)=1,1,0) END as allTchrInfoFilled,group_concat(au.isFilled order by au.role) as statuses, c.client_name,c.client_id,ga.student_round,0 as assessment_id,ga.assessment_type_id,group_concat(CONCAT_WS('$|%',au.isFilled,a.diagnostic_id,dsl.school_level_id) order by au.role) as statusescheck, group_concat(distinct a.diagnostic_id,'$|%',tc.teacher_category) as diagnostic_ids,group_concat(distinct dsl.school_level_id,'$|%',dsl.school_level) as school_level_ids,q.school_aqs_pref_end_date,date(a.create_date) as create_date
			from `d_reports` r
			inner join d_group_assessment ga on ga.assessment_type_id=r.assessment_type_id and ga.group_assessment_id=?
			inner join d_client c on ga.client_id=c.client_id
			inner join h_assessment_ass_group ag on ag.group_assessment_id=ga.group_assessment_id
			inner join d_assessment a on a.assessment_id=ag.assessment_id
			inner join h_assessment_user au on a.assessment_id=au.assessment_id
                        inner join h_assessment_user au1 on a.assessment_id=au1.assessment_id && au1.role=3
                        left join d_AQS_data q on q.id=a.aqsdata_id
			left join h_group_assessment_report ar on r.report_id=ar.report_id and ar.group_assessment_id=ga.group_assessment_id
			left join d_teacher_data td on td.teacher_id=au.user_id and au.role=3 and td.assessment_id=a.assessment_id and td.attr_id=11
                        left join d_student_data sd on sd.student_id=au.user_id and au.role=3 and sd.assessment_id=a.assessment_id and sd.attr_id=49
			left join h_diagnostic_teacher_cat dtc on dtc.diagnostic_id=a.diagnostic_id
			left join d_teacher_category tc on tc.teacher_category_id=dtc.teacher_cat_id
                        left join h_group_assessment_teacher hgat on hgat.group_assessment_id=ga.group_assessment_id && hgat.teacher_id=au1.user_id
			left join d_school_level dsl on hgat.school_level_id=dsl.school_level_id
                        where r.isIndividualAssessmentReport=0 ";
                        $val_array=array();        
                        $val_array[]=$group_assessment_id;        
                        if(!empty($diagnostic_id)){
                        $sql.=" && a.diagnostic_id=?";
                        $val_array[]=$diagnostic_id; 
                        }
                
			$sql.=" group by r.report_id,ga.group_assessment_id";
                        
                        //print_r($val_array);
                                
		if($res= $this->db->get_row($sql,$val_array)){
                        //print_r($res);
                        //echo ($res['statusescheck']);
                        $res["statusescheck"]=$res['statusescheck']!=""?explode(',',$res['statusescheck']):array();
                        $sz1=count($res['statusescheck']);
                        $diagnostics_allowed=array();
                        $department_allowed=array();
                        if($sz1){
                        $tmp2=array_chunk($res['statusescheck'],$sz1/2);
                        //$aa=print_r($tmp2[1]);
                        foreach($tmp2[1] as $key=>$val){
                            $aa2=explode("$|%",$val);
                            if(isset($aa2[0]) && $aa2[0]==1){
                            $diagnostics_allowed[]=isset($aa2[1])?$aa2[1]:0;
                            $department_allowed[]=isset($aa2[2])?$aa2[2]:0;
                            }
                        }
                        //$res['dignosticsstatses']=
                        }
                        
                        //print_r($diagnostics_allowed);
                        //print_r($department_allowed);
                        
			$res["statuses"]=$res['statuses']!=""?explode(',',$res['statuses']):array();
			$res['diagnostic_ids']=explode(",",$res['diagnostic_ids']);
			$tmp=array();
			foreach($res['diagnostic_ids'] as $da){
				$t=explode("$|%",$da);
                                if(in_array($t[0],$diagnostics_allowed)){
				$tmp[$t[0]]=$t[1];
                                }
			}
			$res['diagnostic_ids']=$tmp;
                        
                        $res['school_level_ids']=explode(",",$res['school_level_ids']);
			$tmp1=array();
                        //print_r($res['school_level_ids']);
			foreach($res['school_level_ids'] as $da1){
				$t1=explode("$|%",$da1);
                                if(isset($t1[0]) && isset($t1[1]) && in_array($t1[0],$department_allowed)){
				$tmp1[$t1[0]]=$t1[1];
                                }
			}
			$res['school_level_ids']=$tmp1;
                        
			$sz=count($res['statuses']);
			if($sz){
				$tmp=array_chunk($res['statuses'],$sz/2);
				$res['allStatusFilled']=in_array(0,$tmp[1])?0:1;
			}else{
				$res['allStatusFilled']=0;
			}
			return $res;
		}else
			return null;
	}
	
	function getGroupAssessmentByAssmntId($assessment_id){
		$sql="SELECT ga.*,c.client_name,c.client_id, c.principal_phone_no,c.street,c.city,c.state, cn.network_id, n.network_name,count(distinct ag.assessment_id) as assessmentAssigned,q.status as aqs_status,group_concat(au.user_id order by au.role) as user_ids,group_concat(u.name order by au.role) as user_names,r.isPublished as report_published
			from d_assessment a
            inner join h_assessment_ass_group ag on ag.assessment_id=a.assessment_id
			inner join  d_group_assessment ga on ag.group_assessment_id=ga.group_assessment_id
			inner join `d_client` c on c.client_id=ga.client_id
            inner join `h_assessment_user` au on a.assessment_id=au.assessment_id
			inner join d_user u on u.user_id=au.user_id
			left join h_client_network cn on cn.client_id=c.client_id
			left join d_network n on cn.network_id=n.network_id
			left join d_AQS_data q on q.id=a.aqsdata_id
			left join h_assessment_report r on r.assessment_id=a.assessment_id and r.report_id=5
			where a.assessment_id=?
			group by ga.group_assessment_id;";
		if($res= $this->db->get_row($sql,array($assessment_id))){
			$res['user_ids']=$res['user_ids']!=""?explode(',',$res['user_ids']):array();
			$res['user_names']=$res['user_names']!=""?explode(',',$res['user_names']):array();
			return $res;
		}else
			return null;
	}
	
	function getSubAssReportsByGroupAssessmentId($group_assessment_id,$external_download_teacher=0){
            $sql="select r.report_name,r.report_id,ar.isPublished,date(ar.publishDate) as publishDate,date(ar.valid_until) as valid_until,if(ar.report_id>0,1,0) as isGenerated,count(k.kpa_instance_id) as kcount,group_concat(distinct u.name order by au.role) as user_names,CASE WHEN d.assessment_type_id = 2 THEN if(sum(td.value)>0,1,0) WHEN d.assessment_type_id = 4 THEN if(sum(sd.value)>0,1,0) END as isTchrInfoFilled,a.assessment_id,0 as group_assessment_id, group_concat(au.isFilled order by au.role) statuses,date(a.create_date) as create_date
				from `d_reports` r
				inner join d_diagnostic d on d.assessment_type_id=r.assessment_type_id
				inner join d_assessment a on a.diagnostic_id=d.diagnostic_id
                inner join h_assessment_ass_group ag on a.assessment_id=ag.assessment_id and ag.group_assessment_id=?
                inner join h_kpa_diagnostic k on k.diagnostic_id=a.diagnostic_id
                inner join h_assessment_user au on a.assessment_id=au.assessment_id
                inner join d_user u on u.user_id=au.user_id
                inner join h_assessment_user au_1 on a.assessment_id=au_1.assessment_id && au_1.role=4
		left join h_assessment_report ar on r.report_id=ar.report_id and ar.assessment_id=a.assessment_id
                left join d_teacher_data td on td.teacher_id=au.user_id and au.role=3 and td.assessment_id=a.assessment_id and td.attr_id=11
                left join d_student_data sd on sd.student_id=au.user_id and au.role=3 and sd.assessment_id=a.assessment_id and sd.attr_id=49
				where r.isIndividualAssessmentReport=1 " ;
                $array_query[]=$group_assessment_id;                
                
                if($external_download_teacher!=0){
                 $sql.=" AND au_1.user_id=? ";
                 $array_query[]=$external_download_teacher;
                }
                
                $sql.=" group by r.report_id,a.assessment_id
                having isTchrInfoFilled=1 and statuses like '%,1'";
                                
		$res=$this->db->get_results($sql,$array_query);
                
		if($res){
			$ln=count($res);
			for($i=0;$i<$ln;$i++){
				$res[$i]['user_names']=explode(",",$res[$i]['user_names']);
			}
			return $res;
		}else
			return array();
	}
	
	function getReportsByAssessmentId($assessment_id,$makeReportIdAsKey=true){
		$res=$this->db->get_results("select a.aqs_round,r.report_name,r.report_id,ar.isPublished,date(ar.publishDate) as publishDate,date(ar.valid_until) as valid_until,if(ar.report_id>0,1,0) as isGenerated,count(k.kpa_instance_id) as kcount,0 as group_assessment_id,q.status as aqs_status,q.school_aqs_pref_end_date,a.d_sub_assessment_type_id as subAssessmentType,DATE(a.create_date) as create_date
				from `d_reports` r
				inner join d_diagnostic d on d.assessment_type_id=r.assessment_type_id
				inner join d_assessment a on a.diagnostic_id=d.diagnostic_id and  a.assessment_id=?
                inner join h_kpa_diagnostic k on k.diagnostic_id=a.diagnostic_id
				left join h_assessment_report ar on r.report_id=ar.report_id and ar.assessment_id=a.assessment_id
				left join d_AQS_data q on q.id=a.aqsdata_id
				where r.isIndividualAssessmentReport=1
                group by r.report_id
                having if(kcount!=7,2,0)!= r.report_id;",array($assessment_id));
		if(!$res)
			return array();
		else if($makeReportIdAsKey)
			return $this->db->array_col_to_key($res,"report_id");
		else
			return $res;
	}
	
	function updateAssessmentReport($assessment_id,$report_id,$years,$months,$aqs_last_date,$publishIt=false){
		$totalMonths=$months+($years*12);
		$data=array("valid_until"=>date("Y-m-d H:i:s",strtotime("+$totalMonths month",strtotime($aqs_last_date))),'publishDate'=>date("Y-m-d H:i:s"));
		if($publishIt){
			$data['isPublished']=1;
		}
		return $this->db->update("h_assessment_report",$data,array("assessment_id"=>$assessment_id,"report_id"=>$report_id));
	}
	
	function insertAssessmentReport($assessment_id,$report_id,$years,$months,$aqs_last_date,$publishIt=false){
		$totalMonths=$months+($years*12);
		$data=array("valid_until"=>date("Y-m-d H:i:s",strtotime("+$totalMonths month",strtotime($aqs_last_date))),"assessment_id"=>$assessment_id,"report_id"=>$report_id,'publishDate'=>date("Y-m-d H:i:s"));
		if($publishIt){
			$data['isPublished']=1;
		}
		if($this->db->insert("h_assessment_report",$data)){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	
	function updateGroupAssessmentReport($group_assessment_id,$report_id,$years,$months,$publishIt=false){
		$totalMonths=$months+($years*12);
		$data=array("valid_until"=>date("Y-m-d H:i:s",strtotime("+$totalMonths month")),'publishDate'=>date("Y-m-d H:i:s"));
		if($publishIt){
			$data['isPublished']=1;
		}
		return $this->db->update("h_group_assessment_report",$data,array("group_assessment_id"=>$group_assessment_id,"report_id"=>$report_id));
	}
	
	function insertGroupAssessmentReport($group_assessment_id,$report_id,$years,$months,$publishIt=false){
		$totalMonths=$months+($years*12);
		$data=array("valid_until"=>date("Y-m-d H:i:s",strtotime("+$totalMonths month")),"group_assessment_id"=>$group_assessment_id,"report_id"=>$report_id,'publishDate'=>date("Y-m-d H:i:s"));
		if($publishIt){
			$data['isPublished']=1;
		}
		if($this->db->insert("h_group_assessment_report",$data)){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	
	function getAssessmentTypes(){
		$res=$this->db->get_results("select * from d_assessment_type;");
		return $res?$res:array();
	}
	
	/*function getKpasForDiagnostic($diagnostic_id){
		$res=$this->db->get_results("select '' as score_id, k.kpa_name,k.kpa_id,kd.kpa_instance_id,'' as rating, NULL as numericRating
			from d_kpa k
			inner join h_kpa_diagnostic kd on k.kpa_id=kd.kpa_id
			where kd.diagnostic_id=?
			order by kd.`kpa_order` asc;",
			array($diagnostic_id));
		return $res?$res:array();
	}*/
        function getKpasForDiagnostic($diagnostic_id){
		$res=$this->db->get_results("select '' as score_id, hlt.translation_text kpa_name,k.kpa_id,kd.kpa_instance_id,'' as rating, NULL as numericRating
			from d_kpa k
			inner join h_kpa_diagnostic kd on k.kpa_id=kd.kpa_id
			inner join h_lang_translation hlt on k.equivalence_id = hlt.equivalence_id 		
			where kd.diagnostic_id=?  and hlt.translation_type_id=1 and hlt.language_id={$this->lang} 
			order by kd.`kpa_order` asc;",
			array($diagnostic_id));		
		return $res?$res:array();
	}
        
        function getKpasForDiagnosticLang($diagnostic_id,$lang_id=DEFAULT_LANGUAGE){
		$res=$this->db->get_results("select '' as score_id, hlt.translation_text kpa_name,k.kpa_id,kd.kpa_instance_id,'' as rating, NULL as numericRating
			from d_kpa k
			inner join h_kpa_diagnostic kd on k.kpa_id=kd.kpa_id
			inner join h_lang_translation hlt on k.equivalence_id = hlt.equivalence_id 		
			where kd.diagnostic_id=?  and hlt.translation_type_id=1 and hlt.language_id=? 
			order by kd.`kpa_order` asc;",
			array($diagnostic_id,$lang_id));		
		return $res?$res:array();
	}
        
        function getKpasForAssessment($assessment_id,$assessor_id,$kpa_id=0,$lang_id=DEFAULT_LANGUAGE,$is_collaborative=0,$external=0,$isLeadAssessorKpa=0){
            
               // $sqlArgs = array();
                $score = ' ks.id as score_id';
                if($isLeadAssessorKpa)
                        $score = '0 as score_id';
                $sql = " SELECT $score, hlt.translation_text kpa_name,k.kpa_id,kd.kpa_instance_id,r.rating,hls.rating_level_order as numericRating,kd.`kpa_order` as kpa_no,rls.rating_level_scheme_id as scheme_id
			FROM `d_kpa` k ";
                if($is_collaborative) {
                    //$sqlArgs[]=$assessor_id;
                    $sql .= 'inner join (select ck.diagnostic_id,ck.kpa_id,ck.kpa_instance_id, ck.kpa_order from h_kpa_diagnostic ck inner join d_assessment_kpa ka on ck.kpa_instance_id=ka.kpa_instance_id WHERE ka.user_id=? AND ka.assessment_id=? ) kd on kd.kpa_id = k.kpa_id';
                }else {
                    $sql .= 'inner join h_kpa_diagnostic kd on k.kpa_id=kd.kpa_id';
                }
		$sql .=" inner join d_assessment a on kd.diagnostic_id=a.diagnostic_id";
                if($external == 1) {
                    $sql .=" inner join h_assessment_external_team au on au.assessment_id=a.assessment_id";
                }else{
			$sql .=" inner join h_assessment_user au on au.assessment_id=a.assessment_id";
                }
			$sql .=" inner join h_lang_translation hlt on k.equivalence_id = hlt.equivalence_id 		
			left join `h_kpa_instance_score` ks on kd.kpa_instance_id=ks.kpa_instance_id and a.assessment_id=ks.assessment_id and ks.assessor_id=au.user_id
			left join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = a.diagnostic_id
                        left join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and ks.d_rating_rating_id=hls.rating_id  and hls.rating_level_id=1
                        left join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
                        left join (select hlt.translation_text as rating,r.rating_id from h_lang_translation hlt INNER JOIN d_rating r on r.equivalence_id = hlt.equivalence_id   WHERE  hlt.language_id=?) r on ks.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id 
			where a.assessment_id=? and au.user_id=? and hlt.translation_type_id=1 and hlt.language_id=? ";
		$sqlArgs=array($lang_id,$assessment_id,$assessor_id,$lang_id);
                if($is_collaborative) 
                    array_splice( $sqlArgs, 0, 0, array($assessor_id,$assessment_id) );
                
		if($kpa_id>0){
			$sql.=" and kd.kpa_instance_id=?";
			$sqlArgs[]=$kpa_id;
		}
		$sql.=" order by kd.`kpa_order` asc;";
		$res=$this->db->get_results($sql,$sqlArgs);
               // echo "<pre>";print_r($res);
		return $res?$res:array();
	}
	
	/*function getKpasForAssessment($assessment_id,$assessor_id,$kpa_id=0){
		$sql="SELECT ks.id as score_id, k.kpa_name,k.kpa_id,kd.kpa_instance_id,r.rating,hls.rating_level_order as numericRating,kd.`kpa_order` as kpa_no,rls.rating_level_scheme_id as scheme_id
			FROM `d_kpa` k
			inner join h_kpa_diagnostic kd on k.kpa_id=kd.kpa_id
			inner join d_assessment a on kd.diagnostic_id=a.diagnostic_id
			inner join h_assessment_user au on au.assessment_id=a.assessment_id
			left join `h_kpa_instance_score` ks on kd.kpa_instance_id=ks.kpa_instance_id and a.assessment_id=ks.assessment_id and ks.assessor_id=au.user_id
			left join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = a.diagnostic_id
            left join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and ks.d_rating_rating_id=hls.rating_id  and hls.rating_level_id=1
            left join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
			left join d_rating r on ks.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id 
			where a.assessment_id=? and au.user_id=?";
		$sqlArgs=array($assessment_id,$assessor_id);
		if($kpa_id>0){
			$sql.=" and kd.kpa_instance_id=?";
			$sqlArgs[]=$kpa_id;
		}
		$sql.="
			order by kd.`kpa_order` asc;";
		$res=$this->db->get_results($sql,$sqlArgs);
		return $res?$res:array();
	}
	
	function getKeyQuestionsForDiagnostic($diagnostic_id){
		$res=$this->db->get_results("SELECT '' as score_id, kq.key_question_id, kq.key_question_text,kkq.key_question_instance_id,kkq.kpa_instance_id,'' as rating, NULL as numericRating
			FROM `d_key_question` kq
			inner join h_kpa_kq kkq on kkq.key_question_id=kq.key_question_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=?
			order by kkq.`kq_order` asc",array($diagnostic_id));
		return $res?$res:array();
	}*/
        
        
	function getKeyQuestionsForDiagnostic($diagnostic_id){
		$res=$this->db->get_results("SELECT '' as score_id, kq.key_question_id, hlt.translation_text key_question_text,kkq.key_question_instance_id,kkq.kpa_instance_id,'' as rating, NULL as numericRating
			FROM `d_key_question` kq
			inner join h_kpa_kq kkq on kkq.key_question_id=kq.key_question_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join h_lang_translation hlt on kq.equivalence_id = hlt.equivalence_id 		
			where kd.diagnostic_id=? and hlt.translation_type_id=2 and hlt.language_id={$this->lang} 
			order by kkq.`kq_order` asc",array($diagnostic_id));
		return $res?$res:array();
	}
        
        function getKeyQuestionsForDiagnosticLang($diagnostic_id,$lang_id=DEFAULT_LANGUAGE){
		$res=$this->db->get_results("SELECT '' as score_id, kq.key_question_id, hlt.translation_text key_question_text,kkq.key_question_instance_id,kkq.kpa_instance_id,'' as rating, NULL as numericRating
			FROM `d_key_question` kq
			inner join h_kpa_kq kkq on kkq.key_question_id=kq.key_question_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join h_lang_translation hlt on kq.equivalence_id = hlt.equivalence_id 		
			where kd.diagnostic_id=? and hlt.translation_type_id=2 and hlt.language_id=?
			order by kkq.`kq_order` asc",array($diagnostic_id,$lang_id));
		return $res?$res:array();
	}
	
        function getKeyQuestionsForAssessment($assessment_id,$assessor_id,$kpa_id=0,$lang_id=DEFAULT_LANGUAGE,$external = 0,$userKpas = array(),$isLeadAssessorKpa=0){
                    
                    $score = ' ks.id as score_id';
                    if($isLeadAssessorKpa)
                        $score = '0 as score_id';
                    
                    $sql="SELECT $score, kq.key_question_id, hlt.translation_text key_question_text,kkq.key_question_instance_id,kkq.kpa_instance_id,r.rating,hls.rating_level_order as numericRating,rls.rating_level_scheme_id as scheme_id
			FROM `d_key_question` kq
			inner join h_kpa_kq kkq on kkq.key_question_id=kq.key_question_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join d_assessment a on kd.diagnostic_id=a.diagnostic_id";
                $cond = '';
                if($external == 1) {
                    $sql .= ' inner join h_assessment_external_team au on au.assessment_id=a.assessment_id ';
                    if(!empty($userKpas))
                    $cond = " and kd.kpa_instance_id IN (".implode(",",$userKpas).")";
                }
                else
                    $sql .= ' inner join h_assessment_user au on au.assessment_id=a.assessment_id';
			
                $sql .=" inner join h_lang_translation hlt on kq.equivalence_id = hlt.equivalence_id	
                left join `h_kq_instance_score` ks on kkq.key_question_instance_id=ks.key_question_instance_id and a.assessment_id=ks.assessment_id and ks.assessor_id=au.user_id	
                left join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = a.diagnostic_id
                left join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and ks.d_rating_rating_id=hls.rating_id  and hls.rating_level_id=2
                left join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
                left join (select hlt.translation_text as rating,r.rating_id from h_lang_translation hlt INNER JOIN d_rating r on r.equivalence_id = hlt.equivalence_id   WHERE  hlt.language_id=?) r on ks.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id 
                where a.assessment_id=? $cond and au.user_id=? and hlt.translation_type_id=2 and hlt.language_id=?";
		$sqlArgs=array($lang_id,$assessment_id,$assessor_id,$lang_id);
		if($kpa_id>0){
			$sql.=" and kd.kpa_instance_id=?";
			$sqlArgs[]=$kpa_id;
		}
		 $sql.=" order by kkq.`kq_order` asc;";
		$res=$this->db->get_results($sql,$sqlArgs);
		return $res?$res:array();
	}
	/*function getKeyQuestionsForAssessment($assessment_id,$assessor_id,$kpa_id=0){
		$sql="SELECT ks.id as score_id, kq.key_question_id, kq.key_question_text,kkq.key_question_instance_id,kkq.kpa_instance_id,r.rating,hls.rating_level_order as numericRating,rls.rating_level_scheme_id as scheme_id
			FROM `d_key_question` kq
			inner join h_kpa_kq kkq on kkq.key_question_id=kq.key_question_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join d_assessment a on kd.diagnostic_id=a.diagnostic_id
			inner join h_assessment_user au on au.assessment_id=a.assessment_id
			left join `h_kq_instance_score` ks on kkq.key_question_instance_id=ks.key_question_instance_id and a.assessment_id=ks.assessment_id and ks.assessor_id=au.user_id	
			left join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = a.diagnostic_id
            left join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and ks.d_rating_rating_id=hls.rating_id  and hls.rating_level_id=2
            left join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
			left join d_rating r on ks.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id 
			where a.assessment_id=?  and au.user_id=?";
		$sqlArgs=array($assessment_id,$assessor_id);
		if($kpa_id>0){
			$sql.=" and kd.kpa_instance_id=?";
			$sqlArgs[]=$kpa_id;
		}
		$sql.="
			order by kkq.`kq_order` asc;";
		$res=$this->db->get_results($sql,$sqlArgs);
		return $res?$res:array();
	}
	
	function getCoreQuestionsForDiagnostic($diagnostic_id){
		$res=$this->db->get_results("SELECT '' as score_id, cq.core_question_id,cq.core_question_text,kqcq.core_question_instance_id,kqcq.key_question_instance_id,'' as rating, NULL as numericRating
			FROM `d_core_question` cq
			inner join h_kq_cq kqcq on kqcq.core_question_id=cq.core_question_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=?
			order by kqcq.`cq_order` asc;",array($diagnostic_id));
		return $res?$res:array();
	}*/
        function getCoreQuestionsForDiagnostic($diagnostic_id){
		$res=$this->db->get_results("SELECT '' as score_id, cq.core_question_id,hlt.translation_text as core_question_text,kqcq.core_question_instance_id,kqcq.key_question_instance_id,'' as rating, NULL as numericRating
			FROM `d_core_question` cq
			inner join h_kq_cq kqcq on kqcq.core_question_id=cq.core_question_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join h_lang_translation hlt on cq.equivalence_id = hlt.equivalence_id 		
			where kd.diagnostic_id=? and hlt.translation_type_id=3 and hlt.language_id={$this->lang} 
			order by kqcq.`cq_order` asc;",array($diagnostic_id));
		return $res?$res:array();
	}
        
        function getCoreQuestionsForDiagnosticLang($diagnostic_id,$lang_id=DEFAULT_LANGUAGE){
		$res=$this->db->get_results("SELECT '' as score_id, cq.core_question_id,hlt.translation_text as core_question_text,kqcq.core_question_instance_id,kqcq.key_question_instance_id,'' as rating, NULL as numericRating
			FROM `d_core_question` cq
			inner join h_kq_cq kqcq on kqcq.core_question_id=cq.core_question_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join h_lang_translation hlt on cq.equivalence_id = hlt.equivalence_id 		
			where kd.diagnostic_id=? and hlt.translation_type_id=3 and hlt.language_id=? 
			order by kqcq.`cq_order` asc;",array($diagnostic_id,$lang_id));
		return $res?$res:array();
	}
        
	function getCoreQuestionsForAssessment($assessment_id,$assessor_id,$kpa_id=0,$lang_id=DEFAULT_LANGUAGE,$external=0,$userKpas=array(),$isLeadAssessorKpa=0){
            
                $cond = '';
                 $score = ' cqs.id as score_id';
                    if($isLeadAssessorKpa)
                        $score = '0 as score_id';
                $sql="SELECT $score, cq.core_question_id,hlt.translation_text as core_question_text,kqcq.core_question_instance_id,kqcq.key_question_instance_id,r.rating,hls.rating_level_order as numericRating,rls.rating_level_scheme_id as scheme_id
			FROM `d_core_question` cq
			inner join h_kq_cq kqcq on kqcq.core_question_id=cq.core_question_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join d_assessment a on kd.diagnostic_id=a.diagnostic_id";
                if($external == 1){
                    $sql .= ' inner join h_assessment_external_team au on au.assessment_id=a.assessment_id';
                    if(!empty($userKpas))
                    $cond = " and kd.kpa_instance_id IN (".implode(",",$userKpas).")";
                }else
                    $sql .= ' inner join h_assessment_user au on au.assessment_id=a.assessment_id';
                
		$sql .= "  inner join h_lang_translation hlt on cq.equivalence_id = hlt.equivalence_id
			left join `h_cq_score` cqs on kqcq.core_question_instance_id=cqs.core_question_instance_id and a.assessment_id=cqs.assessment_id and cqs.assessor_id=au.user_id
			left join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = a.diagnostic_id
                        left join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and cqs.d_rating_rating_id=hls.rating_id  and hls.rating_level_id=3
                        left join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
			left join (select hlt.translation_text as rating,r.rating_id from h_lang_translation hlt INNER JOIN d_rating r on r.equivalence_id = hlt.equivalence_id   WHERE  hlt.language_id=?) r on cqs.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id 
			where a.assessment_id=? $cond and au.user_id=? and hlt.translation_type_id=3 and hlt.language_id=? ";
		$sqlArgs=array($lang_id,$assessment_id,$assessor_id,$lang_id);
		if($kpa_id>0){
			$sql.=" and kd.kpa_instance_id=?";
			$sqlArgs[]=$kpa_id;
		}
		 $sql.="
			order by kqcq.`cq_order` asc;";
		$res=$this->db->get_results($sql,$sqlArgs);
                //echo "<pre>";print_r($res);
		return $res?$res:array();
	}
	/*function getCoreQuestionsForAssessment($assessment_id,$assessor_id,$kpa_id=0){
		$sql="SELECT cqs.id as score_id, cq.core_question_id,cq.core_question_text,kqcq.core_question_instance_id,kqcq.key_question_instance_id,r.rating,hls.rating_level_order as numericRating,rls.rating_level_scheme_id as scheme_id
			FROM `d_core_question` cq
			inner join h_kq_cq kqcq on kqcq.core_question_id=cq.core_question_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join d_assessment a on kd.diagnostic_id=a.diagnostic_id
			inner join h_assessment_user au on au.assessment_id=a.assessment_id
			left join `h_cq_score` cqs on kqcq.core_question_instance_id=cqs.core_question_instance_id and a.assessment_id=cqs.assessment_id and cqs.assessor_id=au.user_id
			left join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = a.diagnostic_id
            left join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and cqs.d_rating_rating_id=hls.rating_id  and hls.rating_level_id=3
            left join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
			left join d_rating r on cqs.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id 
			where a.assessment_id=?  and au.user_id=?";
		$sqlArgs=array($assessment_id,$assessor_id);
		if($kpa_id>0){
			$sql.=" and kd.kpa_instance_id=?";
			$sqlArgs[]=$kpa_id;
		}
		$sql.="
			order by kqcq.`cq_order` asc;";
		$res=$this->db->get_results($sql,$sqlArgs);
		return $res?$res:array();
	}*/
        function getCoreQuestionsForKQAssessment($assessment_id,$assessor_id,$key_question_instance_id=0,$lang_id=DEFAULT_LANGUAGE){
		$sql="SELECT cqs.id as score_id, cq.core_question_id,hlt.translation_text as core_question_text,kqcq.core_question_instance_id,kqcq.key_question_instance_id,r.rating,hls.rating_level_order as numericRating,rls.rating_level_scheme_id as scheme_id
			FROM `d_core_question` cq
                        inner join h_lang_translation hlt on cq.equivalence_id = hlt.equivalence_id
			inner join h_kq_cq kqcq on kqcq.core_question_id=cq.core_question_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join d_assessment a on kd.diagnostic_id=a.diagnostic_id
			inner join h_assessment_user au on au.assessment_id=a.assessment_id
			left join `h_cq_score` cqs on kqcq.core_question_instance_id=cqs.core_question_instance_id and a.assessment_id=cqs.assessment_id and cqs.assessor_id=au.user_id
			left join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = a.diagnostic_id
            left join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and cqs.d_rating_rating_id=hls.rating_id  and hls.rating_level_id=3
            left join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
			left join (select hlt.translation_text as rating,r.rating_id from h_lang_translation hlt INNER JOIN d_rating r on r.equivalence_id = hlt.equivalence_id   WHERE  hlt.language_id=?) r on cqs.d_rating_rating_id = r.rating_id and hls.rating_id=r.rating_id 
			where a.assessment_id=?  and au.user_id=? and hlt.language_id=?";
		$sqlArgs=array($lang_id,$assessment_id,$assessor_id,$lang_id);
		if($key_question_instance_id>0){
			$sql.=" and kkq.key_question_instance_id=?";
			$sqlArgs[]=$key_question_instance_id;
		}
		$sql.="
			order by kqcq.`cq_order` asc;";
		$res=$this->db->get_results($sql,$sqlArgs);
		return $res?$res:array();
	}
	function getJudgementalStatementsForDiagnostic($diagnostic_id){
			$res=$this->db->get_results("SELECT '' as files, '' as score_id,js.judgement_statement_id,hlt.translation_text judgement_statement_text,'' as evidence_text, cqjs.judgement_statement_instance_id,cqjs.core_question_instance_id,'' as rating,NULL as numericRating
			FROM `d_judgement_statement` js
			inner join h_cq_js_instance cqjs on js.judgement_statement_id=cqjs.judgement_statement_id
			inner join h_kq_cq kqcq on kqcq.core_question_instance_id=cqjs.core_question_instance_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join h_lang_translation hlt on js.equivalence_id = hlt.equivalence_id 			
			where kd.diagnostic_id=?  and hlt.translation_type_id=4 and hlt.language_id={$this->lang} 
			order by cqjs.`js_order` asc",array($diagnostic_id));
		return $res?$res:array();
	}
        
        function getJudgementalStatementsForDiagnosticLang($diagnostic_id,$lang_id=9){
			$res=$this->db->get_results("SELECT '' as files, '' as score_id,js.judgement_statement_id,hlt.translation_text judgement_statement_text,'' as evidence_text, cqjs.judgement_statement_instance_id,cqjs.core_question_instance_id,'' as rating,NULL as numericRating
			FROM `d_judgement_statement` js
			inner join h_cq_js_instance cqjs on js.judgement_statement_id=cqjs.judgement_statement_id
			inner join h_kq_cq kqcq on kqcq.core_question_instance_id=cqjs.core_question_instance_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join h_lang_translation hlt on js.equivalence_id = hlt.equivalence_id 			
			where kd.diagnostic_id=?  and hlt.translation_type_id=4 and hlt.language_id=?
			order by cqjs.`js_order` asc",array($diagnostic_id,$lang_id));
		return $res?$res:array();
	}
        
	/*function getJudgementalStatementsForDiagnostic($diagnostic_id){
			$res=$this->db->get_results("SELECT '' as files, '' as score_id,js.judgement_statement_id,js.judgement_statement_text,'' as evidence_text, cqjs.judgement_statement_instance_id,cqjs.core_question_instance_id,'' as rating,NULL as numericRating
			FROM `d_judgement_statement` js
			inner join h_cq_js_instance cqjs on js.judgement_statement_id=cqjs.judgement_statement_id
			inner join h_kq_cq kqcq on kqcq.core_question_instance_id=cqjs.core_question_instance_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=?
			order by cqjs.`js_order` asc",array($diagnostic_id));
		return $res?$res:array();
	}
	
	function getJudgementalStatementsForAssessment($assessment_id,$assessor_id,$kpa_id=0){
		$sql="SELECT 
			if(fs.score_id is NULL,cqjs.judgement_statement_instance_id,fs.score_id) as groupId, GROUP_CONCAT(  CONCAT(f.file_id,'|',f.file_name) SEPARATOR '||') as files, fs.score_id,js.judgement_statement_id,js.judgement_statement_text,fs.evidence_text, cqjs.judgement_statement_instance_id,cqjs.core_question_instance_id,r.rating,hls.rating_level_order as numericRating,rls.rating_level_scheme_id as scheme_id
			FROM `d_judgement_statement` js
			inner join h_cq_js_instance cqjs on js.judgement_statement_id=cqjs.judgement_statement_id
			inner join h_kq_cq kqcq on kqcq.core_question_instance_id=cqjs.core_question_instance_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join d_assessment a on kd.diagnostic_id=a.diagnostic_id
			inner join h_assessment_user au on au.assessment_id=a.assessment_id
			left join `f_score` fs on cqjs.judgement_statement_instance_id=fs.judgement_statement_instance_id and a.assessment_id=fs.assessment_id and fs.assessor_id=au.user_id and isFinal=1
			left join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = a.diagnostic_id
            left join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and fs.rating_id=hls.rating_id  and hls.rating_level_id=4
            left join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
			left join d_rating r on fs.rating_id = r.rating_id and hls.rating_id=r.rating_id 
			left join h_score_file sf on sf.score_id=fs.score_id
            left join d_file f on sf.file_id=f.file_id
			where a.assessment_id=?  and au.user_id=?";
		$sqlArgs=array($assessment_id,$assessor_id);
		if($kpa_id>0){
			$sql.=" and kd.kpa_instance_id=?";
			$sqlArgs[]=$kpa_id;
		}
		$sql.="
			group by groupId
			order by cqjs.`js_order` asc";
                //echo $this->db->regenerateQuery($sql,$sqlArgs);
		$res=$this->db->get_results($sql,$sqlArgs);
		return $res?$res:array();
	}*/
        
        function getJudgementalStatementsForAssessment($assessment_id,$assessor_id,$kpa_id=0,$lang_id=DEFAULT_LANGUAGE,$external=0,$userKpas = array(),$isLeadAssessorKpa=0){
                    
                    $cond = '';
                    $score = ' fs.score_id';
                    if($isLeadAssessorKpa)
                       $score = '0 as score_id';
                    $sql="SELECT 
			if(fs.score_id is NULL,cqjs.judgement_statement_instance_id,fs.score_id) as groupId, GROUP_CONCAT(  CONCAT(f.file_id,'|',f.file_name) SEPARATOR '||') as files, $score,js.judgement_statement_id,hlt.translation_text judgement_statement_text,fs.evidence_text, cqjs.judgement_statement_instance_id,cqjs.core_question_instance_id,r.rating,hls.rating_level_order as numericRating,rls.rating_level_scheme_id as scheme_id
			FROM `d_judgement_statement` js
			inner join h_cq_js_instance cqjs on js.judgement_statement_id=cqjs.judgement_statement_id
			inner join h_kq_cq kqcq on kqcq.core_question_instance_id=cqjs.core_question_instance_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join d_assessment a on kd.diagnostic_id=a.diagnostic_id";
                    if ($external == 1){
                        $sql .= ' inner join h_assessment_external_team au on au.assessment_id=a.assessment_id';
                        if(!empty($userKpas))
                            $cond = " and kd.kpa_instance_id IN (".implode(",",$userKpas).")";
                    }else
                         $sql .= ' inner join h_assessment_user au on au.assessment_id=a.assessment_id';
            
                    $sql .= " inner join h_lang_translation hlt on js.equivalence_id = hlt.equivalence_id 	
			left join `f_score` fs on cqjs.judgement_statement_instance_id=fs.judgement_statement_instance_id and a.assessment_id=fs.assessment_id and fs.assessor_id=au.user_id and isFinal=1
			left join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = a.diagnostic_id
                        left join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and fs.rating_id=hls.rating_id  and hls.rating_level_id=4
                        left join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
			left join (select hlt.translation_text as rating,r.rating_id from h_lang_translation hlt INNER JOIN d_rating r on r.equivalence_id = hlt.equivalence_id   WHERE  hlt.language_id=?) r on fs.rating_id = r.rating_id and hls.rating_id=r.rating_id 
			left join h_score_file sf on sf.score_id=fs.score_id
                        left join d_file f on sf.file_id=f.file_id
			where a.assessment_id=? $cond and au.user_id=? and hlt.translation_type_id=4 and hlt.language_id=?";
		$sqlArgs=array($lang_id,$assessment_id,$assessor_id,$lang_id);
		if($kpa_id>0){
			$sql.=" and kd.kpa_instance_id=?";
			$sqlArgs[]=$kpa_id;
		}
		 $sql.="
			group by groupId
			order by cqjs.`js_order` asc";
                //echo $this->db->regenerateQuery($sql,$sqlArgs);
		$res=$this->db->get_results($sql,$sqlArgs);
		return $res?$res:array();
	}
	
	function getKpasCompletenessStatus($assessment_id,$assessor_id){
		$res=$this->db->get_results("SELECT kd.kpa_instance_id, sum(hls.rating_level_order is not  NULL) as filled,sum(hls.rating_level_order is NULL) as unfilled
			FROM `d_judgement_statement` js
			inner join h_cq_js_instance cqjs on js.judgement_statement_id=cqjs.judgement_statement_id
			inner join h_kq_cq kqcq on kqcq.core_question_instance_id=cqjs.core_question_instance_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			inner join d_assessment a on kd.diagnostic_id=a.diagnostic_id
			inner join h_assessment_user au on au.assessment_id=a.assessment_id
			left join `f_score` fs on cqjs.judgement_statement_instance_id=fs.judgement_statement_instance_id and a.assessment_id=fs.assessment_id and fs.assessor_id=au.user_id and isFinal=1
			left join h_diagnostic_rating_level_scheme rls on rls.diagnostic_id = a.diagnostic_id
            left join h_rating_level_scheme hls on hls.rating_scheme_id =rls.rating_level_scheme_id and fs.rating_id=hls.rating_id  and hls.rating_level_id=4
            left join d_rating_level rl on rl.rating_level_id = hls.rating_level_id 
			where a.assessment_id=?  and au.user_id=?
            group by kd.kpa_instance_id",array($assessment_id,$assessor_id));
		return $res?$res:array();
	}
	
	function updateAssessorKeyNotesStatus($assessment_id,$status){
		return $this->db->update("d_assessment",array("isAssessorKeyNotesApproved"=>$status),array("assessment_id"=>$assessment_id));
	}
	
	function updateAqsDataIdInAssessment($assmntId_or_grpAssmntId,$assessment_type_id,$aqsdata_id){
		if($assessment_type_id==1){
			return $this->db->update("d_assessment",array("aqsdata_id"=>$aqsdata_id),array("assessment_id"=>$assmntId_or_grpAssmntId));
		}else{
			return $this->db->query("update d_assessment set aqsdata_id=? where assessment_id in (SELECT assessment_id FROM h_assessment_ass_group where group_assessment_id=?);",array($aqsdata_id,$assmntId_or_grpAssmntId));
		}
	}
        
        function updategrpAssessment_AQS($assmntId_or_grpAssmntId,$aqsdata_id){
            return $this->db->update("d_group_assessment",array("grp_aqsdata_id"=>$aqsdata_id),array("group_assessment_id"=>$assmntId_or_grpAssmntId));
        }
	
function getAssessorKeyNotes($assessment_id,$isNotnull=0,$kpa_id=0){
		$sqlArgs=array($assessment_id);
                $sqlCond = '';
                if(!empty($kpa_id)){
                   $sqlCond = ' AND kpa_instance_id = ? ';
                   array_push($sqlArgs, $kpa_id);
                }
                
		$sql="SELECT * FROM assessor_key_notes where assessment_id= ? $sqlCond ;";
		 if($isNotnull>0){
			$sql.=" and length(text_data)>=1";			
		} 
		$res=$this->db->get_results($sql,$sqlArgs);
		return $res?$res:array();
	}
        
	function getAssessorKeyNotesForType($assessment_id,$type,$instance_id){
		$type_instance_id = $type.'_instance_id';
		$sqlArgs=array($assessment_id,$instance_id);
                //$sql="SELECT *  FROM assessor_key_notes where assessment_id= ? and $type_instance_id = ?";
                $sql="SELECT a.*,group_concat(DISTINCT rec_judgement_instance_id) as rec_judgement_instance_id FROM assessor_key_notes a left join h_assessor_key_notes_js b on a.id=b.assessor_key_notes_id where a.rec_type=0 && a.assessment_id= ? and a.$type_instance_id = ? group by a.id";		
		$res=$this->db->get_results($sql,$sqlArgs);
		return $res?$res:array();
                
	}
        
	function getAssessorKeyNotesTypeOrder($assessment_id,$type,$lang=DEFAULT_LANGUAGE,$roleids=array(),$user_id=0){
                //echo print_r($roleids);
                //echo  $user_id;
		$sqlArgs=array($assessment_id);
                $leaderCondition="";
                $arg_array=array();
                $arg_array[]=$assessment_id;
                $arg_array[]=$type;
                if(in_array("1",$roleids) || in_array("2",$roleids) || in_array("5",$roleids) || in_array("6",$roleids)){
                    $leaderCondition="";
                }else if(in_array("3",$roleids)){
                    $leaderCondition=" && n3.leader=?";
                    //$leaderCondition.=" && n3.action_status!=?";
                    $arg_array[]=$user_id;
                    //$arg_array[]=0;
                }
                
		$sql="select xyz1.*,GROUP_CONCAT(kk) as kk1,GROUP_CONCAT(js SEPARATOR '@#@$') as js_text  from (select xyz.*,js_o.show_text,concat('KPA-',kpa_order_f,'/','KQ-',kq_order_f,'/','SQ-',cq_order_f,'/','JS-',js_o.show_text) as kk from (SELECT a.*,f.kpa_order_f,e.kq_order_f,d.cq_order_f,c.js_order_f,((e.kq_order_f-1)*9+(d.cq_order_f-1)*3+c.js_order_f) as final_js_order,hlt.translation_text as js,n3.* FROM assessor_key_notes a 
                      inner join h_assessor_key_notes_js b on a.id=b.assessor_key_notes_id
                      inner join (select *,if(((js_order%3)=0),3,js_order%3) as js_order_f from h_cq_js_instance) c on b.rec_judgement_instance_id=c.judgement_statement_instance_id
                      inner join d_judgement_statement djs on djs.judgement_statement_id=c.judgement_statement_id
                      inner join h_lang_translation hlt on djs.equivalence_id=hlt.equivalence_id && hlt.language_id='".$lang."'
                      inner join (select *,if(((cq_order%3)=0),3,cq_order%3) as cq_order_f from h_kq_cq) d on c.core_question_instance_id=d.core_question_instance_id
                      inner join (select *,if(((kq_order % 3)=0),3,kq_order % 3) as kq_order_f from h_kpa_kq) e on e.key_question_instance_id=d.key_question_instance_id
                      inner join (select *,kpa_order as kpa_order_f from h_kpa_diagnostic) f on f.kpa_instance_id=e.kpa_instance_id
                      left join ( select n1.assessor_key_notes_id as assessor_k_id,n1.from_date,n1.to_date,n1.leader,n1.frequency_report,n1.reporting_authority,n1.action_status,n1.mail_status,GROUP_CONCAT(designation_id) as designations,GROUP_CONCAT(impact_statement SEPARATOR '#--#') as impact_statements,GROUP_CONCAT(assessor_action1_impact_id) as assessor_action1_impact_ids  from h_assessor_action1 n1 left join h_assessor_action1_impact n2 on n1.h_assessor_action1_id=n2.assessor_action1_id group by n1.h_assessor_action1_id) n3 on  n3.assessor_k_id=a.id
                      where assessment_id= ?  and type=? ".$leaderCondition." order by a.rec_type,f.kpa_order,e.kq_order,d.cq_order,c.js_order) xyz 
                      left join d_js_order js_o on xyz.final_js_order=js_o.order_id order by rec_type,kpa_order_f,kq_order_f,cq_order_f,js_order_f) xyz1 group by id order by rec_type,kpa_order_f,kq_order_f,cq_order_f,js_order_f";
                
                
                /*$sql="select xyz.*,js_o.show_text,concat('KPA-',kpa_order_f,'/','KQ-',kq_order_f,'/','SQ-',cq_order_f,'/','JS-',js_o.show_text) as kk from (SELECT a.*,f.kpa_order_f,e.kq_order_f,d.cq_order_f,c.js_order_f,((e.kq_order_f-1)*9+(d.cq_order_f-1)*3+c.js_order_f) as final_js_order FROM assessor_key_notes a 
                      inner join h_assessor_key_notes_js b on a.id=b.assessor_key_notes_id
                      inner join (select *,if(((js_order%3)=0),3,js_order%3) as js_order_f from h_cq_js_instance) c on b.rec_judgement_instance_id=c.judgement_statement_instance_id
                      inner join (select *,if(((cq_order%3)=0),3,cq_order%3) as cq_order_f from h_kq_cq) d on c.core_question_instance_id=d.core_question_instance_id
                      inner join (select *,if(((kq_order % 3)=0),3,kq_order % 3) as kq_order_f from h_kpa_kq) e on e.key_question_instance_id=d.key_question_instance_id
                      inner join (select *,kpa_order as kpa_order_f from h_kpa_diagnostic) f on f.kpa_instance_id=e.kpa_instance_id
                      where assessment_id= ?  and type=?  order by f.kpa_order,e.kq_order,d.cq_order,c.js_order) xyz 
                      left join d_js_order js_o on xyz.final_js_order=js_o.order_id order by kpa_order_f,kq_order_f,cq_order_f,js_order_f ";
                */
		$res=$this->db->get_results($sql,$arg_array);
		return $res?$res:array();
	}
        
        
        
        function getAssessorKeyNotesType($assessment_id,$type){
		$sqlArgs=array($assessment_id);
		$sql="SELECT * FROM assessor_key_notes where assessment_id= ?  and type=?;";		
		$res=$this->db->get_results($sql,array($assessment_id,$type));
		return $res?$res:array();
	}
	function getInternalAssessor($assessment_id){
		$sqlArgs=array($assessment_id);
		$sql="SELECT * FROM h_assessment_user where assessment_id= ?  and role=?;";		
		$res=$this->db->get_row($sql,array($assessment_id,3));
		return $res?$res['user_id']:array();
	}
        
        function getAssessorKeyNoteById($rec_id,$lang=DEFAULT_LANGUAGE){
            
                $sqlArgs=array($rec_id);
		//$sql="SELECT * FROM assessor_key_notes where id= ?;";
                /*$sql="select xyz1.*,GROUP_CONCAT(kk) as kk1 from (select xyz.*,js_o.show_text,concat('KPA-',kpa_order_f,'/','KQ-',kq_order_f,'/','SQ-',cq_order_f,'/','JS-',js_o.show_text) as kk from (SELECT a.*,f.kpa_order_f,e.kq_order_f,d.cq_order_f,c.js_order_f,((e.kq_order_f-1)*9+(d.cq_order_f-1)*3+c.js_order_f) as final_js_order FROM assessor_key_notes a 
                      inner join h_assessor_key_notes_js b on a.id=b.assessor_key_notes_id
                      inner join (select *,if(((js_order%3)=0),3,js_order%3) as js_order_f from h_cq_js_instance) c on b.rec_judgement_instance_id=c.judgement_statement_instance_id
                      inner join (select *,if(((cq_order%3)=0),3,cq_order%3) as cq_order_f from h_kq_cq) d on c.core_question_instance_id=d.core_question_instance_id
                      inner join (select *,if(((kq_order % 3)=0),3,kq_order % 3) as kq_order_f from h_kpa_kq) e on e.key_question_instance_id=d.key_question_instance_id
                      inner join (select *,kpa_order as kpa_order_f from h_kpa_diagnostic) f on f.kpa_instance_id=e.kpa_instance_id
                      where a.id=?  order by f.kpa_order,e.kq_order,d.cq_order,c.js_order) xyz 
                      left join d_js_order js_o on xyz.final_js_order=js_o.order_id order by kpa_order_f,kq_order_f,cq_order_f,js_order_f) xyz1 group by id order by kpa_order_f,kq_order_f,cq_order_f,js_order_f";
                */
                $sql="select xyz1.*,GROUP_CONCAT(kk) as kk1,GROUP_CONCAT(js SEPARATOR '@#@$') as js_text,GROUP_CONCAT(cq SEPARATOR '@#@$') as cq_text,GROUP_CONCAT(kq SEPARATOR '@#@$') as kq_text,GROUP_CONCAT(kpa SEPARATOR '@#@$') as kpa_text  from (select xyz.*,js_o.show_text,concat('KPA-',kpa_order_f,'/','KQ-',kq_order_f,'/','SQ-',cq_order_f,'/','JS-',js_o.show_text) as kk from (SELECT a.*,f.kpa_order_f,e.kq_order_f,d.cq_order_f,c.js_order_f,((e.kq_order_f-1)*9+(d.cq_order_f-1)*3+c.js_order_f) as final_js_order,hlt.translation_text as js,hlt_cq.translation_text as cq,hlt_kq.translation_text as kq,hlt_kpa.translation_text as kpa FROM assessor_key_notes a 
                      inner join h_assessor_key_notes_js b on a.id=b.assessor_key_notes_id
                      inner join (select *,if(((js_order%3)=0),3,js_order%3) as js_order_f from h_cq_js_instance) c on b.rec_judgement_instance_id=c.judgement_statement_instance_id
                      inner join d_judgement_statement djs on djs.judgement_statement_id=c.judgement_statement_id
                      inner join h_lang_translation hlt on djs.equivalence_id=hlt.equivalence_id && hlt.language_id='".$lang."'
                      inner join (select *,if(((cq_order%3)=0),3,cq_order%3) as cq_order_f from h_kq_cq) d on c.core_question_instance_id=d.core_question_instance_id
                      inner join d_core_question dcq on dcq.core_question_id=d.core_question_id
                      inner join h_lang_translation hlt_cq on dcq.equivalence_id=hlt_cq.equivalence_id && hlt_cq.language_id='".$lang."'
                      inner join (select *,if(((kq_order % 3)=0),3,kq_order % 3) as kq_order_f from h_kpa_kq) e on e.key_question_instance_id=d.key_question_instance_id
                      
                      inner join d_key_question dkq on dkq.key_question_id=e.key_question_id
                      inner join h_lang_translation hlt_kq on dkq.equivalence_id=hlt_kq.equivalence_id && hlt_kq.language_id='".$lang."'
                      
                      inner join (select *,kpa_order as kpa_order_f from h_kpa_diagnostic) f on f.kpa_instance_id=e.kpa_instance_id
                      
                      inner join d_kpa dka on dka.kpa_id=f.kpa_id
                      inner join h_lang_translation hlt_kpa on dka.equivalence_id=hlt_kpa.equivalence_id && hlt_kpa.language_id='".$lang."'
                       
                      where a.id=?  order by a.rec_type,f.kpa_order,e.kq_order,d.cq_order,c.js_order) xyz 
                      left join d_js_order js_o on xyz.final_js_order=js_o.order_id order by rec_type,kpa_order_f,kq_order_f,cq_order_f,js_order_f) xyz1 group by id order by rec_type,kpa_order_f,kq_order_f,cq_order_f,js_order_f";
                
		$res=$this->db->get_row($sql,array($rec_id));
		return $res?$res:array();
        }
        
	function getAssessorKeyNotesLevel($assessment_id,$level){
		$sqlArgs=array($assessment_id);
		$sql="SELECT * FROM assessor_key_notes where assessment_id= ?  and $level is not null";
		$res=$this->db->get_results($sql,array($assessment_id));
		return $res?$res:array();
	}
	function getDiagnosticRatingScheme($diagnostic_id){
		//$res=$this->db->get_results("SELECT rating_id,`order`,if(is_judgestmt_rating=1,'js','other') as type FROM `h_diagnostic_rating_scheme` where diagnostic_id=? ;",array($diagnostic_id));
		$res=$this->db->get_results("SELECT rating_id,rating_level_text `type`,rating_level_order `order`,rating_level_scheme_id as scheme FROM h_diagnostic_rating_level_scheme rls INNER JOIN h_rating_level_scheme hls on  
				rls.rating_level_scheme_id = hls.rating_scheme_id inner join d_rating_level rl on rl.rating_level_id = hls.rating_level_id
				where rls.diagnostic_id=?",array($diagnostic_id));
		return $res?$res:array();
	}
	
	function insertJudgementStatementScore($js_id,$assessment_id,$assessor_id,$added_by,$rating_id,$evidence_text=''){
		if($this->db->insert('f_score',array("judgement_statement_instance_id"=>$js_id,"assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id,"added_by"=>$added_by,"rating_id"=>$rating_id,"evidence_text"=>$evidence_text,"isFinal"=>1,"date_added"=>date("Y-m-d H:i:s")))){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	
	function updateJudgementStatementScore($js_id,$assessment_id,$assessor_id,$added_by,$rating_id,$evidence_text=''){
		if($this->db->update("f_score",array("isFinal"=>0),array("judgement_statement_instance_id"=>$js_id,"assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id))){
			return $this->insertJudgementStatementScore($js_id,$assessment_id,$assessor_id,$added_by,$rating_id,$evidence_text);
		}else
			return false;
	}
	function updateCompleteStatus($assessment_id,$assessor_id,$completedPerc,$isLead=0){
                
               //echo $assessor_id;
                $table = 'h_assessment_external_team';
                $params = array("percComplete"=>$completedPerc);
                if($completedPerc < 100)
                    $params['isFilled'] = 0;
                if($isLead == 1)
                    $table = 'h_assessment_user';
		if($this->db->update($table,$params,array("assessment_id"=>$assessment_id,"user_id"=>$assessor_id))){
			return true;
		}else
			return false;
	}
	
	function insertCoreQuestionScore($cq_id,$assessment_id,$assessor_id,$rating_id){
		if($this->db->insert('h_cq_score',array("core_question_instance_id"=>$cq_id,"assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id,"d_rating_rating_id"=>$rating_id))){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	
	function updateCoreQuestionScore($cq_id,$assessment_id,$assessor_id,$rating_id){
		return $this->db->update('h_cq_score',array("d_rating_rating_id"=>$rating_id),array("core_question_instance_id"=>$cq_id,"assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id));
	}
	
	function deleteCoreQuestionScore($cq_id,$assessment_id,$assessor_id){
		return $this->db->delete('h_cq_score',array("core_question_instance_id"=>$cq_id,"assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id));
	}
	function deleteInternalJudgementStatementScore($assessment_id,$assessor_id){
		return $this->db->delete('f_score',array("assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id));
	}
	
	function insertKeyQuestionScore($kq_id,$assessment_id,$assessor_id,$rating_id){
		if($this->db->insert('h_kq_instance_score',array("key_question_instance_id"=>$kq_id,"assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id,"d_rating_rating_id"=>$rating_id))){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	
	function updateKeyQuestionScore($kq_id,$assessment_id,$assessor_id,$rating_id){
		return $this->db->update('h_kq_instance_score',array("d_rating_rating_id"=>$rating_id),array("key_question_instance_id"=>$kq_id,"assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id));
	}
	
	function deleteKeyQuestionScore($kq_id,$assessment_id,$assessor_id){
		return $this->db->delete('h_kq_instance_score',array("key_question_instance_id"=>$kq_id,"assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id));
	}
	
	function insertKpaScore($kpa_id,$assessment_id,$assessor_id,$rating_id){
		if($this->db->insert('h_kpa_instance_score',array("kpa_instance_id"=>$kpa_id,"assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id,"d_rating_rating_id"=>$rating_id))){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	
	function updateKpaScore($kpa_id,$assessment_id,$assessor_id,$rating_id){
		return $this->db->update('h_kpa_instance_score',array("d_rating_rating_id"=>$rating_id),array("kpa_instance_id"=>$kpa_id,"assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id));
	}
	
	function deleteKpaScore($kpa_id,$assessment_id,$assessor_id){
		return $this->db->delete('h_kpa_instance_score',array("kpa_instance_id"=>$kpa_id,"assessment_id"=>$assessment_id,"assessor_id"=>$assessor_id));
	}
	
	function updateAssessmentPercentage($assessment_id,$assessor_id,$percentage,$is_collaborative = 0,$external = 0,$is_submit=0,$isLeadAssessor=0,$isLeadSave=0){
                
            $tablleName = 'h_assessment_user';
             $data = array("percComplete"=>$percentage,'ratingInputDate'=> date('Y-m-d h:i:s'));
            if($isLeadSave == 1){
               // echo "die";die;
                $data['isLeadSave'] = $isLeadSave;
            }
           
            $params = array("assessment_id"=>$assessment_id,"user_id"=>$assessor_id);
            if($is_collaborative &&  $is_submit == 1) {
                $tablleName = 'h_assessment_external_team';
                //if($is_submit == 1){
                    $data = array("percComplete"=>$percentage,'isFilled'=>1,'ratingInputDate'=> date('Y-m-d h:i:s'));
                    $params = array("assessment_id"=>$assessment_id);
                    if($isLeadAssessor == 0)
                        $params['user_id'] = $assessor_id;
                //}
                //array("percComplete"=>$percentage,'isFilled'=>1);
            }else if($is_collaborative && $external &&  $is_submit == 0) {
                $tablleName = 'h_assessment_external_team';                
            }else if($is_submit) {
             //$data = array("percComplete"=>$percentage,'ratingInputDate'=> date('Y-m-d h:i:s'));
                $data = array("percComplete"=>$percentage,'isFilled'=>1,'ratingInputDate'=> date('Y-m-d h:i:s'));
                $params = array("assessment_id"=>$assessment_id,"role"=>3);
            }
           //print_r($data);echo $tablleName;die;
            return $this->db->update($tablleName,$data,$params);
	}
        
        function updateAssessmentReplicate($assessment_id){
                                
		return $this->db->update('d_assessment',array("is_replicated"=>1,"replicated_date_time"=>date("Y-m-d H:i:s")),array("assessment_id"=>$assessment_id));
	}
	
	function updateAssessmentPercentageAndStatus($assessment_id,$assessor_id,$percentage,$status){
		$data=array("percComplete"=>$percentage,"isFilled"=>$status);
		if($status>0)
			$data['ratingInputDate']=date("Y-m-d H:i:s");
		return $this->db->update('h_assessment_user',$data,array("assessment_id"=>$assessment_id,"user_id"=>$assessor_id));
	}
	
	function addAssessorKeyNote($assessment_id,$instance_type,$instance_type_id,$text,$type,$aknJS=array()){
                //echo "ddd".$aknJS;                
		if($this->db->insert('assessor_key_notes',array("assessment_id"=>$assessment_id,$instance_type=>$instance_type_id,"text_data"=>$text,"type"=>$type))){
		     
                     if(count($aknJS)<=0){	
                     return $this->db->get_last_insert_id();
                     }else{
                         $last_id=$this->db->get_last_insert_id();
                         
                         foreach($aknJS as $key=>$val){
                             //echo $val;
                            if(!$this->db->insert('h_assessor_key_notes_js',array("assessor_key_notes_id"=>$last_id,"rec_judgement_instance_id"=>$val))){
                               return false; 
                            }
                         }
                         
                         return $last_id;
                         
                     }
                     
		}else
			return false;
	}
	
	function updateAssessorKeyNote($keynote_id,$text,$type='',$aknJS=array(),$old_js=array()){
                  $c_old_js=array();               
                //echo "ddd".$aknJS;                
		if($this->db->update('assessor_key_notes',array("text_data"=>$text,"type"=>$type),array("id"=>$keynote_id))){
                    
                    
                    
                    if(count($aknJS)<=0){
                        
                     if(!$this->db->delete('h_assessor_key_notes_js',array("assessor_key_notes_id"=>$keynote_id))){
                       return false; 
                     }
                     
                     return true;   
                    }else{
                        
                         
                        
                        foreach($aknJS as $key=>$val){
                            //print_r($old_js);
                            //echo $val;
                            if(in_array($val,$old_js)){
                                
                                    if(!$this->db->update('h_assessor_key_notes_js',array("assessor_key_notes_id"=>$keynote_id,"rec_judgement_instance_id"=>$val),array("assessor_key_notes_id"=>$keynote_id,"rec_judgement_instance_id"=>$val))){
                                       return false; 
                                    }
                                    $c_old_js[]=$val;
                                    }else{

                                    if(!$this->db->insert('h_assessor_key_notes_js',array("assessor_key_notes_id"=>$keynote_id,"rec_judgement_instance_id"=>$val))){
                                       return false; 
                                    }

                            }
                            
                           
                            
                         }
                         
                          $js_left=array_diff($old_js,$c_old_js);
                          //print_r($js_left);
                            if(count($js_left)>0){
                                foreach($js_left as $key_1=>$val_1){
                                    
                                    if(!$this->db->delete('h_assessor_key_notes_js',array("assessor_key_notes_id"=>$keynote_id,"rec_judgement_instance_id"=>$val_1))){
                                    return false; 
                                    }
                                }
                            }
                         
                        return true;
                    }
                }
                
                return false;
	}
	
	function deleteAssessorKeyNote($keynote_id){
                                
		if($this->db->delete('assessor_key_notes',array("id"=>$keynote_id))){
                    
                    if(!$this->db->delete('h_assessor_key_notes_js',array("assessor_key_notes_id"=>$keynote_id))){
                       return false; 
                    }
                    
                    return true;
                }
                
                return false;
	}
	
	public function addUploadedFile($fileName,$uploaded_by,$size=0){
		if($this->db->insert('d_file',array("file_name"=>$fileName,"uploaded_by"=>$uploaded_by,'file_size'=>$size,"upload_date"=>date("Y-m-d H:i:s")))){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	
	public function getFile($file_id){
		$sql="SELECT 
				f.*,sf.score_file_id,sf.score_id
				FROM `d_file` f
				left join `h_score_file` sf on f.file_id=sf.file_id 
				where f.file_id=?";
		return $this->db->get_row($sql,array($file_id));
	}
	
	public function linkFileToScore($score_id,$file_id){
		return $this->db->insert("h_score_file",array("score_id"=>$score_id,"file_id"=>$file_id));
	}
	
	public function unlinkFileFromScore($score_file_id){
		return $this->db->delete("h_score_file",array("score_file_id"=>$score_file_id));
	}
	
	public function deleteFileFromDB($file_id){
		return $this->db->delete("d_file",array("file_id"=>$file_id));
	}
	
	public static function decodeFileArray($filesString){
		$files=array();
		if($filesString!=""){
			$temp= explode("||",$filesString);
			foreach($temp as $t){
				$t2=explode("|",$t);
				$files[$t2[0]]=$t2[1];
			}
		}
		return $files;
	}
	
	public static function getFileExt($fileName){
		$temp=explode(".",$fileName);
		return strtolower(array_pop($temp));
	}
	
	/* public static function calculateStatementResult($res){
		$valuesCount=array('s1'=>0,'s2'=>0,'s3'=>0,'s4'=>0);
		for($i=0;$i<count($res);$i++)
			$valuesCount["s".$res[$i]]++;
		
		if($valuesCount['s1']>1)
			return 1;
		else if($valuesCount['s2']>1)
			return 2;
		else if($valuesCount['s3']>1)
			return 3;
		else if($valuesCount['s4']>1)
			return 4;
		else if($valuesCount['s1']==0)
			return 3;
		else if($valuesCount['s2']==0)
			return 3;
		else if($valuesCount['s3']==0)
			return 2;
		else if($valuesCount['s4']==0)
			return 2;
		else
			return 0;
	} */
		public static function calculateStatementResult($res,$ratingScheme,$level,$kpaJs_ratings = array(),$kpaSq_ratings=array()){
			$resRating = 0;
			//echo 'l: '.$level;
			//print_r($kpaJs_ratings);
			//print_r($kpaSq_ratings);
			switch($ratingScheme){
                                case 4 : 
				case 2 : //currently scheme for teacher review	
						if($level==4){//judgement statement
							$valuesCount=array('s1'=>0,'s2'=>0,'s3'=>0,'s4'=>0);
							for($i=0;$i<count($res);$i++)
								$valuesCount["s".$res[$i]]++;
							 if( ($valuesCount['s3']+$valuesCount['s4'])==3 )//3 mostly/always->exceptional
							 	return 5;
							 else if( ($valuesCount['s3']+$valuesCount['s4'])==2 )//2mostly/always->proficient
							 	return 4;
							 else if( ($valuesCount['s3']+$valuesCount['s4'])==1 )//1 mostly/always->developing
							 	return 3;
							 else if( $valuesCount['s2']>=2)//2 or more mostly-> emerging
							 	return 2;
							 else if($valuesCount['s1']>=2)//2 rarely->foundation
							 	return 1;
							 else 
							 	return 0;							 
						}
						else if($level==3){//SQ ratings received-no rating at KQ level
							//echo 'a';
                                                    if($ratingScheme==4){
                                                    $valuesCount = array('s1'=>0,'s2'=>0,'s3'=>0,'s4'=>0,'s5'=>0);    
                                                     for($i=0;$i<count($res);$i++)
						         $valuesCount["s".$res[$i]]++;
                                                     
                                                     $ftot=0;
                                                     $tot=(1*$valuesCount['s1'])+(2*$valuesCount['s2'])+(3*$valuesCount['s3'])+(4*$valuesCount['s4'])+(5*$valuesCount['s5']);
                                                     $ftot=round($tot/3);
                                                     return $ftot;   
                                                    }else{
							return 6;
                                                    }
						}
						else if($level==2 && $ratingScheme==2){//rating at KPA level based on JS ratings;
							if(empty($kpaJs_ratings)||empty($kpaSq_ratings))
								return 0;
							$valuesCount=array('s1'=>0,'s2'=>0,'s3'=>0,'s4'=>0);
							for($i=0;$i<count($kpaJs_ratings);$i++)
								$valuesCount["s".$kpaJs_ratings[$i]]++;
							
							$sqValuesCount = array('s1'=>0,'s2'=>0,'s3'=>0,'s4'=>0,'s5'=>0);
							for($i=0;$i<count($kpaSq_ratings);$i++)
								$sqValuesCount["s".$kpaSq_ratings[$i]]++;
							//print_r($valuesCount);print_r($sqValuesCount);echo $sqValuesCount['s4']+$sqValuesCount['s5'];
								
							if(($valuesCount['s4'] + $valuesCount['s3'])>=19)//mostly and/or always rating 	
								return 5;
							else if(($valuesCount['s4'] + $valuesCount['s3'])>=10 && ($valuesCount['s4'] + $valuesCount['s3'])<=18 ){//mostly and/or always rating								
								if(($sqValuesCount['s4']+$sqValuesCount['s5'])>=4)
									return 4;								
								else 
									return 3;
							}
							else if( ($valuesCount['s4'] + $valuesCount['s3'])>=6 && ($valuesCount['s4'] + $valuesCount['s3'])<=9)	//mostly and/or always rating
								return 3;
							else if( ($valuesCount['s4'] + $valuesCount['s3'])>=3 && ($valuesCount['s4'] + $valuesCount['s3'])<=5 )//mostly and/or always rating
								return 2;
							else if(($valuesCount['s4'] + $valuesCount['s3'])>=0 && ($valuesCount['s4'] + $valuesCount['s3'])<=2)//mostly and/or always rating
								return 1;
							else 
								return 0;														
						}else if($level==2 && $ratingScheme==4){
                                                        $valuesCount=array('s1'=>0,'s2'=>0,'s3'=>0,'s4'=>0,'s5'=>0);
							for($i=0;$i<count($res);$i++)
								$valuesCount["s".$res[$i]]++;
                                                        
                                                        $sqValuesCount = array('s1'=>0,'s2'=>0,'s3'=>0,'s4'=>0,'s5'=>0);
							for($i=0;$i<count($kpaSq_ratings);$i++)
								$sqValuesCount["s".$kpaSq_ratings[$i]]++;
                                                        
                                                        $ftot=0;
                                                        $tot=(1*$valuesCount['s1'])+(2*$valuesCount['s2'])+(3*$valuesCount['s3'])+(4*$valuesCount['s4'])+(5*$valuesCount['s5']);
                                                        $ftot=round($tot/3);
                                                        //return $ftot;
                                                        
                                                        if($sqValuesCount['s5']>3){
                                                            return 5;
                                                        }else if($valuesCount['s5']>1){
							    return 5;
                                                        }else if($sqValuesCount['s4']>3){
                                                            return 4;
                                                        }else if($valuesCount['s4']>1){
							    return 4;
                                                        }else if($ftot>0){
                                                            return $ftot;
                                                        }else{
					                    return 0;
                                                        }
                                                        
                                                        /*if($sqValuesCount['s5']>3)
                                                            return 5;
                                                        else if($valuesCount['s5']>1)
							    return 5;
                                                        else if($sqValuesCount['s4']>3)
                                                            return 4;
                                                        else if($valuesCount['s4']>1)
							    return 4;
                                                        else if($valuesCount['s3']>1)
							    return 3;
                                                        else if($valuesCount['s2']>1)
							    return 2;
                                                        else if($valuesCount['s1']>1)
							    return 1;
						        else if($valuesCount['s1']==0 && $valuesCount['s2']==0)
					                    return 4;
				                        else if($valuesCount['s1']==0 && $valuesCount['s3']==0)
					                    return 4;
                                                        else if($valuesCount['s1']==0 && $valuesCount['s4']==0)
					                    return 3;
                                                        else if($valuesCount['s1']==0 && $valuesCount['s5']==0)
					                    return 3;
                                                        else if($valuesCount['s2']==0 && $valuesCount['s3']==0)
					                    return 4;
                                                        else if($valuesCount['s2']==0 && $valuesCount['s4']==0)
					                    return 3;
                                                        else if($valuesCount['s2']==0 && $valuesCount['s5']==0)
					                    return 3;
                                                        else if($valuesCount['s3']==0 && $valuesCount['s4']==0)
					                    return 2;
                                                        else if($valuesCount['s3']==0 && $valuesCount['s5']==0)
					                    return 2;
                                                        else if($valuesCount['s4']==0 && $valuesCount['s5']==0)
					                    return 2;
                                                        else
					                    return 0; 
                                                        */
                                                        
                                                }
						break;
                                case 5:                
				case 1: //school reviews have this scheme currently	
					$valuesCount=array('s1'=>0,'s2'=>0,'s3'=>0,'s4'=>0);
					for($i=0;$i<count($res);$i++)
						$valuesCount["s".$res[$i]]++;
						if($valuesCount['s1']>1)
							return 1;
						else if($valuesCount['s2']>1)
							return 2;
						else if($valuesCount['s3']>1)
							return 3;
						else if($valuesCount['s4']>1)
							return 4;
						else if($valuesCount['s1']==0)
							return 3;
						else if($valuesCount['s2']==0)
							return 3;
						else if($valuesCount['s3']==0)
							return 2;
						else if($valuesCount['s4']==0)
							return 2;
						else
							return 0;
						break;	
			}			
		}	
	
	public static function getAssessorKeyNoteHtmlRow($kpa7='',$kpa_id,$akn_id,$text,$type,$attrbutes='',$addDelete=1,$type_q="",$jsDrop=array(),$jsselected=array()){
		$akn_id = $akn_id =='new'?'new_'.uniqid():$akn_id;
                $options="";
                //$kpa_count=$_GET['kpa_no'];
                if($type_q=="kpa"){
                    $options.='Choose a judgement statement : 
                   <select name="js['.$type.']['.$kpa_id.']['.$akn_id.'][]" id="js_'.$type.'_'.$kpa_id.'_'.$akn_id.'"  class="form-control rec_dropdown" required multiple>';   
                   foreach($jsDrop as $key=>$val){
                      $options.='<option value="'.$val['judgement_statement_instance_id'].'"';
                      if(in_array($val['judgement_statement_instance_id'],$jsselected)) $options.=' selected="selected" ';
                      $options.=' >'.$val['show_text'].'. '.$val['judgement_statement_text'].'</option>'; 
                      //print_r($jsselected);
                      //print_r($val['judgement_statement_instance_id']);
                   }
                   $selected_js_id=implode(',',$jsselected);
                   $good="'good'";
                   //$options.='</select><span><a href="?controller=diagnostic&action=goodlookslike&type=kpa&instance_id='.$selected_js_id.'" target="_blank">&nbsp;&nbsp;<span style="color:#000000;">Click here to view </span><span style="color:blue;">What '.$good.' looks like?</span></a></span>';
                   $options.='</select>';

                   if($kpa7!=1){
                       $options.= '<span><a href="#" class="good_stmnt" id="js_'.$type.'_'.$kpa_id.'_'.$akn_id.'">&nbsp;&nbsp;<span style="color:#000000;">Click here to view </span><span style="color:blue;">What '.$good.' looks like?</span></a></span>';
                       $options.='<input type="hidden" id="goodstatementurl" value="'.SITEURL.'index.php?controller=diagnostic&action=goodlookslike&type=kpa&instance_id=">';
                   }

                //$options.='</select><span><span style="color:#000000;">Click here to view </span><span style="color:blue;">What '.$good.' looks like?</span></a></span></span>';
                    //}
                }
                
		if(!empty($type)){
		return '<dl class="fldList keynote-wrap '.$type.'" id="kn-id-'.$akn_id.'"><dd class="ml0" style="background:none;">
                        
                        '.$options.'
                    
                       <textarea cols="20" rows="4" name="data['.$type.']['.$kpa_id.']['.$akn_id.']" class="form-control keynotes-text" '.$attrbutes.' autocomplete="off" placeholder="Enter text" required>'.$text.'</textarea>'.($addDelete>0?'<span class="deleteKeyNote"><i class="fa fa-remove"></i></span>':'').'</dd></dl>';
                }
                
                return '<dl class="fldList keynote-wrap" id="kn-id-'.$akn_id.'"><dd class="ml0" style="background:none;"><textarea cols="20" rows="4" name="data['.$kpa_id.']['.$akn_id.']" class="form-control keynotes-text" '.$attrbutes.' autocomplete="off" placeholder="Assessor Key Recommendations" required>'.$text.'</textarea>'.($addDelete>0?'<span class="deleteKeyNote"><i class="fa fa-remove"></i></span>':'').'</dd></dl>';
	}
        
        public static function getAssessorKeyNoteHtmlRowKPA($kpa_id,$akn_id,$text,$type,$attrbutes='',$addDelete=1,$jsDrop=array()){
		$akn_id = $akn_id =='new'?'new_'.uniqid():$akn_id;
                //print_r($jsDrop);
                $options="";
                foreach($jsDrop as $key=>$val){
                   $options.='<option value="'.$val['judgement_statement_instance_id'].'">'.$val['show_text'].'.  '.$val['judgement_statement_text'].'</option>'; 
                }
		if(!empty($type)){
		return '<dl class="fldList keynote-wrap '.$type.'" id="kn-id-'.$akn_id.'"><dd class="ml0" style="background:none;">
                    Choose a judgement statement : 
                    <select name="js['.$type.']['.$kpa_id.']['.$akn_id.']">
                        <option value=""> Choose Judgement Statement</option>
                        '.$options.'
                    </select><br><br>
  <textarea cols="20" rows="4" name="data['.$type.']['.$kpa_id.']['.$akn_id.']" class="form-control keynotes-text" '.$attrbutes.' autocomplete="off" placeholder="Enter text" required>'.$text.'</textarea>'.($addDelete>0?'<span class="deleteKeyNote"><i class="fa fa-remove"></i></span>':'').'</dd></dl>';
                }
                //return '<dl class="fldList keynote-wrap" id="kn-id-'.$akn_id.'"><dd class="ml0" style="background:none;"><textarea cols="20" rows="4" name="data['.$kpa_id.']['.$akn_id.']" class="form-control keynotes-text" '.$attrbutes.' autocomplete="off" placeholder="Assessor Key Recommendations" required>'.$text.'</textarea>'.($addDelete>0?'<span class="deleteKeyNote"><i class="fa fa-remove"></i></span>':'').'</dd></dl>';
	}
        
        
        public function getJSforKPA($kpaId,$langId = DEFAULT_LANGUAGE){
		$res=$this->db->get_results("select xyz1.*,d_js_order.show_text from (select (@cnt := @cnt + 1) AS rowNumber,xyz.* from (SELECT js.judgement_statement_id,cqjs.judgement_statement_instance_id, b.translation_text as judgement_statement_text,js_order
			FROM `d_judgement_statement` js
                        inner join h_lang_translation b on js.equivalence_id=b.equivalence_id
			inner join h_cq_js_instance cqjs on js.judgement_statement_id=cqjs.judgement_statement_id
			inner join h_kq_cq kqcq on kqcq.core_question_instance_id=cqjs.core_question_instance_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kkq.kpa_instance_id=? AND b.language_id = ?
			order by kd.kpa_order,kkq.kq_order,kqcq.cq_order,cqjs.`js_order` asc) xyz CROSS JOIN (SELECT @cnt := 0) AS dummy) xyz1 left join d_js_order on xyz1.rowNumber=d_js_order.order_id ",array($kpaId,$langId));
		return $res?$res:array();
	}
        
	//get all the existing kpas for the review type
	function getKpasForAssessmentType($assessment_type=1,$diagnostic_id=0,$lang_id=0){		
			$sql="select distinct a.kpa_id,hld.translation_text as kpa_name, b.diagnostic_id 
			FROM `d_kpa` a
			INNER JOIN `h_kpa_diagnostic` b ON a.kpa_id = b.kpa_id
			INNER JOIN `d_diagnostic` c ON c.diagnostic_id = b.diagnostic_id
			INNER JOIN `d_assessment_type` d ON c.assessment_type_id = d.assessment_type_id
                        INNER JOIN h_lang_translation hld ON a.equivalence_id= hld.equivalence_id
			where d.assessment_type_id = ? && hld.translation_type_id=1 && language_id=?";
			if($diagnostic_id>0)
			$sql .=" AND a.kpa_id NOT IN (select distinct a.kpa_id 
			FROM `d_kpa` a
			INNER JOIN `h_kpa_diagnostic` b ON a.kpa_id = b.kpa_id
			INNER JOIN `d_diagnostic` c ON c.diagnostic_id = b.diagnostic_id AND b.diagnostic_id = ? )";
                        
		if($diagnostic_id>0)	
			$sqlArgs=array($assessment_type,$lang_id,$diagnostic_id);	
		else
			$sqlArgs=array($assessment_type,$lang_id);
		
		$sql.=" group by a.kpa_id order by a.`kpa_id` asc;";
		$res=$this->db->get_results($sql,$sqlArgs);		
		return $res?$res:array();
	}
        
	function createDiagnostic($assessmentTypeId,$diagnosticName,$isPublished,$userId, $imageId = NULL,$is_kpa_recommendations=0,$is_kq_recommendations=0,$is_sq_recommendations=0,$is_js_recommendations=0,$lang_id){	
		$date_created = date("Y-m-d H:i:s");
                if($this->db->insert("d_equivalence",array("equivalence_id"=>0))){
                $equivalence_id=$this->db->get_last_insert_id();    
                
                if($this->db->insert("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>$lang_id,"translation_type_id"=>7,"translation_text"=>$diagnosticName,"isActive"=>1))){
                 $last_id_1=$this->db->get_last_insert_id();       
		if($this->db->insert("d_diagnostic",array("equivalence_id"=>$equivalence_id,"isPublished"=>$isPublished,"date_created"=>$date_created,"user_id"=>$userId,"assessment_type_id"=>$assessmentTypeId,'kpa_recommendations'=>$is_kpa_recommendations, 'kq_recommendations'=>$is_kq_recommendations, 'cq_recommendations'=>$is_sq_recommendations, 'js_recommendations'=>$is_js_recommendations, 'diagnostic_image_id' => $imageId))){	
			//$this->db->get_last_insert_id();
                        $last_id=$this->db->get_last_insert_id(); 
                        if($this->db->insert("h_lang_trans_diagnostics_details",array("lang_translation_id"=>$last_id_1,"date_created"=>$date_created,"create_user_id"=>$userId))){
                        return $last_id;
                        }else{
                        return false;    
                        }
                            
		} else
			return false;
                
                }else{
                        return false;  
                     }
                
                
                }
	}
        
	function createDiagnosticRatingScheme($diagnosticId,$ratingLevelSchemeId){			
		if(!$this->db->insert("h_diagnostic_rating_level_scheme",array("diagnostic_id"=>$diagnosticId,"rating_level_scheme_id"=>$ratingLevelSchemeId)))
			return false;
		return true;	
	}
        function getDiagnosticName($diagnostic_id,$lang_id=DEFAULT_LANGUAGE){		
		$res=$this->db->get_results("select hlt.translation_text as 'name',kpa_recommendations,kq_recommendations,cq_recommendations,js_recommendations
			from d_diagnostic d	
			inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 		
			where d.diagnostic_id=? and hlt.translation_type_id=7 and hlt.language_id=?  ",
			array($diagnostic_id,$lang_id));			
		return $res?$res:'';
	}
        
        function getDiagnosticNameByLang($diagnostic_id,$lang_id){		
		$res=$this->db->get_results("select hlt.translation_text as 'name',kpa_recommendations,kq_recommendations,cq_recommendations,js_recommendations
			from d_diagnostic d	
			inner join h_lang_translation hlt on d.equivalence_id = hlt.equivalence_id 		
			where d.diagnostic_id=? and hlt.translation_type_id=7 and hlt.language_id=?  ",
			array($diagnostic_id,$lang_id));			
		return $res?$res:'';
	}
	/*function getDiagnosticName($diagnostic_id){		
		$res=$this->db->get_results("select d.name as 'name',kpa_recommendations,kq_recommendations,cq_recommendations,js_recommendations
			from d_diagnostic d			
			where d.diagnostic_id=? ",
			array($diagnostic_id));			
		return $res?$res:'';
	}*/
        
        
        function updateDiagnosticLang($diagnosticId,$diagnosticName,$isPublished,$lang_id,$parent_id,$userId=0){
                $date_created = date("Y-m-d H:i:s");
                $diagnostic=$this->getDiagnosticBYLang($diagnosticId,$lang_id);
                $equivalence_id=isset($diagnostic['equivalence_id'])?$diagnostic['equivalence_id']:0;
                $lang_translation_id=isset($diagnostic['lang_translation_id'])?$diagnostic['lang_translation_id']:0;
                
                if($lang_translation_id==0){
                $diagnosticLang=$this->getDiagnosticById($diagnosticId);
                $equivalence_id=$diagnosticLang['equivalence_id'];
                }
                
                $rType=FALSE;
                if($lang_translation_id==0){
                     if($this->db->insert("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>$lang_id,"translation_type_id"=>7,"translation_text"=>$diagnosticName,"isActive"=>'1',"parent_lang_translation_id"=>$parent_id))){
                           $rType= true;
                           $lang_translation_insert_id=$this->db->get_last_insert_id();
                        }   
                    }else{
                        if($this->db->update("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>$lang_id,"translation_type_id"=>7,"translation_text"=>$diagnosticName,"isActive"=>'1'),array("lang_translation_id"=>$lang_translation_id,"language_id"=>$lang_id))){
                           $rType= true; 
                        }
                }
               
                if($rType==true){
                    if($lang_translation_id==0){
                    return $this->db->insert("h_lang_trans_diagnostics_details",array("lang_translation_id"=>$lang_translation_insert_id,"date_created"=>$date_created,"create_user_id"=>$userId)); 
                    }else{
                    return $this->db->update("h_lang_trans_diagnostics_details",array("isPublished"=>$isPublished,"date_published"=>$date_created,"publish_user_id"=>$userId),array("lang_translation_id"=>$lang_translation_id)); 
                        
                    }
                }
        }
        
	function updateDiagnostic($diagnosticId,$diagnosticName,$isPublished, $imageId = NULL,$is_kpa_recommendations=0,$is_kq_recommendations=0,$is_sq_recommendations=0,$is_js_recommendations=0,$lang_id,$userId=0){		
		$date_created = date("Y-m-d H:i:s");
                $diagnostic=$this->getDiagnosticBYLang($diagnosticId,$lang_id);
                $equivalence_id=isset($diagnostic['equivalence_id'])?$diagnostic['equivalence_id']:0;
                $lang_translation_id=isset($diagnostic['lang_translation_id'])?$diagnostic['lang_translation_id']:0;
                
                if($lang_translation_id==0){
                    
                $diagnosticLang=$this->getDiagnosticById($diagnosticId);
                if($diagnosticLang['equivalence_id']==NULL || $diagnosticLang['equivalence_id']==""){
                
                        if($this->db->insert("d_equivalence",array("equivalence_id"=>0))){
                            $equivalence_id=$this->db->get_last_insert_id();

                        }else{
                        return false;
                        }
                
                }else{
                    $equivalence_id=$diagnosticLang['equivalence_id'];
                }
                
                }
                
                $rType=FALSE;
                if($lang_translation_id==0){
                     if($this->db->insert("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>$lang_id,"translation_type_id"=>7,"translation_text"=>$diagnosticName,"isActive"=>'1'))){
                           $rType= true;
                           $lang_translation_id=$this->db->get_last_insert_id();
                        }   
                    }else{
                        if($this->db->update("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>$lang_id,"translation_type_id"=>7,"translation_text"=>$diagnosticName,"isActive"=>'1'),array("lang_translation_id"=>$lang_translation_id,"language_id"=>$lang_id))){
                           $rType= true; 
                        }
                }
                    
		
                
                if($rType==true){
                    $rType_1=FALSE;
                    if($isPublished>0){
                    if($imageId != NULL){
			$rType_1 =$this->db->update("d_diagnostic",array("equivalence_id"=>$equivalence_id,"date_published"=>$date_created,"isPublished"=>$isPublished, 'diagnostic_image_id' => $imageId,'kpa_recommendations'=>$is_kpa_recommendations, 'kq_recommendations'=>$is_kq_recommendations, 'cq_recommendations'=>$is_sq_recommendations, 'js_recommendations'=>$is_js_recommendations),array("diagnostic_id"=>$diagnosticId),array("diagnostic_id"=>$diagnosticId));
                    } else {
                        $rType_1 =$this->db->update("d_diagnostic",array("equivalence_id"=>$equivalence_id,"date_published"=>$date_created,"isPublished"=>$isPublished,'kpa_recommendations'=>$is_kpa_recommendations, 'kq_recommendations'=>$is_kq_recommendations, 'cq_recommendations'=>$is_sq_recommendations, 'js_recommendations'=>$is_js_recommendations),array("diagnostic_id"=>$diagnosticId));
                    }
                    
                    if($rType_1){
                        return $this->db->update("h_lang_trans_diagnostics_details",array("isPublished"=>$isPublished,"date_published"=>$date_created,"publish_user_id"=>$userId),array("lang_translation_id"=>$lang_translation_id)); 
                        
                    }else{
                        return FALSE;
                    }
                }
		else {
                      if($imageId != NULL){                        
			return $this->db->update("d_diagnostic",array("isPublished"=>$isPublished,"user_id"=>"1", 'diagnostic_image_id' => $imageId,'kpa_recommendations'=>$is_kpa_recommendations, 'kq_recommendations'=>$is_kq_recommendations, 'cq_recommendations'=>$is_sq_recommendations, 'js_recommendations'=>$is_js_recommendations),array("diagnostic_id"=>$diagnosticId),array("diagnostic_id"=>$diagnosticId));	
                      } else {                      	
                          return $this->db->update("d_diagnostic",array("isPublished"=>$isPublished,"user_id"=>"1",'kpa_recommendations'=>$is_kpa_recommendations, 'kq_recommendations'=>$is_kq_recommendations, 'cq_recommendations'=>$is_sq_recommendations, 'js_recommendations'=>$is_js_recommendations),array("diagnostic_id"=>$diagnosticId),array("diagnostic_id"=>$diagnosticId));
                      }
                    
                }
                
                }
	}
        
	function createKpa($kpas,$lang_id=9){				
		$kpaNameArr = array();
		$kpaIdArr = array();
                
                foreach($kpas as $kpa){
                        if($this->db->insert("d_equivalence",array("equivalence_id"=>0))){
                        $equivalence_id=$this->db->get_last_insert_id();

                        }else{
                            return false;
                        }
                        
                        if($this->db->insert("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>$lang_id,"translation_type_id"=>1,"translation_text"=>$kpa,"isActive"=>'1'))){
                                        
			if($this->db->insert("d_kpa",array("equivalence_id"=>$equivalence_id,"isActive"=>'1')))
			{	$kpa_id=$this->db->get_last_insert_id();			
				//array_push($kpasArr,array_combine());
                                //return $this->db->get_last_insert_id();
                                array_push($kpaIdArr,$kpa_id);
				array_push($kpaNameArr, $kpa);    
                                
				
			}	
			else
				return false;
                        }else{
                                return false;
                        }
		}		
		return array_combine($kpaIdArr,$kpaNameArr);
			
	}
        
        function newLangKpa($kpas,$lang_id=9){
            foreach($kpas as $key=>$val){
             $kpa_id=$key;
             
            $sql="select * from d_kpa where kpa_id=?";
            $res=$this->db->get_row($sql,array($kpa_id));
            $langDetails=$this->getLangDetails($res['equivalence_id'],$lang_id);
            
            if(count($langDetails)==0){
                if($this->db->insert("h_lang_translation",array("equivalence_id"=>$res['equivalence_id'],"language_id"=>$lang_id,"translation_type_id"=>1,"translation_text"=>$val,"isActive"=>'1'))){
                                

                }else{
                     return false;  
                }
            }else{
                
                if($this->db->update("h_lang_translation",array("translation_text"=>$val,"isActive"=>'1'),array("lang_translation_id"=>$langDetails['lang_translation_id']))){
                                

                    }else{
                      return false;  
                }
            }
                
            }
            
            return true;
        }
        
	function updateKpa($kpas){				
		$kpaNameArr = array();
		$kpaIdArr = array();
		foreach($kpas as $kpa){
			if($this->db->update("d_kpa",array("kpa_name"=>$kpa['kpa_name']),array("kpa_id"=>$kpa['kpa_id'])))
			{
				//array_push($kpasArr,"aa"=>$kpa);
			}	
			else
				return false;
		}
			
	}
	function createKpaDiagnosticInstance($kpas,$diagnosticId){	//todo	
	
		foreach($kpas as $kpa):		
			if(!$this->db->insert("h_kpa_diagnostic",array("kpa_id"=>$kpa['id'],"kpa_order"=>$kpa['order'],"diagnostic_id"=>$diagnosticId)))
				return false;			
		endforeach;
		return true;
			
	} 
	function createSingleKpaDiagnosticInstance($kpa,$diagnosticId){	//todo	
			
			if(!$this->db->insert("h_kpa_diagnostic",array("kpa_id"=>$kpa['id'],"kpa_order"=>$kpa['order'],"diagnostic_id"=>$diagnosticId)))
				return false;					
		return true;
			
	} 
	function updateKpaDiagnosticInstance($kpas,$diagnosticId){						
		foreach($kpas as $kpa):
			if(!($this->db->update("h_kpa_diagnostic",array("kpa_order"=>$kpa['order']),array("kpa_id"=>$kpa['id'],"diagnostic_id"=>$diagnosticId))))
			{
				return false;
			}				
		endforeach;
		return true;
			
	}
	function updateSingleKpaDiagnosticInstance($kpa,$diagnosticId){				
		
			if(!($this->db->update("h_kpa_diagnostic",array("kpa_order"=>$kpa['order']),array("kpa_id"=>$kpa['id'],"diagnostic_id"=>$diagnosticId))))
			{
				return false;
			}				
		
		return true;
			
	}
	function getSelectedKpasForDiagnostic($diagnostic_id,$lang_id=0){
                $sql="select k.kpa_id, hld.translation_text  as kpa_name,kd.diagnostic_id
			from d_kpa k
			inner join h_kpa_diagnostic kd on k.kpa_id=kd.kpa_id
                        inner join h_lang_translation hld on k.equivalence_id=hld.equivalence_id
			where kd.diagnostic_id=?";
                
                        $p_array[]= $diagnostic_id;       
                        if($lang_id>0) {
                            $sql.=" && hld.language_id=?";
                            $p_array[]= $lang_id;
                        }
                        
			$sql.="order by kd.`kpa_order` asc;" ;
                                
		$res=$this->db->get_results($sql,
			$p_array);
		return $res?$res:array();
	}
	function deleteKpaDiagnosticInstance($kpa_id,$diagnosticId){				
			
			$sql = "Select kpa_instance_id 
			from h_kpa_diagnostic 
			where kpa_id=? and diagnostic_id=?";
			$res=$this->db->get_row($sql,array($kpa_id,$diagnosticId));	
			$kpaInstanceId = $res['kpa_instance_id'];
			$res='';
			
			
			$sql = "Select group_concat(key_question_instance_id) as kqs 
			from h_kpa_kq 
			where kpa_instance_id=? ;";
			$res=$this->db->get_row($sql,array($kpaInstanceId));				
			$res = explode(',',$res['kqs']);
			foreach($res as $kq=>$kqInstanceId)
			{
				$sql = "SELECT group_concat(core_question_instance_id) as cqs
				FROM h_kq_cq 
				WHERE key_question_instance_id=? group by key_question_instance_id;";
				$res=$this->db->get_row($sql,array($kqInstanceId));
				$res = explode(',',$res['cqs']);
				
				foreach($res as $cq=>$cqInstanceId)
				{
					if(!$this->db->delete("h_cq_js_instance",array("core_question_instance_id"=>$cqInstanceId)))				
						return false;				
				}	
				
				if(!($this->db->delete("h_kq_cq",array("key_question_instance_id"=>$kqInstanceId))))
					return false;
					
			}
			
			if(!($this->db->delete("h_kpa_kq",array("kpa_instance_id"=>$kpaInstanceId))))
			{
				return false;
			}	
			
			if(!($this->db->delete("h_kpa_diagnostic",array("kpa_id"=>$kpa_id,"diagnostic_id"=>$diagnosticId))))
			{
				return false;
			}				
		
		return true;
			
	}
	function getKpaIdsForDiagnostic($diagnostic_id){
		$res=$this->db->get_results("select kpa_id
			from h_kpa_diagnostic k			
			where k.diagnostic_id=?
			order by k.`kpa_order` asc;",
			array($diagnostic_id));
		return $res?$res:array();
	}
	function checkAssessorExternalTeam($assessment_id,$assessor_id){
		$res=$this->db->get_row("select count(user_id) as isExternal
			from h_assessment_external_team		
			where assessment_id = ? AND user_id=? ",
			array($assessment_id,$assessor_id));
		return $res?$res:array();
	}
	function checkAssessorIsLead($assessment_id,$assessor_id){
		$res=$this->db->get_row("select user_id as isLead
			from h_assessment_user	
			where assessment_id = ? AND user_id=? AND role = ? ",
			array($assessment_id,$assessor_id,4));
		return $res?$res:array();
	}
	function getExternalTeamRatingPerc($assessment_id,$assessor_id=0){
		/*$res=$this->db->get_row("select t.user_id, GROUP_CONCAT(t.isFilled) as filledStatus,GROUP_CONCAT(t.user_id) as user_ids,sum(t.percComplete) as percentageSum,count(t.user_id) as numTeamMembers
			from h_assessment_external_team t inner join d_assessment_kpa kp on t.assessment_id = kp.assessment_id and t.user_id = kp.user_id		
			where t.assessment_id = ?   and t.user_role = ? ",
			array($assessment_id,4));*/
            $res=$this->db->get_results("select t.user_id, t.isFilled ,t.user_id,t.percComplete,t.user_id
			from h_assessment_external_team t inner join d_assessment_kpa kp on t.assessment_id = kp.assessment_id and t.user_id = kp.user_id		
			where t.assessment_id = ?   and t.user_role = ? group by t.user_id ",
			array($assessment_id,4));
            if(!empty($res)){
            $dataArray  = array('filledStatus'=>'','user_ids'=>'','percentageSum'=>0);
            foreach($res as $data){
                $dataArray['filledStatus'] .= $data['isFilled'].",";
                $dataArray['user_ids'] .= $data['user_id'].",";
                $dataArray['percentageSum'] = $dataArray['percentageSum']+$data['percComplete'];
                $dataArray['numTeamMembers'] = count($res);
                
            }
             $dataArray['filledStatus'] = trim( $dataArray['filledStatus'],",");
             $dataArray['user_ids'] = trim( $dataArray['user_ids'],",");
             //print_r($dataArray);die;
		return $dataArray?$dataArray:array();
            } else {
                
            return $res?$res:array();}
	}
	function getAssessmentLead($assessment_id){
		$res=$this->db->get_row("select user_id
			from h_assessment_user	
			where assessment_id = ? AND role=? ",
			array($assessment_id,4));
		return $res?$res:array();
	}
	function getAllKeyQuestions($diagnosticId=0,$lang_id=0){
		$sql = "select  kq.key_question_id, kq.translation_text as key_question_text
		from (select a.*,b.translation_text,b.language_id from d_key_question a inner join h_lang_translation b on a.equivalence_id=b.equivalence_id) kq ";
		if($diagnosticId>0)
		{	
			$sql .= "where kq.key_question_id not in (SELECT distinct kq.key_question_id
			FROM `d_key_question` kq
			inner join h_kpa_kq kkq on kkq.key_question_id=kq.key_question_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=?
			order by kkq.`kq_order` asc)
		";
                        if($lang_id>0){
                         $sql.=" && kq.language_id=?";  
                         $res=$this->db->get_results($sql,array($diagnosticId,$lang_id));
                        }else{
                            $res=$this->db->get_results($sql,array($diagnosticId));
                        }
			
		}
		else
                if($lang_id>0){
                 $sql.=" where kq.language_id=?";    
                 $res=$this->db->get_results($sql,array($lang_id));   
                }else{    
		$res=$this->db->get_results($sql);
                }
		return $res?$res:array();
	}
        
	function getSelectedKeyQuestionsForDiagnostic($diagnostic_id,$kpaId=0,$lang_id=0){
                $sql="SELECT distinct kq.key_question_id, kq.translation_text as key_question_text,kd.diagnostic_id
			FROM (select a.*,b.language_id,b.translation_text from `d_key_question` a left join h_lang_translation b on a.equivalence_id=b.equivalence_id) kq
			inner join h_kpa_kq kkq on kkq.key_question_id=kq.key_question_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=? and kd.kpa_instance_id=?";
		$array_p=array($diagnostic_id,$kpaId);	
                if($lang_id>0){
                    $sql.=" && kq.language_id=?";
                    $array_p[]=$lang_id;
                }    
                $sql.=" order by kkq.`kq_order` asc;";
		$res=$this->db->get_results($sql,$array_p);
		return $res?$res:array();
	}
        
        function newLangCreateKeyQuestions($kqs,$lang_id=0){
            
            foreach($kqs as $key=>$val){
            $kqs_id=$key;
             
            $sql="select * from d_key_question where key_question_id=?";
            $res=$this->db->get_row($sql,array($kqs_id));
            
            $langDetails=$this->getLangDetails($res['equivalence_id'],$lang_id);
            if(count($langDetails)==0){
                if($this->db->insert("h_lang_translation",array("equivalence_id"=>$res['equivalence_id'],"language_id"=>$lang_id,"translation_type_id"=>2,"translation_text"=>$val,"isActive"=>'1'))){
                                

                    }else{
                      return false;  
                }
            }else{
                
                if($this->db->update("h_lang_translation",array("translation_text"=>$val,"isActive"=>'1'),array("lang_translation_id"=>$langDetails['lang_translation_id']))){
                                

                    }else{
                      return false;  
                }
            }
            
                
            }
            
            return true;
            
        }
        
        function newLangCreateCoreQuestions($cqs,$lang_id=0){
            
            foreach($cqs as $key=>$val){
            $cqs_id=$key;
             
            $sql="select * from d_core_question where core_question_id=?";
            $res=$this->db->get_row($sql,array($cqs_id));
            
            $langDetails=$this->getLangDetails($res['equivalence_id'],$lang_id);
            if(count($langDetails)==0){
                if($this->db->insert("h_lang_translation",array("equivalence_id"=>$res['equivalence_id'],"language_id"=>$lang_id,"translation_type_id"=>3,"translation_text"=>$val,"isActive"=>'1'))){
                                

                    }else{
                      return false;  
                }
            }else{
                
                if($this->db->update("h_lang_translation",array("translation_text"=>$val,"isActive"=>'1'),array("lang_translation_id"=>$langDetails['lang_translation_id']))){
                                

                    }else{
                      return false;  
                }
            }
            
                
            }
            
            return true;
            
        }
        
        function newLangCreateJudgementStatements($js,$lang_id=0){
            foreach($js as $key=>$val){
            $js_id=$key;
             
            $sql="select * from d_judgement_statement where judgement_statement_id=?";
            $res=$this->db->get_row($sql,array($js_id));
            
            $langDetails=$this->getLangDetails($res['equivalence_id'],$lang_id);
            if(count($langDetails)==0){
                if($this->db->insert("h_lang_translation",array("equivalence_id"=>$res['equivalence_id'],"language_id"=>$lang_id,"translation_type_id"=>4,"translation_text"=>$val,"isActive"=>'1'))){
                                

                    }else{
                      return false;  
                }
            }else{
                
                if($this->db->update("h_lang_translation",array("translation_type_id"=>4,"translation_text"=>$val,"isActive"=>'1'),array("lang_translation_id"=>$langDetails['lang_translation_id']))){
                                

                    }else{
                      return false;  
                }
            }
            
                
            }
            
            return true;
        }
        
        function getLangDetails($equivalence_id,$language_id){
            $sql_trans="select * from h_lang_translation where equivalence_id=? && language_id=?";
            $res=$this->db->get_row($sql_trans,array($equivalence_id,$language_id));
            return $res?$res:array();
        }
        
	function createKeyQuestions($kqs,$lang_id=0){				
		$kqNameArr = array();
		$kqIdArr = array();
                
		        foreach($kqs as $kq){
                        if($this->db->insert("d_equivalence",array("equivalence_id"=>0))){
                        $equivalence_id=$this->db->get_last_insert_id();

                        }else{
                            return false;
                        } 
                        
                        if($this->db->insert("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>$lang_id,"translation_type_id"=>2,"translation_text"=>$kq,"isActive"=>'1'))){
                                
			if($this->db->insert("d_key_question",array("equivalence_id"=>$equivalence_id,"isActive"=>'1')))
			{				
				//array_push($kpasArr,array_combine());
				array_push($kqIdArr, $this->db->get_last_insert_id());
				array_push($kqNameArr, $kq);
			}	
			else
				return false;
                        }else{
                                return false;
                        }
		}		
		return array_combine($kqIdArr,$kqNameArr);
			
	}	
	function getKeyQuestionIdsPerKpaForDiagnostic($diagnostic_id,$kpaId){
		$res=$this->db->get_results("SELECT distinct kq.key_question_id 
			FROM `d_key_question` kq
			inner join h_kpa_kq kkq on kkq.key_question_id=kq.key_question_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=? and kd.kpa_instance_id=?
			order by kkq.`key_question_id` asc",
			array($diagnostic_id,$kpaId));
		return $res?$res:array();
	}
	function createSingleKeyQuestionDiagnosticInstance($kq,$kpaInstanceId){	
			
			if(!$this->db->insert("h_kpa_kq",array("key_question_id"=>$kq['key_question_id'],"kpa_instance_id"=>$kpaInstanceId,"kq_order"=>$kq['order'])))
				return false;					
		return true;
			
	} 
	function updateSingleKeyQuestionDiagnosticInstance($kq,$kpaInstanceId){	
			
			if(!$this->db->update("h_kpa_kq",array("kq_order"=>$kq['order']),array("key_question_id"=>$kq['key_question_id'],"kpa_instance_id"=>$kpaInstanceId)))
				return false;					
		return true;
			
	} 
	function deleteKeyQuestionDiagnosticInstance($kq_id,$kpaInstanceId){				
		
			$sql = "Select key_question_instance_id 
			from h_kpa_kq 
			where kpa_instance_id=? and key_question_id=?";
			$res=$this->db->get_row($sql,array($kpaInstanceId,$kq_id));	
			$kqInstanceId = $res['key_question_instance_id'];
			$res='';
			
			$sql = "SELECT group_concat(core_question_instance_id) as cqs
			FROM h_kq_cq 
			WHERE key_question_instance_id=? group by key_question_instance_id;";
			$res=$this->db->get_row($sql,array($kqInstanceId));
			$res = explode(',',$res['cqs']);
			foreach($res as $cq=>$cqInstanceId)
			{
				if(!$this->db->delete("h_cq_js_instance",array("core_question_instance_id"=>$cqInstanceId)))				
					return false;				
			}			
			if(!($this->db->delete("h_kq_cq",array("key_question_instance_id"=>$kqInstanceId))))
					return false;
			
			if(!($this->db->delete("h_kpa_kq",array("key_question_id"=>$kq_id,"kpa_instance_id"=>$kpaInstanceId))))
			{
				return false;
			}				
		
		return true;
			
	}
	function getMinMaxLimitForDiagnosticQuestions($type,$assessmentTypeId)
	{
		$res=$this->db->get_results("SELECT limit_min, limit_max 
		FROM d_diagnostic_limit_matrix
		WHERE limit_question_type = ? AND limit_assessment_type = ?",
		array($type,$assessmentTypeId));
		return $res?$res:array();
	}
	function getAssessmentTypeById($assessmentId){
		$res=$this->db->get_results("select at.assessment_type_name from d_assessment_type at
		WHERE at.assessment_type_id = ?",
		array($assessmentId));
		return $res?$res:array();
	}
	function getAllCoreQuestions($diagnosticId=0,$lang_id=0){
		$sql = "select  cq.core_question_id, cq.translation_text as core_question_text
		from (select a.*,b.language_id,b.translation_text from d_core_question a inner join h_lang_translation b on a.equivalence_id=b.equivalence_id) cq ";
		if($diagnosticId>0)
		{	
			$sql .= " WHERE cq.core_question_id NOT IN (SELECT distinct cq.core_question_id
			FROM `d_core_question` cq
			inner join h_kq_cq kqcq on kqcq.core_question_id=cq.core_question_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=? order by kqcq.`cq_order` asc)";
                        
                        if($lang_id>0){
                         $sql .= " && cq.language_id=? ";   
                        }
                        
			$sql .= " order by cq.core_question_id asc;";
                        if($lang_id>0){
                        $res=$this->db->get_results($sql,array($diagnosticId,$lang_id));     
                        }else{
			$res=$this->db->get_results($sql,array($diagnosticId));
                        }
		}
		else
                if($lang_id>0){
                         $sql .= " where cq.language_id=?";
                         $res=$this->db->get_results($sql,array($lang_id));
                }else{    
		$res=$this->db->get_results($sql);
                }
		return $res?$res:array();
	}
        
	function getSelectedCoreQuestionsForDiagnostic($diagnostic_id,$kpaId,$kqId,$lang_id=0){
                                
                $sql="SELECT distinct cq.core_question_id,cq.translation_text as core_question_text,kd.diagnostic_id
			FROM (select a.*,b.language_id,b.translation_text from d_core_question a inner join h_lang_translation b on a.equivalence_id=b.equivalence_id) cq
			inner join h_kq_cq kqcq on kqcq.core_question_id=cq.core_question_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=? and kkq.kpa_instance_id= ? and kqcq.key_question_instance_id=?";
                        $array_p=array($diagnostic_id,$kpaId,$kqId);        
                        if($lang_id>0){
                          $sql.=" && cq.language_id=?";
                          $array_p[]=$lang_id;
                        }
                        
			$sql.="order by kqcq.`cq_order` asc";
                                
		$res=$this->db->get_results($sql,$array_p);
		return $res?$res:array();
	}
        
	function createCoreQuestions($cqs,$lang_id=0){				
		$cqNameArr = array();
		$cqIdArr = array();
		foreach($cqs as $cq){
                        if($this->db->insert("d_equivalence",array("equivalence_id"=>0))){
                        $equivalence_id=$this->db->get_last_insert_id();

                        }else{
                            return false;
                        }
                        if($this->db->insert("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>$lang_id,"translation_type_id"=>3,"translation_text"=>$cq,"isActive"=>'1'))){
                        
			if($this->db->insert("d_core_question",array("equivalence_id"=>$equivalence_id,"isActive"=>'1')))
			{				
				//array_push($kpasArr,array_combine());
				array_push($cqIdArr, $this->db->get_last_insert_id());
				array_push($cqNameArr, $cq);
			}	
			else
				return false;
                        }else{
                                return false;
                        }
		}		
		return array_combine($cqIdArr,$cqNameArr);
			
	}
	function getKeyQuestionIdsPerKqForDiagnostic($diagnostic_id,$kpaId,$kqId){
		$res=$this->db->get_results("SELECT distinct cq.core_question_id
			FROM `d_core_question` cq
			inner join h_kq_cq kqcq on kqcq.core_question_id=cq.core_question_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=? and kkq.kpa_instance_id= ? and kqcq.key_question_instance_id=?
			order by kqcq.`cq_order` asc",array($diagnostic_id,$kpaId,$kqId));
		return $res?$res:array();
	}
	function updateSingleCoreQuestionDiagnosticInstance($cq,$cqInstanceId){	
			
			if(!$this->db->update("h_kq_cq",array("cq_order"=>$cq['order']),array("core_question_id"=>$cq['core_question_id'],"key_question_instance_id"=>$cqInstanceId)))
				return false;					
		return true;
			
	} 
	function createSingleCoreQuestionDiagnosticInstance($cq,$cqInstanceId){	
			
			if(!$this->db->insert("h_kq_cq",array("core_question_id"=>$cq['core_question_id'],"key_question_instance_id"=>$cqInstanceId,"cq_order"=>$cq['order'])))
				return false;					
		return true;
			
	} 
	function deleteCoreQuestionDiagnosticInstance($cq_id,$cqInstanceId){				
		
			//delete related judgement statement if any by getting cqinstanceId
			$sql = "SELECT core_question_instance_id 
			FROM h_kq_cq 
			WHERE key_question_instance_id=? and core_question_id=?";
			$res=$this->db->get_row($sql,array($cqInstanceId,$cq_id));				
			$cId = $res['core_question_instance_id'];	
			if(($this->db->delete("h_cq_js_instance",array("core_question_instance_id"=>$cId))))
			{
				if(($this->db->delete("h_kq_cq",array("core_question_id"=>$cq_id,"key_question_instance_id"=>$cqInstanceId))))
				{
					return true;
				}
			}									
		
		return false;
			
	}
	function getAllJudgementStatements($diagnosticId=0,$lang_id=0){
		$sql = "select jss.judgement_statement_id, jss.translation_text as judgement_statement_text
		from (select a.*,b.language_id,b.translation_text from d_judgement_statement a inner join h_lang_translation b on a.equivalence_id=b.equivalence_id) jss ";
		if($diagnosticId>0)
		{	
			$sql .= " WHERE jss.judgement_statement_id NOT IN (SELECT js.judgement_statement_id
			FROM `d_judgement_statement` js
			inner join h_cq_js_instance cqjs on js.judgement_statement_id=cqjs.judgement_statement_id
			inner join h_kq_cq kqcq on kqcq.core_question_instance_id=cqjs.core_question_instance_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=?
			)";
                        
                        if($lang_id>0){
                          $sql .= " && jss.language_id=? ";  
                        }
                        
                        $sql .= " order by jss.`judgement_statement_id` asc 
		";
			if($lang_id>0){
                        $res=$this->db->get_results($sql,array($diagnosticId,$lang_id));    
                        }else{
			$res=$this->db->get_results($sql,array($diagnosticId));	
                        }
		}
		else
                if($lang_id>0){
                          $sql .= " WHERE jss.language_id=? ";  
                          $res=$this->db->get_results($sql,array($lang_id));
                }else{
		$res=$this->db->get_results($sql);
                }
	
		foreach($res as $k=>$r){
				//$row[$k]['judgement_statement_text']=utf8_encode($r['judgement_statement_text']);
			}
			//print_r($res); die;
		return $res?$res:array();
	}
	function getSelectedJSSForDiagnostic($diagnosticId,$kpaId,$kqId,$cqId,$lang_id=0){
			
			$sql = "SELECT js.judgement_statement_id,js.translation_text as judgement_statement_text
			FROM (select a.*,b.language_id,b.translation_text from d_judgement_statement a inner join h_lang_translation b on a.equivalence_id=b.equivalence_id) js
			inner join h_cq_js_instance cqjs on js.judgement_statement_id=cqjs.judgement_statement_id
			inner join h_kq_cq kqcq on kqcq.core_question_instance_id=cqjs.core_question_instance_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=? AND kkq.kpa_instance_id=? AND kqcq.key_question_instance_id =? AND kqcq.core_question_instance_id=?";
                        $array_p=array($diagnosticId,$kpaId,$kqId,$cqId);
                        
                        if($lang_id>0){
                         $sql .= " && js.language_id=?";
                         $array_p[]=$lang_id;
                        }
			$sql .= " order by cqjs.`js_order` asc ";
			$res=$this->db->get_results($sql,$array_p);		
		return $res?$res:array();
	}
        
        
        
	function createJudgementStatements($jss,$lang_id){				
		$jssNameArr = array();
		$jssIdArr = array();
		foreach($jss as $js){
			if($this->db->insert("d_equivalence",array("equivalence_id"=>0))){
                        $equivalence_id=$this->db->get_last_insert_id();

                        }else{
                            return false;
                        }
                        
                     if($this->db->insert("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>$lang_id,"translation_type_id"=>4,"translation_text"=>$js,"isActive"=>'1'))){
                           
                     if($this->db->insert("d_judgement_statement",array("equivalence_id"=>$equivalence_id,"isActive"=>'1')))
			{				
				//array_push($kpasArr,array_combine());
				array_push($jssIdArr, $this->db->get_last_insert_id());
				array_push($jssNameArr, $js);
			}	
			else
				return false;
                        }else{
                                return false;
                        }
		}		
		return array_combine($jssIdArr,$jssNameArr);
			
	}
	
	function getCoreQuestionIdsPerKqForDiagnostic($diagnostic_id,$kpaId,$kqId){
		$res=$this->db->get_results("SELECT distinct cq.core_question_id
			FROM `d_core_question` cq
			inner join h_kq_cq kqcq on kqcq.core_question_id=cq.core_question_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=? and kkq.kpa_instance_id= ? and kqcq.key_question_instance_id=?
			order by kqcq.`cq_order` asc",array($diagnostic_id,$kpaId,$kqId));
		return $res?$res:array();
	}
	function getJSSIdsPerCqForDiagnostic($diagnostic_id,$kpaId,$kqId,$cqId,$langId = DEFAULT_LANGUAGE){
		$res=$this->db->get_results("SELECT js.judgement_statement_id, b.translation_text as judgement_statement_text
			FROM `d_judgement_statement` js
                        inner join h_lang_translation b on js.equivalence_id=b.equivalence_id
			inner join h_cq_js_instance cqjs on js.judgement_statement_id=cqjs.judgement_statement_id
			inner join h_kq_cq kqcq on kqcq.core_question_instance_id=cqjs.core_question_instance_id
			inner join h_kpa_kq kkq on kkq.key_question_instance_id=kqcq.key_question_instance_id
			inner join h_kpa_diagnostic kd on kd.kpa_instance_id=kkq.kpa_instance_id
			where kd.diagnostic_id=? AND kkq.kpa_instance_id=? AND kqcq.key_question_instance_id =? AND kqcq.core_question_instance_id=?
                        AND b.language_id = ?
			order by cqjs.`js_order` asc",array($diagnostic_id,$kpaId,$kqId,$cqId,$langId));
		return $res?$res:array();
	}
	function updateSingleJSSDiagnosticInstance($jss,$cqInstanceId){	
			
			if(!$this->db->update("h_cq_js_instance",array("js_order"=>$jss['order']),array("judgement_statement_id"=>$jss['judgement_statement_id'],"core_question_instance_id"=>$cqInstanceId)))
				return false;					
		return true;
			
	} 
	function createSingleJSSDiagnosticInstance($jss,$cqInstanceId){	
			
			if(!$this->db->insert("h_cq_js_instance",array("judgement_statement_id"=>$jss['judgement_statement_id'],"core_question_instance_id"=>$cqInstanceId,"js_order"=>$jss['order'])))
				return false;					
		return true;
			
	} 
	function deleteJSSDiagnosticInstance($jssId,$cqInstanceId){				
		
			if(!($this->db->delete("h_cq_js_instance",array("judgement_statement_id"=>$jssId,"core_question_instance_id"=>$cqInstanceId))))
			{
				return false;
			}				
		
		return true;
			
	}
        
	function countKpaForDiagnostic($diagnostic_id,$lang_id){
		$res=$this->db->get_results("Select count(kpa_instance_id) as num,group_concat(kpa_instance_id order by kpa_order) as kpas,
		b.assessment_type_id as type 
                from h_kpa_diagnostic a
                inner join d_kpa kpa on a.kpa_id=kpa.kpa_id
                inner join d_diagnostic b on  a.diagnostic_id=b.diagnostic_id
                inner join h_lang_translation c on  c.equivalence_id=kpa.equivalence_id && translation_type_id=1
                where a.diagnostic_id = ? && c.language_id=?;",array($diagnostic_id,$lang_id));
		return $res?$res:array();
	}
        
	function countKeyQuestionsForKpa($kpaInstanceId,$lang_id){
		$res=$this->db->get_results("Select count(key_question_instance_id) as num, group_concat(key_question_instance_id order by kq_order) as kq
		from h_kpa_kq a
                inner join d_key_question b on a.key_question_id=b.key_question_id
                inner join h_lang_translation c on b.equivalence_id=c.equivalence_id && translation_type_id=2
		where a.kpa_instance_id = ? && c.language_id=?;",array($kpaInstanceId,$lang_id));
		return $res?$res:array();
	}
        
	function countCoreQuestionsForKeyQuestion($kqInstanceId,$langId){
		$res=$this->db->get_results("Select count(core_question_instance_id) as num, group_concat(core_question_instance_id order by cq_order) as cq
		from h_kq_cq a 
                inner join d_core_question b on a.core_question_id=b.core_question_id
                inner join h_lang_translation c on b.equivalence_id=c.equivalence_id && translation_type_id=3
                where a.key_question_instance_id = ? && c.language_id=?;
		",array($kqInstanceId,$langId));
		return $res?$res:array();
	}
	function countJudgementStatementsForeCoreQuestion($cqInstanceId,$langId){
		$res=$this->db->get_results("Select count(judgement_statement_instance_id) as num, group_concat(judgement_statement_instance_id order by js_order) as jss
		from h_cq_js_instance a
                inner join d_judgement_statement b on a.judgement_statement_id=b.judgement_statement_id
                inner join h_lang_translation c on b.equivalence_id=c.equivalence_id && translation_type_id=4
		where a.core_question_instance_id = ? && c.language_id=?;",
		array($cqInstanceId,$langId));
		return $res?$res:array();
	}
	function createDiagnosticTeacherCategory($diagnostic_id,$teacher_cat_id){
		if(!$this->db->insert("h_diagnostic_teacher_cat",array("diagnostic_id"=>$diagnostic_id,"teacher_cat_id"=>$teacher_cat_id)))
				return false;					
		return true;
	}
        
        function getStudentCatId(){
        $res=$this->db->get_row("select * from d_teacher_category where teacher_category='Student' order by teacher_category");
        return $res['teacher_category_id']; 
        }
        
	function getDiagnosticTeacherCategory($diagnostic_id){
		$sql="SELECT dtc.diagnostic_teacher_cat_id,dtc.teacher_cat_id,dt.teacher_category FROM 
		h_diagnostic_teacher_cat dtc INNER JOIN d_teacher_category dt ON dtc.teacher_cat_id = dt.teacher_category_id
		WHERE dtc.diagnostic_id=?;";
		return $this->db->get_row($sql,array($diagnostic_id));
	}
	function getPostReviewDecisionList(){
		$sql="SELECT * from d_review_decision";
		return $this->db->get_results($sql);
	}
	function getPostReviewEngMgmtList(){
		$sql="SELECT * from d_review_engagement";
		return $this->db->get_results($sql);
	}
	function getPostReviewActionList(){
		$sql="SELECT * from d_review_action";
		return $this->db->get_results($sql);
	}
	function getPostReviewPrinTenure(){
		$sql="SELECT * from d_review_principal_tenure";
		return $this->db->get_results($sql);
	}
	function getPostReviewVision(){
		$sql="SELECT * from d_review_vision";
		return $this->db->get_results($sql);
	}
	function getPostReviewInvolvement(){
		$sql="SELECT * from d_review_involvement";
		return $this->db->get_results($sql);
	}
	function getPostReviewOpenness(){
		$sql="SELECT * from d_review_openness";
		return $this->db->get_results($sql);
	}
	function getPostReviewMidLeaders(){
		$sql="SELECT * from d_review_midleaders";
		return $this->db->get_results($sql);
	}
	function getPostReviewParentTeacherAssoc(){
		$sql="SELECT * from d_review_association";
		return $this->db->get_results($sql);
	}
        function getPostReviewAlumniAssoc(){
		$sql="SELECT * from d_review_association where association_id not in (2,4)";
		return $this->db->get_results($sql);
	}
	function getPostReviewStaffTenure(){
		$sql="SELECT * from d_review_avgstafftenure";
		return $this->db->get_results($sql);
	}
	function getPostReviewClassRooms(){
		$sql="SELECT * from d_review_classroom";
		return $this->db->get_results($sql);
	}
	function getPostReviewAvgStudents(){
		$sql="SELECT * from d_review_students";
		return $this->db->get_results($sql);
	}
        function getPostReviewAvgTeachers(){
		$sql="SELECT * from d_review_teachers";
		return $this->db->get_results($sql);
	}
	function getPostReviewRatioClass(){
		$sql="SELECT * from d_review_classratio";
		return $this->db->get_results($sql);
	}
	function getPostReviewStaffCount($type){
		$sql="SELECT * from d_review_staff where type=?";
		return $this->db->get_results($sql,array($type));
	}
	function getPostReviewStudentBody(){
		$sql="SELECT * from d_student_body";
		return $this->db->get_results($sql);
	}
	function getValidSchoolLevels($aqsData_id,$post_review_id=0){
		$sql = "SELECT distinct s.school_level_id,s.school_level,t.staff_id,t.staff_level_id,t.post_review_id FROM d_school_level s
			inner join
			h_AQS_school_level a on s.school_level_id=a.school_level_id            
            left join h_teaching_staff_school_level t on s.school_level_id=t.school_level_id and post_review_id=?            
			where AQS_data_id=?";
		$res=$this->db->get_results($sql,array($post_review_id,$aqsData_id));
		return $res?$res:array();
	}
        
        function getValidSchoolLevelsStudentTeacherClass($aqsData_id,$post_review_id=0){
		      $sql = "SELECT distinct s.school_level_id,s.school_level,t.student_id,t.teacher_id,t.school_level_id as s_l_id,t.post_review_id FROM d_school_level s
			inner join
			h_AQS_school_level a on s.school_level_id=a.school_level_id            
            left join h_post_review_student_teacher t on s.school_level_id=t.school_level_id and post_review_id=?           
			where AQS_data_id=?";
		$res=$this->db->get_results($sql,array($post_review_id,$aqsData_id));
		return $res?$res:array();
	}
        
	function getInstructionMedium($diagnostic_id=0){
		$sql = "SELECT * from d_review_medium_instrn WHERE diagnostic_id ".($diagnostic_id==0?" is NULL":" =$diagnostic_id");
		return $this->db->get_results($sql);
	}
	function getAllLanguages($aqs = 0,$val = ''){
		$sql = "SELECT * from d_language where ";
                if($aqs == 1) {
                     $sql .= " LOWER(language_name) = '$val' ";
                }else 
                    $sql .= " language_id>0";
		return $this->db->get_results($sql);
	}
	function insertPostReviewData($data)
	{
		if($this->db->insert("d_post_review",$data)){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
	function updatePostReviewData($data,$assessment_id)
	{
		if($this->db->update("d_post_review",$data,array("assessment_id"=>$assessment_id)))
			return true;		
		return false;
	}
	function getPostReviewData($assessment_id)
	{
		$sql = "SELECT * FROM d_post_review where assessment_id=?";
		return $this->db->get_row($sql,array($assessment_id));
	}
	function insertPostReviewActionPlanning($data)
	{
		if($this->db->insert("h_post_review_action_planning",$data))
			return $this->db->get_last_insert_id();
		else
			return false;
	}
        function removePostReviewActionPlanning($postReviewId)
	{
		return $this->db->delete("h_post_review_action_planning",array("post_review_id"=>$postReviewId));
	}
        function insertPostReviewActionPlanningCQ($data)
	{
		if($this->db->insert("h_post_review_action_planning_core_question",$data))
			return $this->db->get_last_insert_id();
		else
			return false;
	}
        function removePostReviewActionPlanningCQ($postReviewId)
	{
		return $this->db->delete("h_post_review_action_planning_core_question",array("post_review_id"=>$postReviewId));
	}
	function removePostReviewTeachingStaff($postReviewId){//remove school team=0 is for removing adhyayan team								
		return $this->db->delete("h_teaching_staff_school_level",array("post_review_id"=>$postReviewId));
	}
        function insertPostReviewTeachingStaff($data)
	{
		if($this->db->insert("h_teaching_staff_school_level",$data)){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
        
        function removePostReviewStudentTeacherClass($postReviewId){//remove school team=0 is for removing adhyayan team								
		return $this->db->delete("h_post_review_student_teacher",array("post_review_id"=>$postReviewId));
	}
        
        function insertPostReviewStudentTeacherClass($data)
	{
		if($this->db->insert("h_post_review_student_teacher",$data)){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
/*	function getPostReviewTeachingStaff($postReviewId)
	{
		$sql = "select t.staff_id,t.school_level_id from  h_teaching_staff_school_level t inner join d_post_review r
				on r.post_review_id=t.post_review_id 
				where assessment_id=?";
		return $this->db->get_results($sql,array($postReviewId));
	}*/
        
        // function for adding language On 01-06-2016
        function addLanguage($language){
            if($this->db->insert("h_user_language",$language)){
                return true;
            } else {
                return false;
            }
        }
        
        // function for update language On 01-06-2016
        function updateLanguage($language,$where){
            if($this->db->update("h_user_language",$language,$where)){
                return true;
            } else {
                return false;
            }
        }
	
        // function for getting language id from name or name from id
        function getLanguageData($value,$type){
            if($type=='id'){
                $condition=" and language_name='".$value."'";
                $field ="language_id";
            } else if($type=='name'){
                $condition=" and language_id='".$value."'";
                $field ="language_name";
            }
            $SQL="Select ".$field." from d_language where 1 ".$condition;
            $data = $this->db->get_row($SQL);
            if(!empty($data)){
                return $data[$field];
            } else {
                return 0;
            }
            
        }
        function getNumberOfKpasDiagnostic($diagnostic_id){
        	$res=$this->db->get_row("select count(*) as num
			from d_kpa k
			inner join h_kpa_diagnostic kd on k.kpa_id=kd.kpa_id
			where kd.diagnostic_id=?
			order by kd.`kpa_order` asc;",
        			array($diagnostic_id));
        	return $res?$res:array();
        }
        function getSchoolCommunities(){
        	$sql = "SELECT * from d_school_communities where school_community_id>0";
        	return $this->db->get_results($sql);
        }
        
        function getDiagnosticImage($diagnostic_id){
        	$res=$this->db->get_results("select d.diagnostic_image_id as 'diagnostic_image_id', f.file_name
			from d_diagnostic d
                        left join d_file f on d.diagnostic_image_id = f.file_id
			where d.diagnostic_id=? ",
        			array($diagnostic_id));
        	return $res?$res:'';
        }
        function getReportsType($assessment_type_id=1){
        	$res=$this->db->get_results("select * from d_reports where assessment_type_id=? ",
        			array($assessment_type_id));
        	return $res?$res:array();
        }
        
        function getReportName($report_id){
        	$res=$this->db->get_row("select report_name from d_reports where report_id=? ",
        			array($report_id));
        	return $res?$res:array();
        }
        function isDuplicateDiagnosticName($diagName){
        	$res = $this->db->get_row("select * from d_diagnostic a inner join h_lang_translation b on a.equivalence_id=b.equivalence_id where b.translation_text = ?",array($diagName));
        	return $res?true:false;
        }
        
        function cloneDiagnostic($diagnosticId,$diagnosticName,$user_id,$langId=DEFAULT_LANGUAGE){        
        	$create_date = date('Y-m-d h:i:s'); 
                 //echo " call clone_diagnostic($diagnosticId,$diagnosticName,$user_id,$create_date,$langId);";
        	return $this->db->get_row("call clone_diagnostic(?,?,?,?,?);",array($diagnosticId,$diagnosticName,$user_id,$create_date,$langId));        
        }
        
        function getCommentsFieldPostReview(){
   	$sql = "SELECT 
    COLUMN_name,COLUMN_COMMENT
	FROM 
    information_schema.COLUMNS
	WHERE
     table_schema='".DB_NAME."' and TABLE_NAME = 'd_post_review' and COLUMN_name in ('decision_maker','principal_tenure','decision_maker_other','management_engagement','principal_involvement','principal_openness','action_management_decision','
principal_tenure','principal_vision','middle_leaders','parent_teacher_association','alumni_association','student_body_activity','
student_body_school_level','average_staff_tenure','student_count','average_number_students_class','number_non_teaching_staff_rest','ratio_students_class_size','number_teaching_staff','number_non_teaching_staff_prep','rte');";
   	$res = $this->db->get_results($sql);
   	$res = $this->db->array_col_to_key($res, 'COLUMN_name');
   	return $res?$res:array();
   }
   static function getPostReviewDiagnosticHTML($sno,$assessment_id,$assessor_id,$addDelete=1,$viewdata=array(),$lang_id=DEFAULT_LANGUAGE){
        $obj = new diagnosticModel();
        $kpas = $obj->getKpasForAssessment($assessment_id,$assessor_id,0,$lang_id);
       // print_r($kpas);
        $kpaOpt='';
        $kqOpt='';
        $cqOpt ='';
        foreach($kpas as $kpa){
            $kpaOpt .= '<option value="'.$kpa['kpa_instance_id'].'" '.(isset($viewdata['kpa_instance_id']) && $viewdata['kpa_instance_id']==$kpa['kpa_instance_id']?'selected="selected"':"").' >'.$kpa['kpa_name'].'</option>';
        }
        if(!empty($viewdata['kpa_instance_id'])){    
            $kqs = $obj->getKeyQuestionsForAssessment($assessment_id, $assessor_id, $viewdata['kpa_instance_id'],$lang_id);
             foreach($kqs as $kq){
                    $kqOpt .= '<option value="'.$kq['key_question_instance_id'].'" '.(isset($viewdata['key_question_instance_id']) && $viewdata['key_question_instance_id']==$kq['key_question_instance_id']?'selected="selected"':"").' >'.$kq['key_question_text'].'</option>';
                }
            $cqs = $obj->getCoreQuestionsForKQAssessment($assessment_id, $assessor_id,$viewdata['key_question_instance_id'],$lang_id);
             foreach($cqs as $cq){
                    $cqOpt .= '<option value="'.$cq['core_question_instance_id'].'" '.(isset($viewdata['core_question_instance_ids']) && in_array($cq['core_question_instance_id'],explode(',',$viewdata['core_question_instance_ids']))?'selected="selected"':"").' >'.$cq['core_question_text'].'</option>';
                }    
        }
        $uniqId = uniqid();
        $html = '<tr class="prow">';
        $html .= '<td class="s_no">'.$sno.'</td>';
        $html .= '<td><select name="kpa['.$uniqId.']" class="form-control kpa" required><option value="">--KPA--</option>'.$kpaOpt.'</select></td>';
        $html .= '<td><select name="kq['.$uniqId.']" class="form-control kq" required><option value="">--KQ--</option>'.$kqOpt.'</select></td>';
        $html .= '<td><div class="mulDataWrap"><select name="cq['.$uniqId.'][]" class="form-control sq mulData" required multiple="multiple">'.$cqOpt.'</select></div></td>';
        $html .= '<td><textarea rows="15" cols="25" name="action_planning['.$uniqId.']" placeholder="Enter text" class="form-control" required>'.(empty($viewdata['action_planning'])?'':$viewdata['action_planning']).'</textarea></td>';
        $html .= '<td>'.($addDelete>0?'<a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a>':'').'</td>';
        $html .='</tr>';
        return $html;
    }
    
    function getPostReviewActionPlanningData($postReviewId){
        $sql = "select a.post_review_id,a.kpa_instance_id,a.key_question_instance_id,a.action_planning,group_concat(core_question_instance_id) as core_question_instance_ids "
                . "from h_post_review_action_planning a inner join h_post_review_action_planning_core_question b on a.post_review_id= b.post_review_id "
                . "and a.key_question_instance_id = b.key_question_instance_id"
                . " where a.post_review_id=? group by a.post_review_id,a.kpa_instance_id,a.key_question_instance_id";
        $res = $this->db->get_results($sql,array($postReviewId));
        return $res?$res:array();
    }
    //function to get accessors feedback  questions
        function getAssessorFeedbackQuestions($user_id = 0,$assessment_id = 0) {
            
                $whrCond = '';
                
                if(!empty($assessment_id)) {
                    
                     $whrCond .= " Where  assessment_id = '$assessment_id'";
                }
                if(!empty($user_id) ) {
                    $whrCond .= " AND user_id = '$user_id' ";
                }
                      $sql = " SELECT f.answer,f.peer_id,q.q_id,q.question_name,q.field_type,q.field_name, q.parent_id,q.user_type"
                   . " FROM d_feedback_question q LEFT JOIN d_feedback_question pq ON q.parent_id = pq.q_id"
                   . " LEFT JOIN (select * from d_school_feedback_answers $whrCond) f  ON f.q_id = q.q_id ORDER BY q.rank ASC  ";
                    
                   
            $res=$this->db->get_results($sql);
            return $res?$res:array();
        }
    //function to get accessors feedback  answers
        function getAssessorFeedbackAnswer($user_id = 0,$assessment_id = 0,$peer_id = 0,$is_received = 0) {
            
                $whrCond = '';
                $joinCond = '';
                if(!empty($user_id)  ) {
                    $whrCond .= "  f.user_id = '$user_id' AND ";
                }if(!empty($peer_id)) {
                
                    $whrCond .= "  f.peer_id = '$peer_id' AND ";
                }if(!empty($assessment_id)) {
                    
                    $whrCond .= ' f.assessment_id = '.$assessment_id;
                }
                if($is_received == 1) {
                    $joinCond = " INNER JOIN d_feedback_submit sb ON f.assessment_id = sb.assessment_id AND f.user_id = sb.user_id";
                    $whrCond .= " AND sb.feedback_status = 1";
                }
                if($whrCond!='') {
                    $whrCond = "Where".$whrCond;
                }
                $sql = " SELECT f.answer,f.q_id,f.user_id,f.peer_id,f.assessment_id "
                   . " FROM d_feedback_question q INNER JOIN d_school_feedback_answers f ON f.q_id = q.q_id $joinCond "
                   . " $whrCond  ";
                    
                   
            $res=$this->db->get_results($sql);
            return $res?$res:array();
        }
        //function to get assessment external team members
        function getAssessmentExternalTeam($assessment_id,$user_id) {
            
                $sql = "SELECT r.sub_role_name ,et.user_id FROM `h_assessment_external_team` et 
                    INNER JOIN d_user_sub_role r ON et.user_sub_role = r.sub_role_id  AND  et.user_id!='$user_id' 
                    WHERE et.assessment_id = '$assessment_id' 
                    UNION SELECT r.role_name ,et.user_id 
                    FROM `h_assessment_user` et INNER JOIN d_user_role r ON et.role = r.role_id 
                    WHERE et.assessment_id = '$assessment_id' AND et.user_id!='$user_id'";
                 $res=$this->db->get_results($sql);
                return $res?$res:array();
        }
        //function to insert peer feedback
        function insertPeerFeedback($data) {
                
            unset($data['isAjaxRequest']);
            unset($data['token']);
           
                if($this->db->insert("d_peer_feedback",$data)){
			return $this->db->get_last_insert_id();
		}else
			return false;
        }
        //function to insert  feedback golas
        function insertPeerFeedbackGoals($data) {
           
                if($this->db->insert("d_peer_feedback_goals",$data)){
			return $this->db->get_last_insert_id();
		}else
			return false;
        }
        //function to insert school assessment self feedback
        function submitSchoolAssessmentSelfFeedback($feedbackData,$peerFeedback = 1,$is_submit = 0,$user_id=0,$assessment_id=0,$users) {
             
           // echo "<pre>";print_r($users);die;
            $type = 0;
            //$peer = 0;
            if($peerFeedback == 1) {
                $type =1;
                $sql = "INSERT INTO d_school_feedback_answers (q_id,answer,assessment_id,sub_role_id,user_id,peer_id,type) VALUES  ";
            }
            else {
                $type = 2;
                $sql = "INSERT INTO d_school_feedback_answers (q_id,answer,assessment_id,sub_role_id,user_id,type) VALUES  ";
            }
           $values = array();
           $inner_array=array();
           $final_array=array();
            if (!empty($feedbackData)) {
            //$notkey = array('peer_id','assessment_id','sub_role','user_id');

                    if ($peerFeedback == 1) {
                        foreach ($feedbackData as $data) {

                            foreach ($data as $key => $val) {

                                if ($key != 'peer_id' && $key != 'assessment_id' && $key != 'sub_role' && $key != 'user_id') {
                                    $inner_array[] = $key;
                                    $inner_array[] = $val;
                                    $inner_array[] = $data['assessment_id'];
                                    $inner_array[] = $data['sub_role'];
                                    $inner_array[] = $data['user_id'];
                                    $inner_array[] = $data['peer_id'];
                                    $inner_array[] = $type;
                                    if ($peerFeedback == 1) {
                                        $sql .= "(" . "?,?,?,?,?,?,?" . "),";
                                    } else
                                        $sql .= "(" . "?,?,?,?,?,?" . "),";
                                }
                            }
                            //$final_array[] = $inner_array;
                            //echo "<pre>";print_r($inner_array);die;
                        }
                    }else {

                        foreach ($feedbackData as $data) {

                            //foreach ($data as $key => $val) {

                                //if ( $key != 'assessment_id' && $key != 'sub_role_id' && $key != 'user_id') {
                                    $inner_array[] = $data['q_id'];
                                    $inner_array[] = $data['answer'];
                                    $inner_array[] = $data['assessment_id'];
                                    $inner_array[] = $data['sub_role_id'];
                                    $inner_array[] = $data['user_id'];
                                    $inner_array[] = $type;
                                   // $inner_array[] = 0;
                                    if ($peerFeedback == 1) {
                                        $sql .= "(" . "?,?,?,?,?,?,?" . "),";
                                    } else
                                        $sql .= "(" . "?,?,?,?,?,?" . "),";
                                //}
                           // }
                            //$final_array[] = $inner_array;
                        }
                    }
            }
            // echo "<pre>";print_r($inner_array);
            $sql = trim($sql,",");
            $removeOldAnswersStatus =  $this->db->delete('d_school_feedback_answers',array('user_id'=>$user_id,'assessment_id'=>$assessment_id,'type'=>$type));
            $saveFeedbackStatus = $this->db->query($sql,$inner_array);
            $feedbackRoles = explode(',', FEEDBACK_ROLES);
           // print_r($users['role_ids']);
            if($is_submit == 1 && count(array_intersect($feedbackRoles, $users['role_ids']))) {
                $res=$this->db->get_row("select * from d_feedback_submit where assessment_id=?  AND user_id =? AND type='$type'",
        			array($assessment_id,$user_id));
        	if(!empty($res)) 
                    $feedbackSubmitStatus = $this->db->update("d_feedback_submit",array('feedback_status'=>1),array('user_id'=>$user_id,'assessment_id'=>$assessment_id,'type'=>$type));
                    else
                    $feedbackSubmitStatus = $this->db->insert("d_feedback_submit",array('user_id'=>$user_id,'assessment_id'=>$assessment_id,'is_submit'=>1,'type'=>$type,'feedback_status'=>1));
            }else if($is_submit == 1) 
              $feedbackSubmitStatus = $this->db->insert("d_feedback_submit",array('user_id'=>$user_id,'assessment_id'=>$assessment_id,'is_submit'=>1,'type'=>$type,'feedback_status'=>0));
            else
              $feedbackSubmitStatus = 1;
            
            if($saveFeedbackStatus && $feedbackSubmitStatus && $removeOldAnswersStatus ){
                    return true;
            }else
                    return false;
        }
        
        /*
         * get self feedback question
         */
        function getSelfFeedbackQuestion($assessment_id,$user_id) {
            
                $whrCond = '';
                if(!empty($user_id) && !empty($assessment_id)) {
                    $whrCond .= " Where user_id = '$user_id' AND assessment_id = '$assessment_id'";
                }
                $sql = "select f.answer,q2.q_id,q2.question_name,q2.field_type,r.user_sub_role,sb.sub_role_name FROM
                    (SELECT a.role as user_role, a.user_id,d.assessment_id,if(a.role =4,1 ,0) as user_sub_role FROM 
                    d_assessment d LEFT JOIN `h_assessment_user` a ON d.assessment_id = a.assessment_id    WHERE 
                    ( a.assessment_id = ?) AND (a.user_id = ?) UNION 
                    SELECT e.user_role,e.user_id,d.assessment_id,e.user_sub_role FROM  d_assessment d 
                    LEFT JOIN `h_assessment_external_team` e ON d.assessment_id = e.assessment_id WHERE ( e.assessment_id = ?) AND (e.user_id = ?)) as r 
                    INNER JOIN d_user_sub_role sb ON r.user_sub_role = sb.sub_role_id LEFT JOIN h_school_feedback_question q ON  q.sub_role_id = sb.sub_role_id 
                    LEFT JOIN d_feedback_question q2 ON q2.q_id = q.q_id  
                    LEFT JOIN (select * from d_school_feedback_answers $whrCond) f  ON f.q_id = q.q_id"
                        . " ORDER BY q2.rank desc ";
                
                
                 
                  $res=$this->db->get_results($sql,array($assessment_id,$user_id,$assessment_id,$user_id));
                return $res?$res:array();
        }
        
        /*
         * get all self feedback question
         */
        function getAllSelfFeedbackQuestion($assessment_id,$user_id) {
            
                $whrCond = '';
                if(!empty($user_id) && !empty($assessment_id)) {
                    $whrCond .= " Where user_id IN (". implode($user_id,",").") AND assessment_id = '$assessment_id'";
                }
                    $sql = "select q2.q_id,q2.question_name,q2.field_type,r.user_sub_role,r.user_id,sb.sub_role_name FROM
                    (SELECT a.role as user_role, a.user_id,d.assessment_id,if(a.role =4,1 ,0) as user_sub_role FROM 
                    d_assessment d LEFT JOIN `h_assessment_user` a ON d.assessment_id = a.assessment_id    WHERE 
                    ( a.assessment_id = ?) AND (a.user_id  IN (". implode($user_id,",").")) UNION 
                    SELECT e.user_role,e.user_id,d.assessment_id,e.user_sub_role FROM  d_assessment d 
                    LEFT JOIN `h_assessment_external_team` e ON d.assessment_id = e.assessment_id WHERE ( e.assessment_id = ?) AND (e.user_id IN (". implode($user_id,",")."))) as r 
                    INNER JOIN d_user_sub_role sb ON r.user_sub_role = sb.sub_role_id LEFT JOIN h_school_feedback_question q ON  q.sub_role_id = sb.sub_role_id 
                    LEFT JOIN d_feedback_question q2 ON q2.q_id = q.q_id                     
                     ORDER BY q2.rank desc ";
                
                
                 
                  $res=$this->db->get_results($sql,array($assessment_id,$assessment_id));
                return $res?$res:array();
        }
        /*
         * get self feedback submit status
         */
        function getSelfFeedbackStatus($assessment_id,$user_id=0,$type) {            
                
                $whrCond = '';
                
                if(!empty($user_id)) {
                    
                    $whrCond .= 'user_id = ? AND';
                    $param = array($user_id);
                }
                $param[] = $assessment_id;
                $param[] = $type;
                //$param[] = 1;
                $sql = "SELECT is_submit,feedback_status,user_id,assessment_id FROM d_feedback_submit WHERE $whrCond assessment_id = ? AND type = ?";
                if(!empty($user_id)) 
                     $res=$this->db->get_row($sql,$param);
                    else
                        $res=$this->db->get_results($sql,$param);
                return !empty($res)?$res:array();
        }
        /*
         * get self feedback submit status
         */
        function getReviewGoals($assessment_id,$user_id,$assessmentTeam = array()) {            
                
                $teamData = array();
                $sqlParams = '';
                $sql = "SELECT goal,user_id FROM d_peer_feedback_goals WHERE assessment_id = ? AND user_id  ";
                //print_r($assessmentTeam);
                if(!empty($assessmentTeam)) {
                    $teamData[] = $assessment_id;
                    foreach($assessmentTeam as $key=>$val) {
                        
                        $sqlParams .= '?,';
                        $teamData[] = $val;
                    }
                    $sqlParams = !empty($sqlParams)?trim($sqlParams,","):'';
                    $sql .= "IN(".$sqlParams.")"; 
                    $res=$this->db->get_results($sql,$teamData);
                    
                }else {
                    
                    $sql .= "= ?";
                    $res=$this->db->get_row($sql,array($assessment_id,$user_id));
                }
                //$sql = "SELECT goal FROM d_peer_feedback_goals WHERE assessment_id = ? AND user_id = ?";
               
                return !empty($res)?$res:array();
        }
        /*
         * get self feedback question
         */
        function getSchoolAssessmentAllUser($assessment_id,$user_id = 0) {
            
            
                /*$sql = "SELECT u.name,u.user_id from (SELECT a.role as user_role, a.user_id,d.assessment_id,if(a.role =4,1 ,0) as user_sub_role 
                    FROM  d_assessment d INNER JOIN `h_assessment_user` a ON d.assessment_id = a.assessment_id    
                    WHERE ( a.assessment_id = ?) AND (a.user_id != ?) UNION SELECT e.user_role,e.user_id,d.assessment_id,e.user_sub_role 
                    FROM  d_assessment d INNER JOIN `h_assessment_external_team` e ON d.assessment_id = e.assessment_id 
                    WHERE ( e.assessment_id = ?) AND (e.user_id != ?)) as a INNER JOIN d_user u ON a.user_id = u.user_id";*/
            $sqlCond = '';
            if(!empty($user_id)) {
                 $sqlCond = "  AND u.user_id!=$user_id";
            }
                $sql = "SELECT u.email,u.name,u.user_id,a.user_sub_role,a.sub_role_name from (SELECT sb.sub_role_name ,a.role as user_role, a.user_id,d.assessment_id,if(a.role =4,1 ,0) "
                     . "as user_sub_role FROM d_assessment d INNER JOIN `h_assessment_user` a "
                     . "ON d.assessment_id = a.assessment_id INNER JOIN `d_user_sub_role` sb ON sb.sub_role_id = 1  WHERE ( a.assessment_id = ?) AND a.role!=? AND a.isFilled=? "
                     . "UNION SELECT sbr.sub_role_name,e.user_role,e.user_id,d.assessment_id,e.user_sub_role FROM d_assessment d"
                     . " INNER JOIN `h_assessment_external_team` e ON d.assessment_id = e.assessment_id "
                     . " INNER JOIN `h_assessment_user` au ON au.assessment_id = e.assessment_id "
                     . " INNER JOIN `d_user_sub_role` sbr ON sbr.sub_role_id = e.user_sub_role "
                     . "WHERE ( e.assessment_id = ?) AND e.user_role !=? AND au.isFilled=? AND au.role!=3 ) as a "
                     . "INNER JOIN d_user u ON a.user_id = u.user_id $sqlCond ";
                $res=$this->db->get_results($sql,array($assessment_id,3,1,$assessment_id,3,1));
                return $res?$res:array();
        }
        /*
         * function to get all users feedback status
         */
        function getFeedbackStatus($assessment_id){
            
            //echo $feedbackAllUsers = implode(",",$feedbackUsers);
            $sql = "SELECT assessment_id,user_id FROM d_feedback_submit WHERE assessment_id = ? AND feedback_status = ? AND type = 1";
            $res=$this->db->get_results($sql,array($assessment_id,1));
            return $res?$res:array();
            
            
        }
        /*
         * function to get all users feedback status
         */
        function createSubmitNotificationQueue($assessment_id,$usersTeam){
            
            $sql = "INSERT INTO d_review_submit_notification_queue (user_id,assessment_id,status) VALUES";
            $values = '';
            $paramsValues = array();
            foreach($usersTeam as $data) {
                $values .= "(?,?,?),";
                $paramsValues[] = $data['user_id'];
                $paramsValues[] = $assessment_id;
                $paramsValues[] = 1;
            }
            if(!empty($values)) {
               $values = trim($values,",");
               $sql = $sql.$values;
               return $this->db->query($sql,$paramsValues);
            }
            return false;
            
        }
         /*
         * get assessment school name
         */
        function getAssessmentSchool($assessment_id,$user_id = 0) {
            
            
                
            
                $sql = "SELECT c.client_name from d_client  c INNER JOIN d_assessment a ON  "
                     . " a.client_id = c.client_id WHERE a.assessment_id =  ?";
                $res=$this->db->get_row($sql,array($assessment_id));
                return $res?$res['client_name']:'';
        }
        
        function getRatingsForScheme($scheme_id,$rating_level_id,$order){
        	$sql = "select b.rating_id,hlt.translation_text rating,rating_level_order from h_rating_level_scheme a 
        			inner join d_rating b on a.rating_id=b.rating_id
        			inner join h_lang_translation hlt on hlt.equivalence_id = b.equivalence_id
        			where  a.rating_level_id=? and rating_scheme_id=? and hlt.translation_type_id=8 and hlt.language_id= {$this->lang}
        	        order by a.rating_level_order $order";
        	$res = $this->db->get_results($sql, array($rating_level_id,$scheme_id));
        	return $res?$res:array();
        }
        
        /*function resetTableforHindi (){
           
            $query="Select * from h_lang_translation";
            $res=$this->db->get_results($query);
            echo $tot=count($res);
           
            if($tot==0){
                $this->db->start_transaction ();  
                //$query_1="select * from d_translation_type where translation_type_table='d_diagnostic'";
                $query_1="select * from d_translation_type";
                $res_1=$this->db->get_results($query_1);
                $array_r=array("kpa_name1","key_question_text1","core_question_text1","judgement_statement_text1","award_name1","recommendation_text1","name1","rating1");
                $array_r1=array("kpa_id","key_question_id","core_question_id","judgement_statement_id","award_id","recommendation_id","diagnostic_id","rating_id");
                //$array_r=array("name1");
                //$array_r1=array("diagnostic_id");
               
                $i=0;
                $t_type=0;
                foreach($res_1 as $key_1=>$val_1){
                   if($val_1['translation_type_table']=="d_kpa" || $val_1['translation_type_table']=="d_key_question" || $val_1['translation_type_table']=="d_core_question" || $val_1['translation_type_table']=="d_judgement_statement" ){
                       
                    $query_2="select ".$array_r[$i]." as name,".$array_r1[$i]." as id,isActive,equivalence_id from ".$val_1['translation_type_table']."";
                   
                   }else if($val_1['translation_type_table']=="d_diagnostic"){
                   $query_2="select ".$array_r[$i]." as name,".$array_r1[$i]." as id,equivalence_id,date_created,isPublished,date_published,user_id from ".$val_1['translation_type_table']."";
                       
                   }else{
                    $query_2="select ".$array_r[$i]." as name,".$array_r1[$i]." as id,equivalence_id from ".$val_1['translation_type_table']."";
                      
                   }
                  
                    $res_2=$this->db->get_results($query_2);
                   
                    foreach($res_2 as $key_2=>$val_2){
                       
                        //print_r($val_2);
                        if($this->db->insert("d_equivalence",array("equivalence_id"=>0))){
                        $equivalence_id=$this->db->get_last_insert_id();
                       
                        $query_3="select * from h_lang_translation_back where equivalence_id=? && translation_type_id=? && language_id=?";
                        $res_3=$this->db->get_row($query_3,array($val_2['equivalence_id'],$val_1['translation_type_id'],8));
                        $isActive=1;
                        $totf=count($res_3);
                       
                        if($val_1['translation_type_table']=="d_kpa" || $val_1['translation_type_table']=="d_key_question" || $val_1['translation_type_table']=="d_core_question" || $val_1['translation_type_table']=="d_judgement_statement" ){
                        $isActive=$val_2['isActive'];
                       
                        }
                       
                        if($isActive==NULL && $isActive==""){
                          $isActive=1; 
                        }
                       
                       
                       
                        if($totf>1 && $res_3['parent_lang_translation_id']==NULL){
                         $language_id=8;
                         $translation_text=$res_3['translation_text'];
                        }else{
                         $language_id=9;
                         $translation_text=$val_2['name'];
                        }
                       
                        if($translation_text==NULL){
                        $query_31="Select * from h_lang_translation_back where equivalence_id=? && translation_type_id=? && language_id=?";
                        $res_31=$this->db->get_row($query_31,array($val_2['equivalence_id'],$val_1['translation_type_id'],9));
                        $translation_text=$res_31['translation_text'];
                        }
                       
                        if($val_1['translation_type_table']=="d_diagnostic"){
                        $query_32="select a.*,b.* from h_lang_translation_back a left join h_lang_trans_diagnostics_details_back b on a.lang_translation_id=b.lang_translation_id where equivalence_id=? && translation_type_id=? && language_id=?";
                        $res_32=$this->db->get_row($query_32,array($val_2['equivalence_id'],$val_1['translation_type_id'],9));
                        $tot_f3=count($res_32);
                       
                        if($tot_f3>1){
                        $val_2['date_created']=$res_32['date_created'];
                        $val_2['date_published']=$res_32['date_published'];
                        $val_2['isPublished']=$res_32['isPublished'];
                        $val_2['user_id']=$res_32['create_user_id'];
                        }
                       
                        }
                       
                       
                       
                        if($this->db->insert("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>$language_id,"translation_type_id"=>$val_1['translation_type_id'],"translation_text"=>"".$translation_text."","isActive"=>$isActive))){
                           $last_id=$this->db->get_last_insert_id();
                          
                           if($totf>1 && $language_id==9){
                             
                             if($this->db->insert("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>8,"translation_type_id"=>$val_1['translation_type_id'],"translation_text"=>"".$res_3['translation_text']."","isActive"=>$isActive))){
                             $last_id_1=$this->db->get_last_insert_id();
                             
                             if($val_1['translation_type_table']=="d_diagnostic"){
                                
                        $query_32="select a.*,b.* from h_lang_translation_back a left join h_lang_trans_diagnostics_details_back b on a.lang_translation_id=b.lang_translation_id where equivalence_id=? && translation_type_id=? && language_id=?";
                        $res_32=$this->db->get_row($query_32,array($val_2['equivalence_id'],$val_1['translation_type_id'],8));
                        $tot_f3=count($res_32);
                       
                        if($tot_f3>1){
                        $val_2['date_created']=$res_32['date_created'];
                        $val_2['date_published']=$res_32['date_published'];
                        $val_2['isPublished']=$res_32['isPublished'];
                        $val_2['user_id']=$res_32['create_user_id'];
                        } 
                           
                               if($this->db->insert("h_lang_trans_diagnostics_details",array("lang_translation_id"=>$last_id_1,"date_created"=>$val_2['date_created'],"date_published"=>$val_2['date_published'],"isPublished"=>$val_2['isPublished'],"create_user_id"=>$val_2['user_id'],"publish_user_id"=>$val_2['user_id']))){
                                  
                               }else{
                                   $t_type=6;
                                   return;
                               }
                             }
                            
                             }else{
                                   $t_type=5;
                                   return;
                               } 
                           }
                          
                           if($this->db->update($val_1['translation_type_table'],array("equivalence_id"=>$equivalence_id),array($array_r1[$i]=>$val_2['id']))){
                              
                           }else{
                                   $t_type=4;
                                   return;
                               }
                          
                           if($val_1['translation_type_table']=="d_diagnostic"){
                           
                               if($this->db->insert("h_lang_trans_diagnostics_details",array("lang_translation_id"=>$last_id,"date_created"=>$val_2['date_created'],"date_published"=>$val_2['date_published'],"isPublished"=>$val_2['isPublished'],"create_user_id"=>$val_2['user_id'],"publish_user_id"=>$val_2['user_id']))){
                                  
                               }else{
                                   $t_type=3;
                                   return;
                               }
                           }
                          
                          
                        }else{
                            $t_type=2;
                            return;
                        }
                       
                       
                        }else{
                            $t_type=1;
                            return;
                        }
                       
                    }
                   
                    $i++;
                }
               
                
               
                if($t_type==0){
                    $this->db->commit ();
                }else{
                    return $t_type;
                    $this->db->rollback ();
               
       
                }
               
               
               
               
               
            }
            
            
            $hindi_convert=array("5"=>"d_award","6"=>"d_recommendation","8"=>"d_rating");
                $hindi_convert_id=array("5"=>"award_name1","6"=>"recommendation_text1","8"=>"rating1");
                foreach($hindi_convert as $key=>$val){
                    $field=$hindi_convert_id[$key];
                    echo $query100="select ".$field." as name , equivalence_id from ".$val."";
                    $res_100=$this->db->get_results($query100);
                   
                    foreach($res_100 as $key_1=>$val_1){
                       
                        $query_101="select * from h_lang_translation where equivalence_id=? && language_id=?";
                        $res_101=$this->db->get_row($query_101,array($val_1['equivalence_id'],8));
                        $totf=count($res_101);
                       
                        if($totf<=1){
                         
                           if($this->db->insert("h_lang_translation",array("equivalence_id"=>$val_1['equivalence_id'],"language_id"=>8,"translation_type_id"=>$key,"translation_text"=>"".$val_1['name']."","isActive"=>1))){
                           echo $last_id=$this->db->get_last_insert_id();
                           echo"<br>";    
                           echo $key;
                           }
                          
                        }else{
                            $t_type=100;
                            return;
                        }
                       
                      
                       
                    }
                   
                   
                }
           
           
        }*/
        
        function getAssessmentPrefferedLanguage($assessment_id){
            
            $sql="SELECT language_id FROM d_assessment WHERE assessment_id = ?";                        
                                
            $res=$this->db->get_row($sql,array($assessment_id));
            return $res?$res:array();
        }
        
        //function to send notification after admin/tap approvie peer feedback
        function sendNotification($feedbackData,$school_name,$type,$userEmail='',$userName=''){
            
            if(!empty($type)){
                $sql="SELECT nm.sender,nm.sender_name,nm.cc,nm.subject,nt.template_text,nt.id FROM d_review_notification_template nt INNER JOIN h_review_notification_mail_users nm "
                     . " ON nt.id = nm.notification_id WHERE nt.id='$type' AND  nm.status = 1";                        

                $res=$this->db->get_row($sql);
                if(!empty($res)) {

                    //echo '<a href="http://www.website.com/page.html">Click here</a>';
                   // echo $type;die;
                    //echo "<pre>";print_r($res);
                    $subject = str_replace('_school_',$school_name,$res['subject']) ;
                    //$sender = $res['sender'];
                    $sender = $res['sender'];
                    $body = str_replace('_school_',$school_name,$res['template_text']);
                    $link = "<a href=".SITEURL.'index.php?controller=assessment&action=assessment'.">here</a>";
                    //$cc = $res['cc'];
                    //$cc = 'deepakchauhan89@gmail.com';
                    if($type == 3) {
                        
                        
                        $body = str_replace('_link_',$link,$body) ;
                        foreach($feedbackData as $data) {

                            $name = $data['name'];
                            $mail_body = nl2br(str_replace('_name_',$data['name'],$body)) ;
                            $to = $data['email'];
                            $name = $data['name'];
                            $sender = $res['sender'];
                            $senderName = $res['sender_name'];
                            $cc=$res['cc'];
                           // $to = 'deepak.t@tatrasdata.com';
                            sendEmail($sender,$senderName,$to,$name,$cc,'',$subject,$mail_body);                       

                        }
                    }else if($type == 2) {
                        //echo $toName = $feedbackData['userName'];
                        $mail_body = nl2br(str_replace('_name_',$userName,$body)) ;
                        //$userEmail = 'deepak.t@tatrasdata.com';
                        //$sender = 'amisha.modi@adhyayan.asia';
                        //$senderName = 'Amisha Modi';
                        $sender = $res['sender'];
                        $senderName = $res['sender_name'];
                        //$cc = 'amisha.modi@adhyayan.asia';
                        $cc=$res['cc'];
                        sendEmail($sender,$senderName,$userEmail,$userName,$cc,'',$subject,$mail_body);   
                    }else if($type == 1) {
                        
                        $link = "<a href=".SITEURL.'index.php'.">here</a>";
                        $body = str_replace('_link_',$link,$body) ;
                        $mail_body = nl2br(str_replace('_name_',$userName,$body)) ;
                        //$userEmail = 'deepak.t@tatrasdata.com';
                        //$sender = 'amisha.modi@adhyayan.asia';
                        //$senderName = 'Amisha Modi';
                        $sender = $res['sender'];
                        $senderName = $res['sender_name'];
                        
                        //$ccEmail = 'amisha.modi@adhyayan.asia';
                        //$ccName = 'Amisha Modi';
                        $cc=$res['cc'];
                        sendEmail($sender,$senderName,$userEmail,$userName,$cc,'',$subject,$mail_body);   
                    }
                }
              //  echo "<pre>";print_r($feedbackData);die;
                return $res?$res:array();
            }
            return array();
        }
        
        
        function resetTableforHindi (){
            
            $query="Select * from h_lang_translation";
            $res=$this->db->get_results($query);
            echo $tot=count($res);
            
            if($tot==0){
                $this->db->start_transaction ();   
                //$query_1="select * from d_translation_type where translation_type_table='d_diagnostic'";
                $query_1="select * from d_translation_type";
                $res_1=$this->db->get_results($query_1);
                $array_r=array("kpa_name1","key_question_text1","core_question_text1","judgement_statement_text1","award_name1","recommendation_text1","name1","rating1");
                $array_r1=array("kpa_id","key_question_id","core_question_id","judgement_statement_id","award_id","recommendation_id","diagnostic_id","rating_id");
                //$array_r=array("name1");
                //$array_r1=array("diagnostic_id");
                
                $i=0;
                $t_type=0;
                foreach($res_1 as $key_1=>$val_1){
                   if($val_1['translation_type_table']=="d_kpa" || $val_1['translation_type_table']=="d_key_question" || $val_1['translation_type_table']=="d_core_question" || $val_1['translation_type_table']=="d_judgement_statement" ){
                        
                    $query_2="select ".$array_r[$i]." as name,".$array_r1[$i]." as id,isActive,equivalence_id from ".$val_1['translation_type_table']."";
                    
                   }else if($val_1['translation_type_table']=="d_diagnostic"){
                   $query_2="select ".$array_r[$i]." as name,".$array_r1[$i]." as id,equivalence_id,date_created,isPublished,date_published,user_id from ".$val_1['translation_type_table']."";
                        
                   }else{
                    $query_2="select ".$array_r[$i]." as name,".$array_r1[$i]." as id,equivalence_id from ".$val_1['translation_type_table']."";
                       
                   }
                   
                    $res_2=$this->db->get_results($query_2);
                    
                    foreach($res_2 as $key_2=>$val_2){
                        
                        //print_r($val_2);
                        if($this->db->insert("d_equivalence",array("equivalence_id"=>0))){
                        $equivalence_id=$this->db->get_last_insert_id(); 
                        
                        $query_3="select * from h_lang_translation_back where equivalence_id=? && translation_type_id=? && language_id=?";
                        $res_3=$this->db->get_row($query_3,array($val_2['equivalence_id'],$val_1['translation_type_id'],8));
                        $isActive=1;
                        $totf=count($res_3);
                        
                        if($val_1['translation_type_table']=="d_kpa" || $val_1['translation_type_table']=="d_key_question" || $val_1['translation_type_table']=="d_core_question" || $val_1['translation_type_table']=="d_judgement_statement" ){
                        $isActive=$val_2['isActive']; 
                        
                        }
                        
                        if($isActive==NULL && $isActive==""){
                          $isActive=1;  
                        }
                        
                        
                        $query_311="Select * from h_lang_translation_back where equivalence_id=? && translation_type_id=? && language_id=?";
                        $res_311=$this->db->get_row($query_311,array($val_2['equivalence_id'],$val_1['translation_type_id'],9));
                        $totf311=count($res_311);
                        
                        
                        if($totf>1 && $totf311<=1 && $res_3['parent_lang_translation_id']==NULL){
                         $language_id=8; 
                         $translation_text=$res_3['translation_text'];
                        }else{
                         $language_id=9;
                         $translation_text=$val_2['name'];
                        }
                        
                        if($translation_text==NULL){
                        $query_31="Select * from h_lang_translation_back where equivalence_id=? && translation_type_id=? && language_id=?";
                        $res_31=$this->db->get_row($query_31,array($val_2['equivalence_id'],$val_1['translation_type_id'],9));
                        $translation_text=$res_31['translation_text'];
                        }
                        
                        if($val_1['translation_type_table']=="d_diagnostic"){
                        $query_32="select a.*,b.* from h_lang_translation_back a left join h_lang_trans_diagnostics_details_back b on a.lang_translation_id=b.lang_translation_id where equivalence_id=? && translation_type_id=? && language_id=?";
                        $res_32=$this->db->get_row($query_32,array($val_2['equivalence_id'],$val_1['translation_type_id'],9));
                        $tot_f3=count($res_32);
                        
                        if($tot_f3>1){
                        $val_2['date_created']=$res_32['date_created'];
                        $val_2['date_published']=$res_32['date_published'];
                        $val_2['isPublished']=$res_32['isPublished'];
                        $val_2['user_id']=$res_32['create_user_id'];
                        }
                        
                        }
                        
                        
                        
                        if($this->db->insert("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>$language_id,"translation_type_id"=>$val_1['translation_type_id'],"translation_text"=>"".$translation_text."","isActive"=>$isActive))){
                           $last_id=$this->db->get_last_insert_id();
                           
                           if($totf>1 && $language_id==9){
                              
                             if($this->db->insert("h_lang_translation",array("equivalence_id"=>$equivalence_id,"language_id"=>8,"translation_type_id"=>$val_1['translation_type_id'],"translation_text"=>"".$res_3['translation_text']."","isActive"=>$isActive))){
                             $last_id_1=$this->db->get_last_insert_id();
                              
                             if($val_1['translation_type_table']=="d_diagnostic"){
                                 
                        $query_32="select a.*,b.* from h_lang_translation_back a left join h_lang_trans_diagnostics_details_back b on a.lang_translation_id=b.lang_translation_id where equivalence_id=? && translation_type_id=? && language_id=?";
                        $res_32=$this->db->get_row($query_32,array($val_2['equivalence_id'],$val_1['translation_type_id'],8));
                        $tot_f3=count($res_32);
                        
                        if($tot_f3>1){
                        $val_2['date_created']=$res_32['date_created'];
                        $val_2['date_published']=$res_32['date_published'];
                        $val_2['isPublished']=$res_32['isPublished'];
                        $val_2['user_id']=$res_32['create_user_id'];
                        }  
                            
                               if($this->db->insert("h_lang_trans_diagnostics_details",array("lang_translation_id"=>$last_id_1,"date_created"=>$val_2['date_created'],"date_published"=>$val_2['date_published'],"isPublished"=>$val_2['isPublished'],"create_user_id"=>$val_2['user_id'],"publish_user_id"=>$val_2['user_id']))){
                                   
                               }else{
                                   $t_type=6;
                                   return;
                               }
                             }
                             
                             }else{
                                   $t_type=5;
                                   return;
                               }  
                           }
                           
                           if($this->db->update($val_1['translation_type_table'],array("equivalence_id"=>$equivalence_id),array($array_r1[$i]=>$val_2['id']))){
                               
                           }else{
                                   $t_type=4;
                                   return;
                               }
                           
                           if($val_1['translation_type_table']=="d_diagnostic"){
                            
                               if($this->db->insert("h_lang_trans_diagnostics_details",array("lang_translation_id"=>$last_id,"date_created"=>$val_2['date_created'],"date_published"=>$val_2['date_published'],"isPublished"=>$val_2['isPublished'],"create_user_id"=>$val_2['user_id'],"publish_user_id"=>$val_2['user_id']))){
                                   
                               }else{
                                   $t_type=3;
                                   return;
                               }
                           }
                           
                           
                        }else{
                            $t_type=2;
                            return;
                        }
                        
                        
                        }else{
                            $t_type=1;
                            return;
                        }
                        
                    }
                    
                    $i++;
                }
                
                
                
                if($t_type==0){
                    $this->db->commit ();
                }else{
                    return $t_type;
                    $this->db->rollback ();
				
		
                }
                
                
                
                
                
                
                
            }
            
            
                if(isset($_REQUEST['second']) && $_REQUEST['second']==1 && $tot>0){
                $this->db->start_transaction ();
                $t_type=0;
                $hindi_convert=array("5"=>"d_award","6"=>"d_recommendation","8"=>"d_rating");
                //$hindi_convert=array("8"=>"d_rating");
                $hindi_convert_id=array("5"=>"award_name1","6"=>"recommendation_text1","8"=>"rating1");
                foreach($hindi_convert as $key=>$val){
                    $field=$hindi_convert_id[$key];
                    echo $query100="select ".$field." as name , equivalence_id from ".$val."";
                    $res_100=$this->db->get_results($query100);
                    
                    foreach($res_100 as $key_1=>$val_1){
                        
                        $query_101="select * from h_lang_translation where equivalence_id=? && language_id=?";
                        $res_101=$this->db->get_row($query_101,array($val_1['equivalence_id'],8));
                        $totf=count($res_101);
                        
                        if($totf<=1){
                          
                           if($this->db->insert("h_lang_translation",array("equivalence_id"=>$val_1['equivalence_id'],"language_id"=>8,"translation_type_id"=>$key,"translation_text"=>"".$val_1['name']."","isActive"=>1))){
                           echo $last_id=$this->db->get_last_insert_id();
                           echo"<br>";     
                           echo $key;
                           }else{
                            echo $t_type=100;
                            return;
                          }
                           
                        }
                        
                       
                        
                    }
                    
                    
                }
                
                if($t_type==0){
                    $this->db->commit ();
                }else{
                    return $t_type;
                    $this->db->rollback ();
				
		
                }
                
                }
                
                
                
                if(isset($_REQUEST['second']) && $_REQUEST['second']==2 && $tot>0){
                $this->db->start_transaction ();
                $t_type=0;
                $hindi_convert=array("5"=>"d_award","6"=>"d_recommendation","8"=>"d_rating");
                //$hindi_convert=array("8"=>"d_rating");
                $hindi_convert_id=array("5"=>"award_name1","6"=>"recommendation_text1","8"=>"rating1");
                foreach($hindi_convert as $key=>$val){
                    $field=$hindi_convert_id[$key];
                    echo $query100="select ".$field." as name , equivalence_id from ".$val."";
                    $res_100=$this->db->get_results($query100);
                    
                    foreach($res_100 as $key_1=>$val_1){
                        
                        $query_101="select * from h_lang_translation where equivalence_id=? && language_id=?";
                        $res_101=$this->db->get_row($query_101,array($val_1['equivalence_id'],18));
                        $totf=count($res_101);
                        
                        if($totf<=1){
                          
                           if($this->db->insert("h_lang_translation",array("equivalence_id"=>$val_1['equivalence_id'],"language_id"=>18,"translation_type_id"=>$key,"translation_text"=>"".$val_1['name']."","isActive"=>1))){
                           echo $last_id=$this->db->get_last_insert_id();
                           echo"<br>";     
                           echo $key;
                           }else{
                            echo $t_type=100;
                            return;
                          }
                           
                        }
                        
                       
                        
                    }
                    
                    
                }
                
                if($t_type==0){
                    $this->db->commit ();
                }else{
                    return $t_type;
                    $this->db->rollback ();
				
		
                }
                
                }
                
                
                if(isset($_REQUEST['second']) && $_REQUEST['second']==3 && $tot>0){
                $this->db->start_transaction ();
                $t_type=0;
                $hindi_convert=array("5"=>"d_award","6"=>"d_recommendation","8"=>"d_rating");
                //$hindi_convert=array("8"=>"d_rating");
                $hindi_convert_id=array("5"=>"award_name1","6"=>"recommendation_text1","8"=>"rating1");
                foreach($hindi_convert as $key=>$val){
                    $field=$hindi_convert_id[$key];
                    echo $query100="select ".$field." as name , equivalence_id from ".$val."";
                    $res_100=$this->db->get_results($query100);
                    
                    foreach($res_100 as $key_1=>$val_1){
                        
                        $query_101="select * from h_lang_translation where equivalence_id=? && language_id=?";
                        $res_101=$this->db->get_row($query_101,array($val_1['equivalence_id'],2));
                        $totf=count($res_101);
                        
                        if($totf<=1){
                          
                           if($this->db->insert("h_lang_translation",array("equivalence_id"=>$val_1['equivalence_id'],"language_id"=>2,"translation_type_id"=>$key,"translation_text"=>"".$val_1['name']."","isActive"=>1))){
                           echo $last_id=$this->db->get_last_insert_id();
                           echo"<br>";     
                           echo $key;
                           }else{
                            echo $t_type=100;
                            return;
                          }
                           
                        }
                        
                       
                        
                    }
                    
                    
                }
                
                if($t_type==0){
                    $this->db->commit ();
                }else{
                    return $t_type;
                    $this->db->rollback ();
				
		
                }
                
                }
             
            
            
        }
        
        function resetDataStudentReviewNewLogic(){
            $this->db->start_transaction ();
            $query_1="select ga.group_assessment_id,asg.assessment_id,hu.user_id,isFilled 
                      from d_group_assessment ga 
                      inner join h_assessment_ass_group asg on ga.group_assessment_id=asg.group_assessment_id
                      inner join h_assessment_user hu on asg.assessment_id = hu.assessment_id 
                      where hu.role=3 and ga.assessment_type_id=4 and (hu.isFilled=1  ||  hu.percComplete>=100);";
            $result_1=$this->db->get_results($query_1);
            
            $query_2="select ga.group_assessment_id,asg.assessment_id,hu.user_id,isFilled 
                      from d_group_assessment ga 
                      inner join h_assessment_ass_group asg on ga.group_assessment_id=asg.group_assessment_id
                      inner join h_assessment_user hu on asg.assessment_id = hu.assessment_id 
                       where hu.role=4 and ga.assessment_type_id=4 and (hu.isFilled=1  ||  hu.percComplete>=100);";
            $result_2=$this->db->get_results($query_2);
            
            $t_type=0;
            $studentidint="";
                        foreach($result_1 as $key=>$val){
                        $assessment_id=$val['assessment_id'];
                        $user_id=$val['user_id'];
                        $query_3="drop temporary table if exists groupedData;
			create temporary table groupedData
			select key_question_instance_id,a.assessment_id,rating1,count(*) cnt,user_id,rating_id
			from h_cq_score a
			inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id
			inner join d_rating c on a.d_rating_rating_id = c.rating_id
			inner join h_kq_cq d on a.core_question_instance_id = d.core_question_instance_id
			where a.assessment_id=? and user_id=?
			group by key_question_instance_id,a.assessment_id,rating1
			order by assessment_id,key_question_instance_id desc;";
                        
                        $result_3=$this->db->query($query_3,array($assessment_id,$user_id));
                        
                        $query_5="select sum(if(rating1='Exceptional',1*cnt,0)) Exceptional,sum(if(rating1='Proficient',1*cnt,0)) Proficient,sum(if(rating1='Developing',1*cnt,0)) Developing,sum(if(rating1='Emerging',1*cnt,0)) Emerging,sum(if(rating1='Foundation',1*cnt,0)) Foundation,key_question_instance_id,assessment_id,user_id from groupedData group by user_id";
                        $result_5=$this->db->get_row($query_5);
                        
                        $Exceptional_SUB=$result_5['Exceptional'];
                        $Proficient_SUB=$result_5['Proficient'];
                        $Developing_SUB=$result_5['Developing'];
                        $Emerging_SUB=$result_5['Emerging'];
                        $Foundation_SUB=$result_5['Foundation'];
                        $sub=array();
                        $sub['s5']=$Exceptional_SUB;
                        $sub['s4']=$Proficient_SUB;
                        $sub['s3']=$Developing_SUB;
                        $sub['s2']=$Emerging_SUB;
                        $sub['s1']=$Foundation_SUB;
                        //echo"<pre>";
                        //print_r($result_5);
                        //echo"</pre>";
                        $query_4="drop temporary table if exists groupedData1;
			        create temporary table groupedData1
                                select kpa_instance_id,a.assessment_id,rating1,count(*) cnt,user_id,rating_id from h_kq_instance_score a
                                inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id
			        inner join d_rating c on a.d_rating_rating_id = c.rating_id
			        inner join h_kpa_kq d on a.key_question_instance_id = d.key_question_instance_id
                                where a.assessment_id=? and user_id=?
                                group by kpa_instance_id,a.assessment_id,rating1
			        order by a.assessment_id,kpa_instance_id desc";
                        $result_4=$this->db->query($query_4,array($assessment_id,$user_id));
                        
                        $query_6="select sum(if(rating1='Exceptional',1*cnt,0)) Exceptional,sum(if(rating1='Proficient',1*cnt,0)) Proficient,sum(if(rating1='Developing',1*cnt,0)) Developing,sum(if(rating1='Emerging',1*cnt,0)) Emerging,sum(if(rating1='Foundation',1*cnt,0)) Foundation,kpa_instance_id,assessment_id,user_id from groupedData1 group by user_id";
                        $result_6=$this->db->get_row($query_6);
                        //echo"<pre>";
                        //print_r($result_6);
                        //echo"</pre>";
                        
                        $Exceptional_KEY=$result_6['Exceptional'];
                        $Proficient_KEY=$result_6['Proficient'];
                        $Developing_KEY=$result_6['Developing'];
                        $Emerging_KEY=$result_6['Emerging'];
                        $Foundation_KEY=$result_6['Foundation'];
                        $kpa=array();
                        $kpa['s5']=$Exceptional_KEY;
                        $kpa['s4']=$Proficient_KEY;
                        $kpa['s3']=$Developing_KEY;
                        $kpa['s2']=$Emerging_KEY;
                        $kpa['s1']=$Foundation_KEY;
                        $kpa_internal=$this->getKPARatingNewLogic($kpa,$sub)+20;
                        $querykpa="select * from h_kpa_instance_score where assessment_id=? && assessor_id=? && kpa_instance_id=?";
                        $resultkpa=$this->db->get_row($querykpa,array($assessment_id,$user_id,$result_6['kpa_instance_id']));
                        //echo $resultkpa['id'];
                        if(!$this->db->update("h_kpa_instance_score",array("d_rating_rating_id"=>$kpa_internal),array("id"=>$resultkpa['id']))){
                            $t_type++;
                        }else{
                            $studentidint.="".$user_id.",";
                        }
                        
                        }
                        
                        $studentidext="";
                        foreach($result_2 as $key=>$val){
                        $assessment_id=$val['assessment_id'];
                        $user_id=$val['user_id'];
                        $query_3="drop temporary table if exists groupedData;
			create temporary table groupedData
			select key_question_instance_id,a.assessment_id,rating1,count(*) cnt,user_id,rating_id
			from h_cq_score a
			inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id
			inner join d_rating c on a.d_rating_rating_id = c.rating_id
			inner join h_kq_cq d on a.core_question_instance_id = d.core_question_instance_id
			where a.assessment_id=? and user_id=?
			group by key_question_instance_id,a.assessment_id,rating1
			order by assessment_id,key_question_instance_id desc;";
                        
                        $result_3=$this->db->query($query_3,array($assessment_id,$user_id));
                        
                        $query_5="select sum(if(rating1='Exceptional',1*cnt,0)) Exceptional,sum(if(rating1='Proficient',1*cnt,0)) Proficient,sum(if(rating1='Developing',1*cnt,0)) Developing,sum(if(rating1='Emerging',1*cnt,0)) Emerging,sum(if(rating1='Foundation',1*cnt,0)) Foundation,key_question_instance_id,assessment_id,user_id from groupedData group by user_id";
                        $result_5=$this->db->get_row($query_5);
                        
                        $Exceptional_SUB=$result_5['Exceptional'];
                        $Proficient_SUB=$result_5['Proficient'];
                        $Developing_SUB=$result_5['Developing'];
                        $Emerging_SUB=$result_5['Emerging'];
                        $Foundation_SUB=$result_5['Foundation'];
                        
                        $sub=array();
                        $sub['s5']=$Exceptional_SUB;
                        $sub['s4']=$Proficient_SUB;
                        $sub['s3']=$Developing_SUB;
                        $sub['s2']=$Emerging_SUB;
                        $sub['s1']=$Foundation_SUB;
                        //echo"<pre>";
                        //print_r($result_5);
                        //echo"</pre>";
                        $query_4="drop temporary table if exists groupedData1;
			        create temporary table groupedData1
                                select kpa_instance_id,a.assessment_id,rating1,count(*) cnt,user_id,rating_id from h_kq_instance_score a
                                inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id
			        inner join d_rating c on a.d_rating_rating_id = c.rating_id
			        inner join h_kpa_kq d on a.key_question_instance_id = d.key_question_instance_id
                                where a.assessment_id=? and user_id=?
                                group by kpa_instance_id,a.assessment_id,rating1
			        order by a.assessment_id,kpa_instance_id desc";
                        $result_4=$this->db->query($query_4,array($assessment_id,$user_id));
                        
                        $query_6="select sum(if(rating1='Exceptional',1*cnt,0)) Exceptional,sum(if(rating1='Proficient',1*cnt,0)) Proficient,sum(if(rating1='Developing',1*cnt,0)) Developing,sum(if(rating1='Emerging',1*cnt,0)) Emerging,sum(if(rating1='Foundation',1*cnt,0)) Foundation,kpa_instance_id,assessment_id,user_id from groupedData1 group by user_id";
                        $result_6=$this->db->get_row($query_6);
                        //echo"<pre>";
                        //print_r($result_6);
                        //echo"</pre>";
                        
                        $Exceptional_KEY=$result_6['Exceptional'];
                        $Proficient_KEY=$result_6['Proficient'];
                        $Developing_KEY=$result_6['Developing'];
                        $Emerging_KEY=$result_6['Emerging'];
                        $Foundation_KEY=$result_6['Foundation'];
                        $kpa=array();
                        $kpa['s5']=$Exceptional_KEY;
                        $kpa['s4']=$Proficient_KEY;
                        $kpa['s3']=$Developing_KEY;
                        $kpa['s2']=$Emerging_KEY;
                        $kpa['s1']=$Foundation_KEY;
                        $kpa_internal=$this->getKPARatingNewLogic($kpa,$sub)+20;
                        //if($user_id=="1120" && $assessment_id==830){
                          //echo"<pre>";
                          //print_r($result_6);
                          //echo"</pre>";   
                          //echo $kpa_internal;
                          //print_r($kpa);
                          //print_r($sub);
                          //echo"<br>";
                        //}
                        $querykpa="select * from h_kpa_instance_score where assessment_id=? && assessor_id=? && kpa_instance_id=?";
                        $resultkpa=$this->db->get_row($querykpa,array($assessment_id,$user_id,$result_6['kpa_instance_id']));
                        //echo $resultkpa['id'];
                        if(!$this->db->update("h_kpa_instance_score",array("d_rating_rating_id"=>$kpa_internal),array("id"=>$resultkpa['id']))){
                            $t_type++;
                        }else{
                            $studentidext.="".$user_id.",";
                        }
                        
                        }
                        
                        
                if($t_type==0){
                    $this->db->commit ();
                    echo $studentidint;
                    echo"<br>";
                    echo $studentidext;
                    echo"<br>";
                    echo"Done";
                }else{
                    echo $t_type;
                    echo"RollBack";
                    $this->db->rollback ();
		}
                        
            
            
        }
        
        function getKPARatingNewLogic($kpa,$sub){
                                                        $ftot=0;
                                                        $tot=(1*$kpa['s1'])+(2*$kpa['s2'])+(3*$kpa['s3'])+(4*$kpa['s4'])+(5*$kpa['s5']);
                                                        $ftot=round($tot/3);
                                                        
                                                        if($sub['s5']>3){
                                                            return 5;
                                                        }else if($kpa['s5']>1){
							    return 5;
                                                        }else if($sub['s4']>3){
                                                            return 4;
                                                        }else if($kpa['s4']>1){
							    return 4;
                                                        }else if($ftot>0){
                                                           return $ftot;
                                                        }else{
					                   return 0;
                                                        }
                                                        
                                                        /*if($sub['s5']>3)
                                                            return 5;
                                                        else if($kpa['s5']>1)
							    return 5;
                                                        else if($sub['s4']>3)
                                                            return 4;
                                                        else if($kpa['s4']>1)
							    return 4;
                                                        else if($kpa['s3']>1)
							    return 3;
                                                        else if($kpa['s2']>1)
							    return 2;
                                                        else if($kpa['s1']>1)
							    return 1;
						        else if($kpa['s1']==0 && $kpa['s2']==0)
					                    return 4;
				                        else if($kpa['s1']==0 && $kpa['s3']==0)
					                    return 4;
                                                        else if($kpa['s1']==0 && $kpa['s4']==0)
					                    return 3;
                                                        else if($kpa['s1']==0 && $kpa['s5']==0)
					                    return 3;
                                                        else if($kpa['s2']==0 && $kpa['s3']==0)
					                    return 4;
                                                        else if($kpa['s2']==0 && $kpa['s4']==0)
					                    return 3;
                                                        else if($kpa['s2']==0 && $kpa['s5']==0)
					                    return 3;
                                                        else if($kpa['s3']==0 && $kpa['s4']==0)
					                    return 2;
                                                        else if($kpa['s3']==0 && $kpa['s5']==0)
					                    return 2;
                                                        else if($kpa['s4']==0 && $kpa['s5']==0)
					                    return 2;
                                                        else
					                    return 0;
                                                         */
        }
        
         /**************************Condition in case of multiple Round 1 and Round 2 Exists Start*************************/
                public function getRound2AssessmentIdsCount($clId,$dgId){
                    //echo 'adgsyadgsy';die;
                        $round2_comp='a.aqs_round IN(1,2)';
                        
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

                        where a.client_id='$clId'  &&  (d.assessment_type_id=1 || d.assessment_type_id=5) && a.isAssessmentActive=1 && c.is_guest!=1 and  a.diagnostic_id = '$dgId' and ".$round2_comp." and a.d_sub_assessment_type_id!=1 and d.assessment_type_id=1 group by a.assessment_id having 1 ) ) as z  order by assessment_round asc";
                                        $res = $this->db->get_results($sql);
                                        $num_rows=count($res);
                                        //print_r($num_rows);
                                        return $num_rows;
                }
        /*************************Condition in case of multiple Round 1 and Round 2 Exists End**************************/
        
}