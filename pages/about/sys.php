<?php

$clkMenuMod = "about";
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";



$db->where ("pageType", $clkMenuMod);
$db->where ("lang", $gloLang);
$db->where ("menu_menu", 0);
$clkMenuObj = $db->getOne($tbl_main_menu); 

$pageID = $clkMenuObj["id"];
	
$allWidgetArr = array("pagesch","submenu");
$incPageUrl = $clkMenuModDir."list.php";


?>