<?php
$objID = 0;

if(isset($_REQUEST["objID"])){
	
	$editID=txtSec($_REQUEST["objID"]);
	
	$db->where("adminGroupID", $editID);
	$selGroupObj = $db->getOne($tbl_admingroup);
	
	
	$accessArr = explode("-",$selGroupObj["adminGroupAction"]);
	
	if(count($accessArr)>0)
	foreach($accessArr as $key=>$obj){
		if($obj!=""){
			$objA = explode("_",$obj);
			$accessCArr[$objA[1]]=$objA[1];
		}
	}
				
}
?>