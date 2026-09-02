<?php
/** Блокийн дэд мөрүүд (мэдээллийн мөр, цомгийн зураг г.м). */

$subParentID = 0;
if (isset($_REQUEST["objID"])) {
	$subParentID = (int)txtSec($_REQUEST["objID"]);
}

$subParent = $db->rawQueryOne(
	"SELECT * FROM `" . $regTbl["block"] . "` WHERE `blockID`=? AND `parentID`=0",
	array($subParentID)
);

if (!is_array($subParent) || count($subParent) < 1) {
	header("location: /registration/design");
	exit;
}

$subTypeObj = RegistrationCore::blockTypeObj($subParent["blockType"]);

if ($subTypeObj == null || empty($subTypeObj["subCols"])) {
	header("location: /registration/design");
	exit;
}

$subRows = $db->rawQuery(
	"SELECT * FROM `" . $regTbl["block"] . "` WHERE `parentID`=? ORDER BY `blockOrder` ASC, `blockID` ASC",
	array($subParentID)
);
if (!is_array($subRows)) {
	$subRows = array();
}

$widJsArr["regSub"] = $clkMenuModDir . "sub.list.js.php";
$incPageUrl         = $clkMenuModDir . "sub.list.php";
