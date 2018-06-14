<?php
class customReportController extends controller {
	function networkAction() {
		if (in_array ( "view_all_assessments", $this->user ['capabilities'] )) {
			// $this->_template->addHeaderScript('http://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.js');
			// $this->_template->addHeaderScript('http://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-sanitize.js');
			$customreportModel = new customreportModel ();
			$added_by = $this->user['user_id'];
			$this->set ( "filters", $customreportModel->getFilters ($added_by) );
			$this->set ( "existingReports", $customreportModel->getNetworkReports () );
			$this->_template->addHeaderStyle ( 'selectize.default.css' );
			$this->_template->addHeaderStyle ( 'jquery.mCustomScrollbar.min.css' );
			$this->_template->addHeaderScript ( 'selectize.min.js' );
			$this->_template->addHeaderScript ( 'customreport.js' );
			$this->_template->addHeaderScript ( 'jquery.mCustomScrollbar.concat.min.js' );
			$this->_template->addHeaderStyleURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css' );
			$this->_template->addHeaderScriptURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js' );
                        $this->_template->addHeaderScriptURL ( SITEURL.'public'.DS.'js'.DS.'tinymce'.DS.'tinymce.min.js' );
			//$this->_template->addHeaderScriptURL ( 'https://raw.githubusercontent.com/shvetsgroup/jquery.multisortable/master/src/jquery.multisortable.js' );
		} else
			$this->_notPermitted = 1;
	}
	function editnetworkreportAction() {
		if (in_array ( "view_all_assessments", $this->user ['capabilities'] )) {
			$network_report_id = $_GET ['network_report_id'];			
			$objCustom = new customreportModel ();
			$network_report_data = $objCustom->getNetworkReportData ( $network_report_id );
				if(empty($network_report_data))
					$this->_is404 = 1;
				else{
						$network_report_name = ucwords ( $network_report_data ['report_name'] );
						$this->set('report_name', $network_report_name);
						$this->set('network_report_id', $network_report_id);
						$experience = explode('~',$network_report_data['review_experience']);
						$this->set('experience',$experience);						
						//$this->_render=false;
						//$file = ROOT.'reports'.DS.$network_report_name.'.pdf';
						/* if(file_exists($file))
						{
							$filename = $network_report_name.".pdf";
							header('Content-type: application/pdf');
							header('Content-Disposition: inline; filename="' . $filename . '"');
							header('Content-Transfer-Encoding: binary');
							header('Content-Length: ' . filesize($file));
							header('Accept-Ranges: bytes');
							@readfile($file);
						} */
						$this->_template->addHeaderStyle ( 'selectize.default.css' );
						$this->_template->addHeaderStyle ( 'jquery.mCustomScrollbar.min.css' );
						$this->_template->addHeaderScript ( 'selectize.min.js' );
						$this->_template->addHeaderScript ( 'customreport.js' );
						$this->_template->addHeaderScript ( 'jquery.mCustomScrollbar.concat.min.js' );
                                                $this->_template->addHeaderScriptURL ( SITEURL.'public'.DS.'js'.DS.'tinymce'.DS.'tinymce.min.js' );
						$this->_template->addHeaderStyleURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css' );
						$this->_template->addHeaderScriptURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js' );
                                                //$this->_template->addHeaderScriptURL ( 'https://cloud.tinymce.com/stable/tinymce.min.js' );
						//$this->_template->addHeaderScriptURL ( 'https://raw.githubusercontent.com/shvetsgroup/jquery.multisortable/master/src/jquery.multisortable.js' );
					}
			} else
			$this->_notPermitted = 1;
	}
	function createFilterAction() {
		//pricipal, admin and school principal are able to create filters
		if (in_array ( 1, $this->user ['role_ids'] ) || in_array ( 2, $this->user ['role_ids'] ) || in_array ( 6, $this->user ['role_ids'] )) {
			// $this->_template->addHeaderScript('http://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.js');                    
			$isDashboard = !empty($_GET['isDashboard'])?$_GET['isDashboard']:0;
			//echo "dashboard: ".$isDashboard;
			$this->set('isDashboard',$isDashboard);
			$this->_template->addHeaderStyle ( 'selectize.default.css' );
			$this->_template->addHeaderScript ( 'selectize.min.js' );
			$this->_template->addHeaderScript ( 'customreport.js' );
		} else
			$this->_notPermitted = 1;
	}
	function editFilterAction() {
		if (in_array ( 1, $this->user ['role_ids'] ) || in_array ( 2, $this->user ['role_ids'] ) || in_array ( 6, $this->user ['role_ids'] )) {
			$filter_id = ! empty ( $_GET ['fid'] ) ? $_GET ['fid'] : 0;
			$this->set ( 'filter_id', $filter_id );
			$customreportModel = new customreportModel ();
			$this->set ( 'filterData', $customreportModel->getFilterData ( $filter_id ) );
			$isDashboard = !empty($_GET['isDashboard'])?$_GET['isDashboard']:0;
			//echo "dashboard: ".$isDashboard;
			$this->set('isDashboard',$isDashboard);
			// $this->_template->addHeaderScript('http://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.js');
			$this->_template->addHeaderStyle ( 'selectize.default.css' );
			$this->_template->addHeaderScript ( 'selectize.min.js' );
			$this->_template->addHeaderScript ( 'customreport.js' );
		} else
			$this->_notPermitted = 1;
	}
	
	// generate network report
	function generateNetworkReportPDFAction() {
		if (in_array ( "view_all_assessments", $this->user ['capabilities'] )) {
			$network_report_id = $_GET ['network_report_id'];//echo 'a'.$network_report_id;
			if ($network_report_id != '') {
				$objCustom = new customreportModel ();
				$network_report_data = $objCustom->getNetworkReportData ( $network_report_id );
				if (empty ( $network_report_data ))
					$this->_is404 = 1;
				else {
					$network_report_name = ucwords ( $network_report_data ['report_name'] );					
					$filter_id = $network_report_data ['filter_id'];
					$experience = explode ( '~', $network_report_data ['review_experience'] );
					$include_self_review = $network_report_data ['include_self_review'];
					// load result on basis of filter
					$resParams = $objCustom->applyFilterQuery ( $filter_id );
					$queryParams = '1=1';
					$award_scheme_id =1;
					foreach ( $resParams as $param ) {
						if ($param ['filter_table'] == 'd_award_scheme')
							$award_scheme_id = $param['value'];
						if ($param ['filter_table'] == 'd_fees' || $param ['filter_table'] == 'd_school_strength') {
							$temp = $objCustom->getStaticTableVals ( $param ['filter_table'], $param ['filter_table_col_id'], $param ['filter_table_col_name'], $param ['value'] );
							$param ['value'] = $temp ['staticCol'];
							$param ['filter_table'] == 'd_fees' ? ($param ['filter_table_col_id'] = 'annual_fee') : ($param ['filter_table'] == 'd_school_strength' ? ($param ['filter_table_col_id'] = 'no_of_students') : '');
						}
						$queryParams .= ' AND ' . $param ['filter_table_col_id'] . ' ' . $param ['operator_text'] . ' ' . ($param ['operator_text'] == 'IN' ? '(' . $param ['value'] . ')' : $param ['value']);
					}
					
					$objCustom->createAQSclientsTbl ( $queryParams, $network_report_id );
					$schoolAwards = $objCustom->getSchoolAwards ();                                        
					$objCustom->createAnnex2Data();
					$kpa7arr = array ();
					$diagnostic_id = $schoolAwards[0]['diagnostic_id']; 										
					$num_of_schools = $objCustom->getClientCount ();
					$num_of_schools = $num_of_schools ['num'];
					$statewiseSchoolAwards = array();
					if (! empty ( $schoolAwards )) {
						$start_client_data = 0;
						$end_client_data = 0;
						$dates = 0;	
						$num_of_states = $objCustom->getDataAwardParamCount('state_name');
						$num_of_states = $num_of_states['num'];
						$stateSpread =	$objCustom->getDataAwardParamGroupCount('client_id','state_name');
						//print_r($stateSpread);die;
						$stringStateSpread='';
						$arrStateSpread = array();						
						foreach($stateSpread as $k=>$st){
							$statewiseSchoolAwards[$st['param']] = $objCustom->getParamwiseData($st['param']);
							array_push($arrStateSpread, ' '.$st['num'].' of the school(s) '.($st['num']>1?'were':'was').' located in '.$st['param']);	
						}
						//print_r($statewiseSchoolAwards);die;
						$numStateBySchool = count($arrStateSpread);
						$i = 1;
						if($numStateBySchool>1)
							foreach($arrStateSpread as $k=>$v)
							{
								 ($i++==$numStateBySchool)?($clause = ' and the remaining '.str_replace('of the ','',$v)):($clause=$v.',');
								 ($i>$numStateBySchool)? ($stringStateSpread = substr($stringStateSpread, 0,-1)):'';
								 $stringStateSpread .= $clause;
							}
						else 
							$stringStateSpread = $arrStateSpread[0];
						//echo $stringStateSpread;
						//die;
						unset($k);
						unset($i);
						// $end_client_data = end($clientData);
						$dates = $objCustom->getStartEndDate ( $start_client_data, $end_client_data );
						$dateyearReview = $objCustom->getMonthReviewDate($start_client_data);
						$dateyearReview = $dateyearReview['month'].'\' '.$dateyearReview['year'];
						$data = $objCustom->getSchoolAwardsCount ();
						$performaceKPAs = $objCustom->getPerformaceKPAS ();
						$getAllSchoolPerf = $objCustom->getAllSchoolPerf ();
						$objCustom->createKPACQRating ();
						$objCustom->createKPAParameterWiseAnalysis (4);
						//echo 'diag: '.$diagnostic_id;die;
						$objCustom->createDiagnosticStmtNumbers($diagnostic_id);
						$CQstmts = $objCustom->getCQstatements ();
						$objCustom->createJDtable ();
						$CQstmts = $this->db->array_col_to_key($CQstmts,'core_question_id');					
						$clientsJd = $objCustom->getClientsJd ();
						$kpaCQrating = array ();
						$kpaJDarr = array ();
						/* for($i = 1; $i <= 6; $i ++) {
							$kpaCQrating [$i] = $objCustom->getKpaCQrating ( $i );
							//print_r($kpaCQrating [$i]);
							$kpaJDarr [$i] = $objCustom->getKpaJd ( $i );
						} */
						//print_r($kpaCQrating);die;
						// for kpa 7
						$is_validated = 0;
						$kpa7Id = 0;
						if($include_self_review==1)
						{
							$is_validated = $network_report_data['is_validated'];
							$role = $is_validated==1?4:3;
							$objCustom->createKPA7ParameterWiseAnalysis($role);
							//$objCustom->createKpa7Rating ($role);
							$objCustom->createKPA7CQRating($role);							
							//if($role==3)
								//$objCustom->createKpa7Evidence();
									
								//$kpa7arr [7] = $objCustom->getKpa7rating ();
								//print_r($kpa7arr [7] );
								//print_r($kpa7arr);die;
								//get kpa 7 name
								
								$kpa7Schools = $objCustom->getKpa7CQrating ();
								//print_r($kpa7Schools);die;
								$kpa7Name = $kpa7Schools[0]['kpa_name'];
								$kpa7Id = $kpa7Schools[0]['kpa_id'];
								//print_r($kpa7Schools);die;
						}
						$internalAwards = $objCustom->getInternalAwards();
						//if($objCustom->createKqKPARating())							
							//$kqRatings = $objCustom->getKpaKqRating();								
						//print_r($kqRatings);												
						//die;						
						include (ROOT . 'application' . DS . 'views' . DS . "customreport" . DS . 'generateNetworkReportPDF.php');						
					}
				}
			} else
				$this->_is404 = 1;
		} else
			$this->_notPermitted = 1;
	}
	// generate network report
	function generateDataSummaryAction() {
		if (in_array ( "view_all_assessments", $this->user ['capabilities'] )) {
			$objCustom = new customreportModel();
			$network_report_id = $_GET ['network_report_id'];//echo 'a'.$network_report_id;
			$network_report_data = $objCustom->getNetworkReportData ( $network_report_id );
			if ($network_report_id != '' && !empty($network_report_data)) {
				$network_report_name = ucwords ( $network_report_data ['report_name'] );
				$filter_id = $network_report_data ['filter_id'];								
				// load result on basis of filter
				$resParams = $objCustom->applyFilterQuery ( $filter_id );
				$queryParams = '1=1';
				$award_scheme_id =1;
				foreach ( $resParams as $param ) {
					if ($param ['filter_table'] == 'd_award_scheme')
						$award_scheme_id = $param['value'];
						if ($param ['filter_table'] == 'd_fees' || $param ['filter_table'] == 'd_school_strength') {
							$temp = $objCustom->getStaticTableVals ( $param ['filter_table'], $param ['filter_table_col_id'], $param ['filter_table_col_name'], $param ['value'] );
							$param ['value'] = $temp ['staticCol'];
							$param ['filter_table'] == 'd_fees' ? ($param ['filter_table_col_id'] = 'annual_fee') : ($param ['filter_table'] == 'd_school_strength' ? ($param ['filter_table_col_id'] = 'no_of_students') : '');
						}
						$queryParams .= ' AND ' . $param ['filter_table_col_id'] . ' ' . $param ['operator_text'] . ' ' . ($param ['operator_text'] == 'IN' ? '(' . $param ['value'] . ')' : $param ['value']);
				}
				$start_client_data = 0;
				$end_client_data = 0;
				$dates = 0;
				$objCustom->createAQSclientsTbl ( $queryParams, $network_report_id );
				$schoolAwards = $objCustom->getSchoolAwards ();
				$num_of_schools = $objCustom->getClientCount ();
				$num_of_schools = $num_of_schools ['num'];
				$diagnostic_id = $schoolAwards[0]['diagnostic_id'];
				$objCustom->createDiagnosticStmtNumbers($diagnostic_id);
				$dates = $objCustom->getStartEndDate ( $start_client_data, $end_client_data );
				$dateyearReview = $objCustom->getMonthReviewDate($start_client_data);
				$dateyearReview = $dateyearReview['month'].'\' '.$dateyearReview['year'];
				//$data = $objCustom->getSchoolAwardsCount ();
				$performaceKPAs = $objCustom->getPerformaceKPAS ();				
				$objCustom->createJDtable();
				include (ROOT . 'application' . DS . 'views' . DS . "customreport" . DS . 'generatedatasummary.php');
			} else
				$this->_is404 = 1;
		} else
			$this->_notPermitted = 1;
	}
	function networkreportlistAction() {
		if (in_array ( "view_all_assessments", $this->user ['capabilities'] )) {
			$customreportModel = new customreportModel();		
			$cPage=empty($_POST['page'])?1:$_POST['page'];			
			$order_type=empty($_POST['order_type'])?"desc":$_POST['order_type'];			
			$order_by=empty($_POST['order_by'])?"create_date":$_POST['order_by'];				
			$param=array(
			
					"report_name_like"=>empty($_POST['report_name'])?"":$_POST['report_name'],								
			
					"page"=>$cPage,
			
					"order_by"=>$order_by,
			
					"order_type"=>$order_type,
			
			);
			$this->set("filterParam",$param);			
			$this->set("cPage",$cPage);
			$this->set("orderBy",$order_by);
			$this->set("orderType",$order_type);
			$this->set('networkReportList',$customreportModel->getNetworkReportsList($param));
			$this->set("pages",$customreportModel->getPageCount());
		}
		else
			$this->_notPermitted = 1;
	}
	function viewreportAction() {
		if (in_array ( "view_all_assessments", $this->user ['capabilities'] )) {
			$network_report_id = $_GET ['network_report_id'];
			if ($network_report_id != '') {
				$objCustom = new customreportModel ();
				$network_report_data = $objCustom->getNetworkReportData ( $network_report_id );
				if (empty ( $network_report_data ))
					$this->_is404 = 1;
				else {
						$network_report_name = ucwords ( $network_report_data ['report_name'] );
						$this->_render=false;
						$file = ROOT.'reports'.DS.$network_report_name.'.pdf';	
						if(file_exists($file))
						{
							$filename = $network_report_name.".pdf"; /* Note: Always use .pdf at the end. */						
							header('Content-type: application/pdf');
							header('Content-Disposition: inline; filename="' . $filename . '"');
							header('Content-Transfer-Encoding: binary');
							header('Content-Length: ' . filesize($file));
							header('Accept-Ranges: bytes');						
							@readfile($file);
						}
						else{
							header("location:?controller=customreport&action=generateNetworkReportPDF&network_report_id=".$network_report_id);
						}
											
					}
		} else
			$this->_notPermitted = 1;
		}
	}
	
	function dashboardAction(){
		if (in_array(6,$this->user['role_ids'])) {						
			$this->_template->addHeaderStyle ( 'selectize.default.css' );
			$this->_template->addHeaderStyle ( 'jquery.mCustomScrollbar.min.css' );
			$this->_template->addHeaderStyleURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css' );
			$this->_template->addHeaderScript ( 'selectize.min.js' );
			$this->_template->addHeaderScript ( 'customreport.js' );
			$this->_template->addHeaderScript ( 'jquery.mCustomScrollbar.concat.min.js' );			
			$this->_template->addHeaderScriptURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js' );
			$this->_template->addHeaderScript( 'd3js.v4.js' );
			//get last school review settings
		//	print_r($this->user);			
			$client_id = $this->user['client_id'];
			$customreportModel = new customreportModel ();
			$assessmentModel = new assessmentModel();
			//$customreportModel->generateComparisonData();
			$assessment_type_id = 1;//for school reviews
			$lastReviewData = $customreportModel->getClientLatestReviewData($client_id,$assessment_type_id);
			//print_r($lastReviewData);						
			$clientModel=new clientModel();
			$this->set('lastReviewData',$lastReviewData);	
			//print_r($lastReviewData);
			$lastKPAratings = $assessmentModel->getKPAratingsforAssessment($lastReviewData['assessment_id'],4);//get external reviewer ratings for the last review
			$this->set('lastKPAratings',$lastKPAratings);
			//print_r($lastKPAratings);
                        $kpaIds = array_column($lastKPAratings, 'kpa_id');
                        $kpasConcat = implode(',',$kpaIds);
                        //$this->set("countries",$clientModel->getCountryList());
			$this->set("ccountry",!empty($lastReviewData['country_id'])?$lastReviewData['country_id']:101);					
			//awardTiers
			$added_by = $this->user['user_id'];
			$this->set("awardTiers",($assessmentModel->getTiers()));			
			
			//store default filter for the user when the page loads so that data is refreshed according to the new reviews
			$filter_id = 0;
			$this->set ( "filters", $customreportModel->getFilters ($added_by) );
			//$customreportModel->generateComparisonData();
			$awardScheme = "";			
			$awardScheme>0?($awardScheme=" AND award_scheme_id=".$awardScheme):($awardScheme=" AND award_scheme_id=1");			
			$this->set("data",$customreportModel->getClientComparisonAwards(array(" 1=1"),$awardScheme));
			
			//for kpas
			$customreportModel->createKpaRatingsDashboard($kpasConcat);
			$kpas = $customreportModel->getKpasDasboard();
			$i=0;
			$maxScaleY = 0;
			foreach($kpas as $row){
				$kpaMatrix[$i]['name']=$row['kpa_name'];				
					$kpaData = $customreportModel->getKpaRatingsDasboard($row['kpa_id']);
					$j=0;
					foreach($kpaData as $kRow):
					$kpaMatrix[$i][$j]['default']=$kRow['num'];
					$kpaMatrix[$i][$j]['rating']=$kRow['rating'];
					$j++;		
					$maxScaleY = $kRow['num']>$maxScaleY?$kRow['num']:$maxScaleY;
					endforeach;													
				$i++;
			}
			
			$this->set("kpaMatrix", $kpaMatrix);
			$this->set("maxScaleY", $maxScaleY);
			
		} else
			$this->_notPermitted = 1;
	}
	function dashboardfiltersAction(){
		if (in_array(6,$this->user['role_ids'])) {
			$added_by = $this->user['user_id'];		
			//store default filter for the user when the page loads so that data is refreshed according to the new reviews
			$filter_id = 0;
			$customreportModel = new customreportModel();
			$this->set ( "filters", $customreportModel->getFilters ($added_by) );
			$this->_template->addHeaderScript('jquery.mCustomScrollbar.concat.min.js');
			$this->_template->addHeaderStyle('jquery.mCustomScrollbar.min.css');
		} else 
			$this->_notPermitted = 1;
	}
	function admindashboardAction(){
		if (in_array ( "view_all_assessments", $this->user ['capabilities'] )) {		
			$customreportModel = new customreportModel ();
			$this->_template->addHeaderStyle ( 'selectize.default.css' );
			$this->_template->addHeaderStyle ( 'jquery.mCustomScrollbar.min.css' );
			$this->_template->addHeaderStyleURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css' );
			$this->_template->addHeaderScript ( 'selectize.min.js' );
			$this->_template->addHeaderScript ( 'customreport.js' );
			$this->_template->addHeaderScript ( 'jquery.mCustomScrollbar.concat.min.js' );			
			$this->_template->addHeaderScriptURL ( 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js' );
			$this->_template->addHeaderScript( 'd3js.v4.js' );
			//get last school review settings
		//	print_r($this->user);			
			$client_id = $this->user['client_id'];
			$customreportModel = new customreportModel ();
			$customreportModel->createAdminDashboardData();
			$added_by = $this->user['user_id'];
			//store default filter for the user when the page loads so that data is refreshed according to the new reviews
			$filter_id = 0;
			$this->set ( "filters", $customreportModel->getFilters ($added_by) );	
			/* $this->_template->addFooterScript ( 'app.js' ); */
		} else
			$this->_notPermitted = 1;
	}
	function diagnosticlevelquesAction(){
		$this->_template->addHeaderScript ( 'customreport.js' );	
		if (in_array ( "view_all_assessments", $this->user ['capabilities'] )) {			
			$cPage = empty ( $_POST ['page'] ) ? 1 : $_POST ['page'];			
			$order_by = empty ( $_POST ['order_by'] ) ? "name" : $_POST ['order_by'];			
			$order_type = empty ( $_POST ['order_type'] ) ? "asc" : $_POST ['order_type'];	
			$type = empty($_GET['type'])?'kpa':$_GET['type'];	
			$sno = empty($_GET['sno'])?0:$_GET['sno'];
			$frm = empty($_GET['frm'])?0:$_GET['frm'];//ismulti
			$name=''; $id='';$tableName='';$title='';
			switch($type){
				case 13 : $name = 'kpa_name';$id = "kpa_id";$tableName='d_kpa';$title = 'KPA';
				break;
				case 23 : $name = 'key_question_text';$id = "key_question_id";$tableName='d_key_question';$title = 'Key Questions';
				break;
				case 24: $name = 'core_question_text';$id = "core_question_id";$tableName='d_core_question';$title = 'Sub Questions';
				break;
				case 25 : $name = 'judgement_statement_text';$id = "judgement_statement_id";$tableName='d_judgement_statement';$title="Judgement Statemts";
				break;
			}	
			//print_r($_POST);
			$val =  empty ( $_POST [$name] ) ? "" : $_POST [$name];
			$key =  empty ( $_POST [$id] ) ? "" : $_POST [$id];
			
			$param = array (
					
					"page" => $cPage,
					$name."_like" => $val,
					$id => $key,
					"order_by" => $name,
					"order_type" => $order_type 
			)
			;			
			//echo $id,$name;
		//	print_r($param);
			$customreportModel = new customreportModel();
			$this->set('name',$name);
			$this->set('title',$title);
			$this->set('type',$type);
			$this->set('id',$id);
			$this->set('sno',$sno);
			$this->set('frm',$frm);
			$this->set ( "filterParam", $param );		
			$this->set ( "questions", $customreportModel->getDiagnosticLevelQues( $param ,$tableName,$id,$name) );			
			$this->set ( "pages", $customreportModel->getPageCount () );			
			$this->set ( "cPage", $cPage );			
			$this->set ( "orderBy", $order_by );			
			$this->set ( "orderType", $order_type );
			$attrId = $id;
			$currentSelectionIdsArr = empty ( $_POST ['question'] ) ? array() : $_POST ['question'] ;
			$currentSelectionIds = empty ( $_POST ['question'] ) ? 0 : implode(',',$_POST ['question'] );
			//echo $currentSelectionIds;
			$currentSelection = $customreportModel->getQuestionTextforIds( $tableName,$id,$name,$currentSelectionIds );		
			//echo "hey: ";print_r($currentSelection);
			$this->set ( "currentSelection", $currentSelection );			
			$this->set ( "currentSelectionIdsArr", $currentSelectionIdsArr );
		} else
			
			$this->_notPermitted = 1;
		
	}
	function ngDataViewAction(){
		if (in_array ( "view_all_assessments", $this->user ['capabilities'] )) {
			$customreportModel = new customreportModel ();			
			$this->_template->addHeaderScript ( 'customreport.js' );		
			$this->_template->addHeaderScriptURL ( 'http://ajax.googleapis.com/ajax/libs/angularjs/1.4.3/angular.js' );
			$this->_template->addHeaderScriptURL ( 'http://ajax.googleapis.com/ajax/libs/angularjs/1.4.3/angular-touch.js' );
			$this->_template->addHeaderScriptURL ( 'http://ajax.googleapis.com/ajax/libs/angularjs/1.4.3/angular-animate.js' );
			$this->_template->addHeaderScriptURL ( 'http://ui-grid.info/docs/grunt-scripts/csv.js' );
			$this->_template->addHeaderScriptURL ( 'http://ui-grid.info/docs/grunt-scripts/pdfmake.js' );
			$this->_template->addHeaderScriptURL ( 'http://ui-grid.info/docs/grunt-scripts/vfs_fonts.js' );
			$this->_template->addHeaderScriptURL ( 'http://ui-grid.info/release/ui-grid.js' );
			$this->_template->addHeaderStyleURL ( 'http://ui-grid.info/release/ui-grid.css' );
			
			$this->_template->addFooterScript ( 'app.js' );
		} else
			$this->_notPermitted = 1;
	}
	
}