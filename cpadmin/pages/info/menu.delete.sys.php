<?php
$selTypeID = 0;

if(isset($_REQUEST["objID"]))
	$selTypeID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."menu.delete.php";

	
if($selTypeID>0){
	
	$isDelete = false;
	

	$db->where("id", $selTypeID);
	$selTypeObj = $db->getOne($tbl_main_menu);
	
	$typeSql = "SELECT A.* FROM $db_pagesch A WHERE A.schKey in (SELECT id FROM $tbl_main_menu WHERE id=? or menu_menu=?)";	

	$isTypeArr = $db->rawQuery($typeSql, Array($selTypeID,$selTypeID));

	if(count($isTypeArr)<1)
		$isDelete = true;
			
}

?>