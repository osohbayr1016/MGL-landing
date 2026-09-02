<?php
/** Формын талбар нэмэх / засах модал. */

$editFieldID = 0;
if (isset($_REQUEST["objID"])) {
	$editFieldID = (int)txtSec($_REQUEST["objID"]);
}

$regFieldTypes = RegistrationCore::fieldTypes();

$fieldObj = array(
	"fieldKey" => "", "fieldLabel" => "", "fieldType" => "text",
	"fieldPlaceholder" => "", "fieldHelp" => "", "fieldOptions" => "",
	"fieldRequired" => 0, "fieldWidth" => "full", "fieldCore" => "", "fieldStatus" => 1,
	"fieldOrder" => RegistrationCore::nextOrder($db, $regTbl["field"], "fieldOrder")
);

$fieldIsNew = true;

if ($editFieldID > 0) {
	$row = $db->rawQueryOne(
		"SELECT * FROM `" . $regTbl["field"] . "` WHERE `fieldID`=?",
		array($editFieldID)
	);

	if (is_array($row) && count($row) > 0) {
		$fieldIsNew = false;
		$fieldObj   = $row;
	} else {
		$editFieldID = 0;
	}
}

$incPageUrl = $clkMenuModDir . "field.frm.php";
