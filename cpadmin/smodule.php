<?php	
include_once("const.php");
include_once("functions.php");
	if(isset($_REQUEST["action"])) $_action = $_REQUEST["action"];
	
	if($_action=="") $_action = "default";
	
	switch($_action){
		case "commentReply":
			include "_php/comment.reply.php";
		break;
		case "curxml":
			include "_php/currency.data.setting.php";
		break;
		default:
			include $templateDir."home.php";
		break;
	}
	
?>