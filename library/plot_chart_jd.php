<?php
//echo $stateSilver,'sdfsd';die;
define('DS', DIRECTORY_SEPARATOR);
define('ROOT1', dirname(__FILE__).DS);
include '..'.DS.'config'.DS.'config.php';
include 'pData.class.php';
include 'pDraw.class.php';
include 'pImage.class.php';
/*
function __autoload($className) {
	if (file_exists('..'.DS.'library' . DS . strtolower($className) . '.class.php')) 
		require_once('..'.DS.'library' . DS . strtolower($className) . '.class.php');
	else if (file_exists('..'.DS.'application' . DS . 'models' . DS . strtolower($className) . '.php'))
		require_once(ROOT1 . '..'.DS.'application' . DS . 'models' . DS . strtolower($className) . '.php');
	
}
$customreportModel = new customreportModel();
$jsDistance = $customreportModel->getJSdistance();*/
//print_r($jsDistance);
 /* Create and populate the pData object */
 $MyData = new pData();  
 
 
 $data = $_GET;
 $clients = array();
 $agreements = array();
 $disagree1 = array();
 $disagree2 = array();
 $disagree3 = array();
 $total=162;
 foreach($data as $row){
 	$r = explode(';',$row);
 	$clients[] = urldecode($r[0]);
 	$agreements[] = number_format(($r[1]*100)/$total,2);
 	$disagree1[] = number_format(($r[2]*100)/$total,2);
 	$disagree2[] = number_format(($r[3]*100)/$total,2);
 	$disagree3[] = number_format(($r[4]*100)/$total,2);
 }
 
$MyData->addPoints($agreements,"Agreements");
$MyData->addPoints($disagree1,"Disagreements by 1");
$MyData->addPoints($disagree2,"Disagreements by 2");
$MyData->addPoints($disagree3,"Disagreements by 3");
 
 $MyData->setAxisName(0,"Percentage");
 $MyData->addPoints($clients,"Labels");
 $MyData->setSerieDescription("Labels","Schools");
 $MyData->setAbscissa("Labels");
 /*$MyData->setSeriePicture("Agreements","../public/images/reports/leg_outstanding.png");
 $MyData->setSeriePicture("Disagreements by 1","../public/images/reports/leg_good.png");
 $MyData->setSeriePicture("Disagreements by 2","../public/images/reports/leg_variable.png");
 $MyData->setSeriePicture("Disagreements by 3","../public/images/reports/leg_attn.png");
 $MyData->setSerieWeight("Agreements",7);
 */
 $MyData->setPalette("Agreements",array("R"=>48,"G"=>122,"B"=>206,"Alpha"=>80));
 $MyData->setPalette("Disagreements by 1",array("R"=>94,"G"=>153,"B"=>0,"Alpha"=>80));
 $MyData->setPalette("Disagreements by 2",array("R"=>208,"G"=>177,"B"=>34,"Alpha"=>80));
 $MyData->setPalette("Disagreements by 3",array("R"=>209,"G"=>34,"B"=>0,"Alpha"=>80));
 $MyData->setSerieWeight("Agreements",11);
 $MyData->setSerieWeight("Disagreements by 1",8);
 $MyData->setSerieWeight("Disagreements by 2",5);
 $MyData->setSerieWeight("Disagreements by 3",2);

 /* Create the pChart object */
 $myPicture = new pImage(700,600,$MyData);

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Draw the background */
 $Settings = array("R"=>255, "G"=>255, "B"=>255);
 $myPicture->drawFilledRectangle(0,0,700,600,$Settings);

 /* Overlay with a gradient */
 
 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,599,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"../public/fonts/HelveticaLTStd-LightCond.ttf","FontSize"=>8,"R"=>0,"G"=>0,"B"=>0));

 /* Define the chart area */
 $myPicture->setGraphArea(180,40,650,580);

 /* Draw the scale */
 $scaleSettings = array("Pos"=>SCALE_POS_TOPBOTTOM,"DrawSubTicks"=>TRUE,"GridR"=>200,"GridG"=>0,"GridB"=>0,"Floating"=>TRUE,"Mode"=>SCALE_MODE_START0);
 //$scaleSettings = array("XMargin"=>10,"YMargin"=>5,"Floating"=>TRUE,"GridR"=>200,"GridG"=>0,"GridB"=>0,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);

 /* Turn on Antialiasing */
 $myPicture->Antialias = TRUE;

 /* Enable shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw the line chart */
 //$myPicture->drawLineChart();
 //$myPicture->drawPlotChart(array("DisplayValues"=>TRUE,"PlotBorder"=>TRUE,"BorderSize"=>2,"Surrounding"=>-60,"BorderAlpha"=>80));
 $myPicture->drawPlotChart(array("DisplayValues"=>FALSE,"DisplayColor"=>DISPLAY_MANUAL));

 /* Write the chart legend */
 $myPicture->drawLegend(140,585,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>0,"FontG"=>0,"FontB"=>0,"R"=>255,"G"=>0,"B"=>0));
 //$myPicture->drawLegend(140,310,array("BoxSize"=>4,"R"=>173,"G"=>163,"B"=>83,"Surrounding"=>20,"Family"=>LEGEND_FAMILY_CIRCLE));
 

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawLineChart.plots.png");