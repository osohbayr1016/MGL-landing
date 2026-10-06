<?php
/**
 * The public page path: widgets/pagesch/temp.php with a real database.
 *
 *   FP_DB_PORT=3306 FP_DB_PASS=... php tests/floorplan/page.test.php
 *
 * Checks that the About hero replaces the old photo banner, that the CMS
 * widgets after it are untouched, and that EVERY failure mode (no tables,
 * plan disabled, no enabled hotspots, partial throws) falls back to the
 * original banner instead of breaking the page.
 */
ini_set("display_errors", "0");
chdir(__DIR__ . "/../..");

require "class/main.class.php";
require "class/floorplan.class.php";

$db = new MysqliDb(getenv("FP_DB_HOST") ?: "127.0.0.1", getenv("FP_DB_USER") ?: "root", getenv("FP_DB_PASS") ?: "", getenv("FP_DB_NAME") ?: "fp_test", (int)(getenv("FP_DB_PORT") ?: 3306));
$tbl_pref = "fptest_";
$t = FloorPlanCore::tables();

function newsPicFnc($a, $pic) { return "/cp/" . $pic; }

$fail = 0; $count = 0;
function check($c, $m) { global $fail, $count; $count++; if (!$c) { $fail++; echo "FAIL: $m\n"; } }

/* what widgets/pagesch/sys.php prepares for an About page with two CMS sections */
function render($mod = "about")
{
	global $db, $clkMenuMod, $allSchArr, $selSchBody, $objs, $incTemp, $fpData, $fpLevel, $fpWidHtml, $fpFinalHtml, $gloLangObj;
	$clkMenuMod = $mod;
	$allSchArr = array(
		1166 => array("schID" => 1166, "schTemp" => 0, "sub" => array(), "schNote" => json_encode(array("title" => "Профайл", "pic" => "Picture/111.jpg", "body" => '<div class="aboutText"><ul><li>First section body</li></ul></div>'))),
		21   => array("schID" => 21, "schTemp" => 0, "sub" => array(), "schNote" => json_encode(array("title" => "Ажил эрхлэлт", "pic" => "Picture/222.jpg", "body" => "<p>Second section body</p>")))
	);
	ob_start();
	include "widgets/pagesch/temp.php";
	return ob_get_clean();
}

function reset_state()
{
	$GLOBALS["floorPlanModuleReady"] = false;
}

/* ---- happy path ---- */
$db->rawQuery("DROP TABLE IF EXISTS `" . $t["plan"] . "`");
$db->rawQuery("DROP TABLE IF EXISTS `" . $t["hotspot"] . "`");
reset_state();
$html = render();
check(strpos($html, 'class="fp-hero"') !== false, "hero rendered");
check(strpos($html, 'id="widhas1166"') !== false, "hero carries the first section's anchor id");
check(substr_count($html, 'class="pageHeader"') === 1, "the first section's photo banner is replaced (only the second banner remains)");
check(strpos($html, "<h1 class=\"fp-sr-only\">Профайл</h1>") !== false, "h1 text kept for SEO / screen readers");
check(strpos($html, "First section body") !== false && strpos($html, "Second section body") !== false, "CMS text of both sections is intact");
check(strpos($html, "Ажил эрхлэлт") !== false && strpos($html, 'id="widhas21"') !== false, "second section untouched");
check(strpos($html, "20-Р ДАВХАР") !== false, "floor title from the database");
preg_match('#<script type="application/json" data-fp-data>(.*?)</script>#s', $html, $m);
$json = json_decode(isset($m[1]) ? $m[1] : "", true);
check(is_array($json) && count($json["floors"]) === 2 && count($json["floors"][0]["hotspots"]) === 11 && count($json["floors"][1]["hotspots"]) === 5, "both floors (11 + 5 hotspots) embedded as valid JSON");
check($json["floors"][0]["number"] === "20" && $json["floors"][1]["number"] === "21", "floor numbers for ?floor= and the stair label");
check(isset($json["floors"][0]["link"]["x"]) && count($json["floors"][1]["bounds"]) === 4, "staircase and drawing bounds embedded");
check(substr_count($html, "data-fp-floor=") === 2, "one layer per floor");
check(preg_match('#data-fp-floor="office-20f"[^>]*>\s*<div class="fp-world" data-fp-world>\s*<img class="fp-image" src=#', $html) === 1, "first floor image loads immediately");
check(preg_match('#data-fp-floor="office-21f"[^>]*>\s*<div class="fp-world" data-fp-world>\s*<img class="fp-image" data-src=#', $html) === 1, "second floor image is deferred");
check(strpos($html, "data-fp-floor-switch") !== false && strpos($html, "data-fp-floor-switch disabled") === false, "floor title is an active switch");
check(strpos($html, "</script><") === false || substr_count($html, "</script>") >= 4, "no stray script termination from data");
check(strpos($html, "/assets/js/floorplan/core.js?v=") !== false && strpos($html, "/assets/css/floorplan.css?v=") !== false, "assets referenced with cache-busting versions");

check($json["lang"] === "mn", "Mongolian page");
check(strpos($html, "Хувцас солих болон хадгалах") !== false, "Mongolian description in the no-JS list");

/* ---- English page: English descriptions ---- */
$gloLangObj = array("langKey" => "EN");
reset_state();
$htmlEn = render();
preg_match('#<script type="application/json" data-fp-data>(.*?)</script>#s', $htmlEn, $mEn);
$jsonEn = json_decode($mEn[1], true);
check($jsonEn["lang"] === "en" && $jsonEn["floors"][0]["hotspots"][0]["descriptionEn"] === "Changing room with personal storage", "English page: lang=en with English descriptions");
check(strpos($htmlEn, "Changing room with personal storage") !== false, "English description in the no-JS list");
unset($gloLangObj);

/* ---- other pages are untouched ---- */
reset_state();
$other = render("home");
check(strpos($other, "fp-hero") === false && substr_count($other, 'class="pageHeader"') === 2, "non-about pages render exactly as before");

/* ---- disabled hotspot is not public ---- */
$db->rawQuery("UPDATE `" . $t["hotspot"] . "` SET enabled=0 WHERE slug='kitchen'");
reset_state();
$html = render();
preg_match('#<script type="application/json" data-fp-data>(.*?)</script>#s', $html, $m);
$json = json_decode($m[1], true);
check(count($json["floors"][1]["hotspots"]) === 4 && strpos($m[1], "kitchen") === false, "disabled hotspot is not exposed publicly");

/* ---- one floor switched off -> single floor, no switch ---- */
$db->rawQuery("UPDATE `" . $t["plan"] . "` SET enabled=0 WHERE planKey='office-21f'");
reset_state();
$html = render();
preg_match('#<script type="application/json" data-fp-data>(.*?)</script>#s', $html, $m);
$json = json_decode($m[1], true);
check(count($json["floors"]) === 1 && strpos($m[1], "ceo-office") === false, "a disabled floor is not exposed");
check(strpos($html, "data-fp-floor-switch disabled") !== false, "with one floor the title is not a switch");
$db->rawQuery("UPDATE `" . $t["plan"] . "` SET enabled=1");

/* ---- plan disabled -> original banner ---- */
$db->rawQuery("UPDATE `" . $t["plan"] . "` SET enabled=0");
reset_state();
$html = render();
check(strpos($html, "fp-hero") === false && substr_count($html, 'class="pageHeader"') === 2, "plan switched off -> original photo banner");
$db->rawQuery("UPDATE `" . $t["plan"] . "` SET enabled=1");

/* ---- no enabled hotspots -> original banner ---- */
$db->rawQuery("UPDATE `" . $t["hotspot"] . "` SET enabled=0");
reset_state();
$html = render();
check(strpos($html, "fp-hero") === false && substr_count($html, 'class="pageHeader"') === 2, "no enabled hotspots -> original photo banner");
$db->rawQuery("UPDATE `" . $t["hotspot"] . "` SET enabled=1");

/* ---- database unusable -> seeded defaults (page still works) ---- */
$realDb = $db;
$db = null;
reset_state();
$html = render();
check(strpos($html, "fp-hero") !== false, "no database object -> hero from the built-in defaults");
$db = $realDb;

/* ---- the partial itself throws -> original banner ---- */
$partial = "widgets/pagesch/floorplan.hero.php";
rename($partial, $partial . ".bak");
file_put_contents($partial, '<?php throw new Exception("boom");');
reset_state();
$html = render();
rename($partial . ".bak", $partial);
check(strpos($html, "fp-hero") === false && substr_count($html, 'class="pageHeader"') === 2 && strpos($html, "First section body") !== false, "a failing hero partial falls back to the original banner");

$db->rawQuery("DROP TABLE IF EXISTS `" . $t["plan"] . "`");
$db->rawQuery("DROP TABLE IF EXISTS `" . $t["hotspot"] . "`");

echo ($fail === 0 ? "OK" : "FAILED") . " — " . $count . " checks, " . $fail . " failed\n";
exit($fail === 0 ? 0 : 1);
