<?php
$selTypeID = 0;

if(isset($_REQUEST["objID"]))
	$selTypeID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."lang.delete.php";

	
if($selTypeID>0){
	
	$isDelete = true;

	$db->where("langID", $selTypeID);
	$selTypeObj = $db->getOne($db_lang);
	
	$db->where("lang", $selTypeID);
	$langMenuCount = $db->getValue($tbl_main_menu,"count(id)");

	if($langMenuCount>0)
		$isDelete = false;
			
}

?>