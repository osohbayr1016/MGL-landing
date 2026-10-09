<?php
/**
 * Ирсэн асуултын шууд feed (JSON). questions.js 4 секунд тутам дууддаг:
 *   ?after=<сүүлийн id>  — зөвхөн түүнээс хойших шинэ асуултууд
 *   ?after=0             — бүгд (хуудас анх нээгдэхэд)
 * Мөн нийт / шинэ асуултын тоог буцаана (устгасан, тэмдэглэснийг тусгана).
 */

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store");

try {
	$odqAfter = isset($_GET["after"]) ? max(0, (int)$_GET["after"]) : 0;
	$odqList  = OpenDayCore::questions($db, $odqAfter, $odqAfter > 0 ? 200 : 1000);

	$odqT = OpenDayCore::tables();
	$odqRows = $db->rawQuery("SELECT `qID`, `status` FROM `" . $odqT["question"] . "`", null);
	$odqState = array();
	$odqNew = 0;
	if (is_array($odqRows)) {
		foreach ($odqRows as $odqR) {
			$odqState[] = array((int)$odqR["qID"], (int)$odqR["status"]);
			if ((int)$odqR["status"] === 0) {
				$odqNew++;
			}
		}
	}

	echo json_encode(array(
		"ok"    => 1,
		"items" => $odqList,
		/* [id, status] — өөр админ тэмдэглэсэн / устгасныг бусдын дэлгэцэнд тусгана */
		"state" => $odqState,
		"new"   => $odqNew,
		"now"   => date("Y-m-d H:i:s")
	));
} catch (Exception $odqErr) {
	error_log("openday qfeed failed: " . $odqErr->getMessage());
	header("HTTP/1.1 500 Internal Server Error");
	echo json_encode(array("ok" => 0));
}
