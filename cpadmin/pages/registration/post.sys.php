<?php
/**
 * Арга хэмжээний бүртгэлийн модулийн бүх бичилт.
 * /userPost/registration руу ирсэн POST-ыг frmPost-оор нь ялгана.
 */

include __DIR__ . "/lib.php";

$sysReturnLink = "/registration/list";

/** Эрэмбийг нэгээр дээш/доош шилжүүлж, бүх мөрийг 1..N болгож дугаарлана. */
if (!function_exists("regMoveRow")) {
function regMoveRow($db, $table, $idCol, $orderCol, $itemID, $dir, $scopeSql, $scopeParams)
{
	$rows = $db->rawQuery(
		"SELECT `" . $idCol . "` FROM `" . $table . "` WHERE " . $scopeSql
			. " ORDER BY `" . $orderCol . "` ASC, `" . $idCol . "` ASC",
		$scopeParams
	);

	if (!is_array($rows)) {
		return;
	}

	$ids = array();
	foreach ($rows as $row) {
		$ids[] = (int)$row[$idCol];
	}

	$pos = array_search((int)$itemID, $ids, true);
	if ($pos === false) {
		return;
	}

	$swap = $dir == "up" ? $pos - 1 : $pos + 1;
	if ($swap < 0 || $swap >= count($ids)) {
		return;
	}

	$tmp = $ids[$pos];
	$ids[$pos] = $ids[$swap];
	$ids[$swap] = $tmp;

	$order = 1;
	foreach ($ids as $id) {
		$db->rawQuery(
			"UPDATE `" . $table . "` SET `" . $orderCol . "`=? WHERE `" . $idCol . "`=?",
			array($order, $id)
		);
		$order++;
	}
}

}

/** Талбарын түлхүүрийг цэвэрлэж, давхардвал дугаар нэмнэ. */
if (!function_exists("regUniqueFieldKey")) {
function regUniqueFieldKey($db, $table, $key, $label, $skipID = 0)
{
	$key = strtolower(trim((string)$key));
	$key = preg_replace('/[^a-z0-9_]/', "", $key);

	if ($key == "") {
		$key = preg_replace('/[^a-z0-9_]/', "", strtolower(str_replace(" ", "_", (string)$label)));
	}

	if ($key == "") {
		$key = "field" . ((int)RegistrationCore::scalar($db, "SELECT MAX(`fieldID`) FROM `" . $table . "`", null) + 1);
	}

	$base = $key;

	/* Давхардвал key2, key3 ... 50 оролдлогын дараа зогсоно (хязгааргүй давталтаас сэргийлнэ) */
	for ($i = 2; $i <= 50; $i++) {
		$exists = (int)RegistrationCore::scalar($db,
			"SELECT COUNT(*) FROM `" . $table . "` WHERE `fieldKey`=? AND `fieldID`<>?",
			array($key, (int)$skipID)
		);

		if ($exists < 1) {
			return $key;
		}

		$key = $base . $i;
	}

	return $key;
}
}

$regFrmPost = isset($_POST["frmPost"]) ? $_POST["frmPost"] : "";

/** $_POST-оос аюулгүй унших */
if (!function_exists("regPost")) {
function regPost($key, $def = "")
{
	return isset($_POST[$key]) ? $_POST[$key] : $def;
}
}

switch ($regFrmPost) {

	/* ---------------- Тохиргоо / загвар ---------------- */

	case "regSettings":
		if (is_array(regPost("frmSet", 0))) {
			RegistrationCore::saveSettings($db, regPost("frmSet", array()));
		}
		$sysReturnLink = "/registration/settings";
		break;

	case "regTheme":
		if (is_array(regPost("frmSet", 0))) {
			RegistrationCore::saveSettings($db, regPost("frmSet", array()));
		}
		$sysReturnLink = "/registration/design";
		break;

	/* ---------------- Үндсэн блок ---------------- */

	case "regBlock":
		$blockType = txtSec(regPost("frmBlockType"));
		if (RegistrationCore::blockTypeObj($blockType) == null) {
			$sysReturnLink = "/registration/design";
			break;
		}

		$values = is_array(regPost("frmVal", array())) ? regPost("frmVal", array()) : array();
		$order  = max(1, (int)txtSec(regPost("frmOrder", 1)));

		$saveArr = array(
			"blockData"  => json_encode($values, JSON_UNESCAPED_UNICODE),
			"blockOrder" => $order
		);

		if ((int)regPost("frmEditID", 0) > 0) {
			$blockID = (int)txtSec(regPost("frmEditID", 0));
			$db->where("blockID", $blockID);
			$db->update($regTbl["block"], $saveArr);
		} else {
			$saveArr["parentID"]    = 0;
			$saveArr["blockType"]   = $blockType;
			$saveArr["blockStatus"] = 1;
			$blockID = $db->insert($regTbl["block"], $saveArr);
		}

		if ($blockID > 0) {
			reorderScopedItem($db, $regTbl["block"], "blockID", "blockOrder", $blockID, $order, array("parentID" => 0));
		}

		$sysReturnLink = "/registration/design";
		break;

	case "regBlockDel":
		$delID = (int)txtSec(regPost("frmDelID", 0));

		$db->rawQuery("DELETE FROM `" . $regTbl["block"] . "` WHERE `blockID`=? OR `parentID`=?", array($delID, $delID));

		orderAjaxDone();
		break;

	case "regBlockToggle":
		$blockID = (int)txtSec(regPost("frmBlockID", 0));

		$db->rawQuery(
			"UPDATE `" . $regTbl["block"] . "` SET `blockStatus`=1-`blockStatus` WHERE `blockID`=?",
			array($blockID)
		);

		orderAjaxDone();
		break;

	case "regBlockMove":
		regMoveRow(
			$db, $regTbl["block"], "blockID", "blockOrder",
			(int)txtSec(regPost("frmBlockID", 0)), txtSec(regPost("frmDir")),
			"`parentID`=0", null
		);

		orderAjaxDone();
		break;

	/* ---------------- Дэд мөр ---------------- */

	case "regSubBlock":
		$parentID = (int)txtSec(regPost("frmParentID", 0));

		$parent = $db->rawQueryOne(
			"SELECT * FROM `" . $regTbl["block"] . "` WHERE `blockID`=?",
			array($parentID)
		);

		if (!is_array($parent) || count($parent) < 1) {
			$sysReturnLink = "/registration/design";
			break;
		}

		$values = is_array(regPost("frmVal", array())) ? regPost("frmVal", array()) : array();
		$order  = max(1, (int)txtSec(regPost("frmOrder", 1)));

		$saveArr = array(
			"blockData"  => json_encode($values, JSON_UNESCAPED_UNICODE),
			"blockOrder" => $order
		);

		if ((int)regPost("frmEditID", 0) > 0) {
			$subID = (int)txtSec(regPost("frmEditID", 0));
			$db->where("blockID", $subID);
			$db->update($regTbl["block"], $saveArr);
		} else {
			$saveArr["parentID"]    = $parentID;
			$saveArr["blockType"]   = $parent["blockType"];
			$saveArr["blockStatus"] = 1;
			$subID = $db->insert($regTbl["block"], $saveArr);
		}

		if ($subID > 0) {
			reorderScopedItem($db, $regTbl["block"], "blockID", "blockOrder", $subID, $order, array("parentID" => $parentID));
		}

		$sysReturnLink = "/registration/subList/" . $parentID;
		break;

	case "regSubDel":
		$delID = (int)txtSec(regPost("frmDelID", 0));

		$db->rawQuery("DELETE FROM `" . $regTbl["block"] . "` WHERE `blockID`=? AND `parentID`>0", array($delID));

		orderAjaxDone();
		break;

	case "regSubMove":
		$subID = (int)txtSec(regPost("frmBlockID", 0));

		$parentID = (int)RegistrationCore::scalar($db, 
			"SELECT `parentID` FROM `" . $regTbl["block"] . "` WHERE `blockID`=?",
			array($subID)
		);

		regMoveRow(
			$db, $regTbl["block"], "blockID", "blockOrder",
			$subID, txtSec(regPost("frmDir")),
			"`parentID`=?", array($parentID)
		);

		orderAjaxDone();
		break;

	/* ---------------- Формын талбар ---------------- */

	case "regField":
		$editID = (int)txtSec(regPost("frmEditID", 0));

		$existing = null;
		if ($editID > 0) {
			$existing = $db->rawQueryOne(
				"SELECT * FROM `" . $regTbl["field"] . "` WHERE `fieldID`=?",
				array($editID)
			);
		}

		$isCore = is_array($existing) && $existing["fieldCore"] != "";
		$order  = max(1, (int)txtSec(regPost("frmOrder", 1)));

		$saveArr = array(
			"fieldLabel"       => RegistrationCore::clean(regPost("frmLabel"), 190),
			"fieldPlaceholder" => RegistrationCore::clean(regPost("frmPlaceholder"), 190),
			"fieldHelp"        => RegistrationCore::clean(regPost("frmHelp"), 190),
			"fieldRequired"    => (int)regPost("frmRequired", 0) == 1 ? 1 : 0,
			"fieldWidth"       => in_array(regPost("frmWidth"), array("full", "half")) ? regPost("frmWidth") : "full",
			"fieldStatus"      => (int)regPost("frmStatus", 1) == 1 ? 1 : 0,
			"fieldOrder"       => $order
		);

		/* Үндсэн талбарын түлхүүр, төрөл өөрчлөгдөхгүй */
		if (!$isCore) {
			$types = RegistrationCore::fieldTypes();
			$type  = isset($types[regPost("frmType")]) ? regPost("frmType") : "text";

			$saveArr["fieldType"] = $type;
			$saveArr["fieldOptions"] = RegistrationCore::fieldHasOptions($type)
				? RegistrationCore::clean(regPost("frmOptions"), 2000)
				: "";
			$saveArr["fieldKey"] = regUniqueFieldKey(
				$db, $regTbl["field"],
				regPost("frmKey"),
				$saveArr["fieldLabel"],
				$editID
			);
		}

		if ($saveArr["fieldLabel"] == "") {
			$sysReturnLink = "/registration/fields";
			break;
		}

		if ($editID > 0 && is_array($existing)) {
			$db->where("fieldID", $editID);
			$db->update($regTbl["field"], $saveArr);
			$fieldID = $editID;
		} else {
			$saveArr["fieldCore"] = "";
			$fieldID = $db->insert($regTbl["field"], $saveArr);
		}

		if ($fieldID > 0) {
			reorderScopedItem($db, $regTbl["field"], "fieldID", "fieldOrder", $fieldID, $order, array());
		}

		$sysReturnLink = "/registration/fields";
		break;

	case "regFieldDel":
		$delID = (int)txtSec(regPost("frmDelID", 0));

		/* Үндсэн талбар (нэр/утас/и-мэйл) устгагдахгүй */
		$db->rawQuery(
			"DELETE FROM `" . $regTbl["field"] . "` WHERE `fieldID`=? AND (`fieldCore`='' OR `fieldCore` IS NULL)",
			array($delID)
		);

		orderAjaxDone();
		break;

	case "regFieldMove":
		regMoveRow(
			$db, $regTbl["field"], "fieldID", "fieldOrder",
			(int)txtSec(regPost("frmFieldID", 0)), txtSec(regPost("frmDir")),
			"1=1", null
		);

		orderAjaxDone();
		break;

	/* ---------------- Бүртгэл ---------------- */

	case "regEntryDel":
		$delID = (int)txtSec(regPost("frmDelID", 0));

		$db->rawQuery("DELETE FROM `" . $regTbl["entry"] . "` WHERE `entryID`=?", array($delID));

		die();
		break;
}
