<?php
$selTypeID = 0;

if(isset($_REQUEST["objID"]))
	$selTypeID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."course.sch.del.php";

	
if($selTypeID>0){
	
	$isDelete = false;
	
	$typeSql = "SELECT A.* FROM $db_coursesch A WHERE A.schID='$selTypeID'";
	$selTypeArr = $mainClass->selectQueryClass($typeSql);	
	$selTypeObj = $selTypeArr[0];	
	
	
	if(count($isTypeArr)<1)
		$isDelete = true;
			
}

?>