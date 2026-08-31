<?php
	
$selCourseID = 0;

if(isset($_REQUEST["objID"]))
	$selCourseID=txtSec($_REQUEST["objID"]);


$incPageUrl = $clkMenuModDir."page.sch.frm.php";

$db->orderBy("`id`","asc");
$pageSchTemp = $db->get($db_widgets);

$selSchTemp = $pageSchTemp[0]["id"];
	

	
	$db->orderBy("`schOrder`","DESC");
	if($selCourseID>0)
		$db->where("schKey", $selCourseID);
	else
		$db->where("schKey", $gloSessionID);
	$db->where("parentID", "0");
	$lastTypeOrder = $db->getValue($db_pagesch,"`schOrder`",1) + 1;
	

if(isset($_REQUEST["frmModulType"]))
    $selSchTemp =  txtSec($_REQUEST["frmModulType"]);    


$db->where("widgetID", $selSchTemp);
$db->where("rowType", "main");
$db->orderBy("`colOrder`","asc");
$mainColArr = $db->get($db_widget_cols);

?>