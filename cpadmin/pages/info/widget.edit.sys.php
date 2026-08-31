<?php

	
$selWidgetID = 0;

if(isset($_REQUEST["objID"]))
	$selWidgetID=txtSec($_REQUEST["objID"]);
		

$incPageUrl = $clkMenuModDir."widget.edit.frm.php";

	
if($selWidgetID>0){
	
	$db->where("id", $selWidgetID);
	$selWidgetObj = $db->getOne($db_widgets);

	$db->where('rowType', 'main');
	$db->where('widgetID', $selWidgetID);
	$db->orderBy("`colOrder`","asc");
	$selMainRowArr = $db->get($db_widget_cols);


	$db->where('rowType', 'sub');
	$db->where('widgetID', $selWidgetID);
	$db->orderBy("`colOrder`","asc");
	$selSubRowArr = $db->get($db_widget_cols);
	
			
}

?>