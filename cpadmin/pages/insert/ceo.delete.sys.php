<?php
$selNewsID = 0;

if(isset($_REQUEST["objID"]))
	$selNewsID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."ceo.delete.php";

	
if($selNewsID>0){
	
	$isDelete = true;
	
	
	$db->where("ceoID", $selNewsID);
	$selNewObj = $db->getOne($db_ceo);
			
}

?>