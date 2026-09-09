<?php
/*
	Харилцагч байгууллагын лого.

	Өгөгдөл нь шинэ хүснэгт биш — сайт дээр аль хэдийн ажиллаж байгаа
	"Харилцагч" агуулгын хэсэг (db_pagesch, schTemp = 4) бөгөөд лого бүр
	нь түүний дэд мөр: schNote = {title, pic, link}. Тиймээс энэ хуудас
	одоо байгаа логонуудыг шууд гаргаж, зөвхөн засварлах хэрэгсэл нэмнэ.

	Харагдах тал: widgets/pagesch/wid4.php
*/

$widJsArr["clients"] = $clkMenuModDir."clients.js.php";
$incPageUrl = $clkMenuModDir."clients.php";

$clWidgetTemp = 4;

$clSections = array();
$clItems    = array();
$clNote     = array();
$clSelID    = 0;
$clSelName  = "";

$db->where("schTemp", $clWidgetTemp);
$db->where("parentID", "0");
$db->orderBy("`schID`", "asc");
$clSchArr = $db->get($db_pagesch);

if (!is_array($clSchArr)) {
	$clSchArr = array();
}

/* Хэсэг бүр аль хуудсанд байгааг нэрээр нь харуулна. */
$clMenuByID = array();
$clMenuIDs  = array();

foreach ($clSchArr as $obj) {
	$menuID = (int)$obj["schKey"];
	if ($menuID > 0 && !in_array($menuID, $clMenuIDs)) {
		$clMenuIDs[] = $menuID;
	}
}

if (count($clMenuIDs) > 0) {
	$db->where("id", $clMenuIDs, "IN");
	$clMenuArr = $db->get($tbl_main_menu, null, "id,name,lang");
	if (is_array($clMenuArr)) {
		foreach ($clMenuArr as $obj) {
			$clMenuByID[(int)$obj["id"]] = $obj;
		}
	}
}

foreach ($clSchArr as $obj) {
	$menuID = (int)$obj["schKey"];
	$menu   = isset($clMenuByID[$menuID]) ? $clMenuByID[$menuID] : null;

	/* Өөр хэлний хуудсыг харуулахгүй — админ хэлээ дээд талаас нь солино. */
	if ($menu && (string)$menu["lang"] !== (string)$adminLang) {
		continue;
	}

	$note = json_decode($obj["schNote"], true);
	if (!is_array($note)) {
		$note = array();
	}

	$title = isset($note["title"]) && $note["title"] !== "" ? $note["title"] : "Харилцагч";

	$clSections[] = array(
		"id"    => (int)$obj["schID"],
		"title" => $title,
		"page"  => $menu ? $menu["name"] : "",
		"note"  => $note
	);
}

if (isset($_REQUEST["objID"])) {
	$clSelID = (int)txtSec($_REQUEST["objID"]);
}

$clSelValid = false;
foreach ($clSections as $obj) {
	if ($obj["id"] === $clSelID) {
		$clSelValid = true;
	}
}

if (!$clSelValid) {
	$clSelID = count($clSections) > 0 ? $clSections[0]["id"] : 0;
}

foreach ($clSections as $obj) {
	if ($obj["id"] === $clSelID) {
		$clNote    = $obj["note"];
		$clSelName = $obj["title"];
	}
}

if ($clSelID > 0) {
	$db->where("parentID", $clSelID);
	$db->orderBy("`schOrder`", "asc");
	$db->orderBy("`schID`", "asc");
	$clSubArr = $db->get($db_pagesch);

	if (is_array($clSubArr)) {
		foreach ($clSubArr as $obj) {
			$note = json_decode($obj["schNote"], true);
			if (!is_array($note)) {
				$note = array();
			}

			$pic = isset($note["pic"]) ? $note["pic"] : "";

			$clItems[] = array(
				"id"     => (int)$obj["schID"],
				"title"  => isset($note["title"]) ? $note["title"] : "",
				"link"   => isset($note["link"]) ? $note["link"] : "",
				"pic"    => $pic,
				"picUrl" => $pic !== "" ? newsPicFnc(0, $pic) : ""
			);
		}
	}
}

/* Тохиргоо: аль аль нь бичигдээгүй бол асаалттай гэж үзнэ. */
$clHover  = !isset($clNote["hover"])  || $clNote["hover"]  != "0";
$clNewTab = !isset($clNote["newtab"]) || $clNote["newtab"] != "0";
?>
