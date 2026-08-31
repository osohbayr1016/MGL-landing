<?php
	
$selCourseID = 0;

if(isset($_REQUEST["objID"]))
	$selCourseID=txtSec($_REQUEST["objID"]);
		


	
if($selCourseID>0){
	
	$db->where("schID", $selCourseID);
	$selSchObj = $db->getOne($db_pagesch);

	
	$db->where("parentID", $selCourseID);
	$db->orderBy("`schOrder`","desc");
	$selSubObj = $db->getOne($db_pagesch);

	$lastTypeOrder = $selSubObj["schOrder"]+1;


	$db->where("widgetID", $selSchObj["schTemp"]);
	$db->where("rowType", "sub");
	$db->orderBy("`colOrder`","asc");
	$subColArr = $db->get($db_widget_cols);


	$incPageUrl = $clkMenuModDir."page.sch.sub.frm.php";

	
}

// if($selSchObj["schTemp"]=="doctors"){

// 	$selBranchSql = "SELECT A.* FROM $db_type A WHERE A.`type`='tourtype' order by A.order asc";
// 	$selTasagArr = $mainClass->selectQueryClass($selBranchSql);		

// 	$selBranchSql = "SELECT A.* FROM $db_type A WHERE A.`type`='includes' order by A.order asc";
// 	$selZeregArr = $mainClass->selectQueryClass($selBranchSql);		

// }

?>