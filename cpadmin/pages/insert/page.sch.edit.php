<?php
	
	
$editSchID = 0;

if(isset($_REQUEST["objID"]))
	$editSchID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."page.sch.frm.php";


$db->orderBy("`id`","asc");
$pageSchTemp = $db->get($db_widgets);

$selSchTemp = $pageSchTemp[0]["id"];
	
if($editSchID>0){
	

	$db->where("schID", $editSchID);
	$selTypeObj = $db->getOne($db_pagesch);

	$selSchTemp = $selTypeObj["schTemp"];
	$selSchBody = json_decode($selTypeObj["schNote"],true);
	$lastTypeOrder = $selTypeObj["schOrder"];
	$selCourseID = $selTypeObj["schKey"];
			
}

if(isset($_REQUEST["frmModulType"]))
    $selSchTemp =  txtSec($_REQUEST["frmModulType"]);    


$db->where("widgetID", $selSchTemp);
$db->where("rowType", "main");
$db->orderBy("`colOrder`","asc");
$mainColArr = $db->get($db_widget_cols);

?>