<?php
/** Өдөрлөг — засварын хуудас: агуулга, давхрууд, CSRF токен бэлтгэнэ. */

$incPageUrl = $clkMenuModDir . "edit.php";

$odeLoaded = OpenDayCore::load($db, false);
$odePlans  = FloorPlanCore::load($db, false);

/* Схемийн зургийг CP Admin домэйнээс asset route-оор өгнө (cross-origin биш). */
$odeAsset = function ($url) {
	$name = basename((string)$url);
	list($file) = openDayImagePath($name);
	/* ".htaccess"-аас хамааралгүй: үндсэн index.php үргэлж хүлээн авна */
	return $file !== null ? "/?incPageType=openday&subPage=asset&asset=" . rawurlencode($name) : "";
};

$odeFloors = array();
foreach ($odePlans["floors"] as $odeF) {
	$odeP = $odeF["plan"];
	$odeFloors[] = array(
		"key"         => $odeP["key"],
		"number"      => preg_match('/(\d+)/', $odeP["key"], $odeM) ? $odeM[1] : $odeP["key"],
		"imageUrl"    => $odeAsset($odeP["imageMidUrl"] != "" ? $odeP["imageMidUrl"] : $odeP["imageUrl"]),
		"imageWidth"  => $odeP["imageWidth"],
		"imageHeight" => $odeP["imageHeight"],
		"bounds"      => $odeP["bounds"],
		"link"        => $odeP["link"]
	);
}

$odeIcons = array();
foreach (OpenDayCore::icons() as $odeKey => $odeIcon) {
	$odeIcons[] = array("key" => $odeKey, "fa" => $odeIcon[0], "label" => $odeIcon[1]);
}

$odeConfig = array(
	"settings"  => $odeLoaded["settings"],
	"items"     => $odeLoaded["items"],
	"revision"  => $odeLoaded["revision"],
	"floors"    => $odeFloors,
	"icons"     => $odeIcons,
	"csrf"      => openDayCsrf(),
	"saveUrl"   => "/userPost/openday",
	"publicUrl" => openDayPublicUrl(),
	"fallback"  => !empty($odeLoaded["fallback"])
);

$odeAssetVer = function ($local, $rel) {
	$f = $local ? __DIR__ . "/" . $rel : openDaySiteRoot() . $rel;
	return is_file($f) ? (int)filemtime($f) : 1;
};
