<?php
/**
 * Хавсралтыг татаж авах — зөвхөн нэвтэрсэн админд.
 *
 * Файлууд нь cpadmin/postpic/apply/ дотор ба тэр хавтас .htaccess-ээр
 * гаднаас хаалттай тул зөвхөн энэ файлаар дамжиж уншигдана.
 */

include __DIR__ . "/lib.php";

$fileEntryID = 0;
if (isset($_REQUEST["objID"])) {
	$fileEntryID = (int)txtSec($_REQUEST["objID"]);
}

$fileIndex = isset($_REQUEST["f"]) ? (int)$_REQUEST["f"] : 0;

$fileRow = null;
if ($fileEntryID > 0) {
	$fileRow = $db->rawQueryOne(
		"SELECT * FROM `" . $applyTbl["entry"] . "` WHERE `entryID`=?",
		array($fileEntryID)
	);
}

if (!is_array($fileRow) || count($fileRow) < 1) {
	http_response_code(404);
	die("Хүсэлт олдсонгүй.");
}

$fileArr = ApplyCore::files($fileRow);

if (!isset($fileArr[$fileIndex])) {
	http_response_code(404);
	die("Хавсралт олдсонгүй.");
}

$filePath = ApplyCore::filePath($fileArr[$fileIndex]["stored"]);

if ($filePath == "") {
	http_response_code(404);
	die("Файл сервер дээр байхгүй байна.");
}

$fileName = $fileArr[$fileIndex]["name"];
$fileName = preg_replace('/[\r\n"]+/', "", (string)$fileName);
if ($fileName == "") {
	$fileName = $fileArr[$fileIndex]["stored"];
}

while (ob_get_level() > 0) {
	ob_end_clean();
}

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
header("Content-Length: " . filesize($filePath));
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

readfile($filePath);
exit;
