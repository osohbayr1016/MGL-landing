<?php
/** Бүртгүүлсэн хүмүүсийн жагсаалт. */

$widJsArr["regList"] = $clkMenuModDir . "list.js.php";
$incPageUrl          = $clkMenuModDir . "list.php";

$regSet       = RegistrationCore::settings($db);
$regFieldsAll = RegistrationCore::fields($db, false);
$regStatus    = RegistrationCore::status($db, $regSet);

/* Хайлт */
$regQ = "";
if (isset($_REQUEST["q"])) {
	$regQ = RegistrationCore::clean($_REQUEST["q"], 100);
}

$regWhere  = "`entryStatus`=1";
$regParams = array();

if ($regQ != "") {
	$regWhere .= " AND (`entryName` LIKE ? OR `entryPhone` LIKE ? OR `entryEmail` LIKE ? OR `entryData` LIKE ?)";
	$like = "%" . $regQ . "%";
	$regParams = array($like, $like, $like, $like);
}

/* Хуудаслалт */
$regPerPage = 50;
$regPage    = isset($_REQUEST["p"]) ? (int)$_REQUEST["p"] : 1;
if ($regPage < 1) {
	$regPage = 1;
}

$regTotal = (int)RegistrationCore::scalar($db, 
	"SELECT COUNT(*) FROM `" . $regTbl["entry"] . "` WHERE " . $regWhere,
	count($regParams) > 0 ? $regParams : null
);

$regPageCount = $regTotal > 0 ? (int)ceil($regTotal / $regPerPage) : 1;
if ($regPage > $regPageCount) {
	$regPage = $regPageCount;
}
$regOffset = ($regPage - 1) * $regPerPage;

$regRows = $db->rawQuery(
	"SELECT * FROM `" . $regTbl["entry"] . "` WHERE " . $regWhere
		. " ORDER BY `entryID` DESC LIMIT " . (int)$regOffset . "," . (int)$regPerPage,
	count($regParams) > 0 ? $regParams : null
);
if (!is_array($regRows)) {
	$regRows = array();
}

/* Нийт бүртгэл (хайлтгүй) — багтаамжийн заалтад */
$regAllCount = RegistrationCore::entryCount($db);
$regLimit    = (int)$regSet["regLimit"];

/* Өнөөдрийн бүртгэл */
$regToday = (int)RegistrationCore::scalar($db, 
	"SELECT COUNT(*) FROM `" . $regTbl["entry"] . "` WHERE `entryStatus`=1 AND DATE(`entryDate`)=CURDATE()",
	null
);

/* Хүснэгтэд гаргах нэмэлт талбарууд (нэр/утас/имэйлээс бусад) */
$regExtraCols = array();
foreach ($regFieldsAll as $regFieldObj) {
	if ($regFieldObj["fieldCore"] == "") {
		$regExtraCols[] = $regFieldObj;
	}
}

$regPageLink = RegistrationCore::pageUrl($regSet);
