<?php
	
	include "config.php";	
	
	include "user.info.php";
	
	if($gloUserOnline){
		

		$incPage = "home";

		if(isset($_REQUEST["incPageType"]))
			$incPage = txtSec($_REQUEST["incPageType"]);

		
	
		if(!is_file("pages/".$incPage."/sys.php"))
			$incPage = "home";
			
		include "pages/".$incPage."/sys.php";		
		
		
		if(isset($_POST["modAjax"]))
			include $incPageUrl;
		else
			include "skin/new/home.php";
		
		
	
	}
	else		
		include "skin/new/login.php";
		
?>