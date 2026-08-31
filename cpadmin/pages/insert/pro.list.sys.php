<?php

$thisTypeType = "worktype";

$widJsArr["prolist"] = $clkMenuModDir."pro.list.js.php";



$adminArr = $db->get($tbl_prefadmin);


foreach($adminArr as $key=>$obj){
	$adminNameArr[$obj["id"]] = $obj["name"];
}

$incPageUrl = $clkMenuModDir."pro.list.php";



$db->where("menu_menu", 0);
$db->where("type", $thisTypeType);
$db->where("lang", $adminLang);
$db->orderBy("`order`","asc");
$typesArr = $db->get($db_type);

if(isset($_REQUEST["objID"]) and $_REQUEST["objID"]>0){
	
	$catID=txtSec($_REQUEST["objID"]);	
	
	
	$db->where("id", $catID);
	$db->orderBy("`order`","asc");
	$selCatArr = $db->get($db_type);

	$selCatTitle = $selCatArr[0]["name"];	
	$db->where("newsCatID", $catID);
}
else{	
	$catID = 0;
	$selCatTitle = "Бүх";
}

$db->where("lang", $adminLang);
$db->orderBy("`createDate`","asc");
$newsArr = $db->get($db_newsroll);


?>