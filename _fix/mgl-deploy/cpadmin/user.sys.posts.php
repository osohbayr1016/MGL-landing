<?php	
include "config.php";

$sysReturnLink = "/";
$isOrderAjax = !empty($_POST["ajaxOrder"]);

include "user.info.php";
	
if($gloUserOnline){
		
	$sysModule = txtSec($_REQUEST["mod"]);
	
	if(is_file($gloConstModuleDir.$sysModule."/post.sys.php"))
		include $gloConstModuleDir.$sysModule."/post.sys.php";

	if ($isOrderAjax) {
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(array("ok" => 0, "error" => "order_handler_missing"));
		exit;
	}

}
else{
	
	$sysModule = "users";
	$_POST["frmPost"] = "login";
	
	if(is_file($gloConstModuleDir.$sysModule."/post.sys.php"))
		include $gloConstModuleDir.$sysModule."/post.sys.php";

	if ($isOrderAjax) {
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(array("ok" => 0, "error" => "auth_required"));
		exit;
	}

}

header("location: $sysReturnLink");
?>
