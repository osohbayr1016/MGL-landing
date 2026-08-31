<?php

$widJsArr["prolist"] = $clkMenuModDir."course.list.js.php";

$adminSql = "SELECT A.* FROM $tbl_prefadmin A WHERE 1 ";
$adminArr = $mainClass->selectQueryClass($adminSql);	

foreach($adminArr as $key=>$obj){
	$adminNameArr[$obj["id"]] = $obj["name"];
}

$incPageUrl = $clkMenuModDir."course.list.php";

$typesSql = "SELECT A.* FROM $db_type A WHERE A.`menu_menu`='0' and A.`lang`=$adminLang and A.`type`='tourtype' ORDER BY A.`name` ASC limit 0,100";
					
$typesArr = $mainClass->selectQueryClass($typesSql);	

if(isset($_REQUEST["objID"]) and $_REQUEST["objID"]>0){
	
	$catID=txtSec($_REQUEST["objID"]);	
	
	$selCatSql = "SELECT A.* FROM $db_type A WHERE A.`id`='$catID' ";
					
	$selCatArr = $mainClass->selectQueryClass($selCatSql);			
	$selCatTitle = $selCatArr[0]["name"];	
	$newsWhere = "A.menuID ='$catID'";
}
else{
	
	$catID = 0;
	$selCatTitle = "Бүх";
	$newsWhere = "A.`lang`=$adminLang";
}

$proSql = "SELECT A.* FROM $db_course A WHERE $newsWhere ORDER BY A.courseOrder DESC limit 0,100";
					
$newsArr = $mainClass->selectQueryClass($proSql);	

?>