<?php
$objID = 0;

if(isset($_REQUEST["objID"]))
	$objID=txtSec($_REQUEST["objID"]);
	
$selMainCat = $objID;
		
$widJsArr["mapedit"] = $clkMenuModDir."pro.edit.js.php";

$incPageUrl = $clkMenuModDir."pro.edit.frm.php";
	


$db->where("menu_menu", 0);
$db->where("type", "worktype");
$db->where("lang", $adminLang);
$db->orderBy("`order`","asc");
$typesArr = $db->get($db_type);


$db->where("menu_menu", $typesArr[0]["id"]);
$db->orderBy("`order`","asc");
$subTypesArr = $db->get($db_type);



?>