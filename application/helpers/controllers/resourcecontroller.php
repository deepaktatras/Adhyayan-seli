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
            $list = $resourceModel->getResourceParentDirectoryList();
            $this->set("directory_list",$this->buildTree($list,0));
            $this->set("networks", $networkModel->getNetworkList());
            $this->set("provinces", $networkModel->getProvinceList());
            $this->set("provinces", $resourceModel->getResourceParentDirectoryList());
            $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
            $this->set("roles", $this->userModel->getRoles());
        }
    }

    function editResourceAction() {
        if (!in_array("upload_resources", $this->user['capabilities'])) {
            $this->_notPermitted = 1;
        } else {
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
                    //$this->set("networks",$networkModel->getNetworkList());
                    $list = $resourceModel->getResourceParentDirectoryList();
                    $this->set("directory_list",$this->buildTree($list,0));
                    $this->set("networks", $networkModel->getNetworkList());
                    $resource_networks = $resourceModel->getResourceNetworkList($_GET['resource_id']);
                    //print_r($resource_networks);die;
                    $network_provinces = array();
                    $resource_provinces = array();
                    $provinces_schools = array();
                    $resource_schools = array();
                    $resource_users = array();
                    $schools_users = array();
                    $network_ids = '';
                    $provinces_ids = '';
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
                        }else
                            $provinces_schools = $networkModel->getSchoolsByNetwork($network_ids);;
                        //if($network_provinces)
                        //print_r($resource_schools);
                    }
                   // }
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
                }
            }
        }
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
                $resourceList = $resourceModel->getResources($this->user['role_ids'], $param);
               
               //echo "<pre>"; print_r($resourceList);
                if (empty($resourceList)) {
                    $this->_notPermitted = 1;
                } else {
                    
                    $resource_networks = array();
                    $this->set("succMsg", empty($_GET['succMsg']) ? 0 : $_GET['succMsg']);
                    $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
                    $this->set("roles", $this->userModel->getRoles());
                    $this->set("resource_detail", $resourceList);
                    ////$this->set("networks",$networkModel->getNetworkList());
                    $list = $resourceModel->getResourceParentDirectoryList();
                     $fileList = $resourceModel->getDirectoryRecourceList();
                   // echo "<pre>";print_r($fileList);
                    $this->set("directory_list",$this->buildTree($list,0,'Tree',$fileList));
                    
                    //$this->set("tree_list",$resourceModel->getDirectoryRecourceList());
                    $this->set("networks", $networkModel->getNetworkList());
                   // $resource_networks = $resourceModel->getResourceNetworkList($_GET['resource_id']);
                    //print_r($resource_networks);die;
                    $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
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
            $list = $resourceModel->getResourceParentDirectoryList();
            $this->set("directory_list",$this->buildTree($list,0));
            $this->set("isPop", empty($_GET['ispop']) ? 0 : $_GET['ispop']);
            $this->set("roles", $this->userModel->getRoles());
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
        }
    }

    public function buildTree(array &$elements, $parentId = 0 , $tree = '', $fileList = array()) {

        $branch = array();
        foreach ($elements as $element) {
            if ($element['ParentCategoryId'] == $parentId) {
                
                $directoryFile = $this->getDirectoryFiles($element['directory_id'], $fileList);
                $children = $this->buildTree($elements, $element['directory_id'],$tree,$fileList);
                if ($children) {
                    $element['children'] = $children;
                }
                if(count($directoryFile)) {
                    $element['files'] = $directoryFile;
                }
                $branch[$element['directory_id']] = $element;
            }
        }
        return $branch;
    }   
    
    /*
     * 
     */
   public function getDirectoryFiles($directory_id, $fileList){
        
            $list = array();
            foreach($fileList as $files) {
                
                if($directory_id ==  $files['directory_id']) {
                    
                    $list[] =  array(
                        "file_name"=>$files['file_name'],
                        "file_id"=>$files['file_id'],
                        "resource_id"=>$files['resource_id'],
                        "resource_file_id"=>$files['resource_file_id'],
                        "status"=>$files['status']
                    );
                }
                
            }
            return $list;
   }
}

?>