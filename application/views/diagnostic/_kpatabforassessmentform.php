<?php 
	$isActive=true;
	$kpa_no=1;
	$kpa=array();
	if(isset($kpas[$kpa_id])){
		$kpa=$kpas[$kpa_id];
		include("common/kpatab.php");
		echo "\n<input type=\"hidden\" name=\"kpa_id\" value=\"$kpa_id\" />\n";
	}
?>