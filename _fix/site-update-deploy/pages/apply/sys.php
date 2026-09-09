<?php
/**
 * Өргөдлийн хуудас.
 *
 *   /internship  — дадлага хийх хүсэлт
 *   /career      — ажлын байрны өргөдөл
 *
 * Ирсэн хүсэлт бүр CP Admin -> Дадлага хэсэгт харагдана.
 */

$clkMenuMod    = "apply";
$clkMenuModDir = $gloConstModuleDir . $clkMenuMod . "/";

$applyType = "intern";

if (isset($_REQUEST["applyType"]) && $_REQUEST["applyType"] != "") {
	$applyType = txtSec($_REQUEST["applyType"]);
}

include $clkMenuModDir . "handle.php";

/* Сайтын толгой хэсэг $clkMenuObj-оос body class болон нэрээ авдаг */
$clkMenuObj = array(
	"id"       => 0,
	"name"     => ApplyCore::pageTitle($applySet, $applyType),
	"pageType" => "page"
);

$pageID        = 0;
$addPageTitle  = ApplyCore::pageTitle($applySet, $applyType);
$siteInfoDes   = ApplyCore::pageText($applySet, $applyType);

$subMenuArr    = array();
$allWidgetArr  = array();

$incPageUrl = $clkMenuModDir . "page.php";
