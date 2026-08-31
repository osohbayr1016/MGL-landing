<?php

$mainType = $_REQUEST["typeType"];

if($mainType=="")
	$mainType = "worktype";


$widJsArr["typeslist"] = $clkMenuModDir."types.list.js.php";


$incPageUrl = $clkMenuModDir."types.list.php";
	

$db->where('type', $mainType);
$db->where('lang', $adminLang);
$db->orderBy("`order`","asc");
$typesArr = $db->get($db_type);


$allTypesArr		= formatTree($typesArr, 0, 'menu_menu', 'id');
?>