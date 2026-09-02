<?php
/**
 * CP Admin -> Арга хэмжээний бүртгэл
 *
 *   /registration/list      — бүртгүүлсэн хүмүүсийн жагсаалт, Excel татах
 *   /registration/design    — хуудасны блокууд + загварын тохиргоо
 *   /registration/fields    — формын талбарууд
 *   /registration/settings  — арга хэмжээ, багтаамж, хугацаа, мессежүүд
 */

$clkMenuMod    = "registration";
$clkMenuModDir = $gloConstModuleDir . $clkMenuMod . "/";

include __DIR__ . "/lib.php";

$subPage = "list";
if (isset($_REQUEST["subPage"]) && $_REQUEST["subPage"] != "") {
	$subPage = txtSec($_REQUEST["subPage"]);
}

switch ($subPage) {

	/* ---- Excel татах (шууд файл өгөөд зогсоно) ---- */
	case "export":
		include $clkMenuModDir . "export.php";
		die();

	/* ---- Хуудасны дизайн ---- */
	case "design":
		include $clkMenuModDir . "design.sys.php";
		break;

	case "blockEdit":
		include $clkMenuModDir . "block.edit.sys.php";
		break;

	case "subList":
		include $clkMenuModDir . "sub.list.sys.php";
		break;

	case "subEdit":
		include $clkMenuModDir . "sub.edit.sys.php";
		break;

	/* ---- Формын талбарууд ---- */
	case "fields":
		include $clkMenuModDir . "fields.sys.php";
		break;

	case "fieldEdit":
		include $clkMenuModDir . "field.edit.sys.php";
		break;

	/* ---- Тохиргоо ---- */
	case "settings":
		include $clkMenuModDir . "settings.sys.php";
		break;

	/* ---- Бүртгэлийн жагсаалт ---- */
	default:
		include $clkMenuModDir . "list.sys.php";
		break;
}
