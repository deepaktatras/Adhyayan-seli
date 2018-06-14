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
 
 $data = $_GET;
 $clients = array();
 $agreements = array();
 $disagree1 = array();
 $disagree2 = array();
 $disagree3 = array();
 $total=0;
 $legend = 1;
 foreach($data as $row){
 	$r = explode(';',$row);  	
 	$scl = urldecode($r[0]);
 	if(preg_match('/^sub question/i',$scl))
 		$legend = 2;
 	$scl = preg_replace('/\s+/',' ',trim($scl));  	
 	$break = 20;  	
 	$array = explode(PHP_EOL,wordwrap($scl,$break,PHP_EOL));
 	array_walk($array,function(&$val) use($break){
 		$val = str_pad($val,$break,' ',STR_PAD_RIGHT); 		
 	});
 	$labels[] = $array[0].(!empty($array[1])?PHP_EOL.$array[1]:'');
 	$total = $r[1]+$r[2]+$r[3]+$r[4];
 	$agreements[] = number_format(($r[1]*100)/$total,1);
 	$disagree1[] = number_format(($r[2]*100)/$total,1);
 	$disagree2[] = number_format(($r[3]*100)/$total,1);
 	$disagree3[] = number_format(($r[4]*100)/$total,1); 	
 };
 if($legend==1){
	$MyData->addPoints($agreements,"Agreements");
	$MyData->addPoints($disagree1,"Disagreements by 1");
	$MyData->addPoints($disagree2,"Disagreements by 2");
	$MyData->addPoints($disagree3,"Disagreements by 3");
 }
 elseif($legend==2){
 	$MyData->addPoints($agreements,"JD=0(perfect match)");
 	$MyData->addPoints($disagree1,"JD=1(difference of one level)");
 	$MyData->addPoints($disagree2,"JD=2(difference of two level)");
 	$MyData->addPoints($disagree3,"JD=3(difference of three level)");
 }
 $MyData->setAxisName(0,"Percentage");
 $MyData->addPoints($labels,"Labels");
 $MyData->setSerieDescription("Labels","Schools");
 $MyData->setAbscissa("Labels");

 if($legend==1){
	 $MyData->setPalette("Agreements",array("R"=>195,"G"=>203,"B"=>113,"Alpha"=>100));
	 $MyData->setPalette("Disagreements by 1",array("R"=>174,"G"=>90,"B"=>65,"Alpha"=>100));
	 $MyData->setPalette("Disagreements by 2",array("R"=>128,"G"=>128,"B"=>128,"Alpha"=>100));
	 $MyData->setPalette("Disagreements by 3",array("R"=>194,"G"=>194,"B"=>163,"Alpha"=>100));
	 $MyData->setSerieWeight("Agreements",11);
	 $MyData->setSerieWeight("Disagreements by 1",8);
	 $MyData->setSerieWeight("Disagreements by 2",5);
	 $MyData->setSerieWeight("Disagreements by 3",2);
 }
 elseif($legend==2){
 		$MyData->setPalette("JD=0(perfect match)",array("R"=>195,"G"=>203,"B"=>113,"Alpha"=>100));
 		$MyData->setPalette("JD=1(difference of one level)",array("R"=>174,"G"=>90,"B"=>65,"Alpha"=>100));
 		$MyData->setPalette("JD=2(difference of two level)",array("R"=>128,"G"=>128,"B"=>128,"Alpha"=>100));
 		$MyData->setPalette("JD=3(difference of three level)",array("R"=>194,"G"=>194,"B"=>163,"Alpha"=>100));
 		$MyData->setSerieWeight("JD=0(perfect match)",11);
 		$MyData->setSerieWeight("JD=1(difference of one level)",8);
 		$MyData->setSerieWeight("JD=2(difference of two level)",5);
 		$MyData->setSerieWeight("JD=3(difference of three level)",2);
 	
 }
 $num_clients = count($data);
 $addHeight = 0;
 if($num_clients>10)
	 $addHeight = $num_clients*15;
 /* Normalize all the data series to 100% */
 $MyData->normalize(100,"");

 /* Create the pChart object */
 $myPicture = new pImage(770,520+$addHeight,$MyData);
 
 /* Set the default font properties */
 $myPicture->setFontProperties(array("FontName"=>"../public/fonts/DejaVuSansMono.ttf","FontSize"=>8));

 /* Draw the scale and the chart */
 $myPicture->setGraphArea(190,38,750,440+$addHeight);
 $myPicture->drawRectangle(190,38,750,440+$addHeight,array("R"=>0,"G"=>0,"B"=>0));

 $myPicture->drawScale(array("Floating"=>TRUE,"GridR"=>200,"GridG"=>0,"GridB"=>0,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE,"Pos"=>SCALE_POS_TOPBOTTOM,"Mode"=>SCALE_MODE_MANUAL,"ManualScale"=>array(0=>array("Min"=>0,"Max"=>100))));
 //$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 $myPicture->drawStackedBarChart(array("DisplayOrientation"=>ORIENTATION_HORIZONTAL,"DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO,"Gradient"=>FALSE,"GradientMode"=>GRADIENT_EFFECT_CAN));
 $myPicture->setShadow(FALSE);

 /* Write the chart legend */
 $myPicture->drawLegend(190,450+$addHeight,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_VERTICAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawStackedBarChart.can.png");
?>