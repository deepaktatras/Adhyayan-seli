<?php
include ROOT . 'library' . DS . 'tcpdf' . DS . 'tcpdf.php';
ini_set('memory_limit','50M');
set_time_limit(0);
ini_set('max_execution_time', 0);
$even_page=isset($_GET['even_page'])?$_GET['even_page']:0;
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
			$this->Cell(0, 5, 'Network Overview Report for ' . $this->network_report_name, 0, false, 'L', 0, '', 0, false, 'T', 'M');
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
$kpaGraph = array ();
$kpaPercGraph = array ();
$kpaRating = array ();
$i = 0;
$graphNum = 1;
$tableNum = 2;
$performaceQstring = "?";
$prev_kpa_id = 0;
$kpaWeightage = array("kpa1"=>0,"kpa2"=>0,"kpa3"=>0,"kpa4"=>0,"kpa5"=>0,"kpa6"=>0,"kpa7"=>0);
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
$kpa7nametemp = isset($kpa7Name)?preg_replace('/\b(THE)/','',strtoupper($kpa7Name)):'';
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
$pdf->SetAutoPageBreak ( TRUE, 10 );

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
	$firstpagehtml = '<br/><br/><table class="pdfHdr broad"> <tbody><tr><td class="halfSec fl" align="left"><a href=""><img src="' . SITEURL . 'public/images/logo.png"alt="" style="height:55px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'.UPLOAD_URL_DIAGNOSTIC.'diagnostic_image_1.jpg" style="height:80px;" alt=""></a></td></tr><tr style="text-align:center;padding:2px;font-weight:bold;font-family: CalibriRegular,Verdana,sans-serif;font-size:12px;"><td colspan="2">Don Bosco School Self-Review and Evaluation Programme (DBSSRE) conducted under the aegis of<br/> \'Don Bosco for Excellence\'</td></tr> </tbody></table>' . '<br/><br/>';
else
	$firstpagehtml = '<br/><br/><div style="text-align:center"><img src="' . SITEURL . 'public/images/logo.png"></div>' . '<br/><br/>' ;
$firstpagehtml.='<div style="text-align:center;font-size:18;">Adhyayan Quality Standard Award (AQS) Network Overview Report:<span style="font-weight:bold;">'.$network_report_name.'</span></div>' . '<br/>' . '<div style="text-align:center;font-size:14;">Schools reviewed between ' . date_format ( date_create ( $dates ['startdate'] ), "d M, Y" ) . ' and ' . date_format ( date_create ( $dates ['enddate'] ), "d M, Y" ) . '</div>' . '<br/>' . '<div style="text-align:center;font-size:18;font-weight:bold">' . date ( 'F Y' ) . '</div><br/>' ;
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

// section 1 page start
$pdf->AddPage ();
// $pdf->writeHTML($secondpagehtml, true, false, false, false, '');
// $pdf->AddPage();

// set a bookmark for the current position
$pdf->Bookmark ( '1. Overview of the process & network priorities', 0, 0, '', 'B', array (
		0,0,0
) );
$pdf->Bookmark ( '1.1 The Adhyayan Quality Standard (AQS) Process', 1, 0, '', '', array (
		0,0,0
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->Cell ( 0, 8, '1. Overview of the Process & Network priorities ', 0, 1, 'C', true );
$pdf->Ln(3);
$pdf->SetFont ( 'times', 'B', 15 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->SetFillColor ( 211, 211, 211 );
$pdf->Cell ( 0, 8, '1.1 The Adhyayan Quality Standard (AQS) Process and Award', 1, 1, 'C', true );
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

$pdf->AddPage();

$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section1_html6 = "<div style='page-break-inside:avoid;'><b>2. Quality Dialogue:</b><br/>Following the SSRE and SERE team's review of the ".$num_of_schools." schools, both sets of teams -
        <ul>
            <li>shared their judgments on each of the KPAs and the evidence on which they were based, and discussed the similarities and differences
                in their evaluation of each school's performance.</li>
            <li>identified key areas for celebration and improvement based on the external review scores.</li>
            <li>shared examples of good practice where an individual school's performance had been evaluated as being variable or less 
                by the external review team.</li>
        </ul></div>";
$pdf->writeHTMLCell ( 0, 0, '', $pdf->GetY(), $section1_html6, 0, 1, 0, true, 'J', true );
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
		<tr ><td style="text-align:center;">O</td><td>The school\'s performance is outstanding on most KPAs as measured against their chosen tier.</td><td rowspan="3"><br/><br/><br/><br/><br/><br/>When the school has scored \'good\' in the KPAs of either \'L & M\' and/or \'T & L\'.</td><td rowspan="3"><br/><br/><br/><br/><br/>Apart from scoring \'+\', when the school also scores an \'Outstanding\' in any one of the KPA</td></tr>
		<tr><td style="text-align:center;">A</td><td>The school\'s performance is good and ensures strong practise on most KPAs.</td></tr>
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
$section1_html8 = "<b>3. Adhyayan Quality Standard Award:</b><br/>The initial review provides the baseline for the school's future performance. This enables each school's progress to be tracked and then assessed against the baseline assessment through the subsequent two yearly reviews.";
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
// section 3 page start
$pdf->SetFont ( 'times', 'B', 12 );
$pdf->addPage();
$pdf->Ln(3);
$section3_html1='';
if($award_scheme_id==1)
{	$i=0;$prev_award_state = '';
	$queryStringAward ='';
	foreach($data as $key=>$value){
		if($prev_award_state !=$value['state_name']){
			$i++;
			$i>1?($queryStringAward.='&'):'';
			$queryStringAward .= "s".$i.'=name_'.urlencode($value['state_name']);
		}		
		$str = strtolower(preg_replace('~\b(\w)|.~', '$1', $value['external_award_text']));//acronym
		$queryStringAward .= ';'.$str.'_'.$value['num'];
	}
	$section3_html1 = '<div style="page-break-inside:avoid;">Graph 1 provides a graphical representation of the distribution of school performance by award.<br/><span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span><br/>
       <img border="1" src="' . SITEURL . 'library/bar_chart.php?'.$queryStringAward.'" /></div></div>';	
}
elseif($award_scheme_id==2){
	//add don bosco graph
	$i=0;$prev_award_state = '';
	$queryStringAward ='';
	foreach($data as $key=>$value){
		if($prev_award_state !=$value['state_name']){
			$i++;
			$i>1?($queryStringAward.='&'):'';
			$queryStringAward .= "s".$i.'=name_'.urlencode(ucwords($value['state_name']));
		}
		$str = urlencode(strtolower($value['external_award_text']));
		$queryStringAward .= ';'.$str.'_'.$value['num'];
	}
	$section3_html1 = '<div style="page-break-inside:avoid;">Graph 1 provides a graphical representation of the distribution of school performance by award.<br/><span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span><br/>
       <img border="1" src="' . SITEURL . 'library/bar_chart_grade.php?'.$queryStringAward.'" /></div></div>';		
}
//echo $section3_html1;die;
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->Ln(3);
$pdf->writeHTMLCell ( 0, 0, '', '', $section3_html1, 0, 1, 0, true, 'J', true );

// section 2 page start
if(!empty($experience)){
	$pdf->AddPage ();
	// set a bookmark for the current position
	$pdf->Bookmark ( '1.2.	Recommendations for the network', 1, 0, '', '', array (
		0,0,0
) );
	$pdf->SetFont ( 'times', 'B', 15 );
	$pdf->SetTextColor ( 0, 0, 0 );
	$pdf->SetFillColor ( 211, 211, 211 );
	$pdf->Ln(2);
	// print a line using Cell()
	$pdf->Cell ( 0, 8, '1.2. Recommendations Overview ', 1, 1, 'C', true );		
	$section2_html1 = '
	<div>
	    <table width="100%" >';
	
	foreach ( $experience as $value ) {
		$section2_html1 .= $value;
		/* $section2_html1 .= '<tr>
	                        <td width="12%" style="color:#6c0d10;font-size:12" align="center" valign="top">
	                        <img src="' . SITEURL . 'public/images/tick.png" width="25" height="25">
	                        </td>
	                        <td width="88%" style="font-size:12" align="left">' . $value . '<br/></td>
	                    </tr>'; */
	}
	$section2_html1 .= '</table>
	</div>
	';
	$pdf->SetFont ( 'times', '', 12 );
	$pdf->SetTextColor ( 0, 0, 0 );
	$pdf->writeHTMLCell ( 0, 0, '', '', $section2_html1, 0, 1, 0, true, 'J', true );
// section 2 page end
}
// add performance page
$pdf->AddPage ();
// set a bookmark for the current position
$pdf->Bookmark ( "2. Performance of schools on the Key Performance Areas (KPAs)", 0, 0, '', 'B', array (
		0,0,0
) );
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->Cell ( 0, 8, '2. Performance of schools on the Key Performance Areas (KPAs)', 0, 1, 'C', true );
$pdf->Ln(3);
$pdf->SetFont ( 'times', 'B', 15 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->SetFillColor ( 211, 211, 211 );
$pdf->Cell ( 0, 8, '2.1 Data collection, analysis and evaluation', 1, 1, 'C', true );
$pdf->Bookmark ( "2.1 Data collection, analysis and evaluation", 1, 0, '', '', array (
		0,0,0
) );
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$num_of_js = $objCustom->getStatementsCount();
$num_of_js = $num_of_js['num'];
$num_of_network =  $objCustom->getDistinctNetworkCount();
$num_of_network = $num_of_network['num'];
if(date_format ( date_create ( $dates ['startdate'] ), "Y-m" )==date_format ( date_create ( $dates ['enddate'] ), "Y-m")){
$text_show='During the month of '.$dateyearReview.'';    
}else{
$dateyearReviewend=date_format ( date_create ( $dates ['enddate'] ), "F' Y");    
$text_show='Between '.$dateyearReview.' and '.$dateyearReviewend.'';    
}
$html_data_collection = ''.$text_show.', Adhyayan undertook the review of '.$num_of_schools.' schools in the '.$network_report_name.'. The findings of the external review teams, which in some cases included network assessors, was underpinned by the detailed evidence collected and recorded by the external review teams.
		<br/><br/>
		The evidence base of the '.$num_of_schools.' schools\' effectiveness consisted of in excess of :'.
		'<ul>
		<li> '.($num_of_schools*45).' lessons observed</li>
		<li> '.($num_of_schools*4).' stakeholder interviews</li>
		<li> '.($num_of_schools*3).' learning walks</li>		
		<li> '.($num_of_schools*1).' Book looks</li>
		<li> '.($num_of_schools*$num_of_js).' descriptors of performance</li>				
		</ul><br/>
		Analysis and evaluation of these evidences has resulted in the award of the Adhyayan Quality Standard identified in the section 4.2 of this report: 
		<br/><br/>The architecture of the AQS software enables Adhyayan to interpret and sort data by an increasingly varied set of indicators to reflect the unique characteristics of each school network beyond the generic key performance indicators of the diagnostic.<br/><br/>
		In the '.$network_report_name.' report we have sorted the school data by fee, by examination board and by State in order to reflect the diversity that exists across the network\'s schools. Analysis of these data sets should enable the Province to identify appropriate priorities for improvement for the individual schools and the province with greater contextual clarity.';
/* $num_of_network>1?($html_data_collection.='<br/><br/><br/><br/><br/><br/><br/><br/><br/><hr width="30%"><br/><br/><sup>1</sup>The '.$network_report_name.' data collection is a subset of the '.$num_of_schools.' Don Bosco schools which have so far undertaken The Adhyayan Quality Standard across <no of province> provinces in Phase 1 of the National DBE programme.<br/> 
The data collected nationally represents:
		<ul>
			<li>Over '.($num_of_schools*45).' lesson observations</li>
			<li>Over '.($num_of_schools*4).' stakeholder interviews</li>
			<li>'.($num_of_schools*3).'  learning walks</li>
			<li>'.($num_of_schools*1).' book looks</li>		
		</ul><br/><br/>
		This data has so far enabled Adhyayan\'s external review teams to come to judgement on the performance of '.$network_report_name.' nationally against '.($num_of_schools*$num_of_js).' descriptors.<br/>
		<br/><sup>2</sup>This data will be included for all states in the final national review report of DBE Phase 1.
		'):''; */
$pdf->writeHTMLCell ( 0, 0, '', '', $html_data_collection, 0, 1, 0, true, 'J', true );
$pdf->AddPage();
//this section should always come on even page.So if the current page happens to be an odd page, then add a new page
if(($pdf->PageNo())%2==0 && $even_page==0)
	$pdf->addPage();
$pdf->SetFont ( 'times', 'B', 15 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->SetFillColor ( 211, 211, 211 );
$pdf->Cell ( 0, 8, '2.2 Overall performance of the schools on KPAs', 1, 1, 'C', true );
$pdf->Bookmark ( "2.2 Overall performance of the schools on KPAs", 1, 0, '', '', array (
		0,0,0
) );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->SetFont ( 'times', '', 12 );
//key for reading the report
$section3_html2 = '<div style="page-break-inside:avoid;"><br/>
		The following matrix defines the criteria by which school performance was judged.<br/>
		<span style="text-align:center;font-size:12;color:#6c0d10;font-weight:normal;">Table '.$tableNum++.'</span><br/><table cellspacing="0" cellpadding="1" border="0" nobr="true" style="font-size:12;">
	    <tr style="background-color:#CCCCCC;text-align:center;">
	        <th style="width:10%;text-align:center;">Key</th>
	        <th style="width:15%;">To be read as</th>
	        <th style="width:75%;">What this means</th>
	    </tr>
		<tr>
			<td style="background-color:#307ACE;text-align:center;">O</td>
			<td style="text-align:center;">Outstanding</td>
			<td>Best practice is consistently and visibly embedded in the culture of the school, is documented well and known to all stakeholders.</td>
		</tr>
		<tr style="background-color:#E6EBED;">
			<td style="background-color:#5e9900;text-align:center;">G</td>
			<td style="text-align:center;" >Good</td>
			<td>There are consistently visible examples of good practice that have become part of the school\'s culture and are known to all stakeholders. The leadership and management ensures secure system and processes.</td>
		</tr>
		<tr>
			<td style="background-color:#D0B122;text-align:center;">V</td>
			<td style="text-align:center;" >Variable</td>
			<td >Some examples of good practice are visible. These are not embedded in school culture and are known or practiced by only a few.</td>
		</tr>
		<tr style="background-color:#E6EBED;">
			<td style="background-color:#d12200;text-align:center;">NA</td>
			<td style="text-align:center;" >Needs Attention</td>
			<td >Action needs to be taken immediately. There is little or no evidence of good practice in the school.</td>
		</tr>
	</table>
	<br/><br/>
		<span style="font-weight:bold;font-size:12;">Note</span> Refer Section 1 - Table '.$awardTableNum.'</div>
	</div>
		';
$pdf->writeHTMLCell ( 0, 0, '', '', $section3_html2, 0, 1, 0, true, 'J', true );
$section6_html1 = "";
$section6_html1 .= "<p>The following section provides the ".$network_report_name." with a granular overview of all schools'
performance. This section identifies areas in each KPA where the schools of the network
have good or variable practice and also highlights practices that need attention.
This overview will help the network to identify practices that need to be spread to all
schools and made consistent as well as practices that need to be attended to on an urgent
basis.</p>
";
$pdf->writeHTMLCell ( 0, 0, '', '', $section6_html1, 0, 1, 0, true, 'J', true );
$section4_html1 = '
	<div style="text-align:left;font-size:12;font-weight:normal;">The performance data presented in this report refers to the schools\' performance on the
date of the external review. All findings are attributable to the school leadership that
existed during the period of the review.</div>		
    <div style="text-align:center;font-size:15;font-weight:bold;">
		<span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span><br/>
		The performance of ' . $num_of_schools . ' schools across the Key Performance Areas</div>';

$section4_html1 .= '<img style="border:1px solid #000000;" src="' . SITEURL . 'library/stacked_val.chart.php' . $performaceQstring . '" />';

$pdf->writeHTMLCell ( 0, 0, '', '', $section4_html1, 0, 1, 0, true, 'J', true );

$pdf->SetFont ( 'times', '', 10 );
$pdf->SetTextColor ( 0, 0, 0 );
//if(($pdf->PageNo())%2==0)
$pdf->AddPage();
$widthCol = count($kpaAcronyms)==7?'9.45%':'11%';
$section5_html1 = '<table cellspacing="0" cellpadding="3" border="0">
		<thead>
		<tr><th width="100%" style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum++.'</th></tr>
		<tr><th style="text-align:center;font-size:12;font-weight:bold;">Performance of each school in each Key Performance Area (KPA)</th></tr>		
	    <tr style="font-size:9;">
			<th width="34%" style="border:solid 1px #000000;"><b>School Name</b></th>
	        <th align="center" width="'.$widthCol.'" style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa1'].'</b></th>
			<th align="center" width="'.$widthCol.'" style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa2'].'</b></th>
	        <th align="center" width="'.$widthCol.'" style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa3'].'</b></th>
	        <th align="center" width="'.$widthCol.'" style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa4'].'</b></th>
		 	<th align="center" width="'.$widthCol.'"  style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa5'].'</b></th>
		 	<th align="center" width="'.$widthCol.'" style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa6'].'</b></th>';
		 	(count($kpaAcronyms)==7)? ($section5_html1 .= '<th align="center"  width="'.$widthCol.'" style="border:solid 1px #000000;"><b>'.$kpaAcronyms['kpa7'].'</b></th>'):'';		
	  $section5_html1.='</tr>
		</thead>';
$section5_tbl = "";
$count = count ( $getAllSchoolPerf );
$i = 0;
foreach ( $getAllSchoolPerf as $row ) {
	$i ++;
	$row ['kpa_order'] == 1 ? $section5_tbl .= '<tr style="font-size:9;"><td  width="34%"  style="border:solid 1px #000000;">' . $row ['client_name'] . '</td>' : '';
	$bgColor = $row ['rating_id'] == 8 ? '#307ACE;' : ($row ['rating_id'] == 7 ? '#5e9900;' : ($row ['rating_id'] == 6 ? '#D0B122;' : ($row ['rating_id'] == 5 ? '#d12200;' : '')));
	$text = $row ['rating_id'] == 8 ? 'O' : ($row ['rating_id'] == 7 ? 'G' : ($row ['rating_id'] == 6 ? 'V' : ($row ['rating_id'] == 5 ? 'NA' : '')));
	$section5_tbl .= '<td style="text-align:center;background-color:' . $bgColor . ';border:solid 1px #000000"  width="'.$widthCol.'">' .$text. '</td>';
	((count($kpaAcronyms)==6 && $row ['kpa_order'] == 6 && $i != $count)||(count($kpaAcronyms)==7 && $row ['kpa_order'] == 7 && $i != $count)) ? ($section5_tbl .= '</tr>') : (($i == $count) ? ($section5_tbl .= '</tr>') : '');
}
$section5_html1 .= $section5_tbl . "</table>";
$section5_html1 .= "<p>This table of school performance identified by KPA enables the network to:
		<ul>
			<li>identify best practice</li>
			<li>confirm schools that are in need of additional support</li>
			<li>confirm where there is a need to adopt network wide priorities </li>
		</ul></p>";
$pdf->writeHTMLCell ( 0, 0, '', '', $section5_html1, 0, 1, 0, true, 'L', true );
// cumulative report

// section 5 performance of each school on kpa


$numHeading=2;
$point = 3;
$pdf->addPage();
// cumulative report indicator bars
//$pdf->writeHTMLCell ( 0, 0, '', '', $section6_html1, 0, 1, 0, true, 'J', true );
$k=1;
foreach($kpaNames as $kpa){
	if(($pdf->PageNo())%2==0 && $even_page==0)
		$pdf->AddPage();
	$pdf->Bookmark ( $numHeading.'.'.$point.'. Performance on KPA'.$k.': '.$kpaNames['kpa'.$k], 1, 0, '', '', array (
			0,0,0
	) );
	
	$pdf->SetFont ( 'times', 'B', 13 );
	$pdf->SetTextColor ( 0, 0, 0 );
	$pdf->SetFillColor ( 211, 211, 211 );	
	$pdf->Cell ( 0, 5, $numHeading.'.'.$point++.' Performance on KPA'.$k.': '.$kpaNames['kpa'.$k], 1, 1, 'C', true );
	$pdf->SetFont ( 'times', '', 9 );
	//legends
	$pdf->ln(1);
	$legends = '<table width="100%" border="1"><tr><td><b>Judgement Key</b></td><td style="border:1px solid #000000;text-align:center;background-color:#307ACE;">Outstanding</td><td style="border:1px solid #000000;text-align:center;background-color:#5e9900;">Good</td><td style="border:1px solid #000000;text-align:center;background-color:#D0B122;">Variable</td><td style="border:1px solid #000000;text-align:center;background-color:#D12200;">Needs Attention</td></tr></table>';
	$pdf->writeHTMLCell ( 0, 0, '', '', $legends, 0, 1, 0, true, 'C', true );
	$pdf->SetTextColor ( 108, 13, 16 );
	$pdf->ln(1);	
	$pdf->Cell ( 0, 0, 'Table '.$tableNum++.' : Overall performance of schools by Key Question and Sub-Question', 0, 1, 'C', false );
	$pdf->ln(1);
	
	//print_r($kpaCQrating[$k]);die;
	$ratings = $objCustom->getKPAKqSqRatings($kpaCQrating [$k], $k, $num_of_schools );
	//print_r($ratings);die;
	$pdf->SetTextColor ( 0, 0, 0 );
	for($kqnum=1;$kqnum<=3;$kqnum++){
		$kqcqjshtml = '';
		$newkq=1;
		$pdf->SetFont ( 'times', '', 9 );
		//$pdf->writeHTMLCell ( 0, 0, '', '', $kqcqjshtml, 0, 1, 0, true, 'J', true );
		//get js data for every kq
		//$kqjsJdRatings = $objCustom->getKpaKqRatingAndJd($kpaIds['kpa'.$k],($kqnum-1)*9*$num_of_schools,9*$num_of_schools);
		//print table for kq wise js ratings and jd
		//		
		for($numcq=3*$kqnum-2;$numcq<=3*$kqnum;$numcq++){
			$kqcqJs = $objCustom->getKPAKqCqstatements($kpaIds['kpa'.$k],$kqnum,$numcq);			
			//get html kq1
			if($newkq++==1)
				$kqcqjshtml .= '<table border="0" cellpadding="3" nobr="true">						
						<tr style="background-color:#DCDCDC"><td style="border:1px solid #000000;font-weight:bold;width:7%;text-align:center;font-size:10;'.$ratings['kq'.$kqnum.'weightage'].'">KQ'.$kqnum.'</td><td colspan="5"  style="border:1px solid #000000;font-weight:bold;width:93%;font-size:10;">'.$kqcqJs[0]['key_question_text'].'</td></tr>
						';
				$kqcqjshtml .= $objCustom->getKPAKqCqTable($kqcqJs,$numcq,$ratings);
		}
		$kqcqjshtml .='</table>';		
		$pdf->writeHTMLCell ( 0, 0, '', '', ''.$kqcqjshtml.'', 0, 1, 0, true, 'J', true );
	
		//$pdf->writeHTMLCell ( 0, 0, '', '', $kqjsJdRatingsTbl, 0, 1, 0, true, 'J', true );
	}
	$pdf->AddPage ();
	//add kpa-state,board,fee wise analysis
	$kpa_html = "";
        $title="";
	//statewise
	$kpa_state = $objCustom->getKPAParameterWiseAnalysis($kpaIds['kpa'.$k],'state');
	$kpa_fee = $objCustom->getKPAParameterWiseAnalysis($kpaIds['kpa'.$k],'fee');
	$kpa_board = $objCustom->getKPAParameterWiseAnalysis($kpaIds['kpa'.$k],'board');
	$uriStringImg = "";
	//prepare uri to created stacked chart
	$pdf->SetFont ( 'times', '', 10 );
	//$pdf->Ln(1);
	$uriStringState = $objCustom->getKPAparameterGraph($kpa_state);
	$uriStringFee = $objCustom->getKPAparameterGraph($kpa_fee);
	$uriStringBoard = $objCustom->getKPAparameterGraph($kpa_board);
	$uriStringImg1 = strlen($uriStringState)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringState . '" />':'';
	$uriStringImg2 = strlen($uriStringFee)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringFee . '" />':'';
	$uriStringImg3 = strlen($uriStringBoard)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringBoard . '" />':'';
	//echo $uriStringImg1,$uriStringImg2,$uriStringImg3;die;
	$table ='<span style="text-align:center;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span> : <span style="text-align:center;"> Performance of all schools categorised by state, school fees and examination board</span><br/>
			<div style="border:1px solid #000000;">
			<table border="0" cellpadding="1" cellspacing="0" width="100%">
			<tr style="text-align:center;">'.($uriStringImg1!=''?'<td>State<br/>'.$uriStringImg1.'</td>':'').($uriStringImg2!=''?'<td>Fee<br/>'.$uriStringImg2.'</td>':'').($uriStringImg3!=''?'<td>Board<br/>'.$uriStringImg3.'</td>':'').'</tr>
			</table>
			</div>';
	$kpa_html .= '<div style="page-break-inside:avoid;" >'.($k==1?$title.'<br/>':'').$table.'</div>';
	
	$pdf->writeHTMLCell ( 0, 0, '', '', $kpa_html, 0, 1, 0, true, 'J', true );
	$pdf->Ln(1);
	$pdf->writeHTMLCell ( 0, 0, '', '', $legends, 0, 1, 0, true, 'C', true );
	$pdf->Ln(1);
	$agreementGraphData =$objCustom->getKPAJd01($kpaIds['kpa'.$k],'desc');
	//get top schools - least distance
	$topSchoolsJd01 = $objCustom->getToporBottomSchoolsJD01(array_reverse($agreementGraphData),'81andabove','61-80');
	//print_r($topSchoolsJd01);
        $agreementGraphData_asc =$objCustom->getKPAJd01($kpaIds['kpa'.$k],'asc');
	//get bottom schools - highest distance	
	$bottomSchoolsJd01 = $objCustom->getToporBottomSchoolsJD01($agreementGraphData_asc,'0-20','21-40');
	
	$uriAgreementString = $objCustom->getKPAparameterGraph($agreementGraphData);
	$uriAgreementGraph = strlen($uriStringState)>0?'<img style="border:solid 1px #000;" src="' . SITEURL . 'library/stacked_jd01.php?' . $uriAgreementString . '" />':'';	
	
	$agreementGraph =$uriAgreementGraph;
	$agreementGraphHtml = empty($agreementGraphData)?'':$agreementGraph;
	$table = "";
	//table for top schools
	if(!empty($topSchoolsJd01)){
		$table .= '<table border="1" cellpadding="3" ><tr><td><b>List of schools closest to the external review ratings</b></td></tr>';
		foreach($topSchoolsJd01 as $tkey=>$tschool)
			$table .='<tr><td >'.$tschool.'</td></tr>';
		$table .='</table>';
	}
	unset($tkey);
	unset($tschool);
	if(!empty($bottomSchoolsJd01)){
		$table .= '<br/><table border="1" cellpadding="3" ><tr><td><b>List of schools farthest from the external review ratings</b></td></tr>';
		foreach($bottomSchoolsJd01 as $bkey=>$bschool)
			$table .='<tr><td>'.$bschool.'</td></tr>';
		$table .='</table>';
	}
	unset($bkey);
	unset($bschool);
	//print_r($table);die;
	//$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;" >'.$agreementGraphHtml.$table.'</div>', 0, 1, 0, true, 'J', true );
	$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;" ><table cellpadding="3" cellspacing="3"><tr><td colspan="2"><span style="text-align:center;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span> : <span style="text-align:center;">The level of agreements between the school\'s self-review team and external review team</span></td></tr><tr><td style="width:30%;">'.$agreementGraphHtml.'</td><td style="width:70%;">'.$table.'</td></tr></table></div>', 0, 1, 0, true, 'J', true );
	//get champion schools
	$champSchoolsData = $objCustom->getChampionSchools($kpaIds['kpa'.$k]);
        $vulnerableSchoolsData = $objCustom->getVulnerableSchools($kpaIds['kpa'.$k]);
        $champSchoolsData_key_ch=$this->db->array_grouping($champSchoolsData,"client_name");
        $vulnerableSchoolsData_key_ch=$this->db->array_grouping($vulnerableSchoolsData,"client_name");
        
        $common_vulnerable_champ=array_intersect_key($champSchoolsData_key_ch,$vulnerableSchoolsData_key_ch);
        
        //$vulnerableSchoolsData_key_ch=$this->db->array_grouping($vulnerableSchoolsData,"client_name");
        //$common_vulnerable_champ=array_intersect_key($champSchoolsData,$vulnerableSchoolsData);
        //$common_vulnerable_champ=$this->db->array_grouping($common_vulnerable_champ,"client_name");
        //$common_vulnerable_champ = array_column($common_vulnerable_champ, 'client_name');
        $common_vulnerable_champ = array_keys($common_vulnerable_champ);
        
        $champ_msg="";
	$table1 = "";
	$colspan=0;	
	$champcount=0;
	//echo $objCustom->getPageCount();die;
	$champcount = count($champSchoolsData);	
	$showChampVulnerableTitle = 0;
	if($champcount>10)
		($table1 = '<br/><br/><b>Note:</b> '.$champcount.' schools out of '.$num_of_schools.' schools have been identified as "Champion".') && $colspan++;
	else {				
		//table for champ schools
		if(!empty($champSchoolsData)){
			$showChampVulnerableTitle=1;
			$colspan++;
			$table1 .= '<span style="text-align:center;font-size:12;color:#6c0d10;font-weight:normal;">Table '.$tableNum++.'</span><br/><table border="1" cellpadding="3" ><tr><td><b>Champion Schools</b></td></tr>';
			/*foreach($champSchoolsData as $tkey=>$tschool)
				$table1 .='<tr><td style="border:solid 1px #000;">'.$tschool['client_name'].'</td></tr>';
				$table1 .='</table>';*/
                        foreach($champSchoolsData as $tkey=>$tschool)
				in_array($tschool['client_name'],$common_vulnerable_champ)?$table1 .='<tr><td style="border:solid 1px #000;">'.$tschool['client_name'].'*</td></tr>':$table1 .='<tr><td style="border:solid 1px #000;">'.$tschool['client_name'].'</td></tr>';
				$table1 .='</table>';
			if(count($common_vulnerable_champ)>0){
			$champ_msg='<br><span style="font-size:11px;">* Champion schools with some areas needing urgent attention.</span>';
			}
		}
		unset($tkey);
		unset($tschool);
	}
	//$vulnerableSchoolsData = $objCustom->getVulnerableSchools($kpaIds['kpa'.$k]);
	//table for vulnerable schools
	$table2="";
	$vulnerablecount=0;
	$vulnerablecount = count($vulnerableSchoolsData);	
	if($vulnerablecount>10)
		($table2 = '<br/><br/><b>Note:</b> '.$vulnerablecount.' schools out of '.$num_of_schools.' schools have been identified as "Vulnerable" and need urgent attention & great support from the network.') && $colspan++;
	else {
		if(!empty($vulnerableSchoolsData)){
			$showChampVulnerableTitle=1;
			$colspan++;
			$table2 .= '<span style="text-align:center;font-size:12;color:#6c0d10;font-weight:normal;">Table '.$tableNum++.'</span><br/><table border="1" cellpadding="3" ><tr><td><b>Vulnerable Schools</b></td></tr>';
			/*foreach($vulnerableSchoolsData as $tkey=>$tschool)
				$table2 .='<tr><td style="border:solid 1px #000;">'.$tschool['client_name'].'</td></tr>';
				$table2 .='</table>';*/
                        foreach($vulnerableSchoolsData as $tkey=>$tschool)
				//$table2 .='<tr><td style="border:solid 1px #000;">'.$tschool['client_name'].'</td></tr>';
                                in_array($tschool['client_name'],$common_vulnerable_champ)?$table2 .='<tr><td style="border:solid 1px #000;">'.$tschool['client_name'].'*</td></tr>':$table2 .='<tr><td style="border:solid 1px #000;">'.$tschool['client_name'].'</td></tr>';
				$table2 .='</table>';
                        if(count($common_vulnerable_champ)>0){
			$champ_msg ='<br><span style="font-size:11px;">* Champion schools with some areas needing urgent attention.</span>';
			}
		}
	}
        if($vulnerablecount>10 && $champcount>10){
$htmlsmg='<div style="page-break-inside:avoid;" ><table cellpadding="3" cellspacing="3"><tr><td><b>Notes:</b><br>(i) '.$champcount.' schools out of '.$num_of_schools.' schools have been identified as "Champion".';

                        if(count($common_vulnerable_champ)>0){
		                if(count($common_vulnerable_champ)>10){
				$htmlsmg .=' Out of these, the <b>'.implode(",",$common_vulnerable_champ).'</b> Champion School(s) need urgent attention in certain areas.<br>';
		                }else{
                                 $htmlsmg .=' Out of these, the following Champion School(s) need urgent attention in certain areas:';
                                 $htmlsmg .='<br><table border="0" cellpadding="5" width="100%">';
		                         foreach($common_vulnerable_champ as $key=>$val){
                                         $htmlsmg .='<tr><td><b>'.($key+1).'. '.$val.'</b></td></tr>';
		                         }
                                 $htmlsmg .='</table><br>';  

		                }
                        }

$htmlsmg.='<br>(ii) '.$vulnerablecount.' schools out of '.$num_of_schools.' schools have been identified as "Vulnerable" and need urgent attention & great support from the network';

$htmlsmg.='</td></tr></table></div>';

$colspan>0?$pdf->writeHTMLCell ( 0, 0, '', '', $htmlsmg, 0, 1, 0, true, 'J', true ):'';

}else{
	$colspan>0?$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;" ><table cellpadding="3" cellspacing="3"><tr '.($showChampVulnerableTitle==0?' style="display:none;"':'').'><td colspan="'.$colspan.'"><span>The following tables identify the champion and vulnerable schools of the network, to enable the network leaders create a buddy and mentoring system for all schools to achieve "Good".</span></td></tr><tr>'.($colspan==1?'<td style="width:100%;">'.($table1==""?$table2:$table1).'</td>':'<td style="width:50%;">'.$table1.'</td><td style="width:50%;">'.$table2.'</td></tr></table>'.$champ_msg.'</div>'), 0, 1, 0, true, 'J', true ):'';
}
        $k++;
	unset($table);
	unset($tkey);
	unset($tschool);
	$pdf->AddPage();
	unset($ratings);
	if($k==7)
		break;//kpa 7 has different analysis
}
if($include_self_review==1 && $kpa7Id!=''){
	if(($pdf->PageNo())%2==0 && $even_page==0)
		$pdf->AddPage();
		$pdf->Bookmark ( $numHeading.'.'.$point.'. Performance on KPA'.$k.': '.$kpa7Name, 1, 0, '', '', array (
				0,0,0
		) );
		$pdf->SetFont ( 'times', 'B', 13 );
		$pdf->SetTextColor ( 0, 0, 0 );
		$pdf->SetFillColor ( 211, 211, 211 );
		$pdf->Cell ( 0, 5, $numHeading.'.'.$point.' Performance on KPA'.$k.': '.$kpa7Name, 1, 1, 'C', true );		
		$pdf->SetFont ( 'times', '', 9 );
		$pdf->SetTextColor ( 0, 0, 0 );
		if($is_validated==0){
			$pdf->Cell ( 0, 5, '* Self-review not Validated', 0, 1, 'R', false );
		}
		$pdf->ln(1);
		$legends = '<table width="100%"  border="1"><tr><td><b>Judgement Key</b></td><td style="border:1px solid #000000;text-align:center;background-color:#307ACE;">Outstanding</td><td style="border:1px solid #000000;text-align:center;background-color:#5e9900;">Good</td><td style="border:1px solid #000000;text-align:center;background-color:#D0B122;">Variable</td><td style="border:1px solid #000000;text-align:center;background-color:#D12200;">Needs Attention</td></tr></table>';
		$pdf->writeHTMLCell ( 0, 0, '', '', $legends, 0, 1, 0, true, 'C', true );
		$pdf->SetTextColor ( 108, 13, 16 );
		$pdf->ln(1);
		$pdf->Cell ( 0, 0, 'Table '.$tableNum++.' : Overall performance of schools by Key Question and Sub-Question', 0, 1, 'C', false );
		$pdf->ln(1);
		//print_r($kpaCQrating[$k]);die;
		$ratings = $objCustom->getKPAKqSqRatings($kpa7Schools, $k, $num_of_schools );
		//print_r($ratings);die;
		$pdf->SetTextColor ( 0, 0, 0 );
		for($kqnum=1;$kqnum<=3;$kqnum++){
			$kqcqjshtml = '';
			$newkq=1;
			$pdf->SetFont ( 'times', '', 9 );
			//$pdf->writeHTMLCell ( 0, 0, '', '', $kqcqjshtml, 0, 1, 0, true, 'J', true );
			//get js data for every kq
			//$kqjsJdRatings = $objCustom->getKpaKqRatingAndJd($kpa7Id,($kqnum-1)*9*$num_of_schools,9*$num_of_schools);
			//print table for kq wise js ratings and jd
			//
			for($numcq=3*$kqnum-2;$numcq<=3*$kqnum;$numcq++){
				$kqcqJs = $objCustom->getKPAKqCqstatements($kpa7Id,$kqnum,$numcq);
				//get html kq1
				if($newkq++==1)
					$kqcqjshtml .= '<table border="0" cellpadding="3" nobr="true">						
						<tr style="background-color:#DCDCDC"><td style="border:1px solid #000000;font-weight:bold;width:7%;text-align:center;font-size:10;'.$ratings['kq'.$kqnum.'weightage'].'">KQ'.$kqnum.'</td><td colspan="5"  style="border:1px solid #000000;font-weight:bold;width:93%;font-size:10;">'.$kqcqJs[0]['key_question_text'].'</td></tr>
						';
					$kqcqjshtml .= $objCustom->getKPAKqCqTable($kqcqJs,$numcq,$ratings);
			}
			$kqcqjshtml .='</table>';
			$pdf->writeHTMLCell ( 0, 0, '', '', ''.$kqcqjshtml.'', 0, 1, 0, true, 'J', true );
	
			//$pdf->writeHTMLCell ( 0, 0, '', '', $kqjsJdRatingsTbl, 0, 1, 0, true, 'J', true );
		}
		$pdf->AddPage ();
		//add kpa-state,board,fee wise analysis
		$kpa_html = "";
		//statewise
		$kpa_state = $objCustom->getKPA7ParameterWiseAnalysis($kpa7Id,'state');
		$kpa_fee = $objCustom->getKPA7ParameterWiseAnalysis($kpa7Id,'fee');
		$kpa_board = $objCustom->getKPA7ParameterWiseAnalysis($kpa7Id,'board');
		$uriStringImg = "";
		//$pdf->Ln(1);		
		//prepare uri to created stacked chart
		$pdf->SetFont ( 'times', '', 10 );
		$uriStringState = $objCustom->getKPAparameterGraph($kpa_state);
		$uriStringFee = $objCustom->getKPAparameterGraph($kpa_fee);
		$uriStringBoard = $objCustom->getKPAparameterGraph($kpa_board);
		$uriStringImg1 = strlen($uriStringState)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringState . '" />':'';
		$uriStringImg2 = strlen($uriStringFee)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringFee . '" />':'';
		$uriStringImg3 = strlen($uriStringBoard)>0?'<img  src="' . SITEURL . 'library/stacked_multiple.php?' . $uriStringBoard . '" />':'';
		//echo $uriStringImg1,$uriStringImg2,$uriStringImg3;die;
		$table ='<span style="text-align:center;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span> : <span style="text-align:center;"> Performance of all schools categorised by state, school fees and examination board</span><br/>
			<div style="border:1px solid #000000;">
			<table border="0" cellpadding="1" cellspacing="0" width="100%">
			<tr style="text-align:center;">'.($uriStringImg1!=''?'<td>State<br/>'.$uriStringImg1.'</td>':'').($uriStringImg2!=''?'<td>Fee<br/>'.$uriStringImg2.'</td>':'').($uriStringImg3!=''?'<td>Board<br/>'.$uriStringImg3.'</td>':'').'</tr>
			</table>
			</div>';
		$kpa_html .= '<div style="page-break-inside:avoid;" >'.($k==1?$title.'<br/>':'').$table.'</div>';		
		$pdf->writeHTMLCell ( 0, 0, '', '', $kpa_html, 0, 1, 0, true, 'J', true );
		$pdf->Ln(1);
		$pdf->writeHTMLCell ( 0, 0, '', '', $legends, 0, 1, 0, true, 'C', true );
		$pdf->Ln(1);
		if($is_validated==1){
		$agreementGraphData =$objCustom->getKPAJd01($kpa7Id,'desc');
		//get top schools - least distance
		$topSchoolsJd01 = $objCustom->getToporBottomSchoolsJD01(array_reverse($agreementGraphData),'81andabove','61-80');
		//print_r($topSchoolsJd01);
                $agreementGraphData_asc =$objCustom->getKPAJd01($kpa7Id,'asc');
		//get bottom schools - highest distance
		$bottomSchoolsJd01 = $objCustom->getToporBottomSchoolsJD01($agreementGraphData_asc,'0-20','21-40');
	
		$uriAgreementString = $objCustom->getKPAparameterGraph($agreementGraphData);
		$uriAgreementGraph = strlen($uriStringState)>0?'<img style="border:solid 1px #000;" src="' . SITEURL . 'library/stacked_jd01.php?' . $uriAgreementString . '" />':'';
		$agreementGraph =$uriAgreementGraph;
		$agreementGraphHtml = empty($agreementGraphData)?'':$agreementGraph;
		$table = "";
		//table for top schools
		if(!empty($topSchoolsJd01)){
			$table .= '<table border="1" cellpadding="3" ><tr><td><b>List of schools closest to the external review ratings</b></td></tr>';
			foreach($topSchoolsJd01 as $tkey=>$tschool)
				$table .='<tr><td >'.$tschool.'</td></tr>';
				$table .='</table>';
		}
		unset($tkey);
		unset($tschool);
		if(!empty($bottomSchoolsJd01)){
			$table .= '<br/><table border="1" cellpadding="3" ><tr><td><b>List of schools farthest from the external review ratings</b></td></tr>';
			foreach($bottomSchoolsJd01 as $bkey=>$bschool)
				$table .='<tr><td >'.$bschool.'</td></tr>';
				$table .='</table>';
		}
		unset($bkey);
		unset($bschool);
		//print_r($table);die;
		//$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;" >'.$agreementGraphHtml.$table.'</div>', 0, 1, 0, true, 'J', true );
		$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;" ><table cellpadding="3" cellspacing="3"><tr><td colspan="2"><span style="text-align:center;font-weight:normal;color:#6c0d10">Graph '.$graphNum++.'</span> : <span style="text-align:center;">The level of agreements between the school\'s self-review team and external review team</span></td></tr><tr><td style="width:30%;">'.$agreementGraphHtml.'</td><td style="width:70%;">'.$table.'</td></tr></table></div>', 0, 1, 0, true, 'J', true );
		//get champion schools
		$champSchoolsData = $objCustom->getChampionSchools($kpa7Id);
		$table1 = "";
		$colspan=0;
		//echo $objCustom->getPageCount();die;
		$champcount=0;
		$showChampVulnerableTitle = 0;
		$champcount = count($champSchoolsData);	
		if($champcount>10)
			($table1 = '<br/><br/><b>Note:</b>'.$champcount.' schools out of '.$num_of_schools.' schools have been identified as "Champion".') && $colspan++;
			else {
				//table for champ schools
				if(!empty($champSchoolsData)){
					$showChampVulnerableTitle=1;
					$colspan++;
					$table1 .= '<span style="text-align:center;font-size:12;color:#6c0d10;font-weight:normal;">Table '.$tableNum++.'</span><br/><table border="1" cellpadding="3" ><tr><td><b>Champion Schools</b></td></tr>';
					foreach($champSchoolsData as $tkey=>$tschool)
						$table1 .='<tr><td style="border:solid 1px #000;">'.$tschool['client_name'].'</td></tr>';
						$table1 .='</table>';
				}
				unset($tkey);
				unset($tschool);
			}
			$vulnerableSchoolsData = $objCustom->getVulnerableSchools($kpa7Id);
			//table for vulnerable schools
			$table2="";
			$vulnerablecount=0;
			$vulnerablecount = count($vulnerableSchoolsData);
			if($vulnerablecount>10)
				($table2 = '<br/><br/><b>Note:</b>'.$vulnerablecount.' schools out of '.$num_of_schools.' schools have been identified as "Vulnerable" and need urgent attention & great support from the network.') && $colspan++;
				else {
					if(!empty($vulnerableSchoolsData)){
						$showChampVulnerableTitle=1;
						$colspan++;
						$table2 .= '<span style="text-align:center;font-size:12;color:#6c0d10;font-weight:normal;">Table '.$tableNum++.'</span><br/><table border="1" cellpadding="3" ><tr><td><b>Vulnerable Schools</b></td></tr>';
						foreach($vulnerableSchoolsData as $tkey=>$tschool)
							$table2 .='<tr><td style="border:solid 1px #000;">'.$tschool['client_name'].'</td></tr>';
							$table2 .='</table>';
					}
				}
				$colspan>0?$pdf->writeHTMLCell ( 0, 0, '', '', '<div style="page-break-inside:avoid;" ><table cellpadding="3" cellspacing="3"><tr '.($showChampVulnerableTitle==0?' style="display:none;"':'').'><td colspan="'.$colspan.'"><span>The following tables identify the champion and vulnerable schools of the network, to enable the network leaders create a buddy and mentoring system for all schools to achieve "Good".</span></td></tr><tr>'.($colspan==1?'<td style="width:100%;">'.($table1==""?$table2:$table1).'</td>':'<td style="width:50%;">'.$table1.'</td><td style="width:50%;">'.$table2.'</td></tr></table></div>'), 0, 1, 0, true, 'J', true ):'';
				$k++;
				unset($table);
				unset($tkey);
				unset($tschool);
				$pdf->AddPage();
				unset($ratings);
		}
                else
                    $pdf->AddPage();
}
// set a bookmark for the current position
$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->Bookmark ( "3. Schools' Effectiveness in applying the self-review diagnostic", 0, 1, '', 'B', array (
		0,0,0
) );
// print a line using Cell()
$pdf->Cell ( 0, 8, '3. School Effectiveness in applying the self-review diagnostic', 1, 1, 'C', true );
$pdf->SetFont ( 'times', 'B', 15 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->SetFillColor ( 211, 211, 211 );
$pdf->Bookmark ( "3.1. Judgement distance between self-review and external review", 1, 1, '', '', array (
		0,0,0
) );
$pdf->Ln(3);
$pdf->Cell ( 0, 8, '3.1. Judgement distance between self-review and external review', 1, 1, 'C', true );

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
$urlArray = array();
$rankArray = array();
$url_count = 0;
foreach ( $clientsJd as $jd ) {	
	$client_name = urlencode ( $jd ['client_name'] );
	$allJD = $jd ['JD'];
	$agreements = substr_count ( $allJD, 0 );
	$disagree1 = substr_count ( $allJD, 1 );
	$disagree2 = substr_count ( $allJD, 2 );
	$disagree3 = substr_count ( $allJD, 3 );
	$urlArray[$jd ['client_name']] = "i" . $j . "=" . $client_name . ";" . $agreements . ";" . $disagree1 . ";" . $disagree2 . ";" . $disagree3;	
	//calculate weightage
	$total = $agreements + $disagree1 + $disagree2 +$disagree3;
	$weightage = $objCustom->getWeightage($agreements*100/$total,8)+$objCustom->getWeightage($disagree1*100/$total,7)+$objCustom->getWeightage($disagree2*100/$total,6)+$objCustom->getWeightage($disagree3*100/$total,5);
	$rankArray[$jd ['client_name']] = $weightage;
	$j++;
}
arsort($rankArray,SORT_NUMERIC);
//url
$isGraphPage1 = 1;
$k=1;
foreach($rankArray as $key=>$val){
	$k++;
	if(($k%16==0 && $isGraphPage1==1)|| (($k+3)%19==0 && $isGraphPage1==0)){//after 13 schools create a new image
		$isGraphPage1 =0;
		$url_count++;
		$url[$url_count]="?";
	}
	$url[$url_count] .= $urlArray[$key].'&';
}
for($i=0;$i<=$url_count;$i++){
	
$section8_html1 .= '<div style="page-break-inside:avoid;">'.($i==0?'<span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10;width:100%">Graph '.$graphNum++.'</span>':'').		
'<div style="text-align:center;font-weight:bold;">Judgement distance between the self-review and external review ratings arranged from <br/>highest to lowest </div>
<img style="border:solid 1px #000000;" src="' . SITEURL . '/library/stacked.chart_jd.php?' . substr($url[$i],0,-1) . '">
</div>';
}

$pdf->writeHTMLCell ( 0, 0, '', '', $section8_html1, 0, 1, 0, true, 'J', true );
$pdf->AddPage ();
// set a bookmark for the current position
$pdf->SetFont ( 'times', 'B', 15 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->SetFillColor ( 211, 211, 211 );
$pdf->Cell ( 0, 8, '3.2. Limitations of first use of the diagnostic', 1, 1, 'C', true );
$pdf->Bookmark ( '3.2. Limitations of first use of the diagnostic', 1, 0, '', '', array (
		0,0,0 
) );

// print a line using Cell()
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$section9_html1 = "";
$section9_html1 .= '<p>It is inevitable that the first time a school prepares to undertake a self-review it will not be as accurate in coming to judgement about
&lsquo;what good looks like&rsquo; as the experienced external validation team. 
By the end of review process, the school self-review and evaluation team (SSRE) is likely to have a much better grasp of what strong evidence looks like. <br/><br/>
		Where judgement distance is significant, it isn\'t because the school is collecting poor evidence. Schools almost always collects good evidence. The problem is that either there is not enough evidence, or more frequently, the school has not studied the diagnostic statements closely enough.  Here are a few more common errors:
<ul>
<li>An inaccurate interpretation of the statement in question, "Oh, we read it to mean something else."</li>
<li>Partial reading of the statement, "We only collected evidence on the first half of the statement. We didn&rsquo;t realise that every word or phrase counts."</li>
<li>Incomplete understanding of how much evidence you need to collect to make a secure judgement, "You mean that we have to collect evidence from every class in the school not just a few of them.  And it is not enough for something to happen once or twice a year?"</li>
<li>The teacher or leader uses their own practice as their reference point for what good looks like, "I&rsquo;m sure the rest of the school is doing it. I certainly am!"</li>
<li>Making a judgement on the school&rsquo;s effectiveness which does not take account of all aspects of the diagnostic statement or of all stakeholders awareness, "I know we haven&rsquo;t documented this practice but I am sure we shared it with parents at a meeting."</li>
<li>Making a judgement with reference to the school&rsquo;s status locally and not to the national standard. "Everyone says we are the best school parents, students, even teachers, so it must be &lsquo;Always&rsquo;.</li>	
</ul>
The final step in the school self-review process occurs on the final day of the self-review, when the whole self-review team comes together to moderate the judgements of each its KPA teams. Unfortunately, this step is not always taken! The consequence of missing on this essential step is that often there is a very significant difference between the School\'s and the external review team\'s judgements.<br/>
<br/>
The quality dialogue almost always results in a significant shift in the SSRE team\'s judgement about the nature and quality of evidence required to make a secure judgement. This insightful discussion is intended to result in building a consensus between the SSRE and SERE teams across all performance areas and regarding the whole school judgement.   		
<br/>		
<br/>		
<b>Recommendations:</b><br/>
All schools benefit from a regular use of the Adhyayan Quality Diagnostic
throughout their development journey, taking steps to engage with resources and
resource people to increasing their knowledge of What Good Looks Like. Some exemplars
on the Adhyayan website that may help can be found on <a href="http://adhyayan.asia/site/" target="_blank">http://adhyayan.asia/site/</a>
 <br/><br/>
If the '.$network_report_name.' wishes, it will be possible to arrange visits to &lsquo;Good&rsquo; schools within the Adhyayan network to experience aspects of good practice.  		
</p>';


$pdf->writeHTMLCell ( 0, 0, '', '', $section9_html1, 0, 1, 0, true, 'J', true );

$pdf->AddPage ();

$pdf->SetFont ( 'times', 'B', 16 );
$pdf->SetTextColor ( 255, 255, 255 );
$pdf->SetFillColor ( 108, 13, 16 );
$pdf->Cell ( 0, 8, '4. Appendix', 0, 1, 'C', true );
// set a bookmark for the current position
$pdf->Bookmark ( "4. Appendix", 0, 0, '', 'B', array (
		0,0,0
) );
$pdf->Ln(3);
$pdf->SetFont ( 'times', 'B', 15 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->SetFillColor ( 211, 211, 211 );
$pdf->Cell ( 0, 8, '4.1. Judgement distance for all schools on each KPA', 1, 1, 'C', true );
$pdf->Bookmark ( "4.1. Judgement distance for all schools on each KPA", 1, 0, '', '', array (
		0,0,0
) );

$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->Ln ( 3 );
$section10_htmlintro = "<p>The following tables will help the network leaders to identify every school's
 accuracy in their self-review on each of the Key Performance Area.  It will help the network to organise
 visits and create a buddy system for the schools to facilitate and grow every school's understanding of
 what &lsquo;Good&rsquo; looks like.</p><br/>There is common agreement internationally that the single most important characteristic of improving schools is the fact that they know themselves well.  Without a clear understanding of their current position, a school will be unable to make significant progress because it has no secure foundation on which to build.";
$pdf->writeHTMLCell ( 0, 0, '', '', $section10_htmlintro, 0, 1, 0, true, 'J', true );
$pdf->SetFont ( 'times', '', 9 );
 $i=0;
foreach($kpaNames as $key=>$kpaname) 
{	
	$section10_html1 = "";
	$section10_html1 .= $objCustom->createKpaJDtbl ( $tableNum++,$kpaJDarr [++$i], $kpaname );
	$pdf->writeHTMLCell ( 0, 0, '', '', $section10_html1, 0, 1, 0, true, 'J', true );
	/* if ($i>0 && $i<7 &&  $addSchoolNum >16 && $addSchoolNum=0 )
		$pdf->AddPage ();
	$addSchoolNum += $num_of_schools; */
	$pdf->Ln ( 3 );
}
 unset($i);
//annexure 2
$pdf->AddPage ();
// print a line using Cell()
$pdf->SetFont ( 'times', 'B', 15 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->SetFillColor ( 211, 211, 211 );
$pdf->Cell ( 0, 8, '4.2  AQS Awards presented to each school', 1, 1, 'C', true );
$pdf->Bookmark ( "4.2 AQS Awards presented to each school", 1, 0, '', '', array (
		0,0,0
) );
$pdf->SetFont ( 'times', '', 9 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->Ln ( 3 );
//create statewise table for schooldata
$pdf->SetFont ( 'times', '', 9 );
$pdf->SetTextColor ( 0, 0, 0 );
//print_r($statewiseSchoolAwards);die;
foreach($statewiseSchoolAwards as $k=>$stdata){	
	$table = '<table border="0" cellpadding="3">
					<thead>
						<tr><th colspan="7"><span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum++.' : AQS Awards presented to the '.$network_report_name.' Schools in '.$k.'</span>'.'</th></tr>
						<tr style="background-color:#c0c0c0;text-align:center;border:1px solid #000000;">						                      
                        <th style=";border:1px solid #000000;" align="center" width="27%">School Name</th>
                        <th style=";border:1px solid #000000;" align="center" width="10%">Location</th>
						<th style=";border:1px solid #000000;" align="center" width="12%">Medium of Instruction</th>
						<th style=";border:1px solid #000000;" align="center" width="8%">Board</th>
						<th style=";border:1px solid #000000;" align="center" width="13%">Annual Fees</th>
						<th style=";border:1px solid #000000;" align="center" width="14%">Type of School</th>
                        <th style=";border:1px solid #000000;" align="center" width="16%">Award<span style="color:#FF0000;">*</span></th>
                    	</tr>
				</thead>';
	foreach($stdata as $key=>$value)
		$table.= '<tr style="border:1px solid #000000;">                           
                            <td style=";border:1px solid #000000;" align="left"  width="27%">' . $value ['client_name'] . '</td>							                           		
                            <td style=";border:1px solid #000000;" align="left"  width="10%">' . $value ['region_name'] . '</td>
                           	<td style=";border:1px solid #000000;" align="left"  width="12%">' . $value ['language_name'] . '</td>
							<td style=";border:1px solid #000000;" align="left"  width="8%">' . $value ['board'] . '</td>
							<td style=";border:1px solid #000000;" align="left"  width="13%">' . $value ['annual_fee'] . '</td>
							<td style=";border:1px solid #000000;" align="left"  width="14%">' . $value ['school_type'] . '</td>		
                            <td style=";border:1px solid #000000;" align="center"  width="16%">' . $value ['external_award_text'] . '</td>
                        </tr>';
	$table .='</table><br/>';	
	$pdf->writeHTMLCell ( 0, 0, '', '', $table, 0, 1, 0, true, 'J', true );
}
$note = '<div style="page-break-inside:avoid;">					
			<span style="color:#FF0000;"><sup>*</sup></span> Refer Section 1 - Table '.$awardTableNum.'<br/><br/>
			<span style="font-size:12;font-weight:bold;">Disclaimer:</span>This data is self-reported by schools in the software on the school profile page.					 
					</div>';

$pdf->writeHTMLCell ( 0, 0, '', '', $note, 0, 1, 0, true, 'J', true );
//echo $pdf->GetY();die;
$pdf->SetFont ( 'times', '', 9 );
$aqsDisclaimer = '<b>AQS Disclaimer</b><br/>Every review and inspection regime around the world, including Adhyayan, which make  judgements on school effectiveness  based on qualitative evidence is potentially subject to human error. All put systems in place to reduce that possibility to a minimum. Adhyayan\'s strategy to reduce variability of judgement is through the application of a diagnostic which embeds the expertise of assessors in its rubric and contents, thereby diminishing the reliance on individual expertise. While our evidence is that our assessments are highly reliable there is always the possibility of human error. This does not, however, diminish its importance in enabling sustained school improvement.';
$margin = $pdf->getMargins();
$contentHeight =  $pdf->getPageHeight()-$margin['top']-$margin['bottom']-20	 ;
$y='';
if($pdf->GetY()<=$contentHeight)
	$y=$contentHeight;
else
	$pdf->addPage();
//$y = $pdf->GetY()<=$contentHeight?($contentHeight && $pdf->AddPage()):'';
//echo $pdf->GetY(),' aa ',$contentHeight;die;
$pdf->writeHTMLCell ( 0, 0, '', $y, $aqsDisclaimer, 0, 1, 0, true, 'J', true );
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
$pdf->SetFont ( 'times', '', 12 );
$pdf->SetTextColor ( 0, 0, 0 );
$pdf->addTOC(2, PDF_FONT_MONOSPACED, '.', '', 'B', array(0,0,0));

//$secondpagehtml [0] = '<table border="1" cellpadding="5" cellspacing="0" style="background-color:#fff">' . '<tr style="text-align: left">' . '<td width="155mm">' . '<span style="font-family:times;font-weight:normal;font-size:12pt;color:black;">#TOC_DESCRIPTION#</span>' . '</td>' . '<td width="25mm" style="text-align:center;">' . '<span style="font-weight:normal;font-size:12pt;color:black;">#TOC_PAGE_NUMBER#</span>' . '</td>' . '</tr>' . '</table>';

// add table of content at page 1
// (check the example n. 45 for a text-only TOC
/* $pdf->addHTMLTOC ( 2, 'INDEX', $secondpagehtml, true, 'B', array (
		128,
		0,
		0 
) ); */

// end of TOC page
$pdf->endTOCPage ();
// . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
// ---------------------------------------------------------

$path = ROOT.'reports';
// Close and output PDF document
//$pdf->Output ($path.'/'.$network_report_name.'.pdf', 'FI' );
$pdf->Output ($path.'/'.$network_report_name.'.pdf', 'I' );
//$pdf->Output (__DIR__."\\". $network_report_name.'.pdf', 'F' );

// ============================================================+
// END OF FILE
// ============================================================+

?>