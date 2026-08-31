<?php

if(isset($_REQUEST["objID"])){
	
	$selSchID=txtSec($_REQUEST["objID"]);
	
	$schWhere = "A.parentID='$selSchID'";
		
	$contentSql = "SELECT A.* FROM $db_coursesch A WHERE $schWhere ORDER BY A.schOrder ASC limit 0,100";
	$schArr = $mainClass->selectQueryClass($contentSql);	
	
	$subSchList = $schArr;

}

?>