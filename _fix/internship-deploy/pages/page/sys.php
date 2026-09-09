<?php


$clkMenuMod = "page";
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";


$selPageID = txtSec($_REQUEST["pageID"]);


$db->where ("id", $selPageID);
$clkMenuObj = $db->getOne($tbl_main_menu); 


$pageID = $clkMenuObj["id"];
$mainMenuID =  $pageID;
if($clkMenuObj["menu_menu"]>0){
	$mainMenuID =  $clkMenuObj["menu_menu"];

	$db->where ("id", $mainMenuID);
	$mainMenuObj = $db->getOne($tbl_main_menu); 


}


$db->where ("menu_menu", $mainMenuID);
$subMenuArr = $db->get($tbl_main_menu); 


$db->orderBy("`order`","ASC");
$db->where ("menu_menu", $mainMenuID);
$subMenuArr = $db->get($tbl_main_menu); 

$addPageTitle = $clkMenuObj["name"];
$siteInfoDes = preg_replace('/\s+/', ' ',convertPhotoHtml(strip_tags($newsObj["newsDesc"])));	
$siteInfoImg   = absUrl(picUrl("news",$newsObj["newsID"].".jpg"));
	

/* Өргөдлийн форм — CP Admin -> Дадлага -> Тохиргоо хэсэгт заасан хуудсуудад
   агуулгын доор автоматаар гарна (өгөгдмөл: /page/7 дадлага, /page/6 ажлын байр). */
include_once __DIR__ . "/../../class/apply.class.php";

ApplyCore::ensure($db);

$applyEmbed = ApplyCore::embedPages(ApplyCore::settings($db));

if (isset($applyEmbed[(int)$pageID])) {
	$applyType = $applyEmbed[(int)$pageID];
	include $gloConstModuleDir . "apply/handle.php";
}

switch($clkMenuObj["pageType"]){
	
	case "onepage":

		$gloOnePage = true;

		
		$allWidgetArr = array("pagesch");
		$incPageUrl = $clkMenuModDir."onepage.php";
	break;
	default:
		$allWidgetArr = array("pagesch");
		$incPageUrl = $clkMenuModDir."page.php";
	break;
}
?>