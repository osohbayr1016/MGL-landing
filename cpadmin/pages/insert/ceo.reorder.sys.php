<?php

include_once __DIR__ . "/order.helper.php";

$ceoIDs = isset($_POST["ceoIDs"]) ? $_POST["ceoIDs"] : array();
$order = 1;

if (is_array($ceoIDs)) {
	foreach ($ceoIDs as $ceoID) {
		$ceoID = (int)$ceoID;
		if ($ceoID < 1) {
			continue;
		}

		$db->where("ceoID", $ceoID);
		$db->where("lang", $adminLang);
		$db->update($db_ceo, array("ceoOrder" => $order));
		$order++;
	}
}

if (!empty($_POST["ajaxOrder"])) {
	orderAjaxDone();
}

$pageID = (int)txtSec($_POST["pageID"]);
$sysReturnLink = "/insert/promo";
if ($pageID > 0) {
	$sysReturnLink = "/insert/promo/" . $pageID;
}
