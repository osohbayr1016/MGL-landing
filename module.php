<?php	
session_start();
include "config.php";


include "site.info.php";

	
if(isset($_REQUEST["action"])) 
	$_action = txtSec($_REQUEST["action"]);


switch($_action){
	
	case "pagePost":
		$selPage = txtSec($_POST["selPage"]);
		include "pages/".$selPage."/post.sys.php";
	break;	
	case "modAjax":
		
		$sysModule = txtSec($_REQUEST["modName"]);

		if(is_file($gloConstWidDir.$sysModule."/sys.php"))
			include $gloConstWidDir.$sysModule."/sys.php";
	
	break;
	case "widSys":	

		$wid = txtSec($_POST["selWid"]);
		include "widgets/".$wid."/sys.php";

	break;
}
	
if($pdfIncFile!="")
	include "skin/new/pdf.php"
	
?>