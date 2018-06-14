<?php
//echo $stateSilver,'sdfsd';die;
define('DS', DIRECTORY_SEPARATOR);
define('ROOT1', dirname(__FILE__).DS);
include '..'.DS.'config'.DS.'config.php';
include ROOT1 . 'pData.class.php';
include ROOT1 . 'pDraw.class.php';
include ROOT1 . 'pImage.class.php';
include ROOT1 . 'pIndicator.class.php';

 
 /* Create the pChart object */
 $myPicture = new pImage(500,400);

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 //$myPicture->drawFilledRectangle(0,0,700,230,$Settings);

 /* Overlay with a gradient */
 
 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,499,399,array("R"=>48,"G"=>122,"B"=>206));

 
 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../public/fonts/HelveticaLTStd-LightCond.ttf","FontSize"=>10));
 //$myPicture->drawText(10,13,"drawIndicator() - Create nice looking indicators",array("R"=>255,"G"=>255,"B"=>255));

 /* boxes color */
 //$RectangleSettings = array("R"=>209,"G"=>198,"B"=>27,"Alpha"=>100,"Surrounding"=>10);
$myPicture->drawFilledRectangle(170,10,180,20,array("R"=>48,"G"=>122,"B"=>206,"Alpha"=>100,"Surrounding"=>30));
$myPicture->drawText(182,23,"Always",array("R"=>0,"G"=>0,"B"=>0));
 $myPicture->drawFilledRectangle(250,10,260,20,array("R"=>94,"G"=>153,"B"=>0,"Alpha"=>100,"Surrounding"=>30));
 $myPicture->drawText(262,23,"Mostly",array("R"=>0,"G"=>0,"B"=>0));
 $myPicture->drawFilledRectangle(320,10,330,20,array("R"=>208,"G"=>177,"B"=>34,"Alpha"=>100,"Surrounding"=>30));
 $myPicture->drawText(332,21,"Sometimes",array("R"=>0,"G"=>0,"B"=>0));
$myPicture->drawFilledRectangle(400,10,410,20,array("R"=>209,"G"=>34,"B"=>0,"Alpha"=>100,"Surrounding"=>30));
$myPicture->drawText(412,22,"Rarely",array("R"=>0,"G"=>0,"B"=>0));
 /* Create the pIndicator object */ 
 $Indicator = new pIndicator($myPicture);

 $myPicture->setFontProperties(array("FontName"=>"../public/fonts/HelveticaLTStd-LightCond.ttf","FontSize"=>12));

 //print_r($_GET);die;
 
 /* Define the indicator sections */
 $x=170;
 $offset=0;
 $CaptionPosition = INDICATOR_CAPTION_BOTTOM;
 $CaptionLayout = INDICATOR_CAPTION_EXTENDED;
 $request = $_GET;
 
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
 
 	$IndicatorSections   = "";

 	/*
 	$Outstanding>0?($IndicatorSections[] = array("Start"=>0,"End"=>$Outstanding,"Caption"=>$Outstanding,"R"=>48,"G"=>122,"B"=>206)):''; 	
 	$Variable>0?($IndicatorSections[] = array("Start"=>$Outstanding,"End"=>$Variable+$Outstanding,"Caption"=>$Variable,"R"=>94,"G"=>153,"B"=>0)):'';
 	$Good>0?($IndicatorSections[] = array("Start"=>$Variable+$Outstanding,"End"=>$Variable+$Outstanding+$Good,"Caption"=>$Good,"R"=>208,"G"=>177,"B"=>34)):'';
 	$NeedsAttention>0?($IndicatorSections[] = array("Start"=>$Variable+$Outstanding+$Good,"End"=>$Variable+$Outstanding+$NeedsAttention,"Caption"=>$NeedsAttention,"R"=>209,"G"=>34,"B"=>0)):'';
 	*/

 	$Outstanding>0?($IndicatorSections[] = array("Start"=>0,"End"=>$Outstanding,"Caption"=>$Outstanding,"R"=>48,"G"=>122,"B"=>206)):'';
 	$Good>0?($IndicatorSections[] = array("Start"=>$Outstanding,"End"=>$Outstanding+$Good,"Caption"=>$Good,"R"=>94,"G"=>153,"B"=>0)):'';
 	$Variable>0?($IndicatorSections[] = array("Start"=>$Outstanding+$Good,"End"=>$Outstanding+$Good+$Variable,"Caption"=>$Variable,"R"=>208,"G"=>177,"B"=>34)):''; 	
 	$NeedsAttention>0?($IndicatorSections[] = array("Start"=>$Variable+$Outstanding+$Good,"End"=>$Variable+$Outstanding+$Good+$NeedsAttention,"Caption"=>$NeedsAttention,"R"=>209,"G"=>34,"B"=>0)):'';
 	
 
 /* Draw the 2nd indicator */
 	$myPicture->drawText(0,50+$offset,$name,array("R"=>0,"G"=>0,"B"=>0));
 	//$myPicture->drawText(0,50+$offset,"kpa",array("R"=>0,"G"=>0,"B"=>0));
 $IndicatorSettings = array("SectionsMargin"=>0,"Values"=>array(-1),"CaptionPosition"=>$CaptionPosition,"ValueDisplay"=>INDICATOR_VALUE_BUBBLE,"CaptionLayout"=>$CaptionLayout ,"CaptionR"=>0,"CaptionG"=>0,"CaptionB"=>0,"DrawLeftHead"=>FALSE,"DrawRightHead"=>FALSE,"ValueDisplay"=>INDICATOR_VALUE_LABEL,"ValueFontName"=>"../public/fonts/Forgotte.ttf","ValueFontSize"=>15,"IndicatorSections"=>$IndicatorSections);
 $Indicator->draw($x,30+$offset,300,30,$IndicatorSettings);
 $offset +=50;
 }
 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawIndicator.png");
?>