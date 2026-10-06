<?php
/**
 * Integration test of the SQL in class/floorplan.class.php against a REAL
 * MySQL / MariaDB, through the site's own MysqliDb wrapper.
 *
 *   FP_DB_HOST=127.0.0.1 FP_DB_PORT=3306 FP_DB_USER=root FP_DB_PASS=... FP_DB_NAME=fp_test \
 *     php tests/floorplan/db.test.php
 *
 * It creates `fptest_floorplan*` tables (and drops them again) in that database.
 */
ini_set("display_errors", "0");

require __DIR__ . "/../../class/main.class.php";
require __DIR__ . "/../../class/floorplan.class.php";

$host = getenv("FP_DB_HOST") ?: "127.0.0.1";
$port = (int)(getenv("FP_DB_PORT") ?: 3306);
$user = getenv("FP_DB_USER") ?: "root";
$pass = getenv("FP_DB_PASS") ?: "";
$name = getenv("FP_DB_NAME") ?: "fp_test";

$db = new MysqliDb($host, $user, $pass, $name, $port);

/* use test table names so nothing real is touched */
$tbl_pref = "fptest_";
$GLOBALS["tbl_pref"] = $tbl_pref;
$t = FloorPlanCore::tables();
$db->rawQuery("DROP TABLE IF EXISTS `" . $t["plan"] . "`");
$db->rawQuery("DROP TABLE IF EXISTS `" . $t["hotspot"] . "`");

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

function floorOf($loaded, $key)
{
	foreach ($loaded["floors"] as $f) {
		if ($f["plan"]["key"] === $key) {
			return $f;
		}
	}
	return null;
}

/** editor-style payload from a load() result */
function payloadOf($loaded)
{
	$floors = array();
	foreach ($loaded["floors"] as $f) {
		$floors[] = array("key" => $f["plan"]["key"], "floorTitle" => $f["plan"]["floorTitle"], "enabled" => $f["plan"]["enabled"], "link" => $f["plan"]["link"], "hotspots" => $f["hotspots"]);
	}
	return array("floors" => $floors);
}

/* ---- first load creates + seeds both floors ---- */
$GLOBALS["floorPlanModuleReady"] = false;
$a = FloorPlanCore::load($db, false);
check($a["fallback"] === false, "load() used the database, not the fallback");
check(count($a["floors"]) === 2 && $a["floors"][0]["plan"]["key"] === "office-20f" && $a["floors"][1]["plan"]["key"] === "office-21f", "two floors, ordered 20 then 21");
check(count($a["floors"][0]["hotspots"]) === 11 && count($a["floors"][1]["hotspots"]) === 5, "seeded 11 + 5 hotspots");
check($a["revisions"] === array("office-20f" => 1, "office-21f" => 1), "revisions start at 1");
check($a["floors"][1]["plan"]["floorTitle"] === "21-Р ДАВХАР", "floor title seeded (Cyrillic round-trips)");
check(abs($a["floors"][0]["plan"]["link"]["x"] - 0.59284) < 1e-9 && abs($a["floors"][1]["plan"]["bounds"][3] - 0.95279) < 1e-9, "staircase and bounds stored as numbers");
check($a["floors"][0]["hotspots"][1]["titleMn"] === "Гол орц", "Mongolian title round-trips through the DB");

$eng = $db->rawQueryOne("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_NAME=?", array($name, $t["hotspot"]));
check(isset($eng["ENGINE"]) && $eng["ENGINE"] === "InnoDB", "hotspot table is InnoDB");

FloorPlanCore::seed($db);
check((int)FloorPlanCore::scalar($db, "SELECT COUNT(*) FROM `" . $t["hotspot"] . "`", null) === 16, "seed is idempotent");

/* ---- save both floors in one go ---- */
$p = payloadOf($a);
$p["floors"][0]["floorTitle"] = "20-Р ДАВХАР (шинэ)";
$p["floors"][0]["link"] = array("x" => 0.6, "y" => 0.7);
$p["floors"][0]["hotspots"][1]["titleMn"] = "Гол орц (шинэчилсэн)";
$p["floors"][0]["hotspots"][1]["descriptionEn"] = "The front door — updated";
$p["floors"][0]["hotspots"][1]["desktop"]["zoom"] = 100;
$p["floors"][0]["hotspots"] = array_merge(array($p["floors"][0]["hotspots"][1]), array($p["floors"][0]["hotspots"][0]), array_slice($p["floors"][0]["hotspots"], 2));
$p["floors"][1]["enabled"] = false;
$p["floors"][1]["hotspots"][] = array("slug" => "archive", "x" => 0.5, "y" => 0.5, "titleEn" => "Archive", "kind" => "room");
array_shift($p["floors"][1]["hotspots"]); /* kitchen removed */

$clean = FloorPlanCore::sanitizePayload($p);
check($clean["ok"], "payload validates: " . implode(" | ", $clean["errors"]));

$r = FloorPlanCore::save($db, $clean, $a["revisions"]);
check($r["ok"] && $r["revisions"] === array("office-20f" => 2, "office-21f" => 2), "save succeeds and bumps every floor's revision");

$b = FloorPlanCore::load($db, false);
$b20 = floorOf($b, "office-20f");
$b21 = floorOf($b, "office-21f");
check($b20["plan"]["floorTitle"] === "20-Р ДАВХАР (шинэ)", "floor title updated");
check($b20["plan"]["link"] === array("x" => 0.6, "y" => 0.7), "staircase position persisted");
check($b20["hotspots"][0]["slug"] === "main-entrance" && $b20["hotspots"][1]["slug"] === "locker-room", "new order persisted");
check($b20["hotspots"][0]["titleMn"] === "Гол орц (шинэчилсэн)", "edited Mongolian text persisted");
check($b20["hotspots"][0]["descriptionEn"] === "The front door — updated", "English description persisted");
check($a["floors"][0]["hotspots"][0]["descriptionEn"] === "Changing room with personal storage", "English description seeded");
check($b20["hotspots"][0]["desktop"]["zoom"] === FloorPlanCore::ZOOM_MAX, "zoom=100 was clamped before it reached the DB");
check($b21["plan"]["enabled"] === false, "floor disabled");
check(count($b21["hotspots"]) === 5 && $b21["hotspots"][4]["slug"] === "archive", "new hotspot inserted on the 21st");
$slugs21 = array();
foreach ($b21["hotspots"] as $h) { $slugs21[] = $h["slug"]; }
check(!in_array("kitchen", $slugs21, true), "removed hotspot deleted");

$pub = FloorPlanCore::load($db, true);
check(count($pub["floors"]) === 1 && $pub["floors"][0]["plan"]["key"] === "office-20f", "public load leaves out the disabled floor");

/* ---- moving a hotspot to the other floor ---- */
$p2 = payloadOf($b);
$moved = array_pop($p2["floors"][1]["hotspots"]); /* archive -> 20th */
$p2["floors"][0]["hotspots"][] = $moved;
$c2 = FloorPlanCore::sanitizePayload($p2);
$r2 = FloorPlanCore::save($db, $c2, $b["revisions"]);
$m = FloorPlanCore::load($db, false);
$m20 = array(); foreach (floorOf($m, "office-20f")["hotspots"] as $h) { $m20[] = $h["slug"]; }
$m21 = array(); foreach (floorOf($m, "office-21f")["hotspots"] as $h) { $m21[] = $h["slug"]; }
check($r2["ok"] && in_array("archive", $m20, true) && !in_array("archive", $m21, true), "a hotspot moved between floors exists exactly once");

/* ---- stale editor (someone else saved) ---- */
$stale = FloorPlanCore::save($db, $clean, $a["revisions"]);
check(!$stale["ok"] && $stale["code"] === "conflict", "stale revisions are rejected as a conflict");
$stale2 = FloorPlanCore::save($db, $clean, array("office-20f" => $m["revisions"]["office-20f"]));
check(!$stale2["ok"] && $stale2["code"] === "conflict", "a missing revision for one floor is a conflict too");
$still = FloorPlanCore::load($db, false);
check($still["revisions"] === $m["revisions"], "conflicting saves changed nothing");

/* ---- an invalid write rolls back as a whole ---- */
$bad = $c2;
$bad["floors"][0]["title"] = "SHOULD NOT STICK";
/* a NEW slug twice reaches SQL as two inserts -> unique key violation on the 2nd floor processed */
$dupNew = array_merge($bad["floors"][1]["hotspots"][0], array("slug" => "dup-new"));
$bad["floors"][1]["hotspots"][] = $dupNew;
$bad["floors"][1]["hotspots"][] = $dupNew;
try {
	FloorPlanCore::save($db, $bad, $m["revisions"]);
} catch (Exception $e) {
	/* expected */
}
$after = FloorPlanCore::load($db, false);
check($after["revisions"] === $m["revisions"] && floorOf($after, "office-20f")["plan"]["floorTitle"] !== "SHOULD NOT STICK", "failed save rolled back completely (the floor saved first too)");

/* ---- empty floor allowed ---- */
$pe = payloadOf($after);
$pe["floors"][1]["hotspots"] = array();
$e = FloorPlanCore::save($db, FloorPlanCore::sanitizePayload($pe), $after["revisions"]);
check($e["ok"] && count(floorOf(FloorPlanCore::load($db, false), "office-21f")["hotspots"]) === 0, "saving an empty floor removes its hotspots");

/* ---- upgrade from the earlier single-floor table layout ---- */
$db->rawQuery("DROP TABLE IF EXISTS `" . $t["plan"] . "`");
$db->rawQuery("DROP TABLE IF EXISTS `" . $t["hotspot"] . "`");
$db->rawQuery("CREATE TABLE `" . $t["plan"] . "` (`planKey` varchar(32) NOT NULL, `floorTitle` varchar(120) NOT NULL DEFAULT '', `imageUrl` varchar(255) NOT NULL DEFAULT '', `imageMidUrl` varchar(255) NOT NULL DEFAULT '', `imageFullUrl` varchar(255) NOT NULL DEFAULT '', `imageWidth` int(11) NOT NULL DEFAULT '0', `imageHeight` int(11) NOT NULL DEFAULT '0', `enabled` tinyint(1) NOT NULL DEFAULT '1', `revision` int(11) NOT NULL DEFAULT '1', `createdAt` datetime DEFAULT NULL, `updatedAt` datetime DEFAULT NULL, PRIMARY KEY (`planKey`)) ENGINE=InnoDB DEFAULT CHARSET=utf8");
$db->rawQuery("INSERT INTO `" . $t["plan"] . "` (`planKey`,`floorTitle`,`imageUrl`,`imageWidth`,`imageHeight`) VALUES ('office-20f','20-Р ДАВХАР','/assets/images/floorplan/office-20f-2560.webp',8852,4252)");
$GLOBALS["floorPlanModuleReady"] = false;
$up = FloorPlanCore::load($db, false);
check($up["fallback"] === false && count($up["floors"]) === 2, "old single-floor plan table is upgraded in place (columns added, hotspot table created)");
check(floorOf($up, "office-20f")["plan"]["imageWidth"] === 5288 && count(floorOf($up, "office-20f")["hotspots"]) === 11, "old whole-image data replaced by the per-floor seed");

/* ---- hotspot table from before the English description: column added in place, data kept ---- */
$db->rawQuery("ALTER TABLE `" . $t["hotspot"] . "` DROP COLUMN `descriptionEn`");
$db->rawQuery("UPDATE `" . $t["hotspot"] . "` SET `titleMn`='Хадгалагдсан' WHERE `slug`='kitchen'");
$GLOBALS["floorPlanModuleReady"] = false;
$en = FloorPlanCore::load($db, false);
check($en["fallback"] === false && floorOf($en, "office-21f")["hotspots"][0]["titleMn"] === "Хадгалагдсан", "descriptionEn column added without touching existing content");
check(floorOf($en, "office-21f")["hotspots"][0]["descriptionEn"] === "", "existing rows get an empty English description");

/* ---- tables dropped -> auto-reinstall (fresh server) ---- */
$db->rawQuery("DROP TABLE `" . $t["plan"] . "`");
$GLOBALS["floorPlanModuleReady"] = false;
$c = FloorPlanCore::load($db, false);
check($c["fallback"] === false && count($c["floors"]) === 2, "missing tables are recreated and re-seeded");

$db->rawQuery("DROP TABLE IF EXISTS `" . $t["plan"] . "`");
$db->rawQuery("DROP TABLE IF EXISTS `" . $t["hotspot"] . "`");

echo ($fail === 0 ? "OK" : "FAILED") . " — " . $count . " checks, " . $fail . " failed\n";
exit($fail === 0 ? 0 : 1);
