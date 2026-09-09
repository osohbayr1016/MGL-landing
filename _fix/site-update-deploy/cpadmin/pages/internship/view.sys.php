<?php
/** Нэг хүсэлтийн дэлгэрэнгүй (modal). */

$applyViewID = 0;
if (isset($_REQUEST["objID"])) {
	$applyViewID = (int)txtSec($_REQUEST["objID"]);
}

$applyEntry = null;

if ($applyViewID > 0) {
	$row = $db->rawQueryOne(
		"SELECT * FROM `" . $applyTbl["entry"] . "` WHERE `entryID`=?",
		array($applyViewID)
	);

	if (is_array($row) && count($row) > 0) {
		$applyEntry = $row;
	}
}

if ($applyEntry !== null) {

	/* Нээж үзсэн бол "шинэ" төлвийг "үзсэн" болгоно */
	if ($applyEntry["entryState"] == "new") {
		$db->rawQuery(
			"UPDATE `" . $applyTbl["entry"] . "` SET `entryState`='read' WHERE `entryID`=?",
			array($applyViewID)
		);

		$applyEntry["entryState"] = "read";
	}

	$applyViewFields = ApplyCore::fields($db, $applyEntry["entryType"], false);
	$applyViewFiles  = ApplyCore::files($applyEntry);
	$applyViewExtra  = ApplyCore::decode($applyEntry["entryData"]);
}

$applyStates = ApplyCore::states();

$incPageUrl = $clkMenuModDir . "view.frm.php";
