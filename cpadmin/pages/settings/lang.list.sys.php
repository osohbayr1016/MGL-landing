<?php

$mainType = "";
if(isset($_REQUEST["typeType"]))
	$mainType = $_REQUEST["typeType"];

if($mainType=="")
	$mainType = "worktype";


$widJsArr["typeslist"] = $clkMenuModDir."lang.list.js.php";


$incPageUrl = $clkMenuModDir."lang.list.php";
	

$db->orderBy("langOrder","asc");
$langArr = $db->get($db_lang);

?>