<?php
class clientController extends controller{
	
	function clientAction(){
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
		elseif(in_array("create_client",$this->user['capabilities']) || in_array("manage_own_network_clients",$this->user['capabilities'])){
			$clientModel=new clientModel();	
			$cPage=empty($_POST['page'])?1:$_POST['page'];
			$order_by=empty($_POST['order_by'])?"create_date":$_POST['order_by'];
			$order_type=empty($_POST['order_type'])?"desc":$_POST['order_type'];
			$param=array(
					"page"=>$cPage,
					"name_like"=>empty($_POST['name'])?"":$_POST['name'],
					"street_like"=>empty($_POST['street'])?"":$_POST['street'],
					"city_like"=>empty($_POST['city'])?"":$_POST['city'],
					"state_id"=>empty($_POST['state_id'])?0:$_POST['state_id'],
					"country_id"=>empty($_POST['country_id'])?0:$_POST['country_id'],
					"province_id"=>empty($_POST['province_id'])?"":$_POST['province_id'],
					"network_id"=>empty($_POST['network_id'])?0:$_POST['network_id'],
					"order_by"=>$order_by,
					"order_type"=>$order_type,
					);
			$canCreateClient=1;
			if(!in_array("create_client",$this->user['capabilities'])){
				$param['network_id']=$this->user['network_id'];
				$canCreateClient=0;
			}
			$this->set("canCreateClient",$canCreateClient);
			$this->set("filterParam",$param);
			$this->set("clients",$clientModel->getClients($param));
			$this->set("countries",$clientModel->getCountryList());			
			//$this->set("states",$clientModel->getStateList(101));//for india
			$this->set("states",empty($_POST['country_id'])?array():$clientModel->getStateList($_POST['country_id']));
			//$this->set("cities",$clientModel->getCityList());//for mumbai
			//$this->set("cities",array());//for mumbai
			$this->set("cities",empty($_POST['state_id'])?array():$clientModel->getCityList($_POST['state_id']));						
			$this->set("pages",$clientModel->getPageCount());
			$this->set("cPage",$cPage);
			$this->set("orderBy",$order_by);
			$this->set("orderType",$order_type);
			$networkModel=new networkModel();
			$this->set("networks",$networkModel->getNetworkList());
                        $this->set("provinces",empty($_POST['network_id'])?array():$networkModel->getProvinces($_POST['network_id']));
		}else
			$this->_notPermitted=1;
	}
	
	function createClientAction(){
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
		elseif(in_array("create_client",$this->user['capabilities'])){
			$networkModel=new networkModel();
			$clientModel = new clientModel();
			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
			$this->set("networks",$networkModel->getNetworkList());
			$this->set("countries",$clientModel->getCountryList());	
			$this->set("provinces",$networkModel->getProvinceList());
		}else
			$this->_notPermitted=1;
	}
	
	function editClientAction(){
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
		elseif(in_array("create_client",$this->user['capabilities'])){
			$clientModel=new clientModel();	
			$client_data = empty($_GET['id'])?array():$clientModel->getClientById($_GET['id']);
			$this->set("eClient",$client_data);
			$networkModel=new networkModel();
			$this->set("networks",$networkModel->getNetworkList());
			$this->set("principal",$this->userModel->getPrincipal($_GET['id']));
			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
			$this->set("countries",$clientModel->getCountryList());
			$this->set("states",$clientModel->getStateList($client_data['country_id']));
			$this->set("cities",$clientModel->getCityList($client_data['state_id']));
			$this->set("provinces",empty($client_data['network_id'])?array():$networkModel->getProvinces($client_data['network_id']));			
		}else
			$this->_notPermitted=1;
	}
	
	function clientListAction(){
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
			$this->_notPermitted=1;
		elseif(in_array("manage_all_users",$this->user['capabilities']) || ($this->user['network_id']>0 && in_array("manage_own_network_users",$this->user['capabilities'])) || in_array("create_assessment",$this->user['capabilities'])){
			$clientModel=new clientModel();	
			$cPage=empty($_POST['page'])?1:$_POST['page'];
			$order_by=empty($_POST['order_by'])?"create_date":$_POST['order_by'];
			$order_type=empty($_POST['order_type'])?"desc":$_POST['order_type'];
			$param=array(
					"page"=>$cPage,
					"name_like"=>empty($_POST['name'])?"":$_POST['name'],
					"street_like"=>empty($_POST['street'])?"":$_POST['street'],
					"city_like"=>empty($_POST['city'])?"":$_POST['city'],
					"state_id"=>empty($_POST['state_id'])?0:$_POST['state_id'],
					"country_id"=>empty($_POST['country_id'])?0:$_POST['country_id'],
					"province_id"=>empty($_POST['province_id'])?"":$_POST['province_id'],
					"network_id"=>empty($_POST['network_id'])?0:$_POST['network_id'],
					"order_by"=>$order_by,
					"order_type"=>$order_type,
					);
			if(!in_array("manage_all_users",$this->user['capabilities'])){
				$param["network_id"]=$this->user['network_id'];
			}
			$this->set("for",empty($_POST['for'])?"":$_POST['for']);
			$this->set("filterParam",$param);
			//$this->set("clients",$clientModel->getClients($param));
			$states = $clientModel->getStateList();
			$this->set("clients",$clientModel->getClients($param));
			$this->set("countries",$clientModel->getCountryList());			
			//$this->set("states",$clientModel->getStateList(101));//for india
			$this->set("states",empty($_POST['country_id'])?array():$clientModel->getStateList($_POST['country_id']));
			//$this->set("cities",$clientModel->getCityList());//for mumbai
			//$this->set("cities",array());//for mumbai
			$this->set("cities",empty($_POST['state_id'])?array():$clientModel->getCityList($_POST['state_id']));			
			$this->set("pages",$clientModel->getPageCount());
			$this->set("cPage",$cPage);
			$this->set("orderBy",$order_by);
			$this->set("orderType",$order_type);
			$networkModel=new networkModel();
			$this->set("networks",$networkModel->getNetworkList());
                        $this->set("provinces",empty($_POST['network_id'])?array():$networkModel->getProvinces($_POST['network_id']));
		}else
			$this->_notPermitted=1;
	}
}