<?php
include ROOT . 'library' . DS . 'tcpdf' . DS . 'tcpdf.php';
ini_set('memory_limit','50M');
set_time_limit(0);
ini_set('max_execution_time', 0);
class TOC_TCPDF extends TCPDF {
	public $top_margin = 20;
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
		
		if ($this->tocpage) {
			// *** replace the following parent::Footer() with your code for TOC page
			parent::Footer ();
		} else {
			// *** replace the following parent::Footer() with your code for normal pages
			$this->bottom_margin = $this->GetY() + 5;
			parent::Footer ();
		}
	}
}
// end of class
// create new PDF document
$pdf = new TOC_TCPDF ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
$pdf->SetMargins(15, $pdf->top_margin, 15);
$kpaGraph = array ();
$kpaPercGraph = array ();
$kpaRating = array ();
$i = 0;
$graphNum = 1;
$tableNum = 2;
$performaceQstring = "?";
$prev_kpa_id = 0;
$kpaWeightage = array("kpa1"=>0,"kpa2"=>0,"kpa3"=>0,"kpa4"=>0,"kpa5"=>0,"kpa6"=>0);
$kpaNames = array();
$kpaAcronyms = array("kpa1"=>0,"kpa2"=>0,"kpa3"=>0,"kpa4"=>0,"kpa5"=>0,"kpa6"=>0);
$kpaIds = array("kpa1"=>0,"kpa2"=>0,"kpa3"=>0,"kpa4"=>0,"kpa5"=>0,"kpa6"=>0);
//create uri to pass to get image
foreach ( $performaceKPAs as $val ) {	
	if ($i == 0 || $prev_kpa_id != $val ['kpa_id']) {
		$i > 0 ? ($performaceQstring .= "&") : '';
		$performaceQstring .= "kpa$i=name_" . urlencode ( $val ['kpa_name'] ) . ";" . str_replace ( " ", '', $val ['rating'] ) . "_" . $val ['num'];		
		$i ++;		
	} else {
		$performaceQstring .= ";" . str_replace ( " ", '', $val ['rating'] ) . "_" . $val ['num'];
	}
	$prev_kpa_id = $val['kpa_id'];
	$kpaNames["kpa$i"] = $val ['kpa_name'];
	$kpaIds["kpa$i"] = $val['kpa_id']; 
		if(preg_match_all('/\b(\w)/',strtoupper($val['kpa_name']),$m) && !in_array(strtolower($val['kpa_name']),array('the child','the curriculum')) )
			$kpaAcronyms["kpa$i"] = implode(' & ',$m[1]);		
		elseif(strtolower($val['kpa_name'])=='the child')//removing the in acronyms
			$kpaAcronyms["kpa$i"] = $val['kpa_name'];
		elseif(strtolower($val['kpa_name'])=='the curriculum')
		$kpaAcronyms["kpa$i"] = 'Curriculum';
		
	$kpaWeightage["kpa$i"] += $objCustom->getWeightage($val ['percentage'],$val ['rating_id']);	
}
//Kpa wise cq ratings and JD
$x=0;
foreach($kpaIds as $val) {
	$x++;
	$kpaCQrating [$x] = $objCustom->getKpaCQrating ( $val );	
	$kpaJDarr [$x] = $objCustom->getKpaJd ( $val );
}

//kpa7 remove the in acronyms
$kpa7nametemp = $kpa7Name?preg_replace('/\b(THE)/','',strtoupper($kpa7Name)):'';
if($kpa7nametemp!='' && preg_match_all('/\b(\w)/',strtoupper($kpa7nametemp),$m) && !in_array(strtolower($kpa7nametemp),array('the child','the curriculum')) )
	$kpaAcronyms["kpa7"] = implode('',$m[1]);
 
$performaceQstring .= "&num_of_schools=".$num_of_schools;
//print_r($kpaNames);
//echo $performaceQstring;
//die;
// set document information
// $pdf->SetCreator(PDF_CREATOR);
// $pdf->SetAuthor('Nicola Asuni');
 $pdf->SetTitle($network_report_name);
// $pdf->SetSubject('TCPDF Tutorial');
// $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
$pdf->SetHeaderData ( '', '', 'Adhyayan Quality Standard Award: Report for ' . $network_report_name );
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
$pdf->SetMargins ( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
$pdf->SetHeaderMargin ( PDF_MARGIN_HEADER );
$pdf->SetFooterMargin ( PDF_MARGIN_FOOTER );

// set auto page breaks
$pdf->SetAutoPageBreak ( TRUE, PDF_MARGIN_BOTTOM );

// set image scale factor
$pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );

// set some language-dependent strings (optional)
if (@file_exists ( dirname ( __FILE__ ) . '/lang/eng.php' )) {
	require_once (dirname ( __FILE__ ) . '/lang/eng.php');
	$pdf->setLanguageArray ( $l );
}

// set font
$pdf->SetFont ( 'times', '', 10 );

// ---------------------------------------------------------
// create some content ...
// add a page
// first page start
$pdf->addPage ();
if($diagnostic_id==1)//for Don Bosco diagnostic
	$firstpagehtml = '<br/><table class="pdfHdr broad"> <tbody><tr><td class="halfSec fl" align="left"><a href=""><img src="' . SITEURL . 'public/images/logo.png"alt="" style="height:80px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="./uploads/diagnostic/diagnostic_image_1.jpg" style="height:80px;" alt=""></a></td></tr><tr style="text-align:center;padding:2px;font-weight:bold;font-family: CalibriRegular,Verdana,sans-serif;font-size:10px;"><td colspan="2">Don Bosco School Self-Review and Evaluation Programme (DBSSRE) conducted under the aegis of \'Don Bosco for Excellence\'</td></tr> </tbody></table>' . '<br/><br/>';
else
	$firstpagehtml = '<br/><div style="text-align:center"><img src="' . SITEURL . 'public/images/logo.png"></div>' . '<br/><br/>' ;
$firstpagehtml.='<div style="text-align:center;font-size:18;">Adhyayan Quality Standard Award (AQS) Network Overview Report:<span style="font-weight:bold;">'.$network_report_name.'</span></div>' . '<br/>' . '<div style="text-align:center;font-size:14;">Schools reviewed between ' . date_format ( date_create ( $dates ['startdate'] ), "d M, Y" ) . ' to ' . date_format ( date_create ( $dates ['enddate'] ), "d M, Y" ) . '</div>' . '<br/>' . '<div style="text-align:center;font-size:18;font-weight:bold">' . date ( 'F Y' ) . '</div>' . '<br/>' . '<div style="text-align:center;font-size:15;font-weight:bold;background-color: #6c0d10;color:#fff;">An Overview</div>' . '<p style="font-size:15;">In this report:</p>' . '<div>
			<ol> <li>The quality standard award process and the school awards</li>
				<li>Adhyayan Recommendations</li>
				<li>Performance of the schools on the Key Performance Areas (KPA)
					<ul>
						<li>Categorised by states</li>
						<li>Categorised by examination boards</li>
						<li>Categorised by school fees</li>
					</ul>
				</li>
				<li>Cumulative report for all 6 KPAs across all schools</li>
				<li>School effectiveness in applying the school review diagnostic</li>
			</ol></div>';
// Print text using writeHTMLCell()

$firstpagehtml .= '<div style="text-align:center;font-size:15;font-weight:bold;background-color: #6c0d10;color:#fff;">Glossary of Terms</div>';

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

// section 1 page start
$pdf->AddPage ();
// $pdf->writeHTML($secondpagehtml, true, false, false, false, '');
// $pdf->AddPage();

// set a bookmark for the current position
$pdf->Bookmark ( '1. The Adhyayan Quality Standard (AQS) Process and Award', 0, 0, '', 'B', array (
		0,
		64,
		128 
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->Cell ( 0, 8, '1. The Adhyayan Quality Standard (AQS) Process and Award', 0, 1, 'C', true );
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section1_html1 = 'Adhyayan undertook the review of ' . $num_of_schools . ' schools of ' . $network_report_name . ' ' . 'spread across '.$num_of_states.' state/s. '.$stringStateSpread.'. Adhyayan worked alongside the schools\' SSRE teams to facilitate their understanding of \'What Good Schools Look ' . 'Like\' across the world through the Adhyayan review process explained below.';
$pdf->Write ( 0, $section1_html1, '', 0, '', true, 0, false, false, 0 );
$pdf->SetFont ( 'times', 'B', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
//$section1_html2 = "1. AQS Orientation:\n";
//$pdf->Write ( 0, $section1_html2, '', 0, '', true, 0, false, false, 0 );
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
//$section1_html3 = 'Adhyayan oriented the SSRE team of each school on how to review their own performance on six Key Performance Areas ' . "(KPAs): Leadership and Management (L&M); Teaching and Learning (T&L); The Child, The Curriculum, Community and Partnerships,(C&P), and " . "Infrastructure and Resources (I&R), using a diagnostic based on the AQS. Guidance to the school's review team included: " . "(a) a reading of what good looks like for each KPA (b) how to collect evidence through four methods1 which form the basis " . "of their evaluation of school effectiveness (c) the schedule for the 2 day self-review process and the deployment of the SSRE team members. ";
$section1_html3 = '<b>1. AQS Orientation:</b><br/>Adhyayan oriented the SSRE team of each school on how to review their own performance on six Key Performance Areas ' . "(KPAs): Leadership and Management (L&M); Teaching and Learning (T&L); The Child, The Curriculum, Community and Partnerships,(C&P), and " . "Infrastructure and Resources (I&R), using a diagnostic based on the AQS. Guidance to the school's review team included: " . "(a) a reading of what good looks like for each KPA (b) how to collect evidence through four methods<sup>1</sup> which form the basis " . "of their evaluation of school effectiveness (c) the schedule for the 2 day self-review process and the deployment of the SSRE team members. ";
$pdf->Image ( SITEURL . 'public/images/aqs-image.png', 140, $pdf->GetY(), '', 50, 'PNG', '', '', true );
//$pdf->Write ( 0, 'table 1', '', 0, '', true, 0, false, false, 0 );
/* $regions = array (
		array (
				'page' => '',
				'xt' => 150,
				'yt' => 30,
				'xb' => 150,
				'yb' => 100,
				'side' => 'R' 
		) 
); */
//$pdf->writeHTMLCell ( 0, 120, '', '','<div style="text-align:center;font-size:15;font-weight:normal;color:#6c0d10">Table 2</div>', 0, 1, 0, true, 'J', true );
$pdf->Ln ( 2 );
$pdf->writeHTMLCell ( 120, 0, '', '',$section1_html3, 0, 1, 0, true, 'J', true );
$section1_html4 = '1 The evidences are collected through 4 methods : Learning walk, Classroom Observation, Book Look and Interviews ';
$pdf->SetFont ( 'times', '', 10 );
$pdf->Ln ( 2 );
$pdf->Write ( 0, $section1_html4, '', 0, '', true, 0, false, false, 0 );
$pdf->Ln ( 2 );
$pdf->SetFont ( 'times', '', 12 );
$pdf->writeHTMLCell ( 120, 0, 40, '','<span style="text-align:center;font-size:12;color:#6c0d10;font-weight:normal;">Table '.$tableNum++.'</span>', 0, 1, 0, true, 'C', true );
//$pdf->Ln ( 2 );
$pdf->writeHTMLCell ( 170, 0, 15, '','<img src= "'.SITEURL . 'public/images/6-days-process.jpg'.'" />', 0, 1, 0, true, 'C', true );
//$pdf->Image ( SITEURL . 'public/images/6-days-process.jpg', 10, $pdf->GetY(), 180, '', 'JPG', '', '', true );
$pdf->Ln ( 2 );
$pdf->SetTextColor ( 108, 13, 16 );
$pdf->SetFont ( 'times', '', 15 );
$pdf->Ln ( 2 );
//$pdf->Cell ( 0, 15, 'Table 2', 0, 1, 'C', true );
$pdf->SetFont ( 'times', '', 10 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->Ln ( 2 );

//$pdf->AddPage();

$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section1_html6 = "<b>2. Quality Dialogue:</b><br/>Following the SSRE and SERE team's review of the ".$num_of_schools." schools, both sets of teams did the following:
        <ul>
            <li>Shared their judgments on each of the KPAs and the evidence on which they were based, and discussed the similarities and differences
                in their evaluation of each school's performance.</li>
            <li>Identified key areas for celebration and improvement based on the external review scores.</li>
            <li>Shared examples of good practice where an individual school's performance had been evaluated as being variable or less 
                by the external review team.</li>
        </ul>";
$pdf->writeHTMLCell ( 0, 0, '', $pdf->GetY(), '<div style="page-break-avoid:inside;">'.$section1_html6.'</div>', 0, 1, 0, true, 'J', true );
//$pdf->Ln ( 2 );
$awardSchemeTable="";
$awardTableNum = $tableNum;
if($award_scheme_id==1){
$awardSchemeTable = '<span style="text-align:center;font-size:12;color:#6c0d10;font-weight:normal;">Table '.$tableNum++.'</span><br/><table width="100%" style="border:1px solid #c0c0c0;" cellpadding="1">';
$awardSchemeTable .='<tr><td colspan="2" align="center" style="height:20px;background-color:#6c0d10;color:#ffffff;font-weight:bold;text-align:center;">Adhyayan Quality Standard Definitions:</td></tr>
		<tr><td style="width:7%"><img src="'.SITEURL . 'public/images/reports/platinum.png'.'" /></td><td style="width:93%"><b>Platinum</b>: The Platinum award confirms that school\'s performance is outstanding on most KPAs as measured against the chosen tier.</td></tr>
		<tr><td><img src="'.SITEURL . 'public/images/reports/gold.png'.'" /></td><td><b>Gold</b>: Gold is the bedrock of Adhyayan Quality Standard. The Gold award confirms that the school\'s performance is good and strong on most KPAs.</td></tr>
		<tr><td><img src="'.SITEURL . 'public/images/reports/silver.png'.'" /></td><td><b>Silver</b>: The Silver award indicates that the school\'s practice is variable. The Silver award confirms that while some aspects of the school\'s performance may 
		be good, others may be satisfactory or less.</td></tr>
		<tr><td><img src="'.SITEURL . 'public/images/reports/bronze.png'.'" /></td><td><b>Bronze</b>: The Bronze award is an entry grade for schools aspiring to become high performing. While the school confirms that certain aspects of its 
		performance within the KPAs are at least satisfactory, it is working towards a greater consistency.</td></tr>
		';
$awardSchemeTable .="</table>";
$pdf->writeHTMLCell ( 0, 0, '', '', $awardSchemeTable, 0, 1, 0, true, 'J', true );
}
else 
{
$awardSchemeTable = '<span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10;">Table '.$tableNum++.'</span><br/><table width="100%" cellpadding="4">';
$awardSchemeTable .='
		<tr><td colspan="3" style="background-color:#6c0d10;color:#ffffff;font-weight:bold;text-align:center;">Adhyayan Quality Standard Award</td></tr>
		<tr style="background-color:#6c0d10;color:#ffffff;font-weight:bold;text-align:center;"><td>Getting to Good</td><td>Good schools</td><td>Outstanding schools</td></tr>
		<tr style="background-color:#B79493;text-align:center;"><td>O</td><td>O+</td><td>O++</td></tr>
		<tr style="background-color:#DEC0BF;text-align:center;"><td>A</td><td>A+</td><td>A++</td></tr>
		<tr style="background-color:#B79493;text-align:center;"><td>B</td><td>B+</td><td>B++</td></tr>
		<tr style="background-color:#DEC0BF;text-align:center;"><td colspan="3">C (Entry Level)</td></tr>
		';
$awardSchemeTable .='</table>';
$pdf->writeHTMLCell ( 0, 0, '', '', $awardSchemeTable, 0, 1, 0, true, 'J', true );
$pdf->Ln ( 2 );
$awardSchemeTable = 
		'<span style="text-align:center;font-size:12;color:#6c0d10;font-weight:normal;">Table '.$tableNum++.'</span><br/><table width="100%" border="1" cellpadding="4" style="margin-top:10px;">
		<tr style="background-color:#6c0d10;color:#ffffff;font-weight:bold;text-align:center;"><td style="width:10%">Key</td><td style="width:30%">What it means</td><td style="width:30%">+</td><td style="width:30%">++</td></tr>
		<tr ><td style="text-align:center;">O</td><td>The school\'s performance is outstanding on most KPAs as measured against their chosen tier.</td><td rowspan="3">When the school has scored \'good\' in the KPAs of either \'L & M\' and/or \'T & L\'.</td><td rowspan="3">Apart from scoring \'+\', when the school also scores an \'Outstanding\' in any one of the KPA</td></tr>
		<tr><td style="text-align:center;">A</td><td>The school\'s performance is good and strong on most KPAs.</td></tr>
		<tr><td style="text-align:center;">B</td><td>The school\'s practices are variable. The award confirms that while some aspects of the school\'s performance may be good, others may be satisfactory or less.</td></tr>
		<tr><td style="text-align:center;">C</td><td colspan="3">The award is an entry grade for schools aspiring to become high performing. While the school confirms that certain aspects of its performance within the KPAs are at least satisfactory, it is working towards greater consistency.</td></tr>
		</table>
		';
$pdf->writeHTMLCell ( 0, 0, '', '', $awardSchemeTable, 0, 1, 0, true, 'J', true );
}
//$pdf->Image ( SITEURL . 'public/images/Page-4-Image-4.jpg', 20, 75, 170, 80, 'JPG', '', '', true );


$pdf->Ln ( 5 );
/* $pdf->SetFont ( 'times', 'B', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section1_html7 = "3. Adhyayan Quality Standard Award:\n";
$pdf->Write ( 0, $section1_html7, '', 0, '', true, 0, false, false, 0 );
$pdf->Ln ( 2 ); */
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section1_html8 = "<b>3. Adhyayan Quality Standard Award:</b><br/>Based on each SERE team's scores, the Adhyayan Quality Standard Award was given to the school. The initial review provides
        the baseline for the school's future performance, this enables each school's progress to be tracked and then assessed against the baseline
        assessment through its subsequent two yearly reviews.";
$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;">'.$section1_html8.'</div>', 0, 1, 0, true, 'J', true );
//$pdf->AddPage();
//$pdf->SetFont ( 'times', 'B', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
//$section1_html9 = "4. Action Planning:\n";
//$pdf->Write ( 0, $section1_html9, '', 0, '', true, 0, false, false, 0 );
$pdf->Ln ( 2 );
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section1_html10 = "<b>4. Action Planning:</b><br/>The final step in the review process involves planning for improvement. After studying the contents of the Report Card,
        paying special attention to the judgments of the external review team, the self-review teams were then asked to identify a few
        important issues from each KPA that they were convinced needed to be addressed. From this list the SSRE team chose one or two areas
        to begin planning how to implement. As a first step the SSRE teams of each school learnt how to 'Ripple Plan'. This planning tool is particularly
        helpful in enabling the school's leadership to understand the critical roles of stakeholders in the process of school improvement and 
        the key stages of action planning. By the end of the face to face planning process school's leadership team was clear about which areas 
        they want to focus for planning and implementation.";
$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;">'.$section1_html10.'</div>', 0, 1, 0, true, 'J', true );
/* $pdf->Ln ( 2 );
$pdf->SetFont ( 'times', 'B', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section1_html11 = "5. Recommendation Report:\n";
$pdf->Write ( 0, $section1_html11, '', 0, '', true, 0, false, false, 0 );
$pdf->Ln ( 2 ); */
$pdf->Ln ( 2 );
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section1_html12 = "<b>5. Recommendation Report:</b><br/>Following the completion of the School Review Process, Adhyayan provides the school with a comprehensive report of 
        recommendations for improvement. The Adhyayan external review team identifies from each of the Key Performance Areas, one strength
        for celebration and one area for development.";

$pdf->writeHTMLCell ( 0, 0, '', '',  '<div style="page-break-inside:avoid;">'.$section1_html12.'</div>', 0, 1, 0, true, 'J', true );
// section 1 page end

// section 2 page start
if(!empty($experience)){
	$pdf->AddPage ();
	// set a bookmark for the current position
	$pdf->Bookmark ( "2. Recommendations Overview ", 0, 0, '', 'B', array (
			0,
			64,
			128 
	) );
	$pdf->SetFont ( 'times', 'B', 16 );
	$pdf->SetTextColor ( 255, 255, 255 );
	$pdf->SetFillColor ( 108, 13, 16 );
	// print a line using Cell()
	$pdf->Cell ( 0, 10, '2. Recommendations Overview ', 0, 1, 'C', true );
	
	$pdf->Ln ();
	
	$section2_html1 = '
	<div>
	    <table width="100%" >';
	
	foreach ( $experience as $value ) {
		$section2_html1 .= '<tr>
	                        <td width="12%" style="color:#6c0d10;font-size:12" align="center" valign="top">
	                        <img src="' . SITEURL . 'public/images/tick.png" width="25" height="25">
	                        </td>
	                        <td width="88%" style="font-size:12" align="left">' . $value . '<br/></td>
	                    </tr>';
	}
	$section2_html1 .= '</table>
	</div>
	';
	$pdf->SetFont ( 'times', '', 12 );
	$pdf->SetTextColor ( 0, 0, 0 );
	$pdf->writeHTMLCell ( 0, 0, '', '', $section2_html1, 0, 1, 0, true, 'J', true );
// section 2 page end
}
// section 3 page start
$pdf->AddPage ();
// set a bookmark for the current position
$pdf->Bookmark ( "3. The Adhyayan Quality Standard Awarded", 0, 0, '', 'B', array (
		0,
		64,
		128 
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
// print a line using Cell()
$pdf->Cell ( 0, 10, '3.The Adhyayan Quality Standard Awarded', 0, 1, 'C', true );
$pdf->SetFont ( 'times', 'B', 12 );
if (! empty ( $schoolAwards )) {
	$section3_html1 = '
	<div style="text-align:center;font-size:12;font-weight:bold;">
			<span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum++.'</span><br/>
			AQS Awards presented to the '.$network_report_name.' Schools</div>		
		    
       <table cellpadding="4">';
	$i = 1;	
	$section3_html1 .= '<thead>
						<tr style="background-color:#c0c0c0;text-align:center;border:1px solid #000000;">						                      
                        <th style="font-size:11;border:1px solid #000000;" align="center" width="16%">School Name</th>
						<th style="font-size:11;border:1px solid #000000;" align="center" width="11%">State</th>
                        <th style="font-size:11;border:1px solid #000000;" align="center" width="10%">Location</th>
						<th style="font-size:11;border:1px solid #000000;" align="center" width="12%">Medium of Instruction</th>
						<th style="font-size:11;border:1px solid #000000;" align="center" width="8%">Board</th>
						<th style="font-size:11;border:1px solid #000000;" align="center" width="13%">Annual Fees</th>
						<th style="font-size:11;border:1px solid #000000;" align="center" width="14%">Type of School</th>
                        <th style="font-size:11;border:1px solid #000000;" align="center" width="16%">Award<span style="color:#FF0000;">*</span></th>
                    	</tr>
						</thead>';
	foreach ( $schoolAwards as $value ) {
		$section3_html1 .= '<tr style="border:1px solid #000000;">                           
                            <td style="font-size:11;border:1px solid #000000;" align="left"  width="16%">' . $value ['client_name'] . '</td>
							<td style="font-size:11;border:1px solid #000000;" align="left"  width="11%">' . $value ['state_name'] . '</td>                            		
                            <td style="font-size:11;border:1px solid #000000;" align="left"  width="10%">' . $value ['region_name'] . '</td>
                           	<td style="font-size:11;border:1px solid #000000;" align="left"  width="12%">' . $value ['language_name'] . '</td>
							<td style="font-size:11;border:1px solid #000000;" align="left"  width="8%">' . $value ['board'] . '</td>
							<td style="font-size:11;border:1px solid #000000;" align="left"  width="13%">' . $value ['annual_fee'] . '</td>
							<td style="font-size:11;border:1px solid #000000;" align="left"  width="14%">' . $value ['school_type'] . '</td>		
                            <td style="font-size:11;border:1px solid #000000;" align="center"  width="16%">' . $value ['external_award_text'] . '</td>
                        </tr>';
		$i ++;
	}
	$section3_html1 .= '</table><br/><br/>					
			<span style="font-size:12;font-weight:bold;">Note<span style="color:#FF0000;"><sup>*</sup></span></span> Refer Section 1 - Table '.$awardTableNum.'		
    ';
}
if($award_scheme_id==1)
{
$stateGold = isset ( $data ['State'] ['Gold'] ) ? $data ['State'] ['Gold'] : 0;
$stateSilver = isset ( $data ['State'] ['Silver'] ) ? $data ['State'] ['Silver'] : 0;
$stateBronze = isset ( $data ['State'] ['Bronze'] ) ? $data ['State'] ['Bronze'] : 0;
$statePlatinum = isset ( $data ['State'] ['Platinum'] ) ? $data ['State'] ['Platinum'] : 0;

$nationalGold = isset ( $data ['National'] ['Gold'] ) ? $data ['National'] ['Gold'] : 0;
$nationalSilver = isset ( $data ['National'] ['Silver'] ) ? $data ['National'] ['Silver'] : 0;
$nationalBronze = isset ( $data ['National'] ['Bronze'] ) ? $data ['National'] ['Bronze'] : 0;
$nationalPlatinum = isset ( $data ['National'] ['Platinum'] ) ? $data ['National'] ['Platinum'] : 0;

$inationalGold = isset ( $data ['International'] ['Gold'] ) ? $data ['International'] ['Gold'] : 0;
$inationalSilver = isset ( $data ['International'] ['Silver'] ) ? $data ['International'] ['Silver'] : 0;
$inationalBronze = isset ( $data ['International'] ['Bronze'] ) ? $data ['International'] ['Bronze'] : 0;
$inationalPlatinum = isset ( $data ['International'] ['Platinum'] ) ? $data ['International'] ['Platinum'] : 0;

$section3_html1 .= '<div style="page-break-inside:avoid;"><span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span><br/>
       <img src="' . SITEURL . '/library/bar_chart.php?sg=' . $stateGold .'&ip=' . $inationalPlatinum.'&np=' . $nationalPlatinum . '&sp=' . $statePlatinum . '&ss=' . $stateSilver . '&sb=' . $stateBronze . '&ng=' . $nationalGold . '&ns=' . $nationalSilver . '&nb=' . $nationalBronze . '&ig=' . $inationalGold . '&is=' . $inationalSilver . '&ib=' . $inationalBronze . '&count=' . $num_of_schools . '" /></div></div>';
}
elseif($award_scheme_id==2){
//add don bosco graph
$o1 = isset ( $data ['Grade'] ['O'] ) ? $data ['Grade'] ['O'] : 0;
$o2 = isset ( $data ['Grade'] ['O+'] ) ? $data ['Grade'] ['O+'] : 0;
$o3 = isset ( $data ['Grade'] ['O++'] ) ? $data ['Grade'] ['O++'] : 0;

$a1 = isset ( $data ['Grade'] ['A'] ) ? $data ['Grade'] ['A'] : 0;
$a2 = isset ( $data ['Grade'] ['A+'] ) ? $data ['Grade'] ['A+'] : 0;
$a3 = isset ( $data ['Grade'] ['A++'] ) ? $data ['Grade'] ['A++'] : 0;

$b1 = isset ( $data ['Grade'] ['B'] ) ? $data ['Grade'] ['B'] : 0;
$b2 = isset ( $data ['Grade'] ['B+'] ) ? $data ['Grade'] ['B+'] : 0;
$b3 = isset ( $data ['Grade'] ['B++'] ) ? $data ['Grade'] ['B++'] : 0;

$c = isset ( $data ['Grade'] ['C'] ) ? $data ['Grade'] ['C'] : 0;
$section3_html1 .= '<div style="page-break-inside:avoid;"><span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span><br/>
       <img src="' . SITEURL . '/library/bar_chart_grade.php?o1=' . $o1 .'&o2='.$o2.'&o3='.$o3.'&a1='.$a1.'&a2='.$a2.'&a3='.$a3.'&b1='.$b1.'&b2='.$b2.'&b3='.$b3.'&c='.$c. '&count=' . $num_of_schools . '" /></div>';
$section3_html2 = '<div style="page-break-inside:avoid;"><span style="text-align:center;font-size:12;color:#6c0d10;font-weight:normal;">Table '.$tableNum++.'</span><br/><table cellspacing="0" cellpadding="1" border="0" nobr="true" style="font-size:12;">
	    <tr style="background-color:#CCCCCC;text-align:center;">
	        <th style="width:10%;">Key</th>
	        <th style="width:15%;">To be read as</th>
	        <th style="width:75%;">What this means</th>
	    </tr>
		<tr>
			<td style="background-color:#307ACE;"></td>
			<td style="text-align:center;">Outstanding</td>
			<td>Best practice is consistently and visibly embedded in the culture of the school, is documented well and known to all stakeholders.</td>
		</tr>
		<tr style="background-color:#E6EBED;">
			<td style="background-color:#5e9900;"></td>
			<td style="text-align:center;" >Good</td>
			<td>There are consistently visible examples of good practice that have become part of the school\'s culture and are known to all stakeholders. The leadership and management ensures secure system and processes.</td>
		</tr>
		<tr>
			<td style="background-color:#D0B122;"></td>
			<td style="text-align:center;" >Variable</td>
			<td >Some examples of good practice are visible. These are not embedded in school culture and are known or practiced by only a few.</td>
		</tr>
		<tr style="background-color:#E6EBED;">
			<td style="background-color:#d12200;"></td>
			<td style="text-align:center;" >Needs Attention</td>
			<td >Action needs to be taken immediately. There is little or no evidence of good practice in the school.</td>
		</tr>
	</table>
	<br/><br/>
		<span style="font-weight:bold;font-size:12;">Note</span> Refer Section 1 - Table '.$awardTableNum.'</div>	
	</div>			
		';
}
$pdf->SetFont ( 'times', '', 9.5 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->writeHTMLCell ( 0, 0, '', '', $section3_html1.$section3_html2, 0, 1, 0, true, 'J', true );

// add performance page
$pdf->AddPage ();
// set a bookmark for the current position
$pdf->Bookmark ( "4. Performance of Schools on the 6 KPAs", 0, 0, '', 'B', array (
		0,
		64,
		128 
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );

// print a line using Cell()
$pdf->Cell ( 0, 10, '4. Performance of Schools on the 6 KPAs', 0, 1, 'C', true );
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section4_html1 = '
	<div style="text-align:left;font-size:12;">The performance data presented in this report refers to the schools\' performance on the
date of the external review. All findings are attributable to the school leadership that
existed during the period of the review.</div>		
    <div style="text-align:center;font-size:15;font-weight:bold;">
		<span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span><br/>
		The performance of ' . $num_of_schools . ' schools across the Key Performance Areas</div>';

$section4_html1 .= '<img style="border:1px solid #000000;" src="' . SITEURL . 'library/stacked_val.chart.php' . $performaceQstring . '" />';

$pdf->writeHTMLCell ( 0, 0, '', '', $section4_html1, 0, 1, 0, true, 'J', true );

// section 5 performance of each school on kpa
/*
if($kpaKqAnalysis!='')
{
	$pdf->AddPage ();
	$section4_html2 .=  '<p><b>All the schools performed well in the key performance areas below</b>:<br/><br/>' . $kpaKqAnalysis . '</p>';
	$pdf->writeHTMLCell ( 0, 0, '', '', $section4_html2, 0, 1, 0, true, 'J', true );
}*/

//add state,fee and boardwise graph for each kpa
//$pdf->addPage();
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$k=1;
$title = '<div style="text-align:center;font-size:15;font-weight:bold;">Performance of schools across 6 KPAs categorised by state, school fees and examination board</div>';
foreach($kpaNames as $kpa){//key performace are pages
	// set a bookmark for the current position			
	$kpa_html = "";	
	//statewise
	$kpa_state = $objCustom->getKPAParameterWiseAnalysis($kpaIds['kpa'.$k],'state');
	$kpa_fee = $objCustom->getKPAParameterWiseAnalysis($kpaIds['kpa'.$k],'fee');
	$kpa_board = $objCustom->getKPAParameterWiseAnalysis($kpaIds['kpa'.$k],'board');
	$uriStringImg = "";
	//prepare uri to created stacked chart
	$uriStringState = $objCustom->getKPAparameterGraph($kpa_state);
	$uriStringFee = $objCustom->getKPAparameterGraph($kpa_fee);
	$uriStringBoard = $objCustom->getKPAparameterGraph($kpa_board);
	$uriStringImg1 = strlen($uriStringState)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringState . '" />':'';
	$uriStringImg2 = strlen($uriStringFee)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringFee . '" />':'';
	$uriStringImg3 = strlen($uriStringBoard)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringBoard . '" />':'';
	//echo $uriStringImg1,$uriStringImg2,$uriStringImg3;die;
	$table ='<span style="text-align:center;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span> - <span style="text-align:center;">'.$kpaNames['kpa'.$k].'</span><br/>
			<div style="border:1px solid #000000;">
			<table border="0" cellpadding="1" cellspacing="0" width="100%">									
			<tr style="text-align:center;">'.($uriStringImg1!=''?'<td>State<br/>'.$uriStringImg1.'</td>':'').($uriStringImg2!=''?'<td>Fee<br/>'.$uriStringImg2.'</td>':'').($uriStringImg3!=''?'<td>Board<br/>'.$uriStringImg3.'</td>':'').'</tr>							
			</table>						
			</div>';
	$kpa_html .= '<div style="page-break-inside:avoid;" >'.($k==1?$title.'<br/>':'').$table.'</div>';	
	
	$pdf->writeHTMLCell ( 0, 0, '', '', $kpa_html, 0, 1, 0, true, 'J', true );	
	$k++;
}
//kpa7 board state fee analysis if selected
if($kpa7Id>0){
	$kpa_html = "";	
	$kpa_state = $objCustom->getKPAParameterWiseAnalysis($kpa7Id,'state');
	$kpa_fee = $objCustom->getKPAParameterWiseAnalysis($kpa7Id,'fee');
	$kpa_board = $objCustom->getKPAParameterWiseAnalysis($kpa7Id,'board');	
	$uriStringImg = "";
	//prepare uri to created stacked chart
	$uriStringState = $objCustom->getKPAparameterGraph($kpa_state);
	$uriStringFee = $objCustom->getKPAparameterGraph($kpa_fee);
	$uriStringBoard = $objCustom->getKPAparameterGraph($kpa_board);
	
	$uriStringImg1 = strlen($uriStringState)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringState . '" />':'';
	$uriStringImg2 = strlen($uriStringFee)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringFee . '" />':'';
	$uriStringImg3 = strlen($uriStringBoard)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringBoard . '" />':'';	
	$table ='<span style="text-align:center;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span> - <span style="text-align:center;">'.$kpa7Name.'</span><br/>
			<div style="border:1px solid #000000;">
			<table border="0" cellpadding="1" cellspacing="0" width="100%">
			<tr style="text-align:center;">'.($uriStringImg1!=''?'<td>State<br/>'.$uriStringImg1.'</td>':'').($uriStringImg2!=''?'<td>Fee<br/>'.$uriStringImg2.'</td>':'').($uriStringImg3!=''?'<td>Board<br/>'.$uriStringImg3.'</td>':'').'</tr>
			</table>
			</div>';
			
	$kpa_html .= '<div style="page-break-inside:avoid;" >'.$table.'</div>';
	
	$pdf->writeHTMLCell ( 0, 0, '', '', $kpa_html, 0, 1, 0, true, 'J', true );
}
$pdf->AddPage ();
// set a bookmark for the current position
/* $pdf->Bookmark ( "5. The performance of each school on the Key Performance Areas", 0, 0, '', 'B', array (
		0,
		64,
		128 
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 ); */

// print a line using Cell()
//$pdf->Cell ( 0, 10, '5.The performance of each school on the Key Performance Areas', 0, 1, 'C', true );
//$pdf->Ln ( 5 );
$pdf->SetFont ( 'times', '', 10 );
$pdf->SetTextColor ( 0, 0, 0 );
$section5_html1 = '<table cellspacing="0" cellpadding="3" border="0">
		<thead>
		<tr><th width="100%" style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum++.'</th></tr>
		<tr><th style="text-align:center;font-size:12;font-weight:bold;">Performance of each school on each KPA</th></tr>		
	    <tr style="font-size:9;">
			<th width="34%" style="border:solid 1px #000000;"></th>
	        <th align="center" width="11%" style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa1'].'</b></th>
			<th align="center" width="11%" style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa2'].'</b></th>
	        <th align="center" width="11%" style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa3'].'</b></th>
	        <th align="center" width="11%" style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa4'].'</b></th>
		 	<th align="center" width="11%" style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa5'].'</b></th>
		 	<th align="center" width="11%"  style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa6'].'</b></th>		 	
	    </tr>
		</thead>';
$section5_tbl = "";
$count = count ( $getAllSchoolPerf );
$i = 0;
foreach ( $getAllSchoolPerf as $row ) {
	$i ++;
	$row ['kpa_id'] == 1 ? $section5_tbl .= '<tr style="font-size:9;"><td  width="34%"  style="border:solid 1px #000000;">' . $row ['client_name'] . '</td>' : '';
	$bgColor = $row ['rating_id'] == 8 ? '#307ACE;' : ($row ['rating_id'] == 7 ? '#5e9900;' : ($row ['rating_id'] == 6 ? '#D0B122;' : ($row ['rating_id'] == 5 ? '#d12200;' : '')));
	$section5_tbl .= '<td style="background-color:' . $bgColor . ';border:solid 1px #000000"  width="11%">' . '</td>';
	($row ['kpa_id'] == 6 && $i != $count) ? ($section5_tbl .= '</tr>') : (($i == $count) ? ($section5_tbl .= '</tr>') : '');
}
$section5_html1 .= $section5_tbl . "</table>";
$section5_html1 .= "<p>This graph helps identify the quality of practice of each school across the 6 KPAs, thus
informing the network which KPAs are predominantly consistent or variable in some schools
and the KPA that need attention in others.</p>";
$pdf->writeHTMLCell ( 0, 0, '', '', $section5_html1, 0, 1, 0, true, 'L', true );

// cumulative report

// section 5 performance of each school on kpa

$pdf->AddPage ();
// set a bookmark for the current position
$pdf->Bookmark ( "5. The cumulative report of each Key Performance Area", 0, 0, '', 'B', array (
		0,
		64,
		128 
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );

// print a line using Cell()
$pdf->Cell ( 0, 10, '5. The cumulative report of each Key Performance Area', 0, 1, 'C', true );
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section6_html1 = "";
$section6_html1 .= "<p>The following report provides the ".$network_report_name." with a granular overview of the schools'
performance. This section identifies areas in each KPA where the schools of the network
have either a good or variable practice and also highlights practices that need attention.
This overview will help the network to identify practices that need to be spread to all
schools and made consistent as well as practices that need to be attended to on an urgent
basis.</p>
";
$numHeading=5;
$point = 1;
// cumulative report indicator bars
$pdf->writeHTMLCell ( 0, 0, '', '', $section6_html1, 0, 1, 0, true, 'J', true );
$k=1;
foreach($kpaNames as $kpa){//key performace are pages
	// set a bookmark for the current position
	$pdf->Bookmark ( $numHeading.'.'.$k.'. Key Performance Area '.$k.': '.$kpaNames['kpa'.$k], 0, 0, '', 'B', array (
			0,
			64,
			128
	) );
	$pdf->SetFont ( 'times', 'B', 16 );
	$pdf->SetTextColor ( 255, 255, 255 );
	$pdf->SetFillColor ( 108, 13, 16 );

	// print a line using Cell()
	$pdf->Cell ( 0, 10, $numHeading.'.'.$k.'  Key Performance Area '.$k.': '.$kpaNames['kpa'.$k], 0, 1, 'C', true );
	$pdf->SetFont ( 'times', '', 10 );
	$pdf->SetTextColor ( 0, 0, 0 );
	$kpa_html = "";
	$kpa_html .= '<br/><div style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum++.'</div>';		
	$kpa_html .= $objCustom->getKPACQtbl ( $kpaCQrating [$k], $k, $num_of_schools );	
	//key for reading the report
	$kpa_keys = $objCustom->getKeyForReportKPA();	
	$kpa_html .= '<br/><div style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum++.'</div>'.$kpa_keys;
	$pdf->writeHTMLCell ( 0, 0, '', '', $kpa_html, 0, 1, 0, true, 'J', true );	
	
	//Kpa- KQ wise table
	//kpa- kq
	for($kqnum=1;$kqnum<=3;$kqnum++){
		$kqcqjshtml = '';
		$newkq=1;
		
		//$pdf->writeHTMLCell ( 0, 0, '', '', $kqcqjshtml, 0, 1, 0, true, 'J', true );
		//get js data for every kq
		$kqjsJdRatings = $objCustom->getKpaKqRatingAndJd($kpaIds['kpa'.$k],($kqnum-1)*9*$num_of_schools,9*$num_of_schools);
		//print table for kq wise js ratings and jd
		//print_r($kqjsJdRatings);
		$kqjsJdRatingsTbl = $objCustom->getKPAKQJSTable($kqjsJdRatings,$kqnum,$tableNum++);
		for($numcq=3*$kqnum-2;$numcq<=3*$kqnum;$numcq++){
			$kqcqJs = $objCustom->getKPAKqCqstatements($kpaIds['kpa'.$k],$kqnum,$numcq);
			//get html kq1
			if($newkq++==1)
				$kqcqjshtml .= 	'<br/><table border="0" cellpadding="3" nobr="true"><thead>
    			<tr><th colspan="2" style="width:100%;"><span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum++.'</span></th></tr>
    			<tr><th style="border:1px solid #000000;width:8%;font-weight:bold;">No.</th><th style="border:1px solid #000000;width:92%;font-weight:bold;">Statements</th></tr>
    			</thead><tr style="font-weight:bold;background-color:#CCCCCC;border:1px solid #000000;"><td style="border:1px solid #000000;width:8%;" >KQ'.$kqnum.'</td><td style="border:1px solid #000000;width:92%;">'.$kqcqJs[0]['key_question_text'].'</td></tr>';
				$kqcqjshtml .= $objCustom->getKPAKqCqTable($kqcqJs,$numcq);
		}
		$kqcqjshtml .='</table>';
		//echo '<pre>'.$kqjsJdRatingsTbl.'</pre>';
		$pdf->writeHTMLCell ( 0, 0, '', '', $kqjsJdRatingsTbl, 0, 1, 0, true, 'J', true );
		$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;">'.$kqcqjshtml.'</div>', 0, 1, 0, true, 'J', true );
		
		//$pdf->writeHTMLCell ( 0, 0, '', '', $kqjsJdRatingsTbl, 0, 1, 0, true, 'J', true );				
	}
	$pdf->AddPage ();
	$k++;
}
if (! empty ( $kpa7Schools )) {
$kpa7_html2 = '';
	
	$kpa7_html2 .= '<br/><table cellspacing="0" cellpadding="1" border="0">
		<thead>	
		<tr style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10"><th colspan="10">Table '.$tableNum++.' : Performance on each question</th></tr>	
	    <tr style="font-size:10;">
			<th style="width:28%;border:solid 1px #000000;"></th>
	        <th style="width:8%;font-weight:bold;border:solid 1px #000000;" align="center">SQ1</th>
		 	<th style="width:8%;font-weight:bold;border:solid 1px #000000;"  align="center">SQ2</th>
			<th style="width:8%;font-weight:bold;border:solid 1px #000000;"  align="center">SQ3</th>
			<th style="width:8%;font-weight:bold;border:solid 1px #000000;"  align="center">SQ4</th>
			<th style="width:8%;font-weight:bold;border:solid 1px #000000;"  align="center">SQ5</th>
			<th style="width:8%;font-weight:bold;border:solid 1px #000000;"  align="center">SQ6</th>
			<th style="width:8%;font-weight:bold;border:solid 1px #000000;"  align="center">SQ7</th>
			<th style="width:8%;font-weight:bold;border:solid 1px #000000;"  align="center">SQ8</th>
			<th style="width:8%;font-weight:bold;border:solid 1px #000000;"  align="center">SQ9</th>			
	    </tr>
		</thead>	';
	$kpa7_tbl = "";
	$count = count ( $kpa7Schools );
	$j = 0;
	$kpa7Weightage = array();
	foreach ( $kpa7Schools as $row ) {
		$j ++;
		$wt = 0;
		$kpa7_tbl .= '<tr style="font-size:11;"><td style="width:28%;border:solid 1px #000000;">' . $row ['client_name'] . '</td>';
		$allRatings = explode ( ',', $row ['ratingIds'] );
		$bgColor = '';
		for($i = 0; $i < count ( $allRatings ); $i ++) {
			$wt += round($objCustom->getWeightage((1*100)/9,$allRatings[$i]),1);
			$kpa7Weightage[$row ['client_name']] = $wt;
			$bgColor = $allRatings [$i] == 8 ? '#307ACE;' : ($allRatings [$i] == 7 ? '#5e9900;' : ($allRatings [$i] == 6 ? '#D0B122;' : ($allRatings [$i] == 5 ? '#d12200;' : '')));
			$kpa7_tbl .= '<td style="background-color:' . $bgColor . ';width:8%;border:solid 1px #000000;">' . '</td>				
				';
		}
		//$kpa7_tbl .= ($j != $count) ? '</tr>' : '</tr>';
		$kpa7_tbl .= "</tr>";
	}
	$kpa7_html2 .= $kpa7_tbl . "</table>";	
	
// set a bookmark for the current position
$kpa7 = "";
$kpa7Title="";
$kpa7BkMark="";
//$numHeading=4;
if($is_validated==1){	
	$kpa7BkMark = $numHeading."7. Key Performace Area 7: $kpa7Name";		
	$kpa7Title = $numHeading++."7. Key Performace Area 7: $kpa7Name";		
	$kpa7 .= '<div style="page-break-inside:avoid;"><h3 style="text-align:center;">'.$kpa7Name.' - School Performance at KPA level</h3><br/>
			<div style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum++.'</div>';
	
}
else
{
	$kpa7BkMark = $numHeading.'.7. Analysis of the self-review of the 7th KPA - '.$kpa7Name;		
	$kpa7Title = $numHeading++.'.7. Analysis of the self-review of the 7th KPA - '.$kpa7Name;	
	$kpa7 .= '<div style="page-break-inside:avoid;"><h3 style="text-align:center;">'.$kpa7Name.' - School Performance at KPA level: SELF REVIEW ONLY*<br/>
			<span style="font-size:13px;font-weight:normal;">The variability or consistency has been entirely decided by the School Review team for itself.</span>
			</h1>		
		<div style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum++.'</div>';	
}

$pdf->Bookmark ( $kpa7BkMark, 0, 0, '', 'B', array (
		0,
		64,
		128 
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
// print a line using Cell()
	$pdf->Cell ( 0, 10, $kpa7Title, 0, 1, 'C', true );
	$pdf->SetFont ( 'times', '', 12 );
	$pdf->SetTextColor ( 0, 0, 0 );	
	$kpa7 .= $objCustom->getKPACQtbl ( $kpa7arr [7], 7,$num_of_schools,1,$is_validated);
	
	
	$pdf->writeHTMLCell ( 0, 0, '', '', $kpa7_html2, 0, 1, 0, true, 'J', true );
	$pdf->AddPage();
	$pdf->writeHTMLCell ( 0, 0, '', '', "".$kpa7."</div>", 0, 1, 0, true, 'J', true );
	
	//if not validated then show evidence text
	if($is_validated==0)
	{
		
		$kpa7_html3 = "";
		//$tempnum = $tableNum++;
		//$evidenceTtl = '<div style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum++.'</div><div style="text-align:center;"><b>Evidence shared by '.$num_of_schools.' schools for their performance</b></div><br/>';
		for($j=1;$j<=9;$j){
			$text1 = $objCustom->getKPA7Evidence($j);
			$text1 = $text1['text'];
			$text2 = $objCustom->getKPA7Evidence($j+1);
			$text2 = $text2['text'];
			$text3 = $objCustom->getKPA7Evidence($j+2);
			$text3 = $text3['text'];
		$kpa7_html3 = '<table cellspacing="0" cellpadding="5" border="0" style="padding: 3px 3px 3px 3px;">
		<thead>
		<tr><th colspan="3" style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum++.'</th></tr>		
		<tr><th colspan="3" style="text-align:center;"><b>Evidence shared by '.$num_of_schools.' schools for their performance</b></th></tr>				
	    <tr style="font-size:10;text-align:center;background-color:#ffe6e6;">
			<th style="width:33.33%;border:solid 1px #000000;"><b>SQ '.($j).'</b></th>
			<th style="width:33.33%;border:solid 1px #000000;"><b>SQ '.($j+1).'</b></th>
			<th style="width:33.33%;border:solid 1px #000000;"><b>SQ '.($j+2).'</b></th>	       		
	    </tr>
		</thead>			
		<tr style="font-size:10;text-align:center;">
			<td style="width:33.33%;border:solid 1px #000000;"></td>
			<td style="width:33.33%;border:solid 1px #000000;"></td>
			<td style="width:33.33%;border:solid 1px #000000;"></td>	       		
	    </tr>
		<tr style="font-size:10;">
			<td style="width:33.33%;border:solid 1px #000000;">'.$text1.'</td>
			<td style="width:33.33%;border:solid 1px #000000;">'.$text2.'</td>
			<td style="width:33.33%;border:solid 1px #000000;">'.$text3.'</td>	       		
	    </tr>
		</table>';
		$j=$j+3;
		$pdf->AddPage();
		
		$pdf->writeHTMLCell ( 0, 0, '', '', $kpa7_html3, 0, 1, 0, true, '', true );
		$evidenceTtl = "";
		}
		
	}
	$pdf->AddPage();
}
// set a bookmark for the current position
$pdf->Bookmark ( $numHeading.". School Effectiveness in applying the school review diagnostic", 0, 0, '', 'B', array (
		0,
		64,
		128 
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
//$numHeading = 4;
// print a line using Cell()
$pdf->Cell ( 0, 10, $numHeading++.'. School Effectiveness in applying the school review diagnostic', 0, 1, 'C', true );
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section8_html1 = "";
$section8_html1 .= '<p>The following graph depicts the level of agreements and disagreements the SSRE team
shared with the SERE team on the judgment statements. Limitations (explained in the next section) of the use of the diagnostic may 
		also contribute to agreements and difference in agreements between the SSRE and SERE, which is often 
		indicative of the level of experience between the two teams regarding global standards.</p>';

// create querystring
$j = 1;
$url = array("");
$rankArray = array();
$url_count = 0;
foreach ( $clientsJd as $jd ) {
	if($j%16==0){//after 20 schools create a new image
		$url_count++;
		$url[$url_count]="";
	}
	$client_name = urlencode ( $jd ['client_name'] );
	$allJD = $jd ['JD'];
	$agreements = substr_count ( $allJD, 0 );
	$disagree1 = substr_count ( $allJD, 1 );
	$disagree2 = substr_count ( $allJD, 2 );
	$disagree3 = substr_count ( $allJD, 3 );
	$url[$url_count] .= "i" . $j . "=" . $client_name . ";" . $agreements . ";" . $disagree1 . ";" . $disagree2 . ";" . $disagree3;
	$j == $num_of_schools ? '' : ($url[$url_count] .= '&');
	$j ++;
	//calculate weightage
	$total = $agreements + $disagree1 + $disagree2 +$disagree3;
	$weightage = $objCustom->getWeightage($agreements*100/$total,8)+$objCustom->getWeightage($disagree1*100/$total,7)+$objCustom->getWeightage($disagree2*100/$total,6)+$objCustom->getWeightage($disagree3*100/$total,5);
	$rankArray[$jd ['client_name']] = $weightage;
}
for($i=0;$i<=$url_count;$i++){
$section8_html1 .= '<div style="page-break-inside:avoid;">'.($i==0?'<span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10;width:100%">Graph '.$graphNum++.'</span>':'').		
'<div style="text-align:center;font-weight:bold;">Agreements and Disagreements between the SSRE and the SSRE team</div>
<img style="border:solid 1px #000000;" src="' . SITEURL . '/library/stacked.chart_jd.php?' . $url[$i] . '">
</div>';
}
$section8_html2 = "";
$section8_html2 .= '<span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10" width="100%">Table '.$tableNum++.'</span><div style="text-align:center;"><b>Accuracy of Self-Review(Ranked from highest to lowest)</b></div><table cellspacing="0" cellpadding="1" border="1">
	<tr style="text-align:center;"><td style="width:10%;"><b>Rank</b></td><td style="width:90%;"><b>School</b></td></tr>';
	$k=1;
	$rank = 1;
	$awardPrev = 0;
	arsort($rankArray,SORT_NUMERIC);
	foreach($rankArray as $key=>$val)
	{
		if($k>1 && $awardPrev != $val)
			$rank++;
			
		$section8_html2 .= '<tr><td>'.$rank.'</td><td>'.$key.'</td></tr>';
		$awardPrev = $val;
		$k++;
	}
	$section8_html2 .= '</table>';

$pdf->writeHTMLCell ( 0, 0, '', '', $section8_html1, 0, 1, 0, true, 'J', true );

$pdf->AddPage ();
$pdf->writeHTMLCell ( 0, 0, '', '', $section8_html2, 0, 1, 0, true, 'J', true );
$pdf->AddPage ();
// set a bookmark for the current position
$pdf->Bookmark ( $numHeading.". Limitations of first use of the diagnostic", 0, 0, '', 'B', array (
		0,
		64,
		128 
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );

// print a line using Cell()
$pdf->Cell ( 0, 10, $numHeading++.'. Limitations of first use of the diagnostic', 0, 1, 'C', true );
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section9_html1 = "";
$section9_html1 .= '<p>It is inevitable that the first time a school prepares to undertake a self-review it will not be as accurate in coming to judgement about
&lsquo;what good looks like&rsquo; as the experienced external validation team. 
By the end of the review process the school self-review team (SSRE) has a much better grasp of what strong evidence looks like.<br/><br/>
Where judgement difference is significant it isn&lsquo;t because of school collecting poor evidence. Schools usually collect good evidence.
The problem is that either there is not enough of it or more frequently, 
that the school has not studied the diagnostic statements closely enough. Here are a few of the more common errors:
<ul>
<li>An inaccurate interpretation of the statement in question, "Oh, we read it to mean something else."</li>
<li>Partial reading of the statement, "We only collected evidence on the first half of the statement. We didn&rsquo;t realise that every word or phrase counts."</li>
<li>Incomplete understanding of how much evidence you need to collect to make a secure judgement, "You mean that we have to collect evidence from every class in the school not just a few of them.  And it is not enough for something to happen once or twice a year?"</li>
<li>The teacher or leader uses their own practice as their reference point for what good looks like, "I&rsquo;m sure the rest of the school is doing it. I certainly am!"</li>
<li>Making a judgement on the school&rsquo;s effectiveness which does not take account of all aspects of the diagnostic statement or of all stakeholders awareness, "I know we haven&rsquo;t documented this practice but I am sure we shared it with parents at a meeting."</li>
<li>Making a judgement with reference to the school&rsquo;s status locally and not to the national standard. "Everyone says we are the best school parents, students, even teachers, so it must be &lsquo;Always&rsquo;.</li>	
</ul>
The final step in the school self-review process should be on the final day of the self-review, when the whole self-review team comes together to moderate the judgements of each of the Key Performance Area teams. Unfortunately, this step is not always taken! The consequence is that often there is a very significant difference between the School&rsquo;s and the external review team&rsquo;s judgements.  <br/>
<br/>
The quality dialogue almost always results in a significant shift in the SSRE&rsquo;s judgement about the nature and quality of evidence required to make a secure judgement. This insightful discussion results in building a consensus both across all performance areas but also between the school and external evaluations. 		
<br/>		
<br/>		
<b>Recommendations:</b><br/>
All schools would benefit from a regular use of the Adhyayan Quality Diagnostic
throughout their development journey, taking steps to engage with resources and
resource people to increasing their knowledge of What Good Looks Like. Some exemplars
on the Adhyayan website that may help can be found on <a href="http://adhyayan.asia/site/" target="_blank">http://adhyayan.asia/site/</a>
 <br/><br/>
If the network wishes, it could contact organisations such as Adhyayan to arrange visits
to &lsquo;Good&rsquo; schools to experiences aspects of good practice.		
</p>';


$pdf->writeHTMLCell ( 0, 0, '', '', $section9_html1, 0, 1, 0, true, 'J', true );

$pdf->AddPage ();
// set a bookmark for the current position
$pdf->Bookmark ( $numHeading.". Appendix", 0, 0, '', 'B', array (
		0,
		64,
		128 
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );

// print a line using Cell()
$pdf->Cell ( 0, 10, $numHeading++.'. Annexure 1 : Judgement Distance for each KPA', 0, 1, 'C', true );
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->Ln ( 3 );
$section10_htmlintro = "<p>The below tables will help the network leaders to identify every school's
 accuracy in their self-review on each of the Key Performance Area.  It will help the network to organise
 visits and create a buddy system for the schools to facilitate and grow every school's understanding of
 what &lsquo;Good&rsquo; looks like.</p><br/>";
$pdf->writeHTMLCell ( 0, 0, '', '', $section10_htmlintro, 0, 1, 0, true, 'J', true );
$pdf->SetFont ( 'times', '', 9 );
 $i=0;
foreach($kpaNames as $key=>$kpaname) 
{	
	$section10_html1 = "";
	$section10_html1 .= $objCustom->createKpaJDtbl ( $tableNum++,$kpaJDarr [++$i], $kpaname );
	$pdf->writeHTMLCell ( 0, 0, '', '', $section10_html1, 0, 1, 0, true, 'J', true );
	if ($i>0 && $i<7 &&  $addSchoolNum >16 && $addSchoolNum=0 )
		$pdf->AddPage ();
	$addSchoolNum += $num_of_schools;
	$pdf->Ln ( 3 );
}
 unset($i);
//annexure 2
$pdf->AddPage ();
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );

// print a line using Cell()
$pdf->Cell ( 0, 10,'Annexure 2 : Count of Mostly and Always as judged by the SERE team', 0, 1, 'C', true );
$pdf->SetFont ( 'times', '', 9 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->Ln ( 3 );
$widthOfCol = $kpa7Name!=''? 85/8:85/7;
$widthOfCol .='%';
$tbl = '<table cellspacing="0" cellpadding="1" border="0" align="center" nobr="true">
    	<thead>
    	<tr style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10;border:solid 1px #ffffff;"><td style="border:solid 1px #ffffff;" colspan="'.($kpa7Name!=''?9:8).'">Table '.$tableNum++.'</td></tr>
    	<tr style="background-color:#fbc140;border:solid 1px #000000;font-weight:bold;">
    	<th style="border:solid 1px #000000;" width="15%">School Names</th><th style="border:solid 1px #000000;" width="'.$widthOfCol.'">'.$kpaAcronyms['kpa1'].'<br/>(KPA1)</th><th style="border:solid 1px #000000;" width="'.$widthOfCol.'" >'.$kpaAcronyms['kpa2'].'<br/>(KPA2)</th><th style="border:solid 1px #000000;" width="'.$widthOfCol.'">'.$kpaAcronyms['kpa3'].'<br/>(KPA3)</th><th style="border:solid 1px #000000;" width="'.$widthOfCol.'">'.$kpaAcronyms['kpa4'].'<br/>(KPA4)</th><th style="border:solid 1px #000000;" width="'.$widthOfCol.'">'.$kpaAcronyms['kpa5'].'<br/>(KPA5)</th><th style="border:solid 1px #000000;" width="'.$widthOfCol.'">'.$kpaAcronyms['kpa6'].' <br/>(KPA6)</th><th style="border:solid 1px #000000;" width="'.$widthOfCol.'">Total</th>
    	'.($kpaAcronyms['kpa7']!=''?'<th style="border:solid 1px #000000;">'.$kpaAcronyms['kpa7'].'<br/>(KPA7)</th>':'').'		
    	</tr>
    	</thead>';
foreach($clientsJd as $clt){
	$rowforClient = $objCustom ->getAnnex2TblByClientId($clt['client_id']);
	$tbl .= '<tr><td style="background-color:#fbc140;border:solid 1px #000000;" width="15%">'.$clt['client_name'].'</td>';
	$total =0;
	$kpanum=0;
	$kpa7score='0';
	 foreach($rowforClient as $r){
	 	if(++$kpanum>6){
	 		$kpa7score = $r['Mostly'].($r['Always']?'('.$r['Always'].')':'');
	 		break;
	 	}
		$tbl .= '<td style="border:solid 1px #000000;" width="'.$widthOfCol.'">'.$r['Mostly'].($r['Always']?'('.$r['Always'].')':'').'</td>';
		$total += $r['Always'] + $r['Mostly'];
	} 
	$tbl .= '<td style="border:solid 1px #000000;" width="'.$widthOfCol.'">'.$total.'</td>';
	$tbl.= $kpa7Name!=''?'<td style="border:solid 1px #000000;" width="'.$widthOfCol.'">'.$kpa7score.'</td>':'';
	$tbl .='</tr>';
}
$tbl .='</table><p>Note: The count of Always is listed inside the bracket for every KPA and school.</p>';
$pdf->writeHTMLCell ( 0, 0, '', '', $tbl, 0, 1, 0, true, 'J', true );


$pdf->addTOCPage ();
// write the TOC title and/or other elements on the TOC page
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->MultiCell ( 0, 0, 'Table Of Contents', 1, 'C', true, 1, '', '', true, 1 );

// $pdf->Ln();
$pdf->SetFont ( 'times', '', 10 );

// define styles for various bookmark levels
$secondpagehtml = array ();

$pdf->Ln ( 5 );
$pdf->SetTextColor ( 108, 13, 16 );
$pdf->SetFont ( 'times', '', 15 );
$pdf->Write ( 0,'Table 1', '', 0, 'C', true, 0, false, false, 0 );
$pdf->Ln ( 2 );

$secondpagehtml [0] = '<table border="1" cellpadding="5" cellspacing="0" style="background-color:#fff">' . '<tr style="text-align: left">' . '<td width="155mm">' . '<span style="font-family:times;font-weight:normal;font-size:12pt;color:black;">#TOC_DESCRIPTION#</span>' . '</td>' . '<td width="25mm" style="text-align:center;">' . '<span style="font-weight:normal;font-size:12pt;color:black;">#TOC_PAGE_NUMBER#</span>' . '</td>' . '</tr>' . '</table>';

// add table of content at page 1
// (check the example n. 45 for a text-only TOC
$pdf->addHTMLTOC ( 2, 'INDEX', $secondpagehtml, true, 'B', array (
		128,
		0,
		0 
) );

// end of TOC page
$pdf->endTOCPage ();
// . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
// ---------------------------------------------------------

$path = ROOT.'reports';
// Close and output PDF document
$pdf->Output ($path.'/'.$network_report_name.'.pdf', 'FI' );
//$pdf->Output (__DIR__."\\". $network_report_name.'.pdf', 'F' );

// ============================================================+
// END OF FILE
// ============================================================+

?>