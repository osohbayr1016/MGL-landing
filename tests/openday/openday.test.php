<?php
/**
 * class/openday.class.php — өгөгдлийн сангүй шалгалт (цэвэрлэгээ, хэвлэлт, жишээ агуулга).
 *
 *   php tests/openday/openday.test.php        (PHP 7.2 дээр ч ажиллана)
 *
 * CP Admin + нийтийн хуудасны браузерын шалгалтыг (нэвтрэлт, эрх, чирэх,
 * хадгалах, revision зөрчил, CSRF) локал MariaDB дээр гараар ажиллуулсан.
 */
ini_set("display_errors", "1");
error_reporting(E_ALL);

require __DIR__ . "/../../class/openday.class.php";

$fail = 0;
$count = 0;
function check($c, $m)
{
	global $fail, $count;
	$count++;
	if (!$c) {
		$fail++;
		echo "FAIL: " . $m . "\n";
	}
}

/* ---- жишээ агуулга (DB уншиж чадаагүй үед ч хуудас ажиллана) ---- */
$fb = OpenDayCore::load(null, true);
check($fb["fallback"] === true, "no database -> sample content");
check(count($fb["items"]["agenda"]) === 7 && count($fb["items"]["spot"]) === 11 && count($fb["items"]["faq"]) === 4, "sample rows");
$slugs = array();
foreach ($fb["items"]["spot"] as $s) {
	$slugs[$s["slug"]] = true;
}
foreach (array("agenda", "activity", "info") as $k) {
	foreach ($fb["items"][$k] as $it) {
		check($it["spot"] === "" || isset($slugs[$it["spot"]]), "sample $k row points at an existing spot ({$it["spot"]})");
	}
}

/* ---- цаг, огноо ---- */
check(OpenDayCore::cleanTime("9:05") === "09:05", "time padded");
check(OpenDayCore::cleanTime("24:00") === "" && OpenDayCore::cleanTime("12:60") === "" && OpenDayCore::cleanTime(array()) === "", "bad times dropped");
check(OpenDayCore::cleanDate("2026-10-17") === "2026-10-17" && OpenDayCore::cleanDate("2026-02-30") === "", "dates validated");

/* ---- текст ---- */
check(OpenDayCore::cleanLine("a\nb", 10) === "a b", "titles are one line");
check(OpenDayCore::cleanText("a\r\nb\x07", 10) === "a\nb", "bodies keep newlines, drop control bytes");
check(OpenDayCore::cleanText("\xC3\x28", 10) === "", "invalid UTF-8 dropped");
check(OpenDayCore::cleanText(str_repeat("ө", 20), 5) === "өөөөө", "cut on characters, not bytes");
check(OpenDayCore::cleanSlug("Кофе Bar!") === "bar", "slug: latin, digits, dashes only");

/* ---- мөр ---- */
check(OpenDayCore::sanitizeItem(array("kind" => "hack")) === null, "unknown kind rejected");
check(OpenDayCore::sanitizeItem(array("kind" => "spot", "slug" => "a", "floor" => "office-20f", "x" => "NaN", "y" => 0)) === null, "spot needs numeric x/y");
$s = OpenDayCore::sanitizeItem(array("kind" => "spot", "slug" => "a", "floor" => "office-20f", "x" => 5, "y" => -1, "icon" => "<script>"));
check($s["x"] === 1.0 && $s["y"] === 0.0 && $s["icon"] === "pin", "spot clamped, unknown icon -> pin");
$i = OpenDayCore::sanitizeItem(array("kind" => "info", "enabled" => "0", "start" => "10:00"));
check($i["enabled"] === false && $i["start"] === "" && $i["icon"] === "info", "info: no times, default icon, disabled kept");

/* ---- бүх өгөгдөл ---- */
$p = OpenDayCore::sanitizePayload(array(
	"settings" => array("titleMn" => "T", "eventDate" => "2026-10-17", "startTime" => "11:00", "introMn" => "a\nb"),
	"items" => array(
		"spot" => array(
			array("slug" => "x", "floor" => "office-20f", "x" => 0.1, "y" => 0.1, "titleMn" => "A"),
			array("slug" => "x", "floor" => "office-20f", "x" => 0.2, "y" => 0.2, "titleMn" => "B")
		),
		"agenda" => array(array("spot" => "nope", "titleMn" => "Z"), array("spot" => "x", "titleMn" => "Y"))
	)
), array("office-20f", "office-21f"));
check($p["ok"], "valid payload accepted");
check($p["items"]["spot"][1]["slug"] === "x-2", "duplicate spot slug renamed");
check($p["items"]["agenda"][0]["spot"] === "" && $p["items"]["agenda"][1]["spot"] === "x", "link to a missing spot cleared");
check($p["settings"]["introMn"] === "a\nb", "intro keeps newlines");

$p = OpenDayCore::sanitizePayload(array("settings" => array(), "items" => array()), null);
check(!$p["ok"] && in_array("settings.title", $p["errors"], true), "title required");

$p = OpenDayCore::sanitizePayload(array("settings" => array("titleMn" => "T"), "items" => array(
	"spot" => array(array("slug" => "a", "floor" => "office-99f", "x" => 0, "y" => 0))
)), array("office-20f"));
check(!$p["ok"] && in_array("spot.floor", $p["errors"], true), "spot on an unknown floor refused");

$many = array();
for ($k = 0; $k < OpenDayCore::MAX_ITEMS + 5; $k++) {
	$many[] = array("titleMn" => "q" . $k);
}
$p = OpenDayCore::sanitizePayload(array("settings" => array("titleMn" => "T"), "items" => array("faq" => $many)), null);
check(!$p["ok"] && count($p["items"]["faq"]) === OpenDayCore::MAX_ITEMS, "row count capped");

/* ---- асуулт хүлээн авах, хэсгийн гарчиг ---- */
$p = OpenDayCore::sanitizePayload(array("settings" => array("titleMn" => "T", "askOn" => "0", "faqTitleMn" => "Q
A"), "items" => array()), null);
check($p["settings"]["askOn"] === "0" && $p["settings"]["faqTitleMn"] === "Q A", "askOn kept, section title one line");
$p = OpenDayCore::sanitizePayload(array("settings" => array("titleMn" => "T", "askOn" => "yes"), "items" => array()), null);
check($p["settings"]["askOn"] === "1", "askOn defaults to on");
$d = OpenDayCore::defaultSettings();
check($d["schedTitleMn"] === "Хөтөлбөр" && $d["askOn"] === "1", "section title / ask defaults");

/* ---- засах горимын талбар ---- */
check(OpenDayCore::field(false, "s.title", "а", "a") === OpenDayCore::bi("а", "a"), "field() == bi() outside the editor");
$f = OpenDayCore::field(true, "agenda.2.body", "x
y", "", true, "Тайлбар");
check(strpos($f, 'data-ode-f="agenda.2.bodyMn"') !== false && strpos($f, 'data-ode-f="agenda.2.bodyEn"') !== false && strpos($f, 'data-ode-multi="1"') !== false, "editor field: both languages, own paths");
check(strpos($f, "x
y") !== false && strpos($f, "<br") === false, "editor field keeps raw newlines (pre-wrap), no <br>");
check(strpos(OpenDayCore::field(true, "s.title", "<b>", "", false), "&lt;b&gt;") !== false, "editor field escaped");

/* ---- хэвлэлт ---- */
check(OpenDayCore::bi("<b>", "") === "&lt;b&gt;", "escaped, single span-less text when EN empty");
check(OpenDayCore::bi("а", "a") === '<span class="od-mn">а</span><span class="od-en" lang="en">a</span>', "two languages");
check(OpenDayCore::bi("1\n2", "", true) === "1<br>\n2", "multi-line text");
check(OpenDayCore::timeRange("11:00", "") === "11:00" && OpenDayCore::timeRange("11:00", "12:00") === "11:00 – 12:00", "time range");
check(strpos(OpenDayCore::jsonForHtml(array("a" => "</script>\xE2\x80\xA8")), "</script>") === false, "JSON safe inside <script>");

echo ($count - $fail) . "/" . $count . " passed\n";
exit($fail ? 1 : 0);
