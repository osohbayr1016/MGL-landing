<?php

$sysReturnLink =  "/access";

switch($_POST["frmPost"]){
	case "groups":
		
		$adminAccess = "";
		
		if(count($_POST["frmAccess"])>0)
		foreach($_POST["frmAccess"] as $key=>$obj){
			
			if($obj!="")
				$adminAccess .=$obj."-";
			
		}
		
	
		$post_value_arr["adminGroupName"]	=	txtSec($_POST["frmName"]);
		$post_value_arr["adminGroupAction"]	=	$adminAccess;
		
		if($_POST["frmEditID"]>0){
		

			$db->where ('adminGroupID', txtSec($_POST["frmEditID"]));
			$db->update($tbl_admingroup, $post_value_arr);			
		
		}
		else{
			
			$typeID = $db->insert($tbl_admingroup, $post_value_arr);
			
		}
		
	
	break;
	case "admins":
		
		$sysReturnLink =  "/access/admins";
		
	
		$post_value_arr["name"]			=	txtSec($_POST["frmName"]);
		$post_value_arr["aname"]		=	txtSec($_POST["frmAName"]);
		$post_value_arr["adminGroupID"]	=	txtSec($_POST["frmGroup"]);
		
		if($_POST["frmPass"]!="")
			$post_value_arr["apass"]	=	md5($_POST["frmPass"]);
			
		if($_POST["frmEditID"]>0){
		

			$db->where ('id', txtSec($_POST["frmEditID"]));
			$db->update($tbl_prefadmin, $post_value_arr);		
		
		}
		else{
			
			$typeID = $db->insert($tbl_prefadmin, $post_value_arr);
			
		}
		
		
	break;
	case "adminDel":
		
		
		$delID = txtSec($_POST["frmDelID"]);
		
		
		$db->where('id', $delID);
		$db->delete($tbl_prefadmin);
		
		
		$sysReturnLink =  "/access/admins";
		
	
	break;
}


?>