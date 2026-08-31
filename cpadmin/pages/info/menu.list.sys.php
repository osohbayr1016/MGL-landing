<?php

$mainType = $_REQUEST["typeType"];

if($mainType=="")
	$mainType = "main";


$widJsArr["typeslist"] = $clkMenuModDir."menu.list.js.php";


$incPageUrl = $clkMenuModDir."menu.list.php";
	


$db->where('menuLoc', $mainType);
$db->where('lang', $adminLang);
$db->orderBy("`order`","asc");
$typesArr = $db->get($tbl_main_menu);

$allTypesArr		= formatTree($typesArr, 0, 'menu_menu', 'id');
?>