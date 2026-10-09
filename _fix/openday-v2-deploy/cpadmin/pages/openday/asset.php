<?php
/**
 * Засварлагчид хэрэгтэй файлуудыг (сайтын core.js / viewer.js / floorplan.css /
 * схемийн зураг + өөрийн editor.js / editor.css) CP Admin-ы домэйнээс өгнө.
 * Зөвхөн нэрийн жагсаалтад байгаа файл (зам оруулах боломжгүй).
 *
 * Дуудагдахаас өмнө sys.php нь админ нэвтэрсэн + эрхтэй эсэхийг шалгасан.
 */

$assetName = isset($_REQUEST["asset"]) ? (string)$_REQUEST["asset"] : "";
$assetFile = null;
$assetMime = "application/octet-stream";

$assetMap = openDayAssetMap();

if (isset($assetMap[$assetName])) {
	$def = $assetMap[$assetName];
	$assetMime = $def[2];

	if ($def[0] === "local") {
		$assetFile = __DIR__ . "/" . $def[1];
	} else {
		$root = openDaySiteRoot();
		$assetFile = $root !== null ? $root . $def[1] : null;
	}
} else {
	list($assetFile, $assetMime) = openDayImagePath($assetName);
}

if ($assetFile === null || !is_file($assetFile)) {
	header("HTTP/1.1 404 Not Found");
	die();
}

header("Content-Type: " . $assetMime);
header("Content-Length: " . filesize($assetFile));
header("Cache-Control: private, max-age=3600");
header("X-Content-Type-Options: nosniff");
readfile($assetFile);
