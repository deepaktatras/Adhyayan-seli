<?php

class workshopController extends controller{

	

	function myworkshopAction(){
            
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
                $this->_notPermitted=1;

		elseif(in_array("view_own_workshop",$this->user['capabilities'])){
                    //print_r($this->user);
                    $workshopModel=new WorkshopModel();
                    $cPage=empty($_POST['page'])?1:$_POST['page'];
		    $order_by=empty($_POST['order_by'])?"workshop_date_from":$_POST['order_by'];
		    $order_type=empty($_POST['order_type'])?"desc":$_POST['order_type'];
                    if(isset($_REQUEST['uid']) && $_REQUEST['uid']!= '') {
                        $user_id = $_REQUEST['uid'];
                    }else if(!empty($this->user['user_id'])) {
                        $user_id = $this->user['user_id'];
                    }
                    
                    $param=array(
					"page"=>$cPage,
					"workshop_like"=>empty($_POST['workshop_name'])?"":$_POST['workshop_name'],
					"location_like"=>empty($_POST['workshop_location'])?"":$_POST['workshop_location'],
                                         "user_id"=>$user_id,
                                        //"user_id"=>315,
                                        "fdate_like"=>empty($_POST['fdate'])?"":ChangeFormat($_POST['fdate'],"Y-m-d"), 
                                        "edate_like"=>empty($_POST['edate'])?"":ChangeFormat($_POST['edate'],"Y-m-d"),
                                        "role_id"=>empty($_REQUEST['role_id'])?"":$_REQUEST['role_id'],
                                        "client_id"=>empty($_REQUEST['client_id'])?"":$_REQUEST['client_id'],
                                        "role"=>empty($_REQUEST['role'])?"":$_REQUEST['role'],
                                        "programme_id"=>empty($_POST['programme_id'])?"":$_POST['programme_id'], 
					"order_by"=>$order_by,
					"order_type"=>$order_type,
					);
                   // print_r($workshopModel->getWorkshops($param));
                    $this->set("filterParam",$param);
		    $this->set("workshops",$workshopModel->getWorkshops($param));
                    
                    $this->set("pages",$workshopModel->getPageCount());
                    $this->set("totalCount",$workshopModel->getTotalCount());
		    $this->set("cPage",$cPage);
		    $this->set("orderBy",$order_by);
		    $this->set("orderType",$order_type);
                    $this->set("list_roles_allowed",$workshopModel->get_list_roles_allowed());
                    $list_programmes=$workshopModel->get_list_programmes();
                    $this->set("list_programmes",$list_programmes);
                        
		}else

			$this->_notPermitted=1;

	}

	
        public function allworkshopAction(){
            
            if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
                $this->_notPermitted=1;

		elseif(in_array("manage_workshop",$this->user['capabilities'])){
                    //print_r($this->user);
                    $this->_template->addHeaderScript('userprofile.js'); 
                    $workshopModel=new WorkshopModel();
                    $cPage=empty($_POST['page'])?1:$_POST['page'];
		    $order_by=empty($_POST['order_by'])?"workshop_date_from":$_POST['order_by'];
		    $order_type=empty($_POST['order_type'])?"desc":$_POST['order_type'];
                    if(in_array(8,$this->user['role_ids'])){
                    $programme_id=1;    
                    }  else {
                    $programme_id=empty($_POST['programme_id'])?0:$_POST['programme_id'];  
                    }
                    
                    $param=array(
					"page"=>$cPage,
					"workshop_like"=>empty($_POST['workshop_name'])?"":$_POST['workshop_name'],
					"location_like"=>empty($_POST['workshop_location'])?"":$_POST['workshop_location'],
                                        "fdate_like"=>empty($_POST['fdate'])?"":ChangeFormat($_POST['fdate'],"Y-m-d"), 
                                        "edate_like"=>empty($_POST['edate'])?"":ChangeFormat($_POST['edate'],"Y-m-d"),
                                        "workshop_led_like"=>empty($_POST['workshop_led'])?"":$_POST['workshop_led'],
                                        "programme_id"=>$programme_id,
					"order_by"=>$order_by,
					"order_type"=>$order_type,
					);
                    
                    $this->set("filterParam",$param);
		    $this->set("workshops",$workshopModel->getAllWorkshops($param));
                    
                    $this->set("pages",$workshopModel->getPageCount());
                    $this->set("totalCount",$workshopModel->getTotalCount());
		    $this->set("cPage",$cPage);
		    $this->set("orderBy",$order_by);
		    $this->set("orderType",$order_type);
                    
                    $this->set("list_roles_allowed",$workshopModel->get_list_roles_allowed());
                    /*$list_prgrammes=array(array("workshop_prog_id"=>1,"workshop_prog_name"=>"TAP"),
                                          array("workshop_prog_id"=>2,"workshop_prog_name"=>"TFP"),
                                          array("workshop_prog_id"=>3,"workshop_prog_name"=>"AQS"),
                                         );*/
                    $list_programmes=$workshopModel->get_list_programmes();
                    $this->set("list_programmes",$list_programmes);
                        
		}else

			$this->_notPermitted=1;

        }

        public function createworkshopAction(){
        if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
                $this->_notPermitted=1;

	elseif(in_array("manage_workshop",$this->user['capabilities'])){
            $this->_template->addHeaderScript('userprofile.js'); 
         $clientModel=new clientModel();
         /*$programmes=array(array("workshop_prog_id"=>1,"workshop_prog_name"=>"TAP"),
                                          array("workshop_prog_id"=>2,"workshop_prog_name"=>"TFP"),
                                          array("workshop_prog_id"=>3,"workshop_prog_name"=>"AQS"),
                                         );*/
         $workshopModel=new WorkshopModel();
         $programmes=$workshopModel->get_list_programmes();
	 $this->set("clients",$clientModel->getClients(array("max_rows"=>-1)));   
         $this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
         $this->set("programmes",$programmes);
         $document_category=$workshopModel->getDocumentCategory();
         $this->set("document_category",$document_category);
         $programmestype=$workshopModel->getprogrammeType();
         $this->set("programmestype",$programmestype);
         
        }else

			$this->_notPermitted=1; 
        }
        
        public function editworkshopAction(){
         if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
                $this->_notPermitted=1;

	elseif(in_array("manage_workshop",$this->user['capabilities'])){
             $this->_template->addHeaderScript('userprofile.js');
         $workshopModel=new WorkshopModel();   
         $workshop_id = empty($_GET['wid'])?0:$_GET['wid'];
         $workshop = $workshopModel->getworkshopById($workshop_id);
         //print_r($workshop);
         if($workshop['programme_id']!=1 && in_array(8,$this->user['role_ids']) || $workshop['workshop_id']<=0){
          $this->_notPermitted=1;
          
         }else{
         $clientModel=new clientModel();
         if(!empty($workshop['document'])) {
             
             //$workshopFiles = $workshopModel->getFilesDetails($workshop['document']);
            // print_r($workshopFiles);
             $this->set("upload_document",$workshop['document']);
             $this->set("workshop_files",$workshopModel->getFilesDetails($workshop_id));
             
         }
         
         $this->set("clients",$clientModel->getClients(array("max_rows"=>-1)));   
         $this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
         $this->set("workshop_id",$workshop_id);
         $assessmentModel=new assessmentModel();
         $workshop_attende = $workshopModel->getAttendesbyWorkshopId($workshop_id);
         $this->set('facilitatorRoles',$workshopModel->getReviewerSubRolesFacilitator());
         $this->set("workshop",$workshop);
         $this->set("workshop_attende",$workshop_attende);
         $subroles=$workshop['subroles'];
         $subroles = explode(',',$subroles);
         $externalAssessorsTeam = array();
         
         
         //echo "<pre>";  print_r($workshopModel->getReviewerSubRolesFacilitator());
         $ledTeam = array();
         $k=0;
         foreach($subroles as $role=>$row){
             $exTeamClientId = explode('_',$row);

             if($exTeamClientId[2]==1 && $exTeamClientId[3]==1){
                 $ledTeam['led_client_id']=$exTeamClientId[0];
                 $ledTeam['led_id']=$exTeamClientId[1];
                 $ledTeam['led_role']=$exTeamClientId[2];
                 $ledTeam['payment_to_facilitator']=$exTeamClientId[4];
                 $ledTeam['led_facilitator']=$workshopModel->getUsersbyClient($exTeamClientId[0]);
                 $k++;
             }else{
                 $externalAssessorsTeam1 = array();
                 $externalAssessorsTeam1['client_id']=$exTeamClientId[0];
                 $externalAssessorsTeam1['id']=$exTeamClientId[1];
                 $externalAssessorsTeam1['role']=$exTeamClientId[2];
                 //$externalAssessorsTeam1['facilitator']=$assessmentModel->getFacilitators($exTeamClientId[0]);
                 $externalAssessorsTeam1['facilitator']=$workshopModel->getUsersbyClient($exTeamClientId[0]);

                 $externalAssessorsTeam1['payment_to_facilitator']=$exTeamClientId[4];

                 array_push($externalAssessorsTeam,$externalAssessorsTeam1);
             }
         }
         // echo "<pre>";print_r($ledTeam);
         $this->set("led",$ledTeam);
         $this->set("externalAssessorsTeam",$externalAssessorsTeam);
         /*$programmes=array(array("workshop_prog_id"=>1,"workshop_prog_name"=>"TAP"),
                                          array("workshop_prog_id"=>2,"workshop_prog_name"=>"TFP"),
                                          array("workshop_prog_id"=>3,"workshop_prog_name"=>"AQS"),
                                         );*/
         $list_programmes=$workshopModel->get_list_programmes();
         $this->set("programmes",$list_programmes);
         $document_category=$workshopModel->getDocumentCategory();
         $this->set("document_category",$document_category);
         $programmestype=$workshopModel->getprogrammeType();
         $this->set("programmestype",$programmestype);
         }
        }else

			$this->_notPermitted=1; 
            
        }
        
        function downloadAttendeesAction(){
        if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review
                $this->_notPermitted=1;

	elseif(in_array("manage_workshop",$this->user['capabilities'])){
         if(!empty($_GET['wid'])){
         $workshop_id = empty($_GET['wid'])?0:$_GET['wid'];
         $workshopModel=new WorkshopModel();
         $workshop = $workshopModel->getworkshopById($workshop_id);
         if($workshop['programme_id']!=1 && in_array(8,$this->user['role_ids'])){
          $this->_notPermitted=1;
         }else{
         $this->_render=false;
         // output headers so that the file is downloaded rather than displayed
         header('Content-Type: text/csv; charset=utf-8');
         header('Content-Type: application/force-download');
         header('Content-Disposition: attachment; filename=workshop_attendes_'.$workshop_id.'.csv');
         // create a file pointer connected to the output stream
         $output = fopen('php://output', 'w');
         $workshop_attende = $workshopModel->getAttendesbyWorkshopId($workshop_id);
         // output the column headings
         fputcsv($output, array('Title of Workshop', ''.$workshop['workshop_name'].''));
         fputcsv($output, array('Workshop Attendees'));
         fputcsv($output, array('Sr. No','Name','Email','Mode of attendance','Status'));
         $i=1;
         foreach($workshop_attende as $attende_detail){
         fputcsv($output, array(''.$i.'',''.$attende_detail['name'].'',''.$attende_detail['email'].'',''.$attende_detail['mode_attendance'].'',''.$attende_detail['attendance_status'].''));
         $i++;
         }
         }
         
         }
        }else

			$this->_notPermitted=1; 
            
        
        }
}