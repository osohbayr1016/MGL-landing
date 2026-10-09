<?php
/** Өдөрлөг — хуудсан дээр засах: агуулга, давхрууд, CSRF токен бэлтгэнэ. */

$incPageUrl = $clkMenuModDir . "edit.php";

$odeLoaded = OpenDayCore::load($db, false);
$odePlans  = FloorPlanCore::load($db, true);

$odeFloors = array();
foreach ($odePlans["floors"] as $odeF) {
	$odeKey = $odeF["plan"]["key"];
	$odeFloors[] = array(
		"key"    => $odeKey,
		"number" => preg_match('/(\d+)/', $odeKey, $odeM) ? $odeM[1] : $odeKey
	);
}

$odeConfig = array(
	"settings"  => $odeLoaded["settings"],
	"items"     => $odeLoaded["items"],
	"revision"  => $odeLoaded["revision"],
	"floors"    => $odeFloors,
	"csrf"      => openDayCsrf(),
	"saveUrl"   => "/userPost/openday",
	"canvasUrl" => "/?incPageType=openday&subPage=canvas",
	"publicUrl" => openDayPublicUrl(),
	"fallback"  => !empty($odeLoaded["fallback"])
);

$odeAssetVer = function ($rel) {
	$f = __DIR__ . "/" . $rel;
	return is_file($f) ? (int)filemtime($f) : 1;
};
