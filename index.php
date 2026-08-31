<?php
session_start();
include "config.php";


include "site.info.php";

$gloIncHomePage = "home.php";
$gloPageIframe = false;
$gloOnePage = false;
$homeTools = false;
$pageHeaderCls = "page-header-default";
$themeColor     = "#FFFFFF";

$incPage = "home";

if(isset($_REQUEST["incPageType"]))
	$incPage = txtSec($_REQUEST["incPageType"]);

if(!is_file("pages/".$incPage."/sys.php"))
	$incPage = "home";
	
include "pages/".$incPage."/sys.php";


include "widgets/inc.widget.php";


if(isset($_POST["ajaxMod"]))
	include $gloConstSkinDir."ajax.php";
else{
	
	if(isset($_REQUEST["qs"]) && $_REQUEST["qs"]=="in-frame")
		$gloPageIframe = true;

	

	$db->orderBy("`order`","asc");
	$db->where("lang", $gloLang);
	$db->where("menuLoc", "main");
	
	$mainMenuArr = $db->get($tbl_main_menu);


	// $db->where("lang", $gloLang);
	// $db->where("menuLoc", "bott");
	// $db->orderBy("order","asc");
	// $bottMenuArr = $db->get($tbl_main_menu);

	
	$allAllMenuArr = formatTree($mainMenuArr, 0, "menu_menu", "id");
	// $bottAllMenuArr = formatTree($bottMenuArr, 0, "menu_menu", "id");


	include $gloConstSkinDir.$gloIncHomePage;
}
		
?>