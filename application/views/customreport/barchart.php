<?php
//echo $stateSilver,'sdfsd';die;
include ROOT . 'library\pData.class.php';
include ROOT . 'library\pDraw.class.php';
include ROOT . 'library\pImage.class.php';

$MyData = new pData();
$statePlatinum = isset($_GET['sp'])?$_GET['sp']:0;
$stateGold = isset($_GET['sg'])?$_GET['sg']:0;
$stateSilver = isset($_GET['ss'])?$_GET['ss']:0;
$stateBronze = isset($_GET['sb'])?$_GET['sb']:0;
$nationalPlatinum = isset($_GET['np'])?$_GET['np']:0;
$nationalGold = isset($_GET['ng'])?$_GET['ng']:0;
$nationalSilver = isset($_GET['ns'])?$_GET['ns']:0;
$nationalBronze = isset($_GET['nb'])?$_GET['nb']:0;
$internationalPlatinum = isset($_GET['ip'])?$_GET['ip']:0;
$internationalGold = isset($_GET['ig'])?$_GET['ig']:0;
$internationalSilver = isset($_GET['is'])?$_GET['is']:0;
$internationalBronze = isset($_GET['ib'])?$_GET['ib']:0;;

$MyData->addPoints(array($statePlatinum, $nationalPlatinum, $internationalPlatinum), "Platinum");
$MyData->addPoints(array($stateGold, $nationalGold, $internationalGold), "Gold");
$MyData->addPoints(array($stateSilver, $nationalSilver, $internationalSilver), "Silver");
$MyData->addPoints(array($stateBronze, $nationalBronze, $internationalBronze), "Bronze");
$MyData->setAxisName(0, "Number of Schools");
$MyData->addPoints(array("State",'National','International'), "Labels");
$MyData->setSerieDescription("Labels", "Awards");
$MyData->setAbscissa("Labels");

/* Create the pChart object */
$myPicture = new pImage(700, 230, $MyData);

/* Draw the background */
$Settings = array("R" => 255, "G" => 255, "B" => 255);
$myPicture->drawFilledRectangle(0, 0, 700, 230, $Settings);

/* Write the chart title */
$myPicture->setFontProperties(array("FontName" => ROOT."/public/fonts/HelveticaLTStd-LightCond.ttf", "FontSize" => 11));
$myPicture->drawText(300, 55, "Distribution of schools on the awards rubric", array("FontSize" => 20, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));

/* Draw the scale and the chart */
$myPicture->setGraphArea(60, 80, 600, 190);

$myPicture->drawScale(array("DrawSubTicks" => true, "Mode" => SCALE_MODE_MANUAL, "ManualScale" => array(0 => array("Min" => 0, "Max" => $_GET['count'])),
    'Factors' => array(ceil($_GET['count']/5)), 'InnerTickWidth' => 540, 'OuterTickWidth' => 0, 'DrawYLines' => FALSE, 'TickAlpha' => 5));
$myPicture->setShadow(TRUE, array("X" => 0, "Y" => 0, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));
$myPicture->setFontProperties(array("FontName" => ROOT."/public/fonts/HelveticaLTStd-LightCond.ttf", "FontSize" => 11, 'Alpha' => 250));
$myPicture->drawBarChart(array("DisplayValues" => TRUE, "DisplayColor" => DISPLAY_AUTO, "Rounded" => FALSE, "Surrounding" => 50, 'Interleave' => 0));
//$myPicture->setShadow(FALSE);

/* Write the chart legend */
$myPicture->drawLegend(620, 80, array("Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL));

/* Render the picture (choose the best way) */
$myPicture->autoOutput("example.drawBarChart.png");

?>