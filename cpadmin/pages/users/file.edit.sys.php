<?php

$editID = txtSec($_REQUEST["userID"]);


$incPageUrl = $clkMenuModDir."file.add.php";
	
		
$db->where("id", $editID);
$editFileObj = $db->getOne($db_files);

$userID = $editFileObj["userID"];

$db->where("userID", $userID);
$selUserObj = $db->getOne($tbl_user);



?>