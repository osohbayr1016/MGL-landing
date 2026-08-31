<?php

$clkMenuMod = "home";
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";

$db->where ("pageType", $clkMenuMod);
$db->where ("lang", $gloLang);
$db->where ("menu_menu", 0);
$clkMenuObj = $db->getOne($tbl_main_menu); 


$pageID = $clkMenuObj["id"];
$themeColor     = "#003d59";


$allWidgetArr = array("pagesch");

$bannerType = "H";
$incPageUrl = $clkMenuModDir."home.php";
$homeTools = true;
?>