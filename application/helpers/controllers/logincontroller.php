<?php
class loginController extends Controller{
	
	function loginAction(){
		$this->userModel->a();
		if(isset($_POST['_action']) && $_POST['_action']="login"){
			$res=$this->userModel->authenticateUser($_POST['email'],$_POST['password']);
			if(isset($res['user_id']) && $token=$this->userModel->generateToken($res['user_id'], $_POST['email'])){
				setcookie('ADH_TOKEN',$token);
				$this->_redirect(empty($_GET['redirect'])?SITEURL:urldecode($_GET['redirect']));
			}else{
				$this->set("error","Wrong username or password");
			}
		}
		
		$this->_template->clearHeaderFooter();
		$this->_template->addHeaderStyle("nui-login.css");
		$this->_template->addHeaderStyle("bootstrap.min.css");
		$this->_template->addHeaderStyle("font-awesome.min.css");
		$this->_template->addHeaderStyle("bootstrap-social.css");
	}
	
	function logoutAction(){
		$token=empty($_COOKIE['ADH_TOKEN'])?"":$_COOKIE['ADH_TOKEN'];
		if($token!=""){
			setcookie('ADH_TOKEN','');
			$this->userModel->logoutUser($token);
		}
		$this->_redirect(createUrl(array("controller"=>"login","action"=>"login")));
	}
}