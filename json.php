<?php	
session_start();
header('Content-Type: application/json; charset=utf-8;');

include "config.php";

include "site.info.php";

	
if(isset($_REQUEST["action"])) 
	$_action = txtSec($_REQUEST["action"]);


switch($_action){
	case "contact":
		include "pages/contact.sys.php";
	break;	
	case "news":
		include "pages/news/list.sys.php";
	break;	
	case "newsmore":
		include "pages/news/more.sys.php";
	break;	
	case "leadership":
		include "widgets/pagesch/team.json.sys.php";
	break;	
}

$string = json_encode($apiResponse);
echo $string;
?>