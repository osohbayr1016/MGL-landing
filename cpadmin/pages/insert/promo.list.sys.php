<?php

$thisTypeType = "blogcat";

$widJsArr["maplist"] = $clkMenuModDir."promo.list.js.php";


$incPageUrl = $clkMenuModDir."promo.list.php";

$selMenuID = 0;

foreach($gloMenuType as $key=>$obj){
	
	$db->where('menuLoc', $key);
	$db->where('lang', $adminLang);
	$db->orderBy("`order`","asc");
	$typesArr = $db->get($tbl_main_menu);


	if($selMenuID<1){
		$selMenuID = $typesArr[0]["id"];
		$selMenuObj = $typesArr[0];
	}
	
	$menuArr[$key]		= formatTree($typesArr, 0, 'menu_menu', 'id');

}

if(isset($_REQUEST["objID"])){
	
	$selMenuID=txtSec($_REQUEST["objID"]);	
	$db->where("id", $selMenuID);
	$selMenuObj = $db->getOne($tbl_main_menu);	
		
}


$courseID = $selMenuID;
include "page.sch.list.sys.php";
	
switch($selMenuObj["pageType"]){
	case "video":
		

		$db->where('lang', $adminLang);
		$db->orderBy("`visualDate`","desc");
		$ceoArr = $db->get($db_visual);
		
	break;
	case "projects":
		
		$db->where('lang', $adminLang);
		$db->orderBy("`ceoOrder`","asc");
		$ceoArr = $db->get($db_ceo);
		
	break;
}
	

?>