<?php

$clkMenuMod = txtSec($_REQUEST["incPageType"]);
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";


$subPage = "";
if(isset($_REQUEST["subPage"]))
	$subPage = txtSec($_REQUEST["subPage"]);
	
switch($subPage){
	case "autoSubType":
	
		include "auto.sub.type.php";
		die();
		
	break;
	case "menus":
		include "menu.list.sys.php";
	break;
	case "menuEdit":
		include "menu.edit.sys.php";
	break;
	case "menuDelete":
		include "menu.delete.sys.php";
	break;
	case "submenuadd":
		
		if(isset($_REQUEST["objID"]))
			$mainMenuID=txtSec($_REQUEST["objID"]);
	
		include "menu.sub.edit.sys.php";
	break;
	case "widget":
		include "widget.list.sys.php";
	break;
	case "widgetEdit":
		include "widget.edit.sys.php";
	break;
	case "types":
		include "types.list.sys.php";
	break;
	case "typeEdit":
		include "type.edit.sys.php";
	break;
	case "typeDelete":
		include "type.delete.sys.php";
	break;
	case "subtypeadd":
		
		if(isset($_REQUEST["objID"]))
			$mainCatID=txtSec($_REQUEST["objID"]);
	
		include "type.sub.edit.sys.php";
	break;
	
	default:
		
		include "menu.list.sys.php";

	break;
}
?>