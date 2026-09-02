<?php

$schID = (int)txtSec($_POST["schID"]);
$rawItems = isset($_POST["items"]) ? $_POST["items"] : array();
$ids = array();

if (is_array($rawItems)) {
	foreach ($rawItems as $id) {
		$id = (int)$id;
		if ($id > 0 && !in_array($id, $ids)) {
			$ids[] = $id;
		}
		if (count($ids) >= 12) {
			break;
		}
	}
}

if ($schID < 1) {
	orderAjaxDone(array("ok" => 0, "error" => "missing"));
}

$db->where("schID", $schID);
$row = $db->getOne($db_pagesch, "schID, schNote");
if (!$row) {
	orderAjaxDone(array("ok" => 0, "error" => "not found"));
}

$note = json_decode($row["schNote"], true);
if (!is_array($note)) {
	$note = array();
}
$note["count"] = 12;
$note["items"] = $ids;
if (empty($note["title"])) {
	$note["title"] = "Төслүүд";
}

$db->where("schID", $schID);
$db->update($db_pagesch, array("schNote" => json_encode($note, JSON_UNESCAPED_UNICODE)));

if (!empty($_POST["pics"]) && is_array($_POST["pics"])) {
	foreach ($_POST["pics"] as $ceoID => $pic) {
		$ceoID = (int)$ceoID;
		if ($ceoID < 1 || $pic === "") {
			continue;
		}
		$db->where("ceoID", $ceoID);
		$db->update($db_ceo, array("ceoPic" => txtSec($pic)));
	}
}

orderAjaxDone(array("count" => count($ids)));
