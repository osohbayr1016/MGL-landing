<?php
$selTypeID = 0;

if(isset($_REQUEST["objID"]))
	$selTypeID=txtSec($_REQUEST["objID"]);
		
$incPageUrl = $clkMenuModDir."detail.add.frm.php";

	
if($selTypeID>0){
	
	$typeSql = "SELECT A.* FROM $db_details A WHERE A.typeID='$selTypeID'";
						
						
	$selTypeArr = $mainClass->selectQueryClass($typeSql);	
	$selTypeObj = $selTypeArr[0];	
	$lastTypeOrder = $selTypeObj["detailOrder"] * 1;
	
			
}
?>