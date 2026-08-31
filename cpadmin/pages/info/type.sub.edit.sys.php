<?php
$selTypeID = 0;

$mainType = "company";

if(isset($_REQUEST["typeType"]))
	$mainType = $_REQUEST["typeType"];

		
$widJsArr["districtedit"] = $clkMenuModDir."type.edit.js.php";

$incPageUrl = $clkMenuModDir."type.edit.frm.php";

if($mainCatID>0){
	
	$db->orderBy("`order`","DESC");
	$db->where("menu_menu", $mainCatID);
	$lastTypeOrder = $db->getValue($db_type,"`order`",1) + 1;
		
}
else{
	
	if(isset($_REQUEST["objID"]))
		$selTypeID=txtSec($_REQUEST["objID"]);
		
	
	$db->where("id", $selTypeID);
	$selTypeObj = $db->getOne($db_type);
		
	$lastTypeOrder = $selTypeObj["order"];
	
	$mainCatID	 = $selTypeObj["menu_menu"];
	

}
?>