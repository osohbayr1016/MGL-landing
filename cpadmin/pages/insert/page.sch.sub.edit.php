<?php
	
	
$editSchID = 0;

if(isset($_REQUEST["objID"]))
	$editSchID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."page.sch.sub.frm.php";
	
if($editSchID>0){
	
	$db->where("schID", $editSchID);
	$selTypeObj = $db->getOne($db_pagesch);

	$selSchBody = json_decode($selTypeObj["schNote"],true);

	$lastTypeOrder = $selTypeObj["schOrder"];
	$selCourseID = $selTypeObj["parentID"];
	

	$db->where("schID", $selCourseID);
	$selSchObj = $db->getOne($db_pagesch);
	
	
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