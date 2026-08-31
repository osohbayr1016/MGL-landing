<?php
	
	
$editSchID = 0;

if(isset($_REQUEST["objID"]))
	$editSchID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."course.sch.frm.php";
	
if($editSchID>0){
	
	$typeSql = "SELECT A.* FROM $db_coursesch A WHERE A.schID='$editSchID'";
						
						
	$selTypeArr = $mainClass->selectQueryClass($typeSql);	
	$selTypeObj = $selTypeArr[0];	
	$lastTypeOrder = $selTypeObj["schOrder"];
	$selCourseID = $selTypeObj["schKey"];
	if($selTypeObj["schImage"]!="")
		$schImageArr = explode("|",$selTypeObj["schImage"]);

	
}

?>