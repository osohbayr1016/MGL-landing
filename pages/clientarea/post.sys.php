<?php

switch($_POST["frmPost"]){
	case "changepass":
		

		$err = "";
	
		$oldPass = trim($_POST["oldPass"]);
		$newPass = trim($_POST["newPass"]);
		$newPass2 = trim($_POST["newPass2"]);

		
	
		$oldPass = str_replace(  "/*", "" , $oldPass);
		$newPass = str_replace(  "/*", "" , $newPass);
	
		$oldPass = md5(username_p($oldPass));
		$newPass = md5(username_p($newPass));

		if($newPass!=md5($newPass2))
			$err = "2";

		if($oldPass!=$_SESSION["cpass"])
			$err = "1";
	
		if($err==""){
			
			$thisUserMail = txtSec($_SESSION["cmail"]);
			$thisUserPas = txtSec($_SESSION["cpass"]);
			$db->where ("loginMail", $thisUserMail);
			$db->where ("loginPass", $thisUserPas);
			$db->where ("userActive", "a");
			$onlainUserObj = $db->getOne($db_users);

			unset($post_value_arr);
			$post_value_arr["loginPass"]	=	$newPass;
		

			$db->where ('userID', $onlainUserObj["userID"]);
			$db->update($db_users, $post_value_arr);		
		
			$_SESSION["cpass"]		=$newPass;
			
			$err = "done";
			
		}

		header("location: /clientarea/changepass?err=".$err);
	
	break;
	case "login":
		
	
		$umail = $_POST["userName"];
		$upass = $_POST["userPass"];
		$upass = trim($upass);
	
		$umail = str_replace(  "/*", "" , $umail);
		$upass = str_replace(  "/*", "" , $upass);
	
		$umail = username_p($umail);
		$upass = username_p($upass);
		$upass = md5(txtSec($upass));	
	
		$db->where('loginMail',$umail);
		$db->where('loginPass',$upass);
		$numRows = $db->getValue($db_users, "count(*)");

		if($numRows>0)
		{ 
			$_SESSION["cmail"]		=$umail;
			$_SESSION["cpass"]		=$upass;
			
			
		}  
		header("location: /clientarea");
	
	break;
}

?>