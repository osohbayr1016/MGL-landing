<?php
/**
 * CP Admin -> Өдөрлөг (Open Office Day тур хуудас, /openday)
 *
 *   /openday/edit       — хуудсыг ЯГ харагдах байдлаар нь дээр нь засах (edit/sys.php shim)
 *   /openday/questions  — зочдын илгээсэн асуулт, шууд шинэчлэгдэнэ (questions/sys.php shim)
 *   /?incPageType=openday&subPage=canvas    — POST: ноорог -> засах горимтой хуудасны HTML (iframe)
 *   /?incPageType=openday&subPage=qfeed     — GET: шинэ асуултууд (JSON, 4 секунд тутам)
 *   /?incPageType=openday&subPage=asset&asset=<name> — засварлагчид хэрэгтэй JS/CSS/зураг
 *
 * Хадгалалт: /userPost/openday (post.sys.php). .htaccess-д мөр нэмэх шаардлагагүй.
 */

$clkMenuMod    = "openday";
$clkMenuModDir = $gloConstModuleDir . $clkMenuMod . "/";

include __DIR__ . "/lib.php";

$subPage = "edit";
if (isset($_REQUEST["subPage"]) && $_REQUEST["subPage"] != "") {
	$subPage = txtSec($_REQUEST["subPage"]);
}

/* Эрх сервер талд шалгагдана — цэсийг нуух нь хамгаалалт биш. */
$openDayNeed = in_array($subPage, array("questions", "qfeed"), true) ? openDayCan("questions")
	: ($subPage === "asset" ? (openDayCan("edit") || openDayCan("questions")) : openDayCan("edit"));

if (!$openDayNeed) {
	if (in_array($subPage, array("asset", "canvas", "qfeed"), true)) {
		header("HTTP/1.1 403 Forbidden");
		die();
	}

	$incPageUrl = $clkMenuModDir . "denied.php";
	return;
}

switch ($subPage) {

	case "asset":
		include $clkMenuModDir . "asset.php";
		die();

	case "canvas":
		include $clkMenuModDir . "canvas.php";
		die();

	case "qfeed":
		include $clkMenuModDir . "qfeed.php";
		die();

	case "questions":
		$incPageUrl = $clkMenuModDir . "questions.php";
		break;

	default:
		include $clkMenuModDir . "edit.sys.php";
		break;
}
