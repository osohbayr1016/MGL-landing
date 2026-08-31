<?php
$objID = 0;

if(isset($_REQUEST["objID"]))
	$objID=txtSec($_REQUEST["objID"]);
	
$selMainCat = $objID;
		
$widJsArr["mapedit"] = $clkMenuModDir."course.edit.js.php";

$incPageUrl = $clkMenuModDir."course.edit.frm.php";
	
$typesSql = "SELECT A.* FROM $db_ceo A 
					WHERE 1=1 ORDER BY A.`ceoID` ASC limit 0,100";
	$destArr = $mainClass->selectQueryClass($typesSql);

$typesSql = "SELECT A.* FROM $db_type A 
					WHERE A.`menu_menu`='0' and A.`type`='tourtype' ORDER BY A.`order` ASC limit 0,100";
					
$typesArr = $mainClass->selectQueryClass($typesSql);

$typesSql = "SELECT A.* FROM $db_type A WHERE A.`type`='includes' ORDER BY A.`order` ASC limit 0,100";
$tourIncArr = $mainClass->selectQueryClass($typesSql);



$courseID = 0;

include "course.sch.list.sys.php";
//include "exam.list.sys.php";
?>