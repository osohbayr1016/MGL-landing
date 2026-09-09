<?php
/** Вэб сайтаар ирсэн өргөдлийн жагсаалт. */

$widJsArr["applyList"] = $clkMenuModDir . "list.js.php";
$incPageUrl            = $clkMenuModDir . "list.php";

$applySet    = ApplyCore::settings($db);
$applyTypes  = ApplyCore::types();
$applyStates = ApplyCore::states();

/* Хайлт ба шүүлтүүр */
$applyQ = "";
if (isset($_REQUEST["q"])) {
	$applyQ = ApplyCore::clean($_REQUEST["q"], 100);
}

$applyFType = "";
if (isset($_REQUEST["t"]) && isset($applyTypes[$_REQUEST["t"]])) {
	$applyFType = $_REQUEST["t"];
}

$applyFState = "";
if (isset($_REQUEST["s"]) && isset($applyStates[$_REQUEST["s"]])) {
	$applyFState = $_REQUEST["s"];
}

$applyWhere  = "`entryStatus`=1";
$applyParams = array();

if ($applyFType != "") {
	$applyWhere .= " AND `entryType`=?";
	$applyParams[] = $applyFType;
}

if ($applyFState != "") {
	$applyWhere .= " AND `entryState`=?";
	$applyParams[] = $applyFState;
}

if ($applyQ != "") {
	$applyWhere .= " AND (`entryName` LIKE ? OR `entryPhone` LIKE ? OR `entryEmail` LIKE ?"
		. " OR `entryPosition` LIKE ? OR `entryData` LIKE ?)";
	$like = "%" . $applyQ . "%";
	for ($i = 0; $i < 5; $i++) {
		$applyParams[] = $like;
	}
}

/* Хуудаслалт */
$applyPerPage = 50;
$applyPage    = isset($_REQUEST["p"]) ? (int)$_REQUEST["p"] : 1;
if ($applyPage < 1) {
	$applyPage = 1;
}

$applyTotal = (int)ApplyCore::scalar($db,
	"SELECT COUNT(*) FROM `" . $applyTbl["entry"] . "` WHERE " . $applyWhere,
	count($applyParams) > 0 ? $applyParams : null
);

$applyPageCount = $applyTotal > 0 ? (int)ceil($applyTotal / $applyPerPage) : 1;
if ($applyPage > $applyPageCount) {
	$applyPage = $applyPageCount;
}
$applyOffset = ($applyPage - 1) * $applyPerPage;

$applyRows = $db->rawQuery(
	"SELECT * FROM `" . $applyTbl["entry"] . "` WHERE " . $applyWhere
		. " ORDER BY `entryID` DESC LIMIT " . (int)$applyOffset . "," . (int)$applyPerPage,
	count($applyParams) > 0 ? $applyParams : null
);
if (!is_array($applyRows)) {
	$applyRows = array();
}

/* Тоон самбар */
$applyAllCount    = ApplyCore::entryCount($db);
$applyInternCount = ApplyCore::entryCount($db, "intern");
$applyJobCount    = ApplyCore::entryCount($db, "job");
$applyNewCount    = ApplyCore::entryCount($db, "", "new");

/* Хайлт/шүүлтийг линкэнд дамжуулах */
$applyQuery = "";
if ($applyQ != "") {
	$applyQuery .= "&q=" . urlencode($applyQ);
}
if ($applyFType != "") {
	$applyQuery .= "&t=" . urlencode($applyFType);
}
if ($applyFState != "") {
	$applyQuery .= "&s=" . urlencode($applyFState);
}
