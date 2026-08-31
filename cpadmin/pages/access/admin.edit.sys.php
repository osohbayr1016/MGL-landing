<?php

if(isset($_REQUEST["objID"])){
	
	$editID=txtSec($_REQUEST["objID"]);
	
	
	$db->where("id", $editID);
	$selAdminObj = $db->getOne($tbl_prefadmin);

				
}


$groupArr = $db->get($tbl_admingroup);
?>