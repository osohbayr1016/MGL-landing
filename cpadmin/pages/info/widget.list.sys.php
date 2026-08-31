<?php


$widJsArr["typeslist"] = $clkMenuModDir."widget.list.js.php";


$incPageUrl = $clkMenuModDir."widget.list.php";
	

$db->orderBy("`id`","asc");
$widgetArr = $db->get($db_widgets);

?>