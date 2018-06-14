<?php
class clientModel extends Model{
	
	function getIndividualClientList(){
		$res=$this->db->get_results("select  c.client_id,c.client_name,cn.network_id 
		from d_client c 
		left join h_client_network cn on cn.client_id=c.client_id
		where cn.network_id is null or cn.network_id=0;");
		return $res?$res:array();
	}
	function getIndividualClientProvinceList($network_id){
		$sql = "select c.client_id,c.client_name,cn.network_id  from d_network n 
				inner join h_client_network cn on n.network_id = cn.network_id
				inner join d_client c on c.client_id = cn.client_id				
			where n.network_id=? and cn.client_id not in (select client_id from h_client_province cp 
			inner join h_province_network pn  on cp.province_id = pn.province_id
			where pn.network_id = ? )";
		$res=$this->db->get_results($sql,array($network_id,$network_id));
		return $res?$res:array();
	}
	
	/* function getClients($args=array()){
		$args=$this->parse_arg($args,array("name_like"=>"","street_like"=>"","city_like"=>"","state_like"=>"","network_id"=>0,"max_rows"=>10,"page"=>1,"order_by"=>"name","order_type"=>"asc"));
		$order_by=array("client_name"=>"c.client_name","street"=>"c.street","city"=>"c.city","state"=>"c.state","create_date"=>"c.create_date","network"=>"n.network_name");
		$sqlArgs=array();
		
		$sql="select SQL_CALC_FOUND_ROWS c.*,date(c.create_date) as created_on, n.network_id, n.network_name
		from d_client c 
		left join h_client_network cn on cn.client_id=c.client_id
		left join d_network n on cn.network_id=n.network_id
		where 1=1 ";
		
		if($args['name_like']!=""){
			$sql.="and c.client_name like ? ";
			$sqlArgs[]="%".$args['name_like']."%";
		}
		if($args['street_like']!=""){
			$sql.="and c.street like ? ";
			$sqlArgs[]=$args['street_like']."%";
		}
		if($args['city_like']!=""){
			$sql.="and c.city like ? ";
			$sqlArgs[]=$args['city_like']."%";
		}
		if($args['state_like']!=""){
			$sql.="and c.state like ? ";
			$sqlArgs[]=$args['state_like']."%";
		}
		
		if($args['network_id']>0){
			$sql.="and n.network_id=? ";
			$sqlArgs[]=$args['network_id'];
		}
		
		$sql.=" order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"c.client_name").($args["order_type"]=="desc"?" desc ":" asc ").$this->limit_query($args['max_rows'],$args['page']);
		$res= $this->db->get_results($sql,$sqlArgs);
		$this->setPageCount($args['max_rows']);
		return $res;
	} */
	function getClients($args=array()){
		$args=$this->parse_arg($args,array("name_like"=>"","street_like"=>"","city_like"=>"","state_like"=>"","country_like"=>"","province_id"=>0,"network_id"=>0,"max_rows"=>10,"page"=>1,"order_by"=>"name","order_type"=>"asc"));
		$order_by=array("client_name"=>"c.client_name","street"=>"c.street","city"=>"c.city","state"=>"c.state","country"=>"c.country","province"=>"p.province_name","create_date"=>"c.create_date","network"=>"n.network_name","client_type"=>"cli.institution");
		$sqlArgs=array();
		
		$sql="select SQL_CALC_FOUND_ROWS c.*,s.state_name,ct.city_name,dc.country_name,date(c.create_date) as created_on, n.network_id, n.network_name,p.province_name,cli.institution
		from d_client c 
		left join d_countries dc ON c.country_id = dc.country_id		
		left join d_states s ON c.state_id = s.state_id
		left join d_cities ct ON c.city_id = ct.city_id 		
		left join h_client_network cn on cn.client_id=c.client_id				
		left join d_network n on cn.network_id=n.network_id
		left join h_client_province cp on cp.client_id = c.client_id	
		left join h_province_network pn on pn.network_id = n.network_id and cp.province_id = pn.province_id					
		left join d_province p on p.province_id = cp.province_id
                left join d_client_institution cli on c.client_institution_id = cli.client_institution_id
		where 1=1 ";
		
		if($args['name_like']!=""){
			$sql.="and c.client_name like ? ";
			$sqlArgs[]="%".$args['name_like']."%";
		}
		if($args['street_like']!=""){
			$sql.="and c.street like ? ";
			$sqlArgs[]=$args['street_like']."%";
		}
		if($args['city_like']!=""){
			$sql.="and ( ct.city_name like ? or c.city like ? )";
			$sqlArgs[]="%".$args['city_like']."%";
			$sqlArgs[]="%".$args['city_like']."%";
		}
		if(isset($args['state_id']) && $args['state_id']>0){
			$sql.="and s.state_id = ? ";
			$sqlArgs[]=$args['state_id'];				
		}		
		
		if(isset($args['country_id']) && $args['country_id']>0){
			$sql.="and dc.country_id=? ";
			$sqlArgs[]=$args['country_id'];
		}
                
                if(isset($args['client_institution_id']) && $args['client_institution_id']>0){
			$sql.="and c.client_institution_id=? ";
			$sqlArgs[]=$args['client_institution_id'];
		}
		
		if($args['network_id']>0){
			$sql.="and n.network_id=? ";
			$sqlArgs[]=$args['network_id'];
		}
                
		if($args['province_id']>0){
			$sql.="and p.province_id=? ";
			$sqlArgs[]=$args['province_id'];
		}
                
                if(isset($args['school_ids'])){
                    $sql.=" || c.client_name IN (".(implode(",",$args['school_ids'])).")";
                }
                
		$sql.=" order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"c.client_name").($args["order_type"]=="desc"?" desc ":" asc ").$this->limit_query($args['max_rows'],$args['page']);
		
		$res= $this->db->get_results($sql,$sqlArgs);
		$this->setPageCount($args['max_rows']);
		return $res;
	}
	
	function createClient($client_institution_id,$cname, $street,$addrLine2, $cityId, $state,$countryId, $phone, $remarks='',$web=0){				
		if($this->db->insert("d_client",array('client_institution_id'=>$client_institution_id,'client_name'=>$cname, 'street'=>$street,'addressLine2'=>$addrLine2, 'city_id'=>$cityId, 'state_id'=>$state,'country_id'=>$countryId, 'create_date'=>date("Y-m-d H:i:s"), "principal_phone_no"=>$phone, 'remarks'=>$remarks,'is_web'=>$web))){
			return $this->db->get_last_insert_id();
		}else
			return false;
	}
        //function to create bulk client
        function createClientBulk($schoolInfo) {

                //$sql = "INSERT IGNORE INTO d_client(client_name,principal_name,email,principal_phone_no,addressLine2,city,city_id,state,state_id,country_id,create_date) VALUES ";
                
               // $values = $this->createQueryValues($schoolInfo);
                //$sql = $sql . $values;
               // return $this->db->query($sql);
                 if($this->db->insert("d_client",$schoolInfo)){
                     return $this->db->get_last_insert_id();
                  }else
                  return false; 
        }
        
        //function to create query
        function createQueryValues($schoolInfo){
            $values = '';
            foreach ($schoolInfo as $dataArr) {
                $values .= "(";
                foreach($dataArr as $data){
                    
                    $values .= "'".$data."',";
                  // if(isset($data['client_name']) && $data['client_name']!='') {
                        //$values .= "('".$data['client_name']."','" .$data['addressLine2']."','" .$data['city']."','" .$data['city_id']."','" .$data['state']
                              //  ."','" .$data['state_id']."','" .$data['country_id']."','" .$data['principal_name']."','" .$data['email']."','" .date("Y-m-d h:i:s")."','" .$data['principal_phone_no']."'),";
                    //}
                }
                $values = rtrim($values, ",");
                $values .= "),";
            }
            //echo $values;die;
            return $values = rtrim($values, ",");
        }

        function updateClient($client_institution_id,$client_id,$cname, $street, $addrLine2, $cityId, $state,$countryId, $phone, $remarks=''){
		return $this->db->update("d_client",array('client_institution_id'=>$client_institution_id,'client_name'=>$cname, 'street'=>$street,'addressLine2'=>$addrLine2, 'city_id'=>$cityId, 'state_id'=>$state,'country_id'=>$countryId, "principal_phone_no"=>$phone, 'remarks'=>$remarks),array("client_id"=>$client_id));
	}
	
	function updateClientName($client_id,$cname){
		return $this->db->update("d_client",array('client_name'=>$cname),array("client_id"=>$client_id));
	}
	
	public function getClientById($clientId){
		/*$sql="SELECT 
				c.*,date(c.create_date) as created_on, n.network_id, n.network_name
				from d_client c
				left join h_client_network cn on cn.client_id=c.client_id
				left join d_network n on cn.network_id=n.network_id
				where c.client_id=? ";*/
		$sql="SELECT 
				c.*,date(c.create_date) as created_on, s.state_name, n.network_id, n.network_name,p.province_id,p.province_id
				from d_client c
				left join d_states s ON c.state_id = s.state_id
				left join h_client_network cn on cn.client_id=c.client_id
				left join d_network n on cn.network_id=n.network_id 
				left join h_client_province cp on cp.client_id = c.client_id 
				left join h_province_network pn on pn.network_id = n.network_id	and cp.province_id = pn.province_id 
				left join d_province p on p.province_id = cp.province_id
				where c.client_id=? ";		
		return $this->db->get_row($sql,array($clientId));
	}
	function getCountryList(){
		$res = $this->db->get_results("Select country_id,country_name from d_countries");
		return $res?$res:array();
	}
        
        function getInstitutionTypeList(){
            $res = $this->db->get_results("Select client_institution_id,institution  from d_client_institution where status=1");
	    return $res?$res:array();
        }
	/*public function getStateList($stateId=0){
		$sql="SELECT 
				state_id,state_name from d_states";
		if($stateId>0)
		{
			$sql .= " WHERE state_id = ? order by state_name asc;";
			return $this->db->get_row($sql,array($stateId));
		}
		$sql .= " order by state_name asc;";	
		return $this->db->get_results($sql);
		
	}*/
	function getStateList($countryId=101,$stateId=0){
		$sql = "select * from d_states WHERE country_id = ?";
		if($stateId>0)
			{
				$sql .= " AND state_id = ? order by state_name asc;";
				return $this->db->get_row($sql,array($countryId,$stateId));
			}
			 $sql .= " order by state_name asc;";
			return $this->db->get_results($sql,array($countryId));
	}
        /*
         * get country with country telephone code 
         */
	function getCountryWithCode(){
		$sql = "select * from d_countries WHERE phonecode !=0 ";
		
			 $sql .= " order by country_name asc;";
			return $this->db->get_results($sql);
	}
        function getStateWiseCityList(){
		$sql = "select s.state_name,GROUP_CONCAT(c.city_name  ORDER BY c.city_name) as city from d_cities c INNER JOIN d_states s  on c.state_id = s.state_id Where s.country_id=101 ";	
		$sql .= " GROUP BY  c.state_id;";
		return $this->db->get_results($sql);
	}
        function getStateCityList(){
		$sql = "select c.city_name ,c.city_id from d_cities c INNER JOIN d_states s  on c.state_id = s.state_id Where s.country_id=101 ";	
		//$sql .= " GROUP BY  c.state_id;";
		return $this->db->get_results($sql);
	}
	function getCityList($stateId=0){
		$sql = "select * from d_cities ";	
		$stateId>0? $sql.=" WHERE state_id = ?":'';
                $sql .= " order by city_name asc;";
		return $this->db->get_results($sql,array($stateId));
	}
        function getCityByName($stateId=0,$cityName){
		$sql = "select * from d_cities ";	
		$stateId>0? $sql.=" WHERE state_id = ?":'';
                ($cityName!='')?$sql.=" AND city_name = ?":'';
               // $sql .= " order by city_name asc;";
		return $this->db->get_results($sql,array($stateId,$cityName));
	}
        
	
	function addClientToNetwork($client_id,$network_id){
		return $this->db->insert("h_client_network",array("client_id"=>$client_id,"network_id"=>$network_id));
	}
	
	function removeClientFromNetwork($client_id,$network_id){
		return $this->db->delete("h_client_network",array("client_id"=>$client_id,"network_id"=>$network_id));
	}
	function removeClientFromNetworkProvince($client_id){
		return $this->db->delete("h_client_province",array("client_id"=>$client_id));
	}	
	function updateClientNetwork($client_id,$network_id){
		return $this->db->update("h_client_network",array("network_id"=>$network_id),array("client_id"=>$client_id));
	}
	function getAllClientNames(){
		$res=$this->db->get_results("SELECT distinct client_name FROM d_client;");
		return $res?$res:array();
	}
	function addClientToProvince($client_id,$province_id){
		return $this->db->insert("h_client_province",array("client_id"=>$client_id,"province_id"=>$province_id));
	}
	function removeClientFromProvince($client_id,$province_id){
		return $this->db->delete("h_client_province",array("client_id"=>$client_id,"province_id"=>$province_id));
	}
	function updateClientProvince($client_id,$province_id){
		return $this->db->update("h_client_province",array("province_id"=>$province_id),array("client_id"=>$client_id));
	}
}