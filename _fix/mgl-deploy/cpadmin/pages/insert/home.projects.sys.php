<?php

$widJsArr["homeProjects"] = $clkMenuModDir."home.projects.js.php";
$incPageUrl = $clkMenuModDir."home.projects.php";

$hpSlotCount = 12;
$hpHomeSch = null;
$hpSlots = array();
$hpCatalog = array();
$byId = array();
$allIds = array();

$db->where("pageType", "home");
$db->where("lang", $adminLang);
$db->where("menu_menu", 0);
$hpHomeMenu = $db->getOne($tbl_main_menu);

$db->join("$db_type B", "B.id=A.ceoType", "LEFT");
$db->join("$db_type C", "C.id=A.ceoCat", "LEFT");
$db->where("A.lang", $adminLang);
$db->orderBy("`ceoOrder`", "asc");
$db->orderBy("`ceoID`", "asc");
$hpAll = $db->get("$db_ceo A", null, "A.*,B.name brandName,C.name typeName");
if (!is_array($hpAll)) {
	$hpAll = array();
}

foreach ($hpAll as $obj) {
	$id = (int)$obj["ceoID"];
	$obj["_pic"] = newsPicFnc($id, $obj["ceoPic"]);
	$byId[$id] = $obj;
	$allIds[] = $id;
	$hpCatalog[] = array(
		"id" => $id,
		"name" => $obj["ceoName"],
		"brand" => isset($obj["brandName"]) ? $obj["brandName"] : "",
		"pic" => $obj["_pic"]
	);
}

if ($hpHomeMenu) {
	$db->where("schKey", (int)$hpHomeMenu["id"]);
	$db->where("schTemp", 6);
	$hpHomeSch = $db->getOne($db_pagesch);
}

$savedIds = array();
if ($hpHomeSch) {
	$note = json_decode($hpHomeSch["schNote"], true);
	if (is_array($note) && !empty($note["items"]) && is_array($note["items"])) {
		foreach ($note["items"] as $id) {
			$id = (int)$id;
			if ($id > 0 && isset($byId[$id]) && !in_array($id, $savedIds)) {
				$savedIds[] = $id;
			}
		}
	}
}

foreach ($allIds as $id) {
	if (count($savedIds) >= $hpSlotCount) {
		break;
	}
	if (!in_array($id, $savedIds)) {
		$savedIds[] = $id;
	}
}

for ($i = 0; $i < $hpSlotCount; $i++) {
	if (isset($savedIds[$i]) && isset($byId[$savedIds[$i]])) {
		$hpSlots[] = $byId[$savedIds[$i]];
	} else {
		$hpSlots[] = null;
	}
}

$hpCatalogJson = json_encode($hpCatalog, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
$hpSchId = $hpHomeSch ? (int)$hpHomeSch["schID"] : 0;
?>
