<?php
class communicationController extends controller{
    
    // function for getting user list whose tap admin communicated with them
    public function communicationAction(){
        $objCommunication = new communicationModel();
        if(in_array(8,$this->user['role_ids'])){
            $cPage=empty($_POST['page'])?1:$_POST['page'];
            $order_by=empty($_POST['order_by'])?"date":$_POST['order_by'];
            $order_type=empty($_POST['order_type'])?"desc":$_POST['order_type'];
            $param=array(
                "page"=>$cPage,
                "name_like"=>empty($_POST['name'])?"":$_POST['name'],
                "client_id"=>empty($_POST['client_id'])?0:$_POST['client_id'],
                "client_like"=>empty($_POST['client'])?"":$_POST['client'],
                "fdate_like"=>empty($_POST['fdate'])?"":$_POST['fdate'],
                "edate_like"=>empty($_POST['edate'])?'':$_POST['edate'],
                "order_by"=>$order_by,
                "order_type"=>$order_type
            );
            $userList =$objCommunication->getCommunicateUsers($param);
            $this->set("pages",$this->userModel->getPageCount());
            $this->set("filterParam",$param);
            $this->set("users",$userList);
            $this->set("cPage",$cPage);
            $this->set("orderBy",$order_by);
            $this->set("orderType",$order_type);
        } else {
            $this->_notPermitted=1;
        }
    }
    
}
