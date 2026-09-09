<?php
/**
 * Дадлагын модулийн сангуудыг ачаална.
 *
 * Цөм код нь сайтын үндсэн хавтас дахь class/apply.class.php —
 * вэб сайт болон CP Admin хоёулаа ЯГ ижил файлыг ашигладаг тул
 * хүснэгтийн бүтэц, талбарын каталог хэзээ ч зөрөхгүй.
 */

if (!class_exists("ApplyCore")) {
	$applyLibCandidates = array(
		__DIR__ . "/../../../class/apply.class.php",   /* public_html/class/ */
		__DIR__ . "/../../class/apply.class.php"       /* cpadmin/class/ (нөөц) */
	);

	foreach ($applyLibCandidates as $applyLibPath) {
		if (is_file($applyLibPath)) {
			include_once $applyLibPath;
			break;
		}
	}
}

if (!class_exists("XlsxWriter")) {
	$applyXlsxCandidates = array(
		__DIR__ . "/../../../class/xlsx.writer.class.php",
		__DIR__ . "/../../class/xlsx.writer.class.php"
	);

	foreach ($applyXlsxCandidates as $applyXlsxPath) {
		if (is_file($applyXlsxPath)) {
			include_once $applyXlsxPath;
			break;
		}
	}
}

if (!class_exists("ApplyCore")) {
	die('<div style="padding:40px;font:15px sans-serif;color:#a00">'
		. 'class/apply.class.php олдсонгүй. Файлыг сайтын үндсэн хавтасны class/ дотор байрлуулна уу.'
		. '</div>');
}

/* Хүснэгтүүд байхгүй бол энд үүсгэнэ. Нэг хүсэлтэд ганц удаа шалгана. */
ApplyCore::ensure($db);

/* Энэ мөр ЯМАГТ ажиллах ёстой — lib.php-г дахин оруулсан ч дэд файлууд
   $applyTbl-гүй үлдэхгүй байх учиртай. */
$applyTbl = ApplyCore::tables();

/** Эрэмбийг нэгээр дээш/доош шилжүүлж, бүх мөрийг 1..N болгож дугаарлана. */
if (!function_exists("applyMoveRow")) {
	function applyMoveRow($db, $table, $idCol, $orderCol, $itemID, $dir)
	{
		$rows = $db->rawQuery(
			"SELECT `" . $idCol . "` FROM `" . $table . "` ORDER BY `" . $orderCol . "` ASC, `" . $idCol . "` ASC",
			null
		);

		if (!is_array($rows)) {
			return;
		}

		$ids = array();
		foreach ($rows as $row) {
			$ids[] = (int)$row[$idCol];
		}

		$pos = array_search((int)$itemID, $ids, true);
		if ($pos === false) {
			return;
		}

		$swap = $dir == "up" ? $pos - 1 : $pos + 1;
		if ($swap < 0 || $swap >= count($ids)) {
			return;
		}

		$tmp        = $ids[$pos];
		$ids[$pos]  = $ids[$swap];
		$ids[$swap] = $tmp;

		$order = 1;
		foreach ($ids as $id) {
			$db->rawQuery(
				"UPDATE `" . $table . "` SET `" . $orderCol . "`=? WHERE `" . $idCol . "`=?",
				array($order, $id)
			);
			$order++;
		}
	}
}

/** Талбарын жагсаалтыг 1..N болгож дахин дугаарлана (шинэ байрлалтай нь) */
if (!function_exists("applyReorder")) {
	function applyReorder($db, $table, $idCol, $orderCol, $itemID, $newOrder)
	{
		$itemID   = (int)$itemID;
		$newOrder = max(1, (int)$newOrder);

		if ($itemID < 1) {
			return;
		}

		$rows = $db->rawQuery(
			"SELECT `" . $idCol . "` FROM `" . $table . "` ORDER BY `" . $orderCol . "` ASC, `" . $idCol . "` ASC",
			null
		);

		$ids = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				if ((int)$row[$idCol] !== $itemID) {
					$ids[] = (int)$row[$idCol];
				}
			}
		}

		$insertAt = min($newOrder, count($ids) + 1) - 1;
		if ($insertAt < 0) {
			$insertAt = 0;
		}
		array_splice($ids, $insertAt, 0, array($itemID));

		$order = 1;
		foreach ($ids as $id) {
			$db->rawQuery(
				"UPDATE `" . $table . "` SET `" . $orderCol . "`=? WHERE `" . $idCol . "`=?",
				array($order, $id)
			);
			$order++;
		}
	}
}

if (!function_exists("applyAjaxDone")) {
	function applyAjaxDone($payload = array())
	{
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(array_merge(array("ok" => 1), $payload), JSON_UNESCAPED_UNICODE);
		exit;
	}
}
