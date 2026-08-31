<?php

$ceoIDs = isset($_POST["ceoIDs"]) ? $_POST["ceoIDs"] : array();
$order = 1;

if(is_array($ceoIDs)){
	foreach($ceoIDs as $ceoID){
		$ceoID = (int)$ceoID;
		if($ceoID < 1) continue;

		$db->where("ceoID", $ceoID);
		$db->where("lang", $adminLang);
		$db->update($db_ceo, array("ceoOrder" => $order));
		$order++;
	}
}

$sysReturnLink = "/insert/promo";
