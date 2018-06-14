<?php
class networkModel extends Model{
	
	public function getNetworkList(){
		$res=$this->db->get_results("select * from d_network order by network_name");
		return $res?$res:array();
	}
	
	public function getNetworks($args=array()){
		$args=$this->parse_arg($args,array("name_like"=>"","max_rows"=>10,"page"=>1,"order_by"=>"name","order_type"=>"asc"));
		$order_by=array("name"=>"network_name","noOfClients"=>"noOfClients","province"=>"province_name");
		$sqlArgs=array();
		$sql="select SQL_CALC_FOUND_ROWS a.* from(
		
		select * from (
(select n.*, count(cn.client_id) as noOfClients,0 as clientInProvince,'' as province_id,''as province_name from d_network n left join h_client_network cn on cn.network_id=n.network_id left join d_client c on c.client_id=cn.client_id group by n.network_id )
 UNION 
 (select n.*, count(cn.client_id) as noOfClients,count(cp.client_id) as clientInProvince,IFNULL(p.province_id,''),IFNULL(p.province_name,'') from d_network n left join h_client_network cn on cn.network_id=n.network_id left join d_client c on c.client_id=cn.client_id	left join h_province_network pn on pn.network_id = n.network_id left join d_province p on p.province_id = pn.province_id left join h_client_province cp on cp.client_id = c.client_id and cp.province_id = pn.province_id group by n.network_id,p.province_id)
 ) a order by network_name,province_id 
) a WHERE 1=1  ";
		if($args['name_like']!=""){
			$sql.="and network_name like ? ";
			$sqlArgs[]=$args['name_like']."%";
		}
		if($args['province_like']!=""){
			$sql.="and province_name like ? ";
			$sqlArgs[]=$args['province_like']."%";
		}
		$sql.=" group by  network_id,province_id order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"network_name").($args["order_type"]=="desc"?" desc ":" asc ").(",province_id").$this->limit_query($args['max_rows'],$args['page']);
		$res= $this->db->get_results($sql,$sqlArgs);
		$this->setPageCount($args['max_rows']);
		return $res;
	}
	
	function getNetworkByClientId($client_id){
		return $this->db->get_row("select n.* from d_network n inner join h_client_network cn on n.network_id=cn.network_id where cn.client_id=?;",array($client_id));
	}
	
	function getNetworkByClientName($name,$exclude_id=0){
		return $this->db->get_row("select n.* from d_network n where n.network_name=? ".($exclude_id>0?"and n.network_id!=$exclude_id":"").";",array($name));
	}
	
	function createNetwork($name){
		if($this->db->insert("d_network",array("network_name"=>$name)))
			return $this->db->get_last_insert_id();
		else
			return false;
	}
	
	function updateNetwork($network_id,$name){
		return $this->db->update("d_network",array("network_name"=>$name),array("network_id"=>$network_id));
	}
	function updateProvince($province_id,$name){
		return $this->db->update("d_province",array("province_name"=>$name),array("province_id"=>$province_id));
	}
	
	function getNetworkById($network_id){
		$sql="select n.*, count(cn.client_id) as noOfClients
				from d_network n
				left join h_client_network cn on cn.network_id=n.network_id
                left join d_client c on c.client_id=cn.client_id
				where n.network_id=?
                group by n.network_id;";
		return $this->db->get_row($sql,array($network_id));
	}
	function getProvinceById($province_id){
		$sql="select distinct n.network_id,n.network_name,p.province_name,p.province_id from d_network n
				inner join h_client_network cn on cn.network_id=n.network_id
               -- inner join d_client c on c.client_id=cn.client_id				
				inner join h_province_network pn on pn.network_id = n.network_id
				inner join d_province p on p.province_id = pn.province_id
                -- left join h_client_province cp on cp.client_id = c.client_id and cp.province_id = pn.province_id
				where 1=1 and p.province_id = ?
                ";
		$res = $this->db->get_row($sql,array($province_id));
		return $res ? $res :array();
	}
	static function getEditSchoolsInnetworkRowHtml($network_id,$client){
		$address=$client['street']; 
		$address.=($address==""?"":", ").$client['city'];
		$address.=($address==""?"":", ").$client['state'];
		return '
			<tr>
				<td>'.$client['client_name'].'</td>
				<td>'.$address.'</td>
				<td><a href="javascript:void(0)" data-id="'.$client['client_id'].'" data-nid="'.$network_id.'" class="unlinkClient"><i class="vtip glyphicon glyphicon-remove" title="Remove School from network"></i></a></td>
			</tr>';
	}
	static function getEditSchoolsInnetworkProvinceRowHtml($province_id,$client){
		$address=$client['street'];
		$address.=($address==""?"":", ").$client['city'];
		$address.=($address==""?"":", ").$client['state'];
		return '
			<tr>
				<td>'.$client['client_name'].'</td>
				<td>'.$address.'</td>
				<td><a href="javascript:void(0)" data-id="'.$client['client_id'].'" data-pid="'.$province_id.'" class="unlinkClientFromProvince"><i class="vtip glyphicon glyphicon-remove" title="Remove School from province"></i></a></td>
			</tr>';
	}
	function getProvinceByName($province_name){
		$sql="Select province_name from d_province where province_name=?";
		$res = $this->db->get_row($sql,array($province_name));
		return $res?$res:array();
	}
	function createProvince($province_name){		
		if($this->db->insert("d_province",array("province_name"=>$province_name,"is_active"=>1)))
			return $this->db->get_last_insert_id();
		else
			return false;
	}
	function addProvinceToNetwork($province_id,$network_id){		
		if($this->db->insert("h_province_network",array("network_id"=>$network_id,"province_id"=>$province_id)))
			return $this->db->get_last_insert_id();
		else
			return false;
	}
	public function getProvinceList(){
		$res=$this->db->get_results("select * from d_province");
		return $res?$res:array();
	}
	function getProvinces($network_id){
		$sql = "select a.province_id,b.network_id,a.province_name from d_province a inner join h_province_network b on a.province_id=b.province_id
where network_id = ?";
		$res = $this->db->get_results($sql,array($network_id));
		return $res?$res:array();
	}
        
        function getMultiProvinces($network_ids){
                $sql = "select a.province_id,b.network_id,a.province_name from d_province a inner join h_province_network b on a.province_id=b.province_id";
                if(!substr_count($network_ids, 'all')) {
                   // print_r($network_ids);
                   // $ids = implode(',',$network_ids);
                    $network_ids = trim($network_ids,",");
                    $sql .= " where b.network_id IN ($network_ids)";
                }
		$res = $this->db->get_results($sql);
		return $res?$res:array();
	}
        function getSchools($province_ids){
		$sql = "select a.client_id,a.client_name from d_client a inner join h_client_province b on a.client_id=b.client_id";
                if(!substr_count($province_ids, 'all')) {
                   // $ids = implode(',',$province_ids);
                    $sql .= " where b.province_id IN ($province_ids)";
                }
		$res = $this->db->get_results($sql);
		return $res?$res:array();
	}
         //function to get schools by network id
        function getSchoolsByNetwork($network_ids){
		$sql = "select a.client_id,a.client_name from d_client a inner join h_client_network b on a.client_id=b.client_id";
                //if(!in_array('all',$network_ids)) {
                if(!substr_count($network_ids, 'all')) {
                    //$ids = implode(',',$network_ids);
                    $sql .= " where b.network_id IN ($network_ids)";
                }
		$res = $this->db->get_results($sql);
		return $res?$res:array();
	}
	function getProvinceField(){
		$html = '<dl class="fldList provinceField">
					<dt>Province Name<span class="astric">*</span>:</dt>										
					<dd class="the-basics province inputHldr"><input type="text" value="" class="form-control typeahead tt-query" name="province_name[]" required /><a href="javascript:void(0)" class="delete_row"><i class="fa fa-times-circle"></i></a></dd>
				</dl>';
		return $html;		
	}
}