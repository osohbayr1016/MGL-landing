<?php

$userID = txtSec($_REQUEST["userID"]);
		
$db->where("userID", $userID);
$selUserObj = $db->getOne($tbl_user);


$incPageUrl = $clkMenuModDir."file.add.php";
	

								
?>