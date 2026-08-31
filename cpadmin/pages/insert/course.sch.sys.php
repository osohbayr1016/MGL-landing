<?php
	
$selCourseID = 0;

if(isset($_REQUEST["objID"]))
	$selCourseID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."course.sch.frm.php";
	
if($selCourseID>0){
	
	$typeSql = "SELECT A.`schOrder` FROM $db_coursesch A WHERE A.`schKey`='$selCourseID'  order by A.`schOrder` DESC LIMIT 1";
	$selTypeArr = $mainClass->selectQueryClass($typeSql);	
	$selTypeObj = $selTypeArr[0];	
	
	$lastTypeOrder = $selTypeObj["schOrder"]+1;
	
			
}
else{
	
	$typeSql = "SELECT A.`schOrder` FROM $db_coursesch A WHERE A.`schKey`='$gloSessionID'  order by A.`schOrder` DESC LIMIT 1";
	
	$lastTypeArr = $mainClass->selectQueryClass($typeSql);	
	$lastTypeOrder = $lastTypeArr[0]["schOrder"] + 1;	
	
	
}


?>