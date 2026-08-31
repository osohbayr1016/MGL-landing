<?php

$sysReturnLink =  "/users";

switch($_POST["frmPost"]){
	case "login":
		
	
		$umail = $_POST["userName"];
		$upass = $_POST["userPass"];
		$upass = trim($upass);
	
		$umail = str_replace(  "/*", "" , $umail);
		$upass = str_replace(  "/*", "" , $upass);
	
		$umail = username_p($umail);
		$upass = username_p($upass);
		$upass = md5(convert_html($upass));	
	
		$db->where('aname',$umail);
		$db->where('apass',$upass);
		$numRows = $db->getValue($tbl_prefadmin, "count(*)");

		if($numRows>0)
		{ 
			$_SESSION["umail"]		=$umail;
			$_SESSION["upass"]		=$upass;
			
			
		}  
		$sysReturnLink = "/";
	
	break;
	case "checkname":

		$db->where('loginMail',$_POST["loginName"]);
		if($_POST["editID"]>0)
			$db->where('userID',$_POST["editID"],"!=");

		$count = $db->getValue($tbl_user, "count(*)");
		echo $count;
		die();
	break;
	case "filePost":
		
	
		unset($post_value_arr);
		$post_value_arr["fileName"]		=	$_POST["frmName"];
		$post_value_arr["fileLink"]		=	$_POST["frmLink"];
		$post_value_arr["userID"]		=	$_POST["frmUserID"];
		$post_value_arr["fileSize"]		=	$_POST["frmSize"];
		$post_value_arr["filepass"]		=	$_POST["frmPass"];
		
		if($_POST["frmEditID"]>0){
			
			$fileId = txtSec($_POST["frmEditID"]);
			

			$db->where ('id',$fileId);
			$db->update($db_files, $post_value_arr);
			
		
		}
		else{
			
			$post_value_arr["createDate"]	=	date("Y-m-d H:i:s");
			
			$fileId = $db->insert($db_files, $post_value_arr);
			
		}

		

		$sysReturnLink =  "/users/files/".$_POST["frmUserID"];
		
	
	break;
	case "userPost":
		
	
		unset($post_value_arr);
		$post_value_arr["companyName"]	=	$_POST["frmName"];
		$post_value_arr["companyPhone"]	=	$_POST["frmPhone"];
		$post_value_arr["companyMail"]	=	$_POST["frmMail"];
		$post_value_arr["loginMail"]	=	$_POST["frmLogin"];
		$post_value_arr["userName"]	=	$_POST["frmFname"];

		if($_POST["frmPass"]!="")
			$post_value_arr["loginPass"]	=	md5($_POST["frmPass"]);
		$post_value_arr["userActive"]	=	$_POST["frmStatus"];
		
		$db->where('loginMail',$_POST["frmLogin"]);
		if($_POST["frmEditID"]>0)
			$db->where('userID',$_POST["frmEditID"],"!=");

		$count = $db->getValue($tbl_user, "count(*)");

		if($count<1){
			if($_POST["frmEditID"]>0){
				
				$userId = txtSec($_POST["frmEditID"]);
				

				$db->where ('userID',$userId);
				$db->update($tbl_user, $post_value_arr);
				
			
			}
			else{
				
				$post_value_arr["createDate"]	=	time();
				
				$userId = $db->insert($tbl_user, $post_value_arr);
				
			}

		}

		$sysReturnLink =  "/users/list/";
		
	
	break;
	case "delete":
		
		
		$delID = txtSec($_POST["frmUserID"]);
		
		
		$db->where('userID', $delID);
		$db->delete($tbl_user);

		
		$sysReturnLink =  "/users/list/";
		
	
	break;
	case "fileDelete":
		
		
		$delID = txtSec($_POST["frmFileID"]);
		$userID = txtSec($_POST["frmUserID"]);
		
		$db->where('id', $delID);
		$db->delete($db_files);

		
		$sysReturnLink =  "/users/files/".$userID;
		
	
	break;
	
	
}


?>