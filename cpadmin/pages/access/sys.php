<?php

$clkMenuMod = txtSec($_REQUEST["incPageType"]);
$clkMenuModDir = $gloConstModuleDir.$clkMenuMod."/";


$subPage = "";
if(isset($_REQUEST["subPage"]))
	$subPage = txtSec($_REQUEST["subPage"]);
	
switch($subPage){
	case "add":
		$incPageUrl = $clkMenuModDir."group.add.frm.php";
	break;
	case "edit":
		include "group.edit.sys.php";
		$incPageUrl = $clkMenuModDir."group.add.frm.php";
	break;
	case "admins":
		include "admin.list.sys.php";
		$incPageUrl = $clkMenuModDir."admin.list.php";
	break;
	case "adminAdd":
	
		include "admin.edit.sys.php";
		$incPageUrl = $clkMenuModDir."admin.add.frm.php";
	break;
	case "adminDel":
		include "admin.delete.sys.php";
	break;
	case "adminEdit":
		include "admin.edit.sys.php";
		$incPageUrl = $clkMenuModDir."admin.add.frm.php";
	break;
	default:
		
		include "group.list.sys.php";

	break;
}
?>