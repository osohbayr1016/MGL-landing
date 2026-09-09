<?php
/**
 * Дадлагын модулийн бүх бичилт.
 * /userPost/internship руу ирсэн POST-ыг frmPost-оор нь ялгана.
 */

include __DIR__ . "/lib.php";

$sysReturnLink = "/internship/list";

/** Талбарын түлхүүрийг цэвэрлэж, давхардвал дугаар нэмнэ. */
if (!function_exists("applyUniqueFieldKey")) {
	function applyUniqueFieldKey($db, $table, $key, $label, $skipID = 0)
	{
		$key = strtolower(trim((string)$key));
		$key = preg_replace('/[^a-z0-9_]/', "", $key);

		if ($key == "") {
			$key = preg_replace('/[^a-z0-9_]/', "", strtolower(str_replace(" ", "_", (string)$label)));
		}

		if ($key == "") {
			$key = "field" . ((int)ApplyCore::scalar($db, "SELECT MAX(`fieldID`) FROM `" . $table . "`", null) + 1);
		}

		$base = $key;

		/* Давхардвал key2, key3 ... 50 оролдлогын дараа зогсоно */
		for ($i = 2; $i <= 50; $i++) {
			$exists = (int)ApplyCore::scalar($db,
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

/** $_POST-оос аюулгүй унших */
if (!function_exists("applyPost")) {
	function applyPost($key, $def = "")
	{
		return isset($_POST[$key]) ? $_POST[$key] : $def;
	}
}

$applyFrmPost = isset($_POST["frmPost"]) ? $_POST["frmPost"] : "";

switch ($applyFrmPost) {

	/* ---------------- Тохиргоо ---------------- */

	case "applySettings":
		if (is_array(applyPost("frmSet", 0))) {
			ApplyCore::saveSettings($db, applyPost("frmSet", array()));
		}

		$sysReturnLink = "/internship/settings";
		break;

	/* ---------------- Формын асуулт ---------------- */

	case "applyField":
		$editID = (int)txtSec(applyPost("frmEditID", 0));

		$existing = null;
		if ($editID > 0) {
			$existing = $db->rawQueryOne(
				"SELECT * FROM `" . $applyTbl["field"] . "` WHERE `fieldID`=?",
				array($editID)
			);
		}

		$isCore = is_array($existing) && $existing["fieldCore"] != "";
		$order  = max(1, (int)txtSec(applyPost("frmOrder", 1)));
		$forArr = ApplyCore::fieldForTypes();

		$saveArr = array(
			"fieldLabel"       => ApplyCore::clean(applyPost("frmLabel"), 190),
			"fieldPlaceholder" => ApplyCore::clean(applyPost("frmPlaceholder"), 190),
			"fieldHelp"        => ApplyCore::clean(applyPost("frmHelp"), 190),
			"fieldRequired"    => (int)applyPost("frmRequired", 0) == 1 ? 1 : 0,
			"fieldWidth"       => in_array(applyPost("frmWidth"), array("full", "half")) ? applyPost("frmWidth") : "full",
			"fieldFor"         => isset($forArr[applyPost("frmFor")]) ? applyPost("frmFor") : "both",
			"fieldStatus"      => (int)applyPost("frmStatus", 1) == 1 ? 1 : 0,
			"fieldOrder"       => $order
		);

		/* Үндсэн талбарын түлхүүр, төрөл өөрчлөгдөхгүй */
		if (!$isCore) {
			$types = ApplyCore::fieldTypes();
			$type  = isset($types[applyPost("frmType")]) ? applyPost("frmType") : "text";

			$saveArr["fieldType"]    = $type;
			$saveArr["fieldOptions"] = ApplyCore::fieldHasOptions($type)
				? ApplyCore::clean(applyPost("frmOptions"), 2000)
				: "";
			$saveArr["fieldKey"] = applyUniqueFieldKey(
				$db, $applyTbl["field"],
				applyPost("frmKey"),
				$saveArr["fieldLabel"],
				$editID
			);
		}

		if ($saveArr["fieldLabel"] == "") {
			$sysReturnLink = "/internship/fields";
			break;
		}

		if ($editID > 0 && is_array($existing)) {
			$db->where("fieldID", $editID);
			$db->update($applyTbl["field"], $saveArr);
			$fieldID = $editID;
		} else {
			$saveArr["fieldCore"] = "";
			$fieldID = $db->insert($applyTbl["field"], $saveArr);
		}

		if ($fieldID > 0) {
			applyReorder($db, $applyTbl["field"], "fieldID", "fieldOrder", $fieldID, $order);
		}

		$sysReturnLink = "/internship/fields";
		break;

	case "applyFieldDel":
		$delID = (int)txtSec(applyPost("frmDelID", 0));

		/* Үндсэн талбар (нэр/утас/и-мэйл/ажлын байр) устгагдахгүй */
		$db->rawQuery(
			"DELETE FROM `" . $applyTbl["field"] . "` WHERE `fieldID`=? AND (`fieldCore`='' OR `fieldCore` IS NULL)",
			array($delID)
		);

		applyAjaxDone();
		break;

	case "applyFieldMove":
		applyMoveRow(
			$db, $applyTbl["field"], "fieldID", "fieldOrder",
			(int)txtSec(applyPost("frmFieldID", 0)), txtSec(applyPost("frmDir"))
		);

		applyAjaxDone();
		break;

	/* ---------------- Хүсэлт ---------------- */

	case "applyEntryState":
		$entryID = (int)txtSec(applyPost("frmEntryID", 0));
		$states  = ApplyCore::states();
		$state   = applyPost("frmState", "new");

		if ($entryID > 0 && isset($states[$state])) {
			$db->rawQuery(
				"UPDATE `" . $applyTbl["entry"] . "` SET `entryState`=?, `entryNote`=? WHERE `entryID`=?",
				array($state, ApplyCore::clean(applyPost("frmNote"), 2000), $entryID)
			);
		}

		if (!empty($_POST["ajaxOrder"])) {
			applyAjaxDone();
		}

		$sysReturnLink = "/internship/list";
		break;

	case "applyEntryDel":
		ApplyCore::deleteEntry($db, (int)txtSec(applyPost("frmDelID", 0)));

		applyAjaxDone();
		break;
}
