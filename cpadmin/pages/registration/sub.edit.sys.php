<?php
/** Дэд мөр нэмэх / засах модал. */

$editSubID = 0;
if (isset($_REQUEST["objID"])) {
	$editSubID = (int)txtSec($_REQUEST["objID"]);
}

$subParentID = 0;
if (isset($_REQUEST["parent"])) {
	$subParentID = (int)txtSec($_REQUEST["parent"]);
}

$subData  = array();
$subIsNew = true;

if ($editSubID > 0) {
	$row = $db->rawQueryOne(
		"SELECT * FROM `" . $regTbl["block"] . "` WHERE `blockID`=?",
		array($editSubID)
	);

	if (is_array($row) && count($row) > 0) {
		$subIsNew    = false;
		$subData     = RegistrationCore::decode($row["blockData"]);
		$subParentID = (int)$row["parentID"];
		$subOrder    = (int)$row["blockOrder"];
	} else {
		$editSubID = 0;
	}
}

$subParent = $db->rawQueryOne(
	"SELECT * FROM `" . $regTbl["block"] . "` WHERE `blockID`=?",
	array($subParentID)
);

if (!is_array($subParent) || count($subParent) < 1) {
	die("Эх блок олдсонгүй.");
}

$subTypeObj = RegistrationCore::blockTypeObj($subParent["blockType"]);
if ($subTypeObj == null || empty($subTypeObj["subCols"])) {
	die("Энэ блок дэд мөр дэмждэггүй.");
}

if ($subIsNew) {
	$subOrder = RegistrationCore::nextOrder($db, $regTbl["block"], "blockOrder", "`parentID`=" . (int)$subParentID);
}

$incPageUrl = $clkMenuModDir . "sub.frm.php";
