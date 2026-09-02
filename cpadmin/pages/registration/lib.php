<?php
/**
 * Бүртгэлийн модулийн сангуудыг ачаална.
 *
 * Цөм код нь сайтын үндсэн хавтас дахь class/registration.class.php —
 * вэб сайт болон CP Admin хоёулаа ЯГ ижил файлыг ашигладаг тул
 * хүснэгтийн бүтэц, блок/талбарын каталог хэзээ ч зөрөхгүй.
 */

if (!class_exists("RegistrationCore")) {
	$regLibCandidates = array(
		__DIR__ . "/../../../class/registration.class.php",   /* public_html/class/ */
		__DIR__ . "/../../class/registration.class.php"       /* cpadmin/class/ (нөөц) */
	);

	foreach ($regLibCandidates as $regLibPath) {
		if (is_file($regLibPath)) {
			include_once $regLibPath;
			break;
		}
	}
}

if (!class_exists("XlsxWriter")) {
	$regXlsxCandidates = array(
		__DIR__ . "/../../../class/xlsx.writer.class.php",
		__DIR__ . "/../../class/xlsx.writer.class.php"
	);

	foreach ($regXlsxCandidates as $regXlsxPath) {
		if (is_file($regXlsxPath)) {
			include_once $regXlsxPath;
			break;
		}
	}
}

/* Эрэмбэлэх туслах. Хэрэв серверт order.helper.php байхгүй бол
   энэ модуль fatal алдаа өгөхгүй, өөрийн хувилбарыг ашиглана. */
if (is_file(__DIR__ . "/../insert/order.helper.php")) {
	include_once __DIR__ . "/../insert/order.helper.php";
}

if (!function_exists("reorderScopedItem")) {
	function reorderScopedItem($db, $table, $idCol, $orderCol, $itemId, $newOrder, $scopeWhere)
	{
		$itemId = (int)$itemId;
		$newOrder = max(1, (int)$newOrder);
		if ($itemId < 1) {
			return;
		}

		$where = "1=1";
		$params = array();
		foreach ($scopeWhere as $col => $val) {
			$where .= " AND `" . $col . "`=?";
			$params[] = $val;
		}

		$rows = $db->rawQuery(
			"SELECT `" . $idCol . "` FROM `" . $table . "` WHERE " . $where
				. " ORDER BY `" . $orderCol . "` ASC, `" . $idCol . "` ASC",
			count($params) > 0 ? $params : null
		);

		$ids = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				if ((int)$row[$idCol] !== $itemId) {
					$ids[] = (int)$row[$idCol];
				}
			}
		}

		$insertAt = min($newOrder, count($ids) + 1) - 1;
		if ($insertAt < 0) {
			$insertAt = 0;
		}
		array_splice($ids, $insertAt, 0, array($itemId));

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

if (!function_exists("orderAjaxDone")) {
	function orderAjaxDone($payload = array())
	{
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(array_merge(array("ok" => 1), $payload));
		exit;
	}
}

if (!class_exists("RegistrationCore")) {
	die('<div style="padding:40px;font:15px sans-serif;color:#a00">'
		. 'class/registration.class.php олдсонгүй. Файлыг сайтын үндсэн хавтасны class/ дотор байрлуулна уу.'
		. '</div>');
}

/* Хүснэгтүүд байхгүй бол энд үүсгэнэ (_sql/registration.sql-тэй ижил бүтэц).
   Нэг хүсэлтэд ганц удаа шалгана. */
if (empty($GLOBALS["regModuleReady"])) {
	RegistrationCore::ensure($db);
	$GLOBALS["regModuleReady"] = true;
}

/* Энэ мөр ЯМАГТ ажиллах ёстой — lib.php-г дахин оруулсан ч дэд файлууд
   $regTbl-гүй үлдэхгүй байх учиртай. */
$regTbl = RegistrationCore::tables();
