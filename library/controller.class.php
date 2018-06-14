<?php
class Controller {

	protected $_model;
	protected $_controller;
	protected $_action;
	protected $_template;
	protected $_ajaxRequest=0;
	protected $_notPermitted=0;
	protected $_is404=0;
	protected $_isPDF =0;
	
	protected $userModel;
	protected $db;
	
	protected $user=false;
	
	protected $_render=true;
	
	protected $_ajaxResponse=array("status"=>1,"content"=>"","message"=>"");
	

	function __construct( $controller, $action, $ajaxRequest=0,$isPDF=0) {
		$this->db=db::getInstance();
		$this->_controller = $controller;
		$this->_action = $action;
		$this->_ajaxRequest=$ajaxRequest;
		$this->_isPDF=$isPDF;
		$this->userModel=new userModel();
                $this->userModel->setmaxconcatlength();
                //echo $_COOKIE['ADH_TOKEN'];
                //die();
                //echo"ddddd";
                //echo"ddd";
                $token_refresh=isset($_COOKIE['ADH_TOKEN_REFRESH'])?$_COOKIE['ADH_TOKEN_REFRESH']:'';
                $token=isset($_COOKIE['ADH_TOKEN'])?$_COOKIE['ADH_TOKEN']:'';
                
                if((empty($token_refresh) || empty($token)) && $controller!="login"){
                    $redirect=SITEURL."index.php?redirected=1";
                    if(!empty($_GET))
					foreach($_GET as $k=>$v)
						$redirect.="&$k=$v";
                 $this->_redirect(createUrl(array("controller"=>"login","action"=>"logout","redirect"=>urlencode($redirect))));   
                }elseif(!$this->userModel->userExist($token_refresh) && $controller!="login" ){
                    $redirect=SITEURL."index.php?redirected=1";
                    if(!empty($_GET))
					foreach($_GET as $k=>$v)
						$redirect.="&$k=$v";
                 $this->_redirect(createUrl(array("controller"=>"login","action"=>"logout","redirect"=>urlencode($redirect)))); 
                }
                
                try{
                 $token=isset($_COOKIE['ADH_TOKEN'])?$_COOKIE['ADH_TOKEN']:'';   
                 $decoded = Firebase\JWT\JWT::decode($token, PUBLICKEY, array('RS256'));
                 $decoded_array = (array) $decoded;
                 //Firebase\JWT\JWT::refresh();
                 //print_r($decoded_array);
                 
                 //$decoded_array['exp']=time+60;
                 //die();
                  //$new_array=$decoded_array;
                  //$new_array['exp']=time()+60;
                  
                  //$jwt_token=Firebase\JWT\JWT::encode($new_array, PRIVATEKEY, 'RS256');
                  //setcookie('ADH_TOKEN',$jwt_token);
                 
                 /*if(count($this->userModel->checkBacklistJWT($token))>0){
                 $this->_redirect(createUrl(array("controller"=>"login","action"=>"login")));    
                 }*/
                  
		if((!isset($_COOKIE['ADH_TOKEN']) || !$this->user=(array) $decoded_array['user']) && $controller!="login"){						
		    //print_r($this->user);	
                    
		}else if($this->user && $controller=="login" && $action!="logout"){
		    $this->_redirect(SITEURL);
		}
                
                }catch (Exception $e) {
                    //$this->_redirect(createUrl(array("controller"=>"login","action"=>"login","redirect"=>urlencode($redirect))));
                    //$this->_redirect(createUrl(array("controller"=>"login","action"=>"logout","redirect"=>urlencode($redirect))));
	           //echo $e->getMessage();
                   //die();
                    /*try{
                     $token_refresh=isset($_COOKIE['ADH_TOKEN_REFRESH'])?$_COOKIE['ADH_TOKEN_REFRESH']:'';
                     $decoded_refresh_array = Firebase\JWT\JWT::decode($token_refresh, PUBLICKEY, array('RS256'));
                     $decoded_refresh_array = (array) $decoded_refresh_array;
                     $refresh_t=$decoded_refresh_array['refresh_token'];
                     $refresh_exp=$decoded_refresh_array['exp'];
                     
                     if(count($this->userModel->checkBacklistJWT($token_refresh))>0){
                     $this->_redirect(createUrl(array("controller"=>"login","action"=>"login")));    
                     }*/
                      $token_refresh=isset($_COOKIE['ADH_TOKEN_REFRESH'])?$_COOKIE['ADH_TOKEN_REFRESH']:'';
                     if((!isset($_COOKIE['ADH_TOKEN_REFRESH']) || !$this->user=$this->userModel->checkToken($_COOKIE['ADH_TOKEN_REFRESH'])) && $controller!="login"){						
                         //echo"Yes";
                         //die();
                         if($ajaxRequest==1){
				$this->_render=false;
				$this->_ajaxResponse['status']=-1;
				exit;
			}else{
				$redirect=SITEURL."index.php?redirected=1";
				if(!empty($_GET))
					foreach($_GET as $k=>$v)
						$redirect.="&$k=$v";
				//$this->_redirect(createUrl(array("controller"=>"login","action"=>"login","redirect"=>urlencode($redirect))));
				
				if(isset($_REQUEST['process']) && $_REQUEST['process']=='invite' && $_REQUEST['id']!=''){
					$this->_render=true;
					$this->_ajaxResponse['status']=1;
//                                    exit;
				} else {
                                        //echo"No";
                                        //die();
					$this->_redirect(createUrl(array("controller"=>"login","action"=>"login","redirect"=>urlencode($redirect))));
				}
			}
                         
                     }else if($this->user && $action!="logout"){
                                //echo"dds";
                                //die();
                                $time=time();
                                $issuedAt   = $time;
                                $notBefore  = $issuedAt; //Adding 10 seconds
                                $expire     = $notBefore + TOKEN_LIFE_REFRESH;
                                $refresh    = $notBefore + TOKEN_LIFE;
                                
                                $jwt_token = array(
                                "iss" => "adhyayan.asia",
                                "jti" => $_COOKIE['ADH_TOKEN_REFRESH'],
                                "iat" => $issuedAt,
                                "nbf" => $notBefore,
                                "exp" => $expire,
                                "user" => $this->user   
                                );
                                
                                $jwt_token=Firebase\JWT\JWT::encode($jwt_token, PRIVATEKEY, 'RS256');
                                setcookie('ADH_TOKEN',$jwt_token);
                                if(COOKIE_GEN==1){
                                setcookie('ADH_TOKEN',$jwt_token, 0 , '/',COOKIE_DOMAIN);
                                }
                                $this->_redirect(SITEURL);
                                
                                //echo "Encode:\n" . print_r($jwt_token, true) . "\n";
                                //die();
                                //$decoded = Firebase\JWT\JWT::decode($jwt_token, PUBLICKEY, array('RS256'));
                                //$decoded_array = (array) $decoded;
                                //echo"<pre>";
                                //echo "Decode:\n" . print_r($decoded_array, true) . "\n";
                                //echo"</pre>";
                                //die();
                                
                                /*$jwt_token_refresh = array(
                                "iss" => "adhyayan.asia",
                                "jti" => $refresh_t,     
                                "iat" => $issuedAt,
                                "nbf" => $notBefore,
                                "exp" => $refresh,
                                "refresh_token" => $refresh_t   
                                );
                                
                                $jwt_token_refresh=Firebase\JWT\JWT::encode($jwt_token_refresh, PRIVATEKEY, 'RS256');
                                $jwt_token=Firebase\JWT\JWT::encode($jwt_token, PRIVATEKEY, 'RS256');
                                
                                setcookie('ADH_TOKEN',$jwt_token);
                                setcookie('ADH_TOKEN_REFRESH',$jwt_token_refresh);
                                
                                if($this->userModel->addBacklistJWT($token_refresh,$refresh_exp) && $this->userModel->deleteBacklistJWT()){
                                $this->_redirect(SITEURL);
                                //die();    
                                }else{
                                 echo"Problem in tokens.Please contact Administartor";
                                 die();
                                }*/
		    
                     }
                     //echo"dsdsd";
                 
                    /*} catch (Exception $ex) {
                     
                    // echo $ex;
                     //die();
                        
                     if($controller!="login"){
                     if($ajaxRequest==1){
				$this->_render=false;
				$this->_ajaxResponse['status']=-1;
				exit;
			}else{
				$redirect=SITEURL."index.php?redirected=1";
				if(!empty($_GET))
					foreach($_GET as $k=>$v)
						$redirect.="&$k=$v";
				//$this->_redirect(createUrl(array("controller"=>"login","action"=>"login","redirect"=>urlencode($redirect))));
				
				if(isset($_REQUEST['process']) && $_REQUEST['process']=='invite' && $_REQUEST['id']!=''){
					$this->_render=true;
					$this->_ajaxResponse['status']=1;
//                                    exit;
				} else {
                                        //echo createUrl(array("controller"=>"login","action"=>"login","redirect"=>urlencode($redirect)));
					$this->_redirect(createUrl(array("controller"=>"login","action"=>"login","redirect"=>urlencode($redirect))));
				}
			}
                     }
                        
                    }*/
                    
                    
                }
                
                

		$this->_template = new Template($controller,$action);
		if($ajaxRequest!=1 && $isPDF!=1)
			$this->initializeHeader();
		$this->_template->set("ajaxRequest",$ajaxRequest);
		$this->_template->set("user",$this->user);
                $this->setLanguage();
	}
	
	function initializeHeader(){
		$this->_template->addHeaderStyle("bootstrap.min.css");
		$this->_template->addHeaderStyle("font-awesome.min.css");
		$this->_template->addHeaderStyle('bootstrap-datetimepicker.min.css');
		$this->_template->addHeaderStyle("common.css");
		$this->_template->addHeaderStyle("custom.css");
		$this->_template->addHeaderStyle("malay.css");
		$this->_template->addHeaderScript("jquery.js");
		$this->_template->addHeaderScript('jquery.maskedinput.js');
		$this->_template->addHeaderScript('moment.min.js');
		$this->_template->addHeaderScript('bootstrap-datetimepicker.min.js');
		$this->_template->addHeaderScript("script.js");
		$this->_template->addHeaderScript("bootstrap.min.js");
		//$this->_template->addFooterScript('nui.js');
		$this->_template->addHeaderScript('typeahead.bundle.js');
		//$this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
                if($this->_controller!="actionplan"){
		$this->_template->addHeaderScript('bootstrap-select.min.js');
                }
		$this->_template->addHeaderScript('jquery.ui.min.js');
		//$this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
	}

	function set($name,$value) {
		$this->_template->set($name,$value);
	}

	function __destruct() {
		if($this->_isPDF==1);
		else if($this->_ajaxRequest!=1){
			if($this->_render){
				$this->set("notPermitted",$this->_notPermitted);
				$this->set("is404",$this->_is404);
				$this->_template->render();
			}
		}else{
			if($this->_render){
				$this->set("notPermitted",$this->_notPermitted);
				$this->set("is404",$this->_is404);
				ob_start();
				$this->_template->render();
				$this->_ajaxResponse['content']=ob_get_contents();
				ob_end_clean();
			}
			echo json_encode($this->_ajaxResponse);
		}
	}
	
	function _redirect($url){
		$this->_render=false;
		header("location: $url");
		exit;
	}
        function setLanguage(){
		$http_accept_lang = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$langChars = explode('_',$http_accept_lang);
		$http_accept_lang = $langChars[0];
		if(isset($_COOKIE['ADH_LANGCODE']))
		 	$lang_code = $_COOKIE['ADH_LANGCODE'];
		 else
                                $lang_code = 'all';
                 $expire = time()+90*24*60*60;		
                 if($lang_code != 'all'){
		 		 		
		 		$lang = $this->userModel->getLanguageByCode($lang_code);
                 }else
                    $lang = $lang_code;
                    $this->user['lang'] = $lang?$lang:9;//english if none is found
                    setcookie('ADH_LANG',$lang,$expire);
                    $_COOKIE['ADH_LANG']=$lang;
	}
}
