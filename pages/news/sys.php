<?php

$clkMenuMod = "news";
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";

$db->where ("pageType", $clkMenuMod);
$db->where ("lang", $gloLang);
$db->where ("menu_menu", 0);
$clkMenuObj = $db->getOne($tbl_main_menu); 

$pageID = $clkMenuObj["id"];

if(isset($_REQUEST["newsID"])){
		
	
	$allWidgetArr = array("newsmore");
	$incPageUrl = $clkMenuModDir."more.php";

}
else{
	
	$allWidgetArr = array("pagesch");
	$incPageUrl = $clkMenuModDir."list.php";
}



?>