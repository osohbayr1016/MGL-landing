<?php


$clkMenuMod = "home";
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";

if(isset($_REQUEST["changeLang"]))
	$_SESSION["adminLang"] = $_REQUEST["changeLang"];

	$subPage = "";
if(isset($_REQUEST["subPage"]))
	$subPage = txtSec($_REQUEST["subPage"]);
	
switch($subPage){
	case "delComm":
		include "cmd.del.sys.php";
	break;
	case "resComm":
		include "cmd.res.sys.php";
	break;
	default:
		
		include "def.php";

	break;
}

?>