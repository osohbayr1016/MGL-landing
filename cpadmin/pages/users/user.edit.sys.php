<?php

$editID = txtSec($_REQUEST["userID"]);
		
$widJsArr["useradd"] = $clkMenuModDir."user.add.js.php";


$incPageUrl = $clkMenuModDir."user.add.php";
	
		
$db->where("userID", $editID);
$editUserObj = $db->getOne($tbl_user);

								
?>