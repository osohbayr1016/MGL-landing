<?php

$slideType = "banner";
$selSlideID = 0;


if(isset($_REQUEST["objID"]))
	$selSlideID=txtSec($_REQUEST["objID"]);
	
if(isset($_REQUEST["slideType"]))
	$slideType=txtSec($_REQUEST["slideType"]);



if($selSlideID>0){
	
	$db->where("visualID", $selSlideID);
	$selTypeObj = $db->getOne($db_visual);
	
	$selCeoPics = explode(":",$selTypeObj["visualSlide"]);
	$selCeoTypeArr = "";
	if($selTypeObj["visualFields"]!=""){
		
		$selCeoTypes = explode("|",$selTypeObj["visualFields"]);
		
		foreach($selCeoTypes as $k=>$ceoType){
			$selCeoTypeArr[$ceoType] = $ceoType;
		}
	}
	
	$selProTypeArr = "";
	if($selTypeObj["visualTools"]!=""){
		$selProTypes = explode("|",$selTypeObj["visualTools"]);
		
		foreach($selProTypes as $k=>$proType){
			$selProTypeArr[$proType] = $proType;
		}
	}
			
}



$db->where("menu_menu", 0);
$db->where("type", "fields");
$db->where("lang", $adminLang);
$db->orderBy("`order`","asc");
$typesArr = $db->get($db_type);

$db->where("menu_menu", 0);
$db->where("type", "tools");
$db->where("lang", $adminLang);
$db->orderBy("`order`","asc");
$proTypesArr = $db->get($db_type);

$db->where("menu_menu", 0);
$db->where("type", "pro");
$db->where("lang", $adminLang);
$db->orderBy("`order`","asc");
$proTypeArr = $db->get($db_type);



$typesSql = "SELECT A.* FROM $db_pagesch A 
					WHERE A.`parentID` in (SELECT schID FROM $db_pagesch where schTemp = '9')";

$proTypeArr = $db->rawQuery($typesSql);

$incPageUrl = $clkMenuModDir."visual.frm.php";


?>