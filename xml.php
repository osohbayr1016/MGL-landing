<?php	
session_start();
header('Content-Type: application/xml');
include "config.php";

include "site.info.php";

	
switch($_REQUEST["page"]){
	case "location":
		include "widgets/pagesch/loc.xml.sys.php";
	break;	
	case "news":
		include "pages/news/list.sys.php";
	break;	
	case "newsmore":
		include "pages/news/more.sys.php";
	break;	
	case "productmore":
		include "pages/product/more.sys.php";
	break;	
}

?>