<?php
if($courseID>0)
	$schWhere = "A.schKey='$courseID'";
else
	$schWhere = "A.schKey='$gloSessionID'";
	
$contentSql = "SELECT A.* FROM $db_coursesch A WHERE $schWhere ORDER BY A.schOrder ASC limit 0,100";
$schArr = $mainClass->selectQueryClass($contentSql);	

$allSchArr = formatTree($schArr, 0, "parentID", "schID");

?>