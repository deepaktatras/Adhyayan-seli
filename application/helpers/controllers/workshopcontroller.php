<?php

class workshopController extends controller{

	

	function myworkshopAction(){
            
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
                $this->_notPermitted=1;

		elseif(in_array("view_own_workshop",$this->user['capabilities'])){
                    //print_r($this->user);
                    $workshopModel=new WorkshopModel();
                    $cPage=empty($_POST['page'])?1:$_POST['page'];
		    $order_by=empty($_POST['order_by'])?"workshop_date":$_POST['order_by'];
		    $order_type=empty($_POST['order_type'])?"desc":$_POST['order_type'];
                    
                    $param=array(
					"page"=>$cPage,
					"workshop_like"=>empty($_POST['workshop_name'])?"":$_POST['workshop_name'],
					"location_like"=>empty($_POST['workshop_location'])?"":$_POST['workshop_location'],
                                        "user_id"=>empty($this->user['user_id'])?0:$this->user['user_id'],
                                        "fdate_like"=>empty($_POST['fdate'])?"":$_POST['fdate'], 
                                        "edate_like"=>empty($_POST['edate'])?"":$_POST['edate'],
                                        "sub_role_id"=>empty($_POST['sub_role_id'])?"":$_POST['sub_role_id'], 
					"order_by"=>$order_by,
					"order_type"=>$order_type,
					);
                    
                    $this->set("filterParam",$param);
		    $this->set("workshops",$workshopModel->getWorkshops($param));
                    
                    $this->set("pages",$workshopModel->getPageCount());
                    $this->set("totalCount",$workshopModel->getTotalCount());
		    $this->set("cPage",$cPage);
		    $this->set("orderBy",$order_by);
		    $this->set("orderType",$order_type);
                    $this->set("list_roles_allowed",$workshopModel->get_list_roles_allowed());
                    
                        
		}else

			$this->_notPermitted=1;

	}

	


}