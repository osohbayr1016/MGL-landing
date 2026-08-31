<?php

if(isset($_REQUEST["incPageType"])){
	$incLang = str_replace("/","",txtSec($_REQUEST["incPageType"]));
	

	$db->where ("langKey", $incLang);
	$incLangArr = $db->getOne($db_lang); 

	if($incLangArr!=null)
		$_SESSION["adminLang"] = $incLangArr["langID"];
}

if(isset($_REQUEST["pageID"])){

	$db->where ("id", txtSec($_REQUEST["pageID"]));
	$langMenuArr = $db->getOne($tbl_main_menu,"lang"); 

	
	if($langMenuArr!=null)
		$_SESSION["adminLang"] = $langMenuArr["lang"];

}

if(isset($_REQUEST["productID"])){

	$db->where ("ceoID", txtSec($_REQUEST["productID"]));
	$langTourArr = $db->getOne($db_ceo,"lang"); 
	
	if($langTourArr!=null)
		$_SESSION["adminLang"] = $langTourArr["lang"];

}



$db->orderBy("langOrder","asc");
$sysLangArr = $db->get($db_lang);


if(!isset($_SESSION["adminLang"]))
	$_SESSION["adminLang"] = $sysLangArr[0]["langID"];
	
$gloLang =  $_SESSION["adminLang"];

$selSiteLang = false;

if(count($sysLangArr)>0)
foreach($sysLangArr as $key=>$obj){
	if($obj["langID"]==$gloLang){
		$selSiteLang = true;
		$gloLangObj = $obj;
	}
}

if(!$selSiteLang){
	$gloLangObj = $sysLangArr[0];
	$_SESSION["adminLang"] = $sysLangArr[0]["langID"];
}
	

	$gloLang =  $_SESSION["adminLang"];

	
// $mainClass->tableClass=$tbl_config;
// $siteConfigWhere	= "1=1";
// $selConfigArr = $mainClass->selectClass($siteConfigWhere,"0,1","id","");
// $selConfig = $selConfigArr[0];

$selConfig = $db->getOne($tbl_config); 

$siteName			= $selConfig["site_name"];
$siteInfoName		= $selConfig["site_name"];
$siteInfoMail		= "info@mglenc.com";
$siteInfoDes		= $selConfig["siteDes"];
$siteInfoKeywords	= $selConfig["keywords"];
$siteInfoImg		= $gloConstSiteURL."fb.jpg";
$siteInfoTitleF 	= "mglenc.com | ";
$siteInfoThisUrl	= $gloConstSiteURL;
$siteMainTitle 		= $siteInfoName;


$gloBodyClass = "";

$defPP = 12;

if(!isset($_REQUEST["pp"])){
	
	$_pp =$defPP;
	
}
else{
	
	$_pp = $_REQUEST["pp"];
	if($_pp%$defPP!=0){
		$_pp = $defPP;
	}
	
}
if(!isset($_REQUEST["p"]))
	$_p = 1;
else
	$_p = $_REQUEST["p"];
	


?>
