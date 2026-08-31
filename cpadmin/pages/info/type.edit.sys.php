<?php

$mainType = "company";

if(isset($_REQUEST["typeType"]))
	$mainType = $_REQUEST["typeType"];
	
	
$selTypeID = 0;

if(isset($_REQUEST["objID"]))
	$selTypeID=txtSec($_REQUEST["objID"]);
		
$widJsArr["districtedit"] = $clkMenuModDir."type.edit.js.php";

$incPageUrl = $clkMenuModDir."type.edit.frm.php";

$addWhere = "and A.lang='$adminLang'";
if($mainType=="pagesch") $addWhere = "";
	
if($selTypeID>0){
	
						
	$db->where("id", $selTypeID);
	$selTypeObj = $db->getOne($db_type);
	
	$lastTypeOrder = $selTypeObj["order"];
	
	$mainCatID	 = $selTypeObj["menu_menu"];
			
}
else{

	$db->orderBy("`order`","DESC");
	$db->where("type", $mainType);
	$db->where("menu_menu", 0);
	$db->where("lang", $adminLang);
	$lastTypeOrder = $db->getValue($db_type,"`order`",1) + 1;

	
	$mainCatID	 = 0;
	
}
?>