<?php	
include "config.php";



$sysReturnLink = "/";

include "user.info.php";
	
if($gloUserOnline){
		
	$sysModule = txtSec($_REQUEST["mod"]);
	
	if(is_file($gloConstModuleDir.$sysModule."/post.sys.php"))
		include $gloConstModuleDir.$sysModule."/post.sys.php";


}
else{
	
	$sysModule = "users";
	$_POST["frmPost"] = "login";
	
	if(is_file($gloConstModuleDir.$sysModule."/post.sys.php"))
		include $gloConstModuleDir.$sysModule."/post.sys.php";

}

header("location: $sysReturnLink");
?>