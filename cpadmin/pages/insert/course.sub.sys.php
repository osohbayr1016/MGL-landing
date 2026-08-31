<?php
	
$selCourseID = 0;

if(isset($_REQUEST["objID"]))
	$selCourseID=txtSec($_REQUEST["objID"]);
		


	
if($selCourseID>0){
	
	$typeSql = "SELECT A.* FROM $db_coursesch A WHERE A.schID='$selCourseID'";
	$selTypeArr = $mainClass->selectQueryClass($typeSql);	
	$selSchObj = $selTypeArr[0];	
	
	$typeSql = "SELECT A.* FROM $db_coursesch A WHERE A.parentID='$selCourseID' order by A.`schOrder` DESC LIMIT 1";
	$selTypeArr = $mainClass->selectQueryClass($typeSql);	
	$selSubObj = $selTypeArr[0];	
	
	$lastTypeOrder = $selSubObj["schOrder"]+1;
	
	$incPageUrl = $clkMenuModDir."course.sub.frm.php";
}


?>