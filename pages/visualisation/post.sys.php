<?php

switch($_POST["frmPost"]){
	case "join":
	
		$returnUrl = "/user/jobs";
		
		if($gloComID>0 and $gloUserOnline){
			
			$mainClass->tableClass=$db_jobs;
			
			$dateRange = explode("-",txtSec($_POST["frmDate"]));
			
			unset($post_value_arr);
			$post_value_arr["userID"]		=	$gloUserOnlineID;
			$post_value_arr["companyID"]	=	$gloComID;
			$post_value_arr["jobTitle"]		=	txtSec($_POST["frmTitle"]);
			$post_value_arr["jobBranch"]	=	txtSec($_POST["frmBranch"]);
			$post_value_arr["jobLoc"]		=	txtSec($_POST["frmLoc"]);
			$post_value_arr["jobWorkNote"]	=	txtSec($_POST["frmUureg"]);
			$post_value_arr["jobNote"]		=	txtSec($_POST["frmShaardlaga"]);
			$post_value_arr["jobTime"]		=	txtSec($_POST["frmTime"]);
			$post_value_arr["jobSalary"]	=	txtSec($_POST["frmFName"]);
			$post_value_arr["jobPhone"]		=	txtSec($_POST["frmPhone"]);
			$post_value_arr["jobGender"]	=	txtSec($_POST["frmGender"]);
			$post_value_arr["proStartDate"]	=	$dateRange[0];
			$post_value_arr["proEndDate"]	=	$dateRange[1];
			
			if($_POST["editJobID"]){
				
				$post_value_arr["editID"]	=	txtSec($_POST["editJobID"]);
				$mainClass->valueClass = $post_value_arr;
				$mainClass->editClass("jobID");
			}
			else{	
				$mainClass->valueClass = $post_value_arr;
				$mainClass->addClass();
			}
		
		}
		else
			$returnUrl = "/jobs";
		
		
		header("location: ".$returnUrl);

		

		
	
	break;
}

?>