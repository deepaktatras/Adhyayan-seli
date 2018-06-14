<?php
//echo $stateSilver,'sdfsd';die;
define('DS', DIRECTORY_SEPARATOR);
define('ROOT1', dirname(__FILE__).DS);
include '..'.DS.'config'.DS.'config.php';
include ROOT1 . 'pData.class.php';
include ROOT1 . 'pDraw.class.php';
include ROOT1 . 'pImage.class.php';

 /* Create and populate the pData object */
 $MyData = new pData();  
 
 
 $request = $_GET;
 $kpas = array();
 $outstandingPoints = array();
 $goodPoints = array();
 $variablePoints = array();
 $needsAttentionPoints = array();
 $sameRating = array();
 foreach($request as $r){
 	$Variable = "0";
 	$Good = "0";
 	$Outstanding = "0";
 	$NeedsAttention = "0";
 	$values = explode(';',$r);
 	$name = substr($values[0],strpos($values[0],'_')+1);
 	$num = count($values);
 	for($i=1;$i<$num;$i++){
 		//$var_name = str_replace(" ","_",substr($values[$i],0,strpos($values[$i],'_')));
 		$var_name = str_replace(" ","_",substr($values[$i],0,strpos($values[$i],'_')));
 			
 		$$var_name = number_format(substr($values[$i],strpos($values[$i],'_')+1),2);
 		
 	}
 	$kpas[] = $name;
 	array_push($outstandingPoints, $Outstanding>0?$Outstanding:VOID);
	array_push($goodPoints, $Good>0?$Good:VOID);
 	array_push($variablePoints, $Variable>0?$Variable:VOID);
 	array_push($needsAttentionPoints, $NeedsAttention>0?$NeedsAttention:VOID);
 }
 
 
 $MyData->addPoints($outstandingPoints,"Outstanding");
 $MyData->addPoints($goodPoints,"Good");
 $MyData->addPoints($variablePoints,"Variable");
 $MyData->addPoints($needsAttentionPoints,"Needs Attention");
 
//  $MyData->setSerieShape("Outstanding",SERIE_SHAPE_FILLEDTRIANGLE);
//  $MyData->setSerieShape("Good",SERIE_SHAPE_FILLEDSQUARE);
//  $MyData->setSerieShape("Variable",SERIE_SHAPE_FILLEDTRIANGLE);
//  $MyData->setSerieShape("Needs Attention",SERIE_SHAPE_FILLEDSQUARE);

 $MyData->setAxisName(0,"Percentage");
 $MyData->addPoints($kpas,"Labels");
 $MyData->setSerieDescription("Labels","KPAs");
 $MyData->setAbscissa("Labels");
 
 $MyData->setPalette("Outstanding",array("R"=>48,"G"=>122,"B"=>206,"Alpha"=>80));
 $MyData->setPalette("Good",array("R"=>94,"G"=>153,"B"=>0,"Alpha"=>80));
 $MyData->setPalette("Variable",array("R"=>208,"G"=>177,"B"=>34,"Alpha"=>80));
 $MyData->setPalette("Needs Attention",array("R"=>209,"G"=>34,"B"=>0,"Alpha"=>80));
 $MyData->setSerieWeight("Outstanding",11);
 $MyData->setSerieWeight("Good",8);
 $MyData->setSerieWeight("Variable",5);
 $MyData->setSerieWeight("Needs Attention",2);
 
 /* Create the pChart object */
 $myPicture = new pImage(770,330,$MyData);

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Draw the background */
 $Settings = array("R"=>255, "G"=>255, "B"=>255);
 $myPicture->drawFilledRectangle(0,0,770,330,$Settings);

 /* Overlay with a gradient */
 
 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,769,329,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"../public/fonts/HelveticaLTStd-LightCond.ttf","FontSize"=>8,"R"=>0,"G"=>0,"B"=>0));

 /* Define the chart area */
 $myPicture->setGraphArea(40,40,769,300);

 /* Draw the scale */
 $scaleSettings = array("Floating"=>TRUE,"GridR"=>200,"GridG"=>0,"GridB"=>0,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE,"Mode"=>SCALE_MODE_START0);
 $myPicture->drawScale($scaleSettings);

 /* Turn on Antialiasing */
 $myPicture->Antialias = TRUE;

 /* Enable shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw the line chart */
 //$myPicture->drawLineChart();
 //$myPicture->drawPlotChart(array("DisplayValues"=>TRUE,"PlotBorder"=>TRUE,"BorderSize"=>2,"Surrounding"=>-60,"BorderAlpha"=>80));
 $myPicture->drawPlotChart(array("DisplayValues"=>FALSE,"DisplayColor"=>DISPLAY_MANUAL));
 //$myPicture->drawPlotChart(array("PlotSize"=>1,"PlotBorder"=>TRUE,"BorderSize"=>1));

 /* Write the chart legend */
 $myPicture->drawLegend(190,9,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>0,"FontG"=>0,"FontB"=>0));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawLineChart.plots.png");