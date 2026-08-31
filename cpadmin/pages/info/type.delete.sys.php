<?php
$selTypeID = 0;

if(isset($_REQUEST["objID"]))
	$selTypeID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."type.delete.php";

	
if($selTypeID>0){
	
	$isDelete = true;
	

	$db->where("id", $selTypeID);
	$selTypeObj = $db->getOne($db_type);
	

			
}

?>