<?php
$gloUserOnline = false;

if(isset($_SESSION["cpass"]) && isset($_SESSION["cmail"])) {
	
	$gloLang =1;
	$thisUserMail = txtSec($_SESSION["cmail"]);
	$thisUserPas = txtSec($_SESSION["cpass"]);
	
	$db->where ("loginMail", $thisUserMail);
	$db->where ("loginPass", $thisUserPas);
	$db->where ("userActive", "a");
	$onlainUserObj = $db->getOne($db_users);

	
	if($onlainUserObj!=null){ 
	
		
		$onlainUserName		= $onlainUserObj["userName"];
		$gloUserOnlineID	= $onlainUserObj["userID"];
		
		$gloUserOnline		= true;
		
		
	}
	
}
	
?>