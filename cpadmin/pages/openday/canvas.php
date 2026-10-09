<?php
/**
 * Хуудсан дээр засах цонх (iframe) — POST-оор ирсэн хадгалаагүй ноорогоор
 * нийтийн хуудсыг (pages/openday/sys.php + skin/new/openday.php) "засах
 * горимоор" зурж буцаана. Өгөгдлийн санд юу ч бичихгүй.
 *
 *   csrf    — засварын хуудасны токен
 *   payload — {"settings": {...}, "items": {...}} (editor.js-ийн model)
 *   page    — аль хуудсыг нээх (home / schedule / map / info / faq)
 */

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !openDayCsrfOk(isset($_POST["csrf"]) ? $_POST["csrf"] : "")) {
	header("HTTP/1.1 403 Forbidden");
	header("Content-Type: text/plain; charset=utf-8");
	echo "Хуудасны хугацаа дууссан байна. Засварын хуудсаа дахин ачаална уу.";
	die();
}

$odcRaw = isset($_POST["payload"]) && is_string($_POST["payload"]) ? $_POST["payload"] : "";
$odcPayload = strlen($odcRaw) <= 600000 ? json_decode($odcRaw, true) : null;

if (!is_array($odcPayload)) {
	header("HTTP/1.1 400 Bad Request");
	header("Content-Type: text/plain; charset=utf-8");
	echo "Өгөгдөл буруу байна.";
	die();
}

/* хадгалахтай ижил цэвэрлэгээ — гэхдээ гарчиг хоосон байж болно (ноорог) */
$odcClean = OpenDayCore::sanitizePayload($odcPayload, null);

$odcSettings = OpenDayCore::defaultSettings();
foreach ($odcClean["settings"] as $odcK => $odcV) {
	$odcSettings[$odcK] = $odcV;
}

/* нийтийн хуудасны кодын оролт */
$odEdit     = true;
$odData     = array("settings" => $odcSettings, "items" => $odcClean["items"], "revision" => 0, "fallback" => false);
$odPage     = isset($_POST["page"]) && is_string($_POST["page"]) ? $_POST["page"] : "home";
$odSiteBase = openDaySiteBase();
$odAssetUrl = function ($rel) {
	return openDayCanvasAsset($rel);
};

$odcSite = openDaySiteRoot();
if ($odcSite === null || !is_file($odcSite . "pages/openday/sys.php") || !is_file($odcSite . "skin/new/openday.php")) {
	header("Content-Type: text/plain; charset=utf-8");
	echo "pages/openday/sys.php эсвэл skin/new/openday.php олдсонгүй.";
	die();
}

include $odcSite . "pages/openday/sys.php";

header("Content-Type: text/html; charset=utf-8");
header("Cache-Control: no-store");
header("X-Content-Type-Options: nosniff");
include $odcSite . "skin/new/openday.php";
