<?php
$selNewsID = 0;

if(isset($_REQUEST["objID"]))
	$selNewsID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."visual.delete.php";

	
if($selNewsID>0){
	
	$isDelete = true;
	

	$db->where("visualID", $selNewsID);
	$selNewObj = $db->getOne($db_visual);
	
			
}

?>