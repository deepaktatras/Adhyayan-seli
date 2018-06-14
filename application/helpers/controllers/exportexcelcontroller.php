<?php

class exportExcelController extends controller {
    
    
    function breadcrumbs($separator = ' » ', $home = 'Home') {

$path = array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
$base_url = substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], '/')) . '://' . $_SERVER['HTTP_HOST'] . '/';
$breadcrumbs = array("<a href=.DS.$base_url.DS.>$home</a>");
$tmp = array_keys($path);
$last = end($tmp);
unset($tmp);

foreach ($path as $x => $crumb) {
$title = ucwords(str_replace(array('.php', '_'), array('', ' '), $crumb));
if ($x == 1){
$breadcrumbs[] = "<a href=.DS.$base_url$crumb.DS.>$title</a>";
}elseif ($x > 1 && $x < $last){
$tmp = " for($i = 1; $i <= $x; $i++){ $tmp .= $path[$i] . '/'; } $tmp .= .DS.>$title";
$breadcrumbs[] = $tmp;
unset($tmp);
}else{
$breadcrumbs[] = "$title";
}
}

return implode($separator, $breadcrumbs);
}
    
    
    

    function allDataAction() {
        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif (!in_array("create_assessment", $this->user['capabilities'])) {
            $this->_notPermitted = 1;
        }
    }

    function downloadAllDataExcelAction() {
        if ((in_array(6, $this->user['role_ids']) || in_array(5, $this->user['role_ids'])) && $this->user['has_view_video'] != 1 && $this->user['is_web'] == 1)//principal and school admin have to view video for self-review
            $this->_notPermitted = 1;
        elseif (!in_array("create_assessment", $this->user['capabilities'])) {
            $this->_notPermitted = 1;
            return;
        }
        //error_reporting(0);
        ini_set("memory_limit", "20000M");
        ini_set('max_execution_time', 1200);
        $diagnosticModel = new diagnosticModel();
        $kpas = $diagnosticModel->getKpasForDiagnostic(1);
        $exportExcelModel = new exportExcelModel();
        $assessments = $exportExcelModel->getAllData();

        $objPHPExcel = new PHPExcel();
        $workSheetArr = array(
            $objPHPExcel->createSheet(),
            $objPHPExcel->createSheet(),
            $objPHPExcel->createSheet(),
            $objPHPExcel->createSheet(),
            $objPHPExcel->createSheet(),
            $objPHPExcel->createSheet(),
            $objPHPExcel->createSheet()
        );

        $kpaData = array();
        for ($i = 0; $i < 6; $i++) {
            $workSheetArr[$i]->fromArray(array(array("Sr No", "School Name", "KPA{$i}SSREKQ1", "KPA{$i}SEREKQ1", "KPA{$i}JDKQ1", "KPA{$i}SSRE1", "KPA{$i}SERE1", "KPA{$i}JD1", "KPA{$i}SSRE1A", "KPA{$i}SERE1A", "KPA{$i}JD1A", "KPA{$i}SSRE1B", "KPA{$i}SERE1B", "KPA{$i}JD1B", "KPA{$i}SSRE1C", "KPA{$i}SERE1C", "KPA{$i}JD1C", "KPA{$i}SSRE2", "KPA{$i}SERE2", "KPA{$i}JD2", "KPA{$i}SSRE2A", "KPA{$i}SERE2A", "KPA{$i}JD2A", "KPA{$i}SSRE2B", "KPA{$i}SERE2B", "KPA{$i}JD2B", "KPA{$i}SSRE2C", "KPA{$i}SERE2C", "KPA{$i}JD2C", "KPA{$i}SSRE3", "KPA{$i}SERE3", "KPA{$i}JD3", "KPA{$i}SSRE3A", "KPA{$i}SERE3A", "KPA{$i}JD3A", "KPA{$i}SSRE3B", "KPA{$i}SERE3B", "KPA{$i}JD3B", "KPA{$i}SSRE3C", "KPA{$i}SERE3C", "KPA{$i}JD3C", "KPA{$i}SSREKQ2", "KPA{$i}SEREKQ2", "KPA{$i}JDKQ2", "KPA{$i}SSRE4", "KPA{$i}SERE4", "KPA{$i}JD4", "KPA{$i}SSRE4A", "KPA{$i}SERE4A", "KPA{$i}JD4A", "KPA{$i}SSRE4B", "KPA{$i}SERE4B", "KPA{$i}JD4B", "KPA{$i}SSRE4C", "KPA{$i}SERE4C", "KPA{$i}JD4C", "KPA{$i}SSRE5", "KPA{$i}SERE5", "KPA{$i}JD5", "KPA{$i}SSRE5A", "KPA{$i}SERE5A", "KPA{$i}JD5A", "KPA{$i}SSRE5B", "KPA{$i}SERE5B", "KPA{$i}JD5B", "KPA{$i}SSRE5C", "KPA{$i}SERE5C", "KPA{$i}JD5C", "KPA{$i}SSRE6", "KPA{$i}SERE6", "KPA{$i}JD6", "KPA{$i}SSRE6A", "KPA{$i}SERE6A", "KPA{$i}JD6A", "KPA{$i}SSRE6B", "KPA{$i}SERE6B", "KPA{$i}JD6B", "KPA{$i}SSRE6C", "KPA{$i}SERE6C", "KPA{$i}JD6C", "KPA{$i}SSREKQ3", "KPA{$i}SEREKQ3", "KPA{$i}JDKQ3", "KPA{$i}SSRE7", "KPA{$i}SERE7", "KPA{$i}JD7", "KPA{$i}SSRE7A", "KPA{$i}SERE7A", "KPA{$i}JD7A", "KPA{$i}SSRE7B", "KPA{$i}SERE7B", "KPA{$i}JD7B", "KPA{$i}SSRE7C", "KPA{$i}SERE7C", "KPA{$i}JD7C", "KPA{$i}SSRE8", "KPA{$i}SERE8", "KPA{$i}JD8", "KPA{$i}SSRE8A", "KPA{$i}SERE8A", "KPA{$i}JD8A", "KPA{$i}SSRE8B", "KPA{$i}SERE8B", "KPA{$i}JD8B", "KPA{$i}SSRE8C", "KPA{$i}SERE8C", "KPA{$i}JD8C", "KPA{$i}SSRE9", "KPA{$i}SERE9", "KPA{$i}JD9", "KPA{$i}SSRE9A", "KPA{$i}SERE9A", "KPA{$i}JD9A", "KPA{$i}SSRE9B", "KPA{$i}SERE9B", "KPA{$i}JD9B", "KPA{$i}SSRE9C", "KPA{$i}SERE9C", "KPA{$i}JD9C")), NULL, "A1", true)->setTitle($kpas[$i]['kpa_name'])->getStyle('A1:DO1')->getFont()->setBold(true);

            $dataArray = array();
            foreach ($assessments as $aid => $a) {
                if (empty($kpaData[$aid]))
                    $kpaData[$aid] = array();
                if (isset($a['external']['kpa_scores'][$i])) {
                    $havInternal = isset($a['internal']['kpa_scores'][$i]) && count($a['internal']['js_scores']) > 26 ? true : false;

                    if ($havInternal) {
                        $kpaData[$aid][] = $a['internal']['kpa_scores'][$i];
                        $kpaData[$aid][] = $a['external']['kpa_scores'][$i];
                        $kpaData[$aid][] = $a['internal']['kpa_scores'][$i] - $a['external']['kpa_scores'][$i];
                    } else {
                        $kpaData[$aid][] = "";
                        $kpaData[$aid][] = $a['external']['kpa_scores'][$i];
                        $kpaData[$aid][] = "";
                    }
                    $row = array();
                    $row[] = $a['client_id'];
                    $row[] = $a['client_name'];
                    for ($j = 0; $j < 3; $j++) {
                        if ($havInternal) {
                            $row[] = $a['internal']['kq_scores'][$i][$j];
                            $row[] = $a['external']['kq_scores'][$i][$j];
                            $row[] = $a['internal']['kq_scores'][$i][$j] - $a['external']['kq_scores'][$i][$j];
                        } else {
                            $row[] = "";
                            $row[] = $a['external']['kq_scores'][$i][$j];
                            $row[] = "";
                        }
                        for ($k = 0; $k < 3; $k++) {
                            $cq_index = $i * 3 + $j;
                            if ($havInternal) {
                                $row[] = $a['internal']['cq_scores'][$cq_index][$k];
                                $row[] = $a['external']['cq_scores'][$cq_index][$k];
                                $row[] = $a['internal']['cq_scores'][$cq_index][$k] - $a['external']['cq_scores'][$cq_index][$k];
                            } else {
                                $row[] = "";
                                $row[] = $a['external']['cq_scores'][$cq_index][$k];
                                $row[] = "";
                            }
                            for ($l = 0; $l < 3; $l++) {
                                $js_index = $cq_index * 3 + $k;
                                if ($havInternal) {
                                    $row[] = $a['internal']['js_scores'][$js_index][$l];
                                    $row[] = $a['external']['js_scores'][$js_index][$l];
                                    $row[] = $a['internal']['js_scores'][$js_index][$l] - $a['external']['js_scores'][$js_index][$l];
                                } else {
                                    $row[] = "";
                                    $row[] = $a['external']['js_scores'][$js_index][$l];
                                    $row[] = "";
                                }
                            }
                        }
                    }
                    $dataArray[] = $row;
                }
            }
            $workSheetArr[$i]->fromArray($dataArray, NULL, 'A2', TRUE);
        }

        $workSheetArr[6]->fromArray(array(array('Sr No', 'School code', 'School Name', 'Principal Name', 'AQS Date (day 5 date)', 'SVSU', 'ASIST', 'Network Name', 'networks/standalone', 'Urban/Rural', 'Govt/Aided/Private', 'school fee', 'medium of instruction', 'No. of students', 'no. of boys', 'no. of girls', 'section_pre-primary', 'section_primary', 'section_middle', 'section_secondary', 'section_high-school', 'award level', 'award tier', 'award level+tier', 'medium of instrcution', 'boys/girls/CO-ed', 'state board', 'ICSE board', 'cbse board', 'ib board', 'igcse board', 'other_board', 'boarding_yes_no', 'playground', 'TAP', 'research visit', 'any NGO intervention', 'NGO_name1', 'NGO_name2', 'NGO_name3', 'PPP_Yes_no', 'RTE Compliance', 'school recognition no.', 'school recognition authority', 'school mission_yes_no', 'school_vision_yes_no', 'KPA1SSRE', 'KPA1SERE', 'KPA1JD', 'KPA2SSRE', 'KPA2SERE', 'KPA2JD', 'KPA3SSRE', 'KPA3SERE', 'KPA3JD', 'KPA4SSRE', 'KPA4SERE', 'KPA4JD', 'KPA5SSRE', 'KPA5SERE', 'KPA5JD', 'KPA6SSRE', 'KPA6SERE', 'KPA6JD')), NULL, 'A1', true)->setTitle("overall")->getStyle('A1:BL1')->getFont()->setBold(true);

        $rId = 1;
        $alphaArr = array(1 => "A", 2 => "B", 3 => "C", 4 => "D", 5 => "E");
        foreach ($assessments as $k => $a) {
            $rId++;
            $workSheetArr[6]->setCellValue("A$rId", $k)->setCellValue("C$rId", $a['client_name'])->setCellValue("D$rId", $a['principal_name'])->setCellValue("H$rId", $a['network_name'])->setCellValue("K$rId", $a['school_type'])->setCellValue("L$rId", $a['annual_fee'])->setCellValue("N$rId", $a['no_of_students'])->fromArray(array($kpaData[$k]), NULL, "AU$rId", true);
            if ($a['board_id'] > 0 && $a['board_id'] < 6) {
                $workSheetArr[6]->setCellValue("A" . $alphaArr[$a['board_id']] . $rId, 1);
            }
        }

        $objPHPExcel->removeSheetByIndex(0);
        $objPHPExcel->setActiveSheetIndex(0);

        $this->_render = false;

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="kpa_report.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    function downloadEvidenceDataExcelAction() {
        
       //ini_set("memory_limit", "20000M");
        ini_set('max_execution_time', 1200000);
        $exportExcelModel = new exportExcelModel();  
        $client_info = $exportExcelModel->getDistinctClient();
		
        $client_id=  explode(",", $client_info['client_id']);
        $clients_name=  explode(";", $client_info['client_name']);
        //print_r( $client_id);
       
        $count_client=$client_info['client_count'];
        //echo '<pre>';
        //echo $count_client;
        
       	 $tmpPath = ROOT."tmp".DS."tmp_evidence".DS;
         /*echo '<pre>';
        echo $tmpPath;
        die;*/
        $i=0;
         $zip = new ZipArchive();
             $zip->open("Evidence_Report.zip",  ZipArchive::OVERWRITE);
        while($i<$count_client){
         $objPHPExcel = new PHPExcel();         
	 $workSheetArr = $objPHPExcel->createSheet(); 
         $workSheetArr->setCellValue('A1','KPA Name')
                     ->setCellValue('B1','Key Question Text')
                     ->setCellValue('C1','Core Question text')				 	
		     ->setCellValue('D1','Judgement Statement Text')
		     ->setCellValue('E1','Internal Evidence Text')
		     ->setCellValue('F1','External Evidence Text')
		     ->setCellValue('G1','Internal file Names');
		  $objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->getFont()->setBold(true); // Making header of a sheet bold  		
			$workSheetArr->setTitle(substr($clients_name[$i],0,30));
                   
		$evidences = $exportExcelModel->getFileDetails($client_id[$i]);
                $objPHPExcel->removeSheetByIndex(0);
                $objPHPExcel->setActiveSheetIndex(0);
                
	 
        $rId = 1;       
        $max_internal_files=0;
        $file_internal=array();
        $file_external=array();
		foreach ($evidences as $key => $value) {                   
           $evidences[$key] = array();
                   $file_int=  explode(",", $value['file_int']);
		   $file_ext=  explode(",", $value['file_ext']);
                   $file1_cnt=count($file_int);
                   $file2_cnt=count($file_ext);
                   $final_files_cnt=$file1_cnt+$file2_cnt;
                   //echo '<pre>';
                    //echo $final_files_cnt;
                    //die;
                    //echo '<pre>';

		   $file_internal=  explode(",", $value['file_int']);
		   $file_external=  explode(",", $value['file_ext']);
                   $file_names=array_merge($file_internal, $file_external);
                   $file_total=count($file_names);
//                  echo $file_total;
//                  echo '<pre>';
////                 print_r($file_int);
////                  
//                print_r($file_names);
////                 echo '</pre>';
//                  die;
                  
                  
             //$count_file1= count($file_internal);
            //echo '<pre>';
             //echo $count_file1;
             //die;
             //echo '<pre>';
	     //$count_file2= count($file_external);
             //echo $count_file2;
              //die;
             $srcfile = ROOT."uploads".DS;			 
		$client_name = $value['client_name'];
                $kpa = "KPA" . $value['kpa_order'];
                $kq = "_KQ" . $value['kq_order'];
                $cq = "_CQ" . $value['cq_order'];
                $js = "_JS" . $value['js_order'];
                $dstfile_path = $kpa . $kq . $cq . $js;
                $dstfile1 = $tmpPath."$client_name".DS."$dstfile_path".DS;			
//                echo count($file_names);
                
                for($j=0;$j<$file_total;$j++){
                    
                        //echo $file_names[$j][$k]."<br>";
                     if(!file_exists($dstfile1) && file_exists($srcfile.$file_names[$j]) )   
                        mkdir($dstfile1,0777,true);	
                        @copy($srcfile.$file_names[$j], $dstfile1.$file_names[$j]);
                          $zip->addFile($dstfile1.$file_names[$j],"$client_name".DS."$dstfile_path".DS."$file_names[$j]");  // Adding files into zip
                        }
                
                       // die;
               $col1=6;
            $rId++;
            $workSheetArr->setCellValue("A$rId",$value['kpa_name'])
                         ->setCellValue("B$rId",$value['key_question_text'])
                         ->setCellValue("C$rId",$value['core_question_text'])
                         ->setCellValue("D$rId",$value['judgement_statement_text'])
                         ->setCellValue("E$rId",$value['internal_evidence'])
                         ->setCellValue("F$rId",$value['external_evidence']);
                   
			$max_internal_files =$max_internal_files>$file1_cnt?$max_internal_files:$file1_cnt;
                        $col2=$max_internal_files+$col1;
                    
			if($file1_cnt>0){
                      for($kl=0;$kl<$file1_cnt;$kl++)
                     {
                          
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col1,$rId,$file_internal[$kl]);
                        $file_internal[$kl]!=''?$objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col1,$rId)->getHyperlink()->setUrl("$dstfile_path".DS.$file_internal[$kl]):'';
                        //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($max_internal_files,$rId,$file_external[$kl]);
                        //$objPHPExcel->getActiveSheet()->getCellByColumnAndRow($max_internal_files,$rId)->getHyperlink()->setUrl("$dstfile_path".DS.$file_external[$kl]);   
                        $col1++;
                        //$max_internal_files++;
                        //$col2++;
                     
                     }
                        }
                        if($file2_cnt>0){
                      
                    $col2++;
                      for($l=0;$l<$file2_cnt;$l++)
                     {
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col2,$rId,$file_external[$l]);
                        $file_external[$l]!=''?$objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col2,$rId)->getHyperlink()->setUrl("$dstfile_path".DS.$file_external[$l]):'';
                        $col2++;
                     }
                     
			}
                      			     
        }
         $workSheetArr->setCellValueByColumnAndRow(6+$max_internal_files,1,'External File Names');
        $i++;
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        if(!file_exists($tmpPath."$client_name"))
        mkdir($tmpPath."$client_name",0777,true);
        $objWriter->save(str_replace(__FILE__,$tmpPath."$client_name".DS.$client_name.'.xlsx',__FILE__));			
        $zip->addFile( $tmpPath."$client_name".DS.$client_name.'.xlsx',"$client_name".DS.$client_name.'.xlsx'); // Adding files into zip		
		//unlink($tmpPath."$client_name".DS.$client_name.'.xlsx');				
          }
             $zip->close();  
          header('Content-type: application/zip');
          header('Content-Disposition: attachment; filename="Evidence_Report.zip"');
           readfile("Evidence_Report.zip"); 
           //unlink($tmpPath."$client_name".DS.$client_name.'.xlsx');
	   unlink(ROOT."Evidence_Report.zip");	 
           //unlink($tmpPath);
         }
            
   }
