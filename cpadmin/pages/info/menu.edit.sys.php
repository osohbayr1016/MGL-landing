<?php

$mainType = "main";

if(isset($_REQUEST["typeType"]))
	$mainType = $_REQUEST["typeType"];
	
	
$selTypeID = 0;

if(isset($_REQUEST["objID"]))
	$selTypeID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."menu.edit.frm.php";

	
if($selTypeID>0){
	
						
	$db->where("id", $selTypeID);
	$selTypeObj = $db->getOne($tbl_main_menu);

	$lastTypeOrder = $selTypeObj["order"];	
	$mainMenuID	 = $selTypeObj["menu_menu"];
			
}
else{
	
	
	$db->orderBy("`order`","DESC");
	$db->where("menuLoc", $mainType);
	$db->where("menu_menu", 0);
	$db->where("lang", $adminLang);
	$lastTypeOrder = $db->getValue($tbl_main_menu,"`order`",1) + 1;

	
	$mainMenuID	 = 0;
	
}
?>