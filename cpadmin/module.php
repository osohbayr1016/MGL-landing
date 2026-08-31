<?php
	
include "config.php";


include "user.info.php";
	
if(isset($_REQUEST["action"])) 
	$_action = txtSec($_REQUEST["action"]);
	
if($gloUserOnline){
	
	switch($_action){
		case "userLogin":
		
			$sysModule = "users";
	
			if(is_file($gloConstModuleDir.$sysModule."/sys.php"))
				include $gloConstModuleDir.$sysModule."/sys.php";
				
		break;
		case "logout":
			setcookie("umail","",time()-3600);
			  setcookie("upass","",time()-3600);
			  session_destroy();
			  header("location: /");
		break;
		case "modAjax":
			
			$sysModule = txtSec($_REQUEST["modName"]);
	
			if(is_file($gloConstModuleDir.$sysModule."/sys.php"))
				include $gloConstModuleDir.$sysModule."/sys.php";
				
			if(is_file($gloConstReportDir.$sysModule."/sys.php"))
				include $gloConstReportDir.$sysModule."/sys.php";
		
		break;
	}

}
?>