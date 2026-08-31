<?php
$selTypeID = 0;

if(isset($_REQUEST["objID"]))
	$selTypeID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."page.sch.del.php";

	
if($selTypeID>0){
	
	$isDelete = true;
	
	$typeSql = "SELECT A.* FROM $db_pagesch A WHERE A.schID='$selTypeID'";
	
	$db->where("schID", $selTypeID);
	$selTypeObj = $db->getOne($db_pagesch);
	
}

?>