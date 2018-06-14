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
$request = $_GET;
$labels = array();
$cat020Points = array();
$cat2140Points = array();
$cat4160Points = array();
$cat6180Points = array();
$cat81andabovePoints = array();
$sameRating = array();
//$num_of_schools = $_GET['num_of_schools'];
$max_num = 0;
//print_r($request);die;
foreach($request as $k=>$r){
	//print_r($r);
	/* if($k=='num_of_schools')
	 continue; */
	$cat_020 = "0";
	$cat_2140 = "0";
	$cat_4160 = "0";
	$cat_6180 = "0";
	$cat_81andabove = "0";
	$values = explode(';',$r);
	$name = substr($values[0],strpos($values[0],'_')+1);
	//echo '<br/>',$name,'<br/>';
	$num = count($values);//echo $num;
	//print_r($values);die;
	for($i=1;$i<$num;$i++){
		//$var_name = str_replace(" ","_",substr($values[$i],0,strpos($values[$i],'_')));
		$var_name = 'cat_'.str_replace("-","",substr($values[$i],0,strpos($values[$i],'_')));
		//echo $var_name;
		$$var_name = substr($values[$i],strpos($values[$i],'_')+1);
	}
	$break = 55;
	$tempName =str_split($name,$break);
	//$tempName = explode(PHP_EOL,wordwrap($name,$break,PHP_EOL,TRUE));
	/* array_walk($tempName,function(&$val) use($break){
	 $val = str_pad($val,$break,' ',STR_PAD_RIGHT);
	 });  */
	$name = $tempName[0].(!empty($tempName[1])?PHP_EOL.'-'.$tempName[1]:'');
	$labels[] = $name;
	array_push($cat81andabovePoints, $cat_81andabove>0?$cat_81andabove:0);
	array_push($cat6180Points, $cat_6180>0?$cat_6180:0);
	array_push($cat4160Points, $cat_4160>0?$cat_4160:0);
	array_push($cat2140Points, $cat_2140>0?$cat_2140:0);
	array_push($cat020Points, $cat_020>0?$cat_020:0);

}
$MyData->addPoints($cat81andabovePoints,"81 and above");
$MyData->addPoints($cat6180Points,"61-80");
$MyData->addPoints($cat4160Points,"41-60");
$MyData->addPoints($cat2140Points,"21-40");
$MyData->addPoints($cat020Points,"0-20");

 /* Create the pChart object */
 $myPicture = new pImage(217,240,$MyData);
 $MyData->setAxisName(0,"Number of Schools");
 $MyData->addPoints($labels,"Labels");
 $MyData->setSerieDescription("Labels","KPAs");
 $MyData->setAbscissa("Labels");

 /* Add a border to the picture */
 $MyData->setPalette("0-20",array("R"=>207,"G"=>207,"B"=>196,"Alpha"=>100));
 $MyData->setPalette("21-40",array("R"=>255,"G"=>179,"B"=>71,"Alpha"=>100));
 $MyData->setPalette("41-60",array("R"=>222,"G"=>165,"B"=>164,"Alpha"=>100));
 $MyData->setPalette("61-80",array("R"=>0,"G"=>130,"B"=>127,"Alpha"=>100));
 $MyData->setPalette("81 and above",array("R"=>130,"G"=>105,"B"=>83,"Alpha"=>100));
 
 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../public/fonts/HelveticaLTStd-LightCond.ttf","FontSize"=>9));
 $myPicture->drawText(10,13,"drawStackedBarChart() - draw a stacked bar chart",array("R"=>255,"G"=>255,"B"=>255));

 /* Draw the scale and the 1st chart */
 $myPicture->setGraphArea(30,10,170,220);
 
 $myPicture->drawScale(array("DrawSubTicks"=>TRUE,"Mode"=>SCALE_MODE_ADDALL_START0));
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 $myPicture->drawStackedBarChart(array("DisplayValues"=>TRUE,"Rounded"=>FALSE));
 $myPicture->setShadow(FALSE);

 /* Write the chart legend */
 $myPicture->drawLegend(127,20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_VERTICAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawStackedBarChart.png");
?>