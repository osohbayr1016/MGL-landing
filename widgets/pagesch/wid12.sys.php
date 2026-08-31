<?php

require_once __DIR__ . "/ceo-order.php";

$schSchBody = json_decode($objs["schNote"], true);
if (!is_array($schSchBody)) {
	$schSchBody = array();
}

$db->where("lang", $gloLang);
$db->where("type", "pro");
$db->orderBy("`order`", "asc");
$workLocArr = $db->get($db_type);

$db->where("lang", $gloLang);
$db->where("type", "protype");
$db->orderBy("`order`", "asc");
$workTypeArr = $db->get($db_type);

$db->where("lang", $gloLang);
$db->where("type", "person");
$db->orderBy("`order`", "asc");
$workSecArr = $db->get($db_type);

$db->where("lang", $gloLang);
$db->orderBy("`ceoStatus`", "asc");
$db->groupBy("ceoStatus");
$workStatusArr = $db->get($db_ceo, null, "ceoStatus");

$projectRows = fetchCeoProjectsForLang($db, $db_ceo, $db_type, $gloLang);
$workWidArr[$objs["schID"]] = sortCeoRowsByOrder($projectRows);

$widJsArr["pagesch"] = $gloConstWidDir . "pagesch/filter.js.php";

?>
