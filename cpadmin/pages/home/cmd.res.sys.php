<?php
$selTypeID = 0;

if(isset($_REQUEST["objID"]))
	$selTypeID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."cmd.res.php";

	
if($selTypeID>0){
	
	
	$db->where ("id", $selTypeID);
	$selTypeObj = $db->getOne($tbl_comment);

}

?>