<?php
/** Блок нэмэх / засах модал формын өгөгдөл. */

$editBlockID = 0;
if (isset($_REQUEST["objID"])) {
	$editBlockID = (int)txtSec($_REQUEST["objID"]);
}

$regBlockTypes = RegistrationCore::blockTypes();

$blockType  = "";
$blockData  = array();
$blockOrder = RegistrationCore::nextOrder($db, $regTbl["block"], "blockOrder", "`parentID`=0");
$blockIsNew = true;

if ($editBlockID > 0) {
	$row = $db->rawQueryOne(
		"SELECT * FROM `" . $regTbl["block"] . "` WHERE `blockID`=?",
		array($editBlockID)
	);

	if (is_array($row) && count($row) > 0) {
		$blockIsNew = false;
		$blockType  = $row["blockType"];
		$blockData  = RegistrationCore::decode($row["blockData"]);
		$blockOrder = (int)$row["blockOrder"];
	} else {
		$editBlockID = 0;
	}
}

/* Шинэ блокийн төрөл — dropdown-оос эсвэл төрөл солих үед */
if ($blockIsNew) {
	if (isset($_REQUEST["type"]) && $_REQUEST["type"] != "") {
		$blockType = txtSec($_REQUEST["type"]);
	}
	if (isset($_REQUEST["frmBlockType"]) && $_REQUEST["frmBlockType"] != "") {
		$blockType = txtSec($_REQUEST["frmBlockType"]);
	}
}

if (!isset($regBlockTypes[$blockType])) {
	$blockTypeKeys = array_keys($regBlockTypes);
	$blockType = $blockTypeKeys[0];
}

$blockTypeObj = $regBlockTypes[$blockType];

$incPageUrl = $clkMenuModDir . "block.frm.php";
