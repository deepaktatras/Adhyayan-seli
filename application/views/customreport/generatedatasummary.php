<?php
error_reporting(0);
include ROOT . 'library' . DS . 'tcpdf' . DS . 'tcpdf.php';
ini_set('memory_limit','50M');
set_time_limit(0);
ini_set('max_execution_time', 0);
if($_REQUEST['custom_report']==1){
$report_point3=isset($_REQUEST['report_point3'])?$_REQUEST['report_point3']:0;
$report_point5=isset($_REQUEST['report_point5'])?$_REQUEST['report_point5']:0;
}else{
$report_point3=1;
$report_point5=1;
}

class TOC_TCPDF extends TCPDF {
	public $top_margin = 12;
	public $network_report_name = '';
	/**
	 * Overwrite Header() method.
	 * @public
	 */
	public function Header() {
		if ($this->tocpage) {
			// *** replace the following parent::Header() with your code for TOC page
			parent::Header ();
		} else {
			// *** replace the following parent::Header() with your code for normal pages
			parent::Header ();
		}
	}
	
	/**
	 * Overwrite Footer() method.
	 * @public
	 */
	public function Footer() {
		
		/* if ($this->tocpage) {
			// *** replace the following parent::Footer() with your code for TOC page
			parent::Footer ();
		} else */ {
			// *** replace the following parent::Footer() with your code for normal pages
			$this->bottom_margin = $this->GetY() + 2;			
			//$this->SetY(-5);
			$this->SetFont('helvetica', 'N', 8);			
			$this->Cell(0, 5, 'Data Summary Report for ' . $this->network_report_name, 0, false, 'L', 0, '', 0, false, 'T', 'M');
		//+
		 parent::Footer ();
		}
	}
}
// end of class
// create new PDF document
$pdf = new TOC_TCPDF ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
$pdf->network_report_name = $network_report_name;
$pdf->SetMargins(15, $pdf->top_margin, 15);
$pdf->SetPrintHeader(false);
$pdf->SetTitle($network_report_name);
// $pdf->SetSubject('TCPDF Tutorial');
// $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
$pdf->SetHeaderData ( '', '', '' );
//$pdf->SetHeaderData ( '', '', 'Network Overview Report for ' . $network_report_name );
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $network_report_name.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
// set header and footer fonts
$pdf->setHeaderFont ( Array (
		PDF_FONT_NAME_MAIN,
		'',
		PDF_FONT_SIZE_MAIN 
) );
$pdf->setFooterFont ( Array (
		PDF_FONT_NAME_DATA,
		'',
		PDF_FONT_SIZE_DATA 
) );

// set default monospaced font
$pdf->SetDefaultMonospacedFont ( PDF_FONT_MONOSPACED );

// set margins
//$pdf->SetMargins ( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
$pdf->SetHeaderMargin ( PDF_MARGIN_HEADER );
$pdf->SetFooterMargin ( PDF_MARGIN_FOOTER );

// set auto page breaks
$pdf->SetAutoPageBreak ( TRUE, 15 );

// set image scale factor
$pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );

// set some language-dependent strings (optional)
if (@file_exists ( dirname ( __FILE__ ) . '/lang/eng.php' )) {
	require_once (dirname ( __FILE__ ) . '/lang/eng.php');
	$pdf->setLanguageArray ( $l );
}

// set font
// set font
$pdf->SetFont ( 'times', '', 10 );

// ---------------------------------------------------------
// create some content ...
// add a page
// first page start
$pdf->addPage ();
if($diagnostic_id==1)//for Don Bosco diagnostic
	$firstpagehtml = '<br/><br/><table class="pdfHdr broad"> <tbody><tr><td class="halfSec fl" align="left"><a href=""><img src="' . SITEURL . 'public/images/logo.png"alt="" style="height:55px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'.UPLOAD_URL_DIAGNOSTIC.'diagnostic_image_1.jpg" style="height:80px;" alt=""></a></td></tr><tr style="text-align:center;padding:2px;font-weight:bold;font-family: CalibriRegular,Verdana,sans-serif;font-size:12px;"><td colspan="2">Don Bosco School Self-Review and Evaluation Programme (DBSSRE) conducted under the aegis of<br/> \'Don Bosco for Excellence\'</td></tr> </tbody></table>' . '<br/><br/>';
else
	$firstpagehtml = '<br/><br/><div style="text-align:center"><img src="' . SITEURL . 'public/images/logo.png"></div>' . '<br/><br/>' ;
$firstpagehtml.='<div style="text-align:center;font-size:18;">Data Summary Report:<span style="font-weight:bold;">'.$network_report_name.'</span></div>' . '<br/>' . '<div style="text-align:center;font-size:14;">Schools reviewed between ' . date_format ( date_create ( $dates ['startdate'] ), "d M, Y" ) . ' to ' . date_format ( date_create ( $dates ['enddate'] ), "d M, Y" ) . '</div>' . '<br/>' . '<div style="text-align:center;font-size:18;font-weight:bold">' . date ( 'F Y' ) . '</div><br/>' ;
// Print text using writeHTMLCell()

$firstpagehtml .= '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><div style="text-align:center;font-size:15;font-weight:bold;background-color: #6c0d10;color:#fff;">Glossary of Terms</div>';

$firstpagehtml .= <<<EOD
<div>
    <table width="100%">
        <tr>
            <td width="12%" style="color:#6c0d10;font-size:12">AQS</td>
            <td width="88%" style="font-size:12">Adhyayan Quality Standard</td>
        </tr>
        <tr>
            <td width="12%" style="color:#6c0d10;font-size:12">KPA</td>
            <td width="88%" style="font-size:12">Key Performance Area</td>
        </tr>
        <tr>
            <td width="12%" style="color:#6c0d10;font-size:12">SSRE</td>
            <td width="88%" style="font-size:12">School Self-Review and Evaluation</td>
        </tr>
        <tr>
            <td width="12%" style="color:#6c0d10;font-size:12">SERE</td>
            <td width="88%" style="font-size:12">School External Review and Evaluation</td>
        </tr>
    </table>
</div>
EOD;
$pdf->writeHTMLCell ( 0, 0, '', '', $firstpagehtml, 0, 1, 0, true, 'J', true );
// first page end
$i = 0;
$prev_kpa_id = 0;
$kpaNames = array();
$kpaAcronyms = array("kpa1"=>0,"kpa2"=>0,"kpa3"=>0,"kpa4"=>0,"kpa5"=>0,"kpa6"=>0);
$kpaIds = array("kpa1"=>0,"kpa2"=>0,"kpa3"=>0,"kpa4"=>0,"kpa5"=>0,"kpa6"=>0);
//create uri to pass to get image
foreach ( $performaceKPAs as $val ) {
	if ($i == 0 || $prev_kpa_id != $val ['kpa_id'])
		$i ++;
	$prev_kpa_id = $val['kpa_id'];
	$kpaNames["kpa$i"] = $val ['kpa_name'];
	$kpaIds["kpa$i"] = $val['kpa_id'];
	if(preg_match_all('/\b(\w)/',strtoupper($val['kpa_name']),$m) && !in_array(strtolower($val['kpa_name']),array('the child','the curriculum')) )
		$kpaAcronyms["kpa$i"] = implode(' & ',$m[1]);
		elseif(strtolower($val['kpa_name'])=='the child')//removing the in acronyms
		$kpaAcronyms["kpa$i"] = $val['kpa_name'];
		elseif(strtolower($val['kpa_name'])=='the curriculum')
		$kpaAcronyms["kpa$i"] = 'Curriculum';		
}
// section 1 page start
$pdf->AddPage ();
// set a bookmark for the current position
$mainHeadingNum = 1;
$mainHeadingTxt = '. Judgement statement and ratings table for every school in the network';
$pdf->Bookmark ( $mainHeadingNum.$mainHeadingTxt, 0, 0, '', 'B', array (
		0,0,0
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->Cell ( 0, 8, $mainHeadingNum++.$mainHeadingTxt, 0, 1, 'C', true );
// create js and ratings table for every school in the network

$point = 1;
//$pdf->addPage();
// cumulative report indicator bars
//$pdf->writeHTMLCell ( 0, 0, '', '', $section6_html1, 0, 1, 0, true, 'J', true );
$k=1;
$tableNum = 1;
$pdf->Ln(1);
foreach($kpaNames as $kpa){	
		$pdf->Bookmark ( $mainHeadingNum.'.'.$point.'. Performance on KPA'.$k.': '.$kpaNames['kpa'.$k], 1, 0, '', '', array (
				0,0,0
		) );
		$pdf->SetFont ( 'times', 'B', 13 );
		$pdf->SetTextColor ( 0, 0, 0 );
		$pdf->SetFillColor ( 211, 211, 211 );
		$pdf->Cell ( 0, 5, $mainHeadingNum.'.'.$point++.' Performance on KPA'.$k.': '.$kpaNames['kpa'.$k], 1, 1, 'C', true );
		$pdf->SetFont ( 'times', '', 9 );		
		$pdf->ln(1);		
		for($kqnum=1;$kqnum<=3;$kqnum++){
			$newkq=1;
			$kqjsJdRatings = $objCustom->getKpaKqRatingAndJd($kpaIds['kpa'.$k],($kqnum-1)*9*$num_of_schools,9*$num_of_schools);			
			$kqjsJdRatingsTbl = $objCustom->getKPAKQJSTable($kqjsJdRatings,$kqnum,$tableNum++);			
			$pdf->writeHTMLCell ( 0, 0, '', '', $kqjsJdRatingsTbl, 0, 1, 0, true, 'J', true );
                        $kqjsJdRatingsTbl = $objCustom->getKPARatingsTable($kqjsJdRatings,$kqnum,$tableNum++,$diagnostic_id);
                $pdf->writeHTMLCell ( 0, 0, '', '', $kqjsJdRatingsTbl, 0, 1, 0, true, 'J', true );
			/* for($numcq=3*$kqnum-2;$numcq<=3*$kqnum;$numcq++){								
			} */
		}		
		
		$pdf->AddPage();	
		$k++;
}


//$pdf->AddPage ();
// set a bookmark for the current position
$mainHeadingTxt = '. Assessor key recommendations for every school KPA wise';
$pdf->Bookmark ( $mainHeadingNum.$mainHeadingTxt, 0, 0, '', 'B', array (
		0,0,0
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->Cell ( 0, 8, $mainHeadingNum++.$mainHeadingTxt, 0, 1, 'C', true );
$k=1;
$point = 1;
$pdf->SetFont ( 'times', '', 10 );
$pdf->SetTextColor ( 0, 0, 0 );
foreach($kpaNames as $kpa){	
	$pdf->ln(1);	
	$clients = $objCustom->getClients($diagnostic_id,$kpaIds['kpa'.$k]);
	$table = '<table  cellpadding="3" nobr="true"><thead><tr><th colspan="3" style="text-align:center;">Table '.$tableNum++.' - Recommendations on KPA'.$k.': '.$kpaNames['kpa'.$k].' </th></tr><tr><th style="width:20%;font-weight:bold;border:solid 1px #000000;">School Name</th><th style="width:40%;font-weight:bold;border:solid 1px #000000;">Recommendation-Celebrate</th><th style="width:40%;font-weight:bold;border:solid 1px #000000;">Recommendation-Improvement</th></tr></thead>';
	foreach($clients as $row){
		$table .= '<tr><td style="width:20%;border:solid 1px #000000;">'.$row['client_name'].'</td>';
		$externalCelebrate = $objCustom->getExternalAssessorRecommendations($diagnostic_id,$kpaIds['kpa'.$k],'celebrate',$row['client_id']);		
		$externalImprove = $objCustom->getExternalAssessorRecommendations($diagnostic_id,$kpaIds['kpa'.$k],'recommendation',$row['client_id']);
		$table .= '<td style="width:40%;border:solid 1px #000000;">'.(!empty($externalCelebrate['recommendation'])?$externalCelebrate['recommendation']:'').'</td>'.'<td style="width:40%;border:solid 1px #000000;">'.(!empty($externalImprove['recommendation'])?$externalImprove['recommendation']:'').'</td>';
		$table .= '</tr>';
	}
	$table .= '</table><br/>';
	$k++;
	//echo $table;
	$pdf->writeHTMLCell ( 0, 0, '', '', $table, 0, 1, 0, true, '', true );
	//$pdf->AddPage ();
}

if($report_point3==1){
$pdf->AddPage ();
// set a bookmark for the current position
$mainHeadingTxt = '. Post review form data ';
$pdf->Bookmark ( $mainHeadingNum.$mainHeadingTxt, 0, 0, '', 'B', array (
		0,0,0
) );
$diagnosticModel = new diagnosticModel();
$postRevFields = $diagnosticModel->getCommentsFieldPostReview();
//print_r($objCustom->getPostReviewDAta());die;
//create table for post review form
$postTable = '';
/* foreach($postRevFields as $key=>$field){
 $postTable[$key] = '<td>'.$field['COLUMN_COMMENT'].'</td>';
 }
 print_r($postTable);
 die; */
//show post-review questions

//print_r($postRevFields);die;
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->Cell ( 0, 8, $mainHeadingNum++.$mainHeadingTxt, 0, 1, 'C', true );
reset($clients);
$pdf->SetFont ( PDF_FONT_MONOSPACED, '', 9 );
$pdf->SetTextColor ( 0, 0, 0 );
$postReviewData = $objCustom->getPostReviewDAta();
$i=0;
$table = '';
$totalschools = count($postReviewData);
$schoolsCovered = 0;
//$margins = $pdf->getMargins();
//$mainWidth = $pdf->getPageWidth()-$margins['left']-margins['right'];
$x=0;
$y=0;
$pdf->Ln(2);
foreach($postReviewData as $row){
	$schoolsCovered++;
	//$totalschools<(++$schoolsCovered)
	//show  at most 3 schools in one line
	//($totalschools-3)==++$schoolsCovered?($width = ):($width=25)	
		//$width = 25;
	//$width = ($totalschools - $schoolsCovered < 3 && $totalschools%3>0)? ($mainWidth/($schoolsCovered+1)): $mainWidth/4;		;
	//echo "totalsch: $totalschools covered: $schoolsCovered schoolsCovered mod 3: ".($schoolsCovered%3);
	if($totalschools - $schoolsCovered ==0 && $schoolsCovered%3==1)
			$width = 50;		
	elseif($totalschools - $schoolsCovered ==1 && $schoolsCovered%3==1)
			$width = 33;		
	elseif($totalschools - $schoolsCovered>=2)
		$width=25;
	if(++$i==1){	
	$table = '<table><tr><td style="text-align:center;" colspan="'.($width==25?4:($width==33?3:2)).'">Table '.$tableNum++.'</td></tr><tr><td style="width:'.$width.'%"><table border="1" cellpadding="3" ><tr>';
	 $table .='<td><pre><b>'.str_pad('School Name',50,' ').'</b></pre></td></tr>';	 
	 $table .='<tr><td><pre>'.$postRevFields['decision_maker']['COLUMN_COMMENT'].'</pre></td></tr>';	 	 	 
	 $table .='<tr><td><pre>'.$postRevFields['management_engagement']['COLUMN_COMMENT'].'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.$postRevFields['principal_involvement']['COLUMN_COMMENT'].'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.$postRevFields['principal_openness']['COLUMN_COMMENT'].'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.$postRevFields['action_management_decision']['COLUMN_COMMENT'].'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.$postRevFields['principal_tenure']['COLUMN_COMMENT'].'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad($postRevFields['principal_vision']['COLUMN_COMMENT'],30,' ').'</pre></td></tr>';
         $table .='<tr><td><pre>'.$postRevFields['average_staff_tenure']['COLUMN_COMMENT'].'</pre></td></tr>';
         
	 	 
	 $table .='<tr><td><pre>'.$postRevFields['parent_teacher_association']['COLUMN_COMMENT'].'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.$postRevFields['alumni_association']['COLUMN_COMMENT'].'</pre></td></tr>';
	 $table .='<tr><td><pre>'.$postRevFields['student_body_activity']['COLUMN_COMMENT'].'</pre></td></tr>';
	 $table .='<tr><td><pre>'.$postRevFields['middle_leaders']['COLUMN_COMMENT'].'</pre></td></tr>';
         
	 //$table .='<tr><td><pre>'.$postRevFields['average_number_students_class']['COLUMN_COMMENT'].'</pre></td></tr>';
	 $table .='<tr><td><pre>'.$postRevFields['ratio_students_class_size']['COLUMN_COMMENT'].'</pre></td></tr>';
         $table .='<tr><td><pre>'.$postRevFields['rte']['COLUMN_COMMENT'].'</pre></td></tr>';
         $table .='<tr><td><pre>'.$postRevFields['student_count']['COLUMN_COMMENT'].'</pre></td></tr>';
         $table .='<tr><td><pre>'.str_pad('Avg. students in single class',110,' ').'</pre></td></tr>';
         $table .='<tr><td><pre>'.str_pad('Avg. teachers in single class',90,' ').'</pre></td></tr>';
         
	 $table .='<tr><td><pre>'.str_pad($postRevFields['number_teaching_staff']['COLUMN_COMMENT'],90,' ').'</pre></td></tr>';
	 $table .='<tr><td><pre>'.$postRevFields['number_non_teaching_staff_prep']['COLUMN_COMMENT'].'</pre></td></tr>';
	 $table .='<tr><td><pre>'.$postRevFields['number_non_teaching_staff_rest']['COLUMN_COMMENT'].'</pre></td></tr></table></td>';
	 
	}
	$table .= '<td style="width:'.$width.'%"><table border="1" cellpadding="3" ><tr>';	
	 $table .='<td><pre><b>'.str_pad(ucfirst($row['client_name']),50,' ').'</b></pre></td></tr>';	
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['decision_maker']).($row['decision_maker_other']? ' - '.$row['decision_maker_other']:''),strlen($postRevFields['decision_maker']['COLUMN_COMMENT'])+strlen($row['decision_maker_other']),' ').'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['management_engagement']),strlen($postRevFields['management_engagement']['COLUMN_COMMENT']),' ').'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['principal_involvement']),strlen($postRevFields['principal_involvement']['COLUMN_COMMENT']),' ').'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['principal_openness']),strlen($postRevFields['principal_openness']['COLUMN_COMMENT']),' ').'</pre></td></tr>';	 	 	
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['action_management_decision']),strlen($postRevFields['action_management_decision']['COLUMN_COMMENT']),' ').'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['principal_tenure']),strlen($postRevFields['principal_tenure']['COLUMN_COMMENT']),' ').'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['principal_vision']),30,' ').'</pre></td></tr>';
         $table .='<tr><td><pre>'.str_pad(ucfirst($row['average_staff_tenure']),strlen($postRevFields['average_staff_tenure']['COLUMN_COMMENT']),' ').'</pre></td></tr>';
	 	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['parent_teacher_association']),strlen($postRevFields['parent_teacher_association']['COLUMN_COMMENT']),' ').'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['alumni_association']),strlen($postRevFields['alumni_association']['COLUMN_COMMENT']),' ').'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['student_body_activity']),strlen($postRevFields['student_body_activity']['COLUMN_COMMENT']),' ').'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['middle_leaders']),strlen($postRevFields['middle_leaders']['COLUMN_COMMENT']),' ').'</pre></td></tr>';
         
	 //$table .='<tr><td><pre>'.str_pad(ucfirst($row['average_number_students_class']),strlen($postRevFields['average_number_students_class']['COLUMN_COMMENT']),' ').'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['ratio_students_class_size']),strlen($postRevFields['ratio_students_class_size']['COLUMN_COMMENT']),' ').'</pre></td></tr>'; 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['rte']),strlen($postRevFields['rte']['COLUMN_COMMENT']),' ').'</pre></td></tr>';
         $table .='<tr><td><pre>'.str_pad(ucfirst($row['student_count']),strlen($postRevFields['student_count']['COLUMN_COMMENT']),' ').'</pre></td></tr>';
         $table .='<tr><td><pre>'.str_pad(ucfirst($row['number_students_class']),110,' ').'</pre></td></tr>';
         $table .='<tr><td><pre>'.str_pad(ucfirst($row['number_teachers_class']),90,' ').'</pre></td></tr>';
         $table .='<tr><td><pre>'.str_pad(ucfirst($row['number_teaching_staff']),90,' ').'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['number_non_teaching_staff_prep']),strlen($postRevFields['number_non_teaching_staff_prep']['COLUMN_COMMENT']),' ').'</pre></td></tr>';	 
	 $table .='<tr><td><pre>'.str_pad(ucfirst($row['number_non_teaching_staff_rest']),strlen($postRevFields['number_non_teaching_staff_rest']['COLUMN_COMMENT']),' ').'</pre></td></tr></table></td>';	 

	// $pdf->writeHTMLCell ( 0, 0, '', '', $table, 0, 1, 0, true, 'J', true );	  
	 //$i==3?$i=0:'';
	  if($i==3 || $totalschools==$schoolsCovered){
		  $i=0;
		  $table.= '</tr></table>';
		  $block = '<div style="page-break-inside:avoid;">'.$table.'</div>';	 
		$pdf->writeHTMLCell ( 0, 0, '', '', $block, 0, 1, 0, true, '', true );
		//echo $table;
	  }
	 
	 // $block = '<div style="page-break-inside:avoid;">'.$table.'</div>';
	  //$block = 'dsfdsf';
	  //$pdf->writeHTMLCell ( 0, 0, '', '', $block, 0, 1, 0, true, 'J', true );
	  //echo 'hi'.$block; */
	// echo $table;
}

}
//die;
reset($clients);

$pdf->AddPage ();
// set a bookmark for the current position
$mainHeadingTxt = '. Areas identified for planning by the school';
$pdf->Bookmark ( $mainHeadingNum.$mainHeadingTxt, 0, 0, '', 'B', array (
		0,0,0
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->Cell ( 0, 8, $mainHeadingNum++.$mainHeadingTxt, 0, 1, 'C', true );
$pdf->Ln(1);
$pdf->SetFont ( 'times', '', 10 );
$pdf->SetTextColor ( 0, 0, 0 );
reset($clients);

foreach($clients as $row){
	$clientActionPlanning = $objCustom->getActionPlanningData($row['client_id']);
       // print_r($clientActionPlanning);
	if(!empty($clientActionPlanning)){
        $table='<table cellpadding="3"><thead><tr><th style="text-align:center;" colspan="4">Table '.$tableNum++.' - '.$row['client_name'].'</th></tr><tr><th style="border:1px solid #000;"><b>KPA</b></th><th style="border:1px solid #000;"><b>Key Question</b></th><th style="border:1px solid #000;"><b>Sub Question</b></th><th style="border:1px solid #000;"><b>Action planning area chosen by the school</b></th></tr></thead>';        
        foreach($clientActionPlanning as $key=>$row){
            $cqs = explode('~',$row['core_question_text']);
           // $cqText = implode('<br/>',$cqs);
            $sequence = 1;
           foreach($cqs as $k=>&$v)
               $v = ($sequence++).'. '.$v;
           unset($v);
             $cqText = implode('<br/>',$cqs);
            $table .='<tr><td style="border:1px solid #000000;">'.$row['kpa_name'].'</td><td style="border:1px solid #000;">'.$row['key_question_text'].'</td><td style="border:1px solid #000;">'.$cqText.'</td><td style="border:1px solid #000;">'.$row['action_planning'].'</td></tr>';
        }
           $table .='</table><br/><br/>' ;	
	$pdf->writeHTMLCell ( 0, 0, '', '', $table, 0, 1, 0, true, '', true );
	}	
}
if($report_point5==1){
$pdf->AddPage ();
// set a bookmark for the current position
$mainHeadingTxt = '. Other information/Comments about the school';
$pdf->Bookmark ( $mainHeadingNum.$mainHeadingTxt, 0, 0, '', 'B', array (
		0,0,0
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->Cell ( 0, 8, $mainHeadingNum++.$mainHeadingTxt, 0, 1, 'C', true );
$pdf->Ln(1);
//action planning text field from post review form
reset($clients);
$pdf->SetFont ( 'times', '', 10 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->Ln(2);
foreach($clients as $row){
	$clientComments = $objCustom->getPostReviewComments($row['client_id']);
	if(!empty($clientComments)){
	$block = '<div style="page-break-inside:avoid;"><b>'.$row['client_name'].'</b>:<br/><br/>'.$clientComments['comments'].'</div><br/>';	
	//$block = 'dsfdsf';
	$pdf->writeHTMLCell ( 0, 0, '', '', $block, 0, 1, 0, true, 'J', true );
	}
	//echo 'hi'.$block;
}

}
$pdf->addTOCPage ();
// write the TOC title and/or other elements on the TOC page
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->MultiCell ( 0, 0, 'Table Of Contents', 1, 'C', true, 1, '', '', true, 1 );



// $pdf->Ln();
$pdf->SetFont ( 'times', '', 10 );

$pdf->Ln ( 5 );
$pdf->SetTextColor ( 108, 13, 16 );
$pdf->SetFont ( 'times', '', 15 );
$pdf->Write ( 0,'Table 1', '', 0, 'C', true, 0, false, false, 0 );
$pdf->Ln ( 2 );
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->addTOC(2, PDF_FONT_MONOSPACED, '.', '', 'B', array(0,0,0));
$path = ROOT.'reports';
// Close and output PDF document
//$pdf->Output ($path.'/'.$network_report_name.'-datasummary.pdf', 'FI' );
$pdf->Output ($path.'/'.$network_report_name.'-datasummary.pdf', 'I' );