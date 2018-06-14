<?php

class resourceModel extends Model {

    function createResource($resource_title, $resource_description, $resource_uploaded_by, $file_id, $roles_string,$parents='',$directory_id='',$network_option='',$resource_type=0,$file_name='', $web = 0,$resource_link_type="file",$url="",$tags='') {
            if ($this->db->insert("d_resources", array('resource_title' => $resource_title, 'resource_description' => $resource_description, 'resource_uploaded_by' => $resource_uploaded_by, 'directory_id' => $directory_id, 'parents_directory_path' => $parents,'network_option'=>$network_option,'resource_type'=>$resource_type,'file_name'=>$file_name,'tags'=>$tags, 'resource_added_at' => date('Y-m-d H:i:s')))) {
            $resource_id = $this->db->get_last_insert_id();
            if ($resource_id > 0) {
                if ($this->db->insert("h_resource_file", array('file_id' => $file_id, 'resource_id' => $resource_id, 'resource_link_type'=>$resource_link_type,'resource_url'=>$url, 'user_role_id' => $roles_string,'created_at' => date('Y-m-d H:i:s')))) {
                    return $resource_id;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else
            return false;
    }
    function deleteResourceUsers($resource_id) {
        
        $this->db->delete("h_resource_schools",array("resource_id"=>$resource_id));
        $this->db->delete("h_resource_networks",array("resource_id"=>$resource_id));
        //$this->db->delete("h_resource_networks",array("resource_id"=>$resource_id));
        $this->db->delete("h_resource_province",array("resource_id"=>$resource_id));
        $this->db->delete("h_resource_users",array("resource_id"=>$resource_id));
    }
    function deleteFolderResourceUsers($resourceIds) {
        
        $this->db->query("DELETE FROM h_resource_schools WHERE resource_id IN ($resourceIds)");
        $this->db->query("DELETE FROM h_resource_networks WHERE resource_id IN ($resourceIds)");
        //$this->db->delete("h_resource_networks",array("resource_id"=>$resource_id));
        $this->db->query("DELETE FROM h_resource_province WHERE resource_id IN ($resourceIds)");
        $this->db->query("DELETE FROM h_resource_users WHERE resource_id IN ($resourceIds)");
    }
    
    function deleteFolderUsers($folderIds) {
        
         $this->db->query("DELETE FROM h_folder_schools WHERE directory_id IN ($folderIds)");
        $this->db->query("DELETE FROM h_folder_networks WHERE directory_id IN ($folderIds)");
        //$this->db->delete("h_resource_networks",array("resource_id"=>$resource_id));
        $this->db->query("DELETE FROM h_folder_province WHERE directory_id IN ($folderIds)");
        $this->db->query("DELETE FROM h_folder_users WHERE directory_id IN ($folderIds)");
        $this->db->query("DELETE FROM h_folder_users_roles WHERE directory_id IN ($folderIds)");
       
    }
     function createResourceUsers($resresult='',$province,$school,$rec_user,$network,$from='',$resource_id='' ,$network_option='') {
         
         $resource_school_id = 0;$resource_network_id=0;$resource_network_id=0;$resource_province_id=0;$resource_user_id=0;
         if(!empty($school) && count($school)) {
             if($from == 1){
                  $this->deleteResourceUsers($resource_id);
             }
            $school_values =  $this->prepareValuesResource('all',$school,$resource_id);
            $sql_schools = "INSERT INTO h_resource_schools (resource_id,client_id ) VALUES $school_values";
            $resource_school_id = $this->db->query($sql_schools);
            
         }
         if(!empty($network) && count($network)) {
             if($from == 1){
                  $this->db->delete("h_resource_networks",array("resource_id"=>$resource_id));
             }
            $network_values =  $this->prepareValuesResource('all',$network,$resource_id);
            $sql_network = " INSERT INTO h_resource_networks (resource_id,network_id ) VALUES $network_values";
            $resource_network_id = $this->db->query($sql_network);
            
         }
         if(!empty($network_option)) {
             if($network_option == '2' || $network_option == '3'){
                  $this->db->delete("h_resource_networks",array("resource_id"=>$resource_id));
             }             
          //  $network_values =  $this->prepareValuesResource('all',$network,$resresult);
            //$sql_network = " INSERT INTO h_resource_networks (resource_id,network_id ) VALUES $network_values";
            //$resource_network_id = $this->db->query($sql_network);
            
         }
         if(!empty($province) && count($province)) {
             if($from == 1){
                  $this->db->delete("h_resource_province",array("resource_id"=>$resource_id));
             }
            $province_values =  $this->prepareValuesResource('all',$province,$resource_id);
            $sql_pro = " INSERT INTO h_resource_province (resource_id,province_id ) VALUES $province_values";
            $resource_province_id = $this->db->query($sql_pro);
            
         }
         if(!empty($rec_user) && count($rec_user)) {
             if($from == 1){
                  $this->db->delete("h_resource_users",array("resource_id"=>$resource_id));
             }
            $users_values =  $this->prepareValuesResource('all',$rec_user,$resource_id);
            $sql_user = " INSERT INTO h_resource_users (resource_id,user_id ) VALUES $users_values";
            $resource_user_id = $this->db->query($sql_user);
            
         }
        if($resource_school_id || $resource_user_id || $resource_province_id || $resource_network_id) {
            return true;
        }else{
            return false;
        }
        
    }
    function createFolderResourceUsers($resresult='',$province,$school,$rec_user,$network,$from='' ,$network_option='',$resourceIds,$resource_type=0,$roles_string='') {
        
       // print_r($rec_user);
         $resource_school_id = 0;$resource_network_id=0;$resource_network_id=0;$resource_province_id=0;$resource_user_id=0;
         $ids = '';
         if(!empty($resourceIds) && !empty($rec_user)) {
            
            $resourceValues = '';
            foreach($resourceIds as $data) {
                $ids .=  "'".$data['resource_id']."',";
            }
            $ids = trim($ids,",");
            // $ids = trim($ids,"'");
         }
         if(!empty($resource_type) && !empty($network_option)) {
             //echo $roles_string;
           //  $this->db->query("DELETE FROM d_resource_directory WHERE directory_id IN(?) ",array($ids));
               $this->db->query("UPDATE  d_resources SET resource_type=?,network_option=? WHERE resource_id IN($ids) ",array($resource_type,$network_option));
               $this->db->query("UPDATE  h_resource_file SET user_role_id=? WHERE resource_id IN($ids) ",array($roles_string));
             
         }
        // $ids = trim($ids,"'");
        //echo $resourceIds = implode(",",$resourceIds);
         if(!empty($school) && count($school)) {
             if($from == 1){
                  $this->deleteFolderResourceUsers($ids);
             }
            $school_values =  $this->prepareValuesFolderResource('all',$school,$resourceIds);
            $sql_schools = "INSERT INTO h_resource_schools (resource_id,client_id ) VALUES $school_values";
            $resource_school_id = $this->db->query($sql_schools);
            
         }
         if(!empty($network) && count($network)) {
             if($from == 1){
                  $sql = "DELETE FROM h_resource_networks WHERE resource_id IN ($ids)";
                  $this->db->query($sql);
             }
            $network_values =  $this->prepareValuesFolderResource('all',$network,$resourceIds);
            $sql_network = " INSERT INTO h_resource_networks (resource_id,network_id ) VALUES $network_values";
            $resource_network_id = $this->db->query($sql_network);
            
         }
         if(!empty($network_option)) {
             if($network_option == '2' || $network_option == '3'){
                  $this->db->query("DELETE FROM h_resource_networks WHERE resource_id IN ($ids)");
             }             
          //  $network_values =  $this->prepareValuesResource('all',$network,$resresult);
            //$sql_network = " INSERT INTO h_resource_networks (resource_id,network_id ) VALUES $network_values";
            //$resource_network_id = $this->db->query($sql_network);
            
         }
         if(!empty($province) && count($province)) {
             if($from == 1){
                  $this->db->query("DELETE FROM h_resource_province WHERE resource_id IN ($ids)");
             }
            $province_values =  $this->prepareValuesFolderResource('all',$province,$resourceIds);
             $sql_pro = " INSERT INTO h_resource_province (resource_id,province_id ) VALUES $province_values";
            $resource_province_id = $this->db->query($sql_pro);
            
         }
         if(!empty($rec_user) && count($rec_user)) {
             if($from == 1){
                  $this->db->query("DELETE FROM h_resource_users WHERE resource_id IN ($ids)");
             }
             //echo count($rec_user);
             //echo "<pre>";print_r($rec_user);
            $users_values =  $this->prepareValuesFolderResource('all',$rec_user,$resourceIds);
            $sql_user = " INSERT INTO h_resource_users (resource_id,user_id ) VALUES $users_values";
            $resource_user_id = $this->db->query($sql_user);
            
         }
        if($resource_school_id || $resource_user_id || $resource_province_id || $resource_network_id) {
            return true;
        }else{
            return false;
        }
        
        
        /*if(!empty($resourceIds) && !empty($rec_user)) {
            $ids = '';
            $resourceValues = '';
            foreach($resourceIds as $data) {
                $ids .=  $data['resource_id'].",";
            }
            $ids = trim($ids,",");
            $sql = "DELETE FROM h_resource_users WHERE resource_id in ($ids)";
            //$this->db->query($sql)
            $sqlData = '';
            $sqlUsers = "INSERT INTO h_resource_users (resource_id,user_id) VALUES ";
           if( $this->db->query($sql)){
               
            foreach($resourceIds as $data) {
               // $sqlData .=  $data['resource_id'].",";
                foreach($rec_user as $user) {
                     $sqlData .=  "(".$data['resource_id'].",".$user."),";
                }
            }  
            $sqlData = trim($sqlData,",");
            $sqlUsers =  $sqlUsers.$sqlData;
            return $this->db->query($sqlUsers);
            //$sql_network = " INSERT INTO h_resource_users (resource_id,user_id ) VALUES $values";
            //$folder_network_id = $this->db->query($sql_network);
           }
            //$this->db->delete("h_folder_networks",array("resource_id"=>$folder_id));
            
        }*/
        
        
    }
    function createFolderUsers($resresult='',$province,$school,$rec_user,$network,$from='',$folder_id='' ,$network_option='',$dir=array(),$resource_type=0,$roles_string='',$user_roles=array()) {
         
         $folder_school_id = 0;$folder_network_id=0;$folder_network_id=0;$folder_province_id=0;$folder_user_id=0;
         $ids='';
         //print_r($school);
         if(!empty($dir) && !empty($rec_user)) {
            
            foreach($dir as $key=>$value) {
                $ids .=  "'".$value."',";
            }
            $ids = trim($ids,",");
           // $ids = trim($ids,"'");
         }
         if(empty($dir)){
             $dir[] = $folder_id;
         }
         if(!empty($ids) && !empty($resource_type) && !empty($network_option)) {
             //echo $roles_string;
           //  $this->db->query("DELETE FROM d_resource_directory WHERE directory_id IN(?) ",array($ids));
               $this->db->query("UPDATE  d_resource_directory SET directory_type=?,network_option=? WHERE directory_id IN($ids) ",array($resource_type,$network_option));
             
         }
         if(empty($resource_type) && !empty($ids)){
             $this->deleteFolderUsers($ids);
         }
         if(!empty($school) && count($school)) {
             if($from == 1){
                  $this->deleteFolderUsers($ids);
             }
            $school_values =  $this->prepareValuesFolder('all',$school,$dir);
            if(!empty($school_values)){
                $sql_schools = "INSERT INTO h_folder_schools (directory_id,client_id ) VALUES $school_values";
                $folder_school_id = $this->db->query($sql_schools);
            }
            
         }
         if(!empty($network) && count($network)) {
             if($from == 1){
                  $this->db->query("DELETE FROM h_folder_networks WHERE directory_id IN($ids)");
             }
            $network_values =  $this->prepareValuesFolder('all',$network,$dir);
            if(!empty($network_values)) {
             $sql_network = " INSERT INTO h_folder_networks (directory_id,network_id ) VALUES $network_values";
             $folder_network_id = $this->db->query($sql_network);
            }
            
         }
         if(!empty($network_option)) {
             if($network_option == '2' || $network_option == '3'){
                  $this->db->query("DELETE FROM h_folder_networks WHERE directory_id IN($ids)");
             }             
          //  $network_values =  $this->prepareValuesResource('all',$network,$resresult);
            //$sql_network = " INSERT INTO h_resource_networks (resource_id,network_id ) VALUES $network_values";
            //$resource_network_id = $this->db->query($sql_network);
            
         }
         if(!empty($province) && count($province)) {
             if($from == 1){
                  $this->db->query("DELETE FROM h_folder_province WHERE directory_id IN($ids)");
             }
            $province_values =  $this->prepareValuesFolder('all',$province,$dir);
            if(!empty($province_values)) {
               $sql_pro = " INSERT INTO h_folder_province (directory_id,province_id ) VALUES $province_values";
                $folder_province_id = $this->db->query($sql_pro);
            }
            
         }
         if(!empty($user_roles) && count($user_roles)) {
             if($from == 1){
                  $this->db->query("DELETE FROM h_folder_users_roles WHERE directory_id IN($ids)");
             }
            $users_values =  $this->prepareValuesFolder('all',$user_roles,$dir);
            if(!empty($users_values)) {
                $sql_user = " INSERT INTO h_folder_users_roles (directory_id,role_id ) VALUES $users_values";
                $folder_user_id = $this->db->query($sql_user);
            }
            
         }
         if(!empty($rec_user) && count($rec_user)) {
             if($from == 1){
                  $this->db->query("DELETE FROM h_folder_users WHERE directory_id IN($ids)");
             }
            $users_values =  $this->prepareValuesFolder('all',$rec_user,$dir);
            if(!empty($users_values)) {
                $sql_user = " INSERT INTO h_folder_users (directory_id,user_id ) VALUES $users_values";
                $folder_user_id = $this->db->query($sql_user);
            }
            
         }
        if($folder_school_id || $folder_user_id || $folder_province_id || $folder_network_id) {
            return true;
        }else{
            return false;
        }
        
    }
    function updateParentUsers($resresult='',$province,$school,$rec_user,$network,$from='',$folder_id='' ,$network_option='',$dir=array(),$resource_type=0,$roles_string='',$user_roles=array()) {
         
         $folder_school_id = 0;$folder_network_id=0;$folder_network_id=0;$folder_province_id=0;$folder_user_id=0;
         $ids='';
         //print_r($school);
         if(!empty($dir) && !empty($rec_user)) {
            
            foreach($dir as $key=>$value) {
                $ids .=  "'".$value."',";
            }
            $ids = trim($ids,",");
           // $ids = trim($ids,"'");
         }
         if(empty($dir)){
             $dir[] = $folder_id;
         }
        if(!empty($resource_type) && !empty($network_option)) {
             //echo $roles_string;
           //  $this->db->query("DELETE FROM d_resource_directory WHERE directory_id IN(?) ",array($ids));
               $this->db->query("UPDATE  d_resource_directory SET directory_type=?,network_option=? WHERE directory_id IN($ids) ",array($resource_type,$network_option));
             
        }
         if(!empty($school) && count($school)) {
             if($from == 1){
                  $this->deleteFolderUsers($ids);
             }
            $school_values =  $this->prepareValuesFolder('all',$school,$dir);
            if(!empty($school_values)){
                $sql_schools = "INSERT INTO h_folder_schools (directory_id,client_id ) VALUES $school_values";
                $folder_school_id = $this->db->query($sql_schools);
            }
            
         }
         if(!empty($network) && count($network)) {
             if($from == 1){
                  $this->db->query("DELETE FROM h_folder_networks WHERE directory_id IN($ids)");
             }
            $network_values =  $this->prepareValuesFolder('all',$network,$dir);
            if(!empty($network_values)) {
             $sql_network = " INSERT INTO h_folder_networks (directory_id,network_id ) VALUES $network_values";
             $folder_network_id = $this->db->query($sql_network);
            }
            
         }
         
         if(!empty($province) && count($province)) {
             if($from == 1){
                  $this->db->query("DELETE FROM h_folder_province WHERE directory_id IN($ids)");
             }
            $province_values =  $this->prepareValuesFolder('all',$province,$dir);
            if(!empty($province_values)) {
               $sql_pro = " INSERT INTO h_folder_province (directory_id,province_id ) VALUES $province_values";
                $folder_province_id = $this->db->query($sql_pro);
            }
            
         }
         if(!empty($user_roles) && count($user_roles)) {
             if($from == 1){
                  $this->db->query("DELETE FROM h_folder_users_roles WHERE directory_id IN($ids)");
             }
            $users_values =  $this->prepareValuesFolder('all',$user_roles,$dir);
            if(!empty($users_values)) {
                $sql_user = " INSERT INTO h_folder_users_roles (directory_id,role_id ) VALUES $users_values";
                $folder_user_id = $this->db->query($sql_user);
            }
            
         }
         if(!empty($rec_user) && count($rec_user)) {
             if($from == 1){
                  $this->db->query("DELETE FROM h_folder_users WHERE directory_id IN($ids)");
             }
            $users_values =  $this->prepareValuesFolder('all',$rec_user,$dir);
            if(!empty($users_values)) {
                $sql_user = " INSERT INTO h_folder_users (directory_id,user_id ) VALUES $users_values";
                $folder_user_id = $this->db->query($sql_user);
            }
            
         }
        if($folder_school_id || $folder_user_id || $folder_province_id || $folder_network_id) {
            return true;
        }else{
            return false;
        }
        
    }
    function prepareValuesResource($key,$values,$resresult){
        
        if (!in_array($key, $values,TRUE)) {
            $query_values = '';
            foreach ($values as $data) {

                $query_values .= "(" . $resresult . ',' . $data . "),";
            }
        }else{
            $query_values = "(" . $resresult . ',' ."'". $key."'" . ")";
        }
        return rtrim($query_values,',');
    }
    function prepareValuesFolder($key,$values,$folderIds){
      $query_values = '';
        foreach($folderIds as $k=>$val)
        if (!in_array($key, $values,TRUE)) {
            
            foreach ($values as $data) {

                $query_values .= "(" . $val . ',' . $data . "),";
            }
        }else{
            $query_values = "(" . $val . ',' ."'". $key."'" . ")";
        }
        return rtrim($query_values,',');
    }
    function prepareValuesFolderResource($key,$values,$resourceIds){
        
        $query_values = '';
        foreach($resourceIds as $resresult)
        if (!in_array($key, $values,TRUE)) {
            
            foreach ($values as $data) {

                $query_values .= "(" . $resresult['resource_id'] . ',' . $data . "),";
            }
        }else{
            $query_values = "(" . $resresult['resource_id'] . ',' ."'". $key."'" . ")";
        }
        return rtrim($query_values,',');
    }
    

    function getResources($role_ids = array(), $args = array(),$user_id) {
        if (is_array($role_ids) && !empty($role_ids)) {
            $a = array();
            $b = array();
            foreach ($role_ids as $role_id) {
                $a[] = " FIND_IN_SET('" . $role_id . "',rf.user_role_id) <> 0";
                $b[] = " '" . $role_id . "' IN (1,2,8)";
            }
            $sqlCond = '';
            $whrCond = '';
            if(!in_array(1, $role_ids) && !in_array(2, $role_ids) && !in_array(8, $role_ids)) {
                $sqlCond .= "INNER JOIN h_resource_users as ru on r.resource_id = ru.resource_id ";
                $sqlCond .= "INNER JOIN h_resource_file as ruf on ruf.resource_id = ru.resource_id ";
                $whrCond = " AND ruf.status = 1";
            }
            $args = $this->parse_arg($args, array("max_rows" => 10, "page" => 1, "order_by" => "name", "order_type" => "asc"));
            $order_by = array("name" => "r.resource_title");
             $SQL = "select SQL_CALC_FOUND_ROWS  r.resource_title, f.file_name, f.file_id,f.upload_date, rf.status, r.resource_id, rf.id resource_file_id, 
            GROUP_CONCAT( CASE WHEN ug.role_id !=1 AND ug.role_id !=2 AND ug.role_id !=8
                THEN ug.role_name END ) role_name from d_resources as r "
                    . "LEFT JOIN h_resource_file as rf on r.resource_id = rf.resource_id "
                    .  $sqlCond 
                    . "LEFT JOIN d_file f on f.file_id = rf.file_id "
                    . "LEFT JOIN d_user_role ug ON FIND_IN_SET(ug.role_id, rf.user_role_id) > 0 "
                    . "where (" . implode(' OR ', $a) . ") 
                   AND (CASE When ((" . implode(' OR ', $b) . ")) THEN rf.status IN (1,0) ELSE rf.status = 1  END ) "
                    . "  $whrCond GROUP BY r.resource_id, rf.user_role_id";
            $SQL.=" order by " . (isset($order_by[$args["order_by"]]) ? $order_by[$args["order_by"]] : "r.resource_title") . ($args["order_type"] == "desc" ? " desc " : " asc ") . $this->limit_query($args['max_rows'], $args['page']);

            $res = $this->db->get_results($SQL);
            $this->setPageCount($args['max_rows']);
            return $res ? $res : array();
        }
    }

    function updateResource($h_resource_file_data, $d_resources_data, $resource_file_id, $resource_id,$resource_type=0) {
        if ($this->db->update("h_resource_file", $h_resource_file_data, array("id" => $resource_file_id))) {
            return $this->db->update("d_resources", $d_resources_data, array("resource_id" => $resource_id));
        }
    }
    function updateFolder( $d_resources_data,$folder_id) {
            return $this->db->update("d_resource_directory", $d_resources_data, array("directory_id" => $folder_id));
    }
    function getUpdateParent($folder_id,$parent_id) {
            return $this->db->update("d_resource_directory", array('parent_directory_id'=>$parent_id), array("parent_directory_id" => $folder_id));
    }
    function getFolderParent($folder_id) {
            $sql = "SELECT parent_directory_id FROM d_resource_directory WHERE directory_id = ?";
            return $this->db->get_row($sql, array($folder_id));
    }
    function getFileDetails($file_id) {
            $sql = "SELECT file_name FROM d_file WHERE file_id = ?";
            return $this->db->get_row($sql, array($file_id));
    }

    function getResourceById($resource_id, $resource_file_id) {
        $SQL = "Select r.tags,r.resource_type,r.resource_id, r.resource_title, r.resource_description, rf.id resource_file_id,rf.user_role_id, f.file_id, rf.user_role_id, f.file_name,r.network_option,r.directory_id,rf.resource_link_type,rf.resource_url from d_resources as r "
                . "LEFT JOIN h_resource_file as rf on r.resource_id = rf.resource_id "
                . "LEFT JOIN d_file f on f.file_id = rf.file_id "
                . "WHERE r.resource_id = ? AND rf.id = ?";

        if ($res = $this->db->get_row($SQL, array($resource_id, $resource_file_id))) {
            return $res;
        } else {
            return null;
        }
    }

    function updateUploadedFile($newName, $file_id) {
        return $this->db->update("d_file", array('file_name' => $newName), array("file_id" => $file_id));
    }
    
    //function to get schools by option type
    public function getSchoolsList($option_type) {

        $sql = "select client_id,client_name from d_client ORDER BY client_name ASC";
        if ($option_type == '2') {
            $sql = "SELECT d.client_id,d.client_name,h.network_id FROM d_client d LEFT JOIN "
                    . " `h_client_network` h ON d.client_id = h.client_id WHERE h.network_id IS null ORDER BY client_name ASC";
        }
        $res = $this->db->get_results($sql);
        return $res ? $res : array();
    }
    //function to get all resources of a folder
    public function getResourceIds($directory_id) {

        $sql = "select * FROM d_resources WHERE directory_id = ?  ";
        
        $res = $this->db->get_results($sql,array($directory_id));
        return $res ? $res : array();
    }
    //function to get school users
    function getSchoolUsers($school_ids,$user_role_ids='') {
        
          $sqlCond= '';
          if(!empty($user_role_ids)) {
              $sqlCond = "AND ur.role_id IN($user_role_ids)";
          }
          $SQL = "Select u.name,u.user_id from d_user as u "
            ." LEFT JOIN d_client as cl on u.client_id = cl.client_id "
            ." LEFT JOIN h_user_user_role urol  on u.user_id = urol.user_id "
            ." LEFT JOIN h_user_user_role ur  on u.user_id = ur.user_id " 
            ." WHERE cl.client_id IN ($school_ids ) $sqlCond  GROUP BY ur.user_id";

        if ($res = $this->db->get_results($SQL)) {
            return $res;
        } else {
            return null;
        }
    }
    
    function getTeachersStudentUsers($school_ids,$assessment_type,$gaid=0,$user=0) {
          
          if($gaid==0){
          $sqlCond= " && assessment_type_id='".$assessment_type."'";
          }else{
            $sqlCond="ga.group_assessment_id='".$gaid."'";  
            
            if($user>0){
               $sqlCond.=" && haue.user_id='".$user."' && hau.isFilled=1 "; 
            }
          }
          
          $SQL = "Select u.name,u.user_id from d_group_assessment ga"
            . " INNER JOIN h_assessment_ass_group hga on ga.group_assessment_id=hga.group_assessment_id"
            . " INNER JOIN d_assessment da on da.assessment_id=hga.assessment_id"
            . " INNER JOIN h_assessment_user hau on da.assessment_id=hau.assessment_id && hau.role=3"
            . " INNER JOIN h_assessment_user haue on da.assessment_id=haue.assessment_id && haue.role=4";
          
            $SQL.=" INNER JOIN d_user as u on u.user_id=hau.user_id"
            ."  INNER  JOIN d_client as cl on u.client_id = cl.client_id ";
          
            if($gaid==0){
            $SQL.=" WHERE cl.client_id IN ($school_ids ) $sqlCond  && hau.percComplete >0 GROUP BY u.user_id";
            } else{
            $SQL.=" WHERE $sqlCond && hau.percComplete >0  GROUP BY u.user_id";
            }

        if ($res = $this->db->get_results($SQL)) {
            return $res;
        } else {
            return null;
        }
    }
    //function to get school users roles
    function getSchoolUsersRoles($school_ids) {
        
            $SQL = "Select ur.role_name,ur.role_id from d_user as u "
            ." LEFT JOIN d_client as cl on u.client_id = cl.client_id "
            ." LEFT JOIN h_user_user_role urol  on u.user_id = urol.user_id "
            ." LEFT JOIN d_user_role ur  on urol.role_id = ur.role_id " 
            ." WHERE cl.client_id IN ($school_ids )   GROUP BY  ur.role_id";

        if ($res = $this->db->get_results($SQL)) {
            return $res;
        } else {
            return null;
        }
    }
    //function to get resource directories    
    function getResourceParentDirectoryList($role_ids=array(),$user_id=0) {
            //print_r($role_ids);
            $directoryIds = array();
            if(!in_array(1, $role_ids) && !in_array(2, $role_ids) && !in_array(8, $role_ids)) {
               
                $SQL = "SELECT r.`directory_id`, r.`parents_directory_path` "
                . " FROM d_resources r "
                . " INNER JOIN h_resource_users u ON u.`resource_id` = r.`resource_id`"
                . " INNER JOIN  h_resource_file rf  ON rf.`resource_id` = r.`resource_id`"
                . " WHERE u.user_id = ? AND rf.status = ? ";
                
                $directoryRes = $this->db->get_results($SQL,array($user_id,1));
                $directoryIds = array();
                foreach($directoryRes as $dir) {
                    $dirArr = explode("/",$dir['parents_directory_path']);
                    foreach($dirArr as $key=>$value) {
                        $directoryIds[] = $value ;
                    }
                }
                 $directoryIds = array_unique($directoryIds);              
                }
                $sqlCond = '';
               // print_r($directoryIds);
                if(!empty($directoryIds)) {
                    
                    $sqlCond = " WHERE c.directory_id IN (". implode(",", $directoryIds).")";
                }
                
                  $SQL = "SELECT c.`directory_id`, c.`directory_name`, cat.`directory_name` AS `ParentCategoryName`, cat.`directory_id` AS"
                . " `ParentCategoryId` FROM d_resource_directory c LEFT JOIN d_resource_directory cat ON "
                . " cat.`directory_id` = c.`parent_directory_id` "
                . " $sqlCond ORDER BY c.`directory_id`  ASC ";
        
                //$SQL = "SELECT directory_id,directory_name from d_resource_directory where parent_directory_id = $catid ";

                if ($res = $this->db->get_results($SQL)) {
                    return $res;
                } else {
                    return array();
                }
    }
    
    //get all folders of a user
    //function to get resource directories    
    function getUserDirectoryList($role_ids=array(),$user_id=0) {
            //print_r($role_ids);
            $directoryIds = array();
            if(!in_array(1, $role_ids) && !in_array(2, $role_ids) && !in_array(8, $role_ids)) {
               
                $SQL = "SELECT dr.`directory_id` FROM d_resource_directory dr LEFT JOIN 
                h_folder_users f ON f.directory_id=dr.directory_id "
                . " WHERE f.user_id = ?  ";
                
                $directoryRes = $this->db->get_results($SQL,array($user_id));
                //print_r($directoryRes);die;
                $directoryIds = array();
                foreach($directoryRes as $dir) {
                    //$dirArr = explode("/",$dir['parents_directory_path']);
                   // foreach($dirArr as $data) {
                        $directoryIds[] = $dir['directory_id'] ;
                    //}
                }
                $directoryIds[] = 1;
                 $directoryIds = array_unique($directoryIds);              
                }
                $sqlCond = '';
                //print_r($directoryIds);
                if(!empty($directoryIds)) {
                    
                    $sqlCond = " WHERE c.directory_id IN (". implode(",", $directoryIds).")";
                }
                
                  $SQL = "SELECT c.`directory_id`, c.`directory_name`, cat.`directory_name` AS `ParentCategoryName`, cat.`directory_id` AS"
                . " `ParentCategoryId` FROM d_resource_directory c LEFT JOIN d_resource_directory cat ON "
                . " cat.`directory_id` = c.`parent_directory_id` "
                . " $sqlCond ORDER BY c.`directory_id`  ASC ";
        
                //$SQL = "SELECT directory_id,directory_name from d_resource_directory where parent_directory_id = $catid ";

                if ($res = $this->db->get_results($SQL)) {
                    return $res;
                } else {
                    return array();
                }
    }
    //get search folders tree
    function getSearchDirectoryList($role_ids=array(),$user_id=0,$directoryIds  =array()) {
            //print_r($role_ids);
           // $directoryIds = array();
           /* if(!in_array(1, $role_ids) && !in_array(2, $role_ids) && !in_array(8, $role_ids)) {
               
                $SQL = "SELECT dr.`directory_id` FROM d_resource_directory dr LEFT JOIN 
                h_folder_users f ON f.directory_id=dr.directory_id "
                . " WHERE f.user_id = ?  ";
                
                $directoryRes = $this->db->get_results($SQL,array($user_id));
                //print_r($directoryRes);die;
                $directoryIds = array();
                foreach($directoryRes as $dir) {
                    //$dirArr = explode("/",$dir['parents_directory_path']);
                   // foreach($dirArr as $data) {
                        $directoryIds[] = $dir['directory_id'] ;
                    //}
                }
                $directoryIds[] = 1;
                 $directoryIds = array_unique($directoryIds);              
                }*/
                $directoryIds[] = 1;
                $directoryIds = array_unique($directoryIds); 
                $sqlCond = '';
               
                if(!empty($directoryIds)) {
                    
                    $sqlCond = " WHERE c.directory_id IN (". implode(",", $directoryIds).")";
                }
                
                  $SQL = "SELECT c.`directory_id`, c.`directory_name`, cat.`directory_name` AS `ParentCategoryName`, cat.`directory_id` AS"
                . " `ParentCategoryId` FROM d_resource_directory c LEFT JOIN d_resource_directory cat ON "
                . " cat.`directory_id` = c.`parent_directory_id` "
                . " $sqlCond ORDER BY c.`directory_id`  ASC ";
        
                //$SQL = "SELECT directory_id,directory_name from d_resource_directory where parent_directory_id = $catid ";

                if ($res = $this->db->get_results($SQL)) {
                    return $res;
                } else {
                    return array();
                }
    }
    //function to get all directory which have resource   
    function getResourceDirectoryList($resource_id = 0) {
        
                $whrCond = '';
                if(!empty($resource_id)) {
                     $whrCond = "WHERE res.resource_id = ".$resource_id;
                }
                 $SQL = "SELECT c.`directory_id`, c.`directory_name`, cat.`directory_name` AS `ParentCategoryName`, cat.`directory_id` AS"
                . " `ParentCategoryId` FROM d_resource_directory c LEFT JOIN d_resource_directory cat ON "
                . " cat.`directory_id` = c.`parent_directory_id` LEFT JOIN d_resources res ON"
                  . " res.directory_id = c.directory_id $whrCond ORDER BY cat.`directory_name` ASC ";
        
        //$SQL = "SELECT directory_id,directory_name from d_resource_directory where parent_directory_id = $catid ";

        if ($res = $this->db->get_results($SQL)) {
            return $res;
        } else {
            return array();
        }
    }
    //function to get all directory    
    function getDirectoryList() {
                 $SQL = "SELECT c.`directory_id`, c.`directory_name`, cat.`directory_name` AS `ParentCategoryName`, cat.`directory_id` AS"
                . " `ParentCategoryId` FROM d_resource_directory c LEFT JOIN d_resource_directory cat ON "
                . " cat.`directory_id` = c.`parent_directory_id`  ORDER BY c.`directory_id` ASC ";
        
        //$SQL = "SELECT directory_id,directory_name from d_resource_directory where parent_directory_id = $catid ";

        if ($res = $this->db->get_results($SQL)) {
            return $res;
        } else {
            return array();
        }
    }
    //function to get  directory parent child tree
    function getDirectoryTree($folder_id) {
        
                $SQL = "SELECT c.`directory_id`, c.`directory_name`, cat.`directory_name` AS `ParentCategoryName`, cat.`directory_id` AS"
                . " `ParentCategoryId` FROM d_resource_directory c LEFT JOIN d_resource_directory cat ON "
                . " cat.`directory_id` = c.`parent_directory_id` "
                . " WHERE cat.directory_id = ? ORDER BY c.`directory_id`  ASC ";
        //$SQL = "SELECT directory_id,directory_name from d_resource_directory where parent_directory_id = $catid ";

        if ($res = $this->db->get_results($SQL,array($folder_id))) {
            return $res;
        } else {
            return array();
        }
    }
    //function to get all resource    
    function getResourceList($directory_id,&$dir = array()) {
                 $SQL = "SELECT * from d_resource_directory where parent_directory_id= '$directory_id'";
        
        //$SQL = "SELECT directory_id,directory_name from d_resource_directory where parent_directory_id = $catid ";

        if ($res = $this->db->get_results($SQL)) {
            foreach ($res as $data){
                $dir[] = $data['directory_id'];
                $this->getResourceList($data['directory_id'],$dir);
                //echo  $SQL1 = "SELECT * from d_resources where directory_id= '".$data['directory_id']."'";
            }
           // echo "<pre>";print_r($res);
            //return $res;
        } 
        //$dir[] = $directory_id;
        return $dir;
    }
    
    //function to get all resource    
    function getAllParents($directory_id,&$parents = array()) {
        $SQL = "SELECT * from d_resource_directory where directory_id= '$directory_id'";
        if ($res = $this->db->get_row($SQL)) {
            if($res['parent_directory_id'] > 1){
                $parents[] = $res['parent_directory_id'];
                $this->getAllParents( $res['parent_directory_id'],$parents);
            }
        } 
        $parents[] = $directory_id;
        return $parents;
    }
    //function to get all resource    
    function getAllChilds($list,$directory_id,&$childs = array()) {
        //$SQL = "SELECT * from d_resource_directory where y ";
        if (!empty($list)) {
            foreach($list as $data) {
                
                  if ($data['ParentCategoryId'] == $directory_id) {
                        $childs[] = $data['directory_id'];
                        $this->getAllChilds($list, $data['directory_id'],$childs);
                    }
            }
           
        } 
       // $childs[] = $directory_id;
        return $childs;
    }
    //function to get all directory which have resource   
    function getDirectoryDetails($directory_id) {
                 $SQL = "SELECT directory_id,directory_name,parent_directory_id,directory_type,network_option,user_role_id,tags FROM d_resource_directory WHERE directory_id = ?";
        
        //$SQL = "SELECT directory_id,directory_name from d_resource_directory where parent_directory_id = $catid ";

        if ($res = $this->db->get_results($SQL,array($directory_id))) {
            return $res;
        } else {
            return array();
        }
    }
    //function to get all directory which have resource   
    function getFolderResources($dir){
                $SQL = "SELECT resource_id FROM d_resources WHERE directory_id IN (".implode(",",$dir).") ";
        
        //$SQL = "SELECT directory_id,directory_name from d_resource_directory where parent_directory_id = $catid ";

        if ($res = $this->db->get_results($SQL)) {
            return $res;
        } else {
            return array();
        }
    }
    //function to get resource directories    
    function getDirectoryRecourceList($role_ids,$user_id,$directory_id=0,$childs = array()) {
        
                //print_r($role_ids);
                $sqlCond = '';
                $whrCond = '';
                if(!in_array(1, $role_ids) && !in_array(2, $role_ids) && !in_array(8, $role_ids)) {
                    $sqlCond .= "INNER JOIN h_resource_users ru ON  ru.resource_id = cat.resource_id INNER JOIN d_user u ON  ru.user_id = u.user_id "
                            . " INNER JOIN d_user u2 ON  cat.resource_uploaded_by = u2.user_id ";
                    $whrCond = " where ru.user_id = ".$user_id ." AND rf.status = 1";
                }else {
                    $sqlCond .=  "INNER JOIN d_user u2 ON  cat.resource_uploaded_by = u2.user_id ";
                }
                
                if(!empty($directory_id)) {
                    
                    if(!empty($whrCond))
                        $whrCond .= ' AND ';
                    else 
                         $whrCond .= ' WHERE '; 
                    
                    $whrCond .= 'cat.directory_id = '.$directory_id;
                }
                if(!empty($childs)) {
                    
                    if(!empty($whrCond))
                        $whrCond .= ' AND ';
                    else 
                         $whrCond .= ' WHERE '; 
                    
                    $whrCond .= 'cat.directory_id IN ('.implode(",",$childs).")";
                }
                $SQL = "SELECT rf.status,u2.name as user_name,c.`directory_id`, c.`directory_name`,f.file_name,f.file_id, cat.resource_id, cat.resource_title,cat.file_name as file,f.upload_date,f.uploaded_by,f.file_size,rf.id as resource_file_id,rf.status,rf.resource_link_type,rf.resource_url"
                . " FROM d_resource_directory c "
                . "INNER JOIN d_resources cat ON  cat.`directory_id` = c.`directory_id` "
                . "INNER JOIN h_resource_file rf ON  rf.resource_id = cat.resource_id "
                . $sqlCond
                . "LEFT JOIN d_file f ON  f.file_id = rf.file_id "                 
                . " $whrCond  GROUP BY cat.resource_id ORDER BY c.`directory_id` ASC ";
        
        //$SQL = "SELECT directory_id,directory_name from d_resource_directory where parent_directory_id = $catid ";
        //echo $SQL;
        if ($res = $this->db->get_results($SQL)) {
            return $res;
        } else {
            return array();
        }
    }
    //function to get resource directories    
    function getRecourceLogList() {
        
                //print_r($role_ids);
                $sqlCond = '';
                $whrCond = '';
            
                $SQL = "SELECT resource_id  from d_resource_download_logs   ";
        
        //$SQL = "SELECT directory_id,directory_name from d_resource_directory where parent_directory_id = $catid ";
       //echo $SQL;
        if ($res = $this->db->get_results($SQL)) {
            return $res;
        } else {
            return array();
        }
    }
    //function to add new directory
    function addNewResourceDirectory($dir_name,$parent_id,$roles,$type=0,$network_option=0,$dir_tags='') {
        if ($this->db->insert("d_resource_directory", array('directory_name' => $dir_name, 'parent_directory_id' => $parent_id,'user_role_id'=>$roles,'directory_type'=>$type,'network_option'=>$network_option,'tags'=>$dir_tags))) {
            return $this->db->get_last_insert_id();
            
        } else
            return false;
    }
    // check directory already exist 
    function checkDirectoryExistance($dir_name,$parent_id) {
         $SQL = "SELECT directory_id FROM d_resource_directory WHERE directory_name = ? AND parent_directory_id = ?  ";
        if ($res = $this->db->get_row($SQL,array($dir_name,$parent_id))) {
            return $res;
        } else {
            return null;
        }
    }
    
    // fetch resource networks 
    function getResourceNetworkList($resource_id) {
         $SQL = "SELECT network_id FROM h_resource_networks WHERE resource_id = ?  ";
        if ($res = $this->db->get_results($SQL,array($resource_id))) {
            return $res;
        } else {
            return null;
        }
    }
    // fetch directory networks 
    function getFolderNetworkList($resource_id) {
         $SQL = "SELECT network_id FROM h_folder_networks WHERE directory_id = ?  ";
        if ($res = $this->db->get_results($SQL,array($resource_id))) {
            return $res;
        } else {
            return null;
        }
    }
    // gets resources provinces
    function getResourceProvinces($resource_id) {
         $SQL = "SELECT province_id FROM h_resource_province WHERE resource_id = ?  ";
        if ($res = $this->db->get_results($SQL,array($resource_id))) {
            return $res;
        } else {
            return null;
        }
    }
    // gets directory provinces
    function getFolderProvinces($resource_id) {
         $SQL = "SELECT province_id FROM h_folder_province WHERE directory_id = ?  ";
        if ($res = $this->db->get_results($SQL,array($resource_id))) {
            return $res;
        } else {
            return null;
        }
    }
    // gets resources users
    function getResourceUsers($resource_id) {
         $SQL = "SELECT user_id FROM h_resource_users WHERE resource_id = ?  ";
        if ($res = $this->db->get_results($SQL,array($resource_id))) {
            return $res;
        } else {
            return null;
        }
    }
    // gets folder users
    function getFoldereUsers($resource_id) {
         $SQL = "SELECT user_id FROM h_folder_users WHERE directory_id = ?  ";
        if ($res = $this->db->get_results($SQL,array($resource_id))) {
            return $res;
        } else {
            return null;
        }
    }
    // gets folder users roles
    function getFoldereRoles($resource_id) {
         $SQL = "SELECT * FROM h_folder_users_roles WHERE directory_id = ?  ";
        if ($res = $this->db->get_results($SQL,array($resource_id))) {
            return $res;
        } else {
            return null;
        }
    }
    // gets resources schools
    function getResourceSchools($resource_id) {
        // $SQL = "SELECT client_id,client_name FROM h_resource_schools WHERE resource_id = ?  ";
        $SQL = "select a.client_id,a.client_name from d_client a inner join h_resource_schools b on a.client_id=b.client_id WHERE b.resource_id = ?";
        if ($res = $this->db->get_results($SQL,array($resource_id))) {
            return $res;
        } else {
            return null;
        }
    }
    // gets folder schools
    function getFolderSchools($directory_id) {
        // $SQL = "SELECT client_id,client_name FROM h_resource_schools WHERE resource_id = ?  ";
        $SQL = "select a.client_id,a.client_name from d_client a inner join h_folder_schools b on a.client_id=b.client_id WHERE b.directory_id = ?";
        if ($res = $this->db->get_results($SQL,array($directory_id))) {
            return $res;
        } else {
            return null;
        }
    }
    // gets provinces in network
    function getProvinceInNetwork($networks) {
        // $SQL = "SELECT client_id,client_name FROM h_resource_schools WHERE resource_id = ?  ";
        $sqlCond = "(";
        foreach($networks as $key=>$value) {
            $sqlCond .= "?,";
        }
        $sqlCond = trim($sqlCond,",");
        $sqlCond .= ")";
        $SQL = "select count(province_network_id) as num_province from h_province_network hp "
                . " INNER JOIN d_province p ON p.province_id = hp.province_id WHERE hp.network_id  IN $sqlCond";
        if ($res = $this->db->get_row($SQL,$networks)) {
            return $res;
        } else {
            return null;
        }
    }
    // gets provinces in network
    function getAllFilesByName($serachVal,$page) {
        // $SQL = "SELECT client_id,client_name FROM h_resource_schools WHERE resource_id = ?  ";
       //print_r($role_ids);
                $sqlCond = '';
                $rowPerPage = 10;
                $whrCond = ' WHERE r.resource_title LIKE  '. "'%$serachVal%'  OR r.tags LIKE "."'%$serachVal%'";
               /* if(!in_array(1, $role_ids) && !in_array(2, $role_ids) && !in_array(8, $role_ids)) {
                    $sqlCond .= "INNER JOIN h_resource_users ru ON  ru.resource_id = cat.resource_id INNER JOIN d_user u ON  ru.user_id = u.user_id "
                            . " INNER JOIN d_user u2 ON  cat.resource_uploaded_by = u2.user_id ";
                    $whrCond = " where ru.user_id = ".$user_id ." AND rf.status = 1";
                }else {
                    $sqlCond .=  "INNER JOIN d_user u2 ON  cat.resource_uploaded_by = u2.user_id ";
                }*/
                  $SQL = "SELECT SQL_CALC_FOUND_ROWS f.file_name,f.file_id, r.resource_id, r.resource_title,r.file_name as file,f.upload_date,f.uploaded_by,f.file_size,rf.id as resource_file_id,rf.status,rf.resource_link_type,rf.resource_url"
                . " FROM d_resources r "
                . "INNER JOIN h_resource_file rf ON  rf.`resource_id` = r.`resource_id` "
                . "INNER JOIN d_file f ON  f.file_id = rf.file_id "
                . " $whrCond ORDER BY r.`resource_title` ASC ".$this->limit_query($rowPerPage,$page);
        if ($res = $this->db->get_results($SQL)) {
            $this->setPageCount($rowPerPage); 
            return $res;
        } else {
            return null;
        }
    }
    // gets folder by name
    function getAllFolderByName($serachVal,$page) {
        // $SQL = "SELECT client_id,client_name FROM h_resource_schools WHERE resource_id = ?  ";
       //print_r($role_ids);
                $sqlCond = '';
                $rowPerPage = 10;
                $whrCond = ' WHERE d.directory_name LIKE  '. "'%$serachVal%' OR d.tags LIKE "."'%$serachVal%'";
               /* if(!in_array(1, $role_ids) && !in_array(2, $role_ids) && !in_array(8, $role_ids)) {
                    $sqlCond .= "INNER JOIN h_resource_users ru ON  ru.resource_id = cat.resource_id INNER JOIN d_user u ON  ru.user_id = u.user_id "
                            . " INNER JOIN d_user u2 ON  cat.resource_uploaded_by = u2.user_id ";
                    $whrCond = " where ru.user_id = ".$user_id ." AND rf.status = 1";
                }else {
                    $sqlCond .=  "INNER JOIN d_user u2 ON  cat.resource_uploaded_by = u2.user_id ";
                }*/
                $SQL = "SELECT SQL_CALC_FOUND_ROWS d.directory_id,d.directory_name,d.directory_type,r.directory_id as resource_directory "
                . " FROM d_resource_directory d"
                          . " LEFT JOIN d_resources r ON r.directory_id = d.directory_id  "
                . " $whrCond GROUP BY d.directory_id ORDER BY d.directory_name ASC ".$this->limit_query($rowPerPage,$page);
        if ($res = $this->db->get_results($SQL)) {
            $this->setPageCount($rowPerPage); 
            return $res;
        } else {
            return null;
        }
    }
    // check directory already exist for given title 
    function getResourceByTitle($resource_title) {
         $SQL = "SELECT resource_id FROM d_resources WHERE resource_title  = ?";
        if ($res = $this->db->get_row($SQL,array($resource_title))) {
            return $res;
        } else {
            return null;
        }
    }
    /*
     * function to delete directory
     */
    function deleteDirectory($directory_id) {
        
        if($this->db->delete("d_resource_directory",array("directory_id"=>$directory_id))) {
            
            return true;
        }
        return false;
    }
    /*
     * function to delete directory
     */
    function editResourceDirectory($directory_id,$directory_name) {
        
        if($this->db->update("d_resource_directory",array("directory_name"=>$directory_name),array('directory_id'=>$directory_id))) {
            
            return true;
        }
        return false;
    }
    // check directory is valid for delete 
    function chkValidDirectoryForDelete($directory_id) {
         $SQL = "SELECT directory_id FROM d_resource_directory WHERE directory_id  = ?";
         $this->db->get_row($SQL,array($directory_id));
            
            if(!empty($res) && $res['directory_id']>=1) {
                $SQL = "SELECT directory_id FROM d_resource_directory WHERE parent_directory_id  = ?";
                $res = $this->db->get_row($SQL,array($directory_id));
                if(!empty($res) && $res['directory_id']>=1) {
                    return false;
                }else {
                    return true;
                    
                }
            }else {
                return false;
            }
        
    }
    // function to check resource download log is exist with a resurce and user
    function saveResourceDownloadLog($resource_id,$user_id) {
         $SQL = "SELECT log_id FROM d_resource_download_logs WHERE resource_id  = ? and user_id = ?";
         $res =$this->db->get_row($SQL,array($resource_id,$user_id));
            
            if(!empty($res))  {
                
                $d_resources_data = array('log_date'=>date("Y-m-d h:i:s"));
                if($this->db->update("d_resource_download_logs", $d_resources_data, array("resource_id" => $resource_id,'user_id'=>$user_id)))
                    return true;
            }else {
                
                $d_resources_data = array("resource_id" => $resource_id,'user_id'=>$user_id,'log_date'=>date("Y-m-d h:i:s"));
                if($this->db->insert("d_resource_download_logs", $d_resources_data))
                    return true;
                
            }
            return false;
          
    }
    
    //function to get resource logs
      function getResourceDownloadData($resource_id) {
            $sql = "SELECT r.resource_title as resource_name,u.name,l.log_date FROM d_resource_download_logs l "
                    . "INNER JOIN d_resources r ON l.resource_id = r.resource_id "
                    . "INNER JOIN d_user u  ON u.user_id = l.user_id  WHERE l.resource_id = ?";
            return $this->db->get_results($sql, array($resource_id));
    }
    // check directory is valid for delete 
    function chkChilds($directory_id) {
            $SQL = "SELECT count(directory_id) as  num_childs,parent_directory_id FROM d_resource_directory WHERE parent_directory_id  = (SELECT parent_directory_id FROM d_resource_directory WHERE directory_id = ?)";
            $res = $this->db->get_row($SQL,array($directory_id));
           // print_r($res)
            if(!empty($res) && $res['num_childs']>=1) {
                $SQL = "SELECT count(resource_id) as num_files FROM d_resources WHERE directory_id  = ?";
                $file_res = $this->db->get_row($SQL,array($res['parent_directory_id']));
                if(!empty($file_res) && $file_res['num_files']) {
                    $res['num_files'] = $file_res['num_files'];
                }else
                    $res['num_files'] = 0;
                //if(!empty($res) && $res['directory_id']>=1) {
                return $res;
                //}else {
                    //return true;
                    
               // }
            }else {
                return false;
            }
        
    }
    
    
    

}
