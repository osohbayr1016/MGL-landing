<?php

$clkMenuMod = "users";
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";


$subPage = "";
if(isset($_REQUEST["subPage"]))
	$subPage = txtSec($_REQUEST["subPage"]);
	
switch($subPage){
	case "fileadd":
		
		include "file.add.sys.php";
		
	break;
	case "fileedit":
		
		include "file.edit.sys.php";
		
	break;
	case "filedelete":
		
		include "file.delete.sys.php";
		
	break;
	case "add":
		
		include "user.add.sys.php";
		
	break;
	case "edit":
		
		include "user.edit.sys.php";
		
	break;
	
	case "delete":	
		
		include "user.confrim.sys.php";
		
	break;
	case "files":
		
		include "file.list.sys.php";
		
	break;
	default:
		
		include "user.list.sys.php";
	break;
}
?>