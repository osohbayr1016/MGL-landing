<?php
/**
 * Ирсэн хүсэлтийг Excel (.xlsx) эсвэл CSV болгон татаж авах.
 *
 * Баганууд нь "Формын асуулт" хэсэгт тодорхойлсон талбаруудаас
 * автоматаар үүсдэг — шинэ асуулт нэмэхэд Excel-д ч шууд нэмэгдэнэ.
 */

include __DIR__ . "/lib.php";

$expType = isset($_REQUEST["type"]) ? txtSec($_REQUEST["type"]) : "xlsx";

$expQ = "";
if (isset($_REQUEST["q"])) {
	$expQ = ApplyCore::clean($_REQUEST["q"], 100);
}

$expTypes  = ApplyCore::types();
$expStates = ApplyCore::states();

$expFType = "";
if (isset($_REQUEST["t"]) && isset($expTypes[$_REQUEST["t"]])) {
	$expFType = $_REQUEST["t"];
}

$expFState = "";
if (isset($_REQUEST["s"]) && isset($expStates[$_REQUEST["s"]])) {
	$expFState = $_REQUEST["s"];
}

$expWhere  = "`entryStatus`=1";
$expParams = array();

if ($expFType != "") {
	$expWhere .= " AND `entryType`=?";
	$expParams[] = $expFType;
}

if ($expFState != "") {
	$expWhere .= " AND `entryState`=?";
	$expParams[] = $expFState;
}

if ($expQ != "") {
	$expWhere .= " AND (`entryName` LIKE ? OR `entryPhone` LIKE ? OR `entryEmail` LIKE ?"
		. " OR `entryPosition` LIKE ? OR `entryData` LIKE ?)";
	$like = "%" . $expQ . "%";
	for ($i = 0; $i < 5; $i++) {
		$expParams[] = $like;
	}
}

$expRows = $db->rawQuery(
	"SELECT * FROM `" . $applyTbl["entry"] . "` WHERE " . $expWhere . " ORDER BY `entryID` ASC",
	count($expParams) > 0 ? $expParams : null
);
if (!is_array($expRows)) {
	$expRows = array();
}

$expFields = ApplyCore::fields($db, "", false);
$expCols   = ApplyCore::exportColumns($expFields);

$expTitle = "Өргөдөл";
$expName  = "huselt-" . date("Y-m-d-Hi");

/* ------------------------------------------------------------------
   CSV — Excel дээр шууд нээгдэхийн тулд UTF-8 BOM тавина
   ------------------------------------------------------------------ */
if ($expType == "csv") {

	while (ob_get_level() > 0) {
		ob_end_clean();
	}

	header("Content-Type: text/csv; charset=UTF-8");
	header("Content-Disposition: attachment; filename=\"" . $expName . ".csv\"");
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Pragma: no-cache");

	echo "\xEF\xBB\xBF";

	$out = fopen("php://output", "w");
	fputcsv($out, $expCols);

	$i = 0;
	foreach ($expRows as $entry) {
		$i++;
		$row = ApplyCore::exportRow($entry, $expFields, $i);

		/*
		   Хоёр зүйлээс хамгаална:
		   1) Утасны дугаарын эхний 0 арилах (Excel тоо болгож хувиргадаг)
		   2) CSV formula injection — "=", "+", "-", "@"-аар эхэлсэн утга
		*/
		foreach ($row as $k => $cell) {
			if (!is_string($cell) || $cell === "") {
				continue;
			}

			$isPhone   = preg_match('/^[0-9+][0-9\s+()-]{5,}$/', $cell);
			$isFormula = in_array(substr($cell, 0, 1), array("=", "+", "-", "@", "\t"));

			if ($isPhone || $isFormula) {
				$row[$k] = '="' . str_replace('"', '""', $cell) . '"';
			}
		}

		fputcsv($out, $row);
	}

	fclose($out);
	exit;
}

/* ------------------------------------------------------------------
   XLSX — жинхэнэ Excel файл
   ------------------------------------------------------------------ */
if (!class_exists("XlsxWriter")) {
	die("class/xlsx.writer.class.php олдсонгүй.");
}

$xlsx = new XlsxWriter($expTitle);

/* Баганын өргөн: № нарийн, нэр/и-мэйл өргөн, бусад дунд */
$widths = array(6, 14);
foreach ($expFields as $field) {
	switch ($field["fieldCore"]) {
		case "name":
			$widths[] = 28;
			break;
		case "phone":
			$widths[] = 16;
			break;
		case "email":
			$widths[] = 30;
			break;
		default:
			$widths[] = $field["fieldType"] == "textarea" ? 40 : 22;
			break;
	}
}
$widths[] = 14;
$widths[] = 30;
$widths[] = 20;

$xlsx->setColumns($widths);
$xlsx->addHeader($expCols);

$i = 0;
foreach ($expRows as $entry) {
	$i++;
	$xlsx->addRow(ApplyCore::exportRow($entry, $expFields, $i));
}

$xlsx->download($expName . ".xlsx");
exit;
