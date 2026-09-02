<?php

require_once __DIR__ . "/ceo-order.php";

$schSchBody = json_decode($objs["schNote"], true);
if (!is_array($schSchBody)) {
	$schSchBody = array();
}

$homeProjectLimit = 12;
$itemIds = array();

if (!empty($schSchBody["items"]) && is_array($schSchBody["items"])) {
	foreach ($schSchBody["items"] as $id) {
		$id = (int)$id;
		if ($id > 0) {
			$itemIds[] = $id;
		}
		if (count($itemIds) >= $homeProjectLimit) {
			break;
		}
	}
}

if (count($itemIds) > 0) {
	$db->join("$db_type B", "B.id=A.ceoType", "LEFT");
	$db->join("$db_type C", "C.id=A.ceoCat", "LEFT");
	$db->where("A.lang", $gloLang);
	$db->where("A.ceoID", $itemIds, "IN");
	$rows = $db->get("$db_ceo A", null, "A.*,B.name brandName,C.name typeName");
	$byId = array();
	if (is_array($rows)) {
		foreach ($rows as $row) {
			$byId[(int)$row["ceoID"]] = $row;
		}
	}
	$ordered = array();
	foreach ($itemIds as $id) {
		if (isset($byId[$id])) {
			$ordered[] = $byId[$id];
		}
	}
	$workWidArr[$objs["schID"]] = array_slice($ordered, 0, $homeProjectLimit);
} else {
	$rows = fetchCeoProjectsForLang($db, $db_ceo, $db_type, $gloLang);
	$rows = sortCeoRowsByOrder($rows);
	$workWidArr[$objs["schID"]] = array_slice($rows, 0, $homeProjectLimit);
}

if (!is_array($workWidArr[$objs["schID"]])) {
	$workWidArr[$objs["schID"]] = array();
}

?>
