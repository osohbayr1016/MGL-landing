<?php

$slideType = "banner";
$selSlideID = 0;


if(isset($_REQUEST["objID"]))
	$selSlideID=txtSec($_REQUEST["objID"]);
	
if(isset($_REQUEST["slideType"]))
	$slideType=txtSec($_REQUEST["slideType"]);

//$widJsArr["districtedit"] = $clkMenuModDir."type.edit.js.php";


if($selSlideID>0){
	

	$db->where("ceoID", $selSlideID);
	$selTypeObj = $db->getOne($db_ceo);

	$lastTypeOrder = $selTypeObj["ceoOrder"];
	$slideType=$selTypeObj["slideType"];
	
	$selCeoPics = explode(":",$selTypeObj["ceoSlide"]);
	$selCeoTypeArr = [];
	if($selTypeObj["ceoType"]!=""){
		
		$selCeoTypes = explode("|",$selTypeObj["ceoType"]);
		
		foreach($selCeoTypes as $k=>$ceoType){
		    
			$selCeoTypeArr[$ceoType] = $ceoType;
		}
	}
	$selProTypeArr = [];
	if($selTypeObj["proType"]!=""){
		$selProTypes = explode("|",$selTypeObj["proType"]);
		
		foreach($selProTypes as $k=>$proType){
			$selProTypeArr[$proType] = $proType;
		}
	}
	
}
else{
	
	$db->orderBy("`ceoOrder`","DESC");
	$lastTypeOrder = $db->getValue($db_ceo,"`ceoOrder`",1) + 1;
	
}


$db->where("menu_menu", 0);
$db->where("type", "person");
$db->where("lang", $adminLang);
$db->orderBy("`order`","asc");
$typesArr = $db->get($db_type);

$db->where("menu_menu", 0);
$db->where("type", "protype");
$db->where("lang", $adminLang);
$db->orderBy("`order`","asc");
$proTypesArr = $db->get($db_type);

$db->where("menu_menu", 0);
$db->where("type", "pro");
$db->where("lang", $adminLang);
$db->orderBy("`order`","asc");
$proTypeArr = $db->get($db_type);


$incPageUrl = $clkMenuModDir."about.ceo.frm.php";


?>