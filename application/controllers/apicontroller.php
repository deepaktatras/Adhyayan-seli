<?php
class apiController extends controller {
	protected $apiResult;
	protected $_postData;
	function __construct($controller, $action) {
		//error_reporting(E_ALL);
		//ini_set('display_errors','On');
		$this->db = db::getInstance ();
		
		$this->_controller = $controller;
		
		$this->_action = $action;
		
		$this->apiResult = array (
				"status" => 0,
				"message" => "" 
		);
		//echo "a".$_POST ['process'];
		$this->userModel = new userModel ();
		
		if ($action != "login" && ! isset ( $_POST ['process'] )) {
			
			$this->checkToken ();
		}
	}
	function __destruct() {
		echo json_encode ( $this->apiResult );
		
		exit ();
	}
	protected function checkToken() {
                
                $token_refresh=isset($_COOKIE['ADH_TOKEN_REFRESH'])?$_COOKIE['ADH_TOKEN_REFRESH']:(isset($_POST ['token_refresh'])?$_POST ['token_refresh']:'');
                $token=isset($_POST ['token'])?$_POST ['token']:''; 
                if((empty($token_refresh) || empty($token))){
                        $this->apiResult ["message"] = "Token missing or expired. Please re-login";
			$this->apiResult ["status"] = - 1;
			exit ();
			 
                }elseif(!$this->userModel->userExist($token_refresh)){
                        $this->apiResult ["message"] = "Token missing or expired. Please re-login";
			$this->apiResult ["status"] = - 1;
			exit ();
			        
                }
                
                try{
                 $token=isset($_POST ['token'])?$_POST ['token']:'';   
                 $decoded = Firebase\JWT\JWT::decode($token, PUBLICKEY, array('RS256'));
                 $decoded_array = (array) $decoded;
                 $this->user = (array) $decoded_array['user'];
                 
                  //$new_array=$decoded_array;
                  //$new_array['exp']=time()+60;
                  //$jwt_token=Firebase\JWT\JWT::encode($new_array, PRIVATEKEY, 'RS256');
                  //setcookie('ADH_TOKEN',$jwt_token);
                 
                }catch (Exception $e) {
                        /*try{
                        $token_refresh=isset($_COOKIE['ADH_TOKEN_REFRESH'])?$_COOKIE['ADH_TOKEN_REFRESH']:'';
                        $decoded_refresh_array = Firebase\JWT\JWT::decode($token_refresh, PUBLICKEY, array('RS256'));
                        $decoded_refresh_array = (array) $decoded_refresh_array;
                        $refresh_t=$decoded_refresh_array['refresh_token'];
                        $refresh_exp=$decoded_refresh_array['exp'];
                        */
                        $adh_token_refresh=isset($_COOKIE['ADH_TOKEN_REFRESH'])?$_COOKIE['ADH_TOKEN_REFRESH']:$_POST ['token_refresh'];         
                       //echo $_COOKIE['ADH_TOKEN_REFRESH'] ;
                       if (! isset ( $adh_token_refresh ) || ! $this->user = $this->userModel->checkToken ( $adh_token_refresh )) {
			$this->apiResult ["message"] = "Token missing or expired. Please re-login";
			$this->apiResult ["status"] = - 1;
			exit ();
			
		       } else{
                           
                                $time=time();
                                $issuedAt   = $time;
                                $notBefore  = $issuedAt; //Adding 10 seconds
                                $expire     = $notBefore + TOKEN_LIFE_REFRESH;
                                $refresh    = $notBefore + TOKEN_LIFE;
                                
                                $jwt_token = array(
                                "iss" => "adhyayan.asia",
                                "jti" => $adh_token_refresh,
                                "iat" => $issuedAt,
                                "nbf" => $notBefore,
                                "exp" => $expire,
                                "user" => $this->user   
                                );
                                
                                /*$jwt_token_refresh = array(
                                "iss" => "adhyayan.asia",
                                "jti" => $refresh_t,     
                                "iat" => $issuedAt,
                                "nbf" => $notBefore,
                                "exp" => $refresh,
                                "refresh_token" => $refresh_t   
                                );*/
                                
                                //$jwt_token_refresh=Firebase\JWT\JWT::encode($jwt_token_refresh, PRIVATEKEY, 'RS256');
                                $jwt_token=Firebase\JWT\JWT::encode($jwt_token, PRIVATEKEY, 'RS256');
                                
                                setcookie('ADH_TOKEN',$jwt_token);
                                if(COOKIE_GEN==1){
                                setcookie('ADH_TOKEN',$jwt_token, 0 , '/',COOKIE_DOMAIN);
                                }
                                //setcookie('ADH_TOKEN_REFRESH',$jwt_token_refresh);
                                
                                //if($this->userModel->addBacklistJWT($token_refresh,$refresh_exp) && $this->userModel->deleteBacklistJWT()){
                                //$this->_redirect(SITEURL);
                                //die();    
                                //}else{
                                   //$this->apiResult ["message"] = "Token missing or expired. Please re-login";
			           //$this->apiResult ["status"] = - 1;
			           //exit ();
                                //}
                           
                           
                       }
                        
                        
                        /*} catch (Exception $ex) {
                                
                        $this->apiResult ["message"] = "Token missing or expired. Please re-login";
			$this->apiResult ["status"] = - 1;
			exit ();
                        
                        }*/   
                    
		//if (! isset ( $_POST ['token'] ) || ! $this->user = $this->userModel->checkToken ( $_POST ['token'] )) {
			
			
		//}
                        
                
                }
                                
	}
	
	function apiAction() {
		
	}
        
        /*public function getCurrentUserAction() {
            
            $this->apiResult['data'] = array(
                'email' => $this->user['email'],
                'name' => $this->user['name']
            );
            
            $this->apiResult ["status"] = 1;
        }*/
        
        public function getCurrentUserAction() {
            //$adh_token_refresh=isset($_COOKIE['ADH_TOKEN_REFRESH'])?$_COOKIE['ADH_TOKEN_REFRESH']:$_POST ['token_refresh'];
            
            $this->apiResult['data'] =$this->user;
            
            $this->apiResult ["status"] = 1;
        }
        
	function loginAction() {
                
                $this->apiResult ["confirmstatus"] = 1;                
		if (empty ( $_POST ['email'] ))
			
			$this->apiResult ["message"] = "Username field missing";
		
		else if (empty ( $_POST ['password'] ))
			
			$this->apiResult ["message"] = "Password field missing";
		
		else if ($res = $this->userModel->authenticateUser ( $_POST ['email'], $_POST ['password'] )) {
			if(isset($res['user_id']) && isset($_POST['actionconfirm']) && $_POST['actionconfirm']==1){
                         $this->db->delete("session_token", array("user_id" => $res['user_id']));   
                        }
                        $user_exists=0;
                        if(isset($res['user_id']) && !in_array(1,$res['role_ids']) && !in_array(2,$res['role_ids']) && $details_user=$this->userModel->userTokenExists($res['user_id'])){
                            //echo"ss";
                            $user_exists=count($details_user);
                            
                        }
                        
                        if($user_exists>0){
                        
                        $server_details=unserialize($details_user['server_details']);
                        $login_time=date("d-m-Y H:i:s",strtotime($details_user['created_date']));
                        //print_r($server_details);
                        $ip=isset($server_details['REMOTE_ADDR'])?"(IP:".$server_details['REMOTE_ADDR'].")":'';
                        $agent=isset($server_details['HTTP_USER_AGENT'])?"/".$server_details['HTTP_USER_AGENT']."":'';
                        $this->apiResult ["errormsg"]="You are already logged in from another computer ".$ip." using the same credentials at ".$login_time.". By logging in, you can lose the unsaved data from previous login.<br><b>Please confirm to proceed.</b>";
                        $this->apiResult ["confirmstatus"] = 0;
                        $this->apiResult ["status"] = 1;
                        }else{
			if ($token = $this->userModel->generateToken ( $res ['user_id'], $_POST ['email'] )) {
				
				$time=time();
                                $issuedAt   = $time;
                                $notBefore  = $issuedAt; //Adding 10 seconds
                                $expire     = $notBefore + TOKEN_LIFE_REFRESH;
                                $refresh    = $notBefore + TOKEN_LIFE;
                                
                                $jwt_token = array(
                                "iss" => "adhyayan.asia",
                                "jti" => $token,
                                "iat" => $issuedAt,
                                "nbf" => $notBefore,
                                "exp" => $expire,
                                "user" => $this->userModel->checkTokenJWT($res['user_id'])   
                                );
                                
                                $jwt_token=Firebase\JWT\JWT::encode($jwt_token, PRIVATEKEY, 'RS256');
                                setcookie('ADH_TOKEN_REFRESH',$token);
                                
                                if(COOKIE_GEN==1){
                                setcookie('ADH_TOKEN',$jwt_token, 0 , '/',COOKIE_DOMAIN);
                                setcookie('ADH_TOKEN_REFRESH',$token, 0 , '/',COOKIE_DOMAIN);
                                }
                                
                                $this->apiResult ["status"] = 1;
				
				$this->apiResult ["token"] = $jwt_token;
				
				$this->apiResult ["message"] = "Successfully logged in";
                                
			} else
				
				$this->apiResult ["message"] = "Unable to generate token, please try again.";
                        }
		} else
			
			$this->apiResult ["message"] = "Invalid username or password";
	}
	function getInternalAssessorsAction() {
		if (! (in_array ( "create_assessment", $this->user ['capabilities'] ) || in_array ( "create_self_review", $this->user ['capabilities'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else {
			
			$assessmentModel = new assessmentModel ();
			
			$this->apiResult ["assessors"] = $assessmentModel->getInternalAssessors ( $_POST ['client_id'] );
			
			$this->apiResult ["status"] = 1;
		}
	}
        
        function createActionnew1Action(){
            //print_r($_POST);
            $check=false;
            $final_post_array=array();
                    $i=0;
            $actionModel=new actionModel();
            $recids=array();
            $duplicate=false;
            $msg="";
            foreach($_POST['kpa'] as $key=>$val){
                
                $kpa=$val;
                $kq=$_POST['kq'][$key];
                $cq=$_POST['cq'][$key];
                $js=$_POST['js'][$key];
                $rec=$_POST['rec'][$key];
                
                if(empty($kpa) || empty($kq) || empty($cq) || empty($js) || empty($rec)){
                    $check=true;
                    
                }else{
                    $textdata=$actionModel->getRecommendationtext($rec);
                    $final_post_array[$i]['text_data']=$textdata['recommendation_text'];
                    $final_post_array[$i]['kpa_instance_id']=$kpa;
                    $final_post_array[$i]['recommendation_id']=$rec;
                    $final_post_array[$i]['rec_judgement_instance_id']=$js;
                    
                    
                    if(in_array($js,$recids)){
                      $duplicate=true;
                      $key = array_search ($js, $recids);
                      $msg.="Duplicate Rows : Row-".($i+1)." is duplicate of Row-".($key+1)."\n";
                    }
                    
                    $recids[]=$js;
                    
                    $i++;
                }
                
            }
            
            if (empty ( $_POST ['assessment_id'] )) {
			
			$this->apiResult ["message"] = "Assessment id cannot be empty\n";
            } else if($check){
                         $this->apiResult ["message"] = "Some field is missing.Please check\n";       
            } else if($duplicate){
                        $this->apiResult ["message"] = "".$msg." Please remove.\n";           
            } else{
                
                
                     $this->db->start_transaction ();
                     $success = true;
                     
                     
                     //echo"<pre>";
                     //print_r($final_post_array);
                     //echo"</pre>";
                     //die();
                     foreach($final_post_array as $key=>$val){
                         
                     if(!$actionModel->addactionnew1($_POST ['assessment_id'],$val)){
                         $success=false;
                     }
                     
                     }
                
               if(!$success){
                   $this->db->rollback();
                   $this->apiResult ["status"] = 0;
		   $this->apiResult ["message"] = "Unable to add data";
               }else{
                      $this->db->commit ();
                      $this->apiResult ["status"] = 1;
                      $this->apiResult ["message"] = "saved successfully";
               }  
            }
            
            
            
        }
        
        function createAction1Action(){
            
          //echo "<pre>";print_r($_POST);
         // die();
            
          $current_id=(isset($_POST['id_c']) && $_POST['id_c']>0)?1*$_POST['id_c']:0;
          $current_status=(isset($_POST['id_y']) && $_POST['id_y']>0)?1*$_POST['id_y']:0;
          $principle_email = isset($_POST['principle_email'])?$_POST['principle_email']:'';
          $principle_name = isset($_POST['principle_name'])?$_POST['principle_name']:'';
          $school_name = isset($_POST['school_name'])?$_POST['school_name']:'';
          $this->apiResult ["error"]=0;
          
          if(empty($_POST['assessment_id'])){
            $this->apiResult ["message"]="Assessment-id cannot be blank ";  
          }else{
          
          if($current_id>0){
              
              $current_stackholder=isset($_POST['stackholder'][$current_id])?$_POST['stackholder'][$current_id]:array();
              $current_impact=isset($_POST['stackholderimpact'][$current_id])?$_POST['stackholderimpact'][$current_id]:array();
              $valid_stack=1;
              $valid_impact=1;
              foreach($current_stackholder as $key=>$val){
                  $current_impact_val=$current_impact[$key];
                  
                  if(empty($current_impact_val)){
                      $valid_impact=0;
                  }
                  
                  if(empty($val)){
                      $valid_stack=0;
                  }
              }
              //$assessor_key_notes_id[]
              $assessor_key_notes_id=isset($_POST['assessor_key_notes_id'])?$_POST['assessor_key_notes_id']:array();
              
              $currentkey = array_search ($current_id, $assessor_key_notes_id);
              
              $currentid=isset($assessor_key_notes_id[$currentkey])?$assessor_key_notes_id[$currentkey]:'';
              
              //$currentfrom_date=isset($_POST['from_date'][$currentkey])?$_POST['from_date'][$currentkey]:'';
              //$currentto_date=isset($_POST['to_date'][$currentkey])?$_POST['to_date'][$currentkey]:'';
              
              $currentfrom_date=(isset($_POST['from_date'][$currentkey]) && !empty($_POST['to_date'][$currentkey]))?$_POST['from_date'][$currentkey]:'';
              
              if($current_status==0 && empty($currentfrom_date)){
                  
                  $currentfrom_date=date("d-m-Y");
              }
              
              $currentto_date=(isset($_POST['to_date'][$currentkey]) && !empty($_POST['to_date'][$currentkey]))?$_POST['to_date'][$currentkey]:'';
             
              
              $currentleader=isset($_POST['leader'][$currentkey])?$_POST['leader'][$currentkey]:'';
              
              $currentfrequency_report=isset($_POST['frequency_report'][$currentkey])?$_POST['frequency_report'][$currentkey]:'';
              
              $currentreporting_authority=isset($_POST['reporting_authority'][$currentkey])?$_POST['reporting_authority'][$currentkey]:'';
              
              
              /*if(empty($currentid)){
                  $this->apiResult ["message"]['rec_id'] = "Please check the Recommendation-id.\n";
              } else if($valid_stack==0){
                  $this->apiResult ["message"]['impactteam'] = "Please check the Stackholder.\n";
              } else if($valid_impact==0){
                  $this->apiResult ["message"] = "Please check the Impact Statement.\n";
              } else if(empty($currentto_date)){
                  $this->apiResult ["message"] = "Please check the To Date.\n";
              } else if(empty($currentleader)){
                  $this->apiResult ["message"] = "Please check the Leader.\n";
              } else if(empty($currentfrequency_report)){
                  $this->apiResult ["message"] = "Please check the Frequency.\n";
              } else if(empty($currentreporting_authority)){
                  $this->apiResult ["message"] = "Please check the Reporting Authority.\n";
              }*/
              $this->apiResult ["message"]=array();
              if(empty($currentid)){
                  $this->apiResult ["message"]['rec_id'] = "Please check the Recommendation-id.\n";
              }
              
              if($valid_stack==0){
                  $this->apiResult ["message"][$current_id] = "Please check  all the  Stackholder/Impact Statement.\n";
              }
              
              if($valid_impact==0){
                  $this->apiResult ["message"][$current_id] = "Please check  all the  Stackholder/Impact Statement.\n";
              }
              
              $error_fromdate=false;
              $error_todate=false;
              if(empty($currentfrom_date) && $current_status!=0){
                  $this->apiResult ["message"]['fromdate_'.$current_id.''] = "From Date cannot be empty.\n";
                  $error_fromdate = true;
              }
              //echo $currentto_date;
              if(empty($currentto_date)){
                  $this->apiResult ["message"]['todate_'.$current_id.''] = "To Date cannot be empty.\n";
                  $error_todate = true;
              }
              
              
              //echo $yyyy;
              //echo $dd;
              //echo $mm;
              
              //echo "".checkdate($mm,$dd,$yyyy)."dsd";
              //echo $currentfrom_date;
              if(!$error_fromdate){
              if(count(explode('-',$currentfrom_date))==3){    
              list($dd,$mm,$yyyy) = explode('-',$currentfrom_date);    
              if (!checkdate($mm,$dd,$yyyy)) {
                $this->apiResult ["message"]['fromdate_'.$current_id.''] = "Check the date format.\n";  
                $error_fromdate = true;
              }
              }else{
                $this->apiResult ["message"]['fromdate_'.$current_id.''] = "Check the date format.\n";  
                $error_fromdate = true;
              }
              
              }
              
              
              if(!$error_todate){
              if(count(explode('-',$currentto_date))==3){
                  
              list($dd1,$mm1,$yyyy1) = explode('-',$currentto_date);
              if (!checkdate($mm1,$dd1,$yyyy1)) {
                $error_todate = true;
                $this->apiResult ["message"]['todate_'.$current_id.''] = "Check the date format.\n";
              }
              
              }else{
                $error_todate = true;
                $this->apiResult ["message"]['todate_'.$current_id.''] = "Check the date format.\n";
              }
              
              }
              
              if(!$error_todate && !$error_fromdate && date("Y-m-d",strtotime($currentto_date)) < date("Y-m-d",strtotime($currentfrom_date))){
              
                  $this->apiResult ["message"]['todate_'.$current_id.''] = "To Date should be greater than From date.\n";
              }else {
                  
                    $actionModel=new actionModel();
                    $previous_from_date = $_POST['previous_fromdate_'.$current_id];
                    $previous_to_date = $_POST['previous_todate_'.$current_id];
                    $this->apiResult ['fromdate'] = $previous_from_date;
                    $this->apiResult ['todate'] = $previous_to_date;
                    if(!empty($previous_from_date) && date("Y-m-d",strtotime($currentfrom_date)) < date("Y-m-d",strtotime($previous_from_date))){
                         $this->apiResult ['popup'] = "From Date should be greater than original From date.\n";
                         $this->apiResult ['error']=1;
                    }else {
                        $actionDateValidationStatus = $actionModel->checkActionActivityDate($_POST['assessment_id'],$current_id,date("Y-m-d",strtotime($currentfrom_date)),date("Y-m-d",strtotime($currentto_date)));
                        if(!empty($actionDateValidationStatus)) {                          
                             $this->apiResult ['popup'] = "Action activities date is incorrect.First change action activities date.\n";
                              $this->apiResult ['error']=1;
                        }
                        
                        $dateValidationStatus = $actionModel->checkImpactStatementDate($_POST['assessment_id'],$current_id,date("Y-m-d",strtotime($currentfrom_date)),date("Y-m-d",strtotime($currentto_date)));
                        if(!empty($dateValidationStatus)) {                          
                             $this->apiResult ['popup'] = "Impact statements date is incorrect.First change impact statements date.\n";
                              $this->apiResult ['error']=1;
                        }
                    }
                   //echo "aaaa";die;
                  
              }
              
              
              if(empty($currentleader)){
                  $this->apiResult ["message"]['leader_'.$current_id.''] = "Leader cannot be empty.\n";
              }
              
              if(empty($currentfrequency_report)){
                  $this->apiResult ["message"]['frequency_r_'.$current_id.''] = "Frequency cannot be empty.\n";
              }
              
              
              if(empty($currentreporting_authority)){
                  $this->apiResult ["message"]['authority_'.$current_id.''] = "Reporting Authority cannot be empty.\n";
              }
              $email_error=0;
              if(!empty($currentreporting_authority)){
                  
                  $emailsids=explode(",",$currentreporting_authority);
                  
                  foreach($emailsids as $keye=>$vale){
                      
                      if (preg_match ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", trim($vale) ) != 1) {
				
				$email_error=1;
			}
                  }
                  
              }
              
              if($email_error==1){
                  $this->apiResult ["message"]['authority_'.$current_id.''] = "Please check the email ids.\n";
              }
              
              if(count($this->apiResult ["message"])>0){
              $this->apiResult ["error"]=1;   
              }
              
              $this->apiResult ["status"] = 1;
              
        }
        
        if($this->apiResult ["error"]==0){
            
        $assessor_key_notes_id=isset($_POST['assessor_key_notes_id'])?$_POST['assessor_key_notes_id']:array();
        
        $this->db->start_transaction ();
        $success = true;
        $actionModel=new actionModel();
        foreach($assessor_key_notes_id as $key=>$val){
            
            
            $assessor_key_notes_id_c=$val;
            $current_stackholder=isset($_POST['stackholder'][$assessor_key_notes_id_c])?$_POST['stackholder'][$assessor_key_notes_id_c]:array();
            $current_impact=isset($_POST['stackholderimpact'][$assessor_key_notes_id_c])?$_POST['stackholderimpact'][$assessor_key_notes_id_c]:array();
            $already_ids=isset($_POST['assessor_action1_impact_id'][$assessor_key_notes_id_c])?$_POST['assessor_action1_impact_id'][$assessor_key_notes_id_c]:array();
           
              $currentfrom_date=(isset($_POST['from_date'][$key]) && !empty($_POST['to_date'][$key]))?date("Y-m-d",strtotime($_POST['from_date'][$key])):'0000-00-00';
              
              if($current_status==0 && $current_id==$val && (empty($currentfrom_date) || $currentfrom_date=="0000-00-00")){
                  $currentfrom_date=date("Y-m-d");
              }
              
              $currentto_date=(isset($_POST['to_date'][$key]) && !empty($_POST['to_date'][$key]))?date("Y-m-d",strtotime($_POST['to_date'][$key])):'0000-00-00';
              
              $currentleader=isset($_POST['leader'][$key])?$_POST['leader'][$key]:'';
              
              $currentfrequency_report=isset($_POST['frequency_report'][$key])?$_POST['frequency_report'][$key]:'';
              
              $currentreporting_authority=isset($_POST['reporting_authority'][$key])?$_POST['reporting_authority'][$key]:'';
              $status=0;
              
              
              if($current_id==$val && $current_status==0){
                  $status=1;
              }else if($current_id==$val && $current_status>0){
                  $status=$current_status;
              }
              
              $createdBy=$this->user['user_id'];
              
              if(!$actionModel->addaction1($assessor_key_notes_id_c,$current_stackholder,$current_impact,$currentfrom_date,$currentto_date,$currentleader,$currentfrequency_report,$currentreporting_authority,$status,$createdBy,$current_id,$already_ids)){
                  $success=false;
              }
              
                                
        }
        
               if(!$actionModel->updateOverallstatus($_POST['assessment_id'],1)){
                   
                   $success=false;
               }                
        
               if(!$success){
                   $this->db->rollback();
                   $this->apiResult ["status"] = 0;
		   $this->apiResult ["message"] = "Unable to add data";
               }else{
                        $this->db->commit ();
                        $leaderIds = array();
                        $leaderIdsString = '';
                        $emailParams = array();
                        //echo $current_id;  echo "<pre>";print_r($_POST);die;
                         if(!empty($_POST['id_c']) && !empty($_POST['leader'])) {
                            
                            if(isset($_POST['leader'][$currentkey])){
                                $leaderIds[] = empty($_POST['mail_status'][$currentkey])?$_POST['leader'][$currentkey]:'';
                            }
                            if(!empty($leaderIds)){

                                $leaderIds =  array_unique($leaderIds);
                                $leaderIdsString = implode(',', $leaderIds);
                                //$emailParams[] = array ('email'=>$principle_email,'name'=>$principle_name);
                               // echo $leaderIdsString;
                                if(!empty($leaderIdsString)){
                                    $emailParams[] = $actionModel->getLeaderData($leaderIdsString);
                                }
                               // print_r($emailParams);
                                if(!empty($emailParams[0]) && !in_array($principle_email,array_column($emailParams[0],'email'))) {
                                     $emailParams[0][] = array ('email'=>$principle_email,'name'=>$principle_name);                         
                                }
                                if(!empty($leaderIdsString) && !empty($emailParams)){
                                    $actionModel->sendNotificationMail($emailParams[0],$school_name,$current_id,$_POST['assessor_key_notes_id']);
                                }
                            }
                        }
                        //die;
                        $this->apiResult ["message"] ="Data Saved Successfully!";
                        $this->apiResult ["status"] = 1;    
               }                
        
        }
              
        
          }
          
        }
        
        function createAction2Action(){
          
          $impactFiles  = array();  
          if(empty($_POST['assessment_id'])){
          $this->apiResult ["message"]="Assessment-id cannot be blank ";  
          }else if(empty($_POST['id_c'])){
          $this->apiResult ["message"]="Recommendation-id cannot be blank ";  
          }else if(empty($_POST['h_assessor_action1_id'])){
          $this->apiResult ["message"]="Action-id cannot be blank ";  
          }else{
             $this->apiResult ["message"]=array(); 
             $this->apiResult ["message"]['team_designation']=array();
             $this->apiResult ["message"]['team_member_name']=array();
             
             $start_date = !empty($_POST['from_date'])?$_POST['from_date']:'';
             $end_date = !empty($_POST['to_date'])?$_POST['to_date']:'';
             $team_designation=$_POST['team_designation'];
             $is_submit = !empty($_POST['is_submit'])?$_POST['is_submit']:'';
             $error=0;
             $team_add=array();
             $count=0;
             foreach($team_designation as $keyd=>$vald){
                 $name=$_POST['team_member_name'][$keyd];
                 
                 //if(!empty($vald) || !empty($name)){
                     
                 if(empty($vald)){
                     $this->apiResult ["message"]['team_designation'][$keyd]="Designation cannot be blank";
                     $error=1;
                 }
                 
                 if(empty($name)){
                     $this->apiResult ["message"]['team_member_name'][$keyd]="Name cannot be blank";
                     $error=1;
                 }
                 
                 
                 //}
                 
                 if(!empty($vald) && !empty($name)){
                 $team_add[$count]['team_designation']=$vald;
                 $team_add[$count]['team_member_name']=$name;
                 $count++;
                 }
             }
             
             
            $activity_stackholder_mutiple=array();
            if(!empty($_POST['activity_stackholder_check'])){
            foreach($_POST['activity_stackholder_check'] as $keyac=>$valac){
                
                if(!isset($_POST['activity_stackholder'][$keyac])){
                    
                    $activity_stackholder_mutiple[]=array();
                }else{
                    $activity_stackholder_mutiple[]=$_POST['activity_stackholder'][$keyac];
                }
            }
            }
            
            //$activity_stackholder=array_values($activity_stackholder_mutiple);
            
            //print_r($activity_stackholder_mutiple);
            //print_r($_POST['activity_details']);
            //print_r($_POST['activity_stackholder_check']);
            //die();
            
            $activity_stackholder=$activity_stackholder_mutiple;
            
            $this->apiResult ["message"]['activity_stackholder']=array();
            $this->apiResult ["message"]['activity_details']=array();
            $this->apiResult ["message"]['activity_status']=array();
            $this->apiResult ["message"]['activity_date']=array();
            $this->apiResult ["message"]['activity_comments']=array();
            $this->apiResult ["message"]['activity']=array();
            $this->apiResult ["message"]['activity_actual_date']=array();
            $activity_add=array(); 
            $count1=0;
            foreach($activity_stackholder as $keya=>$vala){
                 $ad=$_POST['activity_details'][$keya];
                 $astatus=$_POST['activity_status'][$keya];
                 $adate=$_POST['activity_date'][$keya];
                 $a=$_POST['activity'][$keya];
                 $acomments=$_POST['activity_comments'][$keya];
                 $acdate=$_POST['activity_actual_date'][$keya];
                 $a_old_id=$_POST['activity_old_id'][$keya];
                 
                 if(!empty($vala) || !empty($ad) || $astatus!="" || !empty($adate) || !empty($acomments) || !empty($a)){
                     
                 if(empty($vala)){
                     $this->apiResult ["message"]['activity_stackholder'][$keya]="Stackholder cannot be blank";
                     $error=1;
                 }
                 
                 if(empty($a)){
                     $this->apiResult ["message"]['activity'][$keya]="Activity cannot be blank";
                     $error=1;
                 }
                 
                 if(empty($ad)){
                     $this->apiResult ["message"]['activity_details'][$keya]="Activity Details cannot be blank";
                     $error=1;
                 }
                 
                 if($astatus==""){
                     $this->apiResult ["message"]['activity_status'][$keya]="Activity Status cannot be blank";
                     $error=1;
                 }
                 
                 $error_adate=false;
                 if(empty($adate)){
                     $this->apiResult ["message"]['activity_date'][$keya]="Activity Date cannot be blank";
                     $error=1;
                     $error_adate=true;
                 }
                 
                 
                  if (!$error_adate) {
                        if (count(explode('-', $adate)) == 3) {
                            list($dd, $mm, $yyyy) = explode('-', $adate);
                            if (!checkdate($mm, $dd, $yyyy)) {
                                $this->apiResult ["message"]['activity_date'][$keya] = "Check the date format.It should be DD-MM-YYYY\n";
                                $error_adate = true;
                                $error=1;
                            }else if(strlen($mm)!=2 || strlen($dd)!=2 || strlen($yyyy)!=4){
                                $this->apiResult ["message"]['activity_date'][$keya] = "Check the date format.It should be DD-MM-YYYY\n";
                                $error_adate = true;
                                $error=1;
                            }
                            
                            
                        } else {
                            $this->apiResult ["message"]['activity_date'][$keya] = "Check the date format.It should be DD-MM-YYYY\n";
                            $error_adate = true;
                            $error=1;
                        }
                  }
                  
                  
                  if (!$error_adate) {
                      
                  if(strtotime($adate) < strtotime($start_date) || strtotime($adate)> strtotime($end_date) ){
                                    $this->apiResult['message']['activity_date'][$keya] = 'Date must be between action plan start and end date ('.$start_date." to ".$end_date.")";
                                    $error = 1;
                                } 
                  }
                  
                   if($astatus=="2"){
                      
                      $error_fdate = false;
                        if (empty($acdate)) {
                            $this->apiResult ["message"]['activity_actual_date'][$keya] = "Actual Day cannot be blank for completed activities";
                            $error = 1;
                            $error_fdate = true;
                        }
                        
                        if (!$error_fdate) {
                        if (count(explode('-', $acdate)) == 3) {
                            list($dd, $mm, $yyyy) = explode('-', $acdate);
                            if (!checkdate($mm, $dd, $yyyy)) {
                                $this->apiResult ["message"]['activity_actual_date'][$keya] = "Check the date format.It should be DD-MM-YYYY\n";
                                $error_fdate = true;
                                $error=1;
                            }else if(strlen($mm)!=2 || strlen($dd)!=2 || strlen($yyyy)!=4){
                                $this->apiResult ["message"]['activity_actual_date'][$keya] = "Check the date format.It should be DD-MM-YYYY\n";
                                $error_fdate = true;
                                $error=1;
                            }
                            
                            
                        } else {
                            $this->apiResult ["message"]['activity_actual_date'][$keya] = "Check the date format.It should be DD-MM-YYYY\n";
                            $error_fdate = true;
                            $error=1;
                        }
                        
                       
                        
                        if(!$error_adate && !$error_fdate && (date("Y-m-d",strtotime($acdate)) < date("Y-m-d",strtotime($adate)))){
              
                         $this->apiResult ["message"]['activity_actual_date'][$keya]  = "Actual Day should be greater than Date.\n";
                         $error=1;       
                         }
                    }
                    
                    
                        
                    }

                  if(empty($acomments)){
                     if($astatus==3){
                     $this->apiResult ["message"]['activity_comments'][$keya]="Please fill reason for
postponing";    
                     }else{
                     $this->apiResult ["message"]['activity_comments'][$keya]="Please fill comments for the activity";
                     }
                     $error=1;
                 }
                 
                 if(empty($a_old_id) && $astatus=="3"){
                     $this->apiResult ["message"]['activity_status'][$keya]="Activity Status cannot be postponed";
                     $error=1;
                 }
                 
                 }
                 
                 if(!empty($vala) && !empty($ad) && $astatus!="" && !empty($adate) && !empty($acomments) && !empty($a)){
                     
                     $activity_add[$count1]['activity_stackholder']=$vala;
                     $activity_add[$count1]['activity']=$a;
                     $activity_add[$count1]['activity_details']=$ad;
                     $activity_add[$count1]['activity_status']=$astatus;
                     $activity_add[$count1]['activity_date']=$adate;
                     
                     $activity_add[$count1]['activity_actual_date']=$acdate;   
                     $activity_add[$count1]['activity_comments']=$acomments;
                     
                     $activity_add[$count1]['activity_old_id']=$a_old_id;
                     
                     $count1++;
                 }
            }
             
            //validations for impact statements
            //echo "<pre>";print_r($_POST['impactStmnt']);die;
            $actionModel=new actionModel();
            if(!empty($_POST['impactStmnt'])) {
                    $impactFiles = isset($_POST['impactStmnt']['files'])?$_POST['impactStmnt']['files']:array();
                    if(isset($_POST['impactStmnt']['files'])){
                      unset($_POST['impactStmnt']['files']);
                    }
                    $rowNo = 0;
                    $numInsertedRows = 0;                   
                    foreach($_POST['impactStmnt'] as $stmntKey=>$stmntData){
                        foreach($stmntData as $key=>$values){
                        //echo "<pre>";print_r(array_filter(array_values($data)));
                        //echo count(array_values($data));
                            
                            if((!empty($values['activity_method']) && $values['activity_method'] == 4)){
                                unset($values['activity_option']);
                            }else if((!empty($values['activity_method']) && $values['activity_method'] == 2)){
                                unset($values['stakeholder']);
                            }
                            if(!empty(array_filter(array_values($values)))){
                                
                                if(empty($values['date'])){
                                            $this->apiResult['message']['impact_date'][$rowNo] = 'Date cannot be blank'.
                                            $error = 1;
                                }else if(strtotime($values['date']) < strtotime($start_date) || strtotime($values['date'])> strtotime($end_date) ){
                                    $this->apiResult['message']['impact_date'][$rowNo] = 'Date must be between action plan start and end date ('.$start_date." to ".$end_date.")";
                                    $error = 1;
                                }                                 
                                if(empty($values['activity_method'] )){
                                            $this->apiResult['message']['impact_activity_method'][$rowNo] = 'Activity method cannot be blank';
                                            $error = 1;
                                }if((!empty($values['activity_method']) && $values['activity_method'] == 4) && empty($values['stakeholder'] )){ 
                                       $this->apiResult ['message']['impact_stakeholder'][$rowNo] = 'Stakeholder cannot be blank';
                                        $error = 1;
                                }if(( !empty($values['activity_method']) && $values['activity_method'] == 2) && empty($values['activity_option'] )){ 
                                       $this->apiResult ['message']['impact_activity_option'][$rowNo] = 'Classess cannot be blank';
                                        $error = 1;
                                }if(empty($values['comments'] )){
                                            $this->apiResult['message']['impact_comments'][$rowNo] = 'Comments method cannot be blank';
                                            $error = 1;
                                }   
                                $numInsertedRows++;
                        
                            }   
                            $rowNo++;
                        }
                        
                    }
                    if(empty($numInsertedRows)){
                         $deleteStatus = $actionModel->deleteImpactStatement($_POST['assessment_id'],$_POST['id_c']); 
                    }
            }
            else{
                 $deleteStatus = $actionModel->deleteImpactStatement($_POST['assessment_id'],$_POST['id_c']); 
            }
            
            
            
             if($error>0){
              $this->apiResult ["error"]=1;   
             }
             
             
             $this->apiResult ["status"] = 1;
             
             if($error==0){
                 
               $success=true;
               $this->db->start_transaction ();
              
               $h_assessor_action1_id=$_POST['h_assessor_action1_id'];
               $createdBy=$this->user['user_id'];
               
               if(!$actionModel->deleteTeamAction2($h_assessor_action1_id)){
                $success=false;   
               }
               
               foreach($team_add as $keyd=>$vald){
               $td=$vald['team_designation'];
               $tmn=$vald['team_member_name'];
                
               if(!$actionModel->addTeamAction2($h_assessor_action1_id,$td,$tmn,$createdBy)){
                $success=false;   
               }
               
               }
               $ids_not_delete = array();
               if(!empty($_POST['activity_old_id'])) {
                $ids_not_delete=array_filter($_POST['activity_old_id'], function($value) { return $value !== ''; });
               }
               //print_r($ids_not_delete);
               //die();
               if(!$actionModel->deleteActionActivity2($h_assessor_action1_id,$ids_not_delete)){
                $success=false;   
               }
               //print_r($activity_add);
               foreach($activity_add as $keya=>$vala){
               $valas=$vala['activity_stackholder'];
               $a=$vala['activity'];
               $ad=$vala['activity_details'];
               $as=$vala['activity_status'];
               $ada=date("Y-m-d",strtotime($vala['activity_date']));
               $o_id=$vala['activity_old_id'];
               
               if($as==2){
               $ac_date=(!empty($vala['activity_actual_date']) && $vala['activity_actual_date']!="0000-00-00")?(date("Y-m-d",strtotime($vala['activity_actual_date']))):'';
               }else{
               $ac_date="";    
               }
               
               $ac=$vala['activity_comments'];
               
               if(!$actionModel->addActionActivity2($h_assessor_action1_id,$valas,$a,$ad,$as,$ada,$ac_date,$ac,$createdBy,$o_id)){
                    $success=false;   
               }  
              
                 
            }
            
           // echo "<pre>";print_r($_POST['impactStmnt']);
            //if(isset($_POST['impactStmnt']))
            if(!$this->addImpactStatement($_POST, $impactFiles)){
                 //echo "aaaaa";die;
                 $success=false;   
            } 
            if($is_submit == 1 && $success){
                if(!$actionModel->chngeActionActivity2Status($h_assessor_action1_id)){
                    $success=false;   
                }
            }
            // $this->addImpactStatement($_POST);
            if(!$success){
                    $this->db->rollback();
                    $this->apiResult ["status"] = 0;
                    $this->apiResult ["message"] = "Unable to add data";
            }else{
                    $this->db->commit ();
                    $this->apiResult ["message"] ="Records added Successfully.";
                    $this->apiResult ["status"] = 1;    
            }  
           //$this->apiResult ["message"] ="Data Saved Successfully!"; 
             
              
              
          }
          
        }
        }
        function addImpactStatement($data,$files = array()){
            
           $impactStmnt = isset($data['impactStmnt'])?$data['impactStmnt']:'';
               
           if(!empty($impactStmnt)){
               
               $paramsData = array();
               $rowData = array();
               $error = 0;
               /*if(!empty($impactFiles)){
                $files = $impactFiles;
                //unset($impactStmnt['files']);
               }*/
               $actionPlanModel = new actionModel;
              
               foreach($impactStmnt as $key=>$stmnt ){
                   
                   foreach($stmnt as $dateKey=>$values){
                   
                     /*if(empty($values['date'])){
                             $this->apiResult[$key][$dateKey]['date']["message"] = 'Date cannot be blank';
                             $this->apiResult ["status"] = 0;
                             $error = 1;
                    }else if(empty($values['activity_method'] )){
                             $this->apiResult[$key][$dateKey]['activity_method']["message"] = 'Activity method cannot be blank';
                             $this->apiResult ["status"] = 0;
                             $error = 1;
                    }else if(( $values['activity_method'] == 4) && empty($values['stakeholder'] )){ 
                        $this->apiResult [$key][$dateKey]['stakeholder']["message"] = 'Stakeholder cannot be blank';
                        $this->apiResult ["status"] = 0;
                         $error = 1;
                    }else if(( $values['activity_method'] == 2) && empty($values['activity_option'] )){ 
                        $this->apiResult [$key][$dateKey]['activity_option']["message"] = 'Classess cannot be blank';
                        $this->apiResult ["status"] = 0;
                         $error = 1;
                    }else{ */
                        //echo "<pre>";print_r($values);die
                       if(!empty(array_filter(array_values($values)))){
                            $rowData['date']  = isset($values['date'])?$values['date']:'';
                            $rowData['activity_method_id'] = $values['activity_method'];
                            $rowData['activity_option_id'] = 0;
                            if(!empty($values['activity_option']) && isset($values['activity_method']) && ($values['activity_method'] == 2 || $values['activity_method'] == 4))
                                $rowData['activity_option_id'] = $values['activity_option'];
                            else if(!empty($values['stakeholder']) && isset($values['activity_method']) && ($values['activity_method'] == 4 )){
                                $rowData['activity_option_id'] = $values['stakeholder'];
                                // unset($rowData['activity_option_id']);
                            }

                            $rowData['comments'] = $values['comments'];
                            $rowData['assessment_id'] = $data['assessment_id'];
                            $rowData['action_plan_id'] = $data['id_c'];
                            $rowData['row_id'] = $dateKey;
                            $rowData['statement_id'] = $key;

                      // }
                            $paramsData[] = $rowData;
                            $rowData = array();
                       }
                   
                   }
                   
               }
               if(!empty($paramsData) && empty($error)){
                  
                   //$this->db->start_transaction ();
                   $deleteStatus = $actionPlanModel->deleteImpactStatement($data['assessment_id'],$data['id_c'],$files);                   
                   $insertStatus = $actionPlanModel->addImpactStatement($paramsData,$files);
                   if($insertStatus == 1){
                        //$this->db->commit ();
                        $error++;
                        return true;
                       // $this->apiResult ["message"] = 'Impact statements data saved successfully';
                        //$this->apiResult ["status"] = 1;
                   }else{
                        //$this->db->rollback ();
                        //$this->apiResult ["message"] = 'Something wrong ! Please try again';
                        //$this->apiResult ["status"] = 0;
                       return false;
                   }
               }
              // echo "<pre>";print_r($this->apiResult);die;
               
           }
           return true;
        }
        
        /*function addImpactStatement($data){
            
           $impactStmnt = isset($data['impactStmnt'])?$data['impactStmnt']:'';
               
           if(!empty($impactStmnt)){
               
               $paramsData = array();
               $rowData = array();
               $error = 0;
               
               foreach($impactStmnt as $key=>$stmnt ){
                   
                   foreach($stmnt['date'] as $dateKey=>$values){
                      
                       //$rowData[$dateKey] = 
                    //$rowData[$dateKey][]   = $values[$dateKey];
                     if(empty($stmnt['date'][$dateKey]) || empty($stmnt['activity_method'][$dateKey] )){
                        
                            // echo $dateKey;
                             $this->apiResult ["message"] = 'Please fill all values for impact statements';
                             $this->apiResult ["status"] = 0;
                             $error = 1;
                    }else if(!empty($stmnt['activity_method'][$dateKey]) && empty($stmnt['activity_option_id'][$dateKey] )){ 
                        $this->apiResult ["message"] = 'Please fill all values for impact statements';
                        $this->apiResult ["status"] = 0;
                         $error = 1;
                    }else{ 
                    $rowData[$dateKey]['date']  = isset($stmnt['date'][$dateKey])?$stmnt['date'][$dateKey]:'';
                    $rowData[$dateKey]['activity_method_id'] = $stmnt['activity_method'][$dateKey];
                    if(isset($stmnt['activity_method'][$dateKey]) && ($stmnt['activity_method'][$dateKey] == 2 || $stmnt['activity_method'][$dateKey] == 4))
                        $rowData[$dateKey]['activity_option_id'] = $stmnt['activity_option'][$dateKey];
                    else
                        $rowData[$dateKey]['activity_option_id'] = 0;
                    
                    $rowData[$dateKey]['comments'] = $stmnt['comments'][$dateKey];
                    $rowData[$dateKey]['assessment_id'] = $data['assessment_id'];
                    $rowData[$dateKey]['action_plan_id'] = $data['id_c'];
                   }
                   //echo "<pre>"; print_r($rowData);
                   }
                   $paramsData[$key] = $rowData;
                   $rowData = array();
               }
               if(!empty($paramsData) && empty($error)){
                   
                   //$dataToBeInsert = array();
                   $actionPlanModel = new actionModel;
                  return  $actionPlanModel->addImpactStatement($paramsData);
               }
               return false;
           }
          //echo "<pre>"; print_r($paramsData);
        }*/
        
        function deletePlanningrowAction(){
            //echo"<pre>";
            //print_r($_POST);
            //echo"</pre>";
            //die();
            if(empty($_POST['id_c'])){
                $this->apiResult ["message"] = "Invalid key notes-id.\n";
            }else if(empty($_POST['assessment_id'])){
                $this->apiResult ["message"] = "Invalid assessment-id.\n";
            }else{
                $assessment_id=$_POST['assessment_id'];
                $assessor_key_notes_id=$_POST['id_c'];
                $actionModel= new actionModel();
                $details_key=$actionModel->getrowdetails($assessor_key_notes_id);
                $success=true;
                $this->db->start_transaction ();
                if((isset($details_key['action_status']) && $details_key['action_status']==0) || !isset($details_key['action_status'])){
                
                 if(!$actionModel->deleteRec($_POST['assessment_id'],$assessor_key_notes_id)){
                   $success=false;  
                 } 
                 
                 
                if(!$success){
                      $this->db->rollback();
                      $this->apiResult ["status"] = 0;
		      $this->apiResult ["message"] = "Unable to delete data";
                }else{
                      $this->db->commit ();
                      $this->apiResult ["message"] ="Records deleted Successfully.";
                      $this->apiResult ["status"] = 1;    
                }  
                    
                }else{
                 $this->apiResult ["message"] = "Not allowed to delete.\n";   
                }
                
                
                
            }
            
        }
        
        function createStudentReportAction(){
               //print_r($_POST);
               $province=isset($_POST ['province'])?$_POST ['province']:array();
               $school=isset($_POST ['school'])?$_POST ['school']:array();
               
               if (! (in_array ( "create_assessment", $this->user ['capabilities'] ) || in_array ( "create_self_review", $this->user ['capabilities'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['report_type'] )) {
			
			$this->apiResult ["message"] = "Report type cannot be empty\n";
		}else if (empty ( $_POST ['network'] )) {
			$this->apiResult ["message"] = "Network/ Organisation cannot be empty\n";
		}else if (($_POST ['report_type']==11 || $_POST ['report_type']==8) && empty ( $_POST ['province'] )) {
			
			$this->apiResult ["message"] = "Province/ Centre cannot be empty\n";
		}else if (($_POST ['report_type']==8) && empty ( $_POST ['school'] )) {
			
			$this->apiResult ["message"] = "School/ Batch cannot be empty\n";
		}else if (empty ( $_POST ['round'] )) {
			
			$this->apiResult ["message"] = "Round cannot be empty\n";
		}else if (count($province)>1 &&  empty(trim($_POST ['report_name'])) && ($_POST ['report_type']==11 || $_POST ['report_type']==12)) {
			
			$this->apiResult ["message"] = "Report Name cannot be empty\n";
		}
                /*else if ($_POST ['round']==2 &&  ($_POST ['report_type']==12) ) {
			
			$this->apiResult ["message"] = "This Report is under construction\n";
		}*/else{
                                
                    
                            $report_type=$_POST ['report_type'] ;
                            $network=$_POST ['network'];
                            
                            
                            $round=$_POST ['round'];
                            $assessmentModel=new assessmentModel();
                            $diagnosticModel=new diagnosticModel();
                            $customreportModel=new customreportModel();
                            $networkModel=new networkModel();
                            $clientModel=new clientModel;
                            $centre_id=$province;
                            $organisation_id=$network;
                            $batch_id=$school;
                            if($report_type==8) $batch_id=$school;
                            if($report_type==11) $centre_id=$province;
                            if($report_type==12) $organisation_id=$network;
                            
                            
                            /*if(count($_POST ['province'])>1){
                                $reportName=$_POST['report_name'];
                            }else{
                                
                                $reportName=$_POST['report_name'];
                            }*/
                            
                            if($customreportModel->getDuplicateReport($batch_id,$centre_id,$organisation_id,$round,$report_type)==0){
                            
                            $this->db->start_transaction ();
                            $success = true;
                            if($report_type==8){
                                
                                $school=$school[0];
                                $province=$province[0];
                                
                                $groupdata=$assessmentModel->getGAIdfromClientandRound($school,$round);
                                //print_r($groupdata);
                                $gaid=$groupdata['group_assessment_id'];
                                if($gaid>0){
                                $assessment=$diagnosticModel->getTeacherAssessmentReports($gaid); 
                                
                                if(count($assessment['diagnostic_ids'])>0){
                                        foreach($assessment['diagnostic_ids'] as $key=>$val){
                                            $diagnostic_id=$key;
                                        }
                                
                                        //$reportObject=new groupReport($gaid,$report_type,$diagnostic_id,'','');
                                        $numTeachersCompletedAsst = $assessmentModel->getAssessmentCountInGroupAssessmentDiagnostic($gaid,$diagnostic_id);
                                        $assessments=$assessmentModel->getAssessmentsInGroupAssessmentDiagnostic($gaid,$diagnostic_id);
                                        //print_r($numTeachersCompletedAsst);
                                        if(count($numTeachersCompletedAsst)==count($assessments)){
                                        $this->apiResult ["message"] ="No student Assessment found";    
                                        }else{
                                        $client=$clientModel->getClientById($school);    
                                        $client_name=$client['client_name'];
                                        $report_name=$diagnosticModel->getReportName(8);
                                        $report_des="".$report_name['report_name']."-".$client_name."-Round-".$round."";
                                        
                                        if($lid=$customreportModel->saveStudentReport($report_type,$report_des)){
                                            
                                                if($lid_1=$customreportModel->saveStudentReport_1($lid,$school,$province,$network,$round)){
                                                    
                                                    if($lid2=$customreportModel->saveStudentReportClient($lid_1,$_POST['school']) && $lid3=$customreportModel->saveStudentReportProvince($lid_1,$_POST['province'])){

                                                    $this->apiResult ["status"] = 1;

                                                    $nwrow=$customreportModel->getNetworkReportsListfromID($lid);

                                                    $this->apiResult ["message"] ="Report Added Successfully. Click <a href='?controller=report&action=student&report_id=".$nwrow['report_id']."&group_assessment_id=".$nwrow['group_assessment_id']."&diagnostic_id=".$nwrow['diagnostic_id']."' target='_blank'>Here</a> to View";

                                                    }else{
                                                        $success=false;
                                                    }
                                                
                                                }else{
                                                    $success=false;
                                                }
                                                
                                                
                                        
                                        }else{
                                          $this->apiResult ["message"] ="Some Problem in addition";
                                          $success=false;
                                        }
                                        
                                        
                                        }
                                
                                
                                
                                }else{
                                $this->apiResult ["message"] ="No Records found for this criteria";     
                                }
                                //$report=$this->downloadStudentDataAction($gaid,$report_type,$diagnostic_id,'','');
                                
                                
                            }else{
                               
                            $this->apiResult ["message"] ="No Records found for this criteria";     
             
                            }
                            
                }else if($report_type==11){
                    
                    
                                //$diagnosticModel=new diagnosticModel();
                                $diagnostic_id=$diagnosticModel->getDiagnosticCentre($centre_id);
                                $diagnostic_id=$diagnostic_id['diagnostic_id'];
                                $report_data=$customreportModel->getKeyQuestionsCentre($province,$diagnostic_id,$school);
                                
                                if(count($report_data)>0){
                                        $report_name=$diagnosticModel->getReportName(11);
                                        
                                        if(count($province)>1){
                                        $report_des="".$report_name['report_name']." ".$_POST['report_name']."-Round-".$round."";
                                        }else{
                                        $province_data=$networkModel->getProvinceById($province[0]);
                                        $province_name=$province_data['province_name'];
                                        
                                        $schools_name="";
                                        
                                        if(count($school)>0){
                                            
                                            $schools_name=" (".$customreportModel->getSchoolsName($school).")";
                                            
                                        }
                                        
                                        $report_des="".$report_name['report_name']."-".$province_name."".$schools_name."-Round-".$round."";
                                        
                                        }
                                        
                                        if($lid=$customreportModel->saveStudentReport($report_type,$report_des)){
                                            
                                               if($lid_1=$customreportModel->saveStudentReport_1($lid,0,0,$network,$round)){
                                                    if($lid2=$customreportModel->saveStudentReportClient($lid_1,$school) && $lid3=$customreportModel->saveStudentReportProvince($lid_1,$province)){

                                                    $nwrow=$customreportModel->getNetworkReportsListfromID($lid);   
                                                    $this->apiResult ["status"] = 1;
                                                    $this->apiResult ["message"] ="Report Added Successfully. Click <a href='?controller=report&action=studentCentre&report_id=".$nwrow['report_id']."&centre_id=".$nwrow['province_id']."&batch_id=".$nwrow['client_id']."&round_id=".$nwrow['round_id']."'  target='_blank'>Here</a> to View";

                                                    }else{
                                                     $success=false;   
                                                    }
                                                
                                               }else{
                                                    $success=false;
                                                }
                                        }else{
                                          $this->apiResult ["message"] ="Some Problem in addition";
                                          $success=false;
                                        }        
                                }else{
                                         $this->apiResult ["message"] ="No Records found for this criteria";    
                                }
                    
                    
                }else if($report_type==12){
                    
                    
                                //$diagnosticModel=new diagnosticModel();
                                $diagnostic_id=$diagnosticModel->getDiagnosticOrg($organisation_id);
                                $diagnostic_id=$diagnostic_id['diagnostic_id'];
                                $report_data=$customreportModel->getKeyQuestionsOrg($network,$diagnostic_id,$province,$school);
                                
                                if(count($report_data)>0){
                                        $report_name=$diagnosticModel->getReportName(12);
                                        if(count($province)>1){
                                        $report_des="".$report_name['report_name']." ".$_POST['report_name']."-Round-".$round."";    
                                        }else{
                                            
                                        $network_data=$networkModel->getNetworkById($network);
                                        $network_name=$network_data['network_name'];
                                        
                                        $centres_name="";
                                        
                                        if(count($school)>0){
                                            
                                            $centres_name.=" (".$customreportModel->getSchoolsName($school).")";
                                            
                                        }
                                        
                                        if(count($province)>0 && count($school)==0){
                                            
                                            $centres_name.=" (".$customreportModel->getCentresName($province).")";
                                        }
                                        
                                        $report_des="".$report_name['report_name']."-".$network_name."".$centres_name."-Round-".$round."";
                                        }
                                        
                                        if($lid=$customreportModel->saveStudentReport($report_type,$report_des)){ 
                                            
                                            if($lid_1=$customreportModel->saveStudentReport_1($lid,0,0,$network,$round)){
                                                if($lid2=$customreportModel->saveStudentReportClient($lid_1,$school) && $lid3=$customreportModel->saveStudentReportProvince($lid_1,$province)){
   
                                                $nwrow=$customreportModel->getNetworkReportsListfromID($lid);   
                                                $this->apiResult ["status"] = 1;
                                                $this->apiResult ["message"] ="Report Added Successfully. Click <a href='?controller=report&action=studentOrg&report_id=".$nwrow['report_id']."&org_id=".$nwrow['network_id']."&centre_id=".$nwrow['province_id']."&batch_id=".$nwrow['client_id']."&round_id=".$nwrow['round_id']."'  target='_blank'>Here</a> to View";
                                                
                                                
                                                }else{
                                                     $success=false;   
                                                    }
                                                
                                               }else{
                                                $success=false;
                                                }
                                        }else{
                                          $this->apiResult ["message"] ="Some Problem in addition";
                                          $success=false;
                                        }        
                                }else{
                                         $this->apiResult ["message"] ="No Records found for this criteria";    
                                }
                    
                    
                }
                
               if(!$success){
                   $this->db->rollback();
		   $this->apiResult ["message"] = "Unable to generate report";
               }else{
                      $this->db->commit ();
               } 
                
                            }else{
                              $add_msg="";  
                              if($report_type==8){  
                              $res=$customreportModel->getNetworkReportIdfrombatchcenternetworkround($report_type,$network,$province[0],$school[0],$round);
                              $network_report_id=$res['network_report_id'];
                              $nwrow=$customreportModel->getNetworkReportsListfromID($network_report_id);
                              
                              if(count($nwrow)>0){
                              $add_msg=" Click <a href='?controller=report&action=student&report_id=".$nwrow['report_id']."&group_assessment_id=".$nwrow['group_assessment_id']."&diagnostic_id=".$nwrow['diagnostic_id']."' target='_blank'>Here</a> to View";
                              }  
                              }else if($report_type==11){ 
                              $centres_s=count($province)>0?implode(",",$province):'';
                              $schools_s=count($school)>0?implode(",",$school):'';
                              
                              $add_msg =" Click <a href='?controller=report&action=studentCentre&report_id=".$report_type."&centre_id=".$centres_s."&batch_id=".$schools_s."&round_id=".$round."'  target='_blank'>Here</a> to View";
                              }else if($report_type==12){ 
                              $centres_s=count($province)>0?implode(",",$province):'';
                              $schools_s=count($school)>0?implode(",",$school):'';
                              
                              $add_msg =" Click <a href='?controller=report&action=studentOrg&report_id=".$report_type."&org_id=".$network."&centre_id=".$centres_s."&batch_id=".$schools_s."&round_id=".$round."'  target='_blank'>Here</a> to View";
                              }
                              
                             $this->apiResult ["message"] ="This report is already generated.".$add_msg."";    
                            }
            
        }
        }
        
        //Added by Vikas for facilitator List
        function getFacilitatorsAction() {
		if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else {
			
			//$assessmentModel = new assessmentModel ();
			$WorkshopModel = new WorkshopModel ();
			//$this->apiResult ["assessors"] = $assessmentModel->getFacilitators ( $_POST ['client_id'] );
			$this->apiResult ["assessors"] = $WorkshopModel->getUsersbyClient ( $_POST ['client_id'] );
			$this->apiResult ["status"] = 1;
		}
	}
        
         function getUsersAction() {
		if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else {
			
			$WorkshopModel = new WorkshopModel ();
			
			$this->apiResult ["assessors"] = $WorkshopModel->getUsersbyClient ( $_POST ['client_id'] );
			
			$this->apiResult ["status"] = 1;
		}
	}
        //Added by Vikas for facilitator List
        
	function updateVideoAction() {
		$user_id = $this->user ['user_id'];
		
		$view = 1;
		
		$this->userModel->updateUserVideo ( $user_id, $view );
		
		$this->apiResult ["message"] = "success\n";
		
		$this->apiResult ["status"] = 1;
	}
	function getEditInternalAssessorsAction() {
            
                $isEditable = isset($_POST ['isEditable'])?$_POST ['isEditable']:0;
		if (! (in_array ( "create_assessment", $this->user ['capabilities'] ) || $isEditable == 1)) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else if (empty ( $_POST ['user_id'] )) {
			
			$this->apiResult ["message"] = "User id cannot be empty\n";
		} 

		else {
			
			$assessmentModel = new assessmentModel ();
			
			$user_id = explode ( ',', $_POST ['user_id'] );
			
			$user_id = $user_id [0]; // internal reviwer is at the first position in the list
			
			$this->apiResult ["assessors"] = $assessmentModel->getEditInternalAssessors ( $_POST ['client_id'], $user_id );
			
			$this->apiResult ["status"] = 1;
		}
	}
	function getExternalAssessorsAction() {
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else {
			
			$assessmentModel = new assessmentModel ();
			
			$this->apiResult ["assessors"] = $assessmentModel->getExternalAssessors ( $_POST ['client_id'] );
			
			$this->apiResult ["status"] = 1;
		}
	}
	function getDiagnosticsForInternalAssessorAction() {
		if (! (in_array ( "create_assessment", $this->user ['capabilities'] ) || in_array ( "create_self_review", $this->user ['capabilities'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['internal_assessor_id'] )) {
			
			$this->apiResult ["message"] = "Internal assessor id cannot be empty\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else {
			
			$assessmentModel = new assessmentModel ();
			
			$diagnosticModel = new diagnosticModel ();
			
			$asstId = empty ( $_POST ['assessment_id'] ) ? 1 : $_POST ['assessment_id'];
			
			$hideDiag = $assessmentModel->getDiagnosticsForInternalAssessor ( $_POST ['client_id'], $_POST ['internal_assessor_id'], $asstId );
			
			$this->apiResult ["hidediagnostics"] = $hideDiag;
			
			$this->apiResult ["allDiagnostics"] = $diagnosticModel->getDiagnostics ( array (
					"assessment_type_id" => 1,
					"isPublished" => "yes" 
			),'all',1);
			
			$this->apiResult ["status"] = 1;
		}
	}
        
        function getDiagnosticsForInternalAssessorCollegeAction() {
		if (! (in_array ( "create_assessment", $this->user ['capabilities'] ) || in_array ( "create_self_review", $this->user ['capabilities'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['internal_assessor_id'] )) {
			
			$this->apiResult ["message"] = "Internal assessor id cannot be empty\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else {
			
			$assessmentModel = new assessmentModel ();
			
			$diagnosticModel = new diagnosticModel ();
			
			$asstId = empty ( $_POST ['assessment_id'] ) ? 1 : $_POST ['assessment_id'];
			
			$hideDiag = $assessmentModel->getCollegeDiagnosticsForInternalAssessor ( $_POST ['client_id'], $_POST ['internal_assessor_id'], $asstId );
			
			$this->apiResult ["hidediagnostics"] = $hideDiag;
			
			$this->apiResult ["allDiagnostics"] = $diagnosticModel->getDiagnostics ( array (
					"assessment_type_id" => 5,
					"isPublished" => "yes" 
			),'all',1);
			
			$this->apiResult ["status"] = 1;
		}
	}
        
	function getSchoolAdminsAction() {
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else {
			
			$assessmentModel = new assessmentModel ();
			
			$this->apiResult ["schoolAdmins"] = $assessmentModel->getSchoolAdmins ( $_POST ['client_id'] );
			
			$this->apiResult ["status"] = 1;
		}
	}
	function getLanguageDiagnosticsAction() {
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['lang_id'] )) {
			
			$this->apiResult ["message"] = "Diagnostic language id cannot be empty\n";
		} else {
			
			$diagnosticModel =  new diagnosticModel;
			//print_r($diagnosticModel->getDiagnostics('',$_POST ['lang_id']));
			$this->apiResult ["langDiagnostics"] = $diagnosticModel->getDiagnostics('',$_POST ['lang_id']);
			
			$this->apiResult ["status"] = 1;
		}
	}
	function getDiagnosticLanguagesAction() {
		/*if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else*/
                if (empty ( $_POST ['diagnostic_id'] )) {
			
			$this->apiResult ["message"] = "Diagnostic language id cannot be empty\n";
		} else {
			
			$diagnosticModel =  new diagnosticModel;
			//print_r($diagnosticModel->getDiagnosticLanguages($_POST ['diagnostic_id']));
			$this->apiResult ["langDiagnostics"] = $diagnosticModel->getDiagnosticLanguages($_POST ['diagnostic_id']);
			
			$this->apiResult ["status"] = 1;
		}
	}
        
        function getroundsAction() {
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else {
			
			$assessmentModel = new assessmentModel ();
			
			$this->apiResult ["rounds"] = $assessmentModel->getStudentRoundsAll ();
			$roundsUnusedi= $assessmentModel->getStudentRounds ( $_POST ['client_id'],0 );
                        $roundsUnusedf=array();
                        foreach($roundsUnusedi as $key=>$val){
                         $roundsUnusedf[]= $val['aqs_round'];  
                        }
                        $this->apiResult ["roundsUnused"]=$roundsUnusedf;
			//print_r();
                        $this->apiResult ["status"] = 1;
		}
	}
        
	function getNetworkListAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else {
			
			$networkModel = new networkModel ();
			
			$this->apiResult ["networks"] = $networkModel->getNetworkList ();
			
			$this->apiResult ["status"] = 1;
		}
	}
	function addSchoolToNetworkAction() {
		$networkModel = new networkModel ();
		
		if (! in_array ( "create_client", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_ids'] ) || ! is_array ( $_POST ['client_ids'] ) || count ( $_POST ['client_ids'] ) == 0) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else if (empty ( $_POST ['network_id'] )) {
			
			$this->apiResult ["message"] = "Network id cannot be empty\n";
		} else if ($network = $networkModel->getNetworkById ( $_POST ['network_id'] )) {
			
			$clientModel = new clientModel ();
			
			$this->db->start_transaction ();
			
			$success = true;
			
			$this->apiResult ["content"] = '';
			
			foreach ( $_POST ['client_ids'] as $client_id ) {
				
				$client = $clientModel->getClientById ( $client_id );
				
				if (empty ( $client ['client_id'] )) {
					
					$this->apiResult ["message"] = "School id $client_id does not exists\n";
					
					$success = false;
				} else if ($client ['network_id'] > 0) {
					
					$this->apiResult ["message"] = "School " . $client ['client_name'] . " is already associated with a network '" . $client ['network_name'] . "'\n";
					
					$success = false;
				} else if ($clientModel->addClientToNetwork ( $client_id, $_POST ['network_id'] )) {
					
					$this->apiResult ["content"] .= networkModel::getEditSchoolsInnetworkRowHtml ( $_POST ['network_id'], $client );
				} else {
					
					$this->apiResult ["message"] = "Unable to add school " . $client ['client_name'] . " to the network.";
					
					$success = false;
				}
			}
			
			if ($success && $this->db->commit ()) {
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["message"] = "Schools successfully added to the network.";
				
				$this->apiResult ["network_id"] = $_POST ['network_id'];
			}
		} else
			
			$this->apiResult ["message"] = "Network does not exists\n";
	}
	function addSchoolToProvinceAction() {
		$networkModel = new networkModel ();
	
		if (! in_array ( "create_client", $this->user ['capabilities'] )) {
				
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_ids'] ) || ! is_array ( $_POST ['client_ids'] ) || count ( $_POST ['client_ids'] ) == 0) {
				
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else if (empty ( $_POST ['province_id'] )) {
				
			$this->apiResult ["message"] = "Province id cannot be empty\n";
		} else if ($province = $networkModel->getProvinceById ( $_POST ['province_id'] )) {
				
			$clientModel = new clientModel ();
				
			$this->db->start_transaction ();
				
			$success = true;
				
			$this->apiResult ["content"] = '';
				
			foreach ( $_POST ['client_ids'] as $client_id ) {
	
				$client = $clientModel->getClientById ( $client_id );
	
				if (empty ( $client ['client_id'] )) {
						
					$this->apiResult ["message"] = "School id $client_id does not exists\n";
						
					$success = false;
				} else if ($client ['province_id'] > 0) {
						
					$this->apiResult ["message"] = "School " . $client ['client_name'] . " is already associated with a province '" . $client ['province_name'] . "'\n";
						
					$success = false;
				} else if ($clientModel->addClientToProvince( $client_id, $_POST ['province_id'] )) {
						
					$this->apiResult ["content"] .= networkModel::getEditSchoolsInnetworkProvinceRowHtml ( $_POST ['province_id'], $client );
				} else {
						
					$this->apiResult ["message"] = "Unable to add school " . $client ['client_name'] . " to the network.";
						
					$success = false;
				}
			}
				
			if ($success && $this->db->commit ()) {
	
				$this->apiResult ["status"] = 1;
	
				$this->apiResult ["message"] = "Schools successfully added to the province.";
	
				$this->apiResult ["province_id"] = $_POST ['province_id'];
			}
		} else
				
			$this->apiResult ["message"] = "Province does not exist\n";
	}
	function createSchoolAssessmentAction() {
		
		// print_r($_POST);die;
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty.\n";
		} else if (empty ( $_POST ['internal_assessor_id'] )) {
			
			$this->apiResult ["message"] = "Internal reviewer cannot be empty.\n";
		} else if (empty ( $_POST ['external_assessor_id'] )) {
			
			$this->apiResult ["message"] = "External reviewer cannot be empty.\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['role'] ) && empty ( $_POST ['externalReviewTeam'] ['role'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {
			
			$this->apiResult ["message"] = "External reviewer role cannot be empty\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['member'] ) && empty ( $_POST ['externalReviewTeam'] ['member'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {
			
			$this->apiResult ["message"] = "External reviewer member cannot be empty.\n";
		} else if (empty ( $_POST ['diagnostic_id'] )) {
			
			$this->apiResult ["message"] = "Diagnostic cannot be empty.\n";
		} else if (empty ( $_POST ['tier_id'] )) {
			
			$this->apiResult ["message"] = "Tier cannot be empty.\n";
		} else if (empty ( $_POST ['award_scheme_name'] )) {
			
			$this->apiResult ["message"] = "Award Scheme cannot be empty.\n";
		} else if ($_POST ['internal_assessor_id'] == $_POST ['external_assessor_id']) {
			
			$this->apiResult ["message"] = "Internal and External reviewer cannot be same.\n";
		} else {
			
			$externalReviewTeam = '';
			
			$i = 0;
			
			$externalRoleClient = array ();
			
			// $externalRoleClient = array($_POST['externalReviewTeam']['clientId'],$_POST['externalReviewTeam']['role'],$_POST['externalReviewTeam']['member']);
			
			if (! empty ( $_POST ['externalReviewTeam'] ['clientId'] ))
				
				foreach ( $_POST ['externalReviewTeam'] ['clientId'] as $client ) 

				{
					
					array_push ( $externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i] );
					
					$i ++;
				}
				
				// print_r($externalRoleClient);
			
			$externalReviewTeam = empty ( $_POST ['externalReviewTeam'] ['clientId'] ) ? '' : array_combine ( $_POST ['externalReviewTeam'] ['member'], $externalRoleClient );
			
			// $externalReviewTeam = array_combine($_POST['externalReviewTeam']['member'],$externalRoleClient);
			
			// print_r($externalReviewTeam);
			
			// die;
			
			/*
			 * foreach($_POST['externalReviewTeam']['member'] as $member){
			 *
			 *
			 *
			 * $externalReviewTeam = array_push($externalReviewTeam,$)
			 *
			 *
			 *
			 * }
			 */
			
			$assessmentModel = new assessmentModel ();
			
			if ($aid = $assessmentModel->createSchoolAssessment ( $_POST ['client_id'], $_POST ['internal_assessor_id'], $_POST ['external_assessor_id'], $_POST ['diagnostic_id'], $_POST ['tier_id'], $_POST ['award_scheme_name'], $externalReviewTeam )) {
				
                             //if(!empty($externalReviewTeam))
                               
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["assessment_id"] = $aid;
				
				$this->apiResult ["message"] = "Review successfully created.";
			} else {
				
				$this->apiResult ["message"] = "Unable to create review.";
			}
		}
	}
	function editSchoolAssessmentAction() {
            $assessmentModel = new assessmentModel();
            $assessment = $assessmentModel->getSchoolAssessment($_POST ['assessment_id']);
            $diagnostic_id = $assessment['diagnostic_id'];
            $externalRevPerc = null;
            $internalRevPerc = null;
            $notificationStatus = 1;
            $rev = explode(',',$assessment['percCompletes']);
            if(count($rev)>1){   
                 $externalRevPerc = $rev[1];
                 $internalRevPerc = $rev[0];
            }            
             else
                 $externalRevPerc = $internalRevPerc = $assessment['percCompletes'];
           // echo $internalRevPerc; echo $_POST['internal_assessor_id'];
            //die;
               // echo"<pre>";print_r($_POST);
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['assessment_id'] )) {
			
			$this->apiResult ["message"] = "Assessment id cannot be empty.\n";
		} else if ($internalRevPerc==0 && empty ( $_POST ['internal_assessor_id'] )) {
			
			$this->apiResult ["message"] = "Internal reviewer cannot be empty.\n";
		} 
                else if ( ($internalRevPerc>0 ||$externalRevPerc>0) && (!empty ( $_POST ['internal_assessor_id'] )||!empty ( $_POST ['diagnostic_id'] ))) {
			$this->apiResult ["message"] = "Internal reviewer cannot be assigned after filling review.\n";
		}
                else if ( $externalRevPerc>0 && !empty ( $_POST ['external_assessor_id'] )) {
			$this->apiResult ["message"] = "External reviewer cannot be assigned after filling external-review.\n";
		}else if (empty ( $_POST ['school_aqs_pref_start_date'] )) {
			
			$this->apiResult ["message"] = "AQS Start Date cannot be empty.\n";
		}else if (empty ( $_POST ['school_aqs_pref_end_date'] )) {
			
			$this->apiResult ["message"] = "AQS End Date cannot be empty.\n";
		} else if ( empty ( $_POST ['diagnostic_lang'] )) {
			
			$this->apiResult ["message"] = "Diagnostic language cannot be empty.\n";
		} else if (!($internalRevPerc>0 ||$externalRevPerc>0) && empty ( $_POST ['diagnostic_id'] )) {
			
			$this->apiResult ["message"] = "Diagnostic cannot be empty.\n";
		} else if (empty ( $_POST ['tier_id'] )) {
			
			$this->apiResult ["message"] = "Tier cannot be empty.\n";
		} else if (empty ( $_POST ['award_scheme_name'] )) {
			
			$this->apiResult ["message"] = "Award Scheme cannot be empty.\n";
		} else if (empty ( $_POST ['aqs_round'] )) {
			$this->apiResult ["message"] = "AQS Round cannot be empty.\n";
                } else if (!empty($_POST ['external_assessor_id']) && !empty($_POST ['internal_assessor_id']) && $_POST ['internal_assessor_id'] == $_POST ['external_assessor_id']) {			
			$this->apiResult ["message"] = "Internal and External reviewer cannot be same.\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['role'] ) && empty ( $_POST ['externalReviewTeam'] ['role'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {			
			$this->apiResult ["message"] = "External reviewer role cannot be empty.\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['member'] ) && empty ( $_POST ['externalReviewTeam'] ['member'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {			
			$this->apiResult ["message"] = "External reviewer member cannot be empty.\n";
		} 
                else if( !in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && (!empty ( $_POST ['externalReviewTeam'] ['clientId'] ) || !empty($_POST ['external_assessor_id']) ) ){
                    $this->apiResult ["message"] = "You are not authorized to update external review team.\n";
                }else if(isset($_POST ['facilitatorReviewTeam'] ['member']) && count(array_unique($_POST ['facilitatorReviewTeam'] ['member']))<count($_POST ['facilitatorReviewTeam'] ['member'])) {
                         $this->apiResult ["message"] = "Cannot assign multiple role to same facilitator.\n";               
                }
		else if(isset($_POST ['facilitatorReviewTeam'] ['member']) && isset($_POST ['facilitator_id']) && in_array($_POST ['facilitator_id'],$_POST ['facilitatorReviewTeam'] ['member'])) {
                         $this->apiResult ["message"] = "Cannot assign multiple role to same facilitator.\n";               
                }/*else if(isset($_POST['notification_type']) && !empty($_POST['notifySett']) &&  empty($_POST['assessorLog'])) {
                     $this->apiResult ["message"] = "Please assign users to assessor log.\n"; 
                }*/ 
                else  {
                        
			//&& in_array ( "assign_external_review_team", $this->user ['capabilities'] )
                        if(isset($_POST['notifySett'])){
                        
                            foreach($_POST['notifySett'] as $data) {

                                if(count($data) == 1 && in_array(10,$data)){
                                     $this->apiResult ["message"] = "Invalid notification settings.Please change  notification settings\n";
                                     $notificationStatus = 0;
                                     break;                            
                                }

                            }
                             //echo"<pre>";print_r($_POST['notifySett']);
                         }
                       if (!empty($_POST['school_aqs_pref_end_date']) && !empty($_POST['school_aqs_pref_start_date'])) {
                        $eDate = explode("-", $_POST['school_aqs_pref_end_date']);
                        $sDate = explode("-", $_POST['school_aqs_pref_start_date']);
                        if ($eDate[2] < $sDate[2] || ($eDate[2] == $sDate[2] && $eDate[1] < $sDate[1]) || ($eDate[2] == $sDate[2] && $eDate[1] == $sDate[1] && $eDate[0] < $sDate[0]))
                            $this->apiResult ["message"] = "End date can't be less than Start date";
                        else if($notificationStatus) {
			$externalReviewTeam = '';
			
			$i = 0;
			
			$externalRoleClient = array ();
			
			// $externalRoleClient = array($_POST['externalReviewTeam']['clientId'],$_POST['externalReviewTeam']['role'],$_POST['externalReviewTeam']['member']);
			
			if (! empty ( $_POST ['externalReviewTeam'] ['clientId'] ) && in_array ( "assign_external_review_team", $this->user ['capabilities'] ) )
				
				foreach ( $_POST ['externalReviewTeam'] ['clientId'] as $client ) 

				{
					
					array_push ( $externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i] );
					
					$i ++;
				}
				
			$externalReviewTeam = empty ( $_POST ['externalReviewTeam'] ['clientId'] ) ? '' : array_combine ( $_POST ['externalReviewTeam'] ['member'], $externalRoleClient );
			
			// $externalReviewTeam = array_combine($_POST['externalReviewTeam']['member'],$_POST['externalReviewTeam']['role']);
			$facilitatorCount=0;

                        $facilitatorDataArray = array();

                        if(isset($_POST ['facilitatorReviewTeam'] ['clientId'])) {

                            foreach ($_POST ['facilitatorReviewTeam'] ['clientId'] as $client=>$val) {
                                //array_push($externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i]);
                                $facilitatorDataArray[$facilitatorCount]['client_id'] = $val;
                                $facilitatorDataArray[$facilitatorCount]['role_id'] = $_POST ['facilitatorReviewTeam'] ['role'][$facilitatorCount];
                                $facilitatorDataArray[$facilitatorCount]['user_id'] = $_POST ['facilitatorReviewTeam'] ['member'][$facilitatorCount];
                                $facilitatorCount++;
                            }
                        }
                        if(!empty($_POST['facilitator_client_id']) && !empty($_POST['facilitator_id'])) {

                            $facilitatorDataArray[$facilitatorCount]['client_id'] = $_POST['facilitator_client_id'];
                            $facilitatorDataArray[$facilitatorCount]['role_id'] = 1;
                            $facilitatorDataArray[$facilitatorCount]['user_id'] = $_POST['facilitator_id'];
                           // $facilitatorCount++;
                        }
			// echo"<pre>"; print_r($facilitatorDataArray);
			$assessmentModel = new assessmentModel ();			
			$this->db->start_transaction ();
                        $existing_assessor_id = explode(',',$assessment ['user_ids']);//$externalRevPerc
			$external_assessor_id = empty($_POST ['external_assessor_id'])?$existing_assessor_id[1]:$_POST ['external_assessor_id'];//
                        $internal_assessor_id = empty($_POST ['internal_assessor_id'])?$existing_assessor_id[0]:$_POST ['internal_assessor_id'];
                        $diagnostic_id = empty($_POST ['diagnostic_id'])?$diagnostic_id:$_POST ['diagnostic_id'];
                        $facilitator_id = empty($_POST ['facilitator_id'])?'':$_POST ['facilitator_id'];
                        $aqs_round = empty($_POST ['aqs_round'])?'':$_POST ['aqs_round'];
                        $aqsdata_id = $assessment['aqsdata_id'];
                        $notificationID = '';
                        $notificationsArray = array();
                        $notificationUsers = array();
                        $lang_id = '';
                        if(isset($_POST ['diagnostic_lang']) && !empty($_POST ['diagnostic_lang']) ) {
                            $lang_id = $_POST ['diagnostic_lang'];
                        }
                        /*if(isset($_POST['notifySett']) ) {
                            
                            $notificationsArray = $_POST['notifySett'];
                            
                        }
                        if(isset($_POST['notification_type']) ) {
                            
                            $notificationID = $_POST['notification_type'];
                            $notificationUsers = in_array($notificationID, $notificationsArray)?$_POST['assessorLog']:array();
                            
                            
                        }*/
                        if(isset($_POST['notifySett']) ) {
                            $notificationsArray = $_POST['notifySett'];
                            
                        }
                       
                        
                        //in_array ( "assign_external_review_team", $this->user ['capabilities'] )
                        $notificationOldUsers = $assessmentModel->getReviewTeamMembers($_POST ['assessment_id']);
                        //echo "<pre>";print_r($notificationOldUsers);die;
			if (!in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && $assessmentModel->updateSchoolAssessment ( $_POST ['assessment_id'], $internal_assessor_id, $external_assessor_id, $facilitator_id , $diagnostic_id, $_POST ['tier_id'], $_POST ['award_scheme_name'], $aqs_round, 0,$_POST['school_aqs_pref_start_date'],$_POST['school_aqs_pref_end_date'],$aqsdata_id,$facilitatorDataArray,$notificationID,$notificationsArray,$notificationUsers )) {				                           
                            $this->db->commit ();
				
				$this->apiResult ["status"] = 1;
				$this->apiResult ["assessment_id"] = $_POST ['assessment_id'];
				$this->apiResult ["message"] = "Review successfully updated";
			}
                        elseif ($assessmentModel->updateSchoolAssessment ( $_POST ['assessment_id'], $internal_assessor_id, $external_assessor_id, $facilitator_id, $diagnostic_id, $_POST ['tier_id'], $_POST ['award_scheme_name'], $aqs_round, $externalReviewTeam ,$_POST['school_aqs_pref_start_date'],$_POST['school_aqs_pref_end_date'],$aqsdata_id,$facilitatorDataArray,$notificationID,$notificationsArray,$notificationUsers,$lang_id,$_POST['review_criteria'])) {				
				$this->db->commit ();
				
				$this->apiResult ["status"] = 1;
				$this->apiResult ["assessment_id"] = $_POST ['assessment_id'];
				
				$this->apiResult ["message"] = "Review successfully updated";
                                if(!empty($externalReviewTeam))
                                 $this->updateReviewNotificationSettings($_POST ['assessment_id'],$externalReviewTeam,$external_assessor_id,$notificationOldUsers) ;
                               
			} else {
				$this->db->rollback();
				$this->apiResult ["message"] = "Unable to create review";
			}
                        }
                       }
		}
	}
        function updateReviewNotificationSettings($assessment_id,$externalReviewTeam,$external_assessor_id,$notificationOldUsers = array()){
            
            $assessmentModel = new assessmentModel();
            $notificationUsers = $assessmentModel->getReviewNotificationMembers($assessment_id);
          //  $notificationUsers = $assessmentModel->getReviewTeamMembers($assessment_id);
            $notificationOldUsers = array_unique(array_column($notificationOldUsers, 'user_id'));
           
            if(!empty($external_assessor_id))
                $externalReviewTeam[$external_assessor_id] = $external_assessor_id;
            
               // $notificationOldUsers[] = $external_assessor_id;
                //array_push($externalReviewTeam, $external_assessor_id);
            //$notificationUsers = empty($notificationUsers)?array_keys($externalReviewTeam):$notificationUsers;
            if(!empty($notificationUsers)) {
                $notificationTeam = array();
                if(!empty($notificationOldUsers)){
                    $notificationOldUsers[] = $external_assessor_id;
                    foreach($externalReviewTeam as $key=>$val) {

                        if(!in_array($key,$notificationOldUsers)) {
                            $notificationTeam[] = $key;
                        }
                    }
                }else{
                    $notificationTeam = array_keys($externalReviewTeam);
                }
            }else{
                $notificationTeam = array_keys($externalReviewTeam);
            }
           // print_r($notificationTeam);die;
            $notificationData = array();
            if(!empty($notificationTeam)) {
                $notifications = array_column($assessmentModel->getReviewNotifications(),'id');
                
                foreach($notificationTeam as $key=>$val) {
                    //$notificationData[] = $val;
                     $notificationData[$val] = $notifications;
                }
            }
             if(!empty($notificationData)) {
                return    $assessmentModel->addReviewNotificationSettings($assessment_id,$notificationData);
             }
            
            return false;
        }
        
        
        function addReviewNotificationSettings($assessment_id,$externalReviewTeam= array(),$external_assessor_id,$type=1){
            
            $assessmentModel = new assessmentModel();
            
            if(!empty($external_assessor_id))
                $externalReviewTeam[$external_assessor_id] = $external_assessor_id;
                //array_push($externalReviewTeam, $external_assessor_id);
            //$notificationUsers = empty($notificationUsers)?array_keys($externalReviewTeam):$notificationUsers;
                $notificationTeam = array();            
                $notificationTeam = array_keys($externalReviewTeam);
             //print_r($notificationTeam);die;
            $notificationData = array();
            if(!empty($notificationTeam)) {
                $notifications = array_column($assessmentModel->getReviewNotifications($type),'id');
                
                foreach($notificationTeam as $key=>$val) {
                    //$notificationData[] = $val;
                     $notificationData[$val] = $notifications;
                }
            }
             if(!empty($notificationData)) {
                return    $assessmentModel->addReviewNotificationSettings($assessment_id,$notificationData);
             }
            
            return false;
        }
        
        function editSchoolAssessmentNotificationSettingsAction() {
            
            $assessmentModel = new assessmentModel();
            $assessment = $assessmentModel->getSchoolAssessment($_POST ['assessment_id']);
            $diagnostic_id = $assessment['diagnostic_id'];
            $notificationValidationStatus = 1;
            $reminderValidationStatus = 1;
            $notificationsArray = array();
            $reminderArray = array();
           // echo "<pre>";print_r($_POST);die;
            if(isset($_POST['notifySett']) ) {
               // $rowNum = 1;
                foreach($_POST['notifySett'] as $data){
                    if(count($data) == 1 && in_array(10, $data)) {
                            $this->apiResult ["message"] = "Sorry! You are selecting Invalid notification option ";
                            $notificationValidationStatus = 0;
                    }
                   // $rowNum++;
                }
                if($notificationValidationStatus)
                    $notificationsArray = $_POST['notifySett'];

            }
            if(isset($_POST['remindrSett']) ) {
               // $rowNum = 1;
                foreach($_POST['remindrSett'] as $data){
                    if(count($data) == 1 && in_array(10, $data)) {
                            $this->apiResult ["message"] = "Sorry! You are selecting Invalid reminder option ";
                            $reminderValidationStatus = 0;
                    }
                   // $rowNum++;
                }
                if($reminderValidationStatus)
                    $reminderArray = $_POST['remindrSett'];

            }

           // print_r($reminderArray);
            if($notificationValidationStatus && $reminderValidationStatus) {
                
                    if(isset($_POST['remindrSett'])){
                        //print_r($_POST['remindrSett']);
                        foreach($_POST['remindrSett'] as $key=>$data){
                            if(!isset($_POST['notifySett'][$key]) && isset($_POST['remindrSett'][$key])) {
                                    $this->apiResult ["message"] = "Sorry! You are selecting Invalid notification option ";
                                    $reminderValidationStatus = 0;
                            }else if(count($_POST['remindrSett'][$key]) > count($_POST['notifySett'][$key])) {
                                $this->apiResult ["message"] = "Sorry! You are selecting Invalid notification option ";
                                 $reminderValidationStatus = 0;
                            }
                           // $rowNum++;
                        }
                    }
            }

            if($notificationValidationStatus && $reminderValidationStatus) {
                    $notificationUsers = array();
                    $remSheetData = array();
                    if(!empty($notificationsArray)) {

                        $notificationUsers = array_keys($notificationsArray);
                    }
                    if(!empty($notificationUsers)) {
                        foreach($notificationUsers as $key=>$val) {
                            if(isset($_POST['reim_sheet_'.$val]) && ($_POST['reim_sheet_'.$val] == 1 || $_POST['reim_sheet_'.$val] == 0)) 
                                 $remSheetData[$val] = $_POST['reim_sheet_'.$val];
                        }
                    }
                    $observers = isset($_POST['obsNotif'])?array_unique($_POST['obsNotif']):array();
                    if(!empty($observers)) {
                        foreach($observers as $key=>$val){
                            $notificationsArray[$val] = array(9,10); 
                        }
                    }
                    //echo "<pre>";print_r( $notificationsArray);die;
                    $this->db->start_transaction ();
                    if ( $assessmentModel->updateNotificationSettings ( $_POST ['assessment_id'],$notificationsArray,1 ) && $assessmentModel->updateNotificationSettings ( $_POST ['assessment_id'],$reminderArray,2 )) {				                           
                            
                            if(!empty($remSheetData))
                                  $assessmentModel->updateReimSheetSettings( $_POST ['assessment_id'],$remSheetData);
                            $this->db->commit();
                            $this->apiResult ["status"] = 1;

                            $this->apiResult ["message"] = "Review notifications settings updated successfully";
                    }else {
                         $this->db->rollback();
                          $this->apiResult ["message"] = "Unable to update review notifications settings ";
                    }
            }
           
            //echo "<pre>";print_r($_POST);
            
        }
        
        function editCollegeAssessmentAction() {
            $assessmentModel = new assessmentModel();
            $assessment = $assessmentModel->getSchoolAssessment($_POST ['assessment_id']);
            //print_r($assessment);
            $diagnostic_id = $assessment['diagnostic_id'];
            $externalRevPerc = null;
            $internalRevPerc = null;
            $rev = explode(',',$assessment['percCompletes']);
            if(count($rev)>1){   
                 $externalRevPerc = $rev[1];
                 $internalRevPerc = $rev[0];
            }            
             else
                 $externalRevPerc = $internalRevPerc = $assessment['percCompletes'];
           // echo $internalRevPerc; echo $_POST['internal_assessor_id'];
            //die;
             
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['assessment_id'] )) {
			
			$this->apiResult ["message"] = "Assessment id cannot be empty.\n";
		} else if ($internalRevPerc==0 && empty ( $_POST ['internal_assessor_id'] )) {
			
			$this->apiResult ["message"] = "Internal reviewer cannot be empty.\n";
		} 
                else if ( ($internalRevPerc>0 ||$externalRevPerc>0) && (!empty ( $_POST ['internal_assessor_id'] )||!empty ( $_POST ['diagnostic_id'] ))) {
			$this->apiResult ["message"] = "Internal reviewer cannot be assigned after filling review.\n";
		}
                else if ( $externalRevPerc>0 && !empty ( $_POST ['external_assessor_id'] )) {
			$this->apiResult ["message"] = "External reviewer cannot be assigned after filling external-review.\n";
		}else if (empty ( $_POST ['school_aqs_pref_start_date'] )) {
			
			$this->apiResult ["message"] = "AQS Start Date cannot be empty.\n";
		}else if (empty ( $_POST ['school_aqs_pref_end_date'] )) {
			
			$this->apiResult ["message"] = "AQS End Date cannot be empty.\n";
		} else if (!($internalRevPerc>0 ||$externalRevPerc>0) && empty ( $_POST ['diagnostic_id'] )) {
			
			$this->apiResult ["message"] = "Diagnostic cannot be empty.\n";
		} /*else if (empty ( $_POST ['tier_id'] )) {
			
			$this->apiResult ["message"] = "Tier cannot be empty.\n";
		} else if (empty ( $_POST ['award_scheme_name'] )) {
			
			$this->apiResult ["message"] = "Award Scheme cannot be empty.\n";
		}*/ else if (empty ( $_POST ['aqs_round'] )) {
			$this->apiResult ["message"] = "AQS Round cannot be empty.\n";
                } else if (!empty($_POST ['external_assessor_id']) && !empty($_POST ['internal_assessor_id']) && $_POST ['internal_assessor_id'] == $_POST ['external_assessor_id']) {			
			$this->apiResult ["message"] = "Internal and External reviewer cannot be same.\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['role'] ) && empty ( $_POST ['externalReviewTeam'] ['role'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {			
			$this->apiResult ["message"] = "External reviewer role cannot be empty.\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['member'] ) && empty ( $_POST ['externalReviewTeam'] ['member'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {			
			$this->apiResult ["message"] = "External reviewer member cannot be empty.\n";
		} 
                else if( !in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && (!empty ( $_POST ['externalReviewTeam'] ['clientId'] ) || !empty($_POST ['external_assessor_id']) ) ){
                    $this->apiResult ["message"] = "You are not authorized to update external review team.\n";
                }else if(isset($_POST ['facilitatorReviewTeam'] ['member']) && count(array_unique($_POST ['facilitatorReviewTeam'] ['member']))<count($_POST ['facilitatorReviewTeam'] ['member'])) {
                         $this->apiResult ["message"] = "Cannot assign multiple role to same facilitator.\n";               
                }
		else if(isset($_POST ['facilitatorReviewTeam'] ['member']) && isset($_POST ['facilitator_id']) && in_array($_POST ['facilitator_id'],$_POST ['facilitatorReviewTeam'] ['member'])) {
                         $this->apiResult ["message"] = "Cannot assign multiple role to same facilitator.\n";               
                }else if (empty ( $_POST ['diagnostic_lang'] )) {
			$this->apiResult ["message"] = "Diagnostic language cannot be empty.\n";
                }
                else {
			//&& in_array ( "assign_external_review_team", $this->user ['capabilities'] )
                       if (!empty($_POST['school_aqs_pref_end_date']) && !empty($_POST['school_aqs_pref_start_date'])) {
                        $eDate = explode("-", $_POST['school_aqs_pref_end_date']);
                        $sDate = explode("-", $_POST['school_aqs_pref_start_date']);
                        if ($eDate[2] < $sDate[2] || ($eDate[2] == $sDate[2] && $eDate[1] < $sDate[1]) || ($eDate[2] == $sDate[2] && $eDate[1] == $sDate[1] && $eDate[0] < $sDate[0]))
                            $this->apiResult ["message"] = "End date can't be less than Start date";
                        else {
			$externalReviewTeam = '';
			
			$i = 0;
			
			$externalRoleClient = array ();
			
			// $externalRoleClient = array($_POST['externalReviewTeam']['clientId'],$_POST['externalReviewTeam']['role'],$_POST['externalReviewTeam']['member']);
			
			if (! empty ( $_POST ['externalReviewTeam'] ['clientId'] ) && in_array ( "assign_external_review_team", $this->user ['capabilities'] ) )
				
				foreach ( $_POST ['externalReviewTeam'] ['clientId'] as $client ) 

				{
					
					array_push ( $externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i] );
					
					$i ++;
				}
				
			$externalReviewTeam = empty ( $_POST ['externalReviewTeam'] ['clientId'] ) ? '' : array_combine ( $_POST ['externalReviewTeam'] ['member'], $externalRoleClient );
			
			// $externalReviewTeam = array_combine($_POST['externalReviewTeam']['member'],$_POST['externalReviewTeam']['role']);
			$facilitatorCount=0;
                        $facilitatorDataArray=array();
                        if(isset($_POST ['facilitatorReviewTeam'] ['clientId'])) {

                            foreach ($_POST ['facilitatorReviewTeam'] ['clientId'] as $client=>$val) {
                                //array_push($externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i]);
                                $facilitatorDataArray[$facilitatorCount]['client_id'] = $val;
                                $facilitatorDataArray[$facilitatorCount]['role_id'] = $_POST ['facilitatorReviewTeam'] ['role'][$facilitatorCount];
                                $facilitatorDataArray[$facilitatorCount]['user_id'] = $_POST ['facilitatorReviewTeam'] ['member'][$facilitatorCount];
                                $facilitatorCount++;
                            }
                        }
                        if(!empty($_POST['facilitator_client_id']) && !empty($_POST['facilitator_id'])) {

                            $facilitatorDataArray[$facilitatorCount]['client_id'] = $_POST['facilitator_client_id'];
                            $facilitatorDataArray[$facilitatorCount]['role_id'] = 1;
                            $facilitatorDataArray[$facilitatorCount]['user_id'] = $_POST['facilitator_id'];
                           // $facilitatorCount++;
                        }
			// echo"<pre>"; print_r($facilitatorDataArray);
			$assessmentModel = new assessmentModel ();			
			$this->db->start_transaction ();
                        $existing_assessor_id = explode(',',$assessment ['user_ids']);//$externalRevPerc
			$external_assessor_id = empty($_POST ['external_assessor_id'])?$existing_assessor_id[1]:$_POST ['external_assessor_id'];//
                        $internal_assessor_id = empty($_POST ['internal_assessor_id'])?$existing_assessor_id[0]:$_POST ['internal_assessor_id'];
                        $diagnostic_id = empty($_POST ['diagnostic_id'])?$diagnostic_id:$_POST ['diagnostic_id'];
                        $facilitator_id = empty($_POST ['facilitator_id'])?'':$_POST ['facilitator_id'];
                        $aqs_round = empty($_POST ['aqs_round'])?'':$_POST ['aqs_round'];
                        $aqsdata_id = $assessment['aqsdata_id'];
                        //in_array ( "assign_external_review_team", $this->user ['capabilities'] )
			if (!in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && $assessmentModel->updateSchoolAssessment ( $_POST ['assessment_id'], $internal_assessor_id, $external_assessor_id, $facilitator_id , $diagnostic_id, NULL, NULL, $aqs_round, 0,$_POST['school_aqs_pref_start_date'],$_POST['school_aqs_pref_end_date'],$aqsdata_id,$facilitatorDataArray,0,array(),array(),$_POST ['diagnostic_lang'] )) {				                           
                            $this->db->commit ();
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["message"] = "Review successfully updated";
			}
                        elseif ($assessmentModel->updateSchoolAssessment ( $_POST ['assessment_id'], $internal_assessor_id, $external_assessor_id, $facilitator_id, $diagnostic_id, NULL, NULL, $aqs_round, $externalReviewTeam ,$_POST['school_aqs_pref_start_date'],$_POST['school_aqs_pref_end_date'],$aqsdata_id,$facilitatorDataArray,0,array(),array(),$_POST ['diagnostic_lang'])) {				
				$this->db->commit ();
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["message"] = "Review successfully updated";
			} else {
				$this->db->rollback();
				$this->apiResult ["message"] = "Unable to create review";
			}
                        }
                       }
		}
	}
        
	function createSchoolSelfAssessmentAction() {
		$admin_role=0;
                if(in_array(1,$this->user['role_ids'])||in_array(2,$this->user['role_ids'])){
                $admin_role=1;    
                }
                $guest_user=(isset($this->user['is_guest']) && $this->user['is_guest']==1)?1:0;
		// print_r($_POST);die;
		if (! in_array ( "create_self_review", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty.\n";
		} else if (empty ( $_POST ['reviewtype'] ) || intval ( $_POST ['reviewtype'] ) < 1) {
			
			$this->apiResult ["message"] = "Review Type cannot be empty.\n";
		} else if (empty ( $_POST ['internal_assessor_id'] )) {
			
			$this->apiResult ["message"] = "Internal reviewer cannot be empty.\n";
		} else if (empty ( $_POST ['diagnostic_id'] )) {
			
			$this->apiResult ["message"] = "Diagnostic cannot be empty.\n";
		} else if (empty ( $_POST ['tier_id'] )) {
			
			$this->apiResult ["message"] = "Tier cannot be empty.\n";
		} else if (empty ( $_POST ['award_scheme_name'] )) {
			
			$this->apiResult ["message"] = "Award Scheme cannot be empty.\n";
		} else {
                    
			
			$clientId = $admin_role==1?$_POST ['client_id']:$this->user ['client_id'];
                        
			
			$reviewType = 1;
			
			$assessmentModel = new assessmentModel ();
			
			//$ReviewTypeProductsAvailed = $assessmentModel->getReviewTypeProductsAvailed ( 1, $clientId );
			
			/*foreach ( $ReviewTypeProductsAvailed as $availedProduct ) 

			{
				
				// print_r($availedProduct);die;
				
				if ($availedProduct ['active'] <= 0 || $availedProduct ['isPmtApproved'] <= 0) 

				{
					
					$this->apiResult ["message"] = "You are not allowed to create more reviews. Please pay.";
					
					return;
				}
			}*/
			//echo "<pre>";print_r($_POST);die;
			$this->db->start_transaction ();
			$isApproved = ($admin_role==1 || $guest_user==1)?1:0;
			if ($aid = $assessmentModel->createSchoolSelfAssessment ( $clientId, $_POST ['internal_assessor_id'], $_POST ['diagnostic_id'], $_POST ['tier_id'], $_POST ['award_scheme_name'], $reviewType,$isApproved,$_POST ['diagnostic_lang'] )) {
				
				$subAssessmentType_id = 1; // self-review				
				//$pastTransactions = $assessmentModel->getClientProducts ( $clientId );												
				// user has bought one or more packages in the past
				
				/*foreach ( $pastTransactions as $transaction ) 

				{
					
					if ($transaction ['selfRevsLeft'] > 0) 

					{
						
						// $transaction_row_id = $transaction['transaction_row_id'];
						
						$transaction_id = $transaction ['transaction_id'];
						
						// $reviewIds = $pastReviews.",".$aid;
						
						// die("here");
						
						if ($assessmentModel->updateClientTransaction ( $transaction_id, $aid )) 

						{
							
							$this->db->commit ();
							
							$this->apiResult ["assessment_id"] = $aid;
							
							$this->apiResult ["status"] = 1;
							
							$this->apiResult ["message"] = "Review saved successfully.";
							
							return;
						} 

						else 

						{
							
							$this->db->rollback ();
							
							$this->apiResult ["message"] = "Payment could not be saved.";
							
							return;
						}
					}
				}
				*/
				$this->db->commit ();
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["assessment_id"] = $aid;
				$this->apiResult ["message"]=($admin_role==1 || $guest_user==1)?"Review successfully created":"Review successfully created and pending for approval.<br>You can start the review process once it will get approved from Adhyayan.";
			} else {
				
				$this->db->rollback ();
				
				$this->apiResult ["message"] = "Unable to create review.";
			}
		}
	}
        
        function getSelfReviewDataAction(){
                $admin_role=0;
                if(in_array(1,$this->user['role_ids'])||in_array(2,$this->user['role_ids'])){
                $admin_role=1;    
                }                
                if (! (in_array ( "create_self_review", $this->user ['capabilities'] ) || in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		}else{
                    $clientId=$_POST['client_id'];
                    $diagnosticModel=new diagnosticModel();
                    $assessmentModel=new assessmentModel();
                    $clientReviews=$assessmentModel->getClientReviews($clientId);
		    
                    //print_r($clientReviews);
                    $selfReviewsPast = 0;
                    $validatedReviews = 0; 
                    $lastReviewSettings = array();
                    $aqs_status=array();
                    $review_status=array();
                    $previous_status=0;
                                if(!empty($clientReviews))
                                        foreach($clientReviews as $review)
                                        {
                                                $review['sub_assessment_type']==1 ? $selfReviewsPast++ : '';
                                                if($review['isPublished']==1 && $review['sub_assessment_type']!=1) 
                                                {
                                                        $validatedReviews++ ;
                                                        $lastReviewSettings = $review;
                                                }
                                                
                                                if($review['sub_assessment_type']==1 && $review['is_approved']!=2){
                                                if($review['filledStatus']!=1){
                                                    $aqs_status[]=$review['assessment_id'];
                                                }
                                                if($review['AQS_status']!=1){
                                                    $review_status[]=$review['assessment_id'];
                                                }
                                                }

                                        }
                                        
                    $previous_status=(count($aqs_status)>0 || count($review_status)>0)?1:0;
                    if(!$previous_status){
                    if($selfReviewsPast==0){
                            if($validatedReviews==0){
                            $this->apiResult ["diagnostic"]=$diagnosticModel->getFirstDefaultDiagnosticDrop();
                            $this->apiResult ["awardScheme_id"]=1;
                            $this->apiResult ["awardScheme"]="";
                            $this->apiResult ["tire_id"]=3;
                            $this->apiResult ["tire"]="";
                            $this->apiResult ["type"]="00";
                            }else{
                            $last_review_diagnostic=isset($lastReviewSettings['diagnostic_id'])?$lastReviewSettings['diagnostic_id']:0;
                            $lastandfreediagnostic=$diagnosticModel->getFreeAndLastDiagnostic($last_review_diagnostic);
                            $this->apiResult ["diagnostic"]=$lastandfreediagnostic;
                            $this->apiResult ["awardScheme_id"]=$lastReviewSettings['award_scheme_id'];
                            $this->apiResult ["awardScheme"]=$assessmentModel->getAwardSchemeById($lastReviewSettings['award_scheme_id']);
                            $this->apiResult ["tire_id"]=$lastReviewSettings['tier_id'];
                            $this->apiResult ["tire"]=$assessmentModel->getTierById($lastReviewSettings['tier_id']);
                            $this->apiResult ["type"]="01";
                            }
                    } elseif($selfReviewsPast>0){
                            $this->apiResult ["awardScheme_id"]=1;
                            $this->apiResult ["awardScheme"]="";
                            $this->apiResult ["tire_id"]=3;
                            $this->apiResult ["tire"]="";
                            
                            if($validatedReviews==0){
                            $this->apiResult ["diagnostic"]=$diagnosticModel->getFreeDiagnostic();
                            $this->apiResult ["type"]="10";    
                            }else{
                            $last_review_diagnostic=isset($lastReviewSettings['diagnostic_id'])?$lastReviewSettings['diagnostic_id']:0;
                            $lastandfreediagnostic=$diagnosticModel->getFreeAndLastDiagnostic($last_review_diagnostic);
                            $this->apiResult ["diagnostic"]=$lastandfreediagnostic;    
                            $this->apiResult ["type"]="11";  
                            }
                                
                        
                    }          
                            $this->apiResult ["previous_status"] = $previous_status;    
                            $this->apiResult ["status"] = 1;  
                    }else{
                            $this->apiResult ["status"] = 0;
                            $this->apiResult ["previous_status"] = $previous_status;
                            $this->apiResult ["message"] = "Some of the reviews of the selected organization are still pending. Please complete the existing pending review in order to generate new self-review";
                    }
                    
                }
        }
        
	function editSchoolSelfAssessmentAction() {
		$admin_role=0;
                if(in_array(1,$this->user['role_ids'])||in_array(2,$this->user['role_ids'])){
                $admin_role=1;    
                }
		// print_r($_POST);die;
		if (! (in_array ( "create_self_review", $this->user ['capabilities'] ) || in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['assessment_id'] )) {
			
			$this->apiResult ["message"] = "Assessment id cannot be empty.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty.\n";
		} else if (empty ( $_POST ['internal_assessor_id'] )) {
			
			$this->apiResult ["message"] = "Internal reviewer cannot be empty.\n";
		} else if (empty ( $_POST ['diagnostic_id'] )) {
			
			$this->apiResult ["message"] = "Diagnostic cannot be empty.\n";
		} else if (empty ( $_POST ['tier_id'] )) {
			
			$this->apiResult ["message"] = "Tier cannot be empty.\n";
		} else if (empty ( $_POST ['award_scheme_name'] )) {
			
			$this->apiResult ["message"] = "Award Scheme cannot be empty.\n";
		} else if (empty ( $_POST ['diagnostic_lang'] )) {
			
			$this->apiResult ["message"] = "Diagnostic preferred language cannot be empty.\n";
		} else {
			
			 //print_r($_POST);die;
			
			$clientId = $_POST ['client_id'];
			
			$assessmentId = $_POST ['assessment_id'];
			$langId = $_POST ['diagnostic_lang'];
			
			$reviewType = 1;
			
			$assessmentModel = new assessmentModel ();
			
			$this->db->start_transaction ();
			
			if ($assessmentModel->updateSchoolSelfAssessment ( $assessmentId, $clientId, $_POST ['internal_assessor_id'], $_POST ['diagnostic_id'], $_POST ['tier_id'], $_POST ['award_scheme_name'],$langId )) {
				
				$subAssessmentType_id = 1; // self-review
				
				//$pastTransactions = $assessmentModel->getClientProducts ( $clientId );
				
				$this->db->commit ();
				
				$this->apiResult ["status"] = 1;
				
				// $this->apiResult["assessment_id"]=$aid;
				//$this->apiResult ["message"]=($admin_role==1)?"Review successfully updated":"Review successfully created and pending for approval.<br>You can start the review process once it will get approved from Adhyayan.";
                                $this->apiResult ["message"]="Review successfully updated";
				//$this->apiResult ["message"] = "Review successfully updated and pending for approval.<br>You can start the review process once it will get approved from Adhyayan.";
			} else {
				
				$this->db->rollback ();
				
				$this->apiResult ["message"] = "Unable to update review.";
			}
		}
	}
	function saveClientProductAction() {
		if (! in_array ( "create_self_review", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} 

		else if (empty ( $_POST ['assessment_id'] )) {
			
			$this->apiResult ["message"] = "Assessment Id cannot be empty.\n";
		} 

		else if (empty ( $_POST ['product'] )) {
			
			$this->apiResult ["message"] = "Product Id cannot be empty.\n";
		} 

		else if (empty ( $_POST ['payment_mode_id'] )) {
			
			$this->apiResult ["message"] = "Payment Mode cannot be empty.\n";
		} 

		else if (empty ( $_POST ['transactionId'] )) {
			
			$this->apiResult ["message"] = "Transanction Id cannot be empty.\n";
		} 

		else {
			
			$clientId = $this->user ['client_id'];
			
			$paymentModeId = $_POST ['payment_mode_id'];
			
			$transactionId = $_POST ['transactionId'];
			
			$productId = $_POST ['product'];
			
			$reviewId = $_POST ['assessment_id'];
			
			$transStatus = 'pass';
			
			$date = date ( 'Y-m-d' );
			
			$subAssessmentType_id = 1; // self-review
			
			$assessmentModel = new assessmentModel ();
			
			$pastTransactions = $assessmentModel->getClientProducts ( $clientId, 1 );
			
			$currentProduct = $assessmentModel->getProducts ( 1, $productId );
			
			$num_reviews = $currentProduct ['self_reviews'];
			
			$this->db->start_transaction ();
			
			if ($paymentModeId == 1 && $assessmentModel->saveClientProduct ( $clientId, $productId, $transactionId, $transStatus, $date, $paymentModeId, 1 ) && $assessmentModel->updateClientTransaction ( $transactionId, $reviewId )) 

			{
				
				$this->db->commit ();
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["message"] = "Payment transaction completed successfully.\n";
			} 

			else if ($paymentModeId == 2 && $assessmentModel->saveClientProduct ( $clientId, $productId, $transactionId, $transStatus, $date, $paymentModeId, 0 ) && $assessmentModel->updateClientTransaction ( $transactionId, $reviewId )) 

			{
				
				$this->db->commit ();
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["message"] = "Your payment will be approved by admin. Please contact admin.\n";
			} 

			else 

			{
				
				$this->db->rollback ();
				
				$this->apiResult ["message"] = "Payment could not be saved.\n";
			}
		}
	}
	function approveReviewAction() {
		if (empty ( $_POST ['assessmentId'] )) 				
			$this->apiResult ["message"] = "Review Id can not be empty.\n";		
                elseif(!isset($_POST['isApproved']))
                    $this->apiResult ["message"] = "Approval can not be empty.\n";
                elseif(isset($_POST['isApproved']) && $_POST['isApproved']==2 && empty($_POST['reason']))
                    $this->apiResult ["message"] = "Reason cannot be empty if you want to reject the review.\n";
		else if (! in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] )) {			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} 
		else {
			
			$reviewId = $_POST ['assessmentId'];
                        $isApproved = $_POST ['isApproved'];
			$reason = $_POST['reason'];
			$assessmentModel = new assessmentModel ();
			$this->db->start_transaction();
			if ($assessmentModel->updateReviewApproval ( $reviewId,$isApproved,$reason)) 
			{
                                if($isApproved==2){//send email to user for rejection
                                    $clientId = $this->user ['client_id'];
                                    $clientModel = new clientModel ();			
                                    $schoolName = $clientModel->getClientById ( $clientId );
                                    $schoolName = $schoolName ['client_name'];
                                    $assessmentModel->saveSubAssessmentTypeRequest ( 1, $this->user ['user_id'] );						
                                    $toEmail = $this->user ['email'];
                                    $toName = $this->user ['name'];
                                    $ccEmail = 'poonam.choksi@adhyayan.asia'; // 'nisha@a-insight.com';
                                    $fromEmail = 'shraddha.adhyayan@gmail.com'; // 'nisha@a-insight.com';
                                    $fromName = 'Shraddha Khedekar'; 
                                    $subject = 'Online Self-Review Rejected';
                                    $body = "Dear $toName, <br><br><br>The Online Self-Review created by you has been rejected."
                                            .ucfirst($reason). ".<br><br><br>Thanks<br/>Adhyayan Admin";
                                    if (! sendEmail($fromEmail,$fromName,$toEmail,$toName,$subject,$body,$ccEmail)) {	
                                            $this->db->rollback();
                                            $this->apiResult ["message"] = "Error occured while sending email.";
                                            return;
                                    } else {
                                            $this->db->commit();
                                            $this->apiResult ["message"] = "Email with reason for rejection sent to the user.";
                                            $this->apiResult ["status"] = 1;
                                            return;
                                    }
                                }
                                $this->db->commit();
				$this->apiResult ["status"] = 1;				
				$this->apiResult ["message"] = "The review has been updated.\n";
			} 

			else {
				$this->db->rollback();
				$this->apiResult ["message"] = "There was an error in updating the review.\n";
			}
		}
	}
	function createNetworkAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( trim($_POST ['name']) )) {
			
			$this->apiResult ["message"] = "Network Name cannot be empty.\n";
		} else {
			
			$name = trim ( $_POST ['name'] );
			//$province_name[] =  $_POST ['province_name'];
			$networkModel = new networkModel ();
			//print_r($province_name);die;			
			if (!empty($networkModel->getNetworkByClientName ( $name )) )			
				$this->apiResult ["message"] = "Network Name already exists\n";
			else{
				$pid=null;$nid=null;					
				$this->db->start_transaction();
				if ($nid = $networkModel->createNetwork ( $name )) {
					
						/* foreach($_POST['province_name'] as $key=>$v){
						if(!empty($v) && !($networkModel->getProvinceByName( $v )) && ($pid = $networkModel->createProvince ( $v )) && $networkModel->addProvinceToNetwork($pid,$nid));
						else{
							$this->apiResult ["message"] = "Province name is blank or already exists";
							$this->db->rollback();
							return;	
							}							
						} */
						if (OFFLINE_STATUS == TRUE) {
						$uniqueID = $this->db->createUniqueID ( 'addNetwork' );
						// start---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
						$action_json = json_encode ( array (
								'network_name' => $name 
						) );
						$this->db->saveHistoryData ( $nid, 'd_network', $uniqueID, 'addNetwork', $nid, $name, $action_json, 0, date ( 'Y-m-d H:i:s' ) );
						// end---> call function for saving add school client data on 04-03-2016 by Mohit Kumar						
					}						
					
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["assessment_id"] = $nid;
					
					$this->apiResult ["message"] = "Network successfully added";
					$this->db->commit();
					
				} else {
					
					$this->apiResult ["message"] = "Unable to add network\n";
					$this->db->rollback();
				}
			}
		}
	}
	function addProvinceFieldAction(){
		if (! in_array ( "create_network", $this->user ['capabilities'] ))			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";		
		else{
			$networkModel = new networkModel();
			$html = $networkModel->getProvinceField();
			$this->apiResult ["content"] = $html;
			$this->apiResult ["status"] = 1;
		}
	}
	function updateNetworkAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['id'] )) {
			
			$this->apiResult ["message"] = "Network ID missing.\n";
		} else if (empty ( trim($_POST ['name']) )) {
			
			$this->apiResult ["message"] = "Network Name cannot be empty.\n";
		} else {
			
			$networkModel = new networkModel ();
			
			$name = trim ( $_POST ['name'] );
			
			$network_id = trim ( $_POST ['id'] );
			
			$network = $networkModel->getNetworkById ( $network_id );
			
			if (empty ( $network )) {
				
				$this->apiResult ["message"] = "Network does not exist.\n";
			} else if ($networkModel->getNetworkByClientName ( $name, $network_id )) {
				
				$this->apiResult ["message"] = "Network Name already exists.\n";
			} else {
				
				if ($networkModel->updateNetwork ( $network_id, $name )) {
					
					if (OFFLINE_STATUS == TRUE) {
						$uniqueID = $this->db->createUniqueID ( 'updateNetwork' );
						// start---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
						$action_json = json_encode ( array (
								'network_name' => $name 
						) );
						$this->db->saveHistoryData ( $network_id, 'd_network', $uniqueID, 'updateNetwork', $network_id, $name, $action_json, 0, date ( 'Y-m-d H:i:s' ) );
						// end---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
					}
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["message"] = "Network successfully updated.";
				} else {
					
					$this->apiResult ["message"] = "Unable to update network.\n";
				}
			}
		}
	}
	function updateProvinceAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] )) {
				
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['id'] )) {
				
			$this->apiResult ["message"] = "Province ID missing.\n";
		} else if (empty ( trim($_POST ['name']) )) {
				
			$this->apiResult ["message"] = "Province Name cannot be empty.\n";
		} else {
				
			$networkModel = new networkModel ();
				
			$name = trim ( $_POST ['name'] );
				
			$province_id = trim ( $_POST ['id'] );
				
			$province = $networkModel->getProvinceById ( $province_id );
				
			if (empty ( $province )) {
	
				$this->apiResult ["message"] = "Province does not exist.\n";
			} else if ($networkModel->getProvinceByName ( $name, $province_id )) {
	
				$this->apiResult ["message"] = "Province Name already exists.\n";
			} else {
	
				if ($networkModel->updateProvince ( $province_id, $name )) {
						
					if (OFFLINE_STATUS == TRUE) {
						$uniqueID = $this->db->createUniqueID ( 'updateProvince' );
						// start---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
						$action_json = json_encode ( array (
								'province_name' => $name
						) );
						$this->db->saveHistoryData ( $network_id, 'd_province', $uniqueID, 'updateProvince', $province_id, $name, $action_json, 0, date ( 'Y-m-d H:i:s' ) );
						// end---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
					}
						
					$this->apiResult ["status"] = 1;
						
					$this->apiResult ["message"] = "Province successfully updated.";
				} else {
						
					$this->apiResult ["message"] = "Unable to update network.\n";
				}
			}
		}
	}
	function removeClientFromNetworkAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['network_id'] )) {
			
			$this->apiResult ["message"] = "Network ID missing\n";
		}
		if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School ID missing\n";
		} else {
			
			$clientModel = new clientModel ();
			
			$network_id = $_POST ['network_id'];
			
			$client_id = $_POST ['client_id'];
			$this->db->start_transaction();
			if ($clientModel->removeClientFromNetworkProvince($client_id)  && $clientModel->removeClientFromNetwork ( $client_id, $network_id )&& $this->db->commit()) {
				
				if (OFFLINE_STATUS == TRUE) {
					$clientUniqueID = $this->db->createUniqueID ( 'removeNetworkSchool' );
					// start---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
					$action_network_json = json_encode ( array (
							'client_id' => $client_id,
							'network_id' => $network_id 
					) );
					$this->db->saveHistoryData ( $client_id, 'h_client_network', $clientUniqueID, 'removeNetworkSchool', $client_id, $network_id, $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
					// end---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
				}
				
				if (OFFLINE_STATUS == TRUE) {
					$clientUniqueID = $this->db->createUniqueID ( 'removeProvinceSchool' );
					// start---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
					$action_network_json = json_encode ( array (
							'client_id' => $client_id,
							'client_id' => $client_id
					) );
					$this->db->saveHistoryData ( $client_id, 'h_client_province', $clientUniqueID, 'removeProvinceSchool', $client_id, $client_id, $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
					// end---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
				}
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["message"] = "School successfully removed from network.";
			} else {
				$this->db->rollback();
				$this->apiResult ["message"] = "Unable to remove school from network\n";
			}
		}
	}
	function removeClientFromProvinceAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] )) {
				
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['province_id'] )) {
				
			$this->apiResult ["message"] = "Province ID missing\n";
		}
		if (empty ( $_POST ['client_id'] )) {
				
			$this->apiResult ["message"] = "School ID missing\n";
		} else {
				
			$clientModel = new clientModel ();
				
			$province_id = $_POST ['province_id'];
				
			$client_id = $_POST ['client_id'];
				
			if ($clientModel->removeClientFromProvince ( $client_id, $province_id )) {
	
				if (OFFLINE_STATUS == TRUE) {
					$clientUniqueID = $this->db->createUniqueID ( 'removeProvinceSchool' );
					// start---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
					$action_network_json = json_encode ( array (
							'client_id' => $client_id,
							'province_id' => $province_id
					) );
					$this->db->saveHistoryData ( $client_id, 'h_client_network', $clientUniqueID, 'removeProvinceSchool', $client_id, $province_id, $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
					// end---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
				}
	
				$this->apiResult ["status"] = 1;
	
				$this->apiResult ["message"] = "School successfully removed from province.";
			} else {
	
				$this->apiResult ["message"] = "Unable to remove school from province\n";
			}
		}
	}
	function checkUserRoleAction() {
		$networkModel = new networkModel ();
		
               // $_POST ['client_id'] = 1;
                $user_profile = 1;
                if(isset($_POST['edit_request_from']) && $_POST['edit_request_from'] == 'user_profile'){
                    
                    $_POST ['name'] =  $_POST ['first_name'];
                    
                }
                if(isset($_POST['user_profile']) && $_POST['user_profile'] == 1){
                    
                    $user_profile =  0;
                    
                }
		if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School cannot be empty\n";
			
			/*
			 * }else if( !in_array("manage_all_users",$this->user['capabilities']) &&
			 *
			 *
			 *
			 * ($_POST['client_id']!=$this->user['client_id'] || !in_array("manage_own_users",$this->user['capabilities'])) &&
			 *
			 *
			 *
			 * (empty($this->user['network_id']) || $this->db->get_array_value("network_id",$networkModel->getNetworkByClientId($_POST['client_id']))!=$this->user['network_id'] || !in_array("manage_own_network_users",$this->user['capabilities'])) ){
			 *
			 *
			 *
			 * $this->apiResult["message"] = "You are not authorized to perform this task.\n";
			 */
		} else if ($user_profile && empty ( $_POST ['name'] )) {
			
			$this->apiResult ["message"] = "Name cannot be empty\n";
		} else if (empty ( $_POST ['roles'] ) && in_array ( "manage_all_users", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "User Role cannot be empty\n";
		} else {
			
			$client_id = $_POST ['client_id'];
			$client_id_old = isset($_POST ['client_id_old']) ? $_POST ['client_id_old']:$_POST ['client_id'];
			$name = trim ( $_POST ['name'] );
			
			$user_id = empty ( $_POST ['id'] ) ? 0 : $_POST ['id'];
			
			if ($user_profile && preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $name ) != 1) {
				
				$this->apiResult ["message"] = "Invalid Characters are not allowed in Name.\n";
			} else if (isset ( $_POST ['roles'] )) 

			{
				
				$roles = $_POST ['roles'];
				
				$rolePrincipal = 6;
				
				// check if the user with school principal role already exists
				
				$allRoles = implode ( ",", $roles );
				//print_r($allRoles);
				// start changes for tap admin on 12-05-2016 by Mohit Kumar
				
				// check role id count is 1 or not and the value is 8(tap admin) or not
				
				if (count ( $roles ) == 1 && in_array ( 8, $roles )) {
					
					$usersRole8 = $this->userModel->getUsersForClientByRole ( '', 8, $user_id );
					
					$this->apiResult ["message"] = "The tap admin already exists.\n";
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["duplicate"] = $usersRole8 ['users'];
					
					return;
				} else {
					
					if (in_array ( $rolePrincipal, $roles )) 

					{ // get all users for the client with role 6
					  
						// $userModel=new userModel();
						$usersRole6 = $this->userModel->getUsersForClientByRole ( $client_id, $rolePrincipal, $user_id );
						//print_r($usersRole6);
						if (! empty ( $usersRole6 )) 

						{
							if($client_id!=$client_id_old){
                                                        $this->apiResult ["message"] = "The principal already exists in the new school.\nPlease note that it will create a new user and all the reviews (if any) of current principal will get transfered to new user.\n";    
                                                        }else
							$this->apiResult ["message"] = "The principal already exists.\n";
							
							$this->apiResult ["status"] = 1;
							
							$this->apiResult ["duplicate"] = $usersRole6 ['users'];
							
							return;
						}
					}
				}
				
				$this->apiResult ["message"] = "success.\n";
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["duplicate"] = 0;
			} 

			else {
				
				$this->apiResult ["message"] = "success.\n";
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["duplicate"] = 0;
			}
		}
	}
	
	/*
	 * function checkUpdateUserRoleAction(){
	 *
	 *
	 *
	 * $networkModel=new networkModel();
	 *
	 *
	 *
	 * if( !in_array("manage_all_users",$this->user['capabilities']) &&
	 *
	 *
	 *
	 * (!in_array("manage_own_users",$this->user['capabilities'])) &&
	 *
	 *
	 *
	 * !in_array("manage_own_network_users",$this->user['capabilities'])) ){
	 *
	 *
	 *
	 * $this->apiResult["message"] = "You are not authorized to perform this task.\n";
	 *
	 *
	 *
	 * else if(empty($_POST['name'])){
	 *
	 *
	 *
	 * $this->apiResult["message"] = "Name cannot be empty\n";
	 *
	 *
	 *
	 * }else if(empty($_POST['id'])){
	 *
	 *
	 *
	 * $this->apiResult["message"] = "User ID missing\n";
	 *
	 *
	 *
	 * }else if(!empty($_POST['password']) && strlen($_POST['password'])<6){
	 *
	 *
	 *
	 * $this->apiResult["message"] = "Password too short. Minimum 6 characters required.\n";
	 *
	 *
	 *
	 * }else if(empty($_POST['roles']) && in_array("manage_all_users",$this->user['capabilities'])){
	 *
	 *
	 *
	 * $this->apiResult["message"] = "User Role cannot be empty\n";
	 *
	 *
	 *
	 * }else{
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 * $client_id=$_POST['client_id'];
	 *
	 *
	 *
	 * $name=trim($_POST['name']);
	 *
	 *
	 *
	 * $email=strtolower(trim($_POST['email']));
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 * if(preg_match('/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $name)!=1){
	 *
	 *
	 *
	 * $this->apiResult["message"] = "Invalid Characters are not allowed in Name.\n";
	 *
	 *
	 *
	 * }else if(preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $email)!=1){
	 *
	 *
	 *
	 * $this->apiResult["message"] = "Invalid email.\n";
	 *
	 *
	 *
	 * }else if($this->userModel->getUserByEmail($email)){
	 *
	 *
	 *
	 * $this->apiResult["message"] = "Email already exists.\n";
	 *
	 *
	 *
	 * }else{
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 * $roles=$_POST['roles'];
	 *
	 *
	 *
	 * $rolePrincipal = 6;
	 *
	 *
	 *
	 * //check if the user with school principal role already exists
	 *
	 *
	 *
	 * $allRoles = implode(",",$roles);
	 *
	 *
	 *
	 * if(in_array($rolePrincipal,$roles))
	 *
	 *
	 *
	 * { // get all users for the client with role 6
	 *
	 *
	 *
	 * $userModel=new userModel();
	 *
	 *
	 *
	 * $usersRole6 = $userModel->getUsersForClientByRole($client_id,$rolePrincipal);
	 *
	 *
	 *
	 * if(!empty($usersRole6))
	 *
	 *
	 *
	 * {
	 *
	 *
	 *
	 * $this->apiResult["message"] = "The principal already exists.\n";
	 *
	 *
	 *
	 * $this->apiResult["status"]=1;
	 *
	 *
	 *
	 * $this->apiResult["duplicate"]=$usersRole6['users'];
	 *
	 *
	 *
	 * return;
	 *
	 *
	 *
	 * }
	 *
	 *
	 *
	 * }
	 *
	 *
	 *
	 * $this->apiResult["message"] = "success.\n";
	 *
	 *
	 *
	 * $this->apiResult["status"]=1;
	 *
	 *
	 *
	 * $this->apiResult["duplicate"]=0;
	 *
	 *
	 *
	 * }
	 *
	 *
	 *
	 * }
	 *
	 *
	 *
	 * }
	 */
	function deleteUserRoleAction() {
		if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School cannot be empty\n";
		} 

		elseif (empty ( $_POST ['users_id'] )) {
			
			$this->apiResult ["message"] = "UsersId cannot be empty\n";
		} 

		elseif (empty ( $_POST ['role_id'] )) {
			
			$this->apiResult ["message"] = "RoleId cannot be empty\n";
		} 

		else {
			
			$userModel = new userModel ();
			
			$roleId = $_POST ['role_id'];
			
			$usersId = $_POST ['users_id'];
			
			$this->db->start_transaction ();
			
			$usersId = explode ( ",", $usersId );
			
			$queryFailed = 0;
			
			foreach ( $usersId as $user => $id ) 

			{
				
				if (! $userModel->deleteUserRole ( $id, $roleId ))
					
					$queryFailed = 10;
			}
			
			if (! $queryFailed && $this->db->commit ()) 

			{
				
				$this->apiResult ["message"] = "delete user action.\n";
				
				$this->apiResult ["status"] = 1;
				
				return;
			}
			
			$this->apiResult ["message"] = "Error in deleting user role\n";
			
			$this->apiResult ["status"] = 0;
		}
	}
	function createUserAction() {
		$check = function ($input, $allowed) 

		{
			
			foreach ( $input as $val ) 

			{
				
				if (! in_array ( $val, $allowed ))
					
					return 0;
			}
			
			return 1;
		};
		
		$networkModel = new networkModel ();
		
		if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School/College cannot be empty\n";
		} else if (! in_array ( "manage_all_users", $this->user ['capabilities'] ) && 

		($_POST ['client_id'] != $this->user ['client_id'] || ! in_array ( "manage_own_users", $this->user ['capabilities'] )) && 

		(empty ( $this->user ['network_id'] ) || $this->db->get_array_value ( "network_id", $networkModel->getNetworkByClientId ( $_POST ['client_id'] ) ) != $this->user ['network_id'] || ! in_array ( "manage_own_network_users", $this->user ['capabilities'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['name'] )) {
			
			$this->apiResult ["message"] = "Name cannot be empty\n";
		} else if (empty ( $_POST ['email'] )) {
			
			$this->apiResult ["message"] = "Email cannot be empty\n";
		} else if (empty ( $_POST ['password'] )) {
			
			$this->apiResult ["message"] = "Password cannot be empty\n";
		} else if (strlen ( $_POST ['password'] ) < 6) {
			
			$this->apiResult ["message"] = "Password too short. Minimum 6 characters required.\n";
		} else if (empty ( $_POST ['roles'] ) && in_array ( "manage_all_users", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "User Role cannot be empty\n";
		} 

		else if (in_array ( "manage_own_users", $this->user ['capabilities'] ) && in_array ( 6, $this->user ['role_ids'] ) && (count ( $_POST ['roles'] ) > 2 || ! $check ( $_POST ['roles'], array (
				3,
				5 
		) ))) { // principal is allowed to add internal reviewer or school admin only
		                                                                                                                                                                                
			// todo
			
			$this->apiResult ["message"] = "You are not authorised to perform this task\n";
		} else if (in_array ( "manage_own_users", $this->user ['capabilities'] ) && in_array ( 7, $this->user ['role_ids'] ) && (count ( $_POST ['roles'] ) > 3 || ! $check ( $_POST ['roles'], array (
				3,
                                5,
				6 
		) ))) { // network admin is allowed to add  school principal or school admin only
		                                                                                                                                                                                
			// todo
			
			$this->apiResult ["message"] = "You are not authorised to perform this task\n";
		}

		else {
			
			$client_id = $_POST ['client_id'];
			
			$name = trim ( $_POST ['name'] );
			
			$email = strtolower ( trim ( $_POST ['email'] ) );
                        
                        $moodle_user=isset($_POST['moodle_user'])?$_POST['moodle_user']:0;
			
			// check role for tap admin on 12-05-2016 by Mohit Kumar
			
			if ($_POST ['roles'] [0] == 8 && ! empty ( $_POST ['roles'] )) {
				
				$roleId = $_POST ['roles'] [0];
			} else {
				
				$roleId = empty ( $_POST ['role_id'] ) ? 0 : $_POST ['role_id'];
			}
			
			$usersId = empty ( $_POST ['users_id'] ) ? 0 : $_POST ['users_id'];
			
			$usersId = explode ( ",", $usersId );
			
			// $userModel = new userModel();
                        $clientModel=new clientModel();
                        $clientDetails=$clientModel->getClientById($client_id);
                        //print_r($clientDetails);
			
			if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $name ) != 1) {
				
				$this->apiResult ["message"] = "Invalid Characters are not allowed in Name.\n";
			} else if (preg_match ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $email ) != 1) {
				
				$this->apiResult ["message"] = "Invalid email.\n";
			} else if ($this->userModel->getUserByEmail ( $email )) {
				
				$this->apiResult ["message"] = "Email already exists.\n";
			} else if(in_array(7,$_POST ['roles']) && empty($clientDetails['network_id'])){
                                $this->apiResult ["message"] = "Network admin role is allowed only for schools/colleges which are under some network.\n";
                            
                        } else {
				$currentUser=isset($this->user ['user_id'])?$this->user['user_id']:NULL;
				$this->db->start_transaction ();
				
				$queryFailed = 0;
				
				if (OFFLINE_STATUS == TRUE) {
					$userUniqueID = $this->db->createUniqueID ( 'addUser' );
				}
				
				foreach ( $usersId as $user => $id ) 

				{
					
					if (! $this->userModel->deleteUserRole ( $id, $roleId )) {
						
						$queryFailed = 10;
					} else {
						if (OFFLINE_STATUS == TRUE && $id != 0 && $roleId != 0) {
							$action_user_deleted_role_json = json_encode ( array (
									'user_id' => $id,
									'role_id' => $roleId 
							) );
							$this->db->saveHistoryData ( $id, 'h_user_user_role', $userUniqueID, 'removeUserRole', $id, $roleId, $action_user_deleted_role_json, 0, date ( 'Y-m-d H:i:s' ) );
							$queryFailed = 0;
						}
					}
				}
				
				$roles = 3;
				
				if ((in_array ( "manage_all_users", $this->user ['capabilities'] )) || (in_array ( "manage_own_users", $this->user ['capabilities'] ) && in_array ( 6, $this->user ['role_ids'] ))) {
					
					$roles = $_POST ['roles'];
				}else if ((in_array ( "manage_all_users", $this->user ['capabilities'] )) || (in_array ( "manage_own_users", $this->user ['capabilities'] ) && in_array ( 7, $this->user ['role_ids'] ))) {
					
					$roles = $_POST ['roles'];
				} 

				else
					
					$roles = array (
							3 
					);
				
				$uid = $this->userModel->createUser ( $email, $_POST ['password'], $name, $_POST ['client_id'],0, date("Y-m-d H:i:s"),$currentUser,$moodle_user);
				
				if (OFFLINE_STATUS == TRUE) {
					$userUniqueID = $this->db->createUniqueID ( 'addUser' );
					// start--> call function for save history for new insert user in history table on 03-03-2016 by Mohit Kumar
					$action_user_json = json_encode ( array (
							'email' => $email,
							'name' => $name,
							'password' => $_POST ['password'],
							'client_id' => $_POST ['client_id'] 
					) );
					$this->db->saveHistoryData ( $uid, 'd_user', $userUniqueID, 'addUser', $uid, $email, $action_user_json, 0, date ( 'Y-m-d H:i:s' ) );
					// end--> call function for save history for new insert user in history table on 03-03-2016 by Mohit Kumar
				}
				
				$rolesAdded = true;
				
				$alert = 1;
				
				foreach ( $roles as $role ) {
					
					if (! $this->userModel->addUserRole ( $uid, $role )) {
						
						$rolesAdded = false;
						
						break;
					} else {
						if (OFFLINE_STATUS == TRUE) {
							$user_role_id = $this->db->get_last_insert_id ();
							// start--> call function for save history for new insert user in history table on 03-03-2016 by Mohit Kumar
							$action_user_role_json = json_encode ( array (
									'user_id' => $uid,
									'role_id' => $role 
							) );
							$this->db->saveHistoryData ( $user_role_id, 'h_user_user_role', $userUniqueID, 'addUserRole', $uid, $role, $action_user_role_json, 0, date ( 'Y-m-d H:i:s' ) );
							// end--> call function for save history for new insert user in history table on 03-03-2016 by Mohit Kumar
							$rolesAdded = true;
						}
					}
					
					if ($role == 4 && $this->db->addAlerts ( 'd_user', $uid, $name, $email, 'CREATE_EXTERNAL_ASSESSOR' )) {
						$alertid = $this->db->get_last_insert_id ();
						if (OFFLINE_STATUS == TRUE) {
							$action_alert_json = json_encode ( array (
									'table_name' => 'd_user',
									'content_id' => $uid,
									'content_title' => $name,
									'content_description' => $email,
									"type" => 'CREATE_EXTERNAL_ASSESSOR' 
							) );
							$this->db->saveHistoryData ( $alertid, 'd_alerts', $userUniqueID, 'addUserAlert', $uid, $email, $action_alert_json, 0, date ( 'Y-m-d H:i:s' ) );
						}
						
						$this->db->insert ( 'h_tap_user_assessment', array (
								'tap_program_status' => 1,
								'user_id' => $uid 
						) );
						if (OFFLINE_STATUS == TRUE) {
							$tapuserid = $this->db->get_last_insert_id ();
							$action_tab_user_json = json_encode ( array (
									'table_name' => 'h_tap_user_assessment',
									'tap_program_status' => 1,
									'user_id' => $uid 
							) );
							$this->db->saveHistoryData ( $tapuserid, 'h_tap_user_assessment', $userUniqueID, 'addUserTabAssessorAlert', $uid, $uid, $action_tab_user_json, 0, date ( 'Y-m-d H:i:s' ) );
						}
						
						$alert = 1;
					}
				}
				
				if ($uid > 0 && $rolesAdded && ! $queryFailed && $this->db->commit () && $alert) {
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["message"] = "User successfully added";
                                        $nameArray = explode(" ", $name);
                                        $firstname=array_shift($nameArray);
                                        $last_name=is_array($nameArray) ? implode(" ", $nameArray) : '';
                                        $student_moodle_data=$this->prepare_student_data($email,$_POST ['password'],$firstname,$last_name,$email);
                                        $moodle_user=isset($_POST['moodle_user'])?$_POST['moodle_user']:0;
                                        $prepare_check_data=$this->prepare_check_data($email);
                                        $update_data=array();
                                        
                                        if($moodle_user==1 && !add_update_user_moodle($student_moodle_data,$prepare_check_data,$update_data)){
                                         $this->apiResult ["message"] = "User successfully added but not added/updated in Moodle";   
                                        }
                                        
                                } else {
					
					$this->db->rollback ();
					
					$this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
				}
			}
		}
        }
                
        function prepare_student_data($username,$password,$firstname,$lastname,$email){
        $student_data['users'][0]['username'] = $username;
        
        if(!empty($password)){
        $student_data['users'][0]['password'] =$password;
        }
        
        $student_data['users'][0]['firstname'] =$firstname;
        $student_data['users'][0]['lastname'] =$lastname;
        $student_data['users'][0]['email'] =$email;
        return $student_data;
       }
       
       function prepare_check_data($email){
       
        $username = array( 'key' => 'email' ,  'value' => $email );
        $params = array( 'criteria' => array($username));
        return $params;
       }
	
       
	function updateUserAction() {
                $email_update = isset($_POST ['email'])? strtolower ( trim ( $_POST ['email'] ) ):'';
                $principal_user_row_new=$this->userModel->getPrincipal($_POST['client_id']);
                $principal_user_id_new=empty ( $principal_user_row_new ) ? 0:$principal_user_row_new['user_id'];
                $moodle_user=isset($_POST['moodle_user'])?$_POST['moodle_user']:0;
                $user_profile = 1;
                if(isset($_POST['user_profile']) && $_POST['user_profile'] == 1){
                    
                    $user_profile =  0;
                    
                }
                if(isset($_POST['edit_request_from'])) {
                    
                    $_POST ['name'] = $_POST ['first_name'];
                }
                
		if ($user_profile && empty ( $_POST ['name'] )) {
			
			$this->apiResult ["message"] = "Name cannot be empty\n";
		} else if (empty ( $_POST ['id'] )) {
			
			$this->apiResult ["message"] = "User ID missing\n";
		} else if (! empty ( $_POST ['password'] ) && strlen ( $_POST ['password'] ) < 6) {
			
			$this->apiResult ["message"] = "Password too short. Minimum 6 characters required.\n";
		} else if ($user_profile && empty ( $_POST ['email'] ) && isset($_POST ['email'])) {
			
			$this->apiResult ["message"] = "Email cannot be empty\n";
		} else if ($user_profile && preg_match ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $email_update ) != 1  && isset($_POST ['email'])) {
				
			$this->apiResult ["message"] = "Invalid email.\n";
		}else if($principal_user_id_new==$_POST ['id'] && isset($_POST['roles']) && !in_array(6,$_POST ['roles'])){
                        $this->apiResult ["message"] = "Not allowed to remove the Principal Role. Please first assign another user with the Principal Role\n";
                }else if (isset($_POST['roles']) && in_array("8",$_POST['roles']) && count($_POST['roles'])>1)  {
				
			$this->apiResult ["message"] = "TAP Admin cannot be assigned other roles.\n";
		}
                else {
			
			$user_id = trim ( $_POST ['id'] );
			
			$user = $this->userModel->getUserById ( $user_id );
                        $client_id_old=$user['client_id'];
			$networkModel = new networkModel ();
                        $clientModel=new clientModel();
                        $clientDetails=$clientModel->getClientById($_POST['client_id']);
			$currentUser=isset($this->user ['user_id'])?$this->user['user_id']:NULL;                        
			if (empty ( $user )) {
				
				$this->apiResult ["message"] = "User does not exist\n";
			}else if ($this->userModel->getUserByEmailExceptSelf ( $email_update,$user_id) && isset($_POST ['email'])) {
                            
			        $this->apiResult ["message"] = "Email already exists.\n";
		        }else if (! in_array ( 6, $user ['role_ids'] ) && empty ( $_POST ['roles'] ) && in_array ( "manage_all_users", $this->user ['capabilities'] )) {
				
				$this->apiResult ["message"] = "User Role cannot be empty\n";
			} else if(isset($_POST ['roles']) && in_array(7,$_POST ['roles']) && empty($clientDetails['network_id'])){
                                $this->apiResult ["message"] = "Network admin role is allowed only for schools/colleges which are under some network.\n";
                            
                        } else if (in_array ( "manage_all_users", $this->user ['capabilities'] ) || 

			$this->user ['user_id'] == $user ['user_id'] || 

			($user ['client_id'] == $this->user ['client_id'] && in_array ( "manage_own_users", $this->user ['capabilities'] )) || 

			($this->user ['network_id'] > 0 && $this->db->get_array_value ( "network_id", $networkModel->getNetworkByClientId ( $_POST ['client_id'] ) ) == $this->user ['network_id'] && in_array ( "manage_own_network_users", $this->user ['capabilities'] ))) {
				
				$name = trim ( $_POST ['name'] );
                                
				
				if ($user_profile && preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $name ) != 1) {
					
					$this->apiResult ["message"] = "Invalid Characters are not allowed in Name.\n";
				} else {
					
					// $userModel = new userModel();
					
					// check role for tap admin on 12-05-2016 by Mohit Kumar
					
					if (isset($_POST ['roles']) && $_POST ['roles'] [0] == 8 && ! empty ( $_POST ['roles'] )) {
						
						$roleId = $_POST ['roles'] [0];
					} else {
						
						$roleId = empty ( $_POST ['role_id'] ) ? 0 : $_POST ['role_id'];
					}
					
					$usersId = empty ( $_POST ['users_id'] ) ? 0 : $_POST ['users_id'];
					
					$usersId = explode ( ",", $usersId );
					
					$queryFailed = 0;
					
					$email = $user ['email'];
					
					$this->db->start_transaction ();
					
					if (OFFLINE_STATUS == TRUE) {
						$userUniqueID = $this->db->createUniqueID ( 'editUser' );
					}
					
                                        
                                        
                                        //Vikas
                                        $oldRoles = empty($this->userModel->getUserRoles ( $user_id ))?'':$this->userModel->getUserRoles ( $user_id );
                                        $review_trasfer_status=true;
                                        $new_principal_generated_id=0;
                                        if(in_array ( "manage_own_users", $this->user ['capabilities'] ) && in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] )){
                                        if($_POST['client_id']!=$client_id_old){
                                                //$this->apiResult ["message"] = "Diffent Client ".$_POST['client_id']."|".$client_id_new."";
                                                //if(count($old_reviews=$this->userModel->getReviewsAssessmentCount($client_id_old))>0 || count($new_reviews=$this->userModel->getReviewsAssessmentCount($_POST['client_id']))>0){
                                                        //$this->apiResult ["message"] = "Diffent Client ".$_POST['client_id']."|".$client_id_old."";

                                                        //print_r($this->userModel->getReviewsAssessmentCount($client_id_old)); 
                                                        //print_r($this->userModel->getReviewsAssessmentCount($_POST['client_id'])); 
                                                        //print_r($old_reviews);
                                                        $principal_user_row_old=$this->userModel->getPrincipal($client_id_old);
                                                        $principal_user_id_old=empty ( $principal_user_row_old ) ? 0:$principal_user_row_old['user_id'];
                                                        $principal_user_row_new=$this->userModel->getPrincipal($_POST['client_id']);
                                                        $principal_user_id_new=empty ( $principal_user_row_new ) ? 0:$principal_user_row_new['user_id'];
                                                       if($principal_user_id_old==$_POST['id']){
                                                            //echo"Principal";  
                                                            $new_principal_generated_id=$this->userModel->createrandomuser($client_id_old,$this->user['user_id'],$_POST['id'],$currentUser);
                                                            if($new_principal_generated_id>0){
                                                            //$new_principal_generated_id=$this->db->createrandomuser($client_id_old);
                                                            //echo $new_principal_generated_id;    
                                                            if(!$this->userModel->transfer_reviews($principal_user_id_old,$new_principal_generated_id)){
                                                            $review_trasfer_status=false;    
                                                            }
                                                            if(!$this->userModel->transfer_reviews($principal_user_id_new,$principal_user_id_old)){
                                                            $review_trasfer_status=false;    
                                                            }
                                                            }else{
                                                            $review_trasfer_status=false;    
                                                            }
                                                            
                                                        }else{
                                                            if($principal_user_id_old===0){   
                                                              //echo"Other-Principal Null";
                                                              $new_principal_generated_id=$this->userModel->createrandomuser($client_id_old,$this->user['user_id'],0,$currentUser);
                                                              if($new_principal_generated_id>0){
                                                              //$new_principal_generated_id=$this->db->createrandomuser($client_id_old);
                                                              //echo $new_principal_generated_id;      
                                                              if(!$this->userModel->transfer_reviews($_POST['id'],$new_principal_generated_id)){
                                                              $review_trasfer_status=false;    
                                                              }
                                                              }else{
                                                              $review_trasfer_status=false;    
                                                              }

                                                            }else{
                                                                //echo"Other-Principal Exist";
                                                                if(!$this->userModel->transfer_reviews($_POST['id'],$principal_user_id_old)){
                                                                 $review_trasfer_status=false;   
                                                                }
                                                                
                                                            }    
                                                        }
                                                  //}
                                        }
                                        }
                                        //Vikas

                                       // find clientId by userId
					
					foreach ( $usersId as $user => $id ) {
						
						if ($id != 0) {
							
							if (! $this->userModel->deleteUserRole ( $id, $roleId )) {
								
								$queryFailed = 1;
							} else {
								if (OFFLINE_STATUS == TRUE) {
									$action_user_deleted_role_json = json_encode ( array (
											'user_id' => $id,
											'role_id' => $roleId 
									) );
									$this->db->saveHistoryData ( $id, 'h_user_user_role', $userUniqueID, 'removeUserRole', $id, $roleId, $action_user_deleted_role_json, 0, date ( 'Y-m-d H:i:s' ) );
									$queryFailed = 0;
								}
							}
						}
					}
					
                                        
                                        
                                        //die();
                                        if(in_array ( "manage_own_users", $this->user ['capabilities'] ) && in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] )){
					$userUpdated = $this->userModel->updateUserEmail ( $user_id, $name, $email_update ,$_POST ['password'],$_POST['client_id'],-1,date("Y-m-d H:i:s"),$currentUser,$moodle_user);
                                        }else{
                                        $userUpdated = $this->userModel->updateUser ( $user_id, $name ,$_POST ['password'],0,-1,date("Y-m-d H:i:s"),$currentUser,$moodle_user);    
                                        }
					
					if (OFFLINE_STATUS == TRUE) {
						// start---> save edited user details on history table on 08-03-2016 by Mohit Kumar
						$action_user_json = json_encode ( array (
								'name' => $name,
								'password' => $_POST ['password'] 
						) );
						$this->db->saveHistoryData ( $user_id, 'd_user', $userUniqueID, 'editUser', $user_id, $email, $action_user_json, 0, date ( 'Y-m-d H:i:s' ) );
						// start---> save edited user details on history table on 08-03-2016 by Mohit Kumar
					}
					
					$rolesUpdated = true;
					
					// print_r($_POST['roles']);die;
					
					if ((in_array ( "manage_all_users", $this->user ['capabilities'] ) || in_array ( 6, $this->user ['role_ids'] ) || in_array ( 7, $this->user ['role_ids'] )) && ! empty ( $_POST ['roles'] )) { // principal can update user role to school admin and internal reviewer
						
						$currentRoles = $this->userModel->getUserRoles ( $user_id );
						
						if ($currentRoles === null) {
							
							// add tap admin role on 12-05-2016 by Mohit Kumar
							
							if (current ( $_POST ['roles'] ) == 8) {
								
								foreach ( $_POST ['roles'] as $role ) {
									
									if (! $this->userModel->addUserRole ( $user_id, $role )) {
										
										$rolesUpdated = false;
										
										break;
									} else {
										if (OFFLINE_STATUS == TRUE) {
											$action_user_deleted_role_json = json_encode ( array (
													'user_id' => $user_id,
													'role_id' => $role 
											) );
											$this->db->saveHistoryData ( $user_id, 'h_user_user_role', $userUniqueID, 'removeUserRole', $user_id, $role, $action_user_deleted_role_json, 0, date ( 'Y-m-d H:i:s' ) );
										}
									}
								}
							} else {
								
								//$rolesUpdated = false;
                                                            if(isset($_POST['roles'])){
                                                            foreach ( $_POST ['roles'] as $role ) {
									
									if (! $this->userModel->addUserRole ( $user_id, $role )) {
										
										$rolesUpdated = false;
										
										break;
									} else {
										if (OFFLINE_STATUS == TRUE) {
											$action_user_deleted_role_json = json_encode ( array (
													'user_id' => $user_id,
													'role_id' => $role 
											) );
											$this->db->saveHistoryData ( $user_id, 'h_user_user_role', $userUniqueID, 'removeUserRole', $user_id, $role, $action_user_deleted_role_json, 0, date ( 'Y-m-d H:i:s' ) );
										}
									}
								}
                                                            }
							}
						} else {
							
							$currentRoles = $currentRoles != "" ? explode ( ",", $currentRoles ) : array ();
							if(in_array ( 6, $this->user ['role_ids'] )){
                                                         $array_allow_roles=array(3,5);
                                                         $currentRoles=  array_intersect($currentRoles,$array_allow_roles);
                                                        }else if(in_array ( 7, $this->user ['role_ids'] )){
                                                         $array_allow_roles=array(3,6,5);
                                                         $currentRoles=  array_intersect($currentRoles,$array_allow_roles);
                                                        }
                                                        
							$commonValues = array_intersect ( $currentRoles, $_POST ['roles'] );
							
							$rolesNeedToBeAdded = array_diff ( $_POST ['roles'], $commonValues );
							
							$rolesNeedToBeDeleted = array_diff ( $currentRoles, $commonValues );
							
							if (($key = array_search ( 6, $rolesNeedToBeDeleted )) !== false && in_array ( 6, $this->user ['role_ids'] )) { // principal can not delete his own principal role
								
								unset ( $rolesNeedToBeDeleted [$key] );
							}
							
							foreach ( $rolesNeedToBeDeleted as $role )
								
								if (! $this->userModel->deleteUserRole ( $user_id, $role )) {
									
									$rolesUpdated = false;
									
									break;
								} else {
									if (OFFLINE_STATUS == TRUE) {
										$action_user_role_json = json_encode ( array (
												'user_id' => $user_id,
												'role_id' => $role 
										) );
										$this->db->saveHistoryData ( $user_id, 'h_user_user_role', $userUniqueID, 'editUserRole', $user_id, $role, $action_user_role_json, 0, date ( 'Y-m-d H:i:s' ) );
									}
								}
							
							foreach ( $rolesNeedToBeAdded as $role )
								
								if (! $this->userModel->addUserRole ( $user_id, $role )) {
									
									$rolesUpdated = false;
									
									break;
								} else {
									if (OFFLINE_STATUS == TRUE) {
										$action_user_role_json = json_encode ( array (
												'user_id' => $user_id,
												'role_id' => $role 
										) );
										$this->db->saveHistoryData ( $user_id, 'h_user_user_role', $userUniqueID, 'editUserRole', $user_id, $role, $action_user_role_json, 0, date ( 'Y-m-d H:i:s' ) );
									}
								}
						}
					}
					//print_r($_SESSION);
                                        //print_r($this->user['user_id']);
                                        $currentRoles_new = empty($this->userModel->getUserRoles ( $user_id ))?'':$this->userModel->getUserRoles ( $user_id );
                                        $add_user_history=true;
                                        if(!$this->userModel->add_user_history($user_id,$client_id_old,$_POST['client_id'],$oldRoles,$currentRoles_new,'Updated',$this->user['user_id'],date("Y-m-d H:i:s"))){
                                        $add_user_history=false;    
                                        }
                                        //echo $rolesUpdated?1:0;
					if ($userUpdated && ! $queryFailed && $rolesUpdated && $review_trasfer_status && $add_user_history && $this->db->commit ()) {
						
                                                if($new_principal_generated_id>0 && in_array ( "manage_own_users", $this->user ['capabilities'] ) && in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] )){
                                                $new_user_details=$this->userModel->getUserById($new_principal_generated_id);
                                                //$toEmail = 'shraddha.adhyayan@gmail.com';
                                                $toEmail = 'deepakchauhan89@gmail.com';
                                                $body_mail="Dear Admin<br><br>User with Email Id: ".$new_user_details['email']." as Role of Principal for <b>".$new_user_details['client_name']."</b> is generated automatically."
                                                        . "<br>This is requested to modify the user<br><br>This is auto generated email, need not to reply<br><br>Thanks";
                                                sendEmail(''.$this->user['email'].'',''.$this->user['name'].'',$toEmail,'Shraddha Khedekar','','','Adhyayan:: Auto Generated User as Principal for '.$new_user_details['client_name'].'',$body_mail,'poonam.choksi@adhyayan.asia');
                                                }
                                                
                                                $this->apiResult ["status"] = 1;
						
						$this->apiResult ["message"] = "User successfully updated";
						$this->apiResult ["client_id"] = $_POST['client_id'];
                                                
                                                $nameArray = explode(" ", $name);
                                                $firstname=array_shift($nameArray);
                                                $last_name=is_array($nameArray) ? implode(" ", $nameArray) : '';
                                                $password_moodle=empty($_POST ['password'])?"adhyayan_123456":$_POST ['password'];
                                                $student_moodle_data=$this->prepare_student_data($email_update,$password_moodle,$firstname,$last_name,$email_update);
                                                $moodle_user=isset($_POST['moodle_user'])?$_POST['moodle_user']:0;
                                                $prepare_check_data=$this->prepare_check_data($email);
                                                $update_data=array();
                                                if($moodle_user==1 && !add_update_user_moodle($student_moodle_data,$prepare_check_data)){
                                                    $this->apiResult ["message"] = "User successfully updated but not added/updated in Moodle";   
                                                }
                                                
					} else {
						
						$this->db->rollback ();
						
						$this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
					}
				}
			} else {
				
				$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			}
		}
	}
	function createClientAction() {
		if (! in_array ( "create_client", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_institution_id'] )) {
			
			$this->apiResult ["message"] = "Type of Institution cannot be empty\n";
		} else if (empty ( $_POST ['client_name'] )) {
			
			$this->apiResult ["message"] = "Name of the school/college cannot be empty\n";
		} else if (empty ( $_POST ['street'] )) {
			
			$this->apiResult ["message"] = "Street cannot be empty\n";
		} else if (empty ( $_POST ['city'] )) {
			
			$this->apiResult ["message"] = "City cannot be empty\n";
		} else if (empty ( $_POST ['country'] )) {
			
			$this->apiResult ["message"] = "Country cannot be empty\n";
		} else if (empty ( $_POST ['state'] )) {
			
			$this->apiResult ["message"] = "State cannot be empty\n";
		} else if (empty ( $_POST ['principal_name'] )) {
			
			$this->apiResult ["message"] = "Principal name cannot be empty\n";
		} else if (empty ( $_POST ['email'] )) {
			
			$this->apiResult ["message"] = "Email cannot be empty\n";
		} else if (empty ( $_POST ['password'] )) {
			
			$this->apiResult ["message"] = "Password cannot be empty\n";
		} else if (strlen ( $_POST ['password'] ) < 6) {
			
			$this->apiResult ["message"] = "Password too short. Minimum 6 characters required.\n";
		} else if (! empty ( $_POST ['haveNetwork'] ) && $_POST ['haveNetwork'] == 1 && empty ( $_POST ['network'] )) {
			
			$this->apiResult ["message"] = "Network cannot be empty\n";
			// $_POST['country']
		} else if (! empty ( $_POST ['phone'] ) && !preg_match("/^[1-9][0-9]*$/",$_POST ['phone'] )) {
			
			$this->apiResult ["message"] = "Invalid phone number\n";
		} else {
			
			$cname = trim ( $_POST ['client_name'] );
			
			$pname = trim ( $_POST ['principal_name'] );
			
			$email = strtolower ( trim ( $_POST ['email'] ) );
                        
                        $currentUser=isset($this->user ['user_id'])?$this->user['user_id']:NULL;
			
			if (preg_match ( '/^[\.A-Za-z0-9\s,.\'-]+$/', $cname ) != 1) {
				
				$this->apiResult ["message"] = "Only Alphabets(a-z), numbers (0-9) ,period(.),apostrophe(') and hyphen(-) allowed in name of the school/college.\n";
			} else if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $pname ) != 1) {
				
				$this->apiResult ["message"] = "Invalid Characters are not allowed in Principal Name.\n";
			} else if (preg_match ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $email ) != 1) {
				
				$this->apiResult ["message"] = "Invalid email.\n";
			} else if ($this->userModel->getUserByEmail ( $email )) {
				
				$this->apiResult ["message"] = "Email already exists.\n";
			} else {
				
				$this->db->start_transaction ();
				
				if (OFFLINE_STATUS == TRUE) {
					// start--> call function for creating unique id for add school on 07-03-2016 by Mohit Kumar
					$clientUniqueID = $this->db->createUniqueID ( 'addSchool' );
					// end--> call function for creating unique id for add school on 07-03-2016 by Mohit Kumar
				}
				
				$clientModel = new clientModel ();
				 $principle_ph = "(+".$_POST ['country_code'].")".$_POST ['phone'];
				$cid = $clientModel->createClient ( $_POST ['client_institution_id'], $cname, $_POST ['street'], $_POST ['addrline2'], $_POST ['city'], $_POST ['state'], $_POST ['country'], $principle_ph, $_POST ['remarks'], 0 );
				
                               
				if (OFFLINE_STATUS == true) {
					// start---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
					$action_client_json = json_encode ( array (
							'client_name' => $cname,
							'street' => $_POST ['street'],
							'addressLine2' => $_POST ['addrline2'],
							'city_id' => $_POST ['city'],
							'state_id' => $_POST ['state'],
							'country_id' => $_POST ['country'],
							"principal_phone_no" => $principle_ph,
							'remarks' => $_POST ['remarks'] 
					) );
					$this->db->saveHistoryData ( $cid, 'd_client', $clientUniqueID, 'addSchool', $cid, $email, $action_client_json, 0, date ( 'Y-m-d H:i:s' ) );
					// end---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
				}
				
				$uid = $this->userModel->createUser ( $email, $_POST ['password'], $pname, $cid, 0, date("Y-m-d H:i:s"), $currentUser);
				// start---> call function for saving add school principal data on 04-03-2016 by Mohit Kumar
				$action_principal_json = json_encode ( array (
						'email' => $email,
						'name' => $pname,
						'password' => $_POST ['password'],
						'client_id' => $cid 
				) );
				
				// end---> call function for saving add school principal data on 04-03-2016 by Mohit Kumar
				// start-----> i have commented existing function bcoz i need auto increment id for history table
				// $roleAdded = $this->userModel->addUserRole($uid, 6);
				// end-----> i have commented existing function bcoz i need auto increment id for history table
				// start----> i have used used new function for add the user role.
				$roleAdded = $this->userModel->addNewUserRole ( $uid, 6 );
                                $roleAdded = $this->userModel->addNewUserRole ( $uid, 3 );
				// start---> call function for saving add school principal role data on 04-03-2016 by Mohit Kumar
				$action_role_json = json_encode ( array (
						'user_id' => $uid,
						'role_id' => 6 
				) );
				if (OFFLINE_STATUS == TRUE) {
					$this->db->saveHistoryData ( $uid, 'd_user', $clientUniqueID, 'addSchoolPrincipal', $cid, $email, $action_principal_json, 0, date ( 'Y-m-d H:i:s' ) );
					$this->db->saveHistoryData ( $roleAdded, 'h_user_user_role', $clientUniqueID, 'addSchoolPrincipalRole', $uid, 6, $action_role_json, 0, date ( 'Y-m-d H:i:s' ) );
					// end---> call function for saving add school principal role data on 04-03-2016 by Mohit Kumar
				}
				
				$addedToNetwork = true;
				$addedToProvince = true;				
				if (! empty ( $_POST ['haveNetwork'] ) && $_POST ['haveNetwork'] == 1 && $_POST ['network'] > 0) {
					
						$addedToNetwork = $clientModel->addClientToNetwork ( $cid, $_POST ['network'] );
						// start---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
						$action_network_json = json_encode ( array (
								'client_id' => $cid,
								'network_id' => $_POST ['network'] 
						) );
						if (OFFLINE_STATUS == TRUE)
							$this->db->saveHistoryData ( $cid, 'h_client_network', $clientUniqueID, 'addSchoolNetwork', $cid, $_POST ['network'], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
						// end---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
						//province to be added if selected
						$province_id = $_POST['province'];
						if(!empty($province_id)){
						$addedToProvince = $clientModel->addClientToProvince ( $cid, $province_id );
						// start---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
						$action_network_json = json_encode ( array (
								'client_id' => $cid,
								'province_id' => $province_id
						) );
						if (OFFLINE_STATUS == TRUE)
							$this->db->saveHistoryData ( $cid, 'h_client_province', $clientUniqueID, 'addSchoolProvince', $cid, $_POST ['province'], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
					}
				}
				
				if ($cid > 0 && $uid > 0 && $roleAdded && $addedToNetwork && $addedToProvince && $this->db->commit ()) {
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["message"] = "School/College successfully added";
				} else {
					
					$this->db->rollback ();
					
					$this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
				}
			}
		}
	}
	function updateClientAction() {
		if (! in_array ( "create_client", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['id'] )) {
			
			$this->apiResult ["message"] = "School/College ID missing\n";
		} else if (empty ( $_POST ['client_institution_id'] )) {
			
			$this->apiResult ["message"] = "Type of Institution cannot be empty\n";
		} else if (empty ( $_POST ['client_name'] )) {
			
			$this->apiResult ["message"] = "Name of the school/college cannot be empty\n";
		} else if (empty ( $_POST ['principal_name'] )) {
			
			$this->apiResult ["message"] = "Principal name cannot be empty\n";
		} else if (empty ( $_POST ['street'] )) {
			
			$this->apiResult ["message"] = "Street cannot be empty\n";
		} else if (empty ( $_POST ['city'] )) {
			
			$this->apiResult ["message"] = "City cannot be empty\n";
		} else if (empty ( $_POST ['state'] )) {
			
			$this->apiResult ["message"] = "State cannot be empty\n";
		} else if (! empty ( $_POST ['haveNetwork'] ) && $_POST ['haveNetwork'] == 1 && empty ( $_POST ['network'] )) {
			
			$this->apiResult ["message"] = "Network cannot be empty\n";
		} else if (! empty ( $_POST ['phone'] ) && !preg_match("/^[1-9][0-9]*$/",$_POST ['phone'])) {
			
			$this->apiResult ["message"] = "Invalid phone number\n";
		} else {
			
			$pname = trim ( $_POST ['principal_name'] );
			
			$cname = trim ( $_POST ['client_name'] );
			
			$clientModel = new clientModel ();
			
			$client_id = trim ( $_POST ['id'] );
			
			$client = $clientModel->getClientById ( $client_id );
			
			$principal = $this->userModel->getPrincipal ( $client_id );
                        
                        $currentUser=isset($this->user ['user_id'])?$this->user['user_id']:NULL;
                        
                        $principle_ph = "(+".$_POST ['country_code'].")".$_POST ['phone'];
                        
			// else if (preg_match ( '/^[\.A-Za-z\s]+$/', $cname ) != 1) {
                        
			if (empty ( $client )) {
				
				$this->apiResult ["message"] = "School/College does not exist\n";
			}if (preg_match ( '/^[\.A-Za-z0-9\s,.\'-]+$/', $cname ) != 1) {
				
				//$this->apiResult ["message"] = "Only Alphabets(a-z) and period(.)  allowed in name of the school.\n";
                            $this->apiResult ["message"] = "Only Alphabets(a-z), numbers (0-9) ,period(.),apostrophe(') and hyphen(-) allowed in name of the school/college.\n";

			} else if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $pname ) != 1) {
				
				$this->apiResult ["message"] = "Invalid Characters are not allowed in Principal Name.\n";
			} else if (empty ( $principal ['user_id'] )) {
				
				$this->apiResult ["message"] = "Principal does not exist for this school/school.\n";
			} else {
				
				if (OFFLINE_STATUS == TRUE) {
					// start--> call function for creating unique id for add school on 08-03-2016 by Mohit Kumar
					$clientUniqueID = $this->db->createUniqueID ( 'editSchool' );
					// end--> call function for creating unique id for add school on 08-03-2016 by Mohit Kumar
				}
				
				$this->db->start_transaction ();
				
				$updated = $clientModel->updateClient ( $_POST ['client_institution_id'],$client_id, $cname, $_POST ['street'], $_POST ['addrline2'], $_POST ['city'], $_POST ['state'], $_POST ['country'], $principle_ph, $_POST ['remarks'] );
				
				if (OFFLINE_STATUS == true) {
					// start---> call function for saving edit school client data on 08-03-2016 by Mohit Kumar
					$action_client_json = json_encode ( array (
							'client_name' => $cname,
							'street' => $_POST ['street'],
							'addressLine2' => $_POST ['addrline2'],
							'city_id' => $_POST ['city'],
							'state_id' => $_POST ['state'],
							'country_id' => $_POST ['country'],
							"principal_phone_no" => $principle_ph,
							'remarks' => $_POST ['remarks'] 
					) );
					$this->db->saveHistoryData ( $client_id, 'd_client', $clientUniqueID, 'editSchool', $client_id, $principal ['user_id'], $action_client_json, 0, date ( 'Y-m-d H:i:s' ) );
					// end---> call function for saving edit school client data on 08-03-2016 by Mohit Kumar
				}
				
				if (! $this->userModel->updateUser ( $principal ['user_id'], $pname,'',0,-1,date("Y-m-d H:i:s"),$currentUser)) {
					
					$updated = false;
				} else {
					if (OFFLINE_STATUS == TRUE) {
						// start---> call function for saving edit school principal data on 08-03-2016 by Mohit Kumar
						$action_principal_json = json_encode ( array (
								'name' => $pname 
						) );
						$this->db->saveHistoryData ( $principal ['user_id'], 'd_user', $clientUniqueID, 'editSchoolPrincipal', $client_id, $principal ['user_id'], $action_principal_json, 0, date ( 'Y-m-d H:i:s' ) );
						// end---> call function for saving add school principal data on 08-03-2016 by Mohit Kumar
						$updated = true;
					}
				}
				
				$networkUpdated = true;
				$provinceUpdated = true;
				
				if ($client ["network_id"] > 0 && empty ( $_POST ['haveNetwork'] )) {
					if($client ["province_id"]>0){//school can't belong to province without belonging to a network
						$provinceUpdated = $clientModel->removeClientFromProvince ( $client_id, $client ["province_id"] );
							
						if (OFFLINE_STATUS == TRUE) {
							// start---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
							$action_network_json = json_encode ( array (
									'client_id' => $client_id,
									'province_id' => $client ["province_id"]
							) );
							$this->db->saveHistoryData ( $client_id, 'h_client_province', $clientUniqueID, 'removeSchoolProvince', $client_id, $client ["province_id"], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
						}
					}
					$provinceUpdated = $clientModel->removeClientFromNetwork ( $client_id, $client ["network_id"] );
					
					if (OFFLINE_STATUS == TRUE) {
						// start---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
						$action_network_json = json_encode ( array (
								'client_id' => $client_id,
								'network_id' => $client ["network_id"] 
						) );
						$this->db->saveHistoryData ( $client_id, 'h_client_network', $clientUniqueID, 'removeSchoolNetwork', $client_id, $client ["network_id"], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
						// end---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
					}
					
				} else if (empty ( $client ["network_id"] ) && ! empty ( $_POST ['haveNetwork'] ) && $_POST ['haveNetwork'] == 1 && $_POST ['network'] > 0) {
					
					$networkUpdated = $clientModel->addClientToNetwork ( $client_id, $_POST ['network'] );
					
					if (OFFLINE_STATUS == TRUE) {
						// start---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
						$action_network_json = json_encode ( array (
								'client_id' => $client_id,
								'network_id' => $_POST ['network'] 
						) );
						$this->db->saveHistoryData ( $client_id, 'h_client_network', $clientUniqueID, 'addSchoolNetwork', $client_id, $_POST ['network'], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
						// end---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
					}
					$province_id = $_POST['province'];
					if(!empty($province_id) && empty($client['province_id'])){
						$provinceUpdated = $clientModel->addClientToProvince ( $client_id, $province_id );
						// start---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
						$action_network_json = json_encode ( array (
								'client_id' => $client_id,
								'province_id' => $province_id
						) );
						if (OFFLINE_STATUS == TRUE)
							$this->db->saveHistoryData ( $client_id, 'h_client_province', $clientUniqueID, 'addSchoolProvince', $client_id, $_POST ['province'], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
					}
					else if($client ["province_id"]>0 && empty($province_id)){//school is being removed from province
						$provinceUpdated = $clientModel->removeClientFromProvince ( $client_id, $client ["province_id"] );
							
						if (OFFLINE_STATUS == TRUE) {
							// start---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
							$action_network_json = json_encode ( array (
									'client_id' => $client_id,
									'province_id' => $client ["province_id"]
							) );
							$this->db->saveHistoryData ( $client_id, 'h_client_province', $clientUniqueID, 'removeSchoolProvince', $client_id, $client ["province_id"], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
						}
					}
					else if($client ["province_id"]>0 && !empty($_POST['province']) && $client ["province_id"] != $_POST ['province']){//province is different from the previous
						$province_id = $_POST['province'];
						$addedToProvince = $clientModel->updateClientProvince ( $client_id, $province_id );
						// start---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
						$action_network_json = json_encode ( array (
								'client_id' => $client_id,
								'province_id' => $province_id
						) );
						if (OFFLINE_STATUS == TRUE)
							$this->db->saveHistoryData ( $client_id, 'h_client_province', $clientUniqueID, 'addSchoolProvince', $client_id, $_POST ['province'], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
					}
				} else if ($client ["network_id"] > 0 && $_POST ['network'] > 0 ) {
					
					if($client ["network_id"] != $_POST ['network']){
						$networkUpdated = $clientModel->updateClientNetwork ( $client_id, $_POST ['network'] );
						
						if (OFFLINE_STATUS == TRUE) {
							// start---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
							$action_network_json = json_encode ( array (
									'client_id' => $client_id,
									'network_id' => $_POST ['network'] 
							) );
							$this->db->saveHistoryData ( $client_id, 'h_client_network', $clientUniqueID, 'editSchoolNetwork', $client_id, $_POST ['network'], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
						}
					}
					$province_id = $_POST['province'];					
					if($province_id>0 && empty($client['province_id'])){//school being added to the province
						$provinceUpdated = $clientModel->addClientToProvince ( $client_id, $province_id );
						// start---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
						$action_network_json = json_encode ( array (
								'client_id' => $client_id,
								'province_id' => $province_id
						) );
						if (OFFLINE_STATUS == TRUE)
							$this->db->saveHistoryData ( $client_id, 'h_client_province', $clientUniqueID, 'addSchoolProvince', $client_id, $_POST ['province'], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
					}
				 	else if($client ["province_id"]>0 && empty($province_id)){//school is being removed from province
						$provinceUpdated = $clientModel->removeClientFromProvince ( $client_id, $client ["province_id"] );
							
						if (OFFLINE_STATUS == TRUE) {
							// start---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
							$action_network_json = json_encode ( array (
									'client_id' => $client_id,
									'province_id' => $client ["province_id"]
							) );
							$this->db->saveHistoryData ( $client_id, 'h_client_province', $clientUniqueID, 'removeSchoolProvince', $client_id, $client ["province_id"], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving remove school network data on 08-03-2016 by Mohit Kumar
						}
					}
					else if($client ["province_id"]>0 && !empty($_POST['province']) && $client ["province_id"] != $_POST ['province']){//province is different from the previous						
						$province_id = $_POST['province'];
						$addedToProvince = $clientModel->updateClientProvince ( $client_id, $province_id );
						// start---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
						$action_network_json = json_encode ( array (
								'client_id' => $client_id,
								'province_id' => $province_id
						) );
						if (OFFLINE_STATUS == TRUE)
							$this->db->saveHistoryData ( $client_id, 'h_client_province', $clientUniqueID, 'addSchoolProvince', $client_id, $_POST ['province'], $action_network_json, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving add school network data on 04-03-2016 by Mohit Kumar
					}
					
				}
				
				if ($updated && $networkUpdated && $this->db->commit ()) {
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["message"] = "School/College successfully updated";
				} else {
					
					$this->db->rollback ();
					
					$this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
				}
			}
		}
	}
	function uploadFileAction() {
		$maxUploadFileSize = 104857600; // in bytes
		
		$allowedExt = array (
				"jpeg",
				"png",
				"gif",
				"jpg",
				"avi",
				"mp4",
				"mov",
				"doc",
				"docx",
				"txt",
				"xls",
				"xlsx",
				"pdf",
				"csv",
				'xml',
				'pptx',
				'ppt',
				'cdr',
				'mp3',
				'wav' 
		);
		
		if (! in_array ( "upload_file", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_FILES ['file'] )) {
			
			$this->apiResult ["message"] = "No file uploaded with this request\n";
		} else if ($_FILES ['file'] ['error'] > 0) {
			
			$this->apiResult ["message"] = "File contains error or error occurred while uploading\n";
		} else if (! ($_FILES ['file'] ['size'] > 0)) {
			
			$this->apiResult ["message"] = "Invalid file size or empty file\n";
		} else if ($_FILES ['file'] ['size'] > $maxUploadFileSize) {
			
			$this->apiResult ["message"] = "File too big\n";
		} else {
			
			$nArr = explode ( ".", $_FILES ['file'] ['name'] );
			
			$ext = strtolower ( array_pop ( $nArr ) );
			
			if (in_array ( $ext, $allowedExt ) && count ( $nArr ) > 0) {
				
				$newName = sanitazifileName(str_replace ( " ", "_", substr ( implode ( "_", $nArr ), 0, 35 ) ) . "_" . rand ( 1, 9999 ) . "_" . time () ). "." . $ext;
				//echo UPLOAD_PATH . "" . $newName ;
				if (upload_file(UPLOAD_PATH . "" . $newName,$_FILES ['file'] ['tmp_name'])) {
					
					$diagnosticModel = new diagnosticModel ();
					
					$id = $diagnosticModel->addUploadedFile ( $newName, $this->user ['user_id'] );
					
					if ($id > 0) {
						
						$this->apiResult ["message"] = "File successfully uploaded";
						
						$this->apiResult ["status"] = 1;
						
						$this->apiResult ["id"] = $id;
						
						$this->apiResult ["name"] = $newName;
						
						$this->apiResult ["ext"] = $ext;
						
						$this->apiResult ["url"] = UPLOAD_URL . "" . $newName;
					} else {
						
						$this->apiResult ["message"] = "Unable to make entry in database";
						
						@unlink ( UPLOAD_PATH . "" . $newName );
					}
				} else {
					
					$this->apiResult ["message"] = "Error occurred while moving file\n";
				}
			} else {
				
				$this->apiResult ["message"] = "Invalid file extension. Only " . implode ( ", ", $allowedExt ) . " type files are allowed\n";
			}
		}
	}
	function addKeyNoteAction() {
		$diagnosticModel = new diagnosticModel ();
		
		if (empty ( $_POST ['assessment_id'] )) {
			
			$this->apiResult ["message"] = "Review id cannot be empty\n";
		} else if (empty ( $_POST ['level_type'] )) {
			
			$this->apiResult ["message"] = "Level type  cannot be empty\n";
		} else if (empty ( $_POST ['instance_id'] )) {
			$this->apiResult ["message"] = "Instance Id  cannot be empty\n";
		} else if ($assessment = $diagnosticModel->getAssessmentByRole ( $_POST ['assessment_id'], 4 )) {
			
			if ($assessment ["status"] == 1 && ! in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] )) {
				
				$this->apiResult ["message"] = "You are not authorized to update review after submission\n";
			} else if ($assessment ["status"] == 0 && $assessment ["user_id"] != $this->user ['user_id']) {
				
				$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			} else {
				
				$type = empty ( $_POST ['type'] ) ? '' : $_POST ['type'];
				
				$instance_type = $_POST ['level_type'];
				$instance_type_id = $_POST ['instance_id'];
                                $type_q=isset($_POST['type_q'])?$_POST['type_q']:'';
				
				$this->apiResult ["status"] = 1;
                                
                                //print_r($assessment);
				
				// $this->apiResult["content"]=$diagnosticModel::getAssessorKeyNoteHtmlRow($_POST['kpa_id'],$akn_id,'',$type);
				//echo $type;
                                if($type_q=="kpa" && $type=="recommendation" && $assessment['assessment_type_id']==1){
                                $getJSforKPA=$diagnosticModel->getJSforKPA($instance_type_id);    
                                $this->apiResult ["content"] = $diagnosticModel::getAssessorKeyNoteHtmlRow ( $instance_type_id, 'new', '', $type,0,1,$type_q,$getJSforKPA );    
                                }else{
                                $this->apiResult ["content"] = $diagnosticModel::getAssessorKeyNoteHtmlRow ( $instance_type_id, 'new', '', $type );
                                }
                                
				$this->apiResult ["message"] = "Successfully added";
			}
		} else {
			
			$this->apiResult ["message"] = "Wrong review id\n";
		}
	}
	function addExternalReviewTeamAction() {
		if (empty ( $_POST ['sn'] )) {
			
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} 

		else 

		{
			
			$assessmentModel = new assessmentModel ();
			
			$this->apiResult ["status"] = 1;
			if($_POST ['frm']=="edit_college_assessment_form" || $_POST ['frm']=="create_college_assessment_form"){
			$this->apiResult ["content"] = $assessmentModel->getExternalReviewTeamHTMLRow ( $_POST ['sn'],'college');
                        }else if($_POST ['frm']=="create_school_assessment_form" || $_POST ['frm']=="edit_school_assessment_form"){
			$this->apiResult ["content"] = $assessmentModel->getExternalReviewTeamHTMLRow ( $_POST ['sn'],'school');
                        }else{
                        $this->apiResult ["content"] = $assessmentModel->getExternalReviewTeamHTMLRow ( $_POST ['sn'] );    
                        }
		}
	}
        
        
        function addImpactTeamAction() {
		if (empty ( $_POST ['sn'] )) {
			
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} else if(empty($_POST ['id_c'])){
                    
                    $this->apiResult ["message"] = "Please provide a id.\n";
                }

		else 

		{
			
			$assessmentModel = new assessmentModel ();
			
			$this->apiResult ["status"] = 1;
			
                        //$this->apiResult ["content"] = $assessmentModel->getExternalReviewTeamHTMLRow ( $_POST ['sn'] );  
                         $this->apiResult ["content"] = $assessmentModel->getExternalImpactTeamHTMLRow ( $_POST ['sn'],$_POST ['id_c'] );
                        
		}
	}
        
        
        function addActionTeamAction() {
		if (empty ( $_POST ['sn'] )) {
			
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} 

		else 

		{
			
			$assessmentModel = new assessmentModel ();
			
			$this->apiResult ["status"] = 1;
			
                        //$this->apiResult ["content"] = $assessmentModel->getExternalReviewTeamHTMLRow ( $_POST ['sn'] );  
                         $this->apiResult ["content"] = $assessmentModel->getActionTeamHTMLRow ( $_POST ['sn']);
                        
		}
	}
        
        
        function addActionActityAction() {
		if (empty ( $_POST ['sn'] )) {
			
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} 

		else 

		{
			
			$assessmentModel = new assessmentModel ();
			
			$this->apiResult ["status"] = 1;
			
                        //$this->apiResult ["content"] = $assessmentModel->getExternalReviewTeamHTMLRow ( $_POST ['sn'] );  
                         $this->apiResult ["content"] = $assessmentModel->getActionActivityHTMLRow ( $_POST ['sn']);
                        
		}
	}
        function addActionImpactStmntAction() {
		if (empty ( $_POST ['sn'] )) {
			
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} 

		else 

		{
                    $impactStmntId = !empty ( $_POST ['impactStmntId']) ? $_POST ['impactStmntId']:0;
                    $aqsDataModel = new aqsDataModel();
                    $actionModel = new actionModel;
                    $designations=$aqsDataModel->getDesignations();
                    $classes=$aqsDataModel->getSchoolClassList();
                    $methods=$actionModel->getImpactMethod();
                    $assessmentModel = new assessmentModel ();
                    $this->apiResult ["status"] = 1;
                    //$this->apiResult ["content"] = $assessmentModel->getExternalReviewTeamHTMLRow ( $_POST ['sn'] );  
                     $this->apiResult ["content"] = $assessmentModel->getActionImpactStmntHTMLRow ( $_POST ['sn'],'',$impactStmntId,$designations,$classes,'',$methods);
                        
		}
	}
        
	function addFacilitatorReviewTeamAction() {
		if (empty ( $_POST ['sn'] )) {
			
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} 

		else 

		{
			
			$assessmentModel = new assessmentModel ();
			
			$this->apiResult ["status"] = 1;
			if($_POST ['frm']=="edit_college_assessment_form" || $_POST ['frm']=="create_college_assessment_form"){
                        $this->apiResult ["content"] = $assessmentModel->getFacilitatorReviewTeamHTMLRow ( $_POST ['sn'] ,'college');    
                        }else if($_POST ['frm']=="create_school_assessment_form" || $_POST ['frm']=="edit_school_assessment_form"){
                        $this->apiResult ["content"] = $assessmentModel->getFacilitatorReviewTeamHTMLRow ( $_POST ['sn'] ,'school');    
                        }else{
                        $this->apiResult ["content"] = $assessmentModel->getFacilitatorReviewTeamHTMLRow ( $_POST ['sn'] );    
                        }
			
		}
	}
        //Added by Vikas for workshop add
        function addFacilitatorTeamAction() {
		if (empty ( $_POST ['sn'] )) {
			
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} 

		else 

		{
			
			$workshopModel = new WorkshopModel ();
			
			$this->apiResult ["status"] = 1;
			
			$this->apiResult ["content"] = $workshopModel->getFacilitatorTeamHTMLRow ( $_POST ['sn'] );
		}
	}
        //Added by Vikas for workshop add
	function addFilterRowAction() {
		if (empty ( $_POST ['sn'] )) {
			
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} 

		else 

		{
			
			// $assessmentModel=new customreportModel();
			
			$isDashboard = ! empty ( $_POST ['isDashboard'] ) ? $_POST ['isDashboard'] : 0;
			
			$this->apiResult ["status"] = 1;
			
			$this->apiResult ["content"] = customreportModel::getFilterRow ( $_POST ['sn'], 0, $isDashboard );
		}
	}
	function addNetworkExpRowAction() {
		if (empty ( $_POST ['sn'] )) {
			
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} 

		else 

		{
			
			// $assessmentModel=new customreportModel();
			
			$this->apiResult ["status"] = 1;
			
			$this->apiResult ["content"] = customreportModel::getExperienceRow ( $_POST ['sn'] );
		}
	}
	function getDateFieldsAction() {
		$this->apiResult ["content"] = customreportModel::getDateRow ();
		$this->apiResult ["status"] = 1;
	}
	function addTeamRowAction() {
		if (empty ( $_POST ['sn'] )) {
			
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} else if (empty ( $_POST ['type'] )) {
			
			$this->apiResult ["message"] = "Please provide the type of row(school/adhyayan/teacherAssessor)\n";
		} else {
			
			$this->apiResult ["status"] = 1;
			
			switch ($_POST ['type']) {
				
				case 'adhyayan' :
				
				case 'school' :
					
					$aqsDataModel = new aqsDataModel ();
					
					$this->apiResult ["content"] = $aqsDataModel->getAqsTeamHtmlRow ( $_POST ['sn'], $_POST ['type'] == "school" ? 1 : 0, '', '', '', '', '', '', 1 );
					
					break;
				
				case 'teacherAssessor' :
					
					$this->apiResult ["content"] = assessmentModel::getTeacherAssessorHTMLRow ( $_POST ['sn'], '', '', '', '', 1 );
					
					break;
				
				case 'teacherForAssessment' :
					
					$assessmentModel = new assessmentModel ();
					
					$this->apiResult ["content"] = $assessmentModel->getTeacherInTeacherAssessmentHTMLRow ( $_POST ['attach'], $_POST ['sn'], '', '', '', '',0, 0, 0, 0, 1 );
					
					break;
                                case 'studentForAssessment' :
                                        $assessmentModel = new assessmentModel ();
					
					$this->apiResult ["content"] = $assessmentModel->getStudentInStudentAssessmentHTMLRow ( $_POST ['attach'], $_POST ['sn'], '', '', '', '',0, 0, 0, 1 );
					
					break;
                                default :
					
					$this->apiResult ["status"] = 0;
					
					$this->apiResult ["message"] = "Please provide a valid type of row(school/adhyayan/teacherAssessor)\n";
					
					break;
			}
		}
	}
	
	/*
	 * function deleteFileAction(){
	 *
	 *
	 *
	 * if(empty($_POST['file_id'])){
	 *
	 *
	 *
	 * $this->apiResult["message"] = "File id cannot be empty\n";
	 *
	 *
	 *
	 * }else{
	 *
	 *
	 *
	 * $diagnosticModel=new diagnosticModel();
	 *
	 *
	 *
	 * $file=$diagnosticModel->getFile($_POST['file_id']);
	 *
	 *
	 *
	 * if(array_intersect($this->user['roles'],array(1,2)) || $this->user['user_id']==$file['uploaded_by']){//roles need to be replaced by capabilities
	 *
	 *
	 *
	 * if($file['score_file_id']>0){
	 *
	 *
	 *
	 * if(!$diagnosticModel->unlinkFileFromScore($file['score_file_id'])){
	 *
	 *
	 *
	 * $this->apiResult["message"] = "Error occurred while unlinking file from db\n";
	 *
	 *
	 *
	 * return;
	 *
	 *
	 *
	 * }
	 *
	 *
	 *
	 * }
	 *
	 *
	 *
	 * if(!$diagnosticModel->deleteFileFromDB($_POST['file_id'])){
	 *
	 *
	 *
	 * $this->apiResult["message"] = "Error occurred while unlinking file from db\n";
	 *
	 *
	 *
	 * }else{
	 *
	 *
	 *
	 * @unlink(ROOT."uploads/".$file['file_name']);
	 *
	 *
	 *
	 * $this->apiResult["message"] = "File successfully removed\n";
	 *
	 *
	 *
	 * $this->apiResult["status"] = 1;
	 *
	 *
	 *
	 * }
	 *
	 *
	 *
	 * }else{
	 *
	 *
	 *
	 * $this->apiResult["message"] = "You are not authorized to perform this task.\n";
	 *
	 *
	 *
	 * }
	 *
	 *
	 *
	 * }
	 *
	 *
	 *
	 * }
	 */
        function replicateAssessmentAction(){
                $diagnosticModel = new diagnosticModel ();
		
		if (empty ( $_POST ['assessment_id'])) {
			
			$this->apiResult ["message"] = "Review id cannot be empty\n";
		}else{
                
                $self_review=$diagnosticModel->getAssessmentByRole($_POST ['assessment_id'],3);
                $external_review=$diagnosticModel->getAssessmentByRole($_POST ['assessment_id'],4);
                
                if($self_review['status']!=1 || $self_review['percComplete']<100){
                    $this->apiResult ["message"] = "Self Review should be fully completed\n";
                }else if($external_review['percComplete']>=100 && $external_review['status']==1){
                    $this->apiResult ["message"] = "This is not allowed\n";
                }else if($external_review['is_replicated']==1){
                    $this->apiResult ["message"] = "This is not allowed\n";
                }else{
                
                //print_r($self_review);
                $internal_reviewer=$self_review['user_id'];
                $external_reviewer=$external_review['user_id'];
                $a=$diagnosticModel->getAllJudgementScore($_POST ['assessment_id'],$internal_reviewer);
                $b=$diagnosticModel->getAllCoreQuestionScore($_POST ['assessment_id'],$internal_reviewer);
                $c=$diagnosticModel->getAllKeyQuestionScore($_POST ['assessment_id'],$internal_reviewer);
                $d=$diagnosticModel->getAllKpaScore($_POST ['assessment_id'],$internal_reviewer);
                $this->db->start_transaction ();
                $success=true;
                $success_1=true;
                $success_2=true;
                $success_3=true;
                $fail=0;
                $noOfComplete=0;
                $total=0;
                //$diagnosticModel->updateJudgementStatementScore();
                foreach($a as $key=>$val){
                    
                $js_id=$val['judgement_statement_instance_id'];
                $assessment_id=$val['assessment_id'];
                $assessor_id=$external_reviewer;
                $added_by = $this->user ['user_id'];
                $rating_id=$val['rating_id'];
                //$text=$val['evidence_text'];
                $text="";
                $score_id = $diagnosticModel->updateJudgementStatementScore ( $js_id, $assessment_id, $assessor_id, $added_by, $rating_id, $text );
                if ($score_id == false) {
									
					$success = false;
                                        $fail=1;
                                        break; 
					}else{
                                            $noOfComplete++;
                                        }
                $total++;                        
                }
                
                
                foreach($b as $key=>$val){
                    $cq_id=$val['core_question_instance_id'];
                    $assessment_id=$val['assessment_id'];
                    $assessor_id=$external_reviewer;
                    $rating_id=$val['d_rating_rating_id'];
                    //$diagnosticModel->deleteCoreQuestionScore ( $cq_id, $assessment_id, $assessor_id );
                    $cqQestionStatus=$diagnosticModel->getSingleCoreQuestionScore ( $cq_id, $assessment_id, $assessor_id );
                    if(count($cqQestionStatus)>0){
                    $score_id=$diagnosticModel->updateCoreQuestionScore ( $cq_id, $assessment_id, $assessor_id, $rating_id );    
                    }else{
                    $score_id=$diagnosticModel->insertCoreQuestionScore ( $cq_id, $assessment_id, $assessor_id, $rating_id );
                    }
                    if ($score_id == false) {
									
					$success_1 = false;
                                        $fail=2;
                                        break;
					}else{
                                            $noOfComplete++;
                                        }
                $total++;                
                }
                
                foreach($c as $key=>$val){
                    $kq_id=$val['key_question_instance_id'];
                    $assessment_id=$val['assessment_id'];
                    $assessor_id=$external_reviewer;
                    $rating_id=$val['d_rating_rating_id'];
                    //$diagnosticModel->deleteKeyQuestionScore ( $kq_id, $assessment_id, $assessor_id );
                    $kqQestionStatus=$diagnosticModel->getSingleKeyQuestionScore ( $kq_id, $assessment_id, $assessor_id );
                    if(count($kqQestionStatus)>0){
                    $score_id=$diagnosticModel->updateKeyQuestionScore ( $kq_id, $assessment_id, $assessor_id, $rating_id );    
                    }else{
                    $score_id=$diagnosticModel->insertKeyQuestionScore ( $kq_id, $assessment_id, $assessor_id, $rating_id );
                    }
                    if ($score_id == false) {
									
					$success_2 = false;
                                        $fail=3;
                                        break;
					}else{
                                            $noOfComplete++;
                                        }
                $total++;                
                }
                
                foreach($d as $key=>$val){
                    $kpa_id=$val['kpa_instance_id'];
                    $assessment_id=$val['assessment_id'];
                    $assessor_id=$external_reviewer;
                    $rating_id=$val['d_rating_rating_id'];
                    //$diagnosticModel->deleteKpaScore ( $kpa_id, $assessment_id, $assessor_id );
                    $kpaQestionStatus=$diagnosticModel->getSingleKpaScore ( $kpa_id, $assessment_id, $assessor_id );
                    if(count($kpaQestionStatus)>0){
                    $score_id=$diagnosticModel->updateKpaScore ( $kpa_id, $assessment_id, $assessor_id, $rating_id );    
                    }else{
                    $score_id=$diagnosticModel->insertKpaScore ( $kpa_id, $assessment_id, $assessor_id, $rating_id );
                    }
                    if ($score_id == false) {
									
					$success_3 = false;
                                        $fail=4;
                                        break;
					}else{
                                            $noOfComplete++;
                                        }
                $total++;                
                }
                
                $kpa_id_percentage=isset($kpa_id)?$kpa_id:0;
                $keynotes=$diagnosticModel->getKeyNotesPer($_POST ['assessment_id'],$kpa_id_percentage);
                //print_r($a);
                //echo $fail;
                if($keynotes>0){
                $completedPerc = round ( (100 * $noOfComplete) / $total, 2 );   
                }else{
                $completedPerc = round ( (100 * $noOfComplete-2) / $total, 2 );
                }
						
		if ($success && ! $diagnosticModel->updateAssessmentPercentage ( $_POST ['assessment_id'], $external_reviewer, $completedPerc )) {
							
		 $success = false;
		}
                
                if ($success && ! $diagnosticModel->updateAssessmentReplicate($_POST ['assessment_id'])){
                 $success = false;   
                }
                
                if($success && $success_1 && $success_2 && $success_3 && $this->db->commit ()){
                $this->apiResult ["status"] = 1;
		$this->apiResult ["message"] = "Successfully saved";
                }else{
                    $this->db->rollback ();
		    $this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
                }
                }
                
                }
                
                
                
        }
        
        function saveInternalAssessorRatings($assessment_id,$completedPerc,$is_submit,$lang_id){
                $diagnosticModel = new diagnosticModel ();
		if (empty ( $assessment_id)) {
			
			$this->apiResult ["message"] = "Review id cannot be empty\n";
		}else{
               
                $external_review =$diagnosticModel->getAssessmentByRole($assessment_id,3,$lang_id);
                $self_review=$diagnosticModel->getAssessmentByRole($assessment_id,4,$lang_id);
                
                
                
                $internal_reviewer=$self_review['user_id'];
                $external_reviewer=$external_review['user_id'];
                $a=$diagnosticModel->getAllJudgementScore($assessment_id,$internal_reviewer);
                $b=$diagnosticModel->getAllCoreQuestionScore($assessment_id,$internal_reviewer);
                $c=$diagnosticModel->getAllKeyQuestionScore($assessment_id,$internal_reviewer);
                $d=$diagnosticModel->getAllKpaScore($assessment_id,$internal_reviewer);
                $this->db->start_transaction ();
                $success=true;
                $success_1=true;
                $success_2=true;
                $success_3=true;
                $fail=0;
                $noOfComplete=0;
                $total=0;
                //$diagnosticModel->updateJudgementStatementScore();
                foreach($a as $key=>$val){
                    
                $js_id=$val['judgement_statement_instance_id'];
                $assessment_id=$val['assessment_id'];
                $assessor_id=$external_reviewer;
                $added_by = $this->user ['user_id'];
                $rating_id=$val['rating_id'];
                //$text=$val['evidence_text'];
                $text="";
                $score_id = $diagnosticModel->updateJudgementStatementScore ( $js_id, $assessment_id, $assessor_id, $added_by, $rating_id, $text );
                if ($score_id == false) {
									
					$success = false;
                                        $fail=1;
                                        break; 
					}else{
                                            $noOfComplete++;
                                        }
                $total++;                        
                }
                
                
                foreach($b as $key=>$val){
                    $cq_id=$val['core_question_instance_id'];
                    $assessment_id=$val['assessment_id'];
                    $assessor_id=$external_reviewer;
                    $rating_id=$val['d_rating_rating_id'];
                    //$diagnosticModel->deleteCoreQuestionScore ( $cq_id, $assessment_id, $assessor_id );
                    $cqQestionStatus=$diagnosticModel->getSingleCoreQuestionScore ( $cq_id, $assessment_id, $assessor_id );
                    if(count($cqQestionStatus)>0){
                    $score_id=$diagnosticModel->updateCoreQuestionScore ( $cq_id, $assessment_id, $assessor_id, $rating_id );    
                    }else{
                    $score_id=$diagnosticModel->insertCoreQuestionScore ( $cq_id, $assessment_id, $assessor_id, $rating_id );
                    }
                    if ($score_id == false) {
									
					$success_1 = false;
                                        $fail=2;
                                        break;
					}else{
                                            $noOfComplete++;
                                        }
                $total++;                
                }
                
                foreach($c as $key=>$val){
                    $kq_id=$val['key_question_instance_id'];
                    $assessment_id=$val['assessment_id'];
                    $assessor_id=$external_reviewer;
                    $rating_id=$val['d_rating_rating_id'];
                    //$diagnosticModel->deleteKeyQuestionScore ( $kq_id, $assessment_id, $assessor_id );
                    $kqQestionStatus=$diagnosticModel->getSingleKeyQuestionScore ( $kq_id, $assessment_id, $assessor_id );
                    if(count($kqQestionStatus)>0){
                    $score_id=$diagnosticModel->updateKeyQuestionScore ( $kq_id, $assessment_id, $assessor_id, $rating_id );    
                    }else{
                    $score_id=$diagnosticModel->insertKeyQuestionScore ( $kq_id, $assessment_id, $assessor_id, $rating_id );
                    }
                    if ($score_id == false) {
									
					$success_2 = false;
                                        $fail=3;
                                        break;
					}else{
                                            $noOfComplete++;
                                        }
                $total++;                
                }
                
                foreach($d as $key=>$val){
                    $kpa_id=$val['kpa_instance_id'];
                    $assessment_id=$val['assessment_id'];
                    $assessor_id=$external_reviewer;
                    $rating_id=$val['d_rating_rating_id'];
                    //$diagnosticModel->deleteKpaScore ( $kpa_id, $assessment_id, $assessor_id );
                    $kpaQestionStatus=$diagnosticModel->getSingleKpaScore ( $kpa_id, $assessment_id, $assessor_id );
                    if(count($kpaQestionStatus)>0){
                    $score_id=$diagnosticModel->updateKpaScore ( $kpa_id, $assessment_id, $assessor_id, $rating_id );    
                    }else{
                    $score_id=$diagnosticModel->insertKpaScore ( $kpa_id, $assessment_id, $assessor_id, $rating_id );
                    }
                    if ($score_id == false) {
									
					$success_3 = false;
                                        $fail=4;
                                        break;
					}else{
                                            $noOfComplete++;
                                        }
                $total++;                
                }
                
                $kpa_id_percentage=isset($kpa_id)?$kpa_id:0;
                $keynotes=$diagnosticModel->getKeyNotesPer($assessment_id,$kpa_id_percentage);
                //print_r($a);
                //echo $fail;
                /*if($keynotes>0){
                $completedPerc = round ( (100 * $noOfComplete) / $total, 2 );   
                }else{
                $completedPerc = round ( (100 * $noOfComplete-2) / $total, 2 );
                }*/
		if(empty($is_submit)){				
                    if ($success && ! $diagnosticModel->updateAssessmentPercentage ( $assessment_id, $external_reviewer, $completedPerc )) {

                     $success = false;
                    }
                }else if ($success && ! $diagnosticModel->updateAssessmentPercentageAndStatus ( $assessment_id, $external_reviewer, $completedPerc,1)) {
                     $success = false;
                }
                
                if ($success && ! $diagnosticModel->updateAssessmentReplicate($assessment_id)){
                 $success = false;   
                }
                
                if($success && $success_1 && $success_2 && $success_3 && $this->db->commit ()){
                //$this->apiResult ["status"] = 1;
		//$this->apiResult ["message"] = "Successfully saved";
                }else{
                    $this->db->rollback ();
		   // $this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
                }
                }
                
                
                
                
        }
	function saveAssessmentAction() {
               // print_r($_POST);                
		$diagnosticModel = new diagnosticModel ();
		$assessmentModel = new assessmentModel ();
                $lang_id = isset($_POST ['lang_id'])?$_POST ['lang_id']:DEFAULT_LANGUAGE;
                $external = isset($_POST ['external'])?$_POST ['external']:0;
                $is_collaborative = isset($_POST ['is_collaborative'])?$_POST ['is_collaborative']:0;	
                $isLeadSave = 0;
		if (empty ( $_POST ['assessment_id'] )) {
			
			$this->apiResult ["message"] = "Review id cannot be empty\n";
		} else if (empty ( $_POST ['assessor_id'] )) {
			
			$this->apiResult ["message"] = "Reviewer id cannot be empty\n";
		} else if ($assessment = $diagnosticModel->getAssessmentByUser ( $_POST ['assessment_id'], $_POST ['assessor_id'],$lang_id,$external )) {
			
                     
			/*if ($assessment ['aqs_status'] != 1) {
				
				$this->apiResult ["message"] = "You are not authorized to fill review before School profile\n";
			} else */
                    
                        //echo "<pre>";print_r($_POST);die;
                        if ($assessment ['report_published'] == 1) {
				
				$this->apiResult ["message"] = "You can't update data after publishing reports\n";
			} else if ($assessment ["status"] == 1 && ! in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] )) {
				
				$this->apiResult ["message"] = "You are not authorized to update review after submission\n";
			} else if ($assessment ["status"] == 0  && $assessment ["user_id"] != $this->user ['user_id']) {
				
				$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			} else {
				
                             
				$assessment_id = $_POST ['assessment_id'];
				
				$assessor_id = $_POST ['assessor_id'];
				
				$added_by = $this->user ['user_id'];
				
				$singleKpaId = 0; // empty($_POST['kpa_id'])?0:$_POST['kpa_id']; //if kpa id is given then we will check & save data for single kpa otherwise if it is 0 then we will check & save data for all kpas
				
                                $isLeadAssessorKpa = isset($_POST ['isLeadAssessorKpa'])?trim($_POST ['isLeadAssessorKpa']):'';
                                $isLeadAssessor = isset($_POST ['isLeadAssessor'])?trim($_POST ['isLeadAssessor']):'';
                                $isRevCompleteNtSubmitted= isset($_POST ['isRevCompleteNtSubmitted'])?trim($_POST ['isRevCompleteNtSubmitted']):'';
				
				$added_by = $this->user ['user_id'];
                                //echo "kkkk";die;
                                $kpas = array();
                                $userKpas = array();
                                $allKpas = array();
                                $kqs = array();
                                $cqs = array();
                                $jss = array();
                                $singleKpaId = 0; // empty($_POST['kpa_id'])?0:$_POST['kpa_id']; //if kpa id is given then we will check & save data for single kpa otherwise if it is 0 then we will check & save data for all kpas
                                if($isLeadAssessorKpa==1 && $is_collaborative == 1){
                                     $isLeadSave = 1; 
                                }
                                
                                if($isLeadAssessorKpa && $is_collaborative && $isLeadSave==0){
                                   
                                    $percentageData = $diagnosticModel->getExternalTeamRatingPerc($assessment_id); 
                                    //print_r($percentageData);die;
                                  
                                    if(!empty($percentageData)){
                                    $allAccessorsId = explode(",",$percentageData['user_ids']);
                                    foreach($allAccessorsId as $key=>$val) {
                                        
                                           //echo $val;
                                         $kpas =   $this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $val,0,$lang_id,$is_collaborative,1,$isLeadAssessorKpa), "kpa_instance_id");
                                         //print
                                         $userKpas = array_keys($kpas);
                                         $allKpas = $allKpas+$kpas;
                                         
                                         $kqs  = $kqs + $this->db->array_grouping($diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $val,0,$lang_id,1,$userKpas,$isLeadAssessorKpa), "kpa_instance_id", "key_question_instance_id");
                                         $cqs  = $cqs + $this->db->array_grouping($diagnosticModel->getCoreQuestionsForAssessment($assessment_id, $val,0,$lang_id,1,$userKpas,$isLeadAssessorKpa), "key_question_instance_id", "core_question_instance_id");
                                         $jss  = $jss + $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForAssessment($assessment_id, $val,0,$lang_id,1,$userKpas,$isLeadAssessorKpa), "core_question_instance_id", "judgement_statement_instance_id");
                                        // ksort($kqs);
                                         
                                        
                                         //die;
                                        }
                                    }
                                }
                                
                                //echo "<pre>";print_r($jss); die();
                                if($is_collaborative && (($isLeadAssessor && $isLeadSave==0) || (!$isLeadAssessor))) {
				$kpas = $allKpas+$this->db->array_col_to_key ( $diagnosticModel->getKpasForAssessment ( $assessment_id, $assessor_id, $singleKpaId,$lang_id,$is_collaborative,$external ), "kpa_instance_id" );
                                ksort($kpas);
                               
				$kqs = $kqs+$this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForAssessment ( $assessment_id, $assessor_id, $singleKpaId ,$lang_id,$external), "kpa_instance_id", "key_question_instance_id" );
				ksort($kqs);
                                
				$cqs = $cqs+$this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForAssessment ( $assessment_id, $assessor_id, $singleKpaId,$lang_id,$external ), "key_question_instance_id", "core_question_instance_id" );
				ksort($cqs);
                               
				$jss = $jss+$this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForAssessment ( $assessment_id, $assessor_id, $singleKpaId,$lang_id,$external ), "core_question_instance_id", "judgement_statement_instance_id" );
				ksort($jss);
                                }else{
                                
                                $kpas = $allKpas+$this->db->array_col_to_key ( $diagnosticModel->getKpasForAssessment ( $assessment_id, $assessor_id, $singleKpaId,$lang_id,0,0 ), "kpa_instance_id" );
                                ksort($kpas);
                               
				$kqs = $kqs+$this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForAssessment ( $assessment_id, $assessor_id, $singleKpaId ,$lang_id,0), "kpa_instance_id", "key_question_instance_id" );
				ksort($kqs);
                                
				$cqs = $cqs+$this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForAssessment ( $assessment_id, $assessor_id, $singleKpaId,$lang_id,0 ), "key_question_instance_id", "core_question_instance_id" );
				ksort($cqs);
                               
				$jss = $jss+$this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForAssessment ( $assessment_id, $assessor_id, $singleKpaId,$lang_id,0 ), "core_question_instance_id", "judgement_statement_instance_id" );
				ksort($jss);    
                                    
                                }
                                
				$rScheme = $this->db->array_grouping ( $diagnosticModel->getDiagnosticRatingScheme ( $assessment ['diagnostic_id'] ), "type", "order" );
				//print_r($rScheme);
				if (count ( $rScheme ['js'] ) == 0 || count ( $rScheme ['kpa'] ) == 0 || count ( $rScheme ['sq'] ) == 0 || count ( $rScheme ['kq'] ) == 0) {
					
					$this->apiResult ["message"] = "Rating scheme does not exists for this diagnostic";
					
					return;
				}
				
				$rSchemeId = $rScheme ['js'] [1] ['scheme']; // echo 'sch: ';print_r($rScheme['js']);die;
				$akns = $assessment ['role'] == 4 ? $this->db->array_grouping ( $diagnosticModel->getAssessorKeyNotes ( $assessment_id ), "kpa_instance_id", "id" ) : array ();
				
				if (OFFLINE_STATUS == TRUE) {
					$uniqueID = $this->db->createUniqueID ( 'internalAssessment' );
				}
				
				$this->db->start_transaction ();
				
				$success = true;
				
				$complete = true;
				
				$kpa_count = 0;
				
				$noOfComplete = 0;
				
				$noOfIncomplete = 0;
                                $kpaIndex = 0;
                                //echo "<pre>";print_r($_POST);die;
                               
				
                                //echo count($cqs); echo "<pre>";print_r($cqs);die;
				foreach ( $kpas as $kpa_id => $kpa ) {
					
					$kpaJs_ratings = array ();
					$kpaSq_ratings = array ();
					$kpa_count ++;
					
					$kq_ratings = array ();
					
					$kq_count = 0;
					
					foreach ( $kqs [$kpa_id] as $kq_id => $kq ) {
						
						$kq_count ++;
						
						$cq_ratings = array ();
						
						$cq_count = 0;
						
						foreach ( $cqs [$kq_id] as $cq_id => $cq ) {
							
							$cq_count ++;
							
							$js_ratings = array ();
							
							$js_count = 0;
							
							foreach ( $jss [$cq_id] as $js_id => $js ) {
								
								$js_count ++;
                                                                
                                                                if(empty($js['score_id'])){ //echo $js['score_id'];
                                                                        //echo "<pre>";print_r($jss [$cq_id]);
                                                                        //echo "<pre>";print_r($js);
                                                                       // echo $i++;
                                                                }
                                                                //echo"sds";
								$val = empty ( $_POST ['data'] ["$kpa_id-$kq_id-$cq_id-$js_id"] ['value'] ) ? "" : $_POST ['data'] ["$kpa_id-$kq_id-$cq_id-$js_id"] ['value'];
								
								$text = empty ( $_POST ['data'] ["$kpa_id-$kq_id-$cq_id-$js_id"] ['text'] ) ? "" : trim ( $_POST ['data'] ["$kpa_id-$kq_id-$cq_id-$js_id"] ['text'] );
								
								$files = empty ( $_POST ['data'] ["$kpa_id-$kq_id-$cq_id-$js_id"] ['files'] ) ? array () : $_POST ['data'] ["$kpa_id-$kq_id-$cq_id-$js_id"] ['files'];
								
								sort ( $files, SORT_NUMERIC );
								
								$existing_files = array_keys ( diagnosticModel::decodeFileArray ( $js ['files'] ) );
								
								sort ( $existing_files, SORT_NUMERIC );
								
								$score_id = - 1;
								
								$rating_id = 20;
								
								if ($val > 0) {
									
									$js_ratings [] = $val;
									array_push ( $kpaJs_ratings, $val );
									
									$rating_id = $rScheme ['js'] [$val] ['rating_id'];
									
									$noOfComplete ++;
								} else
									
									$noOfIncomplete ++;
								
								if (empty ( $js ['score_id'] ) && ($val != "" || $text != "" || count ( $files ) > 0)) {
									
									// insert new score row if it does not exist and we have some data to put
									//echo"a". $js ['score_id'];
                                                                        //print_r($jss [$cq_id]);
                                                                    
									$score_id = $diagnosticModel->insertJudgementStatementScore ( $js_id, $assessment_id, $assessor_id, $added_by, $rating_id, $text );
									if (OFFLINE_STATUS == TRUE) {
										// start--> call function for save history for insert internal rating in history table on 21-03-2016 by Mohit Kumar
										$action_insert_judgement_statement_json = json_encode ( array (
												'judgement_statement_instance_id' => $js_id,
												'assessment_id' => $assessment_id,
												"assessor_id" => $assessor_id,
												'added_by' => $added_by,
												'rating_id' => $rating_id,
												'evidence_text' => $text,
												'isFinal' => 1,
												'date_added' => date ( "Y-m-d H:i:s" ) 
										) );
										$this->db->saveHistoryData ( $score_id, 'f_score', $uniqueID, 'internalAssessmentJudgementStatementInsert', $assessment_id, $assessor_id, $action_insert_judgement_statement_json, 0, date ( 'Y-m-d H:i:s' ) );
										// end--> call function for save history for insert internal rating in history table on 03-03-2016 by Mohit Kumar
									}
								} else if (! empty ( $js ['score_id'] ) && ($val != $js ['numericRating'] || $text != $js ['evidence_text'] || $existing_files != $files)) {
									
									// if score row already exists in our database and user made any changes to it then update data
									
									$score_id = $diagnosticModel->updateJudgementStatementScore ( $js_id, $assessment_id, $assessor_id, $added_by, $rating_id, $text );
									
									if (OFFLINE_STATUS == true) {
										// start--> call function for save history for update internal rating in history table on 21-03-2016 by Mohit Kumar
										$action_update_judgement_statement_json = json_encode ( array (
												'judgement_statement_instance_id' => $js_id,
												'assessment_id' => $assessment_id,
												"assessor_id" => $assessor_id,
												'added_by' => $added_by,
												'rating_id' => $rating_id,
												'evidence_text' => $text 
										) );
										$this->db->saveHistoryData ( $score_id, 'f_score', $uniqueID, 'internalAssessmentJudgementStatementUpdate', $assessment_id, $assessor_id, $action_update_judgement_statement_json, 0, date ( 'Y-m-d H:i:s' ) );
										$this->db->saveHistoryData ( $score_id, 'f_score', $uniqueID, 'internalAssessmentJudgementStatementUpdateInsert', $assessment_id, $assessor_id, $action_update_judgement_statement_json, 0, date ( 'Y-m-d H:i:s' ) );
										// end--> call function for save history for update internal rating in history table on 03-03-2016 by Mohit Kumar
									}
								} // else neither we need to insert new nor we need to update existing score
								
								if ($score_id == false) {
									
									$success = false;
								} else if ($score_id > 0) {
									
									foreach ( $files as $file_id )
										
										if (! $diagnosticModel->linkFileToScore ( $score_id, $file_id )) {
											
											$success = false;
										} else {
											if (OFFLINE_STATUS == TRUE) {
												$linkFileToScoreId = $this->db->get_last_insert_id ();
												// start--> call function for save history for insert link file to score in history table on 21-03-2016 by Mohit Kumar
												$action_insert_score_file_json = json_encode ( array (
														'score_id' => $score_id,
														'file_id' => $file_id 
												) );
												$this->db->saveHistoryData ( $linkFileToScoreId, 'h_score_file', $uniqueID, 'internalAssessmentScoreFileInsert', $score_id, $file_id, $action_insert_score_file_json, 0, date ( 'Y-m-d H:i:s' ) );
												// end--> call function for save history for insert link file to score in history table on 03-03-2016 by Mohit Kumar
											}
										}
								}
							}
							
							if ($js_count == count ( $js_ratings )) {
								
								$js_res = diagnosticModel::calculateStatementResult ( $js_ratings, $rSchemeId, 4 );
								
								$cq_ratings [] = $js_res;
								
								// print_r($js_res);print_r($rScheme['sq'][$js_res]['rating_id']);
								
								$rating_id = $rScheme ['sq'] [$js_res] ['rating_id'];
								array_push ( $kpaSq_ratings, $js_res );
								
								if (($cq ['score_id'] > 0 && ! $diagnosticModel->updateCoreQuestionScore ( $cq_id, $assessment_id, $assessor_id, $rating_id )) || (empty ( $cq ['score_id'] ) && ! $diagnosticModel->insertCoreQuestionScore ( $cq_id, $assessment_id, $assessor_id, $rating_id ))) {
									
									$success = false;
								} else {
									if (OFFLINE_STATUS == TRUE) {
										// start--> call function for save history for update and insert core question score in history table on 21-03-2016 by Mohit Kumar
										$action_core_question_json = json_encode ( array (
												'd_rating_rating_id' => $rating_id,
												'core_question_instance_id' => $cq_id,
												'assessment_id' => $assessment_id,
												'assessor_id' => $assessor_id 
										) );
										if ($cq ['score_id'] > 0) {
											$this->db->saveHistoryData ( $cq_id, 'h_cq_score', $uniqueID, 'internalAssessmentCoreQuestionUpdate', $assessment_id, $assessor_id, $action_core_question_json, 0, date ( 'Y-m-d H:i:s' ) );
										} else if (empty ( $cq ['score_id'] )) {
											$coreQuestionId = $this->db->get_last_insert_id ();
											$this->db->saveHistoryData ( $coreQuestionId, 'h_cq_score', $uniqueID, 'internalAssessmentCoreQuestionInsert', $assessment_id, $assessor_id, $action_core_question_json, 0, date ( 'Y-m-d H:i:s' ) );
										}
										// end--> call function for save history for update and insert core qiestion score in history table on 03-03-2016 by Mohit Kumar
									}
								}
							} else if ($cq ['score_id'] > 0) {
								
								$diagnosticModel->deleteCoreQuestionScore ( $cq_id, $assessment_id, $assessor_id );
								
								if (OFFLINE_STATUS == TRUE) {
									// start--> call function for save history for delete core question score in history table on 21-03-2016 by Mohit Kumar
									$action_core_question_json = json_encode ( array (
											"core_question_instance_id" => $cq_id,
											"assessment_id" => $assessment_id,
											"assessor_id" => $assessor_id 
									) );
									$this->db->saveHistoryData ( $cq_id, 'h_cq_score', $uniqueID, 'internalAssessmentCoreQuestionDelete', $assessment_id, $assessor_id, $action_core_question_json, 0, date ( 'Y-m-d H:i:s' ) );
									// end--> call function for save history for delete core qiestion score in history table on 03-03-2016 by Mohit Kumar
								}
							}
						}
						
						if ($cq_count == count ( $cq_ratings )) {
							
							$cq_res = diagnosticModel::calculateStatementResult ( $cq_ratings, $rSchemeId, 3 );
							
							$kq_ratings [] = $cq_res;
							
							$rating_id = $rScheme ['sq'] [$cq_res] ['rating_id'];
							
							if (($kq ['score_id'] > 0 && ! $diagnosticModel->updateKeyQuestionScore ( $kq_id, $assessment_id, $assessor_id, $rating_id )) || (empty ( $kq ['score_id'] ) && ! $diagnosticModel->insertKeyQuestionScore ( $kq_id, $assessment_id, $assessor_id, $rating_id ))) {
								
								$success = false;
							} else {
								if (OFFLINE_STATUS == TRUE) {
									// start--> call function for save history for update and insert core question score in history table on 21-03-2016 by Mohit Kumar
									$action_key_question_json = json_encode ( array (
											'd_rating_rating_id' => $rating_id,
											'key_question_instance_id' => $kq_id,
											'assessment_id' => $assessment_id,
											'assessor_id' => $assessor_id 
									) );
									if ($kq ['score_id'] > 0) {
										$this->db->saveHistoryData ( $kq_id, 'h_kq_instance_score', $uniqueID, 'internalAssessmentKeyQuestionUpdate', $assessment_id, $assessor_id, $action_key_question_json, 0, date ( 'Y-m-d H:i:s' ) );
									} else if (empty ( $kq ['score_id'] )) {
										$keyQuestionId = $this->db->get_last_insert_id ();
										$this->db->saveHistoryData ( $keyQuestionId, 'h_kq_instance_score', $uniqueID, 'internalAssessmentKeyQuestionInsert', $assessment_id, $assessor_id, $action_key_question_json, 0, date ( 'Y-m-d H:i:s' ) );
									}
									// end--> call function for save history for update and insert core qiestion score in history table on 03-03-2016 by Mohit Kumar
								}
							}
						} else if ($kq ['score_id'] > 0) {
							
							$diagnosticModel->deleteKeyQuestionScore ( $kq_id, $assessment_id, $assessor_id );
							
							if (OFFLINE_STATUS == TRUE) {
								// start--> call function for save history for delete key question score in history table on 21-03-2016 by Mohit Kumar
								$action_key_question_json = json_encode ( array (
										"key_question_instance_id" => $kq_id,
										"assessment_id" => $assessment_id,
										"assessor_id" => $assessor_id 
								) );
								$this->db->saveHistoryData ( $kq_id, 'h_kq_instance_score', $uniqueID, 'internalAssessmentKeyQuestionDelete', $assessment_id, $assessor_id, $action_key_question_json, 0, date ( 'Y-m-d H:i:s' ) );
								// end--> call function for save history for delete key qiestion score in history table on 03-03-2016 by Mohit Kumar
							}
						}
					}
					
					if ($kq_count == count ( $kq_ratings )) {
						
						$kq_res = diagnosticModel::calculateStatementResult ( $kq_ratings, $rSchemeId, 2, $kpaJs_ratings, $kpaSq_ratings );
						
						$kpa_ratings [] = $kq_res;
						
						$rating_id = $rScheme ['kpa'] [$kq_res] ['rating_id'];
						if (($kpa ['score_id'] > 0 && ! $diagnosticModel->updateKpaScore ( $kpa_id, $assessment_id, $assessor_id, $rating_id )) || (empty ( $kpa ['score_id'] ) && ! $diagnosticModel->insertKpaScore ( $kpa_id, $assessment_id, $assessor_id, $rating_id ))) {
							
							$success = false;
						} else {
							if (OFFLINE_STATUS == TRUE) {
								// start--> call function for save history for update and insert kpa score in history table on 21-03-2016 by Mohit Kumar
								$action_kpa_json = json_encode ( array (
										'd_rating_rating_id' => $rating_id,
										'kpa_instance_id' => $kpa_id,
										'assessment_id' => $assessment_id,
										'assessor_id' => $assessor_id 
								) );
								if ($kpa ['score_id'] > 0) {
									$this->db->saveHistoryData ( $kpa_id, 'h_kpa_instance_score', $uniqueID, 'internalAssessmentKpaUpdate', $assessment_id, $assessor_id, $action_kpa_json, 0, date ( 'Y-m-d H:i:s' ) );
								} else if (empty ( $kpa ['score_id'] )) {
									$kpaScoreId = $this->db->get_last_insert_id ();
									$this->db->saveHistoryData ( $kpaScoreId, 'h_kpa_instance_score', $uniqueID, 'internalAssessmentKpaInsert', $assessment_id, $assessor_id, $action_kpa_json, 0, date ( 'Y-m-d H:i:s' ) );
								}
								// end--> call function for save history for update and insert kpa score in history table on 03-03-2016 by Mohit Kumar
							}
						}
					} else if ($kpa ['score_id'] > 0) {
						
						$diagnosticModel->deleteKpaScore ( $kpa_id, $assessment_id, $assessor_id );
						if (OFFLINE_STATUS == TRUE) {
							// start--> call function for save history for delete key question score in history table on 21-03-2016 by Mohit Kumar
							$action_kpa_json = json_encode ( array (
									"kpa_instance_id" => $kpa_id,
									"assessment_id" => $assessment_id,
									"assessor_id" => $assessor_id 
							) );
							$this->db->saveHistoryData ( $kpa_id, 'h_kpa_instance_score', $uniqueID, 'internalAssessmentKpaDelete', $assessment_id, $assessor_id, $action_kpa_json, 0, date ( 'Y-m-d H:i:s' ) );
							// end--> call function for save history for delete key qiestion score in history table on 03-03-2016 by Mohit Kumar
						}
					}
					
					if ($assessment ['role'] == 4) {
						$isKNComplete = true;
						$akns = isset($_POST ['aknotes'])?$_POST ['aknotes']:array();
                                                //echo "<pre>";print_r($akns);die;
                                                //echo"b". $kpa_count;
						if (isset ( $akns ) && count ( $akns )) {
                                                        if($is_collaborative == 1) {
                                                           $isKNComplete = false;
                                                            if(isset($akns[$kpaIndex]) && $akns[$kpaIndex] == 1){
                                                                
                                                                    $isKNComplete = true;
                                                                    //break;
                                                            }
                                                        }else {
                                                            foreach ( $akns as $akn ) :
                                                                    if ($akn == 0)
                                                                            $isKNComplete = false;
                                                            endforeach
                                                            ;
                                                        }
						}
						if ($isKNComplete)
							$noOfComplete ++;
						else
							$noOfIncomplete ++;
                                                
                                                $kpaIndex++;
						
						if (in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] ))
							if (empty ( $_POST ['approveKeyNotes'] )) {
								$diagnosticModel->updateAssessorKeyNotesStatus ( $assessment_id, 0 );
								if (OFFLINE_STATUS == TRUE) {
									// start--> call function for save history for update assessor key status in d_assessment in history table on 21-03-2016 by Mohit Kumar
									$action_assessor_key_note_json = json_encode ( array (
											"assessment_id" => $assessment_id,
											"isAssessorKeyNotesApproved" => 0 
									) );
									$this->db->saveHistoryData ( $assessment_id, 'd_assessment', $uniqueID, 'internalAssessmentDAssessmentStatusUpdate', $assessment_id, 0, $action_assessor_key_note_json, 0, date ( 'Y-m-d H:i:s' ) );
									// end--> call function for save history for update assessor key status in d_assessment in history table on 03-03-2016 by Mohit Kumar
								}
							} else {
								$diagnosticModel->updateAssessorKeyNotesStatus ( $assessment_id, 1 );
								if (OFFLINE_STATUS == TRUE) {
									// start--> call function for save history for update assessor key status in d_assessment in history table on 21-03-2016 by Mohit Kumar
									$action_assessor_key_note_json = json_encode ( array (
											"assessment_id" => $assessment_id,
											"isAssessorKeyNotesApproved" => 1 
									) );
									$this->db->saveHistoryData ( $assessment_id, 'd_assessment', $uniqueID, 'internalAssessmentDAssessmentStatusUpdate', $assessment_id, 1, $action_assessor_key_note_json, 0, date ( 'Y-m-d H:i:s' ) );
									// end--> call function for save history for update assessor key status in d_assessment in history table on 03-03-2016 by Mohit Kumar
								}
							}
					}
				}
                                                       
				$success = true;
				$completedPerc = 0;
                                $total = $noOfIncomplete + $noOfComplete;
                                $avgPercntg = 0; 
                                
				//echo $noOfComplete;die;
				if ($total > 0 && $success) {
					if ($noOfIncomplete == 0 && ! empty ( $_POST ['submit'] )) {
						
						 $completedPerc = 100;
                                                 $isForSubmit = 1;
                                                 $aid = 0;
						 if($is_collaborative == 1 && $isLeadAssessor == 0){
                                                     $isForSubmit = 0;
                                                     $aid = 1;
                                                 }
                                                 if($isForSubmit)
                                                     $aid = $diagnosticModel->updateAssessmentPercentageAndStatus ( $assessment_id, $assessor_id, $completedPerc, 1 );
						if (!$aid ) {
							
							$success = false;
						} else {
							if (OFFLINE_STATUS == TRUE) {
								// start--> call function for save history for update assessor key status in d_assessment in history table on 21-03-2016 by Mohit Kumar
								$action_assessor_key_note_json = json_encode ( array (
										"assessment_id" => $assessment_id,
										"isAssessorKeyNotesApproved" => 1 
								) );
								$this->db->saveHistoryData ( $assessment_id, 'd_assessment', $uniqueID, 'internalAssessmentDAssessmentStatusUpdate', $assessment_id, 1, $action_assessor_key_note_json, 0, date ( 'Y-m-d H:i:s' ) );
								// end--> call function for save history for update assessor key status in d_assessment in history table on 03-03-2016 by Mohit Kumar
								$success = true;
                                                                
							}                                                        
                                                        if($is_collaborative){                                                            
                                                            $diagnosticModel->updateAssessmentPercentage ( $assessment_id, $assessor_id, $completedPerc,$is_collaborative,$external ,1,$isLeadAssessor);
                                                            if($isLeadAssessor)
                                                                $diagnosticModel->updateAssessmentPercentage ( $assessment_id, $assessor_id, $completedPerc,0,0 ,1,0);
                                                            
                                                            if($is_collaborative == 1){
                                                                $avgPercntg =   $this->calculatePercentage($assessment_id);
                                                            }
                                                            if( $avgPercntg)
                                                                $diagnosticModel->updateAvgAssessmentPercentage ( $assessment_id,$avgPercntg);
                                                        }
                                                        //echo strtotime($assessment['aqs_edate']) <= strtotime('tomorrow');
                                                       // if(strtotime($assessment['aqs_edate']) <= strtotime('tomorrow')) {
                                                             if($assessment ['role'] == 4){
                                                                $notifications = $assessmentModel->getNotificationUsers($assessment_id,1);
                                                                 //echo '<pre>'; print_r($notifications);die;
                                                                    if(!empty($notifications)) {
                                                                       $assessmentModel->createNotificationQueue($notifications,$assessment['aqs_edate']);
                                                                    }
                                                                }

                                                        //}
                                                         if($assessment ['role'] == 4){
                                                                $notifications = $assessmentModel->getNotificationUsers($assessment_id,2);
                                                               //  echo '<pre>'; print_r($notifications);die;
                                                                if(!empty($notifications)) {
                                                                   $assessmentModel->createNotificationQueue($notifications,$assessment['aqs_edate']);
                                                                }
                                                        }
                                                        
						}
					} else if ($assessment ["status"] == 1 && $noOfIncomplete > 0) {
						
						$success = false;
						
						// $this->apiResult["message"] = "Some fields are empty. Kindly ensure all keynotes are filled";
						$this->apiResult ["message"] = "Some fields are empty.";
						
						return false;
					} else {
						
                                                $completedPerc = round ( (100 * $noOfComplete) / $total, 2 );
                                                 
						if (! $diagnosticModel->updateAssessmentPercentage ( $assessment_id, $assessor_id, $completedPerc,$is_collaborative,$external,'','',$isLeadSave )) {
							
							$success = false;
						} else {
							if (OFFLINE_STATUS == TRUE) {
								// start--> call function for save history for update assessment percentage in d_assessment in history table on 21-03-2016 by Mohit Kumar
								$action_assessment_percentage_json = json_encode ( array (
										"percComplete" => $completedPerc,
										"assessment_id" => $assessment_id,
										"user_id" => $assessor_id 
								) );
								$this->db->saveHistoryData ( $assessment_id, 'd_assessment', $uniqueID, 'internalAssessmentPercentageUpdate', $assessment_id, $assessor_id, $action_assessment_percentage_json, 0, date ( 'Y-m-d H:i:s' ) );
								// end--> call function for save history for update assessor key status in d_assessment in history table on 03-03-2016 by Mohit Kumar
								$success = true;
							}
                                                        if($is_collaborative == 1){
                                                               $avgPercntg =   $this->calculatePercentage($assessment_id);
                                                            }
                                                        
                                                        if($is_collaborative && $avgPercntg){
                                                           // echo $avgPercntg;
                                                            $diagnosticModel->updateAvgAssessmentPercentage ( $assessment_id,$avgPercntg);
                                                        }
						}
					}
				} else {
					
					$success = false;
				}
				$leadAssessorStatus = 0;
                                if($isLeadAssessor && empty($isLeadAssessorKpa)){
                                    $leadAssessorStatus = 1;
                                }
				if ($success && $this->db->commit ()) {
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["completedPerc"] = $completedPerc;
					$this->apiResult ["completedStatus"] = $isRevCompleteNtSubmitted;
					$this->apiResult ["leadAssessorStatus"] = $leadAssessorStatus;
					
					$this->apiResult ["submit"] = $assessment ["status"];
					
					$this->apiResult ["message"] = "Successfully saved"; 
                                        if($is_collaborative == 1 && (in_array(1,$this->user['role_ids']) || in_array(2,$this->user['role_ids'])) ){
                                       
                                            $internalAssessorId =  $diagnosticModel->getInternalAssessor ($assessment_id); 
                                             //print_r($internalAssessorId);die;
                                            $diagnosticModel->deleteInternalJudgementStatementScore ($assessment_id,$internalAssessorId);                                    
                                        }
                                        if($assessment['iscollebrative']) {
                                               $submit = isset($_POST ['submit'])?$_POST ['submit']:0;
                                               if($isLeadAssessor == 1 && $submit == 1 && !in_array(1,$this->user['role_ids']) && !in_array(2,$this->user['role_ids']))
                                                    $this->saveInternalAssessorRatings($assessment_id,$completedPerc,$submit,$lang_id);
                                               else if((in_array(1,$this->user['role_ids']) || in_array(2,$this->user['role_ids']))){
                                                   $this->saveInternalAssessorRatings($assessment_id,$completedPerc,$submit,$lang_id);
                                               }
                                        }
				} else {
					
					$this->db->rollback ();
					
					$this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
				}
			}
		} else {
			
			$this->apiResult ["message"] = "Wrong review id or reviewer id\n";
		}
	}
	function loadAQSVersionDataAction() {
		if (empty ( $_POST ['assessment_type_id'] )) {
			
			$this->apiResult ["message"] = "Review type id cannot be empty\n";
		} else if (empty ( $_POST ['aqsversion'] )) {
			
			$this->apiResult ["message"] = ($_POST ['assessment_type_id'] == 1 ? "" : "Group ") . "Review id cannot be empty\n";
		} else if (empty ( $_POST ['aqs'] ['terms_agree'] ) && $_POST ['aqs'] ['terms_agree'] != 1) {
			
			$this->apiResult ["message"] = "Please Accept terms and conditions in order to proceed.";
		} else {
			
			$assmntId_or_grpAssmntId = empty ( $_POST ['aqsversion'] ) ? 0 : $_POST ['aqsversion'];
			
			$assessment_type_id = empty ( $_POST ['load_assessment_type_id'] ) ? 0 : $_POST ['load_assessment_type_id'];
			
			$aqsDataModel = new aqsDataModel ();
			
			// aqs versions of past-filled Data
			
			$aqs = $aqsDataModel->getAqsData ( $assmntId_or_grpAssmntId, $assessment_type_id );
                        
                        //echo "<pre>";print_r($aqs);
			
			$additional_data = $aqsDataModel->getAqsAdditionalData ( $aqs ['id'], 'd_aqs_additional_questions' );
			$aqsAdditionalTeam = $aqsDataModel->getAqsAdditionalRefTeam ( $aqs ['id'] );
			//echo "<pre>";print_r($additional_data);
                        //echo "<pre>";print_r($aqsAdditionalTeam);
			$this->apiResult ["aqs"] = $aqs;
			$this->apiResult ["additional_data"] = $additional_data;
			$this->apiResult ["aqsAdditionalTeam"] = $aqsAdditionalTeam;
			
			$team = isset ( $aqs ['id'] ) ? $aqsDataModel->getAQSTeam ( $aqs ['id'] ) : array (
					"school" => array (),
					"adhyayan" => array () 
			);
			
			$this->apiResult ["school_team"] = $team ['school'];
			
			//$this->apiResult ["adhyayan_team"] = $team ['adhyayan'];
			
			$this->apiResult ["message"] = "Success";
			
			$this->apiResult ["status"] = "1";
		}
	}
	function savePostReviewAction() {				
		$diagnosticModel = new diagnosticModel();
                $lang_id=isset($_POST ['lang_id'])?$_POST ['lang_id']:DEFAULT_LANGUAGE;
		$assessment = $diagnosticModel->getAssessmentByRole ( $_POST ['assessment_id'], 4 ,$lang_id);
		if(!(in_array("take_external_assessment", $this->user['capabilities']) || in_array("edit_all_submitted_assessments", $this->user['capabilities'])))
			$this->apiResult ["message"] = "You are not authorized to perform this task\n";
		elseif (empty ( $_POST ['assessment_id'] ))			
			$this->apiResult ["message"] = "Review id cannot be empty\n";	
                elseif (!empty ( $_POST ['student_count'] ) && intval($_POST ['student_count'])<1)			
			$this->apiResult ["message"] = "Student count must be between 1 and 99999\n";
		elseif(empty($assessment))
			$this->apiResult ["message"] = "Review is not filled\n";					
		else if ($assessment ['aqs_status'] != 1)	
			$this->apiResult ["message"] = "You are not authorized to fill post-review before School profile\n";								
		else if ($assessment ["status"] == 0)		
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		else if($assessment ["user_id"] != $this->user ['user_id'] && !in_array("edit_all_submitted_assessments", $this->user['capabilities']))
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";				
		else {
			if ($postRevData = $diagnosticModel->getPostReviewData($_POST ['assessment_id'])) {				
				if($postRevData['status']==1 && !(in_array("edit_all_submitted_assessments", $this->user['capabilities']))){//admin only can edit the review after submission
					$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
					exit;
				}
				elseif($postRevData['status']!=1 && in_array("edit_all_submitted_assessments", $this->user['capabilities'])){//external reviewer only can fill the review
					$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
					exit;
				}
			}			
			$failed = 0;
				
			$noOfComplete = 0;
				
			$noOfIncomplete = 0;
				
			$total = 0;
				
			$completedPerc = 0;
				
			$assessment_id = 0;
				
			//$teachingStaff = $_POST ['number_teaching_staff'];
				
			//print_r($teachingStaff);
				
			/*$teachingStaff = array_filter ( $teachingStaff, function ($var) {
				if ($var > 0)
					return $var;
			} );*/
                        
                         //print_r($teachingStaff);
                        
                        
                        $average_students_class = isset($_POST ['average_students_class'])?$_POST ['average_students_class']:array();
				
			 
				
			$average_students_class = array_filter ( $average_students_class, function ($var) {
				if ($var > 0)
					return $var;
			} );
                        //print_r($average_students_class);
                        $average_teachers_class = isset($_POST ['average_teachers_class'])?$_POST ['average_teachers_class']:array();
				
			// print_r($average_teachers_class);
				
			$average_teachers_class = array_filter ( $average_teachers_class, function ($var) {
				if ($var > 0)
					return $var;
			} );
                        
                        foreach($_POST['kpa'] as $kpaKey=>$kpaval){
                            if(empty($kpaval) || empty($_POST['kq'][$kpaKey]) || empty($_POST['cq'][$kpaKey]) ||empty(trim($_POST['action_planning'][$kpaKey]))){
                                $this->apiResult["message"] = "Please fill all the fields in Action planning area chosen by school.";
                                return;
                            }
                             
                        }
                        
                        if(count($average_teachers_class)!=count($average_students_class)){
                            $this->apiResult["message"] ="Please fill Avg. students and teachers in single class properly";
                            return;
                        }
					
				// print_r($teachingStaff);die;
					
				$prep_non_teaching = ! empty ( $_POST ['number_non_teaching_staff_prep'] ) ? $_POST ['number_non_teaching_staff_prep'] : '';
					
				$rest_non_teaching = ! empty ( $_POST ['number_non_teaching_staff_rest'] ) ? $_POST ['number_non_teaching_staff_rest'] : '';
					
				$incomplete = 0;
					
				$total = count ( $_POST );
					
				// $incomplete=count(empty($_POST));
					
				foreach ( $_POST as $field => $val )			
				{	
                                        if($field!="comments"){
                                            
					if (! is_array ( $val ) && strlen ( trim($val) ) < 1)			
					{						
						$incomplete ++;
					}			
					elseif (is_array ( $val ))			
					{							
						foreach ( $val as $key => $v )			
						{
                                                    if(is_array($v)){
                                                        foreach ( $v as $sub_key => $sub_val )
                                                            if(strlen(trim($sub_val))<1)
                                                                $incomplete++;
                                                    }
                                                    elseif (strlen ( trim($v)) < 1 )
								$incomplete ++;                                                                                                                       
							//$total ++;
						}
					}
                                        
                                        }
				}					
				if (! empty ( $_POST ['decision_maker'] ) && $_POST ['decision_maker'] != 4 && empty ( $_POST ['decision_maker_other'] ))			
					$total -- && $incomplete --;
						
//                                if (! empty ( $_POST ['student_body_activity'] ) && $_POST ['student_body_activity'] != 2)			
//				{			
//					($total = $total - 2);			
//					($incomplete = $incomplete - 2);
//				}
						
						
					$completedPerc = $incomplete > 0 ? number_format ( (($total - $incomplete) * 100) / $total, 2 ) : '100';
					//echo $total,' ',$incomplete;	
					//$student_body_school_level = ! empty ( $_POST ['student_body_school_level'] ) ? implode ( ',', $_POST ['student_body_school_level'] ) : '';
						
					$postReviewId = 0; // $number_teaching_staff
						
					$currentData = $diagnosticModel->getPostReviewData ( $_POST ['assessment_id'] );
						
					// print_r($currentData);die;
						
					if (! empty ( $currentData ['assessment_id'] )) {
			
						$assessment_id = $currentData ['assessment_id'];
			
						$postReviewId = $currentData ['post_review_id'];
					}
						
					$status = ! empty ( $_POST ['submit'] ) ? 1 : ($currentData['status']==1?1:0);
                                        if($status==1 && $incomplete>0){
                                            $this->apiResult["message"]="Please fill all the fields";
                                            return;
                                        }
					$data = array (
							"assessment_id" => $_POST ['assessment_id'],
							"decision_maker" => $_POST ['decision_maker'],
							"decision_maker_other" => trim($_POST ['decision_maker_other']),
								
							"management_engagement" => $_POST ['management_engagement'],
								
							"action_management_decision" => $_POST ['action_management_decision'],
							"principal_tenure" => $_POST ['principal_tenure'],
							"principal_vision" => $_POST ['principal_vision'],								
							"principal_involvement" => $_POST ['principal_involvement'],
							"principal_openness" => $_POST ['principal_openness'],								
							"middle_leaders" => ! empty ( $_POST ['middle_leaders_select'] ) ? $_POST ['middle_leaders_select'] : '',
							"parent_teacher_association" => $_POST ['parent_teacher_association'],								
							"student_body_activity" => $_POST ['student_body_activity'],
							//"student_body_school_level" => $student_body_school_level,								
							"alumni_association"=>$_POST ['alumni_association'],								
							"average_staff_tenure" => $_POST ['average_staff_tenure'],								
							/*"average_number_students_class" => $_POST ['average_number_students_class'],*/
							"ratio_students_class_size" => $_POST ['ratio_students_class_size'],								
							"number_teaching_staff" => '',
							"number_non_teaching_staff_prep" => $prep_non_teaching,
							"number_non_teaching_staff_rest" => $rest_non_teaching,
							"teaching_staff_comment" => '',
							"comments"=>$_POST ['comments'],
                                                        "student_count"=>trim($_POST ['student_count']),
							"rte" => isset ( $_POST ['rte'] ) ? $_POST ['rte'] : '',
							"status" => $status,
							"percComplete" => $completedPerc,
							"create_date" => date ( 'Y-m-d' )
					);
					// print_r($data);die;l2
						
					$this->db->start_transaction ();
						
					if ( ($assessment_id > 0 && $diagnosticModel->updatePostReviewData ( $data, $assessment_id )) || ($assessment_id == 0 && $postReviewId = $diagnosticModel->insertPostReviewData ( $data ) ))
                                        {
                                            if (! $diagnosticModel->removePostReviewTeachingStaff ( $postReviewId ))
								
							$failed = 1;
			
							/*foreach ( $teachingStaff as $key => $val )
			
							{
									
								if (! $diagnosticModel->insertPostReviewTeachingStaff ( array (
										"staff_id" => $val,
										"school_level_id" => $key,
										"post_review_id" => $postReviewId
								) ))
			
									$failed = 2;
							}*/
                                                        
                                                if (! $diagnosticModel->removePostReviewStudentTeacherClass ( $postReviewId ))
								
							$failed++;
                                                
                                                foreach ( $average_students_class as $key => $val )
			
							{
								//$tid=(isset($average_teachers_class[$key]) && $average_teachers_class[$key]>0)?$average_teachers_class[$key]:0	;
								if (! $diagnosticModel->insertPostReviewStudentTeacherClass ( array (
										"student_id" => $val,
                                                                                "teacher_id" => $average_teachers_class[$key] ,
										"school_level_id" => $key,
										"post_review_id" => $postReviewId
								) ))
			
									$failed++;
							}
                                                
                                                    //action planning kpa-kq-cq-text
                                                    if (! $diagnosticModel->removePostReviewActionPlanning ( $postReviewId ))								
							$failed++;
                                                     if (! $diagnosticModel->removePostReviewActionPlanningCQ ( $postReviewId ))								
							$failed++; 
                                                    //add action planning data
                                                    foreach($_POST['kpa'] as $kpaKey=>$kpaval){
                                                        if(! $diagnosticModel->insertPostReviewActionPlanning(array(
                                                            "post_review_id"=>$postReviewId,
                                                            "kpa_instance_id"=>$kpaval,
                                                            "key_question_instance_id"=>$_POST['kq'][$kpaKey],
                                                            "action_planning"=>trim($_POST['action_planning'][$kpaKey])
                                                        )))
                                                                $failed++;
                                                        foreach($_POST['cq'][$kpaKey] as $key=>$cqVal){
                                                                if(! $diagnosticModel->insertPostReviewActionPlanningCQ(array(
                                                                "post_review_id"=>$postReviewId,
                                                                "core_question_instance_id"=>$cqVal,
                                                                "key_question_instance_id"=>$_POST['kq'][$kpaKey]      
                                                            )))
                                                                 $failed++;
                                                        }
                                                        
                                                    }
							if ($failed > 0)
			
							{
									
								$this->apiResult ["message"] = "Error";
									
								$this->db->rollback ();
							}
			
							else
			
							{
									
								$this->apiResult ["status"] = 1;
									
								$this->apiResult ["message"] = "Successfully saved";
									
								$this->db->commit ();
							}
                                        }			
					else {
			
						$this->db->rollback ();
			
						$this->apiResult ["message"] = "Errorr occurred";
					}
			
			
		}
	}
	function saveAqsFormAction() {
		$noOfComplete = 0;
		
		$noOfIncomplete = 0;
		
		$total = 0;
		
		$completedPerc = 0;
		
		$diagnosticModel = new diagnosticModel ();
		
		$isAdmin = in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] );
		
		if (empty ( $_POST ['assessment_type_id'] )) {
			
			$this->apiResult ["message"] = "Review type id cannot be empty\n";
		} else if (empty ( $_POST ['assmntId_or_grpAssmntId'] )) {
			
			$this->apiResult ["message"] = ($_POST ['assessment_type_id'] == 1 ? "" : "Group ") . "Assessment id cannot be empty\n";
		} else if (empty ( $_POST ['aqs'] ['terms_agree'] ) && $_POST ['aqs'] ['terms_agree'] != 1) {
			
			$this->apiResult ["message"] = "Please Accept terms and conditions in order to proceed.";
		} else if (empty ( $_POST ['aqs'] ) || ! is_array ( $_POST ['aqs'] ) || empty ( $_POST ['schoolTeam'] )) {
			
			$this->apiResult ["message"] = "Data missing\n";
		} else if ($assessment = (($_POST ['assessment_type_id'] == 1 || $_POST ['assessment_type_id']==5) ? $diagnosticModel->getAssessmentById ( $_POST ['assmntId_or_grpAssmntId'] ) : $diagnosticModel->getGroupAssessmentByGAId ( $_POST ['assmntId_or_grpAssmntId'] ))) {
			
			$isSchoolAdmin = in_array ( "view_own_institute_assessment", $this->user ['capabilities'] ) && $assessment ['client_id'] == $this->user ['client_id'] ? 1 : 0;
			
			$assignedtoHim = 0;
			
			if ($_POST ['assessment_type_id'] == 1 || $_POST ['assessment_type_id'] == 5) {
				
				$assignedtoHim = $assessment ['userIdByRole'] [3] == $this->user ['user_id'] ? 1 : 0;
			} else {
				
				$isSchoolAdmin = $isSchoolAdmin && in_array ( 6, $this->user ['role_ids'] ) ? 1 : 0;
				
				$assignedtoHim = $assessment ['admin_user_id'] == $this->user ['user_id'] ? 1 : 0;
			}
			
			if (($_POST ['assessment_type_id'] == 1 || $_POST ['assessment_type_id'] == 5) && $assessment ['report_published'] == 1) {
				
				$this->apiResult ["message"] = "You can't update data after publishing reports\n";
			} else if ($_POST ['assessment_type_id'] > 1 && $_POST ['assessment_type_id'] !=5 && $assessment ['assessmentAssigned'] == 0) {
				
				$this->apiResult ["message"] = "You can't save AQS before assigning assessors\n";
			} else if (($assessment ['aqs_status'] == 0 && ($assignedtoHim || $isSchoolAdmin || 

			(in_array ( "view_own_network_assessment", $this->user ['capabilities'] ) && $assessment ['network_id'] == $this->user ['network_id'] && $this->user ['network_id'] > 0))

			) || ($assessment ['aqs_status'] == 1 && $isAdmin)) 

			{
				
				$aqsDataModel = new aqsDataModel ();
				
				$currentData = $aqsDataModel->getAqsData ( $_POST ['assmntId_or_grpAssmntId'], $_POST ['assessment_type_id'] );
				//print_r($currentData);
				$submit = ! empty ( $_POST ['submit'] ) || $assessment ['aqs_status'] == 1 ? 1 : 0;
				
				$schoolLevels = $aqsDataModel->getSchoolLevelList ();
				
				$isSelfReview = ! empty ( $assessment ['subAssessmentType'] ) && $assessment ['subAssessmentType'] == 1 ? 1 : 0;
				$isCollegeReview=$_POST ['assessment_type_id'] == 5?1:0;
                                
				if (! $isSelfReview && !$isCollegeReview &&  (empty ( $_POST ['aqs'] ) || ! is_array ( $_POST ['aqs'] ) || empty ( $_POST ['other'] ) || ! is_array ( $_POST ['other'] ) ||  empty ( $_POST ['schoolTeam'] ))) {
					
					$this->apiResult ["message"] = "Data missing\n";
					
					return;
				} 

				else if (($isSelfReview || $isCollegeReview) && (empty ( $_POST ['aqs'] ) || ! is_array ( $_POST ['aqs'] ) || empty ( $_POST ['schoolTeam'] ))) {
					
					$this->apiResult ["message"] = "Data missing\n";
					
					return;
				}
				
				// $aqsValidationRes = $isSelfReview==1? $aqsDataModel->aqsFormValidation($_POST['aqs'],array(),$schoolLevels,$submit,$isSelfReview) : $aqsDataModel->aqsFormValidation($_POST['aqs'],$_POST['other'],$schoolLevels,$submit,$isSelfReview);
				
				$other = empty ( $_POST ['other'] ) ? array () : $_POST ['other'];
				// for ashoka changemaker only
				$additional = empty ( $_POST ['additional'] ) ? array () : $_POST ['additional'];
				$schoolsTypeIds = empty ( $_POST ['aqs']['school_type_id'] ) ? array () : $_POST['aqs']['school_type_id'];
				$additionalRefTeamValidation = empty ( $_POST ['additional_ref'] ) ? array () : $aqsDataModel->aqsAdditionalRefTeamValidation ( $_POST ['additional_ref'] );
				//print_r($schoolsTypeIds);
                                $_POST ['aqs']['school_type_id'] = implode(",",$schoolsTypeIds);
				$aqsValidationRes = $aqsDataModel->aqsFormValidation ( $_POST ['aqs'], $other, $additional, $schoolLevels, $submit, $isSelfReview,$schoolsTypeIds,$isCollegeReview );
				
                               // echo "<pre>";  print_r($_POST ['schoolTeam']);
				$schoolTeamValidation = $aqsDataModel->aqsTeamValidation ( $_POST ['schoolTeam'], 1, $submit,$isCollegeReview);
				
				$errors = array_merge ( $aqsValidationRes ['errors'], $schoolTeamValidation ['errors'] );
				// print_r($additionalRefTeamValidation);
				if (! empty ( $additionalRefTeamValidation ))
					$errors = array_merge ( $errors, $additionalRefTeamValidation ['errors'] );
				
				$adhyayanTeamValidation = false;
				
//				if ($isAdmin) {
//					
//					$adhyayanTeamValidation = $aqsDataModel->aqsTeamValidation ( $_POST ['adhyayanTeam'], 0, $submit );
//					
//					$errors = array_merge ( $errors, $adhyayanTeamValidation ['errors'] );
//				}
				
				if (count ( $errors )) {
					
					$this->apiResult ["message"] = "Data is either incorrect or blank.\n";
					
					$this->apiResult ["errors"] = $errors;
				} else {
					
					$aqsId = 0;
					
					$failed = 0;
					
					if ($submit)
						
						$aqsValidationRes ['values'] ['status'] = 1;
					
					$princ = $this->userModel->getPrincipal ( $assessment ['client_id'] );
					
					if (! empty ( $princ ['email'] ))
						
						$aqsValidationRes ['values'] ['principal_email'] = $princ ['email'];
						
						// calculate percentage of filled fields
                                        $mandatoryFields = array("school_name",'principal_name','principal_phone_no','school_address','board_id',
                                            'school_type_id','aqs_school_minority','aqs_school_recognised','school_region_id','classes_from','classes_to',
                                            'no_of_students','num_class_rooms','medium_instruction','student_type_id','annual_fee');
					
					foreach ( $_POST as $key => $val ) 

					{
						
						if (is_array ( $val )) 

						{
							
							switch ($key) 

							{
								
								case 'aqs' :
                                                                   // echo "<pre>";print_r($val);die;
									foreach ( $val as $k => $v ) 

									{
										
										if (($k == 'referrer_text' && $_POST ['aqs'] ['referrer_id'] != 7) || ($k == 'distance_main_building' && $_POST ['aqs'] ['no_of_buildings'] == 1) || $k == 'contract_file_name' || $k == 'school_website'  || (isset($_POST['aqs']['accomodation_arrangement_for_adhyayan']) && $_POST['aqs']['accomodation_arrangement_for_adhyayan']== 1 && $k == 'hotel_name' || $k == 'hotel_school_distance' || $k == 'hotel_airport_distance' || $k == 'hotel_station_distance') || (isset($_POST['aqs']['travel_arrangement_for_adhyayan']) && $_POST['aqs']['travel_arrangement_for_adhyayan']== 1 && $k == 'airport_name' || $k == 'airport_distance'|| $k == 'rail_station_name'|| $k == 'rail_station_distance') || $k == 'aqs_school_registration_num'  ) 

										{
											$noOfComplete ++;
											
											$total ++;
											
											continue;
										}
										
										if (empty ( $v ))
											
											$noOfIncomplete ++;
										
										else
											$noOfComplete ++;
                                                                                
                                                                                if($k == 'aqs_school_gst' && $v == 1 ) {
                                                                                    $noOfComplete--;
                                                                                    $total--;
                                                                                }
										
										$total ++;
									}
									
									break;
								
								case 'other' :
									foreach ( $val as $k => $v ) 

									{
										
										// print_r($v);
										
										if (is_array ( $v )) 

										{
											
											foreach ( $v as $key => $value ) 

											{
												
												if (is_array ( $value )) 

												{
													
													foreach ( $value as $index => $obj ) 

													{
														
														if (empty ( $obj ))
															
															$noOfIncomplete ++;
														
														else
															
															$noOfComplete ++;
														
														$total ++;
													}
												} 

												else 

												{
													
													if (empty ( $value ))
														
														$noOfIncomplete ++;
													
													else
														
														$noOfComplete ++;
													
													$total ++;
												}
											}
										} 

										else 

										{
											
											if (empty ( $v ))
												
												$noOfIncomplete ++;
											
											else
												
												$noOfComplete ++;
											
											$total ++;
										}
									}
									
									break;
								
								case 'schoolTeam' :
									foreach ( $val as $k => $v ) 

									{
										//print_r($k);
                                                                                //print_r($v);
										foreach ( $v as $key => $value ) 

										{
                                                                                        $check_blank="Yes";
											if($k=="mobile" && $val['designation'][$key]=="7"){
                                                                                        $check_blank="No";    
                                                                                        }
											if (empty ( $value ) && $check_blank=="Yes")
												
												$noOfIncomplete ++;
											
											else
												
												$noOfComplete ++;
											
											$total ++;
										}
									}
									
									break;
							}
						} 

						else {
							
							if (empty ( $val ) && $key != 'aqsversion')
								
								$noOfIncomplete ++;
							
							else
								
								$noOfComplete ++;
							
							$total ++;
						}
					}
					$completedPerc = round ( (100 * $noOfComplete) / $total, 2 );
					
					/*
					 * echo "complete: ".$noOfComplete;
					 *
					 *
					 *
					 * echo "incomplete: ".$noOfIncomplete;
					 *
					 *
					 *
					 * echo "total: ".$total;
					 *
					 *
					 *
					 * echo "perc: ".$completedPerc;
					 */
					
					$aqsValidationRes ['values'] ['percComplete'] = $completedPerc;
                                        //echo "<pre>";print_r($aqsValidationRes ['values']);
					
					if (OFFLINE_STATUS == TRUE) {
						// start---> call function for creating unique id for profile details for assessment on 11-03-2016 by Mohit Kumar
						$uniqueID = $this->db->createUniqueID ( 'assessmentAQSData' );
						// end---> call function for creating unique id for profile details for assessment on 11-03-2016 by Mohit Kumar
					}
					
					$this->db->start_transaction ();
                                        
                                        if(!empty($aqsValidationRes ['values']['principal_phone_no']) && !empty($_POST['aqs']['pr_country_code'])) {
                                                    
                                                   $aqsValidationRes ['values']['principal_phone_no'] = "(+".$_POST['aqs']['pr_country_code'].")".$aqsValidationRes ['values']['principal_phone_no'];
                                        }
                                        if(!empty($aqsValidationRes ['values']['coordinator_phone_number']) && !empty($_POST['aqs']['cr_country_code'])) {
                                                    
                                                   $aqsValidationRes ['values']['coordinator_phone_number'] = "(+".$_POST['aqs']['cr_country_code'].")".$aqsValidationRes ['values']['coordinator_phone_number'];
                                        }
                                        if(!empty($aqsValidationRes ['values']['accountant_phone_no']) && !empty($_POST['aqs']['ac_country_code'])) {
                                                    
                                                  $aqsValidationRes ['values']['accountant_phone_no'] = "(+".$_POST['aqs']['ac_country_code'].")".$aqsValidationRes ['values']['accountant_phone_no'];
                                        }
					
					if (isset ( $currentData ['id'] )) {
						
                                               // echo "<pre>";print_r($aqsValidationRes ['values']  );
                                                
                                                $aqsValidationRes ['values']['is_uploaded'] = 0;
						if ($aqsDataModel->updateAqsData ( $currentData ['id'], $aqsValidationRes ['values'] )) {
							
                                                        $aqsId = $currentData ['id'];
                                                        $aqs_school_type_ids = isset($_POST['aqs']['school_type_id'])?($_POST['aqs']['school_type_id']):"";
                                                        if(!empty($aqs_school_type_ids)) {
                                                            $aqs_school_type_ids = explode(",",$aqs_school_type_ids);
                                                        }
                                                           // if(count($aqs_school_type_ids)){
                                                                $aqsDataModel->insertSchoolType($assessment['assessment_id'],$aqs_school_type_ids);
                                                            //}
                                                        //}
							if (OFFLINE_STATUS == TRUE) {
								// start---> save the history for update aqs data into d_aqs_data table on 11-03-2016 By Mohit Kumar
								$action_assessment_aqs_json = json_encode ( $aqsValidationRes ['values'] );
								if (! $this->db->saveHistoryData ( $aqsId, 'd_aqs_data', $uniqueID, 'assessmentAQSDataUpdate', $aqsId, $aqsValidationRes ['values'] ['principal_email'], $action_assessment_aqs_json, 0, date ( 'Y-m-d H:i:s' ) )) {
									$failed = 79;
								}
								// end---> save the history for update aqs data into d_aqs_data table on 11-03-2016 By Mohit Kumar
							}
						}
                                                if(!$diagnosticModel->updategrpAssessment_AQS($_POST ['assmntId_or_grpAssmntId'],$aqsId)){
                                                    $failed = 1002;
                                                }
					} else {
						
						$data = $aqsValidationRes ['values'];
						
						$aqsId = $aqsDataModel->insertAqsData ( $data );
						if (OFFLINE_STATUS == TRUE) {
							// start---> save the history for update aqs data into d_aqs_data table on 11-03-2016 By Mohit Kumar
							$action_assessment_aqs_json = json_encode ( $data );
							if (! $this->db->saveHistoryData ( $aqsId, 'd_aqs_data', $uniqueID, 'assessmentAQSDataInsert', $aqsId, $aqsValidationRes ['values'] ['principal_email'], $action_assessment_aqs_json, 0, date ( 'Y-m-d H:i:s' ) )) {
								$failed = 80;
							}
							// end---> save the history for update aqs data into d_aqs_data table on 11-03-2016 By Mohit Kumar
						}
						
						$aqsId = $diagnosticModel->updateAqsDataIdInAssessment ( $_POST ['assmntId_or_grpAssmntId'], $_POST ['assessment_type_id'], $aqsId ) ? $aqsId : 0;
						if ($aqsId > 0 && OFFLINE_STATUS == TRUE) {
							// start---> save the history for update assessment data into d_assessment table on 11-03-2016 By Mohit Kumar
							$action_assessment_json = json_encode ( array (
									"aqsdata_id" => $aqsId,
									'assmntId_or_grpAssmntId' => $_POST ['assmntId_or_grpAssmntId'],
									'assessment_type_id' => $_POST ['assessment_type_id'] 
							) );
							if (! $this->db->saveHistoryData ( $aqsId, 'd_assessment', $uniqueID, 'assessmentAQSDataAssessmentUpdate', $_POST ['assmntId_or_grpAssmntId'], $aqsId, $action_assessment_json, 0, date ( 'Y-m-d H:i:s' ) )) {
								$failed = 81;
							}
							// end---> save the history for update assessment data into d_assessment table on 11-03-2016 By Mohit Kumar
						}
                                                
                                                if(!$diagnosticModel->updategrpAssessment_AQS($_POST ['assmntId_or_grpAssmntId'],$aqsId)){
                                                    $failed = 1002;
                                                }
                                                
					}
					
					if (! empty ( $aqsValidationRes ['values'] ['principal_name'] ) && $aqsValidationRes ['values'] ['principal_name'] != $princ ['name'] && ! $this->userModel->updateUser ( $princ ['user_id'], $aqsValidationRes ['values'] ['principal_name'] )) {
						
						$failed = 9;
					} else {
						if (OFFLINE_STATUS == TRUE) {
							// start---> save the history for update user data into d_user table on 11-03-2016 By Mohit Kumar
							$action_user_json = json_encode ( array (
									"principal_name" => $aqsValidationRes ['values'] ['principal_name'],
									'user_id' => $princ ['user_id'] 
							) );
							if (! $this->db->saveHistoryData ( $princ ['user_id'], 'd_user', $uniqueID, 'assessmentAQSDataUserUpdate', $princ ['user_id'], $aqsValidationRes ['values'] ['principal_email'], $action_user_json, 0, date ( 'Y-m-d H:i:s' ) )) {
								$failed = 82;
							}
							// end---> save the history for update user data into d_user table on 11-03-2016 By Mohit Kumar
						}
					}
					
					$clientModel = new clientModel ();
					
					if (! empty ( $aqsValidationRes ['values'] ['school_name'] ) && $aqsValidationRes ['values'] ['school_name'] != $assessment ['client_name'] && ! $clientModel->updateClientName ( $assessment ['client_id'], $aqsValidationRes ['values'] ['school_name'] )) {
						
						$failed = 10;
					} else {
						if (OFFLINE_STATUS == TRUE) {
							// start---> save the history for update school data into d_client table on 11-03-2016 By Mohit Kumar
							$action_school_json = json_encode ( array (
									"school_name" => $aqsValidationRes ['values'] ['school_name'],
									'client_id' => $assessment ['client_id'] 
							) );
							if (! $this->db->saveHistoryData ( $assessment ['client_id'], 'd_client', $uniqueID, 'assessmentAQSDataSchoolUpdate', $assessment ['client_id'], $aqsValidationRes ['values'] ['principal_email'], $action_school_json, 0, date ( 'Y-m-d H:i:s' ) )) {
								$failed = 83;
							}
							// end---> save the history for update school data into d_client table on 11-03-2016 By Mohit Kumar
						}
					}
					
					if (! $aqsDataModel->removeItSupport ( $aqsId )) {
						
						$failed = 1;
					} else {
						if (OFFLINE_STATUS == TRUE) {
							// start---> save the history for remove IT Support data into h_aqsdata_itsupport table on 11-03-2016 By Mohit Kumar
							$action_it_support_json = json_encode ( array (
									"aqs_id" => $aqsId 
							) );
							if (! $this->db->saveHistoryData ( $aqsId, 'h_aqsdata_itsupport', $uniqueID, 'assessmentAQSDataITSupportRemove', $aqsId, $aqsId, $action_it_support_json, 0, date ( 'Y-m-d H:i:s' ) )) {
								$failed = 84;
							}
							// end---> save the history for remove IT Support data into h_aqsdata_itsupport table on 11-03-2016 By Mohit Kumar
						}
					}
					
					foreach ( $aqsValidationRes ['otherValues'] ['it_support'] as $sid )
						
						if (! $aqsDataModel->addItSupport ( $aqsId, $sid )) {
							
							$failed = 2;
						} else {
							if (OFFLINE_STATUS == TRUE) {
								// get the last insert id of h_aqsdata_itsupport live server on 11-03-2016 by Mohit Kumar
								$itSupportID = $this->db->get_last_insert_id ();
								// start---> save the history for add IT Support data into h_aqsdata_itsupport table on 11-03-2016 By Mohit Kumar
								$action_it_support_json = json_encode ( array (
										'aqs_id' => $aqsId,
										'itsupport_id' => $sid 
								) );
								if (! $this->db->saveHistoryData ( $itSupportID, 'h_aqsdata_itsupport', $uniqueID, 'assessmentAQSDataITSupportAdd', $itSupportID, $aqsId, $action_it_support_json, 0, date ( 'Y-m-d H:i:s' ) )) {
									$failed = 85;
								}
								// end---> save the history for add IT Support data into h_aqsdata_itsupport table on 11-03-2016 By Mohit Kumar
							}
						}
					
					if (! $aqsDataModel->removeSchoolTiming ( $aqsId )) {
						
						$failed = 3;
					} else {
						if (OFFLINE_STATUS == TRUE) {
							// start---> save the history for remove School Timing data into h_aqs_school_level table on 11-03-2016 By Mohit Kumar
							$action_school_timing_json = json_encode ( array (
									"aqs_id" => $aqsId 
							) );
							if (! $this->db->saveHistoryData ( $aqsId, 'h_aqs_school_level', $uniqueID, 'assessmentAQSDataSchoolTimingRemove', $aqsId, $aqsId, $action_school_timing_json, 0, date ( 'Y-m-d H:i:s' ) )) {
								$failed = 86;
							}
							// end---> save the history for remove School Timing data into h_aqs_school_level table on 11-03-2016 By Mohit Kumar
						}
					}
					foreach ( $aqsValidationRes ['otherValues'] ['timing'] as $tm )
						
						if (! $aqsDataModel->addSchoolTiming ( $aqsId, $tm ['school_level_id'], $tm ['start_time'], $tm ['end_time'] )) {
							
							$failed = 4;
						} else {
							if (OFFLINE_STATUS == TRUE) {
								// get the last insert id of h_aqs_school_level live server on 11-03-2016 by Mohit Kumar
								$schoolTimingID = $this->db->get_last_insert_id ();
								// start---> save the history for add school timing data into h_aqs_school_level table on 11-03-2016 By Mohit Kumar
								$action_school_timing_json = json_encode ( array (
										'AQS_data_id' => $aqsId,
										'school_level_id' => $tm ['school_level_id'],
										'start_time' => $tm ['start_time'],
										"end_time" => $tm ['end_time'] 
								) );
								if (! $this->db->saveHistoryData ( $schoolTimingID, 'h_aqs_school_level', $uniqueID, 'assessmentAQSDataSchoolTimingAdd', $schoolTimingID, $aqsId, $action_school_timing_json, 0, date ( 'Y-m-d H:i:s' ) )) {
									$failed = 87;
								}
								// end---> save the history for add school timing data into h_aqs_school_level table on 11-03-2016 By Mohit Kumar
							}
						}
					
					if (! $aqsDataModel->removeAqsTeam ( $aqsId, 1 )) {
						
						$failed = 5;
					} else {
						if (OFFLINE_STATUS == TRUE) {
							// start---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
							$action_aqs_team_json = json_encode ( array (
									"AQS_data_id" => $aqsId,
									'isInternal' => 1 
							) );
							if (! $this->db->saveHistoryData ( $aqsId, 'd_aqs_team', $uniqueID, 'assessmentAQSDataAQSTeamRemove', $aqsId, $aqsId, $action_aqs_team_json, 0, date ( 'Y-m-d H:i:s' ) )) {
								$failed = 88;
							}
							// end---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
						}
					}
					 //print_r($schoolTeamValidation ['values'])
					foreach ( $schoolTeamValidation ['values'] as $tm )
						
						if (! (empty ( $tm ['name'] ) && empty ( $tm ['designation'] ) && empty ( $tm ['lang_id'] ) && empty ( $tm ['email'] ) && empty ( $tm ['mobile'] )) && ! $aqsDataModel->addAqsTeam ( $aqsId, $tm ['name'], $tm ['designation'], $tm ['lang_id'], $tm ['email'], $tm ['mobile'], 1,$tm ['c_code'] )) {
							
							$failed = 6;
						} else {
							if (OFFLINE_STATUS == TRUE) {
								// get the last insert id of d_aqs_team live server on 11-03-2016 by Mohit Kumar
								$aqsTeamID = $this->db->get_last_insert_id ();
								// start---> save the history for add aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
								$action_aqs_team_json = json_encode ( array (
										'AQS_data_id' => $aqsId,
										'name' => $tm ['name'],
										'designation' => $tm ['designation'],
										"lang_id" => $tm ['lang_id'],
										"email" => $tm ['email'],
										"mobile" => $tm ['mobile'],
										"isInternal" => 1 
								) );
								if (! $this->db->saveHistoryData ( $aqsTeamID, 'd_aqs_team', $uniqueID, 'assessmentAQSDataAQSTeamAdd', $aqsTeamID, $aqsId, $action_aqs_team_json, 0, date ( 'Y-m-d H:i:s' ) )) {
									$failed = 89;
								}
								// end---> save the history for add aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
							}
						}
					
					if ($adhyayanTeamValidation) {
						
						if (! $aqsDataModel->removeAqsTeam ( $aqsId, 0 )) {
							
							$failed = 7;
						} else {
							if (OFFLINE_STATUS == TRUE) {
								// start---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
								$action_aqs_team_json = json_encode ( array (
										"AQS_data_id" => $aqsId,
										'isInternal' => 0 
								) );
								if (! $this->db->saveHistoryData ( $aqsId, 'd_aqs_team', $uniqueID, 'assessmentAQSDataAQSTeamRemove', $aqsId, $aqsId, $action_aqs_team_json, 0, date ( 'Y-m-d H:i:s' ) )) {
									$failed = 90;
								}
								// end---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
							}
						}
						
						foreach ( $adhyayanTeamValidation ['values'] as $tm )
							
							if (! (empty ( $tm ['name'] ) && empty ( $tm ['designation'] ) && empty ( $tm ['lang_id'] ) && empty ( $tm ['email'] ) && empty ( $tm ['mobile'] )) && ! $aqsDataModel->addAqsTeam ( $aqsId, $tm ['name'], $tm ['designation'], $tm ['lang_id'], $tm ['email'], $tm ['mobile'], 0 )) {
								
								$failed = 8;
							} else {
								if (OFFLINE_STATUS == TRUE) {
									// get the last insert id of d_aqs_team live server on 11-03-2016 by Mohit Kumar
									$aqsTeamID = $this->db->get_last_insert_id ();
									// start---> save the history for add aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
									$action_aqs_team_json = json_encode ( array (
											'AQS_data_id' => $aqsId,
											'name' => $tm ['name'],
											'designation' => $tm ['designation'],
											"lang_id" => $tm ['lang_id'],
											"email" => $tm ['email'],
											"mobile" => $tm ['mobile'],
											"isInternal" => 0 
									) );
									if (! $this->db->saveHistoryData ( $aqsTeamID, 'd_aqs_team', $uniqueID, 'assessmentAQSDataAQSTeamAdd', $aqsTeamID, $aqsId, $action_aqs_team_json, 0, date ( 'Y-m-d H:i:s' ) )) {
										$failed = 91;
									}
									// end---> save the history for add aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
								}
							}
					}
					
					// save additional info if any
					if (! $aqsDataModel->removeAdditionalRefTeam ( $aqsId )) {
						$failed = 15;
					} else {
						if (OFFLINE_STATUS == TRUE) {
							// start---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
							$action_remove_additional_refteam_json = json_encode ( array (
									"aqsdata_id" => $aqsId 
							) );
							if (! $this->db->saveHistoryData ( $aqsId, 'd_aqs_additional_references', $uniqueID, 'assessmentAQSDataAdditionalRefTeamRemove', $aqsId, $aqsId, $action_remove_additional_refteam_json, 0, date ( 'Y-m-d H:i:s' ) )) {
								$failed = 92;
							}
							// end---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
						}
					}
					// print_r($additionalRefTeamValidation);
					if (! empty ( $additionalRefTeamValidation ['values'] ))
						foreach ( $additionalRefTeamValidation ['values'] as $tm ) {
							 //print_r($tm);
							if (! empty ( $tm ) && ! $aqsDataModel->addAdditionalRefTeam ( $aqsId, $tm ['name'], $tm ['phone'], $tm ['email'], $tm ['role_stakeholder'] )) {
								$failed = 16;
							} else {
								if (OFFLINE_STATUS == TRUE) {
									$aqsAdditionalTeamID = $this->db->get_last_insert_id ();
									// start---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
									$action_add_additional_refteam_json = json_encode ( array (
											'aqsdata_id' => $aqsId,
											'name' => $tm ['name'],
											'phone' => $tm ['phone'],
											"email" => $tm ['email'],
											"role_stakeholder" => $tm ['role_stakeholder'] 
									) );
									if (! $this->db->saveHistoryData ( $aqsAdditionalTeamID, 'd_aqs_additional_references', $uniqueID, 'assessmentAQSDataAdditionalRefTeamAdd', $aqsAdditionalTeamID, $aqsId, $action_add_additional_refteam_json, 0, date ( 'Y-m-d H:i:s' ) )) {
										$failed = 93;
									}
									// end---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
								}
							}
						}
					if (! empty ( $aqsValidationRes ['additionalValues'] )) {
						$aqs_additional_id = ! empty ( $_POST ['additional'] ['aqs_additional_id'] ) ? $_POST ['additional'] ['aqs_additional_id'] : 0;
						if ($aqs_additional_id == 0) {
							$this->apiResult ['message'] = "aqs_additional_id can't be empty";
							$this->apiResult ['status'] = 0;
							return;
						}
						$aqsAdditinalRow = $aqsDataModel->getAqsAdditionalData ( $aqsId, 'd_aqs_additional_questions' );
						$aqsAdditinalRowId = ! empty ( $aqsAdditinalRow ['aqs_data_id'] ) ? $aqsAdditinalRow ['aqs_data_id'] : 0;
						$additionalQuestions = null;
						if (! $aqsDataModel->removeAdditionalSchoolCommunity ( $aqsId )) {
							$failed = 9;
						} else {
							if (OFFLINE_STATUS == TRUE) {
								// start---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
								$action_remove_additional_school_community_json = json_encode ( array (
										'aqsdata_id' => $aqsId 
								) );
								if (! $this->db->saveHistoryData ( $aqsId, 'h_aqs_school_communities', $uniqueID, 'assessmentAQSDataAdditionalSchoolCommunityRemove', $aqsId, $aqsId, $action_remove_additional_school_community_json, 0, date ( 'Y-m-d H:i:s' ) )) {
									$failed = 94;
								}
								// end---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
							}
						}
						if (! $aqsDataModel->removeAdditionalMediumInstruction ( $aqsId )) {
							$failed = 10;
						} else {
							if (OFFLINE_STATUS == TRUE) {
								// start---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
								$action_remove_additional_medium_instruction_json = json_encode ( array (
										'aqsdata_id' => $aqsId 
								) );
								if (! $this->db->saveHistoryData ( $aqsId, 'h_aqs_medium_instruction', $uniqueID, 'assessmentAQSDataAdditionalMediumInstructionRemove', $aqsId, $aqsId, $action_remove_additional_medium_instruction_json, 0, date ( 'Y-m-d H:i:s' ) )) {
									$failed = 95;
								}
								// end---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
							}
						}
						foreach ( $aqsValidationRes ['additionalValues'] as $key => $additionalData ) {
							if ($key == 'school_community') {
								foreach ( $additionalData as $cmt )
									if (! $aqsDataModel->addAdditionalSchoolCommunity ( $aqsId, $cmt )) {
										$failed = 11;
									} else {
										if (OFFLINE_STATUS == TRUE) {
											$schoolCommID = $this->db->get_last_insert_id ();
											// start---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
											$action_add_school_community_json = json_encode ( array (
													"aqsdata_id" => $aqsId,
													"school_community_id" => $cmt 
											) );
											if (! $this->db->saveHistoryData ( $schoolCommID, 'h_aqs_school_communities', $uniqueID, 'assessmentAQSDataAdditionalSchoolCommunityAdd', $schoolCommID, $aqsId, $action_add_school_community_json, 0, date ( 'Y-m-d H:i:s' ) )) {
												$failed = 96;
											}
											// end---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
										}
									}
							} else if ($key == 'review_medium_instrn_id') {
								foreach ( $additionalData as $cmt )
									if (! $aqsDataModel->addAdditionalMediumInstruction ( $aqsId, $cmt )) {
										$failed = 12;
									} else {
										if (OFFLINE_STATUS == TRUE) {
											$mediumInstructionID = $this->db->get_last_insert_id ();
											// start---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
											$action_add_medium_instruction_json = json_encode ( array (
													"aqsdata_id" => $aqsId,
													"review_medium_instrn_id" => $cmt 
											) );
											if (! $this->db->saveHistoryData ( $mediumInstructionID, 'h_aqs_medium_instruction', $uniqueID, 'assessmentAQSDataAdditionalMediumInstructionAdd', $mediumInstructionID, $aqsId, $action_add_medium_instruction_json, 0, date ( 'Y-m-d H:i:s' ) )) {
												$failed = 97;
											}
											// end---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
										}
									}
							} else
								$additionalQuestions [$key] = $additionalData;
						}
						// if data exists update it
						// else insert
						$additionalQuestions ['aqs_data_id'] = $aqsId;
						$additionalQuestions ['create_date'] = date ( 'Y-m-d' );
						$additionalQuestions ['aqs_additional_id'] = $aqs_additional_id;
						if ($aqsAdditinalRowId > 0) {
							if (! $aqsDataModel->updateAdditionalQuestionsData ( $aqsId, $additionalQuestions )) {
								$failed = 13;
							} else {
								if (OFFLINE_STATUS == TRUE) {
									// start---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
									$action_update_question_data_json = json_encode ( $additionalQuestions );
									if (! $this->db->saveHistoryData ( $aqsId, 'd_aqs_additional_questions', $uniqueID, 'assessmentAQSDataAdditionalQuestionsDataUpdate', $aqsId, $aqsId, $action_update_question_data_json, 0, date ( 'Y-m-d H:i:s' ) )) {
										$failed = 98;
									}
									// end---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
								}
							}
						} else {
							if (! $aqsDataModel->insertAdditionalQuestionsData ( $additionalQuestions )) {
								$failed = 14;
							} else {
								if (OFFLINE_STATUS == TRUE) {
									$additionalQuestionsID = $this->db->get_last_insert_id ();
									// start---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
									$action_add_question_data_json = json_encode ( $additionalQuestions );
									if (! $this->db->saveHistoryData ( $additionalQuestionsID, 'd_aqs_additional_questions', $uniqueID, 'assessmentAQSDataAdditionalQuestionsDataAdd', $additionalQuestionsID, $aqsId, $action_add_question_data_json, 0, date ( 'Y-m-d H:i:s' ) )) {
										$failed = 99;
									}
									// end---> save the history for remove aqs team data into d_aqs_team table on 11-03-2016 By Mohit Kumar
								}
							}
						}
					}
					
					if ($aqsId > 0 && $failed == 0 && $this->db->commit ()) {
						
						$this->apiResult ["status"] = 1;
						
						$this->apiResult ["message"] = "Successfully saved";
					} else {
						
						$this->db->rollback ();
						
						$this->apiResult ["message"] = "Error occurred, please check the error logs. Error Code: $failed\n";
					}
				}
			} else {
				
				$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			}
		} else {
			
			$this->apiResult ["message"] = "Wrong review id\n";
		}
	}
        
	function sendMailAction() 

	{
		if (! (in_array ( "create_self_review", $this->user ['capabilities'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['reviewtype'] )) {
			
			$this->apiResult ["message"] = "Review id cannot be empty\n";
		} 

		else 

		{       
                        $admin_role=0;
                        if(in_array(1,$this->user['role_ids'])||in_array(2,$this->user['role_ids'])){
                        $admin_role=1;    
                        }                                        
			
                        $is_guest=$this->user['is_guest'];
			// date_default_timezone_set('Etc/UTC');
			
			$clientId = $this->user ['client_id'];
			
			$userModel = new userModel ();
			
			$adminMail = $userModel->getAdminEmail ();
			
			$clientModel = new clientModel ();
			
			$schoolName = $clientModel->getClientById ( $clientId );
			
			$assessmentModel = new assessmentModel ();
			
			$schoolName = $schoolName ['client_name'];
			
			$adminEmails = '';
			
			foreach ( $adminMail as $admin )
				
				$adminEmails .= $admin ['email'] . ';';
			
			$subAssessmentTypeId = $_POST ['reviewtype'];
			
			// Replace the plain text body with one created manually						
			$assessmentModel->saveSubAssessmentTypeRequest ( $subAssessmentTypeId, $this->user ['user_id'] );						
			$fromEmail = $this->user ['email'];
			
			$fromName = $this->user ['name'];
			
			$ccEmail = 'poonam.choksi@adhyayan.asia'; // 'nisha@a-insight.com';
                        $toEmail = 'shraddha.adhyayan@gmail.com'; // 'nisha@a-insight.com';
			$ccName = 'Shraddha Khedekar';
			$toName = 'Adhyayan Admin';
                        $reviewType = '';
                        if ($subAssessmentTypeId == 1){				
                                $reviewType = "Online Self-Review";
				$message = "$fromName($fromEmail) from $schoolName school  has created an $reviewType.";                                
                        }
			elseif ($subAssessmentTypeId == 2){				
                                $reviewType = "Face to Face review";
				$message = "$fromName($fromEmail) from $schoolName school  has requested for a $reviewType.";                                
                        }			
			elseif ($subAssessmentTypeId == 3){
                                $reviewType = "Online and Face to Face review";
				$message = "$fromName($fromEmail) from $schoolName school  has requested for an $reviewType.";
                        }
			// Set the subject line
			
			$subject = 'Review Request';			
			
			$body = "Dear $toName, <br><br><br>{$message} Please take required action.<br><br><br>Thanks";									
			if ($admin_role==0 && $is_guest==0 && ! sendEmail($fromEmail,$fromName,$toEmail,$toName,$subject,$body,$ccEmail)) {												
				$this->apiResult ["message"] = "Error occured while sending email.";
			} else {
				if($admin_role==0 && $is_guest==0){
				$this->apiResult ["message"] = "A request has been sent for $reviewType. For more details, please contact Adhyayan administrator (info@adhyayan.asia).";
                                }else if($admin_role==1 || $is_guest==1){
                                $this->apiResult ["message"] = "";    
                                }
                                
				$this->apiResult ["status"] = 1;
			}
		}
	}
	function getReportDataAction() {
		$assessment_id = empty ( $_POST ['assessment_id'] ) ? 0 : $_POST ['assessment_id'];
		
		$group_assessment_id = empty ( $_POST ['group_assessment_id'] ) ? 0 : $_POST ['group_assessment_id'];
		
		$repId = empty ( $_POST ['report_id'] ) ? 0 : $_POST ['report_id'];
		$lang_id = empty ( $_POST ['lang_id'] ) ? 0 : $_POST ['lang_id'];
		
		$diagnosticModel = new diagnosticModel ();
		
		if (! (in_array ( "view_all_assessments", $this->user ['capabilities'] ) || in_array ( "create_self_review", $this->user ['capabilities'] ) || in_array ( "take_external_assessment", $this->user ['capabilities'] ) || $repId == 5 || $repId == 9)) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if ($group_assessment_id == 0 && $assessment_id == 0) {
			
			$this->apiResult ["message"] = "Review id and Group review id both can not be empty\n";
		} else if (empty ( $_POST ['report_id'] )) {
			
			$this->apiResult ["message"] = "Report id cannot be empty\n";
		} else if ($report = $assessment_id > 0 ? $diagnosticModel->getReportsByAssessmentId ( $assessment_id ) : $diagnosticModel->getTeacherAssessmentReports ( $group_assessment_id )) {
			//print_r($report);
			if (! empty ( $assessment ['statusByRole'] [4] ) && $assessment ['statusByRole'] [4] == 1 && in_array ( "take_external_assessment", $this->user ['capabilities'] )) 

			{
				
				$this->_notPermitted = 1;
				
				return;
			}
			//print_r($report);
                        //echo $_POST ['report_id'];
			if ($assessment_id > 0) {
				
				$report = isset ( $report [$_POST ['report_id']] ) ? $report [$_POST ['report_id']] : null;
			} else {
				
				$report = isset ( $report ['report_id'] ) && $report ['report_id'] == $_POST ['report_id'] ? $report : null;
			}
			
			if (! $report) {
				
				$this->apiResult ["message"] = "Wrong report id\n";
			} /*else if ($report ['aqs_status'] != 1 && $repId!=9) {
				
				$this->apiResult ["message"] = "Assessment not completed yet\n";
			}*/ else if ($report ['isGenerated'] == 0 && empty ( $_POST ['years'] ) && empty ( $_POST ['months'] )) {
				
				$this->apiResult ["message"] = "Report not generated yet\n";
			} else {
				
				$years = empty ( $_POST ['years'] ) ? 0 : $_POST ['years'];
				
				$months = empty ( $_POST ['months'] ) ? 0 : $_POST ['months'];
				
				$tMonths = $months + ($years * 12);
				
				//$conductedDate = date ( "m-Y" );
                                if($report['school_aqs_pref_end_date']=="" || $report['school_aqs_pref_end_date']=="0000-00-00"){
                                $conductedDate = date ( "M-Y", strtotime($report['create_date']));
				$validDate = date ( "M-Y", strtotime ( "+$tMonths month", strtotime($report['create_date'])) );
                                }else{
                                $conductedDate = date ( "M-Y", strtotime($report['school_aqs_pref_end_date']));
				$validDate = date ( "M-Y", strtotime ( "+$tMonths month", strtotime($report['school_aqs_pref_end_date'])) );
                                }
				//$validDate = date ( "m-Y", strtotime ( "+$tMonths month" ) );
				
				$diagnostic_id = empty ( $_POST ['diagnostic_id'] ) ? 0 : $_POST ['diagnostic_id'];
				
				$subAssessmentType = ! empty ( $report ['subAssessmentType'] ) ? $report ['subAssessmentType'] : 0;
				
                                //$lang_id = $diagnosticModel->getLanguageId();
				$reportObject = $assessment_id > 0 ? new individualReport ( $assessment_id, $subAssessmentType, $_POST ['report_id'], $conductedDate, $validDate ) : new groupReport ( $group_assessment_id, $_POST ['report_id'], $diagnostic_id, $conductedDate, $validDate );
				
				$this->apiResult ["status"] = 1;
				//echo "<pre>";print_r($reportObject->generateOutput ($lang_id));
				$this->apiResult ["reportData"] = $reportObject->generateOutput ($lang_id);
			}
		} else {
			
			$this->apiResult ["message"] = "Wrong review id\n";
		}
	}
        function getReportRound2DataAction() {
		$assessment_id = empty ( $_POST ['assessment_id'] ) ? 0 : $_POST ['assessment_id'];
		
		$group_assessment_id = empty ( $_POST ['group_assessment_id'] ) ? 0 : $_POST ['group_assessment_id'];
		
		$repId = empty ( $_POST ['report_id'] ) ? 0 : $_POST ['report_id'];
		$lang_id = empty ( $_POST ['lang_id'] ) ? 0 : $_POST ['lang_id'];
		
		$diagnosticModel = new diagnosticModel ();
		
		if (! (in_array ( "view_all_assessments", $this->user ['capabilities'] ) || in_array ( "create_self_review", $this->user ['capabilities'] ) || in_array ( "take_external_assessment", $this->user ['capabilities'] ) || $repId == 5 || $repId == 9)) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if ($group_assessment_id == 0 && $assessment_id == 0) {
			
			$this->apiResult ["message"] = "Review id and Group review id both can not be empty\n";
		} else if (empty ( $_POST ['report_id'] )) {
			
			$this->apiResult ["message"] = "Report id cannot be empty\n";
		} else if ($report = $assessment_id > 0 ? $diagnosticModel->getReportsByAssessmentId ( $assessment_id ) : $diagnosticModel->getTeacherAssessmentReports ( $group_assessment_id )) {
			//print_r($report);
			if (! empty ( $assessment ['statusByRole'] [4] ) && $assessment ['statusByRole'] [4] == 1 && in_array ( "take_external_assessment", $this->user ['capabilities'] )) 

			{
				
				$this->_notPermitted = 1;
				
				return;
			}
			//print_r($report);
                        //echo $_POST ['report_id'];
			if ($assessment_id > 0) {
				
				$report = isset ( $report [$_POST ['report_id']] ) ? $report [$_POST ['report_id']] : null;
			} else {
				
				$report = isset ( $report ['report_id'] ) && $report ['report_id'] == $_POST ['report_id'] ? $report : null;
			}
			
			if (! $report) {
				
				$this->apiResult ["message"] = "Wrong report id\n";
			} /*else if ($report ['aqs_status'] != 1 && $repId!=9) {
				
				$this->apiResult ["message"] = "Assessment not completed yet\n";
			}*/ else if ($report ['isGenerated'] == 0 && empty ( $_POST ['years'] ) && empty ( $_POST ['months'] )) {
				
				$this->apiResult ["message"] = "Report not generated yet\n";
			} else {
				
				$years = empty ( $_POST ['years'] ) ? 0 : $_POST ['years'];
				
				$months = empty ( $_POST ['months'] ) ? 0 : $_POST ['months'];
				
				$tMonths = $months + ($years * 12);
				
				//$conductedDate = date ( "m-Y" );
                                if($report['school_aqs_pref_end_date']=="" || $report['school_aqs_pref_end_date']=="0000-00-00"){
                                $conductedDate = date ( "M-Y", strtotime($report['create_date']));
				$validDate = date ( "M-Y", strtotime ( "+$tMonths month", strtotime($report['create_date'])) );
                                }else{
                                $conductedDate = date ( "M-Y", strtotime($report['school_aqs_pref_end_date']));
				$validDate = date ( "M-Y", strtotime ( "+$tMonths month", strtotime($report['school_aqs_pref_end_date'])) );
                                }
				//$validDate = date ( "m-Y", strtotime ( "+$tMonths month" ) );
				
				$diagnostic_id = empty ( $_POST ['diagnostic_id'] ) ? 0 : $_POST ['diagnostic_id'];
				
				$subAssessmentType = ! empty ( $report ['subAssessmentType'] ) ? $report ['subAssessmentType'] : 0;
				
                                //$lang_id = $diagnosticModel->getLanguageId();
				$reportObject = $assessment_id > 0 ? new individualReport ( $assessment_id, $subAssessmentType, $_POST ['report_id'], $conductedDate, $validDate ) : new groupReport ( $group_assessment_id, $_POST ['report_id'], $diagnostic_id, $conductedDate, $validDate );
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["reportData"] = $reportObject->generateRound2Output ($lang_id);
                                //echo "<pre>";print_r($this->apiResult ["reportData"]);
			}
		} else {
			
			$this->apiResult ["message"] = "Wrong review id\n";
		}
	}
	function publishReportAction() {
		$diagnosticModel = new diagnosticModel ();
		
		if (! in_array ( "view_all_assessments", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['ass_or_group_ass_id'] )) {
			
			$this->apiResult ["message"] = "Review id cannot be empty\n";
		} else if (empty ( $_POST ['assessment_type_id'] )) {
			
			$this->apiResult ["message"] = "Review type id cannot be empty\n";
		} else if (empty ( $_POST ['years'] ) && empty ( $_POST ['months'] )) {
			
			$this->apiResult ["message"] = "Year and month both can't be empty\n";
		} else if ($assessment = ($_POST ['assessment_type_id'] == 1 || $_POST ['assessment_type_id'] == 5) ? $diagnosticModel->getAssessmentById ( $_POST ['ass_or_group_ass_id'] ) : $diagnosticModel->getTeacherAssessmentReports ( $_POST ['ass_or_group_ass_id'] )) {
			
			$assessment_id = ($_POST ['assessment_type_id'] == 1 || $_POST ['assessment_type_id'] == 5) ? $_POST ['ass_or_group_ass_id'] : 0;
			
			$group_assessment_id = ($_POST ['assessment_type_id'] == 1 || $_POST ['assessment_type_id'] == 5 ) ? 0 : $_POST ['ass_or_group_ass_id'];
			
			$reports = $assessment_id > 0 ? $diagnosticModel->getReportsByAssessmentId ( $_POST ['ass_or_group_ass_id'], false ) : $diagnosticModel->getSubAssReportsByGroupAssessmentId ( $group_assessment_id );
			
			$years = empty ( $_POST ['years'] ) ? 0 : $_POST ['years'];
			
			$months = empty ( $_POST ['months'] ) ? 0 : $_POST ['months'];
			
			$isPublished = $group_assessment_id > 0 ? (isset ( $reports [0] ['report_data'] [0] ) ? $reports [0] ['report_data'] [0] ['isPublished'] : 0) : $reports [0] ['isPublished'];
			
			if ($assessment ['aqs_status'] != 1 && $assessment['assessment_type_id']!=4) {
				
				$this->apiResult ["message"] = "School profile still not submitted\n";
			} else if ($assessment_id > 0 && $assessment ['statusByRole'] [4] != 1) {
				
				$this->apiResult ["message"] = "External review form still not submitted\n";
			} else if ($group_assessment_id > 0 && ($assessment ['allStatusFilled'] != 1 || $assessment ['allTchrInfoFilled'] != 1)) {
				if($_POST ['assessment_type_id']==4){
                                $this->apiResult ["message"] = "Either all student info form still not submitted or all external review forms not submitted\n";    
                                }else{
				$this->apiResult ["message"] = "Either all teacher info form still not submitted or all external review forms not submitted\n";
                                }
			} else if (count ( $reports ) == 0) {
				
				$this->apiResult ["message"] = "Reports does not exists\n";
			} else if ($group_assessment_id > 0 ? (isset ( $reports [0] ['report_data'] [0] ) ? $reports [0] ['report_data'] [0] ['isPublished'] : 0) : $reports [0] ['isPublished']) {
				
				$this->apiResult ["message"] = "Reports already published\n";
			} else {
				
				if (OFFLINE_STATUS == TRUE) {
					$uniqueID = $this->db->createUniqueID ( 'publishReport' );
				}
                                
				
				$aids = array ();
				$grs=array();
				$reportsType = null;
				foreach ( $reports as $report ) {
					
					$aid = $group_assessment_id > 0 ? $report ['assessment_id'] : $assessment_id;
					
					$aids [$aid] = $aid;
                                        
                                        if($assessment['school_aqs_pref_end_date']=="" || $assessment['school_aqs_pref_end_date']=="0000-00-00"){
                                        $valid_start_date=$assessment['create_date'];    
                                        }else{
                                        $valid_start_date=$assessment['school_aqs_pref_end_date'];       
                                        }
					
					if ($report ['isGenerated'] == 1) {
						
						$diagnosticModel->updateAssessmentReport ( $aid, $report ['report_id'], $years, $months,$valid_start_date, 1 );
						if ($_POST ['assessment_type_id'] == 1 && OFFLINE_STATUS == TRUE) {
							// save publish report history on local server on 01-04-2016 By Mohit Kumar
							$totalMonths = $months + ($years * 12);
							$data = json_encode ( array (
									/*"valid_until" => date ( "Y-m-d H:i:s", strtotime ( "+$totalMonths month" ) ),*/
                                                                        "valid_until" => date ( "Y-m-d H:i:s", strtotime ( "+$totalMonths month", strtotime($valid_start_date)) ),
									'publishDate' => date ( "Y-m-d H:i:s" ),
									'isPublished' => 1 
							) );
							$this->db->saveHistoryData ( $aid, 'h_assessment_report', $uniqueID, 'publishReportUpdate', $aid, $report ['report_id'], $data, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
						}
					} else {
						
						$diagnosticModel->insertAssessmentReport ( $aid, $report ['report_id'], $years, $months,$valid_start_date,1 );
						$assessmentReportId = $this->db->get_last_insert_id ();
						if ($_POST ['assessment_type_id'] == 1 && OFFLINE_STATUS == TRUE) {
							// save publish report history on local server on 01-04-2016 By Mohit Kumar
							$totalMonths = $months + ($years * 12);
							$data = json_encode ( array (
									/*"valid_until" => date ( "Y-m-d H:i:s", strtotime ( "+$totalMonths month" ) ),*/
                                                                        "valid_until" => date ( "Y-m-d H:i:s", strtotime ( "+$totalMonths month", strtotime($valid_start_date)) ),
									'publishDate' => date ( "Y-m-d H:i:s" ),
									'isPublished' => 1,
									'report_id' => $report ['report_id'] 
							) );
							$this->db->saveHistoryData ( $assessmentReportId, 'h_assessment_report', $uniqueID, 'publishReportInsert', $aid, $report ['report_id'], $data, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
						}
					}
				}
				
				foreach ( $aids as $aid ) {
					
					$diagnosticModel->updateAssessorKeyNotesStatus ( $aid, 1 );
					if (OFFLINE_STATUS == TRUE) {
						// save publish report history on local server on 01-04-2016 By Mohit Kumar
						$data = json_encode ( array (
								"isAssessorKeyNotesApproved" => 1 
						) );
						$this->db->saveHistoryData ( $aid, 'd_assessment', $uniqueID, 'publishReportAssessorKeyNotesStatusUpdate', $aid, 1, $data, 0, date ( 'Y-m-d H:i:s' ) );
						// end---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
					}
				}
				
				if ($group_assessment_id > 0) {
					
					if ($assessment ['isGenerated'] == 1)
						
						$diagnosticModel->updateGroupAssessmentReport ( $group_assessment_id, $assessment ['report_id'], $years, $months, 1 );
					
					else
						
						$diagnosticModel->insertGroupAssessmentReport ( $group_assessment_id, $assessment ['report_id'], $years, $months, 1 );
				}
				
				$this->apiResult ["status"] = 1;
			}
			
			if ($this->apiResult ["status"] == 1) {
				
				$reports = $assessment_id > 0 ? $diagnosticModel->getReportsByAssessmentId ( $assessment_id, false ) : $diagnosticModel->getSubAssReportsByGroupAssessmentId ( $group_assessment_id );
				//$reportsType = $diagnosticModel->getReportsType(2);//teacher
                                 if(isset($assessment['assessment_type_id']) && $assessment['assessment_type_id']==4){
                                 $reportsType = $diagnosticModel->getReportsType(4);    
                                 }else{
                                 $reportsType = $diagnosticModel->getReportsType(2);    
                                 }
				if ($group_assessment_id > 0) {
					
					$assessment = $diagnosticModel->getTeacherAssessmentReports ( $group_assessment_id );
					
					$reports = array_merge ( array (
							$assessment 
					), $reports );
										
					
					foreach($assessment['diagnostic_ids'] as $dId=>$cat_name){
					
						$temp=$assessment;
					
						$temp['diagnostic_id']=$dId;
					
						$temp['report_name'].=' - '.$cat_name;
					
						$temp['teacher_category']=$cat_name;
					
						$grs[]=$temp;
					
					}
				}
				
				ob_start ();
				$reportsIndividual = array_filter($reports,function($var){
					if($var['report_id']==5 || $var['report_id']==9)
						return array($var['assessment_id']=>$var['user_names'][0]);
				});				
				$reportsSingleTeacher = array_filter($reports,function($var){
					if($var['report_id']==7 || $var['report_id']==10)
						return $var;
				});
				$groupAssessmentId = $group_assessment_id;
				$diagnosticsForGroup = (!empty($grs[0])?$grs[0]['diagnostic_ids']:0);
                                $res = $group_assessment_id>0?0:$diagnosticModel->getNumberOfKpasDiagnostic($assessment['diagnostic_id']);
				//print_r($res);
				$numKpas = $group_assessment_id>1?0:$res['num'];
                                if(isset($assessment['diagnostic_id']) && $assessment['diagnostic_id']!='')
                                    $diagnosticsLanguage = $diagnosticModel->getDiagnosticLanguages($assessment['diagnostic_id']);
				include (ROOT . 'application' . DS . 'views' . DS . "assessment" . DS . 'reportlist.php');
				
				$this->apiResult ["content"] = ob_get_contents ();
				
				ob_end_clean ();
			}
		} else {
			
			$this->apiResult ["message"] = "Wrong review id\n";
		}
	}
        
        function createStudentAssessmentAction() {
                $client_id=isset($_POST ['client_id'])?$_POST ['client_id']:0;
                $assessmentModel = new assessmentModel ();
                $rounds=$assessmentModel->getStudentRounds($_POST ['client_id']);
                $array_round = array_column($rounds, 'aqs_round');
                //die();
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else if (empty ( $_POST ['school_admin_id'] )) {
			
			$this->apiResult ["message"] = "School Admin cannot be empty\n";
		} else if (empty ( $_POST ['teacher_cat'] )) {
			
			$this->apiResult ["message"] = "Please select diagnostic\n";
		}else if (empty ( $_POST ['student_review_type'] )) {
			
			$this->apiResult ["message"] = "Please select type\n";
		}else if (empty ( $_POST ['student_round'] )) {
			
			$this->apiResult ["message"] = "Please select round\n";
		}else if(!in_array($_POST ['student_round'],$array_round)){
                    $this->apiResult ["message"] = "This round is already assigned for this batch\n";
                }
                /*else if (empty ( $_POST ['student_batch_no'] )) {
			
			$this->apiResult ["message"] = "Please select batch\n";
		}*/ else {
                                                                
			if($_POST ['student_round']>=2){
                        $groupdata=$assessmentModel->getGAIdfromClientandRound($client_id,1);
                        $gaid=$groupdata['group_assessment_id'];
                        if(empty($gaid)|| $gaid<=0){
                        $this->apiResult ["message"] = "Please first create the assessment for Round-1\n";
                        return;
                        }
                        }
                        
			$diagnosticAssignedToTeacherCat = array ();
			
			foreach ( $_POST ['teacher_cat'] as $teacher_cat_id => $diagnostic_id ) {
				
				if ($diagnostic_id > 0) {
					
					$diagnosticAssignedToTeacherCat [$teacher_cat_id] = $diagnostic_id;
				}
			}
			
			if (count ( $diagnosticAssignedToTeacherCat ) > 0) {
				
				$this->db->start_transaction ();
				
				
				$error = 0;
				
				$gaid = $assessmentModel->createStudentAssessment ( $_POST ['client_id'], $_POST ['school_admin_id'], $_POST['student_review_type'],$_POST['student_profile_form_id'],$_POST['student_round']);
				
				if ($gaid > 0) {
					
					if (! empty ( $_POST ['eAssessor'] ))
						
						foreach ( $_POST ['eAssessor'] as $eAssessorId => $val ) {
							
							if (! $assessmentModel->addExternalAssessorToGroupAssessment ( $gaid, $eAssessorId, 1 ))
								
								$error = 1;
						}
					
					foreach ( $diagnosticAssignedToTeacherCat as $teacher_cat_id => $diagnostic_id ) {
						
						if (! $assessmentModel->addDignosticToGroupAssessment ( $gaid, $teacher_cat_id, $diagnostic_id ))
							
							$error = 2;
					}
				} else
					
					$error = 3;
				
				if ($error == 0 && $this->db->commit ()) {
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["assessment_id"] = $gaid;
					
					$this->apiResult ["message"] = "Review successfully created";
				} else {
					
					$this->apiResult ["message"] = "Unable to create review";
					
					$this->apiResult ["errorCode"] = $error;
					
					$this->db->rollback ();
				}
			} else {
				
				$this->apiResult ["message"] = "Please select diagnostic\n";
			}
		}
	}
        
        function updateStudentAssessmentAction() {
                $client_id=isset($_POST ['client_id'])?$_POST ['client_id']:0;
                $gaid=isset($_POST ['gaid'])?$_POST ['gaid']:0;
                $assessmentModel = new assessmentModel ();
                $diagnosticModel = new diagnosticModel();
                $teacherAssessment=$diagnosticModel->getGroupAssessmentByGAId($gaid);
                $rounds=$assessmentModel->getStudentRounds($_POST ['client_id'],$teacherAssessment['student_round']);
                $array_round = array_column($rounds, 'aqs_round');                                                
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['gaid'] )) {
			
			$this->apiResult ["message"] = "Group assessment id cannot be empty\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else if (empty ( $_POST ['school_admin_id'] )) {
			
			$this->apiResult ["message"] = "School Admin cannot be empty\n";
		} else if (empty ( $_POST ['teacher_cat'] )) {
			
			$this->apiResult ["message"] = "Please select diagnostic\n";
		} else if (empty ( $_POST ['student_review_type'] )) {
			
			$this->apiResult ["message"] = "Please select type\n";
		}else if (empty ( $_POST ['student_round'] )) {
			
			$this->apiResult ["message"] = "Please select round\n";
		}else if(!in_array($_POST ['student_round'],$array_round)){
                    $this->apiResult ["message"] = "This round is already assigned for this batch\n";
                }/*else if (empty ( $_POST ['student_batch_no'] )) {
			
			$this->apiResult ["message"] = "Please select batch\n";
		}*/else {
                    
                        if($_POST ['student_round']>=2){
                        $groupdata=$assessmentModel->getGAIdfromClientandRoundUpdate($client_id,1,$gaid);
                        $gaid=$groupdata['group_assessment_id'];
                        if(empty($gaid)|| $gaid<=0){
                        $this->apiResult ["message"] = "Please first create the assessment for Round-1\n";
                        return;
                        }
                        }
                        
			$gaid = $_POST ['gaid'];
                        //$assessmentModel = new assessmentModel ();
                        $used_diagnostics_category=$assessmentModel->getAllUsedDiagnosticsCategory($gaid);
                        //print_r($used_diagnostics_category);
			$diagnosticAssignedToTeacherCat = array ();
			$UsedTeacherCatBlank = array ();
                        $all_dia=$assessmentModel->getTeacherCategoryList();
                        $diagnostic_array=array();
                        foreach($all_dia as $teacherCategory){
                        $diagnostic_array[$teacherCategory['teacher_category_id']]=$teacherCategory['teacher_category'];    
                        }
			foreach ( $_POST ['teacher_cat'] as $teacher_cat_id => $diagnostic_id ) {
				
				if ($diagnostic_id > 0) {
					
					$diagnosticAssignedToTeacherCat [$teacher_cat_id] = $diagnostic_id;
				}else{
                                    if(in_array($teacher_cat_id,$used_diagnostics_category)){
                                     $UsedTeacherCatBlank[]=$diagnostic_array[$teacher_cat_id];   
                                    }
                                }
			}
                        $reviewers_data=$assessmentModel->getAllUsedDiagnostics($gaid,2);
                        $used_reviewers=(isset($reviewers_data['all_validators']) && !empty($reviewers_data['all_validators']))?explode(",",$reviewers_data['all_validators']):array();
                        
                        //echo print_r(array_intersect($used_reviewers, $_POST ['eAssessor']));
                        //print_r($used_reviewers);                                        
                        $not_to_delete=1;
                        if (! empty ( $_POST ['eAssessor'] ) && count($used_reviewers)>0 && count(array_intersect($used_reviewers, $_POST ['eAssessor'])) === 0){
                        $not_to_delete=0;    
                        }
                        if($not_to_delete){
			if (count ( $diagnosticAssignedToTeacherCat ) > 0) {
                                if(count($UsedTeacherCatBlank)<=0 || $teacherAssessment['assessmentAssigned']==0){                                

                               $this->db->start_transaction ();
				
				$error = 0;
				
				if ($assessmentModel->updateStudentAssessment ( $_POST ['client_id'], $_POST ['school_admin_id'], $_POST['student_review_type'],$_POST['student_profile_form_id'], $gaid, $_POST['student_round'] ) && $assessmentModel->removeAllExternalAssessorFromGroupAssessment ( $gaid ) && $assessmentModel->removeDignosticToGroupAssessment ( $gaid )) {
					
					if (! empty ( $_POST ['eAssessor'] ))
						
						foreach ( $_POST ['eAssessor'] as $eAssessorId => $val ) {
							
							if (! $assessmentModel->addExternalAssessorToGroupAssessment ( $gaid, $eAssessorId, 1 ))
								
								$error = 1;
						}
					
					foreach ( $diagnosticAssignedToTeacherCat as $teacher_cat_id => $diagnostic_id ) {
						
						if (! $assessmentModel->addDignosticToGroupAssessment ( $gaid, $teacher_cat_id, $diagnostic_id ))
							
							$error = 2;
					}
				} else
					
					$error = 3;
				
				if ($error == 0 && $this->db->commit ()) {
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["assessment_id"] = $gaid;
					
					$this->apiResult ["message"] = "Review successfully updated";
				} else {
					
					$this->apiResult ["message"] = "Unable to update review";
					
					$this->apiResult ["errorCode"] = $error;
					
					$this->db->rollback ();
				}
                                }else{
                                $this->apiResult ["message"] = "Diagnostic for categories ".(implode(",",$UsedTeacherCatBlank)).", cannot be blank as these are getting used in Step-2 against Teachers. To remove these diagnostic, please update/assign the other category against teachers in Step-2.\n";    
                                }
			} else {
				
				$this->apiResult ["message"] = "Please select diagnostic for atleast one teacher category\n";
			}
                        }else{
                          $this->apiResult ["message"] = "Not allowed to delete external reviewer\n";  
                        }
		}
	}
        
	function createTeacherAssessmentAction() {
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else if (empty ( $_POST ['school_admin_id'] )) {
			
			$this->apiResult ["message"] = "School Admin cannot be empty\n";
		} else if (empty ( $_POST ['teacher_cat'] )) {
			
			$this->apiResult ["message"] = "Please select atleast one diagnostic\n";
		}else if (empty ( $_POST ['student_round'] )) {
			
			$this->apiResult ["message"] = "Round cannot be empty\n";
		} else {
			
			$diagnosticAssignedToTeacherCat = array ();
			
			foreach ( $_POST ['teacher_cat'] as $teacher_cat_id => $diagnostic_id ) {
				
				if ($diagnostic_id > 0) {
					
					$diagnosticAssignedToTeacherCat [$teacher_cat_id] = $diagnostic_id;
				}
			}
			
			if (count ( $diagnosticAssignedToTeacherCat ) > 0) {
				
				$this->db->start_transaction ();
				
				$assessmentModel = new assessmentModel ();
				
				$error = 0;
				
				$gaid = $assessmentModel->createTeacherAssessment ( $_POST ['client_id'], $_POST ['school_admin_id'], $_POST ['student_round'] );
				
				if ($gaid > 0) {
					
					if (! empty ( $_POST ['eAssessor'] ))
						
						foreach ( $_POST ['eAssessor'] as $eAssessorId => $val ) {
							
							if (! $assessmentModel->addExternalAssessorToGroupAssessment ( $gaid, $eAssessorId, 1 ))
								
								$error = 1;
						}
					
					foreach ( $diagnosticAssignedToTeacherCat as $teacher_cat_id => $diagnostic_id ) {
						
						if (! $assessmentModel->addDignosticToGroupAssessment ( $gaid, $teacher_cat_id, $diagnostic_id ))
							
							$error = 2;
					}
				} else
					
					$error = 3;
				
				if ($error == 0 && $this->db->commit ()) {
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["assessment_id"] = $gaid;
					
					$this->apiResult ["message"] = "Review successfully created";
				} else {
					
					$this->apiResult ["message"] = "Unable to create review";
					
					$this->apiResult ["errorCode"] = $error;
					
					$this->db->rollback ();
				}
			} else {
				
				$this->apiResult ["message"] = "Please select diagnostic for atleast one teacher category\n";
			}
		}
	}
	function updateTeacherAssessmentAction() {
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['gaid'] )) {
			
			$this->apiResult ["message"] = "Group assessment id cannot be empty\n";
		} else if (empty ( $_POST ['client_id'] )) {
			
			$this->apiResult ["message"] = "School id cannot be empty\n";
		} else if (empty ( $_POST ['school_admin_id'] )) {
			
			$this->apiResult ["message"] = "School Admin cannot be empty\n";
		} else if (empty ( $_POST ['teacher_cat'] )) {
			
			$this->apiResult ["message"] = "Please select atleast one diagnostic\n";
		} else if (empty ( $_POST ['student_round'] )) {
			
			$this->apiResult ["message"] = "Round cannot be empty\n";
		} else {
			$gaid = $_POST ['gaid'];
                        $assessmentModel = new assessmentModel ();
                        $used_diagnostics_category=$assessmentModel->getAllUsedDiagnosticsCategory($gaid);
                        //print_r($used_diagnostics_category);
			$diagnosticAssignedToTeacherCat = array ();
			$UsedTeacherCatBlank = array ();
                        $all_dia=$assessmentModel->getTeacherCategoryList();
                        $diagnostic_array=array();
                        foreach($all_dia as $teacherCategory){
                        $diagnostic_array[$teacherCategory['teacher_category_id']]=$teacherCategory['teacher_category'];    
                        }
			foreach ( $_POST ['teacher_cat'] as $teacher_cat_id => $diagnostic_id ) {
				
				if ($diagnostic_id > 0) {
					
					$diagnosticAssignedToTeacherCat [$teacher_cat_id] = $diagnostic_id;
				}else{
                                    if(in_array($teacher_cat_id,$used_diagnostics_category)){
                                     $UsedTeacherCatBlank[]=$diagnostic_array[$teacher_cat_id];   
                                    }
                                }
			}
                        $reviewers_data=$assessmentModel->getAllUsedDiagnostics($gaid,2);
                        $used_reviewers=(isset($reviewers_data['all_validators']) && !empty($reviewers_data['all_validators']))?explode(",",$reviewers_data['all_validators']):array();
                        
                        $diagnosticModel = new diagnosticModel();
			$teacherAssessment=$diagnosticModel->getGroupAssessmentByGAId($gaid);
                        //echo print_r(array_intersect($used_reviewers, $_POST ['eAssessor']));
                        //print_r($used_reviewers);                                        
                        $not_to_delete=1;
                        if (! empty ( $_POST ['eAssessor'] ) && count($used_reviewers)>0 && count(array_intersect($used_reviewers, $_POST ['eAssessor'])) === 0){
                        $not_to_delete=0;    
                        }
                        if($not_to_delete){
			if (count ( $diagnosticAssignedToTeacherCat ) > 0) {
                                if(count($UsedTeacherCatBlank)<=0 || $teacherAssessment['assessmentAssigned']==0){                                

                               $this->db->start_transaction ();
				
				$error = 0;
				
				if ($assessmentModel->updateTeacherAssessment ( $_POST ['client_id'], $_POST ['school_admin_id'], $gaid,$_POST ['student_round'] ) && $assessmentModel->removeAllExternalAssessorFromGroupAssessment ( $gaid ) && $assessmentModel->removeDignosticToGroupAssessment ( $gaid )) {
					
					if (! empty ( $_POST ['eAssessor'] ))
						
						foreach ( $_POST ['eAssessor'] as $eAssessorId => $val ) {
							
							if (! $assessmentModel->addExternalAssessorToGroupAssessment ( $gaid, $eAssessorId, 1 ))
								
								$error = 1;
						}
					
					foreach ( $diagnosticAssignedToTeacherCat as $teacher_cat_id => $diagnostic_id ) {
						
						if (! $assessmentModel->addDignosticToGroupAssessment ( $gaid, $teacher_cat_id, $diagnostic_id ))
							
							$error = 2;
					}
				} else
					
					$error = 3;
				
				if ($error == 0 && $this->db->commit ()) {
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["assessment_id"] = $gaid;
					
					$this->apiResult ["message"] = "Review successfully updated";
				} else {
					
					$this->apiResult ["message"] = "Unable to update review";
					
					$this->apiResult ["errorCode"] = $error;
					
					$this->db->rollback ();
				}
                                }else{
                                $this->apiResult ["message"] = "Diagnostic for categories ".(implode(",",$UsedTeacherCatBlank)).", cannot be blank as these are getting used in Step-2 against Teachers. To remove these diagnostic, please update/assign the other category against teachers in Step-2.\n";    
                                }
			} else {
				
				$this->apiResult ["message"] = "Please select diagnostic for atleast one teacher category\n";
			}
                        }else{
                          $this->apiResult ["message"] = "Not allowed to delete external reviewer\n";  
                        }
		}
	}
        
	function uploadFileInPartsAction() {
		
		// Make sure file is not cached (as it happens for example on iOS devices)
		header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		
		header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" );
		
		header ( "Cache-Control: no-store, no-cache, must-revalidate" );
		
		header ( "Cache-Control: post-check=0, pre-check=0", false );
		
		header ( "Pragma: no-cache" );
		
		@set_time_limit ( 10 * 60 );
		
		$maxFileAge = 5 * 3600;
		
		$allowedExtensions = array (
				"csv" 
		);
		
		$temp_path = ROOT . 'tmp' . DS . 'temp_upload';
		
		$cleanup_temp_path = true;
		
		if (! file_exists ( $temp_path )) {
			
			@mkdir ( $temp_path );
		}
		
		$fileName = uniqid ( "file_" );
		
		if (isset ( $_REQUEST ["name"] )) {
			
			$fileName = $_REQUEST ["name"];
		} else if (! empty ( $_FILES )) {
			
			$fileName = $_FILES ["file"] ["name"];
		}
		
		$extension = explode ( ".", $fileName );
		
		$extension = strtolower ( end ( $extension ) );
		
		if (! in_array ( $extension, $allowedExtensions )) {
			
			$this->apiResult ["message"] = "Invalid file type";
			
			return;
		}
		
		$filePath = $temp_path . DS . $fileName;
		
		$chunk = isset ( $_REQUEST ["chunk"] ) ? intval ( $_REQUEST ["chunk"] ) : 0;
		
		$chunks = isset ( $_REQUEST ["chunks"] ) ? intval ( $_REQUEST ["chunks"] ) : 0;
		
		if ($cleanup_temp_path) {
			
			if (! is_dir ( $temp_path ) || ! $dir = opendir ( $temp_path )) {
				
				$this->apiResult ["message"] = "Failed to open temp directory.";
				
				return;
			}
			
			while ( ($file = readdir ( $dir )) !== false ) {
				
				$tmpfilePath = $temp_path . DS . $file;
				
				if ($tmpfilePath == "{$filePath}.part") {
					
					continue;
				}
				
				if (preg_match ( '/\.part$/', $file ) && (filemtime ( $tmpfilePath ) < time () - $maxFileAge)) {
					
					@unlink ( $tmpfilePath );
				}
			}
			
			closedir ( $dir );
		}
		
		if (! $out = @fopen ( "{$filePath}.part", $chunks ? "ab" : "wb" )) {
			
			$this->apiResult ["message"] = "Failed to open output stream.";
			
			return;
		}
		
		if (! empty ( $_FILES )) {
			
			if ($_FILES ["file"] ["error"] || ! is_uploaded_file ( $_FILES ["file"] ["tmp_name"] )) {
				
				$this->apiResult ["message"] = "Failed to move uploaded file.";
				
				return;
			}
			
			if (! $in = @fopen ( $_FILES ["file"] ["tmp_name"], "rb" )) {
				
				$this->apiResult ["message"] = "Failed to open input stream.";
				
				return;
			}
		} else {
			
			if (! $in = @fopen ( "php://input", "rb" )) {
				
				$this->apiResult ["message"] = "Failed to open input stream.";
				
				return;
			}
		}
		
		while ( $buff = fread ( $in, 4096 ) ) {
			
			fwrite ( $out, $buff );
		}
		
		@fclose ( $out );
		
		@fclose ( $in );
		
		$this->apiResult ["status"] = 1;
		
		if (! $chunks || $chunk == $chunks - 1) {
			
			rename ( "{$filePath}.part", $filePath );
			
			$this->apiResult ["message"] = "File successfully uploaded.";
			
			if (! empty ( $_POST ['actionAfterUpload'] )) {
				
				switch ($_POST ['actionAfterUpload']) {
					
					case 'extractAssessorCSV' :
						
						try {
							
							$this->apiResult ['result'] = array (
									"content" => "",
									"error" => 0,
									"msg" => "" 
							);
							
							if ($extension != "csv") {
								
								$this->apiResult ['result'] ["error"] = 1;
								
								$this->apiResult ['result'] ["msg"] = "Invalid file type";
							} else {
								
								$file = fopen ( $filePath, "r" );
								
								$i = 0;
								$ci=0;
                                                                $error=false;
								while ( ! feof ( $file ) ) 

								{
									
									$i ++;
									
									$fileData = fgetcsv ( $file, 0 );
									
									if ($i == 1 && ! empty ( $fileData [2] ) && preg_match ( '/^[A-Za-z0-9\._-]+@[A-Za-z0-9]+\.[A-Za-z]+$/', $fileData [2] ) != 1) {
										
										continue;
									}
									if(isset ( $fileData [1] ) && !empty(trim($fileData [1])) && isset ( $fileData [2] ) && !empty(trim($fileData [2])) ){
									if(isset($fileData [3]) && !empty($fileData [3])){
                                                                        if (strpos($fileData [3],'/')>-1){
                                                                        list($mm,$dd,$yyyy) = explode('/',$fileData [3]);
                                                                        }else if(strpos($fileData [3],'-')>-1){
                                                                        list($dd,$mm,$yyyy) = explode('-',$fileData [3]);    
                                                                        }else{
                                                                        list($dd,$mm,$yyyy) = explode('-',$fileData [3]);    
                                                                        }
                                                                        if (!checkdate($mm,$dd,$yyyy)) {
                                                                         $error = true;
                                                                        }
                                                                        }
                                                                        
                                                                        if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $fileData [0] ) != 1) {
                                                                         $this->apiResult ['result'] ["error"] = 1;
							                 $this->apiResult ['result']["msg"] = "Invalid first name.  Please remove the special characters at row ".$i."\n";
                                                                         return;
						                        } else if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $fileData [1] ) != 1) {
                                                                         $this->apiResult ['result'] ["error"] = 1;   
							                 $this->apiResult ['result']["msg"] = "Invalid last name.  Please remove the special characters at row ".$i."\n";
                                                                         return;
						                        }
                                                                        
                                                                        $this->apiResult ['result'] ["content"] .= assessmentModel::getTeacherAssessorHTMLRow ( 0, isset ( $fileData [0] ) ? $fileData [0] : '', isset ( $fileData [1] ) ? $fileData [1] : '', isset ( $fileData [2] ) ? $fileData [2] : '', (isset ( $fileData [3] ) && !empty($fileData [3])) ? date("d-m-Y",strtotime("".$yyyy."-".$mm."-".$dd."")) : '', 1 );
                                                                        $ci++;     
                                                                        }
                                                                 }
                                                                
								if($ci==0){
                                                                //$this->apiResult ['result'] ["error"] = 1;
								//$this->apiResult ['result'] ["msg"] = "Check the data format of uploaded file";  
                                                                }
                                                                if($error){
                                                                $this->apiResult ['result'] ["error"] = 1;
                                                                $this->apiResult ['result'] ["msg"] = "Please enter a valid date in the format - MM/DD/YYYY";
                                                                }
								fclose ( $file );
							}
						} catch ( Exception $e ) {
							
							$this->apiResult ['result'] ["error"] = 1;
							
							$this->apiResult ['result'] ["msg"] = "Error while parsing the file";
						}
						
						break;
					
					case 'extractTeacherCSV' :
						
						$diagnosticModel = new diagnosticModel ();
						
						try {
							
							$this->apiResult ['result'] = array (
									"content" => "",
									"error" => 0,
									"msg" => "" 
							);
							
							if (empty ( $_POST ['taid'] ) || ! $teacherAssessment = $diagnosticModel->getGroupAssessmentByGAId ( $_POST ['taid'] )) {
								
								$this->apiResult ['result'] ["error"] = 1;
								
								$this->apiResult ['result'] ["msg"] = "Invalid Teacher Review ID";
							} else if ($extension != "csv") {
								
								$this->apiResult ['result'] ["error"] = 1;
								
								$this->apiResult ['result'] ["msg"] = "Invalid file type";
							} else {
								
								$assessmentModel = new assessmentModel ();
								
								$file = fopen ( $filePath, "r" );
								
								$i = 0;
								$ci=0;
								while ( ! feof ( $file ) ) 

								{
									
									$i ++;
									
									if ($i == 1 && ! empty ( $fileData [2] ) && preg_match ( '/^[A-Za-z0-9\._-]+@[A-Za-z0-9]+\.[A-Za-z]+$/', $fileData [2] ) != 1) {
										
										continue;
									}
									
									$fileData = fgetcsv ( $file, 0 );
									if(isset ( $fileData [1] ) && !empty(trim($fileData [1])) && isset ( $fileData [2] ) && !empty(trim($fileData [2])) ){
									if(isset($fileData [3]) && !empty($fileData [3])){
                                                                        if (strpos($fileData [3],'/')>-1){
                                                                        list($mm,$dd,$yyyy) = explode('/',$fileData [3]);
                                                                        }else if(strpos($fileData [3],'-')>-1){
                                                                        list($dd,$mm,$yyyy) = explode('-',$fileData [3]);    
                                                                        }else{
                                                                        list($dd,$mm,$yyyy) = explode('-',$fileData [3]);    
                                                                        }
                                                                        if (!checkdate($mm,$dd,$yyyy)) {
                                                                         $error = true;
                                                                        }
                                                                        }
                                                                        
                                                                        if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $fileData [0] ) != 1) {
                                                                         $this->apiResult ['result'] ["error"] = 1;
							                 $this->apiResult ['result']["msg"] = "Invalid first name. Please remove the special characters at row ".$i."\n";
                                                                         return;
						                        } else if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $fileData [1] ) != 1) {
                                                                         $this->apiResult ['result'] ["error"] = 1;   
							                 $this->apiResult ['result']["msg"] = "Invalid last name. Please remove the special characters at row ".$i."\n";
                                                                         return;
						                        }
                                                                        
                                                                        $this->apiResult ['result'] ["content"] .= $assessmentModel->getTeacherInTeacherAssessmentHTMLRow ( $_POST ['taid'], 0, isset ( $fileData [0] ) ? $fileData [0] : '', isset ( $fileData [1] ) ? $fileData [1] : '', isset ( $fileData [2] ) ? $fileData [2] : '', (isset ( $fileData [3] ) && !empty($fileData [3])) ? date("d-m-Y",strtotime("".$yyyy."-".$mm."-".$dd."")) : '',0 ,0, 0, 0, 1 );
                                                                        
                                                                        $ci++;    
                                                                        }
                                                                }
								
								fclose ( $file );
                                                                if($ci==0){
                                                                //$this->apiResult ['result'] ["error"] = 1;
								//$this->apiResult ['result'] ["msg"] = "Check the data format of uploaded file";  
                                                                }
							}
						} catch ( Exception $e ) {
							
							$this->apiResult ['result'] ["error"] = 1;
							
							$this->apiResult ['result'] ["msg"] = "Error while parsing the file";
						}
						
						break;
                                                
                                            case 'extractStudentCSV' :
						
						$diagnosticModel = new diagnosticModel ();
						
						try {
							
							$this->apiResult ['result'] = array (
									"content" => "",
									"error" => 0,
									"msg" => "" 
							);
							
							if (empty ( $_POST ['taid'] ) || ! $teacherAssessment = $diagnosticModel->getGroupAssessmentByGAId ( $_POST ['taid'] )) {
								
								$this->apiResult ['result'] ["error"] = 1;
								
								$this->apiResult ['result'] ["msg"] = "Invalid Student Review ID";
							} else if ($extension != "csv") {
								
								$this->apiResult ['result'] ["error"] = 1;
								
								$this->apiResult ['result'] ["msg"] = "Invalid file type";
							} else {
								
								$assessmentModel = new assessmentModel ();
								
								$file = fopen ( $filePath, "r" );
								
								$i = 0;
								$ci=0;
								while ( ! feof ( $file ) ) 

								{
									
									$i ++;
									
									if ($i == 1 && ! empty ( $fileData [2] ) && preg_match ( '/^[A-Za-z0-9\._-]+@[A-Za-z0-9]+\.[A-Za-z]+$/', $fileData [2] ) != 1) {
										
										continue;
									}
									
									$fileData = fgetcsv ( $file, 0 );
									if(isset ( $fileData [1] ) && !empty(trim($fileData [1])) && isset ( $fileData [2] ) && !empty(trim($fileData [2])) ){
									if(isset($fileData [3]) && !empty($fileData [3])){
                                                                        if (strpos($fileData [3],'/')>-1){
                                                                        list($mm,$dd,$yyyy) = explode('/',$fileData [3]);
                                                                        }else if(strpos($fileData [3],'-')>-1){
                                                                        list($dd,$mm,$yyyy) = explode('-',$fileData [3]);    
                                                                        }else{
                                                                        list($dd,$mm,$yyyy) = explode('-',$fileData [3]);    
                                                                        }
                                                                        if (!checkdate($mm,$dd,$yyyy)) {
                                                                         $error = true;
                                                                        }
                                                                        }
                                                                        
                                                                        if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $fileData [0] ) != 1) {
                                                                         $this->apiResult ['result'] ["error"] = 1;
							                 $this->apiResult ['result']["msg"] = "Invalid first name. Please remove the special characters at row ".$i."\n";
                                                                         return;
						                        } else if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $fileData [1] ) != 1) {
                                                                         $this->apiResult ['result'] ["error"] = 1;   
							                 $this->apiResult ['result']["msg"] = "Invalid last name. Please remove the special characters at row ".$i."\n";
                                                                         return;
						                        }
                                                                        
                                                                        $this->apiResult ['result'] ["content"] .= $assessmentModel->getStudentInStudentAssessmentHTMLRow ( $_POST ['taid'], 0, isset ( $fileData [0] ) ? $fileData [0] : '', isset ( $fileData [1] ) ? $fileData [1] : '', isset ( $fileData [2] ) ? $fileData [2] : '', (isset ( $fileData [3] ) && !empty($fileData [3])) ? date("d-m-Y",strtotime("".$yyyy."-".$mm."-".$dd."")) : '',0 ,0, 0, 1 );
                                                                        
                                                                        $ci++;    
                                                                        }
                                                                }
								
								fclose ( $file );
                                                                if($ci==0){
                                                                //$this->apiResult ['result'] ["error"] = 1;
								//$this->apiResult ['result'] ["msg"] = "Check the data format of uploaded file";  
                                                                }
							}
						} catch ( Exception $e ) {
							
							$this->apiResult ['result'] ["error"] = 1;
							
							$this->apiResult ['result'] ["msg"] = "Error while parsing the file";
						}
						
						break;    
				}
			}
			
			@unlink ( $filePath );
		} else {
			
			$this->apiResult ["message"] = "File chunk successfully uploaded";
		}
	}
        
        function getReviewerListforSchoolAction(){
            if (empty($_POST['grp_sch_id'])){			
                    $this->apiResult ["message"] = "Assessment id should not be blank.\n";
            }else{
                    $assessmentModel = new assessmentModel ();
                    $list=$assessmentModel->getReviewerListforSchool($_POST['grp_sch_id']);
                $external_reviewer=array();
                $internal_reviewer=array();
                foreach($list as $external_internal){
                if($external_internal['added_by_admin']==1){
                $external_reviewer[]=$external_internal;    
                }else{
                $internal_reviewer[]=$external_internal;    
                }
                
                }
                    $this->apiResult ["status"]=1;
                    $this->apiResult ["reviwerlist"] =$external_reviewer;
                    $this->apiResult ["internalreviwerlist"] =$internal_reviewer;
                 }
        }
        
	function saveTeacherAssessorsFormAction() {
		$diagnosticModel = new diagnosticModel ();
		
		$teacherAssessment = null;
		
		$isAdmin = in_array ( "create_assessment", $this->user ['capabilities'] );
		
		if (empty ( $_POST ['taid'] ) || ! ($teacherAssessment = $diagnosticModel->getGroupAssessmentByGAId ( $_POST ['taid'] ))) {
			
			$this->apiResult ["message"] = "Teacher review id is either empty or invalid\n";
		} /*else if ($teacherAssessment ['assessmentAssigned'] > 0) {
			
			$this->apiResult ["message"] = "Reviews already assigned\n";
		}*/ else if ($isAdmin || ($this->user ['client_id'] == $teacherAssessment ['client_id'] && ($teacherAssessment ['admin_user_id'] == $this->user ['user_id'] || in_array ( 6, $this->user ['role_ids'] ))) || ($this->user ['network_id'] == $teacherAssessment ['network_id'] && in_array ( "view_own_network_assessment", $this->user ['capabilities'] ))) {
			
			$assessmentModel = new assessmentModel ();
			
			$postedExtAssessors = isset ( $_POST ['eAssessor'] ) ? $_POST ['eAssessor'] : array ();
			
			$eSelectedAssessorsIds = array_keys ( $postedExtAssessors );
			
			$assessors = array (
					"add" => array (),
					"update" => array (),
					"rejectedCount" => 0,
					"count" => 0 
			);
			
			if (! empty ( $_POST ['teacherAssessors'] ) && ! empty ( $_POST ['teacherAssessors'] ['first_name'] )) {
				
				$l = count ( $_POST ['teacherAssessors'] ['first_name'] );
				
				$emails = array ();
				
				$timeNow = time ();
				
				for($i = 0; $i < $l; $i ++) {
					
					$fn = isset ( $_POST ['teacherAssessors'] ['first_name'] [$i] ) ? trim ( $_POST ['teacherAssessors'] ['first_name'] [$i] ) : '';
					
					$ln = isset ( $_POST ['teacherAssessors'] ['last_name'] [$i] ) ? trim ( $_POST ['teacherAssessors'] ['last_name'] [$i] ) : '';
					
					$em = isset ( $_POST ['teacherAssessors'] ['email'] [$i] ) ? trim ( $_POST ['teacherAssessors'] ['email'] [$i] ) : '';
					
					$jd = isset ( $_POST ['teacherAssessors'] ['doj'] [$i] ) ? trim ( $_POST ['teacherAssessors'] ['doj'] [$i] ) : '';
					
					if ($fn == "" && $ln == "" && $em == "" && $jd == "") {
						
						continue;
					} else if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $fn ) != 1) {
						
						$this->apiResult ["message"] = "Invalid or empty first name in row " . ($i + 1) . "\n";
						
						return;
					} else if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $ln ) != 1) {
						
						$this->apiResult ["message"] = "Invalid or empty last name in row " . ($i + 1) . "\n";
						
						return;
					} else if ($em == "" && $jd == "") {
						
						$this->apiResult ["message"] = "Email and DoJ both can't be empty in row " . ($i + 1) . "\n";
						
						return;
					} else if ($em != "" && preg_match ( '^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^', $em ) != 1) {
						
						$this->apiResult ["message"] = "Invalid email in row " . ($i + 1) . "\n";
						
						return;
					} else if ($jd != "" && preg_match ( '/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[0-9]|1[0-2])-(19|20)[0-9]{2}$/', $jd ) != 1) {
						
						$this->apiResult ["message"] = "Invalid DoJ in row " . ($i + 1) . "\n";
						
						return;
					}
					
					$dArr = explode ( "-", $jd );
					
					if (count ( $dArr ) == 3 && mktime ( 0, 0, 0, $dArr [1], $dArr [0], $dArr [2] ) > $timeNow) {
						
						$this->apiResult ["message"] = "DoJ can't be greater than today in row " . ($i + 1) . "\n";
						
						return;
					}
					
					if ($em == "") {
						
						$em = str_replace ( "-", "", $jd ) . "@" . str_replace ( " ", "", $fn ) . "." . str_replace ( " ", "", $ln );
					}
					
					$em = strtolower ( $em );
					
					if (in_array ( $em, $emails )) {
						
						$this->apiResult ["message"] = "Email $em used twice\n";
						
						return;
					} else {
						
						$emails [] = $em;
					}
					
					$c = array (
							"fn" => $fn,
							"ln" => $ln,
							"em" => $em,
							"jd" => $jd,
							"sn" => $i + 1 
					);
					
					$u = $this->userModel->getUserByEmailWithGA ( $em, $_POST ['taid'] );
					
					if (empty ( $u )) {
						
						$assessors ['add'] [] = $c;
					} else {
						
						if ($u ['aqs_status_id'] == 3)
							
							$assessors ['rejectedCount'] ++;
						
						if ($u ['inGivenGA'] && ! in_array ( $u ['user_id'], $eSelectedAssessorsIds )) {
							
							$u ['inGivenGA'] = 0;
						}
						
						$assessors ['update'] [] = array (
								"new" => $c,
								"cur" => $u 
						);
					}
					
					$assessors ['count'] ++;
				}
			}
			
			$this->apiResult ["needConfirmation"] = array ();
			
			if ($assessors ['rejectedCount'] > 0 && empty ( $_POST ['proceedWithRejected'] )) {
				
				foreach ( $assessors ['update'] as $ea ) {
					
					if ($ea ['cur'] ['aqs_status_id'] == 3) {
						
						$this->apiResult ["needConfirmation"] [] = $ea ['new'];
					}
				}
				
				$this->apiResult ["status"] = 1;
				
				return;
			}
			
			$associativeAssessors = $assessmentModel->getExternalAssessorsInGroupAssessment ( $_POST ['taid'], 0 );
			
			$associativeAssessors_ids = array ();
			
			foreach ( $associativeAssessors as $a )
				
				$associativeAssessors_ids [] = $a ['user_id'];
			
			$this->db->start_transaction ();
			$currentUser=isset($this->user ['user_id'])?$this->user['user_id']:NULL;
			$queryfailed = 0;
			$reviewers_data=$assessmentModel->getAllUsedDiagnostics($_POST ['taid'],2);
                        $used_reviewers=explode(",",$reviewers_data['all_validators']);
			foreach ( $associativeAssessors as $a ) {
				
				if (in_array ( $a ['user_id'], $eSelectedAssessorsIds )) {
					
					if ($isAdmin && $postedExtAssessors [$a ['user_id']] != $a ['original_status_id'] && ! $this->userModel->updateUser ( $a ['user_id'], $a ['name'], '', 0, $postedExtAssessors [$a ['user_id']],date("Y-m-d H:i:s"),$currentUser)) {
						
						$queryfailed = 1;
					}
				} else if (!in_array($a['user_id'], $used_reviewers) && ! $assessmentModel->removeExternalAssessorFromGroupAssessment ( $_POST ['taid'], $a ['user_id'] )) {
					
					$queryfailed = 5;
				}
                                
			}
			
			if ($assessors ['count'] > 0) {
				
				foreach ( $assessors ['add'] as $na ) {
					
					$uid = $this->userModel->createUser ( $na ['em'], $na ['em'] . "@123", $na ['fn'] . " " . $na ['ln'], $teacherAssessment ['client_id'], $isAdmin ? 1 : 2,date("Y-m-d H:i:s"),$currentUser );
					
					if (! $uid || ! $this->userModel->addUserRole ( $uid, 3 ) || ! $assessmentModel->addExternalAssessorToGroupAssessment ( $_POST ['taid'], $uid, $isAdmin ? 1 : 0 ) || ($na ['jd'] != '' && ! $assessmentModel->addTeacherAttributeValue ( $uid, $na ['jd'], 'doj' ))) {
						
						$queryfailed = 10;
					}
				}
				
				foreach ( $assessors ['update'] as $ea ) {
					
					if (! $ea ['cur'] ['inGivenGA'] && ! $assessmentModel->addExternalAssessorToGroupAssessment ( $_POST ['taid'], $ea ['cur'] ['user_id'], $isAdmin ? 1 : 0 )) {
						
						$queryfailed = 15;
					}
					
					$status = $isAdmin || $ea ['cur'] ['aqs_status_id'] == 1 ? 1 : 2;
					
					if ($ea ['cur'] ['client_id'] == $teacherAssessment ['client_id']) {
						
						if (! $this->userModel->updateUser ( $ea ['cur'] ['user_id'], $ea ['new'] ['fn'] . " " . $ea ['new'] ['ln'], '', 0, $status,date("Y-m-d H:i:s"),$currentUser ) || ($ea ['new'] ['jd'] != '' && ! $assessmentModel->updateTeacherAttributeValue ( $ea ['cur'] ['user_id'], $ea ['new'] ['jd'], 'doj' ))) {
							
							$queryfailed = 20;
						}
					} else {
						
						if (! $this->userModel->updateUser ( $ea ['cur'] ['user_id'], $ea ['cur'] ['name'], '', 0, $status,date("Y-m-d H:i:s"),$currentUser )) {
							
							$queryfailed = 25;
						}
					}
				}
			}
			
			if ($queryfailed == 0 && $this->db->commit ()) {
				
				$this->apiResult ["status"] = 1;
                                $this->apiResult ["add_type"] = "saveTeacherAssessorsForm";
				
				$this->apiResult ["content"] = array (
						"1-1" => "",
						"1-0" => "",
						2 => "",
						3 => "" 
				);
				
				$assessors_group = $assessmentModel->getExternalAssessorsInGroupAssessment ($_POST ['taid']);
				
                                $diagnostics_data=$assessmentModel->getAllUsedDiagnostics($_POST ['taid'],2);
                                $used_reviewers=explode(",",$diagnostics_data['all_validators']);
				foreach ( $assessors_group as $status_id => $assessors ) {
					
					foreach ( $assessors as $assessor ) {
						
						$this->apiResult ["content"] [$status_id . ($status_id == 1 ? '-' . $assessor ['added_by_admin'] : '')] .= userModel::getExternalAssessorNodeHtml ( $assessor, (($assessor ['added_by_admin']==1 && !$isAdmin) || in_array($assessor['user_id'],$used_reviewers))?0:1, $status_id );
					}
				}
			} else {
				
				$this->apiResult ["message"] = "Unable to process your request \nError Code:$queryfailed";
				
				$this->db->rollback ();
			}
		} else {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		}
	}
	function saveTeachersForAssessmentFormAction() {
		$diagnosticModel = new diagnosticModel ();
		$r_type="";
                if(isset($_POST['r_type']) && $_POST['r_type']=="student"){
                $r_type=$_POST['r_type'];    
                }
		$teacherAssessment = null;
                $this->apiResult ["add_type"] = "saveTeachersForAssessmentForm";
		$isAdmin = in_array ( "create_assessment", $this->user ['capabilities'] );
                
		if (empty ( $_POST ['taid'] ) || ! ($teacherAssessment = $diagnosticModel->getGroupAssessmentByGAId ( $_POST ['taid'] ))) {
			
			$this->apiResult ["message"] = "Review id is either empty or invalid\n";
		} /*else if ($teacherAssessment ['assessmentAssigned'] > 0) {
			
			$this->apiResult ["message"] = "Assessments already assigned\n";
		}*/ else if ($isAdmin || ($this->user ['client_id'] == $teacherAssessment ['client_id'] && ($teacherAssessment ['admin_user_id'] == $this->user ['user_id'] || in_array ( 6, $this->user ['role_ids'] ))) || ($this->user ['network_id'] == $teacherAssessment ['network_id'] && in_array ( "view_own_network_assessment", $this->user ['capabilities'] ))) {
			
			$assessorsNotApproved = array ();
			
			$assessmentModel = new assessmentModel ();
			
			$existingTeachers = $assessmentModel->getTeachersInTeacherAssessment ( $_POST ['taid'] );
                        
                        //print_r($teacherAssessment);
                        $teachers_list=array();
                        if($teacherAssessment['student_round']==2 && $r_type=="student"){
                        $groupdata=$assessmentModel->getGAIdfromClientandRound($teacherAssessment ['client_id'],1);
                        $gaid=$groupdata['group_assessment_id'];
                        $existingTeachers_r1 = $assessmentModel->getTeachersInTeacherAssessment ( $gaid );
			
                        //$existingTeachers=$this->db->array_col_to_key ($existingTeachers,'teacher_id');
                        
                        $existingTeachers_r1=$this->db->array_col_to_key ($existingTeachers_r1,'email');
                        
                        //print_r($existingTeachers_r1);
                         
                           
                        }
                        
                        
                        
                        
			$t_assessor_list = $this->db->array_col_to_key ( $assessmentModel->getExternalAssessorsInGroupAssessment ( $_POST ['taid'], 0, array (
					1,
					2 
			) ), "user_id" );
			
			$t_cat_ids = $this->db->array_col_to_array ( $assessmentModel->getTeacherCategoryListForTchrAsmnt ( $_POST ['taid'] ), "teacher_category_id" );
			
			$assessorIds = array_keys ( $t_assessor_list );
			
			$teacherIdsToDelete = array ();
			
			$teachersToUpdate = array ();
			
			$teachersToAdd = array ();
			
			$emails = array ();
                        $can_names = array ();
			
			$submit = isset ( $_POST ['submit'] ) && $_POST ['submit'] == 1;
			
			$teacherCount = 0;
			
			$i = 0;
			
			$timeNow = time ();
                        
			if (! empty ( $_POST ['teachers'] ['old'] )) {
				
				$postedTeacherIds = array_keys ( $_POST ['teachers'] ['old'] );
				
				$i = count ( $postedTeacherIds );
				
				foreach ( $existingTeachers as $et ) {
					
					if (isset ( $_POST ['teachers'] ['old'] [$et ['teacher_id']] )) {
						
						$c = array_search ( $et ['teacher_id'], $postedTeacherIds ) + 1;
						
						$t = $_POST ['teachers'] ['old'] [$et ['teacher_id']];
						
						if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $t ['first_name'] ) != 1) {
							
							$this->apiResult ["message"] = "Invalid or empty first name in row $c \n";
							
							return;
						} else if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $t ['last_name'] ) != 1) {
							
							$this->apiResult ["message"] = "Invalid or empty last name in row $c \n";
							
							return;
						} else if ($t ['doj'] != "" && preg_match ( '/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[0-9]|1[0-2])-(19|20)[0-9]{2}$/', $t ['doj'] ) != 1) {
							
							$this->apiResult ["message"] = "Invalid DoJ in row $c \n";
							
							return;
						} else if(empty($t ['department'])){
                                                    
                                                    $this->apiResult ["message"] = "Please select a department in row $c \n";
							
						    return;
                                                    
                                                }else if (! in_array ( $t ['assessor_id'], $assessorIds )) {
							
							$this->apiResult ["message"] = "Please select an reviewer in row $c \n";
							
							return;
						} else if ($t ['assessor_id'] == $et ['teacher_id']) {
							if($r_type=="student"){
                                                        $this->apiResult ["message"] = "Reviewer and student can't be same in row $c \n";    
                                                        }else{
							$this->apiResult ["message"] = "Reviewer and teacher can't be same in row $c \n";
                                                        }
							return;
						} else if (! in_array ( $t ['cat_id'], $t_cat_ids )) {
							if($r_type=="student"){
                                                        $this->apiResult ["message"] = "Please select a student category in row $c \n";    
                                                        }else{
							$this->apiResult ["message"] = "Please select a teacher category in row $c \n";
                                                        }
							return;
						}
						
						$dArr = explode ( "-", $t ['doj'] );
						
						if (count ( $dArr ) == 3 && mktime ( 0, 0, 0, $dArr [1], $dArr [0], $dArr [2] ) > $timeNow) {
							
							$this->apiResult ["message"] = "DoJ can't be greater than today in row $c \n";
							
							return;
						}
						
						if ($t_assessor_list [$t ['assessor_id']] ['status_id'] != 1) {
							
							$assessorsNotApproved [$c] = $t ['assessor_id'];
						}
						
						$t ['teacher_id'] = $et ['teacher_id'];
						
						$t ['inGroup'] = 1;
						
						$teachersToUpdate [] = $t;
						
						$emails [] = $t ['email'];
                                                $can_names[$t ['email']]['name']= "".$t ['first_name']." ".$t ['last_name']."";
						
						$teacherCount ++;
					} else {
						
						$teacherIdsToDelete [] = $et ['teacher_id'];
					}
				}
			}
			
			if (! empty ( $_POST ['teachers'] ['new'] )) {
				
				$l = isset ( $_POST ['teachers'] ['new'] ['first_name'] [0] ) ? count ( $_POST ['teachers'] ['new'] ['first_name'] ) : 0;
				
				for($j = 0; $j < $l; $j ++) {
					
					$i ++;
					
					$nt = array ();
					
					$nt ['first_name'] = isset ( $_POST ['teachers'] ['new'] ['first_name'] [$j] ) ? trim ( $_POST ['teachers'] ['new'] ['first_name'] [$j] ) : '';
					
					$nt ['last_name'] = isset ( $_POST ['teachers'] ['new'] ['last_name'] [$j] ) ? trim ( $_POST ['teachers'] ['new'] ['last_name'] [$j] ) : '';
					
					$nt ['email'] = isset ( $_POST ['teachers'] ['new'] ['email'] [$j] ) ? trim ( $_POST ['teachers'] ['new'] ['email'] [$j] ) : '';
					
					$nt ['doj'] = isset ( $_POST ['teachers'] ['new'] ['doj'] [$j] ) ? trim ( $_POST ['teachers'] ['new'] ['doj'] [$j] ) : '';
					
                                        $nt ['department'] = isset ( $_POST ['teachers'] ['new'] ['department'] [$j] ) ?  $_POST ['teachers'] ['new'] ['department'] [$j] : '';
                                        
					$nt ['assessor_id'] = isset ( $_POST ['teachers'] ['new'] ['assessor_id'] [$j] ) ? $_POST ['teachers'] ['new'] ['assessor_id'] [$j] : '';
					
					$nt ['cat_id'] = isset ( $_POST ['teachers'] ['new'] ['cat_id'] [$j] ) ? $_POST ['teachers'] ['new'] ['cat_id'] [$j] : '';
					
					if ($nt ['first_name'] == "" && $nt ['last_name'] == "" && $nt ['email'] == "" && $nt ['doj'] == "") {
						
						continue;
					} else if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $nt ['first_name'] ) != 1) {
						
						$this->apiResult ["message"] = "Invalid or empty first name in row $i \n";
						
						return;
					} else if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $nt ['last_name'] ) != 1) {
						
						$this->apiResult ["message"] = "Invalid or empty last name in row $i \n";
						
						return;
					} else if ($nt ['email'] == "" && $nt ['doj'] == "") {
						
						$this->apiResult ["message"] = "Email and DoJ both can't be empty in row $i \n";
						
						return;
					} else if ($nt ['email'] != "" && preg_match ( '^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^', $nt ['email'] ) != 1) {
						
						$this->apiResult ["message"] = "Invalid email in row $i \n";
						
						return;
					} else if ($nt ['doj'] != "" && preg_match ( '/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[0-9]|1[0-2])-(19|20)[0-9]{2}$/', $nt ['doj'] ) != 1) {
						
						$this->apiResult ["message"] = "Invalid DoJ in row $i \n";
						
						return;
					} else if (empty($nt ['department'])) {
						
						$this->apiResult ["message"] = "Please select a department in row $i \n";
						
						return;
					}else if (! in_array ( $nt ['assessor_id'], $assessorIds )) {
						
						$this->apiResult ["message"] = "Please select an reviewer in row $i \n";
						
						return;
					} else if (! in_array ( $nt ['cat_id'], $t_cat_ids )) {
						if($r_type=="student"){
                                                $this->apiResult ["message"] = "Please select a student category in row $i \n";    
                                                }else{
						$this->apiResult ["message"] = "Please select a teacher category in row $i \n";
                                                }
						return;
					}
					
					$dArr = explode ( "-", $nt ['doj'] );
					
					if (count ( $dArr ) == 3 && mktime ( 0, 0, 0, $dArr [1], $dArr [0], $dArr [2] ) > $timeNow) {
						
						$this->apiResult ["message"] = "DoJ can't be greater than today in row $i \n";
						
						return;
					}
					
					if ($t_assessor_list [$nt ['assessor_id']] ['status_id'] != 1) {
						
						$assessorsNotApproved [$i] = $nt ['assessor_id'];
					}
					
					if ($nt ['email'] == "") {
						
						$nt ['email'] = str_replace ( "-", "", $nt ['doj'] ) . "@" . str_replace ( " ", "", $nt ['first_name'] ) . "." . str_replace ( " ", "", $nt ['last_name'] );
					}
					
					$nt ['email'] = strtolower ( $nt ['email'] );
					
					if (in_array ( $nt ['email'], $emails )) {
						
						$this->apiResult ["message"] = "Email " . $nt ['email'] . " used twice\n";
						
						return;
					} else {
						
						$emails [] = $nt ['email'];
                                                $can_names[$nt ['email']]['name']= "".$nt ['first_name']." ".$nt ['last_name']."";
					}
					
					$u = $this->userModel->getUserByEmailWithTA ( $nt ['email'], $_POST ['taid'] );
					
					if (empty ( $u )) {
						
						$teachersToAdd [] = $nt;
					} else if ($nt ['assessor_id'] == $u ['user_id']) {
						if($r_type=="student"){
                                                $this->apiResult ["message"] = "Reviewer and student can't be same in row $i \n";    
                                                }else{
						$this->apiResult ["message"] = "Reviewer and teacher can't be same in row $i \n";
                                                }
						
						return;
                                                
					/*} else if ($u ['client_id'] == $this->user ['client_id']) {*/
                                         } else if ($u ['client_id'] == $teacherAssessment ['client_id']) {       
						
						$nt ['teacher_id'] = $u ['user_id'];
						
						$nt ['inGroup'] = 0;
						
						$teachersToUpdate [] = $nt;
					} else {
						
						$this->apiResult ["message"] = "The email id '" . $nt ['email'] . "' already exists in our database but for a different school.\n";
						
						return;
					}
					
					$teacherCount ++;
				}
			}
			
                        //print_r($can_names);
                        if($teacherAssessment['student_round']==2 && $r_type=="student"){
                            
                        $result_teacher_diff=array_diff_key($can_names,$existingTeachers_r1);
                        
                        //print_r($result_teacher_diff);
                        
                        if(count($result_teacher_diff)>0){
                         foreach($result_teacher_diff as $key_t=>$val_t){
                          $teachers_list[]="".$val_t['name']." (".$key_t.")";    
                         }
                         
                        }
                        
                        if($teacherAssessment['student_round']==2 && count($teachers_list)>0 && $r_type=="student"){
                         $this->apiResult ["message"] = "Please remove following students, not present in Round-1:\n".implode(",\n",$teachers_list)."";
                         return;    
                        }
                        
                        }
                        
			$this->db->start_transaction ();
			
			$queryfailed = 0;
			
			foreach ( $teacherIdsToDelete as $id ) {
                                if($r_type=="student"){
                                $teacherstatus=$assessmentModel->getStudentStatus($_POST ['taid'],$id);
                                }else{        
                                $teacherstatus=$assessmentModel->getTeacherStatus($_POST ['taid'],$id);
                                }
                                $to_delete=1;
                                if(($teacherAssessment ['assessmentAssigned'] >0) && (!empty($teacherstatus['teacher_data_id']) || $teacherstatus['internalreview']>0 || $teacherstatus['exreview']>0)){
                                $to_delete=0;     
                                }
				if($to_delete){
				if (! $assessmentModel->removeTeacherFromTeacherAssessment ( $_POST ['taid'], $id ,$teacherAssessment ['assessmentAssigned']))
					
					$queryfailed = 10;
                                }
			}
			$currentUser=isset($this->user ['user_id'])?$this->user['user_id']:NULL;
			foreach ( $teachersToAdd as $nt ) {
				
				$uid = $this->userModel->createUser ( $nt ['email'], $nt ['email'] . "@123", $nt ['first_name'] . " " . $nt ['last_name'], $teacherAssessment ['client_id'],0,date("Y-m-d H:i:s"),$currentUser);
				
				if (! $uid || ! $this->userModel->addUserRole ( $uid, 3 ) || ! $assessmentModel->addTeacherToTeacherAssessment ( $_POST ['taid'], $uid, $nt ['cat_id'], $nt ['assessor_id'], $nt ['department'], $teacherAssessment ['assessmentAssigned'] ) || ($nt ['doj'] != '' && ! $assessmentModel->addTeacherAttributeValue ( $uid, $nt ['doj'], 'doj' ))) {
					
					$queryfailed = 20;
				}
			}
			
			foreach ( $teachersToUpdate as $t ) {
				
				if ($t ['inGroup'] == 0 && ! $assessmentModel->addTeacherToTeacherAssessment ( $_POST ['taid'], $t ['teacher_id'], $t ['cat_id'], $t ['assessor_id'], $t ['department'], $teacherAssessment ['assessmentAssigned'] )) {
					
					$queryfailed = 30;
				} else if ($t ['inGroup'] == 1 && ! $assessmentModel->updateTeacherInTeacherAssessment ( $_POST ['taid'], $t ['teacher_id'], $t ['cat_id'], $t ['assessor_id'], $t ['department'],$teacherAssessment ['assessmentAssigned'])) {
					
					$queryfailed = 35;
				}
				
				if (! $this->userModel->updateUser ( $t ['teacher_id'], $t ['first_name'] . " " . $t ['last_name'], '', 0, -1, date("Y-m-d H:i:s"), $currentUser) || (! $assessmentModel->updateTeacherAttributeValue ( $t ['teacher_id'], $t ['doj'], 'doj' ))) {
					
					$queryfailed = 40;
				}
			}
			
			if ($queryfailed == 0 && $this->db->commit ()) {
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["message"] = "Saved successfully";
				
				$submitted = 0;
				
				if ($submit && count ( $assessorsNotApproved ) == 0 && $teacherCount > 0) {
					
					$res = $assessmentModel->assignTeacherAssessment ( $_POST ['taid'] );
					
					if (! empty ( $res ['cnt'] )) {
						
						$submitted = 1;
						
						$this->apiResult ["message"] = "Reviews created successfully";
					} else {
						
						$this->apiResult ["message"] = "Saved successfully but unable to submit";
					}
				}
				
				$this->apiResult ["content"] = '';
				
				$teachers = $assessmentModel->getTeachersInTeacherAssessment ( $_POST ['taid'] );
				
				$i = 0;
				
				foreach ( $teachers as $teacher ) {
					
					$i ++;
					
					$nm = explode ( " ", $teacher ['name'], 2 );
					if($r_type=="student"){
                                        $this->apiResult ["content"] .= $assessmentModel->getStudentInStudentAssessmentHTMLRow ( $teacher ['group_assessment_id'], $i, $nm [0], isset ( $nm [1] ) ? $nm [1] : '', $teacher ['email'], $teacher ['doj'] ,$teacher ['teacher_category_id'], $teacher ['assessor_id'], $teacher ['teacher_id'], ! $submitted, ! $submitted );
                                            
                                        }else{
					$this->apiResult ["content"] .= $assessmentModel->getTeacherInTeacherAssessmentHTMLRow ( $teacher ['group_assessment_id'], $i, $nm [0], isset ( $nm [1] ) ? $nm [1] : '', $teacher ['email'], $teacher ['doj'], $teacher ['school_level_id'] ,$teacher ['teacher_category_id'], $teacher ['assessor_id'], $teacher ['teacher_id'], ! $submitted, ! $submitted );
                                        }
				}
				
				$this->apiResult ["submitted"] = $submitted;
				
				$this->apiResult ["enableSubmit"] = count ( $assessorsNotApproved ) == 0 && count ( $teachers ) ? 1 : 0;
			} else {
				
				$this->apiResult ["message"] = "Unable to process your request \nError Code:$queryfailed";
				
				$this->db->rollback ();
			}
		} else {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		}
	}
        
        function updateDepartmentAction(){
           //print_r($_POST);
           $diagnosticModel=new diagnosticModel();
           $assessment=$diagnosticModel->getTeacherAssessmentReports($_POST['groupassessment_id'],$_POST['diagnostic_id']);
           //print_r($assessment['school_level_ids']);
           $department_array=array();
           $i=0;
           foreach($assessment['school_level_ids'] as $key=>$val){
             $department_array[$i]['department_id']=  $key;
             $department_array[$i]['department']=  $val;
             $i++;
           }
           //print_r($department_array);
           $this->apiResult ["department"]=$department_array;
           $this->apiResult ["status"] = 1; 
        }
        
	function getAssessmentOfGrpAssAction() {
		$diagnosticModel = new diagnosticModel ();
		
		$groupAssessment = null;
		
		if (empty ( $_POST ['gaid'] )) {
			
			$this->apiResult ["message"] = "Group Teacher ID is empty.\n";
		} else if ($groupAssessment = $diagnosticModel->getGroupAssessmentByGAId ( $_POST ['gaid'] )) {
			
			$uid = $this->user ['user_id'];
			
			$assessmentListRowHelper = new assessmentListRowHelper ( $this->user );
			
			if ($assessmentListRowHelper->isAdmin || ($this->user ['client_id'] == $groupAssessment ['client_id'] && ($assessmentListRowHelper->isSchoolAdmin || $assessmentListRowHelper->isPrincipal)) || ($this->user ['network_id'] == $groupAssessment ['network_id'] && $assessmentListRowHelper->isNetworkAdmin)) {
				
				$uid = 0;
			}
			
			$assessmentModel = new assessmentModel ();
			
			$assessments = $assessmentModel->getAssessmentsInGroupAssessment ( $_POST ['gaid'], $uid );
			
			if (count ( $assessments )) {
				
				$this->apiResult ["content"] = '';
				
				foreach ( $assessments as $a ) {
					
					$this->apiResult ["content"] .= $assessmentListRowHelper->printBodyRow ( $a );
				}
				
				$this->apiResult ["gaid"] = $_POST ['gaid'];
				
				$this->apiResult ["status"] = 1;
			} else {
				
				$this->apiResult ["message"] = "No review found.\n";
			}
		} else {
			
			$this->apiResult ["message"] = "Invalid Group review ID.\n";
		}
	}
        
        
        
        function getActivityPostponedAction() {
		$actionModel = new actionModel ();
		
		
		if (empty ( $_POST ['id'] )) {
			
			$this->apiResult ["message"] = "ID is empty.\n";
		} else if ($postponed = $actionModel->getActivityPostponedAction2 ( $_POST ['id'] )) {
			
			//$uid = $this->user ['user_id'];
			
                        //$this->apiResult ["message"] = "Invalid  Activity ID.\n";
                        
                        if (count ( $postponed )) {
				
				$this->apiResult ["content"] = '';
				
				foreach ( $postponed as $a ) {
					
					$this->apiResult ["content"] .= assessmentModel::getActionActivityViewRow ($a);
				}
				
				$this->apiResult ["id"] = $_POST ['id'];
				
				$this->apiResult ["status"] = 1;
			} else {
				
				$this->apiResult ["message"] = "No review found.\n";
			}
                        
		} else {
			
			$this->apiResult ["message"] = "Invalid  Activity ID.\n";
		}
	}
        
	function saveTchrInfoAction() {
		$diagnosticModel = new diagnosticModel ();
		
		$groupAssmt = null;
		
		if (empty ( $_POST ['assessment_id'] )) {
			
			$this->apiResult ["message"] = "Review ID is empty.\n";
		} else if (! $groupAssmt = $diagnosticModel->getGroupAssessmentByAssmntId ( $_POST ['assessment_id'] )) {
			
			$this->apiResult ["message"] = "No teacher review is bind this this Review ID.\n";
		} else if ($groupAssmt ['report_published'] == 1) {
			
			$this->apiResult ["message"] = "You can't update data after publishing reports\n";
		} else {
			
			$teacher_id = $groupAssmt ['user_ids'] [0];
			
			$isAdmin = in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] );
			
			$assessmentModel = new assessmentModel ();
			
			$teacherInfo = $assessmentModel->getTeacherInfo ( $_POST ['assessment_id'] );
			
			if (($teacherInfo ['isTeacherInfoFilled'] ['value'] == 1 && ! $isAdmin) || ($teacherInfo ['isTeacherInfoFilled'] ['value'] != 1 && $teacher_id != $this->user ['user_id'])) {
				
				$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
				
				return;
			}
			
			$submit = ! empty ( $_POST ['submit'] ) || $teacherInfo ['isTeacherInfoFilled'] ['value'] == 1 ? 1 : 0;
			
			$fields = array (
					"name" => "Name",
					"designation" => "Designation",
					"mobile" => "Mobile No.",
					"qualification" => "Educational qualification",
					"experience" => "Total years of teaching experience in the current school",
                                        "other_experience" => "Total years of teaching experience other than current school",
					"joinning_year" => "School joining year",
					"position_when_joined" => "Position when joined the school",
					"no_of_promotions" => "No. of promotions since joining",
					"no_of_subjects_taught" => "No. of subjects taught",
					"no_of_classes_per_week" => "No. of classes taught per week" 
			);
			
			$complete = 1;
			
			$teacherDataIdToDelete = array_flip ( array_merge ( array_keys ( $teacherInfo ['supervisors'] ['value'] ), array_keys ( $teacherInfo ['other_roles'] ['value'] ) ) );
			
			$data = array ();
			//echo "<pre>";print_r($_POST);die;
                        if(isset($_POST ['tchrInfo']['mobile'])){
                            
                            $_POST ['tchrInfo']['mobile'] = "(+".$_POST ['tr_country_code'].")".$_POST ['tchrInfo']['mobile'];
                        }
			foreach ( $fields as $f => $label ) {
				
				if (! isset ( $_POST ['tchrInfo'] [$f] ) || trim ( $_POST ['tchrInfo'] [$f] ) == "") {
					
					if ($submit) {
						
						$this->apiResult ["message"] = 'Field "' . $label . '" is mandatory.';
						
						return;
					} else if ($teacherInfo [$f] ['teacher_data_id'] > 0) {
						
						$teacherDataIdToDelete [$teacherInfo [$f] ['teacher_data_id']] = 1;
					}
					
					$complete = 0;
				} else {
					
					$data [] = array (
							"value" => trim ( $_POST ['tchrInfo'] [$f] ),
							"id" => $teacherInfo [$f] ['attr_id'],
							"teacher_data_id" => $teacherInfo [$f] ['teacher_data_id'] 
					);
				}
			}
			
			if (! empty ( $_POST ['tchrInfo'] ['other_roles'] ) && count ( $_POST ['tchrInfo'] ['other_roles'] )) {
				
				foreach ( $_POST ['tchrInfo'] ['other_roles'] as $or ) {
					
					$or = trim ( $or );
					
					if (! empty ( $or )) {
						
						$row = array (
								"value" => $or,
								"id" => $teacherInfo ['other_roles'] ['attr_id'],
								"teacher_data_id" => 0 
						);
						
						if ($teacher_data_id = array_search ( $or, $teacherInfo ['other_roles'] ['value'], false )) {
							
							unset ( $teacherDataIdToDelete [$teacher_data_id] );
							
							$row ['teacher_data_id'] = $teacher_data_id;
						}
						
						$data [] = $row;
					}
				}
			}
			
			$allEmpty = 1;
			
			if (! empty ( $_POST ['tchrInfo'] ['supervisors'] ) && count ( $_POST ['tchrInfo'] ['supervisors'] )) {
				
				foreach ( $_POST ['tchrInfo'] ['supervisors'] as $sp ) {
					
					$sp = trim ( $sp );
					
					if (! empty ( $sp )) {
						
						$allEmpty = 0;
						
						$row = array (
								"value" => $sp,
								"id" => $teacherInfo ['supervisors'] ['attr_id'],
								"teacher_data_id" => 0 
						);
						
						if ($teacher_data_id = array_search ( $sp, $teacherInfo ['supervisors'] ['value'], false )) {
							
							unset ( $teacherDataIdToDelete [$teacher_data_id] );
							
							$row ['teacher_data_id'] = $teacher_data_id;
						}
						
						$data [] = $row;
					}
				}
			}
			
			if ($allEmpty) {
				
				if ($submit) {
					
					$this->apiResult ["message"] = 'Field "Supervisor" is mandatory.';
					
					return;
				}
				
				$complete = 0;
			}
			
			if (empty ( $_POST ['tchrInfo'] ['email'] ['value'] )) {
				
				$teacher = $this->userModel->getUserById ( $teacher_id );
				
				$data [] = array (
						"value" => $teacher ['email'],
						"id" => $teacherInfo ['email'] ['attr_id'],
						"teacher_data_id" => $teacherInfo ['email'] ['teacher_data_id'] 
				);
			}
			
			$this->db->start_transaction ();
			
			$queryFailed = 0;
			
			foreach ( $data as $row ) {
				
				if (! ($row ['teacher_data_id'] > 0 ? $assessmentModel->updateTeacherInfoData ( $row ['teacher_data_id'], $row ['value'] ) : $assessmentModel->addTeacherInfoData ( $teacher_id, $row ['id'], $_POST ['assessment_id'], $row ['value'] ))) {
					
					$queryFailed = 1;
				}
			}
			
			foreach ( $teacherDataIdToDelete as $teacher_data_id => $z ) {
				
				if (! $assessmentModel->deleteTeacherInfoData ( $teacher_data_id ))
					
					$queryFailed = 10;
			}
			
			if ($submit && $complete) {
				
				if (! ($teacherInfo ['isTeacherInfoFilled'] ['teacher_data_id'] > 0 ? $assessmentModel->updateTeacherInfoData ( $teacherInfo ['isTeacherInfoFilled'] ['teacher_data_id'], 1 ) : $assessmentModel->addTeacherInfoData ( $teacher_id, 11, $_POST ['assessment_id'], 1 ))) {
					
					$queryFailed = 20;
				}
			}
			
			if ($queryFailed == 0 && $this->db->commit ()) {
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["message"] = "Saved successfully";
				
				$submitted = 0;
				
				if ($submit && $complete) {
					
					$this->apiResult ["message"] = "Submitted successfully";
				}
				
				$this->apiResult ["submitted"] = $submit && $complete ? 1 : 0;
				
				$this->apiResult ["enableSubmit"] = $complete;
			} else {
				
				$this->apiResult ["message"] = "Unable to process your request";
				
				$this->db->rollback ();
			}
		}
	}
	function saveDiagnosticKpaAction() {
                //print_r($_POST);
                //die();
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['type'] )) {
			
			$this->apiResult ["message"] = "Diagnostic type can not be empty\n";
		}else if (empty ( $_POST ['langId'] )) {
			$this->apiResult ["message"] = "Language can not be empty\n";
		} else if (empty ( $_POST ['assessmentId'] )) {
			
			$this->apiResult ["message"] = "Diagnostic review type can not be empty\n";
		} else if (empty ( $_POST ['diagnosticName'] )) {
			
			$this->apiResult ["message"] = "Diagnostic Name can not be empty\n";
		} else if (empty ( $_POST ['diagnostic_questions'] )) {
			
			$this->apiResult ["message"] = "Diagnostic questions can not be empty\n";
		} else {
			$diagnosticModel = new diagnosticModel ();
			// assessor recommendations
			$is_kpa_recommendations = empty ( $_POST ['kpa_recommendations'] ) ? 0 : $_POST ['kpa_recommendations'];
			$is_kq_recommendations = empty ( $_POST ['kq_recommendations'] ) ? 0 : $_POST ['kq_recommendations'];
			$is_cq_recommendations = empty ( $_POST ['cq_recommendations'] ) ? 0 : $_POST ['cq_recommendations'];
			$is_js_recommendations = empty ( $_POST ['js_recommendations'] ) ? 0 : $_POST ['js_recommendations'];
                        $diagnosticId=isset($_POST['diagnosticId'])?$_POST['diagnosticId']:0;
                        $langId=isset($_POST['langId'])?$_POST['langId']:0;
                        $parentId=isset($_POST['parentdiaId'])?$_POST['parentdiaId']:0;
                        $equivalenceId=isset($_POST['equivalenceId'])?$_POST['equivalenceId']:0;
                        $langIdOriginal=isset($_POST['langIdOriginal'])?$_POST['langIdOriginal']:0;
                        
                        $diagnosticLabels = array();                                               
                                        $diagnosticLabelsData = $diagnosticModel->getDiagnosticLabels($diagnosticId,$langId);
                                        foreach($diagnosticLabelsData as $data) {
                                            $diagnosticLabels[$data['label_key']] = $data['label_text'];
                        }
                        
                        if($parentId>0 && $equivalenceId>0 && $langIdOriginal>0){
                            
                                $assessmentId = $_POST ['assessmentId']; // ASSESSmenttypeid
				
				$diagnosticName = trim ( $_POST ['diagnosticName'] );
				
				// $diagnosticId = empty($_GET['diagnosticId'])?0:$_GET['diagnosticId'];
				
				$diagnosticId = empty ( $_POST ['diagnosticId'] ) ? 0 : $_POST ['diagnosticId'];
				
				$teacherCategoryId = empty ( $_POST ['teacherCategoryId'] ) ? 0 : $_POST ['teacherCategoryId'];
				
				$isPublished = 0;
				
				// $diagnosticId = 18;
				
				$flagNewKpas = 0; // flag set to 1 if any new kpas exist
				
				if ($assessmentId == 2 && ! $teacherCategoryId) {
					
					$this->apiResult ["message"] = "Diagnostic teacher type can not be empty\n";
					
					return;
				}
				
				$row = $diagnosticModel->getMinMaxLimitForDiagnosticQuestions ( 'kpa', $assessmentId );
				
				if (! $row) {
					
					$this->apiResult ["message"] = "Unable to process your request";
					
					return;
				} else if ((count ( $_POST ['diagnostic_questions'] ) > $row [0] ['limit_max'])) {
					
					$this->apiResult ["message"] = "Number of KPAs can't be more than " . $row [0] ['limit_max'];
					
					return;
				}
                                
                                
                                $blankKpas=array();
                                $existingKpas = array ();
                                $newKpas = array ();
                                if (! empty ( $_POST ['diagnostic_questions'] )) {
					
					foreach ( $_POST ['diagnostic_questions'] as $q ) :
						
						$q = explode ( '_', $q );
						
						$index = $q [0];
						
						$val = $q [1];
                                                if(empty($val)){
						$blankKpas[]=$val;
                                                }
                                                $existingKpas [$index] = $val;
					endforeach
					;
				}
                                
                                if(count($blankKpas)>0){
                                    $this->apiResult ["message"] = "All fields should be filled";
                                    return;
                                }
                            
                                $this->db->start_transaction ();
				
				$queryFailed = 0;
				
				$diagnosticExists = 1;
                                
                                if (isset ( $_POST ['dig_image_id'] ) && $_POST ['dig_image_id'] != '') {
				$imageId = $_POST ['dig_image_id'];
                                } else {
                                        $imageId = NULL;
                                }
                                $image_name = '';
                                if($diagnosticId>0){
                                if (! ($diagnosticModel->updateDiagnosticLang ( $diagnosticId, $diagnosticName, $isPublished, $langId ,$parentId,$this->user ['user_id'] )))
						$queryFailed = 1;
                                }
                                //$_POST ['diagnostic_questions_new']
                                if (! empty ( $_POST ['diagnostic_questions'] )) {
					
					foreach ( $_POST ['diagnostic_questions'] as $q ) :
						
						$q = explode ( '_', $q );
						
						$index = $q [0];
						
						$val = $q [1];
						$newKpas [$index] = $val;
					endforeach
					;
				}
                                
                                
                                $newKpas=count($newKpas)>0?$newKpas:NULL;
                                
				if ($newKpas) {
					
					$kpasCreated = $diagnosticModel->newLangKpa ( $newKpas, $langId);
					
					if (! $kpasCreated)
						$queryFailed = 20;
					
					$flagNewKpas = 1;
				}
                                
                                
                                if ($queryFailed == 0 && $this->db->commit ()) {
                                    
                                        
                //$this->set("diagnosticLabels", $diagnosticLabels);
					
					$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnosticLang ( $diagnosticId ,$langId ), "kpa_instance_id" );
					
					$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "kpa_instance_id", "key_question_instance_id" );
					
					$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "key_question_instance_id", "core_question_instance_id" );
					
					$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnosticLang ( $diagnosticId,$langId ), "core_question_instance_id", "judgement_statement_instance_id" );
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["diagnosticId"] = $diagnosticId;
					
					$this->apiResult ["message"] = "KPAs saved successfully";
					
					ob_start ();
					
					include (ROOT . 'application' . DS . 'views' . DS . "diagnostic" . DS . 'diagnostickpatabs.php');
					
					$this->apiResult ["content"] = ob_get_contents ();
					
					ob_end_clean ();
				} else {
					
					$this->apiResult ["message"] = "Unable to process your request";
					
					$this->db->rollback ();
				}
                                
                            
                            
                        }else{
                            
                        
			if (isset ( $_POST ['dig_image_id'] ) && $_POST ['dig_image_id'] != '') {
				$imageId = $_POST ['dig_image_id'];
			} else {
				$imageId = NULL;
			}
			$image_name = '';
			$file_error = FALSE;
			if (! empty ( $_FILES ['dig_image'] ['name'] )) {
				$maxUploadFileSize = 104857600; // in bytes
				$allowedExt = array (
						"jpeg",
						"png",
						"gif",
						"jpg" 
				);
				
				if ($_FILES ['dig_image'] ['error'] > 0) {
					$this->apiResult ["message"] = "File contains error or error occurred while uploading\n";
				} else if (! ($_FILES ['dig_image'] ['size'] > 0)) {
					$this->apiResult ["message"] = "Invalid file size or empty file\n";
				} else if ($_FILES ['dig_image'] ['size'] > $maxUploadFileSize) {
					$this->apiResult ["message"] = "File too big\n";
				} else {
					$nArr = explode ( ".", $_FILES ['dig_image'] ['name'] );
					$ext = strtolower ( array_pop ( $nArr ) );
					if (in_array ( $ext, $allowedExt ) && count ( $nArr ) > 0) {
						$newName = str_replace ( " ", "_", substr ( implode ( "_", $nArr ), 0, 35 ) ) . "_" . rand ( 1, 9999 ) . "_" . time () . "." . $ext;
						if (upload_file ( UPLOAD_PATH_DIAGNOSTIC . "" . $newName, $_FILES ['dig_image'] ['tmp_name'],true,'180','60' )) {
							//resizeImage ( UPLOAD_PATH_DIAGNOSTIC . "" . $newName, '180', '60' );
							if ($diagnosticId == 0) {
                                                                //print_r($_POST);
								$imageId = $diagnosticModel->addUploadedFile ( $newName, $this->user ['user_id'] );
							} else if($diagnosticId >0 && $imageId==NULL) {
                                                               $imageId = $diagnosticModel->addUploadedFile ( $newName, $this->user ['user_id'] );
	
                                                        }else {
								$resourceModel = new resourceModel ();
								$resourceModel->updateUploadedFile ( $newName, $imageId );
							}
							if ($imageId > 0 && 1==2) {
								$this->apiResult ["id"] = $imageId;
								$this->apiResult ["image_name"] = $image_name = $newName;
								$this->apiResult ["ext"] = $ext;
								$this->apiResult ["url"] = UPLOAD_URL_DIAGNOSTIC . "" . $newName;
							} else {
								$file_error = TRUE;
								$imageId = NULL;
								$this->apiResult ["message"] = "Unable to make entry in database";
								//@unlink ( UPLOAD_PATH_DIAGNOSTIC . "" . $newName );
                                                                deleteFile(UPLOAD_PATH_DIAGNOSTIC . "" . $newName);
							}
						} else {
							$file_error = TRUE;
							$this->apiResult ["message"] = "Error occurred while moving file\n ";
						}
					} else {
						$file_error = TRUE;
						$this->apiResult ["message"] = "Invalid file extension. Only " . implode ( ", ", $allowedExt ) . " type files are allowed\n";
					}
				}
			}
			if ($file_error == FALSE) {
				
				$assessmentId = $_POST ['assessmentId']; // ASSESSmenttypeid
				
				$diagnosticName = trim ( $_POST ['diagnosticName'] );
				
				// $diagnosticId = empty($_GET['diagnosticId'])?0:$_GET['diagnosticId'];
				
				$diagnosticId = empty ( $_POST ['diagnosticId'] ) ? 0 : $_POST ['diagnosticId'];
				
				$teacherCategoryId = empty ( $_POST ['teacherCategoryId'] ) ? 0 : $_POST ['teacherCategoryId'];
				
				$isPublished = 0;
				
				// $diagnosticId = 18;
				
				$flagNewKpas = 0; // flag set to 1 if any new kpas exist
				
				if ($assessmentId == 2 && ! $teacherCategoryId) {
					
					$this->apiResult ["message"] = "Diagnostic teacher type can not be empty\n";
					
					return;
				}
				
				$row = $diagnosticModel->getMinMaxLimitForDiagnosticQuestions ( 'kpa', $assessmentId );
				
				if (! $row) {
					
					$this->apiResult ["message"] = "Unable to process your request";
					
					return;
				} else if ((count ( $_POST ['diagnostic_questions'] ) > $row [0] ['limit_max'])) {
					
					$this->apiResult ["message"] = "Number of KPAs can't be more than " . $row [0] ['limit_max'];
					
					return;
				}
				
				$this->db->start_transaction ();
				
				$queryFailed = 0;
				
				$diagnosticExists = 1;
				
				if ($diagnosticId == 0) { // if diagnostic doesnt exist create it
					
					$diagnosticExists = 0;
					
					$userId = $this->user ['user_id'];
					
					$diagnosticId = $diagnosticModel->createDiagnostic ( $assessmentId, $diagnosticName, $isPublished, $userId, $imageId, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations, $langId );
					
					if (! $diagnosticId)
						$queryFailed = 1;
					
					if (! $diagnosticModel->createDiagnosticRatingScheme ( $diagnosticId, $assessmentId ))
						$queryFailed = 1;
					
					if ($assessmentId == 2 || $assessmentId == 4) {
						
                                                if($assessmentId == 4){
                                                $teacherCategoryId=$diagnosticModel->getStudentCatId();    
                                                }
						if (! $diagnosticModel->createDiagnosticTeacherCategory ( $diagnosticId, $teacherCategoryId ))
							$queryFailed = 2;
					}
				} else {
					if ($image_name == '') {
						$dig_image = $diagnosticModel->getDiagnosticImage ( $diagnosticId );
						$image_name = $dig_image [0] ['file_name'];
						$imageId = $dig_image [0] ['diagnostic_image_id'];
					}
					if (! ($diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, $imageId, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations, $langId,$this->user ['user_id'] )))
						$queryFailed = 1;
				}
				
				$existingKpas = array ();
				
				if (! empty ( $_POST ['diagnostic_questions_update'] )) {
					
					foreach ( $_POST ['diagnostic_questions_update'] as $q ) :
						
						$q = explode ( '_', $q );
						
						$index = $q [0];
						
						$val = $q [1];
						$existingKpas [$index] = $val;
					endforeach
					;
				}
				
				// add new kpas
				// print_r($_POST['diagnostic_questions_new']);
				
				$kpas = empty ( $_POST ['diagnostic_questions_new'] ) ? NULL : $_POST ['diagnostic_questions_new'];
				// print_r($kpas);
				
				if ($kpas) {
					
					$kpasCreated = $diagnosticModel->createKpa ( $kpas, $langId);
					
					if (! $kpasCreated)
						$queryFailed = 20;
					
					$flagNewKpas = 1;
				}
				
				// print_r($kpasCreated);
				// save kpa instances for diagnostic
				
				$kpaOrder = array ();
				
				$order = 0;
				
				$selKpaIndex = array ();
				
				foreach ( $_POST ['diagnostic_questions'] as $kpa => $val ) :
					
					$val = explode ( '_', $val );
					
					$index = $val [0];
					
					$value = $val [1];
					
					if (in_array ( $value, $existingKpas )) {
						
						$order ++;
						
						array_push ( $kpaOrder, array (
								'id' => $index,
								'name' => $value,
								'order' => $order 
						) );
						array_push ( $selKpaIndex, $index );
					} elseif (in_array ( $value, $kpasCreated )) {
						
						$order ++;
						
						array_push ( $kpaOrder, array (
								'id' => array_search ( $value, $kpasCreated ),
								'name' => $value,
								'order' => $order 
						) );
						
						array_push ( $selKpaIndex, $index );
					}
				endforeach
				;
				
				if (! ($diagnosticExists)) { // For new diagnostic only.if diagnostic doesnt exist(always so at the first time), kpa instances are created for the diagnostic
				                            
					// print_r($kpaOrder);
				                            // echo "<br>$diagnosticId<br>";
				                            // die("new diag");
					
					if (! ($diagnosticModel->createKpaDiagnosticInstance ( $kpaOrder, $diagnosticId )))
						$queryFailed = 30;
				} 

				else {
					
					// if diagnostic already exists
					// new kpas have already been inserted in d_kpa table. So now check KpaOrder array only
					// if new kpas are there create kpaInstance and if some existing kpas have been selected, create kpa instances
					// if only order has been changed, update kpainstances
					
					$kpaInstanceDiagnostic = array ();
					
					$kpaInstanceDiagnostic = $diagnosticModel->getKpaIdsForDiagnostic ( $diagnosticId );
					
					$instanceKpa = array ();
					
					foreach ( $kpaInstanceDiagnostic as $kpa ) :
						
						array_push ( $instanceKpa, $kpa ['kpa_id'] );
					endforeach
					;
					
					foreach ( $kpaOrder as $kpa ) :
						
						$id = $kpa ['id'];
						
						// if kpa instance exists in database as well as kpaorder -> update instance
						
						if (in_array ( $kpa ['id'], ($instanceKpa) )) {
							
							$diagnosticModel->updateSingleKpaDiagnosticInstance ( array (
									"id" => $kpa ['id'],
									"name" => $kpa ['name'],
									"order" => $kpa ['order'] 
							), $diagnosticId );
							
							// if kpa instance exists in database but not in kpaorder -> delete instance
						} else {
							
							// if kpa instance does nt exist in database but exists in kpaorder -> insert instance
							
							$diagnosticModel->createSingleKpaDiagnosticInstance ( array (
									"id" => $kpa ['id'],
									"name" => $kpa ['name'],
									"order" => $kpa ['order'] 
							), $diagnosticId );
							
							// if kpa instance exists neither in database nor in kpaorder - not possible
						}
					endforeach
					;
					
					// delete kpa instances that are in the kpainstance(h_kpa_diagnostic) but not in kpaOrder array for the diagnostic
					
					foreach ( $instanceKpa as $kpa_id ) :
						
						if (! in_array ( $kpa_id, $selKpaIndex )) {
							
							if (! $diagnosticModel->deleteKpaDiagnosticInstance ( $kpa_id, $diagnosticId )) {
								
								$queryFailed = 45;
								
								break;
							}
						}
					endforeach
					;
				}
				
				if ($queryFailed == 0 && $this->db->commit ()) {
					
                                       
                                        
					$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnosticLang ( $diagnosticId ,$langId ), "kpa_instance_id" );
					
					$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "kpa_instance_id", "key_question_instance_id" );
					
					$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "key_question_instance_id", "core_question_instance_id" );
					
					$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnosticLang ( $diagnosticId,$langId ), "core_question_instance_id", "judgement_statement_instance_id" );
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["diagnosticId"] = $diagnosticId;
					
					$this->apiResult ["message"] = "KPAs saved successfully";
					
					ob_start ();
					
					include (ROOT . 'application' . DS . 'views' . DS . "diagnostic" . DS . 'diagnostickpatabs.php');
					
					$this->apiResult ["content"] = ob_get_contents ();
					
					ob_end_clean ();
				} else {
					
					$this->apiResult ["message"] = "Unable to process your request";
					
					$this->db->rollback ();
				}
			}
		}
                }
	}
	
	// create or save key questions for diagnostic
	function saveDiagnosticKeyQuestionsAction() {
                
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['type'] )) {
			
			$this->apiResult ["message"] = "Diagnostic type can not be empty\n";
		} else if (empty ( $_POST ['langId'] )) {
			$this->apiResult ["message"] = "Language can not be empty\n";
		}else if (empty ( $_POST ['assessmentId'] )) {
			
			$this->apiResult ["message"] = "Diagnostic review type can not be empty\n";
		} else if (empty ( $_POST ['diagnosticName'] )) {
			
			$this->apiResult ["message"] = "Diagnostic Name can not be empty\n";
		} else if (empty ( $_POST ['diagnostic_questions'] )) {
			
			$this->apiResult ["message"] = "Diagnostic questions can not be empty\n";
		} else if (empty ( $_POST ['diagnosticId'] )) {
			
			$this->apiResult ["message"] = "diagnosticId can not be empty\n";
		} else if (empty ( $_POST ['kpaId'] )) {
			
			$this->apiResult ["message"] = "kpaId can not be empty\n";
		} else {
                    
			$langId=isset($_POST['langId'])?$_POST['langId']:0;
			$diagnosticModel = new diagnosticModel ();
			
			$assessmentId = $_POST ['assessmentId'];
			
			$diagnosticName = trim ( $_POST ['diagnosticName'] );
                        
                        $parentId=isset($_POST['parentdiaId'])?$_POST['parentdiaId']:0;
                        $equivalenceId=isset($_POST['equivalenceId'])?$_POST['equivalenceId']:0;
                        $langIdOriginal=isset($_POST['langIdOriginal'])?$_POST['langIdOriginal']:0;
                        
                        // assessor recommendations
			$is_kpa_recommendations = empty ( $_POST ['kpa_recommendations'] ) ? 0 : $_POST ['kpa_recommendations'];
			$is_kq_recommendations = empty ( $_POST ['kq_recommendations'] ) ? 0 : $_POST ['kq_recommendations'];
			$is_cq_recommendations = empty ( $_POST ['cq_recommendations'] ) ? 0 : $_POST ['cq_recommendations'];
			$is_js_recommendations = empty ( $_POST ['js_recommendations'] ) ? 0 : $_POST ['js_recommendations'];
			
			// $diagnosticId = empty($_GET['diagnosticId'])?0:$_GET['diagnosticId'];
                        
                        $diagnosticId = empty ( $_POST ['diagnosticId'] ) ? 0 : $_POST ['diagnosticId'];
                        
                        $diagnosticLabels = array();                                               
                                        $diagnosticLabelsData = $diagnosticModel->getDiagnosticLabels($diagnosticId,$langId);
                                        foreach($diagnosticLabelsData as $data) {
                                            $diagnosticLabels[$data['label_key']] = $data['label_text'];
                        }
                        
			$image_name = '';
			$imageId = NULL;
                        
                        if ($diagnosticId != 0) {
				if ($image_name == '') {
					$dig_image = $diagnosticModel->getDiagnosticImage ( $diagnosticId );
					$image_name = $dig_image [0] ['file_name'];
					$imageId = $dig_image [0] ['diagnostic_image_id'];
				}
			}
			
			$kpaId = empty ( $_POST ['kpaId'] ) ? 0 : $_POST ['kpaId'];
			
			$isPublished = 0;
			
			// $diagnosticId = 18;
			
			$flagNewKpas = 0; // flag set to 1 if any new kpas exist
                        
                        $row = $diagnosticModel->getMinMaxLimitForDiagnosticQuestions ( 'kq', $assessmentId );
			
			if (! $row) 

			{
				
				$this->apiResult ["message"] = "Unable to process your request";
				
				return;
			} 

			else if ((count ( $_POST ['diagnostic_questions'] ) > $row [0] ['limit_max'])) {
				
				$this->apiResult ["message"] = "Number of Key Questions can't be more than " . $row [0] ['limit_max'];
				
				return;
			}
                        
                        if($parentId>0 && $equivalenceId>0 && $langIdOriginal>0){
                        
                                $blankKpas=array();
                                $existingKpas = array ();
                                $newKpas = array ();
                                if (! empty ( $_POST ['diagnostic_questions'] )) {
					
					foreach ( $_POST ['diagnostic_questions'] as $q ) :
						
						$q = explode ( '_', $q );
						
						$index = $q [0];
						
						$val = $q [1];
                                                if(empty($val)){
						$blankKpas[]=$val;
                                                }
                                                $existingKpas [$index] = $val;
					endforeach
					;
				}
                                
                                if(count($blankKpas)>0){
                                    $this->apiResult ["message"] = "All fields should be filled";
                                    return;
                                }
                                
                        $this->db->start_transaction ();
			$queryFailed = 0;
                        
                        
                        
                        
                             if($diagnosticId>0){
                                if (! ($diagnosticModel->updateDiagnosticLang ( $diagnosticId, $diagnosticName, $isPublished, $langId ,$parentId,$this->user ['user_id'] )))
						$queryFailed = 1;
                                }
                                //$_POST ['diagnostic_questions_new']
                                if (! empty ( $_POST ['diagnostic_questions'] )) {
					
					foreach ( $_POST ['diagnostic_questions'] as $q ) :
						
						$q = explode ( '_', $q );
						
						$index = $q [0];
						
						$val = $q [1];
						$newKpas [$index] = $val;
					endforeach
					;
				}
                                
                                
                                $newKpas=count($newKpas)>0?$newKpas:NULL;
                                
				if ($newKpas) {
					
					$kpasCreated = $diagnosticModel->newLangCreateKeyQuestions ( $newKpas, $langId);
					
					if (! $kpasCreated)
						$queryFailed = 20;
					
					$flagNewKpas = 1;
				}
			
			
                        if ($queryFailed == 0 && $this->db->commit ()) 

			{
				
				$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnosticLang ( $diagnosticId, $langId ), "kpa_instance_id" );
				
				$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnosticLang ( $diagnosticId, $langId ), "kpa_instance_id", "key_question_instance_id" );
				
				$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnosticLang ( $diagnosticId, $langId ), "key_question_instance_id", "core_question_instance_id" );
				
				$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnosticLang ( $diagnosticId, $langId ), "core_question_instance_id", "judgement_statement_instance_id" );
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["diagnosticId"] = $diagnosticId;
				
				$this->apiResult ["message"] = "KQs saved successfully";
				
				ob_start ();
				
				include (ROOT . 'application' . DS . 'views' . DS . "diagnostic" . DS . 'diagnostickpatabs.php');
				
				$this->apiResult ["content"] = ob_get_contents ();
				
				ob_end_clean ();
			} 

			else 

			{
				
				$this->apiResult ["message"] = "Unable to process your request";
				
				$this->db->rollback ();
			}
                        
                        
                            
                        }else{
                        
			
			
			$this->db->start_transaction ();
			
			$queryFailed = 0;
			
			if (! ($diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, $imageId, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations,$langId,$this->user ['user_id'] )))
				
				$queryFailed = 1;
			
			$existingKpas = array ();
			
			if (! empty ( $_POST ['diagnostic_questions_update'] )) 

			{
				
				foreach ( $_POST ['diagnostic_questions_update'] as $q ) :
					
					$q = explode ( '_', $q );
					
					$index = $q [0];
					
					$val = $q [1];
					
					$existingKpas [$index] = $val;
				endforeach
				;
			}
			
			// add new kpas
			
			// print_r($_POST['diagnostic_questions_new']);
			
			$kpas = empty ( $_POST ['diagnostic_questions_new'] ) ? NULL : $_POST ['diagnostic_questions_new'];
			
			// print_r($kpas);
			
			if ($kpas) 

			{
				
				$kpasCreated = $diagnosticModel->createKeyQuestions ( $kpas , $langId );
				
				if (! $kpasCreated)
					
					$queryFailed = 20;
				
				$flagNewKpas = 1;
			}
			
			// print_r($kpasCreated);
			
			// save kpa instances for diagnostic
			
			$kpaOrder = array ();
			
			$order = 0;
			
			$selKpaIndex = array ();
			
			foreach ( $_POST ['diagnostic_questions'] as $kpa => $val ) :
				
				$val = explode ( '_', $val );
				
				$index = $val [0];
				
				$value = $val [1];
				
				if (in_array ( $value, $existingKpas )) 

				{
					
					$order ++;
					
					array_push ( $kpaOrder, array (
							'id' => $index,
							'name' => $value,
							'order' => $order 
					) );
					
					array_push ( $selKpaIndex, $index );
				} 

				elseif (in_array ( $value, $kpasCreated )) 

				{
					
					$order ++;
					
					array_push ( $kpaOrder, array (
							'id' => array_search ( $value, $kpasCreated ),
							'name' => $value,
							'order' => $order 
					) );
					
					array_push ( $selKpaIndex, $index );
				}
			endforeach
			;
			
			// if kq exists in kpaOrder but not in kq instance, insert it
			
			// if kq exists in kpaOrder as well as kq instance, update it
			
			// if kq doesnot exist in kpaOrder but exists in kq instance, delete it
			
			$kpaInstanceDiagnostic = array ();
			
			$kpaInstanceDiagnostic = $diagnosticModel->getKeyQuestionIdsPerKpaForDiagnostic ( $diagnosticId, $kpaId );
			
			$instanceKpa = array ();
			
			foreach ( $kpaInstanceDiagnostic as $kpa ) :
				
				array_push ( $instanceKpa, $kpa ['key_question_id'] );
			endforeach
			;
			
			foreach ( $kpaOrder as $kpa ) :
				
				$id = $kpa ['id'];
				
				// if kpa instance exists in database as well as kpaorder -> update instance
				
				if (in_array ( $kpa ['id'], ($instanceKpa) )) {
					
					$diagnosticModel->updateSingleKeyQuestionDiagnosticInstance ( array (
							"key_question_id" => $kpa ['id'],
							"order" => $kpa ['order'] 
					), $kpaId );
				} else 

				{
					
					// if kpa instance does nt exist in database but exists in kpaorder -> insert instance
					
					$diagnosticModel->createSingleKeyQuestionDiagnosticInstance ( array (
							"key_question_id" => $kpa ['id'],
							"order" => $kpa ['order'] 
					), $kpaId );
					
					// if kpa instance exists neither in database nor in kpaorder - not possible
				}
			endforeach
			;
			
			// if kpa instance exists in database but not in kpaorder -> delete instance
			
			// delete kpa instances that are in the kpainstance(h_kpa_diagnostic) but not in kpaOrder array for the diagnostic
			
			foreach ( $instanceKpa as $kpa_id ) :
				
				if (! in_array ( $kpa_id, $selKpaIndex )) {
					
					if (! $diagnosticModel->deleteKeyQuestionDiagnosticInstance ( $kpa_id, $kpaId )) 

					{
						
						$queryFailed = 45;
						
						break;
					}
				}
			endforeach
			;
			
			// ///
			
			if ($queryFailed == 0 && $this->db->commit ()) 

			{
				
				$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnosticLang ( $diagnosticId, $langId ), "kpa_instance_id" );
				
				$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnosticLang ( $diagnosticId, $langId ), "kpa_instance_id", "key_question_instance_id" );
				
				$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnosticLang ( $diagnosticId, $langId ), "key_question_instance_id", "core_question_instance_id" );
				
				$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnosticLang ( $diagnosticId, $langId ), "core_question_instance_id", "judgement_statement_instance_id" );
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["diagnosticId"] = $diagnosticId;
				
				$this->apiResult ["message"] = "KQs saved successfully";
				
				ob_start ();
				
				include (ROOT . 'application' . DS . 'views' . DS . "diagnostic" . DS . 'diagnostickpatabs.php');
				
				$this->apiResult ["content"] = ob_get_contents ();
				
				ob_end_clean ();
			} 

			else 

			{
				
				$this->apiResult ["message"] = "Unable to process your request";
				
				$this->db->rollback ();
			}
		}
                }
	}
	
	// create or save core questions for diagnostic
	function saveDiagnosticCoreQuestionsAction() {
                
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['type'] )) {
			
			$this->apiResult ["message"] = "Diagnostic type can not be empty\n";
		} else if (empty ( $_POST ['langId'] )) {
			$this->apiResult ["message"] = "Language can not be empty\n";
		} else if (empty ( $_POST ['assessmentId'] )) {
			
			$this->apiResult ["message"] = "Diagnostic review type can not be empty\n";
		} else if (empty ( $_POST ['diagnosticName'] )) {
			
			$this->apiResult ["message"] = "Diagnostic Name can not be empty\n";
		} else if (empty ( $_POST ['diagnostic_questions'] )) {
			
			$this->apiResult ["message"] = "Diagnostic questions can not be empty\n";
		} else if (empty ( $_POST ['diagnosticId'] )) {
			
			$this->apiResult ["message"] = "diagnosticId can not be empty\n";
		} else if (empty ( $_POST ['kpaId'] )) {
			
			$this->apiResult ["message"] = "kpaId can not be empty\n";
		} else if (empty ( $_POST ['kqId'] )) {
			
			$this->apiResult ["message"] = "kqId can not be empty\n";
		} else {
                                        
			$langId=isset($_POST['langId'])?$_POST['langId']:0;
			$diagnosticModel = new diagnosticModel ();
			
			$assessmentId = $_POST ['assessmentId'];
                        
                        $parentId=isset($_POST['parentdiaId'])?$_POST['parentdiaId']:0;
                        $equivalenceId=isset($_POST['equivalenceId'])?$_POST['equivalenceId']:0;
                        $langIdOriginal=isset($_POST['langIdOriginal'])?$_POST['langIdOriginal']:0;
			
			$diagnosticName = trim ( $_POST ['diagnosticName'] );
			// assessor recommendations
			$is_kpa_recommendations = empty ( $_POST ['kpa_recommendations'] ) ? 0 : $_POST ['kpa_recommendations'];
			$is_kq_recommendations = empty ( $_POST ['kq_recommendations'] ) ? 0 : $_POST ['kq_recommendations'];
			$is_cq_recommendations = empty ( $_POST ['cq_recommendations'] ) ? 0 : $_POST ['cq_recommendations'];
			$is_js_recommendations = empty ( $_POST ['js_recommendations'] ) ? 0 : $_POST ['js_recommendations'];
			
                        
                        
			// $diagnosticId = empty($_GET['diagnosticId'])?0:$_GET['diagnosticId'];
			
			$diagnosticId = empty ( $_POST ['diagnosticId'] ) ? 0 : $_POST ['diagnosticId'];
                        
                        $diagnosticLabels = array();                                               
                                        $diagnosticLabelsData = $diagnosticModel->getDiagnosticLabels($diagnosticId,$langId);
                                        foreach($diagnosticLabelsData as $data) {
                                            $diagnosticLabels[$data['label_key']] = $data['label_text'];
                        }
			
			$image_name = '';
			$imageId = NULL;
			if ($diagnosticId != 0) {
				if ($image_name == '') {
					$dig_image = $diagnosticModel->getDiagnosticImage ( $diagnosticId );
					$image_name = $dig_image [0] ['file_name'];
					$imageId = $dig_image [0] ['diagnostic_image_id'];
				}
			}
			
			$kpaId = empty ( $_POST ['kpaId'] ) ? 0 : $_POST ['kpaId'];
			
			$kqId = empty ( $_POST ['kqId'] ) ? 0 : $_POST ['kqId'];
			
			$isPublished = 0;
			
			// $diagnosticId = 18;
			
			$flagNewKpas = 0; // flag set to 1 if any new kpas exist
			
			$row = $diagnosticModel->getMinMaxLimitForDiagnosticQuestions ( 'cq', $assessmentId );
			
			if (! $row) 

			{
				
				$this->apiResult ["message"] = "Unable to process your request";
				
				return;
			} 

			else if ((count ( $_POST ['diagnostic_questions'] ) > $row [0] ['limit_max'])) {
				
				$this->apiResult ["message"] = "Number of Sub Questions can't be more than " . $row [0] ['limit_max'];
				
				return;
			}
			
                        
                        if($parentId>0 && $equivalenceId>0 && $langIdOriginal>0){
                        
                                $blankKpas=array();
                                $existingKpas = array ();
                                $newKpas = array ();
                                if (! empty ( $_POST ['diagnostic_questions'] )) {
					
					foreach ( $_POST ['diagnostic_questions'] as $q ) :
						
						$q = explode ( '_', $q );
						
						$index = $q [0];
						
						$val = $q [1];
                                                if(empty($val)){
						$blankKpas[]=$val;
                                                }
                                                $existingKpas [$index] = $val;
					endforeach
					;
				}
                                
                                if(count($blankKpas)>0){
                                    $this->apiResult ["message"] = "All fields should be filled";
                                    return;
                                }
                                
                        $this->db->start_transaction ();
			$queryFailed = 0;
                        
                        
                        
                        
                             if($diagnosticId>0){
                                if (! ($diagnosticModel->updateDiagnosticLang ( $diagnosticId, $diagnosticName, $isPublished, $langId ,$parentId,$this->user ['user_id'] )))
						$queryFailed = 1;
                                }
                                //$_POST ['diagnostic_questions_new']
                                if (! empty ( $_POST ['diagnostic_questions'] )) {
					
					foreach ( $_POST ['diagnostic_questions'] as $q ) :
						
						$q = explode ( '_', $q );
						
						$index = $q [0];
						
						$val = $q [1];
						$newKpas [$index] = $val;
					endforeach
					;
				}
                                
                                
                                $newKpas=count($newKpas)>0?$newKpas:NULL;
                                
				if ($newKpas) {
					
					$kpasCreated = $diagnosticModel->newLangCreateCoreQuestions ( $newKpas, $langId);
					
					if (! $kpasCreated)
						$queryFailed = 20;
					
					$flagNewKpas = 1;
				}
			
			
                        if ($queryFailed == 0 && $this->db->commit ()) 

			{
				
				$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnosticLang ( $diagnosticId,$langId ), "kpa_instance_id" );
				
				$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "kpa_instance_id", "key_question_instance_id" );
				
				$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "key_question_instance_id", "core_question_instance_id" );
				
				$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnosticLang ( $diagnosticId,$langId ), "core_question_instance_id", "judgement_statement_instance_id" );
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["diagnosticId"] = $diagnosticId;
				
				$this->apiResult ["message"] = "CQs saved successfully";
				
				ob_start ();
				
				include (ROOT . 'application' . DS . 'views' . DS . "diagnostic" . DS . 'diagnostickpatabs.php');
				
				$this->apiResult ["content"] = ob_get_contents ();
				
				ob_end_clean ();
			} 

			else 

			{
				
				$this->apiResult ["message"] = "Unable to process your request";
				
				$this->db->rollback ();
			}
                        
                        
                            
                        }else{
                        
                        
			$this->db->start_transaction ();
			
			$queryFailed = 0;
			
			if (! ($diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, $imageId, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations,$langId,$this->user ['user_id'] )))
				
				$queryFailed = 1;
			
			$existingKpas = array ();
			
			if (! empty ( $_POST ['diagnostic_questions_update'] )) 

			{
				
				foreach ( $_POST ['diagnostic_questions_update'] as $q ) :
					
					$q = explode ( '_', $q );
					
					$index = $q [0];
					
					$val = $q [1];
					
					$existingKpas [$index] = $val;
				endforeach
				;
			}
			
			// add new kpas
			
			// print_r($_POST['diagnostic_questions_new']);
			
			$kpas = empty ( $_POST ['diagnostic_questions_new'] ) ? NULL : $_POST ['diagnostic_questions_new'];
			
			// print_r($kpas);
			
			if ($kpas) 

			{
				
				$kpasCreated = $diagnosticModel->createCoreQuestions ( $kpas, $langId );
				
				if (! $kpasCreated)
					
					$queryFailed = 20;
				
				$flagNewKpas = 1;
			}
			
			// print_r($kpasCreated);
			
			// save kpa instances for diagnostic
			
			$kpaOrder = array ();
			
			$order = 0;
			
			$selKpaIndex = array ();
			
			foreach ( $_POST ['diagnostic_questions'] as $kpa => $val ) :
				
				$val = explode ( '_', $val );
				
				$index = $val [0];
				
				$value = $val [1];
				
				if (in_array ( $value, $existingKpas )) 

				{
					
					$order ++;
					
					array_push ( $kpaOrder, array (
							'id' => $index,
							'name' => $value,
							'order' => $order 
					) );
					
					array_push ( $selKpaIndex, $index );
				} 

				elseif (in_array ( $value, $kpasCreated )) 

				{
					
					$order ++;
					
					array_push ( $kpaOrder, array (
							'id' => array_search ( $value, $kpasCreated ),
							'name' => $value,
							'order' => $order 
					) );
					
					array_push ( $selKpaIndex, $index );
				}
			endforeach
			;
			
			// if kq exists in kpaOrder but not in kq instance, insert it
			
			// if kq exists in kpaOrder as well as kq instance, update it
			
			// if kq doesnot exist in kpaOrder but exists in kq instance, delete it
			
			$kpaInstanceDiagnostic = array ();
			
			$kpaInstanceDiagnostic = $diagnosticModel->getCoreQuestionIdsPerKqForDiagnostic ( $diagnosticId, $kpaId, $kqId );
			
			$instanceKpa = array ();
			
			foreach ( $kpaInstanceDiagnostic as $kpa ) :
				
				array_push ( $instanceKpa, $kpa ['core_question_id'] );
			endforeach
			;
			
			foreach ( $kpaOrder as $kpa ) :
				
				$id = $kpa ['id'];
				
				// if kpa instance exists in database as well as kpaorder -> update instance
				
				if (in_array ( $kpa ['id'], ($instanceKpa) )) {
					
					$diagnosticModel->updateSingleCoreQuestionDiagnosticInstance ( array (
							"core_question_id" => $kpa ['id'],
							"order" => $kpa ['order'] 
					), $kqId );
				} else 

				{
					
					// if kpa instance does nt exist in database but exists in kpaorder -> insert instance
					
					$diagnosticModel->createSingleCoreQuestionDiagnosticInstance ( array (
							"core_question_id" => $kpa ['id'],
							"order" => $kpa ['order'] 
					), $kqId );
					
					// if kpa instance exists neither in database nor in kpaorder - not possible
				}
			endforeach
			;
			
			// if kpa instance exists in database but not in kpaorder -> delete instance
			
			// delete kpa instances that are in the kpainstance(h_kpa_diagnostic) but not in kpaOrder array for the diagnostic
			
			foreach ( $instanceKpa as $kpa_id ) :
				
				if (! in_array ( $kpa_id, $selKpaIndex )) {
					
					// delete Judgement Statement if any for the CQ instance
					
					if (! $diagnosticModel->deleteCoreQuestionDiagnosticInstance ( $kpa_id, $kqId )) 

					{
						
						$queryFailed = 45;
						
						break;
					}
				}
			endforeach
			;
			
			if ($queryFailed == 0 && $this->db->commit ()) 

			{
				
				$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnosticLang ( $diagnosticId,$langId ), "kpa_instance_id" );
				
				$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "kpa_instance_id", "key_question_instance_id" );
				
				$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "key_question_instance_id", "core_question_instance_id" );
				
				$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnosticLang ( $diagnosticId,$langId ), "core_question_instance_id", "judgement_statement_instance_id" );
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["diagnosticId"] = $diagnosticId;
				
				$this->apiResult ["message"] = "CQs saved successfully";
				
				ob_start ();
				
				include (ROOT . 'application' . DS . 'views' . DS . "diagnostic" . DS . 'diagnostickpatabs.php');
				
				$this->apiResult ["content"] = ob_get_contents ();
				
				ob_end_clean ();
			} 

			else 

			{
		
				$this->apiResult ["message"] = "Unable to process your request";
				
				$this->db->rollback ();
			}
		}
                }
	}
	
	// create or save judgement statements for diagnostic
	function saveDiagnosticJudgementStatementsAction() {
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['type'] )) {
			
			$this->apiResult ["message"] = "Diagnostic type can not be empty\n";
		} else if (empty ( $_POST ['langId'] )) {
			$this->apiResult ["message"] = "Language can not be empty\n";
		} else if (empty ( $_POST ['assessmentId'] )) {
			
			$this->apiResult ["message"] = "Diagnostic review type can not be empty\n";
		} else if (empty ( $_POST ['diagnosticName'] )) {
			
			$this->apiResult ["message"] = "Diagnostic Name can not be empty\n";
		} else if (empty ( $_POST ['diagnostic_questions'] )) {
			
			$this->apiResult ["message"] = "Diagnostic questions can not be empty\n";
		} else if (empty ( $_POST ['diagnosticId'] )) {
			
			$this->apiResult ["message"] = "diagnosticId can not be empty\n";
		} else if (empty ( $_POST ['kpaId'] )) {
			
			$this->apiResult ["message"] = "kpaId can not be empty\n";
		} else if (empty ( $_POST ['kqId'] )) {
			
			$this->apiResult ["message"] = "kqId can not be empty\n";
		} else if (empty ( $_POST ['cqId'] )) {
			
			$this->apiResult ["message"] = "cqId can not be empty\n";
		} else {
			$langId=isset($_POST['langId'])?$_POST['langId']:0;
                        $parentId=isset($_POST['parentdiaId'])?$_POST['parentdiaId']:0;
                        $equivalenceId=isset($_POST['equivalenceId'])?$_POST['equivalenceId']:0;
                        $langIdOriginal=isset($_POST['langIdOriginal'])?$_POST['langIdOriginal']:0;
                        
			$diagnosticModel = new diagnosticModel ();
			
			$assessmentId = $_POST ['assessmentId'];
			
			$diagnosticName = trim ( $_POST ['diagnosticName'] );
			// assessor recommendations
			$is_kpa_recommendations = empty ( $_POST ['kpa_recommendations'] ) ? 0 : $_POST ['kpa_recommendations'];
			$is_kq_recommendations = empty ( $_POST ['kq_recommendations'] ) ? 0 : $_POST ['kq_recommendations'];
			$is_cq_recommendations = empty ( $_POST ['cq_recommendations'] ) ? 0 : $_POST ['cq_recommendations'];
			$is_js_recommendations = empty ( $_POST ['js_recommendations'] ) ? 0 : $_POST ['js_recommendations'];
			
                        
			// $diagnosticId = empty($_GET['diagnosticId'])?0:$_GET['diagnosticId'];
			
			$diagnosticId = empty ( $_POST ['diagnosticId'] ) ? 0 : $_POST ['diagnosticId'];
                        $diagnosticLabels = array();                                               
                                        $diagnosticLabelsData = $diagnosticModel->getDiagnosticLabels($diagnosticId,$langId);
                                        foreach($diagnosticLabelsData as $data) {
                                            $diagnosticLabels[$data['label_key']] = $data['label_text'];
                        }
                        
			$image_name = '';
			$imageId = NULL;
			if ($diagnosticId != 0) {
				if ($image_name == '') {
					$dig_image = $diagnosticModel->getDiagnosticImage ( $diagnosticId );
					$image_name = $dig_image [0] ['file_name'];
					$imageId = $dig_image [0] ['diagnostic_image_id'];
				}
			}
			$kpaId = empty ( $_POST ['kpaId'] ) ? 0 : $_POST ['kpaId'];
			
			$kqId = empty ( $_POST ['kqId'] ) ? 0 : $_POST ['kqId'];
			
			$cqId = empty ( $_POST ['cqId'] ) ? 0 : $_POST ['cqId'];
			
			$isPublished = 0;
			
			// $diagnosticId = 18;
			
			$flagNewKpas = 0; // flag set to 1 if any new kpas exist
			
			$row = $diagnosticModel->getMinMaxLimitForDiagnosticQuestions ( 'jss', $assessmentId );
			
			if (! $row) 

			{
				
				$this->apiResult ["message"] = "Unable to process your request";
				
				return;
			} 

			else if ((count ( $_POST ['diagnostic_questions'] ) > $row [0] ['limit_max'])) {
				
				$this->apiResult ["message"] = "Number of Judgement Statements can't be more than " . $row [0] ['limit_max'];
				
				return;
			}
			
                        
                        if($parentId>0 && $equivalenceId>0 && $langIdOriginal>0){
                        
                                $blankKpas=array();
                                $existingKpas = array ();
                                $newKpas = array ();
                                if (! empty ( $_POST ['diagnostic_questions'] )) {
					
					foreach ( $_POST ['diagnostic_questions'] as $q ) :
						
						$q = explode ( '_', $q );
						
						$index = $q [0];
						
						$val = $q [1];
                                                if(empty($val)){
						$blankKpas[]=$val;
                                                }
                                                $existingKpas [$index] = $val;
					endforeach
					;
				}
                                
                                if(count($blankKpas)>0){
                                    $this->apiResult ["message"] = "All fields should be filled";
                                    return;
                                }
                                
                        $this->db->start_transaction ();
			$queryFailed = 0;
                        
                        
                        
                        
                             if($diagnosticId>0){
                                if (! ($diagnosticModel->updateDiagnosticLang ( $diagnosticId, $diagnosticName, $isPublished, $langId ,$parentId, $this->user ['user_id'] )))
						$queryFailed = 1;
                                }
                                //$_POST ['diagnostic_questions_new']
                                if (! empty ( $_POST ['diagnostic_questions'] )) {
					
					foreach ( $_POST ['diagnostic_questions'] as $q ) :
						
						$q = explode ( '_', $q );
						
						$index = $q [0];
						
						$val = $q [1];
						$newKpas [$index] = $val;
					endforeach
					;
				}
                                
                                
                                $newKpas=count($newKpas)>0?$newKpas:NULL;
                                
				if ($newKpas) {
					
					$kpasCreated = $diagnosticModel->newLangCreateJudgementStatements ( $newKpas, $langId);
					
					if (! $kpasCreated)
						$queryFailed = 20;
					
					$flagNewKpas = 1;
				}
			
			
                        if ($queryFailed == 0 && $this->db->commit ()) 

			{
				
				$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnosticLang ( $diagnosticId,$langId ), "kpa_instance_id" );
				
				$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "kpa_instance_id", "key_question_instance_id" );
				
				$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "key_question_instance_id", "core_question_instance_id" );
				
				$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnosticLang ( $diagnosticId,$langId ), "core_question_instance_id", "judgement_statement_instance_id" );
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["diagnosticId"] = $diagnosticId;
				
				$this->apiResult ["message"] = "Judgement statements saved successfully";
				
				ob_start ();
				
				include (ROOT . 'application' . DS . 'views' . DS . "diagnostic" . DS . 'diagnostickpatabs.php');
				
				$this->apiResult ["content"] = ob_get_contents ();
				
				ob_end_clean ();
			} 

			else 

			{
				
				$this->apiResult ["message"] = "Unable to process your request";
				
				$this->db->rollback ();
			}
                        
                        
                            
                        }else{
                        
                        
			$this->db->start_transaction ();
			
			$queryFailed = 0;
			
			if (! ($diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, $imageId, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations, $langId, $this->user ['user_id'] )))
				
				$queryFailed = 1;
			
			$existingKpas = array ();
			
			if (! empty ( $_POST ['diagnostic_questions_update'] )) 

			{
				
				foreach ( $_POST ['diagnostic_questions_update'] as $q ) :
					
					$q = explode ( '_', $q );
					
					$index = $q [0];
					
					$val = $q [1];
					
					$existingKpas [$index] = $val;
				endforeach
				;
			}
			
			// add new kpas
			
			// print_r($_POST['diagnostic_questions_new']);
			
			$kpas = empty ( $_POST ['diagnostic_questions_new'] ) ? NULL : $_POST ['diagnostic_questions_new'];
			
			// print_r($kpas);
			
			if ($kpas) 

			{
				
				$kpasCreated = $diagnosticModel->createJudgementStatements ( $kpas, $langId );
				
				if (! $kpasCreated)
					
					$queryFailed = 20;
				
				$flagNewKpas = 1;
			}
			
			// print_r($kpasCreated);
			
			// save kpa instances for diagnostic
			
			$kpaOrder = array ();
			
			$order = 0;
			
			$selKpaIndex = array ();
			
			foreach ( $_POST ['diagnostic_questions'] as $kpa => $val ) :
				
				$val = explode ( '_', $val );
				
				$index = $val [0];
				
				$value = $val [1];
				
				if (in_array ( $value, $existingKpas )) 

				{
					
					$order ++;
					
					array_push ( $kpaOrder, array (
							'id' => $index,
							'name' => $value,
							'order' => $order 
					) );
					
					array_push ( $selKpaIndex, $index );
				} 

				elseif (in_array ( $value, $kpasCreated )) 

				{
					
					$order ++;
					
					array_push ( $kpaOrder, array (
							'id' => array_search ( $value, $kpasCreated ),
							'name' => $value,
							'order' => $order 
					) );
					
					array_push ( $selKpaIndex, $index );
				}
			endforeach
			;
			
			// if kq exists in kpaOrder but not in kq instance, insert it
			
			// if kq exists in kpaOrder as well as kq instance, update it
			
			// if kq doesnot exist in kpaOrder but exists in kq instance, delete it
			
			$kpaInstanceDiagnostic = array ();
			
			$kpaInstanceDiagnostic = $diagnosticModel->getJSSIdsPerCqForDiagnostic ( $diagnosticId, $kpaId, $kqId, $cqId, $langId );
			
			$instanceKpa = array ();
			
			foreach ( $kpaInstanceDiagnostic as $kpa ) :
				
				array_push ( $instanceKpa, $kpa ['judgement_statement_id'] );
			endforeach
			;
			
			foreach ( $kpaOrder as $kpa ) :
				
				$id = $kpa ['id'];
				
				// if kpa instance exists in database as well as kpaorder -> update instance
				
				if (in_array ( $kpa ['id'], ($instanceKpa) )) {
					
					$diagnosticModel->updateSingleJSSDiagnosticInstance ( array (
							"judgement_statement_id" => $kpa ['id'],
							"order" => $kpa ['order'] 
					), $cqId );
				} else 

				{
					
					// if kpa instance does nt exist in database but exists in kpaorder -> insert instance
					
					$diagnosticModel->createSingleJSSDiagnosticInstance ( array (
							"judgement_statement_id" => $kpa ['id'],
							"order" => $kpa ['order'] 
					), $cqId );
					
					// if kpa instance exists neither in database nor in kpaorder - not possible
				}
			endforeach
			;
			
			// if kpa instance exists in database but not in kpaorder -> delete instance
			
			// delete kpa instances that are in the kpainstance(h_kpa_diagnostic) but not in kpaOrder array for the diagnostic
			
			foreach ( $instanceKpa as $kpa_id ) :
				
				if (! in_array ( $kpa_id, $selKpaIndex )) {
					
					if (! $diagnosticModel->deleteJSSDiagnosticInstance ( $kpa_id, $cqId )) 

					{
						
						$queryFailed = 45;
						
						break;
					}
				}
			endforeach
			;
			
			if ($queryFailed == 0 && $this->db->commit ()) 

			{
				
				$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnosticLang ( $diagnosticId,$langId ), "kpa_instance_id" );
				
				$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "kpa_instance_id", "key_question_instance_id" );
				
				$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnosticLang ( $diagnosticId,$langId ), "key_question_instance_id", "core_question_instance_id" );
				
				$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnosticLang ( $diagnosticId,$langId ), "core_question_instance_id", "judgement_statement_instance_id" );
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["diagnosticId"] = $diagnosticId;
				
				$this->apiResult ["message"] = "Judgement statements saved successfully";
				
				ob_start ();
				
				include (ROOT . 'application' . DS . 'views' . DS . "diagnostic" . DS . 'diagnostickpatabs.php');
				
				$this->apiResult ["content"] = ob_get_contents ();
				
				ob_end_clean ();
			} 

			else 

			{
				
				$this->apiResult ["message"] = "Unable to process your request";
				
				$this->db->rollback ();
			}
		}
                }
	}
	function submitDiagnosticAction() {
                //print_r($_POST);                        
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['diagnosticName'] )) {
			
			$this->apiResult ["message"] = "Diagnostic Name can not be empty\n";
		} else if (empty ( $_POST ['langId'] )) {
			
			$this->apiResult ["message"] = "Diagnostic Language can not be empty\n";
		} else if (empty ( $_POST ['diagnosticId'] )) {
			
			$this->apiResult ["message"] = "diagnosticId can not be empty\n";
		} else {
			
			$diagnosticModel = new diagnosticModel ();
			
			// $assessmentId = $_POST['assessmentId'];
			
			$diagnosticName = trim ( $_POST ['diagnosticName'] );
                        
                        $langId=isset($_POST['langId'])?$_POST['langId']:0;
                        $parentId=isset($_POST['parentdiaId'])?$_POST['parentdiaId']:0;
                        $equivalenceId=isset($_POST['equivalenceId'])?$_POST['equivalenceId']:0;
                        $langIdOriginal=isset($_POST['langIdOriginal'])?$_POST['langIdOriginal']:0;
                        
			$diagnosticId = empty ( $_POST ['diagnosticId'] ) ? 0 : $_POST ['diagnosticId'];
			$is_kpa_recommendations = empty ( $_POST ['kpa_recommendations'] ) ? 0 : $_POST ['kpa_recommendations'];
			$is_kq_recommendations = empty ( $_POST ['kq_recommendations'] ) ? 0 : $_POST ['kq_recommendations'];
			$is_cq_recommendations = empty ( $_POST ['cq_recommendations'] ) ? 0 : $_POST ['cq_recommendations'];
			$is_js_recommendations = empty ( $_POST ['js_recommendations'] ) ? 0 : $_POST ['js_recommendations'];
			
			// count kpas for diagnostic
			
			$kpa = $diagnosticModel->countKpaForDiagnostic ( $diagnosticId, $langId );
			
			$assessmentTypeId = $kpa [0] ['type'];
			
			$kpaCount = $kpa [0] ['num'];
			
			// get min max limits allowed for kpa
			
			$kpaLim = $diagnosticModel->getMinMaxLimitForDiagnosticQuestions ( 'kpa', $assessmentTypeId );
			
			$kpaMin = $kpaLim [0] ['limit_min'];
			
			$kpaMax = $kpaLim [0] ['limit_max'];
			
			// check if number of kpas are within limits
			
			if ($kpaCount < $kpaMin || $kpaCount > $kpaMax) 

			{
				
				$this->apiResult ["message"] = "Number of KPAs must be between $kpaMin and $kpaMax";
				
				$this->apiResult ["type"] = 'kpa';
				
				return;
			}
			
			// check key questions for each kpa
			
			$kpas = explode ( ',', $kpa [0] ['kpas'] );
			
			foreach ( $kpas as $kpa => $kpa_id ) {
				
				$kq = $diagnosticModel->countKeyQuestionsForKpa ( $kpa_id, $langId );
				
				$kqCount = $kq [0] ['num'];
				
				$kqLim = $diagnosticModel->getMinMaxLimitForDiagnosticQuestions ( 'kq', $assessmentTypeId );
				
				$kqMin = $kqLim [0] ['limit_min'];
				
				$kqMax = $kqLim [0] ['limit_max'];
				
				if ($kqCount < $kqMin || $kqCount > $kqMax) 

				{
					
					if ($kqMin == $kqMax)
						
						$this->apiResult ["message"] = "Number of Key Questions for KPA " . ($kpa + 1) . " must be $kqMin";
					
					else
						
						$this->apiResult ["message"] = "Number of Key Questions for KPA " . ($kpa + 1) . " must be between $kqMin and $kqMax";
					
					$this->apiResult ["type"] = 'kq';
					
					$this->apiResult ["kpaId"] = $kpa_id;
					
					return;
				}
				
				// check core questions for each key questions
				
				$kqs = explode ( ',', $kq [0] ['kq'] );
				
				foreach ( $kqs as $kq => $kq_id ) {
					
					$cq = $diagnosticModel->countCoreQuestionsForKeyQuestion ( $kq_id , $langId );
					
					$cqCount = $cq [0] ['num'];
					
					$cqLim = $diagnosticModel->getMinMaxLimitForDiagnosticQuestions ( 'cq', $assessmentTypeId );
					
					$cqMin = $cqLim [0] ['limit_min'];
					
					$cqMax = $cqLim [0] ['limit_max'];
					
					if ($cqCount < $cqMin || $cqCount > $cqMax) 

					{
						
						if ($cqMin == $cqMax)
							
							$this->apiResult ["message"] = "Number of Sub Questions in KPA " . ($kpa + 1) . "> Key Question " . ($kq + 1) . " must be $cqMin";
						
						else
							
							$this->apiResult ["message"] = "Number of Sub Questions in KPA " . ($kpa + 1) . "> Key Question " . ($kq + 1) . " must be between $cqMin and $cqMax";
						
						$this->apiResult ["type"] = 'cq';
						
						$this->apiResult ["kqid"] = $kq_id;
						
						$this->apiResult ["kpaId"] = $kpa_id;
						
						return;
					}
					
					// check judgement statements for each core question
					
					$cqs = explode ( ',', $cq [0] ['cq'] );
					
					foreach ( $cqs as $cq => $cq_id ) {
						
						$jss = $diagnosticModel->countJudgementStatementsForeCoreQuestion ( $cq_id , $langId);
						
						$jssCount = $jss [0] ['num'];
						
						$jssLim = $diagnosticModel->getMinMaxLimitForDiagnosticQuestions ( 'jss', $assessmentTypeId );
						
						$jssMin = $jssLim [0] ['limit_min'];
						
						$jssMax = $jssLim [0] ['limit_max'];
						
						if ($jssCount < $jssMin || $jssCount > $jssMax) 

						{
							
							if ($jssMin == $jssMax)
								
								$this->apiResult ["message"] = "Number of Judgement statements in KPA " . ($kpa + 1) . "> Key Question " . ($kq + 1) . " > Sub Question " . ($cq + 1) . " must be must be $jssMin";
							
							else
								
								$this->apiResult ["message"] = "Number of Judgement statements in KPA " . ($kpa + 1) . "> Key Question " . ($kq + 1) . " > Sub Question " . ($cq + 1) . " must be between $jssMin and $jssMax";
							
							$this->apiResult ["type"] = 'js';
							
							$this->apiResult ["cqid"] = $cq_id;
							
							$this->apiResult ["kqid"] = $kq_id;
							
							$this->apiResult ["kpaId"] = $kpa_id;
							
							return;
						}
					}
				}
			}
			
			// submit diagnostic and set is published to 1
			
			$isPublished = 1;
			if($parentId>0 && $equivalenceId>0 && $langIdOriginal>0){
                            
                            
                                if (! ($diagnosticModel->updateDiagnosticLang ( $diagnosticId, $diagnosticName, $isPublished, $langId ,$parentId, $this->user ['user_id'] ))){
						
                                        $this->apiResult ["message"] = "There was error in publishing diagnostic";

                                        return;
                                
                                }
                            
                        }else{
                            
                                if (! $diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, NULL, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations, $langId, $this->user ['user_id'] )) 

                                {

                                        $this->apiResult ["message"] = "There was error in publishing diagnostic";

                                        return;
                                }
                        
                        }
                        
			$this->apiResult ["status"] = 1;
			
			$this->apiResult ["message"] = "Diagnostic has been published successfully";
		}
	}
	function checkPaymentSelfReviewAction() {
		if ((in_array ( 6, $this->user ['role_ids'] ) || in_array ( 5, $this->user ['role_ids'] )) && $this->user ['has_view_video'] != 1 && $this->user ['is_web'] == 1) // principal and school admin have to view video for self-review
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		
		else if (in_array ( "create_self_review", $this->user ['capabilities'] )) {
			
			$clientModel = new clientModel ();
			
			$clientId = $this->user ['client_id'];
			
			$assessmentModel = new assessmentModel ();
			
			$ReviewTypeProductsAvailed = $assessmentModel->getReviewTypeProductsAvailed ( 1, $clientId );
			
			foreach ( $ReviewTypeProductsAvailed as $availedProduct ) 

			{
				
				// print_r($availedProduct);die;
				
				if ($availedProduct ['active'] <= 0 || $availedProduct ['isPmtApproved'] <= 0) 

				{
					
					$this->apiResult ["message"] = "Please pay for the existing self-reviews to create more self-reviews";
					
					$this->apiResult ["auth"] = 0;
					
					$this->apiResult ["status"] = 1;
					
					return;
				}
			}
			
			$this->apiResult ["message"] = "";
			
			$this->apiResult ["auth"] = 1;
			
			$this->apiResult ["status"] = 1;
		} else {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		}
	}
	function saveDiagnosticAction() {
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['assessmentId'] )) {
			
			$this->apiResult ["message"] = "Diagnostic review type can not be empty\n";
		} else if (empty ( $_POST ['langId'] )) {
			
			$this->apiResult ["message"] = "Diagnostic language can not be empty\n";
		}else if (empty ( $_POST ['diagnosticName'] )) {
			
			$this->apiResult ["message"] = "Diagnostic Name can not be empty\n";
		} 

		else {
			
			$dig_image_id = isset ( $_POST ['dig_image_id'] ) ? $_POST ['dig_image_id'] : NULL;
			// assessor recommendations
			$is_kpa_recommendations = empty ( $_POST ['kpa_recommendations'] ) ? 0 : $_POST ['kpa_recommendations'];
			$is_kq_recommendations = empty ( $_POST ['kq_recommendations'] ) ? 0 : $_POST ['kq_recommendations'];
			$is_cq_recommendations = empty ( $_POST ['cq_recommendations'] ) ? 0 : $_POST ['cq_recommendations'];
			$is_js_recommendations = empty ( $_POST ['js_recommendations'] ) ? 0 : $_POST ['js_recommendations'];
			
			$diagnosticModel = new diagnosticModel ();
			
			$assessmentId = $_POST ['assessmentId'];
			
			$diagnosticName = trim ( $_POST ['diagnosticName'] );
			
			$diagnosticId = empty ( $_POST ['diagnosticId'] ) ? 0 : $_POST ['diagnosticId'];
                        
                        $langId = empty ( $_POST ['langId'] ) ? 0 : $_POST ['langId'];
                        
                        $parentId=isset($_POST['parentdiaId'])?$_POST['parentdiaId']:0;
                        $equivalenceId=isset($_POST['equivalenceId'])?$_POST['equivalenceId']:0;
                        $langIdOriginal=isset($_POST['langIdOriginal'])?$_POST['langIdOriginal']:0;
			
			$teacherCategoryId = empty ( $_POST ['teacherCategoryId'] ) ? 0 : $_POST ['teacherCategoryId'];
			
			$isPublished = 0;
			
			if ($assessmentId == 2 && ! $teacherCategoryId) 

			{
				
				$this->apiResult ["message"] = "Diagnostic teacher type can not be empty\n";
				
				return;
			}
			
                        if($parentId>0 && $equivalenceId>0 && $langIdOriginal>0){
                            
                            if($diagnosticId>0){
                                if (! ($diagnosticModel->updateDiagnosticLang ( $diagnosticId, $diagnosticName, $isPublished, $langId ,$parentId, $this->user ['user_id'] )))
						return;
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["diagnosticId"] = $diagnosticId;
				
				$this->apiResult ["message"] = "Diagnostic has been updated successfully";
                                }else{
                                $this->apiResult ["status"] = 0;
				
				$this->apiResult ["diagnosticId"] = $diagnosticId;
				
				$this->apiResult ["message"] = "Unknown Problem";
                                }
                            
                        }else{
                        
			if ($diagnosticId == 0) // if diagnostic doesnt exist create it

			{
				
				$this->db->start_transaction ();
				
				$userId = $this->user ['user_id'];
				
				$dId = $diagnosticModel->createDiagnostic ( $assessmentId, $diagnosticName, $isPublished, $userId, $dig_image_id, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations, $langId );
				
				if (! $dId) 

				{
					
					$this->db->rollback ();
					
					return;
				}
				
				if (! $diagnosticModel->createDiagnosticRatingScheme ( $dId, $assessmentId )) 

				{
					
					$this->db->rollback ();
					
					return;
				}
				
				if ($assessmentId == 2 || $assessmentId == 4) 

				{
					if($assessmentId == 4){
                                        $teacherCategoryId=$diagnosticModel->getStudentCatId();    
                                        }
					if (! $diagnosticModel->createDiagnosticTeacherCategory ( $dId, $teacherCategoryId )) 

					{
						
						$this->db->rollback ();
						
						return;
					}
				}
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["diagnosticId"] = $dId;
				
				$this->apiResult ["message"] = "Diagnostic has been created successfully";
				
				$this->db->commit ();
			} 

			else {
				
				if (! ($diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, $dig_image_id, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations, $langId, $this->user ['user_id'] )))
					
					return;
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["diagnosticId"] = $diagnosticId;
				
				$this->apiResult ["message"] = "Diagnostic has been updated successfully";
			}
		}
                
                }
	}
	function saveFilterAction() {
		
		// print_r($_POST['filter_name']);
		
		// echo count($_POST['attr_val']);
		if (! (in_array ( 1, $this->user ['role_ids'] ) || in_array ( 2, $this->user ['role_ids'] ) || in_array ( 6, $this->user ['role_ids'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['fliter_name'] )) {
			
			$this->apiResult ["message"] = "Filter name cannot be empty\n";
		} 

		else if (empty ( $_POST ['attr_id'] )) {
			
			$this->apiResult ["message"] = "Parameter cannot be empty\n";
		} 

		else if (empty ( $_POST ['operator_id'] )) {
			
			$this->apiResult ["message"] = "Operator cannot be empty\n";
		} 

		else if (empty ( $_POST ['attr_val'] )) {
			
			$this->apiResult ["message"] = "Value cannot be empty\n";
		} else if (empty ( $_POST ['f_attr_val'] )) {
			
			$this->apiResult ["message"] = "First Value cannot be empty\n";
		} 

		else if (empty ( $_POST ['s_attr_val'] )) {
			
			$this->apiResult ["message"] = "Second Value cannot be empty\n";
		} 

		else {
			
			$filter_id = empty ( $_POST ['filter_id'] ) ? 0 : $_POST ['filter_id'];
			
			$isEdit = 0;
			
			$customreportModel = new customreportModel ();
			
			$filter_name = $_POST ['fliter_name'];
			
			// check duplicate filter name
			
			if ($filter_id == 0 && $customreportModel->checkDuplicateFilterName ( $filter_name )) 

			{
				
				$this->apiResult ["message"] = "Filter name already exists.";
				
				$this->apiResult ["status"] = 0;
				
				return;
			}
			
			// prepare datarray to be saved
			
			$num_attr = count ( $_POST ['attr_id'] );
			
			$dataArray = array ();
			
			$error = 0;
			
			// print_r($dataArray);
			
			// print_r($_POST["mul_attr_val_2"]);die;
			
			$this->db->start_transaction ();
			
			if (($filter_id > 0 && $customreportModel->updateFilter ( $filter_id, $filter_name ) && $isEdit = 1 && $filter_data = $customreportModel->getFilterData ( $filter_id )) || ($filter_id = $customreportModel->saveFilter ( $filter_name, $this->user ['user_id'] ))) 

			{
				
				$this->apiResult ["filter_id"] = $filter_id;
				
				// delete all existing data in sub tables if edit
				
				if ($isEdit > 0 && ! empty ( $filter_data )) 

				{
					
					foreach ( $filter_data as $attr ) 

					{
						
						if (! $customreportModel->deleteSubFilterAttr ( $attr ['filter_instance_id'] ))
							$error = 1 && $message = "Error in updating data in sub-attributes.";
						if (! $customreportModel->deleteFilterAttrMulVals ( $attr ['filter_instance_id'] ))
							
							$error = 1 && $message = "Error in updating data.";
					}
					
					if (! $customreportModel->deleteFilterAttr ( $filter_id ))
						
						$error = 1 && $message = "Error in updating data.";
				}
				
				if ($error > 0) 

				{
					
					$this->db->rollback ();
					
					$this->apiResult ["message"] = $message;
					
					$this->apiResult ["status"] = 0;
					
					return;
				}
				
				for($i = 0; $i < $num_attr; $i ++) 

				{
					
					if ($_POST ['operator_id'] [$i] == 8) // IN operatr

					{
						
						$dataArray [$i] ["filter_attr_id"] = $_POST ['attr_id'] [$i];
						
						$dataArray [$i] ["filter_operator"] = $_POST ['operator_id'] [$i];
						
						$dataArray [$i] ["filter_attr_value"] = '0';
						
						$dataArray [$i] ["filter_attr_value_text"] = '';
						
						$dataArray [$i] ["filter_f_value"] = '';
						
						$dataArray [$i] ["filter_s_value"] = '';
						
						$dataArray [$i] ["filter_mul_value"] = $_POST ["mul_attr_val_$i"];
					} 

					elseif ($_POST ['operator_id'] [$i] == 7) 

					{ // check if operator is BETWEEN If yes then get first and second value
						
						$dataArray [$i] ["filter_attr_id"] = $_POST ['attr_id'] [$i];
						
						$dataArray [$i] ["filter_operator"] = $_POST ['operator_id'] [$i];
						
						$dataArray [$i] ["filter_attr_value"] = 0;
						
						$dataArray [$i] ["filter_attr_value_text"] = '';
						
						if ($dataArray [$i] ["filter_attr_id"] == 18) // if attribute is date of review get fdate and sdate
{
							if (empty ( $_POST ['fdate'] ) || empty ( $_POST ['sdate'] )) {
								$this->apiResult ['message'] = "From Date/To date cannot be empty";
								$this->db->rollback ();
								return;
							}
							$dataArray [$i] ["filter_f_value"] = $_POST ['fdate'];
							$dataArray [$i] ["filter_s_value"] = $_POST ['sdate'];
						} else {
							$dataArray [$i] ["filter_f_value"] = $_POST ['f_attr_val'] [$i];
							$dataArray [$i] ["filter_s_value"] = $_POST ['s_attr_val'] [$i];
						}
						
						if (trim ( $dataArray [$i] ["filter_f_value"] ) == trim ( $dataArray [$i] ["filter_s_value"] )) 

						{
							
							$error = 1;
							
							$message = "First and second value cannot be same.";
						}
					} 

					else 

					{ // check if attribute is numeric and operator is not BETWEEN, get text value
						
						$dataArray [$i] ["filter_attr_id"] = $_POST ['attr_id'] [$i];
						
						$dataArray [$i] ["filter_operator"] = $_POST ['operator_id'] [$i];
						
						$dataArray [$i] ["filter_attr_value"] = $_POST ['attr_val'] [$i];
						// if subrow exists save it
						// $dataArray[$i]["filter_attr_value"] = !empty($_POST['attr_val'][$i])?$_POST['attr_val'][$i]:$_POST['question'][$dataArray[$i]["filter_attr_id"]];
						
						$dataArray [$i] ["filter_attr_value_text"] = '';
						
						$dataArray [$i] ["filter_f_value"] = '';
						
						$dataArray [$i] ["filter_s_value"] = '';
					}
					
					// save sub attributes
					if (! empty ( $_POST ['subattr_id'] [$dataArray [$i] ["filter_attr_id"]] ) && ! empty ( $_POST ['suboperator_id'] [$dataArray [$i] ["filter_attr_id"]] ) && ! empty ( $_POST ['subcardinality_id'] [$dataArray [$i] ["filter_attr_id"]] ) && ! empty ( $_POST ['subcriteria_id'] [$dataArray [$i] ["filter_attr_id"]] )) {
						$dataArray [$i] ["sub_filter_attr_id"] = $_POST ['subattr_id'] [$dataArray [$i] ["filter_attr_id"]];
						$dataArray [$i] ["sub_operator_id"] = $_POST ['suboperator_id'] [$dataArray [$i] ["filter_attr_id"]];
						$dataArray [$i] ["sub_cardinality_id"] = $_POST ['subcardinality_id'] [$dataArray [$i] ["filter_attr_id"]];
						$dataArray [$i] ["sub_criteria_id"] = $_POST ['subcriteria_id'] [$dataArray [$i] ["filter_attr_id"]];
						$dataArray [$i] ["sub_attr_max_cardinality"] = $_POST ['max_cardinality'] [$dataArray [$i] ["filter_attr_id"]];
					}
					
					if ($error > 0) 

					{
						
						$this->db->rollback ();
						
						$this->apiResult ["message"] = $message;
						
						$this->apiResult ["status"] = 0;
						
						return;
					}
				}
				
				for($i = 0; $i < $num_attr; $i ++) {
					
					$filter_instance_id = $customreportModel->saveFilterAttr ( $filter_id, $dataArray [$i] ["filter_attr_id"], $dataArray [$i] ["filter_operator"], $dataArray [$i] ["filter_attr_value"], $dataArray [$i] ["filter_attr_value_text"], $dataArray [$i] ["filter_f_value"], $dataArray [$i] ["filter_s_value"] );
					
					// if there is filter subattr
					if ($filter_instance_id && ! empty ( $dataArray [$i] ["sub_filter_attr_id"] )) {
						$is_sub_added = $customreportModel->saveFilterSubAttr ( $filter_instance_id, $dataArray [$i] ["sub_filter_attr_id"], $dataArray [$i] ["sub_operator_id"], $dataArray [$i] ["sub_criteria_id"], $dataArray [$i] ["sub_cardinality_id"], $dataArray [$i] ["sub_attr_max_cardinality"] );
						if (! $is_sub_added) {
							// echo $filter_instance_id;
							// echo "atts: ",$dataArray[$i]["sub_filter_attr_id"], $dataArray[$i]["sub_operator_id"], $dataArray[$i]["sub_cardinality_id"], $dataArray[$i]["sub_criteria_id"];
							$this->db->rollback ();
							$this->apiResult ["message"] = "Error while saving filter sub attributes";
							$this->apiResult ["status"] = 0;
							return;
						}
					}
					
					if (! $filter_instance_id) 

					{
						
						$this->db->rollback ();
						
						$this->apiResult ["message"] = "Error while saving filter attributes";
						
						$this->apiResult ["status"] = 0;
						
						return;
					}
					
					if ($dataArray [$i] ["filter_operator"] == 8) 

					{
						
						foreach ( $dataArray [$i] ["filter_mul_value"] as $mul_val )
							
							if (! $customreportModel->saveFilterAttrMulVals ( $filter_instance_id, $mul_val )) 

							{
								
								$this->db->rollback ();
								
								$this->apiResult ["message"] = "Error while saving filter";
								
								$this->apiResult ["status"] = 0;
								
								return;
							}
					}
				}
				
				$this->db->commit ();
				
				$this->apiResult ["message"] = "Filter saved successfully";
				
				$this->apiResult ["status"] = 1;
			} 

			else {
				
				$this->db->rollback ();
				
				$this->apiResult ["message"] = "Error while saving filter";
				
				$this->apiResult ["status"] = 0;
			}
		}
	}
	function getSelectionLinkAction() {
		if (! (in_array ( 1, $this->user ['role_ids'] ) || in_array ( 2, $this->user ['role_ids'] ))) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['attr_id'] ) || empty ( $_POST ['label'] ) || empty ( $_POST ['sno'] ) || empty ( $_POST ['frm'] )) {
			$this->apiResult ["message"] = "Attribute name cannot be empty\n";
		} else {
			$customreportModel = new customreportModel ();
			$attr_id = $_POST ['attr_id'];
			$label = $_POST ['label'];
			$sno = $_POST ['sno'];
			$frm = $_POST ['frm'];
			$selectionBoxHtml = $customreportModel->getSelectionBox ( $attr_id, $label, $sno, $frm );
			$this->apiResult ["content"] = $selectionBoxHtml;
			$this->apiResult ["message"] = "Success";
			$this->apiResult ["status"] = 1;
		}
	}
	function loadOperatorsAndValuesAction() {
		if (! (in_array ( 1, $this->user ['role_ids'] ) || in_array ( 2, $this->user ['role_ids'] ) || in_array ( 6, $this->user ['role_ids'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['attr_id'] )) {
			
			$this->apiResult ["message"] = "Parameter name cannot be empty\n";
		} 

		else {
			
			$customreportModel = new customreportModel ();
			
			$attr_id = $_POST ['attr_id'];
			
			$this->apiResult ["operators"] = $customreportModel->getAttrOperators ( $attr_id );
			
			// for fee and student strength dont fetch values from table
			
			switch ($attr_id) 

			{ //
				
				case - 100 :
					
					$this->apiResult ["values"] = 'na';
					
					break;
				
				default :
					$this->apiResult ["values"] = $customreportModel->getAttrValues ( $attr_id, $_POST ['csId'] );
					;
			}
			
			$this->apiResult ["status"] = 1;
		}
	}
	function getFilterSchoolsAction() 

	{
		if (! (in_array ( "view_all_assessments", $this->user ['capabilities'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['filter_name'] )) {
			
			$this->apiResult ["message"] = "Filter name cannot be empty\n";
		} 

		else {
			
			$customreportModel = new customreportModel ();
			
			$queryParams = '1=1';
			
			$filter_id = $_POST ['filter_name'];
			
			$resParams = $customreportModel->applyFilterQuery ( $filter_id );
			
			$temp = 0;
			
			foreach ( $resParams as $param ) 

			{
				
				if ($param ['filter_table'] == 'd_fees' || $param ['filter_table'] == 'd_school_strength') {
					
					$temp = $customreportModel->getStaticTableVals ( $param ['filter_table'], $param ['filter_table_col_id'], $param ['filter_table_col_name'], $param ['value'] );
					
					$param ['value'] = $temp ['staticCol'];
					
					$param ['filter_table'] == 'd_fees' ? ($param ['filter_table_col_id'] = 'annual_fee') : ($param ['filter_table'] == 'd_school_strength' ? ($param ['filter_table_col_id'] = 'no_of_students') : '');
				} elseif ($param ['filter_table_col_id'] == 'province')
					$param ['value'] = "'" . addslashes ( $param ['value'] ) . "'";
				
				$queryParams .= ' AND ' . $param ['filter_table_col_id'] . ' ' . $param ['operator_text'] . ' ' . ($param ['operator_text'] == 'IN' ? '(' . $param ['value'] . ')' : $param ['value']);
			}
			
			// echo $queryParams;
			
			$result = $customreportModel->getAQSclients ( $queryParams );
			
			$this->apiResult ["schools"] = $result;
			
			$this->apiResult ["message"] = "Success";
			
			$this->apiResult ["status"] = 1;
		}
	}
	function saveNetworkReportAction() 

	{
		if (! (in_array ( "view_all_assessments", $this->user ['capabilities'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['report_id'] )) {
			
			$this->apiResult ["message"] = "Report Id cannot be empty\n";
		} else if (empty ( $_POST ['report_name'] ) || trim ( $_POST ['report_name'] ) == '') {
			
			$this->apiResult ["message"] = "Report name cannot be empty\n";
		} else if (empty ( $_POST ['filter_name'] )) {
			
			$this->apiResult ["message"] = "Filter Id cannot be empty\n";
		} else if (empty ( $_POST ['review_experience'] )) {
			
			$this->apiResult ["message"] = "Review Experience cannot be empty\n";
		} else if (empty ( $_POST ['clients'] )) {
			
			$this->apiResult ["message"] = "Selected Schools cannot be empty\n";
		} 

		else {
			
			$customreportModel = new customreportModel ();
			
			$report_id = $_POST ['report_id'];
			
			$filter_id = $_POST ['filter_name'];
			
			$report_name = $_POST ['report_name'];
			
			$review_experience = $_POST ['review_experience'];
			
			$review_experience = implode ( '~', $review_experience );
			
			$clients = $_POST ['clients'];
			
			$clients = explode ( ',', $clients );
			
			$include_self_review = empty ( $_POST ['include_self_review'] ) ? 0 : 1;
			$is_validated = empty ( $_POST ['is_validated'] ) ? 0 : 1;
			
			// check network with same name exists or not
			
			if ($customreportModel->checkDuplicateReportName ( $report_name )) 

			{
				
				$this->apiResult ["message"] = "Network report name already exists.";
				
				$this->apiResult ["status"] = 0;
				
				return;
			}
			
			$this->db->start_transaction ();
			
			$networkReportId = $customreportModel->saveNetworkReport ( $report_id, $report_name, $filter_id, $review_experience, $include_self_review, $is_validated );
			
			if (! $networkReportId) 

			{
				
				$this->db->rollback ();
				
				$this->apiResult ["message"] = "Error in saving report.";
			} 

			else 

			{
				
				foreach ( $clients as $client_id )
					
					if (! $customreportModel->saveNetworkReportClients ( $networkReportId, $client_id )) {
						
						$this->db->rollback ();
						
						$this->apiResult ["message"] = "Error in saving report.";
					}
			}
			
			$this->db->commit ();
			
			$this->apiResult ["message"] = "Network report saved successfully.";
			
			$this->apiResult ["network_report_id"] = $networkReportId;
			
			$this->apiResult ["status"] = 1;
		}
	}
	function getNetworkReportDataAction() 

	{
		if (! (in_array ( "view_all_assessments", $this->user ['capabilities'] ))) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['network_report_id'] )) {
			
			$this->apiResult ["message"] = "Network Report Id cannot be empty\n";
		} 

		else {
			
			$customreportModel = new customreportModel ();
			
			$networkReportId = $_POST ['network_report_id'];
			
			$networkReportData = $customreportModel->getNetworkReportData ( $networkReportId );
			
			$this->apiResult ["data"] = $networkReportData;
			
			// $this->apiResult["message"]="Network report saved successfully.";
			
			$this->apiResult ["status"] = 1;
		}
	}
	
	// function for adding language Row on 26-05-2016 by Mohit Kumar
	function addLanguageRowAction() {
		if (empty ( $_POST ['sn'] )) {
			
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} else {
			
			$objUserModel = new userModel ();
			
			$this->apiResult ["status"] = 1;
			
			$this->apiResult ["content"] = $objUserModel->getLanguageHTMLRow ( $_POST ['sn'] );
		}
	}
	
	// function for updating the user profile on 27-05-2016 by Mohit Kumar
	function updateUserProfileAction() {
		//error_reporting ( ~ E_NOTICE & ~ E_WARNING );	
                $uniqueID = "";
                $whatsAppStatus = 0;
                $moodle_user=0;
                $dob_yy=$dob_mm =$dob_dd='';
                                    $dob='';
                if(isset($_POST['dob_yy']) && $_POST['dob_yy']!=''){
                    $dob_yy = $_POST['dob_yy'];   

                }
                if(isset($_POST['dob_mm']) && $_POST['dob_mm']!=''){
                    $dob_mm = $_POST['dob_mm']; 

                }
                if(isset($_POST['dob_dd']) && $_POST['dob_dd']!=''){
                    $dob_dd = $_POST['dob_dd']; 

                }
                if($dob_yy && $dob_mm && $dob_dd){
                                        $dob = $dob_dd."-".$dob_mm."-".$dob_yy;
                                        $dob = date('d-m-Y', strtotime($dob));
                 }
                                    
                //echo "<pre>";print_r($_POST);
		if (empty ( $_POST ['first_name'] ) || empty ( $_POST ['last_name'] )) {
			$this->apiResult ["message"] = "First and Last Name cannot be empty\n";
		} else if (empty ( $_POST ['id'] )) {
			$this->apiResult ["message"] = "User ID missing\n";
		} else if (empty ( $_POST ['email'] )) {
			$this->apiResult ["message"] = "Field is required: 'Email cannot be empty'\n";
		}else if(!isset($_POST['roles'])){ 
                   $this->apiResult ["message"] = "Field is required: 'Roles cannot be empty'\n"; 
                    
                }if (empty($dob) && (isset($_POST ['is_submit']) && $_POST ['is_submit'] == 1 || isset($_POST ['submit_value']) && $_POST ['submit_value'] == 1)) {
                
                                           $this->apiResult ["message"] = "Please select a valid DOB.\n";
                }    
                else {
                        
			$user_id = trim ( $_POST ['id'] );
                       // echo "<pre>";print_r($_POST);
                        $is_admin=0;
                        if(!isset($_POST['contract_value'])) {
                            $_POST['contract_value'] = array(1,2,3,4,5);
                            if(isset($_POST['is_facilitator']) && $_POST['is_facilitator']==1){
                                 $_POST['contract_value'] = array(1,2,3,4,5,6,7,8,9,10);
                            }
                        }
                        if(isset($_POST ['is_admin']) && $_POST ['is_admin'] == 0) {
                            $is_admin = $_POST ['is_admin'];
                            //unset($_POST ['moodle_user']);
                            
                        }else if(isset($_POST ['is_admin']) && $_POST ['is_admin'] == 1) {
                            $is_admin = $_POST ['is_admin'];
                            unset($_POST ['term_condition']);
                        }
                        //echo $_POST ['moodle_user'];
                        if(isset($_POST ['moodle_user'])) {
                            $moodle_user = $_POST['moodle_user'];
                            unset($_POST ['moodle_user']);
                             
                        }
                        //echo "<pre>";print_r($_POST);die;
                        unset($_POST ['is_admin']);
                        unset($_POST ['is_facilitator']);
                        //print_r($_POST['contract_value']);die;
                        if(isset($_POST ['edit_request_from']))
                            unset($_POST ['edit_request_from']);
                        $currentUser=isset($this->user ['user_id'])?$this->user['user_id']:NULL;
                        
			if ($_POST ['table'] == 'd_aqs_team') {
				$user = array ();
			} else if ($_POST ['table'] == 'd_user') {
				$user = $this->userModel->getUserById ( $user_id );
			}
			if (empty ( $user ) && $_POST ['table'] == 'd_user') {
				$this->apiResult ["message"] = "User does not exist\n";
			} 
                        else {
				
				$name = trim ( $_POST ['first_name'] ) . " " . trim ( $_POST ['last_name'] );
				$checkUserId = $this->userModel->getUserIdByEmail ( strtolower ( trim ( $_POST ['email'] ) ) );
				if (!isset($_POST ['is_submit']) && preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $name ) != 1) {
					$this->apiResult ["message"] = "Invalid Characters are not allowed in Name.\n";
				} else if (!isset($_POST ['is_submit']) && preg_match ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", strtolower ( trim ( $_POST ['email'] ) ) ) == 0 && ($checkUserId == $_POST ['id'] || $checkUserId == false)) {
					$this->apiResult ["message"] = "Invalid email.\n";
				}else if ( !empty($_POST ['date_of_birth']) && $_POST['date_of_birth']!="0000-00-00" && $this->userModel->validateDate($_POST['date_of_birth'],1)){
                                        
                                            $this->apiResult ["message"] = "Invalid date of birth.\n";
				} else {

				   
                                        unset($_POST['dob_yy']);
                                        unset($_POST['dob_mm']);
                                        unset($_POST['dob_dd']);
                                        //echo"aaa". $dob;
                                        $_POST ['date_of_birth']=(!empty($dob) && $dob!="0000-00-00")?date("Y-m-d", strtotime($dob)):$dob;
					if (!isset($_REQUEST ['process'])) {
						if (! empty ( $_POST ['data'] )) {
							$_POST ['upload_document'] = $_POST ['data'] ['undefined-undefined-undefined-undefined'] ['files'] ;
						} else if (! empty ( $_POST ['files'] )) {
							$_POST ['upload_document'] = $_POST ['files'] ;
							unset ( $_POST ['files'] );
						} else {
							$_POST ['upload_document'] = '';
						}
						
						unset ( $_POST ['data'] );
						if (trim ( $_POST ['pincode'] ) != '') {
							if (strlen ( trim ( $_POST ['pincode'] ) ) == 6 && ctype_digit ( trim ( $_POST ['pincode'] ) )) {
								$_POST ['pincode'] = trim ( $_POST ['pincode'] );
								$error_pincode = 0;
							} else {
								$_POST ['pincode'] = trim ( $_POST ['pincode'] );
								$error_pincode = 1;
							}
						} else {
							$_POST ['pincode'] = trim ( $_POST ['pincode'] );
							$error_pincode = 0;
						}
						if (trim ( $_POST ['account_number'] ) != '') {
							if (strlen ( trim ( $_POST ['account_number'] ) ) >= 8 && ctype_alnum ( trim ( $_POST ['account_number'] ) )) {
								$_POST ['account_number'] = trim ( $_POST ['account_number'] );
								$error_account_number = 0;
							} else {
								$_POST ['account_number'] = trim ( $_POST ['account_number'] );
								$error_account_number = 1;
							}
						} else {
							$_POST ['account_number'] = trim ( $_POST ['account_number'] );
							$error_account_number = 0;
						}
						if (trim ( $_POST ['emergency_firstname'] ) != '') {
							if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', trim ( $_POST ['emergency_firstname'] ) ) != 1) {
								$_POST ['emergency_firstname'] = trim ( $_POST ['emergency_firstname'] );
							} else {
								$_POST ['emergency_firstname'] = trim ( $_POST ['emergency_firstname'] );
							}
						} else {
							$_POST ['emergency_firstname'] = '';
						}
						if (trim ( $_POST ['emergency_lastname'] ) != '') {
							if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', trim ( $_POST ['emergency_lastname'] ) ) != 1) {
								$_POST ['emergency_lastname'] = trim ( $_POST ['emergency_lastname'] );
							} else {
								$_POST ['emergency_lastname'] = trim ( $_POST ['emergency_lastname'] );
							}
						} else {
							$_POST ['emergency_lastname'] = '';
						}
						if (($_POST ['emergency_firstname'] != '' && $_POST ['emergency_lastname'] != '') || $_POST ['emergency_firstname'] == '' && $_POST ['emergency_lastname'] == '') {
							$error_emer_name = 0;
						} else {
							$error_emer_name = 1;
						}
						if (trim ( $_POST ['emergency_pincode'] ) != '') {
							if (strlen ( trim ( $_POST ['emergency_pincode'] ) ) == 6 && ctype_digit ( trim ( $_POST ['emergency_pincode'] ) )) {
								$_POST ['emergency_pincode'] = trim ( $_POST ['emergency_pincode'] );
								$error_emergency_pincode = 0;
							} else {
								$_POST ['emergency_pincode'] = trim ( $_POST ['emergency_pincode'] );
								$error_emergency_pincode = 1;
							}
						} else {
							$_POST ['emergency_pincode'] = trim ( $_POST ['emergency_pincode'] );
							$error_emergency_pincode = 0;
						}
						if (trim ( $_POST ['emergency_email'] ) != '') {
							if (preg_match ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", strtolower ( trim ( $_POST ['emergency_email'] ) ) ) == 1) {
								$_POST ['emergency_email'] = strtolower ( trim ( $_POST ['emergency_email'] ) );
								$error_emergency_email = 0;
							} else {
								$_POST ['emergency_email'] = strtolower ( trim ( $_POST ['emergency_email'] ) );
								$error_emergency_email = 1;
							}
						} else {
							$_POST ['emergency_email'] = strtolower ( trim ( $_POST ['emergency_email'] ) );
							$error_emergency_email = 0;
						}
						// $languageKeys = array_keys(array_column($_POST['languageData'], 'language_id'), 'other');
						$languageKeys1 = array_values ( $_POST ['languageData'] );
						$languageKeys = array ();
						foreach ( $languageKeys1 as $key => $value ) {
							if ($value ['language_id'] == 'other') {
								$languageKeys [] = $key;
							}
						}
						
						if (empty ( $languageKeys )) {
							$error_language = 0;
						} else {
							$otherLanguage = array ();
							foreach ( $languageKeys as $value ) {
								$otherLanguage [] = trim ( $_POST ['other_language' . ($value + 1)] );
							}
							if (in_array ( '', $otherLanguage )) {
								$error_language = 1;
							} else {
								$error_language = 0;
							}
						}
					} else {
						if (! empty ( $_POST ['password'] ) && strlen ( $_POST ['password'] ) >= 6) {
							$_POST ['password'] = $_POST ['password'];
						} else {
							$_POST ['password'] = '';
						}
						//
						if (! empty ( $_POST ['confirm_password'] ) && strlen ( $_POST ['confirm_password'] ) >= 6) {
							$_POST ['confirm_password'] = $_POST ['confirm_password'];
						} else {
							$_POST ['confirm_password'] = '';
						}
						
						if (($_POST ['password'] != '' && $_POST ['confirm_password'] != '' && $_POST ['password'] == $_POST ['confirm_password']) || ($_POST ['password'] == '' && $_POST ['confirm_password'] == '' && $_POST ['process'] == '')) {
							$error_password = 0;
						} else {
							$error_password = 1;
						}
					}
					if (! empty ( $_POST ['cell_number'] ) &&  empty ( $_POST ['whatsapp_num'] )) {
							$_POST ['whatsapp_num'] = $_POST ['cell_number'];
							$_POST ['wap_country_code'] = $_POST ['cell_country_code'];
                                                        
						} 
					// echo $error_language;
					// die;
					
					// if($error_password==1 && $_REQUEST['process']!=''){
					// $this->apiResult["message"] = "Password too short. Minimum 6 characters required.\n";
					// } else if($error_account_number==1){
					// $this->apiResult["message"] = "Invalid account number.\n";
					// } else if($error_emer_name==1){
					// $this->apiResult["message"] = "Invalid emergency name.\n";
					// } else if($error_emergency_email==1){
					// $this->apiResult["message"] = "Invalid emergency email.\n";
					// } else if($error_emergency_pincode==1){
					// $this->apiResult["message"] = "Invalid emergency pincode.\n";
					// } else if($error_pincode==1){
					// $this->apiResult["message"] = "Invalid pincode.\n";
					// } else
					if ($error_language == 1) {
						$this->apiResult ["message"] = "Other language textbox can't empty.\n";
					} else {
						
						$objDianosticModel = new diagnosticModel ();
						$this->db->start_transaction ();
						if (OFFLINE_STATUS == TRUE) {
							$uniqueID = $this->db->createUniqueID ( 'updateAssessorProfile' );
						}
						/*
						 * if(!empty($_POST['languageData']['language_id1']['language_id'])){
						 * $i=1;
						 * foreach ($_POST['languageData'] as $key => $value) {
						 * if(!empty($value)){
						 * if($value['language_id']=='other'){
						 * $languageId = $objDianosticModel->getLanguageData($_POST['other_language'.$i], 'id');
						 * if($languageId>0){
						 * $value['language_id']=$languageId;
						 * unset($_POST['other_language'.$i]);
						 * } else {
						 * if($this->db->insert('d_language',array('language_name'=>$_POST['other_language'.$i]))){
						 * $value['language_id']= $this->db->get_last_insert_id();
						 * if(OFFLINE_STATUS==TRUE){
						 * //start---> call function for add other langauge on 10-08-2016 by Mohit Kumar
						 * $action_json = json_encode(array(
						 * 'language_name' => $_POST['other_language'.$i]
						 * ));
						 * $this->db->saveHistoryData($value['language_id'], 'd_language',$uniqueID, 'updateAssessorLanguageAdd',
						 * $value['language_id'], $_POST['other_language'.$i], $action_json,0,date('Y-m-d H:i:s'));
						 * //end---> call function for add other langauge on 10-08-2016 by Mohit Kumar
						 * }
						 * unset($_POST['other_language'.$i]);
						 * }
						 * }
						 * }
						 * }
						 * $_POST['languageData'][$key]=$value;
						 * $i++;
						 * }
						 * }
						 */
						
						if (isset($_POST ['is_submit']) && $_POST ['is_submit'] == 1 || isset($_POST ['submit_value']) && $_POST ['submit_value'] == 1) {
							$postData = $this->userModel->userProfileValidation ( $_POST,$is_admin );
							$errors = ! empty ( $postData ['errors'] ) ? $postData ['errors'] : array ();
						} else {
							$errors = array ();
						}
						
						if (count ( $errors )) {
							$this->apiResult ["message"] = "Data is either incorrect or blank.\n";
							$this->apiResult ["errors"] = $errors;
						} else {
							
							$alert = 1;
							$rolesUpdated = true;
							if ($_POST ['table'] == 'd_aqs_team' || $_POST ['table'] == 'd_AQS_team') {
								$user_id = $this->userModel->createUser ( strtolower ( trim ( $_POST ['email'] ) ), $_POST ['password'], $name, $_POST ['client_id'],0, date("Y-m-d H:i:s"),$currentUser );
								if (OFFLINE_STATUS == TRUE) {
									// start---> call function for add new user on 10-08-2016 by Mohit Kumar
									$action_user_json = json_encode ( array (
											'password' => $_POST ['password'],
											'name' => $name,
											'email' => strtolower ( trim ( $_POST ['email'] ) ),
											'client_id' => $_POST ['client_id'] 
									) );
									$this->db->saveHistoryData ( $user_id, 'd_user', $uniqueID, 'updateAssessorUserAdd', $user_id, strtolower ( trim ( $_POST ['email'] ) ), $action_user_json, 0, date ( 'Y-m-d H:i:s' ) );
									// end---> call function for add new user on 10-08-2016 by Mohit Kumar
								}
								$this->db->update ( $_POST ['table'], array (
										'user_added_flag' => 1 
								), array (
										'id' => $_POST ['id'] 
								) );
								if (OFFLINE_STATUS == TRUE) {
									$tapuserid = $this->db->get_last_insert_id ();
									$action_aqs_user_json = json_encode ( array (
											'table_name' => $_POST ['table'],
											'user_added_flag' => 1,
											'id' => $_POST ['id'] 
									) );
									$this->db->saveHistoryData ( $_POST ['id'], $_POST ['table'], $uniqueID, 'updateAssessorUserAQSFlag', $_POST ['id'], $_POST ['id'], $action_aqs_user_json, 0, date ( 'Y-m-d H:i:s' ) );
								}
								
								$this->db->insert ( 'h_tap_user_assessment', array (
										'tap_program_status' => 1,
										'user_id' => $user_id 
								) );
								if (OFFLINE_STATUS == TRUE) {
									$tapuserid = $this->db->get_last_insert_id ();
									$action_tab_user_json = json_encode ( array (
											'table_name' => 'h_tap_user_assessment',
											'tap_program_status' => 1,
											'user_id' => $user_id 
									) );
									$this->db->saveHistoryData ( $tapuserid, 'h_tap_user_assessment', $uniqueID, 'updateAssessorUserAssessment', $user_id, $user_id, $action_tab_user_json, 0, date ( 'Y-m-d H:i:s' ) );
								}
								foreach ( $_POST ['roles'] as $role ) {
									if (! $this->userModel->addUserRole ( $user_id, $role )) {
										$rolesUpdated = false;
										break;
									} else {
										if (OFFLINE_STATUS == TRUE) {
											$user_role_id = $this->db->get_last_insert_id ();
											// start--> call function for save history for new insert user in history table on 03-03-2016 by Mohit Kumar
											$action_user_role_json = json_encode ( array (
													'user_id' => $user_id,
													'role_id' => $role 
											) );
											$this->db->saveHistoryData ( $user_role_id, 'h_user_user_role', $uniqueID, 'updateAssessorUserRoleAdd', $user_id, $role, $action_user_role_json, 0, date ( 'Y-m-d H:i:s' ) );
											// end--> call function for save history for new insert user in history table on 03-03-2016 by Mohit Kumar
											$rolesAdded = true;
										}
									}
									if ($role == 4 && $this->db->addAlerts ( 'd_user', $user_id, $name, strtolower ( trim ( $_POST ['email'] ) ), 'CREATE_EXTERNAL_ASSESSOR' )) {
										$alertid = $this->db->get_last_insert_id ();
										if (OFFLINE_STATUS == TRUE) {
											$action_alert_json = json_encode ( array (
													'table_name' => 'd_user',
													'content_id' => $user_id,
													'content_title' => $name,
													'content_description' => strtolower ( trim ( $_POST ['email'] ) ),
													"type" => 'CREATE_EXTERNAL_ASSESSOR' 
											) );
											$this->db->saveHistoryData ( $alertid, 'd_alerts', $uniqueID, 'updateAssessorUserAlert', $user_id, strtolower ( trim ( $_POST ['email'] ) ), $action_alert_json, 0, date ( 'Y-m-d H:i:s' ) );
										}
										$alert = 1;
									}
								}
								$userUpdated = true;
							} else {
								$userUpdated = $this->userModel->updateUser ( $user_id, $name, $_POST ['password'],0,-1,date("Y-m-d H:i:s"),$currentUser,$moodle_user );
								if (OFFLINE_STATUS == TRUE) {
									// start---> call function for add new user on 10-08-2016 by Mohit Kumar
									$action_user_json = json_encode ( array (
											'password' => $_POST ['password'],
											'name' => $name,
											'user_id' => $user_id 
									) );
									$this->db->saveHistoryData ( $user_id, 'd_user', $uniqueID, 'updateAssessorUserAdd', $user_id, strtolower ( trim ( $_POST ['email'] ) ), $action_user_json, 0, date ( 'Y-m-d H:i:s' ) );
									// end---> call function for add new user on 10-08-2016 by Mohit Kumar
								}
							}
							
							$queryLanguageFailed = 0;
							if (! empty ( $_POST ['languageData'] ['language_id1'] ['language_id'] )) {
								$languageData = $this->userModel->getUserLanguage ( $user_id );								
								if (! empty ( $languageData )) {
									$this->db->delete ( 'h_user_language', array (
											'user_id' => $user_id 
									) );
									if (OFFLINE_STATUS == TRUE) {
										// start---> call function for add other langauge on 10-08-2016 by Mohit Kumar
										$action_language_delete_json = json_encode ( array (
												'user_id' => $user_id 
										) );
										$this->db->saveHistoryData ( $user_id, 'h_user_language', $uniqueID, 'updateAssessorLanguageDelete', $user_id, $user_id, $action_language_delete_json, 0, date ( 'Y-m-d H:i:s' ) );
										// end---> call function for add other langauge on 10-08-2016 by Mohit Kumar
									}
								}
								$i = 1;
								foreach ( $_POST ['languageData'] as $key => $value ) {
									if (! empty ( $value )) {
										if ($value ['language_id'] == 'other') {
											$languageId = $objDianosticModel->getLanguageData ( $_POST ['other_language' . $i], 'id' );
											if ($languageId > 0) {
												$value ['language_id'] = $languageId;
												unset ( $_POST ['other_language' . $i] );
											} else {
												if ($this->db->insert ( 'd_language', array (
														'language_name' => $_POST ['other_language' . $i] 
												) )) {
													$value ['language_id'] = $this->db->get_last_insert_id ();
													if (OFFLINE_STATUS == TRUE) {
														// start---> call function for add other langauge on 10-08-2016 by Mohit Kumar
														$action_json = json_encode ( array (
																'language_name' => $_POST ['other_language' . $i] 
														) );
														$this->db->saveHistoryData ( $value ['language_id'], 'd_language', $uniqueID, 'updateAssessorLanguageAdd', $value ['language_id'], $_POST ['other_language' . $i], $action_json, 0, date ( 'Y-m-d H:i:s' ) );
														// end---> call function for add other langauge on 10-08-2016 by Mohit Kumar
													}
													unset ( $_POST ['other_language' . $i] );
												} else {
													$queryLanguageFailed = 0;
												}
											}
										} else {
											unset ( $_POST ['other_language' . $i] );
										}
										if ($objDianosticModel->addLanguage ( array_merge ( $value, array (
												'user_id' => $user_id,
												'creation_date' => date ( 'Y-m-d H:i:s' ) 
										) ) )) {
											$queryLanguageFailed = 1;
											$language_id = $this->db->get_last_insert_id ();
											;
											if (OFFLINE_STATUS == TRUE) {
												// start---> call function for add other langauge on 10-08-2016 by Mohit Kumar
												$action_language_json = json_encode ( array_merge ( $value, array (
														'user_id' => $user_id,
														'creation_date' => date ( 'Y-m-d H:i:s' ) 
												) ) );
												$this->db->saveHistoryData ( $language_id, 'h_user_language', $uniqueID, 'updateAssessorUserLanguageAdd', $language_id, $user_id, $action_language_json, 0, date ( 'Y-m-d H:i:s' ) );
												// end---> call function for add other langauge on 10-08-2016 by Mohit Kumar
											}
										}
									}
									$_POST ['languageData'] [$key] = $value;
									
									$i ++;
								}
							} else {
								unset ( $_POST ['other_language1'] );
								$queryLanguageFailed = 1;
							}
							
							$assessmentData = isset($_POST ['assessment'])?$_POST ['assessment']:array();
                                                        if(isset($_POST ['assessment'])) {
                                                            unset ( $_POST ['assessment'] );
                                                        }
                                                        $uniqueID = isset($uniqueID)?$uniqueID:"";

                                                      //  echo "<pre>";print_r($_POST);
							$profileUpdated = $this->userModel->updateUserProfile ( $_POST, $user_id, $uniqueID );
							//if the user has already filled introductory assessment and submitted the form, it will not be reflected in the post data because fields are disabled in the introductory assessement	
							//print_r($this->userModel->getAssessorIntroductoryAssessment($user_id));die;
							if(!empty($_POST['submit_value']) && $_POST['submit_value']==1 && empty($assessmentData))
								$introductoryAssessment = 1;
							else
								$introductoryAssessment = $this->userModel->saveAssessorIntroductoryAssessment ( array_merge ( $assessmentData, array (
										'user_id' => $user_id 
								) ), $uniqueID );								
							
// print_r(array($userUpdated,$rolesUpdated,$profileUpdated,$queryLanguageFailed,$alert,$introductoryAssessment));
							if ($userUpdated && $rolesUpdated && $this->db->commit () && $profileUpdated && $queryLanguageFailed && $alert == 1 && $introductoryAssessment) {
								$this->apiResult ["status"] = 1;
								$this->apiResult ["message"] = "User Profile successfully updated";
							} else {
								$this->db->rollback ();
								$this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
							}
						}
					}
				}
			}
		}
	}
        
        // function for updating the user profile on 27-05-2016 by Mohit Kumar
	function updateUserIntroAssAction() {
		//error_reporting ( ~ E_NOTICE & ~ E_WARNING );	
                $uniqueID = "";
                $user_id = $_POST['user_id'];
                
      
								 
                //if the user has already filled introductory assessment and submitted the form, it will not be reflected in the post data because fields are disabled in the introductory assessement	
              // echo'<pre>'; print_r($_POST);die;;
                $finalArray = array();
               // foreach($_POST as $data) {
                if(isset($_POST['key_behaviour'])) {
                    $questiion_id = array_shift($_POST['key_behaviour']);
                    $finalArray['key_behaviour'] = array('question_id'=>$questiion_id,'answer_id'=>$_POST['key_behaviour']);
                    //$finalArray[] = $_POST['key_behaviour'];
                    //$finalArray['question_id'] = $data[0];
                }
                if(isset($_POST['leader_statement'])) {
                    //$finalArray['question_id'] = $_POST['leader_statement'][0];
                    $finalArray['leader_statement'] = isset($_POST['leader_statement'][1])?trim($_POST['leader_statement'][1]):'';
                    //$finalArray['question_id'] = $data[0];
                }
                if(isset($_POST['statement'])) {
                   // $finalArray['question_id'] = $_POST['statement'][0];
                    $finalArray['statement'] = isset($_POST['statement'][1])?trim($_POST['statement'][1]):'';
                    //$finalArray['question_id'] = $data[0];
                }
               /* if(isset($_POST['chool_rating'])) {
                   // $finalArray['question_id'] = $_POST['statement'][0];
                    $finalArray['chool_rating'] = $_POST['chool_rating'][1];
                    //$finalArray['question_id'] = $data[0];
                }*/
                if(isset($_POST['rating_text'])) {
                   // $finalArray['question_id'] = $_POST['statement'][0];
                    $finalArray['rating_text'] = isset($_POST['rating_text'][1])?trim($_POST['rating_text'][1]):'';
                    //$finalArray['question_id'] = $data[0];
                }
                if(isset($_POST['classroom_observation'])) {
                   // $finalArray['question_id'] = $_POST['statement'][0];
                   // $finalArray['classroom_observation'] = isset($_POST['classroom_observation'][1])?trim($_POST['classroom_observation'][1]):'';
                    //$finalArray['question_id'] = $data[0];
                    $questiion_id = array_shift($_POST['classroom_observation']);
                    $finalArray['classroom_observation'] = array('question_id'=>$questiion_id,'answer_id'=>$_POST['classroom_observation']);
                }
                if(isset($_POST['stakeholder_text'])) {
                   // $finalArray['question_id'] = $_POST['statement'][0];
                   // $finalArray['stakeholder_text'] = isset($_POST['stakeholder_text'][1])?trim($_POST['stakeholder_text'][1]):'';
                    $questiion_id = array_shift($_POST['stakeholder_text']);
                    $finalArray['stakeholder_text'] = array('question_id'=>$questiion_id,'answer_id'=>$_POST['stakeholder_text']);
                    //$finalArray['question_id'] = $data[0];
                }
                if(isset($_POST['key_performance'])) {
                     $questiion_id = array_shift($_POST['key_performance']);
                    $finalArray['key_performance'] = array('question_id'=>$questiion_id,'answer_id'=>$_POST['key_performance']);
                   // $finalArray['answer_id'][] = $_POST['key_performance'][1];
                    //$finalArray['question_id'] = $data[0];
                }
                if(isset($_POST['key_performance_text'])) {
                    //$finalArray['question_id'] = $_POST['key_performance'][0];
                    $finalArray['key_performance_text'] = isset($_POST['key_performance_text'][1])?trim($_POST['key_performance_text'][1]):'';
                    //$finalArray['question_id'] = $data[0];
                }
                if(isset($_POST['goal'])) {
                   // $finalArray['question_id'] = $_POST['key_performance'][0];
                    $finalArray['goal'] = isset($_POST['goal'][1])? trim($_POST['goal'][1]):'';
                    //$finalArray['question_id'] = $data[0];
                }
                if(isset($_POST['school_rating'])) {
                   // $finalArray['question_id'] = $_POST['key_performance'][0];
                    $finalArray['school_rating'] = isset($_POST['school_rating'][1])?trim($_POST['school_rating'][1]):'';
                    //$finalArray['question_id'] = $data[0];
                }
                if(isset($_POST['rating_text'])) {
                   // print_r($_POST['rating_text']);
                   // $finalArray['question_id'] = $_POST['statement'][0];
                   // $finalArray['stakeholder_text'] = isset($_POST['stakeholder_text'][1])?trim($_POST['stakeholder_text'][1]):'';
                    $questiion_id = array_shift($_POST['rating_text']);
                    $finalArray['rating_text'] = array('question_id'=>$questiion_id,'answer_id'=>$_POST['rating_text']);
                    //$finalArray['question_id'] = $data[0];
                }
                if(isset($_POST['school_rating_txt'])) {
                   // print_r($_POST['rating_text']);
                   // $finalArray['question_id'] = $_POST['statement'][0];
                   // $finalArray['stakeholder_text'] = isset($_POST['stakeholder_text'][1])?trim($_POST['stakeholder_text'][1]):'';
                    //$questiion_id = array_shift($_POST['rating_text']);
                    $finalArray['school_rating_txt'] = $_POST['school_rating_txt'];
                    //$finalArray['question_id'] = $data[0];
                }
                    // echo "<pre>";print_r($finalArray);die;
               // }
                $postData = array();
             
                //if(isset($finalArray['key_behaviour']['answer_id'])&& count($finalArray['key_behaviour']['answer_id'])>=1 ) {
                   // print_r($finalArray['key_behaviour']);
                    $score = $this->userModel->calculateScore($finalArray);
                //}
                //echo"ddd". $_POST['submit_value'];
                  //echo "<pre>";print_r($finalArray);DIE;
                if((!empty($_POST['is_submit']) && $_POST['is_submit']==1) || ($_POST['submit_value'] == 1) ){
                   //$finalArray['is_submit'] = 1;
                   $postData =  $this->userModel->validateIntroAss($finalArray);
                   $errors = ! empty ( $postData ['errors'] ) ? $postData ['errors'] : array ();
                   //echo "<pre>";print_r($postData['values']);die;
                   // $introductoryAssessment = 1;
                    if(isset($postData['errors']) && count($postData['errors'])) {
                        $this->apiResult ["message"] = "Data is either incorrect or blank.\n";
                        $this->apiResult ["errors"] = $errors;
                    }else{
                           $postData['values']['score'] = $score;
                         $introductoryAssessment = $this->userModel->saveIntroductoryAssessment ($postData['values'],$user_id,1);
                    }
                }
                else {
                        $finalArray['score'] = $score;
                        $introductoryAssessment = $this->userModel->saveIntroductoryAssessment ($finalArray,$user_id);								
                    }
                        if (isset($introductoryAssessment) && $introductoryAssessment) {
                                $this->apiResult ["status"] = 1;
                                if(isset($score)) 
                                    $this->apiResult ["score"] = $score;
                                $this->apiResult ["message"] = "User Profile successfully updated";
                        } else {
                                //$this->db->rollback ();
                                $this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
                        }
                
                
        }
                
        // function for updating the user profile on 27-05-2016 by Mohit Kumar
	function updateUserAssessmentAction() {
		//error_reporting ( ~ E_NOTICE & ~ E_WARNING );	
                $uniqueID = "";
		if (empty ( $_POST ['first_name'] ) && empty ( $_POST ['last_name'] )) {
			$this->apiResult ["message"] = "First and Last Name cannot be empty\n";
		} else if (empty ( $_POST ['id'] )) {
			$this->apiResult ["message"] = "User ID missing\n";
		} else if (empty ( $_POST ['email'] )) {
			$this->apiResult ["message"] = "Email cannot be empty\n";
		} else {
                    
                }
	}
	
	// FUNCTION for getting for getting new alert counts
	public function getAlertCountAction() {
		$count = $this->db->getAlertCount ();
		$this->apiResult ["status"] = 1;
		$this->apiResult ["totalCount"] = $count ['assessor_count'] + $count ['review_count'];
		$this->apiResult ["assessorCount"] = $count ['assessor_count'];
		$this->apiResult ["reviewCount"] = $count ['review_count'];
	}
        //Added by Vikas for workshop add
        function createWorkshopAction() {
               // echo "<pre>";print_r($_POST);die;
                $workshopModel = new WorkshopModel ();
                $workshop_attachment = 0;
                if(in_array(8,$this->user['role_ids'])){
                    $_POST ['workshop_programme']=1;
                }    
                if(!empty($_POST['workshop_upload'])) {
                    $workshop_attachment = 1;
                }
                $uploadFiles=array();
                $uploadFilesCat=array();
                if(!empty($_POST['workshop_upload'])){
                    $uploadFiles = $_POST['workshop_upload'];
                    $uploadFilesCat = $_POST['workshop_upload_cat'];
                }
                $_POST ['workshop_date_to']=(!empty($_POST ['workshop_date_to']) && $_POST['workshop_date_to']!="0000-00-00")?date("Y-m-d", strtotime($_POST ['workshop_date_to'])):$_POST ['workshop_date_to'];
                $_POST ['workshop_date_from']=(!empty($_POST ['workshop_date_from']) && $_POST['workshop_date_from']!="0000-00-00")?date("Y-m-d", strtotime($_POST ['workshop_date_from'])):$_POST ['workshop_date_from'];
               
                if (! in_array ( "manage_workshop", $this->user ['capabilities'] )) {
                                $this->apiResult ["message"] = "You are not authorized to perform this task.\n";
                }else if (empty ( trim($_POST ['workshop_name']) )) {
                                $this->apiResult ["message"] = "Workshop Title cannot be empty.\n";
                }else if (empty ( trim($_POST ['workshop_location']) )) {
                                $this->apiResult ["message"] = "Address cannot be empty.\n";
                }else if (empty ( $_POST ['workshop_date_from'] ) || $_POST['workshop_date_from']=="0000-00-00") {
                                $this->apiResult ["message"] = "Start Date cannot be empty.\n";
                }else if (empty ( $_POST ['workshop_date_to'] ) || $_POST['workshop_date_to']=="0000-00-00") {
                                $this->apiResult ["message"] = "End Date cannot be empty.\n";
                }else if ($_POST ['workshop_date_to']<$_POST['workshop_date_from']) {
                                $this->apiResult ["message"] = "End Date should not be less then Start Date.\n";
                }else if (empty ( $_POST ['workshop_programme'])) {
                                $this->apiResult ["message"] = "Programme cannot be empty.\n";
                }else if ($workshopModel->getUniqueWorkshop ( trim($_POST ['workshop_name']),$_POST ['workshop_date_from'],$_POST ['workshop_date_to'],trim($_POST ['workshop_location']))) {
				
		 $this->apiResult ["message"] = "Workshop already exists.\n";
		}else if (empty ( $_POST ['facilitator_id'])) {
                                $this->apiResult ["message"] = "LED cannot be empty.\n";
                }else if (isset($_POST ['externalReviewTeam'] ['member']) && in_array($_POST['facilitator_id'],$_POST ['externalReviewTeam'] ['member'])) {
                                $this->apiResult ["message"] = "Same Member cannot be assigned twice.\n";
                }else if(isset($_POST ['externalReviewTeam'] ['member']) && count($_POST ['externalReviewTeam'] ['member'])!=count(array_unique($_POST ['externalReviewTeam'] ['member']))){
                                $this->apiResult ["message"] = "Same Member cannot be assigned twice.\n";
                }else if(!empty($_POST['workshop_charges']) && !is_numeric($_POST['workshop_charges'])){
                    $this->apiResult ["message"] = "Charges for the Workshop should be numbers";
                }else{
                        //print_r($_POST);
                        $externalRoleClient = array ();
			
                        $facilitator_client_id=  empty($_POST['facilitator_client_id'])?'':$_POST['facilitator_client_id'];
                        $facilitator_id=$_POST['facilitator_id'];
                        $facilitator_payment=$_POST['facilitator_payment'];
                        $facilitator_role=1;
                        $led_array=array();
                        $led_array['facilitator_client_id']=$facilitator_client_id;
                        $led_array['facilitator_id']=$facilitator_id;
                        $led_array['facilitator_role']=$facilitator_role;
                        $led_array['facilitator_payment']=$facilitator_payment;
                        //print_r($led_array);
                        $i=0;
                        //$externalReviewTeam_client = $_POST ['externalReviewTeam'] ['clientId'];
                        //$externalReviewTeam_member = $_POST ['externalReviewTeam'] ['member'];
                        if (! empty ( $_POST ['externalReviewTeam'] ['clientId'] ))
				
				foreach ( $_POST ['externalReviewTeam'] ['clientId'] as $client ) 
				{					
					array_push ( $externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i]. '_' . $_POST ['externalReviewTeam'] ['facilitator_payment'] [$i] );					
					$i ++;
				}				
				// print_r($externalRoleClient);
			
			$externalReviewTeam = empty ( $_POST ['externalReviewTeam'] ['clientId'] ) ? '' : array_combine ( $_POST ['externalReviewTeam'] ['member'], $externalRoleClient );
                       // print_r($externalReviewTeam);
                        if($_POST['workshop_school']=="None of the mentioned"){
                            $_POST['workshop_school']=0;
                        }else{
                            $_POST['workshop_school_none']="";
                        }
                        $this->db->start_transaction();
                        if ($wid = $workshopModel->createWorkshop ( $_POST ['workshop_name'], $_POST ['workshop_location'],$_POST['workshop_school'],$_POST['workshop_school_none'] , $_POST ['workshop_programme'], $_POST ['prog_type_id'], $_POST ['workshop_charges'],$_POST ['workshop_payment_facilitator'],  $_POST ['workshop_date_from'] ,  $_POST ['workshop_date_to'],  $_POST['workshop_description'] , $led_array, $externalReviewTeam,$this->user['user_id'],$uploadFiles,$uploadFilesCat)) {
				//$this->db->addAlerts ( 'd_assessment', $aid, $_POST ['client_id'], $aid, 'CREATE_REVIEW' );
				//Sending mail to LED
                               // $wid = $workshopModel->createWorkshop ();
                                $userModel=new userModel();
                                
                                
                                $workshop_id = empty($wid)?0:$wid;
                                $workshopModel=new WorkshopModel();
                                $workshop = $workshopModel->getworkshopById($workshop_id);
                                $subroles=$workshop['subroles'];
                                $subroles = explode(',',$subroles);
                                foreach($subroles as $role=>$row){
                                        $exTeamClientId = explode('_',$row);
                                        if($exTeamClientId[2]==1){
                                            $led_id=$exTeamClientId[1];
                                            $user_led=$userModel->getUserById($led_id);
                                            $body_mail="Dear ".$user_led['name']."<br><br>You are nominated as <b>Workshop LEADER</b>.<br><br>The Details of workshop are:<br><b>Title of Workshop:</b> ".$_POST ['workshop_name']."<br><b>Conducted on:</b>".$_POST ['workshop_date_from']." to ".$_POST ['workshop_date_to']."<br><b>Location/Address:</b> ".$_POST ['workshop_location']."<br><br>This is auto generated email, need not to reply<br><br>Thanks";
                                            //sendEmail(''.$user_led['email'].'',''.$user_led['name'].'','shraddha.adhyayan@gmail.com','Shraddha Khedekar','Adhyayan:: Workshop - '.$_POST ['workshop_name'].'',$body_mail,'poonam.choksi@adhyayan.asia');

                                        }else{
                                            $f_id=$exTeamClientId[1];
                                            $user_facilitator=$userModel->getUserById($f_id);
                                            $body_mail="Dear ".$user_facilitator['name']."<br><br>You are nominated as <b>Workshop Co-facilitator</b>.<br><br>The Details of workshop are:<br><b>Title of Workshop:</b> ".$_POST ['workshop_name']."<br><b>Conducted on:</b>".$_POST ['workshop_date_from']." to ".$_POST ['workshop_date_to']."<br><b>Location/Address:</b> ".$_POST ['workshop_location']."<br><br>This is auto generated email, need not to reply<br><br>Thanks";
                                            //sendEmail(''.$user_facilitator['email'].'',''.$user_facilitator['name'].'','shraddha.adhyayan@gmail.com','Shraddha Khedekar','Adhyayan:: Workshop - '.$_POST ['workshop_name'].'',$body_mail,'poonam.choksi@adhyayan.asia');
      
                                        }
                                }
                                
                                $this->apiResult ["status"] = 1;
				$this->apiResult ["workshop_id"] = $wid;
				$this->apiResult ["message"] = "Workshop successfully created.";
                                $this->db->commit();
			} else {
				$this->apiResult ["message"] = "Unable to create workshop.";
                                $this->db->rollback();
			}
                    
                }
        
        }
        
        function editWorkshopAction() {
            //print_r($_FILES);
            $maxUploadFileSize = 104857600; // in bytes
            $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
            $workshop="";
            $attende_del=0;
            if(isset($_FILES['file'])){
            $pathInfo = pathinfo($_FILES['file']['name']);
            //echo $pathInfo['extension'];
            if(is_uploaded_file($_FILES['file']['tmp_name']) && in_array($_FILES['file']['type'],$csvMimes) && $pathInfo['extension']=="csv"){
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            $line1=fgetcsv($csvFile);
            //print_r($line1);
	    $workshop=isset($line1[1])?$line1[1]:"";
            $_FILES['file']['type'];
            } 
            
            }
            $workshopModel = new WorkshopModel ();
            if(in_array(8,$this->user['role_ids'])){
            $_POST ['workshop_programme']=1;
            }
            $uploadFiles=array();
            $uploadFilesCat=array();
            if(!empty($_POST['workshop_upload'])){
                $uploadFiles = $_POST['workshop_upload'];
                $uploadFilesCat = $_POST['workshop_upload_cat'];
            }
            
            $_POST ['workshop_date_to']=(!empty($_POST ['workshop_date_to']) && $_POST['workshop_date_to']!="0000-00-00")?date("Y-m-d", strtotime($_POST ['workshop_date_to'])):$_POST ['workshop_date_to'];
            $_POST ['workshop_date_from']=(!empty($_POST ['workshop_date_from']) && $_POST['workshop_date_from']!="0000-00-00")?date("Y-m-d", strtotime($_POST ['workshop_date_from'])):$_POST ['workshop_date_from'];
               
            //is_numeric($_POST['workshop_charges']);
            if (! in_array ( "manage_workshop", $this->user ['capabilities'] )) {
                                $this->apiResult ["message"] = "You are not authorized to perform this task.\n";
                }else if (empty ( $_POST ['workshop_id'] )) {
			
			$this->apiResult ["message"] = "Workshop id cannot be empty.\n";
		}else if (empty ( trim($_POST ['workshop_name']) )) {
                                $this->apiResult ["message"] = "Workshop Title cannot be empty.\n";
                }else if (empty ( trim($_POST ['workshop_location']))) {
                                $this->apiResult ["message"] = "Address cannot be empty.\n";
                }else if (empty ( $_POST ['workshop_date_from'] ) || $_POST['workshop_date_from']=="0000-00-00") {
                                $this->apiResult ["message"] = "Start Date cannot be empty.\n";
                }else if (empty ( $_POST ['workshop_date_to'] ) || $_POST['workshop_date_to']=="0000-00-00") {
                                $this->apiResult ["message"] = "End Date cannot be empty.\n";
                }else if ($_POST ['workshop_date_to']<$_POST['workshop_date_from']) {
                                $this->apiResult ["message"] = "End Date should not be less then Start Date.\n";
                }else if (empty ( $_POST ['workshop_programme'])) {
                                $this->apiResult ["message"] = "Programme cannot be empty.\n";
                }else if ($workshopModel->getUniqueWorkshop ( trim($_POST ['workshop_name']),$_POST ['workshop_date_from'],$_POST ['workshop_date_to'],trim($_POST ['workshop_location']),$_POST ['workshop_id'])) {
				
		 $this->apiResult ["message"] = "Workshop already exists.\n";
		}else if (empty ( $_POST ['facilitator_id'])) {
                                $this->apiResult ["message"] = "LED cannot be empty.\n";
                }else if (isset($_POST ['externalReviewTeam'] ['member']) && in_array($_POST['facilitator_id'],$_POST ['externalReviewTeam'] ['member'])) {
                                $this->apiResult ["message"] = "Same Member cannot be assigned twice.\n";
                }else if(isset($_POST ['externalReviewTeam'] ['member']) && count($_POST ['externalReviewTeam'] ['member'])!=count(array_unique($_POST ['externalReviewTeam'] ['member']))){
                                $this->apiResult ["message"] = "Same Member cannot be assigned twice.\n";
                }else if(isset($_FILES['file']) && !empty($_FILES['file']['name']) && $_FILES['file']['size']>$maxUploadFileSize){
                     $this->apiResult ["message"] = "File too big in size \n";
                }else if(isset($_FILES['file']) && !empty($_FILES['file']['name']) && (!in_array($_FILES['file']['type'],$csvMimes) || $pathInfo['extension']!="csv")){
                     $this->apiResult ["message"] = "Invalid file type \n";
                }else if(isset($_FILES['file']) && !empty($_FILES['file']['name']) && strtolower(trim($_POST['workshop_name']))!=strtolower(trim($workshop))){
                 $this->apiResult ["message"] = "Please Check the title of workshop in uploaded csv file \n";   
                }else if(isset($_FILES['file']) && count($line1)!=5){
                    $this->apiResult ["message"] = "Please Check the format of your uploaded csv file \n";   
                }else if(!empty($_POST['workshop_charges']) && !is_numeric($_POST['workshop_charges'])){
                    $this->apiResult ["message"] = "Charges for the Workshop should be numbers";
                }
                else{
                        //print_r($_POST);
                        //die();
                    //print_r($_POST ['externalReviewTeam'] ['clientId']);
                    //print_r($_POST ['externalReviewTeam'] ['member']);
                    //echo count($_POST ['externalReviewTeam'] ['clientId']);
                   //echo count($_POST ['externalReviewTeam'] ['member']);
                    //die();        
                        $externalRoleClient = array ();
			$facilitator_client_id=  empty($_POST['facilitator_client_id'])?'':$_POST['facilitator_client_id'];
                        $facilitator_id=$_POST['facilitator_id'];
                        $facilitator_payment=$_POST['facilitator_payment'];
                        $facilitator_role=1;
                        $led_array=array();
                        $led_array['facilitator_client_id']=$facilitator_client_id;
                        $led_array['facilitator_id']=$facilitator_id;
                        $led_array['facilitator_role']=$facilitator_role;
                        $led_array['facilitator_payment']=$facilitator_payment;
                        //print_r($led_array);
                        $i=0;
                        //$externalReviewTeam_client = $_POST ['externalReviewTeam'] ['clientId'];
                        //$externalReviewTeam_member = $_POST ['externalReviewTeam'] ['member'];
                        if (! empty ( $_POST ['externalReviewTeam'] ['clientId'] ))
				
				foreach ( $_POST ['externalReviewTeam'] ['clientId'] as $client ) 
				{					
					array_push ( $externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i]. '_' . $_POST ['externalReviewTeam'] ['facilitator_payment'] [$i] );					
					$i ++;
				}				
				// print_r($externalRoleClient);
			
			$externalReviewTeam = empty ( $_POST ['externalReviewTeam'] ['clientId'] ) ? '' : array_combine ( $_POST ['externalReviewTeam'] ['member'], $externalRoleClient );
                        //print_r($externalReviewTeam);
                        $notfound=array();
                        $notfound_name=array();
                        if(isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name']) && in_array($_FILES['file']['type'],$csvMimes) && $pathInfo['extension']=="csv"){
                        $line2=fgetcsv($csvFile);
                        $line3=fgetcsv($csvFile);
                        $csv_blank=1;
                        $csv_not_blank_array=array();
                        while(($line = fgetcsv($csvFile)) !== FALSE){
                        $userModel=new userModel();
                        $line['1']=isset($line['1'])?$line['1']:'';
                        $line['2']=isset($line['2'])?$line['2']:'';
                        $user=$userModel->getUserByEmail(trim($line['2']));
                        if($user['email']==trim($line['2']) && !empty($line['2'])){
                        //$status="A";
                        $line['4']=isset($line['4'])?$line['4']:'';
                        $line['3']=isset($line['3'])?$line['3']:''; 
                        if($line['4']=="P" || $line['4']=="Present"){
                            $status="P";
                        }else{
                        $status=$line['4'];    
                        }
                        $externalReviewTeam[$user['user_id']]="".$user['client_id']."_3_".$line['3']."_".$status."";
                        $attende_del=1;
                        $csv_blank=2;
                        $csv_not_blank_array[]=$line['2'];
                        
                        }else{
                        $notfound[]=$line['2'];
                        $notfound_name[]=$line['1'];
                        if(!empty($line['2'])){
                        $csv_blank=0;    
                        }
                        }
                        
                        }
                            
                        }
                        
                        if($_POST['workshop_school']=="None of the mentioned"){
                            $_POST['workshop_school']=0;
                        }else{
                            $_POST['workshop_school_none']="";
                        }
                        
                        $this->db->start_transaction();
                        if(isset($csv_blank) && $csv_blank==1){
                        $this->apiResult ["message"] = "Please Check the file , it should not be blank\n";    
                        }else if(isset($csv_not_blank_array) && count($csv_not_blank_array)==0){
                         $this->apiResult ["message"] = "Please Check the file , it should consists of atleast one valid E-mail Id\n";       
                        }
                        else if ($wid = $workshopModel->editWorkshop ( $_POST['workshop_id'], $_POST ['workshop_name'], $_POST ['workshop_location'], $_POST['workshop_school'], $_POST['workshop_school_none'] , $_POST ['workshop_programme'], $_POST['prog_type_id'] ,$_POST['workshop_charges'], $_POST ['workshop_payment_facilitator'], $_POST ['workshop_date_from'] , $_POST ['workshop_date_to'] ,  $_POST['workshop_description'] ,$led_array, $externalReviewTeam,$this->user['user_id'],$attende_del, $uploadFiles,$uploadFilesCat)) {
				//$this->db->addAlerts ( 'd_assessment', $aid, $_POST ['client_id'], $aid, 'CREATE_REVIEW' );
				$this->apiResult ["status"] = 1;
				$this->apiResult ["assessment_id"] = $wid;
				$this->apiResult ["message"] = "Workshop updated successfully.";
                                if(count($notfound)>0) {
                                
  $message_modal='<div id="myModal" class="modal fade">
    <div class="modal-dialog " style="width:400px;"><div class="modal-content">
                                <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" style="color:#ffffff;">List of Not Found Email-id(s)</h4></div>
                                <div class="modal-body">
				<div class="clr"></div>
                                <div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
                                    <div class="subTabWorkspace pad26">
                                        <div class="form-stmnt">';
                                 $message_modal.='<table class="cmnTable"><thead><tr><th>Name</th><th>E-mail</th></tr></thead><tbody>';
                                 $i=1;
                                 foreach($notfound as $kemail=>$email){
                                     $message_modal.="<tr><td>".$notfound_name[$kemail]."</td><td>".$email."</td></tr>";
                                     //$message_modal.="<br>";
                                     $i++;
                                 }
                                        
                                        $message_modal.='</tbody></table></div>
                                    </div>
                                </div>
                                </div></div></div>
</div>';    
                                $this->apiResult ["message"] = "Workshop updated successfully. Some of the users don't exist in Adhyayan System. Click <a href='#' data-toggle='modal' data-target='#myModal'>here</a> to view list.".$message_modal."";
                                
                                
                                }
                                $this->db->commit();
			} else {
				$this->apiResult ["message"] = "Unable to update workshop.";
                                $this->db->rollback();
			}
                    
                }
        
        }
        
          
               
	// new function for creating school review according to tap admin functionality on 06-06-2016 by Mohit Kumar
	function createSchoolAssessmentNewAction() {
               // print_r($_POST);
               // echo"hhhhhhhhh". count(array_unique($_POST ['facilitatorReviewTeam'] ['member']))<count($_POST ['facilitatorReviewTeam'] ['member']);
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		}else if (isset ( $_POST ['review_type'] ) && $_POST ['review_type']=='') {
			$this->apiResult ["message"] = "Review type cannot be empty.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			$this->apiResult ["message"] = "School id cannot be empty.\n";
		} else if (empty ( $_POST ['internal_assessor_id'] )) {
			$this->apiResult ["message"] = "Internal reviewer cannot be empty.\n";
		}else if (empty ( $_POST ['diagnostic_id'] )) {
			$this->apiResult ["message"] = "Diagnostic cannot be empty.\n";
		} else if (empty ( $_POST ['tier_id'] )) {
			$this->apiResult ["message"] = "Tier cannot be empty.\n";
		} else if (empty ( $_POST ['award_scheme_name'] )) {
			$this->apiResult ["message"] = "Award Scheme cannot be empty.\n";
                }else if (empty ( $_POST ['aqs_round'] )) {
			$this->apiResult ["message"] = "AQS Round cannot be empty.\n";
                }
                else if (empty ( $_POST['aqs']['school_aqs_pref_start_date'] )) {
			$this->apiResult ["message"] = "AQS start date cannot be empty.\n";
                }
                else if (empty ( $_POST['aqs']['school_aqs_pref_end_date'] )) {
			$this->apiResult ["message"] = "AQS end date cannot be empty.\n";
                }else if (!empty($_POST ['external_assessor_id']) && !empty($_POST ['internal_assessor_id']) && $_POST ['internal_assessor_id'] == $_POST ['external_assessor_id']) {			
			$this->apiResult ["message"] = "Internal and External reviewer cannot be same.\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['role'] ) && empty ( $_POST ['externalReviewTeam'] ['role'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {			
			$this->apiResult ["message"] = "External reviewer role cannot be empty.\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['member'] ) && empty ( $_POST ['externalReviewTeam'] ['member'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {			
			$this->apiResult ["message"] = "External reviewer member cannot be empty.\n";
		}
                else if( in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && empty ( $_POST ['external_assessor_id'])){
                    $this->apiResult ["message"] = "You are not authorized to update external review team.\n";                        
		} 
                else if( !in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && ! empty ( $_POST ['externalReviewTeam'] ['clientId'] )){
                    $this->apiResult ["message"] = "You are not authorized to update external review team.\n";                        
		}else if(isset($_POST ['facilitatorReviewTeam'] ['member']) && count(array_unique($_POST ['facilitatorReviewTeam'] ['member']))<count($_POST ['facilitatorReviewTeam'] ['member'])) {
                         $this->apiResult ["message"] = "Cannot assign multiple role to same facilitator.\n";               
                }
		else if(isset($_POST ['facilitatorReviewTeam'] ['member']) && isset($_POST ['facilitator_id']) && in_array($_POST ['facilitator_id'],$_POST ['facilitatorReviewTeam'] ['member'])) {
                         $this->apiResult ["message"] = "Cannot assign multiple role to same facilitator.\n";               
                }else if (empty ( $_POST ['diagnostic_lang'] )) {
			$this->apiResult ["message"] = "Diagnostic language cannot be empty.\n";
                }
                
                else {
                    //echo "<pre>";print_r($_POST);die;
                    if (!empty($_POST['aqs']['school_aqs_pref_end_date']) && !empty($_POST['aqs']['school_aqs_pref_start_date'])) {
                        $eDate = explode("-", $_POST['aqs']['school_aqs_pref_end_date']);
                        $sDate = explode("-", $_POST['aqs']['school_aqs_pref_start_date']);
                        if ($eDate[2] < $sDate[2] || ($eDate[2] == $sDate[2] && $eDate[1] < $sDate[1]) || ($eDate[2] == $sDate[2] && $eDate[1] == $sDate[1] && $eDate[0] < $sDate[0]))
                            $this->apiResult ["message"] = "End date can't be less than Start date";

                        else {

                                //echo "<pre>";print_r( $_POST['aqs']['school_aqs_pref_start_date']);die;
                                $externalRoleClient = array();
                                $facilitatorDataArray = array();
                                $assessmentModel = new assessmentModel ();
                                $i = 0;
                                if (!empty($_POST ['externalReviewTeam'] ['clientId']) && in_array("assign_external_review_team", $this->user ['capabilities']))
                                    foreach ($_POST ['externalReviewTeam'] ['clientId'] as $client) {
                                        array_push($externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i]);
                                        $i ++;
                                    }
                                     $facilitatorCount=0;
                                   // print_r($_POST ['facilitatorReviewTeam'] ['member']);
                                    //$facilitatorDataArray=array();
                                    if(isset($_POST ['facilitatorReviewTeam'] ['clientId'])) {
                                       
                                        foreach ($_POST ['facilitatorReviewTeam'] ['clientId'] as $client=>$val) {
                                            //array_push($externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i]);
                                            $facilitatorDataArray[$facilitatorCount]['client_id'] = $val;
                                            $facilitatorDataArray[$facilitatorCount]['role_id'] = $_POST ['facilitatorReviewTeam'] ['role'][$facilitatorCount];
                                            $facilitatorDataArray[$facilitatorCount]['user_id'] = $_POST ['facilitatorReviewTeam'] ['member'][$facilitatorCount];
                                            $facilitatorCount++;
                                        }
                                    }
                                    if(!empty($_POST['facilitator_client_id']) && !empty($_POST['facilitator_id'])) {
                                        
                                        $facilitatorDataArray[$facilitatorCount]['client_id'] = $_POST['facilitator_client_id'];
                                        $facilitatorDataArray[$facilitatorCount]['role_id'] = 1;
                                        $facilitatorDataArray[$facilitatorCount]['user_id'] = $_POST['facilitator_id'];
                                       // $facilitatorCount++;
                                    }
                                

                                $externalReviewTeam = empty($_POST ['externalReviewTeam'] ['clientId']) ? '' : array_combine($_POST ['externalReviewTeam'] ['member'], $externalRoleClient);
                               // echo "<pre>"; print_r($_POST);
                               // echo "<pre>"; print_r($facilitatorDataArray);
                                $notificationSettingData = isset($_POST ['notifySett'])?$_POST ['notifySett']:array();
                                $notificationTeam = isset($_POST ['externalReviewTeam'] ['member'])?$_POST ['externalReviewTeam'] ['member']:array();
                                
                                $sheetStatusData = array();
                                foreach($notificationTeam as $key=>$val){
                                    
                                    if(isset($_POST ['externalReviewTeam'] ['role'][$key]) && $_POST ['externalReviewTeam'] ['role'][$key] != 8 )
                                        $sheetStatusData[$val]  = 0;                                   
                                }
                                 if(isset($_POST ['external_assessor_id']))
                                      $sheetStatusData[$_POST ['external_assessor_id']]= 0 ;
                              // echo"<pre>"; print_r($sheetStatusData);die;
                               $notificationTeam[] = isset($_POST ['external_assessor_id'])?$_POST ['external_assessor_id']:'';
                                $notificationTeam[] = isset($_POST ['internal_assessor_id'])?$_POST ['internal_assessor_id']:'';
                                
                                $this->db->start_transaction();
                                $aid = $assessmentModel->createSchoolAssessmentNew($_POST ['client_id'], $_POST ['internal_assessor_id'], $_POST ['facilitator_id'], $_POST ['diagnostic_id'], $_POST ['tier_id'], $_POST ['award_scheme_name'], $_POST ['aqs_round'], $_POST ['external_assessor_id'], $externalReviewTeam, $_POST['aqs']['school_aqs_pref_start_date'], $_POST['aqs']['school_aqs_pref_end_date'],$facilitatorDataArray,$notificationSettingData,$notificationTeam,$_POST ['diagnostic_lang'],$_POST ['review_criteria'],$_POST ['review_type']);
                                $reviwSettingStatus = $this->addReviewNotificationSettings($aid,$externalReviewTeam,$_POST ['external_assessor_id'],1) ;
                                $reviwSheetStatus = $assessmentModel->updateReimSheetSettings($aid,$sheetStatusData,1) ;
                                if ($aid && $reviwSettingStatus && $reviwSheetStatus) {
                                      
                                    $this->db->addAlerts('d_assessment', $aid, $_POST ['client_id'], $aid, 'CREATE_REVIEW');
                                    $this->apiResult ["status"] = 1;
                                    $this->apiResult ["assessment_id"] = $aid;
                                    $this->apiResult ["message"] = "Review successfully created.";
                                    $this->db->commit();
                                } else {
                                    $this->apiResult ["message"] = "Unable to create review.";
                                    $this->db->rollback();
                                }
                            }
                    }
                }
        }
        
        
        function createSchoolAssessmentKpaAction() {
               // print_r($_POST);
               // echo"hhhhhhhhh". count(array_unique($_POST ['facilitatorReviewTeam'] ['member']))<count($_POST ['facilitatorReviewTeam'] ['member']);
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		}                
                else {
                    //echo "<pre>";print_r($_POST);die;
                    if (empty($_POST['team_kpa_id']) ) 
                            $this->apiResult ["message"] = "KPAs can't empty";
                    
                        else {
                                $allAssessmentKpas = array();
                                $num_kpa = isset($_POST['num_kpa'])?$_POST['num_kpa']:0;
                                foreach($_POST['team_kpa_id'] as $teamKpa) {
                                    foreach($teamKpa as $key=>$val) {
                                        
                                        $allAssessmentKpas[] = $val;
                                    }
                                    
                                }
//                                / print_r($allAssessmentKpas);
                                if(!empty($allAssessmentKpas) && count($allAssessmentKpas) != $num_kpa){
                                    
                                    $this->apiResult ["message"] = "All KPAs must be assigned to assessors";
                                }else if(!empty($allAssessmentKpas) && count($allAssessmentKpas) != count(array_unique($allAssessmentKpas))){
                                    
                                    $this->apiResult ["message"] = "Same KPA cannot  assigned to multiple assessors";
                                }else {
                                    //print_r($allAssessmentKpas);
                                    //echo "<pre>";print_r( $_POST['aqs']['school_aqs_pref_start_date']);die;
                                    $externalRoleClient = array();
                                    $facilitatorDataArray = array();
                                    $assessmentModel = new assessmentModel ();
                                    $assessment_id = isset($_POST['assessment_id'])?$_POST['assessment_id']:'';
                                    $i = 0;
                                    $aid = $assessmentModel->addAssessmentKpa($_POST['team_kpa_id'],$assessment_id);


                                    if ($aid ) {                                    

                                        $this->apiResult ["status"] = 1;
                                        $this->apiResult ["message"] = "KPAs assigned successfully";
                                       // $this->db->commit();
                                    } else {
                                        $this->apiResult ["message"] = "Unable to create review.";
                                        //$this->db->rollback();
                                    }
                                }
                            }
                    
                }
        }
        function editSchoolAssessmentKpaAction() {
                //print_r($_POST);
               // echo"hhhhhhhhh". count(array_unique($_POST ['facilitatorReviewTeam'] ['member']))<count($_POST ['facilitatorReviewTeam'] ['member']);
                $editStatus = isset($_POST['editStatus'])?$_POST['editStatus']:0;
		if (! (in_array ( "create_assessment", $this->user ['capabilities'] ) || $editStatus)) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		}                
                else {
                    //echo "<pre>";print_r($_POST);die;
                    if (empty($_POST['team_kpa_id']) ) 
                            $this->apiResult ["message"] = "KPAs can't empty";

                        else {
                                $assessmentModel = new assessmentModel ();
                                $diagnosticModel = new diagnosticModel;
                                $assessment_id = isset($_POST['assessment_id'])?$_POST['assessment_id']:'';
                                $allAssessmentKpas = array();
                                $num_kpa = isset($_POST['num_kpa'])?$_POST['num_kpa']:0;
                                $assessmentStatus = $assessmentModel->getAssessmentRatingStatus($assessment_id);
                                $percentageData = $diagnosticModel->getExternalTeamRatingPerc($assessment_id);                        
                                $leadAssessor = $diagnosticModel->getAssessmentLead($assessment_id); 
                                $allAccessorsId = array();
                                if(!empty($percentageData)){
                                    $allAccessorsId = explode(",",$percentageData['user_ids']);
                                }
                                //print_r($_POST['team_kpa_id']);die;
                                if($assessmentStatus == 1)
                                    $this->apiResult ["message"] = "You are not authorized to perform this task.\n";
                                else if(!array_key_exists($leadAssessor['user_id'],$_POST['team_kpa_id'])){
                                         $this->apiResult ["message"] = "KPAs must be assigned to lead assessor";
                                }else {
                                    foreach($_POST['team_kpa_id'] as $teamKpa) {
                                        foreach($teamKpa as $key=>$val) {

                                            $allAssessmentKpas[] = $val;
                                        }

                                    }
                                   // print_r($_POST['team_kpa_id']);
                                    if(!empty($allAssessmentKpas) && count($allAssessmentKpas) != $num_kpa){

                                        $this->apiResult ["message"] = "All KPAs must be assigned to assessors";
                                    }else if(!empty($allAssessmentKpas) && count($allAssessmentKpas) != count(array_unique($allAssessmentKpas))){

                                        $this->apiResult ["message"] = "Same KPA can't  assigned to multiple assessors";
                                    } else {
                                        //echo "<pre>";print_r( $_POST['aqs']['school_aqs_pref_start_date']);die;
                                        $externalRoleClient = array();
                                        $facilitatorDataArray = array();
                                        
                                        $this->db->start_transaction();
                                        if(isset($_POST['isNewReview']) && $_POST['isNewReview'] == 0 )
                                                $assessmentModel->deleteOldKpaRating($_POST['team_kpa_id'],$assessment_id);
                                      
                                        /*foreach($_POST['team_kpa_id'] as $key=>$data){
                                            
                                            $assessmentModel->deleteOldKpaRating($key,$assessment_id,$data);
                                        }*/
                                        $i = 0;
                                        $aid = $assessmentModel->editAssessmentKpa($_POST['team_kpa_id'],$assessment_id);
                                         $assessmentModel->editAssessmentStatus($assessment_id);


                                        if ($aid ) {

                                            $this->apiResult ["status"] = 1;
                                            $this->apiResult ["message"] = "KPAs assigned successfully";
                                            $this->db->commit();
                                            if(isset($_POST['isNewReview']) && $_POST['isNewReview'] == 0 ){
                                                //print_r($leadAssessor);
                                                $this->updateCollaborativeAssessmentPercentage($assessment_id,$allAccessorsId,1,0);
                                                if(!empty($leadAssessor)){
                                                    //echo "z";
                                                     $this->updateCollaborativeAssessmentPercentage($assessment_id,$leadAssessor,0,1);
                                                }
                                            }
                                        } else {
                                            $this->apiResult ["message"] = "Unable to create review.";
                                            $this->db->rollback();
                                        }
                                    }
                                }
                            }
                    
                }
        }
	
        function createCollegeAssessmentNewAction() {
           // print_r($_POST ['facilitatorReviewTeam'] ['member']);
               // echo"hhhhhhhhh". count(array_unique($_POST ['facilitatorReviewTeam'] ['member']))<count($_POST ['facilitatorReviewTeam'] ['member']);
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['client_id'] )) {
			$this->apiResult ["message"] = "College id cannot be empty.\n";
		} else if (empty ( $_POST ['internal_assessor_id'] )) {
			$this->apiResult ["message"] = "Internal reviewer cannot be empty.\n";
		}else if (empty ( $_POST ['diagnostic_id'] )) {
			$this->apiResult ["message"] = "Diagnostic cannot be empty.\n";
		} /*else if (empty ( $_POST ['tier_id'] )) {
			$this->apiResult ["message"] = "Tier cannot be empty.\n";
		} else if (empty ( $_POST ['award_scheme_name'] )) {
			$this->apiResult ["message"] = "Award Scheme cannot be empty.\n";
                }*/else if (empty ( $_POST ['aqs_round'] )) {
			$this->apiResult ["message"] = "AQS Round cannot be empty.\n";
                }
                else if (empty ( $_POST['aqs']['school_aqs_pref_start_date'] )) {
			$this->apiResult ["message"] = "AQS start date cannot be empty.\n";
                }
                else if (empty ( $_POST['aqs']['school_aqs_pref_end_date'] )) {
			$this->apiResult ["message"] = "AQS end date cannot be empty.\n";
                }else if (!empty($_POST ['external_assessor_id']) && !empty($_POST ['internal_assessor_id']) && $_POST ['internal_assessor_id'] == $_POST ['external_assessor_id']) {			
			$this->apiResult ["message"] = "Internal and External reviewer cannot be same.\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['role'] ) && empty ( $_POST ['externalReviewTeam'] ['role'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {			
			$this->apiResult ["message"] = "External reviewer role cannot be empty.\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['member'] ) && empty ( $_POST ['externalReviewTeam'] ['member'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {			
			$this->apiResult ["message"] = "External reviewer member cannot be empty.\n";
		}
                else if( in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && empty ( $_POST ['external_assessor_id'])){
                    $this->apiResult ["message"] = "You are not authorized to update external review team.\n";                        
		} 
                else if( !in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && ! empty ( $_POST ['externalReviewTeam'] ['clientId'] )){
                    $this->apiResult ["message"] = "You are not authorized to update external review team.\n";                        
		}else if(isset($_POST ['facilitatorReviewTeam'] ['member']) && count(array_unique($_POST ['facilitatorReviewTeam'] ['member']))<count($_POST ['facilitatorReviewTeam'] ['member'])) {
                         $this->apiResult ["message"] = "Cannot assign multiple role to same facilitator.\n";               
                }
		else if(isset($_POST ['facilitatorReviewTeam'] ['member']) && isset($_POST ['facilitator_id']) && in_array($_POST ['facilitator_id'],$_POST ['facilitatorReviewTeam'] ['member'])) {
                         $this->apiResult ["message"] = "Cannot assign multiple role to same facilitator.\n";               
                }else if (empty ( $_POST ['diagnostic_lang'] )) {
			$this->apiResult ["message"] = "Diagnostic language cannot be empty.\n";
                }
                
                else {

                    if (!empty($_POST['aqs']['school_aqs_pref_end_date']) && !empty($_POST['aqs']['school_aqs_pref_start_date'])) {
                        $eDate = explode("-", $_POST['aqs']['school_aqs_pref_end_date']);
                        $sDate = explode("-", $_POST['aqs']['school_aqs_pref_start_date']);
                        if ($eDate[2] < $sDate[2] || ($eDate[2] == $sDate[2] && $eDate[1] < $sDate[1]) || ($eDate[2] == $sDate[2] && $eDate[1] == $sDate[1] && $eDate[0] < $sDate[0]))
                            $this->apiResult ["message"] = "End date can't be less than Start date";

                        else {

                                //echo "<pre>";print_r( $_POST['aqs']['school_aqs_pref_start_date']);die;
                                $externalRoleClient = array();
                                $facilitatorDataArray = array();
                                $assessmentModel = new assessmentModel ();
                                $i = 0;
                                if (!empty($_POST ['externalReviewTeam'] ['clientId']) && in_array("assign_external_review_team", $this->user ['capabilities']))
                                    foreach ($_POST ['externalReviewTeam'] ['clientId'] as $client) {
                                        array_push($externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i]);
                                        $i ++;
                                    }
                                     $facilitatorCount=0;
                                   // print_r($_POST ['facilitatorReviewTeam'] ['member']);
                                     //$facilitatorDataArray=array();
                                    if(isset($_POST ['facilitatorReviewTeam'] ['clientId'])) {
                                       
                                        foreach ($_POST ['facilitatorReviewTeam'] ['clientId'] as $client=>$val) {
                                            //array_push($externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i]);
                                            $facilitatorDataArray[$facilitatorCount]['client_id'] = $val;
                                            $facilitatorDataArray[$facilitatorCount]['role_id'] = $_POST ['facilitatorReviewTeam'] ['role'][$facilitatorCount];
                                            $facilitatorDataArray[$facilitatorCount]['user_id'] = $_POST ['facilitatorReviewTeam'] ['member'][$facilitatorCount];
                                            $facilitatorCount++;
                                        }
                                    }
                                    if(!empty($_POST['facilitator_client_id']) && !empty($_POST['facilitator_id'])) {
                                        
                                        $facilitatorDataArray[$facilitatorCount]['client_id'] = $_POST['facilitator_client_id'];
                                        $facilitatorDataArray[$facilitatorCount]['role_id'] = 1;
                                        $facilitatorDataArray[$facilitatorCount]['user_id'] = $_POST['facilitator_id'];
                                       // $facilitatorCount++;
                                    }
                                

                                $externalReviewTeam = empty($_POST ['externalReviewTeam'] ['clientId']) ? '' : array_combine($_POST ['externalReviewTeam'] ['member'], $externalRoleClient);
                               // echo "<pre>"; print_r($_POST);
                               // echo "<pre>"; print_r($facilitatorDataArray);
                                $this->db->start_transaction();
                                if ($aid = $assessmentModel->createSchoolAssessmentNew($_POST ['client_id'], $_POST ['internal_assessor_id'], $_POST ['facilitator_id'], $_POST ['diagnostic_id'], NULL, NULL, $_POST ['aqs_round'], $_POST ['external_assessor_id'], $externalReviewTeam, $_POST['aqs']['school_aqs_pref_start_date'], $_POST['aqs']['school_aqs_pref_end_date'],$facilitatorDataArray,array(),array(),$_POST ['diagnostic_lang'])) {
                                    $this->db->addAlerts('d_assessment', $aid, $_POST ['client_id'], $aid, 'CREATE_REVIEW');
                                    $this->apiResult ["status"] = 1;
                                    $this->apiResult ["assessment_id"] = $aid;
                                    $this->apiResult ["message"] = "Review successfully created.";
                                    $this->db->commit();
                                } else {
                                    $this->apiResult ["message"] = "Unable to create review.";
                                    $this->db->rollback();
                                }
                            }
                    }
                }
        }
	// function for send sign up mail for external assessor from tap admin on 14-06-2016 by Mohit Kumar
	public function sendSignUpEmailAction() {
		if ($_POST ['email'] == '') {
			$this->apiResult ["message"] = "Email can't empty!\n";
		} else if (preg_match ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", strtolower ( trim ( $_POST ['email'] ) ) ) != 1) {
			$this->apiResult ["message"] = "Invalid email!\n";
		} else {
			if ($this->db->query ( 'Select 1 from d_AQS_team limit 1' )) {
				$table = 'd_AQS_team';
			} else {
				$table = 'd_aqs_team';
			}
			
			$checkUser = $this->db->get_row ( "Select
                (Select id from h_aqs_team_invite_user where aqs_team_user_id='" . $_POST ['id'] . "' and email='" . strtolower ( trim ( $_POST ['email'] ) ) . "')
                as id,(Select name from " . $table . " WHERE id='" . $_POST ['id'] . "') as name,(Select email from d_user where 
                email='" . strtolower ( trim ( $_POST ['email'] ) ) . "') as system_email  " );
			
			if (! empty ( $checkUser ) && $checkUser ['system_email'] != '') {
				$this->apiResult ["message"] = "This user is already in our system!\n";
			} else {
				if (! empty ( $checkUser ) && $checkUser ['id'] != '') {
					if ($this->db->update ( 'h_aqs_team_invite_user', array (
							'send_email_status' => 1,
							'modification_date' => date ( 'Y-m-d H:i:s' ) 
					), array (
							'email' => $_POST ['email'] 
					) )) {
						$uid = $checkUser ['id'];
					}
				} else if ($this->db->insert ( 'h_aqs_team_invite_user', array (
						'aqs_team_user_id' => $_POST ['id'],
						'email' => strtolower ( trim ( $_POST ['email'] ) ),
						'send_email_status' => 1,
						'creation_date' => date ( 'Y-m-d H:i:s' ),
						'user_type_table' => 'd_aqs_team' 
				) ) && $checkUser ['id'] == '') {
					$uid = $this->db->get_last_insert_id ();
				} else if (! empty ( $checkUser ) && $checkUser ['id'] != '' && $checkUser ['system_email'] == '') {
					$this->apiResult ["message"] = "This user is already invited for signup process!\n";
				}
				
				if ($uid != '') {
					require ROOT . 'library' . DS . 'phpmailer' . DS . "PHPMailerAutoload" . '.php';
					// Create a new PHPMailer instance
					$mail = new PHPMailer ();
					// Tell PHPMailer to use SMTP
					$mail->isSMTP ();
					// Enable SMTP debugging
					// 0 = off (for production use)
					// 1 = client messages
					// 2 = client and server messages
					$mail->SMTPDebug = 0;
					// Ask for HTML-friendly debug output
					$mail->Debugoutput = 'html';
					// Set the hostname of the mail server
					$mail->Host = "omkara.freewaydns.net";
					// Set the SMTP port number - likely to be 25, 465 or 587
					$mail->Port = 465;
					// Whether to use SMTP authentication
					$mail->SMTPAuth = true;
					// Username to use for SMTP authentication
					$mail->SMTPSecure = 'ssl';
					$mail->Username = "mail@algoinsighttest.com";
					// Password to use for SMTP authentication
					$mail->Password = "dNDT$*NQ4MXn";
					// $fromEmail = 'mohit.k@tatrasdata.com';
					$fromName = 'Adhyayan Tap Admin';
					$toEmail = $_POST ['email'];
					$toName = $checkUser ['name'];
					// Set who the message is to be sent from
					$mail->setFrom ( 'mohit.k@tatrasdata.com', $fromName );
					// Set an alternative reply-to address
					// $mail->addReplyTo($fromEmail, $fromName);
					// Set who the message is to be sent to
					$mail->addAddress ( $toEmail, $toName );
					// $mail->AddBCC("mohit.k@tatrasdata.com", $toName);
					// Set the subject line
					$mail->Subject = 'Join the movement, become an Adhyayan Assessor!';
					// Read an HTML message body from an external file, convert referenced images to embedded,
					// convert HTML into a basic plain-text alternative body
					// $message=' asdasdsa ';
					// $mail->AltBody = $message;
					$signup_link = SITEURL . "index.php?controller=user&action=userProfile&process=invite&id=" . $uid;
					$video_link = "https://www.youtube.com/watch?v=hIOgQy1DT_E";
					$company_site = 'http://adhyayan.asia/site/the-assessor-programme/';
					$mail->msgHTML ( "Dear $toName,
                                <br><br><br>Congratulations on completing the Adhyayan Quality Standard Review Programme.<br/><br>
                                We would like to extend an invitation to you and your leadership team (inc. supervisors, co-ordinators,
                                heads of departments), to engage further in Adhyayan's education movement by joining�<b>The
                                Assessor Programme (TAP)</b>.<br/><br>
                                TAP is a network of school leaders that interact to engage in their continuous professional
                                development by showcasing talents and learning across the schooling spectrum.<br/><br>
                                To hear from Assessors about how TAP can support you, your peers, and your school, please 
                                <a href='" . $video_link . "' target='_blank' title='Click here for watch video'>click here</a>. 
                                You may also visit our website by <a href='" . $company_site . "' target='_blank'>clicking here</a>.<br/><br>
                                <a href='" . $signup_link . "' target='_blank' title='Click here for sign up'>Register for The Assessor Programme here</a>,
                                and continue your learning journey. Registering with us
                                will give you immediate access to the TAP WhatsApp Group, connecting you with school leaders
                                across the world.<br/><br>
                                We look forward to your continued engagement in enriching the quality of education across India
                                through The Assessor Programme.<br/><br/><br>
                                Warm wishes,<br/><br>
                                Amisha Modi<br/>
                                TAP Programme Lead
                                " );
					if (! $mail->send ()) {
						$this->apiResult ["message"] = $mail->ErrorInfo;
					} else {
						$this->apiResult ["message"] = "Mail has been sent successfully.";
						$this->apiResult ["status"] = 1;
					}
				} else {
					$this->apiResult ["message"] = 'There is some unknown error!';
				}
			}
		}
	}
	
	/*
	 * public function getAllAssessorsReviewCountAction(){
	 * // $this->_render=FALSE;
	 * $objAssessment = new assessmentModel();
	 * $assessorsCount = $objAssessment->getReviewCountByRole();
	 * $this->apiResult["count"]=$assessorsCount;
	 * $this->apiResult["status"]=1;
	 * }
	 */
	
	// function for check language is already exist or not
	function checkLanguageExistAction() {
		if (isset ( $_POST ['language'] ) && trim ( $_POST ['language'] ) != '') {
			$objDianosticModel = new diagnosticModel ();
			$checkData = $objDianosticModel->getLanguageData ( trim ( $_POST ['language'] ), 'id' );
			if (! empty ( $checkData )) {
				$this->apiResult ["message"] = 'This language is already exist. Please enter new language!';
			} else {
				$this->apiResult ["status"] = 1;
			}
		} else {
			$this->apiResult ["message"] = 'Please enter new language!';
		}
	}
	
	// function for updating user password on 25-07-2016 by Mohit Kumar
	public function updateUserPasswordAction() {
		if (isset ( $_POST ['id'] ) && isset ( $_POST ['password'] )) {
			$user = $this->userModel->getUserById ( $_POST ['id'] );
			if (empty ( $user )) {
				$this->apiResult ["message"] = 'This user is invalid user!';
			} else {
				if ($this->db->update ( 'd_user', array (
						'password' => md5 ( trim ( $_POST ['password'] ) ) 
				), array (
						'user_id' => $_POST ['id'] 
				) )) {
					if (OFFLINE_STATUS == TRUE) {
						// start---> call function for add other langauge on 10-08-2016 by Mohit Kumar
						$action_json = json_encode ( array (
								'password' => $_POST ['password'],
								'user_id' => $_POST ['id'] 
						) );
						$this->db->saveHistoryData ( $_POST ['id'], 'd_user', $uniqueID, 'updateAssessorIntroductoryAssessment', $_POST ['id'], $_POST ['password'], $action_json, 0, date ( 'Y-m-d H:i:s' ) );
						// end---> call function for add other langauge on 10-08-2016 by Mohit Kumar
					}
					$this->apiResult ["message"] = 'Password Updated Successfully!';
					$this->apiResult ["status"] = 1;
				}
			}
		}
	}
	public function getStateByCountryAction() {
		if (empty ( $_POST ['country'] )) {
			$this->apiResult ["message"] = "Country can not be empty\n";
		} else {
			$clientModel = new clientModel ();
			$countryId = $_POST ['country'];
			$stateId = empty ( $_POST ['state'] ) ? 0 : $_POST ['state'];
			$states = $clientModel->getStateList ( $countryId, $stateId );
			$this->apiResult ["states"] = $states;
			$this->apiResult ["status"] = 1;
		}
	}
	public function getCityByStateAction() {
		if (empty ( $_POST ['state'] )) {
			$this->apiResult ["message"] = "State can not be empty\n";
		} else {
			$clientModel = new clientModel ();
			$stateId = $_POST ['state'];
			$cities = $clientModel->getCityList ( $stateId );
			$this->apiResult ["cities"] = $cities;
			$this->apiResult ["status"] = 1;
		}
	}
	/*
	 * function getgraphDataAction(){
	 * if(!in_array(6,$this->user['role_ids'])){
	 * $this->apiResult["message"] = "You are not authorized to perform this task.\n";
	 * $this->apiResult["status"]=0;
	 * }
	 * elseif(empty($_POST['filter_name']))
	 * {
	 * $this->apiResult["message"] = "Filter name can not be empty.\n";
	 * }
	 * else{
	 * $customreportModel = new customreportModel ();
	 * $awardScheme = 0;
	 * $filter_id = $_POST['filter_name'];
	 * $queryParams =' 1=1 ';
	 * //$this->set("data",($customreportModel->getClientComparisonAwards($lastReviewData)));
	 * $resParams = $customreportModel->applyFilterQuery($filter_id);
	 * foreach($resParams as $param){
	 * if($param['filter_table']=='d_fees'||$param['filter_table']=='d_school_strength'){
	 * $temp=$customreportModel->getStaticTableVals($param['filter_table'], $param['filter_table_col_id'], $param['filter_table_col_name'], $param['value']);
	 * $param['value'] = $temp['staticCol'];
	 * $param['filter_table']=='d_fees'?($param['filter_table_col_id']='annual_fee'):($param['filter_table']=='d_school_strength'?($param['filter_table_col_id']='no_of_students'):'');
	 * }
	 * $param['filter_table']=='d_award_scheme'?$awardScheme=$param['value']:'';
	 * $queryParams .= ' AND '.$param['filter_table_col_id'].' '.$param['operator_text'].' '.($param['operator_text']=='IN'?'('.$param['value'].')':$param['value']);
	 * }
	 * $awardScheme>0?($awardScheme=" AND award_scheme_id=".$awardScheme):($awardScheme=" AND award_scheme_id=1");
	 * $customreportModel->generateComparisonData();
	 * $data= $customreportModel->getClientComparisonAwards($queryParams,$awardScheme);
	 *
	 * $awardsMatrix = array();
	 * $awardNames = array_keys(array_flip(array_column($data,'award_name')));
	 * //print_r($awardNames);
	 * $i=0;
	 * $skipAward = array('Bronze','Silver','Gold','Platinum');
	 * $tiers= array("State","National","International");
	 * foreach($tiers as $tier){
	 * $awardsMatrix[$i]['tier']= $tier;
	 * $tier=='National'||$tier=='International'?$awardsMatrix[$i]['Bronze']=0:'';
	 * array_walk($data,function($val,$key) use($tier,&$awardsMatrix,$i){
	 * if($tier==$val['standard_name'])
	 * $awardsMatrix[$i][$val['award_name']]=intval($val['num']);
	 * });
	 * $i++;
	 * }
	 * }
	 * $this->apiResult["data"] = $awardsMatrix;
	 * $this->apiResult["status"]=1;
	 * }
	 */
	function getgraphDataAction() {
		if (! in_array ( 6, $this->user ['role_ids'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			$this->apiResult ["status"] = 0;
		} else {
			// get user json
			$user_json = file_get_contents ( ROOT . DS . 'config' . DS . 'user-config.json' );
			// print_r($user_json);
			$user_json = json_decode ( $user_json, true );
			$customreportModel = new customreportModel ();
			$awardScheme = 0;
			$totalAwards = 0;
			$queryParams = array ();
			$results = array ();
			if (empty ( $_POST ['selectedfilters'] )) // show all data
{
				$queryParams [0] = " 1=1 ";
				$awardScheme > 0 ? ($awardScheme = " AND award_scheme_id=" . $awardScheme) : ($awardScheme = " AND award_scheme_id=1");
			} else {
				$i = 0;
				$filter_ids = explode ( ',', $_POST ['selectedfilters'] );
				$result = array ();
				foreach ( $filter_ids as $filter_id ) :
					$queryParams [$i] ['filter'] = $filter_id;
					$queryParams [$i] ['clause'] = ' 1=1 ';
					$resParams = $customreportModel->applyFilterQuery ( $filter_id );
					foreach ( $resParams as $param ) {
						if ($param ['filter_table'] == 'd_fees' || $param ['filter_table'] == 'd_school_strength') {
							$temp = $customreportModel->getStaticTableVals ( $param ['filter_table'], $param ['filter_table_col_id'], $param ['filter_table_col_name'], $param ['value'] );
							$param ['value'] = $temp ['staticCol'];
							$param ['filter_table'] == 'd_fees' ? ($param ['filter_table_col_id'] = 'annual_fee') : ($param ['filter_table'] == 'd_school_strength' ? ($param ['filter_table_col_id'] = 'no_of_students') : '');
						} elseif ($param ['filter_table_col_id'] == 'province')
							$param ['value'] = "'" . addslashes ( $param ['value'] ) . "'";
						$param ['filter_table'] == 'd_award_scheme' ? $awardScheme = $param ['value'] : '';
						$queryParams [$i] ['clause'] .= ' AND ' . $param ['filter_table_col_id'] . ' ' . $param ['operator_text'] . ' ' . ($param ['operator_text'] == 'IN' ? '(' . $param ['value'] . ')' : $param ['value']);
					}
					$i ++;
				endforeach
				;
				
				$awardScheme > 0 ? ($awardScheme = " AND award_scheme_id=" . $awardScheme) : ($awardScheme = " AND award_scheme_id=1");
				// $customreportModel->generateComparisonData ();
				// get all the possible combinations for the given set of filters
				$num = count ( $filter_ids );
				if ($num) {
					$sqlSubQuery = "select * from temp_reviewData where ";
					// The total number of possible combinations where one is no element being present
					$total = pow ( 2, $num );
					// Loop through each possible combination
					for($i = 0; $i < $total; $i ++) {
						// For each combination check if each bit is set
						$key = '';
						$val = '';
						for($j = 0; $j < $num; $j ++) {
							// Is bit $j set in $i?
							if (pow ( 2, $j ) & $i) {
								$key .= ($key != '' ? "+" : '') . $_POST ['sel-filter'] [$queryParams [$j] ['filter']];
								$val .= ($val != '' ? ' UNION ' : '') . $sqlSubQuery . $queryParams [$j] ['clause'];
							}
						}
						$key != '' ? ($results [$key] = $val) : '';
					}
					// print_r($results);die;
				}
			}
			// for default case
			$data = $customreportModel->getClientComparisonAwards ( array (
					' 1=1 ' 
			), $awardScheme );
			$combination_filters = array ();
			if (! empty ( $results )) { // if multiple filters are applied get result of each combination
				foreach ( $results as $key => $res )
					$combination_filters [$key] = $customreportModel->getClientComparisonAwardsCombination ( $res, $awardScheme );
			}
			// print_r($combination_filters);die;
			$awardsMatrix = array ();
			$i = 0;
			foreach ( $data as $row ) {
				$awardsMatrix [$i] ['award'] = ($row ['standard_name'] ? $row ['standard_name'] . " " : '') . $row ['award_name'];
				if (empty ( $combination_filters )) {
					$awardsMatrix [$i] ['default'] = $row ['num'];
					$totalAwards += $row ['num'];
					$maxScaleY = $row ['num'] > $maxScaleY ? $row ['num'] : $maxScaleY;
				}
				if (! empty ( $combination_filters ))
					foreach ( $combination_filters as $key => $res ) {
						$awardsMatrix [$i] [$key] = $res [$i] ['num'];
						preg_match ( '/\\+/', $key ) ? "" : ($totalAwards += $res [$i] ['num']);
					}
				$i ++;
			}
			// print_r($awardsMatrix);die;
			
			$this->apiResult ["data"] = $totalAwards >= $user_json ["Dashboard"] ["numSchools"] ? $awardsMatrix : "";
			$this->apiResult ["status"] = 1;
			return;
		}
	}
	// subgraphs for kpa
	function getSubgraphDataAction() {
		if (! in_array ( 6, $this->user ['role_ids'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			$this->apiResult ["status"] = 0;
		} else {
			// get user json
			$user_json = file_get_contents ( ROOT . DS . 'config' . DS . 'user-config.json' );
			// print_r($user_json);
			$user_json = json_decode ( $user_json, true );
			$customreportModel = new customreportModel ();
			$awardScheme = 0;
			$totalAwards = 0;
			$maxScaleY = 0;
			$queryParams = array ();
			$results = array ();
			if (empty ( $_POST ['selectedfilters'] )) // show all data
{
				$queryParams [0] = " 1=1 ";
				$awardScheme > 0 ? ($awardScheme = " AND award_scheme_id=" . $awardScheme) : ($awardScheme = " AND award_scheme_id=1");
			} else {
				$i = 0;
				$filter_ids = explode ( ',', $_POST ['selectedfilters'] );
				$result = array ();
				foreach ( $filter_ids as $filter_id ) :
					$queryParams [$i] ['filter'] = $filter_id;
					$queryParams [$i] ['clause'] = ' 1=1 ';
					$resParams = $customreportModel->applyFilterQuery ( $filter_id );
					foreach ( $resParams as $param ) {
						if ($param ['filter_table'] == 'd_fees' || $param ['filter_table'] == 'd_school_strength') {
							$temp = $customreportModel->getStaticTableVals ( $param ['filter_table'], $param ['filter_table_col_id'], $param ['filter_table_col_name'], $param ['value'] );
							$param ['value'] = $temp ['staticCol'];
							$param ['filter_table'] == 'd_fees' ? ($param ['filter_table_col_id'] = 'annual_fee') : ($param ['filter_table'] == 'd_school_strength' ? ($param ['filter_table_col_id'] = 'no_of_students') : '');
						} elseif ($param ['filter_table_col_id'] == 'province')
							$param ['value'] = "'" . addslashes ( $param ['value'] ) . "'";
						$param ['filter_table'] == 'd_award_scheme' ? $awardScheme = $param ['value'] : '';
						$queryParams [$i] ['clause'] .= ' AND ' . $param ['filter_table_col_id'] . ' ' . $param ['operator_text'] . ' ' . ($param ['operator_text'] == 'IN' ? '(' . $param ['value'] . ')' : $param ['value']);
					}
					$i ++;
				endforeach
				;
				
				$awardScheme > 0 ? ($awardScheme = " AND award_scheme_id=" . $awardScheme) : ($awardScheme = " AND award_scheme_id=1");
				// $customreportModel->generateComparisonData ();
				// get all the possible combinations for the given set of filters
				$num = count ( $filter_ids );
				if ($num) {
					$sqlSubQuery = "select assessment_id from temp_reviewData where ";
					// The total number of possible combinations where one is no element being present
					$total = pow ( 2, $num );
					// Loop through each possible combination
					for($i = 0; $i < $total; $i ++) {
						// For each combination check if each bit is set
						$key = '';
						$val = '';
						for($j = 0; $j < $num; $j ++) {
							// Is bit $j set in $i?
							if (pow ( 2, $j ) & $i) {
								$key .= ($key != '' ? "+" : '') . $_POST ['sel-filter'] [$queryParams [$j] ['filter']];
								$val .= ($val != '' ? ' UNION ' : '') . $sqlSubQuery . $queryParams [$j] ['clause'];
							}
						}
						$key != '' ? ($results [$key] = $val) : '';
					}
					// print_r($results);die;
				}
			}
			// for default case
                        $client_id = $this->user['client_id'];
			$customreportModel = new customreportModel ();
			$assessmentModel = new assessmentModel();
			$assessment_type_id = 1;//for school reviews
			$lastReviewData = $customreportModel->getClientLatestReviewData($client_id,$assessment_type_id);
			$clientModel=new clientModel();
			$lastKPAratings = $assessmentModel->getKPAratingsforAssessment($lastReviewData['assessment_id'],4);//get external reviewer ratings for the last review						
                        $kpaIds = array_column($lastKPAratings, 'kpa_id');
                        $kpasConcat = implode(',',$kpaIds);
			$customreportModel->createKpaRatingsDashboard ($kpasConcat);
			// $data = $customreportModel->getKpaRatingsDasboard ();
			
			// print_r($results);die;
			$kpaMatrix = array ();
			$i = 0;
			$maxArr = array (
					0 
			);
			// get distinct kpas
			$kpas = $customreportModel->getKpasDasboard ();
			foreach ( $kpas as $row ) {
				$kpaMatrix [$i] ['name'] = $row ['kpa_name'];
				if (empty ( $results )) {
					$kpaData = $customreportModel->getKpaRatingsDasboard ( $row ['kpa_id'] );
					$j = 0;
					foreach ( $kpaData as $kRow ) :
						$kpaMatrix [$i] [$j] ['default'] = $kRow ['num'];
						$kpaMatrix [$i] [$j] ['rating'] = $kRow ['rating'];
						$maxScaleY = $kRow ['num'] > $maxScaleY ? $kRow ['num'] : $maxScaleY;
						$j ++;
						$totalAwards += $kRow ['num'];
					endforeach
					;
				}
				if (! empty ( $results )) {
					$filterNumAdd = 0;
					foreach ( $results as $key => $res ) : // for each filter combination
						$kpaData = $customreportModel->getKpaRatingsDasboard ( $row ['kpa_id'], $res );
						$j = 0;
						if (! empty ( $kpaData ))
							foreach ( $kpaData as $kRow ) :
								$kpaMatrix [$i] [$j] [$key] = $kRow ['num'];
								$kpaMatrix [$i] [$j] ['rating'] = $kRow ['rating'];
								if (count ( $results ) > 1)
									$filterNumAdd += $kRow ['num'];
								else
									$maxScaleY = $kRow ['num'] > $maxScaleY ? $kRow ['num'] : $maxScaleY;
								$j ++;
								$totalAwards += $kRow ['num'];
							endforeach
						;
						count ( $results ) > 1 ? ($maxScaleY = $filterNumAdd > $maxScaleY ? $filterNumAdd : $maxScaleY) : '';
					endforeach
					;
				}
				
				$i ++;
			}
			// print_r($kpaMatrix);die;
			
			$this->apiResult ["data"] = $totalAwards >= $user_json ["Dashboard"] ["numSchools"] ? $kpaMatrix : "";
			$this->apiResult ["maxScaleY"] = $maxScaleY;
			$this->apiResult ["status"] = 1;
			return;
		}
	}
	function addAdditionalRefTeamRowAction() {
		if (empty ( $_POST ['sn'] )) {
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		} else {
			$this->apiResult ["status"] = 1;
			$aqsDataModel = new aqsDataModel ();
			$this->apiResult ["content"] = $aqsDataModel->getAqsAdditonalRefHtmlRow ( $_POST ['sn'], '', '', '', '', 1 );
		}
	}
	function createResourceAction() {
		$maxUploadFileSize = 104857600; // in bytes
               //print_r($_POST);die;
		$resource_title = isset ( $_POST ['resource_title'] ) ? trim ( $_POST ['resource_title'] ) : '';
                $province = empty ( $_POST ['province'] ) ? 0 : $_POST ['province'];
                $school = empty ( $_POST ['school'] ) ? 0 : $_POST ['school'];
                $network = empty ( $_POST ['network'] ) ? 0 : $_POST ['network'];
                $rec_user = empty ( $_POST ['rec_user'] ) ? 0 : $_POST ['rec_user'];
                $network_option = empty ( $_POST ['school_related_to'] ) ? 0 : $_POST ['school_related_to'];
                $user_roles = empty ( $_POST ['roles'] ) ? array() : $_POST ['roles'];
                $resource_type = isset ( $_POST ['resource_type'] ) ?  1 : 0;
                $resource_link_type=isset($_POST['resource_link_type'])?$_POST['resource_link_type']:'';
                $resource_url=(isset($_POST['resource_url']) && $resource_link_type=="url")?trim($_POST['resource_url']):'';
                $tags = isset ( $_POST ['dir_tags'] ) ?  trim($_POST ['dir_tags']) : 0;
                $resourceModel = new resourceModel ();
                $is_province = 0;
                
                if(!empty($network)) {
                    $provinceInNetwork = $resourceModel->getProvinceInNetwork($network);
                    if(!empty($provinceInNetwork['num_province'])) {
                        $is_province = 1;
                    }
                }
               
		if (empty ( $resource_title )) {
			$this->apiResult ["message"] = "Title of the resource cannot be empty\n";
		} else if (empty ( $resource_link_type )) {
			$this->apiResult ["message"] = "Add Resource cannot be empty\n";
		} else if ($resource_link_type=="file" && empty ( $_FILES ['file'] )) {
			$this->apiResult ["message"] = "No file uploaded with this request\n";
		} else if ($resource_link_type=="file" && $_FILES ['file'] ['error'] > 0) {
			$this->apiResult ["message"] = "File contains error or error occurred while uploading\n";
		} else if ($resource_link_type=="file" && ! ($_FILES ['file'] ['size'] > 0)) {
			$this->apiResult ["message"] = "Invalid file size or empty file\n";
		} else if ($resource_link_type=="file" && $_FILES ['file'] ['size'] > $maxUploadFileSize) {
			$this->apiResult ["message"] = "File too big\n";
		} else if ($resource_link_type=="url" && empty ( $resource_url )) {
			$this->apiResult ["message"] = "Resource url cannot be empty\n";
		} else if ($resource_link_type=="url" && !preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$resource_url)){
                        $this->apiResult ["message"] = "Invalid URL. Please enter the correct URL\n";        
                } else if (! in_array ( "upload_resources", $this->user ['capabilities'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['directory'] )) {
			$this->apiResult ["message"] = "Folder not selected\n";
		}else if (  $resource_type == 1 && $network_option == 1 && empty($network) && $resource_type ) {
			$this->apiResult ["message"] = "Networks cannot be empty\n";
		}else if (  $resource_type == 1 && $network_option == 1 && !empty($is_province) && empty($province)  ) {
			$this->apiResult ["message"] = "Province cannot be empty\n";
		}else if ($resource_type == 1  && empty($school) && $resource_type) {
			$this->apiResult ["message"] = "Schools cannot be empty\n";
		}else if (empty ( $_POST ['roles'] ) && $resource_type == 1 ) {
			$this->apiResult ["message"] = "User Role cannot be empty\n";
		}else if ($resource_type == 1  && empty($rec_user) && $resource_type) {
			$this->apiResult ["message"] = "Users cannot be empty\n";
		}else {
                        $error=0;
                        $message="";
                        
                        if($resource_link_type=="file"){
			$resUploadResult = $this->uploadResourceFileAction ();
                        
                        if (empty ( $resUploadResult )) {
                            
                                $message = "No file uploaded with this request\n";
                                $error++;
				
                        }else{
                            if (!isset ( $resUploadResult ['id'] ) || $resUploadResult ['id'] <= 0) {
                                
                                       if ($resUploadResult ['message'] != '') {
						$message = $resUploadResult ['message'];
                                                $error++;
					} else {
						$message = "Unable to make entry in database";
                                                $error++;
					}
                            }
                        }
                        
                        }
                        
			       if ($error==0){
					
					$resource_description = trim ( $_POST ['resource_description'] );
					$file_id = $resource_link_type=="file"?$resUploadResult ['id']:Null;
					$file_name = $resource_link_type=="file"?$_FILES ['file'] ['name']:'';
					$file_size = $resource_link_type=="file"?$_FILES ['file'] ['size']:'';
					$resource_uploaded_by = $this->user ['user_id'];
                                        $directory_id = trim($_POST ['directory']);
                                        $roles_string = '';
                                        
                                        $user_roles = isset( $_POST ['roles'])? $_POST ['roles']:array();
                                        
                                        if(count($user_roles)>= 1) {
                                            $roles_string = implode ( ',', $user_roles );
                                            if(empty($roles_string)) {
                                                $roles_string .= "1,2,8";
                                            }else {
                                                $roles_string .= ",1,2,8";
                                            }
                                        }
					if ( ! empty ( $_POST ['roles'] ) && $_POST ['roles'] [0] == 8 ) {
						$roleId = $_POST ['roles'] [0];
					} else {
						$roleId = empty ( $_POST ['role_id'] ) ? 0 : $_POST ['role_id'];
					}
                                        
					$province = empty ( $_POST ['province'] ) ? 0 : $_POST ['province'];
                                        $school = empty ( $_POST ['school'] ) ? 0 : $_POST ['school'];
                                        $network = empty ( $_POST ['network'] ) ? 0 : $_POST ['network'];
                                        $rec_user = empty ( $_POST ['rec_user'] ) ? 0 : $_POST ['rec_user'];
					
					$resourceModel = new resourceModel ();
                                        $resourceExistStatus = $resourceModel->getResourceByTitle($resource_title);
                                        $parentsIdsArray = array();
                                        $parentsIds = $resourceModel->getAllParents($directory_id,$parentsIdsArray);
                                        $parentsIds[] = $directory_id;
//                                       / print_r($resourceExistStatus);
                                        $res_network_result = 0;
                                        if(count($resourceExistStatus)< 1) {
                                                    //$this->db->start_transaction ();
                                                    $list = $resourceModel->getResourceParentDirectoryList($this->user['role_ids'],$this->user['user_id']);
                                                    $parents = rtrim($this->getParentDirectory($list,$directory_id,''),'/');
                                                    $resource_id = $resourceModel->createResource ( $resource_title, $resource_description, $resource_uploaded_by, $file_id, $roles_string,$parents,$directory_id,$network_option,$resource_type,$file_name,0,$resource_link_type,$resource_url,$tags);
                                                if($resource_id > 0 && $resource_type ){
                                                    $res_network_result = $resourceModel->createResourceUsers('',$province,$school,$rec_user,$network,'',$resource_id,'' );
                                                    $res_folder_result = $resourceModel->createFolderUsers('', $province, $school, $rec_user, $network,0,$directory_id,'',$parentsIds,$resource_type,'',$user_roles);
                                                   // createResourceUsers($resresult,$province,$school,$rec_user,$network,$from='',$resource_id='' ,$network_option='')
                                                
                                                    if ($resource_id > 0  && $res_network_result > 0) {
                                                            $this->apiResult ["status"] = 1;
                                                            $this->apiResult ["message"] = "Resource successfully added";
                                                    } else {
                                                            //$this->db->rollback ();
                                                            $this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
                                                    }
                                                }else if($resource_id > 0 && !$resource_type) {
                                                    
                                                    $this->apiResult ["status"] = 1;
                                                    $this->apiResult ["message"] = "Resource successfully added";
                                                } else {
                                                            //$this->db->rollback ();
                                                            $this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
                                                    }
                                        }else{
                                            $this->apiResult ["status"] = 0;
                                            $this->apiResult ["message"] = "Resource title already exist.\n";
                                        }
					// }
                        }else{
                             $this->apiResult ["status"] = 0;
                             $this->apiResult ["message"]=$message;
                        }
		}
	}
         // function to find all parent of a directory
        public function getParentDirectory(array &$elements, $child_dir_id = 0,$parent_path='') {

            $branch = array();
            foreach ($elements as $element) {
                if ($element['directory_id'] == $child_dir_id) {
                    $parent_path .=$element['directory_id']."/";
                    $parent_path = $this->getParentDirectory($elements, $element['ParentCategoryId'],$parent_path);
                    //$branch[$element['directory_id']] = $element;
                }
            }
            return $parent_path;
        }
	function updateResourceAction() {
                //print_r($_POST);
		$maxUploadFileSize = 104857600; // in bytes
		$resource_title = isset ( $_POST ['resource_title'] ) ? trim ( $_POST ['resource_title'] ) : '';
		$directory_id  = isset ( $_POST ['directory'] ) ? trim ( $_POST ['directory'] ) : '';
                $province = empty ( $_POST ['province'] ) ? 0 : $_POST ['province'];
                $school = empty ( $_POST ['school'] ) ? 0 : $_POST ['school'];
                $network = empty ( $_POST ['network'] ) ? 0 : $_POST ['network'];
                $rec_user = empty ( $_POST ['rec_user'] ) ? 0 : $_POST ['rec_user'];
                $network_option = empty ( $_POST ['network_option'] ) ? 0 : $_POST ['network_option'];
                $resource_type = isset ( $_POST ['resource_type'] ) ?  1 : 0;
                $resource_link_type=isset($_POST['resource_link_type'])?$_POST['resource_link_type']:'';
                $resource_url=(isset($_POST['resource_url']) && $resource_link_type=="url")?trim($_POST['resource_url']):'';
                 $tags = isset ( $_POST ['dir_tags'] ) ?  trim($_POST ['dir_tags'] ) : '';
                $file_name = '';
                //print_r($this->user ['capabilities']);
                // echo "<pre>";print_r($_POST);
		if (empty ( $_POST ['resource_file_id'] ) || empty ( $_POST ['resource_id'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $resource_title )) {
			$this->apiResult ["message"] = "Title of the resource cannot be empty\n";
		} else if (empty ( $resource_link_type )) {
			$this->apiResult ["message"] = "Add Resource cannot be empty\n";
		} else if ($resource_link_type=="url" && empty ( $resource_url )) {
			$this->apiResult ["message"] = "Resource url cannot be empty\n";
		} else if ($resource_link_type=="url" && !preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$resource_url)){
                        $this->apiResult ["message"] = "Invalid URL. Please enter the correct URL\n";        
                } else if (empty ( $_POST ['roles'] )  && $resource_type) {
			$this->apiResult ["message"] = "User Role cannot be empty\n";
		} else if (empty ( $_POST ['directory'] )) {
			$this->apiResult ["message"] = "Resource folder cannot be empty\n";
		}else if (empty($school) && $resource_type) {
			$this->apiResult ["message"] = "Schools cannot be empty\n";
		}else if (empty($rec_user) && $resource_type) {
			$this->apiResult ["message"] = "Users cannot be empty\n";
		}		// else if (empty($_FILES['file'])) {
		// $this->apiResult["message"] = "No file uploaded with this request\n";
		// }
		else if (! in_array ( "upload_resources", $this->user ['capabilities'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else {
                        $error=0;
                        $file_id=isset($_POST['file_id'])?$_POST['file_id']:0;
                        //print_r($_FILES);
			if ((! empty ( $_FILES ['resource_file'] ['name']) || $file_id==0) && $resource_link_type=="file") {
                               //print_r($_FILES ['resource_file']);
				if (empty ( $_FILES ['resource_file'] )) {
					$this->apiResult ["message"] = "No file uploaded with this request\n";
                                        $error++;
				} else if (empty($_FILES ['resource_file'] ['name'])) {
					$this->apiResult ["message"] = "No file uploaded with this request\n";
                                        $error++;
				}else if ($_FILES ['resource_file'] ['error'] > 0) {
					$this->apiResult ["message"] = "File contains error or error occurred while uploading\n";
                                        $error++;
				} else if (! ($_FILES ['resource_file'] ['size'] > 0)) {
					$this->apiResult ["message"] = "Invalid file size or empty file\n";
                                        $error++;
				} else if ($_FILES ['resource_file'] ['size'] > $maxUploadFileSize) {
					$this->apiResult ["message"] = "File too big\n";
                                        $error++;
				} else {
                                        if(isset($_POST['file_id']) && $_POST ['file_id']>0){
                                            
					$resUploadResult = $this->updateResourceFileAction ( $_POST ['file_id'] );
					if (! empty ( $resUploadResult )) {
						if (isset ( $resUploadResult ['id'] ) && $resUploadResult ['id'] > 0) {
							$file_id = $resUploadResult ['id'];
							$file_name = $_FILES ['resource_file'] ['name'];
						} else {
							$this->apiResult ["message"] = "No file uploaded with this request\n";
                                                        $error++;
						}
					}
                                        
                                        }else{
                                                $resUploadResult = $this->uploadUpdateResourceFileAction ();

                                                if (empty ( $resUploadResult )) {

                                                        $this->apiResult ["message"] = "No file uploaded with this request\n";
                                                        $error++;

                                                }else{
                                                    if (!isset ( $resUploadResult ['id'] ) || $resUploadResult ['id'] <= 0) {

                                                               if ($resUploadResult ['message'] != '') {
                                                                        $this->apiResult ["message"] = $resUploadResult ['message'];
                                                                        $error++;
                                                                } else {
                                                                        $this->apiResult ["message"] = "Unable to make entry in database";
                                                                        $error++;
                                                                }
                                                    }else{
                                                        
                                                        $file_id = $resUploadResult ['id'];
							$file_name = $_FILES ['resource_file'] ['name'];
                                                    }
                                                }
                                        }
				}
			}
                        
			if($error>0){
                          return;  
                        }

			$resource_description = trim ( $_POST ['resource_description'] );
			$resource_uploaded_by = $this->user ['user_id'];
			$roles_string = isset($_POST ['roles'])?implode ( ',', $_POST ['roles'] ):'';
			$user_role = isset($_POST ['roles'])?$_POST ['roles']:'';
			
			if (isset($_POST ['roles']) && $_POST ['roles'] [0] == 8 && ! empty ( $_POST ['roles'] )) {
				$roleId = $_POST ['roles'] [0];
			} else {
				$roleId = empty ( $_POST ['role_id'] ) ? 0 : $_POST ['role_id'];
			}
			$resourceModel = new resourceModel ();
                        //$resourceModel = new resourceModel ();
                        $list = $resourceModel->getResourceParentDirectoryList($this->user['role_ids'],$this->user['user_id']);
                        $parents = rtrim($this->getParentDirectory($list, $directory_id, ''), '/');
			$d_resources_data = array (
					'resource_title' => $resource_title,
					'resource_description' => $resource_description,
					'resource_updated_by' => $this->user ['user_id'],
					'resource_modified_at' => date ( 'Y-m-d H:i:s' ) ,
					'parents_directory_path' => $parents ,
					'directory_id' => $directory_id ,
					'network_option' => $network_option,
					'resource_type' => $resource_type, 
					'file_name' => $file_name ,
					'tags' => $tags 
			);
                        
			$h_resource_file_data = array (
                                        'file_id'=>$resource_link_type=="url"?Null:$file_id,
                                        'resource_link_type' =>$resource_link_type,
                                        'resource_url'=>$resource_url,
					'user_role_id' => $roles_string,
					'modified_at' => date ( 'Y-m-d H:i:s' ) 
			);
                        
                        $res_network_result = 0;
                        $parentsIdsArray = array();
                        $parentsIds = $resourceModel->getAllParents($directory_id,$parentsIdsArray);
                        $parentsIds[] = $directory_id;
                        //print_r($parentsIds);die;
			//$this->db->start_transaction ();
                        $resresult = $resourceModel->updateResource ( $h_resource_file_data, $d_resources_data, $_POST ['resource_file_id'], $_POST ['resource_id'],$resource_type);
                        if($resource_type && $resresult) {
                             $res_network_result = $resourceModel->createResourceUsers($resresult, $province, $school, $rec_user, $network,1,$_POST ['resource_id'],$network_option);
                             $res_folder_result = $resourceModel->createFolderUsers($resresult, $province, $school, $rec_user, $network,0,$directory_id,'',$parentsIds,$resource_type,'',$user_role);
                            
                        }else if($resresult) {
                            $resourceModel ->deleteResourceUsers($_POST ['resource_id']) ;
                        }
			if ($resresult && !$resource_type) {
				// $this->set("resource_detail", 'hkjdfghdjhfdj');
				$this->apiResult ["status"] = 1;
				$this->apiResult ["message"] = "Resource successfully updated";
			}else if ($resresult && $res_network_result) {
				// $this->set("resource_detail", 'hkjdfghdjhfdj');
				$this->apiResult ["status"] = 1;
				$this->apiResult ["message"] = "Resource successfully updated";
			} else {
				 //$this->db->rollback();
				$this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
			}
		}
	}
        function updateFolderAction() {
		$directory_id = empty ( $_POST ['directory_id'] ) ? 0 : $_POST ['directory_id'];
		$province = empty ( $_POST ['province'] ) ? 0 : $_POST ['province'];
                $school = empty ( $_POST ['school'] ) ? 0 : $_POST ['school'];
                $network = empty ( $_POST ['network'] ) ? 0 : $_POST ['network'];
                $rec_user = empty ( $_POST ['rec_user'] ) ? 0 : $_POST ['rec_user'];
                $network_option = empty ( $_POST ['network_option'] ) ? 0 : $_POST ['network_option'];
                $user_roles = empty ( $_POST ['roles'] ) ? array() : $_POST ['roles'];
                $resource_type = isset ( $_POST ['resource_type'] ) ?  1 : 0;
                $tags = isset ( $_POST ['dir_tags'] ) ?  trim($_POST ['dir_tags']) : 0;
                $is_province = 0;
                $resourceIds = array();
                $resourceModel = new resourceModel();
                if(!empty($network)) {
                    $provinceInNetwork = $resourceModel->getProvinceInNetwork($network);
                    if(!empty($provinceInNetwork['num_province'])) {
                        $is_province = 1;
                    }
                }
                if (!in_array("upload_resources", $this->user ['capabilities']))
                    $this->apiResult ["message"] = "You are not authorized to perform this task.\n";
                else if (empty(trim($_POST ['folder_name'])))
                    $this->apiResult ["message"] = "Folder Name cannot be empty.\n";
                else if (empty($_POST ['directory']))
                     $this->apiResult ["message"] = "Select a Folder.\n";
                else if (  $resource_type == 1 && $network_option == 1 && empty($network) && $resource_type ) {
                     $this->apiResult ["message"] = "Networks cannot be empty\n";
		}else if (  $resource_type == 1 && $network_option == 1 && !empty($is_province) && empty($province)  ) {
			$this->apiResult ["message"] = "Province cannot be empty\n";
		}else if ($resource_type == 1  && empty($school) && $resource_type) {
			$this->apiResult ["message"] = "Schools cannot be empty\n";
		}else if (empty ( $_POST ['roles'] ) && $resource_type == 1 ) {
			$this->apiResult ["message"] = "User Role cannot be empty\n";
		}else if ($resource_type == 1  && empty($rec_user) && $resource_type) {
			$this->apiResult ["message"] = "Users cannot be empty\n";
                }else if ($directory_id == $_POST ['directory']){
                    $this->apiResult ["message"] = "Please select diffrent folder\n";
                }else {
			
			
                       // $this->set("directory_list",$this->buildTree($list,0));
			$roles_string = isset($_POST ['roles'])?implode ( ',', $_POST ['roles'] ):'';
			
			if (isset($_POST ['roles']) && $_POST ['roles'] [0] == 8 && ! empty ( $_POST ['roles'] )) {
				$roleId = $_POST ['roles'] [0];
			} else {
				$roleId = empty ( $_POST ['role_id'] ) ? 0 : $_POST ['role_id'];
			}
			$resourceModel = new resourceModel ();
                        //$resourceModel = new resourceModel ();
                       // $list = $resourceModel->getResourceParentDirectoryList($this->user['role_ids'],$this->user['user_id']);
                      //  $parents = rtrim($this->getParentDirectory($list, $directory_id, ''), '/');
                        //echo $_POST ['directory'];
			$d_resources_data = array (
					'directory_name' => trim($_POST ['folder_name']),
					'parent_directory_id' => $_POST ['directory'] ,
					'network_option' => $network_option,
					'directory_type' => $resource_type,
					'tags' => $tags,
                                       // 'user_role_id'=>$roles_string,
			);
			
                        $res_network_result = 0;
			//$this->db->start_transaction ();
                        
                        $parentId = $resourceModel->getFolderParent($directory_id);
                        $directoryParentId = $resourceModel->getFolderParent($_POST ['directory']);
                        $dirIds = array($directory_id);
                        $dir = $resourceModel->getResourceList($directory_id,$dirIds) ;
                        
                        //print_r($dir);
                        if(!empty($dir)) {
                            $resourceIds = $resourceModel->getFolderResources($dir);
                        }
                        $parentsIds = array();
                        $parentsFolderIds = array();
                        $parents = array();
                        
                        
                        if(isset($_POST ['directory']) && $_POST ['directory'] > 1){
                            $parents = $resourceModel->getAllParents($_POST ['directory'],$parentsIds);
                            $parentsFolderIds[] = $_POST ['directory'];
                        }
                        $parentsFolderIds = array_merge($parents,$parentsFolderIds);
                       
                          //echo "<pre>";
                        //  print_r($dir);
                          //print_r($parentsFolderIds);die;
                       // echo $parentId['parent_directory_id'];
                        $list = $resourceModel->getResourceParentDirectoryList($this->user['role_ids'],$this->user['user_id']);
                        // echo"<pre>"; 
                        // print_r($resourceIds);die;
                        //echo $parentId['parent_directory_id'];
                        if($parentId['parent_directory_id'] !=  $directoryParentId['parent_directory_id'] ) {
                            $childList = array();
                            $folderChild = $this->buildChildTree($list,$parentId['parent_directory_id'],$childList);
                            //print_r($folderChild);die;
                            if(!empty($folderChild) && in_array($_POST ['directory'],$folderChild))
                                $parentUpdateStatus = $resourceModel->getUpdateParent($directory_id,$parentId['parent_directory_id']);
                        }
                        $resresult = $resourceModel->updateFolder ( $d_resources_data,  $directory_id);
                        if($resource_type && $resresult) {
                            $res_network_result = $resourceModel->createFolderUsers($resresult, $province, $school, $rec_user, $network,1,$directory_id,$network_option,$dir,$resource_type,$roles_string,$user_roles);
                            
                            if(count($parentsFolderIds)) 
                                $res_parent_result = $resourceModel->updateParentUsers($resresult, $province, $school, $rec_user, $network,0,$_POST ['directory'],$network_option,$parentsFolderIds,$resource_type,$roles_string,$user_roles);
                            // $resourceIds = $resourceModel->getResourceIds($directory_id);
                             //print_r($resourceIds);
                            if(!empty($resourceIds))
                                $res_users = $resourceModel->createFolderResourceUsers($resresult, $province, $school, $rec_user, $network,1,$network_option,$resourceIds,$resource_type,$roles_string);
                        }else if($resresult) {
                            $resourceModel ->deleteFolderUsers($directory_id) ;
                        }
			if ($resresult && !$resource_type) {
				// $this->set("resource_detail", 'hkjdfghdjhfdj');
				$this->apiResult ["status"] = 1;
				$this->apiResult ["message"] = "Resource successfully updated";
			}else if ($resresult && $res_network_result) {
				// $this->set("resource_detail", 'hkjdfghdjhfdj');
				$this->apiResult ["status"] = 1;
				$this->apiResult ["message"] = "Resource successfully updated";
			} else {
				 //$this->db->rollback();
				$this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
			}
		}
	}
	function uploadResourceFileAction() {
		$maxUploadFileSize = 104857600; // in bytes
		$allowedExt = array ("jpeg","png","gif","jpg","avi","mp4","mov","doc","docx","txt","xls","xlsx","pdf","csv",'xml','pptx','ppt','cdr','mp3','wav','wmv','flv','mkv','vob');
		
		$nArr = explode ( ".", $_FILES ['file'] ['name'] );
		$ext = strtolower ( array_pop ( $nArr ) );
		if (in_array ( $ext, $allowedExt ) && count ( $nArr ) > 0) {
			$newName = str_replace ( " ", "_", substr ( implode ( "_", $nArr ), 0, 35 ) ) . "_" . rand ( 1, 9999 ) . "_" . time () . "." . $ext;
			if (upload_file ( UPLOAD_PATH_RESOURCE . "" . $newName, $_FILES ['file'] ['tmp_name'])) {
				$diagnosticModel = new diagnosticModel ();
				$id = $diagnosticModel->addUploadedFile ( $newName, $this->user ['user_id'],$_FILES ['file'] ['size'] );
				if ($id > 0) {
					$this->apiResult ["message"] = "Resource file successfully uploaded";
					$this->apiResult ["status"] = 1;
					$this->apiResult ["id"] = $id;
					$this->apiResult ["name"] = $newName;
					$this->apiResult ["ext"] = $ext;
					$this->apiResult ["url"] = UPLOAD_URL_RESOURCE . "" . $newName;
				} else {
					$this->apiResult ["message"] = "Unable to make entry in database";
					//@unlink ( UPLOAD_PATH_RESOURCE . "" . $newName );
                                         deleteFile(UPLOAD_PATH_RESOURCE . "" . $newName);
				}
			} else {
				$this->apiResult ["message"] = "Error occurred while moving file\n ";
			}
		} else {
			$this->apiResult ["message"] = "Invalid file extension. Only " . implode ( ", ", $allowedExt ) . " type files are allowed\n";
		}
		
		return $this->apiResult;
		// }
	}
        
        function uploadUpdateResourceFileAction() {
		$maxUploadFileSize = 104857600; // in bytes
		$allowedExt = array ("jpeg","png","gif","jpg","avi","mp4","mov","doc","docx","txt","xls","xlsx","pdf","csv",'xml','pptx','ppt','cdr','mp3','wav','wmv','flv','mkv','vob');
		
		$nArr = explode ( ".", $_FILES ['resource_file'] ['name'] );
		$ext = strtolower ( array_pop ( $nArr ) );
		if (in_array ( $ext, $allowedExt ) && count ( $nArr ) > 0) {
			$newName = str_replace ( " ", "_", substr ( implode ( "_", $nArr ), 0, 35 ) ) . "_" . rand ( 1, 9999 ) . "_" . time () . "." . $ext;
			if (upload_file ( UPLOAD_PATH_RESOURCE . "" . $newName, $_FILES ['resource_file'] ['tmp_name'] )) {
				$diagnosticModel = new diagnosticModel ();
				$id = $diagnosticModel->addUploadedFile ( $newName, $this->user ['user_id'],$_FILES ['resource_file'] ['size'] );
				if ($id > 0) {
					$this->apiResult ["message"] = "Resource file successfully uploaded";
					$this->apiResult ["status"] = 1;
					$this->apiResult ["id"] = $id;
					$this->apiResult ["name"] = $newName;
					$this->apiResult ["ext"] = $ext;
					$this->apiResult ["url"] = UPLOAD_URL_RESOURCE . "" . $newName;
				} else {
					$this->apiResult ["message"] = "Unable to make entry in database";
					//@unlink ( UPLOAD_PATH_RESOURCE . "" . $newName );
                                         deleteFile(UPLOAD_PATH_RESOURCE . "" . $newName);
				}
			} else {
				$this->apiResult ["message"] = "Error occurred while moving file\n ";
			}
		} else {
			$this->apiResult ["message"] = "Invalid file extension. Only " . implode ( ", ", $allowedExt ) . " type files are allowed\n";
		}
		
		return $this->apiResult;
		// }
	}
        
	function updateResourceFileAction($file_id = '') {
		if ($file_id != '') {
			$maxUploadFileSize = 104857600; // in bytes
			$allowedExt = array ("jpeg","png","gif","jpg","avi","mp4","mov","doc","docx","txt","xls","xlsx","pdf","csv",'xml','pptx','ppt','cdr','mp3','wav','wmv','flv','mkv','vob');
			
			$nArr = explode ( ".", $_FILES ['resource_file'] ['name'] );
			$ext = strtolower ( array_pop ( $nArr ) );
			if (in_array ( $ext, $allowedExt ) && count ( $nArr ) > 0) {
				$newName = str_replace ( " ", "_", substr ( implode ( "_", $nArr ), 0, 35 ) ) . "_" . rand ( 1, 9999 ) . "_" . time () . "." . $ext;
				
				if (upload_file ( UPLOAD_PATH_RESOURCE . "" . $newName, $_FILES ['resource_file'] ['tmp_name'] )) {
					$resourceModel = new resourceModel ();
					
					if ($resourceModel->updateUploadedFile ( $newName, $file_id )) {
						$this->apiResult ["message"] = "Resource file successfully uploaded";
						$this->apiResult ["status"] = 1;
						$this->apiResult ["id"] = $file_id;
						$this->apiResult ["name"] = $newName;
						$this->apiResult ["ext"] = $ext;
						$this->apiResult ["url"] = UPLOAD_URL_RESOURCE . "" . $newName;
					} else {
						$this->apiResult ["message"] = "Unable to make entry in database";
						//@unlink ( UPLOAD_PATH_RESOURCE . "" . $newName );
                                                deleteFile(UPLOAD_PATH_RESOURCE . "" . $newName);
					}
				} else {
					$this->apiResult ["message"] = "Error occurred while moving file\n ";
				}
			} else {
				$this->apiResult ["message"] = "Invalid file extension. Only " . implode ( ", ", $allowedExt ) . " type files are allowed\n";
			}
			
			return $this->apiResult;
		} else {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			return $this->apiResult;
		}
	}
	function resourceStatusChangeAction() {
		if (! in_array ( "upload_resources", $this->user ['capabilities'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if ($_POST ['rstatus'] == '') {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['resource_file_id'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['resource_id'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else {
			$d_resources_data = array (
					'resource_updated_by' => $this->user ['user_id'],
					'resource_modified_at' => date ( 'Y-m-d H:i:s' ) 
			);
			$h_resource_file_data = array (
					'status' => $_POST ['rstatus'],
					'modified_at' => date ( 'Y-m-d H:i:s' ) 
			);
			$resourceModel = new resourceModel ();
			if ($resourceModel->updateResource ( $h_resource_file_data, $d_resources_data, $_POST ['resource_file_id'], $_POST ['resource_id'] )) {
				$this->apiResult ["status"] = 1;
				$this->apiResult ["message"] = "Resource successfully updated";
			}
		}
	}
	function getDiagnosticGrainHtmlRowAction() {
		if (! in_array ( "view_all_assessments", $this->user ['capabilities'] )) {
			$this->apiResult ["message"] = "You are not authorised to perform this task\n";
		} elseif (empty ( $_POST ['attr_id'] ) || empty ( $_POST ['sno'] )) {
			$this->apiResult ["message"] = "AttributeId cannot be empty\n";
		} else {
			$this->apiResult ["status"] = 1;
			$attr_id = $_POST ['attr_id'];
			$cardinality = $_POST ['cardinality'];
			$sno = $_POST ['sno'];
			$grainLevel = 1;
			switch ($attr_id) {
				case 13 :
					$grainLevel = 1;
					break;
				case 23 :
					$grainLevel = 2;
					break;
				case 24 :
					$grainLevel = 3;
					break;
				case 25 :
					$grainLevel = 4;
					break;
			}
			
			$customreportModel = new customreportModel ();
			$sub_attr_ids = $customreportModel->getSubAttr ();
			$this->apiResult ["content"] = customreportModel::getDiagnosticGrainHtmlRow ( $grainLevel, $attr_id, $cardinality, $sub_attr_ids, $sno );
		}
		
		return $this->apiResult;
	}
	function loadSubOperatorsAndValuesAction() {
		if (! (in_array ( 1, $this->user ['role_ids'] ) || in_array ( 2, $this->user ['role_ids'] ))) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['attr_id'] ) || empty ( $_POST ['cardinality'] )) {
			$this->apiResult ["message"] = "Parameter name and cardinality cannot be empty\n";
		} else {
			$customreportModel = new customreportModel ();
			$attr_id = $_POST ['attr_id'];
			$cardinality = $_POST ['cardinality'];
			$cardinalityFactor = 0;
			switch ($attr_id) {
				case 13 :
					$cardinalityFactor = $cardinality * pow ( 3, 0 );
					break;
				case 23 :
					$cardinalityFactor = $cardinality * pow ( 3, 1 );
					break;
				case 24 :
					$cardinalityFactor = $cardinality * pow ( 3, 2 );
					break;
				case 25 :
				case 27 :
					$cardinalityFactor = $cardinality * pow ( 3, 3 );
					break;
			}
			$crdnArr = array ();
			$criteria = array ();
			for($i = 1; $i <= $cardinalityFactor; $i ++)
				array_push ( $crdnArr, array (
						"id" => $i,
						"text" => $i 
				) );
			$is_judgement = $attr_id == 25 ? 1 : 0;
			if ($attr_id == 27) { // for judgement distance
				array_push ( $criteria, array (
						"rating_id" => '0',
						"rating" => "Agreement" 
				) );
				array_push ( $criteria, array (
						"rating_id" => '1',
						"rating" => "Disagreement by One" 
				) );
				array_push ( $criteria, array (
						"rating_id" => '2',
						"rating" => "Disagreement by Two" 
				) );
				array_push ( $criteria, array (
						"rating_id" => '3',
						"rating" => "Disagreement by Three" 
				) );
			} else
				$criteria = $customreportModel->getRatingForAttr ( $is_judgement );
			$this->apiResult ["operators"] = $customreportModel->getAttrSubOperators ( $attr_id );
			$this->apiResult ["cardinality"] = $crdnArr;
			$this->apiResult ["criteria"] = $criteria;
			$this->apiResult ["maxCardinality"] = $cardinalityFactor;
			$this->apiResult ["status"] = 1;
		}
	}
	function getvarListAction() {
		if (! (in_array ( 1, $this->user ['role_ids'] ) || in_array ( 2, $this->user ['role_ids'] ))) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else {
			$customreportModel = new customReportModel ();
			$varList = $customreportModel->getvarList ();
			$this->apiResult ["status"] = 1;
			$this->apiResult ['vars'] = $varList;
		}
	}
	function applyFilterGenerateAdminDataAction() {
		if (! (in_array ( 1, $this->user ['role_ids'] ) || in_array ( 2, $this->user ['role_ids'] ))) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			/*
			 * }else if(empty($_POST['filter_name'])){
			 * $this->apiResult["message"] = "Filter cannot be empty.\n";
			 */
		} else if (empty ( $_POST ['rows'] ) || empty ( $_POST ['cols'] )) {
			$this->apiResult ["message"] = "Row and columns cannot be empty.\n";
		} elseif (empty ( $_POST ['count_criteria'] )) {
			$this->apiResult ["message"] = "Criteria for counting of values cannot be empty.\n";
		} else {
			$customreportModel = new customReportModel ();
			$filter_id = empty ( $_POST ['filter_name'] ) ? 0 : $_POST ['filter_name'];
			$count_criteria = $_POST ['count_criteria'];
			$is_jd = 0;
			$queryParams = '';
			$sub_attr_data = '';
			$filterString = '';
			if ($filter_id > 0) {
				$resParams = $customreportModel->applyFilterQuery ( $filter_id );
				$temp = 0;
				
				foreach ( $resParams as $param ) {
					$filterString .= $param ['filter_attr_name'] . ' ' . $param ['operator_text'];
					$str = $customreportModel->getStringForFilterValue ( $param ['filter_table'], $param ['filter_table_col_id'], $param ['filter_table_col_name'], $param ['filter_attr_id'], $param ['operator_text'], $param ['value'], $param ['filter_f_value'], $param ['filter_s_value'] );
					$filterString .= ' (' . $str ['data'] . ') ';
					if ($param ['filter_table'] == 'd_fees' || $param ['filter_table'] == 'd_school_strength') {
						$temp = $customreportModel->getStaticTableVals ( $param ['filter_table'], $param ['filter_table_col_id'], $param ['filter_table_col_name'], $param ['value'] );
						$param ['value'] = $temp ['staticCol'];
						$param ['filter_table'] == 'd_fees' ? ($param ['filter_table_col_id'] = 'annual_fee') : ($param ['filter_table'] == 'd_school_strength' ? ($param ['filter_table_col_id'] = 'no_of_students') : '');
					} elseif ($param ['filter_table_col_id'] == 'province')
						$param ['value'] = "'" . addslashes ( $param ['value'] ) . "'";
					elseif (in_array ( $param ['filter_attr_id'], array (
							16,
							17 
					) )) {
						switch ($param ['filter_attr_id']) {
							case 16 :
								$param ['filter_table_col_id'] = 'year';
								break;
							case 17 :
								$param ['filter_table_col_id'] = 'month';
								break;
						}
					} elseif ($param ['filter_attr_id'] == 18) {
						$param ['filter_table_col_id'] = 'str_to_date(tbl.' . $param ['filter_table_col_id'] . ',"%m/%d/%Y")';
						$param ['filter_f_value'] = "'$param[filter_f_value]'";
						$param ['filter_s_value'] = "'$param[filter_s_value]'";
					} elseif ($param ['filter_attr_id'] == 21) { // external reviewer
						$param ['filter_table_col_id'] = 'assessor_id';
					}
					// grainarray
					$grainArr = array (
							13,
							23,
							24,
							25 
					); // getSubFilterData
					if (in_array ( $param ['filter_attr_id'], $grainArr )) {
						$sub_attr_data = $customreportModel->getSubFilterData ( $param ['filter_instance_id'] );
						if (! empty ( $sub_attr_data )) {
							$sub_operator = '';
							$sattr = $customreportModel->getAttrLabel ( $sub_attr_data ['filter_sub_attr_id'] );
							$filterString .= ' with ' . $sattr ['filter_attr_name'];
							$cardinality = $sub_attr_data ['filter_sub_attr_cardinality'];
							$sub_attr_idrating = 'rating' . $sub_attr_data ['filter_sub_attr_id'];
							$is_jd = $sub_attr_data ['filter_sub_attr_id'] == 27 ? 1 : 0;
							switch ($sub_attr_data ['filter_sub_attr_operator']) {
								case 9 :
									$sub_operator = '>=';
									$filterString .= ' at least '; // at least
									break;
								case 10 :
									$sub_operator = '<=';
									$filterString .= ' at most '; // at most
									break;
								case 11 :
									$sub_operator = '=';
									$filterString .= ' exactly '; // exactly
									break;
							}
							$filterString .= $cardinality;
							$sub_attr_rating = $sub_attr_data ['filter_sub_attr_rating'];
							if ($param ['filter_attr_id'] == 27)
								switch ($sub_attr_rating) {
									case 0 :
										$filterString .= ' Agreements ';
										break;
									case 1 :
										$filterString .= ' Disagreement by one ';
										break;
									case 2 :
										$filterString .= ' Disagreement by two ';
										break;
									case 3 :
										$filterString .= ' Disagreement by three ';
										break;
								}
							else {
								$r = $customreportModel->getRatingText ( $sub_attr_rating );
								$filterString .= ' ' . $r ['rating'];
							}
							$assessment_list = $customreportModel->getsubAttrBuildQuery ( $sub_attr_data ['filter_sub_attr_id'], $sub_attr_idrating, $sub_operator, $cardinality, $sub_attr_rating ); // get assessment id
							                                                                                                                                                                  // $queryParams .= $assessment_list;
							$data = $assessment_list ['score_id'] ? $assessment_list ['score_id'] : 0;
							$queryParams .= ' AND tbl.score_id IN (' . $data . ')';
						}
					}
					// check if it has sub attribute
					if ($param ['filter_attr_id'] == 18)
						$queryParams .= ' AND ' . $param ['filter_table_col_id'] . ' ' . $param ['operator_text'] . ' ' . ($param ['operator_text'] == 'IN' ? '(' . $param ['value'] . ')' : ($param ['operator_id'] == '7' ? $param ['filter_f_value'] . ' AND ' . $param ['filter_s_value'] : $param ['value']));
					else
						$queryParams .= ' AND tbl.' . $param ['filter_table_col_id'] . ' ' . $param ['operator_text'] . ' ' . ($param ['operator_text'] == 'IN' ? '(' . $param ['value'] . ')' : ($param ['operator_id'] == '7' ? $param ['filter_f_value'] . ' AND ' . $param ['filter_s_value'] : $param ['value']));
						
						// $filterString .= $param['operator_text']=='IN'?'('.$param['value'].')':($param['operator_id']=='7'?$param['filter_f_value'].' AND '.$param['filter_s_value']:$param['value']);
					$filterString .= '; ';
				}
			}
			// echo $queryParams;die;
			// join with column table and row table
			$rowAttr = $_POST ['rows'];
			$colAttr = $_POST ['cols'];
			$rowTbl = $customreportModel->getAttrValues ( $rowAttr, 0, 1 );
			$colTbl = $customreportModel->getAttrValues ( $colAttr, 0, 1 );
			
			$rowMatch = $customreportModel->getMatchingColName ( $rowTbl ['tbl_name'], $rowTbl ['tbl_col'], $rowTbl ['tbl_col_name'], $rowAttr, $colAttr, $customreportModel );
			$colMatch = $customreportModel->getMatchingColName ( $colTbl ['tbl_name'], $colTbl ['tbl_col'], $colTbl ['tbl_col_name'], $colAttr, $rowAttr, $customreportModel );
			// print_r($rowMatch);
			// print_r($colMatch);
			/*
			 * $joinQuery = " inner join {$rowTbl['tbl_name']} on {$rowTbl['tbl_name']}.{$rowMatch['col_id']} = {$rowMatch['col_text']}
			 * inner join {$colTbl['tbl_name']} on {$colTbl['tbl_name']}.{$colMatch['col_id']} = {$colMatch['col_text']}";
			 */
			$joinQuery = $rowMatch . " " . $colMatch;
			$pivotCol = $colTbl ['tbl_col_name'];
			$pivotRow = $rowTbl ['tbl_col_name'];
			switch ($rowAttr) {
				case 16 :
					$pivotRow = 'year';
					break;
				case 17 :
					$pivotRow = 'month';
					break;
				case 19 :
					$pivotRow = "concat(sc.class_name,'-' ,sc2.class_name)";
					break;
				case 12 :
					$pivotRow = 'd_client.' . $pivotRow;
					break;
				case 22 :
					$pivotRow = 'c.client_name'; // num of reviews
					break;
				case 20 :
					$pivotRow = "concat(ifnull(standard_name,''),' ',award_name)";
					break;
			}
			switch ($colAttr) {
				case 16 :
					$pivotCol = 'year';
					break;
				case 17 :
					$pivotCol = 'month';
					break;
				case 19 :
					$pivotCol = "concat(sc.class_name,'-' ,sc2.class_name)";
					break;
				case 12 :
					$pivotCol = 'd_client.' . $pivotCol;
					break;
				case 22 :
					$pivotCol = 'c.client_name'; // num of reviews
					break;
				case 20 :
					$pivotCol = "concat(ifnull(standard_name,''),' ',award_name)";
					break;
			}
			$order_by = ' ORDER BY $orderRow,$orderCol asc';
			// echo $pivotRow,$pivotCol;
			$this->apiResult ["data"] = $customreportModel->getAdminDashboardData ( $queryParams, $joinQuery, $pivotCol, $pivotRow, $count_criteria, $order_by );
			$fp = fopen ( 'data.json', 'w' );
			fwrite ( $fp, json_encode ( $this->apiResult ["data"] ) );
			fclose ( $fp );
			$this->apiResult ["status"] = 1;
			$this->apiResult ['filterString'] = ! empty ( $filterString ) ? "Filter: " . $filterString : '';
			$this->apiResult ['basis'] = $count_criteria == 'client_id' ? 'Based on count of schools' : 'Based on count of reviews';
			$this->apiResult ["message"] = $this->apiResult ["data"] == 0 ? "No results found." : "Data has been generated successfully.";
		}
	}
	public function uploadUserDetailsAction() {
		ini_set ( 'memory_limit', '50M' );
                $currentUser=isset($this->user ['user_id'])?$this->user['user_id']:NULL;
		// $maxUploadFileSize = 104857600; //in bytes
		// $this->apiResult["error_row"] = array();
		// die(print_r($_FILES['user_excel_file']));
		$allowedExt = array (
				"xls",
				"xlsx" 
		);
		$user_profile_error_data = array ();
		$user_profile_error_data_row = array ();
		if (empty ( $_FILES ['user_excel_file'] ['name'] )) {
			$this->apiResult ["message"] = "No file uploaded with this request\n";
		} else {
			$nArr = explode ( ".", $_FILES ['user_excel_file'] ['name'] );
			$ext = strtolower ( array_pop ( $nArr ) );
			if (in_array ( $ext, $allowedExt ) && count ( $nArr ) > 0) {
				$newName = str_replace ( " ", "_", substr ( implode ( "_", $nArr ), 0, 35 ) ) . "_" . rand ( 1, 9999 ) . "_" . time () . "." . $ext;
				if (@copy ( $_FILES ['user_excel_file'] ['tmp_name'], ROOT . "uploads/userProfileExcel/" . $newName )) {
					$diagnosticModel = new diagnosticModel ();
					$file_id = $diagnosticModel->addUploadedFile ( $newName, $this->user ['user_id'] );
					if ($file_id > 0) {
						// $this->apiResult["id"] = $id;
						// $this->apiResult["name"] = $newName;
						// $this->apiResult["ext"] = $ext;
						// $this->apiResult["url"] = SITEURL . "uploads/userProfileExcel/" . $newName;
						
						/**
						 * PHPExcel_IOFactory
						 */
						include_once (ROOT . "library/PHPExcel/Classes/PHPExcel/IOFactory.php");
						define ( 'CLI', (PHP_SAPI == 'cli') ? true : false );
						define ( 'EOL', CLI ? PHP_EOL : '<br />' );
						// echo date('H:i:s'), " Load from Excel2007 file", EOL;
						$objReader = PHPExcel_IOFactory::createReader ( 'Excel2007' );
						$objPHPExcel = $objReader->load ( ROOT . "uploads/userProfileExcel/" . $newName );
						// echo date('H:i:s'), " Iterate worksheets by Row", EOL;
						$wc = 0;
						foreach ( $objPHPExcel->getWorksheetIterator () as $worksheet ) {
							$wc ++;
							// echo 'Worksheet - ', $worksheet->getTitle(), EOL;
							if ($wc == 1) {
								$user_data_row = array ();
								$user_profile_data = array ();
								$contract_value1 = 0;
								$contract_value2 = 0;
								$upload_document = '';
								$user_data_row ['email'] = '';
								$client_name = '';
								$client_id = '';
								$state_name = '';
								$emergency_state_name = '';
								$medical_conditions = '';
								foreach ( $worksheet->getRowIterator () as $key => $row ) {
									// echo $key;
									if ($key < ($_POST ['start_row'])) {
										// $this->apiResult["message"] = "No file uploaded with this request\n";
										continue;
									}
									// if ($row->getRowIndex() == 4) {
									// echo ' Row number - ', $row->getRowIndex(), EOL;
									$cellIterator = $row->getCellIterator ();
									$cellIterator->setIterateOnlyExistingCells ( false ); // Loop all cells, even if it is not set
									
									foreach ( $cellIterator as $cell ) {
										if (! is_null ( $cell )) {
											$cell_val = $cell->getCalculatedValue ();
											switch ($cell->getColumn ()) {
												case 'A' :
													$cell_val != '' ? $first_name = trim ( $cell_val ) : '';
													break;
												case 'B' :
													$cell_val != '' ? $last_name = trim ( $cell_val ) : '';
													break;
												case 'C' :
													$cell_val != '' ? $user_data_row ['email'] = strtolower ( trim ( $cell_val ) ) : '';
													break;
												case 'D' :
													$cell_val != '' ? $client_name = trim ( $cell_val ) : '';
													break;
												case 'E' :
													$cell_val != '' ? $user_role = $cell_val : '';
													break;
												case 'F' :
													$cell_val != '' ? $user_profile_data ['gender'] = $cell_val : '';
													break;
												case 'G' :
													$cell_val != '' ? ($user_profile_data ['date_of_birth'] = date ( "Y-m-d", (((trim ( $cell_val ) - 25569) * 86400)) )) : '';
													break;
												case 'H' :
													$cell_val != '' ? $user_profile_data ['address'] = $cell_val : '';
													break;
												case 'I' :
													$cell_val != '' ? $user_profile_data ['town'] = $cell_val : '';
													break;
												case 'J' :
													$cell_val != '' ? $state_name = $cell_val : '';
													break;
												case 'K' :
													$cell_val != '' ? $user_profile_data ['pincode'] = $cell_val : '';
													break;
												case 'L' :
													$cell_val != '' ? $user_profile_data ['school_contact_number'] = $cell_val : '';
													break;
												case 'M' :
													$cell_val != '' ? $user_profile_data ['cell_number'] = $cell_val : '';
													break;
												case 'N' :
													$cell_val != '' ? $user_profile_data ['emergency_firstname'] = $cell_val : '';
													break;
												case 'O' :
													$cell_val != '' ? $user_profile_data ['emergency_lastname'] = $cell_val : '';
													break;
												case 'P' :
													$cell_val != '' ? $user_profile_data ['emergency_email'] = $cell_val : '';
													break;
												case 'Q' :
													$cell_val != '' ? $user_profile_data ['emergency_relationship'] = $cell_val : '';
													break;
												case 'R' :
													$cell_val != '' ? $user_profile_data ['emergency_address'] = $cell_val : '';
													break;
												case 'S' :
													$cell_val != '' ? $user_profile_data ['emergency_town'] = $cell_val : '';
													break;
												case 'T' :
													$cell_val != '' ? $emergency_state_name = $cell_val : '';
													break;
												case 'U' :
													$cell_val != '' ? $user_profile_data ['emergency_pincode'] = $cell_val : '';
													break;
												case 'V' :
													$cell_val != '' ? $user_profile_data ['emergency_home_contact_no'] = $cell_val : '';
													break;
												case 'W' :
													$cell_val != '' ? $user_profile_data ['emergency_cell_no'] = $cell_val : '';
													break;
												case 'X' :
													$cell_val != '' ? $user_profile_data ['meal_preferences'] = $cell_val : '';
													break;
												case 'Y' :
													$cell_val != '' ? $medical_conditions = $cell_val : ''; // find medical-condition ids
													break;
												case 'Z' :
													$cell_val != '' ? $user_profile_data ['other_medical_text'] = $cell_val : '';
													break;
												case 'AA' :
													$cell_val != '' ? $user_profile_data ['education'] = $cell_val : '';
													break;
												case 'AB' :
													$cell_val != '' ? $user_profile_data ['assessor_experience'] = $cell_val : '';
													break;
												case 'AC' :
													$cell_val != '' ? $hobbies = $cell_val : ''; // hobbies id and concat ~ and save
													break;
												case 'AD' :
													$cell_val != '' ? $user_profile_data ['workshop'] = $cell_val : '';
													break;
												case 'AE' :
													$cell_val != '' ? $user_profile_data ['designation'] = $cell_val : '';
													break;
												case 'AF' :
													$cell_val != '' ? $user_profile_data ['work_experience'] = $cell_val : '';
													break;
												case 'AG' :
													$cell_val != '' ? $user_profile_data ['other_hobbies_text'] = $cell_val : '';
													break;
												case 'AH' :
													$cell_val != '' ? $user_profile_data ['bank_name'] = $cell_val : '';
													break;
												case 'AI' :
													$cell_val != '' ? $user_profile_data ['ifsc_code'] = $cell_val : '';
													break;
												case 'AJ' :
													$cell_val != '' ? $user_profile_data ['account_name'] = $cell_val : '';
													break;
												case 'AK' :
													$cell_val != '' ? $user_profile_data ['account_number'] = $cell_val : '';
													break;
												case 'AL' :
													$cell_val != '' ? $user_profile_data ['pancard_name'] = $cell_val : '';
													break;
												case 'AM' :
													$cell_val != '' ? $user_profile_data ['pancard_number'] = $cell_val : '';
													break;
												case 'AN' :
													$cell_val != '' ? $user_profile_data ['branch_address'] = $cell_val : '';
													break;
												case 'AO' :
													$cell_val != '' ? $language_read = $cell_val : '';
													break;
												case 'AP' :
													$cell_val != '' ? $language_write = $cell_val : '';
													break;
												case 'AQ' :
													$cell_val != '' ? $language_speak = $cell_val : '';
													break;
												case 'AR' :
													$cell_val != '' ? $user_profile_data ['term_condition'] = ($cell_val == 'Yes' ? 1 : 0) : '';
													break;
												case 'AS' :
													$cell_val != '' ? $contract_value1 = ($cell_val == 'Yes' ? 1 : 0) : '';
													break;
												case 'AT' :
													$cell_val != '' ? $contract_value2 = ($cell_val == 'Yes' ? 2 : 0) : '';
													break;
												case 'AU' :
													$cell_val != '' ? $upload_document = $cell_val : '';
													break;
												
												default :
													'';
											}
											// echo 'Cell - ', $cell->getColumn() . ' ' . $cell->getCoordinate(), ' - ', $cell_val, EOL;
										}
									} // closing of cell foreach
									$name = (isset ( $first_name ) ? $first_name . ' ' : '') . (isset ( $last_name ) ? $last_name : '');
									if (isset ( $user_data_row ['email'] ) && $user_data_row ['email'] != '') {
										
										if ($client_name != '') {
											$clientModel = new clientModel ();
											// echo $client_name;
											$client_id = $clientModel->getClientIdByName ( $client_name );
										}
										// echo $client_id; //die('dead');
										if ($client_id == '') {
											// die($client_id.' dead');
											// $this->apiResult["message"] = "$client_name not exist";
											$this->apiResult ["error_row"] [$row->getRowIndex ()] ['name'] = $name;
											$this->apiResult ["error_row"] [$row->getRowIndex ()] ['email'] = $user_data_row ['email'];
											$this->apiResult ["error_row"] [$row->getRowIndex ()] ['error'] = "$client_name not exist";
											// unset($this->apiResult["status"]);
											// unset($this->apiResult["message"]);
											$user_profile_error_data ['name'] = $name;
											$user_profile_error_data ['client_name'] = $client_name;
											$user_profile_error_data ['email'] = $user_data_row ['email'];
											$user_profile_error_data = $user_profile_data;
											
											$user_profile_error_data_row = array (
													'row_no' => $row->getRowIndex (),
													'row_data' => json_encode ( $user_profile_error_data ),
													'file_id' => $file_id,
													'added_at' => date ( 'Y-m-d H:i:s' ) 
											);
											$this->userModel->insertUserProfileErrorLog ( $user_profile_error_data_row );
											
											$this->apiResult ["status"] = 1;
										} else { //
											if (isset ( $state_name )) {
												$user_profile_data ['state_id'] = $this->userModel->getStateIdByName ( $state_name );
											}
											if (isset ( $emergency_state_name )) {
												$user_profile_data ['emergency_state_id'] = $this->userModel->getStateIdByName ( $emergency_state_name );
											}
											
											if (isset ( $medical_conditions )) {
												$medical_conditions_arr = explode ( ',', $medical_conditions );
												if (! empty ( $medical_conditions_arr )) {
													$user_profile_data ['medical_conditions'] = '';
													foreach ( $medical_conditions_arr as $med_con_name ) {
														$med_con_id = $this->userModel->getMedConIdByName ( $med_con_name );
														$user_profile_data ['medical_conditions'] .= $med_con_id . ',';
													}
													$user_profile_data ['medical_conditions'] = rtrim ( $user_profile_data ['medical_conditions'], "," );
												}
											}
											
											if (isset ( $hobbies )) {
												$hobbies_arr = $hobbies != '' ? explode ( ',', $hobbies ) : array ();
												if (! empty ( $hobbies_arr )) {
													$user_profile_data ['hobbies'] = '';
													foreach ( $hobbies_arr as $hobbie_name ) {
														$hobbie_id = $this->userModel->getHobbieIdByName ( $hobbie_name );
														$user_profile_data ['hobbies'] .= $hobbie_id . ',';
													}
													$user_profile_data ['hobbies'] = rtrim ( $user_profile_data ['hobbies'], "," );
												}
											}
											
											if (isset ( $language_read ) || isset ( $language_write ) || isset ( $language_speak )) {
												$language_read_arr = $language_read != '' ? explode ( ',', $language_read ) : array ();
												$language_write_arr = $language_write != '' ? explode ( ',', $language_write ) : array ();
												$language_speak_arr = $language_speak != '' ? explode ( ',', $language_speak ) : array ();
												
												$language_list = array_unique ( array_merge ( $language_read_arr, $language_write_arr, $language_speak_arr ), SORT_REGULAR );
											}
											
											$user_profile_data ['contract_value'] = rtrim ( ($contract_value1 == 1 ? '1,' : '') . ($contract_value2 == 2 ? '2' : ''), ',' );
											
											$upload_document_arr = $upload_document != '' ? explode ( ',', $upload_document ) : array ();
											
											if ($user_id = $this->userModel->getUserIdByEmail ( $user_data_row ['email'] )) { // update user and user profile details
											                                                                              // echo $user_id;
												$userUpdated = $this->userModel->updateUser ( $user_id, $name );
												$user_profile_details = $this->userModel->getUserProfileRowByUserId ( $user_id );
												if (isset ( $user_role )) {
													$role_name_arr = $user_role != '' ? explode ( ',', $user_role ) : array ();
													if (! empty ( $role_name_arr )) {
														$currentRoles = $this->userModel->getUserRoles ( $user_id );
														$currentRoles = $currentRoles != "" ? explode ( ",", $currentRoles ) : array ();
														$commonValues = array_intersect ( $currentRoles, $role_name_arr );
														$rolesNeedToBeAdded = array_diff ( $role_name_arr, $commonValues );
														$rolesNeedToBeDeleted = array_diff ( $currentRoles, $commonValues );
														if (! empty ( $rolesNeedToBeDeleted )) {
															foreach ( $rolesNeedToBeDeleted as $role ) {
																$this->userModel->deleteUserRole ( $user_id, $role );
															}
														}
														if (! empty ( $rolesNeedToBeAdded )) {
															foreach ( $role_name_arr as $role_name ) {
																$roleId = $this->userModel->getRoleIdByName ( $role_name );
																$this->userModel->addUserRole ( $user_id, $roleId );
																if ($roleId == 4) {
																	$this->db->addAlerts ( 'd_user', $user_id, $name, $user_data_row ['email'], 'CREATE_EXTERNAL_ASSESSOR' );
																}
															}
														}
													}
												}
												// update language
												if (! empty ( $language_list )) {
													foreach ( $language_list as $lang ) {
														$user_lang_data = array ();
														$user_lang_data ['language_read'] = in_array ( $lang, $language_read_arr ) ? 'reading' : '';
														$user_lang_data ['language_write'] = in_array ( $lang, $language_write_arr ) ? 'writing' : '';
														$user_lang_data ['language_speak'] = in_array ( $lang, $language_speak_arr ) ? 'speaking' : '';
														$language_id = $this->userModel->getlangIdByName ( $lang );
														
														if ($lang_inc_id = $this->userModel->checkLangExistForUser ( $language_id, $user_id )) {
															$user_lang_data ['modification_date'] = date ( 'Y-m-d H:i:s' );
															$this->userModel->updateUserLanguage ( $user_lang_data, $lang_inc_id );
														} else {
															$user_lang_data ['user_id'] = $user_id;
															$user_lang_data ['language_id'] = $language_id;
															$user_lang_data ['creation_date'] = date ( 'Y-m-d H:i:s' );
															$this->userModel->addUserLanguage ( $user_lang_data );
														}
													}
												}
												
												if (! empty ( $upload_document_arr )) {
													$user_profile_data ['upload_document'] = '';
													foreach ( $upload_document_arr as $up_doc ) {
														$user_profile_data ['upload_document'] .= $diagnosticModel->addUploadedFile ( $up_doc, $this->user ['user_id'] ) . ',';
													}
													if ($user_profile_details ['upload_document'] != '') {
														$user_profile_data ['upload_document'] = $user_profile_details ['upload_document'] . ',' . $user_profile_data ['upload_document'];
													}
													$user_profile_data ['upload_document'] = rtrim ( $user_profile_data ['upload_document'], ',' );
												}
												// echo $user_profile_data['contract_value']; die;
												$user_profile_data ['contract_value'] = explode ( ',', $user_profile_data ['contract_value'] );
												$user_profile_data ['hobbies'] = explode ( ',', $user_profile_data ['hobbies'] );
												$user_profile_data ['medical_conditions'] = explode ( ',', $user_profile_data ['medical_conditions'] );
												$profileUpdated = $this->userModel->updateUserProfile ( $user_profile_data, $user_id );
											} else { // add user and user profile
												$user_profile_data ['user_id'] = $user_id = $this->userModel->createUser ( $user_data_row ['email'], $password = '', $name, $client_id,0,date("Y-m-d H:i:s"),$currentUser );
												if (isset ( $user_role )) {
													$role_name_arr = $user_role != '' ? explode ( ',', $user_role ) : array ();
													if (! empty ( $role_name_arr )) {
														foreach ( $role_name_arr as $role_name ) {
															$roleId = $this->userModel->getRoleIdByName ( $role_name );
															$this->userModel->addUserRole ( $user_id, $roleId );
															if ($roleId == 4) {
																$this->db->addAlerts ( 'd_user', $user_id, $name, $user_data_row ['email'], 'CREATE_EXTERNAL_ASSESSOR' );
															}
														}
													}
												}
												
												if (! empty ( $language_list )) {
													foreach ( $language_list as $lang ) {
														$user_lang_data = array ();
														$user_lang_data ['user_id'] = $user_id;
														$user_lang_data ['language_id'] = $this->userModel->getlangIdByName ( $lang );
														$user_lang_data ['language_read'] = in_array ( $lang, $language_read_arr ) ? 'reading' : '';
														$user_lang_data ['language_write'] = in_array ( $lang, $language_write_arr ) ? 'writing' : '';
														$user_lang_data ['language_speak'] = in_array ( $lang, $language_speak_arr ) ? 'speaking' : '';
														$user_lang_data ['creation_date'] = date ( 'Y-m-d H:i:s' );
														$this->userModel->addUserLanguage ( $user_lang_data );
													}
												}
												
												if (! empty ( $upload_document_arr )) {
													$user_profile_data ['upload_document'] = '';
													foreach ( $upload_document_arr as $up_doc ) {
														
														$user_profile_data ['upload_document'] .= $diagnosticModel->addUploadedFile ( $up_doc, $this->user ['user_id'] ) . ',';
													}
													$user_profile_data ['upload_document'] = rtrim ( $user_profile_data ['upload_document'], ',' );
												}
												// $this->userModel->updateUserLanguage($user_lang_data);
												$profileUpdated = $this->userModel->addUserProfile ( $user_profile_data );
											}
											$this->apiResult ["message"] = "User profile file successfully uploaded";
											$this->apiResult ["status"] = 1;
										}
									} else {
										// $this->apiResult["message"] = "Email can not be empty ";
										$this->apiResult ["error_row"] [$row->getRowIndex ()] ['name'] = $name;
										$this->apiResult ["error_row"] [$row->getRowIndex ()] ['email'] = $user_data_row ['email'];
										$this->apiResult ["error_row"] [$row->getRowIndex ()] ['error'] = "Email can not be empty";
										// $this->apiResult["error_row"][$row->getRowIndex()] = "Email can not be empty";
										$this->apiResult ["status"] = 1;
										$user_profile_error_data ['name'] = $name;
										$user_profile_error_data ['client_name'] = $client_name;
										$user_profile_error_data ['email'] = $user_data_row ['email'];
										$user_profile_error_data = $user_profile_data;
										// $user_data_row_json = $user_data_row;
										$user_profile_error_data_row = array (
												'row_no' => $row->getRowIndex (),
												'row_data' => json_encode ( $user_profile_error_data ),
												'file_id' => $file_id,
												'added_at' => date ( 'Y-m-d H:i:s' ) 
										);
										$this->userModel->insertUserProfileErrorLog ( $user_profile_error_data_row );
									}
									// }
								}
							}
						}
						// die('dead end');
					} else {
						$this->apiResult ["message"] = "Unable to make entry in database";
						@unlink ( ROOT . "uploads/resources/" . $newName );
					}
				} else {
					$this->apiResult ["message"] = "Error occurred while moving file\n ";
				}
			} else {
				$this->apiResult ["message"] = "Invalid file extension. Only " . implode ( ", ", $allowedExt ) . " type files are allowed\n";
			}
		}
		
		return $this->apiResult;
	}
	function saveKeyRecommendationsAction() {
                //echo"<pre>";
                //print_r($_POST);
                //echo"</pre>";
                //die();
		$diagnosticModel = new diagnosticModel ();
                 $lang_id = isset($_POST ['lang_id'])?$_POST ['lang_id']:DEFAULT_LANGUAGE;
                 $external = isset($_POST ['external'])?$_POST ['external']:0;
                 $is_collaborative = isset($_POST ['is_collaborative'])?$_POST ['is_collaborative']:0;
                 $assessor_id = isset($_POST ['assessor_id'])?$_POST ['assessor_id']:0;
		if (empty ( $_POST ['assessment_id'] )) {
			$this->apiResult ["message"] = "Review id cannot be empty\n";
		} else if (empty ( $_POST ['level_type'] )) {
			$this->apiResult ["message"] = "Level type  cannot be empty\n";
		} else if (empty ( $_POST ['instance_id'] )) {
			$this->apiResult ["message"] = "Instance Id  cannot be empty\n";
		} else if ($assessment = $diagnosticModel->getAssessmentByRole ( $_POST ['assessment_id'], 4,$lang_id ,$external,$is_collaborative,$assessor_id)) {
			$report = $diagnosticModel->getAssessmentByUser($_POST ['assessment_id'],$assessment ["user_id"],$lang_id,$external);
                        //echo "<pre>";print_r($assessment);
//			if ($assessment ['aqs_status'] != 1) {
//				
//				$this->apiResult ["message"] = "You are not authorized to fill review before School profile\n";
//			} else 
                        if ($report ['report_published'] == 1) {
				
				$this->apiResult ["message"] = "You can't update data after publishing reports\n";
			} else if ($assessment ["status"] == 1 && ! in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] )) {
				
				$this->apiResult ["message"] = "You are not authorized to update review after submission\n";
			} else if ($assessment ["status"] == 0 && $assessment ["user_id"] != $this->user ['user_id']) {
				
				$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			} else {
				$type = empty ( $_POST ['type'] ) ? '' : $_POST ['type'];
				$instance_type = $_POST ['level_name'];
				$instance_type_id = $_POST ['instance_id'];
				$assessment_id = $_POST ['assessment_id'];
				$success = true;
				
				$this->db->start_transaction ();
				
				$formKnsCelebrateNew = $_POST ['data'] ['celebrate'] [$instance_type_id];
				$formKnsImproveNew = $_POST ['data'] ['recommendation'] [$instance_type_id];
				foreach ( $formKnsCelebrateNew as $key => $cel ) {
					if (preg_match ( '/^new_*/', $key )) {
						if ($akn_id = $diagnosticModel->addAssessorKeyNote ( $_POST ['assessment_id'], $_POST ['level_type'], $instance_type_id, $cel, 'celebrate' ))
							$_POST ['data'] ['celebrate'] [$instance_type_id] [$akn_id] = $cel;
						else
							$success = false;
						unset ( $_POST ['data'] ['celebrate'] [$instance_type_id] [$key] );
					}
				}
				foreach ( $formKnsImproveNew as $key => $imp ) {
					if (preg_match ( '/^new_*/', $key )) {
                                                $aknJS=isset($_POST ['js'] ['recommendation'] [$instance_type_id][$key])?$_POST ['js'] ['recommendation'] [$instance_type_id][$key]:array();  
                                                //print_r($aknJS);
                                                
						if ($akn_id = $diagnosticModel->addAssessorKeyNote ( $_POST ['assessment_id'], $_POST ['level_type'], $instance_type_id, $imp, 'recommendation',$aknJS )){
							$_POST ['data'] ['recommendation'] [$instance_type_id] [$akn_id] = $imp;
                                                        $_POST ['js'] ['recommendation'] [$instance_type_id] [$akn_id] = $aknJS;                                        
                                                }else{
							$success = false;
						unset ( $_POST ['data'] ['recommendation'] [$instance_type_id] [$key] );
                                                unset ( $_POST ['js'] ['recommendation'] [$instance_type_id] [$key]);
                                                }
					}
				}
				
				$akns = $diagnosticModel->getAssessorKeyNotesForType ( $assessment_id, $instance_type, $instance_type_id );
				
				// print_r($akns);
				if (OFFLINE_STATUS == TRUE) {
					$uniqueID = $this->db->createUniqueID ( 'internalAssessment' );
				}
				
				$isKNComplete = true;
				
				$akn_count = 0;
				
				if (isset ( $akns ) && count ( $akns )) {
					
					$oldKN = array_filter ( $akns, function ($var) {
						if ($var ['type'] == '' || $var ['type'] == null)
							return $var;
					} );
					
					$celebrateKN = array_filter ( $akns, function ($var) {
						if ($var ['type'] == 'celebrate')
							return $var;
					} );
					
					$recommendationKN = array_filter ( $akns, function ($var) {
						if ($var ['type'] == 'recommendation')
							return $var;
					} );
					
					if (! empty ( $oldKN )) 

					{
						
						foreach ( $akns as $k => $akn ) {
							
							$akn_id = $akn ['id'];
							
							if (isset ( $_POST ['data'] [$akn_id] )) {
								
								$akn_count ++;
								
								$aknText = trim ( $_POST ['data'] [$akn_id] );
                                                                $aknJS =  isset($_POST ['js'] [$akn_id])?$_POST ['js'] [$akn_id]:array();
                                                                
                                                                $diff11 = array_diff($aknJS, explode(",",$akn ['rec_judgement_instance_id']));
                                                                $diff22 = array_diff(explode(",",$akn ['rec_judgement_instance_id']), $aknJS);
                                                                $client_diff=array_merge($diff11, $diff22);
								
								if (($aknText != $akn ['text_data']) || (isset($aknJS) && count($client_diff)>0)) {
									
									if (! $diagnosticModel->updateAssessorKeyNote ( $akn_id, $aknText,'', $aknJS,explode(",",$akn ['rec_judgement_instance_id']) )) {
										
										$success = false;
									} else {
										if (OFFLINE_STATUS == TRUE) {
											// start--> call function for save history for update assessor key notes in history table on 21-03-2016 by Mohit Kumar
											$action_assessor_key_note_json = json_encode ( array (
													"id" => $akn_id,
													"text_data" => $aknText,
													'assessment_id' => $assessment_id 
											) );
											$this->db->saveHistoryData ( $akn_id, 'assessor_key_notes', $uniqueID, 'internalAssessmentAssessorKeyNoteUpdate', $akn_id, $aknText, $action_assessor_key_note_json, 0, date ( 'Y-m-d H:i:s' ) );
											// end--> call function for save history for update assessor key notes in history table on 03-03-2016 by Mohit Kumar
										}
									}
								}
								
								if ($aknText == "")
									
									$isKNComplete = false;
							} else {
								
								if (! $diagnosticModel->deleteAssessorKeyNote ( $akn_id )) {
									
									$success = false;
								} else {
									if (OFFLINE_STATUS == TRUE) {
										// start--> call function for save history for delete assessor key notes in history table on 21-03-2016 by Mohit Kumar
										$action_assessor_key_note_json = json_encode ( array (
												"id" => $akn_id,
												'assessment_id' => $assessment_id 
										) );
										$this->db->saveHistoryData ( $akn_id, 'assessor_key_notes', $uniqueID, 'internalAssessmentAssessorKeyNoteDelete', $akn_id, $akn_id, $action_assessor_key_note_json, 0, date ( 'Y-m-d H:i:s' ) );
										// end--> call function for save history for delete assessor key notes in history table on 03-03-2016 by Mohit Kumar
									}
								}
							}
						}
					} 

					else {
						
						if (! empty ( $celebrateKN ))
							
							foreach ( $celebrateKN as $k => $akn ) {
								
								$akn_id = $akn ['id'];
								
								if (isset ( $_POST ['data'] ['celebrate'] [$instance_type_id] [$akn_id] )) {
									
									$akn_count ++;
									
									$aknText = trim ( $_POST ['data'] ['celebrate'] [$instance_type_id] [$akn_id] );
									
									if ($aknText != $akn ['text_data']) {
										
										if (! $diagnosticModel->updateAssessorKeyNote ( $akn_id, $aknText, 'celebrate' )) {
											
											$success = false;
										} else {
											if (OFFLINE_STATUS == TRUE) {
												// start--> call function for save history for update assessor key notes in history table on 21-03-2016 by Mohit Kumar
												$action_assessor_key_note_json = json_encode ( array (
														"id" => $akn_id,
														"text_data" => $aknText,
														"type" => 'celebrate',
														'assessment_id' => $assessment_id 
												) );
												$this->db->saveHistoryData ( $akn_id, 'assessor_key_notes', $uniqueID, 'internalAssessmentAssessorKeyNoteUpdate', $akn_id, $aknText, $action_assessor_key_note_json, 0, date ( 'Y-m-d H:i:s' ) );
												// end--> call function for save history for update assessor key notes in history table on 03-03-2016 by Mohit Kumar
											}
										}
									}
									
									if ($aknText == "")
										
										$isKNComplete = false;
								} else {
									
									if (! $diagnosticModel->deleteAssessorKeyNote ( $akn_id )) {
										
										$success = false;
									} else {
										if (OFFLINE_STATUS == TRUE) {
											// start--> call function for save history for delete assessor key notes in history table on 21-03-2016 by Mohit Kumar
											$action_assessor_key_note_json = json_encode ( array (
													"id" => $akn_id,
													'assessment_id' => $assessment_id 
											) );
											$this->db->saveHistoryData ( $akn_id, 'assessor_key_notes', $uniqueID, 'internalAssessmentAssessorKeyNoteDelete', $akn_id, $akn_id, $action_assessor_key_note_json, 0, date ( 'Y-m-d H:i:s' ) );
											// end--> call function for save history for delete assessor key notes in history table on 03-03-2016 by Mohit Kumar
										}
									}
								}
							}
						
						if (! empty ( $recommendationKN ))
							
							foreach ( $recommendationKN as $k => $akn ) {
								$akn_id = $akn ['id'];
								
								if (isset ( $_POST ['data'] ['recommendation'] [$instance_type_id] [$akn_id] )) {
									
									$akn_count ++;
									
									$aknText = trim ( $_POST ['data'] ['recommendation'] [$instance_type_id] [$akn_id] );
									$aknJS =  isset($_POST ['js'] ['recommendation'] [$instance_type_id] [$akn_id])?$_POST ['js'] ['recommendation'] [$instance_type_id] [$akn_id]:array();
									//print_r($aknJS);
                                                                        $diff11 = array_diff($aknJS, explode(",",$akn ['rec_judgement_instance_id']));
                                                                        $diff22 = array_diff(explode(",",$akn ['rec_judgement_instance_id']), $aknJS);
                                                                        $client_diff=array_merge($diff11, $diff22);
                                                                
                                                                        if (($aknText != $akn ['text_data']) || (isset($aknJS) && count($client_diff)>0)) {
										
										if (! $diagnosticModel->updateAssessorKeyNote ( $akn_id, $aknText, 'recommendation', $aknJS ,explode(",",$akn ['rec_judgement_instance_id']) )) {
											
											$success = false;
										} else {
											if (OFFLINE_STATUS == TRUE) {
												// start--> call function for save history for update assessor key notes in history table on 21-03-2016 by Mohit Kumar
												$action_assessor_key_note_json = json_encode ( array (
														"id" => $akn_id,
														"text_data" => $aknText,
														"type" => 'recommendation',
														'assessment_id' => $assessment_id 
												) );
												$this->db->saveHistoryData ( $akn_id, 'assessor_key_notes', $uniqueID, 'internalAssessmentAssessorKeyNoteUpdate', $akn_id, $aknText, $action_assessor_key_note_json, 0, date ( 'Y-m-d H:i:s' ) );
												// end--> call function for save history for update assessor key notes in history table on 03-03-2016 by Mohit Kumar
											}
										}
									}
									
									if ($aknText == "")
										
										$isKNComplete = false;
								} else {
									
									if (! $diagnosticModel->deleteAssessorKeyNote ( $akn_id )) {
										
										$success = false;
									} else {
										if (OFFLINE_STATUS == TRUE) {
											// start--> call function for save history for delete assessor key notes in history table on 21-03-2016 by Mohit Kumar
											$action_assessor_key_note_json = json_encode ( array (
													"id" => $akn_id,
													'assessment_id' => $assessment_id 
											) );
											$this->db->saveHistoryData ( $akn_id, 'assessor_key_notes', $uniqueID, 'internalAssessmentAssessorKeyNoteDelete', $akn_id, $akn_id, $action_assessor_key_note_json, 0, date ( 'Y-m-d H:i:s' ) );
											// end--> call function for save history for delete assessor key notes in history table on 03-03-2016 by Mohit Kumar
										}
									}
								}
							}
					}
				}
				
				// $isKNComplete
				$this->apiResult ["knid"] = $type . '_' . $instance_type_id;
				$this->apiResult ["kncomplete"] = $isKNComplete ? 1 : 0;
				if ($akn_count == 0 || ! $success) {
					$this->db->rollback ();
					$this->apiResult ["message"] = "Atleast one assessor key recommendation is required.\n";
				} else {
					$this->db->commit ();
					$this->apiResult ["message"] = "Saved successfully";
					$this->apiResult ["status"] = 1;
				}
			}
		}
		else {				
			$this->apiResult ["message"] = "Wrong review id or reviewer id\n";
		}
	}
	function addOverviewRecommendationsAction() {
		if (empty ( $_POST ['sn'] ))
			$this->apiResult ["message"] = "Please provide a serial no.\n";
		elseif (empty ( $_POST ['type'] ))
			$this->apiResult ["message"] = "Please provide type\n";
		elseif (empty ( $_POST ['instance_id'] ))
			$this->apiResult ["message"] = "Please provide instance_id\n";
		else {
			$this->apiResult ["status"] = 1;
			$instance_id = $_POST ['instance_id'];
			$this->apiResult ["content"] = reportModel::getRecommendationRow ( $_POST ['type'], $instance_id, $_POST ['sn'] );
		}
	}
	function updateNetworkReportAction() {
		if (! (in_array ( "view_all_assessments", $this->user ['capabilities'] )))
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		else if (empty ( $_POST ['report_id'] ))
			$this->apiResult ["message"] = "Report Id cannot be empty\n";
		else if (empty ( $_POST ['report_name'] ) || trim ( $_POST ['report_name'] ) == '')
			$this->apiResult ["message"] = "Report name cannot be empty\n";
		else if (empty ( $_POST ['network_report_id'] ) || trim ( $_POST ['network_report_id'] ) == '')
			$this->apiResult ["message"] = "Network Report Id cannot be empty\n";
		else {
			$customreportModel = new customreportModel ();
			$report_id = $_POST ['report_id'];
			$report_name = $_POST ['report_name'];
			$network_report_id = $_POST ['network_report_id'];
			$review_experience = $_POST ['review_experience'];
			$review_experience = implode ( '~', $review_experience );
			// print_r($review_experience);
			// $networkReportId = $customreportModel->saveNetworkReport($report_id, $report_name, $filter_id, $review_experience,$include_self_review,$is_validated);
			if (! $customreportModel->updateNetworkReport ( $review_experience, $network_report_id )) {
				$this->apiResult ["message"] = "Error in updating report.";
				return;
			}
			
			$this->apiResult ["message"] = "Network report updated successfully.";
			$this->apiResult ["status"] = 1;
			$this->apiResult ["network_report_id"] = $network_report_id;
		}
	}
	function saveTeacherOverviewRecommendationsAction() {
		if (! (in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] )))
			$this->apiResult ["message"] = "You are not authorized to perform this action\n";
		else if (empty ( $_POST ['group_assessment_id'] ))
			$this->apiResult ["message"] = "Group assessment id cannot be empty\n";
		else if (empty ( $_POST ['diagnostic_id'] ))
			$this->apiResult ["message"] = "Diagnostic id cannot be empty\n";
		else if (empty ( $_POST ['recommendations_kpa'] ) && empty ( $_POST ['recommendations_kq'] ) && empty ( $_POST ['recommendations_cq'] ) && empty ( $_POST ['recommendations_js'] ))
			$this->apiResult ["message"] = "Recommendations cannot be empty\n";
		else {
			$group_assessment_id = $_POST ['group_assessment_id'];
			$diagnostic_id = $_POST ['diagnostic_id'];
                        $dept_id = isset($_POST['dept_id'])?$_POST['dept_id']:Null;
			$reportModel = new reportModel ();
			// if kpa is not empty, get recommendatios for each kpa
			// print_r($_POST['recommendations_kpa']);
			$this->db->start_transaction ();
			$failed = 0;
			if (! empty ( $_POST ['recommendations_kpa'] )) {
				$kpas = $_POST ['recommendations_kpa'];
				foreach ( $kpas as $k => $kpa ) {
					$kpa_instance_id = $k;
					$kpa_recommendations = implode ( '~', $_POST ['recommendations_kpa'] [$kpa_instance_id] );
					if ($reportModel->isExistingRecommendation ( $group_assessment_id, $diagnostic_id, 'kpa', $kpa_instance_id, $dept_id ) != 0) {
						! $reportModel->updateTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'kpa', $kpa_instance_id, $kpa_recommendations, $dept_id ) ? $this->db->rollback () && $failed = 1 : '';
					} elseif (! $reportModel->saveTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'kpa', $kpa_instance_id, $kpa_recommendations, $dept_id ))
						$this->db->rollback () && $failed = 2;
				}
			}
			if (! empty ( $_POST ['recommendations_kq'] )) {
				$kqs = $_POST ['recommendations_kq'];
				foreach ( $kqs as $k => $kq ) {
					$kq_instance_id = $k;
					$kq_recommendations = implode ( '~', $_POST ['recommendations_kq'] [$kq_instance_id] );
					if ($reportModel->isExistingRecommendation ( $group_assessment_id, $diagnostic_id, 'key_question', $kq_instance_id, $dept_id ) != 0) {
						! $reportModel->updateTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'key_question', $kq_instance_id, $kq_recommendations, $dept_id ) ? $this->db->rollback () && $failed = 1 : '';
					} elseif (! $reportModel->saveTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'key_question', $kq_instance_id, $kq_recommendations, $dept_id ))
						$this->db->rollback () && $failed = 2;
				}
			}
			if (! empty ( $_POST ['recommendations_cq'] )) {
				$cqs = $_POST ['recommendations_cq'];
				foreach ( $cqs as $k => $cq ) {
					$cq_instance_id = $k;
					$cq_recommendations = implode ( '~', $_POST ['recommendations_cq'] [$cq_instance_id] );
					if ($reportModel->isExistingRecommendation ( $group_assessment_id, $diagnostic_id, 'core_question', $cq_instance_id, $dept_id ) != 0) {
						! $reportModel->updateTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'core_question', $cq_instance_id, $cq_recommendations, $dept_id ) ? $this->db->rollback () && $failed = 1 : '';
					} elseif (! $reportModel->saveTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'core_question', $cq_instance_id, $cq_recommendations, $dept_id ))
						$this->db->rollback () && $failed = 2;
				}
			}
			if (! empty ( $_POST ['recommendations_js'] )) {
				$js = $_POST ['recommendations_js'];
				foreach ( $js as $k => $j ) {
					$js_instance_id = $k;
					$js_recommendations = implode ( '~', $_POST ['recommendations_js'] [$js_instance_id] );
					if ($reportModel->isExistingRecommendation ( $group_assessment_id, $diagnostic_id, 'judgement_statement', $js_instance_id, $dept_id ) != 0) {
						! $reportModel->updateTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'judgement_statement', $js_instance_id, $js_recommendations, $dept_id ) ? $this->db->rollback () && $failed = 1 : '';
					} elseif (! $reportModel->saveTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'judgement_statement', $js_instance_id, $js_recommendations, $dept_id ))
						$this->db->rollback () && $failed = 2;
				}
			}
			
			if ($failed == 0) {
				$this->db->commit ();
				$this->apiResult ["status"] = 1;
				$this->apiResult ["message"] = "Teacher Overview Recommendations saved successfully";
			} else
				$this->apiResult ["message"] = "Teacher Overview Recommendations could not be saved";
		}
	}
	function cloneDiagnosticAction(){
		if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
				$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		elseif (!in_array("manage_diagnostic", $this->user['capabilities'])) {
				$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			}
		elseif (empty($_POST['diagnosticId'])||empty($_POST['diagnosticName'])) {
				$this->apiResult ["message"] = "Id and name cannot be empty.\n";
			}
                elseif (empty($_POST['langId'])) {
				$this->apiResult ["message"] = "Language cannot be empty.\n";
			}        
		else {
			//check if diagnosticname exists
			$diagnosticName = $_POST['diagnosticName'];
			$diagnosticId = $_POST['diagnosticId'];
                        $langId= $_POST['langId'];
			$diagnosticModel = new diagnosticModel();
			if($diagnosticModel->isDuplicateDiagnosticName($diagnosticName))
			{
				$this->apiResult ["message"] = "Diagnostic Name already exists.\n";
			}
			else{
				$success = $diagnosticModel->cloneDiagnostic($diagnosticId,$diagnosticName,$this->user['user_id'],$langId);
				$success = $success['SUCCESS'];				
				if($success==1)
				{
					$this->apiResult ["message"] = "Diagnostic has been cloned successfully. Please check Manage diagnostics.\n";
					$this->apiResult ["status"] = 1;
				}else{
                                    $this->apiResult ["message"] = "Problem.\n";
					$this->apiResult ["status"] = 0;
                                };
			}
		}	
			
	}
	
	function createProvinceAction(){
		if (! in_array ( "create_network", $this->user ['capabilities'] )) 				
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		else if (empty ( $_POST ['network_id'] ))			
				$this->apiResult ["message"] = "Network Name cannot be empty.\n";
		else if (empty ( $_POST ['province_name'] ))				
			$this->apiResult ["message"] = "Province Name cannot be empty.\n";
		else {
							
			$network_id = trim ( $_POST ['network_id'] );
			$networkModel = new networkModel();	
			$this->db->start_transaction();
			foreach($_POST['province_name'] as $key=>$v){
						if(!empty(trim($v)) && empty($networkModel->getProvinceByName( $v )) && ($pid = $networkModel->createProvince ( $v )) && $networkModel->addProvinceToNetwork($pid,$network_id));
						else{                                                        
                                                        $this->db->rollback ();
							$this->apiResult ["message"] = "Province name is blank or already exists";							
							return;	
							}							
						}
			$this->db->commit();
			$this->apiResult ["status"] = 1;					
			$this->apiResult ["province_id"] = $pid;
			$this->apiResult ["message"] = "Province successfully added";						
		}
		
	}
	
	function getProvinceListAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] )) {
				
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else {
				
			$networkModel = new networkModel ();
				
			$this->apiResult ["provinces"] = $networkModel->getProvinceList ();
				
			$this->apiResult ["status"] = 1;
		}
	}
	function getProvincesInNetworkAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] ))
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			if(empty($_POST['network_id']))
				$this->apiResult ["message"] = "Network Id cannot be empty.\n";
				else {
					$networkModel = new networkModel ();
					$network_id = $_POST['network_id'];
					//echo $network_id;
					$this->apiResult ["message"] = $networkModel->getProvinces($network_id);
					$this->apiResult ["status"] = 1;
				}
	}
         /**
         *  get province by network ids
         * Author Deepak
         * Input  Network Ids
         */
       
        function getProvincesInMultiNetworkAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] ))
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			if(empty($_POST['network_id']))
				$this->apiResult ["message"] = "Network Id cannot be empty.\n";
				else {
					$networkModel = new networkModel ();
                                        $network_ids = $_POST['network_id'];
					$this->apiResult ["message"] = $networkModel->getMultiProvinces($network_ids);
					$this->apiResult ["status"] = 1;
				}
	}
        /**
         *  get schools by provience id
         * Author Deepak
         * Input  Provience ID
         */
       
        function getSchoolsInProvincesAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] ))
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			if(empty($_POST['province_id']))
				$this->apiResult ["message"] = "Province Id cannot be empty.\n";
				else {
					$networkModel = new networkModel ();
				        $provience_ids = $_POST['province_id'];
                                       // print_r($provience_ids);die;
					//echo $network_id;
                                        $schools = $networkModel->getSchools($provience_ids);
                                        if(count($schools)) {
                                            $this->apiResult ["message"] = $networkModel->getSchools($provience_ids);
                                            $this->apiResult ["status"] = 1;
                                        }else 
                                            $this->apiResult ["message"] = "School does not exist for this province";
				}
	}
         /**
         *  get schools by network id
         * Author Deepak
         * Input  Network ID
         */
       
        function getSchoolsInNetworksAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] ))
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			if(empty($_POST['network_id']))
				$this->apiResult ["message"] = "Network Id cannot be empty.\n";
				else {
					$networkModel = new networkModel ();
				        $network_ids = $_POST['network_id'];
                                       // print_r($provience_ids);die;
					//echo $network_id;
					$this->apiResult ["message"] = $networkModel->getSchoolsByNetwork($network_ids);
					$this->apiResult ["status"] = 1;
				}
	}
         /**
         *  get schools by type option (Schools associated to network/non network,all)
         * Author Deepak
         * Input  User Option 
         */
        function getAllSchoolsAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] ))
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			if(isset($_POST['school_related_to']) && empty($_POST['school_related_to']))
				$this->apiResult ["message"] = "Option Type cannot be empty.\n";
				else {
					$resourceModel = new resourceModel ();
				        $option_type = $_POST['school_related_to'];
                                       // print_r($provience_ids);die;
					//echo $network_id;
					$this->apiResult ["message"] = $resourceModel->getSchoolsList($option_type);
					$this->apiResult ["status"] = 1;
				}
	}
         /**
         *  get all users of schools  (Schools associated to network/non network,all)
         * Author Deepak
         * Input  Schools Ids 
         */
        function getSchoolAllUsersAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] ))
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			if(empty($_POST['school_ids']) && empty($_POST['user_role_ids']))
				$this->apiResult ["message"] = "School id/User Role cannot be empty.\n";
				else {
					$resourceModel = new resourceModel ();
				        $school_ids = $_POST['school_ids'];
				        $user_role_ids = $_POST['user_role_ids'];
                                        //print_r($school_ids);die;
					//echo $network_id;
                                        $user_list = $resourceModel->getSchoolUsers($school_ids,$user_role_ids);
                                        if(count($user_list) >= 1) {
                                          $this->apiResult ["status"] = 1;  
                                           $this->apiResult ["message"] = $resourceModel->getSchoolUsers($school_ids,$user_role_ids);
                                        }else {
                                            $this->apiResult ["status"] = 0;
                                            $this->apiResult ["message"] = "User not exist for this schools";
                                        }
					
				}
	}
        
        
        function getAllTeachersStudentsAction() {
		
			if(empty($_POST['school_ids']))
				$this->apiResult ["message"] = "Teacher/Students cannot be empty.\n";
				else {
					$resourceModel = new resourceModel ();
				        $school_ids = $_POST['school_ids'];
                                        $assessment_type = $_POST['assessment_type'];
                                        //print_r($school_ids);die;
					//echo $network_id;
                                        
                                        $user_list = $resourceModel->getTeachersStudentUsers($school_ids,$assessment_type);
                                        if(count($user_list) >= 1) {
                                          $this->apiResult ["status"] = 1;
                                          $this->apiResult ["NF"] = 0;
                                           $this->apiResult ["message"] = $user_list;
                                        }else {
                                            $this->apiResult ["status"] = 1;
                                            $this->apiResult ["NF"] = 1;
                                            $this->apiResult ["message"] = "Teacher/Student not exist for these schools";
                                        }
					
				}
	}
        
        function addPostReviewDiagnosticRowAction(){
            if(empty($_POST['assessment_id']))
                $this->apiResult ["message"] = "Assessment Id cannot be empty.\n";
            elseif(empty($_POST['assessor_id']))
                $this->apiResult ["message"] = "Assessor Id cannot be empty.\n";
            elseif(empty($_POST['sn']))
                $this->apiResult ["message"] = "Serial number cannot be empty.\n";
            else{
                $sn = $_POST['sn'];
                $assessment_id = $_POST['assessment_id'];
                $assessor_id = $_POST['assessor_id'];
                $lang_id=isset($_POST['lang_id'])?$_POST['lang_id']:DEFAULT_LANGUAGE;
                $row = diagnosticModel::getPostReviewDiagnosticHTML($sn,$assessment_id,$assessor_id,1,array(),$lang_id);
                $this->apiResult ["content"] = $row;
		$this->apiResult ["status"] = 1;
            }
        }
        
        function addPlanningDiagnosticRowAction(){
            if(empty($_POST['assessment_id']))
                $this->apiResult ["message"] = "Assessment Id cannot be empty.\n";
            elseif(empty($_POST['sn']))
                $this->apiResult ["message"] = "Serial number cannot be empty.\n";
            else{
                $sn = $_POST['sn'];
                $assessment_id = $_POST['assessment_id'];
                $lang_id=isset($_POST['lang_id'])?$_POST['lang_id']:DEFAULT_LANGUAGE;
                
                $row = actionModel::getAction1HTML($sn,$assessment_id,1,array(),$lang_id);
                $this->apiResult ["content"] = $row;
		$this->apiResult ["status"] = 1;
            }
        }
        
        function getKeyQuestionsAction(){
            if(empty($_POST['sn']))
                $this->apiResult ["message"] = "Serial number cannot be empty.\n";
            elseif(empty($_POST['assessment_id']))
                $this->apiResult ["message"] = "Assessment Id cannot be empty.\n";
            elseif(empty($_POST['assessor_id']))
                $this->apiResult ["message"] = "Assessor Id cannot be empty.\n";
            elseif(empty($_POST['kpa_instance_id']))
                $this->apiResult ["message"] = "KPA instance Id cannot be empty.\n";
         
           else{
                $diagnosticModel = new diagnosticModel();
                 $assessment_id = $_POST['assessment_id'];
                $assessor_id = $_POST['assessor_id'];
                $kpa = $_POST['kpa_instance_id'];
                $lang_id = isset($_POST['lang_id'])?$_POST['lang_id']:DEFAULT_LANGUAGE;
                $content = $diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $assessor_id,$kpa,$lang_id);
                $this->apiResult ["content"] = $content;
		$this->apiResult ["status"] = 1;
            }
        }
        
        function getKeyQuestionsPlanningAction(){
            if(empty($_POST['sn']))
                $this->apiResult ["message"] = "Serial number cannot be empty.\n";
            elseif(empty($_POST['assessment_id']))
                $this->apiResult ["message"] = "Assessment Id cannot be empty.\n";
            
            elseif(empty($_POST['kpa_instance_id']))
                $this->apiResult ["message"] = "KPA instance Id cannot be empty.\n";
         
           else{
                                                                                               
                 $actionModel = new actionModel();
                 $assessment_id = $_POST['assessment_id'];
                
                $kpa = $_POST['kpa_instance_id'];
                $lang_id = isset($_POST['lang_id'])?$_POST['lang_id']:DEFAULT_LANGUAGE;
                $content = $actionModel->getKeyQuestionsForAssessment($assessment_id,$kpa,$lang_id);
                $this->apiResult ["content"] = $content;
		$this->apiResult ["status"] = 1;
            }
        }
        
        function getCoreQuestionsPlanningAction(){
            if(empty($_POST['sn']))
                $this->apiResult ["message"] = "Serial number cannot be empty.\n";
            elseif(empty($_POST['assessment_id']))
                $this->apiResult ["message"] = "Assessment Id cannot be empty.\n";
            elseif(empty($_POST['key_question_instance_id']))
                $this->apiResult ["message"] = "KQ instance Id cannot be empty.\n";
           else{
                $actionModel = new actionModel();
                $assessment_id = $_POST['assessment_id'];
                
                $kq = $_POST['key_question_instance_id'];
                $lang_id = isset($_POST['lang_id'])?$_POST['lang_id']:DEFAULT_LANGUAGE;
                $content = $actionModel->getCoreQuestionsForKQAssessment($assessment_id,$kq,$lang_id);
                $this->apiResult ["content"] = $content;
		$this->apiResult ["status"] = 1;
            }      
           
        }
        
        
        function getJSPlanningAction(){
            if(empty($_POST['sn']))
                $this->apiResult ["message"] = "Serial number cannot be empty.\n";
            elseif(empty($_POST['assessment_id']))
                $this->apiResult ["message"] = "Assessment Id cannot be empty.\n";
            elseif(empty($_POST['core_question_instance_id']))
                $this->apiResult ["message"] = "CQ instance Id cannot be empty.\n";
           else{
                $actionModel = new actionModel();
                $assessment_id = $_POST['assessment_id'];
                
                $cq = $_POST['core_question_instance_id'];
                $lang_id = isset($_POST['lang_id'])?$_POST['lang_id']:DEFAULT_LANGUAGE;
                $content = $actionModel->getJSForCQAssessment($assessment_id,$cq,$lang_id);
                $this->apiResult ["content"] = $content;
		$this->apiResult ["status"] = 1;
            }      
           
        }
        
        
         function getrecPlanningAction(){
            if(empty($_POST['sn']))
                $this->apiResult ["message"] = "Serial number cannot be empty.\n";
            elseif(empty($_POST['assessment_id']))
                $this->apiResult ["message"] = "Assessment Id cannot be empty.\n";
            elseif(empty($_POST['judgement_statement_instance_id']))
                $this->apiResult ["message"] = "JS instance Id cannot be empty.\n";
           else{
                $actionModel = new actionModel();
                $assessment_id = $_POST['assessment_id'];
                
                $js = $_POST['judgement_statement_instance_id'];
                $lang_id = isset($_POST['lang_id'])?$_POST['lang_id']:DEFAULT_LANGUAGE;
                $content = $actionModel->getRecforJSAssessment($assessment_id,$js,$lang_id);
                $this->apiResult ["content"] = $content;
		$this->apiResult ["status"] = 1;
            }      
           
        }
        
        
        function getCoreQuestionsAction(){
            if(empty($_POST['sn']))
                $this->apiResult ["message"] = "Serial number cannot be empty.\n";
            elseif(empty($_POST['assessment_id']))
                $this->apiResult ["message"] = "Assessment Id cannot be empty.\n";
            elseif(empty($_POST['assessor_id']))
                $this->apiResult ["message"] = "Assessor Id cannot be empty.\n";
            elseif(empty($_POST['key_question_instance_id']))
                $this->apiResult ["message"] = "KQ instance Id cannot be empty.\n";
           else{
                $diagnosticModel = new diagnosticModel();
                $assessment_id = $_POST['assessment_id'];
                $assessor_id = $_POST['assessor_id'];
                $kq = $_POST['key_question_instance_id'];
                $lang_id = isset($_POST['lang_id'])?$_POST['lang_id']:DEFAULT_LANGUAGE;
                $content = $diagnosticModel->getCoreQuestionsForKQAssessment($assessment_id, $assessor_id,$kq,$lang_id);
                $this->apiResult ["content"] = $content;
		$this->apiResult ["status"] = 1;
            }      
           
        }
         /*
             * 
             */
        function createResourceDirectoryAction() {
            
                 $province = empty ( $_POST ['province'] ) ? 0 : $_POST ['province'];
                $school = empty ( $_POST ['school'] ) ? 0 : $_POST ['school'];
                $network = empty ( $_POST ['network'] ) ? 0 : $_POST ['network'];
                $rec_user = empty ( $_POST ['rec_user'] ) ? 0 : $_POST ['rec_user'];
                $network_option = empty ( $_POST ['school_related_to'] ) ? 0 : $_POST ['school_related_to'];
                $user_roles = empty ( $_POST ['roles'] ) ? array() : $_POST ['roles'];
                $resource_type = isset ( $_POST ['resource_type'] ) ?  1 : 0;
                $is_province = 0;
                $resourceModel = new resourceModel();
               // print_r($_POST);
                if(!empty($network)) {
                    $provinceInNetwork = $resourceModel->getProvinceInNetwork($network);
                    if(!empty($provinceInNetwork['num_province'])) {
                        $is_province = 1;
                    }
                }
                if (!in_array("upload_resources", $this->user ['capabilities']))
                    $this->apiResult ["message"] = "You are not authorized to perform this task.\n";
                else if (empty(trim($_POST ['new_dir_name'])))
                    $this->apiResult ["message"] = "Folder Name cannot be empty.\n";
                else if (empty($_POST ['directory']))
                     $this->apiResult ["message"] = "Select a Folder.\n";
                else if (  $resource_type == 1 && $network_option == 1 && empty($network) && $resource_type ) {
                     $this->apiResult ["message"] = "Networks cannot be empty\n";
		}else if (  $resource_type == 1 && $network_option == 1 && !empty($is_province) && empty($province)  ) {
			$this->apiResult ["message"] = "Province cannot be empty\n";
		}else if ($resource_type == 1  && empty($school) && $resource_type) {
			$this->apiResult ["message"] = "Schools cannot be empty\n";
		}else if (empty ( $_POST ['roles'] ) && $resource_type == 1 ) {
			$this->apiResult ["message"] = "User Role cannot be empty\n";
		}else if ($resource_type == 1  && empty($rec_user) && $resource_type) {
			$this->apiResult ["message"] = "Users cannot be empty\n";
                }else {
                    $res_dir_name = trim($_POST ['new_dir_name']);
                    $dir_tags = isset($_POST ['dir_tags'])?trim($_POST ['dir_tags']):'';
                    $directory_id = trim($_POST ['directory']);
                    $resourceModel = new resourceModel();
                    $roles_string = '';
                    if(count($user_roles)>= 1) {
                        $roles_string = implode ( ',', $_POST ['roles'] );
                        if(empty($roles_string)) {
                            $roles_string .= "1,2,8";
                        }else {
                            $roles_string .= ",1,2,8";
                        }
                    }
                    if ( ! empty ( $_POST ['roles'] ) && $_POST ['roles'] [0] == 8 ) {
                            $roleId = $_POST ['roles'] [0];
                    } else {
                            $roleId = empty ( $_POST ['role_id'] ) ? 0 : $_POST ['role_id'];
                    }
                    if ($resourceModel->checkDirectoryExistance($res_dir_name, $directory_id)) 
                        $this->apiResult ["message"] = "Folder already exist.\n";
                    else {
                        //$this->db->start_transaction ();
                         $addDirId = $resourceModel->addNewResourceDirectory($res_dir_name, $directory_id,$roles_string,$resource_type,$network_option,$dir_tags);
                      //echo $resource_type;
                        if($addDirId > 0 && $resource_type ){
                            $res_network_result = $resourceModel->createFolderUsers('',$province,$school,$rec_user,$network,'',$addDirId,'','','','', $user_roles);
                                                   // createResourceUsers($resresult,$province,$school,$rec_user,$network,$from='',$resource_id='' ,$network_option='')
                        } 
                        if($addDirId) {
                            $this->apiResult ["status"] = 1;
                            $this->apiResult ["Resource_Directory_Id "] = $addDirId;
                            $this->apiResult ["message"] = "Folder successfully added";
                        }else {
                            
                            $this->apiResult ["status"] = 0;
                            $this->apiResult ["message"] = "Something went wronge" ;
                        }
                    }
        }
    }


    
    /*
     * function to edit resource folder(directory)
     */
       function editResourceDirectoryAction() {

            if (!in_array("upload_resources", $this->user ['capabilities']))
                $this->apiResult ["message"] = "You are not authorized to perform this task.\n";
            else if (empty(trim($_POST ['new_dir_name'])))
                $this->apiResult ["message"] = "Folder Name cannot be empty.\n";
            else {
                $res_dir_name = trim($_POST ['new_dir_name']);
                $directory_id = trim($_POST ['directory_id']);
                $resourceModel = new resourceModel();
                if($resourceModel->editResourceDirectory($directory_id,$res_dir_name)) {
                    $this->apiResult ["status"] = 1;
                    $this->apiResult ["message"] = "Folder successfully added";
                }else {
                    
                    $this->apiResult ["status"] = 0;
                    $this->apiResult ["message"] = "Something went wronge" ;
                }
            }
        }   

      
    function getDownloadSampleStudentProfileAction() {
        $male_female=array("Male","Female");
        $yes_no=array("Yes","No");
        $educations=array("Below 8th","8th-10th","SSC","HSC","Graduate","Post Graduate","ITI","Diploma","Others");
        $leaving_reason=array("No interest in studies","No family support","Financial constraints","Failed multiple times","Others");
        require_once(ROOT."library/PHPExcel/Classes/PHPExcel.php");
        $objPHPExcel = new PHPExcel();
                function transpose($value) {
                        return array($value);
                    }
                        $objPHPExcel->getProperties()
                        ->setCreator("PHPOffice")
                        ->setLastModifiedBy("PHPOffice")
                        ->setTitle("PHPExcel Test Document")
                        ->setSubject("PHPExcel Test Document")
                        ->setDescription("Test document for PHPExcel, generated using PHP classes.")
                        ->setKeywords("Office PHPExcel php")
                        ->setCategory("Test result file");
                $clientModel = new clientModel();       
                $cityList = $clientModel->getStateWiseCityList();        
                $newSheet = $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex(1);
                $newSheet->setTitle("optionSheet");
                // Set data for dropdowns
                $continentColumn = 'A';
                $column="B";
                foreach($cityList as $key => $data) {
                    //$continent = $data['state_name'];
                    $continent =str_replace(' ', '', $data['state_name']);
                    $countries = explode(",",$data['city']);
                    $countryCount = count($countries);

                    // Transpose $countries from a row to a column array
                    $countries = array_map('transpose', $countries);
                    $objPHPExcel->getActiveSheet(1)
                        ->fromArray($countries, null, $column . '1');
                    $objPHPExcel->addNamedRange(
                        new PHPExcel_NamedRange(
                            $continent, 
                            $objPHPExcel->getActiveSheet(1), $column . '1:' . $column . $countryCount
                        )
                    );


                    $objPHPExcel->getActiveSheet(1)
                        ->setCellValue($continentColumn . ($key+1), $continent);

                    ++$column;
                }
                $objPHPExcel->addNamedRange(
                    new PHPExcel_NamedRange(
                        'State', 
                        $objPHPExcel->getActiveSheet(1), $continentColumn . '1:' . $continentColumn . ($key+1)
                    )
                );
                
                $male_female_column=$column;
                $male_female_column++;
                foreach($male_female as $key=>$value){
                    
                    $objPHPExcel->getActiveSheet(1)
                        ->setCellValue($male_female_column . ($key+1), $value);
                    
                }
                
                $objPHPExcel->addNamedRange(
                    new PHPExcel_NamedRange(
                        'gender', 
                        $objPHPExcel->getActiveSheet(1), $male_female_column . '1:' . $male_female_column . ($key+1)
                    )
                );
                
                $education=$male_female_column;
                $education++;
                foreach($educations as $key=>$value){
                    
                    $objPHPExcel->getActiveSheet(1)
                        ->setCellValue($education . ($key+1), $value);
                    
                }
                
                $objPHPExcel->addNamedRange(
                    new PHPExcel_NamedRange(
                        'education', 
                        $objPHPExcel->getActiveSheet(1), $education . '1:' . $education . ($key+1)
                    )
                );
                
                $yesno=$education;
                $yesno++;
                foreach($yes_no as $key=>$value){
                    
                    $objPHPExcel->getActiveSheet(1)
                        ->setCellValue($yesno . ($key+1), $value);
                    
                }
                
                $objPHPExcel->addNamedRange(
                    new PHPExcel_NamedRange(
                        'optionyesno', 
                        $objPHPExcel->getActiveSheet(1), $yesno . '1:' . $yesno . ($key+1)
                    )
                );
                
                $l_reason=$yesno;
                $l_reason++;
                foreach($leaving_reason as $key=>$value){
                    
                    $objPHPExcel->getActiveSheet(1)
                        ->setCellValue($l_reason . ($key+1), $value);
                    
                }
                
                $objPHPExcel->addNamedRange(
                    new PHPExcel_NamedRange(
                        'leavereason', 
                        $objPHPExcel->getActiveSheet(1), $l_reason . '1:' . $l_reason . ($key+1)
                    )
                );
                
                $objPHPExcel->setActiveSheetIndex(0);
                $columns=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
                $fields=array("Site",
                              "State",
                              "City",
                              "Student-UID",
                              "Name",
                              "Email",
                              "DOB (DD/MM/YYYY)",
                    "Gender",
                    "Address",
                    "Contact Number 1",
                    "Contact Number 2",
                    "Current Education",
                    "Vocational Course (If any)",
                    "Dropped out from school",
                    "Reason for dropping out",
                    "Working right now?",
                    "Sector of employment",
                    "Position",
                    "Current Salary CTC (Monthly)",
                    "Working since (DD/MM/YYYY)",
                    "Any previous work experience?",
                    "Sector of employment",
                    "Position",
                    "Last Drawn Salary CTC (Monthly)",
                    "Working since (DD/MM/YYYY)",
                    "Worked till (DD/MM/YYYY)",
                    "Mother's education",
                    "Mother's Position",
                    "Mother's sector of employment",
                    "Mother's Monthly Income CTC",
                    "Father's education",
                    "Father's Position",
                    "Father's sector of employment",
                    "Father's Monthly Income CTC",
                    "Sibling 1 education",
                    "Sibling 1 Position",
                    "Sibling 1 sector of employment",
                    "Sibling 1 Monthly Income CTC",
                    "Sibling 2 education",
                    "Sibling 2 Position",
                    "Sibling 2 sector of employment",
                    "Sibling 2 Monthly Income CTC",
                    "Others- Sector of employment",
                    "Others-Monthly Income CTC",
                            );
                $assessmentModel = new assessmentModel ();
                $diagnosticModel=new diagnosticModel();
		$teacherAssessment=$diagnosticModel->getGroupAssessmentByGAId($_POST['gaid']);
                // Set selection cells
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A1", "Batch Code");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B1", $teacherAssessment['client_name']);
                $row=2;
                foreach($fields as $key=>$val){
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("".$columns[$key]."".$row."", $val);
                }
                
                $row++;
                	
	        $assessments=$assessmentModel->getTeachersInTeacherAssessment($_POST['gaid']);
                
                if (count ( $assessments )) {
				
				
				foreach ( $assessments as $a ) {
                                        foreach($fields as $key=>$val){
                                        if($val=="Name"){
                                           $value=$a['name']; 
                                        }else if($val=="Email"){
                                           $value=$a['email']; 
                                        }else{
                                           $value=""; 
                                        }    
                                        $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("".$columns[$key]."".$row."", $value);
                                        
                                        
                                        if($val=="City"){
                                        $objPHPExcel->getActiveSheet(0)->setCellValue("".$columns[$key]."".$row."", '=' . $column . 1);
                                         // Set linked validators for state
                                        $this->setValidator($objPHPExcel,"".$columns[($key-1)]."".$row."", 'State', 'value');
                
                                       // Set linked validators for city
                                        $this->setValidator($objPHPExcel,"".$columns[$key]."".$row."", 'INDIRECT($'.$columns[$key-1].'$'.$row.')', 'value');
                                        
                                       
                                        }
                                        
                                        if($val=="Gender"){
                                        
                                        $this->setValidator($objPHPExcel,"".$columns[$key]."".$row."", 'gender', 'value');
                                        }
                                        
                                        if($val=="Current Education" || $val=="Mother's education" || $val=="Father's education" || $val=="Sibling 1 education" || $val=="Sibling 2 education"){
                                        
                                        $this->setValidator($objPHPExcel,"".$columns[$key]."".$row."", 'education', 'value');
                                        }
                                        
                                        if($val=="Dropped out from school" || $val=="Working right now?" || $val=="Any previous work experience?" || $val=="Any previous work experience?"){
                                        
                                        $this->setValidator($objPHPExcel,"".$columns[$key]."".$row."", 'optionyesno', 'value');
                                        }
                                        
                                        if($val=="Reason for dropping out"){
                                        
                                        $this->setValidator($objPHPExcel,"".$columns[$key]."".$row."", 'leavereason', 'value');
                                        }
                                       // $objPHPExcel->getActiveSheet()->getColumnDimension("".$columns[$key]."".$row."")->setWidth(150);
                                        $objPHPExcel->getActiveSheet()->getColumnDimension("".$columns[$key]."")->setWidth(15);
                                        
                                        }
                                        $row++;
					//print_r($a);
					//$this->apiResult ["content"] .= $assessmentListRowHelper->printBodyRow ( $a );
				}
				
				
		}
                $objPHPExcel->getActiveSheet()->mergeCells('B1:H1');
                //$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(40);
                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(-1);

                $objPHPExcel->getActiveSheet()->getStyle('A2:AR2')->getAlignment()->setWrapText(true); 
                $objPHPExcel->getActiveSheet()->getStyle('A2:K2')->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'C5E5A1')
                        )
                    )
                );
                
                $objPHPExcel->getActiveSheet()->getStyle('L2:AR2')->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'E9CC89')
                        )
                    )
                );
                //die();
                //$objPHPExcel->getActiveSheet(0)->setCellValue('B7', '=' . $column . 1);
                
                // Set linked validators for state
                //$this->setValidator($objPHPExcel,'B6', 'State', 'value');
                
                // Set linked validators for city
                //$this->setValidator($objPHPExcel,'B7', 'INDIRECT($B$6)', 'value');
                
                $BStyle = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
                $objPHPExcel->getActiveSheet()->getStyle('A1:AR'.($row-1).'')->applyFromArray($BStyle);
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('uploads/sample_csv/sample_student_profile.xlsx');
                $this->apiResult ["message"] = "File is created.\n";
                $this->apiResult ["site_url"] = SITEURL;
                $this->apiResult ["status"] = 1;      
    }
    /*
         * function to download sample to upload school assessment 
         */
        function getDownloadSampleSchoolAssessmentAction() {

                /** Error reporting */
                
                require_once(ROOT."library/PHPExcel/Classes/PHPExcel.php");
                $clientModel = new clientModel();
                $aqsDataModel = new aqsDataModel();
                $boardList = $aqsDataModel->getBoardList(INDIAN_SCHOOL_BORAD);
                $schoolTypeList = $aqsDataModel->getSchoolTypeList();
                $schoolClassList = $aqsDataModel->getSchoolClassList();
                $schoolLanguageList = $aqsDataModel->getPreferredLanguages();
                $schoolFeeList = $aqsDataModel->getAnnualFee();
               // print_r($schoolClassList);die;
               // $states = $clientModel->getStateList();
                $cityList = $clientModel->getStateWiseCityList();
                $strnths=array("50-250","251-500","501-1000","1001-1500","1501-2000","2001-3000","3001+");
                $continentColumn = 'A';
                $column = 'B';
                $objPHPExcel = new PHPExcel();
                function transpose($value) {
                        return array($value);
                    }
                
                $objPHPExcel->getProperties()
                        ->setCreator("PHPOffice")
                        ->setLastModifiedBy("PHPOffice")
                        ->setTitle("PHPExcel Test Document")
                        ->setSubject("PHPExcel Test Document")
                        ->setDescription("Test document for PHPExcel, generated using PHP classes.")
                        ->setKeywords("Office PHPExcel php")
                        ->setCategory("Test result file");

                $fields = array('school_registration_num' => 'fhfhd', 'school_name' => 'Dyanand public school', 'school_address' => 'b-32,sec-50', 'country' => 'india', 'state' => 'Delhi', 'city' => 'delhi', 'principal_name' => 'deepak', 'email_id' => 'deepak@gmail.com',
                    'phone_num' => 9250028090,'school_co-ordinator_name'=>"Ramesh","school_co-ordinator_phone"=>9250028090,"school_co-ordinator_email"=>'cordin@gmail.com',
                    "school_website"=>'school.com',"school_email"=>"school@school.com",
                    "school_board"=>'CBSE','school_type'=>'Private(option:-Private,Govt,Aided,Public Private Partnership(PPP),Partly Private-Partly Aided)you can define more than one by , seprate dvalues )','is_school_minority'=>'2(options-:yes=1,no=2)','school_region_id'=>'1 (options-:city=1,town=2,village=3)',
                    'it_support'=>'2(options-:Internet availability=2,Projector=1)you can define more than one by , seprate dvalues ','no_of_gates'=>'1 (options-:1,2,3,4,5,6)','no_of_buildings'=>'2(options-:1,2,3,4,5)','distance_main_building'=>'50(options-:25, 50, 100, 500, 1000, 1000+) in meters','classes_from'=>'Nursery','classes_to'=>'x','student_strength'=>'500-1000','num_class_rooms'=>40,'medium_instruction'=>'hindi','student_type_id'=>'3 (options-:male=1,female=2,co-ed=3)',
                    'annual_fee'=>'6000-12000','accomodation_arrangement_for_adhyayan'=>'2 (options-:By Adhyayan= 2, By School = 1)','nearest_airport_name'=>'abc','airport_distance_from_school'=>40,'nearest_railways_station'=>'ddddd','station_distance_from_school'=>40,'travel_arrangement_for_adhyayan'=>'2(options-:By Adhyayan= 2, By School = 1)',
                    'nearest_hotel_name'=>'taj','school_hotel_distance'=>30,'hotel_railstation_distance'=>40,'hotel_airport_distance'=>40,'school_accountant_name'=>'ram',
                    'school_accountant_phone_num'=>9250028090,'school_accountant_email'=>'accountant@gmail.com','billing_name'=>'ram','billing_address'=>'b-52,noida,sec-53',
                    'is_school_recognised'=>'2 (options-:yes =1,no=2)','school_aqs_pref_start_date'=>'2017/06/06','school_aqs_pref_end_date'=>'2017/06/06','is_school_gst_registered'=>'2 (options-:yes =1,no=2)','school_gst_num'=>'dshjdgf678709'
                    );

                $newSheet = $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex(1);
                $newSheet->setTitle("optionSheet");
                // Set data for dropdowns
                foreach($cityList as $key => $data) {
                    $continent = $data['state_name'];
                    $countries = explode(",",$data['city']);
                    $countryCount = count($countries);

                    // Transpose $countries from a row to a column array
                    $countries = array_map('transpose', $countries);
                    $objPHPExcel->getActiveSheet(1)
                        ->fromArray($countries, null, $column . '1');
                    $objPHPExcel->addNamedRange(
                        new PHPExcel_NamedRange(
                            $continent, 
                            $objPHPExcel->getActiveSheet(1), $column . '1:' . $column . $countryCount
                        )
                    );


                    $objPHPExcel->getActiveSheet(1)
                        ->setCellValue($continentColumn . ($key+1), $continent);

                    ++$column;
                }
                $objPHPExcel->addNamedRange(
                    new PHPExcel_NamedRange(
                        'State', 
                        $objPHPExcel->getActiveSheet(1), $continentColumn . '1:' . $continentColumn . ($key+1)
                    )
                );
                
                //create dropdown for school board
                $boardColumn = $column;
                $boardColumn++;
                $this->createDropDown($objPHPExcel, $boardColumn, $boardList,'Board','board');
                
                //drop down for school type
               /* $SchoolTypeColumn = $boardColumn;
                $SchoolTypeColumn++;
                $this->createDropDown($objPHPExcel, $SchoolTypeColumn, $schoolTypeList,'School_Type','school_type');*/
                
                //create dropdown for classes from
                $SchoolClassesColumn = $boardColumn;
                $SchoolClassesColumn++;
                $this->createDropDown($objPHPExcel, $SchoolClassesColumn, $schoolClassList,'School_Classes','class_name');
                
                //create dropdown for classes to
                $this->createDropDown($objPHPExcel, $SchoolClassesColumn, $schoolClassList,'School_Classes_To','class_name');
                
                //create dropdown for student strength
                $studentStrength = $SchoolClassesColumn;
                $studentStrength++;
                //$this->createDropDown($objPHPExcel, $studentStrength, $schoolClassList,'School_Classes_To','class_name');
                foreach($strnths as $key=>$value){
                    
                    $objPHPExcel->getActiveSheet(1)
                        ->setCellValue($studentStrength . ($key+1), $value);
                    
                }
                $objPHPExcel->addNamedRange(
                    new PHPExcel_NamedRange(
                        'School_Strength', 
                        $objPHPExcel->getActiveSheet(1), $studentStrength . '1:' . $studentStrength . ($key+1)
                    )
                );
                //create drop down for language
                $studentLanguage = $studentStrength;
                $studentLanguage++;
                $this->createDropDown($objPHPExcel, $studentLanguage, $schoolLanguageList,'School_Language','lang_name');
                
                //create dropdown for annual fee
                $schoolFee = $studentLanguage;
                $schoolFee++;
                $this->createDropDown($objPHPExcel, $schoolFee, $schoolFeeList,'School_Fees','fee');
                $objPHPExcel->setActiveSheetIndex(0);
                // Set selection cells
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A1", "Details");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B1", "Sample School1");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C1", "School2");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("D1", "School3");
                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("E1", "School4");
                $objPHPExcel->getActiveSheet(0)->setCellValue('B7', '=' . $column . 1);
                $i=2;
                foreach ($fields as $key => $val) {
                     $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A$i", $key);

                        $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B$i", $val);
                    $i++;
                }
                
                // Set linked validators for state
                $this->setValidator($objPHPExcel,'B6', 'State', 'value');
                
                // Set linked validators for city
                $this->setValidator($objPHPExcel,'B7', 'INDIRECT($B$6)', 'value');
                
                // Set linked validators for school board
                $this->setValidator($objPHPExcel,'B16', 'Board', 'value');
                
                // Set linked validators for school type
               // $this->setValidator($objPHPExcel,'B19', 'School_Type', 'value');
                
                // Set validators for school Classes
                $this->setValidator($objPHPExcel,'B24', 'School_Classes', 'value');
                
                // Set validators for school Classes From
                $this->setValidator($objPHPExcel,'B25', 'School_Classes_To', 'value');
                
                // Set validators for school strength
                $this->setValidator($objPHPExcel,'B26', 'School_Strength', 'value');
                //set validator for school language
                $this->setValidator($objPHPExcel,'B28', 'School_Language', 'value');
                //set validators for school fees
                $this->setValidator($objPHPExcel,'B30', 'School_Fees', 'value');
                
                 $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A55", "Note:You Can Upload Columns From C TO Z Only ");
                  $objPHPExcel->getActiveSheet(0)->getStyle("A55:A55")->getFont()->setBold(true);
                //echo date('H:i:s') , " Write to Excel2007 format" ;
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('uploads/sample_csv/sample.xlsx');
                
                $this->apiResult ["message"] = "File is created.\n";
                $this->apiResult ["site_url"] = SITEURL;
                $this->apiResult ["status"] = 1;

        }
        
        function resourceDownloadHistoryAction() {

                /** Error reporting */
                
                require_once(ROOT."library/PHPExcel/Classes/PHPExcel.php");
                
                $resourceDataModel = new resourceModel();
                
                $resource_id = isset($_POST['resource_id'])?$_POST['resource_id']:'';
                
                if(!empty($resource_id)) {
                    
                        $resourceLogsData = $resourceDataModel->getResourceDownloadData($resource_id);
                        $continentColumn = 'A';
                        $column = 'B';
                        $objPHPExcel = new PHPExcel();
                        function transpose($value) {
                                return array($value);
                            }

                        $objPHPExcel->getProperties()
                                ->setCreator("PHPOffice")
                                ->setLastModifiedBy("PHPOffice")
                                ->setTitle("PHPExcel Test Document")
                                ->setSubject("PHPExcel Test Document")
                                ->setDescription("Test document for PHPExcel, generated using PHP classes.")
                                ->setKeywords("Office PHPExcel php")
                                ->setCategory("Test result file");

                  
                        $newSheet = $objPHPExcel->createSheet();
                        // Set data for dropdowns

                       // $this->createDropDown($objPHPExcel, $schoolFee, $schoolFeeList,'School_Fees','fee');
                        $objPHPExcel->setActiveSheetIndex(0);
                        // Set selection cells
                        $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A1", "Resource Name");
                        $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B1", "User Name");
                        $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C1", "Download Date");
                     
                        $i=2;
                        foreach ($resourceLogsData as $key => $val) {
                             $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("A$i", $val['resource_name']);

                                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("B$i", $val['name']);
                                $objPHPExcel->setActiveSheetIndex(0)->SetCellValue("C$i", $val['log_date']);
                            $i++;
                        }


                        //echo date('H:i:s') , " Write to Excel2007 format" ;
                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                        $objWriter->save('public/resources/resource_log.xlsx');

                        $this->apiResult ["message"] = "success";
                        $this->apiResult ["site_url"] = SITEURL;
                        $this->apiResult ["status"] = 1;
                
                }

        }
        function setValidator($objPHPExcel,$cell,$formula,$for) {
            
            $objValidation = $objPHPExcel->getActiveSheet(0)
                    ->getCell(''.$cell.'')
                    ->getDataValidation();
                $objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST )
                    ->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION )
                    ->setAllowBlank(false)
                    ->setShowInputMessage(true)
                    ->setShowErrorMessage(true)
                    ->setShowDropDown(true)
                    ->setErrorTitle('Input error')
                    ->setError($formula.' is not in the list.')
                    ->setPromptTitle('Pick from the list')
                    ->setPrompt('Please pick a  '.$for.' from the drop-down list.')
                    ->setFormula1('='.$formula.'');
        }
        function createDropDown($objPHPExcel,$cell_no,$data,$formula,$field_name) {
            
            foreach($data as $key=>$value){
                    
                    $objPHPExcel->getActiveSheet(1)
                        ->setCellValue($cell_no . ($key+1), ''.$value[$field_name].'');
                    
                }
                $objPHPExcel->addNamedRange(
                    new PHPExcel_NamedRange(
                        $formula, 
                        $objPHPExcel->getActiveSheet(1), $cell_no . '1:' . $cell_no . ($key+1)
                    )
                );
        }
        //function to create student profile
	function createStudentProfileAction(){
                $diagnosticModel=new diagnosticModel();                                                                                
                $groupAssmt = null;                                                                                
                if (empty ( $_POST ['assessment_id'] )) {
			
		$this->apiResult ["message"] = "Review ID is empty.\n";
		} else if (! $groupAssmt = $diagnosticModel->getGroupAssessmentByAssmntId ( $_POST ['assessment_id'] )) {
			
			$this->apiResult ["message"] = "No student review is bind this this Review ID.\n";
		} else if ($groupAssmt ['report_published'] == 1) {
			
			$this->apiResult ["message"] = "You can't update data after publishing reports\n";
		} else {                                                                        
                        
                        $assessmentModel=new assessmentModel();
                        //$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
			            $assessment_id = $_POST['assessment_id'];
                        $student_data=$assessmentModel->getInternalAssessor($assessment_id);
                        //print_r($student_data);
                        $student_id=$student_data['user_id'];
			
                        $data = $_POST;
                        //echo "<pre>";print_r($data);
                        $studentAssessmentFormAttributes = $assessmentModel->getStudentFormAttributes($student_id,$assessment_id);
                        if(isset($_POST['working_since_month']) && isset($_POST['working_since_year'])) {
                            $data['working_since'] = $_POST['working_since_month']."/".$_POST['working_since_year'];
                        }
                        if(isset($_POST['previous_worked_since_month']) &&  isset($_POST['previous_worked_since_year'])) {
                            $data['previous_worked_since'] = $_POST['previous_worked_since_month']."/".$_POST['previous_worked_since_year'];
                        }
                        if(isset($_POST['previous_worked_till_month']) && isset($_POST['previous_worked_till_year'])) {
                            $data['previous_worked_till'] = $_POST['previous_worked_till_month']."/".$_POST['previous_worked_till_year'];
                        }
                        unset($data['token']);
                        unset($data['isAjaxRequest']);
                       
                        
                        //unset($data['state']);
                       //echo "<pre>"; print_r($_POST);die;
                        $validationStatus = array();
                        $isSubmit=false;
                        
                        if (empty ( $_POST ['50'])) {
				
				$student = $this->userModel->getUserById ( $student_id );
				
				$data ['50'] = $student ['email'];
			}
                        if (!empty ( $_POST ['10']) && !empty($_POST['st_country_code_10'])) {
				
				
				$data ['10'] = "(+".$_POST['st_country_code_10'].")".$_POST ['10'];
			}
                        if (!empty ( $_POST ['11']) && !empty($_POST['st_country_code_11'])) {
				
				
				$data ['11'] = "(+".$_POST['st_country_code_11'].")".$_POST ['11'];
			}
                        
                        if(isset($data['is_submit']) && $data['is_submit'] == '1') {
                            unset($data['is_submit']);
                            $validationStatus = $assessmentModel->studentProfileValidation($data,$student_id,$assessment_id);
                            $isSubmit=true;
                        }
                        if(isset($validationStatus['errors']) && count($validationStatus['errors'])>=1) {
                           //print_r($validationStatus['errors']); 
                           $this->apiResult ["message"] = "Data is either incorrect or blank.\n";
			   $this->apiResult ["errors"] = $validationStatus['errors'];
                        }else {
                            $this->apiResult ["status"] = 1;
                            $this->apiResult ["assessment_id"] = $assessment_id;
                            $this->apiResult ["assessor_id"] = $student_id;
                            //print_r($data);
                            $data = (isset($validationStatus['values']) && count($validationStatus['values'])>=1) ?$validationStatus['values']:$assessmentModel->studentProfileData($data,$student_id,$assessment_id);
                            //print_r($data);
                            if( $assessmentModel->insertStudentProfile(array_filter($data),$student_id,$assessment_id)) {
                                if($isSubmit){
                                $this->apiResult ["message"] = "Student profile submitted successfully";
                                    
                                }else{
                                $this->apiResult ["message"] = "Student profile saved successfully";
                                }
                                
                            }else {
                                $this->apiResult ["message"] = "Something went wrong";
                            }
                        }
                        
                }     //echo "<pre>";print_r($studentAssessmentFormList);
			
                        //$this->set("form_attributes",$studentAssessmentFormAttributes);

	}
        
        public function uploadStudentDetailsAction() {
            ini_set ( 'memory_limit', '50M' );
            //$this->apiResult['error'] = 0; 
            $allowedExt = array (
				"xls",
				"xlsx" 
		);
            
            if (empty ( $_FILES ['aqs_excel_file'] ['name'] )) {
			$this->apiResult ["message"] = "No file uploaded with this request\n";
		} else {
                    $diagnosticModel=new diagnosticModel();
                    $assessmentModel=new assessmentModel();
                    $clientModel = new clientModel;
                    $teacherAssessment=$diagnosticModel->getGroupAssessmentByGAId($_POST['gaid']);
                    $nArr = explode ( ".", $_FILES ['aqs_excel_file'] ['name'] );
		    $ext = strtolower ( array_pop ( $nArr ) );
                    if($teacherAssessment['assessmentAssigned']==0){
                       $this->apiResult ["message"] = "Student review is not submitted yet\n"; 
                    }
                    else if (in_array ( $ext, $allowedExt ) && count ( $nArr ) > 0) {
			        $newName = str_replace ( " ", "_", substr ( implode ( "_", $nArr ), 0, 35 ) ) . "_" . rand ( 1, 9999 ) . "_" . time () . "." . $ext;
                               // die;
				if (@copy ( $_FILES ['aqs_excel_file'] ['tmp_name'], ROOT . "uploads/AQSExcel/" . $newName )) {
                                    
                                                include_once (ROOT . "library/PHPExcel/Classes/PHPExcel/IOFactory.php");
						define ( 'CLI', (PHP_SAPI == 'cli') ? true : false );
						define ( 'EOL', CLI ? PHP_EOL : '<br />' );
						// echo date('H:i:s'), " Load from Excel2007 file", EOL;
						$objReader = PHPExcel_IOFactory::createReader ( 'Excel2007' );
						$objPHPExcel = $objReader->load ( ROOT . "uploads/AQSExcel/" . $newName ); 
                                                //$sheetData = $objPHPExcel->getActiveSheet(0)->rangeToArray('A1:'.max($column_array).'50');
                                                $sheet = $objPHPExcel->getSheet(0); 
                                                $highestRow = $sheet->getHighestRow(); 
                                                $highestColumn = $sheet->getHighestColumn();
                                                $rowData1 = $sheet->rangeToArray('A1:' . $highestColumn .'1',
                                                NULL,
                                                TRUE,
                                                FALSE);
                                                //print_r($rowData1);
                                                $rowData2 = $sheet->rangeToArray('A2:' . $highestColumn .'2',
                                                NULL,
                                                TRUE,
                                                FALSE);
                                                $batch=$rowData1[0][1];
                                                
                                                //print_r($teacherAssessment);
                                                $counall=0;
                                                $validationError=array();
                                                $validationWarning=array();
                                                $array_table_ids=array();
                                                $array_name_email=array();
                                                $studentAssessmentFormAttributes=$assessmentModel->getStudentFormAttributes(0,0);
                                                //print_r($studentAssessmentFormAttributes);
                                                foreach($studentAssessmentFormAttributes as $key=>$val){
                                                //$array_table_ids[$val['field_name']]=$val['field_id'];
                                                $array_table_ids[]=$val['field_id'];       
                                                }
                                                
                                                function trim_value(&$value){ 
                                                            $value = str_replace(" ","",$value); 
                                                }

                                                $stateList = $clientModel->getStateList();
                                                
                                                //$cityList = $clientModel->getStateCityList();
                                                
                                                $stateList = array_map('strtolower',array_combine(array_column($stateList, 'state_id'), array_column($stateList, 'state_name')));
                                                //print_r($stateList);
                                                array_walk($stateList,'trim_value');
                                                //print_r($stateList);
                                                //print_r($array_table_ids);
                                                //$array_table_ids=array(2,47,3,4,5,46,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,45,26,27,28,29,30,31,32,33,34,43,35,36,37,38,39,40,41,42);
                                                $this->db->start_transaction ();
                                                $queryType=0;
                                                $email_array=array();
                                                if($teacherAssessment['client_name']==$batch){
                                                $checkerrorcount=0;    
                                                for ($row = 3; $row <= $highestRow; $row++){ 
                                                //  Read a row of data into an array
                                                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                NULL,
                                                TRUE,
                                                FALSE);
                                                //print_r($rowData);
                                                
                                                $email=$rowData[0][5];
                                                $name=$rowData[0][4];
                                                if(!empty($email)){
                                                   
                                                $data_post=array();    
                                                $student_details=$assessmentModel->getassessmentIdByEmail($_POST['gaid'],$email);
                                                $assessment_id=isset($student_details['assessment_id'])?$student_details['assessment_id']:0;
                                                $student_id=isset($student_details['teacher_id'])?$student_details['teacher_id']:0;
                                                if($assessment_id>0 && $student_id>0){
                                                 $data_post[1]=$batch;
                                                 $cityIds = $stateIds = array();
                                                 foreach($rowData[0] as $id=>$value){
                                                        if($id==5){
                                                                 $data_post[50]=$value;     
                                                        }else{
                                                                 if($id==1){
                                                                     $value=str_replace(' ', '', $value);
                                                                     $stateIds = (in_array(strtolower($value),$stateList)) ? array_keys($stateList, strtolower($value)) : array();
                                                                     
                                                                     if(count($stateIds)>0){
                                                                            $value=$stateIds[0];   
                                                                     }else{
                                                                            $value="";
                                                                     }                           
                                                                 }
                                                                 if($id==2){
                                                                 
                                                                    if(isset($value) && trim($value)!='' &&  count($stateIds) >=1){
                                                                            $cityIds = $clientModel->getCityByName($stateIds[0],strtolower($value));
                                                                            $cityIds = array_map('strtolower',array_column($cityIds, 'city_id'));
                                                                            $value=isset($cityIds[0])?$cityIds[0]:'';
                                                                    }else{
                                                                            $value="";
                                                                    }
                                                                 }
                                                                 
                                                                 if($id==6){
                                                                        if($value!=""){
                                                                             $value=date('d-m-Y',PHPExcel_Shared_Date::ExcelToPHP($value)); 
                                                                        }   
                                                                 }
                                                                 if($id==19 || $id==24 || $id==25){
                                                                        if($value!=""){
                                                                             $value=date('d-m-Y',PHPExcel_Shared_Date::ExcelToPHP($value)); 
                                                                        }   
                                                                 }
                                                                 if($value=="Male" || $value=="Yes")  $value=1;    
                                                                 if($value=="Female" || $value=="No") $value=2;
                                                                 if($id>5){
                                                                 $data_post[$array_table_ids[$id]]=$value;    
                                                                 }else{
                                                                 $data_post[$array_table_ids[$id+1]]=$value;
                                                                 }
                                                        }
                                                   
                                                   
                                                 }   
                                                   //print_r($data_post); 
                                                 $validationStatus = $assessmentModel->studentProfileValidation($data_post,$student_id,$assessment_id);
                                                   
                                                        if(isset($validationStatus['errors']) && count($validationStatus['errors'])>=1) {
                                                        //print_r($validationStatus['errors']);
                                                                $validationError[$row-2]=$validationStatus['errors'];
                                                                $array_name_email[$row-2]['email']=$email;
                                                                $array_name_email[$row-2]['name']=$name;
                                                        }else{
                                                          $data = (isset($validationStatus['values']) && count($validationStatus['values'])>=1) ?$validationStatus['values']:$assessmentModel->studentProfileData($data_post,$student_id,$assessment_id);    
                                                          //print_r($data);
                                                            if(in_array($email,$email_array)){
                                                                $this->db->rollback ();
                                                                $this->apiResult ["message"] = "Email id ".$email." is found in more than one column";
                                                                return;    
                                                            }
                                                            if( $assessmentModel->insertStudentProfile(array_filter($data),$student_id,$assessment_id)) {
                                                                $checkerrorcount++;
                                                                $queryType=1;
                                                             }else{
                                                                $this->db->rollback ();
                                                                $this->apiResult ["message"] = "Upload Fail! Please try again";
                                                                return;
                                                             }
                                                             
                                                            $email_array[]=$email; 
                                                        }
                                                }else{
                                                    $validationError[$row-2][]="Email id is not attached with this Student review"; 
                                                    $array_name_email[$row-2]['email']=$email;
                                                    $array_name_email[$row-2]['name']=$name;
                                                } 
                                                    
                                                    
                                                }else{
                                                    $validationError[$row-2][]="Email id should not be blank";
                                                    $array_name_email[$row-2]['email']=$email;
                                                    $array_name_email[$row-2]['name']=$name;
                                                }
                                                
                                               
                                                $counall++;
                                                }
                                                
                                $message_modal='<div id="myModal" class="modal fade">
                                <div class="modal-dialog " style="width:600px;"><div class="modal-content">
                                <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" style="color:#ffffff;">List of Errors</h4></div>
                                <div class="modal-body">
				<div class="clr"></div>
                                <div class="">
					<div class="ylwRibbonHldr">
					<div class="tabitemsHldr"></div>
					</div>
                                    <div class="subTabWorkspace pad26">
                                        <div class="form-stmnt text-danger">';
                                       if(count($validationError)>0){         
                                       foreach($validationError as $key=>$val){
                                       $name=isset($array_name_email[$key]['name'])?" -".$array_name_email[$key]['name']."":'';
                                       $email=isset($array_name_email[$key]['email'])?" (".$array_name_email[$key]['email'].")":'';
                                       $message_modal.='<ul><span style="font-weight:bold;">Error(s) for Row-'.$key.''.$name.''.$email.'</span>';
                                       foreach($val as $keye=>$vale){
                                       $message_modal.='<br> -'.$vale.'';    
                                       }
                                       $message_modal.='</ul><br>';
                                       }
                                       
                                       }
                                        
                                        $message_modal.='
                                            
                                   </div>
                                    </div>
                                </div>
                                </div></div></div>
                                </div>';  

                                                if($counall>0){
                                                //print_r($validationError);    
                                                $this->db->commit();
                                                $this->apiResult['warnings'] = $validationWarning;
                                                if(count($validationError)>0){
                                                $this->apiResult ["error_msg"]=1;
                                                if($checkerrorcount>0){
                                                $this->apiResult ["message"] = "Few Students Data is not submitted. Click <a href='#' data-toggle='modal' data-target='#myModal'>here</a> to view error list.".$message_modal."";    
                                                }else{
                                                $this->apiResult ["message"] = "Problem in uploading .Click <a href='#' data-toggle='modal' data-target='#myModal'>here</a> to view error list.".$message_modal."";    
                                                }
                                                }else{
                                                $this->apiResult ["error_msg"]=0; 
                                                $this->apiResult ["message"] = 'Uploaded Successfully';
                                                }
                                                $this->apiResult ["status"] = 1;
                                                
                                                
                                                
                                                    
                                                }else{
                                                $this->apiResult ["status"] = 1;
                                                $this->apiResult ["message"] = "Upload Fail! No Data Found in sheet ";
                                                }
                                                
                                                }else{
                                                $this->apiResult ["message"] = "Batch Code is wrong \n";    
                                                }
                                                
                                                
                                                
                                                
                                }
                                }else {
                                $this->apiResult ["status"] = 1;
				$this->apiResult ["message"] = "Invalid file extension. Only " . implode ( ", ", $allowedExt ) . " type files are allowed\n";
			}
                    
                }
                
                $this->apiResult['file_name'] = "File Name-".$_FILES ['aqs_excel_file'] ['name'];
		$this->apiResult['uploaded_date'] = "Upload Date-".date("Y-m-d h:i:s");
		return $this->apiResult;
        }
        /*
         * function to upload AQS data
         */
        public function uploadAQSDetailsAction() {
                $currentUser=isset($this->user ['user_id'])?$this->user['user_id']:NULL;
		ini_set ( 'memory_limit', '50M' );
		// $maxUploadFileSize = 104857600; //in bytes
		// $this->apiResult["error_row"] = array();
		// die(print_r($_FILES['user_excel_file']));
		$allowedExt = array (
				"xls",
				"xlsx" 
		);
		$user_profile_error_data = array ();
		$user_profile_error_data_row = array ();
		if (empty ( $_FILES ['aqs_excel_file'] ['name'] )) {
			$this->apiResult ["message"] = "No file uploaded with this request\n";
		} else {
			$nArr = explode ( ".", $_FILES ['aqs_excel_file'] ['name'] );
			$ext = strtolower ( array_pop ( $nArr ) );
			if (in_array ( $ext, $allowedExt ) && count ( $nArr ) > 0) {
			        $newName = str_replace ( " ", "_", substr ( implode ( "_", $nArr ), 0, 35 ) ) . "_" . rand ( 1, 9999 ) . "_" . time () . "." . $ext;
                               // die;
				if (@copy ( $_FILES ['aqs_excel_file'] ['tmp_name'], ROOT . "uploads/AQSExcel/" . $newName )) {
					$diagnosticModel = new diagnosticModel ();
					//$file_id = $diagnosticModel->addUploadedFile ( $newName, $this->user ['user_id'] );
					
						/**
						 * PHPExcel_IOFactory
						 */
						include_once (ROOT . "library/PHPExcel/Classes/PHPExcel/IOFactory.php");
						define ( 'CLI', (PHP_SAPI == 'cli') ? true : false );
						define ( 'EOL', CLI ? PHP_EOL : '<br />' );
						// echo date('H:i:s'), " Load from Excel2007 file", EOL;
						$objReader = PHPExcel_IOFactory::createReader ( 'Excel2007' );
						$objPHPExcel = $objReader->load ( ROOT . "uploads/AQSExcel/" . $newName );
                                                $clientModel = new clientModel;
                                                $assessmentModel = new assessmentModel();
                                                $aqsModel = new aqsDataModel();
                                                $column_array=array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');                                              
                                                $roles = array(3,6);
                                                $sheetData = $objPHPExcel->getActiveSheet(0)->rangeToArray('A2:'.max($column_array).'50');
                                                //$sheetData = $objPHPExcel->getActiveSheet(0)->fromArray();
                                               // echo "<pre>";print_r($sheetData);die;
                                                $insertDataArr = array();
                                                $ini=2;
                                                $clientId = '';
                                                $aqsRequiredFields = array('school_name','principal_name','principal_email','principal_phone_no','coordinator_name','coordinator_phone_number','coordinator_email','school_address','school_email',
                                                    'board_id','school_type_id','aqs_school_minority','aqs_school_recognised','aqs_school_registration_num','school_region_id','distance_main_building',
                                                    'no_of_gates','no_of_buildings','aqs_distance_main_building','classes_from','classes_to','no_of_students','num_class_rooms',
                                                    'medium_instruction','student_type_id','airport_name','airport_distance','rail_station_name','rail_station_distance','hotel_name',
                                                    'hotel_school_distance','hotel_station_distance','hotel_airport_distance','accountant_name','accountant_phone_no','accountant_email','billing_name',
                                                    'billing_address','annual_fee','aqs_school_registration_num','accomodation_arrangement_for_adhyayan','travel_arrangement_for_adhyayan','school_aqs_pref_start_date','school_aqs_pref_end_date',
                                                    'terms_agree','referrer_id','referrer_text','it_support','aqs_school_gst','aqs_school_gst_num');
                                                 $aqsRequiredFields = array_flip($aqsRequiredFields);
                                                foreach($sheetData as $key =>$data) {
                                                    
                                                    foreach($column_array as $key1=>$val1){
                                                    if(!empty($data[$key1+2])) 
                                                        $insertDataArr[$key1][$data[0]] = $data[$key1+2];
                                                    }
                                                    
                                                }
                                                $stateList = $clientModel->getStateList();
                                                
                                                //$cityList = $clientModel->getStateCityList();
                                                
                                                $stateList = array_map('strtolower',array_combine(array_column($stateList, 'state_id'), array_column($stateList, 'state_name')));
                                                //$cityList = array_map('strtolower',array_combine(array_column($cityList, 'city_id'), array_column($cityList, 'city_name')));
                                                
                                               // $cityList = $clientModel->getCityList();
                                               //echo "<pre>";print_r($insertDataArr);die;
                                                $finalValuesSchools = array();
                                                
                                                $validationWarning = array();
                                                
                                               // $InvalidDataErrors = array();
                                                  $validationError = array();
                                                  $valueIndex = 2;
                                                if(count($insertDataArr)>=1) {
                                                    foreach($insertDataArr as $key=>$data){
                                                        //echo "<pre>";print_r($data);
                                                        $finalValuesSchools = array();
                                                        $finalValuesAQS = array();
                                                        $invalidDataError = '';
                                                        //$invalidDataError = array();
                                                        $validationStatus = 1;
                                                        $valueIndex ++;
                                                        $email_status = 1;
                                                        $cityIds = $stateIds = array();
                                                        //echo $data['city'];
                                                        if(isset($data['is_school_recognised'])  && trim($data['is_school_recognised']) == 1) {
                                                            
                                                            if(isset($data['school_registration_num']) && trim($data['school_registration_num']) !=''){
                                                                $res = $aqsModel->getSchoolByRegistrationNum($data['school_registration_num']);
                                                                //print_r($res);
                                                                if(count($res)>=1) {
                                                                   // $errors[]="Registartion num already exist ";
                                                                    $validationError[] = 'Registration Num already exist for column ----'.$valueIndex;
                                                                    $validationStatus = 0;
                                                                }
                                                                
                                                            }
                                                        }
                                                        if(isset($data['is_school_gst_registered'])  && trim($data['is_school_gst_registered']) == 1) {
                                                            
                                                            if(isset($data['school_gst_num']) && trim($data['school_gst_num']) !=''){
                                                                $res = $aqsModel->getSchoolByGSTNum($data['school_gst_num']);
                                                                //print_r($res);
                                                                if(count($res)>=1) {
                                                                   // $errors[]="Registartion num already exist ";
                                                                    $validationError[] = 'GST Num already exist for column ----'.$valueIndex;
                                                                    $validationStatus = 0;
                                                                }
                                                                
                                                            }
                                                        }
                                                        if(!isset($data['school_name']) || empty(trim($data['school_name']))) {

                                                            $validationError[] = 'School name cannot found for column ----'.$valueIndex;
                                                            $validationStatus = 0;
                                                        }else if (preg_match ( '/^[\.A-Za-z\s]+$/', $data['school_name'] ) != 1) {

                                                           $validationError[] = 'Invalid characters found in school name for column ----'.$valueIndex;
                                                           $validationStatus = 0;
                                                        }if(!isset($data['school_address']) || empty(trim($data['school_address']))) {

                                                           $validationError[] = 'School address cannot found for column ----'.$valueIndex;
                                                           $validationStatus = 0;
                                                        }if(!isset($data['country']) || empty(trim($data['country']))) {

                                                           $validationError[] = 'School country cannot found for column ----'.$valueIndex;
                                                           $validationStatus = 0;
                                                        }else if(strtolower($data['country'])!='india') { 
                                                            $validationError[] = 'School country cannot found for column ! only india is available ----'.$valueIndex;
                                                           $validationStatus = 0;
                                                            
                                                        }                                                        
                                                        if(!isset($data['state']) || empty(trim($data['state']))) {

                                                           $validationError[] = 'School state cannot found for column ----'.$valueIndex;
                                                           $validationStatus = 0;
                                                        }if(!isset($data['city']) || empty(trim($data['city']))) {

                                                           $validationError[] = 'School city cannot found for column ----'.$valueIndex;
                                                           $validationStatus = 0;
                                                        }if(!isset($data['principal_name']) || empty(trim($data['principal_name']))) {

                                                            $validationError[] = 'School principal name cannot found for column ----'.$valueIndex;
                                                            $validationStatus = 0;
                                                        }else if (preg_match ('/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $data['principal_name'] ) != 1) {

                                                           $validationError[] = 'Invalid characters found in principal name for column ----'.$valueIndex;
                                                           $validationStatus = 0;
                                                        }if(!isset($data['email_id']) || empty(trim($data['email_id']))) {

                                                             $validationError[] = 'Principal email cannot found for column ----'.$valueIndex;
                                                            $validationStatus = 0;
                                                        } else if (isset($data['email_id']) && preg_match ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $data['email_id'] ) != 1) {

                                                                 $validationError[] = "Invalid email for column ----".$valueIndex;
                                                               $validationStatus = 0;
                                                        }if (isset($data['phone_num']) && trim($data['phone_num'])!=='' ) {
                                                                if(preg_match ( "/^[1-9][0-9]*$/", $data['phone_num'] ) != 1) {
                                                                    $validationWarning[] = "Invalid phone_num for column ----".$valueIndex;
                                                                }else if(strlen($data['phone_num']) != 10 ) {
                                                                    $validationWarning[] = "Phone Number should be 10 digit for column ----".$valueIndex;
                                                                }
                                                        }if(isset($data['state']) && trim($data['state'])!=''){
                                                            $stateIds = (in_array(strtolower($data['state']),$stateList)) ? array_keys($stateList, strtolower($data['state'])) : array();
                                                            if(isset($data['city']) && trim($data['city'])!='' &&  count($stateIds) >=1){
                                                                $cityIds = $clientModel->getCityByName($stateIds[0],strtolower($data['city']));
                                                                $cityIds = array_map('strtolower',array_column($cityIds, 'city_id'));
                                                            } 
                                                        }                                                        
                                                         if(count($cityIds)< 1) {
                                                             $validationWarning[] = 'City cannot found for column ----'.$valueIndex;
                                                         }
                                                         if(count($stateIds)<1) {
                                                             $validationWarning[] = 'State cannot found for column-- '.$valueIndex;
                                                         }
                                                         //echo count($validationError);
                                                         if($validationStatus == 1) {
                                                                
                                                                $finalValuesSchools['client_institution_id'] = 1;
                                                                if(isset($data['school_name']) && trim($data['school_name'])!='') {
                                                                    $finalValuesSchools['client_name'] =trim($data['school_name'])  ;                                                          
                                                                }
                                                                if(isset($data['principal_name']) && trim($data['principal_name'])!='') {
                                                                    $finalValuesSchools['principal_name'] = trim($data['principal_name']);
                                                                }
                                                                /*if(isset($data['principle_name']) && trim($data['principle_name'])!='') {
                                                                    $finalValuesSchools['principal_name'] = trim($data['principle_name']);
                                                                }*/
                                                                if(isset($data['email_id']) && trim($data['email_id'])!='') {
                                                                    $finalValuesSchools['email'] = trim($data['email_id']);
                                                                }
                                                                if(isset($data['phone_num']) && trim($data['phone_num'])!='') {
                                                                    $finalValuesSchools['principal_phone_no'] = trim($data['phone_num']);
                                                                }
                                                                if(isset($data['school_address']) && trim($data['school_address'])!='') {
                                                                    $finalValuesSchools['addressLine2'] = trim($data['school_address']);
                                                                    $finalValuesSchools['street'] = trim($data['school_address']);
                                                                }
                                                               /* if(isset($data['city']) && $data['city']!='') {
                                                                    $finalValuesSchools['city'] = $data['city'];
                                                                }*/
                                                                if(isset($cityIds) && count($cityIds)>= 1) {
                                                                    $finalValuesSchools['city_id'] = $cityIds[0];
                                                                     $finalValuesSchools['city'] = trim($data['city']);
                                                                }
                                                                if(isset($data['state']) && trim($data['state'])!='') {
                                                                    $finalValuesSchools['state'] = trim($data['state']);
                                                                }
                                                                if(isset($stateIds) && count($stateIds) >=1) {
                                                                    $finalValuesSchools['state_id'] = $stateIds[0];
                                                                }
                                                                if(isset($data['country']) && trim($data['country'])!='') {
                                                                    $finalValuesSchools['country_id'] = DEFAULT_COUNTRY_ID;
                                                                }                                                        
                                                                $finalValuesAQS['school_name'] = isset($data['school_name']) && trim($data['school_name'])!=''?trim($data['school_name']):'';
                                                                $finalValuesAQS['principal_name'] = isset($data['principal_name']) && trim($data['principal_name'])!=''?trim($data['principal_name']):'';
                                                                $finalValuesAQS['principal_email'] = isset($data['email_id']) && trim($data['email_id'])!=''?trim($data['email_id']):'';
                                                                $finalValuesAQS['principal_phone_no'] = isset($data['phone_num']) && trim($data['phone_num'])!=''?trim($data['phone_num']):'';
                                                                $finalValuesAQS['coordinator_name'] = isset($data['school_co-ordinator_name']) && trim($data['school_co-ordinator_name'])!=''?trim($data['school_co-ordinator_name']):'';
                                                                $finalValuesAQS['coordinator_phone_number'] = isset($data['school_co-ordinator_phone']) && trim($data['school_co-ordinator_phone'])!=''?trim($data['school_co-ordinator_phone']):'';
                                                                $finalValuesAQS['coordinator_email'] = isset($data['school_co-ordinator_email']) && trim($data['school_co-ordinator_email'])!=''?trim($data['school_co-ordinator_email']):'';
                                                                $finalValuesAQS['school_website'] = isset($data['school_website']) && trim($data['school_website'])!=''?trim($data['school_website']):'';
                                                                $finalValuesAQS['school_address'] = isset($data['school_address']) && trim($data['school_address'])!=''?trim($data['school_address']):'';
                                                                $finalValuesAQS['school_email'] = isset($data['school_email']) && trim($data['school_email'])!=''?trim($data['school_email']):'';
                                                                $finalValuesAQS['board_id'] = isset($data['school_board']) && trim($data['school_board'])!=''?trim($data['school_board']):'';
                                                                $finalValuesAQS['school_type_id'] = isset($data['school_type']) && trim($data['school_type'])!=''?trim($data['school_type']):'';
                                                                $finalValuesAQS['aqs_school_minority'] = isset($data['is_school_minority']) && trim($data['is_school_minority'])!=''?trim($data['is_school_minority']):'';
                                                                $finalValuesAQS['aqs_school_recognised'] = isset($data['is_school_recognised']) && trim($data['is_school_recognised'])!=''?trim($data['is_school_recognised']):'';
                                                                $finalValuesAQS['aqs_school_registration_num'] = isset($data['school_registration_num']) && trim($data['school_registration_num'])!=''?trim($data['school_registration_num']):'';
                                                                $finalValuesAQS['school_region_id'] = isset($data['school_region_id']) && trim($data['school_region_id'])!=''?trim($data['school_region_id']):'';
                                                                $finalValuesAQS['it_support'] = isset($data['it_support']) && trim($data['it_support'])!=''?trim($data['it_support']):'';
                                                                $finalValuesAQS['distance_main_building'] = isset($data['distance_main_building']) && trim($data['distance_main_building'])!=''?trim($data['distance_main_building']):'';
                                                                $finalValuesAQS['no_of_gates'] = isset($data['no_of_gates']) && trim($data['no_of_gates'])!=''?trim($data['no_of_gates']):'';
                                                                $finalValuesAQS['no_of_buildings'] = isset($data['no_of_buildings']) && trim($data['no_of_buildings'])!=''?trim($data['no_of_buildings']):'';
                                                                $finalValuesAQS['aqs_distance_main_building'] = isset($data['distance_main_building']) && trim($data['distance_main_building'])!=''?trim($data['distance_main_building']):'';
                                                                $finalValuesAQS['classes_from'] = isset($data['classes_from']) && trim($data['classes_from'])!=''?trim($data['classes_from']):'';
                                                                $finalValuesAQS['classes_to'] = isset($data['classes_to']) && trim($data['classes_to'])!=''?trim($data['classes_to']):'';
                                                                $finalValuesAQS['no_of_students'] = isset($data['student_strength']) && trim($data['student_strength'])!=''?trim($data['student_strength']):'';
                                                                $finalValuesAQS['num_class_rooms'] = isset($data['num_class_rooms']) && trim($data['num_class_rooms'])!=''?trim($data['num_class_rooms']):'';
                                                                $finalValuesAQS['medium_instruction'] = isset($data['medium_instruction']) && trim($data['medium_instruction'])!=''?trim($data['medium_instruction']):'';
                                                                $finalValuesAQS['student_type_id'] = isset($data['student_type_id']) && trim($data['student_type_id'])!=''?trim($data['student_type_id']):'';
                                                                $finalValuesAQS['airport_name'] = isset($data['nearest_airport_name']) && trim($data['nearest_airport_name'])!=''?trim($data['nearest_airport_name']):'';
                                                                $finalValuesAQS['airport_distance'] = isset($data['airport_distance_from_school']) && trim($data['airport_distance_from_school'])!=''?trim($data['airport_distance_from_school']):'';
                                                                $finalValuesAQS['rail_station_name'] = isset($data['nearest_railways_station']) && trim($data['nearest_railways_station'])!=''?trim($data['nearest_railways_station']):'';
                                                                $finalValuesAQS['rail_station_distance'] = isset($data['station_distance_from_school']) && trim($data['station_distance_from_school'])!=''?trim($data['station_distance_from_school']):'';
                                                                $finalValuesAQS['hotel_name'] = isset($data['nearest_hotel_name']) && trim($data['nearest_hotel_name'])!=''?trim($data['nearest_hotel_name']):'';
                                                                $finalValuesAQS['hotel_school_distance'] = isset($data['school_hotel_distance']) && trim($data['school_hotel_distance'])!=''?trim($data['school_hotel_distance']):'';
                                                                $finalValuesAQS['hotel_station_distance'] = isset($data['hotel_railstation_distance']) && trim($data['hotel_railstation_distance'])!=''?trim($data['hotel_railstation_distance']):'';
                                                                $finalValuesAQS['hotel_airport_distance'] = isset($data['hotel_airport_distance']) && trim($data['hotel_airport_distance'])!=''?trim($data['hotel_airport_distance']):'';
                                                                $finalValuesAQS['accountant_name'] = isset($data['school_accountant_name']) && trim($data['school_accountant_name'])!=''?trim($data['school_accountant_name']):'';
                                                                $finalValuesAQS['accountant_phone_no'] = isset($data['school_accountant_phone_num']) && trim($data['school_accountant_phone_num'])!=''?trim($data['school_accountant_phone_num']):'';
                                                                $finalValuesAQS['accountant_email'] = isset($data['school_accountant_email']) && trim($data['school_accountant_email'])!=''?trim($data['school_accountant_email']):'';
                                                                $finalValuesAQS['billing_name'] = isset($data['billing_name']) && trim($data['billing_name'])!=''?trim($data['billing_name']):'';
                                                                $finalValuesAQS['billing_address'] = isset($data['billing_address']) && trim($data['billing_address'])!=''?trim($data['billing_address']):'';
                                                                $finalValuesAQS['annual_fee'] = isset($data['annual_fee']) && trim($data['annual_fee'])!=''?trim($data['annual_fee']):'';
                                                                $finalValuesAQS['aqs_school_registration_num'] = isset($data['school_registration_num']) && trim($data['school_registration_num'])!=''?trim($data['school_registration_num']):'';
                                                                $finalValuesAQS['accomodation_arrangement_for_adhyayan'] = isset($data['accomodation_arrangement_for_adhyayan']) && trim($data['accomodation_arrangement_for_adhyayan'])!=''?trim($data['accomodation_arrangement_for_adhyayan']):'';
                                                                $finalValuesAQS['travel_arrangement_for_adhyayan'] = isset($data['travel_arrangement_for_adhyayan']) && trim($data['travel_arrangement_for_adhyayan'])!=''?trim($data['travel_arrangement_for_adhyayan']):'';
                                                                $finalValuesAQS['school_aqs_pref_start_date'] = isset($data['school_aqs_pref_start_date']) && trim($data['school_aqs_pref_start_date'])!=''?trim($data['school_aqs_pref_start_date']):'';
                                                                $finalValuesAQS['school_aqs_pref_end_date'] = isset($data['school_aqs_pref_end_date']) && trim($data['school_aqs_pref_end_date'])!=''?trim($data['school_aqs_pref_end_date']):'';
                                                                $finalValuesAQS['aqs_school_gst'] = isset($data['is_school_gst_registered']) && trim($data['is_school_gst_registered'])!=''?trim($data['is_school_gst_registered']):'';
                                                                $finalValuesAQS['aqs_school_gst_num'] = isset($data['school_gst_num']) && trim($data['school_gst_num'])!=''?trim($data['school_gst_num']):'';
                                                               
                                                               // echo "<pre>";print_r($finalValuesAQS);
                                                                $aqsDataValidation = $aqsModel->aqsUploadValidation($finalValuesAQS,$valueIndex);
                                                                //echo "<pre>";print_r($aqsDataValidation);die;
                                                                //$aqsDataValidation['values']['is_uploaded'] = 1;
                                                                $finalValuesSchools['create_date'] = date("Y-m-d H:i:s");
                                                                if(!$this->userModel->getUserIdByEmail($data['email_id'])) {
                                                                    $clientId = $uid = $roleId = '';
                                                                    $this->db->start_transaction ();
                                                                    $clientId = $clientModel->createClientBulk($finalValuesSchools);

                                                                    if($clientId && $email_status && $data['principal_name'] != '') {
                                                                         $uid = $this->userModel->createUserBatch ( $data['email_id'], $data['email_id'], $data['principal_name'], $clientId,0,date("Y-m-d H:i:s"),$currentUser );
                                                                    }
                                                                    if(isset($uid) && $uid) {
                                                                        $roleId = $this->userModel->addUserRoleBatch ( $uid, $roles );

                                                                    }
                                                                    
                                                                    //calculate profile complete %
                                                                    $noOfComplete=$total=$noOfIncomplete=$completedPerc=0;
                                                                    $noOfIncomplete = 0;
                                                                   
                                                                    $aqsValuesArray = $aqsDataValidation['values'];
                                                                   // echo "<pre>";print_r($aqsValuesArray);
                                                                    foreach ( $aqsRequiredFields as $k => $v ) 

									{
										//echo $k;
										if ( ($k == 'distance_main_building' && isset($aqsValuesArray['no_of_buildings']) && $aqsValuesArray['no_of_buildings'] == 1) || $k == 'contract_file_name' || $k == 'school_website'  || (isset($aqsValuesArray['accomodation_arrangement_for_adhyayan']) && $aqsValuesArray['accomodation_arrangement_for_adhyayan']== 1 && $k == 'hotel_name' || $k == 'hotel_school_distance' || $k == 'hotel_airport_distance' || $k == 'hotel_station_distance') || (isset($aqsValuesArray['travel_arrangement_for_adhyayan']) && $aqsValuesArray['travel_arrangement_for_adhyayan']== 1 && $k == 'airport_name' || $k == 'airport_distance'|| $k == 'rail_station_name'|| $k == 'rail_station_distance') || $k == 'aqs_school_registration_num') 

										{
											
											$noOfComplete ++;
											
											$total ++;
											
											continue;
										}
										
										if (empty ( $aqsValuesArray[$k] )){
											
                                                                                   // echo $k;
                                                                                    $noOfIncomplete ++;}
										
										else											
											$noOfComplete ++;
										
										$total ++;
                                                                                
									}
                                                                        //echo"t". $total;
                                                                        //echo"n". $noOfIncomplete;
                                                                         $completedPerc = round ( (100 * $noOfComplete) / $total, 2 );
                                                                   
                                                                        $aqsDataValidation['values']['percComplete'] = $completedPerc;
                                                                    // $aqsDataValidation['values']['is_uploaded'] = 1;
                                                                     $aqsDataValidation['values']['terms_agree'] = 1;
                                                                     $aqsDataValidation['values']['status'] = 1;
                                                                    // echo "<pre>";print_r($aqsDataValidation['values']);die;
                                                                    //$assessmentModel->createSchoolAssessment ( $_POST ['client_id'], $_POST ['internal_assessor_id'], $_POST ['external_assessor_id'], $_POST ['diagnostic_id'], $_POST ['tier_id'], $_POST ['award_scheme_name'], $externalReviewTeam );
                                                                    $reviewId = $assessmentModel->uploadSchoolReviews($clientId,$uid, DEFAULT_DIAGNOSTIC, DEFAULT_TIER,DEFAULT_AWARD_STANDARD,DEFAULT_AQS_ROUND);
                                                                    if($reviewId)
                                                                        $aqsId = $assessmentModel->uploadReviewsAQS($aqsDataValidation['values'],$reviewId);
                                                                    if($clientId && $uid && $roleId && $reviewId && $aqsId)                                                                         
                                                                        $this->db->commit();
                                                                    else
                                                                        $this->db->rollback();
                                                                    
                                                                }else {
                                                                    $invalidDataError = "Principal already exist for column--".$valueIndex;
                                                                }
                                                         }
                                                         if($invalidDataError!='') {
                                                             $validationError[count($validationError)] = $invalidDataError;
                                                            // $this->apiResult['error'][count($this->apiResult['error'])] = $invalidDataError;
                                                         }
                                                         if(isset($aqsDataValidation['errors']) && count($aqsDataValidation['errors'])>=1) {
                                                              $warningIndex = count($validationWarning);
                                                             foreach( $aqsDataValidation['errors'] as $warning) {
                                                                // echo "k";
                                                                $validationWarning[$warningIndex] = $warning."----".$valueIndex;
                                                                $warningIndex++;
                                                             }
                                                             $aqsDataValidation = array();
                                                            // $this->apiResult['error'][count($this->apiResult['error'])] = $invalidDataError;
                                                         }
                                                    }
                                                    $this->apiResult['error'] = $validationError;
                                                    $this->apiResult['warnings'] = $validationWarning;
                                                    if(!$clientId) {
                                                        
                                                        $this->apiResult ["message"] = 'Failed';
                                                        $this->apiResult ["status"] = 1;
                                                    }else {
                                                        $this->apiResult ["message"] = 'Success';
                                                        $this->apiResult ["status"] = 1;
                                                    }
                                                }else {
                                                    $this->apiResult ["status"] = 1;
                                                    $this->apiResult ["message"] = "Upload Fail! No Data Found in sheet ";
                                                }
                                                //echo "<pre>";print_r($validationError);
                                                   
                                                //foreach($finalValuesSchools as $data) {
                                                    
                                              //  }
                                                /*if(count($finalValuesSchools)>=1) {
                                                    
                                                   $clientId = $clientModel->createClientBulk($finalValuesSchools);
                                                    
                                                   $uid = $this->userModel->createUserBatch ( $data['email_id'], $data['email_id'], $data['principle_name'], $clientId );
                                                    
                                                   $this->userModel->addUserRoleBatch ( $uid, $roles );
                                                }*/
                                                // print_r($finalValuesSchools);die;

						
					
				} else {
                                        $this->apiResult ["status"] = 1;
					$this->apiResult ["message"] = "Error occurred while moving file\n ";
				}
			} else {
                                $this->apiResult ["status"] = 1;
				$this->apiResult ["message"] = "Invalid file extension. Only " . implode ( ", ", $allowedExt ) . " type files are allowed\n";
			}
		}
		$this->apiResult['file_name'] = "File Name-".$_FILES ['aqs_excel_file'] ['name'];
		$this->apiResult['uploaded_date'] = "Upload Date-".date("Y-m-d h:i:s");
		return $this->apiResult;
	}
        
        /*
         * function to submit  school assessment self feedback
         */
        function submitSelfFeedbackAction() {
           
                $feedbackData = array();
                $validationStatus = 0;
                $_POST['q_id'] = isset($_POST['q_id'])?$_POST['q_id']:array();
                $user_id = isset($_POST['user_id'])?$_POST['user_id']:0;
                $assessment_id = isset($_POST['assessment_id'])?$_POST['assessment_id']:0;
                $is_submit = isset($_POST['is_submit'])?$_POST['is_submit']:0;
                $userEmail = isset($_POST['userEmail'])?$_POST['userEmail']:'';
                $userName = isset($_POST['userName'])?$_POST['userName']:'';
                $school_name = isset($_POST['school_name'])?$_POST['school_name']:'';
                if(empty($_POST['user_id']))
                    $this->apiResult ["message"] = 'User Id is mandatory';
                else if(empty($_POST['assessment_id']))
                    $this->apiResult ["message"] = 'Assessment ID is mandatory';
                else if(empty($_POST['sub_role']))
                    $this->apiResult ["message"] = 'User Sub role is mandatory';
                else {
                        foreach($_POST['q_id'] as $key=>$value) {

                            if(empty(trim($value))) {
                                $validationStatus = 1;
                            }else {
                                $feedbackData[] = array("user_id"=>isset($_POST['user_id'])?$_POST['user_id']:0,
                                                'assessment_id'=> isset($_POST['assessment_id'])?$_POST['assessment_id']:0,
                                                'sub_role_id'=> isset($_POST['sub_role'])?$_POST['sub_role']:0,
                                                'q_id'=> $key,
                                                'answer'=>trim($value));
                            }

                        }
                        if($validationStatus == 1 && $is_submit == 1) {
                            $this->apiResult ["message"] = 'All questions are mandatory';
                        }else {
                            $this->apiResult ["message"] = 'Something went wronge';
                            $this->apiResult ["status"] = 0;
                            $dignosticModel = new diagnosticModel();
                           
                            $feedbackStatus = $dignosticModel->submitSchoolAssessmentSelfFeedback($feedbackData,0,$is_submit,$user_id,$assessment_id,$this->user);
                            if($feedbackStatus >= 1) {
                                
                                if($is_submit){ 
                                    $this->apiResult ["message"] = 'Your feedback submitted successfully';
                                    $data = array_intersect ( $this->user['role_ids'] , array(1,2,8));
                                    $type=0;
                                    //$userEmail='';$userName='';
                                    if(empty($data)) {
                                        $type=1;
                                        $dignosticModel->sendNotification($feedbackData,$school_name,$type,$userEmail,$userName);
                                    }  
                                }else
                                    $this->apiResult ["message"] = 'Your feedback saved successfully';
                                
                                    $this->apiResult ["status"] = 1;
                                
                            }
                        }
                }
                return $this->apiResult;
        }
        
        /*
         * function to  submit goals for next review
         */
        function submitFeedbackGoalAction(){
            
                $is_submit = isset($_POST['is_submit'])?$_POST['is_submit']:0;                
                $assessment_id = isset($_POST['assessment_id'])?$_POST['assessment_id']:0;
                $user_id = isset($_POST['user_id'])?$_POST['user_id']:0;
                $goal = isset($_POST['feedback_goal'])?trim($_POST['feedback_goal']):'';
                $this->apiResult ["status"] = 0;
                if(empty($goal)) {
                    
                     $this->apiResult ["message"] = 'Please enter your goal';
                }else {
                    
                    $dignosticModel = new diagnosticModel();
                    //echo "<pre>"; print_r($peerIds); 
                    $postData = array(
                        'assessment_id'=>$assessment_id,
                        'user_id'=>$user_id,
                        'goal'=>$goal
                    );
                   if($dignosticModel->insertPeerFeedbackGoals($postData)){
                       
                        $this->apiResult ["message"] = 'Submit successfully';
                        $this->apiResult ["status"] = 1;
                   }
                }
                return $this->apiResult;
        }
         /*
         * function to submit  school assessment peer feedback
         */
        function submitPeerFeedbackAction() {
           
                $feedbackData = array();
                $peerIds = array();
                $assessmentTeam = array();
                $validationStatus = 0;
                $sendMail = 0;
                //$_POST['q_id'] = isset($_POST['q_id'])?$_POST['q_id']:array();
                $is_submit = isset($_POST['is_submit'])?$_POST['is_submit']:0;                
                $is_approve = isset($_POST['is_approve'])?$_POST['is_approve']:0;                
                $assessment_id = isset($_POST['assessment_id'])?$_POST['assessment_id']:0;
                $user_id = isset($_POST['user_id'])?$_POST['user_id']:0;
                $school_name = isset($_POST['school_name'])?$_POST['school_name']:'';
                //print_r($_POST );die;
                if(empty($_POST['user_id']))
                    $this->apiResult ["message"] = 'User Id is mandatory';
                else if(empty($_POST['assessment_id']))
                    $this->apiResult ["message"] = 'Assessment ID is mandatory';
               // else if(empty($_POST['peer_id']))
                   // $this->apiResult ["message"] = "Name of the assessor/facilitator is mandatory";
                
                else {
                        //print_r($this->user['role_ids']);
                        foreach($_POST['user'] as $key=>$data) {
                            
                            $data = array_map('trim', $data);
                            if(count(array_filter($data)) != count($data))  {
                                $validationStatus = 1;
                            }else {
                                
                                $data['assessment_id'] = isset($_POST['assessment_id'])?$_POST['assessment_id']:0;
                                $data['sub_role'] = isset($_POST['sub_role'])?$_POST['sub_role']:0;
                                $data['user_id'] = isset($_POST['user_id'])?$_POST['user_id']:0;
                                $data['peer_id'] = isset($key)?$key:0;
                                $peerIds[] = isset($key)?$key:0;
                                $feedbackData[] = $data;
                                
                            }

                        }
                        //echo $validationStatus;
                        //echo "<pre>";print_r($feedbackData);
                        if(count($feedbackData) >= 1) {
                            if($validationStatus == 1 && $is_submit == 1) {
                                $this->apiResult ["message"] = 'All questions are mandatory';
                            }else {
                                $this->apiResult ["message"] = 'Something went wronge';
                                $this->apiResult ["status"] = 0;
                                $dignosticModel = new diagnosticModel();
                                $this->db->start_transaction ();
                               // echo "<pre>"; print_r($peerIds); 
                               
                                $feedbackStatus = $dignosticModel->submitSchoolAssessmentSelfFeedback($feedbackData,1,$is_submit,$user_id,$assessment_id,$this->user);
                                if($feedbackStatus ) {
                                    $this->db->commit();
                                    if($is_submit == 1) {
                                        $this->apiResult ["message"] = 'Your feedback submitted successfully';
                                        $data = array_intersect ( $this->user['role_ids'] , array(1,2,8));
                                        $type=0;$userEmail='';$userName='';
                                        if(count($data))
                                            $type=3;
                                        else{
                                            $type=2;
                                            $userEmail = isset($_POST['userEmail'])?$_POST['userEmail']:'';
                                            $userName = isset($_POST['userName'])?$_POST['userName']:'';
                                        }
                                        if($is_approve == 1) {
                                            $assessmentTeam = $dignosticModel->getSchoolAssessmentAllUser($assessment_id);
                                            foreach($assessmentTeam as $key=>$data) {

                                                if($data['user_sub_role'] == 8) {
                                                    unset($assessmentTeam[$key]);
                                                }
                                            }
                                            //,array_column($assessmentTeam,'user_id')
                                           
                                            $feedbackStatusArray = $dignosticModel->getFeedbackStatus($assessment_id);
                                            if(!empty($feedbackStatusArray) && count($feedbackStatusArray) == count($assessmentTeam)) {
                                                $sendMail = 0;
                                                $dignosticModel->createSubmitNotificationQueue($assessment_id,$assessmentTeam);
                                            }
                                                //$sendMail =0;
                                        }else 
                                            $sendMail = 1;
                                       // echo "aaa". $sendMail;
                                        if($sendMail)
                                            $dignosticModel->sendNotification($feedbackData,$school_name,$type,$userEmail,$userName);
                                    }
                                    else 
                                        $this->apiResult ["message"] = 'Your feedback saved successfully';
                                    $this->apiResult ["status"] = 1;
                                }else {
                                    $this->db->rollback();
                                }
                            }
                        }else {
                            $this->apiResult ["message"] = 'Atleast All questions for one user are mandatory';
                            //$this->apiResult ["status"] = 0;
                        }
                }
                return $this->apiResult;
        }
        /*
         * function to delete a folder(Directory)
         */
        function deleteFolderAction() {

            if (!in_array("upload_resources", $this->user ['capabilities']))
                $this->apiResult ["message"] = "You are not authorized to perform this task.\n";
            else if (empty(trim($_REQUEST['directory_id'])))
                $this->apiResult ["message"] = "Folder Id cannot be empty.\n";
            else {
                $resourceModel = new resourceModel();
                $directoryId = isset($_REQUEST['directory_id']) ? $_REQUEST['directory_id'] : '';
                $directoryStatus =  $resourceModel->chkValidDirectoryForDelete($directoryId);
                if ($directoryId >= 1) {
                     $directoryStatus =  $resourceModel->chkChilds($directoryId);
                    //$resourceModel->deleteDirectory($directoryId)
                    if ($resourceModel->deleteDirectory($directoryId)) {
                       
                        $this->apiResult ["status"] = 1;
                       // $this->apiResult ["parent_id"] = $directoryStatus;
                        $this->apiResult ["message"] = "Folder deleted successfully";
                        if(!empty($directoryStatus)) {
                            $this->apiResult ["parent_id"] = $directoryStatus['parent_directory_id'];
                            $this->apiResult ["childs"] = $directoryStatus['num_childs']-1;
                            $this->apiResult ["num_files"] = $directoryStatus['num_files'];
                        }
                    } else {
                        $this->apiResult ["status"] = 1;
                        $this->apiResult ["message"] = "Something went wronge";
                    }
                }
            }
        }
          /*
         * function to get users roles by schools 
         */
        function getSchoolUsersRoleAction() {
		if (! in_array ( "create_network", $this->user ['capabilities'] ))
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			if(empty($_POST['school_ids']) && empty($_POST['user_role_ids']))
				$this->apiResult ["message"] = "School id/User Role cannot be empty.\n";
				else {
					$resourceModel = new resourceModel ();
				        $school_ids = $_POST['school_ids'];
				        //$user_role_ids = $_POST['user_role_ids'];
                                        //print_r($school_ids);die;
					//echo $network_id;
                                        $user_list = $resourceModel->getSchoolUsersRoles($school_ids);
                                        $user_list = array_values(array_filter(array_map('array_filter', $user_list)));
//                                        / echo "<pre>";  print_r($user_list);
                                        if(count($user_list) >= 1) {
                                          $this->apiResult ["status"] = 1;  
                                          $this->apiResult ["message"] = 'Success';
                                          $this->apiResult ["role_list"] = $user_list;

                                        }else {
                                            $this->apiResult ["status"] = 0;
                                            $this->apiResult ["message"] = "User not exist for this schools";
                                        }
					
				}
	}
                 
        function getLanguagesfromDiagnosticAction(){
    
        //print_r($_POST);
        $diagnostic_id=$_POST['diagnostic_id'];
        $diagnosticModel = new diagnosticModel ();
        $languages=$diagnosticModel->getLanguagesfrombase($diagnostic_id,explode(",",DIAGNOSTIC_LANG));
        $this->apiResult ["languages"]=$languages;
        //$diagnostic_type=$diagnosticModel->getDiagnosticDetailsfromTransLang($diagnostic_id);
        //$this->apiResult ["d_type"]=$diagnostic_type['assessment_type_id'];
        //$this->apiResult ["d_teacher_type"]=$diagnostic_type['teacher_cat_id'];
        //$this->apiResult ["diagnostic_id"]=$diagnostic_type['diagnostic_id'];
        //$this->apiResult ["equivalence_id"]=$diagnostic_type['equivalence_id'];
        //$this->apiResult ["lang_id_original"]=$diagnostic_type['language_id'];
        //$this->apiResult ["diag_title"]=$diagnostic_type['translation_text'];
        //$this->apiResult ["kpa_recommendations_p"]=$diagnostic_type['kpa_recommendations'];
        //$this->apiResult ["kq_recommendations_p"]=$diagnostic_type['kq_recommendations'];
        //$this->apiResult ["cq_recommendations_p"]=$diagnostic_type['cq_recommendations'];
        //$this->apiResult ["js_recommendations_p"]=$diagnostic_type['js_recommendations'];
        $this->apiResult ["status"] = 1;
        }
        
        function getLanguagesAvaifromDiagnosticAction(){
            $equivalence_id=$_POST['diagnostic_id'];
            $lang_id=$_POST['lang_id'];
            $diagnosticModel = new diagnosticModel ();
            $dia_details=$diagnosticModel->getDiagnosticBYEqui($equivalence_id,$lang_id);
            $diagnostic_id=$dia_details['lang_translation_id'];
            
        $languages=$diagnosticModel->getLanguages($diagnostic_id,explode(",",DIAGNOSTIC_LANG));
        $this->apiResult ["languages"]=$languages;
        $diagnostic_type=$diagnosticModel->getDiagnosticDetailsfromTransLang($diagnostic_id);
        $this->apiResult ["d_type"]=$diagnostic_type['assessment_type_id'];
        $this->apiResult ["d_teacher_type"]=$diagnostic_type['teacher_cat_id'];
        $this->apiResult ["diagnostic_id"]=$diagnostic_type['diagnostic_id'];
        $this->apiResult ["equivalence_id"]=$diagnostic_type['equivalence_id'];
        $this->apiResult ["lang_translation_id"]=$diagnostic_type['lang_translation_id'];
        $this->apiResult ["lang_id_original"]=$diagnostic_type['language_id'];
        $this->apiResult ["diag_title"]=$diagnostic_type['translation_text'];
        $this->apiResult ["kpa_recommendations_p"]=$diagnostic_type['kpa_recommendations'];
        $this->apiResult ["kq_recommendations_p"]=$diagnostic_type['kq_recommendations'];
        $this->apiResult ["cq_recommendations_p"]=$diagnostic_type['cq_recommendations'];
        $this->apiResult ["js_recommendations_p"]=$diagnostic_type['js_recommendations'];
        $this->apiResult ["status"] = 1;
        }
                
        function getLanguagesfromAAction(){
        $action_type=$_POST['action_type'];
        if($action_type==1){
        $languages=$this->userModel->getTranslationLanguale(explode(",",DIAGNOSTIC_LANG));
        }else{
        $languages=array();    
        }
        $this->apiResult ["languages"]=$languages;
        $this->apiResult ["status"] = 1;
        }
        
        function getLanguagesNameAction(){
            $language_id=$_POST['language_id'];
            $userModel=new userModel();
            
            $this->apiResult ["language"]=$userModel->getLanguageById($language_id);
            $this->apiResult ["status"] = 1;
            
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
          public function buildChildTree(array &$elements, $parentId = 0 , &$childList = array()) {

            $branch = array();
            foreach ($elements as $element) {
                if ($element['ParentCategoryId'] == $parentId) {

                    //$directoryFile = $this->getDirectoryFiles($element['directory_id'], $fileList);
                    $children = $this->buildChildTree($elements, $element['directory_id'],$childList);
                   // echo"<br>". $element['directory_id'];
                    $childList[]=$element['directory_id'];
                    //$branch[$element['directory_id']] = $element['directory_id'];
                }
            }
            return $childList;
    } 
    
    public function validateDate($dbDate){
        
         $DOB = explode("-",$dbDate);
         //var_dump(checkdate($DOB[1], $DOB[0], $DOB[2]));
        if(checkdate($DOB[1], $DOB[0], $DOB[2]) === FALSE)
            return 1;
        else 
            return 0;
    }
    
    //update assessment complete percentage
    function updateCollaborativeAssessmentPercentage($assessment_id,$allAccessorsId,$external=0,$isLeadAccessor = 0){
        
                               // echo $isLeadAccessor;
                                $kpas = array();
                                $userKpas = array();
                                $allKpas = array();
                                $kqs = array();
                                $cqs = array();
                                $jss = array();
                                $diagnosticModel = new diagnosticModel();
                                $lang_id = DEFAULT_LANGUAGE;
                                $singleKpaId = 0; // empty($_POST['kpa_id'])?0:$_POST['kpa_id']; //if kpa id is given then we will check & save data for single kpa otherwise if it is 0 then we will check & save data for all kpas
                                                                                                
                                    $is_collaborative = 1;
                                    $user_id = 0;
                                    //echo "<pre>"; print_r($allAccessorsId);
                                    foreach($allAccessorsId as $key=>$val) {
                                        
                                         $user_id =  $val;                                        
                                         $kpas =   $this->db->array_col_to_key($diagnosticModel->getKpasForAssessment($assessment_id, $val,0,$lang_id,$is_collaborative,$external,$isLeadAccessor), "kpa_instance_id");
                                         
                                         $userKpas = array_keys($kpas);
                                         $kpas = $allKpas+$kpas;
                                         
                                         $kqs  = $kqs+ $this->db->array_grouping($diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $val,0,$lang_id,$external,$userKpas,$isLeadAccessor), "kpa_instance_id", "key_question_instance_id");
                                         $cqs  = $cqs+ $this->db->array_grouping($diagnosticModel->getCoreQuestionsForAssessment($assessment_id, $val,0,$lang_id,$external,$userKpas,$isLeadAccessor), "key_question_instance_id", "core_question_instance_id");
                                         $jss  = $jss+ $this->db->array_grouping($diagnosticModel->getJudgementalStatementsForAssessment($assessment_id, $val,0,$lang_id,$external,$userKpas,$isLeadAccessor), "core_question_instance_id", "judgement_statement_instance_id");
                                       
				
				$success = true;
				
				$complete = true;
				
				$kpa_count = 0;
				
				$noOfComplete = 0;
				
				$noOfIncomplete = 0;
                                //echo "<pre>";print_r($kpas);
                                foreach ( $kpas as $kpa_id => $kpa ) {
					
					$kpaJs_ratings = array ();
					$kpaSq_ratings = array ();
					$kpa_count ++;
					
					$kq_ratings = array ();
					
					$kq_count = 0;
					
					foreach ( $kqs [$kpa_id] as $kq_id => $kq ) {
						
						$kq_count ++;
						
						$cq_ratings = array ();
						
						$cq_count = 0;
						
						foreach ( $cqs [$kq_id] as $cq_id => $cq ) {
							
							$cq_count ++;
							
							$js_ratings = array ();
							
							$js_count = 0;
							
							foreach ( $jss [$cq_id] as $js_id => $js ) {
								
								$js_count ++;
								//echo "<pre>";print_r($js);die;
                                                                
								$val = empty ( $js['numericRating'] ) ? "" : $js['numericRating'];
								
								if ($val > 0) {
									
									$noOfComplete ++;
								} else
									
									$noOfIncomplete ++;
							}
							
							
						}
						
					}
					
					//echo $noOfComplete;
					//if ($assessment ['role'] == 4) {
                                               /* $akns =  $diagnosticModel->getAssessorKeyNotes ( $assessment_id,'',$kpa_id );
						$isKNComplete = true;
						//$akns = isset($_POST ['aknotes'])?$_POST ['aknotes']:array();
                                                //echo "<pre>"; print_r($akns);
						if (isset ( $akns ) && count ( $akns )) {
							foreach ( $akns as $akn ) {
                                                           // print_r($akn['text_data']);
								if (empty($akn['text_data']))
									$isKNComplete = false;
                                                                
                                                                break;
                                                        }
						}else
                                                    $isKNComplete = false;
						if ($isKNComplete)
							$noOfComplete ++;
						else
							$noOfIncomplete ++;*/
							
					//}
				}
                                                       
				//$success = true;
                                //echo"a". $noOfComplete;
				$completedPerc = 0;
                                $total = $noOfIncomplete + $noOfComplete;
                               // echo $isLeadAccessor;
                                $completedPerc =   @((100 * $noOfComplete) / $total);
                               // echo $val;
                                $diagnosticModel->updateCompleteStatus($assessment_id,$user_id,$completedPerc,$isLeadAccessor);
                                $avgPercntg = $this->calculatePercentage($assessment_id);
                                if(!empty($avgPercntg))
                                    $diagnosticModel->updateAvgAssessmentPercentage ( $assessment_id,$avgPercntg);
                                //die;
                                    }
                                    return true;
        
    }
    
     //function to calculate overall % for collaborative review
    function  calculatePercentage($assessment_id){
        
         $diagnosticModel = new diagnosticModel();
         $externalTeam = $diagnosticModel->getCollAssessmentTeam($assessment_id);
         $assessmentKpas = $diagnosticModel->getAssessmentKpa($assessment_id);
         //print_r($assessmentKpas);
         $totalMembers = 0;
         $totalPercntg = 0;
         $avgPercntg = 0;
         if(!empty($externalTeam)){
             $totalMembers = count($externalTeam);
             foreach($externalTeam as $perc){
                 
                 if($perc['percComplete'] > 0){
                 $userKpas = $diagnosticModel->getAssessmentKpa($assessment_id,$perc['user_id']);
                // print_r($userKpas);
                 $totalPercntg += ($perc['percComplete']*$userKpas['kpa_num'])/$assessmentKpas['kpa_num'];
                 }
             }
             //echo $totalPercntg;die;
             /*if(!empty($totalPercntg) && $totalPercntg > $totalMembers){
                 $avgPercntg = ($totalPercntg/$totalMembers);
             }else if(!empty($totalPercntg)){
                 $avgPercntg = $totalPercntg;
             }*/
         }
         return $totalPercntg;
         //print_r($externalTeam);die;
         
        
    }

     public function actionplandataAction(){
        
        //print_r($_POST);
        //die();
        $id_c=$_POST['id_c'];
        $assessment_id=$_POST['assessment_id'];
        $actionModel=new actionModel();
        $aqsDataModel = new aqsDataModel();
        $details=$actionModel->getDetailsofAssessment($id_c);
        $datesrange=isset($_POST['datesrange'])?$_POST['datesrange']:'';
        
        /*if(!isset($_POST['datesrange'])){
        $from_date=$details['from_date'];
        $to_date=$details['to_date'];
        }else{
          $datesrangeex=explode("/",$datesrange);
          $from_date=$datesrangeex['0'];
          $to_date=$datesrangeex['1'];
        }*/
        
        if(isset($_POST['datesrange']) && !empty($_POST['datesrange'])){
             $datesrangeex=explode("/",$datesrange);
             $from_date=$datesrangeex['0'];
             $to_date=$datesrangeex['1'];
        }else{
            
            if(isset($_POST['type']) && $_POST['type']=="image"){
                $date=(isset($details['from_date']) && $details['from_date']!="0000-00-00")?$details['from_date']:'';
                $date_start_real=(isset($details['from_date']) && $details['from_date']!="0000-00-00")?$details['from_date']:'';
                $date_end_real=(isset($details['to_date']) && $details['to_date']!="0000-00-00")?$details['to_date']:date("Y-m-d");
                $end_date =$date_end_real;

                $array_dates=array();
                $ii=0;
                if(!empty($date)){

                    $ii=0;   
                    while (strtotime($date) <= strtotime($end_date)) {
                             $sdate=date ("Y-m-d",strtotime($date));

                             $dateex = date ("Y-m-d", strtotime("".$details['frequency_days']."", strtotime($date)));

                             $date = date ("Y-m-d", strtotime("-1 day", strtotime($dateex)));

                             if($date>$date_end_real){
                             $dateex = date ("Y-m-d", strtotime("+1 day", strtotime($date_end_real)));
                             $date = date ("Y-m-d", strtotime("-1 day", strtotime($dateex)));  
                             $array_dates[$ii]['fromDate']=$sdate;
                             $array_dates[$ii]['endDate']=$date_end_real;
                             $date = date ("Y-m-d", strtotime("+1 day", strtotime($date_end_real)));
                             $ii++;
                             break;
                             }else{

                             $array_dates[$ii]['fromDate']=$sdate;
                             $array_dates[$ii]['endDate']=$date;
                             $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
                             }
                       $ii++;
                    }

                }


                if($ii==0){
                $sdate=date ("Y-m-d",strtotime($date));
                $dateex = date ("Y-m-d", strtotime($end_date));
                $array_dates[$ii]['fromDate']=$sdate;
                $array_dates[$ii]['endDate']=$dateex;
                
                }

                //echo"<pre>";
                //print_r($array_dates);
                $array_dates_f=array();
                foreach($array_dates as $key=>$val){

                    $array_dates_f[]=$val;

                }
                
                $from_date=$details['from_date'];
                $to_date=$details['to_date'];
                foreach($array_dates_f as $key=>$val){
                        
                        if($val['endDate']<=date("Y-m-d")){
                         $to_date = $val['endDate'];   
                        }
                }
                
                
                
            }else{
            $from_date=$details['from_date'];
            $to_date=$details['to_date'];
            }
            
            
        }
        
        $frequency=$details['frequency_days'];
        
        //echo "<pre>";
        //print_r($details);
        
        $begin = new DateTime($from_date);
        $end = new DateTime($to_date);
        $end = $end->modify('+1 day'); 
        
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        
        //print_r($period);
        //die();
        $activity=$aqsDataModel->getActivity();
        
        $h_assessor_action1_id=isset($details['h_assessor_action1_id'])?$details['h_assessor_action1_id']:'';
        $activityDetails=$actionModel->getActivityAction2($h_assessor_action1_id);
        
        
                
        $activityDetails_final=$this->db->array_grouping($activityDetails,"activity","");
        
       
        //echo"<pre>";
        //print_r($activityDetails_final);
        //echo"</pre>";
        //die();
        
        $activity_data=array();
        $a=array();
        $date_count=array();
        $tickarray=array();
        foreach($activity as $key=>$val){
        $data_p=array();
        $categories=array();
        $data_c=array();
        $data_e=array();
        
        $activity_group=isset($activityDetails_final[$val['activity_id']])?$activityDetails_final[$val['activity_id']]:array();
        //echo"<pre>";
        //print_r($activity_group);
        //echo"</pre>";
        // die();
        if(count($activity_group)>0){
        $activity_data_c=array();
        $activity_data_p=array();
        $activity_data_e=array();
        
        $group_status_overall=$this->db->array_grouping($activity_group,"activity_status","");
        
        $status_notstarted_overall=isset($group_status_overall[0])?$group_status_overall[0]:array();
        $status_started_overall=isset($group_status_overall[1])?$group_status_overall[1]:array();
        $status_completed_overall=isset($group_status_overall[2])?$group_status_overall[2]:array();
        
        $status_started_overall_date=$this->db->array_grouping($status_started_overall,"activity_date","");
        $status_notstarted_overall_date=$this->db->array_grouping($status_notstarted_overall,"activity_date","");
        $exp=0;
        $expp=0;
        foreach($status_started_overall_date as $expkey=>$expval){
            if($expkey<date("Y-m-d")){
                $exp++;
            }else{
              $expp++;  
            }
        }
        
        foreach($status_notstarted_overall_date as $expkey=>$expval){
            if($expkey<date("Y-m-d")){
                $exp++;
            }else{
              $expp++;  
            }
        }
        //echo $expp;
        //echo $exp;
        //print_r($status_started_overall_date);
        //print_r($status_notstarted_overall_date);
        //die();
        if(count($status_completed_overall)>0){
        $activity_data_c["name"]= ''.$val['activity'].'-C';
        $activity_data_c["color"]='rgba(3, 168, 3,1)';
        $activity_data_c["marker"]=array("symbol"=>isset($val['symbol'])?$val['symbol']:'');
        }
        
        
        if((count($status_started_overall)>0 || count($status_notstarted_overall)>0) && $expp>0){
        $activity_data_p=array("name"=> ''.$val['activity'].'-P',
        "color"=> 'rgba(255,140,0,1)');
        $activity_data_p["marker"]=array("symbol"=>isset($val['symbol'])?$val['symbol']:'');
        }
        
        if((count($status_started_overall)>0 || count($status_notstarted_overall)>0) && $exp>0){
        $activity_data_e=array("name"=> ''.$val['activity'].'-Ex',
        "color"=> 'rgba(255,0,0,1)');
        $activity_data_e["marker"]=array("symbol"=>isset($val['symbol'])?$val['symbol']:'');
        }
        
        $activity_group_date=$this->db->array_grouping($activity_group,"activity_date","");
        
        foreach ($period as $dt) {
            
        $date=$dt->format("Y-m-d");
        
        if($date==date("Y-m-d")){
        $categories[]='Today';
        //$tickarray[]=strtotime($date)*1000;
        }else if($date==$from_date || $date==$to_date){
        $categories[]=date("d-m-Y",strtotime($date));    
        //$tickarray[]=strtotime($date)*1000;
        }else{
        $categories[]='';
        }
        
        $final_date=isset($activity_group_date[$date])?$activity_group_date[$date]:array();
        $group_status=$this->db->array_grouping($final_date,"activity_status","");
        
        if(count($group_status)>0){
        //echo"<pre>";
        //print_r($group_status);
        //echo"</pre>";
        }
        
        $status_notstarted=isset($group_status[0])?$group_status[0]:array();
        $status_started=isset($group_status[1])?$group_status[1]:array();
        $status_completed=isset($group_status[2])?$group_status[2]:array();
        
        $expcount=0;
        $pendcount=0;
        foreach($status_started as $k1=>$v1){
            if($v1['activity_date']<date("Y-m-d")){
              $expcount++;  
            }else{
              $pendcount++;                                                                                  
            }
        }
        
        foreach($status_notstarted as $k2=>$v2){
            if($v2['activity_date']<date("Y-m-d")){
              $expcount++;  
            }else{
              $pendcount++;                                                                                  
            }
        }
        
        $start=(isset($date_count[$date])?$date_count[$date]:0)*0.25;
        //$gmtTimezone = new DateTimeZone('GMT');
        date_default_timezone_set('UTC');
        $myDateTime = strtotime($date)*1000;
        
        if($date==date("Y-m-d") || $date==$from_date || $date==$to_date){
        if(!in_array($myDateTime,$tickarray)){    
        $tickarray[]=$myDateTime;
        }
        }
        //$myDateTime = strtotime("".$date." 12");
        //$dateutc=DateTime::createFromFormat( 'U',strtotime($date));
        //$myDateTime=$date->getTimestamp();
        $tot_point=0;
        if(count($status_completed)>0){
        $data_c[]=array($myDateTime,$start);
        $start=$start+0.25;
        
        $tot_point++;
        }else{
        $data_c[]=array($myDateTime,-1);    
        }
        
        if(count($status_started)>0 && $pendcount>0){
        $data_p[]=array($myDateTime,$start);
        $start=$start+0.25;
        $tot_point++;
        }else if(count($status_started)==0 && count($status_notstarted)>0 && $pendcount>0){
        $data_p[]=array($myDateTime,$start);
        $start=$start+0.25;
        
        $tot_point++;
        }else{
          $data_p[]=array($myDateTime,-1);  
        }
        
        
        if(count($status_started)>0 && $expcount>0){
        $data_e[]=array($myDateTime,$start);
        $start=$start+0.25;
        $tot_point++;
        }else if(count($status_started)==0 && count($status_notstarted)>0 && $expcount>0){
        $data_e[]=array($myDateTime,$start);
        $start=$start+0.25;
        
        $tot_point++;
        }else{
          $data_e[]=array($myDateTime,-1);  
        }
        
        $date_count[$date]=(isset($date_count[$date])?$date_count[$date]:0)+$tot_point;
        
        }
        
        $activity_data_c['data']=$data_c;
        $activity_data_p['data']=$data_p;
        $activity_data_e['data']=$data_e;
        
        if(count($status_completed_overall)>0){
        $a[]=$activity_data_c;
        }
        
        if((count($status_started_overall)>0 || count($status_notstarted_overall)>0) && $expp>0){
        $a[]=$activity_data_p;
        }
        
        if((count($status_started_overall)>0 || count($status_notstarted_overall)>0) && $exp>0){
        $a[]=$activity_data_e;
        }
        
       
        
        }
        
        }
        
        //print_r($a);
        //die();
        /*$a=array(array( "name"=> 'Research-C',
        "color"=> 'rgba(0,128,0, .5)',
        "data"=> array(0,'', 0, '', 0),
        "marker"=>array("symbol"=> 'triangle')
        ),
        array( "name"=> 'Research-P',
        "color"=> 'rgba(255,165,0, .5)',
        "data"=> array(0.75,'', 0, '', 0),
        "marker"=>array("symbol"=> 'triangle')
        )    
        );*/
        //echo $date;
        //$xais=array('categories'=>$categories);
        // Date.UTC(2015, 1, 12) // feb 12, 2015
        
        
        $xais=array('type'=>'datetime',
            'dateTimeLabelFormats'=>array('second'=>'%H:%M:%S',
                                          'minute'=>'%H:%M',
                                          'hour'=>'%H:%M',
                                          'day' => '%e. %b',
                                          'week' => '%e. %b',
                                          'month' => '%b \'%y',
                                          'year' => '%Y'                                                      
                                     ),
            'tickInterval'=> 24 * 3600 * 1000,
             'tickPositions'=> $tickarray,
            'labels' =>array("format"=>'{value:%e .%m. %y}','style'=>array("color"=>"#000000"),'autoRotation'=>array (-10, -20, -30, -40, -50, -60, -70, -80, -90)),
            'lineColor'=>'#000000',
            'gridLineColor'=>'#000000',
            'tickColor'=> '#000000',
            
            );
        $this->apiResult ["data"]=$a;
        $this->apiResult ["xaxis"]=$xais;
        $this->apiResult ["status"] = 1;
        
    }
    
    function actionplantipAction(){
        //print_r($_POST);
        $id_c=$_POST['id_c'];
        $assessment_id=$_POST['assessment_id'];
        $actionModel=new actionModel();
        $aqsDataModel = new aqsDataModel();
        $details=$actionModel->getDetailsofAssessment($id_c);
        $from_date=$details['from_date'];
        $to_date=$details['to_date'];
        $frequency=$details['frequency_days'];
        
        $begin = new DateTime($from_date);
        $end = new DateTime($to_date);
        $end = $end->modify('+1 day'); 
        
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        $date=array();
        foreach ($period as $dt) {
            
        $date[]=$dt->format("Y-m-d");
        }
        
        $date_a=$date[$_POST['index']];
        
        $planorc=explode("-",$_POST['series']);
        //$totc=count($planorc)-1;
        
        $planorcf=$planorc[1];
        $activity=$planorc[0];
        
        $h_assessor_action1_id=isset($details['h_assessor_action1_id'])?$details['h_assessor_action1_id']:'';
        $activityDetails=$actionModel->getActivityActionTip2($h_assessor_action1_id,$date_a,$activity,$planorcf);
        $text="";
        $i=1;
        foreach($activityDetails as $keyac=>$valac){
          $text.="".$i."-".$valac['activity_stackholder_ids']."";
          $i++;
        }
        
        $array=array();
        $array['series']=$_POST['series'];
        $array['a_date']="".date("d-m-Y",strtotime($date_a))." (".($i-1).")";
        $array['textshow']=$text;
        $this->apiResult ["data"]=$array;
        $this->apiResult ["status"] = 1;
    }
    
    
    function actionplanchartsaveAction(){
        $file=$_POST['file'];
        $id_c=$_POST['id_c'];
        $assessment_id=$_POST['assessment_id'];
        $actionModel=new actionModel();
        $details=$actionModel->getDetailsofAssessment($id_c);
        $datesrange=isset($_POST['datesrange'])?$_POST['datesrange']:'';
        /*if(!isset($_POST['datesrange'])){
        $from_date=$details['from_date'];
        $to_date=$details['to_date'];
        }else{
          $datesrangeex=explode("/",$datesrange);
          $from_date=$datesrangeex['0'];
          $to_date=$datesrangeex['1'];
        }*/
        
        if(isset($_POST['datesrange']) && !empty($_POST['datesrange'])){
             $datesrangeex=explode("/",$datesrange);
             $from_date=$datesrangeex['0'];
             $to_date=$datesrangeex['1'];
        }else{
            $from_date=$details['from_date'];
            $to_date=$details['to_date'];
        }
        
         $chartname=explode("charts/",$file);
         //print_r($chartname);
          $chartname_f=$chartname[1];
         if($_POST['type']=="outside"){
         $chart_url="http://export.highcharts.com/".$file."";    
         }else{
         $chart_url="".DOWNLOAD_CHART_URL."".$file."";
         }
        //$chart_url="".DOWNLOAD_CHART_URL."".$file."";
        //$chart_url="".DOWNLOAD_CHART_URL."".$file."";

        $contents=getdata($chart_url);
        
        if(isset($_POST['datesrange']) && !empty($_POST['datesrange'])){
        $upload_url="".UPLOAD_PATH."charts/".$assessment_id."_".$id_c."_".$from_date."_".$to_date.".png";
        }else{
        $upload_url="".UPLOAD_PATH."charts/".$assessment_id."_".$id_c.".png";    
        }
        

        file_put_contents($upload_url,$contents);
        
        upload_file($upload_url,$upload_url);
        @unlink($upload_url);
        
        if($_POST['type']=="local"){
        //echo "".CHART_URL_GENERATE."".$chartname_f."";    
        @unlink("".CHART_URL_GENERATE."".$chartname_f."");
        //echo "".CHART_URL_GENERATE."".$chartname_f.""; 
        }
        //print_r($_POST);
        $this->apiResult ["file"] = $chartname_f;
        $this->apiResult ["status"] = 1;
    }

}

