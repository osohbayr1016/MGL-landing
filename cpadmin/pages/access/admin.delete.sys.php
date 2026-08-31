<?php
$selNewsID = 0;

if(isset($_REQUEST["objID"]))
	$selNewsID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."admin.delete.php";

	
if($selNewsID>0){
	
	$isDelete = true;
	
	$db->where("id", $selNewsID);
	$selNewObj = $db->getOne($tbl_prefadmin);
	
	if($selNewsID==$gloUserOnlineID)
		$isDelete = false;
	
			
}

?>