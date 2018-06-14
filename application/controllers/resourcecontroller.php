<?php

class resourceController extends Controller {

    function resourceList1Action() {

        $resourceModel = new resourceModel();
        $cPage = empty($_POST['page']) ? 1 : $_POST['page'];
        $order_by = empty($_POST['order_by']) ? "name" : $_POST['order_by'];
        $order_type = empty($_POST['order_type']) ? "asc" : $_POST['order_type'];
        $param = array(
            "page" => $cPage,
            //"name_like"=>empty($_POST['name'])?"":$_POST['name'],
            "order_by" => $order_by,
            "order_type" => $order_type
        );
        
        $resourceList = $resourceModel->getResources($this->user['role_ids'], $param);
       

        $this->set("pages", $resourceModel->getPageCount());
        $this->set("resourceList", $resourceList);
        $this->set("cPage", $cPage);
        $this->set("orderBy", $order_by);
        $this->set("orderType", $order_type);
    }

    function createResourceAction() {
        if (!in_array("upload_resources", $this->user['capabilities'])) {
            $this->_notPermitted = 1;
        } else {            
            $resourceModel = new resourceModel();
            $networkModel = new networkModel();
            $resourceModel = new resourceModel();
            $list = array();
            $list = $resourceModel->getResourceParentDirectoryList($this->user['role_ids'],$this->user['user_id']);
            $this->set("directory_list",$this->buildTree($list,0));
            $this->set("networks", $networkModel->getNetworkList());
            $this->set("provinces", $networkModel->getProvinceList());
            $this->set("provinces", $resourceModel->getResourceParentDirectoryList($this->user['role_ids'],$this->user['user_id']));
            $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
            $this->set("roles", $this->userModel->getRoles());
            $this->_template->addHeaderStyle('bootstrap-multiselect.css');    
            $this->_template->addHeaderScript('bootstrap-multiselect.js'); 
            $this->_template->addHeaderScript('resource.js');            
            $this->_template->addHeaderStyle('filetree.css');    
            $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
            $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
            $this->_template->addHeaderStyle('bootstrap-tagsinput.css');
            $this->_template->addHeaderScript('bootstrap-tagsinput.min.js');
            $this->_template->addHeaderScript('bootstrap-tagsinput.js');
        }
    }

    function editResourceAction() {
        if (!in_array("upload_resources", $this->user['capabilities'])) {
            $this->_notPermitted = 1;
        } else {
            $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
            $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
            if (empty($_GET['resource_id']) || empty($_GET['resource_file_id'])) {
                $this->_notPermitted = 1;
            } else {
                $resourceModel = new resourceModel();
                $networkModel = new networkModel();
                $resource_detail = $resourceModel->getResourceById($_GET['resource_id'], $_GET['resource_file_id']);
                if (empty($resource_detail)) {
                    $this->_notPermitted = 1;
                } else {
                    $resource_networks = array();
                    $this->set("succMsg", empty($_GET['succMsg']) ? 0 : $_GET['succMsg']);
                    $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
                    $this->set("roles", $this->userModel->getRoles());
                    $this->set("resource_detail", $resource_detail);
                    $this->set("resource_id", $_GET['resource_id']);
                    $this->set("resource_file_id", $_GET['resource_file_id']);
                    //$this->set("networks",$networkModel->getNetworkList());
                    $list = $resourceModel->getResourceParentDirectoryList($this->user['role_ids'],$this->user['user_id']);
                    $this->set("directory_list",$this->buildTree($list,0));
                    $this->set("networks", $networkModel->getNetworkList());
                    $resource_networks = $resourceModel->getResourceNetworkList($_REQUEST['resource_id']);
                    //print_r($resource_networks);die;
                    $network_provinces = array();
                    $resource_provinces = array();
                    $provinces_schools = array();
                    $resource_schools = array();
                    $resource_users = array();
                    $schools_users = array();
                    $network_ids = '';
                    $provinces_ids = '';
                    //echo $resource_detail['network_option'];
                    if($resource_detail['network_option'] != '1' ) {
                        $provinces_schools  =  $resourceModel->getSchoolsList($resource_detail['network_option']);
                        $resource_schools = $resourceModel->getResourceSchools($_GET['resource_id']);
                    }else{ 
                       // if(count($resource_networks)>0) {
                        if(count($resource_networks)) {
                            foreach($resource_networks as $networks) {
                                if($networks == 'all') 
                                    $network_ids = 'all';
                                else                                
                                   $network_ids .=$networks['network_id'].",";                                
                            }
                            $network_ids = rtrim($network_ids,",");
                            $network_provinces = $networkModel->getMultiProvinces($network_ids);
                        }
                        
                        $resource_provinces = $resourceModel->getResourceProvinces($_GET['resource_id']);
                        
                        //print_r($resource_provinces);
                    //}
                   
                    //if(count($network_provinces)>0) {
                        if(count($resource_provinces)) {
                            foreach($resource_provinces as $provinces) {
                                if($provinces == 'all') 
                                    $provinces_ids = 'all';
                                else                                
                                   $provinces_ids .=$provinces['province_id'].",";                                
                            }
                            $provinces_ids = rtrim($provinces_ids,",");
                        }
                        $resource_schools = $resourceModel->getResourceSchools($_GET['resource_id']);
                        if($provinces_ids){                            
                             $provinces_schools = $networkModel->getSchools($provinces_ids);
                        }else if($network_ids){
                            $provinces_schools = $networkModel->getSchoolsByNetwork($network_ids);
                        }
                        //if($network_provinces)
                        //print_r($resource_schools);
                    }
                   // }
                   // print_r($resource_networks);
                    if(count($resource_schools)>0) {
                    $schools_ids = '';
                    foreach ($resource_schools as $schools)
                        $schools_ids .= $schools['client_id'] . ",";
                        $schools_users = $resourceModel->getSchoolUsers(rtrim($schools_ids, ","), $resource_detail['user_role_id']);
                        $resource_users = $resourceModel->getResourceUsers($_GET['resource_id']);
                    }
                    //$resource_networks = $resourceModel->getResourceNetworkList($_GET['resource_file_id']);
                    $this->set("resource_networks", $resource_networks);
                    
                    $this->set("network_provinces", $network_provinces);
                    $this->set("resource_provinces", $resource_provinces);
                    $this->set("resource_schools", $resource_schools);
                    $this->set("provinces_schools", $provinces_schools);
                    $this->set("school_users", $schools_users);
                    $this->set("resource_users", $resource_users);
                    $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
                    $this->_template->addHeaderStyle('bootstrap-multiselect.css');    
                    $this->_template->addHeaderScript('bootstrap-multiselect.js'); 
                    $this->_template->addHeaderScript('resource.js');
                    $this->_template->addHeaderStyle('bootstrap-tagsinput.css');
                    $this->_template->addHeaderScript('bootstrap-tagsinput.min.js');
                    $this->_template->addHeaderScript('bootstrap-tagsinput.js');
                }
            }
        }
    }
    
    function updateFolderAction() {
       // print_r($this->user['capabilities']);
        if (!in_array("upload_resources", $this->user['capabilities'])) {
            $this->_notPermitted = 1;
        } else {
            $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
            $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
            if (empty($_GET['directory_id'])) {
                $this->_notPermitted = 1;
            } else {
                $resourceModel = new resourceModel();
                $networkModel = new networkModel();
                $folder_detail = $resourceModel->getDirectoryDetails($_GET['directory_id']);
                $folder_detail = $folder_detail[0];
                $allNetworks = $networkModel->getNetworkList();
                if (empty($folder_detail)) {
                    $this->_notPermitted = 1;
                } else {
                     
                    $resource_networks = array();
                    $this->set("succMsg", empty($_GET['succMsg']) ? 0 : $_GET['succMsg']);
                    $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
                    $this->set("roles", $this->userModel->getRoles());
                    $this->set("folder_detail", $folder_detail);
                    $this->set("directory_id", $_GET['directory_id']);
                    //$this->set("resource_file_id", $_GET['resource_file_id']);
                    //$this->set("networks",$networkModel->getNetworkList());
                    $list = $resourceModel->getDirectoryList();
                    $this->set("directory_list",$this->buildTree($list,0));
                    
                    $resource_networks = $resourceModel->getFolderNetworkList($_REQUEST['directory_id']);
                    //echo "<pre>";print_r( $folder_detail);die;
                    $network_provinces = array();
                    $resource_provinces = array();
                    $provinces_schools = array();
                    $resource_schools = array();
                    $resource_users = array();
                    $schools_users = array();
                    $folder_users =array();
                    $schools_users =array();
                   // $networks =array();
                    $network_ids = '';
                    $provinces_ids = '';
                    $is_network = 1;
                    if(isset($folder_detail['network_option']) && $folder_detail['network_option'] == '0' ) {
                        //$provinces_schools  =  $resourceModel->getSchoolsList($folder_detail['network_option']);
                        $resource_schools = $resourceModel->getFolderSchools($_GET['directory_id']);                        
                       // print_r($provinces_schools);
                    } else if(isset($folder_detail['network_option']) && $folder_detail['network_option'] != '1' ) {
                        $provinces_schools  =  $resourceModel->getSchoolsList($folder_detail['network_option']);
                        $resource_schools = $resourceModel->getFolderSchools($_GET['directory_id']);
                        $is_network = 0;
                       // print_r($provinces_schools);
                    } else  {
                   
                       // if(count($resource_networks)>0) {
                        if(count($resource_networks)) {
                            foreach($resource_networks as $networks) {
                                if($networks == 'all') 
                                    $network_ids = 'all';
                                else                                
                                   $network_ids .=$networks['network_id'].",";                                
                            }
                            $network_ids = rtrim($network_ids,",");
                            $network_provinces = $networkModel->getMultiProvinces($network_ids);
                        }
                        
                        $resource_provinces = $resourceModel->getFolderProvinces($_GET['directory_id']);
                        
                       
                    //}
                   
                    //if(count($network_provinces)>0) {
                        if(count($resource_provinces)) {
                            foreach($resource_provinces as $provinces) {
                                if($provinces == 'all') 
                                    $provinces_ids = 'all';
                                else                                
                                   $provinces_ids .=$provinces['province_id'].",";                                
                            }
                            $provinces_ids = rtrim($provinces_ids,",");
                        }
                        $resource_schools = $resourceModel->getFolderSchools($_GET['directory_id']);
                        if($provinces_ids){                            
                             $provinces_schools = $networkModel->getSchools($provinces_ids);
                        }else if($network_ids){
                            $provinces_schools = $networkModel->getSchoolsByNetwork($network_ids);
                        }
                        //if($network_provinces)
                    }
                   // }
                    //print_r($resource_schools);
                  
                    if(count($resource_schools)>0) {
                    $schools_ids = '';
                    
                    foreach ($resource_schools as $schools)
                        $schools_ids .= $schools['client_id'] . ",";
                        $schools_users = $resourceModel->getSchoolUsers(rtrim($schools_ids, ","));
                       
                    }
                     $folder_users = $resourceModel->getFoldereUsers($_GET['directory_id']);
                     $folder_roles = $resourceModel->getFoldereRoles($_GET['directory_id']);
                     if(!empty($folder_roles))
                        $folder_roles = array_unique(array_column($folder_roles, 'role_id'));
                    //$resource_networks = $resourceModel->getResourceNetworkList($_GET['resource_file_id']);
                   // echo "<pre>";print_r($folder_roles);
                    $this->set("resource_networks", $resource_networks);
                    $this->set("network_provinces", $network_provinces);
                    $this->set("resource_provinces", $resource_provinces);
                    $this->set("resource_schools", $resource_schools);
                    $this->set("provinces_schools", $provinces_schools);
                    $this->set("school_users", $schools_users);
                    $this->set("resource_users", $folder_users);
                    $this->set("folder_roles", $folder_roles);
                    $this->set("networks", $allNetworks);
                    $this->set("is_network", $is_network);
                    $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
                    $this->_template->addHeaderStyle('bootstrap-multiselect.css');    
                    $this->_template->addHeaderScript('bootstrap-multiselect.js'); 
                    $this->_template->addHeaderScript('resource.js');
                    $this->_template->addHeaderStyle('bootstrap-tagsinput.css');
                    $this->_template->addHeaderScript('bootstrap-tagsinput.min.js');
                    $this->_template->addHeaderScript('bootstrap-tagsinput.js');
                }
            }
        }
    }
    function treeFolderAction() {
                // print_r($this->user['capabilities']);
               /* $resourceList = array();
                $resourceModel = new resourceModel();
                $networkModel = new networkModel();
                $cPage = empty($_POST['page']) ? 1 : $_POST['page'];
                $directory_id = empty($_REQUEST['directory_id']) ? '' : $_REQUEST['directory_id'];
                

                        $order_by = empty($_POST['order_by']) ? "name" : $_POST['order_by'];
                        $order_type = empty($_POST['order_type']) ? "asc" : $_POST['order_type'];
                        $param = array(
                         "page" => $cPage,
                         "order_by" => $order_by,
                         "order_type" => $order_type
                        );
                $resourceList = $resourceModel->getResources($this->user['role_ids'], $param,$this->user['user_id']);
                if (empty($resourceList)) {
                    $this->_notPermitted = 1;
                } else {
                        

                       //print_r($this->user);
                        



                          // echo "<pre>"; print_r($resourceList);


                            $resource_networks = array();
                            $this->set("succMsg", empty($_GET['succMsg']) ? 0 : $_GET['succMsg']);
                            $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
                            $this->set("roles", $this->userModel->getRoles());
                            $this->set("resource_detail", $resourceList);
                            ////$this->set("networks",$networkModel->getNetworkList());
                            $parents_childs = $resourceModel->getAllParents($directory_id);
                            $childs = $resourceModel->getAllChilds($directory_id);
                            $list = $resourceModel->getSearchDirectoryList($this->user['role_ids'],$this->user['user_id'],$parents_childs);
                            
                           
                            
                            //echo "<pre>";print_r($childs);

                           // $fileList = $resourceModel->getDirectoryRecourceList();
                            $fileList = $resourceModel->getDirectoryRecourceList($this->user['role_ids'],$this->user['user_id'],$directory_id);

                          //  echo "<pre>";print_r($fileList);
                            $fileList = $this->getDirectoryFiles($fileList);
                            //echo "<pre>";print_r($fileList);
                            //echo"</pre>";
                            //die;
                            echo"<pre>"; print_r($this->buildTree($list,0,'Tree',$fileList));die;
                            $directory_list = $this->buildTree($list,0,'Tree',$fileList);
                            if (empty($fileList)&& (!in_array(1, $this->user['role_ids']) && !in_array(2, $this->user['role_ids']) && !in_array(8, $this->user['role_ids']))) {
                                $this->set("is_resource",1);
                            }
                            $this->set("directory_list",$directory_list);
                            $this->set("fileList",$fileList);

                            //$this->set("tree_list",$resourceModel->getDirectoryRecourceList());
                            $this->set("networks", $networkModel->getNetworkList());
                           // $resource_networks = $resourceModel->getResourceNetworkList($_GET['resource_id']);
                            //print_r($resource_networks);die;

                    $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
                    $this->_template->addHeaderStyle('filetree.css');    
                    $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
                    $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
                        // $this->_template->addHeaderScript('bootstrap-multiselect.js'); 
                       // $this->_template->addHeaderScript('resource.js');
                    //echo "<pre>";print_r($fileList);die;
                }*/
        
         $resourceModel = new resourceModel();
        $directory_list = array();
        if(isset($_REQUEST['directory_id']) && $_REQUEST['directory_id']>=1)
        {
            $list = $resourceModel->getDirectoryList();
            $parents_childs = $resourceModel->getAllParents($_REQUEST['directory_id']);
            $childs = array_unique($resourceModel->getAllChilds($list,$_REQUEST['directory_id']));
            
            //print_r($childs);
            $child_parents = array_merge($parents_childs,$childs);
            $childs[] = $_REQUEST['directory_id'];
            $list = $resourceModel->getSearchDirectoryList($this->user['role_ids'],$this->user['user_id'],$child_parents);
            $fileList = $resourceModel->getDirectoryRecourceList($this->user['role_ids'],$this->user['user_id'],0,$childs);
            $fileList = $this->getDirectoryFiles($fileList);
           
            $directory_list = $this->buildTree($list,0,'Tree',$fileList);
            $this->set("fileList",$fileList);
            $this->set("directory_id",$_REQUEST['directory_id']);
            //echo "<pre>";  print_r($fileList);
            
        }
            //echo "kkk";  print_r($directory_list);die;
            $this->set("directory_list",$directory_list);
            if(isset($_REQUEST['resource_id']) && isset($_REQUEST['resource_file_id'])) {

                 $resource_detail = $resourceModel->getResourceById($_REQUEST['resource_id'], $_REQUEST['resource_file_id']);
                 $this->set("resource_detail", $resource_detail);
            }elseif (isset($_REQUEST['directory_id'])) {
                
                $resource_detail = $resourceModel->getDirectoryDetails($_REQUEST['directory_id']);
                $this->set("resource_detail", $resource_detail[0]);
            
            }   
                    $this->_template->addHeaderStyle('filetree.css');    
                    $this->_template->addHeaderStyle('common.css');    
                    $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
                    $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
    }
    
    function fileTreeAction(){
        
        $resourceModel = new resourceModel();
        $directory_list = array();
        $diretory_id = 0;
        if(isset($_REQUEST['resource_id']) && $_REQUEST['resource_id']>=1)
        {
            $list = $resourceModel->getDirectoryList();
            $diretories= $resourceModel->getResourceDirectoryList($_REQUEST['resource_id']);
            if(!empty($diretories)) {
              $diretory_id = $diretories[0]['directory_id'];
                
            }
           // print_r($diretory_id);die;
            
            $parents_childs = $resourceModel->getAllParents($diretory_id);
            $childs = array_unique($resourceModel->getAllChilds($list,$diretory_id));
            $child_parents = array_merge($parents_childs,$childs);
            $list = $resourceModel->getSearchDirectoryList($this->user['role_ids'],$this->user['user_id'],$child_parents);
            $fileList = $resourceModel->getDirectoryRecourceList($this->user['role_ids'],$this->user['user_id']);
            $fileList = $this->getDirectoryFiles($fileList);
           
            $directory_list = $this->buildTree($list,0,'Tree',$fileList);
            $this->set("fileList",$fileList);
            //echo "<pre>";  print_r($fileList);
            
        }
            //echo "kkk";  print_r($directory_list);die;
            $this->set("directory_list",$directory_list);
            if(isset($_REQUEST['resource_id']) && isset($_REQUEST['resource_file_id'])) {

                 $resource_detail = $resourceModel->getResourceById($_REQUEST['resource_id'], $_REQUEST['resource_file_id']);
                 $this->set("resource_detail", $resource_detail);
            }elseif (isset($_REQUEST['directory_id'])) {
                
                $resource_detail = $resourceModel->getDirectoryDetails($_REQUEST['directory_id']);
                $this->set("resource_detail", $resource_detail[0]);
            
            }   
                    $this->_template->addHeaderStyle('filetree.css');    
                    $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
                    $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
        
    }
    function createFolderTreeAction() {
        
        $resourceModel = new resourceModel();
        $directory_list = array();
        if(isset($_REQUEST['directory_id']) && $_REQUEST['directory_id']>=1)
        {
            $list = $resourceModel->getDirectoryList();
            $directory_list = $this->buildTree($list,0);
            
        }else{
            
            $list = $resourceModel->getResourceParentDirectoryList($this->user['role_ids'],$this->user['user_id']);
            $fileList = $resourceModel->getDirectoryRecourceList($this->user['role_ids'],$this->user['user_id']);
            $fileList = $this->getDirectoryFiles($fileList);
            //print_r($fileList);
            $directory_list = $this->buildTree($list,0,'Tree',$fileList);
        }
            //echo "kkk";  print_r($directory_list);die;
            $this->set("directory_list",$directory_list);
            if(isset($_REQUEST['resource_id']) && isset($_REQUEST['resource_file_id'])) {

                 $resource_detail = $resourceModel->getResourceById($_REQUEST['resource_id'], $_REQUEST['resource_file_id']);
                 $this->set("resource_detail", $resource_detail);
            }elseif (isset($_REQUEST['directory_id'])) {
                
                $resource_detail = $resourceModel->getDirectoryDetails($_REQUEST['directory_id']);
                $this->set("resource_detail", $resource_detail[0]);
            
            }   
        
        
    }
    function searchResourceFilesAction() {
        
        
        $resourceModel = new resourceModel();
        
        $directory_list = array();
       // echo"d". $_REQUEST['search_val'];
        $serachVal = !empty($_REQUEST['search_val'])?trim($_REQUEST['search_val']):'';
        $cPage=empty($_POST['page'])?1:$_POST['page'];
        if(empty($serachVal))
        {
            //echo "deepak";die;
            
        }else{
            
                // $resourceList = $resourceModel->getResources($this->user['role_ids'], $param,$this->user['user_id']);
            
                 $filesData = $resourceModel->getAllFilesByName($serachVal);
                //echo $resourceModel->getPageCount();
                if (empty($filesData)) {
                    $this->_notPermitted = 1;
                } else {
                    
                    
                    //echo "kkkk";
                    //print_r($assessmentModel->getPageCount());die;
                    $this->set("filedata",$filesData);
                   // $this->set("pages",$resourceModel->getPageCount());
                    $this->set("pages",5);
                    $this->set("cPage",$cPage);
                    $this->set("searchValue",$serachVal);
                   // $this->_template->addHeaderStyle('filetree.css');    
                    //$this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
                    //$this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
            
            
               // $filesData = $resourceModel->getAllFilesByName($serachVal);
           // $serachVal = preg_replace("/[^a-zA-Z0-9]/", "", $serachVal);
                }

           
        }
            //echo "kkk";  print_r($directory_list);die;
           
    }
    
    /*
     * 
     */
    function resourceListAction() {
      
                $resourceList = array();
                $resourceModel = new resourceModel();
                $networkModel = new networkModel();
                $cPage = empty($_POST['page']) ? 1 : $_POST['page'];
                $order_by = empty($_POST['order_by']) ? "name" : $_POST['order_by'];
                $order_type = empty($_POST['order_type']) ? "asc" : $_POST['order_type'];
                $param = array(
                 "page" => $cPage,
                 "order_by" => $order_by,
                 "order_type" => $order_type
                );
                $resourceList = $resourceModel->getResources($this->user['role_ids'], $param,$this->user['user_id']);
                if (empty($resourceList)) {
                    $this->_notPermitted = 1;
                } else {
                        
                        $serachVal =  empty($_POST['search_val']) ? '' : $_POST['search_val'];
                        $searchResource =  empty($_POST['searchResource']) ? '' : $_POST['searchResource'];
                        $searchFor =  empty($_POST['search_for']) ? '' : $_POST['search_for'];
                        if($searchResource == 1 && !empty($serachVal) && !empty($searchFor)) {

                            if($searchFor == 1) {
                              $filesData = $resourceModel->getAllFilesByName($serachVal,$cPage);
                            
                            }else if($searchFor == 2){
                                
                                $filesData = $resourceModel->getAllFolderByName($serachVal,$cPage); 
                            }
                            //echo "<pre>";print_r($filesData);
                              $this->set("filedata",$filesData);
                            //$resourceModel->getPageCount();
                            $this->set("pages",$resourceModel->getPageCount());
                            $this->set("cPage",$cPage);
                           // $this->set("pages",5);
                             $this->set("searchResource",1);
                         

                        }else {

                            $resource_networks = array();
                            $this->set("succMsg", empty($_GET['succMsg']) ? 0 : $_GET['succMsg']);
                            $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
                            $this->set("roles", $this->userModel->getRoles());
                            $this->set("resource_detail", $resourceList);
                            ////$this->set("networks",$networkModel->getNetworkList());
                            $list = $resourceModel->getUserDirectoryList($this->user['role_ids'],$this->user['user_id']);


                           // $fileList = $resourceModel->getDirectoryRecourceList();
                            $fileList = $resourceModel->getDirectoryRecourceList($this->user['role_ids'],$this->user['user_id']);
                            $logList = $resourceModel->getRecourceLogList();
                            if(!empty($logList))
                                 $logList = array_column($logList, 'resource_id');
                            //echo "<pre>";print_r($fileList);
                            $fileList = $this->getDirectoryFiles($fileList);
                            //echo "<pre>";print_r($fileList);
                            //echo"</pre>";
                            //die;
                           // echo"<pre>"; print_r($this->buildTree($list,0,'Tree',$fileList));die;
                            $directory_list = $this->buildTree($list,0,'Tree',$fileList);
                            if (empty($fileList)&& (!in_array(1, $this->user['role_ids']) && !in_array(2, $this->user['role_ids']) && !in_array(8, $this->user['role_ids']))) {
                                $this->set("is_resource",1);
                            }
                            $this->set("directory_list",$directory_list);
                            $this->set("fileList",$fileList);
                            $this->set("logList",$logList);
                            //$this->set("tree_list",$resourceModel->getDirectoryRecourceList());
                            $this->set("networks", $networkModel->getNetworkList());
                           // $resource_networks = $resourceModel->getResourceNetworkList($_GET['resource_id']);
                            //print_r($resource_networks);die;

                        }
                     
                        $this->set("searchValue",$serachVal);
                       
                        $this->set("searchFor",$searchFor);
                        $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
                        $this->_template->addHeaderStyle('filetree.css');    
                        $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
                        $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
                        // $this->_template->addHeaderScript('bootstrap-multiselect.js'); 
                        // $this->_template->addHeaderScript('resource.js');
                        //echo "<pre>";print_r($fileList);die;
                }
    }

    /*
     * Des : function to create resource directory
     * Author : Deepak
     * Input : Directory Name,Parent Directory ID
     */

    function createResourceDirectoryAction() {
        if (!in_array("upload_resources", $this->user['capabilities'])) {
            $this->_notPermitted = 1;
        } else {
            $resourceModel = new resourceModel();
            $networkModel = new networkModel();
            $list = $resourceModel->getResourceParentDirectoryList($this->user['role_ids'],$this->user['user_id']);
            $this->set("directory_list",$this->buildTree($list,0));
            $this->set("networks", $networkModel->getNetworkList());
            $this->set("provinces", $networkModel->getProvinceList());
           // $this->set("folder_detail", $resourceModel->getDirectoryDetails($_GET['directory_id']));
            $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
            $this->set("roles", $this->userModel->getRoles());
            
            $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
            $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
             $this->_template->addHeaderStyle('bootstrap-multiselect.css');    
             //$this->_template->addHeaderStyle('bootstrap.min.css');    
            $this->_template->addHeaderScript('bootstrap-multiselect.js'); 
            $this->_template->addHeaderScript('bootstrap.min.js'); 
            $this->_template->addHeaderScript('resource.js');            
            $this->_template->addHeaderStyle('filetree.css');    
            $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
            $this->_template->addHeaderStyle('bootstrap-tagsinput.css');
            $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
            $this->_template->addHeaderScript('bootstrap-tagsinput.min.js');
            $this->_template->addHeaderScript('bootstrap-tagsinput.js');
        }
    }
    /*
     * Des : function to list all resource directory
     * Author : Deepak
     */

    function listResourceDirectoryAction() {
        if (!in_array("upload_resources", $this->user['capabilities'])) {
            $this->_notPermitted = 1;
        } else {
            $resourceModel = new resourceModel();
            $list = $resourceModel->getResourceParentDirectoryList();
            $this->set("directory_list",$this->buildTree($list,0));
            $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
            $this->set("roles", $this->userModel->getRoles());
            
            $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
            $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
        }
    }

    public function buildTree(array &$elements, $parentId = 0 , $tree = '', $fileList = array()) {

        $branch = array();
        foreach ($elements as $element) {
            if ($element['ParentCategoryId'] == $parentId) {
                
                //$directoryFile = $this->getDirectoryFiles($element['directory_id'], $fileList);
                $children = $this->buildTree($elements, $element['directory_id'],$tree,$fileList);
                if ($children) {
                    $element['children'] = $children;
                }
                /*if(isset($fileList[$element['directory_id']]) && count($fileList[$element['directory_id']])) {
                    $element['files'] = $fileList[$element['directory_id']];
                }*/
                $branch[$element['directory_id']] = $element;
            }
        }
        return $branch;
    }   
    
    /*
     * 
     */
   public function getDirectoryFiles($fileList){
        
            $list = array();
            //echo "<pre>";  PRINT_R($fileList);die;
            $new_directory_id = 0;
            $list = array();
            $list_flag = 0;
             foreach($fileList as $files) {
                 if(array_key_exists($files['directory_id'],$list)) {
                     $list[$files['directory_id']][] = $this->prepareValues($files);
                    
                 }else {
                     $list[$files['directory_id']][] = $this->prepareValues($files);
                   
                }
            }
            return $list;
   }
   
   function prepareValues($files){
                     $file = '';
                     if(!empty($files['file'])) {
                        $file = $files['file'];
                    }else {
                         $file = $this->getFileName($files['file_name']);
                         $files['file_name'] = $files['file_name'];
                    }
                    if(!empty($files['file_size'])) 
                        $files['file_size'] = $this->calculateSize($files['file_size']);
                    return array(
                        "directory_id"=>$files['directory_id'],
                        "file_name"=>$files['file_name'],                        
                        "file_id"=>$files['file_id'],
                        "resource_id"=>$files['resource_id'],
                        "resource_title"=>$files['resource_title'],
                        "resource_link_type"=>$files['resource_link_type'],
                        "resource_url"=>$files['resource_url'],
                        "resource_file_id"=>$files['resource_file_id'],
                        "status"=>$files['status'],
                        "file"=>$file,
                        "file_size"=>$files['file_size'],
                        "upload_date"=>$files['upload_date'],
                         //"uploaded_by"=>$files['uploaded_by']
                        "user_name"=>$files['user_name']
                    );
   }
   
   function getFileName($file_name) {
       
       if(!empty($file_name)) {
           
           $file_name_arr = explode("_", $file_name);
           $file_ext = $file_name_arr[count($file_name_arr)-1];
           $file_ext = substr($file_ext,strpos($file_ext,".")+1);
           $file_name_arr = array_slice($file_name_arr, 0,count($file_name_arr)-2);
           return implode('_', $file_name_arr).".".$file_ext;
           //print_r($file_name_arr);die;
       }
   }
   
   function calculateSize($bytes) {
       
     
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
   }
   /*
    * function for create popup to edit folder (directory)
    */
   function editFolderAction() {
        if (!in_array("upload_resources", $this->user['capabilities'])) {
            $this->_notPermitted = 1;
        } else {
            $directory_id = $_REQUEST['directory_id'];
            $resourceModel = new resourceModel();
            $folderDetails = $resourceModel->getDirectoryDetails($directory_id);
            $list = $resourceModel->getResourceParentDirectoryList($this->user['role_ids'],$this->user['user_id']);
            $this->set("directory_details", $folderDetails[0]);
            $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
            $this->set("roles", $this->userModel->getRoles());

            $this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
            $this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
        }
    }
    
    function forcedDownloadAction(){
        $this->_render = false;
        $file_id = isset($_GET['file_id'])?$_GET['file_id']:0;
        $file_name = isset($_GET['d_file'])?$_GET['d_file']:0;
        $resource_id = isset($_GET['resource_id'])?$_GET['resource_id']:0;
        
        $resourceModel = new resourceModel();
        if(!empty($file_id) && !empty($file_name)) {
            $fileDetails = $resourceModel->getFileDetails($file_id);
            $fileExt = explode('.', $fileDetails['file_name']);
            $ext = end($fileExt);
            $file_name .= ".".$ext;
             //print_r($fileExt);die;
            $file="".UPLOAD_URL_RESOURCE."".$fileDetails['file_name']."";

            DownloadAnything($file, $file_name, '', true);
            if(!empty($resource_id)) {                
                //echo "<pre>";print_r($this->user);
                $resourceModel->saveResourceDownloadLog($resource_id,$this->user['user_id']); 
                
            }

        }
        

    }
    

}

?>