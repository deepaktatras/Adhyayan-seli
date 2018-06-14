<?php
//error_reporting(0);
//echo $stateSilver,'sdfsd';die;
define('DS', DIRECTORY_SEPARATOR);
define('ROOT1', dirname(__FILE__).DS);
include '..'.DS.'config'.DS.'config.php';
include ROOT1 . 'pData.class.php';
include ROOT1 . 'pDraw.class.php';
include ROOT1 . 'pImage.class.php';

$MyData = new pData();
$labels = array();
//print_r($_GET);
$data = $_GET;
$j=0;
$count = 0;
$awardNames = array();
$orderAwards = array('c'=>1,'b'=>2,'b+'=>3,'b++'=>4,'a'=>5,'a+'=>6,'a++'=>7,'o'=>8,'o+'=>9,'o++'=>10);
$allAwards = array('c'=>"C",'b'=>"B",'b+'=>"B+",'b++'=>"B++",'a'=>"A",'a+'=>"A+",'a++'=>"A++",'o'=>"O",'o+'=>"O+",'o++'=>"O++");
foreach($data as $k=>$r){	
	$values = explode(';',$r);
	$name = substr($values[0],strpos($values[0],'_')+1);
	$num = count($values);//echo $num;
	for($i=1;$i<$num;$i++){
		//$var_name = str_replace(" ","_",substr($values[$i],0,strpos($values[$i],'_')));
		$var_name = str_replace("","",substr($values[$i],0,strpos($values[$i],'_')));
		$val=0;
		$val = substr($values[$i],strpos($values[$i],'_')+1);
		$count=$val>$count?$val:$count;
		//$labels[$j][$var_name] = $val;
		$val>0?($labels[$name][$var_name] = $val):'';
		$val>0?($awardNames[$var_name] =$allAwards[$var_name]):'';
	}
	//$break = 55;
	//$tempName =str_split($name,$break);
	//$name = $tempName[0].(!empty($tempName[1])?PHP_EOL.'-'.$tempName[1]:'');
	//$labels[$name]['name'] = $name;	
}
//print_r($awardNames);
$awardKeys = array_unique(array_keys($awardNames));
$awardNames = array_values(array_unique($awardNames));
$numofawards = count($awardNames);
//print_r($awardNames);print_r($awardKeys);
//print_r(array_intersect($orderAwards,array_flip($awardKeys)));
$awardKeysFinal = array_flip(array_intersect_ukey($orderAwards, array_flip($awardKeys), 'key_compare_func'));
$awardNamesFinal = array();
//awardnames in order
//print_r($awardKeysFinal);
foreach($awardKeysFinal as $a)
	$awardNamesFinal[$a] = $allAwards[$a];
//print_r($awardNamesFinal);die;

function key_compare_func($key1, $key2)
{
	if ($key1 == $key2)
		return 0;
		else if ($key1 > $key2)
			return 1;
			else
				return -1;
}

//for all the labels push data of awards in array
$numofStates = 0;
 foreach($labels as $key=>$label){	
 	$numofStates++;
	$name = $key;	
	$labelKeys = array();
	foreach($awardKeysFinal as $k=>$v)
		$labelKeys[$v] = isset($label[$v])?$label[$v]:0;	
	$MyData->addPoints($labelKeys, ucwords($name));
} 
//print_r($awardNamesFinal);print_r($awardNames);die;
$MyData->setAxisName(0, "Number of Schools");
$MyData->addPoints($awardNamesFinal, "Labels");
$MyData->setSerieDescription("Labels", "Awards");
$MyData->setAbscissa("Labels");

//print_r($MyData->getData());die;
/* Create the pChart object */
$myPicture = new pImage(700, 250, $MyData);

/* Draw the background */
$Settings = array("R" => 255, "G" => 255, "B" => 255);
//$myPicture->drawFilledRectangle(0, 0, 400+($numofawards*20), 250, $Settings);
//$myPicture->drawRectangle(0,0,400+($numofawards*20),249,array("R"=>0,"G"=>0,"B"=>0));

/* Write the chart title */
$myPicture->setFontProperties(array("FontName" => "../public/fonts/HelveticaLTStd-LightCond.ttf", "FontSize" => 9));
$myPicture->drawText(320, 35, "Distribution of schools on the awards rubric", array("FontSize" => 15, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));

/* Draw the scale and the chart */
$myPicture->setGraphArea(30, 50, 150+($numofawards*$numofStates*20), 190);

$myPicture->drawScale(array("DrawSubTicks" => true, "Mode" => SCALE_MODE_MANUAL, "ManualScale" => array(0 => array("Min" => 0, "Max" => $count)),
    'Factors' => array(ceil($count/5))));
$myPicture->setShadow(TRUE, array("X" => 0, "Y" => 0, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));
$myPicture->setFontProperties(array("FontName" => "../public/fonts/HelveticaLTStd-LightCond.ttf", "FontSize" => 10, 'Alpha' => 250));
$myPicture->drawBarChart(array("DisplayValues" => TRUE, "DisplayColor" => DISPLAY_MANUAL, "Rounded" => FALSE, "Surrounding" => 50, 'Interleave' => 1));
//$myPicture->setShadow(FALSE);

/* Write the chart legend */
$myPicture->drawLegend(10, 225, array("Style" => LEGEND_NOBORDER, "Mode" => LEGEND_HORIZONTAL));

/* Render the picture (choose the best way) */
$myPicture->autoOutput("example.drawBarChart.png");

?>