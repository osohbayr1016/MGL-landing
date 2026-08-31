<?php
$objID = 0;

if(isset($_REQUEST["objID"]))
	$objID=txtSec($_REQUEST["objID"]);
		
$widJsArr["mapedit"] = $clkMenuModDir."course.edit.js.php";

$incPageUrl = $clkMenuModDir."course.edit.frm.php";
	

if($objID>0){
	
	$editID = $objID;
	$proSql = "SELECT A.* FROM $db_course A WHERE A.courseID='$editID'";						
	$selProArr = $mainClass->selectQueryClass($proSql);	
	$selNewObj = $selProArr[0];
	
	$selTourDest = explode("|",$selNewObj["courseType"]);	

	$selTourInc = explode("|",$selNewObj["tourInc"]);	
	$selTourNotInc = explode("|",$selNewObj["tourNotInc"]);	

	$selPriceInd = explode("|",$selNewObj["tourPriceInd"]);	
	$selPriceVal = explode("|",$selNewObj["tourPriceVal"]);	

	if(count($selTourDest)>0)
	foreach($selTourDest as $key=>$obj){
		if($obj!="")
			$setlTourDestArr[$obj] = true;
	}

	if(count($selTourInc)>0)
	foreach($selTourInc as $key=>$obj){
		if($obj!="")
			$selTourIncArr[$obj] = true;
	}

	if(count($selTourNotInc)>0)
	foreach($selTourNotInc as $key=>$obj){
		if($obj!="")
			$selTourNotIncArr[$obj] = true;
	}
	
	$courseID = $editID;
	
	include "course.sch.list.sys.php";
	
	$typesSql = "SELECT A.* FROM $db_type A 
					WHERE A.`menu_menu`='0' and A.`type`='tourtype' and A.`lang`=$adminLang ORDER BY A.`order` ASC limit 0,100";
	$typesArr = $mainClass->selectQueryClass($typesSql);

	$typesSql = "SELECT A.* FROM $db_ceo A 
					WHERE A.pageID in (select id from $tbl_main_menu where `lang`=$adminLang) ORDER BY A.`ceoID` ASC limit 0,100";
	$destArr = $mainClass->selectQueryClass($typesSql);
	
	
}


$typesSql = "SELECT A.* FROM $db_type A WHERE A.`type`='includes' and A.`lang`=$adminLang ORDER BY A.`order` ASC limit 0,100";
$tourIncArr = $mainClass->selectQueryClass($typesSql);

?>