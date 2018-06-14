<?php
class networkController extends controller{
	
	function networkAction(){
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
		elseif(in_array("create_network",$this->user['capabilities'])){
			$networkModel=new networkModel();
			$cPage=empty($_POST['page'])?1:$_POST['page'];
			$order_by=empty($_POST['order_by'])?"name":$_POST['order_by'];
			$order_type=empty($_POST['order_type'])?"asc":$_POST['order_type'];
			$param=array(
					"page"=>$cPage,
					"name_like"=>empty($_POST['name'])?"":$_POST['name'],
					"province_like"=>empty($_POST['province'])?"":$_POST['province'],
					"order_by"=>$order_by,
					"order_type"=>$order_type,
					);
			$this->set("filterParam",$param);
			$this->set("networks",$networkModel->getNetworks($param));
			
			$this->set("pages",$networkModel->getPageCount());
			$this->set("cPage",$cPage);
			$this->set("orderBy",$order_by);
			$this->set("orderType",$order_type);
		}else
			$this->_notPermitted=1;
	}
	
	function editNetworkAction(){
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
		elseif(in_array("create_network",$this->user['capabilities'])){
			$networkModel=new networkModel();
			$this->set("eNetwork",empty($_GET['id'])?array():$networkModel->getNetworkById($_GET['id']));
			$clientModel=new clientModel();
			$this->set("clients", empty($_GET['id'])?array():$clientModel->getClients(array("network_id"=>$_GET['id'],"max_rows"=>-1)) );
			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
			$this->set("networks",$networkModel->getNetworkList());
		}else
			$this->_notPermitted=1;
	}
	function editNetworkProvinceAction(){		
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
			elseif(in_array("create_network",$this->user['capabilities'])){
				$networkModel=new networkModel();
				//print_r($networkModel->getProvinceById($_GET['pid']));
				$this->set("provinceData",empty($_GET['pid'])?array():$networkModel->getProvinceById($_GET['pid']));
				//$this->set("eNetwork",empty($_GET['id'])?array():$networkModel->getNetworkById($_GET['id']));
				$clientModel=new clientModel();
				$this->set("clients", empty($_GET['pid'])?array():$clientModel->getClients(array("province_id"=>$_GET['pid'],"max_rows"=>-1)) );
				$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
				$this->set("networks",$networkModel->getNetworkList());
				$this->set("provinces",$networkModel->getProvinceList());
			}else
				$this->_notPermitted=1;
	}
	function createNetworkAction(){
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
		elseif(in_array("create_network",$this->user['capabilities']))
		{
			$networkModel=new networkModel();
			$this->set("networks",$networkModel->getNetworkList());
			//$this->set("provinces",$networkModel->getProvinceList());
			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
		}else
			$this->_notPermitted=1;					
	}
	
	function addSchoolToNetworkAction(){
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
		elseif(!in_array("create_network",$this->user['capabilities'])){
			$this->_notPermitted=1;
		}else if(empty($_GET['network_id'])){
			$this->_is404=1;
		}else{
			$clientModel=new clientModel();
			$this->set("clients", $clientModel->getIndividualClientList() );
			$this->set("network_id", $_GET['network_id'] );
		}			
	}
	function addSchoolToProvinceAction(){
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
			elseif(!in_array("create_network",$this->user['capabilities'])){
				$this->_notPermitted=1;
			}else if(empty($_GET['province_id']) && empty($_GET['network_id'])){
				$this->_is404=1;
			}else{
				$clientModel=new clientModel();
				$this->set("clients", $clientModel->getIndividualClientProvinceList($_GET['network_id']) );
				$this->set("province_id", $_GET['province_id'] );
				$this->set("network_id", $_GET['network_id'] );
			}
	}
	function createProvinceAction(){
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
			elseif(!in_array("create_network",$this->user['capabilities'])){
				$this->_notPermitted=1;			
			}else{
				$networkModel=new networkModel();
				$this->set("networks",$networkModel->getNetworkList());
				$this->set("provinces",$networkModel->getProvinceList());
				$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
			}
	}
	
}