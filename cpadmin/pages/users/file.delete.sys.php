<?php

$deleteID = txtSec($_REQUEST["userID"]);

$db->where("id", $deleteID);
$delFileObj = $db->getOne($db_files);

$userID = $delFileObj["userID"];

$db->where("userID", $userID);
$selUserObj = $db->getOne($tbl_user);


$incPageUrl = $clkMenuModDir."file.delete.php";


$modTitle = $selUserObj["companyName"]." харилцагчийн файл устгах";
$modBtn = "danger";
$userActive = "d";

								
?>