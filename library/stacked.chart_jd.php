<?php
 /* CAT:Stacked chart */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT1', dirname(__FILE__).DS);
include '..'.DS.'config'.DS.'config.php';
include ROOT1 . 'pData.class.php';
include ROOT1 . 'pDraw.class.php';
include ROOT1 . 'pImage.class.php';

 /* Create and populate the pData object */
$MyData = new pData();  
function multiCol($string, $numcols)
{
	$collength = ceil(strlen($string) / $numcols) + 3;
	$retval = explode("\n", wordwrap(strrev($string), $collength));
	if(count($retval) > $numcols) {
		$retval[$numcols-1] .= " " . $retval[$numcols];
		unset($retval[$numcols]);
	}
	$retval = array_map("strrev", $retval);
	return array_reverse($retval);
}
 
 $data = $_REQUEST;
 //print_r($data);
 $clients = array();
 $agreements = array();
 $disagree1 = array();
 $disagree2 = array();
 $disagree3 = array();
 $kpasData = array();
 $sortKpaData = array();
 $total=162;
 $lang_id=(isset($data['lang_id']) && $data['lang_id']!="")?$data['lang_id']:DEFAULT_LANGUAGE;
 unset($data['lang_id']);
 $jd = 0;
 if (isset($data['jd']) && $data['jd'] == 1) {
    // echo "<pre>";print_r($data);die;
    $jd = 1;
    unset($data['jd']);
   

    foreach ($data as $row) {
        $r = explode(';', $row);
        $kpasData[$r[0]] = array_slice($r, 1);
        //  $kpasData[$r[0]] = $r;
    }

//usort($kpasData, 'compareMid');
    array_multisort($kpasData, SORT_DESC);
    foreach ($kpasData as $key => $val) {
        //$val[] = $key;
        array_unshift($val, $key);
        $sortKpaData[$key] = $val;
    }

    if (isset($sortKpaData) && count($sortKpaData) >= 1) {
        $data = $sortKpaData;
    }
//}
//echo "<pre>";print_r($data);die;
foreach($data as $r){
 	//$r = explode(';',$row); 
 	//$scl = str_replace('  ',' ',urldecode($r[0]));
 	$scl = urldecode($r[0]);
 //	echo "<pre>",$scl,"</pre>";
 	$scl = preg_replace('/\s+/',' ',trim($scl));  	
 	$break = 29; 
        //echo array_sum(array_slice($r, 1));die;
 	//$scl = explode(PHP_EOL,wordwrap($scl,$break,PHP_EOL));	
 	//$scl = implode(PHP_EOL.'-', str_split($scl, $break)); 	
 	//echo "<pre>",sprintf("%-{$break}s",$scl),strlen($scl),"</pre>";
 	//$clients[] = sprintf("%-{$break}s",$scl);
 	//$clients[] = str_pad($scl[0],$break,' ',STR_PAD_RIGHT).(!empty($scl[1])?str_pad($scl[1],$break,' ',STR_PAD_RIGHT):'');
 	//$clients[] = str_pad($scl,$break,' ',STR_PAD_RIGHT);
        if($lang_id==8){
        $array = explode(PHP_EOL,wordwrap($scl));    
        }else{
 	$array = explode(PHP_EOL,wordwrap($scl,$break,PHP_EOL));
        }
        //print_r($array);
        if($lang_id==8){
           
        }else{
 	array_walk($array,function(&$val) use($break){
 		$val = str_pad($val,$break,' ',STR_PAD_RIGHT); 		
 	});
        }
 	$clients[] = $array[0].(!empty($array[1])?PHP_EOL.$array[1]:'').(!empty($array[2])?PHP_EOL.$array[2]:'');
 	//echo $r[1];
        
 	$agreements[] = @number_format(($r[1]*100)/ array_sum(array_slice($r, 1)),1);
 	$disagree1[] = @number_format(($r[2]*100)/array_sum(array_slice($r, 1)),1);
 	$disagree2[] = @number_format(($r[3]*100)/array_sum(array_slice($r, 1)),1);
 	$disagree3[] = @number_format(($r[4]*100)/array_sum(array_slice($r, 1)),1); 	
 }
 }else {
     foreach($data as $row){
 	$r = explode(';',$row); 
 	//$scl = str_replace('  ',' ',urldecode($r[0]));
 	$scl = urldecode($r[0]);
 //	echo "<pre>",$scl,"</pre>";
 	$scl = preg_replace('/\s+/',' ',trim($scl));  	
 	$break = 29; 
 	//$scl = explode(PHP_EOL,wordwrap($scl,$break,PHP_EOL));	
 	//$scl = implode(PHP_EOL.'-', str_split($scl, $break)); 	
 	//echo "<pre>",sprintf("%-{$break}s",$scl),strlen($scl),"</pre>";
 	//$clients[] = sprintf("%-{$break}s",$scl);
 	//$clients[] = str_pad($scl[0],$break,' ',STR_PAD_RIGHT).(!empty($scl[1])?str_pad($scl[1],$break,' ',STR_PAD_RIGHT):'');
 	//$clients[] = str_pad($scl,$break,' ',STR_PAD_RIGHT);
        if($lang_id==8){
        $array = explode(PHP_EOL,wordwrap($scl));    
        }else{
 	$array = explode(PHP_EOL,wordwrap($scl,$break,PHP_EOL));
        }
        if($lang_id==8){
            
        }else{
 	array_walk($array,function(&$val) use($break){
 		$val = str_pad($val,$break,' ',STR_PAD_RIGHT); 		
 	});
        }
 	//$clients[] = $array[0].(!empty($array[1])?PHP_EOL.$array[1]:'');
        $clients[] = $array[0].(!empty($array[1])?PHP_EOL.$array[1]:'').(!empty($array[2])?PHP_EOL.$array[2]:'');
 	//echo $r[1];
 	$agreements[] = number_format(($r[1]*100)/$total,1);
 	$disagree1[] = number_format(($r[2]*100)/$total,1);
 	$disagree2[] = number_format(($r[3]*100)/$total,1);
 	$disagree3[] = number_format(($r[4]*100)/$total,1); 	
 }
 }
 //die();
$MyData->addPoints($agreements,"Agreements");
$MyData->addPoints($disagree1,"Disagreements by 1");
$MyData->addPoints($disagree2,"Disagreements by 2");
$MyData->addPoints($disagree3,"Disagreements by 3");
if($lang_id==8){
$MyData->setAxisName(0,"प्रतिशत");    
}else{
$MyData->setAxisName(0,"Percentage");     
}

 
 $MyData->addPoints($clients,"Labels");
 $MyData->setSerieDescription("Labels","Schools");
 $MyData->setAbscissa("Labels");
 /*$MyData->setSeriePicture("Agreements","../public/images/reports/leg_outstanding.png");
 $MyData->setSeriePicture("Disagreements by 1","../public/images/reports/leg_good.png");
 $MyData->setSeriePicture("Disagreements by 2","../public/images/reports/leg_variable.png");
 $MyData->setSeriePicture("Disagreements by 3","../public/images/reports/leg_attn.png");
 $MyData->setSerieWeight("Agreements",7);
 */
 /*if($jd == 1) {
     
        $MyData->setPalette("Agreements",array("R"=>46,"G"=>92,"B"=>230,"Alpha"=>100));
        $MyData->setPalette("Disagreements by 1",array("R"=>71,"G"=>178,"B"=>71,"Alpha"=>100));
        $MyData->setPalette("Disagreements by 2",array("R"=>231,"G"=>191,"B"=>25,"Alpha"=>100));
        $MyData->setPalette("Disagreements by 3",array("R"=>255,"G"=>0,"B"=>0,"Alpha"=>100));
 }else {*/
        $MyData->setPalette("Agreements",array("R"=>195,"G"=>203,"B"=>113,"Alpha"=>100));
        $MyData->setPalette("Disagreements by 1",array("R"=>174,"G"=>90,"B"=>65,"Alpha"=>100));
        $MyData->setPalette("Disagreements by 2",array("R"=>128,"G"=>128,"B"=>128,"Alpha"=>100));
        $MyData->setPalette("Disagreements by 3",array("R"=>194,"G"=>194,"B"=>163,"Alpha"=>100));
 //}
 $MyData->setSerieWeight("Agreements",11);
 $MyData->setSerieWeight("Disagreements by 1",8);
 $MyData->setSerieWeight("Disagreements by 2",5);
 $MyData->setSerieWeight("Disagreements by 3",2);

 $num_clients = count($clients);
 $addHeight = 0;
 if($num_clients>10)
	 $addHeight = $num_clients*20;
 /* Normalize all the data series to 100% */
 $MyData->normalize(100,"");

 /* Create the pChart object */
 $myPicture = new pImage(770,470+$addHeight,$MyData);
 
 /* Set the default font properties */
 //$myPicture->setFontProperties(array("FontName"=>"../public/fonts/calibrib-webfont.ttf","FontSize"=>8));
 if($lang_id==8){
 $myPicture->setFontProperties(array("FontName"=>"../public/fonts/MANGAL.TTF","FontSize"=>8));
 }else{
 $myPicture->setFontProperties(array("FontName"=>"../public/fonts/calibrib-webfont.ttf","FontSize"=>8));    
 }

 /* Draw the scale and the chart */
 $myPicture->setGraphArea(190,38,750,440+$addHeight);
 $myPicture->drawRectangle(190,38,750,440+$addHeight,array("R"=>0,"G"=>0,"B"=>0));

 $myPicture->drawScale(array("Floating"=>TRUE,"GridR"=>200,"GridG"=>0,"GridB"=>0,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE,"Pos"=>SCALE_POS_TOPBOTTOM,"Mode"=>SCALE_MODE_MANUAL,"ManualScale"=>array(0=>array("Min"=>0,"Max"=>100))));
 //$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 $myPicture->drawStackedBarChart(array("DisplayOrientation"=>ORIENTATION_HORIZONTAL,"DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO,"Gradient"=>FALSE,"GradientMode"=>GRADIENT_EFFECT_CAN));
 $myPicture->setShadow(FALSE);


 $myPicture->setFontProperties(array("FontName"=>"../public/fonts/calibrib-webfont.ttf","FontSize"=>8));    
 
 /* Write the chart legend */
 $myPicture->drawLegend(190,450+$addHeight,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawStackedBarChart.can.png");
?>