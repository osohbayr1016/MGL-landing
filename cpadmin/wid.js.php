<?php	
	include_once("const.php");
	include_once("functions.php");
	
	header('Content-Type: application/javascript');
	
	$widType = txtSec($_REQUEST["widType"]);
	$widID = txtSec($_REQUEST["widID"]);
	$jsName = txtSec($_REQUEST["jsName"]);
	if(!is_file("/widgets/".$widType."/".$jsName.".php"))
		include "widgets/".$widType."/".$jsName.".php";
			
	
?>