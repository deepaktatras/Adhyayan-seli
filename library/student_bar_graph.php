<?php
//echo $stateSilver,'sdfsd';die;
define('DS', DIRECTORY_SEPARATOR);
define('ROOT1', dirname(__FILE__).DS);
include '..'.DS.'config'.DS.'config.php';
include ROOT1 . 'pData.class.php';
include ROOT1 . 'pDraw.class.php';
include ROOT1 . 'pImage.class.php';

$MyData = new pData();

//print_r($_GET);
$data = $_GET;
$awardNamesFinal=array();
$round1_array=array();
$round2_array=array();
foreach($data as $key=>$val){
if($key!="head_show" && $key!="type"){
$awardNamesFinal[]= $key;
$round_values= explode("~", $val);
$round1_array[$key]=$round_values[0];
$round2_array[$key]=$round_values[1];
}
}
$heading=isset($_GET['head_show'])?$_GET['head_show']:'';
$type=isset($_GET['type'])?$_GET['type']:'';
$j=0;
$count = 0;
$awardNamesFinal=array("Exceptional","Proficient","Developing","Emerging","Foundation"); 

$MyData->addPoints($round1_array,"Round1");  
$MyData->addPoints($round2_array,"Round2");  

$MyData->setAxisName(0, "Percentage (%)");
$MyData->addPoints($awardNamesFinal, "Labels");
$MyData->setSerieDescription("Labels", "Awards");
$MyData->setAbscissa("Labels");


//print_r($MyData->getData());die;
/* Create the pChart object */
if($type=="SQ"){
 $myPicture = new pImage(350, 170, $MyData);
     
}else if($type=="SQ1"){
  $myPicture = new pImage(600, 200, $MyData);  
}else{
$myPicture = new pImage(700, 265, $MyData);
}

$serieSettings = array("R"=>0,"G"=>0,"B"=>255,"Alpha"=>80);

$MyData->setPalette("Round1",$serieSettings);


$serieSettings = array("R"=>255,"G"=>128,"B"=>0,"Alpha"=>80);

$MyData->setPalette("Round2",$serieSettings);

/* Draw the background */
$Settings = array("R" => 255, "G" => 255, "B" => 255);
//$myPicture->drawFilledRectangle(0, 0, 400+($numofawards*20), 250, $Settings);
//$myPicture->drawRectangle(0,0,400+($numofawards*20),249,array("R"=>0,"G"=>0,"B"=>0));

/* Write the chart title */
$myPicture->setFontProperties(array("FontName" => "../public/fonts/HelveticaLTStd-LightCond.ttf", "FontSize" => 10));
//$myPicture->drawText(320, 35, $heading, array("FontSize" => 17, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));

/* Draw the scale and the chart */
if($type=="SQ"){
  $myPicture->setGraphArea(50, 15, 350, 120);
  
}else if($type=="SQ1"){
  $myPicture->setGraphArea(50, 20, 600, 150);  
}else{
 $myPicture->setGraphArea(50, 20, 150+550, 205);
}

$myPicture->drawScale(array("DrawSubTicks" => true, "Mode" => SCALE_MODE_MANUAL, "ManualScale" => array(0 => array("Min" => 0, "Max" => 100)),
    'Factors' => array(ceil(100/5))));

$myPicture->setShadow(TRUE, array("X" => 0, "Y" => 0, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));
$myPicture->setFontProperties(array("FontName" => "../public/fonts/HelveticaLTStd-LightCond.ttf", "FontSize" => 10, 'Alpha' => 250));
$myPicture->drawBarChart(array("DisplayValues" => TRUE, "DisplayColor" => DISPLAY_MANUAL, "Rounded" => FALSE, "Surrounding" => 50, 'Interleave' => 1));
//$myPicture->setShadow(FALSE);

/* Write the chart legend */
if($type=="SQ"){
$myPicture->drawLegend(125, 145, array("Style" => LEGEND_NOBORDER, "Mode" => LEGEND_HORIZONTAL));
}else if($type=="SQ1"){
$myPicture->drawLegend(210, 180, array("Style" => LEGEND_NOBORDER, "Mode" => LEGEND_HORIZONTAL));     
}else{
$myPicture->drawLegend(260, 245, array("Style" => LEGEND_NOBORDER, "Mode" => LEGEND_HORIZONTAL));
}

/* Render the picture (choose the best way) */
$myPicture->autoOutput("example.drawBarChart.png");

?>