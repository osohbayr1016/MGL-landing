<?php
if($courseID>0)
	$schWhere = $courseID;
else
	$schWhere = $gloSessionID;
	

$db->join("$db_widgets B", "B.id = A.schTemp", "LEFT");
$db->where("A.schKey", $schWhere);
$db->orderBy("A.schOrder", "ASC");
$schArr = $db->get("$db_pagesch A", null, "A.*, B.widgetTitle,B.widgetSub");


$allSchArr = formatTree($schArr, 0, "parentID", "schID");

?>