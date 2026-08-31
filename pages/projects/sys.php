<?php

$clkMenuMod = "projects";
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";


if(isset($_REQUEST["productID"])){
	
	$selProductID = txtSec($_REQUEST["productID"]);


	$db->join("$db_type C", "C.id=A.ceoCat", "LEFT");
	$db->where("A.ceoID", $selProductID);
	$productObj = $db->getOne("$db_ceo A", "A.*, C.name typeName");
 
	$selProPics = explode(":",$productObj["ceoSlide"]);
	$productTypeArr=[];
	if($productObj["proType"]!=""){
	    $selProTypes = explode("|",$productObj["proType"]);
	    
	    $db->where ("id", $selProTypes, 'IN');
    	$db->orderBy("`order`","asc");
    	$productTypeArr = $db->get($db_type);
	
	}
	
	$allWidgetArr = array("projectmore");
	$incPageUrl = $clkMenuModDir."more.php";
	
}
else{
	
	$db->where ("pageType", $clkMenuMod);
	$db->where ("lang", $gloLang);
	$db->where ("menu_menu", 0);
	$clkMenuObj = $db->getOne($tbl_main_menu); 
	
	$pageID = $clkMenuObj["id"];
	
	
	
	$allWidgetArr = array("pagesch");
	$incPageUrl = $clkMenuModDir."projects.php";
}
?>