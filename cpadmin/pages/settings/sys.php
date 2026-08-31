<?php

$clkMenuMod = txtSec($_REQUEST["incPageType"]);
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";


$subPage = "";
if(isset($_REQUEST["subPage"]))
	$subPage = txtSec($_REQUEST["subPage"]);
	
switch($subPage){
	case "langDelete":
		include "lang.delete.sys.php";
	break;
	case "langEdit":
		include "lang.edit.sys.php";
	break;
	case "langs":
		include "lang.list.sys.php";
	break;
	default:
		
		include "config.sys.php";

	break;
}
?>