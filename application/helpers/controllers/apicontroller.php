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
		if (! isset ( $_POST ['token'] ) || ! $this->user = $this->userModel->checkToken ( $_POST ['token'] )) {
			
			$this->apiResult ["message"] = "Token missing or expired. Please re-login";
			
			$this->apiResult ["status"] = - 1;
			
			exit ();
		}
	}
	
	function apiAction() {
		
	}
        
        public function getCurrentUserAction() {
            
            $this->apiResult['data'] = array(
                'email' => $this->user['email'],
                'name' => $this->user['name']
            );
            
            $this->apiResult ["status"] = 1;
        }
        
	function loginAction() {
		if (empty ( $_POST ['email'] ))
			
			$this->apiResult ["message"] = "Username field missing";
		
		else if (empty ( $_POST ['password'] ))
			
			$this->apiResult ["message"] = "Password field missing";
		
		else if ($res = $this->userModel->authenticateUser ( $_POST ['email'], $_POST ['password'] )) {
			
			if ($token = $this->userModel->generateToken ( $res ['user_id'], $_POST ['email'] )) {
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["token"] = $token;
				
				$this->apiResult ["message"] = "Successfully logged in";
			} else
				
				$this->apiResult ["message"] = "Unable to generate token, please try again.";
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
	function updateVideoAction() {
		$user_id = $this->user ['user_id'];
		
		$view = 1;
		
		$this->userModel->updateUserVideo ( $user_id, $view );
		
		$this->apiResult ["message"] = "success\n";
		
		$this->apiResult ["status"] = 1;
	}
	function getEditInternalAssessorsAction() {
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			
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
			) );
			
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
		} else if (!($internalRevPerc>0 ||$externalRevPerc>0) && empty ( $_POST ['diagnostic_id'] )) {
			
			$this->apiResult ["message"] = "Diagnostic cannot be empty.\n";
		} else if (empty ( $_POST ['tier_id'] )) {
			
			$this->apiResult ["message"] = "Tier cannot be empty.\n";
		} else if (empty ( $_POST ['award_scheme_name'] )) {
			
			$this->apiResult ["message"] = "Award Scheme cannot be empty.\n";
		} else if (!empty($_POST ['external_assessor_id']) && !empty($_POST ['internal_assessor_id']) && $_POST ['internal_assessor_id'] == $_POST ['external_assessor_id']) {			
			$this->apiResult ["message"] = "Internal and External reviewer cannot be same.\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['role'] ) && empty ( $_POST ['externalReviewTeam'] ['role'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {			
			$this->apiResult ["message"] = "External reviewer role cannot be empty.\n";
		} else if (isset ( $_POST ['externalReviewTeam'] ['member'] ) && empty ( $_POST ['externalReviewTeam'] ['member'] ) && count ( $_POST ['externalReviewTeam'] ['member'] ) != count ( $_POST ['externalReviewTeam'] ['role'] )) {			
			$this->apiResult ["message"] = "External reviewer member cannot be empty.\n";
		} 
                else if( !in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && (!empty ( $_POST ['externalReviewTeam'] ['clientId'] ) || !empty($_POST ['external_assessor_id']) ) ){
                    $this->apiResult ["message"] = "You are not authorized to update external review team.\n";
                }
                else {
			//&& in_array ( "assign_external_review_team", $this->user ['capabilities'] )
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
				
				// print_r($externalRoleClient);
			
			$externalReviewTeam = empty ( $_POST ['externalReviewTeam'] ['clientId'] ) ? '' : array_combine ( $_POST ['externalReviewTeam'] ['member'], $externalRoleClient );
			
			// $externalReviewTeam = array_combine($_POST['externalReviewTeam']['member'],$_POST['externalReviewTeam']['role']);
			
			// print_r($externalReviewTeam);die;
			
			$assessmentModel = new assessmentModel ();			
			$this->db->start_transaction ();
                        $existing_assessor_id = explode(',',$assessment ['user_ids']);//$externalRevPerc
			$external_assessor_id = empty($_POST ['external_assessor_id'])?$existing_assessor_id[1]:$_POST ['external_assessor_id'];//
                        $internal_assessor_id = empty($_POST ['internal_assessor_id'])?$existing_assessor_id[0]:$_POST ['internal_assessor_id'];
                        $diagnostic_id = empty($_POST ['diagnostic_id'])?$diagnostic_id:$_POST ['diagnostic_id'];
                        //in_array ( "assign_external_review_team", $this->user ['capabilities'] )
			if (!in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && $assessmentModel->updateSchoolAssessment ( $_POST ['assessment_id'], $internal_assessor_id, $external_assessor_id, $diagnostic_id, $_POST ['tier_id'], $_POST ['award_scheme_name'], 0 )) {				                           
                            $this->db->commit ();
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["message"] = "Review successfully updated";
			}
                        elseif ($assessmentModel->updateSchoolAssessment ( $_POST ['assessment_id'], $internal_assessor_id, $external_assessor_id, $diagnostic_id, $_POST ['tier_id'], $_POST ['award_scheme_name'], $externalReviewTeam )) {				
				$this->db->commit ();
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["message"] = "Review successfully updated";
			} else {
				$this->db->rollback();
				$this->apiResult ["message"] = "Unable to create review";
			}
		}
	}
	function createSchoolSelfAssessmentAction() {
		
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
			
			$clientId = $this->user ['client_id'];
			
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
			
			$this->db->start_transaction ();
			$isApproved = 0;
			if ($aid = $assessmentModel->createSchoolSelfAssessment ( $clientId, $_POST ['internal_assessor_id'], $_POST ['diagnostic_id'], $_POST ['tier_id'], $_POST ['award_scheme_name'], $reviewType,$isApproved )) {
				
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
				
				$this->apiResult ["message"] = "Review successfully created.";
			} else {
				
				$this->db->rollback ();
				
				$this->apiResult ["message"] = "Unable to create review.";
			}
		}
	}
	function editSchoolSelfAssessmentAction() {
		
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
		} else {
			
			// print_r($_POST);die;
			
			$clientId = $_POST ['client_id'];
			
			$assessmentId = $_POST ['assessment_id'];
			
			$reviewType = 1;
			
			$assessmentModel = new assessmentModel ();
			
			$this->db->start_transaction ();
			
			if ($assessmentModel->updateSchoolSelfAssessment ( $assessmentId, $clientId, $_POST ['internal_assessor_id'], $_POST ['diagnostic_id'], $_POST ['tier_id'], $_POST ['award_scheme_name'] )) {
				
				$subAssessmentType_id = 1; // self-review
				
				//$pastTransactions = $assessmentModel->getClientProducts ( $clientId );
				
				$this->db->commit ();
				
				$this->apiResult ["status"] = 1;
				
				// $this->apiResult["assessment_id"]=$aid;
				
				$this->apiResult ["message"] = "Review successfully updated.";
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
		} else if (empty ( $_POST ['name'] )) {
			
			$this->apiResult ["message"] = "Name cannot be empty\n";
		} else if (empty ( $_POST ['roles'] ) && in_array ( "manage_all_users", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "User Role cannot be empty\n";
		} else {
			
			$client_id = $_POST ['client_id'];
			$client_id_old = isset($_POST ['client_id_old']) ? $_POST ['client_id_old']:$_POST ['client_id'];
			$name = trim ( $_POST ['name'] );
			
			$user_id = empty ( $_POST ['id'] ) ? 0 : $_POST ['id'];
			
			if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $name ) != 1) {
				
				$this->apiResult ["message"] = "Invalid Characters are not allowed in Name.\n";
			} else if (isset ( $_POST ['roles'] )) 

			{
				
				$roles = $_POST ['roles'];
				
				$rolePrincipal = 6;
				
				// check if the user with school principal role already exists
				
				$allRoles = implode ( ",", $roles );
				
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
						
						if (! empty ( $usersRole6 )) 

						{
							if($client_id!=$client_id_old){
                                                        $this->apiResult ["message"] = "The principal already exists in the new school.\nPlease note that it will create a new user and all the reviews (if any) of current principle will get transfered to new user.\n";    
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
			
			$this->apiResult ["message"] = "School cannot be empty\n";
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
		} 

		else {
			
			$client_id = $_POST ['client_id'];
			
			$name = trim ( $_POST ['name'] );
			
			$email = strtolower ( trim ( $_POST ['email'] ) );
			
			// check role for tap admin on 12-05-2016 by Mohit Kumar
			
			if ($_POST ['roles'] [0] == 8 && ! empty ( $_POST ['roles'] )) {
				
				$roleId = $_POST ['roles'] [0];
			} else {
				
				$roleId = empty ( $_POST ['role_id'] ) ? 0 : $_POST ['role_id'];
			}
			
			$usersId = empty ( $_POST ['users_id'] ) ? 0 : $_POST ['users_id'];
			
			$usersId = explode ( ",", $usersId );
			
			// $userModel = new userModel();
			
			if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $name ) != 1) {
				
				$this->apiResult ["message"] = "Invalid Characters are not allowed in Name.\n";
			} else if (preg_match ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $email ) != 1) {
				
				$this->apiResult ["message"] = "Invalid email.\n";
			} else if ($this->userModel->getUserByEmail ( $email )) {
				
				$this->apiResult ["message"] = "Email already exists.\n";
			} else {
				
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
				} 

				else
					
					$roles = array (
							3 
					);
				
				$uid = $this->userModel->createUser ( $email, $_POST ['password'], $name, $_POST ['client_id'] );
				
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
				} else {
					
					$this->db->rollback ();
					
					$this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
				}
			}
		}
	}
	function updateUserAction() {
                $email_update = isset($_POST ['email'])? strtolower ( trim ( $_POST ['email'] ) ):'';
                $principal_user_row_new=$this->userModel->getPrincipal($_POST['client_id']);
                $principal_user_id_new=empty ( $principal_user_row_new ) ? 0:$principal_user_row_new['user_id'];
            
		if (empty ( $_POST ['name'] )) {
			
			$this->apiResult ["message"] = "Name cannot be empty\n";
		} else if (empty ( $_POST ['id'] )) {
			
			$this->apiResult ["message"] = "User ID missing\n";
		} else if (! empty ( $_POST ['password'] ) && strlen ( $_POST ['password'] ) < 6) {
			
			$this->apiResult ["message"] = "Password too short. Minimum 6 characters required.\n";
		} else if (empty ( $_POST ['email'] ) && isset($_POST ['email'])) {
			
			$this->apiResult ["message"] = "Email cannot be empty\n";
		} else if (preg_match ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $email_update ) != 1  && isset($_POST ['email'])) {
				
			$this->apiResult ["message"] = "Invalid email.\n";
		}else if($principal_user_id_new==$_POST ['id'] && !in_array(6,$_POST ['roles'])){
                        $this->apiResult ["message"] = "Not allowed to remove the Principal Role. Please first assign another user with the Principal Role\n";
                }else {
			
			$user_id = trim ( $_POST ['id'] );
			
			$user = $this->userModel->getUserById ( $user_id );
                        $client_id_old=$user['client_id'];
			
			$networkModel = new networkModel ();
			
			if (empty ( $user )) {
				
				$this->apiResult ["message"] = "User does not exist\n";
			}else if ($this->userModel->getUserByEmailExceptSelf ( $email_update,$user_id) && isset($_POST ['email'])) {
                            
			        $this->apiResult ["message"] = "Email already exists.\n";
		        }else if (! in_array ( 6, $user ['role_ids'] ) && empty ( $_POST ['roles'] ) && in_array ( "manage_all_users", $this->user ['capabilities'] )) {
				
				$this->apiResult ["message"] = "User Role cannot be empty\n";
			} else if (in_array ( "manage_all_users", $this->user ['capabilities'] ) || 

			$this->user ['user_id'] == $user ['user_id'] || 

			($user ['client_id'] == $this->user ['client_id'] && in_array ( "manage_own_users", $this->user ['capabilities'] )) || 

			($this->user ['network_id'] > 0 && $this->db->get_array_value ( "network_id", $networkModel->getNetworkByClientId ( $_POST ['client_id'] ) ) == $this->user ['network_id'] && in_array ( "manage_own_network_users", $this->user ['capabilities'] ))) {
				
				$name = trim ( $_POST ['name'] );
				
				if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $name ) != 1) {
					
					$this->apiResult ["message"] = "Invalid Characters are not allowed in Name.\n";
				} else {
					
					// $userModel = new userModel();
					
					// check role for tap admin on 12-05-2016 by Mohit Kumar
					
					if ($_POST ['roles'] [0] == 8 && ! empty ( $_POST ['roles'] )) {
						
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
                                        $oldRoles = $this->userModel->getUserRoles ( $user_id );
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
                                                            $new_principal_generated_id=$this->userModel->createrandomuser($client_id_old,$this->user['user_id'],$_POST['id']);
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
                                                            if($principal_user_id_old===null){   
                                                              //echo"Other-Principal Null";
                                                              $new_principal_generated_id=$this->userModel->createrandomuser($client_id_old,$this->user['user_id']);
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
					$userUpdated = $this->userModel->updateUserEmail ( $user_id, $name, $email_update ,$_POST ['password'],$_POST['client_id']);
                                        }else{
                                        $userUpdated = $this->userModel->updateUser ( $user_id, $name ,$_POST ['password']);    
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
					
					if ((in_array ( "manage_all_users", $this->user ['capabilities'] ) || in_array ( 6, $this->user ['role_ids'] )) && ! empty ( $_POST ['roles'] )) { // principal can update user role to school admin and internal reviewer
						
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
								
								$rolesUpdated = false;
							}
						} else {
							
							$currentRoles = $currentRoles != "" ? explode ( ",", $currentRoles ) : array ();
							
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
                                        $currentRoles_new = $this->userModel->getUserRoles ( $user_id );
                                        $add_user_history=true;
                                        if(!$this->userModel->add_user_history($user_id,$client_id_old,$_POST['client_id'],$oldRoles,$currentRoles_new,'Updated',$this->user['user_id'],date("Y-m-d H:i:s"))){
                                        $add_user_history=false;    
                                        }
                                        
					if ($userUpdated && ! $queryFailed && $rolesUpdated && $review_trasfer_status && $add_user_history && $this->db->commit ()) {
						
                                                if($new_principal_generated_id>0 && in_array ( "manage_own_users", $this->user ['capabilities'] ) && in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] )){
                                                $new_user_details=$this->userModel->getUserById($new_principal_generated_id);
                                                $body_mail="Dear Admin<br><br>User with Email Id: ".$new_user_details['email']." as Role of Principal for <b>".$new_user_details['client_name']."</b> is generated automatically."
                                                        . "<br>This is requested to modify the user<br><br>This is auto generated email, need not to reply<br><br>Thanks";
                                                sendEmail(''.$this->user['email'].'',''.$this->user['name'].'','shraddha.adhyayan@gmail.com','Shraddha Khedekar','Aadhayan:: Auto Generated User as Principal for '.$new_user_details['client_name'].'',$body_mail,'poonam.choksi@adhyayan.asia');
                                                }
                                                
                                                $this->apiResult ["status"] = 1;
						
						$this->apiResult ["message"] = "User successfully updated";
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
		} else if (empty ( $_POST ['client_name'] )) {
			
			$this->apiResult ["message"] = "Name of the school cannot be empty\n";
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
		} else if (! empty ( $_POST ['phone'] ) && strlen ( $_POST ['phone'] ) < 10) {
			
			$this->apiResult ["message"] = "Invalid phone number\n";
		} else {
			
			$cname = trim ( $_POST ['client_name'] );
			
			$pname = trim ( $_POST ['principal_name'] );
			
			$email = strtolower ( trim ( $_POST ['email'] ) );
			
			if (preg_match ( '/^[\.A-Za-z\s]+$/', $cname ) != 1) {
				
				$this->apiResult ["message"] = "Only Alphabets(a-z) and period(.) allowed in name of the school.\n";
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
				
				$cid = $clientModel->createClient ( $cname, $_POST ['street'], $_POST ['addrline2'], $_POST ['city'], $_POST ['state'], $_POST ['country'], $_POST ['phone'], $_POST ['remarks'], 0 );
				
				if (OFFLINE_STATUS == true) {
					// start---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
					$action_client_json = json_encode ( array (
							'client_name' => $cname,
							'street' => $_POST ['street'],
							'addressLine2' => $_POST ['addrline2'],
							'city_id' => $_POST ['city'],
							'state_id' => $_POST ['state'],
							'country_id' => $_POST ['country'],
							"principal_phone_no" => $_POST ['phone'],
							'remarks' => $_POST ['remarks'] 
					) );
					$this->db->saveHistoryData ( $cid, 'd_client', $clientUniqueID, 'addSchool', $cid, $email, $action_client_json, 0, date ( 'Y-m-d H:i:s' ) );
					// end---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
				}
				
				$uid = $this->userModel->createUser ( $email, $_POST ['password'], $pname, $cid );
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
					
					$this->apiResult ["message"] = "School successfully added";
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
			
			$this->apiResult ["message"] = "School ID missing\n";
		} else if (empty ( $_POST ['client_name'] )) {
			
			$this->apiResult ["message"] = "Name of the school cannot be empty\n";
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
		} else if (! empty ( $_POST ['phone'] ) && strlen ( $_POST ['phone'] ) < 10) {
			
			$this->apiResult ["message"] = "Invalid phone number\n";
		} else {
			
			$pname = trim ( $_POST ['principal_name'] );
			
			$cname = trim ( $_POST ['client_name'] );
			
			$clientModel = new clientModel ();
			
			$client_id = trim ( $_POST ['id'] );
			
			$client = $clientModel->getClientById ( $client_id );
			
			$principal = $this->userModel->getPrincipal ( $client_id );
			
			if (empty ( $client )) {
				
				$this->apiResult ["message"] = "School does not exist\n";
			} else if (preg_match ( '/^[\.A-Za-z\s]+$/', $cname ) != 1) {
				
				$this->apiResult ["message"] = "Only Alphabets(a-z) and period(.)  allowed in name of the school.\n";
			} else if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $pname ) != 1) {
				
				$this->apiResult ["message"] = "Invalid Characters are not allowed in Principal Name.\n";
			} else if (empty ( $principal ['user_id'] )) {
				
				$this->apiResult ["message"] = "Principal does not exist for this school.\n";
			} else {
				
				if (OFFLINE_STATUS == TRUE) {
					// start--> call function for creating unique id for add school on 08-03-2016 by Mohit Kumar
					$clientUniqueID = $this->db->createUniqueID ( 'editSchool' );
					// end--> call function for creating unique id for add school on 08-03-2016 by Mohit Kumar
				}
				
				$this->db->start_transaction ();
				
				$updated = $clientModel->updateClient ( $client_id, $cname, $_POST ['street'], $_POST ['addrline2'], $_POST ['city'], $_POST ['state'], $_POST ['country'], $_POST ['phone'], $_POST ['remarks'] );
				
				if (OFFLINE_STATUS == true) {
					// start---> call function for saving edit school client data on 08-03-2016 by Mohit Kumar
					$action_client_json = json_encode ( array (
							'client_name' => $cname,
							'street' => $_POST ['street'],
							'addressLine2' => $_POST ['addrline2'],
							'city_id' => $_POST ['city'],
							'state_id' => $_POST ['state'],
							'country_id' => $_POST ['country'],
							"principal_phone_no" => $_POST ['phone'],
							'remarks' => $_POST ['remarks'] 
					) );
					$this->db->saveHistoryData ( $client_id, 'd_client', $clientUniqueID, 'editSchool', $client_id, $principal ['user_id'], $action_client_json, 0, date ( 'Y-m-d H:i:s' ) );
					// end---> call function for saving edit school client data on 08-03-2016 by Mohit Kumar
				}
				
				if (! $this->userModel->updateUser ( $principal ['user_id'], $pname )) {
					
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
					
					$this->apiResult ["message"] = "School successfully updated";
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
				"cvs",
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
				
				$newName = str_replace ( " ", "_", substr ( implode ( "_", $nArr ), 0, 35 ) ) . "_" . rand ( 1, 9999 ) . "_" . time () . "." . $ext;
				
				if (@copy ( $_FILES ['file'] ['tmp_name'], ROOT . "uploads/" . $newName )) {
					
					$diagnosticModel = new diagnosticModel ();
					
					$id = $diagnosticModel->addUploadedFile ( $newName, $this->user ['user_id'] );
					
					if ($id > 0) {
						
						$this->apiResult ["message"] = "File successfully uploaded";
						
						$this->apiResult ["status"] = 1;
						
						$this->apiResult ["id"] = $id;
						
						$this->apiResult ["name"] = $newName;
						
						$this->apiResult ["ext"] = $ext;
						
						$this->apiResult ["url"] = SITEURL . "uploads/" . $newName;
					} else {
						
						$this->apiResult ["message"] = "Unable to make entry in database";
						
						@unlink ( ROOT . "uploads/" . $newName );
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
				
				$this->apiResult ["status"] = 1;
				
				// $this->apiResult["content"]=$diagnosticModel::getAssessorKeyNoteHtmlRow($_POST['kpa_id'],$akn_id,'',$type);
				$this->apiResult ["content"] = $diagnosticModel::getAssessorKeyNoteHtmlRow ( $instance_type_id, 'new', '', $type );
				
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
			
			$this->apiResult ["content"] = $assessmentModel->getExternalReviewTeamHTMLRow ( $_POST ['sn'] );
		}
	}
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
					
					$this->apiResult ["content"] = $assessmentModel->getTeacherInTeacherAssessmentHTMLRow ( $_POST ['attach'], $_POST ['sn'], '', '', '', '', 0, 0, 0, 1 );
					
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
	function saveAssessmentAction() {
		$diagnosticModel = new diagnosticModel ();
		
		if (empty ( $_POST ['assessment_id'] )) {
			
			$this->apiResult ["message"] = "Review id cannot be empty\n";
		} else if (empty ( $_POST ['assessor_id'] )) {
			
			$this->apiResult ["message"] = "Reviewer id cannot be empty\n";
		} else if ($assessment = $diagnosticModel->getAssessmentByUser ( $_POST ['assessment_id'], $_POST ['assessor_id'] )) {
			
			if ($assessment ['aqs_status'] != 1) {
				
				$this->apiResult ["message"] = "You are not authorized to fill review before School profile\n";
			} else if ($assessment ['report_published'] == 1) {
				
				$this->apiResult ["message"] = "You can't update data after publishing reports\n";
			} else if ($assessment ["status"] == 1 && ! in_array ( "edit_all_submitted_assessments", $this->user ['capabilities'] )) {
				
				$this->apiResult ["message"] = "You are not authorized to update review after submission\n";
			} else if ($assessment ["status"] == 0 && $assessment ["user_id"] != $this->user ['user_id']) {
				
				$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
			} else {
				
				$assessment_id = $_POST ['assessment_id'];
				
				$assessor_id = $_POST ['assessor_id'];
				
				$added_by = $this->user ['user_id'];
				
				$singleKpaId = 0; // empty($_POST['kpa_id'])?0:$_POST['kpa_id']; //if kpa id is given then we will check & save data for single kpa otherwise if it is 0 then we will check & save data for all kpas
				
				$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForAssessment ( $assessment_id, $assessor_id, $singleKpaId ), "kpa_instance_id" );
				
				$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForAssessment ( $assessment_id, $assessor_id, $singleKpaId ), "kpa_instance_id", "key_question_instance_id" );
				
				$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForAssessment ( $assessment_id, $assessor_id, $singleKpaId ), "key_question_instance_id", "core_question_instance_id" );
				
				$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForAssessment ( $assessment_id, $assessor_id, $singleKpaId ), "core_question_instance_id", "judgement_statement_instance_id" );
				
				$rScheme = $this->db->array_grouping ( $diagnosticModel->getDiagnosticRatingScheme ( $assessment ['diagnostic_id'] ), "type", "order" );
				
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
						$akns = $_POST ['aknotes'];
						if (isset ( $akns ) && count ( $akns )) {
							foreach ( $akns as $akn ) :
								if ($akn == 0)
									$isKNComplete = false;
							endforeach
							;
						}
						if ($isKNComplete)
							$noOfComplete ++;
						else
							$noOfIncomplete ++;
						
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
				
				if ($total > 0 && $success) {
					
					if ($noOfIncomplete == 0 && ! empty ( $_POST ['submit'] )) {
						
						$completedPerc = 100;
						
						if (! $diagnosticModel->updateAssessmentPercentageAndStatus ( $assessment_id, $assessor_id, $completedPerc, 1 )) {
							
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
						}
					} else if ($assessment ["status"] == 1 && $noOfIncomplete > 0) {
						
						$success = false;
						
						// $this->apiResult["message"] = "Some fields are empty. Kindly ensure all keynotes are filled";
						$this->apiResult ["message"] = "Some fields are empty.";
						
						return false;
					} else {
						
						$completedPerc = round ( (100 * $noOfComplete) / $total, 2 );
						
						if (! $diagnosticModel->updateAssessmentPercentage ( $assessment_id, $assessor_id, $completedPerc )) {
							
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
						}
					}
				} else {
					
					$success = false;
				}
				
				if ($success && $this->db->commit ()) {
					
					$this->apiResult ["status"] = 1;
					
					$this->apiResult ["completedPerc"] = $completedPerc;
					
					$this->apiResult ["submit"] = $assessment ["status"];
					
					$this->apiResult ["message"] = "Successfully saved";
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
			
			$additional_data = $aqsDataModel->getAqsAdditionalData ( $aqs ['id'], 'd_aqs_additional_questions' );
			$aqsAdditionalTeam = $aqsDataModel->getAqsAdditionalRefTeam ( $aqs ['id'] );
			
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
		$assessment = $diagnosticModel->getAssessmentByRole ( $_POST ['assessment_id'], 4 );
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
				
			$teachingStaff = $_POST ['number_teaching_staff'];
				
			// print_r($teachingStaff);
				
			$teachingStaff = array_filter ( $teachingStaff, function ($var) {
				if ($var > 0)
					return $var;
			} );
                        
                        foreach($_POST['kpa'] as $kpaKey=>$kpaval){
                            if(empty($kpaval) || empty($_POST['kq'][$kpaKey]) || empty($_POST['cq'][$kpaKey]) ||empty(trim($_POST['action_planning'][$kpaKey]))){
                                $this->apiResult["message"] = "Pleas fill all the fields in Action planning area chosen by school.";
                                return;
                            }
                             
                        }
					
				// print_r($teachingStaff);die;
					
				$prep_non_teaching = ! empty ( $_POST ['number_non_teaching_staff_prep'] ) ? $_POST ['number_non_teaching_staff_prep'] : '';
					
				$rest_non_teaching = ! empty ( $_POST ['number_non_teaching_staff_rest'] ) ? $_POST ['number_non_teaching_staff_rest'] : '';
					
				$incomplete = 0;
					
				$total = count ( $_POST );
					
				// $incomplete=count(empty($_POST));
					
				foreach ( $_POST as $field => $val )			
				{			
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
							"average_number_students_class" => $_POST ['average_number_students_class'],
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
			
							foreach ( $teachingStaff as $key => $val )
			
							{
									
								if (! $diagnosticModel->insertPostReviewTeachingStaff ( array (
										"staff_id" => $val,
										"school_level_id" => $key,
										"post_review_id" => $postReviewId
								) ))
			
									$failed = 2;
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
		} else if ($assessment = ($_POST ['assessment_type_id'] == 1 ? $diagnosticModel->getAssessmentById ( $_POST ['assmntId_or_grpAssmntId'] ) : $diagnosticModel->getGroupAssessmentByGAId ( $_POST ['assmntId_or_grpAssmntId'] ))) {
			
			$isSchoolAdmin = in_array ( "view_own_institute_assessment", $this->user ['capabilities'] ) && $assessment ['client_id'] == $this->user ['client_id'] ? 1 : 0;
			
			$assignedtoHim = 0;
			
			if ($_POST ['assessment_type_id'] == 1) {
				
				$assignedtoHim = $assessment ['userIdByRole'] [3] == $this->user ['user_id'] ? 1 : 0;
			} else {
				
				$isSchoolAdmin = $isSchoolAdmin && in_array ( 6, $this->user ['role_ids'] ) ? 1 : 0;
				
				$assignedtoHim = $assessment ['admin_user_id'] == $this->user ['user_id'] ? 1 : 0;
			}
			
			if ($_POST ['assessment_type_id'] == 1 && $assessment ['report_published'] == 1) {
				
				$this->apiResult ["message"] = "You can't update data after publishing reports\n";
			} else if ($_POST ['assessment_type_id'] > 1 && $assessment ['assessmentAssigned'] == 0) {
				
				$this->apiResult ["message"] = "You can't save AQS before assigning assessors\n";
			} else if (($assessment ['aqs_status'] == 0 && ($assignedtoHim || $isSchoolAdmin || 

			(in_array ( "view_own_network_assessment", $this->user ['capabilities'] ) && $assessment ['network_id'] == $this->user ['network_id'] && $this->user ['network_id'] > 0))

			) || ($assessment ['aqs_status'] == 1 && $isAdmin)) 

			{
				
				$aqsDataModel = new aqsDataModel ();
				
				$currentData = $aqsDataModel->getAqsData ( $_POST ['assmntId_or_grpAssmntId'], $_POST ['assessment_type_id'] );
				
				$submit = ! empty ( $_POST ['submit'] ) || $assessment ['aqs_status'] == 1 ? 1 : 0;
				
				$schoolLevels = $aqsDataModel->getSchoolLevelList ();
				
				$isSelfReview = ! empty ( $assessment ['subAssessmentType'] ) && $assessment ['subAssessmentType'] == 1 ? 1 : 0;
				
				if (! $isSelfReview && (empty ( $_POST ['aqs'] ) || ! is_array ( $_POST ['aqs'] ) || empty ( $_POST ['other'] ) || ! is_array ( $_POST ['other'] ) ||  empty ( $_POST ['schoolTeam'] ))) {
					
					$this->apiResult ["message"] = "Data missing\n";
					
					return;
				} 

				else if ($isSelfReview && (empty ( $_POST ['aqs'] ) || ! is_array ( $_POST ['aqs'] ) || empty ( $_POST ['schoolTeam'] ))) {
					
					$this->apiResult ["message"] = "Data missing\n";
					
					return;
				}
				
				// $aqsValidationRes = $isSelfReview==1? $aqsDataModel->aqsFormValidation($_POST['aqs'],array(),$schoolLevels,$submit,$isSelfReview) : $aqsDataModel->aqsFormValidation($_POST['aqs'],$_POST['other'],$schoolLevels,$submit,$isSelfReview);
				
				$other = empty ( $_POST ['other'] ) ? array () : $_POST ['other'];
				// for ashoka changemaker only
				$additional = empty ( $_POST ['additional'] ) ? array () : $_POST ['additional'];
				$additionalRefTeamValidation = empty ( $_POST ['additional_ref'] ) ? array () : $aqsDataModel->aqsAdditionalRefTeamValidation ( $_POST ['additional_ref'] );
				
				$aqsValidationRes = $aqsDataModel->aqsFormValidation ( $_POST ['aqs'], $other, $additional, $schoolLevels, $submit, $isSelfReview );
				
				$schoolTeamValidation = $aqsDataModel->aqsTeamValidation ( $_POST ['schoolTeam'], 1, $submit );
				
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
					
					foreach ( $_POST as $key => $val ) 

					{
						
						if (is_array ( $val )) 

						{
							
							switch ($key) 

							{
								
								case 'aqs' :
									foreach ( $val as $k => $v ) 

									{
										
										if (($k == 'referrer_text' && $_POST ['aqs'] ['referrer_id'] != 7) || ($k == 'distance_main_building' && $_POST ['aqs'] ['no_of_buildings'] == 1) || $k == 'contract_file_name' || $k == 'school_website' || $k == 'airport_name' || $k == 'rail_station_name' || $k == 'hotel_name' || $k == 'hotel_school_distance' || $k == 'hotel_airport_distance' || $k == 'hotel_station_distance') 

										{
											
											$noOfComplete ++;
											
											$total ++;
											
											continue;
										}
										
										if (empty ( $v ))
											
											$noOfIncomplete ++;
										
										else
											
											$noOfComplete ++;
										
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
										
										foreach ( $v as $key => $value ) 

										{
											
											if (empty ( $value ))
												
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
					
					// //
					
					if (OFFLINE_STATUS == TRUE) {
						// start---> call function for creating unique id for profile details for assessment on 11-03-2016 by Mohit Kumar
						$uniqueID = $this->db->createUniqueID ( 'assessmentAQSData' );
						// end---> call function for creating unique id for profile details for assessment on 11-03-2016 by Mohit Kumar
					}
					
					$this->db->start_transaction ();
					
					if (isset ( $currentData ['id'] )) {
						
						if ($aqsDataModel->updateAqsData ( $currentData ['id'], $aqsValidationRes ['values'] )) {
							
							$aqsId = $currentData ['id'];
							if (OFFLINE_STATUS == TRUE) {
								// start---> save the history for update aqs data into d_aqs_data table on 11-03-2016 By Mohit Kumar
								$action_assessment_aqs_json = json_encode ( $aqsValidationRes ['values'] );
								if (! $this->db->saveHistoryData ( $aqsId, 'd_aqs_data', $uniqueID, 'assessmentAQSDataUpdate', $aqsId, $aqsValidationRes ['values'] ['principal_email'], $action_assessment_aqs_json, 0, date ( 'Y-m-d H:i:s' ) )) {
									$failed = 79;
								}
								// end---> save the history for update aqs data into d_aqs_data table on 11-03-2016 By Mohit Kumar
							}
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
					
					foreach ( $schoolTeamValidation ['values'] as $tm )
						
						if (! (empty ( $tm ['name'] ) && empty ( $tm ['designation'] ) && empty ( $tm ['lang_id'] ) && empty ( $tm ['email'] ) && empty ( $tm ['mobile'] )) && ! $aqsDataModel->addAqsTeam ( $aqsId, $tm ['name'], $tm ['designation'], $tm ['lang_id'], $tm ['email'], $tm ['mobile'], 1 )) {
							
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
							// print_r($tm);
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
			if (! sendEmail($fromEmail,$fromName,$toEmail,$toName,$subject,$body,$ccEmail)) {												
				$this->apiResult ["message"] = "Error occured while sending email.";
			} else {
				
				$this->apiResult ["message"] = "A request has been sent for $reviewType. For more details, please contact Adhyayan administrator (info@adhyayan.asia).";
				
				$this->apiResult ["status"] = 1;
			}
		}
	}
	function getReportDataAction() {
		$assessment_id = empty ( $_POST ['assessment_id'] ) ? 0 : $_POST ['assessment_id'];
		
		$group_assessment_id = empty ( $_POST ['group_assessment_id'] ) ? 0 : $_POST ['group_assessment_id'];
		
		$repId = empty ( $_POST ['report_id'] ) ? 0 : $_POST ['report_id'];
		
		$diagnosticModel = new diagnosticModel ();
		
		if (! (in_array ( "view_all_assessments", $this->user ['capabilities'] ) || in_array ( "create_self_review", $this->user ['capabilities'] ) || in_array ( "take_external_assessment", $this->user ['capabilities'] ) || $repId == 5)) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if ($group_assessment_id == 0 && $assessment_id == 0) {
			
			$this->apiResult ["message"] = "Review id and Group review id both can not be empty\n";
		} else if (empty ( $_POST ['report_id'] )) {
			
			$this->apiResult ["message"] = "Report id cannot be empty\n";
		} else if ($report = $assessment_id > 0 ? $diagnosticModel->getReportsByAssessmentId ( $assessment_id ) : $diagnosticModel->getTeacherAssessmentReports ( $group_assessment_id )) {
			
			if (! empty ( $assessment ['statusByRole'] [4] ) && $assessment ['statusByRole'] [4] == 1 && in_array ( "take_external_assessment", $this->user ['capabilities'] )) 

			{
				
				$this->_notPermitted = 1;
				
				return;
			}
			
			if ($assessment_id > 0) {
				
				$report = isset ( $report [$_POST ['report_id']] ) ? $report [$_POST ['report_id']] : null;
			} else {
				
				$report = isset ( $report ['report_id'] ) && $report ['report_id'] == $_POST ['report_id'] ? $report : null;
			}
			
			if (! $report) {
				
				$this->apiResult ["message"] = "Wrong report id\n";
			} else if ($report ['aqs_status'] != 1) {
				
				$this->apiResult ["message"] = "Assessment not completed yet\n";
			} else if ($report ['isGenerated'] == 0 && empty ( $_POST ['years'] ) && empty ( $_POST ['months'] )) {
				
				$this->apiResult ["message"] = "Report not generated yet\n";
			} else {
				
				$years = empty ( $_POST ['years'] ) ? 0 : $_POST ['years'];
				
				$months = empty ( $_POST ['months'] ) ? 0 : $_POST ['months'];
				
				$tMonths = $months + ($years * 12);
				
				$conductedDate = date ( "m-Y" );
				
				$validDate = date ( "m-Y", strtotime ( "+$tMonths month" ) );
				
				$diagnostic_id = empty ( $_POST ['diagnostic_id'] ) ? 0 : $_POST ['diagnostic_id'];
				
				$subAssessmentType = ! empty ( $report ['subAssessmentType'] ) ? $report ['subAssessmentType'] : 0;
				
				$reportObject = $assessment_id > 0 ? new individualReport ( $assessment_id, $subAssessmentType, $_POST ['report_id'], $conductedDate, $validDate ) : new groupReport ( $group_assessment_id, $_POST ['report_id'], $diagnostic_id, $conductedDate, $validDate );
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["reportData"] = $reportObject->generateOutput ();
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
		} else if ($assessment = $_POST ['assessment_type_id'] == 1 ? $diagnosticModel->getAssessmentById ( $_POST ['ass_or_group_ass_id'] ) : $diagnosticModel->getTeacherAssessmentReports ( $_POST ['ass_or_group_ass_id'] )) {
			
			$assessment_id = $_POST ['assessment_type_id'] == 1 ? $_POST ['ass_or_group_ass_id'] : 0;
			
			$group_assessment_id = $_POST ['assessment_type_id'] == 1 ? 0 : $_POST ['ass_or_group_ass_id'];
			
			$reports = $assessment_id > 0 ? $diagnosticModel->getReportsByAssessmentId ( $_POST ['ass_or_group_ass_id'], false ) : $diagnosticModel->getSubAssReportsByGroupAssessmentId ( $group_assessment_id );
			
			$years = empty ( $_POST ['years'] ) ? 0 : $_POST ['years'];
			
			$months = empty ( $_POST ['months'] ) ? 0 : $_POST ['months'];
			
			$isPublished = $group_assessment_id > 0 ? (isset ( $reports [0] ['report_data'] [0] ) ? $reports [0] ['report_data'] [0] ['isPublished'] : 0) : $reports [0] ['isPublished'];
			
			if ($assessment ['aqs_status'] != 1) {
				
				$this->apiResult ["message"] = "School profile still not submitted\n";
			} else if ($assessment_id > 0 && $assessment ['statusByRole'] [4] != 1) {
				
				$this->apiResult ["message"] = "External review form still not submitted\n";
			} else if ($group_assessment_id > 0 && ($assessment ['allStatusFilled'] != 1 || $assessment ['allTchrInfoFilled'] != 1)) {
				
				$this->apiResult ["message"] = "Either all teacher info form still not submitted or all external review forms not submitted\n";
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
					
					if ($report ['isGenerated'] == 1) {
						
						$diagnosticModel->updateAssessmentReport ( $aid, $report ['report_id'], $years, $months, 1 );
						if ($_POST ['assessment_type_id'] == 1 && OFFLINE_STATUS == TRUE) {
							// save publish report history on local server on 01-04-2016 By Mohit Kumar
							$totalMonths = $months + ($years * 12);
							$data = json_encode ( array (
									"valid_until" => date ( "Y-m-d H:i:s", strtotime ( "+$totalMonths month" ) ),
									'publishDate' => date ( "Y-m-d H:i:s" ),
									'isPublished' => 1 
							) );
							$this->db->saveHistoryData ( $aid, 'h_assessment_report', $uniqueID, 'publishReportUpdate', $aid, $report ['report_id'], $data, 0, date ( 'Y-m-d H:i:s' ) );
							// end---> call function for saving add school client data on 04-03-2016 by Mohit Kumar
						}
					} else {
						
						$diagnosticModel->insertAssessmentReport ( $aid, $report ['report_id'], $years, $months, 1 );
						$assessmentReportId = $this->db->get_last_insert_id ();
						if ($_POST ['assessment_type_id'] == 1 && OFFLINE_STATUS == TRUE) {
							// save publish report history on local server on 01-04-2016 By Mohit Kumar
							$totalMonths = $months + ($years * 12);
							$data = json_encode ( array (
									"valid_until" => date ( "Y-m-d H:i:s", strtotime ( "+$totalMonths month" ) ),
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
				$reportsType = $diagnosticModel->getReportsType(2);//teacher
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
					if($var['report_id']==5)
						return array($var['assessment_id']=>$var['user_names'][0]);
				});				
				$reportsSingleTeacher = array_filter($reports,function($var){
					if($var['report_id']==7)
						return $var;
				});
					$groupAssessmentId = $group_assessment_id;
				$diagnosticsForGroup = (!empty($grs[0])?$grs[0]['diagnostic_ids']:0);
                                $res = $group_assessment_id>0?0:$diagnosticModel->getNumberOfKpasDiagnostic($assessment['diagnostic_id']);
				//print_r($res);
				$numKpas = $group_assessment_id>1?0:$res['num'];
				include (ROOT . 'application' . DS . 'views' . DS . "assessment" . DS . 'reportlist.php');
				
				$this->apiResult ["content"] = ob_get_contents ();
				
				ob_end_clean ();
			}
		} else {
			
			$this->apiResult ["message"] = "Wrong review id\n";
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
				
				$gaid = $assessmentModel->createTeacherAssessment ( $_POST ['client_id'], $_POST ['school_admin_id'] );
				
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
		} else {
			
			$diagnosticAssignedToTeacherCat = array ();
			
			foreach ( $_POST ['teacher_cat'] as $teacher_cat_id => $diagnostic_id ) {
				
				if ($diagnostic_id > 0) {
					
					$diagnosticAssignedToTeacherCat [$teacher_cat_id] = $diagnostic_id;
				}
			}
			
			if (count ( $diagnosticAssignedToTeacherCat ) > 0) {
				
				$gaid = $_POST ['gaid'];
				
				$this->db->start_transaction ();
				
				$assessmentModel = new assessmentModel ();
				
				$error = 0;
				
				if ($assessmentModel->updateTeacherAssessment ( $_POST ['client_id'], $_POST ['school_admin_id'], $gaid ) && $assessmentModel->removeAllExternalAssessorFromGroupAssessment ( $gaid ) && $assessmentModel->removeDignosticToGroupAssessment ( $gaid )) {
					
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
			} else {
				
				$this->apiResult ["message"] = "Please select diagnostic for atleast one teacher category\n";
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
								
								while ( ! feof ( $file ) ) 

								{
									
									$i ++;
									
									$fileData = fgetcsv ( $file, 0 );
									
									if ($i == 1 && ! empty ( $fileData [2] ) && preg_match ( '/^[A-Za-z0-9\._-]+@[A-Za-z0-9]+\.[A-Za-z]+$/', $fileData [2] ) != 1) {
										
										continue;
									}
									
									$this->apiResult ['result'] ["content"] .= assessmentModel::getTeacherAssessorHTMLRow ( 0, isset ( $fileData [0] ) ? $fileData [0] : '', isset ( $fileData [1] ) ? $fileData [1] : '', isset ( $fileData [2] ) ? $fileData [2] : '', isset ( $fileData [3] ) ? $fileData [3] : '', 1 );
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
								
								while ( ! feof ( $file ) ) 

								{
									
									$i ++;
									
									if ($i == 1 && ! empty ( $fileData [2] ) && preg_match ( '/^[A-Za-z0-9\._-]+@[A-Za-z0-9]+\.[A-Za-z]+$/', $fileData [2] ) != 1) {
										
										continue;
									}
									
									$fileData = fgetcsv ( $file, 0 );
									
									$this->apiResult ['result'] ["content"] .= $assessmentModel->getTeacherInTeacherAssessmentHTMLRow ( $_POST ['taid'], 0, isset ( $fileData [0] ) ? $fileData [0] : '', isset ( $fileData [1] ) ? $fileData [1] : '', isset ( $fileData [2] ) ? $fileData [2] : '', isset ( $fileData [3] ) ? $fileData [3] : '', 0, 0, 0, 1 );
								}
								
								fclose ( $file );
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
	function saveTeacherAssessorsFormAction() {
		$diagnosticModel = new diagnosticModel ();
		
		$teacherAssessment = null;
		
		$isAdmin = in_array ( "create_assessment", $this->user ['capabilities'] );
		
		if (empty ( $_POST ['taid'] ) || ! ($teacherAssessment = $diagnosticModel->getGroupAssessmentByGAId ( $_POST ['taid'] ))) {
			
			$this->apiResult ["message"] = "Teacher review id is either empty or invalid\n";
		} else if ($teacherAssessment ['assessmentAssigned'] > 0) {
			
			$this->apiResult ["message"] = "Reviews already assigned\n";
		} else if ($isAdmin || ($this->user ['client_id'] == $teacherAssessment ['client_id'] && ($teacherAssessment ['admin_user_id'] == $this->user ['user_id'] || in_array ( 6, $this->user ['role_ids'] ))) || ($this->user ['network_id'] == $teacherAssessment ['network_id'] && in_array ( "view_own_network_assessment", $this->user ['capabilities'] ))) {
			
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
					} else if (preg_match ( '/^[A-Za-z\s]+$/', $fn ) != 1) {
						
						$this->apiResult ["message"] = "Invalid or empty first name in row " . ($i + 1) . "\n";
						
						return;
					} else if (preg_match ( '/^[A-Za-z\s]+$/', $ln ) != 1) {
						
						$this->apiResult ["message"] = "Invalid or empty last name in row " . ($i + 1) . "\n";
						
						return;
					} else if ($em == "" && $jd == "") {
						
						$this->apiResult ["message"] = "Email and DoJ both can't be empty in row " . ($i + 1) . "\n";
						
						return;
					} else if ($em != "" && preg_match ( '^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^', $em ) != 1) {
						
						$this->apiResult ["message"] = "Invalid email in row " . ($i + 1) . "\n";
						
						return;
					} else if ($jd != "" && preg_match ( '/^(0[0-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-(19|20)[0-9]{2}$/', $jd ) != 1) {
						
						$this->apiResult ["message"] = "Invalid DoJ in row " . ($i + 1) . "\n";
						
						return;
					}
					
					$dArr = explode ( "-", $jd );
					
					if (count ( $dArr ) == 3 && mktime ( 0, 0, 0, $dArr [0], $dArr [1], $dArr [2] ) > $timeNow) {
						
						$this->apiResult ["message"] = "DoJ can't be greater than today in row " . ($i + 1) . "\n";
						
						return;
					}
					
					if ($em == "") {
						
						$em = str_replace ( "/", "", $jd ) . "@" . str_replace ( " ", "", $fn ) . "." . str_replace ( " ", "", $ln );
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
			
			$queryfailed = 0;
			
			foreach ( $associativeAssessors as $a ) {
				
				if (in_array ( $a ['user_id'], $eSelectedAssessorsIds )) {
					
					if ($isAdmin && $postedExtAssessors [$a ['user_id']] != $a ['original_status_id'] && ! $this->userModel->updateUser ( $a ['user_id'], $a ['name'], '', 0, $postedExtAssessors [$a ['user_id']] )) {
						
						$queryfailed = 1;
					}
				} else if (! $assessmentModel->removeExternalAssessorFromGroupAssessment ( $_POST ['taid'], $a ['user_id'] )) {
					
					$queryfailed = 5;
				}
			}
			
			if ($assessors ['count'] > 0) {
				
				foreach ( $assessors ['add'] as $na ) {
					
					$uid = $this->userModel->createUser ( $na ['em'], $na ['em'] . "@123", $na ['fn'] . " " . $na ['ln'], $teacherAssessment ['client_id'], $isAdmin ? 1 : 2 );
					
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
						
						if (! $this->userModel->updateUser ( $ea ['cur'] ['user_id'], $ea ['new'] ['fn'] . " " . $ea ['new'] ['ln'], '', 0, $status ) || ($ea ['new'] ['jd'] != '' && ! $assessmentModel->updateTeacherAttributeValue ( $ea ['cur'] ['user_id'], $ea ['new'] ['jd'], 'doj' ))) {
							
							$queryfailed = 20;
						}
					} else {
						
						if (! $this->userModel->updateUser ( $ea ['cur'] ['user_id'], $ea ['cur'] ['name'], '', 0, $status )) {
							
							$queryfailed = 25;
						}
					}
				}
			}
			
			if ($queryfailed == 0 && $this->db->commit ()) {
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["content"] = array (
						"1-1" => "",
						"1-0" => "",
						2 => "",
						3 => "" 
				);
				
				$assessors_group = $assessmentModel->getExternalAssessorsInGroupAssessment ( $_POST ['taid'] );
				
				foreach ( $assessors_group as $status_id => $assessors ) {
					
					foreach ( $assessors as $assessor ) {
						
						$this->apiResult ["content"] [$status_id . ($status_id == 1 ? '-' . $assessor ['added_by_admin'] : '')] .= userModel::getExternalAssessorNodeHtml ( $assessor, 1, $status_id );
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
		
		$teacherAssessment = null;
		
		if (empty ( $_POST ['taid'] ) || ! ($teacherAssessment = $diagnosticModel->getGroupAssessmentByGAId ( $_POST ['taid'] ))) {
			
			$this->apiResult ["message"] = "Teacher review id is either empty or invalid\n";
		} else if ($teacherAssessment ['assessmentAssigned'] > 0) {
			
			$this->apiResult ["message"] = "Assessments already assigned\n";
		} else if (($this->user ['client_id'] == $teacherAssessment ['client_id'] && ($teacherAssessment ['admin_user_id'] == $this->user ['user_id'] || in_array ( 6, $this->user ['role_ids'] ))) || ($this->user ['network_id'] == $teacherAssessment ['network_id'] && in_array ( "view_own_network_assessment", $this->user ['capabilities'] ))) {
			
			$assessorsNotApproved = array ();
			
			$assessmentModel = new assessmentModel ();
			
			$existingTeachers = $assessmentModel->getTeachersInTeacherAssessment ( $_POST ['taid'] );
			
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
						
						if (preg_match ( '/^[A-Za-z\s]+$/', $t ['first_name'] ) != 1) {
							
							$this->apiResult ["message"] = "Invalid or empty first name in row $c \n";
							
							return;
						} else if (preg_match ( '/^[A-Za-z\s]+$/', $t ['last_name'] ) != 1) {
							
							$this->apiResult ["message"] = "Invalid or empty last name in row $c \n";
							
							return;
						} else if ($t ['doj'] != "" && preg_match ( '/^(0[0-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-(19|20)[0-9]{2}$/', $t ['doj'] ) != 1) {
							
							$this->apiResult ["message"] = "Invalid DoJ in row $c \n";
							
							return;
						} else if (! in_array ( $t ['assessor_id'], $assessorIds )) {
							
							$this->apiResult ["message"] = "Please select an reviewer in row $c \n";
							
							return;
						} else if ($t ['assessor_id'] == $et ['teacher_id']) {
							
							$this->apiResult ["message"] = "Reviewer and teacher can't be same in row $c \n";
							
							return;
						} else if (! in_array ( $t ['cat_id'], $t_cat_ids )) {
							
							$this->apiResult ["message"] = "Please select a teacher category in row $c \n";
							
							return;
						}
						
						$dArr = explode ( "-", $t ['doj'] );
						
						if (count ( $dArr ) == 3 && mktime ( 0, 0, 0, $dArr [0], $dArr [1], $dArr [2] ) > $timeNow) {
							
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
					
					$nt ['assessor_id'] = isset ( $_POST ['teachers'] ['new'] ['assessor_id'] [$j] ) ? $_POST ['teachers'] ['new'] ['assessor_id'] [$j] : '';
					
					$nt ['cat_id'] = isset ( $_POST ['teachers'] ['new'] ['cat_id'] [$j] ) ? $_POST ['teachers'] ['new'] ['cat_id'] [$j] : '';
					
					if ($nt ['first_name'] == "" && $nt ['last_name'] == "" && $nt ['email'] == "" && $nt ['doj'] == "") {
						
						continue;
					} else if (preg_match ( '/^[A-Za-z\s]+$/', $nt ['first_name'] ) != 1) {
						
						$this->apiResult ["message"] = "Invalid or empty first name in row $i \n";
						
						return;
					} else if (preg_match ( '/^[A-Za-z\s]+$/', $nt ['last_name'] ) != 1) {
						
						$this->apiResult ["message"] = "Invalid or empty last name in row $i \n";
						
						return;
					} else if ($nt ['email'] == "" && $nt ['doj'] == "") {
						
						$this->apiResult ["message"] = "Email and DoJ both can't be empty in row $i \n";
						
						return;
					} else if ($nt ['email'] != "" && preg_match ( '^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^', $nt ['email'] ) != 1) {
						
						$this->apiResult ["message"] = "Invalid email in row $i \n";
						
						return;
					} else if ($nt ['doj'] != "" && preg_match ( '/^(0[0-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-(19|20)[0-9]{2}$/', $nt ['doj'] ) != 1) {
						
						$this->apiResult ["message"] = "Invalid DoJ in row $i \n";
						
						return;
					} else if (! in_array ( $nt ['assessor_id'], $assessorIds )) {
						
						$this->apiResult ["message"] = "Please select an reviewer in row $i \n";
						
						return;
					} else if (! in_array ( $nt ['cat_id'], $t_cat_ids )) {
						
						$this->apiResult ["message"] = "Please select a teacher category in row $i \n";
						
						return;
					}
					
					$dArr = explode ( "-", $nt ['doj'] );
					
					if (count ( $dArr ) == 3 && mktime ( 0, 0, 0, $dArr [0], $dArr [1], $dArr [2] ) > $timeNow) {
						
						$this->apiResult ["message"] = "DoJ can't be greater than today in row $i \n";
						
						return;
					}
					
					if ($t_assessor_list [$nt ['assessor_id']] ['status_id'] != 1) {
						
						$assessorsNotApproved [$i] = $nt ['assessor_id'];
					}
					
					if ($nt ['email'] == "") {
						
						$nt ['email'] = str_replace ( "/", "", $nt ['doj'] ) . "@" . str_replace ( " ", "", $nt ['first_name'] ) . "." . str_replace ( " ", "", $nt ['last_name'] );
					}
					
					$nt ['email'] = strtolower ( $nt ['email'] );
					
					if (in_array ( $nt ['email'], $emails )) {
						
						$this->apiResult ["message"] = "Email " . $nt ['email'] . " used twice\n";
						
						return;
					} else {
						
						$emails [] = $nt ['email'];
					}
					
					$u = $this->userModel->getUserByEmailWithTA ( $nt ['email'], $_POST ['taid'] );
					
					if (empty ( $u )) {
						
						$teachersToAdd [] = $nt;
					} else if ($nt ['assessor_id'] == $u ['user_id']) {
						
						$this->apiResult ["message"] = "Reviewer and teacher can't be same in row $i \n";
						
						return;
					} else if ($u ['client_id'] == $this->user ['client_id']) {
						
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
			
			$this->db->start_transaction ();
			
			$queryfailed = 0;
			
			foreach ( $teacherIdsToDelete as $id ) {
				
				if (! $assessmentModel->removeTeacherFromTeacherAssessment ( $_POST ['taid'], $id ))
					
					$queryfailed = 10;
			}
			
			foreach ( $teachersToAdd as $nt ) {
				
				$uid = $this->userModel->createUser ( $nt ['email'], $nt ['email'] . "@123", $nt ['first_name'] . " " . $nt ['last_name'], $teacherAssessment ['client_id'] );
				
				if (! $uid || ! $this->userModel->addUserRole ( $uid, 3 ) || ! $assessmentModel->addTeacherToTeacherAssessment ( $_POST ['taid'], $uid, $nt ['cat_id'], $nt ['assessor_id'] ) || ($nt ['doj'] != '' && ! $assessmentModel->addTeacherAttributeValue ( $uid, $nt ['doj'], 'doj' ))) {
					
					$queryfailed = 20;
				}
			}
			
			foreach ( $teachersToUpdate as $t ) {
				
				if ($t ['inGroup'] == 0 && ! $assessmentModel->addTeacherToTeacherAssessment ( $_POST ['taid'], $t ['teacher_id'], $t ['cat_id'], $t ['assessor_id'] )) {
					
					$queryfailed = 30;
				} else if ($t ['inGroup'] == 1 && ! $assessmentModel->updateTeacherInTeacherAssessment ( $_POST ['taid'], $t ['teacher_id'], $t ['cat_id'], $t ['assessor_id'] )) {
					
					$queryfailed = 35;
				}
				
				if (! $this->userModel->updateUser ( $t ['teacher_id'], $t ['first_name'] . " " . $t ['last_name'], '', 0 ) || ($t ['doj'] != '' && ! $assessmentModel->updateTeacherAttributeValue ( $t ['teacher_id'], $t ['doj'], 'doj' ))) {
					
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
					
					$this->apiResult ["content"] .= $assessmentModel->getTeacherInTeacherAssessmentHTMLRow ( $teacher ['group_assessment_id'], $i, $nm [0], isset ( $nm [1] ) ? $nm [1] : '', $teacher ['email'], $teacher ['doj'], $teacher ['teacher_category_id'], $teacher ['assessor_id'], $teacher ['teacher_id'], ! $submitted, ! $submitted );
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
					"experience" => "Total years of teaching experience",
					"joinning_year" => "School joining year",
					"position_when_joined" => "Position when joined the school",
					"no_of_promotions" => "No. of promotions since joining",
					"no_of_subjects_taught" => "No. of subjects taught",
					"no_of_classes_per_week" => "No. of classes taught per week" 
			);
			
			$complete = 1;
			
			$teacherDataIdToDelete = array_flip ( array_merge ( array_keys ( $teacherInfo ['supervisors'] ['value'] ), array_keys ( $teacherInfo ['other_roles'] ['value'] ) ) );
			
			$data = array ();
			
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
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['type'] )) {
			
			$this->apiResult ["message"] = "Diagnostic type can not be empty\n";
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
						if (@copy ( $_FILES ['dig_image'] ['tmp_name'], ROOT . "uploads/diagnostic/" . $newName )) {
							resizeImage ( ROOT . "uploads/diagnostic/" . $newName, '180', '60' );
							if ($diagnosticId == 0) {
								$imageId = $diagnosticModel->addUploadedFile ( $newName, $this->user ['user_id'] );
							} else {
								$resourceModel = new resourceModel ();
								
								$resourceModel->updateUploadedFile ( $newName, $imageId );
							}
							if ($imageId > 0) {
								$this->apiResult ["id"] = $imageId;
								$this->apiResult ["image_name"] = $image_name = $newName;
								$this->apiResult ["ext"] = $ext;
								$this->apiResult ["url"] = SITEURL . "uploads/diagnostic/" . $newName;
							} else {
								$file_error = TRUE;
								$imageId = NULL;
								$this->apiResult ["message"] = "Unable to make entry in database";
								@unlink ( ROOT . "uploads/diagnostic/" . $newName );
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
					
					$diagnosticId = $diagnosticModel->createDiagnostic ( $assessmentId, $diagnosticName, $isPublished, $userId, $imageId, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations );
					
					if (! $diagnosticId)
						$queryFailed = 1;
					
					if (! $diagnosticModel->createDiagnosticRatingScheme ( $diagnosticId, $assessmentId ))
						$queryFailed = 1;
					
					if ($assessmentId == 2) {
						
						if (! $diagnosticModel->createDiagnosticTeacherCategory ( $diagnosticId, $teacherCategoryId ))
							$queryFailed = 2;
					}
				} else {
					if ($image_name == '') {
						$dig_image = $diagnosticModel->getDiagnosticImage ( $diagnosticId );
						$image_name = $dig_image [0] ['file_name'];
						$imageId = $dig_image [0] ['diagnostic_image_id'];
					}
					if (! ($diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, $imageId, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations )))
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
					
					$kpasCreated = $diagnosticModel->createKpa ( $kpas );
					
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
					
					$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnostic ( $diagnosticId ), "kpa_instance_id" );
					
					$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnostic ( $diagnosticId ), "kpa_instance_id", "key_question_instance_id" );
					
					$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnostic ( $diagnosticId ), "key_question_instance_id", "core_question_instance_id" );
					
					$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnostic ( $diagnosticId ), "core_question_instance_id", "judgement_statement_instance_id" );
					
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
	
	// create or save key questions for diagnostic
	function saveDiagnosticKeyQuestionsAction() {
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['type'] )) {
			
			$this->apiResult ["message"] = "Diagnostic type can not be empty\n";
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
		} else {
			
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
			
			$this->db->start_transaction ();
			
			$queryFailed = 0;
			
			if (! ($diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, $imageId, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations )))
				
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
				
				$kpasCreated = $diagnosticModel->createKeyQuestions ( $kpas );
				
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
				
				$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnostic ( $diagnosticId ), "kpa_instance_id" );
				
				$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnostic ( $diagnosticId ), "kpa_instance_id", "key_question_instance_id" );
				
				$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnostic ( $diagnosticId ), "key_question_instance_id", "core_question_instance_id" );
				
				$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnostic ( $diagnosticId ), "core_question_instance_id", "judgement_statement_instance_id" );
				
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
	
	// create or save core questions for diagnostic
	function saveDiagnosticCoreQuestionsAction() {
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['type'] )) {
			
			$this->apiResult ["message"] = "Diagnostic type can not be empty\n";
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
			
			$this->db->start_transaction ();
			
			$queryFailed = 0;
			
			if (! ($diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, $imageId, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations )))
				
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
				
				$kpasCreated = $diagnosticModel->createCoreQuestions ( $kpas );
				
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
				
				$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnostic ( $diagnosticId ), "kpa_instance_id" );
				
				$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnostic ( $diagnosticId ), "kpa_instance_id", "key_question_instance_id" );
				
				$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnostic ( $diagnosticId ), "key_question_instance_id", "core_question_instance_id" );
				
				$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnostic ( $diagnosticId ), "core_question_instance_id", "judgement_statement_instance_id" );
				
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
	
	// create or save judgement statements for diagnostic
	function saveDiagnosticJudgementStatementsAction() {
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['type'] )) {
			
			$this->apiResult ["message"] = "Diagnostic type can not be empty\n";
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
			
			$this->db->start_transaction ();
			
			$queryFailed = 0;
			
			if (! ($diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, $imageId, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations )))
				
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
				
				$kpasCreated = $diagnosticModel->createJudgementStatements ( $kpas );
				
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
			
			$kpaInstanceDiagnostic = $diagnosticModel->getJSSIdsPerCqForDiagnostic ( $diagnosticId, $kpaId, $kqId, $cqId );
			
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
				
				$kpas = $this->db->array_col_to_key ( $diagnosticModel->getKpasForDiagnostic ( $diagnosticId ), "kpa_instance_id" );
				
				$kqs = $this->db->array_grouping ( $diagnosticModel->getKeyQuestionsForDiagnostic ( $diagnosticId ), "kpa_instance_id", "key_question_instance_id" );
				
				$cqs = $this->db->array_grouping ( $diagnosticModel->getCoreQuestionsForDiagnostic ( $diagnosticId ), "key_question_instance_id", "core_question_instance_id" );
				
				$jss = $this->db->array_grouping ( $diagnosticModel->getJudgementalStatementsForDiagnostic ( $diagnosticId ), "core_question_instance_id", "judgement_statement_instance_id" );
				
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
	function submitDiagnosticAction() {
		if (! in_array ( "manage_diagnostic", $this->user ['capabilities'] )) {
			
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['diagnosticName'] )) {
			
			$this->apiResult ["message"] = "Diagnostic Name can not be empty\n";
		} else if (empty ( $_POST ['diagnosticId'] )) {
			
			$this->apiResult ["message"] = "diagnosticId can not be empty\n";
		} else {
			
			$diagnosticModel = new diagnosticModel ();
			
			// $assessmentId = $_POST['assessmentId'];
			
			$diagnosticName = trim ( $_POST ['diagnosticName'] );
			
			$diagnosticId = empty ( $_POST ['diagnosticId'] ) ? 0 : $_POST ['diagnosticId'];
			$is_kpa_recommendations = empty ( $_POST ['kpa_recommendations'] ) ? 0 : $_POST ['kpa_recommendations'];
			$is_kq_recommendations = empty ( $_POST ['kq_recommendations'] ) ? 0 : $_POST ['kq_recommendations'];
			$is_cq_recommendations = empty ( $_POST ['cq_recommendations'] ) ? 0 : $_POST ['cq_recommendations'];
			$is_js_recommendations = empty ( $_POST ['js_recommendations'] ) ? 0 : $_POST ['js_recommendations'];
			
			// count kpas for diagnostic
			
			$kpa = $diagnosticModel->countKpaForDiagnostic ( $diagnosticId );
			
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
				
				$kq = $diagnosticModel->countKeyQuestionsForKpa ( $kpa_id );
				
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
					
					$cq = $diagnosticModel->countCoreQuestionsForKeyQuestion ( $kq_id );
					
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
						
						$jss = $diagnosticModel->countJudgementStatementsForeCoreQuestion ( $cq_id );
						
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
			
			if (! $diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, NULL, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations )) 

			{
				
				$this->apiResult ["message"] = "There was error in publishing diagnostic";
				
				return;
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
		} else if (empty ( $_POST ['diagnosticName'] )) {
			
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
			
			$teacherCategoryId = empty ( $_POST ['teacherCategoryId'] ) ? 0 : $_POST ['teacherCategoryId'];
			
			$isPublished = 0;
			
			if ($assessmentId == 2 && ! $teacherCategoryId) 

			{
				
				$this->apiResult ["message"] = "Diagnostic teacher type can not be empty\n";
				
				return;
			}
			
			if ($diagnosticId == 0) // if diagnostic doesnt exist create it

			{
				
				$this->db->start_transaction ();
				
				$userId = $this->user ['user_id'];
				
				$dId = $diagnosticModel->createDiagnostic ( $assessmentId, $diagnosticName, $isPublished, $userId, $dig_image_id, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations );
				
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
				
				if ($assessmentId == 2) 

				{
					
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
				
				if (! ($diagnosticModel->updateDiagnostic ( $diagnosticId, $diagnosticName, $isPublished, $dig_image_id, $is_kpa_recommendations, $is_kq_recommendations, $is_cq_recommendations, $is_js_recommendations )))
					
					return;
				
				$this->apiResult ["status"] = 1;
				
				$this->apiResult ["diagnosticId"] = $diagnosticId;
				
				$this->apiResult ["message"] = "Diagnostic has been updated successfully";
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
		if (empty ( $_POST ['first_name'] ) && empty ( $_POST ['last_name'] )) {
			$this->apiResult ["message"] = "First and Last Name cannot be empty\n";
		} else if (empty ( $_POST ['id'] )) {
			$this->apiResult ["message"] = "User ID missing\n";
		} else if (empty ( $_POST ['email'] )) {
			$this->apiResult ["message"] = "Email cannot be empty\n";
		} else {
			$user_id = trim ( $_POST ['id'] );
			if ($_POST ['table'] == 'd_aqs_team') {
				$user = array ();
			} else if ($_POST ['table'] == 'd_user') {
				$user = $this->userModel->getUserById ( $user_id );
			}
			if (empty ( $user ) && $_POST ['table'] == 'd_user') {
				$this->apiResult ["message"] = "User does not exist\n";
			} else {
				
				$name = trim ( $_POST ['first_name'] ) . " " . trim ( $_POST ['last_name'] );
				$checkUserId = $this->userModel->getUserIdByEmail ( strtolower ( trim ( $_POST ['email'] ) ) );
				if (preg_match ( '/^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/', $name ) != 1) {
					$this->apiResult ["message"] = "Invalid Characters are not allowed in Name.\n";
				} else if (preg_match ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", strtolower ( trim ( $_POST ['email'] ) ) ) == 0 && ($checkUserId == $_POST ['id'] || $checkUserId == false)) {
					$this->apiResult ["message"] = "Invalid email.\n";
				} else {
					
					if ($_REQUEST ['process'] == '') {
						if (! empty ( $_POST ['data'] )) {
							$_POST ['upload_document'] = implode ( ',', $_POST ['data'] ['undefined-undefined-undefined-undefined'] ['files'] );
						} else if (! empty ( $_POST ['files'] )) {
							$_POST ['upload_document'] = implode ( ',', $_POST ['files'] );
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
							$postData = $this->userModel->userProfileValidation ( $_POST );
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
								$user_id = $this->userModel->createUser ( strtolower ( trim ( $_POST ['email'] ) ), $_POST ['password'], $name, $_POST ['client_id'] );
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
								$userUpdated = $this->userModel->updateUser ( $user_id, $name, $_POST ['password'] );
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
	
	// FUNCTION for getting for getting new alert counts
	public function getAlertCountAction() {
		$count = $this->db->getAlertCount ();
		$this->apiResult ["status"] = 1;
		$this->apiResult ["totalCount"] = $count ['assessor_count'] + $count ['review_count'];
		$this->apiResult ["assessorCount"] = $count ['assessor_count'];
		$this->apiResult ["reviewCount"] = $count ['review_count'];
	}
        
	// new function for creating school review according to tap admin functionality on 06-06-2016 by Mohit Kumar
	function createSchoolAssessmentNewAction() {
		if (! in_array ( "create_assessment", $this->user ['capabilities'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
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
                } 
                else if( in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && empty ( $_POST ['external_assessor_id'])){
                    $this->apiResult ["message"] = "You are not authorized to update external review team.\n";                        
		} 
                else if( !in_array ( "assign_external_review_team", $this->user ['capabilities'] ) && ! empty ( $_POST ['externalReviewTeam'] ['clientId'] )){
                    $this->apiResult ["message"] = "You are not authorized to update external review team.\n";                        
		} 
                else {
			$externalRoleClient = array ();
			$assessmentModel = new assessmentModel ();
                        $i=0;
                        if (! empty ( $_POST ['externalReviewTeam'] ['clientId'] ) && in_array ( "assign_external_review_team", $this->user ['capabilities'] ) )
				
				foreach ( $_POST ['externalReviewTeam'] ['clientId'] as $client ) 
				{					
					array_push ( $externalRoleClient, $client . '_' . $_POST ['externalReviewTeam'] ['role'] [$i] );					
					$i ++;
				}				
				// print_r($externalRoleClient);
			
			$externalReviewTeam = empty ( $_POST ['externalReviewTeam'] ['clientId'] ) ? '' : array_combine ( $_POST ['externalReviewTeam'] ['member'], $externalRoleClient );
			$this->db->start_transaction();
                        if ($aid = $assessmentModel->createSchoolAssessmentNew ( $_POST ['client_id'], $_POST ['internal_assessor_id'], $_POST ['diagnostic_id'], $_POST ['tier_id'], $_POST ['award_scheme_name'],$_POST ['external_assessor_id'],$externalReviewTeam)) {
				$this->db->addAlerts ( 'd_assessment', $aid, $_POST ['client_id'], $aid, 'CREATE_REVIEW' );
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
               // print_r($_POST);die;
		$resource_title = isset ( $_POST ['resource_title'] ) ? trim ( $_POST ['resource_title'] ) : '';
                $province = empty ( $_POST ['province'] ) ? 0 : $_POST ['province'];
                $school = empty ( $_POST ['school'] ) ? 0 : $_POST ['school'];
                $network = empty ( $_POST ['network'] ) ? 0 : $_POST ['network'];
                $rec_user = empty ( $_POST ['rec_user'] ) ? 0 : $_POST ['rec_user'];
                $network_option = empty ( $_POST ['school_related_to'] ) ? 0 : $_POST ['school_related_to'];
               //print_r($_POST ['network']);
		if (empty ( $resource_title )) {
			$this->apiResult ["message"] = "Title of the resource cannot be empty\n";
		} else if (empty ( $_FILES ['file'] )) {
			$this->apiResult ["message"] = "No file uploaded with this request\n";
		} else if ($_FILES ['file'] ['error'] > 0) {
			$this->apiResult ["message"] = "File contains error or error occurred while uploading\n";
		} else if (! ($_FILES ['file'] ['size'] > 0)) {
			$this->apiResult ["message"] = "Invalid file size or empty file\n";
		} else if ($_FILES ['file'] ['size'] > $maxUploadFileSize) {
			$this->apiResult ["message"] = "File too big\n";
		} else if (empty ( $_POST ['roles'] )) {
			$this->apiResult ["message"] = "User Role cannot be empty\n";
		} else if (! in_array ( "upload_resources", $this->user ['capabilities'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $_POST ['directory'] )) {
			$this->apiResult ["message"] = "Resource directory cannot be empty\n";
		}else if ($network_option == 1 && empty($network)) {
			$this->apiResult ["message"] = "Networks cannot be empty\n";
		}else if (empty($school)) {
			$this->apiResult ["message"] = "Schools cannot be empty\n";
		}else if (empty($rec_user)) {
			$this->apiResult ["message"] = "Users cannot be empty\n";
		}else {
			$resUploadResult = $this->uploadResourceFileAction ();
			if (! empty ( $resUploadResult )) {
				if (isset ( $resUploadResult ['id'] ) && $resUploadResult ['id'] > 0) {
					
					$resource_description = trim ( $_POST ['resource_description'] );
					$file_id = $resUploadResult ['id'];
					$resource_uploaded_by = $this->user ['user_id'];
                                        $directory_id = trim($_POST ['directory']);
					$roles_string = implode ( ',', $_POST ['roles'] );
					
					if ($_POST ['roles'] [0] == 8 && ! empty ( $_POST ['roles'] )) {
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
//                                       / print_r($resourceExistStatus);
                                        if(count($resourceExistStatus)< 1) {
                                                    $this->db->start_transaction ();
                                                    $list = $resourceModel->getResourceParentDirectoryList();
                                                    $parents = rtrim($this->getParentDirectory($list,$directory_id,''),'/');
                                                    $resource_id = $resourceModel->createResource ( $resource_title, $resource_description, $resource_uploaded_by, $file_id, $roles_string,$parents,$directory_id,$network_option);
                                                if($resource_id > 0 ){
                                                    $res_network_result = $resourceModel->createResourceUsers('',$province,$school,$rec_user,$network,'',$resource_id,'' );
                                                   // createResourceUsers($resresult,$province,$school,$rec_user,$network,$from='',$resource_id='' ,$network_option='')
                                                }
                                                if ($resource_id > 0  && $res_network_result > 0 && $this->db->commit ()) {
                                                        $this->apiResult ["status"] = 1;
                                                        $this->apiResult ["message"] = "Resource successfully added";
                                                } else {
                                                        $this->db->rollback ();
                                                        $this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
                                                }
                                        }else{
                                            $this->apiResult ["status"] = 0;
                                            $this->apiResult ["message"] = "Resource title already exist.\n";
                                        }
					// }
				} else {
					if ($resUploadResult ['message'] != '') {
						$this->apiResult ["message"] = $resUploadResult ['message'];
					} else {
						$this->apiResult ["message"] = "Unable to make entry in database";
					}
				}
			} else {
				$this->apiResult ["message"] = "No file uploaded with this request\n";
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
		$maxUploadFileSize = 104857600; // in bytes
		$resource_title = isset ( $_POST ['resource_title'] ) ? trim ( $_POST ['resource_title'] ) : '';
		$directory_id  = isset ( $_POST ['directory'] ) ? trim ( $_POST ['directory'] ) : '';
                $province = empty ( $_POST ['province'] ) ? 0 : $_POST ['province'];
                $school = empty ( $_POST ['school'] ) ? 0 : $_POST ['school'];
                $network = empty ( $_POST ['network'] ) ? 0 : $_POST ['network'];
                $rec_user = empty ( $_POST ['rec_user'] ) ? 0 : $_POST ['rec_user'];
                $network_option = empty ( $_POST ['network_option'] ) ? 0 : $_POST ['network_option'];
		if (empty ( $_POST ['resource_file_id'] ) || empty ( $_POST ['resource_id'] ) || empty ( $_POST ['file_id'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else if (empty ( $resource_title )) {
			$this->apiResult ["message"] = "Title of the resource cannot be empty\n";
		} else if (empty ( $_POST ['roles'] )) {
			$this->apiResult ["message"] = "User Role cannot be empty\n";
		} else if (empty ( $_POST ['directory'] )) {
			$this->apiResult ["message"] = "Resource directory cannot be empty\n";
		}else if (empty($school)) {
			$this->apiResult ["message"] = "Schools cannot be empty\n";
		}else if (empty($rec_user)) {
			$this->apiResult ["message"] = "Users cannot be empty\n";
		}		// else if (empty($_FILES['file'])) {
		// $this->apiResult["message"] = "No file uploaded with this request\n";
		// }
		else if (! in_array ( "upload_resources", $this->user ['capabilities'] )) {
			$this->apiResult ["message"] = "You are not authorized to perform this task.\n";
		} else {
			if (! empty ( $_FILES )) {
				if (empty ( $_FILES ['resource_file'] )) {
					$this->apiResult ["message"] = "No file uploaded with this request\n";
				} else if ($_FILES ['resource_file'] ['error'] > 0) {
					$this->apiResult ["message"] = "File contains error or error occurred while uploading\n";
				} else if (! ($_FILES ['resource_file'] ['size'] > 0)) {
					$this->apiResult ["message"] = "Invalid file size or empty file\n";
				} else if ($_FILES ['resource_file'] ['size'] > $maxUploadFileSize) {
					$this->apiResult ["message"] = "File too big\n";
				} else {
					$resUploadResult = $this->updateResourceFileAction ( $_POST ['file_id'] );
					if (! empty ( $resUploadResult )) {
						if (isset ( $resUploadResult ['id'] ) && $resUploadResult ['id'] > 0) {
							$file_id = $resUploadResult ['id'];
						} else {
							$this->apiResult ["message"] = "No file uploaded with this request\n";
						}
					}
				}
			}
			
			$resource_description = trim ( $_POST ['resource_description'] );
			$resource_uploaded_by = $this->user ['user_id'];
			$roles_string = implode ( ',', $_POST ['roles'] );
			
			if ($_POST ['roles'] [0] == 8 && ! empty ( $_POST ['roles'] )) {
				$roleId = $_POST ['roles'] [0];
			} else {
				$roleId = empty ( $_POST ['role_id'] ) ? 0 : $_POST ['role_id'];
			}
			$resourceModel = new resourceModel ();
                        //$resourceModel = new resourceModel ();
                        $list = $resourceModel->getResourceParentDirectoryList();
                        $parents = rtrim($this->getParentDirectory($list, $directory_id, ''), '/');
			$d_resources_data = array (
					'resource_title' => $resource_title,
					'resource_description' => $resource_description,
					'resource_updated_by' => $this->user ['user_id'],
					'resource_modified_at' => date ( 'Y-m-d H:i:s' ) ,
					'parents_directory_path' => $parents ,
					'directory_id' => $directory_id ,
					'network_option' => $network_option 
			);
			$h_resource_file_data = array (
					'user_role_id' => $roles_string,
					'modified_at' => date ( 'Y-m-d H:i:s' ) 
			);
			$this->db->start_transaction ();
                        $resresult = $resourceModel->updateResource ( $h_resource_file_data, $d_resources_data, $_POST ['resource_file_id'], $_POST ['resource_id'] );
                        $res_network_result = $resourceModel->createResourceUsers($resresult, $province, $school, $rec_user, $network,1,$_POST ['resource_id'],$network_option);
			if ($resresult && $res_network_result && $this->db->commit ()) {
				// $this->set("resource_detail", 'hkjdfghdjhfdj');
				$this->apiResult ["status"] = 1;
				$this->apiResult ["message"] = "Resource successfully updated";
			} else {
				 $this->db->rollback();
				$this->apiResult ["message"] = "Error occurred, please check the error logs.\n";
			}
		}
	}
	function uploadResourceFileAction() {
		$maxUploadFileSize = 104857600; // in bytes
		$allowedExt = array (
				"jpeg",
				"png",
				"gif",
				"jpg",
				"doc",
				"docx",
				"txt",
				"xls",
				"xlsx",
				"pdf",
				'pptx',
				'ppt' 
		);
		
		$nArr = explode ( ".", $_FILES ['file'] ['name'] );
		$ext = strtolower ( array_pop ( $nArr ) );
		if (in_array ( $ext, $allowedExt ) && count ( $nArr ) > 0) {
			$newName = str_replace ( " ", "_", substr ( implode ( "_", $nArr ), 0, 35 ) ) . "_" . rand ( 1, 9999 ) . "_" . time () . "." . $ext;
			if (@copy ( $_FILES ['file'] ['tmp_name'], ROOT . "uploads/resources/" . $newName )) {
				$diagnosticModel = new diagnosticModel ();
				$id = $diagnosticModel->addUploadedFile ( $newName, $this->user ['user_id'] );
				if ($id > 0) {
					$this->apiResult ["message"] = "Resource file successfully uploaded";
					$this->apiResult ["status"] = 1;
					$this->apiResult ["id"] = $id;
					$this->apiResult ["name"] = $newName;
					$this->apiResult ["ext"] = $ext;
					$this->apiResult ["url"] = SITEURL . "uploads/resources/" . $newName;
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
		
		return $this->apiResult;
		// }
	}
	function updateResourceFileAction($file_id = '') {
		if ($file_id != '') {
			$maxUploadFileSize = 104857600; // in bytes
			$allowedExt = array (
					"jpeg",
					"png",
					"gif",
					"jpg",
					"doc",
					"docx",
					"txt",
					"xls",
					"xlsx",
					"pdf",
					'pptx',
					'ppt' 
			);
			
			$nArr = explode ( ".", $_FILES ['resource_file'] ['name'] );
			$ext = strtolower ( array_pop ( $nArr ) );
			if (in_array ( $ext, $allowedExt ) && count ( $nArr ) > 0) {
				$newName = str_replace ( " ", "_", substr ( implode ( "_", $nArr ), 0, 35 ) ) . "_" . rand ( 1, 9999 ) . "_" . time () . "." . $ext;
				
				if (copy ( $_FILES ['resource_file'] ['tmp_name'], ROOT . 'uploads/resources/' . $newName )) {
					$resourceModel = new resourceModel ();
					
					if ($resourceModel->updateUploadedFile ( $newName, $file_id )) {
						$this->apiResult ["message"] = "Resource file successfully uploaded";
						$this->apiResult ["status"] = 1;
						$this->apiResult ["id"] = $file_id;
						$this->apiResult ["name"] = $newName;
						$this->apiResult ["ext"] = $ext;
						$this->apiResult ["url"] = SITEURL . "uploads/resources/" . $newName;
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
												$user_profile_data ['user_id'] = $user_id = $this->userModel->createUser ( $user_data_row ['email'], $password = '', $name, $client_id );
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
		$diagnosticModel = new diagnosticModel ();
		if (empty ( $_POST ['assessment_id'] )) {
			$this->apiResult ["message"] = "Review id cannot be empty\n";
		} else if (empty ( $_POST ['level_type'] )) {
			$this->apiResult ["message"] = "Level type  cannot be empty\n";
		} else if (empty ( $_POST ['instance_id'] )) {
			$this->apiResult ["message"] = "Instance Id  cannot be empty\n";
		} else if ($assessment = $diagnosticModel->getAssessmentByRole ( $_POST ['assessment_id'], 4 )) {
			$report = $diagnosticModel->getAssessmentByUser($_POST ['assessment_id'],$assessment ["user_id"]);
			if ($assessment ['aqs_status'] != 1) {
				
				$this->apiResult ["message"] = "You are not authorized to fill review before School profile\n";
			} else if ($report ['report_published'] == 1) {
				
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
						if ($akn_id = $diagnosticModel->addAssessorKeyNote ( $_POST ['assessment_id'], $_POST ['level_type'], $instance_type_id, $imp, 'recommendation' ))
							$_POST ['data'] ['recommendation'] [$instance_type_id] [$akn_id] = $imp;
						else
							$success = false;
						unset ( $_POST ['data'] ['recommendation'] [$instance_type_id] [$key] );
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
								
								if ($aknText != $akn ['text_data']) {
									
									if (! $diagnosticModel->updateAssessorKeyNote ( $akn_id, $aknText )) {
										
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
									
									if ($aknText != $akn ['text_data']) {
										
										if (! $diagnosticModel->updateAssessorKeyNote ( $akn_id, $aknText, 'recommendation' )) {
											
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
					if ($reportModel->isExistingRecommendation ( $group_assessment_id, $diagnostic_id, 'kpa', $kpa_instance_id ) != 0) {
						! $reportModel->updateTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'kpa', $kpa_instance_id, $kpa_recommendations ) ? $this->db->rollback () && $failed = 1 : '';
					} elseif (! $reportModel->saveTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'kpa', $kpa_instance_id, $kpa_recommendations ))
						$this->db->rollback () && $failed = 2;
				}
			}
			if (! empty ( $_POST ['recommendations_kq'] )) {
				$kqs = $_POST ['recommendations_kq'];
				foreach ( $kqs as $k => $kq ) {
					$kq_instance_id = $k;
					$kq_recommendations = implode ( '~', $_POST ['recommendations_kq'] [$kq_instance_id] );
					if ($reportModel->isExistingRecommendation ( $group_assessment_id, $diagnostic_id, 'key_question', $kq_instance_id ) != 0) {
						! $reportModel->updateTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'key_question', $kq_instance_id, $kq_recommendations ) ? $this->db->rollback () && $failed = 1 : '';
					} elseif (! $reportModel->saveTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'key_question', $kq_instance_id, $kq_recommendations ))
						$this->db->rollback () && $failed = 2;
				}
			}
			if (! empty ( $_POST ['recommendations_cq'] )) {
				$cqs = $_POST ['recommendations_cq'];
				foreach ( $cqs as $k => $cq ) {
					$cq_instance_id = $k;
					$cq_recommendations = implode ( '~', $_POST ['recommendations_cq'] [$cq_instance_id] );
					if ($reportModel->isExistingRecommendation ( $group_assessment_id, $diagnostic_id, 'core_question', $cq_instance_id ) != 0) {
						! $reportModel->updateTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'core_question', $cq_instance_id, $cq_recommendations ) ? $this->db->rollback () && $failed = 1 : '';
					} elseif (! $reportModel->saveTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'core_question', $cq_instance_id, $cq_recommendations ))
						$this->db->rollback () && $failed = 2;
				}
			}
			if (! empty ( $_POST ['recommendations_js'] )) {
				$js = $_POST ['recommendations_js'];
				foreach ( $js as $k => $j ) {
					$js_instance_id = $k;
					$js_recommendations = implode ( '~', $_POST ['recommendations_js'] [$js_instance_id] );
					if ($reportModel->isExistingRecommendation ( $group_assessment_id, $diagnostic_id, 'judgement_statement', $js_instance_id ) != 0) {
						! $reportModel->updateTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'judgement_statement', $js_instance_id, $js_recommendations ) ? $this->db->rollback () && $failed = 1 : '';
					} elseif (! $reportModel->saveTeacherOverviewRecommendations ( $group_assessment_id, $diagnostic_id, 'judgement_statement', $js_instance_id, $js_recommendations ))
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
		else {
			//check if diagnosticname exists
			$diagnosticName = $_POST['diagnosticName'];
			$diagnosticId = $_POST['diagnosticId'];
			$diagnosticModel = new diagnosticModel();
			if($diagnosticModel->isDuplicateDiagnosticName($diagnosticName))
			{
				$this->apiResult ["message"] = "Diagnostic Name already exists.\n";
			}
			else{
				$success = $diagnosticModel->cloneDiagnostic($diagnosticId,$diagnosticName,$this->user['user_id']);
				$success = $success['SUCCESS'];				
				if($success==1)
				{
					$this->apiResult ["message"] = "Diagnostic has been cloned successfully. Please check Manage diagnostics.\n";
					$this->apiResult ["status"] = 1;
				}	;
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
					$this->apiResult ["message"] = $resourceModel->getSchoolUsers($school_ids,$user_role_ids);
					$this->apiResult ["status"] = 1;
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
                $row = diagnosticModel::getPostReviewDiagnosticHTML($sn,$assessment_id,$assessor_id,1);
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
                $content = $diagnosticModel->getKeyQuestionsForAssessment($assessment_id, $assessor_id,$kpa);
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
                $content = $diagnosticModel->getCoreQuestionsForKQAssessment($assessment_id, $assessor_id,$kq);
                $this->apiResult ["content"] = $content;
		$this->apiResult ["status"] = 1;
            }      
           
        }
         /*
             * 
             */
        function createResourceDirectoryAction() {

            if (!in_array("upload_resources", $this->user ['capabilities']))
                $this->apiResult ["message"] = "You are not authorized to perform this task.\n";
            else if (empty(trim($_POST ['new_dir_name'])))
                $this->apiResult ["message"] = "Directory Name cannot be empty.\n";
            else if (empty($_POST ['directory']))
                 $this->apiResult ["message"] = "Select a directory.\n";
            else {
                $res_dir_name = trim($_POST ['new_dir_name']);
                $directory_id = trim($_POST ['directory']);
                $resourceModel = new resourceModel();
                if ($resourceModel->checkDirectoryExistance($res_dir_name, $directory_id)) 
                    $this->apiResult ["message"] = "Directory already exist.\n";
                else {
                    $addDirId = $resourceModel->addNewResourceDirectory($res_dir_name, $directory_id);
                    $this->apiResult ["status"] = 1;
                    $this->apiResult ["Resource_Directory_Id "] = $addDirId;
                    $this->apiResult ["message"] = "Directory successfully added";
                }
        }
    }

}