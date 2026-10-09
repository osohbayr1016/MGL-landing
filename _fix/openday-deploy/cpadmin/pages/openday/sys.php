<?php
/**
 * CP Admin -> Өдөрлөг (Open Office Day тур хуудас, /openday)
 *
 *   /openday/edit              — бүх агуулгыг засах цорын ганц хуудас
 *                                (.htaccess-д мөр нэмэх шаардлагагүй: edit/sys.php)
 *   /?incPageType=openday&subPage=asset&asset=<name>
 *                              — засварлагчид хэрэгтэй JS/CSS/зураг (нэрийн жагсаалттай)
 *
 * Хадгалалт: /userPost/openday (post.sys.php).
 */

$clkMenuMod    = "openday";
$clkMenuModDir = $gloConstModuleDir . $clkMenuMod . "/";

include __DIR__ . "/lib.php";

/* Эрх сервер талд шалгагдана — цэсийг нуух нь хамгаалалт биш. */
if (!openDayCan()) {
	if (isset($_REQUEST["subPage"]) && $_REQUEST["subPage"] === "asset") {
		header("HTTP/1.1 403 Forbidden");
		die();
	}

	$incPageUrl = $clkMenuModDir . "denied.php";
	return;
}

$subPage = "edit";
if (isset($_REQUEST["subPage"]) && $_REQUEST["subPage"] != "") {
	$subPage = txtSec($_REQUEST["subPage"]);
}

switch ($subPage) {

	case "asset":
		include $clkMenuModDir . "asset.php";
		die();

	default:
		include $clkMenuModDir . "edit.sys.php";
		break;
}
