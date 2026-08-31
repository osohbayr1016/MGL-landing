<?php
$selNewsID = 0;

if(isset($_REQUEST["objID"]))
	$selNewsID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."course.delete.php";

	
if($selNewsID>0){
	
	$isDelete = true;
	
	$proSql = "SELECT A.* FROM $db_course A
				 WHERE A.courseID='$selNewsID'";						
	$selProArr = $mainClass->selectQueryClass($proSql);	
	$selNewObj = $selProArr[0];	
	
			
}

?>