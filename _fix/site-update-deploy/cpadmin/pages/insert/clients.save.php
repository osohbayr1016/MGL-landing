<?php
/*
	Харилцагчийн жагсаалтыг бүхэлд нь хадгална.

	Мөр бүр db_pagesch-ийн дэд бичлэг тул хуучин мөрийг ID-гаар нь шинэчилж,
	шинийг нь нэмж, жагсаалтаас хассаныг устгана. schNote доторх бусад
	түлхүүрийг хэвээр нь үлдээнэ.
*/

$clSchID = (int)txtSec($_POST["schID"]);

if ($clSchID < 1) {
	orderAjaxDone(array("ok" => 0, "error" => "Хэсэг сонгогдоогүй байна."));
}

$db->where("schID", $clSchID);
$clSection = $db->getOne($db_pagesch);

if (!$clSection || (int)$clSection["parentID"] !== 0) {
	orderAjaxDone(array("ok" => 0, "error" => "Хэсэг олдсонгүй."));
}

$clPosted = isset($_POST["items"]) ? json_decode($_POST["items"], true) : array();

if (!is_array($clPosted)) {
	$clPosted = array();
}

/* Хэсгийн тохиргоо */
$clNote = json_decode($clSection["schNote"], true);

if (!is_array($clNote)) {
	$clNote = array();
}

$clNote["title"]  = isset($_POST["frmTitle"]) ? trim($_POST["frmTitle"]) : "";
$clNote["hover"]  = (isset($_POST["frmHover"])  && $_POST["frmHover"]  == "1") ? "1" : "0";
$clNote["newtab"] = (isset($_POST["frmNewTab"]) && $_POST["frmNewTab"] == "1") ? "1" : "0";

$db->where("schID", $clSchID);
$db->update($db_pagesch, array("schNote" => json_encode($clNote, JSON_UNESCAPED_UNICODE)));

/* Одоо байгаа дэд мөрүүд */
$clOldIDs = array();

$db->where("parentID", $clSchID);
$clOldArr = $db->get($db_pagesch, null, "schID, schNote");

if (is_array($clOldArr)) {
	foreach ($clOldArr as $obj) {
		$clOldIDs[(int)$obj["schID"]] = $obj["schNote"];
	}
}

$clKeepIDs = array();
$clOrder   = 1;

foreach ($clPosted as $obj) {
	if (!is_array($obj)) {
		continue;
	}

	$itemID = isset($obj["id"]) ? (int)$obj["id"] : 0;
	$pic    = isset($obj["pic"]) ? trim($obj["pic"]) : "";
	$title  = isset($obj["title"]) ? trim($obj["title"]) : "";
	$link   = isset($obj["link"]) ? trim($obj["link"]) : "";

	if ($pic == "" && $title == "") {
		continue;
	}

	/* Хуучин мөрийн бусад түлхүүрийг хадгалж үлдээнэ. */
	$note = array();

	if ($itemID > 0 && isset($clOldIDs[$itemID])) {
		$note = json_decode($clOldIDs[$itemID], true);
		if (!is_array($note)) {
			$note = array();
		}
	}

	$note["title"] = $title;
	$note["pic"]   = $pic;
	$note["link"]  = $link;

	$rowArr = array(
		"schNote"  => json_encode($note, JSON_UNESCAPED_UNICODE),
		"schOrder" => $clOrder
	);

	if ($itemID > 0 && isset($clOldIDs[$itemID])) {
		$db->where("schID", $itemID);
		$db->update($db_pagesch, $rowArr);
	} else {
		$rowArr["schKey"]   = $clSection["schKey"];
		$rowArr["parentID"] = $clSchID;
		$itemID = $db->insert($db_pagesch, $rowArr);
	}

	if ($itemID > 0) {
		$clKeepIDs[] = (int)$itemID;
	}

	$clOrder++;
}

/* Жагсаалтаас хассан мөрүүд */
foreach ($clOldIDs as $oldID => $oldNote) {
	if (in_array((int)$oldID, $clKeepIDs)) {
		continue;
	}

	$db->where("schID", (int)$oldID);
	$db->delete($db_pagesch);
}

orderAjaxDone(array("count" => count($clKeepIDs)));
