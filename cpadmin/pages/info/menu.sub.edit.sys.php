<?php

$mainType = "main";

if(isset($_REQUEST["typeType"]))
	$mainType = $_REQUEST["typeType"];

		
$incPageUrl = $clkMenuModDir."menu.edit.frm.php";

if($mainMenuID>0){
	
	$db->orderBy("`order`","DESC");
	$db->where("menu_menu", $mainMenuID);
	$lastTypeOrder = $db->getValue($tbl_main_menu,"`order`",1) + 1;
		
}
else{
			
	
						
						
	$db->where("id", $selTypeID);
	$selTypeObj = $db->getOne($tbl_main_menu);
	
	$lastTypeOrder = $selTypeObj["order"];
	
	$mainMenuID	 = $selTypeObj["menu_menu"];
	

}
?>