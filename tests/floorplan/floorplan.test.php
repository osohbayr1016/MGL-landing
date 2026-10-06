<?php
/**
 * Run: php tests/floorplan/floorplan.test.php
 * Pure-PHP checks of class/floorplan.class.php (validation, defaults, fallback).
 * SQL (install/save) is covered by tests/floorplan/db.test.php against a real MySQL/MariaDB.
 */

require __DIR__ . "/../../class/floorplan.class.php";

$fail = 0;
$count = 0;

function check($cond, $msg)
{
	global $fail, $count;
	$count++;
	if (!$cond) {
		$fail++;
		echo "FAIL: " . $msg . "\n";
	}
}

/* ---- defaults: 20th floor (left building) + 21st floor (right building) ---- */
$def = FloorPlanCore::defaults();
check(count($def) === 2 && $def[0]["plan"]["key"] === "office-20f" && $def[1]["plan"]["key"] === "office-21f", "two floors, 20 below 21");
check(count($def[0]["hotspots"]) === 11 && count($def[1]["hotspots"]) === 5, "11 hotspots on the 20th, 5 on the 21st");
check($def[0]["plan"]["floorTitle"] === "20-Р ДАВХАР" && $def[1]["plan"]["floorTitle"] === "21-Р ДАВХАР", "floor titles");

$slugs = array();
foreach ($def as $floor) {
	$p = $floor["plan"];
	check(is_file(__DIR__ . "/../.." . $p["imageUrl"]) && is_file(__DIR__ . "/../.." . $p["imageMidUrl"]) && is_file(__DIR__ . "/../.." . $p["imageFullUrl"]), $p["key"] . ": all three image tiers exist");
	$sz = getimagesize(__DIR__ . "/../.." . $p["imageFullUrl"]);
	check($sz && $sz[0] === $p["imageWidth"] && $sz[1] === $p["imageHeight"], $p["key"] . ": imageWidth/Height match the full image");
	check($p["link"]["x"] > 0 && $p["link"]["x"] < 1 && $p["link"]["y"] > 0 && $p["link"]["y"] < 1, $p["key"] . ": staircase inside the image");
	check($p["bounds"][2] > $p["bounds"][0] && $p["bounds"][3] > $p["bounds"][1], $p["key"] . ": bounds valid");
	foreach ($floor["hotspots"] as $h) {
		check(!isset($slugs[$h["slug"]]), "slug unique across floors: " . $h["slug"]);
		$slugs[$h["slug"]] = true;
		check($h["x"] >= $p["bounds"][0] && $h["x"] <= $p["bounds"][2] && $h["y"] >= $p["bounds"][1] && $h["y"] <= $p["bounds"][3], "hotspot inside its floor's drawing: " . $h["slug"]);
		check($h["titleEn"] !== "" && $h["titleMn"] !== "", "titles " . $h["slug"]);
		check($h["descriptionMn"] !== "", "description " . $h["slug"]);
		check($h["descriptionEn"] !== "", "English description " . $h["slug"]);
		check(in_array($h["kind"], FloorPlanCore::kinds(), true), "kind " . $h["slug"]);
		check(FloorPlanCore::sanitizeHotspot($h, 0) !== null, "default passes validation " . $h["slug"]);
	}
}
check(count($slugs) === 16, "16 hotspots in total");
check(isset($slugs["terrace-west"]) && isset($slugs["terrace-east"]), "both terraces are separate");

/* ---- PHP 7.2: a NUL byte inside a regex pattern is an error there (PHP 8.2 tolerates it) ---- */
foreach (array("class/floorplan.class.php", "widgets/pagesch/floorplan.hero.php", "widgets/pagesch/temp.php",
	"cpadmin/pages/floorplan/lib.php", "cpadmin/pages/floorplan/post.sys.php", "cpadmin/pages/floorplan/edit.sys.php",
	"cpadmin/pages/floorplan/edit.php", "cpadmin/pages/floorplan/asset.php", "cpadmin/pages/floorplan/sys.php") as $src) {
	$bytes = file_get_contents(__DIR__ . "/../../" . $src);
	check(!preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $bytes), $src . ": no raw control bytes in the source");
}

/* ---- sanitizeHotspot ---- */
$s = FloorPlanCore::sanitizeHotspot(array(
	"slug" => " Locker-Room ", "x" => 1.7, "y" => "-3", "titleEn" => "  Locker  ",
	"desktop" => array("zoom" => 100, "offsetX" => 9, "offsetY" => -9, "focusX" => 5, "focusY" => ""),
	"mobile"  => array("zoom" => "abc", "offsetX" => "NaN")
), 3);
check($s !== null, "valid hotspot accepted");
check($s["slug"] === "locker-room", "slug cleaned");
check($s["x"] === 1.0 && $s["y"] === 0.0, "coords clamped");
check($s["titleEn"] === "Locker", "title trimmed");
check($s["desktop"]["zoom"] === FloorPlanCore::ZOOM_MAX, "zoom 100 clamped to max");
check($s["desktop"]["offsetX"] === FloorPlanCore::OFFSET_MAX && $s["desktop"]["offsetY"] === -FloorPlanCore::OFFSET_MAX, "offsets clamped");
check($s["desktop"]["focusX"] === 1.0 && $s["desktop"]["focusY"] === null, "focus clamped / empty -> null");
check($s["mobile"]["zoom"] === 7.5, "garbage zoom falls back to default");
check($s["mobile"]["offsetX"] === 0.0, "garbage offset -> 0");
check($s["descriptionMn"] === "" && $s["descriptionEn"] === "", "missing descriptions ok");
check(FloorPlanCore::sanitizeHotspot(array("slug" => "a", "x" => 0.1, "y" => 0.1, "descriptionEn" => str_repeat("e", 900)), 0)["descriptionEn"] === str_repeat("e", 300), "English description bounded to 300");
check($s["order"] === 3, "order falls back to index");

check(FloorPlanCore::sanitizeHotspot(array("slug" => "a", "x" => "nope", "y" => 0.5), 0) === null, "non-numeric x rejected");
check(FloorPlanCore::sanitizeHotspot(array("slug" => "a", "x" => 0.5), 0) === null, "missing y rejected");
check(FloorPlanCore::sanitizeHotspot(array("slug" => "!!", "x" => 0.5, "y" => 0.5), 0) === null, "slug with no valid chars rejected");
check(FloorPlanCore::sanitizeHotspot(array("slug" => "a", "x" => INF, "y" => 0.5), 0) === null, "INF rejected");
check(FloorPlanCore::sanitizeHotspot("string", 0) === null, "non-array rejected");
check(FloorPlanCore::sanitizeHotspot(array("slug" => "a", "x" => 0.5, "y" => 0.5, "kind" => "hacker"), 0)["kind"] === "other", "unknown kind -> other");

/* ---- text ---- */
check(preg_match_all("/./us", FloorPlanCore::cleanText(str_repeat("Х", 500), 120)) === 120, "Cyrillic cut by characters, not bytes");
check(FloorPlanCore::cleanText("ab" . chr(255) . chr(254) . "cd", 10) === "", "invalid UTF-8 dropped (would break json_encode)");
check(FloorPlanCore::cleanText("Хувцас солих өрөө", 120) === "Хувцас солих өрөө", "Cyrillic preserved");
check(FloorPlanCore::cleanText(array("x"), 10) === "", "array text -> empty");
check(FloorPlanCore::cleanText("a\x00b\x07c", 10) === "abc", "control characters stripped");
check(FloorPlanCore::cleanText("<script>alert(1)</script>", 80) === "<script>alert(1)</script>", "markup kept as text (escaped on output)");

/* ---- payload (all floors at once) ---- */
$base = array("slug" => "a", "x" => 0.1, "y" => 0.2, "titleEn" => "A");
$fl = function ($key, $spots, $extra = array()) {
	return array_merge(array("key" => $key, "floorTitle" => "T", "enabled" => true, "link" => array("x" => 0.5, "y" => 0.5), "hotspots" => $spots), $extra);
};
$ok = FloorPlanCore::sanitizePayload(array("floors" => array(
	$fl("office-20f", array($base, array_merge($base, array("slug" => "b", "titleEn" => "B")))),
	$fl("office-21f", array(array_merge($base, array("slug" => "c"))), array("link" => array("x" => 9, "y" => "-1")))
)));
check($ok["ok"] && count($ok["floors"]) === 2 && count($ok["floors"][0]["hotspots"]) === 2, "good payload");
check($ok["floors"][0]["hotspots"][0]["order"] === 1 && $ok["floors"][0]["hotspots"][1]["order"] === 2, "order comes from array position");
check($ok["floors"][1]["link"] === array("x" => 1.0, "y" => 0.0), "staircase position clamped");

check(!FloorPlanCore::sanitizePayload(array("floors" => array($fl("office-20f", array($base)), $fl("office-21f", array($base)))))["ok"], "the same slug on two floors is rejected (?area= must be unique)");
check(!FloorPlanCore::sanitizePayload(array("floors" => array($fl("office-20f", array($base, $base)))))["ok"], "duplicate slug rejected");
check(!FloorPlanCore::sanitizePayload(array("floors" => array($fl("office-20f", array($base), array("floorTitle" => "")))))["ok"], "empty floor title rejected");
check(!FloorPlanCore::sanitizePayload(array("floors" => array($fl("office-99f", array($base)))))["ok"], "unknown floor key rejected (floors cannot be invented by a request)");
check(!FloorPlanCore::sanitizePayload(array("floors" => array($fl("office-20f", array()), $fl("office-20f", array()))))["ok"], "the same floor twice rejected");
check(!FloorPlanCore::sanitizePayload(array("floors" => array($fl("office-20f", array(array("slug" => "a", "x" => 0.1, "y" => 0.1))))))["ok"], "hotspot with no title rejected");
check(!FloorPlanCore::sanitizePayload(array("floors" => array($fl("office-20f", array(array("slug" => "", "x" => 0.1, "y" => 0.1, "titleEn" => "A"))))))["ok"], "empty slug rejected");
check(!FloorPlanCore::sanitizePayload("nope")["ok"], "non-array payload rejected");
check(!FloorPlanCore::sanitizePayload(array("floors" => array()))["ok"], "no floors rejected");
check(!FloorPlanCore::sanitizePayload(array("floorTitle" => "x", "hotspots" => array($base)))["ok"], "old single-floor shape rejected");

$many = array();
for ($i = 0; $i < FloorPlanCore::MAX_SPOTS + 5; $i++) {
	$many[] = array("slug" => "s" . $i, "x" => 0.1, "y" => 0.1, "titleEn" => "S");
}
check(!FloorPlanCore::sanitizePayload(array("floors" => array($fl("office-20f", $many))))["ok"], "too many hotspots rejected");

$off = FloorPlanCore::sanitizePayload(array("floors" => array($fl("office-21f", array(), array("enabled" => "0", "link" => null)))));
check($off["ok"] && $off["floors"][0]["enabled"] === false && $off["floors"][0]["link"] === null && count($off["floors"][0]["hotspots"]) === 0, "a floor can be disabled / empty / without stairs");

/* ---- planFromRow ---- */
$pr = FloorPlanCore::planFromRow(array("planKey" => "office-21f", "sortOrder" => "21", "floorTitle" => "21-Р ДАВХАР", "imageUrl" => "/a.webp", "imageMidUrl" => "", "imageFullUrl" => "",
	"imageWidth" => "3296", "imageHeight" => "2584", "boundX0" => "0.9", "boundY0" => "0", "boundX1" => "0.1", "boundY1" => "1", "linkX" => null, "linkY" => "0.5", "enabled" => "1"));
check($pr["bounds"] === array(0.0, 0.0, 1.0, 1.0), "inverted bounds from the DB fall back to the whole image");
check($pr["link"] === null, "half a staircase position -> no staircase");
check($pr["imageWidth"] === 3296 && $pr["enabled"] === true && $pr["sortOrder"] === 21, "plan row typed");

/* ---- fromRow (DB rows arrive as strings) ---- */
$row = array("slug" => "kitchen", "sortOrder" => "12", "enabled" => "1", "kind" => "room", "titleEn" => "Kitchen", "titleMn" => "Гал тогоо",
	"descriptionMn" => "", "x" => "0.6586", "y" => "0.6068", "desktopZoom" => "4.5", "desktopFocusX" => null, "desktopFocusY" => null,
	"desktopOffsetX" => "0", "desktopOffsetY" => "0", "mobileZoom" => "7.5", "mobileFocusX" => "0.5", "mobileFocusY" => null,
	"mobileOffsetX" => "0", "mobileOffsetY" => "0");
$r = FloorPlanCore::fromRow($row);
check($r !== null && $r["x"] === 0.6586 && $r["order"] === 12 && $r["enabled"] === true, "row -> hotspot with typed values");
check($r["mobile"]["focusX"] === 0.5 && $r["mobile"]["focusY"] === null, "nullable focus round-trips");
check(FloorPlanCore::fromRow(array("slug" => "x", "x" => null, "y" => null)) === null, "row with NULL position dropped");
$back = FloorPlanCore::toRow($r);
check($back["desktopZoom"] === 4.5 && $back["enabled"] === 1 && $back["sortOrder"] === 12, "hotspot -> row");

/* ---- load() never throws: falls back to defaults ---- */
$fb = FloorPlanCore::load(null, true);
check($fb["fallback"] === true && count($fb["floors"]) === 2 && count($fb["floors"][0]["hotspots"]) + count($fb["floors"][1]["hotspots"]) === 16, "no DB -> defaults (both floors)");

class FloorPlanBrokenDb
{
	public function rawQueryOne() { throw new Exception("db down"); }
	public function rawQuery() { return false; }
	public function rawQueryValue() { return false; }
}
$GLOBALS["floorPlanModuleReady"] = false;
$fb2 = FloorPlanCore::load(new FloorPlanBrokenDb(), true);
check($fb2["fallback"] === true, "DB exception -> defaults");

/* ---- JSON in HTML ---- */
$j = FloorPlanCore::jsonForHtml(array("t" => "</script><b>&'\"", "u" => "a\xE2\x80\xA8b", "mn" => "Өрөө"));
check(strpos($j, "</script>") === false && strpos($j, "<") === false, "no raw < in embedded JSON");
check(strpos($j, "\xE2\x80\xA8") === false, "U+2028 escaped");
check(strpos($j, "Өрөө") !== false, "Cyrillic kept readable");
check(json_decode($j, true)["t"] === "</script><b>&'\"", "embedded JSON decodes back exactly");

echo ($fail === 0 ? "OK" : "FAILED") . " — " . $count . " checks, " . $fail . " failed\n";
exit($fail === 0 ? 0 : 1);
