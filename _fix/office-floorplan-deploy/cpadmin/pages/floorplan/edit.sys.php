<?php
/** Офис схем — засварын хуудас: давхрууд + CSRF токен бэлтгэнэ. */

$incPageUrl = $clkMenuModDir . "edit.php";

$fpeLoaded = FloorPlanCore::load($db, false);

/* Схемийн зургийг CP Admin домэйнээс asset route-оор өгнө (cross-origin биш). */
$fpeAsset = function ($url) {
	$name = basename((string)$url);
	list($file) = floorPlanImagePath($name);
	return $file !== null ? "/floorplan/asset/" . $name : "";
};

$fpeFloors = array();
foreach ($fpeLoaded["floors"] as $fpeF) {
	$fpeP = $fpeF["plan"];
	$fpeFloors[] = array(
		"key"         => $fpeP["key"],
		"number"      => preg_match('/(\d+)/', $fpeP["key"], $fpeM) ? $fpeM[1] : $fpeP["key"],
		"floorTitle"  => $fpeP["floorTitle"],
		"enabled"     => $fpeP["enabled"],
		"link"        => $fpeP["link"],
		"imageUrl"    => $fpeAsset($fpeP["imageUrl"]),
		/* засварлагчид дунд хэмжээ хангалттай тод, хурдан */
		"imageMidUrl" => $fpeAsset($fpeP["imageMidUrl"] != "" ? $fpeP["imageMidUrl"] : $fpeP["imageUrl"]),
		"imageWidth"  => $fpeP["imageWidth"],
		"imageHeight" => $fpeP["imageHeight"],
		"bounds"      => $fpeP["bounds"],
		"hotspots"    => $fpeF["hotspots"]
	);
}

$fpeConfig = array(
	"floors"    => $fpeFloors,
	"revisions" => $fpeLoaded["revisions"],
	"csrf"      => floorPlanCsrf(),
	"saveUrl"   => "/userPost/floorplan",
	"fallback"  => !empty($fpeLoaded["fallback"])
);

$fpeAssetVer = function ($local, $rel) {
	$f = $local ? __DIR__ . "/" . $rel : floorPlanSiteRoot() . $rel;
	return is_file($f) ? (int)filemtime($f) : 1;
};
