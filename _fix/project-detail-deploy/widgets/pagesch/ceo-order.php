<?php

function sortCeoRowsByOrder($rows)
{
	if (!is_array($rows)) {
		return array();
	}

	usort($rows, function ($a, $b) {
		$orderA = (int)(isset($a["ceoOrder"]) ? $a["ceoOrder"] : 0);
		$orderB = (int)(isset($b["ceoOrder"]) ? $b["ceoOrder"] : 0);
		if ($orderA !== $orderB) {
			return $orderA - $orderB;
		}
		return (int)$a["ceoID"] - (int)$b["ceoID"];
	});

	return $rows;
}

function fetchCeoProjectsForLang($db, $db_ceo, $db_type, $lang)
{
	$sql = "SELECT A.*, C.name AS typeName
		FROM $db_ceo A
		LEFT JOIN $db_type C ON C.id = A.ceoCat
		WHERE A.lang = ?
		ORDER BY (A.ceoOrder + 0) ASC, A.ceoID ASC";

	return $db->rawQuery($sql, array((int)$lang));
}
