<?php

class userModel extends Model {

	public function a(){
		$a=$this->db->get_row("select * from d_client limit 1");		
	}
    public function authenticateUser($email, $password) {

        $sql = "SELECT u.*,GROUP_CONCAT(distinct ur.role_id SEPARATOR ',') as role_ids,GROUP_CONCAT(distinct c.slug SEPARATOR ',') as capabilities, cn.network_id, n.network_name

				FROM `d_user` u

                                left join h_user_user_role ur on u.user_id=ur.user_id

				left join `h_user_role_user_capability` rc on ur.role_id=rc.role_id

				left join d_user_capability c on c.capability_id=rc.capability_id

				left join h_client_network cn on cn.client_id=u.client_id

				left join d_network n on n.network_id=cn.network_id

				where email=? and password=? group by u.user_id";

        if ($res = $this->db->get_row($sql, array(trim($email), md5(trim($password))))) {

            $res['role_ids'] = $res['role_ids'] != "" ? explode(",", $res['role_ids']) : array();

            $res['capabilities'] = $res['capabilities'] != "" ? explode(",", $res['capabilities']) : array();

            return $res;
        } else
            return null;
    }

    public function getUserById($userId) {

        $sql = "SELECT u.*,GROUP_CONCAT(distinct ur.role_id SEPARATOR ',') as role_ids,GROUP_CONCAT(distinct c.slug SEPARATOR ',') as capabilities, cn.network_id, n.network_name,cl.client_name

				FROM `d_user` u

                                left join h_user_user_role ur on u.user_id=ur.user_id

				left join `h_user_role_user_capability` rc on ur.role_id=rc.role_id

				left join d_user_capability c on c.capability_id=rc.capability_id

				left join h_client_network cn on cn.client_id=u.client_id

				left join d_network n on n.network_id=cn.network_id
                                
                                left join d_client cl on u.client_id=cl.client_id

				where u.user_id=? group by u.user_id";

        if ($res = $this->db->get_row($sql, array($userId))) {

            $res['role_ids'] = $res['role_ids'] != "" ? explode(",", $res['role_ids']) : array();

            $res['capabilities'] = $res['capabilities'] != "" ? explode(",", $res['capabilities']) : array();

            return $res;
        } else
            return null;
    }

    public function getUsersForClientByRole($client_id, $role_id, $user_id = 0) {
        //  check tap admin is already exists or not on 12-05-2016 by Mohit Kumar
        if ($client_id != '') {
            $condition = 'c.client_id = ? and ';
            $array = array($client_id, $role_id, $user_id);
        } else {
            $condition = '';
            $array = array($role_id, $user_id);
        }
        $sql = "SELECT c.client_id,group_concat(u.user_id) as 'users',h.role_id
				FROM  d_client c 
				inner join d_user u on c.client_id=u.client_id
				inner join h_user_user_role h on h.user_id=u.user_id
				where " . $condition . "h.role_id=? and u.user_id!=? group by c.client_id;";


        if ($res = $this->db->get_row($sql, $array)) {
            return $res;
        } else
            return null;
    }

    function getUsernameForIds($userIds) {

        if (empty($userIds) || !is_array($userIds)) {

            return array();
        } else {

            $len = count($userIds);

            return $this->db->get_results("select name,user_id,email from d_user where user_id in (?" . str_repeat(",?", $len - 1) . ");", $userIds);
        }
    }

    public function getUserByEmail($email) {

        $sql = "SELECT u.*,GROUP_CONCAT(distinct ur.role_id SEPARATOR ',') as role_ids,GROUP_CONCAT(distinct c.slug SEPARATOR ',') as capabilities, cn.network_id, n.network_name

				FROM `d_user` u

                left join h_user_user_role ur on u.user_id=ur.user_id

				left join `h_user_role_user_capability` rc on ur.role_id=rc.role_id

				left join d_user_capability c on c.capability_id=rc.capability_id

				left join h_client_network cn on cn.client_id=u.client_id

				left join d_network n on n.network_id=cn.network_id

				where LOWER(email)=? group by u.user_id";

        if ($res = $this->db->get_row($sql, array(strtolower($email)))) {

            $res['role_ids'] = $res['role_ids'] != "" ? explode(",", $res['role_ids']) : array();

            $res['capabilities'] = $res['capabilities'] != "" ? explode(",", $res['capabilities']) : array();

            return $res;
        } else
            return null;
    }
    
    //Added by Vikas on 07-Apr-2017
    
    public function getUserByEmailExceptSelf($email,$id) {

        $sql = "SELECT u.*,GROUP_CONCAT(distinct ur.role_id SEPARATOR ',') as role_ids,GROUP_CONCAT(distinct c.slug SEPARATOR ',') as capabilities, cn.network_id, n.network_name

				FROM `d_user` u

                left join h_user_user_role ur on u.user_id=ur.user_id

				left join `h_user_role_user_capability` rc on ur.role_id=rc.role_id

				left join d_user_capability c on c.capability_id=rc.capability_id

				left join h_client_network cn on cn.client_id=u.client_id

				left join d_network n on n.network_id=cn.network_id

				where LOWER(email)=? && u.user_id!=? group by u.user_id";

        if ($res = $this->db->get_row($sql, array(strtolower($email),$id))) {

            $res['role_ids'] = $res['role_ids'] != "" ? explode(",", $res['role_ids']) : array();

            $res['capabilities'] = $res['capabilities'] != "" ? explode(",", $res['capabilities']) : array();

            return $res;
        } else
            return null;
    }
    
    //Added by Vikas on 07-Apr-2017
    

    function getUserByEmailWithGA($email, $gaid) {

        $sql = "SELECT u.*,if(ea.group_assessment_id is null,0,1) as inGivenGA,ua.value as doj

				FROM `d_user` u

				left join `h_group_assessment_external_assessor` ea on u.user_id=ea.user_id and ea.group_assessment_id=?

				left join h_user_teacher_attr ua on u.user_id=ua.user_id

				left join d_teacher_attribute ta on ta.attr_id=ua.attr_id and ta.attr_name='doj'

				where LOWER(u.email)=?";

        if ($res = $this->db->get_row($sql, array($gaid, strtolower($email)))) {

            return $res;
        } else
            return null;
    }

    function getUserByEmailWithTA($email, $gaid) {

        $sql = "SELECT u.*,if(at.group_assessment_id is null,0,1) as inGivenGA,ua.value as doj

				FROM `d_user` u

				left join `h_group_assessment_teacher` at on u.user_id=at.teacher_id and at.group_assessment_id=?

				left join h_user_teacher_attr ua on u.user_id=ua.user_id

				left join d_teacher_attribute ta on ta.attr_id=ua.attr_id and ta.attr_name='doj'

				where LOWER(u.email)=?";

        if ($res = $this->db->get_row($sql, array($gaid, strtolower($email)))) {

            return $res;
        } else
            return null;
    }

    public function getPrincipal($client_id) {

        $sql = "SELECT u.*

				FROM `d_user` u

                                left join h_user_user_role ur on u.user_id=ur.user_id

				where u.client_id=? and ur.role_id=6 group by u.user_id";

        if ($res = $this->db->get_row($sql, array($client_id))) {

            return $res;
        } else
            return null;
    }

    public function getUsers($args = array(), $tap_admin_role_id = '', $ref = '') {
        $args = $this->parse_arg($args, array("role_id" => 0, "name_like" => "", "client_id" => 0, "network_id" => 0, "exclude_cap" => array(), "client_like" => "", "email_like" => "", "max_rows" => 10, "page" => 1, "order_by" => "name", "order_type" => "asc"));
        $order_by = array("name" => "u.name", "client_name" => "c.client_name", "user_role" => "r.role_name", "email" => "u.email", "network" => "n.network_name", "create_date" => "u.create_date");
        $sqlArgs = array();
        $condition = '';
        
        if (isset($ref) && $ref == 1) {
            $SQL1 = "Select group_concat(distinct content_id) as user_id from d_alerts where status='1' and type='CREATE_EXTERNAL_ASSESSOR' and 
                            table_name='d_user'";
            $user_id = $this->db->get_row($SQL1);

            if (!empty($user_id) && $user_id['user_id'] != '') {
                $condition = " and u.user_id In (" . $user_id['user_id'] . ") ";
            }
        }
        
        $sql = "SELECT 
				SQL_CALC_FOUND_ROWS u.*,GROUP_CONCAT(distinct r.role_name SEPARATOR ',') as roles,GROUP_CONCAT(distinct ur.role_id SEPARATOR ',') as role_ids, c.client_name, cn.network_id, n.network_name
				,count(u.user_id) as count FROM `d_user` u
				left join h_user_user_role ur on u.user_id=ur.user_id
				left join d_user_role r on r.role_id=ur.role_id
				inner join d_client c on c.client_id=u.client_id
				left join h_client_network cn on cn.client_id=u.client_id
				left join d_network n on n.network_id=cn.network_id
				where 1=1  " . $condition;
        // get external assessor user list for tap admin by giving external user id (4) in query on 12-05-2016 by Mohit Kumar

        if ($args['name_like'] != "") {
            $sql.="and u.name like ? ";
            $sqlArgs[] = "%" . $args['name_like'] . "%";
        }
        if ($args['client_like'] != "") {
            $sql.="and c.client_name like ? ";
            $sqlArgs[] = "%" . $args['client_like'] . "%";
        }
        if ($args['email_like'] != "") {
            $sql.="and u.email like ? ";
            $sqlArgs[] = $args['email_like'] . "%";
        }
        if ($args['client_id'] > 0) {
            $sql.="and u.client_id = ? ";
            $sqlArgs[] = $args['client_id'];
        }
        if ($args['network_id'] > 0) {
            $sql.="and cn.network_id = ? ";
            $sqlArgs[] = $args['network_id'];
        }

        $sql.=" group by u.user_id ";
        $havingClauseAdded = 0;
        if ($tap_admin_role_id == 8) {
            $sql.=" having role_ids  rlike concat('[[:<:]]',4,'[[:>:]]') ";
            $havingClauseAdded = 1;
        } else {
            if ($args['role_id'] > 0) {
                $sql.=" having role_ids  rlike concat('[[:<:]]'," . $args['role_id'] . ",'[[:>:]]') ";
                $havingClauseAdded = 1;
            }
        }

        if (!empty($args['exclude_cap']) && is_array($args['exclude_cap'])) {
            $exclude = array();
            $roles = $this->getRolesWithCapSlugs();
            foreach ($roles as $role) {
                foreach ($args['exclude_cap'] as $cap)
                    if (in_array($cap, explode(",", $role['caps']))) {
                        $exclude[] = $role['role_id'];
                        break;
                    }
            }
            if (!empty($exclude)) {
                $sql.=$havingClauseAdded ? " and " : " having ";
                $cnt = 0;
                foreach ($exclude as $rid) {
                    $sql.=($cnt > 0 ? "and " : "") . "role_ids not rlike concat('[[:<:]]',$rid,'[[:>:]]') ";
                    $cnt++;
                }
            }
        }
        $sql.=" order by " . (isset($order_by[$args["order_by"]]) ? $order_by[$args["order_by"]] : "u.name") . ($args["order_type"] == "desc" ? " desc " : " asc ") . $this->limit_query($args['max_rows'], $args['page']);
        $res = $this->db->get_results($sql, $sqlArgs);
        $this->setPageCount($args['max_rows']);
        if (!empty($res) && $ref != '') {
            $this->db->update('d_alerts', array('status' => 1), array('type' => 'CREATE_EXTERNAL_ASSESSOR', 'table_name' => 'd_user'));
        }
        return $res;
    }

    function getRoles($roles = array()) {

        $sql = "select * from d_user_role";
        if(!empty($roles)) {
            $params = array();
            $sql .= " WHERE role_id IN (";
            foreach($roles as $key=>$val){
               $sql .= "?,";
               $params[] = $val;
            }
            $sql = trim($sql,",");
            $sql .= ") order by `order`;";
            $res = $this->db->get_results($sql,$params);
        }else {
            $sql .= " order by `order`;";
             $res = $this->db->get_results($sql);
        }

        return $res ? $res : array();
    }

    function getRolesWithCaps() {

        $res = $this->db->get_results("select r.*,GROUP_CONCAT(rc.capability_id SEPARATOR ',') as cap_ids

			from d_user_role r

			left join h_user_role_user_capability rc on rc.role_id=r.role_id

			group by r.role_id order by `order`;");

        return $res ? $res : array();
    }

    function getRolesWithCapSlugs() {

        $res = $this->db->get_results("select r.*,GROUP_CONCAT(c.slug SEPARATOR ',') as caps

			from d_user_role r

			left join h_user_role_user_capability rc on rc.role_id=r.role_id

            left join d_user_capability c on rc.capability_id=c.capability_id

			group by r.role_id order by `order`");

        return $res ? $res : array();
    }

    function getCapabilities() {

        $res = $this->db->get_results("select * from d_user_capability order by `order`;");

        return $res ? $res : array();
    }

    function addCapabilityToRole($role_id, $capability_id) {

        return $this->db->insert("h_user_role_user_capability", array("role_id" => $role_id, "capability_id" => $capability_id));
    }

    function removeCapabilityFromRole($role_id, $capability_id) {

        return $this->db->delete("h_user_role_user_capability", array("role_id" => $role_id, "capability_id" => $capability_id));
    }

    public function createUser($email, $password, $name, $client_id, $isAQSApproved = 0, $createDate=NULL, $createdBy=NULL,$add_moodle=0 ) {
         
        if ($this->db->insert("d_user", array('password' => md5(trim($password)), 'name' => $name, 'email' => $email, 'client_id' => $client_id, 'aqs_status_id' => $isAQSApproved, 'create_date' => $createDate, 'createdby' => $createdBy,'add_moodle'=>$add_moodle   )))
            return $this->db->get_last_insert_id();
        else
            return false;
    }

    public function addUserRole($user_id, $role_id) {

        return $this->db->insert('h_user_user_role', array("role_id" => $role_id, "user_id" => $user_id));
    }
    
    public function addUserRoleBatch($user_id, $role_ids) {

        $sql = "INSERT IGNORE INTO h_user_user_role(user_id,role_id) VALUES ";
        $values = '';
        foreach($role_ids as $id) {
            $values .= "($user_id,$id),";
        }
        $values = rtrim($values,",");
        $sql =  $sql.$values;
        return $this->db->query($sql);
    }
    public function createUserBatch($email, $password, $name, $client_id, $isAQSApproved = 0, $createDate=NULL, $createdBy=NULL) {

        $sql = "INSERT IGNORE INTO d_user (password,user_name,name,email,client_id,aqs_status_id,create_date,createdby) VALUES ('".md5(trim($password))."','".$email."','".$name."','".$email."',$client_id,$isAQSApproved,'".$createDate."',$createdBy)";
        //$this->db->query($sql);
        
         if($this->db->query($sql)) {
            return $this->db->get_last_insert_id();
         }else {
             return false;
         }
    }

    public function deleteUserRole($user_id, $role_id) {

        return $this->db->delete('h_user_user_role', array("role_id" => $role_id, "user_id" => $user_id));
    }

    function getUserRoles($user_id) {

        return $this->db->get_var("select GROUP_CONCAT(r.role_id SEPARATOR ',') as roles from h_user_user_role r where r.user_id=?;", array($user_id));
    }
    
    
    
    public function updateUser($user_id, $name ,$password = '', $client_id = 0, $isAQSApproved = -1, $modifyDate=NULL, $modifiedBy=NULL,$add_moodle=0) {

        $data = array("name" => $name);
        
        if ($password != "")
            $data['password'] = md5(trim($password));

        if ($client_id > 0)
            $data['client_id'] = $client_id;

        if ($isAQSApproved != -1)
            $data['aqs_status_id'] = $isAQSApproved;
        
        $data['modify_date']=$modifyDate;
        $data['modifyby']=$modifiedBy;
        $data['add_moodle']=$add_moodle;
        return $this->db->update('d_user', $data, array("user_id" => $user_id));
    }

    //Vikas on 07-Apr-2017
    public function updateUserEmail($user_id, $name , $email, $password = '', $client_id = 0, $isAQSApproved = -1, $modifyDate=NULL, $modifiedBy=NULL,$add_moodle=0) {

        $data = array("name" => $name);
        $data['email'] = $email;
        
        if ($password != "")
            $data['password'] = md5(trim($password));

        if ($client_id > 0)
            $data['client_id'] = $client_id;

        if ($isAQSApproved != -1)
            $data['aqs_status_id'] = $isAQSApproved;
        
        $data['modify_date']=$modifyDate;
        $data['modifyby']=$modifiedBy;
        $data['add_moodle']=$add_moodle;
        return $this->db->update('d_user', $data, array("user_id" => $user_id));
    }
    //Vikas on 07-Apr-2017
    
    public function generateToken($user_id = 0, $email = '') {

        $token = "";

        if ($user_id > 0) {

            $token = md5($user_id . "_" . time() . "_" . rand(9, 99999999));

            $date = date("Y-m-d H:i:s");
            //print_r($_SERVER);
            if (!$this->db->insert("session_token", array("token" => $token, "user_id" => $user_id, "created_date" => $date, "updated_date" => $date, "server_details"=>serialize($_SERVER)))) {
                $token = "";
            } else {
                $token .= "--" . md5(strtolower(trim($email)) );
            }
        }

        return $token;
    }

    public function logoutUser($token = "") {

        if ($token != "") {
            $token = current( explode("--", $token) );
            return $this->db->delete("session_token", array("token" => $token));
        }

        return false;
    }
    
    public function userTokenExists($user_id){
      $res = $this->db->get_row("select * from session_token where user_id=? order by updated_date desc limit 0,1",array($user_id));
      return $res ? $res : array();
    }
    
    public function checkToken($token = "") {
        
        $token = current( explode("--", $token) );
        
        $date = date("Y-m-d H:i:s", strtotime("-" . TOKEN_LIFE . " seconds"));
        if ($token != "" && $res = $this->db->get_row("select u.user_id,u.user_name,u.name,u.email,u.client_id,u.aqs_status_id,u.has_view_video,u.create_date,u.createdby,u.modify_date,u.modifyby,u.add_moodle ,cl.is_web,cl.client_institution_id,cl.is_guest,GROUP_CONCAT(distinct ur.role_id SEPARATOR ',') as role_ids,GROUP_CONCAT(distinct c.slug SEPARATOR ',') as capabilities, cn.network_id, n.network_name,hup.is_submit  as assessor_profile,
			(Select count(id) from d_alerts where status=0 and type='CREATE_EXTERNAL_ASSESSOR') as assessor_count,
                        (Select count(id) from d_alerts where status=0 and type='CREATE_REVIEW')as review_count from `session_token` s 
			inner join `d_user` u on s.user_id=u.user_id 
		    inner join d_client cl on cl.client_id = u.client_id
            left join h_user_user_role ur on u.user_id=ur.user_id
			left join `h_user_role_user_capability` rc on ur.role_id=rc.role_id
			left join d_user_capability c on c.capability_id=rc.capability_id
			left join h_client_network cn on cn.client_id=u.client_id				
			left join d_network n on n.network_id=cn.network_id
                        left join h_user_profile hup on hup.user_id=u.user_id
			where `token`= ? and updated_date > ? group by u.user_id", array($token, $date))) {
            $res['role_ids'] = $res['role_ids'] != "" ? explode(",", $res['role_ids']) : array();
            $res['capabilities'] = $res['capabilities'] != "" ? explode(",", $res['capabilities']) : array();
            $res['has_view_video'] = $res['has_view_video'] > 0 ? 1 : 0;
            $res['is_web'] = $res['is_web'] > 0 ? 1 : 0;
            $this->db->update("session_token", array("updated_date" => date("Y-m-d H:i:s"),"server_details"=>serialize($_SERVER)), array("token" => $token));
            $today = date("m/d/Y");
            $new_date= date("m/d/Y", strtotime("$today +1 week"));

            $ass_res = $this->db->get_row("SELECT count(d.assessment_id) as num_ass from d_assessment d "
                    . " inner join d_AQS_data aqs on d.aqsdata_id =  aqs.id WHERE"
                    . "  STR_TO_DATE(aqs.school_aqs_pref_start_date, '%d-%m-%Y') BETWEEN CURRENT_DATE() and DATE_ADD(CURRENT_DATE(), INTERVAL 7 DAY)  AND aqs.status is null");
            
             //print_r($res['role_ids']);
           
            if(in_array(1,$res['role_ids'])){
                $res["alert_count"] = $res['assessor_count'] + $res['review_count']+$ass_res['num_ass'];
                $res["ass_count"] = $ass_res['num_ass'];
            }else if(in_array(2,$res['role_ids'])){
                $res["alert_count"] = $ass_res['num_ass'];
                $res["ass_count"] = $ass_res['num_ass'];
            }else {
                $res["alert_count"] = $res['assessor_count'] + $res['review_count'];
            }
            
            return $res;
        } else {
            $date = date("Y-m-d H:i:s", strtotime("-" . (TOKEN_LIFE - 15) . " seconds"));
            $this->db->query("DELETE FROM  `session_token` where `updated_date` < ?", array($date));
        }
        return false;
    }
    
    
    
    public function checkTokenJWT($user_id) {
        
        //$token = current( explode("--", $token) );
        
        $date = date("Y-m-d H:i:s", strtotime("-" . TOKEN_LIFE . " seconds"));
        if ( $res = $this->db->get_row("select u.user_id,u.user_name,u.name,u.email,u.client_id,u.aqs_status_id,u.has_view_video,u.create_date,u.createdby,u.modify_date,u.modifyby,u.add_moodle,cl.is_web,cl.client_institution_id,cl.is_guest,GROUP_CONCAT(distinct ur.role_id SEPARATOR ',') as role_ids,GROUP_CONCAT(distinct c.slug SEPARATOR ',') as capabilities, cn.network_id, n.network_name,hup.is_submit  as assessor_profile,
			(Select count(id) from d_alerts where status=0 and type='CREATE_EXTERNAL_ASSESSOR') as assessor_count,
                        (Select count(id) from d_alerts where status=0 and type='CREATE_REVIEW')as review_count from 
			`d_user` u 
		        inner join d_client cl on cl.client_id = u.client_id
                        left join h_user_user_role ur on u.user_id=ur.user_id
			left join `h_user_role_user_capability` rc on ur.role_id=rc.role_id
			left join d_user_capability c on c.capability_id=rc.capability_id
			left join h_client_network cn on cn.client_id=u.client_id				
			left join d_network n on n.network_id=cn.network_id
                        left join h_user_profile hup on hup.user_id=u.user_id
			where u.user_id= ? group by u.user_id", array($user_id))) {
            $res['role_ids'] = $res['role_ids'] != "" ? explode(",", $res['role_ids']) : array();
            $res['capabilities'] = $res['capabilities'] != "" ? explode(",", $res['capabilities']) : array();
            $res['has_view_video'] = $res['has_view_video'] > 0 ? 1 : 0;
            $res['is_web'] = $res['is_web'] > 0 ? 1 : 0;
            $this->db->update("session_token", array("updated_date" => date("Y-m-d H:i:s"),"server_details"=>serialize($_SERVER)), array("user_id" => $user_id));
            $today = date("m/d/Y");
            $new_date= date("m/d/Y", strtotime("$today +1 week"));

            $ass_res = $this->db->get_row("SELECT count(d.assessment_id) as num_ass from d_assessment d "
                    . " inner join d_AQS_data aqs on d.aqsdata_id =  aqs.id WHERE"
                    . "  STR_TO_DATE(aqs.school_aqs_pref_start_date, '%d-%m-%Y') BETWEEN CURRENT_DATE() and DATE_ADD(CURRENT_DATE(), INTERVAL 7 DAY)  AND aqs.status is null");
            
             //print_r($res['role_ids']);
           
            if(in_array(1,$res['role_ids'])){
                $res["alert_count"] = $res['assessor_count'] + $res['review_count']+$ass_res['num_ass'];
                $res["ass_count"] = $ass_res['num_ass'];
            }else if(in_array(2,$res['role_ids'])){
                $res["alert_count"] = $ass_res['num_ass'];
                $res["ass_count"] = $ass_res['num_ass'];
            }else {
                $res["alert_count"] = $res['assessor_count'] + $res['review_count'];
            }
            
            return $res;
        } else {
            $date = date("Y-m-d H:i:s", strtotime("-" . (TOKEN_LIFE - 15) . " seconds"));
            $this->db->query("DELETE FROM  `session_token` where `updated_date` < ?", array($date));
        }
        return false;
    }

    public static function getExternalAssessorNodeHtml($eUser, $addDelete = 1, $value = null) {

        return '<div title="' . $eUser['email'] . '" class="eAssessorNode clearfix eAssessorNode-' . $eUser['user_id'] . '" data-id="' . $eUser['user_id'] . '"><span class="uname">' . $eUser['name'] . '</span><input type="hidden" class="ajaxFilterAttach" name="eAssessor[' . $eUser['user_id'] . ']" value="' . (empty($value) ? $eUser['user_id'] : $value) . '"/>' . ($addDelete ? '<span class="delete"><i class="fa fa-times"></i></span>' : '') . '</div>';
    }

    public function updateUserVideo($user_id, $view = 0) {

        return $this->db->update('d_user', array("has_view_video" => $view), array("user_id" => $user_id));
    }

    function getAdminEmail() {

        $sql = "Select u.user_name,u.email from d_user u inner join h_user_user_role hu on u.user_id=hu.user_id and (role_id=2 or role_id=1)";

        $res = $this->db->get_results($sql);

        return $res ? $res : array();
    }

    public function createResetUser($user_id, $key, $create_date, $expiration_date) {

        if ($this->db->insert("d_user_enc", array("user_id" => $user_id, "key" => $key, "create_date" => $create_date, "expiration_date" => $expiration_date)))
            return true;

        return false;
    }

    public function getPasswordResetKey($key) {

        $curDate = date("Y-m-d");

        $sql = "SELECT e.`user_id`,u.email,e.key FROM `d_user_enc` e inner join d_user u on e.user_id=u.user_id  WHERE `key` = ? AND `expiration_date` >= ?";

        $res = $this->db->get_row($sql, array($key, $curDate));

        return $res ? $res : array();
    }

    public function deleteResetUser($user_id, $key = 0) {

        if ($key == 0 && $this->db->delete("d_user_enc", array("user_id" => $user_id)))
            return true;

        else if ($this->db->delete("d_user_enc", array("user_id" => $user_id, "key" => $key)))
            return true;

        return false;
    }

    public function updateUserPassword($user_id, $password) {

        $data['password'] = md5(trim($password));

        return $this->db->update('d_user', $data, array("user_id" => $user_id));
    }

    // function for creating temp table for storing or getting assessor data
    public function getAQSTeamAssessorData($roles = array()) {
        if (!empty($roles) && current($roles) != '') {
            $having = array();
            foreach ($roles as $key => $value) {
                $having[] = " role_ids  rlike concat('[[:<:]]'," . $value . ",'[[:>:]]') ";
            }
            $having = implode(' OR ', $having);
        } else {
            $having = " role_ids  rlike concat('[[:<:]]',4,'[[:>:]]') ";
        }

        $SQL1 = "CREATE TEMPORARY TABLE userList
                Select SQL_CALC_FOUND_ROWS z.* from (
                SELECT t1.user_id,t1.name,t1.email,t1.client_id,t2.client_name,t3.network_id,t4.network_name,
                GROUP_CONCAT(distinct t6.role_name SEPARATOR ',') as roles ,(if (t1.email!='',1,2)) as count,
                GROUP_CONCAT(distinct t5.role_id SEPARATOR ',') as role_ids,
                (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='" . DB_NAME . "' and TABLE_NAME='d_user') as table_name
                FROM d_user t1 Left JOIN d_client t2 ON 
                (t1.client_id=t2.client_id) LEFT JOIN h_client_network t3 ON (t2.client_id=t3.client_id) LEFT JOIN d_network t4 ON 
                (t3.network_id=t4.network_id) LEFT JOIN h_user_user_role t5 ON (t1.user_id=t5.user_id) LEFT JOIN d_user_role t6 ON 
                (t5.role_id=t6.role_id) WHERE 1 GROUP by t1.user_id having " . $having . "
                UNION ALL
                SELECT a1.id as user_id,trim(a1.name) as name,a1.email,a2.client_id,a2.client_name,a3.network_id,a4.network_name,a1.designation as roles ,
                (select count(*) from d_AQS_team as tt where tt.email=a1.email) as count,a1.id as role_ids,
                (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='" . DB_NAME . "' and TABLE_NAME='d_AQS_team') as table_name
                FROM d_AQS_team a1 LEFT JOIN 
                d_assessment aa ON (a1.AQS_data_id=aa.aqsdata_id) LEFT JOIN d_client a2 ON (aa.client_id=a2.client_id) LEFT JOIN h_client_network a3 ON 
                (a2.client_id=a3.client_id) LEFT JOIN d_network a4 ON (a3.network_id=a4.network_id) WHERE 1 and ( a1.designation LIKE '%Principal%' OR
                a1.designation LIKE '%Teacher%' OR a1.designation LIKE '%Leader%' ) AND 
                a1.name != '' AND a1.designation != '' AND a1.email != '' AND a1.isInternal = 1 and a1.user_added_flag='0'
                ) as z;";

        $this->db->query($SQL1);
    }

    public function getAQSTeamAssessor($args = array(), $tap_admin_role, $ref = '', $ref_key = '') {

        $args = $this->parse_arg($args, array("role_id" => 0, "name_like" => "", "client_id" => 0, "network_id" => 0, "exclude_cap" => array(),
            "client_like" => "", "email_like" => "", "max_rows" => 10, "page" => 1, "order_by" => "name", "order_type" => "asc",
            'table_name' => ''));
        $order_by = array("name" => "name", "client_name" => "client_name", "email" => "email", "network" => "network_name", 'user_role' => 'roles');
        $sqlArgs = array();
        $condition = '';
        if (isset($ref) && $ref == 1 && $ref_key != '') {
            $SQL1 = "Select alert_ids as user_id from h_alert_relation where login_user_role='" . $tap_admin_role . "' and type='ASSESSOR'";
            $user_id = $this->db->get_row($SQL1);

            if (!empty($user_id) && $user_id['user_id'] != '') {
                $condition = " and user_id In (" . $user_id['user_id'] . ") and table_name='d_user' ";
            }
        }
        $SQL = "SELECT  SQL_CALC_FOUND_ROWS * from userList Where 1 " . $condition . "";

        if ($args['name_like'] != "") {
            $SQL.="and name like ? ";
            $sqlArgs[] = "%" . $args['name_like'] . "%";
        }
        if ($args['client_like'] != "") {
            $SQL.="and client_name like ? ";
            $sqlArgs[] = "%" . $args['client_like'] . "%";
        }
        if ($args['email_like'] != "") {
            $SQL.="and email like ? ";
            $sqlArgs[] = $args['email_like'] . "%";
            $SQL.="and table_name like ? ";
            $sqlArgs[] = "d_user";
        }
        if ($args['client_id'] > 0) {
            $SQL.="and client_id = ? ";
            $sqlArgs[] = $args['client_id'];
        }
        if ($args['network_id'] > 0) {
            $SQL.="and network_id = ? ";
            $sqlArgs[] = $args['network_id'];
        }
        if ($args['table_name'] != '') {
            $SQL.="and table_name = ? ";
            $sqlArgs[] = $args['table_name'];
        }

        $SQL.=" group by name,client_name ";

//            print_r($args);

        $SQL.=" order by " . (isset($order_by[$args["order_by"]]) ? $order_by[$args["order_by"]] : "name") . ($args["order_type"] == "desc" ? " desc " : " asc ") . $this->limit_query($args['max_rows'], $args['page']);
        $this->getAQSTeamAssessorData(array());

        $result = $this->db->get_results($SQL, $sqlArgs);
        $this->setPageCount($args['max_rows']);

        $final = array();
        foreach ($result as $key => $value) {
            if ($value['table_name'] == 'd_aqs_team' || $value['table_name'] == 'd_AQS_team') {
                $value['roles'] = '';
                $value['email'] = '';
                $final[] = $value;
            } else {
                $final[] = $value;
            }
        }

        return $final;
    }

    // function for getting user profile data on 17-05-2016 by Mohit Kumar
    public function getUserProfileData($user_id, $client_id = '') {

        $SQL = "Select t1.*,t2.name,t2.email,t2.add_moodle as moodle_user,t2.client_id,t2.user_id as userId,GROUP_CONCAT(h_doc.document_id SEPARATOR ',') as upload_document,t12.file_name as resume_name, GROUP_CONCAT(distinct t3.role_id SEPARATOR ',') as role_ids,
                GROUP_CONCAT(distinct t6.role_name SEPARATOR ',') as roles,t7.client_name,
                (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='" . DB_NAME . "' and TABLE_NAME='d_user') as table_name from h_user_profile t1 Left Join d_user t2 On (t1.user_id=t2.user_id)
                    Left Join d_file t12 On (t1.profile_resume=t12.file_id)
                    Left Join h_user_document h_doc On (t1.user_id=h_doc.user_id)
                Left Join h_user_user_role t3 On (t1.user_id=t3.user_id) 
				LEFT JOIN d_user_role t6 ON (t3.role_id=t6.role_id) Left Join d_client t7
                ON (t2.client_id=t7.client_id)
                Where t1.user_id='" . $user_id . "' and t2.client_id='" . $client_id . "' 
                group by t1.user_id";
        $userData = $this->db->get_row($SQL);
       // echo "<pre>";print_r($userData);
        if (!empty($userData) && $userData['upload_document'] != '') {
            $SQL1 = "Select file_name as file_name,file_id from d_file where file_id IN (" . $userData['upload_document'] . ")";
            $file_name = $this->db->get_results($SQL1);
            //print_r($file_name);
            if (!empty($file_name)) {
                $userData['file_name'] = $file_name;
            } else {
                $userData['file_name'] = '';
            }
        } else {
            $userData['file_name'] = '';
        }

        if (!empty($userData['id'])) {
            $result = $userData;
        } else {
            $roles = explode(',', $this->getUserRoles($user_id));
            $this->getAQSTeamAssessorData($roles);
            if ($client_id != '') {
                $condition = "and client_id='" . $client_id . "'";
            }
            $SQL = "Select * from userList where user_id='" . $user_id . "' " . $condition . " ";
            $result = $this->db->get_row($SQL);
            if ($result['table_name'] == 'd_aqs_team' || $result['table_name'] == 'd_AQS_team') {
                $result['roles'] = '';
                $result['email'] = '';
            }
            $result['file_name'] = '';
            $result['upload_document'] = '';
            $result['term_condition'] = 0;
            $result['medical_conditions'] = '';
            $result['other_medical_text'] = '';
            $result['hobbies'] = '';
            $result['other_hobbies_text'] = '';
            $result['is_submit'] = '';
        }
        return $result;
    }

    // function for getting add language row html on 26-05-2016 by Mohit Kumar
    static function getLanguageHTMLRow($sn) {
        $objDianosticModel = new diagnosticModel();
        $languageList = $objDianosticModel->getAllLanguages();
        $row = '<tr class="language_row">
                        <td class="s_no">' . $sn . '</td>';

        $row .= '<td>
                        <select class="form-control language" id="language_id' . $sn . '" name="languageData[language_id' . $sn . '][language_id]" onchange="addNewLanguage(' . "'language_id'" . ',' . $sn . ',this.value)">
                            <option value=""> - Select Language - </option>';

        foreach ($languageList as $language)
            $row .="<option value=\"" . $language['language_id'] . "\">" . $language['language_name'] . "</option>\n";

        $row .= ' <option value="other">Other</option>
                        </select>
                        <div id="other_language_div' . $sn . '" style="display: none;margin-top: 10px;">
                            <input type="text" name="other_language' . $sn . '" class="form-control other_language" id="other_language' . $sn . '" disabled placeholder="Enter new language" onblur="checkLanguageExist(this.value,' . $sn . ')"/>
                        </div>
                    </td>';
        $row .= '<td>
                        <div class="chkHldr">
                            <input type="checkbox" name="languageData[language_id' . $sn . '][language_speak]" class="" value="speaking" id="language_speak' . $sn . '">
                            <label class="chkF checkbox"><span>Speaking</span></label>
                        </div>
                    </td>
                    <td>
                        <div class="chkHldr">
                            <input type="checkbox" name="languageData[language_id' . $sn . '][language_read]" class="" value="reading" id="language_read' . $sn . '">
                            <label class="chkF checkbox"><span>Reading</span></label>
                        </div>
                    </td>
                    <td>
                        <div class="chkHldr">
                            <input type="checkbox" name="languageData[language_id' . $sn . '][language_write]" class="" value="writing" id="language_write' . $sn . '">
                            <label class="chkF checkbox"><span>Writing</span></label>
                        </div>
                    </td>
                    <td>
                        <a href="javascript:void(0)" class="delete_language_row"><i class="fa fa-times"></i>
                        </a>
                    </td>
            </tr>';
        return $row;
    }

    // function for getting the medical desease name
    public function getMedicalDesease($value = '', $field = '') {
        if ($field == 'id') {
            $where = " and desease_name ='" . $value . "' ";
        } else if ($field == 'desease_name') {
            $where = " and id ='" . $value . "' ";
        } else {
            $where = '';
        }
        $SQL = "Select id,desease_name from d_medical Where 1 " . $where;
        return $this->db->get_results($SQL);
    }

    // function for updateing user profile on 27-05-2016 by Mohit Kumar
    public function updateUserProfile($postData, $user_id, $uniqueID = '') {
        if (!empty($postData['contract_value'])) {
            $postData['contract_value'] = implode(',', $postData['contract_value']);
        }
        if (isset($postData['login_user_id'])) {
            unset($postData['login_user_id']);
        }
        if (isset($postData['client_id_old'])) {
            unset($postData['client_id_old']);
        }
        if (isset($postData['user_profile'])) {
            unset($postData['user_profile']);
        }
//            $postData['contract_value'] = !empty($postData['contract_value'])? implode(',',$postData['contract_value']):'';
        //echo "<pre>";print_r($postData);die;
        $postData['hobbies'] = !empty($postData['hobbies']) ? implode(',', $postData['hobbies']) : '';
        $postData['medical_conditions'] = !empty($postData['medical_conditions']) ? implode(',', $postData['medical_conditions']) : '';
        $postData['upload_document'] = !empty($postData['upload_document']) ? implode(",",$postData['upload_document']) : '';
        $postData['profile_resume'] = !empty($postData['profile_resume']) ? $postData['profile_resume'] : '';
        $postData['gender'] = !empty($postData['gender']) ? $postData['gender'] : '';
        $postData['date_of_birth'] = !empty($postData['date_of_birth']) ? $postData['date_of_birth'] : '0000-00-00';
        $postData['meal_preferences'] = !empty($postData['meal_preferences']) ? $postData['meal_preferences'] : '';
        $postData['user_id'] = $user_id;
        $postData['ifsc_code'] = !empty($postData['ifsc_code']) ? base64_encode($postData['ifsc_code']) : '';
        $postData['account_number'] = !empty($postData['account_number']) ? base64_encode($postData['account_number']) : '';
        $postData['experience_description'] = !empty($postData['experience_description']) ? ($postData['experience_description']) : '';
        if(isset($postData['files']) && count($postData['files'])) {
            $allFiles = implode(",", $postData['files']);
            if(isset($postData['upload_document']) && $postData['upload_document']!='') {
                $postData['upload_document'] = $postData['upload_document'].",".$allFiles;
            }else
                $postData['upload_document'] = $allFiles;
            
        }
        // echo "<pre>";print_r($postData);die;
         if(isset($postData['cell_number']) && isset($postData['cell_country_code'])) {
             
             $postData['cell_number'] = "(+".trim($postData['cell_country_code']).")".$postData['cell_number'];
         }
         if(isset($postData['whatsapp_num']) && isset($postData['wap_country_code'])) {
             
             $postData['whatsapp_num'] = "(+".trim($postData['wap_country_code']).")".$postData['whatsapp_num'];
         }
         if(isset($postData['school_contact_number']) && isset($postData['sc_country_code'])) {
             
             $postData['school_contact_number'] = "(+".trim($postData['sc_country_code']).")".$postData['school_contact_number'];
         }
         if(isset($postData['emergency_home_contact_no']) && isset($postData['ec_country_code'])) {
             
             $postData['emergency_home_contact_no'] = "(+".trim($postData['ec_country_code']).")".$postData['emergency_home_contact_no'];
         }
         if(isset($postData['emergency_cell_no']) && isset($postData['ecc_country_code'])) {
             
             $postData['emergency_cell_no'] = "(+".trim($postData['ecc_country_code']).")".$postData['emergency_cell_no'];
         }
         //echo $postData['cell_number'];
        unset($postData['id']);
        unset($postData['client_id']);
        unset($postData['email']);
        unset($postData['password']);
        unset($postData['confirm_password']);
        unset($postData['roles']);
        unset($postData['table']);
        unset($postData['process']);
        unset($postData['isAjaxRequest']);
        unset($postData['token']);
        unset($postData['first_name']);
        unset($postData['last_name']);
        unset($postData['languageData']);
        unset($postData['submit_value']);
        unset($postData['files']);
        unset($postData['cell_country_code']);
        unset($postData['wap_country_code']);
        unset($postData['sc_country_code']);
        unset($postData['ec_country_code']);
        unset($postData['ecc_country_code']);
         
        if(strlen(trim($postData['medical_conditions'])) >= 1) 
            $medicalConditionOptions = explode(",",$postData['medical_conditions']);
       // print_r($medicalConditionOptions);
        $medicalConditionData = array();
        if(isset($medicalConditionOptions) && count($medicalConditionOptions)>=1) {
            $medicalOptionSql = " INSERT INTO h_user_medical_condition(user_id,medical_condition_id) VALUES";
            foreach($medicalConditionOptions as $data) {
                $medicalOptionSql .= "(".$user_id.",".$data .")".",";
            }
            $medicalOptionSql = trim($medicalOptionSql,",");
            $this->db->delete('h_user_medical_condition',array('user_id'=>$user_id));
            $this->db->query($medicalOptionSql);
        }
        unset($postData['medical_conditions']);
        
        //code to save hobbies in referance table
       // print_r($postData['hobbies']);
        if(strlen(trim($postData['hobbies'])) >= 1) 
            $hobbiesOptions = explode(",",$postData['hobbies']);
       // print_r($medicalConditionOptions);
        //$hobbiesOptionsData = array();
        if(isset($hobbiesOptions) && count($hobbiesOptions)>=1) {
            $hobbiesSql = " INSERT INTO h_user_hobbies(user_id,hobby_id) VALUES";
            foreach($hobbiesOptions as $data) {
                $hobbiesSql .= "(".$user_id.",".$data .")".",";
            }
            $hobbiesSql = trim($hobbiesSql,",");
            $this->db->delete('h_user_hobbies',array('user_id'=>$user_id));
            $this->db->query($hobbiesSql);
        }
        unset($postData['hobbies']);
        
        if(isset($postData['upload_document']) && !empty($postData['upload_document'])) {
             $uploadDocSql = " INSERT INTO h_user_document(user_id,document_id) VALUES";
            $upload_document = explode(",",$postData['upload_document']);
            foreach($upload_document as $data) {
                $uploadDocSql .= "(".$user_id.",".$data .")".",";
            }
            $uploadDocSql = trim($uploadDocSql,",");
            //echo $uploadDocSql;
            $this->db->delete('h_user_document',array('user_id'=>$user_id));
            $this->db->query($uploadDocSql);
        }else  if(isset($postData['upload_document']) && empty($postData['upload_document'])){
             $this->db->delete('h_user_document',array('user_id'=>$user_id));
        }
        unset($postData['upload_document']);
        //  die;
        //print_r($medicalConditionData);
        $SQL = "Select id from h_user_profile where user_id='" . $user_id . "'";
        $checkData = $this->db->get_row($SQL);
        // echo "<pre>";  print_r($postData);
            //die;
        if (empty($checkData)) {
            $postData['creation_date'] = date('Y-m-d H:i:s');
            if ($this->db->insert('h_user_profile', $postData)) {
                if (OFFLINE_STATUS == TRUE) {
                    //start---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                    $action_profile_json = json_encode($postData);
                    $this->db->saveHistoryData($user_id, 'h_user_profile', $uniqueID, 'updateAssessorUserProfileUpdate', $user_id, $user_id, $action_profile_json, 0, date('Y-m-d H:i:s'));
                    //end---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                }
                return true;
            }
        } else {

//                unset($postData['contract_value']);
            if ($this->db->update('h_user_profile', $postData, array('user_id' => $user_id))) {
                if (OFFLINE_STATUS == TRUE) {
                    //start---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                    $action_profile_json = json_encode($postData);
                    $this->db->saveHistoryData($user_id, 'h_user_profile', $uniqueID, 'updateAssessorUserProfileUpdate', $user_id, $user_id, $action_profile_json, 0, date('Y-m-d H:i:s'));
                    //end---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                }
                return true;
            }
        }
    }
    
    //function to get user medical option
    public function getMedicalConditionOptions($userId) {
        $SQL = "SELECT medical_condition_id FROM h_user_medical_condition WHERE user_id='" . $userId . "'";
        $res = $this->db->get_results($SQL);
        if (!empty($res)) {
            return $res;
        } else {
            return array();
        }
        
    }
    //function to get user hobbies option
    public function getHobbiesAndIntrestOptions($userId) {
        $SQL = "SELECT hobby_id FROM h_user_hobbies WHERE user_id='" . $userId . "'";
        $res = $this->db->get_results($SQL);
        if (!empty($res)) {
            return $res;
        } else {
            return array();
        }
        
    }

    //function for getting for user id by email address
    public function getUserIdByEmail($email) {
        $SQL = "Select user_id from d_user where email='" . $email . "'";
        $user_id = $this->db->get_row($SQL);
        if (!empty($user_id)) {
            return $user_id['user_id'];
        } else {
            return false;
        }
    }

    // function for getting user language list
    public function getUserLanguage($user_id) {
        $SQL = "Select language_id,language_read,language_write,language_speak from h_user_language where user_id='" . $user_id . "'";
        return $this->db->get_results($SQL);
    }

    // function for getting invite user details
    public function getInviteUserData($id) {
        if ($this->db->query('Select 1 from d_AQS_team limit 1')) {
            $table_aqs_team = 'd_AQS_team';
        } else {
            $table_aqs_team = 'd_aqs_team';
        }
        if ($this->db->query('Select 1 from d_AQS_data limit 1')) {
            $table_aqs_data = 'd_AQS_data';
        } else {
            $table_aqs_data = 'd_aqs_data';
        }
        $SQL = "Select t1.email,t2.id as user_id,t2.name,t4.client_id,t5.client_name,(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE 
                TABLE_SCHEMA='" . DB_NAME . "' and TABLE_NAME='" . $table_aqs_team . "') as table_name From h_aqs_team_invite_user t1 
                Left Join " . $table_aqs_team . " t2 ON (t1.aqs_team_user_id=t2.id) Left Join " . $table_aqs_data . " t3 ON (t2.AQS_data_id=t3.id)
                Left Join d_assessment t4 ON (t3.id=t4.aqsdata_id) Left Join d_client t5 ON (t4.client_id=t5.client_id)
                Where t1.id='" . $id . "' ";
        $result = $this->db->get_row($SQL);
        $result['term_condition'] = 0;
        $result['role_ids'] = 4;
        $result['roles'] = 'External reviewer';
        return $result;
    }

    // function for checking mails have been sent or not on 22-06-2016 by Mohit Kumar
    public function getMailRecievedUserlist($id = NULL) {
        if ($id != '') {
            $field = 'email';
            $condition = "aqs_team_user_id='" . $id . "'";
        } else {
            $field = 'aqs_team_user_id';
            $condition = "user_type_table='d_aqs_team'";
        }
        $SQL = "Select " . $field . " from h_aqs_team_invite_user WHERE " . $condition . ";";

        if ($id != '') {
            $value = $this->db->get_row($SQL);
            $data = $value['email'];
        } else {
            $data = array();
            foreach ($this->db->get_results($SQL) as $key => $value) {
                $data[] = $value['aqs_team_user_id'];
            }
        }
        return $data;
    }

    // function for getting tap assessors users list on 29-06-2016 by Mohit Kumar
    public function getTapAssessorUserList() {
        $SQL = "Select user_id from h_tap_user_assessment where tap_program_status='1'";
        $result = $this->db->get_results($SQL);
        if (!empty($result)) {
            $final = array();
            foreach ($result as $value) {
                $final[] = $value['user_id'];
            }
            return $final;
        } else {
            return array();
        }
    }

    //function for getting user sub roles list on 30-06-2016 by Mohit Kumar
    public function getUserSubRoleList() {
        $SQL = "Select sub_role_id,sub_role_name from d_user_sub_role where user_role_id='4'";
        return $this->db->get_results($SQL);
    }

    // function for validating profile data on 30-06-2016 by Mohit Kumar
    public function userProfileValidation($post,$is_admin=0) {
        if (isset($_POST['process']) && $_POST['process'] != '') {
            $profileField = array(
                "term_condition" => array("type" => "int", "isRequired" => $is_admin == 1 ?0:1, "name" => "Accept Terms and Conditions"),
                "first_name" => array('type' => 'string', 'isRequired' => 1, 'name' => 'First Name'),
                "last_name" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Last Name'),
                "gender" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Gender'),
                "date_of_birth" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Date of Birth'),
                "email" => array('type' => 'email', 'isRequired' => 1, 'name' => 'Email')
            );
        } else {
            if (isset($_POST['submit_value']) && $_POST['submit_value'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }
            $hobbiesId = $this->getHobbiesList('Other', 'id');
            $hobbiesId = $hobbiesId[0]['id'];
            $medicalId = $this->getMedicalDesease('Other', 'id');
            $medicalId = $medicalId[0]['id'];

            $profileField = array(
                "term_condition" => array("type" => "int", "isRequired" => $is_admin == 1 ?0:1, "name" => "Accept Terms and Conditions"),
                "first_name" => array('type' => 'string', 'isRequired' => 1, 'name' => 'First Name'),
                "last_name" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Last Name'),
                "profile_resume" => array('type' => 'string', 'isRequired' => 0, 'name' => 'Upload Resume'),
                "travel_sickness" => array('type' => 'array', 'isRequired' => 1, 'name' => 'Travel Sickness'),
                "accomod_pref" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Accomodation Preference'),
                "gender" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Gender'),
                "date_of_birth" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Date of Birth'),
                "email" => array('type' => 'email', 'isRequired' => 1, 'name' => 'Email'),
                "designation" => array('type' => 'designation', 'isRequired' => 1, 'name' => 'Designation'),
                "work_experience" => array('type' => 'work_experience', 'isRequired' => 1, 'name' => 'Work Experience'),
                "address" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Address'),
                "town" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Town'),
                "state_id" => array('type' => 'int', 'isRequired' => 1, 'name' => 'State Name'),
                "pincode" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Pincode'),
                "cell_number" => array('type' => 'int', 'isRequired' => 1, 'name' => 'Cell number'),
                "whatsapp_num" => array('type' => 'int', 'isRequired' => 0, 'name' => 'WhatsApp Number'),
                "school_contact_number" => array('type' => 'int', 'isRequired' => 1, 'name' => 'School Contact Number'),
                "languageData" => array('type' => 'array', 'isRequired' => 1, 'name' => 'Language'),
                "bank_name" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Bank Name'),
                "ifsc_code" => array('type' => 'string', 'isRequired' => 1, 'name' => 'IFSC Code'),
                "branch_address" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Branch Address'),
                "account_name" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Account Name'),
                "pancard_name" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Pancard Name'),
                "account_number" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Account Number'),
                "pancard_number" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Pancard Number'),
                "personal_pan_number" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Personal Pancard Number'),
                "aadhar_number" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Aadhaar Card Number'),
                "upload_document" => array('type' => 'string', 'isRequired' => 0, 'name' => 'Upload Document'),
                "emergency_firstname" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Emergency First Name'),
                "emergency_lastname" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Emergency Last Name'),
                "emergency_email" => array('type' => 'email', 'isRequired' => 1, 'name' => 'Emergency Email'),
                "emergency_relationship" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Emergency Relationship'),
                "emergency_home_contact_no" => array('type' => 'int', 'isRequired' => 1, 'name' => 'Emergency Home Contact Number'),
                "emergency_cell_no" => array('type' => 'int', 'isRequired' => 1, 'name' => 'Emergency Cell Number'),
                "emergency_address" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Emergency Address'),
                "emergency_town" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Emergency Town'),
                "emergency_state_id" => array('type' => 'int', 'isRequired' => 1, 'name' => 'Emergency State Name'),
                "emergency_pincode" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Emergency Pincode'),
                "meal_preferences" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Meal Preferences'),
                "medical_conditions" => array('type' => 'array', 'isRequired' => 1, 'name' => 'Medical Conditions'),
                "travel_outstation" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Are you ready to travel outstation?'),
                "hobbies" => array('type' => 'array', 'isRequired' => 1, 'name' => 'Hobbies'),
                "education" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Education'),
                "workshop" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Workshop'),
                "assessor_experience" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Self Review experience'),
            	"experience_description" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Brief description of experience'),
                //"principal_statement" => array('type' => 'int', 'isRequired' => $val, 'name' => 'Mark the below statements as evidence or supposition'),
                //"leader_statement" => array('type' => 'int', 'isRequired' => $val, 'name' => 'Mark the below statements as evidence or supposition'),
               // "statement" => array('type' => 'int', 'isRequired' => $val, 'name' => 'Mark the below statements as evidence or supposition'),
                //"school_rating" => array('type' => 'int', 'isRequired' => $val, 'name' => 'Rate the school on KPA: Leadership & Management'),
               // "rating_text" => array('type' => 'string', 'isRequired' => $val, 'name' => 'You decide on this rating?'),
               //"aqs_improvement_text" => array('type' => 'string', 'isRequired' => $val, 'name' => 'The role of the Adhyayan Quality Standard in a school’s improvement journey?'),
                //"key_performance" => array('type' => 'int', 'isRequired' => $val, 'name' => 'Education'),
                //"activity" => array('type' => 'string', 'isRequired' => $val, 'name' => 'Inclusive activity in your school/organisation'),
                //"learn" => array('type' => 'int', 'isRequired' => $val, 'name' => 'Self Review experience'),
                //"goal" => array('type' => 'string', 'isRequired' => $val, 'name' => 'Professional development goal for the next 3 months')
            );
        }
        //print_r($post);die;
        $contactNoArray = array('cell_number','whatsapp_num','school_contact_number','emergency_home_contact_no','emergency_cell_no');
        $errors = array();
        $values = array();

        foreach ($profileField as $key => $value) {
            if ($key == 'principal_statement' || $key == 'leader_statement' || $key == 'statement' || $key == 'school_rating' || $key == 'rating_text' ||
                    $key == 'aqs_improvement_text' || $key == 'key_performance' || $key == 'activity' || $key == 'learn' || $key == 'goal'
            ) {
                $val = isset($post['assessment'][$key]) ? trim($post['assessment'][$key]) : "";
            } else {
                $val = isset($post[$key]) ? ($post[$key]) : "";
            }
             
             if($key == 'travel_sickness' && (isset($post['travel_sickness'])&& $post['travel_sickness'] == "yes")) {
                 if ( empty(trim($post['travel_sickness_text']))) {
                            $errors[$key] = "Field is required: 'Medical Sickness Textbox'";
                            break;
                 }
             }
              if($key == 'designation' && (isset($post['designation']) && empty(trim($post['designation'])))){
                   $errors[$key] = "Field is required: 'Designation'";
                           // break;
              }
              
              
            if ($value["isRequired"] && empty($val)) {
                $errors[$key] = "Field is required: '" . $value['name'] . "'";
            } else if ( in_array($key,$contactNoArray) && $value["type"] == 'int' && preg_match("/^[1-9][0-9]*$/", $post[$key]) != 1) {
                $errors[$key] = "Invalid value: '" . $value['name'] . "'";
            }else if ( in_array($key,$contactNoArray) && $value["type"] == 'int' && (strlen($post[$key]) < 3 || strlen($post[$key]) > 15) ) {
                $errors[$key] = "Invalid value: " . $value['name'] . " number must be between  3 and 15 in length";
            }else if (empty($val)) {
                $values[$key] = ($value["type"] == "int") ? 0 : '';
            }else if ($value["type"] == 'email' && preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $val) != 1) {
                $errors[$key] = "Invalid email: '" . $value['name'] . "'";
            }/*else if ($key == 'date_of_birth' && $this->validateDate($val,2)) {
                $errors[$key] = "Invalid value: '" . $value['name'] . "'";
            }*/ else if ($value["type"] == 'array' && !empty($val)) {
                if ($value['name'] == 'Language') {
                    foreach ($val as $k => $v) {
                        if ($v['language_id'] == '') {
                            $errors[$key] = "Field is required: 'Language'";
                            break;
                        } else if ($v['language_speak'] == '' && $v['language_read'] == '' && $v['language_write'] == '') {
                            $errors[$key] = "Field is required: 'Proficiency'";
                            break;
                        }
                    }
                } else if ($value['name'] == 'Upload Document') {
                    if (empty($val)) {
                        $errors[$key] = "Field is required: 'Upload Document'";
                        break;
                    } else {
                        if ( isset($post['upload_document']) &&  empty(trim($post['upload_document']))) {
                            $errors[$key] = "Field is required: 'Upload Document'";
                            break;
                        }
                    }
                }else if ($value['name'] == 'Designation') {
                    if (empty($val)) {
                        $errors[$key] = "Field is required: 'Designation'";
                        break;
                    } else {
                        if ( isset($post['designation']) &&  empty(trim($post['designation']))) {
                            $errors[$key] = "Field is required: 'Designation'";
                            break;
                        }
                    }
                } else if ($value['name'] == 'Medical Conditions') {
                    if (empty($val)) {
                        $errors[$key] = "Field is required: 'Medical Conditions'";
                        break;
                    } else {
                        if (in_array($medicalId, $val) && isset($post['other_medical_text']) && empty(trim($post['other_medical_text']))) {
                            $errors[$key] = "Field is required: 'Other Medical Textbox'";
                            break;
                        }
                    }
                }else if ($value['name'] == 'Hobbies') {
                    if (empty($val)) {
                        $errors[$key] = "Field is required: 'Hobbies'";
                        break;
                    } else {
                        if (in_array($hobbiesId, $val) && isset($post['other_hobbies_text']) && empty(trim($post['other_hobbies_text']))) {
                            $errors[$key] = "Field is required: 'Other Hobbies Textbox'";
                            break;
                        }
                    }
                }
            }
                            

        }

        if (isset($_POST['process']) && $_POST['process'] != '') {
            if (empty($_POST['password'])) {
                $errors['password'] = "Field is required: 'Password is empty'";
            }
            if (empty($_POST['confirm_password'])) {
                $errors['confirm_password'] = "Field is required: 'Confirm Password is empty'";
            }
            if ($_POST['password'] != $_POST['confirm_password']) {
                $errors['password'] = "Field is required: 'Password and confirm password should be same'";
            }
        }

        return array("errors" => $errors, "values" => $values);
    }

    public function validateDate($dbDate,$source){
        
         $DOB = explode("-",$dbDate);
         
        // var_dump(checkdate($DOB[1], $DOB[2], $DOB[0]));
        // print_r($DOB);
         $status='';
         if($source == 1){
          
             $status = strtotime($dbDate) > strtotime(date("d-m-Y"))?FALSE: checkdate($DOB[1], $DOB[0], $DOB[2]);             
         }
         else if($source == 2)
            $status = checkdate($DOB[1], $DOB[2], $DOB[0]);
         if($status === FALSE)
            return 1;
         else
             return 0;
    }

    // function for getting hobbies list
    public function getHobbiesList($value = '', $field = '') {
        if ($field == 'id') {
            $where = " and name ='" . $value . "' ";
        } else if ($field == 'name') {
            $where = " and id ='" . $value . "' ";
        } else {
            $where = '';
        }
        $SQL = "Select id,name from d_hobbies Where 1 " . $where;
        return $this->db->get_results($SQL);
    }

    // function for save assessor introductory assessment data on 21-07-2016 by Mohit Kumar
    public function saveAssessorIntroductoryAssessment($data, $uniqueID = '') {
        if ($data['user_id'] == '') {
            return false;
        } else {
            $SQL = "Select id from h_user_introductory_assessment where user_id='" . $data['user_id'] . "'";
            $id = $this->db->get_row($SQL);
            $data['principal_statement'] = isset($data['principal_statement'])?$data['principal_statement']:"";
            $data['leader_statement'] = isset($data['leader_statement'])?$data['leader_statement']:"";
            $data['statement'] = isset($data['statement'])?$data['statement']:"";
            $data['school_statement'] = $data['principal_statement'] . '~' . $data['leader_statement'] . '~' . $data['statement'];
            unset($data['principal_statement']);
            unset($data['leader_statement']);
            unset($data['statement']);
            if (empty($id)) {
                $data['create_date'] = date('Y-m-d H:i:s');
                if ($this->db->insert('h_user_introductory_assessment', $data)) {
                    if (OFFLINE_STATUS == TRUE) {
                        //start---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                        $action_introductory_assessment_json = json_encode($data);
                        $this->db->saveHistoryData($data['user_id'], 'h_user_introductory_assessment', $uniqueID, 'updateAssessorIntroductoryAssessment', $data['user_id'], $data['user_id'], $action_introductory_assessment_json, 0, date('Y-m-d H:i:s'));
                        //end---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                    }
                    return true;
                } else {
                    return false;
                }
            } else {
                $where = array('user_id' => $data['user_id']);
                if ($this->db->update('h_user_introductory_assessment', $data, $where)) {
                    if (OFFLINE_STATUS == TRUE) {
                        //start---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                        $action_introductory_assessment_json = json_encode($data);
                        $this->db->saveHistoryData($data['user_id'], 'h_user_introductory_assessment', $uniqueID, 'updateAssessorIntroductoryAssessment', $data['user_id'], $data['user_id'], $action_introductory_assessment_json, 0, date('Y-m-d H:i:s'));
                        //end---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                    }
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
    // function for save assessor introductory assessment data on 21-07-2016 by Mohit Kumar
    public function saveIntroductoryAssessment($data, $user_id,$is_submit=0) {
        if ($user_id == '') {
            return false;
        } else {
           // $finalDataArray['rating_text'] = isset($data['rating_text'])?trim($data['rating_text']):''; 
            $finalDataArray['leader_statement'] = isset($data['leader_statement'])?trim($data['leader_statement']):''; 
            $finalDataArray['statement'] = isset($data['statement'])?trim($data['statement']):''; 
           // $finalDataArray['classroom_observation'] = isset($data['classroom_observation'])?trim($data['classroom_observation']):''; 
            //$finalDataArray['stakeholder_text'] = isset($data['stakeholder_text'])?trim($data['stakeholder_text']):''; 
            $finalDataArray['key_performance_text'] = isset($data['key_performance_text'])?trim($data['key_performance_text']):''; 
            $finalDataArray['goal'] = isset($data['goal'])?trim($data['goal']):''; 
            $finalDataArray['school_rating'] = isset($data['school_rating'])?trim($data['school_rating']):''; 
            $finalDataArray['school_rating_txt'] = isset($data['school_rating_txt'])?trim($data['school_rating_txt']):''; 
            $finalDataArray['score'] = isset($data['score'])?trim($data['score']):''; 
            if($is_submit == 1) {
                $finalDataArray['is_submit'] = 1;
            }
            //$finalDataArray['is_submit'] = 1; 
            $finalDataArray = array_filter($finalDataArray);
               //echo "<pre>";print_r($data);
            $SQL = "Select id from h_user_introductory_assessment where user_id='" . $user_id . "'";
             $id = $this->db->get_row($SQL);
            //print_r($id);
            
            //if((isset($data['key_behaviour']['answer_id']) && count($data['key_behaviour']['answer_id'])>=1) || (isset($data['key_performance']['answer_id'])) && count($data['key_performance']['answer_id'])>=1) {
                 $sql = "INSERT INTO h_assessor_question_answer (question_id,option_id,user_id) VALUES ";
            //}
                 $sqlKey = $sqlPer = $sqlClass = $sqlStake = $sqlRate='';
            if(isset($data['key_behaviour'])) {
                foreach($data['key_behaviour']['answer_id'] as $assData) {
                    
                    $sqlKey .= "(".$data['key_behaviour']['question_id'].",".$assData.",$user_id),";
                }
                
            }
            if(isset($data['key_performance'])) {
                foreach($data['key_performance']['answer_id'] as $assData) {
                    
                     $sqlPer .= "(".$data['key_performance']['question_id'].",".$assData.",$user_id),";
                }
                
            }
            if(isset($data['classroom_observation'])) {
                foreach($data['classroom_observation']['answer_id'] as $assData) {
                    
                    $sqlClass .= "(".$data['classroom_observation']['question_id'].",".$assData.",$user_id),";
                }
                
            }if(isset($data['stakeholder_text'])) {
                foreach($data['stakeholder_text']['answer_id'] as $assData) {
                    
                    $sqlStake .= "(".$data['stakeholder_text']['question_id'].",".$assData.",$user_id),";
                }
                
            }
            if(isset($data['rating_text'])) {
                foreach($data['rating_text']['answer_id'] as $assData) {
                    
                    $sqlRate .= "(".$data['rating_text']['question_id'].",".$assData.",$user_id),";
                }
                
            }
            
            if($is_submit == 1) {
                if($sqlKey && $sqlPer && $sqlClass && $sqlStake && $sqlRate) 
                    $sql .= $sqlKey.$sqlPer.$sqlClass.$sqlStake.$sqlRate;
            }else {
                    if($sqlKey || $sqlPer || $sqlClass || $sqlStake || $sqlRate) 
                    $sql .= $sqlKey.$sqlPer.$sqlClass.$sqlStake.$sqlRate;
            }
            
                if(isset($sql)) {
                    $sql = trim($sql,",");
                    $deleteQuery = "DELETE FROM h_assessor_question_answer WHERE user_id = '$user_id'";
                    $this->db->query($deleteQuery) ;
                    $this->db->query($sql) ;
                }
            
           // echo $sql;
           // echo"<pre>"; print_r($data);
           // $finalDataArray['rating_text'] = $data['rating_text']; 
            /*$SQL = "Select id from h_user_introductory_assessment where user_id='" . $data['user_id'] . "'";
            $id = $this->db->get_row($SQL);
            $data['principal_statement'] = isset($data['principal_statement'])?$data['principal_statement']:"";
            $data['leader_statement'] = isset($data['leader_statement'])?$data['leader_statement']:"";
            $data['statement'] = isset($data['statement'])?$data['statement']:"";
            $data['school_statement'] = $data['principal_statement'] . '~' . $data['leader_statement'] . '~' . $data['statement'];
            unset($data['principal_statement']);
            unset($data['leader_statement']);
            unset($data['statement']);*/
            if (empty($id)) {
                $data['create_date'] = date('Y-m-d H:i:s');
                $finalDataArray['user_id'] = $user_id;
                
                if ($this->db->insert('h_user_introductory_assessment', $finalDataArray)) {
                    if (OFFLINE_STATUS == TRUE) {
                        //start---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                        $action_introductory_assessment_json = json_encode($data);
                        $this->db->saveHistoryData($data['user_id'], 'h_user_introductory_assessment', $uniqueID, 'updateAssessorIntroductoryAssessment', $data['user_id'], $data['user_id'], $action_introductory_assessment_json, 0, date('Y-m-d H:i:s'));
                        //end---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                    }
                    return true;
                } else {
                    return false;
                }
            } else {
                $where = array('user_id' => $user_id);
                if ($this->db->update('h_user_introductory_assessment', $finalDataArray, $where)) {
                    if (OFFLINE_STATUS == TRUE) {
                        //start---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                        $action_introductory_assessment_json = json_encode($data);
                        $this->db->saveHistoryData($data['user_id'], 'h_user_introductory_assessment', $uniqueID, 'updateAssessorIntroductoryAssessment', $data['user_id'], $data['user_id'], $action_introductory_assessment_json, 0, date('Y-m-d H:i:s'));
                        //end---> call function for add other langauge on 10-08-2016 by Mohit Kumar
                    }
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
    
    //Fuction by Vikas
    function getReviewsAssessmentCount($user_id) {
        $query="Select a.* from d_assessment a 
                left join d_diagnostic d on a.diagnostic_id=d.diagnostic_id
                left join h_assessment_user au on a.assessment_id=au.assessment_id where d.assessment_type_id=1 && au.user_id='".$user_id."' && au.role='3'
                ";
        
        return $this->db->get_results($query);
         
    }
    //Fuction to get answers of introductory questions
    function getAssessorIntroductoryQuestionsAnswer($user_id) {
        $query="SELECT * FROM h_user_introductory_assessment where user_id = '$user_id'";
        
        return $this->db->get_row($query);
         
    }
    //Fuction to get answers of introductory questions multiple  answer
    function getAssessorIntroductoryOption($user_id) {
        $query="SELECT * FROM h_assessor_question_answer where user_id = '$user_id'";
        
        return $this->db->get_results($query);
         
    }
    
    function transfer_reviews($from_user_id,$to_user_id){
        
       $assessments=$this->getReviewsAssessmentCount($from_user_id);
       //print_r($assessments);
       $success=true; 
       foreach($assessments as $key=>$val){
       //print_r($val);
          
       $data=array();
       $data['user_id']=$to_user_id;
       if(!$this->db->update('h_assessment_user', $data, array("assessment_id" => $val['assessment_id'],"user_id" => $from_user_id,"role"=>3))){
       return false;    
       }
           
       $data1=array();
       $data1['assessor_id']=$to_user_id;
       if(!$this->db->update('f_score', $data1, array("assessment_id" => $val['assessment_id'],"assessor_id" => $from_user_id))){
       return false;    
       }
       
       $data2=array();
       $data2['assessor_id']=$to_user_id;
       if(!$this->db->update('h_cq_score', $data2, array("assessment_id" => $val['assessment_id'],"assessor_id" => $from_user_id))){
       return false;    
       }
       
       $data3=array();
       $data3['assessor_id']=$to_user_id;
       if(!$this->db->update('h_kq_instance_score', $data3, array("assessment_id" => $val['assessment_id'],"assessor_id" => $from_user_id))){
       return false;    
       }
       
       $data4=array();
       $data4['assessor_id']=$to_user_id;
       if(!$this->db->update('h_kpa_instance_score', $data4, array("assessment_id" => $val['assessment_id'],"assessor_id" => $from_user_id))){
       return false;    
       }
           
       }
       
      return $success;
    }
    
    function createrandomuser($client_id,$user_id_login,$user_id=0,$createdBy=0){
        $client_info=$this->db->get_row("select client_name from d_client where client_id='".$client_id."' limit 1");
        $principal_user_row=$this->getPrincipal($client_id);
        $principal_user_id=empty ( $principal_user_row ) ? 0:$principal_user_row['user_id'];
        if($principal_user_id===null || $principal_user_id==$user_id){
        $schoolname="Auto Principal";
        if(!empty($client_info['client_name'])) $schoolname.="-".$client_info['client_name']."";
        
        if($this->db->insert('d_user',array('user_name'=>'auto_user_'.time().'','password'=>md5('#1234#'),'name'=>''.$schoolname.'','email'=>'auto_user_'.time().'@autogenerated.com','client_id'=>$client_id,'create_date'=>date("Y-m-d H:i:s"),'createdby'=>$createdBy))){
        
        $user_id = $this->db->get_last_insert_id ();
        if($this->db->insert('h_user_user_role',array('user_id'=>$user_id,'role_id'=>6)) && $this->db->insert('h_user_user_role',array('user_id'=>$user_id,'role_id'=>3))){
            if($this->add_user_history($user_id,0,$client_id,0,'6,3','Auto User Generated',$user_id_login,date("Y-m-d H:i:s"))){ 
            return $user_id;
            }
        }
        
        }
        }else{
           return $principal_user_id;    
        }
        
        return false;
    }
    
    function add_user_history($user_id,$client_id_from,$client_id_to,$users_roles_from,$users_roles_to,$user_action,$created_by,$action_date){
    $data=array();
    $data['user_id']=$user_id;
    $data['client_id_from']=$client_id_from;
    $data['client_id_to']=$client_id_to;
    $data['users_roles_from']=$users_roles_from;
    $data['users_roles_to']=$users_roles_to;
    $data['user_action']=$user_action;
    $data['created_by']=$created_by;
    $data['action_date']=$action_date;
    if($this->db->insert('z_history_users',$data)){
    return true;    
    }
    return false;
    }
    
    //Function by Vikas
    
    // function for getting assessor introductory assessment data on 22-07-2016 by Mohit Kumar
    function getAssessorIntroductoryAssessment($user_id) {
        $SQL = "Select school_rating_txt,score,school_statement,school_rating,is_submit,rating_text,key_performance,activity,learn,goal,aqs_improvement_text
                    from h_user_introductory_assessment where user_id='" . $user_id . "'";
        $data = $this->db->get_row($SQL);
        if (!empty($data)) {
            if ($data['school_statement'] != '') {
                $school_statement = explode('~', $data['school_statement']);
                unset($data['school_statement']);
                $data['principal_statement'] = $school_statement[0];
                $data['leader_statement'] = $school_statement[1];
                $data['statement'] = $school_statement[2];
            } else {
                unset($data['school_statement']);
                $data['principal_statement'] = '';
                $data['leader_statement'] = '';
                $data['statement'] = '';
            }
            return $data;
        } else {
            return array();
        }
    }

    /*
     * @Purpose: Add user role data
     * @Method: addNewUserRole
     * @Parameters: Table Name, data values
     * @Return: last insert id or False
     * @Date: 03-03-2016 
     * @By: Mohit Kumar
     */

    public function addNewUserRole($user_id, $role_id) {
        if ($this->db->insert('h_user_user_role', array("role_id" => $role_id, "user_id" => $user_id)))
            return $this->db->get_last_insert_id();
        else
            return false;
    }

    /**
     * 
     * @param type $state_name
     * @return type
     */
    function getStateIdByName($state_name) {
        $res = $this->db->get_row("SELECT state_id FROM d_states where state_name= ? ;", array($state_name));
        return $res ? $res['state_id'] : '';
    }

    /**
     * 
     * @param type $role_name
     * @return type
     */
    function getRoleIdByName($role_name) {
        $res = $this->db->get_row("select role_id from d_user_role where role_name = ? ;", array($role_name));
        return $res ? $res['role_id'] : '';
    }
    
    function getMedConIdByName($med_con_name) {
        $res = $this->db->get_row("select id from d_medical where desease_name = ? ;", array($med_con_name));
        return $res ? $res['id'] : '';
    }
    
    function getHobbieIdByName($hobbie_name) {
        $res = $this->db->get_row("select id from d_hobbies where name = ? ;", array($hobbie_name));
        return $res ? $res['id'] : '';
    }
    
    function getlangIdByName($language_name) {
        $res = $this->db->get_row("select language_id from d_language where language_name = ? ;", array($language_name));
        return $res ? $res['language_id'] : '';
    }
    
    public function addUserLanguage($lang_data) {
        return $this->db->insert('h_user_language', $lang_data);
    }
    
    function checkLangExistForUser($lang_id, $user_id){
        $res = $this->db->get_row("select id from h_user_language where language_id = ? AND user_id = ? ;", array($lang_id, $user_id));
        return $res ? $res['id'] : '';
    }
    
    function updateUserLanguage($lang_data, $lang_inc_id){
        $this->db->update('h_user_language', $lang_data, array('id' => $lang_inc_id));
    }
    
    function getUserProfileRowByUserId($user_id){
         $res = $this->db->get_row("select * from h_user_profile where user_id = ? ;", array($user_id));
        return $res ? $res : array();
    }
        
    public function addUserProfile($user_profile_data) {
        return $this->db->insert('h_user_profile', $user_profile_data);
    }
    
    public function insertUserProfileErrorLog($user_profile_error_data){
        return $this->db->insert('z_user_profile_excel_error_log', $user_profile_error_data);
    }
    
    //function to get user workshop data
    public function getWorkshopList(){
        
        $query="SELECT w.workshop_name,w.workshop_id,u.sub_user_type_role,s.sub_role_name,count(u.sub_user_type_role) as num_workshop_attended "
                . " FROM `d_workshop_facilitator` w INNER JOIN h_workshop_facilitator_user u on u.workshop_id = w.workshop_id "
                . " INNER JOIN d_user_sub_role s on s.sub_role_id = u.sub_user_type_role WHERE u.user_id=47 GROUP by u.sub_user_type_role ";
        
        return $this->db->get_results($query);
    }
    //function to sync user profile data to sub tables
    public function syncData(){
        
        $query="SELECT user_id,medical_conditions,hobbies,upload_document FROM h_user_profile ";
        
        $res =  $this->db->get_results($query);
        $medicalSql = "INSERT INTO h_user_medical_condition (user_id,medical_condition_id) VALUES";
        $hobbiesSql = "INSERT INTO h_user_hobbies (user_id,hobby_id) VALUES";
        $docSql = "INSERT INTO h_user_document (user_id,document_id) VALUES";
        foreach($res as $data) {
            
            if(isset($data['medical_conditions']) && $data['medical_conditions']!='') {
                
                $medicalData = explode(",", $data['medical_conditions']);
                foreach($medicalData as $medical) {
                    $medicalSql .= "(".$data['user_id'].",".$medical."),";
                }
                //echo "<pre>";  print_r($medicalData);
            }
            if(isset($data['hobbies']) && $data['hobbies']!='') {
                
                $hobbieData = explode("~", $data['hobbies']);
                foreach($hobbieData as $hobbies) {
                    $hobbiesSql .= "(".$data['user_id'].",".$hobbies."),";
                }
                //echo "<pre>";  print_r($medicalData);
            }
            if(isset($data['upload_document']) && $data['upload_document']!='') {
                
                $docData = explode(",", $data['upload_document']);
                foreach($docData as $doc) {
                    $docSql .= "(".$data['user_id'].",".$doc."),";
                }
                //echo "<pre>";  print_r($medicalData);
            }
            
        }
        $medicalSql = trim($medicalSql,",");
        echo $hobbiesSql = trim($hobbiesSql,",");
        //$docSql = trim($docSql,",");
        //$this->db->query($medicalSql);
        $this->db->query($hobbiesSql);
        //$this->db->query($docSql);
        //echo $docSql;
        //echo "<pre>";print_r($res);die;
    }
    
     public function validateIntroAss($post) {
         
         $val = 1;
        
            $profileField = array(
                //"leader_statement" => array("type" => "string", "isRequired" => 1, "name" => "Accept Terms and Conditions"),
                //"statement" => array('type' => 'string', 'isRequired' => 1, 'name' => 'First Name'),
                //"classroom_observation" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Last Name'),
                //"rating_text" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Upload Resume'),
                //"stakeholder_text" => array('type' => 'array', 'isRequired' => 1, 'name' => 'Travel Sickness'),
                //"key_performance_text" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Accomodation Preference'),
               // "goal" => array('type' => 'string', 'isRequired' => 1, 'name' => 'Gender'),
                //"school_rating" => array('type' => 'string', 'isReg', 'isRequired' => 1, 'name' => 'Brief description of experience'),
                //"principal_statement" => array('type' => 'int', 'isRequired' => $val, 'name' => 'Mark the below statements as evidence or supposition'),
                "leader_statement" => array('type' => 'string', 'isRequired' => $val, 'name' => 'The school leadership team works well together'),
                "statement" => array('type' => 'string', 'isRequired' => $val, 'name' => 'A school leader said that she meets her Principal every Friday to discuss reflections on what has gone well in the past week, accomplishments, areas for improvement and the plan for the coming week. There is a weekly minutes book where this information is recorded. '),
                "school_rating" => array('type' => 'string', 'isRequired' => $val, 'name' => 'Given the evidence provided, how would you rate the school on KPA: Leadership & Management, 9a. The organisation listens carefully to the views of the community? '),
                "rating_text" => array('type' => 'string', 'isRequired' => $val, 'name' => 'Why did you decide on this rating? '),
                "key_behaviour" => array('type' => 'string', 'isRequired' => $val, 'name' => 'What are the key behaviours demonstrated by an Adhyayan Assessor '),
                "key_performance" => array('type' => 'string', 'isRequired' => $val, 'name' => 'Which Key Performance Area(s) are you most interested in learning about? Please select one or more options '),
                "classroom_observation" => array('type' => 'string', 'isRequired' => $val, 'name' => 'What should you remember when doing a Classroom Observation during an AQS Review? '),
                "key_performance_text" => array('type' => 'string', 'isRequired' => $val, 'name' => 'Describe why these areas are important in your context.'),
                "stakeholder_text" => array('type' => 'string', 'isRequired' => $val, 'name' => 'What should you remember when interacting with different stakeholders during the AQS Review? '),
                //"learn" => array('type' => 'int', 'isRequired' => $val, 'name' => 'Self Review experience'),
                "goal" => array('type' => 'string', 'isRequired' => $val, 'name' => 'What is your professional development goal for the next 3 months?'),
                //"is_submit" => array('type' => 'string', 'isRequired' => $val, 'name' => 'What is your professional development goal for the next 3 months?')
            );
        //}
        //print_r($post);die;

        $errors = array();
        $values = array();

        foreach ($profileField as $key => $value) {
          
            if ($value["isRequired"] && empty($post[$key])) {
                $errors[] = "Field is required: '" . $value['name'] . "'";
            } else if (empty($val)) {
                $values[$key] = ($value["type"] == "int") ? 0 : '';
            } else if ($value["type"] == 'email' && preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $val) != 1) {
                $errors[] = "Invalid email: '" . $value['name'] . "'";
            } else if ($value["type"] == 'array' && !empty($val)) {
                if ($value['name'] == 'Language') {
                    foreach ($val as $k => $v) {
                        if ($v['language_id'] == '') {
                            $errors[] = "Field is required: 'Language'";
                            break;
                        } else if ($v['language_speak'] == '' && $v['language_read'] == '' && $v['language_write'] == '') {
                            $errors[] = "Field is required: 'Proficiency'";
                            break;
                        }
                    }
                } 
            }else {
                
                $values[$key] = $post[$key];
            }
                            

        }
        if(!isset($post['key_behaviour']) || isset($post['key_behaviour']) && count($post['key_behaviour']['answer_id']) < 1) {
            
            $errors[] = "Field is required: 'What are the key behaviours demonstrated by an Adhyayan Assesso'";
        }
        if(!isset($post['key_performance']) || isset($post['key_performance']) && count($post['key_performance']['answer_id']) < 1) {
            
            $errors[] = "Field is required: 'Which Key Performance Area(s) are you most interested in learning about? Please select one or more options '";
        }
        if(!isset($post['rating_text']) || isset($post['rating_text']) && count($post['rating_text']['answer_id']) < 1) {
            
            $errors[] = "Field is required: 'Why did you decide on this rating?  '";
        }else if(in_array(RATING_OTHERS_ID,$post['rating_text']['answer_id']) && empty($post['school_rating_txt']) ) {
             $errors[] = "Field is required: 'Why did you decide on this rating? '";
            
        }else {
             $values['school_rating_txt'] = $post['school_rating_txt'];
        }
        if(!isset($post['classroom_observation']) || isset($post['classroom_observation']) && count($post['classroom_observation']['answer_id']) < 1) {
            
            $errors[] = "Field is required: 'What should you remember when doing a Classroom Observation during an AQS Review? '";
        }
        if(!isset($post['stakeholder_text']) || isset($post['stakeholder_text']) && count($post['stakeholder_text']['answer_id']) < 1) {
            
            $errors[] = "Field is required: 'What should you remember when interacting with different stakeholders during the AQS Review?  '";
        }
        

        

        return array("errors" => $errors, "values" => $values);
    }
    
    //function to get id of options to calculate the score
    function calculateScore($finalArray){
         
        $score = 0;
        if(isset($finalArray['key_behaviour']['answer_id']) && !empty($finalArray['key_behaviour']['answer_id'])) {
            $res = $this->db->get_results("select question_option  from d_intro_assess_que_option where o_id  IN (".implode(',',$finalArray['key_behaviour']['answer_id']).")");
            $res = array_column($res,'question_option');
            $answerOptions =  explode(',', KEY_BEHAVIOURS );
            foreach($res as $data){
                if(in_array($data, $answerOptions)) {
                    $score++;
                }
            }
        }
        if(isset($finalArray['classroom_observation']['answer_id']) && !empty($finalArray['classroom_observation']['answer_id'])) {
            $res = $this->db->get_results("select question_option  from d_intro_assess_que_option where o_id  IN (".implode(',',$finalArray['classroom_observation']['answer_id']).")");
            $res = array_column($res,'question_option');
            $answerOptions =  explode('~', CLASSROOM_OBSERVATION );
            foreach($res as $data){
                if(in_array($data, $answerOptions)) {
                    $score++;
                }
            }
        }
        if(isset($finalArray['stakeholder_text']['answer_id']) && !empty($finalArray['stakeholder_text']['answer_id'])) {
            $res = $this->db->get_results("select question_option  from d_intro_assess_que_option where o_id  IN (".implode(',',$finalArray['stakeholder_text']['answer_id']).")");
            $res = array_column($res,'question_option');
            $answerOptions =  explode('~', STAKEHOLEDER );
            foreach($res as $data){
                if(in_array($data, $answerOptions)) {
                    $score++;
                }
            }
        }
        if(isset($finalArray['leader_statement']) && !empty($finalArray['leader_statement'])) {
            if($finalArray['leader_statement'] == 'Supposition') {
                    $score++;
                }
        }
        if(isset($finalArray['statement']) && !empty($finalArray['statement'])) {
            
                if($finalArray['statement'] == 'Evidence') {
                    $score++;
                }
            
        }
        if(isset($finalArray['school_rating']) && !empty($finalArray['school_rating'])) {
            
                if($finalArray['school_rating'] == 'Sometimes') {
                    $score++;
                }
            
        }
        if(isset($finalArray['rating_text']['answer_id']) && !empty($finalArray['rating_text']['answer_id'])) {
            $res = $this->db->get_results("select question_option  from d_intro_assess_que_option where o_id  IN (".implode(',',$finalArray['rating_text']['answer_id']).")");
            $res = array_column($res,'question_option');
            $answerOptions =  explode('~', SCHOOL_RATING );
            foreach($res as $data){
                if(in_array($data, $answerOptions)) {
                    $score++;
                }
            }
        }
       return  $score;
        //echo count(array_intersect($res, explode(',', KEY_BEHAVIOURS )));
               // return $res ? $res : array();
    }
    function getLanguageByCode($lang_code){
    	$res = $this->db->get_row("select language_id from d_language where language_code=? ;", array($lang_code));
    	return $res ? $res['language_id'] : '';
    }
    function getLanguageById($lang_id){
    	$res = $this->db->get_row("select  language_name  from d_language where language_id=? ;", array($lang_id));
    	return $res ? $res['language_name'] : '';
    }
    function getTranslationLanguale($lang_code){
        $len = count($lang_code);
    	$res = $this->db->get_results("select language_id,language_name,language_code,language_words from d_language where language_code in (?" . str_repeat(",?", $len - 1) . ") ORDER BY language_name ASC;", $lang_code);
    	return $res ? $res: array();
    }
    
    function setmaxconcatlength(){
        $queryMaxLength="SET SESSION group_concat_max_len = 1000000;";
        $this->db->query($queryMaxLength);
        
        /*$MaxLength="SELECT @@group_concat_max_len;";
        $max=$this->db->get_row($MaxLength);
        print_r($max);*/
        
    }
    
    function userExist($token){
        $token = current( explode("--", $token) );
        $queryT="select * from session_token where token=?";
        $res = $this->db->get_results($queryT, array($token));
        if($res){
         return true;   
        }else{
         return false;  
        }
    }
    
    /*public function addBacklistJWT($jwtToken,$expTime,$jwtType=0) {

        return $this->db->insert('session_blacklist', array("jwt_token" => $jwtToken, "exp_time" => $expTime,"jwt_type" => $jwtType ));
    }
    
    public function deleteBacklistJWT() {

        return $this->db->query('Delete from session_blacklist where exp_time<?',array(time()));
    }
    
    
    public function checkBacklistJWT($jwtToken) {
        $res = $this->db->get_results("select *  from session_blacklist where jwt_token=? limit 0,1" ,array($jwtToken));
    	return $res ? $res: array();
    }*/


}