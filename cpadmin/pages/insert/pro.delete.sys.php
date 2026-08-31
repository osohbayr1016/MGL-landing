<?php
$selNewsID = 0;

if(isset($_REQUEST["objID"]))
	$selNewsID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."pro.delete.php";

	
if($selNewsID>0){
	
	$isDelete = true;
	

	$db->where("newsID", $selNewsID);
	$selNewObj = $db->getOne($db_newsroll);

			
}

?>