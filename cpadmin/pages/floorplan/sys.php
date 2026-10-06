<?php
/**
 * CP Admin -> Офис схем
 *
 *   /floorplan/edit            — цэг, текст, камерын засвар (энэ нь л цорын ганц хуудас)
 *   /floorplan/asset/<name>    — засварлагчид хэрэгтэй JS/CSS/зураг (нэрийн жагсаалттай)
 *
 * Хадгалалт: /userPost/floorplan (post.sys.php).
 */

$clkMenuMod    = "floorplan";
$clkMenuModDir = $gloConstModuleDir . $clkMenuMod . "/";

include __DIR__ . "/lib.php";

/* Эрх сервер талд шалгагдана — цэсийг нуух нь хамгаалалт биш. */
if (!floorPlanCan()) {
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
