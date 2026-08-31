<?php


		
$widJsArr["userlist"] = $clkMenuModDir."user.list.js.php";


$incPageUrl = $clkMenuModDir."users.list.php";


$db->orderBy("createDate","DESC");
$usersArr = $db->get($tbl_user);
					
?>