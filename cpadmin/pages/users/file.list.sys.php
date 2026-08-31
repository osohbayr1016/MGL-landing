<?php


$userID = txtSec($_REQUEST["userID"]);
		
$db->where("userID", $userID);
$selUserObj = $db->getOne($tbl_user);

		
$widJsArr["userlist"] = $clkMenuModDir."user.list.js.php";


$incPageUrl = $clkMenuModDir."file.list.php";


$db->orderBy("createDate","DESC");
$db->where("userID", $userID);
$fileArr = $db->get($db_files);
					
?>