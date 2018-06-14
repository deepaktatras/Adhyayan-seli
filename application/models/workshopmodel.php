<?php
class WorkshopModel extends Model{
	
	
	function getWorkshops($args=array()){
		$args=$this->parse_arg($args,array("workshop_like"=>"",
                    "location_like"=>"",
                    "user_id"=>0,
                    'fdate_like' => '',
                    'edate_like' => '',
                    'programme_id' =>'',
                    "role_id"=>0,
                    "max_rows"=>10,
                    "page"=>1,
                    "order_by"=>"workshop_date_from",
                    "order_type"=>"asc"));
		$order_by=array("workshop_name"=>"w.workshop_name","workshop_date_from"=>"w.workshop_date_from","workshop_location"=>"w.workshop_location","programme_name"=>"pm.programme_name","user_role"=>"ur.workshop_sub_role_name");
		$sqlArgs=array();
		
		$sql="select SQL_CALC_FOUND_ROWS w.workshop_name,w.workshop_date_from,w.workshop_date_to,w.workshop_location,pm.programme_name,ur.workshop_sub_role_name
		from d_workshops w 
		left join h_workshops_user wu ON w.workshop_id = wu.workshop_id		
		left join d_workshops_role ur ON wu.workshops_user_role_id = ur.id
                left join d_programme_module pm on w.programme_id=pm.programme_id
		where 1=1 ";
		
		if($args['workshop_like']!=""){
			$sql.="and w.workshop_name like ? ";
			$sqlArgs[]="%".$args['workshop_like']."%";
		}
		
                
		if($args['location_like']!=""){
			$sql.="and ( w.workshop_location like ?)";
			$sqlArgs[]="%".$args['location_like']."%";
		}
                if($args['programme_id']>0){
			$sql.="and ( w.programme_id=?)";
			$sqlArgs[]="".$args['programme_id']."";
		}
                if($args['user_id']>0){
			$sql.="and wu.user_id=? ";
			$sqlArgs[]=$args['user_id'];
		}
                
                if($args['fdate_like']!='' && $args['edate_like']==''){
                    //$sql.="and w.workshop_date_from>=? ";
                    $sql.="and ( (w.workshop_date_from Between ? And ?) || (w.workshop_date_to Between ? And ?) )";
                    $sqlArgs[]="".$args['fdate_like']."";
                    $sqlArgs[]="".$args['fdate_like']."";
                    $sqlArgs[]="".$args['fdate_like']."";
                    $sqlArgs[]="".$args['fdate_like']."";
                } else if($args['fdate_like']=='' && $args['edate_like']!=''){
                    //$sql.="and w.workshop_date_from=? ";
                    $sql.="and ( (w.workshop_date_from Between ? And ?) || (w.workshop_date_to Between ? And ?) )";
                    $sqlArgs[]="".$args['edate_like']."";
                    $sqlArgs[]="".$args['edate_like']."";
                    $sqlArgs[]="".$args['edate_like']."";
                    $sqlArgs[]="".$args['edate_like']."";
                } else if($args['fdate_like']!='' && $args['edate_like']!=''){
                    $sql.="and ( (w.workshop_date_from Between ? And ?) || (w.workshop_date_to Between ? And ?) )";
                    $sqlArgs[]="".$args['fdate_like']."";
                    $sqlArgs[]="".$args['edate_like']."";
                    $sqlArgs[]="".$args['fdate_like']."";
                    $sqlArgs[]="".$args['edate_like']."";
                }
                
                if($args['role_id']>0){
			$sql.="and ur.id=? ";
			$sqlArgs[]=$args['role_id'];
		}
                
		$sql.=" order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"w.workshop_date_from").($args["order_type"]=="desc"?" desc ":" asc ").$this->limit_query($args['max_rows'],$args['page']);
		
		$res= $this->db->get_results($sql,$sqlArgs);
		$this->setPageCount($args['max_rows']);
		return $res;
	}
        
        function getAllWorkshops($args=array()){
		$args=$this->parse_arg($args,array("workshop_like"=>"",
                    "location_like"=>"",
                    'fdate_like' => '',
                    'edate_like' => '',
                    'workshop_led_like' => '',
                    'programme_id' =>'',
                    "max_rows"=>10,
                    "page"=>1,
                    "order_by"=>"workshop_date_from",
                    "order_type"=>"asc"));
		$order_by=array("workshop_name"=>"w.workshop_name","workshop_date_from"=>"w.workshop_date_from","workshop_location"=>"w.workshop_location","workshop_attende"=>"wu.tot_attende","workshop_actual"=>"wu.tot_attended_actual","workshop_led"=>"wu.lead_by","workshop_cofaciliated"=>"wu.co_faciliated_by","programme_name"=>"pm.programme_name");
		$sqlArgs=array();
		
		$sql="select SQL_CALC_FOUND_ROWS w.workshop_id,w.workshop_name,w.workshop_date_from,w.workshop_date_to,w.workshop_location,pm.programme_name,wu.tot_attended,wu.tot_attende,wu.tot_attended_actual,wu.lead_by,wu.co_faciliated_by
		from d_workshops w 
		left join (select workshop_id,count(id) as tot_attended, COUNT(CASE WHEN workshops_user_role_id = 3 THEN 1 END) AS tot_attende, COUNT(CASE WHEN workshops_user_role_id = 3 AND attendance_status ='P' THEN 1 END) AS tot_attended_actual ,GROUP_CONCAT(case when workshops_user_role_id=1 then dl.name end) as lead_by,GROUP_CONCAT(case when workshops_user_role_id=2 OR workshops_user_role_id=4 OR workshops_user_role_id=5  then dl.name end separator ', ') as co_faciliated_by  from (select x.*,u.name from h_workshops_user x inner join d_user u on x.user_id=u.user_id) dl group by workshop_id) wu ON w.workshop_id = wu.workshop_id
                left join d_programme_module pm on w.programme_id=pm.programme_id
		where 1=1 ";
		
		if($args['workshop_like']!=""){
			$sql.="and w.workshop_name like ? ";
			$sqlArgs[]="%".$args['workshop_like']."%";
		}
		
                
		if($args['location_like']!=""){
			$sql.="and ( w.workshop_location like ?)";
			$sqlArgs[]="%".$args['location_like']."%";
		}
                
                if($args['programme_id']>0){
			$sql.="and ( w.programme_id=?)";
			$sqlArgs[]="".$args['programme_id']."";
		}
                
                
                /*if($args['fdate_like']!='' && $args['edate_like']==''){
                    $sql.="and w.workshop_date_from=? ";
                    $sqlArgs[]="".$args['fdate_like']."";
                } else if($args['fdate_like']=='' && $args['edate_like']!=''){
                    $sql.="and w.workshop_date_from=? ";
                    $sqlArgs[]="".$args['edate_like']."";
                } else if($args['fdate_like']!='' && $args['edate_like']!=''){
                    $sql.="and ( w.workshop_date_from Between ? And ? )";
                    $sqlArgs[]="".$args['fdate_like']."";
                    $sqlArgs[]="".$args['edate_like']."";
                }*/
                
                if($args['fdate_like']!='' && $args['edate_like']==''){
                    //$sql.="and w.workshop_date_from>=? ";
                    $sql.="and ( (w.workshop_date_from Between ? And ?) || (w.workshop_date_to Between ? And ?) )";
                    $sqlArgs[]="".$args['fdate_like']."";
                    $sqlArgs[]="".$args['fdate_like']."";
                    $sqlArgs[]="".$args['fdate_like']."";
                    $sqlArgs[]="".$args['fdate_like']."";
                } else if($args['fdate_like']=='' && $args['edate_like']!=''){
                    //$sql.="and w.workshop_date_from=? ";
                    $sql.="and ( (w.workshop_date_from Between ? And ?) || (w.workshop_date_to Between ? And ?) )";
                    $sqlArgs[]="".$args['edate_like']."";
                    $sqlArgs[]="".$args['edate_like']."";
                    $sqlArgs[]="".$args['edate_like']."";
                    $sqlArgs[]="".$args['edate_like']."";
                } else if($args['fdate_like']!='' && $args['edate_like']!=''){
                    $sql.="and ( (w.workshop_date_from Between ? And ?) || (w.workshop_date_to Between ? And ?) )";
                    $sqlArgs[]="".$args['fdate_like']."";
                    $sqlArgs[]="".$args['edate_like']."";
                    $sqlArgs[]="".$args['fdate_like']."";
                    $sqlArgs[]="".$args['edate_like']."";
                }
                
                if($args['workshop_led_like']!=""){
			$sql.="and ( wu.lead_by like ?)";
			$sqlArgs[]="%".$args['workshop_led_like']."%";
		}
                
		$sql.=" order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"w.workshop_date_from").($args["order_type"]=="desc"?" desc ":" asc ").$this->limit_query($args['max_rows'],$args['page']);
		
		$res= $this->db->get_results($sql,$sqlArgs);
		$this->setPageCount($args['max_rows']);
		return $res;
	}
	
        function getworkshopById($workshop_id){


		$sql="select group_concat(f.document_id)as document,w.workshop_id,w.workshop_name,w.workshop_date_from,w.workshop_date_to,w.workshop_location,w.workshop_school,w.workshop_school_none,w.programme_id,prog_type_id,workshop_charges,w.workshop_payment_facilitator,w.workshop_description,group_concat(distinct concat(u2.client_id,'_',wu.user_id,'_',wu.workshops_user_role_id,'_',wu.leader_srno,'_',wu.payment_to_facilitator) order by wu.workshops_user_role_id)  as subroles

		from d_workshops w 
		left join  h_workshops_user wu ON w.workshop_id = wu.workshop_id
                left join  d_user u2 on u2.user_id=wu.user_id
                left join  h_workshop_document f on f.workshop_id=w.workshop_id
		where w.workshop_id=? && wu.workshops_user_role_id!='3';";
                
              $res=$this->db->get_row($sql,array($workshop_id));

	      return $res?$res:array();  
        }
        function getFilesDetails($workshop_id){
            
            
		$sql="select f.file_name,f.file_id,d.workshop_doc_cat_id FROM d_file f 
                    INNER JOIN h_workshop_document   d ON
                    d.document_id = f.file_id WHERE d.workshop_id=?";
                
               $res=$this->db->get_results($sql,array($workshop_id));
               
	       return $res?$this->db->array_grouping($res,"workshop_doc_cat_id"):array();  
        }
                
        function get_list_roles_allowed(){
                    
                    $res = $this->db->get_results("Select id,workshop_sub_role_name from d_workshops_role order by `workshop_sub_role_order` ");
                    return $res?$res:array();
        }   
        
        function getReviewerSubRolesFacilitator(){
                    //$res = $this->db->get_results("Select id,workshop_sub_role_name from d_workshops_role where id!=1 order by `workshop_sub_role_order`");
                    $res = $this->db->get_results("Select id,workshop_sub_role_name from d_workshops_role where 1=1 && workshop_sub_role_name!='Attendee' order by `workshop_sub_role_order`");
                    return $res?$res:array();
        }
        
        function get_list_programmes(){
                    
                    $res = $this->db->get_results("Select * from d_programme_module order by `programme_id` ");
                    return $res?$res:array();
        }
        
        function getDocumentCategory(){
            $res = $this->db->get_results("Select * from d_workshop_document_category order by `workshop_doc_cat_id` ");
            return $res?$res:array();
        }
        
        function getprogrammeType(){
            $res = $this->db->get_results("Select * from d_workshop_prog_type order by `order_id` ");
            return $res?$res:array();
        }
        
        // added by Vikas
        function createWorkshop($workshop_name, $workshop_location,$workshop_school , $workshop_school_none, $workshop_programme, $prog_type_id ,$workshop_charges, $workshop_payment_facilitator, $workshop_date_from, $workshop_date_to, $workshop_description, $led_array, $ext_team, $created_by=0,$workshop_upload = '',$workshop_upload_cat = '') {
            $wid= 0;
            $prog_type_id=empty($prog_type_id)?NULL:$prog_type_id;
            $workshop_charges=empty($workshop_charges)?NULL:$workshop_charges;
            //echo $this->db->insert("d_workshops", array('workshop_nam' => $workshop_name, 'workshop_location' =>$workshop_location , 'workshop_school' =>$workshop_school, 'workshop_school_none' =>$workshop_school_none  , 'programme_id' =>$workshop_programme , 'prog_type_id' =>$prog_type_id ,'workshop_charges' =>$workshop_charges,'workshop_payment_facilitator' =>$workshop_payment_facilitator  ,'workshop_date_from' => $workshop_date_from,'workshop_date_to' => $workshop_date_to,'workshop_description' =>$workshop_description,'create_date' => date("Y-m-d H:i:s"),'created_by'=>$created_by));
            if ($this->db->insert("d_workshops", array('workshop_name' => $workshop_name, 'workshop_location' =>$workshop_location , 'workshop_school' =>$workshop_school, 'workshop_school_none' =>$workshop_school_none  , 'programme_id' =>$workshop_programme , 'prog_type_id' =>$prog_type_id ,'workshop_charges' =>$workshop_charges,'workshop_payment_facilitator' =>$workshop_payment_facilitator  ,'workshop_date_from' => $workshop_date_from,'workshop_date_to' => $workshop_date_to,'workshop_description' =>$workshop_description,'create_date' => date("Y-m-d H:i:s"),'created_by'=>$created_by))) 
            {
                $wid = $this->db->get_last_insert_id();
                if(isset($workshop_upload) && !empty($workshop_upload)) {
                   $uploadDocSql = " INSERT INTO h_workshop_document(workshop_id,document_id,workshop_doc_cat_id) VALUES";
                   //$upload_document = implode(",",$workshop_upload);
                   foreach($workshop_upload as $keyf=>$data) {
                       $cat_file=$workshop_upload_cat[$keyf];
                       $uploadDocSql .= "(".$wid.",".$data .",".$cat_file.")".",";
                   }
                   $uploadDocSql = trim($uploadDocSql,",");
                   //echo $uploadDocSql;
                  // $this->db->delete('h_user_document',array('user_id'=>$user_id));
                   $this->db->query($uploadDocSql);
                }
            }
            
            if(!empty($led_array)){

        
                    if (!$this->db->insert("h_workshops_user", array("workshop_id" => $wid, "user_id" => $led_array['facilitator_id'],"leader_srno"=>1, "workshops_user_role_id" => $led_array['facilitator_role'], "payment_to_facilitator" => $led_array['facilitator_payment']))) {

                    return false;    
                    }
            }else{
             return false;   
            }
            $leader_srno=2;
             if (!empty($ext_team))
                    foreach ($ext_team as $member_user_id => $roleClient) {

                        $roleClient = explode('_', $roleClient);

                        $externalClientId = $roleClient[0];

                        $roleId = $roleClient[1];

                        $facilitator_payment = $roleClient[2];
                         $leader_srno=$roleId==1?$leader_srno:0;

                        if (!$this->db->insert("h_workshops_user", array("workshop_id" => $wid, "user_id" => $member_user_id, "leader_srno"=>$leader_srno, "workshops_user_role_id" => $roleId, "payment_to_facilitator" => $facilitator_payment))) {

                            return false;
                        }
                    }
           
            if($wid>0){
             return $wid;   
            }
            
           return false;
        }
       
        function editWorkshop($workshop_id,$workshop_name, $workshop_location,$workshop_school , $workshop_school_none, $workshop_programme, $prog_type_id ,$workshop_charges, $workshop_payment_facilitator, $workshop_date_from, $workshop_date_to , $workshop_description, $led_array, $ext_team, $modify_by=0, $attende_del=0,$workshop_upload=array(),$workshop_upload_cat=array()) {
           $wid=empty($workshop_id)?0:$workshop_id;
           $prog_type_id=empty($prog_type_id)?NULL:$prog_type_id;
           $workshop_charges=empty($workshop_charges)?NULL:$workshop_charges;
           if ($this->db->update("d_workshops", array('workshop_name' => $workshop_name, 'workshop_location' =>$workshop_location, 'workshop_school' =>$workshop_school, 'workshop_school_none' =>$workshop_school_none , 'programme_id' =>$workshop_programme, 'prog_type_id' =>$prog_type_id ,'workshop_charges' =>$workshop_charges ,'workshop_payment_facilitator' =>$workshop_payment_facilitator ,'workshop_date_from' => $workshop_date_from,'workshop_date_to' => $workshop_date_to,'workshop_description' =>$workshop_description,'last_modify_date' => date("Y-m-d H:i:s"),'last_modify_by'=>$modify_by),array('workshop_id'=>$workshop_id))) 
           {
                if(isset($workshop_upload) && !empty($workshop_upload)) {
                   $uploadDocSql = " INSERT INTO h_workshop_document(workshop_id,document_id,workshop_doc_cat_id) VALUES";
                   //$upload_document = implode(",",$workshop_upload);
                   foreach($workshop_upload as $keyf=>$data) {
                       $cat_file=$workshop_upload_cat[$keyf];
                       $uploadDocSql .= "(".$wid.",".$data .",".$cat_file.")".",";
                   }
                   $uploadDocSql = trim($uploadDocSql,",");
                   $this->db->delete('h_workshop_document',array('workshop_id'=>$workshop_id));
                   
                   $this->db->query($uploadDocSql);
                }else {
                     $this->db->delete('h_workshop_document',array('workshop_id'=>$workshop_id));
                }

            if($this->db->update("h_workshops_user", array("user_id"=>$led_array['facilitator_id'],"payment_to_facilitator"=>$led_array['facilitator_payment']), array("workshop_id"=>$workshop_id,"workshops_user_role_id"=>1,"leader_srno"=>1))){

                if($attende_del==1){
                $querydel=$this->db->query("DELETE from h_workshops_user where workshop_id=? && leader_srno!=1",array($workshop_id));    
                }else if($attende_del==0){
                $querydel=$this->db->query("DELETE from h_workshops_user where workshop_id=? && leader_srno!=1 && (workshops_user_role_id=? || workshops_user_role_id=? || workshops_user_role_id=? || workshops_user_role_id=? )",array($workshop_id,1,2,4,5));    
                }
                if($querydel){
                    
                    if (!empty($ext_team)){

                    $leader_srno=2;
                    foreach ($ext_team as $member_user_id => $roleClient) {

                        $roleClient = explode('_', $roleClient);

                        $externalClientId = $roleClient[0];

                        $roleId = $roleClient[1];
                        $facilitatorPayment = $roleClient[2];
                        $leader_srno=$roleId==1?$leader_srno:0;
                        $mode = isset($roleClient[3])?$roleClient[3]:'';
                        $status = isset($roleClient[4])?$roleClient[4]:'';
                        $leader_srno=$roleId==1?$leader_srno:0;

                        if (!$this->db->insert("h_workshops_user", array("workshop_id" => $wid, "user_id" => $member_user_id,"leader_srno"=>$leader_srno, "workshops_user_role_id" => $roleId,"mode_attendance" => $mode,"attendance_status" => $status,"payment_to_facilitator"=>$facilitatorPayment))) {

                            return false;
                        }
                    }    
                    return true;    
                    }else{
                    return true;
                    }
                    
                }
                
                
            }
               
           } 
        return false;
        }
	//Added by Vikas for workshop add
        function getAttendesbyWorkshopId($workshop_id){
        $sql="Select a.id,a.workshop_id,a.mode_attendance,a.attendance_status,b.name,b.email from (select * from h_workshops_user where workshop_id=? && workshops_user_role_id='3') a left join d_user b on a.user_id=b.user_id";    
        $res= $this->db->get_results($sql,array($workshop_id));
        return $res;
        }
        
        function getUniqueWorkshop($workshop_name,$workshop_date_from,$workshop_date_to,$workshop_location,$update_id=0) {
            $sql="select * from d_workshops where workshop_name=? && workshop_date_from=? && workshop_date_to=? && workshop_location=?";
            
            if($update_id>0){
             $sql.=" && workshop_id!=?";
             $res = $this->db->get_row($sql, array($workshop_name,$workshop_date_from,$workshop_date_to,$workshop_location,$update_id));
            }else{
            $res = $this->db->get_row($sql, array($workshop_name,$workshop_date_from,$workshop_date_to,$workshop_location));
            }
            
            if ($res) {

            $res['workshop_id'] = $res['workshop_id'];

            return $res;
        } else
            return null;
        }
        
        static function getFacilitatorTeamHTMLRow($sn){

		$clientModel=new clientModel();

		$clients = $clientModel->getClients(array("max_rows"=>-1));

		$workshopModel = new WorkshopModel();

		$workshopRoles = $workshopModel->getReviewerSubRolesFacilitator();
                //print_r($workshopRoles);

		$row = '<tr class="team_row">

					<td class="s_no">'.$sn.'</td>';

		$row .= '<td><select class="form-control team_facilitator_client_id" id="team_facilitator_client_id'.$sn.'" required name="externalReviewTeam[clientId][]">

												<option value=""> - Select School - </option>';	

		foreach($clients as $client)

			$row .= "<option value=\"".$client['client_id']."\">".$client['client_name'].($client['city']!=""?", ".$client['city']:'')."</option>\n";

		

		$row .= '</select></td>';

		

		$row .=	'<td><select class="form-control " name="externalReviewTeam[role][]" required>

						<option value=""> - Select Role - </option>						

						';	

		foreach($workshopRoles as $roles)													

			//$row .= $roles['id']=='1'?'':"<option value=\"".$roles['id']."\">".$roles['workshop_sub_role_name']."</option>";													

			//$row .= $roles['id']=='1'?'':"<option value=\"".$roles['id']."\">".$roles['workshop_sub_role_name']."</option>";													
			$row .= "<option value=\"".$roles['id']."\">".$roles['workshop_sub_role_name']."</option>";										

						

		$row .= '</select></td>';
                
                //$row .=	'<td> Co-Facilitator <input type="hidden" name="externalReviewTeam[role][]" value="2">';
                $row .= '</td>';

					$row .= '<td><select class="form-control team_facilitator_id" name="externalReviewTeam[member][]" id="team_facilitator_id'.$sn.'" required>

						<option value=""> - Select Member - </option>

						</select>

					</td><td><input type="text" class="form-control team_facilitator_id" name="externalReviewTeam[facilitator_payment][]" id="facilitator_payment'.$sn.'" >
					</td>

					<td><a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a></td>

				</tr>';			

			return $row;

	}
        

	//Added by Vikas for workshop add
        
        function getUsersbyClient($client_id){

		$res=$this->db->get_results("select u.user_id,u.name 

			from d_user u 

			inner join h_user_user_role ur on u.user_id=ur.user_id 

			inner join h_user_role_user_capability rc on rc.role_id=ur.role_id

			inner join d_user_capability c on rc.capability_id=c.capability_id

			where u.client_id=?  group by u.user_id order by u.name",array($client_id));

		return $res?$res:array();

	}
}