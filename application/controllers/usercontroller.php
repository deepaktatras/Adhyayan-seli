<?php

class userController extends controller{

	

	function userAction(){
                //echo $this->user['user_id'];
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

		elseif(in_array("manage_all_users",$this->user['capabilities']) 

				|| in_array("manage_own_users",$this->user['capabilities'])

				|| ($this->user['network_id']>0 && in_array("manage_own_network_users",$this->user['capabilities']))){
                        
			$cPage=empty($_POST['page'])?1:$_POST['page'];
                    
			$order_by=empty($_POST['order_by'])?"name":$_POST['order_by'];

			$order_type=empty($_POST['order_type'])?"asc":$_POST['order_type'];

			$param=array(

					"page"=>$cPage,

					"name_like"=>empty($_POST['name'])?"":$_POST['name'],

					"client_id"=>empty($_POST['client_id'])?0:$_POST['client_id'],

					"client_like"=>empty($_POST['client'])?"":$_POST['client'],

					"email_like"=>empty($_POST['email'])?"":$_POST['email'],

					"role_id"=>empty($_POST['role_id'])?0:$_POST['role_id'],

					"network_id"=>empty($_POST['network_id'])?0:$_POST['network_id'],

					"order_by"=>$order_by,

					"order_type"=>$order_type
                                        
					);

			if(!in_array("manage_all_users",$this->user['capabilities'])){

				if(in_array("manage_own_network_users",$this->user['capabilities'])){

					$param["network_id"]=$this->user['network_id'];

					$param["exclude_cap"]=array("manage_all_users");

				}else{

					$param["client_id"]=$this->user['client_id'];

					$param["exclude_cap"]=array("manage_all_users","manage_own_network_users");

				}

			}
                        // get external assessors list for tap admin on 12-05-2016 by Mohit Kumar
                        if(current($this->user['role_ids'])==8){
                            $tap_admin_role_id = 8;
                            $param['table_name']=empty($_REQUEST['table_name'])?"":$_REQUEST['table_name'];
                        } else {
                            $tap_admin_role_id = '';
                        }
                        // for getting external assessors user list for tap admin by passing tap admin id with getUsers function on 12-05-2016 by Mohit Kumar
                        
                        // get aqs team user listing for tab admin on 16-05-2016 by Mohit Kumar
                        $_REQUEST['ref']=!empty($_REQUEST['ref'])?$_REQUEST['ref']:0;
                        $ref_key="ASSESSOR".md5(time());
                        if(isset($_REQUEST['ref']) && $_REQUEST['ref']==1 && current($this->user['role_ids'])==8){
                            
                            $alertIds = $this->db->getAlertContentIds('d_user','CREATE_EXTERNAL_ASSESSOR');
                            $alertIds = !empty($alertIds)?$alertIds['content_id']:array();
                            
                            if(!empty($alertIds)){
                                $checkAlertRelation = $this->db->getAlertRelationIds(current($this->user['role_ids']),'ASSESSOR');
                                if(!empty($checkAlertRelation)){
                                    $this->db->update('h_alert_relation',array('alert_ids'=>trim($alertIds)),
                                            array('login_user_role'=>current($this->user['role_ids']),'type'=>'ASSESSOR','id'=>$checkAlertRelation['id']));
                                } else {
                                    $this->db->insert('h_alert_relation',array('alert_ids'=>trim($alertIds),'ref_key'=>$ref_key,'flag'=>1,
                                        'login_user_role'=>current($this->user['role_ids']),'type'=>'ASSESSOR'));
                                }
                            }
                        } else if($_REQUEST['ref']==1 && current($this->user['role_ids'])==8) {
                            $this->db->delete('h_alert_relation',array('type'=>'ASSESSOR','login_user_role'=>current($this->user['role_ids'])));
                        }
                        if($_REQUEST['ref']==1 && $ref_key!=''){
                            
                            $this->db->update('d_alerts',array('status'=>1,'ref_key'=>$ref_key),array('type'=>'CREATE_EXTERNAL_ASSESSOR',
                                'table_name'=>'d_user'));
                        }
//                        die;
                        if(current($this->user['role_ids'])==8){
                            $userList = $this->userModel->getAQSTeamAssessor($param,current($this->user['role_ids']),$_REQUEST['ref'],$ref_key);
                            $objAssessment = new assessmentModel();
                            $assessorsCount = $objAssessment->getReviewCountByRole();
                            $this->set("assessorsCount",$assessorsCount);
                            $this->set('mailUser', $this->userModel->getMailRecievedUserlist());
                            $this->set('tapAssessorUser', $this->userModel->getTapAssessorUserList());
                            $this->set('userSubRoleList', $this->userModel->getuserSubRoleList());
                            $this->_template->addHeaderScript('d3.v2.js');
                        } else {
                            $userList = $this->userModel->getUsers($param,$tap_admin_role_id,$_REQUEST['ref']!='');
                            $this->set("roles",$this->userModel->getRoles());
                            $networkModel=new networkModel();
                            $this->set("networks",$networkModel->getNetworkList(array("max_rows"=>-1)));
                        }
                        //echo $this->userModel->getPageCount();
                        
                        
                        $this->set("pages",$this->userModel->getPageCount());
			$this->set("filterParam",$param);
			$this->set("users",$userList);
			
			$this->set("cPage",$cPage);

			$this->set("orderBy",$order_by);

			$this->set("orderType",$order_type);
			
		}else

			$this->_notPermitted=1;

	}
        
        /*
         * Des-function to fetch accessors for super admin
         */
        function accessorsAction(){
            
		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

		elseif(in_array("manage_all_users",$this->user['capabilities']) 

				|| in_array("manage_own_users",$this->user['capabilities'])

				|| ($this->user['network_id']>0 && in_array("manage_own_network_users",$this->user['capabilities']))){
                        
			$cPage=empty($_POST['page'])?1:$_POST['page'];
                    
			$order_by=empty($_POST['order_by'])?"name":$_POST['order_by'];

			$order_type=empty($_POST['order_type'])?"asc":$_POST['order_type'];

			$param=array(

					"page"=>$cPage,

					"name_like"=>empty($_POST['name'])?"":$_POST['name'],

					"client_id"=>empty($_POST['client_id'])?0:$_POST['client_id'],

					"client_like"=>empty($_POST['client'])?"":$_POST['client'],

					"email_like"=>empty($_POST['email'])?"":$_POST['email'],

					"role_id"=>empty($_POST['role_id'])?0:$_POST['role_id'],

					"network_id"=>empty($_POST['network_id'])?0:$_POST['network_id'],

					"order_by"=>$order_by,

					"order_type"=>$order_type
                                        
					);

			if(!in_array("manage_all_users",$this->user['capabilities'])){

				if(in_array("manage_own_network_users",$this->user['capabilities'])){

					$param["network_id"]=$this->user['network_id'];

					$param["exclude_cap"]=array("manage_all_users");

				}else{

					$param["client_id"]=$this->user['client_id'];

					$param["exclude_cap"]=array("manage_all_users","manage_own_network_users");

				}

			}
                        // get external assessors list for tap admin on 12-05-2016 by Mohit Kumar
                       //if(current($this->user['role_ids'])==1 || current($this->user['role_ids'])==8){
                            $tap_admin_role_id = 1;
                            $param['table_name']=empty($_REQUEST['table_name'])?"":$_REQUEST['table_name'];
                        
                        // for getting external assessors user list for tap admin by passing tap admin id with getUsers function on 12-05-2016 by Mohit Kumar
                        
                        // get aqs team user listing for tab admin on 16-05-2016 by Mohit Kumar
                        $_REQUEST['ref']=!empty($_REQUEST['ref'])?$_REQUEST['ref']:0;
                        $ref_key="ASSESSOR".md5(time());
                        if(isset($_REQUEST['ref']) && $_REQUEST['ref']==1){
                            
                            $alertIds = $this->db->getAlertContentIds('d_user','CREATE_EXTERNAL_ASSESSOR');
                            $alertIds = !empty($alertIds)?$alertIds['content_id']:array();
                            
                            if(!empty($alertIds)){
                                $checkAlertRelation = $this->db->getAlertRelationIds(current($this->user['role_ids']),'ASSESSOR');
                                if(!empty($checkAlertRelation)){
                                    $this->db->update('h_alert_relation',array('alert_ids'=>trim($alertIds)),
                                            array('login_user_role'=>current($this->user['role_ids']),'type'=>'ASSESSOR','id'=>$checkAlertRelation['id']));
                                } else {
                                    $this->db->insert('h_alert_relation',array('alert_ids'=>trim($alertIds),'ref_key'=>$ref_key,'flag'=>1,
                                        'login_user_role'=>current($this->user['role_ids']),'type'=>'ASSESSOR'));
                                }
                            }
                        } else if($_REQUEST['ref']==1) {
                            $this->db->delete('h_alert_relation',array('type'=>'ASSESSOR','login_user_role'=>current($this->user['role_ids'])));
                        }
                        if($_REQUEST['ref']==1 && $ref_key!=''){
                            
                            $this->db->update('d_alerts',array('status'=>1,'ref_key'=>$ref_key),array('type'=>'CREATE_EXTERNAL_ASSESSOR',
                                'table_name'=>'d_user'));
                        }
//                        die;
                       // if(current($this->user['role_ids'])== 1 || current($this->user['role_ids'])== 8){
                            $userList = $this->userModel->getAQSTeamAssessor($param,current($this->user['role_ids']),$_REQUEST['ref'],$ref_key);
                            $objAssessment = new assessmentModel();
                            $assessorsCount = $objAssessment->getReviewCountByRole();
                            $this->set("assessorsCount",$assessorsCount);
                            $this->set('mailUser', $this->userModel->getMailRecievedUserlist());
                            $this->set('tapAssessorUser', $this->userModel->getTapAssessorUserList());
                            $this->set('userSubRoleList', $this->userModel->getuserSubRoleList());
                            $this->_template->addHeaderScript('d3.v2.js');
                        
                        //echo $this->userModel->getPageCount();
                        
                        
                        $this->set("pages",$this->userModel->getPageCount());
			$this->set("filterParam",$param);
			$this->set("users",$userList);
			
			$this->set("cPage",$cPage);

			$this->set("orderBy",$order_by);

			$this->set("orderType",$order_type);
			
		}else

			$this->_notPermitted=1;

	}
	

	function editUserAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

		elseif(!empty($_GET['id']) && $user=$this->userModel->getUserById($_GET['id'])){

			if(in_array("manage_all_users",$this->user['capabilities']) 

				|| $this->user['user_id']==$user['user_id'] 

				|| ($user['client_id']==$this->user['client_id'] && in_array("manage_own_users",$this->user['capabilities']))

				|| ($this->user['network_id']>0 && $user['network_id']==$this->user['network_id'] && in_array("manage_own_network_users",$this->user['capabilities']))){

				$this->set("roles",$this->userModel->getRoles());
				
				$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

				$this->set("eUser",$user);

			}else

				$this->_notPermitted=1;

		}else

			$this->_is404=1;

	}

	

	function createUserAction(){ 

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

		elseif( in_array("manage_all_users",$this->user['capabilities']) 

			|| in_array("manage_own_users",$this->user['capabilities'])

			|| ($this->user['network_id']>0 && in_array("manage_own_network_users",$this->user['capabilities'])) ){

			$clientModel=new clientModel();

			//$this->set("clients",$clientModel->getClients(array("max_rows"=>-1)));

			$this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);

			$this->set("roles",$this->userModel->getRoles());

		}else

			$this->_notPermitted=1;

	}

	

	function externalAssessorListAction(){

		if((in_array(6,$this->user['role_ids'])||in_array(5,$this->user['role_ids'])) && $this->user['has_view_video']!=1 && $this->user['is_web']==1 )//principal and school admin have to view video for self-review

			$this->_notPermitted=1;

		elseif(in_array("create_assessment",$this->user['capabilities'])){

			$cPage=empty($_POST['page'])?1:$_POST['page'];

			$order_by=empty($_POST['order_by'])?"name":$_POST['order_by'];

			$order_type=empty($_POST['order_type'])?"asc":$_POST['order_type'];

			$param=array(

					"page"=>$cPage,

					"name_like"=>empty($_POST['name'])?"":$_POST['name'],

					"client_id"=>empty($_POST['client_id'])?0:$_POST['client_id'],

					"client_like"=>empty($_POST['client'])?"":$_POST['client'],

					"role_id"=>4,

					"network_id"=>empty($_POST['network_id'])?0:$_POST['network_id'],

					"order_by"=>$order_by,

					"order_type"=>$order_type,

					);

			if(!in_array("manage_all_users",$this->user['capabilities'])){

				if(in_array("manage_own_network_users",$this->user['capabilities'])){

					$param["network_id"]=$this->user['network_id'];

					$param["exclude_cap"]=array("manage_all_users");

				}else{

					$param["client_id"]=$this->user['client_id'];

					$param["exclude_cap"]=array("manage_all_users","manage_own_network_users");

				}

			}

			$this->set("filterParam",$param);

			$this->set("users",$this->userModel->getUsers($param));

			

			$this->set("pages",$this->userModel->getPageCount());

			$this->set("cPage",$cPage);

			$this->set("orderBy",$order_by);

			$this->set("orderType",$order_type);

			

			$networkModel=new networkModel();

			$this->set("networks",$networkModel->getNetworkList(array("max_rows"=>-1)));

			

			$currentSelectionIds=empty($_POST['eAssessor'])?array():$_POST['eAssessor'];

			$currentSelection=$this->userModel->getUsernameForIds($currentSelectionIds);

			$this->set("currentSelection",$currentSelection);

			$this->set("currentSelectionIds",$currentSelectionIds);

		}else

			$this->_notPermitted=1;

	}
        
        // function for getting user details on1 13-05-2016 by Mohit Kumar
	function userProfileAction() {
//            print_r($_REQUEST);
//            die;
            if(isset($_REQUEST['process']) && $_REQUEST['process']=='invite' && !empty($_REQUEST['id'])){
                $user = $this->userModel->getInviteUserData($_REQUEST['id']);
                
                if(!empty($user)){
                    $this->set("eUser", $user);
                    $this->set('auser', array('role_ids'=>array(4)));
                } else
                    $this->_notPermitted = 1;
                
            } else {
                
                if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) &&
                    $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)
                {//principal and school admin have to view video for self-review
                    $this->_notPermitted = 1;
                } else if (!empty($_REQUEST['id']) && $user = $this->userModel->getUserProfileData($_REQUEST['id'],$_REQUEST['client_id'])) {
                    if (in_array("manage_all_users", $this->user['capabilities']) || $this->user['user_id'] == $user['user_id'] || 
                            ($user['client_id'] == $this->user['client_id'] && in_array("manage_own_users", $this->user['capabilities'])) || 
                            ($this->user['network_id'] > 0 && $user['network_id'] == $this->user['network_id'] && 
                            in_array("manage_own_network_users", $this->user['capabilities']))) 
                    {
                        // let take for testing country as India and country as 101
                        //echo "<pre>";print_r($user);die;
                        $country_id = 101;
                        $objAssessment = new assessmentModel();
                        $objUserModel = new userModel();
                        $userCount = $objAssessment->getReviewCountByRole($user['email']);
                        $objDianosticModel = new diagnosticModel();
                        $languageList = $objDianosticModel->getAllLanguages();
                        //$hobbiesAndIntrestList = $objUserModel->getHobbiesAndIntrestOptions($_REQUEST['id']);
                        $objClientModel = new clientModel();
                        $this->set("deseaseList",$this->userModel->getMedicalDesease());
                        $this->set("userlanguageList",$this->userModel->getUserLanguage($_REQUEST['id']));
                        $this->set("stateList",$objClientModel->getStateList($country_id));
                        $this->set("countryCodeList",$objClientModel->getCountryWithCode());
                        $this->set('languageList', $languageList);
                        $this->set('medicalOptionList', $objUserModel->getMedicalConditionOptions($_REQUEST['id']));
                        $this->set('hobbies',  $objUserModel->getHobbiesAndIntrestOptions($_REQUEST['id']));
                        $this->set("eUser", $user);
                        $this->set("userCount", $userCount);
                        $params = array();
                        if(!in_array(1,$this->user['role_ids']) && !in_array(2,$this->user['role_ids']) && !in_array(6,$this->user['role_ids']) )
                                $params = explode(",",$user['role_ids']);
                        $this->set("roles",$this->userModel->getRoles($params));
                        $this->set("user",$this->user);
                        $this->set('hobbiesList',  $this->userModel->getHobbiesList());
                        $this->set('introductoryAssessment',  $this->userModel->getAssessorIntroductoryAssessment($_REQUEST['id']));
                        
                    } else
                        $this->_notPermitted = 1;
                } else
                    $this->_is404 = 1;
            }
            $this->_template->addHeaderStyle('assessment-form.css');
            $this->_template->addHeaderScript('userprofile.js');
            //$this->_template->addHeaderScript('assessment.js');
        }
    
        // display message on 24-05-2016 by Mohit Kumar
        function messageAction(){
            if($_REQUEST['table']=='d_aqs_team' || $_REQUEST['table']=='d_AQS_team'){
                $message = "The request has been approved and an email has been sent successfully.";
            } else if($_REQUEST['table']=='d_user'){
                $SQL="Select email,name,client_id from d_user where user_id='".(trim($_REQUEST['id']))."'";
                $data = $this->db->get_row($SQL);
                if(!empty($data)){
                    $SQL1="Select id,send_email_status from h_aqs_team_invite_user where email='".$data['email']."'";
                    $result = $this->db->get_row($SQL1);
                    $status = true;
                    if(!empty($result)){
                        if($this->db->update('h_aqs_team_invite_user',array('send_email_status'=>1,'modification_date'=>date('Y-m-d H:i:s')),
                                array('email'=>$data['email'])))
                        {
                            $status = true;
                        } else {
                            $status = false;
                        }                        
                    } else if(empty ($result)){
                        if($this->db->insert('h_aqs_team_invite_user', array('aqs_team_user_id'=>$_REQUEST['id'],
                            'email'=>strtolower(trim($data['email'])),'send_email_status'=>1,'creation_date'=>date('Y-m-d H:i:s'),
                            'user_type_table'=>'d_user')))
                        {
                            $status = true;
                        } else {
                            $status = false;
                        }   
                    } else {
                        $status = false;
                    }
                    if($status==true){
                        $salt = "7j#jhf%gd76574rhjhfpMIs*%3";
                        $key = hash('sha512', $salt.$data['email']);
                        $create_date = date('Y-m-d');
                        $expiration_date = date('Y-m-d', strtotime('+1 day'));
                        $this->userModel->deleteResetUser(trim($_REQUEST['id']));
                        $this->userModel->createResetUser(trim($_REQUEST['id']),$key,$create_date,$expiration_date);
                        require ROOT . 'library' . DS .'phpmailer' .DS. "PHPMailerAutoload" . '.php';
                        //Create a new PHPMailer instance
                        $mail = new PHPMailer;

                        //Tell PHPMailer to use SMTP
                        $mail->isSMTP();
                        //Enable SMTP debugging
                        // 0 = off (for production use)
                        // 1 = client messages
                        // 2 = client and server messages
                        $mail->SMTPDebug = 0;
                        //Ask for HTML-friendly debug output
                        $mail->Debugoutput = 'html';
                        //Set the hostname of the mail server
                        $mail->Host = "omkara.freewaydns.net";
                        //Set the SMTP port number - likely to be 25, 465 or 587
                        $mail->Port = 465;
                        //Whether to use SMTP authentication
                        $mail->SMTPAuth = true;
                        //Username to use for SMTP authentication
                        $mail->SMTPSecure = 'ssl';
                        $mail->Username = "mail@algoinsighttest.com";
                        //Password to use for SMTP authentication
                        $mail->Password = "dNDT$*NQ4MXn";
        //                            $fromEmail = 'mohit.k@tatrasdata.com';
                        $fromName = 'Adhyayan Tap Admin';
                        $toEmail = $data['email'];
                        $toName = $data['name']; 

                        //Set who the message is to be sent from
                        $mail->setFrom('mohit.k@tatrasdata.com', $fromName);
                        //Set an alternative reply-to address
        //                            $mail->addReplyTo($fromEmail, $fromName);
                        //Set who the message is to be sent to
                        $mail->addAddress($toEmail, $toName);
        //                            $mail->AddBCC("mohit.k@tatrasdata.com", $toName);
                        //Set the subject line
                        $mail->Subject = 'Join the movement, become an Adhyayan Assessor!';
                        //Read an HTML message body from an external file, convert referenced images to embedded,
                        //convert HTML into a basic plain-text alternative body
        //                $message=' asdasdsa ';
        //                $mail->AltBody = $message;
    //                    $signup_link=SITEURL."index.php?controller=user&action=userProfile&id=".$_REQUEST['id']."&client_id=".$data['client_id'];
                        $signup_link=SITEURL.'?controller=web&action=reset&key='.$key."&process=assessor";
                        $video_link="https://www.youtube.com/watch?v=hIOgQy1DT_E";
                        $company_site='http://adhyayan.asia/site/the-assessor-programme/';
                        $mail->msgHTML("Dear $toName,
                                <br><br><br>Congratulations on completing the Adhyayan Quality Standard Review Programme.<br/><br>
                                We would like to extend an invitation to you and your leadership team (inc. supervisors, co-ordinators,
                                heads of departments), to engage further in Adhyayan's education movement by joining <b>The
                                Assessor Programme (TAP)</b>.<br/><br>
                                TAP is a network of school leaders that interact to engage in their continuous professional
                                development by showcasing talents and learning across the schooling spectrum.<br/><br>
                                To hear from Assessors about how TAP can support you, your peers, and your school, please
                                <a href='".$video_link."' target='_blank' title='Click here for watch video'>click here</a>. 
                                You may also visit our website by <a href='".$company_site."' target='_blank'>clicking here</a>.<br/><br>
                                <a href='".$signup_link."' target='_blank' title='Click here for login'>Click here to begin your journey</a>,
                                this will give you immediate access to the TAP WhatsApp Group, connecting you with school leaders
                                across the world.<br/><br>
                                We look forward to your continued engagement in enriching the quality of education across India
                                through The Assessor Programme.<br/><br/><br>
                                Warm wishes,<br/><br>
                                Amisha Modi<br/>
                                TAP Programme Lead
                                ");
                        if (!$mail->send()) {
                            $message = $mail->ErrorInfo;
                        } else {
                            $message = "Mail has been sent successfully.";
                        }
                    } else {
                        $message = "There is some unknown error.";
                    }
                } else {
                    $message = "There is some unknown error.";
                }
            }
            $this->set("message", $message);
        }
        
        function sendSignUpEmailAction(){
            if($_GET['id']!='' && ($_GET['table']=='d_aqs_team' || $_GET['table']=='d_AQS_team')){
                 $this->set("email",$this->userModel->getMailRecievedUserlist($_GET['id']));
            }
        }
        
        // function for update password changePassword on 25-07-2016 by Mohit Kumar
        function changePasswordAction() {
            if (!empty($_GET['id']) && $user = $this->userModel->getUserById($_GET['id'])) {
                $this->set("isPop",empty($_GET['ispop'])?0:$_GET['ispop']);
                $this->set("eUser", $user);
            } else
                $this->_is404 = 1;
        }
         /* 
         * Des:function to show my journey page
         * Params:user id
         * Owner:Deepak Thakur
         */
        function myJourneyAction(){
            
		if(isset($_REQUEST['process']) && $_REQUEST['process']=='invite' && !empty($_REQUEST['id'])){
                $user = $this->userModel->getInviteUserData($_REQUEST['id']);
                
                if(!empty($user)){
                    $this->set("eUser", $user);
                    $this->set('auser', array('role_ids'=>array(4)));
                } else
                    $this->_notPermitted = 1;
                
            } else {
                if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) &&
                    $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)
                {//principal and school admin have to view video for self-review
                    $this->_notPermitted = 1;
                } else if (!empty($_REQUEST['id']) && $user = $this->userModel->getUserProfileData($_REQUEST['id'],$_REQUEST['client_id'])) {
                    if (in_array("manage_all_users", $this->user['capabilities']) || $this->user['user_id'] == $user['user_id'] || 
                            ($user['client_id'] == $this->user['client_id'] && in_array("manage_own_users", $this->user['capabilities'])) || 
                            ($this->user['network_id'] > 0 && $user['network_id'] == $this->user['network_id'] && 
                            in_array("manage_own_network_users", $this->user['capabilities']))) 
                    {
                        // let take for testing country as India and country as 101
                        $country_id = 101;
                        $objAssessment = new assessmentModel();
                        $userCount = isset($user['email']) ?$objAssessment->getReviewCountByRoleNew($user['email']):0;
                        $userCount1 = isset($user['email']) ?$objAssessment->getReviewCountByRole($user['email']):0;
                        //echo"<pre>";
                        //print_r($userCount1);
                        //echo"</pre>";
                        $workshopCount = $objAssessment->getWorkshopCountByUserId($_REQUEST['id']);
                        //echo"<pre>"; print_r($workshopCount);die;
                        $objDianosticModel = new diagnosticModel();
                        $languageList = $objDianosticModel->getAllLanguages();
                        $objClientModel = new clientModel();
                        $userModel = new userModel();
                        $introductory_question = $objAssessment->getAssessorIntroductoryQuestions();
                        $introductory_question_option = $userModel->getAssessorIntroductoryOption($_REQUEST['id']);
                        //echo "<pre>";
                        //print_r($userModel->getAssessorIntroductoryOption($_REQUEST['id']));die;
                         $this->set("assessmentQuestionList",$this->createOption($introductory_question, 0));
                        $this->set("deseaseList",$this->userModel->getMedicalDesease());
                        $this->set("userlanguageList",$this->userModel->getUserLanguage($_REQUEST['id']));
                        $this->set("stateList",$objClientModel->getStateList($country_id));
                        $this->set('languageList', $languageList);
                        //$this->set('workshopList', $this->userModel->getWorkshopList());
                        $this->set("eUser", $user);
                        $this->set("userCount", $userCount);
                        $this->set("workshopCount", $workshopCount);
                        $this->set("roles",$this->userModel->getRoles());
                        $this->set("user",$this->user);
                        $this->set('hobbiesList',  $this->userModel->getHobbiesList());
                        $this->set('introductoryAnswer',  $userModel->getAssessorIntroductoryQuestionsAnswer($_REQUEST['id']));
                        $this->set('introductoryAssessment',  $this->userModel->getAssessorIntroductoryAssessment($_REQUEST['id']));
                        $this->set('introductoryAnswerOption',  $userModel->getAssessorIntroductoryOption($_REQUEST['id']));
                        
                    } else
                        $this->_notPermitted = 1;
                } else
                    $this->_is404 = 1;
            }
            $this->_template->addHeaderStyle('assessment-form.css');
            $this->_template->addHeaderScript('userprofile.js');
        }
        
        function createOption(array &$introductory_question, $parent_id = 0) {

            $branch = array();
            foreach ($introductory_question as $element) {
                if ($element['parent_id'] == $parent_id) {

                    //$directoryFile = $this->getDirectoryFiles($element['directory_id'], $fileList);
                    $children = $this->createOption($introductory_question, $element['q_id']);
                    if ($children) {
                        $element['child_question'] = $children;
                    }
                    $branch[$element['q_id']] = $element;
                }
            }
            return $branch;
        }
        
        // function to insert data in sub tables for user profile
        function getInsertUserProfileDataAction() {
            
            $objUser = new userModel();
            $objUser->syncData();
           
        }

}