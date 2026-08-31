<?php	
include_once("const.php");
include_once("functions.php");
	if(isset($_REQUEST["action"])) $_action = $_REQUEST["action"];
	
	if($_action=="") $_action = "default";
	
	switch($_action){
		case "widSys":	

			$wid = txtSec($_REQUEST["selWid"]);
			include "widgets/".$wid."/sys.php";
	
		break;
		default:
			include $templateDir."home.php";
		break;
	}
	
?>