<?php

$userID = txtSec($_REQUEST["userID"]);

$incPageUrl = $clkMenuModDir."user.confrim.php";
	
$db->where("userID", $userID);
$userObj = $db->getOne($tbl_user);


$isDelete = false;

$modTitle = "Харилцагчийг устгах уу";
$modBtn = "danger";
$userActive = "d";

$db->where("userID", $userID);
$isUserFileCount = $db->getValue($db_files,"count(*)");

if($isUserFileCount<1)
    $isDelete = true;
								
?>