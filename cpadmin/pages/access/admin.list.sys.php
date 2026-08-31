<?php

		
$widJsArr["adminlist"] = $clkMenuModDir."admin.list.js.php";


$incPageUrl = $clkMenuModDir."admin.list.php";
	
$db->join("$tbl_admingroup B", "B.adminGroupID = A.adminGroupID", "LEFT");
$adminArr = $db->get ("$tbl_prefadmin A", null, "A.*, B.adminGroupName");

		
								
?>