<?php
/**
 * CP Admin -> Дадлага (вэб сайтаар ирсэн өргөдөл)
 *
 *   /internship/list      — ирсэн хүсэлтийн жагсаалт, Excel татах
 *   /internship/view/ID   — нэг хүсэлтийн дэлгэрэнгүй (modal)
 *   /internship/fields    — формын асуулт/талбарууд
 *   /internship/settings  — хуудасны бичвэр, хавсралт, хаана гарах
 */

$clkMenuMod    = "internship";
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

	/* ---- Хавсралт татах ---- */
	case "file":
		include $clkMenuModDir . "file.php";
		die();

	/* ---- Нэг хүсэлтийн дэлгэрэнгүй ---- */
	case "view":
		include $clkMenuModDir . "view.sys.php";
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

	/* ---- Хүсэлтийн жагсаалт ---- */
	default:
		include $clkMenuModDir . "list.sys.php";
		break;
}
