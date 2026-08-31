<?php
$selTypeID = 0;

if(isset($_REQUEST["objID"]))
	$selTypeID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."page.sch.sub.del.frm.php";

	
if($selTypeID>0){
	
	$isDelete = true;
	
	$db->where("schID", $selTypeID);
	$selTypeObj = $db->getOne($db_pagesch);

	
}

?>